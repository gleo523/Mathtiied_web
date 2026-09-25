<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

function review_module_students(mysqli $conn, int $teacherId): array
{
    $stmt = $conn->prepare(
        "SELECT u.id, u.username, u.first_name, u.last_name, u.section
         FROM users u WHERE u.role='student' AND u.teacher_id=?
         ORDER BY u.last_name, u.first_name, u.username"
    );
    $stmt->bind_param('i', $teacherId);
    $stmt->execute();
    $students = [];
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $name = trim(($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? ''));
        $students[] = [
            'id' => (int)$row['id'],
            'name' => $name !== '' ? $name : $row['username'],
            'section' => $row['section'] ?: 'Unassigned'
        ];
    }
    return $students;
}

try {
    $teacher = require_role('teacher');
    $conn = db();
    $teacherId = (int)$teacher['id'];

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $moduleStmt = $conn->prepare(
            'SELECT rm.id, rm.title, rm.topic, rm.instructions, rm.status, rm.created_at,
                    rm.updated_at, rm.completed_at, COUNT(rma.user_id) AS student_count
             FROM review_modules rm
             LEFT JOIN review_module_assignments rma ON rma.module_id=rm.id
             WHERE rm.teacher_id=? GROUP BY rm.id ORDER BY rm.created_at DESC'
        );
        $moduleStmt->bind_param('i', $teacherId);
        $moduleStmt->execute();
        $modules = [];
        $result = $moduleStmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $modules[] = [
                'id' => (int)$row['id'], 'title' => $row['title'], 'topic' => $row['topic'],
                'instructions' => $row['instructions'], 'status' => $row['status'],
                'student_count' => (int)$row['student_count'], 'created_at' => $row['created_at'],
                'updated_at' => $row['updated_at'], 'completed_at' => $row['completed_at']
            ];
        }

        $topicStmt = $conn->prepare(
            "SELECT tm.topic, COUNT(DISTINCT tm.user_id) AS students_flagged,
                    ROUND(AVG(tm.mastery), 1) AS mastery
             FROM topic_mastery tm INNER JOIN users u ON u.id=tm.user_id
             WHERE u.teacher_id=? AND u.role='student' AND tm.mastery < 70
             GROUP BY tm.topic ORDER BY students_flagged DESC, tm.topic"
        );
        $topicStmt->bind_param('i', $teacherId);
        $topicStmt->execute();
        $topics = [];
        $topicResult = $topicStmt->get_result();
        while ($row = $topicResult->fetch_assoc()) {
            $mastery = (float)$row['mastery'];
            $topics[] = [
                'topic' => $row['topic'], 'students_flagged' => (int)$row['students_flagged'],
                'mastery' => $mastery, 'severity' => $mastery < 50 ? 'High' : ($mastery < 70 ? 'Medium' : 'Low')
            ];
        }
        respond(['review_modules' => $modules, 'weak_topics' => $topics, 'students' => review_module_students($conn, $teacherId)]);
    }

    $payload = request_json();
    $method = $_SERVER['REQUEST_METHOD'];
    if ($method === 'POST') {
        $title = require_string($payload, ['title']);
        $topic = require_string($payload, ['topic']);
        $instructions = isset($payload['instructions']) ? trim((string)$payload['instructions']) : null;
        $status = strtolower((string)($payload['status'] ?? 'draft'));
        if (!in_array($status, ['draft', 'published'], true)) {
            throw new InvalidArgumentException('Status must be draft or published when creating a module.');
        }

        $topicCheck = $conn->prepare(
            "SELECT 1 FROM topic_mastery tm INNER JOIN users u ON u.id=tm.user_id
             WHERE tm.topic=? AND tm.mastery < 70 AND u.teacher_id=? AND u.role='student' LIMIT 1"
        );
        $topicCheck->bind_param('si', $topic, $teacherId);
        $topicCheck->execute();
        if (!$topicCheck->get_result()->fetch_assoc()) {
            respond(['error' => 'Select a weak topic from one of your students.'], 422);
        }

        $studentIds = $payload['student_ids'] ?? [];
        if (!is_array($studentIds)) {
            throw new InvalidArgumentException('student_ids must be an array.');
        }
        $studentIds = array_values(array_unique(array_filter(array_map('intval', $studentIds), fn(int $id): bool => $id > 0)));
        if ($studentIds === []) {
            throw new InvalidArgumentException('Assign the module to at least one student.');
        }

        $conn->begin_transaction();
        $stmt = $conn->prepare('INSERT INTO review_modules (teacher_id, title, topic, instructions, status) VALUES (?, ?, ?, ?, ?)');
        $stmt->bind_param('issss', $teacherId, $title, $topic, $instructions, $status);
        $stmt->execute();
        $moduleId = $conn->insert_id;
        $studentStmt = $conn->prepare("SELECT id FROM users WHERE id=? AND role='student' AND teacher_id=?");
        $assignStmt = $conn->prepare('INSERT INTO review_module_assignments (module_id, user_id) VALUES (?, ?)');
        foreach ($studentIds as $studentId) {
            $studentStmt->bind_param('ii', $studentId, $teacherId);
            $studentStmt->execute();
            if (!$studentStmt->get_result()->fetch_assoc()) {
                throw new InvalidArgumentException('One or more selected students are not assigned to you.');
            }
            $assignStmt->bind_param('ii', $moduleId, $studentId);
            $assignStmt->execute();
        }
        $conn->commit();
        respond(['module_id' => $moduleId, 'status' => $status], 201);
    }

    if ($method === 'PATCH') {
        $moduleId = (int)($payload['id'] ?? 0);
        $status = strtolower((string)($payload['status'] ?? ''));
        if ($moduleId < 1 || !in_array($status, ['draft', 'published', 'completed'], true)) {
            throw new InvalidArgumentException('A valid module id and status are required.');
        }
        $stmt = $conn->prepare('UPDATE review_modules SET status=?, completed_at=IF(?="completed", NOW(), NULL) WHERE id=? AND teacher_id=?');
        $stmt->bind_param('ssii', $status, $status, $moduleId, $teacherId);
        $stmt->execute();
        if ($stmt->affected_rows < 1) {
            respond(['error' => 'Module not found or not owned by you.'], 404);
        }
        respond(['status' => $status]);
    }

    respond(['error' => 'Method not allowed.'], 405);
} catch (Throwable $error) {
    respond(['error' => $error->getMessage()], 400);
}
