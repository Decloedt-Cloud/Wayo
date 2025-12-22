-- =====================================================
-- Migration: Add columns to invoices and create payments table
-- Date: 2025-10-09
-- Issue: SM-744
-- =====================================================

-- Create payments table
CREATE TABLE IF NOT EXISTS `payments` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `student_id` INT(11) NOT NULL,
  `school_id` INT(11) NULL,
  `class_id` INT(11) NULL,
  `invoice_id` INT(11) NULL,
  `amount` DECIMAL(10,2) NOT NULL,
  `currency` VARCHAR(10) DEFAULT 'EUR',
  `payment_type` ENUM('school_join', 'class_enrol', 'subscription', 'other') NOT NULL,
  `payment_status` ENUM('pending', 'paid', 'failed', 'refunded') DEFAULT 'pending',
  `payment_method` VARCHAR(50) NULL COMMENT 'Stripe, PayPal, etc.',
  `transaction_ref` VARCHAR(100) NULL COMMENT 'ID Stripe/PayPal, etc.',
  `paid_at` DATETIME NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_student_id` (`student_id`),
  KEY `idx_school_id` (`school_id`),
  KEY `idx_invoice_id` (`invoice_id`),
  KEY `idx_payment_status` (`payment_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add columns to invoices table (consolidated in single ALTER)
ALTER TABLE `invoices` 
ADD COLUMN `payment_type` ENUM('class_enrol', 'community_join') DEFAULT 'class_enrol' COMMENT 'Type of payment' AFTER `status`,
ADD COLUMN `payment_id` INT(11) NULL COMMENT 'Reference to payments table' AFTER `payment_type`,
ADD COLUMN `currency` VARCHAR(255) DEFAULT NULL COMMENT 'Invoice currency code' AFTER `payment_id`;

-- Add foreign key constraint (optional, can be added later if needed)
-- ALTER TABLE `invoices` ADD CONSTRAINT `fk_invoice_payment` FOREIGN KEY (`payment_id`) REFERENCES `payments` (`id`) ON DELETE SET NULL;
