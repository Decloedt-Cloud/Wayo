<?php
    $school_id = school_id();
    $teachers = $this->db->get_where('teachers', array('school_id' => $school_id))->result_array();
    if(count($teachers) > 0):
?>
<div class="exp-list-header">
    <div><i class="mdi mdi-teach me-2"></i><?php echo get_phrase('teacher'); ?></div>
    <div class="justify-content-center"><i class="mdi mdi-clipboard-text-outline me-2"></i><?php echo get_phrase('marks'); ?></div>
    <div class="justify-content-center"><i class="mdi mdi-calendar-check-outline me-2"></i><?php echo get_phrase('attendance'); ?></div>
</div>

<div class="exp-list-body">
    <?php
        foreach($teachers as $teacher):
            $permission = $this->db->get_where('teacher_permissions', array('teacher_id' => $teacher['id'], 'class_id' => $class_id))->row_array();
            $user_id = $teacher['user_id'];
            $user = $this->db->get_where('users', array('id' => $user_id))->row_array();
    ?>
    <div class="exp-list-item">
        <div class="exp-teacher-info">
            <img src="<?php echo $this->user_model->get_user_image($user_id); ?>" class="exp-teacher-avatar" alt="Avatar">
            <div>
                <div class="exp-teacher-name"><?php echo $user['name']; ?></div>
                <div class="small text-muted"><?php echo $user['email']; ?></div>
            </div>
        </div>
        
        <div class="text-center">
            <label class="exp-switch">
                <input type="checkbox" 
                       value="<?php echo $permission['marks']; ?>" 
                       id="<?php echo $teacher['id'].'1'; ?>" 
                       onchange="togglePermission(this.id, 'marks', '<?php echo $teacher['id']; ?>')" 
                       <?php if($permission['marks'] == 1) echo 'checked'; ?>>
                <span class="exp-slider"></span>
            </label>
            <input type="hidden" name="<?=$this->security->get_csrf_token_name();?>" value="<?=$this->security->get_csrf_hash();?>" />
        </div>
        
        <div class="text-center">
            <label class="exp-switch">
                <input type="checkbox" 
                       value="<?php echo $permission['attendance']; ?>" 
                       id="<?php echo $teacher['id'].'3'; ?>" 
                       onchange="togglePermission(this.id, 'attendance', '<?php echo $teacher['id']; ?>')" 
                       <?php if($permission['attendance'] == 1) echo 'checked'; ?>>
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
