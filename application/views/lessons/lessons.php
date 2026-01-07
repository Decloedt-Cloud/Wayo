<link rel="stylesheet" href="<?php echo base_url(); ?>assets/backend/css/custom/navbar.css">
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/backend/css/curriculum.css">
<style>
/* Gestion globale des textes longs */
.lesson-container, #lesson-container {
    overflow-x: hidden;
}
.course_container h5 {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    max-width: 100%;
}

/* Play Lesson Specific Styles */
.curriculum-container {
    padding-top: 30px;
    align-items: flex-start;
    min-height: calc(100vh - 80px); /* Adjust for navbar */
}

.play-lesson-body {
    flex: 3;
    min-width: 0;
    animation: slideIn 0.3s ease;
}

.play-lesson-sidebar {
    flex: 1;
    min-width: 300px;
    max-width: 400px;
    animation: slideIn 0.3s ease;
}

/* Override editor-card for play lesson context */
.play-lesson-card {
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08), 0 4px 12px rgba(0, 0, 0, 0.05);
    border: 1px solid #e8ecf1;
    overflow: hidden;
    height: 100%;
}

@media (max-width: 992px) {
    .curriculum-container {
        flex-direction: column;
    }
    .play-lesson-sidebar {
        max-width: 100%;
        width: 100%;
    }
}

/* Force embedded videos in summary/note to be full width */
.preview-content iframe,
.preview-content video,
.preview-content embed,
.preview-content object {
    width: 100% !important;
    max-width: 100% !important;
    min-height: 400px;
    height: auto !important;
    aspect-ratio: 16/9;
    border-radius: 8px;
}
</style>
<?php
$course_details = $this->lms_model->get_course_by_id($course_id);
?>
<div class="navbar-custom topnav-navbar navbar-color topnav-navbar-dark">
    <div class="container-fluid course_container">
        <!-- Top bar -->
        <div class="row align-items-center">
            <div class="col-lg-9  ">
                <h5 title="<?php echo htmlspecialchars($course_details['title']); ?>">
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

<div class="curriculum-container" id="lesson-container">
    <?php if (isset($lesson_id)): ?>
        <!-- Course content, video, quizes, files starts-->
        <div class="play-lesson-body">
            <?php include 'course_content_body.php'; ?>
        </div>
        <!-- Course content, video, quizes, files ends-->
    <?php endif; ?>

    <!-- Course sections and lesson selector sidebar starts-->
    <div class="play-lesson-sidebar">
        <?php include 'course_content_sidebar.php'; ?>
    </div>
    <!-- Course sections and lesson selector sidebar ends-->
</div>
