<link rel="stylesheet" href="<?php echo base_url(); ?>assets/backend/css/custom/event_calendar.css">
<!--title-->
<div class="col-xl-12">
  <div class="header-card">
    <div class="card-body">
      <h4 class="page-title d-inline-block">
        <i class="fas fa-calendar-alt fa-fw"></i> <?php echo get_phrase('calendar'); ?>
      </h4>
    </div> <!-- end card body-->
  </div> <!-- end card -->
</div><!-- end col-->


<div class="row">
  <div class="col-12">
    <div class="mb-3">
      <div class="main-card">
        <div class="card-body class_routine_content">
          <?php include 'list.php'; ?>
        </div>
      </div>
    </div>
  </div>
</div>