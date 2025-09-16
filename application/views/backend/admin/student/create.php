<link rel="stylesheet" href="<?php echo base_url(); ?>assets/backend/css/responsive.css">
<!--title-->

<div class="col-xl-12">
    <div class="header-card">
             <div class="card-body">
                <h4 class="page-title d-inline-block">
                      <i class="mdi mdi-account-multiple-plus title_icon"></i> <?php echo get_phrase('student_admission_form'); ?>
                  </h4>
              </div> <!-- end card body-->
          </div> <!-- end card -->
      </div><!-- end col-->

<div class="mb-3">
      <div class="main-card">
        <div class="card-body">
            <ul class="nav nav-pills bg-nav-pills nav-justified mb-3">
                <li class="nav-item">
                    <a href="<?php echo route('student/create'); ?>" class="nav-link rounded-0 <?php if($aria_expand == 'single') echo 'active'; ?>">
                        <i class="mdi mdi-account-plus-outline d-lg-none d-block me-1"></i>
                        <span class="d-none d-lg-block"><?php echo get_phrase('single_student_admission'); ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo route('student/create/bulk'); ?>" class="nav-link rounded-0 <?php if($aria_expand == 'bulk') echo 'active'; ?>">
                        <i class="mdi mdi-account-multiple-plus-outline d-lg-none d-block me-1"></i>
                        <span class="d-none d-lg-block"><?php echo get_phrase('bulk_student_admission'); ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo route('student/create/excel'); ?>" class="nav-link rounded-0 <?php if($aria_expand == 'excel') echo 'active'; ?>">
                        <i class="mdi mdi-file-excel-outline d-lg-none d-block me-1"></i>
                        <span class="d-none d-lg-block"><?php echo get_phrase('excel_upload'); ?></span>
                    </a>
                </li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane active">
                    <?php
                        if($aria_expand == 'single'):
                            include 'single_student_admission.php';
                        elseif($aria_expand == 'bulk'):
                            include 'bulk_student_admission.php';
                        elseif($aria_expand == 'excel'):
                            include 'excel_student_admission.php';
                        endif;
                    ?>
                </div>
            </div>
        </div>
    </div>
    </div>
