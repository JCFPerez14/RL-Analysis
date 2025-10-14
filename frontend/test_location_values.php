<?php
// Simple test page to verify location values are being submitted correctly
session_start();
include 'connections.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    echo "<h2>Location Values Test Results</h2>";
    echo "<h3>Form Data Received:</h3>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Field</th><th>Value</th></tr>";
    
    $locationFields = [
        'province' => 'Province Name',
        'city' => 'City Name', 
        'barangay' => 'Barangay Name',
        'province_code' => 'Province Code',
        'city_code' => 'City Code',
        'barangay_code' => 'Barangay Code'
    ];
    
    foreach ($locationFields as $field => $label) {
        $value = isset($_POST[$field]) ? $_POST[$field] : 'Not set';
        echo "<tr><td><strong>$label</strong></td><td>$value</td></tr>";
    }
    
    echo "</table>";
    
    echo "<h3>Summary:</h3>";
    echo "<p><strong>Province:</strong> " . ($_POST['province'] ?? 'Not set') . "</p>";
    echo "<p><strong>City:</strong> " . ($_POST['city'] ?? 'Not set') . "</p>";
    echo "<p><strong>Barangay:</strong> " . ($_POST['barangay'] ?? 'Not set') . "</p>";
    
    echo "<h3>Codes:</h3>";
    echo "<p><strong>Province Code:</strong> " . ($_POST['province_code'] ?? 'Not set') . "</p>";
    echo "<p><strong>City Code:</strong> " . ($_POST['city_code'] ?? 'Not set') . "</p>";
    echo "<p><strong>Barangay Code:</strong> " . ($_POST['barangay_code'] ?? 'Not set') . "</p>";
    
    echo "<hr>";
    echo "<p><a href='test_location_values.php'>Test Again</a> | <a href='register.php'>Back to Registration</a></p>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Location Values Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        select, input { padding: 8px; width: 300px; }
        button { padding: 10px 20px; background: #007bff; color: white; border: none; cursor: pointer; }
        button:hover { background: #0056b3; }
    </style>
</head>
<body>
    <h1>Location Values Test</h1>
    <p>This page tests whether the location dropdowns are saving names instead of codes.</p>
    
    <form method="POST">
        <div class="form-group">
            <label for="province">Province:</label>
            <select name="province" id="province" required onchange="updateCities()">
                <option value="">Loading provinces...</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="city">City/Municipality:</label>
            <select name="city" id="city" required onchange="updateBarangays()" disabled>
                <option value="">Select City/Municipality</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="barangay">Barangay:</label>
            <select name="barangay" id="barangay" required disabled>
                <option value="">Select Barangay</option>
            </select>
        </div>
        
        <!-- Hidden fields for codes -->
        <input type="hidden" name="province_code" id="province_code" value="">
        <input type="hidden" name="city_code" id="city_code" value="">
        <input type="hidden" name="barangay_code" id="barangay_code" value="">
        
        <button type="submit">Test Location Values</button>
    </form>
    
    <script>
        // PSA API Configuration
        const PSA_API_BASE = 'https://psgc.gitlab.io/api';
        
        // Cache for API data
        let provincesCache = null;
        let citiesCache = {};
        let barangaysCache = {};

        // Initialize provinces dropdown from PSA API
        async function initializeProvinces() {
            const provinceSelect = document.getElementById('province');
            
            try {
                provinceSelect.innerHTML = '<option value="">Loading provinces...</option>';
                
                if (provincesCache) {
                    populateProvinceOptions(provincesCache);
                    return;
                }
                
                const response = await fetch(`${PSA_API_BASE}/provinces/`);
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const data = await response.json();
                provincesCache = data;
                populateProvinceOptions(data);
                
            } catch (error) {
                console.error('Error loading provinces:', error);
                provinceSelect.innerHTML = '<option value="">Error loading provinces</option>';
            }
        }

        // Populate province options
        function populateProvinceOptions(provinces) {
            const provinceSelect = document.getElementById('province');
            provinceSelect.innerHTML = '<option value="">Select Province</option>';
            
            const sortedProvinces = provinces.sort((a, b) => a.name.localeCompare(b.name));
            
            sortedProvinces.forEach(province => {
                const option = document.createElement('option');
                option.value = province.name; // Use name instead of code
                option.textContent = province.name;
                option.setAttribute('data-code', province.code); // Store code as data attribute
                provinceSelect.appendChild(option);
            });
        }

        // Update cities based on selected province
        async function updateCities() {
            const provinceSelect = document.getElementById('province');
            const citySelect = document.getElementById('city');
            const barangaySelect = document.getElementById('barangay');
            
            citySelect.innerHTML = '<option value="">Loading cities...</option>';
            barangaySelect.innerHTML = '<option value="">Select Barangay</option>';
            
            citySelect.disabled = true;
            barangaySelect.disabled = true;
            
            const selectedProvinceName = provinceSelect.value;
            const selectedProvinceCode = provinceSelect.selectedOptions[0]?.getAttribute('data-code');
            
            // Update hidden field
            document.getElementById('province_code').value = selectedProvinceCode || '';
            
            if (!selectedProvinceName) {
                citySelect.innerHTML = '<option value="">Select City/Municipality</option>';
                return;
            }
            
            try {
                if (citiesCache[selectedProvinceCode]) {
                    populateCityOptions(citiesCache[selectedProvinceCode]);
                    citySelect.disabled = false;
                    return;
                }
                
                const response = await fetch(`${PSA_API_BASE}/cities-municipalities/`);
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const allCities = await response.json();
                const provinceCities = allCities.filter(city => 
                    city.provinceCode === selectedProvinceCode || 
                    (selectedProvinceCode === '' && !city.provinceCode)
                );
                
                citiesCache[selectedProvinceCode] = provinceCities;
                populateCityOptions(provinceCities);
                citySelect.disabled = false;
                
            } catch (error) {
                console.error('Error loading cities:', error);
                citySelect.innerHTML = '<option value="">Error loading cities</option>';
            }
        }

        // Populate city options
        function populateCityOptions(cities) {
            const citySelect = document.getElementById('city');
            citySelect.innerHTML = '<option value="">Select City/Municipality</option>';
            
            const sortedCities = cities.sort((a, b) => a.name.localeCompare(b.name));
            
            sortedCities.forEach(city => {
                const option = document.createElement('option');
                option.value = city.name; // Use name instead of code
                option.textContent = city.name;
                option.setAttribute('data-code', city.code); // Store code as data attribute
                citySelect.appendChild(option);
            });
        }

        // Update barangays based on selected city
        async function updateBarangays() {
            const citySelect = document.getElementById('city');
            const barangaySelect = document.getElementById('barangay');
            
            barangaySelect.innerHTML = '<option value="">Loading barangays...</option>';
            barangaySelect.disabled = true;
            
            const selectedCityName = citySelect.value;
            const selectedCityCode = citySelect.selectedOptions[0]?.getAttribute('data-code');
            
            // Update hidden field
            document.getElementById('city_code').value = selectedCityCode || '';
            
            if (!selectedCityName) {
                barangaySelect.innerHTML = '<option value="">Select Barangay</option>';
                return;
            }
            
            try {
                if (barangaysCache[selectedCityCode]) {
                    populateBarangayOptions(barangaysCache[selectedCityCode]);
                    barangaySelect.disabled = false;
                    return;
                }
                
                const response = await fetch(`${PSA_API_BASE}/barangays/`);
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const allBarangays = await response.json();
                const cityBarangays = allBarangays.filter(barangay => 
                    barangay.cityCode === selectedCityCode || 
                    barangay.municipalityCode === selectedCityCode
                );
                
                barangaysCache[selectedCityCode] = cityBarangays;
                populateBarangayOptions(cityBarangays);
                barangaySelect.disabled = false;
                
            } catch (error) {
                console.error('Error loading barangays:', error);
                barangaySelect.innerHTML = '<option value="">Error loading barangays</option>';
            }
        }

        // Populate barangay options
        function populateBarangayOptions(barangays) {
            const barangaySelect = document.getElementById('barangay');
            barangaySelect.innerHTML = '<option value="">Select Barangay</option>';
            
            const sortedBarangays = barangays.sort((a, b) => a.name.localeCompare(b.name));
            
            sortedBarangays.forEach(barangay => {
                const option = document.createElement('option');
                option.value = barangay.name; // Use name instead of code
                option.textContent = barangay.name;
                option.setAttribute('data-code', barangay.code); // Store code as data attribute
                barangaySelect.appendChild(option);
            });
        }

        // Track barangay selection
        document.getElementById('barangay').addEventListener('change', function() {
            const selectedOption = this.selectedOptions[0];
            const barangayCode = selectedOption ? selectedOption.getAttribute('data-code') : '';
            document.getElementById('barangay_code').value = barangayCode;
        });

        // Initialize on page load
        window.onload = async () => {
            await initializeProvinces();
        };
    </script>
</body>
</html>
