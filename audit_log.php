<?php
require 'functions.php';

$result = $conn->query("SELECT * FROM audit_log ORDER BY created_at DESC");

while ($row = $result->fetch_assoc()) {
    echo "[" . $row['created_at'] . "] " . $row['action'] . " - " . $row['details'] . "<br>";
}
?>
