<link rel="stylesheet" href="<?php echo base_url(); ?>assets/backend/css/responsive.min.css">
    <!--title-->
    <div class="col-xl-12">
        <div class="header-card">
            <div class="card-body">
                <h4 class="page-title d-inline-block">
        <i class="mdi mdi-database title_icon"></i> <?php echo get_phrase('book'); ?></h4>
      </div> <!-- end card body-->
    </div> <!-- end card -->
  </div><!-- end col-->

<!-- end page title -->

<div class="row">
  <div class="col-12">
    <div class="mb-3">
    <div class="main-card">
      <div class="card-body">
                <div class = "book_content">
                    <?php include 'list.php'; ?>
                </div> <!-- end table-responsive-->
            </div> <!-- end card body-->
        </div> <!-- end card -->
    </div><!-- end col-->
</div>
</div>

<script>
var showAllBooks = function () {
    var url = '<?php echo route('book/list'); ?>';

    $.ajax({
        type : 'GET',
        url: url,
        success : function(response) {
            $('.book_content').html(response);
            initDataTable('basic-datatable');
        }
    });
}
</script>
