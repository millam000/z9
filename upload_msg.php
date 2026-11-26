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

if ($recipient === '') {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Wybierz odbiorcę pliku']);
    exit;
}

if (!isset($_FILES['fileToUpload']) || $_FILES['fileToUpload']['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Nie wybrano pliku lub błąd przesyłania']);
    exit;
}

// Katalog macierzysty użytkownika = jego login (pkt 5 i 7 instrukcji)
$target_dir = $sender;
$full_dir   = __DIR__ . '/' . $target_dir;
if (!is_dir($full_dir)) {
    @mkdir($full_dir, 0777, true);
}

$originalName = basename((string) $_FILES['fileToUpload']['name']);
$safeName     = preg_replace('/[^a-zA-Z0-9._-]/', '_', $originalName);
$target_file  = $target_dir . '/' . time() . '_' . $safeName;

// Brak ograniczania typów i rozmiaru plików zgodnie z pkt. 8c instrukcji
if (!move_uploaded_file($_FILES['fileToUpload']['tmp_name'], __DIR__ . '/' . $target_file)) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Błąd zapisu pliku na serwerze']);
    exit;
}

$link = getDbConnection();
$stmt = mysqli_prepare($link, "INSERT INTO messages (message, user, recipient) VALUES (?, ?, ?)");
mysqli_stmt_bind_param($stmt, 'sss', $target_file, $sender, $recipient);
$ok = mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);
mysqli_close($link);

if (!$ok) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Błąd zapisu ścieżki pliku w bazie']);
    exit;
}

echo json_encode(['status' => 'ok', 'file' => $target_file]);