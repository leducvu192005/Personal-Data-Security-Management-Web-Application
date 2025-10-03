<?php
$host = "localhost";
$dbname = "secure_app";
$username = "root";
$password = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Could not connect to the database $dbname :" . $e->getMessage());
}

// Key cho AES
define("CIPHER_METHOD", "AES-256-CBC");
define("ENCRYPTION_KEY", "mysecretkey1234567890"); 
?>
