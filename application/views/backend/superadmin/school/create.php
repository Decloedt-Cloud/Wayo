<form method="POST" class="d-block ajaxForm" action="<?php echo route('school_crud/create'); ?>">
    <!-- Champ caché pour le jeton CSRF -->
    <input type="hidden" name="<?=$this->security->get_csrf_token_name();?>" value="<?=$this->security->get_csrf_hash();?>" />
    
    <div class="form-row">
        <div class="form-group mb-1">
            <label for="name"><?php echo get_phrase('name'); ?><span class="required"> * </span></label>
            <input type="text" class="form-control" id="name" name = "name" >
            <small id="" class="form-text"></small>
        </div>

        

        <div class="form-group mb-1">
            <label for="description"><?php echo get_phrase('description'); ?><span class="required"> * </span></label>
            <textarea class="form-control" id="description" name = "description" rows="5" ></textarea>
            <small id="" class="form-text"></small>
        </div>

       
        <div class="form-group mb-1">
            <label for="phone"><?php echo get_phrase('phone_number'); ?><span class="required"> * </span></label>
            <input type="text" class="form-control" id="phone" name = "phone" >
            <small id="" class="form-text"></small>
        </div>

        <div class="form-group mb-1">
            <label for="access"><?php echo get_phrase('Access'); ?><span class="required"> * </span></label>
            <select name="access" id="access" class="form-control"  >
                <option value=""><?php echo get_phrase('select_a_access'); ?></option>
                <option value="1"><?php echo get_phrase('public'); ?></option>
                <option value="0"><?php echo get_phrase('privé'); ?></option>
              
            </select>
            <small id="" class="form-text"></small>
        </div>


        <div class="form-group mb-1">
            <label for="access"><?php echo get_phrase('Category'); ?><span class="required"> * </span></label>
            <select name="category" id="category" class="form-control"  >
                <option value=""><?php echo get_phrase('select_a_category'); ?></option>
                <?php $categories = $this->db->get_where('categories', array())->result_array(); ?>
                <?php foreach ($categories as $categorie): ?>
                    <option value="<?php echo $categorie['name']; ?>"><?php echo $categorie['name']; ?></option>
                <?php endforeach; ?>    
            </select>
            <small id="" class="form-text"><?php echo get_phrase('provide_admin_access'); ?></small>
        </div>

        

        <div class="form-group mb-1">
            <label for="phone"><?php echo get_phrase('address'); ?><span class="required"> * </span></label>
            <textarea class="form-control" id="address" name = "address" rows="5" ></textarea>
            <small id="" class="form-text"></small>
        </div>

        <div class="form-group mb-1">
          <!-- <label for="image_file"><?php //echo get_phrase('upload_image'); ?></label>
          <input type="file" class="form-control" id="school_image" name = "school_image"> -->
          <label for="image_file"><?php echo get_phrase('upload_image'); ?></label>
          <div id="photo-preview" class="photo-preview">
                <!-- L'image sélectionnée apparaîtra ici -->
                <img class="rounded-circle" style="width: 30%;height: 50%;object-fit: cover;border-radius: 50%;"  id="default-avatar" src="<?php echo $this->user_model->get_school_image($param1); ?>">
          </div>
          <input id="school_image" type="file" class="form-control" name="school_image" accept=".jpg, .jpeg, .png" >
          <small id="" class="form-text"></small>

        </div>

        <div class="form-group mt-2 col-md-12">
            <button class="btn btn-block btn-primary" type="submit"><?php echo get_phrase('create_school'); ?></button>
        </div>
    </div>
</form>


<script>
$(document).ready(function () {

    /* ============================
       TOASTR CONFIG
    ============================ */
    toastr.options = {
        closeButton: true,
        progressBar: true,
        positionClass: 'toast-top-right',
        timeOut: 4000,
        showMethod: 'fadeIn',
        hideMethod: 'fadeOut'
    };

    /* ============================
       SELECT2 INIT
    ============================ */
    $('select.select2:not(.normal)').each(function () {
        $(this).select2({ dropdownParent: '#right-modal' });
    });

    /* ============================
       REGEX
    ============================ */
    const nameRegex = /^[a-zA-ZÀ-ÿ0-9\s]{3,}$/;
    const phoneRegex = /^[0-9+\- ]{6,}$/;

    /* ============================
       ERROR HANDLING
    ============================ */
    function showError(input, msg) {
        input.addClass('is-invalid');
        input.closest(".form-group").find("small.form-text").addClass("text-danger").text(msg).show();
    }

    function clearError(input) {
        input.removeClass('is-invalid');
        input.closest(".form-group").find("small.form-text").removeClass("text-danger").text("").hide();
    }

    /* ============================
       REAL-TIME VALIDATION
    ============================ */
    $("#name").on("input", function () {
        if (!nameRegex.test($(this).val().trim())) showError($(this), "<?php echo get_phrase('invalid_name_minimum_3_characters'); ?>");
        else clearError($(this));
    });

    $("#description").on("input", function () {
        if ($(this).val().trim().length < 10) showError($(this), "<?php echo get_phrase('description_too_short'); ?>");
        else clearError($(this));
    });

    $("#phone").on("input", function () {
        if (!phoneRegex.test($(this).val().trim())) showError($(this), "<?php echo get_phrase('invalid_phone_number'); ?>");
        else clearError($(this));
    });

    $("#access, #category").on("change", function () {
        if ($(this).val() === "") showError($(this), "<?php echo get_phrase('please_select_field'); ?>");
        else clearError($(this));
    });

    $("#address").on("input", function () {
        if ($(this).val().trim().length < 5) showError($(this), "<?php echo get_phrase('address_too_short'); ?>");
        else clearError($(this));
    });

    /* ============================
       IMAGE PREVIEW + VALIDATION
    ============================ */
    $("#school_image").on("change", function () {
        const file = this.files[0];
        if (!file) return;

        const validTypes = ["image/jpeg","image/png","image/jpg"];
        if (!validTypes.includes(file.type)) { showError($(this), "<?php echo get_phrase('invalid_image_format'); ?>"); return; }
        if (file.size > 2*1024*1024) { showError($(this), "<?php echo get_phrase('image_too_big_max_2mb'); ?>"); return; }

        clearError($(this));

        const reader = new FileReader();
        reader.onload = function(e) {
            $("#photo-preview").html('<img src="'+e.target.result+'" style="width:30%;height:50%;object-fit:cover;border-radius:50%;">');
        };
        reader.readAsDataURL(file);
    });

    /* ============================
       FORM SUBMIT AJAX
    ============================ */
    $(document).on("submit", ".ajaxForm", function(e) {
        e.preventDefault();
        let valid = true;

        // FINAL VALIDATION
        if (!nameRegex.test($("#name").val().trim())) { showError($("#name"), "<?php echo get_phrase('invalid_name'); ?>"); valid=false; }
        if ($("#description").val().trim().length<10) { showError($("#description"), "<?php echo get_phrase('description_too_short'); ?>"); valid=false; }
        if (!phoneRegex.test($("#phone").val().trim())) { showError($("#phone"), "<?php echo get_phrase('invalid_phone'); ?>"); valid=false; }
        if ($("#access").val()==="") { showError($("#access"), "<?php echo get_phrase('please_select_access'); ?>"); valid=false; }
        if ($("#category").val()==="") { showError($("#category"), "<?php echo get_phrase('please_select_category'); ?>"); valid=false; }
        if ($("#address").val().trim().length<5) { showError($("#address"), "<?php echo get_phrase('address_too_short'); ?>"); valid=false; }
        if ($("#school_image")[0].files.length===0) { showError($("#school_image"), "<?php echo get_phrase('please_upload_image'); ?>"); valid=false; }

        if (!valid) return;

        const submitBtn = $(this).find("button[type=submit]");
        submitBtn.prop("disabled",true).html('<i class="mdi mdi-loading mdi-spin"></i> <?php echo get_phrase("creating"); ?>...');

        const formData = new FormData(this);

        $.ajax({
            url: $(this).attr("action"),
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function(res){
                let response;
                try { response = JSON.parse(res); } 
                catch(err) {
                    toastr.error("<?php echo get_phrase('invalid_server_response'); ?>");
                    submitBtn.prop("disabled",false).html("<?php echo get_phrase('create_school'); ?>");
                    console.log(res);
                    return;
                }

                submitBtn.prop("disabled",false).html("<?php echo get_phrase('create_school'); ?>");

                if(response.status===true){
                    toastr.success(response.notification);
                    if(response.csrf) $('input[name="'+response.csrf.name+'"]').val(response.csrf.hash);
                    setTimeout(()=>location.reload(),1500);
                } else {
                    toastr.error(response.message ?? "<?php echo get_phrase('action_not_allowed'); ?>");
                }
            },
            error: function(xhr){
                submitBtn.prop("disabled",false).html("<?php echo get_phrase('create_school'); ?>");
                toastr.error("<?php echo get_phrase('error_submitting_form'); ?>");
                console.log(xhr.responseText);
            }
        });
    });

});
</script>





<script>
    $(document).ready(function () {
        $('select.select2:not(.normal)').each(function () { $(this).select2({ dropdownParent: '#right-modal' }); });
    });
    

    $(".ajaxForm").validate({}); // Jquery form validation initialization
    $(".ajaxForm").submit(function(e) {
        var form = $(this);
        ajaxSubmit(e, form, showAllSchools);
    });

    document.getElementById('school_image').addEventListener('change', function(event) {
  const file = event.target.files[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = function(e) {
      const preview = document.getElementById('photo-preview');
      preview.innerHTML = '<img src="' + e.target.result + '" style="width: 30%;height: 50%;object-fit: cover;border-radius: 50%;" alt="Photo preview" />';
    };
    reader.readAsDataURL(file);
  }
});
</script>