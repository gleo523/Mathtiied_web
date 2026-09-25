<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/database.php';

try {
    $data = $_POST;
    $first = trim((string)($data['first_name'] ?? ''));
    $last = trim((string)($data['last_name'] ?? ''));
    $school = trim((string)($data['school_name'] ?? ''));
    $email = trim((string)($data['email'] ?? ''));
    $password = (string)($data['password'] ?? '');
    $confirm = (string)($data['confirm_password'] ?? '');
    if ($first === '' || $last === '' || $school === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        respond(['error' => 'All teacher fields are required.'], 400);
    }
    if (strlen($password) < 8 || $password !== $confirm) {
        respond(['error' => 'Passwords must match and be at least 8 characters.'], 400);
    }
    $username = strtolower(preg_replace('/[^a-z0-9]+/i', '.', $first . '.' . $last) ?? '');
    $username = trim($username, '.') ?: 'teacher';
    $base = $username;
    $suffix = 1;
    $check = db()->prepare('SELECT id FROM users WHERE username = ? OR email = ? LIMIT 1');
    while (true) {
        $check->bind_param('ss', $username, $email);
        $check->execute();
        if (!$check->get_result()->fetch_assoc()) {
            break;
        }
        $username = $base . $suffix++;
    }
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = db()->prepare('INSERT INTO users (username,email,password,role,first_name,last_name) VALUES (?,?,?,"teacher",?,?)');
    $stmt->bind_param('sssss', $username, $email, $hash, $first, $last);
    $stmt->execute();
    respond(['status' => 'created', 'username' => $username], 201);
} catch (Throwable $error) {
    respond(['error' => 'Teacher registration could not be completed.'], 500);
}
