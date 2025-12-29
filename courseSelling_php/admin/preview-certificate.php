<?php
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../lib/functions.php';

// Get current certificate template settings
$current_settings = [];
$result = $conn->query("SELECT settings FROM template_settings WHERE template_type='certificates'");
if($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $current_settings = json_decode($row['settings'], true);
}

$primary_color = $current_settings['primary_color'] ?? '#8e44ad';
$secondary_color = $current_settings['secondary_color'] ?? '#2c3e50';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate Preview - Digital Tarai</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, <?php echo $primary_color; ?> 0%, <?php echo $secondary_color; ?> 100%);
            min-height: 100vh;
            padding: 40px 20px;
        }
        
        .container {
            max-width: 1000px;
            margin: 0 auto;
        }
        
        .header {
            background: white;
            padding: 20px;
            border-radius: 8px 8px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .header h1 {
            color: <?php echo $primary_color; ?>;
            font-size: 24px;
        }
        
        .button-group {
            display: flex;
            gap: 10px;
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-download {
            background: <?php echo $primary_color; ?>;
            color: white;
        }
        
        .btn-download:hover {
            opacity: 0.9;
            transform: translateY(-2px);
        }
        
        .btn-close {
            background: #e74c3c;
            color: white;
        }
        
        .btn-close:hover {
            background: #c0392b;
            transform: translateY(-2px);
        }
        
        .preview-container {
            background: white;
            border-radius: 0 0 8px 8px;
            padding: 40px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .certificate {
            width: 100%;
            max-width: 900px;
            margin: 0 auto;
            padding: 60px;
            background: white;
            border: 15px solid transparent;
            background-image: 
                linear-gradient(white, white),
                linear-gradient(135deg, <?php echo $primary_color; ?> 0%, <?php echo $secondary_color; ?> 50%, <?php echo $primary_color; ?> 100%);
            background-origin: border-box;
            background-clip: padding-box, border-box;
            border-radius: 4px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            font-family: 'Georgia', serif;
            text-align: center;
        }
        
        .cert-header {
            margin-bottom: 30px;
        }
        
        .cert-org-name {
            color: <?php echo $primary_color; ?>;
            font-size: 36px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .cert-title {
            color: <?php echo $secondary_color; ?>;
            font-size: 32px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 40px;
        }
        
        .cert-content {
            margin: 40px 0;
        }
        
        .cert-content p {
            color: #666;
            font-size: 16px;
            margin: 15px 0;
            line-height: 1.6;
        }
        
        .cert-recipient-name {
            color: <?php echo $primary_color; ?>;
            font-size: 28px;
            font-weight: bold;
            margin: 30px 0;
        }
        
        .cert-internship-title {
            color: <?php echo $secondary_color; ?>;
            font-size: 22px;
            font-weight: bold;
            margin: 20px 0;
        }
        
        .cert-signature-block {
            display: flex;
            justify-content: space-around;
            align-items: flex-end;
            margin-top: 60px;
            padding-top: 40px;
        }
        
        .signature-item {
            text-align: center;
            width: 150px;
        }
        
        .signature-line {
            border-top: 2px solid <?php echo $secondary_color; ?>;
            margin-bottom: 8px;
            height: 50px;
        }
        
        .signature-name {
            color: <?php echo $secondary_color; ?>;
            font-weight: bold;
            font-size: 13px;
            margin: 8px 0;
        }
        
        .signature-date {
            color: #999;
            font-size: 11px;
        }
        
        .qr-placeholder {
            text-align: center;
            color: #999;
            font-size: 12px;
        }
        
        .cert-footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            font-size: 12px;
            color: #999;
        }
        
        .cert-footer p {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>
                <i class="fas fa-certificate" style="color: <?php echo $primary_color; ?>"></i>
                Certificate Preview
            </h1>
            <div class="button-group">
                <button class="btn btn-download" onclick="downloadPDF()">
                    <i class="fas fa-download"></i>Download PDF
                </button>
                <button class="btn btn-close" onclick="window.close()">
                    <i class="fas fa-times"></i>Close
                </button>
            </div>
        </div>
        
        <div class="preview-container">
            <div id="certificate-content" class="certificate">
                <div class="cert-header">
                    <div class="cert-org-name">
                        <?php echo htmlspecialchars($current_settings['organization_name'] ?? 'Digital Tarai'); ?>
                    </div>
                </div>
                
                <div class="cert-title">
                    <?php echo htmlspecialchars($current_settings['certificate_title'] ?? 'Certificate of Completion'); ?>
                </div>
                
                <div class="cert-content">
                    <p>This is to certify that</p>
                    <div class="cert-recipient-name">Sample Student Name</div>
                    <p>Has successfully completed the internship program in</p>
                    <div class="cert-internship-title">Full Stack Development Internship</div>
                    <p>With dedication, hard work, and commitment to excellence. This certificate is awarded as recognition of the knowledge and skills acquired during the internship period.</p>
                </div>
                
                <div class="cert-signature-block">
                    <div class="signature-item">
                        <div class="signature-line"></div>
                        <div class="signature-name"><?php echo htmlspecialchars($current_settings['director_name'] ?? 'Director'); ?></div>
                        <div class="signature-date"><?php echo date('d M, Y'); ?></div>
                    </div>
                    
                    <div class="signature-item qr-placeholder">
                        <p style="margin-bottom: 40px;">QR Code</p>
                    </div>
                    
                    <div class="signature-item">
                        <div class="signature-line"></div>
                        <div class="signature-name"><?php echo htmlspecialchars($current_settings['coordinator_name'] ?? 'Coordinator'); ?></div>
                        <div class="signature-date"><?php echo date('d M, Y'); ?></div>
                    </div>
                </div>
                
                <div class="cert-footer">
                    <p><?php echo htmlspecialchars($current_settings['organization_address'] ?? 'Siraha, Nepal'); ?></p>
                    <p><?php echo htmlspecialchars($current_settings['footer_text'] ?? 'Professional Internship Programs'); ?></p>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script>
        function downloadPDF() {
            const element = document.getElementById('certificate-content');
            const filename = 'Certificate_Preview_' + new Date().getTime() + '.pdf';
            
            const opt = {
                margin: 10,
                filename: filename,
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2 },
                jsPDF: { orientation: 'landscape', unit: 'mm', format: 'a4' }
            };
            
            html2pdf().set(opt).from(element).save();
        }
    </script>
</body>
</html>
