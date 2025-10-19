<?php
require_once "../includes/auth.php";
require_once "../includes/functions.php";

ensureLoggedIn();
checkRole('staff');
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Nhân viên</title>
</head>
<body>
    <h2>👩‍💻 Khu vực Nhân viên</h2>
    <p>Bạn có thể thêm khách hàng mới vào hệ thống (dữ liệu nhạy cảm được mã hóa).</p>

    <form method="POST">
        <input type="text" name="name" placeholder="Tên khách hàng" required><br>
        <input type="email" name="email" placeholder="Email" required><br>
        <input type="text" name="phone" placeholder="Số điện thoại" required><br>
        <input type="text" name="cmnd" placeholder="CMND / CCCD" required><br>
        <input type="password" name="password" placeholder="Mật khẩu khách hàng" required><br>
        <button type="submit">Thêm khách hàng</button>
    </form>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $ok = secureSaveCustomer(
            $_POST['name'],
            $_POST['email'],
            $_POST['phone'],
            $_POST['cmnd'],
            $_POST['password']
        );

        if ($ok) {
            echo "<p style='color:green;'>✅ Thêm khách hàng thành công!</p>";
        } else {
            echo "<p style='color:red;'>❌ Có lỗi xảy ra khi thêm!</p>";
        }
    }
    ?>

    <br><a href="dashboard.php">⬅ Quay lại</a>
</body>
</html>

