<?php
$pageTitle = 'Contact Us - Digital Tarai';
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../lib/functions.php';
?>
<?php include '../app/views/header.php'; ?>

<div class="bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <h1 class="text-4xl font-bold text-gray-900 mb-4">Contact Us</h1>
        <p class="text-lg text-gray-600">Get in touch with our team. We're here to help.</p>
    </div>
</div>

<section class="py-20 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
            <!-- Contact Info -->
            <div class="space-y-8">
                <!-- Email -->
                <div class="bg-white rounded-lg shadow-md p-8">
                    <div class="flex items-center mb-4">
                        <div class="flex items-center justify-center w-12 h-12 rounded-full bg-purple-100 text-purple-600">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 ml-4">Email</h3>
                    </div>
                    <p class="text-gray-600">
                        <?php echo COMPANY_EMAIL; ?>
                    </p>
                    <p class="text-gray-600 text-sm mt-2">We'll respond within 24 hours</p>
                </div>
                
                <!-- Phone -->
                <div class="bg-white rounded-lg shadow-md p-8">
                    <div class="flex items-center mb-4">
                        <div class="flex items-center justify-center w-12 h-12 rounded-full bg-purple-100 text-purple-600">
                            <i class="fas fa-phone"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 ml-4">Phone</h3>
                    </div>
                    <p class="text-gray-600">
                        <?php echo COMPANY_PHONE; ?>
                    </p>
                    <p class="text-gray-600 text-sm mt-2">Monday to Friday, 9 AM - 6 PM</p>
                </div>
                
                <!-- Location -->
                <div class="bg-white rounded-lg shadow-md p-8">
                    <div class="flex items-center mb-4">
                        <div class="flex items-center justify-center w-12 h-12 rounded-full bg-purple-100 text-purple-600">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 ml-4">Office</h3>
                    </div>
                    <p class="text-gray-600">
                        <?php echo COMPANY_LOCATION; ?>
                    </p>
                    <p class="text-gray-600 text-sm mt-2">Nepal</p>
                </div>
            </div>
            
            <!-- Contact Form -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-md p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Send Us a Message</h2>
                    
                    <form class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Name -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                                <input 
                                    type="text" 
                                    required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                    placeholder="Your name"
                                >
                            </div>
                            
                            <!-- Email -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                                <input 
                                    type="email" 
                                    required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                    placeholder="your@email.com"
                                >
                            </div>
                        </div>
                        
                        <!-- Phone -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                            <input 
                                type="tel" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                placeholder="Your phone number"
                            >
                        </div>
                        
                        <!-- Subject -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Subject</label>
                            <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                <option>Select a subject</option>
                                <option>General Inquiry</option>
                                <option>Web Development</option>
                                <option>Mobile Development</option>
                                <option>Custom Software</option>
                                <option>UI/UX Design</option>
                                <option>Internship Inquiry</option>
                                <option>Partnership</option>
                                <option>Other</option>
                            </select>
                        </div>
                        
                        <!-- Message -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Message</label>
                            <textarea 
                                rows="5"
                                required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                placeholder="Your message..."
                            ></textarea>
                        </div>
                        
                        <!-- Checkbox -->
                        <div class="flex items-start">
                            <input type="checkbox" id="privacy" class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded mt-1">
                            <label for="privacy" class="ml-2 block text-sm text-gray-700">
                                I agree to the privacy policy and terms of service
                            </label>
                        </div>
                        
                        <!-- Submit Button -->
                        <button 
                            type="submit" 
                            class="w-full btn-primary text-white font-medium py-3 rounded-lg"
                        >
                            <i class="fas fa-paper-plane mr-2"></i>Send Message
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include '../app/views/footer.php'; ?>
