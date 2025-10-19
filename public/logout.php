<?php
// public/logout.php
require_once __DIR__ . '/../includes/functions.php';
session_start();
if (isset($_SESSION['user_id'])) {
    addLog($_SESSION['user_id'], 'LOGOUT', 'Người dùng đăng xuất');
}
session_unset();
session_destroy();
header('Location: login.php');
exit;
