<?php
session_start();
require_once '../../inc/config.php';
require_once '../../inc/db_connect.php';

$page_title = "Medicine";
$page_description = "Quality medicines for better health. Shop from our wide range of prescription and OTC medicines.";
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
        body { background: #f0f4f0; display: flex; flex-direction: column; min-height: 100vh; }
        .top-bar { background: #0b4f2e; color: white; padding: 10px 5%; display: flex; justify-content: space-between; font-size: 13px; }
        .logo-area { background: white; padding: 15px 5%; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; }
        .logo h1 { color: #0b4f2e; font-size: 28px; }
        .logo span { font-size: 12px; color: #5a8f5c; display: block; }
        .logo a { text-decoration: none; }
        .back-home { background: #0b4f2e; color: white; padding: 10px 24px; border-radius: 40px; text-decoration: none; font-weight: 600; }
        .container { flex: 1; padding: 40px 5%; max-width: 1200px; margin: 0 auto; width: 100%; }
        .page-header { text-align: center; margin-bottom: 40px; }
        .page-header h1 { font-size: 36px; color: #0b4f2e; margin-bottom: 15px; }
        .page-header p { color: #666; font-size: 16px; }
        .content-card { background: white; border-radius: 24px; padding: 30px; margin-bottom: 30px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); }
        .content-card h2 { color: #0b4f2e; margin-bottom: 20px; font-size: 24px; }
        .content-card p { line-height: 1.6; color: #555; margin-bottom: 15px; }
        .feature-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 25px; margin-top: 30px; }
        .feature-item { text-align: center; padding: 20px; background: #f8faf8; border-radius: 16px; }
        .feature-item i { font-size: 40px; color: #0b4f2e; margin-bottom: 15px; }
        .feature-item h3 { margin-bottom: 10px; color: #1a2e1f; }
        .footer { background: #0c3020; color: #d9ecd9; padding: 40px 5% 25px; margin-top: 60px; }
        .footer-links { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 30px; max-width: 1200px; margin: 0 auto; }
        .footer-col p { margin: 8px 0; font-size: 13px; }
        .footer-col p strong { color: white; }
        .copy { text-align: center; padding: 20px; background: #0a281b; color: #b8dec0; font-size: 12px; }
        @media (max-width: 768px) { .logo-area { flex-direction: column; text-align: center; gap: 15px; } }
    </style>
</head>
<body>

<div class="top-bar">
    <span><i class="fas fa-heartbeat"></i> For better health • Delivery to Bangladesh</span>
    <span><i class="fas fa-phone-alt"></i> Call to Order | Upload Prescription</span>
</div>

<div class="logo-area">
    <div class="logo">
        <a href="../../index.php">
            <h1>argGa <span>For better health</span></h1>
        </a>
    </div>
    <a href="../../index.php" class="back-home"><i class="fas fa-arrow-left"></i> Back to Home</a>
</div>

<div class="container">
    <div class="page-header">
        <h1><i class="fas fa-capsules"></i> <?php echo $page_title; ?></h1>
        <p><?php echo $page_description; ?></p>
    </div>
    
    <div class="content-card">
        <h2>About Our Medicine Collection</h2>
        <p>At argGa, we provide authentic, high-quality medicines sourced from trusted manufacturers. Our extensive range includes prescription drugs, over-the-counter medications, and generic alternatives to suit every need.</p>
        <p>All our medicines are stored in temperature-controlled facilities and delivered with proper handling to ensure efficacy and safety.</p>
    </div>
    
    <div class="feature-grid">
        <div class="feature-item"><i class="fas fa-check-circle"></i><h3>100% Authentic</h3><p>Genuine medicines from certified suppliers</p></div>
        <div class="feature-item"><i class="fas fa-temperature-low"></i><h3>Proper Storage</h3><p>Temperature-controlled facilities</p></div>
        <div class="feature-item"><i class="fas fa-truck"></i><h3>Fast Delivery</h3><p>Free delivery on orders over ৳500</p></div>
        <div class="feature-item"><i class="fas fa-prescription-bottle"></i><h3>Prescription Required</h3><p>For select medications</p></div>
    </div>
</div>

<footer class="footer">
    <div class="footer-links">
        <div class="footer-col"><p><strong>EXPLORE</strong></p><p>Medicine</p><p>Lab Test</p><p>Health Corner</p><p>Offers</p></div>
        <div class="footer-col"><p><strong>POLICIES</strong></p><p>Return Policy</p><p>Shipping</p><p>Privacy</p><p>Terms</p></div>
        <div class="footer-col"><p><strong>CONTACT</strong></p><p>Help Center</p><p>WhatsApp Us</p><p>Email Support</p></div>
        <div class="footer-col"><p><i class="fas fa-truck"></i> <strong>ORDER NOW</strong></p><p><i class="fas fa-phone"></i> 09678 111 222</p><p>© 2025 argGa health</p></div>
    </div>
</footer>
<div class="copy"><p>📍 Delivery Across Bangladesh | Safe & Trusted Pharmacy | 24/7 Support</p></div>
</body>
</html>