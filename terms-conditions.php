<?php
session_start();
include('db.php');
if (!isset($_SESSION['login_user'])) {
    header("location: login.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms & Conditions - A & B STORE</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #5a52e2;
            --secondary-color: #ff6b6b;
            --text-color: #333;
            --light-text: #555;
            --lighter-text: #777;
            --border-color: #eee;
            --bg-color: #fff;
            --light-bg: #f8f8f8;
            --dark-bg: #333;
            --shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            color: var(--text-color);
            background-color: var(--light-bg);
            line-height: 1.6;
            padding-bottom: 80px; /* Space for terms acceptance bar */
        }
        
        .container {
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        /* Consistent Header Styles */
        header {
            background-color: var(--bg-color);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 100;
            padding: 10px 0;
        }

        .nav-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            text-decoration: none;
            color: var(--text-color);
            font-size: 24px;
            font-weight: 700;
            font-family: 'Montserrat', sans-serif;
            letter-spacing: 1px;
        }

        .logo span {
            color: var(--primary-color);
        }

        .nav-menu {
            list-style: none;
            display: flex;
            margin: 0;
            padding: 0;
        }

        .nav-menu li {
            margin-left: 20px;
        }

        .nav-menu a {
            text-decoration: none;
            color: var(--light-text);
            padding: 10px 15px;
            border-radius: 5px;
            transition: var(--transition);
            font-weight: 500;
        }

        .nav-menu a:hover {
            background-color: #f2f2f2;
            color: var(--text-color);
        }

        .nav-menu a.active {
            color: var(--primary-color);
        }

        /* Search Styles */
        .search-container {
            position: relative;
            display: flex;
            align-items: center;
        }

        .search-input {
            padding: 10px 15px;
            border-radius: 6px;
            border: 1px solid var(--border-color);
            width: 200px;
            font-family: 'Poppins', sans-serif;
            transition: var(--transition);
        }

        .search-input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(90, 82, 226, 0.1);
        }

        .search-button {
            padding: 10px 20px;
            border-radius: 6px;
            background-color: var(--primary-color);
            color: white;
            border: none;
            cursor: pointer;
            font-weight: 500;
            transition: var(--transition);
            margin-left: 10px;
        }

        .search-button:hover {
            background-color: #4a42d2;
        }

        /* Policy Section Styles */
        .policy-section {
            background-color: var(--bg-color);
            padding: 40px;
            border-radius: 8px;
            box-shadow: var(--shadow);
            margin: 30px auto;
        }

        .policy-section h2 {
            color: var(--text-color);
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid var(--border-color);
            font-size: 28px;
        }

        .policy-section h3 {
            color: var(--text-color);
            margin-top: 30px;
            margin-bottom: 15px;
            font-size: 20px;
        }

        .policy-section p {
            margin-bottom: 15px;
        }

        .policy-section ul, .policy-section ol {
            margin-bottom: 20px;
            padding-left: 20px;
        }

        .policy-section li {
            margin-bottom: 10px;
        }

        .policy-section a {
            color: var(--primary-color);
            text-decoration: none;
            transition: var(--transition);
        }

        .policy-section a:hover {
            text-decoration: underline;
        }

        .last-updated {
            font-style: italic;
            color: var(--lighter-text);
            margin-top: 40px;
            font-size: 14px;
            text-align: right;
        }

        /* Terms Acceptance Banner */
        .terms-acceptance {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            background-color: rgba(51, 51, 51, 0.95);
            color: white;
            padding: 15px 0;
            z-index: 99;
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(5px);
        }

        .terms-acceptance-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }

        .terms-message {
            flex: 1;
            margin-right: 20px;
            font-size: 15px;
        }

        .terms-buttons {
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 10px 20px;
            border-radius: 4px;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            border: none;
            font-size: 14px;
            min-width: 100px;
            text-align: center;
        }

        .btn-accept {
            background-color: #4CAF50;
            color: white;
        }

        .btn-accept:hover {
            background-color: #3e8e41;
            transform: translateY(-2px);
        }

        .btn-decline {
            background-color: #f44336;
            color: white;
        }

        .btn-decline:hover {
            background-color: #d32f2f;
            transform: translateY(-2px);
        }

        /* Consistent Footer Styles */
        footer {
            background-color: var(--bg-color);
            padding: 25px 0;
            margin-top: 40px;
            border-top: 1px solid var(--border-color);
        }

        .footer-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .footer-links a {
            color: var(--light-text);
            text-decoration: none;
            margin-right: 20px;
            font-size: 14px;
            transition: var(--transition);
        }

        .footer-links a:hover {
            color: var(--primary-color);
        }

        .copyright {
            color: var(--lighter-text);
            font-size: 14px;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .nav-container {
                flex-direction: column;
                padding: 15px;
            }
            
            .nav-menu {
                margin-top: 15px;
                flex-wrap: wrap;
                justify-content: center;
            }
            
            .nav-menu li {
                margin: 5px;
            }
            
            .search-container {
                margin-top: 15px;
                width: 100%;
            }
            
            .search-input {
                width: 100%;
            }

            .footer-content {
                flex-direction: column;
                text-align: center;
            }
            
            .footer-links {
                margin-bottom: 15px;
            }
            
            .footer-links a {
                display: inline-block;
                margin: 0 10px 10px;
            }

            .terms-acceptance-container {
                flex-direction: column;
                text-align: center;
            }
            
            .terms-message {
                margin-right: 0;
                margin-bottom: 15px;
            }
            
            .terms-buttons {
                justify-content: center;
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
                <li><a href="cart.php">Cart</a></li>
                <li><a href="account.php">Account</a></li>
                <li><a href="terms-conditions.php" class="active">Terms</a></li>
            </ul>
            <div class="search-container">
                <form method="GET" action="shop.php">
                    <input type="text" id="searchInput" name="search" class="search-input" placeholder="Search beverages..." autocomplete="off">
                    <button type="submit" class="search-button">Search</button>
                </form>
            </div>
        </div>
    </header>

    <div class="container">
        <div class="policy-section">
            <h2>Terms and Conditions</h2>
            
            <p>Welcome to A & B STORE. By accessing or using our website, you agree to be bound by these Terms and Conditions. Please read them carefully before making any purchase or using our services.</p>
            
            <h3>1. Account Registration</h3>
            <p>When you create an account with us, you must provide accurate, complete, and up-to-date information. You are responsible for maintaining the confidentiality of your account credentials and for all activities that occur under your account.</p>
            
            <h3>2. Products and Pricing</h3>
            <p>All products are subject to availability. We reserve the right to discontinue any product at any time. Prices for products are subject to change without notice.</p>
            
            <h3>3. Orders and Payment</h3>
            <p>When you place an order through our website, you are making an offer to purchase the products in your order. We reserve the right to accept or decline your order for any reason.</p>
            
            <h3>4. Shipping and Delivery</h3>
            <p>We aim to deliver your products within the estimated delivery times indicated on our website. However, delivery times are not guaranteed and may vary depending on your location.</p>
            
            <h3>5. Returns and Refunds</h3>
            <p>We accept returns of unused and undamaged products within 30 days of delivery. To initiate a return, please contact our customer service team.</p>
            
            <h3>6. Intellectual Property</h3>
            <p>All content on our website is the property of A & B STORE or its content suppliers and is protected by copyright, trademark, and other intellectual property laws.</p>
            
            <h3>7. User Content</h3>
            <p>By submitting reviews or other content to our website, you grant A & B STORE a non-exclusive, royalty-free right to use, reproduce, modify, and display such content.</p>
            
            <h3>8. Limitation of Liability</h3>
            <p>To the fullest extent permitted by law, A & B STORE shall not be liable for any indirect, incidental, special, consequential, or punitive damages resulting from your use of our website.</p>
            
            <h3>9. Governing Law</h3>
            <p>These Terms and Conditions shall be governed by and construed in accordance with the laws of Nepal.</p>
            
            <h3>10. Changes to Terms</h3>
            <p>We reserve the right to modify these Terms and Conditions at any time. Changes will be effective immediately upon posting on our website.</p>
            
            <h3>11. Contact Information</h3>
            <p>If you have any questions about these Terms and Conditions, please contact us at <a href="mailto:legal@abstore.com">legal@abstore.com</a>.</p>
            
            <p class="last-updated">Last Updated: March 1, 2025</p>
        </div>
    </div>

    <!-- Terms Acceptance Banner -->
    <div class="terms-acceptance" id="termsAcceptance">
        <div class="container terms-acceptance-container">
            <div class="terms-message">
                By using our website, you agree to our Terms and Conditions. Please review them carefully before proceeding.
            </div>
            <div class="terms-buttons">
                <button class="btn btn-accept" onclick="acceptTerms()">Accept</button>
                <button class="btn btn-decline" onclick="declineTerms()">Decline</button>
            </div>
        </div>
    </div>

    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-links">
                    <a href="services.php">Our Services</a>
                    <a href="contact.php">Contact Us</a>
                    <a href="privacy-policy.php">Privacy Policy</a>
                    <a href="terms-conditions.php">Terms & Conditions</a>
                </div>
                <div class="copyright">
                    <p>&copy; 2025 A & B STORE. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <script>
        function acceptTerms() {
            // Set a cookie to remember acceptance (expires in 1 year)
            document.cookie = "terms_accepted=true; max-age=" + 60*60*24*365 + "; path=/";
            
            // Hide the terms acceptance banner
            document.getElementById('termsAcceptance').style.display = 'none';
            
            // Show acceptance message
            alert('Thank you for accepting our Terms and Conditions.');
        }

        function declineTerms() {
            // Redirect to logout page
            window.location.href = 'logout.php';
        }

        // Check if terms have already been accepted
        document.addEventListener('DOMContentLoaded', function() {
            if (document.cookie.split(';').some((item) => item.trim().startsWith('terms_accepted='))) {
                document.getElementById('termsAcceptance').style.display = 'none';
            }
        });
    </script>
</body>
</html>