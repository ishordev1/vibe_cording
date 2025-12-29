<?php
/**
 * Session Management
 * Handles user session initialization, validation, and cleanup
 */

// Session configuration (MUST be before session_start())
ini_set('session.gc_maxlifetime', 1800); // 30 minutes
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 0); // Set to 1 if using HTTPS
ini_set('session.cookie_samesite', 'Strict');

session_start();

/**
 * Check if user is logged in
 * Returns true if logged in, false otherwise
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['role']);
}

/**
 * Check if user has specific role
 * @param string $role - Role to check ('admin' or 'intern')
 */
function hasRole($role) {
    return isLoggedIn() && $_SESSION['role'] === $role;
}

/**
 * Check if session has timed out
 */
function isSessionTimedOut() {
    if (!isset($_SESSION['last_activity'])) {
        return false;
    }
    
    $session_timeout = 1800; // 30 minutes
    if (time() - $_SESSION['last_activity'] > $session_timeout) {
        return true;
    }
    
    // Update last activity time
    $_SESSION['last_activity'] = time();
    return false;
}

/**
 * Redirect to login if not authenticated
 */
function requireLogin() {
    if (!isLoggedIn() || isSessionTimedOut()) {
        $_SESSION = [];
        session_destroy();
        header("Location: " . APP_URL . "/auth/login.php");
        exit();
    }
}

/**
 * Redirect to login if not admin
 */
function requireAdmin() {
    if (!hasRole('admin')) {
        header("Location: " . APP_URL . "/index.php");
        exit();
    }
}

/**
 * Redirect to login if not intern
 */
function requireIntern() {
    if (!hasRole('intern')) {
        header("Location: " . APP_URL . "/index.php");
        exit();
    }
}

/**
 * Get current user data from database
 */
function getCurrentUser() {
    global $conn;
    
    if (!isLoggedIn()) {
        return null;
    }
    
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT id, email, full_name, role, phone, college_name, degree FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    
    return $user;
}

/**
 * Destroy session and logout user
 */
function logout() {
    $_SESSION = [];
    session_destroy();
    header("Location: " . APP_URL . "/auth/login.php");
    exit();
}

// Set initial activity time
if (!isset($_SESSION['last_activity'])) {
    $_SESSION['last_activity'] = time();
}

?>
