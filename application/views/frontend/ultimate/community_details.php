<?php if (get_common_settings('recaptcha_status')): ?>
  <script src="https://www.google.com/recaptcha/api.js" async defer></script>
<?php endif; ?>

<?php
// --------- TVA / VAT CALCULATION ---------
// On récupère les réglages fiscaux de la communauté / école
$settings_school = $this->settings_model->get_settings_school_data($school_id);

$vat_applicable = isset($settings_school['vat']) && (int)$settings_school['vat'] === 1;
$tax_residence  = isset($settings_school['Tax_residence']) ? $settings_school['Tax_residence'] : null;

$vat_rate = 0; // en pourcentage
if ($vat_applicable) {
    if ($tax_residence === 'MA') {
        // 1 - Communauté au Maroc  => 20% de TVA
        $vat_rate = 20;
    } elseif ($tax_residence === 'UAE') {
        // 2 - Communauté aux EAU => 5% de TVA
        $vat_rate = 5;
    }
}

// Calcul du prix avec TVA pour la communauté
$school_price_ht = (float)$school['price'];
$school_vat_amount = $school_price_ht * ($vat_rate / 100);
$school_price_ttc = $school_price_ht + $school_vat_amount;

// Calcul du prix avec TVA pour chaque classe
$classes_with_vat = [];
foreach ($classes as $key => $class) {
    $class_price_ht = isset($class['price']) ? (float)$class['price'] : 0;
    $class_vat_amount = $class_price_ht * ($vat_rate / 100);
    $class_price_ttc = $class_price_ht + $class_vat_amount;
    
    $classes_with_vat[$key] = $class;
    $classes_with_vat[$key]['price_ht'] = $class_price_ht;
    $classes_with_vat[$key]['vat_amount'] = $class_vat_amount;
    $classes_with_vat[$key]['price_ttc'] = $class_price_ttc;
}
?>

<!-- ===== HERO ===== -->
<section class="py-5 border-bottom hero-grad text-center" <?php echo (get_user_language() === 'arabic') ? 'dir="rtl"' : 'dir="ltr"'; ?>>
  <div class="container">
    <h1 class="display-5 fw-bold mb-2"><?php echo $school["name"] ?></h1>
    <p class="lead mb-0"><?php echo get_phrase("The No. 1 community to learn, practice, and network!") ?></p>
  </div>
</section>

<!-- ===== STATS BAR ===== -->
<section class="py-2 bg-wayo text-white text-uppercase fw-semibold small" <?php echo (get_user_language() === 'arabic') ? 'dir="rtl"' : 'dir="ltr"'; ?>>
  <div class="container">
    <div class="d-flex justify-content-center gap-5">
      <div><strong class="d-block fs-5"><?php echo $school["course_students_count"] ?></strong><span><?php echo get_phrase("Members") ?></span></div>
      <div><strong class="d-block fs-5"><?php echo $school["teachers_count"] ?></strong><span><?php echo get_phrase("Mentors") ?></span></div>
      <div><strong class="d-block fs-5"><?php echo $school['classes_count'] ?></strong><span><?php echo get_phrase("Classes") ?></span></div>
    </div>
  </div>
</section>

<!-- ===== HIGHLIGHT ===== -->
<?php if (!$this->session->userdata('user_id')): ?>
  <section class="py-4 bg-white border-top border-bottom" <?php echo (get_user_language() === 'arabic') ? 'dir="rtl"' : 'dir="ltr"'; ?>>
    <div class="container">
      <div class="d-flex align-items-center gap-3 flex-wrap">
        <div class="fs-1">🤝</div>
        <div class="flex-grow-1">
          <h2 class="h5 fw-bold mb-1"><?php echo get_phrase("Why join our community?") ?></h2>
          <p class="mb-0"><?php echo get_phrase("Access to") ?> <strong><?php echo get_phrase("6 private classes") ?></strong> <?php echo get_phrase("(SEO, Social Ads, Content, Growth, E-commerce, Automation) and connect with over") ?> <strong><?php echo get_phrase("6,000 marketers") ?></strong>.</p>
        </div>
        <a href="<?php echo site_url('admission/online_admission_student'); ?>" class="btn btn-wayo ms-auto"><?php echo get_phrase("Sign up") ?></a>
      </div>
  </section>
<?php endif; ?>

<!-- ===== MAIN GRID ===== -->
<main class="py-5" <?php echo (get_user_language() === 'arabic') ? 'dir="rtl"' : 'dir="ltr"'; ?>>
  <div class="container">
    <div class="row g-4">
      <!-- Main card -->
      <div class="col-lg-8">
        <div class="card shadow-sm border-0">
          <img class="card-img-top card-img-customer" src="<?php echo $this->user_model->get_school_cover($school_id); ?>">
          <div class="card-body p-4">
            <div class="d-flex align-items-center gap-3 mb-3">
              <div class="rounded-circle bg-light text-wayo d-grid place-items-center" style="width:54px;height:54px;">
                <!-- <i class="fa-solid fa-image"></i> -->
                <img style="max-width:45px; max-height:45px" src="<?php echo $this->user_model->get_school_image($school_id); ?>" alt="Logo communauté" class="logo-communaute">
              </div>
              <h2 class="h5 fw-bold mb-0"> <?php echo $school["name"] ?></h2>
            </div>

            <ul class="list-inline small text-muted mb-3">
              <?php if ($school["access"] > 0) { ?>
                <li class="list-inline-item me-3"><i class="fa-solid fa-lock text-wayo me-1"></i><?php echo get_phrase("Private") ?></li>
              <?php } else { ?>

                <li class="list-inline-item me-3"><i class="fa-solid fa-lock-open text-wayo me-1"></i><?php echo get_phrase("Public") ?></li>
              <?php } ?>
              <li class="list-inline-item me-3">
                <i class="fa-solid fa-bullhorn text-wayo me-1"></i>
                <?php echo get_phrase($school['category']); ?>
              </li>
              <li class="list-inline-item me-3"><i class="fa-solid fa-ticket text-wayo me-1"></i>
                <?php
                if ((float)$school['price'] > 0) {
                  echo number_format($school_price_ttc, 2) . " " . $settings_data['system_currency'];
                  if ($vat_rate > 0) {
                    echo " <small class='text-muted'>(TTC)</small>";
                  }
                } else {
                  echo get_phrase("Free");
                }
                ?>
              </li>
              <li class="list-inline-item me-3"><i class="fa-solid fa-user-group text-wayo me-1"></i><?php echo $school["course_students_count"] ?> <?php echo get_phrase("Members") ?></li>
              <!-- <li class="list-inline-item me-3"><i class="fa-solid fa-user-group text-wayo me-1"></i><?php if (!empty($school_creator)): ?>
                  <p><?php echo htmlspecialchars($school_creator['name']); ?></p>
              <?php endif; ?></li> -->
            </ul>

            <p class="mb-4">
              <?php echo $school["description"] ?>
            </p>

            <h3 class="h6 fw-bold mb-3"><?php echo get_phrase("Class schedule") ?></h3>
            <!-- CLASSES GRID -->
             <?php
              $currencies = isset($settings_school['system_currency']) ? $settings_school['system_currency'] : 'USD';
              ?>
            <div class="row row-cols-1 row-cols-md-2 row-cols-xl-2 g-3" id="classesGrid">
              <?php if (!empty($classes)): ?>
                <?php 
                  // Get community status once
                  $community_status = $this->user_model->check_student_status($school_id);
                  $is_logged_in = (bool)$this->session->userdata('user_id');
                ?>
                
                <?php foreach ($classes as $key => $class): ?>
                  <div class="col">
                    <div class="card h-100 border rounded-4 class-card position-relative"
                      data-title="<?php echo htmlspecialchars($class['name']); ?>"
                      data-photo="<?php echo htmlspecialchars($class['photo']); ?>"
                      data-desc="<?php echo isset($class['description']) ? htmlspecialchars($class['description']) : ''; ?>"
                      data-mentor="<?php echo htmlspecialchars($class['mentor']); ?>"
                      data-duration="<?php echo isset($class['duration']) ? htmlspecialchars($class['duration']) : ''; ?>"
                      data-level="<?php echo isset($class['level']) ? htmlspecialchars($class['level']) : ''; ?>"
                      data-start="<?php echo isset($class['date_debut']) ? htmlspecialchars($class['date_debut']) : ''; ?>"
                      data-end="<?php echo isset($class['date_fin']) ? htmlspecialchars($class['date_fin']) : ''; ?>"
                      data-price="<?php echo isset($class['price']) ? $class['price'] : 0; ?>"
                      data-currency="<?php echo $currencies; ?>"
                      data-cycle="<?php echo isset($class['cycle']) ? htmlspecialchars($class['cycle']) : ''; ?>"
                      data-free="<?php echo htmlspecialchars($class['nombre_max_membre']); ?>">

                      <div class="card-body d-flex flex-column gap-2">
                        <div class="d-flex justify-content-between align-items-center">
                          <h4 class="h6 m-0 fw-bold"><?php echo htmlspecialchars($class['name']); ?></h4>
                          <span class="badge rounded-pill border text-brand fw-bold price-badge">
                            <?php
                            if (isset($class['price']) && $class['price'] > 0) {
                              $class_price_ttc = isset($classes_with_vat[$key]['price_ttc']) ? $classes_with_vat[$key]['price_ttc'] : $class['price'];
                              echo number_format($class_price_ttc, 2) . ' ' . (isset($class['currency']) && !empty($class['currency']) ? $class['currency'] : $currencies);
                              if ($vat_rate > 0) {
                                echo " <small class='text-muted'>(TTC)</small>";
                              }
                              if (!empty($class['cycle'])) echo ' ' . $class['cycle'];
                            } else {
                              echo get_phrase('free');
                            }
                            ?>
                          </span>
                        </div>
                        <span class="fomo-badge">🔥 <?php echo get_phrase("Limited offer") ?></span>
                        <p class="small mb-1"><?php echo get_phrase("Class description") ?></p>

                        <?php if (!empty($class['date_debut']) && !empty($class['date_fin']) && 
                                  $class['date_debut'] !== '0000-00-00' && $class['date_fin'] !== '0000-00-00'): ?>

                                <div class="text-secondary small d-flex align-items-center gap-2">
                                  <i class="fa-regular fa-calendar text-brand"></i>
                                  <strong class="date-start"><?php echo (new DateTime($class['date_debut']))->format('d M. Y'); ?></strong> →
                                  <strong class="date-end"><?php echo (new DateTime($class['date_fin']))->format('d M. Y'); ?></strong>
                                </div>
                        <?php else: ?>
                                  &nbsp;
                        <?php endif; ?>
                        
                        <div class="small text-secondary">
                          <i class="fa-regular fa-circle-check me-1"></i>
                          <?php echo htmlspecialchars($class['nombre_max_membre']); ?> <?php echo get_phrase("Maximum_number") ?>
                        </div>
                        <div class="d-flex gap-2 mt-2">
                     <button
                        class="btn btn-outline-wayo btn-sm flex-fill openClassModal"
                        data-bs-toggle="modal"
                        data-bs-target="#classModal"
                        data-class-id="<?php echo $class['id']; ?>"
                        data-student-id="<?php echo $student_id; ?>"
                        data-currency="<?php echo $currencies; ?>"
                        data-class-price="<?php echo $class['price']; ?>"
                        data-class-enrolled="<?php
                            echo $this->db->get_where('enrols', [
                                'student_id' => $student_id,
                                'school_id' => $school_id,
                                'class_id' => $class['id']
                            ])->num_rows();
                        ?>"
                        data-school-id="<?php echo $school_id; ?>"
                        data-community-status="<?php echo $community_status; ?>"
                        data-school-price="<?php echo $school_price_ttc; ?>"
                        data-school-currency="<?php echo $settings_data['system_currency']; ?>"
                        data-is-logged-in="<?php echo $is_logged_in ? '1' : '0'; ?>"
                        data-user-role="<?php echo $this->session->userdata('admin_login') == 1 ? 'admin' : ($this->session->userdata('teacher_login') == 1 ? 'teacher' : ''); ?>"
                      >
                        <?php echo get_phrase("See more"); ?>
                      </button>
                          <?php
                            // Vérifier si déjà inscrit à la classe
                            $enrols_datas = $this->db->get_where('enrols', array(
                                'student_id' => $student_id,
                                'school_id' => $school_id,
                                'class_id' => $class['id']
                            ))->num_rows();

                            $enrols_max = $this->db->get_where('enrols', array(
                                'school_id' => $school_id,
                                'class_id' => $class['id']
                            ))->num_rows();

                            $nombre_max = isset($class['nombre_max_membre']) ? (int)$class['nombre_max_membre'] : 0;
                            $is_max_reached = ($nombre_max > 0 && $enrols_max >= $nombre_max);

                            // CASE 1 : déjà inscrit à la classe → Start course
                            if ($enrols_datas > 0): ?>
                              <button class="btn btn-outline-wayo-join fw-bold start-course-btn"
                                      data-class-id="<?php echo $class['id']; ?>">
                                <?php echo get_phrase("start_course"); ?>
                              </button>

                            <?php 
                            // CASE 2 : classe full → liste d’attente
                            elseif ($is_max_reached): ?>
                              <button type="button" class="btn btn-outline-secondary fw-bold" disabled>
                                <?php echo htmlspecialchars(get_phrase("waiting_list")); ?>
                              </button>

                            <?php 
                            // CASE 3 : pas encore inscrit
                            else:
                              $status = $this->user_model->check_student_status($school_id);

                              // CASE 3.1 : pas encore dans la communauté
                              if ($status == -1): ?>

                                <form action="<?php echo base_url('student/join_school/assigned/' . $school_id); ?>" method="post">
                                  <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>"
                                        value="<?php echo $this->security->get_csrf_hash(); ?>" />
                                  <input type="hidden" name="school_id" value="<?php echo $school_id; ?>" />
                                  <input type="hidden" name="price" value="<?php echo $school_price_ttc; ?>" />
                                  <input type="hidden" name="currency" value="<?php echo $settings_data['system_currency']; ?>" />

                                  <button type="submit" class="btn btn-wayo fw-bold <?php if(!$this->session->userdata('user_id')) echo 'join-community-login-popup'; ?>">
                                    <?php echo htmlspecialchars(get_phrase("join_community")); ?>
                                  </button>
                                </form>

                              <?php 
                              // CASE 3.2 : déjà dans la communauté (approuvé ou en attente)
                              elseif ($status == 0): 
                                // Status = 0 : Payé mais en attente d'approbation admin
                              ?>
                                <button type="button" class="btn btn-outline-secondary fw-bold" disabled>
                                  <?php echo htmlspecialchars(get_phrase("pending")); ?>
                                </button>

                              <?php 
                              // CASE 3.3 : Approuvé dans la communauté (status = 1)
                              else:

                                // NOUVELLE CONDITION : classe gratuite → Start course direct
                                if ((float)$class['price'] == 0): ?>
                                  
                                  <button class="btn btn-outline-wayo-join fw-bold start-course-btn"
                                          data-class-id="<?php echo $class['id']; ?>">
                                    <?php echo get_phrase("start_course"); ?>
                                  </button>

                                <?php else: ?>

                                  <!-- Classe payante : inscription normale -->
                                  <form action="<?php echo site_url('student/online_admission/assigned'); ?>" method="post">
                                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>"
                                          value="<?php echo $this->security->get_csrf_hash(); ?>" />

                                    <input type="hidden" name="student_id" value="<?php echo $student_id; ?>">
                                    <input type="hidden" name="school_id" value="<?php echo $school_id; ?>" />
                                    <input type="hidden" name="class_id" id="class_id" value="<?php echo $class['id']; ?>">
                                    <input type="hidden" name="price" value="<?php echo $class['price']; ?>" />
                                    <input type="hidden" name="currency" value="<?php echo $currencies; ?>" />

                                    <button type="submit" class="btn btn-wayo fw-bold">
                                      <?php echo htmlspecialchars(get_phrase("join_classe")); ?>
                                    </button>
                                  </form>

                                <?php endif; ?>

                              <?php endif; ?>

                            <?php endif; ?>

                        </div>
                      </div>
                    </div>
                  </div>
                <?php endforeach; ?>
              <?php else: ?>
                <div class="col-12">
                  <p><?php echo get_phrase("Aucune classe disponible pour cette communauté.") ?></p>
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
      <!-- Side card -->
      <aside class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
          <div class="bg-logo-communaute p-4 text-center">
            <div class="cercle-logo">
              <img src="<?php echo $this->user_model->get_school_image($school_id); ?>" alt="Logo communauté" class="logo-communaute">
            </div>
          </div>
          <div class="card-body">
            <h3 class="h6 fw-bold"><?php echo get_phrase("Accès communauté") ?></h3>
            <ul class="list-unstyled small text-muted mb-3">
              <li class="d-flex justify-content-between"><span><?php echo get_phrase("Members:") ?></span><span class="text-dark"><?php echo $school["course_students_count"] ?></span></li>
              <li class="d-flex justify-content-between"><span><?php echo get_phrase("Classes :") ?></span><span class="text-dark"><?php echo $school['classes_count'] ?> </span></li>
              <li class="d-flex justify-content-between"><span><?php echo get_phrase("Prix :") ?></span>
                <span class="text-wayo fw-bold">
                  <?php
                  if ((float)$school['price'] > 0) {
                    echo number_format($school_price_ttc, 2) . " " . $settings_data['system_currency'];
                    if ($vat_rate > 0) {
                      echo " <small class='text-muted'>(TTC)</small>";
                    }
                  } else {
                    
                    echo get_phrase("Free");
                  }
                  ?>
                </span>
              </li>
            </ul>
            <?php if ((int)$school['access'] > 0): ?>
              <div class="alert alert-warning small"><?php echo htmlspecialchars(get_phrase("Private community - join request only")); ?></div>
            <?php endif; ?>
            <div class="community-app-button">
              <a id="dashboard-community-app-button" href="<?php echo route('dashboard'); ?>" class="join-button text-uppercase text-center" style="display:none; text-decoration:none; padding: 10px 20px;"> <?php echo htmlspecialchars(get_phrase("community_app")); ?> </a>
            </div>

            <form action="<?php echo base_url('student/join_school/assigned/' . $school_id); ?>" method="post" id="join-community-form">
              <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
              <input type="hidden" name="school_id" value="<?php echo $school_id; ?>" />
              <input type="hidden" name="price" value="<?php echo $school_price_ttc; ?>" />
              <input type="hidden" name="currency" value="<?php echo $settings_data['system_currency']; ?>" />
              <input type="hidden" name="user_role" value="" id="user_role_hidden" />
              <button id="join-button" type="submit" class="join-button text-uppercase btn btn-wayo-join y w-100" style="display:none"> <?php echo htmlspecialchars(get_phrase("join_community")); ?> </button>
            </form>
            <button id="login-join-button" class="join-button text-uppercase  btn btn-wayo-join y w-100" style="display:none"> <?php echo htmlspecialchars(get_phrase("join_community")); ?> </button>
          </div>
        </div>
        <div class="row justify-content-center">

        </div>
      </aside>

    </div>
  </div>
</main>

<!-- Scroll to top -->
<button id="scrollTopBtn" class="btn btn-wayo rounded-circle position-fixed d-flex align-items-center justify-content-center"
  style="width:52px;height:52px;right:24px;bottom:24px;display:none" aria-label="Retour en haut">
  <i class="fa-solid fa-arrow-up text-white"></i>
</button>


<!-- MODAL DETAILS CLASS -->
<!-- MODAL DETAILS CLASS -->
<div class="modal fade" id="classModal" tabindex="-1" aria-labelledby="classModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content rounded-4">
      <div class="modal-header">
        <h5 class="modal-title fw-bold" id="classModalLabel"><?php echo get_phrase("Title") ?></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
      </div>
      <div class="modal-body">
        <div class="d-flex justify-content-center mb-3">
          <img id="classPhoto" src="" alt="Photo de classe" class="img-fluid rounded img-card-dt-communitites">
        </div>

        <ul class="list-inline small text-muted mb-3">
          <li class="list-inline-item me-3">
            <i class="fa-regular fa-calendar text-wayo me-2"></i>
            <span id="classDates"></span>
          </li>
          <li class="list-inline-item me-3">
            <i class="fa-regular fa-user text-wayo me-2"></i>
            <span id="classMentor"></span>
          </li>
          <li class="list-inline-item me-3">
            <i class="fa-regular fa-clock text-wayo me-2"></i>
            <span id="classDuration"></span>
          </li>
          <li class="list-inline-item me-3">
            <i class="fa-solid fa-star text-wayo me-2"></i>
            <span id="classLevel"></span>
          </li>
          <li class="list-inline-item me-3">
            <i class="fa-regular fa-circle-check text-wayo me-2"></i>
            <span id="classFree"></span>
          </li>
        </ul>

        <p id="classDescription" class="text-secondary mb-3"></p>
      </div>
      <div class="modal-footer d-flex justify-content-between">
        <span class="fw-bold text-brand" id="classPrice">—</span>

        <!-- CONTENEUR DU BOUTON : JS injectera Start/Join -->
        <div id="modalActionBtnContainer"></div>

      </div>
    </div>
  </div>
</div>





<!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script> -->

<script>
const base_url = "<?php echo base_url(); ?>";

document.addEventListener("DOMContentLoaded", function() {

  // Quand on clique sur "See More"
  document.querySelectorAll('.openClassModal').forEach(btn => {
    btn.addEventListener('click', function() {

      const classId = this.dataset.classId;
      const classPrice = parseFloat(this.dataset.classPrice);
      const enrolled = parseInt(this.dataset.classEnrolled); // 0 = pas payé, 1 = déjà payé
      const studentId = this.dataset.studentId;
      const schoolId = this.dataset.schoolId;
      const csrfName = "<?php echo $this->security->get_csrf_token_name(); ?>";
      const csrfHash = "<?php echo $this->security->get_csrf_hash(); ?>";
      const currency = this.dataset.currency || "<?php echo $settings_data['system_currency']; ?>";

      const container = document.getElementById('modalActionBtnContainer');
      container.innerHTML = ""; // reset le bouton

      const communityStatus = parseInt(this.dataset.communityStatus);
      const schoolPrice = parseFloat(this.dataset.schoolPrice);
      const schoolCurrency = this.dataset.schoolCurrency;
      const isLoggedIn = this.dataset.isLoggedIn === '1';
      const userRole = this.dataset.userRole || "<?php echo $this->session->userdata('admin_login') == 1 ? 'admin' : ($this->session->userdata('teacher_login') == 1 ? 'teacher' : ''); ?>";

          if (communityStatus === -1) {
          // Not a member -> Join Community
          // Toujours utiliser student/join_school (même pour admin/teacher qui rejoignent en tant que member)
          const form = document.createElement('form');
          let joinUrl = base_url + "student/join_school/assigned/" + schoolId;
          form.action = joinUrl;
          form.method = 'post';

          let btnClass = "btn btn-wayo fw-bold";
          if (!isLoggedIn) {
             btnClass += " join-community-login-popup";
          }
          // Changer le texte selon le rôle
          let buttonText = "<?php echo get_phrase('join_community'); ?>";
          if (userRole === 'admin' || userRole === 'teacher') {
            buttonText = "<?php echo get_phrase('join_as_member'); ?>";
          }

          form.innerHTML = `
            <input type="hidden" name="${csrfName}" value="${csrfHash}">
            <input type="hidden" name="school_id" value="${schoolId}">
            <input type="hidden" name="price" value="${schoolPrice}">
            <input type="hidden" name="currency" value="${schoolCurrency}">
            <input type="hidden" name="user_role" value="${userRole}">
            <button type="submit" class="${btnClass}">${buttonText}</button>
          `;
          container.appendChild(form);
          
            // Re-attach event listener for login popup if needed
            if (!isLoggedIn) {
                 const newBtn = form.querySelector('.join-community-login-popup');
                 if(newBtn){
                     newBtn.addEventListener("click", function(e) {
                      e.preventDefault();
                      
                      // Open login popup
                        const toggle = document.querySelector('.login-toggle');
                        if (toggle) {
                          toggle.click();
                        }

                        // Close modal
                        const applyModal = document.getElementById("classModal");
                        if (applyModal) {
                          const modalInstance = bootstrap.Modal.getInstance(applyModal);
                          if (modalInstance) {
                            modalInstance.hide();
                          }
                        }
                    });
                 }
            }

      } else if (communityStatus === 0) {
        // Status = 0: Paid but pending admin approval -> Show "En attente"
        const pendingBtn = document.createElement('button');
        pendingBtn.className = 'btn btn-outline-secondary fw-bold';
        pendingBtn.disabled = true;
        pendingBtn.textContent = "<?php echo get_phrase('pending'); ?>";
        container.appendChild(pendingBtn);
      } else if (enrolled > 0 || classPrice === 0) {
        // L'utilisateur a déjà payé ou la classe est gratuite → Start Course
        const startBtn = document.createElement('button');
        startBtn.className = 'btn btn-outline-wayo-join fw-bold start-course-btn';
        startBtn.dataset.classId = classId;
        startBtn.textContent = "<?php echo get_phrase('start_course'); ?>";
        container.appendChild(startBtn);
      } else {
        // Classe payante → Join Class (paiement)
        const form = document.createElement('form');
        form.action = base_url + "student/online_admission/assigned";
        form.method = 'post';
        form.innerHTML = `
          <input type="hidden" name="${csrfName}" value="${csrfHash}">
          <input type="hidden" name="student_id" value="${studentId}">
          <input type="hidden" name="school_id" value="${schoolId}">
          <input type="hidden" name="class_id" value="${classId}">
          <input type="hidden" name="price" value="${classPrice}">
          <input type="hidden" name="currency" value="${currency}">
          <button type="submit" class="btn btn-wayo fw-bold"><?php echo get_phrase('join_class'); ?></button>
        `;
        container.appendChild(form);
      }
    });
  });

  // Start Course redirection
  document.addEventListener('click', function(e){
    if (!e.target.classList.contains('start-course-btn')) return;
    const classId = e.target.dataset.classId;
    if (!classId) return;
    window.location.href = base_url + "student/courses/" + classId;
  });

});

</script>
<script>
document.addEventListener("DOMContentLoaded", function() {

  // Bouton login déjà existant
  const loginJoinBtn = document.getElementById("login-join-button");

  // Tous les boutons avec la classe join-community-login
  const joinCommunityBtns = document.querySelectorAll(".join-community-login");

  function openPopup() {
    const toggle = document.querySelector('.login-toggle');
    if (toggle) {
      toggle.click(); // ouvre le popup
    }
  }

  // Pour login-join-button
  if (loginJoinBtn) {
    loginJoinBtn.addEventListener("click", openPopup);
  }

  // Pour tous les boutons avec la classe join-community-login
  joinCommunityBtns.forEach(btn => {
    btn.addEventListener("click", function(e) {
      e.preventDefault();       // empêche submit
      openPopup();              // ouvre popup
    });
  });

});
</script>

<!-- script de button join community affichage poupup and hide modal -->
<script>
  document.addEventListener("DOMContentLoaded", function() {

  // ID de ton modal Bootstrap à fermer
  const applyModal = document.getElementById("classModal"); 

  //  boutons dans le footer avec cette classe
  const popupBtns = document.querySelectorAll(".join-community-login-popup");

  function openPopupAndCloseModal() {

    // Ouvrir le popup login
    const toggle = document.querySelector('.login-toggle');
    if (toggle) {
      toggle.click();
    }

    // Fermer le modal
    if (applyModal) {
      const modalInstance = bootstrap.Modal.getInstance(applyModal);
      if (modalInstance) {
        modalInstance.hide();
      }
    }
  }
  // l’événement sur tous les boutons
  popupBtns.forEach(btn => {
    btn.addEventListener("click", function(e) {
      e.preventDefault();      
      openPopupAndCloseModal();
    });
  });

});

</script>
<script>
  /* ===== Utilitaires prix ===== */
document.querySelectorAll('.class-card').forEach(card => {
  const badge = card.querySelector('.price-badge');
  if (badge) badge.textContent = formatPrice(card.dataset.price);
});


  /* ===== Scroll-to-top ===== */
  const topBtn = document.getElementById('scrollTopBtn');
  window.addEventListener('scroll', () => {
    if (!topBtn) return;
    topBtn.style.display = window.scrollY > 600 ? 'flex' : 'none';
  });
  topBtn?.addEventListener('click', () => window.scrollTo({
    top: 0,
    behavior: 'smooth'
  }));
</script>


<script>
  if (document.getElementById("login-join-button")) {
    document.getElementById("login-join-button").addEventListener("click", function() {
      document.querySelector('.login-toggle').click();
    });
  }

  $(document).ready(function() {
    $('.courses-slider').slick({
      fade: true,
      autoplay: true,
      autoplaySpeed: 4000,
      arrows: false,
      infinite: true,
      pauseOnFocus: false,
      adaptiveHeight: true,
      slidesToShow: 1,
      slidesToScroll: 1,
      centerMode: false,
      /* No centering to avoid gaps */
      variableWidth: false /* Consistent full width */
    });

    const descs = document.querySelectorAll(".course-slider-description");
    descs.forEach(desc => {
      const pars = desc.getElementsByTagName("p");
      Array.from(pars).forEach(par => {
        par.classList.add("text-white");
        par.classList.add("text-center");
      });
    });
  });
</script>

<script>
  $(document).ready(function() {
    // Variable pour stocker le rôle de l'utilisateur dans cette communauté
    var userRoleInThisSchool = null;
    
    function updateButton() {
      $.ajax({
        url: "<?php echo base_url('home/check_student_status_ajax/' . $school_id); ?>",
        method: "GET",
        dataType: "json",
        success: function(response) {
          var button = $("#join-button");
          var button_paye = $("#paye-button");

          var loginButton = $("#login-join-button");
          var dashboardCommunityAppButton = $("#dashboard-community-app-button");
          var form = $("#join-community-form");
          var userRoleHidden = $("#user_role_hidden");

          if (response.status === null) {
            loginButton.show();
            button.hide();
            dashboardCommunityAppButton.hide();
          } else {
            loginButton.hide();
            button.show();
            if (response.status == 1) {
              // L'utilisateur fait partie de cette communauté (admin, teacher ou membre approuvé)
              button.hide(); // Cacher le bouton join
              dashboardCommunityAppButton.show();
              
              // Stocker le rôle pour le clic sur Community App
              userRoleInThisSchool = response.role_in_this_school || response.user_role || 'student';
              
            } else {
              button.show();
              dashboardCommunityAppButton.hide();
              if (response.status == 0) {
                button.prop("disabled", true).text("<?php echo htmlspecialchars(get_phrase('pending')); ?>");
              } else if (response.status == 2) {
                // Si admin ou teacher d'une AUTRE communauté, montrer "join as a member"
                if (response.user_role === 'admin' || response.user_role === 'teacher') {
                  button.prop("disabled", false)
                        .text("<?php echo htmlspecialchars(get_phrase('join_as_member')); ?>")
                        .data("join-as-member", true);
                  // Garder l'action vers student/join_school
                  form.attr("action", base_url + "student/join_school/assigned/<?php echo $school_id; ?>");
                  userRoleHidden.val(response.user_role);
                } else {
                  button.prop("disabled", true).text("<?php echo htmlspecialchars(get_phrase('no_student_account')); ?>");
                }
              } else {
                button.prop("disabled", false).text("<?php echo htmlspecialchars(get_phrase('join_community')); ?>");

                $(".btn-outline-wayo-join, #paye-button").each(function() {
                  $(this).prop("disabled", false)
                    .text("<?php echo htmlspecialchars(get_phrase('join_community')); ?>")
                    .removeClass("btn-outline-wayo-join")
                    .addClass("btn-wayo");
                });

              }
            }
          }
        }
      });
    }
    
    // Gérer le clic sur le bouton Community App pour switcher vers cette communauté
    $("#dashboard-community-app-button").on("click", function(e) {
      e.preventDefault();
      
      var schoolId = "<?php echo $school_id; ?>";
      var role = userRoleInThisSchool || 'student';
      
      // Switcher vers cette communauté puis rediriger
      $.ajax({
        url: "<?php echo site_url('home/switch_community_role'); ?>",
        method: "POST",
        dataType: "json",
        data: {
          school_id: schoolId,
          role: role,
          <?php echo $this->security->get_csrf_token_name(); ?>: "<?php echo $this->security->get_csrf_hash(); ?>"
        },
        success: function(response) {
          if (response.status === 'success') {
            window.location.href = response.redirect_url;
          } else {
            // En cas d'erreur, rediriger quand même vers le dashboard
            window.location.href = "<?php echo route('dashboard'); ?>";
          }
        },
        error: function() {
          // En cas d'erreur, rediriger vers le dashboard
          window.location.href = "<?php echo route('dashboard'); ?>";
        }
      });
    });
    
    updateButton();
    setInterval(updateButton, 5000);
  });
</script>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const classCards = document.querySelectorAll('.class-card');
    const modal = document.getElementById('classModal');

    classCards.forEach(card => {
      card.querySelector('[data-bs-toggle="modal"]').addEventListener('click', function() {

        function formatDate(dateString) {
          const date = new Date(dateString);
          const options = {
            day: '2-digit',
            month: 'short',
            year: 'numeric'
          };
          return date.toLocaleDateString('fr-FR', options);
        }

        const mentorName = card.dataset.mentor || "—";
        // Mettre la première lettre de chaque mot en majuscule et le reste en minuscules
        const capitalizedMentor = mentorName
          .toLowerCase()
          .split(' ')
          .map(word => word.charAt(0).toUpperCase() + word.slice(1))
          .join(' ');

        document.getElementById('classMentor').textContent = capitalizedMentor;

        // Remplir les champs du modal
        modal.querySelector('.modal-title').textContent = card.dataset.title;
        modal.querySelector('#classPhoto').src = '<?php echo base_url("uploads/class/"); ?>' + card.dataset.photo;
        modal.querySelector('#classDescription').textContent = card.dataset.desc;
        modal.querySelector('#classDuration').textContent = card.dataset.duration;
        modal.querySelector('#classLevel').textContent = card.dataset.level;

        // Dates
        const startText = '<?php echo get_phrase("From"); ?>';
        const endText = '<?php echo get_phrase("to"); ?>';

        const startDate = (card.dataset.start && card.dataset.start !== '0000-00-00' && card.dataset.start !== '') 
                  ? formatDate(card.dataset.start) 
                  : null;

        const endDate = (card.dataset.end && card.dataset.end !== '0000-00-00' && card.dataset.end !== '') 
                        ? formatDate(card.dataset.end) 
                        : null;

        if (startDate && endDate) {
          const startText = '<?php echo get_phrase("From"); ?>';
          const endText = '<?php echo get_phrase("to"); ?>';
          modal.querySelector('#classDates').parentElement.style.display = 'inline-block';
          modal.querySelector('#classDates').textContent = `${startText} ${startDate} ${endText} ${endDate}`;
        } else {
          modal.querySelector('#classDates').parentElement.style.display = 'none';
        }
        
        //modal.querySelector('#classDates').textContent = `${startText} ${startDate} ${endText} ${endDate}`;

        // Nombre max
        const nombre_max = card.dataset.free;
        modal.querySelector('#classFree').textContent = nombre_max + ' <?php echo get_phrase("Maximum_number"); ?>';

        // Prix avec TVA
        const classPrice = parseFloat(card.dataset.price) || 0;
        let displayPrice = 'Gratuit';
        if (classPrice > 0) {
          // Calculer le prix TTC pour la modal
          const vatRate = <?php echo $vat_rate; ?>;
          const classPriceTTC = vatRate > 0 ? classPrice * (1 + (vatRate / 100)) : classPrice;
          displayPrice = classPriceTTC.toFixed(2) + ' ' + card.dataset.currency + ' ' + card.dataset.cycle;
          if (vatRate > 0) {
            displayPrice += ' (TTC)';
          }
        }
        modal.querySelector('#classPrice').textContent = displayPrice;
      });
    });
  });
</script>
<script>
  // // CTA "S’inscrire" depuis la modal
  // modalApplyBtn?.addEventListener('click', () => {
  //   alert(`Inscription à “${modalTitle.textContent}” — ${modalPrice.textContent}`);
  //   bsModal?.hide();
  // });
</script>
