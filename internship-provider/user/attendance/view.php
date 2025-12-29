<?php
/**
 * User - View Attendance
 * View attendance records
 */

require_once '../../includes/config.php';
require_once '../../includes/session.php';

requireLogin();
requireIntern();

$user_id = $_SESSION['user_id'];

// Get active internship
$active_app = $conn->query("
    SELECT a.id FROM applications a 
    WHERE a.user_id = $user_id AND a.status = 'Approved' 
    LIMIT 1
")->fetch_assoc();

if (!$active_app) {
    $_SESSION['error'] = 'You need an approved internship to view attendance';
    header("Location: ../dashboard.php");
    exit();
}

// Get attendance records
$attendance = $conn->query("
    SELECT a.attendance_date, a.status, a.notes, u.full_name as marked_by
    FROM attendance a
    JOIN users u ON a.marked_by = u.id
    WHERE a.user_id = $user_id
    ORDER BY a.attendance_date DESC
");

// Get attendance stats
$total = $conn->query("SELECT COUNT(*) as count FROM attendance WHERE user_id = $user_id")->fetch_assoc()['count'];
$present = $conn->query("SELECT COUNT(*) as count FROM attendance WHERE user_id = $user_id AND status = 'Present'")->fetch_assoc()['count'];
$absent = $conn->query("SELECT COUNT(*) as count FROM attendance WHERE user_id = $user_id AND status = 'Absent'")->fetch_assoc()['count'];
$late = $conn->query("SELECT COUNT(*) as count FROM attendance WHERE user_id = $user_id AND status = 'Late'")->fetch_assoc()['count'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance - Internship Platform</title>
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
        
        .stat-box {
            background: white;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .stat-box .number {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .stat-box.present .number {
            color: #28a745;
        }
        
        .stat-box.absent .number {
            color: #dc3545;
        }
        
        .stat-box.late .number {
            color: #ffc107;
        }
        
        .stat-box .label {
            color: #6c757d;
            font-size: 14px;
        }
        
        .attendance-table {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .attendance-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .attendance-row:last-child {
            border-bottom: none;
        }
        
        .date {
            font-weight: 600;
            color: #333;
        }
        
        .badge-present {
            background-color: #d4edda;
            color: #155724;
            padding: 5px 12px;
            border-radius: 20px;
            font-weight: 500;
        }
        
        .badge-absent {
            background-color: #f8d7da;
            color: #721c24;
            padding: 5px 12px;
            border-radius: 20px;
            font-weight: 500;
        }
        
        .badge-late {
            background-color: #fff3cd;
            color: #856404;
            padding: 5px 12px;
            border-radius: 20px;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <div class="navbar-custom">
        <div class="d-flex justify-content-between align-items-center">
            <div class="brand"><i class="bi bi-calendar-check"></i> Attendance</div>
            <div>
                <a href="../dashboard.php">Dashboard</a>
                <a href="../../auth/logout.php">Logout</a>
            </div>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="container-custom">
        <h2 class="mb-4">My Attendance Record</h2>
        
        <!-- Statistics -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stat-box">
                    <div class="number"><?= $total ?></div>
                    <div class="label">Total Days</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-box present">
                    <div class="number"><?= $present ?></div>
                    <div class="label">Present</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-box absent">
                    <div class="number"><?= $absent ?></div>
                    <div class="label">Absent</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-box late">
                    <div class="number"><?= $late ?></div>
                    <div class="label">Late</div>
                </div>
            </div>
        </div>
        
        <!-- Attendance Table -->
        <div class="attendance-table">
            <div style="padding: 20px; background: #f8f9fa; border-bottom: 1px solid #e0e0e0;">
                <h5 class="mb-0">Attendance Details</h5>
            </div>
            
            <?php if ($attendance->num_rows === 0): ?>
                <div style="padding: 40px 20px; text-align: center; color: #6c757d;">
                    <i class="bi bi-inbox" style="font-size: 40px; display: block; margin-bottom: 15px;"></i>
                    No attendance records yet
                </div>
            <?php else: ?>
                <?php while ($record = $attendance->fetch_assoc()): ?>
                    <div class="attendance-row">
                        <div>
                            <div class="date"><?= date('d M Y', strtotime($record['attendance_date'])) ?></div>
                            <small class="text-muted"><?= $record['marked_by'] ?></small>
                            <?php if ($record['notes']): ?>
                                <div style="font-size: 12px; color: #6c757d; margin-top: 5px;">
                                    Note: <?= $record['notes'] ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div>
                            <span class="badge-<?= strtolower($record['status']) ?>">
                                <?= $record['status'] ?>
                            </span>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>
        
        <div class="mt-4">
            <a href="../dashboard.php" class="btn btn-primary">
                <i class="bi bi-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
