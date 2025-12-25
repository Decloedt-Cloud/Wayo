<link rel="stylesheet" href="<?php echo base_url(); ?>assets/backend/css/manageQuizQuestions.css">
<style>
    /* Bouton Wayo AI avec dégradé orange */
    .btn-wayo-ai {
        background: linear-gradient(90deg, #f47a1f 0%, #fbb040 100%);
        border: 1px solid transparent;
        color: #fff;
        font-weight: 600;
        box-shadow: 0 4px 15px rgba(244, 122, 31, 0.3);
        transition: all 0.3s ease;
        border-radius: 0.5rem;
    }

    .btn-wayo-ai:hover {
        background: linear-gradient(90deg, #e06a15 0%, #f0a030 100%);
        color: #fff;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(244, 122, 31, 0.4);
    }

    .btn-wayo-ai:focus,
    .btn-wayo-ai:active {
        background: linear-gradient(90deg, #d85a05 0%, #e09020 100%);
        color: #fff;
        box-shadow: 0 4px 15px rgba(244, 122, 31, 0.5);
    }

    .btn-wayo-ai i {
        animation: sparkle-btn 2s ease-in-out infinite;
    }

    @keyframes sparkle-btn {
        0%, 100% {
            text-shadow: 0 0 4px rgba(255, 255, 255, 0.4);
        }
        50% {
            text-shadow: 0 0 8px rgba(255, 255, 255, 0.8), 0 0 12px rgba(255, 215, 0, 0.6);
        }
    }
</style>
<?php
// $param1 is Exam id
$exam_details = $this->lms_model->get_exams('exam', $param1)->row_array();
$questions = $this->lms_model->get_exam_questions($param1)->result_array();
$entityFlags = defined('ENT_HTML5') ? ENT_QUOTES | ENT_HTML5 : ENT_QUOTES;
?>

<input type="hidden" id="csrf_token" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>" />

<?php if (count($exam_details)): ?>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="row" data-plugin="dragula" data-containers='["question-list"]'>
                        <div class="col-md-12">
                            <div class="bg-dragula p-2 p-lg-4">
                                <h5 class="mt-0 d-flex align-items-center flex-wrap gap-2">
                                    <?php
                                        $safeExamName = htmlspecialchars(
                                            html_entity_decode($exam_details['name'], $entityFlags, 'UTF-8'),
                                            $entityFlags,
                                            'UTF-8'
                                        );
                                        $existingQuestionsCount = count($questions);
                                        echo get_phrase('questions_of') . ': ' . $safeExamName;
                                    ?>
                                    <span class="existing-questions-badge">
                                        <i class="fas fa-layer-group"></i>
                                        <span id="existingQuestionsCount"><?php echo $existingQuestionsCount; ?></span>
                                        <?php echo get_phrase('questions'); ?>
                                    </span>
                                    <div class="d-inline-flex quiz-action-buttons ms-2">
                                        <button type="button" class="btn btn-outline-primary btn-rounded btn-sm" id="question-sort-btn" onclick="sort()" name="button">
                                            <i class="fa-solid fa-sort"></i> <?php echo get_phrase('update_sorting'); ?>
                                        </button>
                                        <button type="button" class="btn btn-outline-primary btn-rounded btn-sm ms-1" onclick="showAjaxModal('<?php echo site_url('modal/popup/academy/exam_question_add/' . $param1) ?>', '<?php echo get_phrase('add_new_question'); ?>')" name="button" data-dismiss="modal">
                                            <i class="fa-solid fa-plus"></i> <?php echo get_phrase('add_new_question'); ?>
                                        </button>
                                        <button type="button" class="btn btn-outline-primary btn-rounded btn-sm ms-1" onclick="largeModal('<?php echo site_url('modal/popup/academy/exam_questions/' . $param1); ?>', '<?php echo get_phrase('manage_exam_questions'); ?>')" name="button" data-dismiss="modal">
                                            <i class="fa-solid fa-arrow-rotate-right"></i> <?php echo get_phrase('refresh'); ?>
                                        </button>
                                        <button type="button" class="btn btn-wayo-ai btn-rounded btn-sm ms-1" onclick="generateQuestionsFromPdf(<?php echo $param1; ?>)">
                                            <i class="fas fa-wand-magic-sparkles"></i>
                                            <?php echo get_phrase('create_questions_from_pdf'); ?>
                                        </button>
                                    </div>
                                </h5>
                                <div id="question-list" class="py-2">
                                    <?php foreach ($questions as $question): ?>
                                        <!-- Item -->
                                        <div class="card mb-0 mt-2 draggable-item on-hover-action" id="<?php echo $question['id']; ?>">
                                            <div class="card-body">
                                                <div class="media">
                                                    <div class="media-body">
                                                        <h5 class="mb-1 mt-0">
                                                            <?php
                                                                echo htmlspecialchars(
                                                                    html_entity_decode($question['title'], $entityFlags, 'UTF-8'),
                                                                    $entityFlags,
                                                                    'UTF-8'
                                                                );
                                                            ?>
                                                            <span id="<?php echo 'widgets-of-' . $question['id']; ?>" class="widgets-of-quiz-question">
                                                                <a href="javascript:void(0)" class="alignToTitle float-end ms-1 text-secondary" onclick="deleteExamQuestionAndReloadModal('<?php echo $param1; ?>', '<?php echo $question['id']; ?>')" data-dismiss="modal"><i class="dripicons-cross"></i></a>
                                                                <a href="javascript:void(0)" class="alignToTitle float-end text-secondary" onclick="showAjaxModal('<?php echo site_url('modal/popup/academy/exam_question_edit/' . $question['id'] . '/' . $param1); ?>', '<?php echo get_phrase('update_exam_question'); ?>')" data-dismiss="modal"><i class="dripicons-document-edit"></i></a>
                                                            </span>
                                                        </h5>
                                                    </div> <!-- end media-body -->
                                                </div> <!-- end media -->
                                            </div> <!-- end card-body -->
                                        </div> <!-- end col -->
                                        <!-- item -->
                                    <?php endforeach; ?>
                                </div> <!-- end question-list -->
                            </div> <!-- end div.bg-light -->
                        </div> <!-- end col -->
                    </div> <!-- end row -->
                </div> <!-- end card-body -->
            </div> <!-- end card -->
        </div> <!-- end col -->
    </div>
<?php else: ?>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4><?php echo get_phrase('no_exam_found'); ?></h4>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- Modal to generate questions from PDF -->
<div class="modal fade" id="pdfQuestionModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content pdf-modal">
            <div class="modal-header pdf-modal__header">
                <div>
                    <h5 class="modal-title">
                        <?php echo get_phrase('generate_questions_from_pdf'); ?>
                    </h5>
                    <p class="pdf-modal__subtitle">
                        <?php echo get_phrase('upload_pdf_file'); ?> · <?php echo get_phrase('number_of_questions_to_generate'); ?>
                    </p>
                </div>
                <button type="button" class="modern-close" onclick="closePdfQuestionModal()" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body pdf-modal__body">
                <div class="row pdf-modal__grid">
                    <div class="col-lg-5 col-12">
                        <div class="pdf-info-panel">
                            <div class="pdf-info-panel__badge">
                                <i class="fas fa-wand-magic-sparkles me-2"></i><?php echo get_phrase('create_questions_from_pdf'); ?>
                            </div>
                            <h4><?php echo get_phrase('create_quizzes_in_seconds'); ?></h4>
                            <p><?php echo get_phrase('ai_pdf_description'); ?></p>
                            <ul class="pdf-info-panel__list">
                                <li><i class="fas fa-check-circle"></i> <?php echo get_phrase('intelligent_text_extraction'); ?></li>
                                <li><i class="fas fa-check-circle"></i> <?php echo get_phrase('balanced_choices_description'); ?></li>
                                <li><i class="fas fa-check-circle"></i> <?php echo get_phrase('auto_exam_ordering'); ?></li>
                            </ul>
                            <div class="pdf-info-panel__tip">
                                <i class="fas fa-lightbulb-on"></i>
                                <div>
                                    <strong><?php echo get_phrase('pro_tip'); ?>:</strong> <?php echo get_phrase('For_best_results,_use_structured_PDFs_(syllabus,_handouts,_course_materials).'); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-7 col-12">
                        <div class="pdf-upload-card">
                            <div class="pdf-section">
                                <label class="pdf-section__label">
                                    <i class="fas fa-file-pdf text-danger"></i>
                                    <?php echo get_phrase('upload_pdf_file'); ?>
                                </label>
                                <div class="file-upload-wrapper" id="fileUploadWrapper">
                                    <div class="file-upload-area" id="fileUploadArea">
                                        <div class="file-upload-content">
                                            <div class="file-upload-illustration">
                                                <span></span>
                                                <span></span>
                                            </div>
                                            <h6><?php echo get_phrase('click_to_select_pdf'); ?></h6>
                        <p><?php echo get_phrase('drag_and_drop_hint'); ?></p>
                                            <small class="text-muted d-block mt-1"><?php echo get_phrase('file_too_large_max_3mb'); ?></small>
                                        </div>
                                        <div class="file-selected-content" style="display: none;">
                                            <i class="fas fa-file-pdf"></i>
                                            <p id="fileName"></p>
                                            <small id="fileSize"></small>
                                            <button type="button" class="btn btn-sm btn-outline-danger mt-2" onclick="removeFile()">
                                                <i class="fas fa-times"></i> <?php echo get_phrase('remove'); ?>
                                            </button>
                                        </div>
                                    </div>
                                    <input type="file" class="form-control d-none" id="pdf_file_input" accept=".pdf" required>
                                </div>
                            </div>

                            <div class="pdf-section">
                                <label class="pdf-section__label">
                                    <i class="fas fa-list-ol"></i>
                                    <?php echo get_phrase('number_of_questions_to_generate'); ?>
                                </label>
                                <div class="question-slider-container">
                                    <div class="question-slider-display">
                                        <span class="question-slider-value" id="sliderValue">10</span>
                                        <span class="question-slider-label"><?php echo get_phrase('questions'); ?></span>
                                    </div>
                                    <div class="question-slider-wrapper">
                                        <span class="slider-min">5</span>
                                        <input type="range" min="5" max="50" value="10" class="question-slider" id="questionSlider">
                                        <span class="slider-max">50</span>
                                    </div>
                                    <div class="slider-presets">
                                        <button type="button" class="preset-btn" data-value="5">5</button>
                                        <button type="button" class="preset-btn active" data-value="10">10</button>
                                        <button type="button" class="preset-btn" data-value="20">20</button>
                                        <button type="button" class="preset-btn" data-value="30">30</button>
                                        <button type="button" class="preset-btn" data-value="50">50</button>
                                    </div>
                                </div>
                                <input type="hidden" id="number_of_questions" value="10">
                            </div>

                            <div class="pdf-section">
                                <label class="pdf-section__label">
                                    <i class="fas fa-signal"></i>
                                    <?php echo get_phrase('difficulty_level'); ?>
                                </label>
                                <div class="difficulty-selector">
                                    <label class="difficulty-option" data-level="easy">
                                        <input type="radio" name="difficulty_level" value="easy" style="display: none;">
                                        <div class="difficulty-badge easy">
                                            <i class="fas fa-leaf"></i>
                                        </div>
                                        <span class="difficulty-name"><?php echo get_phrase('easy'); ?></span>
                                        <span class="difficulty-bar"><span class="bar-fill"></span></span>
                                    </label>
                                    <label class="difficulty-option" data-level="medium">
                                        <input type="radio" name="difficulty_level" value="medium" checked style="display: none;">
                                        <div class="difficulty-badge medium">
                                            <i class="fas fa-balance-scale"></i>
                                        </div>
                                        <span class="difficulty-name"><?php echo get_phrase('medium'); ?></span>
                                        <span class="difficulty-bar"><span class="bar-fill"></span><span class="bar-fill"></span></span>
                                    </label>
                                    <label class="difficulty-option active" data-level="hard">
                                        <input type="radio" name="difficulty_level" value="hard" style="display: none;">
                                        <div class="difficulty-badge hard">
                                            <i class="fas fa-fire-flame-curved"></i>
                                        </div>
                                        <span class="difficulty-name"><?php echo get_phrase('hard'); ?></span>
                                        <span class="difficulty-bar"><span class="bar-fill"></span><span class="bar-fill"></span><span class="bar-fill"></span></span>
                                    </label>
                                    <label class="difficulty-option" data-level="mixed">
                                        <input type="radio" name="difficulty_level" value="mixed" style="display: none;">
                                        <div class="difficulty-badge mixed">
                                            <i class="fas fa-shuffle"></i>
                                        </div>
                                        <span class="difficulty-name"><?php echo get_phrase('mixed'); ?></span>
                                        <span class="difficulty-bar"><span class="bar-fill gradient"></span><span class="bar-fill gradient"></span></span>
                                    </label>
                                </div>
                                <input type="hidden" id="difficulty_level" value="medium">
                            </div>

                            <div class="pdf-section">
                                <label class="pdf-section__label">
                                    <i class="fas fa-eraser text-danger"></i>
                                    <?php echo get_phrase('existing_questions'); ?>
                                </label>
                                <div class="modern-toggle-wrapper">
                                    <input type="checkbox" id="overwriteExistingQuestions" class="modern-toggle-input">
                                    <label for="overwriteExistingQuestions" class="modern-toggle-label">
                                        <span class="modern-toggle-button"></span>
                                        <span class="modern-toggle-text"><?php echo get_phrase('overwrite_existing_questions'); ?></span>
                                    </label>
                                </div>
                            </div>

                            <div class="pdf-section" id="progressContainer" style="display: none;">
                                <label class="pdf-section__label">
                                    <i class="fas fa-waveform-path text-info"></i>
                                    <?php echo get_phrase('progress'); ?>
                                </label>
                                <div class="pdf-progress-block">
                                    <!-- Compteur en temps réel -->
                                    <div class="question-counter">
                                        <div class="counter-circle">
                                            <span class="counter-current" id="questionsGenerated">0</span>
                                            <span class="counter-separator">/</span>
                                            <span class="counter-total" id="questionsTotal">10</span>
                                        </div>
                                        <span class="counter-label"><?php echo get_phrase('questions_generated'); ?></span>
                                    </div>

                                    <!-- Étapes détaillées -->
                                    <div class="progress-steps">
                                        <div class="progress-step" id="step1" data-step="1">
                                            <div class="step-indicator">
                                                <div class="step-icon">
                                                    <i class="fas fa-file-import"></i>
                                                </div>
                                                <div class="step-line"></div>
                                            </div>
                                            <div class="step-content">
                                                <span class="step-number-badge">1</span>
                                                <span class="step-text"><?php echo get_phrase('extracting_text'); ?></span>
                                            </div>
                                        </div>
                                        <div class="progress-step" id="step2" data-step="2">
                                            <div class="step-indicator">
                                                <div class="step-icon">
                                                    <i class="fas fa-brain"></i>
                                                </div>
                                                <div class="step-line"></div>
                                            </div>
                                            <div class="step-content">
                                                <span class="step-number-badge">2</span>
                                                <span class="step-text"><?php echo get_phrase('analyzing_content'); ?></span>
                                            </div>
                                        </div>
                                        <div class="progress-step" id="step3" data-step="3">
                                            <div class="step-indicator">
                                                <div class="step-icon">
                                                    <i class="fas fa-wand-magic-sparkles"></i>
                                                </div>
                                                <div class="step-line"></div>
                                            </div>
                                            <div class="step-content">
                                                <span class="step-number-badge">3</span>
                                                <span class="step-text"><?php echo get_phrase('generating_questions'); ?></span>
                                            </div>
                                        </div>
                                        <div class="progress-step" id="step4" data-step="4">
                                            <div class="step-indicator">
                                                <div class="step-icon">
                                                    <i class="fas fa-check-double"></i>
                                                </div>
                                            </div>
                                            <div class="step-content">
                                                <span class="step-number-badge">4</span>
                                                <span class="step-text"><?php echo get_phrase('validating_answers'); ?></span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Barre de progression globale -->
                                    <div class="neo-progress">
                                        <div class="neo-progress__track">
                                            <div id="progressBar" class="neo-progress__bar"></div>
                                        </div>
                                    </div>
                                    <div class="progress-percentage">
                                        <i class="fas fa-percentage"></i>
                                        <strong id="progressPercent">0%</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer pdf-modal__footer">
                <div class="pdf-footer-copy">
                    <i class="fas fa-circle-nodes"></i>
                    <small>WAYO AI · <?php echo get_phrase('create_questions_from_pdf'); ?></small>
                </div>
                <div class="pdf-footer-actions">
                    <button type="button" class="btn btn-cancel" onclick="closePdfQuestionModal()">
                        <i class="fas fa-times"></i> <?php echo get_phrase('cancel'); ?>
                    </button>
                    <button type="button" class="btn btn-generate-questions" onclick="uploadAndGenerate(<?php echo $param1; ?>)">
                        <i class="fas fa-wand-magic-sparkles"></i> <?php echo get_phrase('generate_questions'); ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.pdf-modal {
    border: none;
    border-radius: 18px;
    box-shadow: 0 20px 60px rgba(14, 23, 38, 0.25);
    overflow: hidden;
}

.pdf-modal__header {
    background: linear-gradient(90deg, #f47a1f 0%, #fbb040 100%);
    color: #fff;
    border-bottom: none;
    padding: 28px 34px;
    align-items: flex-start;
}

.pdf-modal__subtitle {
    margin-bottom: 0;
    margin-top: 8px;
    color: rgba(255,255,255,0.7);
    font-size: 0.9rem;
}

.modern-close {
    border: none;
    background: rgba(255,255,255,0.12);
    color: #fff;
    width: 42px;
    height: 42px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
}

.modern-close:hover {
    background: rgba(255,255,255,0.25);
}

.pdf-modal__body {
    background: #f6f7fb;
    padding: 32px 34px;
}

.pdf-modal__grid {
    row-gap: 30px;
}

.pdf-info-panel {
    background: radial-gradient(140% 140% at 0% 0%, rgba(255,255,255,0.15) 0%, rgba(255,255,255,0) 100%), linear-gradient(165deg, #fbb040 0%, #f47a1f 100%);
    color: #fff;
    border-radius: 18px;
    padding: 30px;
    height: 100%;
    position: relative;
    overflow: hidden;
    box-shadow: inset 0 0 0 1px rgba(255,255,255,0.08);
}

.pdf-info-panel::after {
    content: "";
    position: absolute;
    top: -40px;
    right: -30px;
    width: 180px;
    height: 180px;
    background: rgba(255,255,255,0.08);
    border-radius: 50%;
    filter: blur(1px);
}

.pdf-info-panel__badge {
    display: inline-flex;
    align-items: center;
    padding: 6px 14px;
    border-radius: 999px;
    background: rgba(255,255,255,0.12);
    font-size: 0.8rem;
    margin-bottom: 18px;
    position: relative;
    z-index: 1;
}

.pdf-info-panel h4 {
    font-weight: 700;
    position: relative;
    z-index: 1;
}

.pdf-info-panel p {
    color: rgba(255,255,255,0.78);
    margin-bottom: 18px;
    position: relative;
    z-index: 1;
}

.pdf-info-panel__list {
    list-style: none;
    padding: 0;
    margin: 0 0 20px;
    position: relative;
    z-index: 1;
}

.pdf-info-panel__list li {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 10px;
    font-weight: 500;
}

.pdf-info-panel__list li i {
    color:rgb(241, 241, 241);
}

.pdf-info-panel__tip {
    display: flex;
    gap: 12px;
    align-items: flex-start;
    padding: 14px 16px;
    background: rgba(0,0,0,0.2);
    border-radius: 12px;
    font-size: 0.9rem;
    position: relative;
    z-index: 1;
}

.pdf-info-panel__tip i {
    color: #fcd34d;
    font-size: 1.2rem;
}

.pdf-upload-card {
    background: #ffffff;
    border-radius: 18px;
    padding: 26px;
    box-shadow: 0 25px 60px rgba(15, 23, 42, 0.08);
    border: 1px solid #ecedf3;
}

.pdf-section + .pdf-section {
    margin-top: 24px;
}

.pdf-section__label {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-weight: 600;
    color: #1e1e2f;
    margin-bottom: 14px;
}

.file-upload-wrapper {
    position: relative;
}

.file-upload-area {
    border: 2px dashed #d5d8f0;
    border-radius: 16px;
    padding: 48px 24px;
    text-align: center;
    background: #fdfdff;
    transition: all 0.3s ease;
    cursor: pointer;
    position: relative;
}

.file-upload-area:hover,
.file-upload-area.dragover {
    border-color: #f47a1f;
    background:rgb(250, 250, 250);
    transform: translateY(-2px);
}

.file-upload-illustration {
    position: relative;
    width: 80px;
    height: 80px;
    margin: 0 auto 18px;
}

.file-upload-illustration span {
    position: absolute;
    inset: 0;
    border-radius: 24px;
    border: 2px solid #f47a1f;
    animation: pulse 3s infinite;
}

.file-upload-illustration span:nth-child(2) {
    inset: 12px;
    animation-delay: 0.5s;
}

.file-upload-content h6 {
    font-weight: 700;
    color: #f47a1f;
    margin-bottom: 6px;
}

.file-upload-content p {
    color: #8a8fa6;
    margin-bottom: 0;
    font-size: 0.9rem;
}

.file-selected-content {
    text-align: center;
}

.file-selected-content i {
    font-size: 2.5rem;
    color: #dc3545;
    margin-bottom: 10px;
}

.file-selected-content p {
    font-weight: 600;
    margin-bottom: 4px;
}

.file-selected-content small {
    color: #6c757d;
}

/* Question Slider Styles */
.question-slider-container {
    background: #f8f9fc;
    border-radius: 16px;
    padding: 24px;
    border: 1px solid #e8eaf3;
}

.question-slider-display {
    text-align: center;
    margin-bottom: 20px;
}

.question-slider-value {
    font-size: 3rem;
    font-weight: 800;
    background: linear-gradient(135deg, #f47a1f 0%, #fbb040 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    line-height: 1;
}

.question-slider-label {
    display: block;
    font-size: 0.9rem;
    color: #64748b;
    margin-top: 4px;
    font-weight: 500;
}

.question-slider-wrapper {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 16px;
}

.slider-min, .slider-max {
    font-size: 0.85rem;
    color: #94a3b8;
    font-weight: 600;
    min-width: 24px;
    text-align: center;
}

.question-slider {
    flex: 1;
    -webkit-appearance: none;
    appearance: none;
    height: 8px;
    background: linear-gradient(90deg, #e2e8f0 0%, #e2e8f0 100%);
    border-radius: 10px;
    outline: none;
    cursor: pointer;
}

.question-slider::-webkit-slider-thumb {
    -webkit-appearance: none;
    appearance: none;
    width: 28px;
    height: 28px;
    background: linear-gradient(135deg, #f47a1f 0%, #fbb040 100%);
    border-radius: 50%;
    cursor: grab;
    box-shadow: 0 4px 12px rgba(244, 122, 31, 0.4);
    transition: all 0.2s ease;
    border: 3px solid white;
}

.question-slider::-webkit-slider-thumb:hover {
    transform: scale(1.15);
    box-shadow: 0 6px 20px rgba(244, 122, 31, 0.5);
}

.question-slider::-webkit-slider-thumb:active {
    cursor: grabbing;
    transform: scale(1.1);
}

.question-slider::-moz-range-thumb {
    width: 28px;
    height: 28px;
    background: linear-gradient(135deg, #f47a1f 0%, #fbb040 100%);
    border-radius: 50%;
    cursor: grab;
    box-shadow: 0 4px 12px rgba(244, 122, 31, 0.4);
    border: 3px solid white;
}

.slider-presets {
    display: flex;
    justify-content: space-between;
    gap: 8px;
}

.preset-btn {
    flex: 1;
    padding: 10px 8px;
    border: 2px solid #e2e8f0;
    background: white;
    border-radius: 10px;
    font-weight: 700;
    font-size: 0.9rem;
    color: #64748b;
    cursor: pointer;
    transition: all 0.2s ease;
}

.preset-btn:hover {
    border-color: #f47a1f;
    color: #f47a1f;
}

.preset-btn.active {
    background: linear-gradient(135deg, #f47a1f 0%, #fbb040 100%);
    border-color: transparent;
    color: white;
    box-shadow: 0 4px 12px rgba(244, 122, 31, 0.3);
}

/* Difficulty Selector Styles */
.difficulty-selector {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 12px;
}

.difficulty-option {
    border: 2px solid #e8eaf3;
    border-radius: 16px;
    padding: 18px 14px 14px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    background: white;
    position: relative;
}

.difficulty-option:hover {
    border-color: #f47a1f;
    transform: translateY(-4px);
    box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
}

.difficulty-option.active {
    border-color: #f47a1f;
    background: linear-gradient(135deg, rgba(244, 122, 31, 0.05) 0%, rgba(251, 176, 64, 0.08) 100%);
    box-shadow: 0 8px 20px rgba(244, 122, 31, 0.2);
}

.difficulty-badge {
    width: 48px;
    height: 48px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 10px;
    font-size: 1.3rem;
    transition: all 0.3s ease;
}

.difficulty-badge.easy {
    background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
    color: #16a34a;
}

.difficulty-badge.medium {
    background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
    color: #d97706;
}

.difficulty-badge.hard {
    background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
    color: #dc2626;
}

.difficulty-badge.mixed {
    background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%);
    color: #4f46e5;
}

.difficulty-option.active .difficulty-badge.easy {
    background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
    color: white;
    box-shadow: 0 4px 12px rgba(34, 197, 94, 0.4);
}

.difficulty-option.active .difficulty-badge.medium {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    color: white;
    box-shadow: 0 4px 12px rgba(245, 158, 11, 0.4);
}

.difficulty-option.active .difficulty-badge.hard {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    color: white;
    box-shadow: 0 4px 12px rgba(239, 68, 68, 0.4);
}

.difficulty-option.active .difficulty-badge.mixed {
    background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
    color: white;
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.4);
}

.difficulty-name {
    display: block;
    font-size: 0.85rem;
    font-weight: 600;
    color: #475569;
    margin-bottom: 8px;
}

.difficulty-option.active .difficulty-name {
    color: #1e293b;
    font-weight: 700;
}

.difficulty-bar {
    display: flex;
    gap: 4px;
    justify-content: center;
    height: 4px;
}

.difficulty-bar .bar-fill {
    width: 16px;
    height: 4px;
    border-radius: 2px;
    background: #e2e8f0;
    transition: all 0.3s ease;
}

.difficulty-option[data-level="easy"] .bar-fill { background: #bbf7d0; }
.difficulty-option[data-level="medium"] .bar-fill { background: #fde68a; }
.difficulty-option[data-level="hard"] .bar-fill { background: #fecaca; }
.difficulty-option[data-level="mixed"] .bar-fill.gradient { 
    background: linear-gradient(90deg, #bbf7d0 0%, #fde68a 50%, #fecaca 100%); 
}

.difficulty-option.active[data-level="easy"] .bar-fill { background: #22c55e; }
.difficulty-option.active[data-level="medium"] .bar-fill { background: #f59e0b; }
.difficulty-option.active[data-level="hard"] .bar-fill { background: #ef4444; }
.difficulty-option.active[data-level="mixed"] .bar-fill.gradient { 
    background: linear-gradient(90deg, #22c55e 0%, #f59e0b 50%, #ef4444 100%); 
}

/* Existing Questions Badge */
.existing-questions-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: rgb(255 255 255);
    color: rgb(127 127 127);
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
    border: 1px solid rgb(127 127 127 / 20%);
}

.existing-questions-badge i {
    font-size: 0.8rem;
}

.existing-questions-badge #existingQuestionsCount {
    font-weight: 800;
    font-size: 1rem;
}

/* Progress Steps Styles */
.question-counter {
    text-align: center;
    margin-bottom: 24px;
    padding-bottom: 20px;
    border-bottom: 1px solid rgba(244, 122, 31, 0.15);
}

.counter-circle {
    display: inline-flex;
    align-items: baseline;
    justify-content: center;
    background: linear-gradient(135deg, rgba(244, 122, 31, 0.1) 0%, rgba(251, 176, 64, 0.15) 100%);
    border-radius: 20px;
    padding: 12px 28px;
    margin-bottom: 8px;
}

.counter-current {
    font-size: 2.5rem;
    font-weight: 800;
    color: #f47a1f;
    line-height: 1;
}

.counter-separator {
    font-size: 1.5rem;
    color: #94a3b8;
    margin: 0 4px;
}

.counter-total {
    font-size: 1.5rem;
    font-weight: 600;
    color: #64748b;
}

.counter-label {
    display: block;
    font-size: 0.9rem;
    color: #64748b;
    font-weight: 500;
}

.progress-steps {
    display: flex;
    flex-direction: column;
    gap: 0;
    margin-bottom: 20px;
}

.progress-step {
    display: flex;
    align-items: flex-start;
    gap: 14px;
    opacity: 0.4;
    transition: all 0.4s ease;
}

.progress-step.active {
    opacity: 1;
}

.progress-step.completed {
    opacity: 1;
}

.step-indicator {
    display: flex;
    flex-direction: column;
    align-items: center;
}

.step-icon {
    width: 40px;
    height: 40px;
    border-radius: 12px;
    background: #e8eaf3;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #94a3b8;
    font-size: 1rem;
    transition: all 0.4s ease;
}

.progress-step.active .step-icon {
    background: linear-gradient(135deg, #f47a1f 0%, #fbb040 100%);
    color: white;
    box-shadow: 0 4px 15px rgba(244, 122, 31, 0.4);
    animation: pulse-step 1.5s ease-in-out infinite;
}

.progress-step.completed .step-icon {
    background: #22c55e;
    color: white;
}

.progress-step.completed .step-icon i::before {
    content: "\f00c";
}

.step-line {
    width: 2px;
    height: 20px;
    background: #e2e8f0;
    margin: 6px 0;
    transition: all 0.4s ease;
}

.progress-step.completed .step-line {
    background: #22c55e;
}

.progress-step.active .step-line {
    background: linear-gradient(180deg, #f47a1f 0%, #e2e8f0 100%);
}

.step-content {
    padding-top: 8px;
    padding-bottom: 16px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.step-number-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 24px;
    height: 24px;
    border-radius: 8px;
    background: #e8eaf3;
    color: #94a3b8;
    font-size: 0.75rem;
    font-weight: 700;
    transition: all 0.4s ease;
}

.progress-step.active .step-number-badge {
    background: linear-gradient(135deg, #f47a1f 0%, #fbb040 100%);
    color: white;
    box-shadow: 0 3px 10px rgba(244, 122, 31, 0.3);
}

.progress-step.completed .step-number-badge {
    background: #22c55e;
    color: white;
}

.step-text {
    font-size: 0.95rem;
    color: #64748b;
    font-weight: 500;
}

.progress-step.active .step-text {
    color: #1e293b;
    font-weight: 600;
}

.progress-step.completed .step-text {
    color: #22c55e;
}

.progress-percentage {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    margin-top: 12px;
    font-size: 1.1rem;
    color: #f47a1f;
}

@keyframes pulse-step {
    0%, 100% {
        transform: scale(1);
        box-shadow: 0 4px 15px rgba(244, 122, 31, 0.4);
    }
    50% {
        transform: scale(1.05);
        box-shadow: 0 6px 25px rgba(244, 122, 31, 0.6);
    }
}

.modern-toggle-wrapper {
    display: block;
    margin-top: 4px;
}

.modern-toggle-input {
    display: none;
}

.modern-toggle-label {
    display: inline-flex;
    align-items: center;
    gap: 14px;
    cursor: pointer;
    user-select: none;
}

.modern-toggle-button {
    position: relative;
    width: 54px;
    height: 28px;
    background: linear-gradient(135deg, #e8eaf0 0%, #d4d7e3 100%);
    border-radius: 999px;
    transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: inset 0 2px 6px rgba(0, 0, 0, 0.12), 0 1px 2px rgba(0, 0, 0, 0.08);
    flex-shrink: 0;
}

.modern-toggle-button::before {
    content: '';
    position: absolute;
    top: 3px;
    left: 3px;
    width: 22px;
    height: 22px;
    background: linear-gradient(135deg, #ffffff 0%, #f8f9fb 100%);
    border-radius: 50%;
    transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 3px 8px rgba(0, 0, 0, 0.15), 0 1px 3px rgba(0, 0, 0, 0.1);
}

.modern-toggle-button::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 10px;
    transform: translateY(-50%);
    width: 10px;
    height: 10px;
    background: #ff6b6b;
    border-radius: 50%;
    opacity: 0;
    transition: all 0.3s ease;
}

.modern-toggle-input:checked + .modern-toggle-label .modern-toggle-button {
    background: linear-gradient(135deg, #f47a1f 0%, #fbb040 100%);
    box-shadow: inset 0 2px 6px rgba(244, 122, 31, 0.3), 0 4px 12px rgba(244, 122, 31, 0.4);
}

.modern-toggle-input:checked + .modern-toggle-label .modern-toggle-button::before {
    left: 29px;
    background: linear-gradient(135deg, #ffffff 0%, #fffbf7 100%);
    box-shadow: 0 4px 12px rgba(244, 122, 31, 0.3), 0 2px 4px rgba(0, 0, 0, 0.1);
}

.modern-toggle-input:checked + .modern-toggle-label .modern-toggle-button::after {
    left: auto;
    right: 10px;
    background: #fff;
    opacity: 1;
    animation: toggle-check 0.4s ease;
}

.modern-toggle-label:hover .modern-toggle-button {
    transform: scale(1.02);
}

.modern-toggle-label:active .modern-toggle-button {
    transform: scale(0.98);
}

.modern-toggle-text {
    font-weight: 500;
    color: #4c4f63;
    font-size: 0.95rem;
    transition: color 0.3s ease;
}

.modern-toggle-input:checked + .modern-toggle-label .modern-toggle-text {
    color: #f47a1f;
    font-weight: 600;
}

@keyframes toggle-check {
    0% {
        transform: translateY(-50%) scale(0);
        opacity: 0;
    }
    50% {
        transform: translateY(-50%) scale(1.2);
    }
    100% {
        transform: translateY(-50%) scale(1);
        opacity: 1;
    }
}

.pdf-progress-block {
    background: linear-gradient(135deg, rgba(244, 122, 31, 0.08), rgba(251, 176, 64, 0.15));
    border: 1px solid rgba(244, 122, 31, 0.2);
    border-radius: 18px;
    padding: 20px;
    box-shadow: 0 15px 30px rgba(244, 122, 31, 0.08);
}

.pdf-progress-block__header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 10px;
    color: #4a4d6a;
}

.pdf-progress-heading {
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: #f47a1f;
}

.pdf-progress-chip {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #fff;
    border-radius: 999px;
    padding: 6px 14px;
    border: 1px solid rgba(244, 122, 31, 0.2);
    color: #f47a1f;
    font-weight: 600;
    box-shadow: inset 0 1px 2px rgba(255, 255, 255, 0.6);
}

.pdf-progress-chip i {
    font-size: 0.85rem;
}

.neo-progress__track {
    height: 14px;
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.35);
    position: relative;
    overflow: hidden;
    box-shadow: inset 0 2px 6px rgba(15, 23, 42, 0.12);
}

.neo-progress__bar {
    height: 100%;
    width: 0%;
    border-radius: inherit;
    background: linear-gradient(90deg, #f47a1f 0%, #fbb040 50%, #ffd06b 100%);
    position: relative;
    transition: width 0.4s ease;
    box-shadow: 0 10px 30px rgba(244, 122, 31, 0.35);
}

.neo-progress__bar::after {
    content: "";
    position: absolute;
    inset: 0;
    background-image: linear-gradient(120deg, rgba(255,255,255,0.25) 25%, transparent 25%, transparent 50%, rgba(255,255,255,0.25) 50%, rgba(255,255,255,0.25) 75%, transparent 75%, transparent);
    background-size: 40px 40px;
    animation: progress-stripe 2s linear infinite;
    opacity: 0.6;
}

.pdf-progress-status {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-top: 12px;
    font-size: 0.9rem;
    color: #5f6279;
}

.status-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background: #f47a1f;
    box-shadow: 0 0 12px rgba(244, 122, 31, 0.6);
    animation: status-blink 1.5s ease-in-out infinite;
}

.pdf-modal__footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 34px;
    background: #ffffff;
    border-top: 1px solid #eef0f8;
}

.pdf-footer-copy {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: #7b7f97;
    font-size: 0.85rem;
}

.pdf-footer-actions {
    display: flex;
    gap: 12px;
}

.btn-cancel {
    border-radius: 10px;
    padding: 10px 26px;
    font-weight: 500;
    border: 1px solid #e2e4ef;
    background: #fff;
    color: #4c4f63;
    transition: all 0.25s ease;
}

.btn-generate-questions {
    border-radius: 10px;
    padding: 10px 30px;
    font-weight: 600;
    border: none;
    background: linear-gradient(135deg, #f47a1f 0%, #fbb040 100%);
    color: #fff;
    transition: all 0.25s ease;
}

.btn-generate-questions:hover:not(:disabled) {
    transform: translateY(-3px);
}

.btn-generate-questions:disabled {
    background: linear-gradient(135deg, #a0a0a0 0%, #c0c0c0 100%);
    cursor: not-allowed !important;
    opacity: 0.6;
}

.btn-cancel:hover:not(:disabled) {
    transform: translateY(-3px);
}

.btn-cancel:disabled {
    background: #f5f5f5;
    color: #999;
    cursor: not-allowed !important;
    opacity: 0.6;
}

@keyframes pulse {
    0% {
        transform: scale(1);
        opacity: 1;
    }
    100% {
        transform: scale(1.15);
        opacity: 0;
    }
}

@keyframes progress-stripe {
    from {
        background-position: 0 0;
    }
    to {
        background-position: 40px 0;
    }
}

@keyframes status-blink {
    0%, 100% {
        opacity: 0.3;
    }
    50% {
        opacity: 1;
    }
}
</style>

<!-- Init Dragula -->
<script type="text/javascript">
    ! function(r) {
        "use strict";
        var a = function() {
            this.$body = r("body");
        };
        a.prototype.init = function() {
            r('[data-plugin="dragula"]').each(function() {
                var a = r(this).data("containers"),
                    t = [];
                if (a) {
                    for (var n = 0; n < a.length; n++) t.push(r("#" + a[n])[0]);
                } else {
                    t = [r(this)[0]];
                }
                var i = r(this).data("handleclass");
                i ? dragula(t, {
                    moves: function(a, t, n) {
                        return n.classList.contains(i);
                    }
                }) : dragula(t);
            });
        }, r.Dragula = new a, r.Dragula.Constructor = a;
    }(window.jQuery),
    function(a) {
        "use strict";
        window.jQuery.Dragula.init();
    }();
</script>

<script type="text/javascript">
    jQuery(document).ready(function() {
        $('.widgets-of-quiz-question').hide();
        // Show sort button
        $('#question-sort-btn').show();

        $('.on-hover-action').mouseenter(function() {
            var id = this.id;
            $('#widgets-of-' + id).show();
        });
        $('.on-hover-action').mouseleave(function() {
            var id = this.id;
            $('#widgets-of-' + id).hide();
        });
    });

    function deleteExamQuestionAndReloadModal(examID, questionID) {
        var deletionURL = '<?php echo site_url(); ?>addons/courses/exam_questions/' + examID + '/delete/' + questionID;

        confirmModal(deletionURL, function(response) {
            if (!response) {
                error_notify('<?php echo get_phrase('no_response_from_server'); ?>');
                return;
            }

            if (response.status) {
                success_notify('<?php echo get_phrase('exam_question_deleted_successfully'); ?>');
                try {
                    $('#alert-modal').modal('hide');
                } catch (e) {
                    console.error('Error while closing the modal: ', e);
                }

                setTimeout(function() {
                    try {
                        updateLargeModal('<?php echo site_url('modal/popup/academy/exam_questions/'); ?>' + examID, '<?php echo get_phrase('manage_exam_questions'); ?>');
                    } catch (e) {
                        console.error('Error while reloading the question list: ', e);
                        error_notify('<?php echo get_phrase('error_reloading_question_list'); ?>');
                    }
                }, 500);
            } else {
                console.error('Deletion failed: ', response);
                error_notify('<?php echo get_phrase('error_deleting_question'); ?>');
            }
        });
    }

    function sort() {
        var containerArray = ['question-list'];
        var itemArray = [];
        for (var i = 0; i < containerArray.length; i++) {
            $('#' + containerArray[i]).each(function() {
                $(this).find('.draggable-item').each(function() {
                    itemArray.push(this.id);
                });
            });
        }

        var examID = '<?php echo $param1; ?>';
        var itemJSON = JSON.stringify(itemArray);
        $.ajax({
            url: '<?php echo site_url('addons/courses/ajax_sort_question/'); ?>',
            type: 'POST',
            data: {
                itemJSON: itemJSON,
                exam_id: examID
            },
            dataType: 'json',
            success: function(response) {
                if (response.status) {
                    success_notify('<?php echo get_phrase('questions_have_been_sorted'); ?>');
                    setTimeout(function() {
                        updateLargeModal('<?php echo site_url('modal/popup/academy/exam_questions/'); ?>' + examID, '<?php echo get_phrase('manage_exam_questions'); ?>');
                    }, 1000);
                } else {
                    console.error('Sorting failed: ', response);
                    error_notify('<?php echo get_phrase('error_sorting_questions'); ?>');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error during sorting: ', error);
                error_notify('<?php echo get_phrase('error_sorting_questions'); ?>');
            }
        });
    }
</script>

<script type="text/javascript">
    function generateQuestionsFromPdf(examId) {
        $('#pdfQuestionModal').modal('show');
        
        // Reset the interface after a short delay to ensure the modal is loaded
        setTimeout(function() {
            resetFileUploadUI();
        }, 300);
    }

    function closePdfQuestionModal() {
        $('#pdfQuestionModal').modal('hide');
        // Reset the form
        resetFileUploadUI();
        resetGenerateButton();
        $('#pdf_file_input').val('');
        $('#progressContainer').hide();
        $('#progressBar').css('width', '0%');
        $('#progressPercent').text('0%');
    }

    function resetFileUploadUI() {
        $('.file-upload-content').show();
        $('.file-selected-content').hide();
        $('#fileUploadArea').removeClass('dragover');
        
        // Reset slider to default (10)
        $('#questionSlider').val(10);
        $('#sliderValue').text('10');
        $('#number_of_questions').val('10');
        updateSliderBackground(10);
        
        // Reset preset buttons
        $('.preset-btn').removeClass('active');
        $('.preset-btn[data-value="10"]').addClass('active');
        
        // Reset difficulty to medium
        $('.difficulty-option').removeClass('active');
        $('.difficulty-option[data-level="medium"]').addClass('active');
        $('input[name="difficulty_level"][value="medium"]').prop('checked', true);
        $('#difficulty_level').val('medium');
        
        // Reset progress steps
        resetProgressSteps();
        
        $('#overwriteExistingQuestions').prop('checked', false);
    }
    
    function updateSliderBackground(value) {
        const slider = document.getElementById('questionSlider');
        if (slider) {
            const percent = ((value - 5) / (50 - 5)) * 100;
            slider.style.background = `linear-gradient(90deg, #f47a1f 0%, #fbb040 ${percent}%, #e2e8f0 ${percent}%)`;
        }
    }
    
    function resetProgressSteps() {
        $('.progress-step').removeClass('active completed');
        $('#questionsGenerated').text('0');
        $('#progressPercent').text('0%');
        $('#progressBar').css('width', '0%');
    }
    
    function setProgressStep(stepNumber) {
        $('.progress-step').each(function() {
            const step = parseInt($(this).data('step'));
            if (step < stepNumber) {
                $(this).removeClass('active').addClass('completed');
            } else if (step === stepNumber) {
                $(this).removeClass('completed').addClass('active');
            } else {
                $(this).removeClass('active completed');
            }
        });
    }
    
    function updateQuestionCounter(current, total) {
        $('#questionsGenerated').text(current);
        $('#questionsTotal').text(total);
    }

    function removeFile() {
        $('#pdf_file_input').val('');
        resetFileUploadUI();
    }

    // Gestion du drag & drop
    $(document).ready(function() {
        const doc = $(document);

        // Click on the upload area (delegated to support dynamically injected content)
        doc.off('click.pdfUpload', '#fileUploadArea').on('click.pdfUpload', '#fileUploadArea', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const fileInput = document.getElementById('pdf_file_input');
            if (fileInput && typeof fileInput.click === 'function') {
                fileInput.click();
            }
        });

        // Gestion du changement de fichier
        doc.off('change.pdfUpload', '#pdf_file_input').on('change.pdfUpload', '#pdf_file_input', function(e) {
            handleFileSelect(e.target.files[0]);
        });

        // Drag & drop
        doc.off('dragover.pdfUpload', '#fileUploadArea').on('dragover.pdfUpload', '#fileUploadArea', function(e) {
            e.preventDefault();
            e.stopPropagation();
            $(this).addClass('dragover');
        });

        doc.off('dragleave.pdfUpload', '#fileUploadArea').on('dragleave.pdfUpload', '#fileUploadArea', function(e) {
            e.preventDefault();
            e.stopPropagation();
            $(this).removeClass('dragover');
        });

        doc.off('drop.pdfUpload', '#fileUploadArea').on('drop.pdfUpload', '#fileUploadArea', function(e) {
            e.preventDefault();
            e.stopPropagation();
            $(this).removeClass('dragover');
            
            const dataTransfer = e.originalEvent && e.originalEvent.dataTransfer;
            const files = dataTransfer && dataTransfer.files ? dataTransfer.files : [];
            if (files.length > 0) {
                const fileInput = document.getElementById('pdf_file_input');
                if (fileInput) {
                    try {
                        if (window.DataTransfer) {
                            const transfer = new DataTransfer();
                            Array.from(files).forEach(file => transfer.items.add(file));
                            fileInput.files = transfer.files;
                        } else {
                            fileInput.files = files;
                        }
                    } catch (error) {
                        console.warn('Unable to assign dropped file to input:', error);
                    }
                }
                handleFileSelect(files[0]);
            }
        });

        // Slider pour le nombre de questions
        doc.off('input.pdfUpload', '#questionSlider').on('input.pdfUpload', '#questionSlider', function() {
            const value = $(this).val();
            $('#sliderValue').text(value);
            $('#number_of_questions').val(value);
            updateSliderBackground(value);
            
            // Update preset buttons
            $('.preset-btn').removeClass('active');
            $('.preset-btn[data-value="' + value + '"]').addClass('active');
        });
        
        // Preset buttons
        doc.off('click.pdfUpload', '.preset-btn').on('click.pdfUpload', '.preset-btn', function(e) {
            e.preventDefault();
            const value = $(this).data('value');
            $('#questionSlider').val(value);
            $('#sliderValue').text(value);
            $('#number_of_questions').val(value);
            updateSliderBackground(value);
            
            $('.preset-btn').removeClass('active');
            $(this).addClass('active');
        });
        
        // Difficulty selector
        doc.off('click.pdfUpload', '.difficulty-option').on('click.pdfUpload', '.difficulty-option', function(e) {
            e.preventDefault();
            const level = $(this).data('level');
            
            $('.difficulty-option').removeClass('active');
            $(this).addClass('active');
            
            $('input[name="difficulty_level"]').prop('checked', false);
            $(this).find('input[type="radio"]').prop('checked', true);
            $('#difficulty_level').val(level);
        });

        // Manage the question count selection (delegated event for dynamically loaded modal)
        doc.off('click.pdfUpload', '#pdfQuestionModal .question-option').on('click.pdfUpload', '#pdfQuestionModal .question-option', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            // Retirer la classe active de tous les boutons dans le modal
            $('#pdfQuestionModal .question-option').removeClass('active');
            
            // Uncheck all radio buttons in the modal
            $('#pdfQuestionModal .question-option input[type="radio"]').prop('checked', false);
            
            // Add the active class to the clicked button
            $(this).addClass('active');
            
            // Cocher le bouton radio correspondant
            const radio = $(this).find('input[type="radio"]');
            radio.prop('checked', true);
            
            // Update the hidden select field
            const value = radio.val();
            $('#number_of_questions').val(value);
        });
    });

    function handleFileSelect(file) {
        if (!file) return;

        if (file.type !== 'application/pdf') {
            error_notify('<?php echo get_phrase('please_select_valid_pdf'); ?>');
            return;
        }

        if (file.size > 10 * 1024 * 1024) {
            error_notify('<?php echo get_phrase('file_too_large_max_10mb'); ?>');
            return;
        }

        // Afficher les informations du fichier
        const fileName = file.name;
        const fileSize = (file.size / (1024 * 1024)).toFixed(2) + ' MB';

        $('#fileName').text(fileName);
        $('#fileSize').text(fileSize);
        $('.file-upload-content').hide();
        $('.file-selected-content').show();
    }

    // Fermer le modal avec la touche Escape
    $(document).on('keydown', function(e) {
        if (e.key === 'Escape' && $('#pdfQuestionModal').hasClass('show')) {
            closePdfQuestionModal();
        }
    });

    // Fermer le modal en cliquant sur le backdrop
    $('#pdfQuestionModal').on('click', function(e) {
        if ($(e.target).hasClass('modal') || $(e.target).hasClass('modal-dialog')) {
            closePdfQuestionModal();
        }
    });

    function resetGenerateButton() {
        const generateBtn = $('.btn-generate-questions');
        const cancelBtn = $('.btn-cancel');
        
        generateBtn.prop('disabled', false);
        generateBtn.html('<i class="fas fa-wand-magic-sparkles"></i> <?php echo get_phrase('generate_questions'); ?>');
        generateBtn.css('opacity', '1');
        generateBtn.css('cursor', 'pointer');
        
        cancelBtn.prop('disabled', false).css('opacity', '1');
    }

    function updateExamQuestionsCsrf(csrfData) {
        if (!csrfData || !csrfData.csrfName || !csrfData.csrfHash) {
            return;
        }
        $('#csrf_token').attr('name', csrfData.csrfName).val(csrfData.csrfHash);
    }

    function uploadAndGenerate(examId) {
        const fileInput = document.getElementById('pdf_file_input');
        const file = fileInput.files[0];
        const generateBtn = $('.btn-generate-questions');
        
        // Vérifier si une génération est déjà en cours
        if (generateBtn.prop('disabled')) {
            error_notify('<?php echo get_phrase("generation_already_in_progress"); ?>');
            return;
        }
        
        // Get values from slider and difficulty selector
        const numQuestions = $('#number_of_questions').val() || 10;
        const difficultyLevel = $('#difficulty_level').val() || 'medium';
        const overwriteExisting = $('#overwriteExistingQuestions').is(':checked') ? 1 : 0;

        if (!file) {
            error_notify('<?php echo get_phrase("please_select_a_pdf_file"); ?>');
            return;
        }

        if (file.size > 10 * 1024 * 1024) {
            error_notify('<?php echo get_phrase('file_too_large_max_10mb'); ?>');
            return;
        }

        const formData = new FormData();
        formData.append('pdf_file', file);
        formData.append('exam_id', examId);
        formData.append('number_of_questions', numQuestions);
        formData.append('difficulty_level', difficultyLevel);
        formData.append('overwrite_existing', overwriteExisting);
        const csrfInput = document.getElementById('csrf_token');
        if (csrfInput) {
            formData.append(csrfInput.name, csrfInput.value);
        }

        // Désactiver le bouton et changer son apparence
        generateBtn.prop('disabled', true);
        generateBtn.html('<i class="fas fa-spinner fa-spin"></i> <?php echo get_phrase('generating'); ?>...');
        generateBtn.css('opacity', '0.6');
        generateBtn.css('cursor', 'not-allowed');
        
        // Désactiver aussi le bouton d'annulation
        $('.btn-cancel').prop('disabled', true).css('opacity', '0.6');

        // Afficher progression et initialiser
        $('#progressContainer').show();
        resetProgressSteps();
        updateQuestionCounter(0, numQuestions);
        setProgressStep(1); // Étape 1: Extraction du texte
        
        // Simuler les étapes de progression avec timing réaliste
        let currentStep = 1;
        let simulatedQuestions = 0;
        const totalQuestions = parseInt(numQuestions);
        
        // Calculer le temps estimé par question (environ 2-4 secondes par question pour l'IA)
        const estimatedTimePerQuestion = 3000; // 3 secondes par question
        const totalEstimatedTime = totalQuestions * estimatedTimePerQuestion;
        const stepInterval = 2500; // Intervalle entre vérifications de progression des étapes
        const questionInterval = Math.max(2000, estimatedTimePerQuestion * 0.8); // Intervalle pour incrémenter les questions
        
        let stepTimer = 0;
        let questionTimer = 0;
        
        const progressSimulation = setInterval(function() {
            stepTimer += 600;
            questionTimer += 600;
            
            // Progression des étapes plus lente et réaliste
            if (currentStep < 4 && stepTimer >= stepInterval) {
                stepTimer = 0;
                const randomProgress = Math.random();
                // Étape 1 -> 2 après ~3s, Étape 2 -> 3 après ~5s
                const threshold = currentStep === 1 ? 0.4 : (currentStep === 2 ? 0.3 : 0.8);
                if (randomProgress > threshold && currentStep < 3) {
                    currentStep++;
                    setProgressStep(currentStep);
                }
            }
            
            // Compteur de questions plus lent et réaliste (seulement pendant l'étape 3)
            if (currentStep >= 3 && simulatedQuestions < totalQuestions - 1 && questionTimer >= questionInterval) {
                questionTimer = 0;
                // Incrémenter de 1 seule question à la fois pour plus de réalisme
                simulatedQuestions += 1;
                if (simulatedQuestions > totalQuestions - 1) simulatedQuestions = totalQuestions - 1;
                updateQuestionCounter(simulatedQuestions, totalQuestions);
            }
        }, 600);

        $.ajax({
            url: '<?php echo site_url('addons/courses/generate_questions_from_pdf'); ?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            xhr: function() {
                var xhr = new window.XMLHttpRequest();
                xhr.upload.addEventListener("progress", function(evt) {
                    if (evt.lengthComputable) {
                        var percent = Math.round((evt.loaded / evt.total) * 25); // 25% upload
                        $('#progressBar').css('width', percent + '%');
                        $('#progressPercent').text(percent + '%');
                        
                        // Passer à l'étape 2 après l'upload
                        if (percent >= 25 && currentStep < 2) {
                            currentStep = 2;
                            setProgressStep(2);
                        }
                    }
                }, false);
                return xhr;
            },
            success: function(response) {
                var data;
                try {
                    data = typeof response === 'string' ? JSON.parse(response) : response;
                } catch (e) {
                    error_notify('<?php echo get_phrase('server_response_error'); ?>');
                    resetGenerateButton();
                    return;
                }
                
                if (data && data.csrf) {
                    updateExamQuestionsCsrf(data.csrf);
                }

                if (data.status === true) {
                    // Arrêter la simulation
                    clearInterval(progressSimulation);
                    
                    // Finaliser les étapes
                    setProgressStep(4);
                    setTimeout(() => {
                        $('.progress-step').addClass('completed').removeClass('active');
                    }, 300);
                    
                    // Mettre à jour le compteur final
                    const finalCount = data.questions_count || totalQuestions;
                    updateQuestionCounter(finalCount, totalQuestions);
                    
                    $('#progressBar').css('width', '100%');
                    $('#progressPercent').text('100%');
                    success_notify(data.message || '<?php echo get_phrase('questions_generated_successfully'); ?>');
                    setTimeout(() => {
                        $('#pdfQuestionModal').modal('hide');
                        resetGenerateButton();
                        resetProgressSteps();
                    }, 800);

                    setTimeout(() => {
                        largeModal(
                            '<?php echo site_url('modal/popup/academy/exam_questions/'); ?>' + examId,
                            '<?php echo get_phrase('manage_exam_questions'); ?>'
                        );
                    }, 1000);
                } else {
                    clearInterval(progressSimulation);
                    error_notify(data.message || '<?php echo get_phrase('generation_error'); ?>');
                    resetGenerateButton();
                    resetProgressSteps();
                }
            },
            error: function(xhr) {
                clearInterval(progressSimulation);
                let message = '<?php echo get_phrase('network_or_server_error'); ?>';
                if (xhr.responseJSON) {
                    if (xhr.responseJSON.csrf) {
                        updateExamQuestionsCsrf(xhr.responseJSON.csrf);
                    }
                    if (xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                } else if (xhr.responseText) {
                    try {
                        const parsed = JSON.parse(xhr.responseText);
                        if (parsed.csrf) {
                            updateExamQuestionsCsrf(parsed.csrf);
                        }
                        if (parsed.message) {
                            message = parsed.message;
                        }
                    } catch (err) {
                        // ignore parse errors
                    }
                }
                error_notify(message);
                resetGenerateButton();
                resetProgressSteps();
            },
            complete: function() {
                setTimeout(() => $('#progressContainer').hide(), 2500);
            }
        });
    }
</script>