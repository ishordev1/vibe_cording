<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_login();
$u = current_user();
if ($u['role'] !== 'admin') { header('Location: ../login.php'); exit; }

$uuid = $_GET['uuid'] ?? '';
if (!$uuid || !is_valid_uuid($uuid)) { echo 'Invalid UUID'; exit; }
$stmt = $pdo->prepare('SELECT * FROM papers WHERE uuid=?');
$stmt->execute([$uuid]);
$p = $stmt->fetch();
if (!$p) { echo 'Not found'; exit; }

$msg = '';
$notes = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $st = $_POST['status'];
    $notes = trim($_POST['notes'] ?? '');
    $upd = $pdo->prepare('UPDATE papers SET status=?, review_notes=? WHERE uuid=?');
    $upd->execute([$st, $notes, $uuid]);
    $msg = 'Status updated!';
    header("Location: view_paper.php?uuid=$uuid"); exit;
}

$statusClass = 'status-pending';
if ($p['status'] === 'Pending') $statusClass = 'status-pending';
elseif ($p['status'] === 'Under Review') $statusClass = 'status-review';
elseif ($p['status'] === 'Approved') $statusClass = 'status-approved';
elseif ($p['status'] === 'Rejected') $statusClass = 'status-rejected';
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Review Paper - Admin Panel</title>
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
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <h2><i class="fas fa-pen text-warning me-2"></i>Review Paper</h2>
          <p class="text-secondary mb-0">Change the status of this submission</p>
        </div>
        <a href="view_paper.php?id=<?= $id ?>" class="btn btn-outline-secondary">
          <i class="fas fa-arrow-left me-2"></i>Back to Paper
        </a>
      </div>
    </div>

    <div class="row">
      <div class="col-lg-8">
        <div class="card shadow-sm">
          <div class="card-header bg-white">
            <h5 class="mb-0"><i class="fas fa-file-alt me-2"></i><?= esc($p['title']) ?></h5>
          </div>
          <div class="card-body">
            <?php if ($msg): ?>
              <div class="alert alert-success d-flex align-items-center">
                <i class="fas fa-check-circle me-2"></i>
                <div><?= esc($msg) ?></div>
              </div>
            <?php endif; ?>

            <form method="post" class="needs-validation" novalidate>
              <div class="mb-4">
                <label class="form-label fw-semibold">
                  <i class="fas fa-info-circle text-danger me-1"></i>Current Status
                </label>
                <div>
                  <span class="status-badge <?= $statusClass ?> fs-6"><?= esc($p['status']) ?></span>
                </div>
              </div>

              <div class="mb-4">
                <label class="form-label fw-semibold">
                  <i class="fas fa-exchange-alt text-warning me-1"></i>Change Status To *
                </label>
                <select name="status" class="form-select form-select-lg" required>
                  <option value="">-- Select New Status --</option>
                  <option value="Pending" <?= $p['status']==='Pending'?'selected':'' ?>>
                    📝 Pending
                  </option>
                  <option value="Under Review" <?= $p['status']==='Under Review'?'selected':'' ?>>
                    🔍 Under Review
                  </option>
                  <option value="Approved" <?= $p['status']==='Approved'?'selected':'' ?>>
                    ✅ Approved
                  </option>
                  <option value="Rejected" <?= $p['status']==='Rejected'?'selected':'' ?>>
                    ❌ Rejected
                  </option>
                </select>
                <div class="invalid-feedback">Please select a status</div>
              </div>

              <div class="mb-4">
                <label class="form-label fw-semibold">
                  <i class="fas fa-comment text-primary me-1"></i>Review Notes (Optional)
                </label>
                <textarea 
                  name="notes" 
                  class="form-control" 
                  rows="4" 
                  placeholder="Add any comments or feedback for the author..."
                ><?= esc($notes) ?></textarea>
                <small class="text-muted">These notes will be saved for internal reference</small>
              </div>

              <div class="d-flex gap-3">
                <button type="submit" class="btn btn-warning btn-lg">
                  <i class="fas fa-save me-2"></i>Update Status
                </button>
                <a href="view_paper.php?id=<?= $id ?>" class="btn btn-outline-secondary btn-lg">
                  <i class="fas fa-times me-2"></i>Cancel
                </a>
              </div>
            </form>
          </div>
        </div>
      </div>

      <div class="col-lg-4">
        <!-- Paper Info -->
        <div class="card mb-4 border-info">
          <div class="card-header bg-info text-white">
            <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Paper Information</h6>
          </div>
          <div class="card-body">
            <div class="mb-3">
              <strong class="d-block text-secondary small">Paper ID</strong>
              <p class="mb-0">#<?= $p['id'] ?></p>
            </div>
            <div class="mb-3">
              <strong class="d-block text-secondary small">Authors</strong>
              <p class="mb-0"><?= esc($p['author_list']) ?></p>
            </div>
            <div class="mb-3">
              <strong class="d-block text-secondary small">Submitted On</strong>
              <p class="mb-0"><?= date('F d, Y', strtotime($p['created_at'])) ?></p>
              <small class="text-muted"><?= date('h:i A', strtotime($p['created_at'])) ?></small>
            </div>
          </div>
        </div>

        <!-- Status Guide -->
        <div class="card border-warning">
          <div class="card-header bg-warning text-dark">
            <h6 class="mb-0"><i class="fas fa-lightbulb me-2"></i>Status Guide</h6>
          </div>
          <div class="card-body small">
            <div class="mb-3">
              <span class="status-badge status-pending me-2">Pending</span>
              <p class="mb-0 text-muted">Initial submission, awaiting review</p>
            </div>
            <div class="mb-3">
              <span class="status-badge status-review me-2">Under Review</span>
              <p class="mb-0 text-muted">Currently being evaluated</p>
            </div>
            <div class="mb-3">
              <span class="status-badge status-approved me-2">Approved</span>
              <p class="mb-0 text-muted">Accepted for publication</p>
            </div>
            <div class="mb-0">
              <span class="status-badge status-rejected me-2">Rejected</span>
              <p class="mb-0 text-muted">Not accepted for publication</p>
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