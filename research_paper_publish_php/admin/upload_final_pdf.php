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

$err = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $v = validate_pdf_upload($_FILES['final_pdf']);
    if ($v !== true) $err = $v;
    else {
        $fname = save_uploaded_pdf($_FILES['final_pdf'], UPLOADS_PUBLISHED, 'published');
        if ($fname) {
            $upd = $pdo->prepare('UPDATE papers SET published_pdf=? WHERE uuid=?');
            $upd->execute([$fname, $uuid]);
            $success = 'Final PDF uploaded successfully!';
            header("Refresh: 2; url=view_paper.php?uuid=$uuid");
        } else $err = 'Upload failed';
    }
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Upload Final PDF - Admin Panel</title>
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
          <h2><i class="fas fa-upload text-success me-2"></i>Upload Final Published PDF</h2>
          <p class="text-secondary mb-0">Upload the formatted version for publication</p>
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

            <?php if ($p['published_pdf']): ?>
              <div class="alert alert-info d-flex align-items-center mb-4">
                <i class="fas fa-info-circle me-2"></i>
                <div>
                  <strong>Note:</strong> A published PDF already exists for this paper. 
                  Uploading a new file will replace the current version.
                </div>
              </div>
            <?php endif; ?>

            <form method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
              <div class="mb-4">
                <label class="form-label fw-semibold">
                  <i class="fas fa-file-pdf text-danger me-1"></i>Final Published PDF *
                </label>
                <input 
                  name="final_pdf" 
                  type="file" 
                  accept="application/pdf" 
                  class="form-control form-control-lg"
                  required
                >
                <div class="invalid-feedback">Please select a PDF file</div>
                <small class="text-muted">
                  Upload the professionally formatted PDF ready for publication (Max 20MB)
                </small>
              </div>

              <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Important:</strong> This PDF will be publicly visible to all users. 
                Ensure it is properly formatted and finalized.
              </div>

              <div class="d-flex gap-3">
                <button type="submit" class="btn btn-success btn-lg">
                  <i class="fas fa-cloud-upload-alt me-2"></i>Upload Published PDF
                </button>
                <a href="view_paper.php?id=<?= $id ?>" class="btn btn-outline-secondary btn-lg">
                  <i class="fas fa-times me-2"></i>Cancel
                </a>
              </div>
            </form>
          </div>
        </div>

        <?php if ($p['published_pdf']): ?>
          <div class="card mt-4 border-success">
            <div class="card-header bg-success text-white">
              <h6 class="mb-0"><i class="fas fa-eye me-2"></i>Current Published PDF</h6>
            </div>
            <div class="card-body">
              <iframe 
                src="../uploads/published/<?= esc($p['published_pdf']) ?>" 
                style="width:100%;height:600px;border:1px solid #dee2e6;border-radius:4px;"
              ></iframe>
              <div class="mt-3">
                <a href="../uploads/published/<?= esc($p['published_pdf']) ?>" class="btn btn-success" download>
                  <i class="fas fa-download me-2"></i>Download Current Version
                </a>
              </div>
            </div>
          </div>
        <?php endif; ?>
      </div>

      <div class="col-lg-4">
        <!-- Paper Info -->
        <div class="card mb-4 border-primary">
          <div class="card-header bg-primary text-white">
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
              <strong class="d-block text-secondary small">Status</strong>
              <p class="mb-0"><span class="badge bg-success"><?= esc($p['status']) ?></span></p>
            </div>
            <div class="mb-0">
              <strong class="d-block text-secondary small">Submitted On</strong>
              <p class="mb-0"><?= date('F d, Y', strtotime($p['created_at'])) ?></p>
            </div>
          </div>
        </div>

        <!-- Upload Guidelines -->
        <div class="card border-info">
          <div class="card-header bg-info text-white">
            <h6 class="mb-0"><i class="fas fa-lightbulb me-2"></i>Upload Guidelines</h6>
          </div>
          <div class="card-body small">
            <ul class="list-unstyled mb-0">
              <li class="mb-2">
                <i class="fas fa-check text-success me-2"></i>
                Ensure PDF is properly formatted
              </li>
              <li class="mb-2">
                <i class="fas fa-check text-success me-2"></i>
                Check for spelling and grammar
              </li>
              <li class="mb-2">
                <i class="fas fa-check text-success me-2"></i>
                Verify all figures and tables
              </li>
              <li class="mb-2">
                <i class="fas fa-check text-success me-2"></i>
                Maximum file size: 20MB
              </li>
              <li class="mb-0">
                <i class="fas fa-check text-success me-2"></i>
                Only PDF format accepted
              </li>
            </ul>
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