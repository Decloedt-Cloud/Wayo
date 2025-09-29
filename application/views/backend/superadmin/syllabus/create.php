<link rel="stylesheet" href="<?php echo base_url();?>assets/backend/css/edit-design-button.css">

<form method="POST" class="d-block ajaxForm" action="<?php echo route('syllabus/create'); ?>" enctype="multipart/form-data">
    <!-- Champ caché pour le jeton CSRF -->
    <input type="hidden" name="<?=$this->security->get_csrf_token_name();?>" value="<?=$this->security->get_csrf_hash();?>" />
    
    <div class="form-row">
        <?php $school_id = school_id(); ?>
        <input type="hidden" name="school_id" value="<?php echo $school_id; ?>">
        <input type="hidden" name="session_id" value="<?php echo active_session(); ?>">
        <div class="form-group col-md-12 mb-2">
            <label for="title"><?php echo get_phrase('tittle'); ?><span class="required"> * </span></label>
            <input type="text" class="form-control" id="title" name = "title" required>
        </div>
        <div class="form-group col-md-12 mb-2">
            <label for="class_id_on_create"><?php echo get_phrase('class'); ?><span class="required"> * </span></label>
            <select class="form-control"  id="class_id_on_create" name="class_id"  required>
                <option value=""><?php echo get_phrase('select_a_class'); ?></option>
                <?php $classes = $this->db->get_where('classes', array('school_id' => $school_id))->result_array(); ?>
                <?php foreach($classes as $class): ?>
                    <option value="<?php echo $class['id']; ?>"><?php echo $class['name']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>



 
        <div class="form-group col-md-12 mb-2">
            <label for="syllabus_file"><?php echo get_phrase('upload_syllabus'); ?><span class="required"> * </span></label>
            <div class="custom-file-upload d-inline-block">
                <input type="file" class="form-control" id="syllabus_file" name="syllabus_file" accept=".pdf,.doc,.docx,.txt" required>
            </div>
        </div>
        <div class="form-group mb-1">
            <button class="btn btn-primary btn-l px-4" id="update-btn" type="submit"><i class="mdi mdi-plus"></i><?php echo get_phrase('create_syllabus'); ?></button>
        </div>
    </div>
</form>

<script>
$('document').ready(function(){
    $('select.select2:not(.normal)').each(function () { $(this).select2({ dropdownParent: '#right-modal' }); });

    $(".ajaxForm").validate({}); // Jquery form validation initialization
    $(".ajaxForm").submit(function(e) {
        e.preventDefault(); // Bloque le comportement normal
        var form = $(this);

        // Vérification de la taille du fichier (2 Mo = 2 * 1024 * 1024 bytes)
        var maxSize = 10 * 1024 * 1024; // 2 Mo en bytes
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
                error_notify('<?php echo js_phrase(get_phrase('an_error_occurred_during_submission')); ?>');
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
</script>