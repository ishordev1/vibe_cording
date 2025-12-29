<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

$pageTitle = 'Idea Details - ' . APP_NAME;
require_once __DIR__ . '/../includes/header.php';

$conn = getDBConnection();
$idea_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$user_id = getCurrentUserId();
$check_user_id = $user_id ?? 0;

// Get idea details
$stmt = $conn->prepare("
    SELECT i.*, u.id as creator_id, u.full_name as creator_name, u.username as creator_username, u.bio as creator_bio, u.profile_image as creator_profile_image,
           (SELECT COUNT(*) FROM investors_interested WHERE idea_id = i.id) as interested_count
    FROM ideas i
    JOIN users u ON i.user_id = u.id
    WHERE i.id = ? AND (i.status = 'published' OR i.user_id = ?)
");
$stmt->bind_param("ii", $idea_id, $check_user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    setFlashMessage('error', 'Idea not found.');
    redirect('/pages/ideas.php');
}

$idea = $result->fetch_assoc();

// Update view count (only if not the creator)
if ($user_id !== $idea['creator_id']) {
    $stmt = $conn->prepare("UPDATE ideas SET views = views + 1 WHERE id = ?");
    $stmt->bind_param("i", $idea_id);
    $stmt->execute();
}

// Get idea images
$stmt = $conn->prepare("SELECT * FROM idea_media WHERE idea_id = ? ORDER BY is_primary DESC");
$stmt->bind_param("i", $idea_id);
$stmt->execute();
$images = $stmt->get_result();

// Check if current user is interested (for investors)
$is_interested = false;
if (isLoggedIn() && getCurrentUserType() === USER_TYPE_INVESTOR) {
    $stmt = $conn->prepare("SELECT id FROM investors_interested WHERE idea_id = ? AND investor_id = ?");
    $stmt->bind_param("ii", $idea_id, $user_id);
    $stmt->execute();
    $is_interested = $stmt->get_result()->num_rows > 0;
}

// Check if conversation exists
$conversation_exists = false;
if (isLoggedIn() && getCurrentUserType() === USER_TYPE_INVESTOR) {
    $stmt = $conn->prepare("SELECT id FROM conversations WHERE idea_id = ? AND investor_id = ?");
    $stmt->bind_param("ii", $idea_id, $user_id);
    $stmt->execute();
    $conversation_exists = $stmt->get_result()->num_rows > 0;
}
?>

<div class="container mt-4 mb-5">
    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Image Gallery -->
            <?php if ($images->num_rows > 0): ?>
                <div class="card mb-4">
                    <div id="ideaImageCarousel" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner">
                            <?php 
                            $first = true;
                            $images->data_seek(0); // Reset pointer
                            while ($img = $images->fetch_assoc()): 
                            ?>
                                <div class="carousel-item <?php echo $first ? 'active' : ''; ?>">
                                    <img src="<?php echo APP_URL . '/' . $img['file_path']; ?>" 
                                         class="d-block w-100" alt="Idea image"
                                         style="height: 400px; object-fit: cover;">
                                </div>
                            <?php 
                            $first = false;
                            endwhile; 
                            ?>
                        </div>
                        <?php if ($images->num_rows > 1): ?>
                            <button class="carousel-control-prev" type="button" data-bs-target="#ideaImageCarousel" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon"></span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#ideaImageCarousel" data-bs-slide="next">
                                <span class="carousel-control-next-icon"></span>
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Idea Details -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <span class="badge bg-primary fs-6"><?php echo htmlspecialchars($idea['category']); ?></span>
                        <span class="text-muted">
                            <i class="bi bi-eye"></i> <?php echo $idea['views']; ?> views
                        </span>
                    </div>
                    
                    <h1 class="mb-3"><?php echo htmlspecialchars($idea['title']); ?></h1>
                    
                    <div class="mb-3 text-muted">
                        <i class="bi bi-calendar"></i> Posted <?php echo timeAgo($idea['created_at']); ?>
                        <span class="ms-3">
                            <i class="bi bi-heart-fill text-danger"></i> <?php echo $idea['interested_count']; ?> investors interested
                        </span>
                    </div>
                    
                    <hr>
                    
                    <div class="idea-description">
                        <h4>Description</h4>
                        <div class="content"><?php echo renderHTML($idea['description']); ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Creator Info -->
            <div class="card mb-4">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <?php if (!empty($idea['creator_profile_image']) && $idea['creator_profile_image'] !== 'default-avatar.png' && file_exists(UPLOAD_DIR . $idea['creator_profile_image'])): ?>
                            <img src="<?php echo APP_URL; ?>/uploads/<?php echo htmlspecialchars($idea['creator_profile_image']); ?>" 
                                 alt="<?php echo htmlspecialchars($idea['creator_name']); ?>" 
                                 class="rounded-circle" 
                                 style="width: 120px; height: 120px; object-fit: cover; border: 3px solid #0d6efd;">
                        <?php else: ?>
                            <i class="bi bi-person-circle" style="font-size: 4rem; color: #6c757d;"></i>
                        <?php endif; ?>
                    </div>
                    <h5><?php echo htmlspecialchars($idea['creator_name']); ?></h5>
                    <p class="text-muted">@<?php echo htmlspecialchars($idea['creator_username']); ?></p>
                    <?php if ($idea['creator_bio']): ?>
                        <p class="small"><?php echo htmlspecialchars($idea['creator_bio']); ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Action Buttons -->
            <?php if (isLoggedIn()): ?>
                <?php if (getCurrentUserType() === USER_TYPE_INVESTOR && $user_id !== $idea['creator_id']): ?>
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Interested in this idea?</h5>
                            
                            <?php if (!$is_interested): ?>
                                <button id="interestedBtn" class="btn btn-primary w-100 mb-2">
                                    <i class="bi bi-heart"></i> I'm Interested
                                </button>
                            <?php else: ?>
                                <div class="alert alert-success">
                                    <i class="bi bi-check-circle"></i> You've shown interest in this idea
                                </div>
                                
                                <?php if ($conversation_exists): ?>
                                    <a href="<?php echo APP_URL; ?>/pages/chat/conversation.php?idea_id=<?php echo $idea_id; ?>" 
                                       class="btn btn-success w-100">
                                        <i class="bi bi-chat-dots"></i> Continue Chat
                                    </a>
                                <?php else: ?>
                                    <button id="startChatBtn" class="btn btn-success w-100">
                                        <i class="bi bi-chat-dots"></i> Start Chat
                                    </button>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php elseif ($user_id === $idea['creator_id']): ?>
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Your Idea</h5>
                            <a href="<?php echo APP_URL; ?>/pages/creator/edit-idea.php?id=<?php echo $idea_id; ?>" 
                               class="btn btn-primary w-100 mb-2">
                                <i class="bi bi-pencil"></i> Edit Idea
                            </a>
                            <a href="<?php echo APP_URL; ?>/actions/delete-idea.php?id=<?php echo $idea_id; ?>" 
                               class="btn btn-outline-danger w-100"
                               onclick="return confirm('Are you sure you want to delete this idea?');">
                                <i class="bi bi-trash"></i> Delete Idea
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="card">
                    <div class="card-body text-center">
                        <h5 class="card-title">Interested in this idea?</h5>
                        <p class="text-muted">Sign in or create an account to connect with the creator</p>
                        <a href="<?php echo APP_URL; ?>/pages/signin.php" class="btn btn-primary w-100 mb-2">Sign In</a>
                        <a href="<?php echo APP_URL; ?>/pages/signup.php" class="btn btn-outline-primary w-100">Sign Up</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// Handle "I'm Interested" button
<?php if (isLoggedIn() && getCurrentUserType() === USER_TYPE_INVESTOR && !$is_interested): ?>
document.getElementById('interestedBtn')?.addEventListener('click', function() {
    $.post('<?php echo APP_URL; ?>/actions/mark-interested.php', {
        idea_id: <?php echo $idea_id; ?>
    }, function(response) {
        if (response.success) {
            location.reload();
        } else {
            alert(response.message || 'An error occurred');
        }
    }, 'json');
});
<?php endif; ?>

// Handle "Start Chat" button
<?php if (isLoggedIn() && getCurrentUserType() === USER_TYPE_INVESTOR && $is_interested && !$conversation_exists): ?>
document.getElementById('startChatBtn')?.addEventListener('click', function() {
    $.post('<?php echo APP_URL; ?>/actions/start-conversation.php', {
        idea_id: <?php echo $idea_id; ?>
    }, function(response) {
        if (response.success) {
            window.location.href = '<?php echo APP_URL; ?>/pages/chat/conversation.php?idea_id=<?php echo $idea_id; ?>';
        } else {
            alert(response.message || 'An error occurred');
        }
    }, 'json');
});
<?php endif; ?>
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
