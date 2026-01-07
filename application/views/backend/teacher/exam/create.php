<div class="exam-editor-form">
    <form method="POST" class="d-block" action="<?php echo route('exam/create'); ?>" id="examCreateForm">
        <!-- Certification Title Section -->
        <div class="editor-title-section">
            <input type="text" class="editor-title-input" id="exam_name" name="exam_name" placeholder="<?php echo get_phrase('certification_title'); ?>" maxlength="100" required>
            <span class="title-char-counter" id="examNameCounter">0/100</span>
        </div>
        
        <!-- Certification Details -->
        <div class="exam-details-section">
            <div class="form-row">
                <div class="form-group">
                    <label for="starting_date">
                        <i class="fas fa-calendar"></i> <?php echo get_phrase('date_and_time'); ?><span class="required"> * </span>
                    </label>
                    <input type="datetime-local" class="form-control" id="starting_date" name="starting_date" value="<?php echo date('Y-m-d\TH:i'); ?>" required>
                    <small id="date_help" class="form-text text-muted"><?php echo get_phrase('select_certification_date_and_time'); ?></small>
                </div>
                
                <div class="form-group">
                    <label for="modal_class_id">
                        <i class="fas fa-chalkboard"></i> <?php echo get_phrase('class'); ?><span class="required"> * </span>
                    </label>
                    <select class="form-control" id="modal_class_id" name="class_id" required>
                        <option value=""><?php echo get_phrase('select_class'); ?></option>
                        <?php 
                        $school_id = school_id();
                        
                        // Get teacher ID and permitted classes
                        $user_id = $this->session->userdata('user_id');
                        $teacher = $this->db->get_where('teachers', ['user_id' => $user_id])->row_array();
                        $teacher_id = $teacher['id'] ?? null;
                        
                        // Get permitted class IDs from teacher_permissions where attendance = 1
                        $permitted_class_ids = [];
                        if ($teacher_id) {
                            $this->db->select('class_id');
                            $this->db->from('teacher_permissions');
                            $this->db->where('teacher_id', $teacher_id);
                            $this->db->where('attendance', 1);
                            $permitted_classes = $this->db->get()->result_array();
                            $permitted_class_ids = array_column($permitted_classes, 'class_id');
                        }
                        
                        // Get classes with filter for permitted classes
                        $this->db->where('school_id', $school_id);
                        if (!empty($permitted_class_ids)) {
                            $this->db->where_in('id', $permitted_class_ids);
                        } else {
                            // If teacher has no permitted classes, show none
                            $this->db->where('1', '0');
                        }
                        $classes = $this->crud_model->get_classes()->result_array();
                        
                        if (empty($classes)) {
                            echo '<option value="">' . get_phrase('no_classes_found') . '</option>';
                        } else {
                            foreach ($classes as $class): 
                        ?>
                            <option value="<?php echo html_escape($class['id']); ?>">
                                <?php echo html_escape($class['name']); ?>
                            </option>
                        <?php 
                            endforeach; 
                        }
                        ?>
                    </select>
                    <small id="class_help" class="form-text text-muted"><?php echo get_phrase('select_class_for_certification'); ?></small>
                </div>
            </div>
        </div>
        
        <!-- Form Actions -->
        <div class="form-actions">
            <button class="btn btn-primary btn-save-exam" type="submit">
                <i class="fas fa-plus"></i> <?php echo get_phrase('create_certification'); ?>
            </button>
            <button type="button" class="btn btn-outline-secondary btn-cancel-exam" onclick="closeExamEditor()">
                <i class="fas fa-xmark"></i> <?php echo get_phrase('cancel'); ?>
            </button>
        </div>
    </form>
</div>

<style>
.exam-editor-form {
    padding: 20px;
}

.editor-title-section {
    margin-bottom: 25px;
}

.editor-title-input {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    font-size: 18px;
    font-weight: 600;
    color: #333;
    transition: all 0.2s ease;
    background: #f8f9fa;
}

.editor-title-input:focus {
    outline: none;
    border-color: #4f46e5;
    background: #fff;
    box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
}

.editor-title-input::placeholder {
    color: #6c757d;
    font-weight: normal;
}

.title-char-counter {
    display: block;
    text-align: right;
    font-size: 12px;
    color: #6c757d;
    margin-top: 5px;
}

.exam-details-section {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 25px;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.form-group {
    margin-bottom: 0;
}

.form-group label {
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 500;
    color: #333;
    margin-bottom: 8px;
}

.form-group label i {
    color: #6c757d;
}

.form-group .form-control {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid #dee2e6;
    border-radius: 6px;
    font-size: 14px;
    transition: all 0.2s ease;
}

.form-group .form-control:focus {
    border-color: #4f46e5;
    box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
}

.form-group .form-text {
    display: block;
    margin-top: 5px;
    font-size: 12px;
    color: #6c757d;
}

.required {
    color: #dc3545;
}

.form-actions {
    display: flex;
    gap: 10px;
    justify-content: flex-end;
    padding-top: 20px;
    border-top: 1px solid #e9ecef;
}

.btn-save-exam, .btn-cancel-exam {
    padding: 10px 20px;
    font-size: 14px;
    font-weight: 500;
    border-radius: 8px;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s ease;
}

.btn-save-exam {
    background: #4f46e5;
    border: 1px solid #4f46e5;
    color: #fff;
}

.btn-save-exam:hover {
    background: #4338ca;
    border-color: #3730a3;
    transform: translateY(-1px);
}

.btn-cancel-exam {
    background: transparent;
    border: 1px solid #6c757d;
    color: #6c757d;
}

.btn-cancel-exam:hover {
    background: #6c757d;
    color: #fff;
    transform: translateY(-1px);
}

/* Validation styles */
.is-invalid {
    border-color: #dc3545 !important;
}

.is-invalid:focus {
    box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.1) !important;
}

.text-danger {
    color: #dc3545 !important;
}
</style>

<script>
// Mark form as having a submit handler IMMEDIATELY to prevent initExamForm() from adding another one
$('#examCreateForm').data('hasSubmitHandler', true);

$(document).ready(function() {
    // Character counter for exam title
    const examNameInput = $('#exam_name');
    const examNameCounter = $('#examNameCounter');
    
    examNameInput.on('input', function() {
        const length = $(this).val().length;
        examNameCounter.text(length + '/100');
        
        if (length > 100) {
            examNameCounter.addClass('text-danger');
        } else {
            examNameCounter.removeClass('text-danger');
        }
    });
    
    // Initialize character counter
    examNameInput.trigger('input');
    
    // Form validation
    const nameRegex = /^[a-zA-Z0-9\s\-_]+$/;
    
    function validateForm() {
        let isValid = true;
        
        // Validate exam name
        const examName = examNameInput.val().trim();
        if (examName.length < 3 || examName.length > 100) {
            showFieldError('exam_name', '<?php echo addslashes(get_phrase('exam_name_must_be_between_3_and_100_characters')); ?>');
            isValid = false;
        } else if (!nameRegex.test(examName)) {
            showFieldError('exam_name', '<?php echo addslashes(get_phrase('exam_name_can_only_contain_letters_numbers_spaces_hyphens_and_underscores')); ?>');
            isValid = false;
        } else {
            clearFieldError('exam_name');
        }
        
        // Validate date
        const dateInput = $('#starting_date');
        if (!dateInput.val()) {
            showFieldError('starting_date', '<?php echo addslashes(get_phrase('please_select_exam_date_and_time')); ?>');
            isValid = false;
        } else {
            clearFieldError('starting_date');
        }
        
        // Validate class
        const classInput = $('#modal_class_id');
        if (!classInput.val()) {
            showFieldError('modal_class_id', '<?php echo addslashes(get_phrase('please_select_a_class')); ?>');
            isValid = false;
        } else {
            clearFieldError('modal_class_id');
        }
        
        return isValid;
    }
    
    function showFieldError(fieldId, message) {
        const field = $('#' + fieldId);
        const helpElement = $('#' + fieldId.replace('_', '') + '_help') || field.next('.form-text');
        
        field.addClass('is-invalid');
        if (helpElement.length) {
            helpElement.addClass('text-danger').text(message);
        }
    }
    
    function clearFieldError(fieldId) {
        const field = $('#' + fieldId);
        const helpElement = $('#' + fieldId.replace('_', '') + '_help') || field.next('.form-text');
        
        field.removeClass('is-invalid');
        if (helpElement.length) {
            helpElement.removeClass('text-danger').text('');
        }
    }
    
    // Real-time validation
    examNameInput.on('blur', function() {
        const value = $(this).val().trim();
        if (value.length < 3 || value.length > 100) {
            showFieldError('exam_name', '<?php echo addslashes(get_phrase('exam_name_must_be_between_3_and_100_characters')); ?>');
        } else if (!nameRegex.test(value)) {
            showFieldError('exam_name', '<?php echo addslashes(get_phrase('exam_name_can_only_contain_letters_numbers_spaces_hyphens_and_underscores')); ?>');
        } else {
            clearFieldError('exam_name');
        }
    });
    
    $('#starting_date').on('change', function() {
        if ($(this).val()) {
            clearFieldError('starting_date');
        }
    });
    
    $('#modal_class_id').on('change', function() {
        if ($(this).val()) {
            clearFieldError('modal_class_id');
        }
    });
    
    // Form submission
    let isSubmitting = false;
    
    $('#examCreateForm').on('submit', function(e) {
        e.preventDefault();
        
        if (isSubmitting) return;
        
        // Validate form
        if (!validateForm()) {
            return;
        }
        
        isSubmitting = true;
        const submitBtn = $(this).find('.btn-save-exam');
        const originalText = submitBtn.html();
        
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> <?php echo addslashes(get_phrase('creating')); ?>...');
        
        const formData = {
            exam_name: examNameInput.val().trim(),
            starting_date: $('#starting_date').val(),
            class_id: $('#modal_class_id').val(),
            '<?php echo addslashes($this->security->get_csrf_token_name()); ?>': '<?php echo addslashes($this->security->get_csrf_hash()); ?>'
        };
        
        $.ajax({
            url: '<?php echo route('exam/create'); ?>',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                isSubmitting = false;
                submitBtn.prop('disabled', false).html(originalText);
                
                if (response.status === true) {
                    // Close editor and refresh exams list
                    if (typeof closeExamEditor === 'function') {
                        closeExamEditor();
                    }
                    
                    if (typeof showAllExams === 'function') {
                        showAllExams();
                    }
                }
            },
            error: function(xhr, status, error) {
                isSubmitting = false;
                submitBtn.prop('disabled', false).html(originalText);
                console.error('Error creating exam:', error);
            }
        });
    });
});
</script>