<!-- ========== HERO ========== -->
  <section class="contact-hero position-relative text-center mt-5">
    <div class="hero-overlay"></div>
    <div class="container position-relative hero-inner">
      <h1 class="fw-extrabold text-dark"><?php echo get_phrase("Contact Us") ?></h1>
      <p class="mt-2 mb-0"><?php echo get_phrase("Any questions?") ?> <strong><?php echo get_phrase("Response guaranteed within 24 business hours.") ?></strong></p>
    </div>
  </section>
 <?php $this->load->view('frontend/alert_view'); ?>
  <main class="container py-5">
    <div class="row g-4 align-items-stretch">
      <div class="col-12 col-lg-7">
        <form  action="<?php echo site_url('home/contact/send'); ?>" method="post" id="contact_send" class="pt-8 js-validate contact_send realtime-form container card glass border-0 p-3 p-md-4 needs-validation" enctype="multipart/form-data">
          <input type="hidden" name="<?=$this->security->get_csrf_token_name();?>" value="<?=$this->security->get_csrf_hash();?>" />
          <div class="row g-3">
            <div class="col-12 col-md-6">
              <div class="form-floating">
                <input type="text" class="form-control shadow-none"
                      placeholder="<?php echo get_phrase('First name'); ?>" name="first_name"
                      data-msg="Please enter your first name." data-error-class="u-has-error"
                      data-success-class="u-has-success">
                <label><?php echo get_phrase("First Name*") ?></label>
              </div>
            </div>
            <div class="col-12 col-md-6">
              <div class="form-floating">
                <input type="text" class="form-control shadow-none"
                      placeholder="<?php echo get_phrase('Last name'); ?>" name="last_name"
                      data-msg="Please enter your last name." data-error-class="u-has-error"
                      data-success-class="u-has-success">
                <label><?php echo get_phrase("Last Name*") ?></label>
              </div>
            </div>
 
            <div class="col-12">
              <div class="form-floating">
                <input type="email" class="form-control shadow-none" name="email"
                      placeholder="<?php echo get_phrase('Email address'); ?>"
                      data-msg="Please enter a valid email address." data-error-class="u-has-error"
                      data-success-class="u-has-success">
                <label><?php echo get_phrase("Email*") ?></label>
              </div>
            </div>
 
            <div class="col-12">
              <div class="form-floating">
                <input type="text" class="form-control shadow-none" name="address"
                      placeholder="<?php echo get_phrase('Postal_Address'); ?>"
                      data-success-class="u-has-success">
                      <label><?php echo get_phrase("Postal_Address") ?></label>
              </div>
            </div>
            <div class="col-12 col-md-6">
              <div class="form-floating">
                <input type="tel" class="form-control shadow-none" placeholder="+971501548923" name="phone"
                     data-msg="Please enter a valid phone number starting with '+' followed by 10 to 15 digits."
                    data-error-class="u-has-error" data-success-class="u-has-success">
                <label><?php echo get_phrase("Phone") ?></label>
              </div>
            </div>
 
            <div class="col-12 col-md-6">
              <div class="form-floating">
                <input type="text" class="form-control shadow-none" name="localisation"
                      placeholder="<?php echo get_phrase('City_/_Country'); ?>" 
                      data-success-class="u-has-success">
                      <label><?php echo get_phrase("City_/_Country") ?></label>
              </div>
            </div>
 
            <div class="col-12">
              <div class="form-floating">
                <textarea type="text" class="form-control shadow-none" rows="5"
                      placeholder="<?php echo get_phrase('Please_write_a_message.'); ?>" name="comment"
                      data-msg="Please enter your message." data-error-class="u-has-error"
                      data-success-class="u-has-success"></textarea>
                <label><?php echo get_phrase("Message*") ?></label>
              </div>
            </div>
 
            <div class="col-12">
              <div class="row justify-content-center"> <div class="js-form-message mb-3">
                <div class="form-group">
                </div>
              </div><div class="text-center">
                <button type="submit" id="submitBtn" class="btn btn-wayo w-100 fw-bold"> <?php echo get_phrase('Send'); ?> </button>
                <div id="formSuccess" class="alert alert-success mt-3 d-none" role="alert">
                ✅  <?php echo get_phrase("Message_sent_!_Thank_you") ?> 😊 
              </div>
              <div id="formError" class="alert alert-danger mt-3 d-none" role="alert"></div>
              </div>
            </div>
            </div>
          </div>
        </form>
      </div>
 
      <!-- —— Infos Wayo —— -->
      <aside class="col-12 col-lg-5">
        <div class="card glass border-0 h-100 p-3 p-md-4
                    d-flex flex-column justify-content-center align-items-center text-center">
         
          <h2 class="h4 fw-bold mb-3"><?php echo get_phrase("Our") ?>&nbsp;<?php echo get_phrase("Contact details") ?></h2>
 
          <ul class="list-unstyled d-flex flex-column gap-3 contact-list mb-4">
            <li class="d-flex align-items-center gap-3">
              <i class="fa-solid fa-phone text-primary"></i><a class="text-decoration-none text-dark" href="tel:<?php echo get_settings('phone'); ?>">+971 50 154 8923</a>
            </li>
            <li class="d-flex align-items-center gap-3">
              <i class="fa-solid fa-envelope text-primary"></i><a class="text-decoration-none text-dark" href="mailto:<?php echo get_settings('system_email'); ?>">info@wayo.cloud</a>
            </li>
            <li class="d-flex align-items-center gap-3">
              <i class="fa-solid fa-location-dot text-primary"></i> <a class="text-decoration-none text-dark" href="<?php echo site_url('home/contact#map'); ?>">R320 Umm Hurair 2, Dubai, UAE</a>
            </li>
          </ul>
 
          <hr class="opacity-10 w-100">
          <h3 class="h6 fw-bold mb-3"><?php echo get_phrase("Follow us") ?></h3>
          <div class="d-flex justify-content-center gap-2">
            <a href="https://www.facebook.com/people/Wayo-Academy/61572524656807/" aria-label="Facebook"  class="social-btn" target="_blank"><i class="fa-brands fa-facebook-f"></i></a>
            <a href="https://www.instagram.com/wayo_academy/" aria-label="Instagram" class="social-btn" target="_blank"><i class="fa-brands fa-instagram"></i></a>
            <a href="https://www.linkedin.com/company/wayoacademy/" aria-label="LinkedIn"  class="social-btn" target="_blank"><i class="fa-brands fa-linkedin-in"></i></a>
          </div>
        </div>
      </aside>
    </div>
    <!-- <div class="container-fluid location-container mt-5">
    <div class="row">
      <div id="map" class="g-0 col-12"></div>
    </div>
    </div> -->
       <!-- OÙ NOUS TROUVER -->
    <section id="contact" class="contact py-5">
      <div class="container">
        <div class="row g-3 align-items-stretch">
          <div class="col-12 col-lg-12" data-animate>
            <article class="p-3 bg-white rounded-4 shadow-sm h-100">
              <div class="map-embed rounded-3">
                  <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3608.773591177115!2d55.306372586657815!3d25.24454967224629!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3e5f42d392745e8d%3A0x263aa4ef8ed1cdb8!2sUmm%20Hurair%20Rd%20-%20Dubai%20-%20%C3%89mirats%20arabes%20unis!5e0!3m2!1sfr!2sma!4v1759136094142!5m2!1sfr!2sma" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </article>
          </div>
        </div>
      </div>
    </section>
  </main>

<script>
  const alertS = document.getElementById('formSuccess');
  const errorS = document.getElementById('formError');
  const contactForm = document.getElementById('contact_send');
  const submitBtn = document.getElementById('submitBtn');

  if (contactForm) {
    contactForm.addEventListener('submit', function(event) {
      event.preventDefault();

      if (!contactForm.checkValidity()) {
        contactForm.reportValidity();
        return;
      }

      submitBtn.classList.add('btn-loading');
      submitBtn.innerText = 'Sending...';

      const formData = new FormData(contactForm);

      fetch(contactForm.action, {
        method: 'POST',
        body: formData,
        headers: {
          'X-Requested-With': 'XMLHttpRequest'
        }
      })
      .then(response => response.text()) // Get raw text first
      .then(text => {
        try {
          return JSON.parse(text); // Parse JSON and return it
        } catch (e) {
          console.error('JSON Parse Error:', e, 'Response:', text);
          throw new Error('Invalid JSON response from server');
        }
      })
      .then(data => {
        if (data.status === 1) {
          alertS?.classList.remove('d-none');
          errorS?.classList.add('d-none');
          setTimeout(() => alertS?.classList.add('d-none'), 5000);
          contactForm.reset();
        } else {
          if (errorS) {
            errorS.innerText = data.message || 'Failed to send your message. Please try again.';
            errorS.classList.remove('d-none');
            setTimeout(() => errorS.classList.add('d-none'), 5000);
          } else {
            console.error('Error element not found in DOM');
            alert(data.message || 'Failed to send your message. Please try again.');
          }
        }
      })
      .catch(error => {
        console.error('Error:', error);
        if (errorS) {
          errorS.innerText = 'An error occurred: ' + error.message;
          errorS.classList.remove('d-none');
          setTimeout(() => errorS.classList.add('d-none'), 5000);
        } else {
          console.error('Error element not found in DOM');
          alert('An error occurred: ' + error.message);
        }
      })
      .finally(() => {
        submitBtn.classList.remove('btn-loading');
        submitBtn.innerText = '<?php echo get_phrase("Send"); ?>';
      });
    });
  }

</script>