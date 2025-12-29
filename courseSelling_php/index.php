<?php
$pageTitle = 'Home - Digital Tarai | Software Development Company';
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'lib/functions.php';
require_once 'app/models/Internship.php';
require_once 'app/models/Blog.php';

$internshipModel = new Internship($conn);
$blogModel = new Blog($conn);
$internships = $internshipModel->getAll();
$recentBlogs = $blogModel->getAll(3);
?>
<?php include 'app/views/header.php'; ?>

<!-- Hero Section -->
<section class="hero-gradient py-20 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
            <!-- Left Content -->
            <div>
                <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6">
                    We Build Digital Solutions for Your Ideas
                </h1>
                <p class="text-lg text-gray-600 mb-8">
                    Turn your idea into scalable software with Digital Tarai. We're a software development company based in Siraha, Nepal, dedicated to building innovative digital products.
                </p>
                
                <!-- CTA Buttons -->
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="<?php echo SITE_URL; ?>/pages/contact.php" class="btn-primary text-white font-medium py-3 px-8 rounded-lg text-center">
                        <i class="fas fa-envelope mr-2"></i>Contact Us
                    </a>
                    <a href="<?php echo SITE_URL; ?>/pages/careers.php" class="btn-secondary text-center font-medium py-3 px-8 rounded-lg">
                        <i class="fas fa-briefcase mr-2"></i>Get Free Consultation
                    </a>
                </div>
                
                <!-- Stats -->
                <div class="grid grid-cols-3 gap-4 mt-12">
                    <div class="text-center">
                        <p class="text-3xl font-bold text-purple-600">50+</p>
                        <p class="text-gray-600 text-sm">Projects</p>
                    </div>
                    <div class="text-center">
                        <p class="text-3xl font-bold text-purple-600">30+</p>
                        <p class="text-gray-600 text-sm">Clients</p>
                    </div>
                    <div class="text-center">
                        <p class="text-3xl font-bold text-purple-600">15+</p>
                        <p class="text-gray-600 text-sm">Team Members</p>
                    </div>
                </div>
            </div>
            
            <!-- Right Image -->
            <div class="hidden md:flex items-center justify-center">
                <div class="w-full h-full bg-gradient-to-br from-purple-100 to-purple-50 rounded-lg p-8 flex items-center justify-center">
                    <div class="text-center">
                        <i class="fas fa-laptop-code text-6xl text-purple-600 mb-4"></i>
                        <p class="text-gray-600">Building Digital Solutions</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Services Section -->
<section class="py-20 px-4 sm:px-6 lg:px-8 bg-gray-50">
    <div class="max-w-7xl mx-auto">
        <h2 class="text-3xl md:text-4xl font-bold text-center text-gray-900 mb-12">
            Our Services
        </h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <!-- Web Development -->
            <div class="card bg-white p-8 rounded-lg">
                <div class="service-icon mb-4">
                    <i class="fas fa-globe"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Web Development</h3>
                <p class="text-gray-600">
                    Modern, responsive websites and web applications built with latest technologies.
                </p>
            </div>
            
            <!-- Mobile Development -->
            <div class="card bg-white p-8 rounded-lg">
                <div class="service-icon mb-4">
                    <i class="fas fa-mobile-alt"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Mobile App Development</h3>
                <p class="text-gray-600">
                    Native Android apps and cross-platform solutions for iOS and Android.
                </p>
            </div>
            
            <!-- Custom Software -->
            <div class="card bg-white p-8 rounded-lg">
                <div class="service-icon mb-4">
                    <i class="fas fa-cogs"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Custom Software</h3>
                <p class="text-gray-600">
                    Tailored software solutions designed for your specific business needs.
                </p>
            </div>
            
            <!-- UI/UX Design -->
            <div class="card bg-white p-8 rounded-lg">
                <div class="service-icon mb-4">
                    <i class="fas fa-paint-brush"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">UI/UX Design</h3>
                <p class="text-gray-600">
                    Beautiful, user-friendly interfaces that engage and delight your users.
                </p>
            </div>
        </div>
        
        <div class="text-center mt-12">
            <a href="<?php echo SITE_URL; ?>/pages/services.php" class="btn-primary text-white font-medium py-3 px-8 rounded-lg inline-block">
                View All Services <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>
    </div>
</section>

<!-- Why Choose Us Section -->
<section class="py-20 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <h2 class="text-3xl md:text-4xl font-bold text-center text-gray-900 mb-12">
            Why Choose Digital Tarai?
        </h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Expertise -->
            <div class="p-8 border border-gray-200 rounded-lg">
                <div class="text-4xl text-purple-600 mb-4">
                    <i class="fas fa-star"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Expert Team</h3>
                <p class="text-gray-600">
                    Our team consists of experienced developers and designers with years of industry experience.
                </p>
            </div>
            
            <!-- Quality -->
            <div class="p-8 border border-gray-200 rounded-lg">
                <div class="text-4xl text-purple-600 mb-4">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Quality Assurance</h3>
                <p class="text-gray-600">
                    We ensure every project meets high quality standards through rigorous testing and reviews.
                </p>
            </div>
            
            <!-- Support -->
            <div class="p-8 border border-gray-200 rounded-lg">
                <div class="text-4xl text-purple-600 mb-4">
                    <i class="fas fa-headset"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">24/7 Support</h3>
                <p class="text-gray-600">
                    Dedicated support team available to help you whenever you need assistance.
                </p>
            </div>
            
            <!-- Innovation -->
            <div class="p-8 border border-gray-200 rounded-lg">
                <div class="text-4xl text-purple-600 mb-4">
                    <i class="fas fa-lightbulb"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Innovation</h3>
                <p class="text-gray-600">
                    We stay updated with latest technologies and industry trends to deliver cutting-edge solutions.
                </p>
            </div>
            
            <!-- Cost Effective -->
            <div class="p-8 border border-gray-200 rounded-lg">
                <div class="text-4xl text-purple-600 mb-4">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Cost Effective</h3>
                <p class="text-gray-600">
                    Get the best value for your investment with our competitive pricing and flexible plans.
                </p>
            </div>
            
            <!-- Commitment -->
            <div class="p-8 border border-gray-200 rounded-lg">
                <div class="text-4xl text-purple-600 mb-4">
                    <i class="fas fa-handshake"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Commitment</h3>
                <p class="text-gray-600">
                    We're committed to your success and work closely with you to achieve your goals.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Recent Blog Posts -->
<section class="py-20 px-4 sm:px-6 lg:px-8 bg-gray-50">
    <div class="max-w-7xl mx-auto">
        <h2 class="text-3xl md:text-4xl font-bold text-center text-gray-900 mb-12">
            Latest from Our Blog
        </h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <?php while ($blog = $recentBlogs->fetch_assoc()): ?>
                <div class="card bg-white rounded-lg overflow-hidden">
                    <div class="h-48 bg-gradient-to-br from-purple-100 to-purple-50 p-8 flex items-center justify-center">
                        <i class="fas fa-file-alt text-4xl text-purple-600"></i>
                    </div>
                    <div class="p-6">
                        <span class="inline-block text-xs font-semibold text-purple-600 bg-purple-50 px-3 py-1 rounded-full mb-3">
                            <?php echo $blog['category']; ?>
                        </span>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">
                            <?php echo truncateText($blog['title'], 50); ?>
                        </h3>
                        <p class="text-gray-600 text-sm mb-4">
                            <?php echo truncateText(strip_tags($blog['content']), 100); ?>
                        </p>
                        <div class="flex justify-between items-center text-sm text-gray-500">
                            <span><?php echo formatDate($blog['created_at']); ?></span>
                            <a href="<?php echo SITE_URL; ?>/pages/blog-detail.php?slug=<?php echo $blog['slug']; ?>" class="text-purple-600 hover:text-purple-700 font-semibold">
                                Read More <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
        
        <div class="text-center mt-12">
            <a href="<?php echo SITE_URL; ?>/pages/blog.php" class="btn-primary text-white font-medium py-3 px-8 rounded-lg inline-block">
                View All Articles <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>
    </div>
</section>

<!-- Internship CTA Section -->
<section class="py-20 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <div class="bg-gradient-to-r from-purple-600 to-purple-800 rounded-lg p-12 text-white text-center">
            <h2 class="text-3xl md:text-4xl font-bold mb-4">
                Interested in Internships?
            </h2>
            <p class="text-lg mb-8 max-w-2xl mx-auto">
                Join our internship program and gain real-world experience in software development. Learn from industry experts and build your professional portfolio.
            </p>
            <a href="<?php echo SITE_URL; ?>/pages/careers.php" class="bg-white text-purple-600 font-bold py-3 px-8 rounded-lg hover:bg-gray-100 transition inline-block">
                <i class="fas fa-briefcase mr-2"></i>Explore Internships
            </a>
        </div>
    </div>
</section>

<!-- Contact Form Section -->
<section class="py-20 px-4 sm:px-6 lg:px-8 bg-gray-50">
    <div class="max-w-4xl mx-auto">
        <h2 class="text-3xl md:text-4xl font-bold text-center text-gray-900 mb-4">
            Get In Touch
        </h2>
        <p class="text-center text-gray-600 mb-12">
            Have a project in mind? We'd love to hear from you. Send us a message and we'll respond as soon as possible.
        </p>
        
        <form class="bg-white rounded-lg shadow-md p-8 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                    <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" placeholder="Your name" required>
                </div>
                
                <!-- Email -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                    <input type="email" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" placeholder="your@email.com" required>
                </div>
            </div>
            
            <!-- Subject -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Subject</label>
                <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" placeholder="What is this about?" required>
            </div>
            
            <!-- Message -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Message</label>
                <textarea class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" rows="5" placeholder="Your message..." required></textarea>
            </div>
            
            <!-- Submit -->
            <button type="submit" class="w-full btn-primary text-white font-medium py-3 rounded-lg">
                <i class="fas fa-paper-plane mr-2"></i>Send Message
            </button>
        </form>
        
        <!-- Contact Info -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mt-12">
            <div class="text-center">
                <div class="text-4xl text-purple-600 mb-4">
                    <i class="fas fa-envelope"></i>
                </div>
                <h4 class="font-bold text-gray-900 mb-2">Email</h4>
                <p class="text-gray-600"><?php echo COMPANY_EMAIL; ?></p>
            </div>
            
            <div class="text-center">
                <div class="text-4xl text-purple-600 mb-4">
                    <i class="fas fa-phone"></i>
                </div>
                <h4 class="font-bold text-gray-900 mb-2">Phone</h4>
                <p class="text-gray-600"><?php echo COMPANY_PHONE; ?></p>
            </div>
            
            <div class="text-center">
                <div class="text-4xl text-purple-600 mb-4">
                    <i class="fas fa-map-marker-alt"></i>
                </div>
                <h4 class="font-bold text-gray-900 mb-2">Location</h4>
                <p class="text-gray-600"><?php echo COMPANY_LOCATION; ?></p>
            </div>
        </div>
    </div>
</section>

<?php include 'app/views/footer.php'; ?>
