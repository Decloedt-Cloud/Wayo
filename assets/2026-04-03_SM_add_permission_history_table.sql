CREATE TABLE IF NOT EXISTS `permission_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `teacher_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `column_name` varchar(50) NOT NULL,
  `old_value` tinyint(1) NOT NULL DEFAULT 0,
  `new_value` tinyint(1) NOT NULL DEFAULT 0,
  `changed_by` int(11) NOT NULL,
  `school_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `teacher_id` (`teacher_id`),
  KEY `class_id` (`class_id`),
  KEY `changed_by` (`changed_by`),
  KEY `school_id` (`school_id`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
