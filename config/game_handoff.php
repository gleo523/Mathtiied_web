<?php
declare(strict_types=1);

require_once __DIR__ . '/database.php';

function ensure_game_handoff_table(mysqli $connection): void
{
    $connection->query(
        'CREATE TABLE IF NOT EXISTS game_login_handoffs (
            handoff_id CHAR(64) PRIMARY KEY,
            user_id BIGINT UNSIGNED NULL,
            status ENUM("pending", "authenticated", "consumed") NOT NULL DEFAULT "pending",
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            expires_at DATETIME NOT NULL,
            authenticated_at DATETIME NULL,
            consumed_at DATETIME NULL,
            KEY idx_game_handoff_expiry (expires_at),
            CONSTRAINT fk_game_handoff_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB'
    );
}

function validate_handoff_id(string $handoffId): bool
{
    return (bool) preg_match('/^[a-f0-9]{64}$/', $handoffId);
}
