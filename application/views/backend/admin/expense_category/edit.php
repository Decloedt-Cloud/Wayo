<?php $expense_category_details = $this->db->get_where('expense_categories', array('id' => $param1))->row_array(); ?>

<style>
/* ============================================================================
   PREMIUM FORM DESIGN - EXPENSE CATEGORY EDIT
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

/* Category Preview Card */
.ec-category-preview {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.25rem;
    background: linear-gradient(135deg, var(--form-light), #f1f5f9);
    border-radius: 16px;
    margin-bottom: 2rem;
    border: 1px solid var(--form-border);
}

.ec-preview-avatar {
    width: 52px;
    height: 52px;
    border-radius: 14px;
    background: linear-gradient(135deg, var(--form-primary), #8b5cf6);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 700;
    font-size: 1.125rem;
    flex-shrink: 0;
    box-shadow: 0 4px 12px rgba(var(--form-primary-rgb), 0.3);
}

.ec-preview-info {
    flex: 1;
}

.ec-preview-name {
    font-size: 1rem;
    font-weight: 600;
    color: var(--form-dark);
    margin: 0 0 0.25rem;
}

.ec-preview-id {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    font-size: 0.75rem;
    color: var(--form-gray);
    background: var(--form-white);
    padding: 0.25rem 0.5rem;
    border-radius: 6px;
}

.ec-preview-status {
    padding: 0.5rem 1rem;
    border-radius: 10px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.ec-preview-status.configured {
    background: linear-gradient(135deg, rgba(16, 185, 129, 0.1), rgba(16, 185, 129, 0.2));
    color: var(--form-success);
}

.ec-preview-status.pending {
    background: linear-gradient(135deg, rgba(245, 158, 11, 0.1), rgba(245, 158, 11, 0.2));
    color: var(--form-warning);
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

/* Loading Animation */
.ec-btn .mdi-loading {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

/* Change Indicator */
.ec-change-indicator {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1rem;
    background: linear-gradient(135deg, rgba(var(--form-primary-rgb), 0.05), rgba(var(--form-primary-rgb), 0.1));
    border-radius: 10px;
    margin-bottom: 1.5rem;
    border: 1px dashed rgba(var(--form-primary-rgb), 0.3);
    opacity: 0;
    transform: translateY(-10px);
    transition: all 0.3s;
}

.ec-change-indicator.show {
    opacity: 1;
    transform: translateY(0);
}

.ec-change-indicator i {
    color: var(--form-primary);
    font-size: 1.25rem;
}

.ec-change-indicator span {
    font-size: 0.875rem;
    color: var(--form-dark);
}

/* Responsive */
@media (max-width: 480px) {
    .ec-form-header {
        flex-direction: column;
        text-align: center;
    }
    
    .ec-category-preview {
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
            <i class="mdi mdi-tag-edit"></i>
        </div>
        <div class="ec-form-title">
            <h3><?php echo get_phrase('edit_expense_category'); ?></h3>
            <p><?php echo get_phrase('modify_category_details'); ?></p>
        </div>
    </div>
    
    <!-- Category Preview -->
    <div class="ec-category-preview">
        <div class="ec-preview-avatar">
            <?php echo strtoupper(substr($expense_category_details['name'], 0, 2)); ?>
        </div>
        <div class="ec-preview-info">
            <h4 class="ec-preview-name"><?php echo $expense_category_details['name']; ?></h4>
            <span class="ec-preview-id">
                <i class="mdi mdi-identifier"></i>
                CAT-<?php echo str_pad($expense_category_details['id'], 4, '0', STR_PAD_LEFT); ?>
            </span>
        </div>
        <?php if (!empty($expense_category_details['cost_center'])): ?>
        <span class="ec-preview-status configured">
            <i class="mdi mdi-check-circle"></i>
            <?php echo get_phrase('configured'); ?>
        </span>
        <?php else: ?>
        <span class="ec-preview-status pending">
            <i class="mdi mdi-clock-outline"></i>
            <?php echo get_phrase('pending'); ?>
        </span>
        <?php endif; ?>
    </div>
    
    <!-- Change Indicator -->
    <div class="ec-change-indicator" id="change-indicator">
        <i class="mdi mdi-pencil-circle"></i>
        <span><?php echo get_phrase('you_have_unsaved_changes'); ?></span>
    </div>
    
<form method="POST" class="d-block ajaxForm" action="<?php echo route('expense_category/update/'.$param1); ?>">
        <!-- CSRF Token -->
  <input type="hidden" name="<?=$this->security->get_csrf_token_name();?>" value="<?=$this->security->get_csrf_hash();?>" />
  
        <!-- Original values for change detection -->
        <input type="hidden" id="original_name" value="<?php echo htmlspecialchars($expense_category_details['name']); ?>">
        <input type="hidden" id="original_cost_center" value="<?php echo htmlspecialchars($expense_category_details['cost_center'] ?? ''); ?>">
        <input type="hidden" id="original_department" value="<?php echo htmlspecialchars($expense_category_details['department'] ?? ''); ?>">
        
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
                       value="<?php echo htmlspecialchars($expense_category_details['name']); ?>"
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
                       value="<?php echo htmlspecialchars($expense_category_details['cost_center'] ?? ''); ?>"
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
                       value="<?php echo htmlspecialchars($expense_category_details['department'] ?? ''); ?>"
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
                <i class="mdi mdi-content-save-edit"></i>
                <span><?php echo get_phrase('update_category'); ?></span>
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
    const changeIndicator = $('#change-indicator');
    
    // Original values
    const originalName = $('#original_name').val();
    const originalCostCenter = $('#original_cost_center').val();
    const originalDepartment = $('#original_department').val();
    
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
    
    // Check for changes
    function checkChanges() {
        const hasChanges = nameInput.val() !== originalName || 
                          costCenterInput.val() !== originalCostCenter ||
                          departmentInput.val() !== originalDepartment;
        
        if (hasChanges) {
            changeIndicator.addClass('show');
        } else {
            changeIndicator.removeClass('show');
        }
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
        // Cost center is now optional
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
    
    // Real-time validation and change detection
    nameInput.on('input', function() {
        validateName();
        checkChanges();
    });
    
    costCenterInput.on('input', function() {
        validateCostCenter();
        checkChanges();
    });
    
    departmentInput.on('input', function() {
        validateDepartment();
        checkChanges();
    });
    
    // Initial validation state (mark existing values as valid)
    if (nameInput.val()) {
        validateName();
    }
    if (costCenterInput.val()) {
        validateCostCenter();
    }
    if (departmentInput.val()) {
        validateDepartment();
    }
    
    // Form submission
    let isSubmitting = false;
    
    $(".ajaxForm").on("submit", function(e) {
    e.preventDefault();

        if (isSubmitting) return;
        
        const isNameValid = validateName();

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
                
                if (response.status) {
                    // Success state
                    submitBtn.html('<i class="mdi mdi-check-circle"></i> <span><?php echo get_phrase('updated'); ?>!</span>')
                        .css({
                            'background': 'linear-gradient(135deg, #10b981, #34d399)',
                            'box-shadow': '0 4px 12px rgba(16, 185, 129, 0.3)'
                        });
                    
                    changeIndicator.removeClass('show');
                    
                    success_notify(response.notification || '<?php echo get_phrase('category_updated_successfully'); ?>');
                    
                    // Update CSRF
                    if (response.csrf) {
                        $('input[name="' + response.csrf.name + '"]').val(response.csrf.hash);
                    }
                    
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
            .html('<i class="mdi mdi-content-save-edit"></i> <span><?php echo get_phrase('update_category'); ?></span>')
            .css({
                'background': '',
                'box-shadow': ''
            });
    }
  });
</script>
