<?php
/**
 * Admin Dashboard
 * Overview of all admin functions
 */

require_once '../includes/config.php';
require_once '../includes/session.php';

requireLogin();
requireAdmin();

// Get statistics
$stats = [];

// Total internships
$result = $conn->query("SELECT COUNT(*) as count FROM internships");
$stats['total_internships'] = $result->fetch_assoc()['count'];

// Total applications
$result = $conn->query("SELECT COUNT(*) as count FROM applications");
$stats['total_applications'] = $result->fetch_assoc()['count'];

// Pending applications
$result = $conn->query("SELECT COUNT(*) as count FROM applications WHERE status = 'Pending'");
$stats['pending_applications'] = $result->fetch_assoc()['count'];

// Approved interns
$result = $conn->query("SELECT COUNT(*) as count FROM applications WHERE status = 'Approved'");
$stats['approved_interns'] = $result->fetch_assoc()['count'];

// Recent applications
$recent_apps = $conn->query("
    SELECT a.id, u.full_name, i.title, a.applied_at, a.status
    FROM applications a
    JOIN users u ON a.user_id = u.id
    JOIN internships i ON a.internship_id = i.id
    ORDER BY a.applied_at DESC
    LIMIT 5
");

// Total interns registered
$result = $conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'intern'");
$stats['total_interns'] = $result->fetch_assoc()['count'];

// Get admin info
$admin = getCurrentUser();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Internship Platform</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        :root {
            --primary-color: #667eea;
            --secondary-color: #764ba2;
        }
        
        body {
            background-color: #f8f9fa;
        }
        
        .sidebar {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            min-height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            padding-top: 20px;
            z-index: 1000;
        }
        
        .sidebar .brand {
            color: white;
            font-size: 24px;
            font-weight: 700;
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.2);
        }
        
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 15px 20px;
            border-left: 4px solid transparent;
            transition: all 0.3s;
            text-decoration: none;
            display: block;
        }
        
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background-color: rgba(255,255,255,0.1);
            border-left-color: white;
            color: white;
        }
        
        .main-content {
            margin-left: 250px;
            padding: 30px;
        }
        
        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-left: 5px solid var(--primary-color);
        }
        
        .stat-card h6 {
            color: #6c757d;
            font-size: 14px;
            margin-bottom: 10px;
        }
        
        .stat-card .number {
            font-size: 32px;
            font-weight: 700;
            color: var(--primary-color);
        }
        
        .stat-card.pending {
            border-left-color: #ffc107;
        }
        
        .stat-card.pending .number {
            color: #ffc107;
        }
        
        .stat-card.success {
            border-left-color: #28a745;
        }
        
        .stat-card.success .number {
            color: #28a745;
        }
        
        .table-responsive {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .header-top {
            background: white;
            padding: 20px 30px;
            border-bottom: 1px solid #e0e0e0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-left: 250px;
        }
        
        .header-top .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--primary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
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
        
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }
            .main-content, .header-top {
                margin-left: 0;
            }
            .sidebar .nav-link {
                display: inline-block;
                width: auto;
                padding: 10px 15px;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="brand">
            🎓 InternHub
        </div>
        <nav>
            <a href="dashboard.php" class="nav-link active">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
            <a href="internships/manage.php" class="nav-link">
                <i class="bi bi-briefcase"></i> Manage Internships
            </a>
            <a href="applications/view.php" class="nav-link">
                <i class="bi bi-file-earmark"></i> Applications
            </a>
            <a href="tasks/manage.php" class="nav-link">
                <i class="bi bi-tasks"></i> Tasks & Levels
            </a>
            <a href="applications/review.php" class="nav-link">
                <i class="bi bi-check-circle"></i> Review Submissions
            </a>
            <a href="attendance/manage.php" class="nav-link">
                <i class="bi bi-calendar-check"></i> Mark Attendance
            </a>
            <a href="certificates/generate.php" class="nav-link">
                <i class="bi bi-award"></i> Generate Certificates
            </a>
            <hr style="border-color: rgba(255,255,255,0.2); margin: 20px 0;">
            <a href="../auth/logout.php" class="nav-link">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a>
        </nav>
    </div>
    
    <!-- Header -->
    <div class="header-top">
        <h3>Admin Dashboard</h3>
        <div class="user-info">
            <div class="user-avatar"><?= strtoupper(substr($admin['full_name'], 0, 1)) ?></div>
            <div>
                <div><?= $admin['full_name'] ?></div>
                <small class="text-muted"><?= $admin['role'] ?></small>
            </div>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="main-content">
        <h2 class="mb-4">Welcome, <?= $admin['full_name'] ?>!</h2>
        
        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stat-card">
                    <h6>Total Interns</h6>
                    <div class="number"><?= $stats['total_interns'] ?></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <h6>Total Internships</h6>
                    <div class="number"><?= $stats['total_internships'] ?></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card pending">
                    <h6>Pending Applications</h6>
                    <div class="number"><?= $stats['pending_applications'] ?></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card success">
                    <h6>Approved Interns</h6>
                    <div class="number"><?= $stats['approved_interns'] ?></div>
                </div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                    <h5 class="mb-3">Quick Actions</h5>
                    <div class="row">
                        <div class="col-md-3">
                            <a href="internships/create.php" class="btn btn-outline-primary w-100">
                                <i class="bi bi-plus-circle"></i> Create Internship
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="applications/view.php" class="btn btn-outline-primary w-100">
                                <i class="bi bi-envelope"></i> Review Applications
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="tasks/manage.php" class="btn btn-outline-primary w-100">
                                <i class="bi bi-plus"></i> Add Task
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="attendance/manage.php" class="btn btn-outline-primary w-100">
                                <i class="bi bi-clipboard-check"></i> Mark Attendance
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Recent Applications -->
        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <div style="padding: 20px;">
                        <h5 class="mb-3">Recent Applications</h5>
                        <table class="table table-hover">
                            <thead style="background-color: #f8f9fa;">
                                <tr>
                                    <th>Intern Name</th>
                                    <th>Internship</th>
                                    <th>Applied On</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($app = $recent_apps->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= $app['full_name'] ?></td>
                                        <td><?= $app['title'] ?></td>
                                        <td><?= date('d M Y', strtotime($app['applied_at'])) ?></td>
                                        <td>
                                            <span class="badge badge-<?= strtolower($app['status']) ?>">
                                                <?= $app['status'] ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="applications/view_detail.php?id=<?= $app['id'] ?>" class="btn btn-sm btn-primary">
                                                View
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
