<?php
include 'db.php';

// Base SQL query
$sql = "SELECT * FROM beverages";

// Check if category filter is applied
$categoryFilter = isset($_GET['category']) ? $_GET['category'] : '';

// Build WHERE clause for search and category
$whereClause = "";

// Add search condition if provided
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
    $whereClause .= ($whereClause == "") ? " WHERE name LIKE '%$search%'" : " AND name LIKE '%$search%'";
}

// Add category filter if provided
if (!empty($categoryFilter)) {
    $categoryFilter = mysqli_real_escape_string($conn, $categoryFilter);
    $whereClause .= ($whereClause == "") ? " WHERE type = '$categoryFilter'" : " AND type = '$categoryFilter'";
}

// Add the WHERE clause to the SQL query
$sql .= $whereClause;

// Add sorting based on the sort parameter
if (isset($_GET['sort'])) {
    switch ($_GET['sort']) {
        case 'price-low':
            $sql .= " ORDER BY price ASC";
            break;
        case 'price-high':
            $sql .= " ORDER BY price DESC";
            break;
        case 'name':
            $sql .= " ORDER BY name ASC";
            break;
        default:
            // Default sorting (could be by id or any other default)
            $sql .= " ORDER BY id ASC";
            break;
    }
} else {
    // Default sorting if no sort parameter provided
    $sql .= " ORDER BY id ASC";
}

$result = mysqli_query($conn, $sql);

if ($result) {
?>

<!DOCTYPE html>
<html>
<head>
    <title>Shop Beverages</title>
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

.container {
    width: 90%;
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

h1 {
    text-align: center;
    margin: 30px 0;
    color: #333;
    font-size: 2.5rem;
    text-transform: uppercase;
    letter-spacing: 2px;
    font-family: 'Montserrat', sans-serif;
    position: relative;
}

h1:after {
    content: '';
    display: block;
    width: 80px;
    height: 4px;
    background: #5a52e2;
    margin: 15px auto 0;
    border-radius: 2px;
}

/* NEW ITEM LAYOUT DESIGN */
.products-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

.products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 25px;
}

.product-card {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    transition: transform 0.3s, box-shadow 0.3s;
}

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
}

.product-img-container {
    height: 180px;
    background: #f9f9f9;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    padding: 15px;
}

.product-img-container img {
    max-width: 100%;
    max-height: 150px;
    object-fit: contain;
    transition: transform 0.3s;
}

.product-card:hover .product-img-container img {
    transform: scale(1.05);
}

.product-info {
    padding: 20px;
}

.product-name {
    font-weight: 600;
    font-size: 1.1rem;
    margin: 0 0 10px 0;
    color: #333;
}

.product-price {
    font-size: 1.2rem;
    color: #5a52e2;
    font-weight: 700;
    margin-bottom: 15px;
}

.product-actions {
    display: flex;
    align-items: center;
    margin-top: 15px;
}

.quantity-input {
    width: 60px;
    padding: 8px 10px;
    border: 1px solid #ddd;
    border-radius: 6px;
    margin-right: 10px;
    text-align: center;
    font-family: 'Poppins', sans-serif;
}

.add-to-cart-btn {
    flex: 1;
    background-color: #5a52e2;
    color: white;
    border: none;
    padding: 10px 15px;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 500;
    font-family: 'Poppins', sans-serif;
    transition: background-color 0.3s;
    display: flex;
    align-items: center;
    justify-content: center;
}

.add-to-cart-btn:hover {
    background-color: #4a44d5;
}

/* Category/Filter Bar */
.filter-bar {
    background: white;
    padding: 15px 20px;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    margin-bottom: 25px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 15px;
}

.category-options {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.category-btn {
    padding: 8px 15px;
    background: #f5f5f5;
    border: none;
    border-radius: 20px;
    color: #666;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
    text-decoration: none;
}

.category-btn:hover, .category-btn.active {
    background: #5a52e2;
    color: white;
}

.sort-options select {
    padding: 8px 15px;
    border-radius: 6px;
    border: 1px solid #ddd;
    font-family: 'Poppins', sans-serif;
    cursor: pointer;
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

/* Responsive */
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
    
    .filter-bar {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .products-grid {
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    }
    
    .footer-content {
        flex-direction: column;
        gap: 20px;
        text-align: center;
    }
}

/* No results message styling */
.no-results {
    text-align: center;
    padding: 30px;
    font-size: 1.2rem;
    color: #666;
}
    </style>
</head>
<body>
<header>
    <div class="container nav-container">
        <a href="index.php" class="logo">A & <span>B</span> STORE</a>
        <ul class="nav-menu">
            <li><a href="userpage.php">Home</a></li>
            <li><a href="shop.php" class="active">Shop</a></li>
            <li><a href="cart.php">Cart</a></li>
            <li><a href="account.php">Account</a></li>
        </ul>
        <!-- Search Form with Autocomplete -->
        <div class="search-container">
            <form method="GET" action="shop.php" id="searchForm" style="display: flex; align-items: center;">
                <input type="text" id="searchInput" name="search" class="search-input" placeholder="Search beverages..." 
                    value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" autocomplete="off">
                <!-- Hidden inputs to maintain parameters -->
                <input type="hidden" id="sortParam" name="sort" value="<?php echo isset($_GET['sort']) ? htmlspecialchars($_GET['sort']) : 'default'; ?>">
                <input type="hidden" id="categoryParam" name="category" value="<?php echo htmlspecialchars($categoryFilter); ?>">
                <div id="autocompleteResults" class="autocomplete-items"></div>
                <button type="submit" class="search-button">Search</button>
            </form>
        </div>
    </div>
</header>

<h1>Shop Beverages</h1>

<div class="products-container">
    <!-- Filter and Category Bar -->
    <div class="filter-bar">
        <div class="category-options">
            <a href="shop.php<?php echo isset($_GET['sort']) ? '?sort=' . htmlspecialchars($_GET['sort']) : ''; ?>" 
               class="category-btn <?php echo empty($categoryFilter) ? 'active' : ''; ?>">All</a>
            <a href="shop.php?category=Juice<?php echo isset($_GET['sort']) ? '&sort=' . htmlspecialchars($_GET['sort']) : ''; ?>" 
               class="category-btn <?php echo $categoryFilter === 'Juice' ? 'active' : ''; ?>">Juice</a>
            <a href="shop.php?category=Alcohol<?php echo isset($_GET['sort']) ? '&sort=' . htmlspecialchars($_GET['sort']) : ''; ?>" 
               class="category-btn <?php echo $categoryFilter === 'Alcohol' ? 'active' : ''; ?>">Alcohol</a>
            <a href="shop.php?category=Soft Drinks<?php echo isset($_GET['sort']) ? '&sort=' . htmlspecialchars($_GET['sort']) : ''; ?>" 
               class="category-btn <?php echo $categoryFilter === 'Soft Drinks' ? 'active' : ''; ?>">Soft Drinks</a>
            <a href="shop.php?category=Energy Drinks<?php echo isset($_GET['sort']) ? '&sort=' . htmlspecialchars($_GET['sort']) : ''; ?>" 
               class="category-btn <?php echo $categoryFilter === 'Energy Drinks' ? 'active' : ''; ?>">Energy Drinks</a>
        </div>
        <div class="sort-options">
            <select id="sortSelect">
                <option value="default" <?php echo (!isset($_GET['sort']) || $_GET['sort'] === 'default') ? 'selected' : ''; ?>>Sort By: Default</option>
                <option value="price-low" <?php echo (isset($_GET['sort']) && $_GET['sort'] === 'price-low') ? 'selected' : ''; ?>>Price: Low to High</option>
                <option value="price-high" <?php echo (isset($_GET['sort']) && $_GET['sort'] === 'price-high') ? 'selected' : ''; ?>>Price: High to Low</option>
                <option value="name" <?php echo (isset($_GET['sort']) && $_GET['sort'] === 'name') ? 'selected' : ''; ?>>Name: A to Z</option>
            </select>
        </div>
    </div>

    <!-- Products Grid Layout -->
    <div class="products-grid">
        <?php 
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) { 
        ?>
            <div class="product-card">
                <div class="product-img-container">
                    <?php echo "<img src='img/" . $row['image'] . "' alt='" . htmlspecialchars($row['name']) . "'>"; ?>
                </div>
                <div class="product-info">
                    <h3 class="product-name"><?php echo htmlspecialchars($row['name']); ?></h3>
                    <div class="product-price">Rs <?php echo htmlspecialchars($row['price']); ?></div>
                    <form class="buy-form" data-product-id="<?php echo $row['id']; ?>">
                        <input type="hidden" name="action" value="add">
                        <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                        <div class="product-actions">
                            <input type="number" name="quantity" value="1" min="1" max="99" class="quantity-input">
                            <button type="button" class="add-to-cart-btn">Add to Cart</button>
                        </div>
                    </form>
                </div>
            </div>
        <?php 
            }
        } else {
            echo '<div class="no-results">No beverages found matching your criteria.</div>';
        }
        ?>
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function(){
    // Add to cart functionality
    $(".add-to-cart-btn").click(function(){
        var form = $(this).closest(".buy-form");
        var formData = form.serialize();
        $.ajax({
            type: "POST",
            url: "cart.php",
            data: formData,
            dataType: "json",
            success: function(response){
                if (response.status === "success") {
                    alert(response.message);
                } else {
                    alert(response.message);
                }
            },
            error: function(xhr, status, error){
                console.error(xhr.responseText);
            }
        });
    });

    // Autocomplete functionality
    $("#searchInput").on("input", function() {
        var query = $(this).val();
        if(query.length > 0) {
            $.ajax({
                type: "GET",
                url: "search_autocomplete.php",
                data: { query: query },
                dataType: "json",
                success: function(data) {
                    showAutocompleteResults(data);
                }
            });
        } else {
            $("#autocompleteResults").empty();
        }
    });

    function showAutocompleteResults(data) {
        var resultsDiv = $("#autocompleteResults");
        resultsDiv.empty();
        
        if(data.length > 0) {
            data.forEach(function(item) {
                var div = $("<div>").text(item.name);
                div.click(function() {
                    $("#searchInput").val(item.name);
                    resultsDiv.empty();
                });
                resultsDiv.append(div);
            });
        }
    }

    // Sorting functionality
    $("#sortSelect").change(function() {
        var selectedSort = $(this).val();
        var currentSearch = new URLSearchParams(window.location.search).get('search') || '';
        var currentCategory = new URLSearchParams(window.location.search).get('category') || '';
        
        // Build the URL with parameters
        var url = 'shop.php?sort=' + selectedSort;
        
        if (currentCategory) {
            url += '&category=' + encodeURIComponent(currentCategory);
        }
        
        if (currentSearch) {
            url += '&search=' + encodeURIComponent(currentSearch);
        }
        
        // Redirect to the new URL
        window.location.href = url;
    });

    // Close autocomplete when clicking outside
    $(document).on("click", function(e) {
        if (!$(e.target).closest(".search-container").length) {
            $("#autocompleteResults").empty();
        }
    });
});
</script>

</body>
</html>

<?php
} else {
    echo "Error: " . mysqli_error($conn);
}
?>