<?php
/**
 * Edit Internship
 * Admin edits internship details
 */

require_once '../../includes/config.php';
require_once '../../includes/session.php';
require_once '../../includes/validation.php';

requireAdmin();

$internship_id = (int)($_GET['id'] ?? 0);
$error = '';
$success = '';

if ($internship_id === 0) {
    header("Location: manage.php");
    exit();
}

// Get internship details
$stmt = $conn->prepare("SELECT * FROM internships WHERE id = ? AND created_by = ?");
$stmt->bind_param("ii", $internship_id, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$internship = $result->fetch_assoc();

if (!$internship) {
    header("Location: manage.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitizeInput($_POST['title'] ?? '');
    $description = sanitizeInput($_POST['description'] ?? '');
    $role = sanitizeInput($_POST['role'] ?? '');
    $duration_min = (int)($_POST['duration_min'] ?? 0);
    $duration_max = (int)($_POST['duration_max'] ?? 0);
    $internship_type = sanitizeInput($_POST['internship_type'] ?? '');
    $remote = sanitizeInput($_POST['remote'] ?? '');
    $skills_required = sanitizeInput($_POST['skills_required'] ?? '');
    $number_of_positions = (int)($_POST['number_of_positions'] ?? 1);
    
    // Validate
    if (empty($title) || empty($description) || empty($role)) {
        $error = 'Please fill all required fields';
    } elseif ($duration_min < 1 || $duration_max < 1) {
        $error = 'Duration must be at least 1 month';
    } elseif ($duration_min > $duration_max) {
        $error = 'Minimum duration cannot be greater than maximum duration';
    } else {
        // Update internship
        $update = $conn->prepare("UPDATE internships SET title=?, description=?, role=?, duration_min=?, duration_max=?, internship_type=?, remote=?, skills_required=?, number_of_positions=? WHERE id=? AND created_by=?");
        
        $update->bind_param("sssiisissii", 
            $title, $description, $role, $duration_min, $duration_max,
            $internship_type, $remote, $skills_required, $number_of_positions, $internship_id, $_SESSION['user_id']
        );
        
        if ($update->execute()) {
            $success = 'Internship updated successfully!';
            // Refresh internship data
            $stmt = $conn->prepare("SELECT * FROM internships WHERE id = ? AND created_by = ?");
            $stmt->bind_param("ii", $internship_id, $_SESSION['user_id']);
            $stmt->execute();
            $result = $stmt->get_result();
            $internship = $result->fetch_assoc();
        } else {
            $error = 'Error updating internship: ' . $update->error;
        }
        $update->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Internship - Admin</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <div class="container mt-5 mb-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Edit Internship</h2>
            <a href="view.php?id=<?php echo $internship_id; ?>" class="btn btn-secondary">← Back</a>
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

        <!-- Edit Form -->
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <form method="POST" action="">
                    <div class="row">
                        <!-- Title -->
                        <div class="col-md-6 mb-3">
                            <label for="title" class="form-label">Internship Title *</label>
                            <input type="text" class="form-control" id="title" name="title" 
                                   value="<?php echo htmlspecialchars($internship['title']); ?>" 
                                   required>
                        </div>

                        <!-- Role -->
                        <div class="col-md-6 mb-3">
                            <label for="role" class="form-label">Role *</label>
                            <input type="text" class="form-control" id="role" name="role" 
                                   value="<?php echo htmlspecialchars($internship['role']); ?>" 
                                   required>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="mb-3">
                        <label for="description" class="form-label">Description *</label>
                        <textarea class="form-control" id="description" name="description" 
                                  rows="4" required><?php echo htmlspecialchars($internship['description']); ?></textarea>
                    </div>

                    <div class="row">
                        <!-- Duration Min -->
                        <div class="col-md-3 mb-3">
                            <label for="duration_min" class="form-label">Min Duration (months) *</label>
                            <input type="number" class="form-control" id="duration_min" name="duration_min" 
                                   value="<?php echo $internship['duration_min']; ?>" 
                                   min="1" max="12" required>
                        </div>

                        <!-- Duration Max -->
                        <div class="col-md-3 mb-3">
                            <label for="duration_max" class="form-label">Max Duration (months) *</label>
                            <input type="number" class="form-control" id="duration_max" name="duration_max" 
                                   value="<?php echo $internship['duration_max']; ?>" 
                                   min="1" max="12" required>
                        </div>

                        <!-- Type -->
                        <div class="col-md-3 mb-3">
                            <label for="internship_type" class="form-label">Type *</label>
                            <select class="form-select" id="internship_type" name="internship_type" required>
                                <option value="Task-based" <?php echo $internship['internship_type'] === 'Task-based' ? 'selected' : ''; ?>>Task-based</option>
                                <option value="Learning" <?php echo $internship['internship_type'] === 'Learning' ? 'selected' : ''; ?>>Learning</option>
                            </select>
                        </div>

                        <!-- Remote -->
                        <div class="col-md-3 mb-3">
                            <label for="remote" class="form-label">Remote *</label>
                            <select class="form-select" id="remote" name="remote" required>
                                <option value="Yes" <?php echo $internship['remote'] === 'Yes' ? 'selected' : ''; ?>>Yes (Fully Remote)</option>
                                <option value="No" <?php echo $internship['remote'] === 'No' ? 'selected' : ''; ?>>No (On-site)</option>
                                <option value="Hybrid" <?php echo $internship['remote'] === 'Hybrid' ? 'selected' : ''; ?>>Hybrid</option>
                            </select>
                        </div>
                    </div>

                    <!-- Skills Required -->
                    <div class="mb-3">
                        <label for="skills_required" class="form-label">Skills Required</label>
                        <textarea class="form-control" id="skills_required" name="skills_required" 
                                  rows="3"><?php echo htmlspecialchars($internship['skills_required']); ?></textarea>
                        <small class="text-muted">Comma-separated list</small>
                    </div>

                    <!-- Positions -->
                    <div class="mb-3">
                        <label for="number_of_positions" class="form-label">Number of Positions</label>
                        <input type="number" class="form-control" id="number_of_positions" name="number_of_positions" 
                               value="<?php echo $internship['number_of_positions']; ?>" 
                               min="1" max="100">
                    </div>

                    <!-- Submit Buttons -->
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            💾 Save Changes
                        </button>
                        <a href="view.php?id=<?php echo $internship_id; ?>" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Info Box -->
        <div class="alert alert-info mt-4">
            <strong>ℹ️ Note:</strong> Changes will be saved immediately. Already published internships will remain published.
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
