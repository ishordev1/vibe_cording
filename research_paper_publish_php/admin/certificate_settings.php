<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_login();
$u = current_user();
if ($u['role'] !== 'admin') { header('Location: ../login.php'); exit; }

// Certificate settings table already exists from schema
// No need to create it here

// Handle form submission
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle logo upload
    $logo_url = $_POST['existing_logo'] ?? '';
    if (isset($_FILES['logo_file']) && $_FILES['logo_file']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = BASE_PATH . '/uploads/certificates/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_ext = strtolower(pathinfo($_FILES['logo_file']['name'], PATHINFO_EXTENSION));
        $allowed = ['png', 'jpg', 'jpeg'];
        
        if (in_array($file_ext, $allowed)) {
            $logo_filename = 'cert_logo_' . time() . '.' . $file_ext;
            $logo_path = $upload_dir . $logo_filename;
            
            if (move_uploaded_file($_FILES['logo_file']['tmp_name'], $logo_path)) {
                $logo_url = 'uploads/certificates/' . $logo_filename;
            }
        }
    }
    
    // Handle signature upload
    $signature_url = $_POST['existing_signature'] ?? '';
    if (isset($_FILES['signature_file']) && $_FILES['signature_file']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = BASE_PATH . '/uploads/certificates/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_ext = strtolower(pathinfo($_FILES['signature_file']['name'], PATHINFO_EXTENSION));
        $allowed = ['png', 'jpg', 'jpeg'];
        
        if (in_array($file_ext, $allowed)) {
            $signature_filename = 'cert_signature_' . time() . '.' . $file_ext;
            $signature_path = $upload_dir . $signature_filename;
            
            if (move_uploaded_file($_FILES['signature_file']['tmp_name'], $signature_path)) {
                $signature_url = 'uploads/certificates/' . $signature_filename;
            }
        }
    }
    
    // Update settings (only use columns that exist in database)
    $stmt = $pdo->prepare("UPDATE certificate_settings SET 
        border_color = ?,
        header_text = ?,
        footer_text = ?,
        signature_name = ?,
        signature_title = ?,
        font_family = ?,
        accent_color = ?,
        logo_url = ?,
        signature_url = ?
        WHERE id = 1");
    $stmt->execute([
        $_POST['border_color'] ?? '#2c5aa0',
        $_POST['header_text'] ?? 'CERTIFICATE OF PUBLICATION',
        $_POST['footer_text'] ?? 'This certificate verifies the publication of the research paper.',
        $_POST['signature_name'] ?? 'Chief Editor',
        $_POST['signature_title'] ?? 'Research Journal',
        $_POST['font_family'] ?? 'Times',
        $_POST['accent_color'] ?? '#d4af37',
        $logo_url,
        $signature_url
    ]);

    $msg = 'Settings saved successfully!';
}

// Fetch current settings
$stmt = $pdo->query("SELECT * FROM certificate_settings ORDER BY id DESC LIMIT 1");
$current_settings = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$current_settings) {
    $current_settings = [];
}

// Default values (match database columns)
$defaults = [
    'border_color' => '#2c5aa0',
    'header_text' => 'CERTIFICATE OF PUBLICATION',
    'footer_text' => 'This certificate verifies the publication of the research paper.',
    'signature_name' => 'Chief Editor',
    'signature_title' => 'Research Journal',
    'logo_url' => '',
    'signature_url' => '',
    'font_family' => 'Times',
    'accent_color' => '#d4af37',
];

$settings = array_merge($defaults, $current_settings);
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Certificate Settings - Admin Panel</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="dashboard-wrapper">
  <?php include 'includes/admin_sidebar.php'; ?>
  <?php include 'includes/admin_navbar.php'; ?>

  <main class="main-content" style="padding-top: 80px;">
    <div class="dashboard-header">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <h2><i class="fas fa-certificate text-warning me-2"></i>Certificate Settings</h2>
          <p class="text-secondary mb-0">Customize the appearance of publication certificates</p>
        </div>
        <a href="dashboard.php" class="btn btn-outline-secondary">
          <i class="fas fa-arrow-left me-1"></i>Back to Dashboard
        </a>
      </div>
    </div>

    <?php if ($msg): ?>
      <div class="alert alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle me-2"></i><?= esc($msg) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    <?php endif; ?>

    <div class="row g-4">
      <!-- Settings Form -->
      <div class="col-lg-8">
        <div class="card shadow-sm">
          <div class="card-header bg-white">
            <h5 class="mb-0"><i class="fas fa-sliders-h text-primary me-2"></i>Customization Options</h5>
          </div>
          <div class="card-body">
            <form method="post" enctype="multipart/form-data">
              <!-- Color Settings -->
              <div class="mb-4">
                <h6 class="fw-bold border-bottom pb-2 mb-3"><i class="fas fa-palette text-primary me-2"></i>Colors</h6>
                <div class="row g-3">
                  <div class="col-md-6">
                    <label class="form-label">Border Color</label>
                    <div class="input-group">
                      <input type="color" class="form-control form-control-color" name="border_color" value="<?= esc($settings['border_color']) ?>">
                      <input type="text" class="form-control" value="<?= esc($settings['border_color']) ?>" readonly>
                    </div>
                    <small class="text-muted">Main border color</small>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Accent Color</label>
                    <div class="input-group">
                      <input type="color" class="form-control form-control-color" name="accent_color" value="<?= esc($settings['accent_color']) ?>">
                      <input type="text" class="form-control" value="<?= esc($settings['accent_color']) ?>" readonly>
                    </div>
                    <small class="text-muted">Headers & decorations</small>
                  </div>
                </div>
              </div>

              <!-- Text Content -->
              <div class="mb-4">
                <h6 class="fw-bold border-bottom pb-2 mb-3"><i class="fas fa-font text-primary me-2"></i>Text Content</h6>
                <div class="row g-3">
                  <div class="col-md-12">
                    <label class="form-label">Header Text</label>
                    <input type="text" class="form-control" name="header_text" value="<?= esc($settings['header_text']) ?>" maxlength="100">
                    <small class="text-muted">Main certificate title</small>
                  </div>
                  <div class="col-md-12">
                    <label class="form-label">Footer Text</label>
                    <textarea class="form-control" name="footer_text" rows="2" maxlength="200"><?= esc($settings['footer_text']) ?></textarea>
                    <small class="text-muted">Description text at bottom</small>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Signature Name</label>
                    <input type="text" class="form-control" name="signature_name" value="<?= esc($settings['signature_name']) ?>" maxlength="100">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Signature Title</label>
                    <input type="text" class="form-control" name="signature_title" value="<?= esc($settings['signature_title']) ?>" maxlength="100">
                  </div>
                </div>
              </div>

              <!-- Typography -->
              <div class="mb-4">
                <h6 class="fw-bold border-bottom pb-2 mb-3"><i class="fas fa-text-height text-primary me-2"></i>Typography</h6>
                <div class="row g-3">
                  <div class="col-md-6">
                    <label class="form-label">Font Family</label>
                    <select class="form-select" name="font_family">
                      <option value="Times" <?= $settings['font_family'] === 'Times' ? 'selected' : '' ?>>Times New Roman</option>
                      <option value="Arial" <?= $settings['font_family'] === 'Arial' ? 'selected' : '' ?>>Arial</option>
                      <option value="Helvetica" <?= $settings['font_family'] === 'Helvetica' ? 'selected' : '' ?>>Helvetica</option>
                      <option value="Courier" <?= $settings['font_family'] === 'Courier' ? 'selected' : '' ?>>Courier</option>
                    </select>
                  </div>
                </div>
              </div>

              <!-- Logo -->
              <div class="mb-4">
                <h6 class="fw-bold border-bottom pb-2 mb-3"><i class="fas fa-image text-primary me-2"></i>Logo & Branding</h6>
                <div class="row g-3">
                  <div class="col-md-12">
                    <label class="form-label">Certificate Logo (Optional)</label>
                    <input type="file" class="form-control" name="logo_file" id="logoFile" accept="image/png,image/jpeg,image/jpg">
                    <small class="text-muted">Upload PNG or JPG image. Recommended size: 200x200px. Leave empty for text-only design.</small>
                    <input type="hidden" name="existing_logo" value="<?= esc($settings['logo_url']) ?>">
                    <?php if (!empty($settings['logo_url']) && file_exists(BASE_PATH . '/' . $settings['logo_url'])): ?>
                      <div class="mt-2">
                        <div class="d-flex align-items-center gap-2">
                          <img src="../<?= esc($settings['logo_url']) ?>" alt="Current Logo" class="border rounded" style="max-width: 100px; max-height: 100px;">
                          <div>
                            <small class="text-success d-block"><i class="fas fa-check-circle me-1"></i>Current logo</small>
                            <small class="text-muted d-block"><?= esc($settings['logo_url']) ?></small>
                          </div>
                        </div>
                      </div>
                    <?php endif; ?>
                    <div id="logoPreview" class="mt-2" style="display: none;">
                      <img id="logoPreviewImg" src="" alt="Logo Preview" class="border rounded" style="max-width: 150px; max-height: 150px;">
                    </div>
                  </div>
                  
                  <div class="col-md-12">
                    <label class="form-label">Digital Signature (Optional)</label>
                    <input type="file" class="form-control" name="signature_file" id="signatureFile" accept="image/png,image/jpeg,image/jpg">
                    <small class="text-muted">Upload PNG or JPG image. Recommended: transparent background, 300x100px.</small>
                    <input type="hidden" name="existing_signature" value="<?= esc($settings['signature_url']) ?>">
                    <?php if (!empty($settings['signature_url']) && file_exists(BASE_PATH . '/' . $settings['signature_url'])): ?>
                      <div class="mt-2">
                        <div class="d-flex align-items-center gap-2">
                          <img src="../<?= esc($settings['signature_url']) ?>" alt="Current Signature" class="border rounded bg-white" style="max-width: 150px; max-height: 60px;">
                          <div>
                            <small class="text-success d-block"><i class="fas fa-check-circle me-1"></i>Current signature</small>
                            <small class="text-muted d-block"><?= esc($settings['signature_url']) ?></small>
                          </div>
                        </div>
                      </div>
                    <?php endif; ?>
                    <div id="signaturePreview" class="mt-2" style="display: none;">
                      <img id="signaturePreviewImg" src="" alt="Signature Preview" class="border rounded bg-white" style="max-width: 200px; max-height: 80px;">
                    </div>
                  </div>
                </div>
              </div>

              <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                  <i class="fas fa-save me-2"></i>Save Settings
                </button>
                <a href="preview_certificate.php" class="btn btn-success" target="_blank">
                  <i class="fas fa-eye me-2"></i>Preview Certificate
                </a>
              </div>
              <small class="text-muted d-block mt-2">
                <i class="fas fa-info-circle me-1"></i>Click "Preview Certificate" to see a sample certificate with current settings
              </small>
            </form>
          </div>
        </div>
      </div>

      <!-- Preview & Info -->
      <div class="col-lg-4">
        <!-- Quick Preview -->
        <div class="card shadow-sm mb-3">
          <div class="card-header bg-white">
            <h6 class="mb-0"><i class="fas fa-eye text-info me-2"></i>Live Preview</h6>
          </div>
          <div class="card-body p-0">
            <div id="previewBox" class="p-4 text-center position-relative" style="background: #ffffff; border: 5px solid <?= esc($settings['border_color']) ?>;">
              <!-- Inner border -->
              <div style="position: absolute; top: 8px; left: 8px; right: 8px; bottom: 8px; border: 1px solid <?= esc($settings['border_color']) ?>; opacity: 0.5; pointer-events: none;"></div>
              
              <!-- Header -->
              <div id="previewHeader" class="mb-2 fw-bold" style="color: <?= esc($settings['accent_color']) ?>; font-size: 1.5rem; font-family: <?= esc($settings['font_family']) ?>;">
                <span id="previewHeaderText"><?= esc($settings['header_text']) ?></span>
              </div>
              
              <!-- Decorative line -->
              <div class="mb-3 d-flex justify-content-center">
                <div style="width: 120px; height: 2px; background: <?= esc($settings['accent_color']) ?>;"></div>
              </div>
              
              <!-- Logo placeholder -->
              <div class="mb-3">
                <?php if (!empty($settings['logo_url']) && file_exists(BASE_PATH . '/' . $settings['logo_url'])): ?>
                  <img src="../<?= esc($settings['logo_url']) ?>" alt="Logo" style="max-width: 60px; max-height: 60px;">
                <?php else: ?>
                  <div class="d-inline-block border rounded p-2" style="width: 60px; height: 60px; background: #f0f0f0;">
                    <i class="fas fa-image text-muted"></i>
                  </div>
                <?php endif; ?>
              </div>
              
              <!-- Body text -->
              <div class="mb-2" style="font-size: 0.75rem; font-family: <?= esc($settings['font_family']) ?>;">
                This certifies that the following paper has been published:
              </div>
              
              <div class="mb-2 fw-bold" style="font-size: 0.85rem; font-family: <?= esc($settings['font_family']) ?>;">
                Sample Research Paper Title
              </div>
              
              <div style="font-size: 0.7rem; font-family: <?= esc($settings['font_family']) ?>;">
                <div id="previewAuthor" class="mb-1">Author: Sample Author</div>
                <div class="mb-1">Publication Date: <?= date('F d, Y') ?></div>
              </div>
              
              <!-- Footer text -->
              <div class="mt-2 mb-3" style="font-size: 0.65rem; font-style: italic; color: #666; font-family: <?= esc($settings['font_family']) ?>;">
                <span id="previewFooter"><?= esc($settings['footer_text']) ?></span>
              </div>
              
              <!-- Signature -->
              <div class="d-flex justify-content-between align-items-end mt-3" style="font-size: 0.7rem; font-family: <?= esc($settings['font_family']) ?>;">
                <div class="text-start" style="flex: 0 0 40%;">
                  <div style="border-top: 1px solid #333; padding-top: 4px;">
                    <div id="previewSignature">
                      <strong id="previewSigName"><?= esc($settings['signature_name']) ?></strong><br>
                      <small id="previewSigTitle" class="text-muted"><?= esc($settings['signature_title']) ?></small>
                    </div>
                  </div>
                </div>
                <div class="text-end" style="flex: 0 0 25%;">
                  <div class="border rounded p-1" style="background: #f8f9fa; font-size: 0.5rem;">
                    <i class="fas fa-qrcode"></i><br>QR
                  </div>
                </div>
              </div>
              
              <!-- Certificate ID -->
              <div class="text-start mt-2" style="font-size: 0.6rem; color: #666;">
                Certificate ID: CERT-XXXXX
              </div>
            </div>
          </div>
        </div>

        <!-- Tips -->
        <div class="card shadow-sm border-warning mb-3">
          <div class="card-header bg-warning bg-opacity-10">
            <h6 class="mb-0"><i class="fas fa-lightbulb text-warning me-2"></i>Design Tips</h6>
          </div>
          <div class="card-body">
            <ul class="small mb-0">
              <li class="mb-2">Use professional colors (blues, golds, burgundy)</li>
              <li class="mb-2">Keep border width between 10-20mm</li>
              <li class="mb-2">Choose readable fonts (Times, Arial)</li>
              <li class="mb-2">Ensure good contrast between text and background</li>
              <li>Test the certificate after making changes</li>
            </ul>
          </div>
        </div>

        <!-- Default Presets -->
        <div class="card shadow-sm">
          <div class="card-header bg-white">
            <h6 class="mb-0"><i class="fas fa-magic text-primary me-2"></i>Quick Presets</h6>
          </div>
          <div class="card-body">
            <div class="d-grid gap-2">
              <button class="btn btn-sm btn-outline-primary" onclick="applyPreset('classic')">
                <i class="fas fa-certificate me-1"></i>Classic Blue & Gold
              </button>
              <button class="btn btn-sm btn-outline-success" onclick="applyPreset('elegant')">
                <i class="fas fa-certificate me-1"></i>Elegant Burgundy
              </button>
              <button class="btn btn-sm btn-outline-info" onclick="applyPreset('modern')">
                <i class="fas fa-certificate me-1"></i>Modern Teal
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/main.js"></script>
<script>
// Logo file preview
document.getElementById('logoFile')?.addEventListener('change', function(e) {
  const file = e.target.files[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = function(e) {
      document.getElementById('logoPreviewImg').src = e.target.result;
      document.getElementById('logoPreview').style.display = 'block';
    };
    reader.readAsDataURL(file);
  }
});

// Signature file preview
document.getElementById('signatureFile')?.addEventListener('change', function(e) {
  const file = e.target.files[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = function(e) {
      document.getElementById('signaturePreviewImg').src = e.target.result;
      document.getElementById('signaturePreview').style.display = 'block';
    };
    reader.readAsDataURL(file);
  }
});

// Live preview updates
function updatePreview() {
  const previewBox = document.getElementById('previewBox');
  const bgColor = document.querySelector('[name="bg_color"]').value;
  const borderColor = document.querySelector('[name="border_color"]').value;
  const borderWidth = document.querySelector('[name="border_width"]').value;
  const accentColor = document.querySelector('[name="accent_color"]').value;
  const fontFamily = document.querySelector('[name="font_family"]').value;
  const headerText = document.querySelector('[name="header_text"]').value;
  const footerText = document.querySelector('[name="footer_text"]').value;
  const sigName = document.querySelector('[name="signature_name"]').value;
  const sigTitle = document.querySelector('[name="signature_title"]').value;
  
  // Update preview box styles
  previewBox.style.background = bgColor;
  previewBox.style.borderColor = borderColor;
  previewBox.style.borderWidth = Math.max(1, Math.min(borderWidth / 3, 8)) + 'px';
  
  // Update inner border
  const innerBorder = previewBox.querySelector('div[style*="position: absolute"]');
  if (innerBorder) {
    innerBorder.style.borderColor = borderColor;
  }
  
  // Update decorative line
  const decorLine = previewBox.querySelector('div[style*="width: 120px"]');
  if (decorLine) {
    decorLine.style.background = accentColor;
  }
  
  // Update header
  const header = document.getElementById('previewHeader');
  header.style.color = accentColor;
  header.style.fontFamily = fontFamily;
  document.getElementById('previewHeaderText').textContent = headerText;
  
  // Update body text font
  previewBox.querySelectorAll('div[style*="font-family"]').forEach(el => {
    const currentStyle = el.getAttribute('style');
    el.setAttribute('style', currentStyle.replace(/font-family:[^;]+/, 'font-family: ' + fontFamily));
  });
  
  // Update footer
  document.getElementById('previewFooter').textContent = footerText;
  
  // Update signature
  document.getElementById('previewSigName').textContent = sigName;
  document.getElementById('previewSigTitle').textContent = sigTitle;
}

// Sync color pickers with text inputs and update preview
document.querySelectorAll('.form-control-color').forEach(colorInput => {
  colorInput.addEventListener('input', function() {
    this.nextElementSibling.value = this.value;
    updatePreview();
  });
});

// Update preview on any form input change
document.querySelectorAll('input, select, textarea').forEach(input => {
  input.addEventListener('input', updatePreview);
  input.addEventListener('change', updatePreview);
});

// Preset themes
const presets = {
  classic: {
    bg_color: '#ffffff',
    border_color: '#2c5aa0',
    accent_color: '#d4af37',
    border_width: 15,
    font_family: 'Times'
  },
  elegant: {
    bg_color: '#fef9f3',
    border_color: '#8b2332',
    accent_color: '#c19a6b',
    border_width: 12,
    font_family: 'Times'
  },
  modern: {
    bg_color: '#f0f9ff',
    border_color: '#0891b2',
    accent_color: '#0e7490',
    border_width: 10,
    font_family: 'Arial'
  }
};

function applyPreset(name) {
  const preset = presets[name];
  if (!preset) return;
  
  document.querySelector('[name="bg_color"]').value = preset.bg_color;
  document.querySelector('[name="border_color"]').value = preset.border_color;
  document.querySelector('[name="accent_color"]').value = preset.accent_color;
  document.querySelector('[name="border_width"]').value = preset.border_width;
  document.querySelector('[name="font_family"]').value = preset.font_family;
  
  // Update color display
  document.querySelectorAll('.form-control-color').forEach(input => {
    const name = input.name;
    if (preset[name]) {
      input.value = preset[name];
      input.nextElementSibling.value = preset[name];
    }
  });
  
  // Update preview
  updatePreview();
}
</script>
</body>
</html>
