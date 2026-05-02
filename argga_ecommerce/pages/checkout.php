<?php
session_start();
require_once '../inc/db_connect.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];
$user_email = $_SESSION['user_email'];

// Get cart items
$query = "SELECT c.id as cart_id, c.quantity, p.id as product_id, p.name, p.price, p.image 
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

// If cart is empty, redirect to cart page
if (count($cart_items) == 0) {
    header('Location: cart.php');
    exit;
}

$message = '';
$message_type = '';
$order_placed = false;
$order_number = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    
    if (empty($phone) || empty($address)) {
        $message = 'Please fill all fields';
        $message_type = 'error';
    } else {
        // Generate unique order number
        $order_number = 'ORD-' . date('Ymd') . '-' . rand(1000, 9999);
        
        // Insert into orders table
        $sql = "INSERT INTO orders (order_number, user_id, user_name, user_email, user_phone, user_address, total_amount, status) 
                VALUES ('$order_number', $user_id, '$user_name', '$user_email', '$phone', '$address', $total, 'pending')";
        
        if ($conn->query($sql)) {
            $order_id = $conn->insert_id;
            
            // Insert order items
            foreach ($cart_items as $item) {
                $subtotal = $item['price'] * $item['quantity'];
                $sql2 = "INSERT INTO order_items (order_id, product_id, product_name, product_price, quantity, subtotal) 
                         VALUES ($order_id, {$item['product_id']}, '{$item['name']}', {$item['price']}, {$item['quantity']}, $subtotal)";
                $conn->query($sql2);
            }
            
            // Clear cart
            $conn->query("DELETE FROM cart WHERE user_id = $user_id");
            
            $message = 'Order placed successfully! Your order number is: ' . $order_number;
            $message_type = 'success';
            $order_placed = true;
            
            // Clear cart items array
            $cart_items = [];
            $total = 0;
            $total_items = 0;
        } else {
            $message = 'Failed to place order: ' . $conn->error;
            $message_type = 'error';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - argGa</title>
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
        .back-home {
            background: #0b4f2e;
            color: white;
            padding: 10px 24px;
            border-radius: 40px;
            text-decoration: none;
            font-weight: 600;
        }
        .checkout-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 5%;
        }
        .checkout-header {
            text-align: center;
            margin-bottom: 40px;
        }
        .checkout-header h1 {
            font-size: 32px;
            color: #0b4f2e;
            margin-bottom: 10px;
        }
        .checkout-wrapper {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }
        .order-summary {
            background: white;
            border-radius: 24px;
            padding: 25px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            height: fit-content;
        }
        .order-summary h2 {
            font-size: 20px;
            color: #1a2e1f;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e8f0e8;
        }
        .order-items {
            max-height: 400px;
            overflow-y: auto;
            margin-bottom: 20px;
        }
        .order-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        .order-item-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .order-item-info img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 10px;
            background: #f5f9f5;
        }
        .order-item-info .item-name {
            font-weight: 600;
            color: #1a2e1f;
        }
        .order-item-info .item-qty {
            font-size: 12px;
            color: #8ba888;
        }
        .order-item-price {
            font-weight: 700;
            color: #0b4f2e;
        }
        .order-total {
            padding-top: 15px;
            border-top: 2px solid #e8f0e8;
        }
        .order-total-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
        }
        .order-total-row.grand-total {
            font-size: 20px;
            font-weight: 800;
            color: #0b4f2e;
            border-top: 2px solid #e8f0e8;
            margin-top: 10px;
            padding-top: 15px;
        }
        .delivery-info {
            background: white;
            border-radius: 24px;
            padding: 25px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        }
        .delivery-info h2 {
            font-size: 20px;
            color: #1a2e1f;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e8f0e8;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #333;
        }
        .form-group input, .form-group textarea {
            width: 100%;
            padding: 14px 16px;
            border: 1px solid #ddd;
            border-radius: 12px;
            font-size: 14px;
        }
        .form-group input:focus, .form-group textarea:focus {
            outline: none;
            border-color: #0b4f2e;
        }
        .form-group input[readonly] {
            background: #f5f5f5;
        }
        .place-order-btn {
            width: 100%;
            background: #ff8c42;
            color: white;
            border: none;
            padding: 16px;
            border-radius: 50px;
            font-size: 18px;
            font-weight: 700;
            cursor: pointer;
            transition: 0.3s;
            margin-top: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        .place-order-btn:hover {
            background: #e07a2e;
        }
        .alert {
            padding: 15px 20px;
            border-radius: 12px;
            margin-bottom: 20px;
        }
        .alert-success {
            background: #e8f5e9;
            color: #2e7d32;
            border-left: 4px solid #2e7d32;
        }
        .alert-error {
            background: #fee;
            color: #e74c3c;
            border-left: 4px solid #e74c3c;
        }
        .success-page {
            text-align: center;
            padding: 60px 5%;
            background: white;
            border-radius: 24px;
            margin: 40px auto;
            max-width: 500px;
        }
        .success-page i {
            font-size: 80px;
            color: #27ae60;
            margin-bottom: 20px;
        }
        .success-page h2 {
            color: #0b4f2e;
            margin-bottom: 15px;
        }
        .success-page .order-number {
            background: #f0f4f0;
            padding: 10px;
            border-radius: 8px;
            font-family: monospace;
            font-size: 16px;
            margin: 15px 0;
        }
        .success-page .btn-home {
            display: inline-block;
            margin-top: 25px;
            background: #0b4f2e;
            color: white;
            padding: 12px 30px;
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
        @media (max-width: 768px) {
            .checkout-wrapper {
                grid-template-columns: 1fr;
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
        <h1>DocoMart <span>For better health</span></h1>
    </div>
    <a href="cart.php" class="back-home"><i class="fas fa-arrow-left"></i> Back to Cart</a>
</div>

<div class="checkout-container">
    <?php if ($order_placed): ?>
        <div class="success-page">
            <i class="fas fa-check-circle"></i>
            <h2>Order Confirmed! 🎉</h2>
            <p><?php echo $message; ?></p>
            <div class="order-number">
                <strong>Order Number:</strong> <?php echo $order_number; ?>
            </div>
            <p>A confirmation has been sent to your email.</p>
            <a href="../index.php" class="btn-home">Continue Shopping</a>
        </div>
    <?php else: ?>
        <div class="checkout-header">
            <h1><i class="fas fa-credit-card"></i> Checkout</h1>
            <p>Complete your purchase by providing delivery information</p>
        </div>
        
        <?php if ($message && $message_type == 'error'): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <div class="checkout-wrapper">
            <div class="order-summary">
                <h2><i class="fas fa-shopping-cart"></i> Order Summary</h2>
                <div class="order-items">
                    <?php foreach ($cart_items as $item): ?>
                        <div class="order-item">
                            <div class="order-item-info">
                                <img src="../backend/uploads/<?php echo $item['image'] ?: 'placeholder.jpg'; ?>" 
                                     onerror="this.src='https://via.placeholder.com/50'">
                                <div>
                                    <div class="item-name"><?php echo htmlspecialchars($item['name']); ?></div>
                                    <div class="item-qty">Quantity: <?php echo $item['quantity']; ?></div>
                                </div>
                            </div>
                            <div class="order-item-price">
                                ৳<?php echo number_format($item['price'] * $item['quantity'], 2); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="order-total">
                    <div class="order-total-row">
                        <span>Subtotal (<?php echo $total_items; ?> items)</span>
                        <span>৳<?php echo number_format($total, 2); ?></span>
                    </div>
                    <div class="order-total-row grand-total">
                        <span>Total Amount</span>
                        <span>৳<?php echo number_format($total, 2); ?></span>
                    </div>
                </div>
            </div>
            
            <div class="delivery-info">
                <h2><i class="fas fa-truck"></i> Delivery Information</h2>
                <form method="POST">
                    <div class="form-group">
                        <label><i class="fas fa-user"></i> Full Name</label>
                        <input type="text" value="<?php echo htmlspecialchars($user_name); ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-envelope"></i> Email Address</label>
                        <input type="email" value="<?php echo htmlspecialchars($user_email); ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-phone"></i> Phone Number <span style="color: #e74c3c;">*</span></label>
                        <input type="tel" name="phone" placeholder="Enter your phone number" required>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-map-marker-alt"></i> Delivery Address <span style="color: #e74c3c;">*</span></label>
                        <textarea name="address" rows="4" placeholder="House #, Road #, Area, City, Postal Code" required></textarea>
                    </div>
                    <button type="submit" name="place_order" class="place-order-btn">
                        <i class="fas fa-check-circle"></i> Place Order (৳<?php echo number_format($total, 2); ?>)
                    </button>
                </form>
            </div>
        </div>
    <?php endif; ?>
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
            <p><i class="far fa-copyright"></i> 2025 argGa health</p>
        </div>
    </div>
</footer>
<div class="copy">
    <p>📍 Delivery Across Bangladesh | Safe & Trusted Pharmacy | 24/7 Support</p>
</div>

</body>
</html>