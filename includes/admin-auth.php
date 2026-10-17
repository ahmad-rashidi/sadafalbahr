<?php
declare(strict_types=1);

const SAB_ADMIN_USERNAME = 'admin';
const SAB_ADMIN_PASSWORD_HASH = '$2y$10$VS9loCiNLVFqhCr9MqgbO.ZpNH9WBqlv.M8UJE4BGYsM4iUAaJtEm';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function sab_require_admin(): void
{
    if (empty($_SESSION['sab_admin'])) {
        header('Location: login.php');
        exit;
    }
}

function sab_admin_csrf(): string
{
    if (empty($_SESSION['sab_admin_csrf'])) {
        $_SESSION['sab_admin_csrf'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['sab_admin_csrf'];
}

function sab_admin_csrf_ok(string $token): bool
{
    return $token !== '' && hash_equals($_SESSION['sab_admin_csrf'] ?? '', $token);
}
