<link rel="stylesheet" href="<?php echo base_url(); ?>assets/backend/css/responsive.css">

<style>
/* ============================================================================
   MARK - MODERN DESIGN (MATCHING CLASS STYLE)
   ============================================================================ */

:root {
    --mark-primary: #6366f1;
    --mark-primary-light: #eef2ff;
    --mark-success: #059669;
    --mark-dark: #1e293b;
    --mark-gray: #64748b;
    --mark-light: #f8fafc;
    --mark-border: #e2e8f0;
    --mark-warning: #f59e0b;
}

/* Stats Card (replacing toll-free-box) */
.mark-stats-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.mark-stat-card {
    background: white;
    border-radius: 16px;
    padding: 1.5rem;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    border: 1px solid var(--mark-border);
    display: flex;
    align-items: center;
    gap: 1rem;
    transition: transform 0.3s ease;
}

.mark-stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 24px rgba(0,0,0,0.1);
}

.mark-stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    background: var(--mark-primary-light);
    color: var(--mark-primary);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}

.mark-stat-info h5 {
    margin: 0;
    font-size: 0.9rem;
    color: var(--mark-gray);
    font-weight: 600;
}

.mark-stat-info p {
    margin: 0;
    font-size: 1.1rem;
    color: var(--mark-dark);
    font-weight: 700;
}

/* Table Styling */
.mark-table-wrapper {
    overflow-x: auto;
    border-radius: 12px;
    border: 1px solid var(--mark-border);
}

.mark-table {
    width: 100%;
    border-collapse: collapse;
}

.mark-table thead th {
    background: #f8fafc;
    padding: 1rem;
    font-size: 0.85rem;
    font-weight: 600;
    color: var(--mark-gray);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: 1px solid var(--mark-border);
}

.mark-table tbody td {
    padding: 1rem;
    border-bottom: 1px solid var(--mark-border);
    color: var(--mark-dark);
    font-size: 0.95rem;
    vertical-align: middle;
}

.mark-table tbody tr:last-child td {
    border-bottom: none;
}

.mark-table tbody tr:hover {
    background-color: #f8fafc;
}

.thead-icon {
    margin-right: 8px;
    font-size: 1.1em;
    vertical-align: middle;
}

/* Action Buttons in Table */
.mark-action-btn {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: 1px solid transparent;
    cursor: pointer;
    transition: all 0.2s;
    background: transparent;
}

.mark-action-btn:hover {
    background: var(--mark-light);
    border-color: var(--mark-border);
}

.mark-action-btn i {
    font-size: 1.2rem;
}

.mark-btn-success {
    background: rgba(5, 150, 105, 0.1);
    color: var(--mark-success);
    border: none;
}

.mark-btn-success:hover {
    background: var(--mark-success);
    color: white;
}

/* Header Card */
.mark-header {
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

.mark-header-left {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.mark-header-icon {
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

.mark-header-text h4 {
    margin: 0;
    color: white;
    font-size: 1.35rem;
    font-weight: 700;
}

.mark-header-text p {
    margin: 0.25rem 0 0;
    color: rgba(255,255,255,0.7);
    font-size: 0.85rem;
}

/* Filter Section */
.mark-filter-card {
    background: white;
    border-radius: 16px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
    border: 1px solid var(--mark-border);
}

.mark-filter-title {
    font-size: 1rem;
    font-weight: 600;
    color: var(--mark-dark);
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.form-group-modern {
    margin-bottom: 0;
}

.form-control-modern {
    width: 100%;
    padding: 10px 16px;
    border: 1.5px solid #e5e7eb;
    border-radius: 10px;
    font-size: 14px;
    transition: all 0.2s ease;
    background: #fafafa;
    height: 45px;
}

.form-control-modern:focus {
    outline: none;
    border-color: #6366f1;
    background: #fff;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
}

.btn-modern {
    padding: 10px 24px;
    border-radius: 10px;
    font-weight: 600;
    font-size: 14px;
    border: none;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: all 0.3s ease;
    height: 45px;
    width: 100%;
}

.btn-modern-primary {
    background: linear-gradient(135deg, var(--mark-primary), #8b5cf6);
    color: white;
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.2);
}

.btn-modern-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(99, 102, 241, 0.4);
    color: white;
}

/* Content Card */
.mark-content-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
    border: 1px solid var(--mark-border);
    overflow: hidden;
    min-height: 300px;
}

.mark-content-header {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    padding: 1rem 1.5rem;
    border-bottom: 1px solid var(--mark-border);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.mark-content-title {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 600;
    color: var(--mark-dark);
    font-size: 0.95rem;
}

.mark-content-title i {
    color: var(--mark-primary);
}

.mark-content-body {
    padding: 1.5rem;
}

/* Empty State */
.empty-state-modern {
    text-align: center;
    padding: 3rem;
}

.empty-state-modern img {
    margin-bottom: 1.5rem;
    opacity: 0.8;
    transition: transform 0.3s ease;
}

.empty-state-modern:hover img {
    transform: scale(1.05);
}

.empty-state-modern span {
    color: var(--mark-gray);
    font-size: 1.1rem;
    font-weight: 500;
}

/* Responsive */
@media (max-width: 768px) {
    .mark-header {
        flex-direction: column;
        text-align: center;
        padding: 1.5rem;
    }
    
    .mark-header-left {
        flex-direction: column;
    }

    .btncol {
        margin-top: 1rem;
    }
}
</style>

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

<!-- Header -->
<div class="mark-header">
    <div class="mark-header-left">
        <div class="mark-header-icon">
            <i class="fas fa-star"></i>
        </div>
        <div class="mark-header-text">
            <h4><?php echo get_phrase('manage_marks'); ?></h4>
            <p><?php echo get_phrase('manage_student_marks_and_results'); ?></p>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <!-- Filter Section -->
        <div class="mark-filter-card">
            <div class="mark-filter-title">
                <i class="fas fa-filter"></i> <?php echo get_phrase('filter_selection'); ?>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3 mb-md-0">
                    <div class="form-group-modern">
                        <select name="exam" id="exam_id" class="form-control-modern select2" required>
                            <option value=""><?php echo get_phrase('select_a_exam'); ?></option>
                            <?php 
                            $school_id = school_id();
                            $exams = $this->db->get_where('exams', array('school_id' => $school_id, 'session' => active_session()))->result_array();
                            foreach($exams as $exam){ ?>
                                <option value="<?php echo $exam['id']; ?>"><?php echo $exam['name'];?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-4 mb-3 mb-md-0">
                    <div class="form-group-modern">
                        <select name="class" id="class_id_mark" class="form-control-modern select2" required>
                            <option value=""><?php echo get_phrase('select_a_class'); ?></option>
                            <?php
                            if (!empty($permitted_class_ids)) {
                                $this->db->where_in('id', $permitted_class_ids);
                                $this->db->where('school_id', $school_id);
                                $classes = $this->db->get('classes')->result_array();
                            } else {
                                $classes = [];
                            }
                            foreach($classes as $class){
                                $this->db->where('class_id', $class['id']);
                                $this->db->where('school_id', $school_id);
                                $total_student = $this->db->get('enrols');
                                ?>
                                <option value="<?php echo $class['id']; ?>">
                                    <?php echo $class['name']; ?>
                                    <?php echo "(".$total_student->num_rows().")"; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <button class="btn-modern btn-modern-primary" onclick="filter_attendance()">
                        <i class="mdi mdi-filter"></i> <?php echo get_phrase('filter'); ?>
                    </button>
                </div>
            </div>
        </div>

        <!-- Content Section -->
        <div class="mark-content-card">
            <div class="mark-content-header">
                <span class="mark-content-title">
                    <i class="mdi mdi-format-list-bulleted"></i>
                    <?php echo get_phrase('marks_list'); ?>
                </span>
            </div>
            <div class="mark-content-body mark_content">
                <div class="empty-state-modern">
                    <img width="150px" src="<?php echo base_url('assets/backend/images/empty_box.png'); ?>" />
                    <br>
                    <span><?php echo get_phrase('no_data_found'); ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$('document').ready(function(){
    $('select.select2:not(.normal)').each(function () { $(this).select2({ dropdownParent: '#right-modal' }); });
});




function filter_attendance(){
    var exam = $('#exam_id').val();
    var class_id = $('#class_id_mark').val();

    // Récupérer le nom et la valeur du jeton CSRF depuis l'input caché
    var csrfName = $('input[name="<?= $this->security->get_csrf_token_name(); ?>"]').attr('name');
    var csrfHash = $('input[name="<?= $this->security->get_csrf_token_name(); ?>"]').val();
    if(class_id != ""  && exam != "" ){
        $.ajax({
            type: 'POST',
            url: '<?php echo route('mark/list') ?>',
            data: {class_id : class_id, exam : exam , [csrfName]: csrfHash},
            dataType: 'json',
            success: function(response){
                $('.mark_content').html(response.html); // Teacher returns response.html, Admin returns response.status. Original teacher file used response.html.

                // Mettre à jour le jeton CSRF avec le nouveau jeton renvoyé dans la réponse
                var newCsrfName = response.csrf.csrfName;
                var newCsrfHash = response.csrf.csrfHash;
                $('input[name="' + newCsrfName + '"]').val(newCsrfHash); // Mise à jour du token CSRF

            }
        });
    }else{
        toastr.error('<?php echo get_phrase('please_select_in_all_fields !'); ?>');
    }
}
</script>
