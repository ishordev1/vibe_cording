<?php
// Helper Functions

/**
 * Sanitize user input
 */
function sanitize($data) {
    global $conn;
    return $conn->real_escape_string(trim($data));
}

/**
 * Validate email
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * Hash password
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

/**
 * Verify password
 */
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Check if user is admin
 */
function isAdmin() {
    return isset($_SESSION['user_id']) && $_SESSION['user_type'] === 'admin';
}

/**
 * Redirect to page
 */
function redirect($url) {
    header("Location: " . SITE_URL . "/" . $url);
    exit();
}

/**
 * Get file extension
 */
function getFileExtension($filename) {
    return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
}

/**
 * Check if file upload is valid
 */
function isValidFileUpload($file) {
    if (!isset($file['tmp_name']) || !isset($file['name'])) {
        return false;
    }
    
    if ($file['size'] > MAX_FILE_SIZE) {
        return false;
    }
    
    $ext = getFileExtension($file['name']);
    if (!in_array($ext, ALLOWED_EXTENSIONS)) {
        return false;
    }
    
    return true;
}

/**
 * Generate unique filename
 */
function generateUniqueFilename($originalName) {
    $ext = getFileExtension($originalName);
    return time() . '_' . uniqid() . '.' . $ext;
}

/**
 * Format date
 */
function formatDate($date) {
    return date('F d, Y', strtotime($date));
}

/**
 * Format currency (Nepali Rupees)
 */
function formatCurrency($amount) {
    return '₹' . number_format($amount, 2);
}

/**
 * Truncate text
 */
function truncateText($text, $length = 150) {
    if (strlen($text) > $length) {
        return substr($text, 0, $length) . '...';
    }
    return $text;
}

/**
 * Generate slug from title
 */
function generateSlug($title) {
    $slug = strtolower(trim($title));
    $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
    $slug = preg_replace('/-+/', '-', $slug);
    return trim($slug, '-');
}

/**
 * Check if email exists
 */
function emailExists($email, $tableName = 'users', $userId = null) {
    global $conn;
    $email = sanitize($email);
    $query = "SELECT id FROM $tableName WHERE email = '$email'";
    
    if ($userId) {
        $query .= " AND id != $userId";
    }
    
    $result = $conn->query($query);
    return $result->num_rows > 0;
}

/**
 * Get user by ID
 */
function getUserById($userId) {
    global $conn;
    $query = "SELECT * FROM users WHERE id = $userId LIMIT 1";
    $result = $conn->query($query);
    return $result->fetch_assoc();
}

/**
 * Get internship by ID
 */
function getInternshipById($internshipId) {
    global $conn;
    $query = "SELECT * FROM internships WHERE id = $internshipId LIMIT 1";
    $result = $conn->query($query);
    return $result->fetch_assoc();
}

/**
 * Get application by ID
 */
function getApplicationById($applicationId) {
    global $conn;
    $query = "SELECT * FROM applications WHERE id = $applicationId LIMIT 1";
    $result = $conn->query($query);
    return $result->fetch_assoc();
}

/**
 * Get blog by ID or slug
 */
function getBlogPost($identifier) {
    global $conn;
    $identifier = sanitize($identifier);
    $query = "SELECT * FROM blogs WHERE id = '$identifier' OR slug = '$identifier' LIMIT 1";
    $result = $conn->query($query);
    return $result->fetch_assoc();
}

/**
 * Flash message helper
 */
function setFlashMessage($key, $message) {
    $_SESSION['flash'][$key] = $message;
}

function getFlashMessage($key) {
    if (isset($_SESSION['flash'][$key])) {
        $message = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $message;
    }
    return null;
}

/**
 * CSRF token generation and validation
 */
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validateCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Log activity
 */
function logActivity($action, $userId = null, $details = '') {
    global $conn;
    $date = date('Y-m-d H:i:s');
    $details = sanitize($details);
    $userId = $userId ?? ($_SESSION['user_id'] ?? null);
    
    $query = "INSERT INTO activity_logs (action, user_id, details, created_at) 
              VALUES ('$action', $userId, '$details', '$date')";
    $conn->query($query);
}
?>
