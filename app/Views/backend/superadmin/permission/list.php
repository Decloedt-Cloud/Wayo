
<table id="basic-datatable" class="table table-striped dt-responsive nowrap table-modern" width="100%">
    <thead>
        <tr>
            <th><i class="mdi mdi-teach thead-icon"></i><?php echo get_phrase('teacher'); ?></th>
            <th><i class="mdi mdi-clipboard-text-outline thead-icon"></i><?php echo get_phrase('marks'); ?></th>
            <!-- <th><?php echo get_phrase('assignment'); ?></th> -->
            <th><i class="mdi mdi-calendar-check-outline thead-icon"></i><?php echo get_phrase('attendance'); ?></th>
            <!-- <th><?php echo get_phrase('online_exam'); ?></th> -->
        </tr>
    </thead>
    <tbody>
		<?php
			$school_id = school_id();
			$teachers = db()->table('teachers')->where('school_id', $school_id)->get()->getResultArray();
			foreach($teachers as $teacher){
                $permission = db()->table('teacher_permissions')->where('teacher_id', $teacher['id'])->where('class_id', $class_id)->get()->getRowArray();
		?>
		<tr>
            <td><?php echo esc(db()->table('users')->where('id', $teacher['user_id'])->get()->getRow()->name ?? ''); ?></td>
            <td>
                <input type="checkbox" value="<?php echo $permission['marks']; ?>" id="<?php echo $teacher['id'].'1'; ?>" data-switch="success" onchange="togglePermission(this.id, 'marks', '<?php echo $teacher['id']; ?>')" <?php if($permission['marks'] == 1) echo 'checked'; ?>>
                <label for="<?php echo $teacher['id'].'1'; ?>" data-on-label="Yes" data-off-label="No">
                <input type="hidden" name="<?=csrf_token();?>" value="<?=csrf_hash();?>" />

            </td>
            <!-- <td>
                <input type="checkbox" value="<?php echo $permission['assignment']; ?>" id="<?php echo $teacher['id'].'2'; ?>" data-switch="success" onchange="togglePermission(this.id, 'assignment', '<?php echo $teacher['id']; ?>')" <?php if($permission['assignment'] == 1) echo 'checked'; ?>>
                <label for="<?php echo $teacher['id'].'2'; ?>" data-on-label="Yes" data-off-label="No">
            </td> -->
            <td>
                <input type="checkbox" value="<?php echo $permission['attendance']; ?>" id="<?php echo $teacher['id'].'3'; ?>" data-switch="success" onchange="togglePermission(this.id, 'attendance', '<?php echo $teacher['id']; ?>')" <?php if($permission['attendance'] == 1) echo 'checked'; ?>>
                <label for="<?php echo $teacher['id'].'3'; ?>" data-on-label="Yes" data-off-label="No">
            </td>
            <!-- <td>
                <input type="checkbox" value="<?php echo $permission['online_exam']; ?>" id="<?php echo $teacher['id'].'4'; ?>" data-switch="success" onchange="togglePermission(this.id, 'online_exam', '<?php echo $teacher['id']; ?>')" <?php if($permission['online_exam'] == 1) echo 'checked'; ?>>
                <label for="<?php echo $teacher['id'].'4'; ?>" data-on-label="Yes" data-off-label="No">
            </td> -->
		</tr>
		<?php } ?>
	</tbody>
</table>
<!-- <script type="text/javascript">
    initDataTable('basic-datatable');
</script> -->
