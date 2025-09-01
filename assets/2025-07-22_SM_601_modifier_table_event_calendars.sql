ALTER TABLE event_calendars
MODIFY COLUMN title VARCHAR(255) NOT NULL,
MODIFY COLUMN starting_date DATE NOT NULL,
MODIFY COLUMN ending_date DATE NULL,
ADD COLUMN starting_time TIME NOT NULL AFTER starting_date,
ADD COLUMN ending_time TIME NOT NULL AFTER ending_date,
ADD COLUMN description TEXT NULL,
ADD COLUMN class_id INT(11) NOT NULL,
ADD COLUMN recurrence_type ENUM('does_not_repeat', 'every_weekday', 'daily', 'weekly', 'monthly', 'yearly', 'custom') DEFAULT 'does_not_repeat',
ADD COLUMN recurrence_end_date DATE NULL,
ADD COLUMN custom_recurrence JSON NULL,
ADD COLUMN visio INT(11) DEFAULT 0;