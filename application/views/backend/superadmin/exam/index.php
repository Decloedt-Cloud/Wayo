<link rel="stylesheet" href="<?php echo base_url(); ?>assets/backend/css/responsive.min.css">
<!--title-->
<div class="col-xl-12">
    <div class="header-card">
        <div class="card-body d-flex justify-content-between align-items-center">
            <h4 class="page-title d-inline-block">
                <i class="fas fa-file-signature fa-fw"></i> <?php echo get_phrase('certifications'); ?>
            </h4>
            <button type="button" class="btn btn-outline-primary btn-rounded alignToTitle float-end mt-1" onclick="rightModal('<?php echo site_url('modal/popup/exam/create'); ?>', '<?php echo get_phrase('create_exam'); ?>')"> <i class="mdi mdi-plus"></i> <?php echo get_phrase('add_exam'); ?></button>
        </div>
    </div>
</div>


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
            },
            error: function(xhr, status, error) {
                console.error('Erreur AJAX showAllExams:', status, error, xhr.responseText);
            }
        });
    }

    $(document).ready(function() {
        showAllExams();
    });
</script>