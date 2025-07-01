<style>
   body[dir="rtl"] .navbar-custom.topnav-navbar {
    direction: rtl;
    flex-direction: row-reverse !important;
}

body[dir="rtl"] .navbar-custom .container-fluid {
    flex-direction: row-reverse !important;
    align-items: center;
}

body[dir="rtl"] .topbar-menu.float-end {
    float: left !important;
}

body[dir="rtl"] .app-search {
    float: right !important;
    text-align: right !important;
    display: flex;
    flex-direction: row-reverse;
    align-items: center;
    margin-right: 10px;
}

body[dir="rtl"] .app-search .system-name {
    order: 2;
}
body[dir="rtl"] .app-search .website-button {
    order: 1;
}

body[dir="rtl"] .topnav-logo {
    float: right !important;
    text-align: right !important;
    margin-left: 10px;
}

body[dir="rtl"] .website-button {
    margin-right: 10px !important;
    margin-left: 0 !important;
}
body[dir="rtl"] .topbar-menu {
    display: flex !important;
    flex-direction: row-reverse !important;
    float: none !important;
    align-items: center;
}

body[dir="rtl"] .topbar-menu .language-selector {
    order: 2;
}
body[dir="rtl"] .topbar-menu .profile-menu {
    order: 1;
}

body[dir="rtl"] .account-user-name {
    font-size: 0.9rem !important;
}

body[dir="rtl"] .app-search a,
body[dir="rtl"] .app-search .website-button {
    font-size: 1.1rem !important;
}
</style>
<!-- Topbar Start -->
<div class="navbar-custom topnav-navbar navbar-color topnav-navbar-dark">
    <div class="container-fluid">

        <!-- LOGO -->
        <a href="<?php echo site_url($this->session->userdata('role')); ?>" class="topnav-logo" style = "min-width: unset;">
            <span class="topnav-logo-lg">
                <img src="<?php echo $this->settings_model->get_logo_light(); ?>" alt="" height="40">
            </span>
            <span class="topnav-logo-sm">
                <img src="<?php echo $this->settings_model->get_logo_light('small'); ?>" alt="" height="40">
            </span>
        </a>

        <ul class="list-unstyled topbar-menu float-end mb-0">

          <?php if ($this->session->userdata('user_type') == 'superadmin' || $this->session->userdata('user_type') == 'admin' || $this->session->userdata('user_type') == 'teacher'
          || $this->session->userdata('user_type') == 'student'): ?>
              <li class="dropdown notification-list topbar-dropdown d-none d-lg-block language-selector">
                  <a class="nav-link dropdown-toggle arrow-none" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false" onclick="getLanguageList()">
                      <i class="mdi mdi-translate noti-icon"></i> <?php echo ucfirst(get_user_language()); ?>
                  </a>
                  <div class="dropdown-menu dropdown-menu-end dropdown-menu-animated dropdown-lg">

                      <!-- item-->
                      <div class="dropdown-item noti-title">
                          <h5 class="m-0">
                              <?php echo get_phrase('language'); ?>
                          </h5>
                      </div>

                      <div class="slimscroll" id="language-list" style="min-height: 150px;">

                      </div>
                  </div>
              </li>
          <?php endif; ?>

            <li class="dropdown notification-list profile-menu">
                <a class="nav-link dropdown-toggle nav-user  user-dropdown arrow-none me-0" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false"
                aria-expanded="false">
                    <span class="account-user-avatar">
                        <img src="<?php echo $this->user_model->get_user_image($this->session->userdata('user_id')); ?>" alt="user-image" class="rounded-circle">
                    </span>
                    <span>
                        <span class="account-user-name"><?php echo $user_name; ?></span>
                        <?php if (strtolower($this->db->get_where('users', array('id' => $user_id))->row('role')) == 'admin'): ?>
                            <span class="account-position"><?php echo get_phrase('school_admin'); ?></span>
                        <?php else: ?>
                            <span class="account-position">
                                
                            <?php
                            if($this->db->get_where('users', array('id' => $user_id))->row('role') == "teacher")
                            echo get_phrase('teacher');
                            else
                            echo ucfirst($this->db->get_where('users', array('id' => $user_id))->row('role')); ?>
                        
                        
                        </span>
                        <?php endif; ?>

                    </span>
                </a>

                <div class="dropdown-menu dropdown-menu-end dropdown-menu-animated topbar-dropdown-menu profile-dropdown">
                    <!-- item-->
                    <div class=" dropdown-header noti-title">
                        <h6 class="text-overflow m-0"><?php echo get_phrase('welcome'); ?> !</h6>
                    </div>

                    <!-- item-->
                    <a href="<?php echo route('profile'); ?>" class="dropdown-item notify-item">
                        <i class="mdi mdi-account-circle me-1"></i>
                        <span><?php echo get_phrase('my_account'); ?></span>
                    </a>
                    <?php if ($this->session->userdata('user_type') == 'superadmin'): ?>
                        <!-- item-->
                        <a href="<?php echo route('system_settings'); ?>" class="dropdown-item notify-item">
                            <i class="mdi mdi-account-edit me-1"></i>
                            <span><?php echo get_phrase('settings'); ?></span>
                        </a>
                    <?php endif; ?>

                    <?php if ($this->session->userdata('user_type') == 'superadmin' || $this->session->userdata('user_type') == 'admin'): ?>
                        <!-- item-->
                        <a href="mailto:info@wayo.cloud?Subject=Help%20On%20This" target="_blank" class="dropdown-item notify-item">
                            <i class="mdi mdi-lifebuoy me-1"></i>
                            <span><?php echo get_phrase('support'); ?></span>
                        </a>
                    <?php endif; ?>

                    <!-- item-->
                    <a href="<?php echo site_url('login/logout'); ?>" class="dropdown-item notify-item">
                        <i class="mdi mdi-logout me-1"></i>
                        <span><?php echo get_phrase('logout'); ?></span>
                    </a>

                </div>
            </li>

        </ul>
        <div class="app-search dropdown pt-1 mt-2">
            <h4 style="color: #fff; float: left;" class="d-none d-md-inline-block system-name"><?php echo get_settings('system_name'); ?></h4>
            <a href="<?php echo site_url(); ?>" target="" class="btn btn-outline-light website-button ms-2 d-none d-md-inline-block website-button"><?php echo get_phrase('visit_website'); ?></a>
        </div>
        <a class="button-menu-mobile disable-btn">
            <div class="lines">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </a>
    </div>
</div>
<!-- end Topbar -->


<script type="text/javascript">
function getLanguageList() {
    $.ajax({
        url: "<?php echo route('language/dropdown'); ?>",
        success: function(response){
            $('#language-list').html(response);
        }
    });
}
</script>
