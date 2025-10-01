<?php
// Test the complete registration process
include __DIR__ . '/../frontend/connections.php';

echo "🧪 Testing Complete Registration Process with ML Prediction...\n\n";

// Simulate POST data (like a real registration)
$_POST = [
    'firstname' => 'Juan',
    'middlename' => 'Santos',
    'lastname' => 'Cruz',
    'email' => 'test_ml_' . time() . '@example.com', // Unique email
    'password' => 'password123',
    'mobile' => '09123456789',
    'nationality' => 'Filipino',
    'sex' => 'Male',
    'academic_year' => '2025-2026',
    'academic_term' => '1st Semester',
    'applying_for' => 'Freshman',
    'strand' => 'STEM',
    'program' => 'BSCS',
    'second_program' => 'BSIT',
    'family_income' => '150000',
    'father_occupation' => 'Engineer',
    'mother_occupation' => 'Teacher',
    'birthplace' => 'Manila',
    'city' => 'Lipa',
    'province' => 'Batangas',
    'current_address' => 'Lipa City, Batangas'
];

$_SERVER['REQUEST_METHOD'] = 'POST';
$_FILES['photo'] = ['name' => '', 'tmp_name' => '']; // No photo upload

echo "👤 Test Student Data:\n";
echo "   Name: " . $_POST['firstname'] . " " . $_POST['lastname'] . "\n";
echo "   Email: " . $_POST['email'] . "\n";
echo "   Program: " . $_POST['program'] . " (1st choice), " . $_POST['second_program'] . " (2nd choice)\n";
echo "   Type: " . $_POST['applying_for'] . "\n";
echo "   Income: ₱" . number_format($_POST['family_income']) . "\n\n";

// Capture output to prevent headers and redirects
ob_start();

try {
    include __DIR__ . '/../frontend/process_register.php';
} catch (Exception $e) {
    echo "❌ Error during registration: " . $e->getMessage() . "\n";
}

$output = ob_get_clean();

// Check if student was created and has likelihood
$email = $_POST['email'];
$result = $conn->query("SELECT users.id, users.email, users.status, user_info.firstname, user_info.lastname, user_info.program, user_info.likelihood 
                       FROM users 
                       LEFT JOIN user_info ON users.id = user_info.user_id 
                       WHERE users.email = '$email'");

if ($result && $row = $result->fetch_assoc()) {
    echo "✅ Registration Successful!\n";
    echo "   Student ID: " . $row['id'] . "\n";
    echo "   Name: " . $row['firstname'] . " " . $row['lastname'] . "\n";
    echo "   Program: " . $row['program'] . "\n";
    echo "   Status: " . $row['status'] . "\n";
    echo "   🎯 ML Predicted Likelihood: " . ($row['likelihood'] ?? 'Not calculated') . "%\n\n";
    
    if ($row['likelihood']) {
        $likelihood = floatval($row['likelihood']);
        if ($likelihood >= 70) {
            echo "🟢 HIGH ENROLLMENT PROBABILITY - Recommended for admission!\n";
        } elseif ($likelihood >= 50) {
            echo "🟡 MODERATE ENROLLMENT PROBABILITY - Good candidate.\n";
        } else {
            echo "🔴 LOW ENROLLMENT PROBABILITY - May need additional support.\n";
        }
    }
    
    // Clean up test data
    $conn->query("DELETE FROM user_info WHERE user_id = " . $row['id']);
    $conn->query("DELETE FROM users WHERE id = " . $row['id']);
    echo "\n🧹 Test data cleaned up.\n";
    
} else {
    echo "❌ Registration failed - no student record found.\n";
    if ($output) {
        echo "Debug output: " . $output . "\n";
    }
}

echo "\n✅ Integration test completed!\n";
?>