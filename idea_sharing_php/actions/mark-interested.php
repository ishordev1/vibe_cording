<?php
/**
 * Mark Interested Action
 * Handles investor marking interest in an idea
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

// Validate idea exists
$stmt = $conn->prepare("SELECT id, user_id FROM ideas WHERE id = ? AND status = 'published'");
$stmt->bind_param("i", $idea_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Idea not found']);
    exit;
}

$idea = $result->fetch_assoc();

// Can't be interested in own idea
if ($idea['user_id'] == $investor_id) {
    echo json_encode(['success' => false, 'message' => 'Cannot mark interest in your own idea']);
    exit;
}

// Check if already interested
$stmt = $conn->prepare("SELECT id FROM investors_interested WHERE idea_id = ? AND investor_id = ?");
$stmt->bind_param("ii", $idea_id, $investor_id);
$stmt->execute();

if ($stmt->get_result()->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Already marked as interested']);
    exit;
}

// Insert interest
$stmt = $conn->prepare("INSERT INTO investors_interested (idea_id, investor_id) VALUES (?, ?)");
$stmt->bind_param("ii", $idea_id, $investor_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Interest marked successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to mark interest']);
}
