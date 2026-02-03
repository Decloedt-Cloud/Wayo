<!-- Zone des cartes -->
    <div id="cardsGrid" class="row g-4" <?php echo (get_user_language() === 'arabic') ? 'dir="rtl"' : 'dir="ltr"'; ?>>
      <?php 
      $schools_array = [];
      if (!empty($schools)) {
          if (is_object($schools) && method_exists($schools, 'result_array')) {
              $schools_array = $schools->result_array();
          } elseif (is_array($schools)) {
              $schools_array = $schools;
          }
      }
      if (empty($schools_array)): ?>
          <div class="text-center py-5">
            <img src="../uploads/images/communities/Not-found.png" 
                alt="A" 
                class="img-fluid mb-3" 
                style="max-width: 120px; opacity: 0.8;">
            <p class="text-muted fs-9">
              <?php echo $no_courses_found ?? get_phrase('0_communities_found'); ?>
            </p>
          </div>
      <?php else: ?>
        <?php foreach ($schools_array as $c): ?>
          <div class="col-12 col-sm-6 col-lg-4 col-xxl-3 course" 
               data-cat="<?php echo strtolower($c['category']); ?>" 
               data-lang="<?php echo $c['language'] ?? 'fr'; ?>">

            <div class="card h-100 shadow-sm border-0 rounded-3">
              <img class="card-img-top card-img-custom ratio ratio-16x9 object-fit-cover" 
                   src="<?php echo $this->user_model->get_school_cover($c['id']); ?>" 
                   alt="<?php echo $c['name']; ?>" >

              <div class="card-body d-flex flex-column <?php echo ($c['language'] ?? '') == 'ar' ? 'text-end' : ''; ?>" 
                   <?php echo ($c['language'] ?? '') == 'ar' ? 'dir="rtl"' : ''; ?>>

                <h3 class="h6 fw-bold text-uppercase"><?php echo $c['name']; ?></h3>
                <p class="small text-secondary mb-3 card-description" style="display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; text-overflow: ellipsis; height: 4.5em; line-height: 1.5em;"><?php echo $c['description']; ?></p>

                <ul class="list-inline small text-secondary mb-3">
                  <li class="list-inline-item me-3">
                    <i class="fa-solid fa-users me-1 text-wayo"></i>
                    <?php 
                      $student_count = $this->db->get_where('students', [
                          'school_id' => $c['id'],
                          'status'    => 1
                      ])->num_rows();
                      echo $student_count;
                    ?>
                  </li>
                  <li class="list-inline-item me-3">
                    <i class="fa-solid fa-chalkboard-user me-1 text-wayo"></i>
                    <?php 
                      $classes_count = $this->db->get_where('classes', [
                          'school_id' => $c['id'],
                          'statut' => 'active'
                      ])->num_rows();
                      echo $classes_count;
                    ?>
                    <?php echo get_phrase('classes'); ?>
                  </li>
                  <li class="list-inline-item">
                    <?php if (($c['access'] ?? 0) == 1): ?>
                      <span class="badge rounded-pill text-bg-wayo-secondaire"><?php echo get_phrase('Private'); ?></span>
                    <?php else: ?>
                      <span class="badge rounded-pill text-bg-wayo"><?php echo get_phrase('public'); ?></span>
                    <?php endif; ?>
                  </li>
                </ul>

                <a class="btn btn-outline-wayo mt-auto" 
                   href="<?php echo lang_route('community_details', $c['id']); ?>">
                  <!-- <?php echo ($c['language'] ?? '') == 'ar' ? 'التفاصيل' : 'Détails'; ?> -->
                   <?php echo get_phrase('Details'); ?>
                </a>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

    <!-- Pagination (AVEC ID) -->
    <div id="pagination" class="row justify-content-center mt-4">
      <div class="col-auto">
        <?php echo $links ?? ''; ?>
      </div>
    </div>