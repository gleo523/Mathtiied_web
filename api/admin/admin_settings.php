<?php
declare(strict_types=1);
require_once __DIR__ . '/../../config/auth.php';

try {
    require_role('admin');
    $conn = db();
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = request_json();
        $threshold = (float)($data['dwti_review_threshold'] ?? 0.70);
        if ($threshold < 0 || $threshold > 1) {
            throw new InvalidArgumentException('Threshold must be between 0 and 1.');
        }
        $value = number_format($threshold, 4, '.', '');
        $stmt = $conn->prepare('INSERT INTO system_settings (setting_key, setting_value) VALUES ("dwti_review_threshold", ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)');
        $stmt->bind_param('s', $value);
        $stmt->execute();
    }
    $result = $conn->query('SELECT setting_key, setting_value, updated_at FROM system_settings ORDER BY setting_key');
    respond(['settings' => $result->fetch_all(MYSQLI_ASSOC)]);
} catch (InvalidArgumentException $error) {
    respond(['error' => $error->getMessage()], 400);
} catch (Throwable $error) {
    respond(['error' => $error->getMessage()], 500);
}
