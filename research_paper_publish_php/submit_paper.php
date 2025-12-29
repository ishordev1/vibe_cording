<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';
require_login();
$u = current_user();
if ($u['role'] !== 'author') { header('Location: login.php'); exit; }

$err = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $abstract = trim($_POST['abstract']);
    $author_list = trim($_POST['author_list']);
    $author_emails_json = null;
    
    // Process author emails if provided
    if (isset($_POST['author_emails']) && is_array($_POST['author_emails'])) {
        $author_emails = array_filter(array_map('trim', $_POST['author_emails']));
        if (!empty($author_emails)) {
            $author_emails_json = json_encode($author_emails);
        }
    }
    
    if (!$title || !$abstract || !$author_list) $err = 'All fields required';
    else {
        $v1 = validate_pdf_upload($_FILES['paper_pdf']);
        $v2 = validate_pdf_upload($_FILES['copyright_pdf']);
        if ($v1 !== true) $err = $v1;
        elseif ($v2 !== true) $err = $v2;
        else {
            $paper_fname = save_uploaded_pdf($_FILES['paper_pdf'], UPLOADS_PAPERS, 'paper');
            $copy_fname = save_uploaded_pdf($_FILES['copyright_pdf'], UPLOADS_COPYRIGHTS, 'copyright');
            if ($paper_fname && $copy_fname) {
                global $pdo;
                $uuid = generate_uuid(); // Generate secure UUID
                $stmt = $pdo->prepare('INSERT INTO papers (uuid,title,abstract,author_list,author_emails,paper_pdf,copyright_pdf,created_by) VALUES (?,?,?,?,?,?,?,?)');
                $stmt->execute([$uuid,$title,$abstract,$author_list,$author_emails_json,$paper_fname,$copy_fname,$u['id']]);
                $success = 'Paper submitted successfully! Redirecting...';
                header('Refresh: 2; url=dashboard.php');
            } else $err = 'Failed to save uploads';
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Submit Paper | Research Journal</title>
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
      <h2><i class="fas fa-file-upload text-primary me-2"></i>Submit Research Paper</h2>
      <p class="text-secondary mb-0">Fill in the details and upload your research paper</p>
    </div>

    <div class="row">
      <div class="col-lg-8">
        <div class="card">
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

            <form method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
              <div class="mb-4">
                <label class="form-label fw-semibold">
                  <i class="fas fa-heading text-primary me-1"></i>Paper Title *
                </label>
                <input 
                  name="title" 
                  class="form-control" 
                  placeholder="Enter the title of your research paper"
                  required
                  value="<?= isset($_POST['title']) ? esc($_POST['title']) : '' ?>"
                >
                <div class="invalid-feedback">Title is required</div>
              </div>

              <div class="mb-4">
                <label class="form-label fw-semibold">
                  <i class="fas fa-align-left text-primary me-1"></i>Abstract *
                </label>
                <textarea 
                  name="abstract" 
                  class="form-control" 
                  rows="6" 
                  placeholder="Enter the abstract of your paper (250-500 words recommended)"
                  required
                ><?= isset($_POST['abstract']) ? esc($_POST['abstract']) : '' ?></textarea>
                <div class="invalid-feedback">Abstract is required</div>
                <small class="text-muted">Provide a concise summary of your research</small>
              </div>

              <div class="mb-4">
                <label class="form-label fw-semibold">
                  <i class="fas fa-users text-primary me-1"></i>Authors *
                </label>
                <input 
                  name="author_list" 
                  class="form-control mb-2" 
                  placeholder="e.g., John Doe, Jane Smith, Robert Johnson"
                  required
                  value="<?= isset($_POST['author_list']) ? esc($_POST['author_list']) : '' ?>"
                >
                <div class="invalid-feedback">Authors list is required</div>
                <small class="text-muted d-block mb-3">Separate multiple authors with commas</small>
                
                <div class="card bg-light border-0">
                  <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                      <label class="form-label fw-semibold mb-0">
                        <i class="fas fa-envelope text-primary me-1"></i>Author Email Addresses (for certificates)
                      </label>
                      <button type="button" class="btn btn-sm btn-primary" id="addAuthorEmail">
                        <i class="fas fa-plus me-1"></i>Add Author
                      </button>
                    </div>
                    <small class="text-muted d-block mb-3">Each author will receive an individual certificate at their email address</small>
                    
                    <div id="authorEmailsContainer">
                      <div class="author-email-row mb-2">
                        <div class="input-group">
                          <span class="input-group-text"><i class="fas fa-user"></i></span>
                          <input 
                            type="email" 
                            name="author_emails[]" 
                            class="form-control" 
                            placeholder="Author email address"
                          >
                          <button type="button" class="btn btn-outline-danger remove-author-email" disabled>
                            <i class="fas fa-times"></i>
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="mb-4">
                <label class="form-label fw-semibold">
                  <i class="fas fa-file-pdf text-danger me-1"></i>Research Paper (PDF) *
                </label>
                <input 
                  name="paper_pdf" 
                  type="file" 
                  accept="application/pdf" 
                  class="form-control"
                  required
                >
                <div class="invalid-feedback">Research paper PDF is required</div>
                <small class="text-muted">Maximum file size: 20MB</small>
              </div>

              <div class="mb-4">
                <label class="form-label fw-semibold">
                  <i class="fas fa-file-signature text-warning me-1"></i>Copyright Form (PDF) *
                </label>
                <input 
                  name="copyright_pdf" 
                  type="file" 
                  accept="application/pdf" 
                  class="form-control"
                  required
                >
                <div class="invalid-feedback">Copyright form PDF is required</div>
                <small class="text-muted">Signed copyright transfer agreement</small>
              </div>

              <div class="d-flex gap-3">
                <button class="btn btn-primary btn-lg">
                  <i class="fas fa-paper-plane me-2"></i>Submit Paper
                </button>
                <a href="dashboard.php" class="btn btn-outline-secondary btn-lg">Cancel</a>
              </div>
            </form>
          </div>
        </div>
      </div>

      <div class="col-lg-4">
        <div class="card mb-3">
          <div class="card-body">
            <h6 class="fw-bold mb-3"><i class="fas fa-info-circle text-info me-2"></i>Submission Guidelines</h6>
            <ul class="list-unstyled small">
              <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Ensure your paper is in PDF format</li>
              <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Maximum file size is 20MB</li>
              <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Include all authors in the correct order</li>
              <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Provide email addresses for certificate delivery</li>
              <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Abstract should be 250-500 words</li>
              <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Sign and upload copyright form</li>
            </ul>
          </div>
        </div>

        <div class="card">
          <div class="card-body">
            <h6 class="fw-bold mb-3"><i class="fas fa-clock text-warning me-2"></i>Review Process</h6>
            <div class="small">
              <div class="d-flex mb-2">
                <span class="status-badge status-pending me-2">Pending</span>
                <span>Initial submission</span>
              </div>
              <div class="d-flex mb-2">
                <span class="status-badge status-review me-2">Under Review</span>
                <span>Expert review</span>
              </div>
              <div class="d-flex mb-2">
                <span class="status-badge status-approved me-2">Approved</span>
                <span>Paper accepted</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/main.js"></script>
<script>
// Dynamic author email management
document.addEventListener('DOMContentLoaded', function() {
  const container = document.getElementById('authorEmailsContainer');
  const addBtn = document.getElementById('addAuthorEmail');
  
  // Add author email field
  addBtn.addEventListener('click', function() {
    const newRow = document.createElement('div');
    newRow.className = 'author-email-row mb-2';
    newRow.innerHTML = `
      <div class="input-group">
        <span class="input-group-text"><i class="fas fa-user"></i></span>
        <input 
          type="email" 
          name="author_emails[]" 
          class="form-control" 
          placeholder="Author email address"
        >
        <button type="button" class="btn btn-outline-danger remove-author-email">
          <i class="fas fa-times"></i>
        </button>
      </div>
    `;
    container.appendChild(newRow);
    updateRemoveButtons();
  });
  
  // Remove author email field
  container.addEventListener('click', function(e) {
    if (e.target.closest('.remove-author-email')) {
      const row = e.target.closest('.author-email-row');
      row.remove();
      updateRemoveButtons();
    }
  });
  
  // Update remove button states
  function updateRemoveButtons() {
    const rows = container.querySelectorAll('.author-email-row');
    rows.forEach((row, index) => {
      const btn = row.querySelector('.remove-author-email');
      btn.disabled = rows.length === 1;
    });
  }
});
</script>
</body>
</html>