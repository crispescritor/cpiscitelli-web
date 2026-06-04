<?php
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

$nombre  = htmlspecialchars(strip_tags(trim($_POST['nombre']  ?? '')));
$email   = filter_var(trim($_POST['email']   ?? ''), FILTER_SANITIZE_EMAIL);
$mensaje = htmlspecialchars(strip_tags(trim($_POST['mensaje'] ?? '')));

if (!$nombre || !filter_var($email, FILTER_VALIDATE_EMAIL) || !$mensaje) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Datos incompletos o inválidos']);
    exit;
}

$to      = 'cristian@cpescritor.com';
$subject = '=?UTF-8?B?' . base64_encode("Mensaje desde cpescritor.com — $nombre") . '?=';
$body    = "Nombre: $nombre\nEmail: $email\n\nMensaje:\n$mensaje";

$headers  = "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
$headers .= "From: Sitio cpescritor.com <cristian@cpescritor.com>\r\n";
$headers .= "Reply-To: $nombre <$email>\r\n";
$headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";

$result = @mail($to, $subject, $body, $headers);

if (!$result) {
    error_log('[cpescritor] form-handler: mail() falló para ' . $email);
}

echo json_encode(['ok' => $result === false ? false : true]);
