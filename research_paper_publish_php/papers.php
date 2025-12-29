<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$search = $_GET['q'] ?? '';
$user = current_user();

if ($search) {
    $stmt = $pdo->prepare("SELECT * FROM papers WHERE status='Approved' AND title LIKE ? ORDER BY created_at DESC");
    $stmt->execute(["%$search%"]);
} else {
    $stmt = $pdo->query("SELECT * FROM papers WHERE status='Approved' ORDER BY created_at DESC");
}
$papers = $stmt->fetchAll();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Browse Papers | IJSRET</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<?php include 'includes/public_navbar.php'; ?>

<!-- Papers Section -->
<div class="container py-5">
  <div class="dashboard-header mb-4">
    <div class="d-flex justify-content-between align-items-center">
      <div>
        <h2 class="mb-2"><i class="fas fa-file-alt text-primary me-2"></i>Published Papers</h2>
        <p class="text-secondary mb-0">Browse our collection of peer-reviewed research</p>
      </div>
    </div>
  </div>

  <!-- Search Bar -->
  <div class="card mb-4">
    <div class="card-body">
      <form method="get" action="papers.php">
        <div class="input-group input-group-lg">
          <span class="input-group-text"><i class="fas fa-search"></i></span>
          <input type="text" name="q" class="form-control" placeholder="Search by title..." value="<?= esc($search) ?>">
          <button class="btn btn-primary px-4" type="submit">Search</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Papers Grid -->
  <div class="row g-4">
    <?php if (empty($papers)): ?>
      <div class="col-12">
        <div class="text-center py-5">
          <i class="fas fa-inbox fa-4x text-secondary mb-3"></i>
          <h4>No papers found</h4>
          <p class="text-secondary">Try a different search term</p>
        </div>
      </div>
    <?php else: ?>
      <?php foreach ($papers as $p): ?>
        <div class="col-md-6 col-lg-4">
          <div class="paper-card h-100">
            <div class="d-flex justify-content-between align-items-start mb-3">
              <span class="status-badge status-approved">Published</span>
              <small class="text-muted"><i class="far fa-calendar me-1"></i><?= date('M d, Y', strtotime($p['created_at'])) ?></small>
            </div>
            <h5 class="paper-title"><?= esc($p['title']) ?></h5>
            <p class="paper-meta mb-2">
              <i class="fas fa-users text-primary me-1"></i>
              <?= esc($p['author_list']) ?>
            </p>
            <p class="text-secondary small mb-3">
              <?= esc(substr($p['abstract'], 0, 150)) ?>...
            </p>
            <div class="d-flex gap-2">
              <a href="published.php?uuid=<?= $p['uuid'] ?>" class="btn btn-primary btn-sm flex-grow-1">
                <i class="fas fa-eye me-1"></i>View Paper
              </a>
             
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>

<!-- Footer -->
<?php include 'includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>