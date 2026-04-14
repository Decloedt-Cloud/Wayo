<style>
:root {
    --form-primary: #6366f1;
    --form-primary-rgb: 99, 102, 241;
    --form-success: #10b981;
    --form-danger: #ef4444;
    --form-dark: #1e293b;
    --form-gray: #64748b;
    --form-light: #f8fafc;
    --form-border: #e2e8f0;
    --form-white: #ffffff;
}

.grade-form-container { padding: 0.5rem; }
.grade-form-header { display: flex; align-items: center; gap: 1rem; margin-bottom: 1.5rem; padding-bottom: 1.25rem; border-bottom: 2px solid var(--form-border); }
.grade-form-icon { width: 50px; height: 50px; border-radius: 14px; background: linear-gradient(135deg, var(--form-primary), #8b5cf6); display: flex; align-items: center; justify-content: center; font-size: 1.25rem; color: white; box-shadow: 0 6px 16px rgba(var(--form-primary-rgb), 0.3); }
.grade-form-title { flex: 1; }
.grade-form-title h3 { margin: 0; font-size: 1.125rem; font-weight: 700; color: var(--form-dark); }
.grade-form-title p { margin: 0.25rem 0 0; font-size: 0.8rem; color: var(--form-gray); }

.grade-form-group { margin-bottom: 1.25rem; }
.grade-form-label { display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem; font-size: 0.875rem; font-weight: 600; color: var(--form-dark); }
.grade-form-label i { color: var(--form-primary); font-size: 1rem; }
.grade-form-label .required { color: var(--form-danger); }

.grade-input-wrapper { position: relative; }
.grade-form-input { width: 100%; padding: 0.75rem 1rem 0.75rem 2.75rem; border: 2px solid var(--form-border); border-radius: 10px; font-size: 0.9375rem; background: var(--form-light); color: var(--form-dark); transition: all 0.2s; }
.grade-form-input:focus { outline: none; border-color: var(--form-primary); background: var(--form-white); box-shadow: 0 0 0 4px rgba(var(--form-primary-rgb), 0.1); }
.grade-form-input.is-invalid { border-color: var(--form-danger); background: #fef2f2; }
.grade-input-icon { position: absolute; left: 0.875rem; top: 50%; transform: translateY(-50%); color: var(--form-gray); font-size: 1.125rem; }

.grade-form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }

.grade-form-actions { margin-top: 1.5rem; padding-top: 1.25rem; border-top: 2px solid var(--form-border); }
.grade-btn { display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; width: 100%; padding: 0.875rem 1.5rem; border-radius: 12px; font-size: 0.9375rem; font-weight: 600; cursor: pointer; transition: all 0.2s; border: none; }
.grade-btn-primary { background: linear-gradient(135deg, var(--form-primary), #8b5cf6); color: white; box-shadow: 0 4px 12px rgba(var(--form-primary-rgb), 0.3); }
.grade-btn-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(var(--form-primary-rgb), 0.4); }
.grade-btn-primary:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }

.invalid-feedback { display: block; margin-top: 0.375rem; padding: 0.375rem 0.625rem; background: #fef2f2; border-radius: 6px; font-size: 0.8rem; color: var(--form-danger); }

@media (max-width: 480px) { .grade-form-row { grid-template-columns: 1fr; } }
</style>

<div class="grade-form-container">
    <div class="grade-form-header">
        <div class="grade-form-icon">
            <i class="mdi mdi-clipboard-plus"></i>
        </div>
        <div class="grade-form-title">
            <h3><?php echo get_phrase('create_grade'); ?></h3>
            <p><?php echo get_phrase('add_new_grade_to_system'); ?></p>
        </div>
    </div>
    
    <form method="POST" class="d-block ajaxForm" action="<?php echo route('grade/create'); ?>">
        <input type="hidden" name="<?=csrf_token();?>" value="<?=csrf_hash();?>" />
        
        <div class="grade-form-group">
            <label class="grade-form-label">
                <i class="mdi mdi-alphabet-latin"></i>
                <span><?php echo get_phrase('grade'); ?></span>
                <span class="required">*</span>
            </label>
            <div class="grade-input-wrapper">
                <i class="mdi mdi-school-outline grade-input-icon"></i>
                <input type="text" class="grade-form-input" id="grade" name="grade" placeholder="<?php echo get_phrase('enter_grade_name'); ?>" required>
            </div>
        </div>

        <div class="grade-form-group">
            <label class="grade-form-label">
                <i class="mdi mdi-star-circle"></i>
                <span><?php echo get_phrase('grade_point'); ?></span>
                <span class="required">*</span>
            </label>
            <div class="grade-input-wrapper">
                <i class="mdi mdi-numeric grade-input-icon"></i>
                <input type="number" step="0.01" class="grade-form-input" id="grade_point" name="grade_point" placeholder="<?php echo get_phrase('enter_grade_point'); ?>" required>
            </div>
        </div>

        <div class="grade-form-row">
            <div class="grade-form-group">
                <label class="grade-form-label">
                    <i class="mdi mdi-arrow-collapse-right"></i>
                    <span><?php echo get_phrase('mark_from'); ?></span>
                    <span class="required">*</span>
                </label>
                <div class="grade-input-wrapper">
                    <i class="mdi mdi-percent grade-input-icon"></i>
                    <input type="number" class="grade-form-input" id="mark_from" name="mark_from" placeholder="0" required>
                </div>
            </div>

            <div class="grade-form-group">
                <label class="grade-form-label">
                    <i class="mdi mdi-arrow-expand-right"></i>
                    <span><?php echo get_phrase('mark_upto'); ?></span>
                    <span class="required">*</span>
                </label>
                <div class="grade-input-wrapper">
                    <i class="mdi mdi-percent grade-input-icon"></i>
                    <input type="number" class="grade-form-input" id="mark_upto" name="mark_upto" placeholder="100" required>
                </div>
            </div>
        </div>
        
        <div class="grade-form-actions">
            <button class="grade-btn grade-btn-primary" id="submit-btn" type="submit">
                <i class="mdi mdi-plus-circle"></i>
                <span><?php echo get_phrase('create_grade'); ?></span>
            </button>
        </div>
    </form>
</div>

<script>
$(document).ready(function() {
    const gradeInput = $('#grade');
    const gradePointInput = $('#grade_point');
    const markFromInput = $('#mark_from');
    const markUptoInput = $('#mark_upto');
    const gradeRegex = /^[a-zA-Z0-9 ]+$/;

    function showError(input, message) {
        input.addClass('is-invalid');
        if (input.parent().find('.invalid-feedback').length === 0) {
            input.after('<div class="invalid-feedback">' + message + '</div>');
        }
    }

    function hideError(input) {
        input.removeClass('is-invalid');
        input.parent().find('.invalid-feedback').remove();
    }

    // Real-time validation
    gradeInput.on('input', function() {
        const val = $(this).val().trim();
        if (val.length < 1 || !gradeRegex.test(val)) {
            showError(gradeInput, '<?php echo addslashes(get_phrase("Invalid_grade")); ?>');
        } else { hideError(gradeInput); }
    });

    gradePointInput.on('input', function() {
        const val = parseFloat($(this).val());
        if (isNaN(val) || val < 0) {
            showError(gradePointInput, '<?php echo addslashes(get_phrase("Invalid_grade_point")); ?>');
        } else { hideError(gradePointInput); }
    });

    markFromInput.on('input', function() {
        const fromVal = parseInt($(this).val());
        const uptoVal = parseInt(markUptoInput.val());
        if (isNaN(fromVal) || fromVal < 0 || (markUptoInput.val() && fromVal >= uptoVal)) {
            showError(markFromInput, '<?php echo addslashes(get_phrase("Invalid_mark_range")); ?>');
        } else { hideError(markFromInput); }
    });

    markUptoInput.on('input', function() {
        const fromVal = parseInt(markFromInput.val());
        const uptoVal = parseInt($(this).val());
        if (isNaN(uptoVal) || uptoVal <= fromVal) {
            showError(markUptoInput, '<?php echo addslashes(get_phrase("Invalid_mark_range")); ?>');
        } else { hideError(markUptoInput); }
    });

    // Form submission
    $(".ajaxForm").on('submit', function(e) {
        e.preventDefault();
        const $form = $(this);
        let isValid = true;

        const gradeVal = gradeInput.val().trim();
        const gradePointVal = parseFloat(gradePointInput.val());
        const markFromVal = parseInt(markFromInput.val());
        const markUptoVal = parseInt(markUptoInput.val());

        if (!gradeVal || !gradeRegex.test(gradeVal)) { showError(gradeInput, '<?php echo addslashes(get_phrase("Invalid_grade")); ?>'); isValid = false; }
        if (isNaN(gradePointVal) || gradePointVal < 0) { showError(gradePointInput, '<?php echo addslashes(get_phrase("Invalid_grade_point")); ?>'); isValid = false; }
        if (isNaN(markFromVal) || markFromVal < 0 || markFromVal >= markUptoVal) { showError(markFromInput, '<?php echo addslashes(get_phrase("Invalid_mark_range")); ?>'); isValid = false; }
        if (isNaN(markUptoVal) || markUptoVal <= markFromVal) { showError(markUptoInput, '<?php echo addslashes(get_phrase("Invalid_mark_range")); ?>'); isValid = false; }

        if (!isValid) return;

        const $btn = $form.find('button[type="submit"]');
        $btn.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin"></i> <?php echo addslashes(get_phrase("creating")); ?>...');

        $.ajax({
            url: $form.attr('action'),
            type: 'POST',
            data: new FormData(this),
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.status) {
                    $btn.removeClass('grade-btn-primary').css({'background': 'linear-gradient(135deg, #10b981, #34d399)'})
                        .html('<i class="mdi mdi-check-circle"></i> <?php echo addslashes(get_phrase("created")); ?>!');
                    toastr.success(response.notification);
                    setTimeout(() => location.reload(), 1500);
                } else {
                    $btn.prop('disabled', false).html('<i class="mdi mdi-plus-circle"></i> <?php echo addslashes(get_phrase("create_grade")); ?>');
                    toastr.error(response.notification || '<?php echo addslashes(get_phrase("action_not_allowed")); ?>');
                }
            },
            error: function() {
                $btn.prop('disabled', false).html('<i class="mdi mdi-plus-circle"></i> <?php echo addslashes(get_phrase("create_grade")); ?>');
                toastr.error('<?php echo addslashes(get_phrase("an_error_occurred")); ?>');
            }
        });
    });
});
</script>
