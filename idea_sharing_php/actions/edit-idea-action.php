<?php
/**
 * Edit Idea Action
 * Handles idea updates
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

// Require creator login
requireUserType(USER_TYPE_CREATOR);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/pages/creator/dashboard.php');
}

$conn = getDBConnection();
$user_id = getCurrentUserId();

// Get form data
$idea_id = isset($_POST['idea_id']) ? (int)$_POST['idea_id'] : 0;
$title = sanitize($_POST['title'] ?? '');
$category = sanitize($_POST['category'] ?? '');
$description = $_POST['description'] ?? ''; // Don't sanitize - contains HTML from TinyMCE
$status = sanitize($_POST['status'] ?? 'published');

// Verify ownership
$stmt = $conn->prepare("SELECT id FROM ideas WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $idea_id, $user_id);
$stmt->execute();
if ($stmt->get_result()->num_rows === 0) {
    setFlashMessage('error', 'Idea not found or you do not have permission to edit it.');
    redirect('/pages/creator/dashboard.php');
}

// Validate input
if (empty($title) || empty($category) || empty($description)) {
    setFlashMessage('error', 'Please fill in all required fields.');
    redirect('/pages/creator/edit-idea.php?id=' . $idea_id);
}

// Update idea
$stmt = $conn->prepare("UPDATE ideas SET title = ?, description = ?, category = ?, status = ? WHERE id = ? AND user_id = ?");
$stmt->bind_param("ssssii", $title, $description, $category, $status, $idea_id, $user_id);

if (!$stmt->execute()) {
    setFlashMessage('error', 'Failed to update idea. Please try again.');
    redirect('/pages/creator/edit-idea.php?id=' . $idea_id);
}

// Handle image deletions
if (isset($_POST['delete_images']) && is_array($_POST['delete_images'])) {
    foreach ($_POST['delete_images'] as $image_id) {
        $image_id = (int)$image_id;
        
        // Get image path
        $stmt = $conn->prepare("SELECT file_path FROM idea_media WHERE id = ? AND idea_id = ?");
        $stmt->bind_param("ii", $image_id, $idea_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $image = $result->fetch_assoc();
            $file_path = __DIR__ . '/../' . $image['file_path'];
            
            // Delete file from filesystem
            if (file_exists($file_path)) {
                unlink($file_path);
            }
            
            // Delete from database
            $stmt = $conn->prepare("DELETE FROM idea_media WHERE id = ?");
            $stmt->bind_param("i", $image_id);
            $stmt->execute();
        }
    }
}

// Handle new image uploads
if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
    $upload_dir = UPLOAD_DIR;
    
    // Create uploads directory if it doesn't exist
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    // Check if there's a primary image
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM idea_media WHERE idea_id = ? AND is_primary = 1");
    $stmt->bind_param("i", $idea_id);
    $stmt->execute();
    $has_primary = $stmt->get_result()->fetch_assoc()['count'] > 0;
    
    $is_first = !$has_primary;
    
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
            continue;
        }
        
        if (!in_array($file_type, ALLOWED_IMAGE_TYPES)) {
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

setFlashMessage('success', 'Your idea has been updated successfully!');
redirect('/pages/creator/dashboard.php');
