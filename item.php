<?php
include('db.php');
$sql = "SELECT id, name, type, price, quantity FROM beverages";
$result = $conn->query($sql);
?>

<table border="1">
    <tr>
        <th>Name</th>
        <th>Type</th>
        <th>Price</th>
        <th>Quantity</th>
        <th>Actions</th>
    </tr>
    <?php while($row = $result->fetch_assoc()): ?>
    <tr>
        <td><?php echo $row['name']; ?></td>
        <td><?php echo $row['type']; ?></td>
        <td><?php echo $row['price']; ?></td>
        <td><?php echo $row['quantity']; ?></td>
        <td>
            <a href="edit_beverage.php?id=<?php echo $row['id']; ?>">Edit</a>
            <a href="delete_beverage.php?id=<?php echo $row['id']; ?>">Delete</a>
        </td>
    </tr>
    <?php endwhile; ?>
</table>
