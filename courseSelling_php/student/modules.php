<?php
$pageTitle = 'Course Modules - Digital Tarai';
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../lib/functions.php';

// Helper function to format file size
function formatFileSize($bytes) {
    if($bytes === 0) return '0 Bytes';
    $k = 1024;
    $sizes = ['Bytes', 'KB', 'MB', 'GB'];
    $i = floor(log($bytes) / log($k));
    return round(($bytes / pow($k, $i)) * 100) / 100 . ' ' . $sizes[$i];
}

// Check if user is logged in
if (!isLoggedIn()) {
    redirect('auth.php?action=login');
}

$internship_id = isset($_GET['internship_id']) ? intval($_GET['internship_id']) : 0;

if (!$internship_id) {
    redirect('dashboard.php');
}

// Get internship details
$internship_result = $conn->query("SELECT * FROM internships WHERE id=$internship_id");
$internship = $internship_result && $internship_result->num_rows > 0 ? $internship_result->fetch_assoc() : null;

if (!$internship) {
    die('Internship not found');
}

// Get modules for this internship
$modules_result = $conn->query("SELECT * FROM modules WHERE internship_id=$internship_id ORDER BY order_seq ASC");

// Check if student has a generated certificate for this internship
$cert_query = $conn->query("SELECT c.id, c.filename, c.file_path, c.generated_at FROM certificates c 
                           JOIN applications a ON c.application_id = a.id 
                           WHERE a.user_id={$_SESSION['user_id']} AND c.internship_id=$internship_id AND c.filename != 'pending' LIMIT 1");
$certificate = $cert_query && $cert_query->num_rows > 0 ? $cert_query->fetch_assoc() : null;

// Handle certificate request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'request_certificate') {
    // Check if user has completed all modules
    $total_modules = $modules_result && $modules_result->num_rows > 0 ? $modules_result->num_rows : 0;
    $completed_modules = isset($_POST['completed_count']) ? intval($_POST['completed_count']) : 0;
    
    if ($total_modules > 0 && $completed_modules === $total_modules) {
        // Get user's approved application for this internship
        $app_query = $conn->query("SELECT id FROM applications WHERE user_id={$_SESSION['user_id']} AND internship_id=$internship_id AND status='approved' LIMIT 1");
        
        if ($app_query && $app_query->num_rows > 0) {
            $app = $app_query->fetch_assoc();
            $app_id = $app['id'];
            
            // Check if certificate request already exists
            $existing = $conn->query("SELECT id FROM certificates WHERE application_id=$app_id");
            
            if ($existing && $existing->num_rows == 0) {
                // Insert certificate request
                $user_name = $_SESSION['user_name'];
                $user_email = $_SESSION['user_email'];
                $internship_title = $internship['title'];
                $requested_at = date('Y-m-d H:i:s');
                
                $conn->query("INSERT INTO certificates (application_id, user_id, internship_id, requested_at, filename, file_path) 
                             VALUES ($app_id, {$_SESSION['user_id']}, $internship_id, '$requested_at', 'pending', 'pending')");
                
                $_SESSION['certificate_message'] = 'Certificate request sent successfully! Our team will review and send you the certificate soon.';
            } else {
                $_SESSION['certificate_message'] = 'Certificate request already submitted. Please wait for our team to review.';
            }
        }
        
        // Reload modules result
        $modules_result = $conn->query("SELECT * FROM modules WHERE internship_id=$internship_id ORDER BY order_seq ASC");
    }
}

// Reload modules result for display
$modules_result = $conn->query("SELECT * FROM modules WHERE internship_id=$internship_id ORDER BY order_seq ASC");
?>
<?php include '../app/views/header.php'; ?>

<div class="min-h-screen bg-gray-50">
    <!-- Top Navigation -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-center space-x-4">
                <a href="<?php echo SITE_URL; ?>/student/dashboard.php" class="text-gray-600 hover:text-gray-900">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h1 class="text-2xl font-bold text-gray-900">Course Modules</h1>
            </div>
        </div>
    </div>

    <section class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Success Message -->
        <?php if (isset($_SESSION['certificate_message'])): ?>
            <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                <i class="fas fa-check-circle mr-2"></i><?php echo $_SESSION['certificate_message']; ?>
            </div>
            <?php unset($_SESSION['certificate_message']); ?>
        <?php endif; ?>

        <!-- Program Info Accordion -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
            <button 
                onclick="toggleProgramAccordion(this)" 
                class="w-full px-8 py-6 bg-gradient-to-r from-purple-600 to-blue-600 text-white flex items-center justify-between hover:from-purple-700 hover:to-blue-700 transition"
            >
                <h2 class="text-2xl font-bold"><?php echo htmlspecialchars($internship['title']); ?></h2>
                <i class="fas fa-chevron-down transition-transform duration-300"></i>
            </button>

            <div class="accordion-content-program max-h-0 overflow-hidden transition-all duration-300">
                <div class="px-8 py-6 bg-white border-t border-gray-200">
                    <p class="text-gray-600 mb-6"><?php echo htmlspecialchars($internship['description']); ?></p>
                    
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="bg-purple-50 p-4 rounded-lg">
                            <p class="text-sm text-gray-600">Duration</p>
                            <p class="text-lg font-semibold text-purple-600"><?php echo htmlspecialchars($internship['duration']); ?></p>
                        </div>
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <p class="text-sm text-gray-600">Fee</p>
                            <p class="text-lg font-semibold text-blue-600"><?php echo formatCurrency($internship['fees']); ?></p>
                        </div>
                        <div class="bg-yellow-50 p-4 rounded-lg">
                            <p class="text-sm text-gray-600">Modules</p>
                            <p class="text-lg font-semibold text-yellow-600" id="total-modules"><?php echo $modules_result->num_rows; ?></p>
                        </div>
                        <div class="bg-green-50 p-4 rounded-lg">
                            <p class="text-sm text-gray-600">Completed</p>
                            <p class="text-lg font-semibold text-green-600"><span id="completed-count">0</span>/<span id="total-count"><?php echo $modules_result->num_rows; ?></span></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Progress Bar - Outside Accordion -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <p class="text-sm font-medium text-gray-700 mb-2">Your Progress</p>
            <div class="w-full bg-gray-200 rounded-full h-4">
                <div id="progress-bar" class="bg-green-600 h-4 rounded-full transition-all duration-300" style="width: 0%"></div>
            </div>
        </div>

        <!-- Modules Accordion -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="bg-gradient-to-r from-purple-600 to-blue-600 px-8 py-6">
                <h3 class="text-2xl font-bold text-white">
                    <i class="fas fa-book mr-2"></i>Learning Modules
                </h3>
            </div>

            <div class="divide-y divide-gray-200" id="modules-container">
                <?php
                if($modules_result && $modules_result->num_rows > 0) {
                    $module_num = 1;
                    while($module = $modules_result->fetch_assoc()) {
                        $module_id = 'module_' . $module['id'];
                        ?>
                        <div class="accordion-item border-b border-gray-200">
                            <button 
                                onclick="toggleAccordion(this)" 
                                class="accordion-button w-full px-8 py-6 bg-white hover:bg-gray-50 transition text-left flex items-center justify-between"
                                data-module-id="<?php echo $module['id']; ?>"
                            >
                                <div class="flex items-center flex-1">
                                    <input 
                                        type="checkbox" 
                                        class="module-checkbox mr-4 w-5 h-5 text-green-600 rounded focus:ring-2 focus:ring-green-500"
                                        data-module-id="<?php echo $module['id']; ?>"
                                        onchange="updateProgress()"
                                    >
                                    <div class="flex items-center space-x-4">
                                        <span class="inline-flex items-center justify-center h-10 w-10 rounded-full bg-purple-600 text-white text-sm font-semibold">
                                            <?php echo $module_num; ?>
                                        </span>
                                        <div>
                                            <h4 class="text-lg font-semibold text-gray-900"><?php echo htmlspecialchars($module['title']); ?></h4>
                                        </div>
                                    </div>
                                </div>
                                <i class="fas fa-chevron-down transition-transform duration-300"></i>
                            </button>

                            <div class="accordion-content max-h-0 overflow-hidden bg-gray-50 transition-all duration-300">
                                <div class="px-8 py-6">
                                    <p class="text-gray-600 mb-4"><?php echo htmlspecialchars($module['description']); ?></p>
                                    
                                    <?php 
                                    // Get files for this module from module_files table
                                    $files_result = $conn->query("SELECT * FROM module_files WHERE module_id=" . intval($module['id']) . " ORDER BY upload_order");
                                    $has_files = $files_result && $files_result->num_rows > 0;
                                    
                                    // Also check for legacy single file
                                    $has_legacy = !empty($module['module_file']);
                                    
                                    if($has_files || $has_legacy): 
                                    ?>
                                        <div class="mt-4">
                                            <h4 class="font-semibold text-gray-900 mb-3">
                                                <i class="fas fa-download mr-2 text-blue-600"></i>Module Materials
                                            </h4>
                                            <div class="space-y-2">
                                                <?php 
                                                // Display multiple files
                                                if($has_files) {
                                                    while($file = $files_result->fetch_assoc()) {
                                                        // Determine file icon based on type
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
                                                        <a href='" . SITE_URL . "/public/uploads/" . htmlspecialchars($file['file_path']) . "' 
                                                           class='flex items-center justify-between p-3 bg-white border border-gray-300 rounded-lg hover:border-blue-500 hover:shadow-md transition'>
                                                            <div class='flex items-center flex-1'>
                                                                <i class='fas $file_icon text-xl mr-3'></i>
                                                                <div>
                                                                    <p class='font-semibold text-gray-900'>" . htmlspecialchars($file['file_name']) . "</p>
                                                                    <p class='text-xs text-gray-500'>" . formatFileSize($file['file_size']) . "</p>
                                                                </div>
                                                            </div>
                                                            <i class='fas fa-download text-blue-600 text-lg'></i>
                                                        </a>
                                                        ";
                                                    }
                                                }
                                                
                                                // Display legacy single file if exists
                                                if($has_legacy) {
                                                    echo "
                                                    <a href='" . SITE_URL . htmlspecialchars($module['module_file']) . "' 
                                                       class='flex items-center justify-between p-3 bg-white border border-gray-300 rounded-lg hover:border-blue-500 hover:shadow-md transition'>
                                                        <div class='flex items-center flex-1'>
                                                            <i class='fas fa-file text-xl mr-3 text-gray-600'></i>
                                                            <div>
                                                                <p class='font-semibold text-gray-900'>" . htmlspecialchars(basename($module['module_file'])) . "</p>
                                                                <p class='text-xs text-gray-500'>Course Material</p>
                                                            </div>
                                                        </div>
                                                        <i class='fas fa-download text-blue-600 text-lg'></i>
                                                    </a>
                                                    ";
                                                }
                                                ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php
                        $module_num++;
                    }
                } else {
                    ?>
                    <div class="p-8 text-center text-gray-600">
                        <i class="fas fa-inbox text-4xl mb-4 text-gray-400"></i>
                        <p>No modules available for this program yet.</p>
                    </div>
                    <?php
                }
                ?>
            </div>
        </div>

        <!-- Skills Section -->
        <div class="bg-white rounded-lg shadow-md p-8 mt-8">
            <h3 class="text-2xl font-bold text-gray-900 mb-6">
                <i class="fas fa-star mr-2 text-yellow-500"></i>Skills You'll Learn
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <?php
                if(!empty($internship['skills'])) {
                    $skills = array_map('trim', explode(',', $internship['skills']));
                    foreach($skills as $skill) {
                        echo "
                        <div class='flex items-center p-4 bg-gray-50 rounded-lg'>
                            <i class='fas fa-check text-green-500 mr-3'></i>
                            <span class='text-gray-700'>" . htmlspecialchars($skill) . "</span>
                        </div>
                        ";
                    }
                }
                ?>
            </div>
        </div>

        <!-- Certificate Request/Download Section -->
        <div class="mt-8" id="certificate-section">
            <?php if ($certificate): ?>
                <!-- Certificate Generated - Show Download -->
                <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-lg p-8 border-2 border-green-200">
                    <div class="flex items-start space-x-4">
                        <i class="fas fa-certificate text-5xl text-green-600 flex-shrink-0"></i>
                        <div class="flex-1">
                            <h3 class="text-2xl font-bold text-gray-900 mb-2">🎉 Certificate Generated!</h3>
                            <p class="text-gray-700 mb-4">Congratulations! Your certificate has been generated by our team.</p>
                            
                            <div class="mb-4">
                                <p class="text-sm font-medium text-gray-700 mb-2">Generated Date:</p>
                                <p class="text-lg font-semibold text-green-600">
                                    <?php echo date('M d, Y', strtotime($certificate['generated_at'])); ?>
                                </p>
                            </div>

                            <div class="flex gap-3">
                                <a href="<?php echo SITE_URL; ?>/<?php echo htmlspecialchars($certificate['file_path']); ?>" 
                                   target="_blank"
                                   class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-semibold">
                                    <i class="fas fa-download mr-2"></i>Download Certificate
                                </a>
                                <a href="<?php echo SITE_URL; ?>/<?php echo htmlspecialchars($certificate['file_path']); ?>" 
                                   target="_blank"
                                   class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-semibold">
                                    <i class="fas fa-eye mr-2"></i>View Certificate
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <!-- Certificate Not Generated - Show Request Form -->
                <form method="POST" class="bg-gradient-to-r from-yellow-50 to-orange-50 rounded-lg p-8 border-2 border-yellow-200">
                    <div class="flex items-start space-x-4">
                        <i class="fas fa-certificate text-5xl text-yellow-600 flex-shrink-0"></i>
                        <div class="flex-1">
                            <h3 class="text-2xl font-bold text-gray-900 mb-2">Ready for Your Certificate?</h3>
                            <p class="text-gray-700 mb-4">Complete all course modules to request your digital certificate. Once approved by our team, you'll receive your certificate via email.</p>
                            
                            <div class="mb-4">
                                <p class="text-sm font-medium text-gray-700 mb-2">Completion Status:</p>
                                <p class="text-lg font-semibold">
                                    <span id="cert-completed">0</span>/<span id="cert-total"><?php echo $modules_result->num_rows; ?></span> modules completed
                                </p>
                            </div>

                            <input type="hidden" name="action" value="request_certificate">
                            <input type="hidden" name="completed_count" id="completed_count_input" value="0">
                            
                            <button 
                                type="submit" 
                                id="certificate-btn"
                                disabled
                                class="px-6 py-3 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition font-semibold disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                <i class="fas fa-check-circle mr-2"></i>Request Certificate
                            </button>
                            <p class="text-sm text-gray-600 mt-2">Complete all modules above to enable this button</p>
                        </div>
                    </div>
                </form>
            <?php endif; ?>
        </div>

        <!-- Back Button -->
        <div class="mt-8 text-center">
            <a href="<?php echo SITE_URL; ?>/student/dashboard.php" class="inline-flex items-center px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition font-semibold">
                <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
            </a>
        </div>
    </section>
</div>

<?php include '../app/views/footer.php'; ?>

<script>
    // Program Info Accordion functionality
    function toggleProgramAccordion(button) {
        const content = button.nextElementSibling;
        const icon = button.querySelector('i.fa-chevron-down');
        
        if (content.style.maxHeight && content.style.maxHeight !== '0px') {
            content.style.maxHeight = '0px';
            icon.style.transform = 'rotate(0deg)';
        } else {
            content.style.maxHeight = content.scrollHeight + 'px';
            icon.style.transform = 'rotate(180deg)';
        }
    }

    // Accordion functionality
    function toggleAccordion(button) {
        const content = button.nextElementSibling;
        const icon = button.querySelector('i.fa-chevron-down');
        
        // Close all other accordions
        document.querySelectorAll('.accordion-content').forEach(item => {
            if (item !== content) {
                item.style.maxHeight = '0px';
                item.previousElementSibling.querySelector('i.fa-chevron-down').style.transform = 'rotate(0deg)';
            }
        });
        
        // Toggle current accordion
        if (content.style.maxHeight && content.style.maxHeight !== '0px') {
            content.style.maxHeight = '0px';
            icon.style.transform = 'rotate(0deg)';
        } else {
            content.style.maxHeight = content.scrollHeight + 'px';
            icon.style.transform = 'rotate(180deg)';
        }
    }

    // Update progress based on checkboxes
    function updateProgress() {
        const checkboxes = document.querySelectorAll('.module-checkbox');
        const checkedCount = Array.from(checkboxes).filter(cb => cb.checked).length;
        const totalCount = checkboxes.length;
        
        // Update progress display
        document.getElementById('completed-count').textContent = checkedCount;
        const progressPercent = totalCount > 0 ? (checkedCount / totalCount) * 100 : 0;
        document.getElementById('progress-bar').style.width = progressPercent + '%';
        
        // Update certificate section
        document.getElementById('cert-completed').textContent = checkedCount;
        document.getElementById('completed_count_input').value = checkedCount;
        
        // Enable/disable certificate button
        const certificateBtn = document.getElementById('certificate-btn');
        if (checkedCount === totalCount && totalCount > 0) {
            certificateBtn.disabled = false;
            certificateBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        } else {
            certificateBtn.disabled = true;
            certificateBtn.classList.add('opacity-50', 'cursor-not-allowed');
        }
        
        // Save progress to localStorage
        const progressData = {};
        checkboxes.forEach(cb => {
            progressData['module_' + cb.dataset.moduleId] = cb.checked;
        });
        localStorage.setItem('course_progress_' + new URLSearchParams(window.location.search).get('internship_id'), JSON.stringify(progressData));
    }

    // Load progress from localStorage on page load
    document.addEventListener('DOMContentLoaded', function() {
        const internshipId = new URLSearchParams(window.location.search).get('internship_id');
        const savedProgress = localStorage.getItem('course_progress_' + internshipId);
        
        if (savedProgress) {
            const progressData = JSON.parse(savedProgress);
            Object.keys(progressData).forEach(moduleKey => {
                const moduleId = moduleKey.replace('module_', '');
                const checkbox = document.querySelector(`input[data-module-id="${moduleId}"]`);
                if (checkbox) {
                    checkbox.checked = progressData[moduleKey];
                }
            });
        }
        
        updateProgress();
    });
</script>