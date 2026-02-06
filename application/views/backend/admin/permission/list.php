<?php
    $school_id = school_id();
    
    // Optimized query to fetch teachers, user details, and permissions in one go
    $this->db->select('t.id as teacher_id, t.user_id, t.designation, u.name, u.email, tp.marks, tp.attendance');
    $this->db->from('teachers t');
    $this->db->join('users u', 't.user_id = u.id');
    $this->db->join('teacher_permissions tp', 't.id = tp.teacher_id AND tp.class_id = ' . $this->db->escape($class_id), 'left');
    $this->db->where('t.school_id', $school_id);
    $teachers = $this->db->get()->result_array();

    if(count($teachers) > 0):
?>
<div class="exp-list-header">
    <div class="ps-2"><i class="mdi mdi-teach me-2"></i><?php echo get_phrase('teacher'); ?></div>
    <div class="justify-content-center text-center">
        <span data-bs-toggle="tooltip" title="<?php echo get_phrase('allow_managing_marks'); ?>">
            <i class="mdi mdi-clipboard-text-outline me-1"></i><?php echo get_phrase('marks'); ?>
        </span>
    </div>
    <div class="justify-content-center text-center">
        <span data-bs-toggle="tooltip" title="<?php echo get_phrase('allow_taking_attendance'); ?>">
            <i class="mdi mdi-calendar-check-outline me-1"></i><?php echo get_phrase('attendance'); ?>
        </span>
    </div>
    <div class="justify-content-center text-center">
        <span data-bs-toggle="tooltip" title="<?php echo get_phrase('allow_all_permissions'); ?>">
            <i class="mdi mdi-check-all me-1"></i><?php echo get_phrase('all'); ?>
        </span>
    </div>
</div>

<div class="exp-list-body">
    <?php
        foreach($teachers as $teacher):
            // Default permissions to 0 if null (left join)
            $permission_marks = isset($teacher['marks']) ? $teacher['marks'] : 0;
            $permission_attendance = isset($teacher['attendance']) ? $teacher['attendance'] : 0;
            $user_id = $teacher['user_id'];
            $is_all = ($permission_marks == 1 && $permission_attendance == 1);
    ?>
    <div class="exp-list-item <?php echo $is_all ? 'bg-soft-success' : ''; ?>">
        <div class="exp-teacher-info ps-2">
            <div class="avatar-wrapper">
                <img src="<?php echo $this->user_model->get_user_image($user_id); ?>" class="exp-teacher-avatar" alt="Avatar">
                <?php if($is_all): ?>
                    <span class="status-indicator bg-success" title="<?php echo get_phrase('full_access'); ?>"></span>
                <?php endif; ?>
            </div>
            <div>
                <div class="exp-teacher-name"><?php echo $teacher['name']; ?></div>
                <div class="small text-muted d-flex align-items-center gap-1">
                    <i class="mdi mdi-email-outline" style="font-size: 12px;"></i> <?php echo $teacher['email']; ?>
                </div>
                <?php if(!empty($teacher['designation'])): ?>
                    <div class="badge badge-soft-primary mt-1" style="font-size: 10px;"><?php echo $teacher['designation']; ?></div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Marks Toggle -->
        <div class="text-center d-flex justify-content-center align-items-center">
            <label class="exp-switch">
                <input type="checkbox" 
                       value="<?php echo $permission_marks; ?>" 
                       id="<?php echo $teacher['teacher_id'].'1'; ?>" 
                       onchange="togglePermission(this.id, 'marks', '<?php echo $teacher['teacher_id']; ?>')" 
                       <?php if($permission_marks == 1) echo 'checked'; ?>>
                <span class="exp-slider"></span>
            </label>
            <input type="hidden" name="<?=$this->security->get_csrf_token_name();?>" value="<?=$this->security->get_csrf_hash();?>" />
        </div>
        
        <!-- Attendance Toggle -->
        <div class="text-center d-flex justify-content-center align-items-center">
            <label class="exp-switch">
                <input type="checkbox" 
                       value="<?php echo $permission_attendance; ?>" 
                       id="<?php echo $teacher['teacher_id'].'3'; ?>" 
                       onchange="togglePermission(this.id, 'attendance', '<?php echo $teacher['teacher_id']; ?>')" 
                       <?php if($permission_attendance == 1) echo 'checked'; ?>>
                <span class="exp-slider"></span>
            </label>
        </div>

        <!-- All Toggle -->
        <div class="text-center d-flex justify-content-center align-items-center">
            <label class="exp-switch exp-switch-all">
                <input type="checkbox" 
                       value="<?php echo $is_all ? 1 : 0; ?>" 
                       id="<?php echo $teacher['teacher_id'].'_all'; ?>" 
                       onchange="togglePermission(this.id, 'all', '<?php echo $teacher['teacher_id']; ?>')" 
                       <?php if($is_all) echo 'checked'; ?>>
                <span class="exp-slider slider-all"></span>
            </label>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<style>
    /* Additional inline styles for this advanced view */
    .avatar-wrapper { position: relative; }
    .status-indicator {
        position: absolute;
        bottom: 0;
        right: 0;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        border: 2px solid white;
    }
    .badge-soft-primary {
        background-color: rgba(99, 102, 241, 0.1);
        color: #6366f1;
        padding: 2px 6px;
        border-radius: 4px;
        font-weight: 500;
    }
    .bg-soft-success {
        background-color: rgba(5, 150, 105, 0.03) !important;
    }
    /* Enhance slider for ALL */
    .slider-all:before {
        background-color: #fff;
    }
    input:checked + .slider-all {
        background-color: #1e293b; /* Darker color for ALL */
    }
    
    /* Tooltip initialization */
    .exp-list-header div span {
        cursor: help;
    }
</style>

<script>
    // Initialize tooltips if bootstrap is available
    if (typeof bootstrap !== 'undefined') {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    }
</script>

<?php else: ?>
    <div class="exp-empty-state">
        <img class="exp-empty-img" src="<?php echo base_url('assets/backend/images/empty_box.png'); ?>" />
        <div class="exp-empty-text"><?php echo get_phrase('no_teachers_found'); ?></div>
        <p class="text-muted mt-2"><?php echo get_phrase('add_teachers_to_this_school_to_manage_permissions'); ?></p>
    </div>
<?php endif; ?>