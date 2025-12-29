<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';

$pageTitle = 'Investor Dashboard - ' . APP_NAME;
require_once __DIR__ . '/../../includes/header.php';

// Require investor login
requireUserType(USER_TYPE_INVESTOR);

$conn = getDBConnection();
$user_id = getCurrentUserId();

// Get interested ideas count
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM investors_interested WHERE investor_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$interested_count = $stmt->get_result()->fetch_assoc()['total'];

// Get active conversations count
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM conversations WHERE investor_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$conversations_count = $stmt->get_result()->fetch_assoc()['total'];

// Get scheduled meetings count
$stmt = $conn->prepare("
    SELECT COUNT(*) as total 
    FROM meetings m
    JOIN conversations c ON m.conversation_id = c.id
    WHERE c.investor_id = ? AND m.status = 'pending'
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$meetings_count = $stmt->get_result()->fetch_assoc()['total'];

// Get interested ideas
$stmt = $conn->prepare("
    SELECT i.*, u.full_name as creator_name, u.username as creator_username,
           (SELECT file_path FROM idea_media WHERE idea_id = i.id AND is_primary = 1 LIMIT 1) as primary_image,
           ii.created_at as interested_at
    FROM ideas i
    JOIN investors_interested ii ON i.id = ii.idea_id
    JOIN users u ON i.user_id = u.id
    WHERE ii.investor_id = ?
    ORDER BY ii.created_at DESC
    LIMIT 6
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$interested_ideas = $stmt->get_result();

// Get recent ideas (exclude interested ones)
$stmt = $conn->prepare("
    SELECT i.*, u.full_name as creator_name,
           (SELECT file_path FROM idea_media WHERE idea_id = i.id AND is_primary = 1 LIMIT 1) as primary_image
    FROM ideas i
    JOIN users u ON i.user_id = u.id
    WHERE i.status = 'published' 
    AND i.id NOT IN (SELECT idea_id FROM investors_interested WHERE investor_id = ?)
    ORDER BY i.created_at DESC
    LIMIT 6
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$recent_ideas = $stmt->get_result();
?>

<div class="container mt-4 mb-5">
    <div class="row mb-4">
        <div class="col">
            <h2>Welcome back, <?php echo htmlspecialchars($_SESSION['full_name']); ?>!</h2>
            <p class="text-muted">Discover innovative ideas and connect with entrepreneurs</p>
        </div>
        <div class="col-auto">
            <a href="<?php echo APP_URL; ?>/pages/ideas.php" class="btn btn-primary">
                <i class="bi bi-search"></i> Browse All Ideas
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <i class="bi bi-heart-fill text-danger" style="font-size: 2rem;"></i>
                    <h3 class="mt-2"><?php echo $interested_count; ?></h3>
                    <p class="text-muted mb-0">Interested Ideas</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <i class="bi bi-chat-dots text-info" style="font-size: 2rem;"></i>
                    <h3 class="mt-2"><?php echo $conversations_count; ?></h3>
                    <p class="text-muted mb-0">Active Conversations</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <i class="bi bi-calendar-event text-success" style="font-size: 2rem;"></i>
                    <h3 class="mt-2"><?php echo $meetings_count; ?></h3>
                    <p class="text-muted mb-0">Pending Meetings</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Interested Ideas -->
    <?php if ($interested_ideas->num_rows > 0): ?>
    <div class="row mb-4">
        <div class="col-12">
            <h4 class="mb-3">My Interested Ideas</h4>
        </div>
        <?php while ($idea = $interested_ideas->fetch_assoc()): ?>
            <div class="col-md-4 mb-3">
                <div class="card h-100 shadow-sm">
                    <?php if ($idea['primary_image']): ?>
                        <img src="<?php echo APP_URL . '/' . $idea['primary_image']; ?>" 
                             class="card-img-top" alt="<?php echo htmlspecialchars($idea['title']); ?>"
                             style="height: 200px; object-fit: cover;">
                    <?php else: ?>
                        <div class="bg-secondary text-white d-flex align-items-center justify-content-center" style="height: 200px;">
                            <i class="bi bi-image" style="font-size: 3rem;"></i>
                        </div>
                    <?php endif; ?>
                    <div class="card-body">
                        <span class="badge bg-primary mb-2"><?php echo htmlspecialchars($idea['category']); ?></span>
                        <h5 class="card-title"><?php echo htmlspecialchars($idea['title']); ?></h5>
                        <p class="card-text text-muted small">
                            <i class="bi bi-person"></i> <?php echo htmlspecialchars($idea['creator_name']); ?>
                        </p>
                        <p class="card-text"><?php echo truncateText($idea['description'], 100); ?></p>
                        <small class="text-muted">Interested <?php echo timeAgo($idea['interested_at']); ?></small>
                    </div>
                    <div class="card-footer bg-white">
                        <a href="<?php echo APP_URL; ?>/pages/idea-detail.php?id=<?php echo $idea['id']; ?>" 
                           class="btn btn-outline-primary btn-sm w-100">View Details</a>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
    <?php endif; ?>

    <!-- Recent Ideas -->
    <div class="row">
        <div class="col-12">
            <h4 class="mb-3">Discover New Ideas</h4>
        </div>
        <?php if ($recent_ideas->num_rows > 0): ?>
            <?php while ($idea = $recent_ideas->fetch_assoc()): ?>
                <div class="col-md-4 mb-3">
                    <div class="card h-100 shadow-sm">
                        <?php if ($idea['primary_image']): ?>
                            <img src="<?php echo APP_URL . '/' . $idea['primary_image']; ?>" 
                                 class="card-img-top" alt="<?php echo htmlspecialchars($idea['title']); ?>"
                                 style="height: 200px; object-fit: cover;">
                        <?php else: ?>
                            <div class="bg-secondary text-white d-flex align-items-center justify-content-center" style="height: 200px;">
                                <i class="bi bi-image" style="font-size: 3rem;"></i>
                            </div>
                        <?php endif; ?>
                        <div class="card-body">
                            <span class="badge bg-primary mb-2"><?php echo htmlspecialchars($idea['category']); ?></span>
                            <h5 class="card-title"><?php echo htmlspecialchars($idea['title']); ?></h5>
                            <p class="card-text text-muted small">
                                <i class="bi bi-person"></i> <?php echo htmlspecialchars($idea['creator_name']); ?>
                            </p>
                            <p class="card-text"><?php echo truncateText($idea['description'], 100); ?></p>
                            <small class="text-muted"><i class="bi bi-eye"></i> <?php echo $idea['views']; ?> views</small>
                        </div>
                        <div class="card-footer bg-white">
                            <a href="<?php echo APP_URL; ?>/pages/idea-detail.php?id=<?php echo $idea['id']; ?>" 
                               class="btn btn-primary btn-sm w-100">View Details</a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="bi bi-lightbulb" style="font-size: 4rem; color: #ddd;"></i>
                    <h5 class="mt-3">No new ideas at the moment</h5>
                    <p class="text-muted">Check back later for new opportunities!</p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
