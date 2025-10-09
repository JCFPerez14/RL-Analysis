<?php
/**
 * Populate Location Tables
 * This script populates the provinces, cities, and barangays tables with Philippine data
 */

include '../frontend/connections.php';

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "Populating location tables with Philippine data...\n\n";

try {
    // Clear existing data
    $conn->query("DELETE FROM barangays");
    $conn->query("DELETE FROM cities");
    $conn->query("DELETE FROM provinces");
    echo "✓ Cleared existing location data\n";

    // Sample Philippine provinces data
    $provinces = [
        ['BAT', 'Batangas', 'Region IV-A'],
        ['CAV', 'Cavite', 'Region IV-A'],
        ['LAG', 'Laguna', 'Region IV-A'],
        ['QUE', 'Quezon', 'Region IV-A'],
        ['RIZ', 'Rizal', 'Region IV-A'],
        ['MNL', 'Metro Manila', 'NCR'],
        ['BUL', 'Bulacan', 'Region III'],
        ['PAM', 'Pampanga', 'Region III'],
        ['NUE', 'Nueva Ecija', 'Region III'],
        ['TAR', 'Tarlac', 'Region III'],
        ['ZAM', 'Zambales', 'Region III'],
        ['AUR', 'Aurora', 'Region III'],
        ['BAN', 'Bataan', 'Region III'],
        ['ORI', 'Oriental Mindoro', 'Region IV-B'],
        ['OCM', 'Occidental Mindoro', 'Region IV-B'],
        ['MAR', 'Marinduque', 'Region IV-B'],
        ['ROM', 'Romblon', 'Region IV-B'],
        ['PAL', 'Palawan', 'Region IV-B']
    ];

    // Insert provinces
    $stmt = $conn->prepare("INSERT INTO provinces (province_code, province_name, region) VALUES (?, ?, ?)");
    foreach ($provinces as $province) {
        $stmt->bind_param("sss", $province[0], $province[1], $province[2]);
        $stmt->execute();
    }
    echo "✓ Inserted " . count($provinces) . " provinces\n";

    // Sample cities for Batangas (most relevant for NU Lipa)
    $cities = [
        // Batangas Province
        ['BAT001', 'Lipa City', 'BAT', 'City'],
        ['BAT002', 'Batangas City', 'BAT', 'City'],
        ['BAT003', 'Tanauan City', 'BAT', 'City'],
        ['BAT004', 'Malvar', 'BAT', 'Municipality'],
        ['BAT005', 'Sto. Tomas', 'BAT', 'Municipality'],
        ['BAT006', 'Calamba City', 'BAT', 'City'],
        ['BAT007', 'Bauan', 'BAT', 'Municipality'],
        ['BAT008', 'Alitagtag', 'BAT', 'Municipality'],
        ['BAT009', 'Balayan', 'BAT', 'Municipality'],
        ['BAT010', 'Balete', 'BAT', 'Municipality'],
        
        // Cavite Province
        ['CAV001', 'Bacoor City', 'CAV', 'City'],
        ['CAV002', 'Cavite City', 'CAV', 'City'],
        ['CAV003', 'Dasmariñas City', 'CAV', 'City'],
        ['CAV004', 'Imus City', 'CAV', 'City'],
        ['CAV005', 'Tagaytay City', 'CAV', 'City'],
        
        // Laguna Province
        ['LAG001', 'Calamba City', 'LAG', 'City'],
        ['LAG002', 'San Pablo City', 'LAG', 'City'],
        ['LAG003', 'Santa Rosa City', 'LAG', 'City'],
        ['LAG004', 'Biñan City', 'LAG', 'City'],
        ['LAG005', 'Cabuyao City', 'LAG', 'City'],
        
        // Metro Manila
        ['MNL001', 'Manila', 'MNL', 'City'],
        ['MNL002', 'Quezon City', 'MNL', 'City'],
        ['MNL003', 'Makati City', 'MNL', 'City'],
        ['MNL004', 'Taguig City', 'MNL', 'City'],
        ['MNL005', 'Pasig City', 'MNL', 'City']
    ];

    // Insert cities
    $stmt = $conn->prepare("INSERT INTO cities (city_code, city_name, province_code, city_type) VALUES (?, ?, (SELECT id FROM provinces WHERE province_code = ?), ?)");
    foreach ($cities as $city) {
        $stmt->bind_param("ssss", $city[0], $city[1], $city[2], $city[3]);
        $stmt->execute();
    }
    echo "✓ Inserted " . count($cities) . " cities\n";

    // Sample barangays for Lipa City (most relevant)
    $barangays = [
        // Lipa City barangays
        ['BAT001001', 'Bagong Pook', 'BAT001'],
        ['BAT001002', 'Balintawak', 'BAT001'],
        ['BAT001003', 'Banaybanay I', 'BAT001'],
        ['BAT001004', 'Banaybanay II', 'BAT001'],
        ['BAT001005', 'Bolbok', 'BAT001'],
        ['BAT001006', 'Bugtong na Pulo', 'BAT001'],
        ['BAT001007', 'Bulacnin', 'BAT001'],
        ['BAT001008', 'Bungahan', 'BAT001'],
        ['BAT001009', 'Callejon', 'BAT001'],
        ['BAT001010', 'Calzada', 'BAT001'],
        ['BAT001011', 'Cumba', 'BAT001'],
        ['BAT001012', 'Dagatan', 'BAT001'],
        ['BAT001013', 'Duhatan', 'BAT001'],
        ['BAT001014', 'Ganado', 'BAT001'],
        ['BAT001015', 'Governador Generoso', 'BAT001'],
        ['BAT001016', 'Halang', 'BAT001'],
        ['BAT001017', 'Inosluban', 'BAT001'],
        ['BAT001018', 'Labac', 'BAT001'],
        ['BAT001019', 'Lalo', 'BAT001'],
        ['BAT001020', 'Lodlod', 'BAT001'],
        ['BAT001021', 'Lumil', 'BAT001'],
        ['BAT001022', 'Malitlit', 'BAT001'],
        ['BAT001023', 'Marawoy', 'BAT001'],
        ['BAT001024', 'Mataasnakahoy', 'BAT001'],
        ['BAT001025', 'Muntingpulo', 'BAT001'],
        ['BAT001026', 'Pagkilatan', 'BAT001'],
        ['BAT001027', 'Poblacion', 'BAT001'],
        ['BAT001028', 'Pinagtongulan', 'BAT001'],
        ['BAT001029', 'Punta', 'BAT001'],
        ['BAT001030', 'Sabang', 'BAT001'],
        ['BAT001031', 'San Carlos', 'BAT001'],
        ['BAT001032', 'San Francisco', 'BAT001'],
        ['BAT001033', 'San Guillermo', 'BAT001'],
        ['BAT001034', 'San Jose', 'BAT001'],
        ['BAT001035', 'San Lucas', 'BAT001'],
        ['BAT001036', 'San Salvador', 'BAT001'],
        ['BAT001037', 'Santo Niño', 'BAT001'],
        ['BAT001038', 'Santo Toribio', 'BAT001'],
        ['BAT001039', 'Sapac', 'BAT001'],
        ['BAT001040', 'Tambo', 'BAT001'],
        ['BAT001041', 'Tibig', 'BAT001'],
        ['BAT001042', 'Tipakan', 'BAT001'],
        
        // Batangas City barangays (sample)
        ['BAT002001', 'Balagtas', 'BAT002'],
        ['BAT002002', 'Balagtasin', 'BAT002'],
        ['BAT002003', 'Bilogo', 'BAT002'],
        ['BAT002004', 'Maapas', 'BAT002'],
        ['BAT002005', 'Mabacong', 'BAT002'],
        
        // Tanauan City barangays (sample)
        ['BAT003001', 'Altura Bata', 'BAT003'],
        ['BAT003002', 'Altura Matanda', 'BAT003'],
        ['BAT003003', 'Ambulong', 'BAT003'],
        ['BAT003004', 'Banjo East', 'BAT003'],
        ['BAT003005', 'Banjo West', 'BAT003']
    ];

    // Insert barangays
    $stmt = $conn->prepare("INSERT INTO barangays (barangay_code, barangay_name, city_code) VALUES (?, ?, (SELECT id FROM cities WHERE city_code = ?))");
    foreach ($barangays as $barangay) {
        $stmt->bind_param("sss", $barangay[0], $barangay[1], $barangay[2]);
        $stmt->execute();
    }
    echo "✓ Inserted " . count($barangays) . " barangays\n";

    echo "\n✅ Location data populated successfully!\n";
    
    // Show summary
    $result = $conn->query("SELECT COUNT(*) as count FROM provinces");
    $provinceCount = $result->fetch_assoc()['count'];
    
    $result = $conn->query("SELECT COUNT(*) as count FROM cities");
    $cityCount = $result->fetch_assoc()['count'];
    
    $result = $conn->query("SELECT COUNT(*) as count FROM barangays");
    $barangayCount = $result->fetch_assoc()['count'];
    
    echo "\nSummary:\n";
    echo "- Provinces: $provinceCount\n";
    echo "- Cities: $cityCount\n";
    echo "- Barangays: $barangayCount\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

$conn->close();
?>
