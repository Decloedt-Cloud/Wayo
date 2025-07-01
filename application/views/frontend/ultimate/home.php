<!-- ========== MAIN ========== -->
<main class="main-content" id="content" role="main">
  <!-- Intro Section -->
<div class="intro-section">
  <div id="intro-container" class="intro-container position-relative" style="min-height: 97vh;">
    <!-- Background -->
    <div rel="preload" class="position-absolute top-0 start-0 end-0 bottom-0 opacity-100" style="background-image: url('uploads/images/decloedt/home/bg_header.webp'); background-size: cover; background-position: center; filter: brightness(50%); z-index: 0;"></div>
    <!-- Container for content -->
    <div class="container d-flex align-items-center justify-content-center" style="min-height: 97vh; padding-bottom: 0px;">
      <div class="row position-relative justify-content-center" style="z-index: 1;">
        <!-- Centered Column -->
        <div class="col-lg-12 d-flex flex-column align-items-center text-center">
          <div class="text-container">
            <h1 class="text-white display-2 fw-bold display-md-4 display-lg-5" style="font-size: 3rem; letter-spacing: 2px; text-shadow: 0 2px 6px rgba(0, 0, 0, 0.4); margin-bottom: 1rem;"><?php echo get_phrase("Build your digital future") ?></h1>
            <p class="text-white fs-md-4 fs-lg-3" style="letter-spacing: 1px; font-size: 1.5rem; margin-bottom: 1rem;"><?php echo get_phrase("Practical training, human support and concrete results") ?></p>
          </div>
          <!-- Buttons -->
          <div class="row justify-content-center g-3">
            <!-- Student Admission Button -->
            <div class="col-auto">
              <a class="btn btn-member border-3 shadow-sm rounded-3 w-100 w-md-auto px-4.5 py-2.5" href="<?php echo site_url('admission/online_admission_student'); ?>"><?php echo get_phrase("I'm a member") ?></a>
            </div>
            <!-- Mentor Admission Button -->
            <div class="col-auto">
              <a class="btn btn-mentor border-3 shadow-sm rounded-3 w-100 w-md-auto px-4.5 py-2.5"  href="<?php echo site_url('admission/online_admission'); ?>"><?php echo get_phrase("I'm a Mentor") ?></a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
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
  <section class="py-3 position-relative pricing" style="min-height: 120vh;">
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
                        <td>€0</td>
                        <td>€20 / <?php echo get_phrase("month") ?></td>
                        <td>€27 / <?php echo get_phrase("month") ?></td>
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