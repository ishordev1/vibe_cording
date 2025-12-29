    <!-- Footer -->
    <footer class="footer mt-20 py-12 border-t border-gray-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                <!-- Company Info -->
                <div>
                    <h4 class="text-white font-bold mb-4 flex items-center">
                        <i class="fas fa-code mr-2 accent-color"></i> Digital Tarai
                    </h4>
                    <p class="text-gray-400 text-sm mb-4">
                        Building innovative digital solutions for your ideas.
                    </p>
                    <p class="text-gray-400 text-sm">
                        <i class="fas fa-map-marker-alt mr-2 accent-color"></i>
                        Siraha, Nepal
                    </p>
                </div>
                
                <!-- Services -->
                <div>
                    <h4 class="text-white font-bold mb-4">Services</h4>
                    <ul class="space-y-2">
                        <li><a href="<?php echo SITE_URL; ?>/pages/services.php" class="text-gray-400 text-sm hover:text-white">Web Development</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/pages/services.php" class="text-gray-400 text-sm hover:text-white">Mobile Development</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/pages/services.php" class="text-gray-400 text-sm hover:text-white">Custom Software</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/pages/services.php" class="text-gray-400 text-sm hover:text-white">UI/UX Design</a></li>
                    </ul>
                </div>
                
                <!-- Quick Links -->
                <div>
                    <h4 class="text-white font-bold mb-4">Quick Links</h4>
                    <ul class="space-y-2">
                        <li><a href="<?php echo SITE_URL; ?>/pages/blog.php" class="text-gray-400 text-sm hover:text-white">Blog</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/pages/careers.php" class="text-gray-400 text-sm hover:text-white">Careers</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/pages/contact.php" class="text-gray-400 text-sm hover:text-white">Contact</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/auth.php?action=login" class="text-gray-400 text-sm hover:text-white">Login</a></li>
                    </ul>
                </div>
                
                <!-- Contact Info -->
                <div>
                    <h4 class="text-white font-bold mb-4">Contact Us</h4>
                    <p class="text-gray-400 text-sm mb-2">
                        <i class="fas fa-envelope mr-2 accent-color"></i>
                        <?php echo COMPANY_EMAIL; ?>
                    </p>
                    <p class="text-gray-400 text-sm mb-4">
                        <i class="fas fa-phone mr-2 accent-color"></i>
                        <?php echo COMPANY_PHONE; ?>
                    </p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-github"></i></a>
                    </div>
                </div>
            </div>
            
            <!-- Divider -->
            <div class="border-t border-gray-700 pt-8">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <p class="text-gray-400 text-sm">
                        &copy; <?php echo date('Y'); ?> Digital Tarai. All rights reserved.
                    </p>
                    <div class="flex space-x-4 mt-4 md:mt-0">
                        <a href="#" class="text-gray-400 text-sm hover:text-white">Privacy Policy</a>
                        <a href="#" class="text-gray-400 text-sm hover:text-white">Terms of Service</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script>
        function toggleMobileMenu() {
            const menu = document.getElementById('mobileMenu');
            menu.classList.toggle('active');
        }
        
        // Flash messages
        document.addEventListener('DOMContentLoaded', function() {
            const flashMessages = document.querySelectorAll('[data-flash]');
            flashMessages.forEach(msg => {
                setTimeout(() => {
                    msg.style.display = 'none';
                }, 5000);
            });
        });
    </script>
</body>
</html>
