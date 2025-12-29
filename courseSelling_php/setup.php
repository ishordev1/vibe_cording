<?php
/**
 * Database Setup - Run this file once to initialize the database
 * Access: http://localhost/ai/DigitalTarai/setup.php
 */

require_once 'config/database.php';
require_once 'lib/functions.php';

// Drop existing database and create new one
$dbCreation = "CREATE DATABASE IF NOT EXISTS digital_tarai";
if ($conn->query($dbCreation) === TRUE) {
    echo "Database created successfully<br>";
} else {
    echo "Error creating database: " . $conn->error . "<br>";
}

$conn->select_db('digital_tarai');

// Create tables
$tables = [
    // Users table
    "CREATE TABLE IF NOT EXISTS users (
        id INT PRIMARY KEY AUTO_INCREMENT,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        phone VARCHAR(20),
        user_type ENUM('student', 'admin') DEFAULT 'student',
        status ENUM('active', 'inactive') DEFAULT 'active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )",

    // Internships table
    "CREATE TABLE IF NOT EXISTS internships (
        id INT PRIMARY KEY AUTO_INCREMENT,
        title VARCHAR(100) NOT NULL,
        slug VARCHAR(100) UNIQUE NOT NULL,
        description TEXT NOT NULL,
        skills TEXT NOT NULL,
        duration VARCHAR(50) NOT NULL,
        fees DECIMAL(10, 2) NOT NULL,
        benefits TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )",

    // Applications table
    "CREATE TABLE IF NOT EXISTS applications (
        id INT PRIMARY KEY AUTO_INCREMENT,
        user_id INT NOT NULL,
        internship_id INT NOT NULL,
        status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
        application_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (internship_id) REFERENCES internships(id) ON DELETE CASCADE
    )",

    // Payments table
    "CREATE TABLE IF NOT EXISTS payments (
        id INT PRIMARY KEY AUTO_INCREMENT,
        application_id INT NOT NULL,
        amount DECIMAL(10, 2) NOT NULL,
        payment_method VARCHAR(50),
        screenshot VARCHAR(255),
        payment_id VARCHAR(100),
        status ENUM('pending', 'verified', 'rejected') DEFAULT 'pending',
        verified_by INT,
        verified_at DATETIME,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (application_id) REFERENCES applications(id) ON DELETE CASCADE,
        FOREIGN KEY (verified_by) REFERENCES users(id) ON DELETE SET NULL
    )",

    // Modules table
    "CREATE TABLE IF NOT EXISTS modules (
        id INT PRIMARY KEY AUTO_INCREMENT,
        internship_id INT NOT NULL,
        title VARCHAR(100) NOT NULL,
        description TEXT,
        module_file VARCHAR(255),
        order_seq INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (internship_id) REFERENCES internships(id) ON DELETE CASCADE
    )",

    // Module Files table - stores multiple files per module
    "CREATE TABLE IF NOT EXISTS module_files (
        id INT PRIMARY KEY AUTO_INCREMENT,
        module_id INT NOT NULL,
        file_name VARCHAR(255) NOT NULL,
        file_path VARCHAR(255) NOT NULL,
        file_type VARCHAR(50),
        file_size INT,
        upload_order INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (module_id) REFERENCES modules(id) ON DELETE CASCADE
    )",

    // Student Module Progress
    "CREATE TABLE IF NOT EXISTS student_module_progress (
        id INT PRIMARY KEY AUTO_INCREMENT,
        application_id INT NOT NULL,
        module_id INT NOT NULL,
        completed BOOLEAN DEFAULT FALSE,
        completed_at DATETIME,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (application_id) REFERENCES applications(id) ON DELETE CASCADE,
        FOREIGN KEY (module_id) REFERENCES modules(id) ON DELETE CASCADE,
        UNIQUE KEY unique_progress (application_id, module_id)
    )",

    // Blogs table
    "CREATE TABLE IF NOT EXISTS blogs (
        id INT PRIMARY KEY AUTO_INCREMENT,
        title VARCHAR(255) NOT NULL,
        slug VARCHAR(255) UNIQUE NOT NULL,
        content LONGTEXT NOT NULL,
        author_id INT NOT NULL,
        featured_image VARCHAR(255),
        category VARCHAR(50),
        status ENUM('published', 'draft') DEFAULT 'draft',
        views INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE CASCADE
    )",

    // Activity Logs table
    "CREATE TABLE IF NOT EXISTS activity_logs (
        id INT PRIMARY KEY AUTO_INCREMENT,
        action VARCHAR(255) NOT NULL,
        user_id INT,
        details TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
    )"
];

foreach ($tables as $table) {
    if ($conn->query($table) === TRUE) {
        echo "Table created successfully<br>";
    } else {
        echo "Error creating table: " . $conn->error . "<br>";
    }
}

// Insert dummy data
echo "<hr><h2>Inserting Dummy Data...</h2>";

// Admin user
$adminPassword = password_hash('admin123', PASSWORD_DEFAULT);
$conn->query("INSERT INTO users (name, email, password, phone, user_type, status) 
             VALUES ('Admin User', 'admin@digitaltarai.com', '$adminPassword', '+977-9800000000', 'admin', 'active')
             ON DUPLICATE KEY UPDATE id=LAST_INSERT_ID(id)");
echo "Admin user created<br>";

// Student users
$studentPassword = password_hash('student123', PASSWORD_DEFAULT);
$students = [
    ['Ram Kumar', 'ram@example.com', '+977-9800000001'],
    ['Priya Sharma', 'priya@example.com', '+977-9800000002'],
    ['Anil Singh', 'anil@example.com', '+977-9800000003'],
    ['Neha Kumari', 'neha@example.com', '+977-9800000004'],
];

foreach ($students as $student) {
    $conn->query("INSERT INTO users (name, email, password, phone, user_type, status) 
                 VALUES ('{$student[0]}', '{$student[1]}', '$studentPassword', '{$student[2]}', 'student', 'active')");
}
echo "Student users created<br>";

// Internships
$internships = [
    [
        'Frontend Development Internship',
        'frontend-development-internship',
        'Learn modern frontend development with React, Vue, and Tailwind CSS. Build responsive web applications and master JavaScript fundamentals.',
        'HTML, CSS, JavaScript, Basic React/Vue knowledge',
        '3 months',
        500,
        'Offer Letter, Course Modules, Certificate, Letter of Recommendation'
    ],
    [
        'Backend Development Internship',
        'backend-development-internship',
        'Master backend development with PHP, MySQL, and API design. Learn about databases, authentication, and server-side development.',
        'PHP/Python basics, MySQL, RESTful APIs',
        '3 months',
        500,
        'Offer Letter, Course Modules, Certificate, Letter of Recommendation'
    ],
    [
        'Android Development Internship',
        'android-development-internship',
        'Develop native Android applications using Java and Kotlin. Build real-world projects and learn mobile app best practices.',
        'Java/Kotlin basics, Android SDK, XML',
        '4 months',
        600,
        'Offer Letter, Course Modules, Certificate, Letter of Recommendation'
    ],
    [
        'Full Stack Development Internship',
        'full-stack-development-internship',
        'Become a full-stack developer by learning both frontend and backend technologies. Build complete web applications from scratch.',
        'HTML, CSS, JavaScript, PHP, MySQL',
        '4 months',
        700,
        'Offer Letter, Course Modules, Certificate, Letter of Recommendation'
    ]
];

$internshipIds = [];
foreach ($internships as $internship) {
    $conn->query("INSERT INTO internships (title, slug, description, skills, duration, fees, benefits) 
                 VALUES ('{$internship[0]}', '{$internship[1]}', '{$internship[2]}', '{$internship[3]}', '{$internship[4]}', {$internship[5]}, '{$internship[6]}')");
    $internshipIds[] = $conn->insert_id;
}
echo "Internships created<br>";

// Modules for each internship
$modules = [
    // Frontend modules
    [1, 'HTML & CSS Fundamentals', 'Learn HTML structure and CSS styling'],
    [1, 'JavaScript Basics', 'Master JavaScript fundamentals and DOM manipulation'],
    [1, 'Introduction to React', 'Learn React components and state management'],
    [1, 'Tailwind CSS', 'Style your applications with Tailwind CSS'],
    // Backend modules
    [2, 'PHP Basics', 'Learn PHP syntax and fundamentals'],
    [2, 'MySQL Database Design', 'Master database design and queries'],
    [2, 'Building REST APIs', 'Create scalable REST APIs with PHP'],
    [2, 'Authentication & Security', 'Implement secure authentication systems'],
    // Android modules
    [3, 'Android Studio Setup', 'Setup development environment'],
    [3, 'Android Activities & Layouts', 'Learn activity lifecycle and layouts'],
    [3, 'Android Services & Content Providers', 'Advanced Android components'],
    [3, 'Publishing to Play Store', 'Prepare and publish your app'],
    // Full Stack modules
    [4, 'Full Stack Fundamentals', 'Overview of full stack development'],
    [4, 'Frontend Development', 'Build responsive user interfaces'],
    [4, 'Backend Development', 'Create robust server applications'],
    [4, 'Database Management', 'Design and optimize databases'],
    [4, 'Deployment & DevOps', 'Deploy and maintain web applications']
];

foreach ($modules as $idx => $module) {
    $conn->query("INSERT INTO modules (internship_id, title, description, order_seq) 
                 VALUES ({$module[0]}, '{$module[1]}', '{$module[2]}', " . ($idx % 5) . ")");
}
echo "Modules created<br>";

// Blog posts
$adminId = 1; // Admin user ID
$blogs = [
    [
        'Getting Started with Web Development',
        'getting-started-with-web-development',
        '<h2>Introduction</h2><p>Web development is one of the most in-demand skills in the tech industry. Whether you want to build websites, web applications, or become a full-stack developer, this guide will help you get started.</p><h2>What You Need to Learn</h2><p>To become a web developer, you need to master three core technologies:</p><ul><li><strong>HTML</strong> - Structure your content</li><li><strong>CSS</strong> - Style your pages</li><li><strong>JavaScript</strong> - Add interactivity</li></ul><p>After mastering these, you can explore frameworks like React, Vue, or Angular for frontend development, and learn backend technologies like PHP, Node.js, or Python.</p><h2>Getting Started</h2><p>Start with small projects and gradually increase the complexity. Build a portfolio of projects to showcase your skills to potential employers.</p>',
        'Technology',
        'published'
    ],
    [
        'The Future of Mobile App Development',
        'the-future-of-mobile-app-development',
        '<h2>Mobile Development Trends</h2><p>Mobile app development continues to evolve rapidly. With billions of smartphone users worldwide, the demand for skilled mobile developers is higher than ever.</p><h2>Popular Technologies</h2><p>The most popular approaches for mobile development include:</p><ul><li><strong>Native Development</strong> - iOS (Swift) and Android (Kotlin/Java)</li><li><strong>Cross-Platform</strong> - React Native, Flutter</li><li><strong>Web-based</strong> - Progressive Web Apps (PWAs)</li></ul><h2>Career Opportunities</h2><p>Mobile developers are highly sought after by companies of all sizes. Starting an internship in mobile development can lead to excellent career opportunities.</p>',
        'Technology',
        'published'
    ],
    [
        'Best Practices in Software Development',
        'best-practices-in-software-development',
        '<h2>Clean Code</h2><p>Writing clean, maintainable code is essential for any developer. Follow these principles:</p><ul><li>Use meaningful variable names</li><li>Keep functions small and focused</li><li>Add comments for complex logic</li><li>Follow consistent formatting</li></ul><h2>Version Control</h2><p>Always use version control systems like Git to manage your code. This allows you to track changes, collaborate with others, and maintain a history of your project.</p><h2>Testing</h2><p>Write tests for your code to catch bugs early and ensure reliability. Unit testing, integration testing, and end-to-end testing are all important.</p><h2>Documentation</h2><p>Document your code and projects thoroughly. Good documentation makes it easier for others to understand and maintain your code.</p>',
        'Technology',
        'published'
    ],
    [
        'Why You Should Choose Digital Tarai for Your Internship',
        'why-choose-digital-tarai-internship',
        '<h2>About Digital Tarai</h2><p>Digital Tarai is a software development company dedicated to building innovative digital solutions. We believe in nurturing young talent and providing real-world experience.</p><h2>What We Offer</h2><p>Our internship programs are designed to give you hands-on experience with modern technologies. You will:</p><ul><li>Work on real projects</li><li>Learn from experienced developers</li><li>Get mentorship and guidance</li><li>Build a professional portfolio</li><li>Earn a recognized certificate</li></ul><h2>Location</h2><p>Based in Siraha, Nepal, we are committed to developing local talent and building a strong tech community in the region.</p>',
        'Company',
        'published'
    ]
];

foreach ($blogs as $blog) {
    $conn->query("INSERT INTO blogs (title, slug, content, author_id, category, status) 
                 VALUES ('{$blog[0]}', '{$blog[1]}', '{$blog[2]}', $adminId, '{$blog[3]}', '{$blog[4]}')");
}
echo "Blog posts created<br>";

// Sample applications
$conn->query("INSERT INTO applications (user_id, internship_id, status) 
             VALUES (2, 1, 'pending')");
$applicationId = $conn->insert_id;

// Sample payment
$conn->query("INSERT INTO payments (application_id, amount, payment_method, status) 
             VALUES ($applicationId, 500, 'Online Transfer', 'pending')");

echo "<hr><h2>Setup Complete!</h2>";
echo "<p>Admin Login: admin@digitaltarai.com / admin123</p>";
echo "<p>Student Login: ram@example.com / student123</p>";
echo "<p><a href='index.php'>Go to Home Page</a></p>";

$conn->close();
?>
