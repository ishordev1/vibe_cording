<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $pass = $_POST['password'];
    
    // Email validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $err = 'Please enter a valid email address';
    } elseif (login_user($email,$pass)) {
        // Redirect based on role
        $u = current_user();
        if ($u['role'] === 'admin') header('Location: admin/dashboard.php');
        else header('Location: dashboard.php');
        exit;
    } else {
        $err = 'Invalid email or password';
    }
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Login | Research Journal</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<?php include 'includes/public_navbar.php'; ?>

<div class="auth-wrapper" style="min-height: calc(100vh - 400px);">
  <div class="auth-card">
    <div class="text-center mb-4">
      <i class="fas fa-graduation-cap fa-3x text-primary mb-3"></i>
      <h3>Welcome Back!</h3>
      <p class="subtitle">Sign in to continue to your account</p>
    </div>
    
    <?php if ($err): ?>
      <div class="alert alert-danger d-flex align-items-center">
        <i class="fas fa-exclamation-circle me-2"></i>
        <div><?= esc($err) ?></div>
      </div>
    <?php endif; ?>
    
    <form method="post" class="needs-validation" novalidate>
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
      
      <div class="mb-4">
        <label class="form-label fw-semibold">
          <i class="fas fa-lock me-1"></i>Password
        </label>
        <input 
          name="password" 
          type="password" 
          class="form-control" 
          placeholder="Enter your password"
          required
        >
        <div class="invalid-feedback">Password is required</div>
      </div>
      
      <button class="btn btn-primary w-100 mb-3" type="submit">
        <i class="fas fa-sign-in-alt me-2"></i>Sign In
      </button>
      
      <div class="text-center">
        <p class="text-secondary mb-0">
          Don't have an account? 
          <a href="register.php" class="text-primary fw-semibold text-decoration-none">Register here</a>
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
</body>
</html>