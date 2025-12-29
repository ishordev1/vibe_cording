<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

$pageTitle = 'Profile - ' . APP_NAME;
require_once __DIR__ . '/../includes/header.php';

// Require login
requireLogin();

$conn = getDBConnection();
$user_id = getCurrentUserId();

// Get user data
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
?>

<div class="container mt-4 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-body p-4">
                    <h2 class="mb-4">My Profile</h2>
                    
                    <form id="profileForm" method="POST" action="<?php echo APP_URL; ?>/actions/update-profile.php" enctype="multipart/form-data">
                        <div class="text-center mb-4">
                            <div class="mb-3">
                                <?php if (!empty($user['profile_image']) && $user['profile_image'] !== 'default-avatar.png' && file_exists(UPLOAD_DIR . $user['profile_image'])): ?>
                                    <img src="<?php echo APP_URL; ?>/uploads/<?php echo htmlspecialchars($user['profile_image']); ?>" 
                                         alt="Profile Picture" 
                                         class="rounded-circle" 
                                         style="width: 150px; height: 150px; object-fit: cover; border: 3px solid #0d6efd;">
                                <?php else: ?>
                                    <i class="bi bi-person-circle" style="font-size: 6rem; color: #6c757d;"></i>
                                <?php endif; ?>
                            </div>
                            <input type="file" class="form-control" id="profile_image" name="profile_image" accept="image/*">
                            <small class="form-text text-muted">Upload new profile picture (JPG, PNG, GIF - Max 5MB)</small>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="full_name" class="form-label">Full Name</label>
                                <input type="text" class="form-control" id="full_name" name="full_name" 
                                       value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username" 
                                       value="<?php echo htmlspecialchars($user['username']); ?>" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?php echo htmlspecialchars($user['email']); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="bio" class="form-label">Bio</label>
                            <textarea class="form-control" id="bio" name="bio" rows="4"><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">User Type</label>
                            <input type="text" class="form-control" value="<?php echo ucfirst($user['user_type']); ?>" disabled>
                            <small class="form-text text-muted">User type cannot be changed</small>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Member Since</label>
                            <input type="text" class="form-control" value="<?php echo formatDate($user['created_at']); ?>" disabled>
                        </div>
                        
                        <hr class="my-4">
                        
                        <h4 class="mb-3">Change Password</h4>
                        
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Current Password</label>
                            <input type="password" class="form-control" id="current_password" name="current_password">
                            <small class="form-text text-muted">Leave blank if you don't want to change password</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="new_password" class="form-label">New Password</label>
                            <input type="password" class="form-control" id="new_password" name="new_password" minlength="6">
                        </div>
                        
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Confirm New Password</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-check-circle"></i> Update Profile
                            </button>
                            <a href="<?php echo APP_URL; ?>/index.php" class="btn btn-outline-secondary">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Validate password confirmation
document.getElementById('profileForm').addEventListener('submit', function(e) {
    const currentPassword = document.getElementById('current_password').value;
    const newPassword = document.getElementById('new_password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    
    if (newPassword || currentPassword) {
        if (!currentPassword) {
            e.preventDefault();
            alert('Please enter your current password to change it');
            return;
        }
        
        if (newPassword !== confirmPassword) {
            e.preventDefault();
            alert('New passwords do not match!');
            return;
        }
        
        if (newPassword.length < 6) {
            e.preventDefault();
            alert('New password must be at least 6 characters long');
            return;
        }
    }
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
