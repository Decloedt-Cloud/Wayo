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

/* Drag & Drop Zone */
.exp-drop-zone {
    width: 100%;
    padding: 2.5rem 1.5rem;
    border: 3px dashed var(--form-border);
    border-radius: 16px;
    background: var(--form-light);
    text-align: center;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.exp-drop-zone:hover {
    border-color: var(--form-primary);
    background: rgba(var(--form-primary-rgb), 0.05);
}

.exp-drop-zone.drag-over {
    border-color: var(--form-primary);
    background: rgba(var(--form-primary-rgb), 0.1);
    transform: scale(1.02);
}

.exp-drop-zone.drag-over .exp-drop-icon {
    transform: scale(1.2);
    color: var(--form-primary);
}

.exp-drop-icon {
    font-size: 3rem;
    color: var(--form-gray);
    transition: all 0.3s;
    margin-bottom: 1rem;
}

.exp-drop-text {
    font-size: 1rem;
    color: var(--form-dark);
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.exp-drop-hint {
    font-size: 0.875rem;
    color: var(--form-gray);
}

/* File Preview */
.exp-file-preview {
    display: none;
    margin-top: 1rem;
    padding: 1rem;
    background: rgba(var(--form-primary-rgb), 0.1);
    border-radius: 12px;
    align-items: center;
    gap: 1rem;
}

.exp-file-preview.active {
    display: flex;
}

.exp-file-preview-icon {
    width: 48px;
    height: 48px;
    border-radius: 10px;
    background: linear-gradient(135deg, var(--form-primary), #8b5cf6);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
}

.exp-file-preview-info {
    flex: 1;
}

.exp-file-preview-name {
    font-size: 0.9375rem;
    font-weight: 600;
    color: var(--form-dark);
    margin-bottom: 0.25rem;
    word-break: break-all;
}

.exp-file-preview-size {
    font-size: 0.8125rem;
    color: var(--form-gray);
}

.exp-file-preview-remove {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    background: var(--form-danger);
    color: white;
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
}

.exp-file-preview-remove:hover {
    transform: scale(1.1);
    box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
}

/* Progress Bar */
.exp-progress-container {
    display: none;
    margin-top: 1rem;
    background: var(--form-border);
    border-radius: 10px;
    height: 8px;
    overflow: hidden;
    position: relative;
}

.exp-progress-container.active {
    display: block;
}

.exp-progress-bar {
    width: 0%;
    height: 100%;
    background: linear-gradient(90deg, var(--form-primary), #8b5cf6);
    border-radius: 10px;
    transition: width 0.3s ease;
    position: relative;
}

.exp-progress-bar::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(
        90deg,
        transparent,
        rgba(255, 255, 255, 0.3),
        transparent
    );
    animation: shimmer 2s infinite;
}

@keyframes shimmer {
    0% {
        transform: translateX(-100%);
    }
    100% {
        transform: translateX(100%);
    }
}

.exp-progress-text {
    display: none;
    margin-top: 0.5rem;
    font-size: 0.875rem;
    color: var(--form-gray);
    text-align: center;
}

.exp-progress-text.active {
    display: block;
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

    <form method="POST" class="d-block ajaxForm" action="<?php echo site_url('admin/syllabus/create'); ?>" enctype="multipart/form-data">
        <!-- Champ caché pour le jeton CSRF -->
        <input type="hidden" name="<?=csrf_token();?>" value="<?=csrf_hash();?>" />
        
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
                    <?php $classes = db()->table('classes')->where('school_id', $school_id)->get()->getResultArray(); ?>
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
            
            <div class="exp-drop-zone" id="dropZone" role="button" tabindex="0" aria-label="<?php echo get_phrase('drag_and_drop_file_or_click_to_upload'); ?>">
                <div class="exp-drop-icon">
                    <i class="mdi mdi-cloud-upload-outline"></i>
                </div>
                <div class="exp-drop-text">
                    <?php echo get_phrase('drag_and_drop_file_here'); ?>
                </div>
                <div class="exp-drop-hint">
                    <?php echo get_phrase('or_click_to_browse'); ?> • .pdf, .doc, .docx, .txt • Max 20 Mo
                </div>
                <input type="file" id="syllabus_file" name="syllabus_file" accept=".pdf,.doc,.docx,.txt" required style="display: none;">
            </div>
            
            <div class="exp-file-preview" id="filePreview">
                <div class="exp-file-preview-icon">
                    <i class="mdi mdi-file-document-outline" id="previewIcon"></i>
                </div>
                <div class="exp-file-preview-info">
                    <div class="exp-file-preview-name" id="fileName"></div>
                    <div class="exp-file-preview-size" id="fileSize"></div>
                </div>
                <button type="button" class="exp-file-preview-remove" id="removeFile" aria-label="<?php echo get_phrase('remove_file'); ?>">
                    <i class="mdi mdi-close"></i>
                </button>
            </div>
            
            <div class="exp-progress-container" id="progressContainer">
                <div class="exp-progress-bar" id="progressBar"></div>
            </div>
            <div class="exp-progress-text" id="progressText"></div>
            
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

    var dropZone = document.getElementById('dropZone');
    var fileInput = document.getElementById('syllabus_file');
    var filePreview = document.getElementById('filePreview');
    var fileName = document.getElementById('fileName');
    var fileSize = document.getElementById('fileSize');
    var previewIcon = document.getElementById('previewIcon');
    var removeFileBtn = document.getElementById('removeFile');
    var progressContainer = document.getElementById('progressContainer');
    var progressBar = document.getElementById('progressBar');
    var progressText = document.getElementById('progressText');

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        var k = 1024;
        var sizes = ['Bytes', 'KB', 'MB', 'GB'];
        var i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    function getFileIcon(extension) {
        var icons = {
            'pdf': 'mdi-file-pdf-outline',
            'doc': 'mdi-file-word-outline',
            'docx': 'mdi-file-word-outline',
            'txt': 'mdi-file-document-outline'
        };
        return icons[extension.toLowerCase()] || 'mdi-file-outline';
    }

    function showFilePreview(file) {
        fileName.textContent = file.name;
        fileSize.textContent = formatFileSize(file.size);
        var extension = file.name.split('.').pop().toLowerCase();
        previewIcon.className = 'mdi ' + getFileIcon(extension);
        filePreview.classList.add('active');
        dropZone.style.display = 'none';
    }

    function hideFilePreview() {
        filePreview.classList.remove('active');
        dropZone.style.display = 'block';
        fileInput.value = '';
        progressContainer.classList.remove('active');
        progressText.classList.remove('active');
        progressBar.style.width = '0%';
    }

    function validateFile(file) {
        var maxSize = 20 * 1024 * 1024;
        var allowedExtensions = ['pdf', 'doc', 'docx', 'txt'];
        var extension = file.name.split('.').pop().toLowerCase();

        if (file.size > maxSize) {
            $('#syllabus_file_error').text('<?php echo js_phrase(get_phrase('file_size_exceeds_20mb')); ?>').removeClass('d-none').addClass('d-block');
            return false;
        }

        if (!allowedExtensions.includes(extension)) {
            $('#syllabus_file_error').text('<?php echo js_phrase(get_phrase('invalid_file_type')); ?>').removeClass('d-none').addClass('d-block');
            return false;
        }

        $('#syllabus_file_error').text('').addClass('d-none').removeClass('d-block');
        return true;
    }

    dropZone.addEventListener('click', function() {
        fileInput.click();
    });

    dropZone.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            fileInput.click();
        }
    });

    dropZone.addEventListener('dragover', function(e) {
        e.preventDefault();
        e.stopPropagation();
        dropZone.classList.add('drag-over');
    });

    dropZone.addEventListener('dragleave', function(e) {
        e.preventDefault();
        e.stopPropagation();
        dropZone.classList.remove('drag-over');
    });

    dropZone.addEventListener('drop', function(e) {
        e.preventDefault();
        e.stopPropagation();
        dropZone.classList.remove('drag-over');

        var files = e.dataTransfer.files;
        if (files.length > 0) {
            var file = files[0];
            if (validateFile(file)) {
                fileInput.files = files;
                showFilePreview(file);
            }
        }
    });

    fileInput.addEventListener('change', function() {
        if (this.files.length > 0) {
            var file = this.files[0];
            if (validateFile(file)) {
                showFilePreview(file);
            }
        }
    });

    removeFileBtn.addEventListener('click', function() {
        hideFilePreview();
    });

    $(".ajaxForm").validate({});
    $(".ajaxForm").submit(function(e) {
        e.preventDefault();
        var form = $(this);

        var syllabusFile = $('#syllabus_file')[0].files[0];
        if (!syllabusFile) {
            $('#syllabus_file_error').text('<?php echo js_phrase(get_phrase('please_select_a_file')); ?>').removeClass('d-none').addClass('d-block');
            return false;
        }

        if (!validateFile(syllabusFile)) {
            return false;
        }

        var submitButton = $(this).find('button[type="submit"]');
        var adding_text = "<?php echo htmlspecialchars(get_phrase('creating'), ENT_QUOTES); ?>...";
        submitButton.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin"></i>'+adding_text);

        var csrf = getCsrfToken();
        const formData = new FormData(this);

        progressContainer.classList.add('active');
        progressText.classList.add('active');
        progressText.textContent = '<?php echo get_phrase('uploading'); ?>... 0%';

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            xhr: function() {
                var xhr = new window.XMLHttpRequest();
                xhr.upload.addEventListener('progress', function(e) {
                    if (e.lengthComputable) {
                        var percentComplete = Math.round((e.loaded / e.total) * 100);
                        progressBar.style.width = percentComplete + '%';
                        progressText.textContent = '<?php echo get_phrase('uploading'); ?>... ' + percentComplete + '%';
                    }
                }, false);
                return xhr;
            },
            success: function (response) {
                console.log(`Status is ${response.status} so response is success`);
                
                if (response.status) {
                    progressBar.style.width = '100%';
                    progressText.textContent = '<?php echo get_phrase('upload_complete'); ?>';
                    
                    success_notify(response.notification);
                    $('input[name="' + response.csrf.name + '"]').val(response.csrf.hash);

                    setTimeout(function() {
                        location.reload();
                    }, 3500);
                } else {
                    console.log('else');
                    
                    error_notify(response.notification || '<?php echo js_phrase(get_phrase('action_not_allowed')); ?>');
                    submitButton.prop('disabled', false).html('<i class="mdi mdi-plus"></i><?php echo htmlspecialchars(get_phrase('create_syllabus'), ENT_QUOTES); ?>');
                    progressContainer.classList.remove('active');
                    progressText.classList.remove('active');
                    progressBar.style.width = '0%';
                }
            },
            error: function () {
                error_notify('<?php echo htmlspecialchars(get_phrase('an_error_occurred_during_submission'), ENT_QUOTES); ?>');
                submitButton.prop('disabled', false).html('<i class="mdi mdi-plus"></i><?php echo htmlspecialchars(get_phrase('create_syllabus'), ENT_QUOTES); ?>');
                progressContainer.classList.remove('active');
                progressText.classList.remove('active');
                progressBar.style.width = '0%';
            }
        }); 
    });

    function getCsrfToken() {
        var csrfName = $('input[name="<?php echo csrf_token(); ?>"]').attr('name');
        var csrfHash = $('input[name="<?php echo csrf_token(); ?>"]').val();
        return { csrfName: csrfName, csrfHash: csrfHash };
    }
});
</script>
