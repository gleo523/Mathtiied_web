<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

try {
    $viewer = require_role('teacher', 'admin');
    $studentId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if (!$studentId) {
        respond(['error' => 'A valid student id is required.'], 400);
    }

    $conn = db();
    $studentStmt = $conn->prepare(
        'SELECT id, username, email, first_name, last_name, section, school_number,
                last_level, saved_m_value, has_unfinished_game, teacher_id
         FROM users WHERE id=? AND role="student" LIMIT 1'
    );
    $studentStmt->bind_param('i', $studentId);
    $studentStmt->execute();
    $student = $studentStmt->get_result()->fetch_assoc();
    if (!$student) {
        respond(['error' => 'Student not found.'], 404);
    }
    if ($viewer['role'] === 'teacher' && (int)$student['teacher_id'] !== (int)$viewer['id']) {
        respond(['error' => 'You are not allowed to view this student.'], 403);
    }

    $attempts = [];
    $attemptStmt = $conn->prepare(
        'SELECT topic, assessment, score, max_score, attempted_at
         FROM game_attempts WHERE user_id=? ORDER BY attempted_at DESC LIMIT 50'
    );
    $attemptStmt->bind_param('i', $studentId);
    $attemptStmt->execute();
    $attemptResult = $attemptStmt->get_result();
    while ($row = $attemptResult->fetch_assoc()) {
        $attempts[] = [
            'topic' => $row['topic'],
            'assessment' => $row['assessment'] ?: 'Game attempt',
            'score' => round((float)$row['score'], 1),
            'max_score' => round((float)$row['max_score'], 1),
            'attempted_at' => $row['attempted_at']
        ];
    }

    $mastery = [];
    $masteryStmt = $conn->prepare(
        'SELECT topic, mastery, updated_at FROM topic_mastery
         WHERE user_id=? ORDER BY mastery ASC, topic'
    );
    $masteryStmt->bind_param('i', $studentId);
    $masteryStmt->execute();
    $masteryResult = $masteryStmt->get_result();
    while ($row = $masteryResult->fetch_assoc()) {
        $mastery[] = [
            'topic' => $row['topic'],
            'mastery' => round((float)$row['mastery'], 1),
            'updated_at' => $row['updated_at']
        ];
    }

    $activities = [];
    $activityStmt = $conn->prepare(
        'SELECT event_type, title, description, occurred_at
         FROM activity_events WHERE user_id=? ORDER BY occurred_at DESC LIMIT 50'
    );
    $activityStmt->bind_param('i', $studentId);
    $activityStmt->execute();
    $activityResult = $activityStmt->get_result();
    while ($row = $activityResult->fetch_assoc()) {
        $activities[] = $row;
    }

    $modules = [];
    $moduleStmt = $conn->prepare(
        'SELECT rm.title, rm.status, rma.assigned_at
         FROM review_modules rm
         INNER JOIN review_module_assignments rma ON rma.module_id=rm.id
         WHERE rma.user_id=? ORDER BY rma.assigned_at DESC'
    );
    $moduleStmt->bind_param('i', $studentId);
    $moduleStmt->execute();
    $moduleResult = $moduleStmt->get_result();
    while ($row = $moduleResult->fetch_assoc()) {
        $modules[] = $row;
    }

    $student['name'] = trim(($student['first_name'] ?? '') . ' ' . ($student['last_name'] ?? ''));
    if ($student['name'] === '') {
        $student['name'] = $student['username'];
    }
    $student['mastery'] = $mastery === []
        ? round((float)$student['saved_m_value'] * 100, 1)
        : round(array_sum(array_column($mastery, 'mastery')) / count($mastery), 1);

    respond([
        'student' => $student,
        'attempts' => $attempts,
        'mastery' => $mastery,
        'weak_topics' => array_values(array_filter($mastery, fn(array $item): bool => $item['mastery'] < 70)),
        'activities' => $activities,
        'modules' => $modules
    ]);
} catch (Throwable $error) {
    respond(['error' => $error->getMessage()], 500);
}
