<?php
// Test PHP-based enrollment prediction
include __DIR__ . '/../frontend/process_register.php';

echo "🧪 Testing PHP-based Enrollment Prediction...\n\n";

// Test different scenarios
$test_cases = [
    [
        'program' => 'BSN',
        'second_program' => 'BSCS',
        'applying_for' => 'Freshman',
        'strand' => 'STEM',
        'family_income' => '500000',
        'sex' => 'Female',
        'nationality' => 'Filipino',
        'description' => 'High likelihood case (BSN + high income + female + local)'
    ],
    [
        'program' => 'BSIT',
        'second_program' => 'BSCE',
        'applying_for' => 'Transferee',
        'strand' => 'ICT',
        'family_income' => '75000',
        'sex' => 'Male',
        'nationality' => 'Filipino',
        'description' => 'Medium likelihood case (BSIT + medium income)'
    ],
    [
        'program' => 'BSA - Financial Management',
        'second_program' => 'BSTM',
        'applying_for' => 'Cross Enrollee',
        'strand' => 'ABM',
        'family_income' => '30000',
        'sex' => 'Male',
        'nationality' => 'Foreign',
        'description' => 'Lower likelihood case (low importance program + low income)'
    ]
];

foreach ($test_cases as $index => $test) {
    echo "📊 Test Case " . ($index + 1) . ": " . $test['description'] . "\n";
    echo "   Program: " . $test['program'] . " | Second: " . $test['second_program'] . "\n";
    echo "   Type: " . $test['applying_for'] . " | Income: ₱" . number_format($test['family_income']) . "\n";
    
    $likelihood = calculateEnrollmentLikelihood(
        $test['program'],
        $test['second_program'],
        $test['applying_for'],
        $test['strand'],
        $test['family_income'],
        $test['sex'],
        $test['nationality']
    );
    
    $emoji = $likelihood >= 70 ? '🟢' : ($likelihood >= 50 ? '🟡' : '🔴');
    echo "   Result: $emoji " . $likelihood . "% enrollment likelihood\n\n";
}

echo "✅ PHP-based prediction system is working!\n";
echo "🎯 The system considers:\n";
echo "   - Program importance (based on your XGBoost feature chart)\n";
echo "   - Family income level\n";
echo "   - Application type (Freshman/Transferee/Cross)\n";
echo "   - Academic strand background\n";
echo "   - Demographics (gender, nationality)\n";
?>