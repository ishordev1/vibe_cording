<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';

$pageTitle = 'Welcome to ' . APP_NAME;
require_once __DIR__ . '/includes/header.php';

$conn = getDBConnection();

// Get some featured ideas
$stmt = $conn->prepare("
    SELECT i.*, u.full_name as creator_name,
           (SELECT COUNT(*) FROM investors_interested WHERE idea_id = i.id) as interested_count,
           (SELECT file_path FROM idea_media WHERE idea_id = i.id AND is_primary = 1 LIMIT 1) as primary_image
    FROM ideas i
    JOIN users u ON i.user_id = u.id
    WHERE i.status = 'published'
    ORDER BY i.views DESC, i.created_at DESC
    LIMIT 6
");
$stmt->execute();
$featured_ideas = $stmt->get_result();

// Get statistics
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM ideas WHERE status = 'published'");
$stmt->execute();
$total_ideas = $stmt->get_result()->fetch_assoc()['total'];

$stmt = $conn->prepare("SELECT COUNT(*) as total FROM users WHERE user_type = 'creator'");
$stmt->execute();
$total_creators = $stmt->get_result()->fetch_assoc()['total'];

$stmt = $conn->prepare("SELECT COUNT(*) as total FROM users WHERE user_type = 'investor'");
$stmt->execute();
$total_investors = $stmt->get_result()->fetch_assoc()['total'];
?>

<!-- Hero Section -->
<div class="bg-primary text-white py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <h1 class="display-4 fw-bold mb-4">Turn Your Ideas Into Reality</h1>
                <p class="lead mb-4">
                    Connect innovative entrepreneurs with visionary investors. Share your business ideas and find the perfect investor to bring them to life.
                </p>
                <div class="d-grid gap-2 d-md-flex">
                    <?php if (!isLoggedIn()): ?>
                        <a href="<?php echo APP_URL; ?>/pages/signup.php" class="btn btn-light btn-lg px-4">
                            Get Started
                        </a>
                        <a href="<?php echo APP_URL; ?>/pages/ideas.php" class="btn btn-outline-light btn-lg px-4">
                            Browse Ideas
                        </a>
                    <?php else: ?>
                        <?php if (getCurrentUserType() === USER_TYPE_CREATOR): ?>
                            <a href="<?php echo APP_URL; ?>/pages/creator/create-idea.php" class="btn btn-light btn-lg px-4">
                                Post Your Idea
                            </a>
                        <?php endif; ?>
                        <a href="<?php echo APP_URL; ?>/pages/ideas.php" class="btn btn-outline-light btn-lg px-4">
                            Browse Ideas
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-lg-5 text-center mt-4 mt-lg-0">
                <i class="bi bi-lightbulb-fill" style="font-size: 10rem; opacity: 0.8;"></i>
            </div>
        </div>
    </div>
</div>

<!-- Featured Ideas Section -->
<?php if ($featured_ideas->num_rows > 0): ?>
<div class="bg-light py-2 pt-5">
    <div class="container">
        <div class="row mb-4">
            <div class="col">
                <h2 class="display-6 fw-bold mb-3">Featured Ideas</h2>
                <p class="lead text-muted">Discover the latest innovative business ideas</p>
            </div>
            <div class="col-auto">
                <a href="<?php echo APP_URL; ?>/pages/ideas.php" class="btn btn-primary">
                    View All Ideas <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
        
        <div class="row">
            <?php while ($idea = $featured_ideas->fetch_assoc()): ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        <?php if ($idea['primary_image']): ?>
                            <img src="<?php echo APP_URL . '/' . $idea['primary_image']; ?>" 
                                 class="card-img-top" alt="<?php echo htmlspecialchars($idea['title']); ?>"
                                 style="height: 200px; object-fit: cover;">
                        <?php else: ?>
                            <div class="bg-secondary text-white d-flex align-items-center justify-content-center" style="height: 200px;">
                                <i class="bi bi-lightbulb" style="font-size: 3rem;"></i>
                            </div>
                        <?php endif; ?>
                        
                        <div class="card-body">
                            <span class="badge bg-primary mb-2"><?php echo htmlspecialchars($idea['category']); ?></span>
                            <h5 class="card-title"><?php echo htmlspecialchars($idea['title']); ?></h5>
                            <p class="card-text"><?php echo truncateText($idea['description'], 100); ?></p>
                            
                            <div class="d-flex justify-content-between text-muted small">
                                <span><i class="bi bi-person"></i> <?php echo htmlspecialchars($idea['creator_name']); ?></span>
                                <span><i class="bi bi-heart-fill text-danger"></i> <?php echo $idea['interested_count']; ?></span>
                            </div>
                        </div>
                        
                        <div class="card-footer bg-white">
                            <a href="<?php echo APP_URL; ?>/pages/idea-detail.php?id=<?php echo $idea['id']; ?>" 
                               class="btn btn-outline-primary w-100">View Details</a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</div>
<?php endif; ?>


<!-- Statistics Section -->
<div class="bg-light ">
    <div class="container">
        <div class="row text-center">
           <h2 class="display-5 fw-bold mb-3 border rounded border-3  p-3">Total Creator and Investor</h2>
            <div class="col-md-4 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <i class="bi bi-lightbulb text-primary" style="font-size: 3rem;"></i>
                        <h2 class="mt-3 mb-0"><?php echo number_format($total_ideas); ?></h2>
                        <p class="text-muted mb-0">Business Ideas</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <i class="bi bi-people text-success" style="font-size: 3rem;"></i>
                        <h2 class="mt-3 mb-0"><?php echo number_format($total_creators); ?></h2>
                        <p class="text-muted mb-0">Entrepreneurs</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <i class="bi bi-cash-stack text-warning" style="font-size: 3rem;"></i>
                        <h2 class="mt-3 mb-0"><?php echo number_format($total_investors); ?></h2>
                        <p class="text-muted mb-0">Active Investors</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- How It Works Section -->
<div class="container py-5">
    <div class="row mb-5">
        <div class="col text-center">
            <h2 class="display-5 fw-bold mb-3">How It Works</h2>
            <p class="lead text-muted">Simple steps to connect ideas with investors</p>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-3 mb-4">
            <div class="text-center">
                <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center mb-3" 
                     style="width: 80px; height: 80px; font-size: 2rem;">
                    <i class="bi bi-person-plus"></i>
                </div>
                <h4>1. Sign Up</h4>
                <p class="text-muted">Create an account as an entrepreneur or investor</p>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="text-center">
                <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center mb-3" 
                     style="width: 80px; height: 80px; font-size: 2rem;">
                    <i class="bi bi-lightbulb"></i>
                </div>
                <h4>2. Post/Browse</h4>
                <p class="text-muted">Share your idea or discover innovative projects</p>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="text-center">
                <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center mb-3" 
                     style="width: 80px; height: 80px; font-size: 2rem;">
                    <i class="bi bi-chat-dots"></i>
                </div>
                <h4>3. Connect</h4>
                <p class="text-muted">Start conversations and discuss opportunities</p>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="text-center">
                <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center mb-3" 
                     style="width: 80px; height: 80px; font-size: 2rem;">
                    <i class="bi bi-rocket-takeoff"></i>
                </div>
                <h4>4. Launch</h4>
                <p class="text-muted">Turn ideas into successful businesses</p>
            </div>
        </div>
    </div>
</div>

<!-- CTA Section -->
<div class="bg-primary text-white pt-5">
    <div class="container text-center pb-5">
        <h2 class="display-5 fw-bold mb-3">Ready to Get Started?</h2>
        <p class="lead mb-4">Join our community of entrepreneurs and investors today</p>
        <?php if (!isLoggedIn()): ?>
            <a href="<?php echo APP_URL; ?>/pages/signup.php" class="btn btn-light btn-lg px-5">
                Sign Up Now
            </a>
        <?php else: ?>
            <a href="<?php echo APP_URL; ?>/pages/ideas.php" class="btn btn-light btn-lg px-5">
                Explore Ideas
            </a>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
