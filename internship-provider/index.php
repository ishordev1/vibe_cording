<?php
/**
 * Home / Index
 * Landing page or redirect based on login status
 */

require_once 'includes/config.php';
require_once 'includes/session.php';

// If already logged in, redirect to appropriate dashboard
if (isLoggedIn()) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: " . APP_URL . "/admin/dashboard.php");
    } else {
        header("Location: " . APP_URL . "/user/dashboard.php");
    }
    exit();
}

// Redirect to login
header("Location: " . APP_URL . "/auth/login.php");
exit();

?>
