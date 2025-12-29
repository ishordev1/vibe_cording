-- ============================================================================
-- Digital Tarai Database Setup Script
-- Database: digital_tarai
-- ============================================================================

-- Create Database
CREATE DATABASE IF NOT EXISTS digital_tarai;
USE digital_tarai;

-- ============================================================================
-- TABLE: users
-- Description: Store user information (students and admins)
-- ============================================================================
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    user_type ENUM('student', 'admin') DEFAULT 'student',
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ============================================================================
-- TABLE: internships
-- Description: Store internship program information
-- ============================================================================
CREATE TABLE IF NOT EXISTS internships (
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
);

-- ============================================================================
-- TABLE: applications
-- Description: Store student internship applications
-- ============================================================================
CREATE TABLE IF NOT EXISTS applications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    internship_id INT NOT NULL,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    application_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (internship_id) REFERENCES internships(id) ON DELETE CASCADE
);

-- ============================================================================
-- TABLE: payments
-- Description: Store payment information for applications
-- ============================================================================
CREATE TABLE IF NOT EXISTS payments (
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
);

-- ============================================================================
-- TABLE: modules
-- Description: Store course modules for internships
-- ============================================================================
CREATE TABLE IF NOT EXISTS modules (
    id INT PRIMARY KEY AUTO_INCREMENT,
    internship_id INT NOT NULL,
    title VARCHAR(100) NOT NULL,
    description TEXT,
    module_file VARCHAR(255),
    order_seq INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (internship_id) REFERENCES internships(id) ON DELETE CASCADE
);

-- ============================================================================
-- TABLE: student_module_progress
-- Description: Track student progress through modules
-- ============================================================================
CREATE TABLE IF NOT EXISTS student_module_progress (
    id INT PRIMARY KEY AUTO_INCREMENT,
    application_id INT NOT NULL,
    module_id INT NOT NULL,
    completed BOOLEAN DEFAULT FALSE,
    completed_at DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (application_id) REFERENCES applications(id) ON DELETE CASCADE,
    FOREIGN KEY (module_id) REFERENCES modules(id) ON DELETE CASCADE,
    UNIQUE KEY unique_progress (application_id, module_id)
);

-- ============================================================================
-- TABLE: blogs
-- Description: Store blog posts
-- ============================================================================
CREATE TABLE IF NOT EXISTS blogs (
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
);

-- ============================================================================
-- TABLE: activity_logs
-- Description: Store activity logs for auditing
-- ============================================================================
CREATE TABLE IF NOT EXISTS activity_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    action VARCHAR(255) NOT NULL,
    user_id INT,
    details TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- ============================================================================
-- TABLE: certificates
-- Description: Store generated certificates for internships
-- ============================================================================
CREATE TABLE IF NOT EXISTS certificates (
    id INT PRIMARY KEY AUTO_INCREMENT,
    application_id INT NOT NULL,
    user_id INT NOT NULL,
    internship_id INT NOT NULL,
    filename VARCHAR(255) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    generated_by INT,
    generated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (application_id) REFERENCES applications(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (internship_id) REFERENCES internships(id) ON DELETE CASCADE,
    FOREIGN KEY (generated_by) REFERENCES users(id) ON DELETE SET NULL
);

-- ============================================================================
-- TABLE: offer_letters
-- Description: Store generated offer letters for internships
-- ============================================================================
CREATE TABLE IF NOT EXISTS offer_letters (
    id INT PRIMARY KEY AUTO_INCREMENT,
    application_id INT NOT NULL,
    user_id INT NOT NULL,
    internship_id INT NOT NULL,
    filename VARCHAR(255) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    generated_by INT,
    generated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (application_id) REFERENCES applications(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (internship_id) REFERENCES internships(id) ON DELETE CASCADE,
    FOREIGN KEY (generated_by) REFERENCES users(id) ON DELETE SET NULL
);

-- ============================================================================
-- INSERT DATA: Users
-- ============================================================================

-- Admin User (Password: admin123)
INSERT INTO users (name, email, password, phone, user_type, status) VALUES 
('Admin User', 'admin@digitaltarai.com', '$2y$10$YourHashedPasswordHere1', '+977-9800000000', 'admin', 'active');

-- Student Users (Password: student123)
INSERT INTO users (name, email, password, phone, user_type, status) VALUES 
('Ram Kumar', 'ram@example.com', '$2y$10$YourHashedPasswordHere2', '+977-9800000001', 'student', 'active'),
('Priya Sharma', 'priya@example.com', '$2y$10$YourHashedPasswordHere3', '+977-9800000002', 'student', 'active'),
('Anil Singh', 'anil@example.com', '$2y$10$YourHashedPasswordHere4', '+977-9800000003', 'student', 'active'),
('Neha Kumari', 'neha@example.com', '$2y$10$YourHashedPasswordHere5', '+977-9800000004', 'student', 'active');

-- ============================================================================
-- INSERT DATA: Internships
-- ============================================================================

INSERT INTO internships (title, slug, description, skills, duration, fees, benefits) VALUES 
(
    'Frontend Development Internship',
    'frontend-development-internship',
    'Learn modern frontend development with React, Vue, and Tailwind CSS. Build responsive web applications and master JavaScript fundamentals.',
    'HTML, CSS, JavaScript, Basic React/Vue knowledge',
    '3 months',
    500,
    'Offer Letter, Course Modules, Certificate, Letter of Recommendation'
),
(
    'Backend Development Internship',
    'backend-development-internship',
    'Master backend development with PHP, MySQL, and API design. Learn about databases, authentication, and server-side development.',
    'PHP/Python basics, MySQL, RESTful APIs',
    '3 months',
    500,
    'Offer Letter, Course Modules, Certificate, Letter of Recommendation'
),
(
    'Android Development Internship',
    'android-development-internship',
    'Develop native Android applications using Java and Kotlin. Build real-world projects and learn mobile app best practices.',
    'Java/Kotlin basics, Android SDK, XML',
    '4 months',
    600,
    'Offer Letter, Course Modules, Certificate, Letter of Recommendation'
),
(
    'Full Stack Development Internship',
    'full-stack-development-internship',
    'Become a full-stack developer by learning both frontend and backend technologies. Build complete web applications from scratch.',
    'HTML, CSS, JavaScript, PHP, MySQL',
    '4 months',
    700,
    'Offer Letter, Course Modules, Certificate, Letter of Recommendation'
);

-- ============================================================================
-- INSERT DATA: Modules
-- ============================================================================

-- Frontend Development Modules (internship_id = 1)
INSERT INTO modules (internship_id, title, description, order_seq) VALUES 
(1, 'HTML & CSS Fundamentals', 'Learn HTML structure and CSS styling', 1),
(1, 'JavaScript Basics', 'Master JavaScript fundamentals and DOM manipulation', 2),
(1, 'Introduction to React', 'Learn React components and state management', 3),
(1, 'Tailwind CSS', 'Style your applications with Tailwind CSS', 4);

-- Backend Development Modules (internship_id = 2)
INSERT INTO modules (internship_id, title, description, order_seq) VALUES 
(2, 'PHP Basics', 'Learn PHP syntax and fundamentals', 1),
(2, 'MySQL Database Design', 'Master database design and queries', 2),
(2, 'Building REST APIs', 'Create scalable REST APIs with PHP', 3),
(2, 'Authentication & Security', 'Implement secure authentication systems', 4);

-- Android Development Modules (internship_id = 3)
INSERT INTO modules (internship_id, title, description, order_seq) VALUES 
(3, 'Android Studio Setup', 'Setup development environment', 1),
(3, 'Android Activities & Layouts', 'Learn activity lifecycle and layouts', 2),
(3, 'Android Services & Content Providers', 'Advanced Android components', 3),
(3, 'Publishing to Play Store', 'Prepare and publish your app', 4);

-- Full Stack Development Modules (internship_id = 4)
INSERT INTO modules (internship_id, title, description, order_seq) VALUES 
(4, 'Full Stack Fundamentals', 'Overview of full stack development', 1),
(4, 'Frontend Development', 'Build responsive user interfaces', 2),
(4, 'Backend Development', 'Create robust server applications', 3),
(4, 'Database Management', 'Design and optimize databases', 4),
(4, 'Deployment & DevOps', 'Deploy and maintain web applications', 5);

-- ============================================================================
-- INSERT DATA: Applications
-- ============================================================================

INSERT INTO applications (user_id, internship_id, status) VALUES 
(2, 1, 'pending');

-- ============================================================================
-- INSERT DATA: Payments
-- ============================================================================

INSERT INTO payments (application_id, amount, payment_method, status) VALUES 
(1, 500, 'Online Transfer', 'pending');

-- ============================================================================
-- INSERT DATA: Blog Posts
-- ============================================================================

INSERT INTO blogs (title, slug, content, author_id, category, status) VALUES 
(
    'Getting Started with Web Development',
    'getting-started-with-web-development',
    '<h2>Introduction</h2><p>Web development is one of the most in-demand skills in the tech industry. Whether you want to build websites, web applications, or become a full-stack developer, this guide will help you get started.</p><h2>What You Need to Learn</h2><p>To become a web developer, you need to master three core technologies:</p><ul><li><strong>HTML</strong> - Structure your content</li><li><strong>CSS</strong> - Style your pages</li><li><strong>JavaScript</strong> - Add interactivity</li></ul><p>After mastering these, you can explore frameworks like React, Vue, or Angular for frontend development, and learn backend technologies like PHP, Node.js, or Python.</p><h2>Getting Started</h2><p>Start with small projects and gradually increase the complexity. Build a portfolio of projects to showcase your skills to potential employers.</p>',
    1,
    'Technology',
    'published'
),
(
    'The Future of Mobile App Development',
    'the-future-of-mobile-app-development',
    '<h2>Mobile Development Trends</h2><p>Mobile app development continues to evolve rapidly. With billions of smartphone users worldwide, the demand for skilled mobile developers is higher than ever.</p><h2>Popular Technologies</h2><p>The most popular approaches for mobile development include:</p><ul><li><strong>Native Development</strong> - iOS (Swift) and Android (Kotlin/Java)</li><li><strong>Cross-Platform</strong> - React Native, Flutter</li><li><strong>Web-based</strong> - Progressive Web Apps (PWAs)</li></ul><h2>Career Opportunities</h2><p>Mobile developers are highly sought after by companies of all sizes. Starting an internship in mobile development can lead to excellent career opportunities.</p>',
    1,
    'Technology',
    'published'
),
(
    'Best Practices in Software Development',
    'best-practices-in-software-development',
    '<h2>Clean Code</h2><p>Writing clean, maintainable code is essential for any developer. Follow these principles:</p><ul><li>Use meaningful variable names</li><li>Keep functions small and focused</li><li>Add comments for complex logic</li><li>Follow consistent formatting</li></ul><h2>Version Control</h2><p>Always use version control systems like Git to manage your code. This allows you to track changes, collaborate with others, and maintain a history of your project.</p><h2>Testing</h2><p>Write tests for your code to catch bugs early and ensure reliability. Unit testing, integration testing, and end-to-end testing are all important.</p><h2>Documentation</h2><p>Document your code and projects thoroughly. Good documentation makes it easier for others to understand and maintain your code.</p>',
    1,
    'Technology',
    'published'
),
(
    'Why You Should Choose Digital Tarai for Your Internship',
    'why-choose-digital-tarai-internship',
    '<h2>About Digital Tarai</h2><p>Digital Tarai is a software development company dedicated to building innovative digital solutions. We believe in nurturing young talent and providing real-world experience.</p><h2>What We Offer</h2><p>Our internship programs are designed to give you hands-on experience with modern technologies. You will:</p><ul><li>Work on real projects</li><li>Learn from experienced developers</li><li>Get mentorship and guidance</li><li>Build a professional portfolio</li><li>Earn a recognized certificate</li></ul><h2>Location</h2><p>Based in Siraha, Nepal, we are committed to developing local talent and building a strong tech community in the region.</p>',
    1,
    'Company',
    'published'
);

-- ============================================================================
-- END OF DATABASE SETUP SCRIPT
-- ============================================================================
-- Notes:
-- 1. Replace the hashed passwords with actual bcrypt hashes generated by PHP:
--    password_hash('admin123', PASSWORD_DEFAULT)
--    password_hash('student123', PASSWORD_DEFAULT)
-- 2. This script creates all tables and inserts sample data
-- 3. Modify the data as needed for your requirements
-- 4. To run this script:
--    - Open phpMyAdmin
--    - Create a new database "digital_tarai"
--    - Import this SQL file
--    - OR use command line: mysql -u root < database.sql
-- ============================================================================
