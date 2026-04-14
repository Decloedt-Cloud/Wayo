<?php
$school_id = isset($student_list_school_id) ? (int) $student_list_school_id : (int) school_id();
$school_name = isset($student_list_school_name) ? (string) $student_list_school_name : '';
$students_data = isset($student_list_rows) && is_array($student_list_rows) ? $student_list_rows : [];
?>
<table id="basic-datatable" class="table table-striped dt-responsive nowrap" width="100%">
    <thead>
        <tr style="background-color: #313a46; color: #ababab;">
            <th><?php echo get_phrase('code'); ?></th>
            <th><?php echo get_phrase('photo'); ?></th>
            <th><?php echo get_phrase('name'); ?></th>
            <th><?php echo get_phrase('options'); ?></th>
        </tr>
    </thead>
    <tbody>
    <?php foreach($students_data as $student): ?>
        <tr>
            <td><?php echo $student['code']; ?></td>
            <td>
                <img class="rounded-circle" width="50" height="50" src="<?php echo $this->user_model->get_user_image($student['user_id']); ?>">
            </td>
            <td><?php echo $this->user_model->get_user_details($student['user_id'], 'name'); ?></td>
            <td>
                <button type="button" class="btn btn-icon btn-secondary btn-sm btn-dark" style="margin-right:5px;" onclick="largeModal('<?php echo site_url('modal/popup/student/profile/'.$student['id'])?>', '<?php echo html_escape($school_name); ?>')" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-original-title="<?php echo get_phrase('student_profile'); ?>"> <i class="dripicons-checklist"></i></button>

                <a href="<?php echo route('student/edit/'.$student['id']); ?>" class="btn btn-icon btn-secondary btn-sm btn-dark" style="margin-right:5px;" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-original-title="<?php echo get_phrase('update_student_information'); ?>"> <i class="mdi mdi-wrench"></i></a>

                <button id="uname" type="button" class="btn btn-icon btn-secondary btn-sm tooltip_togle" style="margin-right:5px;" onclick="confirmModal('<?php echo route('student/delete/'.$student['id'].'/'.$student['user_id']); ?>', showAllStudents)" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-original-title="<?php echo get_phrase('delete_student'); ?>"> <i class="mdi mdi-window-close"></i></button>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<script type="text/javascript">
    initDataTable('basic-datatable');

    // $(function () {
    //   $('[data-bs-toggle="tooltip"]').popover();
    // });
</script>
