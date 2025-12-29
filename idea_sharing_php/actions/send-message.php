<?php
/**
 * Send Message Action
 * Handles sending messages in chat
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

// //debugging start
// echo "<pre>";
// print_r($_POST); 
// echo "</pre>";
// exit;
// //debugging end

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
$message = isset($_POST['message']) ? trim($_POST['message']) : '';

// Validate input
if (empty($message)) {
    echo json_encode(['success' => false, 'message' => 'Message cannot be empty']);
    exit;
}

// Verify user is part of conversation
$stmt = $conn->prepare("SELECT id, creator_id, investor_id FROM conversations WHERE id = ? AND (creator_id = ? OR investor_id = ?)");
$stmt->bind_param("iii", $conversation_id, $user_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Debug: Check if conversation exists
    $debug_stmt = $conn->prepare("SELECT creator_id, investor_id FROM conversations WHERE id = ?");
    $debug_stmt->bind_param("i", $conversation_id);
    $debug_stmt->execute();
    $debug_result = $debug_stmt->get_result();
    
    if ($debug_result->num_rows > 0) {
        $conv = $debug_result->fetch_assoc();
        echo json_encode([
            'success' => false, 
            'message' => 'Access denied. Your ID: ' . $user_id . ', Conversation belongs to users: ' . $conv['creator_id'] . ' and ' . $conv['investor_id']
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Conversation ID ' . $conversation_id . ' does not exist']);
    }
    exit;
}

// Insert message
$stmt = $conn->prepare("INSERT INTO messages (conversation_id, sender_id, message) VALUES (?, ?, ?)");
$stmt->bind_param("iis", $conversation_id, $user_id, $message);

if ($stmt->execute()) {
    // Update conversation last_message_at
    $stmt = $conn->prepare("UPDATE conversations SET last_message_at = NOW() WHERE id = ?");
    $stmt->bind_param("i", $conversation_id);
    $stmt->execute();
    
    echo json_encode(['success' => true, 'message' => 'Message sent']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to send message']);
}
