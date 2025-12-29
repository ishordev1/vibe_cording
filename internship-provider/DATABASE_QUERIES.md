# 🗄️ DATABASE QUERIES REFERENCE

Complete list of all SQL queries used in the internship management system.

---

## 📊 TABLE STRUCTURE QUERIES

### CREATE USERS TABLE
```sql
CREATE TABLE IF NOT EXISTS users (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### CREATE INTERNSHIPS TABLE
```sql
CREATE TABLE IF NOT EXISTS internships (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### CREATE APPLICATIONS TABLE
```sql
CREATE TABLE IF NOT EXISTS applications (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### CREATE OFFER_LETTERS TABLE
```sql
CREATE TABLE IF NOT EXISTS offer_letters (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### CREATE LEVELS TABLE
```sql
CREATE TABLE IF NOT EXISTS levels (
    id INT PRIMARY KEY AUTO_INCREMENT,
    level_number INT UNIQUE NOT NULL COMMENT '1, 2, 3, or 4',
    title VARCHAR(255) NOT NULL,
    description TEXT,
    order_sequence INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### CREATE TASKS TABLE
```sql
CREATE TABLE IF NOT EXISTS tasks (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### CREATE TASK_SUBMISSIONS TABLE
```sql
CREATE TABLE IF NOT EXISTS task_submissions (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### CREATE ATTENDANCE TABLE
```sql
CREATE TABLE IF NOT EXISTS attendance (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### CREATE CERTIFICATES TABLE
```sql
CREATE TABLE IF NOT EXISTS certificates (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### CREATE LEVEL_COMPLETION TABLE
```sql
CREATE TABLE IF NOT EXISTS level_completion (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    level_id INT NOT NULL,
    completion_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (level_id) REFERENCES levels(id) ON DELETE CASCADE,
    UNIQUE KEY unique_level_completion (user_id, level_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

## 👤 USER QUERIES

### CREATE NEW USER (Registration)
```sql
INSERT INTO users (
    email, 
    password, 
    full_name, 
    phone, 
    date_of_birth, 
    gender, 
    address, 
    city, 
    state, 
    pincode, 
    college_name, 
    degree, 
    year_of_study, 
    cgpa, 
    role
) VALUES (
    'john@example.com',
    '$2y$10$...',  -- bcrypt hashed password
    'John Doe',
    '9876543210',
    '2000-05-15',
    'Male',
    '123 Street Address',
    'Mumbai',
    'Maharashtra',
    '400001',
    'XYZ College',
    'B.Tech',
    3,
    3.5,
    'intern'
);
```

### LOGIN - GET USER BY EMAIL
```sql
SELECT id, email, password, full_name, role, status 
FROM users 
WHERE email = 'admin@internship.com';
```

### GET USER PROFILE
```sql
SELECT * 
FROM users 
WHERE id = 1;
```

### UPDATE USER PROFILE
```sql
UPDATE users 
SET full_name = 'John Updated', 
    phone = '9876543210', 
    updated_at = CURRENT_TIMESTAMP
WHERE id = 1;
```

### GET ALL INTERNS
```sql
SELECT id, email, full_name, phone, college_name, degree, role, created_at 
FROM users 
WHERE role = 'intern' 
ORDER BY created_at DESC;
```

### GET USER BY ID
```sql
SELECT id, email, full_name, phone, role 
FROM users 
WHERE id = ? 
LIMIT 1;
```

### SUSPEND/ACTIVATE USER
```sql
UPDATE users 
SET status = 'suspended', 
    updated_at = CURRENT_TIMESTAMP
WHERE id = ?;
```

### UPDATE LAST LOGIN
```sql
UPDATE users 
SET last_login = CURRENT_TIMESTAMP 
WHERE id = ?;
```

---

## 💼 INTERNSHIP QUERIES

### CREATE NEW INTERNSHIP (Admin)
```sql
INSERT INTO internships (
    title,
    description,
    role,
    duration_min,
    duration_max,
    internship_type,
    remote,
    skills_required,
    number_of_positions,
    is_published,
    created_by
) VALUES (
    'Junior Web Developer',
    'Build responsive web applications...',
    'Web Developer',
    2,
    3,
    'Task-based',
    'Hybrid',
    'HTML5, CSS3, JavaScript, PHP, MySQL',
    5,
    FALSE,
    1
);
```

### GET ALL INTERNSHIPS (Published Only)
```sql
SELECT i.id, i.title, i.description, i.role, i.duration_min, i.duration_max,
       i.internship_type, i.remote, i.skills_required, i.number_of_positions,
       u.full_name AS created_by_name,
       (SELECT COUNT(*) FROM applications WHERE internship_id = i.id) AS total_applications
FROM internships i
JOIN users u ON i.created_by = u.id
WHERE i.is_published = TRUE
ORDER BY i.created_at DESC;
```

### GET ALL INTERNSHIPS (Admin View)
```sql
SELECT i.*, 
       u.full_name AS created_by_name,
       (SELECT COUNT(*) FROM applications WHERE internship_id = i.id AND status = 'Pending') AS pending_apps,
       (SELECT COUNT(*) FROM applications WHERE internship_id = i.id AND status = 'Approved') AS approved_apps
FROM internships i
JOIN users u ON i.created_by = u.id
ORDER BY i.created_at DESC;
```

### GET INTERNSHIP BY ID
```sql
SELECT i.*, u.full_name AS created_by_name
FROM internships i
JOIN users u ON i.created_by = u.id
WHERE i.id = ? 
LIMIT 1;
```

### PUBLISH INTERNSHIP
```sql
UPDATE internships 
SET is_published = TRUE, 
    updated_at = CURRENT_TIMESTAMP
WHERE id = ? AND created_by = ?;
```

### UNPUBLISH INTERNSHIP
```sql
UPDATE internships 
SET is_published = FALSE, 
    updated_at = CURRENT_TIMESTAMP
WHERE id = ?;
```

### DELETE INTERNSHIP
```sql
DELETE FROM internships 
WHERE id = ? AND created_by = ?;
```

### UPDATE INTERNSHIP
```sql
UPDATE internships 
SET title = ?,
    description = ?,
    role = ?,
    duration_min = ?,
    duration_max = ?,
    internship_type = ?,
    remote = ?,
    skills_required = ?,
    number_of_positions = ?,
    updated_at = CURRENT_TIMESTAMP
WHERE id = ? AND created_by = ?;
```

---

## 📝 APPLICATION QUERIES

### CREATE APPLICATION (User Applies)
```sql
INSERT INTO applications (
    user_id,
    internship_id,
    payment_proof_path,
    cover_letter,
    linkedin_profile,
    github_profile,
    status
) VALUES (
    ?,
    ?,
    'assets/uploads/payments/PROOF_123456.pdf',
    'I am interested in this internship...',
    'https://linkedin.com/in/johndoe',
    'https://github.com/johndoe',
    'Pending'
);
```

### GET ALL APPLICATIONS (Admin)
```sql
SELECT a.id, a.status, a.applied_at,
       u.id AS user_id, u.full_name, u.email, u.phone,
       i.id AS internship_id, i.title AS internship_title,
       (SELECT COUNT(*) FROM offer_letters WHERE application_id = a.id) AS has_offer
FROM applications a
JOIN users u ON a.user_id = u.id
JOIN internships i ON a.internship_id = i.id
ORDER BY a.applied_at DESC;
```

### GET FILTERED APPLICATIONS (By Status)
```sql
SELECT a.id, a.status, a.applied_at,
       u.full_name, u.email,
       i.title AS internship_title
FROM applications a
JOIN users u ON a.user_id = u.id
JOIN internships i ON a.internship_id = i.id
WHERE a.status = 'Pending'
ORDER BY a.applied_at DESC;
```

### GET USER APPLICATIONS
```sql
SELECT a.*, 
       i.title AS internship_title, i.role,
       ol.offer_id, ol.start_date, ol.end_date
FROM applications a
JOIN internships i ON a.internship_id = i.id
LEFT JOIN offer_letters ol ON a.id = ol.application_id
WHERE a.user_id = ?
ORDER BY a.applied_at DESC;
```

### GET APPLICATION DETAILS
```sql
SELECT a.*, 
       u.full_name, u.email, u.phone, u.college_name,
       i.title, i.description, i.role,
       admin.full_name AS reviewed_by_name,
       ol.offer_id, ol.start_date, ol.end_date
FROM applications a
JOIN users u ON a.user_id = u.id
JOIN internships i ON a.internship_id = i.id
LEFT JOIN users admin ON a.reviewed_by = admin.id
LEFT JOIN offer_letters ol ON a.id = ol.application_id
WHERE a.id = ?
LIMIT 1;
```

### APPROVE APPLICATION
```sql
UPDATE applications 
SET status = 'Approved',
    reviewed_at = CURRENT_TIMESTAMP,
    reviewed_by = ?
WHERE id = ?;
```

### REJECT APPLICATION
```sql
UPDATE applications 
SET status = 'Rejected',
    rejection_reason = ?,
    reviewed_at = CURRENT_TIMESTAMP,
    reviewed_by = ?
WHERE id = ?;
```

### CHECK IF USER ALREADY APPLIED
```sql
SELECT id FROM applications 
WHERE user_id = ? AND internship_id = ? 
LIMIT 1;
```

### GET APPROVED APPLICATION (For Intern)
```sql
SELECT a.id, a.internship_id,
       i.title, i.role, i.duration_min, i.duration_max
FROM applications a
JOIN internships i ON a.internship_id = i.id
WHERE a.user_id = ? AND a.status = 'Approved'
LIMIT 1;
```

---

## 💌 OFFER LETTER QUERIES

### CREATE OFFER LETTER (On Approval)
```sql
INSERT INTO offer_letters (
    application_id,
    offer_id,
    user_id,
    internship_id,
    start_date,
    end_date,
    duration_months
) VALUES (
    ?,
    'OFFER-ABC12345',
    ?,
    ?,
    CURDATE(),
    DATE_ADD(CURDATE(), INTERVAL ? MONTH),
    ?
);
```

### GET OFFER LETTER
```sql
SELECT ol.*, 
       u.full_name, u.email,
       i.title, i.role
FROM offer_letters ol
JOIN users u ON ol.user_id = u.id
JOIN internships i ON ol.internship_id = i.id
WHERE ol.id = ?
LIMIT 1;
```

### GET OFFER BY APPLICATION ID
```sql
SELECT ol.* 
FROM offer_letters ol
WHERE ol.application_id = ?
LIMIT 1;
```

### GET ALL OFFERS (Admin)
```sql
SELECT ol.*, 
       u.full_name,
       i.title,
       a.status AS application_status
FROM offer_letters ol
JOIN users u ON ol.user_id = u.id
JOIN internships i ON ol.internship_id = i.id
JOIN applications a ON ol.application_id = a.id
ORDER BY ol.generated_at DESC;
```

### VERIFY OFFER LETTER
```sql
UPDATE offer_letters 
SET verified = TRUE
WHERE id = ?;
```

---

## 📊 LEVEL QUERIES

### GET ALL LEVELS
```sql
SELECT * FROM levels 
ORDER BY level_number ASC;
```

### GET LEVEL BY ID
```sql
SELECT * FROM levels 
WHERE id = ? 
LIMIT 1;
```

### INSERT LEVEL (Auto-done in setup)
```sql
INSERT INTO levels (
    level_number,
    title,
    description,
    order_sequence
) VALUES (
    1,
    'Foundation Level',
    'Basic understanding and setup of development environment',
    1
);
```

---

## 📋 TASK QUERIES

### CREATE TASK (Admin)
```sql
INSERT INTO tasks (
    level_id,
    title,
    description,
    deliverables,
    submission_format,
    order_sequence,
    created_by
) VALUES (
    1,
    'Setup Development Environment',
    'Set up your local development environment with required tools',
    'Screenshot of setup + github repo link',
    'Link',
    1,
    1
);
```

### GET ALL TASKS (Grouped by Level)
```sql
SELECT t.*, l.level_number, l.title AS level_title
FROM tasks t
JOIN levels l ON t.level_id = l.id
ORDER BY l.level_number, t.order_sequence;
```

### GET TASKS BY LEVEL
```sql
SELECT t.*
FROM tasks t
WHERE t.level_id = ?
ORDER BY t.order_sequence;
```

### GET TASK WITH SUBMISSION STATUS (For User)
```sql
SELECT t.*, 
       ts.status AS submission_status,
       ts.id AS submission_id,
       ts.submitted_at,
       ts.admin_feedback
FROM tasks t
LEFT JOIN task_submissions ts ON t.id = ts.task_id AND ts.user_id = ?
WHERE t.level_id = ?
ORDER BY t.order_sequence;
```

### DELETE TASK
```sql
DELETE FROM tasks 
WHERE id = ? AND created_by = ?;
```

### UPDATE TASK
```sql
UPDATE tasks 
SET title = ?,
    description = ?,
    deliverables = ?,
    submission_format = ?,
    order_sequence = ?,
    updated_at = CURRENT_TIMESTAMP
WHERE id = ? AND created_by = ?;
```

---

## 📤 TASK SUBMISSION QUERIES

### CREATE/UPDATE TASK SUBMISSION
```sql
INSERT INTO task_submissions (
    task_id,
    user_id,
    submission_content,
    submission_link,
    file_path,
    status,
    submitted_at
) VALUES (?, ?, ?, ?, ?, 'Submitted', CURRENT_TIMESTAMP)
ON DUPLICATE KEY UPDATE
    submission_content = VALUES(submission_content),
    submission_link = VALUES(submission_link),
    file_path = VALUES(file_path),
    status = 'Submitted',
    submitted_at = CURRENT_TIMESTAMP,
    rework_count = rework_count + 1;
```

### GET ALL SUBMISSIONS (Admin Review)
```sql
SELECT ts.id, ts.status, ts.submitted_at, ts.rework_count,
       u.full_name, u.email,
       t.title, t.submission_format,
       l.level_number,
       ts.admin_feedback
FROM task_submissions ts
JOIN users u ON ts.user_id = u.id
JOIN tasks t ON ts.task_id = t.id
JOIN levels l ON t.level_id = l.id
WHERE ts.status IN ('Submitted', 'Under Review', 'Rework Requested')
ORDER BY ts.submitted_at DESC;
```

### GET SUBMISSIONS BY STATUS
```sql
SELECT ts.*, 
       u.full_name,
       t.title
FROM task_submissions ts
JOIN users u ON ts.user_id = u.id
JOIN tasks t ON ts.task_id = t.id
WHERE ts.status = 'Submitted'
ORDER BY ts.submitted_at ASC;
```

### GET USER SUBMISSIONS FOR LEVEL
```sql
SELECT t.id, t.title, ts.status, ts.submitted_at, ts.admin_feedback
FROM tasks t
LEFT JOIN task_submissions ts ON t.id = ts.task_id AND ts.user_id = ?
WHERE t.level_id = ?
ORDER BY t.order_sequence;
```

### APPROVE SUBMISSION
```sql
UPDATE task_submissions 
SET status = 'Approved',
    reviewed_at = CURRENT_TIMESTAMP,
    reviewed_by = ?
WHERE id = ?;
```

### REJECT SUBMISSION
```sql
UPDATE task_submissions 
SET status = 'Rejected',
    admin_feedback = ?,
    reviewed_at = CURRENT_TIMESTAMP,
    reviewed_by = ?
WHERE id = ?;
```

### REQUEST REWORK
```sql
UPDATE task_submissions 
SET status = 'Rework Requested',
    admin_feedback = ?,
    reviewed_at = CURRENT_TIMESTAMP,
    reviewed_by = ?
WHERE id = ?;
```

### GET SUBMISSION DETAILS
```sql
SELECT ts.*, 
       t.title, t.description, t.deliverables, t.submission_format,
       u.full_name,
       admin.full_name AS reviewed_by_name,
       l.level_number
FROM task_submissions ts
JOIN tasks t ON ts.task_id = t.id
JOIN users u ON ts.user_id = u.id
LEFT JOIN users admin ON ts.reviewed_by = admin.id
JOIN levels l ON t.level_id = l.id
WHERE ts.id = ?
LIMIT 1;
```

---

## ✅ LEVEL COMPLETION QUERIES

### MARK LEVEL COMPLETE
```sql
INSERT INTO level_completion (user_id, level_id, completion_date)
VALUES (?, ?)
ON DUPLICATE KEY UPDATE
    completion_date = CURRENT_TIMESTAMP;
```

### GET COMPLETED LEVELS (For User)
```sql
SELECT l.level_number, l.title, lc.completion_date
FROM level_completion lc
JOIN levels l ON lc.level_id = l.id
WHERE lc.user_id = ?
ORDER BY l.level_number;
```

### CHECK IF LEVEL COMPLETE
```sql
SELECT * FROM level_completion 
WHERE user_id = ? AND level_id = ? 
LIMIT 1;
```

### GET ALL TASKS APPROVAL STATUS (For Level)
```sql
SELECT 
    COUNT(*) AS total_tasks,
    SUM(CASE WHEN ts.status = 'Approved' THEN 1 ELSE 0 END) AS approved_tasks
FROM tasks t
LEFT JOIN task_submissions ts ON t.id = ts.task_id AND ts.user_id = ?
WHERE t.level_id = ?;
```

### CHECK IF ALL TASKS IN LEVEL APPROVED
```sql
SELECT COUNT(*) AS total_tasks
FROM tasks t
WHERE t.level_id = ? 
  AND NOT EXISTS (
    SELECT 1 FROM task_submissions ts 
    WHERE ts.task_id = t.id 
      AND ts.user_id = ? 
      AND ts.status = 'Approved'
  );
```

---

## 📅 ATTENDANCE QUERIES

### MARK ATTENDANCE (Insert or Update)
```sql
INSERT INTO attendance (
    user_id,
    application_id,
    attendance_date,
    status,
    marked_by,
    notes
) VALUES (
    ?,
    ?,
    ?,
    'Present',
    ?,
    'Regular attendance'
)
ON DUPLICATE KEY UPDATE
    status = VALUES(status),
    notes = VALUES(notes),
    marked_at = CURRENT_TIMESTAMP;
```

### GET ATTENDANCE RECORD
```sql
SELECT * FROM attendance 
WHERE user_id = ? AND attendance_date = ? 
LIMIT 1;
```

### GET USER ATTENDANCE
```sql
SELECT a.*, 
       u.full_name,
       admin.full_name AS marked_by_name
FROM attendance a
JOIN users u ON a.user_id = u.id
JOIN users admin ON a.marked_by = admin.id
WHERE a.user_id = ?
ORDER BY a.attendance_date DESC;
```

### GET ATTENDANCE STATISTICS
```sql
SELECT 
    COUNT(*) AS total_days,
    SUM(CASE WHEN status = 'Present' THEN 1 ELSE 0 END) AS present_days,
    SUM(CASE WHEN status = 'Absent' THEN 1 ELSE 0 END) AS absent_days,
    SUM(CASE WHEN status = 'Late' THEN 1 ELSE 0 END) AS late_days
FROM attendance
WHERE user_id = ?;
```

### GET ATTENDANCE FOR ALL INTERNS
```sql
SELECT 
    u.id, u.full_name, u.email,
    COUNT(a.id) AS total_attendance_days,
    SUM(CASE WHEN a.status = 'Present' THEN 1 ELSE 0 END) AS present,
    SUM(CASE WHEN a.status = 'Absent' THEN 1 ELSE 0 END) AS absent
FROM users u
LEFT JOIN attendance a ON u.id = a.user_id
WHERE u.role = 'intern'
GROUP BY u.id
ORDER BY u.full_name;
```

### GET ATTENDANCE BY DATE RANGE
```sql
SELECT *
FROM attendance
WHERE user_id = ? 
  AND attendance_date BETWEEN ? AND ?
ORDER BY attendance_date DESC;
```

---

## 🏆 CERTIFICATE QUERIES

### CREATE CERTIFICATE (After All Levels Complete)
```sql
INSERT INTO certificates (
    user_id,
    application_id,
    certificate_id,
    internship_role,
    duration_months,
    issue_date
) VALUES (
    ?,
    ?,
    'CERT-ABC12345',
    'Web Developer',
    3,
    CURDATE()
);
```

### GET CERTIFICATE
```sql
SELECT c.*,
       u.full_name,
       i.title, i.role
FROM certificates c
JOIN users u ON c.user_id = u.id
JOIN internships i ON c.internship_id = i.id
WHERE c.user_id = ? AND c.application_id = ?
LIMIT 1;
```

### GET USER CERTIFICATE
```sql
SELECT c.*
FROM certificates c
WHERE c.user_id = ?
LIMIT 1;
```

### GET ALL CERTIFICATES (Admin)
```sql
SELECT c.*, u.full_name, u.email
FROM certificates c
JOIN users u ON c.user_id = u.id
ORDER BY c.issue_date DESC;
```

### CHECK IF CERTIFICATE EXISTS
```sql
SELECT * FROM certificates 
WHERE user_id = ? AND application_id = ? 
LIMIT 1;
```

### VERIFY CERTIFICATE
```sql
UPDATE certificates 
SET verified = TRUE
WHERE id = ?;
```

---

## 📊 ADMIN DASHBOARD QUERIES

### TOTAL INTERNS COUNT
```sql
SELECT COUNT(*) AS total_interns
FROM users
WHERE role = 'intern';
```

### TOTAL INTERNSHIPS COUNT
```sql
SELECT COUNT(*) AS total_internships
FROM internships;
```

### PENDING APPLICATIONS COUNT
```sql
SELECT COUNT(*) AS pending_applications
FROM applications
WHERE status = 'Pending';
```

### APPROVED INTERNS COUNT
```sql
SELECT COUNT(*) AS approved_interns
FROM applications
WHERE status = 'Approved';
```

### RECENT APPLICATIONS
```sql
SELECT a.id, a.status, a.applied_at,
       u.full_name, u.email,
       i.title AS internship_title
FROM applications a
JOIN users u ON a.user_id = u.id
JOIN internships i ON a.internship_id = i.id
ORDER BY a.applied_at DESC
LIMIT 5;
```

### INTERNSHIP WITH APPLICATION STATS
```sql
SELECT i.id, i.title,
       (SELECT COUNT(*) FROM applications WHERE internship_id = i.id) AS total_apps,
       (SELECT COUNT(*) FROM applications WHERE internship_id = i.id AND status = 'Approved') AS approved_apps
FROM internships i
WHERE i.is_published = TRUE;
```

---

## 👤 USER DASHBOARD QUERIES

### USER PROFILE WITH STATS
```sql
SELECT u.*,
       (SELECT COUNT(*) FROM applications WHERE user_id = u.id) AS total_applications,
       (SELECT COUNT(*) FROM level_completion WHERE user_id = u.id) AS levels_completed,
       (SELECT COUNT(*) FROM task_submissions WHERE user_id = u.id AND status = 'Approved') AS tasks_completed
FROM users u
WHERE u.id = ?;
```

### USER ACTIVE INTERNSHIP
```sql
SELECT a.id, a.internship_id,
       i.title, i.role, i.duration_min, i.duration_max,
       ol.start_date, ol.end_date
FROM applications a
JOIN internships i ON a.internship_id = i.id
LEFT JOIN offer_letters ol ON a.id = ol.application_id
WHERE a.user_id = ? AND a.status = 'Approved'
LIMIT 1;
```

### USER RECENT APPLICATIONS
```sql
SELECT a.id, a.status, a.applied_at,
       i.title, i.role
FROM applications a
JOIN internships i ON a.internship_id = i.id
WHERE a.user_id = ?
ORDER BY a.applied_at DESC
LIMIT 5;
```

### USER TASK COMPLETION STATUS
```sql
SELECT 
    COUNT(*) AS total_tasks,
    SUM(CASE WHEN ts.status = 'Approved' THEN 1 ELSE 0 END) AS completed_tasks
FROM tasks t
LEFT JOIN task_submissions ts ON t.id = ts.task_id AND ts.user_id = ?;
```

---

## 🔍 SEARCH & FILTER QUERIES

### SEARCH INTERNSHIPS BY TITLE
```sql
SELECT i.* FROM internships i
WHERE i.is_published = TRUE
  AND (i.title LIKE ? OR i.description LIKE ? OR i.skills_required LIKE ?)
ORDER BY i.created_at DESC;
```

### FILTER INTERNSHIPS BY REMOTE
```sql
SELECT i.* FROM internships i
WHERE i.is_published = TRUE AND i.remote = ?
ORDER BY i.created_at DESC;
```

### FILTER INTERNSHIPS BY TYPE
```sql
SELECT i.* FROM internships i
WHERE i.is_published = TRUE AND i.internship_type = ?
ORDER BY i.created_at DESC;
```

### SEARCH INTERNS BY NAME
```sql
SELECT id, full_name, email, phone, college_name
FROM users
WHERE role = 'intern' AND full_name LIKE ?
ORDER BY full_name;
```

---

## 🔐 SECURITY QUERIES

### CHECK EMAIL EXISTS
```sql
SELECT id FROM users WHERE email = ? LIMIT 1;
```

### GET USER FOR LOGIN
```sql
SELECT id, email, password, role, status
FROM users
WHERE email = ? AND status != 'suspended'
LIMIT 1;
```

### BLOCK/UNBLOCK USER
```sql
UPDATE users 
SET status = 'suspended'
WHERE id = ?;
```

---

## 📈 REPORTING QUERIES

### INTERNSHIP COMPLETION RATE
```sql
SELECT 
    i.title,
    COUNT(a.id) AS total_applications,
    SUM(CASE WHEN a.status = 'Approved' THEN 1 ELSE 0 END) AS approved,
    SUM(CASE WHEN COALESCE(c.id, 0) > 0 THEN 1 ELSE 0 END) AS certificates_generated
FROM internships i
LEFT JOIN applications a ON i.id = a.internship_id
LEFT JOIN certificates c ON a.id = c.application_id
GROUP BY i.id
ORDER BY i.created_at DESC;
```

### INTERNS BY LEVEL COMPLETION
```sql
SELECT u.full_name, u.email,
       COUNT(DISTINCT lc.level_id) AS levels_completed,
       COALESCE(cert.id, 'None') AS certificate_status
FROM users u
LEFT JOIN level_completion lc ON u.id = lc.user_id
LEFT JOIN certificates cert ON u.id = cert.user_id
WHERE u.role = 'intern'
GROUP BY u.id
ORDER BY levels_completed DESC;
```

### TASK SUBMISSION STATISTICS
```sql
SELECT 
    t.title,
    COUNT(ts.id) AS total_submissions,
    SUM(CASE WHEN ts.status = 'Approved' THEN 1 ELSE 0 END) AS approved,
    SUM(CASE WHEN ts.status = 'Rejected' THEN 1 ELSE 0 END) AS rejected,
    SUM(CASE WHEN ts.status = 'Rework Requested' THEN 1 ELSE 0 END) AS rework
FROM tasks t
LEFT JOIN task_submissions ts ON t.id = ts.task_id
GROUP BY t.id
ORDER BY t.id;
```

---

## 🛠️ MAINTENANCE QUERIES

### DELETE USER (Cascade)
```sql
DELETE FROM users WHERE id = ?;
```

### CLEAR EXPIRED SESSIONS
```sql
UPDATE users 
SET last_login = NULL
WHERE TIMESTAMPDIFF(DAY, last_login, CURRENT_TIMESTAMP) > 30;
```

### BACKUP USER DATA
```sql
SELECT * INTO OUTFILE '/backup/users_backup.csv'
FIELDS TERMINATED BY ','
FROM users;
```

### GET DATABASE SIZE
```sql
SELECT 
    SUM(ROUND(((data_length + index_length) / 1024 / 1024), 2)) AS size_in_mb
FROM information_schema.TABLES
WHERE table_schema = 'internship_platform';
```

---

## 📝 NOTES

- All `?` placeholders should be replaced with actual values or use prepared statements
- All queries use CURRENT_TIMESTAMP for automatic timestamp insertion
- Foreign keys have cascading delete/update behavior
- UNIQUE constraints prevent duplicate entries
- All text fields support UTF-8 encoding (utf8mb4_unicode_ci)
- Regular backups recommended for production

---

**Last Updated:** December 2025
**Status:** Complete & Production Ready ✅
