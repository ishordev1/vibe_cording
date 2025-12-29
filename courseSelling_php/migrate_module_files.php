<?php
require 'config/database.php';

// Check if module_files table already exists
$result = $conn->query("SHOW TABLES LIKE 'module_files'");
if($result->num_rows == 0) {
    // Create the module_files table
    $sql = "CREATE TABLE module_files (
        id INT PRIMARY KEY AUTO_INCREMENT,
        module_id INT NOT NULL,
        file_name VARCHAR(255) NOT NULL,
        file_path VARCHAR(255) NOT NULL,
        file_type VARCHAR(50),
        file_size INT,
        upload_order INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (module_id) REFERENCES modules(id) ON DELETE CASCADE
    )";
    
    if($conn->query($sql)) {
        echo "✓ module_files table created successfully!";
    } else {
        echo "✗ Error creating module_files table: " . $conn->error;
    }
} else {
    echo "✓ module_files table already exists!";
}
?>
