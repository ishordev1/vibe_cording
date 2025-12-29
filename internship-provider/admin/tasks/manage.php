<?php
/**
 * Admin - Manage Tasks & Levels
 * Create and manage tasks for each level
 */

require_once '../../includes/config.php';
require_once '../../includes/session.php';
require_once '../../includes/validation.php';

requireLogin();
requireAdmin();

$admin_id = $_SESSION['user_id'];
$error = '';
$success = '';

// Handle task creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'create_task') {
        $data = [
            'level_id' => intval($_POST['level_id'] ?? 0),
            'title' => sanitizeInput($_POST['title'] ?? ''),
            'description' => sanitizeInput($_POST['description'] ?? ''),
            'deliverables' => sanitizeInput($_POST['deliverables'] ?? ''),
            'submission_format' => sanitizeInput($_POST['submission_format'] ?? ''),
            'order_sequence' => intval($_POST['order_sequence'] ?? 1)
        ];
        
        if (empty($data['level_id']) || empty($data['title']) || empty($data['description'])) {
            $_SESSION['error'] = 'Please fill all required fields';
        } else {
            $insert = $conn->prepare("INSERT INTO tasks (level_id, title, description, deliverables, submission_format, order_sequence, created_by) 
            VALUES (?, ?, ?, ?, ?, ?, ?)");
            
            $insert->bind_param("issssii", 
                $data['level_id'], $data['title'], $data['description'], $data['deliverables'],
                $data['submission_format'], $data['order_sequence'], $admin_id
            );
            
            if ($insert->execute()) {
                $_SESSION['success'] = 'Task created successfully';
            } else {
                $_SESSION['error'] = 'Error creating task';
            }
            $insert->close();
        }
    }
    
    // Handle task update
    elseif ($_POST['action'] === 'update_task') {
        $task_id = intval($_POST['task_id'] ?? 0);
        $data = [
            'level_id' => intval($_POST['level_id'] ?? 0),
            'title' => sanitizeInput($_POST['title'] ?? ''),
            'description' => sanitizeInput($_POST['description'] ?? ''),
            'deliverables' => sanitizeInput($_POST['deliverables'] ?? ''),
            'submission_format' => sanitizeInput($_POST['submission_format'] ?? ''),
            'order_sequence' => intval($_POST['order_sequence'] ?? 1)
        ];
        
        if (empty($data['level_id']) || empty($data['title']) || empty($data['description'])) {
            $_SESSION['error'] = 'Please fill all required fields';
        } else {
            $update = $conn->prepare("UPDATE tasks SET level_id = ?, title = ?, description = ?, deliverables = ?, submission_format = ?, order_sequence = ? WHERE id = ? AND created_by = ?");
            
            $update->bind_param("issssiii", 
                $data['level_id'], $data['title'], $data['description'], $data['deliverables'],
                $data['submission_format'], $data['order_sequence'], $task_id, $admin_id
            );
            
            if ($update->execute()) {
                $_SESSION['success'] = 'Task updated successfully';
            } else {
                $_SESSION['error'] = 'Error updating task';
            }
            $update->close();
        }
    }
    
    // Handle task deletion
    elseif ($_POST['action'] === 'delete_task') {
        $task_id = intval($_POST['task_id'] ?? 0);
        $delete = $conn->prepare("DELETE FROM tasks WHERE id = ? AND created_by = ?");
        $delete->bind_param("ii", $task_id, $admin_id);
        if ($delete->execute()) {
            $_SESSION['success'] = 'Task deleted successfully';
        }
        $delete->close();
    }
    
    // Handle level creation
    elseif ($_POST['action'] === 'create_level') {
        $level_number = intval($_POST['level_number'] ?? 0);
        $level_title = sanitizeInput($_POST['level_title'] ?? '');
        $level_description = sanitizeInput($_POST['level_description'] ?? '');
        
        if (empty($level_number) || empty($level_title)) {
            $_SESSION['error'] = 'Please fill all required level fields';
        } elseif ($level_number < 1 || $level_number > 10) {
            $_SESSION['error'] = 'Level number must be between 1 and 10';
        } else {
            // Check if level already exists
            $check = $conn->prepare("SELECT id FROM levels WHERE level_number = ?");
            $check->bind_param("i", $level_number);
            $check->execute();
            if ($check->get_result()->num_rows > 0) {
                $_SESSION['error'] = 'Level ' . $level_number . ' already exists';
            } else {
                $insert_level = $conn->prepare("INSERT INTO levels (level_number, title, description, order_sequence) VALUES (?, ?, ?, ?)");
                $insert_level->bind_param("issi", $level_number, $level_title, $level_description, $level_number);
                
                if ($insert_level->execute()) {
                    $_SESSION['success'] = 'Level created successfully';
                } else {
                    $_SESSION['error'] = 'Error creating level';
                }
                $insert_level->close();
            }
            $check->close();
        }
    }
    
    header("Location: manage.php");
    exit();
}

// Get all levels
$levels_result = $conn->query("SELECT id, level_number, title, description FROM levels ORDER BY level_number ASC");
$levels_array = [];
if ($levels_result && $levels_result->num_rows > 0) {
    while ($row = $levels_result->fetch_assoc()) {
        $levels_array[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Tasks & Levels - Admin</title>
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
        
        .sidebar {
            background-color: #2c3e50 !important;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
        }
        
        .sidebar-sticky {
            position: sticky;
            top: 0;
        }
        
        .sidebar .nav-link {
            padding: 12px 20px;
            color: #ecf0f1 !important;
            border-left: 3px solid transparent;
            transition: all 0.3s ease;
        }
        
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background-color: rgba(102, 126, 234, 0.1);
            border-left-color: var(--primary-color);
            color: #fff !important;
        }
        
        main {
            padding-top: 20px;
        }
        
        .container-custom {
            max-width: 1100px;
            margin: 0 auto;
            padding: 0 15px;
        }
        
        .level-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 25px;
            margin-bottom: 30px;
            border-left: 5px solid var(--primary-color);
        }
        
        .level-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .level-title {
            font-size: 20px;
            font-weight: 700;
            color: var(--primary-color);
        }
        
        .task-item {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            display: flex;
            justify-content: space-between;
            align-items: start;
        }
        
        .task-content h6 {
            margin-bottom: 5px;
            color: #333;
        }
        
        .task-content p {
            margin: 0;
            font-size: 14px;
            color: #6c757d;
        }
        
        .modal-content {
            border-radius: 10px;
        }
        
        .form-label {
            font-weight: 500;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar Navigation -->
            <nav class="col-md-2 bg-dark sidebar p-0" style="min-height: 100vh; position: sticky; top: 0;">
                <div class="sidebar-sticky">
                    <h5 class="text-white p-3 border-bottom">Admin Panel</h5>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link text-white" href="../dashboard.php">📊 Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="../internships/manage.php">💼 Internships</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="../applications/view.php">📝 Applications</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white active" href="../tasks/manage.php">📋 Tasks & Levels</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="../attendance/manage.php">✅ Mark Attendance</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="../applications/review.php">👀 Review Submissions</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="../certificates/generate.php">🏆 Generate Certificates</a>
                        </li>
                        <li class="nav-item border-top mt-3 pt-3">
                            <a class="nav-link text-white" href="../../auth/logout.php">🚪 Logout</a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main Content -->
            <main class="col-md-10 ms-sm-auto px-md-4">
                <div class="container-custom">
                    <div class="d-flex justify-content-between align-items-center mb-4 mt-4">
                        <h2>Tasks & Levels Management</h2>
                        <div>
                            <button class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#levelModal">
                                <i class="bi bi-plus-circle"></i> Create Level
                            </button>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#taskModal">
                                <i class="bi bi-plus-circle"></i> Create Task
                            </button>
                        </div>
                    </div>
        
        <!-- Messages -->
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
        
        <!-- Levels and Tasks -->
        <?php foreach ($levels_array as $level): ?>
            <div class="level-card">
                <div class="level-header">
                    <div>
                        <div class="level-title">Level <?= $level['level_number'] ?>: <?= $level['title'] ?></div>
                        <p class="text-muted mb-0"><?= $level['description'] ?></p>
                    </div>
                    <button class="btn btn-outline-primary btn-sm" onclick="addTaskForLevel(<?= $level['id'] ?>)">
                        <i class="bi bi-plus"></i> Add Task
                    </button>
                </div>
                
                <!-- Tasks for this level -->
                <div id="tasks-level-<?= $level['id'] ?>">
                    <?php 
                    $tasks = $conn->prepare("SELECT * FROM tasks WHERE level_id = ? ORDER BY order_sequence ASC");
                    $tasks->bind_param("i", $level['id']);
                    $tasks->execute();
                    $tasks_result = $tasks->get_result();
                    
                    if ($tasks_result->num_rows === 0):
                    ?>
                        <p class="text-muted text-center" style="padding: 40px 0;">
                            <i class="bi bi-inbox"></i> No tasks created for this level yet
                        </p>
                    <?php else: ?>
                        <?php while ($task = $tasks_result->fetch_assoc()): ?>
                            <div class="task-item">
                                <div class="task-content" style="flex: 1;">
                                    <h6><?= $task['title'] ?></h6>
                                    <p><strong>Description:</strong> <?= substr($task['description'], 0, 100) ?>...</p>
                                    <p><strong>Format:</strong> <?= $task['submission_format'] ?> | <strong>Deliverables:</strong> <?= substr($task['deliverables'], 0, 80) ?>...</p>
                                </div>
                                <div style="display: flex; gap: 8px;">
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="editTask(<?= htmlspecialchars(json_encode($task)) ?>)">
                                        <i class="bi bi-pencil"></i> Edit
                                    </button>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="action" value="delete_task">
                                        <input type="hidden" name="task_id" value="<?= $task['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this task?')">
                                            <i class="bi bi-trash"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php endif; ?>
                    <?php $tasks->close(); ?>
                </div>
            </div>
        <?php endforeach; ?>
        
        <?php if (count($levels_array) === 0): ?>
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i> No levels found. Please run the database setup script.
            </div>
        <?php endif; ?>
                </div>
            </main>
        </div>
    </div>
    
    <!-- Create Task Modal -->
    <div class="modal fade" id="taskModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create New Task</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="create_task">
                        
                        <div class="mb-3">
                            <label class="form-label">Level <span class="text-danger">*</span></label>
                            <select class="form-control" name="level_id" id="level_select" required>
                                <option value="">Select Level</option>
                                <?php 
                                foreach ($levels_array as $lvl):
                                ?>
                                    <option value="<?= $lvl['id'] ?>">Level <?= $lvl['level_number'] ?>: <?= $lvl['title'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Task Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="title" placeholder="e.g., Setup Development Environment" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Description <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="description" rows="3" placeholder="Detailed task description" required></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Deliverables <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="deliverables" rows="2" placeholder="What should interns deliver? (e.g., GitHub repo link, screenshots, code files)" required></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Submission Format <span class="text-danger">*</span></label>
                                <select class="form-control" name="submission_format" required>
                                    <option value="">Select Format</option>
                                    <option value="Link">Link (GitHub, Drive)</option>
                                    <option value="Text">Text/Description</option>
                                    <option value="File">File Upload</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Order Sequence</label>
                                <input type="number" class="form-control" name="order_sequence" value="1" min="1">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Create Task</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Edit Task Modal -->
    <div class="modal fade" id="editTaskModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Task</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="update_task">
                        <input type="hidden" name="task_id" id="edit_task_id" value="">
                        
                        <div class="mb-3">
                            <label class="form-label">Level <span class="text-danger">*</span></label>
                            <select class="form-control" name="level_id" id="edit_level_id" required>
                                <option value="">Select Level</option>
                                <?php 
                                foreach ($levels_array as $lvl):
                                ?>
                                    <option value="<?= $lvl['id'] ?>">Level <?= $lvl['level_number'] ?>: <?= $lvl['title'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Task Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_title" name="title" placeholder="e.g., Setup Development Environment" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Description <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="edit_description" name="description" rows="3" placeholder="Detailed task description" required></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Deliverables <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="edit_deliverables" name="deliverables" rows="2" placeholder="What should interns deliver?" required></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Submission Format <span class="text-danger">*</span></label>
                                <select class="form-control" id="edit_submission_format" name="submission_format" required>
                                    <option value="">Select Format</option>
                                    <option value="Link">Link (GitHub, Drive)</option>
                                    <option value="Text">Text/Description</option>
                                    <option value="File">File Upload</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Order Sequence</label>
                                <input type="number" class="form-control" id="edit_order_sequence" name="order_sequence" value="1" min="1">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Task</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Create Level Modal -->
    <div class="modal fade" id="levelModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create New Level</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="create_level">
                        
                        <div class="mb-3">
                            <label class="form-label">Level Number <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="level_number" placeholder="e.g., 5" min="1" max="10" required>
                            <small class="text-muted">Enter a level number (1-10)</small>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Level Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="level_title" placeholder="e.g., Master Level" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="level_description" rows="3" placeholder="Describe this level"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Create Level</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function addTaskForLevel(levelId) {
            document.getElementById('level_select').value = levelId;
            new bootstrap.Modal(document.getElementById('taskModal')).show();
        }
        
        function editTask(taskData) {
            document.getElementById('edit_task_id').value = taskData.id;
            document.getElementById('edit_level_id').value = taskData.level_id;
            document.getElementById('edit_title').value = taskData.title;
            document.getElementById('edit_description').value = taskData.description;
            document.getElementById('edit_deliverables').value = taskData.deliverables;
            document.getElementById('edit_submission_format').value = taskData.submission_format;
            document.getElementById('edit_order_sequence').value = taskData.order_sequence;
            new bootstrap.Modal(document.getElementById('editTaskModal')).show();
        }
    </script>
</body>
</html>
