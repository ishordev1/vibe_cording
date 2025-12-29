<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';

$pageTitle = 'Conversations - ' . APP_NAME;
require_once __DIR__ . '/../../includes/header.php';

// Require login
requireLogin();

$conn = getDBConnection();
$user_id = getCurrentUserId();
$user_type = getCurrentUserType();

// Get conversations based on user type
if ($user_type === USER_TYPE_CREATOR) {
    $query = "
        SELECT c.*, i.title as idea_title, u.full_name as other_user_name, u.username as other_user_username,
               (SELECT COUNT(*) FROM messages WHERE conversation_id = c.id AND sender_id != ? AND is_read = 0) as unread_count,
               (SELECT message FROM messages WHERE conversation_id = c.id ORDER BY created_at DESC LIMIT 1) as last_message
        FROM conversations c
        JOIN ideas i ON c.idea_id = i.id
        JOIN users u ON c.investor_id = u.id
        WHERE c.creator_id = ?
        ORDER BY c.last_message_at DESC
    ";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $user_id, $user_id);
} else {
    $query = "
        SELECT c.*, i.title as idea_title, u.full_name as other_user_name, u.username as other_user_username,
               (SELECT COUNT(*) FROM messages WHERE conversation_id = c.id AND sender_id != ? AND is_read = 0) as unread_count,
               (SELECT message FROM messages WHERE conversation_id = c.id ORDER BY created_at DESC LIMIT 1) as last_message
        FROM conversations c
        JOIN ideas i ON c.idea_id = i.id
        JOIN users u ON c.creator_id = u.id
        WHERE c.investor_id = ?
        ORDER BY c.last_message_at DESC
    ";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $user_id, $user_id);
}

$stmt->execute();
$conversations = $stmt->get_result();
?>

<div class="container mt-4 mb-5">
    <div class="row">
        <div class="col">
            <h2>My Conversations</h2>
            <p class="text-muted">Chat with <?php echo $user_type === USER_TYPE_CREATOR ? 'investors' : 'idea creators'; ?></p>
        </div>
    </div>

    <?php if ($conversations->num_rows > 0): ?>
        <div class="row">
            <div class="col-md-12">
                <div class="list-group">
                    <?php while ($conv = $conversations->fetch_assoc()): ?>
                        <a href="<?php echo APP_URL; ?>/pages/chat/conversation.php?id=<?php echo $conv['id']; ?>" 
                           class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <h5 class="mb-1">
                                        <?php echo htmlspecialchars($conv['other_user_name']); ?>
                                        <?php if ($conv['unread_count'] > 0): ?>
                                            <span class="badge bg-danger"><?php echo $conv['unread_count']; ?> new</span>
                                        <?php endif; ?>
                                    </h5>
                                    <p class="mb-1 text-muted small">
                                        <i class="bi bi-lightbulb"></i> <?php echo htmlspecialchars($conv['idea_title']); ?>
                                    </p>
                                    <?php if ($conv['last_message']): ?>
                                        <p class="mb-1 text-muted">
                                            <?php echo truncateText($conv['last_message'], 80); ?>
                                        </p>
                                    <?php endif; ?>
                                </div>
                                <small class="text-muted"><?php echo timeAgo($conv['last_message_at']); ?></small>
                            </div>
                        </a>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="text-center py-5">
            <i class="bi bi-chat-dots" style="font-size: 5rem; color: #ddd;"></i>
            <h4 class="mt-3">No conversations yet</h4>
            <p class="text-muted">
                <?php if ($user_type === USER_TYPE_INVESTOR): ?>
                    Browse ideas and show interest to start a conversation
                <?php else: ?>
                    Conversations will appear here when investors show interest in your ideas
                <?php endif; ?>
            </p>
            <?php if ($user_type === USER_TYPE_INVESTOR): ?>
                <a href="<?php echo APP_URL; ?>/pages/ideas.php" class="btn btn-primary">Browse Ideas</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
