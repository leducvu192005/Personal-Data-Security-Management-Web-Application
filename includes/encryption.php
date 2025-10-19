<?php
require_once __DIR__ . '/../config/config.php';

function encryptData($data) {
    $ivlen = openssl_cipher_iv_length(CIPHER_METHOD);
    $iv = openssl_random_pseudo_bytes($ivlen);
    $ciphertext = openssl_encrypt($data, CIPHER_METHOD, ENCRYPTION_KEY, 0, $iv);
    return base64_encode($iv . $ciphertext);
}

function decryptData($data) {
    $data = base64_decode($data);
    $ivlen = openssl_cipher_iv_length(CIPHER_METHOD);
    $iv = substr($data, 0, $ivlen);
    $ciphertext = substr($data, $ivlen);
    return openssl_decrypt($ciphertext, CIPHER_METHOD, ENCRYPTION_KEY, 0, $iv);
}
?>
