-- Add is_deleted column if it does not exist
SET @column_exists := (SELECT COUNT(*)
                      FROM information_schema.COLUMNS
                      WHERE TABLE_NAME = 'DEPARTMENT'
                      AND COLUMN_NAME = 'trash'
                      AND TABLE_SCHEMA = 'mydb'
                      );

SET @sql := IF (@column_exists = 0,
                'ALTER TABLE DEPARTMENT ADD COLUMN trash BOOLEAN DEFAULT FALSE',
                'SELECT "Column already exists"');

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;