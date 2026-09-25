<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

try {
    $viewer = require_role('teacher', 'admin');
    $conn = db();
    $students = [];
    $studentSql = "SELECT u.id, u.username, u.first_name, u.last_name, u.section,
                COALESCE(AVG(tm.mastery), u.saved_m_value * 100) AS mastery,
                MAX(gs.topic) AS topic, MAX(gs.last_seen_at) AS last_updated
         FROM users u
         LEFT JOIN topic_mastery tm ON tm.user_id = u.id
         LEFT JOIN game_sessions gs ON gs.user_id = u.id
         WHERE u.role = 'student'";
    if ($viewer['role'] === 'teacher') {
        $studentSql .= ' AND u.teacher_id = ' . (int)$viewer['id'];
    }
    $studentSql .= ' GROUP BY u.id ORDER BY u.last_name, u.first_name, u.username';
    $result = $conn->query($studentSql);
    while ($row = $result->fetch_assoc()) {
        $name = trim(($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? ''));
        $students[] = [
            'id' => (int)$row['id'],
            'name' => $name !== '' ? $name : $row['username'],
            'username' => $row['username'],
            'section' => $row['section'] ?: 'Unassigned',
            'mastery' => round((float)$row['mastery'], 1),
            'topic' => $row['topic'] ?: 'Not started',
            'last_updated' => $row['last_updated']
        ];
    }

    $topics = [];
    $topicSql = "SELECT tm.topic, COUNT(*) AS students_flagged,
                CASE WHEN AVG(mastery) < 50 THEN 'High'
                     WHEN AVG(mastery) < 70 THEN 'Medium' ELSE 'Low' END AS severity
         FROM topic_mastery tm INNER JOIN users u ON u.id=tm.user_id
         WHERE tm.mastery < 70 AND u.role='student'";
    if ($viewer['role'] === 'teacher') {
        $topicSql .= ' AND u.teacher_id = ' . (int)$viewer['id'];
    }
    $topicSql .= ' GROUP BY tm.topic ORDER BY students_flagged DESC, tm.topic';
    $topicResult = $conn->query($topicSql);
    while ($row = $topicResult->fetch_assoc()) {
        $topics[] = [
            'topic' => $row['topic'],
            'students_flagged' => (int)$row['students_flagged'],
            'severity' => $row['severity']
        ];
    }

    $moduleSql = "SELECT rm.id, rm.title, rm.topic, rm.status, rm.created_at,
                COUNT(rma.user_id) AS student_count
         FROM review_modules rm
         LEFT JOIN review_module_assignments rma ON rma.module_id = rm.id";
    if ($viewer['role'] === 'teacher') {
        $moduleSql .= ' WHERE rm.teacher_id = ' . (int)$viewer['id'];
    }
    $moduleSql .= ' GROUP BY rm.id ORDER BY rm.created_at DESC';
    $moduleResult = $conn->query($moduleSql);
    $modules = [];
    while ($row = $moduleResult->fetch_assoc()) {
        $modules[] = [
            'id' => (int)$row['id'],
            'title' => $row['title'],
            'topic' => $row['topic'],
            'status' => $row['status'],
            'student_count' => (int)$row['student_count'],
            'created_at' => $row['created_at']
        ];
    }

    $mastery = $students === [] ? 0 : round(array_sum(array_column($students, 'mastery')) / count($students));
    $attempts = [];
    $attemptStmt = $conn->prepare(
        "SELECT DATE(ga.attempted_at) AS day, COUNT(*) AS attempts,
                ROUND(AVG(ga.score / NULLIF(ga.max_score, 0) * 100), 1) AS average_score
         FROM game_attempts ga
         INNER JOIN users u ON u.id = ga.user_id
         WHERE u.role='student' AND u.teacher_id=? AND ga.attempted_at >= DATE_SUB(CURDATE(), INTERVAL 13 DAY)
         GROUP BY DATE(ga.attempted_at) ORDER BY day"
    );
    $attemptStmt->bind_param('i', $viewer['id']);
    $attemptStmt->execute();
    $attemptResult = $attemptStmt->get_result();
    while ($row = $attemptResult->fetch_assoc()) {
        $attempts[] = ['day' => $row['day'], 'attempts' => (int)$row['attempts'], 'average_score' => (float)$row['average_score']];
    }
    $activityStmt = $conn->prepare(
        "SELECT COUNT(*) AS total FROM activity_events ae
         INNER JOIN users u ON u.id=ae.user_id
         WHERE u.role='student' AND u.teacher_id=? AND ae.occurred_at >= DATE_SUB(CURDATE(), INTERVAL 29 DAY)"
    );
    $activityStmt->bind_param('i', $viewer['id']);
    $activityStmt->execute();
    $activityCount = (int)($activityStmt->get_result()->fetch_assoc()['total'] ?? 0);
    respond([
        'summary' => [
            'total_students' => count($students),
            'average_mastery' => $mastery,
            'students_needing_attention' => count(array_filter($students, fn(array $s): bool => $s['mastery'] < 70)),
            'active_review_modules' => count(array_filter($modules, fn(array $m): bool => $m['status'] !== 'completed'))
        ],
        'students' => $students,
        'topics' => $topics,
        'review_modules' => $modules,
        'analytics' => [
            'attempts_by_day' => $attempts,
            'activity_last_30_days' => $activityCount
        ]
    ]);
} catch (Throwable $error) {
    respond(['error' => $error->getMessage()], 500);
}
