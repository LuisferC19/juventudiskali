<?php
/**
 * iski_chat.php — Backend del koala Iski (Gemini API)
 *
 * MEJORAS:
 * - Access-Control-Allow-Origin restringido al origen del propio sistema.
 * - Validación y límite de longitud del mensaje de entrada.
 * - Respuesta de error estructurada en JSON (no HTML).
 * - Tiempo límite de cURL para evitar que una llamada lenta bloquee el servidor.
 * - La API key nunca se expone en respuestas de error.
 */

header('Content-Type: application/json; charset=utf-8');

// 🔒 Cambia '*' por tu dominio real en producción, p.ej.:
//    header('Access-Control-Allow-Origin: https://tudominio.com');
// En desarrollo con Laragon localhost está bien así:
header('Access-Control-Allow-Origin: http://localhost');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Pre-flight OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// Solo aceptar POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido.']);
    exit;
}

$env    = require __DIR__ . '/env.php';
$apiKey = trim($env['GEMINI_KEY'] ?? '');

if (empty($apiKey)) {
    http_response_code(503);
    echo json_encode(['respuesta' => 'El servicio de chat no está configurado aún.']);
    exit;
}

// Leer y validar el body
$raw  = file_get_contents('php://input');
$data = json_decode($raw, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode(['error' => 'JSON inválido.']);
    exit;
}

// Límite de longitud: máximo 500 caracteres para evitar abusos
$mensaje = trim((string)($data['mensaje'] ?? ''));
if (empty($mensaje)) {
    http_response_code(400);
    echo json_encode(['error' => 'El mensaje no puede estar vacío.']);
    exit;
}
$mensaje = mb_substr($mensaje, 0, 500);

// Construir el payload para Gemini
$body = json_encode([
    'contents' => [[
        'parts' => [[
            'text' =>
                'Eres Iski, la mascota koala de Fundación Iskali A.C. ' .
                'Eres amigable, simpático y muy breve. Solo respondes en español. ' .
                'Ayudas a usuarios a iniciar sesión en el sistema administrativo. ' .
                'Máximo 2 oraciones cortas. Mensaje del usuario: ' . $mensaje
        ]]
    ]]
]);

$url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=' . urlencode($apiKey);

$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => $body,
    CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
    CURLOPT_TIMEOUT        => 10,   // máximo 10 segundos
    CURLOPT_CONNECTTIMEOUT => 5,    // máximo 5 segundos para conectar
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlErr  = curl_error($ch);
curl_close($ch);

if ($curlErr || $response === false) {
    logger('iski_chat cURL error: ' . $curlErr);
    http_response_code(502);
    echo json_encode(['respuesta' => '¡Ups! No pude conectarme ahora. Intenta de nuevo.']);
    exit;
}

$result = json_decode($response, true);
$texto  = $result['candidates'][0]['content']['parts'][0]['text'] ?? null;

if ($texto === null) {
    logger('iski_chat respuesta inesperada de Gemini (HTTP ' . $httpCode . '): ' . substr($response, 0, 200));
    echo json_encode(['respuesta' => '¡Hola! Soy Iski 👋 ¿En qué te puedo ayudar?']);
    exit;
}

echo json_encode(['respuesta' => trim($texto)]);