<?php
/**
 * View Internship Details
 * Admin views internship details and statistics
 */

require_once '../../includes/config.php';
require_once '../../includes/session.php';

requireAdmin();

$internship_id = (int)($_GET['id'] ?? 0);

if ($internship_id === 0) {
    header("Location: manage.php");
    exit();
}

// Get internship details
$stmt = $conn->prepare("
    SELECT i.*, 
           u.full_name as created_by_name,
           (SELECT COUNT(*) FROM applications WHERE internship_id = i.id) as total_applications,
           (SELECT COUNT(*) FROM applications WHERE internship_id = i.id AND status = 'Pending') as pending_apps,
           (SELECT COUNT(*) FROM applications WHERE internship_id = i.id AND status = 'Approved') as approved_apps,
           (SELECT COUNT(*) FROM applications WHERE internship_id = i.id AND status = 'Rejected') as rejected_apps
    FROM internships i
    JOIN users u ON i.created_by = u.id
    WHERE i.id = ? AND i.created_by = ?
");
$stmt->bind_param("ii", $internship_id, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$internship = $result->fetch_assoc();

if (!$internship) {
    header("Location: manage.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Internship - Admin</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <style>
        .info-box { background: #f8f9fa; padding: 15px; border-radius: 5px; margin-bottom: 15px; }
        .label-strong { font-weight: 600; color: #333; }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Internship Details</h2>
            <a href="manage.php" class="btn btn-secondary">← Back to Internships</a>
        </div>

        <div class="row">
            <div class="col-md-8">
                <!-- Basic Information -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><?php echo htmlspecialchars($internship['title']); ?></h5>
                    </div>
                    <div class="card-body">
                        <div class="info-box">
                            <p class="mb-2">
                                <span class="label-strong">Role:</span> <?php echo htmlspecialchars($internship['role']); ?>
                            </p>
                            <p class="mb-2">
                                <span class="label-strong">Duration:</span> <?php echo $internship['duration_min']; ?>-<?php echo $internship['duration_max']; ?> months
                            </p>
                            <p class="mb-2">
                                <span class="label-strong">Type:</span> 
                                <span class="badge bg-info"><?php echo htmlspecialchars($internship['internship_type']); ?></span>
                            </p>
                            <p class="mb-0">
                                <span class="label-strong">Remote:</span> 
                                <span class="badge bg-secondary"><?php echo htmlspecialchars($internship['remote']); ?></span>
                            </p>
                        </div>

                        <div class="mb-3">
                            <h6 class="label-strong">Description:</h6>
                            <p><?php echo nl2br(htmlspecialchars($internship['description'])); ?></p>
                        </div>

                        <div class="mb-3">
                            <h6 class="label-strong">Skills Required:</h6>
                            <p><?php echo htmlspecialchars($internship['skills_required'] ?: 'Not specified'); ?></p>
                        </div>

                        <div class="info-box">
                            <p class="mb-0">
                                <span class="label-strong">Positions Available:</span> <?php echo $internship['number_of_positions']; ?>
                            </p>
                        </div>

                        <div class="info-box">
                            <p class="mb-2">
                                <span class="label-strong">Status:</span>
                                <?php if ($internship['is_published']): ?>
                                    <span class="badge bg-success">✓ Published</span>
                                <?php else: ?>
                                    <span class="badge bg-warning">Draft</span>
                                <?php endif; ?>
                            </p>
                            <p class="mb-0">
                                <span class="label-strong">Created:</span> <?php echo date('d M Y', strtotime($internship['created_at'])); ?>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Applications Statistics -->
                <div class="card shadow-sm">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0">Application Statistics</h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-3">
                                <div class="info-box">
                                    <h3 class="text-primary"><?php echo $internship['total_applications']; ?></h3>
                                    <p class="mb-0">Total Applications</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-box">
                                    <h3 class="text-warning"><?php echo $internship['pending_apps']; ?></h3>
                                    <p class="mb-0">Pending</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-box">
                                    <h3 class="text-success"><?php echo $internship['approved_apps']; ?></h3>
                                    <p class="mb-0">Approved</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-box">
                                    <h3 class="text-danger"><?php echo $internship['rejected_apps']; ?></h3>
                                    <p class="mb-0">Rejected</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions Sidebar -->
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">Actions</h5>
                    </div>
                    <div class="card-body">
                        <a href="edit.php?id=<?php echo $internship['id']; ?>" class="btn btn-warning w-100 mb-2">
                            <i class="bi bi-pencil"></i> Edit Internship
                        </a>
                        
                        <?php if (!$internship['is_published']): ?>
                            <form method="POST" action="manage.php" style="margin-bottom: 10px;">
                                <input type="hidden" name="action" value="publish">
                                <input type="hidden" name="internship_id" value="<?php echo $internship['id']; ?>">
                                <button type="submit" class="btn btn-success w-100" onclick="return confirm('Publish this internship?')">
                                    <i class="bi bi-cloud-upload"></i> Publish
                                </button>
                            </form>
                        <?php else: ?>
                            <button class="btn btn-success w-100 mb-2 disabled">
                                <i class="bi bi-check"></i> Published
                            </button>
                        <?php endif; ?>
                        
                        <a href="../applications/view.php?internship_id=<?php echo $internship['id']; ?>" class="btn btn-info w-100 mb-2">
                            <i class="bi bi-file-earmark-text"></i> View Applications
                        </a>

                        <form method="POST" action="manage.php">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="internship_id" value="<?php echo $internship['id']; ?>">
                            <button type="submit" class="btn btn-danger w-100" onclick="return confirm('Delete this internship? This cannot be undone.')">
                                <i class="bi bi-trash"></i> Delete
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Info Box -->
                <div class="alert alert-info mt-3">
                    <strong>ℹ️ Tip:</strong> You can edit the internship details or publish it when ready to receive applications.
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
