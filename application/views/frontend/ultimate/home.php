<!-- ========== MAIN ========== -->
<main class="main-content" id="content" role="main">

<!-- Hero Section avec Bootstrap Grid -->
<section id="hero" class="hero">
  <span class="hero-glow a"></span><span class="hero-glow b"></span>
  <div class="container-md">
    <div class="row align-items-center g-4">

      <!-- Colonne Gauche: Texte -->
      <div class="col-lg-6 col-xl-6">
        <div class="hero-copy">
          <h1><span class="accent"><?php echo get_phrase("Monetize") ?></span> <?php echo get_phrase("your_community_with_peace_of_mind.") ?></h1>
          <p class="sub"><?php echo get_phrase("We_specialize_in_secure_payment_platforms_that_don’t_freeze_mentors’_accounts._Build,_engage,_and_grow_your_revenue_without_limitations.") ?></p>
          <div class="hero-ctas">
            <a href="<?php echo site_url('admission/online_admission_student'); ?>" class="btn accent"><?php echo get_phrase("I'm a member") ?></a>
            <a href="<?php echo site_url('admission/online_admission'); ?>" class="btn outline"><?php echo get_phrase("I'm a Mentor") ?></a>
          </div>

          <div class="chips">
            <div class="chip pill">
              <span class="chip-icon money"><svg viewBox="0 0 24 24" width="18" height="18"><path fill="currentColor" d="M3 6h18v12H3z" opacity=".15"/><path fill="currentColor" d="M2 5h20v14H2zM5 9a3 3 0 0 0-3-3v12a3 3 0 0 0 3-3h14a3 3 0 0 0 3 3V6a3 3 0 0 0-3 3zM12 9a3 3 0 1 1 0 6 3 3 0 0 1 0-6Z"/></svg></span>
              <strong><?php echo get_phrase("Monetize_your_expertise") ?></strong>
            </div>
            <div class="chip pill">
              <span class="chip-icon users"><svg viewBox="0 0 24 24" width="18" height="18"><path fill="currentColor" d="M16 11a4 4 0 1 0-8 0 4 4 0 0 0 8 0Z" opacity=".2"/><path fill="currentColor" d="M12 13a5 5 0 1 1 5-5 5.006 5.006 0 0 1-5 5Zm0 2c-4.418 0-8 2.239-8 5v2h16v-2c0-2.761-3.582-5-8-5Z"/></svg></span>
              <strong><?php echo get_phrase("Create_communities") ?></strong>
            </div>
            <div class="chip pill">
              <span class="chip-icon course"><svg viewBox="0 0 24 24" width="18" height="18"><path fill="currentColor" d="M4 6h16v12H4z" opacity=".2"/><path fill="currentColor" d="M3 5h18v14H3zM6 9h8v2H6zm0 4h12v2H6z"/></svg></span>
              <strong><?php echo get_phrase("Courses_&_live") ?></strong>
            </div>
            <div class="chip pill">
              <span class="chip-icon social"><svg viewBox="0 0 24 24" width="18" height="18"><path fill="currentColor" d="M4 5h16v10H4z" opacity=".2"/><path fill="currentColor" d="M2 4h20v12H6l-4 4zM6 8h12v2H6zm0 4h8v2H6z"/></svg></span>
              <strong><?php echo get_phrase("Built-in_social") ?></strong>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-1 col-xl-1"></div>

      <!-- Colonne Droite: Vidéo locale -->
      <div class="col-lg-5 col-xl-5">
        <figure class="yt-card" aria-label="Vidéo de présentation">
          <div class="ratio ratio-16x9 yt-desktop">
            <video autoplay muted loop playsinline controls>
              <source src="<?php echo base_url('uploads/videos/v2_Wayo_Academy_Promo_Video.mp4'); ?>" type="video/mp4">
              Votre navigateur ne supporte pas la lecture vidéo.
            </video>
          </div>
          <div class="ratio ratio-9x16 yt-mobile">
            <video autoplay muted loop playsinline controls>
              <source src="<?php echo base_url('uploads/videos/v2_Wayo_Academy_Promo_Video.mp4'); ?>" type="video/mp4">
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
                        <li data-feature="social" tabindex="0"><?php echo get_phrase("Social") ?></li>
                        <li data-feature="quiz" tabindex="0"><?php echo get_phrase("Quiz") ?></li>
                    </ul>
                    <div class="feature-progress">
                        <div class="feature-progress-bar"></div>
                    </div>

                    <div id="bbb" class="feature-pane active">
                        <img src="uploads/images/decloedt/home/online_course.webp" alt="Online Course" loading="lazy"/>
                    </div>
                    <div id="social" class="feature-pane">
                        <img src="uploads/images/decloedt/home/social.webp" alt="Social" loading="lazy"/>
                    </div>
                    <div id="quiz" class="feature-pane">
                        <img src="uploads/images/decloedt/home/quiz.webp" alt="Quiz" loading="lazy" />
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Choose your plan -->
  <section class="py-3 position-relative pricing" style="padding-bottom:75px !important">
    <div class="container position-relative" style="z-index: 1;">
        <h2 class="text-center mb-5 mt-5" style="font-size: 45px; font-weight: bold;"><?php echo get_phrase("Choose your plan") ?></h2>
        <div class="pricing-table-container" <?php echo (get_user_language() === 'arabic') ? 'dir="rtl"' : 'dir="ltr"'; ?>>
            <table class="pricing-table">
                <thead>
                    <tr>
                        <th></th>
                        <th><?php echo get_phrase("Free") ?></th>
                        <th><?php echo get_phrase("More") ?></th>
                        <th><?php echo get_phrase("Premium") ?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="feature"><?php echo get_phrase("members") ?></td>
                        <td data-plan="free">10</td>
                        <td data-plan="plus">75</td>
                        <td data-plan="premium"><?php echo get_phrase("unlimited") ?></td>
                    </tr>
                    <tr>
                        <td class="feature"><?php echo get_phrase("Daily Attendance") ?></td>
                        <td>✔</td>
                        <td>✔</td>
                        <td>✔</td>
                    </tr>
                    <tr>
                        <td class="feature"><?php echo get_phrase("Calendar & Events") ?></td>
                        <td>✔</td>
                        <td>✔</td>
                        <td>✔</td>
                    </tr>
                    <tr>
                        <td class="feature"><?php echo get_phrase("Social Hub") ?></td>
                        <td>✖</td>
                        <td>✔</td>
                        <td>✔</td>
                    </tr>
                    <tr>
                        <td class="feature"><?php echo get_phrase("Chat") ?></td>
                        <td>✔</td>
                        <td>✔</td>
                        <td>✔</td>
                    </tr>
                    <tr>
                        <td class="feature"><?php echo get_phrase("Courses") ?>*</td>
                        <td data-plan="free">1</td>
                        <td data-plan="plus">5</td>
                        <td data-plan="premium"><?php echo get_phrase("unlimited") ?></td>
                    </tr>
                    <tr>
                        <td class="feature"><?php echo get_phrase("Live Classes") ?></td>
                        <td>✖</td>
                        <td>✔</td>
                        <td>✔</td>
                    </tr>
                    <tr>
                        <td class="feature"><?php echo get_phrase("Exams") ?></td>
                        <td>✖</td>
                        <td>✔</td>
                        <td>✔</td>
                    </tr>
                    <tr>
                        <td class="feature"><?php echo get_phrase("Assessments & Progress Tracking") ?></td>
                        <td>✔</td>
                        <td>✔</td>
                        <td>✔</td>
                    </tr>
                    <tr>
                        <td class="feature"><?php echo get_phrase("Additional mentors") ?></td>
                        <td data-plan="free">0</td>
                        <td data-plan="plus">2</td>
                        <td data-plan="premium"><?php echo get_phrase("unlimited") ?></td>
                    </tr>
                    <tr>
                        <td class="feature"><?php echo get_phrase("Automated accounting") ?></td>
                        <td>✖</td>
                        <td>✔</td>
                        <td>✔</td>
                    </tr>
                    <tr class="price-row">
                        <td class="feature"><?php echo get_phrase("Price") ?></td>
                        <td><?php echo get_phrase("free") ?></td>
                        <td data-price="69" data-currency="EUR"><?php echo get_phrase("month") ?></td>
                        <td data-price="79" data-currency="EUR"><?php echo get_phrase("month") ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</section>
    <!-- Why choose Wayo Academy -->
  <section class="features">
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
  </section>
  <!-- Flagship Courses -->
  <section class="courses">
    <div class="container">
      <h2><?php echo get_phrase("Flagship Courses") ?></h2>
      <div class="courses-carousel">
        <button class="carousel-btn prev" aria-label="Previous"><i class='fas fa-chevron-left'></i></button>
        <div class="carousel-track-container">
          <ul class="carousel-track">
            <li class="carousel-slide">
              <img src="uploads/images/decloedt/home/digital_marketing.webp" alt="Marketing" height="252" loading="lazy"/>
              <h3><?php echo get_phrase("Digital Marketing") ?></h3>
              <p><?php echo get_phrase("Learn to create campaigns that convert and retain customers") ?></p>
              <a href="#" class="cta"><?php echo get_phrase("Learn More") ?></a>
            </li>
            <li class="carousel-slide">
              <img src="uploads/images/decloedt/home/web_dev.webp" alt="Web Dev" height="252" loading="lazy"/>
              <h3><?php echo get_phrase("Web Development") ?></h3>
              <p><?php echo get_phrase("Master front-end and back-end technologies with real-world projects") ?></p>
              <a href="#" class="cta"><?php echo get_phrase("Learn More") ?></a>
            </li>
            <li class="carousel-slide">
              <img src="uploads/images/decloedt/home/Cybersecurity.webp" alt="Cybersecurity" height="252" loading="lazy"/>
              <h3><?php echo get_phrase("Cybersecurity") ?></h3>
              <p><?php echo get_phrase("Protect information systems and explore white hat methods") ?></p>
              <a href="#" class="cta"><?php echo get_phrase("Learn More") ?></a>
            </li>
            <li class="carousel-slide">
              <img src="uploads/images/decloedt/home/AI_Data.webp" alt="IA & Data" height="202" style="margin-bottom: 3.5rem;" loading="lazy"/>
              <h3><?php echo get_phrase("AI & Data") ?></h3>
              <p><?php echo get_phrase("Dive into data analysis and machine learning") ?></p>
              <a href="#" class="cta"><?php echo get_phrase("Learn More") ?></a>
            </li>
          </ul>
        </div>
        <button class="carousel-btn next" aria-label="Next"><i class='fas fa-chevron-right'></i></button>
      </div>
    </div>
  </section>
  <!-- Success Stories -->
    <section class="success-stories">
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
    </section>
    <!-- What You Gain -->
    <section class="benefits" <?php echo (get_user_language() === 'arabic') ? 'dir="rtl"' : 'dir="ltr"'; ?>>
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
    </section>
    <!-- Mobile section -->
    <section class="bg-white">
        <div class="app-coming-soon">
      <div class="app-container">
        <svg class="decor-circle" viewBox="0 0 400 400">
          <circle cx="200" cy="200" r="200" fill="rgba(255,255,255,0.15)" />
        </svg>
        <div class="app-image">
          <img src="uploads/images/decloedt/home/bg_download.webp" alt="Wayo Academy App Mockup" loading="lazy"/>
        </div>
        <div class="app-content" <?php echo (get_user_language() === 'arabic') ? 'dir="rtl"' : 'dir="ltr"'; ?>>
          <p class="app-subtitle"><?php echo get_phrase("Easier, faster and more accessible mentoring") ?></p>
          <h2 class="app-title"><?php echo get_phrase("Coming Soon !") ?></h2>
          <div class="app-buttons">
            <a href="#" class="app-btn">
              <img src="uploads/images/decloedt/logo/app-storec-img.webp" alt="Download on the App Store" loading="lazy"/>
            </a>
            <a href="#" class="app-btn">
              <img src="uploads/images/decloedt/logo/play-store-img.webp" alt="Get it on Google Play" loading="lazy"/>
            </a>
          </div>
        </div>
      </div>
      </div>
    </section>
    <!-- Meet Our Mentors -->
    <section class="meet-mentors">
      <div class="container">
        <h2><?php echo get_phrase("Meet Our Mentors") ?></h2>
        <p class="subtitle"><?php echo get_phrase("Passionate professionals to guide you") ?></p>
        <div class="mentors-grid">
          <div class="mentor-card">
            <img src="uploads/images/decloedt/home/mentor_3.webp" alt="Mentor 2" loading="lazy"/>
            <h3>Emma Dubois</h3>
            <p class="specialty"><?php echo get_phrase("Digital Marketing") ?></p>
            <a href="#" class="btn btn-mentors"><?php echo get_phrase("Learn More") ?></a>
          </div>
          <div class="mentor-card">
            <img src="uploads/images/decloedt/home/mentor_1.webp" alt="Mentor 1" loading="lazy"/>
            <h3>Lucas Martin</h3>
            <p class="specialty"><?php echo get_phrase("Web Development") ?></p>
            <a href="#" class="btn btn-mentors"><?php echo get_phrase("Learn More") ?></a>
          </div>
          <div class="mentor-card">
            <img src="uploads/images/decloedt/home/mentor_4.webp" alt="Mentor 4" loading="lazy"/>
            <h3>Sophie Leroy</h3>
            <p class="specialty"><?php echo get_phrase("Cybersecurity") ?></p>
            <a href="#" class="btn btn-mentors"><?php echo get_phrase("Learn More") ?></a>
          </div>
          <div class="mentor-card">
            <img src="uploads/images/decloedt/home/mentor_2.webp" alt="Mentor 2" loading="lazy"/>
            <h3>Antoine Petit</h3>
            <p class="specialty"><?php echo get_phrase("AI & Data") ?></p>
            <a href="#" class="btn btn-mentors"><?php echo get_phrase("Learn More") ?></a>
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