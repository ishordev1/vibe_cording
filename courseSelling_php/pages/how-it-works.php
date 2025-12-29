<?php
$pageTitle = 'How It Works - Digital Tarai';
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../lib/functions.php';
?>
<?php include '../app/views/header.php'; ?>

<div class="bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <h1 class="text-4xl font-bold text-gray-900 mb-4">How It Works</h1>
        <p class="text-lg text-gray-600">Our simple and transparent project process</p>
    </div>
</div>

<section class="py-20 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-12">
            <!-- Step 1 -->
            <div class="relative">
                <div class="bg-white rounded-lg shadow-md p-8 text-center h-full">
                    <div class="flex items-center justify-center w-16 h-16 mx-auto mb-4 rounded-full bg-purple-600 text-white text-2xl font-bold">
                        1
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Submit Your Idea</h3>
                    <p class="text-gray-600">
                        Tell us about your project and what you want to build. Share your vision and requirements with us.
                    </p>
                </div>
                <div class="hidden md:block absolute top-1/2 -right-4 w-8 h-0.5 bg-purple-600"></div>
            </div>
            
            <!-- Step 2 -->
            <div class="relative">
                <div class="bg-white rounded-lg shadow-md p-8 text-center h-full">
                    <div class="flex items-center justify-center w-16 h-16 mx-auto mb-4 rounded-full bg-purple-600 text-white text-2xl font-bold">
                        2
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Requirement Discussion</h3>
                    <p class="text-gray-600">
                        Our team meets with you to understand requirements, goals, timeline, and budget in detail.
                    </p>
                </div>
                <div class="hidden md:block absolute top-1/2 -right-4 w-8 h-0.5 bg-purple-600"></div>
            </div>
            
            <!-- Step 3 -->
            <div class="relative">
                <div class="bg-white rounded-lg shadow-md p-8 text-center h-full">
                    <div class="flex items-center justify-center w-16 h-16 mx-auto mb-4 rounded-full bg-purple-600 text-white text-2xl font-bold">
                        3
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Development & Updates</h3>
                    <p class="text-gray-600">
                        We develop your project with regular updates, progress reports, and your feedback incorporated.
                    </p>
                </div>
                <div class="hidden md:block absolute top-1/2 -right-4 w-8 h-0.5 bg-purple-600"></div>
            </div>
            
            <!-- Step 4 -->
            <div>
                <div class="bg-white rounded-lg shadow-md p-8 text-center h-full">
                    <div class="flex items-center justify-center w-16 h-16 mx-auto mb-4 rounded-full bg-purple-600 text-white text-2xl font-bold">
                        4
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Final Delivery & Support</h3>
                    <p class="text-gray-600">
                        Your project is delivered, deployed, and we provide ongoing support and maintenance.
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Detailed Steps -->
        <div class="space-y-12 mt-20">
            <!-- Step 1 Detailed -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
                <div>
                    <div class="bg-gradient-to-br from-purple-100 to-purple-50 rounded-lg p-12 h-80 flex items-center justify-center">
                        <i class="fas fa-lightbulb text-6xl text-purple-600"></i>
                    </div>
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Step 1: Submit Your Idea</h3>
                    <p class="text-gray-600 mb-4">
                        Start by sharing your business idea with us. Tell us what problem you're solving, who your users are, and what you want to achieve.
                    </p>
                    <ul class="space-y-3">
                        <li class="flex items-start">
                            <i class="fas fa-arrow-right text-purple-600 mr-3 mt-1"></i>
                            <span class="text-gray-700">Describe your project vision</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-arrow-right text-purple-600 mr-3 mt-1"></i>
                            <span class="text-gray-700">Share your target audience</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-arrow-right text-purple-600 mr-3 mt-1"></i>
                            <span class="text-gray-700">Define your success metrics</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- Step 2 Detailed -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
                <div class="order-2 md:order-1">
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Step 2: Requirement Discussion</h3>
                    <p class="text-gray-600 mb-4">
                        We conduct detailed discussions to understand every aspect of your project and create a comprehensive plan.
                    </p>
                    <ul class="space-y-3">
                        <li class="flex items-start">
                            <i class="fas fa-arrow-right text-purple-600 mr-3 mt-1"></i>
                            <span class="text-gray-700">Detailed requirement analysis</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-arrow-right text-purple-600 mr-3 mt-1"></i>
                            <span class="text-gray-700">Technology stack selection</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-arrow-right text-purple-600 mr-3 mt-1"></i>
                            <span class="text-gray-700">Timeline and budget estimation</span>
                        </li>
                    </ul>
                </div>
                <div class="order-1 md:order-2">
                    <div class="bg-gradient-to-br from-purple-100 to-purple-50 rounded-lg p-12 h-80 flex items-center justify-center">
                        <i class="fas fa-comments text-6xl text-purple-600"></i>
                    </div>
                </div>
            </div>
            
            <!-- Step 3 Detailed -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
                <div>
                    <div class="bg-gradient-to-br from-purple-100 to-purple-50 rounded-lg p-12 h-80 flex items-center justify-center">
                        <i class="fas fa-code text-6xl text-purple-600"></i>
                    </div>
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Step 3: Development & Regular Updates</h3>
                    <p class="text-gray-600 mb-4">
                        Our team works on your project with transparency and regular communication to keep you updated.
                    </p>
                    <ul class="space-y-3">
                        <li class="flex items-start">
                            <i class="fas fa-arrow-right text-purple-600 mr-3 mt-1"></i>
                            <span class="text-gray-700">Agile development methodology</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-arrow-right text-purple-600 mr-3 mt-1"></i>
                            <span class="text-gray-700">Weekly progress updates</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-arrow-right text-purple-600 mr-3 mt-1"></i>
                            <span class="text-gray-700">Your feedback implementation</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- Step 4 Detailed -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
                <div class="order-2 md:order-1">
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Step 4: Final Delivery & Support</h3>
                    <p class="text-gray-600 mb-4">
                        Your project is tested, deployed, and we provide continuous support to ensure success.
                    </p>
                    <ul class="space-y-3">
                        <li class="flex items-start">
                            <i class="fas fa-arrow-right text-purple-600 mr-3 mt-1"></i>
                            <span class="text-gray-700">Quality assurance testing</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-arrow-right text-purple-600 mr-3 mt-1"></i>
                            <span class="text-gray-700">Deployment and launch</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-arrow-right text-purple-600 mr-3 mt-1"></i>
                            <span class="text-gray-700">24/7 post-launch support</span>
                        </li>
                    </ul>
                </div>
                <div class="order-1 md:order-2">
                    <div class="bg-gradient-to-br from-purple-100 to-purple-50 rounded-lg p-12 h-80 flex items-center justify-center">
                        <i class="fas fa-rocket text-6xl text-purple-600"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="bg-gray-50 py-20 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto text-center">
        <h2 class="text-3xl font-bold text-gray-900 mb-4">Ready to Get Started?</h2>
        <p class="text-lg text-gray-600 mb-8">
            Let's discuss your project and turn your idea into reality.
        </p>
        <a href="<?php echo SITE_URL; ?>/pages/contact.php" class="btn-primary text-white font-medium py-3 px-8 rounded-lg inline-block">
            <i class="fas fa-envelope mr-2"></i>Contact Us Now
        </div>
    </div>
</section>

<?php include '../app/views/footer.php'; ?>
