<?php
$marks = isset($marks) && is_array($marks) ? $marks : [];
$exam_details = isset($exam_details) && is_array($exam_details) ? $exam_details : [];
$class_name = isset($class_name) ? (string) $class_name : '';
$student_names = isset($student_names) && is_array($student_names) ? $student_names : [];
$exam_starting_date = isset($exam_details['starting_date']) ? (int) $exam_details['starting_date'] : null;

// Calculate Stats
$total_students = count($marks);
$total_score = 0;
$highest_score = 0;
$lowest_score = 100;

foreach ($marks as $mark) {
    $score = $mark['mark_obtained'];
    $total_score += $score;
    if ($score > $highest_score) $highest_score = $score;
    if ($score < $lowest_score) $lowest_score = $score;
}

$average_score = $total_students > 0 ? round($total_score / $total_students, 2) : 0;
?>

<!-- Stats Dashboard -->
<div class="mark-stats-container">
    <div class="mark-stat-card">
        <div class="mark-stat-icon">
            <i class="mdi mdi-file-document-box-multiple-outline"></i>
        </div>
        <div class="mark-stat-info">
            <h5><?php echo get_phrase('exam'); ?></h5>
            <p><?php echo $exam_details['name']; ?></p>
            <small class="text-muted"><?php echo $exam_starting_date ? date('d M Y', $exam_starting_date) : ''; ?></small>
        </div>
    </div>

    <div class="mark-stat-card">
        <div class="mark-stat-icon">
            <i class="mdi mdi-google-classroom"></i>
        </div>
        <div class="mark-stat-info">
            <h5><?php echo get_phrase('class'); ?></h5>
            <p><?php echo $class_name; ?></p>
            <small class="text-muted"><?php echo $total_students . ' ' . get_phrase('students'); ?></small>
        </div>
    </div>

    <div class="mark-stat-card">
        <div class="mark-stat-icon">
            <i class="mdi mdi-chart-bell-curve-cumulative"></i>
        </div>
        <div class="mark-stat-info">
            <h5><?php echo get_phrase('average_score'); ?></h5>
            <p><?php echo $average_score; ?>%</p>
            <small class="text-muted"><?php echo get_phrase('highest'); ?>: <?php echo $highest_score; ?>%</small>
        </div>
    </div>
</div>

<?php if ($total_students > 0): ?>
    <div class="mark-table-wrapper">
        <table class="mark-table">
            <thead>
                <tr>
                    <th><i class="mdi mdi-account-circle-outline thead-icon"></i> <?php echo get_phrase('student'); ?></th>
                    <th><i class="mdi mdi-format-annotation-plus thead-icon"></i> <?php echo get_phrase('mark'); ?> (20)</th>
                    <th class="d-none d-md-table-cell"><i class="mdi mdi-star-circle-outline thead-icon"></i> <?php echo get_phrase('grade'); ?></th>
                    <th class="d-none d-md-table-cell"><i class="mdi mdi-comment-text-outline thead-icon"></i> <?php echo get_phrase('comment'); ?></th>
                    <th class="d-none d-md-table-cell"><i class="mdi mdi-clipboard-check-outline thead-icon"></i> <?php echo get_phrase('result'); ?></th>
                    <th><i class="mdi mdi-dots-horizontal thead-icon"></i> <?php echo get_phrase('action'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($marks as $mark):
                    $student_name = $student_names[(int) $mark['student_id']] ?? '';
                    if ($student_name === '') continue;
                    $mark_on_20 = ($mark['mark_obtained'] / 100) * 20;
                    $mark_on_20 = round($mark_on_20, 2);
                ?>
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            <!-- Avatar Placeholder or Initials could go here -->
                                <span class="font-weight-bold"><?php echo html_escape($student_name); ?></span>
                        </div>
                    </td>
                    <td>
                        <input class="form-control-modern readonly" type="text" id="mark-<?php echo $mark['student_id']; ?>" name="mark" 
                               value="<?php echo $mark_on_20; ?>" readonly style="width: 80px; text-align: center;">
                    </td>
                    <td class="d-none d-md-table-cell">
                        <span class="badge badge-light-primary" id="grade-for-mark-<?php echo $mark['student_id']; ?>" style="font-size: 0.9rem; padding: 5px 10px;">
                            <?php echo get_grade($mark['mark_obtained']); ?>
                        </span>
                    </td>
                    <td class="d-none d-md-table-cell">
                        <input class="form-control-modern" type="text" id="comment-<?php echo $mark['student_id']; ?>" name="comment" 
                               value="<?php echo $mark['comment']; ?>" placeholder="<?php echo get_phrase('comment'); ?>">
                    </td>
                    <td class="d-none d-md-table-cell text-center">
                        <button class="mark-action-btn" onclick="largeModal('<?php echo site_url('superadmin/exam_results/' . $exam_id . '/' . $mark['student_id']); ?>', '<?php echo get_phrase('exam_results'); ?>')" title="<?php echo get_phrase('view_results'); ?>">
                            <i class="mdi mdi-file-document-outline" style="color: var(--mark-primary);"></i>
                        </button>
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <button class="mark-action-btn mark-btn-success d-none d-md-inline-flex" onclick="comment_update('<?php echo $mark['student_id']; ?>')" title="<?php echo get_phrase('update'); ?>">
                                <i class="mdi mdi-check"></i>
                            </button>
                            
                            <!-- Mobile Expand Button -->
                            <button class="mark-action-btn d-md-none expand-button" type="button" style="color: var(--mark-gray);">
                                <i class="mdi mdi-chevron-down"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                
                <!-- Mobile Details Row -->
                <tr class="expandable-row d-md-none" style="display: none; background: #f8fafc;">
                    <td colspan="6">
                        <div class="p-3">
                            <div class="mb-2">
                                <strong><?php echo get_phrase('grade'); ?>:</strong>
                                <span class="badge badge-light-primary"><?php echo get_grade($mark['mark_obtained']); ?></span>
                            </div>
                            <div class="mb-2">
                                <strong><?php echo get_phrase('comment'); ?>:</strong>
                                <input class="form-control-modern mt-1" type="text" id="comment-mobile-<?php echo $mark['student_id']; ?>" value="<?php echo $mark['comment']; ?>">
                            </div>
                            <div class="mb-3">
                                <button class="btn-modern btn-sm w-100 mb-2" onclick="largeModal('<?php echo site_url('superadmin/exam_results/' . $exam_id . '/' . $mark['student_id']); ?>', '<?php echo get_phrase('exam_results'); ?>')" style="background: white; border: 1px solid var(--mark-border); color: var(--mark-primary);">
                                    <i class="mdi mdi-file-document-outline"></i> <?php echo get_phrase('view_results'); ?>
                                </button>
                                <button class="btn-modern btn-modern-primary btn-sm w-100" onclick="comment_update('<?php echo $mark['student_id']; ?>', true)">
                                    <i class="mdi mdi-check"></i> <?php echo get_phrase('update'); ?>
                                </button>
                            </div>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <div class="empty-state-modern">
        <img width="150px" src="<?php echo base_url('assets/backend/images/empty_box.png'); ?>" />
        <br>
        <span><?php echo get_phrase('no_marks_found'); ?></span>
    </div>
<?php endif; ?>

<script>
    // Toggle Mobile Details
    $('.expand-button').on('click', function() {
        var row = $(this).closest('tr').next('.expandable-row');
        var icon = $(this).find('i');
        
        row.slideToggle();
        if (row.is(':visible')) {
            icon.removeClass('mdi-chevron-down').addClass('mdi-chevron-up');
        } else {
            icon.removeClass('mdi-chevron-up').addClass('mdi-chevron-down');
        }
    });

    function comment_update(student_id, is_mobile = false) {
        var comment_id = is_mobile ? '#comment-mobile-' + student_id : '#comment-' + student_id;
        var comment = $(comment_id).val();
        var mark = $('#mark-' + student_id).val(); 

        // Sync inputs if needed
        if(is_mobile) {
             $('#comment-' + student_id).val(comment);
        } else {
             $('#comment-mobile-' + student_id).val(comment);
        }

        if(mark != "") {
            $.ajax({
                type: 'POST',
                url: '<?php echo route('mark/mark_update'); ?>',
                data: {
                    student_id: student_id,
                    comment: comment,
                    mark: mark,
                    exam_id: '<?php echo $exam_id; ?>',
                    class_id: '<?php echo $class_id; ?>',
                    '<?php echo csrf_token(); ?>': '<?php echo csrf_hash(); ?>'
                },
                success: function(response) {
                    toastr.success('<?php echo get_phrase('mark_updated_successfully'); ?>');
                },
                error: function() {
                    toastr.error('<?php echo get_phrase('error_updating_mark'); ?>');
                }
            });
        } else {
            toastr.error('<?php echo get_phrase('required_mark_field'); ?>');
        }
    }
</script>
