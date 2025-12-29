<?php
$pageTitle = 'Internship Detail - Digital Tarai';
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../lib/functions.php';
require_once '../app/models/Internship.php';
require_once '../app/models/Application.php';

$slug = isset($_GET['slug']) ? $_GET['slug'] : '';

if (!$slug) {
    redirect('pages/careers.php');
}

$internshipModel = new Internship($conn);
$internship = $internshipModel->getBySlug($slug);

if (!$internship) {
    echo "Internship not found";
    exit;
}

$modules_result = $internshipModel->getModules($internship['id']);
$modules = [];
while ($mod = $modules_result->fetch_assoc()) {
    $modules[] = $mod;
}
$pageTitle = $internship['title'] . ' - Digital Tarai';

// Check application status for logged in user
$application_status = null;
$application_id = null;
if (isLoggedIn()) {
    $user_id = $_SESSION['user_id'];
    // Get the LATEST application (most recent one)
    $query = "SELECT id, status FROM applications WHERE user_id=$user_id AND internship_id={$internship['id']} ORDER BY created_at DESC LIMIT 1";
    $app_result = $conn->query($query);
    if ($app_result && $app_result->num_rows > 0) {
        $app = $app_result->fetch_assoc();
        $application_status = trim($app['status']); // Trim any whitespace
        $application_id = $app['id'];
    }
}
?>
<?php include '../app/views/header.php'; ?>

<div class="bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <h1 class="text-4xl font-bold text-gray-900 mb-4"><?php echo htmlspecialchars($internship['title']); ?></h1>
        <p class="text-lg text-gray-600"><?php echo truncateText($internship['description'], 150); ?></p>
    </div>
</div>

<section class="py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2">
                <!-- Overview -->
                <div class="bg-white rounded-lg shadow-md p-8 mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Program Overview</h2>
                    <p class="text-gray-600 mb-4">
                        <?php echo htmlspecialchars($internship['description']); ?>
                    </p>
                </div>
                
                <!-- Skills Required -->
                <div class="bg-white rounded-lg shadow-md p-8 mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">
                        <i class="fas fa-star text-purple-600 mr-2"></i>Required Skills
                    </h2>
                    <p class="text-gray-600">
                        <?php echo htmlspecialchars($internship['skills']); ?>
                    </p>
                </div>
                
                <!-- What You'll Learn - Only for Approved Applications -->
                <?php if (isLoggedIn() && $application_status === 'approved'): ?>
                <div class="bg-white rounded-lg shadow-md p-8 mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">
                        <i class="fas fa-graduation-cap text-purple-600 mr-2"></i>What You'll Learn
                    </h2>
                    <p class="text-gray-600 mb-6">
                        During this internship, you will master:
                    </p>
                    <ul class="space-y-3">
                        <?php foreach ($modules as $module): ?>
                            <li class="flex items-start">
                                <i class="fas fa-check text-purple-600 mr-3 mt-1"></i>
                                <div>
                                    <p class="font-semibold text-gray-900"><?php echo htmlspecialchars($module['title']); ?></p>
                                    <p class="text-gray-600 text-sm"><?php echo htmlspecialchars($module['description'] ?? ''); ?></p>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>
                
                <!-- Benefits -->
                <div class="bg-white rounded-lg shadow-md p-8 mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">
                        <i class="fas fa-gift text-purple-600 mr-2"></i>Program Benefits
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex items-start p-4 bg-purple-50 rounded-lg">
                            <i class="fas fa-file-contract text-purple-600 mr-3 mt-1"></i>
                            <div>
                                <p class="font-semibold text-gray-900">Official Offer Letter</p>
                                <p class="text-gray-600 text-sm">Professional offer letter for your career</p>
                            </div>
                        </div>
                        <div class="flex items-start p-4 bg-purple-50 rounded-lg">
                            <i class="fas fa-book text-purple-600 mr-3 mt-1"></i>
                            <div>
                                <p class="font-semibold text-gray-900">Course Modules</p>
                                <p class="text-gray-600 text-sm">Complete learning materials and resources</p>
                            </div>
                        </div>
                        <div class="flex items-start p-4 bg-purple-50 rounded-lg">
                            <i class="fas fa-award text-purple-600 mr-3 mt-1"></i>
                            <div>
                                <p class="font-semibold text-gray-900">Certificate</p>
                                <p class="text-gray-600 text-sm">Recognized internship certificate</p>
                            </div>
                        </div>
                        <div class="flex items-start p-4 bg-purple-50 rounded-lg">
                            <i class="fas fa-thumbs-up text-purple-600 mr-3 mt-1"></i>
                            <div>
                                <p class="font-semibold text-gray-900">Recommendation Letter</p>
                                <p class="text-gray-600 text-sm">Letter of recommendation from mentor</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Sidebar -->
            <div>
                <!-- Key Info Card -->
                <div class="bg-white rounded-lg shadow-md p-8 mb-8 sticky top-24">
                    <h3 class="text-xl font-bold text-gray-900 mb-6">Program Details</h3>
                    
                    <div class="space-y-6">
                        <!-- Duration -->
                        <div>
                            <p class="text-sm text-gray-600 mb-2">
                                <i class="fas fa-clock text-purple-600 mr-2"></i>Duration
                            </p>
                            <p class="text-lg font-semibold text-gray-900"><?php echo htmlspecialchars($internship['duration']); ?></p>
                        </div>
                        
                        <!-- Fee -->
                        <div>
                            <p class="text-sm text-gray-600 mb-2">
                                <i class="fas fa-money-bill text-purple-600 mr-2"></i>Program Fee
                            </p>
                            <p class="text-2xl font-bold text-purple-600"><?php echo formatCurrency($internship['fees']); ?></p>
                        </div>
                        
                        <!-- CTA Button -->
                        <?php if (isLoggedIn()): ?>
                            <?php if ($application_status === 'approved'): ?>
                                <a href="<?php echo SITE_URL; ?>/student/modules.php?internship_id=<?php echo $internship['id']; ?>&application_id=<?php echo $application_id; ?>" class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-3 rounded-lg text-center block transition">
                                    <i class="fas fa-book mr-2"></i>View Course
                                </a>
                            <?php elseif ($application_status === 'rejected'): ?>
                                <a href="<?php echo SITE_URL; ?>/pages/internship-apply.php?internship_id=<?php echo $internship['id']; ?>" class="w-full btn-primary text-white font-medium py-3 rounded-lg text-center block">
                                    <i class="fas fa-check mr-2"></i>Apply Now
                                </a>
                            <?php elseif ($application_status === 'pending'): ?>
                                <button disabled class="w-full bg-yellow-500 text-white font-medium py-3 rounded-lg text-center block cursor-not-allowed">
                                    <i class="fas fa-clock mr-2"></i>Pending
                                </button>
                            <?php else: ?>
                                <a href="<?php echo SITE_URL; ?>/pages/internship-apply.php?internship_id=<?php echo $internship['id']; ?>" class="w-full btn-primary text-white font-medium py-3 rounded-lg text-center block">
                                    <i class="fas fa-check mr-2"></i>Apply Now
                                </a>
                            <?php endif; ?>
                        <?php else: ?>
                            <a href="<?php echo SITE_URL; ?>/auth.php?action=login" class="w-full btn-primary text-white font-medium py-3 rounded-lg text-center block">
                                <i class="fas fa-sign-in-alt mr-2"></i>Login to Apply
                            </a>
                        <?php endif; ?>
                        
                        <!-- Info Box -->
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <p class="text-sm text-blue-900">
                                <i class="fas fa-info-circle mr-2"></i>
                                <strong>Payment Required:</strong> You'll be able to upload payment proof after applying.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action -->
<section class="bg-gradient-to-r from-purple-600 to-purple-800 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto text-white text-center">
        <h2 class="text-3xl font-bold mb-4">Ready to Join the Program?</h2>
        <p class="text-lg mb-8">Start your journey in software development with Digital Tarai</p>
        <?php if (isLoggedIn()): ?>
            <?php if ($application_status === 'approved'): ?>
                <a href="<?php echo SITE_URL; ?>/student/modules.php?internship_id=<?php echo $internship['id']; ?>&application_id=<?php echo $application_id; ?>" class="bg-white text-purple-600 font-bold py-3 px-8 rounded-lg hover:bg-gray-100 transition inline-block">
                    <i class="fas fa-book mr-2"></i>View Course Materials
                </a>
            <?php elseif ($application_status === 'rejected'): ?>
                <a href="<?php echo SITE_URL; ?>/pages/internship-apply.php?internship_id=<?php echo $internship['id']; ?>" class="bg-white text-purple-600 font-bold py-3 px-8 rounded-lg hover:bg-gray-100 transition inline-block">
                    <i class="fas fa-arrow-right mr-2"></i>Apply Now
                </a>
            <?php elseif ($application_status === 'pending'): ?>
                <button disabled class="bg-yellow-500 text-white font-bold py-3 px-8 rounded-lg cursor-not-allowed">
                    <i class="fas fa-clock mr-2"></i>Pending Review
                </button>
            <?php else: ?>
                <a href="<?php echo SITE_URL; ?>/pages/internship-apply.php?internship_id=<?php echo $internship['id']; ?>" class="bg-white text-purple-600 font-bold py-3 px-8 rounded-lg hover:bg-gray-100 transition inline-block">
                    <i class="fas fa-arrow-right mr-2"></i>Apply Now
                </a>
            <?php endif; ?>
        <?php else: ?>
            <a href="<?php echo SITE_URL; ?>/auth.php?action=register" class="bg-white text-purple-600 font-bold py-3 px-8 rounded-lg hover:bg-gray-100 transition inline-block">
                <i class="fas fa-user-plus mr-2"></i>Create Account
            </a>
        <?php endif; ?>
    </div>
</section>

<?php include '../app/views/footer.php'; ?>
