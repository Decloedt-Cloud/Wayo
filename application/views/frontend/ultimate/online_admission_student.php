
<?php if (get_common_settings('recaptcha_status')): ?>
  <script src="https://www.google.com/recaptcha/api.js" async defer></script>
<?php endif; ?>

<?php
?>
<style>
  /* ----------------- Hero ----------------- */

    .hero{ position:relative; min-height:50vh; display:grid; place-items:center; color:#fff; background-image:url('../uploads/images/decloedt/img/cover-wayo.png'); background-size:cover; background-position:center; }
    .hero::before{ content:""; position:absolute; inset:0; background:linear-gradient(180deg, rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.60));}
    .hero .hero-content{ position:relative; text-align:center; }
    .hero .lead{ max-width:760px; margin-inline:auto; color:#e9e9ef }

    /* Buttons override from online_admission */
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

    /* ================= SUMMARY REDESIGN ================= */
    .summary-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        margin-top: 1.5rem;
    }

    .summary-card {
        background: #fff;
        border: 1px solid #ECEEF3;
        border-radius: 16px;
        padding: 1.5rem;
        transition: all 0.3s ease;
        display: flex;
        flex-direction: column;
        gap: 1rem;
        flex: 1 1 300px; /* Allow growth, min-width 300px */
    }

    .summary-card:hover {
        border-color: #f47a1f;
        box-shadow: 0 4px 12px rgba(244, 122, 31, 0.08);
    }

    .summary-card-header {
        display: flex;
        align-items: center;
        gap: 12px;
        border-bottom: 1px solid #f1f5f9;
        padding-bottom: 0.75rem;
        margin-bottom: 0.25rem;
    }

    .summary-card-icon {
        width: 36px;
        height: 36px;
        background: #fff5ec;
        color: #f47a1f;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
    }

    .summary-card-header h3 {
        font-size: 1.1rem;
        font-weight: 700;
        color: #1e1e4b;
        margin: 0;
    }

    .summary-card-body {
        font-size: 0.95rem;
        line-height: 1.6;
        color: #4b5563;
    }

    .summary-item {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.5rem;
    }

    .summary-label {
        font-weight: 600;
        color: #64748b;
        margin-right: 12px;
    }

    .summary-value {
        color: #1e293b;
        text-align: right;
    }

    .status-badge {
        display: inline-block;
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: #fff;
        padding: 0.35rem 0.85rem;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 700;
        letter-spacing: 0.3px;
        box-shadow: 0 2px 8px rgba(16, 185, 129, 0.25);
        text-transform: uppercase;
    }

    /* Info Alert Box */
    .info-alert-box {
        background: #e8f4fd;
        border: 1px solid #b3d9f2;
        border-left: 4px solid #3b82f6;
        border-radius: 12px;
        padding: 1rem 1.25rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-top: 1.5rem;
    }

    .info-alert-icon {
        font-size: 1.5rem;
        color: #3b82f6;
        flex-shrink: 0;
        width: 28px;
        text-align: center;
    }

    .info-alert-text {
        font-size: 0.95rem;
        color: #1e40af;
        line-height: 1.5;
        font-weight: 500;
    }

</style>

<main>
<!-- HERO -->
    <section class="hero">
      <div class="container hero-content py-5" data-animate>
        <h1 class="display-5 fw-bold mb-2"><?php echo get_phrase("Create_your_member_account") ?></h1>
        <p class="lead mb-4 text-white fs-md-4 fs-lg-3" style="letter-spacing: 1px; font-size: 1.5rem; margin-bottom: 1rem;"><?php echo get_phrase("Complete_your_profile_and_confirm_your_account._It’s_quick_and_easy.") ?></p>
      </div>
    </section>

  <section class="container py">
    <!-- Stepper (2 étapes) -->
    <ol class="stepper" role="list" aria-label="Étapes" <?php echo (get_user_language() === 'arabic') ? 'dir="rtl"' : 'dir="ltr"'; ?>>
      <li class="step-create-commaunaute is-active"><span class="num">1</span><span class="lbl"><?php echo get_phrase("Profile") ?></span></li>
      <li class="step-create-commaunaute"><span class="num">2</span><span class="lbl"><?php echo get_phrase("Summary") ?></span></li>
    </ol>

      <form  novalidate action="<?php echo site_url('admission/online_admission_student/submit/student'); ?>" method="post" id="studentform"
      class="js-validate studentform realtime-form container" enctype="multipart/form-data">
      <input type="hidden" name="<?=$this->security->get_csrf_token_name();?>" value="<?=$this->security->get_csrf_hash();?>" />
      <!-- === STEP 1 : PROFIL MEMBRE === -->
      <section class="card panel step-pane is-visible" data-step="1" <?php echo (get_user_language() === 'arabic') ? 'dir="rtl"' : 'dir="ltr"'; ?>>
        <div class="panel-head">
          <h2><?php echo get_phrase("Profile_information") ?></h2>
          <span class="legend-required"><span class="req">*</span> <?php echo get_phrase("Required_fields") ?></span>
        </div>

      <label class="field">
        <span class="field-label"><?php echo get_phrase("Name") ?><span class="req">*</span></span>
        <input id="lastName" type="text" placeholder="<?php echo get_phrase('last_name'); ?>"
                      class="form-control shadow-none rounded-end" name="first_name" required aria-required="true"
                      data-msg="Please enter your first name." data-error-class="u-has-error"
                      data-success-class="u-has-success">
        <div class="error" data-for="lastName"></div>
      </label>

      <label class="field">
        <span class="field-label"><?php echo get_phrase("First name") ?> <span class="req">*</span></span>
        <input id="firstName" type="text" placeholder="<?php echo get_phrase('first_name'); ?>"
                class="form-control shadow-none rounded-end" name="last_name" required aria-required="true"
                data-msg="Please enter your last name." data-error-class="u-has-error"
                data-success-class="u-has-success">
        <div class="error" data-for="firstName"></div>
      </label>


        <div class="grid-2">
          <label class="field">
            <span class="field-label">
              <?php echo get_phrase("Email") ?> <span class="req">*</span>
              <span class="info" data-tooltip="<?php echo get_phrase("Use_a_valid_email_address_(e.g.,_example@domain.com).") ?>">
                <i class="fa-solid fa-circle-info" aria-hidden="true"></i>
              </span>
            </span>
            <!-- <input id="gmail" type="email" placeholder="exemple@gmail.com" required aria-required="true"> -->
            <input id="gmail" type="email" placeholder="<?php echo get_phrase('email'); ?>"
                  class="form-control rounded-end shadow-none " name="student_email" required aria-required="true"
                  data-msg="Please enter a valid email address." data-error-class="u-has-error"
                  data-success-class="u-has-success">
            <div class="error" data-for="gmail"></div>
          </label>

       <?php 
        // Calculer la date limite pour 18 ans
        $today = date('Y-m-d');
        $eighteen_years_ago = date('Y-m-d', strtotime('-18 years'));
        ?>

        <label class="field">
            <span class="field-label">
                <?php echo get_phrase("Date_of_birth") ?> <span class="req">*</span>
                <span class="info" data-tooltip="<?php echo get_phrase('Enter_your_date_of_birth_(must_be_at_least_18_years_old).'); ?>">
                    <i class="fa-solid fa-circle-info" aria-hidden="true"></i>
                </span>
            </span>
            <input id="birthdate" type="date" class="form-control rounded-end shadow-none" 
                  name="date_of_birth" required aria-required="true"
                  max="<?php echo $eighteen_years_ago; ?>"
                  data-msg="Please enter your date of birth" 
                  data-error-class="u-has-error"
                  data-success-class="u-has-success">
            <div class="error" data-for="birthdate"></div>
        </label>
        </div>

        <div class="grid-2">
          <?php
            $is_rtl = (get_user_language() === 'arabic');
            $input_padding_style = $is_rtl 
                ? 'width:100%; padding-left: 45px; box-sizing: border-box;' 
                : 'width:100%; padding-right: 45px; box-sizing: border-box;';
            
            $icon_pos_style = $is_rtl
                ? 'position:absolute; left:35px; top:18px; cursor:pointer; color:#6b7280; font-size:15px; transition: color 0.2s; z-index: 100; text-decoration: none; border: none;'
                : 'position:absolute; right:35px; top:18px; cursor:pointer; color:#6b7280; font-size:15px; transition: color 0.2s; z-index: 100; text-decoration: none; border: none;';
          ?>
          <label class="field">
            <span class="field-label"><?php echo get_phrase("Password") ?> <span class="req">*</span></span>
            <div style="position:relative; width: 100%;">
                <input id="password" type="password"  class="form-control rounded-end shadow-none"
                    name="password-student" required aria-required="true" data-msg="Please enter a password" data-error-class="u-has-error"
                    data-success-class="u-has-success" style="<?php echo $input_padding_style; ?>">
                <i class="fa-regular fa-eye-slash" onclick="toggleStudentPassword('password', this, event)" style="<?php echo $icon_pos_style; ?>"></i>
            </div>
            <div class="error" data-for="password" style="margin-top: 45px;"></div>
          </label>

          <label class="field">
            <span class="field-label"><?php echo get_phrase("Confirm_password") ?> <span class="req">*</span></span>
            <div style="position:relative; width: 100%;">
                <input id="confirmPassword" type="password"  class="form-control rounded-end shadow-none"
                    name="repeat-password-student"  minlength="6" required aria-required="true" data-msg="Please repeat your password"
                    data-error-class="u-has-error" data-success-class="u-has-success" style="<?php echo $input_padding_style; ?>">
                <i class="fa-regular fa-eye-slash" onclick="toggleStudentPassword('confirmPassword', this, event)" style="<?php echo $icon_pos_style; ?>"></i>
            </div>
            <div class="error" data-for="confirmPassword" style="margin-top: 45px;"></div>
          </label>
        </div>

        <div class="panel-actions">
          <button type="button" class="btn btn-secondary prev" disabled><?php echo get_phrase("Back") ?></button>
          <button type="button" class="btn btn-primary next"><?php echo get_phrase("Continue") ?></button>
        </div>
      </section>

      <!-- === STEP 2 : RÉSUMÉ === -->
      <section class="card panel step-pane" data-step="2" <?php echo (get_user_language() === 'arabic') ? 'dir="rtl"' : 'dir="ltr"'; ?>>
        <div class="panel-head"><h2><?php echo get_phrase("Summary_&_Account_Creation") ?></h2></div>

        <div class="summary">
          <!-- Card grid will be injected here by JS -->
          <p><?php echo get_phrase("Check_your_information_and_click_on") ?> <strong><?php echo get_phrase("Create_account") ?></strong>.</p>
        </div>

        <!-- Info Alert Box -->
        <div class="info-alert-box">
          <div class="info-alert-icon">
            <i class="fa-solid fa-lightbulb"></i>
          </div>
          <div class="info-alert-text">
            <?php echo get_phrase("After_creation_you_can_complete_your_profile_directly_from_your_dashboard"); ?>
          </div>
        </div>

        <div class="panel-actions mt-4">
          <button type="button" class="btn btn-secondary prev"><?php echo get_phrase("Back") ?></button>
          <button id="submitBtn" type="submit" class="btn btn-primary-custom">
            <i class="fa-solid fa-user-plus"></i> <?php echo get_phrase("Create_account") ?>
          </button>
        </div>
      </section>
    </form>
  </section>
</main>

    <!--success overlay-->
    <div id="successOverlay" class="success-overlay" aria-hidden="true">
        <div class="success-card">
            <div class="success-icon">
                <i class="fa-solid fa-paper-plane"></i>
            </div>
            <h2><?php echo get_phrase("Request_sent") ?>!</h2>
            <p><?php echo get_phrase("Your_profile_has_been_successfully_created_on_Wayo.") ?></p>

            <div class="success-info-box">
                <div class="success-info-icon">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                </div>
                <div class="success-info-text">
                    <?php echo get_phrase("A_validation_email_will_be_sent_to_you_within_a_maximum_of") ?> <strong><?php echo get_phrase("24 hours.") ?></strong> <?php echo get_phrase("to confirm your registration.") ?>
                </div>
            </div>

            <button id="successBtn" type="button" class="btn btn-primary-custom w-100"><?php echo get_phrase("Discover_our_communities") ?></button>
        </div>
    </div>


<script>
(function () {
  // Helpers sans conflit
  const qs  = (s, ctx) => (ctx || document).querySelector(s);
  const qsa = (s, ctx) => Array.from((ctx || document).querySelectorAll(s));

  // DOM Ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

  function init() {
    // ===== NAV mobile =====
    const navToggle = qs('.nav-toggle'),
          mainNav   = qs('.main-nav');
    if (navToggle && mainNav) {
      navToggle.addEventListener('click', (e) => {
        e.stopPropagation(); // évite de bloquer dropdown Bootstrap
        mainNav.classList.toggle('nav-open');
      });
    }

   // ===== Stepper (2 étapes) =====
      const panes = qsa('.step-pane'),
      steps = qsa('.stepper .step-create-commaunaute'); // ✅ adapter ici
      let current = 0;
      let lastEmail = '';

      function goTo(i) {
        panes.forEach(p => p.classList.remove('is-visible'));

        // Met à jour les étapes visuelles
        steps.forEach((s, idx) => {
          s.classList.toggle('is-active', idx === i);
          s.classList.toggle('is-complete', idx < i);
        });

        if (panes[i]) panes[i].classList.add('is-visible');
        current = i;

        if (i === 1) updateSummary(); // étape Résumé
        if (typeof saveFormState === 'function') saveFormState();

        // window.scrollTo({ top: 0, behavior: 'smooth' });
      }
      window.goToStudent = goTo; // Exposer pour le gestionnaire de persistance

        qsa('.next').forEach(b => b.addEventListener('click', () => {
          if (!validate()) return;
          if (current < panes.length - 1) goTo(current + 1);
        }));

        qsa('.prev').forEach(b => b.addEventListener('click', () => {
          goTo(Math.max(0, current - 1));
        }));

        // ✅ Navigation par clic sur le stepper
        steps.forEach((s, idx) => {
          s.style.cursor = 'pointer';
          s.addEventListener('click', () => {
            if (idx < current) {
              // Retour en arrière : toujours autorisé
              goTo(idx);
            } else if (idx > current) {
              // Avancement : valide l'étape actuelle (et les intermédiaires si besoin)
              // Pour 2 étapes, on valide juste l'étape 0 pour aller à l'étape 1
              if (validate()) {
                goTo(idx);
              }
            }
          });
        });

        // Initialisation intelligente avec persistance
        const saved = sessionStorage.getItem('wayo_student_form_state');
        if (!saved) {
            goTo(0);
        }

    function updateSummary() {
      const summaryDiv = qs('.summary');
      if (!summaryDiv) return;

      const last  = qs('#lastName')?.value?.trim() || '—';
      const first = qs('#firstName')?.value?.trim() || '—';
      const mail  = qs('#gmail')?.value?.trim() || '—';
      const birth = qs('#birthdate')?.value || '—';

      summaryDiv.innerHTML = `
          <div class="summary-grid">
              <!-- Profile Card -->
              <div class="summary-card" style="cursor: pointer;" onclick="goToStudent(0)">
                  <div class="summary-card-header">
                      <div class="summary-card-icon"><i class="fa-solid fa-user"></i></div>
                      <h3><?php echo get_phrase("Profile"); ?></h3>
                  </div>
                  <div class="summary-card-body">
                      <div class="summary-item"><span class="summary-label"><?php echo get_phrase("Name"); ?>:</span><span class="summary-value">${last} ${first}</span></div>
                      <div class="summary-item"><span class="summary-label"><?php echo get_phrase("Email"); ?>:</span><span class="summary-value">${mail}</span></div>
                      <div class="summary-item"><span class="summary-label"><?php echo get_phrase("Date_of_birth"); ?>:</span><span class="summary-value">${birth}</span></div>
                  </div>
              </div>

              <!-- Account Status Card -->
              <div class="summary-card">
                  <div class="summary-card-header">
                      <div class="summary-card-icon"><i class="fa-solid fa-circle-info"></i></div>
                      <h3><?php echo get_phrase("Account_Status"); ?></h3>
                  </div>
                  <div class="summary-card-body">
                      <div class="summary-item">
                          <span class="summary-label"><?php echo get_phrase("Status"); ?>:</span>
                          <span class="summary-value">
                              <span class="status-badge"><?php echo get_phrase("Account_ready_to_create"); ?></span>
                          </span>
                      </div>
                      <div class="summary-item">
                          <span class="summary-label"><?php echo get_phrase("Type"); ?>:</span>
                          <span class="summary-value"><?php echo get_phrase("Member"); ?></span>
                      </div>
                  </div>
              </div>
          </div>
          <p class="mt-4"><?php echo get_phrase("Check_your_information_and_click_on") ?> <strong><?php echo get_phrase("Create_account") ?></strong>.</p>
      `;
    }

    // ===== Duplication email =====
    function checkDuplication(type, value, input) {
        if (!value.trim()) return;
        if (type === 'email' && value === lastEmail) return;

        if (type === 'email') lastEmail = value;

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
            const errorEl = document.querySelector(`.error[data-for="${input.id}"]`);
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
        });
    }

    qs('#gmail')?.addEventListener('blur', () => {
        checkDuplication('email', qs('#gmail').value, qs('#gmail'));
    });

    // ===== Validation =====
    function setInvalid(el, msg) {
      if (!el) return false;
      el.classList.add('is-invalid');
      const err = document.querySelector(`.error[data-for="${el.id}"]`);
      if (err) {
        err.textContent = msg || '';
        err.style.setProperty('display', 'block', 'important');
      }
      return false;
    }

    function clearInvalid(el) {
      if (!el) return;
      el.classList.remove('is-invalid');
      const err = document.querySelector(`.error[data-for="${el.id}"]`);
      if (err) {
        err.textContent = '';
        err.style.setProperty('display', 'none', 'important');
      }
    }

    function isEmail(addr) {
      const v = (addr || '').trim().toLowerCase();
      return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v);
    }
    function notFuture(dateStr) {
      if (!dateStr) return false;
      const d = new Date(dateStr);
      const today = new Date();
      today.setHours(0, 0, 0, 0);
      return d <= today;
    }

    // Vérifs au blur
    [
      ['lastName', '<?php echo get_phrase("Name_required_(minimum_2_characters)."); ?>', v => v.trim().length >= 2],
      ['firstName', '<?php echo get_phrase("First_name_required_(minimum_2_characters)."); ?>', v => v.trim().length >= 2],
      ['gmail', '<?php echo get_phrase("Please_use_a_valid_email_address."); ?>', v => isEmail(v)],
      ['birthdate', '<?php echo get_phrase("Invalid_date_(you_must_be_at_least_18_years_old)."); ?>', v => notFuture(v)],
      ['password', '<?php echo get_phrase("Minimum_6_characters."); ?>', v => (v || '').length >= 6],
      ['confirmPassword', '<?php echo get_phrase("Passwords_do_not_match."); ?>', v => qs('#password')?.value === v && v.length >= 6],
    ].forEach(([id, msg, test]) => {
      const el = document.getElementById(id);
      if (!el) return;
      el.addEventListener('blur', () => { test(el.value) ? clearInvalid(el) : setInvalid(el, msg); });
      el.addEventListener('input', () => { if (current === 1) updateSummary(); }); // live résumé à l’étape 2
    });

    function validate() {
      const stepPane = panes[current];
      if (!stepPane) return true;
      const step = stepPane.getAttribute('data-step');

      if (step === '1') { // Étape profil membre
        const last = qs('#lastName');
        const first = qs('#firstName');
        const mail = qs('#gmail');
        const birth = qs('#birthdate');
        const pass = qs('#password');
        const conf = qs('#confirmPassword');
        let ok = true;

        if (!last.value.trim() || last.value.trim().length < 2) { ok = setInvalid(last, '<?php echo get_phrase("Name_required_(minimum_2_characters)."); ?>'); } else { clearInvalid(last); }
        if (!first.value.trim() || first.value.trim().length < 2) { ok = setInvalid(first, '<?php echo get_phrase("First_name_required_(minimum_2_characters)."); ?>'); } else { clearInvalid(first); }

        // Blocage si doublon email
        if (mail.getAttribute('data-duplicate') === 'true') {
            toastr?.warning('<?= get_phrase("this_email_already_exist"); ?>');
            return false;
        }

        if (!isEmail(mail.value)) {
          ok = setInvalid(mail, '<?php echo get_phrase("Please_use_a_valid_email_address."); ?>');
        } else {
          clearInvalid(mail);
        }
        if (!notFuture(birth.value)) { ok = setInvalid(birth, '<?php echo get_phrase("Invalid_date_(you_must_be_at_least_18_years_old)."); ?>'); } else { clearInvalid(birth); }
        if ((pass.value || '').length < 6) { ok = setInvalid(pass, '<?php echo get_phrase("Minimum_6_characters"); ?>'); } else { clearInvalid(pass); }
        if (conf.value !== pass.value || (conf.value || '').length < 6) { ok = setInvalid(conf, '<?php echo get_phrase("Passwords_do_not_match"); ?>'); } else { clearInvalid(conf); }
        return ok;
      }

      return true;
    }


    // ===== Tooltips clic/touch =====
    qsa('.field-label .info').forEach(el => {
      el.addEventListener('click', (e) => {
        e.stopPropagation();
        el.dataset.open = el.dataset.open === '1' ? '0' : '1';
        if (el.dataset.open === '1') {
          setTimeout(() => { el.dataset.open = '0'; }, 3000);
        }
      });
    });

    document.addEventListener('click', () => {
      qsa('.field-label .info[data-open="1"]').forEach(i => i.dataset.open = '0');
    });
  }
})();

function toggleStudentPassword(inputId, icon, e) {
  if (e) {
    e.preventDefault();
    e.stopPropagation();
  }
  const input = document.getElementById(inputId);
  if (input && icon) {
    const isPassword = input.getAttribute("type") === "password";
    input.setAttribute("type", isPassword ? "text" : "password");
    if (isPassword) {
      icon.classList.remove("fa-eye-slash");
      icon.classList.add("fa-eye");
      icon.style.color = "#F47A1F"; // Orange
    } else {
      icon.classList.remove("fa-eye");
      icon.classList.add("fa-eye-slash");
      icon.style.color = "#6b7280"; // Gray
    }
  }
}
</script>


<script>
document.addEventListener('DOMContentLoaded', function () {
  // === Toastr config ===
  toastr.options = {
    closeButton: true,
    progressBar: true,
    positionClass: 'toast-top-right',
    timeOut: 3000,
    showMethod: 'fadeIn',
    hideMethod: 'fadeOut'
  };

  const studentForm = document.getElementById('studentform');
  const submitBtn = document.getElementById('submitBtn');

  if (studentForm && submitBtn) {
    // ✅ Empêche tout comportement natif de soumission
    studentForm.onsubmit = function (event) {
      event.preventDefault();
      event.stopImmediatePropagation();

      console.log("🟢 Interception du formulaire via JS");

      if (!studentForm.checkValidity()) {
        studentForm.reportValidity();
        return false;
      }

      // Récupération du CSRF token
      const csrfInput = studentForm.querySelector('input[name="<?= $this->security->get_csrf_token_name(); ?>"]');
      const csrfName = csrfInput.name;
      const csrfHash = csrfInput.value;

      const formData = new FormData(studentForm);
      formData.append(csrfName, csrfHash);

      fetch(studentForm.action, {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        console.log("🟢 Réponse reçue:", data);

        // Mise à jour du token CSRF
        if (data.csrf) {
          csrfInput.name = data.csrf.csrfName;
          csrfInput.value = data.csrf.csrfHash;
        }

        if (data.status) {
          // toastr.success(data.message);
          if (typeof clearSavedState === 'function') clearSavedState();
          studentForm.reset();
          
          // Show success popup instead of auto-redirect
          const overlay = document.getElementById('successOverlay');
          if (overlay) overlay.classList.add('is-visible');
        } else {
          toastr.error(data.message || 'Erreur inconnue.');
        }
      })
      .catch(error => {
        console.error("❌ Erreur fetch:", error);
        toastr.error('Erreur réseau ou serveur.');
      });

      return false; // ✅ Empêche toute redirection ou affichage JSON
    };
  }
});


  // Password match validation
  const password = document.getElementById('password-student');
  const repeatPassword = document.getElementById('repeat-password-student');
  const errorMessage = document.getElementById('errorMessage');

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

  if (password && repeatPassword && errorMessage) {
    repeatPassword.addEventListener('input', function () {
      if (password.value !== repeatPassword.value) {
        errorMessage.classList.remove('display-none');
        submitBtn.disabled = true;
      } else {
        errorMessage.classList.add('display-none');
        submitBtn.disabled = false;
      }
    });
  }
</script>

<!-- ==========================================
     PERSISTENCE MANAGER (Student Form)
     ========================================== -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const STORAGE_KEY = 'wayo_student_form_state';
    const form = document.getElementById('studentform');
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

        // Capture step index from DOM
        const visiblePane = document.querySelector('.step-pane.is-visible');
        if (visiblePane) {
            data._step = parseInt(visiblePane.dataset.step) - 1;
        }

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

            // Restore Step
            if (typeof data._step !== 'undefined' && typeof window.goToStudent === 'function') {
                setTimeout(() => window.goToStudent(data._step), 100);
            }

        } catch (e) {
            console.error("Student Form Persistence error:", e);
        }
    };

    window.clearSavedState = function() {
        sessionStorage.removeItem(STORAGE_KEY);
    };

    // Triggers
    form.addEventListener('input', saveFormState);
    form.addEventListener('change', saveFormState);

    // Run restoration
    restoreFormState();
});
</script>

</html>
