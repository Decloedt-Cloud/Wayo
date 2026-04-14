<?php $classes = isset($student_create_classes) && is_array($student_create_classes) ? $student_create_classes : []; ?>

<form method="POST" class="d-block ajaxForm" action="<?php echo site_url('admin/student/create_bulk_student'); ?>" id="student_admission_form">
    <!-- CSRF Token -->
    <input type="hidden" name="<?= csrf_token(); ?>" value="<?= csrf_hash(); ?>" />

    <!-- Info Card -->
    <div class="ec-info-card">
        <div class="ec-info-card-icon">
            <i class="mdi mdi-account-multiple-plus"></i>
        </div>
        <div class="ec-info-card-content">
            <h4><?php echo get_phrase('bulk_student_admission'); ?></h4>
            <p><?php echo get_phrase('add_multiple_students_at_once'); ?></p>
        </div>
    </div>

    <!-- Class Selection -->
    <div class="row mb-4">
        <div class="col-md-6 offset-md-3">
            <div class="ec-form-group">
                <label class="ec-form-label justify-content-center">
                    <i class="mdi mdi-google-classroom"></i>
                    <span><?php echo get_phrase('select_class_for_all_students'); ?></span>
                    <span class="ec-required">*</span>
                </label>
                <div class="ec-input-wrapper">
                    <select name="class_id" id="class_id_bulk" class="ec-form-input" required>
                        <option value=""><?php echo get_phrase('select_a_class'); ?></option>
                        <?php foreach ($classes as $class) { ?>
                            <option value="<?php echo $class['id']; ?>"><?php echo $class['name']; ?></option>
                        <?php } ?>
                    </select>
                    <i class="mdi mdi-school ec-input-icon"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div id="first-row">
        <div class="student-row row align-items-center mb-3">
            <div class="col-md-4">
                <div class="ec-input-wrapper">
                    <input type="text" name="name[]" class="ec-form-input" placeholder="<?php echo get_phrase('Name'); ?>" required>
                    <i class="mdi mdi-account ec-input-icon"></i>
                </div>
            </div>
            <div class="col-md-4">
                <div class="ec-input-wrapper">
                    <input type="email" name="email[]" class="ec-form-input" placeholder="<?php echo get_phrase('Email'); ?>" required>
                    <i class="mdi mdi-email ec-input-icon"></i>
                </div>
            </div>
            <div class="col-md-3">
                <div class="ec-input-wrapper">
                    <select name="gender[]" class="ec-form-input" required>
                        <option value=""><?php echo get_phrase('Gender'); ?></option>
                        <option value="Male"><?php echo get_phrase('male'); ?></option>
                        <option value="Female"><?php echo get_phrase('female'); ?></option>
                        <option value="Others"><?php echo get_phrase('others'); ?></option>
                    </select>
                    <i class="mdi mdi-gender-male-female ec-input-icon"></i>
                </div>
            </div>
            <div class="col-md-1 text-center">
                <button type="button" class="ec-btn ec-btn-success p-2" onclick="appendRow()" style="min-width: auto; width: 42px; height: 42px; border-radius: 50%;">
                    <i class="mdi mdi-plus"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Form Actions -->
    <div class="ec-form-actions justify-content-center">
        <button type="submit" class="ec-btn ec-btn-primary" id="submit-btn">
            <i class="mdi mdi-account-multiple-check"></i>
            <span><?php echo get_phrase('add_students'); ?></span>
        </button>
    </div>
</form>

<div id="blank-row" style="display: none;">
    <div class="student-row row align-items-center mb-3">
        <div class="col-md-4">
            <div class="ec-input-wrapper">
                <input type="text" name="name[]" class="ec-form-input" placeholder="<?php echo get_phrase('Name'); ?>" required>
                <i class="mdi mdi-account ec-input-icon"></i>
            </div>
        </div>
        <div class="col-md-4">
            <div class="ec-input-wrapper">
                <input type="email" name="email[]" class="ec-form-input" placeholder="<?php echo get_phrase('Email'); ?>" required>
                <i class="mdi mdi-email ec-input-icon"></i>
            </div>
        </div>
        <div class="col-md-3">
            <div class="ec-input-wrapper">
                <select name="gender[]" class="ec-form-input" required>
                    <option value=""><?php echo get_phrase('Gender'); ?></option>
                    <option value="Male"><?php echo get_phrase('male'); ?></option>
                    <option value="Female"><?php echo get_phrase('female'); ?></option>
                    <option value="Others"><?php echo get_phrase('others'); ?></option>
                </select>
                <i class="mdi mdi-gender-male-female ec-input-icon"></i>
            </div>
        </div>
        <div class="col-md-1 text-center">
            <button type="button" class="ec-btn ec-btn-danger p-2" onclick="removeRow(this)" style="min-width: auto; width: 42px; height: 42px; border-radius: 50%;">
                <i class="mdi mdi-close"></i>
            </button>
        </div>
    </div>
</div>

<script>
    var blank_field = $('#blank-row').html();

    function appendRow() {
        $('#first-row').append(blank_field);
    }

    function removeRow(elem) {
        $(elem).closest('.student-row').remove();
    }

    $(".ajaxForm").submit(function(e) {
        e.preventDefault();
        var form = $(this);
        var submitBtn = $('#submit-btn');
        var originalBtnHtml = submitBtn.html();

        // Show loading state
        submitBtn.prop('disabled', true)
                 .html('<i class="mdi mdi-loading mdi-spin"></i> <span><?php echo get_phrase('adding'); ?>...</span>');

        // Submit form via AJAX
        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.type === 'error') {
                    submitBtn.prop('disabled', false).html(originalBtnHtml);
                    error_notify(response.notification);
                } else {
                    submitBtn.removeClass('ec-btn-primary')
                             .addClass('ec-btn-success')
                             .html('<i class="mdi mdi-check-circle"></i> <span><?php echo get_phrase('students_added'); ?>!</span>');
                    
                    success_notify(response.notification);

                    // Reset after delay
                    setTimeout(function() {
                        submitBtn.prop('disabled', false)
                                 .removeClass('ec-btn-success')
                                 .addClass('ec-btn-primary')
                                 .html(originalBtnHtml);
                        form.trigger("reset");
                        // Remove all dynamic rows except the first one (if we want to reset completely)
                        // But usually just resetting values is enough. 
                        // To be safe, let's keep it simple.
                    }, 2000);
                }
            },
            error: function(xhr, status, error) {
                submitBtn.prop('disabled', false).html(originalBtnHtml);
                error_notify("<?php echo get_phrase('an_error_occurred'); ?>");
            }
        });
    });
</script>