<?php
declare(strict_types=1);
require_once __DIR__ . '/session_config.php';
require_once __DIR__ . '/db_config.php';
session_start();

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Brak autoryzacji']);
    exit;
}

$sender    = (string) $_SESSION['username'];
$recipient = trim((string) ($_POST['recipient'] ?? ''));
$post      = trim((string) ($_POST['post'] ?? ''));

if ($recipient === '' || $post === '') {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Wybierz odbiorcę i wpisz treść wiadomości']);
    exit;
}

$link = getDbConnection();

$stmt = mysqli_prepare($link, "INSERT INTO messages (message, user, recipient) VALUES (?, ?, ?)");
mysqli_stmt_bind_param($stmt, 'sss', $post, $sender, $recipient);
$ok = mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);
mysqli_close($link);

if (!$ok) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Błąd zapisu wiadomości']);
    exit;
}

echo json_encode(['status' => 'ok']);