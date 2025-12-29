<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

$pageTitle = '404 - Page Not Found - ' . APP_NAME;
require_once __DIR__ . '/../includes/header.php';
?>

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-6 text-center">
            <div class="error-page py-5">
                <h1 class="display-1 fw-bold text-primary">404</h1>
                <h2 class="mb-4">Page Not Found</h2>
                <p class="lead text-muted mb-4">
                    Oops! The page you're looking for doesn't exist or has been moved.
                </p>
                <div class="mb-4">
                    <i class="bi bi-compass" style="font-size: 5rem; color: #ddd;"></i>
                </div>
                <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
                    <a href="<?php echo APP_URL; ?>/index.php" class="btn btn-primary btn-lg px-4">
                        <i class="bi bi-house"></i> Go Home
                    </a>
                    <a href="<?php echo APP_URL; ?>/pages/ideas.php" class="btn btn-outline-secondary btn-lg px-4">
                        <i class="bi bi-lightbulb"></i> Browse Ideas
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
