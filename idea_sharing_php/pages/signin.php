<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

$pageTitle = 'Sign In - ' . APP_NAME;
require_once __DIR__ . '/../includes/header.php';

// Redirect if already logged in
if (isLoggedIn()) {
    $dashboardUrl = getCurrentUserType() === USER_TYPE_CREATOR 
        ? '/pages/creator/dashboard.php' 
        : '/pages/investor/dashboard.php';
    redirect($dashboardUrl);
}
?>

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow">
                <div class="card-body p-5">
                    <h2 class="text-center mb-4">Sign In</h2>
                    <p class="text-center text-muted mb-4">Welcome back to <?php echo APP_NAME; ?></p>
                    
                    <form id="signinForm" method="POST" action="<?php echo APP_URL; ?>/actions/signin-action.php">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember">
                            <label class="form-check-label" for="remember">Remember me</label>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100 mb-3">Sign In</button>
                        
                        <p class="text-center mb-0">
                            Don't have an account? 
                            <a href="<?php echo APP_URL; ?>/pages/signup.php">Sign Up</a>
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
