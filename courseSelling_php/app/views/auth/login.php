<?php
$pageTitle = 'Login - Digital Tarai';
?>
<?php include 'app/views/header.php'; ?>

<div class="min-h-screen flex items-center justify-center bg-gray-100 py-12 px-4">
    <div class="max-w-md w-full">
        <!-- Flash Message -->
        <?php
        $errorMsg = getFlashMessage('error');
        $successMsg = getFlashMessage('success');
        ?>
        
        <?php if ($errorMsg): ?>
            <div data-flash="error" class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                <i class="fas fa-exclamation-circle mr-2"></i><?php echo $errorMsg; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($successMsg): ?>
            <div data-flash="success" class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                <i class="fas fa-check-circle mr-2"></i><?php echo $successMsg; ?>
            </div>
        <?php endif; ?>
        
        <!-- Login Card -->
        <div class="bg-white rounded-lg shadow-md p-8">
            <div class="text-center mb-8">
                <h1 class="text-2xl font-bold text-gray-900">
                    <i class="fas fa-code text-purple-600 mr-2"></i>Digital Tarai
                </h1>
                <p class="text-gray-600 mt-2">Welcome Back</p>
            </div>
            
            <form method="POST" action="<?php echo SITE_URL; ?>/auth.php?action=handle-login" class="space-y-4">
                <!-- Email -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                    <input 
                        type="email" 
                        name="email" 
                        required 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                        placeholder="Enter your email"
                    >
                </div>
                
                <!-- Password -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                    <input 
                        type="password" 
                        name="password" 
                        required 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                        placeholder="Enter your password"
                    >
                </div>
                
                <!-- Remember Me -->
                <div class="flex items-center">
                    <input type="checkbox" id="remember" name="remember" class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                    <label for="remember" class="ml-2 block text-sm text-gray-700">Remember me</label>
                </div>
                
                <!-- Login Button -->
                <button 
                    type="submit" 
                    class="w-full btn-primary text-white font-medium py-2 rounded-lg mt-6"
                >
                    <i class="fas fa-sign-in-alt mr-2"></i>Login
                </button>
            </form>
            
            <!-- Divider -->
            <div class="relative my-6">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-300"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-2 bg-white text-gray-500">Don't have an account?</span>
                </div>
            </div>
            
            <!-- Register Link -->
            <a 
                href="<?php echo SITE_URL; ?>/auth.php?action=register" 
                class="w-full btn-secondary text-center font-medium py-2 rounded-lg block"
            >
                <i class="fas fa-user-plus mr-2"></i>Create Account
            </a>
            
            <!-- Demo Credentials -->
            <div class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                <p class="text-xs font-semibold text-blue-900 mb-2">📝 Demo Credentials:</p>
                <p class="text-xs text-blue-800 mb-1"><strong>Admin:</strong> admin@digitaltarai.com / admin123</p>
                <p class="text-xs text-blue-800"><strong>Student:</strong> ram@example.com / student123</p>
            </div>
        </div>
    </div>
</div>

<?php include 'app/views/footer.php'; ?>
