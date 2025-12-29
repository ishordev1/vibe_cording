<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_login();
$u = current_user();
if ($u['role'] !== 'admin') { header('Location: ../login.php'); exit; }

$q = $_GET['q'] ?? '';
if ($q) {
    $stmt = $pdo->prepare("SELECT p.*, u.name as submitter FROM papers p JOIN users u ON p.created_by=u.id WHERE p.title LIKE ? ORDER BY p.created_at DESC");
    $stmt->execute(["%$q%"]);
} else {
    $stmt = $pdo->query('SELECT p.*, u.name as submitter FROM papers p JOIN users u ON p.created_by=u.id ORDER BY p.created_at DESC');
}
$papers = $stmt->fetchAll();
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Manage Papers - Admin Panel</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="dashboard-wrapper">
  <!-- Admin Sidebar -->
  <?php include 'includes/admin_sidebar.php'; ?>
  
  <!-- Top Navbar -->
  <?php include 'includes/admin_navbar.php'; ?>
  
  <!-- Main Content -->
  <main class="main-content" style="padding-top: 80px;">
    <div class="dashboard-header">
      <h2><i class="fas fa-file-alt text-danger me-2"></i>Manage Papers</h2>
      <p class="text-secondary mb-0">Review and manage all submitted research papers</p>
    </div>

    <div class="card">
      <div class="card-body">
        <form class="mb-4">
          <div class="input-group">
            <span class="input-group-text"><i class="fas fa-search"></i></span>
            <input name="q" class="form-control" placeholder="Search by paper title..." value="<?= esc($q) ?>">
            <button class="btn btn-danger" type="submit">
              <i class="fas fa-search me-2"></i>Search
            </button>
            <?php if ($q): ?>
              <a href="papers.php" class="btn btn-outline-secondary">
                <i class="fas fa-times"></i>
              </a>
            <?php endif; ?>
          </div>
        </form>

        <?php if (empty($papers)): ?>
          <div class="alert alert-info text-center">
            <i class="fas fa-info-circle me-2"></i>
            <?= $q ? 'No papers found matching your search.' : 'No papers submitted yet.' ?>
          </div>
        <?php else: ?>
          <div class="table-responsive">
            <table class="table table-hover">
              <thead class="table-light">
                <tr>
                  <th><i class="fas fa-hashtag me-1"></i>ID</th>
                  <th><i class="fas fa-file-alt me-1"></i>Title</th>
                  <th><i class="fas fa-user me-1"></i>Submitter</th>
                  <th><i class="fas fa-info-circle me-1"></i>Status</th>
                  <th><i class="fas fa-calendar me-1"></i>Submitted</th>
                  <th class="text-center"><i class="fas fa-cog me-1"></i>Actions</th>
                </tr>
              </thead>
              <tbody>
              <?php foreach ($papers as $p): 
                $statusClass = 'status-pending';
                if ($p['status'] === 'Pending') $statusClass = 'status-pending';
                elseif ($p['status'] === 'Under Review') $statusClass = 'status-review';
                elseif ($p['status'] === 'Approved') $statusClass = 'status-approved';
                elseif ($p['status'] === 'Rejected') $statusClass = 'status-rejected';
              ?>
                <tr>
                  <td><strong>#<?= $p['id'] ?></strong></td>
                  <td>
                    <div class="fw-semibold"><?= esc($p['title']) ?></div>
                    <small class="text-muted"><?= substr(esc($p['abstract']), 0, 80) ?>...</small>
                  </td>
                  <td><?= esc($p['submitter']) ?></td>
                  <td><span class="status-badge <?= $statusClass ?>"><?= esc($p['status']) ?></span></td>
                  <td><small><?= date('M d, Y', strtotime($p['created_at'])) ?></small></td>
                  <td class="text-center">
                    <a class="btn btn-sm btn-primary" href="view_paper.php?uuid=<?= $p['uuid'] ?>">
                      <i class="fas fa-eye me-1"></i>View
                    </a>
                  </td>
                </tr>
              <?php endforeach; ?>
              </tbody>
            </table>
          </div>
          
          <div class="mt-3 text-muted small">
            <i class="fas fa-info-circle me-1"></i>
            Showing <?= count($papers) ?> paper<?= count($papers) !== 1 ? 's' : '' ?>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/main.js"></script>
</body>
</html>