<style>
/* Modern Form Design */
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
    /* overflow: hidden; Removed to prevent tooltip clipping */
    border: 1px solid #e2e8f0;
    background: white;
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

/* Fix Quill Tooltip Positioning (No visual style changes) */
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
        <form action="<?php echo site_url('wall/create_post_action/' . $wall_id); ?>" method="post" enctype="multipart/form-data" id="create-post-form">
            <div class="modern-form-wrapper">
                <!-- Title Field -->
                <div class="modern-form-group">
                    <label for="title" class="modern-label">
                        <i class="fas fa-heading"></i> <?php echo get_phrase('title'); ?>
                    </label>
                    <input type="text" class="modern-input" id="title" name="title" 
                           placeholder="<?php echo get_phrase('enter_an_engaging_title'); ?>...">
                </div>

                <!-- Content Field -->
                <div class="modern-form-group">
                    <label for="body" class="modern-label">
                        <i class="fas fa-align-left"></i> <?php echo get_phrase('content'); ?> <span class="text-danger">*</span>
                    </label>
                    <div class="editor-container">
                        <div id="quill-editor"></div>
                    </div>
                    <input type="hidden" name="body" id="body">
                </div>
                
                <input type="hidden" name="post_type" value="post">

                <!-- Actions -->
                <div class="modern-actions">
                    <button type="button" class="btn-modern-cancel" data-bs-dismiss="modal">
                        <?php echo get_phrase('cancel'); ?>
                    </button>
                    <button type="submit" class="btn-modern-submit">
                        <i class="mdi mdi-send"></i> <?php echo get_phrase('publish_post'); ?>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
(function($) {
    if (!$) {
        console.error('jQuery missing in create_post modal');
        return;
    }
    
    // Initialize Quill
    var quill;
    
    $(document).ready(function() {
        // Initialize Quill editor
        if (document.getElementById('quill-editor')) {
            quill = new Quill('#quill-editor', {
                theme: 'snow',
                placeholder: '<?php echo get_phrase('what_is_on_your_mind'); ?>?',
                modules: {
                    toolbar: [
                        ['bold', 'italic', 'underline', 'strike'],        // toggled buttons
                        ['blockquote', 'code-block'],

                        [{ 'header': 1 }, { 'header': 2 }],               // custom button values
                        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                        [{ 'script': 'sub'}, { 'script': 'super' }],      // superscript/subscript
                        [{ 'indent': '-1'}, { 'indent': '+1' }],          // outdent/indent
                        [{ 'direction': 'rtl' }],                         // text direction

                        [{ 'size': ['small', false, 'large', 'huge'] }],  // custom dropdown
                        [{ 'header': [1, 2, 3, 4, 5, 6, false] }],

                        [{ 'color': [] }, { 'background': [] }],          // dropdown with defaults from theme
                        [{ 'font': [] }],
                        [{ 'align': [] }],

                        ['clean'],                                         // remove formatting button
                        ['link', 'image', 'video']                         // link and image, video
                    ]
                }
            });
        }

        $('#create-post-form').on('submit', function(e) {
            e.preventDefault();
            var form = $(this);
            var btn = form.find('button[type="submit"]');
            var originalText = btn.text();
            
            // Get content from Quill
            var bodyContent = quill.root.innerHTML;
            var textContent = quill.getText().trim();
            
            // Update hidden input
            $('#body').val(bodyContent);
            
            // Basic Validation
            if (textContent === '') {
                if(typeof error_notify === 'function') {
                    error_notify('<?php echo get_phrase('content_required'); ?>');
                } else {
                    alert('<?php echo get_phrase('content_required'); ?>');
                }
                return;
            }

            // Loading state
            btn.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin"></i> <?php echo get_phrase('publishing'); ?>...');

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
                        if(typeof success_notify === 'function') {
                            success_notify(response.message);
                        } else {
                            alert(response.message);
                        }
                        
                        // Close modal
                        $('#large-modal').modal('hide');
                        
                        // Reload posts if function exists
                        if (typeof loadPosts === 'function') {
                            loadPosts(1, 'all');
                        } else {
                            location.reload();
                        }
                    } else {
                        if(typeof error_notify === 'function') {
                            error_notify(response.message);
                        } else {
                            alert(response.message);
                        }
                    }
                },
                error: function() {
                    btn.prop('disabled', false).text(originalText);
                    if(typeof error_notify === 'function') {
                        error_notify('<?php echo get_phrase('an_error_occurred'); ?>');
                    } else {
                        alert('<?php echo get_phrase('an_error_occurred'); ?>');
                    }
                }
            });
        });
    });
})(typeof jQuery !== 'undefined' ? jQuery : null);
</script>