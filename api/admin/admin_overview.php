<?php
declare(strict_types=1);
require_once __DIR__ . '/../../config/auth.php';

try {
    require_role('admin');
    $conn = db();
    $hasTeacherId = (bool)$conn->query(
        "SELECT 1 FROM information_schema.columns
         WHERE table_schema = DATABASE() AND table_name = 'users' AND column_name = 'teacher_id'"
    )->fetch_row();
    $sql = $hasTeacherId
        ? 'SELECT s.id, s.username, s.first_name, s.last_name, s.section, t.username AS teacher_username, t.first_name AS teacher_first_name, t.last_name AS teacher_last_name
           FROM users s LEFT JOIN users t ON t.id = s.teacher_id WHERE s.role = "student"
           ORDER BY t.last_name, t.first_name, s.last_name, s.first_name'
        : 'SELECT id, username, first_name, last_name, section, NULL AS teacher_username, NULL AS teacher_first_name, NULL AS teacher_last_name
           FROM users WHERE role = "student" ORDER BY last_name, first_name';
    $result = $conn->query($sql);
    $students = $result->fetch_all(MYSQLI_ASSOC);
    respond(['students' => $students, 'total_students' => count($students)]);
} catch (Throwable $error) {
    respond(['error' => 'Admin overview unavailable.'], 500);
}
