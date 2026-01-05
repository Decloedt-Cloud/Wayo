ALTER TABLE `course`
ADD COLUMN `prerequisites` TEXT NULL DEFAULT NULL
AFTER `outcomes`,
ADD COLUMN `field_of_activity` VARCHAR(255) NULL DEFAULT NULL
AFTER `prerequisites`,
ADD COLUMN `course_style` VARCHAR(255) NULL DEFAULT NULL
AFTER `field_of_activity`;