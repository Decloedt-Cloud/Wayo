<?php if($working_page == 'filter'): ?>
    <div class="row d-print-none">
        <div class="col-12">
            <div class="card ">
                <div class="row mt-3">
                    <div class="col-md-3 mb-1"></div>
                    <div class="col-md-4 mb-1">
                        <select name="class" id="class_id" class="form-control" >
                            <option value="all"><?php echo get_phrase('all_classes'); ?></option>
                            <?php
                            $classes = $this->db->get_where('classes', array('school_id' => school_id()))->result_array();
                            $school_id = school_id();
                            foreach($classes as $class){
                                $this->db->where('class_id', $class['id']); 
                                $this->db->where('school_id', $school_id);
                                $total_student = $this->db->get('enrols');
                                ?>
                                <option value="<?php echo $class['id']; ?>" <?php if($class['id'] == $class_id) echo 'selected'; ?>>
                                    <?php echo $class['name']; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <button class="btn btn-block btn-secondary" onclick="filter_student()"><?php echo get_phrase('filter'); ?></button>
                    </div>
                </div>
                <div class="card-body student_content">
                    <?php include 'list.php'; ?>
                </div>
            </div>
        </div>
    </div>
<?php elseif($working_page == 'create'): ?>
    <?php include 'create.php'; ?>
<?php elseif($working_page == 'edit'): ?>
    <?php include 'update.php'; ?>
<?php endif; ?>

<script>
$('document').ready(function(){
    // Charger tous les étudiants au démarrage
    showAllStudents();
});



function filter_student(){
    var class_id = $('#class_id').val();
 
    if(class_id != "" ){
        showAllStudents();
    }else{
        toastr.error('<?php echo get_phrase('please_select_a_class'); ?>');
    }
}

var showAllStudents = function() {
    var class_id = $('#class_id').val();
   
    var csrfName = $('input[name="<?= $this->security->get_csrf_token_name(); ?>"]').attr('name');
    var csrfHash = $('input[name="<?= $this->security->get_csrf_token_name(); ?>"]').val();
    $.ajax({
        url: '<?php echo route('student/filter/') ?>'+(class_id == 'all' ? '' : class_id),
        data: {[csrfName]: csrfHash},
        dataType: 'json',
        success: function(response){
            $('.student_content').html(response.html);
            var newCsrfName = response.csrf.csrfName;
            var newCsrfHash = response.csrf.csrfHash;
            $('input[name="' + newCsrfName + '"]').val(newCsrfHash); 
        }
    });
}
</script>