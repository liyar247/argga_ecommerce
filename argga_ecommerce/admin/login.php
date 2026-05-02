<?php
session_start();
header('Content-Type: application/json');

// Database connection - আপনার ফাইল লোকেশন অনুযায়ী
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'argga_ecommerce';

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $conn->connect_error]);
    exit;
}

// Check if already logged in (GET request)
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['check'])) {
    if (isset($_SESSION['admin_id']) && isset($_SESSION['is_admin'])) {
        echo json_encode(['success' => true, 'user' => ['name' => $_SESSION['admin_name']]]);
    } else {
        echo json_encode(['success' => false]);
    }
    exit;
}

// Logout
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.html');
    exit;
}

// Login request (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $raw_data = file_get_contents('php://input');
    $data = json_decode($raw_data, true);
    
    $email = $data['email'] ?? '';
    $password = $data['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Email and password required']);
        exit;
    }
    
    // Check user
    $stmt = $conn->prepare("SELECT id, name, email, password, is_admin FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // Verify password (both plain text and hashed)
        $valid = false;
        if ($user['password'] === $password) {
            $valid = true;
        } elseif (password_verify($password, $user['password'])) {
            $valid = true;
        }
        
        if ($valid && $user['is_admin'] == 1) {
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_name'] = $user['name'];
            $_SESSION['is_admin'] = true;
            
            echo json_encode([
                'success' => true,
                'message' => 'Login successful',
                'user' => ['name' => $user['name']]
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid password or not admin']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Admin account not found']);
    }
    $stmt->close();
}

$conn->close();
?>