<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/encryption.php';
require_once __DIR__ . '/../includes/activity_logger.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header('Location: ../login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// ✅ Lấy thông tin hiện tại của người dùng
$stmt = $conn->prepare("
    SELECT u.username, u.email, c.phone, c.cmnd
    FROM users u
    LEFT JOIN customers c ON u.id = c.user_id
    WHERE u.id = ?
");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// ✅ Giải mã dữ liệu nhạy cảm (nếu có)
$decrypted_phone = $user['phone'] ? decryptData($user['phone']) : '';
$decrypted_cmnd  = $user['cmnd']  ? decryptData($user['cmnd'])  : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $phone    = trim($_POST['phone']);
    $cmnd     = trim($_POST['cmnd']);

    if ($username && $email && $phone && $cmnd) {
        // ✅ Mã hóa dữ liệu nhạy cảm
        $enc_phone = encryptData($phone);
        $enc_cmnd  = encryptData($cmnd);

        try {
            $conn->beginTransaction();

            // ✅ Cập nhật bảng users
            $stmt = $conn->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
            $stmt->execute([$username, $email, $user_id]);

            // ✅ Cập nhật bảng customers
            $stmt = $conn->prepare("UPDATE customers SET phone = ?, cmnd = ? WHERE user_id = ?");
            $stmt->execute([$enc_phone, $enc_cmnd, $user_id]);

            $conn->commit();

            // ✅ Ghi log an toàn (ẩn dữ liệu nhạy cảm)
          // ✅ Ghi log gọn gàng, không lưu dữ liệu chi tiết
logActivity(
    'Cập nhật thông tin cá nhân',
    [
        'message' => "Khách hàng {$username} (ID {$user_id}) đã cập nhật hồ sơ cá nhân."
    ]
);


            header("Location: index.php?success=1");
            exit;
        } catch (Exception $e) {
            $conn->rollBack();
            logActivity('Lỗi cập nhật thông tin', [
                'error' => $e->getMessage()
            ]);
            echo "<script>alert('Lỗi khi cập nhật: " . addslashes($e->getMessage()) . "');</script>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Cập nhật thông tin cá nhân</title>
    <link rel="stylesheet" href="../../assets/style.css">
    <style>
        body { font-family: Arial, sans-serif; background: #f3f3f3; padding: 20px; }
        .container {
            background: #fff; padding: 25px; border-radius: 10px;
            width: 420px; margin: 50px auto;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h2 { text-align: center; margin-bottom: 20px; }
        form { display: flex; flex-direction: column; }
        label { margin-top: 10px; font-weight: bold; }
        input { padding: 10px; margin-top: 5px; border-radius: 5px; border: 1px solid #ccc; }
        .btn { background: #007bff; color: #fff; padding: 10px; border: none; border-radius: 5px; cursor: pointer; margin-top: 15px; }
        .btn:hover { background: #0056b3; }
        a { text-decoration: none; color: #007bff; text-align: center; display: block; margin-top: 10px; }
    </style>
</head>
<body>
<div class="container">
    <h2>Cập nhật thông tin cá nhân</h2>
    <form method="POST">
        <label>Username:</label>
        <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>

        <label>Email:</label>
        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>

        <label>Số điện thoại:</label>
        <input type="text" name="phone" value="<?= htmlspecialchars($decrypted_phone) ?>" required>

        <label>CCCD/CMND:</label>
        <input type="text" name="cmnd" value="<?= htmlspecialchars($decrypted_cmnd) ?>" required>

        <button type="submit" class="btn">💾 Lưu thay đổi</button>
    </form>
    <a href="index.php">⬅️ Quay lại</a>
</div>
</body>
</html>
