<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../includes/functions.php';
require_once '../config/db.php'; // Đảm bảo kết nối PDO có trong file này

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $name = $_POST['name'];
    $department = $_POST['department'];

    $conn->beginTransaction();
    try {
        // Lưu tài khoản (email là tên đăng nhập)
        $stmt = $conn->prepare("INSERT INTO users (email, password, role) VALUES (?, ?, 'staff')");
        $stmt->execute([$email, $password]);
        $user_id = $conn->lastInsertId();

        // Lưu thông tin nhân viên
        $stmt = $conn->prepare("INSERT INTO staff_info (user_id, name, department) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $name, $department]);

        addLog($_SESSION['user_id'], 'CREATE_STAFF', "Tạo nhân viên: $email");
        $conn->commit();
        header("Location: staff_list.php");
        exit;
    } catch (Exception $e) {
        $conn->rollBack();
        echo "<p class='error'>Lỗi: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Thêm nhân viên mới</title>
<style>
    * { box-sizing: border-box; font-family: 'Poppins', sans-serif; }
    body {
        background: #f4f6f8;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        margin: 0;
    }
    .container {
        background: #fff;
        padding: 40px;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        width: 380px;
        position: relative;
    }
    h2 {
        text-align: center;
        margin-bottom: 25px;
        color: #333;
        font-weight: 600;
    }
    input {
        width: 100%;
        padding: 10px;
        margin-bottom: 15px;
        border-radius: 6px;
        border: 1px solid #ccc;
        font-size: 14px;
        transition: border-color 0.2s ease;
    }
    input:focus {
        border-color: #007bff;
        outline: none;
    }
    button {
        width: 100%;
        padding: 12px;
        background: #007bff;
        border: none;
        color: white;
        border-radius: 6px;
        font-size: 15px;
        font-weight: 500;
        cursor: pointer;
        transition: background 0.3s ease;
    }
    button:hover {
        background: #0056b3;
    }
    .back-btn {
        position: absolute;
        top: 15px;
        left: 15px;
        background: #6c757d;
        padding: 8px 14px;
        border-radius: 6px;
        color: white;
        text-decoration: none;
        font-size: 13px;
        font-weight: 500;
        transition: background 0.3s ease;
    }
    .back-btn:hover {
        background: #5a6268;
    }
    .error {
        color: red;
        text-align: center;
        margin-bottom: 10px;
    }
</style>
</head>
<body>
    <div class="container">
        <a href="../admin/index.php" class="back-btn">← Quay lại trang quản trị</a>
        <h2>Thêm nhân viên mới</h2>
        <form method="POST">
            <input type="email" name="email" placeholder="Email (tên đăng nhập)" required>
            <input type="password" name="password" placeholder="Mật khẩu" required>
            <input type="text" name="name" placeholder="Họ tên" required>
            <input type="text" name="department" placeholder="Phòng ban">
            <button type="submit">Thêm nhân viên</button>
        </form>
    </div>
</body>
</html>
