<style>
/* ============================================================================
   PREMIUM FORM DESIGN
   ============================================================================ */

:root {
    --form-primary: #6366f1;
    --form-primary-rgb: 99, 102, 241;
    --form-success: #10b981;
    --form-danger: #ef4444;
    --form-warning: #f59e0b;
    --form-dark: #1e293b;
    --form-gray: #64748b;
    --form-light: #f8fafc;
    --form-border: #e2e8f0;
    --form-white: #ffffff;
}

.ec-form-container {
    padding: 0.5rem;
}

/* Form Header */
.ec-form-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 2rem;
    padding-bottom: 1.5rem;
    border-bottom: 2px solid var(--form-border);
}

.ec-form-icon {
    width: 56px;
    height: 56px;
    border-radius: 16px;
    background: linear-gradient(135deg, var(--form-primary), #8b5cf6);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
    box-shadow: 0 8px 20px rgba(var(--form-primary-rgb), 0.3);
}

.ec-form-title {
    flex: 1;
}

.ec-form-title h3 {
    margin: 0;
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--form-dark);
}

.ec-form-title p {
    margin: 0.25rem 0 0;
    font-size: 0.875rem;
    color: var(--form-gray);
}

/* Form Groups */
.ec-form-group {
    margin-bottom: 1.5rem;
    position: relative;
}

.ec-form-label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.625rem;
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--form-dark);
}

.ec-form-label i {
    color: var(--form-primary);
    font-size: 1rem;
}

.ec-form-label .ec-required {
    color: var(--form-danger);
    font-weight: 700;
}

/* Input Wrapper */
.ec-input-wrapper {
    position: relative;
}

.ec-input-icon {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--form-gray);
    font-size: 1.25rem;
    z-index: 1;
    transition: all 0.2s;
}

.ec-form-input {
    width: 100%;
    padding: 0.875rem 1rem 0.875rem 3rem;
    border: 2px solid var(--form-border);
    border-radius: 12px;
    font-size: 0.9375rem;
    background: var(--form-light);
    color: var(--form-dark);
    transition: all 0.2s;
}

.ec-form-input:hover {
    border-color: #cbd5e1;
}

.ec-form-input:focus {
    outline: none;
    border-color: var(--form-primary);
    background: var(--form-white);
    box-shadow: 0 0 0 4px rgba(var(--form-primary-rgb), 0.1);
}

.ec-form-input:focus + .ec-input-icon,
.ec-form-input:not(:placeholder-shown) + .ec-input-icon {
    color: var(--form-primary);
}

/* Form Actions */
.ec-form-actions {
    display: flex;
    gap: 1rem;
    margin-top: 2rem;
    padding-top: 1.5rem;
    border-top: 2px solid var(--form-border);
}

.ec-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 0.875rem 1.5rem;
    border-radius: 12px;
    font-size: 0.9375rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    border: none;
    min-width: 140px;
}

.ec-btn-primary {
    background: linear-gradient(135deg, var(--form-primary), #8b5cf6);
    color: white;
    box-shadow: 0 4px 12px rgba(var(--form-primary-rgb), 0.3);
}

.ec-btn-primary:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(var(--form-primary-rgb), 0.4);
}

.ec-btn-primary:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none;
}

/* Image Upload Section Styles */
.image-upload-section {
    text-align: center;
    margin-bottom: 2rem;
    padding: 1.5rem;
    background: var(--form-light);
    border-radius: 16px;
    border: 2px dashed var(--form-border);
    transition: all 0.2s;
}

.image-upload-section:hover {
    border-color: var(--form-primary);
    background: rgba(var(--form-primary-rgb), 0.05);
}

.image-preview {
    width: 120px;
    height: 120px;
    margin: 0 auto 1rem;
    border-radius: 50%;
    overflow: hidden;
    border: 4px solid white;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    position: relative;
    background: #e2e8f0;
}

.image-preview img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.logo-upload-btn label {
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    background: white;
    border: 1px solid var(--form-border);
    border-radius: 8px;
    font-size: 0.875rem;
    color: var(--form-dark);
    transition: all 0.2s;
}

.logo-upload-btn label:hover {
    border-color: var(--form-primary);
    color: var(--form-primary);
}

.image-upload {
    display: none;
}
</style>

<div class="ec-form-container">
    <!-- Form Header -->
    <div class="ec-form-header">
        <div class="ec-form-icon">
            <i class="mdi mdi-calculator"></i>
        </div>
        <div class="ec-form-title">
            <h3><?php echo get_phrase('create_accountant'); ?></h3>
            <p><?php echo get_phrase('add_new_accountant_details'); ?></p>
        </div>
    </div>

    <form method="POST" class="d-block ajaxForm" action="<?php echo route('accountant/create'); ?>" enctype="multipart/form-data">
        <input type="hidden" name="<?=$this->security->get_csrf_token_name();?>" value="<?=$this->security->get_csrf_hash();?>" />
        
        <!-- Image Upload Section -->
        <div class="image-upload-section">
            <div class="image-preview" id="image-preview">
                <img src="<?php echo base_url('uploads/users/placeholder.jpg'); ?>" alt="Profile Image">
            </div>
            <div class="logo-upload-btn">
                <label for="image_file">
                    <i class="mdi mdi-camera"></i> <?php echo get_phrase('upload_profile_photo'); ?>
                </label>
                <input type="file" class="image-upload" id="image_file" name="image_file" accept="image/*">
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <!-- Name -->
                <div class="ec-form-group">
                    <label class="ec-form-label" for="name">
                        <i class="mdi mdi-account"></i>
                        <span><?php echo get_phrase('name'); ?></span>
                        <span class="ec-required">*</span>
                    </label>
                    <div class="ec-input-wrapper">
                        <input type="text" class="ec-form-input" id="name" name="name" required>
                        <i class="mdi mdi-format-letter-case ec-input-icon"></i>
                    </div>
                    <small id="name-error" class="form-text text-danger" style="display:none;"></small>
                </div>
            </div>

            <div class="col-md-6">
                <!-- Email -->
                <div class="ec-form-group">
                    <label class="ec-form-label" for="email">
                        <i class="mdi mdi-email"></i>
                        <span><?php echo get_phrase('email'); ?></span>
                        <span class="ec-required">*</span>
                    </label>
                    <div class="ec-input-wrapper">
                        <input type="email" class="ec-form-input" id="email" name="email" required>
                        <i class="mdi mdi-at ec-input-icon"></i>
                    </div>
                    <small id="email-error" class="form-text text-danger" style="display:none;"></small>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <!-- Password -->
                <div class="ec-form-group">
                    <label class="ec-form-label" for="password">
                        <i class="mdi mdi-lock"></i>
                        <span><?php echo get_phrase('password'); ?></span>
                        <span class="ec-required">*</span>
                    </label>
                    <div class="ec-input-wrapper">
                        <input type="password" class="ec-form-input" id="password" name="password" required>
                        <i class="mdi mdi-key ec-input-icon"></i>
                    </div>
                    <small id="password-error" class="form-text text-danger" style="display:none;"></small>
                </div>
            </div>

            <div class="col-md-6">
                <!-- Phone -->
                <div class="ec-form-group">
                    <label class="ec-form-label" for="phone">
                        <i class="mdi mdi-phone"></i>
                        <span><?php echo get_phrase('phone'); ?></span>
                        <span class="ec-required">*</span>
                    </label>
                    <div class="ec-input-wrapper">
                        <input type="text" class="ec-form-input" id="phone" name="phone" required>
                        <i class="mdi mdi-phone ec-input-icon"></i>
                    </div>
                    <small id="phone-error" class="form-text text-danger" style="display:none;"></small>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <!-- Gender -->
                <div class="ec-form-group">
                    <label class="ec-form-label" for="gender">
                        <i class="mdi mdi-gender-male-female"></i>
                        <span><?php echo get_phrase('gender'); ?></span>
                        <span class="ec-required">*</span>
                    </label>
                    <div class="ec-input-wrapper">
                        <select name="gender" id="gender" class="ec-form-input" required>
                            <option value=""><?php echo get_phrase('select_a_gender'); ?></option>
                            <option value="Male"><?php echo get_phrase('male'); ?></option>
                            <option value="Female"><?php echo get_phrase('female'); ?></option>
                            <option value="Others"><?php echo get_phrase('others'); ?></option>
                        </select>
                        <i class="mdi mdi-gender-transgender ec-input-icon"></i>
                    </div>
                    <small id="gender-error" class="form-text text-danger" style="display:none;"></small>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <!-- Address -->
                <div class="ec-form-group">
                    <label class="ec-form-label" for="address">
                        <i class="mdi mdi-map-marker"></i>
                        <span><?php echo get_phrase('address'); ?></span>
                        <span class="ec-required">*</span>
                    </label>
                    <div class="ec-input-wrapper">
                        <textarea class="ec-form-input" id="address" name="address" rows="3" required></textarea>
                        <i class="mdi mdi-map-marker-radius ec-input-icon"></i>
                    </div>
                    <small id="adresse-error" class="form-text text-danger" style="display:none;"></small>
                </div>
            </div>
        </div>

        <div class="ec-form-actions">
            <button class="ec-btn ec-btn-primary" id="submit-btn" type="submit">
                <i class="mdi mdi-plus"></i> <?php echo get_phrase('create_accountant'); ?>
            </button>
        </div>
    </form>
</div>

<script>
$(document).ready(function() {

    // Image Preview
    $('#image_file').change(function(){
        let reader = new FileReader();
        reader.onload = (e) => {
            $('#image-preview img').attr('src', e.target.result);
        }
        reader.readAsDataURL(this.files[0]);
    });

    /* ============================
       TOASTR CONFIG
    ============================ */
    if(typeof toastr !== 'undefined') {
        toastr.options = {
            closeButton: true,
            progressBar: true,
            positionClass: 'toast-top-right',
            timeOut: 5000,
            showMethod: 'fadeIn',
            hideMethod: 'fadeOut',
        };
    }

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
        el.text(msg).show();
        field.css('border-color', 'var(--form-danger)');
    }

    function hideError(el, field) {
        el.hide();
        field.css('border-color', 'var(--form-border)');
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
            showError(emailHelp, "<?php echo get_phrase('Invalid_email'); ?>", emailInput);
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
        const originalBtnText = submitBtn.html();
        submitBtn.prop("disabled", true).html('<i class="mdi mdi-loading mdi-spin"></i> <?php echo get_phrase("Creating"); ?>...');

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
                if (response.status) {
                    if(typeof toastr !== 'undefined') toastr.success(response.notification);
                    $('input[name="' + response.csrf.name + '"]').val(response.csrf.hash);
                    setTimeout(() => location.reload(), 2000);
                } else {
                    if(typeof toastr !== 'undefined') toastr.error("<?php echo get_phrase('Action_not_allowed'); ?>");
                    submitBtn.prop("disabled", false).html(originalBtnText);
                }
            },
            error: function() {
                if(typeof toastr !== 'undefined') toastr.error("<?php echo get_phrase('Error_during_submission'); ?>");
                submitBtn.prop("disabled", false).html(originalBtnText);
            }
        });
    });
});
</script>

