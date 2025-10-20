<?php
session_start();
require_once "../config/db.php";
require_once "../includes/auth.php";
require_once "../includes/functions.php";
require_once "../includes/encryption.php";
require_once "../includes/activity_logger.php"; // ✅ Thêm để ghi log

ensureLoggedIn();
checkRole('staff');

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) die("❌ ID khách hàng không hợp lệ!");

// ✅ Lấy thông tin nhân viên từ session
$staff_id = $_SESSION['user_id'] ?? 0;
$username = $_SESSION['username'] ?? 'unknown';

// ✅ Lấy thông tin khách hàng
$stmt = $conn->prepare("SELECT * FROM customers WHERE id = ?");
$stmt->execute([$id]);
$customer = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$customer) {
    die("❌ Không tìm thấy khách hàng!");
}

// ✅ Giải mã dữ liệu nhạy cảm trước khi hiển thị
$customer['phone'] = decryptData($customer['phone']);
$customer['cmnd'] = decryptData($customer['cmnd']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $cmnd = trim($_POST['cmnd']);

    if ($name === '' || $email === '' || $phone === '' || $cmnd === '') {
        echo "<script>alert('⚠️ Vui lòng nhập đầy đủ thông tin!'); history.back();</script>";
        exit;
    }

    try {
        // ✅ Cập nhật dữ liệu
        $stmt = $conn->prepare("
            UPDATE customers
            SET name=?, email=?, phone=?, cmnd=?
            WHERE id=?
        ");
        $stmt->execute([
            $name,
            $email,
            encryptData($phone),
            encryptData($cmnd),
            $id
        ]);

        // ✅ Ghi log (ẩn dữ liệu nhạy cảm)
        logActivity(
            'CẬP_NHẬT_KHÁCH_HÀNG',
            [
                'message' => "Nhân viên {$username} (ID {$staff_id}) đã cập nhật khách hàng ID {$id}",
                'customer_id' => $id,
                'fields_updated' => ['name', 'email', 'phone', 'cmnd']
            ]
        );

        echo "<script>alert('✅ Cập nhật thành công!'); window.location.href='index.php';</script>";
        exit;
    } catch (PDOException $e) {
        // ✅ Ghi log lỗi
        logActivity(
            'LỖI_CẬP_NHẬT_KHÁCH_HÀNG',
            [
                'message' => "Nhân viên {$username} (ID {$staff_id}) gặp lỗi khi cập nhật khách hàng ID {$id}",
                'error' => $e->getMessage()
            ]
        );

        echo "<script>alert('❌ Lỗi khi cập nhật: " . addslashes($e->getMessage()) . "');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>📝 Cập nhật khách hàng</title>
<style>
    body {
        font-family: 'Poppins', sans-serif;
        background: #f4f6f8;
        padding-top: 40px;
        text-align: center;
    }
    form {
        background: white;
        padding: 25px 40px;
        display: inline-block;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        width: 320px;
        text-align: left;
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
    button:hover { background: #0056b3; }
    a {
        display: inline-block;
        margin-top: 10px;
        color: #555;
        text-decoration: none;
    }
    a:hover { text-decoration: underline; }
</style>
</head>
<body>
    <h2>🧾 Cập nhật thông tin khách hàng</h2>
    <form method="POST">
        <label>Tên khách hàng:</label>
        <input type="text" name="name" value="<?= htmlspecialchars($customer['name']) ?>" required>

        <label>Email:</label>
        <input type="email" name="email" value="<?= htmlspecialchars($customer['email']) ?>" required>

        <label>Số điện thoại:</label>
        <input type="text" name="phone" value="<?= htmlspecialchars($customer['phone']) ?>" pattern="[0-9]{10,11}" title="Nhập 10-11 số" required>

        <label>CMND/CCCD:</label>
        <input type="text" name="cmnd" value="<?= htmlspecialchars($customer['cmnd']) ?>" pattern="[0-9]{9,12}" title="Nhập 9-12 số" required>

        <button type="submit">💾 Lưu thay đổi</button><br>
        <a href="index.php">⬅ Quay lại danh sách</a>
    </form>
</body>
</html>
