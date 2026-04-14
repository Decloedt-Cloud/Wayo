<?php $classes = isset($student_create_classes) && is_array($student_create_classes) ? $student_create_classes : []; ?>
<?php $student_user_id = isset($student['user_id']) ? $student['user_id'] : ''; ?>

<form method="POST" class="d-block" action="<?php echo site_url('admin/student/create_single_student/submit'); ?>" id="single_student_admission_form" enctype="multipart/form-data" novalidate>
    <!-- CSRF Token -->
    <input type="hidden" name="<?= csrf_token(); ?>" value="<?= csrf_hash(); ?>" />

    <!-- Image Upload Section -->
    <div class="image-upload-section">
        <div class="image-preview" id="student-image-preview">
            <img src="<?php echo $this->user_model->get_user_image($student_user_id) . '?v=' . time(); ?>" alt="Student Profile Image" class="preview-image">
        </div>
        <div class="logo-upload-btn">
            <label for="student_image">
                <i class="mdi mdi-camera"></i> <?php echo get_phrase('upload_profile_photo'); ?>
            </label>
            <input id="student_image" type="file" class="image-upload" name="student_image" accept="image/*" data-preview="student-image-preview">
        </div>
    </div>

    <!-- Personal Information -->
    <div class="row">
        <div class="col-md-6">
            <!-- Full Name -->
            <div class="ec-form-group">
                <label class="ec-form-label">
                    <i class="mdi mdi-account"></i>
                    <span><?php echo get_phrase('Full_name'); ?></span>
                    <span class="ec-required">*</span>
                </label>
                <div class="ec-input-wrapper">
                    <input type="text" id="name" name="name" class="ec-form-input" placeholder="<?php echo get_phrase("enter_full_name") ?>" required>
                    <i class="mdi mdi-format-letter-case ec-input-icon"></i>
                    <i class="mdi mdi-check-circle ec-validation-icon success"></i>
                    <i class="mdi mdi-alert-circle ec-validation-icon error"></i>
                </div>
                <div class="ec-error-message" id="name_error">
                    <i class="mdi mdi-alert-circle"></i>
                    <span><?php echo get_phrase('please_enter_a_valid_name'); ?></span>
                </div>
            </div>
        </div>

        <div class="col-md-6">
             <!-- Email -->
            <div class="ec-form-group">
                <label class="ec-form-label">
                    <i class="mdi mdi-email"></i>
                    <span><?php echo get_phrase('email'); ?></span>
                    <span class="ec-required">*</span>
                </label>
                <div class="ec-input-wrapper">
                    <input type="email" id="email" name="email" class="ec-form-input" placeholder="<?php echo get_phrase("enter_email_address") ?>" required>
                    <i class="mdi mdi-at ec-input-icon"></i>
                    <i class="mdi mdi-check-circle ec-validation-icon success"></i>
                    <i class="mdi mdi-alert-circle ec-validation-icon error"></i>
                </div>
                <div class="ec-error-message" id="email_error">
                    <i class="mdi mdi-alert-circle"></i>
                    <span><?php echo get_phrase('please_enter_a_valid_email'); ?></span>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <!-- Class -->
            <div class="ec-form-group">
                <label class="ec-form-label">
                    <i class="mdi mdi-school"></i>
                    <span><?php echo get_phrase('class'); ?></span>
                    <span class="ec-required">*</span>
                </label>
                <div class="ec-input-wrapper">
                    <select name="class_id" id="class_id_add" class="ec-form-input" required>
                        <option value=""><?php echo get_phrase('select_a_class'); ?></option>
                        <?php foreach ($classes as $class) { ?>
                            <option value="<?php echo $class['id']; ?>"><?php echo $class['name']; ?></option>
                        <?php } ?>
                    </select>
                    <i class="mdi mdi-google-classroom ec-input-icon"></i>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <!-- Gender -->
            <div class="ec-form-group">
                <label class="ec-form-label">
                    <i class="mdi mdi-gender-male-female"></i>
                    <span><?php echo get_phrase('gender'); ?></span>
                    <span class="ec-required">*</span>
                </label>
                <div class="ec-input-wrapper">
                    <select name="gender" id="gender" class="ec-form-input" required>
                        <option value=""><?php echo get_phrase('select_gender'); ?></option>
                        <option value="Male"><?php echo get_phrase('male'); ?></option>
                        <option value="Female"><?php echo get_phrase('female'); ?></option>
                        <option value="Others"><?php echo get_phrase('others'); ?></option>
                    </select>
                    <i class="mdi mdi-gender-male-female ec-input-icon"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?php 
            // Calculer la date limite pour 18 ans
            $today = date('Y-m-d');
            $eighteen_years_ago = date('Y-m-d', strtotime('-18 years'));
            ?>
            <!-- Birthday -->
            <div class="ec-form-group">
                <label class="ec-form-label">
                    <i class="mdi mdi-calendar"></i>
                    <span><?php echo get_phrase('birthday'); ?></span>
                    <span class="ec-required">*</span>
                </label>
                <div class="ec-input-wrapper">
                    <input type="date" max="<?php echo $eighteen_years_ago; ?>" id="birthday" name="birthday" class="ec-form-input" required>
                    <i class="mdi mdi-calendar-range ec-input-icon"></i>
                </div>
                <div class="ec-error-message" id="birthday_error">
                     <i class="mdi mdi-alert-circle"></i>
                    <span><?php echo get_phrase('invalid_birth_date'); ?></span>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <!-- Phone -->
            <div class="ec-form-group">
                <label class="ec-form-label">
                    <i class="mdi mdi-phone"></i>
                    <span><?php echo get_phrase('phone'); ?></span>
                    <span class="ec-required">*</span>
                </label>
                <div class="ec-input-wrapper">
                    <input type="text" id="phone" name="phone" class="ec-form-input" placeholder="<?php echo get_phrase("enter_phone_number") ?>" required>
                    <i class="mdi mdi-cellphone ec-input-icon"></i>
                    <i class="mdi mdi-check-circle ec-validation-icon success"></i>
                    <i class="mdi mdi-alert-circle ec-validation-icon error"></i>
                </div>
                <div class="ec-error-message" id="phone_error">
                     <i class="mdi mdi-alert-circle"></i>
                    <span><?php echo get_phrase('invalid_phone_number'); ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Address Section -->
    <div class="ec-info-card mt-3">
        <div class="ec-info-card-icon">
            <i class="mdi mdi-map-marker-radius"></i>
        </div>
        <div class="ec-info-card-content">
            <h4><?php echo get_phrase('address_details'); ?></h4>
            <p><?php echo get_phrase('provide_student_address_information'); ?></p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <!-- Street -->
            <div class="ec-form-group">
                <label class="ec-form-label">
                    <i class="mdi mdi-road"></i>
                    <span><?php echo get_phrase('Rue'); ?></span>
                    <span class="ec-required">*</span>
                </label>
                <div class="ec-input-wrapper">
                    <input type="text" id="Street" name="Street" class="ec-form-input" placeholder="<?php echo get_phrase('enter_street_name'); ?>" required>
                    <i class="mdi mdi-map-marker ec-input-icon"></i>
                </div>
                 <div class="ec-error-message" id="street_error">
                     <i class="mdi mdi-alert-circle"></i>
                    <span><?php echo get_phrase('invalid_street'); ?></span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <!-- Number -->
            <div class="ec-form-group">
                <label class="ec-form-label">
                    <i class="mdi mdi-home-floor-1"></i>
                    <span><?php echo get_phrase('Numéro'); ?></span>
                    <span class="ec-required">*</span>
                </label>
                <div class="ec-input-wrapper">
                    <input type="text" id="communityNumber" name="number" class="ec-form-input" placeholder="No." required>
                    <i class="mdi mdi-numeric ec-input-icon"></i>
                </div>
                 <div class="ec-error-message" id="number_error">
                     <i class="mdi mdi-alert-circle"></i>
                    <span><?php echo get_phrase('invalid_number'); ?></span>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <!-- City -->
            <div class="ec-form-group">
                <label class="ec-form-label">
                    <i class="mdi mdi-city"></i>
                    <span><?php echo get_phrase('Ville'); ?></span>
                    <span class="ec-required">*</span>
                </label>
                <div class="ec-input-wrapper">
                    <input type="text" id="communityCity" name="city" class="ec-form-input" placeholder="<?php echo get_phrase('enter_city'); ?>" required>
                    <i class="mdi mdi-city-variant ec-input-icon"></i>
                </div>
                 <div class="ec-error-message" id="city_error">
                     <i class="mdi mdi-alert-circle"></i>
                    <span><?php echo get_phrase('invalid_city'); ?></span>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <!-- Postal Code -->
            <div class="ec-form-group">
                <label class="ec-form-label">
                    <i class="mdi mdi-post"></i>
                    <span><?php echo get_phrase('code_postal'); ?></span>
                    <span class="ec-required">*</span>
                </label>
                <div class="ec-input-wrapper">
                    <input type="text" id="communityPostalCode" name="postal_code" class="ec-form-input" placeholder="<?php echo get_phrase('postal_code'); ?>" required>
                    <i class="mdi mdi-mailbox ec-input-icon"></i>
                </div>
                 <div class="ec-error-message" id="postal_error">
                     <i class="mdi mdi-alert-circle"></i>
                    <span><?php echo get_phrase('invalid_postal_code'); ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- VAT Number (Optional) -->
    <div class="ec-form-group">
        <label class="ec-form-label">
            <i class="mdi mdi-percent"></i>
            <span><?php echo get_phrase('numero_de_tva'); ?></span>
            <span class="ec-form-hint"><?php echo get_phrase('optional'); ?></span>
        </label>
        <div class="ec-input-wrapper">
            <input type="text" id="VAT_number" name="VAT_number" class="ec-form-input" placeholder="<?php echo get_phrase('enter_vat_number'); ?>">
            <i class="mdi mdi-finance ec-input-icon"></i>
        </div>
         <div class="ec-error-message" id="vat_error">
             <i class="mdi mdi-alert-circle"></i>
            <span><?php echo get_phrase('invalid_vat_number'); ?></span>
        </div>
    </div>

    <!-- Form Actions -->
    <div class="ec-form-actions justify-content-center">
        <button type="submit" class="ec-btn ec-btn-primary" id="submit-btn">
            <i class="mdi mdi-account-check"></i>
            <span><?php echo get_phrase('create_student'); ?></span>
        </button>
    </div>
</form>

<!-- Script -->
<script type="text/javascript">
$(document).ready(function() {
    
    // Image preview
    $('#student_image').change(function(){
        const file = this.files[0];
        if (file){
            let reader = new FileReader();
            reader.onload = function(event){
                $('#student-image-preview img').attr('src', event.target.result);
            }
            reader.readAsDataURL(file);
        }
    });

    const regex = {
        name: /^[A-Za-zÀ-ÖØ-öø-ÿ' -]{2,50}$/,
        email: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
        phone: /^(\+?\d{1,3}[- ]?)?\d{8,15}$/,
        street: /^[A-Za-z0-9À-ÖØ-öø-ÿ' -]{3,100}$/,
        number: /^\d{1,5}$/,
        city: /^[A-Za-zÀ-ÖØ-öø-ÿ' -]{2,50}$/,
        postal: /^\d{4,10}$/,
        vat: /^[A-Za-z0-9]{8,15}$/
    };

    // ======= Fonctions utilitaires =======
    function showError(input, errorId) {
        $(input).addClass('is-invalid').removeClass('is-valid');
        $('#' + errorId).addClass('show');
    }

    function clearError(input, errorId) {
        $(input).removeClass('is-invalid').addClass('is-valid');
        $('#' + errorId).removeClass('show');
    }
    
    function clearStatus(input, errorId) {
        $(input).removeClass('is-invalid is-valid');
        $('#' + errorId).removeClass('show');
    }

    // ======= Validation générique =======
    function validateField(input) {
        const id = $(input).attr('id');
        const val = $(input).val().trim();
        
        // Skip empty optional fields
        if(val === '' && id === 'VAT_number') {
            clearStatus(input, 'vat_error');
            return true;
        }

        switch(id) {
            case 'name':
                if (!regex.name.test(val)) { showError(input, 'name_error'); return false; }
                else { clearError(input, 'name_error'); return true; }
            case 'email':
                if (!regex.email.test(val)) { showError(input, 'email_error'); return false; }
                else { clearError(input, 'email_error'); return true; }
            case 'phone':
                if (!regex.phone.test(val)) { showError(input, 'phone_error'); return false; }
                else { clearError(input, 'phone_error'); return true; }
            case 'Street':
                if (!regex.street.test(val)) { showError(input, 'street_error'); return false; }
                else { clearError(input, 'street_error'); return true; }
            case 'communityNumber':
                if (!regex.number.test(val)) { showError(input, 'number_error'); return false; }
                else { clearError(input, 'number_error'); return true; }
            case 'communityCity':
                if (!regex.city.test(val)) { showError(input, 'city_error'); return false; }
                else { clearError(input, 'city_error'); return true; }
            case 'communityPostalCode':
                if (!regex.postal.test(val)) { showError(input, 'postal_error'); return false; }
                else { clearError(input, 'postal_error'); return true; }
            case 'VAT_number':
                if (val && !regex.vat.test(val)) { showError(input, 'vat_error'); return false; }
                else if(val) { clearError(input, 'vat_error'); return true; }
                break;
            case 'birthday':
                if(!val) { showError(input, 'birthday_error'); return false; }
                else { clearError(input, 'birthday_error'); return true; }
                break;
        }
        return true;
    }

    // ======= Validation en direct =======
    $('input').on('input change', function() {
        validateField(this);
    });

    // Form submission
    let isSubmitting = false;

    $('#single_student_admission_form').on('submit', function(e) {
        e.preventDefault();
        
        if (isSubmitting) return;

        // Validate all fields
        var isValid = true;
        $('input[required], select[required]').each(function() {
            if(!validateField(this)) {
                isValid = false;
            }
        });
        
        if (!isValid) {
            // Shake animation
            $('.ec-btn-primary').addClass('shake');
            setTimeout(() => {
                $('.ec-btn-primary').removeClass('shake');
            }, 500);
            return false;
        }
        
        isSubmitting = true;
        
        // Show loading state
        const btn = $('#submit-btn');
        const originalBtnHtml = btn.html();
        
        btn.prop('disabled', true)
           .html('<i class="mdi mdi-loading mdi-spin"></i> <span><?php echo get_phrase('processing'); ?>...</span>');

        const formData = new FormData(this);

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                isSubmitting = false;
                
                if (response.status) {
                     // Success state
                    btn.removeClass('ec-btn-primary')
                        .addClass('ec-btn-success')
                        .html('<i class="mdi mdi-check-circle"></i> <span><?php echo get_phrase('student_created'); ?>!</span>')
                        .css({
                            'background': 'linear-gradient(135deg, #10b981, #34d399)',
                            'box-shadow': '0 4px 12px rgba(16, 185, 129, 0.3)'
                        });
                    
                    success_notify(response.notification);
                    
                    // Reload after delay
                    setTimeout(() => location.reload(), 2000);
                } else {
                    btn.prop('disabled', false).html(originalBtnHtml);
                    error_notify(response.notification || "<?php echo get_phrase('an_error_occurred'); ?>");
                }
            },
            error: function(xhr) {
                isSubmitting = false;
                btn.prop('disabled', false).html(originalBtnHtml);
                error_notify("<?php echo get_phrase('an_error_occurred'); ?>");
                console.error(xhr.responseText);
            }
        });
    });
});
</script>