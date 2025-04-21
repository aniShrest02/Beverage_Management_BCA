<?php
include('db.php');

$sql = "SELECT name, quantity FROM beverages WHERE quantity < 10";
$result = $conn->query($sql);

echo "<h2>Low Stock Beverages</h2>";
while($row = $result->fetch_assoc()) {
    echo $row['name'] . " - " . $row['quantity'] . "<br>";
}
?>
