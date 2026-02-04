<link rel="stylesheet" href="<?php echo base_url('assets/frontend/ultimate/css/flag-icons/flag-icons.min.css'); ?>">
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

    /* === NOUVEAU STYLE UNIFIÉ === */
    .unified-phone-wrapper {
        display: flex;
        align-items: stretch;
        border: 1px solid #e0e0e0;
        border-radius: 10px;
        background: #fff;
        transition: all 0.3s ease;
        /* overflow: hidden; REMOVED to allow dropdown */
        height: 58px; 
    }

    /* Effet focus global sur le wrapper */
    .unified-phone-wrapper:focus-within {
        border-color: #ff6b35;
        box-shadow: 0 0 0 0.2rem rgba(255, 107, 53, 0.25);
    }

    /* Style quand invalide */
    .unified-phone-wrapper.is-invalid {
        border-color: #dc3545 !important;
    }

    /* Style du selecteur pays à l'intérieur */
    .unified-phone-wrapper .country-select-wrapper {
        width: 110px; 
        border-right: 1px solid #eee;
        background-color: #fafafa;
        display: flex;
        align-items: center;
        position: relative;
        border-top-left-radius: 10px;
        border-bottom-left-radius: 10px;
    }

    .unified-phone-wrapper .form-select {
        display: none; /* Hide native select if any */
    }

    /* Custom Dropdown Styles */
    .custom-select-trigger {
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0 1rem;
        cursor: pointer;
        width: 100%;
    }

    .custom-options {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: #fff;
        border: 1px solid #e0e0e0;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        z-index: 1000;
        display: none;
        max-height: 200px;
        overflow-y: auto;
        margin-top: 4px;
        width: 100%; /* Match wrapper width */
        min-width: 80px;
    }

    .custom-options.open {
        display: block;
    }

    .custom-option {
        padding: 10px;
        cursor: pointer;
        text-align: center;
        transition: background 0.2s;
    }

    .custom-option:hover {
        background-color: #fff2ea;
        color: #ff6b35;
    }

    .custom-option.selected {
        background-color: #fff2ea;
        font-weight: bold;
    }
    
    .fi {
        font-size: 1.2em;
        line-height: 1em;
        border-radius: 4px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    /* Style de l'input téléphone à l'intérieur */
    .unified-phone-wrapper .form-floating {
        flex-grow: 1;
    }

    .unified-phone-wrapper .form-control {
        border: none !important;
        box-shadow: none !important;
        background-color: transparent !important;
        height: 56px; /* slightly less to fit inside */
    }
    
    .unified-phone-wrapper .form-control:focus {
        box-shadow: none !important;
    }
    /* === RTL SUPPORT FOR FLOATING LABELS === */
    html[lang="ar"] .form-floating > label,
    [dir="rtl"] .form-floating > label {
        left: auto;
        right: 0;
        transform-origin: 100% 0;
    }

    [dir="rtl"] .form-floating > .form-control:focus ~ label,
    [dir="rtl"] .form-floating > .form-control:not(:placeholder-shown) ~ label,
    [dir="rtl"] .form-floating > .form-select ~ label {
        transform: scale(0.85) translateY(-0.5rem) translateX(0.15rem);
    }

    /* === RTL SPECIFIC FOR PHONE INPUT === */
    [dir="rtl"] .unified-phone-wrapper .country-select-wrapper {
        border-right: none;
        border-left: 1px solid #eee;
        border-radius: 0 10px 10px 0; /* Flip rounding to right side */
    }

    [dir="rtl"] .unified-phone-wrapper .form-control {
        text-align: right; 
        direction: ltr; /* Keep numbers LTR but align block right */
    }

    /* DEBUG: Force MA flag visibility */
    .fi-ma {
        background-image: url('<?php echo base_url("assets/frontend/ultimate/css/flag-icons/flags/4x3/ma.svg"); ?>') !important;
    }
  </style>
 
 
<!-- ========== HERO ========== -->
<section class="contact-hero position-relative text-center" <?php echo (get_user_language() === 'arabic') ? 'dir="rtl"' : 'dir="ltr"'; ?>>
  <div class="hero-overlay"></div>
  <div class="container position-relative hero-inner">
    <h1 class="fw-extrabold text-dark"><?php echo get_phrase("Support") ?></h1>
    <p class="mt-2 mb-0"><strong><?php echo get_phrase("Any questions?") ?></strong>
      <strong><?php echo get_phrase("Response guaranteed within 24 business hours.") ?></strong>
    </p>
  </div>
</section>
 
<?php $this->load->view('frontend/alert_view'); ?>
<main class="container py-5" <?php echo (get_user_language() === 'arabic') ? 'dir="rtl"' : 'dir="ltr"'; ?>>
      <div class="row g-4 align-items-stretch">
      <div class="col-12 col-lg-12">
        <form id="contact_send" class="glass p-3 p-md-4 needs-validation" action="<?php echo site_url('home/contact/send'); ?>" method="post" enctype="multipart/form-data" novalidate>
          <!-- Champ caché pour le jeton CSRF -->
                <input type="hidden" name="<?=$this->security->get_csrf_token_name();?>" value="<?=$this->security->get_csrf_hash();?>" />
         
          <h2 class="h3 fw-bold mb-4 text-center"><?php echo get_phrase('Support_Contact'); ?></h2>
         
          <div class="row g-3">
            <!-- Prénom -->
            <div class="col-12 col-md-6" >
              <div class="form-floating">
                <input type="text" class="form-control shadow-none" id="firstName"
                       placeholder="Prénom" name="first_name" required >
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
 
            <!-- Téléphone avec sélecteur de pays UNIFIÉ -->
            <div class="col-12 col-md-6">
              <div class="unified-phone-wrapper">
                <div class="country-select-wrapper">
                   <?php include 'partials/countrySelect.php'; ?>
                </div>
                <div class="form-floating">
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
      <div class="row g-4 align-items-stretch">
        <!-- OÙ NOUS TROUVER -->
        <div class="col-12 col-lg-8">
          <article class="p-3 bg-white glass rounded-4 shadow-sm h-100">
            <div class="map-embed rounded-3 h-100">
              <iframe
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3608.773591177115!2d55.306372586657815!3d25.24454967224629!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3e5f42d392745e8d%3A0x263aa4ef8ed1cdb8!2sUmm%20Hurair%20Rd%20-%20Dubai%20-%20Émirats%20arabes%20unis!5e0!3m2!1sfr!2sma!4v1759136094142!5m2!1sfr!2sma"
                width="100%"
                height="100%"
                style="border:0; min-height: 450px;"
                allowfullscreen=""
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade">
              </iframe>
            </div>
          </article>
        </div>

        <!-- Sidebar infos -->
        <aside class="col-12 col-lg-4">
          <div class="glass h-100 p-3 p-md-4 <?php echo (get_user_language() === 'arabic') ? 'text-center' : 'text-start'; ?> d-flex flex-column justify-content-center align-items-center">
            <h2 class="h4 fw-bold mb-4"><?php echo get_phrase('Support_Information'); ?></h2>
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
              <a href="https://www.instagram.com/wayo_ma/" target="_blank" class="social-btn">
                <i class="fa-brands fa-instagram"></i>
              </a>
              <a href="https://www.linkedin.com/company/wayo-ma/" target="_blank" class="social-btn">
                <i class="fa-brands fa-linkedin-in"></i>
              </a>
              <a href="https://www.youtube.com/@Wayo-ma" target="_blank" class="social-btn">
                <i class="fa-brands fa-youtube"></i>
              </a>
            </div>
          </div>
        </aside>
      </div>
    </div>
 

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
    // On ignore le select-trigger car c'est pas un input de saisie
    if (input.id === 'countrySelect') return;

    const errorDiv = document.createElement('div');
    errorDiv.className = 'error-message text-danger small d-none';
    errorDiv.style.fontSize = '0.75rem';
    errorDiv.style.marginTop = '2px';
    
    // On l'ajoute à la colonne parente pour éviter d'être bloqué par les wrappers flex/floating
    const container = input.closest('.col-12, .col-md-6');
    if (container) {
        container.appendChild(errorDiv);
    } else {
        input.parentNode.appendChild(errorDiv);
    }
 
    input.addEventListener('input', () => {
      input.classList.remove('is-invalid', 'is-valid');
      errorDiv.classList.add('d-none');
      
      // Si c'est le téléphone, on retire aussi la classe du wrapper
      if (input.id === 'phoneInput') {
          input.closest('.unified-phone-wrapper')?.classList.remove('is-invalid');
      }
    });
  });
 
  // === GESTION DU CODE PAYS CUSTOM ET AUTOMATIQUE ===
const phoneInput = document.getElementById('phoneInput');
// Custom Dropdown Elements
const countryInput = document.getElementById('countrySelect'); // Hidden input
const countryTrigger = document.getElementById('countryTrigger');
const countryOptions = document.getElementById('countryOptions');
const selectedFlag = document.getElementById('selectedFlag');
const options = document.querySelectorAll('.custom-option');

let previousCode = countryInput.value; 

// Toggle Dropdown
countryTrigger.addEventListener('click', (e) => {
    e.stopPropagation(); // Stop bubbling
    countryOptions.classList.toggle('open');
});

// Close when clicking outside
document.addEventListener('click', () => {
    countryOptions.classList.remove('open');
});

// Select Option Logic
options.forEach(option => {
    option.addEventListener('click', function(e) {
        e.stopPropagation();
        const value = this.getAttribute('data-value');
        const flag = this.getAttribute('data-flag');

        // Update UI
        selectedFlag.className = `fi fi-${flag}`;
        countryInput.value = value;
        
        // Update selection state
        options.forEach(opt => opt.classList.remove('selected'));
        this.classList.add('selected');

        countryOptions.classList.remove('open');

        // Trigger logic to update phone input
        updatePhoneCode();
    });
});

// Fonction pour mettre à jour le code (Logic preserved)
function updatePhoneCode() {
  const newCode = countryInput.value;
  let currentVal = phoneInput.value;

  if (currentVal.startsWith(previousCode)) {
    phoneInput.value = newCode + currentVal.substring(previousCode.length);
  } 
  else if (!currentVal.trim() || !currentVal.startsWith('+')) {
    phoneInput.value = newCode;
  }
  else {
     phoneInput.value = newCode + currentVal.replace(/^\+\d+\s*/, '');
  }
  
  previousCode = newCode; 
}

// Init
window.addEventListener('DOMContentLoaded', () => {
    if (!phoneInput.value.trim()) {
        phoneInput.value = countryInput.value;
    }
    previousCode = countryInput.value;
    
    // Set initial flag based on value
    const initialOpt = document.querySelector(`.custom-option[data-value="${countryInput.value}"]`);
    if(initialOpt) {
       const flag = initialOpt.getAttribute('data-flag'); 
       selectedFlag.className = `fi fi-${flag}`;
    }
});

// === REVERSE LOOKUP LOGIC: AUTO-SELECT FLAG WHEN TYPING CODE ===
if (phoneInput && countryInput) {
    phoneInput.addEventListener('input', () => {
        const val = phoneInput.value.trim();
        
        // Allow user to clear input or type anything, but if it looks like a code, try to match
        if (!val.startsWith('+')) return;

        // Find matching option
        let bestMatch = null;
        let bestLen = 0;

        options.forEach(opt => {
            const code = opt.getAttribute('data-value');
            if (val.startsWith(code)) {
                if (code.length > bestLen) {
                    bestLen = code.length;
                    bestMatch = opt;
                }
            }
        });

        if (bestMatch) {
            const code = bestMatch.getAttribute('data-value');
            const flag = bestMatch.getAttribute('data-flag');
            
            // Only update if changed
            if (countryInput.value !== code) {
                 countryInput.value = code;
                 selectedFlag.className = `fi fi-${flag}`;
                 previousCode = code; // Sync previousCode

                 // Update selection in dropdown
                 options.forEach(o => o.classList.remove('selected'));
                 bestMatch.classList.add('selected');
            }
        }
    });
}

contactForm.addEventListener('reset', () => {
    setTimeout(() => {
        // Reset to default (MA)
        countryInput.value = "+212";
        selectedFlag.className = "fi fi-ma";
        phoneInput.value = "+212";
        previousCode = "+212";
    }, 10);
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
      // Trouver l'error-message dans le container parent (col-...)
      const container = input.closest('.col-12, .col-md-6');
      const err = container.querySelector('.error-message');
      
      input.classList.add('is-invalid');
      if (input.id === 'phoneInput') {
          input.closest('.unified-phone-wrapper')?.classList.add('is-invalid');
      }

      err.textContent = message;
      err.classList.remove('d-none');
      isValid = false;
    }
 
    function clearError(input) {
      const container = input.closest('.col-12, .col-md-6');
      const err = container.querySelector('.error-message');
      
      input.classList.remove('is-invalid');
      if (input.id === 'phoneInput') {
          input.closest('.unified-phone-wrapper')?.classList.remove('is-invalid');
      }

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
    phoneInput.value = countryInput.value + ' ';
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