<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/database.php';

try {
    $stmt = db()->prepare('SELECT setting_value FROM system_settings WHERE setting_key = "dwti_review_threshold" LIMIT 1');
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    respond(['dwti_review_threshold' => (float)($row['setting_value'] ?? 0.70)]);
} catch (Throwable $error) {
    respond(['error' => 'Settings unavailable.'], 500);
}
