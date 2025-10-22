<!-- <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet"> -->
<?php
$logo_light = $this->settings_model->get_logo_light();
$system_name = get_frontend_settings('website_title');
?>

<!-- ========== HEADER ========== -->
<header id="header" class="">
  <nav class="navbar position-relative navbar-expand-lg container-fluid navbar-dark sticky-top sticky-nav" <?php echo (get_user_language() === 'arabic') ? 'dir="rtl"' : 'dir="ltr"'; ?>>
    <div class="container-fluid">
      <!-- Toggle Button for Mobile -->
 <button class="navbar-toggler" type="button" data-bs-toggle="collapse" 
        data-bs-target="#navbarContent" aria-controls="navbarContent" 
        aria-expanded="false" aria-label="Toggle navigation">
  <span class="navbar-toggler-icon"style="background-color: #F47A1F; border-radius: 5px;"></span>
</button>
      <!-- End Toggle Button -->

      <!-- Navigation Links -->
      <div class="collapse navbar-collapse" id="navbarContent">
        <ul class="navbar-nav justify-content-center w-100 mb-2 mb-lg-0">
          <!-- Logo -->
          <li class="nav-item navbar-logo">
            <a class="navbar-brand" href="<?php echo site_url('home'); ?>">
              <img src="<?php echo $logo_light; ?>" alt="Logo" style="width: 100px;">
            </a>
          </li>
          <!-- Centered Navigation Links -->
          <li class="nav-item nav-item-home">
            <a class="nav-link <?php if ($page_name === 'home') echo 'active'; ?>" href="<?php echo site_url('home'); ?>">
              <?php echo get_phrase('Home'); ?>
            </a>
          </li>
          <li class="nav-item nav-item-about">
            <a class="nav-link <?php if ($page_name === 'about') echo 'active'; ?>" href="<?php echo site_url('home/about'); ?>">
              <?php echo get_phrase('About_us'); ?>
            </a>
          </li>
          <li class="nav-item nav-item-courses">
            <a class="nav-link <?php if ($page_name === 'communities') echo 'active'; ?>" href="<?php echo site_url('home/communities'); ?>">
              <?php echo get_phrase('Our Communities'); ?>
            </a>
          </li>
           <li class="nav-item nav-item-courses">
            <a class="nav-link <?php if ($page_name === 'online_admission') echo 'active'; ?>" href="<?php echo site_url('admission/online_admission'); ?>">
              <?php echo get_phrase('Create Community'); ?>
            </a>
          </li>
          <li class="nav-item nav-item-demo">
            <a class="nav-link <?php if ($page_name === 'tutorial') echo 'active'; ?>" href="<?php echo site_url('home/tutorial'); ?>">
              <?php echo get_phrase('Tutorial'); ?>
            </a>
          </li>
           <li class="nav-item nav-item-contact">
            <a class="nav-link <?php if ($page_name === 'contact') echo 'active'; ?>" href="<?php echo site_url('home/contact'); ?>">
              <?php echo get_phrase('Contact_us'); ?>
            </a>
          </li>
          <!-- Dashboard and User Section -->
          <?php if ($this->session->userdata('user_id')) { ?>
            <li class="nav-item navbar-user-dashboard">
              <a href="<?php echo route('dashboard'); ?>" target="" class="btn btn-outline-dark button_with website-button d-md-inline-block">
                <?php echo get_phrase('community_app'); ?>
              </a>
            </li>
            <li class="nav-item navbar-user-profile">
              <div class="user-section">
                <span class="text-capitalize align-content-center"><?php echo $this->session->user_name; ?></span>
                <img src="<?php echo $this->user_model->get_user_image($this->session->userdata('user_id')); ?>" alt="user-image" class="rounded-circle nav-user-img">
              </div>
              <?php include 'components/navigation-components/user_loggedin_component.php'; ?>
            </li>
          <?php } else { ?>
            <li class="nav-item navbar-user">
              <a class="btn btn-login login-toggle"><?php echo get_phrase('Login'); ?> </a>
              <?php include 'components/navigation-components/login_register_component.php'; ?>
            </li>
          <?php } ?>
          <?php if ($this->session->userdata('user_type') == 'superadmin' || $this->session->userdata('user_type') == 'admin' || $this->session->userdata('user_type') == 'teacher'
          || $this->session->userdata('user_type') == 'student'): ?>
             <li class="dropdown notification-list topbar-dropdown d-none d-lg-block language-selector">
                <a class="nav-link dropdown-toggle arrow-none" data-bs-toggle="dropdown" href="" role="button" aria-haspopup="false" aria-expanded="false" onclick="getLanguageList()">
                    <?php echo ucfirst(get_user_language()); ?>
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-animated dropdown-lg">
                    <div class="">
                        <h5 class="m-0 dropdown-item-title">
                            <?php echo get_phrase('language'); ?>
                        </h5>
                    </div>
                    <div class="slimscroll" id="language-list" style="min-height: 150px;">
                        <!-- Language items will be populated here -->
                    </div>
                </div>
            </li>
            <?php else: ?>
            <!-- Language selector for guests -->
            <li class="dropdown notification-list topbar-dropdown d-none d-lg-block language-selector">
                <a class="nav-link dropdown-toggle arrow-none" data-bs-toggle="dropdown" href="" role="button" aria-haspopup="false" aria-expanded="false" onclick="getGuestLanguageList()">
                    <?php echo ucfirst(get_user_language()); ?>
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-animated dropdown-lg">
                    <div class="">
                        <h5 class="m-0 dropdown-item-title">
                            <?php echo get_phrase('language'); ?>
                        </h5>
                    </div>
                    <div class="slimscroll" id="guest-language-list" style="min-height: 150px;">
                        <!-- Language items will be populated here -->
                    </div>
                </div>
            </li>
          <?php endif; ?>
        </ul>
      </div>
      <!-- End Navigation Links -->
    </div>
  </nav>
</header>
<!-- ========== END HEADER ========== -->

   <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script> -->

 <script>
  
 function getLanguageList() {
    $.ajax({
        url: "<?php echo route('language/dropdown'); ?>",
        success: function(response){
            $('#language-list').html(response);
        }
    });
  }
  function getGuestLanguageList() {
    $.ajax({
        url: "<?php echo site_url('home/dropdown_guest'); ?>", // <-- ici
        success: function(response){
            $('#guest-language-list').html(response);
        }
    });
}
function setGuestLanguage(lang) {
    $.post("<?php echo site_url('home/set_guest_language'); ?>", {
        language: lang,
        <?php echo $this->security->get_csrf_token_name(); ?>: '<?php echo $this->security->get_csrf_hash(); ?>'
    }, function() {
        location.reload();
    });
}
</script>
