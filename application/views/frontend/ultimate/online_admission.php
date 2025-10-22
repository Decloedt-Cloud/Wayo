<?php if (get_common_settings('recaptcha_status')): ?>
  <script src="https://www.google.com/recaptcha/api.js" async defer></script>
<?php endif; ?>

<?php
?>

<style>
  /* ----------------- Hero ----------------- */

    .hero{ position:relative; min-height:68vh; display:grid; place-items:center; color:#fff; background-image:url('../uploads/images/decloedt/img/cover-wayo.png'); background-size:cover; background-position:center; }
    .hero::before{ content:""; position:absolute; inset:0; background:linear-gradient(180deg, rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.60));}
    .hero .hero-content{ position:relative; text-align:center; }
    .hero .lead{ max-width:760px; margin-inline:auto; color:#e9e9ef }

    .btn-pill{ border-radius:999px; font-weight:700; padding:.8rem 1.2rem; }
    .btn-accent{ background:var(--accent); color:#fff; box-shadow:0 8px 20px rgba(252,123,48,.25) }
    .btn-accent:hover{ background:var(--accent-600); color:#fff; }
    .btn-ghost{ background:#ffffff14; color:#fff; border:1px solid #ffffff40 }
    .btn-ghost:hover{ background:#ffffff26; color:#fff; }

    /* ====== Step 2 layout ====== */
    .panel--community .grid {
      display: grid;
      grid-template-columns: 1.15fr .85fr;
      gap: 24px;
    }
    @media (max-width: 992px){
      .panel--community .grid{ grid-template-columns: 1fr; }
    }
    .panel--community fieldset{
      border:1px solid #eee; border-radius:12px; padding:18px; margin:0 0 18px 0; background:#fff;
    }
    .panel--community legend{
      font-weight:700; font-size:14px; padding:0 8px; color:#333;
    }
    .panel--community .stack > .field{ margin-bottom:14px; }

     /* #priceFieldWrapper { display: none; } */
    #communityprice.bg-light {
      background-color: #f8f9fa;
      cursor: not-allowed;
      color: #777;
    }
    /* #priceFieldWrapper { display: none; } */
    #communityprice.bg-light { background-color:#f8f9fa; cursor:not-allowed; color:#777; }

    .alert { padding:12px 14px; border-radius:8px; font-size:.95rem; }
    .alert-warning { background:#fff7e6; border:1px solid #ffe0a3; color:#7a4d00; }



</style>

<main class="mt-5">
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
      class="js-validate studentform realtime-form container" enctype="multipart/form-data"  novalidate>
              <!-- Champ caché pour le jeton CSRF -->
     <input type="hidden" name="<?=$this->security->get_csrf_token_name();?>" value="<?=$this->security->get_csrf_hash();?>" />
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
          <button type="button" class="btn btn-primary next"><?php echo get_phrase("Continue") ?></button>
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
      <input id="isPrivate" name="visibility" type="checkbox" value="1"/>
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

      <div class="grid-2"  style="grid-template-columns: 50% 50%; gap: 14px;">
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
          <button type="button" class="btn btn-primary next"><?php echo get_phrase("Continue") ?></button>
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
            <button type="button" class="btn btn-primary next"><?php echo get_phrase("Continue") ?></button>
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
                <strong><?php // echo get_phrase("Free") ?></strong>
                <span><?php // echo get_phrase("Free_access") ?></span>
              </div>
            </label> -->

            <label class="radio-tile">
              <input type="radio" name="communityPriceType" value="oneoff" checked>
              <div class="tile" data-coming="true">
                <i class="fa-solid fa-hand-holding-dollar"></i>
                <strong><?php echo get_phrase("price_to_pay_790") ?>  <small id="currencyHint" class="help">MAD</small></strong>
                <span><?php  echo get_phrase("You are entitled to a 14-day free trial") ?></span>
              </div>
            </label>


            <!-- <label class="radio-tile">
              <input type="radio" name="communityPriceType" value="subscription" disabled>
              <div class="tile tile-disabled" data-coming="true">
                <i class="fa-solid fa-arrows-rotate"></i>
                <strong><?php //echo get_phrase("Subscription") ?></strong>
                <span><?php // echo get_phrase("Coming_soon") ?></span>
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
            <button type="button" class="btn btn-primary next"><?php echo get_phrase("Continue") ?></button>
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

<script>
(function(){
  // =================== Helpers ===================
  const $  = (s,ctx)=> (ctx||document).querySelector(s);
  const $$ = (s,ctx)=> Array.from((ctx||document).querySelectorAll(s));

  // =================== App ===================
  // NAV mobile
  const navToggle = $('.nav-toggle'), mainNav = $('.main-nav');
  if(navToggle && mainNav){
    navToggle.addEventListener('click', ()=>{
      const open = mainNav.classList.toggle('nav-open');
      navToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    });
  }

  // Stepper
  const steps = $$('.step-create-commaunaute');
  const panes = $$('.step-pane');
  let current = 0;

  function goTo(i){
    if(i<0 || i>=panes.length) return;
      const prevScroll = window.scrollY; // Sauvegarde la position actuelle
    panes.forEach(p=>p.classList.remove('is-visible'));
    steps.forEach((s,idx)=>{
      s.classList.toggle('is-active', idx===i);
      s.classList.toggle('is-complete', idx<i);
    });
    panes[i].classList.add('is-visible');
    current = i;
    window.scrollTo({top:0,behavior:'smooth'});
    // Restaure la position pour éviter le scroll vers le haut
    window.scrollTo({ top: prevScroll, behavior: 'auto' });
    updateSummary?.();
  }



// === Step 3: "Particulier" => prix interdit, fixé à 0 et désactivé
const iAmSelect        = document.querySelector('#i_am');
const priceWrapper     = document.querySelector('#priceFieldWrapper');
const priceInput       = document.querySelector('#communityprice');
const particulierNotice= document.querySelector('#particulierNotice');

function toggleStep3PriceField() {
  const isParticulier = (iAmSelect?.value === 'Particulier');

  if (isParticulier) {
    // Affiche le message et verrouille le champ prix
    if (particulierNotice) particulierNotice.style.display = 'block';
    if (priceWrapper) priceWrapper.style.display = 'block';
    if (priceInput) {
      priceInput.value = 0;
      priceInput.setAttribute('disabled', 'true');
      priceInput.classList.add('bg-light');
      // même si "required" reste, il est ignoré quand disabled
    }
  } else {
    // Masque le message, réactive le champ
    if (particulierNotice) particulierNotice.style.display = 'none';
    if (priceWrapper) priceWrapper.style.display = 'block'; // visible pour les autres statuts
    if (priceInput) {
      priceInput.removeAttribute('disabled');
      priceInput.classList.remove('bg-light');
      if (priceInput.value === '0') priceInput.value = '';
      priceInput.classList.remove('is-invalid');
      const err = document.querySelector('.error[data-for="communityprice"]');
      if (err) err.textContent = '';
    }
  }
}

// Listener + init
iAmSelect?.addEventListener('change', toggleStep3PriceField);
toggleStep3PriceField();
// ===== Devise selon Tax_residence : MA -> MAD, UAE -> AED, défaut EUR
const taxResSelect   = document.querySelector('#Tax_residence');
const currencyHint   = document.querySelector('#currencyHint');
const currencyCodeEl = document.querySelector('#currencyCode');

function currencyFor(resCode){
  switch (resCode) {
    case 'MA':  return { code: 'MAD', symbol: 'DH'  };
    case 'UAE': return { code: 'AED', symbol: 'د.إ' };
    default:    return { code: 'EUR', symbol: '€'   };
  }
}

function updateCurrencyUI() {
  const sel = taxResSelect?.value || '';
  const { code } = currencyFor(sel);

  if (currencyHint)   currencyHint.textContent = `${code}`;
  if (currencyCodeEl) currencyCodeEl.value     = code;

  // Met à jour le placeholder quand le champ est actif
  if (priceInput && !priceInput.disabled) {
    priceInput.placeholder = `<?php echo get_phrase("price"); ?> (${code})`;
  }
}
// Listeners + init
taxResSelect?.addEventListener('change', updateCurrencyUI);
updateCurrencyUI();

  // Next/Prev buttons
  $$('.next').forEach(btn => btn.addEventListener('click', ()=> {
    if(!validateStep(current)) return;
    if(current < panes.length-1) goTo(current+1);
  }));
  $$('.prev').forEach(btn => btn.addEventListener('click', ()=> {
     e.preventDefault();
    if(current > 0) goTo(current-1);
  }));

   // Clic sur les steps déjà terminés
  steps.forEach((s, idx) => {
    s.style.cursor = 'pointer';
    s.addEventListener('click', ()=> { if(idx <= current) goTo(idx); });
  });

  goTo(0);

  // =================== Validation ===================
  function setInvalid(el, msg){
    if(!el) return false;
    el.classList.add('is-invalid');
    const err = document.querySelector(`.error[data-for="${el.id}"]`);
    const finalMsg = msg || el.getAttribute('data-msg') || '';
    if(err) err.textContent = finalMsg;
    return false;
  }

  function clearInvalid(el){
    if(!el) return;
    el.classList.remove('is-invalid');
    const err = document.querySelector(`.error[data-for="${el.id}"]`);
    if(err) err.textContent = '';
  }

  function validateStep(stepIndex){
    let ok = true;
    const pane = panes[stepIndex];
    if(!pane) return true;

    // STEP 1 : Profil
    if(pane.dataset.step=="1"){
      const name = $('#profileName'), email = $('#profileEmail'), phone = $('#profilePhone'),
            pass = $('#profilePass'), pass2 = $('#profilePass2');

      if(!name.value.trim() || name.value.trim().length<2) ok = setInvalid(name,'Nom obligatoire (2 caractères min)') && ok; else clearInvalid(name);
      if(!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value.trim())) ok = setInvalid(email,'Email invalide') && ok; else clearInvalid(email);
      if(((phone.value)||'').replace(/\D/g,'').length < 8) ok = setInvalid(phone,'Téléphone obligatoire (au moins 8 chiffres)') && ok; else clearInvalid(phone);
      if((pass.value||'').length < 6) ok = setInvalid(pass,'6 caractères minimum') && ok; else clearInvalid(pass);
      if(pass.value !== pass2.value || (pass2.value||'').length<6) ok = setInvalid(pass2,'Les mots de passe ne correspondent pas') && ok; else clearInvalid(pass2);
    }


    // STEP 2 : Communauté
    if(pane.dataset.step=="2"){
      const name = $('#communityName'),
            desc = $('#communityDesc'),
            Tax_residence = $('#Tax_residence'),
            cat = $('#communityCat'),
            lang = $('#communityLang'),
            street = $('#communityStreet'),
            number = $('#communityNumber'),
            city = $('#communityCity'),
            postalCode = $('#communityPostalCode'),
            i_am = $('#i_am');

      if(!name.value.trim() || name.value.trim().length < 2) ok = setInvalid(name) && ok; else clearInvalid(name);
      if(!desc.value.trim() || desc.value.trim().length < 40) ok = setInvalid(desc) && ok; else clearInvalid(desc);
      if(!cat.value) ok = setInvalid(cat) && ok; else clearInvalid(cat);
      if(!Tax_residence.value) ok = setInvalid(Tax_residence) && ok; else clearInvalid(Tax_residence);
      if(!lang.value) ok = setInvalid(lang) && ok; else clearInvalid(lang);

      if(!street.value.trim() || street.value.trim().length < 3) ok = setInvalid(street) && ok; else clearInvalid(street);
      if(!/^[0-9]+$/.test(number.value.trim())) ok = setInvalid(number) && ok; else clearInvalid(number);
      if(!city.value.trim() || city.value.trim().length < 2) ok = setInvalid(city) && ok; else clearInvalid(city);
      if(!i_am.value.trim()) ok = setInvalid(i_am) && ok; else clearInvalid(i_am);
      if(!/^[0-9]{4,5}$/.test(postalCode.value.trim())) ok = setInvalid(postalCode) && ok; else clearInvalid(postalCode);
    }



  // STEP 3 : Pricing and access
 if (pane.dataset.step=="3"){
  const i_am = $('#i_am')?.value || '';
  if (i_am !== 'Particulier') {
    const priceEl = $('#communityprice');
    if (priceEl && !priceEl.disabled) {
      const val = (priceEl.value || '').trim().replace(',', '.');
      const isNum = val !== '' && !isNaN(val);
      if (!isNum || Number(val) < 0) {
        ok = setInvalid(priceEl, '<?php echo get_phrase("Please enter a valid price"); ?>') && ok;
      } else {
        clearInvalid(priceEl);
      }
    }
  }
}



    return ok;
  }

  // =================== Uploaders ===================
  function setupUploader(inputId, previewId){
    const input = $(inputId);
    const preview = $(previewId);
    if(!input || !preview) return;
    input.addEventListener('change', e=>{
      const file = e.target.files[0];
      if(file && /^image\//.test(file.type)){
        const reader = new FileReader();
        reader.onload = ev => preview.innerHTML = `<img src="${ev.target.result}" alt="Preview" style="max-width:100%; border-radius:8px;">`;
        reader.readAsDataURL(file);
      }
    });
  }

  setupUploader('#communityLogo','#logoPreview');
  setupUploader('#communityCover','#coverPreview');

  // =================== Résumé ===================
function updateSummary(){
  const pane = panes[4]; // step 5
  if(!pane) return;
  const summaryDiv = pane.querySelector('.summary');
  if(!summaryDiv) return;

  const profileName = $('#profileName')?.value || '—';
  const profileEmail = $('#profileEmail')?.value || '—';
  const phone = $('#profilePhone')?.value || '—';
  const communityName = $('#communityName')?.value || '—';
  const desc = $('#communityDesc')?.value || '—';
  const cat = $('#communityCat')?.value || '—';
  const communityStreet = $('#communityStreet')?.value || '—';
  const communityNumber = $('#communityNumber')?.value || '—';
  const communityCity = $('#communityCity')?.value || '—';
  const communityPostalCode = $('#communityPostalCode')?.value || '—';
  const Tax_residence = $('#Tax_residence')?.value || '—';
  const i_am = $('#i_am')?.value || '—';
  const lang = $('#communityLang')?.value || '—';
  // const price = $('#price')?.value || '—';
const priceVal = $('#communityprice')?.value || '';
const selectedPriceType = document.querySelector('input[name="communityPriceType"]:checked')?.value || '';
// const finalPrice = (selectedPriceType === 'free') ? '0' : priceVal || '—';

const currency = $('#currencyCode')?.value || '';
// Prix rendu (si Particulier et prix lock à 0, on affiche 0 + devise)
const finalPrice = (i_am === 'Particulier' || selectedPriceType === 'free')
  ? `0 ${currency}`
  : (priceVal ? `${priceVal} ${currency}` : '—');



if (Tax_residence === 'MA') {
  vat = '20%';
}else if (Tax_residence === 'UAE') {
  vat = '5%';   
}

  const isPrivate = $('#isPrivate')?.checked ? 1 : 0;

  const logoEl = $('#logoPreview img');
  const coverEl = $('#coverPreview img');
  const logoHtml = logoEl ? `<img src="${logoEl.src}" style="max-width:80px; border-radius:10px;">` : '—';
  const coverHtml = coverEl ? `<img src="${coverEl.src}" style="max-width:120px; border-radius:10px;">` : '—';

  summaryDiv.innerHTML = `
    <h3><?php echo get_phrase("Profile"); ?></h3>
    <p><?php echo get_phrase("Name"); ?> : ${profileName}<br>
       <?php echo get_phrase("Email"); ?> : ${profileEmail}<br>
       <?php echo get_phrase("Phone"); ?> : ${phone}<br>
       <?php echo get_phrase("Primary_language"); ?> : ${lang}</p>

    <h3><?php echo get_phrase("Community"); ?></h3>
    <p><?php echo get_phrase("I_am"); ?> : ${i_am}<br>
       <?php echo get_phrase("Community_name"); ?> : ${communityName}<br>
       <?php echo get_phrase("Description"); ?> : ${desc}<br>
       <?php echo get_phrase("Category"); ?> : ${cat}<br>
       <?php echo get_phrase("Address"); ?> : ${communityStreet}, ${communityNumber}, ${communityCity}, ${communityPostalCode}<br>
       <?php echo get_phrase("Country applicable for VAT"); ?> : ${Tax_residence}</p>

    <h3><?php echo get_phrase("Media"); ?></h3>
    <p><?php echo get_phrase("Logo"); ?> : ${logoHtml}<br>
       <?php echo get_phrase("Cover"); ?> : ${coverHtml}</p>
    


    <h3><?php echo get_phrase("Price"); ?></h3>
    <p><?php echo get_phrase("price"); ?> : ${finalPrice}</p>
    <p> <?php echo get_phrase("VAT rate applied"); ?> : ${vat}</p>



    <h3><?php echo get_phrase("Visibility"); ?></h3>
    <p><?php echo get_phrase("Community"); ?> : ${isPrivate === 1 ? '<?php echo get_phrase("Private"); ?>' : '<?php echo get_phrase("Public"); ?>'}</p>
  `;
}


})();
</script>


<script>
document.addEventListener('DOMContentLoaded', function () {
    // Configure Toastr options
    toastr.options = {
        closeButton: true,
        progressBar: true,
        positionClass: 'toast-top-right',
        timeOut: 5000,
        showMethod: 'fadeIn',
        hideMethod: 'fadeOut'
    };

    const schoolForm = document.getElementById('schoolform');
    const submitBtn = document.getElementById('submitBtnSchool');
    const resetBtn = document.getElementById('resetBtn');

    if (schoolForm && submitBtn) {
        schoolForm.addEventListener('submit', function (event) {
            event.preventDefault(); // Prevent default form submission

            // Validate form
            if (!schoolForm.checkValidity()) {
                schoolForm.reportValidity();
                return;
            }

            // Get CSRF token from the hidden input
            const csrfName = document.querySelector(`input[name="${<?php echo json_encode($this->security->get_csrf_token_name()); ?>}"]`).name;
            const csrfHash = document.querySelector(`input[name="${<?php echo json_encode($this->security->get_csrf_token_name()); ?>}"]`).value;

            // Prepare form data
            const formData = new FormData(schoolForm);
            formData.append(csrfName, csrfHash); // Ensure CSRF token is included

            // Perform AJAX submission
            fetch(schoolForm.action, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json()) // Expect JSON response
            .then(data => {
              console.log("Réponse du serveur :", data);

                // Update CSRF token for the next request
                if (data.csrf) {
                    document.querySelector(`input[name="${data.csrf.csrfName}"]`).value = data.csrf.csrfHash;
                }

                if (data.status) {
                    // Success case
                    toastr.success(data.message); // Afficher le toast de succès
                    resetBtn.click(); // Reset form
                    setTimeout(() => {
                        window.location.href = '<?php echo site_url('/home/communities'); ?>';
                    }, 2000); // Attendre 2 secondes pour que le toast soit visible
                } else {
                    // Error case (e.g., duplicate email, school name, or validation error)
                    toastr.error(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                toastr.error("<?= get_phrase('an_error_occurred'); ?>");

            });
        });
    }

    // Password match validation
    // const password = document.getElementById('password');
    // const repeatPassword = document.getElementById('repeat-password');
    // const errorMessage = document.getElementById('errorMessage');

    // if (password && repeatPassword && errorMessage) {
    //     repeatPassword.addEventListener('input', function () {
    //         if (password.value !== repeatPassword.value) {
    //             errorMessage.classList.remove('display-none');
    //             submitBtn.disabled = true;
    //         } else {
    //             errorMessage.classList.add('display-none');
    //             submitBtn.disabled = false;
    //         }
    //     });
    // }
});
</script>


