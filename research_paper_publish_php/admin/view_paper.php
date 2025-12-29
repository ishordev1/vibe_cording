<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_login();
$u = current_user();
if ($u['role'] !== 'admin') { header('Location: ../login.php'); exit; }

$uuid = $_GET['uuid'] ?? '';
if (!$uuid || !is_valid_uuid($uuid)) { echo 'Invalid UUID'; exit; }
$stmt = $pdo->prepare('SELECT p.*, u.name as submitter, u.email FROM papers p JOIN users u ON p.created_by=u.id WHERE p.uuid=?');
$stmt->execute([$uuid]);
$p = $stmt->fetch();
if (!$p) { echo 'Not found'; exit; }

// Fetch certificates for this paper
$cert_stmt = $pdo->prepare('SELECT * FROM certificates WHERE paper_uuid=? ORDER BY id');
$cert_stmt->execute([$uuid]);
$certificates = $cert_stmt->fetchAll();

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
<title>View Paper - Admin Panel</title>
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
          <h2><i class="fas fa-file-alt text-danger me-2"></i><?= esc($p['title']) ?></h2>
          <p class="text-secondary mb-0">Paper ID: #<?= $p['id'] ?></p>
        </div>
        <span class="status-badge <?= $statusClass ?>"><?= esc($p['status']) ?></span>
      </div>
    </div>

    <div class="row">
      <div class="col-lg-8">
        <!-- Paper Abstract -->
        <div class="card mb-4">
          <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-align-left me-2"></i>Abstract</h5>
          </div>
          <div class="card-body">
            <p><?= nl2br(esc($p['abstract'])) ?></p>
          </div>
        </div>

        <!-- Paper PDF -->
        <div class="card mb-4">
          <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-file-pdf text-danger me-2"></i>Research Paper</h5>
          </div>
          <div class="card-body">
            <iframe src="../uploads/papers/<?= esc($p['paper_pdf']) ?>" style="width:100%;height:600px;border:1px solid #dee2e6;border-radius:4px;"></iframe>
            <div class="mt-2">
              <a href="../uploads/papers/<?= esc($p['paper_pdf']) ?>" class="btn btn-outline-primary" download>
                <i class="fas fa-download me-2"></i>Download Paper
              </a>
            </div>
          </div>
        </div>

        <!-- Copyright PDF -->
        <div class="card mb-4">
          <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-file-signature text-warning me-2"></i>Copyright Form</h5>
          </div>
          <div class="card-body">
            <iframe src="../uploads/copyrights/<?= esc($p['copyright_pdf']) ?>" style="width:100%;height:600px;border:1px solid #dee2e6;border-radius:4px;"></iframe>
            <div class="mt-2">
              <a href="../uploads/copyrights/<?= esc($p['copyright_pdf']) ?>" class="btn btn-outline-primary" download>
                <i class="fas fa-download me-2"></i>Download Copyright
              </a>
            </div>
          </div>
        </div>

        <?php if ($p['published_pdf']): ?>
          <!-- Published PDF -->
          <div class="card mb-4">
            <div class="card-header bg-success text-white">
              <h5 class="mb-0"><i class="fas fa-check-circle me-2"></i>Published Version</h5>
            </div>
            <div class="card-body">
              <iframe src="../uploads/published/<?= esc($p['published_pdf']) ?>" style="width:100%;height:600px;border:1px solid #dee2e6;border-radius:4px;"></iframe>
              <div class="mt-2">
                <a href="../uploads/published/<?= esc($p['published_pdf']) ?>" class="btn btn-success" download>
                  <i class="fas fa-download me-2"></i>Download Published PDF
                </a>
              </div>
            </div>
          </div>
        <?php endif; ?>
      </div>

      <div class="col-lg-4">
        <!-- Paper Info -->
        <div class="card mb-4">
          <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Paper Information</h5>
          </div>
          <div class="card-body">
            <div class="mb-3">
              <strong class="d-block text-secondary small">Submitter</strong>
              <p class="mb-0"><?= esc($p['submitter']) ?></p>
              <small class="text-muted"><?= esc($p['email']) ?></small>
            </div>
            <div class="mb-3">
              <strong class="d-block text-secondary small">Authors</strong>
              <p class="mb-0"><?= esc($p['author_list']) ?></p>
            </div>
            <div class="mb-3">
              <strong class="d-block text-secondary small">Submission Date</strong>
              <p class="mb-0"><?= date('F d, Y', strtotime($p['created_at'])) ?></p>
            </div>
            <div class="mb-0">
              <strong class="d-block text-secondary small">Current Status</strong>
              <span class="status-badge <?= $statusClass ?>"><?= esc($p['status']) ?></span>
            </div>
          </div>
        </div>

        <!-- Admin Actions -->
        <div class="card mb-4 border-danger">
          <div class="card-header bg-danger text-white">
            <h5 class="mb-0"><i class="fas fa-cog me-2"></i>Admin Actions</h5>
          </div>
          <div class="card-body">
            <div class="d-grid gap-2">
              <a class="btn btn-warning" href="review.php?uuid=<?= $p['uuid'] ?>">
                <i class="fas fa-edit me-2"></i>Review / Change Status
              </a>
              <?php if ($p['status'] === 'Approved'): ?>
                <a class="btn btn-success" href="upload_final_pdf.php?uuid=<?= $p['uuid'] ?>">
                  <i class="fas fa-upload me-2"></i>Upload Published PDF
                </a>
                <a class="btn btn-info" href="generate_certificate.php?uuid=<?= $p['uuid'] ?>">
                  <i class="fas fa-award me-2"></i>Generate Certificates
                </a>
              <?php endif; ?>
              <a class="btn btn-outline-secondary" href="papers.php">
                <i class="fas fa-arrow-left me-2"></i>Back to Papers
              </a>
            </div>
          </div>
        </div>

        <!-- Certificates -->
        <?php if (!empty($certificates)): ?>
          <div class="card border-primary">
            <div class="card-header bg-primary text-white">
              <h5 class="mb-0"><i class="fas fa-certificate me-2"></i>Generated Certificates</h5>
            </div>
            <div class="card-body">
              <div class="list-group list-group-flush">
                <?php foreach ($certificates as $cert): ?>
                  <div class="list-group-item px-0">
                    <div class="d-flex justify-content-between align-items-start">
                      <div>
                        <i class="fas fa-user-graduate text-primary me-2"></i>
                        <strong><?= esc($cert['author_name']) ?></strong>
                        <?php if (!empty($cert['author_email'])): ?>
                          <br><small class="text-muted ms-4"><?= esc($cert['author_email']) ?></small>
                        <?php endif; ?>
                        <br><small class="badge bg-primary mt-1"><?= esc($cert['certificate_id']) ?></small>
                      </div>
                      <a href="../certificate.php?cert_id=<?= esc($cert['certificate_id']) ?>" class="btn btn-sm btn-primary" target="_blank">
                        <i class="fas fa-eye"></i>
                      </a>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
            </div>
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