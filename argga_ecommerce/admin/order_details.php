<?php
session_start();
if (!isset($_SESSION['admin_id']) || !isset($_SESSION['is_admin'])) {
    header('Location: index.html');
    exit;
}

$host = 'localhost';
$user = 'root';
$password = '';
$database = 'argga_ecommerce';

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Get order details
$order_query = "SELECT * FROM orders WHERE id = $order_id";
$order_result = $conn->query($order_query);
$order = $order_result->fetch_assoc();

// Get order items
$items_query = "SELECT * FROM order_items WHERE order_id = $order_id";
$items_result = $conn->query($items_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details - Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }
        body { background: #f0f2f5; padding: 20px; }
        .container { max-width: 1000px; margin: 0 auto; }
        .card { background: white; border-radius: 12px; padding: 25px; margin-bottom: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        h2 { color: #0b4f2e; margin-bottom: 20px; }
        .info-row { display: flex; padding: 8px 0; border-bottom: 1px solid #eee; }
        .info-label { width: 150px; font-weight: 600; }
        .info-value { flex: 1; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #eee; }
        th { background: #f8f9fa; }
        .back-btn { background: #0b4f2e; color: white; padding: 10px 20px; border: none; border-radius: 8px; cursor: pointer; text-decoration: none; display: inline-block; }
        .status-select { padding: 5px 10px; border-radius: 5px; border: 1px solid #ddd; }
        .update-btn { background: #3498db; color: white; border: none; padding: 5px 15px; border-radius: 5px; cursor: pointer; margin-left: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <h2><i class="fas fa-shopping-cart"></i> Order Details</h2>
            
            <div class="info-row">
                <div class="info-label">Order Number:</div>
                <div class="info-value"><?php echo $order['order_number']; ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Customer Name:</div>
                <div class="info-value"><?php echo htmlspecialchars($order['user_name']); ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Email:</div>
                <div class="info-value"><?php echo $order['user_email']; ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Phone:</div>
                <div class="info-value"><?php echo $order['user_phone']; ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Delivery Address:</div>
                <div class="info-value"><?php echo nl2br(htmlspecialchars($order['user_address'])); ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Order Date:</div>
                <div class="info-value"><?php echo date('d M Y, h:i A', strtotime($order['order_date'])); ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Payment Method:</div>
                <div class="info-value">Cash on Delivery</div>
            </div>
            
            <h3 style="margin: 20px 0 10px;">Order Items</h3>
            <table>
                <thead>
                    <tr><th>Product</th><th>Price</th><th>Quantity</th><th>Subtotal</th></tr>
                </thead>
                <tbody>
                    <?php while ($item = $items_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                            <td>৳<?php echo number_format($item['product_price'], 2); ?></td>
                            <td><?php echo $item['quantity']; ?></td>
                            <td>৳<?php echo number_format($item['subtotal'], 2); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
                <tfoot>
                    <tr style="font-weight: bold;">
                        <td colspan="3" style="text-align: right;">Total:</td>
                        <td>৳<?php echo number_format($order['total_amount'], 2); ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        
        <div class="card">
            <form method="POST" action="update_order_status.php">
                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                <label><strong>Update Order Status:</strong></label>
                <select name="status" class="status-select">
                    <option value="pending" <?php echo $order['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="processing" <?php echo $order['status'] == 'processing' ? 'selected' : ''; ?>>Processing</option>
                    <option value="shipped" <?php echo $order['status'] == 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                    <option value="delivered" <?php echo $order['status'] == 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                    <option value="cancelled" <?php echo $order['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                </select>
                <button type="submit" class="update-btn">Update Status</button>
            </form>
        </div>
        
        <a href="orders.php" class="back-btn"><i class="fas fa-arrow-left"></i> Back to Orders</a>
    </div>
</body>
</html>