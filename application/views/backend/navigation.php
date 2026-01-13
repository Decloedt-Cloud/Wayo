<?php
$controller = "";
if ($user_type == 'parent') {
    $controller = 'parents';
} else {
    $controller = $user_type;
}
// Récupérer le nombre total d'examens non passés pour l'étudiant connecté
$user_id = $this->session->userdata('user_id');
$session = active_session();
$this->db->select('exams.id');
$this->db->from('exams');
$this->db->join('enrols', 'enrols.class_id = exams.class_id ', 'left');
$this->db->join('students', 'students.id = enrols.student_id', 'left');
$this->db->where('students.user_id', $user_id);
$this->db->where('enrols.session', $session);
// Exclure les examens déjà soumis
$this->db->where('exams.id NOT IN (SELECT exam_id FROM exam_responses WHERE user_id = ' . $this->db->escape($user_id) . ')', NULL, FALSE);
$total_exams = $this->db->count_all_results();
log_message('debug', 'Total exams not yet taken calculated: ' . $total_exams);
$unread_messages = $this->user_model->get_unread_messages_count($this->session->userdata('user_id'));
$pending_students = $this->db->get_where('students', ['status' => 0, 'school_id' => school_id()])->num_rows();
$pending_schools = $this->db->get_where('schools', ['status' => 0, 'Etat' => 1])->num_rows();
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

    /* ===== Disabled State (pending approval) ===== */
    .sidebar.sidebar-disabled {
        opacity: 0.6;
        pointer-events: none;
        user-select: none;
    }

    .sidebar.sidebar-disabled::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.3);
        z-index: 100;
    }
</style>


<?php
$school_approved  = true;
$allowed_in_menu  = false;
$trial_expired    = false;

if ($this->session->userdata('user_type') == 'admin') {
    $school_id = $this->session->userdata('school_id');
    $school    = $this->db->get_where('schools', ['id' => $school_id])->row_array();

    $school_approved = ($school && (int)$school['status'] === 1);

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

    // Si non approuvé OU essai expiré → on autorise seulement dashboard et logout dans le menu
    $current_method = $this->router->method;
    $allowed_methods_base = ['dashboard', 'logout', 'language'];
    $allowed_methods_trial = array_merge($allowed_methods_base, ['subscription', 'payment']);

    if (!$school_approved) {
        $allowed_in_menu = in_array($current_method, $allowed_methods_base);
    } elseif ($trial_expired) {
        $allowed_in_menu = in_array($current_method, $allowed_methods_trial);
    } else {
        $allowed_in_menu = true;
    }
}
?>
<aside class="sidebar <?php echo (!$school_approved || $trial_expired) ? 'sidebar-disabled' : ''; ?>" id="sidebar">
    <div class="sidebar-header">
        <a href="<?php echo route('profile'); ?>">
            <img src="<?php echo $this->user_model->get_user_image($this->session->userdata('user_id')); ?>" alt="user-image" class="avatar">
            <?php $user_details = $this->user_model->get_user_details($this->session->userdata('user_id')); ?>
            <h3><?php echo $user_details['name']; ?></h3>
            <span class="user-role"><?php echo get_phrase($this->session->userdata('user_type')); ?></span>
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
            // 🔹 Récupération de tous les menus parents avec leur catégorie
            $this->db->order_by('category_order ASC, sort_order ASC');
            $main_menus = $this->db->get_where('menus', [
                'parent' => 0,
                'status' => 1,
                $this->session->userdata('user_type') . '_access' => 1
            ])->result_array();


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
                    $this->db->order_by('sort_order', 'asc');
                    $check_menus = $this->db->get_where('menus', [
                        'parent' => $main_menu['id'],
                        'status' => 1,
                        $this->session->userdata('user_type') . '_access' => 1
                    ]);
                    $has_submenus = $check_menus->num_rows() > 0;

                    $main_route = '#';
                    $has_direct_route = false;

                    if ($main_menu['unique_identifier'] === 'academic') {
                        $main_route = $controller . '/calendar';
                        $has_direct_route = true;
                    } elseif ($main_menu['unique_identifier'] == 'exam') {
                        $main_route = $controller . '/exam';
                        $has_direct_route = true;
                    } elseif ($main_menu['unique_identifier'] == 'billing_entities') {
                        $main_route = $controller . '/billing_entities';
                        $has_direct_route = true;
                    } elseif ($main_menu['unique_identifier'] == 'all_courses') {
                        $main_route = 'addons' . '/courses';
                        $has_direct_route = true;
                    }  elseif ($main_menu['unique_identifier'] == 'admin_fee_manager') {
                        $main_route = $controller . '/invoice';
                        $has_direct_route = true;
                    } elseif ($main_menu['unique_identifier'] == 'student_fee_manager') {
                        $main_route = $controller . '/invoice';
                        $has_direct_route = true;
                    } elseif ($main_menu['unique_identifier'] == 'central') {
                        $main_route = $controller . '/wall';
                        $has_direct_route = true;
                    } elseif ($main_menu['unique_identifier'] == 'event-calender') {
                        $main_route = $controller . '/event_calendar';
                        $has_direct_route = true;
                    } elseif (!$has_submenus) {
                        $main_route = $main_menu['is_addon']
                            ? 'addons/' . $main_menu['route_name']
                            : $controller . '/' . $main_menu['route_name'];
                        $has_direct_route = true;
                    }
                ?>

                    <li class="has-submenu" data-menu-identifier="<?php echo $main_menu['unique_identifier']; ?>">
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
                                    <?php if ($main_menu['unique_identifier'] == 'exam' && $total_exams > 0): ?>
                                        <span class="badge-nav" style="margin: 5px;"><?php echo $total_exams; ?></span>
                                    <?php endif; ?>
                                    <i class="fas fa-chevron-right indicator"></i>
                                    </a>

                                    <ul class="submenu">
                                        <?php foreach ($check_menus->result_array() as $menu):
                                            $check_sub_menus = $this->db->get_where('menus', [
                                                'parent' => $menu['id'],
                                                'status' => 1,
                                                $this->session->userdata('user_type') . '_access' => 1
                                            ]);

                                            if ($check_sub_menus->num_rows() > 0): ?>
                                                <li class="has-submenu">
                                                    <a href="javascript:void(0)" class="toggle-submenu">
                                                        <i class="<?php echo $menu['icon']; ?> fa-fw"></i>
                                                        <span class="text-rtl-menu"><?php echo get_phrase($menu['displayed_name']); ?></span>
                                                        <i class="fas fa-chevron-right indicator"></i>
                                                    </a>
                                                    <ul class="submenu">
                                                        <?php foreach ($check_sub_menus->result_array() as $sub_menu): ?>
                                                            <?php
                                                            $route = $menu['is_addon']
                                                                ? 'addons/' . $sub_menu['route_name']
                                                                : $controller . '/' . $sub_menu['route_name'];
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
                                                    : $controller . '/' . $menu['route_name'];
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
                                            <span class="badge-nav"><?= $unread_messages > 0 ? $unread_messages : '0' ?></span>
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
                const dashboardLinks = document.querySelectorAll('a[href*="admin/dashboard"], a[href*="dashboard"]');
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