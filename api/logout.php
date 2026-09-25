<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/auth.php';

$user = current_user();
if ($user && $user['role'] === 'teacher') {
    record_teacher_auth_event((int)$user['id'], 'logout');
}
start_auth_session();
$_SESSION = [];
session_destroy();
respond(['status' => 'logged_out']);
