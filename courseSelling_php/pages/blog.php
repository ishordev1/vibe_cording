<?php
$pageTitle = 'Blog - Digital Tarai';
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../lib/functions.php';
require_once '../app/models/Blog.php';

$blogModel = new Blog($conn);
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 9;
$offset = ($page - 1) * $perPage;

$blogs = $blogModel->getAll($perPage, $offset);
$totalBlogs = $blogModel->getTotalCount();
$totalPages = ceil($totalBlogs / $perPage);
?>
<?php include '../app/views/header.php'; ?>

<div class="bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <h1 class="text-4xl font-bold text-gray-900 mb-4">Our Blog</h1>
        <p class="text-lg text-gray-600">Latest articles about software development and technology</p>
    </div>
</div>

<section class="py-20 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-12">
            <?php while ($blog = $blogs->fetch_assoc()): ?>
                <div class="card bg-white rounded-lg overflow-hidden">
                    <div class="h-48 bg-gradient-to-br from-purple-100 to-purple-50 p-8 flex items-center justify-center">
                        <i class="fas fa-file-alt text-4xl text-purple-600"></i>
                    </div>
                    <div class="p-6">
                        <span class="inline-block text-xs font-semibold text-purple-600 bg-purple-50 px-3 py-1 rounded-full mb-3">
                            <?php echo htmlspecialchars($blog['category']); ?>
                        </span>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">
                            <?php echo htmlspecialchars($blog['title']); ?>
                        </h3>
                        <p class="text-gray-600 text-sm mb-4">
                            <?php echo truncateText(strip_tags($blog['content']), 100); ?>
                        </p>
                        <div class="flex justify-between items-center text-sm text-gray-500">
                            <span><?php echo formatDate($blog['created_at']); ?></span>
                            <a href="<?php echo SITE_URL; ?>/pages/blog-detail.php?slug=<?php echo urlencode($blog['slug']); ?>" class="text-purple-600 hover:text-purple-700 font-semibold">
                                Read More <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
        
        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
            <div class="flex justify-center items-center space-x-2 mb-12">
                <?php if ($page > 1): ?>
                    <a href="?page=<?php echo $page - 1; ?>" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-600 hover:text-purple-600">
                        <i class="fas fa-chevron-left mr-2"></i>Previous
                    </a>
                <?php endif; ?>
                
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <?php if ($i == $page): ?>
                        <span class="px-4 py-2 bg-purple-600 text-white rounded-lg"><?php echo $i; ?></span>
                    <?php else: ?>
                        <a href="?page=<?php echo $i; ?>" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-600 hover:text-purple-600">
                            <?php echo $i; ?>
                        </a>
                    <?php endif; ?>
                <?php endfor; ?>
                
                <?php if ($page < $totalPages): ?>
                    <a href="?page=<?php echo $page + 1; ?>" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-600 hover:text-purple-600">
                        Next<i class="fas fa-chevron-right ml-2"></i>
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include '../app/views/footer.php'; ?>
