<?php
/**
 * Create Idea Action
 * Handles idea creation with image upload
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

// Require creator login
requireUserType(USER_TYPE_CREATOR);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/pages/creator/create-idea.php');
}

$conn = getDBConnection();
$user_id = getCurrentUserId();

// Get form data
$title = sanitize($_POST['title'] ?? '');
$category = sanitize($_POST['category'] ?? '');
$description = $_POST['description'] ?? ''; // Don't sanitize - contains HTML from TinyMCE
$status = sanitize($_POST['status'] ?? 'published');

// Validate input
if (empty($title) || empty($category) || empty($description)) {
    setFlashMessage('error', 'Please fill in all required fields.');
    redirect('/pages/creator/create-idea.php');
}

// Validate category
if (!in_array($category, IDEA_CATEGORIES)) {
    setFlashMessage('error', 'Invalid category selected.');
    redirect('/pages/creator/create-idea.php');
}

// Validate description length
if (strlen($description) < 100) {
    setFlashMessage('error', 'Description must be at least 100 characters long.');
    redirect('/pages/creator/create-idea.php');
}

// Insert idea into database
$stmt = $conn->prepare("INSERT INTO ideas (user_id, title, description, category, status) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("issss", $user_id, $title, $description, $category, $status);

if (!$stmt->execute()) {
    setFlashMessage('error', 'Failed to create idea. Please try again.');
    redirect('/pages/creator/create-idea.php');
}

$idea_id = $conn->insert_id;

// Handle image uploads
if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
    $upload_dir = UPLOAD_DIR;
    
    // Create uploads directory if it doesn't exist
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    $is_first = true;
    
    foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
        if (empty($tmp_name)) {
            continue;
        }
        
        $file_name = $_FILES['images']['name'][$key];
        $file_size = $_FILES['images']['size'][$key];
        $file_type = $_FILES['images']['type'][$key];
        $file_error = $_FILES['images']['error'][$key];
        
        // Validate file
        if ($file_error !== UPLOAD_ERR_OK) {
            continue;
        }
        
        if ($file_size > MAX_FILE_SIZE) {
            setFlashMessage('warning', 'Some images were too large and were not uploaded.');
            continue;
        }
        
        if (!in_array($file_type, ALLOWED_IMAGE_TYPES)) {
            setFlashMessage('warning', 'Some files were not valid images and were not uploaded.');
            continue;
        }
        
        // Generate unique filename
        $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);
        $new_file_name = 'idea_' . $idea_id . '_' . uniqid() . '.' . $file_extension;
        $file_path = $upload_dir . $new_file_name;
        
        // Move uploaded file
        if (move_uploaded_file($tmp_name, $file_path)) {
            // Save to database
            $db_file_path = 'uploads/' . $new_file_name;
            $is_primary = $is_first ? 1 : 0;
            
            $stmt = $conn->prepare("INSERT INTO idea_media (idea_id, file_path, file_type, is_primary) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("issi", $idea_id, $db_file_path, $file_type, $is_primary);
            $stmt->execute();
            
            $is_first = false;
        }
    }
}

setFlashMessage('success', 'Your idea has been posted successfully!');
redirect('/pages/creator/dashboard.php');
