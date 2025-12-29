<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $subject = $_POST['subject'] ?? '';
    $msg = $_POST['message'] ?? '';
    
    if ($name && $email && $subject && $msg) {
        // In a real implementation, send email here
        $message = '<div class="alert alert-success"><i class="fas fa-check-circle me-2"></i>Thank you for contacting us! We will get back to you soon.</div>';
    } else {
        $message = '<div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i>Please fill in all required fields.</div>';
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Contact Us | IJSRET</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .page-header {
            background: linear-gradient(135deg, #2c5aa0 0%, #1e3a6f 100%);
            color: white;
            padding: 80px 0 60px;
            margin-bottom: 50px;
        }
        .contact-card {
            background: white;
            border-radius: 10px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 3px 15px rgba(0,0,0,0.08);
            text-align: center;
            transition: all 0.3s ease;
        }
        .contact-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }
        .contact-icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #2c5aa0 0%, #1e3a6f 100%);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin: 0 auto 20px;
        }
        .form-control:focus {
            border-color: #2c5aa0;
            box-shadow: 0 0 0 0.2rem rgba(44, 90, 160, 0.25);
        }
    </style>
</head>
<body>
<?php include 'includes/public_navbar.php'; ?>

<div class="page-header">
    <div class="container">
        <h1 class="display-4 fw-bold mb-3">Contact Us</h1>
        <p class="lead">Get in touch with our team</p>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php" class="text-white">Home</a></li>
                <li class="breadcrumb-item active text-white-50">Contact Us</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container mb-5">
    <div class="row mb-5">
        <div class="col-md-4">
            <div class="contact-card">
                <div class="contact-icon">
                    <i class="fas fa-envelope"></i>
                </div>
                <h5>Email Us</h5>
                <p class="text-muted">Send us your queries</p>
                <a href="mailto:editor@ijsret.com" class="text-primary fw-bold">editor@ijsret.com</a>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="contact-card">
                <div class="contact-icon">
                    <i class="fas fa-phone"></i>
                </div>
                <h5>Call Us</h5>
                <p class="text-muted">Mon-Fri, 9AM-6PM</p>
                <a href="tel:+911234567890" class="text-primary fw-bold">+91 123 456 7890</a>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="contact-card">
                <div class="contact-icon">
                    <i class="fas fa-map-marker-alt"></i>
                </div>
                <h5>Visit Us</h5>
                <p class="text-muted">Our office location</p>
                <p class="fw-bold text-primary">India</p>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card border-0 shadow">
                <div class="card-body p-5">
                    <h3 class="mb-4 text-center">Send Us a Message</h3>
                    <?= $message ?>
                    <form method="POST">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Your Name *</label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Your Email *</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Subject *</label>
                            <input type="text" name="subject" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Message *</label>
                            <textarea name="message" class="form-control" rows="6" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg w-100">
                            <i class="fas fa-paper-plane me-2"></i>Send Message
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-5">
        <div class="col-12">
            <div class="alert alert-info">
                <h5><i class="fas fa-clock me-2"></i>Response Time</h5>
                <p class="mb-0">We typically respond to all inquiries within 24-48 hours during business days. For urgent matters, please call us directly.</p>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
