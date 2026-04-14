<link rel="stylesheet" href="<?php echo base_url(); ?>assets/backend/css/quilljs/quill.snow.css">
<script src="<?php echo base_url(); ?>assets/backend/js/quilljs/quill.min.js"></script>
<script src="<?php echo base_url(); ?>assets/backend/js/quilljs/image-resize.min.js"></script>

<style>
/* ============================================================================
   PREMIUM FORM DESIGN - EVENT CREATE
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

/* Quill Editor Customization */
.ql-toolbar.ql-snow {
    border: none;
    border-bottom: 1px solid #e2e8f0;
    background: #f8fafc;
    padding: 0.75rem;
    border-radius: 12px 12px 0 0;
}

.ql-container.ql-snow {
    border: none;
    font-family: 'Outfit', sans-serif;
    font-size: 1rem;
}

.ql-editor {
    min-height: 200px;
    padding: 1rem;
}

.exp-editor-wrapper {
    border: 2px solid var(--form-border);
    border-radius: 12px;
    overflow: hidden;
    background: var(--form-light);
    transition: all 0.2s;
}

.exp-editor-wrapper:hover {
    border-color: #cbd5e1;
}

.exp-editor-wrapper.focused {
    border-color: var(--form-primary);
    background: var(--form-white);
    box-shadow: 0 0 0 4px rgba(var(--form-primary-rgb), 0.1);
}

.exp-editor-wrapper.is-invalid {
    border-color: var(--form-danger);
    background: #fef2f2;
}

.exp-editor-wrapper.is-invalid.focused {
    box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.1);
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
    pointer-events: none;
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
.exp-form-input.is-invalid ~ .exp-validation-icon.error {
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
            <i class="mdi mdi-calendar-plus"></i>
        </div>
        <div class="exp-form-title">
            <h3><?php echo get_phrase('create_event'); ?></h3>
            <p><?php echo get_phrase('fill_in_the_event_details'); ?></p>
        </div>
    </div>
    
    <!-- Info Card -->
    <div class="exp-info-card">
        <div class="exp-info-card-icon">
            <i class="mdi mdi-information-outline"></i>
        </div>
        <div class="exp-info-card-content">
            <h4><?php echo get_phrase('about_events'); ?></h4>
            <p><?php echo get_phrase('schedule_important_events_for_your_school'); ?></p>
        </div>
    </div>
    
    <form method="POST" class="d-block ajaxForm" action="<?php echo route('event_calendar/create'); ?>">
        <!-- CSRF Token -->
        <input type="hidden" name="<?=csrf_token();?>" value="<?=csrf_hash();?>" />
        <input type="hidden" name="school_id" id="school_id" value="<?php echo session()->get('school_id'); ?>">
        
        <!-- Event Title -->
        <div class="exp-form-group">
            <label class="exp-form-label">
                <i class="mdi mdi-format-title"></i>
                <span><?php echo get_phrase('event_title'); ?></span>
                <span class="exp-required">*</span>
            </label>
            <div class="exp-input-wrapper">
                <input type="text" 
                       class="exp-form-input" 
                       id="title" 
                       name="title" 
                       placeholder="<?php echo get_phrase('enter_event_title'); ?>"
                       autocomplete="off"
                       required>
                <i class="mdi mdi-text exp-input-icon"></i>
                <i class="mdi mdi-check-circle exp-validation-icon success"></i>
                <i class="mdi mdi-alert-circle exp-validation-icon error"></i>
            </div>
            <div class="exp-error-message" id="title_error">
                <i class="mdi mdi-alert-circle"></i>
                <span><?php echo get_phrase('please_provide_a_valid_title_(min_3_chars)'); ?></span>
            </div>
        </div>
        
        <!-- Rich Content (Moved after Title) -->
        <div class="exp-form-group">
            <label class="exp-form-label">
                <i class="mdi mdi-file-document-edit"></i>
                <span><?php echo get_phrase('content'); ?></span>
            </label>
            <div class="exp-editor-wrapper">
                <div id="quill-editor"></div>
                <input type="hidden" name="content" id="content">
            </div>
        </div>
        
        <!-- Starting Date -->
        <div class="exp-form-group">
            <label class="exp-form-label">
                <i class="mdi mdi-calendar-start"></i>
                <span><?php echo get_phrase('starting_date'); ?></span>
                <span class="exp-required">*</span>
            </label>
            <div class="exp-input-wrapper">
                <input type="date" 
                       class="exp-form-input" 
                       id="starting_date" 
                       name="starting_date" 
                       value="<?php echo date('Y-m-d'); ?>"
                       required>
                <i class="mdi mdi-calendar-clock exp-input-icon"></i>
                <i class="mdi mdi-check-circle exp-validation-icon success"></i>
                <i class="mdi mdi-alert-circle exp-validation-icon error"></i>
            </div>
            <div class="exp-error-message" id="starting_date_error">
                <i class="mdi mdi-alert-circle"></i>
                <span><?php echo get_phrase('please_provide_starting_date'); ?></span>
            </div>
        </div>
        
        <!-- Ending Date -->
        <div class="exp-form-group">
            <label class="exp-form-label">
                <i class="mdi mdi-calendar-end"></i>
                <span><?php echo get_phrase('ending_date'); ?></span>
                <span class="exp-required">*</span>
            </label>
            <div class="exp-input-wrapper">
                <input type="date" 
                       class="exp-form-input" 
                       id="ending_date" 
                       name="ending_date" 
                       value="<?php echo date('Y-m-d'); ?>"
                       required>
                <i class="mdi mdi-calendar-check exp-input-icon"></i>
                <i class="mdi mdi-check-circle exp-validation-icon success"></i>
                <i class="mdi mdi-alert-circle exp-validation-icon error"></i>
            </div>
            <div class="exp-error-message" id="ending_date_error">
                <i class="mdi mdi-alert-circle"></i>
                <span><?php echo get_phrase('ending_date_cannot_be_before_starting_date'); ?></span>
            </div>
        </div>
        
        <!-- Form Actions -->
        <div class="exp-form-actions">
            <button type="submit" class="exp-btn exp-btn-primary" id="submit-btn">
                <i class="mdi mdi-content-save"></i>
                <span><?php echo get_phrase('save_event'); ?></span>
            </button>
        </div>
    </form>
</div>

<script>
$(document).ready(function() {
    
    // Elements
    const titleInput = $('#title');
    const startDateInput = $('#starting_date');
    const endDateInput = $('#ending_date');
    const titleError = $('#title_error');
    const startDateError = $('#starting_date_error');
    const endDateError = $('#ending_date_error');
    const submitBtn = $('#submit-btn');
    
    // Validation pattern
    const titleRegex = /^.{3,}$/;

    // Initialize Quill editor
    var quill = new Quill('#quill-editor', {
        theme: 'snow',
        placeholder: "<?php echo get_phrase('write_event_content_here'); ?>",
        modules: {
            toolbar: [
                ['bold', 'italic', 'underline', 'strike'],
                ['blockquote', 'code-block'],
                [{ 'header': 1 }, { 'header': 2 }],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                [{ 'script': 'sub'}, { 'script': 'super' }],
                [{ 'indent': '-1'}, { 'indent': '+1' }],
                [{ 'direction': 'rtl' }],
                [{ 'size': ['small', false, 'large', 'huge'] }],
                [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                [{ 'color': [] }, { 'background': [] }],
                [{ 'font': [] }],
                [{ 'align': [] }],
                ['clean'],
                ['link', 'image', 'video']
            ],
            imageResize: window.ImageResize ? {} : undefined
        }
    });

    // Handle editor focus styles
    quill.on('selection-change', function(range, oldRange, source) {
        if (range) {
            $('.exp-editor-wrapper').addClass('focused');
        } else {
            $('.exp-editor-wrapper').removeClass('focused');
        }
    });

    // Sync content to hidden input
    quill.on('text-change', function() {
        $('#content').val(quill.root.innerHTML);
    });

    // Auto-open calendar on click
    $('input[type="date"]').on('click', function() {
        if (this.showPicker) {
            this.showPicker();
        }
    });
    
    // CSRF Token
    function getCsrfToken() {
        return {
            csrfName: $('input[name="<?= csrf_token(); ?>"]').attr('name'),
            csrfHash: $('input[name="<?= csrf_token(); ?>"]').val()
        };
    }
    
    // Validation functions
    function validateTitle() {
        const val = titleInput.val().trim();
        if (val.length < 3) {
            titleInput.removeClass('is-valid').addClass('is-invalid');
            titleError.addClass('show');
            return false;
        }
        titleInput.removeClass('is-invalid').addClass('is-valid');
        titleError.removeClass('show');
        return true;
    }
    
    function validateDates() {
        const start = startDateInput.val();
        const end = endDateInput.val();
        
        if (!start) {
            startDateInput.removeClass('is-valid').addClass('is-invalid');
            startDateError.addClass('show');
            return false;
        } else {
            startDateInput.removeClass('is-invalid').addClass('is-valid');
            startDateError.removeClass('show');
        }
        
        if (!end) {
            endDateInput.removeClass('is-valid').addClass('is-invalid');
            endDateError.text("<?php echo get_phrase('please_provide_ending_date'); ?>");
            endDateError.addClass('show');
            return false;
        }
        
        if (new Date(end) < new Date(start)) {
            endDateInput.removeClass('is-valid').addClass('is-invalid');
            endDateError.text("<?php echo get_phrase('ending_date_cannot_be_before_starting_date'); ?>");
            endDateError.addClass('show');
            return false;
        }
        
        endDateInput.removeClass('is-invalid').addClass('is-valid');
        endDateError.removeClass('show');
        return true;
    }
    
    // Real-time validation
    titleInput.on('input', validateTitle);
    startDateInput.on('change', validateDates);
    endDateInput.on('change', validateDates);
    
    // Form submission
    let isSubmitting = false;
    
    $(".ajaxForm").on("submit", function(e) {
        e.preventDefault();
        
        if (isSubmitting) return;
        
        const isTitleValid = validateTitle();
        const areDatesValid = validateDates();

        if (!isTitleValid || !areDatesValid) {
            // Shake animation on invalid fields
            if (!isTitleValid) {
                titleInput.addClass('shake');
                setTimeout(() => titleInput.removeClass('shake'), 500);
            }
            if (!areDatesValid) {
                if (startDateInput.hasClass('is-invalid')) {
                    startDateInput.addClass('shake');
                    setTimeout(() => startDateInput.removeClass('shake'), 500);
                }
                if (endDateInput.hasClass('is-invalid')) {
                    endDateInput.addClass('shake');
                    setTimeout(() => endDateInput.removeClass('shake'), 500);
                }
            }
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
                    submitBtn.removeClass('exp-btn-primary')
                        .addClass('exp-btn-success')
                        .html('<i class="mdi mdi-check-circle"></i> <span><?php echo get_phrase('saved'); ?>!</span>')
                        .css({
                            'background': 'linear-gradient(135deg, #10b981, #34d399)',
                            'box-shadow': '0 4px 12px rgba(16, 185, 129, 0.3)'
                        });
                    
                    if (typeof success_notify === 'function') {
                        success_notify(response.notification);
                    }
                    
                    // Update CSRF if available
                    if (response.csrf) {
                        $('input[name="' + response.csrf.name + '"]').val(response.csrf.hash);
                    }
                    
                    // Close modal and refresh without reload
                    setTimeout(() => {
                        $('#right-modal').modal('hide');
                        if (typeof showAllEvents === 'function') {
                            showAllEvents();
                        } else {
                            location.reload();
                        }
                    }, 1000);
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
            .html('<i class="mdi mdi-content-save"></i> <span><?php echo get_phrase('save_event'); ?></span>')
            .css({
                'background': '',
                'box-shadow': ''
            });
    }
});
</script>