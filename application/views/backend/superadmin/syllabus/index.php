<link rel="stylesheet" href="<?php echo base_url(); ?>assets/backend/css/responsive.min.css">
<!--title-->
<div class="col-xl-12">
    <div class="header-card">
        <div class="card-body d-flex justify-content-between align-items-center">
            <h4 class="page-title d-inline-block">
                <i class="fas fa-folder-open fa-fw"></i> <?php echo get_phrase('syllabus'); ?>
            </h4>
            <button type="button" class="btn btn-outline-primary btn-rounded alignToTitle float-end mt-1" onclick="rightModal('<?php echo site_url('modal/popup/syllabus/create'); ?>', '<?php echo htmlspecialchars(get_phrase('create_syllabus'), ENT_QUOTES); ?>')"> <i class="mdi mdi-plus"></i> <?php echo get_phrase('add_syllabus'); ?></button>
        </div> <!-- end card body-->
    </div> <!-- end card -->
</div><!-- end col-->


<div class="row">
    <div class="col-12">

        <div class="mb-3">
            <div class="main-card">
                <div class="card-body">
                    <div class="row mt-3">
                        <div class="row mb-3">
                            <div class="col-md-3 mb-1"></div>
                            <div class="col-md-4 mb-1">
                                <select name="class" id="class_id_syllabus" class="form-control" required>
                                    <option value="all"><?php echo get_phrase('all_programs'); ?></option>
                                    <?php
                                    $classes = $this->db->get_where('classes', array('school_id' => school_id()))->result_array();
                                    $school_id = school_id();
                                    foreach ($classes as $class):
                                        $this->db->where('class_id', $class['id']);
                                        $this->db->where('school_id', $school_id);
                                        $total_student = $this->db->get('enrols');
                                    ?>
                                        <option value="<?php echo $class['id']; ?>">
                                            <?php echo $class['name']; ?>
                                            <?php echo "(" . $total_student->num_rows() . ")"; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-2 btncol ">

                                <button class="btn btn-block btn-secondary" onclick="filter_syllabus()"><?php echo get_phrase('filter'); ?></button>
                            </div>
                        </div>
                        <div class="syllabus_content">
                            <?php include 'list.php'; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $('document').ready(function() {
        $('select.select2:not(.normal)').each(function() {
            $(this).select2({
                dropdownParent: '#right-modal'
            });
        }); //initSelect2(['#class_id', ]);
        showAllSyllabuses('all');
    });



    function filter_syllabus() {
        var class_id = $('#class_id_syllabus').val();
        showAllSyllabuses(class_id);
    }


    var showAllSyllabuses = function(class_id = null) {
        class_id = class_id || $('#class_id_syllabus').val();

        // Si 'all', on charge sans filtre
        $.ajax({
            url: '<?php echo route('syllabus/list/') ?>' + class_id,
            success: function(response) {
                $('.syllabus_content').html(response);
                initDataTable('basic-datatable');
            }
        });
    }
</script>