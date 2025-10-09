<?php
/**
 * Database Migration Script
 * This script adds missing fields to the user_info table
 */

include '../frontend/connections.php';

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "Starting database migration...\n";

try {
    // Add missing fields to user_info table
    $queries = [
        "ALTER TABLE user_info ADD COLUMN birth_date DATE NULL AFTER sex",
        "ALTER TABLE user_info ADD COLUMN barangay VARCHAR(255) NULL AFTER city", 
        "ALTER TABLE user_info ADD COLUMN previous_school VARCHAR(255) NULL AFTER second_program",
        "ALTER TABLE user_info ADD COLUMN school_type ENUM('Public', 'Private') NULL AFTER previous_school",
        "ALTER TABLE user_info MODIFY COLUMN likelihood DECIMAL(5,2) NULL",
        "CREATE INDEX idx_user_info_birth_date ON user_info(birth_date)",
        "CREATE INDEX idx_user_info_previous_school ON user_info(previous_school)",
        "CREATE INDEX idx_user_info_school_type ON user_info(school_type)"
    ];

    foreach ($queries as $query) {
        if ($conn->query($query) === TRUE) {
            echo "✓ Query executed successfully: " . substr($query, 0, 50) . "...\n";
        } else {
            echo "✗ Error executing query: " . $conn->error . "\n";
            echo "Query: " . $query . "\n";
        }
    }

    // Update existing records with default values
    $updateQuery = "UPDATE user_info SET 
        birth_date = COALESCE(birth_date, '2000-01-01'),
        barangay = COALESCE(barangay, 'Unknown'),
        previous_school = COALESCE(previous_school, 'Previous School'),
        school_type = COALESCE(school_type, 'Public')
        WHERE birth_date IS NULL OR barangay IS NULL OR previous_school IS NULL OR school_type IS NULL";

    if ($conn->query($updateQuery) === TRUE) {
        echo "✓ Updated existing records with default values\n";
    } else {
        echo "✗ Error updating records: " . $conn->error . "\n";
    }

    echo "\nMigration completed successfully!\n";
    
    // Verify the changes
    echo "\nVerifying table structure...\n";
    $result = $conn->query("DESCRIBE user_info");
    if ($result->num_rows > 0) {
        echo "Current user_info table structure:\n";
        while($row = $result->fetch_assoc()) {
            echo "- " . $row['Field'] . " (" . $row['Type'] . ")\n";
        }
    }

} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
}

$conn->close();
?>
