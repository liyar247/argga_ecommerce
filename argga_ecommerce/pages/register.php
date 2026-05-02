<?php
session_start();
require_once '../inc/config.php';
require_once '../inc/db_connect.php';

if (isset($_SESSION['user_id'])) {
    header('Location: ' . SITE_URL);
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';
    
    if (empty($name) || empty($email) || empty($password)) {
        $error = 'All fields are required';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email address';
    } else {
        $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();
        
        if ($check->num_rows > 0) {
            $error = 'Email already registered';
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $name, $email, $hashed);
            
            if ($stmt->execute()) {
                $success = 'Registration successful! Please login.';
                $_POST = [];
            } else {
                $error = 'Registration failed';
            }
            $stmt->close();
        }
        $check->close();
    }
}
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0b4f2e 0%, #1e6b42 50%, #0b4f2e 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            position: relative;
        }
        body::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.05) 1px, transparent 1px);
            background-size: 40px 40px;
            animation: moveBackground 60s linear infinite;
        }
        @keyframes moveBackground {
            0% { transform: translate(0, 0); }
            100% { transform: translate(40px, 40px); }
        }
        .register-container {
            background: white;
            border-radius: 32px;
            width: 100%;
            max-width: 500px;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);
            position: relative;
            z-index: 1;
            animation: fadeInUp 0.5s ease;
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .register-header {
            background: linear-gradient(135deg, #0b4f2e 0%, #1e6b42 100%);
            padding: 40px 30px;
            text-align: center;
            color: white;
        }
        .register-header .icon {
            width: 70px;
            height: 70px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 32px;
        }
        .register-header h2 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
        }
        .register-header p {
            font-size: 14px;
            opacity: 0.9;
        }
        .register-form {
            padding: 35px 30px;
        }
        .input-group {
            margin-bottom: 20px;
            position: relative;
        }
        .input-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #333;
            font-size: 14px;
        }
        .input-group label i {
            margin-right: 8px;
            color: #0b4f2e;
        }
        .input-group input {
            width: 100%;
            padding: 14px 16px 14px 45px;
            border: 2px solid #e8f0e8;
            border-radius: 16px;
            font-size: 15px;
            transition: all 0.3s;
            background: #fafdfa;
        }
        .input-group input:focus {
            outline: none;
            border-color: #0b4f2e;
            background: white;
            box-shadow: 0 0 0 4px rgba(11,79,46,0.1);
        }
        .input-group .input-icon {
            position: absolute;
            left: 16px;
            bottom: 16px;
            color: #8ba888;
            font-size: 16px;
        }
        .register-btn {
            width: 100%;
            background: linear-gradient(135deg, #ff8c42 0%, #ff6b2c 100%);
            color: white;
            border: none;
            padding: 14px;
            border-radius: 40px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 10px;
        }
        .register-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(255,140,66,0.3);
        }
        .login-section {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #e8f0e8;
            margin-top: 10px;
        }
        .login-section p {
            color: #666;
            font-size: 14px;
        }
        .login-link {
            color: #0b4f2e;
            text-decoration: none;
            font-weight: 700;
        }
        .alert {
            padding: 14px 18px;
            border-radius: 16px;
            margin-bottom: 25px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .alert-error {
            background: #fee;
            color: #e74c3c;
            border-left: 4px solid #e74c3c;
        }
        .alert-success {
            background: #e8f5e9;
            color: #2e7d32;
            border-left: 4px solid #2e7d32;
        }
        .back-home {
            text-align: center;
            margin-top: 20px;
        }
        .back-home a {
            color: #8ba888;
            text-decoration: none;
            font-size: 13px;
        }
        @media (max-width: 480px) {
            .register-container { border-radius: 24px; }
            .register-header { padding: 30px 20px; }
            .register-form { padding: 25px 20px; }
            .register-header .icon { width: 55px; height: 55px; font-size: 26px; }
            .register-header h2 { font-size: 24px; }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-header">
            <div class="icon">
                <i class="fas fa-user-plus"></i>
            </div>
            <h2>Create Account</h2>
            <p>Join us for better health</p>
        </div>
        
        <div class="register-form">
            <?php if ($error): ?>
                <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?php echo $error; ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?php echo $success; ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="input-group">
                    <label><i class="fas fa-user"></i> Full Name</label>
                    <i class="fas fa-user input-icon"></i>
                    <input type="text" name="name" placeholder="Enter your full name" required value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
                </div>
                <div class="input-group">
                    <label><i class="fas fa-envelope"></i> Email Address</label>
                    <i class="fas fa-envelope input-icon"></i>
                    <input type="email" name="email" placeholder="Enter your email" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                </div>
                <div class="input-group">
                    <label><i class="fas fa-lock"></i> Password</label>
                    <i class="fas fa-lock input-icon"></i>
                    <input type="password" name="password" placeholder="Minimum 6 characters" required>
                </div>
                <div class="input-group">
                    <label><i class="fas fa-lock"></i> Confirm Password</label>
                    <i class="fas fa-lock input-icon"></i>
                    <input type="password" name="confirm_password" placeholder="Re-enter your password" required>
                </div>
                <button type="submit" class="register-btn">
                    <i class="fas fa-user-plus"></i> Create Account
                </button>
            </form>
            
            <div class="login-section">
                <p>Already have an account? <a href="login.php" class="login-link">Sign In</a></p>
            </div>
            <div class="back-home">
                <a href="<?php echo SITE_URL; ?>"><i class="fas fa-arrow-left"></i> Back to Home</a>
            </div>
        </div>
    </div>
</body>
</html>