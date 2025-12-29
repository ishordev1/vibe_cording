<?php
/**
 * Sign In Action
 * Handles user authentication
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/pages/signin.php');
}

// Get form data
$email = sanitize($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

// Validate input
if (empty($email) || empty($password)) {
    setFlashMessage('error', 'Please fill in all fields.');
    redirect('/pages/signin.php');
}

// Check if user exists
$conn = getDBConnection();
$stmt = $conn->prepare("SELECT id, username, email, password, user_type, full_name FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    setFlashMessage('error', 'Invalid email or password.');
    redirect('/pages/signin.php');
}

$user = $result->fetch_assoc();

// Verify password
if (!password_verify($password, $user['password'])) {
    setFlashMessage('error', 'Invalid email or password.');
    redirect('/pages/signin.php');
}

// Set session variables
$_SESSION['user_id'] = $user['id'];
$_SESSION['username'] = $user['username'];
$_SESSION['email'] = $user['email'];
$_SESSION['user_type'] = $user['user_type'];
$_SESSION['full_name'] = $user['full_name'];

// Redirect to appropriate dashboard
setFlashMessage('success', 'Welcome back, ' . $user['full_name'] . '!');

if ($user['user_type'] === USER_TYPE_CREATOR) {
    redirect('/pages/creator/dashboard.php');
} else {
    redirect('/pages/investor/dashboard.php');
}
