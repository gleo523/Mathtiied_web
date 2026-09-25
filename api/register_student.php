<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/database.php';

try {
    $data = $_POST;
    $firstName = trim((string)($data['first_name'] ?? ''));
    $lastName = trim((string)($data['last_name'] ?? ''));
    $studentId = trim((string)($data['student_id'] ?? ''));
    $section = trim((string)($data['section'] ?? ''));
    $email = trim((string)($data['email'] ?? ''));
    $password = (string)($data['password'] ?? '');
    $confirmPassword = (string)($data['confirm_password'] ?? '');

    if ($firstName === '' || $lastName === '' || $studentId === '' || $section === '' || $email === '') {
        respond(['error' => 'All student fields are required.'], 400);
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        respond(['error' => 'Enter a valid email address.'], 400);
    }
    if (strlen($password) < 8) {
        respond(['error' => 'Password must be at least 8 characters.'], 400);
    }
    if ($password !== $confirmPassword) {
        respond(['error' => 'Passwords do not match.'], 400);
    }

    $conn = db();
    $check = $conn->prepare('SELECT id FROM users WHERE school_number=? OR email=? LIMIT 1');
    $check->bind_param('ss', $studentId, $email);
    $check->execute();
    if ($check->get_result()->fetch_assoc()) {
        respond(['error' => 'That student ID or email is already registered.'], 409);
    }

    $username = $studentId;
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $insert = $conn->prepare(
        'INSERT INTO users
         (username, email, password, role, first_name, last_name, section, school_number)
         VALUES (?, ?, ?, "student", ?, ?, ?, ?)'
    );
    $insert->bind_param('sssssss', $username, $email, $hash, $firstName, $lastName, $section, $studentId);
    $insert->execute();
    respond([
        'status' => 'created',
        'message' => 'Student account created.',
        'user_id' => (int)$conn->insert_id,
        'username' => $username,
        'student_id' => $studentId,
        'game_identity' => $username
    ], 201);
} catch (Throwable $error) {
    respond(['error' => 'Registration could not be completed.'], 500);
}
