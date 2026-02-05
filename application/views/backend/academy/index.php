<!--title-->
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/backend/css/responsive.min.css">

<style>
/* ============================================================================
   ACADEMY - MODERN DESIGN (MATCHING EXPENSE STYLE)
   ============================================================================ */

:root {
    --aca-primary: var(--bs-primary);
    --aca-primary-rgb: var(--bs-primary-rgb);
    --aca-success: var(--bs-success);
    --aca-success-rgb: var(--bs-success-rgb);
    --aca-warning: var(--bs-warning);
    --aca-warning-rgb: var(--bs-warning-rgb);
    --aca-danger: var(--bs-danger);
    --aca-danger-rgb: var(--bs-danger-rgb);
    --aca-dark: var(--bs-dark);
    --aca-gray: var(--bs-gray);
    --aca-light: var(--bs-light);
    --aca-border: var(--bs-gray-200);
    --aca-white: var(--bs-white);
}

/* Header Card */
.aca-header {
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

.aca-header-left {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.aca-header-icon {
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

.aca-header-text h4 {
    margin: 0;
    color: white;
    font-size: 1.35rem;
    font-weight: 700;
}

.aca-header-text p {
    margin: 0.25rem 0 0;
    color: rgba(255,255,255,0.7);
    font-size: 0.85rem;
}

.aca-header-actions {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
}

/* Modern Button Style (Matching Expense) */
.aca-btn {
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

.aca-btn-primary {
    background: linear-gradient(135deg, var(--aca-primary), #8b5cf6);
    color: white;
    border: 1px solid rgba(255, 255, 255, 0.25);
    backdrop-filter: blur(10px);
}

.aca-btn-primary::before {
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

.aca-btn-primary::after {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, transparent 100%);
    opacity: 0;
    transition: opacity 0.3s ease;
    z-index: 0;
}

.aca-btn-primary span,
.aca-btn-primary i {
    position: relative;
    z-index: 2;
}

.aca-btn-primary i {
    font-size: 1.1rem;
    transition: transform 0.3s ease;
}

.aca-btn-primary:hover {
    transform: translateY(-3px) scale(1.02);
    box-shadow: 
        0 12px 30px rgba(99, 102, 241, 0.6),
        0 4px 15px rgba(139, 92, 246, 0.4),
        inset 0 1px 0 rgba(255, 255, 255, 0.2);
    background: linear-gradient(135deg, #8b5cf6, var(--aca-primary));
    color: white;
}

.aca-btn-primary:hover::before {
    left: 100%;
}

.aca-btn-primary:hover::after {
    opacity: 1;
}

.aca-btn-primary:hover i {
    transform: scale(1.15);
}

.aca-btn-primary:active {
    transform: translateY(-1px) scale(0.99);
    box-shadow: 
        0 6px 20px rgba(99, 102, 241, 0.5),
        0 2px 8px rgba(139, 92, 246, 0.3);
}

.aca-btn-primary:focus {
    outline: none;
    box-shadow: 
        0 0 0 3px rgba(99, 102, 241, 0.3),
        0 12px 30px rgba(99, 102, 241, 0.6);
}

/* Content Card */
.aca-content-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
    border: 1px solid var(--aca-border);
    overflow: hidden;
}

.aca-content-header {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    padding: 1rem 1.5rem;
    border-bottom: 1px solid var(--aca-border);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.aca-content-title {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 600;
    color: var(--aca-dark);
    font-size: 0.95rem;
}

.aca-content-title i {
    color: var(--aca-primary);
}

.aca-content-body {
    padding: 0;
}

/* List/Grid Container */
.academy_content {
    margin: 0;
}

/* Loading State */
.aca-loading {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 3rem;
    color: var(--aca-gray);
}

.aca-loading i {
    font-size: 2rem;
    animation: aca-spin 1s linear infinite;
    margin-right: 0.75rem;
}

@keyframes aca-spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

/* Responsive */
@media (max-width: 768px) {
    .aca-header {
        flex-direction: column;
        text-align: center;
    }
    
    .aca-header-left {
        flex-direction: column;
    }
}
</style>

<?php if($this->session->userdata('student_login') != 1): ?>
    <!-- Header -->
    <div class="aca-header">
        <div class="aca-header-left">
            <div class="aca-header-icon">
                <i class="fas fa-layer-group"></i>
            </div>
            <div class="aca-header-text">
                <h4><?php echo get_phrase('all_courses'); ?></h4>
                <p><?php echo get_phrase('manage_academic_syllabus'); ?></p>
            </div>
        </div>
        <div class="aca-header-actions">
            <a href="<?php echo site_url('addons/courses/course_add'); ?>" class="aca-btn aca-btn-primary">
                <i class="mdi mdi-plus"></i> <?php echo get_phrase('create_new_course'); ?>
            </a>
        </div>
    </div>
<?php endif; ?>

<!-- Content Card -->
<div class="aca-content-card">
    <div class="aca-content-header">
        <span class="aca-content-title">
            <i class="mdi mdi-format-list-bulleted"></i>
            <?php echo get_phrase('course_list'); ?>
        </span>
    </div>
    <div class="aca-content-body">
        <?php if($this->session->userdata('superadmin_login') == 1 || $this->session->userdata('admin_login') == 1 || $this->session->userdata('teacher_login') == 1): ?>
            <div class="academy_content">
                <?php include 'list.php'; ?>
            </div> 
        <?php else: ?>
            <div class="academy_content">
                <?php include 'grid_view_for_student.php'; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    var filterCourse = function() {
        var url = '<?php echo site_url('addons/courses/filter'); ?>';
        var class_id = $('#class_id_course').val();
        var user_id = $('#user_id').val();
        var status = $('#course_status').val();
        
        // Show loading
        $('.academy_content').html('<div class="aca-loading"><i class="mdi mdi-loading"></i> <?php echo get_phrase('loading'); ?>...</div>');
        
        $.ajax({
            type : 'GET',
            url: url,
            data : {class_id : class_id, user_id : user_id, status : status},
            success : function(response) {
                $('.academy_content').html(response);
                initDataTable("basic-datatable");
            }
        });
    }

    var course_activity = function(course_id) {
        var url = '<?php echo site_url('addons/courses/update_status/'); ?>' + course_id;
        $.ajax({
            url: url,
            success: function(response) {
                success_notify('<?php echo get_phrase('course_status_updated_successfully'); ?>');
                filterCourse();
            }
        });
    }
</script>