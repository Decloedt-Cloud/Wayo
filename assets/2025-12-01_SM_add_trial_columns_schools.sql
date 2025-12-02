ALTER TABLE schools
ADD COLUMN trial_start INT(11) NULL AFTER price,
ADD COLUMN trial_end INT(11) NULL AFTER trial_start,
ADD COLUMN is_trial TINYINT(1) DEFAULT 0 AFTER trial_end,
ADD COLUMN is_paid TINYINT(1) DEFAULT 0 AFTER is_trial;

