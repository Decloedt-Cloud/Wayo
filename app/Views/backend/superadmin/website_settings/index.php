<link rel="stylesheet" href="<?php echo base_url(); ?>assets/backend/css/responsive.min.css">
    <!--title-->
    <div class="col-xl-12">
        <div class="header-card">
            <div class="card-body">
                <h4 class="page-title d-inline-block">
          <i class="fas fa-cogs"></i><?php echo ucfirst(get_phrase('website_settings')); ?>
        </h4>
      </div> <!-- end card body-->
    </div> <!-- end card -->
  </div><!-- end col-->

<!-- end page title -->
<div class="row">
<div class="col-md-3">
  <div class="list-group">
    <a href="<?php echo route('noticeboard'); ?>" class="list-group-item list-group-item-action <?php if($page_content=='noticeboard') echo 'active'; ?> d-flex align-items-center justify-content-between mb-1">
      <span><i class="mdi mdi-book-open-page-variant me-2"></i><?php echo get_phrase('noticeboard'); ?></span>
      <i class="mdi mdi-chevron-right"></i>
    </a>
    <a href="<?php echo route('website_settings/events'); ?>" class="list-group-item list-group-item-action <?php if($page_content=='events') echo 'active'; ?> d-flex align-items-center justify-content-between mb-1">
      <span><i class="mdi mdi-calendar me-2"></i><?php echo get_phrase('events'); ?></span>
      <i class="mdi mdi-chevron-right"></i>
    </a>
    <a href="<?php echo route('teacher'); ?>" class="list-group-item list-group-item-action <?php if($page_content=='teachers') echo 'active'; ?> d-flex align-items-center justify-content-between mb-1">
      <span><i class="mdi mdi-account-tie me-2"></i><?php echo get_phrase('teachers'); ?></span>
      <i class="mdi mdi-chevron-right"></i>
    </a>
    <a href="<?php echo route('website_settings/gallery'); ?>" class="list-group-item list-group-item-action <?php if($page_content=='gallery' || $page_content=='gallery_image') echo 'active'; ?> d-flex align-items-center justify-content-between mb-1">
      <span><i class="mdi mdi-image-multiple me-2"></i><?php echo get_phrase('gallery'); ?></span>
      <i class="mdi mdi-chevron-right"></i>
    </a>
    <a href="<?php echo route('website_settings/about_us'); ?>" class="list-group-item list-group-item-action <?php if($page_content=='about_us') echo 'active'; ?> d-flex align-items-center justify-content-between mb-1">
      <span><i class="mdi mdi-information me-2"></i><?php echo get_phrase('about_us'); ?></span>
      <i class="mdi mdi-chevron-right"></i>
    </a>
    <a href="<?php echo route('website_settings/terms_and_conditions'); ?>" class="list-group-item list-group-item-action <?php if($page_content=='terms_and_conditions') echo 'active'; ?> d-flex align-items-center justify-content-between mb-1">
      <span><i class="mdi mdi-file-document-outline me-2"></i><?php echo get_phrase('terms_and_conditions'); ?></span>
      <i class="mdi mdi-chevron-right"></i>
    </a>
    <a href="<?php echo route('website_settings/privacy_policy'); ?>" class="list-group-item list-group-item-action <?php if($page_content=='privacy_policy') echo 'active'; ?> d-flex align-items-center justify-content-between mb-1">
      <span><i class="mdi mdi-shield-lock-outline me-2"></i><?php echo get_phrase('privacy_policy'); ?></span>
      <i class="mdi mdi-chevron-right"></i>
    </a>
    <a href="<?php echo route('website_settings/homepage_slider'); ?>" class="list-group-item list-group-item-action <?php if($page_content=='homepage_slider') echo 'active'; ?> d-flex align-items-center justify-content-between mb-1">
      <span><i class="mdi mdi-image-filter-hdr me-2"></i><?php echo get_phrase('homepage_slider'); ?></span>
      <i class="mdi mdi-chevron-right"></i>
    </a>
    <a href="<?php echo route('website_settings/general_settings'); ?>" class="list-group-item list-group-item-action <?php if($page_content=='general_settings') echo 'active'; ?> d-flex align-items-center justify-content-between mb-1">
      <span><i class="mdi mdi-cog-outline me-2"></i><?php echo get_phrase('general_settings'); ?></span>
      <i class="mdi mdi-chevron-right"></i>
    </a>
    <a href="<?php echo route('website_settings/other_settings'); ?>" class="list-group-item list-group-item-action <?php if($page_content=='other_settings') echo 'active'; ?> d-flex align-items-center justify-content-between mb-1">
      <span><i class="mdi mdi-dots-horizontal me-2"></i><?php echo get_phrase('others'); ?></span>
      <i class="mdi mdi-chevron-right"></i>
    </a>
  </div>
</div>

  <div class="col-md-9 page_content">
    <?php include $page_content.'.php'; ?>
  </div>
</div>

<script type="text/javascript">
// FRONTEND FORM SUBMISSION STRATS FROM HERE
function updateGeneralSettings() {
  $(".generalSettingsAjaxForm").validate({});
  $(".generalSettingsAjaxForm").submit(function(e) {
    var form = $(this);
    ajaxSubmit(e, form, reload);
  });
}

function updateAboutUsSettings() {
  $(".aboutUsSettings").validate({});
  $(".aboutUsSettings").submit(function(e) {
    var form = $(this);
    ajaxSubmit(e, form, reload);
  });
}

function updatePrivactPolicySettings() {
  $(".privacyPolicySettings").validate({});
  $(".privacyPolicySettings").submit(function(e) {
    var form = $(this);
    ajaxSubmit(e, form, doNothing);
  });
}

function updateTermsAndConditionSettings() {
  $(".termsAndConditionSettings").validate({});
  $(".termsAndConditionSettings").submit(function(e) {
    var form = $(this);
    ajaxSubmit(e, form, doNothing);
  });
}

function updateHomepageSliderSettings() {
  $(".homepageSliderSettings").validate({});
  $(".homepageSliderSettings").submit(function(e) {
    var form = $(this);
    ajaxSubmit(e, form, reload);
  });
}

function updateOtherSettings() {
  $(".otherSettingsAjaxForm").validate({});
  $(".otherSettingsAjaxForm").submit(function(e) {
    var form = $(this);
    ajaxSubmit(e, form, reload);
  });
}

function updateRecaptchaSettings() {
  $(".updateRecaptchaSettings").validate({});
  $(".updateRecaptchaSettings").submit(function(e) {
    var form = $(this);
    ajaxSubmit(e, form, reload);
  });
}

// Show All The Events
var showAllEvents = function () {
    var url = '<?php echo route('events/list'); ?>';

    $.ajax({
        type : 'GET',
        url: url,
        success : function(response) {
            $('.page_content').html(response);
            initDataTable('basic-datatable');
        }
    });
}

function reload() {
  setTimeout(
    function()
    {
      location.reload();
    }, 1000);
}
function doNothing() {

}
</script>
