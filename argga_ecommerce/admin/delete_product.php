<?php
session_start();
header('Content-Type: application/json');

// Check if admin is logged in
if (!isset($_SESSION['admin_id']) || !isset($_SESSION['is_admin'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$host = 'localhost';
$user = 'root';
$password = '';
$database = 'argga_ecommerce';

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Connection failed']);
    exit;
}

// Get product ID from request
$data = json_decode(file_get_contents('php://input'), true);
$product_id = isset($data['id']) ? (int)$data['id'] : 0;

if ($product_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid product ID']);
    exit;
}

// First, delete product image if exists
$img_query = "SELECT image FROM products WHERE id = $product_id";
$img_result = $conn->query($img_query);
if ($img_result && $row = $img_result->fetch_assoc()) {
    $image = $row['image'];
    if ($image && file_exists("../backend/uploads/" . $image)) {
        unlink("../backend/uploads/" . $image);
    }
}

// Delete product from database
$delete_query = "DELETE FROM products WHERE id = $product_id";
if ($conn->query($delete_query)) {
    echo json_encode(['success' => true, 'message' => 'Product deleted successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to delete: ' . $conn->error]);
}

$conn->close();
?>