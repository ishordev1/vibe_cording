<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

$pageTitle = 'Browse Ideas - ' . APP_NAME;
require_once __DIR__ . '/../includes/header.php';

$conn = getDBConnection();

// Get filter parameters
$category = isset($_GET['category']) ? sanitize($_GET['category']) : '';
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * IDEAS_PER_PAGE;

// Build query
$where_clauses = ["i.status = 'published'"];
$params = [];
$types = '';

if (!empty($category) && in_array($category, IDEA_CATEGORIES)) {
    $where_clauses[] = "i.category = ?";
    $params[] = $category;
    $types .= 's';
}

if (!empty($search)) {
    $where_clauses[] = "(i.title LIKE ? OR i.description LIKE ?)";
    $search_param = '%' . $search . '%';
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= 'ss';
}

$where_sql = implode(' AND ', $where_clauses);

// Get total count
$count_query = "SELECT COUNT(*) as total FROM ideas i WHERE $where_sql";
$stmt = $conn->prepare($count_query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$total_ideas = $stmt->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total_ideas / IDEAS_PER_PAGE);

// Get ideas
$query = "
    SELECT i.*, u.full_name as creator_name, u.username as creator_username,
           (SELECT COUNT(*) FROM investors_interested WHERE idea_id = i.id) as interested_count,
           (SELECT file_path FROM idea_media WHERE idea_id = i.id AND is_primary = 1 LIMIT 1) as primary_image
    FROM ideas i
    JOIN users u ON i.user_id = u.id
    WHERE $where_sql
    ORDER BY i.created_at DESC
    LIMIT ? OFFSET ?
";

$params[] = IDEAS_PER_PAGE;
$params[] = $offset;
$types .= 'ii';

$stmt = $conn->prepare($query);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$ideas = $stmt->get_result();
?>

<div class="container mt-4 mb-5">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col">
            <h2>Browse Business Ideas</h2>
            <p class="text-muted">Discover innovative ideas from entrepreneurs</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <form method="GET" action="" class="row g-3">
                <div class="col-md-4">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           placeholder="Search ideas..." value="<?php echo htmlspecialchars($search); ?>">
                </div>
                <div class="col-md-4">
                    <label for="category" class="form-label">Category</label>
                    <select class="form-select" id="category" name="category">
                        <option value="">All Categories</option>
                        <?php foreach (IDEA_CATEGORIES as $cat): ?>
                            <option value="<?php echo $cat; ?>" <?php echo $category === $cat ? 'selected' : ''; ?>>
                                <?php echo $cat; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="bi bi-search"></i> Filter
                    </button>
                    <a href="<?php echo APP_URL; ?>/pages/ideas.php" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle"></i> Clear
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Results Info -->
    <div class="row mb-3">
        <div class="col">
            <p class="text-muted">
                Showing <?php echo $total_ideas > 0 ? $offset + 1 : 0; ?> - 
                <?php echo min($offset + IDEAS_PER_PAGE, $total_ideas); ?> of 
                <?php echo $total_ideas; ?> ideas
            </p>
        </div>
    </div>

    <!-- Ideas Grid -->
    <?php if ($ideas->num_rows > 0): ?>
        <div class="row">
            <?php while ($idea = $ideas->fetch_assoc()): ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100 shadow-sm hover-shadow">
                        <?php if ($idea['primary_image']): ?>
                            <img src="<?php echo APP_URL . '/' . $idea['primary_image']; ?>" 
                                 class="card-img-top" alt="<?php echo htmlspecialchars($idea['title']); ?>"
                                 style="height: 200px; object-fit: cover;">
                        <?php else: ?>
                            <div class="bg-secondary text-white d-flex align-items-center justify-content-center" style="height: 200px;">
                                <i class="bi bi-lightbulb" style="font-size: 3rem;"></i>
                            </div>
                        <?php endif; ?>
                        
                        <div class="card-body">
                            <span class="badge bg-primary mb-2"><?php echo htmlspecialchars($idea['category']); ?></span>
                            <h5 class="card-title"><?php echo htmlspecialchars($idea['title']); ?></h5>
                            <p class="card-text"><?php echo truncateText($idea['description'], 120); ?></p>
                            
                            <div class="d-flex justify-content-between text-muted small mb-2">
                                <span><i class="bi bi-person"></i> <?php echo htmlspecialchars($idea['creator_name']); ?></span>
                                <span><i class="bi bi-eye"></i> <?php echo $idea['views']; ?></span>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted"><?php echo timeAgo($idea['created_at']); ?></small>
                                <span class="badge bg-danger"><?php echo $idea['interested_count']; ?> interested</span>
                            </div>
                        </div>
                        
                        <div class="card-footer bg-white">
                            <a href="<?php echo APP_URL; ?>/pages/idea-detail.php?id=<?php echo $idea['id']; ?>" 
                               class="btn btn-primary w-100">View Details</a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center">
                    <?php if ($page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo $page - 1; ?>&category=<?php echo urlencode($category); ?>&search=<?php echo urlencode($search); ?>">
                                Previous
                            </a>
                        </li>
                    <?php endif; ?>
                    
                    <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                        <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $i; ?>&category=<?php echo urlencode($category); ?>&search=<?php echo urlencode($search); ?>">
                                <?php echo $i; ?>
                            </a>
                        </li>
                    <?php endfor; ?>
                    
                    <?php if ($page < $total_pages): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo $page + 1; ?>&category=<?php echo urlencode($category); ?>&search=<?php echo urlencode($search); ?>">
                                Next
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        <?php endif; ?>
    <?php else: ?>
        <div class="text-center py-5">
            <i class="bi bi-search" style="font-size: 5rem; color: #ddd;"></i>
            <h4 class="mt-3">No ideas found</h4>
            <p class="text-muted">Try adjusting your filters or search terms.</p>
            <a href="<?php echo APP_URL; ?>/pages/ideas.php" class="btn btn-primary">View All Ideas</a>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
