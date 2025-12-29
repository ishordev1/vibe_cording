<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';

$pageTitle = 'Edit Idea - ' . APP_NAME;
require_once __DIR__ . '/../../includes/header.php';

// Require creator login
requireUserType(USER_TYPE_CREATOR);

$conn = getDBConnection();
$user_id = getCurrentUserId();
$idea_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Get idea details
$stmt = $conn->prepare("SELECT * FROM ideas WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $idea_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    setFlashMessage('error', 'Idea not found or you do not have permission to edit it.');
    redirect('/pages/creator/dashboard.php');
}

$idea = $result->fetch_assoc();

// Get existing images
$stmt = $conn->prepare("SELECT * FROM idea_media WHERE idea_id = ? ORDER BY is_primary DESC");
$stmt->bind_param("i", $idea_id);
$stmt->execute();
$images = $stmt->get_result();
?>

<div class="container mt-4 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-body p-4">
                    <h2 class="mb-4">Edit Business Idea</h2>
                    
                    <form id="editIdeaForm" method="POST" action="<?php echo APP_URL; ?>/actions/edit-idea-action.php" enctype="multipart/form-data">
                        <input type="hidden" name="idea_id" value="<?php echo $idea['id']; ?>">
                        
                        <div class="mb-3">
                            <label for="title" class="form-label">Idea Title *</label>
                            <input type="text" class="form-control" id="title" name="title" required 
                                   value="<?php echo htmlspecialchars($idea['title']); ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="category" class="form-label">Category *</label>
                            <select class="form-select" id="category" name="category" required>
                                <option value="">Select a category</option>
                                <?php foreach (IDEA_CATEGORIES as $cat): ?>
                                    <option value="<?php echo $cat; ?>" <?php echo $idea['category'] === $cat ? 'selected' : ''; ?>>
                                        <?php echo $cat; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Description *</label>
                            <textarea class="form-control" id="description" name="description" rows="8" required><?php echo renderHTML($idea['description']); ?></textarea>
                            <small class="form-text text-muted">Use the editor toolbar to format your text.</small>
                        </div>
                        
                        <!-- Existing Images -->
                        <?php if ($images->num_rows > 0): ?>
                        <div class="mb-3">
                            <label class="form-label">Current Images</label>
                            <div class="row">
                                <?php while ($img = $images->fetch_assoc()): ?>
                                <div class="col-md-4 mb-2">
                                    <div class="position-relative">
                                        <img src="<?php echo APP_URL . '/' . $img['file_path']; ?>" 
                                             class="img-thumbnail" alt="Idea image">
                                        <?php if ($img['is_primary']): ?>
                                            <span class="position-absolute top-0 start-0 badge bg-primary m-1">Primary</span>
                                        <?php endif; ?>
                                        <div class="form-check mt-1">
                                            <input class="form-check-input" type="checkbox" name="delete_images[]" 
                                                   value="<?php echo $img['id']; ?>" id="delete_<?php echo $img['id']; ?>">
                                            <label class="form-check-label" for="delete_<?php echo $img['id']; ?>">
                                                Delete
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <?php endwhile; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <div class="mb-3">
                            <label for="images" class="form-label">Add New Images (Optional)</label>
                            <input type="file" class="form-control" id="images" name="images[]" multiple accept="image/*">
                            <small class="form-text text-muted">You can upload multiple images. Max 5MB per image.</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="status" class="form-label">Status *</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="published" <?php echo $idea['status'] === 'published' ? 'selected' : ''; ?>>
                                    Published (Visible to investors)
                                </option>
                                <option value="draft" <?php echo $idea['status'] === 'draft' ? 'selected' : ''; ?>>
                                    Draft (Save for later)
                                </option>
                                <option value="archived" <?php echo $idea['status'] === 'archived' ? 'selected' : ''; ?>>
                                    Archived (Hidden)
                                </option>
                            </select>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-check-circle"></i> Update Idea
                            </button>
                            <a href="<?php echo APP_URL; ?>/pages/creator/dashboard.php" class="btn btn-outline-secondary">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.tiny.cloud/1/hxh6hifgyu13rft1ezwxlm0r4euznmqg4kaeqgrlon7qejzl/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
tinymce.init({
    selector: '#description',
    height: 600, // Height badha di, full features ke liye achha hai
    
    
    // ✅ MAXIMUM PLUGINS Add kiye gaye hain
    plugins: [
        'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
        'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
        'insertdatetime', 'media', 'table', 'help', 'wordcount',
        'template', 'codesample', 'nonbreaking', 'pagebreak', 'hr' // Extra features
    ],
    
    // ✅ FULL TOOLBAR (Multiple lines for better organization)
    toolbar: [
        'undo redo | styles | formatselect | fontselect fontsizeselect | forecolor backcolor | removeformat | code fullscreen | help',
        'bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | numlist bullist outdent indent | hr pagebreak',
        'link image media table anchor charmap codesample insertdatetime template nonbreaking visualblocks preview'
    ],
    
    // Advanced Options
    content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:16px }',
    
    // Image and Media handling ko open karein
    image_title: true,
    automatic_uploads: true,
    file_picker_types: 'image media', 

    setup: function(editor) {
        editor.on('change', function() {
            editor.save();
        });
    }
});

// Form validation
document.getElementById('editIdeaForm').addEventListener('submit', function(e) {
    // Update textarea with TinyMCE content
    tinymce.triggerSave();
});
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
