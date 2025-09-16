<link rel="stylesheet" href="<?php echo base_url(); ?>assets/backend/css/responsive.css">
    <!--title-->
    <div class="col-xl-12">
        <div class="header-card">
            <div class="card-body">
                <h4 class="page-title d-inline-block">
          <i class="mdi mdi-translate title_icon"></i> <?php echo get_phrase('languages'); ?>
        </h4>
        <button type="button" class="btn btn-outline-primary btn-rounded alignToTitle float-end mt-1" onclick="rightModal('<?php echo site_url('modal/popup/language/create'); ?>', '<?php echo get_phrase('add_language'); ?>')"> <i class="mdi mdi-plus"></i> <?php echo get_phrase('add_language'); ?></button>
      </div> <!-- end card body-->
    </div> <!-- end card -->
  </div><!-- end col-->

<!-- end page title -->

<div class="mb-3">
    <div class="main-card">
        <div class="card-body">
                <div class = "language_content">
                    <?php include 'list.php'; ?>
                </div> <!-- end table-responsive-->
            </div> <!-- end card body-->
        </div> <!-- end card -->
    </div><!-- end col-->



<script>
var showAllLanguages = function () {
    var url = '<?php echo route('language/list'); ?>';

    $.ajax({
        type : 'GET',
        url: url,
        success : function(response) {
            $('.language_content').html(response);
            initDataTable('basic-datatable');
        }
    });
}
</script>
