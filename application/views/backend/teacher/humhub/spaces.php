<link rel="stylesheet" href="<?php echo base_url(); ?>assets/backend/css/layout-responsive.css">
<div class="col-xl-12">
    <div class="header-card">
             <div class="card-body">
                <h4 class="page-title d-inline-block">
                <i class="fas fa-user-shield fa-fw"></i> <?php echo get_phrase('espaces'); ?> </h4>
            </div>
        </div>
    </div>

<div class="iframe-container">
    <iframe src="<?php echo config_item('humhub_url'); ?>spaces"></iframe>
</div>

