<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/db.php';
require_login();
$u = current_user();
if ($u['role'] !== 'admin') { header('Location: ../login.php'); exit; }

// Basic stats
$tot = $pdo->query('SELECT COUNT(*) FROM papers')->fetchColumn();
$pending = $pdo->query("SELECT COUNT(*) FROM papers WHERE status='Pending'")->fetchColumn();
$review = $pdo->query("SELECT COUNT(*) FROM papers WHERE status='Under Review'")->fetchColumn();
$approved = $pdo->query("SELECT COUNT(*) FROM papers WHERE status='Approved'")->fetchColumn();
$rejected = $pdo->query("SELECT COUNT(*) FROM papers WHERE status='Rejected'")->fetchColumn();
$total_authors = $pdo->query("SELECT COUNT(*) FROM users WHERE role='author'")->fetchColumn();

// Recent papers
$recent = $pdo->query("SELECT p.*, u.name as author_name FROM papers p JOIN users u ON p.created_by=u.id ORDER BY p.created_at DESC LIMIT 5")->fetchAll();
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Admin Dashboard | Research Journal</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="dashboard-wrapper">
  <!-- Sidebar -->
  <?php include 'includes/admin_sidebar.php'; ?>
  
  <!-- Top Navbar -->
  <?php include 'includes/admin_navbar.php'; ?>

  <!-- Main Content -->
  <main class="main-content" style="padding-top: 80px;">
    <div class="dashboard-header">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <h2>Admin Dashboard</h2>
          <p class="text-secondary mb-0">Overview of submissions and statistics</p>
        </div>
        <button class="btn btn-primary d-lg-none" id="sidebarToggle">
          <i class="fas fa-bars"></i>
        </button>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
      <div class="col-md-4 col-lg-2">
        <div class="stat-card">
          <div class="stat-label">Total Papers</div>
          <div class="stat-value"><?= $tot ?></div>
          <i class="fas fa-file-alt fa-2x text-primary opacity-25 float-end"></i>
        </div>
      </div>
      <div class="col-md-4 col-lg-2">
        <div class="stat-card" style="border-left-color: #f59e0b;">
          <div class="stat-label">Pending</div>
          <div class="stat-value" style="color: #f59e0b;"><?= $pending ?></div>
          <i class="fas fa-clock fa-2x opacity-25 float-end" style="color: #f59e0b;"></i>
        </div>
      </div>
      <div class="col-md-4 col-lg-2">
        <div class="stat-card" style="border-left-color: #06b6d4;">
          <div class="stat-label">Under Review</div>
          <div class="stat-value" style="color: #06b6d4;"><?= $review ?></div>
          <i class="fas fa-eye fa-2x opacity-25 float-end" style="color: #06b6d4;"></i>
        </div>
      </div>
      <div class="col-md-4 col-lg-2">
        <div class="stat-card" style="border-left-color: #10b981;">
          <div class="stat-label">Approved</div>
          <div class="stat-value" style="color: #10b981;"><?= $approved ?></div>
          <i class="fas fa-check-circle fa-2x opacity-25 float-end" style="color: #10b981;"></i>
        </div>
      </div>
      <div class="col-md-4 col-lg-2">
        <div class="stat-card" style="border-left-color: #ef4444;">
          <div class="stat-label">Rejected</div>
          <div class="stat-value" style="color: #ef4444;"><?= $rejected ?></div>
          <i class="fas fa-times-circle fa-2x opacity-25 float-end" style="color: #ef4444;"></i>
        </div>
      </div>
      <div class="col-md-4 col-lg-2">
        <div class="stat-card" style="border-left-color: #8b5cf6;">
          <div class="stat-label">Authors</div>
          <div class="stat-value" style="color: #8b5cf6;"><?= $total_authors ?></div>
          <i class="fas fa-users fa-2x opacity-25 float-end" style="color: #8b5cf6;"></i>
        </div>
      </div>
    </div>

    <div class="row g-4">
      <!-- Recent Submissions -->
      <div class="col-lg-8">
        <div class="card">
          <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-clock text-primary me-2"></i>Recent Submissions</h5>
            <a href="papers.php" class="btn btn-sm btn-primary">View All</a>
          </div>
          <div class="card-body p-0">
            <?php if (empty($recent)): ?>
              <div class="text-center py-5">
                <i class="fas fa-inbox fa-3x text-secondary mb-3"></i>
                <p class="text-secondary">No submissions yet</p>
              </div>
            <?php else: ?>
              <div class="table-responsive">
                <table class="table table-hover mb-0">
                  <thead style="background: #f8fafc;">
                    <tr>
                      <th>Title</th>
                      <th>Author</th>
                      <th>Status</th>
                      <th>Date</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($recent as $p): ?>
                      <tr>
                        <td>
                          <div class="fw-semibold"><?= esc(substr($p['title'], 0, 40)) ?>...</div>
                        </td>
                        <td><small><?= esc($p['author_name']) ?></small></td>
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
                        <td><small><?= date('M d', strtotime($p['created_at'])) ?></small></td>
                        <td>
                          <a href="view_paper.php?uuid=<?= $p['uuid'] ?>" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-eye"></i>
                          </a>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="col-lg-4">
        <div class="card mb-3">
          <div class="card-header bg-white">
            <h6 class="mb-0"><i class="fas fa-bolt text-warning me-2"></i>Quick Actions</h6>
          </div>
          <div class="card-body">
            <div class="d-grid gap-2">
              <a href="papers.php" class="btn btn-primary">
                <i class="fas fa-file-alt me-2"></i>View All Papers
              </a>
              <a href="papers.php?status=Pending" class="btn btn-outline-warning">
                <i class="fas fa-clock me-2"></i>Pending Reviews
              </a>
              <a href="../index.php" class="btn btn-outline-secondary">
                <i class="fas fa-globe me-2"></i>Visit Site
              </a>
            </div>
          </div>
        </div>

        <div class="card">
          <div class="card-header bg-white">
            <h6 class="mb-0"><i class="fas fa-chart-pie text-info me-2"></i>Status Distribution</h6>
          </div>
          <div class="card-body">
            <div class="mb-3">
              <div class="d-flex justify-content-between mb-1">
                <small>Pending</small>
                <small class="fw-bold"><?= $tot > 0 ? round(($pending/$tot)*100) : 0 ?>%</small>
              </div>
              <div class="progress" style="height: 8px;">
                <div class="progress-bar bg-warning" style="width: <?= $tot > 0 ? ($pending/$tot)*100 : 0 ?>%"></div>
              </div>
            </div>
            <div class="mb-3">
              <div class="d-flex justify-content-between mb-1">
                <small>Approved</small>
                <small class="fw-bold"><?= $tot > 0 ? round(($approved/$tot)*100) : 0 ?>%</small>
              </div>
              <div class="progress" style="height: 8px;">
                <div class="progress-bar bg-success" style="width: <?= $tot > 0 ? ($approved/$tot)*100 : 0 ?>%"></div>
              </div>
            </div>
            <div>
              <div class="d-flex justify-content-between mb-1">
                <small>Rejected</small>
                <small class="fw-bold"><?= $tot > 0 ? round(($rejected/$tot)*100) : 0 ?>%</small>
              </div>
              <div class="progress" style="height: 8px;">
                <div class="progress-bar bg-danger" style="width: <?= $tot > 0 ? ($rejected/$tot)*100 : 0 ?>%"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/main.js"></script>
</body>
</html>