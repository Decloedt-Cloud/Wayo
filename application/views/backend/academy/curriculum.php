<link rel="stylesheet" href="<?php echo base_url(); ?>assets/backend/css/curriculum.min.css">

<style>
.preview-actions {
    display: flex;
    align-items: center;
    gap: 8px;
}
</style>

<!-- SweetAlert2 -->


<!-- Quill Editor 1.3.7 (compatible avec les extensions) -->
<link href="<?php echo base_url(); ?>assets/backend/css/quilljs/quill.snow.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="<?php echo base_url(); ?>assets/backend/js/quilljs/quill.min.js"></script>

<!-- Quill Image Resize -->
<script type="text/javascript" src="<?php echo base_url(); ?>assets/backend/js/quilljs/image-resize.min.js"></script>

<!-- Global console filter for imageResize logs -->
<script type="text/javascript">
(function() {
    // Store original console methods
    const originalConsoleLog = console.log;
    const originalConsoleWarn = console.warn;

    // Override console.log to filter imageResize debug messages
    console.log = function() {
        // Filter out imageResize debug messages
        if (arguments.length >= 1) {
            const firstArg = arguments[0];
            if (typeof firstArg === 'string' &&
                (firstArg.includes('this.options.modules') ||
                 firstArg.includes('DisplaySize') ||
                 firstArg.includes('Toolbar') ||
                 firstArg.includes('Resize'))) {
                return; // Suppress the log
            }
        }
        // Call original console.log for other messages
        return originalConsoleLog.apply(console, arguments);
    };

    // Override console.warn to filter Quill overwriting warnings
    console.warn = function() {
        // Filter Quill warnings about overwriting modules
        if (arguments.length >= 1) {
            const firstArg = arguments[0];
            if (typeof firstArg === 'string' &&
                firstArg.includes('Overwriting modules/imageResize')) {
                return; // Suppress the warning
            }
        }
        // Call original console.warn for other messages
        return originalConsoleWarn.apply(console, arguments);
    };

    // Add a cleanup function that can be called later if needed
    window.restoreConsoleFilters = function() {
        console.log = originalConsoleLog;
        console.warn = originalConsoleWarn;
    };
})();
</script>

<div class="curriculum-container">
    <!-- Left Panel - Preview (Hidden by default) -->
    <div class="editor-panel" id="previewPanel" style="display: none;">
        <div class="editor-card preview-card">
            <!-- Close Button -->
            <button type="button" class="editor-close-btn" onclick="closePreview()">
                <i class="fas fa-xmark"></i>
            </button>
            
            <!-- Preview Header -->
            <div class="preview-header">
                <div class="preview-type" id="previewType">
                    <i class="fas fa-file-lines"></i>
                    <span><?php echo get_phrase("lesson"); ?></span>
                </div>
                <div class="preview-actions">
                    <button type="button" class="btn-edit-preview-ai" id="btnGeneratePreview" onclick="openGenerateLessonFromPreview()" style="margin-right: 8px;">
                        <i class="fas fa-wand-magic-sparkles"></i> <?php echo get_phrase("generate_with_ai"); ?>
                    </button>
                    <button type="button" class="btn-edit-preview" id="btnEditPreview" onclick="switchToEditMode()">
                        <i class="fas fa-pen-to-square"></i> <?php echo get_phrase("edit"); ?>
                    </button>
                </div>
            </div>
            
            <!-- Preview Title -->
            <div class="preview-title-section">
                <h2 id="previewTitle"><?php echo get_phrase("lesson_title"); ?></h2>
            </div>
            
            <!-- Preview Content -->
            <div class="preview-content" id="previewContent">
                <!-- Content will be loaded here -->
            </div>
            
            <!-- Hidden data for switching to edit -->
            <input type="hidden" id="previewLessonId" value="">
            <input type="hidden" id="previewSectionId" value="">
            <input type="hidden" id="previewIsQuiz" value="false">
        </div>
    </div>
    
    <!-- Left Panel - Editor (Hidden by default) -->
    <div class="editor-panel" id="editorPanel" style="display: none;">
        <div class="editor-card">
            <!-- Close Button -->
            <button type="button" class="editor-close-btn" onclick="closeEditorWithCheck()">
                <i class="fas fa-xmark"></i>
            </button>
            
            <!-- Title Input -->
            <div class="editor-title-section">
                <input type="text" class="editor-title-input" id="lessonTitle" placeholder="<?php echo get_phrase("lesson_title"); ?>" maxlength="100">
                <span class="title-char-counter" id="lessonTitleCounter">0/100</span>
            </div>
            
            <!-- Quill Editor Container -->
            <div id="editor"></div>
            
            <!-- Save Status (Autosave) -->
            <div class="editor-footer">
                <div class="save-status" id="lessonSaveStatus">
                    <span class="status-icon"></span>
                    <span class="status-text"></span>
                    <span class="last-saved-time"></span>
                </div>
                <span class="shortcut-hint"><i class="fas fa-keyboard"></i> Ctrl+S <?php echo get_phrase("to_save"); ?></span>
            </div>
            
            <!-- Hidden inputs for form submission -->
            <input type="hidden" name="lesson_content" id="lessonContent">
            <input type="hidden" id="currentLessonId" value="">
            <input type="hidden" id="currentSectionId" value="">
            <!-- CSRF Token -->
            <input type="hidden" id="csrf_name" value="<?php echo $this->security->get_csrf_token_name(); ?>">
            <input type="hidden" id="csrf_hash" value="<?php echo $this->security->get_csrf_hash(); ?>">
        </div>
    </div>
    
    <!-- Left Panel - Quiz Editor (Hidden by default) -->
    <div class="editor-panel" id="quizEditorPanel" style="display: none;">
        <div class="editor-card quiz-editor-card">
            <!-- Close Button -->
            <button type="button" class="editor-close-btn" onclick="closeQuizEditorWithCheck()">
                <i class="fas fa-xmark"></i>
            </button>
            
            <!-- Quiz Header -->
            <div class="quiz-editor-header">
                <i class="fas fa-circle-question"></i>
                <span id="quizEditorTitle"><?php echo get_phrase("add_new_quiz"); ?></span>
            </div>
            
            <!-- Quiz Title Section (like lesson title) -->
            <div class="editor-title-section">
                <input type="text" class="editor-title-input" id="quizTitle" placeholder="<?php echo get_phrase("quiz_title"); ?>" maxlength="100" required>
                <span class="title-char-counter" id="quizTitleCounter">0/100</span>
            </div>
            
            <!-- Quiz Instruction Section -->
            <div class="editor-instruction-section">
                <textarea class="editor-instruction-input" id="quizInstruction" placeholder="<?php echo get_phrase("instructions_optional"); ?>"></textarea>
            </div>
            
            <!-- Quiz Form -->
            <div class="quiz-editor-form">
                <!-- Questions Builder -->
                <div class="quiz-questions-builder">
                    <div class="questions-header">
                        <label>
                            <i class="fas fa-list-check"></i>
                            <?php echo get_phrase("questions"); ?>
                        </label>
                        <button type="button" class="btn-add-question" onclick="addQuestion()">
                            <i class="fas fa-plus"></i> <?php echo get_phrase("add_question"); ?>
                        </button>
                    </div>
                    
                    <div id="questionsContainer">
                        <!-- Questions will be added here dynamically -->
                    </div>
                    
                    <div class="no-questions-message" id="noQuestionsMessage">
                        <i class="fas fa-clipboard-list"></i>
                        <p><?php echo get_phrase("no_questions_yet"); ?></p>
                        <button type="button" class="btn-add-first-question" onclick="addQuestion()">
                            <i class="fas fa-plus"></i> <?php echo get_phrase("add_first_question"); ?>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Quiz Footer (Autosave) -->
            <div class="editor-footer">
                <div class="save-status" id="quizSaveStatus">
                    <span class="status-icon"></span>
                    <span class="status-text"></span>
                    <span class="last-saved-time"></span>
                </div>
                <span class="shortcut-hint"><i class="fas fa-keyboard"></i> Ctrl+S <?php echo get_phrase("to_save"); ?></span>
            </div>
            
            <!-- Hidden inputs -->
            <input type="hidden" id="currentQuizId" value="">
            <input type="hidden" id="currentQuizSectionId" value="">
        </div>
    </div>
    
    <!-- Right Panel - Outline (Always visible) -->
    <div class="outline-panel" id="outlinePanel">
        <div class="outline-card">
            <div class="outline-header">
                <h4 class="outline-title"><?php echo get_phrase("Outline"); ?></h4>
                <div class="outline-header-actions">
                    <button type="button" class="btn-import-pdf" onclick="openPdfImportModal()" title="<?php echo get_phrase("import_from_pdf"); ?>">
                        <i class="fas fa-wand-magic-sparkles"></i>
                    </button>
                    <button type="button" class="btn-add-section" onclick="showAddSectionForm()">
                        <i class="fas fa-plus"></i> <?php echo get_phrase("add_section"); ?>
                    </button>
                </div>
            </div>
            
            <!-- Inline Add Section Form -->
            <div class="add-section-form" id="addSectionForm" style="display: none;">
                <div class="add-section-input-wrapper">
                    <i class="fas fa-folder add-section-icon"></i>
                    <input type="text" class="add-section-input" id="newSectionTitle" placeholder="<?php echo get_phrase("section_title"); ?>" onkeydown="handleAddSectionKeydown(event)">
                    <div class="add-section-actions">
                        <button type="button" class="btn-save-section" onclick="saveNewSection()">
                            <i class="fas fa-check"></i>
                        </button>
                        <button type="button" class="btn-cancel-section" onclick="hideAddSectionForm()">
                            <i class="fas fa-xmark"></i>
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="outline-content" id="sectionsContainer">
                <!-- Sections chargées dynamiquement -->
                <div id="sectionsContent">
                    <?php if (empty($course_sections)): ?>
                    <div class="empty-state" id="emptyState">
                        <i class="fas fa-folder-plus"></i>
                        <p><?php echo get_phrase("no_sections_yet"); ?></p>
                        <button type="button" class="btn-add-section" onclick="showAddSectionForm()">
                            <i class="fas fa-plus"></i> <?php echo get_phrase("add_first_section"); ?>
                        </button>
                    </div>
                    <?php else: ?>
                    <!-- Chargement initial via PHP, puis AJAX pour les autres pages -->
                    <?php 
                    $section_counter = 0;
                    $first_lesson_info = null; // Pour stocker les infos du premier élément
                    foreach ($course_sections as $section): 
                        $section_counter++;
                        $lessons = $this->lms_model->get_lessons('section', $section['id'])->result_array();
                        $is_first_section = ($section_counter === 1);
                        
                        // Stocker les infos du premier élément de la première section
                        if ($is_first_section && !empty($lessons) && $first_lesson_info === null) {
                            $first_lesson_info = [
                                'id' => $lessons[0]['id'],
                                'section_id' => $section['id'],
                                'title' => html_entity_decode($lessons[0]['title'], ENT_QUOTES, 'UTF-8'),
                                'type' => $lessons[0]['lesson_type']
                            ];
                        }
                    ?>
                    <div class="outline-section" id="outline-section-<?php echo $section['id']; ?>" data-section-id="<?php echo $section['id']; ?>">
                        <div class="section-header" onclick="toggleSection(<?php echo $section['id']; ?>)">
                            <div class="section-drag-handle" onclick="event.stopPropagation();" title="<?php echo get_phrase("drag_to_reorder"); ?>">
                                <i class="fas fa-grip-vertical"></i>
                            </div>
                            <div class="section-info">
                                <i class="fas fa-folder section-icon"></i>
                                <span class="section-label"><?php echo get_phrase("section"); ?> <?php echo $section_counter; ?>:</span>
                                <span class="section-name" id="section-name-<?php echo $section['id']; ?>" ondblclick="event.stopPropagation(); startEditSection(<?php echo $section['id']; ?>)"><?php echo html_entity_decode($section['title'], ENT_QUOTES, 'UTF-8'); ?></span>
                                <input type="text" class="section-name-input" id="section-input-<?php echo $section['id']; ?>" value="<?php echo html_entity_decode($section['title'], ENT_QUOTES, 'UTF-8'); ?>" style="display: none;" onkeydown="handleSectionEditKeydown(event, <?php echo $section['id']; ?>)" onblur="saveSectionName(<?php echo $section['id']; ?>)">
                            </div>
                            <div class="section-actions">
                                <button type="button" class="btn-icon" onclick="event.stopPropagation(); openMoveSection(<?php echo $section['id']; ?>, <?php echo $section_counter; ?>)" title="<?php echo get_phrase("move_to_position"); ?>">
                                    <i class="fas fa-up-down-left-right"></i>
                                </button>
                                <button type="button" class="btn-icon" onclick="event.stopPropagation(); startEditSection(<?php echo $section['id']; ?>)" title="<?php echo get_phrase("edit_section"); ?>">
                                    <i class="fas fa-pen-to-square"></i>
                                </button>
                                <button type="button" class="btn-icon" onclick="event.stopPropagation(); confirmDelete('<?php echo site_url('addons/courses/course_sections/'.$course['id'].'/delete/'.$section['id']); ?>')" title="<?php echo get_phrase("delete_section"); ?>">
                                    <i class="fas fa-trash-can"></i>
                                </button>
                                <i class="fas fa-chevron-right section-toggle<?php echo $is_first_section ? ' rotated' : ''; ?>" id="toggle-<?php echo $section['id']; ?>"></i>
                            </div>
                        </div>
                        
                        <div class="section-lessons<?php echo !$is_first_section ? ' collapsed' : ''; ?>" id="lessons-<?php echo $section['id']; ?>">
                            <?php 
                            $lesson_counter = 0;
                            foreach ($lessons as $lesson): 
                                $lesson_counter++;
                            ?>
                            <div class="lesson-item <?php echo $lesson['lesson_type'] == 'quiz' ? 'quiz-item' : ''; ?>" id="lesson-item-<?php echo $lesson['id']; ?>" data-lesson-id="<?php echo $lesson['id']; ?>">
                                <div class="lesson-drag-handle" title="<?php echo get_phrase("drag_to_reorder"); ?>">
                                    <i class="fas fa-grip-vertical"></i>
                                </div>
                                <div class="lesson-info" onclick="<?php echo $lesson['lesson_type'] == 'quiz' ? 'openQuizPreview('.$lesson['id'].', '.$section['id'].', \''.addslashes(html_entity_decode($lesson['title'], ENT_QUOTES, 'UTF-8')).'\')' : 'openLessonPreview('.$lesson['id'].', '.$section['id'].', \''.addslashes(html_entity_decode($lesson['title'], ENT_QUOTES, 'UTF-8')).'\')'; ?>">
                                    <i class="fas <?php echo $lesson['lesson_type'] == 'quiz' ? 'fa-circle-question' : 'fa-file-lines'; ?> lesson-icon"></i>
                                    <span class="lesson-label"><?php echo $lesson['lesson_type'] == 'quiz' ? get_phrase("quiz") : get_phrase("lesson"); ?> <?php echo $lesson_counter; ?>:</span>
                                    <span class="lesson-name"><?php echo html_entity_decode($lesson['title'], ENT_QUOTES, 'UTF-8'); ?></span>
                                </div>
                                <div class="lesson-actions">
                                    <?php if($lesson['lesson_type'] != 'quiz'): ?>
                                    <button type="button" class="btn-icon btn-generate-lesson" onclick="event.stopPropagation(); openGenerateLessonModal(<?php echo $lesson['id']; ?>, <?php echo $section['id']; ?>, <?php echo (!empty($lesson['attachment_type']) || !empty($lesson['video_url'])) ? 'true' : 'false'; ?>)" title="<?php echo get_phrase("generate_with_ai"); ?>">
                                        <i class="fas fa-wand-magic-sparkles" style="color: orange;"></i>
                                    </button>
                                    <?php endif; ?>
                                    <button type="button" class="btn-icon btn-delete-lesson" onclick="event.stopPropagation(); confirmDelete('<?php echo site_url('addons/courses/lessons/'.$course['id'].'/delete/'.$lesson['id']); ?>')" title="<?php echo get_phrase("delete"); ?>">
                                        <i class="fas fa-trash-can"></i>
                                    </button>
                                </div>
                            </div>
                            <?php endforeach; ?>
                            
                            <!-- Add Lesson/Quiz buttons -->
                            <div class="add-lesson-buttons">
                                <div class="lesson-dropdown-container">
                                    <button type="button" class="btn-add-lesson" onclick="toggleLessonDropdown(<?php echo $section['id']; ?>)">
                                        <i class="fas fa-plus"></i> <?php echo get_phrase("add_lesson"); ?>
                                        <i class="fas fa-chevron-down lesson-dropdown-arrow"></i>
                                    </button>
                                    <div class="lesson-dropdown-menu" id="lessonDropdown-<?php echo $section['id']; ?>">
                                        <button type="button" class="lesson-dropdown-item" onclick="openNewLessonEditor(<?php echo $section['id']; ?>); closeLessonDropdowns();">
                                            <i class="fas fa-pen-to-square"></i>
                                            <div class="lesson-dropdown-item-content">
                                                <span class="lesson-dropdown-item-title"><?php echo get_phrase("add_manually"); ?></span>
                                                <span class="lesson-dropdown-item-desc"><?php echo get_phrase("create_lesson_with_editor"); ?></span>
                                            </div>
                                        </button>
                                        <button type="button" class="lesson-dropdown-item lesson-dropdown-item-ai" onclick="openAiLessonModal(<?php echo $section['id']; ?>); closeLessonDropdowns();">
                                            <i class="fas fa-wand-magic-sparkles"></i>
                                            <div class="lesson-dropdown-item-content">
                                                <span class="lesson-dropdown-item-title"><?php echo get_phrase("generate_with_wayo_ai"); ?></span>
                                                <span class="lesson-dropdown-item-desc"><?php echo get_phrase("create_from_course_outline"); ?></span>
                                            </div>
                                        </button>
                                    </div>
                                </div>
                                <div class="quiz-dropdown-container">
                                    <button type="button" class="btn-add-lesson btn-add-quiz" onclick="toggleQuizDropdown(<?php echo $section['id']; ?>)">
                                        <i class="fas fa-plus"></i> <?php echo get_phrase("add_quiz"); ?>
                                        <i class="fas fa-chevron-down quiz-dropdown-arrow"></i>
                                    </button>
                                    <div class="quiz-dropdown-menu" id="quizDropdown-<?php echo $section['id']; ?>">
                                        <button type="button" class="quiz-dropdown-item" onclick="openNewQuizEditor(<?php echo $section['id']; ?>); closeQuizDropdowns();">
                                            <i class="fas fa-pen-to-square"></i>
                                            <div class="quiz-dropdown-item-content">
                                                <span class="quiz-dropdown-item-title"><?php echo get_phrase("add_manually"); ?></span>
                                                <span class="quiz-dropdown-item-desc"><?php echo get_phrase("create_quiz_with_editor"); ?></span>
                                            </div>
                                        </button>
                                        <button type="button" class="quiz-dropdown-item quiz-dropdown-item-ai" onclick="openAiQuizModal(<?php echo $section['id']; ?>); closeQuizDropdowns();">
                                            <i class="fas fa-wand-magic-sparkles"></i>
                                            <div class="quiz-dropdown-item-content">
                                                <span class="quiz-dropdown-item-title"><?php echo get_phrase("generate_with_wayo_ai"); ?></span>
                                                <span class="quiz-dropdown-item-desc"><?php echo get_phrase("create_from_lessons"); ?></span>
                                            </div>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                
                <!-- Loading spinner -->
                <div id="sectionsLoading" class="sections-loading" style="display: none;">
                    <i class="fas fa-spinner fa-spin"></i>
                    <span><?php echo get_phrase("loading"); ?>...</span>
                </div>
            </div>
            
            <?php if ($total_sections > $sections_per_page): ?>
            <!-- Pagination -->
            <div class="outline-pagination">
                <button type="button" class="pagination-btn" id="prevPage" onclick="loadPage(currentPage - 1)" disabled>
                    <i class="fas fa-chevron-left"></i>
                </button>
                <span class="pagination-info">
                    <span id="currentPage">1</span> / <span id="totalPages"><?php echo $total_pages; ?></span>
                </span>
                <button type="button" class="pagination-btn" id="nextPage" onclick="loadPage(currentPage + 1)" <?php echo $total_pages <= 1 ? 'disabled' : ''; ?>>
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// Initialize quiz fields visibility on page load
document.addEventListener('DOMContentLoaded', function() {
    toggleQuizFields();
});
</script>

<!-- Link Modal -->
<div class="link-modal" id="linkModal">
    <div class="link-modal-content">
        <div class="link-modal-header">
            <h5><?php echo get_phrase("insert_link"); ?></h5>
            <button type="button" class="close-modal" onclick="closeLinkModal()">&times;</button>
        </div>
        <div class="link-modal-body">
            <input type="url" id="linkUrl" class="link-input" placeholder="https://example.com">
        </div>
        <div class="link-modal-footer">
            <button type="button" class="btn-cancel" onclick="closeLinkModal()"><?php echo get_phrase("cancel"); ?></button>
            <button type="button" class="btn-insert" onclick="insertLink()"><?php echo get_phrase("insert"); ?></button>
        </div>
    </div>
</div>

<script>
// Initialize quiz fields visibility on page load
document.addEventListener('DOMContentLoaded', function() {
    toggleQuizFields();
});
</script>

<!-- Image Modal -->
<div class="link-modal" id="imageModal">
    <div class="link-modal-content">
        <div class="link-modal-header">
            <h5><?php echo get_phrase("insert_image"); ?></h5>
            <button type="button" class="close-modal" onclick="closeImageModal()">&times;</button>
        </div>
        <div class="link-modal-body">
            <input type="url" id="imageUrl" class="link-input" placeholder="https://example.com/image.jpg">
            <input type="text" id="imageAlt" class="link-input mt-2" placeholder="<?php echo get_phrase("alt_text"); ?>">
        </div>
        <div class="link-modal-footer">
            <button type="button" class="btn-cancel" onclick="closeImageModal()"><?php echo get_phrase("cancel"); ?></button>
            <button type="button" class="btn-insert" onclick="insertImage()"><?php echo get_phrase("insert"); ?></button>
        </div>
    </div>
</div>

<script>
// Initialize quiz fields visibility on page load
document.addEventListener('DOMContentLoaded', function() {
    toggleQuizFields();
});
</script>

<!-- Video Modal -->
<div class="link-modal" id="videoModal">
    <div class="link-modal-content">
        <div class="link-modal-header">
            <h5><i class="fas fa-video"></i> <?php echo get_phrase("insert_video"); ?></h5>
            <button type="button" class="close-modal" onclick="closeVideoModal()">&times;</button>
        </div>
        <div class="link-modal-body">
            <input type="url" id="videoUrl" class="link-input" placeholder="https://www.youtube.com/watch?v=...">
            <p class="video-platforms-hint"><?php echo get_phrase("supported_platforms"); ?> :</p>
            <p class="video-platforms-info">
                <span><i class="fab fa-youtube"></i> YouTube</span>
                <span><i class="fab fa-vimeo-v"></i> Vimeo</span>
                <span><i class="fas fa-video"></i> Loom</span>
                <span><i class="fas fa-circle-play"></i> Dailymotion</span>
                <span><i class="fas fa-film"></i> Wistia</span>
            </p>
        </div>
        <div class="link-modal-footer">
            <button type="button" class="btn-cancel" onclick="closeVideoModal()"><?php echo get_phrase("cancel"); ?></button>
            <button type="button" class="btn-insert" onclick="insertVideo()"><?php echo get_phrase("insert"); ?></button>
        </div>
    </div>
</div>

<script>
// Initialize quiz fields visibility on page load
document.addEventListener('DOMContentLoaded', function() {
    toggleQuizFields();
});
</script>

<!-- Move Section Modal -->
<div class="link-modal" id="moveSectionModal">
    <div class="link-modal-content">
        <div class="link-modal-header">
            <h5><?php echo get_phrase("move_section"); ?></h5>
            <button type="button" class="close-modal" onclick="closeMoveSectionModal()">&times;</button>
        </div>
        <div class="link-modal-body">
            <p class="move-section-info"><?php echo get_phrase("current_position"); ?>: <strong id="currentSectionPosition">1</strong> / <span id="totalSectionsCount"><?php echo $total_sections ?? 0; ?></span></p>
            <label for="targetPosition"><?php echo get_phrase("new_position"); ?>:</label>
            <input type="number" id="targetPosition" class="link-input" min="1" max="<?php echo $total_sections ?? 1; ?>" placeholder="1">
            <input type="hidden" id="moveSectionId" value="">
        </div>
        <div class="link-modal-footer">
            <button type="button" class="btn-cancel" onclick="closeMoveSectionModal()"><?php echo get_phrase("cancel"); ?></button>
            <button type="button" class="btn-insert" onclick="confirmMoveSection()"><?php echo get_phrase("move"); ?></button>
        </div>
    </div>
</div>

<script>
// Initialize quiz fields visibility on page load
document.addEventListener('DOMContentLoaded', function() {
    toggleQuizFields();
});
</script>

<!-- Unsaved Changes Modal -->
<div class="link-modal" id="unsavedChangesModal">
    <div class="link-modal-content">
        <div class="link-modal-header">
            <h5><i class="fas fa-triangle-exclamation text-warning"></i> <?php echo get_phrase("unsaved_changes"); ?></h5>
            <button type="button" class="close-modal" onclick="closeUnsavedModal()">&times;</button>
        </div>
        <div class="link-modal-body">
            <p><?php echo get_phrase("you_have_unsaved_changes"); ?></p>
        </div>
        <div class="link-modal-footer unsaved-modal-footer">
            <button type="button" class="btn-secondary" onclick="closeUnsavedModal()"><?php echo get_phrase("cancel"); ?></button>
            <button type="button" class="btn-insert btn-save-changes" onclick="saveAndProceed()"><?php echo get_phrase("save"); ?></button>
        </div>
    </div>
</div>

<script>
// Initialize quiz fields visibility on page load
document.addEventListener('DOMContentLoaded', function() {
    toggleQuizFields();
});
</script>

<!-- Offline Status Bar -->
<div class="offline-status-bar" id="offlineStatusBar">
    <i class="fas fa-circle-exclamation"></i>
    <span><?php echo get_phrase("offline_mode"); ?></span>
    <span class="pending-saves-count" id="pendingSavesCount"></span>
</div>

<script>
// Initialize Quill Editor
let quill = null;
let editorInitialized = false;
const courseId = <?php echo $course['id']; ?>;
const totalSectionsGlobal = <?php echo $total_sections ?? 0; ?>;

// Infos du premier élément pour auto-preview
const firstLessonInfo = <?php echo isset($first_lesson_info) && $first_lesson_info ? json_encode($first_lesson_info) : 'null'; ?>;

// ===== AUTOSAVE & DIRTY STATE MANAGEMENT =====
const AutoSaveManager = {
    // Configuration
    AUTOSAVE_DELAY: 3000, // 3 seconds after last change
    STORAGE_KEY: 'curriculum_offline_queue',
    
    // State
    lessonDirty: false,
    quizDirty: false,
    lessonAutosaveTimer: null,
    quizAutosaveTimer: null,
    isOnline: navigator.onLine,
    pendingAction: null, // Store action to execute after save/discard
    isLoadingContent: false, // Flag to prevent dirty tracking during content load
    
    // Initialize
    init: function() {
        // Listen for online/offline events
        window.addEventListener('online', () => this.handleOnline());
        window.addEventListener('offline', () => this.handleOffline());
        
        // Browser close/refresh prevention
        window.addEventListener('beforeunload', (e) => this.handleBeforeUnload(e));
        
        // Initial online state
        if (!navigator.onLine) {
            this.handleOffline();
        }
        
        // Process any pending offline saves
        this.processOfflineQueue();
    },
    
    // Mark lesson as dirty
    setLessonDirty: function(dirty = true) {
        // Ignore if content is being loaded programmatically
        if (this.isLoadingContent && dirty) return;
        
        this.lessonDirty = dirty;
        this.updateLessonStatus(dirty ? 'dirty' : '');
        
        if (dirty) {
            // Start autosave timer
            this.scheduleAutosave('lesson');
        }
    },
    
    // Mark quiz as dirty
    setQuizDirty: function(dirty = true) {
        // Ignore if content is being loaded programmatically
        if (this.isLoadingContent && dirty) return;
        
        this.quizDirty = dirty;
        this.updateQuizStatus(dirty ? 'dirty' : '');
        
        if (dirty) {
            // Start autosave timer
            this.scheduleAutosave('quiz');
        }
    },
    
    // Schedule autosave
    scheduleAutosave: function(type) {
        if (type === 'lesson') {
            clearTimeout(this.lessonAutosaveTimer);
            this.lessonAutosaveTimer = setTimeout(() => {
                if (this.lessonDirty) {
                    saveLesson(true); // true = autosave
                }
            }, this.AUTOSAVE_DELAY);
        } else if (type === 'quiz') {
            clearTimeout(this.quizAutosaveTimer);
            this.quizAutosaveTimer = setTimeout(() => {
                if (this.quizDirty) {
                    saveQuiz(true); // true = autosave
                }
            }, this.AUTOSAVE_DELAY);
        }
    },
    
    // Status text translations
    statusTexts: {
        'saving': '<?php echo addslashes(get_phrase("saving")); ?>...',
        'saved': '<?php echo addslashes(get_phrase("saved")); ?>',
        'error': '<?php echo addslashes(get_phrase("error")); ?>',
        'offline': '<?php echo addslashes(get_phrase("offline")); ?>',
        'dirty': '<?php echo addslashes(get_phrase("unsaved")); ?>'
    },
    
    // Update lesson save status UI
    updateLessonStatus: function(status, time = null) {
        const statusEl = document.getElementById('lessonSaveStatus');
        if (!statusEl) return;
        
        statusEl.className = 'save-status ' + status;
        
        // Update status text
        const textEl = statusEl.querySelector('.status-text');
        if (textEl) {
            textEl.textContent = this.statusTexts[status] || '';
        }
        
        const timeEl = statusEl.querySelector('.last-saved-time');
        if (timeEl) {
            timeEl.textContent = time ? '• ' + time : '';
        }
    },
    
    // Update quiz save status UI
    updateQuizStatus: function(status, time = null) {
        const statusEl = document.getElementById('quizSaveStatus');
        if (!statusEl) return;
        
        statusEl.className = 'save-status ' + status;
        
        // Update status text
        const textEl = statusEl.querySelector('.status-text');
        if (textEl) {
            textEl.textContent = this.statusTexts[status] || '';
        }
        
        const timeEl = statusEl.querySelector('.last-saved-time');
        if (timeEl) {
            timeEl.textContent = time ? '• ' + time : '';
        }
    },
    
    // Get current time formatted (day/month/year hour:minute)
    getCurrentTime: function() {
        const now = new Date();
        return now.toLocaleDateString('fr-FR', { 
            day: '2-digit', 
            month: '2-digit', 
            year: 'numeric',
            hour: '2-digit', 
            minute: '2-digit' 
        });
    },
    
    // Format timestamp to readable time (day/month/year hour:minute)
    formatTimestamp: function(timestamp) {
        if (!timestamp) return null;
        
        // Convert to number if it's a string (JSON returns strings)
        let ts = typeof timestamp === 'string' ? parseInt(timestamp, 10) : timestamp;
        
        // Check if it's a valid number
        if (isNaN(ts) || ts <= 0) return null;
        
        // Unix timestamps are in seconds, JavaScript needs milliseconds
        // If timestamp is less than year 2000 in ms, it's probably in seconds
        if (ts < 946684800000) {
            ts = ts * 1000;
        }
        
        const date = new Date(ts);
        if (isNaN(date.getTime())) return null;
        
        return date.toLocaleDateString('fr-FR', { 
            day: '2-digit', 
            month: '2-digit', 
            year: 'numeric',
            hour: '2-digit', 
            minute: '2-digit' 
        });
    },
    
    // Handle going online
    handleOnline: function() {
        this.isOnline = true;
        document.getElementById('offlineStatusBar').classList.remove('show');
        this.processOfflineQueue();
    },
    
    // Handle going offline
    handleOffline: function() {
        this.isOnline = false;
        document.getElementById('offlineStatusBar').classList.add('show');
        this.updateLessonStatus('offline');
        this.updateQuizStatus('offline');
    },
    
    // Add to offline queue
    addToOfflineQueue: function(type, data) {
        let queue = JSON.parse(localStorage.getItem(this.STORAGE_KEY) || '[]');
        queue.push({
            type: type,
            data: data,
            timestamp: Date.now()
        });
        localStorage.setItem(this.STORAGE_KEY, JSON.stringify(queue));
        this.updatePendingCount();
    },
    
    // Process offline queue when back online
    processOfflineQueue: function() {
        if (!this.isOnline) return;
        
        let queue = JSON.parse(localStorage.getItem(this.STORAGE_KEY) || '[]');
        if (queue.length === 0) return;
        
        // Process each item
        queue.forEach((item, index) => {
            setTimeout(() => {
                if (item.type === 'lesson') {
                    this.retrySaveLesson(item.data);
                } else if (item.type === 'quiz') {
                    this.retrySaveQuiz(item.data);
                }
            }, index * 1000); // Stagger requests
        });
        
        // Clear queue
        localStorage.removeItem(this.STORAGE_KEY);
        this.updatePendingCount();
    },
    
    // Retry save lesson from queue
    retrySaveLesson: function(data) {
        const csrfName = document.getElementById('csrf_name').value;
        const csrfHash = document.getElementById('csrf_hash').value;
        data[csrfName] = csrfHash;
        
        $.ajax({
            url: data.url,
            type: 'POST',
            data: data,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    toastr.success('<?php echo addslashes(get_phrase("offline_save_synced")); ?>');
                }
                if (response.csrf) {
                    document.getElementById('csrf_hash').value = response.csrf.csrfHash;
                }
            }
        });
    },
    
    // Retry save quiz from queue
    retrySaveQuiz: function(data) {
        const csrfName = document.getElementById('csrf_name').value;
        const csrfHash = document.getElementById('csrf_hash').value;
        data[csrfName] = csrfHash;
        
        $.ajax({
            url: data.url,
            type: 'POST',
            data: data,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    toastr.success('<?php echo addslashes(get_phrase("offline_save_synced")); ?>');
                }
                if (response.csrf) {
                    document.getElementById('csrf_hash').value = response.csrf.csrfHash;
                }
            }
        });
    },
    
    // Update pending saves count
    updatePendingCount: function() {
        let queue = JSON.parse(localStorage.getItem(this.STORAGE_KEY) || '[]');
        const countEl = document.getElementById('pendingSavesCount');
        if (countEl) {
            countEl.textContent = queue.length > 0 ? `(${queue.length} <?php echo addslashes(get_phrase("pending")); ?>)` : '';
        }
    },
    
    // Handle browser close/refresh
    handleBeforeUnload: function(e) {
        if (this.lessonDirty || this.quizDirty) {
            e.preventDefault();
            e.returnValue = '<?php echo addslashes(get_phrase("unsaved_changes_warning")); ?>';
            return e.returnValue;
        }
    },
    
    // Check if there are unsaved changes before action
    checkUnsavedChanges: function(callback) {
        if (this.lessonDirty || this.quizDirty) {
            this.pendingAction = callback;
            document.getElementById('unsavedChangesModal').classList.add('show');
            return true;
        }
        return false;
    }
};

// Close unsaved changes modal
function closeUnsavedModal() {
    document.getElementById('unsavedChangesModal').classList.remove('show');
    AutoSaveManager.pendingAction = null;
}

// Save and proceed
function saveAndProceed() {
    document.getElementById('unsavedChangesModal').classList.remove('show');
    
    const afterSave = () => {
        if (AutoSaveManager.pendingAction) {
            AutoSaveManager.pendingAction();
            AutoSaveManager.pendingAction = null;
        }
    };
    
    if (AutoSaveManager.lessonDirty) {
        saveLesson(false, afterSave);
    } else if (AutoSaveManager.quizDirty) {
        saveQuiz(false, afterSave);
    }
}

// Initialize AutoSaveManager when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    AutoSaveManager.init();
});

// ===== KEYBOARD SHORTCUTS =====
document.addEventListener('keydown', function(e) {
    // Ctrl+S or Cmd+S to save immediately
    if ((e.ctrlKey || e.metaKey) && e.key === 's') {
        e.preventDefault();
        
        // Check which editor is open and save
        const lessonEditor = document.getElementById('editorPanel');
        const quizEditor = document.getElementById('quizEditorPanel');
        
        if (lessonEditor && lessonEditor.style.display !== 'none') {
            // Lesson editor is open - force immediate save
            clearTimeout(AutoSaveManager.lessonAutosaveTimer);
            saveLesson(false); // false = manual save (shows toast)
        } else if (quizEditor && quizEditor.style.display !== 'none') {
            // Quiz editor is open - force immediate save
            clearTimeout(AutoSaveManager.quizAutosaveTimer);
            saveQuiz(false); // false = manual save (shows toast)
        }
    }
});

// Register Image Resize module if available (prevent duplicate registration and logging)
if (window.ImageResize) {
    // Check if already registered (multiple ways to check)
    const isAlreadyRegistered = Quill.imports && Quill.imports['modules/imageResize'] ||
                                Quill.imports && Quill.imports['modules/imageResize'];

    if (!isAlreadyRegistered) {
        try {
            // Temporarily suppress ALL console logs during registration and initialization
            const originalConsoleLog = console.log;
            const originalConsoleWarn = console.warn;
            const originalConsoleError = console.error;

            console.log = function() {
                // Filter out imageResize debug messages and Quill overwriting messages
                const message = arguments[0];
                if (typeof message === 'string') {
                    if (message.includes('this.options.modules') ||
                        message.includes('Overwriting modules/imageResize')) {
                        return;
                    }
                }
                originalConsoleLog.apply(console, arguments);
            };

            console.warn = function() {
                // Filter Quill warnings about overwriting
                const message = arguments[0];
                if (typeof message === 'string' && message.includes('Overwriting modules/imageResize')) {
                    return;
                }
                originalConsoleWarn.apply(console, arguments);
            };

            // Register the module
            Quill.register('modules/imageResize', ImageResize.default || ImageResize, true);

            // Restore console methods
            console.log = originalConsoleLog;
            console.warn = originalConsoleWarn;
            console.error = originalConsoleError;

        } catch (e) {
            // Restore console methods in case of error
            console.log = console.log || function() {};
            console.warn = console.warn || function() {};
            console.error = console.error || function() {};
        }
    }
}

// Custom image handler for upload
const MAX_IMAGE_SIZE = 5 * 1024 * 1024; // 5MB in bytes

function imageHandler() {
    const input = document.createElement('input');
    input.setAttribute('type', 'file');
    input.setAttribute('accept', 'image/*');
    input.click();

    input.onchange = async () => {
        const file = input.files[0];
        if (file) {
            // Check file size before upload
            if (file.size > MAX_IMAGE_SIZE) {
                toastr.error('<?php echo addslashes(get_phrase("image_too_large")); ?> (Max: 5MB)');
                return;
            }
            
            const range = quill.getSelection(true);
            
            // Show uploading indicator
            toastr.info('<?php echo addslashes(get_phrase("uploading")); ?>...', '', { timeOut: 0, extendedTimeOut: 0 });
            
            const formData = new FormData();
            formData.append('image', file);
            formData.append(document.getElementById('csrf_name').value, document.getElementById('csrf_hash').value);

            try {
                const response = await fetch('<?php echo site_url('addons/courses/upload_editor_image'); ?>', {
                    method: 'POST',
                    body: formData
                });
                
                // Handle 403 Forbidden (CSRF token expired)
                if (response.status === 403) {
                    toastr.clear();
                    // Try to refresh CSRF token and retry
                    try {
                        const csrfResponse = await fetch('<?php echo site_url('addons/courses/get_csrf_token'); ?>');
                        const csrfData = await csrfResponse.json();
                        if (csrfData.csrf) {
                            document.getElementById('csrf_hash').value = csrfData.csrf.csrfHash;
                            // Retry upload with new token
                            const retryFormData = new FormData();
                            retryFormData.append('image', file);
                            retryFormData.append(document.getElementById('csrf_name').value, csrfData.csrf.csrfHash);
                            
                            toastr.info('<?php echo addslashes(get_phrase("uploading")); ?>...', '', { timeOut: 0, extendedTimeOut: 0 });
                            const retryResponse = await fetch('<?php echo site_url('addons/courses/upload_editor_image'); ?>', {
                                method: 'POST',
                                body: retryFormData
                            });
                            const retryData = await retryResponse.json();
                            toastr.clear();
                            
                            if (retryData.csrf) {
                                document.getElementById('csrf_hash').value = retryData.csrf.csrfHash;
                            }
                            
                            if (retryData.success) {
                                quill.insertEmbed(range.index, 'image', retryData.url);
                                quill.setSelection(range.index + 1);
                                toastr.success('<?php echo addslashes(get_phrase("image_uploaded_successfully")); ?>');
                            } else {
                                toastr.error(retryData.message || '<?php echo addslashes(get_phrase("error_uploading_image")); ?>');
                            }
                            return;
                        }
                    } catch (csrfError) {
                        console.error('CSRF refresh failed:', csrfError);
                    }
                    toastr.error('<?php echo addslashes(get_phrase("session_expired")); ?>');
                    return;
                }
                
                const data = await response.json();
                
                // Clear uploading toast
                toastr.clear();
                
                // Update CSRF token
                if (data.csrf) {
                    document.getElementById('csrf_hash').value = data.csrf.csrfHash;
                }
                
                if (data.success) {
                    quill.insertEmbed(range.index, 'image', data.url);
                    quill.setSelection(range.index + 1);
                    
                    // Update hidden input and trigger autosave
                    document.getElementById('lessonContent').value = quill.root.innerHTML;
                    AutoSaveManager.setLessonDirty(true);
                    
                    toastr.success('<?php echo addslashes(get_phrase("image_uploaded_successfully")); ?>');
                } else {
                    toastr.error(data.message || '<?php echo addslashes(get_phrase("error_uploading_image")); ?>');
                }
            } catch (error) {
                toastr.clear();
                console.error('Upload error:', error);
                toastr.error('<?php echo addslashes(get_phrase("error_uploading_image")); ?>');
            }
        }
    };
}

// Custom video handler - opens modal
function videoHandler() {
    document.getElementById('videoUrl').value = '';
    document.getElementById('videoModal').classList.add('show');
    document.getElementById('videoUrl').focus();
}

// Close video modal
function closeVideoModal() {
    document.getElementById('videoModal').classList.remove('show');
}

// Insert video from modal
function insertVideo() {
    const url = document.getElementById('videoUrl').value.trim();
    
    if (!url) {
        toastr.error('<?php echo addslashes(get_phrase("please_enter_video_url")); ?>');
        return;
    }
    
    let embedUrl = url;
    let isValid = false;
    
    // Convert YouTube URL to embed
    if (url.includes('youtube.com/watch')) {
        const videoId = url.split('v=')[1]?.split('&')[0];
        if (videoId) {
            embedUrl = `https://www.youtube.com/embed/${videoId}`;
            isValid = true;
        }
    } else if (url.includes('youtu.be/')) {
        const videoId = url.split('youtu.be/')[1]?.split('?')[0];
        if (videoId) {
            embedUrl = `https://www.youtube.com/embed/${videoId}`;
            isValid = true;
        }
    } 
    // Convert Vimeo URL to embed
    else if (url.includes('vimeo.com/')) {
        const videoId = url.split('vimeo.com/')[1]?.split('?')[0];
        if (videoId) {
            embedUrl = `https://player.vimeo.com/video/${videoId}`;
            isValid = true;
        }
    }
    // Convert Loom URL to embed
    else if (url.includes('loom.com/share/')) {
        const videoId = url.split('loom.com/share/')[1]?.split('?')[0];
        if (videoId) {
            embedUrl = `https://www.loom.com/embed/${videoId}`;
            isValid = true;
        }
    }
    // Convert Dailymotion URL to embed
    else if (url.includes('dailymotion.com/video/')) {
        const videoId = url.split('dailymotion.com/video/')[1]?.split('?')[0];
        if (videoId) {
            embedUrl = `https://www.dailymotion.com/embed/video/${videoId}`;
            isValid = true;
        }
    } else if (url.includes('dai.ly/')) {
        const videoId = url.split('dai.ly/')[1]?.split('?')[0];
        if (videoId) {
            embedUrl = `https://www.dailymotion.com/embed/video/${videoId}`;
            isValid = true;
        }
    }
    // Convert Wistia URL to embed
    else if (url.includes('wistia.com/medias/')) {
        const videoId = url.split('wistia.com/medias/')[1]?.split('?')[0];
        if (videoId) {
            embedUrl = `https://fast.wistia.net/embed/iframe/${videoId}`;
            isValid = true;
        }
    }
    // Already an embed URL
    else if (url.includes('/embed/') || url.includes('player.vimeo.com')) {
        embedUrl = url;
        isValid = true;
    }
    
    if (!isValid) {
        toastr.error('<?php echo addslashes(get_phrase("unsupported_video_platform")); ?>');
        return;
    }
    
    const range = quill.getSelection(true);
    quill.insertEmbed(range.index, 'video', embedUrl);
    quill.setSelection(range.index + 1);
    
    // Update hidden input and trigger autosave
    document.getElementById('lessonContent').value = quill.root.innerHTML;
    AutoSaveManager.setLessonDirty(true);
    
    closeVideoModal();
    toastr.success('<?php echo addslashes(get_phrase("video_inserted")); ?>');
}

// Handle Enter key in video modal
document.addEventListener('keydown', function(e) {
    if (e.key === 'Enter' && document.getElementById('videoModal').classList.contains('show')) {
        e.preventDefault();
        insertVideo();
    }
    if (e.key === 'Escape' && document.getElementById('videoModal').classList.contains('show')) {
        closeVideoModal();
    }
});

// Custom attachment handler
function attachmentHandler() {
    const input = document.createElement('input');
    input.setAttribute('type', 'file');
    input.setAttribute('accept', '.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.zip,.rar,.txt');
    input.click();

    input.onchange = async () => {
        const file = input.files[0];
        if (file) {
            const range = quill.getSelection(true);
            
            const formData = new FormData();
            formData.append('file', file);
            formData.append(document.getElementById('csrf_name').value, document.getElementById('csrf_hash').value);

            try {
                const response = await fetch('<?php echo site_url('addons/courses/upload_editor_attachment'); ?>', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();
                
                // Update CSRF token
                if (data.csrf) {
                    document.getElementById('csrf_hash').value = data.csrf.csrfHash;
                }
                
                if (data.success) {
                    // Insert a link to the attachment (without icon)
                    const linkText = data.name;
                    quill.insertText(range.index, linkText, 'link', data.url);
                    quill.setSelection(range.index + linkText.length);
                    
                    // Update hidden input and trigger autosave
                    document.getElementById('lessonContent').value = quill.root.innerHTML;
                    AutoSaveManager.setLessonDirty(true);
                    
                    toastr.success('<?php echo addslashes(get_phrase("file_uploaded_successfully")); ?>');
                } else {
                    toastr.error(data.message || '<?php echo addslashes(get_phrase("error_uploading_file")); ?>');
                }
            } catch (error) {
                toastr.error('<?php echo addslashes(get_phrase("error_uploading_file")); ?>');
            }
        }
    };
}

// Quill toolbar configuration
const toolbarOptions = {
    container: [
        ['undo', 'redo'],
        [{ 'header': [1, 2, 3, false] }],
        [{ 'size': ['small', false, 'large', 'huge'] }],
        ['bold', 'italic', 'underline', 'strike'],
        [{ 'color': [] }, { 'background': [] }],
        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
        [{ 'indent': '-1'}, { 'indent': '+1' }],
        [{ 'align': [] }],
        ['blockquote', 'code-block'],
        ['link', 'image', 'video', 'attachment'],
        ['clean']
    ],
    handlers: {
        'image': imageHandler,
        'video': videoHandler,
        'attachment': attachmentHandler,
        'undo': function() { quill.history.undo(); },
        'redo': function() { quill.history.redo(); }
    }
};

// Initialize editor only when needed
function initEditor() {
    if (editorInitialized) return;

    // Check if editor element exists
    const editorElement = document.getElementById('editor');
    if (!editorElement) {
        console.error('Editor element not found');
        return;
    }

    try {
        // Initialize Quill with minimal modules to avoid emit errors
        const modules = {
            toolbar: toolbarOptions,
            // Configure imageResize module to reduce console logs
            imageResize: {
                displaySize: true,
                displayStyles: {
                    backgroundColor: 'black',
                    border: 'none',
                    color: 'white'
                },
                modules: ['DisplaySize', 'Toolbar', 'Resize']
            }
            // Explicitly no history module to prevent emit errors
        };


        try {
            quill = new Quill('#editor', {
                theme: 'snow',
                modules: modules,
                placeholder: '<?php echo addslashes(get_phrase("start_writing_your_content_here")); ?>...'
            });

            // Verify Quill was created successfully
            if (!quill || !quill.root) {
                console.error('Failed to initialize Quill editor');
                return;
            }


        } catch (error) {
            console.error('Error initializing Quill:', error);
            return;
        }

    } catch (error) {
        console.error('Error initializing Quill editor:', error);
        return;
    }
    
    // ===== COPY/PASTE CLEANUP (Word/Google Docs) =====
    // Clean pasted content from Word/Google Docs styles
    quill.clipboard.addMatcher(Node.ELEMENT_NODE, function(node, delta) {
        // List of attributes to remove (Word/Docs specific)
        const attributesToClean = [
            'background', 'color', 'font', 'font-family', 'font-size',
            'line-height', 'margin', 'padding', 'text-indent', 'mso-'
        ];
        
        delta.ops = delta.ops.map(function(op) {
            if (op.attributes) {
                // Remove unwanted formatting
                attributesToClean.forEach(function(attr) {
                    delete op.attributes[attr];
                });
                
                // Clean up style attribute if present
                if (op.attributes.style) {
                    delete op.attributes.style;
                }
                
                // Remove empty attributes object
                if (Object.keys(op.attributes).length === 0) {
                    delete op.attributes;
                }
            }
            return op;
        });
        
        return delta;
    });
    
    // Clean specific elements (remove Word-specific tags)
    quill.clipboard.addMatcher('SPAN', function(node, delta) {
        // Remove spans with mso- styles (Microsoft Office)
        if (node.style && node.style.cssText && node.style.cssText.includes('mso-')) {
            return new Delta().insert(node.textContent);
        }
        return delta;
    });
    
    // Clean tables from Word
    quill.clipboard.addMatcher('TABLE', function(node, delta) {
        // Wrap table in responsive container
        const tableHtml = node.outerHTML
            .replace(/style="[^"]*"/gi, '') // Remove inline styles
            .replace(/class="[^"]*"/gi, '') // Remove classes
            .replace(/width="[^"]*"/gi, '') // Remove width
            .replace(/height="[^"]*"/gi, ''); // Remove height
        return new Delta().insert({ 'table-responsive': tableHtml });
    });

    // Update hidden input on text change + dirty tracking
    quill.on('text-change', function(delta, oldDelta, source) {
        document.getElementById('lessonContent').value = quill.root.innerHTML;
        // Only mark dirty if change is from user (not programmatic)
        if (source === 'user') {
            AutoSaveManager.setLessonDirty(true);
        }
    });

    // Title change tracking with character counter
    document.getElementById('lessonTitle').addEventListener('input', function() {
        AutoSaveManager.setLessonDirty(true);
        updateTitleCounter('lessonTitle', 'lessonTitleCounter');
    });

    editorInitialized = true;
}

// Update title character counter
function updateTitleCounter(inputId, counterId) {
    const input = document.getElementById(inputId);
    const counter = document.getElementById(counterId);
    if (!input || !counter) return;
    
    const length = input.value.length;
    const maxLength = input.maxLength || 100;
    
    counter.textContent = `${length}/${maxLength}`;
    
    // Update counter style based on length
    counter.classList.remove('warning', 'limit');
    if (length >= maxLength) {
        counter.classList.add('limit');
    } else if (length >= maxLength * 0.8) {
        counter.classList.add('warning');
    }
}

// ===== EMBEDS WHITELIST & VALIDATION =====
const EmbedManager = {
    // Allowed video domains
    allowedDomains: [
        'youtube.com', 'www.youtube.com', 'youtu.be',
        'vimeo.com', 'player.vimeo.com',
        'loom.com', 'www.loom.com',
        'dailymotion.com', 'www.dailymotion.com',
        'wistia.com', 'fast.wistia.com'
    ],
    
    // Validate URL against whitelist
    isAllowedDomain: function(url) {
        try {
            const urlObj = new URL(url);
            return this.allowedDomains.some(domain => 
                urlObj.hostname === domain || urlObj.hostname.endsWith('.' + domain)
            );
        } catch(e) {
            return false;
        }
    },
    
    // Convert URL to embed URL
    getEmbedUrl: function(url) {
        try {
            const urlObj = new URL(url);
            
            // YouTube
            if (urlObj.hostname.includes('youtube.com') || urlObj.hostname === 'youtu.be') {
                let videoId = '';
                if (urlObj.hostname === 'youtu.be') {
                    videoId = urlObj.pathname.slice(1);
                } else {
                    videoId = urlObj.searchParams.get('v');
                }
                if (videoId) {
                    return `https://www.youtube.com/embed/${videoId}`;
                }
            }
            
            // Vimeo
            if (urlObj.hostname.includes('vimeo.com')) {
                const videoId = urlObj.pathname.split('/').pop();
                if (videoId && /^\d+$/.test(videoId)) {
                    return `https://player.vimeo.com/video/${videoId}`;
                }
            }
            
            // Loom
            if (urlObj.hostname.includes('loom.com')) {
                const match = url.match(/loom\.com\/share\/([a-zA-Z0-9]+)/);
                if (match) {
                    return `https://www.loom.com/embed/${match[1]}`;
                }
            }
            
            // Dailymotion
            if (urlObj.hostname.includes('dailymotion.com')) {
                const match = url.match(/video\/([a-zA-Z0-9]+)/);
                if (match) {
                    return `https://www.dailymotion.com/embed/video/${match[1]}`;
                }
            }
            
            return null;
        } catch(e) {
            return null;
        }
    },
    
    // Create embed HTML with fallback
    createEmbed: function(url) {
        if (!this.isAllowedDomain(url)) {
            return `<div class="embed-blocked">
                <i class="fas fa-triangle-exclamation"></i>
                <p><?php echo addslashes(get_phrase("embed_domain_not_allowed")); ?></p>
                <a href="${url}" target="_blank" rel="noopener">${url}</a>
            </div>`;
        }
        
        const embedUrl = this.getEmbedUrl(url);
        if (embedUrl) {
            return `<div class="video-embed-container">
                <iframe src="${embedUrl}" frameborder="0" allowfullscreen 
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture">
                </iframe>
            </div>`;
        }
        
        // Fallback - just show link
        return `<div class="embed-fallback">
            <i class="fas fa-video"></i>
            <a href="${url}" target="_blank" rel="noopener"><?php echo addslashes(get_phrase("watch_video")); ?>: ${url}</a>
        </div>`;
    }
};

// ===== SCROLL/FOCUS MANAGEMENT =====
function focusEditor() {
    if (quill) {
        // Scroll to top of editor panel
        const editorPanel = document.getElementById('editorPanel');
        if (editorPanel) {
            editorPanel.scrollTo({ top: 0, behavior: 'smooth' });
        }
        // Focus the editor
        setTimeout(() => {
            quill.focus();
            quill.setSelection(0, 0); // Cursor at start
        }, 100);
    }
}

function focusQuizEditor() {
    const quizPanel = document.getElementById('quizEditorPanel');
    if (quizPanel) {
        quizPanel.scrollTo({ top: 0, behavior: 'smooth' });
    }
    // Focus quiz title
    setTimeout(() => {
        const quizTitle = document.getElementById('quizTitle');
        if (quizTitle) quizTitle.focus();
    }, 100);
}

// ========== PREVIEW MODE ==========

// Open lesson preview (read-only)
function openLessonPreview(lessonId, sectionId, title) {
    // Check for unsaved changes before switching
    if (AutoSaveManager.checkUnsavedChanges(() => openLessonPreview(lessonId, sectionId, title))) {
        return;
    }
    
    // Close any open editors
    closeEditor();
    closeQuizEditor();

    showPreviewPanel();
    
    // Set preview data
    document.getElementById('previewLessonId').value = lessonId;
    document.getElementById('previewSectionId').value = sectionId;
    document.getElementById('previewIsQuiz').value = 'false';
    document.getElementById('previewTitle').textContent = title;
    document.getElementById('previewType').innerHTML = '<i class="fas fa-file-lines"></i><span><?php echo addslashes(get_phrase("lesson")); ?></span>';
    const btnGen = document.getElementById('btnGeneratePreview');
    if(btnGen) btnGen.style.display = 'inline-block';
    document.getElementById('previewContent').innerHTML = '<p class="preview-loading"><i class="fas fa-spinner fa-spin"></i> <?php echo addslashes(get_phrase("loading")); ?>...</p>';
    
    // Mark lesson as active
    document.querySelectorAll('.lesson-item').forEach(item => {
        item.classList.remove('active');
    });
    const lessonItem = document.getElementById('lesson-item-' + lessonId);
    if (lessonItem) lessonItem.classList.add('active');
    
    // Load lesson content (cache: false to always get fresh data)
    $.ajax({
        url: '<?php echo site_url('addons/courses/get_lesson_content'); ?>/' + lessonId,
        type: 'GET',
        dataType: 'json',
        cache: false,
        success: function(response) {
            // Update title from server (always use fresh data)
            if (response.title) {
                document.getElementById('previewTitle').textContent = response.title;
            }
            
            if (response.content && response.content.trim() !== '' && response.content !== '<p><br></p>') {
                document.getElementById('previewContent').innerHTML = response.content;
            } else {
                document.getElementById('previewContent').innerHTML = '<p class="preview-empty"><i class="fas fa-file-lines"></i> <?php echo addslashes(get_phrase("no_content_yet")); ?></p>';
            }
        },
        error: function() {
            document.getElementById('previewContent').innerHTML = '<p class="preview-error"><i class="fas fa-triangle-exclamation"></i> <?php echo addslashes(get_phrase("error_loading_content")); ?></p>';
        }
    });
}

// Open quiz preview (read-only)
function openQuizPreview(quizId, sectionId, title) {
    // Check for unsaved changes before switching
    if (AutoSaveManager.checkUnsavedChanges(() => openQuizPreview(quizId, sectionId, title))) {
        return;
    }
    
    // Close any open editors
    closeEditor();
    closeQuizEditor();

    showPreviewPanel();
    
    // Set preview data
    document.getElementById('previewLessonId').value = quizId;
    document.getElementById('previewSectionId').value = sectionId;
    document.getElementById('previewIsQuiz').value = 'true';
    document.getElementById('previewTitle').textContent = title;
    document.getElementById('previewType').innerHTML = '<i class="fas fa-circle-question"></i><span><?php echo addslashes(get_phrase("quiz")); ?></span>';
    const btnGen = document.getElementById('btnGeneratePreview');
    if(btnGen) btnGen.style.display = 'none';
    document.getElementById('previewContent').innerHTML = '<p class="preview-loading"><i class="fas fa-spinner fa-spin"></i> <?php echo addslashes(get_phrase("loading")); ?>...</p>';
    
    // Mark quiz as active
    document.querySelectorAll('.lesson-item').forEach(item => {
        item.classList.remove('active');
    });
    const quizItem = document.getElementById('lesson-item-' + quizId);
    if (quizItem) quizItem.classList.add('active');
    
    // Load quiz content (cache: false to always get fresh data)
    $.ajax({
        url: '<?php echo site_url('addons/courses/get_quiz_content'); ?>/' + quizId,
        type: 'GET',
        dataType: 'json',
        cache: false,
        success: function(response) {
            if (response.success) {
                // Update title from server (always use fresh data)
                if (response.title) {
                    document.getElementById('previewTitle').textContent = response.title;
                }
                
                let html = '';
                
                // Instructions
                if (response.summary && response.summary.trim() !== '') {
                    html += '<div class="quiz-preview-instructions">' + response.summary + '</div>';
                }
                
                // Questions
                if (response.questions && response.questions.length > 0) {
                    html += '<div class="quiz-preview-questions">';
                    html += '<div class="quiz-questions-header"><?php echo addslashes(get_phrase("questions")); ?> (' + response.questions.length + ')</div>';
                    
                    response.questions.forEach(function(q, index) {
                        html += '<div class="quiz-preview-question-card">';
                        html += '<div class="question-label"><?php echo addslashes(strtoupper(get_phrase("question"))); ?> ' + (index + 1) + '</div>';
                        html += '<div class="question-text">' + q.title + '</div>';
                        
                        // Options
                        const options = JSON.parse(q.options || '[]');
                        const correctAnswers = JSON.parse(q.correct_answers || '[]');
                        
                        if (options.length > 0) {
                            html += '<div class="question-options-list">';
                            options.forEach(function(opt, optIndex) {
                                const isCorrect = correctAnswers.includes(optIndex) || correctAnswers.includes(optIndex.toString());
                                html += '<div class="question-option ' + (isCorrect ? 'correct' : '') + '">';
                                html += '<span class="option-badge">' + String.fromCharCode(65 + optIndex) + '</span>';
                                html += '<span class="option-text">' + opt + '</span>';
                                if (isCorrect) html += '<span class="option-check"><i class="fas fa-check"></i></span>';
                                html += '</div>';
                            });
                            html += '</div>';
                        }
                        
                        html += '</div>';
                    });
                    
                    html += '</div>';
                } else {
                    html += '<p class="preview-empty"><i class="fas fa-circle-question"></i> <?php echo addslashes(get_phrase("no_questions_yet")); ?></p>';
                }
                
                document.getElementById('previewContent').innerHTML = html;
            } else {
                document.getElementById('previewContent').innerHTML = '<p class="preview-error"><i class="fas fa-triangle-exclamation"></i> <?php echo addslashes(get_phrase("error_loading_content")); ?></p>';
            }
        },
        error: function() {
            document.getElementById('previewContent').innerHTML = '<p class="preview-error"><i class="fas fa-triangle-exclamation"></i> <?php echo addslashes(get_phrase("error_loading_content")); ?></p>';
        }
    });
}

// Show preview panel
function showPreviewPanel() {
    const previewPanel = document.getElementById('previewPanel');
    const outlinePanel = document.getElementById('outlinePanel');
    
    previewPanel.style.display = 'block';
    outlinePanel.classList.add('with-editor');
    
    // Add body class for mobile to prevent scroll
    if (window.innerWidth <= 768) {
        document.body.classList.add('editor-open');
    }
}

// Close preview
function closePreview() {
    const previewPanel = document.getElementById('previewPanel');
    const outlinePanel = document.getElementById('outlinePanel');
    
    previewPanel.style.display = 'none';
    outlinePanel.classList.remove('with-editor');
    
    // Remove body class for mobile
    document.body.classList.remove('editor-open');
    
    // Remove active class from lessons
    document.querySelectorAll('.lesson-item').forEach(item => {
        item.classList.remove('active');
    });
}

// Switch from preview to edit mode
function switchToEditMode() {
    const lessonId = document.getElementById('previewLessonId').value;
    const sectionId = document.getElementById('previewSectionId').value;
    const isQuiz = document.getElementById('previewIsQuiz').value === 'true';
    const title = document.getElementById('previewTitle').textContent;
    
    closePreview();
    
    if (isQuiz) {
        loadQuizAndEdit(parseInt(lessonId), parseInt(sectionId));
    } else {
        openLessonEditor(parseInt(lessonId), parseInt(sectionId), title);
    }
}

// ========== EDITOR MODE ==========

// Open editor for NEW lesson
function openNewLessonEditor(sectionId) {
    // Check for unsaved changes
    if (AutoSaveManager.checkUnsavedChanges(() => openNewLessonEditor(sectionId))) {
        return;
    }
    
    // Close preview and quiz editor if open
    closePreview();
    closeQuizEditor();

    // Prevent dirty tracking during initialization
    AutoSaveManager.isLoadingContent = true;

    showEditorPanel();
    document.getElementById('lessonTitle').value = '';
    document.getElementById('currentLessonId').value = '';
    document.getElementById('currentSectionId').value = sectionId;
    updateTitleCounter('lessonTitle', 'lessonTitleCounter');

    // Reset dirty state and status
    AutoSaveManager.lessonDirty = false;
    AutoSaveManager.updateLessonStatus('');

    // Remove active class from all lessons
    document.querySelectorAll('.lesson-item').forEach(item => {
        item.classList.remove('active');
    });

    // Initialize editor if needed
    if (!editorInitialized) {
        initEditor();
    }

    // Clear editor content
    quill.root.innerHTML = '<p><br></p>';
    
    // Re-enable dirty tracking after a short delay
    setTimeout(function() {
        AutoSaveManager.isLoadingContent = false;
    }, 100);
    
    document.getElementById('lessonTitle').focus();
}

// Open editor for EXISTING lesson
function openLessonEditor(lessonId, sectionId, title) {
    // Check for unsaved changes
    if (AutoSaveManager.checkUnsavedChanges(() => openLessonEditor(lessonId, sectionId, title))) {
        return;
    }
    
    // Close preview and quiz editor if open
    closePreview();
    closeQuizEditor();

    showEditorPanel();
    // Set placeholder while loading - fresh title will come from server
    document.getElementById('lessonTitle').value = '<?php echo addslashes(get_phrase("loading")); ?>...';
    document.getElementById('currentLessonId').value = lessonId;
    document.getElementById('currentSectionId').value = sectionId;
    updateTitleCounter('lessonTitle', 'lessonTitleCounter');
    
    // Reset dirty state and status
    AutoSaveManager.lessonDirty = false;
    AutoSaveManager.updateLessonStatus('');

    // Mark lesson as active
    document.querySelectorAll('.lesson-item').forEach(item => {
        item.classList.remove('active');
    });
    const lessonElement = document.getElementById('lesson-item-' + lessonId);
    if (lessonElement) {
        lessonElement.classList.add('active');
    }

    // Initialize editor if needed
    if (!editorInitialized) {
        initEditor();
    }

    // Verify editor is properly initialized before loading content
    if (!quill || !quill.root) {
        console.error('Editor not properly initialized, cannot load lesson content');
        toastr.error('<?php echo addslashes(get_phrase("editor_initialization_failed")); ?>');
        return;
    }

    // Load lesson content (will set fresh title from server)
    loadLessonContent(lessonId);
}

// Load lesson content from server
function loadLessonContent(lessonId) {

    if (!quill || !quill.root) {
        console.error('Editor not properly initialized');
        return;
    }

    // Prevent dirty tracking during content load
    AutoSaveManager.isLoadingContent = true;

    // Show loading state
    quill.root.innerHTML = '<p><em><?php echo addslashes(get_phrase("loading")); ?>...</em></p>';


    // AJAX call to get lesson content (cache: false to always get fresh data)
    $.ajax({
        url: '<?php echo site_url('addons/courses/get_lesson_content'); ?>/' + lessonId,
        type: 'GET',
        dataType: 'json',
        cache: false,
        success: function(response) {
            // Update title from server (always use fresh data)
            if (response.title !== undefined) {
                document.getElementById('lessonTitle').value = response.title || '';
                updateTitleCounter('lessonTitle', 'lessonTitleCounter');
            }
            
            if (response.content) {

                // Check if content is JSON (starts with { and contains lesson_content)
                let content = response.content;
                if (typeof content === 'string' && content.trim().startsWith('{') && content.includes('lesson_content')) {
                    try {
                        const jsonData = JSON.parse(content);
                        content = jsonData.lesson_content || jsonData.content || '<p><br></p>';
                    } catch (e) {
                        console.error('Failed to parse JSON content:', e);
                        content = '<p><br></p>';
                    }
                }

                // Ensure content is valid HTML
                if (content && content.trim()) {
                    // Use Quill's clipboard API instead of direct innerHTML manipulation
                    setTimeout(() => {
                        if (quill && quill.root) {
                            try {
                                // Use Quill's clipboard API which is safer than direct innerHTML
                                quill.clipboard.dangerouslyPasteHTML(content);
                            } catch (clipboardError) {
                                console.error('Clipboard API failed, trying direct assignment:', clipboardError);
                                try {
                                    // Fallback to direct assignment
                                    quill.root.innerHTML = content;
                                } catch (directError) {
                                    console.error('Even direct assignment failed:', directError);
                                }
                            }
                        } else {
                            console.error('Quill not available when setting content');
                        }
                    }, 50);
                } else {
                    if (quill && quill.root) {
                        try {
                            quill.clipboard.dangerouslyPasteHTML('<p><br></p>');
                        } catch (e) {
                            quill.root.innerHTML = '<p><br></p>';
                        }
                    }
                }
            } else {
                quill.root.innerHTML = '<p><br></p>';
            }
            
            // Get last saved time from server response
            const lastSavedTime = AutoSaveManager.formatTimestamp(response.last_modified);
            
            // Re-enable dirty tracking after a short delay to ensure all events have fired
            setTimeout(function() {
                AutoSaveManager.isLoadingContent = false;
                // Reset dirty state to clean and show last saved time
                AutoSaveManager.lessonDirty = false;
                if (lastSavedTime) {
                    AutoSaveManager.updateLessonStatus('saved', lastSavedTime);
                } else {
                    AutoSaveManager.updateLessonStatus('');
                }
            }, 100);
            
            // Focus editor and scroll to top
            focusEditor();
        },
        error: function() {
            document.getElementById('lessonTitle').value = '';
            quill.root.innerHTML = '<p><br></p>';
            
            // Re-enable dirty tracking
            setTimeout(function() {
                AutoSaveManager.isLoadingContent = false;
                AutoSaveManager.updateLessonStatus('');
            }, 100);
            
            focusEditor();
        }
    });
}

// Show editor panel with animation
function showEditorPanel() {
    const editorPanel = document.getElementById('editorPanel');
    const outlinePanel = document.getElementById('outlinePanel');
    
    editorPanel.style.display = 'block';
    outlinePanel.classList.add('with-editor');
    
    // Add body class for mobile to prevent scroll
    if (window.innerWidth <= 768) {
        document.body.classList.add('editor-open');
    }
    
    // Focus on title input
    setTimeout(() => {
        document.getElementById('lessonTitle').focus();
    }, 100);
}

// Close editor with unsaved changes check (for X button)
function closeEditorWithCheck() {
    if (AutoSaveManager.checkUnsavedChanges(() => {
        AutoSaveManager.lessonDirty = false;
        closeEditor();
    })) {
        return;
    }
    closeEditor();
}

// Close editor (internal - no check)
function closeEditor() {
    const editorPanel = document.getElementById('editorPanel');
    const outlinePanel = document.getElementById('outlinePanel');

    editorPanel.style.display = 'none';
    outlinePanel.classList.remove('with-editor');
    
    // Remove body class for mobile
    document.body.classList.remove('editor-open');
    
    // Reset dirty state
    AutoSaveManager.lessonDirty = false;
    AutoSaveManager.updateLessonStatus('');

    // Remove active class from lessons
    document.querySelectorAll('.lesson-item').forEach(item => {
        item.classList.remove('active');
    });
}

// ===== QUIZ EDITOR FUNCTIONS =====

// Open quiz editor for NEW quiz
function openNewQuizEditor(sectionId) {
    // Check for unsaved changes
    if (AutoSaveManager.checkUnsavedChanges(() => openNewQuizEditor(sectionId))) {
        return;
    }
    
    // Close preview and lesson editor if open
    closePreview();
    closeEditor();

    // Prevent dirty tracking during initialization
    AutoSaveManager.isLoadingContent = true;

    showQuizEditorPanel();
    document.getElementById('quizTitle').value = '';
    document.getElementById('quizInstruction').value = '';
    document.getElementById('currentQuizId').value = '';
    document.getElementById('currentQuizSectionId').value = sectionId;
    document.getElementById('quizEditorTitle').textContent = '<?php echo addslashes(get_phrase("add_new_quiz")); ?>';
    updateTitleCounter('quizTitle', 'quizTitleCounter');
    
    // Reset dirty state and status
    AutoSaveManager.quizDirty = false;
    AutoSaveManager.updateQuizStatus('');

    // Re-enable dirty tracking after a short delay
    setTimeout(function() {
        AutoSaveManager.isLoadingContent = false;
    }, 100);

    // Focus on quiz editor and scroll to top
    focusQuizEditor();
}

// Open quiz editor for EXISTING quiz
function openQuizEditor(quizId, sectionId, title, instruction) {
    // Close lesson editor if open
    closeEditor();
    
    showQuizEditorPanel();
    document.getElementById('quizTitle').value = title || '';
    document.getElementById('quizInstruction').value = instruction || '';
    document.getElementById('currentQuizId').value = quizId;
    document.getElementById('currentQuizSectionId').value = sectionId;
    document.getElementById('quizEditorTitle').textContent = '<?php echo addslashes(get_phrase("edit_quiz")); ?>';
    
    // Mark quiz as active
    document.querySelectorAll('.lesson-item').forEach(item => {
        item.classList.remove('active');
    });
    const quizItem = document.getElementById('lesson-item-' + quizId);
    if (quizItem) quizItem.classList.add('active');
}

// Show quiz editor panel
function showQuizEditorPanel() {
    const quizPanel = document.getElementById('quizEditorPanel');
    const outlinePanel = document.getElementById('outlinePanel');
    
    quizPanel.style.display = 'block';
    outlinePanel.classList.add('with-editor');
    
    // Add body class for mobile to prevent scroll
    if (window.innerWidth <= 768) {
        document.body.classList.add('editor-open');
    }
}

// Load quiz data and open editor
function loadQuizAndEdit(quizId, sectionId) {
    // Check for unsaved changes
    if (AutoSaveManager.checkUnsavedChanges(() => loadQuizAndEdit(quizId, sectionId))) {
        return;
    }
    
    // Close preview and lesson editor if open
    closePreview();
    closeEditor();

    // Prevent dirty tracking during content load
    AutoSaveManager.isLoadingContent = true;

    // Show loading state
    showQuizEditorPanel();
    document.getElementById('quizTitle').value = '<?php echo addslashes(get_phrase("loading")); ?>...';
    document.getElementById('quizInstruction').value = '';
    document.getElementById('currentQuizId').value = quizId;
    document.getElementById('currentQuizSectionId').value = sectionId;
    document.getElementById('quizEditorTitle').textContent = '<?php echo addslashes(get_phrase("edit_quiz")); ?>';
    
    // Reset dirty state and status
    AutoSaveManager.quizDirty = false;
    AutoSaveManager.updateQuizStatus('');

    // Mark quiz as active
    document.querySelectorAll('.lesson-item').forEach(item => {
        item.classList.remove('active');
    });
    const quizItem = document.getElementById('lesson-item-' + quizId);
    if (quizItem) quizItem.classList.add('active');

    // Clear existing questions
    clearQuestions();

    // Load quiz data via AJAX (cache: false to always get fresh data)
    $.ajax({
        url: '<?php echo site_url('addons/courses/get_quiz_content'); ?>/' + quizId,
        type: 'GET',
        dataType: 'json',
        cache: false,
        success: function(response) {
            if (response.success) {
                document.getElementById('quizTitle').value = response.title || '';
                document.getElementById('quizInstruction').value = response.summary || '';
                updateTitleCounter('quizTitle', 'quizTitleCounter');
                
                // Load questions if any
                if (response.questions && response.questions.length > 0) {
                    response.questions.forEach(function(q) {
                        addQuestion({
                            question: q.title,
                            options: JSON.parse(q.options || '[]'),
                            correct_answers: JSON.parse(q.correct_answers || '[]')
                        });
                    });
                }

                // Get last saved time from server response
                const lastSavedTime = AutoSaveManager.formatTimestamp(response.last_modified);

                // Re-enable dirty tracking after a short delay
                setTimeout(function() {
                    AutoSaveManager.isLoadingContent = false;
                    // Reset dirty state to clean and show last saved time
                    AutoSaveManager.quizDirty = false;
                    if (lastSavedTime) {
                        AutoSaveManager.updateQuizStatus('saved', lastSavedTime);
                    } else {
                        AutoSaveManager.updateQuizStatus('');
                    }
                }, 100);

                // Focus on quiz editor and scroll to top
                focusQuizEditor();
            } else {
                AutoSaveManager.isLoadingContent = false;
                AutoSaveManager.updateQuizStatus('');
                toastr.error('<?php echo addslashes(get_phrase("error_loading_quiz")); ?>');
                document.getElementById('quizTitle').value = '';
            }
        },
        error: function() {
            AutoSaveManager.isLoadingContent = false;
            AutoSaveManager.updateQuizStatus('');
            toastr.error('<?php echo addslashes(get_phrase("error_loading_quiz")); ?>');
            document.getElementById('quizTitle').value = '';
        }
    });
}

// Close quiz editor with unsaved changes check (for X button)
function closeQuizEditorWithCheck() {
    if (AutoSaveManager.checkUnsavedChanges(() => {
        AutoSaveManager.quizDirty = false;
        closeQuizEditor();
    })) {
        return;
    }
    closeQuizEditor();
}

// Close quiz editor (internal - no check)
function closeQuizEditor() {
    const quizPanel = document.getElementById('quizEditorPanel');
    const outlinePanel = document.getElementById('outlinePanel');

    quizPanel.style.display = 'none';
    outlinePanel.classList.remove('with-editor');
    
    // Remove body class for mobile
    document.body.classList.remove('editor-open');
    
    // Reset dirty state
    AutoSaveManager.quizDirty = false;
    AutoSaveManager.updateQuizStatus('');

    // Remove active class from items
    document.querySelectorAll('.lesson-item').forEach(item => {
        item.classList.remove('active');
    });

    // Clear questions
    clearQuestions();
}

// ===== QUESTIONS BUILDER =====
let questionUniqueId = 0; // For unique IDs only, not for display

function clearQuestions() {
    questionUniqueId = 0;
    answerCounters = {};
    document.getElementById('questionsContainer').innerHTML = '';
    document.getElementById('noQuestionsMessage').style.display = 'flex';
}

function addQuestion(questionData = null) {
    questionUniqueId++;
    const container = document.getElementById('questionsContainer');
    document.getElementById('noQuestionsMessage').style.display = 'none';
    
    // Get the current count of questions for display number
    const currentQuestionCount = container.querySelectorAll('.question-card').length + 1;
    
    const questionId = 'question_' + questionUniqueId;
    const questionHtml = `
        <div class="question-card" id="${questionId}">
            <div class="question-card-header">
                <span class="question-number"><?php echo addslashes(get_phrase("question")); ?> ${currentQuestionCount}</span>
                <button type="button" class="btn-remove-question" onclick="removeQuestion('${questionId}')" title="<?php echo addslashes(get_phrase("remove_question")); ?>">
                    <i class="fas fa-trash-can"></i>
                </button>
            </div>
            <div class="question-card-body">
                <div class="question-input-group">
                    <input type="text" class="question-input" placeholder="<?php echo addslashes(get_phrase("enter_your_question")); ?>" value="${questionData ? escapeHtmlAttr(questionData.question) : ''}">
                </div>
                <div class="answers-section">
                    <div class="answers-header">
                        <span><i class="fas fa-list-ul"></i> <?php echo addslashes(get_phrase("answers")); ?></span>
                        <small class="answers-hint"><?php echo addslashes(get_phrase("check_correct_answers")); ?></small>
                    </div>
                    <div class="answers-container" id="answers_${questionId}">
                        <!-- Answers will be added here -->
                    </div>
                    <button type="button" class="btn-add-answer" onclick="addAnswer('${questionId}')">
                        <i class="fas fa-plus"></i> <?php echo addslashes(get_phrase("add_answer")); ?>
                    </button>
                </div>
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', questionHtml);
    
    // Add dirty tracking for question input
    const questionInput = document.querySelector(`#${questionId} .question-input`);
    if (questionInput) {
        questionInput.addEventListener('input', function() {
            AutoSaveManager.setQuizDirty(true);
        });
    }
    
    // Add default answers
    if (questionData && questionData.options) {
        questionData.options.forEach((option, index) => {
            // Compare as numbers since correct_answers contains numbers
            const isCorrect = questionData.correct_answers && (
                questionData.correct_answers.includes(index) || 
                questionData.correct_answers.includes(index.toString())
            );
            addAnswer(questionId, option, isCorrect);
        });
    } else {
        // Add 2 default empty answers
        addAnswer(questionId);
        addAnswer(questionId);
        // Mark as dirty when adding new question (not when loading)
        AutoSaveManager.setQuizDirty(true);
    }
    
    // Scroll to new question
    document.getElementById(questionId).scrollIntoView({ behavior: 'smooth', block: 'center' });
}

let answerCounters = {};

function addAnswer(questionId, answerText = '', isCorrect = false) {
    if (!answerCounters[questionId]) {
        answerCounters[questionId] = 0;
    }
    answerCounters[questionId]++;
    
    const container = document.getElementById('answers_' + questionId);
    const answerId = questionId + '_answer_' + answerCounters[questionId];
    
    const answerHtml = `
        <div class="answer-item" id="${answerId}">
            <label class="answer-checkbox">
                <input type="checkbox" ${isCorrect ? 'checked' : ''}>
                <span class="checkmark"></span>
            </label>
            <input type="text" class="answer-input" placeholder="<?php echo addslashes(get_phrase("enter_answer")); ?>" value="${escapeHtmlAttr(answerText)}">
            <button type="button" class="btn-remove-answer" onclick="removeAnswer('${answerId}', '${questionId}')" title="<?php echo addslashes(get_phrase("remove_answer")); ?>">
                <i class="fas fa-xmark"></i>
            </button>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', answerHtml);
    
    // Add dirty tracking for answer inputs
    const answerEl = document.getElementById(answerId);
    if (answerEl) {
        const answerInput = answerEl.querySelector('.answer-input');
        const checkbox = answerEl.querySelector('input[type="checkbox"]');
        if (answerInput) {
            answerInput.addEventListener('input', function() {
                AutoSaveManager.setQuizDirty(true);
            });
        }
        if (checkbox) {
            checkbox.addEventListener('change', function() {
                AutoSaveManager.setQuizDirty(true);
            });
        }
    }
}

function removeQuestion(questionId) {
    const question = document.getElementById(questionId);
    if (question) {
        question.remove();
        delete answerCounters[questionId];
        
        // Mark as dirty
        AutoSaveManager.setQuizDirty(true);
        
        // Renumber questions
        renumberQuestions();
        
        // Show message if no questions
        if (document.querySelectorAll('.question-card').length === 0) {
            document.getElementById('noQuestionsMessage').style.display = 'flex';
        }
    }
}

function removeAnswer(answerId, questionId) {
    const answer = document.getElementById(answerId);
    const container = document.getElementById('answers_' + questionId);
    
    // Don't remove if only 2 answers left
    if (container.querySelectorAll('.answer-item').length <= 2) {
        toastr.warning('<?php echo addslashes(get_phrase("minimum_two_answers_required")); ?>');
        return;
    }
    
    if (answer) {
        answer.remove();
        // Mark as dirty
        AutoSaveManager.setQuizDirty(true);
    }
}

function renumberQuestions() {
    const questions = document.querySelectorAll('.question-card');
    questions.forEach((q, index) => {
        q.querySelector('.question-number').textContent = '<?php echo addslashes(get_phrase("question")); ?> ' + (index + 1);
    });
}

function getQuestionsData() {
    const questions = [];
    document.querySelectorAll('.question-card').forEach(card => {
        const questionText = card.querySelector('.question-input').value.trim();
        if (!questionText) return;
        
        const options = [];
        const correctAnswers = [];
        
        card.querySelectorAll('.answer-item').forEach((item, index) => {
            const answerText = item.querySelector('.answer-input').value.trim();
            const isCorrect = item.querySelector('input[type="checkbox"]').checked;
            
            if (answerText) {
                options.push(answerText);
                if (isCorrect) {
                    correctAnswers.push(options.length - 1);
                }
            }
        });
        
        if (options.length >= 2) {
            questions.push({
                question: questionText,
                options: options,
                correct_answers: correctAnswers,
                type: 'mcq'
            });
        }
    });
    
    return questions;
}

function escapeHtmlAttr(text) {
    if (!text) return '';
    return text.replace(/"/g, '&quot;').replace(/'/g, '&#39;');
}

// Save quiz
function saveQuiz(isAutosave = false, callback = null) {
    const quizId = document.getElementById('currentQuizId').value;
    const sectionId = document.getElementById('currentQuizSectionId').value;
    const title = document.getElementById('quizTitle').value;
    const instruction = document.getElementById('quizInstruction').value;
    const questions = getQuestionsData();
    
    if (!title.trim()) {
        if (!isAutosave) {
            toastr.error('<?php echo addslashes(get_phrase("please_enter_quiz_title")); ?>');
        }
        return;
    }
    
    // Update status to saving
    AutoSaveManager.updateQuizStatus('saving');
    
    // Check if offline
    if (!AutoSaveManager.isOnline) {
        AutoSaveManager.addToOfflineQueue('quiz', {
            url: '<?php echo site_url('addons/courses/save_quiz'); ?>' + (quizId ? '/' + quizId : ''),
            title: title,
            summary: instruction,
            section_id: sectionId,
            course_id: courseId,
            questions: JSON.stringify(questions)
        });
        AutoSaveManager.updateQuizStatus('offline');
        AutoSaveManager.quizDirty = false;
        if (!isAutosave) {
            toastr.info('<?php echo addslashes(get_phrase("saved_offline")); ?>');
        }
        return;
    }
    
    // Get CSRF token
    const csrfName = document.getElementById('csrf_name').value;
    const csrfHash = document.getElementById('csrf_hash').value;
    
    const data = {
        title: title,
        summary: instruction,
        section_id: sectionId,
        course_id: courseId,
        questions: JSON.stringify(questions)
    };
    
    data[csrfName] = csrfHash;
    
    let url = '<?php echo site_url('addons/courses/save_quiz'); ?>';
    if (quizId) {
        url += '/' + quizId;
    }
    
    $.ajax({
        url: url,
        type: 'POST',
        data: data,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                // Update status
                AutoSaveManager.updateQuizStatus('saved', AutoSaveManager.getCurrentTime());
                AutoSaveManager.quizDirty = false;
                
                // Update CSRF token
                if (response.csrf) {
                    document.getElementById('csrf_hash').value = response.csrf.csrfHash || csrfHash;
                }
                
                if (!quizId) {
                    // New quiz created - add to DOM without page reload
                    const newQuizId = response.quiz_id;
                    addLessonToSidebar(newQuizId, sectionId, title, 'quiz');
                    
                    // Update editor to reference the new quiz
                    document.getElementById('currentQuizId').value = newQuizId;
                    document.getElementById('quizEditorTitle').textContent = '<?php echo addslashes(get_phrase("edit_quiz")); ?>';
                    
                    // Mark the new quiz as active
                    document.querySelectorAll('.lesson-item').forEach(item => item.classList.remove('active'));
                    const newItem = document.getElementById('lesson-item-' + newQuizId);
                    if (newItem) newItem.classList.add('active');
                    
                    toastr.success('<?php echo addslashes(get_phrase("quiz_saved_successfully")); ?>');
                } else {
                    // Existing quiz updated - update sidebar title
                    const quizItem = document.getElementById('lesson-item-' + quizId);
                    if (quizItem) {
                        const nameEl = quizItem.querySelector('.lesson-name');
                        if (nameEl) {
                            nameEl.textContent = title;
                        }
                    }
                    // Update preview title if this quiz is being previewed
                    const previewLessonId = document.getElementById('previewLessonId').value;
                    if (previewLessonId == quizId) {
                        document.getElementById('previewTitle').textContent = title;
                    }
                    
                    if (!isAutosave) {
                        toastr.success('<?php echo addslashes(get_phrase("quiz_saved_successfully")); ?>');
                    }
                }
                
                // Execute callback if provided
                if (callback) callback();
                
            } else {
                AutoSaveManager.updateQuizStatus('error');
                toastr.error(response.message || '<?php echo addslashes(get_phrase("error_saving_quiz")); ?>');
            }
        },
        error: function(xhr) {
            AutoSaveManager.updateQuizStatus('error');
            try {
                var errorResponse = JSON.parse(xhr.responseText);
                if (errorResponse.csrf) {
                    document.getElementById('csrf_hash').value = errorResponse.csrf.csrfHash || csrfHash;
                }
            } catch(e) {}
            if (!isAutosave) {
                toastr.error('<?php echo addslashes(get_phrase("error_saving_quiz")); ?>');
            }
        }
    });
}

// Save lesson
function saveLesson(isAutosave = false, callback = null) {
    const lessonId = document.getElementById('currentLessonId').value;
    const sectionId = document.getElementById('currentSectionId').value;
    const title = document.getElementById('lessonTitle').value;
    const content = quill.root.innerHTML;
    
    if (!title.trim()) {
        if (!isAutosave) {
            toastr.error('<?php echo addslashes(get_phrase("please_enter_lesson_title")); ?>');
        }
        return;
    }
    
    // Update status to saving
    AutoSaveManager.updateLessonStatus('saving');
    
    // Check if offline
    if (!AutoSaveManager.isOnline) {
        // Queue for later
        AutoSaveManager.addToOfflineQueue('lesson', {
            url: '<?php echo site_url('addons/courses/save_lesson'); ?>' + (lessonId ? '/' + lessonId : ''),
            title: title,
            content: content,
            section_id: sectionId,
            course_id: courseId
        });
        AutoSaveManager.updateLessonStatus('offline');
        AutoSaveManager.lessonDirty = false;
        if (!isAutosave) {
            toastr.info('<?php echo addslashes(get_phrase("saved_offline")); ?>');
        }
        return;
    }
    
    // Get CSRF token
    const csrfName = document.getElementById('csrf_name').value;
    const csrfHash = document.getElementById('csrf_hash').value;
    
    const data = {
        title: title,
        content: content,
        section_id: sectionId,
        course_id: courseId
    };
    
    data[csrfName] = csrfHash;
    
    let url = '<?php echo site_url('addons/courses/save_lesson'); ?>';
    if (lessonId) {
        url += '/' + lessonId;
    }
    
    $.ajax({
        url: url,
        type: 'POST',
        data: data,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                // Update status
                AutoSaveManager.updateLessonStatus('saved', AutoSaveManager.getCurrentTime());
                AutoSaveManager.lessonDirty = false;
                
                // Update CSRF token for next request
                if (response.csrf) {
                    document.getElementById('csrf_name').value = response.csrf.csrfName || csrfName;
                    document.getElementById('csrf_hash').value = response.csrf.csrfHash || csrfHash;
                }
                
                if (!lessonId) {
                    // New lesson created - add to DOM without page reload
                    const newLessonId = response.lesson_id;
                    addLessonToSidebar(newLessonId, sectionId, title, 'lesson');
                    
                    // Update editor to reference the new lesson
                    document.getElementById('currentLessonId').value = newLessonId;
                    document.getElementById('lessonEditorTitle').textContent = '<?php echo addslashes(get_phrase("edit_lesson")); ?>';
                    
                    // Mark the new lesson as active
                    document.querySelectorAll('.lesson-item').forEach(item => item.classList.remove('active'));
                    const newItem = document.getElementById('lesson-item-' + newLessonId);
                    if (newItem) newItem.classList.add('active');
                    
                    toastr.success('<?php echo addslashes(get_phrase("lesson_saved_successfully")); ?>');
                } else {
                    // Existing lesson updated - update sidebar title
                    const lessonItem = document.getElementById('lesson-item-' + lessonId);
                    if (lessonItem) {
                        const nameEl = lessonItem.querySelector('.lesson-name');
                        if (nameEl) {
                            nameEl.textContent = title;
                        }
                    }
                    // Update preview title if this lesson is being previewed
                    const previewLessonId = document.getElementById('previewLessonId').value;
                    if (previewLessonId == lessonId) {
                        document.getElementById('previewTitle').textContent = title;
                    }
                    
                    if (!isAutosave) {
                        toastr.success('<?php echo addslashes(get_phrase("lesson_saved_successfully")); ?>');
                    }
                }
                
                // Execute callback if provided
                if (callback) callback();
                
            } else {
                AutoSaveManager.updateLessonStatus('error');
                toastr.error(response.message || '<?php echo addslashes(get_phrase("error_saving_lesson")); ?>');
            }
        },
        error: function(xhr) {
            AutoSaveManager.updateLessonStatus('error');
            // Try to update CSRF token from error response
            try {
                var errorResponse = JSON.parse(xhr.responseText);
                if (errorResponse.csrf) {
                    document.getElementById('csrf_name').value = errorResponse.csrf.csrfName || csrfName;
                    document.getElementById('csrf_hash').value = errorResponse.csrf.csrfHash || csrfHash;
                }
            } catch(e) {}
            if (!isAutosave) {
                toastr.error('<?php echo addslashes(get_phrase("error_saving_lesson")); ?>');
            }
        }
    });
}

// Outline functions
function toggleSection(sectionId) {
    const lessonsEl = document.getElementById('lessons-' + sectionId);
    const toggleEl = document.getElementById('toggle-' + sectionId);
    
    lessonsEl.classList.toggle('collapsed');
    toggleEl.classList.toggle('rotated');
}

function toggleLessonMenu(lessonId) {
    // Close all other menus and remove active class from dropdowns
    document.querySelectorAll('.lesson-menu.show').forEach(menu => {
        if (menu.id !== 'menu-' + lessonId) {
            menu.classList.remove('show');
            menu.closest('.lesson-dropdown').classList.remove('menu-open');
        }
    });
    
    const menu = document.getElementById('menu-' + lessonId);
    const dropdown = menu.closest('.lesson-dropdown');
    menu.classList.toggle('show');
    dropdown.classList.toggle('menu-open');
}

// Close menus when clicking outside
document.addEventListener('click', function(e) {
    if (!e.target.closest('.lesson-dropdown')) {
        document.querySelectorAll('.lesson-menu.show').forEach(menu => {
            menu.classList.remove('show');
            menu.closest('.lesson-dropdown').classList.remove('menu-open');
        });
    }
});

// SweetAlert2 Delete Confirmation
function confirmDelete(url) {
    // Extract the item type and ID from URL
    // URL format: .../lessons/{course_id}/delete/{lesson_id} or .../course_sections/{course_id}/delete/{section_id}
    const urlParts = url.split('/');
    const deleteIndex = urlParts.indexOf('delete');
    const itemId = deleteIndex >= 0 ? urlParts[deleteIndex + 1] : null;
    const isSection = url.includes('course_sections');
    const isLesson = url.includes('lessons');
    
    Swal.fire({
        title: '<?php echo addslashes(get_phrase("are_you_sure")); ?>',
        text: '<?php echo addslashes(get_phrase("you_wont_be_able_to_revert_this")); ?>',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#6366f1',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<?php echo addslashes(get_phrase("yes_delete_it")); ?>',
        cancelButtonText: '<?php echo addslashes(get_phrase("cancel")); ?>',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: url,
                type: 'GET',
                success: function(response) {
                    Swal.fire({
                        title: '<?php echo addslashes(get_phrase("deleted")); ?>',
                        text: '<?php echo addslashes(get_phrase("item_has_been_deleted")); ?>',
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    });
                    
                    // Handle removal without page reload
                    if (isLesson && itemId) {
                        // Check if this lesson/quiz is currently being previewed
                        const previewLessonId = document.getElementById('previewLessonId').value;
                        if (previewLessonId == itemId) {
                            closePreview();
                        }
                        
                        // Remove the lesson item from DOM
                        const lessonItem = document.getElementById('lesson-item-' + itemId);
                        if (lessonItem) {
                            const sectionLessons = lessonItem.closest('.section-lessons');
                            lessonItem.remove();
                            
                            // Renumber remaining lessons in the section
                            if (sectionLessons) {
                                const sectionId = sectionLessons.id.replace('lessons-', '');
                                renumberLessons(sectionId);
                            }
                        }
                    } else if (isSection && itemId) {
                        // Check if a lesson from this section is being previewed
                        const previewSectionId = document.getElementById('previewSectionId').value;
                        if (previewSectionId == itemId) {
                            closePreview();
                        }
                        
                        // Remove the section from DOM
                        const sectionEl = document.getElementById('outline-section-' + itemId);
                        if (sectionEl) {
                            sectionEl.remove();
                            renumberSections();
                        }
                    }
                },
                error: function() {
                    Swal.fire({
                        title: '<?php echo addslashes(get_phrase("error")); ?>',
                        text: '<?php echo addslashes(get_phrase("something_went_wrong")); ?>',
                        icon: 'error'
                    });
                }
            });
        }
    });
}

// ===== PAGINATION AJAX =====
let currentPage = 1;
let totalPages = <?php echo isset($total_pages) ? $total_pages : 1; ?>;
const sectionsPerPage = 10;
let currentStartNumber = 1; // Track the starting section number for the current page

function loadPage(page, forceReload = false) {
    if (page < 1) return;
    if (!forceReload && (page > totalPages || page === currentPage)) return;
    
    const container = document.getElementById('sectionsContent');
    const loading = document.getElementById('sectionsLoading');
    
    // Afficher le loading
    container.style.opacity = '0.5';
    loading.style.display = 'flex';
    
    $.ajax({
        url: '<?php echo site_url('addons/courses/ajax_get_sections_paginated/'.$course['id']); ?>',
        type: 'GET',
        data: { page: page },
        dataType: 'json',
        success: function(response) {
            if (response.status) {
                // Mettre à jour le CSRF
                if (response.csrf) {
                    document.getElementById('csrf_hash').value = response.csrf.csrfHash;
                }
                
                // Générer le HTML des sections
                let html = '';
                let sectionNumber = response.start_number;
                currentStartNumber = response.start_number; // Store for renumbering after drag
                
                response.sections.forEach(function(section) {
                    html += renderSection(section, sectionNumber);
                    sectionNumber++;
                });
                
                container.innerHTML = html;
                currentPage = response.current_page;
                totalPages = response.total_pages;
                
                // Mettre à jour la pagination
                updatePaginationUI();
            }
        },
        error: function() {
            toastr.error('<?php echo addslashes(get_phrase("error_loading_sections")); ?>');
        },
        complete: function() {
            container.style.opacity = '1';
            loading.style.display = 'none';
        }
    });
}

function renderSection(section, sectionNumber) {
    let lessonsHtml = '';
    let lessonNumber = 0;
    
    section.lessons.forEach(function(lesson) {
        lessonNumber++;
        const isQuiz = lesson.lesson_type === 'quiz';
        const lessonClass = isQuiz ? 'lesson-item quiz-item' : 'lesson-item';
        const lessonIcon = isQuiz ? 'fa-circle-question' : 'fa-file-lines';
        const lessonLabel = isQuiz ? '<?php echo addslashes(get_phrase("quiz")); ?>' : '<?php echo addslashes(get_phrase("lesson")); ?>';
        
        const clickHandler = isQuiz ? `openQuizPreview(${lesson.id}, ${section.id}, '${escapeHtml(lesson.title)}')` : `openLessonPreview(${lesson.id}, ${section.id}, '${escapeHtml(lesson.title)}')`;
        
        const generateBtn = !isQuiz ? `
            <button type="button" class="btn-icon btn-generate-lesson" onclick="event.stopPropagation(); openGenerateLessonModal(${lesson.id}, ${section.id}, ${lesson.attachment_type || lesson.video_url ? 'true' : 'false'})" title="<?php echo addslashes(get_phrase("generate_with_ai")); ?>">
                <i class="fas fa-wand-magic-sparkles" style="color: orange;"></i>
            </button>
        ` : '';
        
        lessonsHtml += `
            <div class="lesson-item ${isQuiz ? 'quiz-item' : ''}" id="lesson-item-${lesson.id}" data-lesson-id="${lesson.id}">
                <div class="lesson-drag-handle" title="<?php echo addslashes(get_phrase("drag_to_reorder")); ?>">
                    <i class="fas fa-grip-vertical"></i>
                </div>
                <div class="lesson-info" onclick="${clickHandler}">
                    <i class="fas ${lessonIcon} lesson-icon"></i>
                    <span class="lesson-label">${lessonLabel} ${lessonNumber}:</span>
                    <span class="lesson-name">${escapeHtml(lesson.title)}</span>
                </div>
                <div class="lesson-actions">
                    ${generateBtn}
                    <button type="button" class="btn-icon btn-delete-lesson" onclick="event.stopPropagation(); confirmDelete('<?php echo site_url('addons/courses/lessons/'.$course['id'].'/delete/'); ?>${lesson.id}')" title="<?php echo addslashes(get_phrase("delete")); ?>">
                        <i class="fas fa-trash-can"></i>
                    </button>
                </div>
            </div>
        `;
    });
    
    return `
        <div class="outline-section" id="outline-section-${section.id}" data-section-id="${section.id}">
            <div class="section-header" onclick="toggleSection(${section.id})">
                <div class="section-drag-handle" onclick="event.stopPropagation();" title="<?php echo addslashes(get_phrase("drag_to_reorder")); ?>">
                    <i class="fas fa-grip-vertical"></i>
                </div>
                <div class="section-info">
                    <i class="fas fa-folder section-icon"></i>
                    <span class="section-label"><?php echo addslashes(get_phrase("section")); ?> ${sectionNumber}:</span>
                    <span class="section-name" id="section-name-${section.id}" ondblclick="event.stopPropagation(); startEditSection(${section.id})">${escapeHtml(section.title)}</span>
                    <input type="text" class="section-name-input" id="section-input-${section.id}" value="${escapeHtmlAttr(section.title)}" style="display: none;" onkeydown="handleSectionEditKeydown(event, ${section.id})" onblur="saveSectionName(${section.id})">
                </div>
                <div class="section-actions">
                    <button type="button" class="btn-icon" onclick="event.stopPropagation(); openMoveSection(${section.id}, ${sectionNumber})" title="<?php echo addslashes(get_phrase("move_to_position")); ?>">
                        <i class="fas fa-up-down-left-right"></i>
                    </button>
                    <button type="button" class="btn-icon" onclick="event.stopPropagation(); startEditSection(${section.id})" title="<?php echo addslashes(get_phrase("edit_section")); ?>">
                        <i class="fas fa-pen-to-square"></i>
                    </button>
                    <button type="button" class="btn-icon" onclick="event.stopPropagation(); confirmDelete('<?php echo site_url('addons/courses/course_sections/'.$course['id'].'/delete/'); ?>${section.id}')" title="<?php echo addslashes(get_phrase("delete_section")); ?>">
                        <i class="fas fa-trash-can"></i>
                    </button>
                    <i class="fas fa-chevron-right section-toggle" id="toggle-${section.id}"></i>
                </div>
            </div>
            
            <div class="section-lessons collapsed" id="lessons-${section.id}">
                ${lessonsHtml}
                
                <div class="add-lesson-buttons">
                    <div class="lesson-dropdown-container">
                        <button type="button" class="btn-add-lesson" onclick="toggleLessonDropdown(${section.id})">
                            <i class="fas fa-plus"></i> <?php echo addslashes(get_phrase("add_lesson")); ?>
                            <i class="fas fa-chevron-down lesson-dropdown-arrow"></i>
                        </button>
                        <div class="lesson-dropdown-menu" id="lessonDropdown-${section.id}">
                            <button type="button" class="lesson-dropdown-item" onclick="openNewLessonEditor(${section.id}); closeLessonDropdowns();">
                                <i class="fas fa-pen-to-square"></i>
                                <div class="lesson-dropdown-item-content">
                                    <span class="lesson-dropdown-item-title"><?php echo addslashes(get_phrase("add_manually")); ?></span>
                                    <span class="lesson-dropdown-item-desc"><?php echo addslashes(get_phrase("create_lesson_with_editor")); ?></span>
                                </div>
                            </button>
                            <button type="button" class="lesson-dropdown-item lesson-dropdown-item-ai" onclick="openAiLessonModal(${section.id}); closeLessonDropdowns();">
                                <i class="fas fa-wand-magic-sparkles"></i>
                                <div class="lesson-dropdown-item-content">
                                    <span class="lesson-dropdown-item-title"><?php echo addslashes(get_phrase("generate_with_wayo_ai")); ?></span>
                                    <span class="lesson-dropdown-item-desc"><?php echo addslashes(get_phrase("create_from_course_outline")); ?></span>
                                </div>
                            </button>
                        </div>
                    </div>
                    <div class="quiz-dropdown-container">
                        <button type="button" class="btn-add-lesson btn-add-quiz" onclick="toggleQuizDropdown(${section.id})">
                            <i class="fas fa-plus"></i> <?php echo addslashes(get_phrase("add_quiz")); ?>
                            <i class="fas fa-chevron-down quiz-dropdown-arrow"></i>
                        </button>
                        <div class="quiz-dropdown-menu" id="quizDropdown-${section.id}">
                            <button type="button" class="quiz-dropdown-item" onclick="openNewQuizEditor(${section.id}); closeQuizDropdowns();">
                                <i class="fas fa-pen-to-square"></i>
                                <div class="quiz-dropdown-item-content">
                                    <span class="quiz-dropdown-item-title"><?php echo addslashes(get_phrase("add_manually")); ?></span>
                                    <span class="quiz-dropdown-item-desc"><?php echo addslashes(get_phrase("create_quiz_with_editor")); ?></span>
                                </div>
                            </button>
                            <button type="button" class="quiz-dropdown-item quiz-dropdown-item-ai" onclick="openAiQuizModal(${section.id}); closeQuizDropdowns();">
                                <i class="fas fa-wand-magic-sparkles"></i>
                                <div class="quiz-dropdown-item-content">
                                    <span class="quiz-dropdown-item-title"><?php echo addslashes(get_phrase("generate_with_wayo_ai")); ?></span>
                                    <span class="quiz-dropdown-item-desc"><?php echo addslashes(get_phrase("create_from_lessons")); ?></span>
                                </div>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
}

function escapeHtml(text) {
    if (!text) return '';
    // First decode HTML entities, then escape for JS string
    const div = document.createElement('div');
    div.innerHTML = text; // Decode HTML entities
    const decoded = div.textContent || div.innerText || '';
    // Escape for JavaScript string (inside single quotes)
    return decoded
        .replace(/\\/g, '\\\\')     // Escape backslashes
        .replace(/'/g, "\\'")       // Escape single quotes
        .replace(/\n/g, '\\n')      // Escape newlines
        .replace(/\r/g, '\\r')      // Escape carriage returns
        .replace(/\t/g, '\\t')      // Escape tabs
        .replace(/"/g, '\\"');      // Escape double quotes (just in case)
}

// Add new lesson/quiz to sidebar without page reload
function addLessonToSidebar(itemId, sectionId, title, type = 'lesson') {
    const lessonsContainer = document.getElementById('lessons-' + sectionId);
    if (!lessonsContainer) {
        console.error('Lessons container not found for section:', sectionId);
        return;
    }
    
    // Count existing lessons/quizzes in this section to get the number
    const existingItems = lessonsContainer.querySelectorAll('.lesson-item');
    const itemNumber = existingItems.length + 1;
    
    const isQuiz = type === 'quiz';
    const itemClass = isQuiz ? 'lesson-item quiz-item' : 'lesson-item';
    const itemIcon = isQuiz ? 'fa-circle-question' : 'fa-file-lines';
        const itemLabel = isQuiz ? '<?php echo addslashes(get_phrase("quiz")); ?>' : '<?php echo addslashes(get_phrase("lesson")); ?>';
    const clickHandler = isQuiz 
        ? `openQuizPreview(${itemId}, ${sectionId}, '${escapeHtml(title)}')`
        : `openLessonPreview(${itemId}, ${sectionId}, '${escapeHtml(title)}')`;
    
    const generateBtn = !isQuiz ? `
        <button type="button" class="btn-icon btn-generate-lesson" onclick="event.stopPropagation(); openGenerateLessonModal(${itemId}, ${sectionId}, false)" title="<?php echo addslashes(get_phrase("generate_with_ai")); ?>">
            <i class="fas fa-wand-magic-sparkles" style="color: orange;"></i>
        </button>
    ` : '';
    
    const itemHtml = `
        <div class="${itemClass}" id="lesson-item-${itemId}" data-lesson-id="${itemId}">
            <div class="lesson-drag-handle" title="<?php echo addslashes(get_phrase("drag_to_reorder")); ?>">
                <i class="fas fa-grip-vertical"></i>
            </div>
            <div class="lesson-info" onclick="${clickHandler}">
                <i class="fas ${itemIcon} lesson-icon"></i>
                <span class="lesson-label">${itemLabel} ${itemNumber}:</span>
                <span class="lesson-name">${escapeHtml(title).replace(/\\'/g, "'")}</span>
            </div>
            <div class="lesson-actions">
                ${generateBtn}
                <button type="button" class="btn-icon btn-delete-lesson" onclick="event.stopPropagation(); confirmDelete('<?php echo site_url('addons/courses/lessons/'.$course['id'].'/delete/'); ?>${itemId}')" title="<?php echo addslashes(get_phrase("delete")); ?>">
                    <i class="fas fa-trash-can"></i>
                </button>
            </div>
        </div>
    `;
    
    // Insert before the Add Lesson/Quiz buttons
    const addButtons = lessonsContainer.querySelector('.add-lesson-buttons');
    if (addButtons) {
        addButtons.insertAdjacentHTML('beforebegin', itemHtml);
    } else {
        // If no add buttons found, just append
        lessonsContainer.insertAdjacentHTML('beforeend', itemHtml);
    }
    
    // Reinitialize drag and drop for the section
    if (typeof initLessonsDragula === 'function') {
        initLessonsDragula();
    }
}

function updatePaginationUI() {
    const currentPageEl = document.getElementById('currentPage');
    const totalPagesEl = document.getElementById('totalPages');
    const prevBtn = document.getElementById('prevPage');
    const nextBtn = document.getElementById('nextPage');
    
    if (currentPageEl) currentPageEl.textContent = currentPage;
    if (totalPagesEl) totalPagesEl.textContent = totalPages;
    if (prevBtn) prevBtn.disabled = (currentPage <= 1);
    if (nextBtn) nextBtn.disabled = (currentPage >= totalPages);
}

// ========== SECTION INLINE EDITING ==========

// Show add section form
function showAddSectionForm() {
    const form = document.getElementById('addSectionForm');
    const input = document.getElementById('newSectionTitle');
    form.style.display = 'block';
    input.value = '';
    input.focus();
}

// Hide add section form
function hideAddSectionForm() {
    const form = document.getElementById('addSectionForm');
    form.style.display = 'none';
}

// Handle keydown on add section input
function handleAddSectionKeydown(event) {
    if (event.key === 'Enter') {
        event.preventDefault();
        saveNewSection();
    } else if (event.key === 'Escape') {
        hideAddSectionForm();
    }
}

// Save new section via AJAX
function saveNewSection() {
    const input = document.getElementById('newSectionTitle');
    const title = input.value.trim();
    
    if (!title) {
        toastr.error('<?php echo addslashes(get_phrase("please_enter_section_title")); ?>');
        input.focus();
        return;
    }
    
    const csrfName = document.getElementById('csrf_name').value;
    const csrfHash = document.getElementById('csrf_hash').value;
    
    const data = { title: title };
    data[csrfName] = csrfHash;
    
    $.ajax({
        url: '<?php echo site_url('addons/courses/ajax_add_section/'.$course['id']); ?>',
        type: 'POST',
        data: data,
        dataType: 'json',
        success: function(response) {
            if (response.csrf) {
                document.getElementById('csrf_hash').value = response.csrf.csrfHash;
            }
            if (response.success) {
                toastr.success('<?php echo addslashes(get_phrase("section_added_successfully")); ?>');
                hideAddSectionForm();
                // Hide empty state if visible
                const emptyState = document.getElementById('emptyState');
                if (emptyState) emptyState.style.display = 'none';
                // Update total pages and go to last page to see new section
                if (response.total_pages) {
                    totalPages = response.total_pages;
                }
                // Update pagination UI visibility
                updatePaginationUI();
                loadPage(totalPages, true);
            } else {
                toastr.error(response.message || '<?php echo addslashes(get_phrase("error_adding_section")); ?>');
            }
        },
        error: function() {
            toastr.error('<?php echo addslashes(get_phrase("error_adding_section")); ?>');
        }
    });
}

// Start editing section name
let editingSectionId = null;

function startEditSection(sectionId) {
    // Cancel any other editing
    if (editingSectionId && editingSectionId !== sectionId) {
        cancelEditSection(editingSectionId);
    }
    
    editingSectionId = sectionId;
    const nameSpan = document.getElementById('section-name-' + sectionId);
    const input = document.getElementById('section-input-' + sectionId);
    
    if (nameSpan && input) {
        nameSpan.style.display = 'none';
        input.style.display = 'inline-block';
        input.focus();
        input.select();
    }
}

// Cancel editing section
function cancelEditSection(sectionId) {
    const nameSpan = document.getElementById('section-name-' + sectionId);
    const input = document.getElementById('section-input-' + sectionId);
    
    if (nameSpan && input) {
        input.value = nameSpan.textContent;
        input.style.display = 'none';
        nameSpan.style.display = 'inline';
    }
    
    if (editingSectionId === sectionId) {
        editingSectionId = null;
    }
}

// Handle keydown on section name input
function handleSectionEditKeydown(event, sectionId) {
    if (event.key === 'Enter') {
        event.preventDefault();
        event.target.blur(); // This will trigger saveSectionName
    } else if (event.key === 'Escape') {
        event.preventDefault();
        cancelEditSection(sectionId);
    }
}

// ========== MOVE SECTION TO POSITION ==========

// Open move section modal
function openMoveSection(sectionId, currentPosition) {
    document.getElementById('moveSectionId').value = sectionId;
    document.getElementById('currentSectionPosition').textContent = currentPosition;
    document.getElementById('totalSectionsCount').textContent = totalSectionsGlobal;
    document.getElementById('targetPosition').value = currentPosition;
    document.getElementById('targetPosition').max = totalSectionsGlobal;
    document.getElementById('moveSectionModal').classList.add('show');
    document.getElementById('targetPosition').focus();
    document.getElementById('targetPosition').select();
}

// Close move section modal
function closeMoveSectionModal() {
    document.getElementById('moveSectionModal').classList.remove('show');
}

// Handle Enter key in move position input
document.addEventListener('keydown', function(e) {
    if (e.key === 'Enter' && document.getElementById('moveSectionModal').classList.contains('show')) {
        e.preventDefault();
        confirmMoveSection();
    } else if (e.key === 'Escape' && document.getElementById('moveSectionModal').classList.contains('show')) {
        closeMoveSectionModal();
    }
});

// Confirm and execute move section
function confirmMoveSection() {
    const sectionId = document.getElementById('moveSectionId').value;
    const targetPosition = parseInt(document.getElementById('targetPosition').value);
    const currentPosition = parseInt(document.getElementById('currentSectionPosition').textContent);
    
    if (!targetPosition || targetPosition < 1 || targetPosition > totalSectionsGlobal) {
        toastr.error('<?php echo addslashes(get_phrase("invalid_position")); ?>');
        return;
    }
    
    if (targetPosition === currentPosition) {
        closeMoveSectionModal();
        return;
    }
    
    const csrfName = document.getElementById('csrf_name').value;
    const csrfHash = document.getElementById('csrf_hash').value;
    
    $.ajax({
        url: '<?php echo site_url('addons/courses/ajax_move_section'); ?>',
        type: 'POST',
        data: {
            section_id: sectionId,
            target_position: targetPosition,
            course_id: courseId,
            [csrfName]: csrfHash
        },
        dataType: 'json',
        success: function(response) {
            if (response.csrf) {
                document.getElementById('csrf_hash').value = response.csrf.csrfHash;
            }
            if (response.success) {
                toastr.success('<?php echo addslashes(get_phrase("section_moved_successfully")); ?>');
                closeMoveSectionModal();
                // Calculate target page and reload
                const targetPage = Math.ceil(targetPosition / sectionsPerPage);
                loadPage(targetPage, true);
            } else {
                toastr.error(response.message || '<?php echo addslashes(get_phrase("error_moving_section")); ?>');
            }
        },
        error: function() {
            toastr.error('<?php echo addslashes(get_phrase("error_moving_section")); ?>');
        }
    });
}

// Save section name via AJAX
let savingSectionId = null;

function saveSectionName(sectionId) {
    // Prevent double-saving
    if (savingSectionId === sectionId) return;
    
    const nameSpan = document.getElementById('section-name-' + sectionId);
    const input = document.getElementById('section-input-' + sectionId);
    
    if (!nameSpan || !input) return;
    
    const newTitle = input.value.trim();
    const oldTitle = nameSpan.textContent;
    
    // If empty or same, cancel
    if (!newTitle || newTitle === oldTitle) {
        cancelEditSection(sectionId);
        return;
    }
    
    savingSectionId = sectionId;
    
    const csrfName = document.getElementById('csrf_name').value;
    const csrfHash = document.getElementById('csrf_hash').value;
    
    const data = { title: newTitle };
    data[csrfName] = csrfHash;
    
    $.ajax({
        url: '<?php echo site_url('addons/courses/ajax_update_section/'); ?>' + sectionId,
        type: 'POST',
        data: data,
        dataType: 'json',
        success: function(response) {
            if (response.csrf) {
                document.getElementById('csrf_hash').value = response.csrf.csrfHash;
            }
            if (response.success) {
                nameSpan.textContent = newTitle;
                toastr.success('<?php echo addslashes(get_phrase("section_updated_successfully")); ?>');
            } else {
                toastr.error(response.message || '<?php echo addslashes(get_phrase("error_updating_section")); ?>');
                input.value = oldTitle;
            }
        },
        error: function() {
            toastr.error('<?php echo addslashes(get_phrase("error_updating_section")); ?>');
            input.value = oldTitle;
        },
        complete: function() {
            input.style.display = 'none';
            nameSpan.style.display = 'inline';
            editingSectionId = null;
            savingSectionId = null;
        }
    });
}

function addTooltipsToQuillToolbar() {
    if (!quill || !quill.container) return;
    
    const toolbar = quill.container.previousElementSibling;
    if (!toolbar) return;

    // Map des tooltips avec les sélecteurs exacts utilisés par Quill
    const tooltipConfig = [
        { selector: 'button.ql-undo', title: '<?php echo addslashes(get_phrase("undo")); ?> (Ctrl+Z)' },
        { selector: 'button.ql-redo', title: '<?php echo addslashes(get_phrase("redo")); ?> (Ctrl+Y)' },
        { selector: 'button.ql-bold', title: '<?php echo addslashes(get_phrase("bold")); ?> (Ctrl+B)' },
        { selector: 'button.ql-italic', title: '<?php echo addslashes(get_phrase("italic")); ?> (Ctrl+I)' },
        { selector: 'button.ql-underline', title: '<?php echo addslashes(get_phrase("underline")); ?> (Ctrl+U)' },
        { selector: 'button.ql-strike', title: '<?php echo addslashes(get_phrase("strike")); ?>' },
        { selector: '.ql-header .ql-picker-label', title: '<?php echo addslashes(get_phrase("headings")); ?>' },
        { selector: '.ql-size .ql-picker-label', title: '<?php echo addslashes(get_phrase("font_size")); ?>' },
        { selector: '.ql-color .ql-picker-label', title: '<?php echo addslashes(get_phrase("text_color")); ?>' },
        { selector: '.ql-background .ql-picker-label', title: '<?php echo addslashes(get_phrase("background_color")); ?>' },
        
        // Listes numérotées et puces
        { selector: 'button.ql-list[value="ordered"]', title: '<?php echo addslashes(get_phrase("ordered_list")); ?>' },
        { selector: 'button.ql-list[value="bullet"]', title: '<?php echo addslashes(get_phrase("bullet_list")); ?>' },
        
        // Indentation
        { selector: 'button.ql-indent[value="+1"]', title: '<?php echo addslashes(get_phrase("indent")); ?>' },
        { selector: 'button.ql-indent[value="-1"]', title: '<?php echo addslashes(get_phrase("outdent")); ?>' },
        
        // Alignement (le picker global et chaque bouton)
        { selector: '.ql-align .ql-picker-label', title: '<?php echo addslashes(get_phrase("text_alignment")); ?>' },
        { selector: 'button.ql-align[value=""]', title: '<?php echo addslashes(get_phrase("align_left")); ?>' },
        { selector: 'button.ql-align[value="center"]', title: '<?php echo addslashes(get_phrase("align_center")); ?>' },
        { selector: 'button.ql-align[value="right"]', title: '<?php echo addslashes(get_phrase("align_right")); ?>' },
        { selector: 'button.ql-align[value="justify"]', title: '<?php echo addslashes(get_phrase("align_justify")); ?>' },
        
        // Autres
        { selector: 'button.ql-blockquote', title: '<?php echo addslashes(get_phrase("blockquote")); ?>' },
        { selector: 'button.ql-code-block', title: '<?php echo addslashes(get_phrase("code_block")); ?>' },
        { selector: 'button.ql-link', title: '<?php echo addslashes(get_phrase("insert_link")); ?> (Ctrl+K)' },
        { selector: 'button.ql-image', title: '<?php echo addslashes(get_phrase("insert_image")); ?> (max 5MB)' },
        { selector: 'button.ql-video', title: '<?php echo addslashes(get_phrase("insert_video")); ?>' },
        { selector: 'button.ql-attachment', title: '<?php echo addslashes(get_phrase("attach_file")); ?>' }, // ton bouton personnalisé
        { selector: 'button.ql-clean', title: '<?php echo addslashes(get_phrase("clear_formatting")); ?>' }
    ];

    tooltipConfig.forEach(item => {
        toolbar.querySelectorAll(item.selector).forEach(el => {
            if (!el.hasAttribute('title') || el.getAttribute('title') === '') {
                el.setAttribute('title', item.title);
            }
        });
    });
}

// Surcharge de initEditor pour ajouter les tooltips après initialisation
let tooltipTimeoutId = null;

const originalInitEditor = initEditor;
initEditor = function() {
    originalInitEditor();
    // Attendre que la toolbar soit complètement rendue
    if (tooltipTimeoutId) clearTimeout(tooltipTimeoutId);
    tooltipTimeoutId = setTimeout(addTooltipsToQuillToolbar, 100);
};

// Si l'éditeur est déjà initialisé (cas où on ouvre une leçon existante rapidement)
if (editorInitialized && quill) {
    if (tooltipTimeoutId) clearTimeout(tooltipTimeoutId);
    tooltipTimeoutId = setTimeout(addTooltipsToQuillToolbar, 100);
}

// Optionnel : réappliquer les tooltips quand on ouvre une nouvelle leçon (au cas où)
const originalOpenNewLessonEditor = openNewLessonEditor;
openNewLessonEditor = function(sectionId) {
    originalOpenNewLessonEditor(sectionId);
    if (tooltipTimeoutId) clearTimeout(tooltipTimeoutId);
    tooltipTimeoutId = setTimeout(addTooltipsToQuillToolbar, 150);
};

const originalOpenLessonEditor = openLessonEditor;
openLessonEditor = function(lessonId, sectionId, title) {
    originalOpenLessonEditor(lessonId, sectionId, title);
    if (tooltipTimeoutId) clearTimeout(tooltipTimeoutId);
    tooltipTimeoutId = setTimeout(addTooltipsToQuillToolbar, 150);
};

// ========== DRAG & DROP (DRAGULA) ==========

let sectionsDrake = null;
let lessonsDrakes = {};
let autoScrollAnimationId = null;
let isDraggingGlobal = false;

// Auto-scroll configuration
const AUTO_SCROLL_MARGIN = 80; // pixels from edge to trigger scroll
const AUTO_SCROLL_SPEED_SLOW = 8;
const AUTO_SCROLL_SPEED_FAST = 20;

// Global auto-scroll handler
function initGlobalAutoScroll() {
    const outlineContent = document.querySelector('.outline-content');
    const outlinePanel = document.querySelector('.outline-panel');
    
    document.addEventListener('mousemove', function(e) {
        if (!isDraggingGlobal) return;
        
        // Scroll outline-content
        if (outlineContent) {
            const rect = outlineContent.getBoundingClientRect();
            const mouseY = e.clientY;
            
            // Calculate scroll speed based on distance from edge
            if (mouseY < rect.top + AUTO_SCROLL_MARGIN && mouseY > rect.top - 100) {
                const distance = rect.top + AUTO_SCROLL_MARGIN - mouseY;
                const speed = Math.min(AUTO_SCROLL_SPEED_FAST, AUTO_SCROLL_SPEED_SLOW + (distance / 10));
                outlineContent.scrollTop -= speed;
            }
            else if (mouseY > rect.bottom - AUTO_SCROLL_MARGIN && mouseY < rect.bottom + 100) {
                const distance = mouseY - (rect.bottom - AUTO_SCROLL_MARGIN);
                const speed = Math.min(AUTO_SCROLL_SPEED_FAST, AUTO_SCROLL_SPEED_SLOW + (distance / 10));
                outlineContent.scrollTop += speed;
            }
        }
        
        // Also scroll outline-panel if needed
        if (outlinePanel) {
            const rect = outlinePanel.getBoundingClientRect();
            const mouseY = e.clientY;
            
            if (mouseY < rect.top + AUTO_SCROLL_MARGIN && mouseY > rect.top - 100) {
                const distance = rect.top + AUTO_SCROLL_MARGIN - mouseY;
                const speed = Math.min(AUTO_SCROLL_SPEED_FAST, AUTO_SCROLL_SPEED_SLOW + (distance / 10));
                outlinePanel.scrollTop -= speed;
            }
            else if (mouseY > rect.bottom - AUTO_SCROLL_MARGIN && mouseY < rect.bottom + 100) {
                const distance = mouseY - (rect.bottom - AUTO_SCROLL_MARGIN);
                const speed = Math.min(AUTO_SCROLL_SPEED_FAST, AUTO_SCROLL_SPEED_SLOW + (distance / 10));
                outlinePanel.scrollTop += speed;
            }
        }
    });
}

// Setup drag events for auto-scroll
function setupDragEvents(drake) {
    drake.on('drag', function() {
        isDraggingGlobal = true;
    });
    
    drake.on('dragend', function() {
        isDraggingGlobal = false;
    });
    
    drake.on('cancel', function() {
        isDraggingGlobal = false;
    });
}

// Initialize Dragula for sections
function initSectionsDragula() {
    const sectionsContainer = document.getElementById('sectionsContent');
    if (!sectionsContainer) return;
    
    // Destroy existing instance if any
    if (sectionsDrake) {
        sectionsDrake.destroy();
    }
    
    sectionsDrake = dragula([sectionsContainer], {
        moves: function(el, container, handle) {
            // Only allow dragging from the handle
            return handle.classList.contains('section-drag-handle') || 
                   handle.closest('.section-drag-handle');
        },
        accepts: function(el, target, source, sibling) {
            // Don't allow dropping on non-section elements
            return !sibling || sibling.classList.contains('outline-section') || sibling === null;
        },
        invalid: function(el, handle) {
            // Don't drag empty state or loading elements
            return el.classList.contains('empty-state') || el.classList.contains('sections-loading');
        }
    });
    
    sectionsDrake.on('drop', function(el, target, source, sibling) {
        saveSectionsOrder();
        renumberSections();
    });
    
    // Setup drag events for auto-scroll
    setupDragEvents(sectionsDrake);
}

// Initialize Dragula for lessons within a section
function initLessonsDragula(sectionId) {
    const lessonsContainer = document.getElementById('lessons-' + sectionId);
    if (!lessonsContainer) return;
    
    // Destroy existing instance if any
    if (lessonsDrakes[sectionId]) {
        lessonsDrakes[sectionId].destroy();
    }
    
    lessonsDrakes[sectionId] = dragula([lessonsContainer], {
        moves: function(el, container, handle) {
            // Don't allow dragging the add buttons
            if (el.classList.contains('add-lesson-buttons')) {
                return false;
            }
            // Only allow dragging from the handle
            return handle.classList.contains('lesson-drag-handle') || 
                   handle.closest('.lesson-drag-handle');
        },
        accepts: function(el, target, source, sibling) {
            // If dropping at the very end (sibling is null), prevent it
            // because the add-lesson-buttons should always be last
            if (sibling === null) {
                return false;
            }
            return true;
        },
        invalid: function(el, handle) {
            // Don't drag add buttons
            return el.classList.contains('add-lesson-buttons');
        }
    });
    
    // Setup drag events for auto-scroll
    setupDragEvents(lessonsDrakes[sectionId]);
    
    lessonsDrakes[sectionId].on('drop', function(el, target, source, sibling) {
        saveLessonsOrder(sectionId);
        renumberLessons(sectionId);
    });
}

// Initialize all lessons dragula
function initAllLessonsDragula() {
    document.querySelectorAll('.outline-section').forEach(function(section) {
        const sectionId = section.dataset.sectionId;
        if (sectionId) {
            initLessonsDragula(sectionId);
        }
    });
}

// Save sections order to backend
function saveSectionsOrder() {
    const container = document.getElementById('sectionsContent');
    if (!container) {
        console.error('sectionsContent container not found');
        return;
    }
    
    const sections = container.querySelectorAll('.outline-section');
    const orderArray = [];
    
    sections.forEach(function(section) {
        const sectionId = section.dataset.sectionId;
        if (sectionId) {
            orderArray.push(sectionId);
        }
    });
    
    if (orderArray.length === 0) {
        console.error('No sections found to save');
        return;
    }
    
    const csrfName = document.getElementById('csrf_name').value;
    const csrfHash = document.getElementById('csrf_hash').value;
    
    $.ajax({
        url: '<?php echo site_url('addons/courses/ajax_sort_section'); ?>',
        type: 'POST',
        data: {
            itemJSON: JSON.stringify(orderArray),
            startOrder: currentStartNumber,
            [csrfName]: csrfHash
        },
        dataType: 'json',
        success: function(response) {
            if (response.csrf) {
                document.getElementById('csrf_hash').value = response.csrf.csrfHash;
            }
            toastr.success('<?php echo addslashes(get_phrase("order_saved")); ?>');
        },
        error: function(xhr, status, error) {
            console.error('Sort error:', status, error, xhr.responseText);
            toastr.error('<?php echo addslashes(get_phrase("error_saving_order")); ?>');
        }
    });
}

// Save lessons order to backend
function saveLessonsOrder(sectionId) {
    const lessonsContainer = document.getElementById('lessons-' + sectionId);
    if (!lessonsContainer) return;
    
    const lessons = lessonsContainer.querySelectorAll('.lesson-item');
    const orderArray = [];
    
    lessons.forEach(function(lesson) {
        const lessonId = lesson.dataset.lessonId;
        if (lessonId) {
            orderArray.push(lessonId);
        }
    });
    
    if (orderArray.length === 0) return;
    
    const csrfName = document.getElementById('csrf_name').value;
    const csrfHash = document.getElementById('csrf_hash').value;
    
    const data = {
        itemJSON: JSON.stringify(orderArray)
    };
    data[csrfName] = csrfHash;
    
    $.ajax({
        url: '<?php echo site_url('addons/courses/ajax_sort_lesson'); ?>',
        type: 'POST',
        data: data,
        dataType: 'json',
        success: function(response) {
            if (response.csrf) {
                document.getElementById('csrf_hash').value = response.csrf.csrfHash;
            }
            toastr.success('<?php echo addslashes(get_phrase("order_saved")); ?>');
        },
        error: function() {
            toastr.error('<?php echo addslashes(get_phrase("error_saving_order")); ?>');
        }
    });
}

// Renumber sections after drag
function renumberSections() {
    const sections = document.querySelectorAll('.outline-section');
    sections.forEach(function(section, index) {
        const label = section.querySelector('.section-label');
        if (label) {
            // Use currentStartNumber to maintain correct numbering on paginated pages
            label.textContent = '<?php echo addslashes(get_phrase("section")); ?> ' + (currentStartNumber + index) + ':';
        }
    });
}

// Renumber lessons after drag
function renumberLessons(sectionId) {
    const lessonsContainer = document.getElementById('lessons-' + sectionId);
    if (!lessonsContainer) return;
    
    const lessons = lessonsContainer.querySelectorAll('.lesson-item');
    lessons.forEach(function(lesson, index) {
        const label = lesson.querySelector('.lesson-label');
        if (label) {
            const isQuiz = lesson.classList.contains('quiz-item');
            const prefix = isQuiz ? '<?php echo addslashes(get_phrase("quiz")); ?>' : '<?php echo addslashes(get_phrase("lesson")); ?>';
            label.textContent = prefix + ' ' + (index + 1) + ':';
        }
    });
}

// Initialize dragula on page load
document.addEventListener('DOMContentLoaded', function() {
    initGlobalAutoScroll(); // Setup auto-scroll once
    initSectionsDragula();
    initAllLessonsDragula();
    
    // Quiz dirty tracking - title and instruction
    const quizTitle = document.getElementById('quizTitle');
    const quizInstruction = document.getElementById('quizInstruction');
    if (quizTitle) {
        quizTitle.addEventListener('input', function() {
            AutoSaveManager.setQuizDirty(true);
            updateTitleCounter('quizTitle', 'quizTitleCounter');
        });
    }
    if (quizInstruction) {
        quizInstruction.addEventListener('input', function() {
            AutoSaveManager.setQuizDirty(true);
        });
    }

    // Auto-ouvrir l'aperçu du premier élément
    if (firstLessonInfo) {
        setTimeout(function() {
            if (firstLessonInfo.type === 'quiz') {
                openQuizPreview(firstLessonInfo.id, firstLessonInfo.section_id, firstLessonInfo.title);
            } else {
                openLessonPreview(firstLessonInfo.id, firstLessonInfo.section_id, firstLessonInfo.title);
            }
        }, 100);
    }
});

// Re-initialize after AJAX page load
const originalLoadPage = loadPage;
loadPage = function(page, forceReload) {
    originalLoadPage(page, forceReload);
    // Wait for content to be loaded
    setTimeout(function() {
        initSectionsDragula();
        initAllLessonsDragula();
    }, 500);
};

// Re-initialize after adding a new section via AJAX
const originalSaveNewSection = saveNewSection;
saveNewSection = function() {
    originalSaveNewSection();
    setTimeout(function() {
        initSectionsDragula();
        initAllLessonsDragula();
    }, 1000);
};

// ==========================================
// COMMON MODAL UTILITIES
// ==========================================

// Global flag to track outline generation state
let isOutlineGenerating = false;

// Common modal management functions
function showModal(modalId) {
    document.getElementById(modalId).style.display = 'flex';
}

function hideModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

function disableScroll() {
    document.body.style.overflow = 'hidden';
}

function enableScroll() {
    document.body.style.overflow = '';
}

// Common file upload handling with proper event listener management
function setupFileUpload(uploadAreaId, fileInputId, previewId, callbacks) {
    const uploadArea = document.getElementById(uploadAreaId);
    const fileInput = document.getElementById(fileInputId);
    const preview = document.getElementById(previewId);

    // Store event listeners for cleanup
    const eventListeners = {
        dragover: null,
        dragleave: null,
        drop: null,
        click: null,
        change: null
    };

    // Remove existing event listeners if they exist
    cleanupFileUploadEvents(uploadAreaId, fileInputId);

    // Drag and drop events
    eventListeners.dragover = function(e) {
        e.preventDefault();
        this.classList.add('dragover');
    };
    uploadArea.addEventListener('dragover', eventListeners.dragover);

    eventListeners.dragleave = function(e) {
        e.preventDefault();
        this.classList.remove('dragover');
    };
    uploadArea.addEventListener('dragleave', eventListeners.dragleave);

    eventListeners.drop = function(e) {
        e.preventDefault();
        this.classList.remove('dragover');

        const files = e.dataTransfer.files;
        if (files.length > 0 && files[0].type === 'application/pdf') {
            handleFileSelection(files[0], fileInput, preview, callbacks);
        } else {
            alert('<?php echo addslashes(get_phrase("please_drop_pdf_file_only")); ?>');
        }
    };
    uploadArea.addEventListener('drop', eventListeners.drop);

    // Click to browse
    eventListeners.click = function() {
        fileInput.click();
    };
    uploadArea.addEventListener('click', eventListeners.click);

    // File input change
    eventListeners.change = function(e) {
        const file = e.target.files[0];
        if (file) {
            handleFileSelection(file, fileInput, preview, callbacks);
        }
    };
    fileInput.addEventListener('change', eventListeners.change);

    // Store event listeners for later cleanup
    if (!window.fileUploadEventListeners) {
        window.fileUploadEventListeners = {};
    }
    window.fileUploadEventListeners[uploadAreaId + '_' + fileInputId] = {
        uploadArea: uploadArea,
        fileInput: fileInput,
        listeners: eventListeners
    };
}

// Clean up file upload events
function cleanupFileUploadEvents(uploadAreaId, fileInputId) {
    if (!window.fileUploadEventListeners) {
        window.fileUploadEventListeners = {};
    }
    
    const key = uploadAreaId + '_' + fileInputId;
    const stored = window.fileUploadEventListeners[key];
    
    if (stored) {
        const { uploadArea, fileInput, listeners } = stored;
        
        if (uploadArea && listeners.dragover) {
            uploadArea.removeEventListener('dragover', listeners.dragover);
        }
        if (uploadArea && listeners.dragleave) {
            uploadArea.removeEventListener('dragleave', listeners.dragleave);
        }
        if (uploadArea && listeners.drop) {
            uploadArea.removeEventListener('drop', listeners.drop);
        }
        if (uploadArea && listeners.click) {
            uploadArea.removeEventListener('click', listeners.click);
        }
        if (fileInput && listeners.change) {
            fileInput.removeEventListener('change', listeners.change);
        }
        
        delete window.fileUploadEventListeners[key];
    }
}

function handleFileSelection(file, fileInput, preview, callbacks) {
    if (file.type !== 'application/pdf') {
        alert('<?php echo addslashes(get_phrase("please_select_pdf_file")); ?>');
        return;
    }

    if (file.size > 10 * 1024 * 1024) { // 10MB
        alert('<?php echo addslashes(get_phrase("file_size_exceeds_10mb_limit")); ?>');
        return;
    }

    // Update file input
    const dt = new DataTransfer();
    dt.items.add(file);
    fileInput.files = dt.files;

    // Show preview
    displayFilePreview(file, preview);

    // Call success callback
    if (callbacks && callbacks.onFileSelected) {
        callbacks.onFileSelected(file);
    }
}

function displayFilePreview(file, previewElement) {
    const fileName = previewElement.querySelector('.ai-lesson-upload-file-name, .pdf-file-name');
    const fileSize = previewElement.querySelector('.ai-lesson-upload-file-size, .pdf-file-size');
    const uploadArea = previewElement.previousElementSibling;

    if (fileName) fileName.textContent = file.name;
    if (fileSize) fileSize.textContent = formatFileSize(file.size);

    uploadArea.style.display = 'none';
    previewElement.style.display = 'block';
}

function removeFileUpload(previewId, uploadAreaId) {
    const preview = document.getElementById(previewId);
    const uploadArea = document.getElementById(uploadAreaId);
    const fileInput = uploadArea.querySelector('input[type="file"]');

    uploadArea.style.display = 'block';
    preview.style.display = 'none';
    if (fileInput) fileInput.value = '';
}

// ==========================================
// PDF STRUCTURE EXTRACTION (Auto-extract on upload)
// ==========================================

function openPdfImportModal() {
    showModal('pdfImportModal');
    resetPdfImportModal();
    initPdfDropZoneEvents();
}

function closePdfImportModal() {
    // Block closing if generation is in progress
    if (isOutlineGenerating) {
        return;
    }
    
    hideModal('pdfImportModal');
    resetPdfImportModal();
    
    // Clean up file upload events
    cleanupFileUploadEvents('pdfDropZone', 'pdfFileInput');
    pdfEventsInitialized = false;
}

function resetPdfImportModal() {
    const pdfFileInput = document.getElementById('pdfFileInput');
    const pdfDropZone = document.getElementById('pdfDropZone');
    const pdfFileName = document.getElementById('pdfFileName');
    const pdfSuccessMessage = document.getElementById('pdfSuccessMessage');
    const pdfLoadingOverlay = document.getElementById('pdfLoadingOverlay');
    
    if (pdfFileInput) pdfFileInput.value = '';
    if (pdfDropZone) {
        pdfDropZone.classList.remove('dragover', 'has-file', 'extracting');
    }
    if (pdfFileName) pdfFileName.textContent = '';
    if (pdfSuccessMessage) pdfSuccessMessage.style.display = 'none';
    if (pdfLoadingOverlay) pdfLoadingOverlay.style.display = 'none';
}

let pdfEventsInitialized = false;

function initPdfDropZoneEvents() {
    // Clean up any existing events first
    cleanupFileUploadEvents('pdfDropZone', 'pdfFileInput');
    
    setupFileUpload('pdfDropZone', 'pdfFileInput', 'pdfUploadPreview', {
        onFileSelected: function(file) {
            extractPdfStructure(file);
        }
    });

    // Modal close handler (blocked during generation)
    document.getElementById('pdfImportModal').addEventListener('click', function(e) {
        if (e.target === this && !isOutlineGenerating) {
            closePdfImportModal();
        }
    });

    pdfEventsInitialized = true;
}

// Outline Rules - Difficulty Badge Selection
document.addEventListener('DOMContentLoaded', function() {
    const difficultyBadges = document.querySelectorAll('.outline-difficulty-badges .outline-badge');
    difficultyBadges.forEach(badge => {
        badge.addEventListener('click', function() {
            difficultyBadges.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
        });
    });
});

// Store current PDF JSON path
let currentPdfJsonPath = '';
let selectedSchemaIndex = null;
let generatedSchemas = null;

// Function to toggle quiz-related fields visibility
function toggleQuizFields() {
    const quizFrequency = document.getElementById('outlineQuizFrequency').value;
    const quizFields = document.querySelectorAll('.quiz-related-field');

    if (quizFrequency === 'none') {
        quizFields.forEach(field => {
            field.style.display = 'none';
        });
    } else {
        quizFields.forEach(field => {
            field.style.display = 'block';
        });
    }
}

function getOutlineRulesData() {
    return {
        audience: document.getElementById('outlineAudience').value,
        duration: document.getElementById('outlineDuration').value,
        language: document.getElementById('outlineLanguage').value,
        tone: document.getElementById('outlineTone').value,
        includeIntro: document.getElementById('outlineIncludeIntro').checked,
        includeConclusion: document.getElementById('outlineIncludeConclusion').checked,
        maxSections: document.getElementById('outlineMaxSections').value,
        lessonsPerSection: document.getElementById('outlineLessonsPerSection').value,
        lessonDuration: document.getElementById('outlineLessonDuration').value,
        quizFrequency: document.getElementById('outlineQuizFrequency').value,
        questionsCount: document.getElementById('outlineQuestionsCount').value,
        difficulty: document.querySelector('.outline-badge.active')?.dataset.difficulty || 'easy',
        numbering: document.getElementById('outlineNumbering').value
    };
}

function generateOutlineSchemas() {
    // Check if PDF has been uploaded
    if (!currentPdfJsonPath) {
        return;
    }
    
    // Set generation flag to block modal closing
    isOutlineGenerating = true;
    
    // Hide close button during generation
    document.getElementById('pdfModalCloseBtn').style.display = 'none';
    
    const outlineData = getOutlineRulesData();
    
    // Get course context data (prerequisites, field_of_activity, course_style)
    const courseId = <?php echo $param1 ?? 0; ?>;
    
    // Show schema selection view with loading
    document.querySelector('.outline-rules-body').style.display = 'none';
    document.getElementById('outlineRulesFooter').style.display = 'none';
    const schemaSelectionEl = document.getElementById('outlineSchemaSelection');
    schemaSelectionEl.style.display = 'block';
    schemaSelectionEl.classList.add('is-loading'); // Add loading class to hide content
    document.getElementById('outlineSchemaFooter').style.display = 'none'; // Hide footer during generation
    showModal('schemaLoadingOverlay');
    
    // Call DeepSeek API
    const csrfName = document.getElementById('csrf_name')?.value || '<?php echo $this->security->get_csrf_token_name(); ?>';
    const csrfHash = document.getElementById('csrf_hash')?.value || '<?php echo $this->security->get_csrf_hash(); ?>';
    
    const formData = new FormData();
    formData.append('course_id', courseId);
    formData.append('pdf_json_path', currentPdfJsonPath);
    formData.append('outline_rules', JSON.stringify(outlineData));
    formData.append(csrfName, csrfHash);
    
    fetch('<?php echo site_url('addons/courses/generate_outline_schemas'); ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        // Reset generation flag - schemas generated, user can close modal
        isOutlineGenerating = false;
        
        // Show close button again
        document.getElementById('pdfModalCloseBtn').style.display = '';
        
        hideModal('schemaLoadingOverlay');
        document.getElementById('outlineSchemaSelection').classList.remove('is-loading'); // Remove loading class
        // Always show footer after loading completes
        document.getElementById('outlineSchemaFooter').style.display = 'flex';
        
        if (data.error) {
            console.error('Error:', data.error);
            backToOutlineRules();
            return;
        }
        
        if (data.schemas) {
            generatedSchemas = data.schemas;
            populateSchemaCards(data.schemas);
        }
    })
    .catch(error => {
        // Reset generation flag on error
        isOutlineGenerating = false;
        
        // Show close button again
        document.getElementById('pdfModalCloseBtn').style.display = '';
        
        hideModal('schemaLoadingOverlay');
        document.getElementById('outlineSchemaSelection').classList.remove('is-loading'); // Remove loading class
        console.error('Failed to generate schemas:', error.message);
        backToOutlineRules();
    });
}

// Translation constants for schema stats
const SCHEMA_TRANSLATIONS = {
    sections: '<?php echo get_phrase("sections"); ?>',
    lessons: '<?php echo get_phrase("lessons"); ?>',
    quiz: '<?php echo get_phrase("quiz"); ?>'
};

function populateSchemaCards(schemas) {
    if (!schemas || !Array.isArray(schemas)) {
        console.warn('Invalid schemas data:', schemas);
        return;
    }

    const quizFrequency = document.getElementById('outlineQuizFrequency')?.value || 'per_section';

    schemas.forEach((schema, index) => {
        const num = index + 1;
        const card = document.querySelector(`.schema-card[data-schema="${num}"]`);

        // Only proceed if the card exists
        if (!card) {
            console.warn(`Schema card ${num} not found in DOM`);
            return;
        }
        
        // Update card title
        const titleEl = document.getElementById('schemaTitle' + num);
        if (titleEl) {
            titleEl.textContent = schema.label || `<?php echo get_phrase("design_schema"); ?> ${num}`;
        }

        // Update description with professional text
        const descEl = document.getElementById('schemaDesc' + num);
        if (descEl) {
            const description = schema.description || '';
            descEl.textContent = description.charAt(0).toUpperCase() + description.slice(1);
        }
        
        // Update sections list
        const sectionsEl = document.getElementById('schemaSections' + num);
        if (sectionsEl && schema.sections && Array.isArray(schema.sections)) {
            sectionsEl.innerHTML = schema.sections.map((s, i) => 
                `<li>${s || ''}</li>`
            ).join('');
        }
        
        // Update individual stat values
        const sectionCountEl = document.getElementById('schemaSectionCount' + num);
        const lessonCountEl = document.getElementById('schemaLessonCount' + num);
        const quizCountEl = document.getElementById('schemaQuizCount' + num);
        const quizStatEl = document.getElementById('schemaQuizStat' + num);

        if (sectionCountEl) {
            sectionCountEl.textContent = (typeof schema.total_sections === 'number') ? schema.total_sections : '--';
        }
        if (lessonCountEl) {
            lessonCountEl.textContent = (typeof schema.total_lessons === 'number') ? schema.total_lessons : '--';
        }
        if (quizCountEl) {
            quizCountEl.textContent = (typeof schema.total_quizzes === 'number') ? schema.total_quizzes : '--';
        }
        
        // Hide quiz stat if no quiz selected
        if (quizStatEl) {
            quizStatEl.style.display = (quizFrequency === 'none') ? 'none' : 'flex';
        }

        // Update details with improved formatting
        const detailsEl = document.getElementById('schemaDetails' + num);
        if (detailsEl && schema.details && Array.isArray(schema.details)) {
            detailsEl.innerHTML = schema.details.map(d => 
                `<div class="schema-detail-item">
                    <span>${d.section || ''}</span>
                    <span class="schema-detail-lessons"><i class="fas fa-book-open"></i> ${d.lessons || '0 lessons'}</span>
                </div>`
            ).join('');
        }
    });
}

function toggleSchemaDetails(schemaNum) {
    const detailsEl = document.getElementById('schemaDetails' + schemaNum);
    const btnEl = document.querySelector(`.schema-card[data-schema="${schemaNum}"] .schema-view-detail`);
    
    if (detailsEl) {
        detailsEl.classList.toggle('show');
        btnEl?.classList.toggle('expanded');
    }
}

function selectSchema(schemaNum) {
    // Remove previous selection
    document.querySelectorAll('.schema-card').forEach(card => {
        card.classList.remove('selected');
    });
    
    // Select new card
    const card = document.querySelector(`.schema-card[data-schema="${schemaNum}"]`);
    if (card) {
        card.classList.add('selected');
        selectedSchemaIndex = schemaNum - 1;
        document.getElementById('btnContinue').disabled = false;
    }
}

function backToOutlineRules() {
    // Block going back during generation
    if (isOutlineGenerating) {
        return;
    }
    
    document.querySelector('.outline-rules-body').style.display = 'block';
    document.getElementById('outlineRulesFooter').style.display = 'flex';
    document.getElementById('outlineSchemaSelection').style.display = 'none';
    document.getElementById('outlineSchemaFooter').style.display = 'none';
    selectedSchemaIndex = null;
    document.getElementById('btnContinue').disabled = true;
}

function continueWithSelectedSchema() {
    if (selectedSchemaIndex === null || !generatedSchemas) {
        return;
    }
    
    const selectedSchema = generatedSchemas[selectedSchemaIndex];
    const courseId = <?php echo $course['id']; ?>;
    
    if (!courseId) {
        return;
    }
    
    // Set generation flag to block modal closing
    isOutlineGenerating = true;
    
    // Hide close button during generation
    document.getElementById('pdfModalCloseBtn').style.display = 'none';
    
    // Add loading class to hide content and show overlay
    document.getElementById('outlineSchemaSelection').classList.add('is-applying');
    showModal('schemaApplyingOverlay');

    // Disable all schema cards during generation
    const schemaContainer = document.querySelector('.schema-cards-container');
    schemaContainer.classList.add('generating');

    // Hide footer buttons during generation
    document.getElementById('outlineSchemaFooter').style.display = 'none';

    // Show generating indicator on selected card
    const selectedCard = document.querySelector(`.schema-card[data-schema="${selectedSchemaIndex + 1}"]`);
    if (selectedCard) {
        selectedCard.classList.add('generating');
    }
    
    // Get CSRF token
    const csrfName = document.getElementById('csrf_name')?.value || '<?php echo $this->security->get_csrf_token_name(); ?>';
    const csrfHash = document.getElementById('csrf_hash')?.value || '<?php echo $this->security->get_csrf_hash(); ?>';
    
    // Get outline rules for content generation settings
    const outlineRules = getOutlineRulesData();
    
    const formData = new FormData();
    formData.append('course_id', courseId);
    formData.append('schema', JSON.stringify(selectedSchema));
    formData.append('outline_rules', JSON.stringify(outlineRules));
    formData.append(csrfName, csrfHash);
    
    fetch('<?php echo site_url('addons/courses/apply_outline_schema'); ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        // Reset generation flag
        isOutlineGenerating = false;
        
        // Show close button again
        document.getElementById('pdfModalCloseBtn').style.display = '';
        
        // Remove loading class and hide overlay
        document.getElementById('outlineSchemaSelection').classList.remove('is-applying');
        hideModal('schemaApplyingOverlay');

        // Re-enable schema cards
        schemaContainer.classList.remove('generating');
        if (selectedCard) {
            selectedCard.classList.remove('generating');
        }
        document.getElementById('outlineSchemaFooter').style.display = 'flex';
        
        if (data.error) {
            console.error('Error:', data.error);
            return;
        }
        
        if (data.success) {
            closePdfImportModal();
            // Reload the curriculum page to show new sections
            location.reload();
        }
    })
    .catch(error => {
        // Reset generation flag on error
        isOutlineGenerating = false;
        
        // Show close button again
        document.getElementById('pdfModalCloseBtn').style.display = '';
        
        // Remove loading class and hide overlay
        document.getElementById('outlineSchemaSelection').classList.remove('is-applying');
        hideModal('schemaApplyingOverlay');

        // Re-enable schema cards on error
        const schemaContainer = document.querySelector('.schema-cards-container');
        schemaContainer.classList.remove('generating');
        const selectedCard = document.querySelector(`.schema-card[data-schema="${selectedSchemaIndex + 1}"]`);
        if (selectedCard) {
            selectedCard.classList.remove('generating');
        }
        document.getElementById('outlineSchemaFooter').style.display = 'flex';
        console.error('Failed to generate course structure:', error.message);
    });
}

// Legacy function - now redirects to generateOutlineSchemas
function saveOutlineRules() {
    generateOutlineSchemas();
}

function extractPdfStructure(file) {
    if (!file || file.type !== 'application/pdf') {
        return;
    }
    
    // Show file name and loading
    const pdfDropZone = document.getElementById('pdfDropZone');
    const pdfFileName = document.getElementById('pdfFileName');
    const pdfLoadingOverlay = document.getElementById('pdfLoadingOverlay');
    
    if (pdfDropZone) pdfDropZone.classList.add('has-file', 'extracting');
    if (pdfFileName) pdfFileName.textContent = file.name;
    if (pdfLoadingOverlay) pdfLoadingOverlay.style.display = 'flex';
    
    const formData = new FormData();
    formData.append('pdf_file', file);
    formData.append('<?php echo $this->security->get_csrf_token_name(); ?>', '<?php echo $this->security->get_csrf_hash(); ?>');
    
    fetch('<?php echo site_url('addons/courses/extract_pdf_structure'); ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        const loadingOverlay = document.getElementById('pdfLoadingOverlay');
        const dropZone = document.getElementById('pdfDropZone');
        const successMessage = document.getElementById('pdfSuccessMessage');
        
        if (loadingOverlay) loadingOverlay.style.display = 'none';
        
        if (data.error) {
            if (dropZone) dropZone.classList.remove('extracting');
            console.error('Error:', data.error);
            return;
        }
        
        if (data.success) {
            // Store JSON path for later use
            currentPdfJsonPath = data.json_path;
            
            // Update CSRF token if provided
            if (data.csrf_hash) {
                const csrfInput = document.getElementById('csrf_hash');
                if (csrfInput) csrfInput.value = data.csrf_hash;
            }
            
            // Hide drop zone, show success
            if (dropZone) dropZone.style.display = 'none';
            if (successMessage) successMessage.style.display = 'flex';
        }
    })
    .catch(error => {
        const loadingOverlay = document.getElementById('pdfLoadingOverlay');
        const dropZone = document.getElementById('pdfDropZone');
        
        if (loadingOverlay) loadingOverlay.style.display = 'none';
        if (dropZone) dropZone.classList.remove('extracting');
        console.error('Extraction failed:', error.message);
    });
}
</script>
<!-- PDF Structure Extraction Modal -->
<div class="pdf-import-modal" id="pdfImportModal" style="display: none;">
    <div class="pdf-import-modal-content outline-rules-modal">
        <div class="pdf-import-modal-header">
            <div class="outline-rules-title">
                <h3><?php echo get_phrase("outline_rules"); ?></h3>
                <p class="outline-rules-subtitle"><?php echo get_phrase("define_rules_to_generate_course_outline"); ?></p>
            </div>
            <button type="button" class="pdf-modal-close" id="pdfModalCloseBtn" onclick="closePdfImportModal()">
                <i class="fas fa-xmark"></i>
            </button>
        </div>
        
        <div class="pdf-import-modal-body outline-rules-body">
            <!-- Loading Overlay -->
            <div class="pdf-loading-overlay" id="pdfLoadingOverlay" style="display: none;">
                <div class="pdf-loading-spinner">
                    <i class="fas fa-spinner fa-spin"></i>
                    <span><?php echo get_phrase("analyzing_pdf"); ?>...</span>
                </div>
            </div>
            
            <!-- PDF Upload Zone -->
            <div class="outline-pdf-upload-section">
                <div class="pdf-drop-zone" id="pdfDropZone">
                    <input type="file" id="pdfFileInput" accept=".pdf" style="display: none;">
                    <i class="fas fa-cloud-upload-alt"></i>
                    <p><?php echo get_phrase("drag_drop_pdf_or_click"); ?></p>
                    <span class="pdf-file-name" id="pdfFileName"></span>
                </div>
            </div>
            
            <div class="outline-rules-grid">
                <!-- Left Column: Pedagogical Context -->
                <div class="outline-rules-section">
                    <h4 class="outline-section-title"><i class="fas fa-graduation-cap"></i> <?php echo get_phrase("pedagogical_context"); ?></h4>
                    
                    <div class="outline-form-group">
                        <label><?php echo get_phrase("audience"); ?></label>
                        <select class="outline-select" id="outlineAudience">
                            <option value="beginner"><?php echo get_phrase("beginner"); ?></option>
                            <option value="intermediate" selected><?php echo get_phrase("intermediate"); ?></option>
                            <option value="advanced"><?php echo get_phrase("advanced"); ?></option>
                            <option value="expert"><?php echo get_phrase("expert"); ?></option>
                        </select>
                    </div>
                    
                    <div class="outline-form-group">
                        <label><?php echo get_phrase("duration"); ?></label>
                        <select class="outline-select" id="outlineDuration">
                            <option value="1-2">1 – 2 <?php echo get_phrase("hours"); ?></option>
                            <option value="3-5" selected>3 – 5 <?php echo get_phrase("hours"); ?></option>
                            <option value="5-10">5 – 10 <?php echo get_phrase("hours"); ?></option>
                            <option value="10-20">10 – 20 <?php echo get_phrase("hours"); ?></option>
                            <option value="20-40">20 – 40 <?php echo get_phrase("hours"); ?></option>
                        </select>
                    </div>
                    
                    <div class="outline-form-group outline-form-row">
                        <label><?php echo get_phrase("language"); ?></label>
                        <select class="outline-select outline-select-compact" id="outlineLanguage">
                            <option value="french" selected><?php echo get_phrase("french"); ?></option>
                            <option value="english"><?php echo get_phrase("english"); ?></option>
                            <option value="spanish"><?php echo get_phrase("spanish"); ?></option>
                            <option value="dutch"><?php echo get_phrase("dutch"); ?></option>
                            <option value="arabic"><?php echo get_phrase("arabic"); ?></option>
                        </select>
                    </div>
                    
                    <div class="outline-form-group outline-form-row">
                        <label><?php echo get_phrase("tone"); ?></label>
                        <select class="outline-select outline-select-compact" id="outlineTone">
                            <option value="very_concise">Very concise (bullet points)</option>
                            <option value="concise_technical" selected>Concise & technical</option>
                            <option value="neutral_professional">Neutral & professional</option>
                            <option value="pedagogical_progressive">Pedagogical & progressive</option>
                            <option value="coach_motivating">Coach / motivating</option>
                            <option value="conversational">Conversational (friendly)</option>
                            <option value="corporate_institutional">Corporate / institutional</option>
                            <option value="expert_best_practices">Expert / best practices</option>
                            <option value="storytelling">Storytelling</option>
                            <option value="action_oriented">Action oriented (checklists)</option>
                            <option value="compliance_oriented">Compliance oriented (rigorous)</option>
                            <option value="support_troubleshooting">Support / troubleshooting</option>
                        </select>
                    </div>
                    
                    <div class="outline-toggles">
                        <div class="outline-toggle-item">
                            <span><?php echo get_phrase("include_introduction_section"); ?></span>
                            <label class="outline-toggle">
                                <input type="checkbox" id="outlineIncludeIntro" checked>
                                <span class="outline-toggle-slider"></span>
                            </label>
                        </div>
                        
                        <div class="outline-toggle-item">
                            <span><?php echo get_phrase("include_conclusion_section"); ?></span>
                            <label class="outline-toggle">
                                <input type="checkbox" id="outlineIncludeConclusion" checked>
                                <span class="outline-toggle-slider"></span>
                            </label>
                        </div>
                    </div>
                </div>
                
                <!-- Right Column: Structure Rules -->
                <div class="outline-rules-section">
                    <h4 class="outline-section-title"><i class="fas fa-sitemap"></i> <?php echo get_phrase("structure_rules"); ?></h4>
                    
                    <div class="outline-form-group outline-form-row">
                        <label><?php echo get_phrase("max_sections"); ?></label>
                        <select class="outline-select outline-select-small" id="outlineMaxSections">
                            <option value="1-3">1 – 3</option>
                            <option value="3-6" selected>3 – 6</option>
                            <option value="6-9">6 – 9</option>
                            <option value="9-12">9 – 12</option>
                            <option value="12-15">12 – 15</option>
                        </select>
                    </div>
                    
                    <div class="outline-form-group outline-form-row">
                        <label><?php echo get_phrase("lessons_per_section"); ?></label>
                        <select class="outline-select outline-select-small" id="outlineLessonsPerSection">
                            <option value="1-2">1 – 2</option>
                            <option value="2-3" selected>2 – 3</option>
                            <option value="3-4">3 – 4</option>
                            <option value="4-5">4 – 5</option>
                            <option value="5-6">5 – 6</option>
                        </select>
                    </div>
                    
                    <div class="outline-form-group outline-form-row">
                        <label><?php echo get_phrase("lesson_duration"); ?></label>
                        <select class="outline-select outline-select-small" id="outlineLessonDuration">
                            <option value="5-10"><?php echo get_phrase("5_–_10_min"); ?></option>
                            <option value="10-15" selected><?php echo get_phrase("10_–_15_min"); ?></option>
                            <option value="15-20"><?php echo get_phrase("15_–_20_min"); ?></option>
                            <option value="20-30"><?php echo get_phrase("20_–_30_min"); ?></option>
                            <option value="30-45"><?php echo get_phrase("30_–_45_min"); ?></option>
                        </select>
                    </div>
                    
                    <div class="outline-form-group">
                        <label><?php echo get_phrase("quiz_frequency"); ?></label>
                        <select class="outline-select" id="outlineQuizFrequency" onchange="toggleQuizFields()">
                            <option value="none"><?php echo get_phrase("no_quiz"); ?></option>
                            <option value="per_section" selected><?php echo get_phrase("one_quiz_per_section"); ?></option>
                            <option value="per_lesson"><?php echo get_phrase("one_quiz_per_lesson"); ?></option>
                            <option value="final"><?php echo get_phrase("one_final_quiz"); ?></option>
                        </select>
                    </div>
                    
                    <div class="outline-form-group quiz-related-field">
                        <label><?php echo get_phrase("questions_per_quiz"); ?></label>
                        <div class="outline-questions-row">
                            <input type="number" class="outline-input-small" id="outlineQuestionsCount" value="10" min="1" max="50">
                            <div class="outline-difficulty-badges">
                                <button type="button" class="outline-badge outline-badge-easy" data-difficulty="easy">
                                    <i class="fas fa-leaf"></i> <?php echo get_phrase("easy"); ?>
                                </button>
                                <button type="button" class="outline-badge outline-badge-medium active" data-difficulty="medium">
                                    <i class="fas fa-balance-scale"></i> <?php echo get_phrase("medium"); ?>
                                </button>
                                <button type="button" class="outline-badge outline-badge-hard" data-difficulty="hard">
                                    <i class="fas fa-fire"></i> <?php echo get_phrase("hard"); ?>
                                </button>
                            </div>
                        </div>
                        <p class="outline-hint"><i class="fas fa-info-circle"></i> <?php echo get_phrase("quiz_will_be_created_automatically"); ?></p>
                    </div>
                    
                    <div class="outline-form-group">
                        <label><?php echo get_phrase("numbering"); ?></label>
                        <select class="outline-select" id="outlineNumbering">
                            <option value="none"><?php echo get_phrase("none"); ?></option>
                            <option value="auto_123"><?php echo get_phrase("automatic_1_2_3"); ?></option>
                            <option value="auto_nested"><?php echo get_phrase("automatic_1_1_1_2"); ?></option>
                            <option value="from_pdf"><?php echo get_phrase("from_pdf"); ?></option>
                        </select>
                    </div>
                </div>
            </div>
            
            <!-- Success Message -->
            <div class="pdf-success-message" id="pdfSuccessMessage" style="display: none;">
                <i class="fas fa-check-circle"></i>
                <span><?php echo get_phrase("pdf_upload_success"); ?></span>
            </div>
        </div>
        
        <!-- Step 2: Schema Selection View -->
        <div class="outline-schema-selection" id="outlineSchemaSelection" style="display: none;">
            <!-- Loading Overlay for AI Generation -->
            <div class="schema-loading-overlay" id="schemaLoadingOverlay" style="display: none;">
                <div class="schema-loading-content">
                    <div class="schema-loading-spinner">
                        <i class="fas fa-wand-magic-sparkles"></i>
                    </div>
                    <span><?php echo get_phrase("generating_course_outline"); ?></span>
                    <small><?php echo get_phrase("this_may_take_a_few_moments"); ?></small>
                </div>
            </div>

            <!-- Loading Overlay for Schema Application -->
            <div class="schema-applying-overlay" id="schemaApplyingOverlay" style="display: none;">
                <div class="schema-applying-content">
                    <div class="schema-applying-spinner">
                        <i class="fas fa-cog fa-spin"></i>
                    </div>
                    <span><?php echo get_phrase("applying_course_structure"); ?></span>
                    <small><?php echo get_phrase("this_may_take_a_few_moments"); ?></small>
                </div>
            </div>
            
            <div class="schema-selection-header">
                <h3><?php echo get_phrase("choose_your_course_structure"); ?></h3>
                <p><?php echo get_phrase("select_one_of_the_proposals_below"); ?></p>
            </div>
            
            <div class="schema-cards-container">
                <!-- Schema Card 1 - Balanced -->
                <div class="schema-card" data-schema="1" onclick="selectSchema(1)">
                    <div class="schema-selected-icon"><i class="fas fa-check"></i></div>
                    <div class="schema-generating-spinner"></div>
                    <div class="schema-card-header">
                    <div class="schema-card-badge schema-badge-balanced">
                            <i class="fas fa-balance-scale"></i> <?php echo get_phrase("balanced"); ?>
                    </div>
                        <h4 class="schema-card-title" id="schemaTitle1"><?php echo get_phrase("design_schema"); ?> 1</h4>
                        <p class="schema-card-description" id="schemaDesc1"><?php echo get_phrase("loading"); ?>...</p>
                    </div>
                    
                    <div class="schema-card-body">
                        <ul class="schema-sections-list" id="schemaSections1">
                            <li><?php echo get_phrase("loading"); ?>...</li>
                        </ul>
                    </div>
                    
                    <div class="schema-card-stats" id="schemaStats1">
                        <div class="schema-stat-item">
                            <span class="schema-stat-value" id="schemaSectionCount1">--</span>
                            <span class="schema-stat-label"><?php echo get_phrase("sections"); ?></span>
                        </div>
                        <div class="schema-stat-item">
                            <span class="schema-stat-value" id="schemaLessonCount1">--</span>
                            <span class="schema-stat-label"><?php echo get_phrase("lessons"); ?></span>
                        </div>
                        <div class="schema-stat-item schema-stat-quiz" id="schemaQuizStat1">
                            <span class="schema-stat-value" id="schemaQuizCount1">--</span>
                            <span class="schema-stat-label"><?php echo get_phrase("quizzes"); ?></span>
                        </div>
                    </div>
                    
                    <div class="schema-card-details" id="schemaDetails1">
                        <!-- Details will be populated by JS -->
                    </div>
                    
                    <div class="schema-card-footer">
                        <button type="button" class="schema-view-detail" onclick="event.stopPropagation(); toggleSchemaDetails(1)">
                            <i class="fas fa-list-ul"></i> <?php echo get_phrase("view_details"); ?> <i class="fas fa-chevron-right"></i>
                    </button>
                        <button type="button" class="schema-select-btn" onclick="event.stopPropagation(); selectSchema(1)">
                            <i class="fas fa-check"></i> <?php echo get_phrase("select_this_plan"); ?>
                    </button>
                    </div>
                </div>
                
                <!-- Schema Card 2 - Consolidated -->
                <div class="schema-card" data-schema="2" onclick="selectSchema(2)">
                    <div class="schema-selected-icon"><i class="fas fa-check"></i></div>
                    <div class="schema-generating-spinner"></div>
                    <div class="schema-card-header">
                    <div class="schema-card-badge schema-badge-consolidated">
                            <i class="fas fa-layer-group"></i> <?php echo get_phrase("consolidated"); ?>
                    </div>
                        <h4 class="schema-card-title" id="schemaTitle2"><?php echo get_phrase("design_schema"); ?> 2</h4>
                        <p class="schema-card-description" id="schemaDesc2"><?php echo get_phrase("loading"); ?>...</p>
                    </div>
                    
                    <div class="schema-card-body">
                        <ul class="schema-sections-list" id="schemaSections2">
                            <li><?php echo get_phrase("loading"); ?>...</li>
                        </ul>
                    </div>
                    
                    <div class="schema-card-stats" id="schemaStats2">
                        <div class="schema-stat-item">
                            <span class="schema-stat-value" id="schemaSectionCount2">--</span>
                            <span class="schema-stat-label"><?php echo get_phrase("sections"); ?></span>
                        </div>
                        <div class="schema-stat-item">
                            <span class="schema-stat-value" id="schemaLessonCount2">--</span>
                            <span class="schema-stat-label"><?php echo get_phrase("lessons"); ?></span>
                        </div>
                        <div class="schema-stat-item schema-stat-quiz" id="schemaQuizStat2">
                            <span class="schema-stat-value" id="schemaQuizCount2">--</span>
                            <span class="schema-stat-label"><?php echo get_phrase("quizzes"); ?></span>
                        </div>
                    </div>
                    
                    <div class="schema-card-details" id="schemaDetails2">
                        <!-- Details will be populated by JS -->
                    </div>
                    
                    <div class="schema-card-footer">
                        <button type="button" class="schema-view-detail" onclick="event.stopPropagation(); toggleSchemaDetails(2)">
                            <i class="fas fa-list-ul"></i> <?php echo get_phrase("view_details"); ?> <i class="fas fa-chevron-right"></i>
                    </button>
                        <button type="button" class="schema-select-btn" onclick="event.stopPropagation(); selectSchema(2)">
                            <i class="fas fa-check"></i> <?php echo get_phrase("select_this_plan"); ?>
                    </button>
                    </div>
                </div>
                
                <!-- Schema Card 3 - Progressive Learning -->
                <div class="schema-card" data-schema="3" onclick="selectSchema(3)">
                    <div class="schema-selected-icon"><i class="fas fa-check"></i></div>
                    <div class="schema-generating-spinner"></div>
                    <div class="schema-card-header">
                    <div class="schema-card-badge schema-badge-progressive">
                            <i class="fas fa-stairs"></i> <?php echo get_phrase("progressive"); ?>
                    </div>
                        <h4 class="schema-card-title" id="schemaTitle3"><?php echo get_phrase("design_schema"); ?> 3</h4>
                        <p class="schema-card-description" id="schemaDesc3"><?php echo get_phrase("loading"); ?>...</p>
                    </div>
                    
                    <div class="schema-card-body">
                        <ul class="schema-sections-list" id="schemaSections3">
                            <li><?php echo get_phrase("loading"); ?>...</li>
                        </ul>
                    </div>
                    
                    <div class="schema-card-stats" id="schemaStats3">
                        <div class="schema-stat-item">
                            <span class="schema-stat-value" id="schemaSectionCount3">--</span>
                            <span class="schema-stat-label"><?php echo get_phrase("sections"); ?></span>
                        </div>
                        <div class="schema-stat-item">
                            <span class="schema-stat-value" id="schemaLessonCount3">--</span>
                            <span class="schema-stat-label"><?php echo get_phrase("lessons"); ?></span>
                        </div>
                        <div class="schema-stat-item schema-stat-quiz" id="schemaQuizStat3">
                            <span class="schema-stat-value" id="schemaQuizCount3">--</span>
                            <span class="schema-stat-label"><?php echo get_phrase("quizzes"); ?></span>
                        </div>
                    </div>
                    
                    <div class="schema-card-details" id="schemaDetails3">
                        <!-- Details will be populated by JS -->
                    </div>
                    
                    <div class="schema-card-footer">
                        <button type="button" class="schema-view-detail" onclick="event.stopPropagation(); toggleSchemaDetails(3)">
                            <i class="fas fa-list-ul"></i> <?php echo get_phrase("view_details"); ?> <i class="fas fa-chevron-right"></i>
                    </button>
                        <button type="button" class="schema-select-btn" onclick="event.stopPropagation(); selectSchema(3)">
                            <i class="fas fa-check"></i> <?php echo get_phrase("select_this_plan"); ?>
                    </button>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="pdf-import-modal-footer outline-rules-footer" id="outlineRulesFooter">
            <button type="button" class="btn-cancel-pdf" onclick="closePdfImportModal()">
                <?php echo get_phrase("cancel"); ?>
            </button>
            <button type="button" class="btn-save-outline" id="btnSaveOutline" onclick="generateOutlineSchemas()">
                <?php echo get_phrase("save"); ?>
            </button>
        </div>
        
        <div class="pdf-import-modal-footer outline-schema-footer" id="outlineSchemaFooter" style="display: none;">
            <button type="button" class="btn-cancel-pdf" onclick="backToOutlineRules()">
                <?php echo get_phrase("cancel"); ?>
            </button>
            <button type="button" class="btn-save-outline" id="btnContinue" onclick="continueWithSelectedSchema()" disabled>
                Continue
            </button>
        </div>
    </div>
</div>

<!-- AI Quiz Generation Modal -->
<div class="ai-quiz-modal" id="aiQuizModal" style="display: none;">
    <div class="ai-quiz-modal-content">
        <div class="ai-quiz-modal-header">
            <div>
                <h5 class="ai-quiz-modal-title">
                    <i class="fas fa-wand-magic-sparkles"></i>
                    <?php echo get_phrase("generate_quiz_with_ai"); ?>
                </h5>
                <p class="ai-quiz-modal-subtitle">
                    <?php echo get_phrase("select_lessons_to_generate_questions"); ?>
                </p>
            </div>
            <button type="button" class="ai-quiz-modal-close" onclick="closeAiQuizModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="ai-quiz-modal-body">
            <div class="ai-quiz-grid">
                <!-- Left Panel - Info -->
                <div class="ai-quiz-info-panel">
                    <div class="ai-quiz-info-badge">
                        <i class="fas fa-wand-magic-sparkles"></i> <?php echo get_phrase("ai_powered"); ?>
                    </div>
                    <h4><?php echo get_phrase("create_quizzes_from_lessons"); ?></h4>
                    <p><?php echo get_phrase("wayo_ai_lesson_quiz_description"); ?></p>
                    <ul class="ai-quiz-info-list">
                        <li><i class="fas fa-check-circle"></i> <?php echo get_phrase("based_on_lesson_content"); ?></li>
                        <li><i class="fas fa-check-circle"></i> <?php echo get_phrase("balanced_choices_description"); ?></li>
                        <li><i class="fas fa-check-circle"></i> <?php echo get_phrase("auto_correct_answers"); ?></li>
                    </ul>
                    <div class="ai-quiz-info-tip">
                        <i class="fas fa-lightbulb"></i>
                        <div>
                            <strong><?php echo get_phrase("pro_tip"); ?>:</strong>
                            <?php echo get_phrase("select_lessons_with_rich_content"); ?>
                        </div>
                    </div>
                </div>
                
                <!-- Right Panel - Form -->
                <div class="ai-quiz-form-panel">
                    <!-- Quiz Title -->
                    <div class="ai-quiz-section">
                        <label class="ai-quiz-section-label">
                            <i class="fas fa-heading"></i>
                            <?php echo get_phrase("quiz_title"); ?>
                        </label>
                        <input type="text" class="ai-quiz-input" id="aiQuizTitle" placeholder="<?php echo get_phrase("enter_quiz_title"); ?>" maxlength="100">
                    </div>
                    
                    <!-- Lesson Selection -->
                    <div class="ai-quiz-section">
                        <label class="ai-quiz-section-label">
                            <i class="fas fa-book-open text-primary"></i>
                            <?php echo get_phrase("select_lessons_for_quiz"); ?>
                        </label>
                        <p class="ai-quiz-section-hint"><?php echo get_phrase("select_lessons_hint"); ?></p>
                        
                        <div class="ai-quiz-lessons-selector" id="aiQuizLessonsSelector">
                            <div class="ai-quiz-lessons-loading" id="aiQuizLessonsLoading">
                                <i class="fas fa-spinner fa-spin"></i>
                                <span><?php echo get_phrase("loading_lessons"); ?>...</span>
                            </div>
                            <div class="ai-quiz-lessons-empty" id="aiQuizLessonsEmpty" style="display: none;">
                                <i class="fas fa-exclamation-circle"></i>
                                <span><?php echo get_phrase("no_lessons_available"); ?></span>
                            </div>
                            <div class="ai-quiz-lessons-list" id="aiQuizLessonsList" style="display: none;">
                                <!-- Lessons will be loaded here dynamically -->
                            </div>
                        </div>
                        
                        <div class="ai-quiz-selection-info" id="aiQuizSelectionInfo" style="display: none;">
                            <i class="fas fa-check-circle"></i>
                            <span><strong id="aiQuizSelectedCount">0</strong> <?php echo get_phrase("lessons_selected"); ?></span>
                            <button type="button" class="btn-clear-selection" onclick="clearLessonSelection()">
                                <i class="fas fa-times"></i> <?php echo get_phrase("clear"); ?>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Number of Questions -->
                    <div class="ai-quiz-section">
                        <label class="ai-quiz-section-label">
                            <i class="fas fa-list-ol"></i>
                            <?php echo get_phrase("number_of_questions"); ?>
                        </label>
                        <div class="ai-quiz-slider-container">
                            <div class="ai-quiz-slider-display">
                                <span class="ai-quiz-slider-value" id="aiQuizSliderValue">10</span>
                                <span class="ai-quiz-slider-label"><?php echo get_phrase("questions"); ?></span>
                            </div>
                            <div class="ai-quiz-slider-wrapper">
                                <span class="ai-quiz-slider-min">3</span>
                                <input type="range" min="3" max="30" value="10" class="ai-quiz-slider" id="aiQuizSlider">
                                <span class="ai-quiz-slider-max">30</span>
                            </div>
                            <div class="ai-quiz-slider-presets">
                                <button type="button" class="ai-quiz-preset-btn" data-value="5">5</button>
                                <button type="button" class="ai-quiz-preset-btn active" data-value="10">10</button>
                                <button type="button" class="ai-quiz-preset-btn" data-value="15">15</button>
                                <button type="button" class="ai-quiz-preset-btn" data-value="20">20</button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Difficulty -->
                    <div class="ai-quiz-section">
                        <label class="ai-quiz-section-label">
                            <i class="fas fa-signal"></i>
                            <?php echo get_phrase("difficulty_level"); ?>
                        </label>
                        <div class="ai-quiz-difficulty-selector">
                            <label class="ai-quiz-difficulty-option" data-level="easy">
                                <div class="ai-quiz-difficulty-badge easy">
                                    <i class="fas fa-leaf"></i>
                                </div>
                                <span class="ai-quiz-difficulty-name"><?php echo get_phrase("easy"); ?></span>
                            </label>
                            <label class="ai-quiz-difficulty-option active" data-level="medium">
                                <div class="ai-quiz-difficulty-badge medium">
                                    <i class="fas fa-balance-scale"></i>
                                </div>
                                <span class="ai-quiz-difficulty-name"><?php echo get_phrase("medium"); ?></span>
                            </label>
                            <label class="ai-quiz-difficulty-option" data-level="hard">
                                <div class="ai-quiz-difficulty-badge hard">
                                    <i class="fas fa-fire"></i>
                                </div>
                                <span class="ai-quiz-difficulty-name"><?php echo get_phrase("hard"); ?></span>
                            </label>
                            <label class="ai-quiz-difficulty-option" data-level="mixed">
                                <div class="ai-quiz-difficulty-badge mixed">
                                    <i class="fas fa-shuffle"></i>
                                </div>
                                <span class="ai-quiz-difficulty-name"><?php echo get_phrase("mixed"); ?></span>
                            </label>
                        </div>
                    </div>
                    
                    <!-- Progress Section (hidden by default) -->
                    <div class="ai-quiz-section ai-quiz-progress-section" id="aiQuizProgressSection" style="display: none;">
                        <label class="ai-quiz-section-label">
                            <i class="fas fa-spinner fa-spin"></i>
                            <?php echo get_phrase("generation_progress"); ?>
                        </label>
                        <div class="ai-quiz-progress-container">
                            <div class="ai-quiz-progress-counter">
                                <span class="ai-quiz-progress-current" id="aiQuizProgressCurrent">0</span>
                                <span>/</span>
                                <span class="ai-quiz-progress-total" id="aiQuizProgressTotal">10</span>
                                <span class="ai-quiz-progress-label"><?php echo get_phrase("questions"); ?></span>
                            </div>
                            <div class="ai-quiz-progress-bar-wrapper">
                                <div class="ai-quiz-progress-bar" id="aiQuizProgressBar"></div>
                            </div>
                            <div class="ai-quiz-progress-steps">
                                <div class="ai-quiz-progress-step" id="aiQuizStep1">
                                    <i class="fas fa-book-open"></i>
                                    <span><?php echo get_phrase("loading_lessons"); ?></span>
                                </div>
                                <div class="ai-quiz-progress-step" id="aiQuizStep2">
                                    <i class="fas fa-brain"></i>
                                    <span><?php echo get_phrase("analyzing_content"); ?></span>
                                </div>
                                <div class="ai-quiz-progress-step" id="aiQuizStep3">
                                    <i class="fas fa-wand-magic-sparkles"></i>
                                    <span><?php echo get_phrase("generating_questions"); ?></span>
                                </div>
                                <div class="ai-quiz-progress-step" id="aiQuizStep4">
                                    <i class="fas fa-check-double"></i>
                                    <span><?php echo get_phrase("finalizing"); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="ai-quiz-modal-footer">
            <div class="ai-quiz-footer-info">
                <i class="fas fa-circle-nodes"></i>
                <small>WAYO AI · <?php echo get_phrase("create_questions_from_lessons"); ?></small>
            </div>
            <div class="ai-quiz-footer-actions">
                <button type="button" class="btn-ai-quiz-cancel" onclick="closeAiQuizModal()">
                    <i class="fas fa-times"></i> <?php echo get_phrase("cancel"); ?>
                </button>
                <button type="button" class="btn-ai-quiz-generate" id="btnAiQuizGenerate" onclick="generateAiQuiz()">
                    <i class="fas fa-wand-magic-sparkles"></i> <?php echo get_phrase("generate_quiz"); ?>
                </button>
            </div>
        </div>
    </div>
    
    <!-- Hidden inputs -->
    <input type="hidden" id="aiQuizSectionId" value="">
    <input type="hidden" id="aiQuizDifficulty" value="medium">
    <input type="hidden" id="aiQuizQuestionsCount" value="10">
</div>

<!-- AI Lesson Generation Modal -->
<div class="ai-lesson-modal" id="aiLessonModal" style="display: none;">
    <div class="ai-lesson-modal-content">
        <div class="ai-lesson-modal-header">
            <div>
                <h5 class="ai-lesson-modal-title">
                    <i class="fas fa-wand-magic-sparkles"></i>
                    <?php echo get_phrase("generate_lesson_with_ai"); ?>
                </h5>
                <p class="ai-lesson-modal-subtitle">
                    <?php echo get_phrase("create_lesson_content_from_pdf_or_course_outline"); ?>
                </p>
            </div>
            <button type="button" class="ai-lesson-modal-close" onclick="closeAiLessonModal()">
                <i class="fas fa-xmark"></i>
            </button>
        </div>

        <div class="ai-lesson-modal-body">
            <!-- Source Selection -->
            <div class="ai-lesson-source-section">
                <h6 class="ai-lesson-section-title">
                    <i class="fas fa-database"></i>
                    <?php echo get_phrase("source_material"); ?>
                </h6>
                <div class="ai-lesson-source-options">
                    <div class="ai-lesson-source-option">
                        <input type="radio" id="sourcePdf" name="lessonSource" value="pdf" checked>
                        <label for="sourcePdf" class="ai-lesson-source-label">
                            <div class="ai-lesson-source-icon">
                                <i class="fas fa-file-pdf"></i>
                            </div>
                            <div class="ai-lesson-source-content">
                                <span class="ai-lesson-source-title"><?php echo get_phrase("upload_pdf_file"); ?></span>
                                <span class="ai-lesson-source-desc"><?php echo get_phrase("extract_content_from_pdf"); ?></span>
                            </div>
                        </label>
                    </div>
                    <div class="ai-lesson-source-option">
                        <input type="radio" id="sourceOutline" name="lessonSource" value="outline">
                        <label for="sourceOutline" class="ai-lesson-source-label">
                            <div class="ai-lesson-source-icon">
                                <i class="fas fa-sitemap"></i>
                            </div>
                            <div class="ai-lesson-source-content">
                                <span class="ai-lesson-source-title"><?php echo get_phrase("use_course_outline"); ?></span>
                                <span class="ai-lesson-source-desc"><?php echo get_phrase("based_on_section_and_lesson_titles"); ?></span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- PDF Upload -->
                <div class="ai-lesson-pdf-upload" id="pdfUploadSection">
                    <div class="ai-lesson-upload-area" id="pdfUploadArea">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <p class="ai-lesson-upload-text"><?php echo get_phrase("drag_drop_pdf_or_click_to_browse"); ?></p>
                        <p class="ai-lesson-upload-subtext"><?php echo get_phrase("max_file_size_10mb"); ?></p>
                        <input type="file" id="aiLessonPdfFileInput" accept=".pdf" style="display: none;">
                        <button type="button" class="btn-ai-lesson-browse" onclick="document.getElementById('aiLessonPdfFileInput').click(); event.stopPropagation();">
                            <?php echo get_phrase("browse_files"); ?>
                        </button>
                    </div>
                    <div class="ai-lesson-upload-preview" id="pdfUploadPreview" style="display: none;">
                        <div class="ai-lesson-upload-file">
                            <i class="fas fa-file-pdf"></i>
                            <div class="ai-lesson-upload-file-info">
                                <span class="ai-lesson-upload-file-name" id="pdfFileName"></span>
                                <span class="ai-lesson-upload-file-size" id="pdfFileSize"></span>
                            </div>
                            <button type="button" class="ai-lesson-upload-remove" onclick="removePdfFile()">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Course Outline Info -->
                <div class="ai-lesson-outline-info" id="outlineInfoSection" style="display: none;">
                    <div class="ai-lesson-outline-card">
                        <i class="fas fa-info-circle"></i>
                        <div class="ai-lesson-outline-content">
                            <p><?php echo get_phrase("lesson_will_be_based_on_course_structure"); ?></p>
                            <small><?php echo get_phrase("using_section_and_lesson_titles_as_context"); ?></small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lesson Configuration -->
            <div class="ai-lesson-config-section">
                <h6 class="ai-lesson-section-title">
                    <i class="fas fa-sliders"></i>
                    <?php echo get_phrase("lesson_configuration"); ?>
                </h6>
                
                <div class="ai-lesson-config-grid">
                    <div class="ai-lesson-config-field">
                        <label for="lessonAudience">
                            <i class="fas fa-users"></i>
                            <?php echo get_phrase("audience"); ?>
                        </label>
                        <select id="lessonAudience" class="ai-lesson-select">
                            <option value="beginner"><?php echo get_phrase("beginner"); ?></option>
                            <option value="intermediate" selected><?php echo get_phrase("intermediate"); ?></option>
                            <option value="advanced"><?php echo get_phrase("advanced"); ?></option>
                            <option value="expert"><?php echo get_phrase("expert"); ?></option>
                        </select>
                    </div>

                    <div class="ai-lesson-config-field">
                        <label for="lessonDuration">
                            <i class="fas fa-clock"></i>
                            <?php echo get_phrase("lesson_duration"); ?>
                        </label>
                        <select id="lessonDuration" class="ai-lesson-select">
                            <option value="5">5 <?php echo get_phrase("minutes"); ?></option>
                            <option value="10">10 <?php echo get_phrase("minutes"); ?></option>
                            <option value="15" selected>15 <?php echo get_phrase("minutes"); ?></option>
                            <option value="20">20 <?php echo get_phrase("minutes"); ?></option>
                            <option value="30">30 <?php echo get_phrase("minutes"); ?></option>
                            <option value="45">45 <?php echo get_phrase("minutes"); ?></option>
                            <option value="60">60 <?php echo get_phrase("minutes"); ?></option>
                        </select>
                    </div>

                    <div class="ai-lesson-config-field">
                        <label for="lessonLanguage">
                            <i class="fas fa-language"></i>
                            <?php echo get_phrase("language"); ?>
                        </label>
                        <select id="lessonLanguage" class="ai-lesson-select">
                            <option value="french" selected><?php echo get_phrase("french"); ?></option>
                            <option value="english"><?php echo get_phrase("english"); ?></option>
                            <option value="spanish"><?php echo get_phrase("spanish"); ?></option>
                            <option value="dutch"><?php echo get_phrase("dutch"); ?></option>
                            <option value="arabic"><?php echo get_phrase("arabic"); ?></option>
                        </select>
                    </div>

                    <div class="ai-lesson-config-field">
                        <label for="lessonTone">
                            <i class="fas fa-comment-dots"></i>
                            <?php echo get_phrase("tone"); ?>
                        </label>
                        <select id="lessonTone" class="ai-lesson-select">
                            <option value="very_concise">Very concise (bullet points)</option>
                            <option value="concise_technical" selected>Concise & technical</option>
                            <option value="neutral_professional">Neutral & professional</option>
                            <option value="pedagogical_progressive">Pedagogical & progressive</option>
                            <option value="coach_motivating">Coach / motivating</option>
                            <option value="conversational">Conversational (friendly)</option>
                            <option value="corporate_institutional">Corporate / institutional</option>
                            <option value="expert_best_practices">Expert / best practices</option>
                            <option value="storytelling">Storytelling</option>
                            <option value="action_oriented">Action oriented (checklists)</option>
                            <option value="compliance_oriented">Compliance oriented (rigorous)</option>
                            <option value="support_troubleshooting">Support / troubleshooting</option>
                        </select>
                    </div>
                </div>


            </div>

            <!-- Progress Indicator -->
            <div class="ai-lesson-progress" id="aiLessonProgress" style="display: none;">
                <div class="ai-lesson-progress-header">
                    <i class="fas fa-spinner fa-spin"></i>
                    <span><?php echo get_phrase("generating_lesson_content"); ?></span>
                </div>
                <div class="ai-lesson-progress-steps">
                    <div class="ai-lesson-progress-step active" id="aiLessonStep1">
                        <i class="fas fa-file-import"></i>
                        <span><?php echo get_phrase("processing_source"); ?></span>
                    </div>
                    <div class="ai-lesson-progress-step" id="aiLessonStep2">
                        <i class="fas fa-brain"></i>
                        <span><?php echo get_phrase("analyzing_content"); ?></span>
                    </div>
                    <div class="ai-lesson-progress-step" id="aiLessonStep3">
                        <i class="fas fa-wand-magic-sparkles"></i>
                        <span><?php echo get_phrase("generating_content"); ?></span>
                    </div>
                    <div class="ai-lesson-progress-step" id="aiLessonStep4">
                        <i class="fas fa-check-double"></i>
                        <span><?php echo get_phrase("finalizing"); ?></span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="ai-lesson-modal-footer">
            <div class="ai-lesson-footer-info">
                <i class="fas fa-circle-nodes"></i>
                <small>WAYO AI · <?php echo get_phrase("create_lesson_content"); ?></small>
            </div>
            <div class="ai-lesson-footer-actions">
                <button type="button" class="btn-ai-lesson-cancel" onclick="closeAiLessonModal()">
                    <i class="fas fa-times"></i> <?php echo get_phrase("cancel"); ?>
                </button>
                <button type="button" class="btn-ai-lesson-generate" id="btnAiLessonGenerate" onclick="generateAiLesson()">
                    <i class="fas fa-wand-magic-sparkles"></i> <?php echo get_phrase("generate_lesson"); ?>
                </button>
            </div>
        </div>
    </div>
    
    <!-- Hidden inputs -->
    <input type="hidden" id="aiLessonSectionId" value="">
    <input type="hidden" id="aiLessonLessonId" value="">
    <input type="hidden" id="aiLessonCourseId" value="<?php echo $course['id']; ?>">
</div>

<script>
// ========== AI QUIZ GENERATION ==========

let aiQuizCurrentSectionId = null;
let aiQuizGenerationInProgress = false;

// Toggle Quiz Dropdown
function toggleQuizDropdown(sectionId) {
    const dropdown = document.getElementById('quizDropdown-' + sectionId);
    const allDropdowns = document.querySelectorAll('.quiz-dropdown-menu');
    
    // Close all other dropdowns
    allDropdowns.forEach(d => {
        if (d.id !== 'quizDropdown-' + sectionId) {
            d.classList.remove('show');
        }
    });
    
    // Toggle current dropdown
    dropdown.classList.toggle('show');
}

// Close all quiz dropdowns
function closeQuizDropdowns() {
    document.querySelectorAll('.quiz-dropdown-menu').forEach(d => {
        d.classList.remove('show');
    });
}

// Close dropdowns when clicking outside
document.addEventListener('click', function(e) {
    if (!e.target.closest('.quiz-dropdown-container')) {
        closeQuizDropdowns();
    }
    if (!e.target.closest('.lesson-dropdown-container')) {
        closeLessonDropdowns();
    }
});

// Toggle Lesson Dropdown
function toggleLessonDropdown(sectionId) {
    const dropdown = document.getElementById('lessonDropdown-' + sectionId);
    const allDropdowns = document.querySelectorAll('.lesson-dropdown-menu');
    
    // Close all other dropdowns
    allDropdowns.forEach(d => {
        if (d.id !== 'lessonDropdown-' + sectionId) {
            d.classList.remove('show');
        }
    });
    
    // Toggle current dropdown
    dropdown.classList.toggle('show');
}

// Close all lesson dropdowns
function closeLessonDropdowns() {
    document.querySelectorAll('.lesson-dropdown-menu').forEach(d => {
        d.classList.remove('show');
    });
}

// Selected lessons for AI quiz
let aiQuizSelectedLessons = [];

// Open AI Quiz Modal
function openAiQuizModal(sectionId) {
    aiQuizCurrentSectionId = sectionId;
    document.getElementById('aiQuizSectionId').value = sectionId;
    document.getElementById('aiQuizModal').style.display = 'flex';
    resetAiQuizModal();
    loadLessonsForQuiz(sectionId);
}

// Close AI Quiz Modal
function closeAiQuizModal() {
    if (aiQuizGenerationInProgress) {
        if (!confirm('<?php echo addslashes(get_phrase("generation_in_progress_confirm_close")); ?>')) {
            return;
        }
    }
    document.getElementById('aiQuizModal').style.display = 'none';
    resetAiQuizModal();
}

// Reset AI Quiz Modal
function resetAiQuizModal() {
    document.getElementById('aiQuizTitle').value = '';
    document.getElementById('aiQuizSlider').value = 10;
    document.getElementById('aiQuizSliderValue').textContent = '10';
    document.getElementById('aiQuizQuestionsCount').value = 10;
    document.getElementById('aiQuizDifficulty').value = 'medium';
    document.getElementById('aiQuizProgressSection').style.display = 'none';
    
    // Reset lesson selection
    aiQuizSelectedLessons = [];
    document.getElementById('aiQuizLessonsLoading').style.display = 'flex';
    document.getElementById('aiQuizLessonsEmpty').style.display = 'none';
    document.getElementById('aiQuizLessonsList').style.display = 'none';
    document.getElementById('aiQuizSelectionInfo').style.display = 'none';
    
    // Reset preset buttons
    document.querySelectorAll('.ai-quiz-preset-btn').forEach(btn => {
        btn.classList.remove('active');
        if (btn.dataset.value === '10') btn.classList.add('active');
    });
    
    // Reset difficulty
    document.querySelectorAll('.ai-quiz-difficulty-option').forEach(opt => {
        opt.classList.remove('active');
        if (opt.dataset.level === 'medium') opt.classList.add('active');
    });
    
    // Reset generate button
    const generateBtn = document.getElementById('btnAiQuizGenerate');
    generateBtn.disabled = false;
    generateBtn.innerHTML = '<i class="fas fa-wand-magic-sparkles"></i> <?php echo addslashes(get_phrase("generate_quiz")); ?>';
    
    updateAiQuizSliderBackground(10);
    aiQuizGenerationInProgress = false;
}

// Load lessons for quiz generation
function loadLessonsForQuiz(targetSectionId) {
    // Get all sections up to and including the target section
    const allSections = document.querySelectorAll('.outline-section');
    const sectionsToLoad = [];

    for (let section of allSections) {
        const sectionId = section.dataset.sectionId;
        sectionsToLoad.push(sectionId);

        // Stop when we reach the target section (inclusive)
        if (sectionId === targetSectionId.toString()) {
            break;
        }
    }

    $.ajax({
        url: '<?php echo site_url("addons/courses/get_lessons_for_quiz"); ?>/' + courseId + '/' + targetSectionId,
        type: 'POST',
        data: {
            sections_to_include: JSON.stringify(sectionsToLoad),
            [document.getElementById('csrf_name').value]: document.getElementById('csrf_hash').value
        },
        dataType: 'json',
        cache: false,
        success: function(data) {
            document.getElementById('aiQuizLessonsLoading').style.display = 'none';

            if (data.success && data.sections && data.sections.length > 0) {
                renderLessonsList(data.sections);
                document.getElementById('aiQuizLessonsList').style.display = 'block';
            } else {
                document.getElementById('aiQuizLessonsEmpty').style.display = 'flex';
            }

            // Update CSRF token
            if (data.csrf) {
                document.getElementById('csrf_hash').value = data.csrf.csrfHash;
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading lessons:', status, error);
            document.getElementById('aiQuizLessonsLoading').style.display = 'none';
            document.getElementById('aiQuizLessonsEmpty').style.display = 'flex';

            // Try to update CSRF token from error response
            try {
                const response = JSON.parse(xhr.responseText);
                if (response.csrf) {
                    document.getElementById('csrf_hash').value = response.csrf.csrfHash;
                }
            } catch(e) {}
        }
    });
}

// Render lessons list
function renderLessonsList(sections) {
    const container = document.getElementById('aiQuizLessonsList');
    container.innerHTML = '';
    
    sections.forEach(section => {
        const sectionDiv = document.createElement('div');
        sectionDiv.className = 'ai-quiz-section-group';
        sectionDiv.innerHTML = `
            <div class="ai-quiz-section-header">
                <i class="fas fa-folder"></i>
                <span>${escapeHtml(section.title).replace(/\\'/g, "'")}</span>
                <span class="ai-quiz-section-count">${section.lessons.length} <?php echo addslashes(get_phrase("lessons")); ?></span>
            </div>
            <div class="ai-quiz-lessons-items">
                ${section.lessons.map(lesson => `
                    <label class="ai-quiz-lesson-item" data-lesson-id="${lesson.id}">
                        <input type="checkbox" value="${lesson.id}" onchange="toggleLessonSelection(${lesson.id}, '${escapeHtml(lesson.title)}')">
                        <span class="ai-quiz-lesson-checkbox"></span>
                        <i class="fas fa-file-lines"></i>
                        <span class="ai-quiz-lesson-title">${escapeHtml(lesson.title).replace(/\\'/g, "'")}</span>
                    </label>
                `).join('')}
            </div>
        `;
        container.appendChild(sectionDiv);
    });
}

// Toggle lesson selection
function toggleLessonSelection(lessonId, lessonTitle) {
    const index = aiQuizSelectedLessons.findIndex(l => l.id === lessonId);
    
    if (index > -1) {
        aiQuizSelectedLessons.splice(index, 1);
    } else {
        aiQuizSelectedLessons.push({ id: lessonId, title: lessonTitle });
    }
    
    updateSelectionInfo();
}

// Update selection info
function updateSelectionInfo() {
    const count = aiQuizSelectedLessons.length;
    const infoEl = document.getElementById('aiQuizSelectionInfo');
    const countEl = document.getElementById('aiQuizSelectedCount');
    
    if (count > 0) {
        infoEl.style.display = 'flex';
        countEl.textContent = count;
    } else {
        infoEl.style.display = 'none';
    }
}

// Clear lesson selection
function clearLessonSelection() {
    aiQuizSelectedLessons = [];
    document.querySelectorAll('.ai-quiz-lesson-item input[type="checkbox"]').forEach(cb => {
        cb.checked = false;
    });
    updateSelectionInfo();
}

// Update slider background
function updateAiQuizSliderBackground(value) {
    const slider = document.getElementById('aiQuizSlider');
    if (slider) {
        const percent = ((value - 3) / (30 - 3)) * 100;
        slider.style.background = `linear-gradient(90deg, #f47a1f 0%, #fbb040 ${percent}%, #e2e8f0 ${percent}%)`;
    }
}

// Initialize AI Quiz Modal handlers
function initAiQuizHandlers() {
    // Slider handling
    const slider = document.getElementById('aiQuizSlider');
    if (slider) {
        slider.addEventListener('input', function() {
            const value = this.value;
            document.getElementById('aiQuizSliderValue').textContent = value;
            document.getElementById('aiQuizQuestionsCount').value = value;
            updateAiQuizSliderBackground(value);
            
            // Update preset buttons
            document.querySelectorAll('.ai-quiz-preset-btn').forEach(btn => {
                btn.classList.remove('active');
                if (btn.dataset.value === value) btn.classList.add('active');
            });
        });
        
        updateAiQuizSliderBackground(10);
    }
    
    // Preset buttons
    document.querySelectorAll('.ai-quiz-preset-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const value = this.dataset.value;
            document.getElementById('aiQuizSlider').value = value;
            document.getElementById('aiQuizSliderValue').textContent = value;
            document.getElementById('aiQuizQuestionsCount').value = value;
            updateAiQuizSliderBackground(value);
            
            document.querySelectorAll('.ai-quiz-preset-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
        });
    });
    
    // Difficulty options
    document.querySelectorAll('.ai-quiz-difficulty-option').forEach(opt => {
        opt.addEventListener('click', function() {
            document.querySelectorAll('.ai-quiz-difficulty-option').forEach(o => o.classList.remove('active'));
            this.classList.add('active');
            document.getElementById('aiQuizDifficulty').value = this.dataset.level;
        });
    });
}

// Initialize on DOMContentLoaded
document.addEventListener('DOMContentLoaded', initAiQuizHandlers);

// Generate AI Quiz
function generateAiQuiz() {
    const title = document.getElementById('aiQuizTitle').value.trim();
    const sectionId = document.getElementById('aiQuizSectionId').value;
    const questionsCount = document.getElementById('aiQuizQuestionsCount').value;
    const difficulty = document.getElementById('aiQuizDifficulty').value;
    
    // Validation
    if (!title) {
        toastr.error('<?php echo addslashes(get_phrase("please_enter_quiz_title")); ?>');
        document.getElementById('aiQuizTitle').focus();
        return;
    }
    
    if (aiQuizSelectedLessons.length === 0) {
        toastr.error('<?php echo addslashes(get_phrase("please_select_at_least_one_lesson")); ?>');
        return;
    }
    
    aiQuizGenerationInProgress = true;
    
    // Update UI
    const generateBtn = document.getElementById('btnAiQuizGenerate');
    generateBtn.disabled = true;
    generateBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <?php echo addslashes(get_phrase("generating")); ?>...';
    
    document.getElementById('aiQuizProgressSection').style.display = 'block';
    document.getElementById('aiQuizProgressTotal').textContent = questionsCount;
    resetAiQuizProgress();
    setAiQuizProgressStep(1);
    
    // Simulate progress
    let currentStep = 1;
    let simulatedQuestions = 0;
    const totalQuestions = parseInt(questionsCount);
    
    const progressSimulation = setInterval(function() {
        if (currentStep < 4) {
            const randomProgress = Math.random();
            if (randomProgress > 0.6 && currentStep < 3) {
                currentStep++;
                setAiQuizProgressStep(currentStep);
            }
        }
        
        if (currentStep >= 3 && simulatedQuestions < totalQuestions - 1) {
            simulatedQuestions++;
            document.getElementById('aiQuizProgressCurrent').textContent = simulatedQuestions;
            updateAiQuizProgressBar(simulatedQuestions, totalQuestions);
        }
    }, 800);
    
    // Build data object
    const csrfName = document.getElementById('csrf_name').value;
    const csrfHash = document.getElementById('csrf_hash').value;
    
    const postData = {
        lesson_ids: JSON.stringify(aiQuizSelectedLessons.map(l => l.id)),
        section_id: sectionId,
        course_id: courseId,
        quiz_title: title,
        questions_count: questionsCount,
        difficulty: difficulty
    };
    postData[csrfName] = csrfHash;
    
    // Make API call with jQuery
    $.ajax({
        url: '<?php echo site_url("addons/courses/generate_quiz_from_lessons"); ?>',
        type: 'POST',
        data: postData,
        dataType: 'json',
        success: function(data) {
            clearInterval(progressSimulation);
            
            // Update CSRF
            if (data.csrf) {
                document.getElementById('csrf_hash').value = data.csrf.csrfHash;
            }
            
            if (data.success) {
                setAiQuizProgressStep(4);
                document.getElementById('aiQuizProgressCurrent').textContent = data.questions_count || totalQuestions;
                updateAiQuizProgressBar(totalQuestions, totalQuestions);
                
                toastr.success('<?php echo addslashes(get_phrase("quiz_generated_successfully")); ?>');
                
                setTimeout(function() {
                    closeAiQuizModal();
                    location.reload();
                }, 1000);
            } else {
                toastr.error(data.message || '<?php echo addslashes(get_phrase("error_generating_quiz")); ?>');
                resetAiQuizGenerateButton();
            }
            
            aiQuizGenerationInProgress = false;
        },
        error: function(xhr, status, error) {
            clearInterval(progressSimulation);
            console.error('Error:', status, error);
            
            // Try to get error message from response
            try {
                const response = JSON.parse(xhr.responseText);
                if (response.csrf) {
                    document.getElementById('csrf_hash').value = response.csrf.csrfHash;
                }
                toastr.error(response.message || '<?php echo addslashes(get_phrase("error_generating_quiz")); ?>');
            } catch(e) {
                toastr.error('<?php echo addslashes(get_phrase("error_generating_quiz")); ?>');
            }
            
            resetAiQuizGenerateButton();
            aiQuizGenerationInProgress = false;
        }
    });
}

function resetAiQuizProgress() {
    document.getElementById('aiQuizProgressCurrent').textContent = '0';
    document.getElementById('aiQuizProgressBar').style.width = '0%';
    document.querySelectorAll('.ai-quiz-progress-step').forEach(step => {
        step.classList.remove('active', 'completed');
    });
}

function setAiQuizProgressStep(stepNumber) {
    document.querySelectorAll('.ai-quiz-progress-step').forEach((step, index) => {
        const stepNum = index + 1;
        if (stepNum < stepNumber) {
            step.classList.remove('active');
            step.classList.add('completed');
        } else if (stepNum === stepNumber) {
            step.classList.remove('completed');
            step.classList.add('active');
        } else {
            step.classList.remove('active', 'completed');
        }
    });
}

function updateAiQuizProgressBar(current, total) {
    const percent = Math.round((current / total) * 100);
    document.getElementById('aiQuizProgressBar').style.width = percent + '%';
}

function resetAiQuizGenerateButton() {
    const generateBtn = document.getElementById('btnAiQuizGenerate');
    generateBtn.disabled = false;
    generateBtn.innerHTML = '<i class="fas fa-wand-magic-sparkles"></i> <?php echo addslashes(get_phrase("generate_quiz")); ?>';
}

// Close modal on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && document.getElementById('aiQuizModal').style.display === 'flex') {
        closeAiQuizModal();
    }
});

// Close modal on backdrop click
document.getElementById('aiQuizModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeAiQuizModal();
    }
});

// ========== AI LESSON GENERATION ==========

let aiLessonCurrentSectionId = null;
let aiLessonGenerationInProgress = false;
let aiLessonPdfEventListener = null; // Store event listener reference

// Open Generate Lesson Modal (for existing lessons)
function openGenerateLessonModal(lessonId, sectionId, hasContent) {
    if (hasContent) {
        Swal.fire({
            title: '<?php echo addslashes(get_phrase("warning_lesson_has_content")); ?>',
            text: '<?php echo addslashes(get_phrase("generating_ai_content_will_overwrite_current_lesson_content")); ?>',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: '<?php echo addslashes(get_phrase("yes_overwrite_it")); ?>',
            cancelButtonText: '<?php echo addslashes(get_phrase("cancel")); ?>',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                _proceedToOpenAiModal(lessonId, sectionId);
            }
        });
    } else {
        _proceedToOpenAiModal(lessonId, sectionId);
    }
}

function openGenerateLessonFromPreview() {
    const lessonId = document.getElementById('previewLessonId').value;
    const sectionId = document.getElementById('previewSectionId').value;
    const previewContent = document.getElementById('previewContent');
    
    // Check if there is content (if .preview-empty is NOT present)
    let hasContent = true;
    if (previewContent.querySelector('.preview-empty')) {
        hasContent = false;
    }
    
    openGenerateLessonModal(lessonId, sectionId, hasContent);
}

function _proceedToOpenAiModal(lessonId, sectionId) {
    aiLessonCurrentSectionId = sectionId;
    document.getElementById('aiLessonSectionId').value = sectionId;
    document.getElementById('aiLessonLessonId').value = lessonId;
    
    showModal('aiLessonModal');
    resetAiLessonModal();
    initAiLessonPdfEvents();
    updateSourceSections();
}

// Open AI Lesson Modal
function openAiLessonModal(sectionId) {
    aiLessonCurrentSectionId = sectionId;
    document.getElementById('aiLessonSectionId').value = sectionId;
    document.getElementById('aiLessonLessonId').value = ''; // Clear lesson ID for new lesson
    showModal('aiLessonModal');
    resetAiLessonModal();

    // Initialize PDF upload events only when modal opens
    initAiLessonPdfEvents();

    // Show/hide sections based on source selection
    updateSourceSections();
}

// Close AI Lesson Modal
function closeAiLessonModal() {
    if (aiLessonGenerationInProgress) {
        if (!confirm('<?php echo addslashes(get_phrase("generation_in_progress_confirm_close")); ?>')) {
            return;
        }
    }
    hideModal('aiLessonModal');

    // Clean up file upload events
    cleanupFileUploadEvents('pdfUploadArea', 'aiLessonPdfFileInput');

    // Clean up file input
    const fileInput = document.getElementById('aiLessonPdfFileInput');
    if (fileInput) {
        fileInput.value = '';
        removePdfFile();
    }
}

// Initialize AI Lesson PDF events
function initAiLessonPdfEvents() {
    // Clean up any existing events first
    cleanupFileUploadEvents('pdfUploadArea', 'aiLessonPdfFileInput');
    
    setupFileUpload('pdfUploadArea', 'aiLessonPdfFileInput', 'pdfUploadPreview', {
        onFileSelected: function(file) {
            displayPdfFile(file);
        }
    });
}

// Reset AI Lesson Modal
function resetAiLessonModal() {
    // Reset form
    document.getElementById('sourcePdf').checked = true;
    document.getElementById('lessonAudience').value = 'intermediate';
    document.getElementById('lessonDuration').value = '15';
    document.getElementById('lessonLanguage').value = 'french';
    document.getElementById('lessonTone').value = 'concise_technical';
    
    // Reset file upload
    removePdfFile();
    
    // Reset progress
    resetAiLessonProgress();
}

// Reset AI Lesson Progress
function resetAiLessonProgress() {
    document.getElementById('aiLessonProgress').style.display = 'none';
    document.querySelectorAll('.ai-lesson-progress-step').forEach(step => {
        step.classList.remove('active');
    });
    document.getElementById('aiLessonStep1').classList.add('active');
}

// Update source sections visibility
function updateSourceSections() {
    const source = document.querySelector('input[name="lessonSource"]:checked').value;
    const pdfSection = document.getElementById('pdfUploadSection');
    const outlineSection = document.getElementById('outlineInfoSection');
    
    if (source === 'pdf') {
        pdfSection.style.display = 'block';
        outlineSection.style.display = 'none';
    } else {
        pdfSection.style.display = 'none';
        outlineSection.style.display = 'block';
    }
}

// PDF event listeners are now managed dynamically in initAiLessonPdfEvents()
// This prevents conflicts between multiple modals

// Display PDF file info
function displayPdfFile(file) {
    const fileName = document.getElementById('pdfFileName');
    const fileSize = document.getElementById('pdfFileSize');
    const uploadArea = document.getElementById('pdfUploadArea');
    const uploadPreview = document.getElementById('pdfUploadPreview');
    
    fileName.textContent = file.name;
    fileSize.textContent = formatFileSize(file.size);
    
    uploadArea.style.display = 'none';
    uploadPreview.style.display = 'block';
    
    // Show analyzing message
    const analyzingMsg = document.createElement('div');
    analyzingMsg.className = 'ai-lesson-upload-analyzing';
    analyzingMsg.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <span><?php echo addslashes(get_phrase("analyzing_pdf_structure")); ?></span>';
    uploadPreview.appendChild(analyzingMsg);
    
    // In a real implementation, you would upload the file to the server
    // and get the structure analysis back via AJAX
    // For now, we'll just show a success message after a short delay
    setTimeout(() => {
        analyzingMsg.innerHTML = '<i class="fas fa-check-circle" style="color: #10b981;"></i> <span><?php echo addslashes(get_phrase("pdf_structure_analyzed_successfully")); ?></span>';
        analyzingMsg.className = 'ai-lesson-upload-analyzed';
    }, 1500);
}

// Remove PDF file
function removePdfFile() {
    const uploadArea = document.getElementById('pdfUploadArea');
    const uploadPreview = document.getElementById('pdfUploadPreview');
    const fileInput = document.getElementById('aiLessonPdfFileInput');

    uploadArea.style.display = 'block';
    uploadPreview.style.display = 'none';
    fileInput.value = '';
}

// Format file size
function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

// Generate AI Lesson
function generateAiLesson() {
    const sectionId = document.getElementById('aiLessonSectionId').value;
    const courseId = document.getElementById('aiLessonCourseId').value;
    const lessonId = document.getElementById('aiLessonLessonId').value;
    const source = document.querySelector('input[name="lessonSource"]:checked').value;
    const audience = document.getElementById('lessonAudience').value;
    const duration = document.getElementById('lessonDuration').value;
    const language = document.getElementById('lessonLanguage').value;
    const tone = document.getElementById('lessonTone').value;
    
    // Validate PDF upload if source is PDF
    if (source === 'pdf') {
        const fileInput = document.getElementById('aiLessonPdfFileInput');
        if (!fileInput.files.length) {
            alert('<?php echo addslashes(get_phrase("please_upload_pdf_file")); ?>');
            return;
        }
    }
    
    // Show progress
    document.getElementById('aiLessonProgress').style.display = 'block';
    const generateBtn = document.getElementById('btnAiLessonGenerate');
    generateBtn.disabled = true;
    generateBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <?php echo addslashes(get_phrase("generating")); ?>';
    aiLessonGenerationInProgress = true;
    
    // Create form data
    const formData = new FormData();
    formData.append('section_id', sectionId);
    formData.append('course_id', courseId);
    if (lessonId) {
        formData.append('lesson_id', lessonId);
    }
    formData.append('source', source);
    formData.append('audience', audience);
    formData.append('duration', duration);
    formData.append('language', language);
    formData.append('tone', tone);
    
    if (source === 'pdf') {
        formData.append('pdf_file', document.getElementById('aiLessonPdfFileInput').files[0]);
    }
    
    // Add CSRF token
    formData.append(document.getElementById('csrf_name').value, document.getElementById('csrf_hash').value);
    
    const url = lessonId 
        ? '<?php echo site_url("addons/courses/update_lesson_from_ai"); ?>' 
        : '<?php echo site_url("addons/courses/generate_lesson_from_ai"); ?>';
    
    // Send request
    fetch(url, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update progress steps
            document.querySelectorAll('.ai-lesson-progress-step').forEach(step => {
                step.classList.remove('active');
            });
            document.getElementById('aiLessonStep4').classList.add('active');
            
            // Show success message and add lesson to sidebar
            setTimeout(() => {
                closeAiLessonModal();
                toastr.success(lessonId ? '<?php echo addslashes(get_phrase("lesson_updated_successfully")); ?>' : '<?php echo addslashes(get_phrase("lesson_generated_successfully")); ?>');

                if (lessonId) {
                    // Open the lesson in editor to show new content
                    openLessonEditor(lessonId, sectionId);
                } else {
                    // Add the new lesson to the sidebar without page refresh
                    if (data.lesson_id && data.lesson_title) {
                        addLessonToSidebar(data.lesson_id, sectionId, data.lesson_title, 'lesson');

                        // Open the lesson in editor after delay to ensure DOM is updated and content is ready
                        setTimeout(() => {
                            openLessonEditor(data.lesson_id, sectionId);
                        }, 300);
                    }
                }
            }, 1000);
        } else {
            throw new Error(data.message || '<?php echo addslashes(get_phrase("generation_failed")); ?>');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        toastr.error(error.message || '<?php echo addslashes(get_phrase("generation_failed")); ?>');
    })
    .finally(() => {
        generateBtn.disabled = false;
        generateBtn.innerHTML = '<i class="fas fa-wand-magic-sparkles"></i> <?php echo addslashes(get_phrase("generate_lesson")); ?>';
        aiLessonGenerationInProgress = false;
    });
}

// Update progress steps
function updateAiLessonProgress(step) {
    document.querySelectorAll('.ai-lesson-progress-step').forEach((s, index) => {
        if (index + 1 <= step) {
            s.classList.add('active');
        } else {
            s.classList.remove('active');
        }
    });
}

// Close modal on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && document.getElementById('aiLessonModal').style.display === 'flex') {
        closeAiLessonModal();
    }
});

// Close modal on backdrop click
document.getElementById('aiLessonModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeAiLessonModal();
    }
});

// Listen to source changes
document.querySelectorAll('input[name="lessonSource"]').forEach(radio => {
    radio.addEventListener('change', updateSourceSections);
});

// Handle drag and drop for PDF
const uploadArea = document.getElementById('pdfUploadArea');
uploadArea.addEventListener('dragover', (e) => {
    e.preventDefault();
    uploadArea.style.borderColor = '#4f46e5';
    uploadArea.style.background = '#f8fafc';
});

uploadArea.addEventListener('dragleave', (e) => {
    e.preventDefault();
    uploadArea.style.borderColor = '#cbd5e1';
    uploadArea.style.background = 'white';
});

uploadArea.addEventListener('drop', (e) => {
    e.preventDefault();
    uploadArea.style.borderColor = '#cbd5e1';
    uploadArea.style.background = 'white';
    
    const file = e.dataTransfer.files[0];
    if (file && file.type === 'application/pdf') {
        if (file.size > 10 * 1024 * 1024) {
            alert('<?php echo addslashes(get_phrase("file_size_exceeds_10mb_limit")); ?>');
            return;
        }
        
        // Create a new FileList
        const dataTransfer = new DataTransfer();
        dataTransfer.items.add(file);
        document.getElementById('aiLessonPdfFileInput').files = dataTransfer.files;

        // Trigger change event
        const event = new Event('change', { bubbles: true });
        document.getElementById('aiLessonPdfFileInput').dispatchEvent(event);
    } else {
        alert('<?php echo addslashes(get_phrase("please_drop_pdf_file_only")); ?>');
    }
});

// Click upload area to trigger file input
uploadArea.addEventListener('click', () => {
    document.getElementById('aiLessonPdfFileInput').click();
});
</script>