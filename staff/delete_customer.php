<?php
require_once "../includes/auth.php";
require_once "../includes/functions.php";
require_once "../includes/activity_logger.php"; // ✅ Ghi log hoạt động

ensureLoggedIn();
checkRole('staff');

$id = $_GET['id'] ?? 0;
$staff_id = $_SESSION['user']['id'];
$staff_name = $_SESSION['user']['username'] ?? 'unknown';

try {
    // ✅ Xóa khách hàng
    $stmt = $conn->prepare("DELETE FROM customers WHERE id = ?");
    $stmt->execute([$id]);

    // ✅ Ghi log hành động xóa
    logActivity(
        'XÓA_KHÁCH_HÀNG',
        [
            'message' => "Nhân viên {$staff_name} (ID {$staff_id}) đã xóa khách hàng có ID {$id}",
            'customer_id' => $id
        ]
    );

    header("Location: index.php");
    exit;
} catch (Exception $e) {
    // ✅ Ghi log lỗi nếu có
    logActivity(
        'LỖI_XÓA_KHÁCH_HÀNG',
        [
            'message' => "Nhân viên {$staff_name} (ID {$staff_id}) gặp lỗi khi xóa khách hàng ID {$id}",
            'error' => $e->getMessage()
        ]
    );

    echo "<p style='color:red;'>Lỗi: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
