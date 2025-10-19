<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/encryption.php';

/**
 * Ghi log hoạt động của người dùng
 */
function addLog($user_id, $action, $detail) {
    global $conn;
    try {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $stmt = $conn->prepare("
            INSERT INTO audit_log (user_id, action, detail, ip_address, created_at)
            VALUES (?, ?, ?, ?, NOW())
        ");
        $stmt->execute([$user_id, $action, $detail, $ip]);
    } catch (Exception $e) {
        error_log("Không thể ghi log: " . $e->getMessage());
    }
}

/**
 * Ẩn dữ liệu nhạy cảm (chỉ giữ lại vài ký tự cuối)
 */
function maskData($data, $visible = 4) {
    if (!$data || strlen($data) <= $visible) return str_repeat('*', strlen($data));
    $len = strlen($data);
    return str_repeat('*', $len - $visible) . substr($data, -$visible);
}

/**
 * Thêm khách hàng an toàn (mã hóa dữ liệu nhạy cảm + ghi log)
 */
function secureSaveCustomer($name, $email, $phone, $cmnd, $password, $created_by) {
    global $conn;
    try {
        // Mã hóa dữ liệu nhạy cảm
        $enc_cmnd = encryptData(trim($cmnd));
        $enc_pass = encryptData(password_hash($password, PASSWORD_BCRYPT));

        // Thực thi câu lệnh thêm vào bảng customers
        $stmt = $conn->prepare("
            INSERT INTO customers (name, email, phone, cmnd, encrypted_password, created_by, created_at)
            VALUES (?, ?, ?, ?, ?, ?, NOW())
        ");
        $stmt->execute([$name, $email, $phone, $enc_cmnd, $enc_pass, $created_by]);

        // Ghi log hành động (ẩn email + CMND)
        $maskedEmail = maskData($email, 3);
        $maskedCMND = maskData($cmnd, 3);
        addLog($created_by, 'CREATE_CUSTOMER', "Thêm khách hàng: {$name} | Email: {$maskedEmail} | CMND: {$maskedCMND}");

        return true;
    } catch (Exception $e) {
        addLog($created_by, 'ERROR', "Lỗi thêm khách hàng: " . $e->getMessage());
        return false;
    }
}
function get_staff_list($admin_id, $search = '') {
    global $conn;

    try {
        if (!empty($search)) {
            $stmt = $conn->prepare("
                SELECT u.id, u.username, s.name, s.department, u.email, u.created_at 
                FROM users u
                JOIN staff_info s ON u.id = s.user_id
                WHERE u.role = 'staff' AND (s.name LIKE ? OR u.email LIKE ?)
                ORDER BY u.created_at DESC
            ");
            $like = "%{$search}%";
            $stmt->execute([$like, $like]);
        } else {
            $stmt = $conn->prepare("
                SELECT u.id, u.username, s.name, s.department, u.email, u.created_at
                FROM users u
                JOIN staff_info s ON u.id = s.user_id
                WHERE u.role = 'staff'
                ORDER BY u.created_at DESC
            ");
            $stmt->execute();
        }

        $staffs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Che email nếu muốn log an toàn
        foreach ($staffs as &$s) {
            $s['email'] = maskData($s['email'], 3);
        }

        // Ghi log hành động admin xem danh sách
        $detail = empty($search) 
            ? "Admin ID {$admin_id} xem danh sách toàn bộ nhân viên" 
            : "Admin ID {$admin_id} tìm kiếm nhân viên với từ khóa: {$search}";
        addLog($admin_id, 'VIEW_STAFF_LIST', $detail);

        return $staffs;
    } catch (Exception $e) {
        addLog($admin_id, 'ERROR', "Lỗi lấy danh sách nhân viên: " . $e->getMessage());
        return [];
    }
}
?>
