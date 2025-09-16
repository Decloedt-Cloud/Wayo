<link rel="stylesheet" href="<?php echo base_url(); ?>assets/backend/css/responsive.css">
    <!--title-->
<div class="col-xl-12">
    <div class="header-card">
        <div class="card-body d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between">
            
            <h4 class="page-title mb-2 mb-md-0">
                <i class="mdi mdi-power-plug title_icon me-2"></i> 
                <?php echo get_phrase('manage_addons'); ?>
            </h4>
            
            <button type="button" class="btn btn-outline-primary btn-rounded mt-2 mt-md-0"
                onclick="rightModal('<?php echo site_url('modal/popup/addon/create') ?>', '<?php echo get_phrase('add_new_addon'); ?>')">
                <i class="mdi mdi-plus"></i> <?php echo get_phrase('add_new_addon'); ?>
            </button>
            
        </div> <!-- end card-body -->
    </div> <!-- end header-card -->
</div> <!-- end col -->


<!-- end page title -->
<div class="row">
  <div class="col-12">
    <div class="mb-3">
<div class="main-card">
        <div class="card-body">
        <h4 class="header-title mt-3"><?php echo get_phrase('installed_addons'); ?></h4>
        <div class="table-responsive-sm addon_content">
          <?php include 'list.php'; ?>
        </div> <!-- end table-responsive-->
      </div> <!-- end card body-->
    </div> <!-- end card -->
  </div><!-- end col-->
</div>
</div>
<script>
var showAllAddons = function () {
  var url = '<?php echo route('addon_manager/list'); ?>';

  $.ajax({
    type : 'GET',
    url: url,
    success : function(response) {
      $('.addon_content').html(response);
      initDataTable("basic-datatable");
      setTimeout(
        function()
        {
          location.reload();
        }, 1000);
      }
    });
  }
  </script>
