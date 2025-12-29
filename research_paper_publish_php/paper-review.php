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
    <title>About Paper Review | IJSRET</title>
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
        .timeline {
            position: relative;
            padding: 20px 0;
        }
        .timeline-item {
            padding: 20px 30px;
            margin-bottom: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 3px 15px rgba(0,0,0,0.08);
            position: relative;
            margin-left: 40px;
        }
        .timeline-item::before {
            content: '';
            position: absolute;
            left: -40px;
            top: 50%;
            transform: translateY(-50%);
            width: 30px;
            height: 30px;
            background: linear-gradient(135deg, #2c5aa0 0%, #1e3a6f 100%);
            border-radius: 50%;
            border: 4px solid white;
            box-shadow: 0 0 0 2px #2c5aa0;
        }
        .timeline-item h5 {
            color: #2c5aa0;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
<?php include 'includes/public_navbar.php'; ?>

<div class="page-header">
    <div class="container">
        <h1 class="display-4 fw-bold mb-3">About Paper Review</h1>
        <p class="lead">Our peer review process and quality standards</p>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php" class="text-white">Home</a></li>
                <li class="breadcrumb-item active text-white-50">About Paper Review</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container mb-5">
    <div class="row mb-5">
        <div class="col-lg-10 mx-auto">
            <div class="card border-0 shadow">
                <div class="card-body p-5">
                    <h3 class="mb-4"><i class="fas fa-search me-2 text-primary"></i>Peer Review Process</h3>
                    <p class="lead">IJSRET follows a rigorous double-blind peer review process to ensure the quality and validity of published research.</p>
                    
                    <p>Our review process is designed to be fair, transparent, and constructive, helping authors improve their work while maintaining high publication standards.</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-lg-10 mx-auto">
            <h3 class="mb-4 text-center">Review Timeline</h3>
            <div class="timeline">
                <div class="timeline-item">
                    <h5><i class="fas fa-upload me-2"></i>Step 1: Submission</h5>
                    <p><strong>Time: Day 1</strong></p>
                    <p>Author submits manuscript through our online portal. Initial screening for format and scope compliance.</p>
                </div>
                
                <div class="timeline-item">
                    <h5><i class="fas fa-check-circle me-2"></i>Step 2: Initial Review</h5>
                    <p><strong>Time: 1-2 days</strong></p>
                    <p>Editorial team checks for plagiarism, formatting, and relevance to journal scope. Papers with >20% plagiarism are desk rejected.</p>
                </div>
                
                <div class="timeline-item">
                    <h5><i class="fas fa-users me-2"></i>Step 3: Reviewer Assignment</h5>
                    <p><strong>Time: 2-3 days</strong></p>
                    <p>Manuscript is sent to 2-3 independent reviewers with expertise in the relevant field.</p>
                </div>
                
                <div class="timeline-item">
                    <h5><i class="fas fa-clipboard-list me-2"></i>Step 4: Peer Review</h5>
                    <p><strong>Time: 7-14 days</strong></p>
                    <p>Reviewers evaluate the manuscript for originality, methodology, results, and contribution. They provide detailed feedback and recommendations.</p>
                </div>
                
                <div class="timeline-item">
                    <h5><i class="fas fa-balance-scale me-2"></i>Step 5: Editorial Decision</h5>
                    <p><strong>Time: 1-2 days after reviews</strong></p>
                    <p>Editor-in-Chief makes final decision based on reviewer recommendations: Accept, Minor Revision, Major Revision, or Reject.</p>
                </div>
                
                <div class="timeline-item">
                    <h5><i class="fas fa-edit me-2"></i>Step 6: Revision (if required)</h5>
                    <p><strong>Time: Author has 7-14 days</strong></p>
                    <p>Authors revise manuscript addressing reviewer comments and resubmit with response letter.</p>
                </div>
                
                <div class="timeline-item">
                    <h5><i class="fas fa-check-double me-2"></i>Step 7: Final Acceptance</h5>
                    <p><strong>Time: 1-2 days</strong></p>
                    <p>Revised manuscript is checked and accepted for publication. Copyright form and fee payment requested.</p>
                </div>
                
                <div class="timeline-item">
                    <h5><i class="fas fa-print me-2"></i>Step 8: Publication</h5>
                    <p><strong>Time: Within 1 week of payment</strong></p>
                    <p>Article is published online with DOI. Authors receive publication certificates.</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-5">
        <div class="col-md-6">
            <div class="card border-0 shadow h-100">
                <div class="card-body p-4">
                    <h4 class="mb-3"><i class="fas fa-star text-warning me-2"></i>Review Criteria</h4>
                    <ul>
                        <li><strong>Originality:</strong> Novel contribution to the field</li>
                        <li><strong>Methodology:</strong> Appropriate research methods</li>
                        <li><strong>Results:</strong> Clear and valid findings</li>
                        <li><strong>Discussion:</strong> Proper interpretation of results</li>
                        <li><strong>References:</strong> Adequate and relevant citations</li>
                        <li><strong>Presentation:</strong> Clear writing and organization</li>
                        <li><strong>Significance:</strong> Impact on the field</li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card border-0 shadow h-100">
                <div class="card-body p-4">
                    <h4 class="mb-3"><i class="fas fa-flag text-danger me-2"></i>Common Rejection Reasons</h4>
                    <ul>
                        <li>High plagiarism (>20% similarity)</li>
                        <li>Poor English language quality</li>
                        <li>Insufficient novelty or contribution</li>
                        <li>Flawed methodology or analysis</li>
                        <li>Incomplete or inadequate results</li>
                        <li>Out of scope for the journal</li>
                        <li>Ethical concerns or data issues</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-5">
        <div class="col-12">
            <div class="alert alert-success">
                <h5><i class="fas fa-lightbulb me-2"></i>Tips for Authors</h5>
                <ul class="mb-0">
                    <li>Follow the author guidelines carefully</li>
                    <li>Ensure your work is original and properly cited</li>
                    <li>Use clear, professional English</li>
                    <li>Provide detailed methodology and results</li>
                    <li>Address all reviewer comments thoroughly when revising</li>
                    <li>Include high-quality figures and tables</li>
                    <li>Highlight the novelty and significance of your work</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
