<?php
// includes/activity_logger.php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/functions.php';

/**
 * Ẩn dữ liệu nhạy cảm trong log (ví dụ: email, số điện thoại, CCCD, password...)
 */
function maskSensitiveData($data) {
    if (!is_array($data)) return $data;

    $masked = [];
    foreach ($data as $key => $value) {
        $keyLower = strtolower($key);

        // Nếu là mảng con -> đệ quy xử lý tiếp
        if (is_array($value)) {
            $masked[$key] = maskSensitiveData($value);
            continue;
        }

        // Nếu là trường nhạy cảm thì ẩn bớt
        if (in_array($keyLower, ['password', 'pass', 'cmnd', 'cccd', 'id_card', 'email', 'phone'])) {
            $masked[$key] = str_repeat('*', max(0, strlen($value) - 3)) . substr($value, -3);
        } else {
            $masked[$key] = $value;
        }
    }
    return $masked;
}

/**
 * Ghi log hoạt động (an toàn, không lộ dữ liệu gốc)
 * 
 * @param string $action        Hành động (VD: 'Xem thông tin cá nhân')
 * @param string|array $detail  Mô tả chi tiết (VD: 'Khách hàng A cập nhật thông tin cá nhân')
 */
function logActivity($action, $detail = '') {
    global $conn;

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $user_id = $_SESSION['user_id'] ?? null;
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $uri = $_SERVER['REQUEST_URI'] ?? '';

    // Chỉ lưu nội dung cần thiết, tránh lưu toàn bộ $_POST hoặc $_GET
    $snapshot = [
        'DETAIL' => (is_array($detail) ? json_encode($detail, JSON_UNESCAPED_UNICODE) : $detail),
        'URI' => $uri
    ];

    // Dữ liệu che giấu
    $masked = maskSensitiveData($snapshot);

    try {
        $stmt = $conn->prepare("
            INSERT INTO audit_logs (user_id, action, data_snapshot, masked_data, ip_address, created_at)
            VALUES (?, ?, ?, ?, ?, NOW())
        ");
        $stmt->execute([
            $user_id,
            $action,
            json_encode($snapshot, JSON_UNESCAPED_UNICODE),
            json_encode($masked, JSON_UNESCAPED_UNICODE),
            $ip
        ]);
    } catch (Exception $e) {
        error_log('Không thể ghi log: ' . $e->getMessage());
    }
}
?>
