<style>
    .exp-modal-form {
        padding: 0.5rem;
    }
    .exp-form-group {
        margin-bottom: 1.5rem;
    }
    .exp-label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 600;
        color: #1e293b;
        font-size: 0.9rem;
    }
    .exp-label .required {
        color: #ef4444;
        margin-left: 0.25rem;
    }
    .exp-input {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        background: #f8fafc;
        color: #1e293b;
    }
    .exp-input:focus {
        outline: none;
        border-color: #6366f1;
        background: white;
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
    }
    .exp-file-upload {
        position: relative;
        width: 100%;
    }
    .exp-file-upload input[type="file"] {
        padding: 0.75rem;
        border: 2px dashed #cbd5e1;
        background: #f8fafc;
        border-radius: 12px;
        width: 100%;
        cursor: pointer;
        transition: all 0.3s;
    }
    .exp-file-upload input[type="file"]:hover {
        border-color: #6366f1;
        background: #eef2ff;
    }
    .exp-submit-btn {
        width: 100%;
        padding: 1rem;
        background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
        color: white;
        border: none;
        border-radius: 12px;
        font-weight: 700;
        font-size: 1rem;
        cursor: pointer;
        transition: transform 0.2s, box-shadow 0.2s;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 0.5rem;
    }
    .exp-submit-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.4);
    }
    .exp-submit-btn:disabled {
        opacity: 0.7;
        cursor: not-allowed;
        transform: none;
    }
</style>

<form method="POST" class="d-block ajaxForm exp-modal-form" action="<?php echo route('syllabus/create'); ?>" enctype="multipart/form-data">
    <input type="hidden" name="<?=$this->security->get_csrf_token_name();?>" value="<?=$this->security->get_csrf_hash();?>" />
    
    <?php $school_id = school_id(); ?>
    <input type="hidden" name="school_id" value="<?php echo $school_id; ?>">
    <input type="hidden" name="session_id" value="<?php echo active_session(); ?>">
    
    <div class="exp-form-group">
        <label for="title" class="exp-label"><?php echo get_phrase('title'); ?><span class="required">*</span></label>
        <input type="text" class="exp-input" id="title" name="title" required>
    </div>

    <div class="exp-form-group">
        <label for="class_id_on_create" class="exp-label"><?php echo get_phrase('class'); ?><span class="required">*</span></label>
        <select class="exp-input select2" id="class_id_on_create" name="class_id" required>
            <option value=""><?php echo get_phrase('select_a_class'); ?></option>
            <?php $classes = $this->db->get_where('classes', array('school_id' => $school_id))->result_array(); ?>
            <?php foreach($classes as $class): ?>
                <option value="<?php echo $class['id']; ?>"><?php echo $class['name']; ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="exp-form-group">
        <label for="syllabus_file" class="exp-label"><?php echo get_phrase('upload_syllabus'); ?><span class="required">*</span></label>
        <div class="exp-file-upload">
            <input type="file" id="syllabus_file" name="syllabus_file" accept=".pdf,.doc,.docx,.txt" required>
        </div>
        <small class="text-muted mt-2 d-block">Allowed: .pdf, .doc, .docx, .txt (Max 10MB)</small>
    </div>

    <div class="exp-form-group mb-0">
        <button class="exp-submit-btn" id="update-btn" type="submit">
            <i class="mdi mdi-plus"></i> <?php echo get_phrase('create_syllabus'); ?>
        </button>
    </div>
</form>

<script>
$('document').ready(function(){
    $('select.select2:not(.normal)').each(function () { 
        $(this).select2({ dropdownParent: '#right-modal' }); 
    });

    $(".ajaxForm").validate({});
    $(".ajaxForm").submit(function(e) {
        e.preventDefault();
        var form = $(this);

        var maxSize = 10 * 1024 * 1024; // 10 Mo
        var syllabusFile = $('#syllabus_file')[0].files[0];
        if (syllabusFile && syllabusFile.size > maxSize) {
            error_notify('<?php echo js_phrase(get_phrase('file_size_exceeds_10mb')); ?>');
            return false;
        }

        var allowedExtensions = ['pdf', 'doc', 'docx', 'txt'];
        var fileExtension = syllabusFile.name.split('.').pop().toLowerCase();
        if (!allowedExtensions.includes(fileExtension)) {
            error_notify('<?php echo js_phrase(get_phrase('invalid_file_type')); ?>');
            return false;
        }

        var submitButton = $(this).find('button[type="submit"]');
        var adding_text = "<?php echo get_phrase('creating'); ?>...";
        
        submitButton.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin"></i> '+adding_text);
        
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
                    $('input[name="' + response.csrf.name + '"]').val(response.csrf.hash);
                    setTimeout(function() {
                        location.reload();
                    }, 3500);
                } else {
                    error_notify(response.notification || '<?php echo js_phrase(get_phrase('action_not_allowed')); ?>');
                    submitButton.prop('disabled', false).html('<i class="mdi mdi-plus"></i> <?php echo get_phrase('create_syllabus'); ?>');
                }
            },
            error: function () {
                error_notify('<?php echo htmlspecialchars(get_phrase('an_error_occurred_during_submission'), ENT_QUOTES); ?>');
                submitButton.prop('disabled', false).html('<i class="mdi mdi-plus"></i> <?php echo get_phrase('create_syllabus'); ?>');
            }
        });
    });
});
</script>
