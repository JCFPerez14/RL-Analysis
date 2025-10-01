<?php
include 'process_register.php';

echo "Testing calculateEnrollmentLikelihoodML...\n";
$result = calculateEnrollmentLikelihoodML('BSN', 'BSCS', 'BSIT', 'Freshman', 'STEM', '250000', 'Female', 'Filipino');
echo "Result: " . $result . "%\n";
?>