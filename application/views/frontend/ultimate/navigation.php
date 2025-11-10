<!-- <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet"> -->
<?php
$logo_light = $this->settings_model->get_logo_light();
$system_name = get_frontend_settings('website_title');
?>

<style>
   
 /* Header Styles */
        @media (min-width: 991px) {
            #openLoginBtnM {
                display: none !important;
            }
        .container-customize {
            padding-left: 12rem !important;
            padding-right: 12rem !important;
        }
        }
        .site-header {
            position: sticky;
            top: 0;
            z-index: 1040;
            backdrop-filter: blur(8px);
            background: rgba(255, 255, 255, 0.88);
            border-bottom: 1px solid var(--line);
        }

        .navbar {
            padding: 0.55rem 0;
            min-height: calc(var(--logo-h-desktop) + 14px);
        }

        /* Logo */
        .logo-img {
            height: var(--logo-h-desktop);
            width: auto;
        }

        /* Nav Links */
        .navbar-nav .nav-link {
            padding: 0.55rem 1rem;
            border-radius: 999px;
            font-weight: 900;
            color: var(--text);
            transition: background 0.2s;
        }

        .navbar-nav .nav-link:hover {
            background: #f7f7f7;
        }

        .navbar-nav .nav-link.active {
            background: #fff6ed;
            color: var(--orange);
            box-shadow: inset 0 0 0 1px #ffd7b7;
        }
        .navbar-user {
            list-style: none;
            padding-left: 0; /* retire aussi le décalage gauche */
            margin: 0;  
        }

         .user-section {
            display: flex;
            align-items: center;
            /* gap: 8px; */
            }

        /* Language Button */
        .btn-lang {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: .75rem 1.15rem;
            border-radius: 12px;
            border: 1px solid var(--line);
            background: #fff;
            font-weight: 800;
            color: var(--text);
        }

        .btn-lang:hover {
            background: #f7f7f7;
        }

        /* Custom Buttons */
        .btn-custom {
            padding: 0.75rem 1.15rem;
            border-radius: 14px;
            font-weight: 900;
            transition: transform 0.18s, filter 0.18s, box-shadow 0.18s;
        }

        .btn-custom:hover {
            transform: translateY(-1px);
        }

        .btn-ghost {
            background: #fff;
            border: 1px solid var(--line);
            color: var(--text);
        }

        .btn-ghost:hover {
            background: #f7f7f7;
            color: var(--text);
        }

        .btn-accent {
            background: linear-gradient(135deg, var(--orange), var(--orange-2));
            color: #fff;
            border: none;
            box-shadow: 0 14px 28px rgba(244, 122, 31, 0.22);
        }

        .btn-accent:hover {
            background: linear-gradient(135deg, var(--orange), var(--orange-2));
            color: #fff;
        }

        /* Mobile Offcanvas */
        /* Forcer le offcanvas-top à prendre toute la hauteur en mobile */
        .offcanvas-top {
            height: 100vh !important; /* pleine hauteur */
            max-height: 100vh !important;
            overflow-y: auto; /* scroll si besoin */
        }

        .offcanvas-backdrop.show {
            opacity: 0.3; /* ou 0 si tu veux le supprimer totalement */
        }
        .offcanvas {
            background: #fff;
        }

        .offcanvas .nav-link {
            padding: 12px;
            border-radius: 10px;
            font-weight: 800;
            color: var(--text);
        }

        .offcanvas .nav-link:hover {
            background: #f7f7f7;
        }

        .offcanvas-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-top: 16px;
            padding-top: 16px;
            border-top: 1px solid var(--line);
        }

        /* Responsive */
        @media (max-width: 991.98px) {
            .navbar {
                min-height: calc(var(--logo-h-tablet) + 14px);
            }
            .logo-img {
                height: var(--logo-h-tablet);
            }
            .offcanvas .nav-link{
                background:#f7f7f7;
            }
        }

        @media (max-width: 575.98px) {
            .navbar {
                min-height: calc(var(--logo-h-mobile) + 14px);
            }
            .logo-img {
                height: var(--logo-h-mobile);
            }
        }

        /* Navbar Toggler Custom */
        .navbar-toggler {
            border: none;
            padding: 0;
            width: 24px;
            height: 18px;
            position: relative;
        }

        .navbar-toggler:focus {
            box-shadow: none;
        }

        .navbar-toggler-icon {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            width: 100%;
            height: 100%;
        }

        .navbar-toggler-icon span {
            display: block;
            height: 3px;
            background: #cfcfcf;
            border-radius: 2px;
        }
    </style>
</head>
<body>
<!-- ========== HEADER ========== -->
    <header class="site-header" <?php echo (get_user_language() === 'arabic') ? 'dir="rtl"' : 'dir="ltr"'; ?>>
        <nav class="navbar navbar-expand-lg">
            <div class="container">
                <!-- Logo -->
                <a class="navbar-brand" href="<?php echo base_url('home'); ?>" aria-label="Wayo Academy">
                    <img class="logo-img" src="https://i.postimg.cc/W1GGVmqG/logo-icone-trans.png" alt="Wayo">
                </a>
                 <!-- Mobile Toggle -->
                <button class="navbar-toggler ms-auto" type="button" data-bs-toggle="offcanvas" data-bs-target="#navbarOffcanvas" aria-controls="navbarOffcanvas" aria-label="Toggle navigation">
                    <i class="fas fa-bars"></i>
                </button>

                 <?php if ($this->session->userdata('user_id')) { ?>
                        <li class="nav-item navbar-user d-lg-none" style="margin:0 2px; list-style: none;">
                            <a href="<?php echo route('dashboard'); ?>" target="" class="btn btn-login btn-ghost login-toggle w-100 mb-2" style="cursor:pointer !important;">
                                <?php echo get_phrase('community_app'); ?>
                            </a>
                        </li>
                    <?php } else { ?>
                        <li class="nav-item navbar-user" style="list-style: none;">
                            <a id="openLoginBtnM" class="btn btn-login btn-ghost btn-custom w-100 mb-2 login-toggle" style="cursor:pointer !important; text-align:center;">
                                <?php echo get_phrase('Login'); ?>
                            </a>
                            <?php include 'components/navigation-components/login_register_component.php'; ?>
                        </li>
                    <?php } ?>

               

                <!-- Desktop Navigation -->
                <div class="collapse navbar-collapse" id="navbarNav"<?php echo (get_user_language() === 'arabic') ? 'dir="rtl"' : 'dir="ltr"'; ?>> 
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item" style="margin:0 4px">
                            <a class="nav-link <?php if ($page_name === 'home') echo 'active'; ?>" href="<?php echo site_url('home'); ?>"><?php echo get_phrase('Home'); ?></a>
                        </li>
                        <li class="nav-item" style="margin:0 4px">
                            <a class="nav-link <?php if ($page_name === 'communities') echo 'active'; ?>" href="<?php echo site_url('home/communities'); ?>"><?php echo get_phrase('Our_Communities'); ?></a>
                        </li>
                        <li class="nav-item" style="margin:0 4px">
                            <a class="nav-link <?php if ($page_name === 'tutorial') echo 'active'; ?>" href="<?php echo site_url('home/tutorial'); ?>"><?php echo get_phrase('How_it_works'); ?></a>
                        </li>
                    </ul>

                    <div class="d-flex align-items-center gap-2">
                    <?php if ($this->session->userdata('user_type') == 'superadmin' || $this->session->userdata('user_type') == 'admin' || $this->session->userdata('user_type') == 'teacher'
                    || $this->session->userdata('user_type') == 'student'): ?>
                        <li class="dropdown notification-list topbar-dropdown d-none d-lg-block language-selector">
                            <a class="nav-link dropdown-toggle arrow-none" data-bs-toggle="dropdown" href="" role="button" aria-haspopup="false" aria-expanded="false" onclick="getLanguageList()">
                                <i class="mdi mdi-earth me-1"></i> <!-- ton icône globe -->
                                        <button class="btn btn-lang" id="langToggle" aria-label="Language">
                                            <span>🌐</span>
                                            <span class="lang-code"><?php echo ucfirst(get_user_language()); ?></span>
                                        </button>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end dropdown-menu-animated dropdown-lg">
                                <div class="">
                                    <h5 class="m-0 dropdown-item-title">
                                        <?php echo get_phrase('language'); ?>
                                    </h5>
                                </div>
                                <div class="slimscroll" id="language-list" style="min-height: 150px;">
                                    <!-- Language items will be populated here -->
                                </div>
                            </div>
                        </li>
                    <?php else: ?>
                        <!-- Language selector for guests -->
                        <li class="dropdown notification-list topbar-dropdown d-none d-lg-block language-selector">
                            <a class="nav-link dropdown-toggle arrow-none" data-bs-toggle="dropdown" href="" role="button" aria-haspopup="false" aria-expanded="false" onclick="getGuestLanguageList()">
                                <i class="mdi mdi-earth me-1"></i> <!-- ton icône globe -->
                                        <button class="btn btn-lang" id="langToggle" aria-label="Language"  style="cursor:pointer !important;">
                                            <span>🌐</span>
                                            <span class="lang-code"><?php echo ucfirst(get_user_language()); ?></span>
                                        </button>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end dropdown-menu-animated dropdown-lg">
                                <div class="">
                                    <h5 class="m-0 dropdown-item-title">
                                        <?php echo get_phrase('language'); ?>
                                    </h5>
                                </div>
                                <div class="slimscroll" id="guest-language-list" style="min-height: 150px;">
                                    <!-- Language items will be populated here -->
                                </div>
                            </div>
                        </li>
                    <?php endif; ?>

                    <?php if ($this->session->userdata('user_id')) { ?>
                        
                        <li class="nav-item navbar-user" style="margin:0 2px ; list-style:none">
                        <a  href="<?php echo route('dashboard'); ?>" target="" class="btn btn-login btn-ghost login-toggle" style="cursor:pointer !important;"> <?php echo get_phrase('community_app'); ?> </a>
                        </li>
                        <li class="nav-item navbar-user-profile" style="margin:0 2px; list-style:none;">
                        <div class="user-section">
                            <span class="text-capitalize align-content-center"><?php echo $this->session->user_name; ?></span>
                            <img src="<?php echo $this->user_model->get_user_image($this->session->userdata('user_id')); ?>" alt="user-image" class="rounded-circle nav-user-img">
                        </div>
                        <?php include 'components/navigation-components/user_loggedin_component.php'; ?>
                        </li>
                    <?php } else { ?>
                        <li class="nav-item navbar-user" style="
                                                        list-style: none;
                                                    ">
                        <a id="openLoginBtn" class="btn btn-login btn-ghost login-toggle" style="cursor:pointer !important;"><?php echo get_phrase('Login'); ?> </a>
                        <?php include 'components/navigation-components/login_register_component.php'; ?>
                        </li>
                    
                        <a class="btn btn-accent btn-custom btn-create" href="<?php echo site_url('admission/online_admission'); ?>">
                            <?php echo get_phrase('Create_Community'); ?>
                        </a>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </nav>
        <!-- Mobile Offcanvas -->
        <div class="offcanvas offcanvas-top" tabindex="-1" id="navbarOffcanvas" aria-labelledby="navbarOffcanvasLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="navbarOffcanvasLabel"><?php echo get_phrase('Menu'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link <?php if ($page_name === 'home') echo 'active'; ?>" href="<?php echo site_url('home'); ?>">
                            <?php echo get_phrase('Home'); ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php if ($page_name === 'communities') echo 'active'; ?>" href="<?php echo site_url('home/communities'); ?>">
                            <?php echo get_phrase('Our_Communities'); ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php if ($page_name === 'tutorial') echo 'active'; ?>" href="<?php echo site_url('home/tutorial'); ?>">
                            <?php echo get_phrase('How_it_works'); ?>
                        </a>
                    </li>
                </ul>
 
                <div class="offcanvas-actions mt-3">
                    <!-- Language Selector for Mobile -->
                    <?php if ($this->session->userdata('user_type') == 'superadmin' || $this->session->userdata('user_type') == 'admin' || $this->session->userdata('user_type') == 'teacher' || $this->session->userdata('user_type') == 'student'): ?>
                        <li class="dropdown notification-list topbar-dropdown language-selector mb-3" style="list-style: none; width:100%">
                            <a class="nav-link dropdown-toggle arrow-none" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false" onclick="getLanguageList()"
                            style="width:100%">
                                <button class="btn  w-100 d-flex align-items-center justify-content-center gap-2" id="langToggleMobile" aria-label="Language" style="cursor:pointer !important;">
                                    <span>🌐</span>
                                    <span class="lang-code"><?php echo ucfirst(get_user_language()); ?></span>
                                </button>
                            </a>
                            <div class="dropdown-menu dropdown-menu-animated w-100">
                                <div class="px-3 py-2">
                                    <h5 class="m-0 dropdown-item-title">
                                        <?php echo get_phrase('language'); ?>
                                    </h5>
                                </div>
                                <div class="slimscroll" id="language-list-mobile" style="min-height: 150px; max-height: 300px; overflow-y: auto;">
                                    <!-- Language items will be populated here -->
                                </div>
                            </div>
                        </li>
                    <?php else: ?>
                        <!-- Language selector for guests (mobile) -->
                        <li class="dropdown notification-list topbar-dropdown language-selector mb-3" style="list-style: none; width:100%">
                            <a class="nav-link dropdown-toggle arrow-none" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false" onclick="getGuestLanguageList()"
                            style="width:100%";>
                                <button class="btn  w-100 d-flex align-items-center justify-content-center gap-2" id="langToggleMobileGuest" aria-label="Language" style="cursor:pointer !important;">
                                    <span>🌐</span>
                                    <span class="lang-code"><?php echo ucfirst(get_user_language()); ?></span>
                                </button>
                            </a>
                            <div class="dropdown-menu dropdown-menu-animated w-100">
                                <div class="px-3 py-2">
                                    <h5 class="m-0 dropdown-item-title">
                                        <?php echo get_phrase('language'); ?>
                                    </h5>
                                </div>
                                <div class="slimscroll" id="guest-language-list-mobile" style="min-height: 150px; max-height: 300px; overflow-y: auto;">
                                    <!-- Language items will be populated here -->
                                </div>
                            </div>
                        </li>
                    <?php endif; ?>
                        
                   <?php if ($this->session->userdata('user_id')){  ?>
                        <li class="nav-item navbar-user-profile mb-3" style="margin:0 2px; list-style:none;">
                            <div class="user-section p-2 border rounded">
                                <div class="d-flex align-items-center gap-2">
                                    <img src="<?php echo $this->user_model->get_user_image($this->session->userdata('user_id')); ?>" 
                                        alt="user-image" class="rounded-circle nav-user-img" style="width: 40px; height: 40px;">
                                    <span class="text-capitalize align-content-center">
                                        <?php echo $this->session->user_name; ?>
                                    </span>
                                </div>
                                <?php include 'components/navigation-components/user_loggedin_component.php'; ?>
                            </div>
                        </li>
                    <?php } else { ?>
                        <a class="btn btn-accent btn-custom w-100" href="<?php echo site_url('admission/online_admission'); ?>">
                        <?php echo get_phrase('Create_Community'); ?>
                        </a>
                    <?php } ?>
                </div>
            </div>
        </div>
    </header>

<!-- ========== END HEADER ========== -->

   <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script> -->

<script>
  
function getLanguageList() {
    $.ajax({
        url: "<?php echo route('language/dropdown'); ?>",
        success: function(response){
            // Remplir à la fois desktop et mobile
            $('#language-list').html(response);
            $('#language-list-mobile').html(response);
        }
    });
}

function getGuestLanguageList() {
    $.ajax({
        url: "<?php echo site_url('home/dropdown_guest'); ?>",
        success: function(response){
            // Remplir à la fois desktop et mobile
            $('#guest-language-list').html(response);
            $('#guest-language-list-mobile').html(response);
        }
    });
}

function setGuestLanguage(lang) {
    $.post("<?php echo site_url('home/set_guest_language'); ?>", {
        language: lang,
        <?php echo $this->security->get_csrf_token_name(); ?>: '<?php echo $this->security->get_csrf_hash(); ?>'
    }, function() {
        location.reload();
    });
}

</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
    const toggler = document.querySelector('.navbar-toggler');
    const collapse = document.querySelector('.navbar-collapse');

    toggler?.addEventListener('click', function () {
        collapse.classList.toggle('active');
    });
});
</script>


