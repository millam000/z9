<?php
require_once __DIR__ . '/session_config.php';
require_once __DIR__ . '/db_config.php';
session_start();

$user = trim((string) ($_POST['user'] ?? ''));
$pass = (string) ($_POST['pass'] ?? '');

$link = getDbConnection();

$stmt = mysqli_prepare($link, "SELECT * FROM users WHERE username = ?");
mysqli_stmt_bind_param($stmt, 's', $user);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$rekord = mysqli_fetch_array($result);
mysqli_stmt_close($stmt);

$ok = $rekord && $rekord['password'] === $pass;

$ip = $_SERVER['REMOTE_ADDR'] ?? '';
$ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
$info = @get_browser($ua, true);
$browser = (is_array($info) && !empty($info['browser'])) ? (string) $info['browser'] : 'Nieznana';

$action = $ok ? 'login_ok' : 'login_fail';
$empty  = '';
$zero   = 0;

$stmtLog = mysqli_prepare(
    $link,
    "INSERT INTO goscieportalu
        (ipaddress, browser, screen_res, window_res, screen_colors,
         cookies_enabled, java_enabled, language, action, username_attempt)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
);
mysqli_stmt_bind_param(
    $stmtLog,
    'sssssiisss',
    $ip, $browser, $empty, $empty, $empty, $zero, $zero, $empty, $action, $user
);
mysqli_stmt_execute($stmtLog);
mysqli_stmt_close($stmtLog);

if (!$ok) {
    mysqli_close($link);
    header("Location: login.php?error=1");
    exit();
}

// Upewnij się, że katalog macierzysty użytkownika istnieje
$userHomeDir = __DIR__ . '/' . $rekord['username'];
if (!is_dir($userHomeDir)) {
    @mkdir($userHomeDir, 0777, true);
}

$_SESSION["logged_in"] = true;
$_SESSION["username"]  = $rekord['username'];
$_SESSION["avatar"]    = $rekord['avatar'] ?: 'media/avatars/default.png';
$_SESSION["idu"]       = (int) $rekord['id'];

mysqli_close($link);
header("Location: index.php");
exit();