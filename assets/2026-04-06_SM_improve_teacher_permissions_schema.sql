-- Migration: Improve teacher_permissions table schema
-- Date: 2026-04-06
-- Purpose: Add missing indexes, constraints, and school isolation

-- Add school_id column if it doesn't exist
ALTER TABLE `teacher_permissions` 
ADD COLUMN `school_id` int(11) UNSIGNED NOT NULL DEFAULT 0 AFTER `id`;

-- Add indexes for better query performance
ALTER TABLE `teacher_permissions` 
ADD INDEX `idx_school_id` (`school_id`),
ADD INDEX `idx_teacher_id` (`teacher_id`),
ADD INDEX `idx_class_id` (`class_id`),
ADD INDEX `idx_section_id` (`section_id`);

-- Add unique constraint to prevent duplicate permissions for same teacher/class
ALTER TABLE `teacher_permissions` 
ADD UNIQUE KEY `unique_teacher_class` (`teacher_id`, `class_id`, `section_id`);

-- Update existing records to have proper school_id based on teacher's school
UPDATE `teacher_permissions` tp
INNER JOIN `teachers` t ON tp.teacher_id = t.id
SET tp.school_id = t.school_id
WHERE tp.school_id = 0;

-- Add foreign key constraints (optional - uncomment if desired)
-- ALTER TABLE `teacher_permissions` 
-- ADD CONSTRAINT `fk_tp_teacher` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`) ON DELETE CASCADE,
-- ADD CONSTRAINT `fk_tp_class` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
-- ADD CONSTRAINT `fk_tp_section` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE CASCADE,
-- ADD CONSTRAINT `fk_tp_school` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE;

-- Add created_at and updated_at timestamps for better auditing
ALTER TABLE `teacher_permissions` 
ADD COLUMN `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP AFTER `online_exam`,
ADD COLUMN `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `created_at`;

-- Add index on updated_at for efficient sorting
ALTER TABLE `teacher_permissions` 
ADD INDEX `idx_updated_at` (`updated_at`);
