<?php
/**
 * DIGITAL TARAI - SOFTWARE DEVELOPMENT COMPANY WEBSITE
 * 
 * A professional, production-ready startup website with:
 * - Modern responsive design
 * - Complete MVC structure
 * - Student internship system
 * - Admin management dashboard
 * - Payment verification system
 * - Blog platform
 * - MySQL database
 * - PHP sessions authentication
 * 
 * @author Digital Tarai Team
 * @version 1.0.0
 * @license MIT
 */

/**
 * ==========================================
 * PROJECT STRUCTURE
 * ==========================================
 */

/*
DigitalTarai/
│
├── config/                 # Configuration files
│   ├── config.php         # Main configuration
│   └── database.php       # Database connection
│
├── app/                   # Application code
│   ├── controllers/
│   │   └── AuthController.php   # Authentication logic
│   ├── models/
│   │   ├── User.php       # User model
│   │   ├── Internship.php # Internship model
│   │   ├── Application.php# Application model
│   │   ├── Payment.php    # Payment model
│   │   └── Blog.php       # Blog model
│   └── views/
│       ├── header.php     # Navigation header
│       ├── footer.php     # Footer
│       ├── auth/
│       │   ├── login.php  # Login page
│       │   └── register.php # Registration page
│       └── pages/         # Content pages
│
├── lib/
│   └── functions.php      # Helper functions
│
├── public/
│   ├── css/              # Stylesheets
│   ├── js/               # JavaScript files
│   ├── uploads/          # User uploads
│   └── downloads/        # Downloadable files
│
├── student/
│   ├── dashboard.php     # Student dashboard
│   └── application-detail.php
│
├── admin/
│   ├── dashboard.php     # Admin dashboard
│   ├── application-detail.php
│   └── verify-payment.php
│
├── pages/
│   ├── services.php
│   ├── how-it-works.php
│   ├── blog.php
│   ├── blog-detail.php
│   ├── careers.php
│   ├── internship-detail.php
│   ├── internship-apply.php
│   ├── internship-payment.php
│   └── contact.php
│
├── index.php             # Homepage
├── auth.php              # Authentication routes
├── setup.php             # Database setup
├── .htaccess             # URL rewriting
├── README.md             # Documentation
├── QUICKSTART.txt        # Quick start guide
└── INSTALLATION.md       # Installation guide
*/

/**
 * ==========================================
 * FEATURES & FUNCTIONALITY
 * ==========================================
 */

/*
1. FRONTEND PAGES
   - Home Page: Hero, services, why choose us, blog preview, contact form
   - Services: Web Dev, Mobile Dev, Custom Software, UI/UX Design
   - How It Works: 4-step process visualization
   - Blog: Listing and detail pages with categories
   - Careers: Internship showcase with FAQs
   - Contact: Contact form and company info

2. AUTHENTICATION
   - Student Registration: Name, email, phone, password
   - Student Login: Email & password
   - Admin Login: Separate admin login
   - PHP Sessions: Secure session management
   - Password Hashing: bcrypt with password_hash()
   - CSRF Protection: Token generation (ready to use)

3. INTERNSHIP SYSTEM
   - Browse Internships: View all available programs
   - Internship Details: Skills, duration, fees, modules
   - Application: Submit application
   - Payment Upload: Screenshot upload
   - Status Tracking: View application status
   - Module Progress: Track course completion
   - Certificate: Auto-unlock on module completion

4. ADMIN SYSTEM
   - Dashboard: View all applications & stats
   - Applications: Approve/reject applications
   - Payments: Verify payment screenshots
   - Offer Letters: Generate (template ready)
   - Activity Logs: Track user activities

5. BLOG SYSTEM
   - Write & Publish: Create blog posts
   - Categories: Organize posts
   - Pagination: Browse multiple pages
   - View Count: Track reader interest
   - Author Info: Display author details

6. RESPONSIVE DESIGN
   - Mobile First: Optimized for mobile
   - Tablet Ready: Works on tablets
   - Desktop Optimized: Full features on desktop
   - Touch Friendly: Easy navigation
   - Dark Mode Ready: Can be added

7. SECURITY FEATURES
   - CSRF Tokens: Ready to implement
   - SQL Injection Prevention: Parameterized queries
   - File Upload Validation: Type & size checks
   - Password Hashing: bcrypt algorithm
   - Session Timeout: Configurable
   - Activity Logging: Track actions

8. DATABASE SCHEMA
   - Users (students & admins)
   - Internships (programs)
   - Applications (student applications)
   - Payments (payment records)
   - Modules (course content)
   - Module Progress (completion tracking)
   - Blogs (blog posts)
   - Activity Logs (audit trail)
*/

/**
 * ==========================================
 * HOW TO USE
 * ==========================================
 */

/*
1. INSTALLATION
   - Extract files to: c:/xampp/htdocs/ai/DigitalTarai/
   - Open: http://localhost/ai/DigitalTarai/setup.php
   - Wait for database creation

2. LOGIN
   Admin:   admin@digitaltarai.com / admin123
   Student: ram@example.com / student123

3. BROWSE PAGES
   - Public can view: Home, Services, How It Works, Blog, Careers, Contact
   - Students see: Apply buttons, Dashboard
   - Admin sees: Management panels

4. CREATE INTERNSHIP APPLICATION
   - Login as student
   - Go to Careers
   - Click "View Details"
   - Click "Apply Now"
   - Upload payment screenshot
   - Login as admin to verify

5. MANAGE PAYMENTS
   - Login as admin
   - Go to Dashboard
   - Click "Pending Payments" tab
   - Review screenshots
   - Click "Verify" to approve
*/

/**
 * ==========================================
 * CUSTOMIZATION
 * ==========================================
 */

/*
1. CHANGE COMPANY INFO
   Edit: /config/config.php
   - COMPANY_NAME
   - COMPANY_EMAIL
   - COMPANY_PHONE
   - COMPANY_LOCATION
   - SITE_URL

2. CHANGE COLORS
   Edit: /app/views/header.php (CSS section)
   - Primary: #8b5cf6 (purple)
   - Background: #ffffff (white)
   - Navbar: #1a1a1a (black)
   - Accent: #a78bfa (light purple)

3. ADD INTERNSHIPS
   Method 1: Direct SQL
   INSERT INTO internships VALUES (...)

   Method 2: Admin Interface
   Create admin panel form

4. ADD BLOG POSTS
   Method 1: Direct SQL
   INSERT INTO blogs VALUES (...)

   Method 2: Admin Dashboard
   Create blog editor interface

5. CUSTOMIZE PAGES
   Edit HTML/CSS in:
   - /pages/services.php
   - /pages/how-it-works.php
   - /app/views/header.php
   - /app/views/footer.php
*/

/**
 * ==========================================
 * DATABASE TABLES
 * ==========================================
 */

/*
users
- id (INT, PRIMARY KEY)
- name (VARCHAR)
- email (VARCHAR, UNIQUE)
- password (VARCHAR)
- phone (VARCHAR)
- user_type (ENUM: 'student', 'admin')
- status (ENUM: 'active', 'inactive')
- created_at, updated_at

internships
- id (INT, PRIMARY KEY)
- title (VARCHAR)
- slug (VARCHAR, UNIQUE)
- description (TEXT)
- skills (TEXT)
- duration (VARCHAR)
- fees (DECIMAL)
- benefits (TEXT)
- created_at, updated_at

applications
- id (INT, PRIMARY KEY)
- user_id (INT, FOREIGN KEY)
- internship_id (INT, FOREIGN KEY)
- status (ENUM: 'pending', 'approved', 'rejected')
- created_at, updated_at

payments
- id (INT, PRIMARY KEY)
- application_id (INT, FOREIGN KEY)
- amount (DECIMAL)
- payment_method (VARCHAR)
- screenshot (VARCHAR)
- status (ENUM: 'pending', 'verified', 'rejected')
- verified_by (INT, FOREIGN KEY)
- verified_at (DATETIME)
- created_at, updated_at

modules
- id (INT, PRIMARY KEY)
- internship_id (INT, FOREIGN KEY)
- title (VARCHAR)
- description (TEXT)
- module_file (VARCHAR)
- order_seq (INT)
- created_at

student_module_progress
- id (INT, PRIMARY KEY)
- application_id (INT, FOREIGN KEY)
- module_id (INT, FOREIGN KEY)
- completed (BOOLEAN)
- completed_at (DATETIME)
- created_at

blogs
- id (INT, PRIMARY KEY)
- title (VARCHAR)
- slug (VARCHAR, UNIQUE)
- content (LONGTEXT)
- author_id (INT, FOREIGN KEY)
- featured_image (VARCHAR)
- category (VARCHAR)
- status (ENUM: 'published', 'draft')
- views (INT)
- created_at, updated_at

activity_logs
- id (INT, PRIMARY KEY)
- action (VARCHAR)
- user_id (INT, FOREIGN KEY)
- details (TEXT)
- created_at
*/

/**
 * ==========================================
 * KEY FUNCTIONS & HELPERS
 * ==========================================
 */

/*
From /lib/functions.php:

sanitize($data)
- Sanitizes user input
- Prevents XSS attacks

validateEmail($email)
- Validates email format

hashPassword($password)
- Hashes password with bcrypt

verifyPassword($password, $hash)
- Verifies password against hash

isLoggedIn()
- Checks if user is logged in

isAdmin()
- Checks if logged-in user is admin

redirect($url)
- Redirects to page

getFileExtension($filename)
- Gets file extension

isValidFileUpload($file)
- Validates file upload

generateUniqueFilename($originalName)
- Creates unique filename

formatDate($date)
- Formats date for display

formatCurrency($amount)
- Formats currency (NPR)

truncateText($text, $length)
- Truncates text to length

generateSlug($title)
- Creates URL-friendly slug

emailExists($email)
- Checks if email exists

getFlashMessage($key)
- Gets flash message

setFlashMessage($key, $message)
- Sets flash message

generateCSRFToken()
- Generates CSRF token

validateCSRFToken($token)
- Validates CSRF token

logActivity($action, $userId, $details)
- Logs user activity
*/

/**
 * ==========================================
 * MODEL CLASSES
 * ==========================================
 */

/*
User Model (/app/models/User.php)
- register()
- login()
- getByEmail()
- getById()
- updateProfile()
- changePassword()

Internship Model (/app/models/Internship.php)
- getAll()
- getById()
- getBySlug()
- create()
- update()
- delete()
- getModules()
- addModule()

Application Model (/app/models/Application.php)
- create()
- getById()
- getByUserId()
- getAll()
- updateStatus()
- getWithPayment()

Payment Model (/app/models/Payment.php)
- create()
- getById()
- getByApplicationId()
- uploadScreenshot()
- getPendingPayments()
- verifyPayment()
- rejectPayment()

Blog Model (/app/models/Blog.php)
- getAll()
- getByIdOrSlug()
- getTotalCount()
- create()
- update()
- delete()
- getByCategory()
*/

/**
 * ==========================================
 * INTERNSHIP APPLICATION FLOW
 * ==========================================
 */

/*
1. STUDENT VIEWS INTERNSHIP
   - Goes to /pages/careers.php
   - Clicks "View Details"
   - Sees internship details
   - If logged out: sees "Login to Apply"
   - If logged in: sees "Apply Now"

2. STUDENT APPLIES
   - Clicks "Apply Now"
   - Sees application summary
   - Confirms application
   - Redirected to payment page

3. STUDENT UPLOADS PAYMENT
   - Uploads screenshot of payment
   - Enters payment reference (optional)
   - Clicks "Upload"
   - Status: Pending verification
   - Redirected to dashboard

4. ADMIN VERIFIES PAYMENT
   - Logs in as admin
   - Goes to Dashboard
   - Clicks "Pending Payments" tab
   - Reviews screenshot
   - Clicks "Verify & Approve" or "Reject"

5. ON APPROVAL
   - Application status: Approved
   - Offer letter available
   - Course modules unlocked
   - Student gets notification (ready to add)

6. STUDENT ACCESSES COURSE
   - Sees modules in dashboard
   - Checks off completed modules
   - When all completed: Certificate available
   - Can download certificate & offer letter
*/

/**
 * ==========================================
 * DEPLOYMENT CHECKLIST
 * ==========================================
 */

/*
[ ] Database setup completed
[ ] All files uploaded to server
[ ] Database credentials configured
[ ] .htaccess properly configured
[ ] Folder permissions set (uploads folder)
[ ] SSL certificate installed
[ ] Email notifications configured
[ ] Payment gateway integrated
[ ] Admin account created
[ ] Logo and branding updated
[ ] Company info customized
[ ] Blog posts created
[ ] Internship programs added
[ ] Testing completed
[ ] Go-live ready
*/

/**
 * ==========================================
 * PERFORMANCE OPTIMIZATION
 * ==========================================
 */

/*
Currently implemented:
- Minimal CSS/JS
- Optimized database queries
- Session management
- File caching headers
- Mobile-first design

Recommendations:
- Add CDN for static files
- Enable GZIP compression
- Cache database queries
- Optimize images
- Minify CSS/JS
- Use lazy loading
- Add service worker for offline
- Implement Redis for sessions
*/

/**
 * ==========================================
 * FUTURE ENHANCEMENTS
 * ==========================================
 */

/*
Phase 2:
[ ] Email notifications
[ ] SMS notifications
[ ] Payment gateway integration (Khalti, Esewa)
[ ] Certificate PDF generation
[ ] Video course support
[ ] Student testimonials
[ ] Job board
[ ] Social login

Phase 3:
[ ] Mobile app
[ ] Live chat support
[ ] Analytics dashboard
[ ] Advanced search
[ ] Course recommendations
[ ] Discussion forums
[ ] Mentorship matching

Phase 4:
[ ] Marketplace
[ ] Freelance jobs
[ ] Portfolio system
[ ] Skill verification
[ ] Partnerships
[ ] Enterprise features
*/

/**
 * ==========================================
 * TROUBLESHOOTING
 * ==========================================
 */

/*
Issue: Database connection error
Solution: Check MySQL is running, verify credentials in config/database.php

Issue: Login not working
Solution: Clear cookies, ensure users table has data, check password hash

Issue: File upload fails
Solution: Check uploads folder permissions, verify file size limit

Issue: Pages show blank/errors
Solution: Check error log, ensure PHP errors are enabled, verify file paths

Issue: Redirects not working
Solution: Check .htaccess is enabled, verify SITE_URL in config.php

Issue: Modules not showing
Solution: Ensure modules are added to database, check internship_id

Issue: Payment screenshot not displaying
Solution: Check file exists in public/uploads/, verify file permissions

Issue: Admin dashboard slow
Solution: Check database indexes, optimize queries, add pagination
*/

/**
 * ==========================================
 * SUPPORT & CONTACT
 * ==========================================
 */

/*
Email: info@digitaltarai.com
Phone: +977-1-xxxxxxxx
Location: Siraha, Nepal
Website: www.digitaltarai.com

Documentation: See README.md
Quick Start: See QUICKSTART.txt
*/

/**
 * ==========================================
 * END OF DOCUMENTATION
 * ==========================================
 * 
 * Built with PHP 7.4+, MySQL 5.7+, Tailwind CSS 3
 * Licensed under MIT
 * 
 * Version: 1.0.0
 * Last Updated: December 2024
 */
?>
