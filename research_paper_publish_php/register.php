<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

$err = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    if (!$name || !$email || !$password) {
        $err = 'All fields are required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $err = 'Please enter a valid email address';
    } elseif (strlen($password) < 6) {
        $err = 'Password must be at least 6 characters long';
    } elseif ($password !== $confirm_password) {
        $err = 'Passwords do not match';
    } else {
        $r = register_user($name,$email,$password);
        if ($r) { 
            $success = 'Registration successful! Redirecting to login...';
            header('Refresh: 2; url=login.php');
        } else {
            $err = 'Registration failed. Email may already be registered.';
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Register | Research Journal</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<?php include 'includes/public_navbar.php'; ?>

<div class="auth-wrapper" style="min-height: calc(100vh - 400px);">
  <div class="auth-card">
    <div class="text-center mb-4">
      <i class="fas fa-user-plus fa-3x text-primary mb-3"></i>
      <h3>Create Account</h3>
      <p class="subtitle">Join our research community today</p>
    </div>
    
    <?php if ($err): ?>
      <div class="alert alert-danger d-flex align-items-center">
        <i class="fas fa-exclamation-circle me-2"></i>
        <div><?= esc($err) ?></div>
      </div>
    <?php endif; ?>
    
    <?php if ($success): ?>
      <div class="alert alert-success d-flex align-items-center">
        <i class="fas fa-check-circle me-2"></i>
        <div><?= esc($success) ?></div>
      </div>
    <?php endif; ?>
    
    <form method="post" class="needs-validation" novalidate>
      <div class="mb-3">
        <label class="form-label fw-semibold">
          <i class="fas fa-user me-1"></i>Full Name
        </label>
        <input 
          name="name" 
          type="text" 
          class="form-control" 
          placeholder="John Doe"
          required
          value="<?= isset($_POST['name']) ? esc($_POST['name']) : '' ?>"
        >
        <div class="invalid-feedback">Name is required</div>
      </div>
      
      <div class="mb-3">
        <label class="form-label fw-semibold">
          <i class="fas fa-envelope me-1"></i>Email Address
        </label>
        <input 
          name="email" 
          type="email" 
          class="form-control" 
          placeholder="name@example.com"
          required
          value="<?= isset($_POST['email']) ? esc($_POST['email']) : '' ?>"
        >
        <div class="invalid-feedback">Please enter a valid email address</div>
      </div>
      
      <div class="mb-3">
        <label class="form-label fw-semibold">
          <i class="fas fa-lock me-1"></i>Password
        </label>
        <input 
          name="password" 
          type="password" 
          class="form-control" 
          placeholder="Minimum 6 characters"
          required
          minlength="6"
        >
        <div class="invalid-feedback">Password must be at least 6 characters</div>
      </div>
      
      <div class="mb-4">
        <label class="form-label fw-semibold">
          <i class="fas fa-lock me-1"></i>Confirm Password
        </label>
        <input 
          name="confirm_password" 
          type="password" 
          class="form-control" 
          placeholder="Re-enter your password"
          required
        >
        <div class="invalid-feedback">Please confirm your password</div>
      </div>
      
      <button class="btn btn-primary w-100 mb-3" type="submit">
        <i class="fas fa-user-plus me-2"></i>Create Account
      </button>
      
      <div class="text-center">
        <p class="text-secondary mb-0">
          Already have an account? 
          <a href="login.php" class="text-primary fw-semibold text-decoration-none">Sign in here</a>
        </p>
      </div>
    </form>
    
    <hr class="my-4">
    
    <div class="text-center">
      <a href="index.php" class="btn btn-outline-secondary">
        <i class="fas fa-home me-2"></i>Back to Home
      </a>
    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/main.js"></script>
<script>
// Password match validation
document.querySelector('form').addEventListener('submit', function(e) {
    const pass = document.querySelector('input[name="password"]').value;
    const confirm = document.querySelector('input[name="confirm_password"]').value;
    if (pass !== confirm) {
        e.preventDefault();
        alert('Passwords do not match!');
    }
});
</script>
</body>
</html>