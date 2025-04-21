<?php
include('db.php');

$id = $_GET['id'];
$sql = "DELETE FROM beverages WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo "Beverage deleted successfully";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$stmt->close();
header("Location: manage_beverages.php");
?>
