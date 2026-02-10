<link rel="stylesheet" href="<?php echo base_url(); ?>assets/backend/css/quilljs/quill.snow.css">
<script src="<?php echo base_url(); ?>assets/backend/js/quilljs/quill.min.js"></script>
<script src="<?php echo base_url(); ?>assets/backend/js/quilljs/image-resize.min.js"></script>

<style>
/* Modern Form Design - Same as create_post */
.modern-form-wrapper {
    padding: 1rem;
}

.modern-form-group {
    margin-bottom: 1.5rem;
}

.modern-label {
    display: block;
    margin-bottom: 0.5rem;
    font-size: 0.95rem;
    font-weight: 600;
    color: #1e293b;
}
.modern-label i {
    color: #4f46e5;
}

.modern-input {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    font-family: 'Outfit', sans-serif;
    font-size: 1rem;
    transition: all 0.2s ease;
    background: #f8fafc;
}

.modern-input:focus {
    border-color: #6366f1;
    background: white;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    outline: none;
}

.editor-container {
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    background: white;
    transition: all 0.2s;
}

.editor-container.focused {
    border-color: #6366f1;
    background: white;
    box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
}

.editor-container.is-invalid {
    border-color: #ef4444;
    background: #fef2f2;
}

/* Quill Editor Customization */
.ql-toolbar.ql-snow {
    border: none;
    border-bottom: 1px solid #e2e8f0;
    background: #f8fafc;
    padding: 0.75rem;
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

.ql-snow .ql-tooltip {
    left: 50% !important;
    transform: translateX(-50%) !important;
    white-space: nowrap !important;
    z-index: 1000 !important;
}

/* Modern Buttons */
.modern-actions {
    display: flex;
    justify-content: flex-end;
    gap: 1rem;
    margin-top: 2rem;
    padding-top: 1rem;
    border-top: 1px solid #e2e8f0;
}

.btn-modern-cancel {
    padding: 0.75rem 1.5rem;
    border-radius: 50px;
    border: 1px solid #e2e8f0;
    background: white;
    color: #64748b;
    font-weight: 600;
    font-family: 'Outfit', sans-serif;
    transition: all 0.2s;
}

.btn-modern-cancel:hover {
    background: #f1f5f9;
    color: #334155;
}

.btn-modern-submit {
    padding: 0.75rem 2rem;
    border-radius: 50px;
    background: linear-gradient(135deg, #6366f1, #818cf8);
    color: white;
    border: none;
    font-weight: 600;
    font-family: 'Outfit', sans-serif;
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.25);
    transition: all 0.2s;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-modern-submit:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(99, 102, 241, 0.35);
}

.btn-modern-submit:disabled {
    opacity: 0.7;
    cursor: not-allowed;
    transform: none;
}
</style>

<div class="row">
    <div class="col-12">
        <form action="<?php echo site_url('api/posts/' . $post['id'] . '/edit'); ?>" method="post" enctype="multipart/form-data" id="edit-post-form">
            <div class="modern-form-wrapper">
                <!-- Title Field (Optional, hidden if not used in backend yet) -->
                <!-- Note: The API edit_post currently only updates 'body', but we'll include title in case we enable it later. 
                     For now, we can hide it or keep it visible but backend might ignore it. 
                     Based on create_post, there is a title. -->
                <?php if (!empty($post['title'])): ?>
                <div class="modern-form-group">
                    <label for="title" class="modern-label">
                        <i class="fas fa-heading"></i> <?php echo get_phrase('title'); ?>
                    </label>
                    <input type="text" class="modern-input" id="title" name="title" 
                           value="<?php echo htmlspecialchars($post['title']); ?>"
                           placeholder="<?php echo get_phrase('enter_an_engaging_title'); ?>...">
                </div>
                <?php endif; ?>

                <!-- Content Field -->
                <div class="modern-form-group">
                    <label for="body" class="modern-label">
                        <i class="fas fa-align-left"></i> <?php echo get_phrase('content'); ?> <span class="text-danger">*</span>
                    </label>
                    <div class="editor-container">
                        <div id="quill-editor-edit"><?php echo $post['body']; ?></div>
                    </div>
                    <input type="hidden" name="body" id="body_edit">
                </div>

                <!-- Actions -->
                <div class="modern-actions">
                    <button type="button" class="btn-modern-cancel" data-bs-dismiss="modal">
                        <?php echo get_phrase('cancel'); ?>
                    </button>
                    <button type="submit" class="btn-modern-submit">
                        <i class="mdi mdi-content-save"></i> <?php echo get_phrase('update_post'); ?>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
(function($) {
    if (!$) {
        console.error('jQuery missing in edit_post modal');
        return;
    }
    
    // Initialize Quill
    var quillEdit;
    
    $(document).ready(function() {
        // Initialize Quill editor
        if (document.getElementById('quill-editor-edit')) {
            quillEdit = new Quill('#quill-editor-edit', {
                theme: 'snow',
                placeholder: '<?php echo get_phrase('what_is_on_your_mind'); ?>',
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

            // Focus styling
            quillEdit.on('selection-change', function(range, oldRange, source) {
                var container = $(quillEdit.container).parent();
                if (range) {
                    container.addClass('focused');
                } else {
                    container.removeClass('focused');
                }
            });

            // Real-time validation
            quillEdit.on('text-change', function() {
                var text = quillEdit.getText().trim();
                var hasMedia = quillEdit.root.getElementsByTagName('img').length > 0 || quillEdit.root.getElementsByTagName('iframe').length > 0;
                var container = $(quillEdit.container).parent();
                
                if (text !== '' || hasMedia) {
                    container.removeClass('is-invalid');
                }
            });
        }

        $('#edit-post-form').on('submit', function(e) {
            e.preventDefault();
            var form = $(this);
            var btn = form.find('button[type="submit"]');
            var originalText = btn.text();
            
            // Get content from Quill
            var bodyContent = quillEdit.root.innerHTML;
            var textContent = quillEdit.getText().trim();
            
            // Update hidden input
            $('#body_edit').val(bodyContent);
            
            // Basic Validation
            var hasMedia = quillEdit.root.getElementsByTagName('img').length > 0 || quillEdit.root.getElementsByTagName('iframe').length > 0;
            
            if (textContent === '' && !hasMedia) {
                if(typeof error_notify === 'function') {
                    error_notify('<?php echo get_phrase('content_required'); ?>');
                } else {
                    alert('<?php echo get_phrase('content_required'); ?>');
                }
                return;
            }

            // Loading state
            btn.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin"></i> <?php echo get_phrase('updating'); ?>...');

            var formData = new FormData(this);

            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                    btn.prop('disabled', false).text(originalText);
                    
                    if (response.status) {
                        Swal.fire({
                            title: '<?php echo get_phrase('success'); ?>',
                            text: response.message,
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(function() {
                            // Close modal and reload page to see changes
                            if (typeof jQuery !== 'undefined' && jQuery('#large-modal').length) {
                                 jQuery('#large-modal').modal('hide');
                            }
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: '<?php echo get_phrase('error'); ?>',
                            text: response.message,
                            icon: 'error'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    btn.prop('disabled', false).text(originalText);
                    var errorMessage = 'An error occurred';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    
                    if(typeof error_notify === 'function') {
                        error_notify(errorMessage);
                    } else {
                        alert(errorMessage);
                    }
                }
            });
        });
    });
})(jQuery);
</script>
