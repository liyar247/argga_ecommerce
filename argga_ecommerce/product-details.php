<?php
session_start();
require_once __DIR__ . '/inc/config.php';
require_once __DIR__ . '/inc/db_connect.php';

// Get product ID from URL
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($product_id <= 0) {
    header('Location: index.php');
    exit;
}

// Get product details
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();
$stmt->close();

if (!$product) {
    header('Location: index.php');
    exit;
}

// Get related products (same category)
$related_products = [];
$stmt = $conn->prepare("SELECT * FROM products WHERE category = ? AND id != ? LIMIT 4");
$stmt->bind_param("si", $product['category'], $product_id);
$stmt->execute();
$related_result = $stmt->get_result();
while ($row = $related_result->fetch_assoc()) {
    $related_products[] = $row;
}
$stmt->close();

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
    <title><?php echo htmlspecialchars($product['name']); ?> - <?php echo SITE_NAME; ?></title>
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
        /* Top Bar */
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
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .cart-count {
            background: #e74c3c;
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
        .offer-badge {
            background: #ffe6d5;
            color: #c25d00;
            padding: 6px 14px;
            border-radius: 25px;
        }
        /* Product Details Section */
        .container {
            flex: 1;
            padding: 40px 5%;
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
        }
        .product-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 50px;
            background: white;
            border-radius: 24px;
            padding: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            margin-bottom: 50px;
        }
        .product-gallery {
            background: #f5f9f5;
            border-radius: 20px;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .product-gallery img {
            width: 100%;
            height: auto;
            object-fit: cover;
        }
        .product-info h1 {
            font-size: 32px;
            color: #1a2e1f;
            margin-bottom: 10px;
        }
        .product-category {
            display: inline-block;
            background: #e8f5e9;
            padding: 5px 15px;
            border-radius: 30px;
            font-size: 12px;
            color: #0b4f2e;
            margin-bottom: 15px;
        }
        .product-price {
            margin: 20px 0;
        }
        .current-price {
            font-size: 36px;
            font-weight: 800;
            color: #0b4f2e;
        }
        .old-price {
            font-size: 20px;
            text-decoration: line-through;
            color: #999;
            margin-left: 15px;
        }
        .discount {
            background: #ff8c42;
            color: white;
            padding: 5px 12px;
            border-radius: 30px;
            font-size: 14px;
            margin-left: 15px;
        }
        .stock-status {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 15px 0;
            padding: 10px 0;
            border-top: 1px solid #eee;
            border-bottom: 1px solid #eee;
        }
        .in-stock {
            color: #27ae60;
            font-weight: 600;
        }
        .out-stock {
            color: #e74c3c;
            font-weight: 600;
        }
        .product-description {
            margin: 20px 0;
            line-height: 1.6;
            color: #555;
        }
        .product-description h3 {
            color: #1a2e1f;
            margin-bottom: 10px;
        }
        .quantity-selector {
            display: flex;
            align-items: center;
            gap: 15px;
            margin: 25px 0;
        }
        .quantity-selector label {
            font-weight: 600;
        }
        .quantity-input {
            display: flex;
            align-items: center;
            border: 1px solid #ddd;
            border-radius: 40px;
            overflow: hidden;
        }
        .quantity-input button {
            width: 40px;
            height: 40px;
            background: #f5f5f5;
            border: none;
            cursor: pointer;
            font-size: 18px;
            font-weight: bold;
        }
        .quantity-input button:hover {
            background: #e0e0e0;
        }
        .quantity-input input {
            width: 60px;
            height: 40px;
            text-align: center;
            border: none;
            outline: none;
            font-size: 16px;
        }
        .add-to-cart-btn {
            background: #ff8c42;
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 50px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }
        .add-to-cart-btn:hover {
            background: #e07a2e;
            transform: scale(1.02);
        }
        .back-home {
            display: inline-block;
            margin-top: 20px;
            color: #0b4f2e;
            text-decoration: none;
        }
        /* Related Products */
        .related-section {
            margin-top: 50px;
        }
        .related-section h2 {
            font-size: 24px;
            color: #0b4f2e;
            margin-bottom: 25px;
        }
        .related-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 25px;
        }
        .related-card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
            transition: 0.3s;
            text-decoration: none;
            color: inherit;
        }
        .related-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.12);
        }
        .related-card img {
            width: 100%;
            height: 150px;
            object-fit: cover;
        }
        .related-card .info {
            padding: 12px;
        }
        .related-card .title {
            font-weight: 600;
            font-size: 14px;
        }
        .related-card .price {
            font-size: 16px;
            font-weight: 700;
            color: #0b4f2e;
            margin-top: 5px;
        }
        /* Footer */
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
        .toast.error { background: #e74c3c; }
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        @media (max-width: 768px) {
            .product-details {
                grid-template-columns: 1fr;
                gap: 30px;
            }
            .logo-area {
                flex-direction: column;
                text-align: center;
                gap: 15px;
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
    <div class="product-details">
        <div class="product-gallery">
            <img src="backend/uploads/<?php echo $product['image'] ?: 'placeholder.jpg'; ?>" 
                 onerror="this.src='https://via.placeholder.com/500x500?text=No+Image'"
                 alt="<?php echo htmlspecialchars($product['name']); ?>">
        </div>
        <div class="product-info">
            <div class="product-category">
                <i class="fas fa-tag"></i> <?php echo strtoupper(htmlspecialchars($product['category'] ?: 'General')); ?>
            </div>
            <h1><?php echo htmlspecialchars($product['name']); ?></h1>
            <div class="product-price">
                <span class="current-price">৳<?php echo number_format($product['price'], 2); ?></span>
                <?php if ($product['old_price'] && $product['old_price'] > 0): ?>
                    <span class="old-price">৳<?php echo number_format($product['old_price'], 2); ?></span>
                <?php endif; ?>
                <?php if ($product['discount']): ?>
                    <span class="discount"><?php echo htmlspecialchars($product['discount']); ?></span>
                <?php endif; ?>
            </div>
            <div class="stock-status">
                <i class="fas fa-check-circle"></i>
                <span class="in-stock">In Stock (<?php echo $product['stock']; ?> items available)</span>
            </div>
            <div class="product-description">
                <h3>Product Description</h3>
                <p><?php echo !empty($product['description']) ? htmlspecialchars($product['description']) : 'No description available for this product.'; ?></p>
            </div>
            <div class="quantity-selector">
                <label>Quantity:</label>
                <div class="quantity-input">
                    <button type="button" id="qty-minus">-</button>
                    <input type="number" id="product-qty" value="1" min="1" max="<?php echo $product['stock']; ?>">
                    <button type="button" id="qty-plus">+</button>
                </div>
            </div>
            <button class="add-to-cart-btn" id="add-to-cart">
                <i class="fas fa-shopping-cart"></i> Add to Cart
            </button>
            <br>
            <a href="index.php" class="back-home"><i class="fas fa-arrow-left"></i> Continue Shopping</a>
        </div>
    </div>
    
    <?php if (count($related_products) > 0): ?>
    <div class="related-section">
        <h2><i class="fas fa-heart"></i> You May Also Like</h2>
        <div class="related-grid">
            <?php foreach ($related_products as $related): ?>
                <a href="product-details.php?id=<?php echo $related['id']; ?>" class="related-card">
                    <img src="backend/uploads/<?php echo $related['image'] ?: 'placeholder.jpg'; ?>" 
                         onerror="this.src='https://via.placeholder.com/220x150?text=No+Image'">
                    <div class="info">
                        <div class="title"><?php echo htmlspecialchars($related['name']); ?></div>
                        <div class="price">৳<?php echo number_format($related['price'], 2); ?></div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<footer class="footer">
    <div class="footer-links">
        <div class="footer-col"><p><strong>EXPLORE</strong></p><p>Medicine</p><p>Lab Test</p><p>Health Corner</p><p>Offers</p></div>
        <div class="footer-col"><p><strong>POLICIES</strong></p><p>Return Policy</p><p>Shipping</p><p>Privacy</p><p>Terms</p></div>
        <div class="footer-col"><p><strong>CONTACT</strong></p><p>Help Center</p><p>WhatsApp Us</p><p>Email Support</p></div>
        <div class="footer-col"><p><i class="fas fa-truck"></i> <strong>ORDER NOW</strong></p><p><i class="fas fa-phone"></i> 09678 111 222</p><p>© <?php echo date('Y'); ?> argGa health</p></div>
    </div>
</footer>
<div class="copy"><p>📍 Delivery Across Bangladesh | Safe & Trusted Pharmacy | 24/7 Support</p></div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
// Quantity selector
$(document).ready(function() {
    $('#qty-minus').click(function() {
        let qty = parseInt($('#product-qty').val());
        if (qty > 1) {
            $('#product-qty').val(qty - 1);
        }
    });
    $('#qty-plus').click(function() {
        let qty = parseInt($('#product-qty').val());
        let max = parseInt($('#product-qty').attr('max'));
        if (qty < max) {
            $('#product-qty').val(qty + 1);
        }
    });
    
    $('#add-to-cart').click(function() {
        let productId = <?php echo $product['id']; ?>;
        let productName = '<?php echo addslashes($product['name']); ?>';
        let quantity = $('#product-qty').val();
        
        <?php if (isset($_SESSION['user_id'])): ?>
            $.ajax({
                url: 'backend/api/add_to_cart.php',
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({ product_id: productId, quantity: parseInt(quantity) }),
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
            setTimeout(function() { window.location.href = 'pages/login.php'; }, 1500);
        <?php endif; ?>
    });
});

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
</script>

</body>
</html>