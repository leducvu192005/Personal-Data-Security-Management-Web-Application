<?php
session_start();
require_once '../config/db.php';
require_once '../includes/functions.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    die("❌ Không tìm thấy ID nhân viên để cập nhật.");
}

$stmt = $conn->prepare("SELECT u.username, s.name, s.department 
                       FROM users u 
                       JOIN staff_info s ON u.id = s.user_id 
                       WHERE u.id = ?");
$stmt->execute([$id]);
$staff = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$staff) {
    die("❌ Không tìm thấy thông tin nhân viên có ID = $id");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $name = $_POST['name'];
    $department = $_POST['department'];

    $pdo->beginTransaction();
    try {
        $stmt = $conn->prepare("UPDATE users SET username = ? WHERE id = ?");
        $stmt->execute([$username, $id]);

        $stmt = $conn->prepare("UPDATE staff_info SET name = ?, department = ? WHERE user_id = ?");
        $stmt->execute([$name, $department, $id]);

        addLog($_SESSION['user_id'], 'UPDATE_STAFF', "Cập nhật nhân viên: $username");
        $conn->commit();

        header("Location: staff_list.php");
        exit;
    } catch (Exception $e) {
        $conn->rollBack();
        echo "❌ Lỗi: " . htmlspecialchars($e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Cập nhật nhân viên</title>
<style>
body {
    background: #f4f6f8;
    font-family: 'Poppins', sans-serif;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}
.container {
    background: #fff;
    padding: 40px;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    width: 380px;
}
h2 {
    text-align: center;
    margin-bottom: 25px;
    color: #333;
}
input {
    width: 100%;
    padding: 10px;
    margin-bottom: 15px;
    border-radius: 6px;
    border: 1px solid #ccc;
}
button {
    width: 100%;
    padding: 12px;
    background: #007bff;
    border: none;
    color: white;
    border-radius: 6px;
    font-size: 15px;
    cursor: pointer;
}
button:hover { background: #0056b3; }
.back-btn {
    display: inline-block;
    background: #6c757d;
    color: white;
    padding: 8px 14px;
    border-radius: 6px;
    text-decoration: none;
    margin-bottom: 10px;
}
.back-btn:hover { background: #5a6268; }
</style>
</head>
<body>
<div class="container">
    <a href="index.php" class="back-btn">← Quay lại trang quản trị</a>
    <h2>Cập nhật thông tin nhân viên</h2>
    <form method="POST">
        <input type="text" name="username" placeholder="Tên đăng nhập" value="<?= htmlspecialchars($staff['username']) ?>" required>
        <input type="text" name="name" placeholder="Họ tên" value="<?= htmlspecialchars($staff['name']) ?>" required>
        <input type="text" name="department" placeholder="Phòng ban" value="<?= htmlspecialchars($staff['department']) ?>">
        <button type="submit">Lưu</button>
    </form>
</div>
</body>
</html>
