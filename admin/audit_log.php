<?php
require_once '../includes/functions.php';
require_once '../includes/activity_logger.php';

$stmt = $conn->query("
    SELECT a.*, u.username 
    FROM audit_logs a
    LEFT JOIN users u ON a.user_id = u.id
    ORDER BY a.created_at DESC
");
$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<h2>📋 Nhật ký hoạt động hệ thống</h2>
<a href="index.php">⬅️ Quay lại</a>

<table border="1" cellpadding="8" cellspacing="0" style="border-collapse: collapse; width:100%;">
<tr style="background: #f2f2f2;">
    <th>Người thực hiện</th>
    <th>Hành động</th>
    <th>Chi tiết</th>
    <th>Địa chỉ IP</th>
    <th>Thời gian</th>
</tr>

<?php foreach ($logs as $l): ?>
    <?php
        // Giải mã dữ liệu JSON
        $masked = json_decode($l['masked_data'], true);
        $detail = $masked['DETAIL'] ?? '(Không có chi tiết)';
        $uri = $masked['URI'] ?? '';
    ?>
    <tr>
        <td><?= htmlspecialchars($l['username'] ?? 'Hệ thống') ?></td>
        <td><?= htmlspecialchars($l['action']) ?></td>
        <td>
            <?= htmlspecialchars($detail) ?><br>
            <small style="color: gray;">Trang: <?= htmlspecialchars($uri) ?></small>
        </td>
        <td><?= htmlspecialchars($l['ip_address']) ?></td>
        <td><?= htmlspecialchars($l['created_at']) ?></td>
    </tr>
<?php endforeach; ?>
</table>
