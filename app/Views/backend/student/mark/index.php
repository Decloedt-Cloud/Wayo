<?php
$student_mark_enrolments = isset($student_mark_enrolments) && is_array($student_mark_enrolments) ? $student_mark_enrolments : [];
$student_mark_exams = isset($student_mark_exams) && is_array($student_mark_exams) ? $student_mark_exams : [];
?>

<link rel="stylesheet" href="<?php echo base_url(); ?>assets/backend/css/responsive.min.css">
<!--title-->
<div class="col-xl-12">
    <div class="header-card">
             <div class="card-body">
                <h4 class="page-title d-inline-block"> <i class="fas fa-star fa-fw"></i> <?php echo get_phrase('marks'); ?> </h4>
            </div>
        </div>
    </div>


<div class="mb-3">
  <div class="main-card">
    <div class="card-body">
            <div class="row mt-3">
                <div class="col-md-3 mb-1"></div>
                <div class="col-md-2 mb-1">
                    <select name="exam" id="exam_id" class="form-control"  required onchange="examsWiseClass(this.value)">
                        <option value=""><?php echo get_phrase('select_a_exam'); ?></option>
                        <?php foreach ($student_mark_exams as $exam) { ?>
                            <option value="<?php echo $exam['id']; ?>"><?php echo $exam['name']; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-md-2 mb-1">
                    <select name="class" id="class_id_mark" class="form-control"  required  disabled>
                        <option value=""><?php echo get_phrase('select_a_class'); ?></option>
                        <?php foreach ($student_mark_enrolments as $enrolment) { ?>
                            <option value="<?php echo $enrolment['class_id']; ?>"><?php echo $enrolment['class_name']; ?></option>
                        <?php } ?>
                    </select>
                </div>

                <div class="col-md-2">
                    <button class="btn btn-block btn-secondary" onclick="filter_marks()"><?php echo get_phrase('filter'); ?></button>
                </div>
            </div>
            <div class="card-body mark_content">
                <div class="empty_box text-center">
                    <img class="mb-3" width="150px" src="<?php echo base_url('assets/backend/images/empty_box.png'); ?>" />
                    <br>
                    <span class=""><?php echo get_phrase('no_data_found'); ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$('document').ready(function(){
    $('select.select2:not(.normal)').each(function () { $(this).select2({ dropdownParent: '#right-modal' }); });
});

function examsWiseClass(examId) {
    var classSelect = $('#class_id_mark');
    
    if (examId) {
        $.ajax({
            url: "<?php echo route('exam_class/list/'); ?>"/"+ examId,
            success: function(response){
                classSelect.html(response);
                classSelect.prop('disabled', false); // Activer le menu des classes

            }
        });
    } else {
        // Si aucun examen n'est sélectionné, désactiver les menus
        classSelect.html('<option value=""><?php echo get_phrase('select_a_class'); ?></option>');
        classSelect.prop('disabled', true);

    }
}



function filter_marks(){
    var exam = $('#exam_id').val();
    var class_id = $('#class_id_mark').val();
    var csrfName = $('input[name="<?= csrf_token(); ?>"]').attr('name');
    var csrfHash = $('input[name="<?= csrf_token(); ?>"]').val();

    if(class_id != "" && exam != ""){
        $.ajax({
            type: 'POST',
            url: '<?php echo route('mark/list') ?>',
            data: {class_id: class_id,  exam: exam, [csrfName]: csrfHash},
            dataType: 'json',
            success: function(response){
                $('.mark_content').html(response.status);
                // Mettre à jour le jeton CSRF
                var newCsrfName = response.csrf.csrfName;
                var newCsrfHash = response.csrf.csrfHash;
                $('input[name="' + newCsrfName + '"]').val(newCsrfHash);
            }
        });
    }else{
        toastr.error('<?php echo get_phrase('please_select_in_all_fields !'); ?>');
    }
}
</script>