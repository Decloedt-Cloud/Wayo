

  <style>
    :root{
      --accent:#FC7B30; /* Orange Wayo */
      --accent-600:#e56f26;
      --text:#1c1c1f;
      --muted:#6b6b75;
      --card:#ffffff;
      --card-alt:#f6f7fb;
      --radius:14px;
      --shadow:0 10px 30px rgba(0,0,0,.08);
      --dark:#333333;
      font-family: 'Shayan', 'Cairo', 'Tajawal', 'Arial', sans-serif;
    }

    html{ 
        scroll-behavior:smooth; 
    }
    body{
          font-family: 'Shayan', 'Cairo', 'Tajawal', 'Arial', sans-serif;
        }
    img{
         max-width:100%; height:auto; display:block; 
        }

    /* Hero */
    .hero{ position:relative; min-height:68vh; display:grid; place-items:center; color:#fff; background-image:url('../uploads/images/decloedt/img/bg-about-us.png'); background-size:cover; background-position:center; }
    .hero::before{ content:""; position:absolute; inset:0; background:linear-gradient(180deg, rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.60));}
    .hero .hero-content{ position:relative; text-align:center; }
    .hero .lead{ max-width:760px; margin-inline:auto; color:#e9e9ef }

    .btn-pill{ border-radius:999px; font-weight:700; padding:.8rem 1.2rem; }
    .btn-accent{ background:var(--accent); color:#fff; box-shadow:0 8px 20px rgba(252,123,48,.25) }
    .btn-accent:hover{ background:var(--accent-600); color:#fff; }
    .btn-ghost{ background:#ffffff14; color:#fff; border:1px solid #ffffff40 }
    .btn-ghost:hover{ background:#ffffff26; color:#fff; }

    /* Sections */
    .section-subtitle{ color:var(--muted); }
    .bg-alt{ background:var(--card-alt); }

    /* Values */
    .value-card{ background:var(--card); border-radius:var(--radius); padding:20px; box-shadow:var(--shadow); transition:transform .2s, box-shadow .2s; height:100%; }
    .value-card:hover{ transform:translateY(-4px); box-shadow:0 14px 36px rgba(0,0,0,.12) }


    /* ====== TEAM CAROUSEL (2 cartes, non-centre en gris) ====== */
      #equipe { position: relative; overflow: visible; }
      #equipe .about-title{
        font-family: 'Poppins', 'Inter', system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
        font-size: clamp(2rem, 7vw, 4rem);
        font-weight: 800;
        letter-spacing: -0.02em;
        margin: 0 0 12px;
        text-align: center;
        background: linear-gradient(180deg, color-mix(in srgb, var(--accent) 30%, #0000) 0%, #0000 90%);
        -webkit-background-clip: text; background-clip: text; color: transparent;
      }
      #equipe .carousel-container{
        width: 100%; max-width: 1120px; height: 440px;
        position: relative; perspective: 1000px; margin: 28px auto 0;
      }
      #equipe .carousel-track{
        width: 100%; height: 100%; display: flex; justify-content: center; align-items: center;
        position: relative; transform-style: preserve-3d; transition: transform .8s cubic-bezier(.25,.46,.45,.94);
      }
      #equipe .card{
        position: absolute; width: 280px; height: 380px; background: var(--card);
        border-radius: var(--radius); overflow: hidden; box-shadow: var(--shadow);
        border: 1px solid rgba(0,0,0,.06); transition: all .8s cubic-bezier(.25,.46,.45,.94);
        cursor: pointer; will-change: transform, opacity, box-shadow;
      }
      #equipe .card img{ width:100%; height:100%; object-fit:cover; transition:inherit; }

      /* Icônes sociales */
      #equipe .card .card-actions{
        position:absolute; right:10px; bottom:10px; display:flex; gap:8px;
        background:rgba(0,0,0,.28); padding:6px; border-radius:999px; backdrop-filter: blur(4px);
      }
      #equipe .card .card-actions .action{
        width:28px; height:28px; border-radius:999px; display:grid; place-items:center;
        background:#fff; color:#0a66c2; text-decoration:none; transition:transform .2s, box-shadow .2s, background .2s;
        box-shadow: 0 2px 6px rgba(0,0,0,.18);
      }
      #equipe .card .card-actions .action:hover{ transform: translateY(-2px); }
      #equipe .card .card-actions .action + .action{ color:#111; }

      /* Positions 3D */
      #equipe .card.center{ z-index:10; transform: scale(1.06) translateZ(0);
        box-shadow: 0 14px 32px rgba(0,0,0,.12), 0 0 0 3px color-mix(in srgb, var(--accent) 18%, transparent); }
      #equipe .card.left-1{ z-index:5; transform: translateX(-200px) scale(.92) translateZ(-120px); opacity:.95; }
      #equipe .card.right-1{ z-index:5; transform: translateX(200px) scale(.92) translateZ(-120px); opacity:.95; }
      #equipe .card.left-2{ z-index:1; transform: translateX(-400px) scale(.82) translateZ(-300px); opacity:.7; }
      #equipe .card.right-2{ z-index:1; transform: translateX(400px) scale(.82) translateZ(-300px); opacity:.7; }

      /* Gris pour cartes non centrées */
      #equipe .card:not(.center) img{ filter: grayscale(100%); }

      /* Légende sous le carrousel */
      #equipe .member-info{ text-align:center; margin-top: 24px; transition: all .4s ease-out; }
      #equipe .member-name{
        font-family:'Poppins','Inter',system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;
        color: var(--text); font-size: clamp(1.35rem, 3vw, 2.1rem); font-weight: 700; margin: 0 0 6px;
        position: relative; display: inline-block;
      }
      #equipe .member-name::before, #equipe .member-name::after{
        content:""; position:absolute; top:100%; width:84px; height:2px; background: var(--accent); opacity:.8;
      }
      #equipe .member-name::before{ left:-100px; }
      #equipe .member-name::after{ right:-100px; }
      #equipe .member-role{
        font-family:'Inter',system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;
        color:#848696; font-size:clamp(.95rem, 2.2vw, 1.1rem); font-weight:600;
        letter-spacing:.08em; text-transform:uppercase; padding-top:6px; margin:0;
      }

      /* Dots + Flèches */
      #equipe .dots{ display:flex; justify-content:center; gap:10px; margin-top: 28px; }
      #equipe .dot{
        width:12px; height:12px; border-radius:50%;
        background: color-mix(in srgb, var(--accent) 25%, #0000);
        cursor:pointer; transition: transform .2s, background .2s, box-shadow .2s; border:0;
        box-shadow: 0 2px 6px rgba(0,0,0,.06);
      }
      #equipe .dot:hover{ transform: scale(1.12); }
      #equipe .dot.active{ background: var(--accent); transform: scale(1.18);
        box-shadow: 0 0 0 4px color-mix(in srgb, var(--accent) 20%, #0000); }
      #equipe .nav-arrow{
        position:absolute; top:50%; transform: translateY(-50%);
        background: var(--accent); color:#fff; width:42px; height:42px; border-radius: 999px;
        display:flex; align-items:center; justify-content:center; cursor:pointer; z-index:20;
        transition: transform .2s, background .2s, box-shadow .2s; font-size:1.35rem; border:none; outline:none;
      }
      #equipe .nav-arrow:hover{ background: var(--accent-600); transform: translateY(-50%) scale(1.06); box-shadow: 0 8px 18px rgba(0,0,0,.12); }
      #equipe .nav-arrow:focus-visible{ box-shadow: 0 0 0 4px color-mix(in srgb, var(--accent) 30%, #0000); }
      #equipe .nav-arrow.left{ left:6px; }
      #equipe .nav-arrow.right{ right:6px; }

         /* ======END TEAM CAROUSEL (2 cartes, non-centre en gris) ====== */

   
    @media (min-width: 992px){
     
    }
    @media (max-width: 991.98px){
     
    }

    /* Team Carousel */
    #teamCarousel .carousel-item{ height:420px; }
    #teamCarousel .card{ width:280px; height:380px; border-radius:var(--radius); overflow:hidden; box-shadow:var(--shadow); border:1px solid rgba(0,0,0,.06); margin-inline:auto; }
    #teamCarousel .card img{ width:100%; height:100%; object-fit:cover; }
    #teamCarousel .card-actions{ position:absolute; right:10px; bottom:10px; display:flex; gap:8px; background:rgba(0,0,0,.28); padding:6px; border-radius:999px; backdrop-filter: blur(4px); }
    #teamCarousel .action{ width:28px; height:28px; border-radius:999px; display:grid; place-items:center; background:#fff; color:#0a66c2; text-decoration:none; box-shadow:0 2px 6px rgba(0,0,0,.18); }
    #teamCarousel .action + .action{ color:#111; }
    .member-legend h3{ font-family:'Poppins',sans-serif; font-weight:700; }
    .member-legend p{ color:#848696; letter-spacing:.08em; text-transform:uppercase; font-weight:600; }

    /* Where */
    .map-embed{ aspect-ratio:16/9; overflow:hidden; border-radius:calc(var(--radius) - 4px); box-shadow:0 8px 24px rgba(0,0,0,.08); }
    .map-embed iframe{ width:100%; height:100%; border:0; }
    .contact-card{ background:var(--card); border-radius:var(--radius); box-shadow:var(--shadow); }

    /* Reveal on scroll */
    [data-animate]{ opacity:0; transform:translateY(16px); transition:opacity .5s ease, transform .5s ease; }
    [data-animate].is-visible{ opacity:1; transform:none; }

    /* Back to top */
    #scrollTopBtn{ position:fixed; right:16px; bottom:16px; width:44px; height:44px; border:none; border-radius:999px; background:var(--accent); color:#fff; display:grid; place-items:center; box-shadow:0 8px 18px rgba(0,0,0,.16); opacity:0; visibility:hidden; transition:opacity .2s, visibility .2s, transform .2s; }
    #scrollTopBtn.show{ opacity:1; visibility:visible; transform:translateY(0); }
  </style>



  <main class="mt-5">
    <!-- HERO -->
    <section class="hero">
      <div class="container hero-content py-5" data-animate>
        <h1 class="display-5 fw-bold mb-2"><?php echo get_phrase("Wayo Academy, much more than a learning platform.") ?></h1>
       
        <p class="lead mb-4 text-white fs-md-4 fs-lg-3" style="letter-spacing: 1px; font-size: 1.5rem; margin-bottom: 1rem;"><?php echo get_phrase("A community of experts and learners united to turn skills into opportunities.") ?></p>
    <div class="row g-2 justify-content-center">
      <div class="col-12 col-md-auto">
        <a class="btn btn-accent btn-pill w-100" href="#mission">
          <?php echo get_phrase("Join the community ") ?>
        </a>
      </div>
      <div class="col-12 col-md-auto">
        <a class="btn btn-ghost btn-pill w-100" href="#histoire" aria-label="Aller à notre histoire">
          <?php echo get_phrase("Découvrir l’histoire ") ?>
        </a>
      </div>
    </div>
      </div>
    </section>

    <!-- MISSION & VALEURS -->
    <section id="mission" class="py-5">
      <div class="container">
        <header class="text-center mission mb-4" data-animate>
          <h2 class="h2 mb-2"><?php echo get_phrase("Our Mission") ?></h2>
          <p class="section-subtitle m-0"><?php echo get_phrase("Helping those with skills to") ?> 
          <strong><?php echo get_phrase("monetize their expertise") ?></strong>
          <?php echo get_phrase("through e-learning and to") ?>  
          <strong><?php echo get_phrase("create communities") ?></strong>
           <?php echo get_phrase("active and supportive learning communities") ?></p>
        </header>

        <div class="row g-3" role="list">
          <div class="col-12 col-sm-6 col-lg-3" role="listitem" data-animate>
            <article class="value-card h-100">
              <div class="fs-3 mb-1">🌍</div>
              <h3 class="h6 m-0"><?php echo get_phrase("Accessibility") ?></h3>
              <p class="mb-0 text-secondary"><?php echo get_phrase("Inclusive learning paths, available anywhere, at your own pace.") ?></p>
            </article>
          </div>
          <div class="col-12 col-sm-6 col-lg-3" role="listitem" data-animate>
            <article class="value-card h-100">
              <div class="fs-3 mb-1">🤝</div>
              <h3 class="h6 m-0"><?php echo get_phrase("Mutual support") ?></h3>
              <p class="mb-0 text-secondary"><?php echo get_phrase("Learn together, progress faster, succeed sustainably.") ?></p>
            </article>
          </div>
          <div class="col-12 col-sm-6 col-lg-3" role="listitem" data-animate>
            <article class="value-card h-100">
              <div class="fs-3 mb-1">💡</div>
              <h3 class="h6 m-0"><?php echo get_phrase("Impact") ?></h3>
              <p class="mb-0 text-secondary"><?php echo get_phrase("Practical, monetizable, and results-oriented skills.") ?></p>
            </article>
          </div>
          <div class="col-12 col-sm-6 col-lg-3" role="listitem" data-animate>
            <article class="value-card h-100">
              <div class="fs-3 mb-1">🕊️</div>
              <h3 class="h6 m-0"><?php echo get_phrase("Freedom") ?></p></h3>
              <p class="mb-0 text-secondary"><?php echo get_phrase("Create your path: courses, community, income… your way.") ?></p>
            </article>
          </div>
        </div>
      </div>
    </section>

    <!-- HISTOIRE -->
     <section class="section-timeline py-5">
        <div class="container  ">
          <div class="text-center histoire mb-5">
            <!-- <h2 class="fw-bold">L’histoire de Wayo</h2> -->
            <h2 class="social-media-main-text">
               <?php echo get_phrase("The story of") ?>
                <span style="color: #FC7B30;"><?php echo get_phrase("Wayo academy") ?></span>
            </h2>
            <p class="text-muted">
             <?php echo get_phrase("Why Wayo? Because there was a missing bridge between") ?> <em> <?php echo get_phrase("talents") ?></em> <?php echo get_phrase("and") ?>  <em> <?php echo get_phrase("opportunities") ?></em>.
            </p>
          </div>

          <div class="timeline">

            <!-- Le déclic (gauche) -->
            <div class="timeline-item row">
              <div class="col-md-6 timeline-left d-flex justify-content-end">
                <div class="card-timeline">
                  <h5> <?php echo get_phrase("The spark") ?></h5>
                  <p><?php echo get_phrase("Observation: many experts struggle to monetize their knowledge.") ?><br>
                    <?php echo get_phrase("We thought :") ?><span class="highlight"><?php echo get_phrase("Let’s go.") ?></span>
                  </p>
                </div>
              </div>
              <div class="col-md-6"></div>
            </div>

            <!-- La vision (droite) -->
            <div class="timeline-item row">
              <div class="col-md-6"></div>
              <div class="col-md-6 timeline-right">
                <div class="card-timeline">
                  <h5><?php echo get_phrase("The vision") ?></h5>
                  <p><?php echo get_phrase("Create an e-learning platform focused on") ?> 
                    <span class="highlight"><?php echo get_phrase("community") ?></span> <?php echo get_phrase("and") ?><span class="highlight"><?php echo get_phrase("mutual support") ?></span>.
                  </p>
                </div>
              </div>
            </div>

            <!-- La mission (gauche) -->
            <div class="timeline-item row">
              <div class="col-md-6 timeline-left d-flex justify-content-end">
                <div class="card-timeline">
                  <h5><?php echo get_phrase("The mission") ?></h5>
                  <p><?php echo get_phrase("Give each mentor the tools to") ?>
                    <span class="highlight"><?php echo get_phrase("to teach") ?></span>, 
                    <span class="highlight"><?php echo get_phrase("unite") ?></span> <?php echo get_phrase("and") ?>
                    <span class="highlight"><?php echo get_phrase("monetize") ?></span>.
                  </p>
                </div>
              </div>
              <div class="col-md-6"></div>
            </div>

          </div>
        </div>
    </section>

    <!-- ÉQUIPE -->
    <!-- <section id="equipe" class="py-5">
      <div class="container">
        <h2 class="display-5 fw-bold text-center mb-1" style="background:linear-gradient(180deg, color-mix(in srgb, var(--accent) 30%, #0000) 0%, #0000 90%); -webkit-background-clip:text; background-clip:text; color:transparent;">Notre équipe</h2>

        <div id="teamCarousel" class="carousel slide" data-bs-ride="false" aria-label="Carrousel équipe">
          <div class="carousel-indicators">
            <button type="button" data-bs-target="#teamCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Mohamed"></button>
            <button type="button" data-bs-target="#teamCarousel" data-bs-slide-to="1" aria-label="Stéphanie"></button>
          </div>

          <div class="carousel-inner py-3" role="listbox">
            <div class="carousel-item active" role="option" aria-selected="true">
              <div class="position-relative card">
                <img src="https://i.postimg.cc/Bb3qk7Fj/moha.jpg" alt="Mohamed – CEO Wayo Academy" width="280" height="380" loading="eager" decoding="async">
                <div class="card-actions">
                  <a class="action" href="https://www.linkedin.com/in/mohamed-bouhouti/" target="_blank" rel="noopener" title="LinkedIn de Mohamed" aria-label="LinkedIn de Mohamed">
                    <svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true"><path d="M4.98 3.5C4.98 4.88 3.86 6 2.5 6S0 4.88 0 3.5 1.12 1 2.5 1 4.98 2.12 4.98 3.5zM0 8.5h5V24H0V8.5zM8 8.5h4.8v2.1h.07c.67-1.2 2.3-2.46 4.73-2.46 5.06 0 6 3.33 6 7.67V24h-5v-6.65c0-1.58-.03-3.62-2.2-3.62-2.2 0-2.54 1.72-2.54 3.5V24H8V8.5z" fill="currentColor"/></svg>
                  </a>
                  <a class="action" href="https://moscaling.ae/" target="_blank" rel="noopener" title="Site web de Mohamed" aria-label="Site web de Mohamed">
                    <svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true"><path d="M12 2a10 10 0 100 20 10 10 0 000-20zm7.94 9h-3.17a15.9 15.9 0 00-1.2-5.02A8.02 8.02 0 0119.94 11zM12 4c.92 0 2.33 1.86 3.06 5H8.94C9.67 5.86 11.08 4 12 4zM6.43 6.98A15.9 15.9 0 005.23 11H2.06a8.02 8.02 0 014.37-4.02zM4.06 13h3.17c.25 1.77.74 3.52 1.2 5.02A8.02 8.02 0 014.06 13zM12 20c-.92 0-2.33-1.86-3.06-5h6.12C14.33 18.14 12.92 20 12 20zm5.51-1.98A15.9 15.9 0 0018.77 13h3.17a8.02 8.02 0 01-4.43 5.02z" fill="currentColor"/></svg>
                  </a>
                </div>
              </div>
              <div class="text-center member-legend mt-3">
                <h3 class="h4 m-0">Mohamed</h3>
                <p class="m-0 small">CEO – Wayo Academy</p>
              </div>
            </div>
            <div class="carousel-item" role="option" aria-selected="false">
              <div class="position-relative card">
                <img src="https://i.postimg.cc/nh71Pcgx/1719021039126.jpg" alt="Stéphanie Decloedt – Co-fondatrice" width="280" height="380" loading="lazy" decoding="async">
                <div class="card-actions">
                  <a class="action" href="https://www.linkedin.com/in/st%C3%A9phanie-decloedt-040259161/" target="_blank" rel="noopener" title="LinkedIn de Stéphanie Decloedt" aria-label="LinkedIn de Stéphanie Decloedt">
                    <svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true"><path d="M4.98 3.5C4.98 4.88 3.86 6 2.5 6S0 4.88 0 3.5 1.12 1 2.5 1 4.98 2.12 4.98 3.5zM0 8.5h5V24H0V8.5zM8 8.5h4.8v2.1h.07c.67-1.2 2.3-2.46 4.73-2.46 5.06 0 6 3.33 6 7.67V24h-5v-6.65c0-1.58-.03-3.62-2.2-3.62-2.2 0-2.54 1.72-2.54 3.5V24H8V8.5z" fill="currentColor"/></svg>
                  </a>
                </div>
              </div>
              <div class="text-center member-legend mt-3">
                <h3 class="h4 m-0">Stéphanie Decloedt</h3>
                <p class="m-0 small">Co-fondatrice</p>
              </div>
            </div>
          </div>

          <button class="carousel-control-prev" type="button" data-bs-target="#teamCarousel" data-bs-slide="prev" aria-label="Précédent">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Précédent</span>
          </button>
          <button class="carousel-control-next" type="button" data-bs-target="#teamCarousel" data-bs-slide="next" aria-label="Suivant">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Suivant</span>
          </button>
        </div>
      </div>
    </section> -->

      <!-- ÉQUIPE (2 personnes) -->
    <section id="equipe" class="section py-5" aria-labelledby="team-title">
      <div class="container">
        <h1 id="team-title" class="about-title"><?php echo get_phrase("Our team") ?></h1>

        <div class="carousel-container" data-animate>
          <button class="nav-arrow left" aria-label="Précédent">‹</button>

          <div class="carousel-track" role="listbox" aria-label="Carrousel équipe">
            <!-- Mohamed (centre) -->
            <div class="card" data-index="0" role="option" aria-selected="true">
              <img
                src="https://i.postimg.cc/Bb3qk7Fj/moha.jpg"
                alt="Mohamed – CEO Wayo Academy"
                width="280" height="380"
                fetchpriority="high"
                decoding="async"
                style="aspect-ratio: 280 / 380; object-fit: cover; object-position: center;"
              >
              <div class="card-actions" aria-label="Liens de Mohamed">
                <a class="action" href="https://www.linkedin.com/in/mohamed-bouhouti/" target="_blank" rel="noopener" title="LinkedIn de Mohamed" aria-label="LinkedIn de Mohamed">
                  <svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true">
                    <path d="M4.98 3.5C4.98 4.88 3.86 6 2.5 6S0 4.88 0 3.5 1.12 1 2.5 1 4.98 2.12 4.98 3.5zM0 8.5h5V24H0V8.5zM8 8.5h4.8v2.1h.07c.67-1.2 2.3-2.46 4.73-2.46 5.06 0 6 3.33 6 7.67V24h-5v-6.65c0-1.58-.03-3.62-2.2-3.62-2.2 0-2.54 1.72-2.54 3.5V24H8V8.5z" fill="currentColor"/>
                  </svg>
                </a>
                <a class="action" href="https://moscaling.ae/" target="_blank" rel="noopener" title="Site web de Mohamed" aria-label="Site web de Mohamed">
                  <svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true">
                    <path d="M12 2a10 10 0 100 20 10 10 0 000-20zm7.94 9h-3.17a15.9 15.9 0 00-1.2-5.02A8.02 8.02 0 0119.94 11zM12 4c.92 0 2.33 1.86 3.06 5H8.94C9.67 5.86 11.08 4 12 4zM6.43 6.98A15.9 15.9 0 005.23 11H2.06a8.02 8.02 0 014.37-4.02zM4.06 13h3.17c.25 1.77.74 3.52 1.2 5.02A8.02 8.02 0 014.06 13zM12 20c-.92 0-2.33-1.86-3.06-5h6.12C14.33 18.14 12.92 20 12 20zm5.51-1.98A15.9 15.9 0 0018.77 13h3.17a8.02 8.02 0 01-4.43 5.02z" fill="currentColor"/>
                  </svg>
                </a>
              </div>
            </div>

            <!-- Stéphanie (gauche de Mohamed) -->
            <div class="card" data-index="1" role="option" aria-selected="false">
              <img
                src="https://i.postimg.cc/nh71Pcgx/1719021039126.jpg"
                alt="Stéphanie Decloedt – Co-fondatrice"
                width="280" height="380"
                loading="lazy" decoding="async"
                style="aspect-ratio: 280 / 380; object-fit: cover; object-position: center;"
              >
              <div class="card-actions" aria-label="Liens de Stéphanie Decloedt">
                <a class="action" href="https://www.linkedin.com/in/st%C3%A9phanie-decloedt-040259161/" target="_blank" rel="noopener" title="LinkedIn de Stéphanie Decloedt" aria-label="LinkedIn de Stéphanie Decloedt">
                  <svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true">
                    <path d="M4.98 3.5C4.98 4.88 3.86 6 2.5 6S0 4.88 0 3.5 1.12 1 2.5 1 4.98 2.12 4.98 3.5zM0 8.5h5V24H0V8.5zM8 8.5h4.8v2.1h.07c.67-1.2 2.3-2.46 4.73-2.46 5.06 0 6 3.33 6 7.67V24h-5v-6.65c0-1.58-.03-3.62-2.2-3.62-2.2 0-2.54 1.72-2.54 3.5V24H8V8.5z" fill="currentColor"/>
                  </svg>
                </a>
              </div>
            </div>
          </div>

          <button class="nav-arrow right" aria-label="Suivant">›</button>
        </div>

        <!-- Légende dynamique sous le carrousel -->
        <div class="member-info" aria-live="polite">
          <h2 class="member-name"><?php echo get_phrase("Mohamed") ?></h2>
          <p class="member-role"><?php echo get_phrase("CEO – Wayo Academy") ?></p>
        </div>

        <div class="dots" role="tablist" aria-label="Navigation membres">
          <button class="dot active" data-index="0" role="tab" aria-selected="true"></button>
          <button class="dot" data-index="1" role="tab" aria-selected="false"></button>
        </div>
      </div>
    </section>


    <!-- OÙ NOUS TROUVER -->
    <section id="contact" class="contact py-5">
      <div class="container">
        <header class="text-center mb-4" data-animate>
          <h2 class="h2 mb-2"><?php echo get_phrase("Where to find us?") ?></h2>
          <p class="section-subtitle"><?php echo get_phrase("Visit us, or get in touch — we respond quickly 🎯") ?></p>
        </header>

        <div class="row g-3 align-items-stretch">
          <div class="col-12 col-lg-8" data-animate>
            <article class="p-3 bg-white rounded-4 shadow-sm h-100">
              <div class="map-embed rounded-3">
                <iframe title="Wayo Academy – R320 Um Hurair 2, Dubai, UAE" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen src="https://www.google.com/maps?q=R320%20Um%20Hurair%202,%20Dubai,%20UAE&output=embed"></iframe>
              </div>
              <!-- <div class="d-flex justify-content-end mt-2">
                <a class="btn btn-accent btn-pill" target="_blank" rel="noopener" href="https://www.google.com/maps/search/?api=1&query=R320%20Um%20Hurair%202%2C%20Dubai%2C%20UAE">Ouvrir dans Google Maps</a>
              </div> -->
            </article>
          </div>
          <div class="col-12 col-lg-4" data-animate>
            <aside class="contact-card p-4 h-100 d-flex flex-column justify-content-center align-items-center text-center">
              <h3 class="h5 mb-3"><?php echo get_phrase("Contact details") ?></h3>
              <ul class="list-unstyled d-grid gap-2 m-0">
                <li class="d-grid" style="grid-template-columns:22px 1fr; gap:8px;">
                  <span>📍</span><span><?php echo get_phrase("R320 Um Hurair 2, Dubai, UAE") ?></span>
                </li>
                <li class="d-grid" style="grid-template-columns:22px 1fr; gap:8px;">
                  <span>✉️</span><a class="fw-semibold" href="mailto:info@wayo.cloud" style="color:var(--accent)"><?php echo get_phrase("info@wayo.cloud") ?></a>
                </li>
                <li class="d-grid" style="grid-template-columns:22px 1fr; gap:8px;">
                  <span>📞</span><a class="fw-semibold" href="tel:+971501548923" style="color:var(--accent)"><?php echo get_phrase("+971 50 154 8923") ?></a>
                </li>
                <li class="d-grid" style="grid-template-columns:22px 1fr; gap:8px;">
                  <span>🌐</span><a class="fw-semibold" href="https://moscaling.ae/" target="_blank" rel="noopener" style="color:var(--accent)">moscaling.ae</a>
                </li>
              </ul>
              <div class="mt-3 d-grid gap-2">
                <p class="m-0"><?php echo get_phrase("Need a demo? Write to us and we’ll schedule a slot.") ?></p>
                <a class="btn btn-outline-secondary btn-pill" href="#cta"><?php echo get_phrase("Request a demo") ?></a>
              </div>
            </aside>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA FINAL -->
    <section id="cta" class="py-5 bg-alt">
      <div class="container" data-animate>
        <div class="text-center">
          <h2 class="h2 mb-2 p-cta"><?php echo get_phrase("Ready to join the community?") ?></h2>
          <p class="mb-3"><?php echo get_phrase("Become a mentor or learner, and grow with Wayo.") ?></p>
          <a class="btn btn-accent btn-pill" href="#"><?php echo get_phrase("Create my account") ?></a>
        </div>
      </div>
    </section>
  </main>



  <script>
    // Intersection reveal
    (function(){
      const els = document.querySelectorAll('[data-animate]');
      const io = new IntersectionObserver((entries)=>{
        entries.forEach(entry=>{
          if(entry.isIntersecting){ entry.target.classList.add('is-visible'); io.unobserve(entry.target); }
        });
      },{threshold:0.15});
      els.forEach(el=>io.observe(el));
    })();

    // Smooth in-page scroll for browsers lacking CSS smooth behavior
    document.querySelectorAll('a[href^="#"]').forEach(a=>{
      a.addEventListener('click', (e)=>{
        const id = a.getAttribute('href').slice(1);
        const target = document.getElementById(id);
        if(target){ e.preventDefault(); target.scrollIntoView({behavior:'smooth', block:'start'}); }
      });
    });


  </script> 

  <script>

  // Révélations au scroll + smooth scroll + menu mobile
(function(){
  const els = document.querySelectorAll('[data-animate]');
  const io = new IntersectionObserver((entries)=>{
    entries.forEach(entry=>{
      if(entry.isIntersecting){
        entry.target.classList.add('is-visible');
        io.unobserve(entry.target);
      }
    });
  },{threshold: 0.15});
  els.forEach(el=>io.observe(el));

  document.querySelectorAll('a[href^="#"]').forEach(a=>{
    a.addEventListener('click', (e)=>{
      const id = a.getAttribute('href').slice(1);
      const target = document.getElementById(id);
      if(target){
        e.preventDefault();
        target.scrollIntoView({behavior:'smooth', block:'start'});
      }
    });
  });

  const menuToggle = document.getElementById('menu-toggle');
  const navLinks = document.querySelector('.nav-links');
  menuToggle?.addEventListener('click', ()=> navLinks?.classList.toggle('show'));
})();

// === TEAM CAROUSEL (2 cartes : Mohamed centre, Stéphanie à gauche) ===
(function(){
  const root = document.getElementById('equipe');
  if(!root) return;

  const teamMembers = [
    { name: "Mohamed", role: "CEO – Wayo Academy" },
    { name: "Stéphanie Decloedt", role: "Co-fondatrice" }
  ];

  const cards = root.querySelectorAll(".card");
  const dots = root.querySelectorAll(".dot");
  const memberName = root.querySelector(".member-name");
  const memberRole = root.querySelector(".member-role");
  const leftArrow = root.querySelector(".nav-arrow.left");
  const rightArrow = root.querySelector(".nav-arrow.right");

  let currentIndex = 0;
  let isAnimating = false;

  function updateCarousel(newIndex) {
    if (isAnimating) return;
    isAnimating = true;

    currentIndex = (newIndex + cards.length) % cards.length;

    cards.forEach((card, i) => {
      const offset = (i - currentIndex + cards.length) % cards.length;
      card.classList.remove("center","left-1","left-2","right-1","right-2","hidden");

      // Ordre pour bien placer la carte précédente à gauche (cas 2 cartes)
      if (offset === 0) {
        card.classList.add("center");
      } else if (offset === cards.length - 1) {
        card.classList.add("left-1");
      } else if (offset === 1) {
        card.classList.add("right-1");
      } else if (offset === 2) {
        card.classList.add("right-2");
      } else if (offset === cards.length - 2) {
        card.classList.add("left-2");
      } else {
        card.classList.add("hidden");
      }

      card.setAttribute("aria-selected", offset === 0 ? "true" : "false");
    });

    dots.forEach((dot, i) => {
      dot.classList.toggle("active", i === currentIndex);
      dot.setAttribute("aria-selected", i === currentIndex ? "true" : "false");
    });

    // Légende sous le carrousel
    memberName.style.opacity = "0";
    memberRole.style.opacity = "0";

    setTimeout(() => {
      memberName.textContent = teamMembers[currentIndex].name;
      memberRole.textContent = teamMembers[currentIndex].role;
      memberName.style.opacity = "1";
      memberRole.style.opacity = "1";
    }, 300);

    setTimeout(() => { isAnimating = false; }, 800);
  }

  leftArrow?.addEventListener("click", () => updateCarousel(currentIndex - 1));
  rightArrow?.addEventListener("click", () => updateCarousel(currentIndex + 1));
  dots.forEach((dot, i) => dot.addEventListener("click", () => updateCarousel(i)));
  cards.forEach((card, i) => card.addEventListener("click", () => updateCarousel(i)));

  // Navigation clavier (focus dans la section)
  document.addEventListener("keydown", (e) => {
    if(!root.contains(document.activeElement)) return;
    if (e.key === "ArrowLeft") updateCarousel(currentIndex - 1);
    if (e.key === "ArrowRight") updateCarousel(currentIndex + 1);
  });

  // Gestes tactiles
  let touchStartX = 0, touchEndX = 0;
  root.addEventListener("touchstart", (e) => { touchStartX = e.changedTouches[0].screenX; }, {passive:true});
  root.addEventListener("touchend", (e) => {
    touchEndX = e.changedTouches[0].screenX;
    const diff = touchStartX - touchEndX;
    const swipeThreshold = 50;
    if (Math.abs(diff) > swipeThreshold) {
      if (diff > 0) updateCarousel(currentIndex + 1);
      else updateCarousel(currentIndex - 1);
    }
  }, {passive:true});

  // Init
  updateCarousel(0);
})();
</script>
