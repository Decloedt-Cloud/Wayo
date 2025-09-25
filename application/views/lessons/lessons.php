<link rel="stylesheet" href="<?php echo base_url(); ?>assets/backend/css/custom/navbar.css">
<?php
$course_details = $this->lms_model->get_course_by_id($course_id);
?>
<div class="navbar-custom topnav-navbar navbar-color topnav-navbar-dark">
<div class="container-fluid course_container">
    <!-- Top bar -->
    <div class="row align-items-center">
        <div class="col-lg-9  ">
            <h5>
                <img src="<?php echo $this->settings_model->get_logo_light(); ?>" alt="" height="40">
                <?php echo get_phrase('online_course'); ?> |
                <?php echo $course_details['title']; ?>
            </h5>
        </div>
        <div class="col-lg-3">
            <a href="javascript::" class="course_btn btn btn-outline-dark website-button ms-2 d-none d-md-inline-block website-button" onclick="toggle_lesson_view()"><i
                    class="dripicons-expand"></i></a>
            <a href="<?php echo site_url('addons/courses'); ?>" class="course_btn btn btn-outline-dark website-button ms-2 d-none d-md-inline-block website-button"> <i class="dripicons-backspace"></i>
                <?php echo get_phrase('back_to_courses'); ?></a>
        </div>
    </div>
    </div>
    </div>
    <div class="container-fluid mt-5">
        <div class="row justify-content-center" id="lesson-container">
            <?php if (isset($lesson_id)): ?>
                <!-- Course content, video, quizes, files starts-->
                <?php include 'course_content_body.php'; ?>
                <!-- Course content, video, quizes, files ends-->
            <?php endif; ?>

            <!-- Course sections and lesson selector sidebar starts-->
            <?php include 'course_content_sidebar.php'; ?>
            <!-- Course sections and lesson selector sidebar ends-->
        </div>
    </div>

            