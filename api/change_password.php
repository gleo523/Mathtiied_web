<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/auth.php';

try {
    $user = require_role('teacher', 'student', 'admin');
    $data = request_json();
    $current = (string)($data['current_password'] ?? '');
    $new = (string)($data['new_password'] ?? '');
    $confirm = (string)($data['confirm_new_password'] ?? '');
    if ($current === '' || strlen($new) < 8 || !preg_match('/[0-9]/', $new) || !preg_match('/[^a-zA-Z0-9]/', $new)) {
        respond(['error' => 'New password must be at least 8 characters and include a number and symbol.'], 400);
    }
    if ($new !== $confirm) {
        respond(['error' => 'New passwords do not match.'], 400);
    }
    $stmt = db()->prepare('SELECT password FROM users WHERE id = ?');
    $stmt->bind_param('i', $user['id']);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    if (!$row || !password_verify($current, $row['password'])) {
        respond(['error' => 'Current password is incorrect.'], 401);
    }
    $hash = password_hash($new, PASSWORD_DEFAULT);
    $update = db()->prepare('UPDATE users SET password = ? WHERE id = ?');
    $update->bind_param('si', $hash, $user['id']);
    $update->execute();
    respond(['status' => 'updated']);
} catch (Throwable $error) {
    respond(['error' => 'Password update could not be completed.'], 500);
}
