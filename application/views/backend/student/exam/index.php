<link rel="stylesheet" href="<?php echo base_url(); ?>assets/backend/css/responsive.css">
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/backend/css/curriculum.css">
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/backend/css/exam.css">

<!-- SweetAlert2 -->
<script type="text/javascript" src="<?php echo base_url(); ?>assets/backend/js/sweetalert.js"></script>

<!-- Mobile overlay -->
<div class="exam-editor-overlay" id="editorOverlay" onclick="closeEditor()"></div>

<div class="exam-container">
    <!-- Single Editor Card (Hidden by default) -->
    <div class="exam-editor-card" id="editorPanel" style="display: none;">
        <button type="button" class="editor-close-btn" onclick="closeEditor()">
            <i class="fas fa-xmark"></i>
        </button>
        <div class="exam-editor-header">
            <i class="fas fa-certificate" id="editorIcon"></i>
            <span id="editorTitle"><?php echo get_phrase('create_certification'); ?></span>
        </div>
        <div id="editorContainer"></div>
    </div>
    
    <!-- Certification List Panel (Always visible) -->
    <div class="exam-list-panel" id="outlinePanel">
        <div class="exam-list-header">
            <h4><i class="fas fa-file-signature fa-fw"></i> <?php echo get_phrase('certifications'); ?></h4>
            <!-- Les étudiants ne peuvent pas créer d'examens -->
            <!-- Pas de bouton "Add certification" pour les étudiants -->
        </div>
        <div class="exam-list-content exam_content" id="examsContainer">
            <?php include 'list.php'; ?>
        </div>
    </div>
</div>

<script>
// Helper function to check if mobile
function isMobileView() {
    return window.innerWidth <= 768;
}

// Show overlay on mobile
function showMobileOverlay() {
    if (isMobileView()) {
        $('#editorOverlay').addClass('active');
        $('body').css('overflow', 'hidden'); // Prevent body scroll
    }
}

// Hide overlay
function hideMobileOverlay() {
    $('#editorOverlay').removeClass('active');
    $('body').css('overflow', ''); // Restore body scroll
}

// Function to open new certification editor
function openNewExamEditor() {
    // Les étudiants ne peuvent pas créer d'examens
    Swal.fire({
        title: '<?php echo addslashes(get_phrase('access_denied')); ?>',
        text: '<?php echo addslashes(get_phrase('students_cannot_create_exams')); ?>',
        icon: 'warning',
        confirmButtonColor: '#4f46e5',
        confirmButtonText: '<?php echo addslashes(get_phrase('ok')); ?>'
    });
}

// Function to open certification editor for editing
function openExamEditor(examId) {
    // Les étudiants ne peuvent pas modifier d'examens
    Swal.fire({
        title: '<?php echo addslashes(get_phrase('access_denied')); ?>',
        text: '<?php echo addslashes(get_phrase('students_cannot_update_exams')); ?>',
        icon: 'warning',
        confirmButtonColor: '#4f46e5',
        confirmButtonText: '<?php echo addslashes(get_phrase('ok')); ?>'
    });
}

// Function to open question editor
function openQuestionEditor(examId, examName) {
    // Les étudiants ne peuvent pas gérer les questions d'examen
    Swal.fire({
        title: '<?php echo addslashes(get_phrase('access_denied')); ?>',
        text: '<?php echo addslashes(get_phrase('students_cannot_manage_exam_questions')); ?>',
        icon: 'warning',
        confirmButtonColor: '#4f46e5',
        confirmButtonText: '<?php echo addslashes(get_phrase('ok')); ?>'
    });
}

// Function to close editor
function closeEditor() {
    $('#editorPanel').hide();
    $('#editorContainer').empty();
    hideMobileOverlay();
}

// Legacy functions for compatibility
function closeExamEditor() { closeEditor(); }
function closeQuestionEditor() { closeEditor(); }

// Function to initialize exam form handlers
function initExamForm() {
    // Check if forms already have submit handlers
    // If they do, don't attach new ones to prevent double submission
    var createForm = $('#examCreateForm');
    var editForm = $('#examEditForm');
    
    // Only attach handlers if forms don't already have them
    // Check both data attribute AND jQuery events to be sure
    if (createForm.length > 0) {
        var hasHandler = createForm.data('hasSubmitHandler') === true;
        var hasEvents = $._data(createForm[0], 'events') && $._data(createForm[0], 'events').submit;
        
        if (!hasHandler && !hasEvents) {
            createForm.data('hasSubmitHandler', true);
            createForm.off('submit').on('submit', handleExamFormSubmit);
        }
    }
    
    if (editForm.length > 0) {
        var hasHandler = editForm.data('hasSubmitHandler') === true;
        var hasEvents = $._data(editForm[0], 'events') && $._data(editForm[0], 'events').submit;
        
        if (!hasHandler && !hasEvents) {
            editForm.data('hasSubmitHandler', true);
            editForm.off('submit').on('submit', handleExamFormSubmit);
        }
    }
}

// Function to handle exam form submission
function handleExamFormSubmit(e) {
    e.preventDefault();
    
    var form = $(this);
    var submitBtn = form.find('button[type="submit"]');
    var originalText = submitBtn.text();
    
    // Check if form has client-side validation
    if (typeof validateForm === 'function') {
        if (!validateForm()) {
            return false;
        }
    }
    
    submitBtn.prop('disabled', true).text('<?php echo get_phrase('saving'); ?>...');
    
    $.ajax({
        url: form.attr('action'),
        type: 'POST',
        data: form.serialize(),
        dataType: 'json',
        success: function(response) {
            submitBtn.prop('disabled', false).text(originalText);
            
            if (response.status === true) {
                closeExamEditor();
                showAllExams();
            }
        },
        error: function(xhr, status, error) {
            submitBtn.prop('disabled', false).text(originalText);
            console.error('Error saving exam:', error);
        }
    });
    
    return false;
}

// Function to confirm deletion
function confirmDelete(url, callback) {
    Swal.fire({
        title: '<?php echo addslashes(get_phrase('are_you_sure')); ?>',
        text: '<?php echo addslashes(get_phrase('you_will_not_be_able_to_revert_this')); ?>',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#4f46e5',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<?php echo addslashes(get_phrase('yes_delete_it')); ?>',
        cancelButtonText: '<?php echo addslashes(get_phrase('cancel')); ?>',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: url,
                type: 'POST',
                dataType: 'json',
                data: {
                    '<?php echo addslashes($this->security->get_csrf_token_name()); ?>': '<?php echo addslashes($this->security->get_csrf_hash()); ?>'
                },
                success: function(response) {
                    if (typeof callback === 'function') {
                        callback();
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Delete AJAX error:', status, error);
                }
            });
        }
    });
}

// Function to reload exams list
var showAllExams = function () {
    var url = '<?php echo route('exam/list'); ?>';
    $.ajax({
        type: 'GET',
        url: url,
        success: function(response) {
            $('.exam_content').html(response);
            // Reinitialize event handlers for exam items
            initExamItemHandlers();
        },
        error: function(xhr, status, error) {
            console.error('Erreur AJAX showAllExams:', status, error, xhr.responseText);
        }
    });
}

// Function to initialize exam item handlers
function initExamItemHandlers() {
    // Les étudiants ne peuvent pas cliquer sur les examens pour les éditer
    // Pas de gestionnaire de clic pour les éléments d'examen
    
    // Les étudiants ne peuvent pas éditer d'examens
    // Pas de gestionnaire pour les boutons d'édition
    
    // Les étudiants ne peuvent pas supprimer d'examens
    // Pas de gestionnaire pour les boutons de suppression
}

$(document).ready(function() {
    showAllExams();
});
</script>