<?php
session_start();
if (!isset($_SESSION['user_name'])) {
    header("Location: login.php");
    exit;
}
echo "<h2>Xin chào, {$_SESSION['user_name']} ({$_SESSION['user_role']})</h2>";
echo "<a href='logout.php'>Đăng xuất</a><br>";

if ($_SESSION['user_role'] === 'admin') {
    echo "<a href='admin.php'>Trang quản trị</a>";
} elseif ($_SESSION['user_role'] === 'staff') {
    echo "<a href='staff.php'>Trang nhân viên</a>";
} else {
    echo "<a href='customer.php'>Thông tin cá nhân</a>";
}
?>
