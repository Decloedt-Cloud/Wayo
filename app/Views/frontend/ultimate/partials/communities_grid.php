<!-- Results Header -->
<?php 
$schools_array = [];
$total_count = 0;
if (!empty($schools)) {
    if (is_object($schools) && method_exists($schools, 'result_array')) {
        $schools_array = $schools;
    } elseif (is_array($schools)) {
        $schools_array = $schools;
    }
    $total_count = (int) ($total_rows ?? count($schools_array));
}
?>

<?php if ($total_count > 0): ?>
<div class="results-header" <?php echo (get_user_language() === 'arabic') ? 'dir="rtl"' : 'dir="ltr"'; ?>>
  <div class="results-count">
    <strong><?php echo $total_count; ?></strong> <?php echo get_phrase('communities_found'); ?>
  </div>
</div>
<?php endif; ?>

<!-- Cards Grid -->
<div id="cardsGrid" class="row g-4" <?php echo (get_user_language() === 'arabic') ? 'dir="rtl"' : 'dir="ltr"'; ?>>
  <?php if (empty($schools_array)): ?>
    <!-- Empty State -->
    <div class="col-12">
      <div class="empty-state">
        <svg width="120" height="120" viewBox="0 0 120 120" fill="none" xmlns="http://www.w3.org/2000/svg" style="margin-bottom: 1.5rem; opacity: 0.5;">
          <circle cx="60" cy="60" r="58" stroke="#e0e0e0" stroke-width="4" fill="none"/>
          <path d="M40 70 L50 60 L60 70 L70 55 L80 65" stroke="#FC7B30" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
          <circle cx="45" cy="45" r="8" fill="#e0e0e0"/>
        </svg>
        <h3 style="color: #333; font-weight: 600; margin-bottom: 0.5rem;"><?php echo get_phrase('No_communities_found'); ?></h3>
        <p class="text-muted"><?php echo $no_courses_found ?? get_phrase('Try_adjusting_your_search_criteria'); ?></p>
      </div>
    </div>
  <?php else: ?>
    <?php foreach ($schools_array as $c): ?>
      <?php 
        $student_count = (int) ($c['members_count'] ?? 0);
        $classes_count = (int) ($c['classes_count'] ?? 0);
        
        $is_private = ($c['access'] ?? 0) == 1;
        $lang = $c['language'] ?? 'fr';
      ?>
      <div class="col-12 col-sm-6 col-lg-4 col-xxl-3 course" 
           data-cat="<?php echo strtolower($c['category']); ?>" 
           data-lang="<?php echo $lang; ?>">
        
        <article class="community-card" <?php echo $lang == 'ar' ? 'dir="rtl"' : ''; ?>>
          <!-- Card Image -->
          <div class="card-image">
            <img src="<?php echo $this->user_model->get_school_cover($c['id']); ?>" 
                 alt="<?php echo htmlspecialchars(html_entity_decode((string) $c['name'], ENT_QUOTES, 'UTF-8'), ENT_QUOTES, 'UTF-8'); ?>" 
                 loading="lazy">
            <span class="card-badge <?php echo $is_private ? 'private' : 'public'; ?>">
              <?php echo $is_private ? get_phrase('Private') : get_phrase('public'); ?>
            </span>
          </div>
          
          <!-- Card Content -->
          <div class="card-content">
            <h3 class="card-title"><?php echo htmlspecialchars(html_entity_decode((string) $c['name'], ENT_QUOTES, 'UTF-8'), ENT_QUOTES, 'UTF-8'); ?></h3>
            <p class="card-description"><?php echo htmlspecialchars(html_entity_decode((string) ($c['description'] ?? ''), ENT_QUOTES, 'UTF-8'), ENT_QUOTES, 'UTF-8'); ?></p>
            
            <!-- Stats -->
            <div class="card-stats">
              <div class="stat-item">
                <i class="fa-solid fa-graduation-cap"></i>
                <span><strong><?php echo number_format($student_count); ?></strong> <?php echo get_phrase('members'); ?></span>
              </div>
              <div class="stat-item">
                <i class="fa-solid fa-play-circle"></i>
                <span><strong><?php echo number_format($classes_count); ?></strong> <?php echo get_phrase('classes'); ?></span>
              </div>
            </div>
            
            <!-- Action Button -->
            <div class="card-action">
              <a href="<?php echo lang_route('community_details', $c['id']); ?>" class="btn-details">
                <?php echo get_phrase('View_community'); ?>
                <i class="fa-solid fa-arrow-right <?php echo $lang == 'ar' ? 'fa-flip-horizontal ms-1' : 'ms-1'; ?>"></i>
              </a>
            </div>
          </div>
        </article>
        
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
</div>

<!-- Pagination -->
<div id="pagination" class="pagination-wrapper">
  <?php if (!empty($links)): ?>
    <?php echo $links; ?>
  <?php endif; ?>
</div>