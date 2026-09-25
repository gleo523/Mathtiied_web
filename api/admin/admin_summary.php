<?php
declare(strict_types=1);
require_once __DIR__ . '/../../config/auth.php';

try {
    require_role('admin');
    $conn = db();
    $counts = $conn->query(
        "SELECT
            COUNT(*) AS total_users,
            SUM(role = 'teacher') AS total_teachers,
            SUM(role = 'student') AS total_students
         FROM users"
    )->fetch_assoc();
    $dwtiReports = (int)$conn->query("SELECT COUNT(*) AS total FROM topic_mastery WHERE mastery < 70")->fetch_assoc()['total'];
    $reviewModules = (int)$conn->query("SELECT COUNT(*) AS total FROM review_modules")->fetch_assoc()['total'];
    $waveScores = (int)$conn->query("SELECT COUNT(*) AS total FROM wave_scores")->fetch_assoc()['total'];
    respond([
        'total_users' => (int)$counts['total_users'],
        'total_teachers' => (int)$counts['total_teachers'],
        'total_students' => (int)$counts['total_students'],
        'dwti_reports' => $dwtiReports,
        'review_modules' => $reviewModules,
        'wave_scores' => $waveScores
    ]);
} catch (Throwable $error) {
    respond(['error' => 'Admin summary unavailable.'], 500);
}
