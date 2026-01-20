<style>
/* ========== MODERN QUIZ RESULT STYLES ========== */
.result-wrapper {
    max-width: 800px;
    margin: 0 auto;
}

/* Result Header Card */
.result-header-card {
    background: var(--bg-card);
    border-radius: var(--radius-xl);
    border: 1px solid var(--border-color);
    box-shadow: var(--shadow-md);
    padding: 2rem;
    text-align: center;
    margin-bottom: 1.5rem;
    animation: fadeUp 0.4s ease;
}

.result-icon {
    width: 100px;
    height: 100px;
    margin: 0 auto 1.5rem;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.5rem;
    position: relative;
}

.result-icon.success {
    background: linear-gradient(135deg, #d1fae5, #a7f3d0);
    color: #059669;
}

.result-icon.warning {
    background: linear-gradient(135deg, #fef3c7, #fde68a);
    color: #d97706;
}

.result-icon.error {
    background: linear-gradient(135deg, #fee2e2, #fecaca);
    color: #dc2626;
}

.result-icon::after {
    content: '';
    position: absolute;
    inset: -8px;
    border-radius: 50%;
    border: 2px dashed currentColor;
    opacity: 0.3;
    animation: spin 20s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.result-title {
    font-family: var(--font-header);
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--text-dark);
    margin-bottom: 0.5rem;
}

.result-subtitle {
    color: var(--text-muted);
    font-size: 0.9375rem;
    margin-bottom: 1.5rem;
    line-height: 1.6;
}

.result-stats {
    display: flex;
    justify-content: center;
    gap: 2rem;
    margin-bottom: 2rem;
    padding: 1.25rem;
    background: var(--bg-main);
    border-radius: var(--radius-lg);
}

.result-stat {
    text-align: center;
}

.result-stat-value {
    display: block;
    font-family: var(--font-header);
    font-size: 2rem;
    font-weight: 700;
    line-height: 1;
}

.result-stat-value.success { color: #059669; }
.result-stat-value.warning { color: #d97706; }
.result-stat-value.error { color: #dc2626; }
.result-stat-value.primary { color: var(--primary); }

.result-stat-label {
    font-size: 0.8125rem;
    color: var(--text-muted);
    margin-top: 0.25rem;
}

.result-actions {
    display: flex;
    justify-content: center;
    gap: 1rem;
    flex-wrap: wrap;
}

.result-btn {
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

.result-btn-primary {
    background: linear-gradient(135deg, var(--primary), var(--primary-light));
    color: white;
    box-shadow: 0 4px 14px rgba(99, 102, 241, 0.35);
}

.result-btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(99, 102, 241, 0.45);
    color: white;
}

/* Answer Review Cards */
.answers-section {
    margin-top: 2rem;
}

.answers-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 1.25rem;
    padding-bottom: 0.75rem;
    border-bottom: 2px solid var(--border-color);
}

.answers-header i {
    color: var(--primary);
    font-size: 1.25rem;
}

.answers-header h3 {
    font-family: var(--font-header);
    font-size: 1.125rem;
    font-weight: 700;
    color: var(--text-dark);
    margin: 0;
}

.answer-card {
    background: var(--bg-card);
    border-radius: var(--radius-lg);
    border: 1px solid var(--border-color);
    box-shadow: var(--shadow-sm);
    margin-bottom: 1rem;
    overflow: hidden;
    animation: fadeUp 0.3s ease;
    animation-fill-mode: backwards;
}

.answer-card:nth-child(1) { animation-delay: 0.05s; }
.answer-card:nth-child(2) { animation-delay: 0.1s; }
.answer-card:nth-child(3) { animation-delay: 0.15s; }
.answer-card:nth-child(4) { animation-delay: 0.2s; }
.answer-card:nth-child(5) { animation-delay: 0.25s; }

.answer-card.correct {
    border-left: 4px solid #059669;
}

.answer-card.incorrect {
    border-left: 4px solid #dc2626;
}

.answer-header {
    padding: 1rem 1.25rem;
    background: var(--bg-main);
    border-bottom: 1px solid var(--border-color);
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
}

.answer-status-icon {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.875rem;
    flex-shrink: 0;
}

.answer-status-icon.correct {
    background: #d1fae5;
    color: #059669;
}

.answer-status-icon.incorrect {
    background: #fee2e2;
    color: #dc2626;
}

.answer-question {
    flex: 1;
    font-size: 0.9375rem;
    font-weight: 600;
    color: var(--text-dark);
    line-height: 1.5;
}

.answer-body {
    padding: 1.25rem;
}

.answer-section {
    margin-bottom: 1rem;
}

.answer-section:last-child {
    margin-bottom: 0;
}

.answer-label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.8125rem;
    font-weight: 600;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 0.5rem;
}

.answer-label i {
    font-size: 0.875rem;
}

.answer-label.correct i { color: #059669; }
.answer-label.submitted i { color: var(--primary); }

.answer-values {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.answer-tag {
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
    padding: 0.5rem 0.875rem;
    border-radius: var(--radius-sm);
    font-size: 0.875rem;
    font-weight: 500;
}

.answer-tag.correct {
    background: #d1fae5;
    color: #065f46;
}

.answer-tag.submitted {
    background: var(--primary-lighter);
    color: var(--primary-dark);
}

/* Responsive */
@media (max-width: 768px) {
    .result-header-card {
        padding: 1.5rem;
    }
    
    .result-icon {
        width: 80px;
        height: 80px;
        font-size: 2rem;
    }
    
    .result-title {
        font-size: 1.25rem;
    }
    
    .result-stats {
        flex-direction: column;
        gap: 1rem;
    }
    
    .result-stat-value {
        font-size: 1.75rem;
    }
    
    .result-actions {
        flex-direction: column;
    }
    
    .result-btn {
        width: 100%;
        justify-content: center;
    }
    
    .answer-header {
        flex-direction: column;
        gap: 0.5rem;
    }
}
</style>

<?php
// Calculate score percentage
$score_percent = $total_questions > 0 ? round(($total_correct_answers / $total_questions) * 100) : 0;

// Determine result type
$result_type = 'error';
$result_icon = 'fas fa-times-circle';
if ($score_percent >= 80) {
    $result_type = 'success';
    $result_icon = 'fas fa-trophy';
} elseif ($score_percent >= 50) {
    $result_type = 'warning';
    $result_icon = 'fas fa-star-half-alt';
}
?>

<div class="result-wrapper">
    <!-- Result Header -->
    <div class="result-header-card">
        <div class="result-icon <?php echo $result_type; ?>">
            <i class="<?php echo $result_icon; ?>"></i>
        </div>
        
        <h2 class="result-title">
            <?php 
            if ($score_percent >= 80) {
                echo get_phrase('excellent_work');
            } elseif ($score_percent >= 50) {
                echo get_phrase('good_effort');
            } else {
                echo get_phrase('keep_practicing');
            }
            ?>
        </h2>
        
        <p class="result-subtitle">
            <?php echo get_phrase('review_the_course_materials_to_expand_your_learning'); ?>
        </p>
        
        <div class="result-stats">
            <div class="result-stat">
                <span class="result-stat-value <?php echo $result_type; ?>"><?php echo $score_percent; ?>%</span>
                <span class="result-stat-label"><?php echo get_phrase('score'); ?></span>
            </div>
            <div class="result-stat">
                <span class="result-stat-value primary"><?php echo $total_correct_answers; ?>/<?php echo $total_questions; ?></span>
                <span class="result-stat-label"><?php echo get_phrase('correct_answers'); ?></span>
            </div>
        </div>
        
        <div class="result-actions">
            <a href="<?php echo site_url('addons/lessons/play/'.$course_slug.'/'.$course_id.'/'.$quiz_id); ?>" class="result-btn result-btn-primary">
                <i class="fas fa-redo-alt"></i>
                <?php echo get_phrase('retake_quiz'); ?>
            </a>
        </div>
    </div>
    
    <!-- Answers Review -->
    <div class="answers-section">
        <div class="answers-header">
            <i class="fas fa-clipboard-list"></i>
            <h3><?php echo get_phrase('detailed_results'); ?></h3>
        </div>
        
        <?php foreach ($submitted_quiz_info as $index => $each):
            $question_details = $this->lms_model->get_quiz_question_by_id($each['question_id'])->row_array();
            $options = json_decode($question_details['options']);
            $correct_answers = json_decode($each['correct_answers']);
            $submitted_answers = json_decode($each['submitted_answers']);
            $is_correct = $each['submitted_answer_status'] == 1;
        ?>
        <div class="answer-card <?php echo $is_correct ? 'correct' : 'incorrect'; ?>" style="animation-delay: <?php echo ($index * 0.05); ?>s;">
            <!-- Question Header -->
            <div class="answer-header">
                <span class="answer-status-icon <?php echo $is_correct ? 'correct' : 'incorrect'; ?>">
                    <i class="fas <?php echo $is_correct ? 'fa-check' : 'fa-times'; ?>"></i>
                </span>
                <span class="answer-question">
                    <?php echo get_phrase('question'); ?> <?php echo $index + 1; ?>: <?php echo $question_details['title']; ?>
                </span>
            </div>
            
            <!-- Answer Body -->
            <div class="answer-body">
                <!-- Correct Answers -->
                <div class="answer-section">
                    <div class="answer-label correct">
                        <i class="fas fa-check-circle"></i>
                        <?php echo get_phrase('correct_answers'); ?>
                    </div>
                    <div class="answer-values">
                        <?php for ($i = 0; $i < count($correct_answers); $i++): ?>
                            <span class="answer-tag correct">
                                <i class="fas fa-check"></i>
                                <?php echo $options[($correct_answers[$i] - 1)]; ?>
                            </span>
                        <?php endfor; ?>
                    </div>
                </div>
                
                <!-- Submitted Answers -->
                <div class="answer-section">
                    <div class="answer-label submitted">
                        <i class="fas fa-reply"></i>
                        <?php echo get_phrase('your_answers'); ?>
                    </div>
                    <div class="answer-values">
                        <?php if (count($submitted_answers) > 0): ?>
                            <?php for ($i = 0; $i < count($submitted_answers); $i++): ?>
                                <span class="answer-tag submitted">
                                    <?php echo $options[($submitted_answers[$i] - 1)]; ?>
                                </span>
                            <?php endfor; ?>
                        <?php else: ?>
                            <span class="answer-tag submitted" style="background: #fee2e2; color: #991b1b;">
                                <i class="fas fa-minus-circle"></i>
                                <?php echo get_phrase('no_answer'); ?>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
