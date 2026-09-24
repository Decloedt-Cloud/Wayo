<link rel="stylesheet" href="<?php echo base_url(); ?>assets/backend/css/responsive.min.css">
    <!--title-->
    <div class="col-xl-12">
        <div class="header-card">
            <div class="card-body">
                <h4 class="page-title d-inline-block">
          <i class="fas fa-shield-halved fa-fw"></i> <?php echo get_phrase('assigned_permission_for_teacher'); ?>
        </h4>
      </div> <!-- end card body-->
    </div> <!-- end card -->
  </div><!-- end col-->

<div class="mb-3">
<div class="main-card">
        <div class="card-body">
            <div class="row mt-3">
                <div class="col-md-3"></div>
                <div class="col-md-4">
                    <select name="class" id="class_id_perm" class="form-control"   required>
                        <option value=""><?php echo get_phrase('select_a_class'); ?></option>
                            <?php
                            $classes = db()->table('classes')->where('school_id', school_id())->get()->getResultArray();
                            $school_id = school_id();
                            foreach($classes as $class){
                                $total_student = db()->table('enrols')
                                    ->where('class_id', $class['id'])
                                    ->where('school_id', $school_id)
                                    ->get()
                                    ->getResultArray();
                            ?>
                                <option value="<?php echo $class['id']; ?>">
                                    <?php echo $class['name']; ?>
                                    <?php echo "(".count($total_student).")"; ?>
                                </option>
                            <?php } ?>
                    </select>
                </div>

                <div class="col-md-2 btncol">
                    <button class="btn btn-block btn-secondary" onclick="filter()" ><?php echo get_phrase('filter'); ?></button>
                </div>
            </div>
            <div class="card-body permission_content">
            	<div class="empty_box text-center">
                    <img class="mb-3" width="150px" src="<?php echo base_url('assets/backend/images/empty_box.png'); ?>" />
                    <br>
                    <span class=""><?php echo get_phrase('no_data_found'); ?></span>
                </div>
            </div>
        </div>
    </div>
 </div>
 

<!-- modyfy section -->
<script>
    function getPermissionCsrfData() {
        var csrfCookieName = '<?= config('Security')->cookieName; ?>';
        var fallbackCsrfName = '<?= csrf_token(); ?>';
        var cookieHash = null;
        var cookieParts = document.cookie ? document.cookie.split('; ') : [];
        for (var i = 0; i < cookieParts.length; i++) {
            var part = cookieParts[i];
            if (part.indexOf(csrfCookieName + '=') === 0) {
                cookieHash = decodeURIComponent(part.substring(csrfCookieName.length + 1));
                break;
            }
        }

        var csrfInput = document.querySelector('input[name="' + fallbackCsrfName + '"]');
        var csrfName = (csrfInput && csrfInput.name) ? csrfInput.name : (window.csrfName || fallbackCsrfName);
        var csrfHash = cookieHash || (csrfInput ? csrfInput.value : '') || window.csrfHash || '';

        if (!csrfName || !csrfHash) {
            return {};
        }

        var csrfData = {};
        csrfData[csrfName] = csrfHash;
        return csrfData;
    }

    $('document').ready(function(){

    });



    function filter(){
        var class_id = $('#class_id_perm').val();
 
        if(class_id != "" ){
            $.ajax({
                url: '<?php echo route('permission/filter/') ?>/'+class_id,
                success: function(response){
                    $('.permission_content').html(response);
                }
            });
        }else{
            toastr.error('<?php echo get_phrase('please_select_a_class'); ?>');
        }
    }
</script>

<!-- permission insert and update -->
<script>
    function togglePermission(checkbox_id, column_name, teacher_id){

        var value = $('#'+checkbox_id).val();
        if(value == 1){
            value = 0;
        }else{
            value = 1;
        }
           
            var class_id = $('#class_id_perm').val();
            var payload = $.extend({
                class_id: class_id,
                teacher_id: teacher_id,
                column_name: column_name,
                value: value
            }, getPermissionCsrfData());
            

        $.ajax({
            type: 'POST',
            url: '<?php echo route('permission/modify_permission/') ?>',
            data: payload,
            dataType: 'json',
            success: function(response){
              
            // Injecter le nouveau contenu HTML
            $('.permission_content').html(response.html);
            success_notify('<?php echo get_phrase('permission_updated_successfully.'); ?>');

            // Mettre à jour le jeton CSRF avec le nouveau jeton renvoyé dans la réponse
            var newCsrfName = response.csrfName;
            var newCsrfHash = response.csrfHash;
            $('input[name="' + newCsrfName + '"]').val(newCsrfHash); // Mise à jour du token CSRF
        
            }
        });

    }
</script>
