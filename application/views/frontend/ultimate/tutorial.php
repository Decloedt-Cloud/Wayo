
  <!-- ===== PAGE BODY ===== -->
<main class="tutorial-body" <?php echo (get_user_language() === 'arabic') ? 'dir="rtl"' : 'dir="ltr"'; ?>>
  <div class="container">
    <div class="row align-items-center">
      
      <!-- Colonne gauche : description -->
      <div class="col-lg-6 col-md-12 mb-4 mb-lg-0">
        <section class="tuto-desc">
          <h1 class="tuto-heading">
            <span class="tag"><?php echo get_phrase("Tutoriel ") ?></span> <?php echo get_phrase("Launch your community on Wayo Academy") ?>
          </h1>

          <p>
            <?php echo get_phrase("This video guide") ?><?php echo get_phrase("Guides you step by step to publish and monetize your knowledge to thousands of learners. ") ?>
          </p>

          <ul class="tuto-list list-group">
            <li><?php echo get_phrase("Create your Community space and set your goals.") ?></li>
            <li><?php echo get_phrase("Customize the homepage: branding, rules, visuals.") ?></li>
            <li><?php echo get_phrase("Add your first classes: live, replay, files.") ?> &amp; <?php echo get_phrase("quiz") ?></li>
            <li><?php echo get_phrase("Invite members, manage roles, and subscription plans.") ?></li>
            <li><?php echo get_phrase("Track engagement, revenue, and feedback from the Mentor dashboard.") ?></li>
          </ul>

          <!-- <p class="tuto-author mt-3">
            <i class="fa-solid fa-user-circle"></i>
            Présenté par <strong>Aymane</strong>, Responsable Communautés.
          </p> -->
        </section>
      </div>

      <!-- Colonne droite : vidéo -->
      <div class="col-lg-6 col-md-12">
        <section class="tuto-video">
          <div class="video-frame">
            <iframe
              src="https://www.youtube.com/embed/2e6g2Lsl7aw?rel=0"
              title="Tutoriel Mentor Wayo Academy"
              frameborder="0"
              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
              allowfullscreen></iframe>
          </div>
        </section>
      </div>


    </div>
  </div>
</main>

  <!-- ============== COMMENT ÇA MARCHE — Processus (Style Stepper) ============== -->
  <section id="comment-ca-marche" class="obi" <?php echo (get_user_language() === 'arabic') ? 'dir="rtl"' : 'dir="ltr"'; ?>>
    <div class="obi-shell">
      <header class="obi-head">
        <span class="obi-kicker"><?php echo get_phrase("How does it work?") ?></span>
        <p><?php echo get_phrase("Follow these 4 steps to create your profile, set up your community, add your classes, and launch your offer.") ?></p>
      </header>

      <!-- Stepper ribbon -->
      <div class="obi-ribbon" role="progressbar" aria-valuemin="0" aria-valuemax="4" aria-valuenow="1">
        <div class="obi-ribbon__track">
          <div class="obi-ribbon__fill" style="--progress:25%"></div>
        </div>
        <ol class="obi-ribbon__steps">
          <li class="is-active"><span>1</span><?php echo get_phrase("Profile") ?></li>
          <li><span>2</span><?php echo get_phrase("Community") ?></li>
          <li><span>3</span><?php echo get_phrase("Classes") ?></li>
          <li><span>4</span><?php echo get_phrase("Launch") ?></li>
        </ol>
      </div>

      <!-- Cards -->
      <div class="obi-grid">
        <!-- 1. Profil -->
        <article class="obi-card" data-animate data-step="1">
          <div class="obi-num">1</div>
          <div class="obi-icon"><i class="fa-solid fa-id-card-clip"></i></div>
          <h3><?php echo get_phrase("Create a profile") ?></h3>
          <p><?php echo get_phrase("Enter your basic information to secure and personalize your space.") ?></p>
          <ul class="obi-bullets">
            <li><i class="fa-regular fa-user"></i> <strong><?php echo get_phrase("Full name") ?></strong> <?php echo get_phrase("(displayed publicly)") ?></li>
            <li><i class="fa-regular fa-envelope"></i> <strong><?php echo get_phrase("Email") ?></strong> <?php echo get_phrase("(verification required)") ?></li>
            <li><i class="fa-solid fa-phone"></i> <strong><?php echo get_phrase("Phone number") ?></strong> <?php echo get_phrase("(support et notifications)") ?></li>
            <li><i class="fa-solid fa-key"></i> <strong><?php echo get_phrase("Password") ?></strong> <?php echo get_phrase("(12+ characters, letters/numbers/symbols)") ?></li>
          </ul>
        </article>

        <!-- 2. Communauté -->
        <article class="obi-card" data-animate data-step="2">
          <div class="obi-num">2</div>
          <div class="obi-icon"><i class="fa-solid fa-users-gear"></i></div>
          <h3><?php echo get_phrase("Create a community") ?></h3>
          <p><?php echo get_phrase("Clearly present your value proposition and set the price.") ?></p>
          <ul class="obi-bullets">
            <li><i class="fa-solid fa-video"></i> <strong><?php echo get_phrase("Community video") ?></strong> <?php echo get_phrase("(YouTube/Vimeo ou téléversement) ") ?></li>
            <li><i class="fa-regular fa-file-lines"></i> <strong><?php echo get_phrase("Community description") ?></strong> (300–600&nbsp;<?php echo get_phrase("characters") ?>)</li>
            <li><i class="fa-solid fa-tags"></i> <strong><?php echo get_phrase("Tags") ?></strong> <?php echo get_phrase("(3–5 keywords for search)") ?></li>
            <li><i class="fa-solid fa-dollar-sign"></i> <strong><?php echo get_phrase("Price") ?></strong> <?php echo get_phrase("(monthly subscription or one-time payment)") ?></li>
          </ul>
          <details class="obi-hint">
            <summary><?php echo get_phrase("Visual tips") ?></summary>
            <div style="color:#878787 ; font-size:13px"><?php echo get_phrase("Upload a logo (PNG, 256×256) and a cover image (JPG/PNG, 1600×600) to make your page look professional.") ?></div>
          </details>
        </article>

        <!-- 3. Classes -->
        <article class="obi-card" data-animate data-step="3">
          <div class="obi-num">3</div>
          <div class="obi-icon"><i class="fa-solid fa-chalkboard-user"></i></div>
          <h3><?php echo get_phrase("Create classes") ?></h3>
          <p><?php echo get_phrase("Add your educational content and organize your catalog.") ?></p>
          <ul class="obi-bullets">
            <li><i class="fa-solid fa-clapperboard"></i> <strong><?php echo get_phrase("Type") ?></strong> : <?php echo get_phrase("live, replay, files") ?> &amp; <?php echo get_phrase("quiz") ?></li>
            <li><i class="fa-regular fa-clock"></i> <strong><?php echo get_phrase("Recommended duration") ?></strong> : 7–15&nbsp;<?php echo get_phrase("min per video") ?></li>
            <li><i class="fa-solid fa-list-check"></i> <strong><?php echo get_phrase("Structure") ?></strong> : <?php echo get_phrase("Intro → Goals → Demo → Exercise → Recap") ?></li>
            <li><i class="fa-solid fa-ticket"></i> <strong><?php echo get_phrase("Class price") ?></strong> <?php echo get_phrase("(per unit) or") ?> <strong><?php echo get_phrase("included") ?></strong> <?php echo get_phrase("in the subscription") ?></li>
          </ul>
        </article>

        <!-- 4. Lancement -->
        <article class="obi-card" data-animate data-step="4">
          <div class="obi-num">4</div>
          <div class="obi-icon"><i class="fa-solid fa-rocket"></i></div>
          <h3><?php echo get_phrase("Launch and monetize") ?></h3>
          <p><?php echo get_phrase("Publish your community, promote it, and track your metrics.") ?></p>
          <ul class="obi-bullets">
            <li><i class="fa-regular fa-paper-plane"></i> <strong><?php echo get_phrase("Publishing & sharing") ?></strong> : <?php echo get_phrase("social networks, email, QR") ?></li>
            <li><i class="fa-solid fa-chart-line"></i> <strong><?php echo get_phrase("Tracking") ?></strong> : <?php echo get_phrase("views, completions, revenue (dashboard)") ?></li>
          </ul>
          <div class="obi-cta">
            <a href="<?php echo site_url('admission/online_admission'); ?>" class="obi-btn"><?php echo get_phrase("Start now") ?></a>
            <button class="obi-btn obi-btn--ghost" data-video="https://www.youtube.com/embed/2e6g2Lsl7aw"><?php echo get_phrase("Watch a demo") ?></button>
          </div>
        </article>
      </div>

      <!-- Modal vidéo -->
      <div class="obi-modal" id="obiModal" aria-hidden="true">
        <div class="obi-modal__dialog" role="dialog" aria-modal="true" aria-label="Démo tutoriel">
          <button class="obi-modal__close" aria-label="Fermer">&times;</button>
          <div class="obi-modal__body">
            <iframe id="obiFrame" src="" title="Démo" loading="lazy" allowfullscreen></iframe>
          </div>
        </div>
        <div class="obi-modal__backdrop"></div>
      </div>
    </div>
  </section>
  <!-- ============ / COMMENT ÇA MARCHE — Processus ============ -->

  <!-- Scripts -->
  <script>
    /* ===== Mobile nav ===== */
const navToggle = document.getElementById('navToggle');
const primaryNav = document.getElementById('primaryNav');
navToggle?.addEventListener('click', () => primaryNav?.classList.toggle('nav-open'));

/* ===== Scroll to top ===== */
const scrollBtn = document.getElementById('scrollTopBtn');
function toggleScrollBtn(){
  if(!scrollBtn) return;
  if(window.scrollY > 240){ scrollBtn.classList.add('show'); }
  else { scrollBtn.classList.remove('show'); }
}
window.addEventListener('scroll', toggleScrollBtn);
scrollBtn?.addEventListener('click', () => window.scrollTo({top:0,behavior:'smooth'}));

/* ===== Reveal on scroll (obi cards) ===== */
(function(){
  const items = document.querySelectorAll('.obi-card[data-animate]');
  if(!items.length) return;
  if(!('IntersectionObserver' in window)){
    items.forEach(el=>el.classList.add('revealed'));
    return;
  }
  const io = new IntersectionObserver((entries, obs)=>{
    entries.forEach(entry=>{
      if(entry.isIntersecting){
        entry.target.classList.add('revealed');
        obs.unobserve(entry.target);
      }
    });
  },{ threshold:.2 });
  items.forEach(el=>io.observe(el));
})();



/* ===== Modal vidéo (Voir une démo) ===== */
(function(){
  const modal = document.getElementById('obiModal');
  if(!modal) return;
  const frame = document.getElementById('obiFrame');
  const close = modal.querySelector('.obi-modal__close');
  const backdrop = modal.querySelector('.obi-modal__backdrop');

  document.addEventListener('click', (e)=>{
    const trigger = e.target.closest('[data-video]');
    if(trigger){
      const src = trigger.getAttribute('data-video');
      frame.src = src + (src.includes('?') ? '&' : '?') + 'autoplay=1';
      modal.classList.add('is-open');
    }
  });

  function hide(){
    modal.classList.remove('is-open');
    frame.src = '';
  }
  close.addEventListener('click', hide);
  backdrop.addEventListener('click', hide);
  document.addEventListener('keydown', (e)=>{ if(e.key === 'Escape') hide(); });
})();

/* ===== Ribbon progress auto (4 étapes) ===== */
(function(){
  const section = document.getElementById('comment-ca-marche');
  if(!section) return;
  
  const ribbon = section.querySelector('.obi-ribbon');
  const fill = section.querySelector('.obi-ribbon__fill');
  const steps = Array.from(section.querySelectorAll('.obi-ribbon__steps li'));
  const cards = Array.from(section.querySelectorAll('.obi-card[data-step]'));
  if(!fill || !steps.length || !cards.length) return;

  const total = steps.length; // 4 étapes

  const io = new IntersectionObserver((entries)=>{
    let best = {ratio:0, step:1};

    entries.forEach(ent=>{
      if(ent.isIntersecting && ent.intersectionRatio > best.ratio){
        best = {
          ratio: ent.intersectionRatio,
          step: Number(ent.target.getAttribute('data-step') || 1)
        };
      }
    });

    if(best.ratio > 0){
      let idx = Math.max(1, Math.min(total, best.step));

      // ✅ Forcer la dernière étape si la dernière carte est au moins un peu visible
      const lastCard = cards[cards.length - 1];
      if (entries.some(ent => ent.target === lastCard && ent.isIntersecting)) {
        idx = total;
      }

      const pct = (idx * 100) / total; // 25 / 50 / 75 / 100
      fill.style.setProperty('--progress', pct + '%');
      ribbon.setAttribute('aria-valuenow', String(idx));
      steps.forEach((li,i)=> li.classList.toggle('is-active', i === idx - 1));
    }
  }, {
    threshold:[0, 0.1, 0.25, 0.5] // 👈 seuils plus permissifs
  });

  cards.forEach(c=>io.observe(c));
})();
  </script>

