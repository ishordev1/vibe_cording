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
    <title>Journal Indexing | IJSRET</title>
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
        .index-card {
            background: white;
            border-radius: 10px;
            padding: 40px;
            margin-bottom: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            text-align: center;
            transition: all 0.3s ease;
            height: 100%;
        }
        .index-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        .index-logo {
            width: 150px;
            height: 150px;
            margin: 0 auto 25px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
        }
        .index-logo img {
            max-width: 100%;
            max-height: 100%;
        }
        .index-name {
            font-size: 1.3rem;
            font-weight: 700;
            color: #2c5aa0;
            margin-bottom: 15px;
        }
        .badge-indexed {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 8px 20px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
        }
    </style>
</head>
<body>
<?php include 'includes/public_navbar.php'; ?>

<div class="page-header">
    <div class="container">
        <h1 class="display-4 fw-bold mb-3">Journal Indexing</h1>
        <p class="lead">IJSRET is indexed and abstracted in major academic databases</p>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php" class="text-white">Home</a></li>
                <li class="breadcrumb-item active text-white-50">Journal Indexing</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container mb-5">
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-success">
                <h4><i class="fas fa-trophy me-2"></i>Wide Coverage & Visibility</h4>
                <p class="mb-0">IJSRET is indexed in multiple international databases, ensuring maximum visibility and impact for published research. This indexing helps researchers worldwide discover and cite your work.</p>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-4">
            <div class="index-card">
                <div class="index-logo">
                    <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; font-size: 4rem; color: #2c5aa0;">
                        <i class="fas fa-bookmark"></i>
                    </div>
                </div>
                <h4 class="index-name">ISSN</h4>
                <p class="text-muted">International Standard Serial Number</p>
                <span class="badge-indexed"><i class="fas fa-check-circle me-1"></i>Registered</span>
                <p class="mt-3"><strong>ISSN: 2395-566X</strong></p>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="index-card">
                <div class="index-logo">
                    <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; font-size: 4rem; color: #d4af37;">
                        <i class="fas fa-fingerprint"></i>
                    </div>
                </div>
                <h4 class="index-name">DOI</h4>
                <p class="text-muted">Digital Object Identifier</p>
                <span class="badge-indexed"><i class="fas fa-check-circle me-1"></i>Assigned</span>
                <p class="mt-3">Every article receives unique DOI</p>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="index-card">
                <div class="index-logo">
                    <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; font-size: 4rem; color: #c4302b;">
                        <i class="fas fa-plus"></i>
                    </div>
                </div>
                <h4 class="index-name">Crossref</h4>
                <p class="text-muted">Citation Linking & Metadata</p>
                <span class="badge-indexed"><i class="fas fa-check-circle me-1"></i>Indexed</span>
                <p class="mt-3">Full metadata registration</p>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="index-card">
                <div class="index-logo">
                    <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; font-size: 4rem; color: #4285f4;">
                        <i class="fab fa-google"></i>
                    </div>
                </div>
                <h4 class="index-name">Google Scholar</h4>
                <p class="text-muted">Academic Search Engine</p>
                <span class="badge-indexed"><i class="fas fa-check-circle me-1"></i>Indexed</span>
                <p class="mt-3">Searchable and discoverable</p>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="index-card">
                <div class="index-logo">
                    <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; font-size: 4rem; color: #ea4335;">
                        <i class="fas fa-university"></i>
                    </div>
                </div>
                <h4 class="index-name">ResearchGate</h4>
                <p class="text-muted">Academic Social Network</p>
                <span class="badge-indexed"><i class="fas fa-check-circle me-1"></i>Listed</span>
                <p class="mt-3">Research collaboration platform</p>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="index-card">
                <div class="index-logo">
                    <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; font-size: 4rem; color: #0077b5;">
                        <i class="fas fa-book-open"></i>
                    </div>
                </div>
                <h4 class="index-name">Academia.edu</h4>
                <p class="text-muted">Academic Repository</p>
                <span class="badge-indexed"><i class="fas fa-check-circle me-1"></i>Available</span>
                <p class="mt-3">Papers accessible to researchers</p>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="index-card">
                <div class="index-logo">
                    <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; font-size: 4rem; color: #7c5295;">
                        <i class="fas fa-database"></i>
                    </div>
                </div>
                <h4 class="index-name">Index Copernicus</h4>
                <p class="text-muted">International Indexing Database</p>
                <span class="badge-indexed"><i class="fas fa-check-circle me-1"></i>Indexed</span>
                <p class="mt-3">Global scientific database</p>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="index-card">
                <div class="index-logo">
                    <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; font-size: 4rem; color: #ff6b6b;">
                        <i class="fas fa-book"></i>
                    </div>
                </div>
                <h4 class="index-name">DRJI</h4>
                <p class="text-muted">Directory of Research Journals Indexing</p>
                <span class="badge-indexed"><i class="fas fa-check-circle me-1"></i>Indexed</span>
                <p class="mt-3">Quality journal directory</p>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="index-card">
                <div class="index-logo">
                    <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; font-size: 4rem; color: #34a853;">
                        <i class="fas fa-globe"></i>
                    </div>
                </div>
                <h4 class="index-name">WorldCat</h4>
                <p class="text-muted">Global Library Catalog</p>
                <span class="badge-indexed"><i class="fas fa-check-circle me-1"></i>Listed</span>
                <p class="mt-3">Accessible through libraries worldwide</p>
            </div>
        </div>
    </div>
    
    <div class="row mt-5">
        <div class="col-12">
            <div class="alert alert-info">
                <h5><i class="fas fa-info-circle me-2"></i>Why Indexing Matters</h5>
                <ul class="mb-0">
                    <li><strong>Increased Visibility:</strong> Your research reaches a global audience</li>
                    <li><strong>Better Citations:</strong> Indexed papers receive more citations</li>
                    <li><strong>Academic Recognition:</strong> Enhances your academic profile and credibility</li>
                    <li><strong>Career Advancement:</strong> Publications in indexed journals are valued for promotions</li>
                    <li><strong>Research Impact:</strong> Contributes to the advancement of your field</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
