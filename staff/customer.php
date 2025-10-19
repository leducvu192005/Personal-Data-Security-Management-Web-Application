<?php
require_once "../includes/auth.php";
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
    <p>Bạn có thể thêm, chỉnh sửa, và xem thông tin khách hàng.</p>

    <form method="POST" action="">
        <input type="text" name="name" placeholder="Tên khách hàng" required>
        <input type="text" name="id_card" placeholder="CMND / CCCD" required>
        <input type="text" name="address" placeholder="Địa chỉ" required>
        <input type="text" name="phone" placeholder="Số điện thoại" required>
        <input type="text" name="email" placeholder="Email" required>
        <button type="submit">Lưu</button>
    </form>

    <?php
    require_once "../includes/functions.php";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (secureSaveCustomer($_POST['name'], $_POST['id_card'], $_POST['address'], $_POST['phone'], $_POST['email'])) {
            echo "<p style='color:green;'>✅ Thêm khách hàng thành công!</p>";
        } else {
            echo "<p style='color:red;'>❌ Lỗi khi lưu!</p>";
        }
    }
    ?>

    <a href="dashboard.php">⬅ Quay lại</a>
</body>
</html>
