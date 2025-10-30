UPDATE
    menus
SET
    sort_order = CASE
        displayed_name
        WHEN 'calendar' THEN 9
        WHEN 'class' THEN 10
        WHEN 'online_courses' THEN 11
        WHEN 'certifications' THEN 12
        WHEN 'online_admission' THEN 13
        WHEN 'online_admission_school' THEN 14
        ELSE sort_order
    END,
    status = CASE
        displayed_name
        WHEN 'library' THEN 0
        WHEN 'wall' THEN 0
        WHEN 'noticeboard' THEN 0
        WHEN 'Announcement' THEN 0
        ELSE status
    END,
    icon = CASE
        displayed_name
        WHEN 'calendar' THEN 'fas fa-calendar-alt fa-fw'
        WHEN 'daily_attendance' THEN 'fas fa-clipboard-user fa-fw'
        WHEN 'Recordings' THEN 'fas fa-save fa-fw'
        WHEN 'class' THEN 'fas fa-chalkboard fa-fw'
        WHEN 'online_courses' THEN 'fas fa-layer-group fa-fw'
        WHEN 'quiz_result' THEN 'fas fa-poll fa-fw'
        WHEN 'syllabus' THEN 'fas fa-folder-open fa-fw'
        WHEN 'online_admission' THEN 'fas fa-tag fa-fw'
        WHEN 'online_admission_school' THEN 'fas fa-tag fa-fw'
        WHEN 'social' THEN 'fas fa-globe fa-fw'
        WHEN 'wall' THEN 'fas fa-stream'
        WHEN 'people' THEN 'fa-solid fa-user'
        WHEN 'spaces' THEN 'fas fa-user-shield fa-fw'
        WHEN 'chat' THEN 'fas fa-comments fa-fw'
        WHEN 'Announcements' THEN 'fas fa-bullhorn fa-fw'
        WHEN 'session_manager' THEN 'fas fa-clock'
        WHEN 'addon_manager' THEN 'fas fa-puzzle-piece'
        WHEN 'noticeboard' THEN 'fas fa-thumbtack'
        WHEN 'Announcement' THEN 'fas fa-bell'
        WHEN 'users' THEN 'fas fa-users-cog fa-fw'
        WHEN 'admin' THEN 'fas fa-user-cog'
        WHEN 'school' THEN 'fas fa-users'
        WHEN 'student' THEN 'fas fa-user-group fa-fw'
        WHEN 'admission' THEN 'fas fa-user-plus fa-fw'
        WHEN 'teacher' THEN 'fas fa-user-plus fa-fw'
        WHEN 'teacher_permission' THEN 'fas fa-shield-halved fa-fw'
        WHEN 'accountant' THEN 'fas fa-calculator'
        WHEN 'settings' THEN 'fas fa-sliders-h fa-fw'
        WHEN 'system_settings' THEN 'fas fa-cog'
        WHEN 'website_settings' THEN 'fas fa-cogs'
        WHEN 'school_settings' THEN 'fas fa-building-user fa-fw'
        WHEN 'payment_settings' THEN 'fas fa-credit-card'
        WHEN 'language_settings' THEN 'fas fa-language'
        WHEN 'accounting' THEN 'fas fa-file-invoice-dollar'
        WHEN 'student_fee_manager' THEN 'fas fa-users-cog'
        WHEN 'expense_manager' THEN 'fas fa-money-bill-wave'
        WHEN 'expense_category' THEN 'fas fa-tags'
        WHEN 'SMTP_settings' THEN 'fas fa-envelope-open-text'
        WHEN 'about' THEN 'fas fa-info-circle'
        ELSE icon
    END,
    unique_identifier = CASE
        displayed_name
        WHEN 'online_courses' THEN 'all_courses'
        WHEN 'social' THEN 'central'
        WHEN 'Announcements' THEN 'event-calender'
        ELSE unique_identifier
    END,
    category_order = CASE
        displayed_name
        WHEN 'Dashboard' THEN 1
        WHEN 'calendar' THEN 1
        WHEN 'class' THEN 1
        WHEN 'online_courses' THEN 1
        WHEN 'certifications' THEN 1
        WHEN 'online_admission' THEN 1
        WHEN 'online_admission_school' THEN 1
        WHEN 'social' THEN 2
        WHEN 'chat' THEN 2
        WHEN 'Announcements' THEN 2
        WHEN 'users' THEN 3
        WHEN 'settings' THEN 3
        ELSE category_order
    END,
    category = CASE
        displayed_name
        WHEN 'Dashboard' THEN 'management'
        WHEN 'calendar' THEN 'management'
        WHEN 'class' THEN 'management'
        WHEN 'online_courses' THEN 'management'
        WHEN 'online_admission' THEN 'management'
        WHEN 'online_admission_school' THEN 'management'
        WHEN 'social' THEN 'communication'
        WHEN 'chat' THEN 'communication'
        WHEN 'Announcements' THEN 'communication'
        WHEN 'users' THEN 'settings'
        WHEN 'settings' THEN 'settings'
        ELSE category
    END
WHERE
    displayed_name IN (
        'calendar',
        'class',
        'online_courses',
        'online_admission',
        'certifications',
        'online_admission_school',
        'library',
        'daily_attendance',
        'Recordings',
        'quiz_result',
        'syllabus',
        'social',
        'wall',
        'people',
        'spaces',
        'chat',
        'Announcements',
        'session_manager',
        'addon_manager',
        'noticeboard',
        'Announcement',
        'users',
        'admin',
        'school',
        'student',
        'admission',
        'teacher',
        'teacher_permission',
        'accountant',
        'settings',
        'system_settings',
        'website_settings',
        'school_settings',
        'payment_settings',
        'language_settings',
        'accounting',
        'student_fee_manager',
        'expense_manager',
        'expense_category',
        'SMTP_settings',
        'about',
        'Dashboard'
    );