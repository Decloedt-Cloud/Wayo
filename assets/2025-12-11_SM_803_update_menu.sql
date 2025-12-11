INSERT INTO menus (
    id, displayed_name, route_name, parent, icon, status,
    superadmin_access, admin_access, teacher_access, student_access,
    accountant_access, librarian_access, sort_order, is_addon, unique_identifier
) VALUES (
    144, 'invoice', 'invoice', 0, 'fas fa-file-invoice', 1,
    0, 0, 0, 1,
    0, 0, 13, 0, 'management'
);