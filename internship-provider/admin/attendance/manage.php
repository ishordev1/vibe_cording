<?php
/**
 * Admin - Mark Attendance
 * Mark attendance for approved interns
 */

require_once '../../includes/config.php';
require_once '../../includes/session.php';

requireLogin();
requireAdmin();

$admin_id = $_SESSION['user_id'];
$success = '';
$error = '';

// Handle attendance submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_attendance'])) {
    $app_id = intval($_POST['application_id'] ?? 0);
    $attendance_date = $_POST['attendance_date'] ?? '';
    $status = sanitizeInput($_POST['status'] ?? '');
    $notes = sanitizeInput($_POST['notes'] ?? '');
    
    // Get user_id from application
    $app = $conn->query("SELECT user_id FROM applications WHERE id = $app_id")->fetch_assoc();
    
    if (!$app) {
        $error = 'Invalid application';
    } else {
        $user_id = $app['user_id'];
        
        // Check if attendance already exists
        $existing = $conn->query("SELECT id FROM attendance WHERE user_id = $user_id AND attendance_date = '$attendance_date'");
        
        if ($existing->num_rows > 0) {
            // Update existing
            $update = $conn->prepare("UPDATE attendance SET status = ?, notes = ? WHERE user_id = ? AND attendance_date = ?");
            $update->bind_param("ssds", $status, $notes, $user_id, $attendance_date);
            if ($update->execute()) {
                $success = 'Attendance updated successfully';
            }
            $update->close();
        } else {
            // Insert new
            $insert = $conn->prepare("INSERT INTO attendance (user_id, application_id, attendance_date, status, marked_by, notes) 
            VALUES (?, ?, ?, ?, ?, ?)");
            
            $insert->bind_param("iissss", $user_id, $app_id, $attendance_date, $status, $admin_id, $notes);
            if ($insert->execute()) {
                $success = 'Attendance marked successfully';
            }
            $insert->close();
        }
    }
}

// Get all approved interns
$interns = $conn->query("
    SELECT a.id, a.user_id, u.full_name, u.email, i.title, i.role,
           (SELECT COUNT(*) FROM attendance WHERE user_id = u.id AND status = 'Present') as present_count,
           (SELECT COUNT(*) FROM attendance WHERE user_id = u.id) as total_attendance
    FROM applications a
    JOIN users u ON a.user_id = u.id
    JOIN internships i ON a.internship_id = i.id
    WHERE a.status = 'Approved'
    ORDER BY u.full_name
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mark Attendance - Admin</title>
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
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .intern-info h6 {
            color: var(--primary-color);
            margin-bottom: 5px;
        }
        
        .intern-info p {
            margin: 0;
            font-size: 14px;
            color: #6c757d;
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
                    <h2 class="mb-4">Mark Attendance</h2>
        
        <?php if ($success): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?= $success ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <?= $error ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <!-- Interns List -->
        <?php if ($interns->num_rows === 0): ?>
            <div class="alert alert-info">
                No approved interns found
            </div>
        <?php else: ?>
            <?php while ($intern = $interns->fetch_assoc()): ?>
                <div class="intern-card">
                    <div class="intern-info">
                        <h6><?= $intern['full_name'] ?></h6>
                        <p><strong><?= $intern['title'] ?></strong> | <?= $intern['role'] ?></p>
                        <p><?= $intern['email'] ?></p>
                        <p><strong>Attendance:</strong> <?= $intern['present_count'] ?> / <?= $intern['total_attendance'] ?> Present</p>
                    </div>
                    <button class="btn btn-primary" onclick="markAttendance(<?= $intern['id'] ?>, '<?= addslashes($intern['full_name']) ?>')">
                        <i class="bi bi-calendar-check"></i> Mark Today
                    </button>
                </div>
            <?php endwhile; ?>
        <?php endif; ?>
    </div>
    
    <!-- Attendance Modal -->
    <div class="modal fade" id="attendanceModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Mark Attendance</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="mark_attendance" value="1">
                        <input type="hidden" name="application_id" id="app_id">
                        
                        <p><strong>Intern:</strong> <span id="intern_name"></span></p>
                        
                        <div class="mb-3">
                            <label class="form-label">Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="attendance_date" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-control" name="status" required>
                                <option value="">Select Status</option>
                                <option value="Present">Present</option>
                                <option value="Absent">Absent</option>
                                <option value="Late">Late</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Notes</label>
                            <textarea class="form-control" name="notes" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Mark Attendance</button>
                    </div>
                </form>
            </div>
        </div>
                </div>
            </main>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function markAttendance(appId, internName) {
            document.getElementById('app_id').value = appId;
            document.getElementById('intern_name').textContent = internName;
            document.querySelector('input[name="attendance_date"]').valueAsDate = new Date();
            new bootstrap.Modal(document.getElementById('attendanceModal')).show();
        }
    </script>
</body>
</html>
