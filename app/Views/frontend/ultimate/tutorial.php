
  <!-- ===== PAGE BODY ===== -->
<main class="tutorial-body" <?php echo (get_user_language() === 'arabic') ? 'dir="rtl"' : 'dir="ltr"'; ?>>
  <div class="container">
    <div class="row align-items-center tutorial-hero">
      
      <!-- Colonne gauche : description -->
      <div class="col-lg-6 col-md-12 mb-4 mb-lg-0">
        <section class="tuto-desc">
          <span class="tuto-badge"><?php echo get_phrase("Creator tutorial") ?></span>
          <h1 class="tuto-heading">
            <?php echo get_phrase("Launch your community on Wayo Academy") ?>
          </h1>

          <p class="tuto-lead">
            <?php echo get_phrase("This video guide helps you step by step to publish and monetize your knowledge to thousands of learners.") ?>
          </p>

          <ul class="tuto-list list-group">
            <li><?php echo get_phrase("Create your Community space and set your goals.") ?></li>
            <li><?php echo get_phrase("Customize the homepage: branding, rules, visuals.") ?></li>
            <li><?php echo get_phrase("Add your first classes: live, replay, files and quiz") ?></li>
            <li><?php echo get_phrase("Invite members, manage roles, and subscription plans.") ?></li>
            <li><?php echo get_phrase("Track engagement, revenue, and feedback from the mentor dashboard.") ?></li>
          </ul>

          <div class="tuto-actions">
            <?php if (session()->get('user_id')) : ?>
              <a href="<?php echo route('dashboard'); ?>" class="obi-btn"><?php echo get_phrase("Start now") ?></a>
            <?php else : ?>
              <a href="<?php echo site_url('admission/online_admission'); ?>" class="obi-btn"><?php echo get_phrase("Start now") ?></a>
            <?php endif; ?>
            <button class="obi-btn obi-btn--ghost" data-video="https://www.youtube.com/embed/2e6g2Lsl7aw"><?php echo get_phrase("Watch a demo") ?></button>
          </div>

          <!-- <p class="tuto-author mt-3">
            <i class="fa-solid fa-user-circle"></i>
            Présenté par <strong>Aymane</strong>, Responsable Communautés.
          </p> -->
        </section>
      </div>

      <!-- Colonne droite : vidéo -->
      <div class="col-lg-6 col-md-12">
        <section class="tuto-video">
          <div class="video-preview" role="button" tabindex="0" data-video="https://www.youtube.com/embed/2e6g2Lsl7aw" aria-label="<?php echo esc(get_phrase('Watch tutorial demo video'), 'attr'); ?>">
            <div class="video-preview__media" aria-hidden="true">
              <i class="fa-brands fa-youtube"></i>
            </div>
            <button class="video-preview__play" type="button" data-video="https://www.youtube.com/embed/2e6g2Lsl7aw" aria-label="<?php echo esc(get_phrase('Play tutorial video'), 'attr'); ?>">
              <i class="fa-solid fa-play"></i>
              <span><?php echo get_phrase("Watch demo") ?></span>
            </button>
          </div>
          <p class="video-preview__hint">
            <?php echo get_phrase("Preview mode keeps this page fast and clean. Click to play the full demo.") ?>
          </p>
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



      <!-- Cards -->
      <div class="obi-grid">
        <!-- 1. Profil -->
        <article class="obi-card" data-animate data-step="1">
          <h3><span class="obi-num-inline">1</span> <?php echo get_phrase("Create a profile") ?></h3>
          <p><?php echo get_phrase("Enter your basic information to secure and personalize your space.") ?></p>
          <ul class="obi-bullets">
            <li><i class="fa-regular fa-user"></i> <strong><?php echo get_phrase("Full name") ?></strong> <?php echo get_phrase("(displayed publicly)") ?></li>
            <li><i class="fa-regular fa-envelope"></i> <strong><?php echo get_phrase("Email") ?></strong> <?php echo get_phrase("(verification required)") ?></li>
            <li><i class="fa-solid fa-phone"></i> <strong><?php echo get_phrase("Phone number") ?></strong> <?php echo get_phrase("(support and notifications)") ?></li>
            <li><i class="fa-solid fa-key"></i> <strong><?php echo get_phrase("Password") ?></strong> <?php echo get_phrase("(12+ characters, letters/numbers/symbols)") ?></li>
          </ul>
        </article>

        <!-- 2. Communauté -->
        <article class="obi-card" data-animate data-step="2">
          <h3><span class="obi-num-inline">2</span> <?php echo get_phrase("Create a community") ?></h3>
          <p><?php echo get_phrase("Clearly present your value proposition and set the price.") ?></p>
          <ul class="obi-bullets">
            <li><i class="fa-solid fa-video"></i> <strong><?php echo get_phrase("Community video") ?></strong> <?php echo get_phrase("(YouTube/Vimeo or upload)") ?></li>
            <li><i class="fa-regular fa-file-lines"></i> <strong><?php echo get_phrase("Community description") ?></strong> <?php echo get_phrase("(from 300 to 600 characters)") ?></li>
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
          <h3><span class="obi-num-inline">3</span> <?php echo get_phrase("Create classes") ?></h3>
          <p><?php echo get_phrase("Add your educational content and organize your catalog.") ?></p>
          <ul class="obi-bullets">
            <li><i class="fa-solid fa-clapperboard"></i> <strong><?php echo get_phrase("Type") ?></strong> : <?php echo get_phrase("live, replay, files and quiz") ?></li>
            <li><i class="fa-regular fa-clock"></i> <strong><?php echo get_phrase("Recommended duration") ?></strong> : <?php echo get_phrase("from 7 to 15 minutes per video") ?></li>
            <li><i class="fa-solid fa-list-check"></i> <strong><?php echo get_phrase("Structure") ?></strong> : <?php echo get_phrase("Intro → Goals → Demo → Exercise → Recap") ?></li>
            <li><i class="fa-solid fa-ticket"></i> <strong><?php echo get_phrase("Class price") ?></strong> <?php echo get_phrase("(per unit) or included in the subscription") ?></li>
          </ul>
        </article>

        <!-- 4. Lancement -->
        <article class="obi-card" data-animate data-step="4">
          <h3><span class="obi-num-inline">4</span> <?php echo get_phrase("Launch and monetize") ?></h3>
          <p><?php echo get_phrase("Publish your community, promote it, and track your metrics.") ?></p>
          <ul class="obi-bullets">
            <li><i class="fa-regular fa-paper-plane"></i> <strong><?php echo get_phrase("Publishing & sharing") ?></strong> : <?php echo get_phrase("social networks, email, QR") ?></li>
            <li><i class="fa-solid fa-chart-line"></i> <strong><?php echo get_phrase("Tracking") ?></strong> : <?php echo get_phrase("views, completions, revenue (dashboard)") ?></li>
          </ul>
          <div class="obi-cta">
            <?php if (session()->get('user_id')) : ?>
                <!-- utilisateur connecté -->
                 <a href="<?php echo route('dashboard'); ?>" class="obi-btn"><?php echo get_phrase("Start now") ?></a>
            <?php else : ?>
                <!-- utilisateur non connecté -->
                  <a href="<?php echo site_url('admission/online_admission'); ?>" class="obi-btn"><?php echo get_phrase("Start now") ?></a>
            <?php endif; ?>
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
            <p
              id="obiVideoFallback"
              class="mt-2"
              style="display:none;color:#6b7280;font-size:0.92rem;"
              aria-live="polite">
              <?php echo get_phrase("If the video does not load here, it may be blocked by your browser or security settings.") ?>
              <a id="obiVideoFallbackLink" href="https://www.youtube.com/watch?v=2e6g2Lsl7aw" target="_blank" rel="noopener noreferrer">
                <?php echo get_phrase("Open the video in YouTube") ?>
              </a>
            </p>
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
  const fallback = document.getElementById('obiVideoFallback');
  const fallbackLink = document.getElementById('obiVideoFallbackLink');
  const close = modal.querySelector('.obi-modal__close');
  const backdrop = modal.querySelector('.obi-modal__backdrop');
  let fallbackTimer = null;

  function toWatchUrl(url){
    if(!url) return 'https://www.youtube.com/watch?v=2e6g2Lsl7aw';
    const match = url.match(/youtube\.com\/embed\/([^?&/]+)/i);
    if(match && match[1]){
      return 'https://www.youtube.com/watch?v=' + match[1];
    }
    return url;
  }

  function clearFallbackTimer(){
    if(fallbackTimer){
      window.clearTimeout(fallbackTimer);
      fallbackTimer = null;
    }
  }

  document.addEventListener('click', (e)=>{
    const trigger = e.target.closest('[data-video]');
    if(trigger){
      const src = trigger.getAttribute('data-video');
      if(fallback){
        fallback.style.display = 'none';
      }
      if(fallbackLink){
        fallbackLink.href = toWatchUrl(src);
      }
      clearFallbackTimer();
      frame.src = src + (src.includes('?') ? '&' : '?') + 'autoplay=1';
      modal.classList.add('is-open');
      fallbackTimer = window.setTimeout(()=>{
        if(modal.classList.contains('is-open') && fallback){
          fallback.style.display = 'block';
        }
      }, 2500);
    }
  });

  frame.addEventListener('load', ()=>{
    clearFallbackTimer();
  });

  document.addEventListener('keydown', (e)=>{
    const trigger = e.target.closest ? e.target.closest('.video-preview') : null;
    if (!trigger) return;
    if (e.key === 'Enter' || e.key === ' ') {
      e.preventDefault();
      trigger.click();
    }
  });

  function hide(){
    clearFallbackTimer();
    modal.classList.remove('is-open');
    if(fallback){
      fallback.style.display = 'none';
    }
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

