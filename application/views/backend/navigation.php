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

?>
<style>
    body[dir="rtl"] .side-nav-forth-level li a {
        padding: 8px 10px 8px 15px !important;
    }

    body[dir="rtl"] .side-nav .side-nav-link {
        padding: 15px 0px !important;
    }

    body[dir="rtl"] .menu-arrow {
        left: 5px;
        right: auto;
        transform: rotate(180deg);
        -webkit-transform: rotate(180deg);
        float: left !important;
        margin-right: 5px;
    }

    body[dir="rtl"] .side-nav-title {
        font-size: 1.2rem !important;

    }

    body[dir="rtl"] .side-nav-link {
        font-size: 1.1rem !important;
        font-weight: bold !important;
    }

    body[dir="rtl"] .side-nav-second-level {
        font-weight: bold !important;
        padding-right: 0 !important;
        margin-right: 35px !important;
    }

    body[dir="ltr"] .side-nav .menu-arrow,
    body[dir="ltr"] .side-nav-link .menu-arrow {
        transform: rotate(0deg) !important;
        -webkit-transform: rotate(180deg) !important;
    }

    body[dir="rtl"] .side-nav-second-level>li>a {
        font-size: 1rem !important;
        padding-right: 0 !important;
        padding-left: 40px !important;
        text-align: right !important;
        margin-right: 0 !important;
        margin-left: 0 !important;
        display: block;
    }

    body[dir="rtl"] .badge.float-end {
        float: left !important;
        margin-left: 10px;
        margin-right: 0;
    }

    @media (max-width: 992px) {
        body[dir="rtl"] .side-nav-title {
            font-size: 1.3rem !important;
        }

        body[dir="rtl"] .side-nav-link {
            font-size: 1.2rem !important;
            font-weight: bold !important;
        }

        body[dir="rtl"] .side-nav-second-level>li>a {
            font-size: 1.1rem !important;
            font-weight: bold !important;
        }
    }

    /* Styles généraux pour le sidebar */
    .leftside-menu {
        min-width: 280px;
        max-width: 280px;
        background: #fff;
        /* Ajustez selon votre thème */
        z-index: 1000;
        /* Assurez-vous que le sidebar est au-dessus du contenu */
        transition: transform 0.3s ease-in-out;
        /* Transition fluide */
    }

    /* Par défaut, le sidebar est visible sur les grands écrans */
    @media (min-width: 768px) {
        .leftside-menu {
            display: block !important;
        }
    }

    @media (max-width: 767px) {
        .leftside-menu {
            min-width: 280px;
            max-width: 280px;
            background: #fff;/* Ajustez selon votre thème */
            z-index: 1000;/* Assurez-vous que le sidebar est au-dessus du contenu */
            transition: transform 0.3s ease-in-out;
            display: none !important;
        }

        .leftside-menu.show-sidebar {
            display: block !important;
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            z-index: 999;
            transform: translateX(0);
        }

        .content-page,.container-fluid {
            width: 100% !important;
            margin-left: 0 !important;
        }

    }
</style>
<!-- ========== Left Sidebar Start ========== -->
<div class="leftside-menu leftside-menu-detached" style="min-width: 280px; max-width: 280px;">
    <div class="leftbar-user">
        <a href="<?php echo route('profile'); ?>">
            <img src="<?php echo $this->user_model->get_user_image($this->session->userdata('user_id')); ?>" alt="user-image" height="42" class="rounded-circle shadow-sm">
            <?php
            $user_details = $this->user_model->get_user_details($this->session->userdata('user_id'));
            ?>
            <span class="leftbar-user-name"><?php echo $user_details['name']; ?></span>
        </a>
    </div>
    <!--- Sidemenu -->
    <ul class="side-nav">
        <li class="side-nav-title side-nav-item py-2"><?php echo get_phrase('navigation'); ?></li>
        <li class="side-nav-item">
            <a href="<?php echo site_url($controller . '/dashboard'); ?>" class="side-nav-link py-2">
                <i class="dripicons-meter"></i>
                <span><?php echo get_phrase('dashboard'); ?></span>
            </a>
        </li>

        <?php
        $this->db->order_by('sort_order', 'asc');
        $main_menus = $this->db->get_where('menus', [
            'parent' => 0,
            'status' => 1,
            $this->session->userdata('user_type') . '_access' => 1
        ])->result_array();

        foreach ($main_menus as $main_menu):
            log_message('debug', 'Processing menu: ' . $main_menu['unique_identifier']);
        ?>
            <li class="side-nav-item">
                <?php
                // Sous-menus du menu principal
                $this->db->order_by('sort_order', 'asc');
                $check_menus = $this->db->get_where('menus', [
                    'parent' => $main_menu['id'],
                    'status' => 1,
                    $this->session->userdata('user_type') . '_access' => 1
                ]);

                if ($check_menus->num_rows() > 0):
                    // Cas particulier : "academic" redirige vers "calendar"
                    if ($main_menu['unique_identifier'] == 'academic'):
                        $route = $controller . '/calendar';
                ?><a data-bs-toggle="collapse"
                            href="#<?php echo $main_menu['unique_identifier']; ?>"
                            aria-expanded="false"
                            aria-controls="<?php echo $main_menu['unique_identifier']; ?>"
                            class="side-nav-link py-2"
                            onclick="window.location='<?php echo site_url($route); ?>'">
                            <i class="<?php echo $main_menu['icon']; ?>"></i>
                            <span><?php echo get_phrase($main_menu['displayed_name']); ?></span>
                            <span class="menu-arrow"></span>
                        </a>
                    <?php elseif ($main_menu['unique_identifier'] == 'exam'):
                        $route = $controller . '/exam'; ?>
                        <a data-bs-toggle="collapse"
                            href="#<?php echo $main_menu['unique_identifier']; ?>"
                            aria-expanded="false"
                            aria-controls="<?php echo $main_menu['unique_identifier']; ?>"
                            class="side-nav-link py-2"
                            onclick="window.location='<?php echo site_url($route); ?>'">
                            <i class="<?php echo $main_menu['icon']; ?>"></i>
                            <span><?php echo get_phrase($main_menu['displayed_name']); ?></span>
                            <span class="menu-arrow"></span>
                        </a>
                    <?php else: ?>
                        <a data-bs-toggle="collapse"
                            href="#<?php echo $main_menu['unique_identifier']; ?>"
                            aria-expanded="false"
                            aria-controls="<?php echo $main_menu['unique_identifier']; ?>"
                            class="side-nav-link py-2">
                            <i class="<?php echo $main_menu['icon']; ?>"></i>
                            <span><?php echo get_phrase($main_menu['displayed_name']); ?></span>

                            <?php if ($main_menu['unique_identifier'] == 'exam_parent' && $total_exams > 0): ?>
                                <span class="badge bg-danger ms-1"><?php echo $total_exams; ?></span>
                            <?php endif; ?>

                            <span class="menu-arrow"></span>
                        </a>
                    <?php endif; ?>

                    <div class="collapse" id="<?php echo $main_menu['unique_identifier']; ?>">
                        <ul class="side-nav-second-level">
                            <?php
                            $menus = $check_menus->result_array();
                            foreach ($menus as $menu):
                                log_message('debug', 'Processing submenu: ' . $menu['unique_identifier']);

                                $this->db->order_by('sort_order', 'asc');
                                $check_sub_menus = $this->db->get_where('menus', [
                                    'parent' => $menu['id'],
                                    'status' => 1,
                                    $this->session->userdata('user_type') . '_access' => 1
                                ]);

                                if ($check_sub_menus->num_rows() > 0):
                            ?>
                                    <li class="side-nav-item">
                                        <a data-bs-toggle="collapse"
                                            href="#<?php echo $menu['unique_identifier']; ?>"
                                            aria-expanded="false"
                                            aria-controls="<?php echo $menu['unique_identifier']; ?>">
                                            <?php echo get_phrase($menu['displayed_name']); ?>
                                            <span class="menu-arrow"></span>
                                        </a>

                                        <div class="collapse" id="<?php echo $menu['unique_identifier']; ?>">
                                            <ul class="side-nav-third-level">
                                                <?php
                                                $sub_menus = $check_sub_menus->result_array();
                                                foreach ($sub_menus as $sub_menu):
                                                    $route = $menu['is_addon']
                                                        ? 'addons/' . $sub_menu['route_name']
                                                        : $controller . '/' . $sub_menu['route_name'];
                                                ?>
                                                    <li>
                                                        <a href="<?php echo site_url($route); ?>">
                                                            <?php echo get_phrase($sub_menu['displayed_name']); ?>
                                                        </a>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                    </li>
                                <?php else: ?>
                                    <li>
                                        <?php
                                        $route = $menu['is_addon']
                                            ? 'addons/' . $menu['route_name']
                                            : $controller . '/' . $menu['route_name'];
                                        ?>
                                        <a href="<?php echo site_url($route); ?>">
                                            <?php echo get_phrase($menu['displayed_name']); ?>
                                            <?php if ($menu['unique_identifier'] == 'exam' && $total_exams > 0): ?>
                                                <span class="badge bg-danger float-end" style="padding: 0.4em .4em;"><?php echo $total_exams; ?></span>
                                            <?php endif; ?>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                <?php else:

                    if ($main_menu['is_addon']) {
                        $route = 'addons/' . $main_menu['route_name'];
                    } else {
                        $route = ($main_menu['unique_identifier'] == 'online_courses')
                            ? 'addons/' . $main_menu['route_name']
                            : $controller . '/' . $main_menu['route_name'];
                    }
                ?>
                    <a href="<?php echo site_url($route); ?>" class="side-nav-link">
                        <i class="<?php echo $main_menu['icon']; ?>"></i>
                        <span><?php echo get_phrase($main_menu['displayed_name']); ?></span>

                        <?php if ($main_menu['unique_identifier'] == 'online_admission'): ?>
                            <span class="badge bg-danger float-end"><?php echo $this->db->get_where('students', array('status' => 0, 'school_id' => school_id()))->num_rows(); ?></span>
                        <?php endif; ?>

                        <?php if ($main_menu['unique_identifier'] == 'online_admission_school'): ?>
                            <span class="badge bg-danger float-end"><?php echo $this->db->get_where('schools', array('status' => 0, 'Etat' => 1))->num_rows(); ?></span>
                        <?php endif; ?>

                        <?php if ($main_menu['unique_identifier'] == 'exam' && $total_exams > 0): ?>
                            <span class="badge bg-primary float-end"><?php echo $total_exams; ?></span>
                        <?php endif; ?>

                        <?php if ($main_menu['unique_identifier'] == 'chat'): ?>
                            <span class="badge bg-danger float-end" id="chat-badge">
                                <?= $unread_messages > 0 ? $unread_messages : '0' ?>
                            </span>
                        <?php endif; ?>
                    </a>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>

        <!-- Bouton mobile uniquement -->
        <li class="side-nav-item d-block d-md-none">
            <a href="<?php echo site_url('home/communities'); ?>" class="side-nav-link website-button">
                <span class="dot"></span>
                <?php echo get_phrase('Discover_our_communities'); ?>
                <i class="mdi mdi-arrow-right ms-2 arrow-animate"></i>
            </a>
        </li>
    </ul>
    <!-- End Sidebar -->

    <div class="clearfix"></div>
    <!-- Sidebar -left -->
</div>
<!-- Left Sidebar End -->
<script>
    $(document).ready(function() {
        var currentUrl = window.location.pathname;

        if (currentUrl.includes('/calendar')) {
            var parentMenu = $('.side-nav-item').find('a[href="#academic"]');
            var subMenu = $('#academic');
            parentMenu.attr('aria-expanded', 'true');
            subMenu.addClass('show');
        }
        // Cas 2 : Si on est sur /exam → ouvrir le menu "exam"
        if (currentUrl.includes('/exam')) {
            var parentMenu = $('.side-nav-item').find('a[href="#exam"]');
            var subMenu = $('#exam');
            parentMenu.attr('aria-expanded', 'true');
            subMenu.addClass('show');
        }
    });
</script>