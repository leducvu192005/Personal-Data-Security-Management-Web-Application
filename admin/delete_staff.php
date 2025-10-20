<?php
require_once '../includes/functions.php';

// Lấy ID từ URL
$id = $_GET['id'] ?? null;
if (!$id) {
    die("Thiếu ID nhân viên để xóa.");
}

// Lấy tên nhân viên (để ghi log)
$stmt = $conn->prepare("SELECT username FROM users WHERE id = ? AND role = 'staff'");
$stmt->execute([$id]);
$staff = $stmt->fetch();
$name = $staff['username'] ?? 'Không rõ';

try {
    $conn->beginTransaction();

    // Xóa trong bảng phụ
    $stmt = $conn->prepare("DELETE FROM staff_info WHERE user_id = ?");
    $stmt->execute([$id]);

    // Xóa trong bảng users
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ? AND role = 'staff'");
    $stmt->execute([$id]);

    $conn->commit();

    // Ghi log hành động

    header("Location: staff_list.php");
    exit;
} catch (Exception $e) {
    $conn->rollBack();
    echo "Lỗi khi xóa: " . htmlspecialchars($e->getMessage());
}
?>
