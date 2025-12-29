<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

// Support both old (id) and new (cert_id) parameters
$cert_id = $_GET['cert_id'] ?? null;
$paper_uuid = $_GET['uuid'] ?? null;

$rec = null;

if ($cert_id) {
    // New method: lookup by certificate_id
    $stmt = $pdo->prepare('SELECT c.*, p.title FROM certificates c JOIN papers p ON c.paper_uuid=p.uuid WHERE c.certificate_id=?');
    $stmt->execute([$cert_id]);
    $rec = $stmt->fetch();
} elseif ($paper_uuid) {
    // Lookup by paper_uuid (for backwards compatibility)
    $stmt = $pdo->prepare('SELECT c.*, p.title FROM certificates c JOIN papers p ON c.paper_uuid=p.uuid WHERE p.uuid=? LIMIT 1');
    $stmt->execute([$paper_uuid]);
    $rec = $stmt->fetch();
}

if (!$rec) { 
    header('Location: 404.php'); 
    exit; 
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Certificate - <?= esc($rec['certificate_id']) ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="assets/css/style.css">
<style>
  .certificate-container {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 3rem 0;
    min-height: 100vh;
  }
  .certificate-card {
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 10px 40px rgba(0,0,0,0.2);
  }
  .certificate-header {
    background: linear-gradient(135deg, #2c5aa0 0%, #1e3c72 100%);
    padding: 2rem;
    border-bottom: 4px solid #d4af37;
  }
  .info-box {
    background: #f8f9fa;
    border-left: 4px solid #2c5aa0;
    padding: 1rem;
    margin-bottom: 1rem;
    border-radius: 4px;
  }
  .info-label {
    font-size: 0.85rem;
    color: #6c757d;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 0.25rem;
  }
  .info-value {
    font-size: 1.1rem;
    color: #212529;
    font-weight: 500;
  }
  .cert-badge {
    font-size: 1rem;
    padding: 0.5rem 1rem;
    background: linear-gradient(135deg, #d4af37 0%, #c19a6b 100%);
    border: none;
    box-shadow: 0 2px 8px rgba(212,175,55,0.3);
  }
  .download-section {
    background: #f8f9fa;
    padding: 2rem;
    border-radius: 8px;
    margin-top: 2rem;
  }
</style>
</head>
<body>
<div class="certificate-container">
  <div class="container py-5">
    <div class="row justify-content-center">
      <div class="col-lg-10">
        <div class="card certificate-card">
          <div class="certificate-header text-white text-center">
            <i class="fas fa-award fa-3x mb-3" style="color: #d4af37;"></i>
            <h2 class="mb-2">Publication Certificate</h2>
            <p class="mb-0 opacity-75">Verified & Authenticated</p>
          </div>
          <div class="card-body p-4">
            <!-- Certificate Information -->
            <div class="row g-3 mb-4">
              <div class="col-md-12">
                <div class="info-box">
                  <div class="info-label"><i class="fas fa-file-alt me-2"></i>Paper Title</div>
                  <div class="info-value"><?= esc($rec['title']) ?></div>
                </div>
              </div>
              
              <div class="col-md-6">
                <div class="info-box">
                  <div class="info-label"><i class="fas fa-fingerprint me-2"></i>Certificate ID</div>
                  <div class="info-value">
                    <span class="cert-badge badge"><?= esc($rec['certificate_id']) ?></span>
                  </div>
                </div>
              </div>
              
              <div class="col-md-6">
                <div class="info-box">
                  <div class="info-label"><i class="fas fa-calendar me-2"></i>Issue Date</div>
                  <div class="info-value"><?= date('F d, Y', strtotime($rec['created_at'] ?? 'now')) ?></div>
                </div>
              </div>
            </div>
            
            <?php if (!empty($rec['author_name'])): ?>
            <div class="row g-3 mb-4">
              <div class="col-md-6">
                <div class="info-box">
                  <div class="info-label"><i class="fas fa-user-graduate me-2"></i>Author Name</div>
                  <div class="info-value"><?= esc($rec['author_name']) ?></div>
                </div>
              </div>
              <?php if (!empty($rec['author_email'])): ?>
              <div class="col-md-6">
                <div class="info-box">
                  <div class="info-label"><i class="fas fa-envelope me-2"></i>Author Email</div>
                  <div class="info-value"><?= esc($rec['author_email']) ?></div>
                </div>
              </div>
              <?php endif; ?>
            </div>
            <?php endif; ?>
            
            <?php if ($rec['pdf_path'] && file_exists(__DIR__ . '/uploads/certificates/' . $rec['pdf_path'])): ?>
              <div class="alert alert-success d-flex align-items-center mb-4">
                <i class="fas fa-shield-check fa-2x me-3"></i>
                <div>
                  <strong>Certificate Verified</strong><br>
                  <small>This certificate is authentic and has been verified by our system</small>
                </div>
              </div>
              
              <!-- Certificate Preview -->
              <div class="mb-4">
                <h5 class="mb-3"><i class="fas fa-file-pdf text-danger me-2"></i>Certificate Preview</h5>
                <div style="border: 2px solid #dee2e6; border-radius: 8px; overflow: hidden;">
                  <iframe src="uploads/certificates/<?= esc($rec['pdf_path']) ?>" style="width:100%;height:800px;border:none;"></iframe>
                </div>
              </div>
              
              <!-- Download Section -->
              <div class="download-section text-center">
                <h5 class="mb-3">Download Your Certificate</h5>
                <div class="d-flex gap-3 justify-content-center flex-wrap">
                  <a class="btn btn-primary btn-lg" href="uploads/certificates/<?= esc($rec['pdf_path']) ?>" download>
                    <i class="fas fa-download me-2"></i>Download PDF
                  </a>
                  <a class="btn btn-outline-secondary btn-lg" href="index.php">
                    <i class="fas fa-home me-2"></i>Back to Home
                  </a>
                  <a class="btn btn-outline-primary btn-lg" href="papers.php">
                    <i class="fas fa-search me-2"></i>Browse Papers
                  </a>
                </div>
                <p class="text-muted mt-3 mb-0">
                  <small><i class="fas fa-info-circle me-1"></i>Keep this certificate for your records</small>
                </p>
              </div>
            <?php else: ?>
              <div class="alert alert-warning d-flex align-items-center">
                <i class="fas fa-exclamation-triangle fa-2x me-3"></i>
                <div>
                  <strong>Certificate Not Available</strong><br>
                  <small>The certificate file could not be found. Please contact support.</small>
                </div>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>