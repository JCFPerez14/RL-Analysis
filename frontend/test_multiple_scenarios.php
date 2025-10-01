<?php
include 'process_register.php';

echo "Testing Multiple Scenarios with ML Integration:\n\n";

$test_cases = [
    [
        'name' => 'High Likelihood - BSN, Female, High Income',
        'program' => 'BSN',
        'second_program' => 'BSCS',
        'third_program' => 'BSIT',
        'applying_for' => 'Freshman',
        'strand' => 'STEM',
        'family_income' => '500000',
        'sex' => 'Female',
        'nationality' => 'Filipino'
    ],
    [
        'name' => 'Medium Likelihood - BSIT, Male, Medium Income',
        'program' => 'BSIT',
        'second_program' => 'BSCS',
        'third_program' => 'BSCE',
        'applying_for' => 'Transferee',
        'strand' => 'ABM',
        'family_income' => '150000',
        'sex' => 'Male',
        'nationality' => 'Filipino'
    ],
    [
        'name' => 'Lower Likelihood - BSCE, Low Income',
        'program' => 'BSCE',
        'second_program' => 'BSIT',
        'third_program' => '',
        'applying_for' => 'Cross Enrollee',
        'strand' => 'HUMSS',
        'family_income' => '30000',
        'sex' => 'Prefer not to say',
        'nationality' => 'American'
    ]
];

foreach ($test_cases as $case) {
    echo "=== " . $case['name'] . " ===\n";
    echo "Programs: " . $case['program'] . " → " . $case['second_program'] . " → " . $case['third_program'] . "\n";
    echo "Demographics: " . $case['sex'] . ", " . $case['nationality'] . ", Income: ₱" . $case['family_income'] . "\n";
    
    $result = calculateEnrollmentLikelihoodML(
        $case['program'],
        $case['second_program'], 
        $case['third_program'],
        $case['applying_for'],
        $case['strand'],
        $case['family_income'],
        $case['sex'],
        $case['nationality']
    );
    
    echo "Predicted Enrollment Likelihood: " . $result . "%\n\n";
}

echo "All ML integration tests completed successfully!\n";
?>