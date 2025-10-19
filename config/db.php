<?php


$host = 'localhost';
$db   = 'bank_app';
$user = 'root';
$pass = ''; 
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $conn = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die("❌ Lỗi kết nối CSDL: " . $e->getMessage());
}
$default_admin = [
    'email' => 'admin@gmail.com',
    'password' => 'ldv192005',
    'username' => 'admin',
    'role' => 'admin',
];
?>
