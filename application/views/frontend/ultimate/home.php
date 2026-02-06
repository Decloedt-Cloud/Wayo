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
                <span class="chip-icon money"><i class="fas fa-sack-dollar"></i></span>
                <strong><?php echo get_phrase("Monetize_your_expertise") ?></strong>
              </div>
              <div class="chip pill">
                <span class="chip-icon users"><i class="fas fa-users"></i></span>
                <strong><?php echo get_phrase("Create_communities_a") ?></strong>
              </div>
              <div class="chip pill">
                <span class="chip-icon course"><i class="fas fa-graduation-cap"></i></span>
                <strong><?php echo get_phrase("Courses_&_live") ?></strong>
              </div>
              <div class="chip pill">
                <span class="chip-icon social"><i class="fas fa-comments"></i></span>
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
                $video_mp4 = 'promo_fr.mp4';
                $video_mp4_mobile = 'promo_fr_mobile.mp4';
                $video_poster = 'poster_fr.webp';
            } elseif ($user_lang === 'arabic') {
                $video_mp4 = 'promo_ar.mp4';
                $video_mp4_mobile = 'promo_ar_mobile.mp4';
                $video_poster = 'poster_ar.webp';
            } else {
                $video_mp4 = 'promo_en.mp4';
                $video_mp4_mobile = 'promo_en_mobile.mp4';
                $video_poster = 'poster_en.webp';
            }
            $poster_url = base_url('uploads/videos/posters/' . $video_poster);
            $mp4_url = base_url('uploads/videos/optimized/' . $video_mp4);
            $mp4_mobile_url = base_url('uploads/videos/optimized/' . $video_mp4_mobile);
            ?>
            <!-- Plyr Video Player -->
            <div class="plyr-container yt-desktop">
              <video 
                id="promo-video-desktop"
                class="lazy-video plyr-video" 
                autoplay
                muted 
                loop 
                playsinline 
                preload="auto" 
                poster="<?php echo $poster_url; ?>"
                data-src-mp4="<?php echo $mp4_url; ?>"
                data-src-mp4-mobile="<?php echo $mp4_mobile_url; ?>">
              </video>
            </div>
            <div class="plyr-container yt-mobile">
              <video 
                id="promo-video-mobile"
                class="lazy-video plyr-video" 
                autoplay
                muted 
                loop 
                playsinline 
                preload="auto" 
                poster="<?php echo $poster_url; ?>"
                data-src-mp4="<?php echo $mp4_url; ?>"
                data-src-mp4-mobile="<?php echo $mp4_mobile_url; ?>">
              </video>
            </div>
          </figure>
        </div>
      </div>
    </div>
  </section>
  </div>
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
                          <div class="feature-icon money"><i class="fas fa-sack-dollar"></i></div>
                          <div class="feature-text"><h5><?php echo get_phrase("Full monetization"); ?></h5><p><?php echo get_phrase("Sell your courses, subscriptions, and live sessions without restriction."); ?></p></div>
                      </li>
                      <li>
                          <div class="feature-icon users"><i class="fas fa-users"></i></div>
                          <div class="feature-text"><h5><?php echo get_phrase("Unlimited community"); ?></h5><p><?php echo get_phrase("Welcome as many members as you want."); ?></p></div>
                      </li>
                      <li>
                          <div class="feature-icon course"><i class="fas fa-graduation-cap"></i></div>
                          <div class="feature-text"><h5><?php echo get_phrase("Unlimited educational content"); ?></h5><p><?php echo get_phrase("Create an infinite number of courses, modules, and quizzes."); ?></p></div>
                      </li>
                      <li>
                          <div class="feature-icon social"><i class="fas fa-comments"></i></div>
                          <div class="feature-text"><h5><?php echo get_phrase("Engagement tools"); ?></h5><p><?php echo get_phrase("Access to the Social Hub, chat, calendar, and events."); ?></p></div>
                      </li>
                  </ul>
              </div>
          </div>
      </div>
    </div>
  </section>
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
            <picture>
              <source 
                type="image/avif"
                srcset="<?php echo base_url('uploads/images/decloedt/home/optimized/online_course.avif'); ?>"
                media="(min-width: 769px)" />
              <source 
                type="image/webp"
                srcset="<?php echo base_url('uploads/images/decloedt/home/optimized/online_course-mobile.webp'); ?>"
                media="(max-width: 768px)" />
              <source 
                type="image/webp"
                srcset="<?php echo base_url('uploads/images/decloedt/home/optimized/online_course.webp'); ?>" />
              <img 
                src="<?php echo base_url('uploads/images/decloedt/home/optimized/online_course.webp'); ?>" 
                alt="<?php echo get_phrase('Online Course'); ?>" 
                width="2418" 
                height="1600"
                loading="lazy" />
            </picture>
          </div>
          <div id="social" class="feature-pane">
            <picture>
              <source 
                type="image/avif"
                srcset="<?php echo base_url('uploads/images/decloedt/home/optimized/Ai.avif'); ?>"
                media="(min-width: 769px)" />
              <source 
                type="image/webp"
                srcset="<?php echo base_url('uploads/images/decloedt/home/optimized/Ai-mobile.webp'); ?>"
                media="(max-width: 768px)" />
              <source 
                type="image/webp"
                srcset="<?php echo base_url('uploads/images/decloedt/home/optimized/Ai.webp'); ?>" />
              <img 
                src="<?php echo base_url('uploads/images/decloedt/home/optimized/Ai.webp'); ?>" 
                alt="<?php echo get_phrase('Ai'); ?>" 
                width="800" 
                height="519"
                loading="lazy" />
            </picture>
          </div>
          <div id="quiz" class="feature-pane">
            <picture>
              <source 
                type="image/avif"
                srcset="<?php echo base_url('uploads/images/decloedt/home/optimized/quiz_recent.avif'); ?>"
                media="(min-width: 769px)" />
              <source 
                type="image/webp"
                srcset="<?php echo base_url('uploads/images/decloedt/home/optimized/quiz_recent-mobile.webp'); ?>"
                media="(max-width: 768px)" />
              <source 
                type="image/webp"
                srcset="<?php echo base_url('uploads/images/decloedt/home/optimized/quiz_recent.webp'); ?>" />
              <img 
                src="<?php echo base_url('uploads/images/decloedt/home/optimized/quiz_recent.webp'); ?>" 
                alt="<?php echo get_phrase('Quiz'); ?>" 
                width="862" 
                height="580"
                loading="lazy" />
            </picture>
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
                        <picture>
                          <source 
                            type="image/avif"
                            srcset="<?php echo base_url('uploads/images/decloedt/home/optimized/without-wayo.avif'); ?>"
                            media="(min-width: 769px)" />
                          <source 
                            type="image/webp"
                            srcset="<?php echo base_url('uploads/images/decloedt/home/optimized/without-wayo-mobile.webp'); ?>"
                            media="(max-width: 768px)" />
                          <source 
                            type="image/webp"
                            srcset="<?php echo base_url('uploads/images/decloedt/home/optimized/without-wayo.webp'); ?>" />
                          <img 
                            src="<?php echo base_url('uploads/images/decloedt/home/optimized/without-wayo.webp'); ?>" 
                            alt="<?php echo get_phrase('Without Wayo'); ?>" 
                            width="1006" 
                            height="558"
                            loading="lazy" />
                        </picture>
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
                        <picture>
                          <source 
                            type="image/avif"
                            srcset="<?php echo base_url('uploads/images/decloedt/home/optimized/with-wayo.avif'); ?>"
                            media="(min-width: 769px)" />
                          <source 
                            type="image/webp"
                            srcset="<?php echo base_url('uploads/images/decloedt/home/optimized/with-wayo-mobile.webp'); ?>"
                            media="(max-width: 768px)" />
                          <source 
                            type="image/webp"
                            srcset="<?php echo base_url('uploads/images/decloedt/home/optimized/with-wayo.webp'); ?>" />
                          <img 
                            src="<?php echo base_url('uploads/images/decloedt/home/optimized/with-wayo.webp'); ?>" 
                            alt="<?php echo get_phrase('With Wayo'); ?>" 
                            width="983" 
                            height="571"
                            loading="lazy" />
                        </picture>
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
          <picture>
            <source 
              type="image/avif"
              srcset="<?php echo base_url('uploads/images/decloedt/home/optimized/mentor01-home.avif'); ?>"
              media="(min-width: 769px)" />
            <source 
              type="image/webp"
              srcset="<?php echo base_url('uploads/images/decloedt/home/optimized/mentor01-home-mobile.webp'); ?>"
              media="(max-width: 768px)" />
            <source 
              type="image/webp"
              srcset="<?php echo base_url('uploads/images/decloedt/home/optimized/mentor01-home.webp'); ?>" />
            <img 
              src="<?php echo base_url('uploads/images/decloedt/home/optimized/mentor01-home.webp'); ?>" 
              alt="<?php echo get_phrase('Youssef_El_Omrani'); ?>" 
              width="1024" 
              height="1024"
              loading="lazy" />
          </picture>
          <h3><?php echo get_phrase("Youssef_El_Omrani") ?></h3>
          <p class="specialty"><?php echo get_phrase("Artificial_Intelligence_and_Data_Analysis") ?></p>
          <a href="#" class="btn btn-mentors"><?php echo get_phrase("Learn More") ?></a>
        </div>
        <div class="mentor-card">
          <picture>
            <source 
              type="image/avif"
              srcset="<?php echo base_url('uploads/images/decloedt/home/optimized/mentor02-home.avif'); ?>"
              media="(min-width: 769px)" />
            <source 
              type="image/webp"
              srcset="<?php echo base_url('uploads/images/decloedt/home/optimized/mentor02-home-mobile.webp'); ?>"
              media="(max-width: 768px)" />
            <source 
              type="image/webp"
              srcset="<?php echo base_url('uploads/images/decloedt/home/optimized/mentor02-home.webp'); ?>" />
            <img 
              src="<?php echo base_url('uploads/images/decloedt/home/optimized/mentor02-home.webp'); ?>" 
              alt="<?php echo get_phrase('Salma_Benkacem'); ?>" 
              width="1024" 
              height="1024"
              loading="lazy" />
          </picture>
          <h3><?php echo get_phrase("Salma_Benkacem") ?></h3>
          <p class="specialty"><?php echo get_phrase("Cybersecurity_and_Systems_Protection") ?></p>
          <a href="#" class="btn btn-mentors"><?php echo get_phrase("Learn More") ?></a>
        </div>
        <div class="mentor-card">
          <picture>
            <source 
              type="image/avif"
              srcset="<?php echo base_url('uploads/images/decloedt/home/optimized/mentor03-home.avif'); ?>"
              media="(min-width: 769px)" />
            <source 
              type="image/webp"
              srcset="<?php echo base_url('uploads/images/decloedt/home/optimized/mentor03-home-mobile.webp'); ?>"
              media="(max-width: 768px)" />
            <source 
              type="image/webp"
              srcset="<?php echo base_url('uploads/images/decloedt/home/optimized/mentor03-home.webp'); ?>" />
            <img 
              src="<?php echo base_url('uploads/images/decloedt/home/optimized/mentor03-home.webp'); ?>" 
              alt="<?php echo get_phrase('Hamza_Aït_Lahcen'); ?>" 
              width="1024" 
              height="1024"
              loading="lazy" />
          </picture>
          <h3><?php echo get_phrase("Hamza_Aït_Lahcen") ?></h3>
          <p class="specialty"><?php echo get_phrase("Web_and_Application_Development") ?></p>
          <a href="#" class="btn btn-mentors"><?php echo get_phrase("Learn More") ?></a>
        </div>
        <div class="mentor-card">
          <picture>
            <source 
              type="image/avif"
              srcset="<?php echo base_url('uploads/images/decloedt/home/optimized/logo-hwe.avif'); ?>" />
            <source 
              type="image/webp"
              srcset="<?php echo base_url('uploads/images/decloedt/home/optimized/logo-hwe-mobile.webp'); ?>"
              media="(max-width: 768px)" />
            <source 
              type="image/webp"
              srcset="<?php echo base_url('uploads/images/decloedt/home/optimized/logo-hwe.webp'); ?>" />
            <img 
              src="<?php echo base_url('uploads/images/decloedt/home/optimized/logo-hwe.webp'); ?>" 
              alt="<?php echo get_phrase('Moscaling_academy'); ?>" 
              width="400" 
              height="400"
              loading="lazy" />
          </picture>
          <h3><?php echo get_phrase("Moscaling_academy") ?></h3>
          <p class="specialty"><?php echo get_phrase("AI_&_Data") ?></p>
          <a href="#" class="btn btn-mentors"><?php echo get_phrase("Learn More") ?></a>
        </div>
      </div>
    </div>
  </section>

      <!-- <section class="app-coming-soon" <?php echo (get_user_language() === 'arabic') ? 'dir="rtl"' : 'dir="ltr"'; ?>>
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
    </section> -->
  <!------------------------------------------------------------------------------------------------>
</main>
<!-- ========== END MAIN ========== -->

<link rel="stylesheet" href="<?php echo base_url('assets/frontend/ultimate/css/plyr/plyr.min.css'); ?>" />

<!-- Plyr Custom Theme (Wayo Academy colors) + Images responsives -->
<style>
  /* Images responsives - maintient le ratio */
  picture {
    display: block;
  }
  picture img {
    max-width: 100%;
    height: auto;
  }
  /* Feature pane - pas d'espace blanc */
  .feature-pane picture {
    display: flex;
    justify-content: center;
  }
  .feature-pane picture img {
    width: auto;
    max-width: 100%;
    max-height: 500px;
  }
  
  .plyr-container {
    --plyr-color-main: #FC7B30;
    --plyr-video-background: #000;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 10px 40px rgba(0,0,0,0.3);
  }
  .plyr {
    border-radius: 12px;
  }
  
  /* Grand bouton Play central - Centrage corrigé */
  .plyr__control--overlaid {
    background: rgba(252, 123, 48, 0.9);
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    border-radius: 12px;
  }
  .plyr__control--overlaid svg {
    left: 0 !important;
    top: 0 !important;
    position: relative !important;
    margin-left: 3px;
    transform: none !important;
  }
  .plyr__control--overlaid:hover {
    background: #FC7B30;
  }
  .plyr__control--overlaid:hover svg {
    left: 0 !important;
    top: 0 !important;
    transform: none !important;
  }
  
  /* Petit bouton Play dans les contrôles - Centrage corrigé */
  .plyr__controls .plyr__control {
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    background: transparent !important;
  }
  .plyr__controls .plyr__control:hover,
  .plyr__controls .plyr__control:focus,
  .plyr__controls .plyr__control.plyr__tab-focus {
    background: transparent !important;
  }
  .plyr__controls .plyr__control svg {
    position: relative !important;
    left: 0 !important;
    top: 0 !important;
    transform: none !important;
  }
  .plyr__controls [data-plyr="play"] svg {
    margin-left: 2px;
  }
  .plyr__controls .plyr__control:hover svg {
    left: 0 !important;
    top: 0 !important;
    transform: none !important;
  }
  

  .plyr__menu__container .plyr__control[role=menuitemradio][aria-checked=true]::before {
    background: #FC7B30;
  }
</style>

<script>
  window.translations = {
    month: '<?php echo get_phrase("month"); ?>'
  };
</script>

<!-- Polyfill pour passive event listeners (fix warnings Plyr) -->
<script>
(function() {
  if (typeof EventTarget !== 'undefined') {
    const originalAddEventListener = EventTarget.prototype.addEventListener;
    const passiveEvents = ['touchstart', 'touchmove', 'wheel', 'mousewheel'];
    
    EventTarget.prototype.addEventListener = function(type, listener, options) {
      let newOptions = options;
      if (passiveEvents.includes(type)) {
        if (typeof options === 'boolean') {
          newOptions = { capture: options, passive: true };
        } else if (typeof options === 'object' || options === undefined) {
          newOptions = { ...options, passive: options?.passive !== false };
        }
      }
      return originalAddEventListener.call(this, type, listener, newOptions);
    };
  }
})();
</script>
<script src="<?php echo base_url('assets/frontend/ultimate/js/plyr.polyfilled.min.js'); ?>"></script>

<script>
  // 🎬 Plyr Video Player - Autoplay au chargement + Pause quand hors écran
  document.addEventListener('DOMContentLoaded', function() {
    const videoElements = document.querySelectorAll('video.plyr-video');
    const plyrInstances = new Map();
    
    // 📱 Détection intelligente: utiliser la version mobile si nécessaire
    function shouldUseMobileVersion() {
      const isSmallScreen = window.innerWidth <= 768;
      const connection = navigator.connection || navigator.mozConnection || navigator.webkitConnection;
      const isSlowConnection = connection && (
        connection.saveData ||
        connection.effectiveType === 'slow-2g' ||
        connection.effectiveType === '2g' ||
        connection.effectiveType === '3g'
      );
      const isMobileDevice = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
      return isSmallScreen || isSlowConnection || isMobileDevice;
    }
    
    // 🎯 Initialiser Plyr et charger la vidéo
    function initPlyrVideo(video, autoplay = false) {
      if (video.dataset.loaded) return plyrInstances.get(video);
      
      const useMobile = shouldUseMobileVersion();
      const mp4Src = useMobile ? video.dataset.srcMp4Mobile : video.dataset.srcMp4;
      
      if (mp4Src) {
        // Ajouter la source
        const source = document.createElement('source');
        source.src = mp4Src;
        source.type = 'video/mp4';
        video.appendChild(source);
        
        // Initialiser Plyr avec autoplay muet
        const player = new Plyr(video, {
          controls: ['play-large', 'play', 'progress', 'current-time', 'mute', 'volume', 'fullscreen'],
          loop: { active: true },
          muted: true,
          autoplay: autoplay,
          clickToPlay: true,
          hideControls: true,
          resetOnEnd: true,
          keyboard: { focused: true, global: false },
          tooltips: { controls: true, seek: true },
          volume: 1,
          storage: { enabled: false },
          i18n: {
            play: '<?php echo get_phrase("Play"); ?>',
            pause: '<?php echo get_phrase("Pause"); ?>',
            mute: '<?php echo get_phrase("Mute"); ?>',
            unmute: '<?php echo get_phrase("Unmute"); ?>',
            enterFullscreen: '<?php echo get_phrase("Enter fullscreen"); ?>',
            exitFullscreen: '<?php echo get_phrase("Exit fullscreen"); ?>',
            currentTime: '<?php echo get_phrase("Current time"); ?>',
            duration: '<?php echo get_phrase("Duration"); ?>',
            volume: '<?php echo get_phrase("Volume"); ?>'
          }
        });
        
        // 🔊 Fix robuste pour unmute
        player.on('ready', function() {
          // S'assurer que la vidéo démarre en muet
          player.muted = true;
          video.muted = true;
          
          // Créer un overlay "Cliquer pour le son"
          const container = player.elements.container;
          const soundOverlay = document.createElement('div');
          soundOverlay.className = 'plyr-sound-overlay';
          soundOverlay.innerHTML = '<span class="sound-icon">🔇</span><span class="sound-text"><?php echo get_phrase("Click for sound"); ?></span>';
          soundOverlay.style.cssText = 'position:absolute;bottom:60px;right:10px;background:rgba(0,0,0,0.7);color:#fff;padding:8px 12px;border-radius:20px;cursor:pointer;z-index:10;display:flex;align-items:center;gap:6px;font-size:12px;transition:all 0.3s;';
          container.style.position = 'relative';
          container.appendChild(soundOverlay);
          
          // Fonction pour activer le son
          function enableSound() {
            player.muted = false;
            video.muted = false;
            player.volume = 1;
            soundOverlay.style.display = 'none';
          }
          
          // Fonction pour toggle play/pause
          function togglePlayPause() {
            if (player.playing) {
              player.pause();
            } else {
              player.play();
            }
          }
          
          // Clic sur l'overlay (son)
          soundOverlay.addEventListener('click', function(e) {
            if (e.cancelable) e.preventDefault();
            e.stopPropagation();
            enableSound();
          });
          
          // Support tactile pour l'overlay (mobile)
          soundOverlay.addEventListener('touchend', function(e) {
            if (e.cancelable) e.preventDefault();
            e.stopPropagation();
            enableSound();
          }, { passive: false });
          
          // 📱 Support mobile: tap sur la vidéo pour play/pause
          let lastTap = 0;
          container.addEventListener('touchend', function(e) {
            // Ignorer si on touche les contrôles
            if (e.target.closest('.plyr__controls') || e.target.closest('.plyr-sound-overlay')) {
              return;
            }
            
            const currentTime = new Date().getTime();
            const tapLength = currentTime - lastTap;
            
            if (tapLength < 300 && tapLength > 0) {
              // Double tap → activer le son
              if (e.cancelable) e.preventDefault();
              if (player.muted) {
                enableSound();
              }
            } else {
              // Simple tap → play/pause
              if (e.cancelable) e.preventDefault();
              togglePlayPause();
            }
            lastTap = currentTime;
          }, { passive: false });
          
          // Cacher l'overlay si l'utilisateur utilise le bouton mute de Plyr
          player.on('volumechange', function() {
            if (!player.muted) {
              soundOverlay.style.display = 'none';
            } else {
              soundOverlay.style.display = 'flex';
            }
          });
        });
        
        video.dataset.loaded = 'true';
        plyrInstances.set(video, player);
        return player;
      }
      return null;
    }
    
    // 🚀 Initialiser et lancer la vidéo immédiatement au chargement
    videoElements.forEach(function(video) {
      const player = initPlyrVideo(video, true);
      if (player) {
        player.play().catch(function() {});
      }
    });
    
    // 👁️ Observer pour pause/play quand hors écran
    if ('IntersectionObserver' in window) {
      const videoObserver = new IntersectionObserver(function(entries) {
        entries.forEach(function(entry) {
          const video = entry.target;
          const player = plyrInstances.get(video);
          
          if (player) {
            if (entry.isIntersecting) {
              // ✅ Vidéo visible → Jouer
              player.play().catch(function() {});
            } else {
              // ⏸️ Vidéo hors écran → Mettre en pause
              player.pause();
            }
          }
        });
      }, { 
        rootMargin: '50px',
        threshold: 0.3
      });
      
      videoElements.forEach(function(video) {
        videoObserver.observe(video);
      });
    }
  });
</script>