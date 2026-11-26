<?php
require_once __DIR__ . '/db_config.php';

$debug = isset($_GET['debug']);
$link = getDbConnection();

$ip = $_SERVER['REMOTE_ADDR'] ?? '';
$ua = $_SERVER['HTTP_USER_AGENT'] ?? '';

$info = @get_browser($ua, true);
$browser = (is_array($info) && !empty($info['browser'])) ? (string) $info['browser'] : 'Nieznana';

$screen_res = mb_substr((string) ($_POST['screen_res'] ?? ''), 0, 20);
$window_res = mb_substr((string) ($_POST['window_res'] ?? ''), 0, 20);
$colors     = mb_substr((string) ($_POST['colors'] ?? ''), 0, 10);
$cookies    = isset($_POST['cookies']) ? (int) $_POST['cookies'] : 0;
$java       = isset($_POST['java']) ? (int) $_POST['java'] : 0;
$lang       = mb_substr((string) ($_POST['language'] ?? ''), 0, 30);
$action     = 'visit';

$stmt = mysqli_prepare(
    $link,
    "INSERT INTO goscieportalu
        (ipaddress, browser, screen_res, window_res, screen_colors,
         cookies_enabled, java_enabled, language, action)
     VALUES (?,?,?,?,?,?,?,?,?)"
);
mysqli_stmt_bind_param(
    $stmt, 'sssssiiss',
    $ip, $browser, $screen_res, $window_res, $colors, $cookies, $java, $lang, $action
);
$ok = mysqli_stmt_execute($stmt);
$stmtErr = $ok ? '' : mysqli_stmt_error($stmt);
mysqli_stmt_close($stmt);
mysqli_close($link);

if (!$ok) {
    http_response_code(500);
    if ($debug) { echo 'INSERT failed: ' . $stmtErr; exit; }
    echo json_encode(['status' => 'error']);
    exit;
}
if ($debug) { header('Content-Type: text/plain; charset=utf-8'); echo "OK – visit zapisany."; exit; }
echo json_encode(['status' => 'ok']);