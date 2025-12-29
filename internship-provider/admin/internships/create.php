<?php
/**
 * Create Internship
 * Admin creates new internship postings
 */

require_once '../../includes/config.php';
require_once '../../includes/session.php';
require_once '../../includes/validation.php';

// Check if user is admin
requireAdmin();

$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get and sanitize input
    $title = sanitizeInput($_POST['title'] ?? '');
    $description = sanitizeInput($_POST['description'] ?? '');
    $role = sanitizeInput($_POST['role'] ?? '');
    $duration_min = (int)($_POST['duration_min'] ?? 0);
    $duration_max = (int)($_POST['duration_max'] ?? 0);
    $internship_type = sanitizeInput($_POST['internship_type'] ?? '');
    $remote = sanitizeInput($_POST['remote'] ?? '');
    $skills_required = sanitizeInput($_POST['skills_required'] ?? '');
    $number_of_positions = (int)($_POST['number_of_positions'] ?? 1);
    
    // Validate required fields
    if (empty($title) || empty($description) || empty($role)) {
        $error = 'Please fill all required fields';
    } elseif ($duration_min < 1 || $duration_max < 1) {
        $error = 'Duration must be at least 1 month';
    } elseif ($duration_min > $duration_max) {
        $error = 'Minimum duration cannot be greater than maximum duration';
    } else {
        // Insert internship
        $stmt = $conn->prepare("INSERT INTO internships (title, description, role, duration_min, duration_max, internship_type, remote, skills_required, number_of_positions, is_published, created_by) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        $is_published = 0;
        $stmt->bind_param("sssiisissii", 
            $title, $description, $role, $duration_min, $duration_max, 
            $internship_type, $remote, $skills_required, $number_of_positions, $is_published, $_SESSION['user_id']
        );
        
        if ($stmt->execute()) {
            $success = 'Internship created successfully! You can publish it from the Manage page.';
            // Clear form
            $_POST = [];
        } else {
            $error = 'Error creating internship: ' . $stmt->error;
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Internship - Admin</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar Navigation -->
            <nav class="col-md-2 bg-dark sidebar p-0">
                <div class="sidebar-sticky">
                    <h5 class="text-white p-3 border-bottom">Admin Panel</h5>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link text-white" href="../dashboard.php">📊 Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white active" href="../internships/manage.php">💼 Internships</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="../applications/view.php">📝 Applications</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="../tasks/manage.php">📋 Tasks & Levels</a>
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
                <div class="d-flex justify-content-between align-items-center my-4">
                    <h2>Create New Internship</h2>
                    <a href="manage.php" class="btn btn-secondary">← Back to Internships</a>
                </div>

                <!-- Alert Messages -->
                <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>❌ Error!</strong> <?php echo $error; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong>✓ Success!</strong> <?php echo $success; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Create Form -->
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <form method="POST" action="">
                            <div class="row">
                                <!-- Title -->
                                <div class="col-md-6 mb-3">
                                    <label for="title" class="form-label">Internship Title *</label>
                                    <input type="text" class="form-control" id="title" name="title" 
                                           value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>" 
                                           required placeholder="e.g., Junior Web Developer">
                                    <small class="text-muted">What is the position called?</small>
                                </div>

                                <!-- Role -->
                                <div class="col-md-6 mb-3">
                                    <label for="role" class="form-label">Role *</label>
                                    <input type="text" class="form-control" id="role" name="role" 
                                           value="<?php echo htmlspecialchars($_POST['role'] ?? ''); ?>" 
                                           required placeholder="e.g., Web Developer">
                                    <small class="text-muted">Job role/position</small>
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="mb-3">
                                <label for="description" class="form-label">Description *</label>
                                <textarea class="form-control" id="description" name="description" 
                                          rows="4" required placeholder="Describe the internship..."><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                                <small class="text-muted">Detailed description of the internship</small>
                            </div>

                            <div class="row">
                                <!-- Duration Min -->
                                <div class="col-md-3 mb-3">
                                    <label for="duration_min" class="form-label">Min Duration (months) *</label>
                                    <input type="number" class="form-control" id="duration_min" name="duration_min" 
                                           value="<?php echo htmlspecialchars($_POST['duration_min'] ?? ''); ?>" 
                                           min="1" max="12" required>
                                </div>

                                <!-- Duration Max -->
                                <div class="col-md-3 mb-3">
                                    <label for="duration_max" class="form-label">Max Duration (months) *</label>
                                    <input type="number" class="form-control" id="duration_max" name="duration_max" 
                                           value="<?php echo htmlspecialchars($_POST['duration_max'] ?? ''); ?>" 
                                           min="1" max="12" required>
                                </div>

                                <!-- Type -->
                                <div class="col-md-3 mb-3">
                                    <label for="internship_type" class="form-label">Type *</label>
                                    <select class="form-select" id="internship_type" name="internship_type" required>
                                        <option value="">-- Select Type --</option>
                                        <option value="Task-based" <?php echo ($_POST['internship_type'] ?? '') === 'Task-based' ? 'selected' : ''; ?>>Task-based</option>
                                        <option value="Learning" <?php echo ($_POST['internship_type'] ?? '') === 'Learning' ? 'selected' : ''; ?>>Learning</option>
                                    </select>
                                </div>

                                <!-- Remote -->
                                <div class="col-md-3 mb-3">
                                    <label for="remote" class="form-label">Remote *</label>
                                    <select class="form-select" id="remote" name="remote" required>
                                        <option value="">-- Select --</option>
                                        <option value="Yes" <?php echo ($_POST['remote'] ?? '') === 'Yes' ? 'selected' : ''; ?>>Yes (Fully Remote)</option>
                                        <option value="No" <?php echo ($_POST['remote'] ?? '') === 'No' ? 'selected' : ''; ?>>No (On-site)</option>
                                        <option value="Hybrid" <?php echo ($_POST['remote'] ?? '') === 'Hybrid' ? 'selected' : ''; ?>>Hybrid</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Skills Required -->
                            <div class="mb-3">
                                <label for="skills_required" class="form-label">Skills Required</label>
                                <textarea class="form-control" id="skills_required" name="skills_required" 
                                          rows="3" placeholder="e.g., HTML5, CSS3, JavaScript, PHP, MySQL"><?php echo htmlspecialchars($_POST['skills_required'] ?? ''); ?></textarea>
                                <small class="text-muted">Comma-separated list of required skills</small>
                            </div>

                            <!-- Positions -->
                            <div class="mb-3">
                                <label for="number_of_positions" class="form-label">Number of Positions</label>
                                <input type="number" class="form-control" id="number_of_positions" name="number_of_positions" 
                                       value="<?php echo htmlspecialchars($_POST['number_of_positions'] ?? '1'); ?>" 
                                       min="1" max="100">
                                <small class="text-muted">How many positions are available?</small>
                            </div>

                            <!-- Submit Buttons -->
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check"></i> Create Internship
                                </button>
                                <a href="manage.php" class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Info Box -->
                <div class="alert alert-info mt-4">
                    <strong>ℹ️ Note:</strong> After creating the internship, you can publish it from the Manage Internships page. Only published internships will be visible to users.
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
