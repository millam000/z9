<?php
declare(strict_types=1);
require_once __DIR__ . '/session_config.php';
require_once __DIR__ . '/db_config.php';
session_start();

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, no-store, must-revalidate');

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Brak autoryzacji']);
    exit;
}

$currentUser = (string) $_SESSION['username'];
$isAdmin     = ($currentUser === 'admin');
$lastId      = isset($_GET['last_id']) ? (int) $_GET['last_id'] : 0;
session_write_close();

$link = getDbConnection();

// 1. Pobierz aktualną listę użytkowników (w tym nowo dodanych przez wykładowcę)
$users = [];
$resUsers = mysqli_query($link, "SELECT id, username, avatar FROM users ORDER BY username ASC");
if ($resUsers) {
    while ($u = mysqli_fetch_assoc($resUsers)) {
        $users[] = [
            'id'       => (int) $u['id'],
            'username' => $u['username'],
            'avatar'   => !empty($u['avatar']) ? $u['avatar'] : 'media/avatars/default.png',
        ];
    }
}

// 2. Pobierz tylko NOWE wiadomości (idk > $lastId)
$messages = [];
if ($isAdmin) {
    $stmt = mysqli_prepare(
        $link,
        "SELECT idk, datetime, message, user, recipient 
         FROM messages 
         WHERE idk > ? 
         ORDER BY idk ASC"
    );
    mysqli_stmt_bind_param($stmt, 'i', $lastId);
} else {
    $stmt = mysqli_prepare(
        $link,
        "SELECT idk, datetime, message, user, recipient 
         FROM messages 
         WHERE idk > ? AND (user = ? OR recipient = ?) 
         ORDER BY idk ASC"
    );
    mysqli_stmt_bind_param($stmt, 'iss', $lastId, $currentUser, $currentUser);
}

mysqli_stmt_execute($stmt);
$resMsg = mysqli_stmt_get_result($stmt);

if ($resMsg) {
    while ($m = mysqli_fetch_assoc($resMsg)) {
        $messages[] = [
            'idk'       => (int) $m['idk'],
            'datetime'  => $m['datetime'],
            'message'   => $m['message'],
            'user'      => $m['user'],
            'recipient' => $m['recipient'],
        ];
    }
}

mysqli_stmt_close($stmt);
mysqli_close($link);

echo json_encode([
    'status'   => 'ok',
    'users'    => $users,
    'messages' => $messages,
], JSON_UNESCAPED_UNICODE);