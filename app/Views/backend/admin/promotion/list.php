<?php if (isset($enrolments) && count($enrolments) > 0): ?>

<style>
:root {
    --promo-primary: #6366f1;
    --promo-primary-rgb: 99, 102, 241;
    --promo-success: #10b981;
    --promo-success-rgb: 16, 185, 129;
    --promo-warning: #f59e0b;
    --promo-dark: #1e293b;
    --promo-gray: #64748b;
    --promo-light: #f8fafc;
    --promo-border: #e2e8f0;
    --promo-white: #ffffff;
}

.promo-info-bar { display: flex; gap: 1rem; flex-wrap: wrap; padding: 1.5rem; background: linear-gradient(135deg, rgba(var(--promo-primary-rgb), 0.05), rgba(var(--promo-primary-rgb), 0.1)); border-bottom: 1px solid var(--promo-border); }
.promo-info-chip { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.625rem 1rem; background: var(--promo-white); border-radius: 12px; font-size: 0.875rem; color: var(--promo-dark); border: 1px solid var(--promo-border); box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
.promo-info-chip i { color: var(--promo-primary); font-size: 1.125rem; }
.promo-info-chip strong { font-weight: 600; color: var(--promo-primary); }
.promo-arrow { display: flex; align-items: center; color: var(--promo-gray); }

.promo-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; padding: 1.5rem; }
.promo-stat-card { background: var(--promo-white); border-radius: 12px; padding: 1rem; display: flex; align-items: center; gap: 0.75rem; border: 1px solid var(--promo-border); }
.promo-stat-icon { width: 44px; height: 44px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; }
.promo-stat-icon.primary { background: rgba(var(--promo-primary-rgb), 0.1); color: var(--promo-primary); }
.promo-stat-data { display: flex; flex-direction: column; }
.promo-stat-value { font-size: 1.5rem; font-weight: 700; color: var(--promo-dark); }
.promo-stat-label { font-size: 0.75rem; color: var(--promo-gray); }

.promo-list-header { display: grid; grid-template-columns: 70px 2fr 1fr 200px; gap: 1rem; padding: 0.875rem 1.5rem; background: var(--promo-light); border-bottom: 2px solid var(--promo-border); font-size: 0.75rem; font-weight: 700; text-transform: uppercase; color: var(--promo-gray); letter-spacing: 0.5px; }
.promo-list-item { display: grid; grid-template-columns: 70px 2fr 1fr 200px; gap: 1rem; padding: 1rem 1.5rem; border-bottom: 1px solid var(--promo-border); align-items: center; transition: all 0.2s; }
.promo-list-item:hover { background: rgba(var(--promo-primary-rgb), 0.02); }
.promo-list-item:last-child { border-bottom: none; }

.promo-avatar { width: 50px; height: 50px; border-radius: 12px; overflow: hidden; border: 2px solid var(--promo-border); }
.promo-avatar img { width: 100%; height: 100%; object-fit: cover; }

.promo-student-info { display: flex; flex-direction: column; gap: 0.25rem; }
.promo-student-name { font-weight: 600; color: var(--promo-dark); font-size: 0.9375rem; }
.promo-student-code { display: flex; align-items: center; gap: 0.25rem; font-size: 0.75rem; color: var(--promo-gray); }

.promo-status-badge { display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.375rem 0.75rem; border-radius: 20px; font-size: 0.8125rem; font-weight: 600; }
.promo-status-badge.pending { background: var(--promo-light); color: var(--promo-gray); }
.promo-status-badge.promoted { background: rgba(var(--promo-success-rgb), 0.1); color: var(--promo-success); }

.promo-action-buttons { display: flex; gap: 0.5rem; flex-wrap: wrap; }
.promo-action-btn { display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.5rem 0.875rem; border-radius: 8px; font-size: 0.8125rem; font-weight: 600; border: none; cursor: pointer; transition: all 0.2s; }
.promo-btn-promote { background: rgba(var(--promo-success-rgb), 0.1); color: var(--promo-success); }
.promo-btn-promote:hover { background: var(--promo-success); color: white; transform: translateY(-1px); }
.promo-btn-hold { background: var(--promo-light); color: var(--promo-gray); border: 1px solid var(--promo-border); }
.promo-btn-hold:hover { background: var(--promo-border); color: var(--promo-dark); }

.promo-empty { display: flex; flex-direction: column; align-items: center; padding: 3rem 1.5rem; text-align: center; }
.promo-empty-icon { width: 80px; height: 80px; border-radius: 50%; background: linear-gradient(135deg, var(--promo-primary), #8b5cf6); display: flex; align-items: center; justify-content: center; margin-bottom: 1.25rem; }
.promo-empty-icon i { font-size: 2rem; color: white; }
.promo-empty h3 { margin: 0 0 0.5rem; font-size: 1.125rem; color: var(--promo-dark); }
.promo-empty p { margin: 0; color: var(--promo-gray); font-size: 0.875rem; }

@media (max-width: 768px) {
    .promo-list-header { display: none; }
    .promo-list-item { grid-template-columns: 1fr; gap: 0.75rem; }
    .promo-action-buttons { justify-content: flex-start; }
}
</style>

<!-- Info Bar -->
<div class="promo-info-bar">
    <div class="promo-info-chip">
        <i class="mdi mdi-google-classroom"></i>
        <span><?php echo get_phrase('from'); ?>:</span>
        <strong><?php echo $class_from_details['name']; ?></strong>
    </div>
    <div class="promo-arrow"><i class="mdi mdi-arrow-right-bold"></i></div>
    <div class="promo-info-chip">
        <i class="mdi mdi-google-classroom"></i>
        <span><?php echo get_phrase('to'); ?>:</span>
        <strong><?php echo $class_to_details['name']; ?></strong>
    </div>
    <div class="promo-info-chip">
        <i class="mdi mdi-calendar"></i>
        <span><?php echo $session_from_details['name']; ?></span>
        <i class="mdi mdi-arrow-right"></i>
        <span><?php echo $session_to_details['name']; ?></span>
    </div>
</div>

<!-- Stats -->
<div class="promo-stats">
    <div class="promo-stat-card">
        <div class="promo-stat-icon primary">
            <i class="mdi mdi-account-group"></i>
        </div>
        <div class="promo-stat-data">
            <span class="promo-stat-value"><?php echo count($enrolments); ?></span>
            <span class="promo-stat-label"><?php echo get_phrase('students_to_promote'); ?></span>
        </div>
    </div>
</div>

<!-- List Header -->
<div class="promo-list-header">
    <div><?php echo get_phrase('photo'); ?></div>
    <div><?php echo get_phrase('student_name'); ?></div>
    <div><?php echo get_phrase('status'); ?></div>
    <div><?php echo get_phrase('actions'); ?></div>
</div>

<!-- List Items -->
<?php foreach ($enrolments as $enrolment):
    $student_details = $this->user_model->get_student_details_by_id('student', $enrolment['student_id']);
?>
<div class="promo-list-item">
    <div class="promo-avatar">
        <img src="<?php echo $this->user_model->get_user_image($student_details['user_id']); ?>" alt="">
    </div>
    
    <div class="promo-student-info">
        <span class="promo-student-name"><?php echo $student_details['name']; ?></span>
        <span class="promo-student-code">
            <i class="mdi mdi-identifier"></i>
            <?php echo $student_details['code']; ?>
        </span>
    </div>
    
    <div>
        <span class="promo-status-badge promoted" id="success_<?php echo $enrolment['id']; ?>" style="display: none;">
            <i class="mdi mdi-check-circle"></i>
            <?php echo get_phrase('promoted'); ?>
        </span>
        <span class="promo-status-badge pending" id="danger_<?php echo $enrolment['id']; ?>">
            <i class="mdi mdi-clock-outline"></i>
            <?php echo get_phrase('not_promoted_yet'); ?>
        </span>
    </div>
    
    <div class="promo-action-buttons">
        <button type="button" class="promo-action-btn promo-btn-promote" 
                onclick="enrollStudent('<?php echo $student_details['id'].'-'.$class_id_to.'-'.$session_to; ?>', '<?php echo $enrolment['id']; ?>')">
            <i class="mdi mdi-arrow-up-bold-circle"></i>
            <?php echo $class_to_details['name']; ?>
        </button>
        <button type="button" class="promo-action-btn promo-btn-hold" 
                onclick="enrollStudent('<?php echo $student_details['id'].'-'.$class_id_from.'-'.$session_to; ?>', '<?php echo $enrolment['id']; ?>')">
            <i class="mdi mdi-refresh"></i>
            <?php echo get_phrase('hold'); ?>
        </button>
    </div>
</div>
<?php endforeach; ?>

<?php else: ?>

<div class="promo-empty">
    <div class="promo-empty-icon">
        <i class="mdi mdi-account-search"></i>
    </div>
    <h3><?php echo get_phrase('no_students_found'); ?></h3>
    <p><?php echo get_phrase('select_filters_to_view_students'); ?></p>
</div>

<?php endif; ?>