<?php
declare(strict_types=1);
require_once __DIR__ . '/../../config/auth.php';

try {
    require_role('admin');
    $data = request_json();
    $userId = (int)($data['user_id'] ?? 0);
    $password = (string)($data['password'] ?? '');
    if ($userId < 1 || strlen($password) < 8) {
        throw new InvalidArgumentException('A user and an 8-character password are required.');
    }
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = db()->prepare("UPDATE users SET password = ? WHERE id = ? AND role = 'teacher'");
    $stmt->bind_param('si', $hash, $userId);
    $stmt->execute();
    if ($stmt->affected_rows < 1) {
        throw new InvalidArgumentException('Teacher account was not found.');
    }
    respond(['status' => 'updated']);
} catch (InvalidArgumentException $error) {
    respond(['error' => $error->getMessage()], 400);
} catch (Throwable $error) {
    respond(['error' => 'Password reset failed.'], 500);
}
