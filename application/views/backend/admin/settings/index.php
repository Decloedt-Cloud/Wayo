<?php
if($settings_type == 'system_settings')
$class = 'col-xl-12';
else if($settings_type == 'language_settings')
$class = 'col-xl-10 offset-xl-1';
else if($settings_type == 'sms_settings')
$class = 'col-xl-10 offset-xl-1';

else if($settings_type == 'sms_settings')
$class = 'col-xl-10 offset-xl-1';
?>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/backend/css/responsive.css">
    <!--title-->
    <div class="col-xl-12">
        <div class="header-card">
            <div class="card-body">
                <h4 class="page-title d-inline-block">
                     <i class="fas fa-cog"></i><?php echo ucfirst(get_phrase($settings_type)); ?> 
                </h4>
      </div> <!-- end card body-->
    </div> <!-- end card -->
  </div><!-- end col-->

<!-- end page title -->
<div class="row">
  <div class="<?php echo $class; ?>">
    <div class="settings_content">
      <?php include $settings_type.'.php'; ?>
    </div>
  </div>
</div>
<script>
function updateSystemInfo(system_name) {
  $(".systemAjaxForm").validate({});
  $(".systemAjaxForm").submit(function(e) {
    var form = $(this);
    ajaxSubmit(e, form, reload);
  });
}

function updateSystemLogo() {
  // die('dddddddddddd');
  // $(".systemLogoAjaxForm").validate({});
  $(".systemLogoAjaxForm").submit(function(e) {
    var form = $(this);
    // die(form);
    ajaxSubmit(e, form, reload);
  });
}
function updateSystemPrice() {
  $(".systempriceAjaxForm").validate({});
  $(".systempriceAjaxForm").submit(function(e) {
    var form = $(this);
    ajaxSubmit(e, form, reload);
  });
}

function updateSystemVat() {
  $(".systemvatAjaxForm").validate({});
  $(".systemvatAjaxForm").submit(function(e) {
    var form = $(this);
    ajaxSubmit(e, form, reload);
  });
}
function updateSystemCurrencyInfo() {
  $(".systemAjaxForm").validate({});
  $(".systemAjaxForm").submit(function(e) {
    var form = $(this);
    ajaxSubmit(e, form, reload);
  });
}
function updatePaypalInfo() {
  $(".paypalAjaxForm").validate({});
  $(".paypalAjaxForm").submit(function(e) {
    var form = $(this);
    ajaxSubmit(e, form, reload);
  });
}

function updateStripeInfo() {
  $(".stripeAjaxForm").validate({});
  $(".stripeAjaxForm").submit(function(e) {
    var form = $(this);
    ajaxSubmit(e, form, reload);
  });
}

function updateSmsInfo() {
  $(".smsForm").validate({});
  $(".smsForm").submit(function(e) {
    var form = $(this);
    ajaxSubmit(e, form, reload);
  });
}

function updateSmtpInfo() {
  $(".smtpForm").validate({});
  $(".smtpForm").submit(function(e) {
    var form = $(this);
    ajaxSubmit(e, form, reload);
  });
}

function updateSchoolInfo() {
  $(".schoolForm").validate({});
  $(".schoolForm").submit(function(e) {
    var form = $(this);
    ajaxSubmit(e, form, reload);
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
