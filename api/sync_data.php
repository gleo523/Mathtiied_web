<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/database.php';

try {
    $data = request_json();
    $username = require_string($data, ['username', 'student_id', 'name', 'user']);
    $topic = (string)($data['topic'] ?? $data['current_topic'] ?? 'General Mathematics');
    $level = max(1, (int)($data['level'] ?? $data['last_level'] ?? 1));
    $mastery = (float)($data['mastery'] ?? $data['mastery_percent'] ?? $data['m_value'] ?? 0);
    if ($mastery <= 1) {
        $mastery *= 100;
    }
    $mastery = max(0, min(100, $mastery));
    $struggling = !empty($data['is_struggling']) || $mastery < 70;
    $externalId = isset($data['session_id']) ? (string)$data['session_id'] : null;
    $conn = db();

    $userStmt = $conn->prepare('SELECT id FROM users WHERE username = ? OR school_number = ? LIMIT 1');
    $userStmt->bind_param('ss', $username, $username);
    $userStmt->execute();
    $user = $userStmt->get_result()->fetch_assoc();
    if (!$user) {
        respond(['error' => 'Student account not found.'], 404);
    }
    $userId = (int)$user['id'];
    $raw = json_encode($data, JSON_UNESCAPED_SLASHES);

    $sessionStmt = $conn->prepare(
        'INSERT INTO game_sessions
         (user_id, external_id, topic, level, mastery_value, score, is_struggling, last_seen_at, raw_payload)
         VALUES (?, NULLIF(?, ""), ?, ?, ?, ?, ?, NOW(), ?)
         ON DUPLICATE KEY UPDATE topic=VALUES(topic), level=VALUES(level),
         mastery_value=VALUES(mastery_value), score=VALUES(score),
         is_struggling=VALUES(is_struggling), last_seen_at=NOW(), raw_payload=VALUES(raw_payload)'
    );
    $score = isset($data['score']) ? (float)$data['score'] : $mastery;
    $sessionStmt->bind_param('issiddis', $userId, $externalId, $topic, $level, $mastery, $score, $struggling, $raw);
    $sessionStmt->execute();

    $update = $conn->prepare('UPDATE users SET last_level=?, saved_m_value=?, has_unfinished_game=1 WHERE id=?');
    $savedValue = $mastery / 100;
    $update->bind_param('idi', $level, $savedValue, $userId);
    $update->execute();

    $masteryStmt = $conn->prepare(
        'INSERT INTO topic_mastery (user_id, topic, mastery) VALUES (?, ?, ?)
         ON DUPLICATE KEY UPDATE mastery=VALUES(mastery)'
    );
    $masteryStmt->bind_param('isd', $userId, $topic, $mastery);
    $masteryStmt->execute();

    if (isset($data['score']) || isset($data['assessment'])) {
        $attemptStmt = $conn->prepare(
            'INSERT INTO game_attempts (user_id, topic, assessment, score, max_score, raw_payload)
             VALUES (?, ?, ?, ?, ?, ?)'
        );
        $assessment = (string)($data['assessment'] ?? 'Game attempt');
        $attemptScore = (float)($data['score'] ?? $mastery);
        $maxScore = (float)($data['max_score'] ?? 100);
        $attemptStmt->bind_param('issdds', $userId, $topic, $assessment, $attemptScore, $maxScore, $raw);
        $attemptStmt->execute();
    }

    $eventStmt = $conn->prepare(
        'INSERT INTO activity_events (user_id, event_type, title, description, raw_payload)
         VALUES (?, "game_sync", ?, ?, ?)'
    );
    $title = 'Game progress synced';
    $description = sprintf('%s: %.1f%% mastery at level %d.', $topic, $mastery, $level);
    $eventStmt->bind_param('isss', $userId, $title, $description, $raw);
    $eventStmt->execute();

    respond(['status' => 'success', 'student_id' => $userId, 'mastery' => $mastery]);
} catch (InvalidArgumentException $error) {
    respond(['error' => $error->getMessage()], 400);
} catch (Throwable $error) {
    respond(['error' => $error->getMessage()], 500);
}
