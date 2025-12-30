<?php if (get_common_settings('recaptcha_status')): ?>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
<?php endif; ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/3.5.0/css/flag-icon.min.css">
<?php
?>

<style>
    /* ----------------- Hero ----------------- */

    .hero {
        position: relative;
        min-height: 68vh;
        display: grid;
        place-items: center;
        color: #fff;
        background-image: url('../uploads/images/decloedt/img/cover-wayo.png');
        background-size: cover;
        background-position: center;
    }

    .hero::before {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg, rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.60));
    }

    .hero .hero-content {
        position: relative;
        text-align: center;
    }

    .hero .lead {
        max-width: 760px;
        margin-inline: auto;
        color: #e9e9ef
    }

    .btn-pill {
        border-radius: 999px;
        font-weight: 700;
        padding: .8rem 1.2rem;
    }

    /* ====== Step 2 layout ====== */
    .panel--community .grid {
        display: grid;
        grid-template-columns: 1.15fr .85fr;
        gap: 24px;
    }

    @media (max-width: 992px) {
        .hero {
            position: relative;
            min-height: 68vh;
            display: grid;
            place-items: center;
            color: #fff;
            background-image: url('../uploads/images/decloedt/img/cover-wayo.png');
            background-size: cover;
            background-position: center;
        }

        .hero::before {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.60));
        }

        .hero .hero-content {
            position: relative;
            text-align: center;
        }

        .hero .lead {
            max-width: 760px;
            margin-inline: auto;
            color: #e9e9ef
        }

        .btn-pill {
            border-radius: 999px;
            font-weight: 700;
            padding: .8rem 1.2rem;
        }

        /* ====== Step 2 layout ====== */
        .panel--community .grid {
            grid-template-columns: 1fr;
        }
    }

    .panel--community fieldset {
        border: 1px solid #eee;
        border-radius: 12px;
        padding: 18px;
        margin: 0 0 18px 0;
        background: #fff;
    }

    .panel--community legend {
        font-weight: 700;
        font-size: 14px;
        padding: 0 8px;
        color: #333;
    }

    .panel--community .stack>.field {
        margin-bottom: 14px;
    }

    #communityprice.bg-light {
        background-color: #f8f9fa;
        cursor: not-allowed;
        color: #777;
    }

    .alert {
        padding: 12px 14px;
        border-radius: 8px;
        font-size: .95rem;
    }

    .alert-warning {
        background: #fff7e6;
        border: 1px solid #ffe0a3;
        color: #7a4d00;
    }
     /* === NOUVEAU STYLE UNIFIÉ POUR TÉLÉPHONE === */
    .unified-phone-wrapper {
        display: flex;
        align-items: stretch;
        border: 1px solid #ECEEF3;
        border-radius: 12px;
        background: #f7f8fb;
        transition: all 0.3s ease;
        height: 52px; 
        /* overflow: hidden; Removed to allow dropdown visibility */
        position: relative;
    }

    .unified-phone-wrapper:focus-within {
        border-color: #ff6b35;
        background: #fff;
    }

    .unified-phone-wrapper.is-invalid {
        border-color: #dc3545 !important;
    }

    .unified-phone-wrapper .country-select-wrapper {
        width: 80px; 
        border-right: 1px solid #ECEEF3;
        background-color: rgba(0,0,0,0.02);
        display: flex;
        align-items: center;
        position: relative;
    }

    .custom-select-trigger {
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0 0.75rem;
        cursor: pointer;
        width: 100%;
    }

    .custom-options {
        position: absolute;
        top: 100%;
        left: 0;
        right: auto;
        background: #fff;
        border: 1px solid #e0e0e0;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        z-index: 9999;
        display: none;
        max-height: 200px;
        overflow-y: auto;
        margin-top: 4px;
        min-width: 80px;
    }

    .custom-options.open {
        display: block;
    }

    .custom-option {
        padding: 10px;
        cursor: pointer;
        text-align: center;
        transition: background 0.2s;
    }

    .custom-option:hover {
        background-color: #fff2ea;
        color: #ff6b35;
    }

    .custom-option.selected {
        background-color: #fff2ea;
        font-weight: bold;
    }

    .flag-icon {
        font-size: 1.1em;
        line-height: 1em;
        border-radius: 3px;
    }

    .unified-phone-wrapper .form-control {
        border: none !important;
        box-shadow: none !important;
        background-color: transparent !important;
        height: 100% !important;
        padding: 10px 14px !important;
        font-size: 1rem;
    }

    .unified-phone-wrapper .form-control:focus {
        box-shadow: none !important;
        background-color: #fff !important;
    }

    /* Ajustement pour coller au style de la page online_admission */
    .unified-phone-wrapper {
        margin-top: 0;
    }

    /* onording popup */
     /* Overlay */
    .onb-overlay {
      position: fixed;
      inset: 0;
      background: rgba(0,0,0,.5);
      backdrop-filter: blur(4px);
      opacity: 0;
      pointer-events: none;
      transition: opacity .3s ease;
      z-index: 99999;
    }
    .onb-overlay.active {
      opacity: 1;
      pointer-events: auto;
    }

    /* Modal wrapper */
    .onb-modal {
      position: fixed;
      inset: 0;
      display: flex;
      align-items: center;
      justify-content: center;
      pointer-events: none;
      z-index: 100000;
      padding: 1rem;
    }
    .onb-modal.active {
      pointer-events: auto;
    }

    /* Card */
    .onb-card {
      background: #fff;
      width: min(700px, 100%);
      border-radius: 20px;
      box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
      transform: translateY(20px) scale(0.98);
      opacity: 0;
      transition: all .3s cubic-bezier(0.16, 1, 0.3, 1);
      display: flex;
      flex-direction: column;
      position: relative;
      overflow: hidden;
    }
    .onb-modal.active .onb-card {
      transform: translateY(0) scale(1);
      opacity: 1;
    }

    /* Close */
    .onb-close {
      position: absolute;
      top: 16px;
      right: 16px;
      border: none;
      background: #f3f4f6;
      width: 32px;
      height: 32px;
      border-radius: 50%;
      font-size: 16px;
      color: #374151;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: background .2s, color .2s;
      z-index: 10;
    }
    .onb-close:hover {
      background: #e5e7eb;
      color: #111;
    }

    /* Header */
    .onb-header {
      padding: 1.5rem 2rem 1rem;
      border-bottom: 1px solid #f3f4f6;
    }
    .onb-brand {
      font-size: 1.25rem;
      font-weight: 800;
      color: #111827;
      margin-left: 0.5rem;
    }

    .onb-progress {
      height: 6px;
      background: #f3f4f6;
      border-radius: 10px;
      margin-top: 1.25rem;
      overflow: hidden;
    }
    .onb-progress-bar {
      height: 100%;
      width: 0%;
      background: #f47a1f;
      border-radius: 10px;
      transition: width .4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* Body */
    .onb-body {
      padding: 2rem;
      min-height: 200px;
    }
    .onb-step {
      display: none;
      animation: fadeIn .4s ease;
    }
    .onb-step.is-active {
      display: block;
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(5px); }
      to { opacity: 1; transform: translateY(0); }
    }
    .onb-step h2 {
      font-size: 1.75rem;
      font-weight: 800;
      color: #111827;
      margin-bottom: 1rem;
    }
    .onb-step p {
      font-size: 1.05rem;
      color: #4b5563;
      line-height: 1.6;
    }
    .onb-step ul {
        list-style-type: disc;
        padding-left: 1.5rem;
        color: #4b5563;
    }

    /* Footer */
    .onb-footer {
      padding: 1.25rem 2rem;
      border-top: 1px solid #f3f4f6;
      background: #fff;
    }

    /* Buttons override */
    .btn-primary-custom, .btn-outline-primary-custom {
      padding: 0.75rem 1.5rem;
      font-weight: 700;
      font-size: 0.95rem;
      border-radius: 12px;
      transition: all 0.2s ease;
      letter-spacing: 0.01em;
      cursor: pointer;
    }

    .btn-primary-custom {
      background: #f47a1f;
      border: 1px solid #f47a1f;
      color: white;
      box-shadow: 0 4px 6px rgba(244, 122, 31, 0.2);
    }
    .btn-primary-custom:hover {
      background: #e06912;
      border-color: #e06912;
      transform: translateY(-1px);
      box-shadow: 0 6px 12px rgba(244, 122, 31, 0.3);
      color:#fff;
    }

    .btn-outline-primary-custom {
      background: transparent;
      color: #f47a1f;
      border: 1px solid #f47a1f;
    }
    .btn-outline-primary-custom:hover {
      background: #fff2ea;
      color: #e06912;
      border-color: #e06912;
    }
    .btn-outline-primary-custom:disabled,
    .btn-primary-custom:disabled {
        opacity: 0.5;
        cursor: pointer;
    }

    /* Responsive Mobile */
    @media (max-width: 576px) {
        .onb-card {
           width: 95%;
           margin: 10px;
        }
        
        .onb-header {
            padding: 1.25rem 1.5rem;
        }
        
        .onb-body {
            padding: 1.5rem;
        }

        .onb-footer {
            flex-direction: column-reverse; /* Ignorer en bas */
            gap: 12px;
            align-items: stretch !important; /* Pleine largeur */
        }

        .onb-footer .d-flex {
             width: 100%;
             display: flex;
             gap: 12px;
        }
        
        .onb-footer .d-flex .btn {
            flex: 1; /* Prev et Next prennent 50% chacun */
        }

        .onb-skip {
            width: 100%;
        }
    }
    /* ================= SUCCESS POPUP ================= */
.success-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(3, 7, 18, 0.6);
  backdrop-filter: blur(4px);
  z-index: 100001;

  display: flex;
  align-items: center;
  justify-content: center;

  opacity: 0;
  pointer-events: none;
  transition: opacity 0.3s ease;
}

/* État visible */
.success-overlay.is-visible {
  opacity: 1;
  pointer-events: auto;
}

/* Card */
.success-card {
  background: #ffffff;
  width: min(500px, 90vw);
  padding: 3rem 2.5rem;
  border-radius: 30px;
  text-align: center;
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);

  transform: scale(0.9) translateY(20px);
  transition: transform 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
}

/* Animation d’entrée */
.success-overlay.is-visible .success-card {
  transform: scale(1) translateY(0);
}

/* Icone */
.success-icon {
  width: 88px;
  height: 88px;
  margin: 0 auto 1.5rem;

  background: #fff5ec; /* light orange */
  color: #f47a1f;      /* orange */
  border-radius: 28px; /* Squircle */

  display: flex;
  align-items: center;
  justify-content: center;

  font-size: 2.5rem;
  transform: rotate(-10deg);
  animation: success-pop 0.6s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
}

/* Titre */
.success-card h2 {
  margin: 0 0 0.75rem;
  font-size: 2rem;
  font-weight: 800;
  color: #1e1e4b;
}

/* Texte */
.success-card p {
  margin: 0 auto 2rem;
  font-size: 1.05rem;
  line-height: 1.5;
  color: #6b7280;
  max-width: 400px;
}

/* Info Box */
.success-info-box {
    background: #f8fafc;
    border: 1px solid #f1f5f9;
    border-radius: 16px;
    padding: 1.25rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 2rem;
    text-align: left;
}

.success-info-icon {
    font-size: 1.5rem;
    color: #f47a1f;
    flex-shrink: 0;
    width: 24px;
    text-align: center;
}

.success-info-text {
    font-size: 0.95rem;
    color: #334155;
    line-height: 1.4;
    font-weight: 500;
}

.success-info-text strong {
    color: #0f172a;
    font-weight: 700;
}

/* Animation icône */
@keyframes success-pop {
  0% {
    transform: scale(0) rotate(-45deg);
    opacity: 0;
  }
  100% {
    transform: scale(1) rotate(-10deg);
    opacity: 1;
  }
}

/* Mobile */
@media (max-width: 480px) {
  .success-card {
    padding: 2rem 1.5rem;
    border-radius: 24px;
  }
  .success-card h2 {
    font-size: 1.75rem;
  }
}



</style>

<main class="bg-light">
    <!-- HERO -->
    <section class="hero">
        <div class="container hero-content py-5" data-animate>
            <h1 class="display-5 fw-bold mb-2"><?php echo get_phrase("Create_a_community") ?></h1>
            <p class="lead mb-4 text-white fs-md-4 fs-lg-3" style="letter-spacing: 1px; font-size: 1.5rem; margin-bottom: 1rem;"><?php echo get_phrase("Start_with_your_profile") ?></p>
        </div>
    </section>
    <section class="">
        <div class="container py-5 mb-5">
            <!-- Stepper -->
            <ol class="stepper" role="list" aria-label="<?php echo get_phrase('Steps'); ?>">
                <li class="step-create-commaunaute is-active" data-stepnav="1"><span class="num">1</span><span class="lbl"><?php echo get_phrase("Profile") ?></span></li>
                <li class="step-create-commaunaute" data-stepnav="2"><span class="num">2</span><span class="lbl"><?php echo get_phrase("Community") ?></span></li>
                <li class="step-create-commaunaute" data-stepnav="3"><span class="num">3</span><span class="lbl"><?php echo get_phrase("Price") ?></span></li>
                <li class="step-create-commaunaute" data-stepnav="4"><span class="num">4</span><span class="lbl"><?php echo get_phrase("subscription") ?></span></li>
                <li class="step-create-commaunaute" data-stepnav="5"><span class="num">5</span><span class="lbl"><?php echo get_phrase("Summary") ?></span></li>
            </ol>

            <form action="<?php echo site_url('admission/online_admission/submit/school'); ?>" method="post" id="schoolform"
                  class="js-validate studentform realtime-form container" enctype="multipart/form-data" novalidate>
                <!-- Champ caché pour le jeton CSRF -->
                <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>" />

                <!-- STEP 1 : PROFIL -->
                <section class="card panel step-pane is-visible" data-step="1" aria-labelledby="title-step1">
                    <div class="panel-head">
                        <h2 id="title-step1"><?php echo get_phrase("Profile_creation") ?></h2>
                        <span class="legend-required"><span class="req">*</span> <?php echo get_phrase("Required_fields") ?></span>
                    </div>

                    <div class="grid-2 mt-4">
                        <label class="field">
                            <span class="field-label"><?php echo get_phrase("Name") ?> <span class="req">*</span></span>
                            <input id="profileName" type="text" placeholder="<?php echo get_phrase('Name'); ?>"
                                   class="form-control shadow-none rounded-end text-capitalize" name="name" required
                                   data-msg="<?php echo get_phrase("Please enter your name") ?>" data-error-class="u-has-error"
                                   data-success-class="u-has-success" aria-required="true" autocomplete="name">
                            <div class="error" data-for="profileName"></div>
                        </label>

                        <label class="field">
                            <span class="field-label"><?php echo get_phrase("Email") ?> <span class="req">*</span></span>
                            <input id="profileEmail" type="email" placeholder="<?php echo get_phrase('email'); ?>"
                                   class="form-control rounded-end shadow-none" name="email" required aria-required="true" autocomplete="email"
                                   data-msg="<?php echo get_phrase("Please enter a valid email address") ?>" data-error-class="u-has-error"
                                   data-success-class="u-has-success">
                            <div class="error" data-for="profileEmail"></div>
                        </label>
                    </div>

                    <div class="grid-2">
                        <div class="field">
                            <span class="field-label"><?php echo get_phrase("Phone") ?> <span class="req">*</span></span>
                            <div class="unified-phone-wrapper">
                                <div class="country-select-wrapper">
                                   <?php include 'partials/countrySelect.php'; ?>
                                </div>
                                <input id="profilePhone" type="tel"
                                       class="form-control shadow-none" name="phone"
                                       data-msg="<?php echo get_phrase("Please enter a valid phone number") ?>"
                                       data-error-class="u-has-error" data-success-class="u-has-success"
                                       required aria-required="true" autocomplete="tel">
                            </div>
                            <div class="error" data-for="profilePhone"></div>
                        </div>
                        <label class="field">
                            <span class="field-label"><?php echo get_phrase("Primary_language") ?> <span class="req">*</span></span>
                            <select id="communityLang" name="communityLang" required aria-required="true"
                                    data-msg="<?php echo get_phrase('Please_select_a_language'); ?>">
                                <option value="french"><?php echo get_phrase("French_(FR)") ?></option>
                                <option value="english"><?php echo get_phrase("Anglais_(EN)") ?></option>
                                <option value="deutsch"><?php echo get_phrase("Allemand_(DE)") ?></option>
                                <option value="arabe"><?php echo get_phrase("Arabic_(AR)") ?></option>
                                <option value="spanish"><?php echo get_phrase("Spanish_(ES)") ?></option>
                            </select>
                            <div class="error" data-for="communityLang"></div>
                        </label>
                    </div>

                    <label class="field">
                        <span class="field-label"><?php echo get_phrase("Password") ?> <span class="req">*</span></span>
                        <input id="profilePass" type="password" placeholder="********" required minlength="8" pattern=".{8,}"
                               aria-required="true" autocomplete="new-password"
                               class="form-control rounded-end shadow-none" name="password"
                               data-msg="<?php echo get_phrase("Please enter a password with at least 8 characters") ?>"
                               data-error-class="u-has-error" data-success-class="u-has-success">
                        <div class="error" data-for="profilePass"></div>
                    </label>

                    <label class="field">
                        <span class="field-label"><?php echo get_phrase("Confirm_password") ?> <span class="req">*</span></span>
                        <input id="profilePass2" type="password" placeholder="********" required minlength="8" pattern=".{8,}"
                               aria-required="true" autocomplete="new-password" class="form-control rounded-end shadow-none"
                               name="repeat-password"
                               data-msg="<?php echo get_phrase("Please repeat your password (min. 8 characters)") ?>"
                               data-error-class="u-has-error" data-success-class="u-has-success">
                        <div class="error" data-for="profilePass2"></div>
                    </label>

                    <div class="panel-actions">
                        <button type="button" class="btn btn-secondary prev" disabled><?php echo get_phrase("Back") ?></button>
                        <button type="button" class="btn btn-primary next" disabled><?php echo get_phrase("Continue") ?></button>
                    </div>
                </section>

                <!-- STEP 2 : COMMUNAUTÉ -->
                <section class="card panel step-pane panel--community" data-step="2" aria-labelledby="title-step2">
                    <div class="panel-head">
                        <h2 id="title-step2"><?php echo get_phrase("Community_details") ?></h2>
                        <span class="legend-required"><span class="req">*</span> <?php echo get_phrase("Required_fields") ?></span>
                    </div>
                    <div class="grid mt-4">
                        <!-- Colonne gauche -->
                        <div class="stack">

                            <!-- Identité -->
                            <fieldset>
                                <legend><?php echo get_phrase("Identity"); ?></legend>

                                <label class="field">
                                    <span class="field-label"><?php echo get_phrase("I_am") ?> <span class="req">*</span></span>
                                    <select id="i_am_id" name="i_am" class="form-control shadow-none" required
                                            data-msg="<?php echo get_phrase("Please select your status") ?>">
                                        <option value=""><?php echo get_phrase('select_a_status'); ?></option>
                                        <option value="Entreprise"><?php echo get_phrase("Entreprise") ?></option>
                                        <option value="Freelancer"><?php echo get_phrase("Freelancer") ?></option>
                                        <option value="Autoentrepreneur"><?php echo get_phrase("Autoentrepreneur") ?></option>
                                        <option value="Particulier"><?php echo get_phrase("Particulier") ?></option>
                                    </select>
                                    <div class="error" data-for="i_am"></div>
                                </label>

                                <div class="grid-2">
                                    <label class="field">
                                        <span class="field-label"><?php echo get_phrase("Tax_residence") ?> <span class="req">*</span></span>
                                        <select id="Tax_residence" name="Tax_residence" class="form-control shadow-none" required
                                                data-msg="<?php echo get_phrase("Please select your tax residence") ?>">
                                            <option value=""><?php echo get_phrase('select_a_Tax_residence'); ?></option>
                                            <option value="MA"><?php echo get_phrase("Morocco") ?></option>
                                            <option value="UAE"><?php echo get_phrase("United_Arab_Emirates") ?></option>
                                        </select>
                                        <div class="error" data-for="Tax_residence"></div>
                                    </label>

                                    <label class="field">
                                        <span class="field-label"><?php echo get_phrase("Category") ?> <span class="req">*</span></span>
                                        <select id="communityCat" name="category" class="form-control shadow-none" required
                                                data-msg="<?php echo get_phrase("please_select_a_category"); ?>">
                                            <option value=""><?php echo get_phrase('select_a_category'); ?></option>
                                            <?php $categories = $this->db->get_where('categories', array())->result_array(); ?>
                                            <?php foreach ($categories as $categorie): ?>
                                                <option value="<?php echo $categorie['name']; ?>"><?php echo get_phrase($categorie['name']); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="error" data-for="communityCat"></div>
                                    </label>
                                </div>
                            </fieldset>

                            <!-- Contenu -->
                            <fieldset>
                                <legend><?php echo get_phrase("Content"); ?></legend>

                                <label class="field">
                                    <span class="field-label"><?php echo get_phrase("Community_name") ?> <span class="req">*</span></span>
                                    <input id="communityName" type="text" maxlength="80"
                                           placeholder="<?php echo get_phrase("Ex._Digital_Marketing") ?>"
                                           class="form-control shadow-none rounded-end text-capitalize"
                                           name="school_name" required
                                           data-msg="<?php echo get_phrase("Please enter your first name") ?>">
                                    <small class="help"><?php echo get_phrase("Max._80_characters") ?></small>
                                    <div class="error" data-for="communityName"></div>
                                </label>

                                <label class="field">
                                    <span class="field-label">
                                        <?php echo get_phrase("Description") ?> <span class="req">*</span>
                                        <span class="info" data-tooltip="<?php echo get_phrase("Goal_in_one_sentence_•_2)_For_whom_•_3)_Benefits_(3–5)_•_4)_Pace/rules._Min._40_characters.") ?>">
                                            <i class="fa-solid fa-circle-info"></i>
                                        </span>
                                    </span>
                                    <textarea id="communityDesc" class="form-control shadow-none" rows="6" name="school_description"
                                              placeholder="<?php echo get_phrase("Describe_the_goal_and_the_value…") ?>" required
                                              data-msg="<?php echo get_phrase("Please enter a description") ?>"></textarea>
                                    <small class="help"><?php echo get_phrase("At_least_40_characters.") ?></small>
                                    <div class="error" data-for="communityDesc"></div>
                                </label>
                            </fieldset>

                            <!-- Visibilité -->
                            <label class="switch emph">
                                <input type="hidden" name="visibility" value="0">
                                <input id="isPrivate_id" name="visibility" type="checkbox" value="1" />
                                <span class="switch-emph">
                                    <i class="fa-solid fa-lock"></i>
                                    <strong><?php echo get_phrase("Private_community") ?></strong>
                                    <em><?php echo get_phrase("(access_upon_approval)") ?></em>
                                    <span class="info field-label"
                                          data-tooltip="<?php echo get_phrase("(A_private_community_means_that_access_is_not_open._Interested_people_will_need_to_send_a_request_for_access._The_administrator_can_accept_or_reject_it.)") ?>">
                                        <i class="fa-solid fa-circle-info"></i>
                                    </span>
                                </span>
                            </label>
                        </div>

                        <!-- Colonne droite -->
                        <div class="stack">
                            <fieldset>
                                <legend><?php echo get_phrase("Media"); ?></legend>

                                <!-- Logo -->
                                <span class="field-label">
                                    <?php echo get_phrase("Logo (1:1)") ?>
                                    <span class="info"
                                          data-tooltip="<?php echo get_phrase("Optimal_size:_512×512_px_(1:1)_•_PNG/JPG_•_transparent_background_recommended_•_&lt;_1_Mo") ?>">
                                        <i class="fa-solid fa-circle-info"></i>
                                    </span>
                                </span>
                                <div class="uploader" data-kind="logo">
                                    <input id="communityLogo" type="file" name="school_image" accept="image/*">
                                    <div class="uploader-content">
                                        <i class="fa-solid fa-upload"></i>
                                        <p><?php echo get_phrase("Upload_or_drag_a_logo") ?></p>
                                    </div>
                                    <div class="preview" id="logoPreview"></div>
                                    <div class="error" data-for="communityLogo"></div>
                                </div>

                                <!-- Cover -->
                                <span class="field-label">
                                    <?php echo get_phrase("Cover_(16:9)") ?>
                                    <span class="info"
                                          data-tooltip="<?php echo get_phrase("Optimal_size:_1600×900_px_•_PNG/JPG_•_transparent_background_not_recommended_•_&lt;_2_Mo.") ?>">
                                        <i class="fa-solid fa-circle-info"></i>
                                    </span>
                                </span>
                                <div class="uploader" data-kind="cover">
                                    <input type="file" id="communityCover" name="communityCover" accept="image/*">
                                    <div class="uploader-content">
                                        <i class="fa-solid fa-image"></i>
                                        <p><?php echo get_phrase("Upload_or_drag_a_cover_photo") ?></p>
                                    </div>
                                    <div class="preview" id="coverPreview"></div>
                                    <div class="error" data-for="communityCover"></div>
                                </div>
                            </fieldset>

                            <!-- Adresse -->
                            <fieldset class="address-zone">
                                <legend><?php echo get_phrase("Address"); ?></legend>

                                <div class="grid-2" style="grid-template-columns: 66% 30%; gap: 14px;">
                                    <label class="field">
                                        <span class="field-label"><?php echo get_phrase("Rue") ?> <span class="req">*</span></span>
                                        <input id="communityStreet" type="text" placeholder="<?php echo get_phrase("Rue") ?>"
                                               class="form-control shadow-none" name="street" required
                                               data-msg="<?php echo get_phrase("Veuillez entrer la rue") ?>">
                                        <div class="error" data-for="communityStreet"></div>
                                    </label>

                                    <label class="field">
                                        <span class="field-label"><?php echo get_phrase("Numéro") ?> <span class="req">*</span></span>
                                        <input id="communityNumber" type="text" placeholder="<?php echo get_phrase("Numéro") ?>"
                                               class="form-control shadow-none" name="number" required
                                               data-msg="<?php echo get_phrase("Veuillez entrer le numéro") ?>">
                                        <div class="error" data-for="communityNumber"></div>
                                    </label>
                                </div>

                                <div class="grid-2" style="grid-template-columns: 50% 50%; gap: 14px;">
                                    <label class="field">
                                        <span class="field-label"><?php echo get_phrase("Ville") ?> <span class="req">*</span></span>
                                        <input id="communityCity" type="text" placeholder="<?php echo get_phrase("Ville") ?>"
                                               class="form-control shadow-none" name="city" required
                                               data-msg="<?php echo get_phrase("Veuillez entrer la ville") ?>">
                                        <div class="error" data-for="communityCity"></div>
                                    </label>

                                    <label class="field">
                                        <span class="field-label"><?php echo get_phrase("code_postal") ?> <span class="req">*</span></span>
                                        <input id="communityPostalCode" type="text" placeholder="<?php echo get_phrase("code_postal") ?>"
                                               class="form-control shadow-none" name="postal_code" required
                                               data-msg="<?php echo get_phrase("Veuillez entrer le postal code") ?>">
                                        <div class="error" data-for="communityPostalCode"></div>
                                    </label>
                                </div>
                            </fieldset>
                        </div>
                    </div>

                    <div class="panel-actions">
                        <button type="button" class="btn btn-secondary prev"><?php echo get_phrase("Back") ?></button>
                        <button type="button" class="btn btn-primary next" disabled><?php echo get_phrase("Continue") ?></button>
                    </div>
                </section>

                <!-- STEP 3 : PRIX -->
                <section class="card panel step-pane" data-step="3" aria-labelledby="title-step3">
                    <div class="panel-head">
                        <h2 id="title-step3"><?php echo get_phrase("Pricing_and_access") ?></h2>
                    </div>

                    <div class="grid-3 price-grid">
                        <div class="grid-2" id="priceFieldWrapper">
                            <label class="field">
                                <span class="field-label"><?php echo get_phrase("price") ?> <span class="req">*</span></span>
                                <input id="communityprice" type="text"
                                       placeholder="<?php echo get_phrase("price") ?>"
                                       class="form-control shadow-none"
                                       name="price"
                                       required
                                       data-msg="<?php echo get_phrase("Please enter the price") ?>"
                                       data-error-class="u-has-error"
                                       data-success-class="u-has-success">
                                <div class="error" data-for="communityprice"></div>
                                <input type="hidden" name="currency" id="currencyCode" value="MAD">
                            </label>
                        </div>

                        <div id="particulierNotice_id" class="alert alert-warning" style="display:none; margin-bottom:12px;">
                            <?php echo get_phrase("As_you_are_a_private_individual_you_are_not_allowed_to_set_a_price_It_is_automatically_set_to_0"); ?>
                        </div>
                    </div>

                    <div class="panel-actions">
                        <button type="button" class="btn btn-secondary prev"><?php echo get_phrase("Back") ?></button>
                        <button type="button" class="btn btn-primary next" disabled><?php echo get_phrase("Continue") ?></button>
                    </div>
                </section>

                <!-- STEP 4 : SUBSCRIPTION -->
                <section class="card panel step-pane" data-step="4" aria-labelledby="title-step4">
                    <div class="panel-head">
                        <h2 id="title-step4"><?php echo get_phrase("subscription") ?></h2>
                    </div>

                    <div class="grid-2 price-grid">
                        <label class="radio-tile">
                            <input type="radio" name="communityPriceType" value="oneoff" checked>
                            <div class="tile" data-coming="true">
                                <i class="fa-solid fa-hand-holding-dollar"></i>
                                <strong>
                                    <?php echo get_phrase("price_to_pay_790") ?>
                                    <small id="currencyHint" class="help">MAD</small>
                                </strong>
                                <span><?php echo get_phrase("You are entitled to a 14-day free trial") ?></span>
                            </div>
                        </label>
                    </div>

                    <div class="panel-actions">
                        <button type="button" class="btn btn-secondary prev"><?php echo get_phrase("Back") ?></button>
                        <button type="button" class="btn btn-primary next" disabled><?php echo get_phrase("Continue") ?></button>
                    </div>
                </section>

                <!-- STEP 5 : RÉSUMÉ -->
                <section class="card panel step-pane" data-step="5" aria-labelledby="title-step5">
                    <div class="panel-head">
                        <h2 id="title-step5"><?php echo get_phrase("Summary_&_publishing") ?></h2>
                    </div>

                    <div class="summary">
                        <p><?php echo get_phrase("Check_your_information_and_click_on") ?> <strong><?php echo get_phrase("Publish") ?></strong>.</p>
                    </div>

                    <div class="panel-actions">
                        <button type="button" class="btn btn-secondary prev"><?php echo get_phrase("Back") ?></button>
                        <button type="submit" id="submitBtnSchool"
                                class="btn btn btn-primary text-uppercase submit-button"><?php echo get_phrase('Submit'); ?></button>
                        <button type="reset" id="resetBtn" style="display: none;"></button>
                    </div>
                </section>
            </form>
        </div>
    </section>
    <!-- Overlay -->
    <?php include 'partials/onboarding_community.php'; ?>
    
    <!--success overlay-->
<div id="successOverlay" class="success-overlay" aria-hidden="true">
        <div class="success-card">
            <div class="success-icon">
                <i class="fa-solid fa-paper-plane"></i>
            </div>
            <h2><?php echo get_phrase("Request_sent") ?>!</h2>
            <p><?php echo get_phrase("Your_profile_and_your_community_have_been_successfully_created_on_Wayo.") ?></p>

            <div class="success-info-box">
                <div class="success-info-icon">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                </div>
                <div class="success-info-text">
                    <?php echo get_phrase("You_will_receive_a_validation_email_within_a_maximum_of") ?> <strong><?php echo get_phrase("24 hours.") ?></strong> <?php echo get_phrase("to confirm your registration.") ?>
                </div>
            </div>

            <button id="successBtn" type="button" class="btn btn-primary-custom w-100"><?php echo get_phrase("I_understand") ?></button>
        </div>
    </div>
    

</main>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

<!-- Aperçu simple via jQuery (tu peux le garder si tu veux un preview rapide) -->

<script>
    $(document).ready(function() {
        function setupUploader(inputId, previewId) {
            const input = $(inputId);
            const preview = $(previewId);

            if (!input.length || !preview.length) return;

            input.on('change', function() {
                const file = this.files[0];
                if (!file) return;

                if (!/^image\//.test(file.type)) {
                    preview.html('<p style="color:red;">Le fichier sélectionné n\'est pas une image.</p>');
                    input.val('');
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(ev) {
                    preview.html(`<img src="${ev.target.result}" alt="Preview" style="max-width:100%; border-radius:8px;">`);
                };
                reader.readAsDataURL(file);
            });
        }

        setupUploader('#communityLogo', '#logoPreview');
        setupUploader('#communityCover', '#coverPreview');
    });
</script>

<!-- Script principal unifié & optimisé -->
<script>
    
document.addEventListener('DOMContentLoaded', function() {
    // ========================
    // Helpers
    // ========================
    const $  = (s, ctx = document) => ctx.querySelector(s);
    const $$ = (s, ctx = document) => Array.from(ctx.querySelectorAll(s));

    if (window.toastr) {
        toastr.options = {
            closeButton: true,
            progressBar: true,
            positionClass: 'toast-top-right',
            timeOut: 5000
        };
    }

    const form            = $('#schoolform');
    const panes           = $$('.step-pane');
    const steps           = $$('.step-create-commaunaute');
    const iAmSelect       = $('#i_am_id');
    const taxResSelect    = $('#Tax_residence');
    const priceWrapper    = $('#priceFieldWrapper');
    const priceInput      = $('#communityprice');
    const monetizationNotice = $('#particulierNotice_id');
    const currencyHint    = $('#currencyHint');
    const currencyCodeEl  = $('#currencyCode');
    const logoInput       = $('#communityLogo');
    const coverInput      = $('#communityCover');
    const logoPreview     = $('#logoPreview');
    const coverPreview    = $('#coverPreview');
    const privateToggle   = $('#isPrivate_id');
    const noticeMessages  = {
        particulier: <?php echo json_encode(get_phrase("As_you_are_a_private_individual_you_are_not_allowed_to_set_a_price_It_is_automatically_set_to_0")); ?>,
        private: <?php echo json_encode(get_phrase("You_cannot_monetize_a_private_community")); ?>
    };

    let currentStep = 0;
    let lastEmail   = '';
    let lastSchool  = '';

    // ========================
    // Config images
    // ========================
    const CONFIG = {
        logo: {
            maxMB: 1,
            ratio: 1,
            maxWidth: 512,
            maxHeight: 512,
            tolerance: 0.03
        },
        cover: {
            maxMB: 2,
            ratio: 16 / 9,
            maxWidth: 1600,
            maxHeight: 900,
            tolerance: 0.12
        }
    };

    async function validateImage(file, type) {
        if (!file) return { valid: true };

        const c = CONFIG[type];

        if (file.size > c.maxMB * 1024 * 1024) {
            return {
                valid: false,
                msg: type === 'logo'
                    ? 'Logo trop lourd, max 1 Mo'
                    : 'Cover trop lourde, max 2 Mo'
            };
        }

        return new Promise(resolve => {
            const img = new Image();
            img.onload = () => {
                const width  = img.width;
                const height = img.height;
                const ratio  = width / height;

                if (type === 'logo') {
                    if (Math.abs(ratio - c.ratio) > c.tolerance) {
                        return resolve({ valid: false, msg: 'Le logo doit être carré (1:1)' });
                    }
                    if (width > c.maxWidth || height > c.maxHeight) {
                        return resolve({ valid: false, msg: 'Le logo ne doit pas dépasser 512×512 px' });
                    }
                }

                if (type === 'cover') {
                    if (width > c.maxWidth || height > c.maxHeight) {
                        return resolve({ valid: false, msg: 'La cover ne doit pas dépasser 1600×900 px' });
                    }
                    if (Math.abs(ratio - c.ratio) > c.tolerance) {
                        return resolve({ valid: false, msg: 'La cover doit être environ 16:9' });
                    }
                }

                resolve({ valid: true });
            };
            img.onerror = () => resolve({ valid: false, msg: 'Image corrompue' });
            img.src = URL.createObjectURL(file);
        });
    }

    function showImageError(previewIdOrEl, msg) {
        let el = previewIdOrEl;
        if (typeof previewIdOrEl === 'string') {
            el = document.getElementById(previewIdOrEl);
        }
        if (!el) return;
        el.innerHTML = `<div class="text-danger small p-3 text-center bg-light border rounded">${msg}</div>`;
    }

    // ========================
    // Duplication email / nom communauté
    // ========================
    function checkDuplication(type, value, input) {
        if (!value.trim()) return;
        if (type === 'email'       && value === lastEmail)  return;
        if (type === 'school_name' && value === lastSchool) return;

        if (type === 'email')       lastEmail  = value;
        if (type === 'school_name') lastSchool = value;

        const formData = new FormData();
        formData.append('type',  type);
        formData.append('value', value);
        formData.append('<?= $this->security->get_csrf_token_name(); ?>', '<?= $this->security->get_csrf_hash(); ?>');

        fetch('<?= site_url('admission/check_duplication_ajax'); ?>', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            const errorEl = $(`.error[data-for="${input.id}"]`);
            if (!data.available) {
                if (errorEl) {
                    errorEl.textContent = data.message;
                    errorEl.style.display = 'block';
                }
                input.classList.add('is-invalid');
                input.setAttribute('data-duplicate', 'true');
            } else {
                if (errorEl) errorEl.style.display = 'none';
                input.classList.remove('is-invalid');
                input.removeAttribute('data-duplicate');
            }
            updateContinueButton();
        });
    }

    $('#profileEmail')?.addEventListener('blur', () => {
        checkDuplication('email', $('#profileEmail').value, $('#profileEmail'));
    });
    $('#communityName')?.addEventListener('blur', () => {
        checkDuplication('school_name', $('#communityName').value, $('#communityName'));
    });

    // ========================
    // Prix / Particulier
    // ========================
    function applyPriceRules() {
        if (!priceWrapper || !priceInput) return;
        const isParticulier      = iAmSelect?.value === 'Particulier';
        const isPrivateCommunity = privateToggle?.checked;
        const shouldDisablePrice = isParticulier || isPrivateCommunity;
        priceWrapper.style.display = 'block';

        if (shouldDisablePrice) {
            priceInput.value = '0';
            priceInput.disabled = true;
            priceInput.classList.add('bg-light');
            clearInvalid(priceInput);
            if (monetizationNotice) {
                let text = '';
                if (isParticulier) {
                    text = noticeMessages.particulier;
                } else if (isPrivateCommunity) {
                    text = noticeMessages.private;
                }
                monetizationNotice.style.display = text ? 'block' : 'none';
                monetizationNotice.textContent   = text;
            }
        } else {
            priceInput.disabled = false;
            priceInput.classList.remove('bg-light');
            if (monetizationNotice) {
                monetizationNotice.style.display = 'none';
                monetizationNotice.textContent = '';
            }
            if (priceInput.value === '0') priceInput.value = '';
        }
    }

    iAmSelect?.addEventListener('change', () => {
        applyPriceRules();
        updateContinueButton();
    });

    privateToggle?.addEventListener('change', () => {
        applyPriceRules();
        updateContinueButton();
        updateSummary();
    });

    // ========================
    // Currency (Tax residence)
    // ========================
    function updateCurrencyUI() {
        if (!taxResSelect) return;
        const sel  = taxResSelect.value || '';
        const code = sel === 'UAE' ? 'AED' : sel === 'EUR' ? 'EUR' : 'MAD';

        if (currencyHint)   currencyHint.textContent = code;
        if (currencyCodeEl) currencyCodeEl.value     = code;

        if (priceInput && !priceInput.disabled) {
            priceInput.placeholder = `<?php echo get_phrase("Price"); ?> (${code})`;
        }
    }

    taxResSelect?.addEventListener('change', () => {
        updateCurrencyUI();
        updateContinueButton();
    });

    priceInput?.addEventListener('input', updateContinueButton);

    // ========================
    // Validation de base des champs required
    // ========================
    function setInvalid(el, msg) {
        if (!el) return;
        el.classList.add('is-invalid');
        // Handle phone wrapper
        if (el.id === 'profilePhone') {
            el.closest('.unified-phone-wrapper')?.classList.add('is-invalid');
        }
        const err = $(`.error[data-for="${el.id}"]`);
        if (err) {
            err.textContent = msg || el.dataset.msg || '';
            err.style.display = 'block';
        }
    }

    function clearInvalid(el) {
        if (!el) return;
        el.classList.remove('is-invalid');
         // Handle phone wrapper
        if (el.id === 'profilePhone') {
            el.closest('.unified-phone-wrapper')?.classList.remove('is-invalid');
        }
        const err = $(`.error[data-for="${el.id}"]`);
        if (err) {
            err.textContent = '';
            err.style.display = 'none';
        }
    }

    $$('input[required], select[required], textarea[required]').forEach(el => {
        el.addEventListener('blur', () => {
            const isEmpty = !el.value.trim();
            if (isEmpty) {
                setInvalid(el);
            } else {
                clearInvalid(el);
            }
            updateContinueButton();
        });
        if (el.tagName === 'INPUT' || el.tagName === 'TEXTAREA') {
            el.addEventListener('input', () => {
                if (el.classList.contains('is-invalid') && el.value.trim()) {
                    clearInvalid(el);
                }
                updateContinueButton();
            });
        }
    });

    // ========================
    // Validation step par step
    // ========================
    function validateStep(index) {
        const pane = panes[index];
        if (!pane) return true;

        let ok = true;

        // Blocage si doublons
        if ($$('[data-duplicate="true"]').length > 0) {
            toastr?.warning('<?= get_phrase("please_correct_duplicate_fields"); ?>');
            return false;
        }

        // Required
        pane.querySelectorAll('[required]').forEach(field => {
            if (!field.value.trim()) {
                setInvalid(field);
                ok = false;
            }
        });

        const stepId = pane.dataset.step;

        // Step 1
        if (stepId === '1') {
            const name  = $('#profileName');
            const email = $('#profileEmail');
            const phone = $('#profilePhone');
            const pass  = $('#profilePass');
            const pass2 = $('#profilePass2');

            if (!name.value.trim() || name.value.trim().length < 2) {
                setInvalid(name); ok = false;
            }

            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value.trim())) {
                setInvalid(email); ok = false;
            }

            if ((phone.value || '').replace(/\D/g, '').length < 8) {
                setInvalid(phone); ok = false;
            }

            if ((pass.value || '').length < 8) {
                setInvalid(pass); ok = false;
            }

            if (pass.value !== pass2.value) {
                setInvalid(pass2); ok = false;
            }
        }

        // Step 2
        if (stepId === '2') {
            const desc   = $('#communityDesc');
            const number = $('#communityNumber');
            const postal = $('#communityPostalCode');

            if (desc.value.trim().length < 40) {
                setInvalid(desc); ok = false;
            }

            if (!/^\d+$/.test(number.value.trim())) {
                setInvalid(number); ok = false;
            }

            if (!/^\d{4,5}$/.test(postal.value.trim())) {
                setInvalid(postal); ok = false;
            }

            // Images
            const logoFile  = logoInput?.files[0];
            const coverFile = coverInput?.files[0];

            if (logoFile && logoInput.dataset.valid !== 'true') {
                ok = false;
                showImageError(logoPreview, '<?php echo get_phrase("Invalid_logo_size_or_ratio"); ?>');
                toastr?.error('<?php echo get_phrase("Please_upload_a_valid_logo_before_continuing"); ?>');
            }

            if (coverFile && coverInput.dataset.valid !== 'true') {
                ok = false;
                showImageError(coverPreview, '<?php echo get_phrase("Invalid_cover_size_or_ratio"); ?>');
                toastr?.error('<?php echo get_phrase("Please_upload_a_valid_cover_before_continuing"); ?>');
            }
        }

        // Step 3 : prix
        if (stepId === '3') {
            console.log(iAmSelect.value);
            const iAm = iAmSelect?.value || '';
            if (iAm !== 'Particulier' && priceInput && !priceInput.disabled) {
                const val = (priceInput.value || '').trim();
                if (val === '' || isNaN(val) || Number(val) < 0) {
                    setInvalid(priceInput, '<?= get_phrase("Please enter a valid price"); ?>');
                    ok = false;
                } else {
                    clearInvalid(priceInput);
                }
            }
        }

        if (!ok) {
            toastr?.warning('<?= get_phrase("Please_correct_the_errors"); ?>');
        }

        return ok;
    }

    // ========================
    // Continue button
    // ========================
    function updateContinueButton() {
        const pane   = $('.step-pane.is-visible');
        if (!pane) return;
        const nextBtn = pane.querySelector('.next');
        if (!nextBtn) return;

        const hasDup        = $$('[data-duplicate="true"]').length > 0;
        const invalidInPane = pane.querySelectorAll('.is-invalid').length > 0;

        let imagesOk = true;
        if (pane.dataset.step === '2') {
            const logoFile  = logoInput?.files[0];
            const coverFile = coverInput?.files[0];

            if (logoFile  && logoInput.dataset.valid  !== 'true') imagesOk = false;
            if (coverFile && coverInput.dataset.valid !== 'true') imagesOk = false;
        }

        let priceOk = true;
        if (pane.dataset.step === '3') {
            const iAm = iAmSelect?.value || '';
            if (iAm !== 'Particulier' && priceInput && !priceInput.disabled) {
                const val = (priceInput.value || '').trim();
                priceOk = val !== '' && !isNaN(val) && Number(val) >= 0;
                priceInput.classList.toggle('is-invalid', !priceOk);
            }
        }

        const disabled = hasDup || invalidInPane || !imagesOk || !priceOk;
        nextBtn.disabled = disabled;
        nextBtn.classList.toggle('opacity-50', disabled);
    }

    window.updateContinueButton = updateContinueButton;

    // ========================
    // Gestion upload images avec validation
    // ========================
    ['logo', 'cover'].forEach(type => {
        const input   = type === 'logo' ? logoInput : coverInput;
        const preview = type === 'logo' ? logoPreview : coverPreview;
        if (!input || !preview) return;

        input.addEventListener('change', async () => {
            const file   = input.files[0];
            const result = file ? await validateImage(file, type) : { valid: true };

            if (result.valid) {
                input.dataset.valid = 'true';
                const reader = new FileReader();
                reader.onload = e => {
                    preview.innerHTML = `
                        <img src="${e.target.result}"
                             style="max-width:100%; border-radius:8px; ${type === 'cover' ? 'height:80px; object-fit:cover;' : ''}">
                    `;
                };
                reader.readAsDataURL(file);
            } else {
                input.removeAttribute('data-valid');
                input.value = '';
                showImageError(preview, result.msg);
            }
            updateContinueButton();
        });
    });

    // ========================
    // Stepper
    // ========================
    function goTo(index) {
        if (index < 0 || index >= panes.length) return;

        if (index > currentStep && !validateStep(currentStep)) return;

        panes.forEach(p => p.classList.remove('is-visible'));
        steps.forEach((s, idx) => {
            s.classList.toggle('is-active',   idx === index);
            s.classList.toggle('is-complete', idx < index);
        });

        panes[index].classList.add('is-visible');
        currentStep = index;

        updateContinueButton();
        updateSummary();
        if (typeof saveFormState === 'function') saveFormState();
        // window.scrollTo({ top: 0, behavior: 'smooth' });
    }
    window.goTo = goTo; // Expose for persistence manager

    $$('.next').forEach(btn => btn.addEventListener('click', () => {
        if (validateStep(currentStep)) goTo(currentStep + 1);
    }));

    $$('.prev').forEach(btn => btn.addEventListener('click', () => {
        if (currentStep > 0) goTo(currentStep - 1);
    }));

    steps.forEach((s, idx) => {
        s.style.cursor = 'pointer';
        s.addEventListener('click', () => {
            if (idx <= currentStep) goTo(idx);
        });
    });

    // ========================
    // Résumé
    // ========================
    function updateSummary() {
        const summaryDiv = $('.summary');
        if (!summaryDiv) return;

        const profileName   = $('#profileName')?.value || '—';
        const profileEmail  = $('#profileEmail')?.value || '—';
        const phone         = $('#profilePhone')?.value || '—';
        const lang          = $('#communityLang')?.value || '—';
        const i_am          = $('#i_am_id')?.value || '—';
        const communityName = $('#communityName')?.value || '—';
        const desc          = $('#communityDesc')?.value || '—';
        const cat           = $('#communityCat')?.value || '—';
        const street        = $('#communityStreet')?.value || '—';
        const number        = $('#communityNumber')?.value || '—';
        const city          = $('#communityCity')?.value || '—';
        const postal        = $('#communityPostalCode')?.value || '—';
        const tax           = $('#Tax_residence')?.value || '—';
        const currency      = $('#currencyCode')?.value || 'MAD';
        const priceVal      = $('#communityprice')?.value || '';
        const isPrivate     = privateToggle?.checked
            ? '<?php echo get_phrase("Private"); ?>'
            : '<?php echo get_phrase("Public"); ?>';

        const vat = tax === 'MA' ? '20%' : tax === 'UAE' ? '5%' : '—';

        const logoImg  = $('#logoPreview img');
        const coverImg = $('#coverPreview img');
        const logoHtml  = logoImg  ? `<img src="${logoImg.src}"  style="max-width:80px; border-radius:10px;">` : '—';
        const coverHtml = coverImg ? `<img src="${coverImg.src}" style="max-width:120px; border-radius:10px;">` : '—';

        const finalPrice = (i_am === 'Particulier' || privateToggle?.checked)
            ? `0 ${currency}`
            : (priceVal ? `${priceVal} ${currency}` : '—');

        summaryDiv.innerHTML = `
            <h3><?php echo get_phrase("Profile"); ?></h3>
            <p><?php echo get_phrase("Name"); ?>: ${profileName}<br>
               <?php echo get_phrase("Email"); ?>: ${profileEmail}<br>
               <?php echo get_phrase("Phone"); ?>: ${phone}<br>
               <?php echo get_phrase("Primary_language"); ?>: ${lang}</p>

            <h3><?php echo get_phrase("Community"); ?></h3>
            <p><?php echo get_phrase("I_am"); ?>: ${i_am}<br>
               <?php echo get_phrase("Community_name"); ?>: ${communityName}<br>
               <?php echo get_phrase("Description"); ?>: ${desc}<br>
               <?php echo get_phrase("Category"); ?>: ${cat}<br>
               <?php echo get_phrase("Address"); ?>: ${street}, ${number}, ${city}, ${postal}<br>
               <?php echo get_phrase("Country applicable for VAT"); ?>: ${tax}</p>

            <h3><?php echo get_phrase("Media"); ?></h3>
            <p><?php echo get_phrase("Logo"); ?>: ${logoHtml}<br>
               <?php echo get_phrase("Cover"); ?>: ${coverHtml}</p>

            <h3><?php echo get_phrase("Price"); ?></h3>
            <p><?php echo get_phrase("price"); ?>: ${finalPrice}<br>
               <?php echo get_phrase("VAT rate applied"); ?>: ${vat}</p>

            <h3><?php echo get_phrase("Visibility"); ?></h3>
            <p><?php echo get_phrase("Community"); ?>: ${isPrivate}</p>
        `;
    }

    window.updateSummary = updateSummary;

    // ========================
    // Submit AJAX
    // ========================
    form?.addEventListener('submit', function(e) {
        e.preventDefault();
        if (!validateStep(currentStep)) return;

        const formData = new FormData(this);
        fetch(this.action, {
            method: 'POST',
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            if (data.csrf) {
                const csrfInput = document.querySelector(`input[name="${data.csrf.csrfName}"]`);
                if (csrfInput) csrfInput.value = data.csrf.csrfHash;
            }

            if (data.status) {
                // toastr?.success(data.message);
                if (typeof clearSavedState === 'function') clearSavedState();
                $('#resetBtn')?.click();
                // Show success popup instead of auto-redirect
                const overlay = document.getElementById('successOverlay');
                if (overlay) overlay.classList.add('is-visible');
            } else {
                toastr?.error(data.message || 'Error');
            }
        })
        .catch(() => {
            toastr?.error('An error occurred.');
        });
    });
     // ========================
    // GESTION DU CODE PAYS CUSTOM ET AUTOMATIQUE
    // ========================
    const phoneInput = $('#profilePhone');
    const countryInput = $('#countrySelect'); // Hidden input
    const countryTrigger = $('#countryTrigger');
    const countryOptions = $('#countryOptions');
    const selectedFlag = $('#selectedFlag');
    const options = $$('.custom-option');

    let previousCode = countryInput ? countryInput.value : '+212'; 

    if (countryTrigger) {
        // Toggle Dropdown
        countryTrigger.addEventListener('click', (e) => {
            e.stopPropagation();
            countryOptions.classList.toggle('open');
        });

        // Close when clicking outside
        document.addEventListener('click', () => {
            countryOptions.classList.remove('open');
        });

        // Select Option Logic
        options.forEach(option => {
            option.addEventListener('click', function(e) {
                e.stopPropagation();
                const value = this.getAttribute('data-value');
                const flag = this.getAttribute('data-flag');

                // Update UI
                selectedFlag.className = `flag-icon flag-icon-${flag}`;
                countryInput.value = value;

                // Update selection state
                options.forEach(opt => opt.classList.remove('selected'));
                this.classList.add('selected');

                countryOptions.classList.remove('open');

                // Trigger logic to update phone input
                updatePhoneCode();
            });
        });
    }

    function updatePhoneCode() {
        if (!phoneInput || !countryInput) return;
        const newCode = countryInput.value;
        let currentVal = phoneInput.value;

        if (currentVal.startsWith(previousCode)) {
            phoneInput.value = newCode + currentVal.substring(previousCode.length);
        } 
        else if (!currentVal.trim() || !currentVal.startsWith('+')) {
            phoneInput.value = newCode;
        }
        else {
            phoneInput.value = newCode + currentVal.replace(/^\+\d+\s*/, '');
        }

        previousCode = newCode; 
        updateContinueButton();
    }

    // Init phone code
    if (phoneInput && countryInput) {
        if (!phoneInput.value.trim()) {
            phoneInput.value = countryInput.value;
        }
        previousCode = countryInput.value;

        const initialOpt = document.querySelector(`.custom-option[data-value="${countryInput.value}"]`);
        if(initialOpt && selectedFlag) {
           const flag = initialOpt.getAttribute('data-flag'); 
           selectedFlag.className = `flag-icon flag-icon-${flag}`;
        }
    }

    // Protection logic
    if (phoneInput && countryInput) {
        ['click', 'focus', 'keyup', 'keydown'].forEach(evt => {
            phoneInput.addEventListener(evt, (e) => {
                const code = countryInput.value;
                if (phoneInput.selectionStart < code.length) {
                    e.preventDefault();
                    phoneInput.setSelectionRange(code.length, code.length);
                }
            });
        });

        phoneInput.addEventListener('keydown', (e) => {
            const code = countryInput.value;
            if (e.key === 'Backspace' && phoneInput.selectionStart <= code.length) {
                 e.preventDefault();
            }
            if (e.key === 'Delete' && phoneInput.selectionStart < code.length) {
                 e.preventDefault();
            }
        });

        phoneInput.addEventListener('input', () => {
            const code = countryInput.value;
            if (!phoneInput.value.startsWith(code)) {
                 const raw = phoneInput.value.replace(code, '').replace(/^\+/, ''); 
                 phoneInput.value = code + raw;
            }
        });
    }

    // ========================
    // Init
    // ========================
    applyPriceRules();
    updateCurrencyUI();
    
    // Check if we should restore step or start at 0
    const saved = sessionStorage.getItem('wayo_admission_form_state');
    if (!saved) {
        goTo(0);
    }

    // Success button redirect
    const successBtn = document.getElementById('successBtn');
    if (successBtn) {
        successBtn.addEventListener('click', () => {
             location.href = '<?= site_url('/home/communities'); ?>';
        });
    }

    // Move success overlay to body to prevent z-index/clipping issues
    const successOverlay = document.getElementById('successOverlay');
    if (successOverlay) {
        document.body.appendChild(successOverlay);
    }
});
</script>

<!-- ==========================================
     PERSISTENCE MANAGER (Non-invasive)
     ========================================== -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const STORAGE_KEY = 'wayo_admission_form_state';
    const form = document.getElementById('schoolform');
    if (!form) return;

    window.saveFormState = function() {
        const formData = new FormData(form);
        const data = {};
        
        formData.forEach((value, key) => {
            // Exclude files and CSRF (Passwords included per user request)
            if (!(value instanceof File) && !key.includes('csrf')) {
                data[key] = value;
            }
        });

        // Save current step by reading from DOM
        const visiblePane = document.querySelector('.step-pane.is-visible');
        if (visiblePane) {
            data._step = parseInt(visiblePane.dataset.step) - 1;
        }

        // Custom phone/country sync
        const countryInput = document.getElementById('countrySelect');
        if (countryInput) data._country = countryInput.value;

        // Visual previews (Base64)
        const logoImg = document.querySelector('#logoPreview img');
        const coverImg = document.querySelector('#coverPreview img');
        if (logoImg) data._logoPre = logoImg.src;
        if (coverImg) data._coverPre = coverImg.src;

        sessionStorage.setItem(STORAGE_KEY, JSON.stringify(data));
    };

    window.restoreFormState = function() {
        const saved = sessionStorage.getItem(STORAGE_KEY);
        if (!saved) return;

        try {
            const data = JSON.parse(saved);
            
            // Restore inputs
            Object.keys(data).forEach(key => {
                if (key.startsWith('_')) return;
                const input = form.elements[key];
                if (input) {
                    if (input.type === 'checkbox') input.checked = !!data[key];
                    else if (input.type === 'radio') {
                        const r = form.querySelector(`input[name="${key}"][value="${data[key]}"]`);
                        if (r) r.checked = true;
                    } else {
                        input.value = data[key];
                    }
                }
            });

            // Restore Phone/Country UI
            if (data._country) {
                const ci = document.getElementById('countrySelect');
                const sf = document.getElementById('selectedFlag');
                if (ci) ci.value = data._country;
                const opt = document.querySelector(`.custom-option[data-value="${data._country}"]`);
                if (opt && sf) {
                    sf.className = `flag-icon flag-icon-${opt.getAttribute('data-flag')}`;
                    document.querySelectorAll('.custom-option').forEach(o => o.classList.remove('selected'));
                    opt.classList.add('selected');
                }
                // Update internal logic if exposed (optional but safer)
                if (typeof previousCode !== 'undefined') window.previousCode = data._country;
            }

            // Restore Previews
            if (data._logoPre) {
                const lp = document.getElementById('logoPreview');
                if (lp) {
                    lp.innerHTML = `<img src="${data._logoPre}" style="max-width:100%; border-radius:8px;">`;
                    const li = document.getElementById('communityLogo');
                    if (li) li.dataset.valid = 'true';
                }
            }
            if (data._coverPre) {
                const cp = document.getElementById('coverPreview');
                if (cp) {
                    cp.innerHTML = `<img src="${data._coverPre}" style="max-width:100%; border-radius:8px; height:80px; object-fit:cover;">`;
                    const ci = document.getElementById('communityCover');
                    if (ci) ci.dataset.valid = 'true';
                }
            }

            // Restore Step
            if (typeof data._step !== 'undefined' && typeof window.goTo === 'function') {
                setTimeout(() => window.goTo(data._step), 100);
            }

            // Trigger existing UI updates
            if (typeof applyPriceRules === 'function') applyPriceRules();
            if (typeof updateCurrencyUI === 'function') updateCurrencyUI();
            if (typeof updateSummary === 'function') updateSummary();
            if (typeof updateContinueButton === 'function') updateContinueButton();

        } catch (e) {
            console.error("Persistence error:", e);
        }
    };

    window.clearSavedState = function() {
        sessionStorage.removeItem(STORAGE_KEY);
    };

    // Auto-save triggers
    form.addEventListener('input', saveFormState);
    form.addEventListener('change', saveFormState);

    // Initial restoration
    restoreFormState();
});
</script>
