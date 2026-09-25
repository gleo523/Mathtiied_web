<?php
declare(strict_types=1);
require_once __DIR__ . '/../../config/auth.php';

try {
    require_role('admin');
    $conn = db();
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = request_json();
        $first = trim((string)($data['first_name'] ?? ''));
        $last = trim((string)($data['last_name'] ?? ''));
        $username = trim((string)($data['username'] ?? ''));
        $email = trim((string)($data['email'] ?? ''));
        $password = (string)($data['password'] ?? '');
        if ($first === '' || $last === '' || $username === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 8) {
            throw new InvalidArgumentException('Valid names, username, email, and an 8-character password are required.');
        }
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare('INSERT INTO users (username, email, password, role, first_name, last_name) VALUES (?, ?, ?, "teacher", ?, ?)');
        $stmt->bind_param('sssss', $username, $email, $hash, $first, $last);
        $stmt->execute();
    }
    $result = $conn->query('SELECT id, username, email, first_name, last_name, created_at FROM users WHERE role = "teacher" ORDER BY last_name, first_name, username');
    respond(['teachers' => $result->fetch_all(MYSQLI_ASSOC)]);
} catch (InvalidArgumentException $error) {
    respond(['error' => $error->getMessage()], 400);
} catch (Throwable $error) {
    respond(['error' => 'Teacher management request failed.'], 500);
}
