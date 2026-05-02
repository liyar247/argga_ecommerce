<?php
session_start();
require_once __DIR__ . '/inc/config.php';
require_once __DIR__ . '/inc/db_connect.php';

// Show welcome message after login
if (isset($_GET['login']) && $_GET['login'] == 'success' && isset($_SESSION['user_name'])) {
    echo '<script>
        document.addEventListener("DOMContentLoaded", function() {
            showToast("Welcome back, ' . $_SESSION['user_name'] . '! 🎉");
        });
    </script>';
}

// Get products
$products = [];
$result = $conn->query("SELECT * FROM products ORDER BY id DESC");
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}

// Get cart count
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
    <title><?php echo SITE_NAME; ?> | <?php echo SITE_TAGLINE; ?></title>
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
            display: flex;
            flex-direction: column;
            min-height: 100vh;
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
        .signin-btn { background: #0b4f2e; color: white; }
        .signup-btn { background: #ff8c42; color: white; }
        .user-greeting {
            background: #e9f5e9;
            padding: 8px 20px;
            border-radius: 40px;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .cart-link {
            background: #ff8c42;
            color: white;
            padding: 6px 15px;
            border-radius: 30px;
            text-decoration: none;
        }
        .cart-count {
            background: #e74c3c;
            border-radius: 50%;
            padding: 2px 8px;
            font-size: 12px;
            margin-left: 5px;
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
        }
        .categories a {
            padding: 6px 14px;
            border-radius: 25px;
            text-decoration: none;
            font-size: 13px;
            color: #2a5e2f;
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
            flex: 1;
            padding: 30px 5%;
            max-width: 1400px;
            margin: 0 auto;
            width: 100%;
        }
        .section-title {
            margin-bottom: 30px;
        }
        .section-title h2 {
            font-size: 28px;
            color: #0b4f2e;
        }
        .offer-tag {
            background: #ff8c42;
            padding: 5px 15px;
            border-radius: 30px;
            color: white;
            font-size: 14px;
            margin-left: 10px;
        }
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 25px;
        }
        .product-card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            transition: 0.3s;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }
        .product-link {
            text-decoration: none;
            color: inherit;
            display: block;
        }
        .product-image {
            height: 200px;
            background: #f8faf8;
            overflow: hidden;
        }
        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: 0.3s;
        }
        .product-card:hover .product-image img {
            transform: scale(1.05);
        }
        .product-info {
            padding: 15px;
        }
        .product-title {
            font-weight: 700;
            font-size: 16px;
            margin-bottom: 5px;
        }
        .product-category {
            font-size: 11px;
            color: #8ba888;
            margin-bottom: 10px;
            text-transform: uppercase;
        }
        .price {
            font-size: 20px;
            font-weight: 800;
            color: #0b4f2e;
            margin: 8px 0;
        }
        .old-price {
            text-decoration: line-through;
            font-size: 13px;
            color: #aaa;
            margin-left: 8px;
        }
        .stock {
            font-size: 11px;
            color: #27ae60;
            margin-bottom: 12px;
        }
        .add-to-cart {
            width: calc(100% - 30px);
            margin: 0 15px 15px 15px;
            background: #0b4f2e;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 40px;
            cursor: pointer;
            font-weight: 600;
            transition: 0.3s;
        }
        .add-to-cart:hover {
            background: #1e6b42;
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
        .toast.error { background: #e74c3c; }
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        @media (max-width: 768px) {
            .logo-area { flex-direction: column; text-align: center; gap: 15px; }
            .products-grid { grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px; }
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
            <input type="text" name="q" placeholder="Search for products...">
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
    <div class="section-title">
        <h2>🔥 Best Offers <span class="offer-tag">UPTO 60% OFF</span></h2>
    </div>
    
    <!-- 👇👇👇 এখানে আপনার কোড বসবে 👇👇👇 -->
    <div class="products-grid">
        <?php if (count($products) > 0): ?>
            <?php foreach ($products as $product): ?>
                <div class="product-card">
                    <a href="product-details.php?id=<?php echo $product['id']; ?>" class="product-link">
                        <div class="product-image">
                            <img src="backend/uploads/<?php echo $product['image'] ?: 'placeholder.jpg'; ?>" 
                                 onerror="this.src='https://via.placeholder.com/300?text=No+Image'">
                        </div>
                        <div class="product-info">
                            <div class="product-title"><?php echo htmlspecialchars($product['name']); ?></div>
                            <div class="product-category"><?php echo htmlspecialchars($product['category']); ?></div>
                            <div class="price">
                                ৳<?php echo number_format($product['price'], 2); ?>
                                <?php if ($product['old_price']): ?>
                                    <span class="old-price">৳<?php echo number_format($product['old_price'], 2); ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="stock">In Stock (<?php echo $product['stock']; ?> items)</div>
                        </div>
                    </a>
                    <button class="add-to-cart" data-id="<?php echo $product['id']; ?>" data-name="<?php echo htmlspecialchars($product['name']); ?>">
                        <i class="fas fa-shopping-cart"></i> Add to Cart
                    </button>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="no-products" style="text-align:center; padding:60px; background:white; border-radius:20px; grid-column:1/-1;">
                <p>No products found</p>
            </div>
        <?php endif; ?>
    </div>
    <!-- 👆👆👆 আপনার কোড এখান পর্যন্ত 👆👆👆 -->
    
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
    $(document).ready(function() {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('login') && urlParams.get('login') === 'success') {
        showToast('Welcome back! 🎉');
        // Remove the parameter from URL without reloading
        const newUrl = window.location.pathname;
        window.history.pushState({}, '', newUrl);
    }
});
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
        showToast('❌ Please login first!', true);
        setTimeout(function() { window.location.href = 'pages/login.php'; }, 1500);
    <?php endif; ?>
}

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

function showToast(message, isError = false) {
    $('.toast').remove();
    var toast = $('<div class="toast' + (isError ? ' error' : '') + '">' + message + '</div>');
    $('body').append(toast);
    setTimeout(function() { toast.fadeOut(300, function() { $(this).remove(); }); }, 3000);
}

$(document).ready(function() {
    $('.add-to-cart').click(function(e) {
        e.stopPropagation();
        addToCart($(this).data('id'), $(this).data('name'));
    });
    updateCartCount();
});
</script>

</body>
</html>