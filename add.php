<?php
require_once __DIR__ . '/session_config.php';
require_once __DIR__ . '/db_config.php';
session_start();

$user = trim((string) ($_POST['user'] ?? ''));
$pass = (string) ($_POST['pass'] ?? '');
$pass_repeat = (string) ($_POST['pass2'] ?? '');

if ($pass_repeat !== $pass) {
    header("Location: register.php?error=1");
    exit();
}
if ($user === "" || $pass === "") {
    header("Location: register.php?error=2");
    exit();
}
// Zabezpieczenie nazwy katalogu macierzystego (loginu)
if (!preg_match('/^[a-zA-Z0-9_-]{1,30}$/', $user)) {
    header("Location: register.php?error=4");
    exit();
}

$link = getDbConnection();

$stmt = mysqli_prepare($link, "SELECT id FROM users WHERE username = ?");
mysqli_stmt_bind_param($stmt, 's', $user);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
if (mysqli_fetch_array($result)) {
    mysqli_stmt_close($stmt);
    mysqli_close($link);
    header("Location: register.php?error=3");
    exit();
}
mysqli_stmt_close($stmt);

// Utworzenie katalogu macierzystego użytkownika (pkt 5 instrukcji)
$userHomeDir = __DIR__ . '/' . $user;
if (!is_dir($userHomeDir)) {
    @mkdir($userHomeDir, 0777, true);
}

$avatar = 'media/avatars/default.png';
if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
    if (!is_dir(__DIR__ . '/media/avatars')) {
        @mkdir(__DIR__ . '/media/avatars', 0777, true);
    }
    $target = 'media/avatars/' . uniqid() . '_' . basename($_FILES['avatar']['name']);
    if (move_uploaded_file($_FILES['avatar']['tmp_name'], __DIR__ . '/' . $target)) {
        $avatar = $target;
    }
}

$stmtIns = mysqli_prepare($link, "INSERT INTO users (username, password, avatar) VALUES (?, ?, ?)");
mysqli_stmt_bind_param($stmtIns, 'sss', $user, $pass, $avatar);
$ok = mysqli_stmt_execute($stmtIns);
mysqli_stmt_close($stmtIns);
mysqli_close($link);

if ($ok) {
    header("Location: login.php");
    exit();
}
header("Location: register.php");
exit();