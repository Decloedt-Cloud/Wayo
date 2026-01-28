-- =====================================================
-- MIGRATION CONSOLIDÉE PREPROD - WAYO School Management
-- Date: 2025-12-30
-- Version: 7.6+
-- Description: Script unique pour mise à jour de la base de données
-- =====================================================
-- 
-- INSTRUCTIONS:
-- 1. Faire un BACKUP avant d'exécuter: mysqldump -u root -p formation_db_1 > backup.sql
-- 2. Exécuter: mysql -u root -p formation_db_1 < PREPROD_migration_2025-12-30.sql
-- 
-- =====================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";

-- =====================================================
-- SECTION 1: TABLE schools - Colonnes abonnement & country
-- =====================================================

-- 1.1 Ajouter country (code ISO-2 pour résidence fiscale)
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'schools' AND COLUMN_NAME = 'country');
SET @sql = IF(@col_exists = 0,
    'ALTER TABLE `schools` ADD COLUMN `country` VARCHAR(2) DEFAULT NULL COMMENT ''Code pays ISO-2 (MA, AE, FR...)'' AFTER `name`',
    'SELECT ''Column country exists'' AS msg');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 1.2 Colonnes période d'essai
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'schools' AND COLUMN_NAME = 'trial_start');
SET @sql = IF(@col_exists = 0,
    'ALTER TABLE `schools` ADD COLUMN `trial_start` INT(11) NULL COMMENT ''Trial start timestamp'' AFTER `price`',
    'SELECT ''Column trial_start exists'' AS msg');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'schools' AND COLUMN_NAME = 'trial_end');
SET @sql = IF(@col_exists = 0,
    'ALTER TABLE `schools` ADD COLUMN `trial_end` INT(11) NULL COMMENT ''Trial end timestamp'' AFTER `trial_start`',
    'SELECT ''Column trial_end exists'' AS msg');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'schools' AND COLUMN_NAME = 'is_trial');
SET @sql = IF(@col_exists = 0,
    'ALTER TABLE `schools` ADD COLUMN `is_trial` TINYINT(1) DEFAULT 0 COMMENT ''1 if in trial'' AFTER `trial_end`',
    'SELECT ''Column is_trial exists'' AS msg');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'schools' AND COLUMN_NAME = 'is_paid');
SET @sql = IF(@col_exists = 0,
    'ALTER TABLE `schools` ADD COLUMN `is_paid` TINYINT(1) DEFAULT 0 COMMENT ''1 if paid'' AFTER `is_trial`',
    'SELECT ''Column is_paid exists'' AS msg');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'schools' AND COLUMN_NAME = 'subscription_end');
SET @sql = IF(@col_exists = 0,
    'ALTER TABLE `schools` ADD COLUMN `subscription_end` INT(11) NULL COMMENT ''Subscription end timestamp'' AFTER `is_paid`',
    'SELECT ''Column subscription_end exists'' AS msg');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'schools' AND COLUMN_NAME = 'subscription_status');
SET @sql = IF(@col_exists = 0,
    'ALTER TABLE `schools` ADD COLUMN `subscription_status` ENUM(''trialing'',''active'',''past_due'',''suspended'',''canceled'') DEFAULT ''trialing'' COMMENT ''Subscription state'' AFTER `subscription_end`',
    'SELECT ''Column subscription_status exists'' AS msg');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 1.3 Index schools
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'schools' AND INDEX_NAME = 'idx_schools_country');
SET @sql = IF(@idx_exists = 0, 'ALTER TABLE `schools` ADD INDEX `idx_schools_country` (`country`)', 'SELECT ''idx exists'' AS msg');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'schools' AND INDEX_NAME = 'idx_schools_subscription_status');
SET @sql = IF(@idx_exists = 0, 'ALTER TABLE `schools` ADD INDEX `idx_schools_subscription_status` (`subscription_status`)', 'SELECT ''idx exists'' AS msg');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- =====================================================
-- SECTION 2: TABLE settings_school - Renommage colonnes
-- =====================================================

-- 2.1 Ajouter vat_enabled si vat existe (ne pas renommer pour éviter casse code)
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'settings_school' AND COLUMN_NAME = 'vat_enabled');
SET @sql = IF(@col_exists = 0,
    'ALTER TABLE `settings_school` ADD COLUMN `vat_enabled` INT(11) DEFAULT 0 COMMENT ''TVA activée (0=non, 1=oui)''',
    'SELECT ''Column vat_enabled exists'' AS msg');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 2.2 Ajouter vat_rate si vat_rat existe
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'settings_school' AND COLUMN_NAME = 'vat_rate');
SET @sql = IF(@col_exists = 0,
    'ALTER TABLE `settings_school` ADD COLUMN `vat_rate` INT(11) DEFAULT 0 COMMENT ''Taux TVA (%)''',
    'SELECT ''Column vat_rate exists'' AS msg');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 2.3 Migrer les données des anciennes colonnes vers les nouvelles
UPDATE `settings_school` SET `vat_enabled` = `vat` WHERE `vat_enabled` = 0 AND `vat` IS NOT NULL AND `vat` > 0;
UPDATE `settings_school` SET `vat_rate` = `vat_rat` WHERE `vat_rate` = 0 AND `vat_rat` IS NOT NULL AND `vat_rat` > 0;

-- =====================================================
-- SECTION 3: TABLE invoices - Colonnes TVA, FX, Stripe
-- =====================================================

-- 3.1 Colonnes TVA
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'invoices' AND COLUMN_NAME = 'vat_amount');
SET @sql = IF(@col_exists = 0,
    'ALTER TABLE `invoices` ADD COLUMN `vat_amount` DECIMAL(10,2) DEFAULT 0.00 COMMENT ''Montant TVA''',
    'SELECT ''Column vat_amount exists'' AS msg');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'invoices' AND COLUMN_NAME = 'vat_rate');
SET @sql = IF(@col_exists = 0,
    'ALTER TABLE `invoices` ADD COLUMN `vat_rate` DECIMAL(5,2) DEFAULT 0.00 COMMENT ''Taux TVA (%)''',
    'SELECT ''Column vat_rate exists'' AS msg');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'invoices' AND COLUMN_NAME = 'sub_total');
SET @sql = IF(@col_exists = 0,
    'ALTER TABLE `invoices` ADD COLUMN `sub_total` DECIMAL(10,2) DEFAULT NULL COMMENT ''Montant HT''',
    'SELECT ''Column sub_total exists'' AS msg');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'invoices' AND COLUMN_NAME = 'tax_country');
SET @sql = IF(@col_exists = 0,
    'ALTER TABLE `invoices` ADD COLUMN `tax_country` VARCHAR(100) DEFAULT NULL COMMENT ''Pays résidence fiscale''',
    'SELECT ''Column tax_country exists'' AS msg');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'invoices' AND COLUMN_NAME = 'legal_entity_name');
SET @sql = IF(@col_exists = 0,
    'ALTER TABLE `invoices` ADD COLUMN `legal_entity_name` VARCHAR(255) DEFAULT NULL COMMENT ''Entité juridique''',
    'SELECT ''Column legal_entity_name exists'' AS msg');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'invoices' AND COLUMN_NAME = 'legal_entity_country');
SET @sql = IF(@col_exists = 0,
    'ALTER TABLE `invoices` ADD COLUMN `legal_entity_country` VARCHAR(255) DEFAULT NULL COMMENT ''Pays entité juridique''',
    'SELECT ''Column legal_entity_country exists'' AS msg');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 3.2 Colonnes FX (taux de change)
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'invoices' AND COLUMN_NAME = 'currency');
SET @sql = IF(@col_exists = 0,
    'ALTER TABLE `invoices` ADD COLUMN `currency` VARCHAR(3) DEFAULT NULL COMMENT ''Devise facture''',
    'SELECT ''Column currency exists'' AS msg');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'invoices' AND COLUMN_NAME = 'payment_currency');
SET @sql = IF(@col_exists = 0,
    'ALTER TABLE `invoices` ADD COLUMN `payment_currency` VARCHAR(3) DEFAULT NULL COMMENT ''Devise paiement réel''',
    'SELECT ''Column payment_currency exists'' AS msg');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'invoices' AND COLUMN_NAME = 'payment_amount_converted');
SET @sql = IF(@col_exists = 0,
    'ALTER TABLE `invoices` ADD COLUMN `payment_amount_converted` DECIMAL(10,2) DEFAULT NULL COMMENT ''Montant converti''',
    'SELECT ''Column payment_amount_converted exists'' AS msg');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'invoices' AND COLUMN_NAME = 'fx_rate');
SET @sql = IF(@col_exists = 0,
    'ALTER TABLE `invoices` ADD COLUMN `fx_rate` DECIMAL(12,6) DEFAULT NULL COMMENT ''Taux de change''',
    'SELECT ''Column fx_rate exists'' AS msg');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'invoices' AND COLUMN_NAME = 'fx_rate_date');
SET @sql = IF(@col_exists = 0,
    'ALTER TABLE `invoices` ADD COLUMN `fx_rate_date` DATE DEFAULT NULL COMMENT ''Date taux change''',
    'SELECT ''Column fx_rate_date exists'' AS msg');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'invoices' AND COLUMN_NAME = 'conversion_applied');
SET @sql = IF(@col_exists = 0,
    'ALTER TABLE `invoices` ADD COLUMN `conversion_applied` TINYINT(1) DEFAULT 0 COMMENT ''1 si conversion appliquée''',
    'SELECT ''Column conversion_applied exists'' AS msg');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 3.3 Colonnes Stripe
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'invoices' AND COLUMN_NAME = 'period_start');
SET @sql = IF(@col_exists = 0,
    'ALTER TABLE `invoices` ADD COLUMN `period_start` INT DEFAULT NULL COMMENT ''Début période abonnement''',
    'SELECT ''Column period_start exists'' AS msg');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'invoices' AND COLUMN_NAME = 'period_end');
SET @sql = IF(@col_exists = 0,
    'ALTER TABLE `invoices` ADD COLUMN `period_end` INT DEFAULT NULL COMMENT ''Fin période abonnement''',
    'SELECT ''Column period_end exists'' AS msg');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'invoices' AND COLUMN_NAME = 'stripe_payment_intent_id');
SET @sql = IF(@col_exists = 0,
    'ALTER TABLE `invoices` ADD COLUMN `stripe_payment_intent_id` VARCHAR(255) DEFAULT NULL COMMENT ''Stripe PaymentIntent ID''',
    'SELECT ''Column stripe_payment_intent_id exists'' AS msg');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'invoices' AND COLUMN_NAME = 'stripe_invoice_id');
SET @sql = IF(@col_exists = 0,
    'ALTER TABLE `invoices` ADD COLUMN `stripe_invoice_id` VARCHAR(255) DEFAULT NULL COMMENT ''Stripe Invoice ID''',
    'SELECT ''Column stripe_invoice_id exists'' AS msg');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'invoices' AND COLUMN_NAME = 'paid_at');
SET @sql = IF(@col_exists = 0,
    'ALTER TABLE `invoices` ADD COLUMN `paid_at` INT DEFAULT NULL COMMENT ''Timestamp paiement''',
    'SELECT ''Column paid_at exists'' AS msg');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 3.4 Colonnes frais processeur (B2B Maroc)
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'invoices' AND COLUMN_NAME = 'processor_fee_ht');
SET @sql = IF(@col_exists = 0,
    'ALTER TABLE `invoices` ADD COLUMN `processor_fee_ht` DECIMAL(10,2) DEFAULT NULL COMMENT ''Frais processeur HT''',
    'SELECT ''Column processor_fee_ht exists'' AS msg');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'invoices' AND COLUMN_NAME = 'processor_fee_vat');
SET @sql = IF(@col_exists = 0,
    'ALTER TABLE `invoices` ADD COLUMN `processor_fee_vat` DECIMAL(10,2) DEFAULT NULL COMMENT ''TVA frais processeur''',
    'SELECT ''Column processor_fee_vat exists'' AS msg');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'invoices' AND COLUMN_NAME = 'processor_fee_ttc');
SET @sql = IF(@col_exists = 0,
    'ALTER TABLE `invoices` ADD COLUMN `processor_fee_ttc` DECIMAL(10,2) DEFAULT NULL COMMENT ''Frais processeur TTC''',
    'SELECT ''Column processor_fee_ttc exists'' AS msg');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'invoices' AND COLUMN_NAME = 'net_cash');
SET @sql = IF(@col_exists = 0,
    'ALTER TABLE `invoices` ADD COLUMN `net_cash` DECIMAL(10,2) DEFAULT NULL COMMENT ''Net cash reçu''',
    'SELECT ''Column net_cash exists'' AS msg');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'invoices' AND COLUMN_NAME = 'net_economic');
SET @sql = IF(@col_exists = 0,
    'ALTER TABLE `invoices` ADD COLUMN `net_economic` DECIMAL(10,2) DEFAULT NULL COMMENT ''Net économique B2B''',
    'SELECT ''Column net_economic exists'' AS msg');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 3.5 Colonnes payment_type et payment_id
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'invoices' AND COLUMN_NAME = 'payment_type');
SET @sql = IF(@col_exists = 0,
    'ALTER TABLE `invoices` ADD COLUMN `payment_type` ENUM(''class_enrol'',''community_join'',''subscription_admin'') DEFAULT ''class_enrol'' COMMENT ''Type de paiement''',
    'SELECT ''Column payment_type exists'' AS msg');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'invoices' AND COLUMN_NAME = 'payment_id');
SET @sql = IF(@col_exists = 0,
    'ALTER TABLE `invoices` ADD COLUMN `payment_id` INT(11) DEFAULT NULL COMMENT ''Ref payments table''',
    'SELECT ''Column payment_id exists'' AS msg');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- =====================================================
-- SECTION 4: TABLE payments
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
    `payment_method` VARCHAR(50) NULL,
    `processor_name` VARCHAR(50) NULL COMMENT 'CashPlus, Stripe, etc.',
    `processor_fee_ht` DECIMAL(10,2) NULL,
    `processor_fee_vat` DECIMAL(10,2) NULL,
    `processor_fee_ttc` DECIMAL(10,2) NULL,
    `transaction_ref` VARCHAR(100) NULL,
    `paid_at` DATETIME NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_student_id` (`student_id`),
    KEY `idx_school_id` (`school_id`),
    KEY `idx_invoice_id` (`invoice_id`),
    KEY `idx_payment_status` (`payment_status`),
    KEY `idx_processor_name` (`processor_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- SECTION 5: TABLE fx_rates_daily (taux de change)
-- =====================================================

CREATE TABLE IF NOT EXISTS `fx_rates_daily` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `rate_date` DATE NOT NULL,
    `base_code` VARCHAR(3) NOT NULL DEFAULT 'USD',
    `usd` DECIMAL(12,6) NOT NULL DEFAULT 1.000000,
    `eur` DECIMAL(12,6) NOT NULL,
    `mad` DECIMAL(12,6) NOT NULL,
    `aed` DECIMAL(12,6) NOT NULL,
    `gbp` DECIMAL(12,6) NOT NULL,
    `source` VARCHAR(32) NOT NULL DEFAULT 'exchange-rate-api',
    `fetched_at` DATETIME NOT NULL,
    `status` VARCHAR(16) NOT NULL DEFAULT 'ok',
    `raw_json` LONGTEXT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `idx_rate_date_base` (`rate_date`, `base_code`),
    KEY `idx_rate_date` (`rate_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- SECTION 6: TABLE subscription_plans
-- =====================================================

CREATE TABLE IF NOT EXISTS `subscription_plans` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    `description` TEXT,
    `amount` DECIMAL(10,2) NOT NULL DEFAULT 790.00,
    `currency` VARCHAR(3) NOT NULL DEFAULT 'EUR',
    `interval_type` ENUM('month','year') NOT NULL DEFAULT 'month',
    `interval_count` INT NOT NULL DEFAULT 1,
    `active` BOOLEAN NOT NULL DEFAULT 1,
    `is_default` BOOLEAN NOT NULL DEFAULT 0,
    `trial_days` INT NOT NULL DEFAULT 14,
    `created_at` INT NOT NULL,
    `updated_at` INT NOT NULL,
    INDEX `idx_plans_active` (`active`),
    INDEX `idx_plans_currency` (`currency`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert plans par défaut si vide
INSERT INTO `subscription_plans` (`name`, `description`, `amount`, `currency`, `interval_type`, `interval_count`, `active`, `is_default`, `trial_days`, `created_at`, `updated_at`)
SELECT 'Monthly Subscription', 'Abonnement mensuel', 790.00, 'EUR', 'month', 1, 1, 1, 14, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()
WHERE NOT EXISTS (SELECT 1 FROM `subscription_plans` WHERE `is_default` = 1);

-- =====================================================
-- SECTION 7: TABLES billing_entities (entités facturation)
-- =====================================================

CREATE TABLE IF NOT EXISTS `billing_entities` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `code` VARCHAR(10) NOT NULL COMMENT 'Code unique (MA, UAE, EU)',
    `name` VARCHAR(100) NOT NULL,
    `legal_name` VARCHAR(200) NOT NULL,
    `country_code` VARCHAR(3) NOT NULL,
    `country_name` VARCHAR(100) NOT NULL,
    `country_flag` VARCHAR(10) DEFAULT NULL,
    `vat_rate` DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    `currency_code` VARCHAR(3) NOT NULL,
    `currency_symbol` VARCHAR(10) DEFAULT NULL,
    `bank_name` VARCHAR(100) DEFAULT NULL,
    `bank_country` VARCHAR(50) DEFAULT NULL,
    `psp_name` VARCHAR(50) DEFAULT NULL,
    `psp_type` ENUM('local', 'international') DEFAULT 'international',
    `address` TEXT DEFAULT NULL,
    `vat_number` VARCHAR(50) DEFAULT NULL,
    `registration_number` VARCHAR(50) DEFAULT NULL,
    `email` VARCHAR(100) DEFAULT NULL,
    `phone` VARCHAR(30) DEFAULT NULL,
    `is_default` TINYINT(1) NOT NULL DEFAULT 0,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `applies_to` JSON DEFAULT NULL,
    `display_order` INT(11) DEFAULT 0,
    `color_primary` VARCHAR(7) DEFAULT '#1a237e',
    `color_secondary` VARCHAR(7) DEFAULT '#3949ab',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `created_by` INT(11) UNSIGNED DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_code` (`code`),
    KEY `idx_country_code` (`country_code`),
    KEY `idx_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert entités par défaut
INSERT INTO `billing_entities` (`code`, `name`, `legal_name`, `country_code`, `country_name`, `country_flag`, `vat_rate`, `currency_code`, `currency_symbol`, `bank_name`, `psp_name`, `psp_type`, `is_default`, `is_active`, `applies_to`, `display_order`, `color_primary`, `color_secondary`)
VALUES ('MA', 'Decloedt SARL', 'Decloedt SARL', 'MA', 'Morocco', '🇲🇦', 20.00, 'MAD', 'DH', 'Banque Populaire', 'CashPlus', 'local', 1, 1, '["MA"]', 1, '#c62828', '#e53935')
ON DUPLICATE KEY UPDATE `updated_at` = CURRENT_TIMESTAMP;

INSERT INTO `billing_entities` (`code`, `name`, `legal_name`, `country_code`, `country_name`, `country_flag`, `vat_rate`, `currency_code`, `currency_symbol`, `bank_name`, `psp_name`, `psp_type`, `is_default`, `is_active`, `applies_to`, `display_order`, `color_primary`, `color_secondary`)
VALUES ('UAE', 'Bouhouti', 'Bouhouti LLC', 'AE', 'United Arab Emirates', '🇦🇪', 5.00, 'AED', 'AED', 'Emirates NBD', 'Stripe', 'international', 0, 1, '["AE","UAE","EU","US","UK","OTHER"]', 2, '#00695c', '#00897b')
ON DUPLICATE KEY UPDATE `updated_at` = CURRENT_TIMESTAMP;

-- Table mapping
CREATE TABLE IF NOT EXISTS `billing_entity_mappings` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `tax_residence_code` VARCHAR(10) NOT NULL,
    `billing_entity_id` INT(11) UNSIGNED NOT NULL,
    `priority` INT(11) DEFAULT 0,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_tax_residence` (`tax_residence_code`),
    KEY `idx_billing_entity` (`billing_entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert mappings
INSERT INTO `billing_entity_mappings` (`tax_residence_code`, `billing_entity_id`, `priority`)
SELECT 'MA', id, 100 FROM `billing_entities` WHERE code = 'MA'
ON DUPLICATE KEY UPDATE `priority` = VALUES(`priority`);

INSERT INTO `billing_entity_mappings` (`tax_residence_code`, `billing_entity_id`, `priority`)
SELECT 'UAE', id, 100 FROM `billing_entities` WHERE code = 'UAE'
ON DUPLICATE KEY UPDATE `priority` = VALUES(`priority`);

INSERT INTO `billing_entity_mappings` (`tax_residence_code`, `billing_entity_id`, `priority`)
SELECT 'AE', id, 100 FROM `billing_entities` WHERE code = 'UAE'
ON DUPLICATE KEY UPDATE `priority` = VALUES(`priority`);

-- Table payment methods
CREATE TABLE IF NOT EXISTS `billing_entity_payment_methods` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `billing_entity_id` INT(11) UNSIGNED NOT NULL,
    `method_code` VARCHAR(20) NOT NULL,
    `is_default` TINYINT(1) DEFAULT 0,
    `is_active` TINYINT(1) DEFAULT 1,
    `priority` INT(11) DEFAULT 0,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_entity_method` (`billing_entity_id`, `method_code`),
    KEY `idx_entity` (`billing_entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table credentials
CREATE TABLE IF NOT EXISTS `billing_entity_credentials` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `billing_entity_id` INT(11) UNSIGNED NOT NULL,
    `provider` VARCHAR(20) NOT NULL,
    `mode` ENUM('test', 'live', 'sandbox', 'production') DEFAULT 'test',
    `is_active` TINYINT(1) DEFAULT 1,
    `test_public_key` VARCHAR(255) DEFAULT NULL,
    `test_secret_key` VARCHAR(255) DEFAULT NULL,
    `live_public_key` VARCHAR(255) DEFAULT NULL,
    `live_secret_key` VARCHAR(255) DEFAULT NULL,
    `sandbox_client_id` VARCHAR(255) DEFAULT NULL,
    `sandbox_secret` VARCHAR(255) DEFAULT NULL,
    `production_client_id` VARCHAR(255) DEFAULT NULL,
    `production_secret` VARCHAR(255) DEFAULT NULL,
    `currency` VARCHAR(3) DEFAULT NULL,
    `webhook_secret` VARCHAR(255) DEFAULT NULL,
    `account_name` VARCHAR(100) DEFAULT NULL,
    `account_email` VARCHAR(100) DEFAULT NULL,
    `notes` TEXT DEFAULT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_entity_provider` (`billing_entity_id`, `provider`),
    KEY `idx_provider` (`provider`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- SECTION 8: MIGRATION DES DONNÉES
-- =====================================================

-- 8.1 Migrer Tax_residence → schools.country
UPDATE `schools` s
JOIN `settings_school` ss ON s.id = ss.school_id
SET s.country = CASE 
    WHEN UPPER(TRIM(ss.Tax_residence)) IN ('MAROC', 'MOROCCO', 'MA') THEN 'MA'
    WHEN UPPER(TRIM(ss.Tax_residence)) IN ('UAE', 'UNITED ARAB EMIRATES', 'EMIRATS', 'AE') THEN 'AE'
    WHEN UPPER(TRIM(ss.Tax_residence)) IN ('FRANCE', 'FR') THEN 'FR'
    ELSE UPPER(LEFT(TRIM(ss.Tax_residence), 2))
END
WHERE s.country IS NULL 
AND ss.Tax_residence IS NOT NULL 
AND TRIM(ss.Tax_residence) != '';

-- 8.2 Migrer exchange_rate → fx_rate dans invoices
UPDATE `invoices` SET `fx_rate` = `exchange_rate` WHERE `fx_rate` IS NULL AND `exchange_rate` IS NOT NULL;

-- 8.3 Ajouter devise MAD si manquante
INSERT INTO `currencies` (`name`, `code`, `symbol`, `paypal_supported`, `stripe_supported`, `paystack_supported`, `payumoney_supported`) 
SELECT 'Dirham marocain', 'MAD', 'د.م', 1, 1, 0, 1
WHERE NOT EXISTS (SELECT 1 FROM `currencies` WHERE `code` = 'MAD');

-- =====================================================
-- SECTION 9: NETTOYAGE FINAL
-- =====================================================

SET FOREIGN_KEY_CHECKS = 1;

SELECT '✅ MIGRATION PREPROD TERMINÉE' AS status, NOW() AS completed_at;
SELECT 
    (SELECT COUNT(*) FROM schools WHERE country IS NOT NULL) AS schools_with_country,
    (SELECT COUNT(*) FROM invoices WHERE fx_rate IS NOT NULL) AS invoices_with_fx_rate,
    (SELECT COUNT(*) FROM billing_entities) AS billing_entities,
    (SELECT COUNT(*) FROM subscription_plans) AS subscription_plans;


-- Version phpMyAdmin 

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";

ALTER TABLE `schools` ADD COLUMN IF NOT EXISTS `country` VARCHAR(2) DEFAULT NULL COMMENT 'Code pays ISO-2 (MA, AE, FR...)' AFTER `name`;

ALTER TABLE `schools` ADD COLUMN IF NOT EXISTS `trial_start` INT(11) NULL COMMENT 'Trial start timestamp' AFTER `price`;
ALTER TABLE `schools` ADD COLUMN IF NOT EXISTS `trial_end` INT(11) NULL COMMENT 'Trial end timestamp' AFTER `trial_start`;
ALTER TABLE `schools` ADD COLUMN IF NOT EXISTS `is_trial` TINYINT(1) DEFAULT 0 COMMENT '1 if in trial' AFTER `trial_end`;
ALTER TABLE `schools` ADD COLUMN IF NOT EXISTS `is_paid` TINYINT(1) DEFAULT 0 COMMENT '1 if paid' AFTER `is_trial`;
ALTER TABLE `schools` ADD COLUMN IF NOT EXISTS `subscription_end` INT(11) NULL COMMENT 'Subscription end timestamp' AFTER `is_paid`;
ALTER TABLE `schools` ADD COLUMN IF NOT EXISTS `subscription_status` ENUM('trialing','active','past_due','suspended','canceled') DEFAULT 'trialing' COMMENT 'Subscription state' AFTER `subscription_end`;

CREATE INDEX IF NOT EXISTS `idx_schools_country` ON `schools` (`country`);
CREATE INDEX IF NOT EXISTS `idx_schools_subscription_status` ON `schools` (`subscription_status`);

ALTER TABLE `settings_school` ADD COLUMN IF NOT EXISTS `vat_enabled` INT(11) DEFAULT 0 COMMENT 'TVA activée (0=non, 1=oui)';

ALTER TABLE `settings_school` ADD COLUMN IF NOT EXISTS `vat_rate` INT(11) DEFAULT 0 COMMENT 'Taux TVA (%)';

UPDATE `settings_school` SET `vat_enabled` = `vat` WHERE `vat_enabled` = 0 AND `vat` IS NOT NULL AND `vat` > 0;
UPDATE `settings_school` SET `vat_rate` = `vat_rat` WHERE `vat_rate` = 0 AND `vat_rat` IS NOT NULL AND `vat_rat` > 0;

ALTER TABLE `invoices` ADD COLUMN IF NOT EXISTS `vat_amount` DECIMAL(10,2) DEFAULT 0.00 COMMENT 'Montant TVA';
ALTER TABLE `invoices` ADD COLUMN IF NOT EXISTS `vat_rate` DECIMAL(5,2) DEFAULT 0.00 COMMENT 'Taux TVA (%)';
ALTER TABLE `invoices` ADD COLUMN IF NOT EXISTS `sub_total` DECIMAL(10,2) DEFAULT NULL COMMENT 'Montant HT';
ALTER TABLE `invoices` ADD COLUMN IF NOT EXISTS `tax_country` VARCHAR(100) DEFAULT NULL COMMENT 'Pays résidence fiscale';
ALTER TABLE `invoices` ADD COLUMN IF NOT EXISTS `legal_entity_name` VARCHAR(255) DEFAULT NULL COMMENT 'Entité juridique';
ALTER TABLE `invoices` ADD COLUMN IF NOT EXISTS `legal_entity_country` VARCHAR(255) DEFAULT NULL COMMENT 'Pays entité juridique';

ALTER TABLE `invoices` ADD COLUMN IF NOT EXISTS `currency` VARCHAR(3) DEFAULT NULL COMMENT 'Devise facture';
ALTER TABLE `invoices` ADD COLUMN IF NOT EXISTS `payment_currency` VARCHAR(3) DEFAULT NULL COMMENT 'Devise paiement réel';
ALTER TABLE `invoices` ADD COLUMN IF NOT EXISTS `payment_amount_converted` DECIMAL(10,2) DEFAULT NULL COMMENT 'Montant converti';
ALTER TABLE `invoices` ADD COLUMN IF NOT EXISTS `fx_rate` DECIMAL(12,6) DEFAULT NULL COMMENT 'Taux de change';
ALTER TABLE `invoices` ADD COLUMN IF NOT EXISTS `fx_rate_date` DATE DEFAULT NULL COMMENT 'Date taux change';
ALTER TABLE `invoices` ADD COLUMN IF NOT EXISTS `conversion_applied` TINYINT(1) DEFAULT 0 COMMENT '1 si conversion appliquée';

ALTER TABLE `invoices` ADD COLUMN IF NOT EXISTS `period_start` INT DEFAULT NULL COMMENT 'Début période abonnement';
ALTER TABLE `invoices` ADD COLUMN IF NOT EXISTS `period_end` INT DEFAULT NULL COMMENT 'Fin période abonnement';
ALTER TABLE `invoices` ADD COLUMN IF NOT EXISTS `stripe_payment_intent_id` VARCHAR(255) DEFAULT NULL COMMENT 'Stripe PaymentIntent ID';
ALTER TABLE `invoices` ADD COLUMN IF NOT EXISTS `stripe_invoice_id` VARCHAR(255) DEFAULT NULL COMMENT 'Stripe Invoice ID';
ALTER TABLE `invoices` ADD COLUMN IF NOT EXISTS `paid_at` INT DEFAULT NULL COMMENT 'Timestamp paiement';

ALTER TABLE `invoices` ADD COLUMN IF NOT EXISTS `processor_fee_ht` DECIMAL(10,2) DEFAULT NULL COMMENT 'Frais processeur HT';
ALTER TABLE `invoices` ADD COLUMN IF NOT EXISTS `processor_fee_vat` DECIMAL(10,2) DEFAULT NULL COMMENT 'TVA frais processeur';
ALTER TABLE `invoices` ADD COLUMN IF NOT EXISTS `processor_fee_ttc` DECIMAL(10,2) DEFAULT NULL COMMENT 'Frais processeur TTC';
ALTER TABLE `invoices` ADD COLUMN IF NOT EXISTS `net_cash` DECIMAL(10,2) DEFAULT NULL COMMENT 'Net cash reçu';
ALTER TABLE `invoices` ADD COLUMN IF NOT EXISTS `net_economic` DECIMAL(10,2) DEFAULT NULL COMMENT 'Net économique B2B';

ALTER TABLE `invoices` ADD COLUMN IF NOT EXISTS `payment_type` ENUM('class_enrol','community_join','subscription_admin') DEFAULT 'class_enrol' COMMENT 'Type de paiement';
ALTER TABLE `invoices` ADD COLUMN IF NOT EXISTS `payment_id` INT(11) DEFAULT NULL COMMENT 'Ref payments table';

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
    `payment_method` VARCHAR(50) NULL,
    `processor_name` VARCHAR(50) NULL COMMENT 'CashPlus, Stripe, etc.',
    `processor_fee_ht` DECIMAL(10,2) NULL,
    `processor_fee_vat` DECIMAL(10,2) NULL,
    `processor_fee_ttc` DECIMAL(10,2) NULL,
    `transaction_ref` VARCHAR(100) NULL,
    `paid_at` DATETIME NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_student_id` (`student_id`),
    KEY `idx_school_id` (`school_id`),
    KEY `idx_invoice_id` (`invoice_id`),
    KEY `idx_payment_status` (`payment_status`),
    KEY `idx_processor_name` (`processor_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `fx_rates_daily` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `rate_date` DATE NOT NULL,
    `base_code` VARCHAR(3) NOT NULL DEFAULT 'USD',
    `usd` DECIMAL(12,6) NOT NULL DEFAULT 1.000000,
    `eur` DECIMAL(12,6) NOT NULL,
    `mad` DECIMAL(12,6) NOT NULL,
    `aed` DECIMAL(12,6) NOT NULL,
    `gbp` DECIMAL(12,6) NOT NULL,
    `source` VARCHAR(32) NOT NULL DEFAULT 'exchange-rate-api',
    `fetched_at` DATETIME NOT NULL,
    `status` VARCHAR(16) NOT NULL DEFAULT 'ok',
    `raw_json` LONGTEXT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `idx_rate_date_base` (`rate_date`, `base_code`),
    KEY `idx_rate_date` (`rate_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `subscription_plans` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    `description` TEXT,
    `amount` DECIMAL(10,2) NOT NULL DEFAULT 790.00,
    `currency` VARCHAR(3) NOT NULL DEFAULT 'EUR',
    `interval_type` ENUM('month','year') NOT NULL DEFAULT 'month',
    `interval_count` INT NOT NULL DEFAULT 1,
    `active` BOOLEAN NOT NULL DEFAULT 1,
    `is_default` BOOLEAN NOT NULL DEFAULT 0,
    `trial_days` INT NOT NULL DEFAULT 14,
    `created_at` INT NOT NULL,
    `updated_at` INT NOT NULL,
    INDEX `idx_plans_active` (`active`),
    INDEX `idx_plans_currency` (`currency`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `subscription_plans` (`name`, `description`, `amount`, `currency`, `interval_type`, `interval_count`, `active`, `is_default`, `trial_days`, `created_at`, `updated_at`)
SELECT 'Monthly Subscription', 'Abonnement mensuel', 790.00, 'EUR', 'month', 1, 1, 1, 14, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()
WHERE NOT EXISTS (SELECT 1 FROM `subscription_plans` WHERE `is_default` = 1);

CREATE TABLE IF NOT EXISTS `billing_entities` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `code` VARCHAR(10) NOT NULL COMMENT 'Code unique (MA, UAE, EU)',
    `name` VARCHAR(100) NOT NULL,
    `legal_name` VARCHAR(200) NOT NULL,
    `country_code` VARCHAR(3) NOT NULL,
    `country_name` VARCHAR(100) NOT NULL,
    `country_flag` VARCHAR(10) DEFAULT NULL,
    `vat_rate` DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    `currency_code` VARCHAR(3) NOT NULL,
    `currency_symbol` VARCHAR(10) DEFAULT NULL,
    `bank_name` VARCHAR(100) DEFAULT NULL,
    `bank_country` VARCHAR(50) DEFAULT NULL,
    `psp_name` VARCHAR(50) DEFAULT NULL,
    `psp_type` ENUM('local', 'international') DEFAULT 'international',
    `address` TEXT DEFAULT NULL,
    `vat_number` VARCHAR(50) DEFAULT NULL,
    `registration_number` VARCHAR(50) DEFAULT NULL,
    `email` VARCHAR(100) DEFAULT NULL,
    `phone` VARCHAR(30) DEFAULT NULL,
    `is_default` TINYINT(1) NOT NULL DEFAULT 0,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `applies_to` JSON DEFAULT NULL,
    `display_order` INT(11) DEFAULT 0,
    `color_primary` VARCHAR(7) DEFAULT '#1a237e',
    `color_secondary` VARCHAR(7) DEFAULT '#3949ab',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `created_by` INT(11) UNSIGNED DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_code` (`code`),
    KEY `idx_country_code` (`country_code`),
    KEY `idx_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `billing_entities` (`code`, `name`, `legal_name`, `country_code`, `country_name`, `country_flag`, `vat_rate`, `currency_code`, `currency_symbol`, `bank_name`, `psp_name`, `psp_type`, `is_default`, `is_active`, `applies_to`, `display_order`, `color_primary`, `color_secondary`)
VALUES ('MA', 'Decloedt SARL', 'Decloedt SARL', 'MA', 'Morocco', '🇲🇦', 20.00, 'MAD', 'DH', 'Banque Populaire', 'CashPlus', 'local', 1, 1, '["MA"]', 1, '#c62828', '#e53935')
ON DUPLICATE KEY UPDATE `updated_at` = CURRENT_TIMESTAMP;

INSERT INTO `billing_entities` (`code`, `name`, `legal_name`, `country_code`, `country_name`, `country_flag`, `vat_rate`, `currency_code`, `currency_symbol`, `bank_name`, `psp_name`, `psp_type`, `is_default`, `is_active`, `applies_to`, `display_order`, `color_primary`, `color_secondary`)
VALUES ('UAE', 'Bouhouti', 'Bouhouti LLC', 'AE', 'United Arab Emirates', '🇦🇪', 5.00, 'AED', 'AED', 'Emirates NBD', 'Stripe', 'international', 0, 1, '["AE","UAE","EU","US","UK","OTHER"]', 2, '#00695c', '#00897b')
ON DUPLICATE KEY UPDATE `updated_at` = CURRENT_TIMESTAMP;

CREATE TABLE IF NOT EXISTS `billing_entity_mappings` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `tax_residence_code` VARCHAR(10) NOT NULL,
    `billing_entity_id` INT(11) UNSIGNED NOT NULL,
    `priority` INT(11) DEFAULT 0,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_tax_residence` (`tax_residence_code`),
    KEY `idx_billing_entity` (`billing_entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `billing_entity_mappings` (`tax_residence_code`, `billing_entity_id`, `priority`)
SELECT 'MA', id, 100 FROM `billing_entities` WHERE code = 'MA'
ON DUPLICATE KEY UPDATE `priority` = VALUES(`priority`);

INSERT INTO `billing_entity_mappings` (`tax_residence_code`, `billing_entity_id`, `priority`)
SELECT 'UAE', id, 100 FROM `billing_entities` WHERE code = 'UAE'
ON DUPLICATE KEY UPDATE `priority` = VALUES(`priority`);

INSERT INTO `billing_entity_mappings` (`tax_residence_code`, `billing_entity_id`, `priority`)
SELECT 'AE', id, 100 FROM `billing_entities` WHERE code = 'UAE'
ON DUPLICATE KEY UPDATE `priority` = VALUES(`priority`);

CREATE TABLE IF NOT EXISTS `billing_entity_payment_methods` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `billing_entity_id` INT(11) UNSIGNED NOT NULL,
    `method_code` VARCHAR(20) NOT NULL,
    `is_default` TINYINT(1) DEFAULT 0,
    `is_active` TINYINT(1) DEFAULT 1,
    `priority` INT(11) DEFAULT 0,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_entity_method` (`billing_entity_id`, `method_code`),
    KEY `idx_entity` (`billing_entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `billing_entity_credentials` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `billing_entity_id` INT(11) UNSIGNED NOT NULL,
    `provider` VARCHAR(20) NOT NULL,
    `mode` ENUM('test', 'live', 'sandbox', 'production') DEFAULT 'test',
    `is_active` TINYINT(1) DEFAULT 1,
    `test_public_key` VARCHAR(255) DEFAULT NULL,
    `test_secret_key` VARCHAR(255) DEFAULT NULL,
    `live_public_key` VARCHAR(255) DEFAULT NULL,
    `live_secret_key` VARCHAR(255) DEFAULT NULL,
    `sandbox_client_id` VARCHAR(255) DEFAULT NULL,
    `sandbox_secret` VARCHAR(255) DEFAULT NULL,
    `production_client_id` VARCHAR(255) DEFAULT NULL,
    `production_secret` VARCHAR(255) DEFAULT NULL,
    `currency` VARCHAR(3) DEFAULT NULL,
    `webhook_secret` VARCHAR(255) DEFAULT NULL,
    `account_name` VARCHAR(100) DEFAULT NULL,
    `account_email` VARCHAR(100) DEFAULT NULL,
    `notes` TEXT DEFAULT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_entity_provider` (`billing_entity_id`, `provider`),
    KEY `idx_provider` (`provider`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

UPDATE `schools` s
JOIN `settings_school` ss ON s.id = ss.school_id
SET s.country = CASE 
    WHEN UPPER(TRIM(ss.Tax_residence)) IN ('MAROC', 'MOROCCO', 'MA') THEN 'MA'
    WHEN UPPER(TRIM(ss.Tax_residence)) IN ('UAE', 'UNITED ARAB EMIRATES', 'EMIRATS', 'AE') THEN 'AE'
    WHEN UPPER(TRIM(ss.Tax_residence)) IN ('FRANCE', 'FR') THEN 'FR'
    ELSE UPPER(LEFT(TRIM(ss.Tax_residence), 2))
END
WHERE s.country IS NULL 
AND ss.Tax_residence IS NOT NULL 
AND TRIM(ss.Tax_residence) != '';

UPDATE `invoices` SET `fx_rate` = `exchange_rate` WHERE `fx_rate` IS NULL AND `exchange_rate` IS NOT NULL;

INSERT INTO `currencies` (`name`, `code`, `symbol`, `paypal_supported`, `stripe_supported`, `paystack_supported`, `payumoney_supported`) 
SELECT 'Dirham marocain', 'MAD', 'د.م', 1, 1, 0, 1
WHERE NOT EXISTS (SELECT 1 FROM `currencies` WHERE `code` = 'MAD');
