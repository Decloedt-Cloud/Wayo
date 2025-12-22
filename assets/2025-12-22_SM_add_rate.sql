-- =====================================================
-- OPTIMIZED COMPLETE MIGRATION
-- School Management System Database Migration
-- Date: 2025-12-22
-- Version: Complete Migration
-- =====================================================
-- This single file contains all database migrations
-- Safe to run multiple times (idempotent)
-- Includes existence checks and error handling
-- =====================================================

DELIMITER ;;

-- =====================================================
-- HELPER PROCEDURE: Check if column exists
-- =====================================================
DROP PROCEDURE IF EXISTS `check_column_exists`;;
CREATE PROCEDURE `check_column_exists`(
    IN table_name VARCHAR(255),
    IN column_name VARCHAR(255),
    OUT exists_flag BOOLEAN
)
BEGIN
    SET exists_flag = (
        SELECT COUNT(*) > 0
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE()
        AND TABLE_NAME = table_name
        AND COLUMN_NAME = column_name
    );
END;;

-- =====================================================
-- HELPER PROCEDURE: Check if table exists
-- =====================================================
DROP PROCEDURE IF EXISTS `check_table_exists`;;
CREATE PROCEDURE `check_table_exists`(
    IN table_name VARCHAR(255),
    OUT exists_flag BOOLEAN
)
BEGIN
    SET exists_flag = (
        SELECT COUNT(*) > 0
        FROM INFORMATION_SCHEMA.TABLES
        WHERE TABLE_SCHEMA = DATABASE()
        AND TABLE_NAME = table_name
    );
END;;

-- =====================================================
-- HELPER PROCEDURE: Check if index exists
-- =====================================================
DROP PROCEDURE IF EXISTS `check_index_exists`;;
CREATE PROCEDURE `check_index_exists`(
    IN table_name VARCHAR(255),
    IN index_name VARCHAR(255),
    OUT exists_flag BOOLEAN
)
BEGIN
    SET exists_flag = (
        SELECT COUNT(*) > 0
        FROM INFORMATION_SCHEMA.STATISTICS
        WHERE TABLE_SCHEMA = DATABASE()
        AND TABLE_NAME = table_name
        AND INDEX_NAME = index_name
    );
END;;

DELIMITER ;

-- =====================================================
-- 1. CREATE PAYMENTS TABLE
-- =====================================================
CALL check_table_exists('payments', @table_exists);
IF @table_exists = FALSE THEN
    CREATE TABLE `payments` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `student_id` INT(11) NOT NULL COMMENT 'Student making the payment',
        `school_id` INT(11) NULL COMMENT 'School receiving the payment',
        `class_id` INT(11) NULL COMMENT 'Class associated with payment',
        `invoice_id` INT(11) NULL COMMENT 'Linked invoice',
        `amount` DECIMAL(10,2) NOT NULL COMMENT 'Payment amount',
        `currency` VARCHAR(10) DEFAULT 'EUR' COMMENT 'Payment currency',
        `payment_type` ENUM('school_join', 'class_enrol', 'subscription', 'other') NOT NULL COMMENT 'Type of payment',
        `payment_status` ENUM('pending', 'paid', 'failed', 'refunded') DEFAULT 'pending' COMMENT 'Payment status',
        `payment_method` VARCHAR(50) NULL COMMENT 'Stripe, PayPal, etc.',
        `transaction_ref` VARCHAR(100) NULL COMMENT 'External payment reference',
        `paid_at` DATETIME NULL COMMENT 'When payment was completed',
        `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
        `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `idx_student_id` (`student_id`),
        KEY `idx_school_id` (`school_id`),
        KEY `idx_invoice_id` (`invoice_id`),
        KEY `idx_payment_status` (`payment_status`),
        KEY `idx_payment_type` (`payment_type`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Payment transactions table';
    SELECT '✓ Created payments table' AS status;
ELSE
    SELECT '✓ Payments table already exists' AS status;
END IF;

-- =====================================================
-- 2. ADD INVOICE PAYMENT COLUMNS
-- =====================================================

-- payment_type column
CALL check_column_exists('invoices', 'payment_type', @col_exists);
IF @col_exists = FALSE THEN
    ALTER TABLE `invoices`
    ADD COLUMN `payment_type` ENUM('class_enrol', 'community_join', 'subscription_admin') DEFAULT 'class_enrol' COMMENT 'Type of payment' AFTER `status`;
    SELECT '✓ Added payment_type column to invoices' AS status;
ELSE
    -- Update existing enum to include new value
    ALTER TABLE `invoices`
    MODIFY COLUMN `payment_type` ENUM('class_enrol', 'community_join', 'subscription_admin') DEFAULT 'class_enrol' COMMENT 'Type of payment';
    SELECT '✓ Updated payment_type enum in invoices' AS status;
END IF;

-- payment_id column
CALL check_column_exists('invoices', 'payment_id', @col_exists);
IF @col_exists = FALSE THEN
    ALTER TABLE `invoices`
    ADD COLUMN `payment_id` INT(11) NULL COMMENT 'Reference to payments table' AFTER `payment_type`;
    SELECT '✓ Added payment_id column to invoices' AS status;
ELSE
    SELECT '✓ payment_id column already exists in invoices' AS status;
END IF;

-- currency column
CALL check_column_exists('invoices', 'currency', @col_exists);
IF @col_exists = FALSE THEN
    ALTER TABLE `invoices`
    ADD COLUMN `currency` VARCHAR(255) DEFAULT NULL COMMENT 'Invoice currency code' AFTER `payment_id`;
    SELECT '✓ Added currency column to invoices' AS status;
ELSE
    SELECT '✓ currency column already exists in invoices' AS status;
END IF;

-- =====================================================
-- 3. ADD SCHOOLS TRIAL & SUBSCRIPTION COLUMNS
-- =====================================================

-- trial_start column
CALL check_column_exists('schools', 'trial_start', @col_exists);
IF @col_exists = FALSE THEN
    ALTER TABLE `schools`
    ADD COLUMN `trial_start` INT(11) NULL COMMENT 'Trial start timestamp' AFTER `price`;
    SELECT '✓ Added trial_start column to schools' AS status;
ELSE
    SELECT '✓ trial_start column already exists in schools' AS status;
END IF;

-- trial_end column
CALL check_column_exists('schools', 'trial_end', @col_exists);
IF @col_exists = FALSE THEN
    ALTER TABLE `schools`
    ADD COLUMN `trial_end` INT(11) NULL COMMENT 'Trial end timestamp' AFTER `trial_start`;
    SELECT '✓ Added trial_end column to schools' AS status;
ELSE
    SELECT '✓ trial_end column already exists in schools' AS status;
END IF;

-- is_trial column
CALL check_column_exists('schools', 'is_trial', @col_exists);
IF @col_exists = FALSE THEN
    ALTER TABLE `schools`
    ADD COLUMN `is_trial` TINYINT(1) DEFAULT 0 COMMENT '1 if school is in trial period' AFTER `trial_end`;
    SELECT '✓ Added is_trial column to schools' AS status;
ELSE
    SELECT '✓ is_trial column already exists in schools' AS status;
END IF;

-- is_paid column
CALL check_column_exists('schools', 'is_paid', @col_exists);
IF @col_exists = FALSE THEN
    ALTER TABLE `schools`
    ADD COLUMN `is_paid` TINYINT(1) DEFAULT 0 COMMENT '1 if school subscription is paid' AFTER `is_trial`;
    SELECT '✓ Added is_paid column to schools' AS status;
ELSE
    SELECT '✓ is_paid column already exists in schools' AS status;
END IF;

-- subscription_end column
CALL check_column_exists('schools', 'subscription_end', @col_exists);
IF @col_exists = FALSE THEN
    ALTER TABLE `schools`
    ADD COLUMN `subscription_end` INT(11) NULL COMMENT 'Subscription end timestamp' AFTER `is_paid`;
    SELECT '✓ Added subscription_end column to schools' AS status;
ELSE
    SELECT '✓ subscription_end column already exists in schools' AS status;
END IF;

-- updated_at column
CALL check_column_exists('schools', 'updated_at', @col_exists);
IF @col_exists = FALSE THEN
    ALTER TABLE `schools`
    ADD COLUMN `updated_at` INT(11) NULL COMMENT 'Last update timestamp' AFTER `subscription_end`;
    SELECT '✓ Added updated_at column to schools' AS status;
ELSE
    SELECT '✓ updated_at column already exists in schools' AS status;
END IF;

-- =====================================================
-- 4. ADD INVOICE VAT COLUMNS
-- =====================================================

-- vat_amount column
CALL check_column_exists('invoices', 'vat_amount', @col_exists);
IF @col_exists = FALSE THEN
    ALTER TABLE `invoices`
    ADD COLUMN `vat_amount` DECIMAL(10,2) DEFAULT 0.00 COMMENT 'Montant de la TVA' AFTER `total_amount`;
    SELECT '✓ Added vat_amount column to invoices' AS status;
ELSE
    SELECT '✓ vat_amount column already exists in invoices' AS status;
END IF;

-- vat_rate column
CALL check_column_exists('invoices', 'vat_rate', @col_exists);
IF @col_exists = FALSE THEN
    ALTER TABLE `invoices`
    ADD COLUMN `vat_rate` DECIMAL(5,2) DEFAULT 0.00 COMMENT 'Taux de TVA en pourcentage (ex: 20.00 pour 20%)' AFTER `vat_amount`;
    SELECT '✓ Added vat_rate column to invoices' AS status;
ELSE
    SELECT '✓ vat_rate column already exists in invoices' AS status;
END IF;

-- sub_total column
CALL check_column_exists('invoices', 'sub_total', @col_exists);
IF @col_exists = FALSE THEN
    ALTER TABLE `invoices`
    ADD COLUMN `sub_total` DECIMAL(10,2) DEFAULT NULL COMMENT 'Montant HT (hors taxes)' AFTER `vat_rate`;
    SELECT '✓ Added sub_total column to invoices' AS status;
ELSE
    SELECT '✓ sub_total column already exists in invoices' AS status;
END IF;

-- Update existing invoices with calculated sub_total
UPDATE `invoices`
SET `sub_total` = `total_amount` - COALESCE(`vat_amount`, 0)
WHERE `vat_amount` > 0 AND (`sub_total` IS NULL OR `sub_total` = 0);

UPDATE `invoices`
SET `sub_total` = `total_amount`
WHERE (`vat_amount` IS NULL OR `vat_amount` = 0) AND (`sub_total` IS NULL OR `sub_total` = 0);

SELECT '✓ Updated existing invoices with calculated sub_total' AS status;

-- =====================================================
-- 5. CREATE FX RATES DAILY TABLE
-- =====================================================
CALL check_table_exists('fx_rates_daily', @table_exists);
IF @table_exists = FALSE THEN
    CREATE TABLE `fx_rates_daily` (
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
        KEY `idx_fetched_at` (`fetched_at`),
        KEY `idx_base_code` (`base_code`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Daily FX rates storage';
    SELECT '✓ Created fx_rates_daily table' AS status;
ELSE
    SELECT '✓ fx_rates_daily table already exists' AS status;
END IF;

-- =====================================================
-- 6. ADD INVOICE FX COLUMNS
-- =====================================================

-- payment_currency column
CALL check_column_exists('invoices', 'payment_currency', @col_exists);
IF @col_exists = FALSE THEN
    ALTER TABLE `invoices`
    ADD COLUMN `payment_currency` VARCHAR(3) NULL COMMENT 'Currency used for actual payment (e.g., USD, EUR, GBP)' AFTER `currency`;
    SELECT '✓ Added payment_currency column to invoices' AS status;
ELSE
    SELECT '✓ payment_currency column already exists in invoices' AS status;
END IF;

-- payment_amount_converted column
CALL check_column_exists('invoices', 'payment_amount_converted', @col_exists);
IF @col_exists = FALSE THEN
    ALTER TABLE `invoices`
    ADD COLUMN `payment_amount_converted` DECIMAL(10,2) NULL COMMENT 'Amount paid in payment currency after FX conversion' AFTER `payment_currency`;
    SELECT '✓ Added payment_amount_converted column to invoices' AS status;
ELSE
    SELECT '✓ payment_amount_converted column already exists in invoices' AS status;
END IF;

-- fx_rate column
CALL check_column_exists('invoices', 'fx_rate', @col_exists);
IF @col_exists = FALSE THEN
    ALTER TABLE `invoices`
    ADD COLUMN `fx_rate` DECIMAL(12,6) NULL COMMENT 'Exchange rate applied (1 invoice_currency = X payment_currency)' AFTER `payment_amount_converted`;
    SELECT '✓ Added fx_rate column to invoices' AS status;
ELSE
    SELECT '✓ fx_rate column already exists in invoices' AS status;
END IF;

-- fx_rate_date column
CALL check_column_exists('invoices', 'fx_rate_date', @col_exists);
IF @col_exists = FALSE THEN
    ALTER TABLE `invoices`
    ADD COLUMN `fx_rate_date` DATE NULL COMMENT 'Date of the exchange rate used' AFTER `fx_rate`;
    SELECT '✓ Added fx_rate_date column to invoices' AS status;
ELSE
    SELECT '✓ fx_rate_date column already exists in invoices' AS status;
END IF;

-- conversion_applied column
CALL check_column_exists('invoices', 'conversion_applied', @col_exists);
IF @col_exists = FALSE THEN
    ALTER TABLE `invoices`
    ADD COLUMN `conversion_applied` TINYINT(1) DEFAULT 0 COMMENT '1 if currency conversion was applied, 0 otherwise' AFTER `fx_rate_date`;
    SELECT '✓ Added conversion_applied column to invoices' AS status;
ELSE
    SELECT '✓ conversion_applied column already exists in invoices' AS status;
END IF;

-- =====================================================
-- 7. ADD INDEXES FOR PERFORMANCE
-- =====================================================

-- Invoice indexes
CALL check_index_exists('invoices', 'idx_payment_currency', @idx_exists);
IF @idx_exists = FALSE THEN
    ALTER TABLE `invoices` ADD INDEX `idx_payment_currency` (`payment_currency`);
    SELECT '✓ Added idx_payment_currency index' AS status;
ELSE
    SELECT '✓ idx_payment_currency index already exists' AS status;
END IF;

CALL check_index_exists('invoices', 'idx_conversion_applied', @idx_exists);
IF @idx_exists = FALSE THEN
    ALTER TABLE `invoices` ADD INDEX `idx_conversion_applied` (`conversion_applied`);
    SELECT '✓ Added idx_conversion_applied index' AS status;
ELSE
    SELECT '✓ idx_conversion_applied index already exists' AS status;
END IF;

-- =====================================================
-- 8. UPDATE CURRENCIES TABLE (if exists)
-- =====================================================
CALL check_table_exists('currencies', @table_exists);
IF @table_exists = TRUE THEN
    UPDATE `currencies`
    SET `stripe_supported` = 0,
        `paypal_supported` = 0,
        `paystack_supported` = 0,
        `payumoney_supported` = 0
    WHERE `code` NOT IN ('AED', 'MAD', 'EUR', 'USD', 'GBP');
    SELECT '✓ Updated currencies table (disabled unsupported gateways)' AS status;
ELSE
    SELECT '✓ Currencies table does not exist, skipping update' AS status;
END IF;

-- =====================================================
-- CLEANUP HELPER PROCEDURES
-- =====================================================
DROP PROCEDURE IF EXISTS `check_column_exists`;
DROP PROCEDURE IF EXISTS `check_table_exists`;
DROP PROCEDURE IF EXISTS `check_index_exists`;

-- =====================================================
-- MIGRATION COMPLETE
-- =====================================================
SELECT CONCAT(
    '🎉 Database migration completed successfully!',
    CHAR(10), 'Tables created/updated: payments, fx_rates_daily',
    CHAR(10), 'Invoices: payment_type, payment_id, currency, vat_amount, vat_rate, sub_total, payment_currency, payment_amount_converted, fx_rate, fx_rate_date, conversion_applied',
    CHAR(10), 'Schools: trial_start, trial_end, is_trial, is_paid, subscription_end, updated_at',
    CHAR(10), 'Indexes added for performance optimization'
) AS 'MIGRATION SUMMARY';
