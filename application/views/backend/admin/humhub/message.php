<link rel="stylesheet" href="<?php echo base_url(); ?>assets/backend/css/layout-responsive.css">
<div class="col-xl-12">
  <div class="header-card">
    <div class="card-body">
      <h4 class="page-title d-inline-block"> <i class="dripicons-message title_icon"></i> <?php echo get_phrase('chat'); ?> </h4>
    </div>
  </div>
</div>


<div class="iframe-container">
  <iframe src="<?php echo config_item('humhub_url'); ?>mail/mail/index"
    allow="microphone *; camera *; autoplay *; clipboard-read *; clipboard-write *; fullscreen *"
    allowfullscreen referrerpolicy="strict-origin-when-cross-origin" loading="lazy"></iframe>
</div>