<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$host = 'localhost';
$user = 'root';
$password = '';
$database = 'argga_ecommerce';

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'cart' => [], 'total' => 0]);
    exit;
}

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'cart' => [], 'total' => 0]);
    exit;
}

$user_id = $_SESSION['user_id'];

$query = "SELECT c.id as cart_id, c.product_id, c.quantity, p.name, p.price, p.image 
          FROM cart c 
          JOIN products p ON c.product_id = p.id 
          WHERE c.user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$cart = [];
$total = 0;

while ($row = $result->fetch_assoc()) {
    $subtotal = $row['price'] * $row['quantity'];
    $total += $subtotal;
    $cart[] = [
        'cart_id' => $row['cart_id'],
        'product_id' => $row['product_id'],
        'name' => $row['name'],
        'price' => floatval($row['price']),
        'quantity' => intval($row['quantity']),
        'image' => $row['image'],
        'subtotal' => $subtotal
    ];
}

echo json_encode([
    'success' => true,
    'cart' => $cart,
    'total' => $total,
    'count' => count($cart)
]);

$conn->close();
?>