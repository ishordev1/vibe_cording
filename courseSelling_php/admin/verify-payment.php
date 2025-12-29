<?php
$pageTitle = 'Verify Payment - Digital Tarai Admin';
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../lib/functions.php';
require_once '../app/models/Payment.php';

// Check if user is admin
if (!isLoggedIn() || $_SESSION['user_type'] !== 'admin') {
    redirect('auth.php?action=login');
}

$paymentId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$paymentId) {
    redirect('dashboard.php');
}

$paymentModel = new Payment($conn);
$payment = $paymentModel->getById($paymentId);

if (!$payment) {
    echo "Payment not found";
    exit;
}

// Handle verify/reject
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'verify') {
        $paymentModel->verifyPayment($paymentId, $_SESSION['user_id']);
        setFlashMessage('success', 'Payment verified successfully');
    } elseif ($action === 'reject') {
        $paymentModel->rejectPayment($paymentId, $_SESSION['user_id']);
        setFlashMessage('success', 'Payment rejected');
    }
    
    redirect('admin/dashboard.php');
}
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
                <h1 class="text-2xl font-bold text-gray-900">Verify Payment</h1>
            </div>
        </div>
    </div>

    <section class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <!-- Payment Info -->
            <div class="bg-gray-50 p-8 border-b border-gray-200">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Payment Details</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <p class="text-gray-600 text-sm mb-2">Payment ID</p>
                        <p class="text-lg font-semibold text-gray-900">#<?php echo $payment['id']; ?></p>
                    </div>
                    <div>
                        <p class="text-gray-600 text-sm mb-2">Amount</p>
                        <p class="text-lg font-semibold text-gray-900"><?php echo formatCurrency($payment['amount']); ?></p>
                    </div>
                    <div>
                        <p class="text-gray-600 text-sm mb-2">Payment Method</p>
                        <p class="text-lg font-semibold text-gray-900"><?php echo htmlspecialchars($payment['payment_method']); ?></p>
                    </div>
                    <div>
                        <p class="text-gray-600 text-sm mb-2">Status</p>
                        <p>
                            <span class="inline-block px-3 py-1 rounded-full text-sm font-semibold bg-yellow-100 text-yellow-800">
                                <?php echo ucfirst($payment['status']); ?>
                            </span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Screenshot -->
            <div class="p-8">
                <h3 class="text-xl font-bold text-gray-900 mb-6">Payment Screenshot</h3>
                
                <?php if ($payment['screenshot']): ?>
                    <div class="mb-8">
                        <a href="<?php echo SITE_URL; ?>/public/uploads/<?php echo htmlspecialchars($payment['screenshot']); ?>" target="_blank">
                            <img src="<?php echo SITE_URL; ?>/public/uploads/<?php echo htmlspecialchars($payment['screenshot']); ?>" alt="Payment Screenshot" class="max-w-2xl max-h-96 rounded-lg border border-gray-200">
                        </a>
                        <p class="text-gray-600 text-sm mt-2">
                            <a href="<?php echo SITE_URL; ?>/public/uploads/<?php echo htmlspecialchars($payment['screenshot']); ?>" target="_blank" class="text-purple-600 hover:text-purple-700">
                                Open Full Size <i class="fas fa-external-link-alt ml-1"></i>
                            </a>
                        </p>
                    </div>
                <?php else: ?>
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 text-center">
                        <i class="fas fa-exclamation-triangle text-4xl text-yellow-600 mb-3 block"></i>
                        <p class="text-yellow-800">No screenshot uploaded</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Actions -->
            <div class="bg-gray-50 p-8 border-t border-gray-200">
                <form method="POST" class="flex space-x-4">
                    <button 
                        type="submit" 
                        name="action" 
                        value="verify"
                        class="flex-1 bg-green-600 text-white font-bold py-3 rounded-lg hover:bg-green-700 transition"
                    >
                        <i class="fas fa-check mr-2"></i>Verify & Approve
                    </button>
                    <button 
                        type="submit" 
                        name="action" 
                        value="reject"
                        class="flex-1 bg-red-600 text-white font-bold py-3 rounded-lg hover:bg-red-700 transition"
                    >
                        <i class="fas fa-times mr-2"></i>Reject Payment
                    </button>
                    <a 
                        href="<?php echo SITE_URL; ?>/admin/dashboard.php"
                        class="flex-1 bg-gray-400 text-white font-bold py-3 rounded-lg hover:bg-gray-500 transition text-center"
                    >
                        <i class="fas fa-times mr-2"></i>Cancel
                    </a>
                </form>
            </div>
        </div>
    </section>
</div>

<?php include '../app/views/footer.php'; ?>
