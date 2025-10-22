--Pour calendar
UPDATE menus
SET sort_order = 20
WHERE displayed_name = 'calendar';

--Pour daily_attendance
UPDATE menus
SET sort_order = 21
WHERE displayed_name = 'daily_attendance';