<?php
$pageTitle = 'Admin Dashboard - Digital Tarai';
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../lib/functions.php';
require_once '../app/models/User.php';
require_once '../app/models/Internship.php';
require_once '../app/models/Application.php';
require_once '../app/models/Blog.php';
require_once '../app/models/Payment.php';

// Check if admin is logged in
if (!isLoggedIn() || $_SESSION['user_type'] !== 'admin') {
    redirect('auth.php?action=login');
}

$userModel = new User($conn);
$internshipModel = new Internship($conn);
$applicationModel = new Application($conn);
$blogModel = new Blog($conn);
$paymentModel = new Payment($conn);

// Get statistics
$totalUsers = $conn->query("SELECT COUNT(*) as count FROM users WHERE user_type='student'")->fetch_assoc()['count'];
$totalInternships = $conn->query("SELECT COUNT(*) as count FROM internships")->fetch_assoc()['count'];
$totalApplications = $conn->query("SELECT COUNT(*) as count FROM applications")->fetch_assoc()['count'];
$approvedApplications = $conn->query("SELECT COUNT(*) as count FROM applications WHERE status='approved'")->fetch_assoc()['count'];
$pendingApplications = $conn->query("SELECT COUNT(*) as count FROM applications WHERE status='pending'")->fetch_assoc()['count'];
$totalBlogs = $conn->query("SELECT COUNT(*) as count FROM blogs WHERE status='published'")->fetch_assoc()['count'];
$pendingPayments = $conn->query("SELECT COUNT(*) as count FROM payments WHERE status='pending'")->fetch_assoc()['count'];

// Get active section from URL
$section = isset($_GET['section']) ? $_GET['section'] : 'dashboard';
$subsection = isset($_GET['subsection']) ? $_GET['subsection'] : '';

// Handle actions
$message = '';
$messageType = '';
?>
<?php include '../app/views/header.php'; ?>

<div class="flex min-h-screen bg-gray-100">
    <!-- Sidebar -->
    <div class="w-64 bg-gray-900 text-white fixed h-full overflow-y-auto">
        <div class="p-6">
            <h2 class="text-2xl font-bold mb-8">
                <i class="fas fa-shield-alt mr-2"></i>Admin Panel
            </h2>
            
            <nav class="space-y-2">
                <!-- Dashboard -->
                <a href="?section=dashboard" class="block px-4 py-3 rounded-lg hover:bg-gray-800 transition <?php echo $section === 'dashboard' ? 'bg-purple-600' : ''; ?>">
                    <i class="fas fa-chart-line mr-2"></i>Dashboard
                </a>
                
                <!-- Users -->
                <a href="?section=users" class="block px-4 py-3 rounded-lg hover:bg-gray-800 transition <?php echo $section === 'users' ? 'bg-purple-600' : ''; ?>">
                    <i class="fas fa-users mr-2"></i>Users
                    <span class="float-right bg-red-500 px-2 py-1 rounded-full text-xs"><?php echo $totalUsers; ?></span>
                </a>
                
                <!-- Internships -->
                <a href="?section=internships" class="block px-4 py-3 rounded-lg hover:bg-gray-800 transition <?php echo $section === 'internships' ? 'bg-purple-600' : ''; ?>">
                    <i class="fas fa-briefcase mr-2"></i>Internships
                    <span class="float-right bg-blue-500 px-2 py-1 rounded-full text-xs"><?php echo $totalInternships; ?></span>
                </a>
                
                <!-- Applications -->
                <a href="?section=applications" class="block px-4 py-3 rounded-lg hover:bg-gray-800 transition <?php echo $section === 'applications' ? 'bg-purple-600' : ''; ?>">
                    <i class="fas fa-file-alt mr-2"></i>Applications
                    <span class="float-right bg-orange-500 px-2 py-1 rounded-full text-xs"><?php echo $totalApplications; ?></span>
                </a>
                
                <!-- Payments -->
                <a href="?section=payments" class="block px-4 py-3 rounded-lg hover:bg-gray-800 transition <?php echo $section === 'payments' ? 'bg-purple-600' : ''; ?>">
                    <i class="fas fa-credit-card mr-2"></i>Payments
                    <span class="float-right bg-red-500 px-2 py-1 rounded-full text-xs"><?php echo $pendingPayments; ?></span>
                </a>
                
                <!-- Blogs -->
                <a href="?section=blogs" class="block px-4 py-3 rounded-lg hover:bg-gray-800 transition <?php echo $section === 'blogs' ? 'bg-purple-600' : ''; ?>">
                    <i class="fas fa-blog mr-2"></i>Blogs
                    <span class="float-right bg-green-500 px-2 py-1 rounded-full text-xs"><?php echo $totalBlogs; ?></span>
                </a>
                
                <!-- Certificates -->
                <a href="?section=certificates" class="block px-4 py-3 rounded-lg hover:bg-gray-800 transition <?php echo $section === 'certificates' ? 'bg-purple-600' : ''; ?>">
                    <i class="fas fa-award mr-2"></i>Certificates
                </a>
                
                <!-- Offer Letters -->
                <a href="?section=offer-letters" class="block px-4 py-3 rounded-lg hover:bg-gray-800 transition <?php echo $section === 'offer-letters' ? 'bg-purple-600' : ''; ?>">
                    <i class="fas fa-file-contract mr-2"></i>Offer Letters
                </a>
                
                <hr class="my-4 border-gray-700">
                
                <!-- Logout -->
                <a href="<?php echo SITE_URL; ?>/auth.php?action=logout" class="block px-4 py-3 rounded-lg hover:bg-red-600 transition text-red-400">
                    <i class="fas fa-sign-out-alt mr-2"></i>Logout
                </a>
            </nav>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="ml-64 flex-1">
        <!-- Top Bar -->
        <div class="bg-white shadow-sm border-b border-gray-200 p-6 sticky top-0 z-10">
            <div class="flex justify-between items-center">
                <h1 class="text-3xl font-bold text-gray-900">
                    <?php
                    switch($section) {
                        case 'dashboard': echo 'Dashboard'; break;
                        case 'users': echo 'Users Management'; break;
                        case 'internships': echo 'Internships Management'; break;
                        case 'applications': echo 'Applications Management'; break;
                        case 'payments': echo 'Payments Verification'; break;
                        case 'blogs': echo 'Blog Management'; break;
                        case 'certificates': echo 'Certificates'; break;
                        case 'offer-letters': echo 'Offer Letters'; break;
                        default: echo 'Dashboard';
                    }
                    ?>
                </h1>
                <div class="text-right">
                    <p class="text-gray-600">Welcome, <strong><?php echo htmlspecialchars($_SESSION['user_name']); ?></strong></p>
                    <p class="text-sm text-gray-500"><?php echo date('l, F j, Y'); ?></p>
                </div>
            </div>
        </div>
        
        <!-- Content Area -->
        <div class="p-6">
            <?php if($message): ?>
                <div class="mb-6 p-4 bg-<?php echo $messageType === 'error' ? 'red' : 'green'; ?>-100 border border-<?php echo $messageType === 'error' ? 'red' : 'green'; ?>-400 text-<?php echo $messageType === 'error' ? 'red' : 'green'; ?>-700 rounded">
                    <i class="fas fa-<?php echo $messageType === 'error' ? 'exclamation-circle' : 'check-circle'; ?> mr-2"></i><?php echo $message; ?>
                </div>
            <?php endif; ?>
            
            <?php
            // Load the appropriate section
            switch($section) {
                case 'dashboard':
                    include 'sections/dashboard.php';
                    break;
                case 'users':
                    include 'sections/users.php';
                    break;
                case 'internships':
                    include 'sections/internships.php';
                    break;
                case 'applications':
                    include 'sections/applications.php';
                    break;
                case 'payments':
                    include 'sections/payments.php';
                    break;
                case 'blogs':
                    include 'sections/blogs.php';
                    break;
                case 'certificates':
                    include 'sections/certificates.php';
                    break;
                case 'offer-letters':
                    include 'sections/offer-letters.php';
                    break;
                default:
                    include 'sections/dashboard.php';
            }
            ?>
        </div>
    </div>
</div>

<?php include '../app/views/footer.php'; ?>
