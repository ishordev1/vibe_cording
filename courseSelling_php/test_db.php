<?php
require 'config/database.php';

echo "Certificates Table Structure:\n";
echo "================================\n";

$result = $conn->query('DESCRIBE certificates');
while($row = $result->fetch_assoc()) {
    echo $row['Field'] . ' - ' . $row['Type'] . ' (' . $row['Null'] . ') ' . $row['Key'] . "\n";
}

echo "\n\nSample Data:\n";
echo "================================\n";

$data = $conn->query('SELECT * FROM certificates LIMIT 3');
while($row = $data->fetch_assoc()) {
    echo "ID: " . $row['id'] . "\n";
    echo "Application ID: " . $row['application_id'] . "\n";
    echo "Filename: " . $row['filename'] . "\n";
    echo "Requested At: " . $row['requested_at'] . "\n";
    echo "Generated At: " . $row['generated_at'] . "\n";
    echo "---\n";
}
?>
