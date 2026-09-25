<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/game_handoff.php';
require_once __DIR__ . '/../config/auth.php';

try {
    $user = require_role('student');
    $data = request_json();
    $handoffId = trim((string) ($data['handoff_id'] ?? ''));

    if (!validate_handoff_id($handoffId)) {
        throw new InvalidArgumentException('Invalid game login request.');
    }

    $connection = db();
    ensure_game_handoff_table($connection);
    $statement = $connection->prepare(
        'UPDATE game_login_handoffs
         SET user_id = ?, status = "authenticated", authenticated_at = NOW()
         WHERE handoff_id = ? AND status = "pending" AND expires_at > NOW()'
    );
    $userId = (int) $user['id'];
    $statement->bind_param('is', $userId, $handoffId);
    $statement->execute();

    if ($statement->affected_rows !== 1) {
        throw new InvalidArgumentException('This game login request expired or was already used.');
    }

    respond(['status' => 'authenticated', 'handoff_id' => $handoffId]);
} catch (InvalidArgumentException $error) {
    respond(['error' => $error->getMessage()], 400);
} catch (Throwable $error) {
    respond(['error' => 'Could not confirm game login.'], 500);
}
