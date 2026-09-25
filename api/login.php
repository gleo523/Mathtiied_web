<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/auth.php';

try {
    $raw = file_get_contents('php://input');
    $json = $raw !== '' ? json_decode($raw, true) : null;
    $data = is_array($json) ? $json : $_POST;
    $identifier = trim((string)($data['username'] ?? $data['student_id'] ?? $data['email'] ?? ''));
    $password = (string)($data['password'] ?? '');
    if ($identifier === '' || $password === '') {
        respond(['error' => 'Username/email and password are required.'], 400);
    }

    $stmt = db()->prepare('SELECT id, username, email, school_number, first_name, last_name, password, role FROM users WHERE username = ? OR email = ? OR school_number = ? LIMIT 1');
    $stmt->bind_param('sss', $identifier, $identifier, $identifier);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    if (!$user || !password_verify($password, $user['password'])) {
        respond(['error' => 'Invalid credentials.'], 401);
    }

    start_auth_session();
    session_regenerate_id(true);
    $_SESSION['user_id'] = (int)$user['id'];
    if ($user['role'] === 'teacher') {
        record_teacher_auth_event((int)$user['id'], 'login');
    }
    respond([
        'status' => 'authenticated',
        'role' => $user['role'],
        'user_id' => (int)$user['id'],
        'username' => $user['username'],
        'student_id' => $user['school_number'],
        'email' => $user['email'],
        'first_name' => $user['first_name'],
        'last_name' => $user['last_name'],
        'game_identity' => $user['username']
    ]);
} catch (Throwable $error) {
    respond(['error' => 'Login could not be completed.'], 500);
}
