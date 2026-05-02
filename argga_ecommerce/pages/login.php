<?php
session_start();
require_once '../inc/config.php';
require_once '../inc/db_connect.php';

// If already logged in, redirect to home
if (isset($_SESSION['user_id'])) {
    header('Location: http://localhost/argga_ecommerce/index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = 'Please fill all fields';
    } else {
        $stmt = $conn->prepare("SELECT id, name, email, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                
                // সরাসরি মূল পেজে রিডাইরেক্ট
                header('Location: http://localhost/argga_ecommerce/index.php?login=success');
                exit;
            } else {
                $error = 'Invalid password';
            }
        } else {
            $error = 'User not found. Please register first.';
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?php echo SITE_NAME; ?></title>
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
            overflow: hidden;
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
        
        .login-container {
            background: white;
            border-radius: 32px;
            width: 100%;
            max-width: 460px;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);
            position: relative;
            z-index: 1;
            animation: fadeInUp 0.5s ease;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .login-header {
            background: linear-gradient(135deg, #0b4f2e 0%, #1e6b42 100%);
            padding: 40px 30px;
            text-align: center;
            color: white;
        }
        
        .login-header .icon {
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
        
        .login-header h2 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
        }
        
        .login-header p {
            font-size: 14px;
            opacity: 0.9;
        }
        
        .login-form {
            padding: 35px 30px;
        }
        
        .input-group {
            margin-bottom: 24px;
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
        
        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            font-size: 13px;
        }
        
        .checkbox {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }
        
        .checkbox input {
            width: 16px;
            height: 16px;
            cursor: pointer;
            accent-color: #0b4f2e;
        }
        
        .checkbox span {
            color: #666;
        }
        
        .forgot-link {
            color: #0b4f2e;
            text-decoration: none;
            font-weight: 500;
            transition: 0.3s;
        }
        
        .forgot-link:hover {
            text-decoration: underline;
        }
        
        .login-btn {
            width: 100%;
            background: linear-gradient(135deg, #0b4f2e 0%, #1e6b42 100%);
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
            margin-bottom: 20px;
        }
        
        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(11,79,46,0.2);
        }
        
        .register-section {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #e8f0e8;
        }
        
        .register-section p {
            color: #666;
            font-size: 14px;
        }
        
        .register-link {
            color: #ff8c42;
            text-decoration: none;
            font-weight: 700;
            transition: 0.3s;
        }
        
        .register-link:hover {
            text-decoration: underline;
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
        
        .back-home {
            text-align: center;
            margin-top: 20px;
        }
        
        .back-home a {
            color: #8ba888;
            text-decoration: none;
            font-size: 13px;
            transition: 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        
        .back-home a:hover {
            color: #0b4f2e;
        }
        
        @media (max-width: 480px) {
            .login-container {
                border-radius: 24px;
            }
            .login-header {
                padding: 30px 20px;
            }
            .login-form {
                padding: 25px 20px;
            }
            .login-header .icon {
                width: 55px;
                height: 55px;
                font-size: 26px;
            }
            .login-header h2 {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <div class="icon">
                <i class="fas fa-laptop-medical"></i>
            </div>
            <h2>Welcome Back!</h2>
            <p>Login to your account for better health</p>
        </div>
        
        <div class="login-form">
            <?php if ($error): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="input-group">
                    <label><i class="fas fa-envelope"></i> Email Address</label>
                    <i class="fas fa-envelope input-icon"></i>
                    <input type="email" name="email" placeholder="Enter your email" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                </div>
                
                <div class="input-group">
                    <label><i class="fas fa-lock"></i> Password</label>
                    <i class="fas fa-lock input-icon"></i>
                    <input type="password" name="password" placeholder="Enter your password" required>
                </div>
                
                <div class="form-options">
                    <label class="checkbox">
                        <input type="checkbox" name="remember">
                        <span>Remember me</span>
                    </label>
                    <a href="#" class="forgot-link">Forgot password?</a>
                </div>
                
                <button type="submit" class="login-btn">
                    <i class="fas fa-sign-in-alt"></i> Login
                </button>
            </form>
            
            <div class="register-section">
                <p>Don't have an account? <a href="register.php" class="register-link">Create Account</a></p>
            </div>
            
            <div class="back-home">
                <a href="http://localhost/argga_ecommerce/index.php">
                    <i class="fas fa-arrow-left"></i> Back to Home
                </a>
            </div>
        </div>
    </div>
</body>
</html>