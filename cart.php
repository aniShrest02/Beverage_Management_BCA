<?php
session_start();
include 'db.php';

// Initialize cart count at the top to avoid undefined variable warning
$cart_count = isset($_SESSION['cart']) && is_array($_SESSION['cart']) ? count($_SESSION['cart']) : 0;

// Initialize discount variables
$discount = 0;
$discountApplied = false;
$discountError = '';

// Handle promo code submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['promo_code'])) {
    $promoCode = strtoupper(trim($_POST['promo_code']));
    
    if ($promoCode === 'BEV25') {
        $discountApplied = true;
        $_SESSION['discount'] = 0.25; // 25% discount
    } else {
        $discountError = "Invalid promo code";
        unset($_SESSION['discount']);
    }
}

// Remove discount if requested
if (isset($_GET['remove_discount'])) {
    unset($_SESSION['discount']);
    $discountApplied = false;
}

function decreaseProductQuantity($conn, $productId, $quantity) {
    $sql = "UPDATE beverages SET quantity = quantity - ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $quantity, $productId);
    $stmt->execute();
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'add') {
    $productId = intval($_POST['product_id']);
    $productQuantity = intval($_POST['quantity']);

    $sql = "SELECT quantity FROM beverages WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $availableStock = $row['quantity'];
    $stmt->close();

    if ($productQuantity > $availableStock) {
        echo json_encode(array("status" => "error", "message" => "Quantity exceeds available stock."));
        exit;
    }

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    $found = false;
    foreach ($_SESSION['cart'] as &$item) {
        if ($item['id'] == $productId) {
            $item['quantity'] += $productQuantity;
            $found = true;
            break;
        }
    }

    if (!$found) {
        $_SESSION['cart'][] = ['id' => $productId, 'quantity' => $productQuantity];
    }

    echo json_encode(array("status" => "success", "message" => "Item added to cart!"));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'remove') {
    $productId = intval($_POST['product_id']);
    foreach ($_SESSION['cart'] as $key => $item) {
        if ($item['id'] == $productId) {
            unset($_SESSION['cart'][$key]);
            break;
        }
    }
    header('Location: cart.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update') {
    $productId = intval($_POST['product_id']);
    $productQuantity = intval($_POST['quantity']);

    $sql = "SELECT quantity FROM beverages WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $availableStock = $row['quantity'];
    $stmt->close();

    if ($productQuantity > $availableStock) {
        $_SESSION['error_message'] = "Quantity exceeds available stock.";
        header('Location: cart.php');
        exit;
    }

    foreach ($_SESSION['cart'] as &$item) {
        if ($item['id'] == $productId) {
            $item['quantity'] = $productQuantity;
            break;
        }
    }
    header('Location: cart.php');
    exit;
}

$totalPrice = 0;
$productNames = [];
if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0) {
    foreach ($_SESSION['cart'] as $item) {
        $productId = $item['id'];
        $quantity = $item['quantity'];

        $sql = "SELECT name, price, image FROM beverages WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $productId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        $productName = $row['name'];
        $productPrice = $row['price'];
        $productImage = $row['image'] ?? 'placeholder.jpg';
        $itemTotal = $productPrice * $quantity;
        $totalPrice += $itemTotal;

        $productNames[] = "$productName (x$quantity)";
        $stmt->close();
    }
}

// Calculate discount if applied
if (isset($_SESSION['discount'])) {
    $discount = $_SESSION['discount'];
    $discountApplied = true;
    $discountAmount = $totalPrice * $discount;
    $totalAfterDiscount = $totalPrice - $discountAmount;
} else {
    $discountAmount = 0;
    $totalAfterDiscount = $totalPrice;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['action']) && !isset($_POST['promo_code'])) {
    $name = $_POST['name'];
    $address = $_POST['address'];
    $cardNumber = $_POST['card_number'];
    $expirationDate = $_POST['expiration_date'];
    $cvv = $_POST['cvv'];
    $productNamesString = implode(", ", $productNames); 

    $sql = "INSERT INTO orders (name, address, card_number, expiration_date, cvv, total_price, product_names) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssss", $name, $address, $cardNumber, $expirationDate, $cvv, $totalAfterDiscount, $productNamesString);

    if ($stmt->execute()) {
        foreach ($_SESSION['cart'] as $item) {
            decreaseProductQuantity($conn, $item['id'], $item['quantity']);
        }
        echo "<script>alert('Order placed successfully!'); window.location.href='cart.php';</script>";
        unset($_SESSION['cart']);
        unset($_SESSION['discount']);
    } else {
        echo "<script>alert('Error placing order: " . $stmt->error . "');</script>";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart | A & B STORE</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap');

        :root {
            --primary-color: #5a52e2;
            --secondary-color: #4a42d2;
            --text-color: #333;
            --light-text: #666;
            --border-color: #eee;
            --background-color: #f8f8f8;
            --white: #fff;
            --success-color: #2e7d32;
            --error-color: #c62828;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--background-color);
            color: var(--text-color);
            line-height: 1.6;
        }

        .container {
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        /* Header Styles */
        header {
            background-color: var(--white);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .nav-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
        }

        .logo {
            font-family: 'Montserrat', sans-serif;
            font-size: 24px;
            font-weight: 700;
            color: var(--text-color);
            text-decoration: none;
        }

        .logo span {
            color: var(--primary-color);
        }

        .nav-menu {
            display: flex;
            list-style: none;
            gap: 20px;
            align-items: center;
        }

        .nav-menu a {
            text-decoration: none;
            color: var(--light-text);
            font-weight: 500;
            padding: 8px 15px;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        .nav-menu a:hover, .nav-menu a.active {
            color: var(--primary-color);
            background-color: rgba(90, 82, 226, 0.1);
        }

        .cart-icon {
            position: relative;
            display: flex;
            align-items: center;
        }

        .cart-icon i {
            font-size: 20px;
        }

        .cart-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background-color: var(--primary-color);
            color: var(--white);
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 12px;
            font-weight: 600;
        }

        /* Cart Container Styles */
        .cart-container {
            background: var(--white);
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin: 30px 0;
        }

        .cart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 1px solid var(--border-color);
        }

        .cart-title {
            font-size: 24px;
            font-weight: 600;
        }

        .cart-count {
            color: var(--light-text);
            font-size: 16px;
        }

        /* Table Styles */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        th {
            text-align: left;
            padding: 15px;
            font-weight: 600;
            color: var(--light-text);
            border-bottom: 2px solid var(--border-color);
        }

        td {
            padding: 20px 15px;
            border-bottom: 1px solid var(--border-color);
        }

        .product-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .product-img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
        }

        .product-name {
            font-weight: 500;
        }

        .price {
            font-weight: 600;
            color: var(--primary-color);
        }

        /* Quantity Control Styles */
        .quantity-control {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .quantity-input {
            width: 60px;
            padding: 8px;
            border: 1px solid var(--border-color);
            border-radius: 5px;
            text-align: center;
        }

        .action-button {
            padding: 8px 15px;
            background-color: var(--primary-color);
            color: var(--white);
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .action-button:hover {
            background-color: var(--secondary-color);
        }

        .remove-button {
            padding: 8px 15px;
            background-color: #ff4444;
            color: var(--white);
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .remove-button:hover {
            background-color: #cc0000;
        }

        /* Cart Summary Styles */
        .cart-summary {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-top: 30px;
        }

        .promo-code, .order-summary {
            background: var(--white);
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .promo-code h3, .order-summary h3 {
            margin-bottom: 15px;
            font-size: 18px;
        }

        .promo-input {
            display: flex;
            gap: 10px;
            margin-bottom: 10px;
        }

        .promo-input input {
            flex: 1;
            padding: 10px;
            border: 1px solid var(--border-color);
            border-radius: 5px;
        }

        .promo-input button {
            padding: 10px 20px;
            background-color: var(--primary-color);
            color: var(--white);
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .promo-error {
            color: var(--error-color);
            font-size: 14px;
            margin-top: 5px;
        }

        .promo-success {
            color: var(--success-color);
            font-size: 14px;
            margin-top: 5px;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding: 10px 0;
            border-bottom: 1px solid var(--border-color);
        }

        .summary-total {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
            font-size: 18px;
            font-weight: 600;
        }

        .checkout-btn {
            width: 100%;
            padding: 15px;
            background-color: var(--primary-color);
            color: var(--white);
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            margin-top: 20px;
            transition: background-color 0.3s ease;
        }

        .checkout-btn:hover {
            background-color: var(--secondary-color);
        }

        /* Empty Cart Styles */
        .empty-cart {
            text-align: center;
            padding: 40px;
            color: var(--light-text);
        }

        .empty-cart a {
            color: var(--primary-color);
            text-decoration: none;
        }

        .empty-cart a:hover {
            text-decoration: underline;
        }

        .payment-container {
            background: var(--white);
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin: 30px 0;
            display: none; /* Initially hidden, shown via JS */
        }

        .payment-header {
            margin-bottom: 25px;
        }

        .payment-header h2 {
            font-size: 22px;
            margin-bottom: 10px;
        }

        .payment-header p {
            color: var(--light-text);
        }

        .payment-methods {
            display: flex;
            gap: 15px;
            margin-bottom: 25px;
        }

        .payment-method {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 15px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .payment-method img {
            width: 60px;
            height: auto;
            margin-bottom: 10px;
        }

        .payment-method.selected {
            border-color: var(--primary-color);
            background-color: rgba(90, 82, 226, 0.05);
        }

        .payment-form {
            padding: 20px 0;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
        }

        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid var(--border-color);
            border-radius: 5px;
            font-size: 16px;
        }

        .form-row {
            display: flex;
            gap: 15px;
        }

        .form-row .form-group {
            flex: 1;
        }

        .submit-button {
            width: 100%;
            padding: 15px;
            background-color: var(--primary-color);
            color: var(--white);
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            margin-top: 10px;
            transition: background-color 0.3s ease;
        }

        .submit-button:hover {
            background-color: var(--secondary-color);
        }

        .secure-badge {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 20px;
            color: var(--light-text);
            font-size: 14px;
        }

        .payment-status {
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
            display: none;
            text-align: center;
        }

        .payment-status.success {
            background-color: rgba(46, 125, 50, 0.1);
            color: var(--success-color);
        }

        .payment-status.error {
            background-color: rgba(198, 40, 40, 0.1);
            color: var(--error-color);
        }

        /* Footer Styles */
        footer {
            background-color: var(--white);
            padding: 30px 0;
            margin-top: 50px;
            border-top: 1px solid var(--border-color);
        }

        .footer-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .footer-links {
            display: flex;
            gap: 20px;
        }

        .footer-links a {
            color: var(--light-text);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .footer-links a:hover {
            color: var(--primary-color);
        }

        .copyright {
            color: var(--light-text);
            font-size: 14px;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .cart-summary {
                grid-template-columns: 1fr;
            }

            .nav-container {
                flex-direction: column;
                gap: 15px;
            }

            .nav-menu {
                flex-wrap: wrap;
                justify-content: center;
            }

            .product-info {
                flex-direction: column;
                text-align: center;
            }

            .footer-content {
                flex-direction: column;
                gap: 20px;
                text-align: center;
            }

            .footer-links {
                flex-wrap: wrap;
                justify-content: center;
            }

            .form-row {
                flex-direction: column;
                gap: 0;
            }
            
            .payment-methods {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="container nav-container">
            <a href="userpage.php" class="logo">A & <span>B</span> STORE</a>
            <ul class="nav-menu">
                <li><a href="userpage.php">Home</a></li>
                <li><a href="shop.php">Shop</a></li>
                <li>
                    <a href="cart.php" class="cart-icon active">
                        <i class="fas fa-shopping-cart"></i>
                        <?php if ($cart_count > 0): ?>
                            <span class="cart-badge"><?php echo $cart_count; ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <li><a href="account.php">Account</a></li>
            </ul>
        </div>
    </header>
    
    <div class="page-wrapper">
        <div class="content-wrapper">
            <div class="container">
                <h1>Your Shopping Cart</h1>
                
                <?php if (isset($_SESSION['error_message'])): ?>
                    <div style="color: red; margin-bottom: 20px; text-align: center;">
                        <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
                    </div>
                <?php endif; ?>
                
                <div class="cart-container">
                    <div class="cart-header">
                        <div class="cart-title">Shopping Bag</div>
                        <div class="cart-count"><?php echo isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0; ?> Items</div>
                    </div>
                    
                    <table>
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Total</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="cart-items">
                            <?php
                            $totalPrice = 0;
                            if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0) {
                                foreach ($_SESSION['cart'] as $item) {
                                    $productId = $item['id'];
                                    $quantity = $item['quantity'];

                                    // Use prepared statement to fetch product details
                                    $sql = "SELECT name, price, image FROM beverages WHERE id = ?";
                                    $stmt = $conn->prepare($sql);
                                    $stmt->bind_param("i", $productId);
                                    $stmt->execute();
                                    $result = $stmt->get_result();
                                    $row = $result->fetch_assoc();

                                    $productName = htmlspecialchars($row['name']);
                                    $productPrice = $row['price'];
                                    // Construct image path - all images are in img folder
                                    $productImage = !empty($row['image']) ? 'img/' . htmlspecialchars($row['image']) : 'img/placeholder.jpg';
                                    $itemTotal = $productPrice * $quantity;
                                    $totalPrice += $itemTotal;

                                    echo "
                                    <tr>
                                        <td>
                                            <div class='product-info'>
                                                <img src='$productImage' alt='$productName' class='product-img'>
                                                <div class='product-name'>$productName</div>
                                            </div>
                                        </td>
                                        <td class='price'>Rs $productPrice</td>
                                        <td>
                                            <form method='post' action='cart.php' class='quantity-control'>
                                                <input type='hidden' name='action' value='update'>
                                                <input type='hidden' name='product_id' value='$productId'>
                                                <input type='number' name='quantity' value='$quantity' min='1' class='quantity-input'>
                                                <button type='submit' class='action-button'>Update</button>
                                            </form>
                                        </td>
                                        <td class='price'>Rs $itemTotal</td>
                                        <td>
                                            <form method='post' action='cart.php'>
                                                <input type='hidden' name='action' value='remove'>
                                                <input type='hidden' name='product_id' value='$productId'>
                                                <button type='submit' class='remove-button'>Remove</button>
                                            </form>
                                        </td>
                                    </tr>";

                                    $stmt->close();
                                }
                            } else {
                                echo "<tr><td colspan='5'><div class='empty-cart'>Your cart is empty. <a href='shop.php'>Continue shopping</a></div></td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                    
                    <?php if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
                    <div class="cart-summary">
                        <div class="promo-code">
                            <h3>Promo Code</h3>
                            <p>Enter your coupon code if you have one.</p>
                            <form method="post" action="cart.php">
                                <div class="promo-input">
                                    <input type="text" name="promo_code" placeholder="Enter promo code" value="<?php echo isset($_POST['promo_code']) ? htmlspecialchars($_POST['promo_code']) : ''; ?>">
                                    <button type="submit">Apply</button>
                                </div>
                                <?php if ($discountError): ?>
                                    <div class="promo-error"><?php echo $discountError; ?></div>
                                <?php elseif ($discountApplied): ?>
                                    <div class="promo-success">
                                        Promo code applied! 25% discount 
                                        <a href="cart.php?remove_discount=1" class="remove-discount">(Remove)</a>
                                    </div>
                                <?php endif; ?>
                            </form>
                        </div>
                        
                        <div class="order-summary">
                            <h3>Order Summary</h3>
                            <div class="summary-item">
                                <span>Subtotal</span>
                                <span>Rs <?php echo number_format($totalPrice, 2); ?></span>
                            </div>
                            <?php if ($discountApplied): ?>
                            <div class="summary-item">
                                <span>Discount (25%)</span>
                                <span>-Rs <?php echo number_format($discountAmount, 2); ?></span>
                            </div>
                            <?php endif; ?>
                            <div class="summary-item">
                                <span>Shipping</span>
                                <span>Free</span>
                            </div>
                            <div class="summary-total">
                                <span>Total</span>
                                <span>
                                    Rs <?php echo number_format($totalAfterDiscount, 2); ?>
                                    <?php if ($discountApplied): ?>
                                        <span class="discount-badge">
                                            <i class="fas fa-tag"></i>25% OFF
                                        </span>
                                    <?php endif; ?>
                                </span>
                            </div>
                            <button id="show-payment-form" class="checkout-btn">Proceed to Checkout</button>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                
                <div id="payment-container" class="payment-container">
                    <div class="payment-header">
                        <h2>Payment Information</h2>
                        <p>Complete your purchase by providing your payment details</p>
                    </div>

                    <div class="payment-methods">
                        <div class="payment-method" data-method="card">
                            <img src="img/visa.png" alt="Visa">
                            <p>Credit/Debit Card</p>
                        </div>
                        <div class="payment-method" data-method="khalti">
                            <img src="img/khalti.png" alt="Khalti">
                            <p>Khalti</p>
                        </div>
                        <div class="payment-method" data-method="esewa">
                            <img src="img/esewa.png" alt="eSewa">
                            <p>eSewa</p>
                        </div>
                    </div>

                    <form id="payment-form" class="payment-form" method="post" action="cart.php">
                        <div class="form-group">
                            <label for="name">Full Name</label>
                            <input type="text" id="name" name="name" required>
                        </div>

                        <div class="form-group">
                            <label for="address">Delivery Address</label>
                            <input type="text" id="address" name="address" required>
                        </div>

                        <div class="form-group card-details">
                            <label for="card_number">Card Number</label>
                            <input type="text" id="card_number" name="card_number" placeholder="1234 5678 9012 3456" required>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="expiration_date">Expiry Date</label>
                                <input type="text" id="expiration_date" name="expiration_date" placeholder="MM/YY" required>
                            </div>
                            <div class="form-group">
                                <label for="cvv">CVV</label>
                                <input type="text" id="cvv" name="cvv" placeholder="123" required>
                            </div>
                        </div>

                        <button type="submit" class="submit-button">Complete Purchase</button>

                        <div class="secure-badge">
                            <i class="fas fa-lock"></i> Your payment information is secure
                        </div>
                    </form>

                    <div id="payment-status" class="payment-status"></div>
                </div>
            </div>
        </div>
        
        <footer>
            <div class="container">
                <div class="footer-content">
                    <div class="footer-links">
                        <a href="services.php">Our Services</a>
                        <a href="contact.php">Contact</a>
                        <a href="privacy-policy.php">Privacy Policy</a>
                        <a href="terms-conditions.php">Terms & Conditions</a>
                    </div>
                    <div class="copyright">
                        <p>© 2024 A & B STORE. All rights reserved.</p>
                    </div>
                </div>
            </div>
        </footer>
    </div>
    
    <script>
        document.getElementById('show-payment-form').addEventListener('click', function() {
            document.getElementById('payment-container').style.display = 'block';
            document.getElementById('payment-container').scrollIntoView({ behavior: 'smooth' });
        });
        
        // Payment method selection
        const paymentMethods = document.querySelectorAll('.payment-method');
        paymentMethods.forEach(method => {
            method.addEventListener('click', () => {
                paymentMethods.forEach(m => m.classList.remove('selected'));
                method.classList.add('selected');
                const paymentType = method.dataset.method;
                updatePaymentForm(paymentType);
            });
        });

        function updatePaymentForm(paymentType) {
            const cardDetails = document.querySelector('.card-details');
            const expiry = document.querySelector('input[name="expiration_date"]').parentElement;
            const cvv = document.querySelector('input[name="cvv"]').parentElement;
            if (paymentType === 'card') {
                cardDetails.style.display = 'block';
                expiry.style.display = 'block';
                cvv.style.display = 'block';
            } else {
                cardDetails.style.display = 'none';
                expiry.style.display = 'none';
                cvv.style.display = 'none';
            }
        }

        // Format card number input
        document.getElementById('card_number').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\s+/g, '');
            if (value.length > 0) {
                value = value.match(new RegExp('.{1,4}', 'g')).join(' ');
            }
            e.target.value = value;
        });

        // Format expiry date input
        document.getElementById('expiration_date').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 2) {
                value = value.substring(0, 2) + '/' + value.substring(2, 4);
            }
            e.target.value = value;
        });
    </script>
</body>
</html>
?>