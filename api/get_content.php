<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

try {
    require_role('teacher', 'admin');
    $partId = trim((string)($_GET['part_id'] ?? ''));
    $type = strtolower(trim((string)($_GET['type'] ?? '')));

    if ($partId === '') {
        respond(['error' => 'part_id is required.'], 400);
    }

    $allowed = ['questions', 'explanations', 'why_check'];
    if (!in_array($type, $allowed, true)) {
        respond(['error' => 'type must be one of: questions, explanations, why_check.'], 400);
    }

    $stmt = db()->prepare(
        'SELECT content_json FROM topic_content WHERE part_id = ? AND content_type = ? LIMIT 1'
    );
    $stmt->bind_param('ss', $partId, $type);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();

    if (!$row || !isset($row['content_json'])) {
        if ($type === 'why_check') {
            respond([], 200);
        }
        respond([], 200);
    }

    $payload = json_decode((string)$row['content_json'], true);
    respond($payload !== null ? $payload : [], 200);
} catch (Throwable $error) {
    respond(['error' => $error->getMessage()], 500);
}
