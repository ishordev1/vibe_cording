<?php
// Start output buffering to handle header redirects
ob_start();

// Check if customize template is requested
if(isset($_GET['customize'])) {
    ob_end_clean();
    include 'customize-template.php';
    return;
}

// Handle offer letter generation
if(isset($_GET['generate'])) {
    $user_id = intval($_GET['generate']);
    $internship_id = intval($_GET['internship']);
    
    $user_result = $conn->query("SELECT * FROM users WHERE id=$user_id");
    $internship_result = $conn->query("SELECT * FROM internships WHERE id=$internship_id");
    
    if($user_result->num_rows > 0 && $internship_result->num_rows > 0) {
        $user = $user_result->fetch_assoc();
        $internship = $internship_result->fetch_assoc();
        
        // Get application_id first so we can use offer letter ID for QR code
        $app_result = $conn->query("SELECT id FROM applications WHERE user_id=$user_id AND internship_id=$internship_id");
        if($app_result->num_rows == 0) {
            ob_end_clean();
            header("Location: ?section=offer-letters&error=Application not found");
            exit;
        }
        
        $app = $app_result->fetch_assoc();
        $app_id = $app['id'];
        
        // Delete previous offer letter if exists
        $old_letter_result = $conn->query("SELECT file_path FROM offer_letters WHERE application_id=$app_id");
        if($old_letter_result && $old_letter_result->num_rows > 0) {
            $old_letter = $old_letter_result->fetch_assoc();
            // Delete file from server
            if(file_exists($old_letter['file_path'])) {
                unlink($old_letter['file_path']);
            }
            // Delete database record
            $conn->query("DELETE FROM offer_letters WHERE application_id=$app_id");
        }
        
        // First, insert a placeholder record to get the offer letter ID
        $admin_id = $_SESSION['user_id'];
        $generated_at = date('Y-m-d H:i:s');
        $conn->query("INSERT INTO offer_letters (application_id, user_id, internship_id, filename, file_path, generated_by, generated_at) 
                     VALUES ($app_id, $user_id, $internship_id, 'temp', 'temp', $admin_id, '$generated_at')");
        $letter_id = $conn->insert_id;
        
        // Now create the QR code URL with the offer letter ID
        $qr_url = SITE_URL . "/pages/verify-certificate.php?cert=" . $letter_id;
        
        // Generate QR code as embedded base64 image for PDF compatibility
        $qr_code_api = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" . urlencode($qr_url);
        
        // Fetch QR code and convert to base64
        $qr_image_data = @file_get_contents($qr_code_api);
        if ($qr_image_data !== false) {
            $qr_code_base64 = 'data:image/png;base64,' . base64_encode($qr_image_data);
        } else {
            // Fallback if API fails
            $qr_code_base64 = $qr_code_api;
        }
        
        // Fetch custom template settings
        $template_settings = [];
        $settings_result = $conn->query("SELECT settings FROM template_settings WHERE template_type='offer-letters'");
        if($settings_result && $settings_result->num_rows > 0) {
            $settings_row = $settings_result->fetch_assoc();
            $template_settings = json_decode($settings_row['settings'], true);
        }
        
        // Set defaults from template or use hardcoded defaults
        $director_name = $template_settings['director_name'] ?? 'Director';
        $coordinator_name = $template_settings['coordinator_name'] ?? 'Coordinator';
        $org_name = $template_settings['organization_name'] ?? 'Digital Tarai';
        $org_address = $template_settings['organization_address'] ?? 'Siraha, Nepal';
        
        // Create offer letter HTML
        $offer_letter_html = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css'>
            <style>
                * { margin: 0; padding: 0; }
                body { font-family: 'Arial', sans-serif; line-height: 1.6; }
                .container {
                    max-width: 8.5in;
                    height: 11in;
                    margin: 0 auto;
                    padding: 1in;
                    box-sizing: border-box;
                    background: white;
                }
                .header {
                    text-align: center;
                    margin-bottom: 30px;
                    border-bottom: 3px solid #2c3e50;
                    padding-bottom: 15px;
                }
                .logo {
                    font-size: 28px;
                    font-weight: bold;
                    color: #8e44ad;
                    margin-bottom: 5px;
                }
                .company-info {
                    font-size: 12px;
                    color: #2c3e50;
                    margin-top: 5px;
                }
                .qr-code {
                    position: absolute;
                    top: 20px;
                    right: 30px;
                    text-align: center;
                }
                .qr-code img {
                    width: 100px;
                    height: 100px;
                    border: 1px solid #2c3e50;
                }
                .qr-label {
                    font-size: 9px;
                    color: #2c3e50;
                    margin-top: 3px;
                }
                @media print {
                    body { margin: 0; padding: 0; }
                    .controls { display: none; }
                    .container { page-break-inside: avoid; }
                }
                .controls {
                    position: fixed;
                    top: 20px;
                    right: 20px;
                    z-index: 1000;
                    display: flex;
                    gap: 10px;
                }
                .controls button {
                    padding: 10px 20px;
                    background-color: #8e44ad;
                    color: white;
                    border: none;
                    border-radius: 5px;
                    cursor: pointer;
                    font-size: 14px;
                    font-weight: bold;
                    box-shadow: 0 2px 5px rgba(0,0,0,0.2);
                    transition: background-color 0.3s;
                }
                .controls button:hover {
                    background-color: #7d3c98;
                }
                .controls button.print-btn {
                    background-color: #3498db;
                }
                .controls button.print-btn:hover {
                    background-color: #2980b9;
                }
                .controls button.download-btn {
                    background-color: #27ae60;
                }
                .controls button.download-btn:hover {
                    background-color: #229954;
                }
                .date-box {
                    text-align: right;
                    margin-bottom: 20px;
                    font-size: 14px;
                }
                .recipient {
                    margin-bottom: 20px;
                    font-size: 14px;
                }
                .recipient p {
                    margin: 5px 0;
                }
                .subject {
                    font-weight: bold;
                    margin-bottom: 20px;
                    text-align: center;
                    font-size: 16px;
                    color: #2c3e50;
                    text-transform: uppercase;
                }
                .body {
                    font-size: 14px;
                    line-height: 1.8;
                    margin-bottom: 20px;
                    text-align: justify;
                }
                .body p {
                    margin-bottom: 15px;
                }
                .terms {
                    font-size: 12px;
                    margin: 20px 0;
                    padding: 15px;
                    border: 1px solid #ddd;
                    background: #f9f9f9;
                }
                .terms h4 {
                    margin-bottom: 10px;
                    color: #2c3e50;
                }
                .terms ul {
                    margin-left: 20px;
                }
                .terms li {
                    margin-bottom: 5px;
                }
                .closing {
                    margin-top: 30px;
                }
                .signature-section {
                    margin-top: 40px;
                    display: flex;
                    justify-content: space-between;
                }
                .signature {
                    width: 150px;
                    text-align: center;
                }
                .signature-line {
                    border-top: 2px solid #2c3e50;
                    margin-bottom: 5px;
                    margin-top: 40px;
                }
                .signature-name {
                    font-weight: bold;
                    font-size: 12px;
                }
            </style>
        </head>
        <body>
            <div class='controls'>
                <button class='print-btn' onclick='window.print()'>
                    <i class='fas fa-print'></i> Print
                </button>
                <button class='download-btn' onclick='downloadOfferLetter()'>
                    <i class='fas fa-download'></i> Download PDF
                </button>
            </div>
            
            <div class='container'>
                <div class='qr-code'>
                    <img src='" . $qr_code_base64 . "' alt='Offer Letter Verification QR Code'>
                    <div class='qr-label'>Scan to verify</div>
                </div>
                
                <div class='header'>
                    <div class='logo'>🚀 Digital Tarai</div>
                    <div class='company-info'>
                        Siraha, Nepal | www.digitaltarai.com<br>
                        Email: info@digitaltarai.com | Phone: +977-XXXXXXXXXX
                    </div>
                </div>
                
                <div class='date-box'>
                    <strong>Date:</strong> " . date('d F, Y') . "
                </div>
                
                <div class='recipient'>
                    <p><strong>" . htmlspecialchars($user['name']) . "</strong></p>
                    <p>" . htmlspecialchars($user['email']) . "</p>
                    <p>" . htmlspecialchars($user['phone']) . "</p>
                </div>
                
                <div class='subject'>
                    INTERNSHIP OFFER LETTER
                </div>
                
                <div class='body'>
                    <p>Dear " . htmlspecialchars($user['name']) . ",</p>
                    
                    <p>We are pleased to offer you a position as an <strong>" . htmlspecialchars($internship['title']) . "</strong> Intern at Digital Tarai.</p>
                    
                    <p>We are impressed with your qualifications and your interest in joining our organization. We believe you will be a valuable addition to our team.</p>
                    
                    <p><strong>Internship Details:</strong></p>
                    <ul style='margin: 10px 0 15px 20px;'>
                        <li>Position: " . htmlspecialchars($internship['title']) . "</li>
                        <li>Duration: " . htmlspecialchars($internship['duration']) . "</li>
                        <li>Program Fee: Rs. " . number_format($internship['fees']) . "</li>
                        <li>Start Date: To be confirmed</li>
                    </ul>
                    
                    <p>During your internship, you will work on real projects and gain hands-on experience in " . htmlspecialchars($internship['title']) . ". You will be mentored by experienced professionals in the field.</p>
                </div>
                
                <div class='terms'>
                    <h4>Key Benefits:</h4>
                    <ul>
                        <li>Professional training and mentorship</li>
                        <li>Work on live projects</li>
                        <li>Certificate of completion</li>
                        <li>Letter of recommendation</li>
                        <li>Skill development in modern technologies</li>
                    </ul>
                </div>
                
                <div class='closing'>
                    <p>Please confirm your acceptance of this offer by signing below and returning it to us. If you have any questions, please do not hesitate to contact us.</p>
                    
                    <p>We look forward to having you join our team!</p>
                    
                    <p>Best Regards,</p>
                </div>
                
                <div class='signature-section'>
                    <div class='signature'>
                        <div class='signature-line'></div>
                        <div class='signature-name'>" . htmlspecialchars($director_name) . "</div>
                    </div>
                    
                    <div class='signature'>
                        <p style='font-size: 12px; margin-bottom: 40px;'>Candidate Signature</p>
                        <div class='signature-line'></div>
                        <div class='signature-name'>" . htmlspecialchars($user['name']) . "</div>
                    </div>
                </div>
            </div>
        </body>
        <script src='https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js'></script>
        <script>
        function downloadOfferLetter() {
            const element = document.querySelector('.container');
            const filename = 'Digital_Tarai_Offer_Letter_' + new Date().getTime() + '.pdf';
            
            const opt = {
                margin: 0.5,
                filename: filename,
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2 },
                jsPDF: { orientation: 'portrait', unit: 'in', format: 'letter' }
            };
            
            html2pdf().set(opt).from(element).save();
        }
        </script>
        </html>
        ";
        
        // Save offer letter
        $letter_dir = '../public/offer-letters/';
        if(!is_dir($letter_dir)) {
            mkdir($letter_dir, 0777, true);
        }
        
        $filename = 'offer_letter_' . $letter_id . '_' . time() . '.html';
        file_put_contents($letter_dir . $filename, $offer_letter_html);
        
        // Update the offer letter record with the actual filename
        $file_path = 'public/offer-letters/' . $filename;
        $conn->query("UPDATE offer_letters SET filename='$filename', file_path='$file_path' WHERE id=$letter_id");
        
        // Clear output buffer and redirect
        ob_end_clean();
        header("Location: " . SITE_URL . "/public/offer-letters/" . $filename);
        exit;
    }
}

// Get all approved applications with offer letter status
$approved_apps = $conn->query("
    SELECT a.id, a.user_id, a.internship_id, u.name, i.title, a.application_date,
           CASE WHEN ol.id IS NOT NULL THEN 'generated' ELSE 'pending' END as letter_status,
           ol.id as letter_id, ol.generated_at, ol.filename
    FROM applications a
    JOIN users u ON a.user_id = u.id
    JOIN internships i ON a.internship_id = i.id
    LEFT JOIN offer_letters ol ON a.id = ol.application_id
    WHERE a.status = 'approved'
    ORDER BY a.application_date DESC
");
?>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-semibold">Total Approved</p>
                <p class="text-3xl font-bold text-gray-900 mt-2"><?php echo $conn->query("SELECT COUNT(*) as count FROM applications WHERE status='approved'")->fetch_assoc()['count']; ?></p>
            </div>
            <div class="bg-blue-100 p-3 rounded-full">
                <i class="fas fa-file-alt text-blue-600 text-2xl"></i>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-semibold">Template Settings</p>
                <p class="text-sm text-gray-500 mt-2">Customize offer letter</p>
            </div>
            <a href="?section=offer-letters&customize=1" class="bg-blue-600 text-white p-3 rounded-full hover:bg-blue-700 transition">
                <i class="fas fa-palette text-xl"></i>
            </a>
        </div>
    </div>
</div>

<div class="bg-white rounded-lg shadow-md p-6">
    <h3 class="text-lg font-bold text-gray-900 mb-4">
        <i class="fas fa-file-contract mr-2 text-blue-600"></i>Approved Applications - Generate Offer Letters
    </h3>
    
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="border-b border-gray-200">
                    <th class="px-4 py-3 font-semibold text-gray-700">Student Name</th>
                    <th class="px-4 py-3 font-semibold text-gray-700">Internship Program</th>
                    <th class="px-4 py-3 font-semibold text-gray-700">Approved Date</th>
                    <th class="px-4 py-3 font-semibold text-gray-700">Status</th>
                    <th class="px-4 py-3 font-semibold text-gray-700">Generated Date</th>
                    <th class="px-4 py-3 font-semibold text-gray-700">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if($approved_apps->num_rows > 0) {
                    while($app = $approved_apps->fetch_assoc()) {
                        $is_generated = $app['letter_status'] === 'generated';
                        $status_color = $is_generated ? 'green' : 'orange';
                        $status_text = $is_generated ? 'Generated' : 'Pending';
                        $generated_date = $is_generated ? date('M d, Y', strtotime($app['generated_at'])) : '-';
                        
                        echo "
                        <tr class='border-b border-gray-100 hover:bg-gray-50'>
                            <td class='px-4 py-3 font-semibold text-gray-900'>" . htmlspecialchars($app['name']) . "</td>
                            <td class='px-4 py-3 text-gray-600'>" . htmlspecialchars($app['title']) . "</td>
                            <td class='px-4 py-3 text-sm text-gray-600'>" . date('M d, Y', strtotime($app['application_date'])) . "</td>
                            <td class='px-4 py-3'>
                                <span class='px-3 py-1 bg-{$status_color}-100 text-{$status_color}-700 rounded-full text-xs font-semibold'>
                                    " . $status_text . "
                                </span>
                            </td>
                            <td class='px-4 py-3 text-sm text-gray-600'>" . $generated_date . "</td>
                            <td class='px-4 py-3 space-x-2'>
                                " . (!$is_generated ? "
                                <a href='?section=offer-letters&generate=" . $app['user_id'] . "&internship=" . $app['internship_id'] . "' class='inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition text-sm'>
                                    <i class='fas fa-file-contract mr-1'></i>Generate
                                </a>
                                " : "
                                <div class='flex gap-2'>
                                    <a href='" . SITE_URL . "/public/offer-letters/" . htmlspecialchars($app['filename']) . "' target='_blank' class='inline-flex items-center px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition text-sm'>
                                        <i class='fas fa-download mr-1'></i>View
                                    </a>
                                    <a href='?section=offer-letters&generate=" . $app['user_id'] . "&internship=" . $app['internship_id'] . "' class='inline-flex items-center px-4 py-2 bg-orange-600 text-white rounded hover:bg-orange-700 transition text-sm'>
                                        <i class='fas fa-redo mr-1'></i>Regenerate
                                    </a>
                                </div>
                                ") . "
                            </td>
                        </tr>
                        ";
                    }
                } else {
                    echo "<tr><td colspan='6' class='px-4 py-3 text-center text-gray-600'>No approved applications</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
