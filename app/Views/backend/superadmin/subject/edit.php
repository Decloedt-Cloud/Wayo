<?php $school_id = school_id(); ?>
<?php $subjects = db()->table('subjects')->where('id', $param1)->get()->getResultArray(); ?>
<?php foreach($subjects as $subject){ ?>
<form method="POST" class="d-block ajaxForm" action="<?php echo route('subject/update/'.$param1); ?>">
    <!-- Champ caché pour le jeton CSRF -->
    <input type="hidden" name="<?=csrf_token();?>" value="<?=csrf_hash();?>" />
    
    <div class="form-row">
        <div class="form-group mb-1">
            <label for="class"><?php echo get_phrase('class'); ?></label>
            <select name="class_id" id="class_id_on_create" class="form-control"  required>
                <option value=""><?php echo get_phrase('select_a_class'); ?></option>
                    <?php
                        $classes = db()->table('classes')->where('school_id', $school_id)->get()->getResultArray();
                        foreach($classes as $class){
                    ?>
                        <option value="<?php echo $class['id']; ?>" <?php if($class['id'] == $subject['class_id']){ echo 'selected'; } ?>><?php echo $class['name']; ?></option>
                    <?php } ?>
            </select>
            <small id="class_help" class="form-text text-muted"><?php echo get_phrase('select_a_class'); ?></small>
        </div>

        <div class="form-group mb-1">
            <label for="name"><?php echo get_phrase('subject_name'); ?></label>
            <input type="text" class="form-control" id="name" name="name" value="<?php echo $subject['name']; ?>" required>
            <small id="name_help" class="form-text text-muted"><?php echo get_phrase('provide_subject_name'); ?></small>
        </div>

        <div class="form-group  col-md-12">
            <button class="btn btn-block btn-primary" type="submit"><?php echo get_phrase('create_subject'); ?></button>
        </div>
    </div>
</form>
<?php } ?>

<script>
$(".ajaxForm").validate({}); // Jquery form validation initialization
$(".ajaxForm").submit(function(e) {
  var form = $(this);
  ajaxSubmit(e, form, showAllSubjects);
});

$(document).ready(function() {
  $('select.select2:not(.normal)').each(function () { $(this).select2({ dropdownParent: '#right-modal' }); }); //initSelect2(['#class_id_on_create']);
});
</script>
