<?php
// Test ML model integration
include __DIR__ . '/../frontend/connections.php';

// Test data
$testData = [
    'user_id' => 999,
    'program' => 'BSCS',
    'second_program' => 'BSIT',
    'applying_for' => 'Freshman',
    'strand' => 'STEM',
    'family_income' => '100000',
    'sex' => 'Male',
    'nationality' => 'Filipino'
];

echo "🧪 Testing ML Model Integration...\n\n";

// Convert data to JSON
$jsonData = json_encode($testData);
echo "📝 Test Data: " . $jsonData . "\n\n";

// Path to Python script and model
$pythonScript = __DIR__ . '/../server/predict_enrollment.py';
$modelPath = __DIR__ . '/../server/Models/88p_sir_ap_best_enrollment_model.pkl';

echo "🐍 Python Script: " . $pythonScript . "\n";
echo "🤖 Model Path: " . $modelPath . "\n\n";

// Check if files exist
if (!file_exists($pythonScript)) {
    echo "❌ Python script not found: $pythonScript\n";
    exit(1);
}

if (!file_exists($modelPath)) {
    echo "❌ Model file not found: $modelPath\n";
    echo "📁 Available model files:\n";
    $modelDir = dirname($modelPath);
    if (is_dir($modelDir)) {
        $files = scandir($modelDir);
        foreach ($files as $file) {
            if (strpos($file, '.pkl') !== false) {
                echo "   - $file\n";
            }
        }
    }
    exit(1);
}

// Escape the JSON data for command line
$escapedData = escapeshellarg($jsonData);

// Execute Python script
$command = "python \"$pythonScript\" \"$modelPath\" $escapedData 2>&1";
echo "🔧 Command: $command\n\n";

$output = shell_exec($command);
echo "📤 Raw Output: " . $output . "\n\n";

// Parse the output
$result = json_decode(trim($output), true);

if ($result && isset($result['likelihood'])) {
    echo "✅ Success! Predicted Enrollment Likelihood: " . $result['likelihood'] . "%\n";
} else {
    echo "❌ Failed to get prediction:\n";
    echo "Output: " . $output . "\n";
    if ($result && isset($result['error'])) {
        echo "Error: " . $result['error'] . "\n";
    }
}
?>