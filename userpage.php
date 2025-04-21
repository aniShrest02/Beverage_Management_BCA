<?php
session_start();
include('db.php');
// if (!isset($_SESSION['login_user'])) {
//     header("location: login.php");
// }

// Initialize cart count
$cart_count = isset($_SESSION['cart']) && is_array($_SESSION['cart']) ? count($_SESSION['cart']) : 0;

// Function to get featured products based on most purchased items
function getFeaturedProducts($limit = 4) {
    global $conn; // Using the database connection from db.php

    // Updated SQL to count purchases by matching product_names with beverage name
    $sql = "SELECT b.*, COUNT(o.id) as purchase_count 
            FROM orders o
            JOIN beverages b ON o.product_names = b.name
            GROUP BY b.id, b.name
            ORDER BY purchase_count DESC
            LIMIT ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $result = $stmt->get_result();

    $featuredProducts = [];
    while ($row = $result->fetch_assoc()) {
        $featuredProducts[] = $row;
    }

    return $featuredProducts;
}

// Get featured products
$featuredProducts = getFeaturedProducts(4);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Page</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap');

        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f8f8;
            color: #333;
        }

        header {
            background-color: #fff;
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
            color: #333;
            font-size: 24px;
            font-weight: 700;
            font-family: 'Montserrat', sans-serif;
            letter-spacing: 1px;
        }

        .logo span {
            color: #5a52e2;
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
            color: #555;
            padding: 10px 15px;
            border-radius: 5px;
            transition: background-color 0.3s ease, color 0.3s ease;
            font-weight: 500;
            font-family: 'Poppins', sans-serif;
        }

        .nav-menu a:hover {
            background-color: #f2f2f2;
            color: #333;
        }

        .nav-menu a.active {
            color: #5a52e2;
        }

        /* Updated Cart Icon and Badge Styles */
        .cart-link {
            position: relative;
            display: inline-flex;
            align-items: center;
        }

        .cart-icon {
            font-size: 18px;
        }

        .cart-badge {
            display: none; /* Hidden by default */
            position: absolute;
            top: -10px;
            right: 0px;
            background-color: #5a52e2; /* Blue to match brand color */
            color: #fff;
            font-size: 11px;
            font-weight: 600;
            padding: 2px 8px;
            border-radius: 12px; /* Pill shape */
            text-align: center;
            line-height: 16px;
            min-width: 16px;
        }

        /* Show badge when there are items */
        .cart-link.has-items .cart-badge {
            display: inline-block;
        }

        .container {
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        /* Autocomplete styles */
        .search-container {
            position: relative;
            display: flex;
            align-items: center;
        }

        .autocomplete-items {
            position: absolute;
            border: 1px solid #ddd;
            border-top: none;
            z-index: 99;
            top: 100%;
            left: 0;
            right: 0;
            background-color: white;
            border-radius: 0 0 4px 4px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .autocomplete-items div {
            padding: 10px;
            cursor: pointer;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        .autocomplete-items div:hover {
            background-color: #f2f2f2;
        }

        .autocomplete-active {
            background-color: #e9e9e9 !important;
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
            background-color: #5a52e2;
            color: white;
            border: none;
            cursor: pointer;
            font-weight: 500;
            font-family: 'Poppins', sans-serif;
        }

        /* Hero Section with Video Background */
        .hero-section {
            position: relative;
            height: 80vh;
            min-height: 400px; /* Adjusted for better mobile compatibility */
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .hero-video {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
        }

        .hero-video video {
            width: 100%;
            height: 100%;
            object-fit: cover; /* Ensures video covers the container */
            object-position: center; /* Centers the video */
        }

        .video-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.4);
            z-index: 2;
        }

        .prompt {
            position: relative;
            z-index: 3;
            width: 100%;
            text-align: center;
            background: transparent;
            padding: 0;
        }

        .prompt h2 {
            font-size: 4rem;
            margin-bottom: 2rem;
            color: #fff;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
        }

        .button {
            display: inline-block;
            padding: 15px 30px;
            background-color: rgba(255,255,255,0.9);
            color: #ff6b6b;
            text-decoration: none;
            border-radius: 4px;
            transition: all 0.3s ease;
            font-weight: 500;
            font-size: 1.2rem;
        }

        .button:hover {
            background-color: #fff;
            transform: scale(1.05);
        }

        /* Featured Products Section */
        .featured-products {
            padding: 60px 0;
            background-color: #f9f9f9;
        }

        .section-title {
            text-align: center;
            font-size: 32px;
            margin-bottom: 40px;
            color: #333;
            position: relative;
            font-weight: 600;
        }

        .section-title:after {
            content: '';
            display: block;
            width: 80px;
            height: 3px;
            background-color: #5a52e2;
            margin: 15px auto 0;
            border-radius: 2px;
        }

        .no-products {
            text-align: center;
            padding: 30px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 30px;
            margin-bottom: 40px;
        }

        .product-card {
            background-color: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            position: relative;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .product-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: #5a52e2;
            color: #fff;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            z-index: 2;
        }

        .product-badge.new {
            background-color: #4CAF50;
        }

        .product-badge.sale {
            background-color: #ff6b6b;
        }

        .product-img {
            position: relative;
            height: 220px;
            overflow: hidden;
        }

        .product-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .product-card:hover .product-img img {
            transform: scale(1.05);
        }

        .product-actions {
            position: absolute;
            bottom: -40px;
            left: 0;
            width: 100%;
            display: flex;
            justify-content: center;
            gap: 15px;
            padding: 10px 0;
            background-color: rgba(255, 255, 255, 0.9);
            transition: bottom 0.3s ease;
        }

        .product-card:hover .product-actions {
            bottom: 0;
        }

        .product-actions a {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background-color: #fff;
            color: #5a52e2;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .product-actions a:hover {
            background-color: #5a52e2;
            color: #fff;
        }

        .product-details {
            padding: 20px;
        }

        .product-title {
            margin: 0 0 8px;
            font-size: 18px;
            font-weight: 600;
            color: #333;
        }

        .product-rating {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            color: #ffc107;
            font-size: 14px;
        }

        .product-rating span {
            margin-left: 5px;
            color: #777;
        }

        .product-price {
            font-size: 18px;
            font-weight: 600;
            color: #ff6b6b;
            margin-bottom: 15px;
        }

        .original-price {
            text-decoration: line-through;
            color: #999;
            margin-right: 8px;
            font-size: 14px;
        }

        .view-button {
            display: block;
            width: 100%;
            padding: 10px;
            background-color: #5a52e2;
            color: #fff;
            border-radius: 5px;
            text-align: center;
            text-decoration: none;
            font-weight: 500;
            transition: background-color 0.3s ease;
        }

        .view-button:hover {
            background-color: #4746b4;
        }

        .view-all-container {
            text-align: center;
            margin-top: 20px;
        }

        .view-all-button {
            display: inline-block;
            padding: 12px 30px;
            background-color: transparent;
            color: #5a52e2;
            border: 2px solid #5a52e2;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .view-all-button:hover {
            background-color: #5a52e2;
            color: #fff;
        }

        .testimonials {
            background-color: #f9f9f9;
            padding: 60px 0;
        }

        .testimonials h2 {
            text-align: center;
            font-size: 32px;
            margin-bottom: 40px;
            color: #333;
        }

        .testimonials-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 30px;
        }

        .testimonial-card {
            background-color: #fff;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            position: relative;
        }

        .testimonial-card:before {
            content: '"';
            position: absolute;
            top: 20px;
            left: 20px;
            font-size: 60px;
            color: #f0f0f0;
            font-family: Georgia, serif;
            z-index: 0;
        }

        .testimonial-content {
            position: relative;
            z-index: 1;
            font-style: italic;
            margin-bottom: 20px;
            color: #555;
            line-height: 1.6;
        }

        .testimonial-author {
            display: flex;
            align-items: center;
        }

        .author-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            overflow: hidden;
            margin-right: 15px;
        }

        .author-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .author-details h4 {
            margin: 0;
            font-size: 16px;
            color: #333;
        }

        .author-details p {
            margin: 5px 0 0;
            font-size: 14px;
            color: #777;
        }

        .contact-us-section {
            background-color: #f2f2f2;
            padding: 50px 0;
        }

        .contact-form label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
        }

        .contact-form input,
        .contact-form textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-bottom: 15px;
            font-family: inherit;
            font-size: 16px;
        }

        .contact-form button {
            padding: 12px 24px;
            background-color: blueviolet;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 500;
            transition: background-color 0.3s ease;
        }

        .contact-form button:hover {
            background-color: #5a52e2;
        }

        footer {
            background-color: #fff;
            padding: 25px 0;
            margin-top: 40px;
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
            color: #4361ee;
        }

        .copyright {
            color: #777;
            font-size: 14px;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .products-grid {
                grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            }
        }

        @media (max-width: 768px) {
            .nav-container {
                flex-direction: column;
                padding: 15px;
            }
            
            .nav-menu {
                margin-top: 15px;
            }
            
            .search-container {
                margin-top: 15px;
                width: 100%;
            }
            
            .search-input {
                width: 100%;
            }
            
            .hero-section {
                height: 60vh;
                min-height: 300px; /* Adjusted for smaller screens */
            }
            
            .prompt h2 {
                font-size: 2.5rem;
            }
            
            .button {
                padding: 12px 24px;
                font-size: 1rem;
            }

            .section-title {
                font-size: 28px;
            }

            .products-grid {
                grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
                gap: 20px;
            }

            .product-img {
                height: 180px;
            }

            .product-title {
                font-size: 16px;
            }

            .product-price {
                font-size: 16px;
            }
        }

        @media (max-width: 576px) {
            .products-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 15px;
            }

            .product-img {
                height: 160px;
            }

            .product-details {
                padding: 15px;
            }

            .hero-section {
                height: 50vh;
                min-height: 250px; /* Adjusted for very small screens */
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
                    <a href="cart.php" class="cart-link <?php echo $cart_count > 0 ? 'has-items' : ''; ?>">
                        <i class="fas fa-cart-shopping cart-icon"></i>
                        <span class="cart-badge"><?php echo $cart_count; ?></span>
                    </a>
                </li>
                <li><a href="account.php" class="active">Account</a></li>
            </ul>
            <div class="search-container">
                <form method="GET" action="shop.php" style="display: flex; align-items: center;">
                    <input type="text" id="searchInput" name="search" class="search-input" placeholder="Search beverages..." autocomplete="off">
                    <div id="autocompleteResults" class="autocomplete-items"></div>
                    <button type="submit" class="search-button">Search</button>
                </form>
            </div>
        </div>
    </header>

    <div class="hero-section">
        <div class="hero-video">
            <video autoplay loop muted playsinline>
                <source src="img/video - Trim.mp4" type="video/mp4">
            </video>
            <img src="img/fallback-hero-image.jpg" alt="Hero Fallback" style="width: 100%; height: 100%; object-fit: cover; display: none;" class="fallback-image">
            <div class="video-overlay"></div>
        </div>
        <div class="prompt">
            <div class="container">
                <h2>Do you want to get drunk?</h2>
                <a href="shop.php" class="button">Buy Now</a>
            </div>
        </div>
    </div>

    <section class="featured-products">
        <div class="container">
            <h2 class="section-title">Best Selling Products</h2>
            
            <?php if(empty($featuredProducts)): ?>
                <div class="no-products">
                    <p>No featured products available at the moment.</p>
                </div>
            <?php else: ?>
                <div class="products-grid">
                    <?php foreach($featuredProducts as $product): ?>
                        <div class="product-card">
                            <?php if($product['purchase_count'] > 10): ?>
                                <div class="product-badge">Popular</div>
                            <?php elseif(isset($product['created_at']) && strtotime($product['created_at']) > strtotime('-30 days')): ?>
                                <div class="product-badge new">New</div>
                            <?php elseif(isset($product['discount_price']) && $product['discount_price'] && $product['discount_price'] < $product['price']): ?>
                                <div class="product-badge sale">Sale</div>
                            <?php endif; ?>
                            
                            <div class="product-img">
                                <img src="<?php echo htmlspecialchars($product['image_url'] ?? '/api/placeholder/400/320'); ?>" alt="<?php echo htmlspecialchars($product['name'] ?? 'Product'); ?>">
                                <div class="product-actions">
                                    <a href="product.php?id=<?php echo $product['id']; ?>" class="quick-view" title="Quick View"><i class="fas fa-eye"></i></a>
                                    <a href="add-to-cart.php?id=<?php echo $product['id']; ?>" class="add-to-cart" title="Add to Cart"><i class="fas fa-cart-plus"></i></a>
                                </div>
                            </div>
                            
                            <div class="product-details">
                                <h3 class="product-title"><?php echo htmlspecialchars($product['name'] ?? 'Product Name'); ?></h3>
                                
                                <?php if(isset($product['rating'])): ?>
                                    <div class="product-rating">
                                        <?php
                                        $rating = $product['rating'];
                                        for($i = 1; $i <= 5; $i++) {
                                            if($i <= floor($rating)) {
                                                echo '<i class="fas fa-star"></i>';
                                            } elseif($i - 0.5 <= $rating) {
                                                echo '<i class="fas fa-star-half-alt"></i>';
                                            } else {
                                                echo '<i class="far fa-star"></i>';
                                            }
                                        }
                                        ?>
                                        <span>(<?php echo number_format($rating, 1); ?>)</span>
                                    </div>
                                <?php endif; ?>
                                
                                <p class="product-price">
                                    <?php if(isset($product['discount_price']) && $product['discount_price'] && $product['discount_price'] < $product['price']): ?>
                                        <span class="original-price">$<?php echo number_format($product['price'], 2); ?></span>
                                        $<?php echo number_format($product['discount_price'], 2); ?>
                                    <?php else: ?>
                                        $<?php echo number_format($product['price'] ?? 0, 2); ?>
                                    <?php endif; ?>
                                </p>
                                
                                <a href="product.php?id=<?php echo $product['id']; ?>" class="view-button">View Details</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="view-all-container">
                    <a href="shop.php" class="view-all-button">View All Products</a>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <section class="testimonials">
        <div class="container">
            <h2>What our customer says?</h2>
            <div class="testimonials-grid">
                <div class="testimonial-card">
                    <p class="testimonial-content">यो उत्पादन धेरै राम्रो छ।</p>
                    <div class="testimonial-author">
                        <div class="author-avatar">
                            <img src="img/anish.jpg" alt="अनिश श्रेष्ठ">
                        </div>
                        <div class="author-details">
                            <h4>अनिश श्रेष्ठ</h4>
                            <p>ग्राहक</p>
                        </div>
                    </div>
                </div>
                <div class="testimonial-card">
                    <p class="testimonial-content">The product is really good.</p>
                    <div class="testimonial-author">
                        <div class="author-avatar">
                            <img src="img/basanta.jpg" alt="बसन्त घिमिरे">
                        </div>
                        <div class="author-details">
                            <h4>बसन्त घिमिरे</h4>
                            <p>Customer</p>
                        </div>
                    </div>
                </div>
                <div class="testimonial-card">
                    <p class="testimonial-content">त्यो धेरै राम्रो उत्पाद हो।</p>
                    <div class="testimonial-author">
                        <div class="author-avatar">
                            <img src="img/dipin.jpg" alt="दीपिन नेउपाने">
                        </div>
                        <div class="author-details">
                            <h4>दीपिन नेउपाने</h4>
                            <p>ग्राहक</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="contact-us-section">
        <div class="container">
            <h2>Contact Us</h2>
            <form action="#" method="POST" class="contact-form">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" required>
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
                <label for="message">Message:</label>
                <textarea id="message" name="message" rows="4" required></textarea>
                <button type="submit">Send</button>
            </form>
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

    <script>
        // Add autocomplete functionality for search
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const autocompleteResults = document.getElementById('autocompleteResults');
            
            if (searchInput && autocompleteResults) {
                searchInput.addEventListener('input', function() {
                    const query = this.value.trim();
                    
                    if (query.length < 2) {
                        autocompleteResults.innerHTML = '';
                        return;
                    }
                    
                    // Fetch suggestions from server
                    fetch('search_suggestions.php?q=' + encodeURIComponent(query))
                        .then(response => response.json())
                        .then(data => {
                            autocompleteResults.innerHTML = '';
                            
                            data.forEach(item => {
                                const div = document.createElement('div');
                                div.textContent = item.name;
                                div.addEventListener('click', function() {
                                    searchInput.value = item.name;
                                    autocompleteResults.innerHTML = '';
                                    // Optional: Submit the form immediately
                                    // searchInput.closest('form').submit();
                                });
                                autocompleteResults.appendChild(div);
                            });
                        })
                        .catch(error => {
                            console.error('Error fetching search suggestions:', error);
                        });
                });
                
                // Hide suggestions when clicking outside
                document.addEventListener('click', function(e) {
                    if (e.target !== searchInput && e.target !== autocompleteResults) {
                        autocompleteResults.innerHTML = '';
                    }
                });
            }

            // Fallback image for video error
            const video = document.querySelector('video');
            const fallbackImage = document.querySelector('.fallback-image');
            if (video && fallbackImage) {
                video.addEventListener('error', function() {
                    fallbackImage.style.display = 'block';
                    video.style.display = 'none';
                });
            }
        });
    </script>
</body>
</html>