<?php
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

$email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Email inválido']);
    exit;
}

$file     = __DIR__ . '/newsletter-lista.csv';
$existing = file_exists($file) ? file_get_contents($file) : '';

if (strpos($existing, $email) !== false) {
    echo json_encode(['ok' => true, 'msg' => 'Ya estás suscripto']);
    exit;
}

$date = date('Y-m-d H:i:s');
file_put_contents($file, $email . ',' . $date . "\n", FILE_APPEND | LOCK_EX);

$headers  = "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
$headers .= "From: Sitio cpescritor.com <cristian@cpescritor.com>\r\n";
$headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";

@mail(
    'cristian@cpescritor.com',
    '=?UTF-8?B?' . base64_encode('Nueva suscripción — Universo Quemado') . '?=',
    "Nuevo suscriptor:\nEmail: $email\nFecha: $date",
    $headers
);

echo json_encode(['ok' => true]);
