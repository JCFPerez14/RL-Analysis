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

    // ✅ Insert into users
    $stmt = $conn->prepare("INSERT INTO users (email, password, status) VALUES (?, ?, 'Not Enrolled')");
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $user_id = $stmt->insert_id;
    $stmt->close();

    // ✅ Insert into user_info (with photo)
    $stmt2 = $conn->prepare("INSERT INTO user_info 
        (user_id, firstname, middlename, lastname, mobile, nationality, sex, academic_year, academic_term, applying_for, strand, program, second_program, family_income, father_occupation, mother_occupation, birthplace, city, province, current_address, photo) 
        VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");

    $stmt2->bind_param("issssssssssssssssssss", 
        $user_id, $firstname, $middlename, $lastname, $mobile, $nationality, $sex,
        $academic_year, $academic_term, $applying_for, $strand, $program, $second_program,
        $family_income, $father_occupation, $mother_occupation, $birthplace, $city, $province,
        $current_address, $photoPath
    );

    if ($stmt2->execute()) {
        header("Location: login.php?success=1");
    } else {
        echo "Error: " . $stmt2->error;
    }

    $stmt2->close();
    $conn->close();
}
?>
