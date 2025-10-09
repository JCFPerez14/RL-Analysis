<?php
/**
 * Get Barangays API
 * Returns barangays for a given city
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

include 'connections.php';

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit();
}

if (!isset($_GET['city_id']) || empty($_GET['city_id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'City ID is required']);
    exit();
}

$city_id = intval($_GET['city_id']);

try {
    $stmt = $conn->prepare("SELECT id, barangay_name FROM barangays WHERE city_id = ? ORDER BY barangay_name");
    $stmt->bind_param("i", $city_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $barangays = [];
    while ($row = $result->fetch_assoc()) {
        $barangays[] = [
            'id' => $row['id'],
            'barangay_name' => $row['barangay_name']
        ];
    }
    
    echo json_encode($barangays);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database query failed: ' . $e->getMessage()]);
}

$stmt->close();
$conn->close();
?>
