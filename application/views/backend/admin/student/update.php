<?php 
    $school_id = school_id(); 
    $student = $this->db->get_where('students', array('id' => $student_id))->row_array();
    $enroll = $this->db->get_where('enrols', array('student_id' => $student_id))->row_array();
    $user_details = $this->user_model->get_user_details($student['user_id']);
?>

<link rel="stylesheet" href="<?php echo base_url(); ?>assets/backend/css/responsive.css">

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

.ec-form-hint {
    font-size: 0.75rem;
    color: var(--form-gray);
    font-weight: 400;
    margin-left: auto;
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

.ec-form-input.is-invalid {
    border-color: var(--form-danger);
    background: #fef2f2;
}

.ec-form-input.is-invalid:focus {
    box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.1);
}

.ec-form-input.is-valid {
    border-color: var(--form-success);
    background: #f0fdf4;
}

/* Validation Icons */
.ec-validation-icon {
    position: absolute;
    right: 1rem;
    top: 50%;
    transform: translateY(-50%);
    font-size: 1.25rem;
    opacity: 0;
    transition: all 0.2s;
}

.ec-validation-icon.success {
    color: var(--form-success);
}

.ec-validation-icon.error {
    color: var(--form-danger);
}

.ec-form-input.is-valid ~ .ec-validation-icon.success,
.ec-form-input.is-invalid ~ .ec-validation-icon.error {
    opacity: 1;
}

/* Error Messages */
.ec-error-message {
    display: flex;
    align-items: center;
    gap: 0.375rem;
    margin-top: 0.5rem;
    padding: 0.5rem 0.75rem;
    background: #fef2f2;
    border-radius: 8px;
    font-size: 0.8125rem;
    color: var(--form-danger);
    opacity: 0;
    transform: translateY(-10px);
    transition: all 0.2s;
}

.ec-error-message.show {
    opacity: 1;
    transform: translateY(0);
}

.ec-error-message i {
    font-size: 1rem;
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
    text-decoration: none;
}

.ec-btn-primary {
    background: linear-gradient(135deg, var(--form-primary), #8b5cf6);
    color: white;
    box-shadow: 0 4px 12px rgba(var(--form-primary-rgb), 0.3);
}

.ec-btn-primary:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(var(--form-primary-rgb), 0.4);
    color: white;
}

.ec-btn-primary:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none;
}

.ec-btn-secondary {
    background: var(--form-light);
    color: var(--form-gray);
    border: 2px solid var(--form-border);
}

.ec-btn-secondary:hover {
    background: var(--form-border);
    color: var(--form-dark);
}

.ec-btn-success {
    background: linear-gradient(135deg, #10b981, #34d399);
    color: white;
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
}

/* Loading Animation */
.ec-btn .mdi-loading {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

/* Shake Animation */
@keyframes shake {
    0%, 100% { transform: translateX(0); }
    10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
    20%, 40%, 60%, 80% { transform: translateX(5px); }
}

.shake {
    animation: shake 0.5s;
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

/* Info Card */
.ec-info-card {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.25rem;
    background: var(--form-light);
    border-radius: 12px;
    border: 1px solid var(--form-border);
    margin-bottom: 1.5rem;
}

.ec-info-card-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    background: rgba(var(--form-primary-rgb), 0.1);
    color: var(--form-primary);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}

.ec-info-card-content h4 {
    margin: 0;
    font-size: 1rem;
    font-weight: 700;
    color: var(--form-dark);
}

.ec-info-card-content p {
    margin: 0.25rem 0 0;
    font-size: 0.875rem;
    color: var(--form-gray);
}
</style>

<div class="ec-form-container">
    <!-- Form Header -->
    <div class="ec-form-header">
        <div class="ec-form-icon">
            <i class="mdi mdi-account-edit"></i>
        </div>
        <div class="ec-form-title">
            <h3><?php echo get_phrase('update_student_information'); ?></h3>
            <p><?php echo get_phrase('modify_student_details_and_enrollment'); ?></p>
        </div>
    </div>

    <form method="POST" class="d-block" action="<?php echo route('student/updated/' . $student_id . '/' . $student['user_id']); ?>" id="student_update_form" enctype="multipart/form-data" novalidate>
        <!-- CSRF Token -->
        <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>" />

        <!-- Image Upload Section -->
        <div class="image-upload-section">
            <div class="image-preview" id="student-image-preview">
                <img src="<?php echo $this->user_model->get_user_image($student['user_id']) . '?v=' . time(); ?>" alt="Student Profile Image" class="preview-image">
            </div>
            <div class="logo-upload-btn">
                <label for="student_image">
                    <i class="mdi mdi-camera"></i> <?php echo get_phrase('change_profile_photo'); ?>
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
                        <input type="text" id="name" name="name" class="ec-form-input" value="<?php echo htmlspecialchars($user_details['name']); ?>" placeholder="<?php echo get_phrase("enter_full_name") ?>" required>
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
                        <input type="email" id="email" name="email" class="ec-form-input" value="<?php echo htmlspecialchars($user_details['email']); ?>" placeholder="<?php echo get_phrase("enter_email_address") ?>" required>
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
                            <?php $classes = $this->db->get_where('classes', array('school_id' => $school_id))->result_array(); ?>
                            <?php foreach ($classes as $class) { ?>
                                <option value="<?php echo $class['id']; ?>" <?php if ($enroll['class_id'] == $class['id']) echo 'selected'; ?>><?php echo $class['name']; ?></option>
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
                            <option value="Male" <?php if ($user_details['gender'] == 'Male') echo 'selected'; ?>><?php echo get_phrase('male'); ?></option>
                            <option value="Female" <?php if ($user_details['gender'] == 'Female') echo 'selected'; ?>><?php echo get_phrase('female'); ?></option>
                            <option value="Others" <?php if ($user_details['gender'] == 'Others') echo 'selected'; ?>><?php echo get_phrase('others'); ?></option>
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
                        <input type="date" max="<?php echo $eighteen_years_ago; ?>" id="birthday" name="birthday" value="<?php echo htmlspecialchars($user_details['birthday'] ?? ''); ?>" class="ec-form-input" required>
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
                        <input type="text" id="phone" name="phone" class="ec-form-input" value="<?php echo htmlspecialchars($user_details['phone']); ?>" placeholder="<?php echo get_phrase("enter_phone_number") ?>" required>
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
                        <input type="text" id="Street" name="Street" class="ec-form-input" value="<?php echo htmlspecialchars($user_details['Rue'] ?? ''); ?>" placeholder="<?php echo get_phrase('enter_street_name'); ?>" required>
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
                        <input type="text" id="communityNumber" name="number" class="ec-form-input" value="<?php echo htmlspecialchars($user_details['Numero'] ?? ''); ?>" placeholder="No." required>
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
                        <input type="text" id="communityCity" name="city" class="ec-form-input" value="<?php echo htmlspecialchars($user_details['Ville'] ?? ''); ?>" placeholder="<?php echo get_phrase('enter_city'); ?>" required>
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
                        <input type="text" id="communityPostalCode" name="postal_code" class="ec-form-input" value="<?php echo htmlspecialchars($user_details['Codepostal'] ?? ''); ?>" placeholder="<?php echo get_phrase('postal_code'); ?>" required>
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
                <input type="text" id="VAT_number" name="VAT_number" class="ec-form-input" value="<?php echo htmlspecialchars($user_details['num_vat'] ?? ''); ?>" placeholder="<?php echo get_phrase('enter_vat_number'); ?>">
                <i class="mdi mdi-finance ec-input-icon"></i>
            </div>
             <div class="ec-error-message" id="vat_error">
                 <i class="mdi mdi-alert-circle"></i>
                <span><?php echo get_phrase('invalid_vat_number'); ?></span>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="ec-form-actions justify-content-end">
             <a href="<?php echo site_url("admin/student"); ?>" class="ec-btn ec-btn-secondary">
                <i class="mdi mdi-arrow-left"></i>
                <span><?php echo get_phrase('back'); ?></span>
            </a>
            <button type="submit" class="ec-btn ec-btn-primary" id="submit-btn">
                <i class="mdi mdi-content-save-edit"></i>
                <span><?php echo get_phrase('update_student'); ?></span>
            </button>
        </div>
    </form>
</div>

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

    $('#student_update_form').on('submit', function(e) {
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
           .html('<i class="mdi mdi-loading mdi-spin"></i> <span><?php echo get_phrase('updating'); ?>...</span>');

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
                
                // Update CSRF token
                if (response.csrf) {
                    $('input[name="' + response.csrf.name + '"]').val(response.csrf.hash);
                }

                if (response.status) {
                     // Success state
                    btn.removeClass('ec-btn-primary')
                        .addClass('ec-btn-success')
                        .html('<i class="mdi mdi-check-circle"></i> <span><?php echo get_phrase('updated'); ?>!</span>')
                        .css({
                            'background': 'linear-gradient(135deg, #10b981, #34d399)',
                            'box-shadow': '0 4px 12px rgba(16, 185, 129, 0.3)'
                        });
                    
                    success_notify(response.notification);
                    
                    // Redirect to student list after delay
                    setTimeout(() => window.location.href = '<?php echo site_url("admin/student"); ?>', 2000);
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