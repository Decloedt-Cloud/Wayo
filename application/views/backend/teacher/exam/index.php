<link rel="stylesheet" href="<?php echo base_url(); ?>assets/backend/css/responsive.css">
<!--title-->
<div class="col-xl-12">
    <div class="header-card">
        <div class="card-body">
            <h4 class="page-title d-inline-block">
                <i class="fas fa-file-signature fa-fw"></i> <?php echo get_phrase('certifications'); ?>
            </h4>
        </div> <!-- end card body-->
    </div> <!-- end card -->
</div><!-- end col-->

<div class="row">
    <div class="col-12">
        <div class="mb-3">
            <div class="main-card">
                <div class="card-body">
                    <div class="card-body exam_content">
                        <?php include 'list.php'; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    var showAllExams = function() {
        var url = '<?php echo route('exam/list'); ?>';

        $.ajax({
            type: 'GET',
            url: url,
            success: function(response) {
                $('.exam_content').html(response);
                initDataTable('basic-datatable');
            }
        });
    }
</script>