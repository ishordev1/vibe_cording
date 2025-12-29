<?php
/**
 * Database Schema Creation Script
 * Run this script once to create all necessary tables
 */

require_once 'config.php';

// SQL queries to create tables
$queries = [
    // Users table (for both admin and interns)
    "CREATE TABLE IF NOT EXISTS users (
        id INT PRIMARY KEY AUTO_INCREMENT,
        email VARCHAR(255) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        full_name VARCHAR(255) NOT NULL,
        phone VARCHAR(20),
        date_of_birth DATE,
        gender ENUM('Male', 'Female', 'Other'),
        address TEXT,
        city VARCHAR(100),
        state VARCHAR(100),
        pincode VARCHAR(10),
        college_name VARCHAR(255),
        degree VARCHAR(100),
        year_of_study INT,
        cgpa DECIMAL(3,2),
        resume_path VARCHAR(255),
        role ENUM('admin', 'intern') NOT NULL DEFAULT 'intern',
        status ENUM('active', 'inactive', 'suspended') NOT NULL DEFAULT 'active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        last_login TIMESTAMP NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",
    
    // Internships table (Admin creates these)
    "CREATE TABLE IF NOT EXISTS internships (
        id INT PRIMARY KEY AUTO_INCREMENT,
        title VARCHAR(255) NOT NULL,
        description TEXT NOT NULL,
        role VARCHAR(100) NOT NULL,
        duration_min INT NOT NULL COMMENT '1-4 months',
        duration_max INT NOT NULL,
        internship_type ENUM('Task-based', 'Learning') NOT NULL,
        remote ENUM('Yes', 'No', 'Hybrid') NOT NULL,
        skills_required TEXT,
        number_of_positions INT DEFAULT 1,
        is_published BOOLEAN DEFAULT FALSE,
        created_by INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",
    
    // Applications table
    "CREATE TABLE IF NOT EXISTS applications (
        id INT PRIMARY KEY AUTO_INCREMENT,
        user_id INT NOT NULL,
        internship_id INT NOT NULL,
        status ENUM('Pending', 'Approved', 'Rejected') DEFAULT 'Pending',
        payment_proof_path VARCHAR(255),
        payment_verified BOOLEAN DEFAULT FALSE,
        cover_letter TEXT,
        linkedin_profile VARCHAR(255),
        github_profile VARCHAR(255),
        applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        reviewed_at TIMESTAMP NULL,
        reviewed_by INT,
        rejection_reason TEXT,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (internship_id) REFERENCES internships(id) ON DELETE CASCADE,
        FOREIGN KEY (reviewed_by) REFERENCES users(id) ON DELETE SET NULL,
        UNIQUE KEY unique_application (user_id, internship_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",
    
    // Offer Letters table
    "CREATE TABLE IF NOT EXISTS offer_letters (
        id INT PRIMARY KEY AUTO_INCREMENT,
        application_id INT NOT NULL UNIQUE,
        offer_id VARCHAR(50) UNIQUE NOT NULL COMMENT 'Unique Offer ID',
        user_id INT NOT NULL,
        internship_id INT NOT NULL,
        start_date DATE NOT NULL,
        end_date DATE NOT NULL,
        duration_months INT NOT NULL,
        generated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        pdf_path VARCHAR(255),
        verified BOOLEAN DEFAULT FALSE,
        FOREIGN KEY (application_id) REFERENCES applications(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (internship_id) REFERENCES internships(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",
    
    // Levels table (Level 1-4)
    "CREATE TABLE IF NOT EXISTS levels (
        id INT PRIMARY KEY AUTO_INCREMENT,
        level_number INT UNIQUE NOT NULL COMMENT '1, 2, 3, or 4',
        title VARCHAR(255) NOT NULL,
        description TEXT,
        order_sequence INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",
    
    // Tasks table (same for all interns)
    "CREATE TABLE IF NOT EXISTS tasks (
        id INT PRIMARY KEY AUTO_INCREMENT,
        level_id INT NOT NULL,
        title VARCHAR(255) NOT NULL,
        description TEXT NOT NULL,
        deliverables TEXT NOT NULL COMMENT 'What interns must deliver',
        submission_format ENUM('Link', 'Text', 'File') NOT NULL,
        order_sequence INT NOT NULL,
        created_by INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (level_id) REFERENCES levels(id) ON DELETE CASCADE,
        FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE RESTRICT
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",
    
    // Task Submissions table
    "CREATE TABLE IF NOT EXISTS task_submissions (
        id INT PRIMARY KEY AUTO_INCREMENT,
        task_id INT NOT NULL,
        user_id INT NOT NULL,
        submission_content TEXT,
        submission_link VARCHAR(500),
        file_path VARCHAR(255),
        status ENUM('Not Started', 'Submitted', 'Under Review', 'Approved', 'Rejected', 'Rework Requested') DEFAULT 'Not Started',
        submitted_at TIMESTAMP NULL,
        reviewed_at TIMESTAMP NULL,
        reviewed_by INT,
        admin_feedback TEXT,
        rework_count INT DEFAULT 0,
        FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (reviewed_by) REFERENCES users(id) ON DELETE SET NULL,
        UNIQUE KEY unique_submission (task_id, user_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",
    
    // Attendance table
    "CREATE TABLE IF NOT EXISTS attendance (
        id INT PRIMARY KEY AUTO_INCREMENT,
        user_id INT NOT NULL,
        application_id INT NOT NULL,
        attendance_date DATE NOT NULL,
        status ENUM('Present', 'Absent', 'Late') NOT NULL,
        marked_by INT NOT NULL,
        marked_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        notes TEXT,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (application_id) REFERENCES applications(id) ON DELETE CASCADE,
        FOREIGN KEY (marked_by) REFERENCES users(id) ON DELETE RESTRICT,
        UNIQUE KEY unique_attendance (user_id, attendance_date)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",
    
    // Certificates table
    "CREATE TABLE IF NOT EXISTS certificates (
        id INT PRIMARY KEY AUTO_INCREMENT,
        user_id INT NOT NULL,
        application_id INT NOT NULL,
        certificate_id VARCHAR(50) UNIQUE NOT NULL COMMENT 'Unique Certificate ID',
        internship_role VARCHAR(100) NOT NULL,
        duration_months INT NOT NULL,
        issue_date DATE NOT NULL,
        pdf_path VARCHAR(255),
        verified BOOLEAN DEFAULT FALSE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (application_id) REFERENCES applications(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",
    
    // Level Completion tracking
    "CREATE TABLE IF NOT EXISTS level_completion (
        id INT PRIMARY KEY AUTO_INCREMENT,
        user_id INT NOT NULL,
        level_id INT NOT NULL,
        completion_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (level_id) REFERENCES levels(id) ON DELETE CASCADE,
        UNIQUE KEY unique_level_completion (user_id, level_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;"
];

// Execute all queries
foreach ($queries as $query) {
    if ($conn->query($query) === TRUE) {
        echo "✓ Table created/verified successfully<br>";
    } else {
        echo "✗ Error: " . $conn->error . "<br>";
    }
}

// Insert default admin user (email: admin@internship.com, password: admin123)
$admin_email = 'admin@internship.com';
$admin_password = password_hash('admin123', PASSWORD_BCRYPT);
$admin_name = 'Administrator';

$check_admin = $conn->prepare("SELECT id FROM users WHERE email = ? AND role = 'admin'");
$check_admin->bind_param("s", $admin_email);
$check_admin->execute();
$result = $check_admin->get_result();

if ($result->num_rows === 0) {
    $insert_admin = $conn->prepare("INSERT INTO users (email, password, full_name, role) VALUES (?, ?, ?, 'admin')");
    $insert_admin->bind_param("sss", $admin_email, $admin_password, $admin_name);
    if ($insert_admin->execute()) {
        echo "✓ Default admin user created (email: admin@internship.com, password: admin123)<br>";
    } else {
        echo "✗ Error creating admin: " . $insert_admin->error . "<br>";
    }
    $insert_admin->close();
}
$check_admin->close();

// Insert default levels (1-4)
$levels_data = [
    [1, 'Foundation Level', 'Basic understanding and setup of development environment'],
    [2, 'Intermediate Level', 'Working on real tasks and features'],
    [3, 'Advanced Level', 'Complex problem solving and optimizations'],
    [4, 'Expert Level', 'Leadership and mentoring other interns']
];

foreach ($levels_data as $level) {
    $check_level = $conn->prepare("SELECT id FROM levels WHERE level_number = ?");
    $check_level->bind_param("i", $level[0]);
    $check_level->execute();
    $result = $check_level->get_result();
    
    if ($result->num_rows === 0) {
        $insert_level = $conn->prepare("INSERT INTO levels (level_number, title, description, order_sequence) VALUES (?, ?, ?, ?)");
        $insert_level->bind_param("issi", $level[0], $level[1], $level[2], $level[0]);
        if ($insert_level->execute()) {
            echo "✓ Level " . $level[0] . " created<br>";
        } else {
            echo "✗ Error creating level: " . $insert_level->error . "<br>";
        }
        $insert_level->close();
    }
    $check_level->close();
}

echo "<br><strong>Database setup completed!</strong><br>";
echo "Admin login credentials: admin@internship.com / admin123";

?>
