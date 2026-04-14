<?php
helper('lms');
$lms_model = $lms_model ?? ($this->lms_model ?? model('App\\Models\\addons\\Lms_model'));
$as_result_array = static function ($result): array {
    if (is_array($result)) {
        return $result;
    }
    if (is_object($result)) {
        if (method_exists($result, 'result_array')) {
            return $result;
        }
        if (method_exists($result, 'getResultArray')) {
            return $result->getResultArray();
        }
        if (method_exists($result, 'getResult')) {
            $rows = $result->getResult();
            return is_array($rows) ? $rows : [];
        }
    }
    return [];
};

if (!isset($sections) || !is_array($sections)) {
    $sections = [];
}
$sections = array_map(static function ($sec) {
    return is_object($sec) ? (array) $sec : (is_array($sec) ? $sec : []);
}, $sections);

// Pagination settings
$sections_per_page = 10;
$total_sections = count($sections);
$total_pages = ceil($total_sections / $sections_per_page);

// Find current page based on current section
$current_section_index = 0;
foreach ($sections as $idx => $sec) {
    if ($sec['id'] == $section_id) {
        $current_section_index = $idx;
        break;
    }
}
$initial_page = floor($current_section_index / $sections_per_page) + 1;

// Calculate total lessons, quizzes and completed
$total_lessons = 0;
$total_quizzes = 0;
$total_items = 0;
$completed_lessons = 0;
foreach ($sections as $sec) {
    $sec_lessons = (is_object($lms_model) && method_exists($lms_model, 'get_lessons'))
        ? $as_result_array($lms_model->get_lessons('section', $sec['id']))
        : [];
    $sec_lessons = array_map(static function ($les) {
        return is_object($les) ? (array) $les : (is_array($les) ? $les : []);
    }, $sec_lessons);
    $total_items += count($sec_lessons);
    foreach ($sec_lessons as $les) {
        if (strtolower($les['lesson_type']) == 'quiz') {
            $total_quizzes++;
        } else {
            $total_lessons++;
        }
        if (lesson_progress($les['id'])) {
            $completed_lessons++;
        }
    }
}
$progress_percent = $total_items > 0 ? round(($completed_lessons / $total_items) * 100) : 0;
?>

<style>
/* ========== MODERN SIDEBAR STYLES ========== */
.sidebar-card {
    height: 100%;
    display: flex;
    flex-direction: column;
    background: var(--bg-card);
}

/* Sidebar Header */
.sidebar-header {
    padding: 1.25rem 1.5rem;
    background: linear-gradient(180deg, rgba(99, 102, 241, 0.05), transparent);
    border-bottom: 1px solid var(--border-color);
}

.sidebar-header-content {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1rem;
}

.sidebar-title {
    display: flex;
    align-items: center;
    gap: 0.625rem;
    font-family: var(--font-header);
    font-size: 0.9rem;
    font-weight: 700;
    color: var(--text-dark);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin: 0;
}

.sidebar-title i {
    color: var(--primary);
    font-size: 1.125rem;
}

.sidebar-stats {
    font-size: 0.8125rem;
    color: var(--text-muted);
    font-weight: 500;
}

/* Progress Bar */
.progress-wrapper {
    background: var(--bg-main);
    border-radius: 50px;
    padding: 0.25rem;
    border: 1px solid var(--border-color);
}

.progress-bar-track {
    height: 8px;
    background: var(--border-color);
    border-radius: 50px;
    overflow: hidden;
}

.progress-bar-fill {
    height: 100%;
    background: linear-gradient(90deg, var(--primary), var(--primary-light));
    border-radius: 50px;
    transition: width 0.6s ease;
    position: relative;
}

.progress-bar-fill::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
    animation: shimmer 2s infinite;
}

@keyframes shimmer {
    0% { transform: translateX(-100%); }
    100% { transform: translateX(100%); }
}

.progress-info {
    display: flex;
    justify-content: space-between;
    margin-top: 0.5rem;
    font-size: 0.75rem;
    font-weight: 600;
}

.progress-text {
    color: var(--text-muted);
}

.progress-percent {
    color: var(--primary);
}

/* Sidebar Content */
.sidebar-content {
    flex: 1;
    overflow-y: auto;
    padding: 1rem 1.5rem;
}

/* Section Styles */
.section-block {
    margin-bottom: 1.25rem;
    animation: fadeUp 0.3s ease;
    animation-fill-mode: backwards;
}

.section-block:nth-child(1) { animation-delay: 0.05s; }
.section-block:nth-child(2) { animation-delay: 0.1s; }
.section-block:nth-child(3) { animation-delay: 0.15s; }
.section-block:nth-child(4) { animation-delay: 0.2s; }
.section-block:nth-child(5) { animation-delay: 0.25s; }

.section-header-bar {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem 0;
    border-bottom: 2px solid var(--border-color);
    margin-bottom: 0.75rem;
}

.section-number {
    min-width: 28px;
    height: 28px;
    border-radius: var(--radius-sm);
    background: linear-gradient(135deg, var(--primary), var(--primary-light));
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.8125rem;
    font-weight: 700;
    box-shadow: 0 2px 8px rgba(99, 102, 241, 0.3);
}

.section-title {
    flex: 1;
    font-family: var(--font-header);
    font-size: 0.9375rem;
    font-weight: 700;
    color: var(--text-dark);
    margin: 0;
    line-height: 1.3;
}

/* Lesson Items */
.lessons-list {
    display: flex;
    flex-direction: column;
    gap: 0.375rem;
}

.lesson-item {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    padding: 0.75rem;
    border-radius: var(--radius-md);
    transition: all 0.2s ease;
    border: 1px solid transparent;
    cursor: pointer;
}

.lesson-item:hover {
    background: var(--bg-main);
    border-color: var(--border-color);
}

.lesson-item.active {
    background: var(--primary-lighter);
    border-color: var(--primary);
}

.lesson-item.active .lesson-link {
    color: var(--primary-dark) !important;
    font-weight: 700;
}

/* Checkbox */
.lesson-checkbox {
    position: relative;
    margin-top: 2px;
}

.lesson-checkbox input[type="checkbox"] {
    width: 20px;
    height: 20px;
    border: 2px solid var(--border-color);
    border-radius: 6px;
    appearance: none;
    -webkit-appearance: none;
    cursor: pointer;
    transition: all 0.2s ease;
    background: var(--bg-card);
}

.lesson-checkbox input[type="checkbox"]:hover {
    border-color: var(--primary);
}

.lesson-checkbox input[type="checkbox"]:checked {
    background: linear-gradient(135deg, var(--secondary), #34d399);
    border-color: var(--secondary);
}

.lesson-checkbox input[type="checkbox"]:checked::after {
    content: '✓';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: white;
    font-size: 0.75rem;
    font-weight: 700;
}

/* Lesson Content */
.lesson-content {
    flex: 1;
    min-width: 0;
}

.lesson-link {
    display: block;
    color: var(--text-dark) !important;
    font-size: 0.875rem;
    font-weight: 500;
    text-decoration: none !important;
    line-height: 1.4;
    transition: color 0.2s ease;
    word-break: break-word;
}

.lesson-link:hover {
    color: var(--primary) !important;
}

.lesson-meta {
    display: flex;
    align-items: center;
    gap: 0.375rem;
    margin-top: 0.375rem;
    font-size: 0.75rem;
    color: var(--text-muted);
    font-weight: 500;
}

.lesson-meta i {
    font-size: 0.8125rem;
    color: var(--primary);
    opacity: 0.7;
}

.lesson-meta.quiz i {
    color: #f59e0b;
}

.lesson-meta.attachment i {
    color: var(--secondary);
}

/* Loading State */
.sidebar-loading {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
    padding: 2rem;
    color: var(--text-muted);
    font-size: 0.9375rem;
}

.sidebar-loading i {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

/* Pagination */
.sidebar-pagination {
    padding: 1rem 1.5rem;
    border-top: 1px solid var(--border-color);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
    background: var(--bg-main);
}

.page-btn {
    width: 36px;
    height: 36px;
    border: 2px solid var(--border-color);
    background: var(--bg-card);
    border-radius: var(--radius-sm);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--text-muted);
    font-size: 1rem;
    cursor: pointer;
    transition: all 0.2s ease;
}

.page-btn:hover:not(:disabled) {
    border-color: var(--primary);
    color: var(--primary);
    transform: translateY(-1px);
}

.page-btn:disabled {
    opacity: 0.4;
    cursor: not-allowed;
}

.page-info {
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--text-muted);
    padding: 0 0.5rem;
}

.page-info span {
    color: var(--text-dark);
}

/* Responsive */
@media (max-width: 992px) {
    .sidebar-card {
        border-radius: var(--radius-xl);
        margin: 0 1rem 1rem;
        box-shadow: var(--shadow-md);
        border: 1px solid var(--border-color);
        height: auto;
        max-height: none;
    }
    
    .sidebar-content {
        max-height: none;
        overflow: visible;
    }
}

@media (max-width: 576px) {
    .sidebar-header {
        padding: 1rem;
    }
    
    .sidebar-content {
        padding: 1rem;
    }
    
    .sidebar-pagination {
        padding: 0.75rem 1rem;
    }
    
    .lesson-item {
        padding: 0.625rem;
    }
    
    .page-btn {
        width: 32px;
        height: 32px;
    }
}
</style>

<div class="sidebar-card">
    <!-- Header -->
    <div class="sidebar-header">
        <div class="sidebar-header-content">
            <h3 class="sidebar-title">
                <i class="fas fa-list-ul"></i>
                <?php echo get_phrase('course_content'); ?>
            </h3>
            <span class="sidebar-stats">
                <?php echo $total_lessons; ?> <?php echo get_phrase('lessons'); ?>
                <?php if ($total_quizzes > 0): ?>
                    &bull; <?php echo $total_quizzes; ?> <?php echo get_phrase('quiz'); ?>
                <?php endif; ?>
            </span>
        </div>
        
        <!-- Progress Bar -->
        <div class="progress-wrapper">
            <div class="progress-bar-track">
                <div class="progress-bar-fill" style="width: <?php echo $progress_percent; ?>%;"></div>
            </div>
        </div>
        <div class="progress-info">
            <span class="progress-text"><?php echo $completed_lessons; ?>/<?php echo $total_items; ?> <?php echo get_phrase('completed'); ?></span>
            <span class="progress-percent"><?php echo $progress_percent; ?>%</span>
        </div>
    </div>
    
    <!-- Content -->
    <div class="sidebar-content" id="sidebar-content">
        <!-- Loading State -->
        <div id="lesson_list_loader" class="sidebar-loading" style="display: none;">
            <i class="fas fa-spinner fa-spin"></i>
            <span><?php echo get_phrase('loading'); ?>...</span>
        </div>
        
        <!-- Sections List -->
        <div id="sections-container">
            <?php foreach ($sections as $key => $section):
                $section_number = $key + 1;
                $page_num = floor($key / $sections_per_page) + 1;
                $display_style = ($page_num == $initial_page) ? '' : 'display: none;';
                $lessons = (is_object($lms_model) && method_exists($lms_model, 'get_lessons'))
                    ? $as_result_array($lms_model->get_lessons('section', $section['id']))
                    : [];
                $lessons = array_map(static function ($lesson) {
                    return is_object($lesson) ? (array) $lesson : (is_array($lesson) ? $lesson : []);
                }, $lessons);
            ?>
            <div class="section-block sidebar-section-page-<?php echo $page_num; ?>" 
                 data-page="<?php echo $page_num; ?>" 
                 data-section-id="<?php echo $section['id']; ?>"
                 style="<?php echo $display_style; ?>">
                
                <!-- Section Header -->
                <div class="section-header-bar">
                    <span class="section-number"><?php echo $section_number; ?></span>
                    <h4 class="section-title"><?php echo htmlspecialchars($section['title']); ?></h4>
                </div>
                
                <!-- Lessons List -->
                <div class="lessons-list">
                    <?php foreach ($lessons as $lesson_key => $lesson):
                        $is_completed = lesson_progress($lesson['id']);
                        $is_active = (isset($lesson_id) && $lesson_id == $lesson['id']);
                    ?>
                    <div class="lesson-item <?php echo $is_active ? 'active' : ''; ?>">
                        <!-- Checkbox -->
                        <div class="lesson-checkbox">
                            <input type="checkbox" 
                                   id="lesson-<?php echo $lesson['id']; ?>"
                                   onchange="markThisLessonAsCompleted('<?php echo $lesson['id']; ?>');"
                                   <?php echo $is_completed ? 'checked' : ''; ?>>
                        </div>
                        
                        <!-- Content -->
                        <div class="lesson-content">
                            <a href="<?php echo site_url('addons/lessons/play/'.slugify($course_details['title']).'/'.$course_id.'/'.$lesson['id']); ?>" 
                               class="lesson-link"
                               title="<?php echo htmlspecialchars($lesson['title']); ?>">
                                <?php echo ($lesson_key + 1) . '. ' . htmlspecialchars($lesson['title']); ?>
                                <?php if ($lesson['lesson_type'] == 'other'): ?>
                                    <i class="fas fa-paperclip"></i>
                                <?php endif; ?>
                            </a>
                            
                            <div class="lesson-meta <?php echo $lesson['lesson_type'] == 'quiz' ? 'quiz' : ''; ?>">
                                <?php if ($lesson['lesson_type'] == 'video' || $lesson['lesson_type'] == '' || $lesson['lesson_type'] == NULL): ?>
                                    <i class="fas fa-play-circle"></i>
                                    <span><?php echo readable_time_for_humans($lesson['duration']); ?></span>
                                <?php elseif ($lesson['lesson_type'] == 'quiz'): ?>
                                    <i class="fas fa-question-circle"></i>
                                    <span><?php echo get_phrase('quiz'); ?></span>
                                <?php else: ?>
                                    <i class="fas fa-book-open"></i>
                                    <span><?php echo get_phrase('lesson'); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <?php if ($total_sections > $sections_per_page): ?>
    <!-- Pagination -->
    <div class="sidebar-pagination">
        <button type="button" class="page-btn" id="sidebar-prev" onclick="loadSidebarPage(currentSidebarPage - 1)" <?php echo $initial_page <= 1 ? 'disabled' : ''; ?>>
            <i class="fas fa-chevron-left"></i>
        </button>
        <span class="page-info">
            <span id="current-page"><?php echo $initial_page; ?></span> / <span id="total-pages"><?php echo $total_pages; ?></span>
        </span>
        <button type="button" class="page-btn" id="sidebar-next" onclick="loadSidebarPage(currentSidebarPage + 1)" <?php echo $initial_page >= $total_pages ? 'disabled' : ''; ?>>
            <i class="fas fa-chevron-right"></i>
        </button>
    </div>
    <?php endif; ?>
</div>

<script>
var currentSidebarPage = <?php echo $initial_page; ?>;
var totalSidebarPages = <?php echo $total_pages; ?>;

function loadSidebarPage(page) {
    if (page < 1 || page > totalSidebarPages) return;
    
    // Hide all sections with fade
    $('.section-block').fadeOut(150);
    
    // Show sections for current page with fade
    setTimeout(function() {
        $('.sidebar-section-page-' + page).fadeIn(200);
    }, 150);
    
    // Update state
    currentSidebarPage = page;
    $('#current-page').text(currentSidebarPage);
    
    // Update button states
    $('#sidebar-prev').prop('disabled', currentSidebarPage <= 1);
    $('#sidebar-next').prop('disabled', currentSidebarPage >= totalSidebarPages);
    
    // Scroll to top of sidebar content
    $('#sidebar-content').animate({ scrollTop: 0 }, 200);
}
</script>
