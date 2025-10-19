<?php
if(session_status() ===    PHP_SESSION_NONE ){
    session_start();
}
function ensureLoggedIn() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../login.php");
        exit;
    }
}

function checkRole($role) {
    if ($_SESSION['role'] !== $role) {
        die("❌ Bạn không có quyền truy cập trang này!");
    }
}
?>
