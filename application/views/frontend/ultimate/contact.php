 <style>
 .glass {
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(10px);
      border-radius: 20px;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    }
 
    /* Uniformiser la hauteur de tous les inputs */
    .form-control,
    .form-select {
      height: 58px;
      border-radius: 10px;
      border: 1px solid #e0e0e0;
      transition: all 0.3s ease;
    }
 
    .form-floating > .form-control,
    .form-floating > .form-select {
      height: 58px;
      padding-top: 1.625rem;
    }
 
    .form-floating > label {
      padding: 1rem 0.75rem;
    }
 
    textarea.form-control {
      height: 120px !important;
      padding-top: 1.625rem !important;
    }
 
    .form-control:focus,
    .form-select:focus {
      border-color: #ff6b35;
      box-shadow: 0 0 0 0.2rem rgba(255, 107, 53, 0.25);
    }
 
    /* Style pour le select avec drapeaux */
    .country-select-wrapper {
      position: relative;
      width: 35%;
    }
 
    .form-select {
      padding-left: 0.75rem;
      font-size: 0.95rem;
      cursor: pointer;
      background-color: white;
    }
 
    .form-select option {
      padding: 10px;
      font-size: 0.95rem;
    }
 
    .phone-wrapper {
      display: flex;
      gap: 0.5rem;
      align-items: stretch;
    }
 
    .btn-wayo {
      background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%);
      border: none;
      color: white;
      padding: 1rem 2rem;
      border-radius: 10px;
      font-size: 1.1rem;
      transition: all 0.3s ease;
      height: 58px;
    }
 
    .btn-wayo:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(255, 107, 53, 0.4);
      background: linear-gradient(135deg, #f7931e 0%, #ff6b35 100%);
    }
 
    .contact-list li {
      padding: 0.75rem;
      border-radius: 10px;
      transition: background 0.3s ease;
    }
 
    .contact-list li:hover {
      background: rgba(255, 107, 53, 0.1);
    }
 
    .contact-list i {
      width: 30px;
      height: 30px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      border-radius: 50%;
      background: rgba(255, 107, 53, 0.1);
      margin-right: 0.75rem;
      color: #ff6b35 !important;
    }
 
    .social-btn {
      width: 45px;
      height: 45px;
      border-radius: 50%;
      background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%);
      color: white;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      text-decoration: none;
      transition: all 0.3s ease;
    }
 
    .social-btn:hover {
      transform: translateY(-3px);
      box-shadow: 0 8px 20px rgba(255, 107, 53, 0.4);
      color: white;
    }
 
    .alert {
      border-radius: 10px;
      border: none;
    }
  </style>
 
 
<!-- ========== HERO ========== -->
<section class="contact-hero position-relative text-center">
  <div class="hero-overlay"></div>
  <div class="container position-relative hero-inner">
    <h1 class="fw-extrabold text-dark"><?php echo get_phrase("Contact_Us") ?></h1>
    <p class="mt-2 mb-0"><?php echo get_phrase("Any questions?") ?>
      <strong><?php echo get_phrase("Response guaranteed within 24 business hours.") ?></strong>
    </p>
  </div>
</section>
 
<?php $this->load->view('frontend/alert_view'); ?>
<main class="container py-5">
      <div class="row g-4 align-items-stretch">
      <div class="col-12 col-lg-7">
        <form id="contact_send" class="glass p-3 p-md-4 needs-validation" action="<?php echo site_url('home/contact/send'); ?>" method="post" enctype="multipart/form-data" novalidate>
          <!-- Champ caché pour le jeton CSRF -->
                <input type="hidden" name="<?=$this->security->get_csrf_token_name();?>" value="<?=$this->security->get_csrf_hash();?>" />
         
          <h2 class="h3 fw-bold mb-4 text-center"><?php echo get_phrase('contact_us'); ?></h2>
         
          <div class="row g-3">
            <!-- Prénom -->
            <div class="col-12 col-md-6">
              <div class="form-floating">
                <input type="text" class="form-control shadow-none" id="firstName"
                       placeholder="Prénom" name="first_name" required>
                <label for="firstName"><?php echo get_phrase('First name'); ?>*</label>
              </div>
            </div>
 
            <!-- Nom -->
            <div class="col-12 col-md-6">
              <div class="form-floating">
                <input type="text" class="form-control shadow-none" id="lastName"
                       placeholder="Nom" name="last_name" required>
                <label for="lastName"><?php echo get_phrase('Last name'); ?>*</label>
              </div>
            </div>
 
            <!-- Email -->
            <div class="col-12">
              <div class="form-floating">
                <input type="email" class="form-control shadow-none" id="email"
                       name="email" placeholder="Email" required>
                <label for="email"><?php echo get_phrase('Email address'); ?>*</label>
              </div>
            </div>
 
            <!-- Adresse postale -->
            <div class="col-12">
              <div class="form-floating">
                <input type="text" class="form-control shadow-none" id="address"
                       name="address" placeholder="<?php echo get_phrase("Postal_Address") ?>">
                <label for="address"><?php echo get_phrase("Postal_Address") ?></label>
              </div>
            </div>
 
            <!-- Téléphone avec sélecteur de pays -->
            <div class="col-12 col-md-6">
              <div class="phone-wrapper">
                <div class="country-select-wrapper">
                   <?php include 'partials/countrySelect.php'; ?>
                </div>
                <div class="form-floating flex-grow-1">
                  <input type="tel" class="form-control shadow-none" id="phoneInput"
                         name="phone" placeholder="501548923">
                  <label for="phoneInput"><?php echo get_phrase("phone") ?>*</label>
                </div>
              </div>
            </div>
 
            <!-- Ville / Pays -->
            <div class="col-12 col-md-6">
              <div class="form-floating">
                <input type="text" class="form-control shadow-none" id="localisation"
                       name="localisation" placeholder="Ville / Pays">
                <label for="localisation"><?php echo get_phrase("City_/_Country") ?></label>
              </div>
            </div>
 
            <!-- Message -->
            <div class="col-12">
              <div class="form-floating">
                <textarea class="form-control shadow-none" id="message" name="comment"
                          placeholder="Votre message" required></textarea>
                <label for="message"><?php echo get_phrase("Message") ?>*</label>
              </div>
            </div>
 
            <!-- Bouton d'envoi -->
            <div class="col-12 text-center">
              <button type="submit" id="submitBtn" class="btn btn-wayo w-100 fw-bold">
                <?php echo get_phrase('Send'); ?>
              </button>
              <div id="formSuccess" class="alert alert-success mt-3 d-none" role="alert">
                ✅ <?php echo get_phrase('Message_sent!_Thank_you'); ?>
              </div>
              <div id="formError" class="alert alert-danger mt-3 d-none" role="alert"></div>
            </div>
          </div>
        </form>
      </div>
 
      <!-- Sidebar infos -->
      <aside class="col-12 col-lg-5">
        <div class="glass h-100 p-3 p-md-4 text-start d-flex flex-column justify-content-center align-items-center">
          <h2 class="h4 fw-bold mb-4"><?php echo get_phrase('Contact_Information'); ?></h2>
          <ul class="list-unstyled d-flex flex-column gap-3 contact-list mb-4">
            <li>
              <i class="fa-solid fa-phone"></i>
              <a class="text-decoration-none text-dark" href="tel:+971501548923">+971 50 154 8923</a>
            </li>
            <li>
              <i class="fa-solid fa-envelope"></i>
              <a class="text-decoration-none text-dark" href="mailto:info@wayo.cloud">info@wayo.cloud</a>
            </li>
            <li>
              <i class="fa-solid fa-location-dot"></i>
              <a class="text-decoration-none text-dark" href="#map">R320 Umm Hurair 2, Dubai, UAE</a>
            </li>
          </ul>
 
          <hr class="opacity-25 w-100 my-4">
         
          <h3 class="h6 fw-bold mb-3"><?php echo get_phrase('Follow_us'); ?></h3>
          <div class="d-flex justify-content-center gap-2">
            <a href="https://www.facebook.com/people/Wayo-Academy/61572524656807/" target="_blank" class="social-btn">
              <i class="fa-brands fa-facebook-f"></i>
            </a>
            <a href="https://www.instagram.com/wayo_academy/" target="_blank" class="social-btn">
              <i class="fa-brands fa-instagram"></i>
            </a>
            <a href="https://www.linkedin.com/company/wayoacademy/" target="_blank" class="social-btn">
              <i class="fa-brands fa-linkedin-in"></i>
            </a>
          </div>
        </div>
      </aside>
    </div>
 
 <!-- OÙ NOUS TROUVER -->
    <section id="contact" class="contact py-5">
      <div class="container">
        <div class="row g-3 align-items-stretch">
          <div class="col-12 col-lg-12" data-animate>
            <article class="p-3 bg-white rounded-4 shadow-sm h-100">
              <div class="map-embed rounded-3">
                    <iframe
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3608.773591177115!2d55.306372586657815!3d25.24454967224629!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3e5f42d392745e8d%3A0x263aa4ef8ed1cdb8!2sUmm%20Hurair%20Rd%20-%20Dubai%20-%20Émirats%20arabes%20unis!5e0!3m2!1sfr!2sma!4v1759136094142!5m2!1sfr!2sma%22"
                    width="100%"
                    height="450"
                    style="border:0;"
                    allowfullscreen=""
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade">
                  </iframe>
            </article>
          </div>
        </div>
      </div>
    </section>
</main>
 
<!-- ===================== SCRIPT ===================== -->
<script>
const contactForm = document.getElementById('contact_send');
const submitBtn = document.getElementById('submitBtn');
const alertS = document.getElementById('formSuccess');
const errorS = document.getElementById('formError');
 
if (contactForm) {
 
  // === Crée un div d’erreur sous chaque input ===
  contactForm.querySelectorAll('input, textarea').forEach(input => {
    const errorDiv = document.createElement('div');
    errorDiv.className = 'error-message text-danger small mt-1 d-none';
    input.parentNode.appendChild(errorDiv);
 
    input.addEventListener('input', () => {
      input.classList.remove('is-invalid', 'is-valid');
      errorDiv.classList.add('d-none');
    });
  });
 
  // === GESTION DU CODE PAYS AUTOMATIQUE ===
const phoneInput = document.getElementById('phoneInput');
const countrySelect = document.getElementById('countrySelect');
 
// Quand on choisit un pays → insérer le code une seule fois (sans espace)
countrySelect.addEventListener('change', () => {
  const code = countrySelect.value;
  const current = phoneInput.value.trim();
 
  // Si le code n'est pas encore présent au début → on le met
  if (!current.startsWith(code)) {
    phoneInput.value = code;
  }
});
 
// S'assurer que le code du pays est présent au chargement
window.addEventListener('DOMContentLoaded', () => {
  const code = countrySelect.value;
  if (!phoneInput.value.trim()) {
    phoneInput.value = code;
  }
});
 
// Empêcher la suppression du code pays
phoneInput.addEventListener('input', () => {
  const code = countrySelect.value;
  if (!phoneInput.value.startsWith(code)) {
    // Retirer tout caractère avant + et réécrire correctement
    const cleaned = phoneInput.value.replace(/^\+?[0-9]*/, '');
    phoneInput.value = code + cleaned;
  }
});
 
  // === Validation du formulaire ===
  contactForm.addEventListener('submit', function (event) {
    event.preventDefault();
 
    let isValid = true;
    const firstName = contactForm.querySelector('[name="first_name"]');
    const lastName  = contactForm.querySelector('[name="last_name"]');
    const email     = contactForm.querySelector('[name="email"]');
    const phone     = contactForm.querySelector('[name="phone"]');
    const message   = contactForm.querySelector('[name="comment"]');
 
    // Regex
    const nameRegex  = /^[A-Za-zÀ-ÿ' -]{2,}$/;
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    const phoneRegex = /^\+[0-9]{10,15}$/;
 
    function setError(input, message) {
      const err = input.parentNode.querySelector('.error-message');
      input.classList.add('is-invalid');
      err.textContent = message;
      err.classList.remove('d-none');
      isValid = false;
    }
 
    function clearError(input) {
      const err = input.parentNode.querySelector('.error-message');
      input.classList.remove('is-invalid');
      input.classList.add('is-valid');
      err.classList.add('d-none');
    }
 
    // Validation
    if (!nameRegex.test(firstName.value.trim())) setError(firstName, '<?php echo get_phrase("Please_enter_a_valid_first_name"); ?>');
    else clearError(firstName);
 
    if (!nameRegex.test(lastName.value.trim())) setError(lastName, '<?php echo get_phrase("Please enter a valid last name"); ?>');
    else clearError(lastName);
 
    if (!emailRegex.test(email.value.trim())) setError(email, '<?php echo get_phrase("Please enter a valid email address"); ?>');
    else clearError(email);
 
    if (!phoneRegex.test(phone.value.trim())) setError(phone, '<?php echo get_phrase("Phone must start with + and contain 10–15 digits"); ?>');
    else clearError(phone);
 
    if (message.value.trim().length < 5) setError(message, '<?php echo get_phrase("Please write a message of at least 5 characters"); ?>');
    else clearError(message);
 
    if (!isValid) return;
 
  // Envoi AJAX
submitBtn.classList.add('btn-loading');
submitBtn.innerText = 'Sending...';
 
const formData = new FormData(contactForm);
 
// 🟩 Afficher toutes les données du formulaire dans la console
console.log("===== Données du formulaire =====");
for (let [key, value] of formData.entries()) {
  console.log(`${key}:`, value);
}
console.log("=================================");
 
fetch(contactForm.action, {
  method: 'POST',
  body: formData,
  headers: { 'X-Requested-With': 'XMLHttpRequest' }
})
.then(response => response.text())
.then(text => {
  try {
    return JSON.parse(text);
  } catch (e) {
    throw new Error('Invalid JSON response from server');
  }
})
.then(data => {
  if (data.status === 1) {
    alertS.classList.remove('d-none');
    errorS.classList.add('d-none');
    contactForm.reset();
    contactForm.querySelectorAll('.is-valid').forEach(el => el.classList.remove('is-valid'));
 
    // Remettre le code pays par défaut après reset
    phoneInput.value = countrySelect.value + ' ';
    setTimeout(() => alertS.classList.add('d-none'), 4000);
  } else {
    errorS.innerText = data.message || 'Failed to send your message.';
    errorS.classList.remove('d-none');
    setTimeout(() => errorS.classList.add('d-none'), 5000);
  }
})
.catch(error => {
  errorS.innerText = 'An error occurred: ' + error.message;
  errorS.classList.remove('d-none');
  setTimeout(() => errorS.classList.add('d-none'), 5000);
})
.finally(() => {
  submitBtn.classList.remove('btn-loading');
  submitBtn.innerText = '<?php echo get_phrase("Send"); ?>';
});
  });
}
</script>