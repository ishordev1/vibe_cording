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
    <title>Article Processing Fee | IJSRET</title>
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
        .pricing-card {
            background: white;
            border-radius: 15px;
            padding: 40px;
            margin-bottom: 30px;
            box-shadow: 0 5px 30px rgba(0,0,0,0.1);
            text-align: center;
            transition: all 0.3s ease;
        }
        .pricing-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        .pricing-card.featured {
            background: linear-gradient(135deg, #2c5aa0 0%, #1e3a6f 100%);
            color: white;
            transform: scale(1.05);
        }
        .pricing-card.featured:hover {
            transform: scale(1.08) translateY(-10px);
        }
        .price {
            font-size: 3rem;
            font-weight: 700;
            margin: 20px 0;
        }
        .price-currency {
            font-size: 1.5rem;
            vertical-align: super;
        }
        .price-period {
            font-size: 1rem;
            color: #666;
        }
        .pricing-card.featured .price-period {
            color: rgba(255,255,255,0.8);
        }
        .feature-list {
            list-style: none;
            padding: 0;
            margin: 30px 0;
        }
        .feature-list li {
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        .pricing-card.featured .feature-list li {
            border-bottom: 1px solid rgba(255,255,255,0.2);
        }
        .feature-list i {
            color: #28a745;
            margin-right: 10px;
        }
        .pricing-card.featured .feature-list i {
            color: #d4af37;
        }
        .info-section {
            background: white;
            padding: 30px;
            margin-bottom: 30px;
            border-radius: 10px;
            box-shadow: 0 3px 15px rgba(0,0,0,0.08);
        }
    </style>
</head>
<body>
<?php include 'includes/public_navbar.php'; ?>

<div class="page-header">
    <div class="container">
        <h1 class="display-4 fw-bold mb-3">Article Processing Fee</h1>
        <p class="lead">Transparent and affordable publication fees</p>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php" class="text-white">Home</a></li>
                <li class="breadcrumb-item active text-white-50">Article Processing Fee</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container mb-5">
    <div class="row mb-5">
        <div class="col-12">
            <div class="alert alert-info">
                <h5><i class="fas fa-info-circle me-2"></i>About Publication Fees</h5>
                <p class="mb-0">IJSRET operates on an open-access model. Authors pay a one-time Article Processing Charge (APC) to make their work freely available to readers worldwide. This fee covers editorial processing, peer review management, journal production, and online hosting.</p>
            </div>
        </div>
    </div>
    
    <div class="row justify-content-center">
        <div class="col-lg-4 col-md-6">
            <div class="pricing-card">
                <h3>Indian Authors</h3>
                <div class="price">
                    <span class="price-currency">₹</span>2,500
                </div>
                <p class="price-period">Per Article</p>
                
                <ul class="feature-list">
                    <li><i class="fas fa-check-circle"></i>Fast Peer Review</li>
                    <li><i class="fas fa-check-circle"></i>Professional Editing</li>
                    <li><i class="fas fa-check-circle"></i>DOI Assignment</li>
                    <li><i class="fas fa-check-circle"></i>ISSN Registration</li>
                    <li><i class="fas fa-check-circle"></i>Digital Certificate</li>
                    <li><i class="fas fa-check-circle"></i>Indexing Services</li>
                    <li><i class="fas fa-check-circle"></i>Open Access</li>
                </ul>
                
                <a href="submit_paper.php" class="btn btn-primary btn-lg w-100">Submit Paper</a>
            </div>
        </div>
        
        <div class="col-lg-4 col-md-6">
            <div class="pricing-card featured">
                <div class="badge bg-warning text-dark mb-3 px-3 py-2">
                    <i class="fas fa-star me-1"></i>Most Popular
                </div>
                <h3>International Authors</h3>
                <div class="price">
                    <span class="price-currency">$</span>50
                </div>
                <p class="price-period">Per Article</p>
                
                <ul class="feature-list">
                    <li><i class="fas fa-check-circle"></i>Fast Peer Review</li>
                    <li><i class="fas fa-check-circle"></i>Professional Editing</li>
                    <li><i class="fas fa-check-circle"></i>DOI Assignment</li>
                    <li><i class="fas fa-check-circle"></i>ISSN Registration</li>
                    <li><i class="fas fa-check-circle"></i>Digital Certificate</li>
                    <li><i class="fas fa-check-circle"></i>Indexing Services</li>
                    <li><i class="fas fa-check-circle"></i>Open Access</li>
                    <li><i class="fas fa-check-circle"></i>Priority Support</li>
                </ul>
                
                <a href="submit_paper.php" class="btn btn-warning btn-lg w-100 text-dark fw-bold">Submit Paper</a>
            </div>
        </div>
    </div>
    
    <div class="row mt-5">
        <div class="col-md-6">
            <div class="info-section">
                <h4><i class="fas fa-credit-card me-2 text-primary"></i>Payment Methods</h4>
                <ul>
                    <li><strong>Bank Transfer:</strong> Direct deposit to our account</li>
                    <li><strong>Online Payment:</strong> UPI, Net Banking, Debit/Credit Cards</li>
                    <li><strong>PayPal:</strong> For international authors</li>
                    <li><strong>Demand Draft:</strong> Payable at par</li>
                </ul>
                <div class="alert alert-warning mt-3">
                    <small><i class="fas fa-exclamation-triangle me-2"></i>Payment should be made only after article acceptance notification</small>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="info-section">
                <h4><i class="fas fa-gift me-2 text-success"></i>What's Included</h4>
                <ul>
                    <li>Rigorous peer review process</li>
                    <li>Professional language editing (if required)</li>
                    <li>Plagiarism checking</li>
                    <li>DOI registration for permanent citation</li>
                    <li>Indexing in major databases</li>
                    <li>Digital publication certificate for all authors</li>
                    <li>Unlimited online access</li>
                    <li>PDF version of published article</li>
                </ul>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-12">
            <div class="info-section">
                <h4><i class="fas fa-percent me-2 text-info"></i>Discounts & Waivers</h4>
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-primary">Available Discounts</h6>
                        <ul>
                            <li><strong>Student Authors:</strong> 10% discount with valid student ID</li>
                            <li><strong>Developing Countries:</strong> Case-by-case consideration</li>
                            <li><strong>Multiple Papers:</strong> 5% discount on second paper from same author</li>
                            <li><strong>Institutional Members:</strong> Special rates available</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-primary">Fee Waiver Requests</h6>
                        <p>Authors from economically disadvantaged regions may request fee waivers. Requests should be made at the time of submission with supporting documentation.</p>
                        <a href="contact.php" class="btn btn-outline-primary">Request Waiver</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-12">
            <div class="info-section">
                <h4><i class="fas fa-undo me-2 text-danger"></i>Refund Policy</h4>
                <ul>
                    <li>Full refund if paper is rejected after payment</li>
                    <li>No refund after article publication</li>
                    <li>Refund processing takes 7-14 business days</li>
                    <li>Refunds are issued through the original payment method</li>
                </ul>
            </div>
        </div>
    </div>
    
    <div class="row mt-5">
        <div class="col-12 text-center">
            <h4 class="mb-4">Ready to Publish Your Research?</h4>
            <a href="submit_paper.php" class="btn btn-primary btn-lg me-3">
                <i class="fas fa-upload me-2"></i>Submit Paper Now
            </a>
            <a href="contact.php" class="btn btn-outline-primary btn-lg">
                <i class="fas fa-envelope me-2"></i>Contact for Queries
            </a>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
