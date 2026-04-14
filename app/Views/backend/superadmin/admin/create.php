<form method="POST" class="d-block ajaxForm" action="<?php echo route('admin/create'); ?>">
    <!-- Champ caché pour le jeton CSRF -->
    <input type="hidden" name="<?=csrf_token();?>" value="<?=csrf_hash();?>" />
    
    <div class="form-row">
        <div class="form-group mb-1">
            <label for="name"><?php echo get_phrase('name'); ?><span class="required"> * </span></label>
            <input type="text" class="form-control" id="name" name = "name" >
            <small id="" class="form-text"></small>
        </div>

        <div class="form-group mb-1">
            <label for="email"><?php echo get_phrase('email'); ?><span class="required"> * </span></label>
            <input type="email" class="form-control" id="email" name = "email" >
            <small id="" class="form-text"></small>
        </div>

        <div class="form-group mb-1">
            <label for="password"><?php echo get_phrase('password'); ?><span class="required"> * </span></label>
            <input type="password" class="form-control" id="password" name = "password" >
            <small id="" class="form-text"></small>
        </div>

        <div class="form-group mb-1">
            <label for="phone"><?php echo get_phrase('phone_number'); ?><span class="required"> * </span></label>
            <input type="text" class="form-control" id="phone" name = "phone" >
            <small id="" class="form-text"></small>
        </div>


        <div class="form-group mb-1">
            <label for="gender"><?php echo get_phrase('admin_of'); ?></label>
            <select name="school_id" id="school_id" class="form-control" >
                <option value=""><?php echo get_phrase('select_a_school'); ?></option>
                <?php $schools = $this->crud_model->get_schools(); ?>
                <?php foreach ($schools as $school): ?>
                    <option value="<?php echo $school['id']; ?>"><?php echo $school['name']; ?></option>
                <?php endforeach; ?>
            </select>
            <small id="" class="form-text"></small>
        </div>

        <div class="form-group mb-1">
            <label for="gender"><?php echo get_phrase('gender'); ?></label>
            <select name="gender" id="gender" class="form-control" >
                <option value=""><?php echo get_phrase('select_a_gender'); ?></option>
                <option value="Male"><?php echo get_phrase('male'); ?></option>
                <option value="Female"><?php echo get_phrase('female'); ?></option>
                <option value="Others"><?php echo get_phrase('others'); ?></option>
            </select>
            <small id="" class="form-text"></small>
        </div>

    

        <div class="form-group mb-1">
            <label for="phone"><?php echo get_phrase('address'); ?><span class="required"> * </span></label>
            <textarea class="form-control" id="address" name = "address" rows="5" ></textarea>
            <small id="" class="form-text"></small>
        </div>

        <div class="form-group mt-2 col-md-12">
            <button class="btn btn-block btn-primary" type="submit"><?php echo get_phrase('create_admin'); ?></button>
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
        hideMethod: 'fadeOut',
    };

    /* ============================
       REGEX VALIDATION
    ============================ */
    var nameRegex = /^[a-zA-ZÀ-ÿ\s]{3,}$/;
    var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    var phoneRegex = /^[0-9+\- ]{6,}$/;

    function showError(input, msg) {
        input.addClass('is-invalid');
        input.next('small').addClass('text-danger').text(msg).show();
    }

    function clearError(input) {
        input.removeClass('is-invalid');
        input.next('small').removeClass('text-danger').show();
    }

    /* ============================
       REAL-TIME VALIDATION
    ============================ */

    $("#name").on("input", function () {
        const value = $(this).val().trim();
        if (!nameRegex.test(value)) {
            showError($(this), "<?php echo get_phrase('invalid_name_minimum_3_letters'); ?>");
        } else {
            clearError($(this));
        }
    });

    $("#email").on("input", function () {
        if (!emailRegex.test($(this).val().trim())) {
            showError($(this), "<?php echo get_phrase('invalid_email_format'); ?>");
        } else {
            clearError($(this));
        }
    });

    $("#password").on("input", function () {
        if ($(this).val().trim().length < 6) {
            showError($(this), "<?php echo get_phrase('password_must_be_at_least_6_characters'); ?>");
        } else {
            clearError($(this));
        }
    });

    $("#phone").on("input", function () {
        if (!phoneRegex.test($(this).val().trim())) {
            showError($(this), "<?php echo get_phrase('invalid_phone_number'); ?>");
        } else {
            clearError($(this));
        }
    });

    $("#school_id").on("change", function () {
        if ($(this).val() === "") {
            showError($(this), "<?php echo get_phrase('please_select_a_community'); ?>");
        } else {
            clearError($(this));
        }
    });

    $("#gender").on("change", function () {
        if ($(this).val() === "") {
            showError($(this), "<?php echo get_phrase('please_select_gender'); ?>");
        } else {
            clearError($(this));
        }
    });

    $("#address").on("input", function () {
        if ($(this).val().trim().length < 5) {
            showError($(this), "<?php echo get_phrase('address_too_short'); ?>");
        } else {
            clearError($(this));
        }
    });

    /* ============================
       FORM SUBMIT (AJAX)
    ============================ */

    $(".ajaxForm").on("submit", function (e) {
        e.preventDefault();
        let valid = true;

        // Champs obligatoires avec regex
        if (!nameRegex.test($("#name").val().trim())) { showError($("#name"), "<?php echo get_phrase('Invalid_name'); ?>"); valid = false; }
        if (!emailRegex.test($("#email").val().trim())) { showError($("#email"), "<?php echo get_phrase('Invalid_email'); ?>"); valid = false; }
        if ($("#password").val().trim().length < 6) { showError($("#password"), "<?php echo get_phrase('Password_too_short'); ?>"); valid = false; }
        if (!phoneRegex.test($("#phone").val().trim())) { showError($("#phone"), "<?php echo get_phrase('Invalid_phone'); ?>"); valid = false; }
        if ($("#school_id").val() === "") { showError($("#school_id"), "<?php echo get_phrase('Select_a_community'); ?>"); valid = false; }
        if ($("#gender").val() === "") { showError($("#gender"), "<?php echo get_phrase('Select_gender'); ?>"); valid = false; }
        if ($("#address").val().trim().length < 5) { showError($("#address"), "<?php echo get_phrase('Address_too_short'); ?>"); valid = false; }

        if (!valid) return;

        // Disable button
        const submitBtn = $(this).find("button[type=submit]");
        submitBtn.prop("disabled", true).html('<i class="fa-solid fa-spinner fa-spin"></i> <?php echo get_phrase("creating"); ?>...');

        const formData = new FormData(this);

        $.ajax({
            url: $(this).attr("action"),
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            dataType: "json",

            success: function (response) {
                submitBtn.prop("disabled", false).html("<?php echo get_phrase('create_admin'); ?>");

                if (response.status) {
                    toastr.success(response.notification);
                    $('input[name="' + response.csrf.name + '"]').val(response.csrf.hash);
                    setTimeout(() => location.reload(), 2000);
                } else {
                    toastr.error("<?php echo get_phrase('action_not_allowed'); ?>");
                }
            },

            error: function () {
                submitBtn.prop("disabled", false).html("<?php echo get_phrase('create_admin'); ?>");
                toastr.error("<?php echo get_phrase('error_submitting_form'); ?>");
            }
        });

    });

});
</script>
