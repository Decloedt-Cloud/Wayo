<main id="main" tabindex="-1" <?php echo (get_user_language() === 'arabic') ? 'dir="rtl"' : 'dir="ltr"'; ?>>
    <!-- HERO -->
    <section class="hero mt-5">
      <div class="container hero-wrap reveal">
        <div class="hero-inner">
          <div class="hero-copy">
            <div class="badge"><?php echo get_phrase("FAQ & User Guide") ?></div>
            <h2><?php echo get_phrase("Monetize your expertise.") ?><br/><?php echo get_phrase("Learn local, progress fast.") ?></h2>
            <p><?php echo get_phrase("Wayo Academy connects local Experts and learners with practical learning paths, adapted to the Moroccan market.") ?></p>
            <div class="hero-points" style="margin-top:16px">
              <span class="chip"><?php echo get_phrase("On-community value courses") ?></span>
              <span class="chip"><?php echo get_phrase("Live sessions & Coaching") ?></span>
              <span class="chip"><?php echo get_phrase("Secure payments") ?></span>
              <span class="chip"><?php echo get_phrase("Certificates (depending on course)") ?></span>
            </div>
          </div>
          <aside class="hero-card" aria-label="Vue rapide — How it works">
            <div class="pill"><?php echo get_phrase("How it works — overview") ?></div>
            <div class="wayo-step"><div class="num">1</div><div><strong><?php echo get_phrase("Create") ?></strong> <?php echo get_phrase("a community or join acommunity account.") ?></div></div>
            <div class="wayo-step"><div class="num">2</div><div><strong><?php echo get_phrase("Publish/Choose a community") ?></strong><?php echo get_phrase(" and set objectives & curriculum.") ?></div></div>
            <div class="wayo-step"><div class="num">3</div><div><strong><?php echo get_phrase("Secure payments") ?></strong> <?php echo get_phrase("& instant access.") ?></div></div>
            <div class="wayo-step"><div class="num">4</div><div><strong><?php echo get_phrase("Follow the path") ?></strong> : <?php echo get_phrase("videos, lives, Q&A, resources.") ?></div></div>
          </aside>
        </div>
      </div>
    </section>

    <!-- KPIs -->
    <section class="d-flex justify-content-center bg-section-primary" aria-label="Indicateurs clés">
    <div class="container kpis reveal">
        <div class="kpi"><h3>+250</h3><p><?php echo get_phrase("Lessons published") ?></p></div>
        <div class="kpi"><h3>4.9/5</h3><p><?php echo get_phrase("Average satisfaction") ?></p></div>
        <div class="kpi"><h3>+120</h3><p><?php echo get_phrase("Active mentors") ?></p></div>
        <div class="kpi"><h3>100%</h3><p><?php echo get_phrase("Secure transactions") ?></p></div>
    </div>
    </section>

    <!-- POUR QUI -->
    <section id="pour-qui" class="bg-section-secondary" aria-label="Public visé">
      <div class="container reveal">
        <div class="sec-title" <?php echo (get_user_language() === 'arabic') ? 'dir="rtl"' : 'dir="ltr"'; ?>>
          <h3><?php echo get_phrase("Who is Wayo for?") ?></h3>
        </div>
        <div class="grid-2">
          <div class="card">
            <h4><?php echo get_phrase("Mentors & Experts") ?></h4>
            <p><?php echo get_phrase("Monetize your knowledge through on-demand courses, live sessions, and 1:1 coaching. Manage content, pricing, schedule, and payments from a simple dashboard.") ?></p>
            <ul>
              <li><?php echo get_phrase("Full control over offers & pricing") ?></li>
              <li><?php echo get_phrase("Analytics & scheduled payouts") ?></li>
            </ul>
          </div>
          <div class="card">
            <h4><?php echo get_phrase("Students & Learners") ?></h4>
            <p><?php echo get_phrase("Gain practical skills relevant to the local market: tech, business, marketing, design, soft skills. Access videos, lives, and resources instantly.") ?></p>
            <ul>
              <li><?php echo get_phrase("Filterable catalog (topic, level, language)") ?></li>
              <li><?php echo get_phrase("Secure payments & invoices") ?></li>
              <li><?php echo get_phrase("chat with other members") ?></li>
            </ul>
          </div>
        </div>
      </div>
    </section>

    <!-- GUIDE -->
    <section class="bg-section-primary" id="guide" aria-label="Guide d'utilisation">
      <div class="container reveal">
        <div class="sec-title">
          <h3><?php echo get_phrase("How to use Wayo") ?></h3>
        </div>
        <div class="grid-2">
          <div class="card">
            <h4 style="color:var(--wayo-orange-700)"><?php echo get_phrase("Mentor Path (6 steps)") ?></h4>
            <div class="wayo-step"><div class="num">1</div><div><strong><?php echo get_phrase("Create your community") ?></strong> : <?php echo get_phrase("bio, expertise, photo, social links.") ?></div></div>
            <div class="wayo-step"><div class="num">2</div><div><strong><?php echo get_phrase("Define your offer") ?></strong> : <?php echo get_phrase("con-demand / live / coaching (+ objectives & program).") ?></div></div>
            <div class="wayo-step"><div class="num">3</div><div><strong><?php echo get_phrase("Set pricing & schedule") ?></strong> : <?php echo get_phrase("rates, seats, availability.") ?></div></div>
            <div class="wayo-step"><div class="num">4</div><div><strong><?php echo get_phrase("Publish & share") ?></strong> : <?php echo get_phrase("put on sale + share the link.") ?></div></div>
            <div class="wayo-step"><div class="num">5</div><div><strong><?php echo get_phrase("Deliver & engage") ?></strong> : <?php echo get_phrase("live sessions, Q&A, feedback.") ?></div></div>
            <div class="wayo-step"><div class="num">6</div><div><strong><?php echo get_phrase("Track & earn") ?></strong> : <?php echo get_phrase("stats, learner satisfaction, payments & transfers.") ?></div></div>
          </div>
          <div class="card">
            <h4 style="color:var(--wayo-orange-700)"><?php echo get_phrase("Learner Path (6 steps)") ?></h4>
            <div class="wayo-step"><div class="num">1</div><div><strong><?php echo get_phrase("Create an account") ?></strong> : <?php echo get_phrase("email + password.") ?></div></div>
            <div class="wayo-step"><div class="num">2</div><div><strong><?php echo get_phrase("Explore communities") ?></strong> : <?php echo get_phrase("topic, level, language, price, reviews.") ?></div></div>
            <div class="wayo-step"><div class="num">3</div><div><strong><?php echo get_phrase("Select a community") ?></strong> :  <?php echo get_phrase("objectives, curriculum, prerequisites") ?></div></div>
            <div class="wayo-step"><div class="num">4</div><div><strong><?php echo get_phrase("Pay online or cash") ?></strong> : <?php echo get_phrase("secure checkout, instant confirmation.") ?></div></div>
            <div class="wayo-step"><div class="num">5</div><div><strong><?php echo get_phrase("Follow the training") ?></strong> : <?php echo get_phrase("videos, lives, resources, messaging.") ?></div></div>
            <div class="wayo-step"><div class="num">6</div><div><strong><?php echo get_phrase("Complete & certify") ?></strong> : <?php echo get_phrase("quizzes/evaluations (if any), certificate (if offered).") ?></div></div>
          </div>
        </div>
      </div>
    </section>

    <!-- AVANTAGES -->
    <section class="bg-section-secondary" id="avantages" aria-label="Avantages Wayo">
      <div class="container reveal">
        <div class="sec-title">
          <h3><?php echo get_phrase("Why choose Wayo?") ?></h3>
        </div>
        <div class="grid-3">
            <div class="card feat">
                <div class="icon" aria-hidden="true">
                    <i class="fas fa-map-marker-alt"></i>
                </div>
                <div>
                    <h4><?php echo get_phrase("Local focus") ?></h4>
                    <p><?php echo get_phrase("Content tailored to the Moroccan market: real cases, language, context, opportunities.") ?></p>
                </div>
            </div>
            <div class="card feat">
                <div class="icon" aria-hidden="true">
                    <i class="fas fa-coins"></i>
                </div>
                <div>
                    <h4><?php echo get_phrase("Easy monetization") ?></h4>
                    <p><?php echo get_phrase("Create, publish, get paid. Transparent commission shown before publishing.") ?></p>
                </div>
            </div>
            <div class="card feat">
                <div class="icon" aria-hidden="true">
                    <i class="fas fa-wave-square"></i>
                </div>
                <div>
                    <h4><?php echo get_phrase("Smooth experience") ?></h4>
                    <p><?php echo get_phrase("Secure videos, built-in messaging, lives & coaching without friction.") ?></p>
                </div>
            </div>
            <div class="card feat">
                <div class="icon" aria-hidden="true">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div>
                    <h4><?php echo get_phrase("Progress & certificates") ?></h4>
                    <p><?php echo get_phrase("Learner stats, evaluations Quizes.") ?></p>
                </div>
            </div>
            <div class="card feat">
                <div class="icon" aria-hidden="true">
                    <i class="fas fa-lock"></i>
                </div>
                <div>
                    <h4><?php echo get_phrase("Secure payments instant or casl") ?></h4>
                    <p><?php echo get_phrase("Certified providers, encrypted connections, downloadable invoices.") ?></p>
                </div>
            </div>
            <div class="card feat">
                <div class="icon" aria-hidden="true">
                    <i class="fas fa-headset"></i>
                </div>
                <div>
                    <h4><?php echo get_phrase("Responsive support") ?></h4>
                    <p><?php echo get_phrase("Local team, step-by-step help, quick resolution of issues.") ?></p>
                </div>
            </div>
      </div>
    </section>

    <!-- CATEGORIES -->
    <section class="bg-section-primary" aria-label="Catégories">
      <div class="container reveal">
        <div class="sec-title">
          <h3><?php echo get_phrase("Training categories") ?></h3>
        </div>
        <div class="grid-4">
          <div class="card"><h4><?php echo get_phrase("Business & Entrepreneurship") ?></h4><p><?php echo get_phrase("Strategy, operations, sales, finance.") ?></p></div>
          <div class="card"><h4><?php echo get_phrase("Marketing & Growtht") ?></h4><p><?php echo get_phrase("SEO, Ads, email, funnels, social.") ?></p></div>
          <div class="card"><h4><?php echo get_phrase("Tech & Data") ?></h4><p><?php echo get_phrase("Web development, no-code, AI, analytics.") ?></p></div>
          <div class="card"><h4><?php echo get_phrase("Creation & Design") ?></h4><p><?php echo get_phrase("Branding, UI/UX, video, content.") ?></p></div>
        </div>
      </div>
    </section>

    <!-- MENTORS -->
    <!-- <section class="bg-section-secondary" id="mentors" aria-label="Mentors">
      <div class="container reveal">
        <div class="sec-title">
          <h3><?php echo get_phrase("Featured Mentors") ?></h3>
          <span class="pill"><?php echo get_phrase("Ambassadors") ?></span>
        </div>
        <div class="grid-3">
          <div class="card" style="display:flex;gap:12px;align-items:center">
            <div aria-hidden="true" style="width:56px;height:56px;border-radius:50%;border:1px solid var(--ink-200);background:linear-gradient(135deg,#eee,#fff)"></div>
            <div><strong><?php echo get_phrase("Salma B.") ?></strong><br/><span class="muted"> <?php echo get_phrase("Growth & Ads") ?></span></div>
          </div>
          <div class="card" style="display:flex;gap:12px;align-items:center">
            <div aria-hidden="true" style="width:56px;height:56px;border-radius:50%;border:1px solid var(--ink-200);background:linear-gradient(135deg,#eee,#fff)"></div>
            <div><strong><?php echo get_phrase("Yassine K.") ?></strong><br/><span class="muted"><?php echo get_phrase("Full-stack Dev") ?></span></div>
          </div>
          <div class="card" style="display:flex;gap:12px;align-items:center">
            <div aria-hidden="true" style="width:56px;height:56px;border-radius:50%;border:1px solid var(--ink-200);background:linear-gradient(135deg,#eee,#fff)"></div>
            <div><strong><?php echo get_phrase("Hajar M.") ?></strong><br/><span class="muted"><?php echo get_phrase("Brand & Design") ?></span></div>
          </div>
        </div>
      </div>
    </section> -->

    <!-- TARIFS -->
    <section class="bg-section-primary" id="tarifs" aria-label="Tarifs">
      <div class="container reveal">
        <div class="sec-title">
          <h3><?php echo get_phrase("Simple Pricing") ?></h3>
        </div>
        <div class="pricing">
          <div class="price">
            <h4><?php echo get_phrase("Learner — Access by Mentor") ?></h4>
            <div class="plan-price-split">
                <p class="big"><?php echo get_phrase("Free or Paid (defined by the mentor)") ?></p>
                <p><?php echo get_phrase("Access communities, courses, and sessions based on the plan set by each mentor.Some content may be free, others paid.") ?></p>
            </div>
            <a class="btn-login" href="<?php echo site_url('home/communities'); ?>"><?php echo get_phrase("Explore communities") ?></a>
          </div>
          <div class="price" style="border-color:rgba(255,122,46,.45)">
            <span class="popular-badge"><?php echo get_phrase("popular") ?></span>
            <h4><?php echo get_phrase("Mentor — Publish & Monetize") ?></h4>
            <div class="plan-price-split">
            <p class="big">
               <span class="price-value"></span>
               <span class="price-currency"></span>
            </p>
            <p><?php echo get_phrase("Create and publish courses, live sessions, or coaching programs. Set free or paid plans. Wayo manages payments and delivery.") ?></p>
            </div>
            <a class="btn-login" href="<?php echo site_url('admission/online_admission'); ?>"><?php echo get_phrase("Become a Mentor") ?></a>
          </div>
          <div class="price">
            <h4><?php echo get_phrase("Platform Tools & Control") ?></h4>
            <div class="plan-price-split">
                <p class="big"><?php echo get_phrase("All included") ?></p>
                <p><?php echo get_phrase("Private content, gated access, payment management, analytics, certificates, and community features.") ?></p>
            </div>
            <a class="btn-login" href="<?php echo site_url('home/tutorial'); ?>"><?php echo get_phrase("how it works") ?></a>
          </div>
        </div>
      </div>
    </section>

    <!-- FAQ -->
    <section class="bg-section-secondary" id="faq" aria-label="FAQ">
      <div class="container reveal">
        <div class="sec-title">
          <h3><?php echo get_phrase("FAQ — Frequently Asked Questions") ?></h3>
        </div>
        <div class="grid-2">
          <div>
            <details class="faq"><summary><?php echo get_phrase("Available languages ?") ?> <span>+</span></summary>
              <div><?php echo get_phrase("French and/or Arabic depending on the mentor (indicated on each course).") ?></div>
            </details>
            <details class="faq"><summary><?php echo get_phrase("Refund policy ?") ?> <span>+</span></summary>
              <div><?php echo get_phrase("Depends on the format (on-demand, live, coaching) and is indicated before payment") ?></div>
            </details>
             <details class="faq"><summary><?php echo get_phrase("Mentor commissions & payments ?") ?> <span>+</span></summary>
              <div><?php echo get_phrase("Transparent commission at publication. Scheduled payouts.") ?></div>
            </details>
          </div>
          <div>
           
            <details class="faq"><summary><?php echo get_phrase("Certificate of completion ?") ?> <span>+</span></summary>
              <div><?php echo get_phrase("Available for certain courses (noted on the course page).") ?></div>
            </details>
            <details class="faq"><summary><?php echo get_phrase("Is my data protected ?") ?> <span>+</span></summary>
              <div><?php echo get_phrase("Security standards & certified providers. Encrypted connections.") ?></div>
            </details>
            <details class="faq"><summary><?php echo get_phrase("Supported browsers ?") ?> <span>+</span></summary>
              <div><?php echo get_phrase("Chrome, Edge, Firefox, Safari (latest versions).") ?></div>
            </details>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA Mentor + Support -->
    <section class="bg-section-primary" id="cta-mentor" aria-label="Action mentor & support">
      <div class="container reveal">
        <div class="grid-2">
          <div class="card">
            <h4><?php echo get_phrase("Ready to share your knowledge ?") ?></h4>
            <p><?php echo get_phrase("Join us and creat your community today.") ?></p>
            <a class="btn-login-faq" href="#"><?php echo get_phrase("Create my Mentor account") ?></a>
          </div>
          <div id="support" class="card">
            <h4><?php echo get_phrase("Support") ?></h4>
            <p><?php echo get_phrase("Need help? Write to us and attach a screenshot if necessary.") ?></p>
            <p><strong><?php echo get_phrase("Email :") ?></strong> <a href="mailto:support@wayo.academy">support@wayo.academy</a></p>
            <p class="muted" id="lastUpdate"><?php echo get_phrase("Last update : —") ?></p>
          </div>
        </div>
      </div>
    </section>
  </main>
  <script>
    // Dates
    (function(){
      var d=new Date();
      var y=document.getElementById('y'); if(y) y.textContent=d.getFullYear();
      var fmt=d.toLocaleDateString('fr-FR',{year:'numeric',month:'long',day:'numeric'});
      var lu=document.getElementById('lastUpdate'); if(lu) lu.textContent='Dernière mise à jour : '+fmt;
    })();

    // Reveal
    (function(){
      var els=[].slice.call(document.querySelectorAll('.reveal'));
      var reduce=window.matchMedia&&window.matchMedia('(prefers-reduced-motion: reduce)').matches;
      if(reduce){ els.forEach(function(el){el.classList.add('show');}); return; }
      if('IntersectionObserver' in window){
        var io=new IntersectionObserver(function(entries){
          entries.forEach(function(e){ if(e.isIntersecting){ e.target.classList.add('show'); io.unobserve(e.target); } });
        },{threshold:0.14});
        els.forEach(function(el){io.observe(el);});
      } else {
        function onScroll(){ els.forEach(function(el){ if(el.getBoundingClientRect().top < window.innerHeight*0.85) el.classList.add('show'); }); }
        window.addEventListener('scroll', onScroll); onScroll();
      }
    })();

    // Burger + Lang menu
    (function(){
      var btn = document.querySelector('.hamburger');
      var menu = document.getElementById('primary-menu');
      if(btn && menu){ btn.addEventListener('click', function(){ var open = menu.classList.toggle('open'); btn.setAttribute('aria-expanded', open ? 'true':'false'); }); }
      var wrap = document.querySelector('.lang2');
      if(wrap){ 
        var trigger = wrap.querySelector('.lang2-trigger');
        trigger.addEventListener('click', function(e){ e.stopPropagation(); var open = wrap.classList.toggle('open'); trigger.setAttribute('aria-expanded', open ? 'true':'false'); });
        document.addEventListener('click', function(){ wrap.classList.remove('open'); });
      }
    })();
  </script>

  <script>
  async function updatePrices() {
    try {
      // Détection du pays de l’utilisateur
      const response = await fetch('https://api.country.is/');
      const data = await response.json();
      const country = data.country;

      // Définition des prix fixes
      const prices = {
        'MA': {
          value: 790,
          currency: 'DH'
        },
        'AE': {
          value: 299, 
          currency: 'AED'
        }
      };

      // Fallback par défaut (Maroc)
      const priceData = prices[country] || prices['MA'];

      // Mise à jour des éléments prix
      const priceElements = document.querySelectorAll('.plan-price-split');

      priceElements.forEach(element => {
        const priceValue = element.querySelector('.price-value');
        const priceCurrency = element.querySelector('.price-currency');

        if (priceValue) priceValue.textContent = priceData.value;
        if (priceCurrency) priceCurrency.textContent = priceData.currency;
      });

    } catch (error) {
      console.error('Error updating prices:', error);
    }
  }

  // Initialisation au chargement de la page
  document.addEventListener('DOMContentLoaded', updatePrices);
</script>