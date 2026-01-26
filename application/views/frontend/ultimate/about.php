

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
      font-family: 'Urbanist',system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;
    }

    html{ 
        scroll-behavior:smooth; 
    }
    body{
          font-family: 'Urbanist',system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;
        }
    img{
         max-width:100%; height:auto;
        }

    /* Hero */
    .hero{ position:relative; min-height:35vh; display:grid; place-items:center; color:#fff; background-image:url('<?php echo base_url('uploads/images/decloedt/img/bg-about-us.png'); ?>'); background-size:cover; background-position:center; }
    .hero::before{ content:""; position:absolute; inset:0; background:linear-gradient(180deg, rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.60));}
    .hero .hero-content{ position:relative; text-align:center; }
    .hero .lead{ max-width:760px; margin-inline:auto; color:#e9e9ef }

    .btn-pill{ border-radius:999px; font-weight:700; padding:.8rem 1.2rem; }
    .btn-accent{ background:var(--accent); color:#fff; box-shadow:0 8px 20px rgba(252,123,48,.25) }
    .btn-accent:hover{ background:var(--accent-600); color:#fff; }
    .btn-ghost-about{ background:#ffffff14; color:#fff; border:1px solid #ffffff40 }
    .btn-ghost-about:hover{ background:#ffffff26; color:#fff; }

    /* Sections */
    .section-subtitle{ color:var(--muted); }
    .bg-alt{ background:var(--card-alt); }

    /* Values */
    .value-card{ background:var(--card); border-radius:var(--radius); padding:20px; box-shadow:var(--shadow); transition:transform .2s, box-shadow .2s; height:100%; text-align: center; }
    .value-card:hover{ transform:translateY(-4px); box-shadow:0 14px 36px rgba(0,0,0,.12) }

   
    @media (min-width: 992px){
     
    }
    @media (max-width: 991.98px){
     
    }

    

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

    /* Timeline Section Styling */
    .section-timeline {
        background: #f8f9fa;
        position: relative;
    }

    .timeline {
      position: relative;
      padding: 40px 0;
      max-width: 1100px;
      margin: 0 auto;
    }
    
    /* Vertical Line */
    .timeline::before {
      content: "";
      position: absolute;
      left: 50%;
      top: 0;
      bottom: 0;
      width: 2px;
      background: #ECEEF3;
      transform: translateX(-50%);
    }

    /* Orange Arrow at bottom */
    .timeline::after {
      content: "" !important;
      position: absolute !important;
      bottom: -15px !important;
      left: 50% !important;
      transform: translateX(-50%) !important;
      width: 0 !important;
      height: 0 !important;
      border-left: 10px solid transparent !important;
      border-right: 10px solid transparent !important;
      border-top: 15px solid var(--accent) !important;
      z-index: 10 !important;
      display: block !important;
    }

    .timeline-item {
      position: relative;
      margin-bottom: 40px;
      z-index: 1;
    }

    /* Hide existing dot from external CSS */
    .timeline-item::before {
        content: none !important;
    }

    /* Dot on the line */
    .timeline-item::after {
      content: "";
      position: absolute;
      left: 50%;
      top: 50%;
      width: 14px;
      height: 14px;
      background: var(--accent);
      border-radius: 50%;
      transform: translate(-50%, -50%);
      border: 3px solid #fff;
      box-shadow: 0 0 0 3px rgba(252,123,48,0.1);
      z-index: 2;
    }

    .card-timeline {
      background: #fff;
      padding: 1.5rem 2rem;
      border-radius: 16px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.05);
      width: 100%;
      transition: all 0.3s ease;
      border: 1px solid rgba(0,0,0,0.05);
      position: relative;
    }
    
    .card-timeline:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 40px rgba(0,0,0,0.1);
        border-color: rgba(252, 123, 48, 0.2);
    }

    .card-timeline h5 {
      color: #1e1e2d;
      font-weight: 800;
      margin-bottom: 0.5rem;
      font-size: 1.15rem;
    }
    
    .card-timeline p {
      margin: 0;
      color: var(--muted);
      line-height: 1.6;
      font-size: 0.95rem;
    }

    .highlight {
      color: var(--accent);
      font-weight: 700;
    }

    @media (min-width: 769px) {
        .timeline-left {
            padding-right: 50px !important;
        }
        .timeline-right {
            padding-left: 50px !important;
        }
        .timeline-left .card-timeline {
            text-align: right;
        }
    }

    @media (max-width: 768px) {
      .timeline::before {
          left: 20px;
          transform: translateX(-50%);
      }
      .timeline::after {
          left: 20px !important;
          transform: translateX(-50%) !important;
      }
      .timeline-item::after {
          left: 32px;
          transform: translate(-50%, -50%);
      }
      .timeline-left {
          justify-content: flex-start !important;
          padding-left: 45px !important;
          margin-bottom: 30px;
      }
      .timeline-right {
          padding-left: 45px !important;
      }
      .card-timeline {
          text-align: left !important;
      }
    }
  </style>



  <main >
    <!-- HERO -->
    <section class="hero" <?php echo (get_user_language() === 'arabic') ? 'dir="rtl"' : 'dir="ltr"'; ?>>
      <div class="container hero-content py-5" data-animate>
        <h1 class="display-5 fw-bold mb-2"><?php echo get_phrase("Wayo Academy, much more than a learning platform") ?></h1>
       
        <p class="lead mb-4 text-white fs-md-4 fs-lg-3" style="letter-spacing: 1px; font-size: 1.5rem; margin-bottom: 1rem;"><?php echo get_phrase("A community of experts and learners united to turn skills into opportunities.") ?></p>
    <div class="row g-2 justify-content-center">
      <div class="col-12 col-md-auto">
        <a class="btn btn-accent btn-pill w-100" href="<?php echo site_url('home/communities'); ?>">
          <?php echo get_phrase("Join the community ") ?>
        </a>
      </div>
      <div class="col-12 col-md-auto">
        <a class="btn btn-ghost-about btn-pill w-100" href="#histoire" aria-label="Aller à notre histoire">
          <?php echo get_phrase("Discover_the_story ") ?>
        </a>
      </div>
    </div>
      </div>
    </section>

    <!-- MISSION & VALEURS -->
    <section id="mission" class="py-5" <?php echo (get_user_language() === 'arabic') ? 'dir="rtl"' : 'dir="ltr"'; ?>>
      <div class="container">
        <header class="text-center mission unified-title-container mb-4" data-animate>
          <h2 class="h2"><?php echo get_phrase("Our Mission") ?></h2>
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
              <h3 class="h6 m-0"><?php echo get_phrase("Freedom") ?></h3>
              <p class="mb-0 text-secondary"><?php echo get_phrase("Create your path: courses, community, income… your way.") ?></p>
            </article>
          </div>
        </div>
      </div>
    </section>

    <!-- HISTOIRE -->
     <section class="section-timeline py-5" id="histoire" <?php echo (get_user_language() === 'arabic') ? 'dir="rtl"' : 'dir="ltr"'; ?>>
        <div class="container">
          <div class="text-center histoire unified-title-container mb-5">
            <h2 class="social-media-main-text">
               <?php echo get_phrase("The story of") ?>
                <span style="color: #FC7B30;"><?php echo get_phrase("Wayo academy") ?></span>
            </h2>
            <p class="text-muted">
             <?php echo get_phrase("Why Wayo? Because there was a missing bridge between") ?> <em> <?php echo get_phrase("talents") ?></em> <?php echo get_phrase("and") ?>  <em> <?php echo get_phrase("opportunities") ?></em>.
            </p>
          </div>

          <div class="timeline">
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

    <!-- caroussel -->

     <section class="team-section py-5">
        <div class="histoire unified-title-container">
            <h2 class="social-media-main-text">
               <?php echo get_phrase("Our_teams") ?>
            </h2>
        </div>
        <div class="slider-container">
            <div class="slider-wrapper" id="sliderWrapper">
                <!-- Team Member 1 -->
                 <div class="team-card">
                  <div class="card-image">
                    <img src="<?php echo base_url('uploads/teams/bouhouti.jpg'); ?>" alt="Mohamed Bouhouti<?php echo get_phrase("Our_teams") ?>">
                  </div>
                  <div class="card-content">
                    <h3><?php echo get_phrase("Mohamed_Bouhouti") ?></h3>
                    <div class="position"><?php echo get_phrase("CEO_–_Wayo_Academy") ?></div>
                    <div class="social-links">
                      <a href="https://www.linkedin.com/in/mohamed-bouhouti/"target="_blank"><i class="fab fa-linkedin-in"></i></a>
                      <a href="https://moscaling.ae/"><i class=" fas fa-link"></i></a>
                      <a href="mailto:contact@exemple.com"><i class="fas fa-envelope"></i></a>
                    </div>
                  </div>
                </div>
              
                <!-- Team Member 2 -->
                <div class="team-card">
                  <div class="card-image">
                    <img src="<?php echo base_url('uploads/teams/decloedt.jpg'); ?>" alt="<?php echo get_phrase("decloedt") ?>">
                  </div>
                  <div class="card-content">
                    <h3><?php echo get_phrase("Stéphanie_Decloedt") ?></h3>
                    <div class="position"><?php echo get_phrase("Co-founder") ?></div>
                    <div class="social-links">
                      <a href="https://www.linkedin.com/in/st%C3%A9phanie-decloedt-040259161/" target="_blank"><i class="fab fa-linkedin-in"></i></a>
                      <a href="mailto:Stephanie@decloedt.cloud"><i class="fas fa-envelope"></i></a>
                    </div>
                  </div>
                </div>

                <!-- Team Member 3 -->
                 <div class="team-card">
                  <div class="card-image">
                    <img src="<?php echo base_url('uploads/teams/bennani.jpg'); ?>" alt="<?php echo get_phrase("Fatine_Bennani") ?>">
                  </div>
                  <div class="card-content">
                    <h3><?php echo get_phrase("Fatine_Bennani") ?></h3>
                    <div class="position"><?php echo get_phrase("Human_Resources") ?></div>   
                    <div class="social-links">
                      <a href="https://www.linkedin.com/in/fatine-bennani-21b029144/" target="_blank"><i class="fab fa-linkedin-in"></i></a>
                      <a href="mailto:fatine.bennani@decloedt.cloud"><i class="fas fa-envelope"></i></a>
                    </div>
                  </div>
                </div>

                <!-- Team Member 4 -->
                  <div class="team-card">
                  <div class="card-image">
                    <img src="<?php echo base_url('uploads/teams/aboulfath.jpg'); ?>" alt="<?php echo get_phrase("Ahmed Aboulfath") ?>">
                  </div>
                  <div class="card-content">
                    <h3><?php echo get_phrase("Ahmed_Aboulfath") ?></h3>
                    <div class="position"><?php echo get_phrase("Digital_Marketing_Specialist") ?></div>
                    <div class="social-links">
                      <a href="https://www.linkedin.com/in/ahmed-aboulfath-2156a6162/" target="_blank"><i class="fab fa-linkedin-in"></i></a>
                      <a href="mailto:ahmed.aboulfath@decloedt.cloud"><i class="fas fa-envelope"></i></a>
                    </div>
                  </div>
                </div>
                

                <!-- Team Member 5 -->
                 <div class="team-card">
                  <div class="card-image">
                    <img src="<?php echo base_url('uploads/teams/naji.png'); ?>" alt="naji">
                  </div>
                  <div class="card-content">
                    <h3><?php echo get_phrase("Aymane_Naji") ?></h3>
                    <div class="position"><?php echo get_phrase("Digital_Marketing_Specialist") ?></div>
                    <div class="social-links">
                      <a href="https://www.linkedin.com/in/aymane-naji-743753297/" target="_blank"><i class="fab fa-linkedin-in"></i></a>
                      <a href="mailto:aymane.naji@decloedt.cloud"><i class="fas fa-envelope"></i></a>
                    </div>
                  </div>
                </div>
                 
                

                <!-- Team Member 6 -->
                 <div class="team-card">
                  <div class="card-image">
                    <img src="<?php echo base_url('uploads/teams/sandale.jpg'); ?>" alt="<?php echo get_phrase("Zakaria_Sandal") ?>">
                  </div>
                  <div class="card-content">
                    <h3><?php echo get_phrase("Zakaria_Sandal") ?></h3>
                    <div class="position"> <?php echo get_phrase("Digital_Marketing_Specialist") ?></div>
                    <div class="social-links">
                      <a href="https://www.linkedin.com/in/ziko-zakaria/" target="_blank"><i class="fab fa-linkedin-in"></i></a>
                      <a href="mailto:zakaria.sandal@decloedt.cloud"><i class="fas fa-envelope"></i></a>
                    </div>
                  </div>
                </div>
                

                <!-- Team Member 7 -->
                 <div class="team-card">
                  <div class="card-image">
                    <img src="<?php echo base_url('uploads/teams/khiat.png'); ?>" alt="<?php echo get_phrase("Mehdi El khiat") ?>">
                  </div>
                  <div class="card-content">
                    <h3><?php echo get_phrase("Mehdi El khiat") ?></h3>
                    <div class="position"><?php echo get_phrase("Product Owner") ?></div>
                    <div class="social-links">
                      <a href="https://www.linkedin.com/in/mehdi-elkhiat-4138aa15b/" target="_blank"><i class="fab fa-linkedin-in"></i></a>
                      <a href="mailto:Mehdi.elkhiat@decloedt.cloud"><i class="fas fa-envelope"></i></a>
                    </div>
                  </div>
                </div>
                 
                

                <!-- Team Member 8 -->
                 <div class="team-card">
                  <div class="card-image">
                    <img src="<?php echo base_url('uploads/teams/fettah.jpg'); ?>" alt="<?php echo get_phrase("Abdelfattah_Allam") ?>">
                  </div>
                  <div class="card-content">
                    <h3><?php echo get_phrase("Abdelfattah_Allam") ?></h3>
                    <div class="position"><?php echo get_phrase("Senior Developer") ?></div>   
                    <div class="social-links">
                      <a href="https://www.linkedin.com/in/abdelfattah-allam-654b92160/" target="_blank"><i class="fab fa-linkedin-in"></i></a>
                      <a href="mailto:abdelfattah.allam@decloedt.cloud"><i class="fas fa-envelope"></i></a>
                    </div>
                  </div>
                </div>
                 
                

                <!-- Team Member 9 -->
                 <div class="team-card">
                  <div class="card-image">
                    <img src="<?php echo base_url('uploads/teams/tchoubi.png'); ?>" alt="<?php echo get_phrase("Mouhssine_Tchoubi") ?>">
                  </div>
                  <div class="card-content">
                    <h3><?php echo get_phrase("Mouhssine_Tchoubi") ?></h3>
                    <div class="position"><?php echo get_phrase("Full-Stack_Web_Developer") ?></div>
                    <div class="social-links">
                      <a href="https://www.linkedin.com/in/mouhssine-tchoubi-8a2b02176/" target="_blank"><i class="fab fa-linkedin-in"></i></a>
                      <a href="mailto:Mouhssine.Tchoubi@decloedt.cloud"><i class="fas fa-envelope"></i></a>
                    </div>
                  </div>
                </div>
                

                <!-- Team Member 10 -->
                 <div class="team-card">
                  <div class="card-image">
                    <img src="<?php echo base_url('uploads/teams/bastor.png'); ?>" alt="<?php echo get_phrase("Hamza_Bastor") ?>">
                  </div>
                  <div class="card-content">
                    <h3><?php echo get_phrase("Hamza_Bastor") ?></h3>
                    <div class="position"><?php echo get_phrase("Software_Engineer") ?></div>
                    <div class="social-links">
                      <a href="https://www.linkedin.com/in/hamza-bastor-5b6215231/" target="_blank"><i class="fab fa-linkedin-in"></i></a>
                      <a href="mailto:Hamza.Bastor@decloedt.cloud"><i class="fas fa-envelope"></i></a>
                    </div>
                  </div>
                </div>
               

                <!-- Team Member 11 -->
                 <div class="team-card">
                  <div class="card-image">
                    <img src="<?php echo base_url('uploads/teams/abbaoui.png'); ?>" alt="<?php echo get_phrase("Khalil_Abbaoui") ?>">
                  </div>
                  <div class="card-content">
                    <h3><?php echo get_phrase("Khalil_Abbaoui") ?></h3>
                    <div class="position"><?php echo get_phrase("Software_Engineer") ?></div>
                    <div class="social-links">
                      <a href="https://www.linkedin.com/in/khalil-abbaoui-537873268/" target="_blank"><i class="fab fa-linkedin-in"></i></a>
                      <a href="mailto:khalil.abbaoui@decloedt.cloud"><i class="fas fa-envelope"></i></a>
                    </div>
                  </div>
                </div>
                

                <!-- Team Member 12 -->
                 <div class="team-card">
                  <div class="card-image">
                    <img src="<?php echo base_url('uploads/teams/maski.png'); ?>" alt="<?php echo get_phrase("Aymen_Maski") ?>">
                  </div>
                  <div class="card-content">
                    <h3><?php echo get_phrase("Aymen_Maski") ?> </h3>
                    <div class="position"><?php echo get_phrase("AI_Software_Engineer") ?> </div>
                    <div class="social-links">
                      <a href="https://www.linkedin.com/in/free-palestine-%F0%9F%87%B5%F0%9F%87%B8-56488125b/?utm_source=share&utm_campaign=share_via&utm_content=profile&utm_medium=android_app" target="_blank"><i class="fab fa-linkedin-in"></i></a>
                      <a href="mailto:Maski.Aymen@decloedt.cloud"><i class="fas fa-envelope"></i></a>
                    </div>
                  </div>
                </div>
                
                
                <!-- Team Member 13 -->
                 <div class="team-card">
                  <div class="card-image">
                    <img src="<?php echo base_url('uploads/teams/zmane.png'); ?>" alt="<?php echo get_phrase("Ilyas Zmane") ?>">
                  </div>
                  <div class="card-content">
                    <h3><?php echo get_phrase("Ilyas Zmane") ?></h3>

                    <div class="position"><?php echo get_phrase("Web Developer") ?></div>
                    <div class="social-links">
                      <a href="https://www.linkedin.com/in/ilyas-zmane-0483691bb/" target="_blank"><i class="fab fa-linkedin-in"></i></a>
                      <a href="mailto:Ilyas.Zmane@decloedt.cloud"><i class="fas fa-envelope"></i></a>
                    </div>
                  </div>
                </div>
            </div>
        </div>

        <div class="slider-controls">
            <button class="slider-btn" id="prevBtn">
                <i class="fas fa-chevron-left"></i>
            </button>
            <button class="slider-btn" id="nextBtn">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>

        <div class="slider-dots" id="dotsContainer"></div>
  </section>


    <!-- CTA FINAL -->
    <section id="cta" class="py-5 bg-alt" <?php echo (get_user_language() === 'arabic') ? 'dir="rtl"' : 'dir="ltr"'; ?>>
      <div class="container" data-animate>
        <div class="text-center">
          <h2 class="h2 mb-2 p-cta"><?php echo get_phrase("Ready to join the community?") ?></h2>
          <p class="mb-3"><?php echo get_phrase("Become a mentor or learner, and grow with Wayo.") ?></p>
          <?php if ($this->session->userdata('user_id')) : ?>
                        <!-- utilisateur connecté -->
                        <a href="<?php echo route('dashboard'); ?>" class="btn btn-accent btn-pill"><?php echo get_phrase("Create my account ") ?></a>
                    <?php else : ?>
                        <!-- utilisateur non connecté -->
                        <a href="<?php echo site_url('admission/online_admission'); ?>" class="btn btn-accent btn-pill"><?php echo get_phrase("Create my account") ?></a>
          <?php endif; ?>
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

    // caroussel


        const wrapper = document.getElementById('sliderWrapper');
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');
        const dotsContainer = document.getElementById('dotsContainer');
        const cards = document.querySelectorAll('.team-card');
        
        let currentIndex = 0;
        const totalCards = cards.length;
        const cardsPerView = window.innerWidth <= 768 ? 1 : window.innerWidth <= 992 ? 2 : 3;
        const maxIndex = Math.ceil(totalCards / cardsPerView) - 1;

        // Create dots
        for (let i = 0; i <= maxIndex; i++) {
            const dot = document.createElement('div');
            dot.classList.add('dot');
            if (i === 0) dot.classList.add('active');
            dot.addEventListener('click', () => goToSlide(i));
            dotsContainer.appendChild(dot);
        }

        const dots = document.querySelectorAll('.dot');

        function updateSlider() {
            const cardWidth = cards[0].offsetWidth;
            const gap = 30;
            const offset = currentIndex * (cardWidth + gap) * cardsPerView;
            wrapper.style.transform = `translateX(-${offset}px)`;

            dots.forEach((dot, index) => {
                dot.classList.toggle('active', index === currentIndex);
            });
        }

        function nextSlide() {
            currentIndex = currentIndex >= maxIndex ? 0 : currentIndex + 1;
            updateSlider();
        }

        function prevSlide() {
            currentIndex = currentIndex <= 0 ? maxIndex : currentIndex - 1;
            updateSlider();
        }

        function goToSlide(index) {
            currentIndex = index;
            updateSlider();
        }

        nextBtn.addEventListener('click', nextSlide);
        prevBtn.addEventListener('click', prevSlide);

        // Auto-play
        // setInterval(nextSlide, 5000);

        // Responsive handling
        window.addEventListener('resize', updateSlider);


</script>
