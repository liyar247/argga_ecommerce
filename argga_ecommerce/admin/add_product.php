<?php
session_start();
header('Content-Type: application/json');

// Database connection
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'argga_ecommerce';

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Connection failed: ' . $conn->connect_error]);
    exit;
}

// Check if admin is logged in (optional - remove for testing)
// if (!isset($_SESSION['admin_id'])) {
//     echo json_encode(['success' => false, 'message' => 'Unauthorized']);
//     exit;
// }

$name = $_POST['name'] ?? '';
$price = $_POST['price'] ?? 0;
$old_price = $_POST['old_price'] ?? null;
$discount = $_POST['discount'] ?? '';
$category = $_POST['category'] ?? '';
$stock = $_POST['stock'] ?? 10;
$description = $_POST['description'] ?? '';

// Handle image upload
$image_name = '';
if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
    $upload_dir = '../backend/uploads/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    $image_name = time() . '_' . basename($_FILES['image']['name']);
    move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $image_name);
}

// Insert product
$stmt = $conn->prepare("INSERT INTO products (name, price, old_price, discount, image, category, stock, description) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sdssssis", $name, $price, $old_price, $discount, $image_name, $category, $stock, $description);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Product added successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed: ' . $conn->error]);
}

$stmt->close();
$conn->close();
?>