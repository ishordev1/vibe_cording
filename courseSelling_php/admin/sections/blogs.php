<?php
// Handle delete
if(isset($_GET['delete'])) {
    $blog_id = intval($_GET['delete']);
    $conn->query("DELETE FROM blogs WHERE id=$blog_id");
    $message = "Blog post deleted successfully!";
    $messageType = "success";
}

// Handle create/edit form submission
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_blog'])) {
    $title = htmlspecialchars($_POST['title'] ?? '');
    $content = htmlspecialchars($_POST['content'] ?? '');
    $status = $_POST['status'] ?? 'draft';
    $blog_id = intval($_POST['blog_id'] ?? 0);
    
    if(empty($title) || empty($content)) {
        $message = "Please fill in all required fields!";
        $messageType = "error";
    } else {
        $author_id = $_SESSION['user_id'];
        
        if($blog_id > 0) {
            // Update existing blog
            $conn->query("UPDATE blogs SET title='$title', content='$content', status='$status' WHERE id=$blog_id");
            $message = "Blog post updated successfully!";
            $messageType = "success";
        } else {
            // Create new blog
            $created_at = date('Y-m-d H:i:s');
            $conn->query("INSERT INTO blogs (title, content, author_id, status, created_at) VALUES ('$title', '$content', $author_id, '$status', '$created_at')");
            $message = "Blog post created successfully!";
            $messageType = "success";
        }
    }
}

// Check if editing or creating
$is_editing = isset($_GET['edit']);
$edit_blog = null;

if($is_editing) {
    $blog_id = intval($_GET['edit']);
    $result = $conn->query("SELECT * FROM blogs WHERE id=$blog_id");
    if($result && $result->num_rows > 0) {
        $edit_blog = $result->fetch_assoc();
    }
}

// Show create/edit form if needed
if(isset($_GET['create']) || $is_editing) {
    ?>
    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js"></script>
    <script>
    tinymce.init({
        selector: '#blog-content',
        plugins: 'lists link image table code help wordcount',
        toolbar: 'undo redo | bold italic underline | bullist numlist | link image table | removeformat | code | help',
        menubar: 'file edit view insert format tools help',
        height: 400,
        setup: function(editor) {
            editor.on('init', function() {
                // Initialize with existing content if editing
            });
        }
    });
    </script>
    
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h3 class="text-2xl font-bold text-gray-900 mb-6">
            <i class="fas fa-pen-fancy mr-2 text-purple-600"></i><?php echo $is_editing ? 'Edit Blog Post' : 'Create New Blog Post'; ?>
        </h3>
        
        <form method="POST" class="space-y-6">
            <input type="hidden" name="save_blog" value="1">
            <?php if($is_editing): ?>
                <input type="hidden" name="blog_id" value="<?php echo $edit_blog['id']; ?>">
            <?php endif; ?>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Title</label>
                <input type="text" name="title" value="<?php echo $is_editing ? htmlspecialchars($edit_blog['title']) : ''; ?>" 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-600" 
                       placeholder="Enter blog title" required>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Content</label>
                <textarea id="blog-content" name="content" class="w-full px-4 py-2 border border-gray-300 rounded-lg" 
                          placeholder="Enter blog content" required><?php echo $is_editing ? $edit_blog['content'] : ''; ?></textarea>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-600">
                    <option value="draft" <?php echo (!$is_editing || $edit_blog['status'] === 'draft') ? 'selected' : ''; ?>>Draft</option>
                    <option value="published" <?php echo ($is_editing && $edit_blog['status'] === 'published') ? 'selected' : ''; ?>>Published</option>
                </select>
            </div>
            
            <div class="flex gap-3">
                <button type="submit" class="px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition font-semibold flex items-center gap-2">
                    <i class="fas fa-save"></i><?php echo $is_editing ? 'Update Blog' : 'Create Blog'; ?>
                </button>
                <a href="?section=blogs" class="px-6 py-3 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition font-semibold flex items-center gap-2">
                    <i class="fas fa-times"></i>Cancel
                </a>
            </div>
        </form>
    </div>
    <?php
}

// Get all blogs
$blogs = $conn->query("
    SELECT b.*, u.name as author_name
    FROM blogs b
    JOIN users u ON b.author_id = u.id
    ORDER BY b.created_at DESC
");
?>

<div class="mb-6">
    <a href="?section=blogs&create=new" class="inline-flex items-center px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition font-semibold">
        <i class="fas fa-plus mr-2"></i>Create New Blog Post
    </a>
</div>

<div class="bg-white rounded-lg shadow-md p-6">
    <h3 class="text-lg font-bold text-gray-900 mb-4">
        <i class="fas fa-blog mr-2 text-green-600"></i>Blog Posts
    </h3>
    
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="border-b border-gray-200">
                    <th class="px-4 py-3 font-semibold text-gray-700">Title</th>
                    <th class="px-4 py-3 font-semibold text-gray-700">Author</th>
                    <th class="px-4 py-3 font-semibold text-gray-700">Status</th>
                    <th class="px-4 py-3 font-semibold text-gray-700">Views</th>
                    <th class="px-4 py-3 font-semibold text-gray-700">Published</th>
                    <th class="px-4 py-3 font-semibold text-gray-700">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if($blogs->num_rows > 0) {
                    while($blog = $blogs->fetch_assoc()) {
                        $statusColor = $blog['status'] === 'published' ? 'green' : 'gray';
                        echo "
                        <tr class='border-b border-gray-100 hover:bg-gray-50'>
                            <td class='px-4 py-3'>
                                <div class='font-semibold text-gray-900'>" . htmlspecialchars($blog['title']) . "</div>
                                <div class='text-sm text-gray-600'>" . htmlspecialchars(substr($blog['content'], 0, 80)) . "...</div>
                            </td>
                            <td class='px-4 py-3 text-gray-600'>" . htmlspecialchars($blog['author_name']) . "</td>
                            <td class='px-4 py-3'>
                                <span class='px-3 py-1 bg-{$statusColor}-100 text-{$statusColor}-700 rounded-full text-xs font-semibold'>
                                    " . ucfirst($blog['status']) . "
                                </span>
                            </td>
                            <td class='px-4 py-3 font-semibold text-gray-900'>" . $blog['views'] . "</td>
                            <td class='px-4 py-3 text-sm text-gray-600'>" . date('M d, Y', strtotime($blog['created_at'])) . "</td>
                            <td class='px-4 py-3 space-x-2 flex gap-2'>
                                <a href='?section=blogs&edit=" . $blog['id'] . "' class='inline-flex items-center px-3 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition' title='Edit'>
                                    <i class='fas fa-edit'></i>
                                </a>
                                <a href='?section=blogs&delete=" . $blog['id'] . "' onclick='return confirm(\"Delete this blog post?\")' class='inline-flex items-center px-3 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition' title='Delete'>
                                    <i class='fas fa-trash'></i>
                                </a>
                            </td>
                        </tr>
                        ";
                    }
                } else {
                    echo "<tr><td colspan='6' class='px-4 py-3 text-center text-gray-600'>No blog posts found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
