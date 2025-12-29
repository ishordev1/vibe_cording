<?php
/**
 * User - Task Submission
 * View tasks and submit work
 */

require_once '../../includes/config.php';
require_once '../../includes/session.php';
require_once '../../includes/validation.php';

requireLogin();
requireIntern();

$user_id = $_SESSION['user_id'];

// Handle task submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_task'])) {
    $task_id = intval($_POST['task_id'] ?? 0);
    $submission_content = sanitizeInput($_POST['submission_content'] ?? '');
    $submission_link = sanitizeInput($_POST['submission_link'] ?? '');
    
    // Check if task exists
    $task = $conn->query("SELECT submission_format FROM tasks WHERE id = $task_id")->fetch_assoc();
    if (!$task) {
        $_SESSION['error'] = 'Invalid task';
    } else {
        // Check existing submission
        $existing = $conn->query("SELECT id FROM task_submissions WHERE task_id = $task_id AND user_id = $user_id");
        
        if ($existing->num_rows > 0) {
            // Update existing submission
            $submission_id = $existing->fetch_assoc()['id'];
            
            $file_path = null;
            if ($task['submission_format'] === 'File' && isset($_FILES['submission_file'])) {
                $validation = validateFile($_FILES['submission_file']);
                if ($validation['valid']) {
                    $filename = generateFileName($_FILES['submission_file']['name']);
                    $upload_path = UPLOAD_DIR . 'submissions/' . $filename;
                    
                    if (!is_dir(UPLOAD_DIR . 'submissions/')) {
                        mkdir(UPLOAD_DIR . 'submissions/', 0755, true);
                    }
                    
                    if (move_uploaded_file($_FILES['submission_file']['tmp_name'], $upload_path)) {
                        $file_path = 'submissions/' . $filename;
                    }
                }
            }
            
            $update = $conn->prepare("UPDATE task_submissions SET status = 'Submitted', submission_content = ?, submission_link = ?, file_path = COALESCE(?, file_path), submitted_at = NOW() WHERE id = ?");
            $update->bind_param("sssi", $submission_content, $submission_link, $file_path, $submission_id);
            if ($update->execute()) {
                $_SESSION['success'] = 'Task submitted successfully';
            }
            $update->close();
        } else {
            // Create new submission
            $file_path = null;
            if ($task['submission_format'] === 'File' && isset($_FILES['submission_file'])) {
                $validation = validateFile($_FILES['submission_file']);
                if ($validation['valid']) {
                    $filename = generateFileName($_FILES['submission_file']['name']);
                    $upload_path = UPLOAD_DIR . 'submissions/' . $filename;
                    
                    if (!is_dir(UPLOAD_DIR . 'submissions/')) {
                        mkdir(UPLOAD_DIR . 'submissions/', 0755, true);
                    }
                    
                    if (move_uploaded_file($_FILES['submission_file']['tmp_name'], $upload_path)) {
                        $file_path = 'submissions/' . $filename;
                    }
                }
            }
            
            $insert = $conn->prepare("INSERT INTO task_submissions (task_id, user_id, status, submission_content, submission_link, file_path, submitted_at) 
            VALUES (?, ?, 'Submitted', ?, ?, ?, NOW())");
            
            $insert->bind_param("iisss", $task_id, $user_id, $submission_content, $submission_link, $file_path);
            if ($insert->execute()) {
                $_SESSION['success'] = 'Task submitted successfully';
            } else {
                $_SESSION['error'] = 'Error submitting task';
            }
            $insert->close();
        }
    }
    
    header("Location: view.php");
    exit();
}

// Get active internship
$active_app = $conn->query("
    SELECT a.id FROM applications a 
    WHERE a.user_id = $user_id AND a.status = 'Approved' 
    LIMIT 1
")->fetch_assoc();

if (!$active_app) {
    $_SESSION['error'] = 'You need an approved internship to view tasks';
    header("Location: ../dashboard.php");
    exit();
}

// Get all levels with tasks
$levels = $conn->query("
    SELECT l.id, l.level_number, l.title, l.description 
    FROM levels l
    ORDER BY l.level_number ASC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Tasks - Internship Platform</title>
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
        
        .level-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 25px;
            margin-bottom: 25px;
            border-left: 5px solid var(--primary-color);
        }
        
        .level-header {
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e0e0e0;
        }
        
        .level-badge {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }
        
        .level-title {
            font-size: 18px;
            font-weight: 700;
            color: var(--primary-color);
            margin-top: 10px;
        }
        
        .task-item {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 15px;
            border-left: 4px solid #e0e0e0;
        }
        
        .task-item.completed {
            border-left-color: #28a745;
            background-color: #f0f9f6;
        }
        
        .task-item.pending {
            border-left-color: #ffc107;
            background-color: #fffbf0;
        }
        
        .task-item.rejected {
            border-left-color: #dc3545;
            background-color: #fff5f5;
        }
        
        .task-title {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }
        
        .task-desc {
            color: #6c757d;
            font-size: 14px;
            margin-bottom: 10px;
        }
        
        .task-status {
            display: inline-block;
            font-size: 12px;
            padding: 4px 8px;
            border-radius: 4px;
            font-weight: 500;
            margin-right: 10px;
        }
        
        .status-submitted {
            background: #e3f2fd;
            color: #1976d2;
        }
        
        .status-approved {
            background: #e8f5e9;
            color: #388e3c;
        }
        
        .status-rejected {
            background: #ffebee;
            color: #d32f2f;
        }
        
        .status-not-started {
            background: #f5f5f5;
            color: #666;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <div class="navbar-custom">
        <div class="d-flex justify-content-between align-items-center">
            <div class="brand"><i class="bi bi-list-check"></i> My Tasks</div>
            <div>
                <a href="../dashboard.php">Dashboard</a>
                <a href="../../auth/logout.php">Logout</a>
            </div>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="container-custom">
        <!-- Messages -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="bi bi-check-circle"></i> <?= $_SESSION['success'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="bi bi-exclamation-circle"></i> <?= $_SESSION['error'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
        
        <!-- Levels and Tasks -->
        <?php while ($level = $levels->fetch_assoc()): ?>
            <div class="level-card">
                <div class="level-header">
                    <span class="level-badge">Level <?= $level['level_number'] ?></span>
                    <h4 class="level-title"><?= $level['title'] ?></h4>
                    <p class="text-muted mb-0"><?= $level['description'] ?></p>
                </div>
                
                <!-- Get tasks for this level -->
                <?php 
                $tasks = $conn->prepare("SELECT t.* FROM tasks t WHERE t.level_id = ? ORDER BY t.order_sequence ASC");
                $tasks->bind_param("i", $level['id']);
                $tasks->execute();
                $tasks_result = $tasks->get_result();
                
                if ($tasks_result->num_rows === 0):
                ?>
                    <p class="text-muted text-center" style="padding: 30px 0;">
                        <i class="bi bi-inbox"></i> No tasks for this level
                    </p>
                <?php else: ?>
                    <?php while ($task = $tasks_result->fetch_assoc()): 
                        // Get user's submission for this task
                        $submission = $conn->query("
                            SELECT * FROM task_submissions 
                            WHERE task_id = {$task['id']} AND user_id = $user_id
                        ")->fetch_assoc();
                        
                        $status = $submission ? $submission['status'] : 'Not Started';
                        $status_class = strtolower(str_replace(' ', '-', $status));
                    ?>
                        <div class="task-item <?= in_array($status, ['Approved']) ? 'completed' : (in_array($status, ['Pending', 'Submitted', 'Under Review']) ? 'pending' : (in_array($status, ['Rejected', 'Rework Requested']) ? 'rejected' : '')) ?>">
                            <div class="d-flex justify-content-between align-items-start">
                                <div style="flex: 1;">
                                    <h6 class="task-title"><?= $task['title'] ?></h6>
                                    <p class="task-desc"><?= $task['description'] ?></p>
                                    
                                    <div class="mb-2">
                                        <span class="task-status status-<?= str_replace(' ', '-', strtolower($status)) ?>">
                                            <?= $status ?>
                                        </span>
                                        <small class="text-muted">
                                            <strong>Format:</strong> <?= $task['submission_format'] ?> | 
                                            <strong>Deliverables:</strong> <?= $task['deliverables'] ?>
                                        </small>
                                    </div>
                                    
                                    <?php if ($submission && $submission['admin_feedback']): ?>
                                        <div class="alert alert-info mb-2" style="font-size: 13px;">
                                            <strong>Feedback:</strong> <?= $submission['admin_feedback'] ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="ms-3">
                                    <button class="btn btn-sm btn-info me-2" onclick="viewTaskDetail(<?= $task['id'] ?>)">
                                        <i class="bi bi-eye"></i> View
                                    </button>
                                    <?php if (!$submission || in_array($submission['status'], ['Rejected', 'Rework Requested'])): ?>
                                        <button class="btn btn-sm btn-primary" onclick="submitTask(<?= $task['id'] ?>, '<?= addslashes($task['title']) ?>', '<?= $task['submission_format'] ?>')">
                                            <i class="bi bi-upload"></i> Submit
                                        </button>
                                    <?php elseif ($submission['status'] === 'Submitted' || $submission['status'] === 'Under Review'): ?>
                                        <button class="btn btn-sm btn-warning" onclick="resubmitTask(<?= $task['id'] ?>, '<?= addslashes($task['title']) ?>', '<?= $task['submission_format'] ?>')">
                                            <i class="bi bi-pencil"></i> Resubmit
                                        </button>
                                    <?php else: ?>
                                        <button class="btn btn-sm btn-success" disabled>
                                            <i class="bi bi-check-circle"></i> Completed
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php endif; ?>
                <?php $tasks->close(); ?>
            </div>
        <?php endwhile; ?>
    </div>
    
    <!-- Submission Modal -->
    <div class="modal fade" id="submissionModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Submit Task</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" name="submit_task" value="1">
                        <input type="hidden" name="task_id" id="task_id">
                        
                        <p><strong>Task:</strong> <span id="task_title"></span></p>
                        
                        <div id="submission-link-container" style="display:none;" class="mb-3">
                            <label class="form-label">GitHub/Drive Link</label>
                            <input type="url" class="form-control" name="submission_link" placeholder="https://...">
                        </div>
                        
                        <div id="submission-text-container" style="display:none;" class="mb-3">
                            <label class="form-label">Your Response</label>
                            <textarea class="form-control" name="submission_content" rows="4" placeholder="Enter your response here"></textarea>
                        </div>
                        
                        <div id="submission-file-container" style="display:none;" class="mb-3">
                            <label class="form-label">Upload File</label>
                            <input type="file" class="form-control" name="submission_file">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Submit Task</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function viewTaskDetail(taskId) {
            window.location.href = 'detail.php?id=' + taskId;
        }
        
        function submitTask(taskId, title, format) {
            document.getElementById('task_id').value = taskId;
            document.getElementById('task_title').textContent = title;
            
            // Show relevant input based on format
            document.getElementById('submission-link-container').style.display = format === 'Link' ? 'block' : 'none';
            document.getElementById('submission-text-container').style.display = format === 'Text' ? 'block' : 'none';
            document.getElementById('submission-file-container').style.display = format === 'File' ? 'block' : 'none';
            
            new bootstrap.Modal(document.getElementById('submissionModal')).show();
        }
        
        function resubmitTask(taskId, title, format) {
            submitTask(taskId, title, format);
        }
    </script>
</body>
</html>
