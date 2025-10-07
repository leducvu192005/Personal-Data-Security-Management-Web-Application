<?php
require 'functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name     = $_POST['name'];
    $email    = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Mã hóa dữ liệu nhạy cảm
    $iv = "";
    $phone_enc = encryptData($_POST['phone'], $iv);
    $cmnd_enc  = encryptData($_POST['cmnd'], $iv);

    // Lưu vào DB
    $stmt = $pdo->prepare("INSERT INTO customers(name,email,phone,cmnd,password,iv) VALUES (?,?,?,?,?,?)");
    $stmt->execute([$name, $email, $phone_enc, $cmnd_enc, $password, $iv]);

    addLog("CREATE", "Thêm khách hàng: $email");

    echo "✅ Thêm thành công!";
    addLog("CREATE", "Thêm khách hàng: $email");
}
?>

<form method="POST">
    Họ tên: <input name="name"><br>
    Email: <input name="email"><br>
    SĐT: <input name="phone"><br>
    CMND: <input name="cmnd"><br>
    Mật khẩu: <input type="password" name="password"><br>
    <button type="submit">Lưu</button>
</form>
