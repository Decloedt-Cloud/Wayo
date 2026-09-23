<?php
$marks = isset($marks) && is_array($marks) ? $marks : [];
$exam_details = isset($exam_details) && is_array($exam_details) ? $exam_details : [];
$class_name = isset($class_name) ? (string) $class_name : '';
$student_names = isset($student_names) && is_array($student_names) ? $student_names : [];
$exam_starting_date = isset($exam_details['starting_date']) ? (int) $exam_details['starting_date'] : null;
?>

<div class="row mb-3">
    <div class="col-md-4"></div>
    <div class="col-md-4 toll-free-box text-center text-white pb-2" style="background-color: #6c757d; border-radius: 10px;">
        <h4><?php echo get_phrase('manage_marks'); ?></h4>
        <span><?php echo get_phrase('Exam name'); ?> : <?php echo html_escape($exam_details['name'] ?? ''); ?></span><br>
        <span><?php echo get_phrase('class'); ?> : <?php echo html_escape($class_name); ?></span><br>
        <span><?php echo get_phrase('exam_date'); ?> : <?php echo $exam_starting_date ? date('D, d-M-Y H:i', $exam_starting_date) : ''; ?></span>
    </div>
</div>
<?php if (count($marks) > 0): ?>
    <div class="table-responsive">
        <table class="table table-bordered table-responsive-sm" width="100%">
            <thead class="thead-dark">
                <tr>
                    <th><i class="mdi mdi-account-multiple-outline thead-icon"></i><?php echo get_phrase('student_name'); ?></th>
                    <th><i class="mdi mdi-numeric thead-icon"></i><?php echo get_phrase('mark'); ?></th>
                    <th class="d-none d-md-table-cell"><i class="mdi mdi-star-circle-outline thead-icon"></i><?php echo get_phrase('grade_point'); ?></th>
                    <th class="d-none d-md-table-cell"><i class="mdi mdi-comment-text-outline thead-icon"></i><?php echo get_phrase('comment'); ?></th>
                    <th class="d-none d-md-table-cell"><i class="mdi mdi-clipboard-check-outline thead-icon"></i><?php echo get_phrase('result'); ?></th>
                    <th>
                        <!-- Version Desktop -->
                        <span class="d-none d-md-inline">
                            <i class="mdi mdi-dots-vertical thead-icon"></i><?php echo get_phrase('action'); ?>
                        </span>
                        <!-- Version Mobile (avec icône) -->
                        <span class="d-md-none">
                            <i class="mdi mdi-chevron-down"></i><?php echo get_phrase('details'); ?>
                        </span>
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($marks as $mark):
                    $student_name = $student_names[(int) $mark['student_id']] ?? '';
                    if ($student_name === '') continue;
                    $mark_on_20 = ($mark['mark_obtained'] / 100) * 20;
                    $mark_on_20 = round($mark_on_20, 2);
                ?>
                    <tr>
                        <td><?php echo html_escape($student_name); ?></td>
                        <td>
                            <input class="form-control readonly" type="text" id="mark-<?php echo $mark['student_id']; ?>" name="mark" placeholder="mark"
                                value="<?php echo $mark_on_20 . '/20'; ?>" readonly>
                        </td>
                        <td class="d-none d-md-table-cell"><span id="grade-for-mark-<?php echo $mark['student_id']; ?>"><?php echo get_grade($mark['mark_obtained']); ?></span></td>
                        <td class="d-none d-md-table-cell">
                            <input class="form-control" type="text" id="comment-<?php echo $mark['student_id']; ?>" name="comment" placeholder="comment"
                                value="<?php echo $mark['comment']; ?>">
                        </td>
                        <td class="text-center d-none d-md-table-cell">
                            <a href="javascript:void(0);"
                                onclick="largeModal('<?php echo site_url('superadmin/exam_results/' . $exam_id . '/' . $mark['student_id']); ?>', '<?php echo get_phrase('exam_results'); ?>')"
                                class="text-primary">
                                <i class="mdi mdi-beaker" style="font-size: 24px;"></i>
                            </a>
                        </td>
                        <td class="text-center">
                            <button class="btn btn-success d-none d-md-inline-block" onclick="comment_update('<?php echo $mark['student_id']; ?>')">
                                <i class="mdi mdi-checkbox-marked-circle"></i>
                            </button>
                            <!-- Bouton "+" visible sur Mobile -->
                            <button class="btn btn-info btn-sm d-md-none expand-button" type="button">+</button>
                        </td>
                    </tr>
                    <!-- AJOUT: Ligne secondaire cachée pour les détails sur mobile -->
                    <tr class="expandable-row d-md-none">
                        <td colspan="3"> <!-- colspan=3 car il y a 3 colonnes visibles sur mobile -->
                            <div class="expanded-details">
                                <div class="detail-item">
                                    <strong><?php echo get_phrase('grade_point'); ?>:</strong>
                                    <span><?php echo get_grade($mark['mark_obtained']); ?></span>
                                </div>
                                <div class="detail-item">
                                    <strong><?php echo get_phrase('comment'); ?>:</strong>
                                    <!-- ID unique pour le champ mobile -->
                                    <input class="form-control" type="text" id="comment-mobile-<?php echo $mark['student_id']; ?>" placeholder="comment" value="<?php echo $mark['comment']; ?>">
                                </div>
                                <div class="detail-item">
                                    <strong><?php echo get_phrase('result'); ?>:</strong>
                                    <a href="javascript:void(0);" onclick="largeModal('<?php echo site_url('superadmin/exam_results/' . $exam_id . '/' . $mark['student_id']); ?>', '<?php echo get_phrase('exam_results'); ?>')" class="text-primary">
                                        <i class="mdi mdi-beaker"></i> <?php echo get_phrase('view_results'); ?>
                                    </a>
                                </div>
                                <div class="detail-item text-center mt-2">
                                    <!-- Le bouton de mise à jour pour la vue mobile -->
                                    <button class="btn btn-success" onclick="comment_update('<?php echo $mark['student_id']; ?>', true)">
                                        <i class="mdi mdi-checkbox-marked-circle"></i> <?php echo get_phrase('update'); ?>
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
    <?php include APPPATH . 'Views/backend/empty.php'; ?>
<?php endif; ?>

<script>
    // AJOUT: Gestionnaire de clic pour le bouton "+"
    $(document).ready(function() {
        $('.expand-button').on('click', function() {
            var expandableRow = $(this).closest('tr').next('.expandable-row');
            expandableRow.toggle(); // Simple toggle, ou .slideToggle() pour une animation

            if ($(this).text() == "+") {
                $(this).text("-");
            } else {
                $(this).text("+");
            }
        });
    });

    function comment_update(student_id) {
        var class_id = '<?php echo $class_id; ?>';

        var exam_id = '<?php echo $exam_id; ?>';
        var comment = is_mobile ? $('#comment-mobile-' + student_id).val() : $('#comment-' + student_id).val();
        var mark_on_20 = $('#mark-' + student_id).val().split('/')[0]; // Extract mark value before /20
        var mark = (mark_on_20 / 20) * 100; // Convert to 100-scale for backend
        var csrfName = $('input[name="<?= csrf_token(); ?>"]').attr('name');
        var csrfHash = $('input[name="<?= csrf_token(); ?>"]').val();

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
                success_notify('<?php echo get_phrase('comment_has_been_updated_successfully'); ?>');
                var newCsrfName = response.csrf.csrfName;
                var newCsrfHash = response.csrf.csrfHash;
                $('input[name="' + newCsrfName + '"]').val(newCsrfHash);
                // Garde les deux champs de commentaire synchronisés
                $('#comment-' + student_id).val(comment);
                $('#comment-mobile-' + student_id).val(comment);
            }
        });
    }
</script>

<style>

    /* AJOUT: CSS pour la ligne de détails */
    .expandable-row {
        display: none;
        /* Cachée par défaut */
    }

    .expanded-details {
        padding: 15px;
        background-color: #f8f9fa;
        border-top: 2px solid #007bff;
        /* Couleur pour mettre en évidence */
    }

    .expanded-details .detail-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 5px;
        border-bottom: 1px solid #e9ecef;
    }

    .expanded-details .detail-item:last-child {
        border-bottom: none;
    }

    .expanded-details strong {
        margin-right: 10px;
        flex-shrink: 0;
        /* Empêche le label de se réduire */
    }

    .expanded-details input,
    .expanded-details a {
        flex-grow: 1;
        /* Permet à l'input ou au lien de prendre l'espace restant */
        text-align: right;
    }
</style>