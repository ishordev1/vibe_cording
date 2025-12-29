<?php
// Handle customization form submission
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['template_type'])) {
    $template_type = $_POST['template_type'];
    $director_name = htmlspecialchars($_POST['director_name'] ?? '');
    $coordinator_name = htmlspecialchars($_POST['coordinator_name'] ?? '');
    $organization_name = htmlspecialchars($_POST['organization_name'] ?? '');
    $organization_address = htmlspecialchars($_POST['organization_address'] ?? '');
    $certificate_title = htmlspecialchars($_POST['certificate_title'] ?? '');
    $footer_text = htmlspecialchars($_POST['footer_text'] ?? '');
    $primary_color = $_POST['primary_color'] ?? '#8e44ad';
    $secondary_color = $_POST['secondary_color'] ?? '#2c3e50';
    
    // Handle logo upload
    $logo_path = '';
    if(isset($_FILES['logo']) && $_FILES['logo']['error'] == 0) {
        $upload_dir = __DIR__ . '/../../public/uploads/logos/';
        if(!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
        
        $filename = uniqid() . '_' . basename($_FILES['logo']['name']);
        if(move_uploaded_file($_FILES['logo']['tmp_name'], $upload_dir . $filename)) {
            $logo_path = '/public/uploads/logos/' . $filename;
        }
    }
    
    // Handle signature upload
    $signature_path = '';
    if(isset($_FILES['signature']) && $_FILES['signature']['error'] == 0) {
        $upload_dir = __DIR__ . '/../../public/uploads/signatures/';
        if(!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
        
        $filename = uniqid() . '_' . basename($_FILES['signature']['name']);
        if(move_uploaded_file($_FILES['signature']['tmp_name'], $upload_dir . $filename)) {
            $signature_path = '/public/uploads/signatures/' . $filename;
        }
    }
    
    // Save or update customization settings
    $settings_json = json_encode([
        'director_name' => $director_name,
        'coordinator_name' => $coordinator_name,
        'organization_name' => $organization_name,
        'organization_address' => $organization_address,
        'certificate_title' => $certificate_title,
        'footer_text' => $footer_text,
        'primary_color' => $primary_color,
        'secondary_color' => $secondary_color,
        'logo_path' => $logo_path,
        'signature_path' => $signature_path,
        'updated_at' => date('Y-m-d H:i:s'),
        'updated_by' => $_SESSION['user_id']
    ]);
    
    $result = $conn->query("SELECT id FROM template_settings WHERE template_type='$template_type'");
    
    if($result && $result->num_rows > 0) {
        $conn->query("UPDATE template_settings SET settings='$settings_json' WHERE template_type='$template_type'");
        $message = "Template settings updated successfully!";
    } else {
        $conn->query("INSERT INTO template_settings (template_type, settings) VALUES ('$template_type', '$settings_json')");
        $message = "Template settings saved successfully!";
    }
}

// Get current settings
$template_type = $_GET['customize'] == 1 ? ($_GET['section'] ?? 'certificates') : '';
$current_settings = [];

if($template_type) {
    // Check if table exists first
    $table_check = $conn->query("SHOW TABLES LIKE 'template_settings'");
    
    if($table_check && $table_check->num_rows > 0) {
        $result = $conn->query("SELECT settings FROM template_settings WHERE template_type='$template_type'");
        if($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $current_settings = json_decode($row['settings'], true);
        }
    }
}

$is_certificate = $template_type === 'certificates';
$page_title = $is_certificate ? 'Certificate Template' : 'Offer Letter Template';
$primary_color = $current_settings['primary_color'] ?? ($is_certificate ? '#8e44ad' : '#2980b9');
$secondary_color = $current_settings['secondary_color'] ?? '#2c3e50';
?>

<div class="grid grid-cols-1 gap-6">
    <!-- Customization Form -->
    <div>
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">
                <i class="fas fa-palette mr-2" style="color: <?php echo $primary_color; ?>"></i><?php echo $page_title; ?> Customization
            </h2>
            
            <form method="POST" enctype="multipart/form-data" class="space-y-6">
                <input type="hidden" name="template_type" value="<?php echo $template_type; ?>">
                
                <?php if(isset($message)): ?>
                <div class="p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                    <i class="fas fa-check-circle mr-2"></i><?php echo $message; ?>
                </div>
                <?php endif; ?>
                
                <!-- Organization Info -->
                <div class="border-t pt-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Organization Information</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Organization Name</label>
                            <input type="text" name="organization_name" value="<?php echo $current_settings['organization_name'] ?? 'Digital Tarai'; ?>" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-600">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                            <input type="text" name="organization_address" value="<?php echo $current_settings['organization_address'] ?? 'Siraha, Nepal'; ?>" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-600">
                        </div>
                    </div>
                </div>
                
                <!-- Signature Info -->
                <div class="border-t pt-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Signatories</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Director Name</label>
                            <input type="text" name="director_name" value="<?php echo $current_settings['director_name'] ?? ''; ?>" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-600">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Coordinator Name</label>
                            <input type="text" name="coordinator_name" value="<?php echo $current_settings['coordinator_name'] ?? ''; ?>" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-600">
                        </div>
                    </div>
                </div>
                
                <!-- Template Content -->
                <div class="border-t pt-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Template Content</h3>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Certificate/Letter Title</label>
                        <input type="text" name="certificate_title" value="<?php echo $current_settings['certificate_title'] ?? ($is_certificate ? 'Certificate of Completion' : 'Offer Letter'); ?>" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-600">
                    </div>
                    
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Footer Text</label>
                        <textarea name="footer_text" rows="3" 
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-600"><?php echo $current_settings['footer_text'] ?? 'Professional Internship Programs'; ?></textarea>
                    </div>
                </div>
                
                <!-- Colors -->
                <div class="border-t pt-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Color Scheme</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Primary Color</label>
                            <div class="flex gap-2">
                                <input type="color" name="primary_color" value="<?php echo $primary_color; ?>" 
                                       class="w-12 h-10 border border-gray-300 rounded cursor-pointer">
                                <input type="text" value="<?php echo $primary_color; ?>" readonly 
                                       class="flex-1 px-4 py-2 border border-gray-300 rounded-lg bg-gray-50">
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Secondary Color</label>
                            <div class="flex gap-2">
                                <input type="color" name="secondary_color" value="<?php echo $secondary_color; ?>" 
                                       class="w-12 h-10 border border-gray-300 rounded cursor-pointer">
                                <input type="text" value="<?php echo $secondary_color; ?>" readonly 
                                       class="flex-1 px-4 py-2 border border-gray-300 rounded-lg bg-gray-50">
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- File Uploads -->
                <div class="border-t pt-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Logo & Signature</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Organization Logo (Optional)</label>
                            <input type="file" name="logo" accept="image/*" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-600">
                            <?php if(!empty($current_settings['logo_path'])): ?>
                            <p class="text-sm text-green-600 mt-2"><i class="fas fa-check"></i> Logo uploaded</p>
                            <?php endif; ?>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Director Signature (Optional)</label>
                            <input type="file" name="signature" accept="image/*" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-600">
                            <?php if(!empty($current_settings['signature_path'])): ?>
                            <p class="text-sm text-green-600 mt-2"><i class="fas fa-check"></i> Signature uploaded</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Submit Button -->
                <div class="border-t pt-6 flex gap-3">
                    <button type="submit" class="px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition font-semibold flex items-center gap-2">
                        <i class="fas fa-save"></i>Save Settings
                    </button>
                    <button type="button" onclick="openPreview()" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-semibold flex items-center gap-2">
                        <i class="fas fa-eye"></i>Preview in New Window
                    </button>
                    <a href="?section=<?php echo $template_type; ?>" class="px-6 py-3 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition font-semibold flex items-center gap-2">
                        <i class="fas fa-arrow-left"></i>Back
                    </a>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Live Preview Section Below Form -->
    <div>
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-2xl font-semibold text-gray-900 mb-6">
                <i class="fas fa-eye mr-2"></i>Live Preview
            </h3>
            
            <div id="preview-content" class="border-4" style="border-color: <?php echo $primary_color; ?>; padding: 40px; background-color: #f9f9f9; min-height: 600px; border-radius: 8px; font-family: Arial, sans-serif;">
                
                <?php if($is_certificate): ?>
                    <!-- Certificate Preview -->
                    <div style="text-align: center;">
                        <div style="color: <?php echo $primary_color; ?>; font-size: 32px; font-weight: bold; margin-bottom: 10px;">
                            <?php echo $current_settings['organization_name'] ?? 'Digital Tarai'; ?>
                        </div>
                        
                        <div style="color: <?php echo $secondary_color; ?>; font-size: 28px; font-weight: bold; margin-bottom: 30px; text-transform: uppercase; letter-spacing: 2px;">
                            <?php echo $current_settings['certificate_title'] ?? 'Certificate of Completion'; ?>
                        </div>
                        
                        <div style="background-color: white; padding: 40px; margin: 30px auto; max-width: 600px; border-radius: 4px; box-shadow: 0 2px 8px rgba(0,0,0,0.15);">
                            <p style="color: #666; font-size: 16px; margin: 0 0 20px 0;">This is to certify that</p>
                            <p style="color: <?php echo $primary_color; ?>; font-size: 28px; font-weight: bold; margin: 30px 0;">Sample Student Name</p>
                            <p style="color: #666; font-size: 15px; margin: 20px 0;">Has successfully completed the internship program in</p>
                            <p style="color: <?php echo $secondary_color; ?>; font-size: 20px; font-weight: bold; margin: 20px 0;">Full Stack Development Internship</p>
                            <p style="color: #666; font-size: 14px; margin: 20px 0; line-height: 1.6;">With dedication, hard work, and commitment to excellence. This certificate is awarded as recognition of the knowledge and skills acquired.</p>
                        </div>
                        
                        <div style="margin-top: 50px; display: flex; justify-content: space-around; align-items: flex-end; max-width: 600px; margin-left: auto; margin-right: auto;">
                            <div style="text-align: center; width: 150px;">
                                <div style="border-top: 2px solid <?php echo $secondary_color; ?>; margin-bottom: 5px; height: 50px;"></div>
                                <p style="color: <?php echo $secondary_color; ?>; font-weight: bold; font-size: 13px; margin: 5px 0;"><?php echo $current_settings['director_name'] ?? 'Director'; ?></p>
                                <p style="color: #999; font-size: 11px;"><?php echo date('d M, Y'); ?></p>
                            </div>
                            <div style="text-align: center; width: 150px;">
                                <p style="color: #999; font-size: 12px; margin-bottom: 40px;">QR Code</p>
                            </div>
                            <div style="text-align: center; width: 150px;">
                                <div style="border-top: 2px solid <?php echo $secondary_color; ?>; margin-bottom: 5px; height: 50px;"></div>
                                <p style="color: <?php echo $secondary_color; ?>; font-weight: bold; font-size: 13px; margin: 5px 0;"><?php echo $current_settings['coordinator_name'] ?? 'Coordinator'; ?></p>
                                <p style="color: #999; font-size: 11px;"><?php echo date('d M, Y'); ?></p>
                            </div>
                        </div>
                        
                        <div style="margin-top: 40px; padding-top: 20px; border-top: 1px solid #ddd; font-size: 12px; color: #999;">
                            <p><?php echo $current_settings['organization_address'] ?? 'Siraha, Nepal'; ?></p>
                            <p><?php echo $current_settings['footer_text'] ?? 'Professional Internship Programs'; ?></p>
                        </div>
                    </div>
                    
                <?php else: ?>
                    <!-- Offer Letter Preview -->
                    <div>
                        <div style="text-align: center; margin-bottom: 30px;">
                            <div style="color: <?php echo $primary_color; ?>; font-size: 28px; font-weight: bold; margin-bottom: 5px;">
                                <?php echo $current_settings['organization_name'] ?? 'Digital Tarai'; ?>
                            </div>
                            <p style="color: #666; font-size: 13px;">
                                <?php echo $current_settings['organization_address'] ?? 'Siraha, Nepal'; ?>
                            </p>
                        </div>
                        
                        <div style="margin-bottom: 20px; padding-bottom: 20px; border-bottom: 2px solid <?php echo $primary_color; ?>;">
                            <h3 style="color: <?php echo $secondary_color; ?>; font-size: 20px; font-weight: bold; margin: 0 0 10px 0;">
                                <?php echo $current_settings['certificate_title'] ?? 'Offer Letter'; ?>
                            </h3>
                            <p style="color: #666; font-size: 13px; margin: 0;">Date: <?php echo date('d M, Y'); ?></p>
                        </div>
                        
                        <div style="margin-bottom: 20px;">
                            <p style="color: #666; font-size: 14px; margin: 0 0 10px 0;"><strong>To:</strong></p>
                            <p style="color: #333; font-size: 14px; margin: 0 0 5px 0;"><strong>Sample Student Name</strong></p>
                            <p style="color: #666; font-size: 13px; margin: 0;">Email: student@example.com</p>
                        </div>
                        
                        <div style="margin-bottom: 20px;">
                            <p style="color: #333; font-size: 14px; line-height: 1.8;">
                                <strong>Dear Sample Student,</strong><br><br>
                                
                                We are pleased to offer you a position as an Intern in our organization. We have reviewed your qualifications and are confident that you will be a valuable addition to our team.<br><br>
                                
                                <strong>Position Details:</strong><br>
                                Position: Full Stack Development Internship<br>
                                Duration: 3 months<br>
                                Start Date: <?php echo date('d M, Y', strtotime('+7 days')); ?><br><br>
                                
                                <strong>Terms and Conditions:</strong><br>
                                This internship is offered on a temporary basis and is subject to satisfactory performance and completion of all required documentation.<br><br>
                                
                                We look forward to working with you. If you have any questions, please feel free to contact us.<br><br>
                                
                                <strong>Best Regards,</strong>
                            </p>
                        </div>
                        
                        <div style="margin-top: 40px;">
                            <div style="text-align: center; margin-bottom: 20px;">
                                <div style="border-top: 2px solid <?php echo $secondary_color; ?>; margin-bottom: 5px; height: 40px;"></div>
                                <p style="color: <?php echo $secondary_color; ?>; font-weight: bold; font-size: 13px; margin: 5px 0;"><?php echo $current_settings['director_name'] ?? 'Director'; ?></p>
                                <p style="color: #999; font-size: 11px;"><?php echo $current_settings['organization_name'] ?? 'Digital Tarai'; ?></p>
                            </div>
                        </div>
                        
                        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd; font-size: 11px; color: #999; text-align: center;">
                            <p><?php echo $current_settings['footer_text'] ?? 'Professional Internship Programs'; ?></p>
                        </div>
                    </div>
                    
                <?php endif; ?>
                
            </div>
            
            <!-- Download Preview Button -->
            <div style="margin-top: 20px; text-align: center;">
                <button onclick="downloadPreview()" class="px-8 py-4 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-semibold flex items-center justify-center gap-2" style="margin: 0 auto;">
                    <i class="fas fa-download"></i>Download Preview as PDF
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
function openPreview() {
    const templateType = '<?php echo $template_type; ?>';
    let previewUrl = '';
    
    if(templateType === 'certificates') {
        previewUrl = '../preview-certificate.php';
    } else if(templateType === 'offer_letter') {
        previewUrl = '../preview-offer-letter.php';
    }
    
    if(previewUrl) {
        const win = window.open(previewUrl, 'preview', 'width=1000,height=800,scrollbars=yes,resizable=yes');
        if (!win) {
            alert('Please disable your pop-up blocker for this site');
        }
    } else {
        alert('Unable to load preview');
    }
}

function downloadPreview() {
    const element = document.getElementById('preview-content');
    const filename = '<?php echo $is_certificate ? 'Certificate_Preview' : 'OfferLetter_Preview'; ?>_' + new Date().getTime() + '.pdf';
    
    const opt = {
        margin: 10,
        filename: filename,
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2 },
        jsPDF: { orientation: '<?php echo $is_certificate ? 'landscape' : 'portrait'; ?>', unit: 'mm', format: 'a4' }
    };
    
    html2pdf().set(opt).from(element).save();
}
</script>
</script>
</script>
</script>
