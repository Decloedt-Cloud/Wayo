
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
    <ol class="stepper" role="list" aria-label="Étapes">
      <li class="step-create-commaunaute is-active"><span class="num">1</span><span class="lbl"><?php echo get_phrase("Profile") ?></span></li>
      <li class="step-create-commaunaute"><span class="num">2</span><span class="lbl"><?php echo get_phrase("Summary") ?></span></li>
    </ol>

      <form  novalidate action="<?php echo site_url('admission/online_admission_student/submit/student'); ?>" method="post" id="studentform"
      class="js-validate studentform realtime-form container" enctype="multipart/form-data">
      <input type="hidden" name="<?=$this->security->get_csrf_token_name();?>" value="<?=$this->security->get_csrf_hash();?>" />
      <!-- === STEP 1 : PROFIL MEMBRE === -->
      <section class="card panel step-pane is-visible" data-step="1">
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
          <label class="field">
            <span class="field-label"><?php echo get_phrase("Password") ?> <span class="req">*</span></span>
            <input id="password" type="password"  class="form-control rounded-end shadow-none"
                name="password-student" required aria-required="true" data-msg="Please enter a password" data-error-class="u-has-error"
                data-success-class="u-has-success">
            <div class="error" data-for="password"></div>
          </label>

          <label class="field">
            <span class="field-label"><?php echo get_phrase("Confirm_password") ?> <span class="req">*</span></span>
            <input id="confirmPassword" type="password"  class="form-control rounded-end shadow-none"
                name="repeat-password-student"  minlength="6" required aria-required="true" data-msg="Please repeat your password"
                data-error-class="u-has-error" data-success-class="u-has-success">
            <div class="error" data-for="confirmPassword"></div>
          </label>
        </div>

        <div class="panel-actions">
          <button type="button" class="btn btn-secondary prev" disabled><?php echo get_phrase("Back") ?></button>
          <button type="button" class="btn btn-primary next"><?php echo get_phrase("Continue") ?></button>
        </div>
      </section>

      <!-- === STEP 2 : RÉSUMÉ === -->
      <section class="card panel step-pane" data-step="2">
        <div class="panel-head"><h2><?php echo get_phrase("Summary_&_Account_Creation") ?></h2></div>

        <div class="grid-2">
          <div class="summary-member">
            <h3 class="summary-title"><?php echo get_phrase("Your_information") ?></h3>
            <dl id="summaryMember" class="summary-list"></dl>
          </div>

          <aside class="publish">
            <div class="card soft">
              <h3><?php echo get_phrase("Checklist") ?></h3>
              <ul class="checklist">
                <li><i class="fa-regular fa-circle-check"></i> <?php echo get_phrase("Full_name") ?></li>
                <li><i class="fa-regular fa-circle-check"></i> <?php echo get_phrase("Valid_Gmail") ?></li>
                <li><i class="fa-regular fa-circle-check"></i> <?php echo get_phrase("Date_of_birth") ?></li>
                <li><i class="fa-regular fa-circle-check"></i> <?php echo get_phrase("Password_confirmed") ?></li>
              </ul>
              <button id="submitBtn" type="submit" class="btn btn-primary w-100">
                <i class="fa-solid fa-user-plus"></i> <?php echo get_phrase("Create_account") ?>
              </button>
              <p class="muted small mt"><?php echo get_phrase("You_can_complete_your_information_later.") ?></p>
            </div>
          </aside>
        </div>

        <div class="panel-actions">
          <button type="button" class="btn btn-secondary prev"><?php echo get_phrase("Back") ?></button>
        </div>
      </section>
    </form>
  </section>
</main>


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

        // Initialisation intelligente avec persistance
        const saved = sessionStorage.getItem('wayo_student_form_state');
        if (!saved) {
            goTo(0);
        }

    // ===== Résumé =====
    function dd(parent, t, v) {
      const DT = document.createElement('dt');
      DT.textContent = t;
      const DD = document.createElement('dd');
      DD.textContent = v;
      parent.appendChild(DT);
      parent.appendChild(DD);
    }

    function updateSummary() {
      const s = qs('#summaryMember');
      if (!s) return;
      s.innerHTML = '';
      const last  = qs('#lastName')?.value?.trim() || '—';
      const first = qs('#firstName')?.value?.trim() || '—';
      const mail  = qs('#gmail')?.value?.trim() || '—';
      const birth = qs('#birthdate')?.value || '—';
      dd(s, '<?php echo get_phrase("Last_name") ?>', last);
      dd(s, '<?php echo get_phrase("First_name") ?>', first);
      dd(s, '<?php echo get_phrase("Gmail") ?>', mail);
      dd(s, '<?php echo get_phrase("Date_of_birth") ?>', birth);
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
      if (err) err.textContent = msg || '';
      return false;
    }

    function clearInvalid(el) {
      if (!el) return;
      el.classList.remove('is-invalid');
      const err = document.querySelector(`.error[data-for="${el.id}"]`);
      if (err) err.textContent = '';
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
          toastr.success(data.message);
          if (typeof clearSavedState === 'function') clearSavedState();
          studentForm.reset();
          setTimeout(() => {
            window.location.href = '<?= site_url('/home/communities'); ?>';
          }, 2000);
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
