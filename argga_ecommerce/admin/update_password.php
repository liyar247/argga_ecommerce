<?php
require_once '../backend/db_connect.php';

// সঠিকভাবে পাসওয়ার্ড হ্যাশ করুন
$email = 'admin@argga.com';
$plain_password = 'admin123';
$hashed_password = password_hash($plain_password, PASSWORD_DEFAULT);

echo "Original Password: " . $plain_password . "<br>";
echo "New Hash: " . $hashed_password . "<br><br>";

// আপডেট করুন
$stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
$stmt->bind_param("ss", $hashed_password, $email);

if ($stmt->execute()) {
    echo "✅ Password updated successfully!<br>";
    
    // ভেরিফাই করুন
    $check = $conn->query("SELECT password FROM users WHERE email = '$email'");
    $row = $check->fetch_assoc();
    
    if (password_verify($plain_password, $row['password'])) {
        echo "✅ Verification: Password is correct!<br>";
        echo "You can now login with:<br>";
        echo "Email: admin@argga.com<br>";
        echo "Password: admin123<br>";
    } else {
        echo "❌ Verification failed!";
    }
} else {
    echo "❌ Error: " . $conn->error;
}

$conn->close();
?>