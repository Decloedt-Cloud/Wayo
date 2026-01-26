<?php
$school_id = school_id();
if (isset($class_id)):
    if ($class_id == 'all') {
        $syllabuses = $this->db->get_where('syllabuses', array(
            'school_id' => $school_id,
            'session_id' => active_session()
        ))->result_array();
        $class_name = get_phrase('all_programs');
    } else {
        $syllabuses = $this->db->get_where('syllabuses', array(
            'class_id' => $class_id,
            'session_id' => active_session()
        ))->result_array();
        $class_name = $this->db->get_where('classes', array('id' => $class_id))->row('name');
    }
    $total_syllabuses = count($syllabuses);
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

@media (max-width: 768px) {
    .syllabus-list-header { display: none; }
    .syllabus-list-item { grid-template-columns: 1fr; gap: 0.75rem; }
}
</style>

<!-- Info Bar -->
<div class="syllabus-info-bar">
    <div class="syllabus-info-chip">
        <i class="mdi mdi-google-classroom"></i>
        <span><?php echo get_phrase('class'); ?>:</span>
        <strong><?php echo $class_name; ?></strong>
    </div>
</div>

<!-- Stats -->
<div class="syllabus-stats">
    <div class="syllabus-stat-card">
        <div class="syllabus-stat-icon primary">
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
    <div class="syllabus-list-header">
        <div><?php echo get_phrase('title'); ?></div>
        <div><?php echo get_phrase('file'); ?></div>
        <div><?php echo get_phrase('action'); ?></div>
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
    <div class="syllabus-list-item">
        <div class="syllabus-file-info">
            <div class="syllabus-file-icon">
                <i class="mdi <?php echo $icon_class; ?>"></i>
            </div>
            <div class="syllabus-file-name">
                <?php echo $syllabus['title']; ?>
                <span class="syllabus-file-meta"><?php echo strtoupper($file_extension); ?></span>
            </div>
        </div>
        
        <div>
            <a href="<?php echo base_url('uploads/syllabus/'.$syllabus['file']); ?>" class="syllabus-download-btn" download>
                <i class="mdi mdi-download"></i> <?php echo get_phrase('download'); ?>
            </a>
        </div>
        
        <div>
            <button type="button" class="syllabus-action-btn" 
                    onclick="confirmModal('<?php echo route('syllabus/delete/'.$syllabus['id']); ?>', showAllSyllabuses)"
                    title="<?php echo get_phrase('delete_syllabus'); ?>">
                <i class="mdi mdi-delete-outline"></i>
            </button>
        </div>
    </div>
    <?php endforeach; ?>

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