<?php
require_once __DIR__ . '/session_config.php';
session_start();
session_unset();
session_destroy();
header('Location: login.php');
exit();
?>