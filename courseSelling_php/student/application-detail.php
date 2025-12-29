<?php
$pageTitle = 'Application Detail - Digital Tarai';
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../lib/functions.php';
require_once '../app/models/Application.php';
require_once '../app/models/Payment.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect('auth.php?action=login');
}

// Handle offer letter download
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['download_letter'])) {
    $app_id = intval($_POST['download_letter']);
    
    // Verify this application belongs to the logged-in user
    $auth_check = $conn->query("SELECT user_id FROM applications WHERE id=$app_id");
    $auth_app = $auth_check->fetch_assoc();
    
    if($auth_app && $auth_app['user_id'] == $_SESSION['user_id']) {
        $letter_result = $conn->query("SELECT * FROM offer_letters WHERE application_id=$app_id");
        if($letter_result && $letter_result->num_rows > 0) {
            $letter = $letter_result->fetch_assoc();
            $file_path = __DIR__ . '/../public/offer-letters/' . $letter['filename'];
            
            if(file_exists($file_path)) {
                header('Content-Type: application/pdf');
                header('Content-Disposition: attachment; filename="' . basename($letter['filename']) . '"');
                header('Content-Length: ' . filesize($file_path));
                header('Pragma: no-cache');
                header('Expires: 0');
                readfile($file_path);
                exit;
            }
        }
    }
    die('Offer letter not found');
}

$applicationId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$applicationId) {
    redirect('dashboard.php');
}

$applicationModel = new Application($conn);
$paymentModel = new Payment($conn);

$application = $applicationModel->getWithPayment($applicationId);

if (!$application || $application['user_id'] != $_SESSION['user_id']) {
    echo "Application not found";
    exit;
}

$payment = $paymentModel->getByApplicationId($applicationId);
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
                <h1 class="text-2xl font-bold text-gray-900">Application Details</h1>
            </div>
        </div>
    </div>

    <section class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Status Alert -->
        <div class="mb-8">
            <?php 
            $statusColor = [
                'pending' => ['bg' => 'yellow-50', 'border' => 'yellow-200', 'text' => 'yellow-800', 'icon' => 'hourglass-half'],
                'approved' => ['bg' => 'green-50', 'border' => 'green-200', 'text' => 'green-800', 'icon' => 'check-circle'],
                'rejected' => ['bg' => 'red-50', 'border' => 'red-200', 'text' => 'red-800', 'icon' => 'times-circle']
            ][$application['status']] ?? [];
            ?>
            <div class="bg-<?php echo $statusColor['bg']; ?> border border-<?php echo $statusColor['border']; ?> rounded-lg p-6 text-<?php echo $statusColor['text']; ?>">
                <div class="flex items-center">
                    <i class="fas fa-<?php echo $statusColor['icon']; ?> text-2xl mr-4"></i>
                    <div>
                        <h3 class="font-bold text-lg">Application Status</h3>
                        <p class="mt-1"><?php echo ucfirst($application['status']); ?> - Your application is currently <?php echo $application['status']; ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2">
                <!-- Internship Details -->
                <div class="bg-white rounded-lg shadow-md p-8 mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Internship Program</h2>
                    
                    <div class="space-y-4">
                        <div class="flex justify-between border-b border-gray-200 pb-4">
                            <span class="text-gray-600">Program Name:</span>
                            <span class="font-semibold text-gray-900"><?php echo htmlspecialchars($application['internship_title']); ?></span>
                        </div>
                        <div class="flex justify-between border-b border-gray-200 pb-4">
                            <span class="text-gray-600">Program Fee:</span>
                            <span class="font-semibold text-gray-900"><?php echo formatCurrency($application['amount']); ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Applied On:</span>
                            <span class="font-semibold text-gray-900"><?php echo formatDate($application['created_at']); ?></span>
                        </div>
                    </div>
                </div>

                <!-- Payment Details -->
                <div class="bg-white rounded-lg shadow-md p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Payment Information</h2>
                    
                    <div class="space-y-4">
                        <div class="flex justify-between border-b border-gray-200 pb-4">
                            <span class="text-gray-600">Amount Due:</span>
                            <span class="font-semibold text-gray-900"><?php echo formatCurrency($payment['amount'] ?? 0); ?></span>
                        </div>
                        <div class="flex justify-between border-b border-gray-200 pb-4">
                            <span class="text-gray-600">Payment Method:</span>
                            <span class="font-semibold text-gray-900"><?php echo htmlspecialchars($payment['payment_method'] ?? 'N/A'); ?></span>
                        </div>
                        <div class="flex justify-between border-b border-gray-200 pb-4">
                            <span class="text-gray-600">Status:</span>
                            <span class="font-semibold">
                                <?php 
                                $pStatus = $payment['status'] ?? 'pending';
                                $pColor = [
                                    'pending' => 'yellow',
                                    'verified' => 'green',
                                    'rejected' => 'red'
                                ][$pStatus] ?? 'gray';
                                ?>
                                <span class="inline-block px-3 py-1 rounded-full text-sm bg-<?php echo $pColor; ?>-100 text-<?php echo $pColor; ?>-800">
                                    <?php echo ucfirst($pStatus); ?>
                                </span>
                            </span>
                        </div>
                        
                        <?php if ($payment && $payment['screenshot']): ?>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Screenshot:</span>
                                <a href="<?php echo SITE_URL; ?>/public/uploads/<?php echo htmlspecialchars($payment['screenshot']); ?>" target="_blank" class="text-purple-600 hover:text-purple-700 font-medium">
                                    View <i class="fas fa-external-link-alt ml-1"></i>
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div>
                <!-- Quick Actions -->
                <div class="bg-white rounded-lg shadow-md p-8 sticky top-24">
                    <h3 class="text-lg font-bold text-gray-900 mb-6">Actions</h3>
                    
                    <div class="space-y-3">
                        <?php if ($application['status'] === 'approved'): ?>
                            <?php 
                            // Check if offer letter exists
                            $offer_letter_result = $conn->query("SELECT * FROM offer_letters WHERE application_id=$applicationId");
                            $offer_letter = $offer_letter_result && $offer_letter_result->num_rows > 0 ? $offer_letter_result->fetch_assoc() : null;
                            ?>
                            <?php if ($offer_letter): ?>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="download_letter" value="<?php echo $applicationId; ?>">
                                    <button type="submit" class="w-full bg-green-600 text-white font-medium py-2 rounded-lg hover:bg-green-700 transition">
                                        <i class="fas fa-download mr-2"></i>Download Offer Letter
                                    </button>
                                </form>
                            <?php else: ?>
                                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-sm text-yellow-800">
                                    <i class="fas fa-hourglass-half mr-2"></i>
                                    Offer letter is being prepared. Please check back soon.
                                </div>
                            <?php endif; ?>
                            <a href="<?php echo SITE_URL; ?>/student/modules.php?internship_id=<?php echo $application['internship_id']; ?>" class="w-full bg-blue-600 text-white font-medium py-2 rounded-lg hover:bg-blue-700 transition text-center block">
                                <i class="fas fa-book mr-2"></i>View Course Modules
                            </a>
                        <?php elseif ($application['status'] === 'pending'): ?>
                            <?php if (!$payment || $payment['status'] === 'pending'): ?>
                                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-sm text-yellow-800">
                                    <i class="fas fa-hourglass-half mr-2"></i>
                                    Your payment is still pending. Please complete the payment to proceed.
                                </div>
                            <?php else: ?>
                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-sm text-blue-800">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    Your payment is awaiting verification. This may take 24-48 hours.
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="bg-red-50 border border-red-200 rounded-lg p-4 text-sm text-red-800">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                Your application has been rejected. Contact support for more details.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Need Help -->
                <div class="bg-purple-50 border border-purple-200 rounded-lg p-6 mt-8">
                    <h4 class="font-bold text-purple-900 mb-3">Need Help?</h4>
                    <p class="text-sm text-purple-800 mb-4">
                        Contact our support team for any queries about your application.
                    </p>
                    <a href="<?php echo SITE_URL; ?>/pages/contact.php" class="text-purple-600 hover:text-purple-700 font-medium text-sm">
                        Contact Support <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>
</div>

<?php include '../app/views/footer.php'; ?>
