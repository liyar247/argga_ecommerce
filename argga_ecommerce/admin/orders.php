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

// Get all orders
$orders_query = "SELECT * FROM orders ORDER BY order_date DESC";
$orders_result = $conn->query($orders_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders - Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
        body { background: #f0f2f5; }
        .admin-container { display: flex; }
        .sidebar {
            width: 260px;
            background: #0b4f2e;
            color: white;
            height: 100vh;
            position: fixed;
            padding: 20px 0;
        }
        .sidebar h2 { padding: 0 20px 20px; border-bottom: 1px solid #2e7d5a; margin-bottom: 20px; }
        .sidebar nav a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 20px;
            color: white;
            text-decoration: none;
            transition: 0.3s;
        }
        .sidebar nav a:hover, .sidebar nav a.active { background: #1e6b42; }
        .main-content { flex: 1; margin-left: 260px; padding: 20px; }
        .header {
            background: white;
            padding: 15px 25px;
            border-radius: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }
        .logout-btn {
            background: #e74c3c;
            color: white;
            padding: 8px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
        }
        .orders-section {
            background: white;
            border-radius: 12px;
            padding: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        th { background: #f8f9fa; font-weight: 600; }
        .status-pending { background: #fff3cd; color: #856404; padding: 4px 12px; border-radius: 20px; font-size: 12px; display: inline-block; }
        .status-processing { background: #cce5ff; color: #004085; padding: 4px 12px; border-radius: 20px; font-size: 12px; display: inline-block; }
        .view-btn {
            background: #3498db;
            color: white;
            border: none;
            padding: 5px 12px;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="sidebar">
            <h2><i class="fas fa-store"></i> argGa Admin</h2>
            <nav>
                <a href="dashboard.php"><i class="fas fa-box"></i> Products</a>
                <a href="orders.php" class="active"><i class="fas fa-shopping-cart"></i> Orders</a>
                <a href="#"><i class="fas fa-users"></i> Customers</a>
            </nav>
        </div>
        
        <div class="main-content">
            <div class="header">
                <h2>Orders Management</h2>
                <a href="logout.php" class="logout-btn">Logout</a>
            </div>
            
            <div class="orders-section">
                <h3><i class="fas fa-shopping-cart"></i> All Orders</h3>
                <div style="overflow-x: auto; margin-top: 20px;">
                    <table>
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Order Number</th>
                                <th>Customer</th>
                                <th>Phone</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($orders_result && $orders_result->num_rows > 0): ?>
                                <?php while ($order = $orders_result->fetch_assoc()): ?>
                                    <tr>
                                        <td>#<?php echo $order['id']; ?></td>
                                        <td><?php echo $order['order_number']; ?></td>
                                        <td><?php echo htmlspecialchars($order['user_name']); ?></td>
                                        <td><?php echo $order['user_phone']; ?></td>
                                        <td>৳<?php echo number_format($order['total_amount'], 2); ?></td>
                                        <td>
                                            <span class="status-<?php echo $order['status']; ?>">
                                                <?php echo ucfirst($order['status']); ?>
                                            </span>
                                         </td>
                                        <td><?php echo date('d M Y', strtotime($order['order_date'])); ?></td>
                                        <td>
                                            <button class="view-btn" onclick="window.location.href='order_details.php?id=<?php echo $order['id']; ?>'">
                                                <i class="fas fa-eye"></i> View
                                            </button>
                                         </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="8" style="text-align:center;">No orders yet</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>