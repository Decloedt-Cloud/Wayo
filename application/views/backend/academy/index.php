<!--title-->
 <link rel="stylesheet" href="<?php echo base_url(); ?>assets/backend/css/responsive.css">
<?php if($this->session->userdata('student_login') != 1): ?>
   


<div class="col-xl-12">
    <div class="header-card">
              <div class="card-body">
                <h4 class="page-title d-inline-block">
              <i class="mdi mdi-calendar-range title_icon"></i> <?php echo get_phrase('all_courses'); ?></h4>
              <div class="action-buttons-container">
                  <a href="<?php echo site_url('addons/courses/course_add'); ?>"type="button" class="btn-modern btn btn-outline-primary btn-rounded alignToTitle" > 
                  <i class="mdi mdi-plus"></i> <?php echo get_phrase('create_new_course'); ?></a>
              </div>
          </div> <!-- end card body-->
        </div> <!-- end card -->
  </div><!-- end col-->
 
<?php endif; ?>

    <?php if($this->session->userdata('superadmin_login') == 1 || $this->session->userdata('admin_login') == 1 || $this->session->userdata('teacher_login') == 1): ?>
      <div class="row academy_content"> <?php include 'list.php'; ?></div> 
    <?php else: ?>
      <?php include 'grid_view_for_student.php'; ?>
    <?php endif; ?>
    
<?php include 'common_scripts.php'; ?>