<?php
declare(strict_types=1);

function sab_csrf_token(): string
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    if (empty($_SESSION['sab_csrf'])) {
        $_SESSION['sab_csrf'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['sab_csrf'];
}

function sab_save_inquiry(PDO $pdo, array $post): array
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $errors = [];
    $token = (string) ($post['csrf'] ?? '');
    if ($token === '' || !hash_equals($_SESSION['sab_csrf'] ?? '', $token)) {
        $errors[] = 'Please refresh the page and try again.';
    }

    $name = trim((string) ($post['name'] ?? ''));
    $phone = trim((string) ($post['phone'] ?? ''));
    $service = trim((string) ($post['service'] ?? ''));
    $message = trim((string) ($post['message'] ?? ''));

    if (mb_strlen($name) < 2) {
        $errors[] = 'Please enter your name.';
    }
    if (mb_strlen($phone) < 7) {
        $errors[] = 'Please enter a valid phone number.';
    }
    if ($message === '') {
        $errors[] = 'Please tell us what you need.';
    }

    if ($errors) {
        return ['ok' => false, 'errors' => $errors];
    }

    $stmt = $pdo->prepare(
        'INSERT INTO inquiries (name, phone, service, message, ip_address) VALUES (:name, :phone, :service, :message, :ip)'
    );
    $stmt->execute([
        ':name' => $name,
        ':phone' => $phone,
        ':service' => $service,
        ':message' => $message,
        ':ip' => $_SERVER['REMOTE_ADDR'] ?? null,
    ]);

    return ['ok' => true, 'errors' => []];
}
