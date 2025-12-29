<?php
$pageTitle = 'Services - Digital Tarai';
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../lib/functions.php';
?>
<?php include '../app/views/header.php'; ?>

<div class="bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <h1 class="text-4xl font-bold text-gray-900 mb-4">Our Services</h1>
        <p class="text-lg text-gray-600">Comprehensive software development solutions for your business</p>
    </div>
</div>

<section class="py-20 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <!-- Web Development -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center mb-20">
            <div class="order-2 md:order-1">
                <div class="bg-gradient-to-br from-purple-100 to-purple-50 rounded-lg p-12 h-80 flex items-center justify-center">
                    <i class="fas fa-globe text-6xl text-purple-600"></i>
                </div>
            </div>
            <div class="order-1 md:order-2">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Web Development</h2>
                <p class="text-gray-600 mb-6">
                    We build modern, responsive websites and web applications using the latest technologies. Our web development services include:
                </p>
                <ul class="space-y-3 mb-6">
                    <li class="flex items-start">
                        <i class="fas fa-check text-purple-600 mr-3 mt-1"></i>
                        <span class="text-gray-700"><strong>Frontend Development:</strong> React, Vue, Tailwind CSS</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-purple-600 mr-3 mt-1"></i>
                        <span class="text-gray-700"><strong>Backend Development:</strong> PHP, MySQL, APIs</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-purple-600 mr-3 mt-1"></i>
                        <span class="text-gray-700"><strong>Database Design:</strong> Scalable and optimized databases</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-purple-600 mr-3 mt-1"></i>
                        <span class="text-gray-700"><strong>E-Commerce:</strong> Online stores and payment integration</span>
                    </li>
                </ul>
                <a href="<?php echo SITE_URL; ?>/pages/contact.php" class="btn-primary text-white font-medium py-2 px-6 rounded-lg inline-block">
                    Get a Quote <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
        </div>
        
        <!-- Mobile Development -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center mb-20">
            <div>
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Mobile App Development</h2>
                <p class="text-gray-600 mb-6">
                    Create powerful mobile applications for iOS and Android platforms. We specialize in:
                </p>
                <ul class="space-y-3 mb-6">
                    <li class="flex items-start">
                        <i class="fas fa-check text-purple-600 mr-3 mt-1"></i>
                        <span class="text-gray-700"><strong>Native Android:</strong> Java and Kotlin development</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-purple-600 mr-3 mt-1"></i>
                        <span class="text-gray-700"><strong>Cross-Platform:</strong> React Native, Flutter</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-purple-600 mr-3 mt-1"></i>
                        <span class="text-gray-700"><strong>App Store Optimization:</strong> Get your app on stores</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-purple-600 mr-3 mt-1"></i>
                        <span class="text-gray-700"><strong>Maintenance & Support:</strong> Keep your app updated</span>
                    </li>
                </ul>
                <a href="<?php echo SITE_URL; ?>/pages/contact.php" class="btn-primary text-white font-medium py-2 px-6 rounded-lg inline-block">
                    Get a Quote <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
            <div>
                <div class="bg-gradient-to-br from-purple-100 to-purple-50 rounded-lg p-12 h-80 flex items-center justify-center">
                    <i class="fas fa-mobile-alt text-6xl text-purple-600"></i>
                </div>
            </div>
        </div>
        
        <!-- Custom Software -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center mb-20">
            <div class="order-2 md:order-1">
                <div class="bg-gradient-to-br from-purple-100 to-purple-50 rounded-lg p-12 h-80 flex items-center justify-center">
                    <i class="fas fa-cogs text-6xl text-purple-600"></i>
                </div>
            </div>
            <div class="order-1 md:order-2">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Custom Software Development</h2>
                <p class="text-gray-600 mb-6">
                    Tailored software solutions designed specifically for your business requirements:
                </p>
                <ul class="space-y-3 mb-6">
                    <li class="flex items-start">
                        <i class="fas fa-check text-purple-600 mr-3 mt-1"></i>
                        <span class="text-gray-700"><strong>Requirement Analysis:</strong> Understand your needs deeply</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-purple-600 mr-3 mt-1"></i>
                        <span class="text-gray-700"><strong>System Architecture:</strong> Scalable design patterns</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-purple-600 mr-3 mt-1"></i>
                        <span class="text-gray-700"><strong>Integration:</strong> Connect with existing systems</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-purple-600 mr-3 mt-1"></i>
                        <span class="text-gray-700"><strong>Deployment:</strong> Smooth go-live and support</span>
                    </li>
                </ul>
                <a href="<?php echo SITE_URL; ?>/pages/contact.php" class="btn-primary text-white font-medium py-2 px-6 rounded-lg inline-block">
                    Get a Quote <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
        </div>
        
        <!-- UI/UX Design -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
            <div>
                <h2 class="text-3xl font-bold text-gray-900 mb-4">UI/UX Design</h2>
                <p class="text-gray-600 mb-6">
                    Create beautiful and user-friendly interfaces that your users will love:
                </p>
                <ul class="space-y-3 mb-6">
                    <li class="flex items-start">
                        <i class="fas fa-check text-purple-600 mr-3 mt-1"></i>
                        <span class="text-gray-700"><strong>User Research:</strong> Understand your audience</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-purple-600 mr-3 mt-1"></i>
                        <span class="text-gray-700"><strong>Wireframing:</strong> Plan the structure</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-purple-600 mr-3 mt-1"></i>
                        <span class="text-gray-700"><strong>Visual Design:</strong> Beautiful prototypes</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-purple-600 mr-3 mt-1"></i>
                        <span class="text-gray-700"><strong>Usability Testing:</strong> Optimize for users</span>
                    </li>
                </ul>
                <a href="<?php echo SITE_URL; ?>/pages/contact.php" class="btn-primary text-white font-medium py-2 px-6 rounded-lg inline-block">
                    Get a Quote <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
            <div>
                <div class="bg-gradient-to-br from-purple-100 to-purple-50 rounded-lg p-12 h-80 flex items-center justify-center">
                    <i class="fas fa-paint-brush text-6xl text-purple-600"></i>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include '../app/views/footer.php'; ?>
