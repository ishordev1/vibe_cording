<?php
// Test script to create certificates and offer_letters tables if they don't exist

require_once 'config/db.php';

// Check if certificates table exists
$check_certs = $conn->query("SHOW TABLES LIKE 'certificates'");
if($check_certs->num_rows == 0) {
    echo "Creating certificates table...<br>";
    $conn->query("CREATE TABLE IF NOT EXISTS certificates (
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
    )");
    echo "✓ Certificates table created successfully!<br>";
} else {
    echo "✓ Certificates table already exists<br>";
}

// Check if offer_letters table exists
$check_letters = $conn->query("SHOW TABLES LIKE 'offer_letters'");
if($check_letters->num_rows == 0) {
    echo "Creating offer_letters table...<br>";
    $conn->query("CREATE TABLE IF NOT EXISTS offer_letters (
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
    )");
    echo "✓ Offer letters table created successfully!<br>";
} else {
    echo "✓ Offer letters table already exists<br>";
}

echo "<br><strong>Database setup complete!</strong>";
?>
