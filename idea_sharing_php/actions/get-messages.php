<?php
/**
 * Get Messages Action
 * Retrieves new messages for a conversation (for polling)
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
$after_id = isset($_GET['after_id']) ? (int)$_GET['after_id'] : 0;

// Verify user is part of conversation
$stmt = $conn->prepare("SELECT id FROM conversations WHERE id = ? AND (creator_id = ? OR investor_id = ?)");
$stmt->bind_param("iii", $conversation_id, $user_id, $user_id);
$stmt->execute();

if ($stmt->get_result()->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Conversation not found']);
    exit;
}

// Get new messages
$stmt = $conn->prepare("
    SELECT m.*, u.full_name as sender_name
    FROM messages m
    JOIN users u ON m.sender_id = u.id
    WHERE m.conversation_id = ? AND m.id > ?
    ORDER BY m.created_at ASC
");
$stmt->bind_param("ii", $conversation_id, $after_id);
$stmt->execute();
$result = $stmt->get_result();

$messages = [];
while ($row = $result->fetch_assoc()) {
    $messages[] = [
        'id' => (int)$row['id'],
        'sender_id' => (int)$row['sender_id'],
        'sender_name' => $row['sender_name'],
        'message' => $row['message'],
        'created_at' => $row['created_at'],
        'time_ago' => timeAgo($row['created_at'])
    ];
}

// Mark new messages as read if they're not from current user
if (count($messages) > 0) {
    $stmt = $conn->prepare("UPDATE messages SET is_read = 1 WHERE conversation_id = ? AND sender_id != ? AND is_read = 0");
    $stmt->bind_param("ii", $conversation_id, $user_id);
    $stmt->execute();
}

echo json_encode(['success' => true, 'messages' => $messages]);
