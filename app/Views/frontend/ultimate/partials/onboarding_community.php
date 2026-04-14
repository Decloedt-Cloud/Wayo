<!-- Overlay -->
<div id="onbOverlay" class="onb-overlay"></div>

<!-- Modal -->
<div id="onbModal" class="onb-modal" inert>
  <div class="onb-card">

    <!-- Close -->
    <button class="onb-close" aria-label="<?php echo get_phrase("Fermer"); ?>">✕</button>

    <!-- Header -->
    <div class="onb-header">
      <div class="d-flex align-items-center gap-2">
        <img alt="Wayo" style="height:24px;" src="<?php echo $logo_light; ?>" alt="<?php echo $system_name; ?>">
        <div class="onb-brand"><?php echo get_phrase("Wayo"); ?></div>
      </div>

      <div class="onb-progress">
        <div class="onb-progress-bar"></div>
      </div>
    </div>

    <!-- Body -->
    <div class="onb-body" <?php echo (get_user_language() === 'arabic') ? 'dir="rtl"' : 'dir="ltr"'; ?>>

      <!-- Step 1 -->
      <div class="onb-step is-active">
        <h2 class="h4 mb-3"><?php echo get_phrase("Welcome_emoji"); ?> 👋</h2>
        <p class="mb-3">
          <?php echo get_phrase("Is_this_your_first_time_using"); ?>
          <strong><?php echo get_phrase("Wayo"); ?></strong> ?
          <?php echo get_phrase("Follow_this_guide_to_get_started"); ?>.
        </p>
        <ul style="padding-left:1.2rem;margin-bottom:0;">
          <li class="mb-2">
            <?php echo get_phrase("You_can_close_this_guide_at_any_time_button"); ?>
            <strong>(✕)</strong> <?php echo get_phrase("or_button"); ?> <strong><?php echo get_phrase("(skip)"); ?></strong>
          </li>
          <li>
            <?php echo get_phrase("Proceed_with"); ?>
            <strong><?php echo get_phrase("Next"); ?></strong>
            <?php echo get_phrase("or_skip_with"); ?>
            <strong><?php echo get_phrase("Skip"); ?></strong>
          </li>
        </ul>
      </div>

      <!-- Step 2 -->
      <div class="onb-step">
        <h2 class="h4"><?php echo get_phrase("Create_your_profile_emoji"); ?> 👤</h2>
        <p><?php echo get_phrase("Start_by_creating_your_profile_default_admin_add_mentors"); ?></p>
        <ul style="padding-left:1.2rem;margin-bottom:0;">
          <li class="mb-2"><?php echo get_phrase("Fill_in_Name_email_phone_and_password"); ?></li>
          <li><?php echo get_phrase("By_joining_the_Dashboard_create_classes_and_courses"); ?></li>
        </ul>
      </div>

      <!-- Step 3 -->
      <div class="onb-step">
        <h2 class="h4"><?php echo get_phrase("Set_up_your_community_emoji"); ?> 🏛️</h2>
        <p><?php echo get_phrase("Choose_name_description_logo_cover"); ?></p>
        <ul style="padding-left:1.2rem;margin-bottom:0;">
          <li class="mb-2"><strong><?php echo get_phrase("Name_&_Description"); ?>:</strong> <?php echo get_phrase("Name_your_community_and_describe_what_you_do"); ?></li>
          <li class="mb-2"><strong><?php echo get_phrase("Logo"); ?>:</strong> <?php echo get_phrase("Preferably_square_recommended_512x512"); ?></li>
          <li class="mb-2"><strong><?php echo get_phrase("Cover"); ?>:</strong> <?php echo get_phrase("16_9_format_example_1920x600"); ?></li>
          <li class="mb-2"><strong><?php echo get_phrase("Visibility"); ?>:</strong> <?php echo get_phrase("Public_or_private_access_requires_admin_approval"); ?></li>
        </ul>
      </div>

      

      <!-- Step 5 -->
      <div class="onb-step">
        <h2 class="h4"><?php echo get_phrase("Summary_emoji"); ?> 📝</h2>
        <p><?php echo get_phrase("Check_your_information_and_publish_your_community"); ?></p>
      </div>

    </div>

    <!-- Footer -->
    <div class="onb-footer d-flex justify-content-between align-items-center">
      <button class="btn btn-outline-primary-custom onb-skip">
        <?php echo get_phrase("Skip"); ?>
      </button>

      <div class="d-flex gap-2">
        <button class="btn btn-outline-primary-custom onb-prev" disabled>
          <?php echo get_phrase("Previous"); ?>
        </button>
        <button class="btn btn-primary-custom onb-next">
          <?php echo get_phrase("Next"); ?>
        </button>
      </div>
    </div>

  </div>
</div>

<!-- JS -->
<script>
document.addEventListener("DOMContentLoaded", () => {

  const ONBOARDING_KEY = "wayo_onboarding_seen";

  const overlay = document.getElementById("onbOverlay");
  const modal = document.getElementById("onbModal");
  const steps = modal.querySelectorAll(".onb-step");
  const nextBtn = modal.querySelector(".onb-next");
  const prevBtn = modal.querySelector(".onb-prev");
  const skipBtn = modal.querySelector(".onb-skip");
  const closeBtn = modal.querySelector(".onb-close");
  const progress = modal.querySelector(".onb-progress-bar");

  let current = 0;

  function openOnboarding() {
    overlay.classList.add("active");
    modal.classList.add("active");
    modal.removeAttribute("inert"); // Rendre le modal interactif
    showStep(0);
  }

  function closeOnboarding() {
    overlay.classList.remove("active");
    modal.classList.remove("active");
    modal.setAttribute("inert", ""); // Désactiver l'interactivité
  }

  function showStep(index) {
    steps.forEach((step, i) =>
      step.classList.toggle("is-active", i === index)
    );

    current = index;
    progress.style.width = ((index + 1) / steps.length) * 100 + "%";
    prevBtn.disabled = index === 0;
    nextBtn.textContent =
      index === steps.length - 1
        ? "<?php echo get_phrase('Finish'); ?>"
        : "<?php echo get_phrase('Next'); ?>";
  }

  nextBtn.addEventListener("click", (e) => {
    e.stopPropagation();
    if (current < steps.length - 1) {
      showStep(current + 1);
    } else {
      closeOnboarding();
    }
  });

  prevBtn.addEventListener("click", (e) => {
    e.stopPropagation();
    if (current > 0) showStep(current - 1);
  });

  skipBtn.addEventListener("click", (e) => {
    e.stopPropagation();
    closeOnboarding();
  });

  closeBtn.addEventListener("click", (e) => {
    e.stopPropagation();
    closeOnboarding();
  });

  overlay.addEventListener("click", closeOnboarding);

  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape" && modal.classList.contains("active")) {
      closeOnboarding();
    }
  });

  // Auto-open ALWAYS
  setTimeout(() => {
    document.body.appendChild(overlay);
    document.body.appendChild(modal);
    openOnboarding();
  }, 600);
});
</script>
