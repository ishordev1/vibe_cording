<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';
require_login();
$u = current_user();
if ($u['role'] !== 'author') { header('Location: login.php'); exit; }

require_once __DIR__ . '/includes/db.php';
$stmt = $pdo->prepare('SELECT * FROM papers WHERE created_by=? ORDER BY created_at DESC');
$stmt->execute([$u['id']]);
$papers = $stmt->fetchAll();
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>My Papers | Research Journal</title>
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
      <h2><i class="fas fa-folder text-primary me-2"></i>My Papers</h2>
      <p class="text-secondary mb-0">Manage all your submissions</p>
    </div>

    <div class="row g-4">
      <?php if (empty($papers)): ?>
        <div class="col-12">
          <div class="text-center py-5">
            <i class="fas fa-inbox fa-4x text-secondary mb-3"></i>
            <h4>No papers submitted yet</h4>
            <p class="text-secondary">Get started by submitting your first research paper</p>
            <a href="submit_paper.php" class="btn btn-primary btn-lg">
              <i class="fas fa-file-upload me-2"></i>Submit Paper
            </a>
          </div>
        </div>
      <?php else: ?>
        <?php foreach ($papers as $p): ?>
          <div class="col-md-6">
            <div class="paper-card">
              <div class="d-flex justify-content-between align-items-start mb-3">
                <?php
                $statusClass = 'status-pending';
                if ($p['status'] === 'Pending') $statusClass = 'status-pending';
                elseif ($p['status'] === 'Under Review') $statusClass = 'status-review';
                elseif ($p['status'] === 'Approved') $statusClass = 'status-approved';
                elseif ($p['status'] === 'Rejected') $statusClass = 'status-rejected';
                ?>
                <span class="status-badge <?= $statusClass ?>"><?= esc($p['status']) ?></span>
                <small class="text-muted"><i class="far fa-calendar me-1"></i><?= date('M d, Y', strtotime($p['created_at'])) ?></small>
              </div>
              <h5 class="paper-title"><?= esc($p['title']) ?></h5>
              <p class="paper-meta mb-2">
                <i class="fas fa-users text-primary me-1"></i>
                <?= esc($p['author_list']) ?>
              </p>
              <p class="text-secondary small mb-3">
                <?= esc(substr($p['abstract'], 0, 120)) ?>...
              </p>
              <div class="d-flex gap-2">
                <a href="paper_view.php?uuid=<?= $p['uuid'] ?>" class="btn btn-primary btn-sm flex-grow-1">
                  <i class="fas fa-eye me-1"></i>View Details
                </a>
                <?php if ($p['status'] === 'Approved' && $p['published_pdf']): ?>
                  <a href="certificate.php?uuid=<?= $p['uuid'] ?>" class="btn btn-success btn-sm">
                    <i class="fas fa-certificate me-1"></i>Certificate
                  </a>
                <?php endif; ?>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/main.js"></script>
</body>
</html>