<link rel="stylesheet" href="<?php echo base_url(); ?>assets/backend/css/curriculum.css">

<!-- SweetAlert2 -->
<script type="text/javascript" src="<?php echo base_url(); ?>assets/backend/js/sweetalert.js"></script>

<!-- Quill Editor 1.3.7 (compatible avec les extensions) -->
<link href="<?php echo base_url(); ?>assets/backend/css/quilljs/quill.snow.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="<?php echo base_url(); ?>assets/backend/js/quilljs/quill.min.js"></script>

<!-- Quill Image Resize -->
<script type="text/javascript" src="<?php echo base_url(); ?>assets/backend/js/quilljs/image-resize.min.js"></script>

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
                    <span><?php echo get_phrase('lesson'); ?></span>
                </div>
                <button type="button" class="btn-edit-preview" id="btnEditPreview" onclick="switchToEditMode()">
                    <i class="fas fa-pen-to-square"></i> <?php echo get_phrase('edit'); ?>
                </button>
            </div>
            
            <!-- Preview Title -->
            <div class="preview-title-section">
                <h2 id="previewTitle"><?php echo get_phrase('lesson_title'); ?></h2>
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
                <input type="text" class="editor-title-input" id="lessonTitle" placeholder="<?php echo get_phrase('lesson_title'); ?>" maxlength="100">
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
                <span class="shortcut-hint"><i class="fas fa-keyboard"></i> Ctrl+S <?php echo get_phrase('to_save'); ?></span>
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
                <span id="quizEditorTitle"><?php echo get_phrase('add_new_quiz'); ?></span>
            </div>
            
            <!-- Quiz Title Section (like lesson title) -->
            <div class="editor-title-section">
                <input type="text" class="editor-title-input" id="quizTitle" placeholder="<?php echo get_phrase('quiz_title'); ?>" maxlength="100" required>
                <span class="title-char-counter" id="quizTitleCounter">0/100</span>
            </div>
            
            <!-- Quiz Instruction Section -->
            <div class="editor-instruction-section">
                <textarea class="editor-instruction-input" id="quizInstruction" placeholder="<?php echo get_phrase('instructions_optional'); ?>"></textarea>
            </div>
            
            <!-- Quiz Form -->
            <div class="quiz-editor-form">
                <!-- Questions Builder -->
                <div class="quiz-questions-builder">
                    <div class="questions-header">
                        <label>
                            <i class="fas fa-list-check"></i>
                            <?php echo get_phrase('questions'); ?>
                        </label>
                        <button type="button" class="btn-add-question" onclick="addQuestion()">
                            <i class="fas fa-plus"></i> <?php echo get_phrase('add_question'); ?>
                        </button>
                    </div>
                    
                    <div id="questionsContainer">
                        <!-- Questions will be added here dynamically -->
                    </div>
                    
                    <div class="no-questions-message" id="noQuestionsMessage">
                        <i class="fas fa-clipboard-list"></i>
                        <p><?php echo get_phrase('no_questions_yet'); ?></p>
                        <button type="button" class="btn-add-first-question" onclick="addQuestion()">
                            <i class="fas fa-plus"></i> <?php echo get_phrase('add_first_question'); ?>
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
                <span class="shortcut-hint"><i class="fas fa-keyboard"></i> Ctrl+S <?php echo get_phrase('to_save'); ?></span>
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
                <h4 class="outline-title"><?php echo get_phrase('Outline'); ?></h4>
                <button type="button" class="btn-add-section" onclick="showAddSectionForm()">
                    <i class="fas fa-plus"></i> <?php echo get_phrase('add_section'); ?>
                </button>
            </div>
            
            <!-- Inline Add Section Form -->
            <div class="add-section-form" id="addSectionForm" style="display: none;">
                <div class="add-section-input-wrapper">
                    <i class="fas fa-folder add-section-icon"></i>
                    <input type="text" class="add-section-input" id="newSectionTitle" placeholder="<?php echo get_phrase('section_title'); ?>" onkeydown="handleAddSectionKeydown(event)">
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
                        <p><?php echo get_phrase('no_sections_yet'); ?></p>
                        <button type="button" class="btn-add-section" onclick="showAddSectionForm()">
                            <i class="fas fa-plus"></i> <?php echo get_phrase('add_first_section'); ?>
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
                                'title' => $lessons[0]['title'],
                                'type' => $lessons[0]['lesson_type']
                            ];
                        }
                    ?>
                    <div class="outline-section" id="outline-section-<?php echo $section['id']; ?>" data-section-id="<?php echo $section['id']; ?>">
                        <div class="section-header" onclick="toggleSection(<?php echo $section['id']; ?>)">
                            <div class="section-drag-handle" onclick="event.stopPropagation();" title="<?php echo get_phrase('drag_to_reorder'); ?>">
                                <i class="fas fa-grip-vertical"></i>
                            </div>
                            <div class="section-info">
                                <i class="fas fa-folder section-icon"></i>
                                <span class="section-label"><?php echo get_phrase('section'); ?> <?php echo $section_counter; ?>:</span>
                                <span class="section-name" id="section-name-<?php echo $section['id']; ?>" ondblclick="event.stopPropagation(); startEditSection(<?php echo $section['id']; ?>)"><?php echo html_escape($section['title']); ?></span>
                                <input type="text" class="section-name-input" id="section-input-<?php echo $section['id']; ?>" value="<?php echo htmlspecialchars($section['title']); ?>" style="display: none;" onkeydown="handleSectionEditKeydown(event, <?php echo $section['id']; ?>)" onblur="saveSectionName(<?php echo $section['id']; ?>)">
                            </div>
                            <div class="section-actions">
                                <button type="button" class="btn-icon" onclick="event.stopPropagation(); openMoveSection(<?php echo $section['id']; ?>, <?php echo $section_counter; ?>)" title="<?php echo get_phrase('move_to_position'); ?>">
                                    <i class="fas fa-up-down-left-right"></i>
                                </button>
                                <button type="button" class="btn-icon" onclick="event.stopPropagation(); startEditSection(<?php echo $section['id']; ?>)" title="<?php echo get_phrase('edit_section'); ?>">
                                    <i class="fas fa-pen-to-square"></i>
                                </button>
                                <button type="button" class="btn-icon" onclick="event.stopPropagation(); confirmDelete('<?php echo site_url('addons/courses/course_sections/'.$course['id'].'/delete/'.$section['id']); ?>')" title="<?php echo get_phrase('delete_section'); ?>">
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
                                <div class="lesson-drag-handle" title="<?php echo get_phrase('drag_to_reorder'); ?>">
                                    <i class="fas fa-grip-vertical"></i>
                                </div>
                                <div class="lesson-info" onclick="<?php echo $lesson['lesson_type'] == 'quiz' ? 'openQuizPreview('.$lesson['id'].', '.$section['id'].', \''.addslashes($lesson['title']).'\')' : 'openLessonPreview('.$lesson['id'].', '.$section['id'].', \''.addslashes($lesson['title']).'\')'; ?>">
                                    <i class="fas <?php echo $lesson['lesson_type'] == 'quiz' ? 'fa-circle-question' : 'fa-file-lines'; ?> lesson-icon"></i>
                                    <span class="lesson-label"><?php echo $lesson['lesson_type'] == 'quiz' ? get_phrase('quiz') : get_phrase('lesson'); ?> <?php echo $lesson_counter; ?>:</span>
                                    <span class="lesson-name"><?php echo html_escape($lesson['title']); ?></span>
                                </div>
                                <div class="lesson-actions">
                                    <button type="button" class="btn-icon btn-delete-lesson" onclick="event.stopPropagation(); confirmDelete('<?php echo site_url('addons/courses/lessons/'.$course['id'].'/delete/'.$lesson['id']); ?>')" title="<?php echo get_phrase('delete'); ?>">
                                        <i class="fas fa-trash-can"></i>
                                    </button>
                                </div>
                            </div>
                            <?php endforeach; ?>
                            
                            <!-- Add Lesson/Quiz buttons -->
                            <div class="add-lesson-buttons">
                                <button type="button" class="btn-add-lesson" onclick="openNewLessonEditor(<?php echo $section['id']; ?>)">
                                    <i class="fas fa-plus"></i> <?php echo get_phrase('add_lesson'); ?>
                                </button>
                                <button type="button" class="btn-add-lesson btn-add-quiz" onclick="openNewQuizEditor(<?php echo $section['id']; ?>)">
                                    <i class="fas fa-plus"></i> <?php echo get_phrase('add_quiz'); ?>
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                
                <!-- Loading spinner -->
                <div id="sectionsLoading" class="sections-loading" style="display: none;">
                    <i class="fas fa-spinner fa-spin"></i>
                    <span><?php echo get_phrase('loading'); ?>...</span>
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

<!-- Link Modal -->
<div class="link-modal" id="linkModal">
    <div class="link-modal-content">
        <div class="link-modal-header">
            <h5><?php echo get_phrase('insert_link'); ?></h5>
            <button type="button" class="close-modal" onclick="closeLinkModal()">&times;</button>
        </div>
        <div class="link-modal-body">
            <input type="url" id="linkUrl" class="link-input" placeholder="https://example.com">
        </div>
        <div class="link-modal-footer">
            <button type="button" class="btn-cancel" onclick="closeLinkModal()"><?php echo get_phrase('cancel'); ?></button>
            <button type="button" class="btn-insert" onclick="insertLink()"><?php echo get_phrase('insert'); ?></button>
        </div>
    </div>
</div>

<!-- Image Modal -->
<div class="link-modal" id="imageModal">
    <div class="link-modal-content">
        <div class="link-modal-header">
            <h5><?php echo get_phrase('insert_image'); ?></h5>
            <button type="button" class="close-modal" onclick="closeImageModal()">&times;</button>
        </div>
        <div class="link-modal-body">
            <input type="url" id="imageUrl" class="link-input" placeholder="https://example.com/image.jpg">
            <input type="text" id="imageAlt" class="link-input mt-2" placeholder="<?php echo get_phrase('alt_text'); ?>">
        </div>
        <div class="link-modal-footer">
            <button type="button" class="btn-cancel" onclick="closeImageModal()"><?php echo get_phrase('cancel'); ?></button>
            <button type="button" class="btn-insert" onclick="insertImage()"><?php echo get_phrase('insert'); ?></button>
        </div>
    </div>
</div>

<!-- Video Modal -->
<div class="link-modal" id="videoModal">
    <div class="link-modal-content">
        <div class="link-modal-header">
            <h5><i class="fas fa-video"></i> <?php echo get_phrase('insert_video'); ?></h5>
            <button type="button" class="close-modal" onclick="closeVideoModal()">&times;</button>
        </div>
        <div class="link-modal-body">
            <input type="url" id="videoUrl" class="link-input" placeholder="https://www.youtube.com/watch?v=...">
            <p class="video-platforms-hint"><?php echo get_phrase('supported_platforms'); ?> :</p>
            <p class="video-platforms-info">
                <span><i class="fab fa-youtube"></i> YouTube</span>
                <span><i class="fab fa-vimeo-v"></i> Vimeo</span>
                <span><i class="fas fa-video"></i> Loom</span>
                <span><i class="fas fa-circle-play"></i> Dailymotion</span>
                <span><i class="fas fa-film"></i> Wistia</span>
            </p>
        </div>
        <div class="link-modal-footer">
            <button type="button" class="btn-cancel" onclick="closeVideoModal()"><?php echo get_phrase('cancel'); ?></button>
            <button type="button" class="btn-insert" onclick="insertVideo()"><?php echo get_phrase('insert'); ?></button>
        </div>
    </div>
</div>

<!-- Move Section Modal -->
<div class="link-modal" id="moveSectionModal">
    <div class="link-modal-content">
        <div class="link-modal-header">
            <h5><?php echo get_phrase('move_section'); ?></h5>
            <button type="button" class="close-modal" onclick="closeMoveSectionModal()">&times;</button>
        </div>
        <div class="link-modal-body">
            <p class="move-section-info"><?php echo get_phrase('current_position'); ?>: <strong id="currentSectionPosition">1</strong> / <span id="totalSectionsCount"><?php echo $total_sections ?? 0; ?></span></p>
            <label for="targetPosition"><?php echo get_phrase('new_position'); ?>:</label>
            <input type="number" id="targetPosition" class="link-input" min="1" max="<?php echo $total_sections ?? 1; ?>" placeholder="1">
            <input type="hidden" id="moveSectionId" value="">
        </div>
        <div class="link-modal-footer">
            <button type="button" class="btn-cancel" onclick="closeMoveSectionModal()"><?php echo get_phrase('cancel'); ?></button>
            <button type="button" class="btn-insert" onclick="confirmMoveSection()"><?php echo get_phrase('move'); ?></button>
        </div>
    </div>
</div>

<!-- Unsaved Changes Modal -->
<div class="link-modal" id="unsavedChangesModal">
    <div class="link-modal-content">
        <div class="link-modal-header">
            <h5><i class="fas fa-triangle-exclamation text-warning"></i> <?php echo get_phrase('unsaved_changes'); ?></h5>
            <button type="button" class="close-modal" onclick="closeUnsavedModal()">&times;</button>
        </div>
        <div class="link-modal-body">
            <p><?php echo get_phrase('you_have_unsaved_changes'); ?></p>
        </div>
        <div class="link-modal-footer unsaved-modal-footer">
            <button type="button" class="btn-secondary" onclick="closeUnsavedModal()"><?php echo get_phrase('cancel'); ?></button>
            <button type="button" class="btn-insert btn-save-changes" onclick="saveAndProceed()"><?php echo get_phrase('save'); ?></button>
        </div>
    </div>
</div>

<!-- Offline Status Bar -->
<div class="offline-status-bar" id="offlineStatusBar">
    <i class="fas fa-circle-exclamation"></i>
    <span><?php echo get_phrase('offline_mode'); ?></span>
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
        'saving': '<?php echo addslashes(get_phrase('saving')); ?>...',
        'saved': '<?php echo addslashes(get_phrase('saved')); ?>',
        'error': '<?php echo addslashes(get_phrase('error')); ?>',
        'offline': '<?php echo addslashes(get_phrase('offline')); ?>',
        'dirty': '<?php echo addslashes(get_phrase('unsaved')); ?>'
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
                    toastr.success('<?php echo addslashes(get_phrase('offline_save_synced')); ?>');
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
                    toastr.success('<?php echo addslashes(get_phrase('offline_save_synced')); ?>');
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
            countEl.textContent = queue.length > 0 ? `(${queue.length} <?php echo addslashes(get_phrase('pending')); ?>)` : '';
        }
    },
    
    // Handle browser close/refresh
    handleBeforeUnload: function(e) {
        if (this.lessonDirty || this.quizDirty) {
            e.preventDefault();
            e.returnValue = '<?php echo addslashes(get_phrase('unsaved_changes_warning')); ?>';
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

// Register Image Resize module if available (true = overwrite silently)
if (window.ImageResize) {
    Quill.register('modules/imageResize', ImageResize.default || ImageResize, true);
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
                toastr.error('<?php echo addslashes(get_phrase('image_too_large')); ?> (Max: 5MB)');
                return;
            }
            
            const range = quill.getSelection(true);
            
            // Show uploading indicator
            toastr.info('<?php echo addslashes(get_phrase('uploading')); ?>...', '', { timeOut: 0, extendedTimeOut: 0 });
            
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
                            
                            toastr.info('<?php echo addslashes(get_phrase('uploading')); ?>...', '', { timeOut: 0, extendedTimeOut: 0 });
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
                                toastr.success('<?php echo addslashes(get_phrase('image_uploaded_successfully')); ?>');
                            } else {
                                toastr.error(retryData.message || '<?php echo addslashes(get_phrase('error_uploading_image')); ?>');
                            }
                            return;
                        }
                    } catch (csrfError) {
                        console.error('CSRF refresh failed:', csrfError);
                    }
                    toastr.error('<?php echo addslashes(get_phrase('session_expired')); ?>');
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
                    
                    toastr.success('<?php echo addslashes(get_phrase('image_uploaded_successfully')); ?>');
                } else {
                    toastr.error(data.message || '<?php echo addslashes(get_phrase('error_uploading_image')); ?>');
                }
            } catch (error) {
                toastr.clear();
                console.error('Upload error:', error);
                toastr.error('<?php echo addslashes(get_phrase('error_uploading_image')); ?>');
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
        toastr.error('<?php echo addslashes(get_phrase('please_enter_video_url')); ?>');
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
        toastr.error('<?php echo addslashes(get_phrase('unsupported_video_platform')); ?>');
        return;
    }
    
    const range = quill.getSelection(true);
    quill.insertEmbed(range.index, 'video', embedUrl);
    quill.setSelection(range.index + 1);
    
    // Update hidden input and trigger autosave
    document.getElementById('lessonContent').value = quill.root.innerHTML;
    AutoSaveManager.setLessonDirty(true);
    
    closeVideoModal();
    toastr.success('<?php echo addslashes(get_phrase('video_inserted')); ?>');
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
                    
                    toastr.success('<?php echo addslashes(get_phrase('file_uploaded_successfully')); ?>');
                } else {
                    toastr.error(data.message || '<?php echo addslashes(get_phrase('error_uploading_file')); ?>');
                }
            } catch (error) {
                toastr.error('<?php echo addslashes(get_phrase('error_uploading_file')); ?>');
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
    
    const modules = {
        toolbar: toolbarOptions,
        history: {
            delay: 1000,
            maxStack: 50,
            userOnly: true
        }
    };
    
    // Add image resize if available
    if (window.ImageResize) {
        modules.imageResize = {
            displayStyles: {
                backgroundColor: 'black',
                border: 'none',
                color: 'white'
            },
            modules: ['Resize', 'DisplaySize', 'Toolbar']
        };
    }
    
    quill = new Quill('#editor', {
        theme: 'snow',
        modules: modules,
        placeholder: '<?php echo addslashes(get_phrase('start_writing_your_content_here')); ?>...'
    });
    
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
                <p><?php echo addslashes(get_phrase('embed_domain_not_allowed')); ?></p>
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
            <a href="${url}" target="_blank" rel="noopener"><?php echo addslashes(get_phrase('watch_video')); ?>: ${url}</a>
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
    document.getElementById('previewType').innerHTML = '<i class="fas fa-file-lines"></i><span><?php echo addslashes(get_phrase('lesson')); ?></span>';
    document.getElementById('previewContent').innerHTML = '<p class="preview-loading"><i class="fas fa-spinner fa-spin"></i> <?php echo addslashes(get_phrase('loading')); ?>...</p>';
    
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
                document.getElementById('previewContent').innerHTML = '<p class="preview-empty"><i class="fas fa-file-lines"></i> <?php echo addslashes(get_phrase('no_content_yet')); ?></p>';
            }
        },
        error: function() {
            document.getElementById('previewContent').innerHTML = '<p class="preview-error"><i class="fas fa-triangle-exclamation"></i> <?php echo addslashes(get_phrase('error_loading_content')); ?></p>';
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
    document.getElementById('previewType').innerHTML = '<i class="fas fa-circle-question"></i><span><?php echo addslashes(get_phrase('quiz')); ?></span>';
    document.getElementById('previewContent').innerHTML = '<p class="preview-loading"><i class="fas fa-spinner fa-spin"></i> <?php echo addslashes(get_phrase('loading')); ?>...</p>';
    
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
                    html += '<div class="quiz-questions-header"><?php echo addslashes(get_phrase('questions')); ?> (' + response.questions.length + ')</div>';
                    
                    response.questions.forEach(function(q, index) {
                        html += '<div class="quiz-preview-question-card">';
                        html += '<div class="question-label"><?php echo addslashes(strtoupper(get_phrase('question'))); ?> ' + (index + 1) + '</div>';
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
                    html += '<p class="preview-empty"><i class="fas fa-circle-question"></i> <?php echo addslashes(get_phrase('no_questions_yet')); ?></p>';
                }
                
                document.getElementById('previewContent').innerHTML = html;
            } else {
                document.getElementById('previewContent').innerHTML = '<p class="preview-error"><i class="fas fa-triangle-exclamation"></i> <?php echo addslashes(get_phrase('error_loading_content')); ?></p>';
            }
        },
        error: function() {
            document.getElementById('previewContent').innerHTML = '<p class="preview-error"><i class="fas fa-triangle-exclamation"></i> <?php echo addslashes(get_phrase('error_loading_content')); ?></p>';
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
    document.getElementById('lessonTitle').value = '<?php echo addslashes(get_phrase('loading')); ?>...';
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
    document.getElementById('lesson-item-' + lessonId).classList.add('active');

    // Initialize editor if needed
    if (!editorInitialized) {
        initEditor();
    }

    // Load lesson content (will set fresh title from server)
    loadLessonContent(lessonId);
}

// Load lesson content from server
function loadLessonContent(lessonId) {
    if (!quill) {
        console.error('Editor not initialized');
        return;
    }

    // Prevent dirty tracking during content load
    AutoSaveManager.isLoadingContent = true;

    // Show loading state
    quill.root.innerHTML = '<p><em><?php echo addslashes(get_phrase('loading')); ?>...</em></p>';

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
                quill.root.innerHTML = response.content;
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
    document.getElementById('quizEditorTitle').textContent = '<?php echo addslashes(get_phrase('add_new_quiz')); ?>';
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
    document.getElementById('quizEditorTitle').textContent = '<?php echo addslashes(get_phrase('edit_quiz')); ?>';
    
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
    document.getElementById('quizTitle').value = '<?php echo addslashes(get_phrase('loading')); ?>...';
    document.getElementById('quizInstruction').value = '';
    document.getElementById('currentQuizId').value = quizId;
    document.getElementById('currentQuizSectionId').value = sectionId;
    document.getElementById('quizEditorTitle').textContent = '<?php echo addslashes(get_phrase('edit_quiz')); ?>';
    
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
                toastr.error('<?php echo addslashes(get_phrase('error_loading_quiz')); ?>');
                document.getElementById('quizTitle').value = '';
            }
        },
        error: function() {
            AutoSaveManager.isLoadingContent = false;
            AutoSaveManager.updateQuizStatus('');
            toastr.error('<?php echo addslashes(get_phrase('error_loading_quiz')); ?>');
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
                <span class="question-number"><?php echo addslashes(get_phrase('question')); ?> ${currentQuestionCount}</span>
                <button type="button" class="btn-remove-question" onclick="removeQuestion('${questionId}')" title="<?php echo addslashes(get_phrase('remove_question')); ?>">
                    <i class="fas fa-trash-can"></i>
                </button>
            </div>
            <div class="question-card-body">
                <div class="question-input-group">
                    <input type="text" class="question-input" placeholder="<?php echo addslashes(get_phrase('enter_your_question')); ?>" value="${questionData ? escapeHtmlAttr(questionData.question) : ''}">
                </div>
                <div class="answers-section">
                    <div class="answers-header">
                        <span><i class="fas fa-list-ul"></i> <?php echo addslashes(get_phrase('answers')); ?></span>
                        <small class="answers-hint"><?php echo addslashes(get_phrase('check_correct_answers')); ?></small>
                    </div>
                    <div class="answers-container" id="answers_${questionId}">
                        <!-- Answers will be added here -->
                    </div>
                    <button type="button" class="btn-add-answer" onclick="addAnswer('${questionId}')">
                        <i class="fas fa-plus"></i> <?php echo addslashes(get_phrase('add_answer')); ?>
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
            <input type="text" class="answer-input" placeholder="<?php echo addslashes(get_phrase('enter_answer')); ?>" value="${escapeHtmlAttr(answerText)}">
            <button type="button" class="btn-remove-answer" onclick="removeAnswer('${answerId}', '${questionId}')" title="<?php echo addslashes(get_phrase('remove_answer')); ?>">
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
        toastr.warning('<?php echo addslashes(get_phrase('minimum_two_answers_required')); ?>');
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
        q.querySelector('.question-number').textContent = '<?php echo addslashes(get_phrase('question')); ?> ' + (index + 1);
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
            toastr.error('<?php echo addslashes(get_phrase('please_enter_quiz_title')); ?>');
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
            toastr.info('<?php echo addslashes(get_phrase('saved_offline')); ?>');
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
                    document.getElementById('quizEditorTitle').textContent = '<?php echo addslashes(get_phrase('edit_quiz')); ?>';
                    
                    // Mark the new quiz as active
                    document.querySelectorAll('.lesson-item').forEach(item => item.classList.remove('active'));
                    const newItem = document.getElementById('lesson-item-' + newQuizId);
                    if (newItem) newItem.classList.add('active');
                    
                    toastr.success('<?php echo addslashes(get_phrase('quiz_saved_successfully')); ?>');
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
                        toastr.success('<?php echo addslashes(get_phrase('quiz_saved_successfully')); ?>');
                    }
                }
                
                // Execute callback if provided
                if (callback) callback();
                
            } else {
                AutoSaveManager.updateQuizStatus('error');
                toastr.error(response.message || '<?php echo addslashes(get_phrase('error_saving_quiz')); ?>');
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
                toastr.error('<?php echo addslashes(get_phrase('error_saving_quiz')); ?>');
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
            toastr.error('<?php echo addslashes(get_phrase('please_enter_lesson_title')); ?>');
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
            toastr.info('<?php echo addslashes(get_phrase('saved_offline')); ?>');
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
                    document.getElementById('lessonEditorTitle').textContent = '<?php echo addslashes(get_phrase('edit_lesson')); ?>';
                    
                    // Mark the new lesson as active
                    document.querySelectorAll('.lesson-item').forEach(item => item.classList.remove('active'));
                    const newItem = document.getElementById('lesson-item-' + newLessonId);
                    if (newItem) newItem.classList.add('active');
                    
                    toastr.success('<?php echo addslashes(get_phrase('lesson_saved_successfully')); ?>');
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
                        toastr.success('<?php echo addslashes(get_phrase('lesson_saved_successfully')); ?>');
                    }
                }
                
                // Execute callback if provided
                if (callback) callback();
                
            } else {
                AutoSaveManager.updateLessonStatus('error');
                toastr.error(response.message || '<?php echo addslashes(get_phrase('error_saving_lesson')); ?>');
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
                toastr.error('<?php echo addslashes(get_phrase('error_saving_lesson')); ?>');
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
        title: '<?php echo addslashes(get_phrase('are_you_sure')); ?>',
        text: '<?php echo addslashes(get_phrase('you_wont_be_able_to_revert_this')); ?>',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: '<?php echo addslashes(get_phrase('yes_delete_it')); ?>',
        cancelButtonText: '<?php echo addslashes(get_phrase('cancel')); ?>',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: url,
                type: 'GET',
                success: function(response) {
                    Swal.fire({
                        title: '<?php echo addslashes(get_phrase('deleted')); ?>',
                        text: '<?php echo addslashes(get_phrase('item_has_been_deleted')); ?>',
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
                        title: '<?php echo addslashes(get_phrase('error')); ?>',
                        text: '<?php echo addslashes(get_phrase('something_went_wrong')); ?>',
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
            toastr.error('<?php echo addslashes(get_phrase('error_loading_sections')); ?>');
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
        const lessonLabel = isQuiz ? '<?php echo addslashes(get_phrase('quiz')); ?>' : '<?php echo addslashes(get_phrase('lesson')); ?>';
        
        const clickHandler = isQuiz ? `openQuizPreview(${lesson.id}, ${section.id}, '${escapeHtml(lesson.title)}')` : `openLessonPreview(${lesson.id}, ${section.id}, '${escapeHtml(lesson.title)}')`;
        
        lessonsHtml += `
            <div class="lesson-item ${isQuiz ? 'quiz-item' : ''}" id="lesson-item-${lesson.id}" data-lesson-id="${lesson.id}">
                <div class="lesson-drag-handle" title="<?php echo addslashes(get_phrase('drag_to_reorder')); ?>">
                    <i class="fas fa-grip-vertical"></i>
                </div>
                <div class="lesson-info" onclick="${clickHandler}">
                    <i class="fas ${lessonIcon} lesson-icon"></i>
                    <span class="lesson-label">${lessonLabel} ${lessonNumber}:</span>
                    <span class="lesson-name">${escapeHtml(lesson.title)}</span>
                </div>
                <div class="lesson-actions">
                    <button type="button" class="btn-icon btn-delete-lesson" onclick="event.stopPropagation(); confirmDelete('<?php echo site_url('addons/courses/lessons/'.$course['id'].'/delete/'); ?>${lesson.id}')" title="<?php echo addslashes(get_phrase('delete')); ?>">
                        <i class="fas fa-trash-can"></i>
                    </button>
                </div>
            </div>
        `;
    });
    
    return `
        <div class="outline-section" id="outline-section-${section.id}" data-section-id="${section.id}">
            <div class="section-header" onclick="toggleSection(${section.id})">
                <div class="section-drag-handle" onclick="event.stopPropagation();" title="<?php echo addslashes(get_phrase('drag_to_reorder')); ?>">
                    <i class="fas fa-grip-vertical"></i>
                </div>
                <div class="section-info">
                    <i class="fas fa-folder section-icon"></i>
                    <span class="section-label"><?php echo addslashes(get_phrase('section')); ?> ${sectionNumber}:</span>
                    <span class="section-name" id="section-name-${section.id}" ondblclick="event.stopPropagation(); startEditSection(${section.id})">${escapeHtml(section.title)}</span>
                    <input type="text" class="section-name-input" id="section-input-${section.id}" value="${escapeHtmlAttr(section.title)}" style="display: none;" onkeydown="handleSectionEditKeydown(event, ${section.id})" onblur="saveSectionName(${section.id})">
                </div>
                <div class="section-actions">
                    <button type="button" class="btn-icon" onclick="event.stopPropagation(); openMoveSection(${section.id}, ${sectionNumber})" title="<?php echo addslashes(get_phrase('move_to_position')); ?>">
                        <i class="fas fa-up-down-left-right"></i>
                    </button>
                    <button type="button" class="btn-icon" onclick="event.stopPropagation(); startEditSection(${section.id})" title="<?php echo addslashes(get_phrase('edit_section')); ?>">
                        <i class="fas fa-pen-to-square"></i>
                    </button>
                    <button type="button" class="btn-icon" onclick="event.stopPropagation(); confirmDelete('<?php echo site_url('addons/courses/course_sections/'.$course['id'].'/delete/'); ?>${section.id}')" title="<?php echo addslashes(get_phrase('delete_section')); ?>">
                        <i class="fas fa-trash-can"></i>
                    </button>
                    <i class="fas fa-chevron-right section-toggle" id="toggle-${section.id}"></i>
                </div>
            </div>
            
            <div class="section-lessons collapsed" id="lessons-${section.id}">
                ${lessonsHtml}
                
                <div class="add-lesson-buttons">
                    <button type="button" class="btn-add-lesson" onclick="openNewLessonEditor(${section.id})">
                        <i class="fas fa-plus"></i> <?php echo addslashes(get_phrase('add_lesson')); ?>
                    </button>
                    <button type="button" class="btn-add-lesson btn-add-quiz" onclick="openNewQuizEditor(${section.id})">
                        <i class="fas fa-plus"></i> <?php echo addslashes(get_phrase('add_quiz')); ?>
                    </button>
                </div>
            </div>
        </div>
    `;
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML.replace(/'/g, "\\'");
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
        const itemLabel = isQuiz ? '<?php echo addslashes(get_phrase('quiz')); ?>' : '<?php echo addslashes(get_phrase('lesson')); ?>';
    const clickHandler = isQuiz 
        ? `openQuizPreview(${itemId}, ${sectionId}, '${escapeHtml(title)}')`
        : `openLessonPreview(${itemId}, ${sectionId}, '${escapeHtml(title)}')`;
    
    const itemHtml = `
        <div class="${itemClass}" id="lesson-item-${itemId}" data-lesson-id="${itemId}">
            <div class="lesson-drag-handle" title="<?php echo addslashes(get_phrase('drag_to_reorder')); ?>">
                <i class="fas fa-grip-vertical"></i>
            </div>
            <div class="lesson-info" onclick="${clickHandler}">
                <i class="fas ${itemIcon} lesson-icon"></i>
                <span class="lesson-label">${itemLabel} ${itemNumber}:</span>
                <span class="lesson-name">${escapeHtml(title).replace(/\\'/g, "'")}</span>
            </div>
            <div class="lesson-actions">
                <button type="button" class="btn-icon btn-delete-lesson" onclick="event.stopPropagation(); confirmDelete('<?php echo site_url('addons/courses/lessons/'.$course['id'].'/delete/'); ?>${itemId}')" title="<?php echo addslashes(get_phrase('delete')); ?>">
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
        toastr.error('<?php echo addslashes(get_phrase('please_enter_section_title')); ?>');
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
                toastr.success('<?php echo addslashes(get_phrase('section_added_successfully')); ?>');
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
                toastr.error(response.message || '<?php echo addslashes(get_phrase('error_adding_section')); ?>');
            }
        },
        error: function() {
            toastr.error('<?php echo addslashes(get_phrase('error_adding_section')); ?>');
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
        toastr.error('<?php echo addslashes(get_phrase('invalid_position')); ?>');
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
                toastr.success('<?php echo addslashes(get_phrase('section_moved_successfully')); ?>');
                closeMoveSectionModal();
                // Calculate target page and reload
                const targetPage = Math.ceil(targetPosition / sectionsPerPage);
                loadPage(targetPage, true);
            } else {
                toastr.error(response.message || '<?php echo addslashes(get_phrase('error_moving_section')); ?>');
            }
        },
        error: function() {
            toastr.error('<?php echo addslashes(get_phrase('error_moving_section')); ?>');
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
                toastr.success('<?php echo addslashes(get_phrase('section_updated_successfully')); ?>');
            } else {
                toastr.error(response.message || '<?php echo addslashes(get_phrase('error_updating_section')); ?>');
                input.value = oldTitle;
            }
        },
        error: function() {
            toastr.error('<?php echo addslashes(get_phrase('error_updating_section')); ?>');
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
        { selector: 'button.ql-undo', title: '<?php echo addslashes(get_phrase('undo')); ?> (Ctrl+Z)' },
        { selector: 'button.ql-redo', title: '<?php echo addslashes(get_phrase('redo')); ?> (Ctrl+Y)' },
        { selector: 'button.ql-bold', title: '<?php echo addslashes(get_phrase('bold')); ?> (Ctrl+B)' },
        { selector: 'button.ql-italic', title: '<?php echo addslashes(get_phrase('italic')); ?> (Ctrl+I)' },
        { selector: 'button.ql-underline', title: '<?php echo addslashes(get_phrase('underline')); ?> (Ctrl+U)' },
        { selector: 'button.ql-strike', title: '<?php echo addslashes(get_phrase('strike')); ?>' },
        { selector: '.ql-header .ql-picker-label', title: '<?php echo addslashes(get_phrase('headings')); ?>' },
        { selector: '.ql-size .ql-picker-label', title: '<?php echo addslashes(get_phrase('font_size')); ?>' },
        { selector: '.ql-color .ql-picker-label', title: '<?php echo addslashes(get_phrase('text_color')); ?>' },
        { selector: '.ql-background .ql-picker-label', title: '<?php echo addslashes(get_phrase('background_color')); ?>' },
        
        // Listes numérotées et puces
        { selector: 'button.ql-list[value="ordered"]', title: '<?php echo addslashes(get_phrase('ordered_list')); ?>' },
        { selector: 'button.ql-list[value="bullet"]', title: '<?php echo addslashes(get_phrase('bullet_list')); ?>' },
        
        // Indentation
        { selector: 'button.ql-indent[value="+1"]', title: '<?php echo addslashes(get_phrase('indent')); ?>' },
        { selector: 'button.ql-indent[value="-1"]', title: '<?php echo addslashes(get_phrase('outdent')); ?>' },
        
        // Alignement (le picker global et chaque bouton)
        { selector: '.ql-align .ql-picker-label', title: '<?php echo addslashes(get_phrase('text_alignment')); ?>' },
        { selector: 'button.ql-align[value=""]', title: '<?php echo addslashes(get_phrase('align_left')); ?>' },
        { selector: 'button.ql-align[value="center"]', title: '<?php echo addslashes(get_phrase('align_center')); ?>' },
        { selector: 'button.ql-align[value="right"]', title: '<?php echo addslashes(get_phrase('align_right')); ?>' },
        { selector: 'button.ql-align[value="justify"]', title: '<?php echo addslashes(get_phrase('align_justify')); ?>' },
        
        // Autres
        { selector: 'button.ql-blockquote', title: '<?php echo addslashes(get_phrase('blockquote')); ?>' },
        { selector: 'button.ql-code-block', title: '<?php echo addslashes(get_phrase('code_block')); ?>' },
        { selector: 'button.ql-link', title: '<?php echo addslashes(get_phrase('insert_link')); ?> (Ctrl+K)' },
        { selector: 'button.ql-image', title: '<?php echo addslashes(get_phrase('insert_image')); ?> (max 5MB)' },
        { selector: 'button.ql-video', title: '<?php echo addslashes(get_phrase('insert_video')); ?>' },
        { selector: 'button.ql-attachment', title: '<?php echo addslashes(get_phrase('attach_file')); ?>' }, // ton bouton personnalisé
        { selector: 'button.ql-clean', title: '<?php echo addslashes(get_phrase('clear_formatting')); ?>' }
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
const originalInitEditor = initEditor;
initEditor = function() {
    originalInitEditor();
    // Attendre que la toolbar soit complètement rendue
    setTimeout(addTooltipsToQuillToolbar, 300);
};

// Si l'éditeur est déjà initialisé (cas où on ouvre une leçon existante rapidement)
if (editorInitialized && quill) {
    setTimeout(addTooltipsToQuillToolbar, 300);
}

// Optionnel : réappliquer les tooltips quand on ouvre une nouvelle leçon (au cas où)
const originalOpenNewLessonEditor = openNewLessonEditor;
openNewLessonEditor = function(sectionId) {
    originalOpenNewLessonEditor(sectionId);
    setTimeout(addTooltipsToQuillToolbar, 400);
};

const originalOpenLessonEditor = openLessonEditor;
openLessonEditor = function(lessonId, sectionId, title) {
    originalOpenLessonEditor(lessonId, sectionId, title);
    setTimeout(addTooltipsToQuillToolbar, 400);
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
            toastr.success('<?php echo addslashes(get_phrase('order_saved')); ?>');
        },
        error: function(xhr, status, error) {
            console.error('Sort error:', status, error, xhr.responseText);
            toastr.error('<?php echo addslashes(get_phrase('error_saving_order')); ?>');
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
            toastr.success('<?php echo addslashes(get_phrase('order_saved')); ?>');
        },
        error: function() {
            toastr.error('<?php echo addslashes(get_phrase('error_saving_order')); ?>');
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
            label.textContent = '<?php echo addslashes(get_phrase('section')); ?> ' + (currentStartNumber + index) + ':';
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
            const prefix = isQuiz ? '<?php echo addslashes(get_phrase('quiz')); ?>' : '<?php echo addslashes(get_phrase('lesson')); ?>';
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
</script>
