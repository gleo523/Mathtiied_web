<?php
declare(strict_types=1);

function db(): mysqli
{
    static $connection;
    if ($connection instanceof mysqli) {
        return $connection;
    }

    $connection = new mysqli(
        getenv('MATHTIFIED_DB_HOST') ?: '127.0.0.1',
        getenv('MATHTIFIED_DB_USER') ?: 'root',
        getenv('MATHTIFIED_DB_PASSWORD') ?: '',
        getenv('MATHTIFIED_DB_NAME') ?: 'mathtified_db'
    );

    if ($connection->connect_errno) {
        throw new RuntimeException('Database connection failed.');
    }

    $connection->set_charset('utf8mb4');
    return $connection;
}

function request_json(): array
{
    $payload = json_decode(file_get_contents('php://input'), true);
    if (!is_array($payload)) {
        throw new InvalidArgumentException('A JSON object is required.');
    }
    return $payload;
}

function respond(array $payload, int $status = 200): never
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($payload, JSON_UNESCAPED_SLASHES);
    exit;
}

function require_string(array $data, array $keys): string
{
    foreach ($keys as $key) {
        if (isset($data[$key]) && is_scalar($data[$key]) && trim((string)$data[$key]) !== '') {
            return trim((string)$data[$key]);
        }
    }
    throw new InvalidArgumentException('Missing required field: ' . $keys[0]);
}
