
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
</style>

<main class="mt-5">
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
              <span class="info" data-tooltip="Utilisez une adresse se terminant par @gmail.com.">
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

          <label class="field">
            <span class="field-label">
              <?php echo get_phrase("Date_of_birth") ?> <span class="req">*</span>
              <span class="info" data-tooltip="Entrez votre date de naissance (pas de date future).">
                <i class="fa-solid fa-circle-info" aria-hidden="true"></i>
              </span>
            </span>
            <input id="birthdate" type="date" class="form-control rounded-end shadow-none" name="date_of_birth" required aria-required="true"
                data-msg="Please enter your date of birth" data-error-class="u-has-error"
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

        window.scrollTo({ top: 0, behavior: 'smooth' });
      }

        qsa('.next').forEach(b => b.addEventListener('click', () => {
          if (!validate()) return;
          if (current < panes.length - 1) goTo(current + 1);
        }));

        qsa('.prev').forEach(b => b.addEventListener('click', () => {
          goTo(Math.max(0, current - 1));
        }));

        goTo(0);

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
      dd(s, 'Nom', last);
      dd(s, 'Prénom', first);
      dd(s, 'Gmail', mail);
      dd(s, 'Date de naissance', birth);
    }

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

    function isGmail(addr) {
      const v = (addr || '').trim().toLowerCase();
      return /^[^\s@]+@gmail\.com$/.test(v);
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
      ['lastName', 'Nom obligatoire (2 caractères min).', v => v.trim().length >= 2],
      ['firstName', 'Prénom obligatoire (2 caractères min).', v => v.trim().length >= 2],
      ['gmail', 'Veuillez utiliser une adresse @gmail.com valide.', v => isGmail(v)],
      ['birthdate', 'Date invalide (pas de date future).', v => notFuture(v)],
      ['password', '6 caractères minimum.', v => (v || '').length >= 6],
      ['confirmPassword', 'Les mots de passe ne correspondent pas.', v => qs('#password')?.value === v && v.length >= 6],
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

        if (!last.value.trim() || last.value.trim().length < 2) { ok = setInvalid(last, 'Nom obligatoire (2 caractères min).'); } else { clearInvalid(last); }
        if (!first.value.trim() || first.value.trim().length < 2) { ok = setInvalid(first, 'Prénom obligatoire (2 caractères min).'); } else { clearInvalid(first); }
        if (!isGmail(mail.value)) { ok = setInvalid(mail, 'Veuillez utiliser une adresse @gmail.com valide.'); } else { clearInvalid(mail); }
        if (!notFuture(birth.value)) { ok = setInvalid(birth, 'Date invalide (pas de date future).'); } else { clearInvalid(birth); }
        if ((pass.value || '').length < 6) { ok = setInvalid(pass, '6 caractères minimum.'); } else { clearInvalid(pass); }
        if (conf.value !== pass.value || (conf.value || '').length < 6) { ok = setInvalid(conf, 'Les mots de passe ne correspondent pas.'); } else { clearInvalid(conf); }
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


</html>
