<?php
/**
 * Get Cities API
 * Returns cities for a given province
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

if (!isset($_GET['province_id']) || empty($_GET['province_id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Province ID is required']);
    exit();
}

$province_id = intval($_GET['province_id']);

try {
    $stmt = $conn->prepare("SELECT id, city_name, city_type FROM cities WHERE province_id = ? ORDER BY city_name");
    $stmt->bind_param("i", $province_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $cities = [];
    while ($row = $result->fetch_assoc()) {
        $cities[] = [
            'id' => $row['id'],
            'city_name' => $row['city_name'],
            'city_type' => $row['city_type']
        ];
    }
    
    echo json_encode($cities);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database query failed: ' . $e->getMessage()]);
}

$stmt->close();
$conn->close();
?>
