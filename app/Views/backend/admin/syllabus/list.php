<?php
$school_id = school_id();
$per_page = 20;
$current_page = isset($page) && is_numeric($page) ? (int) $page : 1;
$current_page = max(1, $current_page);
$offset = ($current_page - 1) * $per_page;

if (isset($class_id)):
    if ($class_id == 'all') {
        $builder = db()->table('syllabuses')->where('school_id', $school_id)->where('session_id', active_session());
        $total_syllabuses = $builder->countAllResults(false);
        $syllabuses = $builder->limit($per_page, $offset)->get()->getResultArray();
        $class_name = get_phrase('all_programs');
    } else {
        $builder = db()->table('syllabuses')->where('class_id', (int) $class_id)->where('session_id', active_session());
        $total_syllabuses = $builder->countAllResults(false);
        $syllabuses = $builder->limit($per_page, $offset)->get()->getResultArray();
        $class_name = db()->table('classes')->where('id', (int) $class_id)->get()->getRow()->name ?? '';
    }
    $total_pages = ceil($total_syllabuses / $per_page);
?>

<style>
:root {
    --syllabus-primary: #6366f1;
    --syllabus-primary-rgb: 99, 102, 241;
    --syllabus-success: #10b981;
    --syllabus-danger: #ef4444;
    --syllabus-warning: #f59e0b;
    --syllabus-dark: #1e293b;
    --syllabus-gray: #64748b;
    --syllabus-light: #f8fafc;
    --syllabus-border: #e2e8f0;
    --syllabus-white: #ffffff;
}

.syllabus-info-bar { display: flex; gap: 1rem; margin-bottom: 1.5rem; flex-wrap: wrap; }
.syllabus-info-chip { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; background: linear-gradient(135deg, rgba(var(--syllabus-primary-rgb), 0.1), rgba(var(--syllabus-primary-rgb), 0.05)); border-radius: 20px; font-size: 0.8125rem; color: var(--syllabus-dark); border: 1px solid rgba(var(--syllabus-primary-rgb), 0.2); }
.syllabus-info-chip i { color: var(--syllabus-primary); }
.syllabus-info-chip strong { font-weight: 600; }

.syllabus-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem; margin-bottom: 1.5rem; }
.syllabus-stat-card { background: var(--syllabus-white); border-radius: 12px; padding: 1rem; border: 1px solid var(--syllabus-border); display: flex; align-items: center; gap: 0.75rem; }
.syllabus-stat-icon { width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; }
.syllabus-stat-icon.primary { background: rgba(var(--syllabus-primary-rgb), 0.1); color: var(--syllabus-primary); }
.syllabus-stat-data { display: flex; flex-direction: column; }
.syllabus-stat-value { font-size: 1.25rem; font-weight: 700; color: var(--syllabus-dark); }
.syllabus-stat-label { font-size: 0.75rem; color: var(--syllabus-gray); }

.syllabus-list-header { display: grid; grid-template-columns: 2fr 1fr 100px; gap: 1rem; padding: 0.75rem 1rem; background: var(--syllabus-light); border-radius: 10px; margin-bottom: 0.75rem; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; color: var(--syllabus-gray); letter-spacing: 0.5px; }
.syllabus-list-item { display: grid; grid-template-columns: 2fr 1fr 100px; gap: 1rem; padding: 1rem; background: var(--syllabus-white); border: 1px solid var(--syllabus-border); border-radius: 12px; margin-bottom: 0.5rem; transition: all 0.2s; align-items: center; }
.syllabus-list-item:hover { border-color: var(--syllabus-primary); box-shadow: 0 4px 12px rgba(var(--syllabus-primary-rgb), 0.1); }

.syllabus-file-info { display: flex; align-items: center; gap: 0.75rem; }
.syllabus-file-icon { width: 40px; height: 40px; border-radius: 10px; background: linear-gradient(135deg, var(--syllabus-primary), #8b5cf6); display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 1.2rem; }
.syllabus-file-name { font-weight: 600; color: var(--syllabus-dark); font-size: 0.9375rem; display: flex; flex-direction: column; }
.syllabus-file-meta { font-size: 0.75rem; color: var(--syllabus-gray); font-weight: normal; }

.syllabus-action-btn { width: 36px; height: 36px; border-radius: 10px; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s; background: rgba(var(--syllabus-primary-rgb), 0.1); color: var(--syllabus-primary); }
.syllabus-action-btn:hover { background: var(--syllabus-danger); color: white; transform: translateY(-2px); }
.syllabus-download-btn { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; background: var(--syllabus-light); border: 1px solid var(--syllabus-border); border-radius: 8px; color: var(--syllabus-dark); font-size: 0.85rem; font-weight: 500; text-decoration: none; transition: all 0.2s; }
.syllabus-download-btn:hover { border-color: var(--syllabus-primary); color: var(--syllabus-primary); background: white; }

.syllabus-pagination { display: flex; justify-content: space-between; align-items: center; margin-top: 1.5rem; padding: 1rem; background: var(--syllabus-white); border: 1px solid var(--syllabus-border); border-radius: 12px; }
.pagination-info { font-size: 0.875rem; color: var(--syllabus-gray); }
.pagination-controls { display: flex; align-items: center; gap: 0.75rem; }
.pagination-btn { width: 36px; height: 36px; border-radius: 8px; border: 1px solid var(--syllabus-border); background: var(--syllabus-light); cursor: pointer; display: flex; align-items: center; justify-content: center; color: var(--syllabus-dark); transition: all 0.2s; }
.pagination-btn:hover:not(:disabled) { background: var(--syllabus-primary); color: white; border-color: var(--syllabus-primary); }
.pagination-btn:disabled { opacity: 0.5; cursor: not-allowed; }
.pagination-btn:focus-visible { outline: 3px solid var(--syllabus-primary); outline-offset: 2px; }
.pagination-pages { font-size: 0.875rem; font-weight: 600; color: var(--syllabus-dark); }
.sr-only { position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0, 0, 0, 0); white-space: nowrap; border-width: 0; }

@media (max-width: 768px) {
    .syllabus-list-header { display: none; }
    .syllabus-list-item { grid-template-columns: 1fr; gap: 0.75rem; }
    .syllabus-pagination { flex-direction: column; gap: 1rem; }
}
</style>

<!-- Info Bar -->
<div class="syllabus-info-bar" role="status" aria-live="polite">
    <div class="syllabus-info-chip">
        <i class="mdi mdi-google-classroom" aria-hidden="true"></i>
        <span><?php echo get_phrase('class'); ?>:</span>
        <strong><?php echo htmlspecialchars($class_name, ENT_QUOTES); ?></strong>
    </div>
</div>

<!-- Stats -->
<div class="syllabus-stats" role="group" aria-label="<?php echo get_phrase('syllabus_statistics'); ?>">
    <div class="syllabus-stat-card">
        <div class="syllabus-stat-icon primary" aria-hidden="true">
            <i class="mdi mdi-file-document-outline"></i>
        </div>
        <div class="syllabus-stat-data">
            <span class="syllabus-stat-value"><?php echo $total_syllabuses; ?></span>
            <span class="syllabus-stat-label"><?php echo get_phrase('total_syllabus'); ?></span>
        </div>
    </div>
</div>

<?php if($total_syllabuses > 0): ?>
    <!-- List Header -->
    <div class="syllabus-list-header" role="row">
        <div role="columnheader"><?php echo get_phrase('title'); ?></div>
        <div role="columnheader"><?php echo get_phrase('file'); ?></div>
        <div role="columnheader"><?php echo get_phrase('action'); ?></div>
    </div>

    <!-- List Items -->
    <?php foreach ($syllabuses as $syllabus):
        $file_extension = pathinfo($syllabus['file'], PATHINFO_EXTENSION);
        $icon_class = 'mdi-file-outline';
        if(in_array(strtolower($file_extension), ['pdf'])) $icon_class = 'mdi-file-pdf-outline';
        elseif(in_array(strtolower($file_extension), ['doc', 'docx'])) $icon_class = 'mdi-file-word-outline';
        elseif(in_array(strtolower($file_extension), ['xls', 'xlsx'])) $icon_class = 'mdi-file-excel-outline';
        elseif(in_array(strtolower($file_extension), ['jpg', 'jpeg', 'png'])) $icon_class = 'mdi-file-image-outline';
    ?>
    <div class="syllabus-list-item" role="row">
        <div class="syllabus-file-info" role="gridcell">
            <div class="syllabus-file-icon" aria-hidden="true">
                <i class="mdi <?php echo $icon_class; ?>"></i>
            </div>
            <div class="syllabus-file-name">
                <?php echo htmlspecialchars($syllabus['title'], ENT_QUOTES); ?>
                <span class="syllabus-file-meta"><?php echo htmlspecialchars(strtoupper($file_extension), ENT_QUOTES); ?></span>
            </div>
        </div>
        
        <div role="gridcell">
            <a href="<?php echo site_url('admin/syllabus/download/'.$syllabus['id']); ?>" 
               class="syllabus-download-btn" 
               download
               aria-label="<?php echo get_phrase('download_file') . ': ' . htmlspecialchars($syllabus['title'], ENT_QUOTES); ?>">
                <i class="mdi mdi-download" aria-hidden="true"></i> 
                <span class="sr-only"><?php echo get_phrase('download'); ?></span>
            </a>
        </div>
        
        <div role="gridcell">
            <button type="button" 
                    class="syllabus-action-btn" 
                    onclick="confirmModal('<?php echo route('syllabus/delete/'.$syllabus['id']); ?>', showAllSyllabuses)"
                    aria-label="<?php echo get_phrase('delete_syllabus') . ': ' . htmlspecialchars($syllabus['title'], ENT_QUOTES); ?>"
                    title="<?php echo get_phrase('delete_syllabus'); ?>">
                <i class="mdi mdi-delete-outline" aria-hidden="true"></i>
            </button>
        </div>
    </div>
    <?php endforeach; ?>
    
    <?php if($total_pages > 1): ?>
    <nav class="syllabus-pagination" role="navigation" aria-label="<?php echo get_phrase('pagination'); ?>">
        <div class="pagination-info">
            <span><?php echo get_phrase('showing'); ?> <?php echo ($offset + 1) . ' - ' . min($offset + $per_page, $total_syllabuses); ?> <?php echo get_phrase('of'); ?> <?php echo $total_syllabuses; ?></span>
        </div>
        <div class="pagination-controls">
            <button type="button" class="pagination-btn" 
                    onclick="changePage(<?php echo max(1, $current_page - 1); ?>)" 
                    <?php echo $current_page <= 1 ? 'disabled' : ''; ?>
                    aria-label="<?php echo get_phrase('previous_page'); ?>"
                    <?php echo $current_page <= 1 ? 'aria-disabled="true"' : ''; ?>>
                <i class="mdi mdi-chevron-left" aria-hidden="true"></i>
            </button>
            <span class="pagination-pages" aria-live="polite">
                <?php echo get_phrase('page'); ?> <?php echo $current_page; ?> / <?php echo $total_pages; ?>
            </span>
            <button type="button" class="pagination-btn" 
                    onclick="changePage(<?php echo min($total_pages, $current_page + 1); ?>)" 
                    <?php echo $current_page >= $total_pages ? 'disabled' : ''; ?>
                    aria-label="<?php echo get_phrase('next_page'); ?>"
                    <?php echo $current_page >= $total_pages ? 'aria-disabled="true"' : ''; ?>>
                <i class="mdi mdi-chevron-right" aria-hidden="true"></i>
            </button>
        </div>
    </nav>
    <?php endif; ?>

<?php else: ?>
    <div class="syllabus-empty">
        <div class="syllabus-empty-icon">
            <i class="mdi mdi-folder-remove-outline"></i>
        </div>
        <h3><?php echo get_phrase('no_data_found'); ?></h3>
        <p><?php echo get_phrase('no_syllabus_found_for_this_selection'); ?></p>
    </div>
<?php endif; ?>

<?php else: ?>
    <div class="syllabus-empty">
        <div class="syllabus-empty-icon">
            <i class="mdi mdi-folder-search-outline"></i>
        </div>
        <h3><?php echo get_phrase('select_a_class'); ?></h3>
        <p><?php echo get_phrase('select_a_class_to_view_syllabus'); ?></p>
    </div>
<?php endif; ?>

<script>
function changePage(page) {
    var class_id = '<?php echo $class_id; ?>';
    $('.syllabus_content').html('<div class="syllabus-loading"><i class="mdi mdi-loading"></i> <?php echo get_phrase('loading'); ?>...</div>');
    
    $.ajax({
        url: '<?php echo site_url('admin/syllabus/list/'); ?>' + class_id + '/' + page,
        success: function(response) {
            $('.syllabus_content').html(response);
        }
    });
}
</script>
