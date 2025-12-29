<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_login();
$u = current_user();
if ($u['role'] !== 'admin') { header('Location: ../login.php'); exit; }

// Check if FPDF and QR libraries exist
$fpdf_path = BASE_PATH . '/vendor/fpdf186/fpdf.php';
$qr_path = BASE_PATH . '/vendor/phpqrcode/qrlib.php';
if (!file_exists($fpdf_path) || !file_exists($qr_path)) {
    die('FPDF or QR library not found. Please install them in /vendor/.');
}

require_once $fpdf_path;
require_once $qr_path;

// Fetch certificate settings
$settings_stmt = $pdo->query("SELECT * FROM certificate_settings ORDER BY id DESC LIMIT 1");
$cert_settings = $settings_stmt->fetch(PDO::FETCH_ASSOC);
if (!$cert_settings) {
    $cert_settings = [];
}

// Default settings if not configured
$defaults = [
    'bg_color' => '#ffffff',
    'border_color' => '#2c5aa0',
    'border_width' => 15,
    'header_text' => 'CERTIFICATE OF PUBLICATION',
    'footer_text' => 'This certificate verifies the publication of the research paper.',
    'signature_name' => 'Chief Editor',
    'signature_title' => 'Research Journal',
    'font_family' => 'Times',
    'accent_color' => '#d4af37',
    'logo_url' => '',
    'signature_url' => '',
];
$cert_settings = array_merge($defaults, $cert_settings);

// Sample data for preview
$sample_title = 'Sample Research Paper Title for Testing Certificate Design';
$sample_author = 'Dr. John Smith';
$sample_email = 'john.smith@university.edu';
$cert_id = 'CERT-PREVIEW-' . strtoupper(bin2hex(random_bytes(4)));

// Generate QR Code
$qr_url = 'http://localhost/research_paper_submission/certificate.php?cert_id=' . $cert_id;
$qr_filename = 'qr-preview-' . time() . '.png';
$qr_path_full = UPLOADS_QRCODES . '/' . $qr_filename;
QRcode::png($qr_url, $qr_path_full, QR_ECLEVEL_L, 4);

// Create PDF certificate with custom settings
$pdf = new FPDF('L', 'mm', 'A4');
$pdf->AddPage();

// Convert hex colors to RGB for FPDF
$border_rgb = sscanf($cert_settings['border_color'], "#%02x%02x%02x");
$accent_rgb = sscanf($cert_settings['accent_color'], "#%02x%02x%02x");
$bg_rgb = sscanf($cert_settings['bg_color'], "#%02x%02x%02x");

// Set background color
$pdf->SetFillColor($bg_rgb[0], $bg_rgb[1], $bg_rgb[2]);
$pdf->Rect(0, 0, 297, 210, 'F');

// Draw border
$border_width = (int)$cert_settings['border_width'];
$pdf->SetLineWidth($border_width / 10);
$pdf->SetDrawColor($border_rgb[0], $border_rgb[1], $border_rgb[2]);
$pdf->Rect($border_width, $border_width, 297 - ($border_width * 2), 210 - ($border_width * 2));

// Inner decorative border
$pdf->SetLineWidth(0.5);
$pdf->Rect($border_width + 3, $border_width + 3, 297 - ($border_width * 2) - 6, 210 - ($border_width * 2) - 6);

// Header text at top
$pdf->SetFont($cert_settings['font_family'], 'B', 28);
$pdf->SetTextColor($accent_rgb[0], $accent_rgb[1], $accent_rgb[2]);
$pdf->SetY(30);
$pdf->Cell(0, 15, $cert_settings['header_text'], 0, 1, 'C');

// Decorative line
$pdf->SetDrawColor($accent_rgb[0], $accent_rgb[1], $accent_rgb[2]);
$pdf->SetLineWidth(0.8);
$line_y = $pdf->GetY() + 5;
$pdf->Line(80, $line_y, 217, $line_y);

// Add logo below header if exists
if (!empty($cert_settings['logo_url'])) {
    $logo_path = BASE_PATH . '/' . $cert_settings['logo_url'];
    if (file_exists($logo_path)) {
        $image_info = getimagesize($logo_path);
        if ($image_info) {
            $max_width = 35;
            $aspect_ratio = $image_info[1] / $image_info[0];
            $logo_width = $max_width;
            $logo_height = $max_width * $aspect_ratio;
            $logo_x = (297 - $logo_width) / 2;
            $logo_y = $pdf->GetY() + 8;
            $pdf->Image($logo_path, $logo_x, $logo_y, $logo_width, $logo_height);
            $pdf->SetY($logo_y + $logo_height + 8);
        }
    } else {
        $pdf->Ln(5);
    }
} else {
    $pdf->Ln(5);
}

// Body text
$pdf->SetFont($cert_settings['font_family'], '', 13);
$pdf->SetTextColor(0, 0, 0);
$pdf->Ln(3);
$pdf->Cell(0, 8, 'This certifies that the following paper has been published:', 0, 1, 'C');

$pdf->SetFont($cert_settings['font_family'], 'B', 15);
$pdf->Ln(3);
$pdf->MultiCell(0, 8, $sample_title, 0, 'C');

$pdf->SetFont($cert_settings['font_family'], '', 11);
$pdf->Ln(3);
$pdf->Cell(0, 6, 'Author: ' . $sample_author, 0, 1, 'C');
$pdf->Cell(0, 6, 'Email: ' . $sample_email, 0, 1, 'C');
$pdf->Cell(0, 6, 'Publication Date: ' . date('F d, Y'), 0, 1, 'C');

// Footer text
$pdf->Ln(4);
$pdf->SetFont($cert_settings['font_family'], 'I', 10);
$pdf->SetTextColor(100, 100, 100);
$pdf->MultiCell(0, 5, $cert_settings['footer_text'], 0, 'C');

// Add spacing before signature and certificate ID
$pdf->Ln(8);

// Add QR Code image
if (file_exists($qr_path_full)) {
    $pdf->Image($qr_path_full, 235, 155, 35, 35);
}

// Signature section on left side
if (!empty($cert_settings['signature_url'])) {
    $signature_path = BASE_PATH . '/' . $cert_settings['signature_url'];
    if (file_exists($signature_path)) {
        // Add digital signature image on left
        $pdf->Image($signature_path, 50, 155, 50, 15);
        $pdf->SetY(172);
    }
}
$pdf->Line(50, 171, 100, 171);
$pdf->SetFont($cert_settings['font_family'], 'B', 12);
$pdf->SetXY(60, 172);
$pdf->Cell(55, 5, $cert_settings['signature_name'], 5, 1, 'L');
$pdf->SetFont($cert_settings['font_family'], '', 10);
$pdf->SetX(60);
$pdf->Cell(55, 5, $cert_settings['signature_title'], 0, 0, 'L');

// Certificate ID at bottom left without extra margin
$pdf->SetFont($cert_settings['font_family'], '', 9);
$pdf->SetTextColor(0, 0, 0);
$pdf->SetXY(25, 184);
$pdf->Cell(0, 5, 'Certificate ID: ' . $cert_id . ' (PREVIEW)', 0, 0, 'L');

// Output PDF
$pdf->Output('I', 'certificate_preview.pdf');

// Clean up preview QR code
if (file_exists($qr_path_full)) {
    unlink($qr_path_full);
}
?>
