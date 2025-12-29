<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/db.php';

$uuid = $_GET['uuid'] ?? '';
if (!$uuid || !is_valid_uuid($uuid)) { header('Location: 404.php'); exit; }

$stmt = $pdo->prepare('SELECT p.*, u.name as submitter, u.email FROM papers p JOIN users u ON p.created_by=u.id WHERE p.uuid=?');
$stmt->execute([$uuid]);
$paper = $stmt->fetch();
if (!$paper) { header('Location: 404.php'); exit; }

// Fetch certificates for this paper
$cert_stmt = $pdo->prepare('SELECT * FROM certificates WHERE paper_uuid=? ORDER BY id');
$cert_stmt->execute([$uuid]);
$certificates = $cert_stmt->fetchAll();

// Check if user can view this paper
require_login();
$u = current_user();
if ($u['role'] === 'author' && $paper['created_by'] != $u['id']) {
    header('Location: dashboard.php'); exit;
}

$statusClass = 'status-pending';
if ($paper['status'] === 'Pending') $statusClass = 'status-pending';
elseif ($paper['status'] === 'Under Review') $statusClass = 'status-review';
elseif ($paper['status'] === 'Approved') $statusClass = 'status-approved';
elseif ($paper['status'] === 'Rejected') $statusClass = 'status-rejected';
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= esc($paper['title']) ?> | Research Journal</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="dashboard-wrapper">
  <?php if ($u['role'] === 'author'): ?>
    <?php include 'includes/author_sidebar.php'; ?>
    <?php include 'includes/author_navbar.php'; ?>
  <?php endif; ?>

  <main class="<?= $u['role'] === 'author' ? 'main-content' : 'container' ?>" style="<?= $u['role'] === 'author' ? 'padding-top: 80px;' : 'padding-top: 2rem;' ?>">
    <?php if ($u['role'] !== 'author'): ?>
      <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.php">Home</a></li>
          <li class="breadcrumb-item active">Paper View</li>
        </ol>
      </nav>
    <?php endif; ?>

    <div class="card shadow-sm mb-4">
      <div class="card-header bg-white py-3">
        <div class="d-flex justify-content-between align-items-start">
          <div>
            <h3 class="mb-2"><?= esc($paper['title']) ?></h3>
            <div class="d-flex gap-3 flex-wrap">
              <span class="status-badge <?= $statusClass ?>"><?= esc($paper['status']) ?></span>
              <small class="text-muted"><i class="far fa-calendar me-1"></i><?= date('F d, Y', strtotime($paper['created_at'])) ?></small>
            </div>
          </div>
          <a href="<?= $u['role'] === 'author' ? 'dashboard.php' : 'index.php' ?>" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i>Back
          </a>
        </div>
      </div>
      <div class="card-body">
        <div class="row g-4">
          <div class="col-md-6">
            <div class="mb-3">
              <h6 class="fw-bold text-secondary mb-2"><i class="fas fa-users text-primary me-2"></i>Authors</h6>
              <p class="mb-0"><?= esc($paper['author_list']) ?></p>
            </div>
            <div class="mb-3">
              <h6 class="fw-bold text-secondary mb-2"><i class="fas fa-user text-primary me-2"></i>Submitted By</h6>
              <p class="mb-0"><?= esc($paper['submitter']) ?> (<?= esc($paper['email']) ?>)</p>
            </div>
          </div>
          <div class="col-md-6">
            <div class="mb-3">
              <h6 class="fw-bold text-secondary mb-2"><i class="fas fa-info-circle text-primary me-2"></i>Status</h6>
              <p class="mb-0"><span class="status-badge <?= $statusClass ?>"><?= esc($paper['status']) ?></span></p>
            </div>
            <?php if ($paper['status'] === 'Approved'): ?>
              <div class="alert alert-success">
                <i class="fas fa-check-circle me-2"></i><strong>Congratulations!</strong> Your paper has been approved and published.
              </div>
            <?php elseif ($paper['status'] === 'Rejected'): ?>
              <div class="alert alert-danger">
                <i class="fas fa-times-circle me-2"></i>Your paper was not approved for publication.
              </div>
            <?php endif; ?>
            <?php if (!empty($paper['review_notes'])): ?>
              <div class="mt-3">
                <h6 class="fw-bold text-secondary mb-2"><i class="fas fa-comment-dots text-info me-2"></i>Review Notes</h6>
                <div class="alert alert-info">
                  <?= nl2br(esc($paper['review_notes'])) ?>
                </div>
              </div>
            <?php endif; ?>
          </div>
        </div>

        <hr class="my-4">

        <h6 class="fw-bold text-secondary mb-3"><i class="fas fa-align-left text-primary me-2"></i>Abstract</h6>
        <p class="text-justify"><?= nl2br(esc($paper['abstract'])) ?></p>
      </div>
    </div>

    <!-- PDF Preview Section -->
    <div class="row g-4">
      <div class="col-lg-6">
        <div class="card shadow-sm">
          <div class="card-header bg-white">
            <h5 class="mb-0"><i class="fas fa-file-pdf text-danger me-2"></i>Research Paper</h5>
          </div>
          <div class="card-body p-0">
            <iframe src="uploads/papers/<?= esc($paper['paper_pdf']) ?>" style="width:100%;height:600px;border:none;"></iframe>
          </div>
          <div class="card-footer bg-white">
            <a href="uploads/papers/<?= esc($paper['paper_pdf']) ?>" class="btn btn-outline-primary" download>
              <i class="fas fa-download me-2"></i>Download Paper
            </a>
          </div>
        </div>
      </div>

      <div class="col-lg-6">
        <div class="card shadow-sm mb-3">
          <div class="card-header bg-white">
            <h5 class="mb-0"><i class="fas fa-file-signature text-warning me-2"></i>Copyright Form</h5>
          </div>
          <div class="card-body p-0">
            <iframe src="uploads/copyrights/<?= esc($paper['copyright_pdf']) ?>" style="width:100%;height:300px;border:none;"></iframe>
          </div>
          <div class="card-footer bg-white">
            <a href="uploads/copyrights/<?= esc($paper['copyright_pdf']) ?>" class="btn btn-outline-warning" download>
              <i class="fas fa-download me-2"></i>Download Copyright Form
            </a>
          </div>
        </div>

        <?php if ($paper['published_pdf']): ?>
          <div class="card shadow-sm mb-3">
            <div class="card-header bg-success text-white">
              <h5 class="mb-0"><i class="fas fa-file-pdf me-2"></i>Published Version</h5>
            </div>
            <div class="card-body">
              <p><i class="fas fa-check-circle text-success me-2"></i>Final formatted version is available</p>
              <a href="published.php?uuid=<?= $paper['uuid'] ?>" class="btn btn-success">
                <i class="fas fa-eye me-2"></i>View Published Paper
              </a>
            </div>
          </div>
        <?php endif; ?>

        <?php if ($paper['status'] === 'Approved' && !empty($certificates)): ?>
          <div class="card shadow-sm border-primary">
            <div class="card-header bg-primary text-white">
              <h5 class="mb-0"><i class="fas fa-certificate me-2"></i>Certificates</h5>
            </div>
            <div class="card-body">
              <i class="fas fa-award fa-4x text-primary mb-3 d-block text-center"></i>
              <h6 class="fw-bold text-center">Publication certificates are ready!</h6>
              <p class="text-secondary small text-center mb-3">Download certificates with QR verification</p>
              
              <div class="list-group">
                <?php foreach ($certificates as $cert): ?>
                  <a href="certificate.php?cert_id=<?= esc($cert['certificate_id']) ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" target="_blank">
                    <div>
                      <i class="fas fa-user-graduate text-primary me-2"></i>
                      <strong><?= esc($cert['author_name']) ?></strong>
                      <?php if (!empty($cert['author_email'])): ?>
                        <br><small class="text-muted ms-4"><?= esc($cert['author_email']) ?></small>
                      <?php endif; ?>
                    </div>
                    <div>
                      <span class="badge bg-primary"><?= esc($cert['certificate_id']) ?></span>
                      <i class="fas fa-download ms-2"></i>
                    </div>
                  </a>
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
<script src="assets/js/main.js"></script>
</body>
</html>