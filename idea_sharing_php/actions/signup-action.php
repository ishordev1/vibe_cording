<?php
/**
 * Sign Up Action
 * Handles user registration
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/pages/signup.php');
}

// Get form data
$user_type = sanitize($_POST['user_type'] ?? '');
$full_name = sanitize($_POST['full_name'] ?? '');
$username = sanitize($_POST['username'] ?? '');
$email = sanitize($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';
$bio = sanitize($_POST['bio'] ?? '');

// Validate input
if (empty($user_type) || empty($full_name) || empty($username) || empty($email) || empty($password)) {
    setFlashMessage('error', 'Please fill in all required fields.');
    redirect('/pages/signup.php');
}

// Validate user type
if (!in_array($user_type, [USER_TYPE_CREATOR, USER_TYPE_INVESTOR])) {
    setFlashMessage('error', 'Invalid user type.');
    redirect('/pages/signup.php');
}

// Validate password match
if ($password !== $confirm_password) {
    setFlashMessage('error', 'Passwords do not match.');
    redirect('/pages/signup.php');
}

// Validate password length
if (strlen($password) < 6) {
    setFlashMessage('error', 'Password must be at least 6 characters long.');
    redirect('/pages/signup.php');
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    setFlashMessage('error', 'Invalid email format.');
    redirect('/pages/signup.php');
}

$conn = getDBConnection();

// Check if username exists
$stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
if ($stmt->get_result()->num_rows > 0) {
    setFlashMessage('error', 'Username already taken. Please choose another.');
    redirect('/pages/signup.php');
}

// Check if email exists
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
if ($stmt->get_result()->num_rows > 0) {
    setFlashMessage('error', 'Email already registered. Please sign in.');
    redirect('/pages/signup.php');
}

// Hash password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Handle profile image upload
$profile_image = 'default-avatar.png';
if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
    $file = $_FILES['profile_image'];
    $file_size = $file['size'];
    $file_type = $file['type'];
    
    // Validate file size
    if ($file_size > MAX_FILE_SIZE) {
        setFlashMessage('error', 'Profile image must be less than 5MB.');
        redirect('/pages/signup.php');
    }
    
    // Validate file type
    if (!in_array($file_type, ALLOWED_IMAGE_TYPES)) {
        setFlashMessage('error', 'Invalid image format. Please upload JPG, PNG, GIF, or WebP.');
        redirect('/pages/signup.php');
    }
    
    // Generate unique filename
    $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $new_file_name = 'profile_' . uniqid() . '.' . $file_extension;
    $upload_path = UPLOAD_DIR . $new_file_name;
    
    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $upload_path)) {
        $profile_image = $new_file_name;
    }
}

// Insert user into database
$stmt = $conn->prepare("INSERT INTO users (username, email, password, full_name, user_type, bio, profile_image) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssssss", $username, $email, $hashed_password, $full_name, $user_type, $bio, $profile_image);

if ($stmt->execute()) {
    // Get the new user ID
    $user_id = $conn->insert_id;
    
    // Set session variables
    $_SESSION['user_id'] = $user_id;
    $_SESSION['username'] = $username;
    $_SESSION['email'] = $email;
    $_SESSION['user_type'] = $user_type;
    $_SESSION['full_name'] = $full_name;
    
    // Redirect to appropriate dashboard
    setFlashMessage('success', 'Account created successfully! Welcome to ' . APP_NAME . '!');
    
    if ($user_type === USER_TYPE_CREATOR) {
        redirect('/pages/creator/dashboard.php');
    } else {
        redirect('/pages/investor/dashboard.php');
    }
} else {
    setFlashMessage('error', 'An error occurred. Please try again.');
    redirect('/pages/signup.php');
}
