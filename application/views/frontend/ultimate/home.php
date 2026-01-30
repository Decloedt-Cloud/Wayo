<!-- ========== MAIN ========== -->
<main class="main-content" id="content" role="main"<?php echo (get_user_language() === 'arabic') ? 'dir="rtl"' : 'dir="ltr"'; ?>>

  <!-- Hero Section avec Bootstrap Grid -->
  <section id="hero" class="hero">
    <!-- Éléments décoratifs -->
    <div class="bg-mesh" aria-hidden="true"></div>
    <div class="bg-orbs" aria-hidden="true">
      <span class="orb o1"></span>
      <span class="orb o2"></span>
      <span class="orb o3"></span>
    </div>

    <!-- Effets lumineux -->
    <span class="hero-glow a"></span>
    <span class="hero-glow b"></span>
    <div class="container-md">
      <div class="row align-items-center g-4">
        <!-- Colonne Gauche: Texte -->
        <div class="col-lg-5 col-xl-5">
          <div class="hero-copy">
            <h1><span class="accent" style="text-transform: capitalize;"><?php echo get_phrase("Monetize") ?></span> <?php echo get_phrase("your_community_with_peace_of_mind.") ?></h1>
            <p class="sub"><?php echo get_phrase("We_specialize_in_secure_payment_platforms_that_don’t_freeze_mentors’_accounts._Build,_engage,_and_grow_your_revenue_without_limitations.") ?></p>
            <div class="hero-ctas">
              <?php if (!$this->session->userdata('user_id')): ?>
                <a href="<?php echo site_url('admission/online_admission'); ?>" class="btn outline"><?php echo get_phrase("create_community") ?></a>
              <?php endif; ?>
            </div>

            <div class="chips">
              <div class="chip pill">
                <span class="chip-icon money"><svg viewBox="0 0 24 24" width="18" height="18">
                    <path fill="currentColor" d="M3 6h18v12H3z" opacity=".15" />
                    <path fill="currentColor" d="M2 5h20v14H2zM5 9a3 3 0 0 0-3-3v12a3 3 0 0 0 3-3h14a3 3 0 0 0 3 3V6a3 3 0 0 0-3 3zM12 9a3 3 0 1 1 0 6 3 3 0 0 1 0-6Z" />
                  </svg></span>
                <strong><?php echo get_phrase("Monetize_your_expertise") ?></strong>
              </div>
              <div class="chip pill">
                <span class="chip-icon users"><svg viewBox="0 0 24 24" width="18" height="18">
                    <path fill="currentColor" d="M16 11a4 4 0 1 0-8 0 4 4 0 0 0 8 0Z" opacity=".2" />
                    <path fill="currentColor" d="M12 13a5 5 0 1 1 5-5 5.006 5.006 0 0 1-5 5Zm0 2c-4.418 0-8 2.239-8 5v2h16v-2c0-2.761-3.582-5-8-5Z" />
                  </svg></span>
                <strong><?php echo get_phrase("Create_communities_a") ?></strong>
              </div>
              <div class="chip pill">
                <span class="chip-icon course"><svg viewBox="0 0 24 24" width="18" height="18">
                    <path fill="currentColor" d="M4 6h16v12H4z" opacity=".2" />
                    <path fill="currentColor" d="M3 5h18v14H3zM6 9h8v2H6zm0 4h12v2H6z" />
                  </svg></span>
                <strong><?php echo get_phrase("Courses_&_live") ?></strong>
              </div>
              <div class="chip pill">
                <span class="chip-icon social"><svg viewBox="0 0 24 24" width="18" height="18">
                    <path fill="currentColor" d="M4 5h16v10H4z" opacity=".2" />
                    <path fill="currentColor" d="M2 4h20v12H6l-4 4zM6 8h12v2H6zm0 4h8v2H6z" />
                  </svg></span>
                <strong><?php echo get_phrase("Built-in_social") ?></strong>
              </div>
            </div>
          </div>
        </div>

        <div class="col-lg-1 col-xl-1"></div>

        <!-- Colonne Droite: Vidéo locale -->
        <div class="col-lg-6 col-xl-6">
          <figure class="yt-card" aria-label="Vidéo de présentation">
            <?php
            $user_lang = get_user_language();
            if ($user_lang === 'french') {
                $video_src = 'V5 Wayo Promo Video francais.mp4';
            } elseif ($user_lang === 'arabic') {
                $video_src = 'v2_Wayo_Academy_Promo_Video.mp4';
            } else {
                $video_src = 'Final En Wayo Promo Video anglais.mp4';
            }
            ?>
            <div class="ratio ratio-16x9 yt-desktop">
              <video autoplay muted loop playsinline controls>
                <source src="<?php echo base_url('uploads/videos/' . $video_src); ?>" type="video/mp4">
                Votre navigateur ne supporte pas la lecture vidéo.
              </video>
            </div>
            <div class="ratio ratio-9x16 yt-mobile">
              <video autoplay muted loop playsinline controls>
                <source src="<?php echo base_url('uploads/videos/' . $video_src); ?>" type="video/mp4">
                Votre navigateur ne supporte pas la lecture vidéo.
              </video>
            </div>
          </figure>
        </div>
      </div>
    </div>
  </section>
  </div>
  <!-- Our key features -->
  <section class="feature-nav-section" <?php echo (get_user_language() === 'arabic') ? 'dir="rtl"' : 'dir="ltr"'; ?>>
    <div class="container py-2">
      <div class="row align-items-center">
        <div class="col-12">
          <h2 class="text-center"><?php echo get_phrase("Our key features") ?></h2>
          <ul class="feature-nav">
            <li class="active" data-feature="bbb" tabindex="0"><?php echo get_phrase("Online course") ?></li>
            <li data-feature="social" tabindex="0"><?php echo get_phrase("Ai") ?></li>
            <li data-feature="quiz" tabindex="0"><?php echo get_phrase("Quiz") ?></li>
          </ul>
          <div class="feature-progress">
            <div class="feature-progress-bar"></div>
          </div>

          <div id="bbb" class="feature-pane active">
            <img src="uploads/images/decloedt/home/online_course.webp" alt="Online Course" loading="lazy" />
          </div>
          <div id="social" class="feature-pane">
            <img src="uploads/images/decloedt/home/Ai.png" alt="Ai" loading="lazy" />
          </div>
          <div id="quiz" class="feature-pane">
            <img src="uploads/images/decloedt/home/quiz_recent.png" alt="Quiz" loading="lazy" />
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- Comparaison Avant et Après Wayo ----->
   <section class="position-relative comparaisonSec"  <?php echo (get_user_language() === 'arabic') ? 'dir="rtl"' : 'dir="ltr"'; ?>>
    <div class="bg-white container position-relative shadow-lg rounded-lg py-4 px-7 unified-title-container" style="z-index: 1;">
      <h2><?php echo get_phrase("A single platform. Zero hassle") ?></h2>
      <p class="text-center text-dark mb-5 fs-6"><?php echo get_phrase("Wayo Academy brings together everything needed to create, sell, and run your training courses without stress.") ?></p>
      <div class="row g-4 ">
            <!-- Sans Wayo Column -->
            <div class="col-lg-6 col-md-12">
                <div class="comparison-card sans-wayo h-100 shadow rounded-4 p-4 p-md-5">
                    <div class="text-center">
                        <span class="badge-header badge-sans"><?php echo get_phrase("Without Wayo"); ?></span>
                    </div>

                    <div class="image-placeholder mb-4 d-flex justify-content-center align-items-center rounded-3">
                        <img src="uploads/images/decloedt/home/without-wayo.png" alt="Without Wayo" loading="lazy" />
                    </div>

                    <ul class="feature-list">
                        <li> <?php echo get_phrase('Too many separate tools, loss of time and errors.'); ?></li>
                        <li> <?php echo get_phrase('Scattered data, no clear visibility.'); ?></li>
                        <li> <?php echo get_phrase('Manual processes, zero automation.'); ?></li>
                    </ul>
                </div>
            </div>

            <!-- Avec Wayo Column -->
            <div class="col-lg-6 col-md-12">
                <div class="comparison-card avec-wayo h-100 shadow rounded-4 p-4 p-md-5">
                    <div class="text-center">
                        <span class="badge-header badge-avec"><?php echo get_phrase("With Wayo"); ?></span>
                    </div>

                    <div class="image-placeholder">
                        <img src="uploads/images/decloedt/home/with-wayo.png" alt="With Wayo" loading="lazy" />
                    </div>

                    <ul class="feature-list">
                        <li><?php echo get_phrase('Single dashboard for courses and payments.'); ?></li>
                        <li><?php echo get_phrase('Centralized data and clear reporting.'); ?></li>
                        <li><?php echo get_phrase('Automation of tracking and communication.'); ?></li>
                    </ul>
                </div>
            </div>
      </div>
      <!-- <div class="pricing-table-container" <?php echo (get_user_language() === 'arabic') ? 'dir="rtl"' : 'dir="ltr"'; ?>>
        
      </div> -->
    </div>
  </section>
  <!-------->
  <!-- Choose your plan -->
  <section class="position-relative pricing">
    <div class="container position-relative unified-title-container" style="z-index: 1;">
      <h2><?php echo get_phrase("A unique offer for maximum impact") ?></h2>
      <p class="subtitle"><?php echo get_phrase("Everything you need to build, manage, and monetize your community from A to Z.") ?></p>
      <div class="pricing-table-container" <?php echo (get_user_language() === 'arabic') ? 'dir="rtl"' : 'dir="ltr"'; ?>>
        
        <div class="split-plan-container ">
              <div class="plan-details-split">
                  <span class="plan-kicker"><?php echo get_phrase("Mentor Plan"); ?></span>
                  <h3><?php echo get_phrase("The Complete Offer"); ?></h3>
                  <p class="plan-description"><?php echo get_phrase("Access our entire platform without any limits. A unique solution to let you focus on what truly matters: sharing your knowledge."); ?></p>
                  <div class="plan-price-split">
                      <span class="price-value"></span>
                      <div class="price-details">
                          <span class="price-currency"></span>
                          <span class="price-period"><?php echo str_replace('/', '/ ', get_phrase("per month, no commitment")); ?></span>
                      </div>
                  </div>
                    <?php if ($this->session->userdata('user_id')) : ?>
                        <!-- utilisateur connecté -->
                        <a href="<?php echo route('dashboard'); ?>" class="btn accent plan-cta-split"><?php echo get_phrase("Start and build my community") ?></a>
                    <?php else : ?>
                        <!-- utilisateur non connecté -->
                          <a href="<?php echo site_url('admission/online_admission'); ?>" class="btn accent plan-cta-split"><?php echo get_phrase("Start and build my community") ?></a>
                    <?php endif; ?>
                  <p class="plan-guarantee">✔<?php echo get_phrase("Enjoy 14 days of free trial before paying."); ?></p>
              </div>
              <div class="plan-features-split">
                  <h4><?php echo get_phrase("Included in your plan:"); ?></h4>
                  <ul>
                      <li>
                          <div class="feature-icon money"><svg viewBox="0 0 24 24" width="22" height="22"><path fill="currentColor" d="M3 6h18v12H3z" opacity=".15"/><path fill="currentColor" d="M2 5h20v14H2zM5 9a3 3 0 0 0-3-3v12a3 3 0 0 0 3-3h14a3 3 0 0 0 3 3V6a3 3 0 0 0-3 3zM12 9a3 3 0 1 1 0 6 3 3 0 0 1 0-6Z"/></svg></div>
                          <div class="feature-text"><h5><?php echo get_phrase("Full monetization"); ?></h5><p><?php echo get_phrase("Sell your courses, subscriptions, and live sessions without restriction."); ?></p></div>
                      </li>
                      <li>
                          <div class="feature-icon users"><svg viewBox="0 0 24 24" width="22" height="22"><path fill="currentColor" d="M16 11a4 4 0 1 0-8 0 4 4 0 0 0 8 0Z" opacity=".2"/><path fill="currentColor" d="M12 13a5 5 0 1 1 5-5 5.006 5.006 0 0 1-5 5Zm0 2c-4.418 0-8 2.239-8 5v2h16v-2c0-2.761-3.582-5-8-5Z"/></svg></div>
                          <div class="feature-text"><h5><?php echo get_phrase("Unlimited community"); ?></h5><p><?php echo get_phrase("Welcome as many members as you want."); ?></p></div>

                      </li>
                      <li>
                          <div class="feature-icon course"><svg viewBox="0 0 24 24" width="22" height="22"><path fill="currentColor" d="M4 6h16v12H4z" opacity=".2"/><path fill="currentColor" d="M3 5h18v14H3zM6 9h8v2H6zm0 4h12v2H6z"/></svg></div>
                          <div class="feature-text"><h5><?php echo get_phrase("Unlimited educational content"); ?></h5><p><?php echo get_phrase("Create an infinite number of courses, modules, and quizzes."); ?></p></div>

                      </li>
                       <li>
                          <div class="feature-icon social"><svg viewBox="0 0 24 24" width="22" height="22"><path fill="currentColor" d="M2 4h20v12H6l-4 4zM6 8h12v2H6zm0 4h8v2H6z" opacity=".4"/><path fill="currentColor" d="M2 4h20v12H6l-4 4zM6 8h12v2H6zm0 4h8v2H6z"/></svg></div>
                          <div class="feature-text"><h5><?php echo get_phrase("Engagement tools"); ?></h5><p><?php echo get_phrase("Access to the Social Hub, chat, calendar, and events."); ?></p></div>
                      </li>
                  </ul>
              </div>
          </div>
      </div>
    </div>
  </section>
  <!-- Why choose Wayo Academy -->
  <!-- <section class="features">
    <div class="container">
      <h2 class="social-media-main-text" dir="rtl">
        <?php echo get_phrase("Why choose") . "&lrm;"; ?>
        <span style="color: #FC7B30;"><?php echo get_phrase("Wayo Academy ?") . "&lrm;"; ?></span>
      </h2>
      <div class="features-grid">
        <div class="feature-item">
          <img src="uploads/images/decloedt/home/icons/speedometer.webp" alt="Speedometer icon representing self-paced learning" height="80" width="80" style="margin-bottom: 2rem;">
          <h3><?php echo get_phrase("Guide at Your Own Pace") ?></h3>
          <p><?php echo get_phrase("Support your members without pressure, according to your availability and teaching style") ?></p>
        </div>
        <div class="feature-item">
          <img src="uploads/images/decloedt/home/icons/target.webp" alt="rythm" height="80" width="80" style="margin-bottom: 2rem;">
          <h3><?php echo get_phrase("Practical projects to mentor") ?></h3>
          <p><?php echo get_phrase("Share your expertise through hands-on case studies for a real impact on members' professional lives") ?></p>
        </div>
        <div class="feature-item">
          <img src="uploads/images/decloedt/home/icons/skills.webp" alt="rythm" height="80" width="80" style="margin-bottom: 2rem;">
          <h3><?php echo get_phrase("Position Yourself as an Expert") ?></h3>
          <p><?php echo get_phrase("Highlight your experience by offering personalized advice to a community seeking meaning and skills") ?></p>
        </div>
        <div class="feature-item">
          <img src="uploads/images/decloedt/home/icons/connect_community.webp" alt="rythm" height="80" width="80" style="margin-bottom: 2rem;">
          <h3><?php echo get_phrase("Join an Engaged Community") ?></h3>
          <p><?php echo get_phrase("Connect with mentors and experts passionate about knowledge sharing, innovation, and digital transformation") ?></p>
        </div>
      </div>
    </div>
  </section> -->
  <!-- Flagship Courses -->
  <!-- <section class="courses">
    <div class="container">
      <h2><?php echo get_phrase("Flagship Courses") ?></h2>
      <div class="courses-carousel">
        <button class="carousel-btn prev" aria-label="Previous"><i class='fas fa-chevron-left'></i></button>
        <div class="carousel-track-container">
          <ul class="carousel-track">
            <li class="carousel-slide">
              <img src="uploads/images/decloedt/home/digital_marketing.webp" alt="Marketing" height="252" loading="lazy" />
              <h3><?php echo get_phrase("Digital Marketing") ?></h3>
              <p><?php echo get_phrase("Learn to create campaigns that convert and retain customers") ?></p>
              <a href="#" class="cta"><?php echo get_phrase("Learn More") ?></a>
            </li>
            <li class="carousel-slide">
              <img src="uploads/images/decloedt/home/web_dev.webp" alt="Web Dev" height="252" loading="lazy" />
              <h3><?php echo get_phrase("Web Development") ?></h3>
              <p><?php echo get_phrase("Master front-end and back-end technologies with real-world projects") ?></p>
              <a href="#" class="cta"><?php echo get_phrase("Learn More") ?></a>
            </li>
            <li class="carousel-slide">
              <img src="uploads/images/decloedt/home/Cybersecurity.webp" alt="Cybersecurity" height="252" loading="lazy" />
              <h3><?php echo get_phrase("Cybersecurity") ?></h3>
              <p><?php echo get_phrase("Protect information systems and explore white hat methods") ?></p>
              <a href="#" class="cta"><?php echo get_phrase("Learn More") ?></a>
            </li>
            <li class="carousel-slide">
              <img src="uploads/images/decloedt/home/AI_Data.webp" alt="IA & Data" height="202" style="margin-bottom: 3.5rem;" loading="lazy" />
              <h3><?php echo get_phrase("AI & Data") ?></h3>
              <p><?php echo get_phrase("Dive into data analysis and machine learning") ?></p>
              <a href="#" class="cta"><?php echo get_phrase("Learn More") ?></a>
            </li>
          </ul>
        </div>
        <button class="carousel-btn next" aria-label="Next"><i class='fas fa-chevron-right'></i></button>
      </div>
    </div>
  </section> -->
  <!-- Success Stories -->
  <!-- <section class="success-stories">
    <div class="container">
      <h2><?php echo get_phrase("Success Stories") ?></h2>
      <div class="stories-carousel">
        <button class="stories-btn prev" aria-label="Previous"><i class='fas fa-chevron-left'></i></button>
        <div class="stories-track-wrapper">
          <ul class="stories-track">
            <li class="story-slide">
              <div class="video-container">
                <video class="video-player" id="janeDoeVideo" loop muted loading="lazy">
                  <source src="Uploads/images/decloedt/home/videos/Jane_Doe_win.webm" type="video/webm">
                  <source src="Uploads/images/decloedt/home/videos/Jane_Doe_ios.mp4" type="video/mp4">
                  <?php echo get_phrase("Your browser does not support HTML5 video") ?>.
                </video>
              </div>
              <div class="story-info">
                <h3>Jane Doe</h3>
                <p><?php echo get_phrase("Junior") ?> → <?php echo get_phrase("Digital Project Manager in 6 months") ?></p>
              </div>
            </li>
            <li class="story-slide">
              <div class="video-container">
                <video class="video-player" loop muted loading="lazy">
                  <source src="uploads/images/decloedt/home/videos/John_Smith_win.webm" type="video/webm">
                  <source src="uploads/images/decloedt/home/videos/John_Smith_ios.mp4" type="video/mp4">
                  <?php echo get_phrase("Your browser does not support HTML5 video") ?>
                </video>
              </div>
              <div class="story-info">
                <h3>John Smith</h3>
                <p><?php echo get_phrase("Community Manager") ?> → <?php echo get_phrase("No-Code & Automation Expert") ?></p>
              </div>
            </li>
            <li class="story-slide">
              <div class="video-container">
                <video class="video-player" id="aliceMartinVideo" loop muted loading="lazy">
                  <source src="uploads/images/decloedt/home/videos/Alice_Martin_win.webm" type="video/webm">
                  <source src="uploads/images/decloedt/home/videos/Alice_Martin_ios.mp4" type="video/mp4">
                  <?php echo get_phrase("Your browser does not support HTML5 video") ?>
                </video>
              </div>
              <div class="story-info">
                <h3>Alice Martin</h3>
                <p><?php echo get_phrase("Analyst") ?> → <?php echo get_phrase("Recognized Data Scientist") ?></p>
              </div>
            </li>
          </ul>
        </div>
        <button class="stories-btn next" aria-label="next"><i class='fas fa-chevron-right'></i></button>
      </div>
      <div class="stories-cta">
        <a href="#" class="btn btn-outline"><?php echo get_phrase("View More Stories") ?></a>
      </div>
    </div>
  </section> -->
  <!-- What You Gain -->
  <!-- <section class="benefits" <?php echo (get_user_language() === 'arabic') ? 'dir="rtl"' : 'dir="ltr"'; ?>>
    <div class="container">
      <h2><?php echo get_phrase("What You Gain") ?></h2>
      <div class="benefits-grid">
        <div class="benefit-box">
          <img src="uploads/images/decloedt/home/icons/member.webp" alt="Learner icon representing benefits for students" height="90" width="90" style="margin-bottom: 1.1rem;" loading="lazy">
          <h3><?php echo get_phrase("for members") ?></h3>
          <ul>
            <li>24/7 <?php echo get_phrase("access to premium resources") ?></li>
            <li><?php echo get_phrase("Real-world projects and detailed tutorials") ?></li>
            <li><?php echo get_phrase("Personalized feedback from expert mentors") ?></li>
            <li><?php echo get_phrase("Recognized certificate upon completion") ?></li>
          </ul>
        </div>
        <div class="benefit-box">
          <img src="uploads/images/decloedt/home/icons/mentor.webp" alt="rythm" height="90" width="90" style="margin-bottom: 1.1rem;" loading="lazy">
          <h3><?php echo get_phrase("For Mentors") ?></h3>
          <ul>
            <li><?php echo get_phrase("Intuitive dashboard") ?></li>
            <li><?php echo get_phrase("Simplified scheduling") ?></li>
            <li><?php echo get_phrase("Interactive live sessions") ?></li>
            <li><?php echo get_phrase("Ability to create and manage your own courses") ?></li>
          </ul>
        </div>
      </div>
    </div>
  </section> -->
  <!-- Mobile section -->

  <!-- Meet Our Mentors -->
  <section class="meet-mentors">
    <div class="container unified-title-container">
      <h2><?php echo get_phrase("Meet Our Mentors") ?></h2>
      <p class="subtitle"><?php echo get_phrase("Passionate professionals to guide you") ?></p>
      <div class="mentors-grid">
        <div class="mentor-card">
          <img src="uploads/images/decloedt/home/mentor01-home.png" alt="Mentor 2" loading="lazy" />
          <h3><?php echo get_phrase("Youssef_El_Omrani") ?></h3>
          <p class="specialty"><?php echo get_phrase("Artificial_Intelligence_and_Data_Analysis") ?></p>
          <a href="#" class="btn btn-mentors"><?php echo get_phrase("Learn More") ?></a>
        </div>
        <div class="mentor-card">
          <img src="uploads/images/decloedt/home/mentor02-home.png" alt="Mentor 1" loading="lazy" />
          <h3><?php echo get_phrase("Salma_Benkacem") ?></h3>
          <p class="specialty"><?php echo get_phrase("Cybersecurity_and_Systems_Protection") ?></p>
          <a href="#" class="btn btn-mentors"><?php echo get_phrase("Learn More") ?></a>
        </div>
        <div class="mentor-card">
          <img src="uploads/images/decloedt/home/mentor03-home.png" alt="Mentor 4" loading="lazy" />
          <h3><?php echo get_phrase("Hamza_Aït_Lahcen") ?></h3>
          <p class="specialty"><?php echo get_phrase("Web_and_Application_Development") ?></p>
          <a href="#" class="btn btn-mentors"><?php echo get_phrase("Learn More") ?></a>
        </div>
        <div class="mentor-card">
          <img src="uploads/images/decloedt/home/logo-hwe.png" alt="Mentor 2" loading="lazy" />
          <h3><?php echo get_phrase("Moscaling_academy") ?></h3>
          <p class="specialty"><?php echo get_phrase("AI_&_Data") ?></p>
          <a href="#" class="btn btn-mentors"><?php echo get_phrase("Learn More") ?></a>
        </div>
      </div>
    </div>
  </section>

      <section class="app-coming-soon" <?php echo (get_user_language() === 'arabic') ? 'dir="rtl"' : 'dir="ltr"'; ?>>
      <div class="app-frame">
        <div class="app-inner">
          <div class="app-media"><img src="https://i.postimg.cc/Z5nbcDC6/app.png" alt="Wayo app mockup"/></div>
          <div class="app-copy">
            <p class="app-kicker"><?php echo get_phrase("EASIER, FASTER AND MORE ACCESSIBLE MENTORING"); ?></p>
            <p class="app-kicker"><?php echo get_phrase("Take_control_of_your_community_and_stay_close_to_your_members_anytime,_mentor-cardanywhere."); ?></p>
            <h2 class="app-title"><?php echo get_phrase("Coming soon "); ?>!</h2>
            <div class="stores">
              <a href="#" class="store"><img src="https://i.postimg.cc/QFpGF7SJ/app-store-badge.png" alt="App Store"/></a>
              <a href="#" class="store"><img src="https://i.postimg.cc/CBxWrQ0c/google-play-badge.png" alt="Google Play"/></a>
            </div>
          </div>
        </div>
      </div>
    </section>
  <!------------------------------------------------------------------------------------------------>
</main>
<!-- ========== END MAIN ========== -->
<script>
  window.translations = {
    month: '<?php echo get_phrase("month"); ?>'
  };
</script>