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
    <title>Ethics & Rights | IJSRET</title>
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
        .ethics-section {
            background: white;
            padding: 30px;
            margin-bottom: 30px;
            border-radius: 10px;
            box-shadow: 0 3px 15px rgba(0,0,0,0.08);
        }
        .ethics-section h3 {
            color: #2c5aa0;
            margin-bottom: 20px;
        }
        .icon-badge {
            background: linear-gradient(135deg, #2c5aa0 0%, #1e3a6f 100%);
            color: white;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: inline-flex;
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
        <h1 class="display-4 fw-bold mb-3">Ethics & Rights</h1>
        <p class="lead">Publication ethics and copyright policies</p>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php" class="text-white">Home</a></li>
                <li class="breadcrumb-item active text-white-50">Ethics & Rights</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container mb-5">
    <div class="ethics-section">
        <h3><i class="fas fa-gavel me-2"></i>Publication Ethics</h3>
        <p>IJSRET is committed to maintaining the highest standards of publication ethics and takes all possible measures against publication malpractices.</p>
        
        <h5 class="text-primary mt-4">Author Responsibilities</h5>
        <ul>
            <li><strong>Originality:</strong> Authors must ensure that submitted work is entirely original and properly cite all sources</li>
            <li><strong>Data Accuracy:</strong> Authors should provide accurate data and results without fabrication or falsification</li>
            <li><strong>Multiple Publications:</strong> Authors should not submit the same manuscript to multiple journals simultaneously</li>
            <li><strong>Authorship:</strong> All contributors who made significant contributions should be listed as co-authors</li>
            <li><strong>Conflicts of Interest:</strong> Authors must disclose any financial or personal relationships that could influence their work</li>
            <li><strong>Errors in Published Work:</strong> Authors should promptly notify the editor of any significant errors and cooperate in corrections</li>
        </ul>
        
        <h5 class="text-primary mt-4">Reviewer Responsibilities</h5>
        <ul>
            <li><strong>Confidentiality:</strong> Reviewers must keep all information about manuscripts confidential</li>
            <li><strong>Objectivity:</strong> Reviews should be conducted objectively and constructively</li>
            <li><strong>Promptness:</strong> Reviewers should complete reviews in a timely manner or decline if unable</li>
            <li><strong>Disclosure:</strong> Reviewers should identify conflicts of interest and recuse themselves when appropriate</li>
        </ul>
        
        <h5 class="text-primary mt-4">Editorial Responsibilities</h5>
        <ul>
            <li><strong>Fair Play:</strong> Editors make decisions based on merit, without bias</li>
            <li><strong>Confidentiality:</strong> Editors maintain confidentiality of submitted manuscripts</li>
            <li><strong>Disclosure:</strong> Editors disclose conflicts of interest</li>
            <li><strong>Quality:</strong> Editors strive to improve the journal's quality continuously</li>
        </ul>
    </div>
    
    <div class="ethics-section">
        <h3><i class="fas fa-ban me-2"></i>Plagiarism Policy</h3>
        <div class="alert alert-danger">
            <h5><i class="fas fa-exclamation-triangle me-2"></i>Zero Tolerance for Plagiarism</h5>
            <p>IJSRET has a strict anti-plagiarism policy. All submissions are checked for plagiarism using advanced software.</p>
        </div>
        
        <ul>
            <li>Manuscripts with <strong>less than 10% similarity</strong> are acceptable</li>
            <li>Submissions with <strong>10-20% similarity</strong> may be sent back for revision</li>
            <li>Papers with <strong>more than 20% similarity</strong> are rejected immediately</li>
            <li>Self-plagiarism is also considered unethical</li>
            <li>Proper citation is mandatory for all referenced work</li>
        </ul>
    </div>
    
    <div class="ethics-section">
        <h3><i class="fas fa-copyright me-2"></i>Copyright Policy</h3>
        <p><strong>Copyright Transfer:</strong> Upon acceptance of an article, authors transfer copyright to IJSRET. This transfer enables the journal to protect the original work from unauthorized use.</p>
        
        <h5 class="text-primary mt-4">Author Rights</h5>
        <p>Even after copyright transfer, authors retain the following rights:</p>
        <ul>
            <li>The right to use the article for teaching and lecture purposes</li>
            <li>The right to include the article in a thesis or dissertation</li>
            <li>The right to use the article for personal use</li>
            <li>The right to share the article with colleagues for scholarly purposes</li>
            <li>The right to use data and materials from the article in future works</li>
        </ul>
        
        <h5 class="text-primary mt-4">Open Access</h5>
        <p>IJSRET is an open-access journal. All published articles are freely available to readers worldwide under Creative Commons licenses, promoting the wide dissemination of research.</p>
        
        <div class="alert alert-info mt-3">
            <h6><i class="fas fa-creative-commons me-2"></i>Creative Commons License</h6>
            <p class="mb-0">Articles are published under <strong>CC BY 4.0</strong> license, allowing readers to share and adapt the work with proper attribution.</p>
        </div>
    </div>
    
    <div class="ethics-section">
        <h3><i class="fas fa-shield-alt me-2"></i>Research Ethics</h3>
        
        <h5 class="text-primary">Human Subjects Research</h5>
        <ul>
            <li>Research involving human participants must be approved by an ethical review board</li>
            <li>Informed consent must be obtained from all participants</li>
            <li>Participant privacy and confidentiality must be protected</li>
        </ul>
        
        <h5 class="text-primary mt-4">Animal Research</h5>
        <ul>
            <li>Research involving animals must comply with institutional and national guidelines</li>
            <li>Animal welfare considerations must be documented</li>
            <li>Unnecessary suffering should be avoided</li>
        </ul>
    </div>
    
    <div class="ethics-section">
        <h3><i class="fas fa-users me-2"></i>Authorship Criteria</h3>
        <p>To be listed as an author, individuals must meet all of the following criteria:</p>
        <ol>
            <li>Substantial contributions to conception, design, or data acquisition/analysis/interpretation</li>
            <li>Drafting the article or revising it critically for important intellectual content</li>
            <li>Final approval of the version to be published</li>
            <li>Agreement to be accountable for all aspects of the work</li>
        </ol>
        
        <div class="alert alert-warning mt-3">
            <p class="mb-0"><strong>Note:</strong> Contributors who do not meet all criteria should be acknowledged rather than listed as authors.</p>
        </div>
    </div>
    
    <div class="row mt-5">
        <div class="col-md-6">
            <div class="card border-0 shadow">
                <div class="card-body text-center p-4">
                    <div class="icon-badge mx-auto">
                        <i class="fas fa-file-contract"></i>
                    </div>
                    <h5>Copyright Form</h5>
                    <p>Download the copyright transfer form</p>
                    <a href="downloads.php" class="btn btn-primary">Download Form</a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow">
                <div class="card-body text-center p-4">
                    <div class="icon-badge mx-auto">
                        <i class="fas fa-question-circle"></i>
                    </div>
                    <h5>Have Questions?</h5>
                    <p>Contact us for ethics-related inquiries</p>
                    <a href="contact.php" class="btn btn-primary">Contact Us</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
