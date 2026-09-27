<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/game_auth.php';

try {
    $user = require_game_access_user();
    $connection = db();
    $userId = (int)$user['id'];
    $stmt = $connection->prepare(
        'SELECT has_unfinished_game, last_level, saved_m_value
         FROM users WHERE id = ? LIMIT 1'
    );
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();

    $gameState = null;
    if ($row && (int)$row['has_unfinished_game'] === 1) {
        $externalId = 'resume_save';
        $snapshot = $connection->prepare(
            'SELECT raw_payload FROM game_sessions WHERE user_id = ? AND external_id = ? LIMIT 1'
        );
        $snapshot->bind_param('is', $userId, $externalId);
        $snapshot->execute();
        $saved = $snapshot->get_result()->fetch_assoc();
        if ($saved && is_string($saved['raw_payload'])) {
            $parsed = json_decode($saved['raw_payload'], true);
            $gameState = is_array($parsed) ? $parsed : null;
        }
    }

    respond([
        'has_unfinished_game' => (int)($row['has_unfinished_game'] ?? 0),
        'last_level' => (int)($row['last_level'] ?? 1),
        'saved_m_value' => (float)($row['saved_m_value'] ?? 1),
        'game_state' => $gameState,
    ]);
} catch (Throwable $error) {
    respond(['error' => $error->getMessage()], 500);
}
