UPDATE `menus` SET `displayed_name` = 'cost_center' WHERE `menus`.`id` = 55;
UPDATE `menus` SET `displayed_name` = 'cost_accounting' WHERE `menus`.`id` = 27;

ALTER TABLE `expense_categories` 
CHANGE COLUMN `cost_center` `cost_center` VARCHAR(255) NULL DEFAULT NULL COMMENT 'Centre de coût - chiffres et caractères'


ALTER TABLE `expense_categories` 
ADD COLUMN `department` VARCHAR(255) NULL DEFAULT NULL COMMENT 'Département associé à la catégorie' AFTER `cost_center`;

ALTER TABLE `announcement` CHANGE `id` `id` INT(11) NOT NULL AUTO_INCREMENT, add PRIMARY KEY (`id`);