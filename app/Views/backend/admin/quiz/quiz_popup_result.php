<style>
:root {
    --qr-primary: #6366f1;
    --qr-primary-rgb: 99, 102, 241;
    --qr-success: #10b981;
    --qr-danger: #ef4444;
    --qr-dark: #1e293b;
    --qr-gray: #64748b;
    --qr-light: #f8fafc;
    --qr-border: #e2e8f0;
}

.qr-container { padding: 0.5rem; }

.qr-score-card { display: flex; align-items: center; gap: 1rem; padding: 1.25rem; background: linear-gradient(135deg, rgba(var(--qr-primary-rgb), 0.1), rgba(var(--qr-primary-rgb), 0.05)); border-radius: 16px; margin-bottom: 1.5rem; border: 1px solid rgba(var(--qr-primary-rgb), 0.2); }
.qr-score-icon { width: 60px; height: 60px; border-radius: 14px; background: linear-gradient(135deg, var(--qr-primary), #8b5cf6); display: flex; align-items: center; justify-content: center; font-size: 1.75rem; color: white; }
.qr-score-data h3 { margin: 0 0 0.25rem; font-size: 1.5rem; font-weight: 700; color: var(--qr-dark); }
.qr-score-data p { margin: 0; font-size: 0.875rem; color: var(--qr-gray); }

.qr-table { width: 100%; border-collapse: separate; border-spacing: 0; }
.qr-table thead { background: var(--qr-light); }
.qr-table th { padding: 0.875rem 1rem; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: var(--qr-gray); text-align: left; border-bottom: 2px solid var(--qr-border); }
.qr-table th:first-child { border-radius: 10px 0 0 0; }
.qr-table th:last-child { border-radius: 0 10px 0 0; }
.qr-table td { padding: 1rem; border-bottom: 1px solid var(--qr-border); font-size: 0.875rem; color: var(--qr-dark); vertical-align: top; }
.qr-table tr:last-child td { border-bottom: none; }
.qr-table tr:hover td { background: rgba(var(--qr-primary-rgb), 0.02); }

.qr-question { font-weight: 500; line-height: 1.5; }
.qr-answer { display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.25rem 0.625rem; background: var(--qr-light); border-radius: 6px; font-size: 0.8125rem; margin: 0.125rem; }

.qr-status { display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.375rem 0.75rem; border-radius: 20px; font-size: 0.8125rem; font-weight: 600; }
.qr-status.correct { background: rgba(16, 185, 129, 0.1); color: var(--qr-success); }
.qr-status.incorrect { background: rgba(239, 68, 68, 0.1); color: var(--qr-danger); }
</style>

<div class="qr-container">
    <!-- Score Card -->
    <div class="qr-score-card">
        <div class="qr-score-icon">
            <i class="mdi mdi-trophy"></i>
        </div>
        <div class="qr-score-data">
            <h3><?php echo $total_correct_answers; ?> / <?php echo $total_questions; ?></h3>
            <p><?php echo get_phrase('you_got') . ' ' . $total_correct_answers . ' ' . get_phrase('out_of') . ' ' . $total_questions . ' ' . get_phrase('correct'); ?></p>
        </div>
    </div>

    <!-- Results Table -->
    <table class="qr-table">
        <thead>
            <tr>
                <th><i class="mdi mdi-help-circle-outline"></i> <?php echo get_phrase('question'); ?></th>
                <th><i class="mdi mdi-check-circle-outline"></i> <?php echo get_phrase('correct_answers'); ?></th>
                <th><i class="mdi mdi-account-check"></i> <?php echo get_phrase('submit_answers'); ?></th>
                <th><i class="mdi mdi-flag"></i> <?php echo get_phrase('status'); ?></th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($submitted_quiz_info as $info): ?>
            <tr>
                <td><span class="qr-question"><?php echo $info['question_title']; ?></span></td>
                <td>
                    <?php 
                    $correct = json_decode($info['correct_answers']);
                    foreach ($correct as $ans): ?>
                    <span class="qr-answer"><?php echo $ans; ?></span>
                    <?php endforeach; ?>
                </td>
                <td>
                    <?php 
                    $submitted = json_decode($info['submitted_answers']);
                    foreach ($submitted as $ans): ?>
                    <span class="qr-answer"><?php echo $ans; ?></span>
                    <?php endforeach; ?>
                </td>
                <td>
                    <?php if($info['submitted_answer_status']): ?>
                    <span class="qr-status correct">
                        <i class="mdi mdi-check"></i> <?php echo get_phrase('correct'); ?>
                    </span>
                    <?php else: ?>
                    <span class="qr-status incorrect">
                        <i class="mdi mdi-close"></i> <?php echo get_phrase('incorrect'); ?>
                    </span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
