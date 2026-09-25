<?php
declare(strict_types=1);

require_once __DIR__ . '/auth.php';

$adminUser = current_user();
if (!$adminUser || $adminUser['role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}
