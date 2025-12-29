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

// Stats
$total = count($papers);
$pending = count(array_filter($papers, fn($p) => $p['status'] === 'Pending'));
$approved = count(array_filter($papers, fn($p) => $p['status'] === 'Approved'));
$rejected = count(array_filter($papers, fn($p) => $p['status'] === 'Rejected'));
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Author Dashboard | Research Journal</title>
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
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <h2>Dashboard</h2>
          <p class="text-secondary mb-0">Welcome back, <?= esc($u['name']) ?>!</p>
        </div>
        <button class="btn btn-primary d-lg-none" id="sidebarToggle">
          <i class="fas fa-bars"></i>
        </button>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
      <div class="col-md-3">
        <div class="stat-card">
          <div class="d-flex align-items-center justify-content-between">
            <div>
              <div class="stat-label">Total Papers</div>
              <div class="stat-value"><?= $total ?></div>
            </div>
            <i class="fas fa-file-alt fa-3x text-primary opacity-25"></i>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="stat-card" style="border-left-color: #f59e0b;">
          <div class="d-flex align-items-center justify-content-between">
            <div>
              <div class="stat-label">Pending</div>
              <div class="stat-value" style="color: #f59e0b;"><?= $pending ?></div>
            </div>
            <i class="fas fa-clock fa-3x opacity-25" style="color: #f59e0b;"></i>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="stat-card" style="border-left-color: #10b981;">
          <div class="d-flex align-items-center justify-content-between">
            <div>
              <div class="stat-label">Approved</div>
              <div class="stat-value" style="color: #10b981;"><?= $approved ?></div>
            </div>
            <i class="fas fa-check-circle fa-3x opacity-25" style="color: #10b981;"></i>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="stat-card" style="border-left-color: #ef4444;">
          <div class="d-flex align-items-center justify-content-between">
            <div>
              <div class="stat-label">Rejected</div>
              <div class="stat-value" style="color: #ef4444;"><?= $rejected ?></div>
            </div>
            <i class="fas fa-times-circle fa-3x opacity-25" style="color: #ef4444;"></i>
          </div>
        </div>
      </div>
    </div>

    <!-- Quick Actions -->
    <div class="card mb-4">
      <div class="card-body">
        <h5 class="mb-3"><i class="fas fa-bolt text-warning me-2"></i>Quick Actions</h5>
        <div class="d-flex gap-3 flex-wrap">
          <a href="submit_paper.php" class="btn btn-primary">
            <i class="fas fa-file-upload me-2"></i>Submit New Paper
          </a>
          <a href="papers.php" class="btn btn-outline-primary">
            <i class="fas fa-search me-2"></i>Browse Published Papers
          </a>
        </div>
      </div>
    </div>

    <!-- Recent Papers -->
    <div class="card">
      <div class="card-header bg-white">
        <h5 class="mb-0"><i class="fas fa-folder-open text-primary me-2"></i>Recent Submissions</h5>
      </div>
      <div class="card-body">
        <?php if (empty($papers)): ?>
          <div class="text-center py-5">
            <i class="fas fa-inbox fa-4x text-secondary mb-3"></i>
            <h5>No submissions yet</h5>
            <p class="text-secondary">Start by submitting your first research paper</p>
            <a href="submit_paper.php" class="btn btn-primary">Submit Paper</a>
          </div>
        <?php else: ?>
          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>Title</th>
                  <th>Status</th>
                  <th>Submitted</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach (array_slice($papers, 0, 5) as $p): ?>
                  <tr>
                    <td>
                      <div class="fw-semibold"><?= esc($p['title']) ?></div>
                      <small class="text-muted"><?= esc(substr($p['author_list'], 0, 50)) ?>...</small>
                    </td>
                    <td>
                      <?php
                      $statusClass = 'status-pending';
                      if ($p['status'] === 'Pending') $statusClass = 'status-pending';
                      elseif ($p['status'] === 'Under Review') $statusClass = 'status-review';
                      elseif ($p['status'] === 'Approved') $statusClass = 'status-approved';
                      elseif ($p['status'] === 'Rejected') $statusClass = 'status-rejected';
                      ?>
                      <span class="status-badge <?= $statusClass ?>"><?= esc($p['status']) ?></span>
                    </td>
                    <td><small><?= date('M d, Y', strtotime($p['created_at'])) ?></small></td>
                    <td>
                      <a href="paper_view.php?uuid=<?= $p['uuid'] ?>" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-eye"></i>
                      </a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
          <?php if (count($papers) > 5): ?>
            <div class="text-center mt-3">
              <a href="my_papers.php" class="btn btn-outline-primary">View All Papers</a>
            </div>
          <?php endif; ?>
        <?php endif; ?>
      </div>
    </div>
  </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/main.js"></script>
</body>
</html>