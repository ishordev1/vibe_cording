<?php
/**
 * Browse Internships (User/Intern)
 * View all published internships and apply
 */

require_once '../../includes/config.php';
require_once '../../includes/session.php';
require_once '../../includes/validation.php';

requireLogin();
requireIntern();

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

// Handle application submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['apply_internship'])) {
    $internship_id = intval($_POST['internship_id'] ?? 0);
    $cover_letter = sanitizeInput($_POST['cover_letter'] ?? '');
    $linkedin = sanitizeInput($_POST['linkedin'] ?? '');
    $github = sanitizeInput($_POST['github'] ?? '');
    
    if (empty($internship_id)) {
        $error = 'Invalid internship';
    } else {
        // Check if user already applied
        $check = $conn->prepare("SELECT id FROM applications WHERE user_id = ? AND internship_id = ?");
        $check->bind_param("ii", $user_id, $internship_id);
        $check->execute();
        $result = $check->get_result();
        
        if ($result->num_rows > 0) {
            $error = 'You have already applied for this internship';
        } else {
            // Handle file upload
            $payment_proof_path = null;
            if (isset($_FILES['payment_proof']) && $_FILES['payment_proof']['error'] === UPLOAD_ERR_OK) {
                $file_validation = validateFile($_FILES['payment_proof'], ['pdf', 'jpg', 'jpeg', 'png']);
                if ($file_validation['valid']) {
                    $filename = generateFileName($_FILES['payment_proof']['name']);
                    $upload_path = UPLOAD_DIR . 'payments/' . $filename;
                    
                    // Create directory if not exists
                    if (!is_dir(UPLOAD_DIR . 'payments/')) {
                        mkdir(UPLOAD_DIR . 'payments/', 0755, true);
                    }
                    
                    if (move_uploaded_file($_FILES['payment_proof']['tmp_name'], $upload_path)) {
                        $payment_proof_path = 'payments/' . $filename;
                    }
                } else {
                    $error = $file_validation['error'];
                }
            }
            
            if (empty($error)) {
                // Insert application
                $insert = $conn->prepare("INSERT INTO applications (user_id, internship_id, cover_letter, linkedin_profile, github_profile, payment_proof_path) 
                VALUES (?, ?, ?, ?, ?, ?)");
                
                $insert->bind_param("iissss", $user_id, $internship_id, $cover_letter, $linkedin, $github, $payment_proof_path);
                
                if ($insert->execute()) {
                    $success = 'Application submitted successfully! Upload your ₹500 security deposit proof.';
                } else {
                    $error = 'Error submitting application';
                }
                $insert->close();
            }
        }
        $check->close();
    }
}

// Get user's existing applications
$user_applications = [];
$apps = $conn->query("SELECT internship_id FROM applications WHERE user_id = $user_id");
while ($app = $apps->fetch_assoc()) {
    $user_applications[] = $app['internship_id'];
}

// Get published internships
$internships = $conn->query("
    SELECT i.*, 
           (SELECT COUNT(*) FROM applications WHERE internship_id = i.id) as total_applications,
           (SELECT COUNT(*) FROM applications WHERE internship_id = i.id AND status = 'Approved') as approved_count
    FROM internships i
    WHERE i.is_published = 1
    ORDER BY i.created_at DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Internships - Internship Platform</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <style>
        :root {
            --primary-color: #667eea;
            --secondary-color: #764ba2;
        }
        
        body {
            background-color: #f8f9fa;
        }
        
        .navbar-custom {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            padding: 15px 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .navbar-custom .brand {
            font-size: 20px;
            font-weight: 700;
            color: white;
        }
        
        .navbar-custom a {
            color: white;
            text-decoration: none;
            margin-left: 20px;
        }
        
        .container-custom {
            max-width: 1000px;
            margin: 30px auto;
            padding: 0 15px;
        }
        
        .internship-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 25px;
            margin-bottom: 20px;
            border-left: 5px solid var(--primary-color);
            transition: all 0.3s;
        }
        
        .internship-card:hover {
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
            transform: translateY(-2px);
        }
        
        .internship-title {
            font-size: 20px;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 10px;
        }
        
        .internship-details {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            margin-bottom: 15px;
        }
        
        .detail-item {
            font-size: 14px;
            color: #6c757d;
        }
        
        .detail-item strong {
            color: #333;
        }
        
        .badge-remote {
            background-color: #17a2b8;
            color: white;
            font-size: 12px;
            padding: 5px 10px;
            border-radius: 20px;
            display: inline-block;
            margin-right: 5px;
        }
        
        .badge-type {
            background-color: #6f42c1;
            color: white;
            font-size: 12px;
            padding: 5px 10px;
            border-radius: 20px;
            display: inline-block;
        }
        
        .skills-tags {
            margin-top: 10px;
        }
        
        .skill-tag {
            display: inline-block;
            background-color: #e9ecef;
            color: #495057;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            margin-right: 5px;
            margin-bottom: 5px;
        }
        
        .apply-btn {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 10px 25px;
            border-radius: 5px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .apply-btn:hover {
            background-color: var(--secondary-color);
        }
        
        .apply-btn:disabled {
            background-color: #6c757d;
            cursor: not-allowed;
        }
        
        .applied-badge {
            background-color: #28a745;
            color: white;
            padding: 10px 25px;
            border-radius: 5px;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <div class="navbar-custom">
        <div class="d-flex justify-content-between align-items-center">
            <div class="brand"><i class="bi bi-briefcase"></i> Browse Internships</div>
            <div>
                <a href="../dashboard.php">Dashboard</a>
                <a href="../../auth/logout.php">Logout</a>
            </div>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="container-custom">
        <!-- Messages -->
        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="bi bi-exclamation-circle"></i> <?= $error ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="bi bi-check-circle"></i> <?= $success ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <!-- Internships List -->
        <?php if ($internships->num_rows === 0): ?>
            <div class="alert alert-info text-center">
                <i class="bi bi-info-circle"></i> No internships available right now
            </div>
        <?php else: ?>
            <?php while ($internship = $internships->fetch_assoc()): ?>
                <div class="internship-card">
                    <div class="internship-title">
                        <?= $internship['title'] ?>
                    </div>
                    
                    <div class="internship-details">
                        <div class="detail-item">
                            <i class="bi bi-briefcase"></i> <strong><?= $internship['role'] ?></strong>
                        </div>
                        <div class="detail-item">
                            <i class="bi bi-calendar"></i> <strong><?= $internship['duration_min'] ?>-<?= $internship['duration_max'] ?> months</strong>
                        </div>
                        <div class="detail-item">
                            <i class="bi bi-people"></i> <strong><?= $internship['total_applications'] ?> applications</strong>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <span class="badge-remote"><i class="bi bi-geo-alt"></i> <?= $internship['remote'] ?></span>
                        <span class="badge-type"><?= $internship['internship_type'] ?></span>
                    </div>
                    
                    <p><?= substr($internship['description'], 0, 200) ?>...</p>
                    
                    <?php if (!empty($internship['skills_required'])): ?>
                        <div class="skills-tags">
                            <strong style="font-size: 14px;">Required Skills:</strong><br>
                            <?php foreach (explode(',', $internship['skills_required']) as $skill): ?>
                                <span class="skill-tag"><?= trim($skill) ?></span>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="mt-4">
                        <?php if (in_array($internship['id'], $user_applications)): ?>
                            <span class="applied-badge">
                                <i class="bi bi-check-circle"></i> Already Applied
                            </span>
                        <?php else: ?>
                            <button class="apply-btn" onclick="showApplicationModal(<?= $internship['id'] ?>, '<?= addslashes($internship['title']) ?>')">
                                <i class="bi bi-send"></i> Apply Now
                            </button>
                        <?php endif; ?>
                        <button class="btn btn-outline-secondary" onclick="viewDetails(<?= $internship['id'] ?>)">
                            <i class="bi bi-eye"></i> View Details
                        </button>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php endif; ?>
    </div>
    
    <!-- Application Modal -->
    <div class="modal fade" id="applicationModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Apply for Internship</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" name="apply_internship" value="1">
                        <input type="hidden" name="internship_id" id="internship_id">
                        
                        <div class="mb-3">
                            <label class="form-label">Internship Title</label>
                            <input type="text" class="form-control" id="internship_title" readonly>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Cover Letter</label>
                            <textarea class="form-control" name="cover_letter" rows="4" placeholder="Tell us why you're interested in this internship"></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">LinkedIn Profile</label>
                                <input type="url" class="form-control" name="linkedin" placeholder="https://linkedin.com/in/...">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">GitHub Profile</label>
                                <input type="url" class="form-control" name="github" placeholder="https://github.com/...">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Upload ₹500 Security Deposit Proof <span class="text-danger">*</span></label>
                            <input type="file" class="form-control" name="payment_proof" accept=".pdf,.jpg,.jpeg,.png" required>
                            <small class="text-muted">Upload receipt/screenshot of ₹500 payment (PDF/JPG/PNG)</small>
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> 
                            <strong>₹500 Security Deposit:</strong> This is a one-time deposit to confirm your commitment. It will be refunded after successful completion of the internship.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Submit Application</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function showApplicationModal(internshipId, title) {
            document.getElementById('internship_id').value = internshipId;
            document.getElementById('internship_title').value = title;
            new bootstrap.Modal(document.getElementById('applicationModal')).show();
        }
        
        function viewDetails(internshipId) {
            window.location.href = 'detail.php?id=' + internshipId;
        }
    </script>
</body>
</html>
