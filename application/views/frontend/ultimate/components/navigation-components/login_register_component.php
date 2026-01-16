<style>
     :root{
  --white:#fff; --text:#171717; --muted:#6b7280; --line:#eee; --line-2:#f3f4f6;
  --orange:#F47A1F; --orange-2:#fbb040; --orange-3:#ffebd8;
  --green:#16a34a; --red:#dc2626;
  --bg:#ffffff;
  --shadow:0 10px 30px rgba(0,0,0,.08);
  --shadow-2:0 30px 80px rgba(0,0,0,.12);
  --radius:16px;
  --font:'Urbanist',system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;
  --logo-h-desktop: 88px; --logo-h-tablet: 60px; --logo-h-mobile: 52px;
}





/* ===== Connexion inline ===== */
/* === Login dropdown compact, ancré au bouton === */
#loginInline[hidden]{ display:none !important; }


.login-inline{
  position: fixed;                 /* on la positionne par JS */
   z-index: 1060; /* > Bootstrap offcanvas (1050) */
  pointer-events: none;            /* clics ignorés hors de la carte */
  animation: dropdownFade .14s ease both;
}
@keyframes dropdownFade{ from{opacity:0; transform:translateY(-6px)} to{opacity:1; transform:none} }

.login-card{
  width: 340px;                    /* compact */
  max-height: 78vh;
  pointer-events: auto;            /* clics actifs sur la carte */
  background:#fff;
  border:1px solid var(--line,#ececec);
  border-radius:14px;
  box-shadow:0 16px 40px rgba(0,0,0,.12);
  padding:12px 12px 14px;
  position: relative;
  right:0px;
  top: 20px;
}

/* caret (triangle) */
.login-card::after{
  content:"";
  position:absolute;
  top:-7px;
  right:18px;                      /* pointe vers le bouton (côté droit) */
  width:14px; height:14px;
  background:#fff;
  border-left:1px solid var(--line,#ececec);
  border-top:1px solid var(--line,#ececec);
  transform: rotate(45deg);
}
.signup-style, .signin-style, .backlogin{
  color:inherit;
  text-decoration:none;
  color: #fc7b30;
}
.signup-style:hover,.signin-style:hover, .backlogin:hover{
  color: #fc7b30;
}

/* typo & éléments, version compacte */
.login-head{display:flex;align-items:center;justify-content:space-between;padding:6px 2px 4px;}
.login-head h2{margin:0;font-size:1.2rem;font-weight:900;color:#20286f;}
.login-close{border:0;background:#f2f3f7;color:#111;width:32px;height:32px;border-radius:10px;font-size:18px;line-height:1;display:grid;place-items:center;cursor:pointer;}
.login-pane{padding:2px 0;}
.login-field{display:grid;gap:4px;margin:8px 0;}
.login-field span{font-weight:800;}
.login-field input{height:46px;padding:0 12px;border-radius:12px;border:2px solid #ececec;font:inherit;outline:none;transition:.15s;background:#fff;}
.login-field input:focus{border-color:#ffd3b2;box-shadow:0 0 0 4px rgba(244,122,31,.12);}
.login-row{display:flex;align-items:center;justify-content:space-between;gap:10px;margin:2px 0 8px;}
.login-check{display:inline-flex;align-items:center;gap:.45rem;font-weight:600;color:#3b3b3b;}
.login-check input{width:16px;height:16px;}
.login-link{font-weight:900;color:#Fc7b30 !important;}
.login-submit{width:100%;height:50px;border-radius:12px;font-size:1rem;}
.login-switch{margin:.65rem 0 0;text-align:center;color:#666;font-weight:600;font-size:.95rem;}

/* hauteur d’en-tête utilisée si fallback centré mobile */
:root{ --header-h: 86px; }
@media (max-width:1024px){ :root{ --header-h: 66px; } }
@media (max-width:640px){
  :root{ --header-h: 58px; }
  .login-card{ width: min(94vw, 360px); } /* un peu plus souple sur mobile */
}
/* === Choix d'inscription: cartes Membre / Mentor === */
.role-chooser{ display:grid; gap:10px; margin:6px 0 8px; }
.role-card{
  text-decoration:none;
  display:grid; grid-template-columns:auto 1fr auto; align-items:center; gap:10px;
  padding:12px; border-radius:14px; background:#fff;
  border:1px solid var(--line,#ececec);
  box-shadow:0 8px 22px rgba(0,0,0,.06);
  transition:transform .12s ease, box-shadow .12s ease, border-color .12s ease, background .12s ease;
}
.role-card:hover{
  transform:translateY(-1px);
  box-shadow:0 12px 28px rgba(0,0,0,.08);
  border-color:#ffd7b7;
  background:#fffdf9;
}
.role-icon{
  width:42px; height:42px; border-radius:12px;
  display:grid; place-items:center;
  background:#ffefe1; color:#F47A1F;
  border:1px solid #ffd7b7;
}
.role-icon.star{ background:#fff4cc; color:#f3a400; border-color:#ffe29a; }
.role-text h3{ margin:0; font-size:1.02rem; font-weight:900; color:#111; }
.role-text p{ margin:2px 0 0; font-size:.92rem; color:#6b7280; }
.role-arrow{
  width:28px; height:28px; border-radius:8px;
  display:grid; place-items:center;
  background:#f6f7fb; color:#111; font-weight:900; font-size:18px;
  border:1px solid #ececec;
}
@media (max-width:420px){
  .role-text h3{ font-size:1rem; }
  .role-text p{ font-size:.9rem; }
}
</style>

      <!-- Header -->
  <header class="">
    <div class="" id="openLoginBtnM"></div>
  </header>

<section id="loginInline" class="login-inline" hidden>
    <div class="container">
      <div class="login-card" role="dialog" aria-labelledby="loginTitle">
        <div class="login-head">
          <h2 id="loginTitle"><?php echo get_phrase("Sign_in") ?></h2>
          <button class="login-close" id="loginClose" aria-label="Fermer">×</button>
        </div>

        <!-- Formulaire : Connexion -->
        <form id="login-form" class="login-pane" action="<?php echo site_url('login/validate_login_frontend'); ?>" method="post" novalidate>
          <div id="loginError" class="text-danger display-none" style="background-color: #fef2f2; border: none; border-radius: 12px; padding: 5px 22px; width: fit-content; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); color: #b91c1c; font-family: 'Inter', sans-serif; font-size: 14px; font-weight: 500; transition: all 0.3s ease; margin-left: auto; margin-right: auto;"></div>
          <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
          <label class="login-field" for="loginEmail">
            <span><?php echo get_phrase("e-mail") ?>*</span>
            <input type="email" id="loginEmail" placeholder="<?php echo get_phrase("e-mail") ?>" aria-describedby="emailHelp" name="login_email">
          </label>
 
          <label for="loginPassword" class="login-field">
            <span><?php echo get_phrase("password") ?> *</span>
            <div style="position:relative; width: 100%;">
              <input type="password" id="loginPassword" placeholder="<?php echo get_phrase("password") ?>" name="login_password" style="width:100%; padding-right: 40px; box-sizing: border-box;">
              <i id="togglePassword" class="fa-regular fa-eye-slash" onclick="toggleLoginPassword(event)" style="position:absolute; right:12px; top:23px; bottom:0; margin:auto; height:18px; line-height:18px; cursor:pointer; color:#6b7280; font-size:13px; transition: color 0.2s; z-index: 100; text-decoration: none; border: none;"></i>
            </div>
          </label>
 
          <div class="login-row">
            <label class="login-check">
              <input type="checkbox"> <span><?php echo get_phrase("Remember_me") ?></span>
            </label>
            <a href="#" class="login-link"><?php echo get_phrase("Forgot_password") ?>&nbsp;?</a>
          </div>
 
          <button type="submit" id="loginSubmit" class="btn btn-accent login-submit"><?php echo get_phrase("Log_in") ?></button>
          <p class="login-switch">Ou <a href="#" class="signup-style" id="goSignup"><?php echo get_phrase("Sign_up") ?></a></p>
        </form>

        <!-- Formulaire : Mot de passe oublié -->
        <form id="forget-form" class="login-pane" hidden method="post" enctype="multipart/form-data" action="<?php echo site_url('login/send_reset_link'); ?>">
          <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
          <!--  -->
          <label class="login-field" for="forgetEmail">
            <span><?php echo get_phrase("Email") ?>*</span>
            <input id="forgotEmail" name="email" type="text" id="forgotEmail" placeholder="<?php echo get_phrase("Enter_your_email") ?>" required data-msg="<?php echo get_phrase("required") ?>">
          </label>
          <button type="submit" id="registerSubmit" class="btn btn-accent login-submit">
            <?php echo get_phrase("sent_password_reset_link") ?>
          </button>

          <p class="login-switch">
            <a href="#" class="backlogin" id="backToLogin"><?php echo get_phrase("Back_to_login") ?></a>
          </p>
        </form>

        <!-- Formulaire : Inscription -->
       <form id="signupForm" class="login-pane" hidden novalidate>
        <div class="role-chooser">
          <a class="role-card" href="<?php echo site_url('admission/online_admission_student'); ?>" id="ctaMember">
            <div class="role-icon">
              <svg viewBox="0 0 24 24" width="22" height="22" aria-hidden="true">
                <path fill="currentColor" d="M12 13a5 5 0 1 1 5-5 5.006 5.006 0 0 1-5 5Zm0 2c-4.418 0-8 2.239-8 5v2h16v-2c0-2.761-3.582-5-8-5Z"/>
              </svg>
            </div>
            <div class="role-text">
              <h3><?php echo get_phrase("Sign_up_as_a_Member") ?></h3>
              <p><?php echo get_phrase("Join_communities,_follow_courses_& live_sessions.") ?></p>
            </div>
            <span class="role-arrow" aria-hidden="true">›</span>
          </a>

          <a class="role-card" href="<?php echo site_url('admission/online_admission'); ?>" id="ctaMentor">
            <div class="role-icon star">
              <svg viewBox="0 0 24 24" width="22" height="22" aria-hidden="true">
                <path fill="currentColor" d="m12 2 2.6 5.7 6.3.9-4.6 4.5 1.1 6.3L12 16.9 6.6 19.4l1.1-6.3L3 8.6l6.3-.9L12 2z"/>
              </svg>
            </div>
            <div class="role-text">
              <h3><?php echo get_phrase("Sign_up_as_a_Mentor") ?></h3>
              <p><?php echo get_phrase("Create_a_community,_courses_&_live_sessions.") ?></p>
            </div>
            <span class="role-arrow" aria-hidden="true">›</span>                                                                                                                                                        
          </a>
        </div>
          <p class="login-switch">
            <?php echo get_phrase("Already_registered ?") ?> <a href="#" class="signin-style" id="goLogin"><?php echo get_phrase("Log in") ?></a>
          </p>
        </form>
      </div>
    </div>
</section>


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

function toggleLoginPassword(e) {
    if (e) { 
      e.preventDefault(); 
      e.stopPropagation();
    }
    
    const password = document.querySelector("#loginPassword");
    const icon = document.querySelector("#togglePassword");

    if (password && icon) {
        // Toggle the type attribute
        const type = password.getAttribute("type") === "password" ? "text" : "password";
        password.setAttribute("type", type);
        
        // Toggle the icon class and style
        if (type === "text") {
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

<script type="text/javascript">
  var checkEmailExistsUrl = '<?php echo site_url('login/check_email_exists'); ?>';
  var csrfTokenName = '<?php echo $this->security->get_csrf_token_name(); ?>';
  var loginValidateUrl = '<?php echo site_url('login/validate_credentials'); ?>';
  var csrfTokenName = '<?php echo $this->security->get_csrf_token_name(); ?>';
  var inputs = document.querySelectorAll('.information');
  var passwordInputs = document.querySelectorAll('.password');
  var selects = document.getElementsByTagName('select');
  window.emailAlreadyInUse = '<?php echo get_phrase("email_already_in_use"); ?>';
  window.passwordsDoNotMatch = '<?php echo get_phrase("passwords_do_not_match"); ?>';
  window.baseUrl = '<?php echo base_url(); ?>';
    if (!window.baseUrl.endsWith('/')) {
        window.baseUrl += '/';
    }

  for (var i = 0; i < inputs.length; i++) {
    inputs[i].addEventListener('input', function () {
      if (this.value != "")
        this.classList.remove("invalid");
    });
  }

  for (var i = 0; i < passwordInputs.length; i++) {
    passwordInputs[i].addEventListener('input', function () {
      if (this.value != "" && checkPassword()) {
        this.classList.remove("invalid");
      }
    });
  }

  for (var i = 0; i < selects.length; i++) {
    selects[i].addEventListener('change', function () {
      if (this.value != "") {
        this.classList.remove("invalid");
      } else {
        this.classList.add("invalid");
      }
    });
  }

  function check_email(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
  }

  document.getElementById("loginSubmit").addEventListener("click", function (event) {
    if (!loginSubmit()) {
      event.preventDefault();
    }
  });

  function loginSubmit() {
    var email = document.getElementById("loginEmail").value;
    var password = document.getElementById("loginPassword").value;

    emailExists(email).then((status) => {
      if (status) {
        error_notify('<?php echo get_phrase("E-mail_address_not_in_use") ?>');
      } else {
        validateCredentials(email, password).then((status) => {
          if (status) {
            document.getElementById("login-form").submit();
          } else {
            error_notify('<?php echo get_phrase("credentials_incorrect") ?>');
          }
        });
      }
    });
  }

  function validateCredentials(email, password) {
    return new Promise((resolve, reject) => {
      var emailInput = document.getElementById("loginEmail");
      var passwordInput = document.getElementById("loginPassword");
      var csrfName = $('input[name="<?= $this->security->get_csrf_token_name(); ?>"]').attr('name');
      var csrfHash = $('input[name="<?= $this->security->get_csrf_token_name(); ?>"]').val();

      $.ajax({
        type: "POST",
        url: "<?php echo site_url('login/validate_credentials'); ?>",
        data: {email: email, password: password, [csrfName]: csrfHash},
        dataType: 'json',
        success: function(response){
          if (response.status == true) {
            resolve(true);
          } else {
            resolve(false);
          }
          var newCsrfName = response.csrf.csrfName;
          var newCsrfHash = response.csrf.csrfHash;
          $('input[name="' + newCsrfName + '"]').val(newCsrfHash);
        },
        error: function(error) {
          console.error("Error:", error);
          resolve(false);
        }
      });
    });
  }
</script>

 <script>
  /* Drawer (pour mobile) : rien à faire, c’est CSS avec #drawerToggle */

/* Parallax doux des orbes + reveal */
const prefersReduced = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
if (!prefersReduced) {
  const orbs = document.querySelectorAll(".bg-orbs .orb");
  const lerp = (a,b,t)=>a+(b-a)*t;
  window.addEventListener("scroll", ()=>{
    const t = Math.min(1, window.scrollY/1200);
    orbs.forEach((o,i)=>{
      const dx = lerp(0, (i%2? 22 : -22), t);
      const dy = lerp(0, (i%2? -18 : 26), t);
      o.style.transform = `translate(${dx}px, ${dy}px)`;
    });
  }, {passive:true});
  const io = new IntersectionObserver((entries)=>{
    entries.forEach((en)=>{ if(en.isIntersecting){ en.target.classList.add("in"); io.unobserve(en.target); }});
  },{threshold:.14});
  document.querySelectorAll(".reveal").forEach(el=>io.observe(el));
}

/* Tabs des fonctionnalités (CSS radios) – pas de JS requis */

/* ===== Connexion inline (affiche sous la navbar) ===== */
document.addEventListener("DOMContentLoaded", function () {
  const box        = document.getElementById('loginInline');
  const btnOpen    = document.getElementById('openLoginBtn');      
  const btnOpenM   = document.getElementById('openLoginBtnM');     
  const btnClose   = document.getElementById('loginClose');        
  const formLogin  = document.getElementById('login-form');
  const formSignup = document.getElementById('signupForm');
  const formForgot = document.getElementById('forget-form');
  const goSignup   = document.getElementById('goSignup');
  const goLogin    = document.getElementById('goLogin');
  const forgotLink = document.querySelector('.login-link');
  const backToLogin= document.getElementById('backToLogin');
  const loginTitle = document.getElementById('loginTitle');

  const isIOS = /iP(hone|od|ad)/.test(navigator.platform) || (navigator.userAgent.includes("Mac") && "ontouchend" in document);
  let outsideClickHandler = null;

  /* ==========================================
        POSITION POPUP LOGIN (desktop seulement)
  =========================================== */
  function positionLoginDropdown(btn) {
    if (!box) return;
    const card = box.querySelector('.login-card');
    if (!card) return;

    // NE PAS POSITIONNER SUR MOBILE / iOS
    if (isIOS || window.innerWidth < 400) {
      // CSS s'occupe de la position
      return;
    }

    // Desktop : calculer position
    const w = Math.min(340, window.innerWidth * 0.94);
    card.style.width = w + 'px';
    const gap = 8;
    let left, top;

    if (!btn) {
      left = Math.max(8, (window.innerWidth - w) / 2);
      top  = parseInt(getComputedStyle(document.documentElement).getPropertyValue('--header-h')) + gap;
    } else {
      const r = btn.getBoundingClientRect();
      left = r.right - w;
      top  = r.bottom + gap;
      left = Math.max(8, Math.min(left, window.innerWidth - w - 8));
    }

    box.style.left = left + 'px';
    box.style.top  = top + 'px';
  }

  /* ==========================================
        OUVRIR / FERMER POPUP LOGIN
  =========================================== */
  function toggleLogin(show, btn) {
    const willShow = (show !== undefined) ? show : box.hasAttribute('hidden');

    if (!willShow) {
      box.setAttribute('hidden','');
      removeOutsideClickListener();
      return;
    }

    box.removeAttribute('hidden');

    // Desktop seulement : position dynamique
    positionLoginDropdown(btn);

    // Focus sur premier input visible
    const activeForm = !formLogin.hidden ? formLogin : !formSignup.hidden ? formSignup : formForgot;
    activeForm.querySelector('input')?.focus();

    addOutsideClickListener();
  }

  /* ==========================================
          CLIC EN DEHORS POUR FERMER
  =========================================== */
  function addOutsideClickListener() {
    removeOutsideClickListener();
    outsideClickHandler = function(e) {
      const card = box.querySelector('.login-card');
      if (!card.contains(e.target) 
        && !e.target.closest('#openLoginBtn') 
        && !e.target.closest('#openLoginBtnM')
      ) {
        toggleLogin(false);
      }
    };
    document.addEventListener('pointerdown', outsideClickHandler, { capture: true });
  }

  function removeOutsideClickListener() {
    if (outsideClickHandler) {
      document.removeEventListener('pointerdown', outsideClickHandler, { capture: true });
      outsideClickHandler = null;
    }
  }

  /* ==========================================
              BOUTONS OPEN / CLOSE
  =========================================== */
  btnOpen?.addEventListener('click', (e) => { e.preventDefault(); toggleLogin(true, btnOpen); });
  btnOpenM?.addEventListener('click', (e) => { e.preventDefault(); toggleLogin(true, btnOpenM); });
  btnClose?.addEventListener('click', () => toggleLogin(false));

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && !box.hasAttribute('hidden')) toggleLogin(false);
  });

  /* ==========================================
                    SWITCH FORM
  =========================================== */
  goSignup?.addEventListener('click', (e) => {
    e.preventDefault();
    formLogin.hidden = true; formForgot.hidden = true; formSignup.hidden = false;
    loginTitle.textContent = "<?php echo get_phrase('Create_an_account') ?>";
    formSignup.querySelector('input')?.focus();
  });

  goLogin?.addEventListener('click', (e) => {
    e.preventDefault();
    formSignup.hidden = true; formForgot.hidden = true; formLogin.hidden = false;
    loginTitle.textContent = "<?php echo get_phrase('Log_in') ?>";
    formLogin.querySelector('input')?.focus();
  });

  forgotLink?.addEventListener('click', (e) => {
    e.preventDefault();
    formLogin.hidden = true; formSignup.hidden = true; formForgot.hidden = false;
    loginTitle.textContent = "<?php echo get_phrase('Forgot_password') ?>";
    formForgot.querySelector('input')?.focus();
  });

  backToLogin?.addEventListener('click', (e) => {
    e.preventDefault();
    formForgot.hidden = true; formSignup.hidden = true; formLogin.hidden = false;
    loginTitle.textContent = "<?php echo get_phrase('Log_in') ?>";
    formLogin.querySelector('input')?.focus();
  });

  // Empêcher soumission (demo)
  [formLogin, formSignup].forEach(f => f?.addEventListener('submit', (e) => e.preventDefault()));

  /* ==========================================
        REPOSITIONNEMENT AU RESIZE / SCROLL (desktop seulement)
  =========================================== */
  ['scroll','resize'].forEach(evt => {
    window.addEventListener(evt, () => {
      if (box?.hasAttribute && !box.hasAttribute('hidden') && window.innerWidth >= 480 && !isIOS) {
        const btn = window.innerWidth < 480 ? btnOpenM : btnOpen;
        positionLoginDropdown(btn);
      }
    }, { passive: true });
  });

});
</script>





<script type="text/javascript">
  var checkEmailExistsUrl = '<?php echo site_url('login/check_email_exists'); ?>';
  var csrfTokenName = '<?php echo $this->security->get_csrf_token_name(); ?>';
  var loginValidateUrl = '<?php echo site_url('login/validate_credentials'); ?>';
  var csrfTokenName = '<?php echo $this->security->get_csrf_token_name(); ?>';
  var inputs = document.querySelectorAll('.information');
  var passwordInputs = document.querySelectorAll('.password');
  var selects = document.getElementsByTagName('select');
  window.emailAlreadyInUse = '<?php echo get_phrase("email_already_in_use"); ?>';
  window.passwordsDoNotMatch = '<?php echo get_phrase("passwords_do_not_match"); ?>';
  window.baseUrl = '<?php echo base_url(); ?>';
    if (!window.baseUrl.endsWith('/')) {
        window.baseUrl += '/';
    }

  for (var i = 0; i < inputs.length; i++) {
    inputs[i].addEventListener('input', function () {
      if (this.value != "")
        this.classList.remove("invalid");
    });
  }

  for (var i = 0; i < passwordInputs.length; i++) {
    passwordInputs[i].addEventListener('input', function () {
      if (this.value != "" && checkPassword()) {
        this.classList.remove("invalid");
      }
    });
  }

  for (var i = 0; i < selects.length; i++) {
    selects[i].addEventListener('change', function () {
      if (this.value != "") {
        this.classList.remove("invalid");
      } else {
        this.classList.add("invalid");
      }
    });
  }

  function check_email(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
  }

  document.getElementById("loginSubmit").addEventListener("click", function (event) {
    if (!loginSubmit()) {
      event.preventDefault();
    }
  });

  function loginSubmit() {
    var email = document.getElementById("loginEmail").value;
    var password = document.getElementById("loginPassword").value;

    emailExists(email).then((status) => {
      if (status) {
        error_notify('<?php echo get_phrase("E-mail_address_not_in_use") ?>');
      } else {
        validateCredentials(email, password).then((status) => {
          if (status) {
            document.getElementById("login-form").submit();
          } else {
            error_notify('<?php echo get_phrase("credentials_incorrect") ?>');
          }
        });
      }
    });
  }

  function validateCredentials(email, password) {
    return new Promise((resolve, reject) => {
      var emailInput = document.getElementById("loginEmail");
      var passwordInput = document.getElementById("loginPassword");
      var csrfName = $('input[name="<?= $this->security->get_csrf_token_name(); ?>"]').attr('name');
      var csrfHash = $('input[name="<?= $this->security->get_csrf_token_name(); ?>"]').val();

      $.ajax({
        type: "POST",
        url: "<?php echo site_url('login/validate_credentials'); ?>",
        data: {email: email, password: password, [csrfName]: csrfHash},
        dataType: 'json',
        success: function(response){
          if (response.status == true) {
            resolve(true);
          } else {
            resolve(false);
          }
          var newCsrfName = response.csrf.csrfName;
          var newCsrfHash = response.csrf.csrfHash;
          $('input[name="' + newCsrfName + '"]').val(newCsrfHash);
        },
        error: function(error) {
          console.error("Error:", error);
          resolve(false);
        }
      });
    });
  }
</script>

 <script>
  /* Drawer (pour mobile) : rien à faire, c’est CSS avec #drawerToggle */

/* Parallax doux des orbes + reveal */
const prefersReduced = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
if (!prefersReduced) {
  const orbs = document.querySelectorAll(".bg-orbs .orb");
  const lerp = (a,b,t)=>a+(b-a)*t;
  window.addEventListener("scroll", ()=>{
    const t = Math.min(1, window.scrollY/1200);
    orbs.forEach((o,i)=>{
      const dx = lerp(0, (i%2? 22 : -22), t);
      const dy = lerp(0, (i%2? -18 : 26), t);
      o.style.transform = `translate(${dx}px, ${dy}px)`;
    });
  }, {passive:true});
  const io = new IntersectionObserver((entries)=>{
    entries.forEach((en)=>{ if(en.isIntersecting){ en.target.classList.add("in"); io.unobserve(en.target); }});
  },{threshold:.14});
  document.querySelectorAll(".reveal").forEach(el=>io.observe(el));
}

/* Tabs des fonctionnalités (CSS radios) – pas de JS requis */

/* ===== Connexion inline (affiche sous la navbar) ===== */
document.addEventListener("DOMContentLoaded", function () {
  const box        = document.getElementById('loginInline');
  const btnOpen    = document.getElementById('openLoginBtn');      // bouton navbar desktop
  const btnOpenM   = document.getElementById('openLoginBtnM');     // bouton mobile offcanvas
  const btnClose   = document.getElementById('loginClose');        // bouton X
  const formLogin  = document.getElementById('login-form');
  const formSignup = document.getElementById('signupForm');
  const formForgot = document.getElementById('forget-form');
  const goSignup   = document.getElementById('goSignup');
  const goLogin    = document.getElementById('goLogin');
  const forgotLink = document.querySelector('.login-link');
  const backToLogin= document.getElementById('backToLogin');
  const loginTitle = document.getElementById('loginTitle');

  const isIOS = /iP(hone|od|ad)/.test(navigator.platform) || (navigator.userAgent.includes("Mac") && "ontouchend" in document);
  let keyboardOpen = false;
  let outsideClickHandler = null;

  // Util: positionne la carte sous le bouton ou sous la navbar si choix mobile stable
  function positionLoginDropdown(btn, forceUnderNavbar=false) {
    if (!box) return;
    const card = box.querySelector('.login-card');
    if (!card) return;

    const w = Math.min(340, window.innerWidth * 0.94);
    card.style.width = w + 'px';

    const gap = 8;
    let left, top;

    if (forceUnderNavbar || window.innerWidth < 480) {
      // option B: fixer sous la navbar (stable sur iOS)
      const headerH = parseInt(getComputedStyle(document.documentElement).getPropertyValue('--header-h')) || 58;
      left = Math.max(8, (window.innerWidth - w) / 2);
      top  = headerH + gap;
    } else {
      if (!btn) {
        // fallback: centrer
        left = Math.max(8, (window.innerWidth - w) / 2);
        top  = parseInt(getComputedStyle(document.documentElement).getPropertyValue('--header-h')) + gap;
      } else {
        const r = btn.getBoundingClientRect();
        left = r.right - w;
        top  = r.bottom + gap;
        left = Math.max(8, Math.min(left, window.innerWidth - w - 8));
      }
    }

    box.style.left = left + 'px';
    box.style.top  = top + 'px';
  }

  // Ouvre / ferme le dropdown
  function toggleLogin(show, btn) {
    if (!box) return;
    const willShow = (show !== undefined) ? show : box.hasAttribute('hidden');

    // si offcanvas ouvert, on attend sa fermeture proprement
    const offcanvasEl = document.getElementById('navbarOffcanvas');
    const finishShow = () => {
      if (!willShow) {
        box.setAttribute('hidden','');
        removeOutsideClickListener();
        return;
      }
      box.removeAttribute('hidden');

      // position selon device ; sur iOS/viewport small, on force sous navbar (Option B)
      const forceUnderNavbar = isIOS || window.innerWidth < 480;
      positionLoginDropdown(btn, forceUnderNavbar);

      // focus sur 1er input visible
      const activeForm = !formLogin.hidden ? formLogin : !formSignup.hidden ? formSignup : formForgot;
      activeForm.querySelector('input')?.focus();

      // n'utilise pas scrollIntoView sur iOS (bug), l'utiliser uniquement hors iOS si nécessaire
      if (!isIOS) {
        // try { box.scrollIntoView({ behavior: 'smooth', block: 'start' }); } catch(e){ /* ignore */ }
      }

      // ajouter listener pour fermer quand on clique en dehors
      addOutsideClickListener();
    };

    if (offcanvasEl) {
      // si offcanvas présent et visible, attendre l'événement hidden.bs.offcanvas
      const offInstance = bootstrap.Offcanvas.getInstance(offcanvasEl);
     if (offInstance) {
  // Si on clique depuis le bouton mobile => NE PAS fermer l’offcanvas
  const isMobileButton = (btn === btnOpenM);

  if (willShow && !isMobileButton) {
    // Cas desktop → fermer offcanvas avant d'afficher le login
    offcanvasEl.addEventListener('hidden.bs.offcanvas', function handler() {
      offcanvasEl.removeEventListener('hidden.bs.offcanvas', handler);
      finishShow();
    }, { once: true });

    offInstance.hide();
    return;
  }

  // Cas mobile => afficher directement dans l’offcanvas
  if (willShow && isMobileButton) {
    finishShow();
    return;
  }
}
    }

    // si pas d'offcanvas, on peut afficher immédiatement
    finishShow();
  }

  // fermeture via clic en dehors
  function addOutsideClickListener() {
    removeOutsideClickListener();
    outsideClickHandler = function(e) {
      if (!box) return;
      const card = box.querySelector('.login-card');
      if (!card) return;
      if (!card.contains(e.target) && !e.target.closest('#openLoginBtn') && !e.target.closest('#openLoginBtnM')) {
        toggleLogin(false);
      }
    };
    // utiliser capture pour être sûr de capter evenements avant d'autres handlers
    document.addEventListener('pointerdown', outsideClickHandler, { capture: true });
  }
  function removeOutsideClickListener() {
    if (outsideClickHandler) {
      document.removeEventListener('pointerdown', outsideClickHandler, { capture: true });
      outsideClickHandler = null;
    }
  }

  // gestion clavier (iOS déclenche resize) -> on ignore repositionnement pendant édition
  window.addEventListener('focusin', () => { keyboardOpen = true; });
  window.addEventListener('focusout', () => { keyboardOpen = false; });

  // repositionner lors du scroll/resize si visible (mais éviter pendant clavier)
  ['scroll','resize'].forEach(evt => {
    window.addEventListener(evt, () => {
      if (box?.hasAttribute && !box.hasAttribute('hidden') && !keyboardOpen) {
        const btn = window.innerWidth < 480 ? btnOpenM : btnOpen;
        const forceUnderNavbar = isIOS || window.innerWidth < 480;
        positionLoginDropdown(btn, forceUnderNavbar);
      }
    }, { passive: true });
  });

  // === événements d'ouverture/fermeture ===
  btnOpen?.addEventListener('click', (e) => { e.preventDefault(); toggleLogin(true, btnOpen); });
  btnOpenM?.addEventListener('click', (e) => { e.preventDefault(); toggleLogin(true, btnOpenM); });
  btnClose?.addEventListener('click', () => toggleLogin(false));
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && box && !box.hasAttribute('hidden')) toggleLogin(false);
  });

  // Switch forms
  goSignup?.addEventListener('click', (e) => {
    e.preventDefault();
    formLogin.hidden = true; formForgot.hidden = true; formSignup.hidden = false;
    loginTitle.textContent = "<?php echo get_phrase('Create_an_account') ?>";
    formSignup.querySelector('input')?.focus();
  });
  goLogin?.addEventListener('click', (e) => {
    e.preventDefault();
    formSignup.hidden = true; formForgot.hidden = true; formLogin.hidden = false;
    loginTitle.textContent = "<?php echo get_phrase('Log_in') ?>";
    formLogin.querySelector('input')?.focus();
  });
  forgotLink?.addEventListener('click', (e) => {
    e.preventDefault();
    formLogin.hidden = true; formSignup.hidden = true; formForgot.hidden = false;
    loginTitle.textContent = "<?php echo get_phrase('Forgot_password') ?>";
    formForgot.querySelector('input')?.focus();
  });
  backToLogin?.addEventListener('click', (e) => {
    e.preventDefault();
    formForgot.hidden = true; formSignup.hidden = true; formLogin.hidden = false;
    loginTitle.textContent = "<?php echo get_phrase('Log_in') ?>";
    formLogin.querySelector('input')?.focus();
  });

  // empêcher soumission réelle pour demo (si tu veux laisser réel, supprime cette ligne)
  [formLogin, formSignup].forEach(f => f?.addEventListener('submit', (e) => e.preventDefault()));

});
</script>


