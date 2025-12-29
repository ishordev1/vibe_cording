<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';

$pageTitle = 'Creator Dashboard - ' . APP_NAME;
require_once __DIR__ . '/../../includes/header.php';

// Require creator login
requireUserType(USER_TYPE_CREATOR);

$conn = getDBConnection();
$user_id = getCurrentUserId();

// Get user's ideas count
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM ideas WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$ideas_count = $stmt->get_result()->fetch_assoc()['total'];

// Get total views
$stmt = $conn->prepare("SELECT SUM(views) as total_views FROM ideas WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$total_views = $stmt->get_result()->fetch_assoc()['total_views'] ?? 0;

// Get interested investors count
$stmt = $conn->prepare("
    SELECT COUNT(DISTINCT ii.investor_id) as total 
    FROM investors_interested ii 
    JOIN ideas i ON ii.idea_id = i.id 
    WHERE i.user_id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$interested_count = $stmt->get_result()->fetch_assoc()['total'];

// Get active conversations count
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM conversations WHERE creator_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$conversations_count = $stmt->get_result()->fetch_assoc()['total'];

// Get recent ideas
$stmt = $conn->prepare("
    SELECT i.*, 
           (SELECT COUNT(*) FROM investors_interested WHERE idea_id = i.id) as interested_count,
           (SELECT file_path FROM idea_media WHERE idea_id = i.id AND is_primary = 1 LIMIT 1) as primary_image
    FROM ideas i 
    WHERE i.user_id = ? 
    ORDER BY i.created_at DESC 
    LIMIT 5
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$recent_ideas = $stmt->get_result();
?>

<div class="container mt-4 mb-5">
    <div class="row mb-4">
        <div class="col">
            <h2>Welcome back, <?php echo htmlspecialchars($_SESSION['full_name']); ?>!</h2>
            <p class="text-muted">Manage your ideas and connect with investors</p>
        </div>
        <div class="col-auto">
            <a href="<?php echo APP_URL; ?>/pages/creator/create-idea.php" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Post New Idea
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <i class="bi bi-lightbulb text-primary" style="font-size: 2rem;"></i>
                    <h3 class="mt-2"><?php echo $ideas_count; ?></h3>
                    <p class="text-muted mb-0">Total Ideas</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <i class="bi bi-eye text-info" style="font-size: 2rem;"></i>
                    <h3 class="mt-2"><?php echo number_format($total_views); ?></h3>
                    <p class="text-muted mb-0">Total Views</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <i class="bi bi-people text-success" style="font-size: 2rem;"></i>
                    <h3 class="mt-2"><?php echo $interested_count; ?></h3>
                    <p class="text-muted mb-0">Interested Investors</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <i class="bi bi-chat-dots text-warning" style="font-size: 2rem;"></i>
                    <h3 class="mt-2"><?php echo $conversations_count; ?></h3>
                    <p class="text-muted mb-0">Active Chats</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Ideas -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">My Recent Ideas</h5>
                        <a href="<?php echo APP_URL; ?>/pages/creator/my-ideas.php" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if ($recent_ideas->num_rows > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Category</th>
                                        <th>Views</th>
                                        <th>Interested</th>
                                        <th>Status</th>
                                        <th>Posted</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($idea = $recent_ideas->fetch_assoc()): ?>
                                        <tr>
                                            <td>
                                                <a href="<?php echo APP_URL; ?>/pages/idea-detail.php?id=<?php echo $idea['id']; ?>" class="text-decoration-none">
                                                    <?php echo htmlspecialchars($idea['title']); ?>
                                                </a>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary"><?php echo htmlspecialchars($idea['category']); ?></span>
                                            </td>
                                            <td><?php echo $idea['views']; ?></td>
                                            <td><?php echo $idea['interested_count']; ?></td>
                                            <td>
                                                <?php
                                                $statusClass = $idea['status'] === 'published' ? 'success' : 'warning';
                                                ?>
                                                <span class="badge bg-<?php echo $statusClass; ?>">
                                                    <?php echo ucfirst($idea['status']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo timeAgo($idea['created_at']); ?></td>
                                            <td>
                                                <a href="<?php echo APP_URL; ?>/pages/creator/edit-idea.php?id=<?php echo $idea['id']; ?>" class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <a href="<?php echo APP_URL; ?>/actions/delete-idea.php?id=<?php echo $idea['id']; ?>" 
                                                   class="btn btn-sm btn-outline-danger" 
                                                   onclick="return confirm('Are you sure you want to delete this idea?');">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="bi bi-lightbulb" style="font-size: 4rem; color: #ddd;"></i>
                            <h5 class="mt-3">No ideas yet</h5>
                            <p class="text-muted">Start by posting your first business idea!</p>
                            <a href="<?php echo APP_URL; ?>/pages/creator/create-idea.php" class="btn btn-primary">
                                <i class="bi bi-plus-circle"></i> Post Your First Idea
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
