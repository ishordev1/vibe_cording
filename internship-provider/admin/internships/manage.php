<?php
/**
 * Admin - Manage Internships
 * Create, edit, delete, and publish internships
 */

require_once '../../includes/config.php';
require_once '../../includes/session.php';
require_once '../../includes/validation.php';

requireLogin();
requireAdmin();

$admin_id = $_SESSION['user_id'];

// Handle form submission for creating/updating internship
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'create' || $_POST['action'] === 'update') {
        $data = [
            'title' => sanitizeInput($_POST['title'] ?? ''),
            'description' => sanitizeInput($_POST['description'] ?? ''),
            'role' => sanitizeInput($_POST['role'] ?? ''),
            'duration_min' => intval($_POST['duration_min'] ?? 1),
            'duration_max' => intval($_POST['duration_max'] ?? 1),
            'internship_type' => sanitizeInput($_POST['internship_type'] ?? ''),
            'remote' => sanitizeInput($_POST['remote'] ?? ''),
            'skills_required' => sanitizeInput($_POST['skills_required'] ?? ''),
            'number_of_positions' => intval($_POST['number_of_positions'] ?? 1)
        ];
        
        // Validation
        if (empty($data['title']) || empty($data['description']) || empty($data['role'])) {
            $_SESSION['error'] = 'Please fill all required fields';
        } else {
            if ($_POST['action'] === 'create') {
                $insert = $conn->prepare("INSERT INTO internships (title, description, role, duration_min, duration_max, internship_type, remote, skills_required, number_of_positions, created_by) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                
                $insert->bind_param("sssissssii", 
                    $data['title'], $data['description'], $data['role'], $data['duration_min'],
                    $data['duration_max'], $data['internship_type'], $data['remote'], $data['skills_required'],
                    $data['number_of_positions'], $admin_id
                );
                
                if ($insert->execute()) {
                    $_SESSION['success'] = 'Internship created successfully';
                } else {
                    $_SESSION['error'] = 'Error creating internship';
                }
                $insert->close();
            }
        }
    }
    
    // Handle publish action
    if ($_POST['action'] === 'publish') {
        $internship_id = intval($_POST['internship_id'] ?? 0);
        $update = $conn->prepare("UPDATE internships SET is_published = 1 WHERE id = ? AND created_by = ?");
        $update->bind_param("ii", $internship_id, $admin_id);
        if ($update->execute()) {
            $_SESSION['success'] = 'Internship published successfully';
        }
        $update->close();
    }
    
    // Handle delete action
    if ($_POST['action'] === 'delete') {
        $internship_id = intval($_POST['internship_id'] ?? 0);
        $delete = $conn->prepare("DELETE FROM internships WHERE id = ? AND created_by = ?");
        $delete->bind_param("ii", $internship_id, $admin_id);
        if ($delete->execute()) {
            $_SESSION['success'] = 'Internship deleted successfully';
        }
        $delete->close();
    }
    
    header("Location: manage.php");
    exit();
}

// Get all internships created by this admin
$internships = $conn->prepare("
    SELECT i.*, 
           (SELECT COUNT(*) FROM applications WHERE internship_id = i.id) as total_applications,
           (SELECT COUNT(*) FROM applications WHERE internship_id = i.id AND status = 'Approved') as approved_count
    FROM internships i
    WHERE i.created_by = ?
    ORDER BY i.created_at DESC
");
$internships->bind_param("i", $admin_id);
$internships->execute();
$result = $internships->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Internships - Admin</title>
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
        
        .internship-card {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 15px;
            transition: all 0.3s;
        }
        
        .internship-card:hover {
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .internship-title {
            font-size: 18px;
            font-weight: 600;
            color: var(--primary-color);
        }
        
        .badge-status {
            font-size: 12px;
            padding: 5px 10px;
        }
        
        .modal-content {
            border-radius: 10px;
        }
        
        .form-label {
            font-weight: 500;
            color: #333;
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
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2>Manage Internships</h2>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">
                            <i class="bi bi-plus-circle"></i> Create New
            </button>
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
        
        <!-- Internships List -->
        <div id="internshipsList">
            <?php while ($internship = $result->fetch_assoc()): ?>
                <div class="internship-card">
                    <div class="row">
                        <div class="col-md-8">
                            <h5 class="internship-title"><?= $internship['title'] ?></h5>
                            <p class="text-muted small mb-2">
                                <strong>Role:</strong> <?= $internship['role'] ?> | 
                                <strong>Duration:</strong> <?= $internship['duration_min'] ?>-<?= $internship['duration_max'] ?> months | 
                                <strong>Type:</strong> <?= $internship['internship_type'] ?>
                            </p>
                            <p class="mb-2"><?= substr($internship['description'], 0, 150) ?>...</p>
                            <div>
                                <span class="badge badge-status bg-info">Applications: <?= $internship['total_applications'] ?></span>
                                <span class="badge badge-status bg-success">Approved: <?= $internship['approved_count'] ?></span>
                                <?php if ($internship['is_published']): ?>
                                    <span class="badge badge-status bg-success">Published</span>
                                <?php else: ?>
                                    <span class="badge badge-status bg-warning">Draft</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="viewInternship(<?= $internship['id'] ?>)">
                                    <i class="bi bi-eye"></i> View
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="editInternship(<?= $internship['id'] ?>)">
                                    <i class="bi bi-pencil"></i> Edit
                                </button>
                                <?php if (!$internship['is_published']): ?>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="action" value="publish">
                                        <input type="hidden" name="internship_id" value="<?= $internship['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-success" onclick="return confirm('Publish this internship?')">
                                            <i class="bi bi-cloud-upload"></i> Publish
                                        </button>
                                    </form>
                                <?php endif; ?>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="internship_id" value="<?= $internship['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this internship?')">
                                        <i class="bi bi-trash"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
                    </div>
        
                    <?php if ($result->num_rows === 0): ?>
                        <div class="alert alert-info text-center">
                            <i class="bi bi-info-circle"></i> No internships created yet. <a href="#" onclick="document.getElementById('createBtn').click()">Create one now</a>
                        </div>
                    <?php endif; ?>
                </div>
            </main>
        </div>
    </div>
    
    <!-- Create/Edit Modal -->
    <div class="modal fade" id="createModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create Internship</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" id="internshipForm">
                        <input type="hidden" name="action" value="create">
                        
                        <div class="mb-3">
                            <label class="form-label">Internship Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="title" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Description <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="description" rows="4" required></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Role <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="role" placeholder="e.g., Web Developer" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Internship Type <span class="text-danger">*</span></label>
                                <select class="form-control" name="internship_type" required>
                                    <option value="">Select Type</option>
                                    <option value="Task-based">Task-based</option>
                                    <option value="Learning">Learning</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Duration From (months)</label>
                                <input type="number" class="form-control" name="duration_min" value="1" min="1" max="4">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Duration To (months)</label>
                                <input type="number" class="form-control" name="duration_max" value="4" min="1" max="4">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Remote/Hybrid</label>
                                <select class="form-control" name="remote" required>
                                    <option value="">Select</option>
                                    <option value="Yes">Fully Remote</option>
                                    <option value="No">On-site</option>
                                    <option value="Hybrid">Hybrid</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Skills Required</label>
                            <textarea class="form-control" name="skills_required" rows="2" placeholder="e.g., HTML, CSS, JavaScript, React"></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Number of Positions</label>
                            <input type="number" class="form-control" name="number_of_positions" value="1" min="1">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" form="internshipForm">Create Internship</button>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function viewInternship(id) {
            window.location.href = 'view.php?id=' + id;
        }
        
        function editInternship(id) {
            window.location.href = 'edit.php?id=' + id;
        }
        
        document.getElementById('createBtn')?.addEventListener('click', function() {
            document.getElementById('createModal').click();
        });
    </script>
</body>
</html>
