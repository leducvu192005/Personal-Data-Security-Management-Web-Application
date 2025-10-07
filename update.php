<?php
require 'functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id    = $_POST['id'];
    $name  = $_POST['name'];
    $email = $_POST['email'];

    $iv = "";
    $phone_enc = encryptData($_POST['phone'], $iv);
    $cmnd_enc  = encryptData($_POST['cmnd'], $iv);

    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;

    if ($password) {
        $stmt = $conn->prepare("UPDATE customers SET name=?, email=?, phone=?, cmnd=?, password=?, iv=? WHERE id=?");
        $stmt->bind_param("ssssssi", $name, $email, $phone_enc, $cmnd_enc, $password, $iv, $id);
    } else {
        $stmt = $conn->prepare("UPDATE customers SET name=?, email=?, phone=?, cmnd=?, iv=? WHERE id=?");
        $stmt->bind_param("sssssi", $name, $email, $phone_enc, $cmnd_enc, $iv, $id);
    }

    $stmt->execute();
    addLog("UPDATE", "Sửa khách hàng ID: $id");
    echo "Cập nhật thành công!";
    addLog("UPDATE", "Sửa khách hàng ID: $id");
}

?>
