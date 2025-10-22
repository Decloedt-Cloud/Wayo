<main class="mt-5">
  <!-- ===== HERO ===== -->
    <section class="hero">
      <div class="container hero-content py-5" data-animate>
        <h1 class="display-5 fw-bold mb-2"><?php echo get_phrase("Discover our communities") ?></h1>
       
        <p class="lead mb-4 text-white fs-md-4 fs-lg-3" style="letter-spacing: 1px; font-size: 1.5rem; margin-bottom: 1rem;"><?php echo get_phrase("Dynamic communities + Quality classes = Learning that takes off!") ?></p>
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
    <form id="searchForm" class="search-bar " action="<?php echo site_url('home/communities_search'); ?>" method="get">
    <!-- ===== BARRE DE FILTRES (style identique au screen) ===== -->
    <section class="py-4 bg-light border-top">
      <div class="container">
        <div class="row g-3 align-items-center">
          <!-- Recherche (pill + icône) -->
          <div class="col-12 col-lg-5">
          <div class="pill-input d-flex align-items-center">
            <input id="searchInput" 
                  type="search" 
                  name="search"
                  class="form-control border-0 bg-transparent ps-3" 
                  placeholder="<?php echo get_phrase('Search'); ?>"
                  value="<?php if ($input_search) echo ($input_search); ?>">
            <button type="submit" class="btn-search">
              <i class="fa-solid fa-magnifying-glass"></i>
            </button>
          </div>
          </div>
          <!-- Sélecteur catégories (pill + bordure orange + icône filtre) -->
          <div class="col-12 col-md-6 col-lg-4">
            <div class="pill-select d-flex border border-2 rounded-pill border-warning">
              <span class="ps-3 d-inline-flex align-items-center text-muted">
                <i class="fa-solid fa-filter"></i>
              </span>
                 <select name="categories" id="catSelect"
                      class="form-select border-0 bg-transparent flex-grow-1 select_course text-dark">
                <option value="<?php echo base_url('home/communities/'); ?>"><?php echo get_phrase('All_categorie'); ?></option>
                <?php foreach ($categories as $category): ?>
                  <?php 
                    $cat_formated = $this->frontend_model->get_category_formated($category['name']); 
                  ?>
                  <option value="<?php echo base_url('home/communities/' . $cat_formated); ?>">
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
    </form>



  <!-- ===== GRID DES COMMUNAUTÉS (20 cartes) ===== -->
  <section class="py-5 section-communities">
   <div class="container mt-5" id="communitiesContainer">
      <?php include 'partials/communities_grid.php'; ?>

    </div>

  </section>



  <script>
    document.addEventListener("DOMContentLoaded", () => {
  const search   = document.getElementById("searchInputs");
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
   <!-- //ajax form recherche -->
    <script>
    document.addEventListener("DOMContentLoaded", () => {
      const form = document.getElementById("searchForm");
      const searchInput = document.getElementById("searchInput");
      const searchButton = form.querySelector("button[type='submit']");
      const catSelect = document.getElementById("catSelect");
      const cardsGrid = document.getElementById("cardsGrid");
      const pagination = document.getElementById("pagination");

      // --- Recherche au clic ---
      searchButton.addEventListener("click", e => {
        e.preventDefault();
        sendAjax();
      });

      // --- Filtrage par catégorie ---
      catSelect.addEventListener("change", e => {
        e.preventDefault();
        sendAjax(catSelect.value);
      });

      function sendAjax(pageUrl = null) {
        const searchValue = searchInput.value.trim();
        let url = pageUrl || "<?php echo site_url('home/communities_search'); ?>";

        // Ajouter le paramètre search si présent
        if (searchValue) {
          const sep = url.includes("?") ? "&" : "?";
          url += sep + "search=" + encodeURIComponent(searchValue);
        }

        cardsGrid.innerHTML = `
          <div class="alert alert-primary text-center" role="alert">
            <?php echo get_phrase("Loading...") ?>
          </div>
        `;

        fetch(url)
          .then(res => res.text())
          .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, "text/html");
            cardsGrid.innerHTML = doc.querySelector("#cardsGrid")?.innerHTML || "<p class='text-center text-muted py-5'>Aucun résultat trouvé.</p>";
            pagination.innerHTML = doc.querySelector("#pagination")?.innerHTML || "";
            attachPaginationEvents();
          })
          .catch(err => {
            console.error("Erreur AJAX :", err);
            cardsGrid.innerHTML = `<p class="text-center text-danger py-5">Erreur de chargement.</p>`;
          });
      }

       function attachPaginationEvents() {
        pagination.querySelectorAll("a").forEach(a => {
          a.addEventListener("click", e => {
            e.preventDefault();
            sendAjax(a.href);
          });
        });
      }
      attachPaginationEvents();
    });
    </script>



