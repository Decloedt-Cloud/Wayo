Ticket SM-722 :
UPDATE menus
SET sort_order = 27
WHERE displayed_name = 'chat';
--------------------------------------------------------------------------------------

Ticket SM-724 :
UPDATE menus
SET status = 0
WHERE displayed_name = 'librarian';

--------------------------------------------------------------------------------------
Ticket SM-725 :
UPDATE menus
SET displayed_name = 'Announcements'
WHERE displayed_name = 'back_office';

UPDATE menus
SET parent = 28
WHERE displayed_name = 'Announcement';

 
UPDATE menus
SET parent = 125
WHERE displayed_name = 'syllabus';
--------------------------------------------------------------------------------------
Ticket SM-725 --j'ai ajouté une nouvelle entrée dans la table menus--
INSERT INTO `menus`
(`displayed_name`, `route_name`, `parent`, `icon`, `status`, `superadmin_access`, `admin_access`, `teacher_access`, `student_access`, `accountant_access`, `librarian_access`, `sort_order`, `is_addon`, `unique_identifier`)
VALUES
('all_courses', 'courses', 125, NULL, 1, 1, 1, 1, 1, 0, 0, 21, 1, 'all_courses');
-----------------------------------------------------------------------------------------
Ticket SM-728
UPDATE menus
SET parent = 0, sort_order = 10, icon = 'dripicons-document'
WHERE displayed_name = 'class';

 ----------------------------------------------------------------------------------------
Ticket SM-734
UPDATE menus
SET parent = 33
WHERE displayed_name = 'accounting';

-----------------------------------------------------------------------------------------------------------------------------------
