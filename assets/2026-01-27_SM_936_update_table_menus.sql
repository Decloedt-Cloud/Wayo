UPDATE menus
SET superadmin_access = 0
WHERE displayed_name = 'calendar';

UPDATE menus
SET superadmin_access = 0
WHERE displayed_name = 'offers and prices';

UPDATE menus
SET superadmin_access = 0
WHERE displayed_name = 'online_courses';

UPDATE menus
SET superadmin_access = 0
WHERE displayed_name = 'certifications';

UPDATE menus
SET superadmin_access = 0
WHERE displayed_name = 'online_admission';

UPDATE menus
SET superadmin_access = 0
WHERE displayed_name = 'Announcements';

UPDATE menus
SET superadmin_access = 0
WHERE displayed_name = 'student';

UPDATE menus
SET superadmin_access = 0
WHERE displayed_name = 'admission';

UPDATE menus
SET superadmin_access = 0
WHERE displayed_name = 'teacher';

UPDATE menus
SET superadmin_access = 0
WHERE displayed_name = 'teacher_permission';

UPDATE menus
SET superadmin_access = 0
WHERE displayed_name = 'accountant';

ALTER TABLE `subscription_plans` ADD COLUMN `country` VARCHAR(10) DEFAULT 'MA' AFTER `name`;

UPDATE `subscription_plans` SET `country` = 'MA' WHERE `country` IS NULL OR `country` = '';

INSERT INTO `subscription_plans` 
(`name`, `country`, `description`, `amount`, `currency`, `interval_type`, `interval_count`, `active`, `is_default`, `trial_days`, `created_at`, `updated_at`) 
VALUES 
('AE Starter Plan', 'AE', 'Starter plan for Affiliate Entrepreneurs in UAE', 300.00, 'AED', 'month', 1, 1, 0, 14, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('AE Pro Plan', 'AE', 'Pro plan for Affiliate Entrepreneurs in UAE', 3000.00, 'AED', 'year', 1, 1, 0, 14, UNIX_TIMESTAMP(), UNIX_TIMESTAMP());


ALTER TABLE schools DROP COLUMN humhub_space_id;