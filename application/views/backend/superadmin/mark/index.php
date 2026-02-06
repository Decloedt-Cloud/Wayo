<link rel="stylesheet" href="<?php echo base_url(); ?>assets/backend/css/responsive.min.css">
<!--title-->
<div class="col-xl-12">
    <div class="header-card">
             <div class="card-body">
                <h4 class="page-title d-inline-block">
          <i class="fas fa-star fa-fw"></i> <?php echo get_phrase('manage_marks'); ?>
        </h4>
      </div> <!-- end card body-->
    </div> <!-- end card -->
  </div><!-- end col-->

<div class="mb-3">
  <div class="main-card">
    <div class="card-body">
            <div class="row mt-3">
                <div class="col-md-3 mb-1"></div>
                <div class="col-md-2 mb-1">

                    <select name="exam" id="exam_id" class="form-control"  required>
                        <option value=""><?php echo get_phrase('select_a_exam'); ?></option>
                        <?php 
                        $user_id = $this->session->userdata('user_id');
                        $school_id = $this->db->get_where('users', array('id' => $user_id))->row('school_id');
                        $exams = $this->db->get_where('exams', array('school_id' => $school_id, 'session' => active_session()))->result_array();
                        foreach($exams as $exam){ ?>
                            <option value="<?php echo $exam['id']; ?>"><?php echo $exam['name'];?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-md-2 mb-1">
                    <select name="class" id="class_id_marks" class="form-control"  required >
                        <option value=""><?php echo get_phrase('select_a_class'); ?></option>
                        <?php
                        $classes = $this->db->get_where('classes', array('school_id' =>  $school_id))->result_array();
                        // $school_id = school_id();
                        foreach($classes as $class){
                            $this->db->where('class_id', $class['id']);
                            $this->db->where('school_id', $school_id);
                            $total_student = $this->db->get('enrols');
                            ?>
                            <option value="<?php echo $class['id']; ?>">
                                <?php echo $class['name']; ?>
                                <?php echo "(".$total_student->num_rows().")"; ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="col-md-2 btncol">
                    <button class="btn btn-block btn-secondary" onclick="filter_attendance()" ><?php echo get_phrase('filter'); ?></button>
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
    $('select.select2:not(.normal)').each(function () { $(this).select2({ dropdownParent: '#right-modal' }); }); //initSelect2(['#class_id', '#exam_id', '', '#subject_id']);
});





function filter_attendance(){
    var exam = $('#exam_id').val();
    var class_id = $('#class_id_marks').val();
  
    // var subject = $('#subject_id').val();
    // Récupérer le nom et la valeur du jeton CSRF depuis l'input caché
  var csrfName = $('input[name="<?= $this->security->get_csrf_token_name(); ?>"]').attr('name');
  var csrfHash = $('input[name="<?= $this->security->get_csrf_token_name(); ?>"]').val();

    if(class_id != ""  && exam != "" ){
        $.ajax({
            type: 'POST',
            url: '<?php echo route('mark/list') ?>',
            data: {class_id : class_id, exam : exam , [csrfName]: csrfHash},
            dataType: 'json',
            success: function(response){
                $('.mark_content').html(response.html);
                // Mettre à jour le jeton CSRF avec le nouveau jeton renvoyé dans la réponse
                var newCsrfName = response.csrf.csrfName;
                var newCsrfHash = response.csrf.csrfHash;
                $('input[name="' + newCsrfName + '"]').val(newCsrfHash); // Mise à jour du token CSRF
            }
        });
    }else{
        toastr.error('<?php echo get_phrase('please_select_in_all_fields !'); ?>');
    }
}
</script>
