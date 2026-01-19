<?php 
$check_data = $this->db->get_where('grades', array('school_id' => school_id(), 'session' => active_session()));
$total_grades = $check_data->num_rows();
$grades = $check_data->result_array();
?>

<style>
:root {
    --grade-primary: #6366f1;
    --grade-primary-rgb: 99, 102, 241;
    --grade-success: #10b981;
    --grade-danger: #ef4444;
    --grade-dark: #1e293b;
    --grade-gray: #64748b;
    --grade-light: #f8fafc;
    --grade-border: #e2e8f0;
    --grade-white: #ffffff;
}

.grade-stats { padding: 1.5rem; }
.grade-stat-card { background: var(--grade-white); border-radius: 16px; padding: 1.25rem; display: flex; align-items: center; gap: 1rem; border: 1px solid var(--grade-border); }
.grade-stat-icon { width: 50px; height: 50px; border-radius: 12px; background: linear-gradient(135deg, var(--grade-primary), #8b5cf6); display: flex; align-items: center; justify-content: center; }
.grade-stat-icon i { font-size: 1.5rem; color: white; }
.grade-stat-data { display: flex; flex-direction: column; }
.grade-stat-value { font-size: 1.75rem; font-weight: 700; color: var(--grade-dark); }
.grade-stat-label { font-size: 0.8rem; color: var(--grade-gray); }

.grade-list-header { display: grid; grid-template-columns: 1.5fr 1fr 1fr 1fr 100px; gap: 1rem; padding: 0.875rem 1.5rem; background: var(--grade-light); border-bottom: 2px solid var(--grade-border); font-size: 0.75rem; font-weight: 700; text-transform: uppercase; color: var(--grade-gray); letter-spacing: 0.5px; }
.grade-list-item { display: grid; grid-template-columns: 1.5fr 1fr 1fr 1fr 100px; gap: 1rem; padding: 1rem 1.5rem; border-bottom: 1px solid var(--grade-border); transition: all 0.2s; align-items: center; }
.grade-list-item:hover { background: rgba(var(--grade-primary-rgb), 0.03); }
.grade-list-item:last-child { border-bottom: none; }

.grade-name { display: flex; align-items: center; gap: 0.75rem; }
.grade-avatar { width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.875rem; color: white; }
.grade-name-text { font-weight: 600; color: var(--grade-dark); font-size: 0.9375rem; }

.grade-badge { display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.375rem 0.75rem; border-radius: 20px; font-size: 0.8125rem; font-weight: 600; background: rgba(var(--grade-primary-rgb), 0.1); color: var(--grade-primary); }
.grade-range { display: flex; align-items: center; gap: 0.25rem; font-size: 0.875rem; color: var(--grade-dark); }
.grade-range i { color: var(--grade-primary); font-size: 1rem; }

.grade-action-buttons { display: flex; gap: 0.375rem; justify-content: center; }
.grade-action-btn { width: 32px; height: 32px; border-radius: 8px; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s; }
.grade-btn-edit { background: rgba(var(--grade-primary-rgb), 0.1); color: var(--grade-primary); }
.grade-btn-edit:hover { background: var(--grade-primary); color: white; }
.grade-btn-delete { background: rgba(239, 68, 68, 0.1); color: var(--grade-danger); }
.grade-btn-delete:hover { background: var(--grade-danger); color: white; }

.grade-empty { display: flex; flex-direction: column; align-items: center; padding: 3rem 1.5rem; text-align: center; }
.grade-empty-icon { width: 80px; height: 80px; border-radius: 50%; background: linear-gradient(135deg, var(--grade-primary), #8b5cf6); display: flex; align-items: center; justify-content: center; margin-bottom: 1.25rem; }
.grade-empty-icon i { font-size: 2rem; color: white; }
.grade-empty h3 { margin: 0 0 0.5rem; font-size: 1.125rem; color: var(--grade-dark); }
.grade-empty p { margin: 0; color: var(--grade-gray); font-size: 0.875rem; }

@media (max-width: 768px) {
    .grade-list-header { display: none; }
    .grade-list-item { grid-template-columns: 1fr; gap: 0.5rem; }
}
</style>

<?php if($total_grades > 0): ?>

<!-- Stats -->
<div class="grade-stats">
    <div class="grade-stat-card">
        <div class="grade-stat-icon">
            <i class="mdi mdi-clipboard-check-outline"></i>
        </div>
        <div class="grade-stat-data">
            <span class="grade-stat-value"><?php echo $total_grades; ?></span>
            <span class="grade-stat-label"><?php echo get_phrase('total_grades'); ?></span>
        </div>
    </div>
</div>

<!-- List Header -->
<div class="grade-list-header">
    <div><?php echo get_phrase('grade'); ?></div>
    <div><?php echo get_phrase('grade_point'); ?></div>
    <div><?php echo get_phrase('mark_from'); ?></div>
    <div><?php echo get_phrase('mark_upto'); ?></div>
    <div><?php echo get_phrase('actions'); ?></div>
</div>

<!-- List Items -->
<?php 
$colors = ['#6366f1', '#8b5cf6', '#a855f7', '#d946ef', '#ec4899', '#f43f5e'];
$i = 0;
foreach($grades as $grade): 
    $color = $colors[$i % count($colors)];
    $i++;
?>
<div class="grade-list-item">
    <div class="grade-name">
        <div class="grade-avatar" style="background: <?php echo $color; ?>">
            <?php echo strtoupper(substr($grade['name'], 0, 1)); ?>
        </div>
        <span class="grade-name-text"><?php echo $grade['name']; ?></span>
    </div>
    
    <div>
        <span class="grade-badge">
            <i class="mdi mdi-star"></i>
            <?php echo $grade['grade_point']; ?>
        </span>
    </div>
    
    <div class="grade-range">
        <i class="mdi mdi-arrow-right-bold"></i>
        <?php echo $grade['mark_from']; ?>%
    </div>
    
    <div class="grade-range">
        <i class="mdi mdi-arrow-left-bold"></i>
        <?php echo $grade['mark_upto']; ?>%
    </div>
    
    <div class="grade-action-buttons">
        <button type="button" class="grade-action-btn grade-btn-edit" 
                title="<?php echo get_phrase('edit'); ?>"
                onclick="rightModal('<?php echo site_url('modal/popup/grade/edit/'.$grade['id'])?>', '<?php echo get_phrase('update_grade'); ?>')">
            <i class="mdi mdi-pencil-outline"></i>
        </button>
        <button type="button" class="grade-action-btn grade-btn-delete" 
                title="<?php echo get_phrase('delete'); ?>"
                onclick="confirmModal('<?php echo route('grade/delete/'.$grade['id']); ?>', showAllGrades)">
            <i class="mdi mdi-trash-can-outline"></i>
        </button>
    </div>
</div>
<?php endforeach; ?>

<?php else: ?>

<div class="grade-empty">
    <div class="grade-empty-icon">
        <i class="mdi mdi-clipboard-plus-outline"></i>
    </div>
    <h3><?php echo get_phrase('no_grades_found'); ?></h3>
    <p><?php echo get_phrase('create_your_first_grade'); ?></p>
</div>

<?php endif; ?>
