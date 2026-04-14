

<link rel="stylesheet" href="<?php echo base_url();?>assets/backend/css/edit-design-button.min.css">
<?php
$exam = isset($modal_exam) && is_array($modal_exam) ? $modal_exam : null;
$classes = isset($modal_exam_classes) && is_array($modal_exam_classes) ? $modal_exam_classes : [];
if (!$exam) {
    return;
}
?>
<form method="POST" class="d-block" action="<?php echo route('exam/update/'.$param1); ?>" id="examEditForm">

    <div class="form-row">

        <div class="form-group mb-1">
            <label for="exam_name"><?php echo get_phrase('exam_name'); ?><span class="required"> * </span></label>
            <input type="text" class="form-control" id="exam_name" name="exam_name" value="<?php echo html_escape($exam['name']); ?>" required>
            <small id="name_help" class="form-text text-muted"><?php echo get_phrase('provide_exam_name'); ?></small>
        </div>
        <div class="form-group mb-1">
            <label for="starting_date"><?php echo get_phrase('date'); ?><span class="required"> * </span></label>
            <input type="datetime-local" class="form-control" id="starting_date" name="starting_date" value="<?php echo date('Y-m-d\TH:i', $exam['starting_date']); ?>" required>
            <small id="date_help" class="form-text text-muted"><?php echo get_phrase('provide_date_and_time'); ?></small>
        </div>
        <div class="form-group mb-1">
            <label for="modal_class_id"><?php echo get_phrase('class'); ?><span class="required"> * </span></label>
            <select class="form-control" id="modal_class_id" name="class_id"  required>
                <option value=""><?php echo get_phrase('select_class'); ?></option>
                <?php foreach ($classes as $class): ?>
                    <option value="<?php echo html_escape($class['id']); ?>" <?php echo $class['id'] == $exam['class_id'] ? 'selected' : ''; ?>>
                        <?php echo html_escape($class['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <small id="class_help" class="form-text text-muted"><?php echo get_phrase('select_a_class'); ?></small>
        </div>

        <div class="form-group col-md-12">
            <button class="btn btn-block btn-primary btn-l px-4 " id="update-btn" type="submit"><i class="mdi mdi-account-check"></i><?php echo get_phrase('update_exam'); ?></button>
        </div>

    </div>
</form>

<style>
/* Personnaliser la notification Toastr de succès */
#toast-container .toast-success {
    background-color: #28a745;
    opacity: 1 !important;
    color: #fff;
    border-radius: 5px;
    font-size: 14px;
}
#toast-container .toast-success .toast-message,
#toast-container .toast-success .toast-title {
    color: #fff;
}
</style>

<script>

// Function to show notifications
function showNotification(type, message) {
    toastr.options = {
        closeButton: true,
        progressBar: true,
        positionClass: 'toast-top-right',
        timeOut: 5000,
        showMethod: 'fadeIn',
        hideMethod: 'fadeOut',
    };
    if (type === 'success') {
        toastr.success(message);
    } else if (type === 'error') {
        toastr.error(message);
    } else if (type === 'warning') {
        toastr.warning(message);
    }
}

$(document).ready(function() {
    // Prevent duplicate event handlers
    $('#examEditForm').off('submit');
    $('#right-modal').off('shown.bs.modal.edit hidden.bs.modal.edit');

  

    // Clean up when modal is hidden
    $('#right-modal').on('hidden.bs.modal.edit', function() {
        const $form = $('#examEditForm');
        if ($form.length) {
            $form[0].reset();
        }
 
        $('body').removeClass('modal-open');
        $('.modal-backdrop').remove();
        $('body').focus();
    });

    // Handle form submission
    let isSubmitting = false;
    $('#examEditForm').on('submit', function(e) {
        e.preventDefault();
        if (isSubmitting) {
            return;
        }
        isSubmitting = true;
        const $submitBtn = $(this).find('button[type="submit"]');
        $submitBtn.prop('disabled', true).text('<?php echo addslashes(get_phrase('updating')); ?>...');

        const formData = $(this).serialize() + '&<?php echo addslashes(csrf_token()); ?>=<?php echo addslashes(csrf_hash()); ?>';
        $.ajax({
            url: '<?php echo route('exam/update/' . $param1); ?>',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                isSubmitting = false;
                $submitBtn.prop('disabled', false).text('<?php echo addslashes(get_phrase('update_exam')); ?>');
                if (response.status === true) {
                    showNotification('success', response.message || '<?php echo addslashes(get_phrase('exam_updated_successfully')); ?>');
                    $('#right-modal').modal('hide');
                    // Update table and calendar after modal is fully hidden
                    $('#right-modal').one('hidden.bs.modal.edit', function() {
                        if (typeof window.updateExamTableAndCalendar === 'function') {
                            window.updateExamTableAndCalendar();
                        } else {
                            console.error('updateExamTableAndCalendar not found');
                            showNotification('error', '<?php echo addslashes(get_phrase('update_function_not_found')); ?>');
                        }
                    });
                } else {
                    showNotification('error', response.message || '<?php echo addslashes(get_phrase('failed_to_update_exam')); ?>');
                }
            },
            error: function(xhr, status, error) {
                isSubmitting = false;
                $submitBtn.prop('disabled', false).text('<?php echo addslashes(get_phrase('update_exam')); ?>');
                console.error('Update AJAX error:', status, error, xhr.responseText);
                showNotification('error', '<?php echo addslashes(get_phrase('failed_to_update_exam')); ?>');
            }
        });
    });

    // Update CSRF token
    $.ajax({
        url: '<?php echo site_url('superadmin/get_csrf_token'); ?>',
        type: 'GET',
        success: function(raw) {
            var d = JSON.parse(raw);
            $('#csrf_token').val(d.csrf_hash);
        },
        error: function(xhr, status, error) {
            console.error('CSRF token refresh error:', status, error, xhr.responseText);
        }
    });

    // .ajaxForm submission handler
    // Note: Ensure ajaxSubmit and showAllExams are defined elsewhere
    $(".ajaxForm").submit(function(e) {
        // If ajaxSubmit is not defined, you can replace this with a custom implementation
        ajaxSubmit(e, $(this), showAllExams);
    });

    // Initialize jQuery validation
    $('#examEditForm').validate({
        rules: {
            exam_name: { required: true, minlength: 2 },
            starting_date: { required: true },
            class_id: { required: true },
           
        },
        messages: {
            exam_name: {
                required: '<?php echo addslashes(get_phrase('exam_name_is_required')); ?>',
                minlength: '<?php echo addslashes(get_phrase('exam_name_must_be_at_least_2_characters')); ?>'
            },
            starting_date: { required: '<?php echo addslashes(get_phrase('date_is_required')); ?>' },
            class_id: { required: '<?php echo addslashes(get_phrase('class_is_required')); ?>' },
        }
    });
});

</script>