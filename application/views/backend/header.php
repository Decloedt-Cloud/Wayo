<?php
$user_id = $this->session->userdata('user_id');
$active_school_id = $this->session->userdata('active_school_id');
$active_role = $this->session->userdata('role');

$current_name = '';
$current_role = 'Member';

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
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/backend/css/custom/navbar.css">
<style>
    .navbar-custom,
    .container-fluid {
        overflow: visible !important;
    }

    .community-switcher {
        position: relative;
        margin-right: 12px;
        z-index: 1050 !important;
    }

    #community-switcher.open #community-menu {
        display: block !important;
    }

    .switcher-trigger {
        background-color: transparent;
        border: 1px solid transparent;
        padding: 6px 10px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        gap: 10px;
        cursor: pointer;
        font-size: 1.1em;
        font-weight: bold;
        color: #4A4A4A;
        transition: all 0.2s ease;
    }

    .switcher-trigger:hover {
        background-color: #f5f5f5;
        border-color: #DDDDDD;
    }

    .switcher-trigger .current-role {
        font-weight: normal;
        color: #888888;
    }

    .switcher-trigger .arrow-icon {
        font-size: 0.8em;
        color: #888888;
        transition: transform 0.2s ease-in-out;
    }

    .community-switcher.open .arrow-icon {
        transform: rotate(180deg);
    }

    #community-menu {
        display: none !important;
        position: fixed !important;
        width: 234px !important;
        background: white !important;
        border: 1px solid #ddd !important;
        border-radius: 12px !important;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1) !important;
        z-index: 9999 !important;
        padding: 0 !important;
    }

    #community-menu.show {
        display: block !important;
    }

    .menu-list {
        max-height: 200px;
        overflow-y: auto;
        transition: all 0.3s ease;
    }

    .menu-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: 100%;
        padding: 12px 16px;
        background: none;
        border: none;
        text-align: left;
        cursor: pointer;
        font-size: 14px;
        color: #4A4A4A;
        transition: background-color 0.15s ease;
    }

    .menu-item:hover {
        background-color: #f5f5f5;
    }

    .role-badge {
        padding: 3px 8px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 700;
    }

    .role-badge.superadmin {
        background-color: #EDE9FE !important;
        color: #6B46C1;
        font-weight: 700;
    }

    .role-badge.admin {
        background-color: #E0F2FE !important;
        color: #0EA5E9;
    }

    .role-badge.teacher {
        background-color: #ECFDF5 !important;
        color: #10B981;
    }

    .menu-divider {
        border: none;
        border-top: 1px solid #eeeeee;
        margin: 4px 0;
    }

    .action-item {
        justify-content: flex-start;
        gap: 12px;
        color: #4A4A4A;
    }

    .action-item .fa-user-circle {
        color: #888888;
    }

    .action-item.create-item {
        color: #F06423;
        font-weight: 600;
    }

    .action-item.create-item .fa-plus {
        font-weight: bold;
    }

    .app-search {
        display: flex !important;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
        padding-top: 0.25rem;
        margin-top: 0.5rem;
    }

    .menu-item.selected {
        background-color: #e6f7ff;
        border-left: 4px solid #536de6;
    }

    .modal-saas {
        display: none;
        position: fixed;
        z-index: 2000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.5);

        align-items: center;
        justify-content: center;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.3s ease, visibility 0.3s ease;
    }

    .modal-saas.show {
        display: flex !important;
        opacity: 1;
        visibility: visible;
    }

    .modal-content-saas {
        background-color: #FFFFFF;
        border-radius: 12px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        width: 90%;
        max-width: 600px;
        position: relative;
        overflow: hidden;
        animation: slideIn 0.3s ease-out;
    }

    @keyframes slideIn {
        from {
            transform: translateY(-30px);
            opacity: 0;
        }

        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    .close-button-saas {
        color: #6B7280;
        position: absolute;
        top: 16px;
        right: 20px;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
        transition: color 0.2s ease;
    }

    .close-button-saas:hover {
        color: #1F2937;
    }

    .modal-header-community {
        padding: 24px 32px;
        border-bottom: 1px solid #E5E7EB;
    }

    .modal-header-community h3 {
        margin: 0 0 16px 0;
        font-size: 1.25rem;
        color: #1F2937;
    }

    .stepper-community {
        display: flex !important;
        align-items: center !important;
    }

    .step-community {
        display: flex;
        align-items: center;
        color: #6B7280;
        font-weight: 500;
    }

    .step-community.active {
        color: #F06423;
    }

    .step-number {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background-color: #F3F4F6;
        border: 1px solid #E5E7EB;
        color: #6B7280;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.8rem;
        font-weight: 600;
        margin-right: 8px;
        transition: all 0.2s ease;
    }

    .step-community.active .step-number {
        background-color: #F06423;
        color: white;
        border-color: #F06423;
    }

    .step-label {
        font-size: 0.9rem;
    }

    .step-divider {
        flex: 1;
        height: 2px;
        background-color: #E5E7EB;
        margin: 0 16px;
    }

    .modal-body-community {
        padding: 32px;
        max-height: 65vh;
        /* Ajusté */
        overflow-y: auto;
    }

    .form-group-community {
        margin-bottom: 20px;
    }

    .form-group-community label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #1F2937;
        font-size: 0.875rem;
    }

    .required-star {
        color: #EF4444;
    }

    .form-input-community {
        width: 100%;
        padding: 12px 16px;
        font-size: 0.95rem;
        border: 1px solid #E5E7EB;
        background-color: #FFFFFF;
        border-radius: 6px;
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .form-input-community:focus {
        outline: none;
        border-color: #F06423;
        box-shadow: 0 0 0 3px rgba(240, 100, 35, 0.2);
    }

    textarea.form-input-community {
        resize: vertical;
    }

    .input-with-prefix-saas {
        display: flex;
        align-items: center;
    }

    .input-with-prefix-saas span {
        padding: 12px;
        font-size: 0.95rem;
        background-color: #F3F4F6;
        border: 1px solid #E5E7EB;
        border-right: none;
        border-top-left-radius: 6px;
        border-bottom-left-radius: 6px;
        color: #6B7280;
    }

    .input-with-prefix-saas input {
        border-top-left-radius: 0;
        border-bottom-left-radius: 0;
    }

    .form-text {
        font-size: 0.8rem;
        color: #6B7280;
        margin-top: 4px;
    }

    .media-upload-grid {
        display: grid;
        grid-template-columns: 100px 1fr;
        gap: 16px;
        margin-bottom: 20px;
    }

    .file-upload-saas {
        position: relative;
        border: 2px dashed #E5E7EB;
        border-radius: 6px;
        background-color: #F3F4F6;
        cursor: pointer;
        transition: border-color 0.2s ease;
        overflow: hidden;
    }

    .file-upload-saas:hover {
        border-color: #F06423;
    }

    .file-upload-saas.wide {
        grid-column: 2 / 3;
        grid-row: 1 / 2;
    }

    .file-upload-saas:not(.wide) {
        grid-column: 1 / 2;
        grid-row: 1 / 2;
        height: 100px;
    }

    .upload-placeholder-saas {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        padding: 16px;
        text-align: center;
        color: #6B7280;
    }

    .upload-placeholder-saas i {
        font-size: 1.5rem;
        margin-bottom: 8px;
    }

    .upload-placeholder-saas span {
        font-size: 0.8rem;
        font-weight: 500;
    }

    .file-input-saas {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        opacity: 0;
        cursor: pointer;
        z-index: 3;
    }

    .image-preview-saas {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-size: cover;
        background-position: center;
        z-index: 2;
    }

    .modal-actions {
        padding-top: 24px;
        border-top: 1px solid #E5E7EB;
        margin-top: 32px;
        display: flex;
        justify-content: flex-end;
        /* Aligné à droite par défaut */
        gap: 12px;
    }

    .modal-actions.space-between {
        justify-content: space-between;
    }

    .btn-saas {
        padding: 12px 20px;
        font-size: 0.95rem;
        font-weight: 600;
        border-radius: 6px;
        border: none;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-community {
        background-color: #F06423;
        color: white;
    }

    .btn-community:hover {
        opacity: 0.9;
    }

    .btn-community-2 {
        background-color: #FFFFFF;
        color: #1F2937;
        border: 1px solid #E5E7EB;
    }

    .btn-community-2:hover {
        background-color: #F3F4F6;
    }

    /* --- Étape 2: Abonnement (Design mis à jour) --- */
    .subscription-plan {
        border: 1px solid #E5E7EB;
        border-radius: 8px;
        overflow: hidden;
        background-color: #F9FAFB;
    }

    .plan-header {
        padding: 20px 24px;
        background-color: #FFFFFF;
    }

    .plan-header h4 {
        margin: 0;
        font-size: 1.1rem;
    }

    .plan-price {
        font-size: 1.75rem;
        font-weight: 700;
        color: #1F2937;
        margin-top: 8px;
    }

    .plan-price span {
        font-size: 0.9rem;
        font-weight: 400;
        color: #6B7280;
    }

    .plan-body {
        padding: 24px;
    }

    .plan-body p {
        margin: 0 0 16px 0;
        font-size: 0.9rem;
    }

    /* NOUVEAU: Grille 2 colonnes pour les features */
    .plan-features {
        list-style: none;
        padding: 0;
        margin: 0;
        font-size: 0.9rem;
        display: grid;
        grid-template-columns: 1fr 1fr;
        /* 2 colonnes */
        gap: 10px 16px;
        /* Espace vertical et horizontal */
    }

    .plan-features li {
        display: flex;
        align-items: flex-start;
        gap: 8px;
        margin-bottom: 0;
        /* Géré par le 'gap' */
    }

    .plan-features li i {
        color: #10B981;
        margin-top: 4px;
    }

    /* FIN NOUVEAU */

    .payment-details {
        margin-top: 0;
        /* Géré par le callout */
    }

    /* NOUVEAU: Bloc d'info pour l'essai gratuit */
    .trial-callout {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        background-color: #f0fdf4;
        border: 1px solid #bbf7d0;
        border-radius: 8px;
        padding: 16px;
        margin-top: 24px;
    }

    .trial-callout .fa-calendar-check {
        color: #22c55e;
        font-size: 1.25rem;
        margin-top: 3px;
    }

    .trial-callout .trial-text strong {
        display: block;
        color: #166534;
        font-weight: 600;
        font-size: 0.95rem;
    }

    .trial-callout .trial-text span {
        font-size: 0.875rem;
        color: #15803d;
    }

    .form-input-community.is-valid {
        border-color: #10B981 !important;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.2);
    }
</style>
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
                                if ($school_id) {
                                    $school = $this->db->select('name')->get_where('schools', ['id' => $school_id])->row();
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
                        <button class="menu-item action-item create-item" id="open-create-modal">
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

        <div id="createCommunityModal" class="modal-saas">
            <div class="modal-content-saas">
                <span class="close-button-saas">&times;</span>

                <!-- En-tête avec Stepper -->
                <div class="modal-header-community">
                    <h3><?php echo get_phrase("create_a_new_community"); ?></h3>
                    <div class="stepper-community">
                        <div class="step-community active" data-step="1">
                            <span class="step-number">1</span>
                            <span class="step-label"><?php echo get_phrase("details"); ?></span>
                        </div>
                        <div class="step-divider"></div>
                        <div class="step-community" data-step="2">
                            <span class="step-number">2</span>
                            <span class="step-label"><?php echo get_phrase("subscription"); ?></span>
                        </div>
                    </div>
                </div>

                <!-- FORMULAIRE GLOBAL (englobe les 2 étapes) -->
                <form id="create-community-form"
                    action="<?php echo site_url('home/online_admission_school'); ?>"
                    method="post"
                    enctype="multipart/form-data"
                    novalidate>

                    <input type="hidden" id="csrf_token" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>" />

                    <!-- ÉTAPE 1 -->
                    <div class="modal-body-community" id="modal-step-1">
                        <!-- Tous les champs (i_am, Tax_residence, etc.) -->
                        <div class="form-group-community">
                            <label for="i_am"><?php echo get_phrase("I_am") ?> <span class="required-star">*</span></label>
                            <select id="i_am" name="i_am" class="form-input-community" required data-msg="<?php echo get_phrase("Please select your status") ?>">
                                <option value=""><?php echo get_phrase('select_a_status'); ?></option>
                                <option value="Entreprise"><?php echo get_phrase("Entreprise") ?></option>
                                <option value="Freelancer"><?php echo get_phrase("Freelancer") ?></option>
                                <option value="Autoentrepreneur"><?php echo get_phrase("Autoentrepreneur") ?></option>
                                <option value="Particulier"><?php echo get_phrase("Particulier") ?></option>
                            </select>
                        </div>

                        <div class="form-group-community">
                            <label for="Tax_residence"><?php echo get_phrase("Tax_residence") ?> <span class="required-star">*</span></label>
                            <select id="Tax_residence" name="Tax_residence" class="form-input-community" required data-msg="<?php echo get_phrase("Please select your tax residence") ?>">
                                <option value=""><?php echo get_phrase('select_a_Tax_residence'); ?></option>
                                <option value="MA"><?php echo get_phrase("Morocco") ?></option>
                                <option value="UAE"><?php echo get_phrase("United_Arab_Emirates") ?></option>
                            </select>
                        </div>

                        <div class="form-group-community">
                            <label for="category"><?php echo get_phrase("Category") ?><span class="required-star">*</span></label>
                            <select id="category" name="category" class="form-input-community" required data-msg="<?php echo get_phrase("please_select_a_category"); ?>">
                                <option value=""><?php echo get_phrase('select_a_category'); ?></option>
                                <?php $categories = $this->db->get_where('categories', array())->result_array(); ?>
                                <?php foreach ($categories as $categorie): ?>
                                    <option value="<?php echo $categorie['name']; ?>"><?php echo $categorie['name']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group-community">
                            <label for="community-name"><?php echo get_phrase("community_name") ?> <span class="required-star">*</span></label>
                            <input type="text" id="community-name" name="school_name" class="form-input-community"
                                placeholder="Ex: Les As du Marketing" maxlength="80" required
                                data-msg="<?php echo get_phrase("Please enter your community name") ?>">
                            <small class="form-text"><?php echo get_phrase("Max._80_characters") ?></small>
                        </div>

                        <div class="form-group-community">
                            <label for="community-description">
                                <?php echo get_phrase("description") ?> <span class="required-star">*</span>
                                <span class="info" data-tooltip="<?php echo get_phrase("Goal_in_one_sentence_•_2)_For_whom_•_3)_Benefits_(3–5)_•_4)_Pace/rules._Min._40_characters.") ?>">
                                    <i class="fa-solid fa-circle-info"></i>
                                </span>
                            </label>
                            <textarea id="community-description" name="school_description" class="form-input-community" rows="4"
                                placeholder="Describe your community..." required
                                data-msg="<?php echo get_phrase("Please enter a description (min 40 characters)") ?>"></textarea>
                            <small class="form-text"><?php echo get_phrase("At_least_40_characters.") ?></small>
                        </div>

                        <div class="form-group-community">
                            <label for="street"><?php echo get_phrase("Rue") ?> <span class="required-star">*</span></label>
                            <input type="text" id="street" name="street" class="form-input-community" placeholder="<?php echo get_phrase("Rue") ?>" required>
                        </div>
                        <div class="form-group-community">
                            <label for="number"><?php echo get_phrase("Numéro") ?> <span class="required-star">*</span></label>
                            <input type="text" id="number" name="number" class="form-input-community" placeholder="<?php echo get_phrase("Numéro") ?>" required>
                        </div>
                        <div class="form-group-community">
                            <label for="city"><?php echo get_phrase("Ville") ?> <span class="required-star">*</span></label>
                            <input type="text" id="city" name="city" class="form-input-community" placeholder="<?php echo get_phrase("Ville") ?>" required>
                        </div>
                        <div class="form-group-community">
                            <label for="code_postal"><?php echo get_phrase("code_postal") ?> <span class="required-star">*</span></label>
                            <input type="text" id="code_postal" name="postal_code" class="form-input-community" placeholder="<?php echo get_phrase("code_postal") ?>" required>
                        </div>

                        <div class="form-group-community" id="priceFieldWrapper">
                            <label for="community-price"><?php echo get_phrase("community_price") ?></label>
                            <div class="input-with-prefix-saas">
                                <span id="currencySymbol"><?php echo get_phrase("MAD") ?></span>
                                <input type="text" id="community-price" name="price" class="form-input-community" placeholder="299.00">
                            </div>
                            <small class="form-text"><?php echo get_phrase("leave_blank_for_a_free community.") ?></small>
                        </div>

                        <div id="particulierNotice" class="alert alert-warning" style="display:none; margin-top:10px; padding:10px; font-size:0.85rem; border-radius:6px;">
                            <?php echo get_phrase("As_you_are_a_private_individual_you_are_not_allowed_to_set_a_price_It_is_automatically_set_to_0"); ?>
                        </div>

                        <label class="switch emph">
                            <input type="hidden" name="visibility" value="0">
                            <input id="isPrivate" name="visibility" type="checkbox" value="1" />
                            <span class="switch-emph">
                                <i class="fa-solid fa-lock"></i>
                                <strong><?php echo get_phrase("Private_community") ?></strong>
                                <em><?php echo get_phrase("(access_upon_approval)") ?></em>
                                <span class="info field-label" data-tooltip="<?php echo get_phrase("(A_private_community_means_that_access_is_not_open._Interested_people_will_need_to_send_a_request_for_access._The_administrator_can_accept_or_reject_it.)") ?>">
                                    <i class="fa-solid fa-circle-info"></i>
                                </span>
                            </span>
                        </label>

                        <label><?php echo get_phrase("media") ?></label>
                        <div class="media-upload-grid">
                            <div class="file-upload-saas" id="logo-upload-box">
                                <div class="upload-placeholder-saas"><i class="fas fa-camera"></i><span><?php echo get_phrase("logo_(1:1)") ?></span></div>
                                <div class="image-preview-saas" id="logo-preview"></div>
                                <input type="file" class="file-input-saas" id="logo-upload" name="school_image" accept="image/*">
                            </div>
                            <div class="file-upload-saas wide" id="cover-upload-box">
                                <div class="upload-placeholder-saas"><i class="fas fa-image"></i><span><?php echo get_phrase("cover_(16:9)") ?></span></div>
                                <div class="image-preview-saas" id="cover-preview"></div>
                                <input type="file" class="file-input-saas" id="cover-upload" name="communityCover" accept="image/*">
                            </div>
                        </div>

                        <div class="modal-actions">
                            <button type="button" class="btn-saas btn-community" id="modal-continue-btn">
                                <?php echo get_phrase("Continue") ?>
                            </button>
                        </div>
                    </div>

                    <!-- ÉTAPE 2 -->
                    <div class="modal-body-community" id="modal-step-2" style="display:none;">
                        <div class="subscription-plan">
                            <div class="plan-header">
                                <h4><?php echo get_phrase("creator_plan") ?></h4>
                                <div class="plan-price">790 <?php echo get_phrase("MAD") ?> <span>/ <?php echo get_phrase("month,_no_commitment") ?></span></div>
                            </div>
                            <div class="plan-body">
                                <p><?php echo get_phrase("access_all_the_features_to_publish_your_community:") ?></p>
                                <ul class="plan-features">
                                    <li><i class="fas fa-check-circle"></i> <?php echo get_phrase("online_course_hosting") ?></li>
                                    <li><i class="fas fa-check-circle"></i> <?php echo get_phrase("integrated_video_conferencing") ?></li>
                                    <li><i class="fas fa-check-circle"></i> <?php echo get_phrase("social_network") ?></li>
                                    <li><i class="fas fa-check-circle"></i> <?php echo get_phrase("discussion_space") ?></li>
                                    <li><i class="fas fa-check-circle"></i> <?php echo get_phrase("certification") ?></li>
                                    <li><i class="fas fa-check-circle"></i> <?php echo get_phrase("multi-class") ?></li>
                                    <li><i class="fas fa-check-circle"></i> <?php echo get_phrase("multi-coach_management") ?></li>
                                </ul>
                            </div>
                        </div>

                        <div class="trial-callout">
                            <i class="fas fa-calendar-check"></i>
                            <div class="trial-text">
                                <strong><?php echo get_phrase("enjoy_a_14-day_free_trial.") ?></strong>
                                <span><?php echo get_phrase("no_payment_will_be_required_before_the_end_of_your_trial.") ?></span>
                            </div>
                        </div>

                        <div class="modal-actions space-between">
                            <button type="button" class="btn-saas btn-community-2" id="modal-back-btn"><?php echo get_phrase("back") ?></button>
                            <button type="submit" class="btn-saas btn-community">
                                <?php echo get_phrase("start_the_14_day_trial") ?>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
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


<script type="text/javascript">
    const CURRENT_USER_ROLE = '<?php echo strtolower($this->session->userdata('role')); ?>';
    const ACTIVE_SCHOOL_ID = '<?php echo $active_school_id; ?>';
    let nameCheckInProgress = false;
    let communityNameIsUnique = true;

    const csrfName = '<?= $this->security->get_csrf_token_name(); ?>';
    const csrfHash = '<?= $this->security->get_csrf_hash(); ?>';

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

                // Définir la classe du badge
                let roleClass = 'teacher'; // par défaut
                if (roleLower === 'admin') roleClass = 'admin';
                else if (roleLower === 'superadmin') roleClass = 'superadmin';

                // Libellé du rôle
                let roleLabel = roleLower === 'superadmin' ? 'Superadmin' :
                    roleLower === 'Mentor' ? '' :
                    c.role.charAt(0).toUpperCase() + c.role.slice(1).toLowerCase();

                // Vérifier si c'est la communauté active
                const isSelected = String(c.school_id) === String(activeSchoolId) &&
                    roleLower === activeRole;

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
                        window.location.replace(response.redirect_url);
                    } else {
                        toastr.error(response.message || 'Erreur lors du changement de rôle');
                    }
                },
                error: function(xhr, status, err) {
                    console.error('AJAX Error:', status, err);
                    toastr.error('Erreur serveur');
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
                        const roleLower = urlParts[4].toLowerCase(); // ← INDEX 4, pas 0 !
                        const display = document.getElementById('current-community-display');

                        if (roleLower === 'student') {
                            display.innerHTML = '<span class="current-role">Member</span>';
                        } else {
                            const name = item.querySelector('span').textContent.trim();
                            const roleLabel = roleLower === 'teacher' ? 'Mentor' :
                                roleLower.charAt(0).toUpperCase() + roleLower.slice(1);
                            display.innerHTML = `${name} <span class="current-role">${roleLabel}</span>`;
                        }

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

        const modal = document.getElementById('createCommunityModal');
        const form = document.getElementById('create-community-form');
        const step1 = document.getElementById('modal-step-1');
        const step2 = document.getElementById('modal-step-2');
        const continueBtn = document.getElementById('modal-continue-btn');
        const backBtn = document.getElementById('modal-back-btn');
        const steps = document.querySelectorAll('.step-community');

        const iAmSelect = document.getElementById('i_am');
        const taxSelect = document.getElementById('Tax_residence');
        const priceInput = document.getElementById('community-price');
        const particulierNotice = document.getElementById('particulierNotice');
        const currencySymbol = document.getElementById('currencySymbol');

        // === DEVISE DYNAMIQUE ===
        function updateCurrency() {
            const tax = taxSelect?.value || 'MA';
            currencySymbol.textContent = tax === 'UAE' ? 'د.إ' : 'DH';
            if (priceInput && !priceInput.disabled) {
                priceInput.placeholder = `299.00 ${tax === 'UAE' ? '(AED)' : '(MAD)'}`;
            }
        }

        // === PRIX BLOQUÉ SI PARTICULIER ===
        function togglePriceField() {
            const isParticulier = iAmSelect?.value === 'Particulier';
            if (isParticulier) {
                particulierNotice.style.display = 'block';
                priceInput.value = '0';
                priceInput.disabled = true;
                priceInput.classList.add('bg-light');
            } else {
                particulierNotice.style.display = 'none';
                priceInput.disabled = false;
                priceInput.classList.remove('bg-light');
                if (priceInput.value === '0') priceInput.value = '';
            }
        }

        // === FONCTION DE VALIDATION COMPLÈTE (temps réel) ===
        function validateStep1() {
            const required = [{
                    el: document.getElementById('i_am'),
                    msg: 'Veuillez sélectionner votre statut'
                },
                {
                    el: document.getElementById('Tax_residence'),
                    msg: 'Veuillez sélectionner votre résidence fiscale'
                },
                {
                    el: document.getElementById('category'),
                    msg: 'Veuillez sélectionner une catégorie'
                },
                {
                    el: document.getElementById('community-name'),
                    msg: 'Veuillez entrer le nom de la communauté'
                },
                {
                    el: document.getElementById('community-description'),
                    msg: 'Description trop courte (min. 40 caractères)'
                },
                {
                    el: document.getElementById('street'),
                    msg: 'Rue requise'
                },
                {
                    el: document.getElementById('number'),
                    msg: 'Numéro requis'
                },
                {
                    el: document.getElementById('city'),
                    msg: 'Ville requise'
                },
                {
                    el: document.getElementById('code_postal'),
                    msg: 'Code postal requis'
                }
            ];

            let allValid = true;

            required.forEach(field => {
                const value = field.el.value.trim();
                let valid = true;

                if (field.el.id === 'community-description') {
                    valid = value.length >= 40;
                } else if (field.el.id === 'community-name') {
                    valid = value !== '' && value.length <= 80;
                } else {
                    valid = value !== '';
                }

                if (valid) {
                    field.el.style.borderColor = '#E5E7EB';
                } else {
                    allValid = false;
                }
            });

            return allValid;
        }

        // === MISE À JOUR DU BOUTON CONTINUE ===
        function updateContinueButton() {
            const baseValid = validateStep1();
            const nameUnique = communityNameIsUnique;
            const isValid = baseValid && nameUnique && !nameCheckInProgress;

            continueBtn.disabled = !isValid;
            continueBtn.style.opacity = isValid ? '1' : '0.5';
            continueBtn.style.cursor = isValid ? 'pointer' : 'not-allowed';
        }
        // === VÉRIFICATION DU NOM DE COMMUNAUTÉ EN TEMPS RÉEL ===
        function checkCommunityNameUniqueness() {
            const nameInput = document.getElementById('community-name');
            const name = nameInput.value.trim();

            clearFieldError(nameInput);
            nameInput.style.borderColor = '#E5E7EB';
            nameInput.classList.remove('is-valid');
            communityNameIsUnique = true;

            if (name.length < 3) {
                updateContinueButton();
                return;
            }

            if (nameCheckInProgress) return;
            nameCheckInProgress = true;

            $.ajax({
                url: '<?php echo site_url("home/check_community_name_exists"); ?>',
                type: 'POST',
                data: Object.assign({
                    school_name: name
                }, getCsrfData()), // ← Ajouté
                success: function(response) {
                    nameCheckInProgress = false;
                    let res = typeof response === 'string' ? JSON.parse(response) : response;

                    if (response.csrf?.csrfHash) {
                        updateCsrfHash(response.csrf.csrfHash);
                    }

                    if (res.exists === true) {
                        communityNameIsUnique = false;
                        nameInput.style.borderColor = '#EF4444';
                        showFieldError(nameInput, res.message);
                    } else {
                        communityNameIsUnique = true;
                        nameInput.style.borderColor = '#10B981';
                        nameInput.classList.add('is-valid');
                    }
                    updateContinueButton();
                },
                error: function() {
                    nameCheckInProgress = false;
                    communityNameIsUnique = true;
                    updateContinueButton();
                }
            });
        }

        function showFieldError(input, message) {
            clearFieldError(input);
            const error = document.createElement('small');
            error.className = 'text-danger form-error-text';
            error.style.display = 'block';
            error.style.marginTop = '4px';
            error.style.fontSize = '0.8rem';
            error.textContent = message;
            input.parentNode.appendChild(error);
        }

        function clearFieldError(input) {
            const existing = input.parentNode.querySelector('.form-error-text');
            if (existing) existing.remove();
        }
        // === CHANGEMENT D'ÉTAPE (amélioré avec validation) ===
        function goToStep(step) {
            if (step === 2) {
                if (!validateStep1()) {
                    return;
                }
            }

            step1.style.display = step === 1 ? 'block' : 'none';
            step2.style.display = step === 2 ? 'block' : 'none';

            steps.forEach((s, i) => {
                s.classList.toggle('active', i < step);
            });
        }

        // === ÉCOUTEURS SUR TOUS LES CHAMPS (validation en temps réel) ===
        document.querySelectorAll('#modal-step-1 input, #modal-step-1 select, #modal-step-1 textarea').forEach(el => {
            el.addEventListener('input', updateContinueButton);
            el.addEventListener('change', updateContinueButton);
        });

        // === ÉCOUTEURS BOUTONS ===
        continueBtn?.addEventListener('click', () => goToStep(2));
        backBtn?.addEventListener('click', () => goToStep(1));

        updateContinueButton();

        // === VÉRIFICATION DU NOM DE COMMUNAUTÉ ===
        const communityNameInput = document.getElementById('community-name');
        communityNameInput?.addEventListener('blur', checkCommunityNameUniqueness);
        communityNameInput?.addEventListener('input', function() {
            if (!nameCheckInProgress) {
                communityNameIsUnique = true;
                this.style.borderColor = '#E5E7EB';
                this.classList.remove('is-valid');
                clearFieldError(this);
                updateContinueButton();
            }
        });

        document.getElementById('open-create-modal')?.addEventListener('click', function(e) {
            e.preventDefault();

            // Ferme le dropdown
            menu.classList.remove('show');
            trigger.parentElement.classList.remove('open');
            isOpen = false;
            window.removeEventListener('scroll', updateMenuPosition);

            // Ouvre le modal
            modal.classList.add('show');
            goToStep(1);
        });

        // === APERÇU IMAGE ===
        function setupImagePreview(inputId, previewId, boxId) {
            const input = document.getElementById(inputId);
            const preview = document.getElementById(previewId);
            const placeholder = document.getElementById(boxId).querySelector('.upload-placeholder-saas');

            input?.addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = e => {
                        preview.style.backgroundImage = `url(${e.target.result})`;
                        placeholder.style.display = 'none';
                    };
                    reader.readAsDataURL(file);
                } else {
                    preview.style.backgroundImage = 'none';
                    placeholder.style.display = 'flex';
                }
            });
        }

        setupImagePreview('logo-upload', 'logo-preview', 'logo-upload-box');
        setupImagePreview('cover-upload', 'cover-preview', 'cover-upload-box');

        // === FERMETURE MODAL ===
        document.querySelector('.close-button-saas')?.addEventListener('click', () => {
            modal.classList.remove('show');
        });

        window.addEventListener('click', e => {
            if (e.target === modal) {
                modal.classList.remove('show');
            }
        });

        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                if (!this.checkValidity()) {
                    this.reportValidity();
                    return;
                }

                const formData = new FormData(this);
                const csrfInput = document.getElementById('csrf_token');
                if (csrfInput) {
                    formData.append(csrfInput.name, csrfInput.value);
                }

                const submitBtn = this.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Création...';

                $.ajax({
                    url: this.action,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function(data) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalText;

                        // Mise à jour du token CSRF
                        if (data.csrf?.csrfHash) {
                            const input = document.getElementById('csrf_token');
                            if (input) input.value = data.csrf.csrfHash;
                        }

                        if (data.status === true || data.status === 'success') {
                            toastr.success(data.message || 'Communauté créée !');
                            setTimeout(() => {
                                window.location.href = data.redirect_url || '<?php echo site_url("home/communities"); ?>';
                            }, 1500);
                        } else {
                            toastr.error(data.message || 'Erreur lors de la création');
                            if (data.errors) {
                                Object.keys(data.errors).forEach(field => {
                                    const input = document.querySelector(`[name="${field}"]`);
                                    if (input) showFieldError(input, data.errors[field]);
                                });
                            }
                        }
                    },
                    error: function(xhr, status, err) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalText;
                        console.error('AJAX Error:', xhr.responseText);
                        toastr.error('Erreur réseau ou serveur (403 ? Vérifie le contrôleur)');
                    }
                });
            });
        }

        // === INITIALISATION ===
        togglePriceField();
        updateCurrency();
    });
</script>