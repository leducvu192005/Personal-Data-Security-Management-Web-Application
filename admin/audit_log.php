<?php
require_once '../includes/functions.php';
$stmt = $conn->query("SELECT a.*, u.username FROM audit_logs a 
                     JOIN users u ON a.user_id = u.id ORDER BY a.created_at DESC");
$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<h2>📋 Nhật ký hoạt động hệ thống</h2>
<a href="index.php">Quay lại</a>
<table border="1" cellpadding="8">
<tr>
    <th>Người thực hiện</th>
    <th>Hành động</th>
    <th>Chi tiết</th>
    <th>Thời gian</th>
</tr>
<?php foreach ($logs as $l): ?>
<tr>
    <td><?= htmlspecialchars($l['username']) ?></td>
    <td><?= htmlspecialchars($l['action']) ?></td>
    <td><?= htmlspecialchars($l['masked_data']) ?></td>
    <td><?= $l['created_at'] ?></td>
</tr>
<?php endforeach; ?>
</table>
