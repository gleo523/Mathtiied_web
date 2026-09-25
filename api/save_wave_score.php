<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/database.php';

try {
    $data = request_json();
    $username = require_string($data, ['username', 'student_id']);
    $topic = trim((string)($data['topic'] ?? ''));
    if ($topic === '') {
        throw new InvalidArgumentException('topic is required.');
    }

    $waveNumber = max(1, (int)($data['wave_number'] ?? 1));
    $score = (float)($data['score'] ?? 0);
    if ($score > 1 && $score <= 100) {
        $score = $score / 100;
    }
    $score = max(0, min(1, $score));
    $timestamp = (int)($data['timestamp'] ?? time());
    if ($timestamp <= 0) {
        $timestamp = time();
    }

    $conn = db();
    $userStmt = $conn->prepare('SELECT id FROM users WHERE username = ? OR school_number = ? LIMIT 1');
    $userStmt->bind_param('ss', $username, $username);
    $userStmt->execute();
    $user = $userStmt->get_result()->fetch_assoc();
    if (!$user) {
        respond(['error' => 'Student account not found.'], 404);
    }

    $stmt = $conn->prepare(
        'INSERT INTO wave_scores (user_id, topic, wave_number, score, recorded_at)
         VALUES (?, ?, ?, ?, FROM_UNIXTIME(?))'
    );
    $userId = (int)$user['id'];
    $stmt->bind_param('isidi', $userId, $topic, $waveNumber, $score, $timestamp);
    $stmt->execute();

    respond([
        'status' => 'saved',
        'username' => $username,
        'topic' => $topic,
        'wave_number' => $waveNumber,
        'score' => $score,
        'timestamp' => $timestamp
    ], 200);
} catch (InvalidArgumentException $error) {
    respond(['error' => $error->getMessage()], 400);
} catch (Throwable $error) {
    respond(['error' => $error->getMessage()], 500);
}
