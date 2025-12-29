<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$uuid = $_GET['uuid'] ?? '';
if (!$uuid || !is_valid_uuid($uuid)) { header('Location: 404.php'); exit; }

$stmt = $pdo->prepare('SELECT p.*, u.name as submitter FROM papers p JOIN users u ON p.created_by=u.id WHERE p.uuid=? AND p.status="Approved"');
$stmt->execute([$uuid]);
$p = $stmt->fetch();
if (!$p) { header('Location: 404.php'); exit; }

$user = current_user();
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= esc($p['title']) ?> | IJSRET</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="assets/css/style.css">
<style>
.paper-header {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  padding: 3rem 0;
}
</style>
</head>
<body>
<?php include 'includes/public_navbar.php'; ?>

<!-- Paper Header -->
<div class="paper-header">
  <div class="container">
    <nav aria-label="breadcrumb" class="mb-3">
      <ol class="breadcrumb bg-transparent text-white">
        <li class="breadcrumb-item"><a href="index.php" class="text-white">Home</a></li>
        <li class="breadcrumb-item"><a href="papers.php" class="text-white">Papers</a></li>
        <li class="breadcrumb-item active text-white">Published Paper</li>
      </ol>
    </nav>
    <h1 class="display-5 fw-bold mb-3"><?= esc($p['title']) ?></h1>
    <div class="d-flex gap-4 flex-wrap align-items-center">
      <div>
        <i class="fas fa-users me-2"></i>
        <strong>Authors:</strong> <?= esc($p['author_list']) ?>
      </div>
      <div>
        <i class="far fa-calendar me-2"></i>
        <strong>Published:</strong> <?= date('F d, Y', strtotime($p['created_at'])) ?>
      </div>
      <div>
        <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Peer Reviewed</span>
      </div>
    </div>
  </div>
</div>

<!-- Main Content -->
<div class="container py-5">
  <div class="row g-4">
    <!-- Left Column - Paper Content -->
    <div class="col-lg-8">
      <!-- Abstract -->
      <div class="card shadow-sm mb-4">
        <div class="card-header bg-white">
          <h5 class="mb-0"><i class="fas fa-align-left text-primary me-2"></i>Abstract</h5>
        </div>
        <div class="card-body">
          <p class="text-justify"><?= nl2br(esc($p['abstract'])) ?></p>
        </div>
      </div>

      <!-- PDF Viewer -->
      <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
          <h5 class="mb-0"><i class="fas fa-file-pdf text-danger me-2"></i>Full Paper</h5>
          <?php if ($p['published_pdf']): ?>
            <a href="uploads/published/<?= esc($p['published_pdf']) ?>" class="btn btn-primary btn-sm" download>
              <i class="fas fa-download me-1"></i>Download PDF
            </a>
          <?php endif; ?>
        </div>
        <div class="card-body p-0">
          <?php if ($p['published_pdf']): ?>
            <iframe src="uploads/published/<?= esc($p['published_pdf']) ?>" style="width:100%;height:800px;border:none;"></iframe>
          <?php else: ?>
            <div class="p-5 text-center">
              <i class="fas fa-file-pdf fa-4x text-secondary mb-3"></i>
              <p class="text-secondary">Published PDF not available yet</p>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <!-- Right Column - Sidebar -->
    <div class="col-lg-4">
      <!-- Paper Info -->
      <div class="card shadow-sm mb-3">
        <div class="card-header bg-white">
          <h6 class="mb-0"><i class="fas fa-info-circle text-info me-2"></i>Paper Information</h6>
        </div>
        <div class="card-body">
          <div class="mb-3">
            <small class="text-secondary d-block mb-1">Submitted by</small>
            <strong><?= esc($p['submitter']) ?></strong>
          </div>
          <div class="mb-3">
            <small class="text-secondary d-block mb-1">Publication Date</small>
            <strong><?= date('F d, Y', strtotime($p['created_at'])) ?></strong>
          </div>
          <div class="mb-3">
            <small class="text-secondary d-block mb-1">Status</small>
            <span class="status-badge status-approved">Published</span>
          </div>
          <div>
            <small class="text-secondary d-block mb-1">Paper ID</small>
            <code class="bg-light p-2 d-block rounded">#<?= str_pad($p['id'], 6, '0', STR_PAD_LEFT) ?></code>
          </div>
        </div>
      </div>

      <!-- Share -->
      <div class="card shadow-sm">
        <div class="card-header bg-white">
          <h6 class="mb-0"><i class="fas fa-share-alt text-success me-2"></i>Share This Paper</h6>
        </div>
        <div class="card-body">
          <div class="input-group">
            <input type="text" class="form-control" value="<?= htmlspecialchars($_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) ?>" id="shareUrl" readonly>
            <button class="btn btn-outline-secondary" onclick="copyUrl()">
              <i class="fas fa-copy"></i>
            </button>
          </div>
          <small class="text-muted d-block mt-2">Copy and share this link</small>
        </div>
      </div>

      <!-- Actions -->
      <div class="d-grid gap-2 mt-3">
        <a href="papers.php" class="btn btn-outline-primary">
          <i class="fas fa-arrow-left me-2"></i>Back to Papers
        </a>
      </div>
    </div>
  </div>
</div>

<!-- Footer -->
<?php include 'includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function copyUrl() {
  const input = document.getElementById('shareUrl');
  input.select();
  document.execCommand('copy');
  alert('Link copied to clipboard!');
}
</script>
</body>
</html>