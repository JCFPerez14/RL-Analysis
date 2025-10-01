<?php
// Test the ML integration directly
include 'connections.php';

// Sample test data
$test_data = [
    'program' => 'BSN',
    'second_program' => 'BSCS', 
    'third_program' => 'BSIT',
    'applying_for' => 'Freshman',
    'strand' => 'STEM',
    'family_income' => '250000',
    'sex' => 'Female',
    'nationality' => 'Filipino'
];

echo "Testing ML Integration...\n\n";

/**
 * Calculate enrollment likelihood using Python XGBoost ML model
 * Uses the 88p_sir_ap_best_enrollment_model.pkl
 */
function calculateEnrollmentLikelihoodML($program, $second_program, $third_program, $applying_for, $strand, $family_income, $sex, $nationality) {
    try {
        // Prepare data for the Python script
        $inputData = [
            'program' => $program,
            'second_program' => $second_program,
            'third_program' => $third_program,
            'applying_for' => $applying_for,
            'strand' => $strand,
            'family_income' => $family_income,
            'sex' => $sex,
            'nationality' => $nationality
        ];
        
        $jsonData = json_encode($inputData);
        $modelPath = __DIR__ . '/../server/Models/88p_sir_ap_best_enrollment_model.pkl';
        $scriptPath = __DIR__ . '/../server/predict_enrollment.py';
        
        // Use the configured Python executable
        $pythonExe = 'C:/Users/Zabdiel/AppData/Local/Programs/Python/Python310/python.exe';
        
        // Escape the JSON data for command line
        $escapedJsonData = escapeshellarg($jsonData);
        $escapedModelPath = escapeshellarg($modelPath);
        $escapedScriptPath = escapeshellarg($scriptPath);
        
        // Execute Python script
        $command = "$pythonExe $escapedScriptPath $escapedModelPath $escapedJsonData";
        echo "Command: $command\n";
        echo "Escaped JSON: $escapedJsonData\n";
        
        $output = shell_exec($command);
        
        if ($output === null) {
            echo "Python script execution failed: No output\n";
            return null;
        }
        
        echo "Raw output: $output\n";
        
        // Parse the JSON output
        $result = json_decode(trim($output), true);
        
        if ($result === null || isset($result['error'])) {
            echo "Python script error: " . ($result['error'] ?? 'JSON parse error') . "\n";
            return null;
        }
        
        if (isset($result['likelihood'])) {
            return floatval($result['likelihood']);
        } else {
            echo "Missing likelihood in Python output\n";
            return null;
        }
        
    } catch (Exception $e) {
        echo "ML Calculation Error: " . $e->getMessage() . "\n";
        return null;
    }
}

// Test the function
$likelihood = calculateEnrollmentLikelihoodML(
    $test_data['program'],
    $test_data['second_program'], 
    $test_data['third_program'],
    $test_data['applying_for'],
    $test_data['strand'],
    $test_data['family_income'],
    $test_data['sex'],
    $test_data['nationality']
);

echo "\nTest Results:\n";
echo "Input: " . json_encode($test_data, JSON_PRETTY_PRINT) . "\n";
echo "Predicted Likelihood: " . ($likelihood !== null ? $likelihood . "%" : "Failed") . "\n";
?>