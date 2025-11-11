<!-- start page title -->
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/backend/css/main-responsive.css">
<?php
$user_id = $this->session->userdata('user_id');
$student_data = $this->db->get_where('students', array('user_id' => $user_id));

if ($student_data->num_rows() == 0) {
?>
  <div class="alert alert-warning-community text-center" role="alert" style="font-size: 15px;">
    <i class="fas fa-exclamation-triangle"></i>
    <?php echo get_phrase('no_course_or_school'); ?>
    <strong><a style="color: black; font-weight: bold;text-decoration: underline !important;" target="_blank" href="<?php echo site_url('home/communities'); ?>"><?php echo get_phrase('click_here'); ?></a>
    </strong>.
  </div>
<?php
}
?>
<div class="row ">

  <div class="col-xl-12">
    <div class="header-card">
      <div class="card-body">
        <h4 class="page-title d-inline-block">
          <i class="fas fa-home fa-fw"></i> <?php echo get_phrase('dashboard'); ?>
        </h4>
      </div> <!-- end card body-->
    </div> <!-- end card -->
  </div><!-- end col-->
</div>

<div class="mb-3">
  <div class="main-card">
    <div class="card-body">

      <div class="row">
        <div class="container">
          <div class="row justify-content-md-center">
            <div class="col-lg-6">
              <div class="card widget-flat" id="student">
                <div class="card-body">
                  <div class="float-end">
                    <i class="mdi mdi-account-multiple widget-icon"></i>
                  </div>
                  <h5 class="text-muted font-weight-normal mt-0" title="Number of Student"> <i class="fas fa-people-arrows fa-lg" style="margin-right: 4px;"></i> <?php echo get_phrase('schools'); ?> <a href="" style="color: #6c757d; display: none;" id="student_list"><i class="mdi mdi-export"></i></a></h5>
                  <h3 class="mt-3 mb-3">
                    <?php

                    echo $student_data->num_rows();
                    ?>
                  </h3>
                  <p class="mb-0 text-muted">
                    <span class="text-nowrap"><?php echo get_phrase('total_number_of_school'); ?></span>
                  </p>
                </div> <!-- end card-body-->
              </div> <!-- end card-->
            </div> <!-- end col-->

            <div class="col-lg-6">
              <div class="card widget-flat" id="teacher">
                <div class="card-body">
                  <div class="float-end">
                    <i class="mdi mdi-account-multiple widget-icon"></i>
                  </div>
                  <h5 class="text-muted font-weight-normal mt-0" title="Number of Teacher"> <i class="fas fa-chalkboard fa-lg" style="margin-right: 10px;"></i><?php echo get_phrase('classes'); ?> <a href="" style="color: #6c757d; display: none;" id="teacher_list"><i class="mdi mdi-export"></i></a></h5>
                  <h3 class="mt-3 mb-3">
                    <?php
                    if ($student_data->num_rows() > 0) {
                      $student_list =  $student_data->result_array();
                      $student_ids = array();
                      foreach ($student_list as $student) {
                        if (!in_array($student['id'], $student_ids)) {
                          array_push($student_ids, $student['id']);
                        }
                      }

                      $this->db->where_in('student_id', $student_ids);
                      $student_cours = $this->db->get('enrols')->num_rows();
                      echo $student_cours;
                    } else {
                      echo $student_data->num_rows();
                    }

                    ?>
                  </h3>
                  <p class="mb-0 text-muted">
                    <span class="text-nowrap"><?php echo get_phrase('total_number_of_class'); ?></span>
                  </p>
                </div> <!-- end card-body-->
              </div> <!-- end card-->
            </div> <!-- end col-->
          </div> <!-- end row -->


        </div> <!-- end col -->
        <div class="col-xl-4">
          <div class="card">
            <div class="card-body">
              <h4 class="header-title"><?php echo get_phrase('recent_events'); ?><a href="<?php echo route('event_calendar'); ?>" style="color: #6c757d;"><i class="mdi mdi-export"></i></a></h4>
              <?php include 'event.php'; ?>
            </div>
          </div>
        </div>
      </div>
    </div><!-- end col-->
  </div>
</div>
<div class="col-xl-12">
  <div class="header-card">
    <div class="card-body">
      <h4 class="page-title d-inline-block">
        <i class="fas fa-globe fa-fw"></i> <?php echo get_phrase('social'); ?>
      </h4>
    </div>
  </div>
</div>

<div class="iframe-container">

  <iframe src="<?= $iframe_url ?>"
    width="100%"
    height="800"
    frameborder="0"
    allowfullscreen>
  </iframe>
</div>

<script>
  $(document).ready(function() {
    initDataTable("expense-datatable");
  });
</script>