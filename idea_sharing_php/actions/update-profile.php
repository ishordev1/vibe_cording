<?php
/**
 * Update Profile Action
 * Handles profile updates
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

// Require login
requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/pages/profile.php');
}

$conn = getDBConnection();
$user_id = getCurrentUserId();

// Get form data
$full_name = sanitize($_POST['full_name'] ?? '');
$username = sanitize($_POST['username'] ?? '');
$email = sanitize($_POST['email'] ?? '');
$bio = sanitize($_POST['bio'] ?? '');
$current_password = $_POST['current_password'] ?? '';
$new_password = $_POST['new_password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

// Validate input
if (empty($full_name) || empty($username) || empty($email)) {
    setFlashMessage('error', 'Please fill in all required fields.');
    redirect('/pages/profile.php');
}

// Validate email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    setFlashMessage('error', 'Invalid email format.');
    redirect('/pages/profile.php');
}

// Check if username is taken by another user
$stmt = $conn->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
$stmt->bind_param("si", $username, $user_id);
$stmt->execute();
if ($stmt->get_result()->num_rows > 0) {
    setFlashMessage('error', 'Username already taken by another user.');
    redirect('/pages/profile.php');
}

// Check if email is taken by another user
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
$stmt->bind_param("si", $email, $user_id);
$stmt->execute();
if ($stmt->get_result()->num_rows > 0) {
    setFlashMessage('error', 'Email already registered to another user.');
    redirect('/pages/profile.php');
}

// Handle profile image upload
$profile_image_updated = false;
$new_profile_image = null;
if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
    $file = $_FILES['profile_image'];
    $file_size = $file['size'];
    $file_type = $file['type'];
    
    // Validate file size
    if ($file_size > MAX_FILE_SIZE) {
        setFlashMessage('error', 'Profile image must be less than 5MB.');
        redirect('/pages/profile.php');
    }
    
    // Validate file type
    if (!in_array($file_type, ALLOWED_IMAGE_TYPES)) {
        setFlashMessage('error', 'Invalid image format. Please upload JPG, PNG, GIF, or WebP.');
        redirect('/pages/profile.php');
    }
    
    // Generate unique filename
    $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $new_file_name = 'profile_' . uniqid() . '.' . $file_extension;
    $upload_path = UPLOAD_DIR . $new_file_name;
    
    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $upload_path)) {
        // Delete old profile image if not default
        $stmt = $conn->prepare("SELECT profile_image FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $current_user = $stmt->get_result()->fetch_assoc();
        
        if (!empty($current_user['profile_image']) && $current_user['profile_image'] !== 'default-avatar.png') {
            $old_image_path = UPLOAD_DIR . $current_user['profile_image'];
            if (file_exists($old_image_path)) {
                unlink($old_image_path);
            }
        }
        
        $new_profile_image = $new_file_name;
        $profile_image_updated = true;
    }
}

// Handle password change
if (!empty($current_password) || !empty($new_password)) {
    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        setFlashMessage('error', 'Please fill in all password fields to change password.');
        redirect('/pages/profile.php');
    }
    
    if ($new_password !== $confirm_password) {
        setFlashMessage('error', 'New passwords do not match.');
        redirect('/pages/profile.php');
    }
    
    if (strlen($new_password) < 6) {
        setFlashMessage('error', 'New password must be at least 6 characters long.');
        redirect('/pages/profile.php');
    }
    
    // Verify current password
    $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    
    if (!password_verify($current_password, $user['password'])) {
        setFlashMessage('error', 'Current password is incorrect.');
        redirect('/pages/profile.php');
    }
    
    // Update with new password
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    if ($profile_image_updated) {
        $stmt = $conn->prepare("UPDATE users SET full_name = ?, username = ?, email = ?, bio = ?, password = ?, profile_image = ? WHERE id = ?");
        $stmt->bind_param("ssssssi", $full_name, $username, $email, $bio, $hashed_password, $new_profile_image, $user_id);
    } else {
        $stmt = $conn->prepare("UPDATE users SET full_name = ?, username = ?, email = ?, bio = ?, password = ? WHERE id = ?");
        $stmt->bind_param("sssssi", $full_name, $username, $email, $bio, $hashed_password, $user_id);
    }
} else {
    // Update without password change
    if ($profile_image_updated) {
        $stmt = $conn->prepare("UPDATE users SET full_name = ?, username = ?, email = ?, bio = ?, profile_image = ? WHERE id = ?");
        $stmt->bind_param("sssssi", $full_name, $username, $email, $bio, $new_profile_image, $user_id);
    } else {
        $stmt = $conn->prepare("UPDATE users SET full_name = ?, username = ?, email = ?, bio = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $full_name, $username, $email, $bio, $user_id);
    }
}

if ($stmt->execute()) {
    // Update session variables
    $_SESSION['username'] = $username;
    $_SESSION['email'] = $email;
    $_SESSION['full_name'] = $full_name;
    
    setFlashMessage('success', 'Profile updated successfully!');
} else {
    setFlashMessage('error', 'Failed to update profile. Please try again.');
}

redirect('/pages/profile.php');
