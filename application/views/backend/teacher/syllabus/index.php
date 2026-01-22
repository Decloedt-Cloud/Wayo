<link rel="stylesheet" href="<?php echo base_url(); ?>assets/backend/css/responsive.css">

<style>
/* ============================================================================
   EXPENSE - MODERN DESIGN (MATCHING EXPENSE STYLE)
   ============================================================================ */

:root {
    --exp-primary: #6366f1;
    --exp-primary-light: #eef2ff;
    --exp-success: #059669;
    --exp-dark: #1e293b;
    --exp-gray: #64748b;
    --exp-light: #f8fafc;
    --exp-border: #e2e8f0;
    --exp-warning: #f59e0b;
}

/* Header Card */
.exp-header {
    background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
    border-radius: 16px;
    padding: 1.5rem 2rem;
    margin-bottom: 1.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
    box-shadow: 0 4px 20px rgba(99, 102, 241, 0.3);
}

.exp-header-left {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.exp-header-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    background: rgba(255,255,255,0.15);
    backdrop-filter: blur(10px);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
}

.exp-header-text h4 {
    margin: 0;
    color: white;
    font-size: 1.35rem;
    font-weight: 700;
}

.exp-header-text p {
    margin: 0.25rem 0 0;
    color: rgba(255,255,255,0.7);
    font-size: 0.85rem;
}

.exp-header-actions {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
}

.exp-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.625rem;
    padding: 0.875rem 1.75rem;
    border-radius: 16px;
    font-size: 0.95rem;
    font-weight: 700;
    letter-spacing: 0.5px;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
    box-shadow: 
        0 4px 12px rgba(0, 0, 0, 0.08),
        inset 0 1px 0 rgba(255, 255, 255, 0.15);
    text-transform: uppercase;
}

.exp-btn-primary {
    background: linear-gradient(135deg, var(--exp-primary), #8b5cf6);
    color: white;
    border: 1px solid rgba(255, 255, 255, 0.25);
    backdrop-filter: blur(10px);
}

.exp-btn-primary::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    transition: left 0.6s ease-in-out;
    z-index: 1;
}

.exp-btn-primary::after {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, transparent 100%);
    opacity: 0;
    transition: opacity 0.3s ease;
    z-index: 0;
}

.exp-btn-primary span,
.exp-btn-primary i {
    position: relative;
    z-index: 2;
}

.exp-btn-primary i {
    font-size: 1.1rem;
    transition: transform 0.3s ease;
}

.exp-btn-primary:hover {
    transform: translateY(-3px) scale(1.02);
    box-shadow: 
        0 12px 30px rgba(99, 102, 241, 0.6),
        0 4px 15px rgba(139, 92, 246, 0.4),
        inset 0 1px 0 rgba(255, 255, 255, 0.2);
    background: linear-gradient(135deg, #8b5cf6, var(--exp-primary));
}

.exp-btn-primary:hover::before {
    left: 100%;
}

.exp-btn-primary:hover::after {
    opacity: 1;
}

.exp-btn-primary:hover i {
    transform: scale(1.15);
}

.exp-btn-primary:active {
    transform: translateY(-1px) scale(0.99);
    box-shadow: 
        0 6px 20px rgba(99, 102, 241, 0.5),
        0 2px 8px rgba(139, 92, 246, 0.3);
}

.exp-btn-primary:focus {
    outline: none;
    box-shadow: 
        0 0 0 3px rgba(99, 102, 241, 0.3),
        0 12px 30px rgba(99, 102, 241, 0.6);
}

/* Content Card */
.exp-content-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
    border: 1px solid var(--exp-border);
    overflow: hidden;
}

.exp-content-header {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    padding: 1rem 1.5rem;
    border-bottom: 1px solid var(--exp-border);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.exp-content-title {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 600;
    color: var(--exp-dark);
    font-size: 0.95rem;
}

.exp-content-title i {
    color: var(--exp-primary);
}

.exp-content-body {
    padding: 0;
}

/* Filter Section */
.exp-filter-section {
    padding: 1.5rem;
    background: white;
    border-radius: 16px;
    margin-bottom: 1.5rem;
    border: 1px solid var(--exp-border);
    box-shadow: 0 2px 12px rgba(0,0,0,0.04);
}

.exp-filter-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1.25rem;
    align-items: end;
}

.exp-filter-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.exp-filter-label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.8125rem;
    font-weight: 600;
    color: var(--exp-dark);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.exp-filter-label i {
    color: var(--exp-primary);
    font-size: 1rem;
}

.exp-filter-input {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 2px solid var(--exp-border);
    border-radius: 10px;
    font-size: 0.9375rem;
    background: var(--exp-light);
    color: var(--exp-dark);
    transition: all 0.2s;
    cursor: pointer;
}

.exp-filter-input:focus {
    outline: none;
    border-color: var(--exp-primary);
    background: white;
    box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
}

.exp-filter-input:hover {
    border-color: #cbd5e1;
}

.exp-filter-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    background: linear-gradient(135deg, var(--exp-primary), #8b5cf6);
    color: white;
    border: none;
    border-radius: 10px;
    font-size: 0.9375rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    height: fit-content;
    width: 100%;
}

.exp-filter-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
}

.exp-filter-btn i {
    font-size: 1.125rem;
}

/* Loading State */
.exp-loading {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 3rem;
    color: var(--exp-gray);
}

/* Table Styling */
.exp-table-container {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    border: 1px solid #e2e8f0;
    overflow: hidden;
    margin-bottom: 2rem;
}

.exp-report-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
}

.exp-report-table th, 
.exp-report-table td {
    padding: 1rem 1.5rem;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
}

.exp-report-table th {
    background: #f8fafc;
    font-weight: 600;
    color: #475569;
    text-transform: uppercase;
    font-size: 0.75rem;
    letter-spacing: 0.05em;
}

.exp-report-table tr:last-child td {
    border-bottom: none;
}

.exp-report-table tr:hover td {
    background: #f8fafc;
}

.thead-icon {
    margin-right: 0.5rem;
    color: var(--exp-primary);
    font-size: 1.1em;
}

/* Button Styles for Table */
.exp-btn-sm {
    padding: 0.5rem 1rem;
    font-size: 0.85rem;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    border: none;
    cursor: pointer;
    transition: all 0.2s;
    text-decoration: none;
}

.exp-btn-download {
    background: #eff6ff;
    color: #3b82f6;
}

.exp-btn-download:hover {
    background: #dbeafe;
    color: #2563eb;
}

.exp-btn-delete {
    background: #fef2f2;
    color: #ef4444;
}

.exp-btn-delete:hover {
    background: #fee2e2;
    color: #dc2626;
}

.exp-loading i {
    font-size: 2rem;
    animation: exp-spin 1s linear infinite;
    margin-right: 0.75rem;
}

@keyframes exp-spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

/* Responsive */
@media (max-width: 1024px) {
    .exp-filter-grid {
        grid-template-columns: 1fr 1fr;
    }
}

@media (max-width: 768px) {
    .exp-header {
        flex-direction: column;
        text-align: center;
    }
    
    .exp-header-left {
        flex-direction: column;
    }
    
    .exp-filter-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<!-- Header -->
<div class="exp-header">
    <div class="exp-header-left">
        <div class="exp-header-icon">
            <i class="fas fa-folder-open"></i>
        </div>
        <div class="exp-header-text">
            <h4><?php echo get_phrase('syllabus'); ?></h4>
            <p><?php echo get_phrase('manage_syllabus'); ?></p>
        </div>
    </div>
    <div class="exp-header-actions">
        <button type="button" 
            class="exp-btn exp-btn-primary" 
            onclick="rightModal('<?php echo site_url('modal/popup/syllabus/create'); ?>', '<?php echo htmlspecialchars(get_phrase('create_syllabus'), ENT_QUOTES); ?>')">
            <i class="mdi mdi-plus"></i> <?php echo get_phrase('add_syllabus'); ?>
        </button>
    </div>
</div>

<!-- Filter Section -->
<div class="exp-filter-section">
    <div class="exp-filter-grid">
        <div class="exp-filter-group"></div>
        <div class="exp-filter-group">
            <label class="exp-filter-label">
                <i class="mdi mdi-google-classroom"></i>
                <span><?php echo get_phrase('class'); ?></span>
            </label>
            <select name="class" id="class_id_teachaer" class="exp-filter-input" required>
                <option value="all"><?php echo get_phrase('all_programs'); ?></option>
                <?php
                $classes = $this->db->get_where('classes', array('school_id' => school_id()))->result_array();
                $school_id = school_id();
                foreach ($classes as $class):
                    $this->db->where('class_id', $class['id']);
                    $this->db->where('school_id', $school_id);
                    $total_student = $this->db->get('enrols');
                ?>
                    <option value="<?php echo $class['id']; ?>">
                        <?php echo $class['name']; ?>
                        <?php echo "(" . $total_student->num_rows() . ")"; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="exp-filter-group">
            <button class="exp-filter-btn" onclick="filter_syllabus()">
                <i class="mdi mdi-filter-outline"></i>
                <span><?php echo get_phrase('filter'); ?></span>
            </button>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="syllabus_content">
            <?php include 'list.php'; ?>
        </div>
    </div>
</div>
<script>
    $('document').ready(function() {
        $('select.select2:not(.normal)').each(function() {
            $(this).select2({
                dropdownParent: '#right-modal'
            });
        }); //initSelect2(['#class_id']);
        showAllSyllabuses('all');
    });


    function filter_syllabus() {
        var class_id = $('#class_id_teachaer').val();

        if (class_id != "") {
            showAllSyllabuses();
        } else {
            toastr.error('<?php echo get_phrase('please_select_a_class'); ?>');
        }
    }

    var showAllSyllabuses = function() {
        var class_id = $('#class_id_teachaer').val();

        if (class_id != "") {
            $.ajax({
                url: '<?php echo route('syllabus/list/') ?>' + class_id,
                success: function(response) {
                    $('.syllabus_content').html(response);
                    initDataTable('basic-datatable');
                }
            });
        }
    }
</script>