<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>404 - Page Not Found | IJSRET</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="assets/css/style.css">
<style>
.error-page {
  min-height: calc(100vh - 80px);
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #3b5998 0%, #2d4373 100%);
  color: white;
  text-align: center;
  padding: 4rem 2rem;
}
.error-code {
  font-size: 10rem;
  font-weight: 900;
  line-height: 1;
  text-shadow: 0 10px 30px rgba(0,0,0,0.3);
  animation: float 3s ease-in-out infinite;
}
@keyframes float {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-20px); }
}
@media (max-width: 768px) {
  .error-code { font-size: 6rem; }
}
</style>
</head>
<body>
<?php include 'includes/public_navbar.php'; ?>
<div class="error-page">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-8">
        <i class="fas fa-exclamation-triangle fa-5x mb-4 opacity-75"></i>
        <h1 class="error-code">404</h1>
        <h2 class="display-5 fw-bold mb-3">Page Not Found</h2>
        <p class="lead mb-4">Oops! The page you're looking for doesn't exist or has been moved.</p>
        <div class="d-flex gap-3 justify-content-center flex-wrap">
          <a href="index.php" class="btn btn-light btn-lg px-5">
            <i class="fas fa-home me-2"></i>Go Home
          </a>
          <a href="papers.php" class="btn btn-outline-light btn-lg px-5">
            <i class="fas fa-search me-2"></i>Browse Papers
          </a>
        </div>
        <div class="mt-5">
          <p class="opacity-75">Common pages:</p>
          <div class="d-flex gap-3 justify-content-center flex-wrap">
            <a href="about.php" class="text-white text-decoration-none">About</a>
            <span class="opacity-50">•</span>
            <a href="submit_paper.php" class="text-white text-decoration-none">Submit Paper</a>
            <span class="opacity-50">•</span>
            <a href="contact.php" class="text-white text-decoration-none">Contact</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php include 'includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>