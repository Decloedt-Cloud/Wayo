<main class="mt-5">
  <!-- ===== HERO ===== -->
    <section class="hero">
      <div class="container hero-content py-5" data-animate>
        <h1 class="display-5 fw-bold mb-2"><?php echo get_phrase("Discover our communities") ?></h1>
       
        <p class="lead mb-4 text-white fs-md-4 fs-lg-3" style="letter-spacing: 1px; font-size: 1.5rem; margin-bottom: 1rem;"><?php echo get_phrase("Dynamic communities + Quality classes = Learning that takes off!") ?></p>
      </div>
    </section>
  <!-- ===== BARRE DE FILTRES (style identique au screen) ===== -->
  <section class="py-4 bg-light border-top">
    <div class="container">
      <div class="row g-3 align-items-center">
        <!-- Recherche (pill + icône) -->
        <div class="col-12 col-lg-5">
          <div class="pill-input d-flex align-items-center">
            <i class="fa-solid fa-magnifying-glass ms-3 me-2 text-muted"></i>
            <input id="searchInput" type="search" class="form-control border-0 bg-transparent" placeholder="<?php echo get_phrase("Search for a community…") ?>">
          </div>
        </div>

        <!-- Sélecteur catégories (pill + bordure orange + icône filtre) -->
        <div class="col-12 col-md-6 col-lg-4">
          <div class="pill-select d-flex border border-2 rounded-pill border-warning">
            <span class="ps-3 d-inline-flex align-items-center text-muted">
              <i class="fa-solid fa-filter"></i>
            </span>
            <select name="categories" id="categories" 
                    class="form-select border-0 bg-transparent flex-grow-1 select_course text-dark"
                    onchange="location = this.value;">
              
              <!-- Option pour "All" -->
              <option value="<?php echo base_url('home/communities/'); ?>" 
                <?php echo empty($selected_category) ? 'selected' : ''; ?>>
                <?php echo get_phrase('All categories'); ?>
              </option>

              <!-- Boucle dynamique sur les catégories -->
              <?php foreach ($categories as $category): ?>
                <?php 
                  $cat_formated = $this->frontend_model->get_category_formated($category['name']); 
                ?>
                <option value="<?php echo base_url('home/communities/' . $cat_formated); ?>" 
                  <?php echo ($selected_category == $category['name']) ? 'selected' : ''; ?>>
                  <?php echo $category['name']; ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <!-- Langues (boutons pastilles, actif = bleu) -->
        <div class="col-12 col-md-6 col-lg-3">
          <div class="d-flex align-items-center gap-3 justify-content-lg-end">
            <span class="text-muted small d-none d-md-inline"><?php echo get_phrase("Language ") ?></span>
            <div id="langFilter" class="d-flex align-items-center gap-2">
              <button class="lang-pill active" data-lang="all"><?php echo get_phrase("All") ?></button>
              <button class="lang-pill" data-lang="fr">FR</button>
              <button class="lang-pill" data-lang="ar">AR</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ===== CTA ===== -->
  <section class="py-4 bg-light">
    <div class="container">
      <div class="p-4 p-md-5 rounded-4 text-white d-flex flex-column flex-md-row align-items-md-center justify-content-between cta-gradient">
        <div class="me-md-3">
          <h2 class="h4 fw-bold mb-2"><i class="fa-solid fa-rocket me-2"></i><?php echo get_phrase("Launch your own community in minutes") ?></h2>
          <p class="mb-0 opacity-90 text-white"><?php echo get_phrase("Monetize your expertise, engage your members, and enjoy the power of the Wayo platform") ?></p>
        </div>
        <a href="<?php echo site_url('admission/online_admission'); ?>" class="btn btn-light text-wayo fw-bold mt-3 mt-md-0 px-4"><?php echo get_phrase("Create my community") ?></a>
      </div>
    </div>
  </section>

  <!-- ===== GRID DES COMMUNAUTÉS (20 cartes) ===== -->
  <section class="py-5 section-communities">
  <div class="container mt-5">
    <div id="cardsGrid" class="row g-4">

      <?php 
      // Vérifier si des écoles existent
      $schools_array = [];

      if (!empty($schools)) {
          if (is_object($schools) && method_exists($schools, 'result_array')) {
              $schools_array = $schools->result_array();
          } elseif (is_array($schools)) {
              $schools_array = $schools;
          }
      }

      if (empty($schools_array)): ?>
        <p class="text-center text-muted"><?php echo $no_courses_found ?? get_phrase('0_communities_found'); ?></p>
      <?php else: ?>
        <?php foreach ($schools_array as $c): ?>
          <!-- Carte cours dynamique -->
          <div class="col-12 col-sm-6 col-lg-4 col-xxl-3 course" 
              data-cat="<?php echo strtolower($c['category']); ?>" 
              data-lang="<?php echo $c['language'] ?? 'fr'; ?>">

            <div class="card h-100 shadow-sm border-0 rounded-3">

              <!-- Image -->
              <img class="card-img-top card-img-custom ratio ratio-16x9 object-fit-cover" 
                  src="<?php echo $this->user_model->get_school_cover($c['id']); ?>" 
                  alt="<?php echo $c['name']; ?>" >

              <div class="card-body d-flex flex-column <?php echo ($c['language'] ?? '') == 'ar' ? 'text-end' : ''; ?>" 
                  <?php echo ($c['language'] ?? '') == 'ar' ? 'dir="rtl"' : ''; ?>>

                <!-- Titre -->
                <h3 class="h6 fw-bold text-uppercase"><?php echo $c['name']; ?></h3>

                <!-- Description -->
                <p class="small text-secondary mb-3 card-description"><?php echo $c['description']; ?></p>

                <!-- Infos cours -->
                <ul class="list-inline small text-secondary mb-3">
                  <li class="list-inline-item me-3">
                    <i class="fa-solid fa-users me-1 text-wayo"></i>
                    <?php echo $c['students_count'] ?? '0'; ?>
                  </li>
                  <li class="list-inline-item me-3">
                    <i class="fa-solid fa-chalkboard-user me-1 text-wayo"></i>
                    <?php echo $c['classes_count'] ?? '0'; ?> classes
                  </li>
                  <li class="list-inline-item">
                    <?php if (($c['access'] ?? 0) == 1): ?>
                      <span class="badge rounded-pill text-bg-wayo-secondaire"><?php echo get_phrase('Privé'); ?></span>
                    <?php else: ?>
                       <span class="badge rounded-pill text-bg-wayo"><?php echo get_phrase('Accès libre'); ?></span>
                    <?php endif; ?>
                  </li>
                </ul>

                <!-- Lien détails -->
                <a class="btn btn-outline-wayo mt-auto" 
                  href="<?php echo base_url('home/community_details/' . $c['id']); ?>">
                  <?php echo ($c['language'] ?? '') == 'ar' ? 'التفاصيل' : 'Détails'; ?>
                </a>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>

    </div>

    <!-- Pagination -->
    <div class="row justify-content-center mt-4">
      <div class="col-auto">
        <?php echo $links ?? ''; ?>
      </div>
    </div>
    
  </div>
</section>



  <!-- Scroll to top -->
  <button id="scrollTopBtn" class="btn btn-wayo btn-lg rounded-circle shadow position-fixed" aria-label="Retour haut">
    <i class="fa-solid fa-arrow-up"></i>
  </button>
</main>
 <!-- Scripts -->
  <script>
    document.addEventListener("DOMContentLoaded", () => {
  const search   = document.getElementById("searchInput");
  const catSel   = document.getElementById("catSelect");
  const langBtns = document.querySelectorAll("#langFilter .lang-pill");
  const cards    = document.querySelectorAll(".course");
  const topBtn   = document.getElementById("scrollTopBtn");

  let currentCat = "all";
  let currentLang = "all";
  let query = "";

  function refresh(){
    cards.forEach(card => {
      const okCat  = (currentCat === "all" || card.dataset.cat === currentCat);
      const okLang = (currentLang === "all" || card.dataset.lang === currentLang);
      const okTxt  = card.textContent.toLowerCase().includes(query);
      card.style.display = (okCat && okLang && okTxt) ? "" : "none";
    });
  }

  // Search
  search.addEventListener("input", () => {
    query = search.value.trim().toLowerCase();
    refresh();
  });

  // Category
  catSel.addEventListener("change", () => {
    currentCat = catSel.value;
    refresh();
  });

  // Language pills
  langBtns.forEach(btn => {
    btn.addEventListener("click", () => {
      langBtns.forEach(b => b.classList.remove("active"));
      btn.classList.add("active");
      currentLang = btn.dataset.lang;
      refresh();
    });
  });

  // Scroll-to-top show/hide
  window.addEventListener("scroll", () => {
    topBtn.style.display = window.scrollY > 600 ? "inline-flex" : "none";
  });

  // Smooth scroll top
  topBtn.addEventListener("click", () => window.scrollTo({ top: 0, behavior: "smooth" }));
});
  </script>

