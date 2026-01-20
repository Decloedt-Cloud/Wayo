<?php
$school_id = school_id();
$class_name = $this->db->get_where('classes', array('id' => $class_id))->row('name');
$cours_name = $this->db->get_where('course', array('id' => $cours_id))->row('title');
$enrols = $this->db->get_where('enrols', array('class_id' => $class_id))->result_array();
$total_students = count($enrols);
?>

<style>
:root {
    --quiz-primary: #6366f1;
    --quiz-primary-rgb: 99, 102, 241;
    --quiz-success: #10b981;
    --quiz-danger: #ef4444;
    --quiz-warning: #f59e0b;
    --quiz-dark: #1e293b;
    --quiz-gray: #64748b;
    --quiz-light: #f8fafc;
    --quiz-border: #e2e8f0;
    --quiz-white: #ffffff;
}

.quiz-info-bar { display: flex; gap: 1rem; margin-bottom: 1.5rem; flex-wrap: wrap; }
.quiz-info-chip { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; background: linear-gradient(135deg, rgba(var(--quiz-primary-rgb), 0.1), rgba(var(--quiz-primary-rgb), 0.05)); border-radius: 20px; font-size: 0.8125rem; color: var(--quiz-dark); border: 1px solid rgba(var(--quiz-primary-rgb), 0.2); }
.quiz-info-chip i { color: var(--quiz-primary); }
.quiz-info-chip strong { font-weight: 600; }

.quiz-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem; margin-bottom: 1.5rem; }
.quiz-stat-card { background: var(--quiz-white); border-radius: 12px; padding: 1rem; border: 1px solid var(--quiz-border); display: flex; align-items: center; gap: 0.75rem; }
.quiz-stat-icon { width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; }
.quiz-stat-icon.primary { background: rgba(var(--quiz-primary-rgb), 0.1); color: var(--quiz-primary); }
.quiz-stat-data { display: flex; flex-direction: column; }
.quiz-stat-value { font-size: 1.25rem; font-weight: 700; color: var(--quiz-dark); }
.quiz-stat-label { font-size: 0.75rem; color: var(--quiz-gray); }

.quiz-list-header { display: grid; grid-template-columns: 2fr 1fr 100px; gap: 1rem; padding: 0.75rem 1rem; background: var(--quiz-light); border-radius: 10px; margin-bottom: 0.75rem; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; color: var(--quiz-gray); letter-spacing: 0.5px; }
.quiz-list-item { display: grid; grid-template-columns: 2fr 1fr 100px; gap: 1rem; padding: 1rem; background: var(--quiz-white); border: 1px solid var(--quiz-border); border-radius: 12px; margin-bottom: 0.5rem; transition: all 0.2s; align-items: center; }
.quiz-list-item:hover { border-color: var(--quiz-primary); box-shadow: 0 4px 12px rgba(var(--quiz-primary-rgb), 0.1); }

.quiz-student-info { display: flex; align-items: center; gap: 0.75rem; }
.quiz-student-avatar { width: 40px; height: 40px; border-radius: 10px; background: linear-gradient(135deg, var(--quiz-primary), #8b5cf6); display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 0.875rem; }
.quiz-student-name { font-weight: 600; color: var(--quiz-dark); font-size: 0.9375rem; }

.quiz-result-badge { display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.375rem 0.75rem; border-radius: 20px; font-size: 0.8125rem; font-weight: 600; }
.quiz-result-badge.success { background: rgba(16, 185, 129, 0.1); color: var(--quiz-success); }
.quiz-result-badge.warning { background: rgba(245, 158, 11, 0.1); color: var(--quiz-warning); }
.quiz-result-badge.pending { background: var(--quiz-light); color: var(--quiz-gray); }

.quiz-action-btn { width: 36px; height: 36px; border-radius: 10px; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s; background: rgba(var(--quiz-primary-rgb), 0.1); color: var(--quiz-primary); }
.quiz-action-btn:hover { background: var(--quiz-primary); color: white; transform: translateY(-2px); }
.quiz-action-btn:disabled { opacity: 0.4; cursor: not-allowed; transform: none; }
.quiz-action-btn:disabled:hover { background: rgba(var(--quiz-primary-rgb), 0.1); color: var(--quiz-primary); }

@media (max-width: 768px) {
    .quiz-list-header { display: none; }
    .quiz-list-item { grid-template-columns: 1fr; gap: 0.75rem; }
}
</style>

<!-- Info Bar -->
<div class="quiz-info-bar">
    <div class="quiz-info-chip">
        <i class="mdi mdi-google-classroom"></i>
        <span><?php echo get_phrase('class'); ?>:</span>
        <strong><?php echo $class_name; ?></strong>
    </div>
    <div class="quiz-info-chip">
        <i class="mdi mdi-book-open-variant"></i>
        <span><?php echo get_phrase('cours'); ?>:</span>
        <strong><?php echo $cours_name; ?></strong>
    </div>
</div>

<!-- Stats -->
<div class="quiz-stats">
    <div class="quiz-stat-card">
        <div class="quiz-stat-icon primary">
            <i class="mdi mdi-account-group"></i>
        </div>
        <div class="quiz-stat-data">
            <span class="quiz-stat-value"><?php echo $total_students; ?></span>
            <span class="quiz-stat-label"><?php echo get_phrase('total_students'); ?></span>
        </div>
    </div>
</div>

<!-- List Header -->
<div class="quiz-list-header">
    <div><?php echo get_phrase('student_name'); ?></div>
    <div><?php echo get_phrase('quiz_result'); ?></div>
    <div><?php echo get_phrase('action'); ?></div>
</div>

<!-- List Items -->
<?php foreach ($enrols as $enrol):
    $student = $this->db->get_where('students', array('id' => $enrol['student_id']))->row_array();
    $student_name = $this->user_model->get_user_details($student['user_id'], 'name');
    $quiz_lists = $this->crud_model->get_quiz($quiz_id, $student['user_id'])->row_array();
    $initials = strtoupper(substr($student_name, 0, 2));
?>
<div class="quiz-list-item">
    <div class="quiz-student-info">
        <div class="quiz-student-avatar"><?php echo $initials; ?></div>
        <span class="quiz-student-name"><?php echo $student_name; ?></span>
    </div>
    
    <div>
        <?php if($quiz_lists): 
            $percent = ($quiz_lists['total_responses'] > 0) ? round(($quiz_lists['correct_responses'] / $quiz_lists['total_responses']) * 100) : 0;
            $badge_class = $percent >= 50 ? 'success' : 'warning';
        ?>
        <span class="quiz-result-badge <?php echo $badge_class; ?>">
            <i class="mdi mdi-check-circle"></i>
            <?php echo $quiz_lists['correct_responses']; ?>/<?php echo $quiz_lists['total_responses']; ?>
        </span>
        <?php else: ?>
        <span class="quiz-result-badge pending">
            <i class="mdi mdi-clock-outline"></i>
            <?php echo get_phrase('Not_realized'); ?>
        </span>
        <?php endif; ?>
    </div>
    
    <div>
        <button type="button" class="quiz-action-btn" 
                title="<?php echo get_phrase('view_details'); ?>"
                <?php if (!$quiz_lists): ?>disabled<?php else: ?>
                onclick="openQuizResultModal(<?php echo $quiz_id; ?>, <?php echo $student['user_id']; ?>);"
                <?php endif; ?>>
            <i class="mdi mdi-eye-outline"></i>
        </button>
    </div>
</div>
<?php endforeach; ?>

<input type="hidden" name="lesson_id" value="<?php echo $quiz_id; ?>">
