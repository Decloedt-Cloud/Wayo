-- =====================================================
-- CONSOLIDATED MIGRATIONS
-- School Management System - Database Migrations
-- =====================================================
-- This file consolidates all migrations for easy application
-- Individual migration files are also available for incremental updates
-- =====================================================

-- =====================================================
-- 1. Payments Table & Invoice Payment Columns
-- Date: 2025-10-09 | Issue: SM-744
-- =====================================================

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

-- Add payment columns to invoices (with existence checks)
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'invoices' AND COLUMN_NAME = 'payment_type');
SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE `invoices` ADD COLUMN `payment_type` ENUM(''class_enrol'', ''community_join'') DEFAULT ''class_enrol'' COMMENT ''Type of payment'' AFTER `status`;',
    'SELECT ''Column payment_type already exists'' AS message;');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'invoices' AND COLUMN_NAME = 'payment_id');
SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE `invoices` ADD COLUMN `payment_id` INT(11) NULL COMMENT ''Reference to payments table'' AFTER `payment_type`;',
    'SELECT ''Column payment_id already exists'' AS message;');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'invoices' AND COLUMN_NAME = 'currency');
SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE `invoices` ADD COLUMN `currency` VARCHAR(255) DEFAULT NULL COMMENT ''Invoice currency code'' AFTER `payment_id`;',
    'SELECT ''Column currency already exists'' AS message;');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- =====================================================
-- 2. Schools Trial & Subscription Columns
-- Date: 2025-12-01
-- =====================================================

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'schools' AND COLUMN_NAME = 'trial_start');
SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE `schools` ADD COLUMN `trial_start` INT(11) NULL COMMENT ''Trial start timestamp'' AFTER `price`;',
    'SELECT ''Column trial_start already exists'' AS message;');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'schools' AND COLUMN_NAME = 'trial_end');
SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE `schools` ADD COLUMN `trial_end` INT(11) NULL COMMENT ''Trial end timestamp'' AFTER `trial_start`;',
    'SELECT ''Column trial_end already exists'' AS message;');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'schools' AND COLUMN_NAME = 'is_trial');
SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE `schools` ADD COLUMN `is_trial` TINYINT(1) DEFAULT 0 COMMENT ''1 if school is in trial period'' AFTER `trial_end`;',
    'SELECT ''Column is_trial already exists'' AS message;');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'schools' AND COLUMN_NAME = 'is_paid');
SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE `schools` ADD COLUMN `is_paid` TINYINT(1) DEFAULT 0 COMMENT ''1 if school subscription is paid'' AFTER `is_trial`;',
    'SELECT ''Column is_paid already exists'' AS message;');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'schools' AND COLUMN_NAME = 'subscription_end');
SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE `schools` ADD COLUMN `subscription_end` INT(11) NULL COMMENT ''Subscription end timestamp'' AFTER `is_paid`;',
    'SELECT ''Column subscription_end already exists'' AS message;');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'schools' AND COLUMN_NAME = 'updated_at');
SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE `schools` ADD COLUMN `updated_at` INT(11) NULL COMMENT ''Last update timestamp'' AFTER `subscription_end`;',
    'SELECT ''Column updated_at already exists'' AS message;');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- =====================================================
-- 3. Invoice Payment Type - Add subscription_admin
-- Date: 2025-12-10
-- =====================================================

-- Modify payment_type enum to include subscription_admin
ALTER TABLE `invoices`
MODIFY COLUMN `payment_type` ENUM('class_enrol', 'community_join', 'subscription_admin') DEFAULT 'class_enrol' COMMENT 'Type of payment';

-- =====================================================
-- 4. Invoice VAT (TVA) Columns
-- Date: 2025-12-15
-- =====================================================

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'invoices' AND COLUMN_NAME = 'vat_amount');
SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE `invoices` ADD COLUMN `vat_amount` DECIMAL(10,2) DEFAULT 0.00 COMMENT ''Montant de la TVA'' AFTER `total_amount`;',
    'SELECT ''Column vat_amount already exists'' AS message;');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'invoices' AND COLUMN_NAME = 'vat_rate');
SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE `invoices` ADD COLUMN `vat_rate` DECIMAL(5,2) DEFAULT 0.00 COMMENT ''Taux de TVA en pourcentage (ex: 20.00 pour 20%)'' AFTER `vat_amount`;',
    'SELECT ''Column vat_rate already exists'' AS message;');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'invoices' AND COLUMN_NAME = 'sub_total');
SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE `invoices` ADD COLUMN `sub_total` DECIMAL(10,2) DEFAULT NULL COMMENT ''Montant HT (hors taxes)'' AFTER `vat_rate`;',
    'SELECT ''Column sub_total already exists'' AS message;');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Update existing invoices with calculated sub_total
UPDATE `invoices` 
SET `sub_total` = `total_amount` - COALESCE(`vat_amount`, 0)
WHERE `vat_amount` > 0 AND (`sub_total` IS NULL OR `sub_total` = 0);

UPDATE `invoices`
SET `sub_total` = `total_amount`
WHERE (`vat_amount` IS NULL OR `vat_amount` = 0) AND (`sub_total` IS NULL OR `sub_total` = 0);

-- =====================================================
-- 5. FX Rates Daily Table
-- Date: 2025-12-19 | Issue: SM-791
-- =====================================================

CREATE TABLE IF NOT EXISTS `fx_rates_daily` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `rate_date` DATE NOT NULL COMMENT 'Date of the rates (YYYY-MM-DD)',
    `base_code` VARCHAR(3) NOT NULL DEFAULT 'USD' COMMENT 'Base currency code',
    `usd` DECIMAL(12,6) NOT NULL DEFAULT 1.000000 COMMENT 'USD rate (always 1 for USD base)',
    `eur` DECIMAL(12,6) NOT NULL COMMENT 'EUR conversion rate',
    `mad` DECIMAL(12,6) NOT NULL COMMENT 'MAD conversion rate',
    `aed` DECIMAL(12,6) NOT NULL COMMENT 'AED conversion rate',
    `gbp` DECIMAL(12,6) NOT NULL COMMENT 'GBP conversion rate',
    `source` VARCHAR(32) NOT NULL DEFAULT 'exchange-rate-api' COMMENT 'Data source identifier',
    `fetched_at` DATETIME NOT NULL COMMENT 'When the data was fetched from API',
    `status` VARCHAR(16) NOT NULL DEFAULT 'ok' COMMENT 'Status: ok|stale|error',
    `raw_json` LONGTEXT NULL COMMENT 'Raw JSON response from API (for debugging)',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `idx_rate_date_base` (`rate_date`, `base_code`),
    KEY `idx_rate_date` (`rate_date`),
    KEY `idx_status` (`status`),
    KEY `idx_fetched_at` (`fetched_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Daily FX rates storage';

-- Update currencies table if it exists
SET @table_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'currencies');
SET @sql = IF(@table_exists > 0,
    'UPDATE `currencies` SET `stripe_supported` = 0, `paypal_supported` = 0, `paystack_supported` = 0, `payumoney_supported` = 0 WHERE `code` NOT IN (''AED'', ''MAD'', ''EUR'', ''USD'', ''GBP'');',
    'SELECT ''Table currencies does not exist, skipping update'' AS message;');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- =====================================================
-- 6. Invoice FX (Foreign Exchange) Columns
-- Date: 2025-12-19
-- =====================================================

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'invoices' AND COLUMN_NAME = 'payment_currency');
SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE `invoices` ADD COLUMN `payment_currency` VARCHAR(3) NULL COMMENT ''Currency used for actual payment'' AFTER `currency`;',
    'SELECT ''Column payment_currency already exists'' AS message;');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'invoices' AND COLUMN_NAME = 'payment_amount_converted');
SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE `invoices` ADD COLUMN `payment_amount_converted` DECIMAL(10,2) NULL COMMENT ''Amount paid in payment currency after FX conversion'' AFTER `payment_currency`;',
    'SELECT ''Column payment_amount_converted already exists'' AS message;');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'invoices' AND COLUMN_NAME = 'fx_rate');
SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE `invoices` ADD COLUMN `fx_rate` DECIMAL(12,6) NULL COMMENT ''Exchange rate applied'' AFTER `payment_amount_converted`;',
    'SELECT ''Column fx_rate already exists'' AS message;');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'invoices' AND COLUMN_NAME = 'fx_rate_date');
SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE `invoices` ADD COLUMN `fx_rate_date` DATE NULL COMMENT ''Date of the exchange rate used'' AFTER `fx_rate`;',
    'SELECT ''Column fx_rate_date already exists'' AS message;');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'invoices' AND COLUMN_NAME = 'conversion_applied');
SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE `invoices` ADD COLUMN `conversion_applied` TINYINT(1) DEFAULT 0 COMMENT ''1 if currency conversion was applied'' AFTER `fx_rate_date`;',
    'SELECT ''Column conversion_applied already exists'' AS message;');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Add indexes for FX columns
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'invoices' AND INDEX_NAME = 'idx_payment_currency');
SET @sql = IF(@idx_exists = 0, 
    'ALTER TABLE `invoices` ADD INDEX `idx_payment_currency` (`payment_currency`);',
    'SELECT ''Index idx_payment_currency already exists'' AS message;');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'invoices' AND INDEX_NAME = 'idx_conversion_applied');
SET @sql = IF(@idx_exists = 0, 
    'ALTER TABLE `invoices` ADD INDEX `idx_conversion_applied` (`conversion_applied`);',
    'SELECT ''Index idx_conversion_applied already exists'' AS message;');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- =====================================================
-- MIGRATIONS COMPLETE
-- =====================================================

