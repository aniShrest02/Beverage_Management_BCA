<?php
session_start();
// if (!isset($_SESSION['login_user'])) {
//     header("location: login.php");
//     exit;
// }

// Database connection
$conn = mysqli_connect("localhost", "root", "", "beverage_management");

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Get total users count
$user_query = "SELECT COUNT(*) as total_users FROM users WHERE role = 'user'";
$user_result = mysqli_query($conn, $user_query);
$total_users = 0;
if ($user_result) {
    $user_data = mysqli_fetch_assoc($user_result);
    $total_users = $user_data['total_users'];
}

// Get total beverages count
$beverage_query = "SELECT COUNT(*) as total_beverages FROM beverages";
$beverage_result = mysqli_query($conn, $beverage_query);
$total_beverages = 0;
if ($beverage_result) {
    $beverage_data = mysqli_fetch_assoc($beverage_result);
    $total_beverages = $beverage_data['total_beverages'];
}

// Get total orders count - check if the table exists first
$total_orders = 0;
$table_check_query = "SHOW TABLES LIKE 'orders'";
$table_result = mysqli_query($conn, $table_check_query);
if (mysqli_num_rows($table_result) > 0) {
    $order_query = "SELECT COUNT(*) as total_orders FROM orders";
    $order_result = mysqli_query($conn, $order_query);
    if ($order_result) {
        $order_data = mysqli_fetch_assoc($order_result);
        $total_orders = $order_data['total_orders'];
    }
}

// Get recent orders - handle possible table structure differences
$recent_orders_result = false;
if (mysqli_num_rows($table_result) > 0) {
    // Adjusted query to match existing column names
    $recent_orders_query = "SELECT id, name, address, total_price, status FROM orders ORDER BY id DESC LIMIT 5";
    $recent_orders_result = mysqli_query($conn, $recent_orders_query);
    
    if (!$recent_orders_result) {
        error_log("First query failed: " . mysqli_error($conn));

        // Check table structure
        $recent_orders_query = "SHOW COLUMNS FROM orders";
        $cols_result = mysqli_query($conn, $recent_orders_query);
        if ($cols_result) {
            $columns = [];
            while ($col = mysqli_fetch_assoc($cols_result)) {
                $columns[] = $col['Field'];
            }
            
            // Build a query based on available columns
            $select_fields = "id"; // Primary key (corrected from order_id)
            if (in_array('name', $columns)) $select_fields .= ", name";
            if (in_array('address', $columns)) $select_fields .= ", address";
            if (in_array('total_price', $columns)) $select_fields .= ", total_price";
            if (in_array('status', $columns)) $select_fields .= ", status";
            
            $recent_orders_query = "SELECT $select_fields FROM orders ORDER BY id DESC LIMIT 5";
            $recent_orders_result = mysqli_query($conn, $recent_orders_query);
        }
    }
}

// Close database connection
mysqli_close($conn);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
      <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            display: flex;
            min-height: 100vh;
        }
        header {
            width: 250px;
            background-color: #2c3e50;
            height: 100vh;
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
            flex: 1;
            padding: 20px 30px 30px;
            overflow-y: auto;
        }
        .welcome-banner {
            background-color: #3498db;
            color: white;
            padding: 20px 30px;
            margin-bottom: 30px;
            border-radius: 8px;
        }
        .dashboard {
            padding-bottom: 30px;
        }
        .stats-container {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }
        .tile {
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            padding: 30px;
            border-radius: 10px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            flex: 1;
            min-height: 180px;
            position: relative;
            overflow: hidden;
            min-width: 200px;
        }
        .tile::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.4);
            z-index: 1;
        }
        .tile:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
        }
        .tile-content {
            position: relative;
            z-index: 2;
            text-align: center;
        }
        .tile-number {
            font-size: 48px;
            font-weight: bold;
            margin-bottom: 10px;
            color: white;
        }
        .tile-label {
            font-size: 18px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: white;
            font-weight: 500;
        }
        .total-users {
            background-color: #3498db;
        }
        .total-beverages {
            background-color: #2ecc71;
        }
        .total-orders {
            background-color: #e74c3c;
        }
        .recent-section {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        .recent-section h2 {
            margin-top: 0;
            color: #2c3e50;
            border-bottom: 2px solid #f1f1f1;
            padding-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table th {
            background-color: #f9f9f9;
            text-align: left;
            padding: 12px;
            font-weight: 600;
            color: #2c3e50;
        }
        table td {
            padding: 12px;
            border-bottom: 1px solid #f1f1f1;
        }
        .status {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }
        .status-pending {
            background-color: #f39c12;
            color: white;
        }
        .status-completed {
            background-color: #2ecc71;
            color: white;
        }
        .status-cancelled {
            background-color: #e74c3c;
            color: white;
        }
        .quick-actions {
            margin-top: 30px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }
        .action-btn {
            background-color: #ecf0f1;
            border: none;
            padding: 15px;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            color: #2c3e50;
            transition: background-color 0.3s;
        }
        .action-btn:hover {
            background-color: #e0e6eb;
        }
        .action-btn span {
            margin-top: 8px;
            font-weight: 500;
        }
        .no-data {
            text-align: center;
            padding: 20px;
            color: #7f8c8d;
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
        <div class="welcome-banner">
            <h2>Welcome, <?php echo isset($_SESSION['login_user']) ? $_SESSION['login_user'] : 'Admin'; ?>!</h2>
            <p>Here's what's happening with your beverage store today</p>
        </div>
        
        <div class="dashboard">
            <div class="stats-container">
                <div class="tile total-users">
                    <div class="tile-content">
                        <div class="tile-number"><?php echo $total_users; ?></div>
                        <div class="tile-label">Total Users</div>
                    </div>
                </div>
                <div class="tile total-beverages">
                    <div class="tile-content">
                        <div class="tile-number"><?php echo $total_beverages; ?></div>
                        <div class="tile-label">Total Beverages</div>
                    </div>
                </div>
                <div class="tile total-orders">
                    <div class="tile-content">
                        <div class="tile-number"><?php echo $total_orders; ?></div>
                        <div class="tile-label">Total Orders</div>
                    </div>
                </div>
            </div>
            
            <div class="recent-section">
                <h2>Recent Orders</h2>
                <?php if($recent_orders_result && mysqli_num_rows($recent_orders_result) > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($order = mysqli_fetch_assoc($recent_orders_result)): ?>
                                <tr>
                                    <td><?php echo $order['id']; ?></td>
                                    <td>
                                    <?php 
                                        if(isset($order['name'])) {
                                            echo $order['name'];
                                        } else {
                                            echo "Customer";
                                        }
                                    ?>
                                    </td>
                                    <td>
                                    <?php 
                                        if(isset($order['address'])) {
                                            echo $order['address'];
                                        } else {
                                            echo "User Location";
                                        }
                                    ?>
                                    </td>
                                    <td>
                                    <?php 
                                        if(isset($order['status'])) {
                                            $status_class = '';
                                            switch($order['status']) {
                                                case 'Pending':
                                                    $status_class = 'status-pending';
                                                    break;
                                                case 'Completed':
                                                    $status_class = 'status-completed';
                                                    break;
                                                case 'Cancelled':
                                                    $status_class = 'status-cancelled';
                                                    break;
                                                default:
                                                    $status_class = '';
                                            }
                                            echo '<span class="status ' . $status_class . '">' . $order['status'] . '</span>';
                                        } else {
                                            echo "N/A";
                                        }
                                    ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="no-data">
                        <p>No orders found. Start selling your beverages!</p>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="quick-actions">
                <a href="add_beverage.php" class="action-btn">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 5V19M5 12H19" stroke="#2c3e50" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span>Add Beverage</span>
                </a>
                <a href="view_users.php" class="action-btn">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M17 21V19C17 16.7909 15.2091 15 13 15H5C2.79086 15 1 16.7909 1 19V21M23 21V19C22.9986 17.1771 21.765 15.5857 20 15.13M16 3.13C17.7699 3.58317 19.0078 5.17703 19.0078 7C19.0078 8.82297 17.7699 10.4168 16 10.87M9 7C9 9.20914 7.20914 11 5 11C2.79086 11 1 9.20914 1 7C1 4.79086 2.79086 3 5 3C7.20914 3 9 4.79086 9 7Z" stroke="#2c3e50" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span>Manage Users</span>
                </a>
                <a href="admin_orders.php" class="action-btn">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9 5H7C5.89543 5 5 5.89543 5 7V19C5 20.1046 5.89543 21 7 21H17C18.1046 21 19 20.1046 19 19V7C19 5.89543 18.1046 5 17 5H15M9 5C9 6.10457 9.89543 7 11 7H13C14.1046 7 15 6.10457 15 5M9 5C9 3.89543 9.89543 3 11 3H13C14.1046 3 15 3.89543 15 5M12 12H15M12 16H15M9 12H9.01M9 16H9.01" stroke="#2c3e50" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span>View Orders</span>
                </a>
            </div>
        </div>
    </div>
</body>
</html>