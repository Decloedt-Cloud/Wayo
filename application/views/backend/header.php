<style>
@media (max-width: 480px) {
    #community-menu {
        position: relative !important;
        top: auto !important;
        left: auto !important;
        width: 100% !important;
    }
    /* Classe pour descendre la card quand le dropdown s’ouvre */
}
</style>

<style>
.menu-section-title {
    padding: 10px 15px 6px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #6c757d;
    display: flex;
    align-items: center;
    gap: 6px;
}

.menu-section-title i {
    font-size: 12px;
}

.menu-section {
    padding-bottom: 4px;
}

.community-count {
    margin-left: auto;
    background: #e9ecef;
    color: #6c757d;
    padding: 2px 8px;
    border-radius: 10px;
    font-size: 10px;
    font-weight: 600;
}

.menu-search {
    padding: 10px 15px;
    position: relative;
}

.search-input {
    width: 100%;
    padding: 8px 35px 8px 12px;
    border: 1px solid #dee2e6;
    border-radius: 6px;
    font-size: 13px;
    outline: none;
    transition: all 0.2s;
}

.search-input:focus {
    border-color: #FF8A3D;
    box-shadow: 0 0 0 3px rgba(255, 138, 61, 0.1);
}

.search-icon {
    position: absolute;
    right: 25px;
    top: 50%;
    transform: translateY(-50%);
    color: #6c757d;
    font-size: 12px;
    pointer-events: none;
}
</style>

<?php
$user_id = $this->session->userdata('user_id');
$active_school_id = $this->session->userdata('active_school_id');
$active_role = $this->session->userdata('role');

$current_name = '';
$current_role = '';

// Vérifier si l'utilisateur appartient vraiment à l'école active
$user_belongs_to_school = false;
if ($active_school_id && $user_id) {
    $user_school_check = $this->db->where('user_id', $user_id)
        ->where('school_id', $active_school_id)
        ->get('user_schools')
        ->row();
    $user_belongs_to_school = !empty($user_school_check);
}

// Récupère le nom de la communauté SEULEMENT si l'utilisateur y appartient
if ($active_school_id && $user_belongs_to_school) {
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
} else {
    // L'utilisateur n'appartient pas à cette école, réinitialiser active_school_id
    if ($active_school_id && !$user_belongs_to_school && strtolower($active_role) === 'student') {
        $this->session->set_userdata('active_school_id', null);
        $active_school_id = null;
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
                        <img src="<?php echo $this->user_model->get_user_image($this->session->userdata('user_id')); ?>" alt="user-image" class="rounded-circle user-style">
                    </span>
                    <span>
                        <span class="account-user-name"><?php echo $user_name; ?></span>
                        <?php 
                            $user_role = strtolower($this->db->get_where('users', array('id' => $user_id))->row('role'));
                            
                            if ($user_role == 'admin'): ?>
                                <span class="account-position"><?php echo get_phrase('admin'); ?></span>
                            <?php elseif ($user_role == 'teacher'): ?>
                                <span class="account-position"><?php echo get_phrase('mentor'); ?></span>
                            <?php elseif ($user_role == 'student'): ?>
                                <span class="account-position"><?php echo get_phrase('member'); ?></span>
                            <?php elseif ($user_role == 'superadmin'): ?>
                                <span class="account-position"><?php echo get_phrase('superadmin'); ?></span>
                            <?php endif; 
                        ?>
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
            <a id="hamburger" class="button-menu-mobile disable-btn">
                <div class="lines">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </a>


        <div class="app-search d-flex align-items-center flex-wrap gap-3 pt-1 mt-2">
            <!-- Community Switcher -->
            <?php 
            // Vérifier si l'utilisateur a rejoint au moins une communauté APPROUVÉE
            $user_has_community = false;
            if ($user_id) {
                // Récupérer TOUTES les communautés de l'utilisateur
                $user_schools = $this->db->where('user_id', $user_id)
                    ->where('school_id IS NOT NULL', null, false)
                    ->get('user_schools')
                    ->result();
                
                if (!empty($user_schools)) {
                    foreach ($user_schools as $user_school) {
                        $role_in_school = strtolower($user_school->role ?? 'student');
                        
                        if ($role_in_school === 'admin' || $role_in_school === 'teacher') {
                            // Admin ou Teacher - pas besoin de vérifier le status
                            $user_has_community = true;
                            break; // Une communauté valide trouvée, on arrête
                        } else if ($role_in_school === 'student') {
                            // Vérifier si le student existe dans cette communauté (peu importe le status)
                            $student_approved = $this->db->where('user_id', $user_id)
                                ->where('school_id', $user_school->school_id)
                                ->get('students')
                                ->row();
                            if (!empty($student_approved)) {
                                $user_has_community = true;
                                break; // Une communauté valide trouvée, on arrête
                            }
                        }
                    }
                }
            }
            ?>
            <?php if (strtolower($this->session->userdata('role')) !== 'superadmin' && $user_has_community): ?>
                <div class="community-switcher" id="community-switcher">
                    <button class="switcher-trigger" id="community-trigger">
                        <span class="current-community" id="current-community-display">
                            <?php
                            // Utiliser les variables déjà calculées
                            $display_name = !empty($current_name) ? $current_name : get_phrase('select_community');
                            $display_role = !empty($current_role) ? $current_role : '';
                            ?>
                            <?php echo htmlspecialchars($display_name); ?>
                            <?php if (!empty($display_role)): ?>
                            <span class="current-role">
                                <?php
                                $role_lower = strtolower($display_role);
                                if ($role_lower === 'student') {
                                    echo get_phrase("member");
                                } elseif ($role_lower === 'teacher') {
                                    echo get_phrase("Mentor");
                                } else {
                                    echo $display_role;
                                }
                                ?>
                            </span>
                            <?php endif; ?>
                        </span>
                        <i class="fas fa-chevron-down arrow-icon"></i>
                    </button>

                    <div class="switcher-menu" id="community-menu">
                        <div class="menu-list" id="community-list">
                        </div>
                        <hr class="menu-divider">
                        <button class="menu-item action-item create-item" id="open-create-modal-sidebar">
                            <i class="fas fa-plus"></i>
                            <?php echo get_phrase("create_community"); ?>
                        </button>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Bouton Discover -->
            <a href="<?php echo lang_route('communities'); ?>" class="btn btn-outline-dark website-button d-none d-md-inline-block">
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
    
    // Traductions
    const TRANSLATIONS = {
        mentor: '<?php echo get_phrase("Mentor"); ?>',
        admin: '<?php echo get_phrase("Admin"); ?>',
        superadmin: '<?php echo get_phrase("Superadmin"); ?>',
        member: '<?php echo get_phrase("member"); ?>',
        loadingError: '<?php echo get_phrase("loading_error"); ?>',
        serverError: '<?php echo get_phrase("server_error"); ?>',
        accessDenied: '<?php echo get_phrase("access_denied"); ?>',
        jsonError: '<?php echo get_phrase("json_error"); ?>',
        invalidResponse: '<?php echo get_phrase("invalid_server_response"); ?>',
        noCommunity: '<?php echo get_phrase("No_community"); ?>',
        noResult: '<?php echo get_phrase("No_result"); ?>'
    };
    
    // Stocker toutes les communautés pour la recherche
    let allCommunities = [];

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

    // === POSITIONNEMENT CROSS-PLATFORM (FIX iPhone) ===
    function updateMenuPosition() {
        if (!isOpen || !menu.classList.contains('show')) return;

        const rect = trigger.getBoundingClientRect();
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        const scrollLeft = window.pageXOffset || document.documentElement.scrollLeft;

        menu.style.position = 'absolute';
        menu.style.top = `${rect.bottom + scrollTop + 8}px`;
        menu.style.left = `${rect.left + scrollLeft}px`;
        menu.style.zIndex = '99999';
        menu.style.willChange = 'top, left';
        menu.style.webkitTransform = 'translateZ(0)'; // FIX iOS
    }

    // Charger les communautés
    function loadCommunities() {
        $.ajax({
            url: '<?php echo site_url("home/get_user_communities"); ?>',
            type: 'GET',
            success: function(response) {
                let res = typeof response === 'string' ? JSON.parse(response) : response;
                if (res.status === 'success' && res.data.length > 0) {
                    // Stocker toutes les communautés pour la recherche
                    allCommunities = res.data;
                    // Créer la structure initiale avec la barre de recherche
                    createMenuStructure();
                    // Afficher les communautés
                    renderCommunitiesList(allCommunities);
                } else {
                    allCommunities = [];
                    listContainer.innerHTML = '<div class="p-3 text-center text-muted">' + TRANSLATIONS.noCommunity + '</div>';
                }
            },
            error: function() {
                listContainer.innerHTML = '<div class="p-3 text-center text-danger">' + TRANSLATIONS.loadingError + '</div>';
            }
        });
    }

    // Créer la structure du menu avec la barre de recherche (une seule fois)
    function createMenuStructure() {
        listContainer.innerHTML = `
            <div class="menu-search">
                <input type="text" 
                       class="search-input" 
                       placeholder="<?php echo get_phrase("Search_for_a_community"); ?>..." 
                       id="community-search-input"
                       autocomplete="off">
                <i class="fas fa-search search-icon"></i>
            </div>
            <div id="communities-content"></div>
        `;
        
        // Attacher l'événement de recherche
        setupSearchInput();
    }

    // Rendu de la liste des communautés (sans recréer la barre de recherche)
    function renderCommunitiesList(communities, searchTerm = '') {
        const contentContainer = document.getElementById('communities-content');
        if (!contentContainer) return;

        const validRoles = ['admin', 'teacher', 'superadmin', 'student'];
        const filtered = communities.filter(c => validRoles.includes(c.role.toLowerCase()));
        
        // Filtrer par terme de recherche
        const searchFiltered = searchTerm 
            ? filtered.filter(c => c.community_name.toLowerCase().includes(searchTerm.toLowerCase()))
            : filtered;

        let html = '';

        if (searchFiltered.length === 0) {
            html = '<div class="p-3 text-center text-muted">' + 
                   (searchTerm ? TRANSLATIONS.noResult : TRANSLATIONS.noCommunity) + 
                   '</div>';
        } else {
            // Séparer les communautés en deux groupes
            const adminTeacherCommunities = searchFiltered.filter(c => 
                ['admin', 'teacher', 'superadmin'].includes(c.role.toLowerCase())
            );
            const studentCommunities = searchFiltered.filter(c => 
                c.role.toLowerCase() === 'student'
            );

            // Section Admin/Teacher/Mentor
            if (adminTeacherCommunities.length > 0) {
                html += `
                    <div class="menu-section">
                        <div class="menu-section-title">
                            <i class="fas fa-briefcase"></i> <?php echo get_phrase("My_communities"); ?>
                            <span class="community-count">${adminTeacherCommunities.length}</span>
                        </div>
                `;
                
                html += adminTeacherCommunities.map(c => {
                    const roleLower = c.role.toLowerCase();

                    let roleClass = roleLower;
                    if (roleLower === 'teacher') roleClass = 'teacher';
                    if (roleLower === 'admin') roleClass = 'admin';
                    if (roleLower === 'superadmin') roleClass = 'superadmin';

                    let roleLabel = roleLower === 'teacher' ? TRANSLATIONS.mentor :
                                    roleLower === 'admin' ? TRANSLATIONS.admin :
                                    roleLower === 'superadmin' ? TRANSLATIONS.superadmin :
                                    c.role;

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

                html += '</div>';

                // Ajouter un séparateur s'il y a aussi des communautés student
                if (studentCommunities.length > 0) {
                    html += '<hr class="menu-divider">';
                }
            }

            // Section Membre (Student)
            if (studentCommunities.length > 0) {
                html += `
                    <div class="menu-section">
                        <div class="menu-section-title">
                            <i class="fas fa-user"></i> <?php echo get_phrase("Member_communities"); ?>
                            <span class="community-count">${studentCommunities.length}</span>
                        </div>
                `;

            html += studentCommunities.map(c => {
                const roleLower = c.role.toLowerCase();

                let roleClass = 'student';
                let roleLabel = TRANSLATIONS.member;

                // Indiquer si en attente
                // Check explicitly for undefined to handle 0 correctly
                if (typeof c.status !== 'undefined' && c.status != 1) {
                    roleLabel += ' (Pending)';
                    roleClass = 'secondary'; // Changement de couleur
                }

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

                html += '</div>';
            }
        }

        contentContainer.innerHTML = html;
    }

    // Configuration de l'input de recherche
    function setupSearchInput() {
        const searchInput = document.getElementById('community-search-input');
        if (searchInput) {
            searchInput.addEventListener('input', function(e) {
                const searchTerm = e.target.value;
                // Filtrer les communautés sans recréer la barre de recherche
                renderCommunitiesList(allCommunities, searchTerm);
            });
        }
    }

    // Ancienne fonction pour compatibilité (maintenant utilise renderCommunitiesList)
    function renderCommunities(communities, searchTerm = '') {
        renderCommunitiesList(communities, searchTerm);
    }

    // Ouvrir / fermer le dropdown
    trigger.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();

        isOpen = !isOpen;

        if (isOpen && listContainer.children.length === 0) {
            loadCommunities();
        }

        menu.classList.toggle('show', isOpen);
        trigger.parentElement.classList.toggle('open', isOpen);

        updateMenuPosition();

        if (isOpen) {
            window.addEventListener('scroll', closeMenuOnScroll);
        } else {
            window.removeEventListener('scroll', closeMenuOnScroll);
        }
    });

    function closeMenuOnScroll() {
        if (!isOpen) return;
        menu.classList.remove('show');
        trigger.parentElement.classList.remove('open');
        isOpen = false;
        window.removeEventListener('scroll', closeMenuOnScroll);
    }

    // Switch to Member - L'ancien gestionnaire est remplacé par updateSwitchToMemberButton
    // qui est appelé automatiquement lors du chargement des communautés

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
            data: Object.assign({ school_id: schoolId, role: role }, getCsrfData()),
            success: function(response) {
                if (typeof response === 'string') {
                    try { response = JSON.parse(response); } 
                    catch (e) { alert(TRANSLATIONS.jsonError); return; }
                }

                if (response.csrf?.csrfHash) updateCsrfHash(response.csrf.csrfHash);

                if (response.status === 'success') {
                    window.location.replace(response.redirect_url);
                } else {
                    toastr.error(response.message || TRANSLATIONS.accessDenied);
                }
            },
            error: function() { toastr.error(TRANSLATIONS.serverError); }
        });

        menu.classList.remove('show');
        isOpen = false;
    }, true); // Utiliser le mode capture pour gérer les événements dans le contenu dynamique

    // Fermer si clic dehors
    document.addEventListener('click', function(e) {
        const switcher = document.getElementById('community-switcher');
        
        // Fermer le menu principal
        if (!switcher.contains(e.target)) {
            if (isOpen) {
                menu.classList.remove('show');
                trigger.parentElement.classList.remove('open');
                isOpen = false;
            }
        }
    });

    // Modal création communauté
    document.getElementById('open-create-modal-sidebar')?.addEventListener('click', e => {
        e.preventDefault();
        document.getElementById('createCommunityModal').classList.add('show');
    });

});


</script>