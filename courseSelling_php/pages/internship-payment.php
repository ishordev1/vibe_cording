<?php
$pageTitle = 'Payment - Digital Tarai';
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../lib/functions.php';
require_once '../app/models/Application.php';
require_once '../app/models/Payment.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect('auth.php?action=login');
}

$applicationId = isset($_GET['application_id']) ? (int)$_GET['application_id'] : 0;

if (!$applicationId) {
    redirect('pages/careers.php');
}

$applicationModel = new Application($conn);
$paymentModel = new Payment($conn);

$application = $applicationModel->getWithPayment($applicationId);

if (!$application || $application['user_id'] != $_SESSION['user_id']) {
    echo "Application not found";
    exit;
}

$payment = $paymentModel->getByApplicationId($applicationId);
$message = '';
$messageType = '';

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if file is actually uploaded
    if (!isset($_FILES['screenshot']) || $_FILES['screenshot']['error'] === UPLOAD_ERR_NO_FILE) {
        $message = 'Please upload a screenshot';
        $messageType = 'error';
    } else if ($_FILES['screenshot']['error'] !== UPLOAD_ERR_OK) {
        $message = 'File upload error. Please try again.';
        $messageType = 'error';
    } else if (!isset($_POST['confirm'])) {
        $message = 'Please confirm the payment details';
        $messageType = 'error';
    } else {
        $filename = $paymentModel->uploadScreenshot($payment['id'], $_FILES['screenshot']);
        
        if ($filename) {
            $message = 'Payment screenshot uploaded successfully! Your payment is now pending verification.';
            $messageType = 'success';
            sleep(2);
            redirect('student/dashboard.php');
        } else {
            $message = 'Failed to upload screenshot. Please try again.';
            $messageType = 'error';
        }
    }
}

$pageTitle = 'Upload Payment - Digital Tarai';
?>
<?php include '../app/views/header.php'; ?>

<div class="bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-4xl font-bold text-gray-900 mb-4">Upload Payment Screenshot</h1>
        <p class="text-lg text-gray-600">Submit your payment proof to complete the application</p>
    </div>
</div>

<section class="py-20 px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl mx-auto">
        <?php if ($message): ?>
            <div class="mb-6 p-4 bg-<?php echo $messageType === 'error' ? 'red' : 'green'; ?>-100 border border-<?php echo $messageType === 'error' ? 'red' : 'green'; ?>-400 text-<?php echo $messageType === 'error' ? 'red' : 'green'; ?>-700 rounded">
                <i class="fas fa-<?php echo $messageType === 'error' ? 'exclamation-circle' : 'check-circle'; ?> mr-2"></i><?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <!-- Payment Info Card -->
        <div class="bg-white rounded-lg shadow-md p-8 mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Payment Details</h2>
            
            <div class="space-y-6">
                <!-- Internship -->
                <div class="flex justify-between items-center pb-4 border-b border-gray-200">
                    <span class="text-gray-600">Internship:</span>
                    <span class="font-semibold text-gray-900"><?php echo htmlspecialchars($application['internship_title']); ?></span>
                </div>
                
                <!-- Amount -->
                <div class="flex justify-between items-center pb-4 border-b border-gray-200">
                    <span class="text-gray-600">Amount Due:</span>
                    <span class="font-semibold text-2xl text-purple-600"><?php echo formatCurrency($application['amount']); ?></span>
                </div>
                
                <!-- Payment Method -->
                <div class="flex justify-between items-center pb-4 border-b border-gray-200">
                    <span class="text-gray-600">Payment Method:</span>
                    <span class="font-semibold text-gray-900"><?php echo htmlspecialchars($application['payment_method']); ?></span>
                </div>
                
                <!-- Status -->
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Status:</span>
                    <span class="inline-block px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm font-semibold">
                        <i class="fas fa-clock mr-2"></i><?php echo ucfirst($application['payment_status']); ?>
                    </span>
                </div>
            </div>
        </div>
        
        <!-- Payment Instructions -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-8">
            <h3 class="font-bold text-blue-900 mb-3">
                <i class="fas fa-info-circle mr-2"></i>Payment Instructions
            </h3>
            <div class="space-y-2 text-blue-800 text-sm">
                <p>You can pay via any of these methods:</p>
                <ul class="list-disc list-inside space-y-2 mt-3">
                    <li>Bank Transfer to: Digital Tarai Pvt. Ltd.</li>
                    <li>Mobile Wallets: Khalti, Esewa, IME Pay</li>
                    <li>Direct Cash Transfer</li>
                    <li>Online Payment Gateway</li>
                </ul>
                <p class="mt-4"><strong>After payment, take a screenshot and upload it below.</strong></p>
            </div>
        </div>
        
        <!-- Upload Form -->
        <div class="bg-white rounded-lg shadow-md p-8">
            <form method="POST" enctype="multipart/form-data" class="space-y-6">
                <!-- File Upload -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-4">
                        <i class="fas fa-file-image text-purple-600 mr-2"></i>Payment Screenshot
                    </label>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-purple-600 transition" id="dropZone">
                        <input 
                            type="file" 
                            name="screenshot" 
                            id="screenshot" 
                            accept="image/jpeg,image/png,application/pdf"
                            required
                            class="hidden"
                        >
                        <label for="screenshot" class="cursor-pointer" id="uploadLabel">
                            <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-4 block"></i>
                            <p class="font-semibold text-gray-700">Click to upload or drag and drop</p>
                            <p class="text-gray-500 text-sm mt-2">PNG, JPG or PDF (Max 5MB)</p>
                        </label>
                        <!-- Image Preview -->
                        <div id="imagePreview" class="mt-4 hidden">
                            <img id="previewImage" src="" alt="Preview" class="max-w-full max-h-64 mx-auto rounded-lg">
                            <p id="fileName" class="text-gray-600 text-sm mt-2"></p>
                        </div>
                    </div>
                </div>
                
                <!-- Additional Info -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Payment ID/Reference (Optional)</label>
                    <input 
                        type="text" 
                        name="payment_id" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                        placeholder="Transaction ID or reference number"
                    >
                </div>
                
                <!-- Confirmation -->
                <div class="flex items-start">
                    <input 
                        type="checkbox" 
                        id="confirm" 
                        name="confirm" 
                        required
                        class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded mt-1"
                    >
                    <label for="confirm" class="ml-3 block text-sm text-gray-700">
                        I confirm that I have paid <strong><?php echo formatCurrency($application['amount']); ?></strong> for this internship and the uploaded screenshot is valid proof of payment
                    </label>
                </div>
                
                <!-- Submit -->
                <button 
                    type="submit"
                    onclick="return validatePaymentForm()"
                    class="w-full btn-primary text-white font-medium py-3 rounded-lg"
                >
                    <i class="fas fa-upload mr-2"></i>Upload Payment Screenshot
                </button>
                
                <a 
                    href="<?php echo SITE_URL; ?>/student/dashboard.php" 
                    class="w-full btn-secondary text-center font-medium py-3 rounded-lg block"
                >
                    <i class="fas fa-arrow-left mr-2"></i>Go to Dashboard
                </a>
            </form>
        </div>
    </div>
</section>

<script>
    // Form validation
    function validatePaymentForm() {
        const fileInput = document.getElementById('screenshot');
        const confirmCheckbox = document.getElementById('confirm');
        
        // Check if file is selected
        if (!fileInput.files || fileInput.files.length === 0) {
            alert('Please upload a payment screenshot');
            return false;
        }
        
        // Check if confirmation checkbox is checked
        if (!confirmCheckbox.checked) {
            alert('Please confirm the payment details');
            return false;
        }
        
        return true;
    }
    
    // File upload preview functionality
    const fileInput = document.getElementById('screenshot');
    const dropZone = document.getElementById('dropZone');
    const uploadLabel = document.getElementById('uploadLabel');
    const imagePreview = document.getElementById('imagePreview');
    const previewImage = document.getElementById('previewImage');
    const fileName = document.getElementById('fileName');
    
    // Handle file selection
    fileInput.addEventListener('change', function() {
        handleFileSelect(this.files[0]);
    });
    
    // Handle drag and drop
    dropZone.addEventListener('dragover', function(e) {
        e.preventDefault();
        dropZone.classList.add('border-purple-600', 'bg-purple-50');
    });
    
    dropZone.addEventListener('dragleave', function(e) {
        e.preventDefault();
        dropZone.classList.remove('border-purple-600', 'bg-purple-50');
    });
    
    dropZone.addEventListener('drop', function(e) {
        e.preventDefault();
        dropZone.classList.remove('border-purple-600', 'bg-purple-50');
        
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            fileInput.files = files;
            handleFileSelect(files[0]);
        }
    });
    
    function handleFileSelect(file) {
        if (!file) return;
        
        // Validate file size (5MB max)
        if (file.size > 5 * 1024 * 1024) {
            alert('File size must be less than 5MB');
            return;
        }
        
        // Check file type
        const validTypes = ['image/jpeg', 'image/png', 'application/pdf'];
        if (!validTypes.includes(file.type)) {
            alert('Please upload a PNG, JPG, or PDF file');
            return;
        }
        
        // Show preview
        uploadLabel.classList.add('hidden');
        imagePreview.classList.remove('hidden');
        fileName.textContent = file.name;
        
        // Create preview
        const reader = new FileReader();
        reader.onload = function(e) {
            if (file.type === 'application/pdf') {
                previewImage.src = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="200" height="200" viewBox="0 0 200 200"%3E%3Crect width="200" height="200" fill="%23f3f4f6"/%3E%3Ctext x="100" y="100" text-anchor="middle" dy=".3em" font-size="16" font-family="Arial" fill="%23666"%3EPDF File%3C/text%3E%3C/svg%3E';
            } else {
                previewImage.src = e.target.result;
            }
        };
        reader.readAsDataURL(file);
    }
</script>

<?php include '../app/views/footer.php'; ?>
