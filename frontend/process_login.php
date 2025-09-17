<?php
session_start();
include 'connections.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Prepare query to get user info from users and user_info tables
    $stmt = $conn->prepare("
        SELECT users.id, users.email, users.password, users.role, 
               user_info.program, user_info.firstname
        FROM users
        LEFT JOIN user_info ON users.id = user_info.user_id
        WHERE users.email = ?
    ");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Verify the hashed password
        if (password_verify($password, $user['password'])) {
            // Password correct, set session variables
            $_SESSION['user'] = [
                'id' => $user['id'],
                'email' => $user['email'],
                'role' => $user['role'],
                'program' => $user['program'] ?? 'N/A',
                'firstname' => $user['firstname'] ?? 'User'
            ];

            // Redirect based on role
            if ($user['role'] === 'admin') {
                header("Location: admin.php");
            } else {
                header("Location: index.php");
            }
            exit();
        } else {
            // Password incorrect
            header("Location: login.php?error=Invalid+email+or+password");
            exit();
        }
    } else {
        // User not found
        header("Location: login.php?error=Invalid+email+or+password");
        exit();
    }
} else {
    // If not POST request, redirect to login
    header("Location: login.php");
    exit();
}
