<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - A & B STORE</title>
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
            --shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
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
            margin-right: 10px;
            border-radius: 6px;
            border: 1px solid #ddd;
            width: 200px;
            font-family: 'Poppins', sans-serif;
            transition: var(--transition);
        }
        
        .search-input:focus {
            border-color: var(--primary-color);
            outline: none;
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
        }
        
        .search-button:hover {
            background-color: #4a42d2;
        }
        
        /* Contact Section Styles */
        .contact-section {
            padding: 60px 0;
            margin: 50px auto;
        }
        
        .section-header {
            text-align: center;
            margin-bottom: 50px;
        }
        
        .section-header h2 {
            font-size: 36px;
            color: var(--text-color);
            margin-bottom: 15px;
            font-weight: 700;
        }
        
        .section-header h2 span {
            color: var(--primary-color);
        }
        
        .section-header p {
            color: var(--light-text);
            max-width: 700px;
            margin: 0 auto;
            font-size: 16px;
        }
        
        .contact-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-bottom: 50px;
        }
        
        .contact-card {
            background-color: var(--bg-color);
            border-radius: 10px;
            padding: 30px;
            text-align: center;
            box-shadow: var(--shadow);
            transition: var(--transition);
            border-bottom: 3px solid var(--primary-color);
        }
        
        .contact-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
        
        .contact-icon {
            background-color: #f0f0ff;
            color: var(--primary-color);
            width: 70px;
            height: 70px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 24px;
        }
        
        .contact-card h3 {
            font-size: 20px;
            margin-bottom: 15px;
            color: var(--text-color);
        }
        
        .contact-card p {
            color: var(--light-text);
            margin-bottom: 10px;
        }
        
        /* Contact Form Styles */
        .form-container {
            background-color: var(--bg-color);
            border-radius: 10px;
            padding: 40px;
            box-shadow: var(--shadow);
            margin-bottom: 50px;
        }
        
        .form-container h3 {
            font-size: 24px;
            margin-bottom: 25px;
            color: var(--text-color);
        }
        
        .form-row {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .form-group {
            flex: 1;
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--text-color);
        }
        
        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-family: 'Poppins', sans-serif;
            font-size: 15px;
            transition: var(--transition);
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(90, 82, 226, 0.1);
        }
        
        textarea.form-control {
            height: 150px;
            resize: vertical;
        }
        
        .submit-btn {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 6px;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            font-size: 16px;
            display: inline-block;
        }
        
        .submit-btn:hover {
            background-color: #4a43c9;
            transform: translateY(-2px);
        }
        
        /* Map Styles */
        .map-container {
            height: 400px;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: var(--shadow);
        }
        
        .map-container iframe {
            width: 100%;
            height: 100%;
            border: none;
        }
        
        /* Footer Styles */
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
            
            .form-row {
                flex-direction: column;
                gap: 0;
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
            
            .section-header h2 {
                font-size: 28px;
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
                <li><a href="contact.php" class="active">Contact</a></li>
            </ul>
            <div class="search-container">
                <form method="GET" action="shop.php">
                    <input type="text" id="searchInput" name="search" class="search-input" placeholder="Search beverages..." autocomplete="off">
                    <button type="submit" class="search-button">Search</button>
                </form>
            </div>
        </div>
    </header>

    <!-- Contact Section -->
    <section class="contact-section container">
        <div class="section-header">
            <h2>Get in <span>Touch</span></h2>
            <p>We'd love to hear from you. Whether you have a question about our products, services, or anything else, our team is ready to answer all your queries.</p>
        </div>
        
        <div class="contact-grid">
            <div class="contact-card">
                <div class="contact-icon">
                    <i class="fas fa-map-marker-alt"></i>
                </div>
                <h3>Visit Us</h3>
                <p>Manamaiju, Kathmandu</p>
                <p>Sun-Fri: 9am-6pm</p>
                <p>Sat: 10am-4pm</p>
            </div>
            
            <div class="contact-card">
                <div class="contact-icon">
                    <i class="fas fa-phone"></i>
                </div>
                <h3>Call Us</h3>
                <p>+977-9744385533</p>
                <p>+977-9841234567</p>
                <p>Customer Support: 24/7</p>
            </div>
            
            <div class="contact-card">
                <div class="contact-icon">
                    <i class="fas fa-envelope"></i>
                </div>
                <h3>Email Us</h3>
                <p>info@aandBstore.com</p>
                <p>support@aandBstore.com</p>
                <p>sales@aandBstore.com</p>
            </div>
        </div>
        
        <div class="form-container">
            <h3>Send Us a Message</h3>
            <form action="process_contact.php" method="POST">
                <div class="form-row">
                    <div class="form-group">
                        <label for="name">Your Name</label>
                        <input type="text" id="name" name="name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Your Email</label>
                        <input type="email" id="email" name="email" class="form-control" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone" class="form-control">
                </div>
                <div class="form-group">
                    <label for="subject">Subject</label>
                    <input type="text" id="subject" name="subject" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="message">Your Message</label>
                    <textarea id="message" name="message" class="form-control" required></textarea>
                </div>
                <button type="submit" class="submit-btn">Send Message</button>
            </form>
        </div>
        
        <div class="map-container">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3532.827594709483!2d85.3120153150621!3d27.69788868279741!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x39eb199a06c2eaf9%3A0x567a0db3a7f70b85!2sManamaiju%2C%20Kathmandu%2044600%2C%20Nepal!5e0!3m2!1sen!2sus!4v1622549400000!5m2!1sen!2sus" allowfullscreen="" loading="lazy"></iframe>
        </div>
    </section>

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