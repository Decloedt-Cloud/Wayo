<style>
   /* Hero */
    .hero{ position:relative; min-height:68vh; display:grid; place-items:center; color:#fff; background-image:url('../uploads/images/decloedt/img/bg-tutoria.png'); background-size:cover; background-position:center; }
    .hero::before{ content:""; position:absolute; inset:0; background:linear-gradient(180deg, rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.60));}
    .hero .hero-content{ position:relative; text-align:center; }
    .hero .lead{ max-width:760px; margin-inline:auto; color:#e9e9ef }

    .btn-pill{ border-radius:999px; font-weight:700; padding:.8rem 1.2rem; }
    .btn-accent{ background:var(--accent); color:#fff; box-shadow:0 8px 20px rgba(252,123,48,.25) }
    .btn-accent:hover{ background:var(--accent-600); color:#fff; }
    .btn-ghost{ background:#ffffff14; color:#fff; border:1px solid #ffffff40 }
    .btn-ghost:hover{ background:#ffffff26; color:#fff; }
</style>
<!-- ==================== TUTORIEL BODY ==================== -->
<main class=" py-5  ">
    <section class="hero">
      <div class="container hero-content py-5" data-animate>
        <h1 class="display-5 fw-bold mb-2"><?php echo get_phrase("Tutoriel") ?></h1>
        <p class="lead mb-4 text-white fs-md-4 fs-lg-3" style="letter-spacing: 1px; font-size: 1.5rem; margin-bottom: 1rem;"><?php echo get_phrase("Launch your community on Wayo Academy") ?></p>
      </div>
    </section>
  <div class="tutorial-body">
  <div class="container py-5">
  <div class="row g-4 align-items-center ">
    
    <!-- ————— Colonne gauche : description ————— -->
    <section class="col-12 col-lg-6">
      <h1 class="tuto-heading h2 fw-bold">
        <span class="badge badge-tutorial rounded-pill text-bg-orange me-2"><?php echo get_phrase("Tutoriel") ?></span>
        <?php echo get_phrase("Launch your community on Wayo Academy") ?>
      </h1>

      <p class="mb-3">
        <?php echo get_phrase("This video guide") ?> (<strong> 10&nbsp;<?php echo get_phrase("minutes") ?>
      </strong>) <?php echo get_phrase("It guides you step by step to publish and monetize your knowledge with thousands of learners.") ?>
      </p>

      <ul class="tuto-list list-unstyled d-grid gap-2">
        <li class="ps-4 position-relative">
          <i class="fa-solid fa-check position-absolute start-0 text-orange"></i>
          <?php echo get_phrase("Create your Community space and set your goals.") ?>
        </li>
        <li class="ps-4 position-relative">
          <i class="fa-solid fa-check position-absolute start-0 text-orange"></i>
          <?php echo get_phrase("Customize your homepage: branding, rules, visuals.") ?>
        </li>
        <li class="ps-4 position-relative">
          <i class="fa-solid fa-check position-absolute start-0 text-orange"></i>
          <?php echo get_phrase("Add your first classes: live, replay, files.") ?> &amp; <?php echo get_phrase("quiz") ?>
        </li>
        <li class="ps-4 position-relative">
          <i class="fa-solid fa-check position-absolute start-0 text-orange"></i>
          <?php echo get_phrase("Invite members, manage roles, and subscription plans.") ?>
        </li>
        <li class="ps-4 position-relative">
          <i class="fa-solid fa-check position-absolute start-0 text-orange"></i>
          <?php echo get_phrase("Track engagement, revenue, and feedback from the Mentor dashboard.") ?>
        </li>
      </ul>
    </section>

    <!-- ————— Colonne droite : vidéo ————— -->
    <section class="col-12 col-lg-6">
      <div class="ratio ratio-16x9 shadow-sm rounded-3 overflow-hidden video-frame">
        <iframe
          src="https://www.youtube.com/embed/2e6g2Lsl7aw?rel=0"
          title="Tutoriel Mentor Wayo Academy"
          allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
          allowfullscreen></iframe>
      </div>
    </section>

  </div>
  </div>
</div>
</main>

  <script>
    // Back-to-top
const topBtn = document.getElementById('scrollTopBtn');
const revealTop = () => {
  if (window.scrollY > 200) {
    topBtn.classList.add('show');
  } else {
    topBtn.classList.remove('show');
  }
};
window.addEventListener('scroll', revealTop);
topBtn.addEventListener('click', () => window.scrollTo({top:0, behavior:'smooth'}));

// (Optionnel) Empêcher le submit vide de la newsletter – juste pour la démo
document.querySelectorAll('.newsletter-form').forEach(form => {
  form.addEventListener('submit', (e) => {
    const input = form.querySelector('input[type="email"]');
    if (!input.value.trim()) {
      e.preventDefault();
      input.focus();
    }
  });
});
  </script>
