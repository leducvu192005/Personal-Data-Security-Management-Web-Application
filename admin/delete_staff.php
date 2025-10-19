<?php
session_start();
require_once '../../includes/functions.php';

$id = $_GET['id'];

$pdo->beginTransaction();
try {
    $pdo->prepare("DELETE FROM staff_info WHERE user_id = ?")->execute([$id]);
    $pdo->prepare("DELETE FROM users WHERE id = ? AND role = 'staff'")->execute([$id]);

    addLog($_SESSION['user_id'], 'DELETE_STAFF', "Xóa nhân viên ID=$id");
    $pdo->commit();
    header("Location: staff_list.php");
} catch (Exception $e) {
    $pdo->rollBack();
    echo "Lỗi khi xóa: " . $e->getMessage();
}
