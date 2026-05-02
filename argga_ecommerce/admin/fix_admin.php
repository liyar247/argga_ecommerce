<?php
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'argga_ecommerce';

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "<h1>🔧 Fixing Admin Account</h1>";

// Delete old admin
$conn->query("DELETE FROM users WHERE email = 'admin@argga.com'");
echo "✓ Old admin deleted<br>";

// Insert new admin
$plain_password = 'admin123';
$hashed_password = password_hash($plain_password, PASSWORD_DEFAULT);

$stmt = $conn->prepare("INSERT INTO users (name, email, password, is_admin) VALUES (?, ?, ?, ?)");
$name = 'Admin';
$email = 'admin@argga.com';
$is_admin = 1;

$stmt->bind_param("sssi", $name, $email, $hashed_password, $is_admin);

if ($stmt->execute()) {
    echo "✓ New admin created successfully!<br><br>";
    echo "<strong>Login Credentials:</strong><br>";
    echo "Email: <strong style='color:green'>admin@argga.com</strong><br>";
    echo "Password: <strong style='color:green'>admin123</strong><br><br>";
    
    // Verify
    $check = $conn->query("SELECT * FROM users WHERE email = 'admin@argga.com'");
    $user = $check->fetch_assoc();
    
    if (password_verify('admin123', $user['password'])) {
        echo "<span style='color:green; font-weight:bold'>✅ Password verification successful!</span><br>";
    } else {
        echo "<span style='color:red'>❌ Password verification failed!</span><br>";
    }
    
    echo "<br><a href='index.html' style='display:inline-block; padding:10px 20px; background:#0b4f2e; color:white; text-decoration:none; border-radius:5px;'>→ Go to Login Page</a>";
    
} else {
    echo "❌ Error: " . $conn->error;
}

$conn->close();
?>