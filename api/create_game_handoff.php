<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/game_handoff.php';

try {
    $connection = db();
    ensure_game_handoff_table($connection);

    $handoffId = bin2hex(random_bytes(32));
    $statement = $connection->prepare(
        'INSERT INTO game_login_handoffs (handoff_id, expires_at)
         VALUES (?, DATE_ADD(NOW(), INTERVAL 10 MINUTE))'
    );
    $statement->bind_param('s', $handoffId);
    $statement->execute();

    $base = rtrim(
        (string) ($_SERVER['REQUEST_SCHEME'] ?? 'http')
        . '://' . (string) $_SERVER['HTTP_HOST']
        . dirname(dirname((string) $_SERVER['SCRIPT_NAME'])),
        '/'
    );

    respond([
        'handoff_id' => $handoffId,
        'login_url' => $base . '/student/login.php?handoff_id=' . rawurlencode($handoffId),
        'expires_in' => 600,
    ], 201);
} catch (Throwable $error) {
    respond(['error' => 'Could not start game login.'], 500);
}
