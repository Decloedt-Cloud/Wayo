<?php
    $student = isset($modal_student) && is_array($modal_student) ? $modal_student : [];
    $enrols = isset($modal_student_enrols) && is_array($modal_student_enrols) ? $modal_student_enrols : [];
    $class_names = isset($modal_student_class_names) && is_array($modal_student_class_names) ? $modal_student_class_names : [];
    if (isset($modal_student_not_found) ? $modal_student_not_found : empty($student)): ?>
        <div class="alert alert-danger"><?php echo get_phrase('student_not_found'); ?></div>
        <a href="<?php echo site_url('student/student'); ?>" class="btn btn-primary"><?php echo get_phrase('go_back'); ?></a>
        <?php return;
    endif;
 
?>
<div class="h-100">
    <div class="row align-items-center h-100">
        <div class="col-md-4 pb-2">
            <div class="text-center">
                <img class="rounded-circle" width="50" height="50" src="<?php echo $this->user_model->get_user_image($student['user_id']); ?>">
                <br>
                <span style="font-weight: bold;">
                    <?php echo get_phrase('name'); ?>: <?php echo $this->user_model->get_user_details($student['user_id'], 'name'); ?>
                </span>
                <br>
                <span style="font-weight: bold;">
                    <?php echo get_phrase('student_code'); ?>: <?php echo $student['code']; ?>
                </span>
            </div>
        </div>
        <div class="col-md-8">
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="profile-tab" data-bs-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false"><?php echo get_phrase('profile'); ?></a>
                </li>
               
            </ul>
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                    <table class="table table-centered mb-0">
                        <tbody>
                            <tr>
                                <td style="font-weight: bold;"><?php echo get_phrase('name'); ?>:</td>
                                <td><?php echo $this->user_model->get_user_details($student['user_id'], 'name'); ?></td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold;"><?php echo get_phrase('class'); ?>:</td>
                                <td>
                                    <?php
                                        $first_enrol = $enrols[0]['class_id'] ?? null;
                                        echo html_escape($class_names[(int) $first_enrol] ?? '');
                                    ?>
                                </td>
                            </tr>

                        </tbody>
                    </table>
                </div>
                <div class="tab-pane fade show" id="parent_info" role="tabpanel" aria-labelledby="parent-tab">
                    <table class="table table-centered mb-0">
                        <tbody>
                            <tr>
                                <td style="font-weight: bold;"><?php echo get_phrase('parent_name'); ?>:</td>
                                <td>
                                    <?php echo $this->user_model->get_user_details($parent['user_id'], 'name'); ?>
                                </td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold;"><?php echo get_phrase('parent_email'); ?>:</td>
                                <td>
                                    <?php echo $this->user_model->get_user_details($parent['user_id'], 'email'); ?>
                                </td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold;"><?php echo get_phrase('parent_phone_number'); ?>:</td>
                                <td>
                                    <?php echo $this->user_model->get_user_details($parent['user_id'], 'phone'); ?>sddfas
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
