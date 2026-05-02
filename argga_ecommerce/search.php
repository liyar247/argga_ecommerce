<?php
session_start();
require_once __DIR__ . '/inc/config.php';
require_once __DIR__ . '/inc/db_connect.php';

// Get search query
$search_query = isset($_GET['q']) ? trim($_GET['q']) : '';
$products = [];

if (!empty($search_query)) {
    $stmt = $conn->prepare("SELECT * FROM products WHERE name LIKE ? OR category LIKE ? ORDER BY id DESC");
    $search_param = "%$search_query%";
    $stmt->bind_param("ss", $search_param, $search_param);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
    $stmt->close();
}

// Get cart count for logged in user
$cart_count = 0;
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $count_result = $conn->query("SELECT SUM(quantity) as total FROM cart WHERE user_id = $user_id");
    if ($count_result && $row = $count_result->fetch_assoc()) {
        $cart_count = $row['total'] ?? 0;
    }
}
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }
        body {
            background: #f0f4f0;
        }
        .top-bar {
            background: #0b4f2e;
            color: white;
            padding: 10px 5%;
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            flex-wrap: wrap;
        }
        .logo-area {
            background: white;
            padding: 15px 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .logo h1 {
            color: #0b4f2e;
            font-size: 28px;
        }
        .logo span {
            font-size: 12px;
            color: #5a8f5c;
            display: block;
        }
        .logo a {
            text-decoration: none;
        }
        .auth-buttons {
            display: flex;
            gap: 12px;
        }
        .signin-btn, .signup-btn {
            padding: 10px 24px;
            border-radius: 40px;
            text-decoration: none;
            font-weight: 600;
        }
        .signin-btn {
            background: #0b4f2e;
            color: white;
        }
        .signup-btn {
            background: #ff8c42;
            color: white;
        }
        .user-greeting {
            background: #e9f5e9;
            padding: 8px 20px;
            border-radius: 40px;
            display: flex;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
        }
        .cart-link {
            background: #ff8c42;
            color: white;
            padding: 6px 15px;
            border-radius: 30px;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .cart-count {
            background: #e74c3c;
            color: white;
            border-radius: 50%;
            padding: 2px 8px;
            font-size: 12px;
        }
        .logout-link {
            background: #e74c3c;
            color: white;
            padding: 6px 15px;
            border-radius: 30px;
            text-decoration: none;
        }
        .nav-section {
            background: white;
            padding: 15px 5%;
            display: flex;
            justify-content: center;
        }
        .search-box {
            width: 100%;
            max-width: 500px;
        }
        .search-box form {
            display: flex;
            border: 1px solid #cde0cd;
            border-radius: 50px;
            overflow: hidden;
        }
        .search-box input {
            flex: 1;
            padding: 12px 20px;
            border: none;
            outline: none;
        }
        .search-box button {
            background: #0b4f2e;
            border: none;
            color: white;
            padding: 0 25px;
            cursor: pointer;
        }
        .categories {
            display: flex;
            gap: 12px;
            padding: 15px 5%;
            background: white;
            border-bottom: 1px solid #e0f0e0;
            flex-wrap: wrap;
            overflow-x: auto;
        }
        .categories a {
            padding: 6px 14px;
            border-radius: 25px;
            text-decoration: none;
            font-size: 13px;
            color: #2a5e2f;
            transition: 0.3s;
            background: #f5f9f5;
            border: 1px solid #e0f0e0;
        }
        .categories a:hover {
            background: #0b4f2e;
            color: white;
        }
        .offer-badge {
            background: #ffe6d5;
            color: #c25d00;
            padding: 6px 14px;
            border-radius: 25px;
        }
        .container {
            padding: 30px 5%;
            max-width: 1400px;
            margin: 0 auto;
        }
        .search-header {
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e0f0e0;
        }
        .search-header h1 {
            font-size: 28px;
            color: #0b4f2e;
            display: flex;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
        }
        .search-header .result-count {
            background: #e8f5e9;
            padding: 5px 15px;
            border-radius: 30px;
            font-size: 14px;
            font-weight: normal;
        }
        .search-header .search-term {
            background: #fff3cd;
            padding: 5px 15px;
            border-radius: 30px;
            font-size: 14px;
            color: #856404;
        }
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 30px;
        }
        .product-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            border: 1px solid #eef3ee;
        }
        .product-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 35px rgba(0,0,0,0.1);
        }
        .product-image {
            height: 220px;
            background: linear-gradient(145deg, #f5f9f5, #edf3ed);
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.4s ease;
        }
        .product-card:hover .product-image img {
            transform: scale(1.05);
        }
        .discount-badge {
            position: absolute;
            top: 12px;
            right: 12px;
            background: linear-gradient(135deg, #ff8c42, #ff6b2c);
            color: white;
            padding: 5px 12px;
            border-radius: 25px;
            font-size: 11px;
            font-weight: bold;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        }
        .product-info {
            padding: 18px;
        }
        .product-title {
            font-weight: 700;
            font-size: 18px;
            margin-bottom: 6px;
            color: #1a2e1f;
        }
        .product-category {
            font-size: 11px;
            color: #8ba888;
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .price {
            font-size: 24px;
            font-weight: 800;
            color: #0b4f2e;
            margin: 10px 0 6px;
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
        }
        .old-price {
            text-decoration: line-through;
            font-size: 14px;
            color: #aaa;
            font-weight: 400;
        }
        .stock {
            font-size: 12px;
            color: #27ae60;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .add-to-cart {
            width: 100%;
            background: #0b4f2e;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 50px;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        .add-to-cart:hover {
            background: #1e6b42;
            transform: scale(1.02);
        }
        .no-results {
            text-align: center;
            padding: 60px;
            background: white;
            border-radius: 24px;
            grid-column: 1 / -1;
        }
        .no-results i {
            font-size: 64px;
            color: #ccc;
            margin-bottom: 20px;
            display: block;
        }
        .no-results h3 {
            color: #555;
            margin-bottom: 10px;
        }
        .back-home {
            display: inline-block;
            margin-top: 20px;
            background: #0b4f2e;
            color: white;
            padding: 10px 28px;
            border-radius: 40px;
            text-decoration: none;
        }
        .footer {
            background: #0c3020;
            color: #d9ecd9;
            padding: 40px 5% 25px;
            margin-top: 60px;
        }
        .footer-links {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 30px;
            max-width: 1200px;
            margin: 0 auto;
        }
        .footer-col p {
            margin: 8px 0;
            font-size: 13px;
        }
        .footer-col p strong {
            color: white;
        }
        .copy {
            text-align: center;
            padding: 20px;
            background: #0a281b;
            color: #b8dec0;
            font-size: 12px;
            margin-top: 30px;
        }
        .toast {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: #27ae60;
            color: white;
            padding: 12px 24px;
            border-radius: 50px;
            z-index: 9999;
            animation: slideIn 0.3s ease;
        }
        .toast.error {
            background: #e74c3c;
        }
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        @media (max-width: 768px) {
            .logo-area {
                flex-direction: column;
                text-align: center;
                gap: 15px;
            }
            .products-grid {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
                gap: 20px;
            }
        }
    </style>
</head>
<body>

<div class="top-bar">
    <span><i class="fas fa-heartbeat"></i> For better health • Delivery to Bangladesh</span>
    <span><i class="fas fa-phone-alt"></i> Call to Order | Upload Prescription</span>
</div>

<div class="logo-area">
    <div class="logo">
        <a href="index.php">
            <h1>DocoMart <span>For better health</span></h1>
        </a>
    </div>
    <?php if (isset($_SESSION['user_id'])): ?>
        <div class="user-greeting">
            <i class="fas fa-user-circle"></i> Hello, <?php echo htmlspecialchars($_SESSION['user_name']); ?>
            <a href="pages/cart.php" class="cart-link">
                <i class="fas fa-shopping-cart"></i> Cart 
                <span class="cart-count" id="cartCount"><?php echo $cart_count; ?></span>
            </a>
            <a href="pages/logout.php" class="logout-link">Logout</a>
        </div>
    <?php else: ?>
        <div class="auth-buttons">
            <a href="pages/login.php" class="signin-btn">Sign In</a>
            <a href="pages/register.php" class="signup-btn">Register</a>
        </div>
    <?php endif; ?>
</div>

<div class="nav-section">
    <div class="search-box">
        <form action="search.php" method="GET">
            <input type="text" name="q" placeholder="Search for products..." value="<?php echo htmlspecialchars($search_query); ?>">
            <button type="submit"><i class="fas fa-search"></i></button>
        </form>
    </div>
</div>

<div class="categories">
    <a href="category.php?cat=medicine">Medicine</a>
    <a href="category.php?cat=healthcare">Healthcare</a>
    <a href="category.php?cat=beauty">Beauty</a>
    <a href="category.php?cat=baby">Baby & Mom Care</a>
    <span class="offer-badge">FLASH SALE 65% OFF</span>
    
</div>

<div class="container">
    <div class="search-header">
        <h1>
            <i class="fas fa-search"></i> Search Results
            <span class="result-count"><?php echo count($products); ?> products found</span>
            <?php if (!empty($search_query)): ?>
                <span class="search-term">"<?php echo htmlspecialchars($search_query); ?>"</span>
            <?php endif; ?>
        </h1>
    </div>
    
    <div class="products-grid">
        <?php if (count($products) > 0): ?>
            <?php foreach ($products as $product): ?>
                <div class="product-card">
                    <div class="product-image">
                        <img src="backend/uploads/<?php echo $product['image'] ?: 'placeholder.jpg'; ?>" 
                             onerror="this.src='https://via.placeholder.com/300x200?text=No+Image'"
                             alt="<?php echo htmlspecialchars($product['name']); ?>">
                        <?php if ($product['discount']): ?>
                            <div class="discount-badge"><?php echo htmlspecialchars($product['discount']); ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="product-info">
                        <div class="product-title"><?php echo htmlspecialchars($product['name']); ?></div>
                        <div class="product-category">
                            <i class="fas fa-tag"></i> <?php echo strtoupper(htmlspecialchars($product['category'] ?: 'General')); ?>
                        </div>
                        <div class="price">
                            ৳<?php echo number_format($product['price'], 2); ?>
                            <?php if ($product['old_price'] && $product['old_price'] > 0): ?>
                                <span class="old-price">৳<?php echo number_format($product['old_price'], 2); ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="stock">
                            <i class="fas fa-check-circle"></i> In Stock (<?php echo $product['stock']; ?> items)
                        </div>
                        <button class="add-to-cart" data-id="<?php echo $product['id']; ?>" data-name="<?php echo htmlspecialchars($product['name']); ?>">
                            <i class="fas fa-shopping-cart"></i> Add to Cart
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="no-results">
                <i class="fas fa-search"></i>
                <h3>No products found</h3>
                <p>We couldn't find any products matching "<?php echo htmlspecialchars($search_query); ?>"</p>
                <p>Try searching with different keywords or browse our categories.</p>
                <a href="index.php" class="back-home">Back to Home</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<footer class="footer">
    <div class="footer-links">
        <div class="footer-col">
            <p><strong>EXPLORE</strong></p>
            <p>Medicine</p><p>Lab Test</p><p>Health Corner</p><p>Offers</p>
        </div>
        <div class="footer-col">
            <p><strong>POLICIES</strong></p>
            <p>Return Policy</p><p>Shipping</p><p>Privacy</p><p>Terms</p>
        </div>
        <div class="footer-col">
            <p><strong>CONTACT</strong></p>
            <p>Help Center</p><p>WhatsApp Us</p><p>Email Support</p>
        </div>
        <div class="footer-col">
            <p><i class="fas fa-truck"></i> <strong>ORDER NOW</strong></p>
            <p><i class="fas fa-phone"></i> 09678 111 222</p>
            <p><i class="far fa-copyright"></i> <?php echo date('Y'); ?> argGa health</p>
        </div>
    </div>
</footer>
<div class="copy">
    <p>📍 Delivery Across Bangladesh | Safe & Trusted Pharmacy | 24/7 Support</p>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
// Add to cart function
function addToCart(productId, productName) {
    <?php if (isset($_SESSION['user_id'])): ?>
        $.ajax({
            url: 'backend/api/add_to_cart.php',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ product_id: productId, quantity: 1 }),
            success: function(response) {
                if (response.success) {
                    showToast('✅ ' + productName + ' added to cart!');
                    updateCartCount();
                } else {
                    showToast('❌ ' + response.message, true);
                }
            },
            error: function() {
                showToast('❌ Error adding to cart', true);
            }
        });
    <?php else: ?>
        showToast('❌ Please login first to add items to cart!', true);
        setTimeout(function() {
            window.location.href = 'pages/login.php';
        }, 1500);
    <?php endif; ?>
}

// Update cart count
function updateCartCount() {
    $.ajax({
        url: 'backend/api/get_cart.php',
        type: 'GET',
        success: function(response) {
            if (response.success) {
                $('.cart-count').text(response.count || 0);
            }
        }
    });
}

// Show toast notification
function showToast(message, isError = false) {
    $('.toast').remove();
    var toast = $('<div class="toast' + (isError ? ' error' : '') + '">' + message + '</div>');
    $('body').append(toast);
    setTimeout(function() {
        toast.fadeOut(300, function() { $(this).remove(); });
    }, 3000);
}

// Attach click event to add to cart buttons
$(document).ready(function() {
    $('.add-to-cart').click(function() {
        var productId = $(this).data('id');
        var productName = $(this).data('name');
        addToCart(productId, productName);
    });
    
    updateCartCount();
});
</script>

</body>
</html>