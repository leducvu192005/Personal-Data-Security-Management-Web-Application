<?php
require 'config.php';

// Mã hóa
function encryptData($data, &$iv) {
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length(CIPHER_METHOD));
    return openssl_encrypt($data, CIPHER_METHOD, ENCRYPTION_KEY, 0, $iv);
}

// Giải mã
function decryptData($data, $iv) {
    return openssl_decrypt($data, CIPHER_METHOD, ENCRYPTION_KEY, 0, $iv);
}

// Audit log (dùng PDO)
function addLog($action, $details) {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO logs(action, details) VALUES (?, ?)");
    $stmt->execute([$action, $details]);
}
?>
