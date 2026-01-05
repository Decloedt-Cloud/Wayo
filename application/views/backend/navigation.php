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
    .sidebar {
        width: 300px;
        background-color: #FFFFFF;
        border-right: 1px solid #E2E8F0;
    }

    .sidebar-header {
        padding: 25px 20px 15px;
        text-align: center;
        border-bottom: 1px solid #E2E8F0;
    }

    .sidebar-header a {
        text-decoration: none;
        color: inherit;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
    }

    .avatar {
        width: 60px;
        height: 60px;
        border: 1px solid #E2E8F0;
        border-radius: 50%;
        object-fit: cover;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
    }

    .sidebar-header h3 {
        font-size: 1rem;
        font-weight: 600;
        color: #1E293B;
    }

    @media (min-width: 768px) {
        .sidebar {
            width: 300px;
            background-color: #FFFFFF;
            border-right: 1px solid #E2E8F0;
        }

        .sidebar-nav {
            width: 250px !important;
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
            background: #fff;
            /* Ajustez selon votre thème */
            z-index: 1000;
            /* Assurez-vous que le sidebar est au-dessus du contenu */
            transition: transform 0.3s ease-in-out;
            display: none !important;
        }

        .sidebar-nav.show-sidebar {
            display: block !important;
            position: fixed;
            top: 70px;
            left: 0;
            height: 95%;
            z-index: 999;
            transform: translateX(0);
        }

        .side-nav-item {
            margin-top: 10px;
        }
    }

    .sidebar-nav {
        padding: 16px;
        flex-grow: 1;
        overflow-y: auto;
    }

    .sidebar-nav ul {
        list-style: none;
    }

    .nav-category {
        font-size: 0.7rem;
        color: #64748B;
        font-weight: 700;
        text-transform: uppercase;
        padding: 20px 12px 8px;
        letter-spacing: 0.05em;
    }

    .sidebar-nav ul li a {
        display: flex;
        align-items: center;
        padding: 10px 12px;
        color: #334155;
        text-decoration: none;
        border-radius: 6px;
        margin: 4px 0;
        font-size: 0.9rem;
        font-weight: 600;
        transition: background-color 0.2s ease, color 0.2s ease;
        position: relative;
    }

    .sidebar-nav ul li a:hover {
        background-color: #EEF2FF;
        color: #4F46E5;
    }

    .sidebar-nav ul li.active>a {
        background-color: #EEF2FF;
        color: #4F46E5;
        font-weight: 600;
    }

    .sidebar-nav ul li.active>a::before {
        content: '';
        position: absolute;
        left: 0;
        top: 50%;
        transform: translateY(-50%);
        height: 20px;
        width: 4px;
        background-color: #4F46E5;
        border-radius: 0 4px 4px 0;
    }

    .sidebar-nav .fa-fw {
        width: 20px;
        margin-right: 12px;
        text-align: center;
        font-size: 1.1em;
        color: #64748B;
    }

    .sidebar-nav ul li a:hover .fa-fw,
    .sidebar-nav ul li.active>a .fa-fw {
        color: #4F46E5;
    }

    .badge-nav {
        background-color: #EF4444;
        color: white;
        font-size: 0.7rem;
        padding: 1px 9px 0px 9px;
        border-radius: 10px;
        margin-left: auto;
        font-weight: 700;
    }

    /* Sous-menus */
    .has-submenu .indicator {
        margin-left: auto;
        transition: transform 0.3s ease;
        font-size: 0.7em;
        color: #64748B;
    }

    .has-submenu.open>a .indicator {
        transform: rotate(90deg);
        color: #4F46E5;
    }

    .submenu {
        display: none;
        list-style: none;
        padding-left: 20px;
        margin-left: 10px;
        border-left: 1px solid #E2E8F0;
        margin-top: 4px;
    }

    .submenu li a::before {
        content: '';
        position: absolute;
        left: -20px;
        top: 50%;
        transform: translateY(-50%);
        width: 12px;
        height: 1px;
        background-color: #E2E8F0;
    }

    .has-submenu.open>.submenu {
        display: block;
    }

    .submenu a {
        font-size: 0.88rem !important;
        font-weight: 400 !important;
        color: #64748B !important;
        padding: 8px 12px !important;
    }

    .submenu a:hover {
        color: #4F46E5 !important;
        background-color: #EEF2FF;
    }

    .submenu a .fa-fw {
        font-size: 0.9em !important;
        color: #64748B !important;
    }

    @media (max-width: 992px) {
        .main-content {
            margin-left: 0;
        }
    }

    /* RTL Support */
    body[dir="rtl"] .sidebar {
        left: auto;
        right: 0;
        border-left: 1px solid #E2E8F0;
        border-right: none;
    }

    body[dir="rtl"] .nav-category {
        font-size: 1rem;
    }

    body[dir="rtl"] .has-submenu .text-rtl-menu {
        font-size: 1.1rem !important;
    }

    .has-submenu .indicator {
        transform: rotate(0deg);
    }

    .has-submenu.open .indicator {
        transform: rotate(90deg);
    }

    body[dir="rtl"] .has-submenu .indicator {
        margin-right: auto;
        margin-left: 8px;
        transform: rotate(180deg);
    }

    body[dir="rtl"] .has-submenu.open .indicator {
        transform: rotate(90deg);
    }

    body[dir="rtl"] .has-submenu .fa-fw {
        width: 20px;
        margin-left: 12px;
        text-align: center;
        font-size: 1.1em;
        color: #64748B;
    }

    body[dir="rtl"] .badge-nav {
        background-color: #EF4444;
        color: white;
        font-size: 0.7rem;
        padding: 1px 9px 0px 9px;
        border-radius: 10px;
        margin-left: auto;
        font-weight: 700;
        margin-right: 1px;
    }

    body[dir="rtl"] .submenu {
        display: none;
        list-style: none;
        padding-left: 20px;
        margin-left: 10px;
        border-right: 1px solid #E2E8F0;
        border-left: none;
        margin-top: 4px;
    }

    body[dir="rtl"] .submenu li a::before {
        content: '';
        position: absolute;
        right: -41px;
        top: 50%;
        transform: translateY(-50%);
        width: 12px;
        height: 1px;
        background-color: #E2E8F0;
    }

    body[dir="rtl"] .sidebar-nav ul li.active>a::before {
        content: '';
        position: absolute;
        right: 0;
        top: 50%;
        transform: translateY(-50%);
        height: 20px;
        width: 4px;
        background-color: #4F46E5;
        border-radius: 0 4px 4px 0;
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
<aside class="sidebar" id="sidebar"
    <?php if (!$school_approved || $trial_expired): ?>
    style="opacity: 0.5; pointer-events: none; user-select: none;"
    onclick="event.preventDefault(); return false;"
    <?php endif; ?>>
    <div class="sidebar-header">
        <a href="<?php echo route('profile'); ?>">
            <img src="<?php echo $this->user_model->get_user_image($this->session->userdata('user_id')); ?>" alt="user-image" class="avatar">
            <?php $user_details = $this->user_model->get_user_details($this->session->userdata('user_id')); ?>
            <h3><?php echo $user_details['name']; ?></h3>
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