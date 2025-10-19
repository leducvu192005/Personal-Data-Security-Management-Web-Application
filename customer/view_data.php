<?php
// public/customer/view_data.php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/encryption.php'; // ✅ Thêm dòng này
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Lấy dữ liệu khách hàng theo user_id
$stmt = $conn->prepare("SELECT * FROM customers WHERE user_id = ? LIMIT 1");
$stmt->execute([$user_id]);
$customer = $stmt->fetch();

if ($customer) {
    // Giải mã dữ liệu
    $customer['phone_decrypted'] = decryptData($customer['phone']);
    $customer['cmnd_decrypted'] = decryptData($customer['cmnd']);
}

// Ghi log hành động
addLog($user_id, 'VIEW_OWN_DATA', 'Xem dữ liệu cá nhân');
?>

<?php include __DIR__ . '/../includes/header.php'; ?>

<div class="container" style="max-width: 600px; margin: 40px auto; background: #fff; padding: 25px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
    <h2 style="text-align: center; margin-bottom: 20px;">Dữ liệu cá nhân</h2>

    <?php if ($customer): ?>
        <p><strong>Tên:</strong> <?= htmlspecialchars($customer['name']) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($customer['email']) ?></p>
        <p><strong>Số điện thoại (đã giải mã):</strong> <?= htmlspecialchars($customer['phone_decrypted']) ?></p>
        <p><strong>CCCD (đã giải mã):</strong> <?= htmlspecialchars($customer['cmnd_decrypted']) ?></p>
    <?php else: ?>
        <p>Không tìm thấy dữ liệu cá nhân.</p>
    <?php endif; ?>

    <div style="margin-top: 20px; text-align: center;">
        <a href="index.php">⬅️ Quay lại</a>
    </div>
</div>

</body>
</html>
