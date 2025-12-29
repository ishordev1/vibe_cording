<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Digital Tarai - Software Development Company'; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        html {
            scroll-behavior: smooth;
        }
        
        body {
            background-color: #ffffff;
        }
        
        .navbar {
            background-color: #1a1a1a;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .navbar a:hover {
            color: #a78bfa;
            transition: color 0.3s ease;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #8b5cf6 0%, #a78bfa 100%);
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(139, 92, 246, 0.3);
        }
        
        .btn-secondary {
            background-color: #333333;
            border: 2px solid #8b5cf6;
            color: #8b5cf6;
            transition: all 0.3s ease;
        }
        
        .btn-secondary:hover {
            background-color: #8b5cf6;
            color: white;
        }
        
        .footer {
            background-color: #1a1a1a;
            color: #e5e5e5;
        }
        
        .footer a:hover {
            color: #a78bfa;
        }
        
        .card {
            transition: all 0.3s ease;
            border: 1px solid #e5e5e5;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 24px rgba(0,0,0,0.1);
        }
        
        .hero-gradient {
            background: linear-gradient(135deg, #f5f5f5 0%, #ffffff 100%);
        }
        
        .accent-color {
            color: #8b5cf6;
        }
        
        .service-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #8b5cf6 0%, #a78bfa 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
        }
        
        @media (max-width: 768px) {
            .navbar-menu {
                display: none;
            }
            
            .navbar-menu.active {
                display: block;
                position: absolute;
                top: 70px;
                left: 0;
                right: 0;
                background-color: #1a1a1a;
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex-shrink-0">
                    <a href="<?php echo SITE_URL; ?>/index.php" class="text-white font-bold text-xl flex items-center">
                        <i class="fas fa-code mr-2 accent-color"></i> Digital Tarai
                    </a>
                </div>
                
                <!-- Desktop Menu -->
                <div class="hidden md:flex space-x-8">
                    <a href="<?php echo SITE_URL; ?>/index.php" class="text-gray-300 hover:text-white">Home</a>
                    <a href="<?php echo SITE_URL; ?>/pages/services.php" class="text-gray-300 hover:text-white">Services</a>
                    <a href="<?php echo SITE_URL; ?>/pages/how-it-works.php" class="text-gray-300 hover:text-white">How It Works</a>
                    <a href="<?php echo SITE_URL; ?>/pages/blog.php" class="text-gray-300 hover:text-white">Blog</a>
                    <a href="<?php echo SITE_URL; ?>/pages/careers.php" class="text-gray-300 hover:text-white">Careers</a>
                    <a href="<?php echo SITE_URL; ?>/pages/contact.php" class="text-gray-300 hover:text-white">Contact Us</a>
                </div>
                
                <!-- Login Button -->
                <div class="hidden md:flex space-x-4">
                    <?php if (isLoggedIn()): ?>
                        <div class="relative group">
                            <button class="text-gray-300 hover:text-white py-2 px-3 rounded-md text-sm font-medium">
                                <?php echo $_SESSION['user_name']; ?> <i class="fas fa-chevron-down ml-2"></i>
                            </button>
                            <div class="hidden group-hover:block absolute right-0 bg-gray-800 rounded-md shadow-lg">
                                <?php if ($_SESSION['user_type'] === 'admin'): ?>
                                    <a href="<?php echo SITE_URL; ?>/admin/dashboard.php" class="block px-4 py-2 text-sm text-gray-300 hover:text-white">Admin Dashboard</a>
                                <?php else: ?>
                                    <a href="<?php echo SITE_URL; ?>/student/dashboard.php" class="block px-4 py-2 text-sm text-gray-300 hover:text-white">My Dashboard</a>
                                <?php endif; ?>
                                <a href="<?php echo SITE_URL; ?>/auth.php?action=logout" class="block px-4 py-2 text-sm text-gray-300 hover:text-white border-t border-gray-700">Logout</a>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="<?php echo SITE_URL; ?>/auth.php?action=login" class="text-gray-300 hover:text-white py-2 px-3 rounded-md text-sm font-medium">Login</a>
                    <?php endif; ?>
                </div>
                
                <!-- Mobile menu button -->
                <div class="md:hidden">
                    <button onclick="toggleMobileMenu()" class="text-gray-300 hover:text-white">
                        <i class="fas fa-bars text-2xl"></i>
                    </button>
                </div>
            </div>
            
            <!-- Mobile Menu -->
            <div id="mobileMenu" class="navbar-menu md:hidden pb-4">
                <a href="<?php echo SITE_URL; ?>/index.php" class="block text-gray-300 hover:text-white py-2">Home</a>
                <a href="<?php echo SITE_URL; ?>/pages/services.php" class="block text-gray-300 hover:text-white py-2">Services</a>
                <a href="<?php echo SITE_URL; ?>/pages/how-it-works.php" class="block text-gray-300 hover:text-white py-2">How It Works</a>
                <a href="<?php echo SITE_URL; ?>/pages/blog.php" class="block text-gray-300 hover:text-white py-2">Blog</a>
                <a href="<?php echo SITE_URL; ?>/pages/careers.php" class="block text-gray-300 hover:text-white py-2">Careers</a>
                <a href="<?php echo SITE_URL; ?>/pages/contact.php" class="block text-gray-300 hover:text-white py-2">Contact Us</a>
                <?php if (!isLoggedIn()): ?>
                    <a href="<?php echo SITE_URL; ?>/auth.php?action=login" class="block text-gray-300 hover:text-white py-2">Login</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
