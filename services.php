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
    <title>Our Services - A & B STORE</title>
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

        /* Services Header Section */
        .services-header {
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('https://www.shaap.org.uk/images/backgrounds/bg-bar.jpg');
            background-size: cover;
            background-position: center;
            color: #fff;
            padding: 100px 0;
            text-align: center;
            margin-bottom: 60px;
        }

        .services-header h1 {
            font-size: 42px;
            margin-bottom: 20px;
            font-weight: 700;
        }

        .services-header p {
            font-size: 18px;
            max-width: 800px;
            margin: 0 auto;
            line-height: 1.6;
            opacity: 0.9;
        }

        /* Services Section */
        .services-section {
            padding: 60px 0;
        }

        .section-title {
            text-align: center;
            margin-bottom: 50px;
        }

        .section-title h2 {
            font-size: 32px;
            position: relative;
            display: inline-block;
            padding-bottom: 15px;
        }

        .section-title h2::after {
            content: '';
            position: absolute;
            width: 50%;
            height: 3px;
            background-color: var(--primary-color);
            bottom: 0;
            left: 25%;
        }

        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }

        .service-card {
            background: var(--bg-color);
            border-radius: 8px;
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: var(--transition);
        }

        .service-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
        }

        .service-image {
            height: 200px;
            width: 100%;
            object-fit: cover;
        }

        .service-content {
            padding: 25px;
        }

        .service-content h3 {
            font-size: 22px;
            margin-bottom: 15px;
            color: var(--text-color);
        }

        .service-content p {
            color: var(--light-text);
            margin-bottom: 20px;
            line-height: 1.6;
        }

        .service-link {
            display: inline-block;
            padding: 10px 20px;
            background-color: var(--primary-color);
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            font-weight: 500;
            transition: var(--transition);
        }

        .service-link:hover {
            background-color: #4a42d2;
        }

        /* Why Choose Us Section */
        .why-choose-us {
            background-color: var(--light-bg);
            padding: 80px 0;
            margin-top: 60px;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
        }

        .feature-box {
            background: var(--bg-color);
            padding: 30px;
            border-radius: 8px;
            text-align: center;
            box-shadow: var(--shadow);
            transition: var(--transition);
        }

        .feature-box:hover {
            transform: translateY(-5px);
        }

        .feature-icon {
            font-size: 40px;
            color: var(--primary-color);
            margin-bottom: 20px;
        }

        .feature-box h3 {
            font-size: 20px;
            margin-bottom: 15px;
        }

        .feature-box p {
            color: var(--light-text);
            line-height: 1.6;
        }

        /* Consistent Footer Styles */
        footer {
            background-color: var(--bg-color);
            padding: 30px 0;
            margin-top: 60px;
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

            .services-header {
                padding: 80px 0;
            }
            
            .services-header h1 {
                font-size: 32px;
            }
            
            .services-header p {
                font-size: 16px;
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
                <li><a href="services.php" class="active">Services</a></li>
            </ul>
            <div class="search-container">
                <form method="GET" action="shop.php">
                    <input type="text" id="searchInput" name="search" class="search-input" placeholder="Search beverages..." autocomplete="off">
                    <button type="submit" class="search-button">Search</button>
                </form>
            </div>
        </div>
    </header>

    <!-- Services Header Section -->
    <div class="services-header">
        <div class="container">
            <h1>Our Premium Services</h1>
            <p>Discover our wide range of premium spirits, wines, and beverages, along with exceptional services tailored to meet your needs.</p>
        </div>
    </div>

    <!-- Services Section -->
    <section class="services-section">
        <div class="container">
            <div class="section-title">
                <h2>What We Offer</h2>
            </div>
            
            <div class="services-grid">
                <div class="service-card">
                    <img src="/api/placeholder/400/300" alt="Premium Selection" class="service-image">
                    <div class="service-content">
                        <h3>Premium Selection</h3>
                        <p>Access to a wide range of premium spirits, wines, and craft beers sourced from around the world.</p>
                        <a href="shop.php" class="service-link">Explore Products</a>
                    </div>
                </div>
                
                <div class="service-card">
                    <img src="/api/placeholder/400/300" alt="Home Delivery" class="service-image">
                    <div class="service-content">
                        <h3>Home Delivery</h3>
                        <p>Fast and secure delivery to your doorstep. Order online and enjoy our reliable delivery service.</p>
                        <a href="#" class="service-link">Learn More</a>
                    </div>
                </div>
                
                <div class="service-card">
                    <img src="/api/placeholder/400/300" alt="Tasting Events" class="service-image">
                    <div class="service-content">
                        <h3>Tasting Events</h3>
                        <p>Join our exclusive tasting events and discover new flavors with guidance from industry experts.</p>
                        <a href="#" class="service-link">View Schedule</a>
                    </div>
                </div>
                
                <div class="service-card">
                    <img src="/api/placeholder/400/300" alt="Subscription Boxes" class="service-image">
                    <div class="service-content">
                        <h3>Subscription Boxes</h3>
                        <p>Monthly curated boxes of premium spirits delivered to your door with tasting notes and pairing suggestions.</p>
                        <a href="#" class="service-link">Subscribe Now</a>
                    </div>
                </div>
                
                <div class="service-card">
                    <img src="/api/placeholder/400/300" alt="Gift Services" class="service-image">
                    <div class="service-content">
                        <h3>Gift Services</h3>
                        <p>Custom gift packages for special occasions with premium wrapping and personalized messages.</p>
                        <a href="#" class="service-link">Send a Gift</a>
                    </div>
                </div>
                
                <div class="service-card">
                    <img src="/api/placeholder/400/300" alt="Party Planning" class="service-image">
                    <div class="service-content">
                        <h3>Party Planning</h3>
                        <p>Complete beverage solutions for events and parties, including bartender service and equipment rental.</p>
                        <a href="#" class="service-link">Plan Your Event</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Why Choose Us Section -->
    <section class="why-choose-us">
        <div class="container">
            <div class="section-title">
                <h2>Why Choose Us</h2>
            </div>
            
            <div class="features-grid">
                <div class="feature-box">
                    <div class="feature-icon">
                        <i class="fas fa-award"></i>
                    </div>
                    <h3>Quality Guaranteed</h3>
                    <p>We source only the finest products from trusted suppliers to ensure authentic, premium quality beverages.</p>
                </div>
                
                <div class="feature-box">
                    <div class="feature-icon">
                        <i class="fas fa-truck"></i>
                    </div>
                    <h3>Fast Delivery</h3>
                    <p>Same-day delivery available for orders placed before 2 PM, with careful handling to ensure safe arrival.</p>
                </div>
                
                <div class="feature-box">
                    <div class="feature-icon">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <h3>Secure Transactions</h3>
                    <p>Our payment system is fully secure and encrypted to protect your personal and financial information.</p>
                </div>
                
                <div class="feature-box">
                    <div class="feature-icon">
                        <i class="fas fa-glass-cheers"></i>
                    </div>
                    <h3>Expert Advice</h3>
                    <p>Our knowledgeable staff is always ready to provide recommendations and guidance for your selections.</p>
                </div>
            </div>
        </div>
    </section>

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
        // Search functionality
        document.addEventListener('DOMContentLoaded', function() {
            const searchForm = document.querySelector('.search-container form');
            if (searchForm) {
                searchForm.addEventListener('submit', function(e) {
                    const searchInput = document.querySelector('.search-input');
                    if (searchInput.value.trim() === '') {
                        e.preventDefault();
                    }
                });
            }
        });
    </script>
</body>
</html>