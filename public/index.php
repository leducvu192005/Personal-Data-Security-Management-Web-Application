<?php
require_once '../config/db.php';
require_once '../includes/functions.php';
require_once '../includes/csrf.php';

generate_csrf_token();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $token = $_POST['csrf_token'];

    if (!verify_csrf_token($token)) {
        die("CSRF token không hợp lệ!");
    }

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user'] = $user;
            addLog("Đăng nhập thành công");
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Sai mật khẩu!";
        }
    } else {
        $error = "Tài khoản không tồn tại!";
    }
}
?>
<!DOCTYPE html>
<html>
<head><title>Đăng nhập</title></head>
<body>
<h2>Đăng nhập hệ thống</h2>
<form method="POST">
    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
    <label>Tên đăng nhập:</label>
    <input type="text" name="username" required><br>
    <label>Mật khẩu:</label>
    <input type="password" name="password" required><br>
    <button type="submit">Đăng nhập</button>
</form>
<p style="color:red;"><?php echo $error ?? ''; ?></p>
</body>
</html>
