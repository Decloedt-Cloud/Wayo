

UPDATE menus
SET displayed_name = 'certifications'
WHERE displayed_name = 'exam';

UPDATE menus
SET status = 0
WHERE displayed_name = 'exam';