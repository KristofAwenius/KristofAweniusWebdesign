<?php
header('Content-Type: application/json; charset=utf-8');

// Nur POST erlauben
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Methode nicht erlaubt.']);
    exit;
}

// Eingaben bereinigen
$name      = trim(strip_tags($_POST['name'] ?? ''));
$email     = trim(strip_tags($_POST['email'] ?? ''));
$nachricht = trim(strip_tags($_POST['nachricht'] ?? ''));

// Validierung
if (empty($name) || empty($email) || empty($nachricht)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Bitte alle Felder ausfüllen.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Ungültige E-Mail-Adresse.']);
    exit;
}

// E-Mail zusammenstellen
$empfaenger = 'kontakt@awenius-webdesign.de';
$betreff    = '=?UTF-8?B?' . base64_encode('Neue Anfrage von ' . $name) . '?=';
$inhalt     = "Name:    $name\r\nE-Mail:  $email\r\n\r\nNachricht:\r\n$nachricht";
$headers    = implode("\r\n", [
    'From: kontakt@awenius-webdesign.de',
    'Reply-To: ' . $email,
    'Content-Type: text/plain; charset=UTF-8',
    'Content-Transfer-Encoding: 8bit',
]);

if (mail($empfaenger, $betreff, $inhalt, $headers)) {
    echo json_encode(['success' => true]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'E-Mail konnte nicht gesendet werden. Bitte versuche es später erneut.']);
}
