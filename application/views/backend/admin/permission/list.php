<?php
    $school_id = school_id();
    
    // Optimized query to fetch teachers, user details, and permissions in one go
    $this->db->select('t.id as teacher_id, t.user_id, u.name, u.email, tp.marks, tp.attendance');
    $this->db->from('teachers t');
    $this->db->join('users u', 't.user_id = u.id');
    $this->db->join('teacher_permissions tp', 't.id = tp.teacher_id AND tp.class_id = ' . $this->db->escape($class_id), 'left');
    $this->db->where('t.school_id', $school_id);
    $teachers = $this->db->get()->result_array();

    if(count($teachers) > 0):
?>
<div class="exp-list-header">
    <div><i class="mdi mdi-teach me-2"></i><?php echo get_phrase('teacher'); ?></div>
    <div class="justify-content-center"><i class="mdi mdi-clipboard-text-outline me-2"></i><?php echo get_phrase('marks'); ?></div>
    <div class="justify-content-center"><i class="mdi mdi-calendar-check-outline me-2"></i><?php echo get_phrase('attendance'); ?></div>
    <div class="justify-content-center"><i class="mdi mdi-check-all me-2"></i><?php echo get_phrase('all'); ?></div>
</div>

<div class="exp-list-body">
    <?php
        foreach($teachers as $teacher):
            // Default permissions to 0 if null (left join)
            $permission_marks = isset($teacher['marks']) ? $teacher['marks'] : 0;
            $permission_attendance = isset($teacher['attendance']) ? $teacher['attendance'] : 0;
            
            $user_id = $teacher['user_id'];
    ?>
    <div class="exp-list-item">
        <div class="exp-teacher-info">
            <img src="<?php echo $this->user_model->get_user_image($user_id); ?>" class="exp-teacher-avatar" alt="Avatar">
            <div>
                <div class="exp-teacher-name"><?php echo $teacher['name']; ?></div>
                <div class="small text-muted"><?php echo $teacher['email']; ?></div>
            </div>
        </div>
        
        <div class="text-center">
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
        
        <div class="text-center">
            <label class="exp-switch">
                <input type="checkbox" 
                       value="<?php echo $permission_attendance; ?>" 
                       id="<?php echo $teacher['teacher_id'].'3'; ?>" 
                       onchange="togglePermission(this.id, 'attendance', '<?php echo $teacher['teacher_id']; ?>')" 
                       <?php if($permission_attendance == 1) echo 'checked'; ?>>
                <span class="exp-slider"></span>
            </label>
        </div>

        <div class="text-center">
            <label class="exp-switch">
                <input type="checkbox" 
                       value="<?php echo ($permission_marks == 1 && $permission_attendance == 1) ? 1 : 0; ?>" 
                       id="<?php echo $teacher['teacher_id'].'_all'; ?>" 
                       onchange="togglePermission(this.id, 'all', '<?php echo $teacher['teacher_id']; ?>')" 
                       <?php if($permission_marks == 1 && $permission_attendance == 1) echo 'checked'; ?>>
                <span class="exp-slider"></span>
            </label>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php else: ?>
    <div class="exp-empty-state">
        <img class="exp-empty-img" src="<?php echo base_url('assets/backend/images/empty_box.png'); ?>" />
        <div class="exp-empty-text"><?php echo get_phrase('no_teachers_found'); ?></div>
    </div>
<?php endif; ?>