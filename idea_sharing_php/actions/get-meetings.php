<?php
/**
 * Get Meetings Action
 * Retrieves meetings for a conversation
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

// Require login
if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$conn = getDBConnection();
$user_id = getCurrentUserId();
$conversation_id = isset($_GET['conversation_id']) ? (int)$_GET['conversation_id'] : 0;

// Verify user is part of conversation
$stmt = $conn->prepare("SELECT id FROM conversations WHERE id = ? AND (creator_id = ? OR investor_id = ?)");
$stmt->bind_param("iii", $conversation_id, $user_id, $user_id);
$stmt->execute();

if ($stmt->get_result()->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Conversation not found']);
    exit;
}

// Get meetings
$stmt = $conn->prepare("
    SELECT m.*, u.full_name as scheduled_by_name
    FROM meetings m
    JOIN users u ON m.scheduled_by = u.id
    WHERE m.conversation_id = ?
    ORDER BY m.meeting_date ASC, m.meeting_time ASC
");
$stmt->bind_param("i", $conversation_id);
$stmt->execute();
$result = $stmt->get_result();

$meetings = [];
while ($row = $result->fetch_assoc()) {
    $meetings[] = [
        'id' => (int)$row['id'],
        'scheduled_by' => (int)$row['scheduled_by'],
        'scheduled_by_name' => $row['scheduled_by_name'],
        'meeting_date' => $row['meeting_date'],
        'meeting_time' => $row['meeting_time'],
        'formatted_date' => date('F j, Y', strtotime($row['meeting_date'])),
        'formatted_time' => date('g:i A', strtotime($row['meeting_time'])),
        'location' => $row['location'],
        'notes' => $row['notes'],
        'status' => $row['status']
    ];
}

echo json_encode(['success' => true, 'meetings' => $meetings]);
