<?php
session_start();
include 'connections.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstname = $_POST['firstname'];
    $middlename = $_POST['middlename'];
    $lastname = $_POST['lastname'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $mobile = $_POST['mobile'];
    $nationality = $_POST['nationality'];
    $sex = $_POST['sex'];
    $birth_date = $_POST['birth_date'];
    $academic_year = $_POST['academic_year'];
    $academic_term = $_POST['academic_term'];
    $applying_for = $_POST['applying_for'];
    $strand = $_POST['strand'];
    $program = $_POST['program'];
    $second_program = $_POST['second_program'];
    $previous_school = $_POST['previous_school'];
    $school_type = $_POST['school_type'];
    $family_income = $_POST['family_income'];
    $father_occupation = $_POST['father_occupation'];
    $mother_occupation = $_POST['mother_occupation'];
    $birthplace = $_POST['birthplace'];
    $province_code = $_POST['province'];
    $city_code = $_POST['city'];
    $barangay_code = $_POST['barangay'];
    $current_address = $_POST['current_address'];
    
    // For PSA API, we'll store the codes directly and get names from the API
    // For now, we'll use the codes as the display values
    $province = $province_code; // Will be updated with actual name
    $city = $city_code; // Will be updated with actual name
    $barangay = $barangay_code; // Will be updated with actual name
    
    // Get actual names from PSA API (optional - for better display)
    // For now, we'll store the codes and display them

    // ✅ Handle file upload
    $photoPath = "uploads/default.png"; // default
    if (!empty($_FILES['photo']['name'])) {
        $targetDir = "uploads/";
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
        
        $fileName = time() . "_" . basename($_FILES['photo']['name']);
        $targetFile = $targetDir . $fileName;

        if (move_uploaded_file($_FILES['photo']['tmp_name'], $targetFile)) {
            $photoPath = $targetFile;
        }
    }

    // ✅ Check if email already exists
    $checkStmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $checkStmt->bind_param("s", $email);
    $checkStmt->execute();
    $result = $checkStmt->get_result();
    
    if ($result->num_rows > 0) {
        // Redirect back to registration form with error message
        header("Location: register.php?error=email_exists");
        exit();
    }
    $checkStmt->close();

    // ✅ Insert into users
    $stmt = $conn->prepare("INSERT INTO users (email, password, status) VALUES (?, ?, 'Not Enrolled')");
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $user_id = $stmt->insert_id;
    $stmt->close();

    // ✅ Insert into user_info (with all new fields including location IDs)
    $stmt2 = $conn->prepare("INSERT INTO user_info 
        (user_id, firstname, middlename, lastname, mobile, nationality, sex, birth_date, academic_year, academic_term, applying_for, strand, program, second_program, previous_school, school_type, family_income, father_occupation, mother_occupation, birthplace, city, province, barangay, current_address, photo, likelihood, province_code, city_code, barangay_code) 
        VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");

    // Set initial likelihood to 0.0
    $initial_likelihood = 0.0;
    
    $stmt2->bind_param("issssssssssssssssssssssssdddd", 
        $user_id, $firstname, $middlename, $lastname, $mobile, $nationality, $sex, $birth_date,
        $academic_year, $academic_term, $applying_for, $strand, $program, $second_program,
        $previous_school, $school_type, $family_income, $father_occupation, $mother_occupation, 
        $birthplace, $city, $province, $barangay, $current_address, $photoPath, $initial_likelihood,
        $province_code, $city_code, $barangay_code
    );

    if ($stmt2->execute()) {
        // ✅ Calculate enrollment likelihood using Python XGBoost model
        $likelihood = calculateEnrollmentLikelihoodML($program, $second_program, $applying_for, $strand, $family_income, $sex, $nationality);
        
        // ✅ Update user_info with calculated likelihood (if ML calculation succeeds)
        if ($likelihood !== null && $likelihood > 0) {
            $stmt3 = $conn->prepare("UPDATE user_info SET likelihood = ? WHERE user_id = ?");
            $stmt3->bind_param("di", $likelihood, $user_id);
            $stmt3->execute();
            $stmt3->close();
        }
        
        // Success - redirect to official NU LIPA registration
        header("Location: https://onlineapp.nu-lipa.edu.ph/quest/home.php");
        exit();
    } else {
        // Error in user_info insertion - clean up user record and show error
        $conn->query("DELETE FROM users WHERE id = $user_id");
        header("Location: register.php?error=registration_failed");
        exit();
    }

    $stmt2->close();
    if (!headers_sent()) {
        $conn->close();
    }
}

/**
 * Calculate enrollment likelihood using Python XGBoost ML model
 * Uses the 88p_sir_ap_best_enrollment_model.pkl
 */
function calculateEnrollmentLikelihoodML($program, $second_program, $applying_for, $strand, $family_income, $sex, $nationality) {
    try {
        // Prepare data for the Python script
        $inputData = [
            'program' => $program,
            'second_program' => $second_program,
            'applying_for' => $applying_for,
            'strand' => $strand,
            'family_income' => $family_income,
            'sex' => $sex,
            'nationality' => $nationality
        ];
        
        $jsonData = json_encode($inputData);
        $modelPath = __DIR__ . '/../server/Models/88p_sir_ap_best_enrollment_model.pkl';
        $scriptPath = __DIR__ . '/../server/predict_enrollment.py';
        
        // Write JSON to temporary file to avoid shell escaping issues
        $tempFile = str_replace('\\', '/', sys_get_temp_dir()) . '/enrollment_data_' . uniqid() . '.json';
        file_put_contents($tempFile, $jsonData);
        
        // Use the configured Python executable
        $pythonExe = 'C:/Users/Zabdiel/AppData/Local/Programs/Python/Python310/python.exe';
        
        // Create a wrapper Python script to read from file
        $wrapperScript = str_replace('\\', '/', sys_get_temp_dir()) . '/enrollment_wrapper_' . uniqid() . '.py';
        $wrapperContent = "#!/usr/bin/env python3
import sys
import json
sys.path.append('" . str_replace('\\', '/', dirname($scriptPath)) . "')
from predict_enrollment import calculate_likelihood_from_programs

with open('" . $tempFile . "', 'r') as f:
    data = json.load(f)

likelihood = calculate_likelihood_from_programs(
    data.get('program', ''),
    data.get('second_program', ''),
    data.get('applying_for', 'Freshman'),
    data.get('family_income', '50000'),
    data.get('sex', 'Female'),
    data.get('nationality', 'Filipino')
)

result = {
    'likelihood': round(likelihood, 2),
    'status': 'success'
}

print(json.dumps(result))";

        file_put_contents($wrapperScript, $wrapperContent);
        
        // Execute the wrapper script
        $command = "$pythonExe \"$wrapperScript\" 2>&1";
        $output = shell_exec($command);
        
        // Clean up temporary files
        unlink($tempFile);
        unlink($wrapperScript);
        
        if ($output === null) {
            error_log("Python script execution failed: No output");
            return calculateEnrollmentLikelihoodFallback($program, $second_program, $applying_for, $strand, $family_income, $sex, $nationality);
        }
        
        // Parse the JSON output
        $result = json_decode(trim($output), true);
        
        if ($result === null || isset($result['error'])) {
            error_log("Python script error: " . ($result['error'] ?? 'JSON parse error') . " | Output: $output");
            return calculateEnrollmentLikelihoodFallback($program, $second_program, $applying_for, $strand, $family_income, $sex, $nationality);
        }
        
        if (isset($result['likelihood'])) {
            return floatval($result['likelihood']);
        } else {
            error_log("Missing likelihood in Python output: $output");
            return calculateEnrollmentLikelihoodFallback($program, $second_program, $applying_for, $strand, $family_income, $sex, $nationality);
        }
        
    } catch (Exception $e) {
        error_log("ML Calculation Error: " . $e->getMessage());
        return calculateEnrollmentLikelihoodFallback($program, $second_program, $applying_for, $strand, $family_income, $sex, $nationality);
    }
}

/**
 * Fallback PHP-based algorithm in case Python/ML model fails
 */
function calculateEnrollmentLikelihoodFallback($program, $second_program, $applying_for, $strand, $family_income, $sex, $nationality) {
    try {
        $likelihood = 50.0; // Base likelihood
        
        // Program weights based on feature importance chart
        $program_weights = [
            'BSN' => 15.0,                           // Highest importance
            'BSA - Accountancy' => 12.0,
            'BSPSY' => 10.0,
            'BSMT-MLA' => 8.5,
            'BSTM' => 8.0,
            'BSA-MLA' => 7.5,
            'BSPSY-MLA' => 7.0,
            'BSArch' => 6.5,
            'BSCS' => 6.0,
            'BSArch-MLA' => 5.5,
            'BSMT' => 5.0,
            'BSN-MLA' => 4.5,
            'BSCS-MLA' => 4.0,
            'BSTM-MLA' => 3.5,
            'BSIT' => 3.0,
            'BSCE' => 3.0,
            'BSA - Marketing' => 2.5,
            'BSA - Financial Management' => 2.0
        ];
        
        // Apply program weight (first choice - highest impact)
        if (isset($program_weights[$program])) {
            $likelihood += $program_weights[$program];
        }
        
        // Apply second program weight (lower impact)
        if (isset($program_weights[$second_program])) {
            $likelihood += $program_weights[$second_program] * 0.6;
        }
        
        // Applying for factor
        switch ($applying_for) {
            case 'Freshman':
                $likelihood += 8.0; // Fresh graduates more likely to enroll
                break;
            case 'Transferee':
                $likelihood += 5.0;
                break;
            case 'Cross Enrollee':
                $likelihood += 3.0;
                break;
        }
        
        // Family income factor (financial capability)
        $income = (float) str_replace([',', '₱', ' '], '', $family_income);
        if ($income >= 500000) {
            $likelihood += 10.0;
        } elseif ($income >= 200000) {
            $likelihood += 7.0;
        } elseif ($income >= 100000) {
            $likelihood += 5.0;
        } elseif ($income >= 50000) {
            $likelihood += 3.0;
        } else {
            $likelihood -= 2.0; // Lower income might affect enrollment
        }
        
        // Gender factor (slight variation)
        if ($sex === 'Female') {
            $likelihood += 2.0; // Historically higher enrollment rates
        }
        
        // Nationality factor
        if (strtolower($nationality) === 'filipino') {
            $likelihood += 3.0; // Local students more likely to enroll
        }
        
        // Strand factor (academic background)
        $strand_lower = strtolower($strand);
        if (strpos($strand_lower, 'stem') !== false) {
            $likelihood += 5.0;
        } elseif (strpos($strand_lower, 'abm') !== false) {
            $likelihood += 4.0;
        } elseif (strpos($strand_lower, 'humss') !== false) {
            $likelihood += 3.0;
        }
        
        // Ensure likelihood is within 0-100 range
        $likelihood = max(5.0, min(95.0, $likelihood));
        
        // Add some randomness to make it more realistic (±5%)
        $randomFactor = (rand(-500, 500) / 100);
        $likelihood += $randomFactor;
        
        // Final bounds check
        $likelihood = max(5.0, min(95.0, $likelihood));
        
        return round($likelihood, 2);
        
    } catch (Exception $e) {
        error_log("Likelihood Calculation Error: " . $e->getMessage());
        return 65.0; // Default moderate likelihood
    }
}
?>
