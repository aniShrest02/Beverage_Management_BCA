<?php
session_start();
if (!isset($_SESSION['login_user'])) {
    header("location: login.php");
    exit;
}

include 'db.php';

$message = '';
$messageType = '';

// Handle order status updates
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action']) && isset($_POST['order_id'])) {
        $orderId = (int)$_POST['order_id'];
        $action = $_POST['action'];
        
        // Validate order ID
        if ($orderId <= 0) {
            $message = "Invalid order ID";
            $messageType = "error";
        } else {
            if ($action == 'accept') {
                $sql = "UPDATE orders SET status = 'Processing' WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $orderId);
                
                if ($stmt->execute()) {
                    $message = "Order #$orderId has been accepted and is now being processed.";
                    $messageType = "success";
                } else {
                    $message = "Error updating order status: " . $stmt->error;
                    $messageType = "error";
                }
                $stmt->close();
            } 
            elseif ($action == 'decline') {
                if (empty($_POST['decline_reason'])) {
                    $message = "Please provide a reason for declining the order";
                    $messageType = "error";
                } else {
                    $declineReason = mysqli_real_escape_string($conn, $_POST['decline_reason']);
                    
                    $sql = "UPDATE orders SET status = 'Declined', decline_reason = ? WHERE id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("si", $declineReason, $orderId);
                    
                    if ($stmt->execute()) {
                        $message = "Order #$orderId has been declined.";
                        $messageType = "success";
                    } else {
                        $message = "Error updating order status: " . $stmt->error;
                        $messageType = "error";
                    }
                    $stmt->close();
                }
            }
            elseif ($action == 'complete') {
                $sql = "UPDATE orders SET status = 'Completed' WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $orderId);
                
                if ($stmt->execute()) {
                    $message = "Order #$orderId has been marked as completed.";
                    $messageType = "success";
                } else {
                    $message = "Error updating order status: " . $stmt->error;
                    $messageType = "error";
                }
                $stmt->close();
            }
        }
        
        // Refresh the page to show updated status
        header("Location: admin_orders.php?message=" . urlencode($message) . "&type=" . urlencode($messageType));
        exit();
    }
}

// Get message from URL if redirected
if (isset($_GET['message']) && isset($_GET['type'])) {
    $message = urldecode($_GET['message']);
    $messageType = urldecode($_GET['type']);
}

// Fetch all orders
$sql = "SELECT id, name, address, product_names, total_price, status, decline_reason FROM orders ORDER BY id DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Orders</title>
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
        
        .btn-accept {
            color: #fff;
            background-color: #2ecc71;
        }
        
        .btn-accept:hover {
            background-color: #27ae60;
        }
        
        .btn-decline {
            color: #fff;
            background-color: #e74c3c;
        }
        
        .btn-decline:hover {
            background-color: #c0392b;
        }
        
        .btn-complete {
            color: #fff;
            background-color: #3498db;
        }
        
        .btn-complete:hover {
            background-color: #2980b9;
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
        
        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        
        .modal-content {
            background-color: #fff;
            margin: 10% auto;
            padding: 30px;
            border-radius: 8px;
            width: 500px;
            max-width: 90%;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
            position: relative;
        }
        
        .close {
            position: absolute;
            right: 20px;
            top: 15px;
            font-size: 24px;
            font-weight: bold;
            color: #aaa;
            cursor: pointer;
        }
        
        .close:hover {
            color: #555;
        }
        
        .modal-header {
            border-bottom: 1px solid #eee;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        
        .modal-header h3 {
            margin: 0;
            color: #2c3e50;
        }
        
        .modal-footer {
            border-top: 1px solid #eee;
            padding-top: 20px;
            margin-top: 20px;
            text-align: right;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #2c3e50;
        }
        
        .form-control {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-family: inherit;
            font-size: 16px;
            transition: border-color 0.3s;
            box-sizing: border-box;
        }
        
        .form-control:focus {
            border-color: #3498db;
            outline: none;
        }
        
        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .status-pending {
            background-color: #f1c40f;
            color: #7f6800;
        }
        
        .status-processing {
            background-color: #3498db;
            color: #fff;
        }
        
        .status-declined {
            background-color: #e74c3c;
            color: #fff;
        }
        
        .status-completed {
            background-color: #2ecc71;
            color: #fff;
        }
        
        /* Tooltip for decline reason */
        .decline-reason {
            position: relative;
            cursor: help;
            border-bottom: 1px dotted #e74c3c;
        }
        
        .decline-reason .tooltip-text {
            visibility: hidden;
            width: 200px;
            background-color: #333;
            color: #fff;
            text-align: center;
            border-radius: 6px;
            padding: 5px;
            position: absolute;
            z-index: 1;
            bottom: 125%;
            left: 50%;
            margin-left: -100px;
            opacity: 0;
            transition: opacity 0.3s;
        }
        
        .decline-reason:hover .tooltip-text {
            visibility: visible;
            opacity: 1;
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
            <h2>Manage Orders</h2>
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
                // Check if there was an error with the query
                if ($result === false) {
                    echo '<div class="alert alert-danger">Error executing query: ' . mysqli_error($conn) . '</div>';
                }
                // Check if there are any results
                elseif (mysqli_num_rows($result) > 0) { ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer Name</th>
                                <th>Delivery Address</th>
                                <th>Products Ordered</th>
                                <th>Total Price</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($result)) { 
                                $orderId = $row['id'];
                                $name = htmlspecialchars($row['name']);
                                $address = htmlspecialchars($row['address']);
                                $productNames = htmlspecialchars($row['product_names']);
                                $totalPrice = 'Rs' . number_format($row['total_price'], 2);
                                $status = $row['status'] ?? 'Pending';
                                $declineReason = htmlspecialchars($row['decline_reason'] ?? '');
                                
                                // Set status badge class
                                $statusClass = '';
                                switch ($status) {
                                    case 'Pending':
                                        $statusClass = 'status-pending';
                                        break;
                                    case 'Processing':
                                        $statusClass = 'status-processing';
                                        break;
                                    case 'Declined':
                                        $statusClass = 'status-declined';
                                        break;
                                    case 'Completed':
                                        $statusClass = 'status-completed';
                                        break;
                                }
                            ?>
                                <tr>
                                    <td><?php echo $orderId; ?></td>
                                    <td><?php echo $name; ?></td>
                                    <td><?php echo $address; ?></td>
                                    <td><?php echo $productNames; ?></td>
                                    <td><?php echo $totalPrice; ?></td>
                                    <td>
                                        <span class="status-badge <?php echo $statusClass; ?>">
                                            <?php echo $status; ?>
                                        </span>
                                        <?php if ($status == 'Declined' && !empty($declineReason)) { ?>
                                            <span class="decline-reason">
                                                (?)
                                                <span class="tooltip-text"><?php echo $declineReason; ?></span>
                                            </span>
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <?php if ($status == 'Pending') { ?>
                                            <form method="post" style="display: inline;">
                                                <input type="hidden" name="action" value="accept">
                                                <input type="hidden" name="order_id" value="<?php echo $orderId; ?>">
                                                <button type="submit" class="btn btn-accept" onclick="return confirm('Are you sure you want to accept this order?')">Accept</button>
                                            </form>
                                            
                                            <button class="btn btn-decline" onclick="openDeclineModal(<?php echo $orderId; ?>)">Decline</button>
                                        <?php } elseif ($status == 'Processing') { ?>
                                            <form method="post" style="display: inline;">
                                                <input type="hidden" name="action" value="complete">
                                                <input type="hidden" name="order_id" value="<?php echo $orderId; ?>">
                                                <button type="submit" class="btn btn-complete" onclick="return confirm('Mark this order as completed?')">Complete</button>
                                            </form>
                                        <?php } elseif ($status == 'Declined') { ?>
                                            <span class="decline-reason">Declined</span>
                                        <?php } elseif ($status == 'Completed') { ?>
                                            <span>Completed</span>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                <?php } else { ?>
                    <div class="empty-message">
                        <p>No orders found in the system.</p>
                    </div>
                <?php }
            } ?>
        </div>
    </div>
    
    <!-- Decline Order Modal -->
    <div id="declineModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeDeclineModal()">&times;</span>
            
            <div class="modal-header">
                <h3>Decline Order</h3>
            </div>
            
            <form method="post" id="declineOrderForm">
                <input type="hidden" name="action" value="decline">
                <input type="hidden" name="order_id" id="decline_order_id">
                
                <div class="form-group">
                    <label for="decline_reason">Please provide a reason for declining this order:</label>
                    <textarea class="form-control" id="decline_reason" name="decline_reason" rows="4" required></textarea>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn" onclick="closeDeclineModal()">Cancel</button>
                    <button type="submit" class="btn btn-decline">Confirm Decline</button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        // Function to open decline modal
        function openDeclineModal(orderId) {
            document.getElementById('decline_order_id').value = orderId;
            document.getElementById('declineModal').style.display = 'block';
        }
        
        // Function to close decline modal
        function closeDeclineModal() {
            document.getElementById('declineModal').style.display = 'none';
            document.getElementById('decline_reason').value = '';
        }
        
        // Close modal when clicking outside of it
        window.onclick = function(event) {
            const modal = document.getElementById('declineModal');
            if (event.target == modal) {
                closeDeclineModal();
            }
        }
        
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
            
            // Validate decline form before submission
            document.getElementById('declineOrderForm').addEventListener('submit', function(e) {
                const reason = document.getElementById('decline_reason').value.trim();
                if (!reason) {
                    e.preventDefault();
                    alert('Please provide a reason for declining this order');
                    document.getElementById('decline_reason').focus();
                }
            });
        });
    </script>
</body>
</html>