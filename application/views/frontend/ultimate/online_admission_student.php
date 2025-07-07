<?php if (get_common_settings('recaptcha_status')): ?>
  <script src="https://www.google.com/recaptcha/api.js" async defer></script>
<?php endif; ?>

<?php
?>

<!-- ========== MAIN ========== -->
<main id="content" role="main">

  <!-- Header Section -->
  <div class="general-container container-fluid">
    <div class="general-header align-items-center">
      <h1 class='col-6 display-4 text_fade text-uppercase text-center  text-sm-break'>
        <?php echo get_phrase('start_your_journey'); ?>
      </h1>
      <!-- Div Section For Header Background Fade In-Out Animation-->
      <div></div>
      <div></div>
      <div></div>
      <!-- End Div Section-->
    </div>
    <img class="ct-img rellax " data-rellax-speed="1.5"
      src="<?php echo base_url('assets/frontend/ultimate/img/online admission/oa-img-top.jpg') ?>" alt="">
    <div class="general-container-ol"></div>
  </div>
  <!-- Admission Form Section -->
  <div class="container-fluid form-section pt-10">
    <!-- Display Error -->
    <?php if ($this->session->flashdata('error')): ?>
        <div class="alert alert-danger">
            <?php echo $this->session->flashdata('error');$this->session->unset_userdata('error');  ?>
            
        </div>
    <?php endif; ?>
    <!-- Display Success -->
    <?php if ($this->session->flashdata('success')): ?>
        <div class="alert alert-success">
            <?php echo $this->session->flashdata('success'); $this->session->unset_userdata('success'); ?>
        </div>
    <?php endif; ?>
    </div>
    <!-- Student Admission Form -->
    <form action="<?php echo site_url('admission/online_admission_student/submit/student'); ?>" method="post" id="studentform"
      class="js-validate studentform realtime-form container" enctype="multipart/form-data">
          <!-- Champ caché pour le jeton CSRF -->
     <input type="hidden" name="<?=$this->security->get_csrf_token_name();?>" value="<?=$this->security->get_csrf_hash();?>" />
      <div class="row justify-content-center">
        <h4 class="col h2 pb-11 text-uppercase d-flex justify-content-center form-title">
          <?php echo get_phrase('learner_admission'); ?>
        </h4>
        <p class="text-white h5 pb-5 text-uppercase d-flex justify-content-center form-label">
          <?php echo get_phrase('learner_information'); ?>
        </p>
      </div>
      <div class="row justify-content-center">
        <!-- Input -->
        <div class="col-sm-4 col-11">
          <div class="js-form-message mb-5">
            <label class="form-label text-white">
              <?php echo get_phrase('first_name'); ?>
              <span class="text-danger">*</span>
            </label>
            <div class="input-group pt-1">
              <span class="input-group-text">
                <svg class="m-1" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                  class="bi bi-person-vcard" viewBox="0 0 16 16">
                  <path
                    d="M5 8a2 2 0 1 0 0-4 2 2 0 0 0 0 4m4-2.5a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 0 1h-4a.5.5 0 0 1-.5-.5M9 8a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 0 1h-4A.5.5 0 0 1 9 8m1 2.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5" />
                  <path
                    d="M2 2a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2zM1 4a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H8.96q.04-.245.04-.5C9 10.567 7.21 9 5 9c-2.086 0-3.8 1.398-3.984 3.181A1 1 0 0 1 1 12z" />
                </svg>
              </span>
              <input type="text" placeholder="<?php echo get_phrase('first_name'); ?>"
                class="form-control shadow-none rounded-end" name="first_name" required
                data-msg="Please enter your first name." data-error-class="u-has-error"
                data-success-class="u-has-success">
            </div>
          </div>
        </div>
        <!-- End Input -->
        <!-- Input -->
        <div class="col-sm-4 col-11">
          <div class="js-form-message mb-5">
            <label class="form-label text-white">
              <?php echo get_phrase('last_name'); ?>
              <span class="text-danger">*</span>
            </label>
            <div class="input-group pt-1">
              <span class="input-group-text">
                <svg class="m-1" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                  class="bi bi-person-vcard" viewBox="0 0 16 16">
                  <path
                    d="M5 8a2 2 0 1 0 0-4 2 2 0 0 0 0 4m4-2.5a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 0 1h-4a.5.5 0 0 1-.5-.5M9 8a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 0 1h-4A.5.5 0 0 1 9 8m1 2.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5" />
                  <path
                    d="M2 2a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2zM1 4a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H8.96q.04-.245.04-.5C9 10.567 7.21 9 5 9c-2.086 0-3.8 1.398-3.984 3.181A1 1 0 0 1 1 12z" />
                </svg>
              </span>
              <input type="text" placeholder="<?php echo get_phrase('last_name'); ?>"
                class="form-control shadow-none rounded-end" name="last_name" required
                data-msg="Please enter your last name." data-error-class="u-has-error"
                data-success-class="u-has-success">
            </div>
          </div>
        </div>
        <!-- End Input -->
      </div>
        <div class="row justify-content-center">
          <!-- Input -->
          <div class="col-sm-4 col-11">
            <div class="js-form-message mb-5">
              <label class="form-label text-white">
                <?php echo get_phrase('learner_email'); ?>
                <span class="text-danger">*</span>
              </label>
              <div class="input-group pt-1">
                <span class="input-group-text">
                  <svg class="m-1" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                    class="bi bi-envelope-at" viewBox="0 0 16 16">
                    <path
                      d="M2 2a2 2 0 0 0-2 2v8.01A2 2 0 0 0 2 14h5.5a.5.5 0 0 0 0-1H2a1 1 0 0 1-.966-.741l5.64-3.471L8 9.583l7-4.2V8.5a.5.5 0 0 0 1 0V4a2 2 0 0 0-2-2zm3.708 6.208L1 11.105V5.383zM1 4.217V4a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v.217l-7 4.2z" />
                    <path
                      d="M14.247 14.269c1.01 0 1.587-.857 1.587-2.025v-.21C15.834 10.43 14.64 9 12.52 9h-.035C10.42 9 9 10.36 9 12.432v.214C9 14.82 10.438 16 12.358 16h.044c.594 0 1.018-.074 1.237-.175v-.73c-.245.11-.673.18-1.18.18h-.044c-1.334 0-2.571-.788-2.571-2.655v-.157c0-1.657 1.058-2.724 2.64-2.724h.04c1.535 0 2.484 1.05 2.484 2.326v.118c0 .975-.324 1.39-.639 1.39-.232 0-.41-.148-.41-.42v-2.19h-.906v.569h-.03c-.084-.298-.368-.63-.954-.63-.778 0-1.259.555-1.259 1.4v.528c0 .892.49 1.434 1.26 1.434.471 0 .896-.227 1.014-.643h.043c.118.42.617.648 1.12.648m-2.453-1.588v-.227c0-.546.227-.791.573-.791.297 0 .572.192.572.708v.367c0 .573-.253.744-.564.744-.354 0-.581-.215-.581-.8Z" />
                  </svg>
                </span>
                <input type="email" placeholder="<?php echo get_phrase('email'); ?>"
                  class="form-control rounded-end shadow-none " name="student_email" required
                  data-msg="Please enter a valid email address." data-error-class="u-has-error"
                  data-success-class="u-has-success">
              </div>
            </div>
          </div>
          <!-- End Input -->
            <!-- Input -->
        <div class="col-sm-4 col-11">
          <div class="js-form-message mb-5">
            <label class="form-label text-white">
              <?php echo get_phrase('date_of_birth'); ?>
              <span class="text-danger">*</span>
            </label>
            <div class="input-group pt-1">
              <span class="input-group-text">
                <svg class="m-1" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                  class="bi bi-calendar3" viewBox="0 0 16 16">
                  <path
                    d="M14 0H2a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2M1 3.857C1 3.384 1.448 3 2 3h12c.552 0 1 .384 1 .857v10.286c0 .473-.448.857-1 .857H2c-.552 0-1-.384-1-.857z" />
                  <path
                    d="M6.5 7a1 1 0 1 0 0-2 1 1 0 0 0 0 2m3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2m3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2m-9 3a1 1 0 1 0 0-2 1 1 0 0 0 0 2m3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2m3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2m3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2m-9 3a1 1 0 1 0 0-2 1 1 0 0 0 0 2m3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2m3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2" />
                </svg>
              </span>
              <input type="date" class="form-control rounded-end shadow-none" name="date_of_birth" required
                data-msg="Please enter your date of birth" data-error-class="u-has-error"
                data-success-class="u-has-success">
            </div>
          </div>
        </div>
        <!-- End Input -->
        </div>
      <div class="row justify-content-center">
        <!-- Input -->
        <div class="col-sm-4 col-11">
          <div class="js-form-message mb-5">
            <label class="form-label text-white">
              <?php echo get_phrase('password'); ?>
              <span class="text-danger">*</span>
            </label>
            <div class="input-group pt-1">
              <span class="input-group-text">
                <svg class="m-1" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                  class="bi bi-key" viewBox="0 0 16 16">
                  <path
                    d="M0 8a4 4 0 0 1 7.465-2H14a.5.5 0 0 1 .354.146l1.5 1.5a.5.5 0 0 1 0 .708l-1.5 1.5a.5.5 0 0 1-.708 0L13 9.207l-.646.647a.5.5 0 0 1-.708 0L11 9.207l-.646.647a.5.5 0 0 1-.708 0L9 9.207l-.646.647A.5.5 0 0 1 8 10h-.535A4 4 0 0 1 0 8m4-3a3 3 0 1 0 2.712 4.285A.5.5 0 0 1 7.163 9h.63l.853-.854a.5.5 0 0 1 .708 0l.646.647.646-.647a.5.5 0 0 1 .708 0l.646.647.646-.647a.5.5 0 0 1 .708 0l.646.647.793-.793-1-1h-6.63a.5.5 0 0 1-.451-.285A3 3 0 0 0 4 5" />
                  <path d="M4 8a1 1 0 1 1-2 0 1 1 0 0 1 2 0" />
                </svg>
              </span>
              <input type="password" id="password-student" class="form-control rounded-end shadow-none"
                name="password-student" required data-msg="Please enter a password" data-error-class="u-has-error"
                data-success-class="u-has-success">
            </div>
          </div>
        </div>
        <!-- End Input -->
        <div class="col-sm-4 col-11">
          <div class="js-form-message mb-5" id="password-repeat-div">
            <label class="form-label text-white">
              <?php echo get_phrase('repeat_password'); ?>
              <span class="text-danger">*</span>
            </label>
            <div class="input-group pt-1">
              <span class="input-group-text">
                <svg class="m-1" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                  class="bi bi-key" viewBox="0 0 16 16">
                  <path
                    d="M0 8a4 4 0 0 1 7.465-2H14a.5.5 0 0 1 .354.146l1.5 1.5a.5.5 0 0 1 0 .708l-1.5 1.5a.5.5 0 0 1-.708 0L13 9.207l-.646.647a.5.5 0 0 1-.708 0L11 9.207l-.646.647a.5.5 0 0 1-.708 0L9 9.207l-.646.647A.5.5 0 0 1 8 10h-.535A4 4 0 0 1 0 8m4-3a3 3 0 1 0 2.712 4.285A.5.5 0 0 1 7.163 9h.63l.853-.854a.5.5 0 0 1 .708 0l.646.647.646-.647a.5.5 0 0 1 .708 0l.646.647.646-.647a.5.5 0 0 1 .708 0l.646.647.793-.793-1-1h-6.63a.5.5 0 0 1-.451-.285A3 3 0 0 0 4 5" />
                  <path d="M4 8a1 1 0 1 1-2 0 1 1 0 0 1 2 0" />
                </svg>
              </span>
              <input type="password" id="repeat-password-student" class="form-control rounded-end shadow-none"
                name="repeat-password-student" required data-msg="Please repeat your password"
                data-error-class="u-has-error" data-success-class="u-has-success">
            </div>
            <span id="errorMessage"
              class="text-danger display-none"><?php echo get_phrase('passwords_need_to_match'); ?>.</span>
          </div>
        </div>
        <!-- End Input -->
      </div>
      <div class="row ">
      </div>
      <?php if (get_common_settings('recaptcha_status')): ?>
        <div class="js-form-message mb-6">
          <div class="form-group">
            <div class="g-recaptcha" data-sitekey="<?php echo get_common_settings('recaptcha_sitekey'); ?>"></div>
          </div>
        </div>
      <?php endif; ?>
      <div class="text-center">
        <button type="submit" id="submitBtn"
          class="btn btn-wide mb-11 text-uppercase submit-button"><?php echo get_phrase('apply'); ?></button>
        <button type="reset" id="resetBtn" style="display: none;"></button>
      </div>
    </form>
    <!-- End Student Admission Form -->
  </div>
  </div>
  <!-- End Contact Form Section -->
  <div class="general-container g-0 container-fluid">
    <img class="ct-img rellax " data-rellax-speed="1.5"
      src="<?php echo base_url('assets/frontend/ultimate/img/online admission/oa-img-bot.jpg') ?>" alt="">
    <div class="general-container-ol-bot"></div>
  </div>

  <style>
  /* Ensure Toastr is fully opaque and matches Bootstrap styling */
  .toast {
    margin-top: 50px !important;
    border-radius: 5px;
    font-size: 16px;
    font-weight: 500;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
    opacity: 1 !important; /* Remove transparency */
  }
  .toast-success {
    background-color: #28a745 !important; /* Bootstrap success green, solid */
  }
  .toast-error {
    background-color: #dc3545 !important; /* Bootstrap danger red, solid */
  }
  .toast-close-button {
    color: #fff !important;
    opacity: 0.8 !important; /* Slightly transparent for aesthetics */
  }
  .toast-close-button:hover {
    color: #f0f0f0 !important;
    opacity: 1 !important; /* Fully opaque on hover */
  }
</style>

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

  const studentForm = document.getElementById('studentform');
  const submitBtn = document.getElementById('submitBtn');
  const resetBtn = document.getElementById('resetBtn');

  if (studentForm && submitBtn) {
    studentForm.addEventListener('submit', function (event) {
      event.preventDefault(); // Prevent default form submission

      // Validate form
      if (!studentForm.checkValidity()) {
        studentForm.reportValidity();
        return;
      }

      // Get CSRF token from the hidden input
      const csrfName = document.querySelector(`input[name="<?php echo $this->security->get_csrf_token_name(); ?>"]`).name;
      const csrfHash = document.querySelector(`input[name="<?php echo $this->security->get_csrf_token_name(); ?>"]`).value;

      // Prepare form data
      const formData = new FormData(studentForm);
      formData.append(csrfName, csrfHash); // Ensure CSRF token is included

      // Perform AJAX submission
      fetch(studentForm.action, {
        method: 'POST',
        body: formData
      })
      .then(response => response.json()) // Expect JSON response
      .then(data => {
        // Update CSRF token for the next request
        if (data.csrf) {
          document.querySelector(`input[name="${data.csrf.csrfName}"]`).value = data.csrf.csrfHash;
        }

        if (data.status) {
          // Success case
          toastr.success(data.message); // Show success toast
          resetBtn.click(); // Reset form
          setTimeout(() => {
            window.location.href = '<?php echo site_url('/home/communities'); ?>';
          }, 2000); // Wait 2 seconds to show the toast
        } else {
          // Error case (e.g., duplicate email, validation error)
          toastr.error(data.message);
        }
      })
      .catch(error => {
        console.error('Error:', error);
        toastr.error('<?php echo get_phrase('an_error_occurred'); ?>');
      });
    });
  }

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

  // Image preview
  document.getElementById('student_image').addEventListener('change', function(event) {
    const file = event.target.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onload = function(e) {
        const preview = document.getElementById('photo-preview');
        preview.innerHTML = '<img src="' + e.target.result + '" alt="Photo preview" />';
      };
      reader.readAsDataURL(file);
    }
  });
});
</script>