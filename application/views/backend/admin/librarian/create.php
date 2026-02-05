<link rel="stylesheet" href="<?php echo base_url();?>assets/backend/css/edit-design-button.min.css">

<form method="POST" class="d-block ajaxForm" action="<?php echo route('librarian/create'); ?>">
  <!-- Champ caché pour le jeton CSRF -->
  <input type="hidden" name="<?=$this->security->get_csrf_token_name();?>" value="<?=$this->security->get_csrf_hash();?>" />
  
  <div class="form-row">
    <div class="form-group mb-1">
      <label for="name"><?php echo get_phrase('name'); ?></label>
      <input type="text" class="form-control" id="name" name = "name">
      <small id="name-error" class="form-text "></small>
    </div>

    <div class="form-group mb-1">
      <label for="email"><?php echo get_phrase('email'); ?></label>
      <input type="email" class="form-control" id="email" name = "email">
      <small id="email-error" class="form-text"></small>
    </div>

    <div class="form-group mb-1">
      <label for="password"><?php echo get_phrase('password'); ?></label>
      <input type="password" class="form-control" id="password" name = "password">
      <small id="password-error" class="form-text"></small>
    </div>

    <div class="form-group mb-1">
      <label for="phone"><?php echo get_phrase('phone'); ?></label>
      <input type="text" class="form-control" id="phone" name = "phone">
      <small id="phone-error" class="form-text"></small>
    </div>

    <div class="form-group mb-1">
      <label for="gender"><?php echo get_phrase('gender'); ?></label>
      <select name="gender" id="gender" class="form-control" >
        <option value=""><?php echo get_phrase('select_a_gender'); ?></option>
        <option value="Male"><?php echo get_phrase('male'); ?></option>
        <option value="Female"><?php echo get_phrase('female'); ?></option>
        <option value="Others"><?php echo get_phrase('others'); ?></option>
      </select>
      <small id="gender-error" class="form-text"></small>
    </div>


    <div class="form-group mb-1">
      <label for="address"><?php echo get_phrase('address'); ?></label>
      <textarea class="form-control" id="address" name = "address" rows="5" ></textarea>
      <small id="adresse-error" class="form-text"></small>
    </div>

    <div class="form-group  col-md-12">
      <button class="btn btn-primary btn-l px-4" id="update-btn" type="submit"><i class="mdi mdi-plus"></i><?php echo get_phrase('create_librarian'); ?></button>
    </div>
  </div>
</form>

<script>
$(document).ready(function() {

    /* ============================
       TOASTR CONFIG
    ============================ */
    toastr.options = {
        closeButton: true,
        progressBar: true,
        positionClass: 'toast-top-right',
        timeOut: 5000,
        showMethod: 'fadeIn',
        hideMethod: 'fadeOut',
    };

    /* ============================
       CSRF
    ============================ */
    function getCsrfToken() {
        var csrfName = $('input[name="<?=$this->security->get_csrf_token_name();?>"]').attr('name');
        var csrfHash = $('input[name="<?=$this->security->get_csrf_token_name();?>"]').val();
        return { csrfName, csrfHash };
    }

    /* ============================
       ELEMENTS
    ============================ */
    const nameInput = $('#name');
    const emailInput = $('#email');
    const passwordInput = $('#password');
    const phoneInput = $('#phone');
    const genderInput = $('#gender');
    const addressInput = $('#address');

    const nameHelp = $('#name-error');
    const emailHelp = $('#email-error');
    const passwordHelp = $('#password-error');
    const phoneHelp = $('#phone-error');
    const genderHelp = $('#gender-error');
    const addressHelp = $('#adresse-error');

    /* ============================
       REGEX
    ============================ */
    const nameRegex = /^[A-Za-zÀ-ÖØ-öø-ÿ ]{3,50}$/;
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    const phoneRegex = /^[0-9 +\-]{6,20}$/;

    /* ============================
       HELP FUNCTIONS
    ============================ */
    function showError(el, msg, field) {
        el.addClass('text-danger').text(msg).show();
        field.addClass('is-invalid');
    }

    function hideError(el, field) {
        el.hide();
        field.removeClass('is-invalid');
    }

    /* ============================
       REAL-TIME VALIDATION
    ============================ */
    nameInput.on("input", function() {
        if (!nameRegex.test($(this).val().trim())) {
            showError(nameHelp, "<?php echo get_phrase('Invalid_name_(minimum_3_letters,_letters_only)'); ?>", nameInput);
        } else {
            hideError(nameHelp, nameInput);
        }
    });

    emailInput.on("input", function() {
        if (!emailRegex.test($(this).val())) {
            showError(emailHelp, "Email invalide", emailInput);
        } else {
            hideError(emailHelp, emailInput);
        }
    });

    passwordInput.on("input", function() {
        if ($(this).val().length < 6) {
            showError(passwordHelp, "<?php echo get_phrase('Password_too_short_(min_6_characters)'); ?>", passwordInput);
        } else {
            hideError(passwordHelp, passwordInput);
        }
    });

    phoneInput.on("input", function() {
        if (!phoneRegex.test($(this).val())) {
            showError(phoneHelp, "<?php echo get_phrase('Invalid number (min 6 digits)'); ?>", phoneInput);
        } else {
            hideError(phoneHelp, phoneInput);
        }
    });

    genderInput.on("change", function() {
        if (!$(this).val()) {
            showError(genderHelp, "<?php echo get_phrase('Please_select_a_gender'); ?>", genderInput);
        } else {
            hideError(genderHelp, genderInput);
        }
    });

    addressInput.on("input", function() {
        if ($(this).val().trim().length < 5) {
            showError(addressHelp, "<?php echo get_phrase('Address_too_short_(min_5_characters)'); ?>", addressInput);
        } else {
            hideError(addressHelp, addressInput);
        }
    });

    /* ============================
       FORM SUBMIT
    ============================ */
    $(".ajaxForm").submit(function(e) {
        e.preventDefault();

        let isValid = true;

        // ============= VALIDATION FINALE ============= //
        if (!nameRegex.test(nameInput.val().trim())) {
            showError(nameHelp, "<?php echo get_phrase('Invalid_name_(minimum_3_letters)'); ?>", nameInput);
            isValid = false;
        }
        if (!emailRegex.test(emailInput.val())) {
            showError(emailHelp, "<?php echo get_phrase('Invalid_email'); ?>", emailInput);
            isValid = false;
        }
        if (passwordInput.val().length < 6) {
            showError(passwordHelp, "<?php echo get_phrase('Password_too_short'); ?>", passwordInput);
            isValid = false;
        }
        if (!phoneRegex.test(phoneInput.val())) {
            showError(phoneHelp, "<?php echo get_phrase('Invalid number'); ?>", phoneInput);
            isValid = false;
        }
        if (!genderInput.val()) {
            showError(genderHelp, "<?php echo get_phrase('Please_choose_a_gender.'); ?>", genderInput);
            isValid = false;
        }
        if (addressInput.val().trim().length < 5) {
            showError(addressHelp, "<?php echo get_phrase('Address_too_short'); ?>", addressInput);
            isValid = false;
        }

        if (!isValid) return;

        // ============= AJAX SUBMISSION ============= //
        const submitBtn = $(this).find("button[type='submit']");
        submitBtn.prop("disabled", true).html('<i class="mdi mdi-loading mdi-spin"></i> Creating...');

        const csrf = getCsrfToken();
        const formData = new FormData(this);
        formData.append(csrf.csrfName, csrf.csrfHash);

        $.ajax({
            url: $(this).attr("action"),
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            dataType: "json",
            success: function(response) {
                submitBtn.prop("disabled", false).html('<i class="mdi mdi-plus"></i> <?php echo get_phrase('Create'); ?>');

                if (response.status) {
                    toastr.success(response.notification);
                    $('input[name="' + response.csrf.name + '"]').val(response.csrf.hash);
                    setTimeout(() => location.reload(), 2000);
                } else {
                    toastr.error("<?php echo get_phrase('Action_not_allowed'); ?>");
                }
            },
            error: function() {
                toastr.error("<?php echo get_phrase('Error_during_submission'); ?>");
                submitBtn.prop("disabled", false).html('<i class="mdi mdi-plus"></i> <?php echo get_phrase('Create'); ?>');
            }
        });
    });
});
</script>