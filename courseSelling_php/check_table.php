<?php
require 'config/database.php';

echo "Checking module_files table...\n";

$result = $conn->query("SHOW TABLES LIKE 'module_files'");
if($result->num_rows > 0) {
    echo "✓ Table exists!\n\n";
    echo "Table structure:\n";
    $cols = $conn->query("DESCRIBE module_files");
    while($row = $cols->fetch_assoc()) {
        echo "  - " . $row['Field'] . " (" . $row['Type'] . ")\n";
    }
} else {
    echo "✗ Table does NOT exist\n";
}
?>
