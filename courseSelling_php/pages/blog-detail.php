<?php
$pageTitle = 'Blog Detail - Digital Tarai';
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../lib/functions.php';
require_once '../app/models/Blog.php';

$blogModel = new Blog($conn);
$slug = isset($_GET['slug']) ? $_GET['slug'] : '';

if (!$slug) {
    redirect('pages/blog.php');
}

$blog = $blogModel->getByIdOrSlug($slug);

if (!$blog) {
    echo "Blog not found";
    exit;
}

$pageTitle = $blog['title'] . ' - Digital Tarai';
?>
<?php include '../app/views/header.php'; ?>

<div class="bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-4xl font-bold text-gray-900 mb-4"><?php echo htmlspecialchars($blog['title']); ?></h1>
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center text-gray-600">
            <div>
                <span class="text-sm"><i class="fas fa-user mr-2"></i><?php echo htmlspecialchars($blog['author_name']); ?></span>
                <span class="mx-2">•</span>
                <span class="text-sm"><i class="fas fa-calendar mr-2"></i><?php echo formatDate($blog['created_at']); ?></span>
            </div>
            <span class="inline-block text-xs font-semibold text-purple-600 bg-purple-50 px-3 py-1 rounded-full mt-3 md:mt-0">
                <?php echo htmlspecialchars($blog['category']); ?>
            </span>
        </div>
    </div>
</div>

<section class="py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        <div class="bg-gradient-to-br from-purple-100 to-purple-50 rounded-lg h-96 mb-12 flex items-center justify-center">
            <i class="fas fa-file-alt text-6xl text-purple-600"></i>
        </div>
        
        <article class="prose max-w-none">
            <?php echo $blog['content']; ?>
        </article>
        
        <!-- Blog Meta -->
        <div class="mt-12 pt-12 border-t border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <p class="text-gray-600 text-sm mb-2">Category</p>
                    <p class="text-lg font-semibold text-gray-900"><?php echo htmlspecialchars($blog['category']); ?></p>
                </div>
                <div>
                    <p class="text-gray-600 text-sm mb-2">Views</p>
                    <p class="text-lg font-semibold text-gray-900"><?php echo $blog['views']; ?> views</p>
                </div>
            </div>
        </div>
        
        <!-- Author Info -->
        <div class="mt-12 p-8 bg-gray-50 rounded-lg">
            <div class="flex items-center space-x-4">
                <div class="w-16 h-16 bg-purple-600 rounded-full flex items-center justify-center text-white text-2xl">
                    <i class="fas fa-user"></i>
                </div>
                <div>
                    <h4 class="font-bold text-gray-900"><?php echo htmlspecialchars($blog['author_name']); ?></h4>
                    <p class="text-gray-600 text-sm">
                        Author at Digital Tarai. Passionate about technology and sharing knowledge.
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Share Section -->
        <div class="mt-12 text-center">
            <p class="text-gray-600 mb-4">Share this article</p>
            <div class="flex justify-center space-x-4">
                <a href="#" class="w-12 h-12 bg-blue-600 text-white rounded-full flex items-center justify-center hover:bg-blue-700">
                    <i class="fab fa-facebook-f"></i>
                </a>
                <a href="#" class="w-12 h-12 bg-blue-400 text-white rounded-full flex items-center justify-center hover:bg-blue-500">
                    <i class="fab fa-twitter"></i>
                </a>
                <a href="#" class="w-12 h-12 bg-blue-700 text-white rounded-full flex items-center justify-center hover:bg-blue-800">
                    <i class="fab fa-linkedin-in"></i>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Back to Blog -->
<section class="py-12 px-4 sm:px-6 lg:px-8 bg-gray-50">
    <div class="max-w-4xl mx-auto text-center">
        <a href="<?php echo SITE_URL; ?>/pages/blog.php" class="btn-primary text-white font-medium py-2 px-6 rounded-lg inline-block">
            <i class="fas fa-arrow-left mr-2"></i>Back to Blog
        </div>
    </div>
</section>

<?php include '../app/views/footer.php'; ?>
