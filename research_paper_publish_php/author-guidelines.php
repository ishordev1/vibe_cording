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
    <title>Author Guidelines | IJSRET</title>
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
        .guideline-section {
            background: white;
            padding: 30px;
            margin-bottom: 30px;
            border-radius: 10px;
            box-shadow: 0 3px 15px rgba(0,0,0,0.08);
        }
        .guideline-section h3 {
            color: #2c5aa0;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 3px solid #2c5aa0;
        }
        .guideline-section ul li {
            margin-bottom: 10px;
            line-height: 1.8;
        }
        .icon-box {
            background: linear-gradient(135deg, #2c5aa0 0%, #1e3a6f 100%);
            color: white;
            padding: 15px;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 15px;
        }
        .step-card {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 20px;
            border-left: 5px solid #2c5aa0;
        }
        .step-number {
            background: linear-gradient(135deg, #2c5aa0 0%, #1e3a6f 100%);
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.2rem;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
<?php include 'includes/public_navbar.php'; ?>

<div class="page-header">
    <div class="container">
        <h1 class="display-4 fw-bold mb-3">Author's Guidelines</h1>
        <p class="lead">Comprehensive guide for manuscript preparation and submission</p>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php" class="text-white">Home</a></li>
                <li class="breadcrumb-item active text-white-50">Author's Guidelines</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container mb-5">
    <!-- Submission Process -->
    <div class="guideline-section">
        <h3><i class="fas fa-upload me-2"></i>Submission Process</h3>
        <div class="row">
            <div class="col-md-6">
                <div class="step-card">
                    <div class="step-number">1</div>
                    <h5>Register an Account</h5>
                    <p>Create your author account on our submission portal with valid email address.</p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="step-card">
                    <div class="step-number">2</div>
                    <h5>Prepare Manuscript</h5>
                    <p>Format your paper according to our template and guidelines.</p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="step-card">
                    <div class="step-number">3</div>
                    <h5>Submit Online</h5>
                    <p>Upload your manuscript through the online submission system.</p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="step-card">
                    <div class="step-number">4</div>
                    <h5>Track Status</h5>
                    <p>Monitor your submission status and respond to reviewer comments.</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Manuscript Preparation -->
    <div class="guideline-section">
        <h3><i class="fas fa-file-alt me-2"></i>Manuscript Preparation</h3>
        <div class="row">
            <div class="col-md-12">
                <h5 class="text-primary mt-3">General Format</h5>
                <ul>
                    <li><strong>File Format:</strong> Microsoft Word (.doc or .docx) or PDF</li>
                    <li><strong>Page Size:</strong> A4 (210 × 297 mm)</li>
                    <li><strong>Margins:</strong> 1 inch (2.54 cm) on all sides</li>
                    <li><strong>Font:</strong> Times New Roman, 12 pt for body text</li>
                    <li><strong>Line Spacing:</strong> Single spacing</li>
                    <li><strong>Alignment:</strong> Justified</li>
                    <li><strong>Column:</strong> Two-column format (except title, authors, and abstract)</li>
                </ul>
                
                <h5 class="text-primary mt-4">Structure</h5>
                <ul>
                    <li><strong>Title:</strong> Concise and informative (14 pt, bold, centered)</li>
                    <li><strong>Authors:</strong> Full names with affiliations and email addresses</li>
                    <li><strong>Abstract:</strong> 150-250 words summarizing the paper</li>
                    <li><strong>Keywords:</strong> 4-6 keywords for indexing</li>
                    <li><strong>Introduction:</strong> Background, literature review, and objectives</li>
                    <li><strong>Methodology:</strong> Detailed description of methods used</li>
                    <li><strong>Results:</strong> Present findings clearly with figures/tables</li>
                    <li><strong>Discussion:</strong> Interpret results and implications</li>
                    <li><strong>Conclusion:</strong> Summary and future scope</li>
                    <li><strong>References:</strong> IEEE citation format</li>
                </ul>
                
                <h5 class="text-primary mt-4">Length</h5>
                <ul>
                    <li>Research Articles: 6-10 pages</li>
                    <li>Review Articles: 8-15 pages</li>
                    <li>Short Communications: 3-5 pages</li>
                </ul>
            </div>
        </div>
    </div>
    
    <!-- Figures and Tables -->
    <div class="guideline-section">
        <h3><i class="fas fa-image me-2"></i>Figures and Tables</h3>
        <ul>
            <li>All figures and tables must be cited in the text</li>
            <li>Figures should be of high resolution (minimum 300 DPI)</li>
            <li>Tables should be created using Word table function, not images</li>
            <li>Each figure/table must have a descriptive caption</li>
            <li>Number figures and tables consecutively (Figure 1, Table 1, etc.)</li>
            <li>Place figures and tables close to their first mention in text</li>
        </ul>
    </div>
    
    <!-- References -->
    <div class="guideline-section">
        <h3><i class="fas fa-bookmark me-2"></i>References</h3>
        <p><strong>Citation Style: IEEE Format</strong></p>
        <ul>
            <li>Citations are numbered sequentially in order of appearance [1], [2], etc.</li>
            <li>List all references at the end of the paper in numerical order</li>
            <li>Include all authors, article title, journal/conference name, volume, issue, page numbers, and year</li>
        </ul>
        
        <div class="alert alert-info mt-3">
            <h6><i class="fas fa-lightbulb me-2"></i>Example Reference Formats:</h6>
            <p class="mb-2"><strong>Journal Article:</strong><br>
            [1] A. B. Author, "Title of paper," <em>Journal Name</em>, vol. X, no. Y, pp. ZZ-ZZ, Month Year.</p>
            
            <p class="mb-2"><strong>Conference Paper:</strong><br>
            [2] A. B. Author, "Title of paper," in <em>Proc. Conference Name</em>, City, Country, Year, pp. XX-XX.</p>
            
            <p class="mb-0"><strong>Book:</strong><br>
            [3] A. B. Author, <em>Book Title</em>, Edition. City: Publisher, Year.</p>
        </div>
    </div>
    
    <!-- Ethical Guidelines -->
    <div class="guideline-section">
        <h3><i class="fas fa-gavel me-2"></i>Ethical Guidelines</h3>
        <ul>
            <li>Submit only original research that has not been published elsewhere</li>
            <li>Do not submit the same manuscript to multiple journals simultaneously</li>
            <li>Cite all sources appropriately and avoid plagiarism</li>
            <li>Include all contributors as co-authors</li>
            <li>Disclose any conflicts of interest</li>
            <li>Obtain necessary permissions for copyrighted material</li>
            <li>Follow ethical standards for research involving human subjects or animals</li>
        </ul>
    </div>
    
    <!-- Quick Links -->
    <div class="row mt-5">
        <div class="col-md-4">
            <div class="text-center p-4 bg-light rounded">
                <div class="icon-box mx-auto">
                    <i class="fas fa-download"></i>
                </div>
                <h5>Download Template</h5>
                <p>Get our manuscript template</p>
                <a href="downloads.php" class="btn btn-primary">Download</a>
            </div>
        </div>
        <div class="col-md-4">
            <div class="text-center p-4 bg-light rounded">
                <div class="icon-box mx-auto">
                    <i class="fas fa-upload"></i>
                </div>
                <h5>Submit Paper</h5>
                <p>Ready to submit your work?</p>
                <a href="submit_paper.php" class="btn btn-primary">Submit Now</a>
            </div>
        </div>
        <div class="col-md-4">
            <div class="text-center p-4 bg-light rounded">
                <div class="icon-box mx-auto">
                    <i class="fas fa-question-circle"></i>
                </div>
                <h5>Need Help?</h5>
                <p>Contact our support team</p>
                <a href="contact.php" class="btn btn-primary">Contact Us</a>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
