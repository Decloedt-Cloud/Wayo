


insert into menus (displayed_name, route_name, parent, icon, status, superadmin_access, admin_access, teacher_access, student_access, accountant_access, librarian_access, sort_order, is_addon, unique_identifier, category, category_order) 
values ('billing_entities', 'billing_entities', 0, 'fas fa-file-invoice-dollar', 1, 1, 1, 1, 1, 1, 1, 11, 0, 'billing_entities', 'accounting',2);
insert into menus (displayed_name, route_name, parent, icon, status, superadmin_access, admin_access, teacher_access, student_access, accountant_access, librarian_access, sort_order, is_addon, unique_identifier, category, category_order) 
values ('payment_methods', 'payment_methods', 143, 'fas fa-file-invoice-dollar', 1, 1, 1, 1, 1, 1, 1, 12, 0, 'payment_methods', 'accounting', 2);

insert into menus (displayed_name, route_name, parent, icon, status, superadmin_access, admin_access, teacher_access, student_access, accountant_access, librarian_access, sort_order, is_addon, unique_identifier, category, category_order) 
values ('admin_fee_manager', 'invoice', 0, 'fas fa-file-invoice-dollar', 1, 1, 1, 1, 1, 1, 1, 13, 0, 'admin_fee_manager', 'accounting', 2);

insert into menus (displayed_name, route_name, parent, icon, status, superadmin_access, admin_access, teacher_access, student_access, accountant_access, librarian_access, sort_order, is_addon, unique_identifier, category, category_order) 
values ('cost_accounting', 'expense_category', 145, 'fas fa-file-invoice-dollar', 1, 1, 1, 1, 1, 1, 1, 14, 0, 'cost_accounting', 'accounting', 2);

insert into menus (displayed_name, route_name, parent, icon, status, superadmin_access, admin_access, teacher_access, student_access, accountant_access, librarian_access, sort_order, is_addon, unique_identifier, category, category_order) 
values ('cost_center', 'expense', 145, 'fas fa-file-invoice-dollar', 1, 1, 1, 1, 1, 1, 1, 15, 0, 'cost_center', 'accounting', 2);


UPDATE `menus` SET `displayed_name` = 'student_fee_manager' WHERE `displayed_name` = 'accounting';

UPDATE `menus` SET `route_name` = 'invoice' WHERE id = 24;
UPDATE `formation_preprod`.`menus` SET `admin_access` = '0' WHERE (`id` = '25');

UPDATE `menus` SET `category_order` = '2', `category` = 'accounting' WHERE (`id` = '24');
UPDATE `menus` SET `category_order` = '3' WHERE (`id` = '140');
UPDATE `menus` SET `category_order` = '3' WHERE (`id` = '28');
UPDATE `menus` SET `category_order` = '3' WHERE (`id` = '136');

UPDATE `menus` SET `displayed_name` = 'cost_center' WHERE (`id` = '27');
UPDATE `menus` SET `displayed_name` = 'cost_accounting' WHERE (`id` = '55');
