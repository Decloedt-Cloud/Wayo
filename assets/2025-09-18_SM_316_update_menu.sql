INSERT INTO menus (
    id, displayed_name, route_name, parent, icon, status, 
    superadmin_access, admin_access, teacher_access, student_access, 
    accountant_access, librarian_access, sort_order, is_addon, unique_identifier
) VALUES (
    20, 'exam', 'exam', 19, NULL, 1, 
    1, 1, 1, 1, 
    0, 0, 20, 0, 'exam'
);