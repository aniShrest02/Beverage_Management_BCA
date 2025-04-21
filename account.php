<?php
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "beverage_management";



// Fetch user data if logged in
$user_id = $_SESSION['user_id'] ?? null; // Assuming user ID is stored in session after login
$user = null;

if ($user_id) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
    $stmt->execute(['id' => $user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Handle form submission for updating profile
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);

    $stmt = $pdo->prepare("UPDATE users SET fullname = :fullname, email = :email, phone = :phone, address = :address WHERE id = :id");
    $stmt->execute([
        'fullname' => $fullname,
        'email' => $email,
        'phone' => $phone,
        'address' => $address,
        'id' => $user_id
    ]);

    // Refresh user data after update
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
    $stmt->execute(['id' => $user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    echo '<script>alert("Profile updated successfully!");</script>';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Account - A & B STORE</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <style>
        /* Import font */
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        /* Reset */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
            color: #333;
            line-height: 1.5;
        }
        /* Container */
        .container {
            width: 90%;
            max-width: 1100px;
            margin: 0 auto;
            padding: 15px;
        }
        /* Header */
        header {
            background-color: #fff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            padding: 15px 0;
        }
        .nav-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logo {
            font-size: 22px;
            font-weight: 700;
            color: #333;
            text-decoration: none;
        }
        .logo span {
            color: #4361ee;
        }
        .nav-menu {
            display: flex;
            gap: 20px;
            list-style: none;
        }
        .nav-menu a {
            text-decoration: none;
            color: #555;
            font-weight: 500;
            font-size: 15px;
            transition: color 0.2s;
        }
        .nav-menu a:hover, .nav-menu a.active {
            color: #4361ee;
        }
        /* Page title */
        .page-title {
            margin: 30px 0;
            font-size: 24px;
            font-weight: 600;
            color: #333;
        }
        /* Main layout */
        .account-layout {
            display: grid;
            grid-template-columns: 220px 1fr;
            gap: 25px;
            margin-bottom: 40px;
        }
        /* Sidebar */
        .sidebar {
            background-color: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        .menu-list {
            list-style: none;
        }
        .menu-item a {
            display: flex;
            align-items: center;
            padding: 15px 20px;
            text-decoration: none;
            color: #555;
            border-left: 3px solid transparent;
            transition: all 0.2s;
        }
        .menu-item a i {
            margin-right: 12px;
            font-size: 16px;
            width: 20px;
            text-align: center;
            color: #777;
        }
        .menu-item a:hover, .menu-item a.active {
            background-color: #f8f9fc;
            color: #4361ee;
            border-left-color: #4361ee;
        }
        .menu-item a:hover i, .menu-item a.active i {
            color: #4361ee;
        }
        /* Content area */
        .content-area {
            background-color: #fff;
            border-radius: 8px;
            padding: 25px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        .section {
            display: none;
        }
        .section.active {
            display: block;
        }
        .section-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        /* Profile section */
        .profile-header {
            display: flex;
            align-items: center;
            margin-bottom: 25px;
        }
        .avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background-color: #4361ee;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            margin-right: 15px;
        }
        .user-name {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 4px;
        }
        .user-email {
            color: #777;
            font-size: 14px;
        }
        /* Form elements */
        .form-row {
            margin-bottom: 20px;
        }
        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            font-size: 14px;
            color: #555;
        }
        .form-input {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            transition: border-color 0.2s;
        }
        .form-input:focus {
            outline: none;
            border-color: #4361ee;
        }
        .btn {
            background-color: #4361ee;
            color: white;
            border: none;
            border-radius: 6px;
            padding: 10px 20px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        .btn:hover {
            background-color: #3a56d4;
        }
        footer {
    background-color: #fff;
    padding: 40px 0;
    margin-top: 60px;
    border-top: 1px solid #eee;
}

.footer-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.footer-links a {
    color: #555;
    text-decoration: none;
    margin-right: 20px;
    font-size: 14px;
    transition: color 0.2s;
}

.footer-links a:hover {
    color: #5a52e2;
}

.copyright {
    color: #777;
    font-size: 14px;
}
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <div class="container nav-container">
            <a href="index.php" class="logo">A & <span>B</span> STORE</a>
            <ul class="nav-menu">
                <li><a href="index.php">Home</a></li>
                <li><a href="shop.php">Shop</a></li>
                <li><a href="cart.php">Cart</a></li>
                <li><a href="account.php" class="active">Account</a></li>
            </ul>
        </div>
    </header>
    <!-- Main Content -->
    <main class="container">
        <h1 class="page-title">My Account</h1>
        <div class="account-layout">
            <!-- Sidebar -->
            <aside class="sidebar">
                <ul class="menu-list">
                    <li class="menu-item">
                        <a href="#profile" class="active" data-section="profile">
                            <i class="fas fa-user"></i> My Profile
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="#orders" data-section="orders">
                            <i class="fas fa-shopping-bag"></i> My Orders
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="#password" data-section="password">
                            <i class="fas fa-lock"></i> Change Password
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="#addresses" data-section="addresses">
                            <i class="fas fa-map-marker-alt"></i> Addresses
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="#wishlist" data-section="wishlist">
                            <i class="fas fa-heart"></i> Wishlist
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="logout.php">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </li>
                </ul>
            </aside>
            <!-- Content Area -->
            <div class="content-area">
                <!-- Profile Section -->
                <section id="profile" class="section active">
                    <h2 class="section-title">My Profile</h2>
                    <div class="profile-header">
                        <div class="avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <div>
                            <h3 class="user-name"><?php echo htmlspecialchars($user['fullname'] ?? ''); ?></h3>
                            <p class="user-email"><?php echo htmlspecialchars($user['email'] ?? ''); ?></p>
                        </div>
                    </div>
                    <form action="" method="post">
                        <div class="form-row">
                            <label class="form-label" for="fullname">Full Name</label>
                            <input type="text" id="fullname" name="fullname" class="form-input" value="<?php echo htmlspecialchars($user['fullname'] ?? ''); ?>" required>
                        </div>
                        <div class="form-row">
                            <label class="form-label" for="email">Email Address</label>
                            <input type="email" id="email" name="email" class="form-input" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>
                        </div>
                        <div class="form-row">
                            <label class="form-label" for="phone">Phone Number</label>
                            <input type="tel" id="phone" name="phone" class="form-input" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                        </div>
                        <div class="form-row">
                            <label class="form-label" for="address">Address</label>
                            <input type="text" id="address" name="address" class="form-input" value="<?php echo htmlspecialchars($user['address'] ?? ''); ?>">
                        </div>
                        <button type="submit" name="update_profile" class="btn">Save Changes</button>
                    </form>
                </section>
            </div>
        </div>
    </main>
    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-links">
                    <a href="about.php">About Us</a>
                    <a href="contact.php">Contact</a>
                    <a href="privacy.php">Privacy Policy</a>
                    <a href="terms.php">Terms & Conditions</a>
                </div>
                <div class="copyright">
                    <p>&copy; 2024 A & B STORE. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>
    <script>
        // Simple tab functionality
        document.addEventListener('DOMContentLoaded', function() {
            const menuLinks = document.querySelectorAll('.menu-item a');
            menuLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    // Update active menu item
                    menuLinks.forEach(item => item.classList.remove('active'));
                    this.classList.add('active');
                    // Show corresponding section
                    const sectionId = this.getAttribute('data-section');
                    document.querySelectorAll('.section').forEach(section => {
                        section.classList.remove('active');
                    });
                    document.getElementById(sectionId).classList.add('active');
                });
            });
        });
    </script>
</body>
</html>