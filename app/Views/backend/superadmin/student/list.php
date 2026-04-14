<?php
$school_id = isset($student_list_school_id) ? (int) $student_list_school_id : (int) school_id();
$school_name = isset($student_list_school_name) ? (string) $student_list_school_name : '';
$students_data = isset($student_list_rows) && is_array($student_list_rows) ? $student_list_rows : [];
$student_count = isset($student_list_count) ? (int) $student_list_count : count($students_data);
?>


<!-- Student Count Card -->
<div class="row mb-2">
  <div class="col-md-12 text-right">
    <span class="badge badge-pill badge-secondary" style="font-size: 14px; padding: 8px 12px; background-color: #313a46; color: #ffffff;">
      <?php echo get_phrase('Total Students'); ?>: <?php echo $student_count; ?>
    </span>
  </div>
</div>


<table id="basic-datatable" class="table table-striped dt-responsive nowrap table-modern" width="100%">
    <thead>
        <tr>
      <th><i class="mdi mdi-file-code-outline thead-icon"></i><?php echo get_phrase('code'); ?></th>
      <th><i class="mdi mdi-account-circle-outline thead-icon"></i><?php echo get_phrase('photo'); ?></th>
      <th><i class="mdi mdi-account-multiple-outline thead-icon"></i><?php echo get_phrase('name'); ?></th>
      <th><i class="mdi mdi-checkbox-marked-circle-outline thead-icon"></i><?php echo get_phrase('status'); ?></th>
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
        <td><?php echo $student['user_name']; ?></td>
        <td>
          <?php if((int) $student['user_status'] == 1): ?>
            <span class="badge bg-success"><?php echo get_phrase('active'); ?></span>
          <?php else: ?>
            <span class="badge bg-secondary"><?php echo get_phrase('deactive'); ?></span>
          <?php endif; ?>
        </td>
        <td>
          <div class="dropdown text-center">
            <button type="button" class="btn btn-sm btn-icon btn-rounded btn-outline-secondary dropdown-btn dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown" aria-expanded="false"><i class="mdi mdi-dots-vertical"></i></button>
            <div class="dropdown-menu dropdown-menu-end">
              <?php if(addon_status('id-card')):?>
                <a href="javascript:void(0);" class="dropdown-item" onclick="largeModal('<?php echo site_url('modal/popup/student/id_card/'.$student['id'])?>', '<?php echo html_escape($school_name); ?>')"><?php echo get_phrase('generate_id_card'); ?></a>
              <?php endif;?>
              <a href="javascript:void(0);" class="dropdown-item" onclick="largeModal('<?php echo site_url('modal/popup/student/profile/'.$student['id'])?>', '<?php echo html_escape($school_name); ?>')"><?php echo get_phrase('profile'); ?></a>
              <a href="<?php echo route('student/edit/'.$student['id']); ?>" class="dropdown-item"><?php echo get_phrase('edit'); ?></a>
              <?php if((int) $student['user_status'] == 1): ?>
                <a href="javascript:;" class="dropdown-item" onclick="confirmModal('<?php echo route('student/status/'.$student['id'].'/'.$student['user_id'].'/0'); ?>', showAllStudents)"><?php echo get_phrase('deactivate'); ?></a>
              <?php else: ?>
                <a href="javascript:;" class="dropdown-item" onclick="confirmModal('<?php echo route('student/status/'.$student['id'].'/'.$student['user_id'].'/1'); ?>', showAllStudents)"><?php echo get_phrase('activate'); ?></a>
              <?php endif; ?>
              <a href="javascript:;" class="dropdown-item" onclick="confirmModal('<?php echo route('student/delete/'.$student['id'].'/'.$student['user_id']); ?>', showAllStudents)"><?php echo get_phrase('delete'); ?></a>
            </div>
          </div>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<script src="<?php echo base_url('assets/backend/js/common_scripts.min.js'); ?>"></script>
