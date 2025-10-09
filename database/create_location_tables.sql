-- Create location tables for provinces, cities, and barangays
-- This script creates the necessary tables for proper location data management

USE nulip_db;

-- Create provinces table
CREATE TABLE IF NOT EXISTS provinces (
    id INT(11) NOT NULL AUTO_INCREMENT,
    province_code VARCHAR(10) NOT NULL,
    province_name VARCHAR(100) NOT NULL,
    region VARCHAR(50) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY unique_province_code (province_code),
    INDEX idx_province_name (province_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Create cities table
CREATE TABLE IF NOT EXISTS cities (
    id INT(11) NOT NULL AUTO_INCREMENT,
    city_code VARCHAR(10) NOT NULL,
    city_name VARCHAR(100) NOT NULL,
    province_id INT(11) NOT NULL,
    city_type ENUM('City', 'Municipality') DEFAULT 'City',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY unique_city_code (city_code),
    INDEX idx_city_name (city_name),
    INDEX idx_province_id (province_id),
    FOREIGN KEY (province_id) REFERENCES provinces(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Create barangays table
CREATE TABLE IF NOT EXISTS barangays (
    id INT(11) NOT NULL AUTO_INCREMENT,
    barangay_code VARCHAR(15) NOT NULL,
    barangay_name VARCHAR(100) NOT NULL,
    city_id INT(11) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY unique_barangay_code (barangay_code),
    INDEX idx_barangay_name (barangay_name),
    INDEX idx_city_id (city_id),
    FOREIGN KEY (city_id) REFERENCES cities(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Update user_info table to use foreign keys
ALTER TABLE user_info 
ADD COLUMN province_id INT(11) NULL AFTER province,
ADD COLUMN city_id INT(11) NULL AFTER city,
ADD COLUMN barangay_id INT(11) NULL AFTER barangay,
ADD INDEX idx_user_province (province_id),
ADD INDEX idx_user_city (city_id),
ADD INDEX idx_user_barangay (barangay_id),
ADD FOREIGN KEY (province_id) REFERENCES provinces(id) ON DELETE SET NULL,
ADD FOREIGN KEY (city_id) REFERENCES cities(id) ON DELETE SET NULL,
ADD FOREIGN KEY (barangay_id) REFERENCES barangays(id) ON DELETE SET NULL;

-- Verify the structure
DESCRIBE provinces;
DESCRIBE cities;
DESCRIBE barangays;
DESCRIBE user_info;
