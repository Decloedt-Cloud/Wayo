ALTER TABLE recordings 
ADD COLUMN formatted_duration VARCHAR(8) DEFAULT NULL AFTER duration;