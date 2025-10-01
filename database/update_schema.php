<?php
// Database schema update script
include __DIR__ . '/../frontend/connections.php';

try {
    // Add likelihood column if it doesn't exist
    $sql = "ALTER TABLE user_info ADD COLUMN likelihood DECIMAL(5,2) DEFAULT NULL COMMENT 'ML predicted enrollment likelihood (0-100)'";
    
    if ($conn->query($sql) === TRUE) {
        echo "✅ Likelihood column added successfully to user_info table.\n";
    } else {
        // Check if column already exists
        if (strpos($conn->error, 'Duplicate column name') !== false) {
            echo "ℹ️ Likelihood column already exists in user_info table.\n";
        } else {
            echo "❌ Error adding likelihood column: " . $conn->error . "\n";
        }
    }
    
    $conn->close();
    
} catch (Exception $e) {
    echo "❌ Exception: " . $e->getMessage() . "\n";
}
?>