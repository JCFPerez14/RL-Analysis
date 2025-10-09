<?php
session_start();
include 'connections.php';

// Debug script to check users and user_info tables
echo "<h2>Database Debug Information</h2>";

echo "<h3>Users Table:</h3>";
$users_query = "SELECT id, email, role, status FROM users ORDER BY id";
$users_result = $conn->query($users_query);

if ($users_result && $users_result->num_rows > 0) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>ID</th><th>Email</th><th>Role</th><th>Status</th></tr>";
    while ($row = $users_result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['id']) . "</td>";
        echo "<td>" . htmlspecialchars($row['email']) . "</td>";
        echo "<td>" . htmlspecialchars($row['role']) . "</td>";
        echo "<td>" . htmlspecialchars($row['status']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No users found or error: " . $conn->error . "</p>";
}

echo "<h3>User Info Table:</h3>";
$user_info_query = "SELECT id, user_id, firstname, lastname, program FROM user_info ORDER BY id";
$user_info_result = $conn->query($user_info_query);

if ($user_info_result && $user_info_result->num_rows > 0) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>ID</th><th>User ID</th><th>First Name</th><th>Last Name</th><th>Program</th></tr>";
    while ($row = $user_info_result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['id']) . "</td>";
        echo "<td>" . htmlspecialchars($row['user_id']) . "</td>";
        echo "<td>" . htmlspecialchars($row['firstname']) . "</td>";
        echo "<td>" . htmlspecialchars($row['lastname']) . "</td>";
        echo "<td>" . htmlspecialchars($row['program']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No user_info records found or error: " . $conn->error . "</p>";
}

echo "<h3>Foreign Key Check:</h3>";
$fk_query = "SELECT u.id as user_id, ui.id as info_id, u.email, ui.firstname, ui.lastname 
             FROM users u 
             LEFT JOIN user_info ui ON u.id = ui.user_id 
             WHERE u.role = 'student'
             ORDER BY u.id";
$fk_result = $conn->query($fk_query);

if ($fk_result && $fk_result->num_rows > 0) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>User ID</th><th>Info ID</th><th>Email</th><th>First Name</th><th>Last Name</th><th>Status</th></tr>";
    while ($row = $fk_result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['user_id']) . "</td>";
        echo "<td>" . htmlspecialchars($row['info_id'] ?? 'NULL') . "</td>";
        echo "<td>" . htmlspecialchars($row['email']) . "</td>";
        echo "<td>" . htmlspecialchars($row['firstname'] ?? 'NULL') . "</td>";
        echo "<td>" . htmlspecialchars($row['lastname'] ?? 'NULL') . "</td>";
        echo "<td>" . ($row['info_id'] ? 'Has Info' : 'Missing Info') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No data found or error: " . $conn->error . "</p>";
}

$conn->close();
?>
