<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/database.php';

try {
    $username = trim((string)($_GET['username'] ?? $_GET['student_id'] ?? ''));
    if ($username === '') {
        respond(['registered' => false], 200);
    }

    $stmt = db()->prepare(
        'SELECT id, username, school_number, first_name, last_name, role
         FROM users WHERE username = ? OR school_number = ? OR email = ? LIMIT 1'
    );
    $stmt->bind_param('sss', $username, $username, $username);
    $stmt->execute();

    $row = $stmt->get_result()->fetch_assoc();
    respond([
        'registered' => $row !== null,
        'role' => $row['role'] ?? null,
        'user_id' => isset($row['id']) ? (int)$row['id'] : null,
        'username' => $row['username'] ?? null,
        'student_id' => $row['school_number'] ?? null,
        'first_name' => $row['first_name'] ?? null,
        'last_name' => $row['last_name'] ?? null,
        'game_identity' => $row['username'] ?? null
    ], 200);
} catch (Throwable $error) {
    respond(['registered' => false], 200);
}
