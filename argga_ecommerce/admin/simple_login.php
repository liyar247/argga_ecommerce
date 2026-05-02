<?php
session_start();
$_SESSION['admin_id'] = 1;
$_SESSION['admin_name'] = 'Admin';
$_SESSION['is_admin'] = true;
header('Location: dashboard.php');
?>