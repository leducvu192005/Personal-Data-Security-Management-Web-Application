<?php
require 'functions.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("DELETE FROM customers WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    addLog("DELETE", "Xóa khách hàng ID: $id");
    echo "Xóa thành công!";
    addLog("DELETE", "Xóa khách hàng ID: $id");
}
?>
