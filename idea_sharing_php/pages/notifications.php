<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

// Require login
if (!isLoggedIn()) {
    header('Location: ' . APP_URL . '/pages/signin.php');
    exit;
}

$pageTitle = 'Notifications';
require_once __DIR__ . '/../includes/header.php';

$conn = getDBConnection();
$user_id = getCurrentUserId();

// Mark all as read if requested
if (isset($_GET['mark_all_read'])) {
    $stmt = $conn->prepare("UPDATE notifications SET is_read = TRUE WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    header('Location: ' . APP_URL . '/pages/notifications.php');
    exit;
}

// Mark single notification as read
if (isset($_GET['mark_read'])) {
    $notification_id = (int)$_GET['mark_read'];
    $stmt = $conn->prepare("UPDATE notifications SET is_read = TRUE WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $notification_id, $user_id);
    $stmt->execute();
}

// Get all notifications for the user
$stmt = $conn->prepare("
    SELECT * FROM notifications 
    WHERE user_id = ? 
    ORDER BY created_at DESC 
    LIMIT 50
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$notifications = $stmt->get_result();

$unread_count = getUnreadNotificationCount($user_id);
?>

<div class="container py-4">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>
                    <i class="bi bi-bell"></i> Notifications
                    <?php if ($unread_count > 0): ?>
                        <span class="badge bg-danger"><?php echo $unread_count; ?></span>
                    <?php endif; ?>
                </h2>
                <?php if ($unread_count > 0): ?>
                    <a href="?mark_all_read=1" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-check-all"></i> Mark All as Read
                    </a>
                <?php endif; ?>
            </div>

            <?php if ($notifications->num_rows > 0): ?>
                <div class="list-group">
                    <?php while ($notification = $notifications->fetch_assoc()): ?>
                        <div class="list-group-item list-group-item-action <?php echo !$notification['is_read'] ? 'list-group-item-light border-primary' : ''; ?>">
                            <div class="d-flex w-100 justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center mb-1">
                                        <?php
                                        $icon = 'bi-bell';
                                        $badge_class = 'bg-secondary';
                                        
                                        switch($notification['type']) {
                                            case 'message':
                                                $icon = 'bi-chat-dots';
                                                $badge_class = 'bg-primary';
                                                break;
                                            case 'meeting_scheduled':
                                                $icon = 'bi-calendar-plus';
                                                $badge_class = 'bg-success';
                                                break;
                                            case 'meeting_updated':
                                                $icon = 'bi-calendar-check';
                                                $badge_class = 'bg-info';
                                                break;
                                            case 'meeting_confirmed':
                                                $icon = 'bi-calendar-check';
                                                $badge_class = 'bg-success';
                                                break;
                                            case 'meeting_cancelled':
                                                $icon = 'bi-calendar-x';
                                                $badge_class = 'bg-danger';
                                                break;
                                            case 'interest':
                                                $icon = 'bi-heart-fill';
                                                $badge_class = 'bg-danger';
                                                break;
                                        }
                                        ?>
                                        <i class="bi <?php echo $icon; ?> text-muted me-2"></i>
                                        <h6 class="mb-0"><?php echo htmlspecialchars($notification['title']); ?></h6>
                                        <?php if (!$notification['is_read']): ?>
                                            <span class="badge bg-primary ms-2">New</span>
                                        <?php endif; ?>
                                    </div>
                                    <p class="mb-1"><?php echo htmlspecialchars($notification['message']); ?></p>
                                    <small class="text-muted">
                                        <i class="bi bi-clock"></i> <?php echo timeAgo($notification['created_at']); ?>
                                    </small>
                                </div>
                                <div class="ms-3">
                                    <?php if (!$notification['is_read']): ?>
                                        <a href="?mark_read=<?php echo $notification['id']; ?>" 
                                           class="btn btn-sm btn-outline-primary"
                                           title="Mark as read">
                                            <i class="bi bi-check"></i>
                                        </a>
                                    <?php endif; ?>
                                    <?php if ($notification['related_type'] === 'meeting' && $notification['related_id']): ?>
                                        <?php
                                        // Get conversation ID for the meeting
                                        $stmt = $conn->prepare("SELECT conversation_id FROM meetings WHERE id = ?");
                                        $stmt->bind_param("i", $notification['related_id']);
                                        $stmt->execute();
                                        $meeting_result = $stmt->get_result()->fetch_assoc();
                                        if ($meeting_result):
                                        ?>
                                            <a href="<?php echo APP_URL; ?>/pages/chat/conversation.php?id=<?php echo $meeting_result['conversation_id']; ?>" 
                                               class="btn btn-sm btn-primary ms-1">
                                                View
                                            </a>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="bi bi-bell-slash text-muted" style="font-size: 4rem;"></i>
                    <h4 class="mt-3 text-muted">No Notifications</h4>
                    <p class="text-muted">You're all caught up!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
