DELETE FROM menus
WHERE displayed_name = "Department";

ALTER TABLE teachers DROP COLUMN department_id;

DROP TABLE `departments`