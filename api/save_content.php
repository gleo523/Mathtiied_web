<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

try {
    require_role('teacher');
    $raw = file_get_contents('php://input');
    $data = $raw !== '' ? json_decode($raw, true) : $_POST;
    if (!is_array($data)) {
        throw new InvalidArgumentException('A JSON object or form payload is required.');
    }

    $partId = trim((string)($data['part_id'] ?? ''));
    $type = strtolower(trim((string)($data['type'] ?? '')));
    $content = $data['content'] ?? $data['data'] ?? null;

    if ($partId === '') {
        throw new InvalidArgumentException('part_id is required.');
    }
    if (!in_array($type, ['questions', 'explanations', 'why_check'], true)) {
        throw new InvalidArgumentException('type must be one of: questions, explanations, why_check.');
    }
    if ($content === null) {
        throw new InvalidArgumentException('content is required.');
    }

    $encoded = json_encode($content, JSON_UNESCAPED_SLASHES);
    if ($encoded === false) {
        throw new InvalidArgumentException('content could not be encoded as JSON.');
    }

    $conn = db();
    $stmt = $conn->prepare(
        'INSERT INTO topic_content (part_id, content_type, content_json)
         VALUES (?, ?, ?)
         ON DUPLICATE KEY UPDATE content_json = VALUES(content_json), updated_at = CURRENT_TIMESTAMP'
    );
    $stmt->bind_param('sss', $partId, $type, $encoded);
    $stmt->execute();

    respond([
        'status' => 'saved',
        'part_id' => $partId,
        'type' => $type
    ], 200);
} catch (InvalidArgumentException $error) {
    respond(['error' => $error->getMessage()], 400);
} catch (Throwable $error) {
    respond(['error' => $error->getMessage()], 500);
}
