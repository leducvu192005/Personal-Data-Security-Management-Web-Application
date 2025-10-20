<?php
require_once "../includes/auth.php";
require_once "../includes/functions.php";
require_once "../config/db.php"; // thêm dòng này nếu chưa có $conn
ensureLoggedIn();
checkRole('staff');

// ✅ Sửa lại phần lấy id nhân viên
$staff_id = $_SESSION['user_id'];
$search = $_GET['search'] ?? '';

// ✅ Truy vấn danh sách khách hàng (không phụ thuộc staff_id)
$stmt = $conn->prepare("
    SELECT * FROM customers
    WHERE name LIKE ? OR email LIKE ? OR phone LIKE ?
    ORDER BY created_at DESC
");
$like = "%$search%";
$stmt->execute([$like, $like, $like]);
$customers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Nhân viên - Quản lý khách hàng</title>
<style>
    body { font-family: 'Poppins', sans-serif; background: #f4f6f8; margin: 0; padding: 20px; }
    h2 { text-align: center; color: #333; }
    .toolbar { text-align: center; margin-bottom: 20px; }
    .toolbar input { padding: 8px; width: 200px; }
    .toolbar a, .toolbar button {
        padding: 8px 12px;
        background: #007bff;
        color: #fff;
        border-radius: 6px;
        text-decoration: none;
        border: none;
        cursor: pointer;
        margin-left: 5px;
    }
    table { width: 100%; border-collapse: collapse; background: white; }
    th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
    th { background: #007bff; color: white; }
    tr:hover { background: #f1f1f1; }
</style>
</head>
<body>
    <h2>👩‍💻 Quản lý khách hàng</h2>

    <div class="toolbar">
        <form method="GET" style="display:inline;">
            <input type="text" name="search" placeholder="Tìm kiếm khách hàng..." value="<?= htmlspecialchars($search) ?>">
            <button type="submit">Tìm kiếm</button>
        </form>
        <a href="create_customer.php">+ Thêm khách hàng</a>
        <a href="../public/logout.php" style="background:#dc3545;">Đăng xuất</a>
    </div>

    <table>
        <tr>
            <th>ID</th>
            <th>Tên</th>
            <th>Email</th>
            <th>SĐT</th>
            <th>CMND</th>
            <th>Ngày tạo</th>
            <th>Hành động</th>
        </tr>
        <?php foreach ($customers as $c): ?>
        <tr>
            <td><?= $c['id'] ?></td>
            <td><?= htmlspecialchars($c['name']) ?></td>
            <td><?= htmlspecialchars($c['email']) ?></td>
            <td><?= htmlspecialchars($c['phone']) ?></td>
            <td><?= maskData(decryptData($c['cmnd'])) ?></td>
            <td><?= htmlspecialchars($c['created_at']) ?></td>
            <td>
                <a href="update_customer.php?id=<?= $c['id'] ?>">Sửa</a> |
                <a href="delete_customer.php?id=<?= $c['id'] ?>" onclick="return confirm('Xóa khách hàng này?')">Xóa</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
