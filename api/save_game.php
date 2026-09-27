<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/game_auth.php';

try {
    $user = require_game_access_user();
    $data = request_json();
    $userId = (int)$user['id'];
    $connection = db();

    if (!empty($data['clear_save'])) {
        $externalId = 'resume_save';
        $delete = $connection->prepare('DELETE FROM game_sessions WHERE user_id = ? AND external_id = ?');
        $delete->bind_param('is', $userId, $externalId);
        $delete->execute();

        $clear = $connection->prepare('UPDATE users SET has_unfinished_game = 0, last_level = 1 WHERE id = ?');
        $clear->bind_param('i', $userId);
        $clear->execute();
        respond(['status' => 'cleared']);
    }

    $level = max(1, (int)($data['level'] ?? $data['wave'] ?? 1));
    $mValue = max(0, min(1, (float)($data['m_value'] ?? $data['mastery'] ?? 0)));

    $gameState = $data['game_state'] ?? null;
    if (is_array($gameState) && in_array((string)($gameState['part_id'] ?? ''), ['part1', 'part2', 'part3', 'part4', 'part5'], true)) {
        $rawPayload = json_encode($gameState, JSON_UNESCAPED_SLASHES);
        if ($rawPayload === false) {
            throw new InvalidArgumentException('Game save could not be encoded.');
        }
        if (strlen($rawPayload) > 1000000) {
            throw new InvalidArgumentException('Game save is too large.');
        }

        $externalId = 'resume_save';
        $topic = (string)$gameState['part_id'];
        $savedLevel = max(1, (int)($gameState['wave_index'] ?? 0) + 1);
        $snapshot = $connection->prepare(
            'INSERT INTO game_sessions
             (user_id, external_id, topic, level, mastery_value, last_seen_at, raw_payload)
             VALUES (?, ?, ?, ?, ?, NOW(), ?)
             ON DUPLICATE KEY UPDATE topic=VALUES(topic), level=VALUES(level),
             mastery_value=VALUES(mastery_value), last_seen_at=NOW(), raw_payload=VALUES(raw_payload)'
        );
        $snapshot->bind_param('issids', $userId, $externalId, $topic, $savedLevel, $mValue, $rawPayload);
        $snapshot->execute();
    }

    $stmt = $connection->prepare('UPDATE users SET last_level=?, saved_m_value=?, has_unfinished_game=1 WHERE id=?');
    $stmt->bind_param('idi', $level, $mValue, $userId);
    $stmt->execute();
    respond(['status' => 'saved', 'username' => $user['username']]);
} catch (InvalidArgumentException $error) {
    respond(['error' => $error->getMessage()], 400);
} catch (Throwable $error) {
    respond(['error' => $error->getMessage()], 500);
}
