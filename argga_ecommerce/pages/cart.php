<?php
session_start();
require_once '../inc/db_connect.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Handle remove action
if (isset($_GET['remove'])) {
    $cart_id = (int)$_GET['remove'];
    $conn->query("DELETE FROM cart WHERE id = $cart_id AND user_id = $user_id");
    header('Location: cart.php');
    exit;
}

// Handle update quantities
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_cart'])) {
    foreach ($_POST['quantity'] as $cart_id => $qty) {
        $qty = (int)$qty;
        if ($qty <= 0) {
            $conn->query("DELETE FROM cart WHERE id = $cart_id AND user_id = $user_id");
        } else {
            $conn->query("UPDATE cart SET quantity = $qty WHERE id = $cart_id AND user_id = $user_id");
        }
    }
    header('Location: cart.php');
    exit;
}

// Get cart items
$query = "SELECT c.id as cart_id, c.quantity, p.id as product_id, p.name, p.price, p.image, p.stock 
          FROM cart c 
          JOIN products p ON c.product_id = p.id 
          WHERE c.user_id = $user_id";
$result = $conn->query($query);

$cart_items = [];
$total = 0;
$total_items = 0;

while ($row = $result->fetch_assoc()) {
    $subtotal = $row['price'] * $row['quantity'];
    $total += $subtotal;
    $total_items += $row['quantity'];
    $cart_items[] = $row;
}
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - argGa</title>
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
        
        /* Logo Area */
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
        
        .back-home {
            background: #0b4f2e;
            color: white;
            padding: 10px 24px;
            border-radius: 40px;
            text-decoration: none;
            font-weight: 600;
            transition: 0.3s;
        }
        
        .back-home:hover {
            background: #1e6b42;
        }
        
        /* Main Content */
        .main-content {
            flex: 1;
        }
        
        .container {
            padding: 30px 5%;
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
        }
        
        .cart-title {
            font-size: 28px;
            color: #0b4f2e;
            margin-bottom: 25px;
        }
        
        /* Cart Table */
        .cart-table {
            width: 100%;
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0,0,0,0.06);
        }
        
        .cart-table th, .cart-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        .cart-table th {
            background: #f8f9fa;
            font-weight: 600;
            color: #333;
        }
        
        .product-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .product-info img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 10px;
            background: #f5f9f5;
        }
        
        .product-info .product-name {
            font-weight: 600;
            color: #1a2e1f;
        }
        
        .quantity-input {
            width: 80px;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 8px;
            text-align: center;
        }
        
        .remove-btn {
            color: #e74c3c;
            text-decoration: none;
            font-size: 14px;
            transition: 0.3s;
        }
        
        .remove-btn:hover {
            color: #c0392b;
            text-decoration: underline;
        }
        
        /* Cart Footer */
        .cart-footer {
            background: white;
            border-radius: 20px;
            padding: 20px;
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.06);
        }
        
        .cart-total {
            font-size: 24px;
            font-weight: 800;
            color: #0b4f2e;
        }
        
        .update-btn {
            background: #3498db;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 40px;
            cursor: pointer;
            font-weight: 600;
            transition: 0.3s;
        }
        
        .update-btn:hover {
            background: #2980b9;
        }
        
        .checkout-btn {
            background: #ff8c42;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 40px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: 0.3s;
        }
        
        .checkout-btn:hover {
            background: #e07a2e;
            transform: scale(1.02);
        }
        
        /* Empty Cart */
        .empty-cart {
            text-align: center;
            padding: 60px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.06);
        }
        
        .empty-cart i {
            font-size: 64px;
            color: #ccc;
            margin-bottom: 20px;
            display: block;
        }
        
        .empty-cart h3 {
            color: #555;
            margin-bottom: 10px;
        }
        
        .empty-cart p {
            color: #888;
        }
        
        .continue-shopping {
            display: inline-block;
            margin-top: 20px;
            background: #0b4f2e;
            color: white;
            padding: 12px 30px;
            border-radius: 40px;
            text-decoration: none;
            font-weight: 600;
            transition: 0.3s;
        }
        
        .continue-shopping:hover {
            background: #1e6b42;
        }
        
        /* Footer */
        .footer {
            background: #0c3020;
            color: #d9ecd9;
            padding: 45px 5% 30px;
            margin-top: 60px;
        }
        
        .footer-links {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 40px;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .footer-col p {
            margin: 10px 0;
            font-size: 13px;
            cursor: pointer;
            transition: 0.3s;
        }
        
        .footer-col p:hover {
            color: white;
            padding-left: 5px;
        }
        
        .footer-col p strong {
            color: white;
            font-size: 15px;
            margin-bottom: 15px;
            display: inline-block;
            cursor: default;
        }
        
        .copy {
            text-align: center;
            padding: 20px;
            background: #0a281b;
            color: #b8dec0;
            font-size: 12px;
            border-top: 1px solid #1e4a35;
            margin-top: 35px;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .logo-area {
                flex-direction: column;
                text-align: center;
                gap: 15px;
            }
            .cart-table th, .cart-table td {
                padding: 10px;
            }
            .product-info {
                flex-direction: column;
                text-align: center;
            }
            .cart-footer {
                flex-direction: column;
                text-align: center;
            }
            .footer-links {
                grid-template-columns: repeat(2, 1fr);
                text-align: center;
            }
        }
        
        @media (max-width: 480px) {
            .footer-links {
                grid-template-columns: 1fr;
            }
            .container {
                padding: 20px 4%;
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
        <a href="../index.php">
            <h1>DocoMart <span>For better health</span></h1>
        </a>
    </div>
    <a href="../index.php" class="back-home"><i class="fas fa-arrow-left"></i> Continue Shopping</a>
</div>

<main class="main-content">
    <div class="container">
        <h2 class="cart-title"><i class="fas fa-shopping-cart"></i> Your Shopping Cart</h2>
        
        <?php if (count($cart_items) > 0): ?>
            <form method="POST">
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Subtotal</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cart_items as $item): 
                            $subtotal = $item['price'] * $item['quantity'];
                        ?>
                            <tr>
                                <td>
                                    <div class="product-info">
                                        <img src="../backend/uploads/<?php echo $item['image'] ?: 'placeholder.jpg'; ?>" 
                                             onerror="this.src='https://via.placeholder.com/60'">
                                        <div>
                                            <div class="product-name"><?php echo htmlspecialchars($item['name']); ?></div>
                                            <small style="color:#8ba888;"><?php echo ucfirst($item['category'] ?? 'General'); ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td>৳<?php echo number_format($item['price'], 2); ?></td>
                                <td>
                                    <input type="number" name="quantity[<?php echo $item['cart_id']; ?>]" 
                                           value="<?php echo $item['quantity']; ?>" min="0" max="99" 
                                           class="quantity-input">
                                </td>
                                <td>৳<?php echo number_format($subtotal, 2); ?></td>
                                <td>
                                    <a href="?remove=<?php echo $item['cart_id']; ?>" class="remove-btn" 
                                       onclick="return confirm('Remove this item?')">
                                        <i class="fas fa-trash"></i> Remove
                                    </a>
                                </td>
                             </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <div class="cart-footer">
                    <div class="cart-total">
                        Total: ৳<?php echo number_format($total, 2); ?>
                    </div>
                    <div>
                        <button type="submit" name="update_cart" class="update-btn">
                            <i class="fas fa-sync-alt"></i> Update Cart
                        </button>
                        <button type="button" class="checkout-btn" onclick="window.location.href='checkout.php'">
                            <i class="fas fa-credit-card"></i> Proceed to Checkout
                        </button>
                    </div>
                </div>
            </form>
        <?php else: ?>
            <div class="empty-cart">
                <i class="fas fa-shopping-cart"></i>
                <h3>Your cart is empty</h3>
                <p>Looks like you haven't added any items to your cart yet.</p>
                <a href="../index.php" class="continue-shopping">Continue Shopping</a>
            </div>
        <?php endif; ?>
    </div>
</main>

<footer class="footer">
    <div class="footer-links">
        <div class="footer-col">
            <p><strong>EXPLORE</strong></p>
            <p>Medicine</p>
            <p>Lab Test</p>
            <p>Health Corner</p>
            <p>Offers</p>
        </div>
        <div class="footer-col">
            <p><strong>POLICIES</strong></p>
            <p>Return Policy</p>
            <p>Shipping</p>
            <p>Privacy</p>
            <p>Terms</p>
        </div>
        <div class="footer-col">
            <p><strong>CONTACT</strong></p>
            <p>Help Center</p>
            <p>WhatsApp Us</p>
            <p>Email Support</p>
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

</body>
</html>