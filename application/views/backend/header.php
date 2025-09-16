<link rel="stylesheet" href="<?php echo base_url(); ?>assets/backend/css/custom/navbar.css">
<!-- Topbar Start -->
<div class="navbar-custom topnav-navbar navbar-color topnav-navbar-dark">
    <div class="container-fluid">
        <ul class="list-unstyled topbar-menu float-end mb-0">

            <?php if (
                $this->session->userdata('user_type') == 'superadmin' || $this->session->userdata('user_type') == 'admin' || $this->session->userdata('user_type') == 'teacher'
                || $this->session->userdata('user_type') == 'student'
            ): ?>

            <?php endif; ?>
            <li class="dropdown notification-list topbar-dropdown language-selector language-selector-desktop">
                <a class="nav-link dropdown-toggle arrow-none" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false" onclick="getLanguageList()">
                    <button id="btnLang" class="lang-btn" aria-haspopup="true" aria-expanded="false" title="Langue">
                        <!-- Votre icône globe -->
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
                            <circle cx="12" cy="12" r="9.5" fill="none" stroke="#FF8A3D" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M3 12h18M12 2.5c3 3 3 15 0 19M12 2.5c-3 3-3 15 0 19"
                                fill="none" stroke="#FF8A3D" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>

                        <!-- Le badge pour la langue -->
                        <span class="lang-badge">
                            <?php echo strtoupper(substr(get_user_language(), 0, 2)); ?>
                        </span>
                    </button>
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-animated dropdown-lg dropup-desktop">

                    <!-- item-->
                    <div class="dropdown-item noti-title">
                        <h5 class="m-0">
                            <?php echo get_phrase('language'); ?>
                        </h5>
                    </div>

                    <div class="slimscroll" id="language-list" style="min-height: 150px;">

                    </div>
                </div>
            </li>
            <li class="dropdown notification-list profile-menu">
                <a class="nav-link dropdown-toggle nav-user  user-dropdown arrow-none me-0" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false"
                    aria-expanded="false">
                    <span class="account-user-avatar">
                        <img src="<?php echo $this->user_model->get_user_image($this->session->userdata('user_id')); ?>" alt="user-image" class="rounded-circle">
                    </span>
                    <span>
                        <span class="account-user-name"><?php echo $user_name; ?></span>
                        <?php if (strtolower($this->db->get_where('users', array('id' => $user_id))->row('role')) == 'admin'): ?>
                            <span class="account-position"><?php echo get_phrase('school_admin'); ?></span>
                        <?php else: ?>
                            <span class="account-position">

                                <?php
                                if ($this->db->get_where('users', array('id' => $user_id))->row('role') == "teacher")
                                    echo get_phrase('teacher');
                                else
                                    echo ucfirst($this->db->get_where('users', array('id' => $user_id))->row('role')); ?>

                            </span>
                        <?php endif; ?>

                    </span>
                </a>

                <div class="dropdown-menu dropdown-menu-end dropdown-menu-animated topbar-dropdown-menu profile-dropdown dropup-mobile">
                    <!-- item-->
                    <div class=" dropdown-header noti-title">
                        <h6 class="text-overflow m-0"><?php echo get_phrase('welcome'); ?> !</h6>
                    </div>

                    <!-- item-->
                    <a href="<?php echo route('profile'); ?>" class="dropdown-item notify-item">
                        <i class="mdi mdi-account-circle me-1"></i>
                        <span><?php echo get_phrase('my_account'); ?></span>
                    </a>
                    <?php if ($this->session->userdata('user_type') == 'superadmin'): ?>
                        <!-- item-->
                        <a href="<?php echo route('system_settings'); ?>" class="dropdown-item notify-item">
                            <i class="mdi mdi-account-edit me-1"></i>
                            <span><?php echo get_phrase('settings'); ?></span>
                        </a>
                    <?php endif; ?>

                    <?php if ($this->session->userdata('user_type') == 'superadmin' || $this->session->userdata('user_type') == 'admin'): ?>
                        <!-- item-->
                        <a href="mailto:info@wayo.cloud?Subject=Help%20On%20This" target="_blank" class="dropdown-item notify-item">
                            <i class="mdi mdi-lifebuoy me-1"></i>
                            <span><?php echo get_phrase('support'); ?></span>
                        </a>
                    <?php endif; ?>

                    <!-- item-->
                    <a href="<?php echo site_url('login/logout'); ?>" class="dropdown-item notify-item">
                        <i class="mdi mdi-logout me-1"></i>
                        <span><?php echo get_phrase('logout'); ?></span>
                    </a>

                </div>

            </li>

        </ul>
        <!-- LOGO -->
        <a href="<?php echo site_url($this->session->userdata('role')); ?>" class="topnav-logo" style="min-width: unset;">
            <span class="topnav-logo-lg">
                <img src="<?php echo $this->settings_model->get_logo_light(); ?>" alt="" height="57">
            </span>
            <span class="topnav-logo-sm">
                <img src="<?php echo $this->settings_model->get_logo_light('small'); ?>" alt="" height="40">
            </span>
        </a>


        <div class="app-search dropdown pt-1 mt-2">
            <?php
            // Récupérer l'ID de l'école depuis la session
            $school_id = $this->session->userdata('school_id');
            $school_name = '';
            $user_type = $this->session->userdata('user_type');

            // Vérifier si l'utilisateur est un teacher ou un admin
            if ($school_id && in_array($user_type, ['teacher', 'admin'])) {
                $school = $this->db->get_where('schools', array('id' => $school_id))->row();
                $school_name = $school ? htmlspecialchars($school->name) : get_settings('system_name');
            } else {
                $school_name = ''; // Ne rien afficher pour student ou autres rôles
            }
            ?>
            <h4 style="color: #000000ff; float: left;" class="d-none d-md-inline-block system-name"><?php echo $school_name; ?></h4>
            <a href="<?php echo site_url('home/communities'); ?>" target="" class="btn btn-outline-dark website-button ms-2 d-none d-md-inline-block website-button">
                <span class="dot"></span>
                <?php echo get_phrase('Discover_our_communities'); ?>
                <i class="mdi mdi-arrow-right ms-2 arrow-animate" aria-hidden="true"></i>
            </a>

        </div>

        <ul class="list-unstyled topbar-menu mb-0 d-flex align-items-center">
            <li class="dropdown notification-list topbar-dropdown language-selector language-selector-mobile">
                <a class="nav-link dropdown-toggle arrow-none" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false" onclick="getLanguageList()">
                    <button id="btnLang" class="lang-btn" aria-haspopup="true" aria-expanded="false" title="Langue">
                        <!-- Votre icône globe -->
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
                            <circle cx="12" cy="12" r="9.5" fill="none" stroke="#FF8A3D" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M3 12h18M12 2.5c3 3 3 15 0 19M12 2.5c-3 3-3 15 0 19"
                                fill="none" stroke="#FF8A3D" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>


                        <span class="lang-badge">
                            <?php echo strtoupper(substr(get_user_language(), 0, 2)); ?>
                        </span>
                    </button>
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-animated dropdown-lg dropup-mobile1 ">
                    <div class="dropdown-item noti-title">
                        <h5 class="m-0"><?php echo get_phrase('language'); ?></h5>
                    </div>
                    <div class="slimscroll" id="language-list-mobile" style="min-height: 150px;">
                    </div>
                </div>
            </li>
        </ul>


        <a id="hamburger" class="button-menu-mobile disable-btn">
            <div class="lines">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </a>
    </div>
</div>
<!-- end Topbar -->


<script type="text/javascript">
    function getLanguageList() {
        $.ajax({
            url: "<?php echo route('language/dropdown'); ?>",
            success: function(response) {
                $('#language-list, #language-list-mobile').html(response);
            }
        });
    }
    const hamburger = document.getElementById('hamburger');

    hamburger.addEventListener('click', function() {
        this.classList.toggle('open');
        // ici tu peux ajouter l'ouverture/fermeture de ton menu
        // exemple : document.querySelector('.menu').classList.toggle('show');
    });

    
</script>