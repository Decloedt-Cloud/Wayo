<?php $school_id = school_id(); ?>
<form method="POST" class="d-block responsive_media_query" action="<?php echo site_url('student/online_admission/assigned'); ?>">

    <!-- Champ caché pour le jeton CSRF -->
    <input type="hidden" name="<?=$this->security->get_csrf_token_name();?>" value="<?=$this->security->get_csrf_hash();?>" />

    <input type="hidden" name="student_id" value="<?php echo $param1; ?>">
    <input type="hidden" name="class_id" id="class_id" value="<?php echo $param2; ?>">
    <input type="hidden" name="school_id" value="<?php echo $param3; ?>">
    <input type="hidden" name="price" id="price" value="<?php echo $param4; ?>">
    <input type="hidden" name="currency" id="currency" value="<?php echo $param5; ?>">
    




    <div class="form-group col-md-12 mt-4">
        <button class="btn w-100 btn-primary" type="submit"><?php echo get_phrase('submit'); ?></button>
    </div>
</form>

<script type="text/javascript">

       $(document).ready(function () {
       
    });

</script>