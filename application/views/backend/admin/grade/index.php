<link rel="stylesheet" href="<?php echo base_url(); ?>assets/backend/css/responsive.css">

<style>
:root {
    --grade-primary: #6366f1;
    --grade-primary-light: #eef2ff;
    --grade-success: #059669;
    --grade-dark: #1e293b;
    --grade-gray: #64748b;
    --grade-light: #f8fafc;
    --grade-border: #e2e8f0;
}

.grade-header { background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); border-radius: 16px; padding: 1.5rem 2rem; margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; box-shadow: 0 4px 20px rgba(99, 102, 241, 0.3); }
.grade-header-left { display: flex; align-items: center; gap: 1rem; }
.grade-header-icon { width: 50px; height: 50px; border-radius: 12px; background: rgba(255,255,255,0.15); backdrop-filter: blur(10px); display: flex; align-items: center; justify-content: center; font-size: 1.5rem; color: white; }
.grade-header-text h4 { margin: 0; color: white; font-size: 1.35rem; font-weight: 700; }
.grade-header-text p { margin: 0.25rem 0 0; color: rgba(255,255,255,0.7); font-size: 0.85rem; }
.grade-header-actions { display: flex; gap: 0.75rem; flex-wrap: wrap; }

.grade-btn { display: inline-flex; align-items: center; gap: 0.625rem; padding: 0.875rem 1.75rem; border-radius: 16px; font-size: 0.95rem; font-weight: 700; letter-spacing: 0.5px; text-decoration: none; border: none; cursor: pointer; transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1); position: relative; overflow: hidden; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08), inset 0 1px 0 rgba(255, 255, 255, 0.15); text-transform: uppercase; }
.grade-btn-primary { background: linear-gradient(135deg, var(--grade-primary), #8b5cf6); color: white; border: 1px solid rgba(255, 255, 255, 0.25); backdrop-filter: blur(10px); }
.grade-btn-primary:hover { transform: translateY(-3px) scale(1.02); box-shadow: 0 12px 30px rgba(99, 102, 241, 0.6), 0 4px 15px rgba(139, 92, 246, 0.4); background: linear-gradient(135deg, #8b5cf6, var(--grade-primary)); color: white; }

.grade-content-card { background: white; border-radius: 16px; box-shadow: 0 2px 12px rgba(0,0,0,0.08); border: 1px solid var(--grade-border); overflow: hidden; }
.grade-content-header { background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); padding: 1rem 1.5rem; border-bottom: 1px solid var(--grade-border); display: flex; justify-content: space-between; align-items: center; }
.grade-content-title { display: flex; align-items: center; gap: 0.5rem; font-weight: 600; color: var(--grade-dark); font-size: 0.95rem; }
.grade-content-title i { color: var(--grade-primary); }
.grade-content-body { padding: 0; }

.grade-loading { display: flex; align-items: center; justify-content: center; padding: 3rem; color: var(--grade-gray); }
.grade-loading i { font-size: 2rem; animation: grade-spin 1s linear infinite; margin-right: 0.75rem; }
@keyframes grade-spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }

@media (max-width: 768px) {
    .grade-header { flex-direction: column; text-align: center; }
    .grade-header-left { flex-direction: column; }
}
</style>

<!-- Header -->
<div class="grade-header">
    <div class="grade-header-left">
        <div class="grade-header-icon">
            <i class="fas fa-clipboard-check"></i>
        </div>
        <div class="grade-header-text">
            <h4><?php echo get_phrase('Grade'); ?></h4>
            <p><?php echo get_phrase('manage_grading_system'); ?></p>
        </div>
    </div>
    <div class="grade-header-actions">
        <button type="button" class="grade-btn grade-btn-primary" onclick="rightModal('<?php echo site_url('modal/popup/grade/create'); ?>', '<?php echo get_phrase('add_grade'); ?>')">
            <i class="mdi mdi-plus"></i> <?php echo get_phrase('add_grade'); ?>
        </button>
    </div>
</div>

<!-- Content Card -->
<div class="grade-content-card">
    <div class="grade-content-header">
        <span class="grade-content-title">
            <i class="mdi mdi-format-list-bulleted"></i>
            <?php echo get_phrase('grade_list'); ?>
        </span>
    </div>
    <div class="grade-content-body">
        <div class="grade_content">
            <?php include 'list.php'; ?>
        </div>
    </div>
</div>

<script>
var showAllGrades = function () {
    $('.grade_content').html('<div class="grade-loading"><i class="mdi mdi-loading"></i> <?php echo get_phrase('loading'); ?>...</div>');
    
    var url = '<?php echo route('grade/list'); ?>';
    $.ajax({
        type : 'GET',
        url: url,
        success : function(response) {
            $('.grade_content').html(response);
            initDataTable('basic-datatable');
        }
    });
}
</script>
