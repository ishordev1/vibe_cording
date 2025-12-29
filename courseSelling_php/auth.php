<?php
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'lib/functions.php';
require_once 'app/controllers/AuthController.php';

// Instantiate controller
$controller = new AuthController($conn);

// Get action from URL
$action = isset($_GET['action']) ? $_GET['action'] : 'login';

// Route to appropriate method
switch ($action) {
    case 'login':
        $controller->login();
        break;
    case 'register':
        $controller->register();
        break;
    case 'handle-login':
        $controller->handleLogin();
        break;
    case 'handle-register':
        $controller->handleRegister();
        break;
    case 'logout':
        $controller->logout();
        break;
    default:
        $controller->login();
}
?>
