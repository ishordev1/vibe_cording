<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';
require_login();
$u = current_user();
if ($u['role'] !== 'author') { header('Location: login.php'); exit; }
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Profile | Research Journal</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="dashboard-wrapper">
  <!-- Sidebar -->
  <?php include 'includes/author_sidebar.php'; ?>
  
  <!-- Top Navbar -->
  <?php include 'includes/author_navbar.php'; ?>

  <!-- Main Content -->
  <main class="main-content" style="padding-top: 80px;">
    <div class="dashboard-header">
      <h2><i class="fas fa-user-circle text-primary me-2"></i>Profile</h2>
      <p class="text-secondary mb-0">Manage your account information</p>
    </div>

    <div class="row">
      <div class="col-md-8">
        <div class="card">
          <div class="card-header bg-white">
            <h5 class="mb-0">Account Information</h5>
          </div>
          <div class="card-body">
            <div class="mb-4">
              <label class="form-label fw-semibold">Full Name</label>
              <input type="text" class="form-control" value="<?= esc($u['name']) ?>" readonly>
            </div>
            <div class="mb-4">
              <label class="form-label fw-semibold">Email Address</label>
              <input type="email" class="form-control" value="<?= esc($u['email']) ?>" readonly>
            </div>
            <div class="mb-4">
              <label class="form-label fw-semibold">Role</label>
              <input type="text" class="form-control" value="<?= ucfirst($u['role']) ?>" readonly>
            </div>
            <div class="alert alert-info">
              <i class="fas fa-info-circle me-2"></i>
              To update your profile information, please contact the administrator.
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card">
          <div class="card-body text-center">
            <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 100px; height: 100px;">
              <i class="fas fa-user fa-3x text-white"></i>
            </div>
            <h5><?= esc($u['name']) ?></h5>
            <p class="text-muted"><?= esc($u['email']) ?></p>
            <span class="badge bg-primary">Author</span>
          </div>
        </div>
      </div>
    </div>
  </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/main.js"></script>
</body>
</html>