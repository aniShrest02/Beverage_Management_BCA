<?php
include('db.php');

$search = $_GET['search'] ?? '';
$sql = "SELECT id, name, type, price, quantity FROM beverages WHERE name LIKE ?";
$stmt = $conn->prepare($sql);
$search = "%$search%";
$stmt->bind_param("s", $search);
$stmt->execute();
$result = $stmt->get_result();
?>
