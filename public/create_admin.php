<?php
require_once "../config/db.php";

try {
    $email = 'admin@gmail.com';
    $password = password_hash('ldv192005', PASSWORD_BCRYPT);
    $username = 'admin';
    $role = 'admin';

    // Kiểm tra xem đã có admin chưa
    $check = $conn->prepare("SELECT * FROM users WHERE role = 'admin'");
    $check->execute();

    if ($check->rowCount() > 0) {
        echo "⚠️ Tài khoản admin đã tồn tại!";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->execute([$username, $email, $password, $role]);
        echo "✅ Tạo tài khoản admin thành công!";
    }

} catch (PDOException $e) {
    echo "❌ Lỗi: " . $e->getMessage();
}
?>
