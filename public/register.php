<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/encryption.php';
require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy dữ liệu từ form và lọc đầu cuối chuỗi
    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $phone    = trim($_POST['phone']);
    $cmnd     = trim($_POST['cmnd']);
    $password = $_POST['password'];

    // ✅ Kiểm tra các trường bắt buộc
    if (empty($name) || empty($email) || empty($password)) {
        echo "<script>alert('Vui lòng nhập đầy đủ thông tin!'); history.back();</script>";
        exit;
    }

    // ✅ Kiểm tra email tồn tại trong bảng users
    $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check->execute([$email]);
    if ($check->fetch()) {
        echo "<script>alert('Email này đã tồn tại!'); window.location='register.php';</script>";
        exit;
    }

    // 🔒 Mã hóa dữ liệu nhạy cảm
    $enc_phone = encryptData($phone);
    $enc_cmnd  = encryptData($cmnd);

    // 🔐 Băm mật khẩu
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    try {
        $conn->beginTransaction();

        // ✅ Thêm vào bảng users
        $stmt = $conn->prepare("
            INSERT INTO users (username, email, password, role, created_at)
            VALUES (?, ?, ?, 'customer', NOW())
        ");
        $stmt->execute([$name, $email, $hashed_password]);

        // ✅ Lấy ID user mới thêm
        $user_id = $conn->lastInsertId();

        // ✅ Thêm vào bảng customers
        $stmt = $conn->prepare("
            INSERT INTO customers (user_id, name, email, phone, cmnd)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([$user_id, $name, $email, $enc_phone, $enc_cmnd]);

        // 🧾 Ghi log
        addLog($user_id, 'REGISTER', "Khách hàng mới đăng ký: $email");

        $conn->commit();

        echo "<script>alert('🎉 Đăng ký thành công! Hãy đăng nhập.'); window.location='login.php';</script>";
        exit;
    } catch (Exception $e) {
        $conn->rollBack();
        echo "<script>alert('❌ Lỗi khi đăng ký: " . addslashes($e->getMessage()) . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Đăng ký tài khoản</title>
<style>
    * { box-sizing: border-box; font-family: 'Poppins', sans-serif; }
    body {
        background: #f5f7fa;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
    }
    .form-container {
        background: #fff;
        padding: 40px;
        border-radius: 12px;
        box-shadow: 0 0 20px rgba(0,0,0,0.1);
        width: 400px;
    }
    h2 { text-align: center; margin-bottom: 25px; color: #222; }
    label { display: block; margin-bottom: 6px; color: #333; font-size: 14px; }
    input {
        width: 100%;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 6px;
        margin-bottom: 15px;
    }
    button {
        width: 100%;
        padding: 12px;
        background: #0a0a23;
        color: white;
        border: none;
        border-radius: 6px;
        font-weight: 500;
        cursor: pointer;
    }
    button:hover { background: #1a1a3d; }
    .switch { text-align: center; margin-top: 15px; font-size: 14px; }
    .switch a { color: #0a0a23; text-decoration: none; font-weight: 500; }
</style>
</head>
<body>
    <div class="form-container">
        <h2>Đăng ký tài khoản</h2>
        <form method="POST">
            <label>Họ tên</label>
            <input name="name" placeholder="Nguyễn Văn A" required>
            <label>Email</label>
            <input type="email" name="email" placeholder="you@example.com" required>
            <label>Số điện thoại</label>
            <input name="phone" placeholder="0123456789" required>
            <label>CMND/CCCD</label>
            <input name="cmnd" placeholder="123456789" required>
            <label>Mật khẩu</label>
            <input type="password" name="password" placeholder="••••••••" required>
            <button type="submit">Đăng ký</button>
        </form>
        <div class="switch">
            <p>Đã có tài khoản? <a href="login.php">Đăng nhập</a></p>
        </div>
    </div>
</body>
</html>
