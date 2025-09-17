<div class="row mb-3">
  <div class="col-md-4"></div>
  <div class="col-md-4 toll-free-box text-center text-white pb-2" style="background-color: #6c757d; border-radius: 10px;">
    <h4><?php echo get_phrase('manage_marks'); ?></h4>
    <span><?php echo get_phrase('class'); ?> : <?php echo $this->db->get_where('classes', array('id' => $class_id))->row('name'); ?></span><br>
  </div>
</div>
<?php
$school_id = school_id();
$marks = $this->crud_model->get_marks($class_id, $exam_id, $school_id)->result_array();
$check_permission = has_permission($class_id, 'marks');
?>
<?php if ($check_permission): ?>
  <?php if (count($marks) > 0): ?>
    <table class="table table-bordered table-responsive-sm" width="100%">
      <thead class="thead-dark">
        <tr>
          <th><i class="mdi mdi-account-multiple-outline thead-icon"></i><?php echo get_phrase('student_name'); ?></th>
          <th><i class="mdi mdi-numeric thead-icon"></i><?php echo get_phrase('mark'); ?></th>
          <th class="d-none d-md-table-cell"><i class="mdi mdi-star-circle-outline thead-icon"></i><?php echo get_phrase('grade_point'); ?></th>
          <th class="d-none d-md-table-cell"><i class="mdi mdi-comment-text-outline thead-icon"></i><?php echo get_phrase('comment'); ?></th>
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
          $student = $this->db->get_where('students', array('id' => $mark['student_id']))->row_array(); ?>
          <tr>
            <td><?php echo $this->user_model->get_user_details($student['user_id'], 'name'); ?></td>
            <td><input class="form-control" type="number" id="mark-<?php echo $mark['student_id']; ?>" name="mark" placeholder="mark" min="0" value="<?php echo $mark['mark_obtained']; ?>" required onchange="get_grade(this.value, this.id)"></td>
            <td class="d-none d-md-table-cell"><span id="grade-for-mark-<?php echo $mark['student_id']; ?>"><?php echo get_grade($mark['mark_obtained']); ?></span> </td>
            <td class="d-none d-md-table-cell"><input class="form-control" type="text" id="comment-<?php echo $mark['student_id']; ?>" name="comment" placeholder="comment" value="<?php echo $mark['comment']; ?>"></td>
            <td class="text-center">
              <!-- CORRECTION 1: Ajout des classes pour cacher ce bouton sur mobile -->
              <button class="btn btn-success d-none d-md-inline-block" onclick="mark_update('<?php echo $mark['student_id']; ?>')"><i class="mdi mdi-checkbox-marked-circle"></i></button>
              <!-- Version Mobile -->
              <button class="btn btn-info btn-sm d-md-none expand-button" type="button">+</button>
            </td>
          </tr>
          <!-- AJOUT: Ligne secondaire cachée pour les détails sur mobile -->
          <tr class="expandable-row d-md-none">
            <td colspan="3"> <!-- colspan=3 car il y a 3 colonnes visibles sur mobile -->
              <div class="expanded-details">
                <div class="detail-item">
                  <strong><?php echo get_phrase('grade_point'); ?>:</strong>
                  <span id="grade-for-mark-mobile-<?php echo $mark['student_id']; ?>"><?php echo get_grade($mark['mark_obtained']); ?></span>
                </div>
                <div class="detail-item">
                  <strong><?php echo get_phrase('comment'); ?>:</strong>
                  <input class="form-control" type="text" id="comment-mobile-<?php echo $mark['student_id']; ?>" placeholder="comment" value="<?php echo $mark['comment']; ?>">
                </div>
                <div class="detail-item text-center mt-2">
                  <!-- Le bouton de mise à jour pour la vue mobile -->
                  <button class="btn btn-success" onclick="mark_update('<?php echo $mark['student_id']; ?>', true)">
                    <i class="mdi mdi-checkbox-marked-circle"></i> <?php echo get_phrase('update'); ?>
                  </button>
                </div>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php else: ?>
    <?php include APPPATH . 'views/backend/empty.php'; ?>
  <?php endif; ?>
<?php else: ?>
  <div class="col-md-12 text-center">
    <div class="alert alert-danger" role="alert">
      <h4 class="alert-heading"><?php echo get_phrase('access_denied'); ?>!</h4>
      <hr>
      <p class="mb-0"><?php echo get_phrase('sorry_you_are_not_permitted_to_access_this_view') . '. <br/>' . get_phrase('admin_handles_it'); ?>.</p>
    </div>
  </div>
<?php endif; ?>


<script>
  $(document).ready(function() {
    $('.expand-button').on('click', function() {
      var expandableRow = $(this).closest('tr').next('.expandable-row');
      expandableRow.toggle();
      $(this).text($(this).text() == '+' ? '-' : '+');
    });
  });

  function mark_update(student_id, is_mobile = false) {
    var class_id = '<?php echo $class_id; ?>';
    var exam_id = '<?php echo $exam_id; ?>';
    var mark = $('#mark-' + student_id).val();

    // Choisit le bon champ de commentaire (desktop ou mobile)
    var comment = is_mobile ? $('#comment-mobile-' + student_id).val() : $('#comment-' + student_id).val();

    var csrfName = $('input[name="<?= $this->security->get_csrf_token_name(); ?>"]').attr('name');
    var csrfHash = $('input[name="<?= $this->security->get_csrf_token_name(); ?>"]').val();

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
        dataType: 'json', // S'assurer que la réponse est traitée comme JSON
        success: function(response) {
          success_notify('<?php echo get_phrase('mark_has_been_updated_successfully'); ?>');

          // Mise à jour du jeton CSRF
          var newCsrfName = response.csrf.csrfName;
          var newCsrfHash = response.csrf.csrfHash;
          $('input[name="' + newCsrfName + '"]').val(newCsrfHash);

          // Garder les deux champs de commentaire synchronisés
          $('#comment-' + student_id).val(comment);
          $('#comment-mobile-' + student_id).val(comment);
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
        // Met à jour le grade dans les deux vues (desktop et mobile)
        $('#grade-for-' + id).text(response);
        $('#grade-for-mark-mobile-' + id.split('-')[1]).text(response);
      }
    });
  }
</script>

<style>
  .form-control.readonly {
    background-color: #e9ecef;
    cursor: not-allowed;
  }

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