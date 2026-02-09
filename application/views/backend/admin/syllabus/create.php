<style>
/* ============================================================================
   PREMIUM FORM DESIGN - CREATE SYLLABUS
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

/* File Input */
.exp-file-input {
    width: 100%;
    padding: 0.6rem 1rem;
    border: 2px dashed var(--form-border);
    border-radius: 12px;
    background: var(--form-light);
    cursor: pointer;
    transition: all 0.2s;
}

.exp-file-input:hover {
    border-color: var(--form-primary);
    background: rgba(var(--form-primary-rgb), 0.05);
}

/* Buttons */
.exp-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.625rem;
    padding: 0.875rem 1.75rem;
    border-radius: 12px;
    font-size: 0.95rem;
    font-weight: 700;
    letter-spacing: 0.5px;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
    text-transform: uppercase;
    width: 100%;
}

.exp-btn-primary {
    background: linear-gradient(135deg, var(--form-primary), #8b5cf6);
    color: white;
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
}

.exp-btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(99, 102, 241, 0.4);
}

.exp-btn-primary:disabled {
    opacity: 0.7;
    cursor: not-allowed;
    transform: none;
}
</style>

<div class="exp-form-container">
    <div class="exp-form-header">
        <div class="exp-form-icon">
            <i class="fas fa-folder-plus"></i>
        </div>
        <div class="exp-form-title">
            <h3><?php echo get_phrase('create_syllabus'); ?></h3>
            <p><?php echo get_phrase('upload_and_share_study_materials'); ?></p>
        </div>
    </div>

    <form method="POST" class="d-block ajaxForm" action="<?php echo route('syllabus/create'); ?>" enctype="multipart/form-data">
        <!-- Champ caché pour le jeton CSRF -->
        <input type="hidden" name="<?=$this->security->get_csrf_token_name();?>" value="<?=$this->security->get_csrf_hash();?>" />
        
        <?php $school_id = school_id(); ?>
        <input type="hidden" name="school_id" value="<?php echo $school_id; ?>">
        <input type="hidden" name="session_id" value="<?php echo active_session(); ?>">
        
        <div class="exp-form-group">
            <label class="exp-form-label" for="title">
                <i class="mdi mdi-format-title"></i>
                <?php echo get_phrase('title'); ?>
                <span class="exp-required">*</span>
            </label>
            <div class="exp-input-wrapper">
                <input type="text" class="exp-form-input" id="title" name="title" required placeholder="<?php echo get_phrase('enter_syllabus_title'); ?>">
                <i class="mdi mdi-pencil-outline exp-input-icon"></i>
            </div>
        </div>

        <div class="exp-form-group">
            <label class="exp-form-label" for="class_id_on_create">
                <i class="mdi mdi-google-classroom"></i>
                <?php echo get_phrase('class'); ?>
                <span class="exp-required">*</span>
            </label>
            <div class="exp-input-wrapper">
                <select class="exp-form-select" id="class_id_on_create" name="class_id" required>
                    <option value=""><?php echo get_phrase('select_a_class'); ?></option>
                    <?php $classes = $this->db->get_where('classes', array('school_id' => $school_id))->result_array(); ?>
                    <?php foreach($classes as $class): ?>
                        <option value="<?php echo $class['id']; ?>"><?php echo $class['name']; ?></option>
                    <?php endforeach; ?>
                </select>
                <i class="mdi mdi-school exp-input-icon"></i>
            </div>
        </div>

        <div class="exp-form-group">
            <label class="exp-form-label" for="syllabus_file">
                <i class="mdi mdi-file-upload-outline"></i>
                <?php echo get_phrase('upload_syllabus'); ?>
                <span class="exp-required">*</span>
            </label>
            <div class="exp-input-wrapper">
                <input type="file" class="exp-file-input" id="syllabus_file" name="syllabus_file" accept=".pdf,.doc,.docx,.txt" required>
            </div>
            <small class="text-muted mt-1 d-block">
                <i class="mdi mdi-information-outline"></i> 
                <?php echo get_phrase('allowed_files'); ?>: .pdf, .doc, .docx, .txt • <?php echo get_phrase('max_size:_20_mo'); ?>
            </small>
            <small id="syllabus_file_error" class="text-danger mt-1 d-none"></small>
        </div>

        <div class="exp-form-group mt-4">
            <button class="exp-btn exp-btn-primary" id="update-btn" type="submit">
                <i class="mdi mdi-plus"></i>
                <?php echo get_phrase('create_syllabus'); ?>
            </button>
        </div>
    </form>
</div>

<script>
$('document').ready(function(){
    $('select.select2:not(.normal)').each(function () { $(this).select2({ dropdownParent: '#right-modal' }); });

    $(".ajaxForm").validate({}); // Jquery form validation initialization
    $(".ajaxForm").submit(function(e) {
        e.preventDefault(); // Bloque le comportement normal
        var form = $(this);

        var maxSize = 20 * 1024 * 1024; // 20 Mo en bytes
        var syllabusFile = $('#syllabus_file')[0].files[0];
        if (syllabusFile && syllabusFile.size > maxSize) {
            $('#syllabus_file_error').text('<?php echo js_phrase(get_phrase('file_size_exceeds_20mb')); ?>').removeClass('d-none').addClass('d-block');
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
        var adding_text = "<?php echo htmlspecialchars(get_phrase('creating'), ENT_QUOTES); ?>...";
        
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
                // return false;
                console.log(`Status is ${response.status} so response is success`);
                
                if (response.status) {
                    success_notify(response.notification);
                    // Met à jour le token CSRF
                    $('input[name="' + response.csrf.name + '"]').val(response.csrf.hash);

                    // Rafraîchissement de la page après un léger délai
                    setTimeout(function() {
                        location.reload();
                    }, 3500);
                } else {
                    console.log('else')
                    
                    error_notify(response.notification || '<?php echo js_phrase(get_phrase('action_not_allowed')); ?>');
                    submitButton.prop('disabled', false).html('<i class="mdi mdi-plus"></i><?php echo htmlspecialchars(get_phrase('create_syllabus'), ENT_QUOTES); ?>');
                }
            },
            error: function () {
                error_notify('<?php echo htmlspecialchars(get_phrase('an_error_occurred_during_submission'), ENT_QUOTES); ?>');
                submitButton.prop('disabled', false).html('<i class="mdi mdi-plus"></i><?php echo htmlspecialchars(get_phrase('create_syllabus'), ENT_QUOTES); ?>');
            }
        }); 
    });

    $('#syllabus_file').on('change', function() {
        var maxSize = 20 * 1024 * 1024;
        var f = this.files[0];
        if (f && f.size > maxSize) {
            $('#syllabus_file_error').text('<?php echo js_phrase(get_phrase('file_size_exceeds_20mb')); ?>').removeClass('d-none').addClass('d-block');
        } else {
            $('#syllabus_file_error').text('').addClass('d-none').removeClass('d-block');
        }
    });

    function getCsrfToken() {
        var csrfName = $('input[name="<?php echo $this->security->get_csrf_token_name(); ?>"]').attr('name');
        var csrfHash = $('input[name="<?php echo $this->security->get_csrf_token_name(); ?>"]').val();
        return { csrfName: csrfName, csrfHash: csrfHash };
    }
});

initCustomFileUploader();
</script>
