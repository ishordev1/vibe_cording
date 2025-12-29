<?php
/**
 * Start Conversation Action
 * Creates a new conversation between investor and creator
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

// Require investor login
if (!isLoggedIn() || getCurrentUserType() !== USER_TYPE_INVESTOR) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$conn = getDBConnection();
$investor_id = getCurrentUserId();
$idea_id = isset($_POST['idea_id']) ? (int)$_POST['idea_id'] : 0;

// Get idea and creator info
$stmt = $conn->prepare("SELECT id, user_id FROM ideas WHERE id = ? AND status = 'published'");
$stmt->bind_param("i", $idea_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Idea not found']);
    exit;
}

$idea = $result->fetch_assoc();
$creator_id = $idea['user_id'];

// Check if investor has shown interest
$stmt = $conn->prepare("SELECT id FROM investors_interested WHERE idea_id = ? AND investor_id = ?");
$stmt->bind_param("ii", $idea_id, $investor_id);
$stmt->execute();

if ($stmt->get_result()->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'You must mark interest first']);
    exit;
}

// Check if conversation already exists
$stmt = $conn->prepare("SELECT id FROM conversations WHERE idea_id = ? AND creator_id = ? AND investor_id = ?");
$stmt->bind_param("iii", $idea_id, $creator_id, $investor_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode(['success' => true, 'message' => 'Conversation already exists']);
    exit;
}

// Create conversation
$stmt = $conn->prepare("INSERT INTO conversations (idea_id, creator_id, investor_id) VALUES (?, ?, ?)");
$stmt->bind_param("iii", $idea_id, $creator_id, $investor_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Conversation started']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to start conversation']);
}
