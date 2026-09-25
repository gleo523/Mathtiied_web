<?php
declare(strict_types=1);
require_once __DIR__ . '/../../config/auth.php';

try {
    require_role('admin');
    $result = db()->query('SELECT l.event_type, l.ip_address, l.occurred_at, u.username, u.first_name, u.last_name
        FROM teacher_auth_logs l JOIN users u ON u.id = l.teacher_id
        ORDER BY l.occurred_at DESC LIMIT 200');
    respond(['logs' => $result->fetch_all(MYSQLI_ASSOC)]);
} catch (Throwable $error) {
    respond(['error' => 'Authentication logs unavailable.'], 500);
}
