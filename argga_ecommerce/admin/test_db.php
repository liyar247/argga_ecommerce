<?php
require_once '../backend/db_connect.php';

echo "<h2>Database Connection Test</h2>";

// Check users table
$result = $conn->query("SELECT id, name, email, is_admin FROM users");
echo "<h3>Users in database:</h3>";
if ($result->num_rows > 0) {
    echo "<table border='1' cellpadding='8'>";
    echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Is Admin</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['name'] . "</td>";
        echo "<td>" . $row['email'] . "</td>";
        echo "<td>" . ($row['is_admin'] ? 'Yes' : 'No') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "No users found!<br>";
}

// Test password for admin
$admin_result = $conn->query("SELECT * FROM users WHERE email = 'admin@argga.com'");
if ($admin_result->num_rows > 0) {
    $admin = $admin_result->fetch_assoc();
    echo "<h3>Admin Check:</h3>";
    echo "Email: " . $admin['email'] . "<br>";
    echo "Is Admin: " . ($admin['is_admin'] ? 'Yes' : 'No') . "<br>";
    echo "Password Hash: " . $admin['password'] . "<br>";
    
    // Test password verification
    $test_password = 'admin123';
    if (password_verify($test_password, $admin['password'])) {
        echo "<span style='color:green'>✅ Password 'admin123' is correct!</span><br>";
    } else {
        echo "<span style='color:red'>❌ Password 'admin123' does NOT match!</span><br>";
        echo "You need to reset the password.<br>";
    }
}

$conn->close();
?>