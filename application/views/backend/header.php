<?php
$user_id = $this->session->userdata('user_id');
$active_school_id = $this->session->userdata('active_school_id');
$active_role = $this->session->userdata('role');

$current_name = '';
$current_role = '';

// Récupère le nom de la communauté
if ($active_school_id) {
    $school = $this->db->select('name as community_name')->from('schools')->where('id', $active_school_id)->get()->row();
    if ($school) {
        $current_name = $school->community_name;
    }
    // Récupère le rôle dans user_schools
    $user_school = $this->db->select('role')->from('user_schools')->where('user_id', $user_id)->where('school_id', $active_school_id)->get()->row();
    if ($user_school && $user_school->role) {
        $current_role = ucfirst($user_school->role);
    } elseif ($active_role) {
        $current_role = ucfirst($active_role);
    }
}
?>
<?php include 'application/views/create_community_modal.php'; ?>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/backend/css/custom/navbar.css">
<!-- Topbar Start -->
<div class="navbar-custom topnav-navbar navbar-color">
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
                                    echo get_phrase('mentor');
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
        <a href="<?php echo site_url($this->session->userdata('role') . '/dashboard'); ?>" class="topnav-logo" style="min-width: unset;">
            <span class="topnav-logo-lg">
                <img src="<?php echo $this->settings_model->get_logo_light(); ?>" alt="" height="57">
            </span>
            <span class="topnav-logo-sm">
                <img src="<?php echo $this->settings_model->get_logo_light('small'); ?>" alt="" height="40">
            </span>
        </a>


        <div class="app-search d-flex align-items-center flex-wrap gap-3 pt-1 mt-2">
            <!-- Community Switcher -->
            <?php if (strtolower($this->session->userdata('role')) !== 'superadmin'): ?>
                <div class="community-switcher" id="community-switcher">
                    <button class="switcher-trigger" id="community-trigger">
                        <span class="current-community" id="current-community-display">
                            <?php
                            $role = strtolower($this->session->userdata('role'));
                            $school_id = $this->session->userdata('active_school_id');
                            ?>
                            <?php if ($role === 'student'): ?>
                                <span class="current-role"><?php echo get_phrase("member"); ?></span>
                            <?php else: ?>
                                <?php
                                $school_name = 'Communauté inconnue';
                                if ($active_school_id) {
                                    $school = $this->db->select('name')->get_where('schools', ['id' => $active_school_id])->row();
                                    if ($school) $school_name = $school->name;
                                }
                                ?>
                                <?php echo htmlspecialchars($school_name); ?>
                                <span class="current-role">
                                    <?php echo $role === 'teacher' ? 'Mentor' : ucfirst($role); ?>
                                </span>
                            <?php endif; ?>
                        </span>
                        <i class="fas fa-chevron-down arrow-icon"></i>
                    </button>

                    <div class="switcher-menu" id="community-menu">
                        <div class="menu-list" id="community-list">
                        </div>
                        <hr class="menu-divider">
                        <button class="menu-item action-item" id="switch-member-btn">
                            <i class="fas fa-user-circle"></i>
                            <?php echo get_phrase("switch_to_member_account"); ?>
                        </button>
                        <button class="menu-item action-item create-item" id="open-create-modal-sidebar">
                            <i class="fas fa-plus"></i>
                            <?php echo get_phrase("create_community"); ?>
                        </button>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Bouton Discover -->
            <a href="<?php echo site_url('home/communities'); ?>" class="btn btn-outline-dark website-button d-none d-md-inline-block">
                <span class="dot"></span>
                <?php echo get_phrase("Discover_our_communities"); ?>
                <i class="mdi mdi-arrow-right ms-2 arrow-animate" aria-hidden="true"></i>
            </a>
        </div>


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
<script>
    window.checkCommunityNameUrl = '<?php echo site_url("home/check_community_name_exists"); ?>';
    window.csrfTokenName = '<?php echo $this->security->get_csrf_token_name(); ?>';
    window.csrfTokenValue = '<?php echo $this->security->get_csrf_hash(); ?>';
</script>
<script src="<?php echo base_url('assets/backend/js/create_community_modal.js'); ?>"></script>
<script type="text/javascript">
    let CURRENT_USER_ROLE = '<?php echo strtolower($this->session->userdata('role')); ?>';
    const ACTIVE_SCHOOL_ID = '<?php echo $active_school_id; ?>';

    let csrfName = '<?= $this->security->get_csrf_token_name(); ?>';
    let csrfHash = '<?= $this->security->get_csrf_hash(); ?>';

    $.ajaxSetup({
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        beforeSend: function(xhr, settings) {
            // Ne touche PAS aux FormData (fichiers, multipart)
            if (settings.data instanceof FormData) {
                return;
            }

            const csrfInput = document.getElementById('csrf_token');
            if (csrfInput && settings.type === 'POST') {
                settings.data += (settings.data ? '&' : '') + csrfInput.name + '=' + encodeURIComponent(csrfInput.value);
            }
        }
    });

    function getCsrfData() {
        const csrfInput = document.getElementById('csrf_token');
        return csrfInput ? {
            [csrfInput.name]: csrfInput.value
        } : {};
    }

    // Mise à jour du hash après chaque requête réussie (important si CI le régénère)
    function updateCsrfHash(newHash) {
        if (newHash) {
            csrfHash = newHash;
            const csrfInput = document.getElementById('csrf_token');
            if (csrfInput) csrfInput.value = newHash;
        }
    }


    function updateCsrfToken() {
        const csrfInput = document.getElementById('csrf_token');
        if (csrfInput) {
            // Recharge le token depuis le serveur si possible, ou laisse tel quel
            $.ajax({
                url: '<?php echo site_url("home/refresh_csrf"); ?>',
                type: 'GET',
                success: function(data) {
                    if (data.csrfHash) {
                        csrfInput.value = data.csrfHash;
                    }
                }
            });
        }
    }

    function getLanguageList() {
        $.ajax({
            url: "<?php echo route('language/dropdown'); ?>",
            success: function(response) {
                $('#language-list, #language-list-mobile').html(response);
            }
        });
    }


    document.addEventListener('DOMContentLoaded', function() {
        // === HAMBURGER MENU ===
        const hamburger = document.getElementById('hamburger');
        const sidebar = document.querySelector('.sidebar-nav');

        if (hamburger && sidebar) {
            hamburger.addEventListener('click', function() {
                this.classList.toggle('open');
                sidebar.classList.toggle('show-sidebar');
            });
        }

        const trigger = document.getElementById('community-trigger');
        const menu = document.getElementById('community-menu');
        const listContainer = document.getElementById('community-list');

        if (!trigger || !menu || !listContainer) return;

        let isOpen = false;

        function updateMenuPosition() {
            if (!isOpen || !menu.classList.contains('show')) return;

            const rect = trigger.getBoundingClientRect();
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            const scrollLeft = window.pageXOffset || document.documentElement.scrollLeft;

            menu.style.top = `${rect.bottom + scrollTop + 8}px`;
            menu.style.left = `${rect.left + scrollLeft}px`;
        }
        // Charger les communautés
        function loadCommunities() {
            $.ajax({
                url: '<?php echo site_url("home/get_user_communities"); ?>',
                type: 'GET',
                success: function(response) {
                    let res = typeof response === 'string' ? JSON.parse(response) : response;
                    if (res.status === 'success' && res.data.length > 0) {
                        renderCommunities(res.data);
                    } else {
                        listContainer.innerHTML = '<div class="p-3 text-center text-muted">Aucune communauté</div>';
                    }
                },
                error: function() {
                    listContainer.innerHTML = '<div class="p-3 text-center text-danger">Erreur de chargement</div>';
                }
            });
        }

        // Rendu HTML
        function renderCommunities(communities) {
            const activeSchoolId = '<?php echo $active_school_id; ?>';
            const activeRole = '<?php echo strtolower($active_role); ?>';

            const validRoles = ['admin', 'teacher', 'superadmin'];
            const filtered = communities.filter(c =>
                validRoles.includes(c.role.toLowerCase())
            );

            if (filtered.length === 0) {
                listContainer.innerHTML = '<div class="p-3 text-center text-muted">Aucune communauté</div>';
                return;
            }

            listContainer.innerHTML = filtered.map(c => {
                const roleLower = c.role.toLowerCase();

                let roleClass = 'teacher';
                if (roleLower === 'admin') roleClass = 'admin';
                else if (roleLower === 'superadmin') roleClass = 'superadmin';

                let roleLabel = '';
                if (roleLower === 'superadmin') roleLabel = 'Superadmin';
                else if (roleLower === 'teacher') roleLabel = 'Mentor';
                else if (roleLower === 'admin') roleLabel = 'Admin';
                else roleLabel = c.role.charAt(0).toUpperCase() + c.role.slice(1).toLowerCase();

                // Utilise le flag is_active du backend
                const isSelected = c.is_active === true;

                return `
            <button class="menu-item community-item ${isSelected ? 'selected' : ''}" 
                    data-school-id="${c.school_id}" 
                    data-role="${c.role}">
                <span>${c.community_name}</span>
                <span class="role-badge ${roleClass}">${roleLabel}</span>
            </button>
        `;
            }).join('');
        }

        // Ouvrir/fermer
        trigger.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            isOpen = !isOpen;

            if (isOpen && listContainer.children.length === 0) {
                loadCommunities();
            }

            // Position initiale
            const rect = trigger.getBoundingClientRect();
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            const scrollLeft = window.pageXOffset || document.documentElement.scrollLeft;

            menu.style.top = `${rect.bottom + scrollTop + 8}px`;
            menu.style.left = `${rect.left + scrollLeft}px`;

            menu.classList.toggle('show', isOpen);
            trigger.parentElement.classList.toggle('open', isOpen);

            // Si on ouvre → on écoute le scroll
            if (isOpen) {
                window.addEventListener('scroll', updateMenuPosition);
            } else {
                window.removeEventListener('scroll', updateMenuPosition);
            }
        });

        window.addEventListener('scroll', function() {
            if (isOpen) {
                menu.classList.remove('show');
                trigger.parentElement.classList.remove('open');
                isOpen = false;
                window.removeEventListener('scroll', updateMenuPosition);
            }
        });

        document.getElementById('switch-member-btn').addEventListener('click', function(e) {
            e.preventDefault();
            $.ajax({
                url: '<?php echo site_url("home/switch_to_member_account"); ?>',
                type: 'POST',
                dataType: 'json',
                data: getCsrfData(), // ← Ajouté
                success: function(response) {
                    if (typeof response === 'string') {
                        try {
                            response = JSON.parse(response);
                        } catch (e) {
                            alert('Réponse invalide du serveur');
                            return;
                        }
                    }

                    // Mise à jour du token si renvoyé
                    if (response.csrf?.csrfHash) {
                        updateCsrfHash(response.csrf.csrfHash);
                    }

                    if (response.status === 'success') {
                        document.getElementById('current-community-display').innerHTML =
                            '<span class="current-role">Member</span>';

                        CURRENT_USER_ROLE = 'student';

                        window.dispatchEvent(new Event('roleSwitched'));
                        updateSwitchToMemberButtonVisibility(); // Masque le bouton

                        window.location.replace(response.redirect_url);
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function(xhr, status, err) {
                    console.error('AJAX Error:', status, err);
                    toastr.error('Error');
                }
            });
        });
        // Clic sur une communauté
        listContainer.addEventListener('click', function(e) {
            const item = e.target.closest('.community-item');
            if (!item) return;

            const schoolId = item.dataset.schoolId;
            const role = item.dataset.role;

            $.ajax({
                url: '<?php echo site_url("home/switch_community_role"); ?>',
                type: 'POST',
                dataType: 'json',
                data: Object.assign({
                    school_id: schoolId,
                    role: role
                }, getCsrfData()), // ← Ajouté
                success: function(response) {
                    if (typeof response === 'string') {
                        try {
                            response = JSON.parse(response);
                        } catch (e) {
                            alert('Erreur JSON');
                            return;
                        }
                    }

                    if (response.csrf?.csrfHash) {
                        updateCsrfHash(response.csrf.csrfHash);
                    }
                    if (response.status === 'success') {
                        const urlParts = response.redirect_url.split('/');
                        const roleLower = urlParts[urlParts.length - 2].toLowerCase(); // plus fiable que index 4

                        // Met à jour la variable globale
                        CURRENT_USER_ROLE = roleLower;

                        const display = document.getElementById('current-community-display');
                        if (roleLower === 'student') {
                            display.innerHTML = '<span class="current-role"><?php echo get_phrase("member"); ?></span>';
                        } else {
                            const name = item.querySelector('span').textContent.trim();
                            const roleLabel = roleLower === 'teacher' ? 'Mentor' :
                                roleLower.charAt(0).toUpperCase() + roleLower.slice(1);
                            display.innerHTML = `${name} <span class="current-role">${roleLabel}</span>`;
                        }

                        updateSwitchToMemberButtonVisibility();

                        window.location.replace(response.redirect_url);
                    } else {
                        toastr.error(response.message || 'Accès refusé');
                    }
                },
                error: function() {
                    toastr.error('Erreur serveur');
                }
            });

            menu.classList.remove('show');
            isOpen = false;
        });

        // Fermer si clic dehors
        document.addEventListener('click', function(e) {
            const switcher = document.getElementById('community-switcher');
            if (!switcher.contains(e.target)) {
                if (isOpen) {
                    menu.classList.remove('show');
                    trigger.parentElement.classList.remove('open');
                    isOpen = false;
                    window.removeEventListener('scroll', updateMenuPosition);
                }
            }
        });
        document.getElementById('open-create-modal-sidebar')?.addEventListener('click', e => {
            e.preventDefault();
            document.getElementById('createCommunityModal').classList.add('show');
        });

        function updateSwitchToMemberButtonVisibility() {
            const switchBtn = document.getElementById('switch-member-btn');
            if (!switchBtn) return;

            const currentRole = CURRENT_USER_ROLE.toLowerCase(); // mis à jour après switch
            const isMember = currentRole === 'student';

            if (isMember) {
                switchBtn.style.display = 'none';
            } else {
                switchBtn.style.display = 'flex'; // ou 'block', selon ton CSS
            }
        }
        updateSwitchToMemberButtonVisibility();
    });
</script>