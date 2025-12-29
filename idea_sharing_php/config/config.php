<?php
/**
 * Application Configuration
 * 
 * General application settings and constants
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Application settings
define('APP_NAME', 'IdeaConnect');
define('APP_URL', 'http://localhost/idea_sharing_website');
define('APP_VERSION', '1.0.0');

// File upload settings
define('UPLOAD_DIR', __DIR__ . '/../uploads/');
define('MAX_FILE_SIZE', 5242880); // 5MB in bytes
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);

// Pagination settings
define('IDEAS_PER_PAGE', 9);
define('MESSAGES_PER_PAGE', 50);

// Date format
define('DATE_FORMAT', 'Y-m-d');
define('TIME_FORMAT', 'H:i:s');
define('DATETIME_FORMAT', 'Y-m-d H:i:s');

// Idea categories
define('IDEA_CATEGORIES', [
    'Technology',
    'E-commerce',
    'Healthcare',
    'Education',
    'Finance',
    'Food & Beverage',
    'Real Estate',
    'Entertainment',
    'Design',
    'Agriculture',
    'Other'
]);

// User types
define('USER_TYPE_CREATOR', 'creator');
define('USER_TYPE_INVESTOR', 'investor');

/**
 * Check if user is logged in
 * 
 * @return bool
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Get current user ID
 * 
 * @return int|null
 */
function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Get current user type
 * 
 * @return string|null
 */
function getCurrentUserType() {
    return $_SESSION['user_type'] ?? null;
}

/**
 * Require login - redirect to login page if not authenticated
 */
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: ' . APP_URL . '/pages/signin.php');
        exit;
    }
}

/**
 * Require specific user type
 * 
 * @param string $type Required user type
 */
function requireUserType($type) {
    requireLogin();
    if (getCurrentUserType() !== $type) {
        header('Location: ' . APP_URL . '/pages/404.php');
        exit;
    }
}

/**
 * Redirect to a specific page
 * 
 * @param string $path Path to redirect to
 */
function redirect($path) {
    header('Location: ' . APP_URL . '/' . ltrim($path, '/'));
    exit;
}

/**
 * Set flash message
 * 
 * @param string $type Message type (success, error, info, warning)
 * @param string $message Message text
 */
function setFlashMessage($type, $message) {
    $_SESSION['flash_message'] = [
        'type' => $type,
        'message' => $message
    ];
}

/**
 * Get and clear flash message
 * 
 * @return array|null
 */
function getFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $message;
    }
    return null;
}

/**
 * Format date for display
 * 
 * @param string $date Date string
 * @param string $format Format string
 * @return string
 */
function formatDate($date, $format = 'M d, Y') {
    return date($format, strtotime($date));
}

/**
 * Get time ago string
 * 
 * @param string $datetime Datetime string
 * @return string
 */
function timeAgo($datetime) {
    $timestamp = strtotime($datetime);
    $diff = time() - $timestamp;
    
    if ($diff < 60) {
        return 'Just now';
    } elseif ($diff < 3600) {
        $minutes = floor($diff / 60);
        return $minutes . ' minute' . ($minutes > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 86400) {
        $hours = floor($diff / 3600);
        return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 604800) {
        $days = floor($diff / 86400);
        return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
    } else {
        return date('M d, Y', $timestamp);
    }
}

/**
 * Truncate text to specified length
 * 
 * @param string $text Text to truncate (can contain HTML)
 * @param int $length Maximum length
 * @param string $suffix Suffix to add
 * @return string
 */
function truncateText($text, $length = 100, $suffix = '...') {
    // Decode HTML entities first
    $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
    // Strip HTML tags for preview
    $text = strip_tags($text);
    // Remove extra whitespace
    $text = preg_replace('/\s+/', ' ', trim($text));
    
    if (strlen($text) <= $length) {
        return htmlspecialchars($text);
    }
    return htmlspecialchars(substr($text, 0, $length)) . $suffix;
}

/**
 * Create a notification for a user
 * 
 * @param int $user_id User to notify
 * @param string $type Notification type
 * @param string $title Notification title
 * @param string $message Notification message
 * @param int $related_id Related item ID (optional)
 * @param string $related_type Related item type (optional)
 * @return bool Success status
 */
function createNotification($user_id, $type, $title, $message, $related_id = null, $related_type = null) {
    $conn = getDBConnection();
    $stmt = $conn->prepare("INSERT INTO notifications (user_id, type, title, message, related_id, related_type) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssss", $user_id, $type, $title, $message, $related_id, $related_type);
    return $stmt->execute();
}

/**
 * Get unread notification count for a user
 * 
 * @param int $user_id User ID
 * @return int Unread count
 */
function getUnreadNotificationCount($user_id) {
    $conn = getDBConnection();
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM notifications WHERE user_id = ? AND is_read = FALSE");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    return $result['count'] ?? 0;
}

/**
 * Render HTML content safely
 * Decodes HTML entities for TinyMCE content
 * 
 * @param string $html HTML content
 * @return string Decoded HTML
 */
function renderHTML($html) {
    return html_entity_decode($html, ENT_QUOTES, 'UTF-8');
}
