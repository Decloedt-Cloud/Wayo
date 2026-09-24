<main id="communitiesPage"
  data-no-results="<?php echo esc(get_phrase('No_results_found'), 'attr'); ?>"
  data-loading-error="<?php echo esc(get_phrase('Loading_error'), 'attr'); ?>"
  data-pagination-nav-label="<?php echo esc(get_phrase('Our_Communities'), 'attr'); ?>"
  data-pagination-page-prefix="<?php echo esc(get_phrase('Page'), 'attr'); ?>">
  <!-- ===== HERO ===== -->
  <section class="communities-hero">
    <div class="container hero-content" data-animate>
      <h1><?php echo get_phrase("Discover our") ?> <span><?php echo get_phrase("communities") ?></span></h1>
      <p><?php echo get_phrase("Engaged_communities_and_high-quality_courses_for_sustainable_progress.") ?></p>
      
      <div class="hero-stats">
        <div class="hero-stat">
          <i class="fa-solid fa-users"></i>
          <span><?php echo (int) ($visible_communities_count ?? 0); ?>+ <?php echo get_phrase("communities"); ?></span>
        </div>
        <div class="hero-stat">
          <i class="fa-solid fa-graduation-cap"></i>
          <span><?php echo (int) ($visible_members_count ?? 0); ?>+ <?php echo get_phrase("members"); ?></span>
        </div>
        <div class="hero-stat">
          <i class="fa-solid fa-star"></i>
          <span><?php echo get_phrase("Quality_content"); ?></span>
        </div>
      </div>

      <!-- ===== FILTER SECTION ===== -->
      <form id="searchForm" action="<?php echo site_url('home/communities_search'); ?>" method="get" <?php echo (get_user_language() === 'arabic') ? 'dir="rtl"' : 'dir="ltr"'; ?>>
        <div class="filter-wrapper">
          
          <!-- Search Box -->
          <div class="search-box">
            <i class="fa-solid fa-magnifying-glass"></i>
            <label for="searchInput" class="visually-hidden"><?php echo get_phrase('Search_communities'); ?></label>
            <input id="searchInput"
              type="search"
              name="search"
              aria-label="<?php echo esc(get_phrase('Search_communities'), 'attr'); ?>"
              placeholder="<?php echo get_phrase('Search_communities'); ?>..."
              value="<?php if (isset($input_search) && $input_search) echo ($input_search); ?>">
            <button type="button" class="clear-btn" id="clearSearch" title="<?php echo get_phrase('Clear'); ?>">
              <i class="fa-solid fa-xmark"></i>
            </button>
            <button type="submit" aria-label="<?php echo esc(get_phrase('Search_communities'), 'attr'); ?>" title="<?php echo esc(get_phrase('Search_communities'), 'attr'); ?>">
              <i class="fa-solid fa-arrow-right"></i>
            </button>
          </div>
          
          <!-- Category Filter -->
          <div class="filter-select">
            <i class="fa-solid fa-th icon-left"></i>
            <select name="categories" id="catSelect">
              <option value="<?php echo lang_route('communities'); ?>"><?php echo get_phrase('All_categories'); ?></option>
              <?php foreach ($categories as $category): ?>
                <?php $cat_formated = str_replace(" ", "_", $category['name']); ?>
                <option value="<?php echo lang_route('communities', $cat_formated); ?>"<?php echo (($selected_category ?? '') === $category['name']) ? ' selected' : ''; ?>>
                  <?php echo get_phrase($category['name']); ?>
                </option>
              <?php endforeach; ?>
            </select>
            <i class="fa-solid fa-chevron-down icon-right"></i>
          </div>
          
          <!-- Language Filter -->
         <!--  <div class="filter-select">
            <i class="fa-solid fa-language icon-left"></i>
            <select id="langSelect">
              <option value="all"><?php echo get_phrase("All_languages") ?></option>
              <option value="fr">Français</option>
              <option value="ar">العربية</option>
              <option value="en">English</option>
            </select>
            <i class="fa-solid fa-chevron-down icon-right"></i>
          </div> -->
          
        </div>
      </form>
    </div>
  </section>



  <!-- ===== GRID DES COMMUNAUTÉS ===== -->
  <section class="communities-section">
    <div class="container" id="communitiesContainer">
      <?php include 'partials/communities_grid.php'; ?>
    </div>
  </section>

  <!-- ===== CTA ===== -->
  <?php if (!session()->get('user_id')): ?>
    <section class="cta-section" <?php echo (get_user_language() === 'arabic') ? 'dir="rtl"' : 'dir="ltr"'; ?>>
      <div class="container">
        <div class="cta-card">
          <div class="cta-content">
            <h2><i class="fa-solid fa-rocket <?php echo (get_user_language() === 'arabic') ? 'ms-2' : 'me-2'; ?>"></i><?php echo get_phrase("Launch your own community in minutes") ?></h2>
            <p><?php echo get_phrase("Monetize your expertise, engage your members, and enjoy the power of the Wayo platform") ?></p>
          </div>
          <a href="<?php echo site_url('admission/online_admission'); ?>" class="cta-btn"><?php echo get_phrase("Create my community") ?> <i class="fa-solid fa-arrow-right <?php echo (get_user_language() === 'arabic') ? 'me-2 fa-flip-horizontal' : 'ms-2'; ?>"></i></a>
        </div>
      </div>
    </section>
  <?php endif; ?>
  <script src="<?php echo base_url('assets/frontend/ultimate/js/communities-page.js'); ?>?v=<?php echo filemtime(ROOTPATH . 'assets/frontend/ultimate/js/communities-page.js'); ?>" defer></script>
</main>