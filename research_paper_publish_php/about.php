<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>About IJSRET | International Journal of Scientific Research in Engineering and Technology</title>
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
        .about-section {
            padding: 40px 0;
        }
        .info-card {
            background: #f8f9fa;
            border-left: 4px solid #2c5aa0;
            padding: 25px;
            margin-bottom: 25px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .info-card:hover {
            transform: translateX(10px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        .info-card h4 {
            color: #2c5aa0;
            margin-bottom: 15px;
        }
        .feature-box {
            text-align: center;
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 3px 15px rgba(0,0,0,0.08);
            margin-bottom: 30px;
            transition: all 0.3s ease;
        }
        .feature-box:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }
        .feature-box i {
            font-size: 3rem;
            color: #2c5aa0;
            margin-bottom: 20px;
        }
        .stats-section {
            background: linear-gradient(135deg, #2c5aa0 0%, #1e3a6f 100%);
            color: white;
            padding: 60px 0;
            margin: 50px 0;
        }
        .stat-box {
            text-align: center;
            padding: 20px;
        }
        .stat-box h2 {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 10px;
        }
        .stat-box p {
            font-size: 1.1rem;
            opacity: 0.9;
        }
    </style>
</head>
<body>
<?php include 'includes/public_navbar.php'; ?>

<div class="page-header">
    <div class="container">
        <h1 class="display-4 fw-bold mb-3">About IJSRET</h1>
        <p class="lead">International Journal of Scientific Research in Engineering and Technology</p>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php" class="text-white">Home</a></li>
                <li class="breadcrumb-item active text-white-50">About IJSRET</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container about-section">
    <div class="row mb-5">
        <div class="col-lg-8">
            <h2 class="mb-4">Welcome to IJSRET</h2>
            <p class="lead">The International Journal of Scientific Research in Engineering and Technology (IJSRET) is a peer-reviewed, open-access international journal that publishes high-quality research articles in all areas of engineering and technology.</p>
            
            <p>IJSRET provides an international forum for the dissemination of original research, innovative concepts, and practical experiences in engineering and technology. Our mission is to foster the exchange of ideas and promote scientific progress across various engineering disciplines.</p>
            
            <div class="info-card">
                <h4><i class="fas fa-bullseye me-2"></i>Our Mission</h4>
                <p>To provide a platform for researchers, academicians, and industry professionals to publish their original research work and contribute to the advancement of engineering and technology. We aim to promote excellence in research and facilitate knowledge sharing across borders.</p>
            </div>
            
            <div class="info-card">
                <h4><i class="fas fa-eye me-2"></i>Our Vision</h4>
                <p>To become a globally recognized journal that sets the standard for quality research publications in engineering and technology, fostering innovation and contributing to sustainable technological development worldwide.</p>
            </div>
            
            <div class="info-card">
                <h4><i class="fas fa-award me-2"></i>Our Values</h4>
                <ul>
                    <li><strong>Quality:</strong> Maintaining the highest standards of peer review and publication ethics</li>
                    <li><strong>Integrity:</strong> Upholding academic honesty and research integrity</li>
                    <li><strong>Innovation:</strong> Encouraging novel ideas and cutting-edge research</li>
                    <li><strong>Accessibility:</strong> Providing open access to research for global readership</li>
                    <li><strong>Diversity:</strong> Welcoming contributions from researchers worldwide</li>
                </ul>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="feature-box">
                <i class="fas fa-globe"></i>
                <h4>Global Reach</h4>
                <p>Published and read in over 100 countries worldwide</p>
            </div>
            
            <div class="feature-box">
                <i class="fas fa-check-circle"></i>
                <h4>Peer Reviewed</h4>
                <p>Rigorous double-blind peer review process</p>
            </div>
            
            <div class="feature-box">
                <i class="fas fa-lock-open"></i>
                <h4>Open Access</h4>
                <p>Free access to all published articles</p>
            </div>
            
            <div class="feature-box">
                <i class="fas fa-database"></i>
                <h4>Indexed</h4>
                <p>Listed in major academic databases</p>
            </div>
        </div>
    </div>
</div>

<div class="stats-section">
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                <div class="stat-box">
                    <h2><i class="fas fa-file-alt"></i> 2.504</h2>
                    <p>Impact Factor</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-box">
                    <h2><i class="fas fa-users"></i> 5000+</h2>
                    <p>Authors Worldwide</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-box">
                    <h2><i class="fas fa-book"></i> 10,000+</h2>
                    <p>Published Papers</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-box">
                    <h2><i class="fas fa-globe-asia"></i> 100+</h2>
                    <p>Countries</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container about-section">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4">Key Features</h2>
        </div>
        <div class="col-md-6 mb-4">
            <div class="info-card">
                <h4><i class="fas fa-rocket me-2"></i>Rapid Publication</h4>
                <p>Fast-track review process with publication within 2-3 weeks of acceptance</p>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="info-card">
                <h4><i class="fas fa-certificate me-2"></i>Digital Certificates</h4>
                <p>All authors receive publication certificates with unique verification codes</p>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="info-card">
                <h4><i class="fas fa-search me-2"></i>Quality Peer Review</h4>
                <p>Double-blind peer review by experts in respective fields</p>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="info-card">
                <h4><i class="fas fa-fingerprint me-2"></i>DOI Assignment</h4>
                <p>All papers receive Digital Object Identifiers for permanent citation</p>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
