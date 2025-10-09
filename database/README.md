# Database Migration for Registration Form

This directory contains the necessary files to update the `nulip_db` database to support the enhanced registration form with all the new fields.

## Files Created

### 1. `add_missing_fields.sql`
- SQL script to add missing fields to the `user_info` table
- Adds: `birth_date`, `barangay`, `previous_school`, `school_type`
- Updates `likelihood` column to allow NULL values
- Adds indexes for better performance
- Updates existing records with default values

### 2. `run_migration.php`
- PHP script to execute the database migration
- Runs all SQL queries safely with error handling
- Provides feedback on each operation
- Updates existing records with default values
- Verifies the final table structure

### 3. `test_registration.php`
- Test script to verify the database connection and new fields
- Checks if all required fields exist
- Tests data insertion with new fields
- Provides comprehensive database status

## How to Run the Migration

### Option 1: Using PHP Script (Recommended)
```bash
cd database
php run_migration.php
```

### Option 2: Using SQL Script
1. Open phpMyAdmin or your MySQL client
2. Select the `nulip_db` database
3. Run the contents of `add_missing_fields.sql`

### Option 3: Manual Execution
Run each SQL command individually in your MySQL client:

```sql
USE nulip_db;

-- Add missing fields
ALTER TABLE user_info ADD COLUMN birth_date DATE NULL AFTER sex;
ALTER TABLE user_info ADD COLUMN barangay VARCHAR(255) NULL AFTER city;
ALTER TABLE user_info ADD COLUMN previous_school VARCHAR(255) NULL AFTER second_program;
ALTER TABLE user_info ADD COLUMN school_type ENUM('Public', 'Private') NULL AFTER previous_school;

-- Update likelihood column
ALTER TABLE user_info MODIFY COLUMN likelihood DECIMAL(5,2) NULL;

-- Add indexes
CREATE INDEX idx_user_info_birth_date ON user_info(birth_date);
CREATE INDEX idx_user_info_previous_school ON user_info(previous_school);
CREATE INDEX idx_user_info_school_type ON user_info(school_type);

-- Update existing records
UPDATE user_info SET 
    birth_date = COALESCE(birth_date, '2000-01-01'),
    barangay = COALESCE(barangay, 'Unknown'),
    previous_school = COALESCE(previous_school, 'Previous School'),
    school_type = COALESCE(school_type, 'Public')
WHERE birth_date IS NULL OR barangay IS NULL OR previous_school IS NULL OR school_type IS NULL;
```

## Testing the Migration

After running the migration, test the database:

```bash
cd database
php test_registration.php
```

This will verify:
- All new fields exist
- Data insertion works with new fields
- Existing data is preserved
- Database connection is working

## New Fields Added

| Field Name | Type | Description |
|------------|------|-------------|
| `birth_date` | DATE | Student's date of birth |
| `barangay` | VARCHAR(255) | Barangay where student lives |
| `previous_school` | VARCHAR(255) | Name of previous school attended |
| `school_type` | ENUM('Public', 'Private') | Type of previous school |

## Updated Registration Process

The registration form now captures:
- **Personal Details**: Name, email, phone, birth date, sex, birthplace, complete address
- **Academic Information**: Applying status, academic year/term, programs, strand, previous school details
- **Other Information**: Family income, parent occupations

## Backward Compatibility

- All existing data is preserved
- Default values are provided for new fields on existing records
- The likelihood calculation still works with existing ML models
- No breaking changes to existing functionality

## Troubleshooting

### Common Issues

1. **Permission Denied**: Make sure the database user has ALTER TABLE permissions
2. **Field Already Exists**: If you run the migration multiple times, some fields might already exist
3. **Connection Failed**: Check your database connection settings in `connections.php`

### Error Messages

- `Duplicate column name`: Field already exists, migration partially completed
- `Table doesn't exist`: Make sure you're connected to the correct database
- `Access denied`: Check database user permissions

### Verification

After migration, verify the table structure:
```sql
DESCRIBE user_info;
```

You should see all the new fields listed in the output.
