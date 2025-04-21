<?php
session_start();
if(!isset($_SESSION['login_user'])){
    header("location: login.php");
    exit;
}
include('db.php');

$message = '';
$messageType = '';

if(isset($_GET['delete_user'])) {
    $userId = $_GET['delete_user'];
    $sql = "DELETE FROM users WHERE id = '$userId'";
    if ($conn->query($sql) === TRUE) {
        $message = "User deleted successfully";
        $messageType = "success";
    } else {
        $message = "Error deleting user: " . $conn->error;
        $messageType = "error";
    }
}

if(isset($_GET['toggle_suspension'])) {
    $userId = $_GET['toggle_suspension'];
    $sql = "UPDATE users SET status = (CASE WHEN status = 'active' THEN 'suspended' ELSE 'active' END) WHERE id = '$userId'";
    if ($conn->query($sql) === TRUE) {
        $message = "User status updated successfully";
        $messageType = "success";
    } else {
        $message = "Error updating user status: " . $conn->error;
        $messageType = "error";
    }
}

$sql = "SELECT id, username, status FROM users";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Users</title>
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
        
        .btn-delete {
            color: #fff;
            background-color: #e74c3c;
        }
        
        .btn-delete:hover {
            background-color: #c0392b;
        }
        
        .btn-suspend {
            color: #fff;
            background-color: #f1c40f;
        }
        
        .btn-suspend:hover {
            background-color: #d4ac0d;
        }
        
        .btn-activate {
            color: #fff;
            background-color: #2ecc71;
        }
        
        .btn-activate:hover {
            background-color: #27ae60;
        }
        
        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .status-active {
            background-color: #2ecc71;
            color: #fff;
        }
        
        .status-suspended {
            background-color: #e74c3c;
            color: #fff;
        }
        
        .empty-message {
            text-align: center;
            padding: 30px;
            color: #7f8c8d;
            font-size: 18px;
        }
        
        .alert {
            padding: 12px 20px;
            border-radius: 6px;
            margin-bottom: 20px;
            animation: fadeIn 0.5s;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
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
            <h2>Manage Users</h2>
        </div>
        
        <?php if(!empty($message)) { ?>
            <div class="alert <?php echo $messageType === 'error' ? 'alert-danger' : 'alert-success'; ?>">
                <?php echo $message; ?>
            </div>
        <?php } ?>
        
        <div class="table-container">
            <?php
            // Check connection
            if (!$conn) {
                echo '<div class="alert alert-danger">Database connection failed: ' . mysqli_connect_error() . '</div>';
            } else {
                // Check if there are any results
                if ($result && mysqli_num_rows($result) > 0) { ?>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Username</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = $result->fetch_assoc()) { 
                                $userId = $row['id'];
                                $username = htmlspecialchars($row['username']);
                                $status = $row['status'] ?? 'active';
                                
                                // Set status badge class
                                $statusClass = ($status == 'active') ? 'status-active' : 'status-suspended';
                            ?>
                                <tr>
                                    <td><?php echo $userId; ?></td>
                                    <td><?php echo $username; ?></td>
                                    <td>
                                        <span class="status-badge <?php echo $statusClass; ?>">
                                            <?php echo ucfirst($status); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="?delete_user=<?php echo $userId; ?>" 
                                           onclick="return confirm('Are you sure you want to delete this user?')" 
                                           class="btn btn-delete">Delete</a>
                                        
                                        <?php if ($status == 'active') { ?>
                                            <a href="?toggle_suspension=<?php echo $userId; ?>" 
                                               class="btn btn-suspend">Suspend</a>
                                        <?php } else { ?>
                                            <a href="?toggle_suspension=<?php echo $userId; ?>" 
                                               class="btn btn-activate">Activate</a>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                <?php } else { ?>
                    <div class="empty-message">
                        <p>No users found in the system.</p>
                    </div>
                <?php }
            } ?>
        </div>
    </div>
    
    <script>
        // Auto-hide alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                setTimeout(function() {
                    alert.style.opacity = '0';
                    setTimeout(function() {
                        alert.style.display = 'none';
                    }, 500);
                }, 5000);
            });
        });
    </script>
</body>
</html>