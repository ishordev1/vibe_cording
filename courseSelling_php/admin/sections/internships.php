<?php
// Helper function to format file size
function formatFileSize($bytes) {
    if($bytes === 0) return '0 Bytes';
    $k = 1024;
    $sizes = ['Bytes', 'KB', 'MB', 'GB'];
    $i = floor(log($bytes) / log($k));
    return round(($bytes / pow($k, $i)) * 100) / 100 . ' ' . $sizes[$i];
}

// Handle Delete Internship
if(isset($_GET['delete_internship'])) {
    $internship_id = intval($_GET['delete_internship']);
    
    // Delete all modules first (cascade delete will handle this, but let's be explicit)
    $modules_to_delete = $conn->query("SELECT module_file FROM modules WHERE internship_id=$internship_id");
    while($module = $modules_to_delete->fetch_assoc()) {
        if($module['module_file']) {
            $file_path = __DIR__ . '/../../public/uploads/' . $module['module_file'];
            if(file_exists($file_path)) {
                unlink($file_path);
            }
        }
    }
    
    // Delete the internship (modules will be deleted via cascade)
    $conn->query("DELETE FROM internships WHERE id=$internship_id");
    echo "<script>window.location='?section=internships';</script>";
    exit;
}

// Process all POST/GET logic at the top before any output

// Handle Form Submission for Add/Edit
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(isset($_POST['create_internship']) || isset($_POST['update_internship'])) {
        $title = $conn->real_escape_string($_POST['title']);
        $duration = $conn->real_escape_string($_POST['duration']);
        $fees = floatval($_POST['fees']);
        $skills = $conn->real_escape_string($_POST['skills']);
        $description = $conn->real_escape_string($_POST['description']);
        $benefits = $conn->real_escape_string($_POST['benefits'] ?? '');
        
        if(isset($_POST['create_internship'])) {
            $slug = strtolower(str_replace(' ', '-', $title));
            $query = "INSERT INTO internships (title, slug, description, skills, duration, fees, benefits) 
                      VALUES ('$title', '$slug', '$description', '$skills', '$duration', $fees, '$benefits')";
            if($conn->query($query)) {
                echo "<script>window.location='?section=internships';</script>";
                exit;
            }
        } elseif(isset($_POST['update_internship'])) {
            $id = intval($_GET['edit']);
            $query = "UPDATE internships SET title='$title', description='$description', skills='$skills', 
                      duration='$duration', fees=$fees, benefits='$benefits' WHERE id=$id";
            if($conn->query($query)) {
                echo "<script>window.location='?section=internships';</script>";
                exit;
            }
        }
    }
    
    // Handle Add/Update Module
    if(isset($_POST['add_module']) || isset($_POST['update_module'])) {
        $internship_id = intval($_GET['internship_id']);
        $title = $conn->real_escape_string($_POST['module_title']);
        $description = $conn->real_escape_string($_POST['module_description'] ?? '');
        $order_seq = intval($_POST['order_seq']);
        $module_file = null;
        
        // Handle single file upload (backward compatibility)
        if(isset($_FILES['module_file']) && $_FILES['module_file']['size'] > 0) {
            $upload_dir = __DIR__ . '/../../public/uploads/modules/';
            if(!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            $file_name = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $_FILES['module_file']['name']);
            $file_path = $upload_dir . $file_name;
            
            if(move_uploaded_file($_FILES['module_file']['tmp_name'], $file_path)) {
                $module_file = 'modules/' . $file_name;
            }
        }
        
        if(isset($_POST['add_module'])) {
            if($module_file) {
                $query = "INSERT INTO modules (internship_id, title, description, order_seq, module_file) 
                          VALUES ($internship_id, '$title', '$description', $order_seq, '$module_file')";
            } else {
                $query = "INSERT INTO modules (internship_id, title, description, order_seq) 
                          VALUES ($internship_id, '$title', '$description', $order_seq)";
            }
            if($conn->query($query)) {
                $module_id = $conn->insert_id;
                
                // Handle multiple file uploads
                if(isset($_FILES['module_files']) && is_array($_FILES['module_files']['name'])) {
                    // First, ensure module_files table exists
                    $check_table = $conn->query("SHOW TABLES LIKE 'module_files'");
                    if($check_table->num_rows == 0) {
                        // Create table if it doesn't exist
                        $conn->query("CREATE TABLE IF NOT EXISTS module_files (
                            id INT PRIMARY KEY AUTO_INCREMENT,
                            module_id INT NOT NULL,
                            file_name VARCHAR(255) NOT NULL,
                            file_path VARCHAR(255) NOT NULL,
                            file_type VARCHAR(50),
                            file_size INT,
                            upload_order INT DEFAULT 0,
                            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                            FOREIGN KEY (module_id) REFERENCES modules(id) ON DELETE CASCADE
                        )");
                    }
                    
                    $upload_dir = __DIR__ . '/../../public/uploads/modules/';
                    $file_count = count($_FILES['module_files']['name']);
                    
                    for($i = 0; $i < $file_count; $i++) {
                        if($_FILES['module_files']['size'][$i] > 0 && $_FILES['module_files']['error'][$i] === UPLOAD_ERR_OK) {
                            $file_name = time() . '_' . $i . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $_FILES['module_files']['name'][$i]);
                            $file_path = $upload_dir . $file_name;
                            $original_name = $_FILES['module_files']['name'][$i];
                            $file_type = pathinfo($original_name, PATHINFO_EXTENSION);
                            $file_size = $_FILES['module_files']['size'][$i];
                            
                            if(move_uploaded_file($_FILES['module_files']['tmp_name'][$i], $file_path)) {
                                $rel_path = 'modules/' . $file_name;
                                $conn->query("INSERT INTO module_files (module_id, file_name, file_path, file_type, file_size, upload_order) 
                                            VALUES ($module_id, '$original_name', '$rel_path', '$file_type', $file_size, $i)");
                            }
                        }
                    }
                }
                
                echo "<script>window.location='?section=internships&action=content&internship_id=$internship_id';</script>";
                exit;
            }
        } elseif(isset($_POST['update_module'])) {
            $module_id = intval($_GET['edit_module']);
            if($module_file) {
                $query = "UPDATE modules SET title='$title', description='$description', order_seq=$order_seq, module_file='$module_file' WHERE id=$module_id";
            } else {
                $query = "UPDATE modules SET title='$title', description='$description', order_seq=$order_seq WHERE id=$module_id";
            }
            if($conn->query($query)) {
                // Handle multiple file uploads
                if(isset($_FILES['module_files']) && is_array($_FILES['module_files']['name'])) {
                    // First, ensure module_files table exists
                    $check_table = $conn->query("SHOW TABLES LIKE 'module_files'");
                    if($check_table->num_rows == 0) {
                        // Create table if it doesn't exist
                        $conn->query("CREATE TABLE IF NOT EXISTS module_files (
                            id INT PRIMARY KEY AUTO_INCREMENT,
                            module_id INT NOT NULL,
                            file_name VARCHAR(255) NOT NULL,
                            file_path VARCHAR(255) NOT NULL,
                            file_type VARCHAR(50),
                            file_size INT,
                            upload_order INT DEFAULT 0,
                            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                            FOREIGN KEY (module_id) REFERENCES modules(id) ON DELETE CASCADE
                        )");
                    }
                    
                    $upload_dir = __DIR__ . '/../../public/uploads/modules/';
                    $file_count = count($_FILES['module_files']['name']);
                    
                    // Get current max order
                    $max_order_result = $conn->query("SELECT MAX(upload_order) as max_order FROM module_files WHERE module_id=$module_id");
                    $max_order = $max_order_result->fetch_assoc()['max_order'] ?? 0;
                    
                    for($i = 0; $i < $file_count; $i++) {
                        if($_FILES['module_files']['size'][$i] > 0 && $_FILES['module_files']['error'][$i] === UPLOAD_ERR_OK) {
                            $file_name = time() . '_' . $i . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $_FILES['module_files']['name'][$i]);
                            $file_path = $upload_dir . $file_name;
                            $original_name = $_FILES['module_files']['name'][$i];
                            $file_type = pathinfo($original_name, PATHINFO_EXTENSION);
                            $file_size = $_FILES['module_files']['size'][$i];
                            
                            if(move_uploaded_file($_FILES['module_files']['tmp_name'][$i], $file_path)) {
                                $rel_path = 'modules/' . $file_name;
                                $upload_order = $max_order + $i + 1;
                                $conn->query("INSERT INTO module_files (module_id, file_name, file_path, file_type, file_size, upload_order) 
                                            VALUES ($module_id, '$original_name', '$rel_path', '$file_type', $file_size, $upload_order)");
                            }
                        }
                    }
                }
                
                echo "<script>window.location='?section=internships&action=content&internship_id=$internship_id';</script>";
                exit;
            }
        }
    }
    
    // Handle Delete Module File
    if(isset($_GET['delete_module_file'])) {
        $file_id = intval($_GET['delete_module_file']);
        $module_id = intval($_GET['module_id']);
        $internship_id = intval($_GET['internship_id']);
        
        // Get file info
        $file = $conn->query("SELECT file_path FROM module_files WHERE id=$file_id")->fetch_assoc();
        if($file) {
            $file_path = __DIR__ . '/../../public/uploads/' . $file['file_path'];
            if(file_exists($file_path)) {
                unlink($file_path);
            }
        }
        
        $conn->query("DELETE FROM module_files WHERE id=$file_id");
        echo "<script>window.location='?section=internships&action=content&internship_id=$internship_id&edit_module=$module_id';</script>";
        exit;
    }
}

// Handle Delete Module
if(isset($_GET['delete_module'])) {
    $module_id = intval($_GET['delete_module']);
    $internship_id = intval($_GET['internship_id']);
    
    // Get module file to delete
    $module = $conn->query("SELECT module_file FROM modules WHERE id=$module_id")->fetch_assoc();
    if($module && $module['module_file']) {
        $file_path = __DIR__ . '/../../public/uploads/' . $module['module_file'];
        if(file_exists($file_path)) {
            unlink($file_path);
        }
    }
    
    $conn->query("DELETE FROM modules WHERE id=$module_id");
    echo "<script>window.location='?section=internships&action=content&internship_id=$internship_id';</script>";
    exit;
}

// Get data needed for current view
$internship_to_edit = null;
$internship_content = null;
$modules = null;
$module_to_edit = null;

if(isset($_GET['action']) && $_GET['action'] === 'add' && isset($_GET['edit'])) {
    $internship_id = intval($_GET['edit']);
    $internship_to_edit = $conn->query("SELECT * FROM internships WHERE id=$internship_id")->fetch_assoc();
}

if(isset($_GET['action']) && $_GET['action'] === 'content') {
    $internship_id = intval($_GET['internship_id']);
    $internship_content = $conn->query("SELECT * FROM internships WHERE id=$internship_id")->fetch_assoc();
    $modules = $conn->query("SELECT * FROM modules WHERE internship_id=$internship_id ORDER BY order_seq");
    
    // Check if editing a module
    if(isset($_GET['edit_module'])) {
        $module_id = intval($_GET['edit_module']);
        $module_to_edit = $conn->query("SELECT * FROM modules WHERE id=$module_id AND internship_id=$internship_id")->fetch_assoc();
    }
}
?>

<?php
// Handle Add/Edit Internship
if(isset($_GET['action']) && $_GET['action'] === 'add') {
    ?>
    
    <!-- Back Button -->
    <a href="?section=internships" class="inline-flex items-center mb-6 px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
        <i class="fas fa-arrow-left mr-2"></i>Back to Internships
    </a>
    
    <!-- Add/Edit Internship Form -->
    <div class="bg-white rounded-lg shadow-md p-8 max-w-4xl mx-auto">
        <h2 class="text-3xl font-bold text-gray-900 mb-6">
            <i class="fas fa-<?php echo $internship_to_edit ? 'edit' : 'plus-circle'; ?> mr-2"></i>
            <?php echo $internship_to_edit ? 'Edit Internship' : 'Add New Internship'; ?>
        </h2>
        
        <form method="POST" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Internship Title *</label>
                    <input type="text" name="title" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-purple-600" 
                           value="<?php echo $internship_to_edit ? htmlspecialchars($internship_to_edit['title']) : ''; ?>" required>
                </div>
                
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Duration *</label>
                    <input type="text" name="duration" placeholder="e.g., 3 months" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-purple-600" 
                           value="<?php echo $internship_to_edit ? htmlspecialchars($internship_to_edit['duration']) : ''; ?>" required>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Fees (Rs.) *</label>
                    <input type="number" name="fees" step="0.01" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-purple-600" 
                           value="<?php echo $internship_to_edit ? htmlspecialchars($internship_to_edit['fees']) : ''; ?>" required>
                </div>
                
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Skills Required *</label>
                    <input type="text" name="skills" placeholder="e.g., HTML, CSS, JavaScript" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-purple-600" 
                           value="<?php echo $internship_to_edit ? htmlspecialchars($internship_to_edit['skills']) : ''; ?>" required>
                </div>
            </div>
            
            <div>
                <label class="block text-gray-700 font-semibold mb-2">Description *</label>
                <textarea name="description" rows="4" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-purple-600" required><?php echo $internship_to_edit ? htmlspecialchars($internship_to_edit['description']) : ''; ?></textarea>
            </div>
            
            <div>
                <label class="block text-gray-700 font-semibold mb-2">Benefits</label>
                <textarea name="benefits" rows="3" placeholder="e.g., Offer Letter, Certificate, Course Materials" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-purple-600"><?php echo $internship_to_edit ? htmlspecialchars($internship_to_edit['benefits']) : ''; ?></textarea>
            </div>
            
            <div class="flex justify-end space-x-4 pt-4 border-t">
                <a href="?section=internships" class="px-6 py-3 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition font-semibold">
                    Cancel
                </a>
                <button type="submit" name="<?php echo $internship_to_edit ? 'update_internship' : 'create_internship'; ?>" class="px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition font-semibold">
                    <i class="fas fa-save mr-2"></i><?php echo $internship_to_edit ? 'Update Internship' : 'Create Internship'; ?>
                </button>
            </div>
        </form>
    </div>
    
    <?php
    exit;
}

// Handle Add Content
if(isset($_GET['action']) && $_GET['action'] === 'content') {
    ?>
    
    <!-- Back Button -->
    <a href="?section=internships" class="inline-flex items-center mb-6 px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
        <i class="fas fa-arrow-left mr-2"></i>Back to Internships
    </a>
    
    <!-- Content Management -->
    <div class="bg-white rounded-lg shadow-md p-8 mb-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-3xl font-bold text-gray-900">
                <i class="fas fa-book mr-2"></i><?php echo htmlspecialchars($internship_content['title']); ?> - Content
            </h2>
            <button onclick="document.getElementById('addModuleForm').classList.toggle('hidden')" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                <i class="fas fa-plus mr-2"></i>Add Module
            </button>
        </div>
        
        <!-- Add Module Form -->
        <div id="addModuleForm" class="<?php echo $module_to_edit ? '' : 'hidden'; ?> bg-gray-50 p-6 rounded-lg mb-6 border border-gray-200">
            <h3 class="text-2xl font-bold mb-4"><i class="fas fa-<?php echo $module_to_edit ? 'edit' : 'plus'; ?> mr-2"></i><?php echo $module_to_edit ? 'Edit Module' : 'Add New Module'; ?></h3>
            <form method="POST" enctype="multipart/form-data" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Module Title *</label>
                        <input type="text" name="module_title" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-purple-600" 
                               value="<?php echo $module_to_edit ? htmlspecialchars($module_to_edit['title']) : ''; ?>" required>
                    </div>
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Order Sequence *</label>
                        <input type="number" name="order_seq" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-purple-600" 
                               value="<?php echo $module_to_edit ? $module_to_edit['order_seq'] : $modules->num_rows + 1; ?>" required>
                    </div>
                </div>
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Description</label>
                    <textarea name="module_description" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-purple-600"><?php echo $module_to_edit ? htmlspecialchars($module_to_edit['description'] ?? '') : ''; ?></textarea>
                </div>
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Upload Files (PDF, DOC, etc.)</label>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-purple-600 transition cursor-pointer" onclick="document.getElementById('multiFileInput').click()">
                        <input type="file" id="multiFileInput" name="module_files[]" multiple class="hidden" accept=".pdf,.doc,.docx,.ppt,.pptx,.zip,.rar,.txt,.xls,.xlsx,.mp4,.mp3,.jpg,.jpeg,.png,.gif">
                        <div id="fileNames" class="text-gray-600">
                            <i class="fas fa-cloud-upload-alt text-3xl mb-2 text-gray-400"></i>
                            <p class="mt-2">Click to upload or drag and drop</p>
                            <p class="text-xs text-gray-500 mt-1">You can select multiple files</p>
                        </div>
                    </div>
                </div>
                
                <?php 
                // Show existing files if editing
                if($module_to_edit) {
                    $existing_files = $conn->query("SELECT * FROM module_files WHERE module_id=" . $module_to_edit['id'] . " ORDER BY upload_order");
                    if($existing_files && $existing_files->num_rows > 0) {
                        echo "<div class='bg-blue-50 p-4 rounded-lg border border-blue-200'>";
                        echo "<h4 class='font-semibold text-blue-900 mb-3'>Uploaded Files (" . $existing_files->num_rows . ")</h4>";
                        echo "<div class='space-y-2'>";
                        while($file = $existing_files->fetch_assoc()) {
                            $file_icon = 'fa-file';
                            switch(strtolower($file['file_type'])) {
                                case 'pdf': $file_icon = 'fa-file-pdf text-red-600'; break;
                                case 'doc':
                                case 'docx': $file_icon = 'fa-file-word text-blue-600'; break;
                                case 'xls':
                                case 'xlsx': $file_icon = 'fa-file-excel text-green-600'; break;
                                case 'ppt':
                                case 'pptx': $file_icon = 'fa-file-powerpoint text-orange-600'; break;
                                case 'zip':
                                case 'rar': $file_icon = 'fa-file-archive text-yellow-600'; break;
                                case 'mp4':
                                case 'mp3': $file_icon = 'fa-file-video text-purple-600'; break;
                                case 'jpg':
                                case 'jpeg':
                                case 'png':
                                case 'gif': $file_icon = 'fa-file-image text-pink-600'; break;
                            }
                            echo "
                            <div class='flex items-center justify-between bg-white p-3 rounded border border-gray-200 hover:border-gray-300'>
                                <div class='flex items-center flex-1'>
                                    <i class='fas $file_icon text-xl mr-3'></i>
                                    <div class='flex-1'>
                                        <p class='font-semibold text-gray-900'>" . htmlspecialchars($file['file_name']) . "</p>
                                        <p class='text-xs text-gray-500'>Size: " . formatFileSize($file['file_size']) . " • Added: " . date('M d, Y H:i', strtotime($file['created_at'])) . "</p>
                                    </div>
                                </div>
                                <div class='flex space-x-2'>
                                    <a href='../../public/uploads/" . htmlspecialchars($file['file_path']) . "' target='_blank' class='px-2 py-1 bg-blue-600 text-white rounded text-xs hover:bg-blue-700'>
                                        <i class='fas fa-download mr-1'></i>Download
                                    </a>
                                    <a href='?section=internships&action=content&internship_id=" . intval($_GET['internship_id']) . "&delete_module_file=" . $file['id'] . "&module_id=" . $module_to_edit['id'] . "' onclick=\"return confirm('Delete this file?');\" class='px-2 py-1 bg-red-600 text-white rounded text-xs hover:bg-red-700'>
                                        <i class='fas fa-trash mr-1'></i>Delete
                                    </a>
                                </div>
                            </div>
                            ";
                        }
                        echo "</div></div>";
                    }
                }
                ?>
                
                <div id="uploadedFilesList" class="hidden bg-green-50 p-4 rounded-lg border border-green-200">
                    <h4 class="font-semibold text-green-900 mb-2">Selected Files:</h4>
                    <div id="filesList" class="space-y-2"></div>
                </div>
                <div class="flex justify-end space-x-2 pt-4 border-t">
                    <button type="button" onclick="document.getElementById('addModuleForm').classList.add('hidden'); <?php echo isset($_GET['edit_module']) ? "window.location='?section=internships&action=content&internship_id=" . intval($_GET['internship_id']) . "';" : "resetModuleForm();" ?>" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">Cancel</button>
                    <button type="submit" name="<?php echo $module_to_edit ? 'update_module' : 'add_module'; ?>" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700"><i class="fas fa-save mr-2"></i><?php echo $module_to_edit ? 'Update Module' : 'Add Module'; ?></button>
                </div>
            </form>
        </div>
        
        <script>
        // Format file size
        function formatFileSize(bytes) {
            if(bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return Math.round((bytes / Math.pow(k, i)) * 100) / 100 + ' ' + sizes[i];
        }
        
        // Get file icon
        function getFileIcon(ext) {
            const iconMap = {
                'pdf': 'fa-file-pdf text-red-600',
                'doc': 'fa-file-word text-blue-600',
                'docx': 'fa-file-word text-blue-600',
                'xls': 'fa-file-excel text-green-600',
                'xlsx': 'fa-file-excel text-green-600',
                'ppt': 'fa-file-powerpoint text-orange-600',
                'pptx': 'fa-file-powerpoint text-orange-600',
                'zip': 'fa-file-archive text-yellow-600',
                'rar': 'fa-file-archive text-yellow-600',
                'mp4': 'fa-file-video text-purple-600',
                'mp3': 'fa-file-audio text-purple-600',
                'jpg': 'fa-file-image text-pink-600',
                'jpeg': 'fa-file-image text-pink-600',
                'png': 'fa-file-image text-pink-600',
                'gif': 'fa-file-image text-pink-600'
            };
            return iconMap[ext.toLowerCase()] || 'fa-file';
        }
        
        // Handle multiple file input display
        document.getElementById('multiFileInput').addEventListener('change', function(e) {
            const fileNames = document.getElementById('fileNames');
            const filesList = document.getElementById('filesList');
            const uploadedFilesList = document.getElementById('uploadedFilesList');
            
            if(this.files.length > 0) {
                let filesHTML = '';
                for(let i = 0; i < this.files.length; i++) {
                    const file = this.files[i];
                    const ext = file.name.split('.').pop();
                    const icon = getFileIcon(ext);
                    filesHTML += `
                    <div class='flex items-center justify-between bg-white p-2 rounded border border-green-200'>
                        <div class='flex items-center flex-1'>
                            <i class='fas ${icon} text-lg mr-2'></i>
                            <span class='text-sm font-semibold text-gray-900'>\${file.name}</span>
                        </div>
                        <span class='text-xs text-gray-500 ml-2'>\${formatFileSize(file.size)}</span>
                    </div>
                    `;
                }
                filesList.innerHTML = filesHTML;
                uploadedFilesList.classList.remove('hidden');
                fileNames.innerHTML = `<i class="fas fa-check-circle text-green-600 text-2xl mb-2"></i><p class="mt-2 text-green-600 font-semibold">\${this.files.length} file(s) selected</p>`;
            }
        });
        
        // Drag and drop for multiple files
        const multiFileInput = document.getElementById('multiFileInput');
        const dropZone = multiFileInput.parentElement;
        
        dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropZone.classList.add('border-purple-600', 'bg-purple-50');
        });
        
        dropZone.addEventListener('dragleave', () => {
            dropZone.classList.remove('border-purple-600', 'bg-purple-50');
        });
        
        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.classList.remove('border-purple-600', 'bg-purple-50');
            multiFileInput.files = e.dataTransfer.files;
            const event = new Event('change', { bubbles: true });
            multiFileInput.dispatchEvent(event);
        });
        
        function resetModuleForm() {
            document.getElementById('multiFileInput').value = '';
            document.getElementById('fileNames').innerHTML = '<i class="fas fa-cloud-upload-alt text-3xl mb-2 text-gray-400"></i><p class="mt-2">Click to upload or drag and drop</p><p class="text-xs text-gray-500 mt-1">You can select multiple files</p>';
            document.getElementById('uploadedFilesList').classList.add('hidden');
        }
        </script>
        
        <!-- Modules List -->
        <div class="space-y-4">
            <?php
            if($modules->num_rows > 0) {
                while($module = $modules->fetch_assoc()) {
                    echo "
                    <div class='bg-gray-50 p-6 rounded-lg border border-gray-200 hover:border-purple-400 transition'>
                        <div class='flex justify-between items-start mb-3'>
                            <div class='flex-1'>
                                <h3 class='text-lg font-bold text-gray-900'>" . htmlspecialchars($module['title']) . "</h3>
                                <p class='text-gray-600 text-sm mt-1'>" . htmlspecialchars($module['description'] ?? 'No description') . "</p>";
                    
                    // Show file info if exists
                    if($module['module_file']) {
                        echo "<p class='text-xs text-green-600 mt-2'><i class='fas fa-file-download mr-1'></i>" . basename($module['module_file']) . " <a href='../../public/uploads/" . htmlspecialchars($module['module_file']) . "' target='_blank' class='text-blue-600 hover:underline'>(Download)</a></p>";
                    }
                    
                    echo "</div>
                            <div class='flex space-x-2'>
                                <button onclick=\"document.getElementById('addModuleForm').classList.remove('hidden'); window.location.hash='edit-module'; document.querySelector('input[name=\\\"module_title\\\"]').focus();\" class='px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm'>
                                    <i class='fas fa-edit mr-1'></i>Edit
                                </button>
                                <a href='?section=internships&action=content&internship_id=" . intval($_GET['internship_id']) . "&edit_module=" . $module['id'] . "' class='inline-flex items-center px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm'>
                                    <i class='fas fa-pencil-alt mr-1'></i>Edit Details
                                </a>
                                <button onclick=\"if(confirm('Delete this module?')) window.location='?section=internships&action=content&internship_id=" . intval($_GET['internship_id']) . "&delete_module=" . $module['id'] . "'\" class='px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700 text-sm'>
                                    <i class='fas fa-trash mr-1'></i>Delete
                                </button>
                            </div>
                        </div>
                        <p class='text-xs text-gray-500'>Module #" . $module['order_seq'] . "</p>
                    </div>
                    ";
                }
            } else {
                echo "<p class='text-center text-gray-600 py-8'>No modules yet. Add your first module above!</p>";
            }
            ?>
        </div>
    </div>
    
    <?php
    exit;
}

// Handle subsection logic
if(isset($_GET['view'])) {
    $internship_id = intval($_GET['view']);
    $internship = $conn->query("SELECT * FROM internships WHERE id=$internship_id")->fetch_assoc();
    
    if(!$internship) {
        header("Location: ?section=internships");
        exit;
    }
    
    // Get applications for this internship
    $applications = $conn->query("
        SELECT a.id, u.id as user_id, u.name, u.email, u.phone, a.status, a.application_date, p.status as payment_status
        FROM applications a
        JOIN users u ON a.user_id = u.id
        LEFT JOIN payments p ON a.id = p.application_id
        WHERE a.internship_id = $internship_id
        GROUP BY a.id
        ORDER BY a.application_date DESC
    ");
} elseif(isset($_GET['user_view'])) {
    $user_id = intval($_GET['user_view']);
    $internship_id = intval($_GET['view']);
    
    $query = "SELECT a.id, u.*, a.status as app_status, a.application_date, i.title as internship_title
              FROM applications a
              JOIN users u ON a.user_id = u.id
              JOIN internships i ON a.internship_id = i.id
              WHERE u.id = $user_id AND a.internship_id = $internship_id";
    
    $user_app = $conn->query($query)->fetch_assoc();
    
    if(!$user_app) {
        header("Location: ?section=internships&view=" . $internship_id);
        exit;
    }
}

if(isset($_GET['view'])) {
    // Internship Detail View
    $total_applicants = $conn->query("SELECT COUNT(*) as count FROM applications WHERE internship_id=$internship_id")->fetch_assoc()['count'];
    $approved_applicants = $conn->query("SELECT COUNT(*) as count FROM applications WHERE internship_id=$internship_id AND status='approved'")->fetch_assoc()['count'];
    $pending_applicants = $conn->query("SELECT COUNT(*) as count FROM applications WHERE internship_id=$internship_id AND status='pending'")->fetch_assoc()['count'];
    ?>
    
    <!-- Back Button -->
    <a href="?section=internships" class="inline-flex items-center mb-6 px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
        <i class="fas fa-arrow-left mr-2"></i>Back to Internships
    </a>
    
    <!-- Internship Header -->
    <div class="bg-white rounded-lg shadow-md p-8 mb-6">
        <h2 class="text-3xl font-bold text-gray-900 mb-2"><?php echo htmlspecialchars($internship['title']); ?></h2>
        <p class="text-gray-600 mb-4"><?php echo htmlspecialchars($internship['description']); ?></p>
        
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">
            <div class="bg-blue-50 p-4 rounded-lg">
                <p class="text-gray-600 text-sm">Duration</p>
                <p class="text-2xl font-bold text-blue-600"><?php echo htmlspecialchars($internship['duration']); ?></p>
            </div>
            <div class="bg-green-50 p-4 rounded-lg">
                <p class="text-gray-600 text-sm">Fees</p>
                <p class="text-2xl font-bold text-green-600">Rs. <?php echo number_format($internship['fees']); ?></p>
            </div>
            <div class="bg-purple-50 p-4 rounded-lg">
                <p class="text-gray-600 text-sm">Total Applicants</p>
                <p class="text-2xl font-bold text-purple-600"><?php echo $total_applicants; ?></p>
            </div>
            <div class="bg-orange-50 p-4 rounded-lg">
                <p class="text-gray-600 text-sm">Pending</p>
                <p class="text-2xl font-bold text-orange-600"><?php echo $pending_applicants; ?></p>
            </div>
        </div>
    </div>
    
    <!-- Applicants Table -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-xl font-bold text-gray-900 mb-4">
            <i class="fas fa-users mr-2"></i>Applicants (<?php echo $total_applicants; ?>)
        </h3>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="px-4 py-3 font-semibold text-gray-700">Name</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">Email</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">Phone</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">Status</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">Applied Date</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if($applications->num_rows > 0) {
                        while($app = $applications->fetch_assoc()) {
                            $statusColor = $app['status'] === 'approved' ? 'green' : ($app['status'] === 'pending' ? 'orange' : 'red');
                            echo "
                            <tr class='border-b border-gray-100 hover:bg-gray-50'>
                                <td class='px-4 py-3 font-semibold text-gray-900'>" . htmlspecialchars($app['name']) . "</td>
                                <td class='px-4 py-3 text-gray-600'>" . htmlspecialchars($app['email']) . "</td>
                                <td class='px-4 py-3 text-gray-600'>" . htmlspecialchars($app['phone']) . "</td>
                                <td class='px-4 py-3'>
                                    <span class='px-3 py-1 bg-{$statusColor}-100 text-{$statusColor}-700 rounded-full text-xs font-semibold'>
                                        " . ucfirst($app['status']) . "
                                    </span>
                                </td>
                                <td class='px-4 py-3 text-sm text-gray-600'>" . date('M d, Y', strtotime($app['application_date'])) . "</td>
                                <td class='px-4 py-3'>
                                    <a href='?section=internships&view=" . $internship_id . "&user_view=" . $app['user_id'] . "' class='inline-flex items-center px-3 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition text-sm'>
                                        <i class='fas fa-eye mr-1'></i>View Details
                                    </a>
                                </td>
                            </tr>
                            ";
                        }
                    } else {
                        echo "<tr><td colspan='6' class='px-4 py-3 text-center text-gray-600'>No applicants yet</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <?php
} elseif(isset($_GET['user_view'])) {
    // User Application Detail View
    ?>
    
    <!-- Back Buttons -->
    <div class="mb-6 space-y-2">
        <a href="?section=internships&view=<?php echo $_GET['view']; ?>" class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
            <i class="fas fa-arrow-left mr-2"></i>Back to Applicants
        </a>
    </div>
    
    <!-- User Application Details -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- User Information -->
        <div class="lg:col-span-2 bg-white rounded-lg shadow-md p-6">
            <h3 class="text-xl font-bold text-gray-900 mb-6 pb-4 border-b">
                <i class="fas fa-user mr-2 text-blue-600"></i>User Information
            </h3>
            
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-600 text-sm font-semibold mb-1">Name</label>
                        <p class="text-gray-900 text-lg"><?php echo htmlspecialchars($user_app['name']); ?></p>
                    </div>
                    <div>
                        <label class="block text-gray-600 text-sm font-semibold mb-1">Email</label>
                        <p class="text-gray-900 text-lg"><?php echo htmlspecialchars($user_app['email']); ?></p>
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-600 text-sm font-semibold mb-1">Phone</label>
                        <p class="text-gray-900 text-lg"><?php echo htmlspecialchars($user_app['phone']); ?></p>
                    </div>
                    <div>
                        <label class="block text-gray-600 text-sm font-semibold mb-1">User Type</label>
                        <p class="text-gray-900 text-lg">
                            <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm font-semibold">
                                <?php echo ucfirst($user_app['user_type']); ?>
                            </span>
                        </p>
                    </div>
                </div>
                
                <div>
                    <label class="block text-gray-600 text-sm font-semibold mb-1">Applied For</label>
                    <p class="text-gray-900 text-lg"><?php echo htmlspecialchars($user_app['internship_title']); ?></p>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-600 text-sm font-semibold mb-1">Application Status</label>
                        <p class="text-gray-900 text-lg">
                            <?php
                            $statusColor = $user_app['app_status'] === 'approved' ? 'green' : ($user_app['app_status'] === 'pending' ? 'orange' : 'red');
                            echo "<span class='px-3 py-1 bg-{$statusColor}-100 text-{$statusColor}-700 rounded-full text-sm font-semibold'>" . ucfirst($user_app['app_status']) . "</span>";
                            ?>
                        </p>
                    </div>
                    <div>
                        <label class="block text-gray-600 text-sm font-semibold mb-1">Application Date</label>
                        <p class="text-gray-900 text-lg"><?php echo date('M d, Y', strtotime($user_app['application_date'])); ?></p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-xl font-bold text-gray-900 mb-6 pb-4 border-b">
                <i class="fas fa-cogs mr-2 text-purple-600"></i>Actions
            </h3>
            
            <div class="space-y-3">
                <a href="?section=certificates&generate=<?php echo $user_app['user_id']; ?>&internship=<?php echo $_GET['view']; ?>" class="block w-full px-4 py-3 bg-green-600 text-white rounded hover:bg-green-700 transition text-center font-semibold">
                    <i class="fas fa-certificate mr-2"></i>Generate Certificate
                </a>
                <a href="?section=offer-letters&generate=<?php echo $user_app['user_id']; ?>&internship=<?php echo $_GET['view']; ?>" class="block w-full px-4 py-3 bg-blue-600 text-white rounded hover:bg-blue-700 transition text-center font-semibold">
                    <i class="fas fa-file-contract mr-2"></i>Generate Offer Letter
                </a>
                <button onclick="if(confirm('Mark as approved?')) window.location='?section=internships&approve=<?php echo $user_app['id']; ?>&back=view&view=<?php echo $_GET['view']; ?>&user=<?php echo $user_app['user_id']; ?>'" class="block w-full px-4 py-3 bg-purple-600 text-white rounded hover:bg-purple-700 transition font-semibold">
                    <i class="fas fa-check mr-2"></i>Approve Application
                </button>
            </div>
        </div>
    </div>
    
    <?php
} else {
    // Internships List View
    $internships_result = $conn->query("SELECT i.*, COUNT(a.id) as total_applicants FROM internships i LEFT JOIN applications a ON i.id = a.internship_id GROUP BY i.id");
    ?>
    
    <!-- Add Internship Button -->
    <div class="mb-6">
        <a href="?section=internships&action=add" class="inline-flex items-center px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-semibold">
            <i class="fas fa-plus-circle mr-2"></i>Add New Internship
        </a>
    </div>
    
    <!-- Internships Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php
        if($internships_result->num_rows > 0) {
            while($internship = $internships_result->fetch_assoc()) {
                echo "
                <div class='bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition'>
                    <div class='bg-gradient-to-r from-purple-600 to-blue-600 p-6 text-white'>
                        <h3 class='text-xl font-bold mb-2'>" . htmlspecialchars($internship['title']) . "</h3>
                        <p class='text-purple-100 text-sm line-clamp-2'>" . htmlspecialchars(substr($internship['description'], 0, 100)) . "...</p>
                    </div>
                    
                    <div class='p-6'>
                        <div class='space-y-3 mb-6'>
                            <div class='flex justify-between items-center'>
                                <span class='text-gray-600'>Duration:</span>
                                <span class='font-semibold text-gray-900'>" . htmlspecialchars($internship['duration']) . "</span>
                            </div>
                            <div class='flex justify-between items-center'>
                                <span class='text-gray-600'>Fees:</span>
                                <span class='font-semibold text-gray-900'>Rs. " . number_format($internship['fees']) . "</span>
                            </div>
                            <div class='flex justify-between items-center'>
                                <span class='text-gray-600'>Total Applicants:</span>
                                <span class='px-3 py-1 bg-blue-100 text-blue-700 rounded-full font-semibold'>" . $internship['total_applicants'] . "</span>
                            </div>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class='space-y-2'>
                            <a href='?section=internships&view=" . $internship['id'] . "' class='block w-full px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition text-center font-semibold text-sm'>
                                <i class='fas fa-users mr-1'></i>View Applicants
                            </a>
                            <a href='?section=internships&action=add&edit=" . $internship['id'] . "' class='block w-full px-4 py-2 bg-orange-600 text-white rounded hover:bg-orange-700 transition text-center font-semibold text-sm'>
                                <i class='fas fa-edit mr-1'></i>Edit
                            </a>
                            <a href='?section=internships&action=content&internship_id=" . $internship['id'] . "' class='block w-full px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 transition text-center font-semibold text-sm'>
                                <i class='fas fa-book mr-1'></i>Add Content
                            </a>
                            <button onclick=\"if(confirm('Are you sure you want to delete this internship? This will also delete all modules and related data.')) window.location='?section=internships&delete_internship=" . $internship['id'] . "'\" class='block w-full px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition text-center font-semibold text-sm'>
                                <i class='fas fa-trash mr-1'></i>Delete
                            </button>
                        </div>
                    </div>
                </div>
                ";
            }
        } else {
            echo "<div class='col-span-full text-center py-12'><p class='text-gray-600'>No internships found. <a href=\"?section=internships&action=add\" class=\"text-purple-600 hover:underline font-semibold\">Create one now</a></p></div>";
        }
        ?>
    </div>
    
    <?php
}
?>
