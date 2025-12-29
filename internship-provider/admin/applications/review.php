<?php
/**
 * Admin - Review Task Submissions
 * Review, approve, or request rework on task submissions
 */

require_once '../../includes/config.php';
require_once '../../includes/session.php';
require_once '../../includes/validation.php';

requireLogin();
requireAdmin();

$admin_id = $_SESSION['user_id'];

// Handle submission review
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $submission_id = intval($_POST['submission_id'] ?? 0);
    $feedback = sanitizeInput($_POST['feedback'] ?? '');
    
    if ($_POST['action'] === 'approve') {
        $update = $conn->prepare("UPDATE task_submissions SET status = 'Approved', reviewed_by = ?, reviewed_at = NOW(), admin_feedback = ? WHERE id = ?");
        $update->bind_param("isi", $admin_id, $feedback, $submission_id);
        if ($update->execute()) {
            // Check if all tasks in a level are approved, then mark level as complete
            $submission = $conn->query("SELECT t.level_id, ts.user_id FROM task_submissions ts JOIN tasks t ON ts.task_id = t.id WHERE ts.id = $submission_id")->fetch_assoc();
            
            // Check if all tasks in this level are approved
            $all_approved = $conn->query("
                SELECT COUNT(*) as count FROM tasks t
                WHERE t.level_id = {$submission['level_id']}
                AND t.id NOT IN (
                    SELECT task_id FROM task_submissions WHERE user_id = {$submission['user_id']} AND status = 'Approved'
                )
            ")->fetch_assoc()['count'];
            
            if ($all_approved == 0) {
                // All tasks in this level are approved, mark level as complete
                $level_check = $conn->query("SELECT id FROM level_completion WHERE user_id = {$submission['user_id']} AND level_id = {$submission['level_id']}");
                if ($level_check->num_rows == 0) {
                    $conn->query("INSERT INTO level_completion (user_id, level_id) VALUES ({$submission['user_id']}, {$submission['level_id']})");
                }
            }
            
            $_SESSION['success'] = 'Submission approved successfully';
        }
        $update->close();
    } elseif ($_POST['action'] === 'reject') {
        $update = $conn->prepare("UPDATE task_submissions SET status = 'Rejected', reviewed_by = ?, reviewed_at = NOW(), admin_feedback = ? WHERE id = ?");
        $update->bind_param("isi", $admin_id, $feedback, $submission_id);
        if ($update->execute()) {
            $_SESSION['success'] = 'Submission rejected';
        }
        $update->close();
    } elseif ($_POST['action'] === 'rework') {
        $update = $conn->prepare("UPDATE task_submissions SET status = 'Rework Requested', reviewed_by = ?, reviewed_at = NOW(), admin_feedback = ?, rework_count = rework_count + 1 WHERE id = ?");
        $update->bind_param("isi", $admin_id, $feedback, $submission_id);
        if ($update->execute()) {
            $_SESSION['success'] = 'Rework requested';
        }
        $update->close();
    }
    
    header("Location: review.php");
    exit();
}

// Get filter
$status_filter = $_GET['status'] ?? 'all';
$selected_user_id = intval($_GET['user_id'] ?? 0);

// Get all interns with submissions count
$users_query = "
    SELECT u.id, u.full_name, u.email, u.college_name,
           COUNT(DISTINCT ts.id) as total_submissions,
           SUM(CASE WHEN ts.status = 'Submitted' THEN 1 ELSE 0 END) as pending_submissions,
           SUM(CASE WHEN ts.status = 'Approved' THEN 1 ELSE 0 END) as approved_submissions,
           SUM(CASE WHEN ts.status = 'Rejected' THEN 1 ELSE 0 END) as rejected_submissions,
           SUM(CASE WHEN ts.status = 'Rework Requested' THEN 1 ELSE 0 END) as rework_submissions
    FROM users u
    LEFT JOIN task_submissions ts ON u.id = ts.user_id
    WHERE u.role = 'Intern'
    GROUP BY u.id, u.full_name, u.email, u.college_name
    HAVING COUNT(DISTINCT ts.id) > 0
    ORDER BY pending_submissions DESC, u.full_name ASC
";

$users = $conn->query($users_query);

// If user is selected, get their submissions
$user_submissions = null;
$selected_user = null;

if ($selected_user_id > 0) {
    // Get user info
    $selected_user = $conn->query("SELECT id, full_name, email, college_name FROM users WHERE id = $selected_user_id AND role = 'Intern'")->fetch_assoc();
    
    if ($selected_user) {
        // Get submissions for this user
        if ($status_filter === 'all') {
            $user_submissions = $conn->query("
                SELECT ts.id, ts.status, ts.submitted_at, ts.submission_content, ts.submission_link, ts.file_path, ts.admin_feedback,
                       t.title as task_title, l.level_number, l.id as level_id, ts.task_id
                FROM task_submissions ts
                JOIN tasks t ON ts.task_id = t.id
                JOIN levels l ON t.level_id = l.id
                WHERE ts.user_id = $selected_user_id
                ORDER BY l.level_number ASC, t.order_sequence ASC
            ");
        } else {
            $submissions_query = $conn->prepare("
                SELECT ts.id, ts.status, ts.submitted_at, ts.submission_content, ts.submission_link, ts.file_path, ts.admin_feedback,
                       t.title as task_title, l.level_number, l.id as level_id, ts.task_id
                FROM task_submissions ts
                JOIN tasks t ON ts.task_id = t.id
                JOIN levels l ON t.level_id = l.id
                WHERE ts.user_id = ? AND ts.status = ?
                ORDER BY l.level_number ASC, t.order_sequence ASC
            ");
            $submissions_query->bind_param("is", $selected_user_id, $status_filter);
            $submissions_query->execute();
            $user_submissions = $submissions_query->get_result();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review Submissions - Admin</title>
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
        
        .user-card {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 12px;
            cursor: pointer;
            transition: all 0.3s;
            background: white;
        }
        
        .user-card:hover {
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.2);
            border-color: var(--primary-color);
        }
        
        .user-card.selected {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
            border-color: var(--primary-color);
        }
        
        .user-name {
            font-weight: 700;
            color: #333;
            margin-bottom: 5px;
        }
        
        .user-stats {
            display: flex;
            gap: 15px;
            margin-top: 8px;
            font-size: 13px;
        }
        
        .stat-badge {
            padding: 4px 8px;
            border-radius: 4px;
            background: #f0f0f0;
            color: #666;
        }
        
        .stat-badge.pending {
            background: #fff3cd;
            color: #856404;
        }
        
        .stat-badge.approved {
            background: #d4edda;
            color: #155724;
        }
        
        .submission-card {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 15px;
            display: flex;
            justify-content: space-between;
            align-items: start;
        }
        
        .submission-info h6 {
            color: var(--primary-color);
            margin-bottom: 5px;
        }
        
        .badge-submitted {
            background-color: #e3f2fd;
            color: #1976d2;
        }
        
        .badge-approved {
            background-color: #d4edda;
            color: #155724;
        }
        
        .badge-rejected {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .badge-rework {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .filter-tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            border-bottom: 2px solid #e0e0e0;
        }
        
        .filter-tabs a {
            padding: 10px 15px;
            text-decoration: none;
            color: #6c757d;
            border-bottom: 3px solid transparent;
            transition: all 0.3s;
        }
        
        .filter-tabs a.active {
            color: var(--primary-color);
            border-bottom-color: var(--primary-color);
        }
        
        .two-column {
            display: grid;
            grid-template-columns: 350px 1fr;
            gap: 20px;
            min-height: 600px;
        }
        
        .users-panel {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 15px;
            background: #f8f9fa;
            max-height: 800px;
            overflow-y: auto;
        }
        
        .submissions-panel {
            min-height: 600px;
        }
        
        .user-header {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            border-left: 5px solid var(--primary-color);
        }
        
        .no-selection {
            text-align: center;
            padding: 60px 20px;
            color: #999;
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
                    <h2 class="mb-4">Review Task Submissions</h2>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?= $_SESSION['success'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        
        <div class="two-column">
            <!-- Users List Panel -->
            <div class="users-panel">
                <h5 style="margin-bottom: 15px; color: var(--primary-color);">
                    <i class="bi bi-people"></i> Interns
                </h5>
                
                <?php if ($users->num_rows === 0): ?>
                    <div class="alert alert-info mb-0">
                        <i class="bi bi-info-circle"></i> No interns with submissions
                    </div>
                <?php else: ?>
                    <?php while ($user = $users->fetch_assoc()): ?>
                        <div class="user-card <?= $selected_user_id === (int)$user['id'] ? 'selected' : '' ?>" 
                             onclick="selectUser(<?= $user['id'] ?>)">
                            <div class="user-name"><?= htmlspecialchars($user['full_name']) ?></div>
                            <small class="text-muted d-block"><?= htmlspecialchars($user['email']) ?></small>
                            <small class="text-muted d-block"><?= htmlspecialchars($user['college_name']) ?></small>
                            
                            <div class="user-stats">
                                <?php if ($user['pending_submissions'] > 0): ?>
                                    <span class="stat-badge pending">
                                        <i class="bi bi-hourglass-split"></i> <?= $user['pending_submissions'] ?> Pending
                                    </span>
                                <?php endif; ?>
                                <?php if ($user['approved_submissions'] > 0): ?>
                                    <span class="stat-badge approved">
                                        <i class="bi bi-check-circle"></i> <?= $user['approved_submissions'] ?> Approved
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php endif; ?>
            </div>
            
            <!-- Submissions Details Panel -->
            <div class="submissions-panel">
                <?php if ($selected_user_id === 0): ?>
                    <div class="no-selection">
                        <i class="bi bi-arrow-left" style="font-size: 48px; color: #ddd;"></i>
                        <p style="margin-top: 15px;">Select an intern to view their submissions</p>
                    </div>
                <?php else: ?>
                    <!-- User Header -->
                    <div class="user-header">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h4 style="color: var(--primary-color); margin-bottom: 5px;">
                                    <i class="bi bi-person-circle"></i> <?= htmlspecialchars($selected_user['full_name']) ?>
                                </h4>
                                <p class="text-muted mb-1"><?= htmlspecialchars($selected_user['email']) ?></p>
                                <p class="text-muted mb-0"><?= htmlspecialchars($selected_user['college_name']) ?></p>
                            </div>
                            <button onclick="clearSelection()" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-x"></i> Clear
                            </button>
                        </div>
                    </div>
                    
                    <!-- Filter Tabs for User -->
                    <div class="filter-tabs" style="margin-bottom: 20px;">
                        <a href="?user_id=<?= $selected_user_id ?>&status=all" class="<?= $status_filter === 'all' ? 'active' : '' ?>">All</a>
                        <a href="?user_id=<?= $selected_user_id ?>&status=Submitted" class="<?= $status_filter === 'Submitted' ? 'active' : '' ?>">Submitted</a>
                        <a href="?user_id=<?= $selected_user_id ?>&status=Approved" class="<?= $status_filter === 'Approved' ? 'active' : '' ?>">Approved</a>
                        <a href="?user_id=<?= $selected_user_id ?>&status=Rejected" class="<?= $status_filter === 'Rejected' ? 'active' : '' ?>">Rejected</a>
                        <a href="?user_id=<?= $selected_user_id ?>&status=Rework Requested" class="<?= $status_filter === 'Rework Requested' ? 'active' : '' ?>">Rework</a>
                    </div>
                    
                    <!-- Submissions List -->
                    <?php if (!$user_submissions || $user_submissions->num_rows === 0): ?>
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> No submissions found for this filter
                        </div>
                    <?php else: ?>
                        <?php while ($sub = $user_submissions->fetch_assoc()): ?>
                            <div class="submission-card">
                                <div class="submission-info" style="flex: 1;">
                                    <h6>Level <?= $sub['level_number'] ?> - <?= htmlspecialchars($sub['task_title']) ?></h6>
                                    <p class="text-muted mb-2">
                                        <strong>Submitted:</strong> <?= date('d M Y H:i', strtotime($sub['submitted_at'])) ?>
                                    </p>
                                    <span class="badge badge-<?= strtolower(str_replace(' ', '-', $sub['status'])) ?>">
                                        <?= $sub['status'] ?>
                                    </span>
                                    
                                    <?php if (!empty($sub['submission_content'])): ?>
                                        <div class="mt-2">
                                            <small><strong>Content:</strong> <?= substr(htmlspecialchars($sub['submission_content']), 0, 100) ?>...</small>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($sub['submission_link'])): ?>
                                        <div class="mt-2">
                                            <small><strong>Link:</strong> <a href="<?= htmlspecialchars($sub['submission_link']) ?>" target="_blank">View Link</a></small>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($sub['admin_feedback'])): ?>
                                        <div class="alert alert-info mt-2 mb-0" style="font-size: 12px;">
                                            <strong>Feedback:</strong> <?= htmlspecialchars($sub['admin_feedback']) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="ms-3">
                                    <?php if (in_array($sub['status'], ['Submitted', 'Rework Requested'])): ?>
                                        <button class="btn btn-sm btn-success mb-2" onclick="reviewSubmission(<?= $sub['id'] ?>, 'approve')">
                                            <i class="bi bi-check"></i> Approve
                                        </button><br>
                                        <button class="btn btn-sm btn-warning mb-2" onclick="reviewSubmission(<?= $sub['id'] ?>, 'rework')">
                                            <i class="bi bi-arrow-clockwise"></i> Rework
                                        </button><br>
                                        <button class="btn btn-sm btn-danger" onclick="reviewSubmission(<?= $sub['id'] ?>, 'reject')">
                                            <i class="bi bi-x"></i> Reject
                                        </button>
                                    <?php else: ?>
                                        <span class="text-muted">Review completed</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Review Modal -->
    <div class="modal fade" id="reviewModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Review Submission</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" id="action" value="">
                        <input type="hidden" name="submission_id" id="submission_id" value="">
                        
                        <div class="mb-3">
                            <label class="form-label">Feedback (Optional)</label>
                            <textarea class="form-control" name="feedback" rows="3" placeholder="Enter your feedback..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
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
        function reviewSubmission(submissionId, action) {
            document.getElementById('submission_id').value = submissionId;
            document.getElementById('action').value = action;
            new bootstrap.Modal(document.getElementById('reviewModal')).show();
        }
        
        function selectUser(userId) {
            window.location.href = '?user_id=' + userId + '&status=all';
        }
        
        function clearSelection() {
            window.location.href = 'review.php';
        }
    </script>
</body>
</html>
