<?php
/**
 * Admin - View Application Details
 * Detailed view of a single application
 */

require_once '../../includes/config.php';
require_once '../../includes/session.php';

requireLogin();
requireAdmin();

$app_id = intval($_GET['id'] ?? 0);

// Get application details
$application = $conn->query("
    SELECT a.*, 
           u.full_name, u.email, u.phone, u.college_name, u.degree, u.cgpa,
           i.title as internship_title, i.role, i.description as internship_desc,
           ol.offer_id, ol.start_date, ol.end_date
    FROM applications a
    JOIN users u ON a.user_id = u.id
    JOIN internships i ON a.internship_id = i.id
    LEFT JOIN offer_letters ol ON a.id = ol.application_id
    WHERE a.id = $app_id
")->fetch_assoc();

if (!$application) {
    $_SESSION['error'] = 'Application not found';
    header("Location: view.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Details - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <style>
        :root {
            --primary-color: #667eea;
        }
        
        .detail-section {
            background: white;
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .section-title {
            font-size: 18px;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 20px;
            border-bottom: 2px solid #e0e0e0;
            padding-bottom: 10px;
        }
        
        .detail-row {
            display: grid;
            grid-template-columns: 200px 1fr;
            gap: 20px;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .detail-row:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        
        .detail-label {
            font-weight: 600;
            color: #333;
        }
        
        .detail-value {
            color: #6c757d;
        }
        
        .badge-status {
            padding: 8px 15px;
            border-radius: 20px;
            font-weight: 600;
        }
        
        .badge-pending {
            background-color: #ffc107;
            color: #333;
        }
        
        .badge-approved {
            background-color: #28a745;
            color: white;
        }
        
        .badge-rejected {
            background-color: #dc3545;
            color: white;
        }
    </style>
</head>
<body>
    <div style="max-width: 900px; margin: 30px auto; padding: 0 15px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
            <h2>Application Details</h2>
            <a href="view.php" class="btn btn-outline-primary">Back to Applications</a>
        </div>
        
        <!-- Applicant Information -->
        <div class="detail-section">
            <h5 class="section-title"><i class="bi bi-person"></i> Applicant Information</h5>
            
            <div class="detail-row">
                <div class="detail-label">Name</div>
                <div class="detail-value"><?= $application['full_name'] ?></div>
            </div>
            
            <div class="detail-row">
                <div class="detail-label">Email</div>
                <div class="detail-value"><?= $application['email'] ?></div>
            </div>
            
            <div class="detail-row">
                <div class="detail-label">Phone</div>
                <div class="detail-value"><?= $application['phone'] ?></div>
            </div>
            
            <div class="detail-row">
                <div class="detail-label">College</div>
                <div class="detail-value"><?= $application['college_name'] ?></div>
            </div>
            
            <div class="detail-row">
                <div class="detail-label">Degree</div>
                <div class="detail-value"><?= $application['degree'] ?></div>
            </div>
            
            <div class="detail-row">
                <div class="detail-label">CGPA</div>
                <div class="detail-value"><?= $application['cgpa'] ?></div>
            </div>
        </div>
        
        <!-- Internship Details -->
        <div class="detail-section">
            <h5 class="section-title"><i class="bi bi-briefcase"></i> Internship Details</h5>
            
            <div class="detail-row">
                <div class="detail-label">Position</div>
                <div class="detail-value"><?= $application['internship_title'] ?> - <?= $application['role'] ?></div>
            </div>
            
            <div class="detail-row">
                <div class="detail-label">Description</div>
                <div class="detail-value"><?= substr($application['internship_desc'], 0, 200) ?>...</div>
            </div>
        </div>
        
        <!-- Application Status -->
        <div class="detail-section">
            <h5 class="section-title"><i class="bi bi-file-earmark"></i> Application Status</h5>
            
            <div class="detail-row">
                <div class="detail-label">Status</div>
                <div class="detail-value">
                    <span class="badge-status badge-<?= strtolower($application['status']) ?>">
                        <?= $application['status'] ?>
                    </span>
                </div>
            </div>
            
            <div class="detail-row">
                <div class="detail-label">Applied On</div>
                <div class="detail-value"><?= date('d M Y H:i', strtotime($application['applied_at'])) ?></div>
            </div>
            
            <?php if ($application['payment_proof_path']): ?>
                <div class="detail-row">
                    <div class="detail-label">Payment Proof</div>
                    <div class="detail-value">
                        <a href="<?= APP_URL ?>/assets/uploads/<?= $application['payment_proof_path'] ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-download"></i> View File
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Offer Letter (if approved) -->
        <?php if ($application['offer_id']): ?>
            <div class="detail-section">
                <h5 class="section-title"><i class="bi bi-file-text"></i> Offer Letter</h5>
                
                <div class="detail-row">
                    <div class="detail-label">Offer ID</div>
                    <div class="detail-value"><?= $application['offer_id'] ?></div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Start Date</div>
                    <div class="detail-value"><?= date('d M Y', strtotime($application['start_date'])) ?></div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">End Date</div>
                    <div class="detail-value"><?= date('d M Y', strtotime($application['end_date'])) ?></div>
                </div>
            </div>
        <?php endif; ?>
        
        <!-- Additional Information -->
        <?php if ($application['cover_letter'] || $application['linkedin_profile'] || $application['github_profile']): ?>
            <div class="detail-section">
                <h5 class="section-title"><i class="bi bi-info-circle"></i> Additional Information</h5>
                
                <?php if ($application['cover_letter']): ?>
                    <div class="detail-row">
                        <div class="detail-label">Cover Letter</div>
                        <div class="detail-value"><?= $application['cover_letter'] ?></div>
                    </div>
                <?php endif; ?>
                
                <?php if ($application['linkedin_profile']): ?>
                    <div class="detail-row">
                        <div class="detail-label">LinkedIn</div>
                        <div class="detail-value">
                            <a href="<?= $application['linkedin_profile'] ?>" target="_blank">
                                <?= $application['linkedin_profile'] ?>
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
                
                <?php if ($application['github_profile']): ?>
                    <div class="detail-row">
                        <div class="detail-label">GitHub</div>
                        <div class="detail-value">
                            <a href="<?= $application['github_profile'] ?>" target="_blank">
                                <?= $application['github_profile'] ?>
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        
        <!-- Action Buttons -->
        <?php if ($application['status'] === 'Pending'): ?>
            <div class="detail-section">
                <div style="display: flex; gap: 10px;">
                    <button class="btn btn-success" onclick="document.getElementById('approveForm').submit()">
                        <i class="bi bi-check-circle"></i> Approve Application
                    </button>
                    <button class="btn btn-danger" onclick="alert('Implement rejection')">
                        <i class="bi bi-x-circle"></i> Reject Application
                    </button>
                </div>
            </div>
            
            <form id="approveForm" method="POST" action="view.php" style="display: none;">
                <input type="hidden" name="action" value="approve">
                <input type="hidden" name="application_id" value="<?= $app_id ?>">
            </form>
        <?php endif; ?>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
