<?php  if (isset($enrolments)): ?>
  <?php if (count($enrolments) > 0): ?>
    <div class="row justify-content-md-center">
            <div class="col-md-4 mt-2">
                <div class="card text-white bg-secondary">
                    <div class="card-body">
                        <div class="toll-free-box text-center">
                       
                            <h4> <i class="mdi mdi-chart-bar-stacked"></i> <?php echo get_phrase('promote_student'); ?></h4>
                            <h5><?php echo get_phrase('class_from').': '.$class_from_details['name'].' '.get_phrase('to').' : '.$class_to_details['name']; ?></h5>
                            <h5><?php echo get_phrase('session_from').': '.$session_from_details['name'].' '.get_phrase('to').' : '.$session_to_details['name']; ?></h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
       
        <div class="row justify-content-md-center">
            <div class="col-xl-8 col-lg-8 col-md-12 col-sm-12 mb-3 mb-lg-0">
                <div class="table-responsive-sm">
                    <table class="table table-bordered table-striped dt-responsive nowrap" width="100%">
                        <thead class="thead-dark">
                        <tr>
                            <th><i class="mdi mdi-account-circle-outline thead-icon"></i><?php echo get_phrase('image'); ?></th>
                            <th><i class="mdi mdi-account-multiple-outline thead-icon"></i><?php echo get_phrase('student_name'); ?></th>
                             <th class="d-none d-md-table-cell"><i class="mdi mdi-checkbox-marked-circle-outline thead-icon"></i><?php echo get_phrase('status'); ?></th>
                               <th>
                            <span class="d-none d-md-inline"><i class="mdi mdi-dots-vertical thead-icon"></i><?php echo get_phrase('action'); ?></span>
                            <span class="d-md-none"><i class="mdi mdi-chevron-down"></i><?php echo get_phrase('details'); ?></span>
                        </th>
                        </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($enrolments as $enrolment):
                            
                                  $student_details = $this->user_model->get_student_details_by_id('student', $enrolment['student_id']);
                                 ?>
                                    <tr>
                                  <td class="text-center">
                                    <img src="<?php echo $this->user_model->get_user_image($student_details['user_id']); ?>" height="50" alt=""><br>
                                  </td>
                                  <td>
                                    <?php echo $student_details['name']; ?>
                                    <br>
                                    <small><b><?php echo get_phrase('student_code'); ?>:</b><?php echo $student_details['code']; ?></small>
                                  </td>
                                  <td class="text-ce d-none d-md-table-cell">
                                      <span class="badge badge-info-lighten" id = "success_<?php echo $student_details['id']; ?>" style="display: none;"><?php echo get_phrase('prmoted'); ?></span>
                                      <span class="badge bg-secondary"  id = "danger_<?php echo $student_details['id'] ?>"><?php echo get_phrase('not_promoted_yet'); ?></span>
                                  </td>
                                  <td style="text-align: center;">
                                     <!-- Boutons visibles uniquement sur grand écran -->
                                  <div class="d-none d-md-block">
                                      <button type="button" class="btn btn-icon btn-success btn-sm" onclick="enrollStudent('<?php echo $student_details['id'].'-'.$class_id_to.'-'.$session_to; ?>', '<?php echo $enrolment['id']; ?>')"> <?php echo get_phrase('enroll_to'); ?> <strong> <?php echo $class_to_details['name']; ?> </strong> </button>
                                      <button type="button" class="btn btn-icon btn-secondary btn-sm" onclick="enrollStudent('<?php echo $student_details['id'].'-'.$class_id_from.'-'.$session_to; ?>', '<?php echo $enrolment['id']; ?>')"> <?php echo get_phrase('enroll_to'); ?> <strong> <?php echo $class_from_details['name']; ?> </strong> </button>
                                    </div>
                                    <!-- Bouton "+" visible uniquement sur mobile -->
                                  <button class="btn btn-info btn-sm d-md-none expand-button" type="button">+</button>
                                  </td>
                              </tr>
                                <!-- Ligne dépliable pour les actions sur mobile -->
                          <tr class="expandable-row d-md-none">
                              <td colspan="3"> <!-- 4 colonnes visibles sur mobile -->
                                  <div class="expanded-details">
                                     <div class="detail-item">
                                          <strong><?php echo get_phrase('status'); ?>:</strong>
                                          <div>
                                              <span class="badge badge-info-lighten" id="success_<?php echo $student_details['id']; ?>" style="display: none;"><?php echo get_phrase('promoted'); ?></span>
                                              <span class="badge bg-secondary" id="danger_<?php echo $student_details['id'] ?>"><?php echo get_phrase('not_promoted_yet'); ?></span>
                                          </div>
                                      </div>

                                      <div class="detail-item justify-content-center">
                                          <button type="button" class="btn btn-success btn-sm m-1" onclick="enrollStudent('<?php echo $student_details['id'].'-'.$class_id_to.'-'.$session_to; ?>', '<?php echo $enrolment['id']; ?>')"> <?php echo get_phrase('enroll_to'); ?> <strong> <?php echo $class_to_details['name']; ?> </strong> </button>
                                          <button type="button" class="btn btn-secondary btn-sm m-1" onclick="enrollStudent('<?php echo $student_details['id'].'-'.$class_id_from.'-'.$session_to; ?>', '<?php echo $enrolment['id']; ?>')"> <?php echo get_phrase('enroll_to'); ?> <strong> <?php echo $class_from_details['name']; ?> </strong> </button>
                                      </div>
                                  </div>
                              </td>
                          </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
  <?php else: ?>
      <?php include APPPATH.'views/backend/empty.php'; ?>
  <?php endif; ?>
<?php else: ?>
  <?php include APPPATH.'views/backend/empty.php'; ?>
<?php endif; ?>
<script>
$(document).ready(function() {
    $('.expand-button').on('click', function() {
        $(this).closest('tr').next('.expandable-row').toggle();
        $(this).text($(this).text() == '+' ? '-' : '+');
    });
});
</script>
<style>
   .expandable-row {
        display: none;
    }
    .expanded-details {
        padding: 10px;
        background-color: #f1f3f7;
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
    .table td .badge {
        white-space: normal;
    }
</style>