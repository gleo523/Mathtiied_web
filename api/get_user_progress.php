<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/database.php';

try {
    $username = trim((string)($_GET['username'] ?? $_GET['student_id'] ?? ''));
    if ($username === '') {
        respond(['error' => 'username is required.'], 400);
    }
    $stmt = db()->prepare(
        'SELECT has_unfinished_game, last_level, saved_m_value
         FROM users WHERE username=? OR school_number=? LIMIT 1'
    );
    $stmt->bind_param('ss', $username, $username);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    if (!$row) {
        respond(['error' => 'Student account not found.'], 404);
    }
    respond([
        'has_unfinished_game' => (int)$row['has_unfinished_game'],
        'last_level' => (int)$row['last_level'],
        'saved_m_value' => (float)$row['saved_m_value']
    ]);
} catch (Throwable $error) {
    respond(['error' => $error->getMessage()], 500);
}
