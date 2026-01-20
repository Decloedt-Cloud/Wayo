<?php
$quiz_questions = $this->lms_model->get_quiz_questions($lesson_details['id']);
$lesson_progress = lesson_progress($lesson_details['id']);
$question_count = count($quiz_questions->result_array());
?>

<style>
/* ========== MODERN QUIZ STYLES ========== */
.quiz-wrapper {
    max-width: 800px;
    margin: 0 auto;
}

/* Quiz Header Card */
.quiz-info-card {
    background: var(--bg-card);
    border-radius: var(--radius-xl);
    border: 1px solid var(--border-color);
    box-shadow: var(--shadow-md);
    padding: 2rem;
    text-align: center;
    animation: fadeUp 0.4s ease;
}

.quiz-icon {
    width: 80px;
    height: 80px;
    margin: 0 auto 1.5rem;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--primary-lighter), #c7d2fe);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    color: var(--primary);
    position: relative;
}

.quiz-icon::after {
    content: '';
    position: absolute;
    inset: -6px;
    border-radius: 50%;
    border: 2px dashed var(--primary-lighter);
    animation: spin 15s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.quiz-info-title {
    font-family: var(--font-header);
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--text-dark);
    margin-bottom: 0.5rem;
}

.quiz-info-subtitle {
    color: var(--text-muted);
    font-size: 0.9375rem;
    margin-bottom: 1.5rem;
}

.quiz-stats {
    display: flex;
    justify-content: center;
    gap: 2rem;
    margin-bottom: 2rem;
    padding: 1.25rem;
    background: var(--bg-main);
    border-radius: var(--radius-lg);
}

.quiz-stat {
    text-align: center;
}

.quiz-stat-value {
    display: block;
    font-family: var(--font-header);
    font-size: 1.75rem;
    font-weight: 700;
    color: var(--primary);
    line-height: 1;
}

.quiz-stat-label {
    font-size: 0.8125rem;
    color: var(--text-muted);
    margin-top: 0.25rem;
}

.quiz-actions {
    display: flex;
    justify-content: center;
    gap: 1rem;
    flex-wrap: wrap;
}

.quiz-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.625rem;
    padding: 0.875rem 1.75rem;
    border-radius: var(--radius-md);
    font-size: 0.9375rem;
    font-weight: 600;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
}

.quiz-btn-primary {
    background: linear-gradient(135deg, var(--primary), var(--primary-light));
    color: white;
    box-shadow: 0 4px 14px rgba(99, 102, 241, 0.35);
}

.quiz-btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(99, 102, 241, 0.45);
}

.quiz-btn-secondary {
    background: var(--bg-main);
    color: var(--primary);
    border: 2px solid var(--primary-lighter);
}

.quiz-btn-secondary:hover {
    background: var(--primary-lighter);
    border-color: var(--primary);
}

/* Question Card */
.question-card {
    background: var(--bg-card);
    border-radius: var(--radius-xl);
    border: 1px solid var(--border-color);
    box-shadow: var(--shadow-md);
    overflow: hidden;
    animation: fadeUp 0.4s ease;
}

.question-header {
    padding: 1.25rem 1.5rem;
    background: linear-gradient(135deg, var(--primary), var(--primary-light));
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.question-number-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
    border-radius: 50px;
    color: white;
    font-weight: 600;
    font-size: 0.875rem;
}

.question-timer {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    background: rgba(255, 255, 255, 0.95);
    border-radius: 50px;
    font-weight: 700;
    font-size: 0.9375rem;
    color: var(--primary-dark);
}

.question-timer i {
    color: #ef4444;
    animation: pulse 1s ease infinite;
}

.question-body {
    padding: 1.5rem;
}

.question-text {
    font-family: var(--font-header);
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--text-dark);
    margin-bottom: 1.5rem;
    line-height: 1.5;
}

/* Timer Progress Bar */
.timer-progress {
    height: 4px;
    background: var(--border-color);
    border-radius: 2px;
    margin-bottom: 1.5rem;
    overflow: hidden;
}

.timer-progress-bar {
    height: 100%;
    background: linear-gradient(90deg, var(--secondary), #f59e0b, #ef4444);
    border-radius: 2px;
    animation: timerCountdown 15s linear forwards;
}

@keyframes timerCountdown {
    from { width: 100%; }
    to { width: 0%; }
}

/* Options List */
.options-header {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 1rem;
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.options-header i {
    color: var(--primary);
}

.options-list {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.option-item {
    position: relative;
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem 1.25rem;
    background: var(--bg-main);
    border: 2px solid var(--border-color);
    border-radius: var(--radius-md);
    cursor: pointer;
    transition: all 0.2s ease;
}

.option-item:hover {
    background: var(--primary-lighter);
    border-color: var(--primary);
    transform: translateX(4px);
}

.option-item.selected {
    background: var(--primary-lighter);
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}

.option-checkbox {
    width: 22px;
    height: 22px;
    border: 2px solid var(--border-color);
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    transition: all 0.2s ease;
    background: var(--bg-card);
}

.option-item.selected .option-checkbox {
    background: var(--primary);
    border-color: var(--primary);
}

.option-item.selected .option-checkbox::after {
    content: '✓';
    color: white;
    font-size: 0.75rem;
    font-weight: 700;
}

.option-text {
    flex: 1;
    font-size: 0.9375rem;
    color: var(--text-dark);
    line-height: 1.5;
}

.option-item input[type="checkbox"] {
    position: absolute;
    opacity: 0;
    cursor: pointer;
}

/* Question Footer */
.question-footer {
    padding: 1.25rem 1.5rem;
    background: var(--bg-main);
    border-top: 1px solid var(--border-color);
    display: flex;
    justify-content: flex-end;
}

/* Quiz Result Container */
#quiz-result {
    margin-top: 1.5rem;
}

/* Responsive */
@media (max-width: 768px) {
    .quiz-info-card {
        padding: 1.5rem;
    }
    
    .quiz-icon {
        width: 64px;
        height: 64px;
        font-size: 1.5rem;
    }
    
    .quiz-info-title {
        font-size: 1.25rem;
    }
    
    .quiz-stats {
        gap: 1.25rem;
        padding: 1rem;
    }
    
    .quiz-stat-value {
        font-size: 1.5rem;
    }
    
    .quiz-actions {
        flex-direction: column;
    }
    
    .quiz-btn {
        width: 100%;
        justify-content: center;
    }
    
    .question-header {
        flex-direction: column;
        gap: 0.75rem;
    }
    
    .question-text {
        font-size: 1.0625rem;
    }
    
    .option-item {
        padding: 0.875rem 1rem;
    }
}

@media (max-width: 576px) {
    .quiz-stats {
        flex-direction: column;
        gap: 1rem;
    }
}
</style>

<div class="quiz-wrapper">
    <!-- Quiz Header -->
    <div id="quiz-header" class="quiz-info-card">
        <div class="quiz-icon">
            <i class="fas fa-question-circle"></i>
        </div>
        <h2 class="quiz-info-title"><?php echo $lesson_details['title']; ?></h2>
        <p class="quiz-info-subtitle"><?php echo get_phrase('test_your_knowledge'); ?></p>
        
        <div class="quiz-stats">
            <div class="quiz-stat">
                <span class="quiz-stat-value"><?php echo $question_count; ?></span>
                <span class="quiz-stat-label"><?php echo get_phrase('questions'); ?></span>
            </div>
            <div class="quiz-stat">
                <span class="quiz-stat-value">15s</span>
                <span class="quiz-stat-label"><?php echo get_phrase('per_question'); ?></span>
            </div>
        </div>
        
        <?php if ($question_count > 0): ?>
        <div class="quiz-actions">
            <button type="button" class="quiz-btn quiz-btn-primary" onclick="getStarted(1);">
                <i class="fas fa-play"></i>
                <?php echo get_phrase('start_quiz'); ?>
            </button>
            <button type="button" class="quiz-btn quiz-btn-secondary" onclick="check_result();">
                <i class="fas fa-chart-bar"></i>
                <?php echo get_phrase('check_result'); ?>
            </button>
        </div>
        <?php else: ?>
        <p class="text-muted"><?php echo get_phrase('no_questions_available'); ?></p>
        <?php endif; ?>
    </div>

    <!-- Quiz Form -->
    <form id="quiz_form" action="" method="post">
        <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>" />
        <input type="hidden" name="overwrite_results" value="1" />
        <input type="hidden" name="lesson_id" value="<?php echo $lesson_details['id']; ?>">
        
        <?php if ($question_count > 0): ?>
            <?php foreach ($quiz_questions->result_array() as $key => $quiz_question):
                $options = json_decode($quiz_question['options']);
                $question_num = $key + 1;
                $is_last = ($question_count == $question_num);
            ?>
            <div class="hidden" id="question-number-<?php echo $question_num; ?>">
                <div class="question-card">
                    <!-- Question Header -->
                    <div class="question-header">
                        <span class="question-number-badge">
                            <i class="fas fa-bookmark"></i>
                            <?php echo get_phrase('question'); ?> <?php echo $question_num; ?>/<?php echo $question_count; ?>
                        </span>
                        <span class="question-timer">
                            <i class="fas fa-clock"></i>
                            <span id="timer<?php echo $question_num; ?>">00:15</span>
                        </span>
                    </div>
                    
                    <!-- Question Body -->
                    <div class="question-body">
                        <!-- Timer Progress -->
                        <div class="timer-progress">
                            <div class="timer-progress-bar" id="timer-bar-<?php echo $question_num; ?>"></div>
                        </div>
                        
                        <!-- Question Text -->
                        <p class="question-text"><?php echo $quiz_question['title']; ?></p>
                        
                        <!-- Options -->
                        <div class="options-header">
                            <i class="fas fa-hand-pointer"></i>
                            <?php echo get_phrase('choose_your_answer'); ?>
                        </div>
                        
                        <div class="options-list">
                            <?php foreach ($options as $key2 => $option): ?>
                            <label class="option-item" for="quiz-<?php echo $quiz_question['id']; ?>-opt-<?php echo $key2 + 1; ?>" onclick="selectOption(this)">
                                <input type="checkbox" 
                                       name="<?php echo $quiz_question['id']; ?>[]" 
                                       value="<?php echo $key2 + 1; ?>"
                                       id="quiz-<?php echo $quiz_question['id']; ?>-opt-<?php echo $key2 + 1; ?>"
                                       onclick="enableNextButton('<?php echo $quiz_question['id']; ?>')">
                                <span class="option-checkbox"></span>
                                <span class="option-text"><?php echo $option; ?></span>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <!-- Question Footer -->
                    <div class="question-footer">
                        <button type="button" 
                                class="quiz-btn quiz-btn-primary" 
                                id="next-btn-<?php echo $quiz_question['id']; ?>"
                                <?php if ($is_last): ?>
                                    onclick="submitQuiz()"
                                <?php else: ?>
                                    onclick="showNextQuestion('<?php echo $question_num + 1; ?>');"
                                <?php endif; ?>
                                disabled
                                data-question-id="<?php echo $quiz_question['id']; ?>">
                            <?php if ($is_last): ?>
                                <i class="fas fa-check-circle"></i>
                                <?php echo get_phrase('submit_quiz'); ?>
                            <?php else: ?>
                                <?php echo get_phrase('next'); ?>
                                <i class="fas fa-arrow-right"></i>
                            <?php endif; ?>
                        </button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </form>
    
    <!-- Results Container -->
    <div id="quiz-result"></div>
</div>

<script>
// Timer management
let timers = {};
const totalQuestions = <?php echo $question_count; ?>;

function selectOption(element) {
    // Toggle selected class
    element.classList.toggle('selected');
}

function enableNextButton(questionId) {
    const button = document.getElementById('next-btn-' + questionId);
    button.disabled = false;
}

function stopAllTimers() {
    Object.keys(timers).forEach(function(key) {
        clearInterval(timers[key]);
    });
    timers = {};
}

function startTimer(index) {
    stopAllTimers();
    let timerSpan = document.getElementById('timer' + index);
    let timerBar = document.getElementById('timer-bar-' + index);
    let timeLeft = 15;
    
    timerSpan.textContent = '00:' + (timeLeft < 10 ? '0' + timeLeft : timeLeft);
    timerBar.style.animation = 'none';
    timerBar.offsetHeight; // Trigger reflow
    timerBar.style.animation = 'timerCountdown 15s linear forwards';

    let timer = setInterval(function() {
        if (timeLeft <= 0) {
            clearInterval(timer);
            delete timers[index];
            
            if (index < totalQuestions) {
                showNextQuestion(index + 1);
            } else {
                submitQuiz();
            }
        } else {
            timerSpan.textContent = '00:' + (timeLeft < 10 ? '0' + timeLeft : timeLeft);
            timeLeft--;
        }
    }, 1000);
    
    timers[index] = timer;
}

function showNextQuestion(nextQuestionNumber) {
    // Hide all questions
    document.querySelectorAll('#quiz_form > div').forEach(function(question) {
        question.classList.add('hidden');
    });
    
    // Show next question
    const nextQuestion = document.getElementById('question-number-' + nextQuestionNumber);
    if (nextQuestion) {
        nextQuestion.classList.remove('hidden');
        startTimer(nextQuestionNumber);
    }
}

function getStarted(questionNumber) {
    document.getElementById('quiz-header').style.display = 'none';
    document.getElementById('question-number-' + questionNumber).classList.remove('hidden');
    startTimer(questionNumber);
}

function submitQuiz() {
    stopAllTimers();
    let form = document.getElementById('quiz_form');
    let submitButton = form.querySelector('button:not([disabled])');
    if (submitButton) {
        submitButton.disabled = true;
    }
    form.submit();
}

function retakeQuiz() {
    window.location.reload();
}

function check_result() {
    fetch('/quiz/results?lesson_id=<?php echo $lesson_details['id']; ?>', {
        method: 'GET',
        headers: {
            'X-CSRF-Token': '<?php echo $this->security->get_csrf_hash(); ?>'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network error: ' + response.status);
        }
        return response.json();
    })
    .then(data => {
        let quizResult = document.getElementById('quiz-result');
        if (data.error) {
            quizResult.innerHTML = `<div class="quiz-info-card"><p class="text-muted">${data.error}</p></div>`;
            return;
        }
        
        quizResult.innerHTML = `
            <div class="quiz-info-card" style="margin-top: 1.5rem;">
                <div class="quiz-icon" style="background: linear-gradient(135deg, #d1fae5, #a7f3d0);">
                    <i class="fas fa-trophy" style="color: #059669;"></i>
                </div>
                <h2 class="quiz-info-title"><?php echo get_phrase('your_results'); ?></h2>
                <div class="quiz-stats">
                    <div class="quiz-stat">
                        <span class="quiz-stat-value" style="color: ${data.score >= 50 ? '#059669' : '#ef4444'};">${data.score}%</span>
                        <span class="quiz-stat-label"><?php echo get_phrase('score'); ?></span>
                    </div>
                    <div class="quiz-stat">
                        <span class="quiz-stat-value">${data.correct_answers}/${data.total_questions}</span>
                        <span class="quiz-stat-label"><?php echo get_phrase('correct'); ?></span>
                    </div>
                </div>
                <div class="quiz-actions">
                    <button type="button" class="quiz-btn quiz-btn-primary" onclick="retakeQuiz()">
                        <i class="fas fa-redo-alt"></i>
                        <?php echo get_phrase('retake_quiz'); ?>
                    </button>
                </div>
            </div>
        `;
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while fetching results: ' + error.message);
    });
}
</script>
