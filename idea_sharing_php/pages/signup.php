<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

$pageTitle = 'Sign Up - ' . APP_NAME;
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
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-body p-5">
                    <h2 class="text-center mb-4">Create Account</h2>
                    <p class="text-center text-muted mb-4">Join <?php echo APP_NAME; ?> today</p>
                    
                    <form id="signupForm" method="POST" action="<?php echo APP_URL; ?>/actions/signup-action.php" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="user_type" class="form-label">I am a...</label>
                            <select class="form-select" id="user_type" name="user_type" required>
                                <option value="">Select your role</option>
                                <option value="creator">Idea Creator</option>
                                <option value="investor">Investor</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="profile_image" class="form-label">Profile Picture (Optional)</label>
                            <input type="file" class="form-control" id="profile_image" name="profile_image" accept="image/*">
                            <small class="form-text text-muted">Upload a profile picture (JPG, PNG, GIF - Max 5MB)</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="full_name" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="full_name" name="full_name" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                            <small class="form-text text-muted">Choose a unique username</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required minlength="6">
                            <small class="form-text text-muted">Minimum 6 characters</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="bio" class="form-label">Bio (Optional)</label>
                            <textarea class="form-control" id="bio" name="bio" rows="3" placeholder="Tell us about yourself..."></textarea>
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="terms" name="terms" required>
                            <label class="form-check-label" for="terms">
                                I agree to the Terms and Conditions
                            </label>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100 mb-3">Sign Up</button>
                        
                        <p class="text-center mb-0">
                            Already have an account? 
                            <a href="<?php echo APP_URL; ?>/pages/signin.php">Sign In</a>
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Client-side password validation
document.getElementById('signupForm').addEventListener('submit', function(e) {
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    
    if (password !== confirmPassword) {
        e.preventDefault();
        alert('Passwords do not match!');
    }
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
