<?php
// Start output buffering to handle header redirects
ob_start();

// Check if customize template is requested
if(isset($_GET['customize'])) {
    ob_end_clean();
    include 'customize-template.php';
    return;
}

// Handle certificate generation
if(isset($_GET['generate'])) {
    $user_id = intval($_GET['generate']);
    $internship_id = intval($_GET['internship']);
    
    $user_result = $conn->query("SELECT * FROM users WHERE id=$user_id");
    $internship_result = $conn->query("SELECT * FROM internships WHERE id=$internship_id");
    
    if($user_result->num_rows > 0 && $internship_result->num_rows > 0) {
        $user = $user_result->fetch_assoc();
        $internship = $internship_result->fetch_assoc();
        
        // Get application_id first so we can use certificate ID for QR code
        $app_result = $conn->query("SELECT id FROM applications WHERE user_id=$user_id AND internship_id=$internship_id");
        if($app_result->num_rows == 0) {
            ob_end_clean();
            header("Location: ?section=certificates&error=Application not found");
            exit;
        }
        
        $app = $app_result->fetch_assoc();
        $app_id = $app['id'];
        
        // Delete previous certificate if exists
        $old_cert_result = $conn->query("SELECT file_path FROM certificates WHERE application_id=$app_id");
        if($old_cert_result && $old_cert_result->num_rows > 0) {
            $old_cert = $old_cert_result->fetch_assoc();
            // Delete file from server
            if(file_exists($old_cert['file_path'])) {
                unlink($old_cert['file_path']);
            }
            // Delete database record
            $conn->query("DELETE FROM certificates WHERE application_id=$app_id");
        }
        
        // First, insert a placeholder record to get the certificate ID
        $admin_id = $_SESSION['user_id'];
        $generated_at = date('Y-m-d H:i:s');
        $conn->query("INSERT INTO certificates (application_id, user_id, internship_id, filename, file_path, generated_by, generated_at) 
                     VALUES ($app_id, $user_id, $internship_id, 'temp', 'temp', $admin_id, '$generated_at')");
        $cert_id = $conn->insert_id;
        
        // Now create the QR code URL with the certificate ID
        $qr_url = SITE_URL . "/pages/verify-certificate.php?cert=" . $cert_id;
        
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
        $settings_result = $conn->query("SELECT settings FROM template_settings WHERE template_type='certificates'");
        if($settings_result && $settings_result->num_rows > 0) {
            $settings_row = $settings_result->fetch_assoc();
            $template_settings = json_decode($settings_row['settings'], true);
        }
        
        // Set defaults from template or use hardcoded defaults
        $director_name = $template_settings['director_name'] ?? 'Director';
        $coordinator_name = $template_settings['coordinator_name'] ?? 'Coordinator';
        $org_name = $template_settings['organization_name'] ?? 'Digital Tarai';
        $org_address = $template_settings['organization_address'] ?? 'Siraha, Nepal';
        
        // Create certificate HTML with QR code
        $certificate_html = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css'>
            <style>
                * { margin: 0; padding: 0; }
                body { font-family: 'Georgia', serif; }
                .certificate {
                    width: 11in;
                    height: 8.5in;
                    border: 15px solid transparent;
                    background: linear-gradient(white, white) padding-box,
                                linear-gradient(135deg, #4a6fa5 0%, #2c3e50 50%, #4a6fa5 100%) border-box;
                    padding: 40px;
                    box-sizing: border-box;
                    position: relative;
                    text-align: center;
                    page-break-inside: avoid;
                }
                .certificate::before {
                    content: '';
                    position: absolute;
                    top: 25px;
                    left: 25px;
                    right: 25px;
                    bottom: 25px;
                    border: 2px dashed #4a6fa5;
                    pointer-events: none;
                }
                .content {
                    position: relative;
                    z-index: 1;
                }
                .header {
                    margin-bottom: 15px;
                }
                .tagline {
                    font-size: 12px;
                    color: #4a6fa5;
                    font-weight: bold;
                    letter-spacing: 1px;
                    margin-bottom: 15px;
                }
                .title {
                    font-size: 52px;
                    font-weight: bold;
                    color: #4a6fa5;
                    margin: 20px 0;
                    text-transform: uppercase;
                    letter-spacing: 3px;
                    word-spacing: 0.2em;
                }
                .description {
                    font-size: 13px;
                    color: #2c3e50;
                    margin: 15px 0;
                    line-height: 1.6;
                }
                .student-name {
                    font-size: 28px;
                    font-weight: bold;
                    color: #4a6fa5;
                    margin: 25px 0 10px 0;
                    text-transform: uppercase;
                    letter-spacing: 1px;
                }
                .student-detail-line {
                    border-bottom: 2px dotted #4a6fa5;
                    margin: 0 0 20px 0;
                    padding-bottom: 5px;
                    height: 5px;
                }
                .body {
                    margin: 15px 0;
                    line-height: 1.8;
                }
                .body p {
                    font-size: 13px;
                    margin-bottom: 8px;
                    color: #2c3e50;
                }
                .internship-name {
                    font-size: 16px;
                    color: #4a6fa5;
                    font-weight: bold;
                    margin: 10px 0;
                }
                .skills-section {
                    font-size: 12px;
                    color: #2c3e50;
                    margin: 15px 0;
                    line-height: 1.6;
                }
                .footer {
                    margin-top: 25px;
                    display: flex;
                    justify-content: space-between;
                    align-items: flex-end;
                    gap: 20px;
                }
                .signature-block {
                    flex: 1;
                    text-align: center;
                }
                .signature-line {
                    border-top: 1px solid #2c3e50;
                    margin: 30px 0 5px 0;
                    height: 1px;
                }
                .signature-name {
                    font-size: 12px;
                    color: #2c3e50;
                    font-weight: bold;
                    margin-top: 5px;
                }
                .signature-title {
                    font-size: 10px;
                    color: #666;
                    margin-top: 2px;
                }
                .qr-section {
                    flex: 1;
                    text-align: center;
                }
                .qr-section img {
                    width: 80px;
                    height: 80px;
                    border: 1px solid #4a6fa5;
                    padding: 3px;
                }
                .reference-number {
                    flex: 1;
                    text-align: center;
                    font-size: 11px;
                }
                .reference-number p {
                    margin: 0;
                    color: #2c3e50;
                }
                .reference-label {
                    font-weight: bold;
                    margin-bottom: 20px;
                }
                .reference-value {
                    margin-top: 5px;
                }
                @media print {
                    body { margin: 0; padding: 0; }
                    .controls { display: none; }
                    .certificate { page-break-inside: avoid; }
                }
                @media print {
                    body { margin: 0; padding: 0; }
                    .controls { display: none; }
                    .certificate { page-break-inside: avoid; }
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
            </style>
        </head>
        <body>
            <div class='controls'>
                <button class='print-btn' onclick='window.print()'>
                    <i class='fas fa-print'></i> Print
                </button>
                <button class='download-btn' onclick='downloadCertificate()'>
                    <i class='fas fa-download'></i> Download PDF
                </button>
            </div>
            
            <div class='certificate'>
                <div class='content'>
                    <div class='header'>
                        <div class='tagline'>🚀 Digital Tarai</div>
                    </div>
                    
                    <div class='title'>Certificate of Internship</div>
                    
                    <p class='description'>This is to certify that</p>
                    
                    <div class='student-name'>" . htmlspecialchars($user['name']) . "</div>
                    <div class='student-detail-line'></div>
                    
                    <p class='body'>
                        <span>student of</span> 
                        <strong>" . htmlspecialchars($internship['title']) . "</strong>
                        <span> has successfully completed an internship in the field of</span> 
                        <strong>" . htmlspecialchars($internship['title']) . "</strong>
                        <span>. During the period of her/his internship program with us, she/he has been exposed to </span>
                        <strong>[Enter skills]</strong>.
                    </p>
                    
                    <div class='footer'>
                        <div class='signature-block'>
                            <div class='signature-line'></div>
                            <p class='signature-name'>" . htmlspecialchars($director_name) . "</p>
                            <p class='signature-title'>Authorized organ</p>
                        </div>
                        
                        <div class='qr-section'>
                            <img src='" . $qr_code_base64 . "' alt='Certificate Verification QR Code'>
                        </div>
                        
                        <div class='reference-number'>
                            <p class='reference-label'>Reference Number</p>
                            <p class='reference-value'>" . str_pad($cert_id, 8, '0', STR_PAD_LEFT) . "</p>
                            <p style='margin-top: 20px; font-size: 10px;'>Issuance Date</p>
                            <p style='font-size: 10px; margin-top: 5px;'>" . date('d M, Y') . "</p>
                        </div>
                    </div>
                </div>
            </div>
        </body>
        <script src='https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js'></script>
        <script>
        function downloadCertificate() {
            const element = document.querySelector('.certificate');
            const filename = 'Digital_Tarai_Certificate_' + new Date().getTime() + '.pdf';
            
            const opt = {
                margin: 0.5,
                filename: filename,
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2 },
                jsPDF: { orientation: 'landscape', unit: 'in', format: [11, 8.5] }
            };
            
            html2pdf().set(opt).from(element).save();
        }
        </script>
        </html>
        ";
        
        // Save certificate
        $cert_dir = '../public/certificates/';
        if(!is_dir($cert_dir)) {
            mkdir($cert_dir, 0777, true);
        }
        
        $filename = 'certificate_' . $cert_id . '_' . time() . '.html';
        file_put_contents($cert_dir . $filename, $certificate_html);
        
        // Update the certificate record with the actual filename and generated_by timestamp
        $file_path = 'public/certificates/' . $filename;
        $generated_at = date('Y-m-d H:i:s');
        $conn->query("UPDATE certificates SET filename='$filename', file_path='$file_path', generated_by={$_SESSION['user_id']}, generated_at='$generated_at' WHERE id=$cert_id");
        
        // Clear output buffer and redirect back to certificates section
        ob_end_clean();
        header("Location: ?section=certificates&success=Certificate generated successfully");
        exit;
    }
}

// Handle sorting
$sort_by = isset($_GET['sort']) ? $_GET['sort'] : 'requested';
$sort_order = isset($_GET['order']) ? ($_GET['order'] === 'asc' ? 'ASC' : 'DESC') : 'DESC';

// Map sort options to SQL ORDER BY clauses
$sort_map = [
    'name' => 'u.name ' . $sort_order,
    'program' => 'i.title ' . $sort_order,
    'approved_date' => 'a.application_date ' . $sort_order,
    'requested' => 'c.requested_at ' . $sort_order . ', a.application_date DESC',
    'status' => 'CASE WHEN c.filename IS NOT NULL AND c.filename != "pending" THEN 3 WHEN c.id IS NOT NULL THEN 2 ELSE 1 END ' . $sort_order,
    'generated_date' => 'c.generated_at ' . $sort_order . ', a.application_date DESC'
];

$order_clause = isset($sort_map[$sort_by]) ? $sort_map[$sort_by] : $sort_map['requested'];

// Get all approved applications with certificate status
$approved_apps = $conn->query("
    SELECT a.id, a.user_id, a.internship_id, u.name, i.title, a.application_date,
           CASE 
               WHEN c.filename IS NOT NULL AND c.filename != 'pending' THEN 'generated'
               WHEN c.id IS NOT NULL THEN 'requested'
               ELSE 'not_requested'
           END as cert_status,
           c.id as cert_id, c.generated_at, c.filename, c.requested_at, c.file_path
    FROM applications a
    JOIN users u ON a.user_id = u.id
    JOIN internships i ON a.internship_id = i.id
    LEFT JOIN certificates c ON a.id = c.application_id
    WHERE a.status = 'approved'
    ORDER BY " . $order_clause
);
?>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-semibold">Total Approved</p>
                <p class="text-3xl font-bold text-gray-900 mt-2"><?php echo $conn->query("SELECT COUNT(*) as count FROM applications WHERE status='approved'")->fetch_assoc()['count']; ?></p>
            </div>
            <div class="bg-green-100 p-3 rounded-full">
                <i class="fas fa-check text-green-600 text-2xl"></i>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-semibold">Template Settings</p>
                <p class="text-sm text-gray-500 mt-2">Customize certificate</p>
            </div>
            <a href="?section=certificates&customize=1" class="bg-purple-600 text-white p-3 rounded-full hover:bg-purple-700 transition">
                <i class="fas fa-palette text-xl"></i>
            </a>
        </div>
    </div>
</div>

<div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-bold text-gray-900">
            <i class="fas fa-certificate mr-2 text-purple-600"></i>Approved Applications - Generate Certificates
        </h3>
        <div class="flex items-center gap-2">
            <label class="text-sm font-medium text-gray-600">Sort by:</label>
            <select id="sortBy" class="px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white hover:border-gray-400 transition" onchange="handleSort()">
                <option value="requested" <?php echo $sort_by === 'requested' ? 'selected' : ''; ?>>Requested Date</option>
                <option value="status" <?php echo $sort_by === 'status' ? 'selected' : ''; ?>>Status</option>
                <option value="generated_date" <?php echo $sort_by === 'generated_date' ? 'selected' : ''; ?>>Generated Date</option>
                <option value="name" <?php echo $sort_by === 'name' ? 'selected' : ''; ?>>Student Name</option>
                <option value="program" <?php echo $sort_by === 'program' ? 'selected' : ''; ?>>Internship Program</option>
                <option value="approved_date" <?php echo $sort_by === 'approved_date' ? 'selected' : ''; ?>>Approved Date</option>
            </select>
            <button id="sortOrder" class="px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white hover:border-gray-400 transition" onclick="toggleSortOrder()" title="Click to toggle sort order">
                <i class="fas fa-arrow-down-a-z"></i>
            </button>
        </div>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="border-b border-gray-200">
                    <th class="px-4 py-3 font-semibold text-gray-700 cursor-pointer hover:bg-gray-50" onclick="setSortAndReload('name')">Student Name <i class="fas fa-sort text-xs text-gray-400"></i></th>
                    <th class="px-4 py-3 font-semibold text-gray-700 cursor-pointer hover:bg-gray-50" onclick="setSortAndReload('program')">Internship Program <i class="fas fa-sort text-xs text-gray-400"></i></th>
                    <th class="px-4 py-3 font-semibold text-gray-700 cursor-pointer hover:bg-gray-50" onclick="setSortAndReload('approved_date')">Approved Date <i class="fas fa-sort text-xs text-gray-400"></i></th>
                    <th class="px-4 py-3 font-semibold text-gray-700 cursor-pointer hover:bg-gray-50" onclick="setSortAndReload('requested')">Requested <i class="fas fa-sort text-xs text-gray-400"></i></th>
                    <th class="px-4 py-3 font-semibold text-gray-700 cursor-pointer hover:bg-gray-50" onclick="setSortAndReload('status')">Status <i class="fas fa-sort text-xs text-gray-400"></i></th>
                    <th class="px-4 py-3 font-semibold text-gray-700 cursor-pointer hover:bg-gray-50" onclick="setSortAndReload('generated_date')">Generated Date <i class="fas fa-sort text-xs text-gray-400"></i></th>
                    <th class="px-4 py-3 font-semibold text-gray-700">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if($approved_apps->num_rows > 0) {
                    while($app = $approved_apps->fetch_assoc()) {
                        // Determine status, color, and action buttons based on cert_status
                        $status_color = 'gray';
                        $status_text = 'Not Requested';
                        $generated_date = '-';
                        $action_buttons = '';
                        
                        if($app['cert_status'] === 'not_requested') {
                            $status_color = 'gray';
                            $status_text = 'Not Requested';
                            $action_buttons = "
                            <a href='?section=certificates&generate=" . $app['user_id'] . "&internship=" . $app['internship_id'] . "' class='inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700 transition text-sm'>
                                <i class='fas fa-certificate mr-1'></i>Generate
                            </a>";
                        } elseif($app['cert_status'] === 'requested') {
                            $status_color = 'orange';
                            $status_text = 'Requested';
                            $action_buttons = "
                            <a href='?section=certificates&generate=" . $app['user_id'] . "&internship=" . $app['internship_id'] . "' class='inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700 transition text-sm'>
                                <i class='fas fa-certificate mr-1'></i>Generate
                            </a>";
                        } elseif($app['cert_status'] === 'generated') {
                            $status_color = 'green';
                            $status_text = 'Generated';
                            $generated_date = date('M d, Y', strtotime($app['generated_at']));
                            $action_buttons = "
                            <div class='flex gap-2'>
                                <a href='" . SITE_URL . "/" . htmlspecialchars($app['file_path']) . "' target='_blank' class='inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition text-sm'>
                                    <i class='fas fa-download mr-1'></i>View
                                </a>
                                <a href='?section=certificates&generate=" . $app['user_id'] . "&internship=" . $app['internship_id'] . "' class='inline-flex items-center px-4 py-2 bg-orange-600 text-white rounded hover:bg-orange-700 transition text-sm'>
                                    <i class='fas fa-redo mr-1'></i>Regenerate
                                </a>
                            </div>";
                        }
                        
                        $requested_date = $app['requested_at'] ? date('M d, Y', strtotime($app['requested_at'])) : '-';
                        
                        echo "
                        <tr class='border-b border-gray-100 hover:bg-gray-50'>
                            <td class='px-4 py-3 font-semibold text-gray-900'>" . htmlspecialchars($app['name']) . "</td>
                            <td class='px-4 py-3 text-gray-600'>" . htmlspecialchars($app['title']) . "</td>
                            <td class='px-4 py-3 text-sm text-gray-600'>" . date('M d, Y', strtotime($app['application_date'])) . "</td>
                            <td class='px-4 py-3 text-sm text-gray-600'>" . $requested_date . "</td>
                            <td class='px-4 py-3'>
                                <span class='px-3 py-1 bg-{$status_color}-100 text-{$status_color}-700 rounded-full text-xs font-semibold'>
                                    " . $status_text . "
                                </span>
                            </td>
                            <td class='px-4 py-3 text-sm text-gray-600'>" . $generated_date . "</td>
                            <td class='px-4 py-3 space-x-2'>
                                " . $action_buttons . "
                            </td>
                        </tr>
                        ";
                    }
                } else {
                    echo "<tr><td colspan='7' class='px-4 py-3 text-center text-gray-600'>No approved applications</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function setSortAndReload(sortBy) {
    const currentOrder = document.getElementById('sortOrder').getAttribute('data-order') || 'desc';
    const newOrder = currentOrder === 'desc' ? 'asc' : 'desc';
    window.location.href = '?section=certificates&sort=' + sortBy + '&order=' + newOrder;
}

function handleSort() {
    const sortBy = document.getElementById('sortBy').value;
    const currentOrder = document.getElementById('sortOrder').getAttribute('data-order') || 'desc';
    window.location.href = '?section=certificates&sort=' + sortBy + '&order=' + currentOrder;
}

function toggleSortOrder() {
    const currentOrder = document.getElementById('sortOrder').getAttribute('data-order') || 'desc';
    const newOrder = currentOrder === 'desc' ? 'asc' : 'desc';
    const sortBy = document.getElementById('sortBy').value;
    window.location.href = '?section=certificates&sort=' + sortBy + '&order=' + newOrder;
}

// Set the current sort order on page load
document.addEventListener('DOMContentLoaded', function() {
    const currentOrder = '<?php echo $sort_order; ?>';
    const sortButton = document.getElementById('sortOrder');
    sortButton.setAttribute('data-order', currentOrder.toLowerCase());
    sortButton.innerHTML = currentOrder === 'DESC' ? '<i class="fas fa-arrow-down-a-z"></i>' : '<i class="fas fa-arrow-up-a-z"></i>';
    sortButton.title = 'Current: ' + currentOrder + ' - Click to toggle';
});
</script>

