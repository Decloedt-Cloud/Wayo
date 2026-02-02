<link rel="stylesheet" href="<?php echo base_url(); ?>assets/backend/css/responsive.css">
<?php
// Récupérer l'ID de l'enseignant connecté
$user_id = $this->session->userdata('user_id');
$teacher = $this->db->get_where('teachers', ['user_id' => $user_id])->row_array();
$teacher_id = $teacher['id'] ?? null;

// Récupérer les classes autorisées pour cet enseignant (attendance = 1)
$permitted_class_ids = [];
if ($teacher_id) {
    $this->db->select('class_id');
    $this->db->from('teacher_permissions');
    $this->db->where('teacher_id', $teacher_id);
    $this->db->where('attendance', 1);
    $permitted_classes = $this->db->get()->result_array();
    $permitted_class_ids = array_column($permitted_classes, 'class_id');
}
?>

<style>
:root {
    --syllabus-primary: #6366f1;
    --syllabus-primary-light: #eef2ff;
    --syllabus-success: #059669;
    --syllabus-dark: #1e293b;
    --syllabus-gray: #64748b;
    --syllabus-light: #f8fafc;
    --syllabus-border: #e2e8f0;
}

.syllabus-header { background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); border-radius: 16px; padding: 1.5rem 2rem; margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; box-shadow: 0 4px 20px rgba(99, 102, 241, 0.3); }
.syllabus-header-left { display: flex; align-items: center; gap: 1rem; }
.syllabus-header-icon { width: 50px; height: 50px; border-radius: 12px; background: rgba(255,255,255,0.15); backdrop-filter: blur(10px); display: flex; align-items: center; justify-content: center; font-size: 1.5rem; color: white; }
.syllabus-header-text h4 { margin: 0; color: white; font-size: 1.35rem; font-weight: 700; }
.syllabus-header-text p { margin: 0.25rem 0 0; color: rgba(255,255,255,0.7); font-size: 0.85rem; }
.syllabus-header-right { display: flex; gap: 1rem; }
.syllabus-btn-add { background: rgba(255,255,255,0.2); color: white; border: 1px solid rgba(255,255,255,0.3); padding: 0.75rem 1.5rem; border-radius: 10px; font-weight: 600; transition: all 0.2s; display: flex; align-items: center; gap: 0.5rem; backdrop-filter: blur(5px); }
.syllabus-btn-add:hover { background: white; color: var(--syllabus-primary); transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.1); }

.syllabus-filter-section { padding: 1.5rem; background: white; border-radius: 16px; margin-bottom: 1.5rem; border: 1px solid var(--syllabus-border); box-shadow: 0 2px 12px rgba(0,0,0,0.04); }
.syllabus-filter-grid { display: grid; grid-template-columns: 1fr 120px; gap: 1.25rem; align-items: end; }
.syllabus-filter-group { display: flex; flex-direction: column; gap: 0.5rem; }
.syllabus-filter-label { display: flex; align-items: center; gap: 0.5rem; font-size: 0.8125rem; font-weight: 600; color: var(--syllabus-dark); text-transform: uppercase; letter-spacing: 0.5px; }
.syllabus-filter-label i { color: var(--syllabus-primary); font-size: 1rem; }
.syllabus-filter-input { width: 100%; padding: 0.75rem 1rem; border: 2px solid var(--syllabus-border); border-radius: 10px; font-size: 0.9375rem; background: var(--syllabus-light); color: var(--syllabus-dark); transition: all 0.2s; cursor: pointer; }
.syllabus-filter-input:focus { outline: none; border-color: var(--syllabus-primary); background: white; box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1); }
.syllabus-filter-btn { display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 0.75rem 1.5rem; background: linear-gradient(135deg, var(--syllabus-primary), #8b5cf6); color: white; border: none; border-radius: 10px; font-size: 0.9375rem; font-weight: 600; cursor: pointer; transition: all 0.2s; height: fit-content; }
.syllabus-filter-btn:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3); }

.syllabus-content-card { background: white; border-radius: 16px; box-shadow: 0 2px 12px rgba(0,0,0,0.08); border: 1px solid var(--syllabus-border); overflow: hidden; }
.syllabus-content-header { background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); padding: 1rem 1.5rem; border-bottom: 1px solid var(--syllabus-border); display: flex; justify-content: space-between; align-items: center; }
.syllabus-content-title { display: flex; align-items: center; gap: 0.5rem; font-weight: 600; color: var(--syllabus-dark); font-size: 0.95rem; }
.syllabus-content-title i { color: var(--syllabus-primary); }
.syllabus-content-body { padding: 1.5rem; }

.syllabus-empty { display: flex; flex-direction: column; align-items: center; padding: 3rem 1.5rem; text-align: center; }
.syllabus-empty-icon { width: 80px; height: 80px; border-radius: 50%; background: linear-gradient(135deg, var(--syllabus-primary), #8b5cf6); display: flex; align-items: center; justify-content: center; margin-bottom: 1.25rem; }
.syllabus-empty-icon i { font-size: 2rem; color: white; }
.syllabus-empty h3 { margin: 0 0 0.5rem; font-size: 1.125rem; color: var(--syllabus-dark); }
.syllabus-empty p { margin: 0; color: var(--syllabus-gray); font-size: 0.875rem; }

.syllabus-loading { display: flex; align-items: center; justify-content: center; padding: 3rem; color: var(--syllabus-gray); }
.syllabus-loading i { font-size: 2rem; animation: syllabus-spin 1s linear infinite; margin-right: 0.75rem; }
@keyframes syllabus-spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }

@media (max-width: 768px) {
    .syllabus-header { flex-direction: column; text-align: center; }
    .syllabus-header-left { flex-direction: column; }
    .syllabus-filter-grid { grid-template-columns: 1fr; }
}
</style>

<!-- Header -->
<div class="syllabus-header">
    <div class="syllabus-header-left">
        <div class="syllabus-header-icon">
            <i class="fas fa-folder-open"></i>
        </div>
        <div class="syllabus-header-text">
            <h4><?php echo get_phrase('syllabus'); ?></h4>
            <p><?php echo get_phrase('manage_syllabus'); ?></p>
        </div>
    </div>
    <div class="syllabus-header-right">
        <button type="button" class="syllabus-btn-add" onclick="rightModal('<?php echo site_url('modal/popup/syllabus/create'); ?>', '<?php echo htmlspecialchars(get_phrase('create_syllabus'), ENT_QUOTES); ?>')">
            <i class="mdi mdi-plus"></i> <?php echo get_phrase('add_syllabus'); ?>
        </button>
    </div>
</div>

<!-- Filter Section -->
<div class="syllabus-filter-section">
    <div class="syllabus-filter-grid">
        <div class="syllabus-filter-group">
            <label class="syllabus-filter-label">
                <i class="mdi mdi-google-classroom"></i>
                <span><?php echo get_phrase('class'); ?></span>
            </label>
            <select name="class" id="class_id_syllabus" class="syllabus-filter-input" required>
                <option value="all"><?php echo get_phrase('all_programs'); ?></option>
                <?php
                if (!empty($permitted_class_ids)) {
                    $this->db->where_in('id', $permitted_class_ids);
                    $this->db->where('school_id', school_id());
                    $classes = $this->db->get('classes')->result_array();
                } else {
                    $classes = [];
                }
                foreach ($classes as $class):
                    $this->db->where('class_id', $class['id']);
                    $this->db->where('school_id', school_id());
                    $total_student = $this->db->get('enrols');
                ?>
                    <option value="<?php echo $class['id']; ?>">
                        <?php echo $class['name']; ?> (<?php echo $total_student->num_rows(); ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="syllabus-filter-group">
            <button class="syllabus-filter-btn" onclick="filter_syllabus()">
                <i class="mdi mdi-filter-outline"></i>
                <span><?php echo get_phrase('filter'); ?></span>
            </button>
        </div>
    </div>
</div>

<!-- Content Card -->
<div class="syllabus-content-card">
    <div class="syllabus-content-header">
        <span class="syllabus-content-title">
            <i class="mdi mdi-clipboard-text-outline"></i>
            <?php echo get_phrase('syllabus_list'); ?>
        </span>
    </div>
    <div class="syllabus-content-body syllabus_content">
        <div class="syllabus-loading">
            <i class="mdi mdi-loading"></i>
            <span><?php echo get_phrase('loading'); ?>...</span>
        </div>
    </div>
</div>

<script>
    $('document').ready(function() {
        $('select.select2:not(.normal)').each(function() {
            $(this).select2({
                dropdownParent: '#right-modal'
            });
        });
        showAllSyllabuses('all');
    });

    function filter_syllabus() {
        var class_id = $('#class_id_syllabus').val();
        showAllSyllabuses(class_id);
    }

    var showAllSyllabuses = function(class_id) {
        // If class_id is not provided or is an object (event/callback result), get value from select
        if (!class_id || typeof class_id === 'object') {
            class_id = $('#class_id_syllabus').val();
        }
        
        $('.syllabus_content').html('<div class="syllabus-loading"><i class="mdi mdi-loading"></i> <?php echo get_phrase('loading'); ?>...</div>');

        $.ajax({
            url: '<?php echo route('syllabus/list/') ?>' + class_id,
            success: function(response) {
                $('.syllabus_content').html(response);
            }
        });
    }
</script>