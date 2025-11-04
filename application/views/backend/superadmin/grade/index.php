<link rel="stylesheet" href="<?php echo base_url(); ?>assets/backend/css/responsive.css">
    <!--title-->
    <div class="col-xl-12">
        <div class="header-card">
            <div class="card-body">
                <h4 class="page-title d-inline-block">
          <i class="fas fa-clipboard-check fa-fw"></i> <?php echo get_phrase('Grade'); ?>
        </h4>
        <button type="button" class="btn btn-outline-primary btn-rounded alignToTitle float-end mt-1" onclick="rightModal('<?php echo site_url('modal/popup/grade/create'); ?>', '<?php echo get_phrase('add_grade'); ?>')"> <i class="mdi mdi-plus"></i> <?php echo get_phrase('add_grade'); ?></button>
      </div> <!-- end card body-->
    </div> <!-- end card -->
  </div><!-- end col-->

    <div class="row">
        <div class="col-12">
            <div class="mb-3">
            <div class="main-card">
                <div class="card-body">
            <div class="card-body grade_content">
                <?php include 'list.php'; ?>
            </div>
        </div>
    </div>
</div>
   </div>
</div>
<script>
    var showAllGrades = function () {
        var url = '<?php echo route('grade/list'); ?>';

        $.ajax({
            type : 'GET',
            url: url,
            success : function(response) {
                $('.grade_content').html(response);
                initDataTable('basic-datatable');
            }
        });
    }
</script>
