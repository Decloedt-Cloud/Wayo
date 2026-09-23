<!-- <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet"> -->
<?php
$session = \Config\Services::session();
$db = \Config\Database::connect();

$logo_light = get_logo_light();
$system_name = get_frontend_settings('website_title');

// Vérifier si l'utilisateur a rejoint au moins une communauté APPROUVÉE
$user_has_community = false;
$current_user_id = $session->get('user_id');

if ($current_user_id) {
    $user_schools = $db->table('user_schools')->where('user_id', $current_user_id)->where('school_id IS NOT NULL', null, false)->get()->getResult();
    
    if (!empty($user_schools)) {
        foreach ($user_schools as $user_school) {
            $role_in_school = strtolower($user_school->role ?? 'student');
            
            if ($role_in_school === 'admin' || $role_in_school === 'teacher') {
                $user_has_community = true;
                break;
            } else if ($role_in_school === 'student') {
                $student_entry = $db->table('students')->where('user_id', $current_user_id)->where('school_id', $user_school->school_id)->get()->getRow();
                if (!empty($student_entry)) {
                    $user_has_community = true;
                    break;
                }
            }
        }
    }
}
?>
<?php include 'app/Views/create_community_modal.php'; ?>
<style>
    /* Header Styles */
    @media (min-width: 991px) {
        #openLoginBtnM {
            display: none !important;
        }
    }

    /* Fixed alignment for 1220px - 1399px range */
    @media (min-width: 1220px) {
        .container-customize {
            max-width: 95% !important;
        }
        .navbar-nav .nav-link {
            white-space: nowrap !important;
            padding-left: 0.8rem !important;
            padding-right: 0.8rem !important;
            font-size: 15px !important;
        }
        .user-section {
            gap: 0.5rem !important;
        }
        .gap-2 {
            gap: 0.5rem !important;
        }
    }

    /* Reduce width for screens 1600px and larger */
    @media (min-width: 1600px) {
        .container-customize {
            max-width: 85% !important;
        }
    }
    @media (min-width: 1900px) {
        .container-customize {
            max-width: 75% !important;
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
        padding-left: 0;
        /* retire aussi le décalage gauche */
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
        position: relative;
        overflow: hidden;
    }
    .btn-lang::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(252, 123, 48, 0.1), transparent);
        transition: left 0.5s ease;
        pointer-events: none;
        z-index: 1;
    }
    .btn-lang:hover::before {
        left: 100%;
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
        position: relative;
        overflow: hidden;
    }
    .btn-custom::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
        transition: left 0.5s ease;
        pointer-events: none;
        z-index: 1;
    }
    .btn-custom:hover::before {
        left: 100%;
    }
    .btn-custom:hover {
        transform: translateY(-1px);
    }

    .btn-ghost {
        background: #fff;
        border: 1px solid var(--line);
        color: var(--text);
        position: relative;
        overflow: hidden;
    }
    .btn-ghost::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(252, 123, 48, 0.1), transparent);
        transition: left 0.5s ease;
        pointer-events: none;
        z-index: 1;
    }
    .btn-ghost:hover::before {
        left: 100%;
    }
    .btn-ghost:hover {
        background: #f7f7f7;
        color: var(--orange) !important;
    }

    .btn-accent {
        background: linear-gradient(135deg, var(--orange), var(--orange-2));
        color: #fff;
        border: none;
        box-shadow: 0 14px 28px rgba(244, 122, 31, 0.22);
        position: relative;
        overflow: hidden;
    }
    .btn-accent::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
        transition: left 0.5s ease;
        pointer-events: none;
        z-index: 1;
    }
    .btn-accent:hover::before {
        left: 100%;
    }
    .btn-accent:hover {
        background: linear-gradient(135deg, var(--orange), var(--orange-2));
        color: #fff;
    }

    /* Mobile Offcanvas */
    /* Forcer le offcanvas-top à prendre toute la hauteur en mobile */
    .offcanvas-top {
        height: 100vh !important;
        /* pleine hauteur */
        max-height: 100vh !important;
        overflow-y: auto;
        /* scroll si besoin */
    }

    .offcanvas-backdrop.show {
        opacity: 0.3;
        /* ou 0 si tu veux le supprimer totalement */
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

        .offcanvas .nav-link {
            background: #f7f7f7;
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


    .community-switcher {
        position: relative;
        z-index: 1050 !important;
    }

    #community-switcher.open #community-menu {
        display: block !important;
    }

    .switcher-trigger {
        background-color: #f7f7f7;
        border: 1px solid transparent;
        padding: 0.55rem 1rem;
        border-radius: 999px;
        display: flex;
        align-items: center;
        gap: 10px;
        cursor: pointer;
        font-weight: 800;
        color: var(--text);
        transition: all 0.2s ease;
        font-size: 1em;
    }

    .switcher-trigger:hover {
        background-color: #eaeaea;
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
        top: 100%;
        left: 0;
        margin-top: 8px;
    }

    #community-menu-mobile {
        display: none !important;
        position: fixed !important;
        width: 234px !important;
        background: white !important;
        border: 1px solid #ddd !important;
        border-radius: 12px !important;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1) !important;
        z-index: 9999 !important;
        padding: 0 !important;
        top: 100%;
        left: 50%;
        transform: translateX(-50%);
        margin-top: 8px;
    }

    #community-switcher-mobile.open #community-menu-mobile {
        display: block !important;
    }

    /* Z-index plus élevé pour le menu de langue dans l'offcanvas mobile */
    #navbarOffcanvas .language-selector .dropdown-menu {
        z-index: 10060 !important;
    }

    .menu-list {
        max-height: 200px;
        overflow-y: auto;
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
        font-weight: 500;
    }

    .action-item .fa-user-circle {
        color: #888888;
    }

    .action-item.create-item {
        color: #F06423;
        font-weight: 600;
    }

    .action-item.create-item .fa-user-circle {
        color: #F06423;
    }

    .menu-item.selected {
        background-color: #e6f7ff;
        border-left: 4px solid #F47A1F;
    }

    .role-card {
        text-decoration: none;
        display: grid;
        grid-template-columns: auto 1fr auto;
        align-items: center;
        gap: 12px;
        padding: 14px 16px;
        border-radius: 14px;
        background: #fff;
        border: 1px solid var(--line, #ececec);
        box-shadow: 0 8px 22px rgba(0, 0, 0, .06);
        transition: transform .12s ease, box-shadow .12s ease, border-color .12s ease, background .12s ease;
        cursor: pointer;
        font-size: 15px;
    }

    .role-card:hover {
        transform: translateY(-1px);
        box-shadow: 0 12px 28px rgba(0, 0, 0, .08);
        border-color: #ffd7b7;
        background: #fffdf9;
    }

    .role-card .community-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: #f7f7f7;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        color: #F47A1F;
    }

    .role-card .community-name {
        font-weight: 600;
        color: #2d2d2d;
        margin: 0;
    }

    .role-card .community-role {
        font-size: 13px;
        color: #888;
        margin: 0;
    }

    .role-card .role-badge {
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
    }

    #communityRoleList {
        display: grid;
        gap: 12px;
        /* Espace entre chaque carte */
        padding: 8px 0;
        /* Optionnel : un peu d'air en haut/bas */
    }

    #communityRoleModal .modal-body {
        padding-left: 1.5rem !important;
        max-height: 50vh;
        overflow-y: auto;
        padding-right: 8px;
        padding-top: 1rem;
        padding-bottom: 1rem;
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
        position: relative;
        overflow: hidden;
    }
    .btn-saas::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
        transition: left 0.5s ease;
        pointer-events: none;
        z-index: 1;
    }
    .btn-saas:hover::before {
        left: 100%;
    }

    .btn-community {
        background-color: #F06423;
        color: white;
        position: relative;
        overflow: hidden;
    }
    .btn-community::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
        transition: left 0.5s ease;
        pointer-events: none;
        z-index: 1;
    }
    .btn-community:hover::before {
        left: 100%;
    }
    .btn-community:hover {
        opacity: 0.9;
    }

    .btn-community-2 {
        background-color: #FFFFFF;
        color: #1F2937;
        border: 1px solid #E5E7EB;
        position: relative;
        overflow: hidden;
    }
    .btn-community-2::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(31, 41, 55, 0.1), transparent);
        transition: left 0.5s ease;
        pointer-events: none;
        z-index: 1;
    }
    .btn-community-2:hover::before {
        left: 100%;
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

    #communityRoleModal .role-card.selected {
        background-color: #e6f7ff !important;
        border-left: 4px solid #F47A1F !important;
        border-color: #ffd7b7 !important;
    }
</style>
</head>

<body>
    <!-- ========== HEADER ========== -->
    <header class="site-header" <?php echo (get_user_language() === 'arabic') ? 'dir="rtl"' : 'dir="ltr"'; ?>>
        <nav class="navbar navbar-expand-lg">
            <div class="container container-customize">
                <!-- Logo -->
                <a class="navbar-brand" href="<?php echo lang_route('home'); ?>" aria-label="Wayo Academy">
                    <img class="logo-img" src="<?php echo $logo_light; ?>" alt="<?php echo $system_name; ?>">
                </a>
                <!-- Mobile Toggle -->
                <button class="navbar-toggler ms-auto" id="nav-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#navbarOffcanvas" aria-controls="navbarOffcanvas" aria-label="Toggle navigation">
                    <i class="fas fa-bars"></i>
                </button>
                <?php if ($session->get('user_id') && $user_has_community) { ?>
                    <li class="nav-item navbar-user d-lg-none" style="margin:0 2px; list-style: none;">
                        <?php
                            // Determine dashboard link: if student is pending approval, redirect to invoice
                            $dashboard_link_mobile = route('dashboard');
                            if ($session->get('user_type') === 'student') {
                                $student_status_check_mobile = $db->table('students')->where(array('user_id' => $session->get('user_id'), 'school_id' => $session->get('active_school_id')))->get()->getRowArray();
                                if ($student_status_check_mobile && $student_status_check_mobile['status'] != 1) {
                                    $dashboard_link_mobile = route('invoice');
                                }
                            }
                        ?>
                        <a href="<?php echo $dashboard_link_mobile; ?>" target="" class="btn btn-login btn-ghost login-toggle w-100 mb-2" style="cursor:pointer !important;">
                            <?php echo get_phrase('community_app'); ?>
                        </a>
                    </li>
                <?php } elseif (!$session->get('user_id')) { ?>
                    <li class="nav-item navbar-user" style="list-style: none;">
                        <a id="openLoginBtnM" class="btn btn-login btn-ghost btn-custom w-100 mb-2 login-toggle" style="cursor:pointer !important; text-align:center;">
                            <?php echo get_phrase('Login'); ?>
                        </a>
                    </li>
                <?php } ?>



                <!-- Desktop Navigation -->
                <div class="collapse navbar-collapse" id="navbarNav" <?php echo (get_user_language() === 'arabic') ? 'dir="rtl"' : 'dir="ltr"'; ?>>
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item" style="margin:0 4px">
                            <a class="nav-link <?php if ($page_name === 'home') echo 'active'; ?>" href="<?php echo lang_route('home'); ?>"><?php echo get_phrase('Home'); ?></a>
                        </li>
                        <li class="nav-item" style="margin:0 4px">
                            <a class="nav-link <?php if ($page_name === 'communities') echo 'active'; ?>" href="<?php echo lang_route('communities'); ?>"><?php echo get_phrase('Our_Communities'); ?></a>
                        </li>
                        <li class="nav-item" style="margin:0 4px">
                            <a class="nav-link <?php if ($page_name === 'tutorial') echo 'active'; ?>" href="<?php echo lang_route('tutorial'); ?>"><?php echo get_phrase('How_it_works'); ?></a>
                        </li>
                    </ul>

                    <div class="d-flex align-items-center gap-2">
                        <?php if (
                            $session->get('user_type') == 'superadmin' || $session->get('user_type') == 'admin' || $session->get('user_type') == 'teacher'
                            || $session->get('user_type') == 'student'
                        ): ?>
                            <li class="dropdown notification-list topbar-dropdown d-none d-lg-block language-selector">
                                <a class="nav-link dropdown-toggle arrow-none" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                                    <i class="mdi mdi-earth me-1"></i>
                                    <button class="btn btn-lang" id="langToggle" aria-label="Language">
                                        <span>🌐</span>
                                        <span class="lang-code"><?php echo ucfirst(get_phrase(get_user_language())); ?></span>
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
                                <a class="nav-link dropdown-toggle arrow-none" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                                    <i class="mdi mdi-earth me-1"></i>
                                    <span class="btn btn-lang" id="langToggle" aria-label="Language" style="cursor:pointer !important;">
                                        <span>🌐</span>
                                        <span class="lang-code"><?php echo ucfirst(get_phrase(get_user_language())); ?></span>
                                    </span>
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
                        <?php if ($session->get('user_id') && $session->get('role') != 'superadmin') { ?>
                            <!-- === COMMUNITY SWITCHER (NOUVEAU STYLE MODERNE) === -->
                            <div class="community-switcher" id="community-switcher" style="margin: 0 8px;">
                                <button class="switcher-trigger" id="switcher-trigger">
                                    <span class="current-role" id="current-role-label"><?php echo get_phrase("loading..."); ?></span>
                                    <i class="fas fa-chevron-down arrow-icon"></i>
                                </button>

                                <div id="community-menu" class="shadow">
                                    <div class="menu-list" id="role-dropdown-menu">
                                        <!-- Rôles injectés par JS -->
                                    </div>
                                    <hr class="menu-divider">
                                    <button class="menu-item action-item create-item" id="open-create-community-btn">
                                        <i class="fas fa-plus"></i>
                                        <span><?php echo get_phrase("create_community"); ?></span>
                                    </button>
                                </div>
                            </div>
                        <?php } ?>
                        <?php if ($session->get('user_id')) { ?>
                            <?php 
                                // Determine dashboard link: if student is pending approval, redirect to invoice
                                $dashboard_link = route('dashboard');
                                if (!$user_has_community) {
                                    $dashboard_link = route('invoice');
                                } else if ($session->get('user_type') === 'student') {
                                    $student_status_check = $db->table('students')->where(array('user_id' => $session->get('user_id'), 'school_id' => $session->get('active_school_id')))->get()->getRowArray();
                                    if ($student_status_check && $student_status_check['status'] != 1) {
                                        $dashboard_link = route('invoice');
                                    }
                                }
                            ?>
                            <li class="nav-item navbar-user" style="margin:0 2px ; list-style:none">
                                <a href="<?php echo $dashboard_link; ?>" target="" class="btn btn-login btn-ghost login-toggle" style="cursor:pointer !important;"> <?php echo get_phrase('community_app'); ?> </a>
                            </li>
                            
                            <li class="nav-item navbar-user-profile" style="margin:0 2px; list-style:none;">
                                <div class="user-section" style="white-space: nowrap;">
                                    <img src="<?php echo get_user_image($session->get('user_id')); ?>" alt="user-image" class="rounded-circle nav-user-img">
                                </div>
                                <?php include 'components/navigation-components/user_loggedin_component.php'; ?>
                            </li>
                        <?php } else { ?>
                            <li class="nav-item navbar-user" style="
                                                        list-style: none;
                                                    ">
                                <a id="openLoginBtn" class="btn btn-login btn-ghost login-toggle" style="cursor:pointer !important;"><?php echo get_phrase('Login'); ?> </a>
                            </li>
                            <li class="nav-item navbar-user" style="list-style: none;">
                                <a href="<?php echo lang_route('join/community'); ?>" class="btn btn-accent btn-custom" style="cursor:pointer !important;">
                                    <?php echo get_phrase('Create_Community'); ?>
                                </a>
                            </li>
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
                        <a class="nav-link <?php if ($page_name === 'home') echo 'active'; ?>" href="<?php echo lang_route('home'); ?>">
                            <?php echo get_phrase('Home'); ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php if ($page_name === 'communities') echo 'active'; ?>" href="<?php echo lang_route('communities'); ?>">
                            <?php echo get_phrase('Our_Communities'); ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php if ($page_name === 'tutorial') echo 'active'; ?>" href="<?php echo lang_route('tutorial'); ?>">
                            <?php echo get_phrase('How_it_works'); ?>
                        </a>
                    </li>
                </ul>

                <div class="offcanvas-actions mt-3">
                    <?php if ($session->get('user_id') && $session->get('role') != 'superadmin') { ?>
                        <!-- Switcher de communauté (mobile) -->
                        <div class="community-switcher mb-3" id="community-switcher-mobile" style="margin: 0 8px; list-style:none; width:100%; display:flex; justify-content:center;">
                            <button class="switcher-trigger" id="switcher-trigger-mobile">
                                <span class="current-role" id="current-role-label-mobile"><?php echo get_phrase("loading..."); ?></span>
                                <i class="fas fa-chevron-down arrow-icon"></i>
                            </button>
                            <div id="community-menu-mobile" class="shadow">
                                <div class="menu-list" id="role-dropdown-menu-mobile">
                                    <!-- Rôles injectés par JS -->
                                </div>
                                <hr class="menu-divider">
                                <button class="menu-item action-item create-item" id="open-create-community-btn-mobile">
                                    <i class="fas fa-plus"></i>
                                    <span><?php echo get_phrase("create_community"); ?></span>
                                </button>
                            </div>
                        </div>
                    <?php } ?>
                    <!-- Language Selector for Mobile -->
                    <?php if ($session->get('user_type') == 'superadmin' || $session->get('user_type') == 'admin' || $session->get('user_type') == 'teacher' || $session->get('user_type') == 'student'): ?>
                        <li class="dropdown notification-list topbar-dropdown language-selector mb-3" style="list-style: none; width:100%">
                            <a class="nav-link dropdown-toggle arrow-none" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false" style="width:100%">
                                <button class="btn  w-100 d-flex align-items-center justify-content-center gap-2" id="langToggleMobile" aria-label="Language" style="cursor:pointer !important;">
                                    <span>🌐</span>
                                    <span class="lang-code"><?php echo ucfirst(get_phrase(get_user_language())); ?></span>
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
                            <a class="nav-link dropdown-toggle arrow-none" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false" style="width:100%">
                                <button class="btn  w-100 d-flex align-items-center justify-content-center gap-2" id="langToggleMobileGuest" aria-label="Language" style="cursor:pointer !important;">
                                    <span>🌐</span>
                                    <span class="lang-code"><?php echo ucfirst(get_phrase(get_user_language())); ?></span>
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

                    <?php if ($session->get('user_id')) {  ?>
                        <li class="nav-item navbar-user-profile mb-3" style="margin:0 2px; list-style:none;">
                            <div class="p-2 border rounded">
                                <div class="d-flex align-items-center justify-content-between gap-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="<?php echo get_user_image($session->get('user_id')); ?>"
                                            alt="user-image" class="rounded-circle nav-user-img" style="width: 40px; height: 40px;">
                                        <span class="fw-semibold" style="font-size: 15px; color: var(--text);">
                                            <?php echo session()->user_name; ?>
                                        </span>
                                    </div>
                                    <a href="<?php echo site_url('login/logout'); ?>" class="btn btn-link text-danger" aria-label="<?php echo get_phrase('logout'); ?>" style="font-size: 18px;">
                                        <i class="fa-solid fa-right-from-bracket"></i>
                                    </a>
                                </div>
                            </div>
                        </li>
                    <?php } else { ?>
                        <a class="btn btn-accent btn-custom w-100" href="<?php echo lang_route('join/community'); ?>">
                            <?php echo get_phrase('Create_Community'); ?>
                        </a>
                    <?php } ?>
                </div>
            </div>
        </div>
        <?php if (!$session->get('user_id')) { ?>
            <?php include 'components/navigation-components/login_register_component.php'; ?>
        <?php } ?>
    </header>
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

                <input type="hidden" id="csrf_token" name="<?php echo csrf_token(); ?>" value="<?php echo csrf_hash(); ?>" />

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
                            <?php $categories = $db->table('categories')->where(array())->get()->getResultArray(); ?>
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
                            <span id="currencySymbol" style="color: #9CA3AF;">—</span>
                            <input type="text" id="community-price" name="price" class="form-input-community" placeholder="299.00" inputmode="decimal">
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
                            <div class="upload-placeholder-saas"><i class="fas fa-image"></i><span><?php echo get_phrase("cover_(16:5)") ?></span></div>
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
                            <div class="plan-price">
                                <?php if (community_billing_enabled()) : ?>
                                    790 <?php echo get_phrase("MAD") ?> <span>/ <?php echo get_phrase("month,_no_commitment") ?></span>
                                <?php else : ?>
                                    <?php echo get_phrase('community_is_free_for_now'); ?>
                                <?php endif; ?>
                            </div>
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
                            <?php if (community_billing_enabled()) : ?>
                                <strong><?php echo get_phrase("enjoy_a_14-day_free_trial.") ?></strong>
                                <span><?php echo get_phrase("no_payment_will_be_required_before_the_end_of_your_trial.") ?></span>
                            <?php else : ?>
                                <strong><?php echo get_phrase('community_is_free_for_now'); ?></strong>
                                <span><?php echo get_phrase('no_payment_is_required_for_now'); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="modal-actions space-between">
                        <button type="button" class="btn-saas btn-community-2" id="modal-back-btn"><?php echo get_phrase("back") ?></button>
                        <button type="submit" class="btn-saas btn-community">
                            <?php echo community_billing_enabled() ? get_phrase("start_the_14_day_trial") : get_phrase('create_the_community'); ?>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="modal fade" id="communityRoleModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><?php echo get_phrase('choose_a_community'); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="communityRoleList">
                    <!-- Liste des communautés -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo get_phrase('cancel'); ?></button>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const joinBtn = document.getElementById("openLoginBtnM");
            const navToggler = document.getElementById("nav-toggler");

            function scrollToTop() {
                window.scrollTo({ top: 0, behavior: "smooth" });
            }

            joinBtn?.addEventListener("click", scrollToTop);
            navToggler?.addEventListener("click", scrollToTop);
        });
</script>
    <!-- ========== END HEADER ========== -->
    <script>
        window.checkCommunityNameUrl = '<?php echo site_url("home/check_community_name_exists"); ?>';
        window.csrfTokenName = '<?php echo csrf_token(); ?>';
        window.csrfTokenValue = '<?php echo csrf_hash(); ?>';
        window.csrfCookieName = '<?php echo config('Security')->cookieName; ?>';

        window.getCurrentCsrfHash = function() {
            var cookieName = window.csrfCookieName || 'csrf_cookie_name';
            var tokenFromCookie = null;
            var escapedName = cookieName.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
            var match = document.cookie.match(new RegExp('(?:^|; )' + escapedName + '=([^;]*)'));
            if (match && match[1]) {
                tokenFromCookie = decodeURIComponent(match[1]);
            }
            return tokenFromCookie || window.csrfTokenValue;
        };

        window.withCsrf = function(data) {
            data = data || {};
            data[window.csrfTokenName] = window.getCurrentCsrfHash();
            return data;
        };
    </script>
    <script src="<?php echo base_url('assets/backend/js/create_community_modal.min.js'); ?>"></script>
    <script>
        // Language code mapping
        var langCodeMap = {
            'french': 'fr',
            'english': 'en',
            'arabic': 'ar',
            'spanish': 'es',
            'dutch': 'nl'
        };

        function getLanguageList() {
            $.ajax({
                url: "<?php echo route('language/dropdown'); ?>",
                success: function(response) {
                    // Remplir à la fois desktop et mobile
                    $('#language-list').html(response);
                    $('#language-list-mobile').html(response);
                }
            });
        }

        function getGuestLanguageList() {
            $.ajax({
                url: "<?php echo site_url('home/dropdown_guest_lang'); ?>",
                success: function(response) {
                    $('#guest-language-list').html(response);
                    $('#guest-language-list-mobile').html(response);
                }
            });
        }

        $(document).ready(function() {
            $('.language-selector .dropdown-toggle').on('shown.bs.dropdown', function() {
                var $this = $(this);
                if ($this.closest('.language-selector').find('#language-list').length) {
                    getLanguageList();
                } else if ($this.closest('.language-selector').find('#guest-language-list').length) {
                    getGuestLanguageList();
                }
            });
        });

        function setGuestLanguage(lang) {
            // Get the language code for URL
            var langCode = langCodeMap[lang.toLowerCase()] || 'fr';
            
            $.post("<?php echo site_url('home/set_guest_language'); ?>", window.withCsrf({
                language: lang
            }), function(response) {
                var supportedCodes = ['fr', 'en', 'ar', 'es', 'nl'];
                var currentUrl = window.location.href;
                var currentPath = window.location.pathname;
                
                // Get base URL without trailing slash
                var baseUrl = '<?php echo rtrim(base_url(), "/"); ?>';
                
                // Extract the path part after the base URL
                var fullPath = currentPath;
                
                // Get base path from the base URL  
                try {
                    var urlObj = new URL(baseUrl);
                    var basePath = urlObj.pathname.replace(/\/$/, '');
                    
                    // Remove base path from current path
                    if (basePath && fullPath.indexOf(basePath) === 0) {
                        fullPath = fullPath.substring(basePath.length);
                    }
                } catch(e) {
                    // Fallback for older browsers
                    var basePath = baseUrl.replace(/^https?:\/\/[^\/]+/, '').replace(/\/$/, '');
                    if (basePath && fullPath.indexOf(basePath) === 0) {
                        fullPath = fullPath.substring(basePath.length);
                    }
                }
                
                // Clean up the path
                fullPath = fullPath.replace(/^\/+/, ''); // Remove leading slashes
                
                // Split path into parts
                var pathParts = fullPath.split('/').filter(function(p) { 
                    return p !== '' && p !== 'home' && p !== 'index.php'; 
                });
                
                // Check if first part is a language code and replace/add
                if (pathParts.length > 0 && supportedCodes.indexOf(pathParts[0]) !== -1) {
                    // Replace existing language prefix
                    pathParts[0] = langCode;
                } else if (pathParts.length > 0) {
                    // Add new language prefix at the beginning
                    pathParts.unshift(langCode);
                } else {
                    // Just the language code for home page
                    pathParts = [langCode];
                }
                
                // Build the new URL using site_url pattern
                var newUrl = baseUrl + '/' + pathParts.join('/');
                
                // Debug (remove after testing)
                console.log('Language switch debug:', {
                    baseUrl: baseUrl,
                    currentPath: currentPath,
                    fullPath: fullPath,
                    pathParts: pathParts,
                    newUrl: newUrl
                });
                
                window.location.href = newUrl;
            });
        }
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggler = document.querySelector('.navbar-toggler');
            const collapse = document.querySelector('.navbar-collapse');

            toggler?.addEventListener('click', function() {
                collapse.classList.toggle('active');
            });
        });
    </script>

    <script>
        const roleTranslations = {
            superadmin: '<?php echo get_phrase("superadmin"); ?>',
            admin: '<?php echo get_phrase("admin"); ?>',
            teacher: '<?php echo get_phrase("mentor"); ?>', // ou "teacher" selon ta traduction
            student: '<?php echo get_phrase("member"); ?>',
        };
        document.addEventListener('DOMContentLoaded', function() {
            const switcher = document.getElementById('community-switcher');
            const trigger = document.getElementById('switcher-trigger');
            const menu = document.getElementById('community-menu');
            const roleMenu = document.getElementById('role-dropdown-menu');
            const currentLabel = document.getElementById('current-role-label');

            // Si les éléments n'existent pas (utilisateur non connecté), on arrête
            if (!switcher || !trigger || !menu || !roleMenu || !currentLabel) {
                return;
            }

            const currentRole = '<?php echo strtolower($session->get("role") ?? ""); ?>';

            // === Charger les rôles ===
            function loadUserRoles() {
                $.ajax({
                    url: '<?php echo site_url("home/get_user_roles"); ?>',
                    type: 'GET',
                    success: function(response) {
                        let res = typeof response === 'string' ? JSON.parse(response) : response;
                        if (res.status === 'success') {
                            renderRoles(res.roles);
                            updateCurrentLabel(res.roles);
                        }
                    }
                });
            }

            // === Rendu des rôles ===
            function renderRoles(roles) {
                roleMenu.innerHTML = '';
                roles.forEach(role => {
                    const item = document.createElement('button');
                    item.className = 'menu-item';
                    if (role.role.toLowerCase() === currentRole) {
                        item.classList.add('selected');
                    }

                    const label = document.createElement('span');
                    label.textContent = roleTranslations[role.role.toLowerCase()] || role.label;

                    const badge = document.createElement('span');
                    badge.className = `role-badge ${role.role.toLowerCase()}`;
                    badge.textContent = role.count;

                    item.appendChild(label);
                    item.appendChild(badge);

                    item.onclick = function(e) {
                        e.stopPropagation();
                        closeMenu();
                        if (role.count > 1) {
                            openCommunityModal(role.role);
                        } else {
                            switchRole(role.role, role.communities[0].school_id);
                        }
                    };

                    roleMenu.appendChild(item);
                });
            }

            // === Mettre à jour le label ===
            function updateCurrentLabel(roles) {
                const current = roles.find(r => r.role.toLowerCase() === currentRole);
                currentLabel.textContent = current ?
                    (roleTranslations[current.role.toLowerCase()] || current.label) :
                    roleTranslations.student;
            }

            // === Ouvrir/Fermer le menu ===
            trigger.addEventListener('click', function(e) {
                e.stopPropagation();
                const isOpen = switcher.classList.contains('open');
                closeMenu();
                if (!isOpen) {
                    switcher.classList.add('open');
                    positionMenu();
                }
            });

            function closeMenu() {
                switcher.classList.remove('open');
            }

            function positionMenu() {
                const rect = trigger.getBoundingClientRect();
                menu.style.left = `${rect.left}px`;
                menu.style.top = `${rect.bottom + 8}px`;
            }

            // === Fermer au clic extérieur ===
            document.addEventListener('click', function(e) {
                if (!switcher.contains(e.target)) {
                    closeMenu();
                }
            });

            window.addEventListener('resize', positionMenu);

            // === Modal Communauté ===
            const communityModal = new bootstrap.Modal(document.getElementById('communityRoleModal'));
            const communityList = document.getElementById('communityRoleList');

            window.openCommunityModal = function(role) {
                $.ajax({
                    url: '<?php echo site_url("home/get_communities_by_role"); ?>',
                    type: 'POST',
                    data: window.withCsrf({
                        role: role
                    }),
                    success: function(response) {
                        let res = typeof response === 'string' ? JSON.parse(response) : response;
                        if (res.status === 'success') {
                            renderCommunityList(res.communities, role);
                            communityModal.show();
                        }
                    }
                });
            };

            function renderCommunityList(communities, role) {
                communityList.innerHTML = '';
                if (communities.length === 0) {
                    const empty = document.createElement('div');
                    empty.className = 'text-center text-muted p-3';
                    empty.textContent = '<?php echo get_phrase("no_community_found"); ?>';
                    communityList.appendChild(empty);
                    return;
                }

                // Récupérer l'ID de la communauté active depuis PHP
                const currentSchoolId = '<?php echo $session->get("school_id"); ?>';

                communities.forEach(c => {
                    const card = document.createElement('a');
                    card.href = 'javascript:void(0);';
                    card.className = 'role-card';

                    // === AJOUT : Highlight si c'est la communauté active ===
                    if (c.school_id == currentSchoolId) {
                        card.classList.add('selected');
                    }

                    // Icône
                    const icon = document.createElement('div');
                    icon.className = 'community-icon';
                    icon.innerHTML = '<i class="fas fa-users"></i>';

                    // Contenu texte
                    let nameHtml = `<div class="community-name">${c.community_name}</div>`;
                    if (c.status !== undefined && c.status != 1) {
                        nameHtml += `<div class="community-role text-warning" style="font-size: 0.85em; margin-top: 2px;">
                                        <i class="fas fa-clock"></i> <?php echo get_phrase('Pending Approval'); ?>
                                     </div>`;
                    } else {
                         nameHtml += `<div class="community-role">${c.role_label}</div>`;
                    }

                    const content = document.createElement('div');
                    content.innerHTML = nameHtml;

                    // Badge rôle
                    const badge = document.createElement('span');
                    badge.className = `role-badge ${role}`;
                    const roleLabels = {
                        'admin': '<?php echo get_phrase('admin'); ?>',
                        'teacher': '<?php echo get_phrase('mentor'); ?>',
                        'student': '<?php echo get_phrase('member'); ?>'
                    };
                    badge.textContent = roleLabels[role.toLowerCase()] || role.charAt(0).toUpperCase() + role.slice(1);

                    // Assemblage
                    card.appendChild(icon);
                    card.appendChild(content);
                    card.appendChild(badge);

                    // Action au clic
                    card.onclick = function() {
                        switchRole(role, c.school_id);
                        communityModal.hide();
                    };

                    communityList.appendChild(card);
                });
            }

            function switchRole(role, school_id) {
                const currentUrl = window.location.href; // URL actuelle

                $.ajax({
                    url: '<?php echo site_url("home/switch_community_role_front"); ?>',
                    type: 'POST',
                    data: window.withCsrf({
                        school_id: school_id,
                        role: role,
                        return_url: currentUrl
                    }),
                    success: function(response) {
                        let res = typeof response === 'string' ? JSON.parse(response) : response;
                        if (res.status === 'success') {
                            // Déclenche l'événement pour mettre à jour le switcher
                            window.dispatchEvent(new Event('roleSwitched'));
                            // Recharge la page actuelle → le rôle est mis à jour
                            window.location.href = res.redirect_url;
                        } else {
                            toastr.error(res.message || 'Erreur');
                        }
                    }
                });
            }

            // === Charger au démarrage ===
            loadUserRoles();
            setTimeout(positionMenu, 100); // petit délai pour le DOM

            // === Recharger après switch ===
            window.addEventListener('roleSwitched', loadUserRoles);
            document.getElementById('open-create-community-btn')?.addEventListener('click', () => {
                document.getElementById('createCommunityModal').classList.add('show');
            });
        });

        // === Initialisation du switcher (mobile) ===
        document.addEventListener('DOMContentLoaded', function() {
            const switcher = document.getElementById('community-switcher-mobile');
            const trigger = document.getElementById('switcher-trigger-mobile');
            const menu = document.getElementById('community-menu-mobile');
            const roleMenu = document.getElementById('role-dropdown-menu-mobile');
            const currentLabel = document.getElementById('current-role-label-mobile');
            if (!switcher || !trigger || !menu || !roleMenu || !currentLabel) return;

            const currentRole = '<?php echo strtolower($session->get("role") ?? ""); ?>';

            function loadUserRolesMobile() {
                $.ajax({
                    url: '<?php echo site_url("home/get_user_roles"); ?>',
                    type: 'GET',
                    success: function(response) {
                        let res = typeof response === 'string' ? JSON.parse(response) : response;
                        if (res.status === 'success') {
                            renderRolesMobile(res.roles);
                            updateCurrentLabelMobile(res.roles);
                        }
                    }
                });
            }

            function renderRolesMobile(roles) {
                roleMenu.innerHTML = '';
                roles.forEach(role => {
                    const item = document.createElement('button');
                    item.className = 'menu-item';
                    if (role.role.toLowerCase() === currentRole) {
                        item.classList.add('selected');
                    }
                    const label = document.createElement('span');
                    label.textContent = roleTranslations[role.role.toLowerCase()] || role.label;
                    const badge = document.createElement('span');
                    badge.className = `role-badge ${role.role.toLowerCase()}`;
                    badge.textContent = role.count;
                    item.appendChild(label);
                    item.appendChild(badge);
                    item.onclick = function(e) {
                        e.stopPropagation();
                        closeMenuMobile();
                        if (role.count > 1) {
                            openCommunityModal(role.role);
                        } else {
                            switchRoleMobile(role.role, role.communities[0].school_id);
                        }
                    };
                    roleMenu.appendChild(item);
                });
            }

            function updateCurrentLabelMobile(roles) {
                const current = roles.find(r => r.role.toLowerCase() === currentRole);
                currentLabel.textContent = current ?
                    (roleTranslations[current.role.toLowerCase()] || current.label) :
                    roleTranslations.student;
            }

            trigger.addEventListener('click', function(e) {
                e.stopPropagation();
                const isOpen = switcher.classList.contains('open');
                closeMenuMobile();
                if (!isOpen) {
                    switcher.classList.add('open');
                    positionMenuMobile();
                }
            });

            function closeMenuMobile() {
                switcher.classList.remove('open');
            }

            function positionMenuMobile() {
                const rect = trigger.getBoundingClientRect();
                menu.style.left = `${rect.left + (rect.width / 2)}px`;
                menu.style.transform = `translateX(-50%)`;
                menu.style.top = `${rect.bottom + 8}px`;
            }

            document.addEventListener('click', function(e) {
                if (!switcher.contains(e.target)) {
                    closeMenuMobile();
                }
            });
            document.addEventListener('touchstart', function(e) {
                if (!switcher.contains(e.target)) {
                    closeMenuMobile();
                }
            }, { passive: true });
            document.getElementById('navbarOffcanvas')?.addEventListener('hide.bs.offcanvas', function() {
                closeMenuMobile();
            });
            window.addEventListener('resize', positionMenuMobile);

            function switchRoleMobile(role, school_id) {
                const currentUrl = window.location.href;
                $.ajax({
                    url: '<?php echo site_url("home/switch_community_role_front"); ?>',
                    type: 'POST',
                    data: window.withCsrf({
                        school_id: school_id,
                        role: role,
                        return_url: currentUrl
                    }),
                    success: function(response) {
                        let res = typeof response === 'string' ? JSON.parse(response) : response;
                        if (res.status === 'success') {
                            window.dispatchEvent(new Event('roleSwitched'));
                            window.location.href = res.redirect_url;
                        } else {
                            toastr.error(res.message || 'Erreur');
                        }
                    }
                });
            }

            loadUserRolesMobile();
            setTimeout(positionMenuMobile, 100);
            window.addEventListener('roleSwitched', loadUserRolesMobile);
            document.getElementById('open-create-community-btn-mobile')?.addEventListener('click', () => {
                document.getElementById('createCommunityModal').classList.add('show');
            });
        });
    </script>
