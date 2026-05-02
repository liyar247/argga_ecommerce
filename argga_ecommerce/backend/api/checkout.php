<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../../inc/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login first']);
    exit;
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

// Get cart items to calculate total
$query = "SELECT SUM(p.price * c.quantity) as total 
          FROM cart c 
          JOIN products p ON c.product_id = p.id 
          WHERE c.user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$total = $row['total'] ?? 0;

if ($total <= 0) {
    echo json_encode(['success' => false, 'message' => 'Cart is empty']);
    exit;
}

// Clear cart (place order)
$delete = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
$delete->bind_param("i", $user_id);

if ($delete->execute()) {
    echo json_encode([
        'success' => true, 
        'message' => 'Order placed successfully! Total: ৳' . number_format($total, 2),
        'order_total' => $total
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to place order']);
}

$stmt->close();
$delete->close();
$conn->close();
?>