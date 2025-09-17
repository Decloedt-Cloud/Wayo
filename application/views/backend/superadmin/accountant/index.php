<link rel="stylesheet" href="<?php echo base_url(); ?>assets/backend/css/responsive.css">
<!--title-->
<div class="col-xl-12">
  <div class="header-card">
    <div class="card-body">
      <h4 class="page-title d-inline-block">
        <i class="mdi mdi-account-circle title_icon"></i> <?php echo get_phrase('accountant'); ?>
      </h4>
      <button type="button" class="btn btn-outline-primary btn-rounded alignToTitle float-end mt-1" onclick="rightModal('<?php echo site_url('modal/popup/accountant/create'); ?>', '<?php echo get_phrase('create_accountant'); ?>')"> <i class="mdi mdi-plus"></i> <?php echo get_phrase('add_accountant'); ?></button>
    </div> <!-- end card body-->
  </div> <!-- end card -->
</div><!-- end col-->

<div class="row">
  <div class="col-12">
    <div class="mb-3">
      <div class="main-card">
        <div class="card-body">
          <div class="card-body accountant_content">
            <?php include 'list.php'; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  var showAllAccountants = function() {
    var url = '<?php echo route('accountant/list'); ?>';

    $.ajax({
      type: 'GET',
      url: url,
      success: function(response) {
        $('.accountant_content').html(response);
        initDataTable('basic-datatable');
      }
    });
  }
</script>