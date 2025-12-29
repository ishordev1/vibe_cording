<?php
/**
 * Intern Dashboard
 * Overview of applications, tasks, attendance, and certificates
 */

require_once '../includes/config.php';
require_once '../includes/session.php';

requireLogin();
requireIntern();

$user_id = $_SESSION['user_id'];

// Get internship applications
$applications = $conn->prepare("
    SELECT a.id, i.title, i.role, a.status, a.applied_at
    FROM applications a
    JOIN internships i ON a.internship_id = i.id
    WHERE a.user_id = ?
    ORDER BY a.applied_at DESC
");
$applications->bind_param("i", $user_id);
$applications->execute();
$apps_result = $applications->get_result();

// Get active internship (if approved)
$active_internship = $conn->query("
    SELECT a.id, i.title, i.role, ol.start_date, ol.end_date, ol.duration_months
    FROM applications a
    JOIN internships i ON a.internship_id = i.id
    LEFT JOIN offer_letters ol ON a.id = ol.application_id
    WHERE a.user_id = $user_id AND a.status = 'Approved'
    LIMIT 1
");

$active = $active_internship->fetch_assoc();

// Get completed tasks count
$completed_tasks = $conn->query("
    SELECT COUNT(*) as count FROM task_submissions 
    WHERE user_id = $user_id AND status = 'Approved'
")->fetch_assoc()['count'];

// Get pending submissions
$pending_submissions = $conn->query("
    SELECT COUNT(*) as count FROM task_submissions 
    WHERE user_id = $user_id AND (status = 'Submitted' OR status = 'Under Review')
")->fetch_assoc()['count'];

// Get certificate status
$certificate = $conn->query("
    SELECT c.id, c.certificate_id
    FROM certificates c
    JOIN applications a ON c.application_id = a.id
    WHERE a.user_id = $user_id
    LIMIT 1
")->fetch_assoc();

// Get current level
$current_level = $conn->query("
    SELECT l.level_number FROM level_completion lc
    JOIN levels l ON lc.level_id = l.id
    WHERE lc.user_id = $user_id
    ORDER BY l.level_number DESC
    LIMIT 1
")->fetch_assoc();

$user = getCurrentUser();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Internship Platform</title>
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
        
        .navbar-custom {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            padding: 15px 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .navbar-custom .brand {
            font-size: 24px;
            font-weight: 700;
            color: white;
        }
        
        .navbar-custom .nav-links {
            margin-left: auto;
            display: flex;
            gap: 20px;
        }
        
        .navbar-custom a {
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .container-custom {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 15px;
        }
        
        .welcome-section {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 30px;
        }
        
        .welcome-section h2 {
            font-weight: 700;
            margin-bottom: 10px;
        }
        
        .stat-box {
            background: white;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            border-left: 5px solid var(--primary-color);
        }
        
        .stat-box .number {
            font-size: 28px;
            font-weight: 700;
            color: var(--primary-color);
        }
        
        .stat-box .label {
            color: #6c757d;
            font-size: 14px;
        }
        
        .card-custom {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            overflow: hidden;
        }
        
        .card-custom .card-header {
            background-color: #f8f9fa;
            padding: 20px;
            border-bottom: 1px solid #e0e0e0;
            font-weight: 600;
        }
        
        .card-custom .card-body {
            padding: 20px;
        }
        
        .app-item {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .badge-pending {
            background-color: #ffc107;
            color: #333;
        }
        
        .badge-approved {
            background-color: #28a745;
        }
        
        .badge-rejected {
            background-color: #dc3545;
        }
        
        .btn-custom {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 500;
        }
        
        .btn-custom.primary {
            background-color: var(--primary-color);
            color: white;
        }
        
        .btn-custom.primary:hover {
            background-color: var(--secondary-color);
            color: white;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <div class="navbar-custom">
        <div class="d-flex justify-content-between align-items-center">
            <div class="brand">🎓 InternHub</div>
            <div class="nav-links">
                <a href="applications/browse.php">Browse Internships</a>
                <a href="tasks/view.php">Tasks</a>
                <a href="attendance/view.php">Attendance</a>
                <a href="certificates/view.php">Certificate</a>
                <a href="../auth/logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
            </div>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="container-custom">
        <!-- Welcome Section -->
        <div class="welcome-section">
            <h2>Welcome back, <?= $user['full_name'] ?>! 👋</h2>
            <p class="mb-0">Your internship journey starts here. Explore opportunities, complete tasks, and grow your skills.</p>
        </div>
        
        <!-- Statistics -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stat-box">
                    <div class="number"><?= $apps_result->num_rows ?></div>
                    <div class="label">Applications</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-box">
                    <div class="number"><?= $completed_tasks ?></div>
                    <div class="label">Tasks Completed</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-box">
                    <div class="number"><?= $current_level ? $current_level['level_number'] : '-' ?></div>
                    <div class="label">Current Level</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-box">
                    <div class="number"><?= $certificate ? '✓' : '-' ?></div>
                    <div class="label">Certificate</div>
                </div>
            </div>
        </div>
        
        <!-- Active Internship (if approved) -->
        <?php if ($active): ?>
            <div class="card-custom">
                <div class="card-header">
                    <i class="bi bi-briefcase"></i> Active Internship
                </div>
                <div class="card-body">
                    <h5><?= $active['title'] ?></h5>
                    <p class="text-muted mb-3">Position: <?= $active['role'] ?></p>
                    <div class="row">
                        <div class="col-md-4">
                            <strong>Duration:</strong> <?= $active['duration_months'] ?> months
                        </div>
                        <div class="col-md-4">
                            <strong>Start Date:</strong> <?= date('d M Y', strtotime($active['start_date'])) ?>
                        </div>
                        <div class="col-md-4">
                            <strong>End Date:</strong> <?= date('d M Y', strtotime($active['end_date'])) ?>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="tasks/view.php" class="btn btn-custom primary">
                            <i class="bi bi-list-check"></i> View Tasks
                        </a>
                        <a href="attendance/view.php" class="btn btn-custom primary" style="background-color: #6c757d;">
                            <i class="bi bi-calendar"></i> View Attendance
                        </a>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-info" role="alert">
                <i class="bi bi-info-circle"></i> No active internship. 
                <a href="applications/browse.php">Browse and apply for internships</a>
            </div>
        <?php endif; ?>
        
        <!-- Recent Applications -->
        <div class="card-custom">
            <div class="card-header">
                <i class="bi bi-file-earmark"></i> Recent Applications
            </div>
            <div class="card-body">
                <?php 
                $apps_result->data_seek(0);
                if ($apps_result->num_rows === 0):
                ?>
                    <p class="text-muted">No applications yet. <a href="applications/browse.php">Browse internships</a></p>
                <?php else: ?>
                    <?php while ($app = $apps_result->fetch_assoc()): ?>
                        <div class="app-item">
                            <div>
                                <h6 class="mb-1"><?= $app['title'] ?></h6>
                                <small class="text-muted">Applied on <?= date('d M Y', strtotime($app['applied_at'])) ?></small>
                            </div>
                            <div>
                                <span class="badge badge-<?= strtolower($app['status']) ?>">
                                    <?= $app['status'] ?>
                                </span>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Quick Links -->
        <div class="card-custom">
            <div class="card-header">
                <i class="bi bi-lightning"></i> Quick Actions
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-3">
                        <a href="applications/browse.php" class="btn btn-custom primary w-100">
                            <i class="bi bi-search"></i> Browse Internships
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="tasks/view.php" class="btn btn-custom primary w-100" style="background-color: #17a2b8;">
                            <i class="bi bi-tasks"></i> My Tasks
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="attendance/view.php" class="btn btn-custom primary w-100" style="background-color: #6f42c1;">
                            <i class="bi bi-calendar-check"></i> Attendance
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="certificates/view.php" class="btn btn-custom primary w-100" style="background-color: #e83e8c;">
                            <i class="bi bi-award"></i> Certificate
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
