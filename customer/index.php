<?php
require_once __DIR__ . '/../includes/functions.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header('Location: ../login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT username, email FROM users WHERE id = ? LIMIT 1");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thông tin cá nhân</title>
    <link rel="stylesheet" href="../../assets/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f3f3f3;
            padding: 20px;
        }
        .container {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            width: 400px;
            margin: 50px auto;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        h2 {
            text-align: center;
        }
        .btn {
            display: inline-block;
            background: #007bff;
            color: #fff;
            padding: 10px 15px;
            border-radius: 5px;
            text-decoration: none;
            margin: 5px;
        }
        .btn:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Thông tin cá nhân</h2>
    <p><strong>Username:</strong> <?= htmlspecialchars($user['username']) ?></p>
    <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>

    <a class="btn" href="update_profile.php">✏️ Cập nhật thông tin</a>
    <a class="btn" href="view_data.php">👁️ Xem chi tiết</a>
    <a class="btn" href="../public/logout.php">🚪 Đăng xuất</a>
</div>
</body>
</html>
