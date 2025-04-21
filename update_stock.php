<?php
include('db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $quantity = $_POST['quantity'];

    $sql = "UPDATE beverages SET quantity=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $quantity, $id);

    if ($stmt->execute()) {
        echo "Stock updated successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $stmt->close();
}
?>

<form method="post">
    Beverage ID: <input type="number" name="id" required>
    New Quantity: <input type="number" name="quantity" required>
    <button type="submit">Update Stock</button>
</form>
