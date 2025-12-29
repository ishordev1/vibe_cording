<?php
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../lib/functions.php';

// Get current offer letter template settings
$current_settings = [];
$result = $conn->query("SELECT settings FROM template_settings WHERE template_type='offer_letter'");
if($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $current_settings = json_decode($row['settings'], true);
}

$primary_color = $current_settings['primary_color'] ?? '#2980b9';
$secondary_color = $current_settings['secondary_color'] ?? '#2c3e50';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Offer Letter Preview - Digital Tarai</title>
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
            max-width: 900px;
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
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .letter-content {
            max-width: 850px;
            margin: 0 auto;
            padding: 60px 50px;
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
        }
        
        .letter-header {
            text-align: center;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 3px solid <?php echo $primary_color; ?>;
        }
        
        .org-name {
            color: <?php echo $primary_color; ?>;
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .org-address {
            color: #666;
            font-size: 13px;
        }
        
        .letter-title {
            color: <?php echo $secondary_color; ?>;
            font-size: 20px;
            font-weight: bold;
            margin: 20px 0 10px 0;
        }
        
        .letter-date {
            color: #666;
            font-size: 13px;
            margin-bottom: 30px;
        }
        
        .recipient-info {
            margin-bottom: 20px;
        }
        
        .recipient-info strong {
            color: #333;
        }
        
        .recipient-info p {
            margin: 5px 0;
            font-size: 14px;
        }
        
        .letter-body {
            margin: 30px 0;
            font-size: 14px;
            line-height: 1.8;
        }
        
        .letter-body p {
            margin-bottom: 15px;
            text-align: justify;
        }
        
        .position-details {
            background: #f5f5f5;
            padding: 20px;
            border-left: 4px solid <?php echo $primary_color; ?>;
            margin: 20px 0;
            font-size: 14px;
        }
        
        .position-details strong {
            color: <?php echo $secondary_color; ?>;
        }
        
        .signature-section {
            margin-top: 50px;
            padding-top: 40px;
        }
        
        .signature {
            text-align: center;
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
        
        .signature-title {
            color: #999;
            font-size: 11px;
        }
        
        .letter-footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            font-size: 11px;
            color: #999;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>
                <i class="fas fa-file-contract" style="color: <?php echo $primary_color; ?>"></i>
                Offer Letter Preview
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
            <div id="letter-content" class="letter-content">
                <div class="letter-header">
                    <div class="org-name"><?php echo htmlspecialchars($current_settings['organization_name'] ?? 'Digital Tarai'); ?></div>
                    <div class="org-address"><?php echo htmlspecialchars($current_settings['organization_address'] ?? 'Siraha, Nepal'); ?></div>
                </div>
                
                <div class="letter-title"><?php echo htmlspecialchars($current_settings['certificate_title'] ?? 'Offer Letter'); ?></div>
                <div class="letter-date">Date: <?php echo date('d M, Y'); ?></div>
                
                <div class="recipient-info">
                    <p><strong>To:</strong></p>
                    <p><strong>Sample Student Name</strong></p>
                    <p>Email: student@example.com</p>
                    <p>Contact: +977-XXXXXXXXXX</p>
                </div>
                
                <div class="letter-body">
                    <p><strong>Dear Sample Student,</strong></p>
                    
                    <p>We are pleased to offer you a position as an Intern in our organization. We have reviewed your qualifications and are confident that you will be a valuable addition to our team. We believe your skills and enthusiasm will contribute significantly to our projects.</p>
                    
                    <div class="position-details">
                        <p><strong>Position Details:</strong></p>
                        <p><strong>Position:</strong> Full Stack Development Internship</p>
                        <p><strong>Duration:</strong> 3 months</p>
                        <p><strong>Start Date:</strong> <?php echo date('d M, Y', strtotime('+7 days')); ?></p>
                        <p><strong>Location:</strong> Digital Tarai Office, Siraha</p>
                    </div>
                    
                    <p><strong>Key Responsibilities:</strong></p>
                    <p>During your internship, you will work on real-world projects, learn industry best practices, and gain hands-on experience in software development. You will have the opportunity to work with experienced professionals and mentors who will guide your learning journey.</p>
                    
                    <p><strong>Terms and Conditions:</strong></p>
                    <p>This internship is offered on a temporary basis and is subject to satisfactory performance and completion of all required documentation. You are expected to adhere to our organizational policies and maintain professional conduct at all times.</p>
                    
                    <p>We look forward to working with you and seeing the contributions you will make to our organization. If you have any questions or concerns, please feel free to contact us at your earliest convenience.</p>
                    
                    <p><strong>Best Regards,</strong></p>
                </div>
                
                <div class="signature-section">
                    <div class="signature">
                        <div class="signature-line"></div>
                        <div class="signature-name"><?php echo htmlspecialchars($current_settings['director_name'] ?? 'Director'); ?></div>
                        <div class="signature-title"><?php echo htmlspecialchars($current_settings['organization_name'] ?? 'Digital Tarai'); ?></div>
                    </div>
                </div>
                
                <div class="letter-footer">
                    <p><?php echo htmlspecialchars($current_settings['footer_text'] ?? 'Professional Internship Programs'); ?></p>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script>
        function downloadPDF() {
            const element = document.getElementById('letter-content');
            const filename = 'OfferLetter_Preview_' + new Date().getTime() + '.pdf';
            
            const opt = {
                margin: 10,
                filename: filename,
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2 },
                jsPDF: { orientation: 'portrait', unit: 'mm', format: 'a4' }
            };
            
            html2pdf().set(opt).from(element).save();
        }
    </script>
</body>
</html>
