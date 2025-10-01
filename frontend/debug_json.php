<?php
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

echo "Original array:\n";
var_dump($test_data);

echo "\nJSON encoded:\n";
$json = json_encode($test_data);
echo $json . "\n";

echo "\nEscaped for shell:\n";
$escaped = escapeshellarg($json);
echo $escaped . "\n";

echo "\nDirect test with Python:\n";
$pythonExe = 'C:/Users/Zabdiel/AppData/Local/Programs/Python/Python310/python.exe';
$command = $pythonExe . ' -c "import json; data = json.loads(\'' . str_replace("'", "\\'", $json) . '\'); print(\'Success:\', data)"';
echo "Command: $command\n";
$output = shell_exec($command);
echo "Output: $output\n";
?>