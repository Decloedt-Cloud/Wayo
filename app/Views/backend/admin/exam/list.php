<?php
$exams_per_page = isset($exams_per_page) ? (int) $exams_per_page : 10;
$current_page = isset($current_page) ? (int) $current_page : 1;
$class_id = isset($filter_class_id) ? (string) $filter_class_id : '';
$date_range = isset($filter_date_range) ? (string) $filter_date_range : '';
$classes = isset($classes) && is_array($classes) ? $classes : [];
$exams = isset($exams) && is_array($exams) ? $exams : [];
$total_exams = isset($total_exams) ? (int) $total_exams : count($exams);
$total_pages = isset($total_pages) ? (int) $total_pages : ($exams_per_page > 0 ? (int) ceil($total_exams / $exams_per_page) : 1);
?>

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
                <div class="exam-item" data-exam-id="<?php echo $exam['id']; ?>">
                    <div class="exam-info">
                        <i class="fas fa-certificate exam-icon"></i>
                        <div class="exam-details">
                            <div class="exam-name"><?php echo html_escape(trim((string) ($exam['name'] ?? '')) !== '' ? $exam['name'] : get_phrase('unnamed_certification')); ?></div>
                            <div class="exam-meta">
                                <span class="exam-date">
                                    <i class="fas fa-calendar"></i> <?php echo date('D, d-M-Y H:i', strtotime($exam['starting_date'])); ?>
                                </span>
                                <span class="exam-class">
                                    <i class="fas fa-chalkboard"></i> <?php echo !empty($exam['class_name']) ? html_escape($exam['class_name']) : get_phrase('no_class'); ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="exam-actions">
                        <button type="button" class="btn-icon btn-edit-exam" data-exam-id="<?php echo $exam['id']; ?>" onclick="event.stopPropagation(); openExamEditor(<?php echo $exam['id']; ?>)">
                            <i class="fas fa-pen-to-square"></i>
                        </button>
                        <button type="button" class="btn-icon btn-delete-exam" onclick="event.stopPropagation(); deleteExam('<?php echo route('exam/delete/'.$exam['id']); ?>')">
                            <i class="fas fa-trash-can"></i>
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-certificate"></i>
            <p><?php echo get_phrase('no_certifications_found'); ?></p>
            <button type="button" class="btn btn-primary" onclick="openNewExamEditor()">
                <i class="fas fa-plus"></i> <?php echo get_phrase('create_first_certification'); ?>
            </button>
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
        url: '<?php echo site_url('admin/get_exams_paginated'); ?>',
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
    
    if (exams && Array.isArray(exams) && exams.length > 0) {
        examsHtml = '<div class="exams-list">';
        
        $.each(exams, function(index, exam) {
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
                        </div>
                    </div>
                    <div class="exam-actions">
                        <button type="button" class="btn-icon btn-edit-exam" data-exam-id="${exam.id}" onclick="event.stopPropagation(); openExamEditor(${exam.id})">
                            <i class="fas fa-pen-to-square"></i>
                        </button>
                        <button type="button" class="btn-icon btn-delete-exam" onclick="event.stopPropagation(); deleteExam('<?php echo route('exam/delete/'); ?>/${exam.id}')">
                            <i class="fas fa-trash-can"></i>
                        </button>
                    </div>
                </div>
            `;
        });
        
        examsHtml += '</div>';
    } else {
        examsHtml = `
            <div class="empty-state">
                <i class="fas fa-certificate"></i>
                <p><?php echo get_phrase('no_certifications_found'); ?></p>
                <button type="button" class="btn btn-primary" onclick="openNewExamEditor()">
                    <i class="fas fa-plus"></i> <?php echo get_phrase('create_first_certification'); ?>
                </button>
            </div>
        `;
    }
    
    $('.exams-outline').html(examsHtml);
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
</style>