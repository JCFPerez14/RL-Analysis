<?php
/**
 * Verify Database Structure
 * This script checks if all required fields exist in the user_info table
 */

include '../frontend/connections.php';

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "Verifying database structure...\n\n";

// Get table structure
$result = $conn->query("DESCRIBE user_info");
if (!$result) {
    die("Error describing table: " . $conn->error . "\n");
}

echo "Current user_info table structure:\n";
echo "=====================================\n";

$fields = [];
while ($row = $result->fetch_assoc()) {
    $fields[] = $row['Field'];
    echo sprintf("%-20s %-20s %-10s %-10s\n", 
        $row['Field'], 
        $row['Type'], 
        $row['Null'], 
        $row['Key']
    );
}

echo "\nRequired fields check:\n";
echo "======================\n";

$requiredFields = [
    'user_id', 'firstname', 'middlename', 'lastname', 'mobile', 'nationality', 'sex', 
    'birth_date', 'academic_year', 'academic_term', 'applying_for', 'strand', 'program', 
    'second_program', 'previous_school', 'school_type', 'family_income', 'father_occupation', 
    'mother_occupation', 'birthplace', 'city', 'province', 'barangay', 'current_address', 
    'photo', 'likelihood'
];

$missingFields = [];
foreach ($requiredFields as $field) {
    if (in_array($field, $fields)) {
        echo "✓ $field\n";
    } else {
        echo "✗ $field (MISSING)\n";
        $missingFields[] = $field;
    }
}

if (empty($missingFields)) {
    echo "\n✅ All required fields are present!\n";
} else {
    echo "\n❌ Missing fields: " . implode(', ', $missingFields) . "\n";
    echo "\nRun the migration script to add missing fields:\n";
    echo "php run_migration.php\n";
}

echo "\nField count: " . count($fields) . " (expected: " . count($requiredFields) . ")\n";

$conn->close();
?>
