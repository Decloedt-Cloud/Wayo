<?php
$student_data = isset($student_data) && is_array($student_data) ? $student_data : [];
$user_details = isset($user_details) && is_array($user_details) ? $user_details : [];
$marks = isset($marks) && is_array($marks) ? $marks : [];
$exam_details = isset($exam_details) && is_array($exam_details) ? $exam_details : [];
$class_name = isset($class_name) ? (string) $class_name : '';
$student_not_found = isset($student_not_found) ? (bool) $student_not_found : empty($student_data);
$exam_starting_date = isset($exam_details['starting_date']) ? (int) $exam_details['starting_date'] : null;
if ($student_not_found): ?>
    <div class="alert alert-danger"><?php echo get_phrase('student_not_found'); ?></div>
    <a href="<?php echo site_url('student/student'); ?>" class="btn btn-primary"><?php echo get_phrase('go_back'); ?></a>
    <?php return;
endif;
?>

<div class="row mb-3">
    <div class="col-md-4"></div>
    <div class="col-md-4 toll-free-box text-center text-white pb-2" style="background-color: #6c757d; border-radius: 10px;">
        <h4><?php echo get_phrase('manage_marks'); ?></h4>
        <span><?php echo get_phrase('Exam name'); ?> : <?php echo html_escape($exam_details['name'] ?? ''); ?></span><br>
        <span><?php echo get_phrase('class'); ?> : <?php echo html_escape($class_name); ?></span><br>
        <!-- Ajout de la date et heure de l'examen -->
        <span><?php echo get_phrase('exam_date'); ?> : <?php echo $exam_starting_date ? date('D, d-M-Y H:i', $exam_starting_date) : ''; ?></span>
    </div>
</div>

<?php if (count($marks) > 0): ?>
    <table class="table table-bordered table-responsive-sm" width="100%">
        <thead class="thead-dark">
            <tr>
                <th><i class="mdi mdi-account-multiple-outline thead-icon"></i><?php echo get_phrase('student_name'); ?></th>
                <th><i class="mdi mdi-numeric thead-icon"></i><?php echo get_phrase('mark'); ?></th>
                <th><i class="mdi mdi-comment-text-outline thead-icon"></i><?php echo get_phrase('comment'); ?></th>
                <th><i class="mdi mdi-clipboard-check-outline thead-icon"></i><?php echo get_phrase('result'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($marks as $mark): ?>
                <?php if ($mark['student_id'] == $student_data['id']): ?>
                    <?php
                    $mark_on_20 = ($mark['mark_obtained'] / 100) * 20;
                    $mark_on_20 = round($mark_on_20, 2);
                    ?>
                    <tr>
                        <td><?php echo $user_details['name']; ?></td>
                        <td>
                            <input class="form-control readonly" type="text" id="mark-<?php echo $mark['student_id']; ?>" name="mark" placeholder="mark" 
                                   value="<?php echo $mark_on_20 . '/20'; ?>" readonly>
                        </td>
                        <td>
                            <input class="form-control readonly" type="text" id="comment-<?php echo $mark['student_id']; ?>" name="comment" placeholder="comment" 
                                   value="<?php echo $mark['comment']; ?>" readonly>
                        </td>
                        <td class="text-center">
                            <a href="javascript:void(0);" 
                                onclick="largeModal('<?php echo site_url('student/exam_results/' . $exam_id . '/' . $mark['student_id']); ?>', '<?php echo get_phrase('exam_results'); ?>')" 
                                class="text-primary">
                                <i class="mdi mdi-beaker" style="font-size: 24px;"></i>
                            </a>
                        </td>
                    </tr>
                <?php endif; ?>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <?php include APPPATH . 'Views/backend/empty.php'; ?>
<?php endif; ?>