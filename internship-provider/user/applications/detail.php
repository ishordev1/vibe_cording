<?php
/**
 * View Internship Details
 * User views detailed information about an internship
 */

require_once '../../includes/config.php';
require_once '../../includes/session.php';

requireLogin();
requireIntern();

$internship_id = (int)($_GET['id'] ?? 0);

if ($internship_id === 0) {
    header("Location: browse.php");
    exit();
}

// Get internship details
$stmt = $conn->prepare("
    SELECT i.*, 
           u.full_name as created_by_name,
           (SELECT COUNT(*) FROM applications WHERE internship_id = i.id AND status = 'Approved') as approved_count
    FROM internships i
    JOIN users u ON i.created_by = u.id
    WHERE i.id = ? AND i.is_published = TRUE
");
$stmt->bind_param("i", $internship_id);
$stmt->execute();
$result = $stmt->get_result();
$internship = $result->fetch_assoc();

if (!$internship) {
    header("Location: browse.php");
    exit();
}

// Check if user already applied
$check_app = $conn->prepare("SELECT id FROM applications WHERE user_id = ? AND internship_id = ?");
$check_app->bind_param("ii", $_SESSION['user_id'], $internship_id);
$check_app->execute();
$already_applied = $check_app->get_result()->num_rows > 0;
$check_app->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($internship['title']); ?> - Internship Details</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 0;
            margin-bottom: 40px;
        }
        
        .header h1 {
            font-size: 36px;
            font-weight: 700;
            margin-bottom: 10px;
        }
        
        .header-meta {
            font-size: 16px;
            opacity: 0.9;
        }
        
        .details-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            padding: 40px;
            margin-bottom: 30px;
        }
        
        .section-title {
            font-size: 22px;
            font-weight: 600;
            color: #333;
            margin-top: 30px;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #667eea;
        }
        
        .info-row {
            display: flex;
            margin-bottom: 15px;
            align-items: start;
        }
        
        .info-label {
            font-weight: 600;
            color: #667eea;
            min-width: 150px;
        }
        
        .info-value {
            color: #333;
            flex: 1;
        }
        
        .badge-group {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }
        
        .badge-custom {
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
        }
        
        .skill-tags {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }
        
        .skill-tag {
            background: #f0f0f0;
            padding: 6px 12px;
            border-radius: 15px;
            font-size: 13px;
            color: #666;
        }
        
        .btn-large {
            padding: 12px 30px;
            font-size: 16px;
            font-weight: 600;
            border-radius: 5px;
        }
        
        .back-link {
            color: white;
            text-decoration: none;
            font-weight: 500;
            margin-bottom: 20px;
            display: inline-block;
        }
        
        .back-link:hover {
            text-decoration: underline;
        }
        
        .navbar-custom {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 15px 0;
            margin-bottom: 20px;
        }
        
        .navbar-custom a {
            color: white;
            text-decoration: none;
            margin: 0 20px;
            font-weight: 500;
            transition: opacity 0.3s;
        }
        
        .navbar-custom a:hover {
            opacity: 0.8;
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <div class="navbar-custom">
        <div class="container">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <a href="browse.php" class="back-link">← Back to Internships</a>
                <div>
                    <a href="../../user/dashboard.php">Dashboard</a>
                    <a href="../../auth/logout.php">Logout</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Header -->
    <div class="header">
        <div class="container">
            <h1><?php echo htmlspecialchars($internship['title']); ?></h1>
            <div class="header-meta">
                <i class="bi bi-building"></i> <?php echo htmlspecialchars($internship['role']); ?>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container" style="margin-bottom: 40px;">
        <div class="details-container">
            <!-- Quick Info -->
            <div class="badge-group">
                <span class="badge badge-custom" style="background: #e3f2fd; color: #1976d2;">
                    <i class="bi bi-clock"></i> <?php echo $internship['duration_min']; ?>-<?php echo $internship['duration_max']; ?> months
                </span>
                <span class="badge badge-custom" style="background: #f3e5f5; color: #7b1fa2;">
                    <i class="bi bi-tag"></i> <?php echo htmlspecialchars($internship['internship_type']); ?>
                </span>
                <span class="badge badge-custom" style="background: #e8f5e9; color: #388e3c;">
                    <i class="bi bi-geo-alt"></i> <?php echo htmlspecialchars($internship['remote']); ?>
                </span>
                <span class="badge badge-custom" style="background: #fff3e0; color: #f57c00;">
                    <i class="bi bi-briefcase"></i> <?php echo $internship['number_of_positions']; ?> Position<?php echo $internship['number_of_positions'] > 1 ? 's' : ''; ?>
                </span>
            </div>

            <!-- Description -->
            <div class="section-title">📝 About This Internship</div>
            <p style="color: #555; line-height: 1.8; font-size: 16px;">
                <?php echo nl2br(htmlspecialchars($internship['description'])); ?>
            </p>

            <!-- Details Row -->
            <div class="row" style="margin-top: 30px;">
                <div class="col-md-6">
                    <div class="info-row">
                        <div class="info-label">Role:</div>
                        <div class="info-value"><?php echo htmlspecialchars($internship['role']); ?></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Duration:</div>
                        <div class="info-value"><?php echo $internship['duration_min']; ?>-<?php echo $internship['duration_max']; ?> months</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Type:</div>
                        <div class="info-value"><?php echo htmlspecialchars($internship['internship_type']); ?></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-row">
                        <div class="info-label">Remote:</div>
                        <div class="info-value"><?php echo htmlspecialchars($internship['remote']); ?></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Positions:</div>
                        <div class="info-value"><?php echo $internship['number_of_positions']; ?></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Posted:</div>
                        <div class="info-value"><?php echo date('d M Y', strtotime($internship['created_at'])); ?></div>
                    </div>
                </div>
            </div>

            <!-- Skills Required -->
            <?php if (!empty($internship['skills_required'])): ?>
                <div class="section-title">🛠️ Skills Required</div>
                <div class="skill-tags">
                    <?php 
                    $skills = explode(',', $internship['skills_required']);
                    foreach ($skills as $skill):
                        $skill = trim($skill);
                    ?>
                        <span class="skill-tag"><?php echo htmlspecialchars($skill); ?></span>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- Statistics -->
            <div class="section-title">📊 Application Status</div>
            <div class="row">
                <div class="col-md-4">
                    <div style="background: #f0f0f0; padding: 20px; border-radius: 8px; text-align: center;">
                        <div style="font-size: 28px; font-weight: 700; color: #667eea;">
                            <?php echo $internship['approved_count']; ?>
                        </div>
                        <div style="color: #666; margin-top: 5px;">Already Approved</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div style="background: #f0f0f0; padding: 20px; border-radius: 8px; text-align: center;">
                        <div style="font-size: 28px; font-weight: 700; color: #667eea;">
                            ₹500
                        </div>
                        <div style="color: #666; margin-top: 5px;">Security Deposit</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div style="background: #f0f0f0; padding: 20px; border-radius: 8px; text-align: center;">
                        <div style="font-size: 28px; font-weight: 700; color: #667eea;">
                            4
                        </div>
                        <div style="color: #666; margin-top: 5px;">Levels to Complete</div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="section-title">Take Action</div>
            <div style="display: flex; gap: 15px; flex-wrap: wrap;">
                <?php if ($already_applied): ?>
                    <button class="btn btn-primary btn-large" disabled>
                        <i class="bi bi-check-circle"></i> Already Applied
                    </button>
                <?php else: ?>
                    <button class="btn btn-primary btn-large" data-bs-toggle="modal" data-bs-target="#applicationModal" onclick="setInternshipData(<?php echo $internship['id']; ?>, '<?php echo htmlspecialchars(addslashes($internship['title'])); ?>')">
                        <i class="bi bi-send"></i> Apply Now
                    </button>
                <?php endif; ?>
                <a href="browse.php" class="btn btn-outline-secondary btn-large">
                    <i class="bi bi-arrow-left"></i> Back to Internships
                </a>
            </div>

            <!-- Info Box -->
            <div style="background: #e3f2fd; border-left: 4px solid #1976d2; padding: 15px; border-radius: 5px; margin-top: 30px;">
                <strong>💡 Tip:</strong> Once you apply and your application is approved, you'll need to complete 4 levels of tasks to earn your certificate!
            </div>
        </div>
    </div>

    <!-- Application Modal -->
    <div class="modal fade" id="applicationModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Apply for Internship</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="browse.php" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="apply">
                        <input type="hidden" name="internship_id" id="modal_internship_id">
                        
                        <div class="mb-3">
                            <label class="form-label">Internship</label>
                            <input type="text" class="form-control" id="modal_internship_title" disabled>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Cover Letter</label>
                            <textarea class="form-control" name="cover_letter" rows="3" placeholder="Tell us why you're interested in this internship" required></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">LinkedIn Profile</label>
                            <input type="url" class="form-control" name="linkedin_profile" placeholder="https://linkedin.com/in/yourprofile">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">GitHub Profile</label>
                            <input type="url" class="form-control" name="github_profile" placeholder="https://github.com/yourprofile">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Payment Proof (₹500 Security Deposit) *</label>
                            <input type="file" class="form-control" name="payment_proof" accept="image/*,.pdf" required>
                            <small class="text-muted">Upload screenshot of payment (PNG, JPG, PDF)</small>
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

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        function setInternshipData(id, title) {
            document.getElementById('modal_internship_id').value = id;
            document.getElementById('modal_internship_title').value = title;
        }
    </script>
</body>
</html>
