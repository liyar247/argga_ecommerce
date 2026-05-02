<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?> | <?php echo SITE_TAGLINE; ?> - Bangladesh</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>css/style.css">
    <style>
        /* ফুটার নিচে থাকার জন্য অতিরিক্ত CSS */
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .main-content {
            flex: 1;
        }
        .container {
            flex: 1;
        }
        footer.footer {
            margin-top: auto;
        }
    </style>
</head>
<body>

<div class="top-bar">
    <span><i class="fas fa-heartbeat"></i> <?php echo SITE_TAGLINE; ?> • Delivery to Bangladesh</span>
    <span><i class="fas fa-phone-alt"></i> Call to Order | <i class="fas fa-upload"></i> Upload Prescription</span>
</div>

<div class="logo-area">
    <div class="logo">
        <a href="<?php echo SITE_URL; ?>">
            <h1><?php echo SITE_NAME; ?> <span><?php echo SITE_TAGLINE; ?></span></h1>
        </a>
    </div>
    <?php if (isset($_SESSION['user_id'])): ?>
        <div class="user-greeting">
            <i class="fas fa-user-circle"></i> Hello, <?php echo htmlspecialchars($_SESSION['user_name']); ?>
            <a href="<?php echo PAGES_URL; ?>cart.php" class="cart-link">
                <i class="fas fa-shopping-cart"></i> Cart <span class="cart-count" id="cartCount">0</span>
            </a>
            <a href="<?php echo PAGES_URL; ?>logout.php" class="logout-link">Logout</a>
        </div>
    <?php else: ?>
        <div class="auth-buttons">
            <a href="<?php echo PAGES_URL; ?>login.php" class="signin-btn">Sign In</a>
            <a href="<?php echo PAGES_URL; ?>register.php" class="signup-btn">Register</a>
        </div>
    <?php endif; ?>
</div>

<div class="nav-section">
    <div class="search-box">
        <form action="<?php echo SITE_URL; ?>search.php" method="GET">
            <input type="text" name="q" placeholder="Search for products...">
            <button type="submit"><i class="fas fa-search"></i></button>
        </form>
    </div>
</div>

<div class="categories">
    <a href="<?php echo SITE_URL; ?>category.php?cat=medicine">Medicine</a>
    <a href="<?php echo SITE_URL; ?>category.php?cat=lab-test">Lab Test</a>
    <a href="<?php echo SITE_URL; ?>category.php?cat=healthcare">Healthcare</a>
    <a href="<?php echo SITE_URL; ?>category.php?cat=beauty">Beauty</a>
    <a href="<?php echo SITE_URL; ?>category.php?cat=sexual-wellness">Sexual Wellness</a>
    <a href="<?php echo SITE_URL; ?>category.php?cat=baby">Baby & Mom Care</a>
    <a href="<?php echo SITE_URL; ?>category.php?cat=herbal">Herbal</a>
    <span class="offer-badge">FLASH SALE 65% OFF</span>
    <a href="<?php echo SITE_URL; ?>category.php?cat=supplements">Supplements</a>
    <a href="<?php echo SITE_URL; ?>category.php?cat=pet-care">Pet Care</a>
</div>

<main class="main-content">