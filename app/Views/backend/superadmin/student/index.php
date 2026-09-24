<?php if ($working_page == 'filter'): ?>
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/backend/css/responsive.min.css">
    <?php $classes = isset($student_list_classes) && is_array($student_list_classes) ? $student_list_classes : []; ?>
    <!--title-->
    <div class="col-xl-12">
        <div class="header-card">
            <div class="card-body d-flex justify-content-between align-items-center">
                <h4 class="page-title d-inline-block">
                    <i class="fas fa-user-group fa-fw"></i> <?php echo get_phrase('student'); ?>
                </h4>
                <a href="<?php echo route('student/create'); ?>"
                    class="btn btn-outline-primary btn-rounded alignToTitle float-end mt-1"> <i class="mdi mdi-plus"></i>
                    <?php echo get_phrase('add_new_student'); ?></a>
            </div> <!-- end card body-->
        </div> <!-- end card -->
    </div><!-- end col-->
    <div class="row">
        <div class="col-12">
            <div class="mb-3">
            <div class="main-card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-1"></div>
                        <div class="col-md-4 mb-1">
                            <select name="class" id="class_id" class="form-control">
                                <option value="all"><?php echo get_phrase('all_classes'); ?></option>
                                <?php
                                foreach ($classes as $class) {
                                    ?>
                                    <option value="<?php echo $class['id']; ?>" <?php if ($class['id'] == $class_id)
                                           echo 'selected'; ?>>
                                        <?php echo $class['name']; ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="col-md-2 btncol">
                            <button class="btn btn-block btn-secondary"
                                onclick="filter_student()"><?php echo get_phrase('filter'); ?></button>
                        </div>
                    </div>

                    <div class="card-body student_content">
                        <?php include 'list.php'; ?>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </div>
<?php elseif ($working_page == 'create'): ?>
    <?php include 'create.php'; ?>
<?php elseif ($working_page == 'edit'): ?>
    <?php include 'update.php'; ?>
<?php endif; ?>

<script>
    function filter_student() {
        var class_id = $('#class_id').val();

        if (class_id != "") {
            showAllStudents();
        } else {
            toastr.error('<?php echo get_phrase('please_select_a_class'); ?>');
        }
    }

    var showAllStudents = function () {
        var class_id = $('#class_id').val();

        var csrfName = $('input[name="<?= csrf_token(); ?>"]').attr('name');
        var csrfHash = $('input[name="<?= csrf_token(); ?>"]').val();
        $.ajax({
            url: '<?php echo site_url('app/student/filter/') ?>' + (class_id == 'all' ? '' : class_id),
            data: { [csrfName]: csrfHash },
            dataType: 'json',
            success: function (response) {
                if (!response || typeof response.html === 'undefined') {
                    return;
                }
                $('.student_content').html(response.html);
                if ($.fn.DataTable.isDataTable('#basic-datatable')) {
                    $('#basic-datatable').DataTable().destroy();
                }
                $('#basic-datatable').DataTable({
                    responsive: true,
                    columnDefs: [
                        { responsivePriority: 1, targets: 2 },
                        { responsivePriority: 2, targets: 3 }
                    ], language: {
                        paginate: {
                            previous: '<i class="mdi mdi-chevron-left"></i>',
                            next: '<i class="mdi mdi-chevron-right"></i>'
                        }
                     },
    drawCallback: function () {
        $('.dataTables_paginate .pagination').addClass('pagination-rounded');
    }

                });
                if (response.csrf && response.csrf.csrfName && response.csrf.csrfHash) {
                    $('input[name="' + response.csrf.csrfName + '"]').val(response.csrf.csrfHash);
                }
            }
        });
    }
</script>