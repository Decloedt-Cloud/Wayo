<link rel="stylesheet" href="<?php echo base_url();?>assets/backend/css/edit-design-button.min.css">
<?php $modal_exam_classes = isset($modal_exam_classes) && is_array($modal_exam_classes) ? $modal_exam_classes : []; ?>
<form method="POST" class="d-block" action="<?php echo route('exam/create'); ?>" id="examCreateForm">

    <div class="form-row">
        <div class="form-group mb-1">
            <label for="exam_name"><?php echo get_phrase('exam_name'); ?><span class="required"> * </span></label>
            <input type="text" class="form-control" id="exam_name" name="exam_name" placeholder="name" >
            <small id="name_help" class="form-text"></small>
        </div>
        <div class="form-group mb-1">
            <label for="starting_date"><?php echo get_phrase('date'); ?><span class="required"> * </span></label>
            <input type="datetime-local" class="form-control" id="starting_date" name="starting_date" value="<?php echo date('Y-m-d\TH:i'); ?>" >
            <small id="date_help" class="form-text "></small>
        </div>
        <div class="form-group mb-1">
            <label for="modal_class_id"><?php echo get_phrase('class'); ?><span class="required"> * </span></label>
            <select class="form-control" id="modal_class_id" name="class_id"  >
                <option value=""><?php echo get_phrase('select_class'); ?></option>
                <?php 
                if (empty($modal_exam_classes)) {
                    echo '<option value="">' . get_phrase('no_classes_found') . '</option>';
                } else {
                    foreach ($modal_exam_classes as $class): 
                ?>
                    <option value="<?php echo html_escape($class['id']); ?>">
                        <?php echo html_escape($class['name']); ?>
                    </option>
                <?php 
                    endforeach; 
                }
                ?>
            </select>
            <small id="class_help" class="form-text"></small>
        </div>
  
        <div class="form-group col-md-12">
            <button class="btn btn-primary btn-l px-4" id="update-btn" type="submit"><i class="mdi mdi-plus"></i><?php echo get_phrase('create_exam'); ?></button>
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
$(document).ready(function() {

    /* ============================
       TOASTR CONFIG
    ============================ */
    toastr.options = {
        closeButton: true,
        progressBar: true,
        positionClass: 'toast-top-right',
        timeOut: 5000,
        showMethod: 'fadeIn',
        hideMethod: 'fadeOut',
    };

    /* ============================
       CSRF TOKEN
    ============================ */
    function getCsrfToken() {
        var csrfName = $('input[name="<?php echo addslashes(csrf_token()); ?>"]').attr('name');
        var csrfHash = $('input[name="<?php echo addslashes(csrf_token()); ?>"]').val();
        return { csrfName, csrfHash };
    }

    /* ============================
       REAL-TIME VALIDATION
    ============================ */

    const nameInput = $('#exam_name');
    const nameHelp = $('#name_help');

    const dateInput = $('#starting_date');
    const dateHelp = $('#date_help');

    const classInput = $('#modal_class_id');
    const classHelp = $('#class_help');

    const nameRegex = /^[a-zA-Z0-9 ]+$/;

    function showError(el, msg) {
        el.addClass('text-danger').text(msg).show();
    }

    function hideError(el) {
        el.hide();
    }

    nameInput.on('input', function () {
        const value = $(this).val().trim();
        if (value.length < 3 || !nameRegex.test(value)) {
            showError(nameHelp, '<?php echo addslashes(get_phrase('Invalid_name_(minimum_3_characters)')); ?>');
            nameInput.addClass('is-invalid');
        } else {
            hideError(nameHelp);
            nameInput.removeClass('is-invalid');
        }
    });

    classInput.on('change', function () {
        if ($(this).val() === '') {
            showError(classHelp, '<?php echo addslashes(get_phrase('Please_select_a_class')); ?>');
            classInput.addClass('is-invalid');
        } else {
            hideError(classHelp);
            classInput.removeClass('is-invalid');
        }
    });

    /* ============================
       GENERIC AJAX FORMS
    ============================ */
    $(".ajaxForm").validate({});
    $(".ajaxForm").submit(function(e) {
        e.preventDefault();

        var form = $(this);
        var submitButton = form.find('button[type="submit"]');
        var adding_text = "<?php echo addslashes(get_phrase('creating')); ?>...";
        submitButton.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin"></i>' + adding_text);

        var csrf = getCsrfToken();
        const formData = new FormData(this);

        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                submitButton.prop('disabled', false).text(adding_text);

                if (response.status) {
                    success_notify(response.notification);
                    $('input[name="' + response.csrf.name + '"]').val(response.csrf.hash);
                    setTimeout(() => location.reload(), 3500);
                } else {
                    error_notify('<?php echo addslashes(get_phrase('action_not_allowed')); ?>');
                }
            },
            error: function() {
                submitButton.prop('disabled', false).text(adding_text);
                error_notify('<?php echo addslashes(get_phrase('an_error_occurred_during_submission')); ?>');
            }
        });
    });

    /* ============================
       EXAM CREATE FORM
    ============================ */

    let isSubmitting = false;

    $('#examCreateForm').off('submit').on('submit', function(e) {
        e.preventDefault();
        e.stopPropagation();

        if (isSubmitting) return;

        /* ---- VALIDATION ---- */
        let isValid = true;
        const nameValue = nameInput.val().trim();

        if (nameValue.length < 3 || !nameRegex.test(nameValue)) {
            showError(nameHelp, '<?php echo addslashes(get_phrase('Invalid_name_(minimum_3_characters)')); ?>');
            nameInput.addClass('is-invalid');
            isValid = false;
        }

        if (dateInput.val().trim() === '') {
            showError(dateHelp, '<?php echo addslashes(get_phrase('Please_provide_date_and_time)')); ?>');
            dateInput.addClass('is-invalid');
            isValid = false;
        }

        if (classInput.val() === '') {
            showError(classHelp, '<?php echo addslashes(get_phrase('Please_select_a_class')); ?>');
            classInput.addClass('is-invalid');
            isValid = false;
        }

        if (!isValid) return;

        /* ---- SUBMIT ---- */
        isSubmitting = true;

        const $submitBtn = $(this).find('button[type="submit"]');
        $submitBtn.prop('disabled', true).text('<?php echo addslashes(get_phrase('creating')); ?>...');

        const formData = {
            exam_name: nameValue,
            starting_date: dateInput.val(),
            class_id: classInput.val(),
            '<?php echo addslashes(csrf_token()); ?>': '<?php echo addslashes(csrf_hash()); ?>'
        };

        $.ajax({
            url: '<?php echo route('exam/create'); ?>',
            type: 'POST',
            data: formData,
            dataType: 'json',

            success: function(response) {
                isSubmitting = false;
                $submitBtn.prop('disabled', false).text('<?php echo addslashes(get_phrase('create_exam')); ?>');

                if (response.status === true) {
                    showNotification('success', response.message || '<?php echo addslashes(get_phrase('exam_created_successfully')); ?>');

                    $('#right-modal').modal('hide');
                    $('#examCreateForm')[0].reset();

                    if (typeof window.updateExamTableAndCalendar === 'function') {
                        window.updateExamTableAndCalendar();
                    } else {
                        showNotification('error', '<?php echo addslashes(get_phrase('update_function_not_found')); ?>');
                    }
                } else {
                    showNotification('error', response.message || '<?php echo addslashes(get_phrase('failed_to_create_exam')); ?>');
                }
            },

            error: function(xhr, status, error) {
                isSubmitting = false;
                $submitBtn.prop('disabled', false).text('<?php echo addslashes(get_phrase('create_exam')); ?>');
                showNotification('error', '<?php echo addslashes(get_phrase('failed_to_create_exam')); ?>: ' + xhr.status + ' ' + error);
            }
        });
    });

    /* ============================
       MODAL HANDLERS
    ============================ */

    $('#right-modal').on('shown.bs.modal.create', function() {
        if ($('#examCreateForm').length === 0) return;
    });

    $('#right-modal').on('hidden.bs.modal.create', function() {
        const $form = $('#examCreateForm');
        if ($form.length > 0) {
            $form[0].reset();
            $('#modal_section_id').html('<option value=""><?php echo addslashes(get_phrase('select')); ?></option>');
        }
    });

});
</script>

<!-- <script>
$(document).ready(function() {
    toastr.options = {
        closeButton: true,
        progressBar: true,
        positionClass: 'toast-top-right',
        timeOut: 5000,
        showMethod: 'fadeIn',
        hideMethod: 'fadeOut'
    };

 

    let isSubmitting = false;

    // Gestionnaire pour le formulaire de création
    $('#examCreateForm').off('submit').on('submit', function(e) {
        e.preventDefault();
        e.stopPropagation();

        if (isSubmitting) {
            return;
        }

        isSubmitting = true;
        const $submitBtn = $(this).find('button[type="submit"]');
        $submitBtn.prop('disabled', true).text('<?php echo get_phrase('creating'); ?>...');

        const formData = {
            exam_name: $('#exam_name').val(),
            starting_date: $('#starting_date').val(),
            class_id: $('#modal_class_id').val(),
        
            '<?php echo csrf_token(); ?>': '<?php echo csrf_hash(); ?>'
        };

        $.ajax({
            url: '<?php echo route('exam/create'); ?>',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                isSubmitting = false;
                $submitBtn.prop('disabled', false).text('<?php echo get_phrase('create_exam'); ?>');

                try {
                    if (response.status === true) {
                        showNotification('success', response.message || '<?php echo get_phrase('exam_created_successfully'); ?>');
                        $('#right-modal').modal('hide');
                        $('#examCreateForm')[0].reset();

                        // Mettre à jour la table et le calendrier avec tous les exams
                        if (typeof window.updateExamTableAndCalendar === 'function') {
                            window.updateExamTableAndCalendar(); // Appel sans class_id pour charger tous les exams
                        } else {
                            console.error('updateExamTableAndCalendar not found');
                            showNotification('error', '<?php echo get_phrase('update_function_not_found'); ?>');
                        }
                    } else {
                        showNotification('error', response.message || '<?php echo get_phrase('failed_to_create_exam'); ?>');
                    }
                } catch (e) {
                    console.error('Erreur de traitement de la réponse:', e, response);
                    showNotification('error', '<?php echo get_phrase('invalid_server_response'); ?>');
                }
            },
            error: function(xhr, status, error) {
                console.error('Exam create AJAX error:', status, error, xhr.responseText);
                isSubmitting = false;
                $submitBtn.prop('disabled', false).text('<?php echo get_phrase('create_exam'); ?>');
                showNotification('error', '<?php echo get_phrase('failed_to_create_exam'); ?>: ' + xhr.status + ' ' + error);
            }
        });
    });

    // Gestionnaire pour l'ouverture du modal, spécifique au formulaire de création
    $('#right-modal').on('shown.bs.modal.create', function() {
        if ($('#examCreateForm').length === 0) {
            return; // Ne pas exécuter si ce n'est pas le formulaire de création
        }
        const class_id = $('#modal_class_id').val();
        
    });

    // Gestionnaire pour la fermeture du modal, spécifique au formulaire de création
    $('#right-modal').on('hidden.bs.modal.create', function() {
        if ($('#examCreateForm').length === 0) {
            return; // Ne pas exécuter si ce n'est pas le formulaire de création
        }
        const $form = $('#examCreateForm');
        if ($form.length > 0) {
            $form[0].reset();
            $form.find('button[type="submit"]').blur();
        }
        $('body').focus();
    });
});
</script> -->