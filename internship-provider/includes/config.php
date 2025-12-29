<?php
/**
 * Database Configuration
 * This file handles all database connections for the internship platform
 */

// Database credentials
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'internship_platform');

// Connection settings
define('DB_CHARSET', 'utf8mb4');

// Create database connection
try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    // Set charset
    $conn->set_charset(DB_CHARSET);
    
} catch (Exception $e) {
    die("Database connection error: " . $e->getMessage());
}

// App settings
define('APP_NAME', 'Internship Platform');
define('APP_URL', 'http://localhost/internship-provider');
define('UPLOAD_DIR', $_SERVER['DOCUMENT_ROOT'] . '/internship-provider/assets/uploads/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_FILE_TYPES', ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx']);

// Session timeout (in seconds)
define('SESSION_TIMEOUT', 1800); // 30 minutes

// Offer Letter Details
define('SECURITY_DEPOSIT', 500); // ₹500
define('SECURITY_DEPOSIT_CURRENCY', '₹');

?>
