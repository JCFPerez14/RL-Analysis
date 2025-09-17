<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "nulip_db";  // change this to your actual database name

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to utf8mb4 for proper encoding support
$conn->set_charset("utf8mb4");
?>
