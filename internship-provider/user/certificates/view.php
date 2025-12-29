<?php
/**
 * User - View Certificate
 * View and download generated certificates
 */

require_once '../../includes/config.php';
require_once '../../includes/session.php';

requireLogin();
requireIntern();

$user_id = $_SESSION['user_id'];

// Get certificate
$certificate = $conn->query("
    SELECT c.*, a.id as app_id, i.title, i.role
    FROM certificates c
    JOIN applications a ON c.application_id = a.id
    JOIN internships i ON a.internship_id = i.id
    WHERE c.user_id = $user_id
    ORDER BY c.issue_date DESC
    LIMIT 1
")->fetch_assoc();

// Get user details
$user = getCurrentUser();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate - Internship Platform</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <style>
        :root {
            --primary-color: #667eea;
            --secondary-color: #764ba2;
        }
        
        body {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            min-height: 100vh;
        }
        
        .container-custom {
            max-width: 900px;
            margin: 40px auto;
            padding: 0 15px;
        }
        
        .certificate-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            overflow: hidden;
        }
        
        .certificate-header {
            background: linear-gradient(135deg, #d4af37 0%, #f0e68c 100%);
            padding: 30px;
            text-align: center;
            color: #2c1810;
        }
        
        .certificate-header h1 {
            font-weight: 700;
            font-size: 48px;
            margin: 0;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
        }
        
        .certificate-body {
            padding: 60px 40px;
            text-align: center;
            min-height: 500px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            border: 3px solid #d4af37;
            margin: 20px;
        }
        
        .certificate-body h2 {
            font-size: 36px;
            color: #2c1810;
            margin-bottom: 30px;
            font-weight: 700;
        }
        
        .cert-text {
            font-size: 18px;
            color: #333;
            margin: 20px 0;
            font-style: italic;
        }
        
        .cert-details {
            margin-top: 40px;
            font-size: 16px;
            color: #555;
        }
        
        .cert-detail-row {
            margin: 15px 0;
        }
        
        .cert-detail-label {
            font-weight: 600;
            color: #2c1810;
        }
        
        .cert-signature {
            margin-top: 60px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 80px;
            padding: 0 80px;
        }
        
        .cert-sig-line {
            border-top: 2px solid #333;
            padding-top: 10px;
            text-align: center;
            font-size: 14px;
        }
        
        .cert-id {
            font-size: 12px;
            color: #999;
            margin-top: 20px;
            text-align: center;
        }
        
        .certificate-footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-top: 1px solid #e0e0e0;
        }
        
        .action-buttons {
            margin-top: 30px;
            display: flex;
            gap: 10px;
            justify-content: center;
        }
        
        .btn-custom {
            padding: 12px 30px;
            font-weight: 600;
            border-radius: 5px;
        }
        
        .no-certificate {
            background: white;
            border-radius: 10px;
            padding: 60px 40px;
            text-align: center;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        
        .no-certificate i {
            font-size: 60px;
            color: #667eea;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container-custom">
        <?php if ($certificate): ?>
            <div class="certificate-container">
                <!-- Certificate Header -->
                <div class="certificate-header">
                    <h1>🎓 CERTIFICATE OF COMPLETION</h1>
                    <p style="font-size: 14px; margin: 10px 0 0 0;">Internship Platform</p>
                </div>
                
                <!-- Certificate Body -->
                <div class="certificate-body">
                    <h2><?= $user['full_name'] ?></h2>
                    
                    <p class="cert-text">
                        "This is to certify that"
                    </p>
                    
                    <p style="font-size: 20px; color: #2c1810; font-weight: 600;">
                        Successfully completed an internship program
                    </p>
                    
                    <p class="cert-text">
                        "in the field of"
                    </p>
                    
                    <h3 style="color: #2c1810; margin: 20px 0;"><?= $certificate['internship_role'] ?></h3>
                    
                    <div class="cert-details">
                        <div class="cert-detail-row">
                            <span class="cert-detail-label">Duration:</span> <?= $certificate['duration_months'] ?> months
                        </div>
                        <div class="cert-detail-row">
                            <span class="cert-detail-label">Issue Date:</span> <?= date('d F Y', strtotime($certificate['issue_date'])) ?>
                        </div>
                    </div>
                    
                    <div class="cert-signature">
                        <div class="cert-sig-line">
                            Authorized By<br>
                            <small>Internship Platform</small>
                        </div>
                        <div class="cert-sig-line">
                            <?= date('d/m/Y') ?><br>
                            <small>Date</small>
                        </div>
                    </div>
                    
                    <div class="cert-id">
                        Certificate ID: <?= $certificate['certificate_id'] ?>
                    </div>
                </div>
                
                <!-- Certificate Footer -->
                <div class="certificate-footer">
                    <p class="mb-0" style="font-size: 12px; color: #666;">
                        This certificate verifies that the holder has successfully completed all required tasks and levels of the internship program.
                        Verify certificate at: <strong><?= APP_URL ?>/verify-certificate.php?id=<?= $certificate['certificate_id'] ?></strong>
                    </p>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="action-buttons">
                <button class="btn btn-custom btn-light" onclick="window.print()">
                    <i class="bi bi-printer"></i> Print
                </button>
                <a href="../dashboard.php" class="btn btn-custom btn-light">
                    <i class="bi bi-arrow-left"></i> Back to Dashboard
                </a>
            </div>
        <?php else: ?>
            <div class="no-certificate">
                <i class="bi bi-award"></i>
                <h3>Certificate Not Available</h3>
                <p class="text-muted" style="margin-bottom: 30px;">
                    Your certificate will be generated after you complete all levels of the internship.
                </p>
                <a href="../dashboard.php" class="btn btn-primary">
                    <i class="bi bi-arrow-left"></i> Back to Dashboard
                </a>
            </div>
        <?php endif; ?>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
