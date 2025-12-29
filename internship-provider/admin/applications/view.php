<?php
/**
 * Admin - Review Applications
 * View, approve, reject applications and manage payment verification
 */

require_once '../../includes/config.php';
require_once '../../includes/session.php';

requireLogin();
requireAdmin();

$admin_id = $_SESSION['user_id'];

// Handle approval/rejection
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $app_id = intval($_POST['application_id'] ?? 0);
    
    if ($_POST['action'] === 'approve') {
        // Get application details
        $app = $conn->query("
            SELECT a.*, u.full_name, i.title, i.role
            FROM applications a
            JOIN users u ON a.user_id = u.id
            JOIN internships i ON a.internship_id = i.id
            WHERE a.id = $app_id
        ")->fetch_assoc();
        
        // Approve application
        $update = $conn->prepare("UPDATE applications SET status = 'Approved', reviewed_by = ?, reviewed_at = NOW() WHERE id = ?");
        $update->bind_param("ii", $admin_id, $app_id);
        if ($update->execute()) {
            // Generate Offer Letter
            $offer_id = 'OFFER-' . strtoupper(substr(md5(time()), 0, 8));
            $start_date = date('Y-m-d');
            $duration_months = rand(1, 4); // Can be customized
            $end_date = date('Y-m-d', strtotime("+$duration_months months"));
            
            $insert_offer = $conn->prepare("INSERT INTO offer_letters (application_id, offer_id, user_id, internship_id, start_date, end_date, duration_months) 
            VALUES (?, ?, ?, ?, ?, ?, ?)");
            
            $insert_offer->bind_param("isiissii", $app_id, $offer_id, $app['user_id'], $app['internship_id'], $start_date, $end_date, $duration_months);
            if ($insert_offer->execute()) {
                $_SESSION['success'] = 'Application approved and Offer Letter generated!';
            }
            $insert_offer->close();
        }
        $update->close();
    } 
    elseif ($_POST['action'] === 'reject') {
        $rejection_reason = sanitizeInput($_POST['rejection_reason'] ?? '');
        $update = $conn->prepare("UPDATE applications SET status = 'Rejected', reviewed_by = ?, reviewed_at = NOW(), rejection_reason = ? WHERE id = ?");
        $update->bind_param("isi", $admin_id, $rejection_reason, $app_id);
        if ($update->execute()) {
            $_SESSION['success'] = 'Application rejected';
        }
        $update->close();
    }
    
    header("Location: view.php");
    exit();
}

// Get filter option
$status_filter = $_GET['status'] ?? 'all';

// Get applications
if ($status_filter === 'all') {
    $applications = $conn->query("
        SELECT a.id, a.status, a.applied_at, a.payment_verified,
               u.full_name, u.email, u.phone,
               i.title, i.role
        FROM applications a
        JOIN users u ON a.user_id = u.id
        JOIN internships i ON a.internship_id = i.id
        ORDER BY a.applied_at DESC
    ");
} else {
    $applications = $conn->prepare("
        SELECT a.id, a.status, a.applied_at, a.payment_verified,
               u.full_name, u.email, u.phone,
               i.title, i.role
        FROM applications a
        JOIN users u ON a.user_id = u.id
        JOIN internships i ON a.internship_id = i.id
        WHERE a.status = ?
        ORDER BY a.applied_at DESC
    ");
    $applications->bind_param("s", $status_filter);
    $applications->execute();
    $applications = $applications->get_result();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Applications - Admin</title>
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
            max-width: 1200px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 30px;
            margin: 30px auto;
        }
        
        .filter-tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 30px;
            border-bottom: 2px solid #e0e0e0;
        }
        
        .filter-tabs a {
            padding: 10px 20px;
            text-decoration: none;
            color: #6c757d;
            border-bottom: 3px solid transparent;
            transition: all 0.3s;
        }
        
        .filter-tabs a.active {
            color: var(--primary-color);
            border-bottom-color: var(--primary-color);
        }
        
        .app-row {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .app-info h6 {
            color: var(--primary-color);
            margin-bottom: 5px;
        }
        
        .badge-pending {
            background-color: #ffc107;
        }
        
        .badge-approved {
            background-color: #28a745;
        }
        
        .badge-rejected {
            background-color: #dc3545;
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
        <h2 class="mb-4">Internship Applications</h2>
        
        <!-- Messages -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?= $_SESSION['success'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        
        <!-- Filter Tabs -->
        <div class="filter-tabs">
            <a href="view.php?status=all" class="<?= $status_filter === 'all' ? 'active' : '' ?>">
                All Applications
            </a>
            <a href="view.php?status=Pending" class="<?= $status_filter === 'Pending' ? 'active' : '' ?>">
                <i class="bi bi-clock"></i> Pending
            </a>
            <a href="view.php?status=Approved" class="<?= $status_filter === 'Approved' ? 'active' : '' ?>">
                <i class="bi bi-check-circle"></i> Approved
            </a>
            <a href="view.php?status=Rejected" class="<?= $status_filter === 'Rejected' ? 'active' : '' ?>">
                <i class="bi bi-x-circle"></i> Rejected
            </a>
        </div>
        
        <!-- Applications List -->
        <?php if ($applications->num_rows === 0): ?>
            <div class="alert alert-info text-center">
                No applications found
            </div>
        <?php else: ?>
            <?php while ($app = $applications->fetch_assoc()): ?>
                <div class="app-row">
                    <div class="app-info">
                        <h6><?= $app['full_name'] ?></h6>
                        <p class="text-muted mb-2">
                            <strong>Internship:</strong> <?= $app['title'] ?> (<?= $app['role'] ?>)<br>
                            <strong>Email:</strong> <?= $app['email'] ?> | 
                            <strong>Phone:</strong> <?= $app['phone'] ?><br>
                            <strong>Applied:</strong> <?= date('d M Y H:i', strtotime($app['applied_at'])) ?>
                        </p>
                    </div>
                    <div class="text-end">
                        <div class="mb-2">
                            <span class="badge badge-<?= strtolower($app['status']) ?>">
                                <?= $app['status'] ?>
                            </span>
                            <?php if (!$app['payment_verified']): ?>
                                <span class="badge bg-danger">Payment Unverified</span>
                            <?php else: ?>
                                <span class="badge bg-success">Payment Verified</span>
                            <?php endif; ?>
                        </div>
                        <button class="btn btn-sm btn-primary" onclick="viewApplication(<?= $app['id'] ?>)">
                            <i class="bi bi-eye"></i> View
                        </button>
                        <?php if ($app['status'] === 'Pending'): ?>
                            <button class="btn btn-sm btn-success" onclick="approveApp(<?= $app['id'] ?>)">
                                <i class="bi bi-check"></i> Approve
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="rejectApp(<?= $app['id'] ?>)">
                                <i class="bi bi-x"></i> Reject
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php endif; ?>
    </div>
    
    <!-- Approval Modal -->
    <div class="modal fade" id="approveModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Approve Application</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="approve">
                        <input type="hidden" name="application_id" id="approve_app_id">
                        <p>Approve this application? An Offer Letter will be generated automatically.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Approve</button>
                    </div>
                </form>
            </div>
        </div>
                </div>
            </main>
        </div>
    </div>
    
    <!-- Rejection Modal -->
    <div class="modal fade" id="rejectModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Reject Application</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="reject">
                        <input type="hidden" name="application_id" id="reject_app_id">
                        <div class="mb-3">
                            <label class="form-label">Rejection Reason</label>
                            <textarea class="form-control" name="rejection_reason" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Reject</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function approveApp(appId) {
            document.getElementById('approve_app_id').value = appId;
            new bootstrap.Modal(document.getElementById('approveModal')).show();
        }
        
        function rejectApp(appId) {
            document.getElementById('reject_app_id').value = appId;
            new bootstrap.Modal(document.getElementById('rejectModal')).show();
        }
        
        function viewApplication(appId) {
            window.location.href = 'view_detail.php?id=' + appId;
        }
    </script>
</body>
</html>
