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
    <title>Privacy Policy - A & B STORE</title>
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
        }
        
        .container {
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        /* Header Styles */
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
            transition: all 0.3s ease;
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
            padding: 8px 15px;
            margin-right: 10px;
            border-radius: 6px;
            border: 1px solid #ddd;
            width: 200px;
            font-family: 'Poppins', sans-serif;
        }
        
        .search-button {
            padding: 8px 15px;
            border-radius: 6px;
            background-color: var(--primary-color);
            color: white;
            border: none;
            cursor: pointer;
            font-weight: 500;
            transition: background-color 0.3s ease;
        }
        
        .search-button:hover {
            background-color: #4a42d2;
        }
        
        /* Policy Content Styles */
        .policy-section {
            background-color: var(--bg-color);
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
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
            color: #444;
            margin-top: 30px;
            margin-bottom: 15px;
            font-size: 22px;
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
            transition: color 0.3s ease;
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
        
        /* Footer Styles */
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
            transition: color 0.2s;
        }
        
        .footer-links a:hover {
            color: var(--primary-color);
        }
        
        .copyright {
            color: var(--lighter-text);
            font-size: 14px;
        }
        
        /* Responsive Styles */
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
            
            .policy-section {
                padding: 25px;
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
                <li><a href="privacy-policy.php" class="active">Privacy Policy</a></li>
            </ul>
            <div class="search-container">
                <form method="GET" action="shop.php" style="display: flex; align-items: center;">
                    <input type="text" id="searchInput" name="search" class="search-input" placeholder="Search beverages..." autocomplete="off">
                    <button type="submit" class="search-button">Search</button>
                </form>
            </div>
        </div>
    </header>

    <div class="container">
        <div class="policy-section">
            <h2>Privacy Policy</h2>
            
            <p>At A & B STORE, we are committed to protecting your privacy and ensuring that your personal information is handled in a secure and responsible manner. This Privacy Policy outlines the types of information we collect, how we use it, and the measures we take to safeguard your information.</p>
            
            <h3>1. Information We Collect</h3>
            <p>We may collect the following types of information:</p>
            <ul>
                <li><strong>Personal Information:</strong> This includes your name, email address, phone number, shipping and billing addresses, and payment information when you create an account, place an order, or contact us.</li>
                <li><strong>Usage Information:</strong> We collect information about how you interact with our website, including the pages you visit, the products you view, and your browsing patterns.</li>
                <li><strong>Device Information:</strong> We may collect information about the device you use to access our website, including your IP address, browser type, and operating system.</li>
            </ul>
            
            <h3>2. How We Use Your Information</h3>
            <p>We use the information we collect for the following purposes:</p>
            <ol>
                <li>To process and fulfill your orders</li>
                <li>To communicate with you about your orders, account, or customer service inquiries</li>
                <li>To personalize your shopping experience and provide product recommendations</li>
                <li>To improve our website, products, and services</li>
                <li>To send you marketing communications (if you've opted in)</li>
                <li>To detect and prevent fraud or unauthorized access</li>
            </ol>
            
            <h3>3. Cookies and Tracking Technologies</h3>
            <p>We use cookies and similar tracking technologies to enhance your browsing experience, analyze website traffic, and personalize content. You can control cookies through your browser settings, but disabling them may limit certain features of our website.</p>
            
            <h3>4. Information Sharing</h3>
            <p>We do not sell or rent your personal information to third parties. We may share your information with:</p>
            <ul>
                <li>Service providers who help us operate our business (payment processors, shipping companies, etc.)</li>
                <li>Law enforcement or government authorities when required by law</li>
                <li>Third parties in the event of a merger, acquisition, or business transfer</li>
            </ul>
            
            <h3>5. Data Security</h3>
            <p>We implement appropriate technical and organizational measures to protect your personal information against unauthorized access, accidental loss, or destruction. We use industry-standard encryption for sensitive data and regularly review our security practices.</p>
            
            <h3>6. Your Rights</h3>
            <p>Depending on your location, you may have the right to:</p>
            <ul>
                <li>Access the personal information we hold about you</li>
                <li>Correct inaccurate or incomplete information</li>
                <li>Request deletion of your personal information</li>
                <li>Object to or restrict certain processing activities</li>
                <li>Data portability</li>
                <li>Withdraw consent for optional processing activities</li>
            </ul>
            
            <h3>7. Children's Privacy</h3>
            <p>Our website is not intended for children under 13 years of age, and we do not knowingly collect personal information from children under 13. If we learn that we have collected personal information from a child under 13, we will promptly delete that information.</p>
            
            <h3>8. Changes to This Policy</h3>
            <p>We may update this Privacy Policy periodically. The "Last Updated" date at the bottom of this page indicates when the policy was last revised. We encourage you to review this policy regularly.</p>
            
            <h3>9. Contact Us</h3>
            <p>If you have any questions about this Privacy Policy or our privacy practices, please contact us at:</p>
            <p>
                <strong>Email:</strong> <a href="mailto:privacy@abstore.com">privacy@abstore.com</a><br>
                <strong>Phone:</strong> +977 9744385533<br>
                <strong>Address:</strong> manamaiju,Kathmandu,Nepal</p>
            
            <p class="last-updated">Last Updated: March 1, 2025</p>
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
                    <p>&copy; 2024 A & B STORE. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>