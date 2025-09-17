

  <!-- ========== HERO ========== -->
  <section class="contact-hero position-relative text-center mt-5">
    <div class="hero-overlay"></div>
    <div class="container position-relative hero-inner">
      <h1 class="fw-extrabold text-dark"><?php echo get_phrase("Contact Us") ?></h1>
      <p class="mt-2 mb-0"><?php echo get_phrase("Any questions?") ?> <strong><?php echo get_phrase("Response guaranteed within 24 business hours.") ?></strong></p>
    </div>
  </section>

  <!-- ========== FORM + INFO (Bootstrap Grid) ========== -->
  <main class="container py-5">
    <div class="row g-4 align-items-stretch">

      <!-- —— Formulaire —— -->
      <div class="col-12 col-lg-7">
        <form id="contactForm" novalidate class="card glass border-0 p-3 p-md-4 needs-validation">
          <div class="row g-3">
            <div class="col-12 col-md-6">
              <div class="form-floating">
                <input id="prenom" name="prenom" class="form-control" placeholder="Prénom" required>
                <label for="prenom"><?php echo get_phrase("First Name*") ?></label>
                <div class="invalid-feedback"><?php echo get_phrase("First name required.") ?></div>
              </div>
            </div>
            <div class="col-12 col-md-6">
              <div class="form-floating">
                <input id="nom" name="nom" class="form-control" placeholder="Nom" required>
                <label for="nom"><?php echo get_phrase("Last Name*") ?></label>
                <div class="invalid-feedback"><?php echo get_phrase("Last name required.") ?></div>
              </div>
            </div>

            <div class="col-12">
              <div class="form-floating">
                <input type="email" id="email" name="email" class="form-control" placeholder="nom@exemple.com" required>
                <label for="email"><?php echo get_phrase("Email*") ?></label>
                <div class="invalid-feedback"><?php echo get_phrase("Valid email required.") ?></div>
              </div>
            </div>

            <div class="col-12">
              <div class="form-floating">
                <input id="adresse" name="adresse" class="form-control" placeholder="Adresse postale">
                <label for="adresse"><?php echo get_phrase("Postal Address") ?></label>
              </div>
            </div>

            <div class="col-12 col-md-6">
              <div class="form-floating">
                <input type="tel" id="tel" name="tel" class="form-control" placeholder="+212 6 XX XX XX XX" pattern="\+?[0-9\s\-]{6,}">
                <label for="tel"><?php echo get_phrase("Phone") ?></label>
                <div class="invalid-feedback"><?php echo get_phrase("Invalid number.") ?></div>
              </div>
            </div>

            <div class="col-12 col-md-6">
              <div class="form-floating">
                <input id="ville" name="ville" class="form-control" placeholder="Ville / Pays">
                <label for="ville"><?php echo get_phrase("City / Country") ?></label>
              </div>
            </div>

            <div class="col-12">
              <div class="form-floating">
                <textarea id="message" name="message" class="form-control h-150" placeholder="Votre message" required></textarea>
                <label for="message"><?php echo get_phrase("Message*") ?></label>
                <div class="invalid-feedback"><?php echo get_phrase("Please write a message.") ?></div>
              </div>
            </div>

            <div class="col-12">
              <button type="submit" class="btn btn-wayo w-100 fw-bold ripple"><?php echo get_phrase("Send") ?></button>
              <p class="form-note text-muted small mt-2 mb-0"><?php echo get_phrase("* Required fields") ?></p>
              <div id="formSuccess" class="alert alert-success mt-3 d-none" role="alert">
                ✅  <?php echo get_phrase("Message sent! Thank you") ?> 😊 
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
              <i class="fa-solid fa-phone text-primary"></i> +971 50 154 8923
            </li>
            <li class="d-flex align-items-center gap-3">
              <i class="fa-solid fa-envelope text-primary"></i> info@wayo.cloud
            </li>
            <li class="d-flex align-items-center gap-3">
              <i class="fa-solid fa-location-dot text-primary"></i> <?php echo get_phrase(" R320 Umm Hurair 2, Dubai, UAE") ?>
            </li>
          </ul>

          <hr class="opacity-10 w-100">

          <h3 class="h6 fw-bold mb-3"><?php echo get_phrase("Follow us") ?></h3>
          <div class="d-flex justify-content-center gap-2">
            <a href="#" aria-label="Facebook"  class="social-btn"><i class="fa-brands fa-facebook-f"></i></a>
            <a href="#" aria-label="X"        class="social-btn"><i class="fa-brands fa-x-twitter"></i></a>
            <a href="#" aria-label="Instagram" class="social-btn"><i class="fa-brands fa-instagram"></i></a>
            <a href="#" aria-label="LinkedIn"  class="social-btn"><i class="fa-brands fa-linkedin-in"></i></a>
          </div>
        </div>
      </aside>


    </div>
  </main>

  <!-- ========== CARTE ========= -->
  <section class="container pb-5">
    <div class="ratio ratio-16x9 shadow-sm map-radius">
      <iframe
        title="Bureau Wayo Academy"
        loading="lazy"
        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3608.050704924542!2d55.311578975116506!3d25.2440513317751!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3e5f5d58ffffffff%3A0x7e1b9fb29a8f4e66!2sUmm%20Hurair%202%20-%20Dubai!5e0!3m2!1sfr!2sae!4v1700000000000!5m2!1sfr!2sae"
        allowfullscreen></iframe>
    </div>
  </section>

  <script>
    /* === Bootstrap validation + success alert === */
const form   = document.getElementById('contactForm');
const alertS = document.getElementById('formSuccess');

if (form){
  form.addEventListener('submit', (e)=>{
    e.preventDefault();
    if (!form.checkValidity()){
      form.classList.add('was-validated');
      return;
    }
    form.classList.remove('was-validated');
    form.reset();
    alertS?.classList.remove('d-none');
    setTimeout(()=> alertS?.classList.add('d-none'), 5000);
  });
}

/* === Newsletter (demo) === */
document.querySelectorAll('.newsletter-form')?.forEach(f=>{
  f.addEventListener('submit', e=>{
    e.preventDefault();
    // brancher votre endpoint ici
  });
});

/* === Ripple button effect === */
document.querySelectorAll('.ripple').forEach(btn =>{
  btn.addEventListener('click', () =>{
    btn.classList.remove('ripple');
    void btn.offsetWidth;
    btn.classList.add('ripple');
  });
});

  </script>

