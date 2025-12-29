<?php
/**
 * Schedule Meeting Action
 * Creates a new meeting
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
$conversation_id = isset($_POST['conversation_id']) ? (int)$_POST['conversation_id'] : 0;
$meeting_date = isset($_POST['meeting_date']) ? sanitize($_POST['meeting_date']) : '';
$meeting_time = isset($_POST['meeting_time']) ? sanitize($_POST['meeting_time']) : '';
$location = isset($_POST['location']) ? sanitize($_POST['location']) : '';
$notes = isset($_POST['notes']) ? sanitize($_POST['notes']) : '';

// Validate input
if (empty($meeting_date) || empty($meeting_time)) {
    echo json_encode(['success' => false, 'message' => 'Date and time are required']);
    exit;
}

// Verify user is part of conversation and get other user
$stmt = $conn->prepare("SELECT creator_id, investor_id FROM conversations WHERE id = ? AND (creator_id = ? OR investor_id = ?)");
$stmt->bind_param("iii", $conversation_id, $user_id, $user_id);
$stmt->execute();
$conversation = $stmt->get_result()->fetch_assoc();

if (!$conversation) {
    echo json_encode(['success' => false, 'message' => 'Conversation not found']);
    exit;
}

// Determine the recipient (other user in conversation)
$recipient_id = ($conversation['creator_id'] == $user_id) ? $conversation['investor_id'] : $conversation['creator_id'];

// Insert meeting
$stmt = $conn->prepare("INSERT INTO meetings (conversation_id, scheduled_by, meeting_date, meeting_time, location, notes) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("iissss", $conversation_id, $user_id, $meeting_date, $meeting_time, $location, $notes);

if ($stmt->execute()) {
    $meeting_id = $conn->insert_id;
    
    // Get scheduler name
    $stmt = $conn->prepare("SELECT full_name FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $scheduler = $stmt->get_result()->fetch_assoc();
    
    // Create notification for the other user
    $formatted_date = date('M d, Y', strtotime($meeting_date));
    $formatted_time = date('g:i A', strtotime($meeting_time));
    $notification_title = 'New Meeting Scheduled';
    $notification_message = $scheduler['full_name'] . ' scheduled a meeting with you on ' . $formatted_date . ' at ' . $formatted_time;
    
    createNotification($recipient_id, 'meeting_scheduled', $notification_title, $notification_message, $meeting_id, 'meeting');
    
    echo json_encode(['success' => true, 'message' => 'Meeting scheduled successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to schedule meeting']);
}
