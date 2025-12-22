ALTER TABLE `syllabuses` 
ADD COLUMN `extracted_text` LONGTEXT NULL AFTER `file`,
ADD COLUMN `page_count` INT(11) DEFAULT 1 AFTER `extracted_text`;