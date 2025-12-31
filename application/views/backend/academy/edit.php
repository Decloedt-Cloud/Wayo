<link rel="stylesheet" href="<?php echo base_url(); ?>assets/backend/css/editCourse.css">

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
                                                    <select class="quiz-form-control quiz-select main-form-field" name="class_id[]" id="class_id_add_cours" multiple required>
                                                        <option value="" disabled><?php echo get_phrase('select_classes'); ?></option>
                                                        <?php foreach ($classes->result_array() as $class): ?>
                                                            <option value="<?php echo $class['id']; ?>"
                                                                <?php if (in_array($class['id'], array_column($course_classes, 'id'))) echo 'selected'; ?>>
                                                                <?php echo $class['name']; ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>

                                                <?php if ($this->session->userdata('teacher_login') == 1): ?>
                                                    <input type="hidden" name="user_id" value="<?php echo $this->session->userdata('user_id'); ?>">
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
                                                <h4 class="section-title"><?php echo get_phrase('Learning outcomes'); ?></h4>

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
    });
</script>