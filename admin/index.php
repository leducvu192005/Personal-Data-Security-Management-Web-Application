<?php
if( session_status()===     PHP_SESSION_NONE){
    session_start();
}
require_once '../includes/auth.php';
require_once '../includes/functions.php';
require_once '../includes/header.php';

// Kiểm tra quyền admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Lấy tổng số nhân viên
$stmt = $conn->query("SELECT COUNT(*) AS total_staff FROM users WHERE role = 'staff'");
$total_staff = $stmt->fetch(PDO::FETCH_ASSOC)['total_staff'];

// Lấy tổng số log
$stmt = $conn->query("SELECT COUNT(*) AS total_logs FROM audit_logs");
$total_logs = $stmt->fetch(PDO::FETCH_ASSOC)['total_logs'];

// Lấy danh sách log gần nhất
$stmt = $conn->query("SELECT a.action, a.masked_data, a.created_at, u.username 
                     FROM audit_logs a 
                     JOIN users u ON a.user_id = u.id 
                     ORDER BY a.created_at DESC LIMIT 5");
$recent_logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f7fa;
            padding: 20px;
        }
        h1 {
            color: #333;
        }
        .stats {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
        }
        .card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            flex: 1;
            text-align: center;
        }
        .links {
            margin: 30px 0;
        }
        .links a {
            display: inline-block;
            background: #007bff;
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
            margin-right: 10px;
            text-decoration: none;
        }
        .links a:hover {
            background: #0056b3;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 10px;
            overflow: hidden;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        th {
            background: #f0f0f0;
        }
        tr:nth-child(even) {
            background: #fafafa;
        }
    </style>
</head>
<body>

<h1>👑 Bảng điều khiển quản trị viên</h1>
<p>Xin chào, <b><?= htmlspecialchars($_SESSION['username']) ?></b>!</p>

<div class="stats">
    <div class="card">
        <h2><?= $total_staff ?></h2>
        <p>Nhân viên hiện tại</p>
    </div>
    <div class="card">
        <h2><?= $total_logs ?></h2>
        <p>Hoạt động được ghi lại</p>
    </div>
</div>

<div class="links">
    <a href="staff_list.php">👨‍💼 Quản lý nhân viên</a>
    <a href="create_staff.php">➕ Thêm nhân viên</a>
    <a href="update_staff.php">Sửa nhân viên</a>
    <a href="delete_staff.php">Xóa nhân viên</a>
    <a href="audit_log.php">📋 Nhật ký hoạt động</a>
    <a href="../public/logout.php">🚪 Đăng xuất</a>
</div>

<h3>🕓 Hoạt động gần đây</h3>
<table>
    <tr>
        <th>Người thực hiện</th>
        <th>Hành động</th>
        <th>Chi tiết</th>
        <th>Thời gian</th>
    </tr>
    <?php if (count($recent_logs) > 0): ?>
        <?php foreach ($recent_logs as $log): ?>
        <tr>
            <td><?= htmlspecialchars($log['username']) ?></td>
            <td><?= htmlspecialchars($log['action']) ?></td>
            <td><?= htmlspecialchars($log['masked_data']) ?></td>
            <td><?= htmlspecialchars($log['created_at']) ?></td>
        </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr><td colspan="4" style="text-align:center;">Chưa có hoạt động nào được ghi lại.</td></tr>
    <?php endif; ?>
</table>

</body>
</html>
