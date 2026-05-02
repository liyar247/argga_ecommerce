<?php
// ফাইল: admin/admin_create.php
require_once '../backend/db_connect.php';

// পাসওয়ার্ড হ্যাশ করুন
$password = 'admin123';
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

echo "Original Password: " . $password . "<br>";
echo "Hashed Password: " . $hashed_password . "<br><br>";

// প্রথমে পুরানো এডমিন ডিলিট করুন
$delete = $conn->prepare("DELETE FROM users WHERE email = 'admin@argga.com'");
$delete->execute();
echo "Old admin deleted (if existed)<br>";

// নতুন এডমিন যোগ করুন
$stmt = $conn->prepare("INSERT INTO users (name, email, password, is_admin) VALUES (?, ?, ?, ?)");
$name = 'Admin';
$email = 'admin@argga.com';
$is_admin = 1;

$stmt->bind_param("sssi", $name, $email, $hashed_password, $is_admin);

if ($stmt->execute()) {
    echo "✅ Admin user created successfully!<br>";
    echo "Email: admin@argga.com<br>";
    echo "Password: admin123<br>";
} else {
    echo "❌ Error: " . $conn->error . "<br>";
}

// ইউজার দেখান
$result = $conn->query("SELECT id, name, email, is_admin FROM users WHERE email = 'admin@argga.com'");
if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    echo "<br>✅ Admin found in database:<br>";
    print_r($user);
} else {
    echo "<br>❌ Admin not found!";
}

$conn->close();
?>