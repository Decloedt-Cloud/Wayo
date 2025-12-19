<link rel="stylesheet" href="<?php echo base_url(); ?>assets/backend/css/main-responsive.css">

<div class="col-xl-12">
  <div class="header-card">
    <div class="card-body">
      <h4 class="page-title d-inline-block">
        <i class="fas fa-home fa-fw"></i> <?php echo get_phrase('dashboard'); ?>
      </h4>
    </div> <!-- end card body-->
  </div> <!-- end card -->
</div><!-- end col-->


<!-- end page title -->

<div class="mb-3">
  <div class="main-card">
    <div class="card-body">

      <div class="row">
        <div class="container">
          <div class="row justify-content-md-center">
            <div class="col-sm-4">
              <div class="card widget-flat" id="student">
                <div class="card-body">
                  <div class="float-end">
                    <i class="mdi mdi-account-multiple widget-icon"></i>
                  </div>
                  <h5 class="text-muted font-weight-normal mt-0" title="Number of Student"><i class="fas fa-users fa-lg" style="margin-right: 10px;"></i><?php echo get_phrase('students'); ?> <a href="<?php echo route('student'); ?>" style="color: #6c757d; display: none;" id="student_list"><i class="mdi mdi-export"></i></a></h5>
                  <h3 class="mt-3 mb-3">
                    <?php
                    $current_session_students = $this->user_model->get_session_wise_student();
                    echo $current_session_students->num_rows();
                    ?>
                  </h3>
                  <p class="mb-0 text-muted">
                    <span class="text-nowrap"><?php echo get_phrase('total_number_of_student'); ?></span>
                  </p>
                </div> <!-- end card-body-->
              </div> <!-- end card-->
            </div> <!-- end col-->


            <div class="col-sm-4">
              <div class="card widget-flat" id="teacher">
                <div class="card-body">
                  <div class="float-end">
                    <i class="mdi mdi-account-multiple widget-icon"></i>
                  </div>
                  <h5 class="text-muted font-weight-normal mt-0" title="Number of Teacher"><i class="fas fa-chalkboard-teacher fa-lg" style="margin-right: 10px;"></i><?php echo get_phrase('teacher'); ?> <a href="<?php echo route('teacher'); ?>" style="color: #6c757d; display: none;" id="teacher_list"><i class="mdi mdi-export"></i></a></h5>
                  <h3 class="mt-3 mb-3">
                    <?php
                    $teachers = $this->user_model->get_teachers();
                    echo $teachers->num_rows();
                    ?>
                  </h3>
                  <p class="mb-0 text-muted">
                    <span class="text-nowrap"><?php echo get_phrase('total_number_of_teacher'); ?></span>
                  </p>
                </div> <!-- end card-body-->
              </div> <!-- end card-->
            </div> <!-- end col-->

            <div class="col-sm-4">
              <div class="card widget-flat">
                <div class="card-body">
                  <div class="float-end">
                    <i class="mdi mdi-account-multiple widget-icon"></i>
                  </div>
                  <h5 class="text-muted font-weight-normal mt-0" title="Number of Staff"><i class="fas fa-user-tie fa-lg" style="margin-right: 3px;"></i><?php echo get_phrase('staff'); ?></h5>
                  <h3 class="mt-3 mb-3">
                    <?php
                    $accountants = $this->user_model->get_accountants()->num_rows();
                    $librarians = $this->user_model->get_librarians()->num_rows();
                    echo $accountants + $librarians;

                    ?>
                  </h3>
                  <p class="mb-0 text-muted">
                    <span class="text-nowrap"><?php echo get_phrase('total_number_of_staff'); ?></span>
                  </p>
                </div> <!-- end card-body-->
              </div> <!-- end card-->
            </div> <!-- end col-->
          </div>
        </div>
        <!-- end col -->
        <div class="col-xl-12">
          <div class="card bg-primary">
            <div class="card-body">
              <h4 class="header-title text-white mb-2"><?php echo get_phrase('todays_attendance'); ?></h4>
              <div class="text-center">
                <h3 class="font-weight-normal text-white mb-2">
                  <?php echo $this->crud_model->get_todays_attendance(); ?>
                </h3>
                <p class="text-light text-uppercase font-13 font-weight-bold"><?php echo $this->crud_model->get_todays_attendance(); ?> <?php echo get_phrase('students_are_attending_today'); ?></p>
                <a href="<?php echo route('attendance'); ?>" class="btn btn-outline-light btn-sm mb-1"><?php echo get_phrase('go_to_attendance'); ?>
                  <i class="mdi mdi-arrow-right ms-1"></i>
                </a>

              </div>
            </div>
          </div>
          <div class="card">
            <div class="card-body">
              <h4 class="header-title"><?php echo get_phrase('recent_events'); ?><a href="<?php echo route('event_calendar'); ?>" style="color: #6c757d;"><i class="mdi mdi-export"></i></a></h4>
              <?php include 'event.php'; ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div><!-- end col-->



<div class="mb-3">
  <div class="main-card">
    <div class="card-body">
      <div class="row">
        <div class="col-xl-6">
          <div class="card">
            <div class="card-body">
              <h4 class="page-title d-inline-block"><?php echo get_phrase('accounts_of'); ?> <?php echo date('F'); ?> <a href="<?php echo route('invoice'); ?>" style="color: #6c757d"><i class="mdi mdi-export"></i></a></h4>
              <?php include 'invoice.php'; ?>
            </div>
          </div>
        </div>
        <div class="col-xl-6">
          <div class="card">
            <div class="card-body">
              <h4 class="header-title mb-3"> <?php echo get_phrase('expense_of'); ?> <?php echo date('F'); ?> <a href="<?php echo route('expense'); ?>" style="color: #6c757d"><i class="mdi mdi-export"></i></a></h4>
              <?php include 'expense.php'; ?>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>

<script>
  $(document).ready(function() {
    initDataTable("expense-datatable");

  });

  $(".widget-flat").mouseenter(function() {
    var id = $(this).attr('id');
    $('#' + id + '_list').show();
  }).mouseleave(function() {
    var id = $(this).attr('id');
    $('#' + id + '_list').hide();
  });
</script>