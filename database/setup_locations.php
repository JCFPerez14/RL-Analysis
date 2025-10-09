<?php
/**
 * Setup Location Database
 * This script creates and populates all location tables
 */

echo "Setting up location database...\n\n";

// Step 1: Create tables
echo "1. Creating location tables...\n";
$sql = file_get_contents('create_location_tables.sql');
if ($sql === false) {
    die("❌ Could not read create_location_tables.sql\n");
}

include '../frontend/connections.php';
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error . "\n");
}

// Split SQL into individual statements
$statements = array_filter(array_map('trim', explode(';', $sql)));

foreach ($statements as $statement) {
    if (!empty($statement) && !preg_match('/^(--|#|\/\*)/', $statement)) {
        if ($conn->query($statement) === TRUE) {
            if (stripos($statement, 'CREATE TABLE') !== false) {
                preg_match('/CREATE TABLE.*?`(\w+)`/', $statement, $matches);
                if (isset($matches[1])) {
                    echo "✓ Created table: " . $matches[1] . "\n";
                }
            } elseif (stripos($statement, 'ALTER TABLE') !== false) {
                echo "✓ Updated user_info table\n";
            }
        } else {
            // Ignore errors for tables that might already exist
            if (stripos($conn->error, 'already exists') === false && 
                stripos($conn->error, 'Duplicate column name') === false) {
                echo "⚠️  Warning: " . $conn->error . "\n";
            }
        }
    }
}

echo "\n2. Populating location data...\n";
include 'populate_locations.php';

echo "\n✅ Location database setup completed!\n";
echo "\nNext steps:\n";
echo "1. Update registration form to use location dropdowns\n";
echo "2. Update process_register.php to use location IDs\n";
echo "3. Test the registration process\n";
?>
