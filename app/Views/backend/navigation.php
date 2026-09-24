<?php
$appPrefix = 'app';
// Récupérer le nombre total d'examens non passés pour l'étudiant connecté
$user_id = session()->get('user_id');
$session = active_session();
$current_school_id = (int) school_id();
if ($current_school_id <= 0) {
    $current_school_id = (int) (session()->get('active_school_id') ?? session()->get('school_id') ?? 0);
}
if ($current_school_id <= 0) {
    $user_school_row = $this->db->table('users')->select('school_id')->where('id', (int) $user_id)->get()->getRowArray();
    $current_school_id = (int) ($user_school_row['school_id'] ?? 0);
}
if ($current_school_id <= 0) {
    $current_school_id = (int) get_settings('school_id');
}

$pending_exams_count = 0;
$pending_students = 0;
$pending_schools = 0;
try {
    $pendingBuilder = $this->db->table('exams');
    $pendingBuilder->select('COUNT(DISTINCT exams.id) AS pending_count', false)
        ->join('enrols e', 'e.class_id = exams.class_id', 'inner')
        ->join('students s', 's.id = e.student_id', 'inner')
        ->where('s.user_id', (int) $user_id);
    if ($current_school_id > 0) {
        $pendingBuilder->where('exams.school_id', $current_school_id);
        $pendingBuilder->where('e.school_id', $current_school_id);
    }
    if ($session !== null && $session !== '') {
        $pendingBuilder->groupStart()
            ->where('exams.session', $session)
            ->orWhere('exams.session', null)
            ->orWhere('exams.session', '')
            ->groupEnd();
        $pendingBuilder->groupStart()
            ->where('e.session', $session)
            ->orWhere('e.session', null)
            ->orWhere('e.session', '')
            ->groupEnd();
    }
    $pendingBuilder->where(
        'NOT EXISTS (SELECT 1 FROM exam_responses er WHERE er.exam_id = exams.id AND er.user_id = ' . (int) $user_id . ')',
        null,
        false
    );
    $pendingRow = $pendingBuilder->get()->getRowArray();
    $pending_exams_count = (int) ($pendingRow['pending_count'] ?? 0);
    $pending_students = count(db()->table('students')->where(['status' => 0, 'school_id' => $current_school_id])->get()->getResultArray());
    $pending_schools = count(db()->table('schools')->where(['status' => 0, 'Etat' => 1])->get()->getResultArray());
} catch (\Throwable $e) {
    log_message('error', 'Sidebar counts failed: ' . $e->getMessage());
}
?>

<style>
    /* ========== MODERN NAVIGATION - Matching Dashboard Design ========== */
    .sidebar {
        --nav-primary: #6366f1;
        --nav-primary-light: #818cf8;
        --nav-secondary: #10b981;
        --nav-accent: #f59e0b;
        --nav-danger: #ef4444;
        --nav-bg-main: #f8fafc;
        --nav-bg-card: #ffffff;
        --nav-text-dark: #1e293b;
        --nav-text-muted: #64748b;
        --nav-border: #e2e8f0;
        --nav-shadow-sm: 0 1px 2px rgba(0,0,0,0.05);
        --nav-shadow-md: 0 4px 6px -1px rgba(0,0,0,0.07), 0 2px 4px -1px rgba(0,0,0,0.04);
        --nav-shadow-lg: 0 10px 15px -3px rgba(0,0,0,0.08), 0 4px 6px -2px rgba(0,0,0,0.04);
        
        width: 280px;
        background: linear-gradient(180deg, var(--nav-bg-card) 0%, #fafbfc 100%);
        border-right: 1px solid var(--nav-border);
        font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, sans-serif;
        display: flex;
        flex-direction: column;
        position: relative;
        overflow: hidden;
    }

    .sidebar::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 200px;
        background: linear-gradient(135deg, rgba(99, 102, 241, 0.03) 0%, rgba(129, 140, 248, 0.02) 100%);
        pointer-events: none;
    }

    /* ===== Header Profile Section ===== */
    .sidebar-header {
        padding: 24px 20px;
        text-align: center;
        border-bottom: 1px solid var(--nav-border);
        background: linear-gradient(180deg, rgba(99, 102, 241, 0.04) 0%, transparent 100%);
        position: relative;
        z-index: 1;
    }

    .sidebar-header a {
        text-decoration: none;
        color: inherit;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 12px;
        transition: all 0.3s ease;
    }

    .sidebar-header a:hover {
        transform: translateY(-2px);
    }

    .avatar {
        width: 72px;
        height: 72px;
        border: 3px solid var(--nav-bg-card);
        border-radius: 50%;
        object-fit: cover;
        box-shadow: 
            0 0 0 3px rgba(99, 102, 241, 0.15),
            0 8px 20px rgba(99, 102, 241, 0.2);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .sidebar-header a:hover .avatar {
        box-shadow: 
            0 0 0 4px rgba(99, 102, 241, 0.25),
            0 12px 28px rgba(99, 102, 241, 0.3);
        transform: scale(1.05);
    }

    .sidebar-header h3 {
        font-family: 'Outfit', 'DM Sans', sans-serif;
        font-size: 1.0625rem;
        font-weight: 600;
        color: var(--nav-text-dark);
        margin: 0;
        letter-spacing: -0.01em;
    }

    .sidebar-header .user-role {
        font-size: 0.75rem;
        color: var(--nav-text-muted);
        background: var(--nav-bg-main);
        padding: 4px 12px;
        border-radius: 20px;
        font-weight: 500;
        text-transform: capitalize;
    }

    /* ===== Navigation Container ===== */
    .sidebar-nav {
        padding: 16px 12px;
        flex-grow: 1;
        overflow-y: auto;
        position: relative;
        z-index: 1;
    }

    .sidebar-nav::-webkit-scrollbar {
        width: 5px;
    }

    .sidebar-nav::-webkit-scrollbar-track {
        background: transparent;
    }

    .sidebar-nav::-webkit-scrollbar-thumb {
        background: var(--nav-border);
        border-radius: 10px;
    }

    .sidebar-nav::-webkit-scrollbar-thumb:hover {
        background: var(--nav-text-muted);
    }

    .sidebar-nav ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    /* ===== Category Headers ===== */
    .nav-category {
        font-family: 'Outfit', sans-serif;
        font-size: 0.6875rem;
        color: var(--nav-text-muted);
        font-weight: 700;
        text-transform: uppercase;
        padding: 20px 16px 10px;
        letter-spacing: 0.08em;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .nav-category::after {
        content: '';
        flex: 1;
        height: 1px;
        background: linear-gradient(90deg, var(--nav-border) 0%, transparent 100%);
    }

    /* ===== Menu Items ===== */
    .sidebar-nav ul li a {
        display: flex;
        align-items: center;
        padding: 11px 14px;
        color: var(--nav-text-dark);
        text-decoration: none;
        border-radius: 12px;
        margin: 3px 0;
        font-size: 0.875rem;
        font-weight: 500;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }

    .sidebar-nav ul li a::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 0;
        background: linear-gradient(135deg, var(--nav-primary), var(--nav-primary-light));
        border-radius: 12px 0 0 12px;
        transition: width 0.25s ease;
    }

    .sidebar-nav ul li a:hover {
        background: linear-gradient(135deg, rgba(99, 102, 241, 0.08) 0%, rgba(129, 140, 248, 0.05) 100%);
        color: var(--nav-primary);
        transform: translateX(4px);
    }

    .sidebar-nav ul li a:hover::before {
        width: 4px;
    }

    .sidebar-nav ul li.active > a {
        background: linear-gradient(135deg, rgba(99, 102, 241, 0.12) 0%, rgba(129, 140, 248, 0.08) 100%);
        color: var(--nav-primary);
        font-weight: 600;
        box-shadow: var(--nav-shadow-sm);
    }

    .sidebar-nav ul li.active > a::before {
        width: 4px;
    }

    /* ===== Menu Icons ===== */
    .sidebar-nav .fa-fw {
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 12px;
        font-size: 1rem;
        color: var(--nav-text-muted);
        background: var(--nav-bg-main);
        border-radius: 10px;
        transition: all 0.25s ease;
        flex-shrink: 0;
    }

    .sidebar-nav ul li a:hover .fa-fw,
    .sidebar-nav ul li.active > a .fa-fw {
        color: white;
        background: linear-gradient(135deg, var(--nav-primary), var(--nav-primary-light));
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
    }

    /* ===== Badges ===== */
    .badge-nav {
        background: linear-gradient(135deg, var(--nav-danger), #f87171);
        color: white;
        font-size: 0.6875rem;
        padding: 3px 10px;
        border-radius: 20px;
        margin-left: auto;
        font-weight: 700;
        box-shadow: 0 2px 8px rgba(239, 68, 68, 0.35);
        animation: pulse-badge 2s infinite;
    }

    .chat-notification-dot {
        width: 10px;
        height: 10px;
        background-color: var(--nav-danger);
        border-radius: 50%;
        margin-left: auto;
        box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7);
        animation: pulse-dot 3s cubic-bezier(0.4, 0, 0.2, 1) infinite;
    }
    @keyframes pulse-dot {
        0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7); }
        50% { transform: scale(1); box-shadow: 0 0 0 6px rgba(239, 68, 68, 0); }
        100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
    }

    @keyframes pulse-badge {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
    }

    /* ===== Submenu Indicators ===== */
    .has-submenu .indicator {
        margin-left: auto;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        font-size: 0.65em;
        color: var(--nav-text-muted);
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--nav-bg-main);
        border-radius: 6px;
    }

    .has-submenu.open > a .indicator {
        transform: rotate(90deg);
        color: var(--nav-primary);
        background: rgba(99, 102, 241, 0.1);
    }

    .has-submenu > a:hover .indicator {
        background: rgba(99, 102, 241, 0.1);
        color: var(--nav-primary);
    }

    /* ===== Submenus ===== */
    .submenu {
        display: none;
        list-style: none;
        padding-left: 24px;
        margin-left: 18px;
        border-left: 2px solid var(--nav-border);
        margin-top: 6px;
        margin-bottom: 6px;
        animation: slideDown 0.3s ease;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .has-submenu.open > .submenu {
        display: block;
    }

    .submenu li a {
        font-size: 0.8125rem !important;
        font-weight: 500 !important;
        color: var(--nav-text-muted) !important;
        padding: 9px 14px !important;
        margin: 2px 0 !important;
        border-radius: 10px !important;
    }

    .submenu li a::before {
        display: none !important;
    }

    .submenu li a::after {
        content: '';
        position: absolute;
        left: -24px;
        top: 50%;
        transform: translateY(-50%);
        width: 14px;
        height: 2px;
        background: var(--nav-border);
        transition: background 0.25s ease;
    }

    .submenu li a:hover {
        color: var(--nav-primary) !important;
        background: rgba(99, 102, 241, 0.06) !important;
        transform: translateX(4px);
    }

    .submenu li a:hover::after {
        background: var(--nav-primary);
    }

    .submenu li.active > a {
        color: var(--nav-primary) !important;
        background: rgba(99, 102, 241, 0.08) !important;
        font-weight: 600 !important;
    }

    .submenu li.active > a::after {
        background: var(--nav-primary);
    }

    .submenu a .fa-fw {
        width: 28px !important;
        height: 28px !important;
        font-size: 0.8rem !important;
        margin-right: 10px !important;
    }

    /* ===== Website Button (Mobile - Only in Sidebar) ===== */
    .sidebar-nav .website-button {
        background: linear-gradient(135deg, var(--nav-primary), var(--nav-primary-light)) !important;
        color: white !important;
        border-radius: 12px !important;
        padding: 12px 16px !important;
        font-weight: 600 !important;
        box-shadow: 0 4px 14px rgba(99, 102, 241, 0.35) !important;
        margin-bottom: 8px !important;
    }

    .sidebar-nav .website-button:hover {
        transform: translateY(-2px) !important;
        box-shadow: 0 6px 20px rgba(99, 102, 241, 0.45) !important;
    }

    .sidebar-nav .website-button .fa-fw,
    .sidebar-nav .website-button i {
        background: transparent !important;
        color: white !important;
        box-shadow: none !important;
    }

    .sidebar-nav .arrow-animate {
        animation: arrowBounce 1.5s infinite;
    }

    @keyframes arrowBounce {
        0%, 100% { transform: translateX(0); }
        50% { transform: translateX(4px); }
    }

    /* ===== Responsive Design ===== */
    @media (min-width: 768px) {
        .sidebar {
            width: 280px;
        }

        .sidebar-nav {
            width: auto !important;
            display: block !important;
        }
    }

    @media (max-width: 767px) {
        .sidebar {
            width: 0;
        }

        .sidebar-header {
            display: none !important;
        }

        .sidebar-nav {
            min-width: 300px;
            max-width: 300px;
            background: linear-gradient(180deg, var(--nav-bg-card) 0%, #fafbfc 100%);
            z-index: 1000;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: none !important;
            box-shadow: var(--nav-shadow-lg);
        }

        .sidebar-nav.show-sidebar {
            display: block !important;
            position: fixed;
            top: 70px;
            left: 0;
            height: calc(100vh - 70px);
            z-index: 999;
            transform: translateX(0);
            overflow-y: auto;
        }

        .side-nav-item {
            margin-top: 10px;
        }
    }

    @media (max-width: 992px) {
        .main-content {
            margin-left: 0;
        }
    }

    /* ===== RTL Support ===== */
    body[dir="rtl"] .sidebar {
        left: auto;
        right: 0;
        border-left: 1px solid var(--nav-border);
        border-right: none;
    }

    body[dir="rtl"] .sidebar::before {
        background: linear-gradient(-135deg, rgba(99, 102, 241, 0.03) 0%, rgba(129, 140, 248, 0.02) 100%);
    }

    body[dir="rtl"] .nav-category {
        font-size: 0.8125rem;
    }

    body[dir="rtl"] .nav-category::after {
        background: linear-gradient(-90deg, var(--nav-border) 0%, transparent 100%);
    }

    body[dir="rtl"] .has-submenu .text-rtl-menu {
        font-size: 1rem !important;
    }

    body[dir="rtl"] .sidebar-nav ul li a {
        transform-origin: right center;
    }

    body[dir="rtl"] .sidebar-nav ul li a:hover {
        transform: translateX(-4px);
    }

    body[dir="rtl"] .sidebar-nav ul li a::before {
        left: auto;
        right: 0;
        border-radius: 0 12px 12px 0;
    }

    body[dir="rtl"] .sidebar-nav .fa-fw {
        margin-right: 0;
        margin-left: 12px;
    }

    body[dir="rtl"] .has-submenu .indicator {
        margin-right: auto;
        margin-left: 0;
        transform: rotate(180deg);
    }

    body[dir="rtl"] .has-submenu.open .indicator {
        transform: rotate(90deg);
    }

    body[dir="rtl"] .badge-nav {
        margin-left: 0;
        margin-right: auto;
    }

    body[dir="rtl"] .submenu {
        padding-left: 0;
        padding-right: 24px;
        margin-left: 0;
        margin-right: 18px;
        border-left: none;
        border-right: 2px solid var(--nav-border);
    }

    body[dir="rtl"] .submenu li a::after {
        left: auto;
        right: -24px;
    }

    body[dir="rtl"] .submenu li a:hover {
        transform: translateX(-4px);
    }

    body[dir="rtl"] .submenu a .fa-fw {
        margin-right: 0 !important;
        margin-left: 10px !important;
    }

    body[dir="rtl"] .sidebar-nav ul li.active > a::before {
        left: auto;
        right: 0;
    }

    /* ===== Disabled Menu State ===== */
    .sidebar.sidebar-disabled .side-nav-item,
    .sidebar.sidebar-disabled .has-submenu {
        opacity: 0.5;
        pointer-events: none;
        filter: grayscale(100%);
    }

    /* Exception for allowed items */
    .sidebar.sidebar-disabled .side-nav-item.allowed-menu-item,
    .sidebar.sidebar-disabled .has-submenu.allowed-menu-item {
        opacity: 1;
        pointer-events: auto;
        filter: none;
    }
</style>
<?php
$user_type = session()->get('user_type');

$student_approved = true;
$school_approved = true;
$trial_expired = false;
$school = null;

if ($user_type == 'admin') {
    $current_school_id = school_id();
    $school = db()->table('schools')->where(['id' => $current_school_id])->get()->getRowArray();
    
    // Si status = 0, l'école n'est pas approuvée
    if (!$school || (int)$school['status'] === 0) {
        $school_approved = false;
    }
} elseif ($user_type == 'student') {
    $current_school_id = school_id();
    $student_row = db()->table('students')
        ->where('user_id', session()->get('user_id'))
        ->where('school_id', $current_school_id)
        ->get()
        ->getRowArray();

    // Si pas de ligne ou status = 0, l'étudiant n'est pas approuvé
    if (!$student_row || (int)$student_row['status'] === 0) {
        $student_approved = false;
    }
}

// Déterminer si le menu doit être grisé
$is_disabled = (!$school_approved || !$student_approved || $trial_expired);

if ($user_type == 'admin' && community_billing_enabled()) {
    // Calcul de l’état de la période d’essai
    if ($school) {
        $now       = time();
        $is_trial  = isset($school['is_trial']) ? (int)$school['is_trial'] : 0;
        $is_paid   = isset($school['is_paid']) ? (int)$school['is_paid'] : 0;
        $trial_end = isset($school['trial_end']) ? (int)$school['trial_end'] : 0;

        if ($is_trial === 1 && $is_paid === 0 && $trial_end > 0 && $now > $trial_end) {
            $trial_expired = true;
        }
    }
}
?>
<aside class="sidebar <?php echo ($is_disabled) ? 'sidebar-disabled' : ''; ?>" id="sidebar">
    <div class="sidebar-header">
        <a href="<?php echo route('profile'); ?>">
            <?php $user_id_nav = session()->get('user_id'); ?>
            <?php $user_details = $user_id_nav ? $this->user_model->get_user_details($user_id_nav) : null; ?>
            <img src="<?php echo $user_id_nav ? $this->user_model->get_user_image($user_id_nav) : base_url('uploads/users/placeholder.jpg'); ?>" alt="user-image" class="avatar">
            <h3><?php echo isset($user_details['name']) ? $user_details['name'] : 'User'; ?></h3>
            <span class="user-role"><?php echo get_phrase(session()->get('user_type') ?? 'user'); ?></span>
        </a>
    </div>

    <nav class="sidebar-nav">
        <li class="side-nav-item d-block d-md-none">
            <a href="<?php echo site_url('home/communities'); ?>" class="side-nav-link website-button">
                <?php echo get_phrase('Discover_our_communities'); ?>
                <i class="mdi mdi-arrow-right ms-2 arrow-animate"></i>
            </a>
        </li>

        <ul style="padding-left: 0rem;">
            <?php
            $user_type_access = session()->get('user_type') . '_access';
            $main_menus = db()->table('menus')
                ->where('parent', 0)
                ->where('status', 1)
                ->where($user_type_access, 1)
                ->orderBy('category_order ASC, sort_order ASC')
                ->get()
                ->getResultArray();


            // 🔹 Regroupement par catégorie
            $menus_by_category = [];
            foreach ($main_menus as $menu) {
                $cat = !empty($menu['category']) ? $menu['category'] : 'General';
                $menus_by_category[$cat][] = $menu;
            }

            // 🔹 Affichage par catégorie
            foreach ($menus_by_category as $category_name => $menus_group): ?>
                <li class="nav-category"><?php echo get_phrase($category_name); ?></li>

                <?php foreach ($menus_group as $main_menu):
                    $check_menus = db()->table('menus')
                        ->where('parent', $main_menu['id'])
                        ->where('status', 1)
                        ->where($user_type_access, 1)
                        ->orderBy('sort_order', 'asc')
                        ->get()
                        ->getResultArray();
                    $has_submenus = count($check_menus) > 0;

                    $main_route = '#';
                    $has_direct_route = false;

                    if ($main_menu['unique_identifier'] === 'academic') {
                        $main_route = $appPrefix . '/calendar';
                        $has_direct_route = true;
                    } elseif ($main_menu['unique_identifier'] == 'exam') {
                        $main_route = $appPrefix . '/exam';
                        $has_direct_route = true;
                    } elseif ($main_menu['unique_identifier'] == 'billing_entities') {
                        $main_route = $appPrefix . '/billing_entities';
                        $has_direct_route = true;
                    } elseif ($main_menu['unique_identifier'] == 'all_courses') {
                        $main_route = 'addons' . '/courses';
                        $has_direct_route = true;
                    }  elseif ($main_menu['unique_identifier'] == 'admin_fee_manager') {
                        $main_route = $appPrefix . '/invoice';
                        $has_direct_route = true;
                    } elseif ($main_menu['unique_identifier'] == 'student_fee_manager') {
                        $main_route = $appPrefix . '/invoice';
                        $has_direct_route = true;
                    } elseif ($main_menu['unique_identifier'] == 'central') {
                        $main_route = $appPrefix . '/wall';
                        $has_direct_route = true;
                    } elseif ($main_menu['unique_identifier'] == 'event-calender') {
                        $main_route = $appPrefix . '/event_calendar';
                        $has_direct_route = true;
                    } elseif ($main_menu['unique_identifier'] == 'chat'){
                        $main_route = 'app/chat';
                        $has_direct_route = true;
                    } elseif (!$has_submenus) {
                        $main_route = $main_menu['is_addon']
                            ? 'addons/' . $main_menu['route_name']
                            : $appPrefix . '/' . $main_menu['route_name'];
                        $has_direct_route = true;
                    }

                    $extra_li_class = '';
                    if ($is_disabled) {
                        // Check if this menu item should be allowed
                        if ($main_menu['unique_identifier'] == 'student_fee_manager' || 
                            $main_menu['unique_identifier'] == 'admin_fee_manager' ||
                            $main_route == $appPrefix . '/invoice') {
                            $extra_li_class = 'allowed-menu-item';
                        }
                    }
                ?>

                    <li class="has-submenu <?php echo $extra_li_class; ?>" data-menu-identifier="<?php echo $main_menu['unique_identifier']; ?>">
                        <?php if ($has_submenus): ?>
                            <?php if ($has_direct_route): ?>
                                <a href="javascript:void(0);"
                                    class="toggle-submenu"
                                    onclick="window.location.href='<?php echo site_url($main_route); ?>'">
                                <?php else: ?>
                                    <a href="javascript:void(0);" class="toggle-submenu">
                                    <?php endif; ?>
                                    <i class="<?php echo $main_menu['icon']; ?> fa-fw"></i>
                                    <span class="text-rtl-menu"><?php echo get_phrase($main_menu['displayed_name']); ?></span>
                                    <?php if ($main_menu['unique_identifier'] == 'exam' && $pending_exams_count > 0): ?>
                                        <span class="badge-nav" style="margin: 5px;"><?php echo $pending_exams_count; ?></span>
                                    <?php endif; ?>
                                    <i class="fas fa-chevron-right indicator"></i>
                                    </a>

                                    <ul class="submenu">
                                        <?php foreach ($check_menus as $menu):
                                            $check_sub_menus = db()->table('menus')
                                                ->where('parent', $menu['id'])
                                                ->where('status', 1)
                                                ->where($user_type_access, 1)
                                                ->get()
                                                ->getResultArray();

                                            if (count($check_sub_menus) > 0): ?>
                                                <li class="has-submenu">
                                                    <a href="javascript:void(0)" class="toggle-submenu">
                                                        <i class="<?php echo $menu['icon']; ?> fa-fw"></i>
                                                        <span class="text-rtl-menu"><?php echo get_phrase($menu['displayed_name']); ?></span>
                                                        <i class="fas fa-chevron-right indicator"></i>
                                                    </a>
                                                    <ul class="submenu">
                                                        <?php foreach ($check_sub_menus as $sub_menu): ?>
                                                            <?php
                                                            $route = $menu['is_addon']
                                                                ? 'addons/' . $sub_menu['route_name']
                                                                : $appPrefix . '/' . $sub_menu['route_name'];
                                                            ?>
                                                            <li>
                                                                <a href="<?php echo site_url($route); ?>">
                                                                    <i class="<?php echo $sub_menu['icon']; ?> fa-fw"></i>
                                                                    <span class="text-rtl-menu"><?php echo get_phrase($sub_menu['displayed_name']); ?></span>
                                                                </a>
                                                            </li>
                                                        <?php endforeach; ?>
                                                    </ul>
                                                </li>
                                            <?php else:
                                                $route = $menu['is_addon']
                                                    ? 'addons/' . $menu['route_name']
                                                    : $appPrefix . '/' . $menu['route_name'];
                                            ?>
                                                <li>
                                                    <a href="<?php echo site_url($route); ?>">
                                                        <i class="<?php echo $menu['icon']; ?> fa-fw"></i>
                                                        <span class="text-rtl-menu"><?php echo get_phrase($menu['displayed_name']); ?></span>
                                                    </a>
                                                </li>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php else: ?>
                                    <a href="<?php echo site_url($main_route); ?>">
                                        <i class="<?php echo $main_menu['icon']; ?> fa-fw"></i>
                                        <span class="text-rtl-menu"><?php echo get_phrase($main_menu['displayed_name']); ?></span>

        <?php if ($main_menu['unique_identifier'] == 'chat'): ?>
            <span id="chat-notification-bubble" class="chat-notification-dot" style="display: none;"></span>
        <?php endif; ?>

        <?php if ($main_menu['unique_identifier'] == 'online_admission' && $pending_students > 0): ?>
                                            <span class="badge-nav"><?php echo $pending_students; ?></span>
                                        <?php endif; ?>

                                        <?php if ($main_menu['unique_identifier'] == 'online_admission_school' && $pending_schools > 0): ?>
                                            <span class="badge-nav"><?php echo $pending_schools; ?></span>
                                        <?php endif; ?>
                                    </a>
                                <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </ul>
    </nav>
</aside>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const currentUrl = window.location.href;
        const currentPath = window.location.pathname;

        // 🔹 Fonction utilitaire RTL
        function getIndicatorTransform(open) {
            const isRtl = document.body.dir === 'rtl' || document.documentElement.dir === 'rtl';
            if (open) {
                return 'rotate(90deg)'; // ↓ bas quand ouvert (toujours)
            }
            return isRtl ? 'rotate(180deg)' : 'rotate(0deg)'; // ← gauche en RTL, → droite en LTR
        }

        function normalizeUrl(url) {
            try {
                const parsed = new URL(url, window.location.origin);
                return parsed.pathname.replace(/\/$/, '');
            } catch (e) {
                return url.replace(/\/$/, '');
            }
        }

        const normalizedCurrentPath = normalizeUrl(currentPath);

        // Reset
        document.querySelectorAll('.has-submenu').forEach(li => {
            li.classList.remove('open', 'active');
        });
        document.querySelectorAll('.sidebar-nav li').forEach(li => {
            li.classList.remove('active');
        });

        let foundActive = false;

        // Active les liens directs
        document.querySelectorAll('.sidebar-nav a[href]').forEach(link => {
            const href = link.getAttribute('href');
            if (!href || href === '#' || href.startsWith('javascript:')) return;

            const normalizedHref = normalizeUrl(href);
            if (normalizedHref === normalizedCurrentPath) {
                const li = link.closest('li');
                if (li) li.classList.add('active');

                let parent = li?.closest('.has-submenu');
                while (parent) {
                    parent.classList.add('open', 'active');
                    const indicator = parent.querySelector('.indicator');
                    if (indicator) {
                        indicator.style.transform = getIndicatorTransform(true);
                    }
                    parent = parent.parentElement?.closest('.has-submenu');
                }
                foundActive = true;
            }
        });

        // Active les liens onclick
        if (!foundActive) {
            document.querySelectorAll('.has-submenu > a.toggle-submenu').forEach(link => {
                const onclick = link.getAttribute('onclick');
                const match = onclick?.match(/window\.location\.href='([^']+)'/);
                if (match && match[1]) {
                    if (normalizeUrl(match[1]) === normalizedCurrentPath) {
                        const li = link.closest('li');
                        if (li) {
                            li.classList.add('open', 'active');
                            const indicator = li.querySelector('.indicator');
                            if (indicator) {
                                indicator.style.transform = getIndicatorTransform(true);
                            }
                        }
                        foundActive = true;
                    }
                }
            });
        }

        // Active par chemin
        if (!foundActive) {
            if (currentPath.includes('/calendar')) openParentMenu('academic');
            if (currentPath.includes('/exam')) openParentMenu('exam');
            if (currentPath.includes('/courses')) openParentMenu('online_courses');
        }

        function openParentMenu(identifier) {
            const menu = document.querySelector(`.has-submenu[data-menu-identifier="${identifier}"]`);
            if (menu && !menu.classList.contains('open')) {
                menu.classList.add('open', 'active');
                const indicator = menu.querySelector('.indicator');
                if (indicator) {
                    indicator.style.transform = getIndicatorTransform(true);
                }
            }
        }

        // 🔹 ÉVÉNEMENT CLICK - Version RTL corrigée
        document.querySelectorAll('.toggle-submenu').forEach(link => {
            link.addEventListener('click', function(e) {
                if (!this.getAttribute('onclick')?.includes('window.location')) {
                    e.preventDefault();
                }

                const parentLi = this.closest('.has-submenu');
                if (parentLi) {
                    parentLi.classList.toggle('open');
                    const indicator = parentLi.querySelector('.indicator');
                    if (indicator) {
                        indicator.style.transform = getIndicatorTransform(parentLi.classList.contains('open'));
                    }
                }
            });
        });
        <?php if (!$school_approved): ?>
            document.addEventListener('DOMContentLoaded', function() {
                // Réactiver le lien Dashboard
                const dashboardLinks = document.querySelectorAll('a[href*="app/dashboard"], a[href*="dashboard"]');
                dashboardLinks.forEach(link => {
                    link.style.pointerEvents = 'auto';
                    link.style.opacity = '1';
                    link.closest('li')?.style.pointerEvents = 'auto';
                });

                // Réactiver le lien Logout
                const logoutLinks = document.querySelectorAll('a[href*="logout"], a[onclick*="logout"]');
                logoutLinks.forEach(link => {
                    link.style.pointerEvents = 'auto';
                    link.style.opacity = '1';
                    link.closest('li')?.style.pointerEvents = 'auto';
                });

                // Optionnel : ajouter un message au survol
                document.querySelectorAll('.sidebar a').forEach(link => {
                    if (!link.href.includes('dashboard') && !link.href.includes('logout')) {
                        link.title = "<?php echo get_phrase('feature_disabled_until_community_approval'); ?>";
                    }
                });
            });
        <?php endif; ?>
    });
</script>