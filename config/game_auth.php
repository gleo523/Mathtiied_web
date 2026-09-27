<?php
declare(strict_types=1);

require_once __DIR__ . '/database.php';

function ensure_game_access_tokens_table(mysqli $connection): void
{
    $connection->query(
        'CREATE TABLE IF NOT EXISTS game_access_tokens (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            user_id BIGINT UNSIGNED NOT NULL,
            token_hash CHAR(64) NOT NULL UNIQUE,
            expires_at DATETIME NOT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            last_used_at DATETIME NULL,
            KEY idx_game_tokens_user (user_id),
            KEY idx_game_tokens_expiry (expires_at),
            CONSTRAINT fk_game_tokens_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB'
    );
}

function issue_game_access_token(mysqli $connection, int $userId): array
{
    ensure_game_access_tokens_table($connection);
    $token = bin2hex(random_bytes(32));
    $tokenHash = hash('sha256', $token);
    $expiresAt = date('Y-m-d H:i:s', time() + 31536000);

    $statement = $connection->prepare(
        'INSERT INTO game_access_tokens (user_id, token_hash, expires_at) VALUES (?, ?, ?)'
    );
    $statement->bind_param('iss', $userId, $tokenHash, $expiresAt);
    $statement->execute();

    return ['token' => $token, 'expires_at' => $expiresAt];
}

function require_game_access_user(): array
{
    $token = trim((string)($_SERVER['HTTP_X_GAME_TOKEN'] ?? ''));
    if ($token === '' && function_exists('getallheaders')) {
        foreach (getallheaders() as $name => $value) {
            if (strtolower((string)$name) === 'x-game-token') {
                $token = trim((string)$value);
                break;
            }
        }
    }
    if (!preg_match('/^[a-f0-9]{64}$/i', $token)) {
        respond(['error' => 'Game authentication is required.'], 401);
    }

    $connection = db();
    ensure_game_access_tokens_table($connection);
    $tokenHash = hash('sha256', strtolower($token));
    $statement = $connection->prepare(
        'SELECT u.id, u.username, u.school_number
         FROM game_access_tokens t
         INNER JOIN users u ON u.id = t.user_id
         WHERE t.token_hash = ? AND t.expires_at > NOW() AND u.role = "student"
         LIMIT 1'
    );
    $statement->bind_param('s', $tokenHash);
    $statement->execute();
    $user = $statement->get_result()->fetch_assoc();
    if (!$user) {
        respond(['error' => 'Game authentication has expired.'], 401);
    }

    $touch = $connection->prepare('UPDATE game_access_tokens SET last_used_at = NOW() WHERE token_hash = ?');
    $touch->bind_param('s', $tokenHash);
    $touch->execute();

    return $user;
}