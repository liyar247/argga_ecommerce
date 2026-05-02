<?php
session_start();
header('Content-Type: application/json');
require_once '../backend/db_connect.php';

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$id = $_POST['id'] ?? 0;
$name = $_POST['name'] ?? '';
$price = $_POST['price'] ?? 0;
$old_price = $_POST['old_price'] ?? null;
$discount = $_POST['discount'] ?? '';
$category = $_POST['category'] ?? '';
$stock = $_POST['stock'] ?? 10;
$description = $_POST['description'] ?? '';

$image_name = null;
if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
    $upload_dir = '../backend/uploads/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    $image_name = time() . '_' . basename($_FILES['image']['name']);
    move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $image_name);
    
    $stmt = $conn->prepare("UPDATE products SET name=?, price=?, old_price=?, discount=?, image=?, category=?, stock=?, description=? WHERE id=?");
    $stmt->bind_param("sdssssisi", $name, $price, $old_price, $discount, $image_name, $category, $stock, $description, $id);
} else {
    $stmt = $conn->prepare("UPDATE products SET name=?, price=?, old_price=?, discount=?, category=?, stock=?, description=? WHERE id=?");
    $stmt->bind_param("sdsssisi", $name, $price, $old_price, $discount, $category, $stock, $description, $id);
}

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Product updated successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update product']);
}
$conn->close();
?>