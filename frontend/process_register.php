<?php
include 'connections.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect POST data (sanitize/validate as needed)
    $firstname = $_POST['firstname'];
    $middlename = $_POST['middlename'];
    $lastname = $_POST['lastname'];
    $email = $_POST['email'];
    $mobile = $_POST['mobile'];
    $password = $_POST['password'];
    $nationality = $_POST['nationality'];
    $sex = $_POST['sex'];
    $academic_year = $_POST['academic_year'];
    $academic_term = $_POST['academic_term'];
    $applying_for = $_POST['applying_for'];
    $strand = $_POST['strand'];
    $program = $_POST['program'];
    $second_program = $_POST['second_program'];
    $family_income = $_POST['family_income'];
    $father_occupation = $_POST['father_occupation'];
    $mother_occupation = $_POST['mother_occupation'];
    $birthplace = $_POST['birthplace'];
    $city = $_POST['city'];
    $province = $_POST['province'];
    $current_address = $_POST['current_address'];

    // Check if email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
    if ($stmt === false) {
        die("Prepare failed: " . htmlspecialchars($conn->error));
    }
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<script>alert('Email already registered! Please use another email.'); window.location.href='register.php';</script>";
        exit();
    }
    $stmt->close();

    // Hash the password securely
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert into users (auth table)
    $stmt = $conn->prepare("INSERT INTO users (email, password, role, status) VALUES (?, ?, 'student', 'Not Enrolled')");
    if ($stmt === false) {
        die("Prepare failed: " . htmlspecialchars($conn->error));
    }
    $stmt->bind_param("ss", $email, $hashedPassword);

    if ($stmt->execute()) {
        $user_id = $conn->insert_id; // get last inserted user id

        // Insert into user_info table
        $stmt2 = $conn->prepare("INSERT INTO user_info 
            (user_id, firstname, middlename, lastname, mobile, nationality, sex, academic_year, academic_term, applying_for, strand, program, second_program, family_income, father_occupation, mother_occupation, birthplace, city, province, current_address) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        if ($stmt2 === false) {
            die("Prepare failed: " . htmlspecialchars($conn->error));
        }

        $types = "i" . str_repeat("s", 19);
        $stmt2->bind_param($types, 
            $user_id, $firstname, $middlename, $lastname, $mobile, $nationality, $sex, $academic_year, $academic_term, 
            $applying_for, $strand, $program, $second_program, $family_income, $father_occupation, $mother_occupation, 
            $birthplace, $city, $province, $current_address);

        if ($stmt2->execute()) {
            header("Location: login.php");
            exit();
        } else {
            echo "Error inserting user info: " . $stmt2->error;
            // Optionally delete the user to keep DB clean
            $conn->query("DELETE FROM users WHERE id = $user_id");
        }

        $stmt2->close();
    } else {
        echo "Error inserting user: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
