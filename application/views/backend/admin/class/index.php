<link rel="stylesheet" href="<?php echo base_url(); ?>assets/backend/css/responsive.css">
<!--title-->
<div class="col-xl-12">
    <div class="header-card">
             <div class="card-body d-flex justify-content-between align-items-center">
                <h4 class="page-title d-inline-block">
            <i class="fas fa-chalkboard fa-fw"></i> <?php echo get_phrase('class'); ?>
        </h4>
        <button type="button" class="btn btn-outline-primary btn-rounded alignToTitle float-end mt-1" onclick="rightModal('<?php echo site_url('modal/popup/class/create'); ?>', '<?php echo htmlspecialchars(get_phrase('create_class'), ENT_QUOTES); ?>')"> <i class="mdi mdi-plus"></i> <?php echo get_phrase('add_class'); ?></button>
      </div> <!-- end card body-->
    </div> <!-- end card -->
  </div><!-- end col-->



<div class="row">
  <div class="col-12">
    <div class="mb-3">
    <div class="main-card">
      <div class="card-body">
            <div class="card-body class_content">
                <?php include 'list.php'; ?>
            </div>
        </div>
    </div>
</div>
</div>
</div>
<script>
    var showAllClasses = function () {
        var url = '<?php echo route('manage_class/list'); ?>';

        $.ajax({
            type : 'GET',
            url: url,
            success : function(response) {
                $('.class_content').html(response);
                initDataTable('basic-datatable');
            }
        });
    }
    function showNotification(type, message) {
    toastr.options = {
        closeButton: true,
        progressBar: true,
        positionClass: 'toast-top-right',
        timeOut: 5000,
        showMethod: 'fadeIn',
        hideMethod: 'fadeOut',
    };
    if (type === 'success') {
        toastr.success(message);
    } else if (type === 'error') {
        toastr.error(message);
    } else if (type === 'warning') {
        toastr.warning(message);
    }
}
</script>


