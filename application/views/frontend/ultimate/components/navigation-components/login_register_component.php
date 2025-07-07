<div class="login-section">
  <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1050;">
  </div>
  <!-- Login Section -->
  <div class="login-dropdown hidden-section display-none">
    <svg class="login-exit-svg" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-box-arrow-right" viewBox="0 0 16 16">
      <path fill-rule="evenodd" d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0z" />
      <path fill-rule="evenodd" d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708z" />
    </svg>
    <form class="login-form mt-8" id="login-form" action="<?php echo site_url('login/validate_login_frontend'); ?>" method="post">
<div id="loginError" class="text-danger display-none" style="background-color: #fef2f2; border: none; border-radius: 12px; padding: 5px 22px; width: fit-content; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); color: #b91c1c; font-family: 'Inter', sans-serif; font-size: 14px; font-weight: 500; transition: all 0.3s ease; margin-left: auto; margin-right: auto;"></div>
      <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
      <div class="mb-4 mt-4 login-input">
        <label for="loginEmail" class="login-input-label login-input-label-rtl text-uppercase"><?php echo get_phrase("e-mail") ?> <span class="required"> * </span></label>
        <input type="email" class="form-control shadow-none" id="loginEmail" placeholder="<?php echo get_phrase("e-mail") ?>" aria-describedby="emailHelp" name="login_email">
      </div>
      <div class="mb-3 login-input">
        <label for="loginPassword" class="login-input-label login-input-label-rtl text-uppercase"><?php echo get_phrase("password") ?> <span class="required"> * </span></label>
        <input type="password" class="form-control shadow-none" id="loginPassword" placeholder="<?php echo get_phrase("password") ?>" name="login_password">
      </div>
      <button type="submit" id="loginSubmit" class="login-button text-uppercase mb-3" style="background-color: #FC7B30;"><?php echo get_phrase("login") ?></button>
      <!-- Conteneur pour le message d'erreur -->
    </form>
    <a class="register-phrase text-uppercase"><?php echo get_phrase("no account yet? ") ?> <span class="ml-1 register-link"><span>(</span> <?php echo get_phrase("register") ?> <span>)</span></span></a>
    <a class="forget-phrase text-uppercase"><?php echo get_phrase("Forgot account?") ?> <span class="ml-1 forget-link"><span>(</span> <?php echo get_phrase("forget password") ?> <span>)</span></span></a>
  </div>

  <!-- Forget Section (independent) -->
  <div class="forget-dropdown hidden-section display-none">
    <a class="text-uppercase"><span class="loginforge-link"><svg class="m-1" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-bar-left" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M12.5 15a.5.5 0 0 1-.5-.5v-13a.5.5 0 0 1 1 0v13a.5.5 0 0 1-.5.5M10 8a.5.5 0 0 1-.5.5H3.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L3.707 7.5H9.5a.5.5 0 0 1 .5.5" /></svg><?php echo get_phrase("login") ?></span></a>
    <form class="forget-form mt-10" id="forget-form" method="post" enctype="multipart/form-data" action="<?php echo site_url('login/send_reset_link'); ?>">
      <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
      <div class="mb-4 login-input">
        <label for="forgetEmail" class="login-input-label login-input-label-forget text-uppercase" style="padding-right: 20px !important;"><?php echo get_phrase("Email") ?><span class="required"> * </span></label>
        <input type="text" class="form-control shadow-none information" id="forgotEmail" name="email" required data-msg="<?php echo get_phrase("required") ?>">
      </div>
      <button type="submit" id="registerSubmit" class="register-button text-uppercase"><?php echo get_phrase("sent_password_reset_link") ?></button>
    </form>
  </div>

  <!-- Register Section -->
  <div class="register-dropdown hidden-section display-none">
    <svg class="register-exit-svg" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-box-arrow-right" viewBox="0 0 16 16">
      <path fill-rule="evenodd" d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0z" />
      <path fill-rule="evenodd" d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708z" />
    </svg>

      <div class="learner-form-container">
        <form class="learner-form" id="learner-form" method="post" enctype="multipart/form-data" action="<?php echo site_url('admission/online_admission_student/submit/student'); ?>">
        <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
        <div class="form-layout">
          <div class="step-indicators">
            <div class="step active" data-step="1">1</div>
            <div class="step" data-step="2">2</div>
          </div>
          <div class="form-steps-container">
            <div class="mb-4 title-register">
              <div style="font-size: 1rem;"><?php echo get_phrase("Member Register") ?></div>
            </div>
            <div class="form-step" data-step="1">
              <div class="mb-4 login-input">
                <label class="login-input-label-register text-uppercase"><?php echo get_phrase("First Name") ?> <span class="required"> * </span></label>
                <input type="text" class="form-control shadow-none" name="first_name" required placeholder="<?php echo get_phrase("First Name") ?>">
              </div>
              <div class="mb-4 login-input">
                <label class="login-input-label-register text-uppercase"><?php echo get_phrase("Last Name") ?> <span class="required"> * </span></label>
                <input type="text" class="form-control shadow-none" name="last_name" required placeholder="<?php echo get_phrase("Last Name") ?>">
              </div>
              <div class="mb-4 login-input">
                <label class="login-input-label-register text-uppercase"><?php echo get_phrase("Email") ?> <span class="required"> * </span></label>
                <input type="email" class="form-control shadow-none" name="student_email" required placeholder="<?php echo get_phrase("Email") ?>">
              </div>
              <div class="form-buttons">
                <button type="button" class="back-btn text-uppercase"><?php echo get_phrase("Back") ?></button>
                <button type="button" class="next-btn text-uppercase"><?php echo get_phrase("Next") ?></button>
              </div>
            </div>
            <div class="form-step display-none" data-step="2">
              <div class="mb-4 login-input">
                <label class="login-input-label-register text-uppercase"><?php echo get_phrase("Date of Birth") ?> <span class="required"> * </span></label>
                <input type="date" class="form-control shadow-none" name="date_of_birth" required>
              </div>
              <div class="mb-4 login-input">
                <label class="login-input-label-register text-uppercase"><?php echo get_phrase("Password") ?> <span class="required"> * </span></label>
                <input type="password" class="form-control shadow-none" name="password-student" placeholder="<?php echo get_phrase("password") ?>" id="password-student" required>
              </div>
              <div class="mb-4 login-input">
                <label class="login-input-label-register text-uppercase"><?php echo get_phrase("Repeat Password") ?> <span class="required"> * </span></label>
                <input type="password" class="form-control shadow-none" name="repeat-password-student" placeholder="<?php echo get_phrase("repeat password") ?>" id="repeat-password-student" required>
                <span id="errorMessage" class="text-danger display-none"><?php echo get_phrase("Passwords need to match.") ?></span>
              </div>
              <div class="form-buttons">
                <button type="button" class="back-btn text-uppercase"><?php echo get_phrase("Back") ?></button>
                <button type="submit" class="register-btn text-uppercase"><?php echo get_phrase("Register") ?></button>
              </div>
            </div>
          </div>
        </div>
      </form>
<div class="loading-spinner display-none">
    <div class="spinner"></div>
  </div>
    </div>
  </div>
</div>
<style>
  /* Loading Spinner */
.loading-spinner {
  position: absolute;
  top: 7%;
  left: 50%;
  transform: translate(-50%, 100%);
  z-index: 1001; /* Above other content (z-index 1000 for .register-dropdown) */
  background: rgba(255, 255, 255, 0.5); /* Semi-transparent background */
  padding: 20px;
  border-radius: 8px;
  width: 100px;
  height: 100px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.spinner {
  border: 4px solid #f3f3f3; /* Light grey */
  border-top: 4px solid #FC7B30; /* Match your theme color */
  border-radius: 50%;
  width: 40px;
  height: 40px;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.visibility-selector {
  list-style-type: none;
  position: relative;
  display: flex; /* Ensure buttons are side by side */
  width: 100%;
}

.visibility-selector .vis-button {
  width: 50%; /* Each button takes half the width */
  position: relative;
  min-height: 36px; /* Consistent height */
}

.visibility-selector label {
  display: block;
  position: relative;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  min-height: 36px;
  cursor: pointer;
  text-align: center;
  align-content: center;
  background: #2e2e2e; /* Default background */
  color: #fff; /* Text color */
  transition: all 0.5s ease-in-out;
  border-radius: 0; /* Reset any default rounding */
}

.visibility-selector input[type="radio"] {
  opacity: 0; /* Completely hide the radio input */
  position: absolute; /* Remove it from the flow */
  width: 0; /* Ensure it doesn't take up space */
  height: 0;
}

.visibility-label {
  position: relative;
  top: -22px;
}

.public-button {
  border-radius: 0 5px 5px 0 !important;
  box-shadow: rgba(0, 0, 0, 0.35) 0px 5px 15px;
}

.private-button {
  border-radius: 5px 0 0 5px !important;
  box-shadow: rgba(0, 0, 0, 0.35) 0px 5px 15px;
}


.visibility-selector input[type="radio"] + label {
  background: #2e2e2e;
  transition: all 0.5s ease-in-out;
  box-shadow: rgba(0, 0, 0, 0.35) 0px 5px 15px;
}

.space-label-button input[type="radio"] + label {
  margin-top: 10px;
}


.visibility-selector input[type="radio"]:checked + label {
  background-color: #FC7B30 !important;
  box-shadow: none; /* Remove shadow when active */
  transition: all 0.5s ease-in-out;
}

  /* Ensure the parent container has a proper background */
  .login-section {
    position: relative; /* Ensure dropdowns are positioned correctly */
  }

  /* Style for hidden sections */
  .hidden-section {
    opacity: 0;
    transition: opacity 0.1s ease-in-out;
    position: absolute;
  }

  .hidden-section:not(.display-none) {
  opacity: 1;
}

  /* When hidden */
  .display-none {
    display: none;
  }

  /* When shown */
  .show {
    opacity: 1;
  }

  /* Specific styling for register-dropdown */
  .register-dropdown {
    width: 300px; /* Adjust as needed */
    padding: 10px; /* Optional: Add padding for spacing */
    z-index: 1000; /* Ensure it’s on top */
  }

  .title-register {
       margin-bottom: 28px !important;
    margin-top: -14px !important;
  } 

  .learner-form-container
  {
    width: 90%;
    margin-top: 10%;
    padding: 20px;
    
  }

  .form-buttons-popup{
      display: flex;
      justify-content: space-between;
      margin-top: 68px;
  }

  .form-layout {
    display: flex;
    flex-direction: row; /* Indicateurs à gauche, champs à droite */
    align-items: flex-start; /* Alignement en haut */
    gap: 20px; /* Espacement entre les indicateurs et les champs */
  }

  /* Indicateurs d'étapes en colonne à gauche */
  .step-indicators {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 20px;
    margin-top: 23px;
  }

  /* Style des étapes */
  .step {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background-color: #ccc;
    color: #fff !important;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: default;
  }

  .step.active {
    background-color: #FC7B30;
    color: #fff;
  }

  /* Conteneur des étapes du formulaire */
  .form-steps-container {
    flex: 1; /* Prend tout l'espace restant à droite */
  }

  /* Transition pour les étapes */
  .form-step {
    opacity: 0;
    transition: opacity 0.3s ease-in-out;
  }

  .form-step:not(.display-none) {
  opacity: 1;
}

  /* Boutons */
  .form-buttons {
    display: flex;
    justify-content: space-between;
    margin-top: 20px;
  }

  .back-btn, .next-btn, .register-btn {
    background-color: #FC7B30;
    padding: 5px 10px;
    border: none;
    border-radius: 5px;
    color: white;
    cursor: pointer;
    font-size: 0.8em;
  }

  .photo-preview-popup {
    display: flex;
    justify-content: center;
    align-items: center;
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background-color: #f0f0f0;
    overflow: hidden;
    margin: 0 auto;
    margin-bottom: 8%;
  }

  .photo-preview img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  #loginError {
    font-size: 0.9em;
    text-align: center !important;
  }

  .login-input-label-register {
    color: #3a3a3a;
    font-size: 12px !important;
    padding-bottom: 15px !important;
    left: 80px;
    font-weight: 500;
}

.login-input-label-forget{
   color: #3a3a3a;
    font-size: 12px !important;
    padding-bottom: 15px !important;
    left: 50px;
    font-weight: 500;
}

[dir="rtl"] .login-input-label-forget{ 
  right: auto !important;
  left: 0 !important;
  text-align: right !important;
  width: 100% !important;
  padding-right: 80px !important;
  padding-bottom: 20px !important;
}
  [dir="rtl"] .login-dropdown,
[dir="rtl"] .register-dropdown,
[dir="rtl"] .forget-dropdown {
  right: auto !important;
  left: 50% !important;
  transform: translateX(-50%) !important; /* Keep centered for consistency */
}

/* Exit SVG positioning for RTL */
[dir="rtl"] .login-exit-svg,
[dir="rtl"] .register-exit-svg {
  right: auto !important;
  left: 20px !important;
}

/* Login and Forget links for RTL */
[dir="rtl"] .loginforge-link,
[dir="rtl"] .login-link {
  left: auto !important;
  right: 20px !important;
  text-align: right !important;
}

[dir="rtl"] .form-layout {
  flex-direction: row-reverse !important;
  gap: 20px !important;
}

/* Step indicators for RTL */
[dir="rtl"] .step-indicators {
  align-items: flex-end !important; /* Align indicators to the right */
  order: 2 !important;
}

[dir="rtl"] .form-steps-container {
  order: 1 !important; /* Place les champs avant les indicateurs */
}

/* Form buttons for RTL */
[dir="rtl"] .form-buttons {
  flex-direction: row-reverse !important; /* Reverse button order */
}

/* Next and Back buttons for RTL */
[dir="rtl"] .next-btn {
  order: 1 !important;
  margin-left: 5px !important; /* Next à gauche */
  margin-right: auto !important;
}

[dir="rtl"] .back-btn {
   order: 2 !important;
  margin-right: 5px !important; /* Back à droite */
  margin-left: auto !important;
}

[dir="rtl"] .register-btn {
  margin-left: 10px !important; /* Register à gauche */
  margin-right: auto !important;
}

/* Input labels for RTL */
[dir="rtl"] .login-input-label {
  right: auto !important;
  left: 0 !important;
  text-align: right !important;
  width: 100% !important;
  padding-right: 70px !important;
  padding-bottom: 20px !important;
}

 [dir="rtl"] .login-input-label-register {
  right: auto !important;
  left: 0 !important;
  text-align: right !important;
  width: 100% !important;
  padding-right: 80px !important;
  padding-bottom: 20px !important;
 }

[dir="rtl"] .login-input-label-rtl {
  right: auto !important;
  left: 0 !important;
  text-align: right !important;
  padding-right: 20px !important;
}

/* Photo preview label for RTL */
[dir="rtl"] .login-input-label[for="popup_student_image"],
[dir="rtl"] .login-input-label[for="popup_mentor_image"] {
  right: auto !important;
  left: 0 !important;
  text-align: right !important;
  padding-right: 70px !important;
}

/* Visibility selector for RTL */
[dir="rtl"] .visibility-selector {
  flex-direction: row-reverse !important; /* Reverse button order */
}

[dir="rtl"] .public-button {
  border-radius: 5px 0 0 5px !important; /* Invert border-radius */
}

[dir="rtl"] .private-button {
  border-radius: 0 5px 5px 0 !important; /* Invert border-radius */
}

/* Text alignment for inputs and textareas */
[dir="rtl"] .login-input input,
[dir="rtl"] .login-input select,
[dir="rtl"] .login-input textarea {
  text-align: right !important;
}

/* Error message alignment for RTL */
[dir="rtl"] #loginError,
[dir="rtl"] #errorMessage,
[dir="rtl"] #errorMessageMentor {
  text-align: right !important;
  margin-right: 10px !important;
}

/* Photo preview alignment for RTL */
[dir="rtl"] .photo-preview-popup {
  margin: 0 auto !important; /* Keep centered */
}

@media (max-width: 991px) {
[dir="rtl"] .register-dropdown {
  height: 400px !important;
    width: 300px; /* Adjust as needed */
    padding: 10px; /* Optional: Add padding for spacing */
    z-index: 1000; /* Ensure it’s on top */
  }

 [dir="rtl"] .learner-form-container
  {
    width: 90%;
    margin-top: -10%;
    padding: 20px;
    
  }
  .login-input {
    position: relative; /* Assure que les labels sont positionnés relativement au conteneur */
    margin-left: 10px; /* Marge commune pour aligner labels et champs en LTR */
    margin-right: 10px; /* Marge pour cohérence */
  }
  .login-input-label {
  color: #3a3a3a;
    font-size: 12px !important;
    padding-bottom: 15px !important;
    padding-left: 3px !important; /* Espacement à gauche pour LTR */
    left: 0 !important; /* Alignement à gauche pour LTR */
    font-weight: 500;
    width: 100% !important; /* S'assurer que le label prend toute la largeur */
    text-align: left !important;
}
  .login-input-label-register {
    color: #3a3a3a;
    font-size: 12px !important;
    padding-bottom: 15px !important;
    padding-left: 3px !important; /* Espacement cohérent à gauche pour LTR */
    left: 0 !important; /* Alignement au début pour LTR */
    font-weight: 500;
    width: 100% !important; /* Prend toute la largeur */
    text-align: left !important;
}
[dir="rtl"] .login-input-label,
  [dir="rtl"] .login-input-label-register {
    left: auto !important;
    right: 0 !important; /* Alignement à droite pour RTL */
    text-align: right !important; /* Alignement texte à droite pour RTL */
    padding-left: 0 !important; /* Supprimer padding gauche pour RTL */
    padding-right: 10px !important; /* Espacement à droite pour RTL */
    padding-bottom: 15px !important; /* Conserver padding-bottom cohérent */
  }
  [dir="rtl"] .form-layout {
    flex-direction: column !important; /* Empile verticalement */
    align-items: center !important;
  }

  [dir="rtl"] .step-indicators {
    flex-direction: row !important; /* Indicateurs en ligne */
    justify-content: center !important;
    margin-bottom: 20px !important;
    order: 0 !important; /* Indicateurs en haut sur mobile */
  }

  [dir="rtl"] .form-steps-container {
    order: 1 !important; /* Champs en bas */
  }

  [dir="rtl"] .next-btn {
    margin-left: 10px !important;
    margin-right: auto !important;
  }

  [dir="rtl"] .back-btn {
    margin-right: 10px !important;
    margin-left: auto !important;
  }

  [dir="rtl"] .register-btn {
    margin-left: 10px !important;
    margin-right: auto !important;
  }
}
</style>

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