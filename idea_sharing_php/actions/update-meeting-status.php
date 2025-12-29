<?php
/**
 * Update Meeting Status Action
 * Updates the status of a meeting (confirm, cancel, complete)
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

// Require login
if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$conn = getDBConnection();
$user_id = getCurrentUserId();
$meeting_id = isset($_POST['meeting_id']) ? (int)$_POST['meeting_id'] : 0;
$status = isset($_POST['status']) ? sanitize($_POST['status']) : '';

// Validate status
$valid_statuses = ['pending', 'confirmed', 'cancelled', 'completed'];
if (!in_array($status, $valid_statuses)) {
    echo json_encode(['success' => false, 'message' => 'Invalid status']);
    exit;
}

// Verify user is part of the conversation
$stmt = $conn->prepare("
    SELECT m.id, m.scheduled_by, c.creator_id, c.investor_id 
    FROM meetings m
    JOIN conversations c ON m.conversation_id = c.id
    WHERE m.id = ? AND (c.creator_id = ? OR c.investor_id = ?)
");
$stmt->bind_param("iii", $meeting_id, $user_id, $user_id);
$stmt->execute();
$meeting_data = $stmt->get_result()->fetch_assoc();

if (!$meeting_data) {
    echo json_encode(['success' => false, 'message' => 'Meeting not found or access denied']);
    exit;
}

// Determine the recipient (other user in conversation)
$recipient_id = ($meeting_data['creator_id'] == $user_id) ? $meeting_data['investor_id'] : $meeting_data['creator_id'];

// Update meeting status
$stmt = $conn->prepare("UPDATE meetings SET status = ? WHERE id = ?");
$stmt->bind_param("si", $status, $meeting_id);

if ($stmt->execute()) {
    // Get user name
    $stmt = $conn->prepare("SELECT full_name FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    
    // Create notification for the other user based on status
    $notification_title = '';
    $notification_message = '';
    $notification_type = 'meeting_updated';
    
    switch($status) {
        case 'confirmed':
            $notification_type = 'meeting_confirmed';
            $notification_title = 'Meeting Confirmed';
            $notification_message = $user['full_name'] . ' confirmed the meeting.';
            break;
        case 'cancelled':
            $notification_type = 'meeting_cancelled';
            $notification_title = 'Meeting Cancelled';
            $notification_message = $user['full_name'] . ' cancelled the meeting.';
            break;
        case 'completed':
            $notification_title = 'Meeting Completed';
            $notification_message = $user['full_name'] . ' marked the meeting as completed.';
            break;
    }
    
    if ($notification_title) {
        createNotification($recipient_id, $notification_type, $notification_title, $notification_message, $meeting_id, 'meeting');
    }
    
    echo json_encode(['success' => true, 'message' => 'Meeting status updated successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update meeting status']);
}
