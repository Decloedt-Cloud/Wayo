<?php if (get_common_settings('recaptcha_status')): ?>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
<?php endif; ?>

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

    .btn-accent {
        background: var(--accent);
        color: #fff;
        box-shadow: 0 8px 20px rgba(252, 123, 48, .25)
    }

    .btn-accent:hover {
        background: var(--accent-600);
        color: #fff;
    }

    .btn-ghost {
        background: #ffffff14;
        color: #fff;
        border: 1px solid #ffffff40
    }

    .btn-ghost:hover {
        background: #ffffff26;
        color: #fff;
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

    /* #priceFieldWrapper { display: none; } */
    #communityprice.bg-light {
        background-color: #f8f9fa;
        cursor: not-allowed;
        color: #777;
    }

    /* #priceFieldWrapper { display: none; } */
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
</style>

<main>
    <!-- HERO -->
    <section class="hero">
        <div class="container hero-content py-5" data-animate>
            <h1 class="display-5 fw-bold mb-2"><?php echo get_phrase("Create_a_community") ?></h1>
            <p class="lead mb-4 text-white fs-md-4 fs-lg-3" style="letter-spacing: 1px; font-size: 1.5rem; margin-bottom: 1rem;"><?php echo get_phrase("Start_with_your_profile") ?></p>
        </div>
    </section>
    <section class="">
        <div class="container py">
            <!-- Stepper -->
            <ol class="stepper" role="list" aria-label="<?php echo get_phrase('Steps'); ?>">
                <li class="step-create-commaunaute is-active" data-stepnav="1"><span class="num">1</span><span class="lbl"><?php echo get_phrase("Profile") ?></span></li>
                <li class="step-create-commaunaute" data-stepnav="2"><span class="num">2</span><span class="lbl"><?php echo get_phrase("Community") ?></span></li>
                <li class="step-create-commaunaute" data-stepnav="3"><span class="num">3</span><span class="lbl"><?php echo get_phrase("Price") ?></span></li>
                <li class="step-create-commaunaute" data-stepnav="4"><span class="num">4</span><span class="lbl"><?php echo get_phrase("subscription") ?></span></li>
                <li class="step-create-commaunaute" data-stepnav="5"><span class="num">5</span><span class="lbl"><?php echo get_phrase("Summary") ?></span></li>
            </ol>
            <!-- <form  id="communityForm"  action="<?php echo site_url('admission/online_admission/submit/school'); ?>" method="post" id="schoolform"
      class="js-validate studentform realtime-form container" enctype="multipart/form-data"  novalidate> -->
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
                            <!-- <input id="profileName" type="text" placeholder="Votre nom" required aria-required="true" autocomplete="name"> -->
                            <input id="profileName" type="text" placeholder="<?php echo get_phrase('full_name'); ?>"
                                class="form-control shadow-none rounded-end text-capitalize" name="name" required
                                data-msg="<?php echo get_phrase("Please enter your full name") ?>" data-error-class="u-has-error"
                                data-success-class="u-has-success" aria-required="true" autocomplete="name">
                            <div class="error" data-for="profileName"></div>
                        </label>

                        <label class="field">
                            <span class="field-label"><?php echo get_phrase("Email") ?> <span class="req">*</span></span>
                            <!-- <input id="profileEmail" type="email" placeholder="exemple@email.com" required aria-required="true" autocomplete="email"> -->
                            <input id="profileEmail" type="email" placeholder="<?php echo get_phrase('email'); ?>"
                                class="form-control rounded-end shadow-none" name="email" required aria-required="true" autocomplete="email"
                                data-msg="<?php echo get_phrase("Please enter a valid email address") ?>" data-error-class="u-has-error"
                                data-success-class="u-has-success">
                            <div class="error" data-for="profileEmail"></div>
                        </label>
                    </div>

                    <div class="grid-2">
                        <label class="field">
                            <span class="field-label"><?php echo get_phrase("Phone") ?> <span class="req">*</span></span>
                            <!-- <input id="profilePhone" type="tel" placeholder="+212 600 00 00 00" required aria-required="true" autocomplete="tel"> -->
                            <input id="profilePhone" type="tel" pattern="\+?\d{1,3}\s?(\d{1,4}\s?){4}" placeholder="+212 600 00 00 00"
                                class="form-control rounded-end shadow-none" name="phone" data-msg="<?php echo get_phrase("Please enter a valid phone number") ?>"
                                data-error-class="u-has-error" data-success-class="u-has-success" required aria-required="true" autocomplete="tel">
                            <div class="error" data-for="profilePhone"></div>
                        </label>
                        <label class="field">
                            <span class="field-label"><?php echo get_phrase("Primary_language") ?> <span class="req">*</span></span>
                            <select id="communityLang" name="communityLang" required aria-required="true" data-msg="<?php echo get_phrase('Please_select_a_language'); ?>">
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
                        <!-- <input id="profilePass" type="password" placeholder="********" required minlength="6" aria-required="true" autocomplete="new-password"> -->
                        <input id="profilePass" type="password" placeholder="********" required minlength="6" aria-required="true" autocomplete="new-password" class="form-control rounded-end shadow-none" name="password" required
                            data-msg="<?php echo get_phrase("Please enter a password") ?>" data-error-class="u-has-error" data-success-class="u-has-success">
                        <div class="error" data-for="profilePass"></div>
                    </label>

                    <label class="field">
                        <span class="field-label"><?php echo get_phrase("Confirm_password") ?> <span class="req">*</span></span>
                        <!-- <input id="profilePass2" type="password" placeholder="********" required minlength="6" aria-required="true" autocomplete="new-password"> -->
                        <input id="profilePass2" type="password" placeholder="********" required minlength="6" aria-required="true" autocomplete="new-password" class="form-control rounded-end shadow-none"
                            name="repeat-password" required data-msg="<?php echo get_phrase("Please repeat your password") ?>" data-error-class="u-has-error"
                            data-success-class="u-has-success">
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
                        <!-- ====== Colonne gauche ====== -->
                        <div class="stack">

                            <!-- Identité -->
                            <fieldset>
                                <legend><?php echo get_phrase("Identity"); ?></legend>

                                <label class="field">
                                    <span class="field-label"><?php echo get_phrase("I_am") ?> <span class="req">*</span></span>
                                    <select id="i_am" name="i_am" class="form-control shadow-none" required
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
                                                <option value="<?php echo $categorie['name']; ?>"><?php echo $categorie['name']; ?></option>
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
                                        class="form-control shadow-none rounded-end text-capitalize" name="school_name" required
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
                        </div>

                        <!-- ====== Colonne droite ====== -->
                        <div class="stack">
                            <fieldset>
                                <legend><?php echo get_phrase("Media"); ?></legend>

                                <!-- Logo -->
                                <span class="field-label">
                                    <?php echo get_phrase("Logo (1:1)") ?>
                                    <span class="info" data-tooltip="<?php echo get_phrase("Optimal_size:_512×512_px_(1:1)_•_PNG/JPG_•_transparent_background_recommended_•_&lt;_1_Mo") ?>">
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
                                    <span class="info" data-tooltip="<?php echo get_phrase("Optimal_size:_1600×900_px_•_PNG/JPG_•_transparent_background_not_recommended_•_&lt;_2_Mo.") ?>">
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
                        <!-- Price (visible uniquement pour Particulier) -->
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
                                <!-- NEW: hint + hidden currency -->
                                <!-- <small id="currencyHint" class="help">Currency: MAD</small> -->
                                <input type="hidden" name="currency" id="currencyCode" value="MAD">
                            </label>
                        </div>
                        <!-- Notice: Particulier cannot set a price -->
                        <div id="particulierNotice" class="alert alert-warning" style="display:none; margin-bottom:12px;">
                            <?php echo get_phrase("As_you_are_a_private_individual_you_are_not_allowed_to_set_a_price_It_is_automatically_set_to_0"); ?>
                        </div>



                    </div>

                    <!-- <label class="field">
            <span class="field-label">Prix d’adhésion (€) <span class="req">*</span></span>
            <select id="price" required>
              <option value="0" selected>Gratuit</option>
            </select>
            <div class="error" data-for="price"></div>
          </label> -->

                    <div class="panel-actions">
                        <button type="button" class="btn btn-secondary prev"><?php echo get_phrase("Back") ?></button>
                        <button type="button" class="btn btn-primary next" disabled><?php echo get_phrase("Continue") ?></button>
                    </div>
                </section>
                <!-- STEP 4 : PRIX -->
                <section class="card panel step-pane" data-step="4" aria-labelledby="title-step3">
                    <div class="panel-head">
                        <h2 id="title-step4"><?php echo get_phrase("subscription") ?></h2>
                    </div>

                    <div class="grid-2 price-grid">
                        <!-- <label class="radio-tile">
              <input  type="radio" name="communityPriceType" value="free" >
              <div class="tile" data-price-tile="free">
                <i class="fa-solid fa-gift"></i>
                <strong><?php // echo get_phrase("Free") 
                        ?></strong>
                <span><?php // echo get_phrase("Free_access") 
                        ?></span>
              </div>
            </label> -->

                        <label class="radio-tile">
                            <input type="radio" name="communityPriceType" value="oneoff" checked>
                            <div class="tile" data-coming="true">
                                <i class="fa-solid fa-hand-holding-dollar"></i>
                                <strong><?php echo get_phrase("price_to_pay_790") ?> <small id="currencyHint" class="help">MAD</small></strong>
                                <span><?php echo get_phrase("You are entitled to a 14-day free trial") ?></span>
                            </div>
                        </label>


                        <!-- <label class="radio-tile">
              <input type="radio" name="communityPriceType" value="subscription" disabled>
              <div class="tile tile-disabled" data-coming="true">
                <i class="fa-solid fa-arrows-rotate"></i>
                <strong><?php //echo get_phrase("Subscription") 
                        ?></strong>
                <span><?php // echo get_phrase("Coming_soon") 
                        ?></span>
              </div>
            </label> -->
                    </div>

                    <!-- <label class="field">
            <span class="field-label">Prix d’adhésion (€) <span class="req">*</span></span>
            <select id="price" required>
              <option value="0" selected>Gratuit</option>
            </select>
            <div class="error" data-for="price"></div>
          </label> -->

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
                        <!-- <button type="submit" class="btn btn-success">Publier</button> -->
                        <button type="submit" id="submitBtnSchool"
                            class="btn btn btn-success text-uppercase submit-button"><?php echo get_phrase('Submit'); ?></button>
                        <button type="reset" id="resetBtn" style="display: none;"></button>
                    </div>
                </section>
            </form>
        </div>
    </section>
</main>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

<!-- uploader -->
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

<script>
    (function() {
        // =================== Helpers ===================
        const $ = (s, ctx) => (ctx || document).querySelector(s);
        const $$ = (s, ctx) => Array.from((ctx || document).querySelectorAll(s));

        // =================== App ===================
        const navToggle = $('.nav-toggle'),
            mainNav = $('.main-nav');
        if (navToggle && mainNav) {
            navToggle.addEventListener('click', () => {
                const open = mainNav.classList.toggle('nav-open');
                navToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
            });
        }

        // Stepper
        const steps = $$('.step-create-commaunaute');
        const panes = $$('.step-pane');
        let current = 0;

        function goTo(i) {
            if (i < 0 || i >= panes.length) return;
            const prevScroll = window.scrollY;
            panes.forEach(p => p.classList.remove('is-visible'));
            steps.forEach((s, idx) => {
                s.classList.toggle('is-active', idx === i);
                s.classList.toggle('is-complete', idx < i);
            });
            panes[i].classList.add('is-visible');
            current = i;
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
            window.scrollTo({
                top: prevScroll,
                behavior: 'auto'
            });
            updateSummary();
        }

        // === Step 3: Particulier => prix interdit
        const iAmSelect = $('#i_am');
        const priceWrapper = $('#priceFieldWrapper');
        const priceInput = $('#communityprice');
        const particulierNotice = $('#particulierNotice');

        function toggleStep3PriceField() {
            const isParticulier = (iAmSelect?.value === 'Particulier');
            if (isParticulier) {
                particulierNotice.style.display = 'block';
                priceWrapper.style.display = 'block';
                priceInput.value = 0;
                priceInput.setAttribute('disabled', 'true');
                priceInput.classList.add('bg-light');
            } else {
                particulierNotice.style.display = 'none';
                priceWrapper.style.display = 'block';
                priceInput.removeAttribute('disabled');
                priceInput.classList.remove('bg-light');
                if (priceInput.value === '0') priceInput.value = '';
            }
        }
        iAmSelect?.addEventListener('change', toggleStep3PriceField);
        toggleStep3PriceField();

        // ===== Devise selon Tax_residence
        const taxResSelect = $('#Tax_residence');
        const currencyHint = $('#currencyHint');
        const currencyCodeEl = $('#currencyCode');

        function updateCurrencyUI() {
            const sel = taxResSelect?.value || '';
            const code = sel === 'MA' ? 'MAD' : sel === 'UAE' ? 'AED' : 'EUR';
            if (currencyHint) currencyHint.textContent = code;
            if (currencyCodeEl) currencyCodeEl.value = code;
            if (priceInput && !priceInput.disabled) {
                priceInput.placeholder = `<?php echo get_phrase("price"); ?> (${code})`;
            }
        }
        taxResSelect?.addEventListener('change', updateCurrencyUI);
        updateCurrencyUI();

        // Next/Prev
        $$('.next').forEach(btn => btn.addEventListener('click', () => {
            if (!validateStep(current)) return;
            if (current < panes.length - 1) goTo(current + 1);
        }));
        $$('.prev').forEach(btn => btn.addEventListener('click', () => {
            if (current > 0) goTo(current - 1);
        }));

        steps.forEach((s, idx) => {
            s.style.cursor = 'pointer';
            s.addEventListener('click', () => idx <= current && goTo(idx));
        });

        // =================== Validation ===================
        function setInvalid(el, msg) {
            if (!el) return;
            el.classList.add('is-invalid');
            const err = $(`.error[data-for="${el.id}"]`);
            if (err) err.textContent = msg || el.dataset.msg || '';
        }

        function clearInvalid(el) {
            if (!el) return;
            el.classList.remove('is-invalid');
            const err = $(`.error[data-for="${el.id}"]`);
            if (err) err.textContent = '';
        }

        function validateStep(stepIndex) {
            let ok = true;
            const pane = panes[stepIndex];
            if (!pane) return true;

            // Vérification doublons
            if ($$('[data-duplicate="true"]').length > 0) {
                toastr.warning('<?= get_phrase("please_correct_duplicate_fields"); ?>');
                return false;
            }

            // Step 1
            if (pane.dataset.step === "1") {
                const name = $('#profileName');
                const email = $('#profileEmail');
                const phone = $('#profilePhone');
                const pass = $('#profilePass');
                const pass2 = $('#profilePass2');

                if (!name.value.trim() || name.value.trim().length < 2) {
                    setInvalid(name);
                    ok = false;
                } else clearInvalid(name);
                if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value.trim())) {
                    setInvalid(email);
                    ok = false;
                } else clearInvalid(email);
                if ((phone.value || '').replace(/\D/g, '').length < 8) {
                    setInvalid(phone);
                    ok = false;
                } else clearInvalid(phone);
                if ((pass.value || '').length < 6) {
                    setInvalid(pass);
                    ok = false;
                } else clearInvalid(pass);
                if (pass.value !== pass2.value) {
                    setInvalid(pass2);
                    ok = false;
                } else clearInvalid(pass2);
            }

            // Step 2
            if (pane.dataset.step === "2") {
                const fields = ['#communityName', '#communityDesc', '#communityCat', '#Tax_residence', '#communityLang', '#communityStreet', '#communityNumber', '#communityCity', '#communityPostalCode', '#i_am'];
                fields.forEach(sel => {
                    const el = $(sel);
                    if (el && !el.value.trim()) {
                        setInvalid(el);
                        ok = false;
                    } else clearInvalid(el);
                });
                const desc = $('#communityDesc');
                if (desc.value.trim().length < 40) {
                    setInvalid(desc);
                    ok = false;
                }
                const number = $('#communityNumber');
                if (!/^\d+$/.test(number.value.trim())) {
                    setInvalid(number);
                    ok = false;
                }
                const postal = $('#communityPostalCode');
                if (!/^\d{4,5}$/.test(postal.value.trim())) {
                    setInvalid(postal);
                    ok = false;
                }
            }

            // Step 3 - Prix
            if (pane.dataset.step === "3") {
                const i_am = $('#i_am')?.value || '';
                if (i_am !== 'Particulier' && priceInput && !priceInput.disabled) {
                    const val = (priceInput.value || '').trim();
                    if (val === '' || isNaN(val) || Number(val) < 0) {
                        setInvalid(priceInput, '<?php echo get_phrase("Please enter a valid price"); ?>');
                        ok = false;
                    } else {
                        clearInvalid(priceInput);
                    }
                }
            }

            return ok;
        }

        // =================== Résumé ===================
        window.updateSummary = function() {
            const summaryDiv = $('.summary');
            if (!summaryDiv) return;

            const profileName = $('#profileName')?.value || '—';
            const profileEmail = $('#profileEmail')?.value || '—';
            const phone = $('#profilePhone')?.value || '—';
            const lang = $('#communityLang')?.value || '—';
            const i_am = $('#i_am')?.value || '—';
            const communityName = $('#communityName')?.value || '—';
            const desc = $('#communityDesc')?.value || '—';
            const cat = $('#communityCat')?.value || '—';
            const street = $('#communityStreet')?.value || '—';
            const number = $('#communityNumber')?.value || '—';
            const city = $('#communityCity')?.value || '—';
            const postal = $('#communityPostalCode')?.value || '—';
            const tax = $('#Tax_residence')?.value || '—';
            const currency = $('#currencyCode')?.value || 'MAD';
            const priceVal = $('#communityprice')?.value || '';
            const isPrivate = $('#isPrivate')?.checked ? '<?php echo get_phrase("Private"); ?>' : '<?php echo get_phrase("Public"); ?>';

            const vat = tax === 'MA' ? '20%' : tax === 'UAE' ? '5%' : '—';

            const logoHtml = $('#logoPreview img') ? `<img src="${$('#logoPreview img').src}" style="max-width:80px; border-radius:10px;">` : '—';
            const coverHtml = $('#coverPreview img') ? `<img src="${$('#coverPreview img').src}" style="max-width:120px; border-radius:10px;">` : '—';

            const finalPrice = (i_am === 'Particulier') ? `0 ${currency}` : (priceVal ? `${priceVal} ${currency}` : '—');

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
        };

        // Initialisation
        goTo(0);

    })(); // Fin correcte du IIFE
</script>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        const CONFIG = {
            logo: {
                maxMB: 1,
                ratio: 1,
                minWidth: 512,
                tolerance: 0.03
            },
            cover: {
                maxMB: 2,
                ratio: 16 / 9,
                minWidth: 1600,
                tolerance: 0.03
            }
        };

        toastr.options = {
            closeButton: true,
            progressBar: true,
            positionClass: 'toast-top-right',
            timeOut: 5000
        };

        const $ = s => document.querySelector(s);
        const $$ = s => document.querySelectorAll(s);

        function formatBytes(b) {
            return b < 1024 * 1024 ? (b / 1024).toFixed(1) + ' Ko' : (b / (1024 * 1024)).toFixed(1) + ' Mo';
        }

        async function validateImage(file, type) {
            const c = CONFIG[type];
            if (!file) return {
                valid: false,
                msg: '<?php echo get_phrase("No_file_selected"); ?>'
            };

            if (file.size > c.maxMB * 1024 * 1024)
                return {
                    valid: false,
                    msg: '<?php echo get_phrase("Too_heavy_Max"); ?> ' + c.maxMB + ' <?php echo get_phrase("MB"); ?> (' + formatBytes(file.size) + ')'
                };

            return new Promise(resolve => {
                const img = new Image();
                img.onload = () => {
                    const ratio = img.width / img.height;
                    const diff = Math.abs(ratio - c.ratio) / c.ratio;
                    if (diff > c.tolerance)
                        return resolve({
                            valid: false,
                            msg: '<?php echo get_phrase("Required_format"); ?>: ' + (type === 'logo' ? '1:1' : '16:9') + '<br><?php echo get_phrase("Current"); ?>: ' + img.width + '×' + img.height
                        });
                    if (img.width < c.minWidth)
                        return resolve({
                            valid: false,
                            msg: '<?php echo get_phrase("Min"); ?> ' + c.minWidth + 'px <?php echo get_phrase("width"); ?>'
                        });
                    resolve({
                        valid: true
                    });
                };
                img.onerror = () => resolve({
                    valid: false,
                    msg: '<?php echo get_phrase("Corrupted_image"); ?>'
                });
                img.src = URL.createObjectURL(file);
            });
        }

        function showImageError(previewId, msg) {
            $(`#${previewId}`).innerHTML = `<div class="text-danger small p-3 text-center bg-light border rounded"> ${msg}</div>`;
        }

        let lastEmail = '',
            lastSchool = '';

        function checkDuplication(type, value, input) {
            if (!value.trim() || (type === 'email' && value === lastEmail) || (type === 'school_name' && value === lastSchool)) return;

            if (type === 'email') lastEmail = value;
            if (type === 'school_name') lastSchool = value;

            const formData = new FormData();
            formData.append('type', type);
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
                        errorEl.textContent = data.message;
                        errorEl.style.display = 'block';
                        input.classList.add('is-invalid');
                        input.setAttribute('data-duplicate', 'true');
                    } else {
                        errorEl.style.display = 'none';
                        input.classList.remove('is-invalid');
                        input.removeAttribute('data-duplicate');
                    }
                    updateContinueButton();
                });
        }

        window.updateContinueButton = function() {
            const pane = $('.step-pane.is-visible');
            const nextBtn = pane?.querySelector('.next');
            if (!nextBtn) return;

            const hasDup = $$('[data-duplicate="true"]').length > 0;
            const invalidInPane = pane.querySelectorAll('.is-invalid').length > 0;

            let imagesOk = true;
            if (pane.dataset.step === '2') {
                const logoInput = $('#communityLogo');
                const coverInput = $('#communityCover');

                const logoFile = logoInput?.files[0];
                const coverFile = coverInput?.files[0];

                // Si fichier uploadé → doit être valide
                if (logoFile && !logoInput.hasAttribute('data-valid')) imagesOk = false;
                if (coverFile && !coverInput.hasAttribute('data-valid')) imagesOk = false;

                // Si pas de fichier → OK (optionnel)
            }

            // 4. Prix (étape 3) - SEULEMENT si non-Particulier
            let priceOk = true;
            if (pane.dataset.step === '3') {
                const iAm = $('#i_am')?.value;
                const priceInput = $('#communityprice');
                if (iAm !== 'Particulier' && priceInput && !priceInput.disabled) {
                    const val = (priceInput.value || '').trim();
                    priceOk = val !== '' && !isNaN(val) && Number(val) >= 0;
                    priceInput.classList.toggle('is-invalid', !priceOk);
                }
            }

            const disabled = hasDup || invalidInPane || !imagesOk || !priceOk;
            nextBtn.disabled = disabled;
            nextBtn.classList.toggle('opacity-50', disabled);
        };

        ['logo', 'cover'].forEach(type => {
            const input = $(`#community${type.charAt(0).toUpperCase() + type.slice(1)}`);
            const preview = $(`#${type}Preview`);
            input?.addEventListener('change', async () => {
                const file = input.files[0];
                const result = file ? await validateImage(file, type) : {
                    valid: false
                };
                input.setAttribute('data-valid', result.valid);
                if (result.valid) {
                    const reader = new FileReader();
                    reader.onload = e => preview.innerHTML = `<img src="${e.target.result}" style="max-width:100%; border-radius:8px; ${type === 'cover' ? 'height:80px; object-fit:cover;' : ''}">`;
                    reader.readAsDataURL(file);
                } else {
                    showImageError(`${type}Preview`, result.msg);
                    input.value = '';
                }
                updateContinueButton();
            });
        });

        // --- Doublons ---
        $('#profileEmail')?.addEventListener('blur', () => checkDuplication('email', $('#profileEmail').value, $('#profileEmail')));
        $('#communityName')?.addEventListener('blur', () => checkDuplication('school_name', $('#communityName').value, $('#communityName')));

        // --- Prix ---
        $('#i_am')?.addEventListener('change', () => {
            const isPart = $('#i_am').value === 'Particulier';
            const priceWrapper = $('#priceFieldWrapper');
            const priceInput = $('#communityprice');
            const notice = $('#particulierNotice');

            if (isPart) {
                priceWrapper.style.display = 'block';
                priceInput.value = '0';
                priceInput.disabled = true;
                priceInput.classList.add('bg-light');
                notice.style.display = 'block';
            } else {
                priceWrapper.style.display = 'block';
                priceInput.disabled = false;
                priceInput.classList.remove('bg-light');
                notice.style.display = 'none';
                if (priceInput.value === '0') priceInput.value = '';
            }
            updateContinueButton();
        });

        $('#communityprice')?.addEventListener('input', updateContinueButton);

        // --- Tax residence → currency ---
        $('#Tax_residence')?.addEventListener('change', () => {
            const code = $('#Tax_residence').value === 'MA' ? 'MAD' : 'AED';
            $('#currencyHint').textContent = code;
            $('#currencyCode').value = code;
            if ($('#communityprice') && !$('#communityprice').disabled) {
                $('#communityprice').placeholder = `Prix (${code})`;
            }
        });

        // --- Validation manuelle des champs ---
        $$('input[required], select[required], textarea[required]').forEach(el => {
            el.addEventListener('blur', () => {
                const isEmpty = !el.value.trim();
                el.classList.toggle('is-invalid', isEmpty);
                updateContinueButton();
            });
        });

        // --- Stepper ---
        let current = 0;
        const panes = $$('.step-pane');
        const steps = $$('.step-create-commaunaute');

        function goTo(i) {
            if (i < 0 || i >= panes.length) return;
            if (i > current && !validateCurrentStep()) return;

            panes.forEach(p => p.classList.remove('is-visible'));
            steps.forEach((s, idx) => {
                s.classList.toggle('is-active', idx === i);
                s.classList.toggle('is-complete', idx < i);
            });
            panes[i].classList.add('is-visible');
            current = i;
            updateContinueButton();
        }

        function validateCurrentStep() {
            const pane = panes[current];
            let valid = true;

            // Champs requis
            pane.querySelectorAll('[required]').forEach(field => {
                if (!field.value.trim()) {
                    field.classList.add('is-invalid');
                    valid = false;
                }
            });

            // Étape 1 : mot de passe
            if (pane.dataset.step === '1') {
                const p1 = $('#profilePass'),
                    p2 = $('#profilePass2');
                if (p1.value !== p2.value || p1.value.length < 6) {
                    p2.classList.add('is-invalid');
                    valid = false;
                }
            }

            // Étape 2 : description min 40
            if (pane.dataset.step === '2') {
                const desc = $('#communityDesc');
                if (desc.value.trim().length < 40) {
                    desc.classList.add('is-invalid');
                    valid = false;
                }
            }

            if (!valid) toastr.warning('Veuillez corriger les erreurs');
            return valid;
        }

        $$('.next').forEach(btn => btn.addEventListener('click', () => goTo(current + 1)));
        $$('.prev').forEach(btn => btn.addEventListener('click', () => goTo(current - 1)));
        steps.forEach((s, i) => s.addEventListener('click', () => i <= current && goTo(i)));

        // --- Submit ---
        $('#schoolform').addEventListener('submit', function(e) {
            e.preventDefault();
            if (!validateCurrentStep()) return;

            const formData = new FormData(this);
            fetch(this.action, {
                    method: 'POST',
                    body: formData
                })
                .then(r => r.json())
                .then(data => {
                    if (data.csrf) $(`input[name="${data.csrf.csrfName}"]`).value = data.csrf.csrfHash;
                    data.status ? (
                        toastr.success(data.message),
                        $('#resetBtn').click(),
                        setTimeout(() => location.href = '<?= site_url('/home/communities'); ?>', 2000)
                    ) : toastr.error(data.message);
                });
        });

        // Init
        goTo(0);
    });
</script>