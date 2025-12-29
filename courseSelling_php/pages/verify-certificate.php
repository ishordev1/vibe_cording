<?php
// Student Certificate Verification Page
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

// Get certificate ID from URL
$cert_id = isset($_GET['cert']) ? intval($_GET['cert']) : 0;

if($cert_id == 0) {
    die('<h1>Invalid Certificate ID</h1>');
}

// Get certificate details
$cert_result = $conn->query("
    SELECT c.*, u.name, u.email, i.title, i.duration, i.skills, i.fees
    FROM certificates c
    JOIN users u ON c.user_id = u.id
    JOIN internships i ON c.internship_id = i.id
    WHERE c.id = $cert_id
");

if($cert_result->num_rows == 0) {
    die('<h1>Certificate Not Found</h1>');
}

$cert = $cert_result->fetch_assoc();

// Handle download request
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['download_cert'])) {
    $file_path = __DIR__ . '/../public/certificates/' . $cert['filename'];
    
    if(file_exists($file_path)) {
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . basename($cert['filename']) . '"');
        header('Content-Length: ' . filesize($file_path));
        header('Pragma: no-cache');
        header('Expires: 0');
        readfile($file_path);
        exit;
    } else {
        die('<h1>Certificate file not found</h1>');
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate Verification - Digital Tarai</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <div class="container mx-auto py-12 px-4">
        <div class="max-w-2xl mx-auto">
            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-4xl font-bold text-gray-900 mb-2">
                    <i class="fas fa-certificate text-purple-600 mr-2"></i>Certificate Verification
                </h1>
                <p class="text-gray-600">Digital Tarai Internship Program</p>
            </div>

            <!-- Certificate Details -->
            <div class="bg-white rounded-lg shadow-lg p-8 mb-6">
                <div class="border-2 border-purple-600 rounded-lg p-8">
                    <!-- Student Info -->
                    <div class="text-center mb-8">
                        <h2 class="text-3xl font-bold text-gray-900 mb-2"><?php echo htmlspecialchars($cert['name']); ?></h2>
                        <p class="text-gray-600"><?php echo htmlspecialchars($cert['email']); ?></p>
                    </div>

                    <!-- Certificate Content -->
                    <div class="bg-gray-50 rounded p-6 mb-6">
                        <p class="text-gray-700 mb-4">
                            <strong>This is to certify that</strong>
                        </p>
                        
                        <h3 class="text-2xl font-bold text-purple-600 mb-4">
                            <?php echo htmlspecialchars($cert['name']); ?>
                        </h3>
                        
                        <p class="text-gray-700 mb-6">
                            Has successfully completed the internship program in
                        </p>
                        
                        <h4 class="text-xl font-semibold text-gray-900 mb-4">
                            <?php echo htmlspecialchars($cert['title']); ?>
                        </h4>
                        
                        <p class="text-gray-700 mb-2">
                            With dedication, hard work, and commitment to excellence
                        </p>
                        <p class="text-gray-700">
                            This certificate is awarded as recognition of the knowledge and skills acquired
                        </p>
                    </div>

                    <!-- Details Grid -->
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div>
                            <p class="text-gray-600 text-sm"><strong>Internship Duration:</strong></p>
                            <p class="text-gray-900"><?php echo htmlspecialchars($cert['duration']); ?></p>
                        </div>
                        <div>
                            <p class="text-gray-600 text-sm"><strong>Generated Date:</strong></p>
                            <p class="text-gray-900"><?php echo date('M d, Y', strtotime($cert['generated_at'])); ?></p>
                        </div>
                        <div>
                            <p class="text-gray-600 text-sm"><strong>Certificate ID:</strong></p>
                            <p class="text-gray-900 font-mono text-sm"><?php echo str_pad($cert['id'], 8, '0', STR_PAD_LEFT); ?></p>
                        </div>
                        <div>
                            <p class="text-gray-600 text-sm"><strong>Verification Code:</strong></p>
                            <p class="text-gray-900 font-mono text-sm"><?php echo strtoupper(substr(hash('sha256', $cert['id'] . $cert['filename']), 0, 8)); ?></p>
                        </div>
                    </div>

                    <!-- Verification Status -->
                    <div class="bg-green-50 border border-green-200 rounded p-4 text-center">
                        <p class="text-green-800">
                            <i class="fas fa-check-circle mr-2"></i>
                            <strong>This certificate is authentic and verified</strong>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Download Button -->
            <div class="text-center">
                <form method="POST" action="" style="display: inline;">
                    <input type="hidden" name="download_cert" value="1">
                    <button type="submit" class="inline-flex items-center px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition font-semibold">
                        <i class="fas fa-download mr-2"></i>Download Certificate
                    </button>
                </form>
            </div>

            <!-- Footer -->
            <div class="text-center mt-8 text-gray-600 text-sm">
                <p>Digital Tarai - Professional Internship Programs</p>
                <p>Siraha, Nepal</p>
            </div>
        </div>
    </div>
</body>
</html>
