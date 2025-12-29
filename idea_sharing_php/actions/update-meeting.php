<?php
/**
 * Update Meeting Action
 * Updates an existing meeting
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
$meeting_date = isset($_POST['meeting_date']) ? sanitize($_POST['meeting_date']) : '';
$meeting_time = isset($_POST['meeting_time']) ? sanitize($_POST['meeting_time']) : '';
$location = isset($_POST['location']) ? sanitize($_POST['location']) : '';
$notes = isset($_POST['notes']) ? sanitize($_POST['notes']) : '';

// Validate input
if (empty($meeting_date) || empty($meeting_time)) {
    echo json_encode(['success' => false, 'message' => 'Date and time are required']);
    exit;
}

// Verify user owns the meeting or is part of the conversation
$stmt = $conn->prepare("
    SELECT m.id, m.scheduled_by, c.creator_id, c.investor_id 
    FROM meetings m
    JOIN conversations c ON m.conversation_id = c.id
    WHERE m.id = ? AND (m.scheduled_by = ? OR c.creator_id = ? OR c.investor_id = ?)
");
$stmt->bind_param("iiii", $meeting_id, $user_id, $user_id, $user_id);
$stmt->execute();
$meeting_data = $stmt->get_result()->fetch_assoc();

if (!$meeting_data) {
    echo json_encode(['success' => false, 'message' => 'Meeting not found or access denied']);
    exit;
}

// Determine the recipient (other user in conversation)
$recipient_id = ($meeting_data['creator_id'] == $user_id) ? $meeting_data['investor_id'] : $meeting_data['creator_id'];

// Update meeting
$stmt = $conn->prepare("UPDATE meetings SET meeting_date = ?, meeting_time = ?, location = ?, notes = ? WHERE id = ?");
$stmt->bind_param("ssssi", $meeting_date, $meeting_time, $location, $notes, $meeting_id);

if ($stmt->execute()) {
    // Get updater name
    $stmt = $conn->prepare("SELECT full_name FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $updater = $stmt->get_result()->fetch_assoc();
    
    // Create notification for the other user
    $formatted_date = date('M d, Y', strtotime($meeting_date));
    $formatted_time = date('g:i A', strtotime($meeting_time));
    $notification_title = 'Meeting Updated';
    $notification_message = $updater['full_name'] . ' updated the meeting details. New time: ' . $formatted_date . ' at ' . $formatted_time;
    
    createNotification($recipient_id, 'meeting_updated', $notification_title, $notification_message, $meeting_id, 'meeting');
    
    echo json_encode(['success' => true, 'message' => 'Meeting updated successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update meeting']);
}
