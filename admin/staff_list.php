<?php
session_start();
require_once '../includes/auth.php';
require_once '../includes/functions.php';

// Kiểm tra quyền admin
if ($_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Lấy ID admin từ session
$admin_id = $_SESSION['user_id'];

// Lấy từ khóa tìm kiếm (nếu có)
$search = $_GET['search'] ?? '';

// Lấy danh sách nhân viên
$staffs = get_staff_list($admin_id, $search);
?>


<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Danh sách nhân viên</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<h2>👨‍💼 Danh sách nhân viên</h2>

<a href="index.php" class="btn">Quay lại trang admin</a>
<table border="1" cellpadding="8">
    <tr>
        <th>ID</th>
        <th>Email</th>
        <th>Họ tên</th>
        <th>Phòng ban</th>
        <th>Ngày tạo</th>
        <th>Hành động</th>
    </tr>
    <?php foreach ($staffs as $s): ?>
    <tr>
        <td><?= $s['id'] ?></td>
        <td><?= htmlspecialchars($s['email']) ?></td>
        <td><?= htmlspecialchars($s['name']) ?></td>
        <td><?= htmlspecialchars($s['department']) ?></td>
        <td><?= $s['created_at'] ?></td>
        <td>
            <a href="update_staff.php?id=<?= $s['id'] ?>">Sửa</a> |
            <a href="delete_staff.php?id=<?= $s['id'] ?>" onclick="return confirm('Bạn có chắc muốn xóa nhân viên này không?')">Xóa</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
</body>
</html>
