<?php
session_start();
if (!isset($_SESSION['login_user'])) {
    header("location: login.php");
    exit;
}
include('db.php');

$message = '';
$messageType = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $type = $_POST['type'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];
    
    // Handle file upload
    $targetDir = "img/";
    $fileName = basename($_FILES["photo"]["name"]);
    $targetFilePath = $targetDir . $fileName;
    $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
    
    if ($price < 0) {
        $message = "Price cannot be negative.";
        $messageType = "error";
    } else {
        // Upload image file if present
        $uploadOk = 1;
        if(!empty($fileName)) {
            // Create directory if it doesn't exist
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0777, true);
            }
            
            // Check if file already exists
            if (file_exists($targetFilePath)) {
                $fileName = time() . '_' . $fileName;
                $targetFilePath = $targetDir . $fileName;
            }
            
            // Check file size (5MB max)
            if ($_FILES["photo"]["size"] > 5000000) {
                $message = "Sorry, your file is too large.";
                $messageType = "error";
                $uploadOk = 0;
            }
            
            // Allow certain file formats
            $allowedTypes = array('jpg', 'png', 'jpeg', 'gif');
            if(!in_array(strtolower($fileType), $allowedTypes)) {
                $message = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
                $messageType = "error";
                $uploadOk = 0;
            }
            
            // Check if $uploadOk is set to 0 by an error
            if ($uploadOk == 1) {
                if (move_uploaded_file($_FILES["photo"]["tmp_name"], $targetFilePath)) {
                    // File uploaded successfully
                } else {
                    $message = "Sorry, there was an error uploading your file.";
                    $messageType = "error";
                    $uploadOk = 0;
                }
            }
        } else {
            // No file uploaded, use a default image
            $fileName = "default_beverage.jpg";
        }
        
        if (empty($message)) {
            $sql = "INSERT INTO beverages (name, image, type, price, quantity) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            if ($stmt === false) {
                $message = "Prepare failed: " . htmlspecialchars($conn->error);
                $messageType = "error";
            } else {
                $stmt->bind_param("sssdi", $name, $fileName, $type, $price, $quantity);
                if ($stmt->execute()) {
                    $message = "New beverage added successfully!";
                    $messageType = "success";
                } else {
                    $message = "Error: " . htmlspecialchars($stmt->error);
                    $messageType = "error";
                }
                $stmt->close();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Beverage</title>
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
        
        .form-container {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            padding: 30px;
            max-width: 600px;
            margin: 0 auto;
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
        
        .file-input-container {
            position: relative;
            overflow: hidden;
            display: inline-block;
            width: 100%;
        }
        
        .file-input-button {
            display: block;
            background-color: #f9f9f9;
            color: #333;
            border: 1px solid #ddd;
            border-radius: 6px;
            padding: 12px;
            text-align: center;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .file-input-button:hover {
            background-color: #eee;
        }
        
        .file-input {
            position: absolute;
            left: 0;
            top: 0;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }
        
        .file-name {
            margin-top: 8px;
            font-size: 14px;
            color: #666;
        }
        
        .btn {
            display: inline-block;
            font-weight: 500;
            text-align: center;
            white-space: nowrap;
            vertical-align: middle;
            user-select: none;
            border: none;
            padding: 12px 24px;
            font-size: 16px;
            line-height: 1.5;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn-primary {
            color: #fff;
            background-color: #3498db;
        }
        
        .btn-primary:hover {
            background-color: #2980b9;
        }
        
        .btn-lg {
            width: 100%;
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
        
        .card-header {
            border-bottom: 1px solid #eee;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        
        .card-header h3 {
            margin: 0;
            color: #2c3e50;
        }
        
        .card-footer {
            border-top: 1px solid #eee;
            padding-top: 20px;
            margin-top: 20px;
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
            <h2>Add New Beverage</h2>
        </div>
        
        <div class="form-container">
            <?php if(!empty($message)): ?>
                <div class="alert <?php echo $messageType === 'error' ? 'alert-danger' : 'alert-success'; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
            
            <div class="card-header">
                <h3>Beverage Details</h3>
            </div>
            
            <form method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="name">Beverage Name</label>
                    <input type="text" class="form-control" id="name" name="name" required placeholder="Enter beverage name">
                </div>
                
                <div class="form-group">
                    <label for="type">Beverage Type</label>
                    <select class="form-control" id="type" name="type" required>
                        <option value="">Select beverage type</option>
                        <option value="Juice">Juice</option>
                        <option value="Alcohol">Alcohol</option>
                        <option value="Soft Drinks">Soft Drinks</option>
                        <option value="Energy Drinks">Energy Drinks</option>
                        <option value="Coffee">Coffee</option>
                        <option value="Tea">Tea</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="price">Price (Rs)</label>
                    <input type="number" step="0.01" class="form-control" id="price" name="price" required placeholder="Enter price">
                </div>
                
                <div class="form-group">
                    <label for="quantity">Stock Quantity</label>
                    <input type="number" class="form-control" id="quantity" name="quantity" required placeholder="Enter quantity">
                </div>
                
                <div class="form-group">
                    <label>Beverage Image</label>
                    <div class="file-input-container">
                        <div class="file-input-button">Choose Image</div>
                        <input type="file" name="photo" class="file-input" id="fileInput" accept="image/*">
                    </div>
                    <div class="file-name" id="fileName">No file chosen</div>
                </div>
                
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary btn-lg">Add Beverage</button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
    // Display filename when file is selected
    document.getElementById('fileInput').addEventListener('change', function() {
        const fileName = this.files[0] ? this.files[0].name : 'No file chosen';
        document.getElementById('fileName').textContent = fileName;
    });
    </script>
</body>
</html>