<?php
// includes/config.php
session_start();

// Adjust these to your local environment
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'research_db');
define('DB_USER', 'root');
define('DB_PASS', '');

// Base paths
define('BASE_PATH', dirname(__DIR__));
define('BASE_URL', '/research_paper_submission');

// Upload paths (relative to project root)
define('UPLOADS_PAPERS', BASE_PATH . '/uploads/papers');
define('UPLOADS_COPYRIGHTS', BASE_PATH . '/uploads/copyrights');
define('UPLOADS_PUBLISHED', BASE_PATH . '/uploads/published');
define('UPLOADS_CERTIFICATES', BASE_PATH . '/uploads/certificates');
define('UPLOADS_QRCODES', BASE_PATH . '/uploads/qrcodes');

// Ensure upload dirs exist
$dirs = [UPLOADS_PAPERS, UPLOADS_COPYRIGHTS, UPLOADS_PUBLISHED, UPLOADS_CERTIFICATES, UPLOADS_QRCODES];
foreach ($dirs as $d) {
    if (!is_dir($d)) @mkdir($d, 0755, true);
}

// Error display for local dev
ini_set('display_errors', 1);
error_reporting(E_ALL);

?>