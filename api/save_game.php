<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/database.php';

try {
    $data = request_json();
    $username = require_string($data, ['username', 'student_id']);
    $level = max(1, (int)($data['level'] ?? $data['wave'] ?? 1));
    $mValue = max(0, min(1, (float)($data['m_value'] ?? $data['mastery'] ?? 0)));
    $conn = db();
    $userCheck = $conn->prepare('SELECT id FROM users WHERE username=? OR school_number=? LIMIT 1');
    $userCheck->bind_param('ss', $username, $username);
    $userCheck->execute();
    if (!$userCheck->get_result()->fetch_assoc()) {
        respond(['error' => 'Student account not found.'], 404);
    }
    $stmt = $conn->prepare('UPDATE users SET last_level=?, saved_m_value=?, has_unfinished_game=1 WHERE username=? OR school_number=?');
    $stmt->bind_param('idss', $level, $mValue, $username, $username);
    $stmt->execute();
    respond(['status' => 'saved']);
} catch (InvalidArgumentException $error) {
    respond(['error' => $error->getMessage()], 400);
} catch (Throwable $error) {
    respond(['error' => $error->getMessage()], 500);
}
