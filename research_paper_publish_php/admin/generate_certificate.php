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
if (!$p || $p['status'] !== 'Approved') { echo 'Paper not approved'; exit; }

// Check if certificate already exists
$chk = $pdo->prepare('SELECT * FROM certificates WHERE paper_uuid=?');
$chk->execute([$uuid]);
$existing = $chk->fetchAll();

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' || (isset($_GET['regenerate']) && $_GET['regenerate'] === '1')) {
    // Generate certificate(s)
    // You'll need FPDF library (place in vendor/fpdf/fpdf.php)
    // and PHP QR Code library (place in vendor/phpqrcode/qrlib.php)

    // For demo, check if libraries exist
    $fpdf_path = BASE_PATH . '/vendor/fpdf186/fpdf.php';
    $qr_path = BASE_PATH . '/vendor/phpqrcode/qrlib.php';
    if (!file_exists($fpdf_path) || !file_exists($qr_path)) {
        $error = 'FPDF or QR library not found. Please install them in /vendor/.';
    } else {
        require_once $fpdf_path;
        require_once $qr_path;

        // Fetch certificate settings
        $settings_stmt = $pdo->query("SELECT * FROM certificate_settings ORDER BY id DESC LIMIT 1");
        $cert_settings = $settings_stmt->fetch(PDO::FETCH_ASSOC);
        if (!$cert_settings) {
            $cert_settings = [];
        }
        
        // Default settings if not configured
        $defaults = [
            'border_color' => '#2c5aa0',
            'header_text' => 'CERTIFICATE OF PUBLICATION',
            'footer_text' => 'This certificate verifies the publication of the research paper.',
            'signature_name' => 'Chief Editor',
            'signature_title' => 'Research Journal',
            'font_family' => 'Times',
            'accent_color' => '#d4af37',
            'logo_url' => '',
            'signature_url' => '',
        ];
        $cert_settings = array_merge($defaults, $cert_settings);

        // Delete existing certificates for this paper
        if (!empty($existing)) {
            $del_stmt = $pdo->prepare('DELETE FROM certificates WHERE paper_uuid=?');
            $del_stmt->execute([$uuid]);
        }

        // Parse author emails
        $author_emails = [];
        if (!empty($p['author_emails'])) {
            $author_emails = json_decode($p['author_emails'], true);
        }
        
        // If no emails provided, create one certificate for all authors
        if (empty($author_emails)) {
            $author_emails = [''];
        }
        
        // Parse author names from author_list
        $author_names = array_map('trim', explode(',', $p['author_list']));
        
        // Generate certificate for each author
        $cert_count = 0;
        foreach ($author_emails as $index => $author_email) {
            $author_name = isset($author_names[$index]) ? $author_names[$index] : $p['author_list'];
            
            // Generate unique certificate ID
            $cert_id = 'CERT-' . strtoupper(bin2hex(random_bytes(6)));

            // Generate QR Code (URL to public certificate page)
            $qr_url = 'http://localhost/research_paper_submission/certificate.php?cert_id=' . $cert_id;
            $qr_filename = 'qr-' . $uuid . '-' . $index . '-' . time() . '.png';
            $qr_path_full = UPLOADS_QRCODES . '/' . $qr_filename;
            QRcode::png($qr_url, $qr_path_full, QR_ECLEVEL_L, 4);

            // Create PDF certificate with custom settings
            $pdf = new FPDF('L', 'mm', 'A4');
            $pdf->AddPage();
            
            // Convert hex colors to RGB for FPDF
            $border_rgb = sscanf($cert_settings['border_color'], "#%02x%02x%02x");
            $accent_rgb = sscanf($cert_settings['accent_color'], "#%02x%02x%02x");
            
            // Set white background
            $pdf->SetFillColor(255, 255, 255);
            $pdf->Rect(0, 0, 297, 210, 'F');
            
            // Draw border (fixed width)
            $border_width = 15;
            $pdf->SetLineWidth($border_width / 10);
            $pdf->SetDrawColor($border_rgb[0], $border_rgb[1], $border_rgb[2]);
            $pdf->Rect($border_width, $border_width, 297 - ($border_width * 2), 210 - ($border_width * 2));
            
            // Inner decorative border
            $pdf->SetLineWidth(0.5);
            $pdf->Rect($border_width + 3, $border_width + 3, 297 - ($border_width * 2) - 6, 210 - ($border_width * 2) - 6);
            
            // Header text at top
            $pdf->SetFont($cert_settings['font_family'], 'B', 28);
            $pdf->SetTextColor($accent_rgb[0], $accent_rgb[1], $accent_rgb[2]);
            $pdf->SetY(30);
            $pdf->Cell(0, 15, $cert_settings['header_text'], 0, 1, 'C');
            
            // Decorative line
            $pdf->SetDrawColor($accent_rgb[0], $accent_rgb[1], $accent_rgb[2]);
            $pdf->SetLineWidth(0.8);
            $line_y = $pdf->GetY() + 5;
            $pdf->Line(80, $line_y, 217, $line_y);
            
            // Add logo below header if exists
            if (!empty($cert_settings['logo_url'])) {
                $logo_path = BASE_PATH . '/' . $cert_settings['logo_url'];
                if (file_exists($logo_path)) {
                    $image_info = getimagesize($logo_path);
                    if ($image_info) {
                        $max_width = 35;
                        $aspect_ratio = $image_info[1] / $image_info[0];
                        $logo_width = $max_width;
                        $logo_height = $max_width * $aspect_ratio;
                        $logo_x = (297 - $logo_width) / 2;
                        $logo_y = $pdf->GetY() + 8;
                        $pdf->Image($logo_path, $logo_x, $logo_y, $logo_width, $logo_height);
                        $pdf->SetY($logo_y + $logo_height + 8);
                    }
                } else {
                    $pdf->Ln(5);
                }
            } else {
                $pdf->Ln(5);
            }
            
            // Body text
            $pdf->SetFont($cert_settings['font_family'], '', 13);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->Ln(3);
            $pdf->Cell(0, 8, 'This certifies that the following paper has been published:', 0, 1, 'C');

            $pdf->SetFont($cert_settings['font_family'], 'B', 15);
            $pdf->Ln(3);
            $pdf->MultiCell(0, 8, $p['title'], 0, 'C');

            $pdf->SetFont($cert_settings['font_family'], '', 11);
            $pdf->Ln(3);
            $pdf->Cell(0, 6, 'Author: ' . $author_name, 0, 1, 'C');
            if (!empty($author_email)) {
                $pdf->Cell(0, 6, 'Email: ' . $author_email, 0, 1, 'C');
            }
            $pdf->Cell(0, 6, 'Publication Date: ' . date('F d, Y'), 0, 1, 'C');
            
            // Footer text
            $pdf->Ln(4);
            $pdf->SetFont($cert_settings['font_family'], 'I', 10);
            $pdf->SetTextColor(100, 100, 100);
            $pdf->MultiCell(0, 5, $cert_settings['footer_text'], 0, 'C');
            
            // Add spacing before signature and certificate ID
            $pdf->Ln(8);
            
            // Add QR Code image
            if (file_exists($qr_path_full)) {
                $pdf->Image($qr_path_full, 235, 155, 35, 35);
            }
            
            // Signature section on left side
            if (!empty($cert_settings['signature_url'])) {
                $signature_path = BASE_PATH . '/' . $cert_settings['signature_url'];
                if (file_exists($signature_path)) {
                    // Add digital signature image on left
                    $pdf->Image($signature_path, 50, 155, 50, 15);
                    $pdf->SetY(172);
                }
            }
            $pdf->Line(50, 171, 100, 171);
            $pdf->SetFont($cert_settings['font_family'], 'B', 12);
            $pdf->SetXY(60, 172);
            $pdf->Cell(55, 5, $cert_settings['signature_name'], 5, 1, 'L');
            $pdf->SetFont($cert_settings['font_family'], '', 10);
            $pdf->SetX(60);
            $pdf->Cell(55, 5, $cert_settings['signature_title'], 0, 0, 'L');
            
            // Certificate ID at bottom left without extra margin
            $pdf->SetFont($cert_settings['font_family'], '', 9);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetXY(25, 184);
            $pdf->Cell(0, 5, 'Certificate ID: ' . $cert_id, 0, 0, 'L');

            // Save certificate PDF
            $cert_pdf_name = 'certificate-' . $uuid . '-' . $index . '-' . time() . '.pdf';
            $cert_pdf_path = UPLOADS_CERTIFICATES . '/' . $cert_pdf_name;
            $pdf->Output('F', $cert_pdf_path);

            // Save to database - only paper_uuid, no paper_id
            $ins = $pdo->prepare('INSERT INTO certificates (certificate_id,paper_uuid,author_id,author_name,author_email,qr_code_path,pdf_path) VALUES (?,?,?,?,?,?,?)');
            $ins->execute([$cert_id, $uuid, $p['created_by'], $author_name, $author_email, $qr_filename, $cert_pdf_name]);
            $cert_count++;
        }

        $success = "Successfully generated $cert_count certificate(s)!";
        header("Refresh: 2; url=view_paper.php?uuid=$uuid");
    }
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Generate Certificates - Admin Panel</title>
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
          <h2><i class="fas fa-award text-primary me-2"></i>Generate Certificates</h2>
          <p class="text-secondary mb-0">Create publication certificates for all authors</p>
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
            <?php if ($error): ?>
              <div class="alert alert-danger d-flex align-items-center">
                <i class="fas fa-exclamation-circle me-2"></i>
                <div>
                  <strong>Error:</strong> <?= esc($error) ?>
                  <hr>
                  <p class="mb-0">
                    <strong>FPDF:</strong> <a href="http://www.fpdf.org/" target="_blank">http://www.fpdf.org/</a><br>
                    <strong>PHP QR Code:</strong> <a href="https://sourceforge.net/projects/phpqrcode/" target="_blank">https://sourceforge.net/projects/phpqrcode/</a>
                  </p>
                </div>
              </div>
            <?php endif; ?>

            <?php if ($success): ?>
              <div class="alert alert-success d-flex align-items-center">
                <i class="fas fa-check-circle me-2"></i>
                <div><?= esc($success) ?></div>
              </div>
            <?php endif; ?>

            <?php if (!empty($existing)): ?>
              <div class="alert alert-warning d-flex align-items-center mb-4">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <div>
                  <strong>Note:</strong> <?= count($existing) ?> certificate(s) already exist for this paper. 
                  Generating new certificates will replace the existing ones.
                </div>
              </div>

              <div class="card bg-light mb-4">
                <div class="card-body">
                  <h6 class="fw-bold mb-3">Existing Certificates:</h6>
                  <div class="list-group">
                    <?php foreach ($existing as $cert): ?>
                      <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                          <i class="fas fa-certificate text-primary me-2"></i>
                          <strong><?= esc($cert['author_name']) ?></strong>
                          <?php if (!empty($cert['author_email'])): ?>
                            <br><small class="text-muted ms-4"><?= esc($cert['author_email']) ?></small>
                          <?php endif; ?>
                        </div>
                        <span class="badge bg-primary"><?= esc($cert['certificate_id']) ?></span>
                      </div>
                    <?php endforeach; ?>
                  </div>
                </div>
              </div>
            <?php endif; ?>

            <form method="post">
              <div class="mb-4">
                <h6 class="fw-bold mb-3">
                  <i class="fas fa-users text-primary me-2"></i>Authors to Generate Certificates For:
                </h6>
                <?php
                $author_names = array_map('trim', explode(',', $p['author_list']));
                $author_emails = !empty($p['author_emails']) ? json_decode($p['author_emails'], true) : [];
                ?>
                <div class="list-group">
                  <?php foreach ($author_names as $index => $author_name): 
                    $email = $author_emails[$index] ?? '';
                  ?>
                    <div class="list-group-item">
                      <div class="d-flex align-items-center">
                        <i class="fas fa-user-graduate text-success me-3 fa-2x"></i>
                        <div>
                          <strong><?= esc($author_name) ?></strong>
                          <?php if (!empty($email)): ?>
                            <br><small class="text-muted"><?= esc($email) ?></small>
                          <?php else: ?>
                            <br><small class="text-warning">No email provided</small>
                          <?php endif; ?>
                        </div>
                      </div>
                    </div>
                  <?php endforeach; ?>
                </div>
                <small class="text-muted mt-2 d-block">
                  Total: <?= count($author_names) ?> certificate(s) will be generated
                </small>
              </div>

              <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                Each certificate will include:
                <ul class="mb-0 mt-2">
                  <li>Paper title</li>
                  <li>Individual author name</li>
                  <li>Author email (if provided)</li>
                  <li>Unique Certificate ID</li>
                  <li>QR code for verification</li>
                  <li>Publication date</li>
                </ul>
              </div>

              <div class="d-flex gap-3">
                <button type="submit" class="btn btn-primary btn-lg">
                  <i class="fas fa-magic me-2"></i><?= !empty($existing) ? 'Regenerate' : 'Generate' ?> Certificates
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
              <strong class="d-block text-secondary small">Status</strong>
              <p class="mb-0"><span class="badge bg-success"><?= esc($p['status']) ?></span></p>
            </div>
            <div class="mb-3">
              <strong class="d-block text-secondary small">Total Authors</strong>
              <p class="mb-0"><?= count($author_names) ?></p>
            </div>
            <div class="mb-0">
              <strong class="d-block text-secondary small">Submitted On</strong>
              <p class="mb-0"><?= date('F d, Y', strtotime($p['created_at'])) ?></p>
            </div>
          </div>
        </div>

        <!-- Certificate Info -->
        <div class="card border-info">
          <div class="card-header bg-info text-white">
            <h6 class="mb-0"><i class="fas fa-lightbulb me-2"></i>Certificate Features</h6>
          </div>
          <div class="card-body small">
            <ul class="list-unstyled mb-0">
              <li class="mb-2">
                <i class="fas fa-check text-success me-2"></i>
                Unique ID per certificate
              </li>
              <li class="mb-2">
                <i class="fas fa-check text-success me-2"></i>
                Individual author names
              </li>
              <li class="mb-2">
                <i class="fas fa-check text-success me-2"></i>
                QR code verification
              </li>
              <li class="mb-2">
                <i class="fas fa-check text-success me-2"></i>
                PDF format (downloadable)
              </li>
              <li class="mb-0">
                <i class="fas fa-check text-success me-2"></i>
                Professional design
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