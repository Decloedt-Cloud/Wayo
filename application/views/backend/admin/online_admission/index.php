<link rel="stylesheet" href="<?php echo base_url(); ?>assets/backend/css/responsive.css">
<!--title-->

<div class="col-xl-12">
    <div class="header-card">
             <div class="card-body">
                <h4 class="page-title d-inline-block">
                    <i class="fas fa-tag fa-fw"></i> <?php echo get_phrase('online_admission'); ?>
                </h4>
            </div> <!-- end card body-->
        </div> <!-- end card -->
</div><!-- end col-->

<div class="mb-3">
  <div class="main-card">
        <div class="card-body">
            <?php include 'list.php'; ?>
        </div>
    </div>
</div>
