<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/game_handoff.php';

try {
    $handoffId = trim((string) ($_GET['handoff_id'] ?? ''));
    if (!validate_handoff_id($handoffId)) {
        respond(['authenticated' => false, 'error' => 'Invalid game login request.'], 400);
    }

    $connection = db();
    ensure_game_handoff_table($connection);
    $statement = $connection->prepare(
        'SELECT h.status, h.user_id, u.username, u.school_number, u.first_name, u.last_name, u.role
         FROM game_login_handoffs h
         LEFT JOIN users u ON u.id = h.user_id
         WHERE h.handoff_id = ? AND h.expires_at > NOW()
         LIMIT 1'
    );
    $statement->bind_param('s', $handoffId);
    $statement->execute();
    $row = $statement->get_result()->fetch_assoc();

    if (!$row || $row['status'] !== 'authenticated' || $row['role'] !== 'student') {
        respond(['authenticated' => false]);
    }

    $update = $connection->prepare(
        'UPDATE game_login_handoffs
         SET status = "consumed", consumed_at = NOW()
         WHERE handoff_id = ? AND status = "authenticated"'
    );
    $update->bind_param('s', $handoffId);
    $update->execute();

    respond([
        'authenticated' => true,
        'user_id' => (int) $row['user_id'],
        'username' => $row['username'],
        'student_id' => $row['school_number'],
        'first_name' => $row['first_name'],
        'last_name' => $row['last_name'],
        'game_identity' => $row['username'],
    ]);
} catch (Throwable $error) {
    respond(['authenticated' => false, 'error' => 'Game login confirmation failed.'], 500);
}
