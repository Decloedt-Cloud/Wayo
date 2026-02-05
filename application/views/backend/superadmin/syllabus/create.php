<link rel="stylesheet" href="<?php echo base_url();?>assets/backend/css/edit-design-button.min.css">

<form method="POST" class="d-block ajaxForm" action="<?php echo route('syllabus/create'); ?>" enctype="multipart/form-data">
    <!-- Champ caché pour le jeton CSRF -->
    <input type="hidden" name="<?=$this->security->get_csrf_token_name();?>" value="<?=$this->security->get_csrf_hash();?>" />
    
    <div class="form-row">
        <?php $school_id = school_id(); ?>
        <input type="hidden" name="school_id" value="<?php echo $school_id; ?>">
        <input type="hidden" name="session_id" value="<?php echo active_session(); ?>">
        <div class="form-group col-md-12 mb-2">
            <label for="title"><?php echo get_phrase('title'); ?><span class="required"> * </span></label>
            <input type="text" class="form-control" id="title" name = "title" >
            <small id="title_help" class="text-danger d-none"></small>
        </div>
        <div class="form-group col-md-12 mb-2">
            <label for="class_id_on_create"><?php echo get_phrase('class'); ?><span class="required"> * </span></label>
            <select class="form-control"  id="class_id_on_create" name="class_id"  >
                <option value=""><?php echo get_phrase('select_a_class'); ?></option>
                <?php $classes = $this->db->get_where('classes', array('school_id' => $school_id))->result_array(); ?>
                <?php foreach($classes as $class): ?>
                    <option value="<?php echo $class['id']; ?>"><?php echo $class['name']; ?></option>
                <?php endforeach; ?>
            </select>
            <small id="class_help" class="text-danger d-none"></small>
        </div>



 
        <div class="form-group col-md-12 mb-2">
            <label for="syllabus_file"><?php echo get_phrase('upload_syllabus'); ?><span class="required"> * </span></label>
            <div class="custom-file-upload d-inline-block">
                <input type="file" class="form-control" id="syllabus_file" name="syllabus_file" accept=".pdf,.doc,.docx,.txt" >
            </div><br>
            <small id="file_help" class="text-danger d-none"></small>
        </div>
        <div class="form-group mb-1">
            <button class="btn btn-primary btn-l px-4" id="update-btn" type="submit"><i class="mdi mdi-plus"></i><?php echo get_phrase('create_syllabus'); ?></button>
        </div>
    </div>
</form>

<script>
$(document).ready(function() {

    /* ============================
       TOASTR CONFIG
    ============================ */
    toastr.options = {
        closeButton: true,
        progressBar: true,
        positionClass: 'toast-top-right',
        timeOut: 4000,
        showMethod: 'fadeIn',
        hideMethod: 'fadeOut',
    };

    /* ============================
       CSRF TOKEN
    ============================ */
    function getCsrfToken() {
        var name = $('input[name="<?php echo $this->security->get_csrf_token_name(); ?>"]').attr('name');
        var hash = $('input[name="<?php echo $this->security->get_csrf_token_name(); ?>"]').val();
        return { name, hash };
    }

    /* ============================
       REAL-TIME VALIDATION
    ============================ */

    const titleInput = $('#title');
    const titleHelp  = $('#title_help');

    const classInput = $('#class_id_on_create');
    const classHelp  = $('#class_help');

    const fileInput  = $('#syllabus_file');
    const fileHelp   = $('#file_help');

    const titleRegex = /^[a-zA-Z0-9 ]{3,}$/;


    function showError(el, msg) {
        el.removeClass('d-none').addClass('text-danger').text(msg);
    }

    function hideError(el) {
        el.addClass('d-none').text('');
    }

    /* ---- Title Validation ---- */
    titleInput.on('input', function () {
        const value = $(this).val().trim();
        if (!titleRegex.test(value)) {
            showError(titleHelp, "<?php echo addslashes(get_phrase('invalid_title (min_3_characters)')); ?>");
            titleInput.addClass('is-invalid');
        } else {
            hideError(titleHelp);
            titleInput.removeClass('is-invalid');
        }
    });

    /* ---- Class Validation ---- */
    classInput.on('change', function () {
        if ($(this).val() === '') {
            showError(classHelp, "<?php echo addslashes(get_phrase('please_select_a_class')); ?>");
            classInput.addClass('is-invalid');
        } else {
            hideError(classHelp);
            classInput.removeClass('is-invalid');
        }
    });

    /* ---- File Validation ---- */
    fileInput.on('change', function () {
        hideError(fileHelp);
        const file = this.files[0];
        if (!file) return;

        const allowed = ['pdf','doc','docx','txt'];
        const ext = file.name.split('.').pop().toLowerCase();

        if (!allowed.includes(ext)) {
            showError(fileHelp, "<?php echo addslashes(get_phrase('invalid_file_type')); ?>");
            fileInput.addClass('is-invalid');
            return;
        }

        if (file.size > 10 * 1024 * 1024) {
            showError(fileHelp, "<?php echo addslashes(get_phrase('file_size_exceeds_10mb')); ?>");
            fileInput.addClass('is-invalid');
            return;
        }

        fileInput.removeClass('is-invalid');
    });



    /* ============================
       FORM SUBMIT VIA AJAX
    ============================ */
    $(".ajaxForm").off("submit").on("submit", function(e) {

        e.preventDefault();
        let isValid = true;

        /* ---- FINAL VALIDATION BEFORE SUBMIT ---- */
        const titleValue = titleInput.val().trim();
        if (!titleRegex.test(titleValue)) {
            showError(titleHelp, "<?php echo addslashes(get_phrase('invalid_title (min_3_characters)')); ?>");
            titleInput.addClass('is-invalid');
            isValid = false;
        }

        if (classInput.val() === '') {
            showError(classHelp, "<?php echo addslashes(get_phrase('please_select_a_class')); ?>");
            classInput.addClass('is-invalid');
            isValid = false;
        }

        const file = fileInput[0].files[0];
        if (!file) {
            showError(fileHelp, "<?php echo addslashes(get_phrase('please_upload_a_file')); ?>");
            isValid = false;
        }

        if (!isValid) return;

        /* ---- SUBMIT ---- */
        var submitButton = $(this).find('button[type="submit"]');
        var adding_text = "<?php echo addslashes(get_phrase('creating')); ?>...";
        submitButton.prop("disabled", true).html('<i class="mdi mdi-loading mdi-spin"></i>'+adding_text);

        var csrf = getCsrfToken();
        var formData = new FormData(this);

        $.ajax({
            url: $(this).attr('action'),
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            dataType: "json",

            success: function (response) {
                if (response.status) {
                    success_notify(response.notification);
                    $('input[name="'+response.csrf.name+'"]').val(response.csrf.hash);
                    setTimeout(() => location.reload(), 3000);
                } else {
                    error_notify(response.notification);
                    submitButton.prop("disabled", false).html('<i class="mdi mdi-plus"></i><?php echo get_phrase('create_syllabus'); ?>');
                }
            },

            error: function () {
                error_notify("<?php echo addslashes(get_phrase('an_error_occurred_during_submission')); ?>");
                submitButton.prop("disabled", false).html('<i class="mdi mdi-plus"></i><?php echo get_phrase('create_syllabus'); ?>');
            }
        });
    });

});
</script>

<!-- <script>
$('document').ready(function(){
    $('select.select2:not(.normal)').each(function () { $(this).select2({ dropdownParent: '#right-modal' }); });

    $(".ajaxForm").validate({}); // Jquery form validation initialization
    $(".ajaxForm").submit(function(e) {
        e.preventDefault(); // Bloque le comportement normal
        var form = $(this);

        var maxSize = 10 * 1024 * 1024; // 10 Mo en bytes
        var syllabusFile = $('#syllabus_file')[0].files[0];
        if (syllabusFile && syllabusFile.size > maxSize) {
            error_notify('<?php echo js_phrase(get_phrase('file_size_exceeds_10mb')); ?>');
            return false; // Arrête l'exécution si le fichier est trop grand
        }

        // Vérification des types de fichiers autorisés
        var allowedExtensions = ['pdf', 'doc', 'docx', 'txt'];
        var fileExtension = syllabusFile.name.split('.').pop().toLowerCase();
        if (!allowedExtensions.includes(fileExtension)) {
            error_notify('<?php echo js_phrase(get_phrase('invalid_file_type')); ?>');
            return false; // Arrête l'exécution si le type de fichier est invalide
        }

        // Cible uniquement le bouton de ce formulaire
        var submitButton = $(this).find('button[type="submit"]');
        var adding_text = "<?php echo get_phrase('creating'); ?>...";
        
        // Désactive et met à jour uniquement ce bouton
        submitButton.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin"></i>'+adding_text);
        
        // Récupérer le token CSRF avant l'envoi
        var csrf = getCsrfToken();
        const formData = new FormData(this);

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function (response) {
                if (response.status) {
                    success_notify(response.notification);
                    // Met à jour le token CSRF
                    $('input[name="' + response.csrf.name + '"]').val(response.csrf.hash);

                    // Rafraîchissement de la page après un léger délai
                    setTimeout(function() {
                        location.reload();
                    }, 3500);
                } else {
                    error_notify(response.notification || '<?php echo js_phrase(get_phrase('action_not_allowed')); ?>');
                    submitButton.prop('disabled', false).html('<i class="mdi mdi-plus"></i><?php echo get_phrase('create_syllabus'); ?>');
                }
            },
            error: function () {
                error_notify('<?php echo htmlspecialchars(get_phrase('an_error_occurred_during_submission'), ENT_QUOTES);?>');
                submitButton.prop('disabled', false).html('<i class="mdi mdi-plus"></i><?php echo get_phrase('create_syllabus'); ?>');
            }
        });
    });

    function getCsrfToken() {
        var csrfName = $('input[name="<?php echo $this->security->get_csrf_token_name(); ?>"]').attr('name');
        var csrfHash = $('input[name="<?php echo $this->security->get_csrf_token_name(); ?>"]').val();
        return { csrfName: csrfName, csrfHash: csrfHash };
    }
});

initCustomFileUploader();
</script> -->