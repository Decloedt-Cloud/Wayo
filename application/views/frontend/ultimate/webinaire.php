
  <style>
    body {
    margin: 0;
     /* font-family: 'Poppins', sans-serif; */
    font-family: 'Shayan', 'Cairo', 'Tajawal', 'Arial', sans-serif;
    }
    
     .video-background {
  position: relative;
  overflow: hidden;
  height: 100vh;
}

.video-background video {
  position: absolute;
  top: 50%;
  left: 50%;
  min-width: 100%;
  min-height: 100%;
  width: auto;
  height: auto;
  z-index: 0;
  transform: translate(-50%, -50%);
  object-fit: cover;
}

.video-overlay {
  position: absolute;
  top: 0;
  left: 0;
  height: 100%;
  width: 100%;
  background: rgba(0, 0, 0, 0.6);
  z-index: 0;
}

.hero-content {
  position: relative;
  /* z-index: 2; */
  height: 100vh;
  width: 100%;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
}

.content-box {
  /* max-width: 750px;  */
  color: white;
}

.content-box h1 {
  font-size: 2.5rem;
}

.content-box .orange {
  color: #F97316;
}
  /* .hero-banner {
      background: url('https://images.unsplash.com/photo-1504384308090-c894fdcc538d?auto=format&fit=crop&w=1920&q=80') no-repeat center center;
      background-size: cover;
      backdrop-filter: blur(3px);
      color: white;
      padding: 100px 20px 180px;
      position: relative;
      text-align: center;
    } */
    .text-second {
      color:#FC7B30;
    }
    .backround-icon{
      background-color:#FC7B30;
    }

    /* .hero-banner::before {
      content: "";
      position: absolute;
      top: 0; left: 0;
      width: 100%; height: 100%;
      background-color: rgba(0, 0, 0, 0.4);
      z-index: 0;
    } */

    .hero-banner .content {
      position: relative;
      z-index: 1;
    }

  .info-bar {
  background-color: #fff;
  padding: 40px 20px;
}

    .info-bar .info-item {
      color: #fff;
      font-size: 1rem;
      margin-bottom: 10px;
    }

.cta-btn {
  background-color: #FC7B30;
  color: #fff;
  border-radius: 8px;
  padding: 12px 24px;
  font-weight: 600;
}

    .cta-btn:hover {
      background-color: #ff9100;
    }
    .card-style {
    background: #fff;
    padding: 2rem;
    border-radius: 12px;
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.05);
    text-align: center;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
    .card-style2{
     background: #f4f4f4;
    padding: 1rem;
    border-radius: 12px;
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.05);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .section-second{
      background-color:#f4f4f4;
    }

@media (min-width: 768px) {
  .content-box h1 {
    font-size: 3rem;
  }
  .content-box p {
    font-size: 1.25rem;
  }
}

  
  </style>
<body>

<!-- HERO SECTION -->
<!-- <section class="hero-banner">
  <div class="container content">
    <h1 class="fw-bold display-5 mb-4">🎓 Webinaire Exclusif : <br>Découvrez le fonctionnement complet de <span class="text-second">Wayo Academy</span></h1>
    <p class="lead mx-auto" style="max-width: 800px;">
      Rejoignez cette session gratuite pour explorer les outils et avantages de Wayo Academy, la plateforme e-learning pensée au Maroc pour les experts, formateurs et créateurs de contenu.
    </p>
  </div>
</section> -->

  <div class="video-background">
  <video autoplay muted loop playsinline poster="<?php echo base_url('uploads/videos/posters/webinar-hero.webp'); ?>">
    <!-- Mobile (720p, 180 KB) -->
    <source src="<?php echo base_url('uploads/videos/optimized/webinar-hero_mobile.mp4'); ?>" type="video/mp4" media="(max-width: 768px)">
    <!-- Desktop (1080p, 1.3 MB) -->
    <source src="<?php echo base_url('uploads/videos/optimized/webinar-hero.mp4'); ?>" type="video/mp4">
    Your browser does not support HTML5 videos.
  </video>
  <div class="video-overlay"></div>

  <div class="hero-content d-flex justify-content-center align-items-center text-center">
    <div class="content-box px-3">
      <h1 class="fw-bold">
        <?php echo get_phrase("Exclusive Webinar :") ?>
       <br>
        <?php echo get_phrase("Discover the complete workings of") ?><span class="orange"> <?php echo get_phrase("Wayo Academy") ?></span>
      </h1>
      <p class="mt-3 text-white">
        <?php echo get_phrase("Join this free session to explore the tools and benefits of Wayo Academy,") ?>
        <br>
        <?php echo get_phrase("the e-learning platform designed in Morocco for experts, trainers, and content creators.") ?>
      </p>
      <div class="mt-4">
         <a href="#inscription" class="btn cta-btn">
      🔵 <?php echo get_phrase("I register for free") ?>
          </a>
      </div>
    </div>
  </div>
</div>
<!-- INFOS CLÉS -->
<section class="info-bar text-center section-second">
  <div class="container">
    <div class="row justify-content-center mb-3">
      <div class="col-12 col-sm-6 col-md-3 info-item">📅 <strong class= text-dark><?php echo get_phrase("Date:") ?></strong> –</div>
      <div class="col-12 col-sm-6 col-md-3 info-item">🕗 <strong class= text-dark><?php echo get_phrase("Time:") ?></strong> –</div>
      <div class="col-12 col-sm-6 col-md-3 info-item">🌐 <strong class= text-dark><?php echo get_phrase("Location:") ?></strong> <span class= text-dark><?php echo get_phrase("Online") ?> </span> – <a href="#" class="text-dark text-decoration-underline">Lien</a></div>
      <div class="col-12 col-sm-6 col-md-3 info-item">💵 <strong class= text-dark> <?php echo get_phrase("Price:") ?></strong><span class= text-dark> <?php echo get_phrase("Free") ?> </span></div>
    </div>

    <!-- <a href="#inscription" class="btn cta-btn">
      🔵 Je m’inscris gratuitement
    </a> -->
  </div>
</section>
  <section class="bg-white text-dark py-5">
  <div class="container">
    <div class="row align-items-center">
      
      <!-- Texte -->
      <div class="col-md-6">
        <h2 class="fw-bold mb-4"><?php echo get_phrase("Why attend this webinar?") ?></h2>
        <ul class="list-unstyled fs-5">
          <li class="mb-3">✔️ <?php echo get_phrase("Benefit from a live demonstration of the key features") ?></li>
          <li class="mb-3">✔️ <?php echo get_phrase("Learn how to create, structure, and sell an online course") ?></li>
          <li class="mb-3">✔️ <?php echo get_phrase("Discover a local alternative") ?></li>
          <li class="mb-3">✔️ <?php echo get_phrase("Ask your questions live to the Wayo team") ?></li>
          <li class="mb-3">✔️ <?php echo get_phrase("Access exclusive resources offered to participants") ?></li>
        </ul>
        <p class="fst-italic mt-4 fs-5"><?php echo get_phrase("Because you deserve a platform that understands your needs") ?></p>
      </div>

      <!-- Image -->
      <div class="col-md-6">
       <img src="<?php echo base_url('uploads/images/webinaire/formation-enligne.png'); ?>" 
            class="img-fluid" 
            alt="Présentation Webinaire">
      </div>
    </div>
  </div>
</section>

  <section class="section-second text-black py-5">
    
  <div class="container">
    <div class="row justify-content-center text-center mb-4">
      <div class="col-lg-8">
        <h2 class="fw-bold mb-3"><?php echo get_phrase("On the agenda") ?></h2>
        <p class="fs-5"><?php echo get_phrase("Discover the key points covered during the webinar to master Wayo Academy.") ?></p>
      </div>
    </div>

    <div class="row row-cols-1 row-cols-md-2 g-4">
      <div class="col">
        <div class="d-flex card-style ">
          <span class="me-3 fs-4">🧭</span>
          <div>
            <h5 class="fw-bold"><?php echo get_phrase("Overview of the Wayo Academy interface") ?></h5>
            <p class="mb-0"><?php echo get_phrase("Comprehensive introduction to the platform.") ?></p>
          </div>
        </div>
      </div>

      <div class="col">
        <div class="d-flex card-style">
          <span class="me-3 fs-4">🧑‍🏫</span>
          <div>
            <h5 class="fw-bold"><?php echo get_phrase("Creation of educational content") ?></h5>
            <p class="mb-0"><?php echo get_phrase("Modules, quizzes, certificates… everything you need to teach.") ?></p>
          </div>
        </div>
      </div>

      <div class="col">
        <div class="d-flex card-style">
          <span class="me-3 fs-4">💳</span>
          <div>
            <h5 class="fw-bold"><?php echo get_phrase("Payment setup") ?></h5>
            <p class="mb-0"><?php echo get_phrase("Paiement en dirhams & devises étrangères.") ?></p>
          </div>
        </div>
      </div>

      <div class="col">
        <div class="d-flex card-style">
          <span class="me-3 fs-4">📈</span>
          <div>
            <h5 class="fw-bold"><?php echo get_phrase("Student management & tracking") ?></h5>
            <p class="mb-0"><?php echo get_phrase("Progress tracking & integrated customer support.") ?></p>
          </div>
        </div>
      </div>

      <div class="col">
        <div class="d-flex card-style">
          <span class="me-3 fs-4">👨‍🏫</span>
          <div>
            <h5 class="fw-bold"><?php echo get_phrase("Espace formateur & apprenant") ?></h5>
            <p class="mb-0"><?php echo get_phrase("Each has their own tailored interface.") ?></p>
          </div>
        </div>
      </div>

      <div class="col">
        <div class="d-flex card-style">
          <span class="me-3 fs-4">❓</span>
          <div>
            <h5 class="fw-bold"><?php echo get_phrase("Live Q&A session") ?></h5>
            <p class="mb-0"><?php echo get_phrase("Ask your questions to the Wayo team at the end of the session.") ?></p>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>
  
<section class="bg-white text-black py-5">
  <div class="container">
    <div class="row justify-content-center text-center mb-5">
      <div class="col-lg-8">
        <h2 class="fw-bold mb-3"><?php echo get_phrase("Why attend this webinar?") ?></h2>
        <p class="fs-5"><?php echo get_phrase("Discover the profiles that will fully benefit from this online session.") ?></p>
      </div>
    </div>

    <div class="row justify-content-center">
      <div class="col-lg-8">
        <ul class="list-group list-group-flush">
          <li class="list-group-item card-style2 d-flex align-items-start mb-3">
            <div class="rounded-circle backround-icon text-dark d-flex justify-content-center align-items-center me-3" style="width: 40px; height: 40px;">🎓</div>
            <div>
              <p class="mb-0  fs-5"><?php echo get_phrase("Trainers and experts looking to build an online business and monetize their expertise.") ?></p>
            </div>
          </li>

          <li class="list-group-item card-style2 d-flex align-items-start mb-3">
            <div class="rounded-circle backround-icon text-dark d-flex justify-content-center align-items-center me-3" style="width: 40px; height: 40px;">🧑‍🎓</div>
            <div>
              <p class="mb-0 fs-5"><?php echo get_phrase("Students and recent graduates ready to showcase their skills.") ?></p>
            </div>
          </li>

          <li class="list-group-item card-style2 d-flex align-items-start mb-3">
            <div class="rounded-circle backround-icon text-dark d-flex justify-content-center align-items-center me-3" style="width: 40px; height: 40px;">🧑‍💼</div>
            <div>
              <p class="mb-0 fs-5"><?php echo get_phrase("Coaches, consultants, independent professionals") ?></p>
            </div>
          </li>

          <li class="list-group-item card-style2 d-flex align-items-start mb-3">
            <div class="rounded-circle backround-icon text-dark d-flex justify-content-center align-items-center me-3" style="width: 40px; height: 40px;">🎥</div>
            <div>
              <p class="mb-0 fs-5"><?php echo get_phrase("Content creators seeking a structured space to share their knowledge") ?></p>
            </div>
          </li>

          <li class="list-group-item card-style2 d-flex align-items-start">
            <div class="rounded-circle backround-icon text-dark d-flex justify-content-center align-items-center me-3" style="width: 40px; height: 40px;">🏫</div>
            <div>
              <p class="mb-0 fs-5"><?php echo get_phrase("Schools and associations looking to digitize their programs") ?></p>
            </div>
          </li>
        </ul>
      </div>
    </div>
  </div>
</section>
  
  <section class="section-second text-black py-5">
  <div class="container">
    <!-- Titre principal -->
    <div class="row justify-content-center text-center mb-5">
      <div class="col-lg-8">
        <h2 class="fw-bold mb-3"> <?php echo get_phrase("🇲🇦 Why") ?> <span class="text-second"><?php echo get_phrase("Wayo Academy") ?></span> </h2>
        <p class="fs-5"><?php echo get_phrase("A local solution designed for Morocco’s talents of today and tomorrow.") ?></p>
      </div>
    </div>

    <!-- Grille des avantages -->
    <div class="row g-4">
      <!-- Avantage 1 -->
      <div class="col-md-6">
        <div class="card-style rounded-4 p-4 h-100">
          <div class="d-flex align-items-center mb-3">
            <div class="rounded-circle backround-icon text-dark d-flex justify-content-center align-items-center me-3" style="width: 50px; height: 50px;">
              🌍
            </div>
            <h5 class="fw-bold mb-0"><?php echo get_phrase("Platform developed and hosted in Morocco") ?></h5>
          </div>
          <p class="mb-0"><?php echo get_phrase("A 100% local technology, reliable and aligned with the needs of the Moroccan market.") ?></p>
        </div>
      </div>

      <!-- Avantage 2 -->
      <div class="col-md-6">
        <div class="card-style rounded-4 p-4 h-100">
          <div class="d-flex align-items-center mb-3">
            <div class="rounded-circle backround-icon text-dark d-flex justify-content-center align-items-center me-3" style="width: 50px; height: 50px;">
              🧠
            </div>
            <h5 class="fw-bold mb-0"><?php echo get_phrase("Interface designed for Moroccans") ?></h5>
          </div>
          <p class="mb-0"><?php echo get_phrase("An intuitive platform, tailored to the culture, language, and local realities.") ?></p>
        </div>
      </div>

      <!-- Avantage 3 -->
      <div class="col-md-6">
        <div class="card-style rounded-4 p-4 h-100">
          <div class="d-flex align-items-center mb-3">
            <div class="rounded-circle backround-icon text-dark d-flex justify-content-center align-items-center me-3" style="width: 50px; height: 50px;">
              💳
            </div>
            <h5 class="fw-bold mb-0"><?php echo get_phrase("Easy payment methods") ?></h5>
          </div>
          <p class="mb-0"><?php echo get_phrase("Moroccan cards, bank transfers, and other easily accessible options for everyone.") ?></p>
        </div>
      </div>

      <!-- Avantage 4 -->
      <div class="col-md-6">
        <div class="card-style rounded-4 p-4 h-100">
          <div class="d-flex align-items-center mb-3">
            <div class="rounded-circle backround-icon text-dark d-flex justify-content-center align-items-center me-3" style="width: 50px; height: 50px;">
              🚀
            </div>
            <h5 class="fw-bold mb-0"><?php echo get_phrase("A clear vision") ?></h5>
          </div>
          <p class="mb-0"><?php echo get_phrase("Accelerate access to digital training for all talents, wherever they are.") ?></p>
        </div>
      </div>
    </div>
  </div>
</section>
  <section class="bg-white text-black py-5">
  <div class="container">
    <!-- Titre -->
    <div class="row text-center mb-5">
      <div class="col-lg-8 mx-auto">
        <h2 class="fw-bold mb-3"> <?php echo get_phrase("They are already using") ?><span class="text-second"> <?php echo get_phrase("Wayo Academy") ?></span></h2>
        <p class="fs-5"><?php echo get_phrase("See what our users think after joining the platform.") ?></p>
      </div>
    </div>

    <!-- Témoignages -->
    <div class="row g-4">
      <!-- Témoignage 1 -->
      <div class="col-md-6">
        <div class="card-style2 rounded-4 p-4 h-100">
          <p class="fs-5 fst-italic mb-4"><?php echo get_phrase("“Wayo allowed me to launch my courses without any technical skills. The support is excellent, and payments in dirhams are a real advantage.”") ?></p>
          <div class="d-flex align-items-center">
            <div class="me-3">
              <div class="backround-icon text-dark fw-bold rounded-circle d-flex justify-content-center align-items-center" style="width: 50px; height: 50px;">👩</div>
            </div>
            <div>
              <strong><?php echo get_phrase("Hanae") ?></strong><br>
              <span class="text-second"><?php echo get_phrase("Nutrition Coach – Agadir") ?></span>
            </div>
          </div>
        </div>
      </div>

      <!-- Témoignage 2 -->
      <div class="col-md-6">
        <div class="card-style2 rounded-4 p-4 h-100">
          <p class="fs-5 fst-italic mb-4"><?php echo get_phrase("“I wanted to avoid complicated international platforms. Wayo is simple, fast, and on top of that, it’s Moroccan.”") ?></p>
          <div class="d-flex align-items-center">
            <div class="me-3">
              <div class="backround-icon text-dark fw-bold rounded-circle d-flex justify-content-center align-items-center" style="width: 50px; height: 50px;">👨</div>
            </div>
            <div>
              <strong><?php echo get_phrase("Ibrahim") ?></strong><br>
              <span class=" text-second"><?php echo get_phrase("Finance Trainer – Tangier") ?></span>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
</section>
  
  <section class="text-black py-5 section-second" id="inscription">
  <div class="container">
    <!-- Titre -->
    <div class="row justify-content-center text-center mb-4">
      <div class="col-lg-8">
        <h2 class="fw-bold mb-3"><?php echo get_phrase("Webinar Registration") ?></h2>
        <p class="fs-5"><strong><?php echo get_phrase("Limited seats") ?></strong> <?php echo get_phrase("– Sign up now to receive your personal access link") ?></p>
      </div>
    </div>

    <!-- Formulaire -->
    <div class="row justify-content-center">
      <div class="col-lg-8">
        <form class="bg-white border border-secondary rounded-4 p-4">

          <!-- Prénom & Nom -->
          <div class="mb-3">
            <label for="nom" class="form-label"><?php echo get_phrase("First and Last Name") ?></label>
            <input type="text" class="form-control bg-light text-dark border-secondary" style="background-color: #f9f9f9;"  id="nom" placeholder="Ex. Salma Benali" required>
          </div>

          <!-- Email -->
          <div class="mb-3">
            <label for="email" class="form-label"><?php echo get_phrase("Email Address") ?></label>
            <input type="email" class="form-control bg-light text-dark border-secondary" style="background-color: #f9f9f9;"  id="email" placeholder="Ex. salma@example.com" required>
          </div>

          <!-- Ville -->
          <div class="mb-3">
            <label for="ville" class="form-label"><?php echo get_phrase("City") ?></label>
            <input type="text" class="form-control bg-light text-dark border-secondary" style="background-color: #f9f9f9;" id="ville" placeholder="Ex. Casablanca" required>
          </div>

          <!-- Profession -->
          <div class="mb-3">
            <label for="profession" class="form-label"><?php echo get_phrase("Profession (optional)") ?></label>
            <input type="text" class="form-control bg-light text-dark border-secondary" style="background-color: #f9f9f9;"  id="profession" placeholder="Ex. Coach en nutrition">
          </div>

          <!-- Checkbox -->
          <div class="form-check mb-4">
            <input class="form-check-input bg-black border-secondary" type="checkbox" id="supports">
            <label class="form-check-label" for="supports">
              <?php echo get_phrase("I would like to receive the materials shared during the webinar") ?>
            </label>
          </div>

          <!-- Bouton -->
          <div class="d-grid">
            <button type="submit" class="btn cta-btn py-2 fs-5">
               <?php echo get_phrase("I reserve my spot") ?>
            </button>
          </div>

        </form>
      </div>
    </div>
  </div>
</section>