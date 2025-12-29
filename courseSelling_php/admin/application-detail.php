<?php
$pageTitle = 'Application Detail - Digital Tarai Admin';
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../lib/functions.php';
require_once '../app/models/Application.php';
require_once '../app/models/Payment.php';

// Check if user is admin
if (!isLoggedIn() || $_SESSION['user_type'] !== 'admin') {
    redirect('auth.php?action=login');
}

$applicationId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$applicationId) {
    redirect('dashboard.php');
}

$applicationModel = new Application($conn);
$paymentModel = new Payment($conn);

$application = $applicationModel->getWithPayment($applicationId);

if (!$application) {
    echo "Application not found";
    exit;
}

// Handle approval/rejection
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'approve') {
        $applicationModel->updateStatus($applicationId, 'approved');
        setFlashMessage('success', 'Application approved successfully');
    } elseif ($action === 'reject') {
        $applicationModel->updateStatus($applicationId, 'rejected');
        setFlashMessage('success', 'Application rejected');
    }
    
    redirect('admin/application-detail.php?id=' . $applicationId);
}

$payment = $paymentModel->getByApplicationId($applicationId);
?>
<?php include '../app/views/header.php'; ?>

<div class="min-h-screen bg-gray-50">
    <!-- Top Navigation -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-center space-x-4">
                <a href="<?php echo SITE_URL; ?>/admin/dashboard.php" class="text-gray-600 hover:text-gray-900">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h1 class="text-2xl font-bold text-gray-900">Application Details</h1>
            </div>
        </div>
    </div>

    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Student Information -->
                <div class="bg-white rounded-lg shadow-md p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Student Information</h2>
                    
                    <div class="space-y-4">
                        <div class="flex justify-between border-b border-gray-200 pb-4">
                            <span class="text-gray-600">Name:</span>
                            <span class="font-semibold text-gray-900"><?php echo htmlspecialchars($application['user_name']); ?></span>
                        </div>
                        <div class="flex justify-between border-b border-gray-200 pb-4">
                            <span class="text-gray-600">Email:</span>
                            <span class="font-semibold text-gray-900"><?php echo htmlspecialchars($application['user_email']); ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Application Date:</span>
                            <span class="font-semibold text-gray-900"><?php echo formatDate($application['created_at']); ?></span>
                        </div>
                    </div>
                </div>

                <!-- Internship Details -->
                <div class="bg-white rounded-lg shadow-md p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Internship Details</h2>
                    
                    <div class="space-y-4">
                        <div class="flex justify-between border-b border-gray-200 pb-4">
                            <span class="text-gray-600">Program:</span>
                            <span class="font-semibold text-gray-900"><?php echo htmlspecialchars($application['internship_title']); ?></span>
                        </div>
                        <div class="flex justify-between border-b border-gray-200 pb-4">
                            <span class="text-gray-600">Fee:</span>
                            <span class="font-semibold text-gray-900"><?php echo formatCurrency($application['amount']); ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Application Status:</span>
                            <span>
                                <?php 
                                $statusColor = [
                                    'pending' => 'yellow',
                                    'approved' => 'green',
                                    'rejected' => 'red'
                                ][$application['status']] ?? 'gray';
                                ?>
                                <span class="inline-block px-3 py-1 rounded-full text-sm font-semibold bg-<?php echo $statusColor; ?>-100 text-<?php echo $statusColor; ?>-800">
                                    <?php echo ucfirst($application['status']); ?>
                                </span>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Payment Information -->
                <div class="bg-white rounded-lg shadow-md p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Payment Information</h2>
                    
                    <div class="space-y-4">
                        <div class="flex justify-between border-b border-gray-200 pb-4">
                            <span class="text-gray-600">Amount:</span>
                            <span class="font-semibold text-gray-900"><?php echo formatCurrency($payment['amount'] ?? 0); ?></span>
                        </div>
                        <div class="flex justify-between border-b border-gray-200 pb-4">
                            <span class="text-gray-600">Payment Method:</span>
                            <span class="font-semibold text-gray-900"><?php echo htmlspecialchars($payment['payment_method'] ?? 'N/A'); ?></span>
                        </div>
                        <div class="flex justify-between pb-4">
                            <span class="text-gray-600">Status:</span>
                            <span>
                                <?php 
                                $pStatus = $payment['status'] ?? 'pending';
                                $pColor = [
                                    'pending' => 'yellow',
                                    'verified' => 'green',
                                    'rejected' => 'red'
                                ][$pStatus] ?? 'gray';
                                ?>
                                <span class="inline-block px-3 py-1 rounded-full text-sm font-semibold bg-<?php echo $pColor; ?>-100 text-<?php echo $pColor; ?>-800">
                                    <?php echo ucfirst($pStatus); ?>
                                </span>
                            </span>
                        </div>
                        
                        <?php if ($payment && $payment['screenshot']): ?>
                            <div>
                                <p class="text-gray-600 mb-3">Payment Screenshot:</p>
                                <a href="<?php echo SITE_URL; ?>/public/uploads/<?php echo htmlspecialchars($payment['screenshot']); ?>" target="_blank" class="inline-block">
                                    <img src="<?php echo SITE_URL; ?>/public/uploads/<?php echo htmlspecialchars($payment['screenshot']); ?>" alt="Payment Screenshot" class="max-w-md max-h-96 rounded-lg border border-gray-200">
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Sidebar - Actions -->
            <div>
                <div class="bg-white rounded-lg shadow-md p-8 sticky top-24">
                    <h3 class="text-lg font-bold text-gray-900 mb-6">Admin Actions</h3>
                    
                    <form method="POST" class="space-y-3">
                        <?php if ($application['status'] === 'pending'): ?>
                            <button 
                                type="submit" 
                                name="action" 
                                value="approve"
                                class="w-full bg-green-600 text-white font-medium py-2 rounded-lg hover:bg-green-700 transition"
                            >
                                <i class="fas fa-check mr-2"></i>Approve Application
                            </button>
                            <button 
                                type="submit" 
                                name="action" 
                                value="reject"
                                class="w-full bg-red-600 text-white font-medium py-2 rounded-lg hover:bg-red-700 transition"
                            >
                                <i class="fas fa-times mr-2"></i>Reject Application
                            </button>
                        <?php else: ?>
                            <div class="bg-gray-100 text-gray-600 font-medium py-2 rounded-lg text-center">
                                Application Already <?php echo ucfirst($application['status']); ?>
                            </div>
                        <?php endif; ?>
                    </form>
                    
                    <!-- Payment Verification -->
                    <?php if ($payment && $payment['status'] === 'pending'): ?>
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <h4 class="font-bold text-gray-900 mb-3">Payment Verification</h4>
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 text-sm text-yellow-800 mb-3">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                Payment screenshot is pending verification.
                            </div>
                            <a href="<?php echo SITE_URL; ?>/admin/verify-payment.php?id=<?php echo $payment['id']; ?>" class="w-full block text-center bg-purple-600 text-white font-medium py-2 rounded-lg hover:bg-purple-700 transition">
                                <i class="fas fa-check mr-2"></i>Verify Payment
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
</div>

<?php include '../app/views/footer.php'; ?>
