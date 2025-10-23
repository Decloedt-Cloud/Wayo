UPDATE menus
SET status = 0
WHERE displayed_name = 'calendar';

UPDATE menus
SET displayed_name = 'calendar'
WHERE displayed_name = 'live classes';