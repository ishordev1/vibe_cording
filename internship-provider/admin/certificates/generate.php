<?php
/**
 * Admin - Generate Certificates
 * Generate certificates for completed interns
 */

require_once '../../includes/config.php';
require_once '../../includes/session.php';

requireLogin();
requireAdmin();

$admin_id = $_SESSION['user_id'];

// Handle certificate generation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['generate_cert'])) {
    $app_id = intval($_POST['application_id'] ?? 0);
    
    // Get application and intern details
    $app = $conn->query("
        SELECT a.user_id, u.full_name, i.title as internship_role, ol.duration_months
        FROM applications a
        JOIN users u ON a.user_id = u.id
        JOIN internships i ON a.internship_id = i.id
        JOIN offer_letters ol ON a.id = ol.application_id
        WHERE a.id = $app_id AND a.status = 'Approved'
    ")->fetch_assoc();
    
    if (!$app) {
        $_SESSION['error'] = 'Invalid application';
    } else {
        // Check if all levels are completed
        $all_levels = $conn->query("SELECT COUNT(*) as total FROM levels")->fetch_assoc()['total'];
        $completed = $conn->query("SELECT COUNT(*) as total FROM level_completion WHERE user_id = {$app['user_id']}")->fetch_assoc()['total'];
        
        if ($completed < $all_levels) {
            $_SESSION['error'] = 'All levels must be completed before generating certificate';
        } else {
            // Check if certificate already exists
            $existing = $conn->query("SELECT id FROM certificates WHERE application_id = $app_id");
            
            if ($existing->num_rows > 0) {
                $_SESSION['error'] = 'Certificate already generated for this internship';
            } else {
                // Generate certificate
                $certificate_id = 'CERT-' . strtoupper(substr(md5(time()), 0, 10));
                $issue_date = date('Y-m-d');
                
                $insert = $conn->prepare("INSERT INTO certificates (user_id, application_id, certificate_id, internship_role, duration_months, issue_date, verified) 
                VALUES (?, ?, ?, ?, ?, ?, 1)");
                
                $insert->bind_param("iisssi", $app['user_id'], $app_id, $certificate_id, $app['internship_role'], $app['duration_months'], $issue_date);
                
                if ($insert->execute()) {
                    $_SESSION['success'] = 'Certificate generated successfully (ID: ' . $certificate_id . ')';
                } else {
                    $_SESSION['error'] = 'Error generating certificate';
                }
                $insert->close();
            }
        }
    }
    
    header("Location: generate.php");
    exit();
}

// Get all approved applications
$applications = $conn->query("
    SELECT a.id, a.user_id, u.full_name, i.title, i.role,
           (SELECT COUNT(*) FROM certificates WHERE application_id = a.id) as cert_exists,
           (SELECT COUNT(*) FROM levels) as total_levels,
           (SELECT COUNT(*) FROM level_completion WHERE user_id = u.id) as completed_levels
    FROM applications a
    JOIN users u ON a.user_id = u.id
    JOIN internships i ON a.internship_id = i.id
    WHERE a.status = 'Approved'
    ORDER BY a.applied_at DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate Certificates - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <style>
        :root {
            --primary-color: #667eea;
        }
        
        body {
            background-color: #f8f9fa;
        }
        
        .container-custom {
            max-width: 1000px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 30px;
            margin: 30px auto;
        }
        
        .intern-card {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 15px;
        }
        
        .progress-badge {
            display: inline-block;
            background: #e9ecef;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .progress-badge.complete {
            background: #d4edda;
            color: #155724;
        }
        
        .progress-badge.incomplete {
            background: #fff3cd;
            color: #856404;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php require_once '../sidebar.php'; ?>
            
            <!-- Main Content -->
            <main class="col-md-10 ms-sm-auto px-md-4">
                <div class="container-custom" style="margin: 30px auto; max-width: none;">
                    <h2 class="mb-4">Generate Certificates</h2>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?= $_SESSION['success'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <?= $_SESSION['error'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
        
        <!-- Applications List -->
        <?php if ($applications->num_rows === 0): ?>
            <div class="alert alert-info">
                No approved internships available for certificate generation
            </div>
        <?php else: ?>
            <?php while ($app = $applications->fetch_assoc()): ?>
                <div class="intern-card">
                    <div class="d-flex justify-content-between align-items-start">
                        <div style="flex: 1;">
                            <h5><?= $app['full_name'] ?></h5>
                            <p class="text-muted mb-2">
                                <strong><?= $app['title'] ?></strong> | Role: <?= $app['role'] ?>
                            </p>
                            <p class="mb-0">
                                <span class="progress-badge <?= $app['completed_levels'] >= $app['total_levels'] ? 'complete' : 'incomplete' ?>">
                                    Levels: <?= $app['completed_levels'] ?>/<?= $app['total_levels'] ?> Completed
                                </span>
                                <?php if ($app['cert_exists']): ?>
                                    <span class="badge bg-success" style="margin-left: 10px;">
                                        <i class="bi bi-check-circle"></i> Certificate Generated
                                    </span>
                                <?php endif; ?>
                            </p>
                        </div>
                        <div>
                            <?php if ($app['cert_exists']): ?>
                                <button class="btn btn-sm btn-outline-secondary" disabled>
                                    <i class="bi bi-check"></i> Generated
                                </button>
                            <?php elseif ($app['completed_levels'] >= $app['total_levels']): ?>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="generate_cert" value="1">
                                    <input type="hidden" name="application_id" value="<?= $app['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Generate certificate?')">
                                        <i class="bi bi-award"></i> Generate
                                    </button>
                                </form>
                            <?php else: ?>
                                <button class="btn btn-sm btn-outline-secondary" disabled>
                                    Levels Pending
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php endif; ?>
                </div>
            </main>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
