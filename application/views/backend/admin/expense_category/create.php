<style>
/* ============================================================================
   PREMIUM FORM DESIGN - EXPENSE CATEGORY CREATE
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

/* Character Counter */
.ec-char-counter {
    position: absolute;
    right: 1rem;
    bottom: -1.5rem;
    font-size: 0.75rem;
    color: var(--form-gray);
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

.ec-btn-secondary {
    background: var(--form-light);
    color: var(--form-gray);
    border: 2px solid var(--form-border);
}

.ec-btn-secondary:hover {
    background: var(--form-border);
    color: var(--form-dark);
}

/* Loading Animation */
.ec-btn .mdi-loading {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

/* Info Card */
.ec-info-card {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    padding: 1rem;
    background: linear-gradient(135deg, rgba(var(--form-primary-rgb), 0.05), rgba(var(--form-primary-rgb), 0.1));
    border-radius: 12px;
    margin-bottom: 1.5rem;
    border: 1px solid rgba(var(--form-primary-rgb), 0.2);
}

.ec-info-card-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    background: var(--form-primary);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.125rem;
    flex-shrink: 0;
}

.ec-info-card-content {
    flex: 1;
}

.ec-info-card-content h4 {
    margin: 0 0 0.25rem;
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--form-dark);
}

.ec-info-card-content p {
    margin: 0;
    font-size: 0.8125rem;
    color: var(--form-gray);
    line-height: 1.5;
}

/* Responsive */
@media (max-width: 480px) {
    .ec-form-header {
        flex-direction: column;
        text-align: center;
    }
    
    .ec-form-actions {
        flex-direction: column;
    }
    
    .ec-btn {
        width: 100%;
    }
}
</style>

<div class="ec-form-container">
    <!-- Form Header -->
    <div class="ec-form-header">
        <div class="ec-form-icon">
            <i class="mdi mdi-tag-plus"></i>
        </div>
        <div class="ec-form-title">
            <h3><?php echo get_phrase('create_expense_category'); ?></h3>
            <p><?php echo get_phrase('fill_in_the_details_below'); ?></p>
        </div>
    </div>
    
    <!-- Info Card -->
    <div class="ec-info-card">
        <div class="ec-info-card-icon">
            <i class="mdi mdi-information-outline"></i>
        </div>
        <div class="ec-info-card-content">
            <h4><?php echo get_phrase('about_expense_categories'); ?></h4>
            <p><?php echo get_phrase('expense_categories_help_organize_and_track_your_spending'); ?></p>
        </div>
    </div>
    
    <form method="POST" class="d-block ajaxForm" action="<?php echo route('expense_category/create'); ?>">
        <!-- CSRF Token -->
        <input type="hidden" name="<?=$this->security->get_csrf_token_name();?>" value="<?=$this->security->get_csrf_hash();?>" />
        
        <!-- Category Name -->
        <div class="ec-form-group">
            <label class="ec-form-label">
                <i class="mdi mdi-tag-outline"></i>
                <span><?php echo get_phrase('category_name'); ?></span>
                <span class="ec-required">*</span>
                <span class="ec-form-hint"><?php echo get_phrase('min_3_characters'); ?></span>
            </label>
            <div class="ec-input-wrapper">
                <input type="text" 
                       class="ec-form-input" 
                       id="name" 
                       name="name" 
                       placeholder="<?php echo get_phrase('enter_category_name'); ?>"
                       autocomplete="off"
                       required>
                <i class="mdi mdi-format-letter-case ec-input-icon"></i>
                <i class="mdi mdi-check-circle ec-validation-icon success"></i>
                <i class="mdi mdi-alert-circle ec-validation-icon error"></i>
            </div>
            <div class="ec-error-message" id="name_error">
                <i class="mdi mdi-alert-circle"></i>
                <span><?php echo get_phrase('invalid_name_minimum_3_characters'); ?></span>
            </div>
        </div>
        
        <!-- Cost Center -->
        <div class="ec-form-group">
            <label class="ec-form-label">
                <i class="mdi mdi-calculator"></i>
                <span><?php echo get_phrase('cost_center'); ?></span>
                <span class="ec-form-hint"><?php echo get_phrase('optional'); ?></span>
            </label>
            <div class="ec-input-wrapper">
                <input type="text"
                       class="ec-form-input"
                       id="cost_center"
                       name="cost_center"
                       placeholder="<?php echo get_phrase('enter_cost_center_code_optional'); ?>"
                       autocomplete="off">
                <i class="mdi mdi-format-letter-case ec-input-icon"></i>
                <i class="mdi mdi-check-circle ec-validation-icon success"></i>
                <i class="mdi mdi-alert-circle ec-validation-icon error"></i>
            </div>
            <div class="ec-error-message" id="cost_center_error">
                <i class="mdi mdi-alert-circle"></i>
                <span><?php echo get_phrase('invalid_cost_center'); ?></span>
            </div>
        </div>
        
        <!-- Department -->
        <div class="ec-form-group">
            <label class="ec-form-label">
                <i class="mdi mdi-building"></i>
                <span><?php echo get_phrase('department'); ?></span>
                <span class="ec-form-hint"><?php echo get_phrase('optional'); ?></span>
            </label>
            <div class="ec-input-wrapper">
                <input type="text"
                       class="ec-form-input"
                       id="department"
                       name="department"
                       placeholder="<?php echo get_phrase('enter_department_name_optional'); ?>"
                       autocomplete="off">
                <i class="mdi mdi-office-building ec-input-icon"></i>
                <i class="mdi mdi-check-circle ec-validation-icon success"></i>
                <i class="mdi mdi-alert-circle ec-validation-icon error"></i>
            </div>
            <div class="ec-error-message" id="department_error">
                <i class="mdi mdi-alert-circle"></i>
                <span><?php echo get_phrase('invalid_department'); ?></span>
            </div>
        </div>
        
        <!-- Form Actions -->
        <div class="ec-form-actions">
            <button type="submit" class="ec-btn ec-btn-primary" id="submit-btn">
                <i class="mdi mdi-content-save"></i>
                <span><?php echo get_phrase('save_category'); ?></span>
            </button>
        </div>
    </form>
</div>

<script>
$(document).ready(function() {
    
    // Elements
    const nameInput = $('#name');
    const costCenterInput = $('#cost_center');
    const departmentInput = $('#department');
    const nameError = $('#name_error');
    const costCenterError = $('#cost_center_error');
    const departmentError = $('#department_error');
    const submitBtn = $('#submit-btn');
    
    // Validation patterns
    const nameRegex = /^[a-zA-Z0-9àâäéèêëïîôùûüç\s\-_]{3,}$/;
    const costCenterRegex = /^[a-zA-Z0-9\s\-_]+$/;
    const departmentRegex = /^[a-zA-Z0-9àâäéèêëïîôùûüç\s\-_]+$/;
    
    // CSRF Token
    function getCsrfToken() {
        return {
            csrfName: $('input[name="<?= $this->security->get_csrf_token_name(); ?>"]').attr('name'),
            csrfHash: $('input[name="<?= $this->security->get_csrf_token_name(); ?>"]').val()
        };
    }
    
    // Validation functions
    function validateName() {
        const val = nameInput.val().trim();
        if (val.length === 0) {
            nameInput.removeClass('is-valid is-invalid');
            nameError.removeClass('show');
            return false;
        }
        if (!nameRegex.test(val)) {
            nameInput.addClass('is-invalid').removeClass('is-valid');
            nameError.addClass('show');
            return false;
        }
        nameInput.addClass('is-valid').removeClass('is-invalid');
        nameError.removeClass('show');
        return true;
    }

    function validateCostCenter() {
        const val = costCenterInput.val().trim();
        // Cost center is optional
        if (val.length === 0) {
            costCenterInput.removeClass('is-valid is-invalid');
            costCenterError.removeClass('show');
            return true; // Valid when empty (optional)
        }
        if (!costCenterRegex.test(val)) {
            costCenterInput.addClass('is-invalid').removeClass('is-valid');
            costCenterError.addClass('show');
            return false;
        }
        costCenterInput.addClass('is-valid').removeClass('is-invalid');
        costCenterError.removeClass('show');
        return true;
    }

    function validateDepartment() {
        const val = departmentInput.val().trim();
        // Department is optional
        if (val.length === 0) {
            departmentInput.removeClass('is-valid is-invalid');
            departmentError.removeClass('show');
            return true; // Valid when empty (optional)
        }
        if (!departmentRegex.test(val)) {
            departmentInput.addClass('is-invalid').removeClass('is-valid');
            departmentError.addClass('show');
            return false;
        }
        departmentInput.addClass('is-valid').removeClass('is-invalid');
        departmentError.removeClass('show');
        return true;
    }
    
    // Real-time validation
    nameInput.on('input', validateName);
    costCenterInput.on('input', validateCostCenter);
    departmentInput.on('input', validateDepartment);
    
    // Form submission
    let isSubmitting = false;
    
    $(".ajaxForm").on("submit", function(e) {
        e.preventDefault();
        
        if (isSubmitting) return;
        
        const isNameValid = validateName();
        const isCostCenterValid = validateCostCenter();
        const isDepartmentValid = validateDepartment();

        if (!isNameValid) {
            // Shake animation on errors
            nameInput.addClass('shake');
            setTimeout(() => {
                nameInput.removeClass('shake');
            }, 500);
            return;
        }
        
        isSubmitting = true;
        
        // Update button state
        submitBtn.prop('disabled', true)
            .html('<i class="mdi mdi-loading mdi-spin"></i> <span><?php echo get_phrase('saving'); ?>...</span>');
        
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
                    submitBtn.removeClass('ec-btn-primary')
                        .addClass('ec-btn-success')
                        .html('<i class="mdi mdi-check-circle"></i> <span><?php echo get_phrase('saved'); ?>!</span>')
                        .css({
                            'background': 'linear-gradient(135deg, #10b981, #34d399)',
                            'box-shadow': '0 4px 12px rgba(16, 185, 129, 0.3)'
                        });
                    
                    success_notify(response.notification);
                    
                    // Update CSRF
                    $('input[name="' + response.csrf.name + '"]').val(response.csrf.hash);
                    
                    // Reload after delay
                    setTimeout(() => location.reload(), 2000);
                } else {
                    resetButton();
                    error_notify("<?php echo get_phrase('action_not_allowed'); ?>");
                }
            },
            error: function() {
                isSubmitting = false;
                resetButton();
                error_notify("<?php echo get_phrase('an_error_occurred'); ?>");
            }
        });
    });
    
    function resetButton() {
        submitBtn.prop('disabled', false)
            .removeClass('ec-btn-success')
            .addClass('ec-btn-primary')
            .html('<i class="mdi mdi-content-save"></i> <span><?php echo get_phrase('save_category'); ?></span>')
            .css({
                'background': '',
                'box-shadow': ''
            });
    }
});
</script>
