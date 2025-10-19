<?php
session_start();
require_once '../config/db.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php'; // để dùng $default_admin nếu có

// 🧩 Tài khoản admin mặc định
$default_admin = [
    'email' => 'admin@gmail.com',
    'password' => 'ldv192005', // mật khẩu thuần, không hash
    'username' => 'admin',
    'role' => 'admin'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $error = '';

    // ✅ Trường hợp admin mặc định
    if ($email === $default_admin['email'] && $password === $default_admin['password']) {
        $_SESSION['user_id'] = 0; // không cần DB
        $_SESSION['username'] = $default_admin['username'];
        $_SESSION['role'] = $default_admin['role'];
        $_SESSION['default_admin'] = true;

        addLog(0, 'LOGIN', "Admin mặc định đăng nhập");

        header("Location: ../admin/index.php");
        exit;
    }

    // 🔍 Kiểm tra người dùng trong DB
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // ✅ Dùng password_verify để so sánh
    if ($user && password_verify($password, $user['password'])) {
        // Lưu thông tin session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        addLog($user['id'], 'LOGIN', "Người dùng đăng nhập");

        // Điều hướng theo vai trò
        switch ($user['role']) {
            case 'admin':
                header("Location: ../admin/index.php");
                break;
            case 'staff':
                header("Location: ../staff/index.php");
                break;
            case 'customer':
                header("Location: ../customer/index.php");
                break;
            default:
                session_destroy();
                header("Location: login.php?error=role_invalid");
                break;
        }
        exit;
    } else {
        $error = "Email hoặc mật khẩu không đúng!";
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Đăng nhập</title>
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
        width: 350px;
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
    .error { color: red; text-align: center; margin-bottom: 10px; }
    .switch { text-align: center; margin-top: 15px; font-size: 14px; }
    .switch a { color: #0a0a23; text-decoration: none; font-weight: 500; }
</style>
</head>
<body>
    <div class="form-container">
        <h2>Đăng nhập</h2>
        <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>
        <form method="POST">
            <label>Email</label>
            <input type="email" name="email" placeholder="you@example.com" required>
            <label>Mật khẩu</label>
            <input type="password" name="password" placeholder="••••••••" required>
            <button type="submit">Đăng nhập</button>
        </form>
        <div class="switch">
            <p>Chưa có tài khoản? <a href="register.php">Đăng ký ngay</a></p>
        </div>
    </div>
</body>
</html>
