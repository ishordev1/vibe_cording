<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';

$pageTitle = 'My Ideas - ' . APP_NAME;
require_once __DIR__ . '/../../includes/header.php';

// Require creator login
requireUserType(USER_TYPE_CREATOR);

$conn = getDBConnection();
$user_id = getCurrentUserId();

// Get all user's ideas
$stmt = $conn->prepare("
    SELECT i.*, 
           (SELECT COUNT(*) FROM investors_interested WHERE idea_id = i.id) as interested_count,
           (SELECT file_path FROM idea_media WHERE idea_id = i.id AND is_primary = 1 LIMIT 1) as primary_image
    FROM ideas i 
    WHERE i.user_id = ? 
    ORDER BY i.created_at DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$ideas = $stmt->get_result();
?>

<div class="container mt-4 mb-5">
    <div class="row mb-4">
        <div class="col">
            <h2>My Ideas</h2>
            <p class="text-muted">Manage all your posted business ideas</p>
        </div>
        <div class="col-auto">
            <a href="<?php echo APP_URL; ?>/pages/creator/create-idea.php" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Post New Idea
            </a>
        </div>
    </div>

    <?php if ($ideas->num_rows > 0): ?>
        <div class="row">
            <?php while ($idea = $ideas->fetch_assoc()): ?>
                <div class="col-md-6 col-lg-4 mb-4">
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
                            <div class="d-flex justify-content-between mb-2">
                                <span class="badge bg-secondary"><?php echo htmlspecialchars($idea['category']); ?></span>
                                <?php
                                $statusColors = [
                                    'published' => 'success',
                                    'draft' => 'warning',
                                    'archived' => 'secondary'
                                ];
                                $statusColor = $statusColors[$idea['status']] ?? 'secondary';
                                ?>
                                <span class="badge bg-<?php echo $statusColor; ?>">
                                    <?php echo ucfirst($idea['status']); ?>
                                </span>
                            </div>
                            
                            <h5 class="card-title"><?php echo htmlspecialchars($idea['title']); ?></h5>
                            <p class="card-text"><?php echo truncateText($idea['description'], 120); ?></p>
                            
                            <div class="d-flex justify-content-between text-muted small mb-2">
                                <span><i class="bi bi-eye"></i> <?php echo $idea['views']; ?> views</span>
                                <span><i class="bi bi-heart-fill text-danger"></i> <?php echo $idea['interested_count']; ?> interested</span>
                            </div>
                            
                            <small class="text-muted">Posted <?php echo timeAgo($idea['created_at']); ?></small>
                        </div>
                        
                        <div class="card-footer bg-white">
                            <div class="btn-group w-100" role="group">
                                <a href="<?php echo APP_URL; ?>/pages/idea-detail.php?id=<?php echo $idea['id']; ?>" 
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i> View
                                </a>
                                <a href="<?php echo APP_URL; ?>/pages/creator/edit-idea.php?id=<?php echo $idea['id']; ?>" 
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                                <a href="<?php echo APP_URL; ?>/actions/delete-idea.php?id=<?php echo $idea['id']; ?>" 
                                   class="btn btn-sm btn-outline-danger" 
                                   onclick="return confirm('Are you sure you want to delete this idea?');">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="text-center py-5">
            <i class="bi bi-lightbulb" style="font-size: 5rem; color: #ddd;"></i>
            <h4 class="mt-3">No ideas yet</h4>
            <p class="text-muted">Start by posting your first business idea!</p>
            <a href="<?php echo APP_URL; ?>/pages/creator/create-idea.php" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Post Your First Idea
            </a>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
