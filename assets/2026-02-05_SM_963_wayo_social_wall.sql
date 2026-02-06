-- ====================================================================
-- Wayo Walls V1 - Database Migration
-- ====================================================================
-- Description: Create tables for the wall/feed system
-- Version: V1
-- Date: 2026-02-03
-- ====================================================================
 
-- 1. Add new mentor permissions to teacher_permissions table
-- ====================================================================
ALTER TABLE `teacher_permissions`
ADD COLUMN `mentor_can_access_class` TINYINT(1) DEFAULT 0 AFTER `online_exam`,
ADD COLUMN `mentor_can_post_on_class_wall` TINYINT(1) DEFAULT 0 AFTER `mentor_can_access_class`;
 
-- 2. Add content field to announcement table for rich text
-- ====================================================================
ALTER TABLE `announcement`
ADD COLUMN `content` TEXT NULL AFTER `title`;
 
-- 3. Create walls table
-- ====================================================================
CREATE TABLE IF NOT EXISTS `walls` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `scope_type` ENUM('community', 'class') NOT NULL,
  `scope_id` INT(11) UNSIGNED NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_wall` (`scope_type`, `scope_id`),
  KEY `idx_scope_type` (`scope_type`),
  KEY `idx_scope_id` (`scope_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
 
-- 4. Create posts table
-- ====================================================================
CREATE TABLE IF NOT EXISTS `posts` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `wall_id` INT(11) UNSIGNED NOT NULL,
  `author_user_id` INT(11) UNSIGNED NOT NULL,
  `type` ENUM('post', 'announcement') NOT NULL DEFAULT 'post',
  `title` VARCHAR(255) NULL,
  `body` TEXT NOT NULL,
  `status` ENUM('published', 'hidden') NOT NULL DEFAULT 'published',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_wall_id` (`wall_id`),
  KEY `idx_author_user_id` (`author_user_id`),
  KEY `idx_status` (`status`),
  KEY `idx_created_at` (`created_at`),
  KEY `idx_deleted_at` (`deleted_at`),
  KEY `idx_type` (`type`),
  CONSTRAINT `fk_posts_wall` FOREIGN KEY (`wall_id`) REFERENCES `walls` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_posts_author` FOREIGN KEY (`author_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
 
-- 5. Create post_attachments table
-- ====================================================================
CREATE TABLE IF NOT EXISTS `post_attachments` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `post_id` INT(11) UNSIGNED NOT NULL,
  `path` VARCHAR(500) NOT NULL,
  `filename` VARCHAR(255) NOT NULL,
  `mime_type` VARCHAR(100) NOT NULL,
  `size` BIGINT(20) UNSIGNED NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_post_id` (`post_id`),
  KEY `idx_mime_type` (`mime_type`),
  CONSTRAINT `fk_attachments_post` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
 
-- 6. Create post_reports table
-- ====================================================================
CREATE TABLE IF NOT EXISTS `post_reports` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `post_id` INT(11) UNSIGNED NOT NULL,
  `reporter_user_id` INT(11) UNSIGNED NOT NULL,
  `reason` TEXT NOT NULL,
  `status` ENUM('pending', 'resolved', 'dismissed') NOT NULL DEFAULT 'pending',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_post_id` (`post_id`),
  KEY `idx_reporter_user_id` (`reporter_user_id`),
  KEY `idx_status` (`status`),
  CONSTRAINT `fk_reports_post` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_reports_reporter` FOREIGN KEY (`reporter_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
 
-- 7. Create moderation_actions table
-- ====================================================================
CREATE TABLE IF NOT EXISTS `moderation_actions` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `post_id` INT(11) UNSIGNED NOT NULL,
  `actor_user_id` INT(11) UNSIGNED NOT NULL,
  `action` ENUM('hide', 'unhide', 'delete') NOT NULL,
  `note` TEXT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_post_id` (`post_id`),
  KEY `idx_actor_user_id` (`actor_user_id`),
  KEY `idx_action` (`action`),
  KEY `idx_created_at` (`created_at`),
  CONSTRAINT `fk_moderation_post` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_moderation_actor` FOREIGN KEY (`actor_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
 
-- ====================================================================
-- End of Migration
-- ====================================================================

INSERT INTO `menus`
(`displayed_name`, `route_name`, `parent`, `icon`, `status`, `superadmin_access`, `admin_access`, `teacher_access`, `student_access`, `accountant_access`, `librarian_access`, `sort_order`, `is_addon`, `unique_identifier`, `category_order`, `category`)
VALUES
('Community Wall', 'community_wall', 0, 'fas fa-users fa-fw', 1, 1, 1, 1, 1, 0, 0, 25, 0, 'community_wall', 1, 'communication'),
('Class Wall', 'class_wall', 0, 'fas fa-chalkboard-teacher fa-fw', 1, 1, 1, 1, 1, 0, 0, 26, 0, 'class_wall', 2, 'communication');