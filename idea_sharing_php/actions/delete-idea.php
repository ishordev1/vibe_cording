<?php
/**
 * Delete Idea Action
 * Handles idea deletion
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

// Require creator login
requireUserType(USER_TYPE_CREATOR);

$conn = getDBConnection();
$user_id = getCurrentUserId();
$idea_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Verify ownership
$stmt = $conn->prepare("SELECT id FROM ideas WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $idea_id, $user_id);
$stmt->execute();

if ($stmt->get_result()->num_rows === 0) {
    setFlashMessage('error', 'Idea not found or you do not have permission to delete it.');
    redirect('/pages/creator/dashboard.php');
}

// Get and delete all associated images
$stmt = $conn->prepare("SELECT file_path FROM idea_media WHERE idea_id = ?");
$stmt->bind_param("i", $idea_id);
$stmt->execute();
$images = $stmt->get_result();

while ($image = $images->fetch_assoc()) {
    $file_path = __DIR__ . '/../' . $image['file_path'];
    if (file_exists($file_path)) {
        unlink($file_path);
    }
}

// Delete idea (cascades to idea_media, investors_interested, conversations, messages, meetings)
$stmt = $conn->prepare("DELETE FROM ideas WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $idea_id, $user_id);

if ($stmt->execute()) {
    setFlashMessage('success', 'Idea deleted successfully.');
} else {
    setFlashMessage('error', 'Failed to delete idea.');
}

redirect('/pages/creator/dashboard.php');
