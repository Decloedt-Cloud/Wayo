<?php
$exams_per_page = isset($exams_per_page) ? (int) $exams_per_page : 10;
$current_page = isset($current_page) ? (int) $current_page : 1;
$class_id = isset($filter_class_id) ? (string) $filter_class_id : '';
$date_range = isset($filter_date_range) ? (string) $filter_date_range : '';
$classes = isset($classes) && is_array($classes) ? $classes : [];
$exams = isset($exams) && is_array($exams) ? $exams : [];
$total_exams = isset($total_exams) ? (int) $total_exams : count($exams);
$total_pages = isset($total_pages) ? (int) $total_pages : ($exams_per_page > 0 ? (int) ceil($total_exams / $exams_per_page) : 1);
$student_not_found = isset($student_not_found) ? (bool) $student_not_found : false;
$to_timestamp = static function ($raw): int {
    if (is_numeric($raw)) {
        return (int) $raw;
    }
    $ts = strtotime((string) $raw);
    return $ts ?: 0;
};
if ($student_not_found): ?>
    <div class="alert alert-danger"><?php echo get_phrase('student_not_found'); ?></div>
    <a href="<?php echo site_url('student/student'); ?>" class="btn btn-primary"><?php echo get_phrase('go_back'); ?></a>
    <?php return; ?>
<?php endif; ?>

<!-- Filter Form -->
<div class="outline-filter">
    <form id="filterForm">
        <div class="filter-row">
            <!-- Class Dropdown -->
            <div class="filter-group">
                <label for="class_id"><?php echo get_phrase('class'); ?></label>
                <select name="class_id" id="class_id" class="form-control filter-select">
                    <option value=""><?php echo get_phrase('select_class'); ?></option>
                    <?php
                    foreach ($classes as $class) {
                        $selected = ($class['id'] == $class_id) ? 'selected' : '';
                        echo "<option value='{$class['id']}' $selected>{$class['name']}</option>";
                    }
                    ?>
                </select>
            </div>
            
            <!-- Date Range Picker -->
            <div class="filter-group">
                <label for="date_range"><?php echo get_phrase('certification_date'); ?></label>
                <input type="text" name="date_range" id="date_range" class="form-control filter-date" value="<?php echo $date_range; ?>" placeholder="Select Date Range">
            </div>
            
            <!-- Search Button -->
            <div class="filter-group filter-actions">
                <button type="submit" class="btn btn-primary filter-btn">
                    <i class="fas fa-search"></i> <?php echo get_phrase('search'); ?>
                </button>
                <button type="button" class="btn btn-outline-secondary filter-btn" onclick="resetFilters()">
                    <i class="fas fa-undo"></i> <?php echo get_phrase('reset'); ?>
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Certifications Counter -->
<div class="exams-counter">
    <span class="counter-icon"><i class="fas fa-certificate"></i></span>
    <span class="counter-text">
        <span id="totalExamsCount"><?php echo $total_exams; ?></span> <?php echo get_phrase('certifications'); ?>
    </span>
</div>

<!-- Certifications Outline -->
<div class="exams-outline">
    <?php if (count($exams) > 0): ?>
        <div class="exams-list">
            <?php foreach ($exams as $exam): ?>
                <?php
                $exam_name = trim((string) ($exam['name'] ?? ''));
                $exam_name = $exam_name !== '' ? $exam_name : get_phrase('unnamed_certification');
                $exam_timestamp = $to_timestamp($exam['starting_date'] ?? null);
                $formatted_date = $exam_timestamp > 0 ? date('D, d-M-Y H:i', $exam_timestamp) : get_phrase('no_date');
                ?>
                <div class="exam-item" data-exam-id="<?php echo $exam['id']; ?>">
                    <div class="exam-info">
                        <i class="fas fa-certificate exam-icon"></i>
                        <div class="exam-details">
                            <div class="exam-name"><?php echo html_escape($exam_name); ?></div>
                            <div class="exam-meta">
                                <span class="exam-date">
                                    <i class="fas fa-calendar"></i> <?php echo $formatted_date; ?>
                                </span>
                                <span class="exam-class">
                                    <i class="fas fa-chalkboard"></i> <?php echo !empty($exam['class_name']) ? html_escape($exam['class_name']) : get_phrase('no_class'); ?>
                                </span>
                            </div>
                            <!-- Compteur pour l'examen -->
                            <?php
                            // Définir already_passed avant de l'utiliser dans l'attribut HTML
                            $already_passed = isset($exam['already_passed']) && $exam['already_passed'] > 0;
                            $now = time();
                            $exam_time = $exam_timestamp;
                            $time_diff = $exam_time - $now;
                            ?>
                            <div class="exam-timer" id="exam-timer-<?php echo $exam['id']; ?>" data-exam-date="<?php echo $exam_timestamp; ?>" data-already-passed="<?php echo $already_passed ? '1' : '0'; ?>">
                                <?php
                                // Debug - afficher visiblement pour tester
                                // if ($already_passed) {
                                //     echo "<div style='background: yellow; padding: 5px; margin: 5px 0;'>DEBUG: Exam {$exam['id']} already passed, count = {$exam['already_passed']}</div>";
                                // }
                                
                                if ($time_diff > 0) {
                                    // Calculer les jours, heures, minutes, secondes
                                    $days = floor($time_diff / (60 * 60 * 24));
                                    $hours = floor(($time_diff % (60 * 60 * 24)) / (60 * 60));
                                    $minutes = floor(($time_diff % (60 * 60)) / 60);
                                    $seconds = $time_diff % 60;
                                    
                                    // Déterminer si on a besoin d'un affichage compact (quand il y a des jours)
                                    $has_days = $days > 0;
                                    $timer_class = $has_days ? ' has-days' : '';
                                    
                                    echo '<div class="timer-display' . $timer_class . '">';
                                    echo '<span class="timer-label">' . get_phrase('starts_in') . ':</span> ';
                                    echo '<span class="timer-value">';
                                    if ($days > 0) echo '<span class="timer-unit">' . $days . 'j</span> ';
                                    echo '<span class="timer-unit">' . str_pad($hours, 2, '0', STR_PAD_LEFT) . 'h</span> ';
                                    echo '<span class="timer-unit">' . str_pad($minutes, 2, '0', STR_PAD_LEFT) . 'm</span> ';
                                    echo '<span class="timer-unit">' . str_pad($seconds, 2, '0', STR_PAD_LEFT) . 's</span>';
                                    echo '</span>';
                                    echo '</div>';
                                } else {
                                    // L'examen a déjà commencé ou est terminé
                                    if ($already_passed) {
                                        // L'étudiant a déjà passé l'examen
                                        echo '<div class="exam-status">';
                                        echo '<span class="exam-already-passed">';
                                        echo '<i class="fas fa-check-circle"></i> ' . get_phrase('exam_already_submitted');
                                        echo '</span>';
                                        echo '</div>';
                                    } else {
                                        // L'examen est disponible
                                        echo '<div class="exam-action">';
                                        echo '<a href="' . site_url('student/online_exam/' . $exam['id']) . '" class="btn-start-exam">';
                                        echo '<i class="fas fa-play-circle"></i> ' . get_phrase('start_exam');
                                        echo '</a>';
                                        echo '</div>';
                                    }
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    <!-- Les étudiants ne peuvent pas modifier ou supprimer des examens -->
                    <!-- Pas d'actions disponibles pour les étudiants -->
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-certificate"></i>
            <p><?php echo get_phrase('no_certifications_found'); ?></p>
        </div>
    <?php endif; ?>
</div>

<?php if ($total_exams > $exams_per_page): ?>
<!-- Pagination -->
<div class="exam-pagination">
    <button type="button" class="pagination-btn" id="prevPage" onclick="loadExamsPage(currentExamPage - 1)" disabled>
        <i class="fas fa-chevron-left"></i>
    </button>
    <span class="pagination-info">
        <span id="currentPageNum">1</span> / <span id="totalPagesNum"><?php echo $total_pages; ?></span>
    </span>
    <button type="button" class="pagination-btn" id="nextPage" onclick="loadExamsPage(currentExamPage + 1)" <?php echo $total_pages <= 1 ? 'disabled' : ''; ?>>
        <i class="fas fa-chevron-right"></i>
    </button>
</div>
<?php endif; ?>

<script>
// Pagination variables
var currentExamPage = 1;
var totalExamPages = <?php echo $total_pages; ?>;
var examsPerPage = <?php echo $exams_per_page; ?>;

// Function to reset filters
function resetFilters() {
    $('#class_id').val('');
    $('#date_range').val('');
    currentExamPage = 1;
    loadExamsPage(1);
}

// Function to delete exam
function deleteExam(url) {
    Swal.fire({
        title: '<?php echo addslashes(get_phrase('are_you_sure')); ?>',
        text: '<?php echo addslashes(get_phrase('you_will_not_be_able_to_revert_this')); ?>',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#6366f1',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<?php echo addslashes(get_phrase('yes_delete_it')); ?>',
        cancelButtonText: '<?php echo addslashes(get_phrase('cancel')); ?>',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: url,
                type: 'POST',
                dataType: 'json',
                data: {
                    '<?php echo csrf_token(); ?>': '<?php echo csrf_hash(); ?>'
                },
                success: function(response) {
                    // Reload current page
                    loadExamsPage(currentExamPage);
                },
                error: function(xhr, status, error) {
                    console.error('Delete error:', error);
                }
            });
        }
    });
}

// Function to load exams page via AJAX
function loadExamsPage(page) {
    if (page < 1 || page > totalExamPages) return;
    
    var classId = $('#class_id').val();
    var dateRange = $('#date_range').val();
    
    $.ajax({
        url: '<?php echo site_url('student/get_exams_paginated'); ?>',
        type: 'GET',
        data: {
            page: page,
            class_id: classId,
            date_range: dateRange
        },
        dataType: 'json',
        beforeSend: function() {
            $('.exams-outline').html('<div class="text-center py-4"><i class="fas fa-spinner fa-spin fa-2x text-primary"></i></div>');
        },
        success: function(response) {
            if (response.status && response.exams) {
                updateExamsList(response.exams);
                currentExamPage = response.current_page;
                totalExamPages = response.total_pages;
                updatePaginationUI();
                // Update total exams counter
                if (response.total_exams !== undefined) {
                    $('#totalExamsCount').text(response.total_exams);
                }
            }
        },
        error: function(xhr, status, error) {
            console.error('Pagination Error:', error);
        }
    });
}

// Function to update pagination UI
function updatePaginationUI() {
    $('#currentPageNum').text(currentExamPage);
    $('#totalPagesNum').text(totalExamPages);
    
    $('#prevPage').prop('disabled', currentExamPage <= 1);
    $('#nextPage').prop('disabled', currentExamPage >= totalExamPages);
    
    // Show/hide pagination based on total pages
    if (totalExamPages > 1) {
        $('.exam-pagination').show();
    } else {
        $('.exam-pagination').hide();
    }
}

// Function to update exams list
function updateExamsList(exams) {
    var examsHtml = '';

    function parseExamTimestamp(raw) {
        if (raw === null || raw === undefined || raw === '') return 0;
        if (!isNaN(raw)) return parseInt(raw, 10);
        var parsed = Date.parse(String(raw).replace(' ', 'T'));
        return isNaN(parsed) ? 0 : Math.floor(parsed / 1000);
    }
    
    if (exams && Array.isArray(exams) && exams.length > 0) {
        examsHtml = '<div class="exams-list">';
        
        $.each(exams, function(index, exam) {
            // Calculer le temps restant
            var now = Math.floor(Date.now() / 1000);
            var examTime = parseExamTimestamp(exam.starting_timestamp || exam.starting_date);
            var timeDiff = examTime - now;
            
            var timerHtml = '';
            if (timeDiff > 0) {
                // Calculer les jours, heures, minutes, secondes
                var days = Math.floor(timeDiff / (60 * 60 * 24));
                var hours = Math.floor((timeDiff % (60 * 60 * 24)) / (60 * 60));
                var minutes = Math.floor((timeDiff % (60 * 60)) / 60);
                var seconds = timeDiff % 60;
                
                // Déterminer si on a besoin d'un affichage compact (quand il y a des jours)
                var hasDays = days > 0;
                var timerClass = hasDays ? ' has-days' : '';
                
                timerHtml = `
                    <div class="exam-timer" id="exam-timer-${exam.id}" data-exam-date="${examTime}" data-already-passed="${exam.already_passed ? '1' : '0'}">
                        <div class="timer-display${timerClass}">
                            <span class="timer-label"><?php echo get_phrase('starts_in'); ?>:</span>
                            <span class="timer-value">
                                ${days > 0 ? '<span class="timer-unit">' + days + 'j</span> ' : ''}
                                <span class="timer-unit">${hours.toString().padStart(2, '0')}h</span>
                                <span class="timer-unit">${minutes.toString().padStart(2, '0')}m</span>
                                <span class="timer-unit">${seconds.toString().padStart(2, '0')}s</span>
                            </span>
                        </div>
                    </div>
                `;
            } else {
                // L'examen a déjà commencé ou est terminé
                if (exam.already_passed) {
                    // L'étudiant a déjà passé l'examen
                    timerHtml = `
                        <div class="exam-status">
                            <span class="exam-already-passed">
                                <i class="fas fa-check-circle"></i> <?php echo get_phrase('exam_already_submitted'); ?>
                            </span>
                        </div>
                    `;
                } else {
                    // L'examen est disponible
                    timerHtml = `
                        <div class="exam-action">
                            <a href="<?php echo site_url('student/online_exam/'); ?>${exam.id}" class="btn-start-exam">
                                <i class="fas fa-play-circle"></i> <?php echo get_phrase('start_exam'); ?>
                            </a>
                        </div>
                    `;
                }
            }
            
            examsHtml += `
                <div class="exam-item" data-exam-id="${exam.id}">
                    <div class="exam-info">
                        <i class="fas fa-certificate exam-icon"></i>
                        <div class="exam-details">
                            <div class="exam-name">${exam.name || '<?php echo get_phrase('unnamed_certification'); ?>'}</div>
                            <div class="exam-meta">
                                <span class="exam-date">
                                    <i class="fas fa-calendar"></i> ${exam.formatted_date || 'No Date'}
                                </span>
                                <span class="exam-class">
                                    <i class="fas fa-chalkboard"></i> ${exam.class_name || '<?php echo get_phrase('no_class'); ?>'}
                                </span>
                            </div>
                            ${timerHtml}
                        </div>
                    </div>
                    <!-- Les étudiants ne peuvent pas modifier ou supprimer des examens -->
                    <!-- Pas d'actions disponibles pour les étudiants -->
                </div>
            `;
        });
        
        examsHtml += '</div>';
    } else {
        examsHtml = `
            <div class="empty-state">
                <i class="fas fa-certificate"></i>
                <p><?php echo get_phrase('no_certifications_found'); ?></p>
                <!-- Les étudiants ne peuvent pas créer d'examens -->
            </div>
        `;
    }
    
    $('.exams-outline').html(examsHtml);
    // Démarrer la mise à jour des compteurs
    startExamTimers();
}

// Function to update exam timers
function updateExamTimers() {
    var now = Math.floor(Date.now() / 1000);
    
    $('.exam-timer').each(function() {
        var $timer = $(this);
        var examId = $timer.attr('id').replace('exam-timer-', '');
        var examTime = parseInt($timer.data('exam-date'));
        var timeDiff = examTime - now;
        
        if (timeDiff > 0) {
            // Calculer les jours, heures, minutes, secondes
            var days = Math.floor(timeDiff / (60 * 60 * 24));
            var hours = Math.floor((timeDiff % (60 * 60 * 24)) / (60 * 60));
            var minutes = Math.floor((timeDiff % (60 * 60)) / 60);
            var seconds = timeDiff % 60;
            
            // Déterminer si on a besoin d'un affichage compact (quand il y a des jours)
            var hasDays = days > 0;
            var timerClass = hasDays ? ' has-days' : '';
            
            var timerHtml = `
                <div class="timer-display${timerClass}">
                    <span class="timer-label"><?php echo get_phrase('starts_in'); ?>:</span>
                    <span class="timer-value">
                        ${days > 0 ? '<span class="timer-unit">' + days + 'j</span> ' : ''}
                        <span class="timer-unit">${hours.toString().padStart(2, '0')}h</span>
                        <span class="timer-unit">${minutes.toString().padStart(2, '0')}m</span>
                        <span class="timer-unit">${seconds.toString().padStart(2, '0')}s</span>
                    </span>
                </div>
            `;
            
            $timer.html(timerHtml);
        } else {
            // Le temps est écoulé, vérifier si l'examen a déjà été passé
            var alreadyPassed = $timer.data('already-passed') == '1';
            
            if (alreadyPassed) {
                // L'étudiant a déjà passé l'examen
                var statusHtml = `
                    <div class="exam-status">
                        <span class="exam-already-passed">
                            <i class="fas fa-check-circle"></i> <?php echo get_phrase('exam_already_submitted'); ?>
                        </span>
                    </div>
                `;
                $timer.replaceWith(statusHtml);
            } else {
                // L'examen est disponible
                var buttonHtml = `
                    <div class="exam-action">
                        <a href="<?php echo site_url('student/online_exam/'); ?>${examId}" class="btn-start-exam">
                            <i class="fas fa-play-circle"></i> <?php echo get_phrase('start_exam'); ?>
                        </a>
                    </div>
                `;
                $timer.replaceWith(buttonHtml);
            }
        }
    });
}

// Function to start timer updates
function startExamTimers() {
    // Mettre à jour immédiatement
    updateExamTimers();
    
    // Mettre à jour toutes les secondes
    if (window.examTimerInterval) {
        clearInterval(window.examTimerInterval);
    }
    window.examTimerInterval = setInterval(updateExamTimers, 1000);
}

// Function to handle exam action response
function handleExamActionResponse(response) {
    try {
        var $data = JSON.parse(response);
        if (data.status === true) {
            showAllExams();
        }
    } catch (e) {
        console.error('Error parsing response:', e);
    }
}

$(document).ready(function() {
    // Initialize date range picker with localized labels
    var daysShort = [
        '<?php echo mb_substr(get_phrase('sunday'), 0, 2, 'UTF-8'); ?>',
        '<?php echo mb_substr(get_phrase('monday'), 0, 2, 'UTF-8'); ?>',
        '<?php echo mb_substr(get_phrase('tuesday'), 0, 2, 'UTF-8'); ?>',
        '<?php echo mb_substr(get_phrase('wednesday'), 0, 2, 'UTF-8'); ?>',
        '<?php echo mb_substr(get_phrase('thursday'), 0, 2, 'UTF-8'); ?>',
        '<?php echo mb_substr(get_phrase('friday'), 0, 2, 'UTF-8'); ?>',
        '<?php echo mb_substr(get_phrase('saturday'), 0, 2, 'UTF-8'); ?>'
    ];
    
    $('#date_range').daterangepicker({
        opens: 'right',
        drops: 'down',
        showDropdowns: true,
        autoApply: false,
        linkedCalendars: false,
        locale: {
            format: 'DD-MM-YYYY',
            separator: ' - ',
            applyLabel: '<?php echo get_phrase('apply'); ?>',
            cancelLabel: '<?php echo get_phrase('cancel'); ?>',
            fromLabel: '<?php echo get_phrase('from'); ?>',
            toLabel: '<?php echo get_phrase('to'); ?>',
            customRangeLabel: '<?php echo get_phrase('custom'); ?>',
            weekLabel: 'S',
            daysOfWeek: daysShort,
            monthNames: [
                '<?php echo get_phrase('january'); ?>',
                '<?php echo get_phrase('february'); ?>',
                '<?php echo get_phrase('march'); ?>',
                '<?php echo get_phrase('april'); ?>',
                '<?php echo get_phrase('may'); ?>',
                '<?php echo get_phrase('june'); ?>',
                '<?php echo get_phrase('july'); ?>',
                '<?php echo get_phrase('august'); ?>',
                '<?php echo get_phrase('september'); ?>',
                '<?php echo get_phrase('october'); ?>',
                '<?php echo get_phrase('november'); ?>',
                '<?php echo get_phrase('december'); ?>'
            ],
            firstDay: 1
        },
        autoUpdateInput: false
    }).on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('DD-MM-YYYY') + ' - ' + picker.endDate.format('DD-MM-YYYY'));
    }).on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
    });

    // Handle filter form submission
    $('#filterForm').on('submit', function(e) {
        e.preventDefault();
        currentExamPage = 1; // Reset to first page when filtering
        loadExamsPage(1);
    });
});
</script>

<style>
.outline-filter {
    background: #fff;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.filter-row {
    display: flex;
    gap: 15px;
    align-items: flex-end;
    flex-wrap: wrap;
}

.filter-group {
    flex: 1;
    min-width: 200px;
}

.filter-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: 500;
    color: #333;
}

.filter-select, .filter-date {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
}

.filter-actions {
    display: flex;
    gap: 10px;
    min-width: auto;
}

.filter-btn {
    padding: 8px 16px;
    font-size: 14px;
}

/* Responsive filter styles */
@media (max-width: 992px) {
    .outline-filter {
        padding: 15px;
    }
    
    .filter-row {
        flex-direction: column;
        gap: 12px;
    }
    
    .filter-group {
        min-width: 100%;
        width: 100%;
        flex: none;
    }
    
    .filter-actions {
        flex-direction: row;
        width: 100%;
    }
    
    .filter-btn {
        flex: 1;
        text-align: center;
        justify-content: center;
        display: flex;
        align-items: center;
        gap: 6px;
    }
}

@media (max-width: 480px) {
    .outline-filter {
        padding: 12px;
    }
    
    .filter-btn {
        padding: 8px 12px;
        font-size: 13px;
    }
}

.exams-outline {
    background: #fff;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.exams-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.exam-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 15px;
    border: 1px solid #e9ecef;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.2s ease;
    background: #f8f9fa;
}

.exam-item:hover {
    background: #e9ecef;
    border-color: #dee2e6;
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.exam-info {
    display: flex;
    align-items: center;
    gap: 15px;
    flex: 1;
}

.exam-icon {
    font-size: 24px;
    color: #6c757d;
}

.exam-details {
    flex: 1;
    min-width: 0; /* Permet au contenu de se réduire si nécessaire */
}

.exam-name {
    font-weight: 600;
    font-size: 16px;
    color: #333;
    margin-bottom: 5px;
}

.exam-meta {
    display: flex;
    gap: 15px;
    font-size: 13px;
    color: #6c757d;
}

.exam-meta i {
    margin-right: 5px;
}

.exam-actions {
    display: flex;
    gap: 8px;
}

.btn-icon {
    width: 32px;
    height: 32px;
    border: none;
    border-radius: 8px;
    background: transparent;
    color: #6c757d;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
}

.btn-icon:hover {
    background: #e9ecef;
    color: #333;
}

.btn-edit-exam:hover {
    color: #4f46e5;
}

.btn-delete-exam:hover {
    color: #dc3545;
}

.empty-state {
    text-align: center;
    padding: 40px 20px;
}

.empty-state i {
    font-size: 48px;
    color: #6c757d;
    margin-bottom: 15px;
}

.empty-state p {
    color: #6c757d;
    margin-bottom: 20px;
    font-size: 16px;
}

.empty-state .btn-primary i,
.btn-primary i.fa-plus {
    color: #ffffff;
}

/* Date Range Picker Fixes */
.outline-filter {
    position: relative;
}

.daterangepicker {
    z-index: 9999 !important;
    font-family: inherit;
    border-radius: 8px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
    border: 1px solid #e2e8f0;
}

.daterangepicker:before,
.daterangepicker:after {
    display: none;
}

.daterangepicker .calendar-table {
    padding: 8px;
}

.daterangepicker .calendar-table th,
.daterangepicker .calendar-table td {
    min-width: 36px;
    width: 36px;
    height: 36px;
    text-align: center;
    font-size: 13px;
    line-height: 36px;
    padding: 0;
}

.daterangepicker .calendar-table th {
    font-weight: 600;
    color: #64748b;
    text-transform: uppercase;
    font-size: 11px;
}

.daterangepicker td.available:hover {
    background-color: #f1f5f9;
    border-radius: 4px;
}

.daterangepicker td.active,
.daterangepicker td.active:hover {
    background-color: #4f46e5 !important;
    border-color: #4f46e5 !important;
    color: #fff !important;
    border-radius: 4px;
}

.daterangepicker td.in-range {
    background-color: #e0e7ff !important;
    color: #3730a3 !important;
}

.daterangepicker td.start-date {
    border-radius: 4px 0 0 4px;
}

.daterangepicker td.end-date {
    border-radius: 0 4px 4px 0;
}

.daterangepicker .drp-buttons {
    border-top: 1px solid #e2e8f0;
    padding: 12px;
}

.daterangepicker .btn {
    padding: 8px 16px;
    font-size: 13px;
    border-radius: 6px;
}

.daterangepicker .btn-primary {
    background-color: #4f46e5 !important;
    border-color: #4f46e5 !important;
}

.daterangepicker .btn-primary:hover {
    background-color: #4338ca !important;
    border-color: #4338ca !important;
}

.daterangepicker .btn-default {
    background-color: #f1f5f9;
    border-color: #e2e8f0;
    color: #475569;
}

.daterangepicker .btn-default:hover {
    background-color: #e2e8f0;
}

.daterangepicker select.monthselect,
.daterangepicker select.yearselect {
    padding: 6px 10px;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    font-size: 13px;
    background-color: #fff;
    cursor: pointer;
}

.daterangepicker select.monthselect:hover,
.daterangepicker select.yearselect:hover {
    border-color: #4f46e5;
}

.daterangepicker .drp-selected {
    font-size: 13px;
    color: #64748b;
}

/* Exams Counter */
.exams-counter {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 16px;
    background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%);
    border-radius: 8px;
    margin-bottom: 16px;
    border: 1px solid #c7d2fe;
}

.counter-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    background: #4f46e5;
    border-radius: 8px;
    color: #ffffff;
    font-size: 16px;
}

.counter-text {
    font-size: 15px;
    color: #3730a3;
    font-weight: 500;
}

.counter-text #totalExamsCount {
    font-size: 20px;
    font-weight: 700;
    color: #4f46e5;
}

/* Pagination Styles */
.exam-pagination {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    padding: 16px 20px;
    background: #fff;
    border-radius: 8px;
    margin-top: 16px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.pagination-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    border: 1px solid #e2e8f0;
    background: #ffffff;
    color: #475569;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.15s ease;
}

.pagination-btn:hover:not(:disabled) {
    background: #f1f5f9;
    border-color: #4f46e5;
    color: #4f46e5;
}

.pagination-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
    background: #f8fafc;
}

.pagination-btn i {
    font-size: 14px;
}

.pagination-info {
    font-size: 14px;
    color: #64748b;
    font-weight: 500;
    min-width: 60px;
    text-align: center;
}

.pagination-info span {
    color: #1e293b;
    font-weight: 600;
}

/* Exam Timer Styles */
.exam-timer {
    margin-top: 10px;
    padding: 8px 12px;
    background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
    border-radius: 6px;
    border: 1px solid #bae6fd;
}

.timer-display {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
}

.timer-label {
    color: #0369a1;
    font-weight: 500;
}

.timer-value {
    display: flex;
    gap: 4px;
    font-weight: 600;
}

.timer-unit {
    background: #ffffff;
    padding: 2px 6px;
    border-radius: 4px;
    border: 1px solid #7dd3fc;
    color: #0c4a6e;
    min-width: 32px;
    text-align: center;
    box-shadow: 0 1px 2px rgba(0,0,0,0.05);
}

/* Responsive styles for timer */
@media (max-width: 768px) {
    .timer-display {
        flex-wrap: wrap;
        gap: 6px;
    }
    
    .timer-value {
        flex-wrap: wrap;
        gap: 3px;
    }
    
    .timer-unit {
        min-width: 28px;
        padding: 2px 4px;
        font-size: 12px;
        white-space: nowrap;
    }
}

@media (max-width: 480px) {
    .timer-display {
        flex-direction: column;
        align-items: flex-start;
        gap: 4px;
    }
    
    .timer-label {
        font-size: 12px;
    }
    
    .timer-value {
        width: 100%;
        justify-content: flex-start;
    }
    
    .timer-unit {
        min-width: 26px;
        padding: 1px 3px;
        font-size: 11px;
        white-space: nowrap;
    }
    
    /* For very small screens, show compact timer */
    .compact-timer .timer-unit {
        min-width: 22px;
        padding: 1px 2px;
        font-size: 10px;
    }
    
    /* When timer has days, make it more compact */
    .timer-display.has-days .timer-unit {
        min-width: 26px;
        padding: 1px 3px;
        font-size: 11px;
    }
}

/* Extra small devices */
@media (max-width: 360px) {
    .timer-display.has-days .timer-unit {
        min-width: 24px;
        padding: 1px 2px;
        font-size: 10px;
        white-space: nowrap;
    }
    
    /* Hide seconds on very small screens when there are days */
    .timer-display.has-days .timer-unit:last-child {
        display: none;
    }
}

/* For large screens with days, make timer more compact */
@media (min-width: 769px) {
    .timer-display.has-days .timer-unit {
        min-width: 30px;
        padding: 2px 5px;
    }
    
    /* On very large screens, keep original size */
    @media (min-width: 1200px) {
        .timer-display.has-days .timer-unit {
            min-width: 32px;
            padding: 2px 6px;
        }
    }
}

/* Optimize timer spacing for large screens */
@media (min-width: 1200px) {
    .timer-display {
        gap: 6px;
        max-width: 100%; /* Empêche le débordement */
        overflow: hidden; /* Cache le contenu qui dépasse */
    }
    
    .timer-value {
        gap: 3px;
        flex-wrap: wrap; /* Permet le retour à la ligne si nécessaire */
    }
    
    .timer-display.has-days {
        gap: 4px;
    }
    
    .timer-display.has-days .timer-value {
        gap: 2px;
    }
    
    /* Limit timer width on large screens */
    .exam-timer {
        max-width: 100%;
        overflow: hidden;
    }
}

/* For extra large screens, prevent timer from taking full width */
@media (min-width: 1400px) {
    .timer-display {
        display: inline-flex; /* S'adapte au contenu au lieu de prendre toute la largeur */
        max-width: none; /* Retire la limitation */
    }
    
    .timer-value {
        flex-wrap: nowrap; /* Garde tout sur une ligne */
    }
    
    /* Adjust exam details to prevent timer from stretching */
    .exam-details {
        min-width: 0; /* Permet au contenu de se réduire */
        flex: 1; /* Prend l'espace disponible */
    }
    
    .exam-timer {
        width: auto; /* S'adapte au contenu */
        display: inline-block; /* Ne prend pas toute la largeur */
    }
}

/* For large screens (≥ 992px), optimize timer display */
@media (min-width: 992px) {
    .timer-display {
        display: inline-flex; /* S'adapte au contenu */
    }
    
    .timer-value {
        flex-shrink: 0; /* Empêche la réduction des unités */
    }
    
    /* Prevent timer from expanding too much */
    .exam-timer {
        max-width: fit-content; /* S'adapte au contenu */
    }
}

/* Exam Action Button */
.exam-action {
    margin-top: 10px;
}

.btn-start-exam {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
    background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%);
    color: white;
    border-radius: 6px;
    text-decoration: none;
    font-weight: 500;
    font-size: 13px;
    transition: all 0.2s ease;
    border: 1px solid #3730a3;
    box-shadow: 0 2px 4px rgba(79, 70, 229, 0.2);
}

.btn-start-exam:hover {
    background: linear-gradient(135deg, #4338ca 0%, #3730a3 100%);
    transform: translateY(-1px);
    box-shadow: 0 4px 6px rgba(79, 70, 229, 0.3);
    color: white;
    text-decoration: none;
}

.btn-start-exam i {
    font-size: 14px;
}

/* Exam Already Passed Status */
.exam-status {
    margin-top: 10px;
}

.exam-already-passed {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
    border-radius: 6px;
    font-weight: 500;
    font-size: 13px;
    border: 1px solid #047857;
    box-shadow: 0 2px 4px rgba(16, 185, 129, 0.2);
}

.exam-already-passed i {
    font-size: 14px;
}

/* Responsive styles for exam cards */
@media (max-width: 768px) {
    .exam-item {
        flex-direction: column;
        align-items: flex-start;
        padding: 12px;
    }
    
    .exam-info {
        width: 100%;
        gap: 12px;
    }
    
    .exam-meta {
        flex-wrap: wrap;
        gap: 10px;
        font-size: 12px;
    }
    
    .exam-name {
        font-size: 15px;
    }
    
    .exam-timer {
        width: 100%;
        margin-top: 8px;
    }
    
    .exam-action, .exam-status {
        width: 100%;
    }
    
    .btn-start-exam, .exam-already-passed {
        width: 100%;
        justify-content: center;
        padding: 8px 12px;
        font-size: 12px;
    }
}

@media (max-width: 480px) {
    .exams-outline {
        padding: 15px;
    }
    
    .exam-item {
        padding: 10px;
    }
    
    .exam-icon {
        font-size: 20px;
    }
    
    .exam-name {
        font-size: 14px;
    }
    
    .exam-meta {
        flex-direction: column;
        gap: 5px;
    }
    
    .exam-date, .exam-class {
        display: flex;
        align-items: center;
        gap: 4px;
    }
    
    .btn-start-exam, .exam-already-passed {
        padding: 6px 10px;
        font-size: 11px;
    }
}
</style>

<script>
$(document).ready(function() {
    // Démarrer les compteurs après le chargement de la page
    startExamTimers();
});
</script>