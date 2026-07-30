-- Migration: Add missing columns and indexes to existing tables
-- Run this if your tables already exist without proper indexes
-- Uses MySQL 8.0+ compatible syntax

-- Enrollments indexes
SET @exist := (SELECT COUNT(*) FROM information_schema.statistics WHERE table_schema = DATABASE() AND table_name = 'enrollments' AND index_name = 'idx_enrollments_status');
SET @stmt := IF(@exist = 0, 'CREATE INDEX idx_enrollments_status ON enrollments (status)', 'SELECT 1');
PREPARE stmt FROM @stmt; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exist := (SELECT COUNT(*) FROM information_schema.statistics WHERE table_schema = DATABASE() AND table_name = 'enrollments' AND index_name = 'idx_enrollments_created_at');
SET @stmt := IF(@exist = 0, 'CREATE INDEX idx_enrollments_created_at ON enrollments (created_at)', 'SELECT 1');
PREPARE stmt FROM @stmt; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exist := (SELECT COUNT(*) FROM information_schema.statistics WHERE table_schema = DATABASE() AND table_name = 'enrollments' AND index_name = 'idx_enrollments_course_name');
SET @stmt := IF(@exist = 0, 'CREATE INDEX idx_enrollments_course_name ON enrollments (course_name)', 'SELECT 1');
PREPARE stmt FROM @stmt; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exist := (SELECT COUNT(*) FROM information_schema.statistics WHERE table_schema = DATABASE() AND table_name = 'enrollments' AND index_name = 'idx_enrollments_course_id');
SET @stmt := IF(@exist = 0, 'CREATE INDEX idx_enrollments_course_id ON enrollments (course_id)', 'SELECT 1');
PREPARE stmt FROM @stmt; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exist := (SELECT COUNT(*) FROM information_schema.statistics WHERE table_schema = DATABASE() AND table_name = 'enrollments' AND index_name = 'idx_enrollments_full_name');
SET @stmt := IF(@exist = 0, 'CREATE INDEX idx_enrollments_full_name ON enrollments (full_name)', 'SELECT 1');
PREPARE stmt FROM @stmt; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exist := (SELECT COUNT(*) FROM information_schema.statistics WHERE table_schema = DATABASE() AND table_name = 'enrollments' AND index_name = 'idx_enrollments_email');
SET @stmt := IF(@exist = 0, 'CREATE INDEX idx_enrollments_email ON enrollments (email)', 'SELECT 1');
PREPARE stmt FROM @stmt; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exist := (SELECT COUNT(*) FROM information_schema.statistics WHERE table_schema = DATABASE() AND table_name = 'enrollments' AND index_name = 'idx_enrollments_phone');
SET @stmt := IF(@exist = 0, 'CREATE INDEX idx_enrollments_phone ON enrollments (phone)', 'SELECT 1');
PREPARE stmt FROM @stmt; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exist := (SELECT COUNT(*) FROM information_schema.statistics WHERE table_schema = DATABASE() AND table_name = 'enrollments' AND index_name = 'idx_enrollments_source');
SET @stmt := IF(@exist = 0, 'CREATE INDEX idx_enrollments_source ON enrollments (enquiry_source)', 'SELECT 1');
PREPARE stmt FROM @stmt; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Contact submissions: add id PK, created_at, and status if missing
SET @exist := (SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'contact_submissions' AND column_name = 'id');
SET @stmt := IF(@exist = 0, 'ALTER TABLE contact_submissions ADD COLUMN id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST', 'SELECT 1');
PREPARE stmt FROM @stmt; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exist := (SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'contact_submissions' AND column_name = 'created_at');
SET @stmt := IF(@exist = 0, 'ALTER TABLE contact_submissions ADD COLUMN created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP', 'SELECT 1');
PREPARE stmt FROM @stmt; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exist := (SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'contact_submissions' AND column_name = 'status');
SET @stmt := IF(@exist = 0, "ALTER TABLE contact_submissions ADD COLUMN status ENUM('pending','contacted','enrolled','cancelled') NOT NULL DEFAULT 'pending'", 'SELECT 1');
PREPARE stmt FROM @stmt; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exist := (SELECT COUNT(*) FROM information_schema.statistics WHERE table_schema = DATABASE() AND table_name = 'contact_submissions' AND index_name = 'idx_contact_created_at');
SET @stmt := IF(@exist = 0, 'CREATE INDEX idx_contact_created_at ON contact_submissions (created_at)', 'SELECT 1');
PREPARE stmt FROM @stmt; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exist := (SELECT COUNT(*) FROM information_schema.statistics WHERE table_schema = DATABASE() AND table_name = 'contact_submissions' AND index_name = 'idx_contact_email');
SET @stmt := IF(@exist = 0, 'CREATE INDEX idx_contact_email ON contact_submissions (email)', 'SELECT 1');
PREPARE stmt FROM @stmt; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exist := (SELECT COUNT(*) FROM information_schema.statistics WHERE table_schema = DATABASE() AND table_name = 'contact_submissions' AND index_name = 'idx_contact_status');
SET @stmt := IF(@exist = 0, 'CREATE INDEX idx_contact_status ON contact_submissions (status)', 'SELECT 1');
PREPARE stmt FROM @stmt; EXECUTE stmt; DEALLOCATE PREPARE stmt;
