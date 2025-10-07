<?php
require 'config.php'; // chứa $pdo

$stmt = $pdo->query("SELECT * FROM logs ORDER BY created_at DESC");

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "[" . $row['created_at'] . "] " . $row['user_action'] . " - " . $row['details'] . "<br>";
}
?>
