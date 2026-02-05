<link rel="stylesheet" href="<?php echo base_url(); ?>assets/backend/css/editCourse.min.css">

<!-- Quill Editor -->
<link href="<?php echo base_url(); ?>assets/backend/css/quilljs/quill.snow.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="<?php echo base_url(); ?>assets/backend/js/quilljs/quill.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/backend/js/quilljs/image-resize.min.js"></script>


<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body">

                <div class="header-title-wrapper">
                    <div class="header-title-content">
                        <h4 class="header-title"><?= get_phrase('edit_course') ?></h4>
                    </div>
                    <div class="header-container d-flex justify-content-between align-items-center">
                        <a href="<?= site_url('addons/lessons/play/' . slugify($course['title']) . '/' . $course['id'] . '/' . $first_lesson_id['id']) ?>"
                            class="btn btn-header btn-play"
                            target="_blank">
                            <i class="mdi mdi-play-circle-outline"></i>
                            <?= get_phrase('play_lesson') ?>
                        </a>
                        <a href="<?= site_url('addons/courses') ?>"
                            class="btn btn-header btn-back">
                            <i class="mdi mdi-arrow-left-circle"></i>
                            <?= get_phrase('back_to_course_list') ?>
                        </a>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xl-12">
                        <div class="form-wrapper">
                            <form class="required-form" action="<?php echo site_url('addons/courses/index/update/' . $course['id']); ?>" method="post" enctype="multipart/form-data">
                                <!-- Champ caché pour le jeton CSRF -->
                                <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>" />
                                <div id="basicwizard">

                                    <!-- Top Navigation -->
                                    <div class="course-steps-nav">
                                        <ul class="nav nav-tabs nav-fill border-0">
                                            <li class="nav-item">
                                                <a href="#curriculum" data-bs-toggle="tab" class="nav-link py-3 rounded-0 active">
                                                    <i class="fa-solid fa-book-open"></i>
                                                    <span class="d-none d-sm-inline"><?php echo get_phrase('curriculum'); ?></span>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="#basic" data-bs-toggle="tab" class="nav-link py-3 rounded-0">
                                                    <i class="fa-solid fa-file-lines"></i>
                                                    <span class="d-none d-sm-inline"><?php echo get_phrase('basic'); ?></span>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="#academy" data-bs-toggle="tab" class="nav-link py-3 rounded-0">
                                                    <i class="fa-solid fa-graduation-cap"></i>
                                                    <span class="d-none d-sm-inline"><?php echo get_phrase('academic'); ?></span>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="#outcomes" data-bs-toggle="tab" class="nav-link py-3 rounded-0">
                                                    <i class="fa-solid fa-bullseye"></i>
                                                    <span class="d-none d-sm-inline"><?php echo get_phrase('outcomes'); ?></span>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="#finish" data-bs-toggle="tab" class="nav-link py-3 rounded-0">
                                                    <i class="fa-solid fa-circle-check"></i>
                                                    <span class="d-none d-sm-inline"><?php echo get_phrase('finish'); ?></span>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="tab-content b-0 mb-0">
                                        <div class="tab-pane active show" id="curriculum">
                                            <?php include 'curriculum.php'; ?>
                                        </div>
                                        <div class="tab-pane" id="basic">
                                            <div class="p-lg-5">
                                                <h4 class="section-title"><?php echo get_phrase('Course details'); ?></h4>

                                                <div class="quiz-form-group">
                                                    <label class="quiz-form-label" for="course_title">
                                                        <i class="fas fa-heading"></i>
                                                        <?php echo get_phrase('Course title'); ?> <span class="required">*</span>
                                                    </label>
                                                    <input type="text" value="<?php echo html_escape($course['title']); ?>" class="quiz-form-control main-form-field" id="course_title" name="title" placeholder="<?php echo get_phrase('Enter an engaging course title'); ?>" required>
                                                    <span class="quiz-form-hint"><?php echo get_phrase('A compelling title helps attract more students'); ?></span>
                                                </div>

                                                <div class="quiz-form-group">
                                                    <label class="quiz-form-label" for="basic_description">
                                                        <i class="fas fa-align-left"></i>
                                                        <?php echo get_phrase('Description'); ?>
                                                    </label>
                                                    <div class="quill-editor-wrapper">
                                                        <div id="basic_description_editor"></div>
                                                        <textarea name="description" id="basic_description" style="display:none;"><?php echo $course['description']; ?></textarea>
                                                    </div>
                                                </div>
                                                <div class="quiz-form-group">
                                                    <label class="quiz-form-label" for="course_thumbnail">
                                                        <i class="fas fa-image"></i>
                                                        <?php echo get_phrase('Course thumbnail'); ?>
                                                    </label>

                                                    <div class="thumbnail-upload-modern">
                                                        <div class="thumbnail-preview-wrapper">
                                                             <img src="<?php echo base_url('uploads/course_thumbnail/' . ($course['thumbnail'] ? $course['thumbnail'] : 'placeholder.png')); ?>" id="thumbnail-preview" class="thumbnail-preview-img">
                                                        </div>

                                                        <label for="course_thumbnail" class="btn-upload-thumbnail">
                                                            <i class="fas fa-cloud-upload-alt"></i> <?php echo get_phrase('Choose image'); ?>
                                                        </label>
                                                        <input id="course_thumbnail" type="file" class="d-none" name="course_thumbnail" accept="image/*">
                                                        <span class="quiz-form-hint"><?php echo get_phrase('Recommended size: 800 × 530 pixels'); ?></span>
                                                    </div>
                                                </div>

                                                <!-- Navigation Buttons -->
                                                <div class="d-flex justify-content-end mt-5 custom-navigation-buttons">
                                                    <button type="button" class="btn btn-outline-primary px-4 py-2" onclick="goToNext()">
                                                        <?php echo get_phrase('Next'); ?> <i class="mdi mdi-chevron-right ms-1"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div> <!-- end tab pane -->

                                        <div class="tab-pane" id="academy">
                                            <div class="p-lg-5">
                                                <h4 class="section-title"><?php echo get_phrase('Academic information'); ?></h4>

                                                <div class="quiz-form-group">
                                                    <label class="quiz-form-label" for="class_id">
                                                        <i class="fas fa-graduation-cap"></i>
                                                        <?php echo get_phrase('Class'); ?> <span class="required">*</span>
                                                    </label>

                                                    <?php
                                                    $permitted_class_ids = [];
                                                    if($this->session->userdata('teacher_login') == 1) {
                                                        $user_id = $this->session->userdata('user_id');
                                                        $teacher_data = $this->db->get_where('teachers', ['user_id' => $user_id])->row_array();
                                                        $teacher_id_perm = $teacher_data['id'] ?? null;
                                                        if ($teacher_id_perm) {
                                                            $this->db->select('class_id');
                                                            $this->db->from('teacher_permissions');
                                                            $this->db->where('teacher_id', $teacher_id_perm);
                                                            $this->db->where('attendance', 1);
                                                            $permitted_classes_result = $this->db->get()->result_array();
                                                            $permitted_class_ids = array_column($permitted_classes_result, 'class_id');
                                                        }
                                                    }
                                                    ?>

                                                    <select class="quiz-form-control quiz-select main-form-field" name="class_id[]" id="class_id_add_cours" multiple required>
                                                        <option value="" disabled><?php echo get_phrase('select_classes'); ?></option>
                                                        <?php foreach ($classes->result_array() as $class): ?>
                                                            <?php 
                                                            if($this->session->userdata('teacher_login') == 1 && !in_array($class['id'], $permitted_class_ids)) {
                                                                continue;
                                                            }
                                                            ?>
                                                            <option value="<?php echo $class['id']; ?>"
                                                                <?php if (in_array($class['id'], array_column($course_classes, 'id'))) echo 'selected'; ?>>
                                                                <?php echo $class['name']; ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>

                                                <?php if ($this->session->userdata('teacher_login') == 1): ?>
                                                    <input type="hidden" name="user_id[]" value="<?php echo $this->session->userdata('user_id'); ?>">
                                                <?php else: ?>
                                                    <div class="quiz-form-group">
                                                        <label class="quiz-form-label" for="user_id">
                                                            <i class="fas fa-chalkboard-teacher"></i>
                                                            <?php echo get_phrase('Instructor'); ?> <span class="required">*</span>
                                                        </label>
                                                        <select class="quiz-form-control quiz-select main-form-field" name="user_id[]" id="user_id" multiple required>
                                                            <option value="" disabled><?php echo get_phrase('select_a_teacher'); ?></option>
                                                            <?php foreach ($all_teachers->result_array() as $teacher): ?>
                                                                <option value="<?php echo $teacher['id']; ?>"
                                                                    <?php if (in_array($teacher['id'], array_column($course_teachers, 'id'))) echo 'selected'; ?>>
                                                                    <?php echo $teacher['name']; ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                <?php endif; ?>

                                                <!-- Navigation Buttons -->
                                                <div class="d-flex justify-content-between mt-5 custom-navigation-buttons">
                                                    <button type="button" class="btn btn-outline-primary px-4 py-2" onclick="goToPrevious()">
                                                        <i class="mdi mdi-chevron-left me-1"></i> <?php echo get_phrase('Previous'); ?>
                                                    </button>
                                                    <button type="button" class="btn btn-outline-primary px-4 py-2" onclick="goToNext()">
                                                        <?php echo get_phrase('Next'); ?> <i class="mdi mdi-chevron-right ms-1"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="tab-pane" id="outcomes">
                                            <div class="p-lg-5">
                                                <h4 class="section-title"><?php echo get_phrase('course_objective'); ?></h4>

                                                <div class="quiz-form-group">
                                                    <label class="quiz-form-label" for="outcomes_desc">
                                                        <i class="fas fa-bullseye"></i>
                                                        <?php echo get_phrase('What will students achieve?'); ?>
                                                    </label>
                                                    <div class="quill-editor-wrapper">
                                                        <div id="outcomes_desc_editor"></div>
                                                        <textarea name="outcomes" id="outcomes_desc" style="display:none;"><?php echo $course['outcomes']; ?></textarea>
                                                    </div>
                                                    <span class="quiz-form-hint"><?php echo get_phrase('List specific skills and knowledge students will gain'); ?></span>
                                                </div>

                                                <div class="quiz-form-group">
                                                    <label class="quiz-form-label" for="prerequisites_input">
                                                        <i class="fas fa-list-check"></i>
                                                        <?php echo get_phrase('Prerequisites'); ?>
                                                    </label>
                                                    <div class="tags-input-container">
                                                        <div class="tags-input-wrapper">
                                                            <input type="text" 
                                                                   class="quiz-form-control tags-input" 
                                                                   id="prerequisites_input" 
                                                                   placeholder="<?php echo get_phrase('Type a prerequisite and press Enter'); ?>">
                                                            <div class="tags-list" id="prerequisites_tags"></div>
                                                        </div>
                                                        <input type="hidden" name="prerequisites" id="prerequisites_hidden" value="<?php echo isset($course['prerequisites']) ? htmlspecialchars($course['prerequisites']) : '[]'; ?>">
                                                        <span class="quiz-form-hint"><?php echo get_phrase('Add prerequisites as tags. Press Enter or comma to add a tag'); ?></span>
                                                    </div>
                                                </div>

                                                <div class="quiz-form-group">
                                                    <label class="quiz-form-label" for="field_of_activity">
                                                        <i class="fas fa-layer-group"></i>
                                                        <?php echo get_phrase('field_of_activity'); ?>
                                                    </label>
                                                    <?php
                                                        // Déterminer la valeur par défaut : utiliser le champ d'activité du cours s'il en a un, sinon celui de l'école
                                                        $default_field = isset($course['field_of_activity']) && !empty($course['field_of_activity']) ? $course['field_of_activity'] : (isset($school_category) ? $school_category : '');
                                                    ?>
                                                    <select class="quiz-form-control quiz-select" name="field_of_activity" id="field_of_activity">
                                                        <option value=""><?php echo get_phrase('select_a_field_of_activity'); ?></option>
                                                        <?php if (isset($categories) && !empty($categories)): ?>
                                                            <?php foreach ($categories as $category): ?>
                                                                <option value="<?php echo htmlspecialchars($category['name']); ?>"
                                                                    <?php echo ($default_field == $category['name']) ? 'selected' : ''; ?>>
                                                                    <?php echo htmlspecialchars($category['name']); ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        <?php else: ?>
                                                            <!-- Fallback options if categories table is empty -->
                                                            <option value="technology" <?php echo ($default_field == 'technology') ? 'selected' : ''; ?>><?php echo get_phrase('Technology'); ?></option>
                                                            <option value="business" <?php echo ($default_field == 'business') ? 'selected' : ''; ?>><?php echo get_phrase('Business'); ?></option>
                                                            <option value="marketing" <?php echo ($default_field == 'marketing') ? 'selected' : ''; ?>><?php echo get_phrase('Marketing'); ?></option>
                                                            <option value="design" <?php echo ($default_field == 'design') ? 'selected' : ''; ?>><?php echo get_phrase('Design'); ?></option>
                                                            <option value="soft_skills" <?php echo ($default_field == 'soft_skills') ? 'selected' : ''; ?>><?php echo get_phrase('Soft Skills'); ?></option>
                                                            <option value="languages" <?php echo ($default_field == 'languages') ? 'selected' : ''; ?>><?php echo get_phrase('Languages'); ?></option>
                                                            <option value="health" <?php echo ($default_field == 'health') ? 'selected' : ''; ?>><?php echo get_phrase('Health & Wellness'); ?></option>
                                                            <option value="arts" <?php echo ($default_field == 'arts') ? 'selected' : ''; ?>><?php echo get_phrase('Arts & Creativity'); ?></option>
                                                            <option value="science" <?php echo ($default_field == 'science') ? 'selected' : ''; ?>><?php echo get_phrase('Science'); ?></option>
                                                            <option value="education" <?php echo ($default_field == 'education') ? 'selected' : ''; ?>><?php echo get_phrase('Education'); ?></option>
                                                            <option value="other" <?php echo ($default_field == 'other') ? 'selected' : ''; ?>><?php echo get_phrase('Other'); ?></option>
                                                        <?php endif; ?>
                                                    </select>
                                                    <span class="quiz-form-hint"><?php echo get_phrase('select_the_main_field_of_activity_for_this_course'); ?></span>
                                                </div>

                                                <div class="quiz-form-group">
                                                    <label class="quiz-form-label" for="course_style">
                                                        <i class="fas fa-graduation-cap"></i>
                                                        <?php echo get_phrase('course_style'); ?>
                                                    </label>
                                                    <?php
                                                        // Valeur par défaut pour le style du cours
                                                        $default_course_style = isset($course['course_style']) && !empty($course['course_style']) ? $course['course_style'] : '';
                                                    ?>
                                                    <select class="quiz-form-control quiz-select" name="course_style" id="course_style">
                                                        <option value=""><?php echo get_phrase('select_a_course_style'); ?></option>
                                                        <option value="tutorial_step_by_step" <?php echo ($default_course_style == 'tutorial_step_by_step') ? 'selected' : ''; ?>><?php echo get_phrase('tutorial_step_by_step'); ?></option>
                                                        <option value="reference_documentation" <?php echo ($default_course_style == 'reference_documentation') ? 'selected' : ''; ?>><?php echo get_phrase('reference_documentation'); ?></option>
                                                        <option value="practical_workshop" <?php echo ($default_course_style == 'practical_workshop') ? 'selected' : ''; ?>><?php echo get_phrase('practical_workshop'); ?></option>
                                                        <option value="project_from_a_to_z" <?php echo ($default_course_style == 'project_from_a_to_z') ? 'selected' : ''; ?>><?php echo get_phrase('project_from_a_to_z'); ?></option>
                                                        <option value="case_study" <?php echo ($default_course_style == 'case_study') ? 'selected' : ''; ?>><?php echo get_phrase('case_study'); ?></option>
                                                        <option value="intensive_bootcamp" <?php echo ($default_course_style == 'intensive_bootcamp') ? 'selected' : ''; ?>><?php echo get_phrase('intensive_bootcamp'); ?></option>
                                                        <option value="microlearning" <?php echo ($default_course_style == 'microlearning') ? 'selected' : ''; ?>><?php echo get_phrase('microlearning'); ?></option>
                                                        <option value="blended" <?php echo ($default_course_style == 'blended') ? 'selected' : ''; ?>><?php echo get_phrase('blended'); ?></option>
                                                        <option value="exam_certification_prep" <?php echo ($default_course_style == 'exam_certification_prep') ? 'selected' : ''; ?>><?php echo get_phrase('exam_certification_prep'); ?></option>
                                                        <option value="onboarding_getting_started" <?php echo ($default_course_style == 'onboarding_getting_started') ? 'selected' : ''; ?>><?php echo get_phrase('onboarding_getting_started'); ?></option>
                                                        <option value="troubleshooting_runbook" <?php echo ($default_course_style == 'troubleshooting_runbook') ? 'selected' : ''; ?>><?php echo get_phrase('troubleshooting_runbook'); ?></option>
                                                        <option value="masterclass" <?php echo ($default_course_style == 'masterclass') ? 'selected' : ''; ?>><?php echo get_phrase('masterclass'); ?></option>
                                                    </select>
                                                    <span class="quiz-form-hint"><?php echo get_phrase('Select the teaching methodology for this course'); ?></span>
                                                </div>

                                                <!-- Navigation Buttons -->
                                                <div class="d-flex justify-content-between mt-5 custom-navigation-buttons">
                                                    <button type="button" class="btn btn-outline-primary px-4 py-2" onclick="goToPrevious()">
                                                        <i class="mdi mdi-chevron-left me-1"></i> <?php echo get_phrase('Previous'); ?>
                                                    </button>
                                                    <button type="button" class="btn btn-outline-primary px-4 py-2" onclick="goToNext()">
                                                        <?php echo get_phrase('Next'); ?> <i class="mdi mdi-chevron-right ms-1"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <?php /*
                                        <div class="tab-pane" id="media">
                                            <div class="p-lg-5">
                                                <h4 class="mb-4 text-slate-800 fw-normal"><?php echo get_phrase('Course media'); ?></h4>

                                                <div class="row">
                                                    <div class="col-md-6 mb-4">
                                                        <label class="form-label fw-medium" for="course_overview_provider">
                                                            <?php echo get_phrase('Video provider'); ?> <span class="text-danger"></span>
                                                        </label>
                                                        <select class="form-select border-0 bg-light" name="course_overview_provider" id="course_overview_provider">
                                                            <option value="youtube" <?php if ($course['course_overview_provider'] == 'youtube') echo 'selected'; ?>><?php echo get_phrase('YouTube'); ?></option>
                                                            <option value="vimeo" <?php if ($course['course_overview_provider'] == 'vimeo') echo 'selected'; ?>><?php echo get_phrase('Vimeo'); ?></option>
                                                            <option value="html5" <?php if ($course['course_overview_provider'] == 'html5') echo 'selected'; ?>><?php echo get_phrase('HTML5'); ?></option>
                                                        </select>
                                                    </div>

                                                    <div class="col-md-6 mb-4">
                                                        <label class="form-label fw-medium" for="course_overview_url">
                                                            <?php echo get_phrase('Video URL'); ?> <span class="text-danger"></span>
                                                        </label>
                                                        <input type="text" class="form-control border-0 bg-light" value="<?php echo $course['course_overview_url']; ?>" name="course_overview_url" id="course_overview_url" placeholder="<?php echo get_phrase('https://www.youtube.com/watch?v=example'); ?>">
                                                    </div>
                                                </div>

                                                <div class="mb-4">
                                                    <label class="form-label fw-medium mb-3" for="course_thumbnail">
                                                        <?php echo get_phrase('Course thumbnail'); ?>
                                                    </label>

                                                    <div class="thumbnail-upload bg-light border rounded-3 p-3 text-center">
                                                        <div class="mb-3">
                                                            <img src="<?php echo base_url('uploads/course_thumbnail/' . $course['thumbnail'] ? $course['thumbnail'] : 'placeholder.png'); ?>" id="thumbnail-preview" class="img-fluid rounded shadow-sm" style="max-height: 180px;">
                                                        </div>

                                                        <label for="course_thumbnail" class="btn btn-outline-primary mb-0">
                                                            <i class="mdi mdi-image me-1"></i> <?php echo get_phrase('Choose image'); ?>
                                                        </label>
                                                        <input id="course_thumbnail" type="file" class="d-none" name="course_thumbnail" accept="image/*">
                                                        <input type="hidden" name="current_thumbnail" value="<?php echo $course['thumbnail']; ?>">
                                                        <div class="form-text text-secondary small mt-2">
                                                            <?php echo get_phrase('Recommended size: 800 × 530 pixels'); ?>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Navigation Buttons -->
                                                <div class="d-flex justify-content-between mt-5 custom-navigation-buttons">
                                                    <button type="button" class="btn btn-outline-primary px-4 py-2" onclick="goToPrevious()">
                                                        <i class="mdi mdi-chevron-left me-1"></i> <?php echo get_phrase('Previous'); ?>
                                                    </button>
                                                    <button type="button" class="btn btn-outline-primary px-4 py-2" onclick="goToNext()">
                                                        <?php echo get_phrase('Next'); ?> <i class="mdi mdi-chevron-right ms-1"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>*/ ?>

                                        <div class="tab-pane" id="finish">
                                            <div class="p-lg-5 text-center">
                                                <div class="max-w-sm mx-auto py-4">
                                                    <div class="completion-check bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-4" style="width: 80px; height: 80px;">
                                                        <i class="mdi mdi-check-bold text-success" style="font-size: 40px;"></i>
                                                    </div>

                                                    <h3 class="mb-3 fw-bold text-dark" ><?php echo get_phrase('Ready to update course'); ?></h3>
                                                    <p class="text-secondary mb-4">
                                                        <?php echo get_phrase('Please review all information before submitting. Your course details will be updated.'); ?>
                                                    </p>

                                                    <button type="button" class="btn btn-success px-5 py-2 fw-medium" id="update-course-button">
                                                        <?php echo get_phrase('Update course'); ?>
                                                    </button>
                                                </div>

                                                <!-- Navigation Buttons -->
                                                <div class="d-flex justify-content-start mt-5 custom-navigation-buttons">
                                                    <button type="button" class="btn btn-outline-primary px-4 py-2" onclick="goToPrevious()">
                                                        <i class="mdi mdi-chevron-left me-1"></i> <?php echo get_phrase('Previous'); ?>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                    </div> <!-- tab-content -->
                                </div> <!-- end #progressbarwizard-->
                            </form>
                        </div>
                    </div><!-- end row-->
                </div> <!-- end card-body-->
            </div> <!-- end card-->
        </div>
    </div>
</div>
<?php include 'common_scripts.php'; ?>
<style media="screen">
    body {
        overflow-x: hidden;
    }
    
    /* Styles pour les tags/badges */
    .tags-input-container {
        position: relative;
    }

    .tags-input-wrapper {
        position: relative;
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        padding: 0.5rem;
        background-color: #fff;
        min-height: 46px;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 0.5rem;
    }

    .tags-input-wrapper:focus-within {
        border-color: #3b76e1;
        box-shadow: 0 0 0 0.2rem rgba(59, 118, 225, 0.25);
    }

    .tags-input {
        border: none !important;
        outline: none !important;
        box-shadow: none !important;
        flex: 1;
        min-width: 120px;
        padding: 0.25rem 0.5rem !important;
        background: transparent !important;
    }

    .tags-input::placeholder {
        color: #6c757d;
        opacity: 0.7;
    }

    .tags-list {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        align-items: center;
        justify-content: flex-start;
        width: 100%;
    }

    .tag-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        padding: 4px 8px;
        background-color: #4f46e5;
        color: #ffffff;
        border-radius: 7px;
        font-size: 0.875rem;
        font-weight: 500;
        line-height: 1.5;
        border: 1px solid #dee2e6;
        transition: all 0.2s ease;
        margin: 2px;
    }


    .tag-remove {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background-color: #4f46e5;
        color:rgb(255, 255, 255);
        font-weight: bold;
        cursor: pointer;
        transition: all 0.2s ease;
        border: none;
        padding: 0;
        margin-right: 5px;
        font-size: 0.635rem;
        order: -1;
    }
    /* Style pour le champ quand il est vide */
    .tags-input-wrapper:empty::before {
        content: attr(data-placeholder);
        color: #6c757d;
        opacity: 0.7;
    }

    /* Animation pour l'ajout de tags */
    @keyframes tagAdd {
        0% {
            opacity: 0;
            transform: scale(0.8);
        }
        100% {
            opacity: 1;
            transform: scale(1);
        }
    }

    .tag-badge {
        animation: tagAdd 0.2s ease-out;
    }

    /* Style pour les tags dans le mode édition */
    .tag-badge.editable {
        cursor: pointer;
        background-color: #f8f9fa;
        border-color: #ced4da;
    }

    .tag-badge.editable:hover {
        background-color: #e9ecef;
        border-color: #adb5bd;
    }
</style>

<script type="text/javascript">
    // Quill Editor instances
    let descriptionEditor, outcomesEditor;

    /**
     * Navigation to the next form tab.
     */
    function goToNext() {
        syncQuillEditors();
        const currentTabLink = document.querySelector('.nav-link.active');
        const nextTabListItem = currentTabLink.closest('li').nextElementSibling;
        if (nextTabListItem) {
            const nextTabLink = nextTabListItem.querySelector('.nav-link');
            const nextTab = new bootstrap.Tab(nextTabLink);
            nextTab.show();
        }
    }

    /**
     * Navigation to the previous form tab.
     */
    function goToPrevious() {
        syncQuillEditors();
        const currentTabLink = document.querySelector('.nav-link.active');
        const prevTabListItem = currentTabLink.closest('li').previousElementSibling;
        if (prevTabListItem) {
            const prevTabLink = prevTabListItem.querySelector('.nav-link');
            const prevTab = new bootstrap.Tab(prevTabLink);
            prevTab.show();
        }
    }

    // Update the tab icons based on active state
    function updateIcons() {
        document.querySelectorAll('.course-steps-nav .nav-link').forEach(tab => {
            const icon = tab.querySelector('i');

            if (tab.classList.contains('active')) {
                icon.classList.remove('text-muted');
                icon.classList.add('text-primary');
            } else {
                icon.classList.remove('text-primary');
                icon.classList.add('text-muted');
            }
        });
    }

    $(document).ready(function() {
        initQuillEditors();
        initThumbnailPreview();
        initDefaultSelect2();
        updateTabIcons();
        $('.course-steps-nav .nav-link').on('click', function() {
            setTimeout(updateTabIcons, 50);
        });
    });

    /**
     * Sync Quill editors content to hidden textareas
     */
    function syncQuillEditors() {
        if (descriptionEditor) {
            document.getElementById('basic_description').value = descriptionEditor.root.innerHTML;
        }
        if (outcomesEditor) {
            document.getElementById('outcomes_desc').value = outcomesEditor.root.innerHTML;
        }
    }

    /**
     * Initializes Quill rich text editors.
     */
    function initQuillEditors() {
        // Quill toolbar configuration
        const toolbarOptions = [
            [{ 'header': [1, 2, 3, false] }],
            ['bold', 'italic', 'underline', 'strike'],
            [{ 'color': [] }, { 'background': [] }],
            [{ 'list': 'ordered'}, { 'list': 'bullet' }],
            [{ 'indent': '-1'}, { 'indent': '+1' }],
            [{ 'align': [] }],
            ['link', 'image', 'video'],
            ['blockquote', 'code-block'],
            ['clean']
        ];

        // Register image resize module if available
        if (window.ImageResize) {
            Quill.register('modules/imageResize', window.ImageResize.default || window.ImageResize);
        }

        // Description editor
        descriptionEditor = new Quill('#basic_description_editor', {
            theme: 'snow',
            placeholder: '<?php echo get_phrase("Describe_what_students_will_learn_in_this_course"); ?>',
            modules: {
                toolbar: toolbarOptions,
                imageResize: window.ImageResize ? {} : undefined
            }
        });

        // Set initial content from hidden textarea
        const descriptionContent = document.getElementById('basic_description').value;
        if (descriptionContent) {
            descriptionEditor.root.innerHTML = descriptionContent;
        }

        // Outcomes editor
        outcomesEditor = new Quill('#outcomes_desc_editor', {
            theme: 'snow',
            placeholder: '<?php echo get_phrase("List_specific_skills_and_knowledge_students_will_gain"); ?>',
            modules: {
                toolbar: toolbarOptions,
                imageResize: window.ImageResize ? {} : undefined
            }
        });

        // Set initial content from hidden textarea
        const outcomesContent = document.getElementById('outcomes_desc').value;
        if (outcomesContent) {
            outcomesEditor.root.innerHTML = outcomesContent;
        }

        // Auto-sync on text change
        descriptionEditor.on('text-change', function() {
            document.getElementById('basic_description').value = descriptionEditor.root.innerHTML;
        });

        outcomesEditor.on('text-change', function() {
            document.getElementById('outcomes_desc').value = outcomesEditor.root.innerHTML;
        });
    }


    function initThumbnailPreview() {
        $('#course_thumbnail').change(function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#thumbnail-preview').attr('src', e.target.result);
                }
                reader.readAsDataURL(this.files[0]);
            }
        });
    }

    /**
     * Initializes Select2 for all quiz-select elements.
     */
    function initDefaultSelect2() {
        if ($.fn.select2) {
            $('.quiz-select').select2({
                width: '100%'
            });
        }
    }
    
    /**
     * Updates the icons of the navigation tabs based on their active state.
     */
    function updateTabIcons() {
        document.querySelectorAll('.course-steps-nav .nav-link').forEach(tab => {
            const icon = tab.querySelector('i');
            if (tab.classList.contains('active')) {
                icon.classList.remove('text-muted');
                icon.classList.add('text-primary');
            } else {
                icon.classList.remove('text-primary');
                icon.classList.add('text-muted');
            }
        });
    }
    
    // Form validation function for EDIT page
    function validateAndSubmitEditForm() {
            // Sync Quill content before validation
            syncQuillEditors();
            
            var isValid = true;
            var firstInvalidField = null;
            
            // Check only required fields with class 'main-form-field' (exclude curriculum modal fields)
            $('form.required-form').find('.main-form-field[required]').each(function() {
                if ($(this).prop('required')) {
                    var $field = $(this);
                    var value = $field.val();
                    var isEmpty = false;
                    
                    // For Select2 fields
                    if ($field.hasClass('select2-hidden-accessible') && $.fn.select2) {
                        try {
                            if ($field.is('select[multiple]')) {
                                value = $field.select2('val');
                                isEmpty = !value || value.length === 0;
                            } else {
                                value = $field.select2('val');
                                isEmpty = !value || value === '';
                            }
                        } catch (select2Error) {
                            if ($field.is('select[multiple]')) {
                                isEmpty = !value || value.length === 0;
                            } else if ($field.is('select')) {
                                isEmpty = !value || value === '';
                            } else {
                                isEmpty = !value || value === '';
                            }
                        }
                    } else {
                        if ($field.is('select[multiple]')) {
                            isEmpty = !value || value.length === 0;
                        } else if ($field.is('select')) {
                            isEmpty = !value || value === '';
                        } else {
                            isEmpty = !value || value === '';
                        }
                    }
                    
                    if (isEmpty) {
                        isValid = false;
                        $field.addClass('is-invalid');
                        
                        if (!firstInvalidField) {
                            firstInvalidField = $field;
                        }
                    } else {
                        $field.removeClass('is-invalid');
                    }
                }
            });
            
            if (firstInvalidField) {
                var tabId = firstInvalidField.closest('.tab-pane').attr('id');
                if (tabId) {
                    $('.nav-link[href="#' + tabId + '"]').tab('show');
                }
            }
            
            if (isValid) {
                $('form.required-form').submit();
            } else {
                toastr.error('<?php echo get_phrase("Please fill all required fields"); ?>');
            }
    }
    
    // Attach event to update button
    $(document).ready(function() {
        $('#update-course-button').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            validateAndSubmitEditForm();
            return false;
        });
        
        // Initialiser le système de tags pour l'édition
        initTagsSystemForEdit();
    });

    /**
     * Initialise le système de tags pour la page d'édition
     */
    function initTagsSystemForEdit() {
        const tagsInput = document.getElementById('prerequisites_input');
        const tagsContainer = document.getElementById('prerequisites_tags');
        const hiddenInput = document.getElementById('prerequisites_hidden');
        
        let tags = [];
        
        // Charger les tags existants depuis le champ caché
        try {
            const existingTags = JSON.parse(hiddenInput.value || '[]');
            tags = Array.isArray(existingTags) ? existingTags : [];
        } catch (e) {
            console.error('Error parsing prerequisites:', e);
            tags = [];
        }
        
        // Fonction pour mettre à jour le champ caché
        function updateHiddenInput() {
            hiddenInput.value = JSON.stringify(tags);
        }
        
        // Fonction pour créer un badge de tag
        function createTagBadge(tag) {
            const badge = document.createElement('span');
            badge.className = 'tag-badge';
            badge.innerHTML = `
                ${tag}
                <button type="button" class="tag-remove" aria-label="Remove tag">
                    <i class="fas fa-times"></i>
                </button>
            `;
            
            // Ajouter l'événement de suppression
            const removeBtn = badge.querySelector('.tag-remove');
            removeBtn.addEventListener('click', function() {
                const index = tags.indexOf(tag);
                if (index > -1) {
                    tags.splice(index, 1);
                    badge.remove();
                    updateHiddenInput();
                }
            });
            
            return badge;
        }
        
        // Fonction pour ajouter un tag
        function addTag(tagText) {
            const tag = tagText.trim();
            
            // Vérifier si le tag n'est pas vide et n'existe pas déjà
            if (tag && !tags.includes(tag)) {
                tags.push(tag);
                
                // Créer et ajouter le badge
                const badge = createTagBadge(tag);
                tagsContainer.appendChild(badge);
                
                // Mettre à jour le champ caché
                updateHiddenInput();
                
                // Réinitialiser l'input
                tagsInput.value = '';
            }
        }
        
        // Afficher les tags existants
        tags.forEach(tag => {
            const badge = createTagBadge(tag);
            tagsContainer.appendChild(badge);
        });
        
        // Gérer l'événement keydown sur l'input
        tagsInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ',') {
                e.preventDefault();
                const tagText = this.value;
                if (tagText.trim()) {
                    addTag(tagText);
                }
            }
            
            // Supprimer le dernier tag avec Backspace si l'input est vide
            if (e.key === 'Backspace' && this.value === '' && tags.length > 0) {
                const lastTag = tags[tags.length - 1];
                const index = tags.indexOf(lastTag);
                if (index > -1) {
                    tags.splice(index, 1);
                    const lastBadge = tagsContainer.lastElementChild;
                    if (lastBadge) {
                        lastBadge.remove();
                    }
                    updateHiddenInput();
                }
            }
        });
        
        // Gérer l'événement blur (perte de focus)
        tagsInput.addEventListener('blur', function() {
            const tagText = this.value.trim();
            if (tagText) {
                addTag(tagText);
            }
        });
        
        // Gérer l'événement paste
        tagsInput.addEventListener('paste', function(e) {
            e.preventDefault();
            const pastedText = e.clipboardData.getData('text');
            const tagsArray = pastedText.split(/[,;\n]/);
            
            tagsArray.forEach(tag => {
                const trimmedTag = tag.trim();
                if (trimmedTag) {
                    addTag(trimmedTag);
                }
            });
        });
        
        // Initialiser le champ caché
        updateHiddenInput();
    }
</script>