-- Add missing fields to user_info table for the new registration form
-- This script adds the fields that are missing from the current database structure

USE nulip_db;

-- Add missing fields to user_info table
ALTER TABLE user_info 
ADD COLUMN birth_date DATE NULL AFTER sex,
ADD COLUMN barangay VARCHAR(255) NULL AFTER city,
ADD COLUMN previous_school VARCHAR(255) NULL AFTER second_program,
ADD COLUMN school_type ENUM('Public', 'Private') NULL AFTER previous_school;

-- Update the likelihood column to allow NULL values (in case ML calculation fails)
ALTER TABLE user_info 
MODIFY COLUMN likelihood DECIMAL(5,2) NULL;

-- Add index on new fields for better performance
CREATE INDEX idx_user_info_birth_date ON user_info(birth_date);
CREATE INDEX idx_user_info_previous_school ON user_info(previous_school);
CREATE INDEX idx_user_info_school_type ON user_info(school_type);

-- Update existing records to have default values for new fields
UPDATE user_info SET 
    birth_date = '2000-01-01',
    barangay = 'Unknown',
    previous_school = 'Previous School',
    school_type = 'Public'
WHERE birth_date IS NULL OR barangay IS NULL OR previous_school IS NULL OR school_type IS NULL;

-- Verify the changes
DESCRIBE user_info;
