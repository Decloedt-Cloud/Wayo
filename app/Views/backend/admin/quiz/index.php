<link rel="stylesheet" href="<?php echo base_url(); ?>assets/backend/css/responsive.min.css">

<style>
:root {
    --quiz-primary: #6366f1;
    --quiz-primary-light: #eef2ff;
    --quiz-success: #059669;
    --quiz-dark: #1e293b;
    --quiz-gray: #64748b;
    --quiz-light: #f8fafc;
    --quiz-border: #e2e8f0;
}

.quiz-header { background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); border-radius: 16px; padding: 1.5rem 2rem; margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; box-shadow: 0 4px 20px rgba(99, 102, 241, 0.3); }
.quiz-header-left { display: flex; align-items: center; gap: 1rem; }
.quiz-header-icon { width: 50px; height: 50px; border-radius: 12px; background: rgba(255,255,255,0.15); backdrop-filter: blur(10px); display: flex; align-items: center; justify-content: center; font-size: 1.5rem; color: white; }
.quiz-header-text h4 { margin: 0; color: white; font-size: 1.35rem; font-weight: 700; }
.quiz-header-text p { margin: 0.25rem 0 0; color: rgba(255,255,255,0.7); font-size: 0.85rem; }

.quiz-filter-section { padding: 1.5rem; background: white; border-radius: 16px; margin-bottom: 1.5rem; border: 1px solid var(--quiz-border); box-shadow: 0 2px 12px rgba(0,0,0,0.04); }
.quiz-filter-grid { display: grid; grid-template-columns: 1fr 1fr 120px; gap: 1.25rem; align-items: end; }
.quiz-filter-group { display: flex; flex-direction: column; gap: 0.5rem; }
.quiz-filter-label { display: flex; align-items: center; gap: 0.5rem; font-size: 0.8125rem; font-weight: 600; color: var(--quiz-dark); text-transform: uppercase; letter-spacing: 0.5px; }
.quiz-filter-label i { color: var(--quiz-primary); font-size: 1rem; }
.quiz-filter-input { width: 100%; padding: 0.75rem 1rem; border: 2px solid var(--quiz-border); border-radius: 10px; font-size: 0.9375rem; background: var(--quiz-light); color: var(--quiz-dark); transition: all 0.2s; cursor: pointer; }
.quiz-filter-input:focus { outline: none; border-color: var(--quiz-primary); background: white; box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1); }
.quiz-filter-btn { display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 0.75rem 1.5rem; background: linear-gradient(135deg, var(--quiz-primary), #8b5cf6); color: white; border: none; border-radius: 10px; font-size: 0.9375rem; font-weight: 600; cursor: pointer; transition: all 0.2s; height: fit-content; }
.quiz-filter-btn:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3); }

.quiz-content-card { background: white; border-radius: 16px; box-shadow: 0 2px 12px rgba(0,0,0,0.08); border: 1px solid var(--quiz-border); overflow: hidden; }
.quiz-content-header { background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); padding: 1rem 1.5rem; border-bottom: 1px solid var(--quiz-border); display: flex; justify-content: space-between; align-items: center; }
.quiz-content-title { display: flex; align-items: center; gap: 0.5rem; font-weight: 600; color: var(--quiz-dark); font-size: 0.95rem; }
.quiz-content-title i { color: var(--quiz-primary); }
.quiz-content-body { padding: 1.5rem; }

.quiz-empty { display: flex; flex-direction: column; align-items: center; padding: 3rem 1.5rem; text-align: center; }
.quiz-empty-icon { width: 80px; height: 80px; border-radius: 50%; background: linear-gradient(135deg, var(--quiz-primary), #8b5cf6); display: flex; align-items: center; justify-content: center; margin-bottom: 1.25rem; }
.quiz-empty-icon i { font-size: 2rem; color: white; }
.quiz-empty h3 { margin: 0 0 0.5rem; font-size: 1.125rem; color: var(--quiz-dark); }
.quiz-empty p { margin: 0; color: var(--quiz-gray); font-size: 0.875rem; }

.quiz-loading { display: flex; align-items: center; justify-content: center; padding: 3rem; color: var(--quiz-gray); }
.quiz-loading i { font-size: 2rem; animation: quiz-spin 1s linear infinite; margin-right: 0.75rem; }
@keyframes quiz-spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }

/* Modal Premium */
.quiz-modal .modal-content { border-radius: 16px; border: none; overflow: hidden; }
.quiz-modal .modal-header { background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); color: white; border: none; padding: 1.25rem 1.5rem; }
.quiz-modal .modal-title { font-weight: 700; font-size: 1.125rem; }
.quiz-modal .modal-header .close { color: white; opacity: 0.8; text-shadow: none; }
.quiz-modal .modal-header .close:hover { opacity: 1; }
.quiz-modal .modal-body { padding: 1.5rem; }
.quiz-modal .modal-footer { border-top: 1px solid var(--quiz-border); padding: 1rem 1.5rem; }
.quiz-modal .btn-close-modal { background: var(--quiz-light); color: var(--quiz-gray); border: 2px solid var(--quiz-border); border-radius: 10px; padding: 0.625rem 1.25rem; font-weight: 600; transition: all 0.2s; }
.quiz-modal .btn-close-modal:hover { background: var(--quiz-border); color: var(--quiz-dark); }

@media (max-width: 768px) {
    .quiz-header { flex-direction: column; text-align: center; }
    .quiz-header-left { flex-direction: column; }
    .quiz-filter-grid { grid-template-columns: 1fr; }
}
</style>

<!-- Header -->
<div class="quiz-header">
    <div class="quiz-header-left">
        <div class="quiz-header-icon">
            <i class="fas fa-poll"></i>
        </div>
        <div class="quiz-header-text">
            <h4><?php echo get_phrase('quiz_result'); ?></h4>
            <p><?php echo get_phrase('view_student_quiz_results'); ?></p>
        </div>
    </div>
</div>

<!-- Filter Section -->
<div class="quiz-filter-section">
    <div class="quiz-filter-grid">
        <div class="quiz-filter-group">
            <label class="quiz-filter-label">
                <i class="mdi mdi-google-classroom"></i>
                <span><?php echo get_phrase('class'); ?></span>
            </label>
            <select name="class" id="class_id_cours" class="quiz-filter-input" required onchange="classWiseCours(this.value)">
                <option value=""><?php echo get_phrase('select_a_class'); ?></option>
                <?php
                $classes = db()->table('classes')->where('school_id', $school_id)->get()->getResultArray();
                foreach($classes as $class){
                    $total_student = db()->table('enrols')
                        ->where('class_id', $class['id'])
                        ->where('school_id', $school_id)
                        ->get()
                        ->getResultArray();
                ?>
                <option value="<?php echo $class['id']; ?>">
                    <?php echo $class['name']; ?> (<?php echo count($total_student); ?>)
                </option>
                <?php } ?>
            </select>
        </div>

        <div class="quiz-filter-group">
            <label class="quiz-filter-label">
                <i class="mdi mdi-help-circle-outline"></i>
                <span><?php echo get_phrase('quiz'); ?></span>
            </label>
            <select name="quiz" id="quiz_id" class="quiz-filter-input" required>
                <option value=""><?php echo get_phrase('select_quiz'); ?></option>
            </select>
        </div>

        <div class="quiz-filter-group">
            <button class="quiz-filter-btn" onclick="filter_attendance()">
                <i class="mdi mdi-filter-outline"></i>
                <span><?php echo get_phrase('filter'); ?></span>
            </button>
        </div>
    </div>
</div>

<!-- Content Card -->
<div class="quiz-content-card">
    <div class="quiz-content-header">
        <span class="quiz-content-title">
            <i class="mdi mdi-clipboard-check-outline"></i>
            <?php echo get_phrase('quiz_results_list'); ?>
        </span>
    </div>
    <div class="quiz-content-body quiz_content">
        <div class="quiz-empty">
            <div class="quiz-empty-icon">
                <i class="mdi mdi-clipboard-search-outline"></i>
            </div>
            <h3><?php echo get_phrase('no_data_found'); ?></h3>
            <p><?php echo get_phrase('select_class_and_quiz_to_view_results'); ?></p>
        </div>
    </div>
</div>

<!-- Modal Premium -->
<div class="modal fade quiz-modal" id="quizResultModal" tabindex="-1" role="dialog" aria-labelledby="quizResultModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="quizResultModalLabel">
                    <i class="mdi mdi-clipboard-check"></i> <?php echo get_phrase('quiz_result'); ?>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="closeModal()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="quiz-result-content">
                <div class="quiz-loading">
                    <i class="mdi mdi-loading"></i>
                    <span><?php echo get_phrase('loading'); ?>...</span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-close-modal" data-dismiss="modal" onclick="closeModal()">
                    <i class="mdi mdi-close"></i> <?php echo get_phrase('close'); ?>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
$('document').ready(function(){
    $('select.select2:not(.normal)').each(function () { $(this).select2({ dropdownParent: '#right-modal' }); });
});

function classWiseCours(classId) {
    $.ajax({
        url: "<?php echo route('quiz/list/'); ?>"/"+classId,
        success: function(response){
            $('#quiz_id').html(response); 
        }
    });
}

function filter_attendance(){
    var class_id = $('#class_id_cours').val();
    var cours_id = $('#cours_id').val();
    var quiz_id = $('#quiz_id').val();
    var csrfName = $('input[name="<?= csrf_token(); ?>"]').attr('name');
    var csrfHash = $('input[name="<?= csrf_token(); ?>"]').val();

    if(class_id != "" && quiz_id != "" ){
        $('.quiz_content').html('<div class="quiz-loading"><i class="mdi mdi-loading"></i> <?php echo get_phrase('loading'); ?>...</div>');
        $.ajax({
            type: 'POST',
            url: '<?php echo route('quiz_result/list') ?>',
            data: {class_id : class_id, cours_id : cours_id, quiz_id : quiz_id ,[csrfName]: csrfHash},
            dataType: 'json',
            success: function(response){
                $('.quiz_content').html(response.status);
                var newCsrfName = response.csrf.csrfName;
                var newCsrfHash = response.csrf.csrfHash;
                $('input[name="' + newCsrfName + '"]').val(newCsrfHash);
            }
        });
    }else{
        toastr.error('<?php echo get_phrase('please_select_in_all_fields'); ?>');
    }
}

function openQuizResultModal(quiz_id, user_id) {
    $('#quizResultModal').modal('show');
    $('#quiz-result-content').html('<div class="quiz-loading"><i class="mdi mdi-loading"></i> <?php echo get_phrase('loading'); ?>...</div>');
    
    var csrfName = $('input[name="<?= csrf_token(); ?>"]').attr('name');
    var csrfHash = $('input[name="<?= csrf_token(); ?>"]').val();
    
    $.ajax({
        url: '<?php echo site_url('addons/lessons/check_result_pop_up'); ?>',
        type: 'post',
        data: { quiz_id: quiz_id, user_id: user_id, [csrfName]: csrfHash },
        dataType: 'json',
        success: function(response) {
            $('#quiz-result-content').html(response.status);
            var newCsrfName = response.csrf.csrfName;
            var newCsrfHash = response.csrf.csrfHash;
            $('input[name="' + newCsrfName + '"]').val(newCsrfHash);
        },
        error: function() {
            $('#quiz-result-content').html('<div class="quiz-empty"><div class="quiz-empty-icon"><i class="mdi mdi-alert"></i></div><h3><?php echo get_phrase('error'); ?></h3><p><?php echo get_phrase('error_loading_quiz_results'); ?></p></div>');
        }
    });
}

function enableNextButton(quizID) {
    $('#next-btn-'+quizID).prop('disabled', false);
}

function closeModal() {
    $('#quizResultModal').modal('hide');
}
</script>
