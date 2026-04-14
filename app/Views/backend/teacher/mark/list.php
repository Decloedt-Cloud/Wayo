<?php
$marks = isset($marks) && is_array($marks) ? $marks : [];
$exam_details = isset($exam_details) && is_array($exam_details) ? $exam_details : [];
$class_name = isset($class_name) ? (string) $class_name : '';
$student_names = isset($student_names) && is_array($student_names) ? $student_names : [];
$check_permission = isset($check_permission) ? (bool) $check_permission : has_permission($class_id, 'marks');
$exam_starting_date = isset($exam_details['starting_date']) ? (int) $exam_details['starting_date'] : null;

// Calculate Stats
$total_students = count($marks);
$total_score = 0;
$highest_score = 0;
$lowest_score = 100; // Initial high value

foreach ($marks as $mark) {
    $score = $mark['mark_obtained'];
    $total_score += $score;
    if ($score > $highest_score) $highest_score = $score;
    if ($score < $lowest_score) $lowest_score = $score;
}

// Adjust lowest score if no students
if ($total_students == 0) $lowest_score = 0;

$average_score = $total_students > 0 ? round($total_score / $total_students, 2) : 0;
?>

<?php if ($check_permission): ?>

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
                        <th><i class="mdi mdi-format-annotation-plus thead-icon"></i> <?php echo get_phrase('mark'); ?></th>
                        <th class="d-none d-md-table-cell"><i class="mdi mdi-star-circle-outline thead-icon"></i> <?php echo get_phrase('grade_point'); ?></th>
                        <th class="d-none d-md-table-cell"><i class="mdi mdi-comment-text-outline thead-icon"></i> <?php echo get_phrase('comment'); ?></th>
                        <th><i class="mdi mdi-dots-horizontal thead-icon"></i> <?php echo get_phrase('action'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($marks as $mark):
                        $student_name = $student_names[(int) $mark['student_id']] ?? '';
                        if ($student_name === '') continue;
                    ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="font-weight-bold"><?php echo html_escape($student_name); ?></span>
                            </div>
                        </td>
                        <td>
                            <input class="form-control-modern" type="number" id="mark-<?php echo $mark['student_id']; ?>" name="mark" 
                                   placeholder="mark" min="0" value="<?php echo $mark['mark_obtained']; ?>" required 
                                   onchange="get_grade(this.value, 'mark-<?php echo $mark['student_id']; ?>')" style="width: 100px; text-align: center;">
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
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <button class="mark-action-btn mark-btn-success d-none d-md-inline-flex" onclick="mark_update('<?php echo $mark['student_id']; ?>')" title="<?php echo get_phrase('update'); ?>">
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
                        <td colspan="5">
                            <div class="p-3">
                                <div class="mb-2">
                                    <strong><?php echo get_phrase('grade_point'); ?>:</strong>
                                    <span class="badge badge-light-primary" id="grade-for-mark-mobile-<?php echo $mark['student_id']; ?>"><?php echo get_grade($mark['mark_obtained']); ?></span>
                                </div>
                                <div class="mb-2">
                                    <strong><?php echo get_phrase('comment'); ?>:</strong>
                                    <input class="form-control-modern mt-1" type="text" id="comment-mobile-<?php echo $mark['student_id']; ?>" value="<?php echo $mark['comment']; ?>">
                                </div>
                                <div class="mb-3">
                                    <button class="btn-modern btn-modern-primary btn-sm w-100" onclick="mark_update('<?php echo $mark['student_id']; ?>', true)">
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

<?php else: ?>
    <div class="alert alert-danger" role="alert" style="border-radius: 10px; margin-top: 20px;">
        <h4 class="alert-heading"><i class="mdi mdi-alert-circle-outline"></i> <?php echo get_phrase('access_denied'); ?></h4>
        <p class="mb-0"><?php echo get_phrase('sorry_you_are_not_permitted_to_access_this_view') . '. ' . get_phrase('admin_handles_it'); ?></p>
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

    function mark_update(student_id, is_mobile = false) {
        var class_id = '<?php echo $class_id; ?>';
        var exam_id = '<?php echo $exam_id; ?>';
        var mark = $('#mark-' + student_id).val();

        // Choose the correct comment field
        var comment = is_mobile ? $('#comment-mobile-' + student_id).val() : $('#comment-' + student_id).val();

        // Get CSRF Token
        var csrfName = $('input[name="<?= csrf_token(); ?>"]').attr('name');
        var csrfHash = $('input[name="<?= csrf_token(); ?>"]').val();

        if (class_id != "" && mark != "") {
            $.ajax({
                type: 'POST',
                url: '<?php echo route('mark/mark_update'); ?>',
                data: {
                    student_id: student_id,
                    class_id: class_id,
                    exam_id: exam_id,
                    mark: mark,
                    comment: comment,
                    [csrfName]: csrfHash
                },
                dataType: 'json',
                success: function(response) {
                    success_notify('<?php echo get_phrase('mark_has_been_updated_successfully'); ?>');

                    // Update CSRF Token
                    if(response.csrf) {
                        var newCsrfName = response.csrf.csrfName;
                        var newCsrfHash = response.csrf.csrfHash;
                        $('input[name="' + newCsrfName + '"]').val(newCsrfHash);
                    }

                    // Sync comment fields
                    $('#comment-' + student_id).val(comment);
                    $('#comment-mobile-' + student_id).val(comment);
                },
                error: function() {
                    toastr.error('<?php echo get_phrase('error_updating_mark'); ?>');
                }
            });
        } else {
            toastr.error('<?php echo get_phrase('mark_field_is_required'); ?>');
        }
    }

    function get_grade(exam_mark, id) {
        $.ajax({
            url: '<?php echo route('get_grade'); ?>/' + exam_mark,
            success: function(response) {
                // Update grade in both views
                var student_id = id.split('-')[1];
                $('#grade-for-' + id).text(response);
                $('#grade-for-mark-mobile-' + student_id).text(response);
            }
        });
    }
</script>

<style>
    .form-control-modern.readonly {
        background-color: #e9ecef;
        cursor: not-allowed;
    }

    /* Mobile Details Styles */
    .expandable-row {
        display: none;
    }

    .mark-table tbody tr.expandable-row td {
        padding: 0;
        border-bottom: 1px solid var(--mark-border);
    }
    
    .mark-table tbody tr.expandable-row:hover {
        background-color: transparent;
    }

    /* Override padding for the content div inside */
    .expandable-row > td > div {
        padding: 1.5rem;
    }
</style>