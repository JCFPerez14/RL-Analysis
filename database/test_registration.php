<?php
/**
 * Test Registration Process
 * This script tests the database connection and new registration fields
 */

include '../frontend/connections.php';

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "Testing database connection and registration process...\n\n";

// Test 1: Check if new fields exist
echo "1. Checking if new fields exist in user_info table:\n";
$result = $conn->query("DESCRIBE user_info");
$fields = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $fields[] = $row['Field'];
    }
}

$requiredFields = ['birth_date', 'barangay', 'previous_school', 'school_type'];
foreach ($requiredFields as $field) {
    if (in_array($field, $fields)) {
        echo "✓ Field '$field' exists\n";
    } else {
        echo "✗ Field '$field' is missing\n";
    }
}

// Test 2: Check table structure
echo "\n2. Current user_info table structure:\n";
foreach ($fields as $field) {
    $result = $conn->query("SELECT COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='user_info' AND COLUMN_NAME='$field'");
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo "- $field: " . $row['COLUMN_TYPE'] . "\n";
    }
}

// Test 3: Check if we can insert test data
echo "\n3. Testing data insertion:\n";
try {
    // Test insert with new fields
    $testQuery = "INSERT INTO users (email, password, role, status) VALUES ('test@example.com', 'test123', 'student', 'Not Enrolled')";
    if ($conn->query($testQuery) === TRUE) {
        $user_id = $conn->insert_id;
        echo "✓ Test user created with ID: $user_id\n";
        
        // Test user_info insert with all fields
        $stmt = $conn->prepare("INSERT INTO user_info 
            (user_id, firstname, middlename, lastname, mobile, nationality, sex, birth_date, academic_year, academic_term, applying_for, strand, program, second_program, previous_school, school_type, family_income, father_occupation, mother_occupation, birthplace, city, province, barangay, current_address, photo, likelihood) 
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
        
        $testData = [
            $user_id, 'Test', 'User', 'Name', '09123456789', 'Filipino', 'Male', '2000-01-01',
            '2025-2026', '1st Semester', 'Freshman', 'STEM', 'BSCS', 'BSIT', 'Test School',
            'Public', '100000', 'Engineer', 'Teacher', 'Manila', 'Lipa', 'Batangas', 'Test Barangay',
            'Test Address', 'uploads/default.png', 75.5
        ];
        
        $stmt->bind_param("isssssssssssssssssssssssd", ...$testData);
        
        if ($stmt->execute()) {
            echo "✓ Test user_info record created successfully\n";
            
            // Clean up test data
            $conn->query("DELETE FROM user_info WHERE user_id = $user_id");
            $conn->query("DELETE FROM users WHERE id = $user_id");
            echo "✓ Test data cleaned up\n";
        } else {
            echo "✗ Error creating test user_info record: " . $stmt->error . "\n";
        }
        $stmt->close();
    } else {
        echo "✗ Error creating test user: " . $conn->error . "\n";
    }
} catch (Exception $e) {
    echo "✗ Test failed: " . $e->getMessage() . "\n";
}

// Test 4: Check existing data
echo "\n4. Checking existing user data:\n";
$result = $conn->query("SELECT COUNT(*) as total FROM users");
if ($result) {
    $row = $result->fetch_assoc();
    echo "Total users: " . $row['total'] . "\n";
}

$result = $conn->query("SELECT COUNT(*) as total FROM user_info");
if ($result) {
    $row = $result->fetch_assoc();
    echo "Total user_info records: " . $row['total'] . "\n";
}

echo "\nDatabase test completed!\n";
$conn->close();
?>
