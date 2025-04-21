<?php
session_start();

if (!isset($_SESSION['login_user'])) {
    header("location: login.php");
    exit;
}

include('db.php');

// Handle form submission for inline editing
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'edit') {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $type = $_POST['type'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];

    if ($price < 0) {
        echo "<script>alert('Price cannot be negative.');</script>";
    } else {
        $query = "UPDATE beverages SET name = ?, type = ?, price = ?, quantity = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssdii", $name, $type, $price, $quantity, $id);

        if ($stmt->execute()) {
            echo "<script>alert('Beverage updated successfully');</script>";
            // Refresh the page to show updated data
            echo "<script>window.location.href = 'manage_beverages.php';</script>";
        } else {
            echo "<script>alert('Error updating record: " . addslashes($conn->error) . "');</script>";
        }
        $stmt->close();
    }
}

$sql = "SELECT id, name, image, type, price, quantity FROM beverages";
$result = $conn->query($sql);
$lowQuantityFound = false;

// Define all beverage types
$beverageTypes = [
    'Juice',
    'Alcohol',
    'Soft Drinks',
    'Energy Drinks',
    'Coffee',
    'Tea',
    'Water',
    'Soda',
    'Milk',
    'Smoothie',
    'Cocktail',
    'Beer',
    'Wine'
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Beverages</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            display: flex;
        }
        header {
            width: 250px;
            background-color: #2c3e50;
            height: 100vh;
            position: fixed;
            padding: 20px 0;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
        }
        header h1 {
            margin: 0;
            padding: 0 20px 20px;
            font-size: 24px;
            color: #ecf0f1;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-bottom: 1px solid #34495e;
        }
        header a {
            color: #ecf0f1;
            text-decoration: none;
            padding: 12px 20px;
            display: block;
            transition: background-color 0.3s ease;
            font-size: 16px;
        }
        header a:hover {
            background-color: #34495e;
        }
        .content {
            margin-left: 250px;
            width: calc(100% - 250px);
            padding: 20px;
        }
        .page-title {
            background-color: #3498db;
            color: white;
            padding: 20px 30px;
            margin-bottom: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .page-title h2 {
            margin: 0;
        }
        
        .table-container {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            padding: 30px;
            overflow-x: auto;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        table th, table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        table th {
            background-color: #f9f9f9;
            color: #2c3e50;
            font-weight: 600;
        }
        
        table tr:hover {
            background-color: #f5f5f5;
        }
        
        table img {
            max-height: 100px;
            border-radius: 4px;
        }
        
        .btn {
            display: inline-block;
            font-weight: 500;
            text-align: center;
            white-space: nowrap;
            vertical-align: middle;
            user-select: none;
            border: none;
            padding: 8px 16px;
            font-size: 14px;
            line-height: 1.5;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            margin-right: 5px;
        }
        
        .btn-edit {
            color: #fff;
            background-color: #3498db;
        }
        
        .btn-edit:hover {
            background-color: #2980b9;
        }
        
        .btn-delete {
            color: #fff;
            background-color: #e74c3c;
        }
        
        .btn-delete:hover {
            background-color: #c0392b;
        }
        
        .empty-message {
            text-align: center;
            padding: 30px;
            color: #7f8c8d;
            font-size: 18px;
        }
        
        .stock-warning {
            color: #e74c3c;
            font-weight: bold;
        }
        
        .normal-stock {
            color: #2ecc71;
        }
        
        .low-stock {
            color: #f39c12;
        }
        
        .no-stock {
            color: #e74c3c;
        }
        
        /* Edit form styles */
        .edit-form-row {
            display: none;
        }
        
        .edit-form-row.active {
            display: table-row;
        }
        
        .edit-form {
            background-color: #f9f9f9;
            padding: 15px;
        }
        
        .edit-form-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 15px;
        }
        
        .edit-form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }
        
        .edit-form-group input,
        .edit-form-group select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
            background-color: white;
        }
        
        .edit-form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }
        
        .edit-form-actions button {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        
        .save-btn {
            background-color: #2ecc71;
            color: white;
        }
        
        .cancel-btn {
            background-color: #e74c3c;
            color: white;
        }
    </style>
</head>
<body>
    <header>
        <h1>Admin Dashboard</h1>
        <a href="adminpage.php">Dashboard</a>
        <a href="add_beverage.php">Add Beverages</a>
        <a href="manage_beverages.php">Manage Beverages</a>
        <a href="view_users.php">View Users</a>
        <a href="admin_orders.php">View Orders</a>
        <a href="logout.php">Logout</a>
    </header>
    
    <div class="content">
        <div class="page-title">
            <h2>Manage Beverages</h2>
        </div>
        
        <div class="table-container">
            <?php if ($result->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Image</th>
                            <th>Type</th>
                            <th>Price (Rs )</th>
                            <th>Quantity</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
                            $id = $row['id'];
                            $quantity = $row['quantity'];
                            
                            if ($quantity < 0) {
                                $quantity = 0;
                            }
                            
                            if ($quantity == 0) {
                                $lowQuantityFound = true;
                                $stockClass = 'no-stock';
                            } elseif ($quantity <= 5) {
                                $lowQuantityFound = true;
                                $stockClass = 'low-stock';
                            } else {
                                $stockClass = 'normal-stock';
                            }
                            
                            echo "<tr id='row-$id'>";
                            echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                            $imagePath = file_exists('img/' . $row['image']) ? 'img/' . $row['image'] : 'img/default_beverage.jpg';
                            echo "<td><img src='" . $imagePath . "' alt='" . htmlspecialchars($row['name']) . "' style='max-height: 100px;'></td>";
                            echo "<td>" . htmlspecialchars($row['type']) . "</td>";
                            echo "<td>Rs " . number_format($row['price'], 2) . "</td>";
                            echo "<td class='" . $stockClass . "'>" . $quantity . "</td>";
                            echo "<td>";
                            echo "<button class='btn btn-edit' onclick='toggleEditForm($id)'>Edit</button>";
                            echo "<a class='btn btn-delete' href='delete_beverage.php?id=$id' onclick='return confirm(\"Are you sure you want to delete this beverage?\");'>Delete</a>";
                            echo "</td>";
                            echo "</tr>";
                            
                            // Edit form row
                            echo "<tr id='edit-form-$id' class='edit-form-row'>";
                            echo "<td colspan='6'>";
                            echo "<div class='edit-form'>";
                            echo "<form method='post' onsubmit='return validateEditForm(this)'>";
                            echo "<input type='hidden' name='action' value='edit'>";
                            echo "<input type='hidden' name='id' value='$id'>";
                            
                            echo "<div class='edit-form-grid'>";
                            echo "<div class='edit-form-group'>";
                            echo "<label>Name</label>";
                            echo "<input type='text' name='name' value='" . htmlspecialchars($row['name']) . "' required>";
                            echo "</div>";
                            
                            echo "<div class='edit-form-group'>";
                            echo "<label>Type</label>";
                            echo "<select name='type' class='form-control' required>";
                            foreach ($beverageTypes as $type) {
                                $selected = ($row['type'] == $type) ? 'selected' : '';
                                echo "<option value='" . htmlspecialchars($type) . "' $selected>" . htmlspecialchars($type) . "</option>";
                            }
                            echo "</select>";
                            echo "</div>";
                            
                            echo "<div class='edit-form-group'>";
                            echo "<label>Price (Rs )</label>";
                            echo "<input type='number' step='0.01' name='price' value='" . htmlspecialchars($row['price']) . "' required>";
                            echo "</div>";
                            
                            echo "<div class='edit-form-group'>";
                            echo "<label>Quantity</label>";
                            echo "<input type='number' name='quantity' value='" . htmlspecialchars($row['quantity']) . "' required>";
                            echo "</div>";
                            echo "</div>";
                            
                            echo "<div class='edit-form-actions'>";
                            echo "<button type='button' class='cancel-btn' onclick='toggleEditForm($id)'>Cancel</button>";
                            echo "<button type='submit' class='save-btn'>Save Changes</button>";
                            echo "</div>";
                            
                            echo "</form>";
                            echo "</div>";
                            echo "</td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty-message">
                    <p>No beverages found in inventory. <a href="add_beverage.php">Add some beverages</a> to get started.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <?php if ($lowQuantityFound): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                alert('Warning: Some beverages are low on stock or out of stock.');
            });
        </script>
    <?php endif; ?>
    
    <script>
    function toggleEditForm(id) {
        // Hide all other edit forms
        document.querySelectorAll('.edit-form-row').forEach(row => {
            row.classList.remove('active');
        });
        
        // Toggle the selected form
        const formRow = document.getElementById('edit-form-' + id);
        formRow.classList.toggle('active');
        
        // Scroll to the form if it's being shown
        if (formRow.classList.contains('active')) {
            formRow.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    }
    
    function validateEditForm(form) {
        const price = parseFloat(form.elements['price'].value);
        if (price < 0) {
            alert('Price cannot be negative');
            return false;
        }
        
        const quantity = parseInt(form.elements['quantity'].value);
        if (quantity < 0) {
            alert('Quantity cannot be negative');
            return false;
        }
        
        return true;
    }
    </script>
</body>
</html>