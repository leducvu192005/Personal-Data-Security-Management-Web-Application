<?php
require 'functions.php';

$result = $conn->query("SELECT * FROM customers");

while ($row = $result->fetch_assoc()) {
    echo "ID: " . $row['id'] . "<br>";
    echo "Tên: " . $row['name'] . "<br>";
    echo "Email: " . $row['email'] . "<br>";

    $phone = decryptData($row['phone'], $row['iv']);
    $cmnd  = decryptData($row['cmnd'], $row['iv']);

    echo "SĐT: " . $phone . "<br>";
    echo "CMND: " . $cmnd . "<br><hr>";
    addLog("READ", "Xem thông tin khách hàng ID: " . $row['id']);
}
?>
