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
    <title>Aim & Scope | IJSRET</title>
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
        .scope-card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 3px 15px rgba(0,0,0,0.08);
            margin-bottom: 20px;
            border-left: 4px solid #2c5aa0;
            transition: all 0.3s ease;
        }
        .scope-card:hover {
            transform: translateX(10px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.12);
        }
        .scope-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #2c5aa0 0%, #1e3a6f 100%);
            color: white;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
<?php include 'includes/public_navbar.php'; ?>

<div class="page-header">
    <div class="container">
        <h1 class="display-4 fw-bold mb-3">Aim & Scope</h1>
        <p class="lead">Our focus areas and research domains</p>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php" class="text-white">Home</a></li>
                <li class="breadcrumb-item active text-white-50">Aim & Scope</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container mb-5">
    <div class="row mb-5">
        <div class="col-lg-10 mx-auto">
            <div class="card border-0 shadow">
                <div class="card-body p-5">
                    <h3 class="mb-4"><i class="fas fa-bullseye me-2 text-primary"></i>Aim</h3>
                    <p class="lead">IJSRET aims to publish original, high-quality research articles and review papers that contribute significantly to the advancement of engineering and technology.</p>
                    
                    <p>The journal seeks to:</p>
                    <ul>
                        <li>Provide a platform for rapid dissemination of innovative research</li>
                        <li>Bridge the gap between academia and industry</li>
                        <li>Foster interdisciplinary collaboration</li>
                        <li>Promote sustainable and ethical engineering practices</li>
                        <li>Encourage young researchers and scholars</li>
                        <li>Facilitate global knowledge exchange</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    <h2 class="text-center mb-5">Research Areas & Topics</h2>
    
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="scope-card">
                <div class="scope-icon">
                    <i class="fas fa-microchip"></i>
                </div>
                <h5>Computer Science & Engineering</h5>
                <ul class="mb-0">
                    <li>Artificial Intelligence & Machine Learning</li>
                    <li>Data Science & Big Data Analytics</li>
                    <li>Cloud Computing & Distributed Systems</li>
                    <li>Cybersecurity & Cryptography</li>
                    <li>Internet of Things (IoT)</li>
                    <li>Computer Networks & Wireless Communication</li>
                    <li>Software Engineering & Development</li>
                </ul>
            </div>
        </div>
        
        <div class="col-md-6 mb-4">
            <div class="scope-card">
                <div class="scope-icon">
                    <i class="fas fa-bolt"></i>
                </div>
                <h5>Electrical & Electronics Engineering</h5>
                <ul class="mb-0">
                    <li>Power Systems & Renewable Energy</li>
                    <li>Control Systems & Automation</li>
                    <li>Signal Processing</li>
                    <li>VLSI Design & Embedded Systems</li>
                    <li>Telecommunications</li>
                    <li>Robotics & Mechatronics</li>
                    <li>Electronic Circuits & Devices</li>
                </ul>
            </div>
        </div>
        
        <div class="col-md-6 mb-4">
            <div class="scope-card">
                <div class="scope-icon">
                    <i class="fas fa-cogs"></i>
                </div>
                <h5>Mechanical Engineering</h5>
                <ul class="mb-0">
                    <li>Thermal Engineering & Thermodynamics</li>
                    <li>Manufacturing & Production</li>
                    <li>CAD/CAM & Automation</li>
                    <li>Fluid Mechanics & Aerodynamics</li>
                    <li>Materials Science & Engineering</li>
                    <li>Renewable Energy Systems</li>
                    <li>Automotive Engineering</li>
                </ul>
            </div>
        </div>
        
        <div class="col-md-6 mb-4">
            <div class="scope-card">
                <div class="scope-icon">
                    <i class="fas fa-building"></i>
                </div>
                <h5>Civil Engineering</h5>
                <ul class="mb-0">
                    <li>Structural Engineering</li>
                    <li>Geotechnical Engineering</li>
                    <li>Transportation Engineering</li>
                    <li>Environmental Engineering</li>
                    <li>Water Resources Engineering</li>
                    <li>Construction Management</li>
                    <li>Earthquake Engineering</li>
                </ul>
            </div>
        </div>
        
        <div class="col-md-6 mb-4">
            <div class="scope-card">
                <div class="scope-icon">
                    <i class="fas fa-flask"></i>
                </div>
                <h5>Chemical Engineering</h5>
                <ul class="mb-0">
                    <li>Process Engineering</li>
                    <li>Biochemical Engineering</li>
                    <li>Polymer Engineering</li>
                    <li>Nanotechnology</li>
                    <li>Environmental Technology</li>
                    <li>Catalysis & Reaction Engineering</li>
                    <li>Petroleum & Petrochemical Engineering</li>
                </ul>
            </div>
        </div>
        
        <div class="col-md-6 mb-4">
            <div class="scope-card">
                <div class="scope-icon">
                    <i class="fas fa-satellite"></i>
                </div>
                <h5>Electronics & Communication</h5>
                <ul class="mb-0">
                    <li>Wireless Communication</li>
                    <li>Optical Communication</li>
                    <li>Antenna Design</li>
                    <li>Microwave Engineering</li>
                    <li>Digital Signal Processing</li>
                    <li>Mobile Computing</li>
                    <li>Satellite Communication</li>
                </ul>
            </div>
        </div>
        
        <div class="col-md-6 mb-4">
            <div class="scope-card">
                <div class="scope-icon">
                    <i class="fas fa-industry"></i>
                </div>
                <h5>Industrial Engineering</h5>
                <ul class="mb-0">
                    <li>Operations Research</li>
                    <li>Supply Chain Management</li>
                    <li>Quality Control & Six Sigma</li>
                    <li>Ergonomics & Human Factors</li>
                    <li>Production Planning & Control</li>
                    <li>Lean Manufacturing</li>
                    <li>Industrial Automation</li>
                </ul>
            </div>
        </div>
        
        <div class="col-md-6 mb-4">
            <div class="scope-card">
                <div class="scope-icon">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <h5>Emerging Technologies</h5>
                <ul class="mb-0">
                    <li>Blockchain Technology</li>
                    <li>Quantum Computing</li>
                    <li>Augmented & Virtual Reality</li>
                    <li>Edge Computing</li>
                    <li>5G & 6G Technologies</li>
                    <li>Green Technology</li>
                    <li>Smart Cities & Infrastructure</li>
                </ul>
            </div>
        </div>
    </div>
    
    <div class="row mt-5">
        <div class="col-12">
            <div class="alert alert-info">
                <h5><i class="fas fa-info-circle me-2"></i>Interdisciplinary Research Welcome</h5>
                <p class="mb-0">IJSRET encourages submissions that combine multiple disciplines or present innovative applications across different engineering domains. If your research doesn't fit exactly into one category but aligns with our overall scope, we still welcome your submission.</p>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
