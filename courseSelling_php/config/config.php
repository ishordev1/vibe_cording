<?php
// Application Configuration

// Session settings
session_start();

// Site settings
define('SITE_NAME', 'Digital Tarai');
define('SITE_URL', 'http://localhost/ai/DigitalTarai');
define('COMPANY_EMAIL', 'info@digitaltarai.com');
define('COMPANY_PHONE', '+977-1-xxxxxxxx');
define('COMPANY_LOCATION', 'Siraha, Nepal');

// File upload settings
define('UPLOAD_DIR', __DIR__ . '/../public/uploads/');
define('DOWNLOAD_DIR', __DIR__ . '/../public/downloads/');
define('MAX_FILE_SIZE', 5242880); // 5MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'pdf', 'zip']);

// Payment settings
define('MIN_INTERNSHIP_FEE', 500);

// Session timeout
define('SESSION_TIMEOUT', 3600); // 1 hour

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
