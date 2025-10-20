<?php
session_start();
require_once '../config/db.php'; // Kết nối CSDL (PDO)

// ✅ Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: ../public/login.php");
    exit();
}

// ✅ Lấy thông tin nhân viên đang đăng nhập từ SESSION
$current_user = [
    'id' => $_SESSION['user_id'],
    'username' => $_SESSION['username'],
    'role' => $_SESSION['role']
];
$created_by = $current_user['username'];
$user_id = $current_user['id'];

// ✅ Khi nhấn nút "Thêm khách hàng"
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $cmnd  = trim($_POST['cmnd']);

    // Kiểm tra dữ liệu bắt buộc
    if ($name === '' || $email === '' || $phone === '' || $cmnd === '') {
        echo "<script>alert('Vui lòng nhập đầy đủ thông tin khách hàng'); history.back();</script>";
        exit();
    }

    try {
        // Chuẩn bị câu lệnh INSERT
        $stmt = $conn->prepare("
            INSERT INTO customers (name, email, phone, cmnd, created_by, created_at, user_id)
            VALUES (:name, :email, :phone, :cmnd, :created_by, NOW(), :user_id)
        ");

        // Gán giá trị tham số
        $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':phone' => $phone,
            ':cmnd' => $cmnd,
            ':created_by' => $created_by,
            ':user_id' => $user_id
        ]);

        echo "<script>alert('✅ Thêm khách hàng thành công!'); window.location.href='index.php';</script>";
    } catch (PDOException $e) {
        echo "<script>alert('❌ Lỗi khi lưu dữ liệu: " . addslashes($e->getMessage()) . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm khách hàng mới</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f7fa;
            text-align: center;
            padding-top: 40px;
        }
        form {
            background: #fff;
            display: inline-block;
            padding: 25px 40px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        input {
            width: 100%;
            margin-bottom: 12px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
        }
        button {
            background: #007bff;
            color: white;
            border: none;
            padding: 10px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
        }
        button:hover {
            background: #0056b3;
        }
        a {
            display: inline-block;
            margin-top: 10px;
            color: #555;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <h2>🧾 Thêm khách hàng mới</h2>
    <form method="POST">
        <input type="text" name="name" placeholder="Tên khách hàng" required>
        <input type="email" name="email" placeholder="Email khách hàng" required>
        <input type="text" name="phone" placeholder="Số điện thoại" pattern="[0-9]{10,11}" title="Nhập 10-11 số" required>
        <input type="text" name="cmnd" placeholder="CMND/CCCD" pattern="[0-9]{9,12}" title="Nhập 9-12 số" required>
        <button type="submit">Lưu khách hàng</button><br>
        <a href="index.php">⬅ Quay lại</a>
    </form>
</body>
</html>
