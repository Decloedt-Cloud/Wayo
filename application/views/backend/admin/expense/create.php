<style>
/* ============================================================================
   PREMIUM FORM DESIGN - EXPENSE CREATE
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

.exp-form-container {
    padding: 0.5rem;
}

/* Form Header */
.exp-form-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 2rem;
    padding-bottom: 1.5rem;
    border-bottom: 2px solid var(--form-border);
}

.exp-form-icon {
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

.exp-form-title {
    flex: 1;
}

.exp-form-title h3 {
    margin: 0;
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--form-dark);
}

.exp-form-title p {
    margin: 0.25rem 0 0;
    font-size: 0.875rem;
    color: var(--form-gray);
}

/* Form Groups */
.exp-form-group {
    margin-bottom: 1.5rem;
    position: relative;
}

.exp-form-label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.625rem;
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--form-dark);
}

.exp-form-label i {
    color: var(--form-primary);
    font-size: 1rem;
}

.exp-form-label .exp-required {
    color: var(--form-danger);
    font-weight: 700;
}

.exp-form-hint {
    font-size: 0.75rem;
    color: var(--form-gray);
    font-weight: 400;
    margin-left: auto;
}

/* Input Wrapper */
.exp-input-wrapper {
    position: relative;
}

.exp-input-icon {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--form-gray);
    font-size: 1.25rem;
    z-index: 1;
    transition: all 0.2s;
}

.exp-form-input {
    width: 100%;
    padding: 0.875rem 1rem 0.875rem 3rem;
    border: 2px solid var(--form-border);
    border-radius: 12px;
    font-size: 0.9375rem;
    background: var(--form-light);
    color: var(--form-dark);
    transition: all 0.2s;
}

.exp-form-input:hover {
    border-color: #cbd5e1;
}

.exp-form-input:focus {
    outline: none;
    border-color: var(--form-primary);
    background: var(--form-white);
    box-shadow: 0 0 0 4px rgba(var(--form-primary-rgb), 0.1);
}

.exp-form-input:focus + .exp-input-icon,
.exp-form-input:not(:placeholder-shown) + .exp-input-icon {
    color: var(--form-primary);
}

.exp-form-input.is-invalid {
    border-color: var(--form-danger);
    background: #fef2f2;
}

.exp-form-input.is-invalid:focus {
    box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.1);
}

.exp-form-input.is-valid {
    border-color: var(--form-success);
    background: #f0fdf4;
}

/* Select Styling */
.exp-form-select {
    width: 100%;
    padding: 0.875rem 1rem 0.875rem 3rem;
    border: 2px solid var(--form-border);
    border-radius: 12px;
    font-size: 0.9375rem;
    background: var(--form-light);
    color: var(--form-dark);
    transition: all 0.2s;
    cursor: pointer;
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%2364748b' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 1rem center;
}

.exp-form-select:hover {
    border-color: #cbd5e1;
}

.exp-form-select:focus {
    outline: none;
    border-color: var(--form-primary);
    background-color: var(--form-white);
    box-shadow: 0 0 0 4px rgba(var(--form-primary-rgb), 0.1);
}

.exp-form-select.is-invalid {
    border-color: var(--form-danger);
    background-color: #fef2f2;
}

.exp-form-select.is-valid {
    border-color: var(--form-success);
    background-color: #f0fdf4;
}

/* Validation Icons */
.exp-validation-icon {
    position: absolute;
    right: 1rem;
    top: 50%;
    transform: translateY(-50%);
    font-size: 1.25rem;
    opacity: 0;
    transition: all 0.2s;
}

.exp-validation-icon.success {
    color: var(--form-success);
}

.exp-validation-icon.error {
    color: var(--form-danger);
}

.exp-form-input.is-valid ~ .exp-validation-icon.success,
.exp-form-input.is-invalid ~ .exp-validation-icon.error,
.exp-form-select.is-valid ~ .exp-validation-icon.success,
.exp-form-select.is-invalid ~ .exp-validation-icon.error {
    opacity: 1;
}

/* Error Messages */
.exp-error-message {
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

.exp-error-message.show {
    opacity: 1;
    transform: translateY(0);
}

.exp-error-message i {
    font-size: 1rem;
}

/* Form Actions */
.exp-form-actions {
    display: flex;
    gap: 1rem;
    margin-top: 2rem;
    padding-top: 1.5rem;
    border-top: 2px solid var(--form-border);
}

.exp-btn {
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

.exp-btn-primary {
    background: linear-gradient(135deg, var(--form-primary), #8b5cf6);
    color: white;
    box-shadow: 0 4px 12px rgba(var(--form-primary-rgb), 0.3);
}

.exp-btn-primary:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(var(--form-primary-rgb), 0.4);
}

.exp-btn-primary:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none;
}

.exp-btn-secondary {
    background: var(--form-light);
    color: var(--form-gray);
    border: 2px solid var(--form-border);
}

.exp-btn-secondary:hover {
    background: var(--form-border);
    color: var(--form-dark);
}

/* Loading Animation */
.exp-btn .mdi-loading {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

/* Info Card */
.exp-info-card {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    padding: 1rem;
    background: linear-gradient(135deg, rgba(var(--form-primary-rgb), 0.05), rgba(var(--form-primary-rgb), 0.1));
    border-radius: 12px;
    margin-bottom: 1.5rem;
    border: 1px solid rgba(var(--form-primary-rgb), 0.2);
}

.exp-info-card-icon {
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

.exp-info-card-content {
    flex: 1;
}

.exp-info-card-content h4 {
    margin: 0 0 0.25rem;
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--form-dark);
}

.exp-info-card-content p {
    margin: 0;
    font-size: 0.8125rem;
    color: var(--form-gray);
    line-height: 1.5;
}

/* Shake Animation */
@keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-5px); }
    75% { transform: translateX(5px); }
}

.shake {
    animation: shake 0.3s ease-in-out;
}

/* Responsive */
@media (max-width: 480px) {
    .exp-form-header {
        flex-direction: column;
        text-align: center;
    }
    
    .exp-form-actions {
        flex-direction: column;
    }
    
    .exp-btn {
        width: 100%;
    }
}
</style>

<div class="exp-form-container">
    <!-- Form Header -->
    <div class="exp-form-header">
        <div class="exp-form-icon">
            <i class="mdi mdi-cash-plus"></i>
        </div>
        <div class="exp-form-title">
            <h3><?php echo get_phrase('create_expense'); ?></h3>
            <p><?php echo get_phrase('fill_in_the_expense_details'); ?></p>
        </div>
    </div>
    
    <!-- Info Card -->
    <div class="exp-info-card">
        <div class="exp-info-card-icon">
            <i class="mdi mdi-information-outline"></i>
        </div>
        <div class="exp-info-card-content">
            <h4><?php echo get_phrase('about_expenses'); ?></h4>
            <p><?php echo get_phrase('track_and_manage_your_organizational_spending'); ?></p>
        </div>
    </div>
    
    <form method="POST" class="d-block ajaxForm" action="<?php echo route('expense/create'); ?>">
        <!-- CSRF Token -->
        <input type="hidden" name="<?=$this->security->get_csrf_token_name();?>" value="<?=$this->security->get_csrf_hash();?>" />
        
        <!-- Date -->
        <div class="exp-form-group">
            <label class="exp-form-label">
                <i class="mdi mdi-calendar"></i>
                <span><?php echo get_phrase('date'); ?></span>
                <span class="exp-required">*</span>
            </label>
            <div class="exp-input-wrapper">
                <input type="date" 
                       class="exp-form-input" 
                       id="date" 
                       name="date" 
                       placeholder="<?php echo get_phrase('select_date'); ?>"
                       required>
                <i class="mdi mdi-calendar-month exp-input-icon"></i>
                <i class="mdi mdi-check-circle exp-validation-icon success"></i>
                <i class="mdi mdi-alert-circle exp-validation-icon error"></i>
            </div>
            <div class="exp-error-message" id="date_error">
                <i class="mdi mdi-alert-circle"></i>
                <span><?php echo get_phrase('please_provide_date'); ?></span>
            </div>
        </div>
        
        <!-- Amount -->
        <div class="exp-form-group">
            <label class="exp-form-label">
                <i class="mdi mdi-currency-usd"></i>
                <span><?php echo get_phrase('amount').' ('. $this->db->get_where('settings_school', array('school_id' => school_id()))->row('system_currency').')'; ?></span>
                <span class="exp-required">*</span>
            </label>
            <div class="exp-input-wrapper">
                <input type="text" 
                       class="exp-form-input" 
                       id="amount" 
                       name="amount" 
                       placeholder="<?php echo get_phrase('enter_amount'); ?>"
                       autocomplete="off"
                       required>
                <i class="mdi mdi-cash exp-input-icon"></i>
                <i class="mdi mdi-check-circle exp-validation-icon success"></i>
                <i class="mdi mdi-alert-circle exp-validation-icon error"></i>
            </div>
            <div class="exp-error-message" id="amount_error">
                <i class="mdi mdi-alert-circle"></i>
                <span><?php echo get_phrase('invalid_amount'); ?></span>
            </div>
        </div>
        
        <!-- Expense Category -->
        <div class="exp-form-group">
            <label class="exp-form-label">
                <i class="mdi mdi-tag-outline"></i>
                <span><?php echo get_phrase('expense_category'); ?></span>
                <span class="exp-required">*</span>
            </label>
            <div class="exp-input-wrapper">
                <select class="exp-form-select" name="expense_category_id" id="expense_category_id_on_create" required>
                    <option value=""><?php echo get_phrase('select_an_expense_category'); ?></option>
                    <?php
                    $expense_categories = $this->crud_model->get_expense_categories()->result_array();
                    foreach ($expense_categories as $expense_category): ?>
                    <option value="<?php echo $expense_category['id']; ?>"><?php echo $expense_category['name']; ?></option>
                    <?php endforeach; ?>
                </select>
                <i class="mdi mdi-tag exp-input-icon"></i>
                <i class="mdi mdi-check-circle exp-validation-icon success"></i>
                <i class="mdi mdi-alert-circle exp-validation-icon error"></i>
            </div>
            <div class="exp-error-message" id="category_error">
                <i class="mdi mdi-alert-circle"></i>
                <span><?php echo get_phrase('please_select_expense_category'); ?></span>
            </div>
        </div>
        
        <!-- Form Actions -->
        <div class="exp-form-actions">
            <button type="submit" class="exp-btn exp-btn-primary" id="submit-btn">
                <i class="mdi mdi-content-save"></i>
                <span><?php echo get_phrase('create_expense'); ?></span>
            </button>
        </div>
    </form>
</div>

<script>
$(document).ready(function() {
    
    // Elements
    const dateInput = $('#date');
    const amountInput = $('#amount');
    const categoryInput = $('#expense_category_id_on_create');
    const dateError = $('#date_error');
    const amountError = $('#amount_error');
    const categoryError = $('#category_error');
    const submitBtn = $('#submit-btn');
    
    // Validation pattern
    const amountRegex = /^[0-9]+(\.[0-9]{1,2})?$/;
    
    // CSRF Token
    function getCsrfToken() {
        return {
            csrfName: $('input[name="<?= $this->security->get_csrf_token_name(); ?>"]').attr('name'),
            csrfHash: $('input[name="<?= $this->security->get_csrf_token_name(); ?>"]').val()
        };
    }
    
    // Validation functions
    function validateDate() {
        const val = dateInput.val().trim();
        if (val === '') {
            dateInput.removeClass('is-valid').addClass('is-invalid');
            dateError.addClass('show');
            return false;
        }
        dateInput.removeClass('is-invalid').addClass('is-valid');
        dateError.removeClass('show');
        return true;
    }
    
    function validateAmount() {
        const val = amountInput.val().trim();
        if (val.length === 0) {
            amountInput.removeClass('is-valid is-invalid');
            amountError.removeClass('show');
            return false;
        }
        if (!amountRegex.test(val)) {
            amountInput.addClass('is-invalid').removeClass('is-valid');
            amountError.addClass('show');
            return false;
        }
        amountInput.addClass('is-valid').removeClass('is-invalid');
        amountError.removeClass('show');
        return true;
    }
    
    function validateCategory() {
        const val = categoryInput.val();
        if (val === '' || val === null) {
            categoryInput.addClass('is-invalid').removeClass('is-valid');
            categoryError.addClass('show');
            return false;
        }
        categoryInput.addClass('is-valid').removeClass('is-invalid');
        categoryError.removeClass('show');
        return true;
    }
    
    // Real-time validation
    dateInput.on('change', validateDate);
    amountInput.on('input', validateAmount);
    categoryInput.on('change', validateCategory);
    
    // Form submission
    let isSubmitting = false;
    
    $(".ajaxForm").on("submit", function(e) {
        e.preventDefault();
        
        if (isSubmitting) return;
        
        const isDateValid = validateDate();
        const isAmountValid = validateAmount();
        const isCategoryValid = validateCategory();

        if (!isDateValid || !isAmountValid || !isCategoryValid) {
            // Shake animation on invalid fields
            if (!isDateValid) {
                dateInput.addClass('shake');
                setTimeout(() => dateInput.removeClass('shake'), 500);
            }
            if (!isAmountValid) {
                amountInput.addClass('shake');
                setTimeout(() => amountInput.removeClass('shake'), 500);
            }
            if (!isCategoryValid) {
                categoryInput.addClass('shake');
                setTimeout(() => categoryInput.removeClass('shake'), 500);
            }
            return;
        }
        
        isSubmitting = true;
        
        // Update button state
        submitBtn.prop('disabled', true)
            .html('<i class="mdi mdi-loading mdi-spin"></i> <span><?php echo get_phrase('creating'); ?>...</span>');
        
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
                    submitBtn.removeClass('exp-btn-primary')
                        .addClass('exp-btn-success')
                        .html('<i class="mdi mdi-check-circle"></i> <span><?php echo get_phrase('created'); ?>!</span>')
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
            .removeClass('exp-btn-success')
            .addClass('exp-btn-primary')
            .html('<i class="mdi mdi-content-save"></i> <span><?php echo get_phrase('create_expense'); ?></span>')
            .css({
                'background': '',
                'box-shadow': ''
            });
    }
});
</script>
