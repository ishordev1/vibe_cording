<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$search = $_GET['q'] ?? '';
$user = current_user();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>IJSRET | International Journal of Scientific Research in Engineering and Technology</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<?php include 'includes/public_navbar.php'; ?>

<!-- Hero Section -->
<section class="hero-section">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-lg-7">
        <h1 class="display-3 fw-bold mb-4">Publish Your Research<br>With Excellence</h1>
        <p class="lead mb-4">Submit, review, and publish high-quality research papers with our streamlined peer-review process. Join the global community of researchers.</p>
        <div class="d-flex gap-3">
          <a href="register.php" class="btn btn-light btn-lg hero-btn"><i class="fas fa-user-plus me-2"></i>Submit Paper</a>
          <a href="papers.php" class="btn btn-outline-light btn-lg hero-btn"><i class="fas fa-search me-2"></i>Browse Papers</a>
        </div>
      </div>
      <div class="col-lg-5 text-center d-none d-lg-block">
        <i class="fas fa-book-reader" style="font-size: 15rem; opacity: 0.2;"></i>
      </div>
    </div>
  </div>
</section>

<!-- Features Section -->
<section class="section-padding bg-light">
  <div class="container">
    <div class="text-center mb-5">
      <h2 class="display-5 fw-bold text-gradient">Why Choose Us?</h2>
      <p class="lead text-secondary">Streamlined submission and review process</p>
    </div>
    <div class="row g-4">
      <div class="col-md-4">
        <div class="feature-card text-center">
          <div class="feature-icon mx-auto"><i class="fas fa-rocket"></i></div>
          <h4 class="fw-bold mb-3">Fast Review Process</h4>
          <p class="text-secondary">Get your research reviewed quickly with our efficient peer-review system</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="feature-card text-center">
          <div class="feature-icon mx-auto"><i class="fas fa-certificate"></i></div>
          <h4 class="fw-bold mb-3">Digital Certificates</h4>
          <p class="text-secondary">Receive professional certificates with QR code verification</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="feature-card text-center">
          <div class="feature-icon mx-auto"><i class="fas fa-shield-alt"></i></div>
          <h4 class="fw-bold mb-3">Secure Platform</h4>
          <p class="text-secondary">Your research is protected with enterprise-grade security</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="feature-card text-center">
          <div class="feature-icon mx-auto"><i class="fas fa-users"></i></div>
          <h4 class="fw-bold mb-3">Expert Reviewers</h4>
          <p class="text-secondary">Reviewed by qualified experts in your field</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="feature-card text-center">
          <div class="feature-icon mx-auto"><i class="fas fa-globe"></i></div>
          <h4 class="fw-bold mb-3">Global Reach</h4>
          <p class="text-secondary">Share your research with worldwide audience</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="feature-card text-center">
          <div class="feature-icon mx-auto"><i class="fas fa-chart-line"></i></div>
          <h4 class="fw-bold mb-3">Track Progress</h4>
          <p class="text-secondary">Monitor your submission status in real-time</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Stats Section -->
<section class="section-padding" style="background: linear-gradient(135deg, #3b5998 0%, #2d4373 100%); color: white;">
  <div class="container">
    <div class="text-center mb-5">
      <h2 class="display-5 fw-bold mb-3">Trusted by Researchers Worldwide</h2>
      <p class="lead opacity-75">Join thousands of authors who trust IJSRET for their publications</p>
    </div>
    <div class="row text-center">
      <div class="col-md-3 mb-4 mb-md-0">
        <div class="p-3">
          <h2 class="display-3 fw-bold mb-2">2.504</h2>
          <p class="lead mb-1">Impact Factor (PIF)</p>
          <small class="opacity-75">Publication Impact Factor</small>
        </div>
      </div>
      <div class="col-md-3 mb-4 mb-md-0">
        <div class="p-3">
          <h2 class="display-3 fw-bold mb-2">10,000+</h2>
          <p class="lead mb-1">Published Papers</p>
          <small class="opacity-75">Quality Research Articles</small>
        </div>
      </div>
      <div class="col-md-3 mb-4 mb-md-0">
        <div class="p-3">
          <h2 class="display-3 fw-bold mb-2">100+</h2>
          <p class="lead mb-1">Countries</p>
          <small class="opacity-75">Global Reach</small>
        </div>
      </div>
      <div class="col-md-3">
        <div class="p-3">
          <h2 class="display-3 fw-bold mb-2">5000+</h2>
          <p class="lead mb-1">Authors</p>
          <small class="opacity-75">Active Researchers</small>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Indexing & Recognition -->
<section class="section-padding bg-light">
  <div class="container">
    <div class="text-center mb-5">
      <h2 class="display-5 fw-bold mb-3">Indexed & Recognized Globally</h2>
      <p class="lead text-secondary">IJSRET is indexed in major academic databases for maximum visibility</p>
    </div>
    <div class="row g-4">
      <div class="col-md-3 col-sm-6">
        <div class="feature-card text-center h-100">
          <div class="mb-3" style="font-size: 3rem; color: #3b5998;">
            <i class="fas fa-fingerprint"></i>
          </div>
          <h5 class="fw-bold">DOI Registration</h5>
          <p class="text-secondary small mb-0">Every article gets unique Digital Object Identifier</p>
        </div>
      </div>
      <div class="col-md-3 col-sm-6">
        <div class="feature-card text-center h-100">
          <div class="mb-3" style="font-size: 3rem; color: #4285f4;">
            <i class="fab fa-google"></i>
          </div>
          <h5 class="fw-bold">Google Scholar</h5>
          <p class="text-secondary small mb-0">Indexed and searchable on Google Scholar</p>
        </div>
      </div>
      <div class="col-md-3 col-sm-6">
        <div class="feature-card text-center h-100">
          <div class="mb-3" style="font-size: 3rem; color: #c4302b;">
            <i class="fas fa-plus"></i>
          </div>
          <h5 class="fw-bold">Crossref</h5>
          <p class="text-secondary small mb-0">Registered with Crossref for citation linking</p>
        </div>
      </div>
      <div class="col-md-3 col-sm-6">
        <div class="feature-card text-center h-100">
          <div class="mb-3" style="font-size: 3rem; color: #2c5aa0;">
            <i class="fas fa-bookmark"></i>
          </div>
          <h5 class="fw-bold">ISSN: 2395-566X</h5>
          <p class="text-secondary small mb-0">Internationally recognized serial number</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Why Trust Us -->
<section class="section-padding">
  <div class="container">
    <div class="row align-items-center mb-5">
      <div class="col-lg-6 mb-4 mb-lg-0">
        <h2 class="display-5 fw-bold mb-4">Why Authors Trust IJSRET</h2>
        <p class="lead mb-4">We're committed to providing a transparent, ethical, and efficient publishing platform</p>
        
        <div class="d-flex mb-4">
          <div class="me-3">
            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
              <i class="fas fa-shield-alt"></i>
            </div>
          </div>
          <div>
            <h5 class="fw-bold mb-2">Rigorous Peer Review</h5>
            <p class="text-secondary mb-0">Double-blind peer review process by qualified experts ensures quality and credibility of every publication.</p>
          </div>
        </div>
        
        <div class="d-flex mb-4">
          <div class="me-3">
            <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
              <i class="fas fa-clock"></i>
            </div>
          </div>
          <div>
            <h5 class="fw-bold mb-2">Fast Publication Timeline</h5>
            <p class="text-secondary mb-0">Get published within 2-3 weeks of acceptance. Quick review process without compromising on quality.</p>
          </div>
        </div>
        
        <div class="d-flex mb-4">
          <div class="me-3">
            <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
              <i class="fas fa-lock-open"></i>
            </div>
          </div>
          <div>
            <h5 class="fw-bold mb-2">Open Access</h5>
            <p class="text-secondary mb-0">Your research reaches a global audience with free, unrestricted access to all published articles.</p>
          </div>
        </div>
        
        <div class="d-flex">
          <div class="me-3">
            <div class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
              <i class="fas fa-award"></i>
            </div>
          </div>
          <div>
            <h5 class="fw-bold mb-2">Professional Recognition</h5>
            <p class="text-secondary mb-0">Digital certificates with QR verification for all authors. Boost your academic profile and career.</p>
          </div>
        </div>
      </div>
      
      <div class="col-lg-6">
        <div class="feature-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
          <h4 class="fw-bold mb-4">Publication Ethics & Standards</h4>
          <ul class="list-unstyled mb-0">
            <li class="mb-3 d-flex align-items-start">
              <i class="fas fa-check-circle me-3 mt-1" style="font-size: 1.2rem;"></i>
              <span><strong>Zero Plagiarism Tolerance:</strong> All manuscripts checked with advanced plagiarism detection software</span>
            </li>
            <li class="mb-3 d-flex align-items-start">
              <i class="fas fa-check-circle me-3 mt-1" style="font-size: 1.2rem;"></i>
              <span><strong>Ethical Guidelines:</strong> Strict adherence to COPE (Committee on Publication Ethics) standards</span>
            </li>
            <li class="mb-3 d-flex align-items-start">
              <i class="fas fa-check-circle me-3 mt-1" style="font-size: 1.2rem;"></i>
              <span><strong>Data Integrity:</strong> Verification of research data and methodology</span>
            </li>
            <li class="mb-3 d-flex align-items-start">
              <i class="fas fa-check-circle me-3 mt-1" style="font-size: 1.2rem;"></i>
              <span><strong>Transparent Process:</strong> Clear communication at every stage of submission</span>
            </li>
            <li class="d-flex align-items-start">
              <i class="fas fa-check-circle me-3 mt-1" style="font-size: 1.2rem;"></i>
              <span><strong>Copyright Protection:</strong> Your intellectual property rights are fully protected</span>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Testimonials -->
<section class="section-padding bg-light">
  <div class="container">
    <div class="text-center mb-5">
      <h2 class="display-5 fw-bold mb-3">What Authors Say About Us</h2>
      <p class="lead text-secondary">Join our community of satisfied researchers</p>
    </div>
    <div class="row g-4">
      <div class="col-md-4">
        <div class="feature-card h-100">
          <div class="mb-3">
            <i class="fas fa-star text-warning"></i>
            <i class="fas fa-star text-warning"></i>
            <i class="fas fa-star text-warning"></i>
            <i class="fas fa-star text-warning"></i>
            <i class="fas fa-star text-warning"></i>
          </div>
          <p class="mb-4 fst-italic">"The peer review process was thorough yet fast. I received valuable feedback that improved my paper significantly. Highly recommend IJSRET!"</p>
          <div class="d-flex align-items-center">
            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
              <i class="fas fa-user"></i>
            </div>
            <div>
              <h6 class="fw-bold mb-0">Dr. Rajesh Kumar</h6>
              <small class="text-secondary">IIT Delhi, India</small>
            </div>
          </div>
        </div>
      </div>
      
      <div class="col-md-4">
        <div class="feature-card h-100">
          <div class="mb-3">
            <i class="fas fa-star text-warning"></i>
            <i class="fas fa-star text-warning"></i>
            <i class="fas fa-star text-warning"></i>
            <i class="fas fa-star text-warning"></i>
            <i class="fas fa-star text-warning"></i>
          </div>
          <p class="mb-4 fst-italic">"Excellent platform with professional service. The submission process was smooth and the dashboard made it easy to track my paper's status."</p>
          <div class="d-flex align-items-center">
            <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
              <i class="fas fa-user"></i>
            </div>
            <div>
              <h6 class="fw-bold mb-0">Prof. Sarah Johnson</h6>
              <small class="text-secondary">MIT, USA</small>
            </div>
          </div>
        </div>
      </div>
      
      <div class="col-md-4">
        <div class="feature-card h-100">
          <div class="mb-3">
            <i class="fas fa-star text-warning"></i>
            <i class="fas fa-star text-warning"></i>
            <i class="fas fa-star text-warning"></i>
            <i class="fas fa-star text-warning"></i>
            <i class="fas fa-star text-warning"></i>
          </div>
          <p class="mb-4 fst-italic">"Published my research in just 3 weeks! The quality of reviewers was excellent and the certificate with QR code is very professional."</p>
          <div class="d-flex align-items-center">
            <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
              <i class="fas fa-user"></i>
            </div>
            <div>
              <h6 class="fw-bold mb-0">Dr. Ahmed Hassan</h6>
              <small class="text-secondary">Cairo University, Egypt</small>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Call to Action -->
<section class="section-padding" style="background: linear-gradient(135deg, #3b5998 0%, #2d4373 100%); color: white;">
  <div class="container text-center">
    <h2 class="display-4 fw-bold mb-4">Ready to Publish Your Research?</h2>
    <p class="lead mb-4 opacity-75">Join thousands of researchers who trust IJSRET for their publications</p>
    <div class="d-flex gap-3 justify-content-center flex-wrap">
      <a href="register.php" class="btn btn-light btn-lg px-5">
        <i class="fas fa-rocket me-2"></i>Submit Your Paper
      </a>
      <a href="about.php" class="btn btn-outline-light btn-lg px-5">
        <i class="fas fa-info-circle me-2"></i>Learn More
      </a>
    </div>
    <div class="mt-5 pt-4 border-top border-white border-opacity-25">
      <div class="row text-center">
        <div class="col-md-3 mb-3 mb-md-0">
          <i class="fas fa-clock fa-2x mb-2 opacity-75"></i>
          <p class="mb-0"><strong>Fast Review</strong><br><small class="opacity-75">2-3 Weeks</small></p>
        </div>
        <div class="col-md-3 mb-3 mb-md-0">
          <i class="fas fa-dollar-sign fa-2x mb-2 opacity-75"></i>
          <p class="mb-0"><strong>Affordable Fees</strong><br><small class="opacity-75">₹2,500 / $50</small></p>
        </div>
        <div class="col-md-3 mb-3 mb-md-0">
          <i class="fas fa-certificate fa-2x mb-2 opacity-75"></i>
          <p class="mb-0"><strong>Digital Certificate</strong><br><small class="opacity-75">With QR Code</small></p>
        </div>
        <div class="col-md-3">
          <i class="fas fa-globe fa-2x mb-2 opacity-75"></i>
          <p class="mb-0"><strong>Global Indexing</strong><br><small class="opacity-75">Maximum Visibility</small></p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- About Section -->
<section class="section-padding" id="about">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-lg-6 mb-4 mb-lg-0">
        <h2 class="display-5 fw-bold mb-4">About Our Journal</h2>
        <p class="lead mb-3">We are dedicated to advancing research and knowledge sharing across all disciplines.</p>
        <p class="text-secondary">Our platform provides researchers with a modern, efficient way to submit and publish their work. With a rigorous peer-review process and commitment to quality, we ensure that only the best research reaches our global audience.</p>
        <ul class="list-unstyled mt-4">
          <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Peer-reviewed publications</li>
          <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Digital certificates with QR verification</li>
          <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Professional dashboard for authors</li>
          <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Real-time submission tracking</li>
        </ul>
      </div>
      <div class="col-lg-6">
        <div class="feature-card">
          <h4 class="fw-bold mb-3">Submission Process</h4>
          <div class="d-flex align-items-start mb-3">
            <div class="badge bg-primary rounded-circle me-3" style="width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;">1</div>
            <div>
              <h6 class="fw-bold">Register & Submit</h6>
              <small class="text-secondary">Create account and submit your paper</small>
            </div>
          </div>
          <div class="d-flex align-items-start mb-3">
            <div class="badge bg-primary rounded-circle me-3" style="width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;">2</div>
            <div>
              <h6 class="fw-bold">Peer Review</h6>
              <small class="text-secondary">Experts review your research</small>
            </div>
          </div>
          <div class="d-flex align-items-start mb-3">
            <div class="badge bg-primary rounded-circle me-3" style="width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;">3</div>
            <div>
              <h6 class="fw-bold">Publication</h6>
              <small class="text-secondary">Approved papers get published</small>
            </div>
          </div>
          <div class="d-flex align-items-start">
            <div class="badge bg-primary rounded-circle me-3" style="width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;">4</div>
            <div>
              <h6 class="fw-bold">Certificate</h6>
              <small class="text-secondary">Receive digital certificate</small>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Footer -->
<?php include 'includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/main.js"></script>
</body>
</html>