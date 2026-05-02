<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'argga_ecommerce');

// Site Configuration
define('SITE_NAME', 'argGa');
define('SITE_TAGLINE', 'For better health');
define('SITE_URL', 'http://localhost/arggaEcommerce/');
define('ASSETS_URL', SITE_URL . 'assets/');  // 👈 এই লাইনটি ঠিক আছে কিনা চেক করুন
define('PAGES_URL', SITE_URL . 'pages/');
define('API_URL', SITE_URL . 'backend/api/');
define('UPLOADS_URL', SITE_URL . 'backend/uploads/');

// Start Session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

date_default_timezone_set('Asia/Dhaka');
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>