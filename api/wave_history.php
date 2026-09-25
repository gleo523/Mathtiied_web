<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/auth.php';

try {
    $user = require_role('teacher', 'admin');
    $studentId = (int)($_GET['student_id'] ?? 0);
    $conn = db();
    if ($studentId > 0) {
        $studentStmt = $conn->prepare('SELECT id, username, first_name, last_name, teacher_id FROM users WHERE id = ? AND role = "student" LIMIT 1');
        $studentStmt->bind_param('i', $studentId);
        $studentStmt->execute();
        $student = $studentStmt->get_result()->fetch_assoc();
        if (!$student) {
            respond(['error' => 'Student not found.'], 404);
        }
        if ($user['role'] === 'teacher' && (int)$student['teacher_id'] !== (int)$user['id']) {
            respond(['error' => 'You are not allowed to view this student.'], 403);
        }
        $stmt = $conn->prepare('SELECT topic, wave_number, score, recorded_at FROM wave_scores WHERE user_id = ? ORDER BY recorded_at, wave_number');
        $stmt->bind_param('i', $studentId);
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        respond(['student' => $student, 'scores' => $rows]);
    }

    $sql = 'SELECT u.id, u.username, u.first_name, u.last_name, COUNT(ws.id) AS score_count
            FROM users u LEFT JOIN wave_scores ws ON ws.user_id = u.id
            WHERE u.role = "student"';
    if ($user['role'] === 'teacher') {
        $sql .= ' AND u.teacher_id = ?';
    }
    $sql .= ' GROUP BY u.id ORDER BY u.last_name, u.first_name, u.username';
    $stmt = $conn->prepare($sql);
    if ($user['role'] === 'teacher') {
        $teacherId = (int)$user['id'];
        $stmt->bind_param('i', $teacherId);
    }
    $stmt->execute();
    respond(['students' => $stmt->get_result()->fetch_all(MYSQLI_ASSOC)]);
} catch (Throwable $error) {
    respond(['error' => $error->getMessage()], 500);
}
