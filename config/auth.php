<?php
declare(strict_types=1);

require_once __DIR__ . '/database.php';

function start_auth_session(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
}

function current_user(): ?array
{
    start_auth_session();
    $id = (int)($_SESSION['user_id'] ?? 0);
    if ($id < 1) {
        return null;
    }

    $stmt = db()->prepare('SELECT id, username, email, role, first_name, last_name, teacher_id FROM users WHERE id = ? LIMIT 1');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc() ?: null;
}

function require_role(string ...$roles): array
{
    $user = current_user();
    if (!$user || !in_array($user['role'], $roles, true)) {
        respond(['error' => 'Authentication required.'], 401);
    }
    return $user;
}

function record_teacher_auth_event(int $teacherId, string $event): void
{
    if (!in_array($event, ['login', 'logout'], true)) {
        return;
    }
    $ip = $_SERVER['REMOTE_ADDR'] ?? null;
    $stmt = db()->prepare('INSERT INTO teacher_auth_logs (teacher_id, event_type, ip_address) VALUES (?, ?, ?)');
    $stmt->bind_param('iss', $teacherId, $event, $ip);
    $stmt->execute();
}
