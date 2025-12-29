<?php
$pageTitle = 'Careers & Internships - Digital Tarai';
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../lib/functions.php';
require_once '../app/models/Internship.php';

$internshipModel = new Internship($conn);
$internships = $internshipModel->getAll();
?>
<?php include '../app/views/header.php'; ?>

<div class="bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <h1 class="text-4xl font-bold text-gray-900 mb-4">Careers & Internships</h1>
        <p class="text-lg text-gray-600">Start your career with Digital Tarai. Gain real-world experience in software development.</p>
    </div>
</div>

<!-- Why Internship Section -->
<section class="py-20 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <h2 class="text-3xl font-bold text-center text-gray-900 mb-12">Why Intern at Digital Tarai?</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-20">
            <div class="text-center p-8">
                <div class="text-5xl text-purple-600 mb-4">
                    <i class="fas fa-laptop-code"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Real Projects</h3>
                <p class="text-gray-600">
                    Work on actual client projects and build real-world experience that matters.
                </p>
            </div>
            
            <div class="text-center p-8">
                <div class="text-5xl text-purple-600 mb-4">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Learn from Experts</h3>
                <p class="text-gray-600">
                    Get mentorship from experienced developers and industry professionals.
                </p>
            </div>
            
            <div class="text-center p-8">
                <div class="text-5xl text-purple-600 mb-4">
                    <i class="fas fa-certificate"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Certifications</h3>
                <p class="text-gray-600">
                    Earn recognized certificates and letters of recommendation.
                </p>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="text-center p-8">
                <div class="text-5xl text-purple-600 mb-4">
                    <i class="fas fa-briefcase"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Job Opportunities</h3>
                <p class="text-gray-600">
                    Outstanding interns may get full-time job offers from our company.
                </p>
            </div>
            
            <div class="text-center p-8">
                <div class="text-5xl text-purple-600 mb-4">
                    <i class="fas fa-network-wired"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Network Building</h3>
                <p class="text-gray-600">
                    Connect with like-minded professionals and build lasting relationships.
                </p>
            </div>
            
            <div class="text-center p-8">
                <div class="text-5xl text-purple-600 mb-4">
                    <i class="fas fa-rocket"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Career Boost</h3>
                <p class="text-gray-600">
                    Kickstart your tech career with practical skills and industry connections.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Available Internships -->
<section class="py-20 px-4 sm:px-6 lg:px-8 bg-gray-50">
    <div class="max-w-7xl mx-auto">
        <h2 class="text-3xl font-bold text-center text-gray-900 mb-12">Available Internships</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <?php while ($internship = $internships->fetch_assoc()): ?>
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
                    <div class="bg-gradient-to-r from-purple-600 to-purple-800 p-8 text-white">
                        <h3 class="text-2xl font-bold mb-2"><?php echo htmlspecialchars($internship['title']); ?></h3>
                        <p class="text-purple-100"><?php echo truncateText($internship['description'], 100); ?></p>
                    </div>
                    
                    <div class="p-8">
                        <!-- Duration -->
                        <div class="mb-6 pb-6 border-b border-gray-200">
                            <p class="text-sm text-gray-600 mb-2"><i class="fas fa-clock text-purple-600 mr-2"></i>Duration</p>
                            <p class="text-lg font-semibold text-gray-900"><?php echo htmlspecialchars($internship['duration']); ?></p>
                        </div>
                        
                        <!-- Fees -->
                        <div class="mb-6 pb-6 border-b border-gray-200">
                            <p class="text-sm text-gray-600 mb-2"><i class="fas fa-money-bill text-purple-600 mr-2"></i>Program Fee</p>
                            <p class="text-lg font-semibold text-gray-900"><?php echo formatCurrency($internship['fees']); ?></p>
                        </div>
                        
                        <!-- Skills -->
                        <div class="mb-6">
                            <p class="text-sm text-gray-600 mb-2"><i class="fas fa-star text-purple-600 mr-2"></i>Required Skills</p>
                            <p class="text-gray-700 text-sm"><?php echo htmlspecialchars($internship['skills']); ?></p>
                        </div>
                        
                        <!-- CTA Button -->
                        <a href="<?php echo SITE_URL; ?>/pages/internship-detail.php?slug=<?php echo urlencode($internship['slug']); ?>" class="w-full btn-primary text-white font-medium py-3 rounded-lg text-center block">
                            <i class="fas fa-arrow-right mr-2"></i>View Details
                        </a>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</section>

<!-- Benefits Section -->
<section class="py-20 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <h2 class="text-3xl font-bold text-center text-gray-900 mb-12">What You'll Get</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div class="bg-white p-8 rounded-lg text-center border border-gray-200 hover:border-purple-600 transition">
                <div class="text-4xl text-purple-600 mb-4">
                    <i class="fas fa-file-contract"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">Offer Letter</h3>
                <p class="text-gray-600 text-sm">Official offer letter to showcase your internship.</p>
            </div>
            
            <div class="bg-white p-8 rounded-lg text-center border border-gray-200 hover:border-purple-600 transition">
                <div class="text-4xl text-purple-600 mb-4">
                    <i class="fas fa-book"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">Course Modules</h3>
                <p class="text-gray-600 text-sm">Comprehensive learning materials and modules.</p>
            </div>
            
            <div class="bg-white p-8 rounded-lg text-center border border-gray-200 hover:border-purple-600 transition">
                <div class="text-4xl text-purple-600 mb-4">
                    <i class="fas fa-award"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">Certificate</h3>
                <p class="text-gray-600 text-sm">Recognized certificate upon completion.</p>
            </div>
            
            <div class="bg-white p-8 rounded-lg text-center border border-gray-200 hover:border-purple-600 transition">
                <div class="text-4xl text-purple-600 mb-4">
                    <i class="fas fa-thumbs-up"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">Recommendation</h3>
                <p class="text-gray-600 text-sm">Letter of recommendation for your career.</p>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="py-20 px-4 sm:px-6 lg:px-8 bg-gray-50">
    <div class="max-w-4xl mx-auto">
        <h2 class="text-3xl font-bold text-center text-gray-900 mb-12">Frequently Asked Questions</h2>
        
        <div class="space-y-6">
            <!-- FAQ 1 -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-3 flex items-center">
                    <i class="fas fa-question-circle text-purple-600 mr-3"></i>
                    What are the eligibility requirements?
                </h3>
                <p class="text-gray-600">
                    You should have basic knowledge of programming and be passionate about learning. No specific degree required, just enthusiasm and dedication.
                </p>
            </div>
            
            <!-- FAQ 2 -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-3 flex items-center">
                    <i class="fas fa-question-circle text-purple-600 mr-3"></i>
                    Is this internship paid?
                </h3>
                <p class="text-gray-600">
                    Our internships are not paid positions, but they are highly valuable for gaining experience. We charge a nominal fee for access to course materials and certification.
                </p>
            </div>
            
            <!-- FAQ 3 -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-3 flex items-center">
                    <i class="fas fa-question-circle text-purple-600 mr-3"></i>
                    Can I do this internship remotely?
                </h3>
                <p class="text-gray-600">
                    Yes! Our internship program is fully remote. You can work from anywhere and communicate with our team online.
                </p>
            </div>
            
            <!-- FAQ 4 -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-3 flex items-center">
                    <i class="fas fa-question-circle text-purple-600 mr-3"></i>
                    What happens after the internship?
                </h3>
                <p class="text-gray-600">
                    Upon successful completion, you'll receive a certificate, offer letter, and letter of recommendation. Outstanding interns may be offered full-time positions.
                </p>
            </div>
        </div>
    </div>
</section>

<?php include '../app/views/footer.php'; ?>
