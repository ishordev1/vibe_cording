<?php
/**
 * User - Task Detail View
 * Detailed view of a specific task
 */

require_once '../../includes/config.php';
require_once '../../includes/session.php';
require_once '../../includes/validation.php';

requireLogin();
requireIntern();

$user_id = $_SESSION['user_id'];
$task_id = intval($_GET['id'] ?? 0);

// Get task details
$task = $conn->prepare("
    SELECT t.*, l.title as level_title, l.level_number, l.description as level_description
    FROM tasks t
    JOIN levels l ON t.level_id = l.id
    WHERE t.id = ?
");
$task->bind_param("i", $task_id);
$task->execute();
$task_result = $task->get_result();
$task_data = $task_result->fetch_assoc();
$task->close();

if (!$task_data) {
    $_SESSION['error'] = 'Task not found';
    header("Location: view.php");
    exit();
}

// Get user's submission for this task
$submission = $conn->query("
    SELECT * FROM task_submissions 
    WHERE task_id = $task_id AND user_id = $user_id
")->fetch_assoc();

$status = $submission ? $submission['status'] : 'Not Started';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($task_data['title']) ?> - Task Detail</title>
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
        
        .navbar-custom a:hover {
            opacity: 0.8;
        }
        
        .container-custom {
            max-width: 900px;
            margin: 30px auto;
            padding: 0 15px;
        }
        
        .header-section {
            background: white;
            border-radius: 10px;
            padding: 30px;
            margin-bottom: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-top: 4px solid var(--primary-color);
        }
        
        .level-badge {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            display: inline-block;
            margin-bottom: 15px;
        }
        
        .task-title {
            font-size: 28px;
            font-weight: 700;
            color: #333;
            margin: 15px 0;
        }
        
        .task-status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            margin-right: 10px;
        }
        
        .status-not-started {
            background: #f5f5f5;
            color: #666;
        }
        
        .status-submitted {
            background: #e3f2fd;
            color: #1976d2;
        }
        
        .status-under-review {
            background: #fff3e0;
            color: #f57c00;
        }
        
        .status-approved {
            background: #e8f5e9;
            color: #388e3c;
        }
        
        .status-rejected {
            background: #ffebee;
            color: #d32f2f;
        }
        
        .status-rework-requested {
            background: #f3e5f5;
            color: #7b1fa2;
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
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e0e0e0;
        }
        
        .detail-row {
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
            margin-bottom: 5px;
        }
        
        .detail-value {
            color: #6c757d;
            line-height: 1.6;
        }
        
        .deliverables-list {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid var(--primary-color);
        }
        
        .feedback-alert {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .action-buttons {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        
        .back-link {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 20px;
        }
        
        .back-link:hover {
            color: var(--secondary-color);
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <div class="navbar-custom">
        <div class="d-flex justify-content-between align-items-center">
            <div class="brand"><i class="bi bi-list-check"></i> Task Detail</div>
            <div>
                <a href="view.php">My Tasks</a>
                <a href="../dashboard.php">Dashboard</a>
                <a href="../../auth/logout.php">Logout</a>
            </div>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="container-custom">
        <a href="view.php" class="back-link">
            <i class="bi bi-arrow-left"></i> Back to Tasks
        </a>
        
        <!-- Header Section -->
        <div class="header-section">
            <span class="level-badge">
                <i class="bi bi-diagram-3"></i> Level <?= $task_data['level_number'] ?> - <?= htmlspecialchars($task_data['level_title']) ?>
            </span>
            <h1 class="task-title"><?= htmlspecialchars($task_data['title']) ?></h1>
            
            <div>
                <span class="task-status-badge status-<?= strtolower(str_replace(' ', '-', $status)) ?>">
                    <i class="bi bi-info-circle"></i> <?= $status ?>
                </span>
            </div>
        </div>
        
        <!-- Task Description Section -->
        <div class="detail-section">
            <h5 class="section-title"><i class="bi bi-file-text"></i> Description</h5>
            <div class="detail-value" style="line-height: 1.8; font-size: 15px;">
                <?= nl2br(htmlspecialchars($task_data['description'])) ?>
            </div>
        </div>
        
        <!-- Task Details Section -->
        <div class="detail-section">
            <h5 class="section-title"><i class="bi bi-info-circle"></i> Task Details</h5>
            
            <div class="detail-row">
                <div class="detail-label">Submission Format</div>
                <div class="detail-value">
                    <span class="badge" style="background-color: var(--primary-color);">
                        <?= htmlspecialchars($task_data['submission_format']) ?>
                    </span>
                    <small class="text-muted d-block mt-2">
                        <?php
                        $format_descriptions = [
                            'Link' => 'Submit a GitHub link, Google Drive link, or any URL',
                            'Text' => 'Write your response as text or description',
                            'File' => 'Upload a file (PDF, ZIP, etc.)'
                        ];
                        echo $format_descriptions[$task_data['submission_format']] ?? '';
                        ?>
                    </small>
                </div>
            </div>
            
            <div class="detail-row">
                <div class="detail-label">Deliverables</div>
                <div class="deliverables-list">
                    <div class="detail-value">
                        <?= nl2br(htmlspecialchars($task_data['deliverables'])) ?>
                    </div>
                </div>
            </div>
            
            <div class="detail-row">
                <div class="detail-label">Order Sequence</div>
                <div class="detail-value">
                    Task #<?= $task_data['order_sequence'] ?>
                </div>
            </div>
        </div>
        
        <!-- Submission Status Section -->
        <div class="detail-section">
            <h5 class="section-title"><i class="bi bi-clipboard-check"></i> Submission Status</h5>
            
            <?php if ($submission): ?>
                <div class="detail-row">
                    <div class="detail-label">Status</div>
                    <div class="detail-value">
                        <span class="task-status-badge status-<?= strtolower(str_replace(' ', '-', $submission['status'])) ?>">
                            <?= htmlspecialchars($submission['status']) ?>
                        </span>
                    </div>
                </div>
                
                <?php if ($submission['submitted_at']): ?>
                    <div class="detail-row">
                        <div class="detail-label">Submitted On</div>
                        <div class="detail-value">
                            <i class="bi bi-calendar"></i> <?= date('F d, Y \a\t h:i A', strtotime($submission['submitted_at'])) ?>
                        </div>
                    </div>
                <?php endif; ?>
                
                <?php if ($submission['submission_link']): ?>
                    <div class="detail-row">
                        <div class="detail-label">Submission Link</div>
                        <div class="detail-value">
                            <a href="<?= htmlspecialchars($submission['submission_link']) ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-box-arrow-up-right"></i> View Link
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
                
                <?php if ($submission['submission_content']): ?>
                    <div class="detail-row">
                        <div class="detail-label">Submission Content</div>
                        <div class="detail-value" style="background: #f8f9fa; padding: 12px; border-radius: 6px;">
                            <?= nl2br(htmlspecialchars($submission['submission_content'])) ?>
                        </div>
                    </div>
                <?php endif; ?>
                
                <?php if ($submission['file_path']): ?>
                    <div class="detail-row">
                        <div class="detail-label">Uploaded File</div>
                        <div class="detail-value">
                            <a href="../../<?= htmlspecialchars($submission['file_path']) ?>" class="btn btn-sm btn-outline-success" download>
                                <i class="bi bi-download"></i> Download File
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
                
                <?php if ($submission['admin_feedback']): ?>
                    <div class="feedback-alert">
                        <strong><i class="bi bi-chat-left-text"></i> Feedback from Admin</strong>
                        <div style="margin-top: 10px; color: #856404;">
                            <?= nl2br(htmlspecialchars($submission['admin_feedback'])) ?>
                        </div>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="alert alert-info mb-0">
                    <i class="bi bi-info-circle"></i> You haven't submitted this task yet.
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Action Buttons -->
        <div class="action-buttons">
            <?php if (!$submission || in_array($submission['status'], ['Rejected', 'Rework Requested'])): ?>
                <button class="btn btn-primary btn-lg" onclick="openSubmissionModal()">
                    <i class="bi bi-upload"></i> Submit Task
                </button>
            <?php elseif ($submission['status'] === 'Submitted' || $submission['status'] === 'Under Review'): ?>
                <button class="btn btn-warning btn-lg" onclick="openSubmissionModal()">
                    <i class="bi bi-pencil"></i> Resubmit Task
                </button>
            <?php else: ?>
                <button class="btn btn-success btn-lg" disabled>
                    <i class="bi bi-check-circle"></i> Task Completed
                </button>
            <?php endif; ?>
            
            <a href="view.php" class="btn btn-outline-secondary btn-lg">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </div>
    </div>
    
    <!-- Submission Modal (Reused from view.php) -->
    <div class="modal fade" id="submissionModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Submit Task</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="view.php" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" name="submit_task" value="1">
                        <input type="hidden" name="task_id" id="modal_task_id" value="<?= $task_id ?>">
                        
                        <p><strong>Task:</strong> <span id="modal_task_title"><?= htmlspecialchars($task_data['title']) ?></span></p>
                        
                        <div id="submission-link-container" style="display:none;" class="mb-3">
                            <label class="form-label">GitHub/Drive Link <span class="text-danger">*</span></label>
                            <input type="url" class="form-control" name="submission_link" placeholder="https://..." required>
                        </div>
                        
                        <div id="submission-text-container" style="display:none;" class="mb-3">
                            <label class="form-label">Your Response <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="submission_content" rows="4" placeholder="Enter your response here" required></textarea>
                        </div>
                        
                        <div id="submission-file-container" style="display:none;" class="mb-3">
                            <label class="form-label">Upload File <span class="text-danger">*</span></label>
                            <input type="file" class="form-control" name="submission_file" required>
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
        function openSubmissionModal() {
            const format = '<?= $task_data['submission_format'] ?>';
            
            // Show relevant input based on format
            document.getElementById('submission-link-container').style.display = format === 'Link' ? 'block' : 'none';
            document.getElementById('submission-text-container').style.display = format === 'Text' ? 'block' : 'none';
            document.getElementById('submission-file-container').style.display = format === 'File' ? 'block' : 'none';
            
            new bootstrap.Modal(document.getElementById('submissionModal')).show();
        }
    </script>
</body>
</html>
