<?php if (get_common_settings('recaptcha_status')): ?>
  <script src="https://www.google.com/recaptcha/api.js" async defer></script>
<?php endif; ?>

<?php
?>

<!-- ===== HERO ===== -->
<section class="py-5 border-bottom hero-grad text-center">
  <div class="container">
    <h1 class="display-5 fw-bold mb-2"><?php echo $school["name"] ?></h1>
    <p class="lead mb-0"><?php echo get_phrase("The No. 1 community to learn, practice, and network!") ?></p>
  </div>
</section>

<!-- ===== STATS BAR ===== -->
<section class="py-2 bg-wayo text-white text-uppercase fw-semibold small">
  <div class="container">
    <div class="d-flex justify-content-center gap-5">
      <div><strong class="d-block fs-5"><?php echo $school["course_students_count"] ?></strong><span><?php echo get_phrase("Members") ?></span></div>
      <div><strong class="d-block fs-5"><?php echo $school["teachers_count"] ?></strong><span><?php echo get_phrase("Mentors") ?></span></div>
      <div><strong class="d-block fs-5"><?php echo $school['classes_count']?></strong><span><?php echo get_phrase("Classes") ?></span></div>
    </div>
  </div>
</section>

<!-- ===== HIGHLIGHT ===== -->
<section class="py-4 bg-white border-top border-bottom">
  <div class="container">
    <div class="d-flex align-items-center gap-3 flex-wrap">
      <div class="fs-1">🤝</div>
      <div class="flex-grow-1">
        <h2 class="h5 fw-bold mb-1"><?php echo get_phrase("Why join our community?") ?></h2>
        <p class="mb-0"><?php echo get_phrase("Access to") ?> <strong><?php echo get_phrase("6 private classes") ?></strong> <?php echo get_phrase("(SEO, Social Ads, Content, Growth, E-commerce, Automation) and connect with over") ?> <strong><?php echo get_phrase("6,000 marketers") ?></strong>.</p>
      </div>
      <a href="#" class="btn btn-wayo ms-auto"><?php echo get_phrase("Sign up") ?></a>
  </div>
</section>

<!-- ===== MAIN GRID ===== -->
<main class="py-5">
  <div class="container">
    <div class="row g-4">
      <!-- Main card -->
      <div class="col-lg-8">
        <div class="card shadow-sm border-0">
          <img class="card-img-top card-img-customer" src="<?php echo $this->user_model->get_school_cover($school_id); ?>">
          <div class="card-body p-4">
            <div class="d-flex align-items-center gap-3 mb-3">
              <div class="rounded-circle bg-light text-wayo d-grid place-items-center" style="width:54px;height:54px;">
                <i class="fa-solid fa-image"></i>
              </div>
              <h2 class="h5 fw-bold mb-0"> <?php echo $school["name"] ?></h2>
            </div>

            <ul class="list-inline small text-muted mb-3">
               <?php if ($school["access"] > 0) { ?>
                  <!-- <i class="fa-solid fa-lock text-wayo me-1"> -->
                 <li class="list-inline-item me-3"><i class="fa-solid fa-lock text-wayo me-1"></i><?php echo get_phrase("Private") ?></li>
                <?php } else { ?>
                  
                  <li class="list-inline-item me-3"><i class="fa-solid fa-lock-open text-wayo me-1"></i><?php echo get_phrase("Public") ?></li>
                <?php } ?>
              <li class="list-inline-item me-3"><i class="fa-solid fa-bullhorn text-wayo me-1"></i><?php echo $school['category'] ?></li>
              <li class="list-inline-item me-3"><i class="fa-solid fa-ticket text-wayo me-1"></i><?php echo get_phrase("Free") ?></li>
              <li class="list-inline-item me-3"><i class="fa-solid fa-user-group text-wayo me-1"></i><?php echo $school["course_students_count"] ?> <?php echo get_phrase("Members") ?></li>

              <!-- <li class="list-inline-item"><i class="fa-solid fa-user-tie text-wayo me-1"></i><?php echo get_phrase("Aymane") ?></li> -->
            </ul>

            <p class="mb-4">
              <?php echo $school["description"] ?>
            </p>

            <h3 class="h6 fw-bold mb-3"><?php echo get_phrase("Class schedule") ?></h3>
            <!-- CLASSES GRID -->
            <div class="row row-cols-1 row-cols-md-2 row-cols-xl-2 g-3" id="classesGrid">
              <?php if (!empty($classes)): ?>
                <?php foreach ($classes as $class): ?>
                  <div class="col">
                    <div class="card h-100 border rounded-4 class-card position-relative"
                        data-title="<?php echo htmlspecialchars($class['name']); ?>"
                        data-plan="<?php echo isset($class['plan']) ? htmlspecialchars($class['plan']) : ''; ?>"
                        data-price="<?php echo isset($class['price']) ? $class['price'] : 0; ?>"
                        data-currency="<?php echo isset($class['currency']) ? htmlspecialchars($class['currency']) : 'DH'; ?>"
                        data-cycle="<?php echo isset($class['cycle']) ? htmlspecialchars($class['cycle']) : ''; ?>"
                        data-free="<?php echo isset($class['free_places']) ? $class['free_places'] : 0; ?>"
                        data-desc="<?php echo isset($class['description']) ? htmlspecialchars($class['description']) : ''; ?>"
                        data-mentor="<?php echo isset($class['mentor']) ? htmlspecialchars($class['mentor']) : 'À définir'; ?>"
                        data-duration="<?php echo isset($class['duration']) ? htmlspecialchars($class['duration']) : ''; ?>"
                        data-level="<?php echo isset($class['level']) ? htmlspecialchars($class['level']) : ''; ?>"
                        data-start="<?php echo isset($class['start_date']) ? htmlspecialchars($class['start_date']) : ''; ?>"
                        data-end="<?php echo isset($class['end_date']) ? htmlspecialchars($class['end_date']) : ''; ?>"
                        data-video="<?php echo isset($class['video']) ? htmlspecialchars($class['video']) : ''; ?>">

                      <div class="card-body d-flex flex-column gap-2">
                        <div class="d-flex justify-content-between align-items-center">
                          <h4 class="h6 m-0 fw-bold"><?php echo htmlspecialchars($class['name']); ?></h4>
                          <span class="badge rounded-pill border text-brand fw-bold price-badge">
                            <?php 
                              if (isset($class['price']) && $class['price'] > 0) {
                                echo $class['price'].' '.(isset($class['currency']) ? $class['currency'] : 'DH');
                                if (!empty($class['cycle'])) echo ' '.$class['cycle'];
                              } else {
                                echo "Gratuit";
                              }
                            ?>
                          </span>
                          
                        </div>
                        <span class="fomo-badge">🔥 <?php echo get_phrase("Limited offer") ?></span>

                        <p class="small mb-1"><?php echo get_phrase("Class description") ?></p>

                        <div class="text-secondary small d-flex align-items-center gap-2">
                          <i class="fa-regular fa-calendar text-brand"></i>
                          <strong class="date-start">
                            01 oct. 2025</strong>
                            → 
                          <strong class="date-end">12 nov. 2025</strong>
                        </div>

                        <div class="small text-secondary">
                          <i class="fa-regular fa-circle-check me-1"></i>
                          25 places gratuites
                        </div>
                        <div class="d-flex gap-2 mt-2">
                            <button class="btn btn-outline-wayo btn-sm flex-fill" data-bs-toggle="modal" data-bs-target="#classModal"><?php echo get_phrase("See more") ?></button>
                            <button class="btn btn-wayo btn-sm flex-fill btn-apply"><?php echo get_phrase("Sign up") ?></button>
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
          <!-- <div class="ratio ratio-16x9">
            <iframe src="https://www.youtube.com/embed/r8cCk-HXcMQ?rel=0&modestbranding=1"
                    title="Présentation Wayo" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                    allowfullscreen></iframe>
          </div> -->
          <div class="text-center py-3 bg-logo-communaute">
            <img src="<?php echo $this->user_model->get_school_image($school_id); ?>" alt="Logo communauté" class="logo-communaute" >
          </div>
           <div class="card-body">
            <h3 class="h6 fw-bold"><?php echo get_phrase("Accès communauté") ?></h3>
            <ul class="list-unstyled small text-muted mb-3">
              <li class="d-flex justify-content-between"><span><?php echo get_phrase("Members:") ?></span><span class="text-dark"><?php echo $school["course_students_count"] ?></span></li>
              <li class="d-flex justify-content-between"><span><?php echo get_phrase("Classes :") ?></span><span class="text-dark"><?php echo $school['classes_count'] ?> </span></li>
              <li class="d-flex justify-content-between"><span><?php echo get_phrase("Prix :") ?></span><span class="text-wayo fw-bold"><?php echo get_phrase("Free") ?></span></li>
            </ul>
            <!-- <a href="#" class="btn btn-wayo w-100">Rejoindre gratuitement</a> -->
             <div class="community-app-button">
              <a id="dashboard-community-app-button" href="<?php echo route('dashboard'); ?>" class="join-button text-uppercase text-center" style="display:none; text-decoration:none; padding: 10px 20px;"> <?php echo htmlspecialchars(get_phrase("community_app")); ?> </a>
             </div>
            <form action="<?php echo base_url('home/join_school/' . $school_id); ?>" method="post">
                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
                <button id="join-button" type="submit" class="join-button text-uppercase btn btn-wayo-join y w-100" style="display:none"> <?php echo htmlspecialchars(get_phrase("join")); ?> </button>
            </form>
                <button id="login-join-button" class="join-button text-uppercase  btn btn-wayo-join y w-100" style="display:none"> <?php echo htmlspecialchars(get_phrase("join")); ?> </button>
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

<!-- ===== MODAL Bootstrap (unique, alimentée dynamiquement) ===== -->
<!-- <div class="modal fade" id="classModal" tabindex="-1" aria-labelledby="modalTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0">
      <div class="modal-header">
        <h5 class="modal-title fw-bold" id="modalTitle">Titre</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
      </div>
      <div class="modal-body">
        <ul class="list-inline small text-muted mb-3">
          <li class="list-inline-item me-3"><i class="fa-regular fa-user text-wayo me-1"></i><span id="modalMentor">Mentor</span></li>
          <li class="list-inline-item me-3"><i class="fa-regular fa-clock text-wayo me-1"></i><span id="modalDuration">Durée</span></li>
          <li class="list-inline-item"><i class="fa-solid fa-ranking-star text-wayo me-1"></i><span id="modalLevel">Niveau</span></li>
        </ul>
        <p class="mb-0" id="modalDesc">Description…</p>
      </div>
      <div class="modal-footer d-flex justify-content-between">
        <span class="fw-bold text-wayo" id="modalPrice">Gratuit</span>
        <button type="button" class="btn btn-wayo" id="modalApplyBtn">S’inscrire</button>
      </div>
    </div>
  </div>
</div> -->

<!-- MODAL -->
  <div class="modal fade" id="classModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content rounded-4">
        <div class="modal-header">
          <h5 class="modal-title fw-bold" id="modalTitle"><?php echo get_phrase("Title") ?></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
        </div>
        <div class="modal-body">
          <div class="ratio ratio-16x9 rounded-3 overflow-hidden mb-3" id="modalVideoWrap"></div>

          <ul class="list-inline small text-muted mb-3">
          <li class="list-inline-item me-3"><i class="fa-regular fa-user text-wayo me-1"></i><span id="modalMentor"><?php echo get_phrase("Mentor") ?></span></li>
          <li class="list-inline-item me-3"><i class="fa-regular fa-clock text-wayo me-1"></i><span id="modalDuration"><?php echo get_phrase("Duration") ?></span></li>
          <li class="list-inline-item"><i class="fa-solid fa-ranking-star text-wayo me-1"></i><span id="modalLevel"><?php echo get_phrase("Niveau") ?></span></li>
        </ul>
          <div class="fw-semibold mb-2"><i class="fa-regular fa-calendar text-brand me-1"></i>
            <?php echo get_phrase("From") ?> <span id="modalStart">—</span> <?php echo get_phrase("to") ?> <span id="modalEnd">—</span>
          </div>

          <p id="modalDesc" class="mb-0">Description…</p>
        </div>
        <div class="modal-footer d-flex justify-content-between">
          <span class="fw-bold text-brand" id="modalPrice">—</span>
          <button class="btn btn-brand fw-bold" id="modalApplyBtn" type="button"><?php echo get_phrase("Sign up") ?></button>
        </div>
      </div>
    </div>
  </div>

<!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script> -->

<script>
/* ===== Utilitaires prix ===== */
const formatPrice = (p) => (Number(p || 0) > 0 ? `${Number(p)} €` : 'Gratuit');

/* ===== Badges prix auto (selon data-price) ===== */
document.querySelectorAll('.class-card').forEach(card => {
  const badge = card.querySelector('.price-badge');
  if (badge) badge.textContent = formatPrice(card.dataset.price);
});

/* ===== Modal Bootstrap alimentée dynamiquement ===== */
const modalEl = document.getElementById('classModal');
const bsModal  = modalEl ? new bootstrap.Modal(modalEl) : null;

const modalTitle    = document.getElementById('modalTitle');
const modalMentor   = document.getElementById('modalMentor');
const modalDuration = document.getElementById('modalDuration');
const modalLevel    = document.getElementById('modalLevel');
const modalDesc     = document.getElementById('modalDesc');
const modalPrice    = document.getElementById('modalPrice');
const modalApplyBtn = document.getElementById('modalApplyBtn');
const modalVideoWrap= document.getElementById('modalVideoWrap');

/* ===== Vidéo statique (fixe) ===== */
const staticVideoUrl = "https://www.youtube.com/embed/r8cCk-HXcMQ?si=qL5bMJii7leDH7z1"; 
// 👉 remplace ce lien par la vidéo que tu veux

document.querySelectorAll('.class-card').forEach(card => {
  // ouvrir via bouton "Voir plus"
  card.querySelector('[data-bs-target="#classModal"]')?.addEventListener('click', () => {
    modalTitle.textContent    = card.dataset.title || 'Classe';
    modalMentor.textContent   = card.dataset.mentor || 'Mentor à venir';
    modalDuration.textContent = card.dataset.duration || 'Durée à venir';
    modalLevel.textContent    = card.dataset.level || 'Tous niveaux';
    modalDesc.textContent     = card.dataset.desc || 'Description de classe';
    modalPrice.textContent    = formatPrice(card.dataset.price);

    // afficher la vidéo fixe
    modalVideoWrap.innerHTML = `<iframe src="${staticVideoUrl}" 
      class="w-100 h-100 border-0" 
      allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
      allowfullscreen></iframe>`;
  });

  // CTA "S’inscrire" direct
  card.querySelector('.btn-apply')?.addEventListener('click', () => {
    const title = card.dataset.title || 'Classe';
    const price = formatPrice(card.dataset.price);
    alert(`Inscription à “${title}” — ${price}`);
  });
});

// Nettoyer vidéo quand modal se ferme (évite le son qui continue)
modalEl?.addEventListener('hidden.bs.modal', () => {
  modalVideoWrap.innerHTML = '';
});

// CTA "S’inscrire" depuis la modal
modalApplyBtn?.addEventListener('click', () => {
  alert(`Inscription à “${modalTitle.textContent}” — ${modalPrice.textContent}`);
  bsModal?.hide();
});

/* ===== Scroll-to-top ===== */
const topBtn = document.getElementById('scrollTopBtn');
window.addEventListener('scroll', () => {
  if (!topBtn) return;
  topBtn.style.display = window.scrollY > 600 ? 'flex' : 'none';
});
topBtn?.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));
</script>


<script>
  if (document.getElementById("login-join-button")) {
    document.getElementById("login-join-button").addEventListener("click", function () {
      document.querySelector('.login-toggle').click();
    });
  }

  $(document).ready(function () {
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
    centerMode: false, /* No centering to avoid gaps */
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
  $(document).ready(function () {
    function updateButton() {
      $.ajax({
            url: "<?php echo base_url('home/check_student_status_ajax/' . $school_id); ?>",
            method: "GET",
            dataType: "json",
            success: function (response) {
                var button = $("#join-button");
                var loginButton = $("#login-join-button");
                var dashboardCommunityAppButton = $("#dashboard-community-app-button");

          if (response.status === null) {
                    loginButton.show();
                    button.hide();
                    dashboardCommunityAppButton.hide();
                } else {
                    loginButton.hide();
                    button.show();
                    if (response.status == 1) {
                        button.prop("disabled", true).text("<?php echo htmlspecialchars(get_phrase('enrolled')); ?>");
                        dashboardCommunityAppButton.show();
                    } else {
                        button.show();
                        dashboardCommunityAppButton.hide();
                        if (response.status == 0) {
                            button.prop("disabled", true).text("<?php echo htmlspecialchars(get_phrase('pending')); ?>");
                        } else if (response.status == 2) {
                            button.prop("disabled", true).text("<?php echo htmlspecialchars(get_phrase('no_student_account')); ?>");
                        } else {
                            button.prop("disabled", false).text("<?php echo htmlspecialchars(get_phrase('join')); ?>");
                        }
                    }
                }
            }
      });
    }

    updateButton();
    setInterval(updateButton, 5000);
  });
</script>

