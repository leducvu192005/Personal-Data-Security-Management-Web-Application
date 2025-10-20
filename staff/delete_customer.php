<?php
require_once "../includes/auth.php";
require_once "../includes/functions.php";
ensureLoggedIn();
checkRole('staff');

$id = $_GET['id'] ?? 0;
$staff_id = $_SESSION['user']['id'];

try {
    $stmt = $conn->prepare("DELETE FROM customers WHERE id = ?");
    $stmt->execute([$id]);
    addLog($staff_id, 'DELETE_CUSTOMER', "Xóa khách hàng ID {$id}");
    header("Location: index.php");
    exit;
} catch (Exception $e) {
    echo "<p style='color:red;'>Lỗi: " . htmlspecialchars($e->getMessage()) . "</p>";
}
