<?php
require_once '../backend/db_connect.php';

echo "<h2>Admin Account Check</h2>";

// Check all users
$result = $conn->query("SELECT id, name, email, password, is_admin FROM users");
echo "<h3>All Users:</h3>";
echo "<table border='1' cellpadding='8'>";
echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Password</th><th>Is Admin</th></tr>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>{$row['id']}</td>";
    echo "<td>{$row['name']}</td>";
    echo "<td>{$row['email']}</td>";
    echo "<td>" . substr($row['password'], 0, 30) . "...</td>";
    echo "<td>" . ($row['is_admin'] ? '✅ Yes' : '❌ No') . "</td>";
    echo "</tr>";
}
echo "</table>";

// Test admin login
echo "<h3>Test Admin Login:</h3>";
$admin = $conn->query("SELECT * FROM users WHERE email = 'admin@argga.com'");
if ($admin->num_rows > 0) {
    $admin_data = $admin->fetch_assoc();
    echo "Email: admin@argga.com<br>";
    echo "Password in DB: " . $admin_data['password'] . "<br>";
    echo "Is Admin: " . ($admin_data['is_admin'] ? 'Yes' : 'No') . "<br>";
    
    // Test password
    $test_pass = 'admin123';
    if ($admin_data['password'] === $test_pass) {
        echo "<span style='color:green'>✅ Password matches as plain text!</span><br>";
    } elseif (password_verify($test_pass, $admin_data['password'])) {
        echo "<span style='color:green'>✅ Password matches as hash!</span><br>";
    } else {
        echo "<span style='color:red'>❌ Password does NOT match!</span><br>";
        echo "Please run this query in phpMyAdmin:<br>";
        echo "<code>UPDATE users SET password = 'admin123' WHERE email = 'admin@argga.com';</code><br>";
    }
} else {
    echo "<span style='color:red'>❌ Admin not found! Run this query:<br>";
    echo "<code>INSERT INTO users (name, email, password, is_admin) VALUES ('Admin', 'admin@argga.com', 'admin123', 1);</code></span>";
}

$conn->close();
?>