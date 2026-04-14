-- Add email verification token and timestamp to users table
ALTER TABLE `users` ADD COLUMN `email_verification_token` VARCHAR(255) NULL DEFAULT NULL AFTER `remember_token`;
ALTER TABLE `users` ADD COLUMN `email_verification_expires` DATETIME NULL DEFAULT NULL AFTER `email_verification_token`;
ALTER TABLE `users` ADD INDEX `idx_email_verification_token` (`email_verification_token`);
