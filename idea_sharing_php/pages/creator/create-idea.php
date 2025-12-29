<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';

$pageTitle = 'Create Idea - ' . APP_NAME;
require_once __DIR__ . '/../../includes/header.php';

// Require creator login
requireUserType(USER_TYPE_CREATOR);
?>

<div class="container mt-4 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-body p-4">
                    <h2 class="mb-4">Post New Business Idea</h2>
                    
                    <form id="createIdeaForm" method="POST" action="<?php echo APP_URL; ?>/actions/create-idea-action.php" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="title" class="form-label">Idea Title *</label>
                            <input type="text" class="form-control" id="title" name="title" required 
                                   placeholder="Enter a catchy title for your idea">
                        </div>
                        
                        <div class="mb-3">
                            <label for="category" class="form-label">Category *</label>
                            <select class="form-select" id="category" name="category" required>
                                <option value="">Select a category</option>
                                <?php foreach (IDEA_CATEGORIES as $cat): ?>
                                    <option value="<?php echo $cat; ?>"><?php echo $cat; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Description *</label>
                            <textarea class="form-control" id="description" name="description" rows="8" required
                                      placeholder="Describe your business idea in detail. Include the problem it solves, target market, unique value proposition, etc."></textarea>
                            <small class="form-text text-muted">Minimum 100 characters. Use the editor toolbar to format your text.</small>
                        </div>

                        
                        <div class="mb-3">
                            <label for="images" class="form-label">Images (Optional)</label>
                            <input type="file" class="form-control" id="images" name="images[]" multiple accept="image/*">
                            <small class="form-text text-muted">You can upload multiple images. Max 5MB per image.</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="status" class="form-label">Status *</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="published">Published (Visible to investors)</option>
                                <option value="draft">Draft (Save for later)</option>
                            </select>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-check-circle"></i> Post Idea
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
// Initialize TinyMCE
// tinymce.init({
//     selector: '#description',
//     height: 400,
//     menubar: false,
//     plugins: [
//         'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
//         'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
//         'insertdatetime', 'media', 'table', 'help', 'wordcount'
//     ],
//     toolbar: 'undo redo | blocks | ' +
//         'bold italic forecolor | alignleft aligncenter ' +
//         'alignright alignjustify | bullist numlist outdent indent | ' +
//         'removeformat | help',
//     content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }',
//     setup: function(editor) {
//         editor.on('change', function() {
//             editor.save();
//         });
//     }
// });


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
document.getElementById('createIdeaForm').addEventListener('submit', function(e) {
    // Update textarea with TinyMCE content
    tinymce.triggerSave();
    
    const description = document.getElementById('description').value;
    const textContent = description.replace(/<[^>]*>/g, '');
    
    if (textContent.length < 100) {
        e.preventDefault();
        alert('Description must be at least 100 characters long.');
        return false;
    }
});
</script>
       

</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
