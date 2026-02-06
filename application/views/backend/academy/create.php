<link rel="stylesheet" href="<?php echo base_url(); ?>assets/backend/css/createCourse.min.css">

<!-- Quill Editor -->
<link href="<?php echo base_url(); ?>assets/backend/css/quilljs/quill.snow.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="<?php echo base_url(); ?>assets/backend/js/quilljs/quill.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/backend/js/quilljs/image-resize.min.js"></script>

<style>
/* Style pour les champs invalides */
.is-invalid {
    border: 1px solid #ff5b5b !important; /* Bordure rouge */
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
    border-radius: 12px;
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
    color:rgb(255, 255, 255);
    font-weight: bold;
    cursor: pointer;
    transition: all 0.2s ease;
    border: none;
    padding: 0;
    margin-right: 5px;
    font-size: 0.875rem;
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

<!--<div class="container-fluid p-0">-->
<!-- Main Content Container -->

<div class="row">

    <div class="col-xl-12">
        <div class="card">
            <div class="card-body">
                <div class="header-container d-flex justify-content-between align-items-center">
                    <h4 class="header-title mb-0">
                        <?php echo get_phrase('add_new_course'); ?>
                    </h4>
                    <div class="action-buttons-container">
                        <a href="<?php echo site_url('addons/courses'); ?>" class="btn-modern btn btn-back ">
                            <i class="mdi mdi-arrow-left-circle"></i>
                            <?php echo get_phrase('back_to_course_list'); ?>
                        </a>
                    </div>
                </div>
                <div class="form-wrapper">
                    <!-- Top Navigation -->
                    <div class="course-steps-nav">
                        <ul class="nav nav-tabs nav-fill border-0">
                            <li class="nav-item">
                                <a href="#basic" data-bs-toggle="tab" class="nav-link py-3 rounded-0 active">
                                    <i class="fa-solid fa-file-lines"></i>
                                    <span><?php echo get_phrase('Basic'); ?></span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#academy" data-bs-toggle="tab" class="nav-link py-3 rounded-0">
                                    <i class="fa-solid fa-graduation-cap"></i>
                                    <span><?php echo get_phrase('Academic'); ?></span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#outcomes" data-bs-toggle="tab" class="nav-link py-3 rounded-0">
                                    <i class="fa-solid fa-bullseye"></i>
                                    <span><?php echo get_phrase('Outcomes'); ?></span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#finish" data-bs-toggle="tab" class="nav-link py-3 rounded-0">
                                    <i class="fa-solid fa-circle-check"></i>
                                    <span><?php echo get_phrase('Finish'); ?></span>
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- Form Content -->
                    <form class="required-form" action="<?php echo site_url('addons/courses/index/create'); ?>" method="post" enctype="multipart/form-data">
                        <!-- CSRF Token -->
                        <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>" />

                        <div class="tab-content">
                            <!-- Basic Tab -->
                            <div class="tab-pane fade show active" id="basic">
                                <div class="p-4 p-lg-5">
                                    <h4 class="section-title"><?php echo get_phrase('Course details'); ?></h4>

                                    <div class="quiz-form-group">
                                        <label class="quiz-form-label" for="course_title">
                                            <i class="fas fa-heading"></i>
                                            <?php echo get_phrase('Course title'); ?> <span class="required">*</span>
                                        </label>
                                        <input type="text" class="quiz-form-control" id="course_title" name="title" placeholder="<?php echo get_phrase('Enter an engaging course title'); ?>" required>
                                        <span class="quiz-form-hint"><?php echo get_phrase('A compelling title helps attract more students'); ?></span>
                                        <div class="invalid-feedback"><?php echo get_phrase('This_field_is_required.'); ?></div>
                                    </div>

                                    <div class="quiz-form-group">
                                        <label class="quiz-form-label" for="basic_description">
                                            <i class="fas fa-align-left"></i>
                                            <?php echo get_phrase('Description'); ?>
                                        </label>
                                        <div class="quill-editor-wrapper">
                                            <div id="basic_description_editor"></div>
                                            <textarea name="description" id="basic_description" style="display:none;"></textarea>
                                        </div>
                                    </div>
                                    <div class="quiz-form-group">
                                        <label class="quiz-form-label" for="course_thumbnail">
                                            <i class="fas fa-image"></i>
                                            <?php echo get_phrase('Course thumbnail'); ?>
                                        </label>

                                        <div class="thumbnail-upload-modern">
                                            <div class="thumbnail-preview-wrapper">
                                                <img src="<?php echo base_url('uploads/course_thumbnail/placeholder.png'); ?>" id="thumbnail-preview" class="thumbnail-preview-img">
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
                            </div>

                            <!-- Academic Tab -->
                            <div class="tab-pane fade" id="academy">
                                <div class="p-4 p-lg-5">
                                    <h4 class="section-title"><?php echo get_phrase('Academic information'); ?></h4>

                                    <div class="quiz-form-group">
                                        <label class="quiz-form-label" for="class_id_add_cours">
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

                                        <select class="quiz-form-control quiz-select" 
                                                name="class_id[]" 
                                                id="class_id_add_cours" 
                                                multiple 
                                                required>
                                            <option value="" disabled><?php echo get_phrase('select_classes'); ?></option>
                                            <?php foreach ($classes->result_array() as $class): ?>
                                                <?php 
                                                if($this->session->userdata('teacher_login') == 1 && !in_array($class['id'], $permitted_class_ids)) {
                                                    continue;
                                                }
                                                ?>
                                                <option value="<?php echo $class['id']; ?>">
                                                    <?php echo $class['name']; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>

                                        <div class="invalid-feedback">
                                            <?php echo get_phrase('Please_select_at_least_one_class'); ?>
                                        </div>
                                    </div>

                                    <?php if ($this->session->userdata('teacher_login') == 1): ?> 
                                        <input type="hidden" name="user_id[]" value="<?php echo $this->session->userdata('user_id'); ?>">
                                    <?php else: ?>

                                        <div class="quiz-form-group">
                                            <label class="quiz-form-label" for="user_id">
                                                <i class="fas fa-chalkboard-teacher"></i>
                                                <?php echo get_phrase('Instructor'); ?> <span class="required">*</span>
                                            </label>

                                            <select class="quiz-form-control quiz-select" 
                                                    name="user_id[]" 
                                                    id="user_id" 
                                                    multiple 
                                                    required>
                                                <option value="" disabled><?php echo get_phrase('select_a_teacher'); ?></option>
                                                <?php foreach ($all_teachers->result_array() as $teacher): ?>
                                                    <option value="<?php echo $teacher['id']; ?>">
                                                        <?php echo $teacher['name']; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>

                                            <div class="invalid-feedback">
                                                <?php echo get_phrase('Please_select_at_least_one_instructor'); ?>
                                            </div>
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

                            <!-- Outcomes Tab -->
                            <div class="tab-pane fade" id="outcomes">
                                <div class="p-4 p-lg-5">
                                    <h4 class="section-title"><?php echo get_phrase('course_objective'); ?></h4>

                                    <div class="quiz-form-group">
                                        <label class="quiz-form-label" for="outcomes_desc">
                                            <i class="fas fa-bullseye"></i>
                                            <?php echo get_phrase('What will students achieve?'); ?>
                                        </label>
                                        <div class="quill-editor-wrapper">
                                            <div id="outcomes_desc_editor"></div>
                                            <textarea name="outcomes" id="outcomes_desc" style="display:none;"></textarea>
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
                                            <input type="hidden" name="prerequisites" id="prerequisites_hidden">
                                            <span class="quiz-form-hint"><?php echo get_phrase('Add prerequisites as tags. Press Enter or comma to add a tag'); ?></span>
                                        </div>
                                    </div>

                                    <div class="quiz-form-group">
                                        <label class="quiz-form-label" for="field_of_activity">
                                            <i class="fas fa-layer-group"></i>
                                            <?php echo get_phrase('field_of_activity'); ?>
                                        </label>
                                        <select class="quiz-form-control quiz-select" name="field_of_activity" id="field_of_activity">
                                            <option value=""><?php echo get_phrase('select_a_field_of_activity'); ?></option>
                                            <?php if (isset($categories) && !empty($categories)): ?>
                                                <?php foreach ($categories as $category): ?>
                                                    <option value="<?php echo htmlspecialchars($category['name']); ?>"
                                                        <?php echo ($school_category == $category['name']) ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($category['name']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <!-- Fallback options if categories table is empty -->
                                                <option value="technology" <?php echo ($school_category == 'technology') ? 'selected' : ''; ?>><?php echo get_phrase('Technology'); ?></option>
                                                <option value="business" <?php echo ($school_category == 'business') ? 'selected' : ''; ?>><?php echo get_phrase('Business'); ?></option>
                                                <option value="marketing" <?php echo ($school_category == 'marketing') ? 'selected' : ''; ?>><?php echo get_phrase('Marketing'); ?></option>
                                                <option value="design" <?php echo ($school_category == 'design') ? 'selected' : ''; ?>><?php echo get_phrase('Design'); ?></option>
                                                <option value="soft_skills" <?php echo ($school_category == 'soft_skills') ? 'selected' : ''; ?>><?php echo get_phrase('Soft Skills'); ?></option>
                                                <option value="languages" <?php echo ($school_category == 'languages') ? 'selected' : ''; ?>><?php echo get_phrase('Languages'); ?></option>
                                                <option value="health" <?php echo ($school_category == 'health') ? 'selected' : ''; ?>><?php echo get_phrase('Health & Wellness'); ?></option>
                                                <option value="arts" <?php echo ($school_category == 'arts') ? 'selected' : ''; ?>><?php echo get_phrase('Arts & Creativity'); ?></option>
                                                <option value="science" <?php echo ($school_category == 'science') ? 'selected' : ''; ?>><?php echo get_phrase('Science'); ?></option>
                                                <option value="education" <?php echo ($school_category == 'education') ? 'selected' : ''; ?>><?php echo get_phrase('Education'); ?></option>
                                                <option value="other" <?php echo ($school_category == 'other') ? 'selected' : ''; ?>><?php echo get_phrase('Other'); ?></option>
                                            <?php endif; ?>
                                        </select>
                                        <span class="quiz-form-hint"><?php echo get_phrase('select_the_main_field_of_activity_for_this_course'); ?></span>
                                    </div>

                                    <div class="quiz-form-group">
                                        <label class="quiz-form-label" for="course_style">
                                            <i class="fas fa-graduation-cap"></i>
                                            <?php echo get_phrase('course_style'); ?>
                                        </label>
                                        <select class="quiz-form-control quiz-select" name="course_style" id="course_style">
                                            <option value=""><?php echo get_phrase('select_a_course_style'); ?></option>
                                            <option value="tutorial_step_by_step"><?php echo get_phrase('tutorial_step_by_step'); ?></option>
                                            <option value="reference_documentation"><?php echo get_phrase('reference_documentation'); ?></option>
                                            <option value="practical_workshop"><?php echo get_phrase('practical_workshop'); ?></option>
                                            <option value="project_from_a_to_z"><?php echo get_phrase('project_from_a_to_z'); ?></option>
                                            <option value="case_study"><?php echo get_phrase('case_study'); ?></option>
                                            <option value="intensive_bootcamp"><?php echo get_phrase('intensive_bootcamp'); ?></option>
                                            <option value="microlearning"><?php echo get_phrase('microlearning'); ?></option>
                                            <option value="blended"><?php echo get_phrase('blended'); ?></option>
                                            <option value="exam_certification_prep"><?php echo get_phrase('exam_certification_prep'); ?></option>
                                            <option value="onboarding_getting_started"><?php echo get_phrase('onboarding_getting_started'); ?></option>
                                            <option value="troubleshooting_runbook"><?php echo get_phrase('troubleshooting_runbook'); ?></option>
                                            <option value="masterclass"><?php echo get_phrase('masterclass'); ?></option>
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
                            <!-- Media Tab -->
                            <div class="tab-pane fade" id="media">
                                <div class="p-4 p-lg-5">
                                    <h4 class="mb-4 text-slate-800 fw-normal"><?php echo get_phrase('Course media'); ?></h4>

                                    <div class="row">
                                        <div class="col-md-6 mb-4">
                                            <label class="form-label fw-medium" for="course_overview_provider">
                                                <?php echo get_phrase('Video provider'); ?> <span class="text-danger"></span>
                                            </label>
                                            <select class="form-select border-0 bg-light" name="course_overview_provider" id="course_overview_provider">
                                                <option value="youtube"><?php echo get_phrase('YouTube'); ?></option>
                                                <option value="vimeo"><?php echo get_phrase('Vimeo'); ?></option>
                                                <option value="html5"><?php echo get_phrase('HTML5'); ?></option>
                                            </select>
                                        </div>

                                        <div class="col-md-6 mb-4">
                                            <label class="form-label fw-medium" for="course_overview_url">
                                                <?php echo get_phrase('Video URL'); ?> <span class="text-danger"></span>
                                            </label>
                                            <input type="text" class="form-control border-0 bg-light" name="course_overview_url" id="course_overview_url" placeholder="<?php echo get_phrase('https://www.youtube.com/watch?v=example'); ?>">
                                        </div>
                                    </div>

                                    <div class="mb-4">
                                        <label class="form-label fw-medium mb-3" for="course_thumbnail">
                                            <?php echo get_phrase('Course thumbnail'); ?>
                                        </label>

                                        <div class="thumbnail-upload bg-light border rounded-3 p-3 text-center">
                                            <div class="mb-3">
                                                <img src="<?php echo base_url('uploads/course_thumbnail/placeholder.png'); ?>" id="thumbnail-preview" class="img-fluid rounded shadow-sm" style="max-height: 180px;">
                                            </div>

                                            <label for="course_thumbnail" class="btn btn-outline-primary mb-0">
                                                <i class="mdi mdi-image me-1"></i> <?php echo get_phrase('Choose image'); ?>
                                            </label>
                                            <input id="course_thumbnail" type="file" class="d-none" name="course_thumbnail" accept="image/*">
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
                            </div>
                            */ ?>

                            <!-- Finish Tab -->
                            <div class="tab-pane fade" id="finish">
                                <div class="p-4 p-lg-5 text-center">
                                    <div class="max-w-sm mx-auto py-4">
                                        <div class="completion-check bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-4" style="width: 80px; height: 80px;">
                                            <i class="mdi mdi-check-bold text-success" style="font-size: 40px;"></i>
                                        </div>

                                        <h3 class="mb-3 fw-bold text-dark"><?php echo get_phrase('Ready to create course'); ?>
                                            <span class="alert-modern space-between-icon " role="alert">
                                                <span class="icon flex-shrink-0"
                                                    data-bs-toggle="popover"
                                                    data-bs-trigger="hover focus"
                                                    data-bs-content="<?php echo get_phrase("don't_forget_to_add_your_content_to_your_course."); ?>"
                                                    data-bs-placement="top">
                                                    <i class="dripicons-information"></i>
                                                </span>
                                            </span>
                                        </h3>
                                        <p class="text-secondary mb-4">
                                            <?php echo get_phrase('Please_review_all_information_before_submitting._Your_course_will_be_available_after_approval.'); ?>
                                        </p>
                                        <div class="d-flex justify-content-between align-items-center mt-5 flex-wrap custom-navigation-buttons">
                                            <!-- Previous -->
                                            <button type="button" class="btn btn-outline-primary px-4 py-2" onclick="goToPrevious()">
                                                <i class="mdi mdi-chevron-left me-1"></i> <?php echo get_phrase('Previous'); ?>
                                            </button>
                                            <input type="hidden" name="status" id="course_status" value="inactive">
                                            <!-- Switch + Submit -->
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="form-check form-switch m-0">
                                                    <input class="form-check-input big-switch" type="checkbox" id="courseActiveSwitch">

                                                </div>
                                                <label class="form-check-label switch text-danger" for="courseActiveSwitch">
                                                    <?php echo get_phrase('inactive'); ?>
                                                </label>
                                                <button type="button" class="btn btn-success px-5 py-2 fw-medium" onclick="checkRequiredFields()">
                                                    <?php echo get_phrase('Submit_course'); ?>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript">
    // Quill Editor instances
    let descriptionEditor, outcomesEditor;

    /**
     * Validate all required fields inside the current tab.
     * Returns true if all fields are valid, false otherwise.
     */
    function validateCurrentTab() {
        let isValid = true;

        const currentPane = document.querySelector('.tab-pane.fade.show.active');

        const requiredFields = currentPane.querySelectorAll('input[required], select[required], textarea[required]');

        requiredFields.forEach(field => {
            if (!field.value || field.value.trim() === '') {
                field.classList.add('is-invalid');
                isValid = false;

                const feedback = field.parentElement.querySelector('.invalid-feedback');
                if (feedback) feedback.style.display = 'block';
            } else {
                field.classList.remove('is-invalid');

                const feedback = field.parentElement.querySelector('.invalid-feedback');
                if (feedback) feedback.style.display = 'none';
            }
        });

        return isValid;
    }


    /**
     * Navigation to the next form tab.
     */
    function goToNext() {
        // Sync Quill content before validation
        syncQuillEditors();

        if (!validateCurrentTab()) {
            toastr.error('<?php echo get_phrase("Please_fill_all_required_fields"); ?>');
            return;
        }

        const currentTab = document.querySelector('.nav-link.active');
        const nextTab = currentTab.parentElement.nextElementSibling?.querySelector('.nav-link');

        if (nextTab) {
            const currentPane = document.querySelector('.tab-pane.fade.show.active');
            const nextPane = document.querySelector(nextTab.getAttribute('href'));

            currentPane.classList.remove('show', 'active');
            currentTab.classList.remove('active');

            nextPane.classList.add('fade', 'show', 'active');
            nextTab.classList.add('active');

            updateTabIcons();

            window.scrollTo(0, 0);
        }
    }


    /**
     * Navigation to the previous form tab.
     */
    function goToPrevious() {
        syncQuillEditors();
        
        const currentTab = document.querySelector('.nav-link.active');
        const prevTab = currentTab.parentElement.previousElementSibling?.querySelector('.nav-link');

        if (prevTab) {
            const currentPane = document.querySelector('.tab-pane.fade.show.active');
            const prevPane = document.querySelector(prevTab.getAttribute('href'));

            currentPane.classList.remove('show', 'active');
            currentTab.classList.remove('active');

            prevPane.classList.add('fade', 'show', 'active');
            prevTab.classList.add('active');

            updateTabIcons();

            window.scrollTo(0, 0);
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


    $(document).ready(function() {
        initQuillEditors();
        initThumbnailPreview();
        initDefaultSelect2();
        updateTabIcons();

        $('.course-steps-nav .nav-link').on('click', function() {
            setTimeout(updateTabIcons, 50);
        });

        // Listen for class selection change
        $('#class_id_add_cours').on('change', function() {
            var classIds = $(this).val();
            var userSelect = $('#user_id');
            
            // Only if user_id select exists (it might not exist for teacher login)
            if (userSelect.length === 0) return;
            
            // Get current CSRF token from hidden input
            var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>';
            var csrfHash = $('input[name="' + csrfName + '"]').val();

            if (!classIds || classIds.length === 0) {
                userSelect.html('<option value="" disabled><?php echo get_phrase("select_a_teacher"); ?></option>');
                return;
            }
            
            $.ajax({
                url: '<?php echo site_url("addons/courses/get_teachers_by_class_ids"); ?>',
                type: 'POST',
                data: {
                    class_ids: classIds,
                    [csrfName]: csrfHash
                },
                dataType: 'json',
                success: function(response) {
                    // Update CSRF token
                    if(response.csrfName && response.csrfHash) {
                        $('input[name="' + response.csrfName + '"]').val(response.csrfHash);
                    }
                    
                    var options = '<option value="" disabled><?php echo get_phrase("select_a_teacher"); ?></option>';
                    if (response.teachers && response.teachers.length > 0) {
                        $.each(response.teachers, function(index, teacher) {
                            options += '<option value="' + teacher.id + '">' + teacher.name + '</option>';
                        });
                    }
                    userSelect.html(options);
                },
                error: function() {
                    console.error('Error fetching teachers');
                }
            });
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

        // Outcomes editor
        outcomesEditor = new Quill('#outcomes_desc_editor', {
            theme: 'snow',
            placeholder: '<?php echo get_phrase("List_specific_skills_and_knowledge_students_will_gain"); ?>',
            modules: {
                toolbar: toolbarOptions,
                imageResize: window.ImageResize ? {} : undefined
            }
        });

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
     * Initializes Select2
     */
    function initDefaultSelect2() {
        if ($.fn.select2) {
            $('.quiz-select').select2({
                width: '100%'
            });
        }
    }


    /**
     * Final validation on submit.
     */
    function checkRequiredFields() {
        // Sync Quill content before validation
        syncQuillEditors();
        
        let isValid = true;
        $('form.required-form').find('input, select, textarea').each(function() {
            if ($(this).prop('required') && !$(this).val()) {
                isValid = false;
                $(this).addClass('is-invalid');

                const tabId = $(this).closest('.tab-pane').attr('id');
                $('.nav-link[href="#' + tabId + '"]').tab('show');
                return false;
            } else {
                $(this).removeClass('is-invalid');
            }
        });

        if (isValid) {
            $('form.required-form').submit();
        } else {
            toastr.error('<?php echo get_phrase("Please_fill_all_required_fields"); ?>');
        }
    }


    const activeText = "<?php echo get_phrase('active'); ?>";
    const inactiveText = "<?php echo get_phrase('inactive'); ?>";

    const courseSwitch = document.getElementById('courseActiveSwitch');
    const courseStatus = document.getElementById('course_status');
    const switchLabel = document.querySelector('label.switch');

    switchLabel.textContent = inactiveText;
    courseStatus.value = 'inactive';

    courseSwitch.addEventListener('change', function() {
        if (this.checked) {
            switchLabel.textContent = activeText;
            courseStatus.value = 'active';
            switchLabel.classList.add('text-success');
            switchLabel.classList.remove('text-danger');
        } else {
            switchLabel.textContent = inactiveText;
            courseStatus.value = 'inactive';
            switchLabel.classList.add('text-danger');
            switchLabel.classList.remove('text-success');
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
        
        // Initialiser le système de tags
        initTagsSystem();
    });

    /**
     * Initialise le système de tags pour les prérequis
     */
    function initTagsSystem() {
        const tagsInput = document.getElementById('prerequisites_input');
        const tagsContainer = document.getElementById('prerequisites_tags');
        const hiddenInput = document.getElementById('prerequisites_hidden');
        
        let tags = [];
        
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
