<?php
require_once "../includes/auth.php";
require_once "../includes/functions.php";
require_once "../config/db.php";

ensureLoggedIn();
checkRole('staff');
?>

<h2>🔍 Tìm kiếm khách hàng</h2>

<form method="GET">
    <input type="text" name="keyword" placeholder="Nhập tên, số CMND/CCCD hoặc SĐT" required>
    <button type="submit">Tìm</button>
</form>

<?php
if (isset($_GET['keyword'])) {
    $keyword = "%" . trim($_GET['keyword']) . "%";

    // ✅ Dùng PDO (không dùng bind_param / get_result)
    $stmt = $conn->prepare("
        SELECT * FROM customers 
        WHERE name LIKE ? OR id_card LIKE ? OR phone LIKE ?
        ORDER BY created_at DESC
    ");
    $stmt->execute([$keyword, $keyword, $keyword]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<h3>Kết quả:</h3>";

    if (count($results) > 0) {
        echo "<table border='1' cellpadding='6'>";
        echo "<tr>
                <th>ID</th>
                <th>Tên</th>
                <th>CMND/CCCD</th>
                <th>Địa chỉ</th>
                <th>SĐT</th>
                <th>Email</th>
                <th>Ngày tạo</th>
                <th>Hành động</th>
              </tr>";

        foreach ($results as $row) {
            // ✅ Giải mã dữ liệu nếu được mã hóa
            $cmnd = isset($row['id_card']) ? decryptData($row['id_card']) : '';
            $phone = isset($row['phone']) ? decryptData($row['phone']) : '';

            echo "<tr>
                    <td>{$row['id']}</td>
                    <td>" . htmlspecialchars($row['name']) . "</td>
                    <td>" . htmlspecialchars($cmnd) . "</td>
                    <td>" . htmlspecialchars($row['address'] ?? '') . "</td>
                    <td>" . htmlspecialchars($phone) . "</td>
                    <td>" . htmlspecialchars($row['email']) . "</td>
                    <td>" . htmlspecialchars($row['created_at']) . "</td>
                    <td>
                        <a href='update_customer.php?id={$row['id']}'>✏️ Sửa</a> |
                        <a href='delete_customer.php?id={$row['id']}' onclick=\"return confirm('Xóa khách hàng này?');\">🗑 Xóa</a>
                    </td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "<p>Không tìm thấy khách hàng nào.</p>";
    }
}
?>

<a href="index.php">⬅ Quay lại</a>
