<?php
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

// Get only sections for initial page
$initial_offset = ($initial_page - 1) * $sections_per_page;
$initial_sections = array_slice($sections, $initial_offset, $sections_per_page);
?>
<div class="col-lg-3 mt-5 order-md-2 course_col hidden text-center" id="lesson_list_loader">
  <img src="<?php echo base_url('assets/backend/images/loader.gif'); ?>" alt="" height="50" width="50">
</div>
<div class="col-lg-3 order-md-2 course_col" id="lesson_list_area">
  <div class="text-center margin-ms">
    <h5 class="text-uppercase"><?php echo get_phrase('course_content'); ?></h5>
  </div>
  <div class="row m-10-1">
    <div class="col-12">
      <ul class="nav nav-tabs modern-tabs" id="lessonTab" role="tablist">
          <a class="nav-link modern-tab-link" id="section_and_lessons-tab" data-bs-toggle="tab" href="#section_and_lessons" role="tab" aria-controls="section_and_lessons" aria-selected="true"><?php echo get_phrase('Lessons') ?></a>
      </ul>
      <div class="tab-content" id="lessonTabContent">
        <div class="tab-pane fade show active" id="section_and_lessons" role="tabpanel" aria-labelledby="section_and_lessons-tab">
          <!-- Lesson Content starts from here -->
          <div class="accordion custom-accordion" id="custom-accordion-one">
            <!-- Sections container - will be populated by AJAX -->
            <div id="sectionsContainer">
              <?php
              $section_number = $initial_offset;
              foreach ($initial_sections as $key => $section):
                $section_number++;
                $lessons = $this->lms_model->get_lessons('section', $section['id'])->result_array();
                ?>
                <div class="m-0 section-item" data-section-id="<?php echo $section['id']; ?>">
                  <div class="card-header ps-0" id="<?php echo 'heading-'.$section['id']; ?>">
                    <h5>
                      <a class="custom-accordion-title d-block py-1 button-stk" type="button" data-bs-toggle="collapse" data-bs-target="<?php echo '#collapse-'.$section['id']; ?>" <?php if($opened_section_id == $section['id']): ?> aria-expanded="true" <?php else: ?> aria-expanded="false" <?php endif; ?> aria-controls="<?php echo 'collapse-'.$section['id']; ?>" onclick="toggleAccordionIcon(this, '<?php echo $section['id']; ?>')" title="<?php echo htmlspecialchars($section['title']); ?>">
                        <span class="badge"><?php echo $section_number; ?></span>
                        <?php echo htmlspecialchars($section['title']); ?>
                      </a>
                    </h5>
                  </div>
                  <div id="<?php echo 'collapse-'.$section['id']; ?>" class="collapse <?php if($section_id == $section['id']) echo 'show'; ?>" aria-labelledby="<?php echo 'heading-'.$section['id']; ?>" data-bs-parent="#custom-accordion-one">
                    <div class="p-0">
                      <table class="w-100 table-tab">
                        <?php foreach ($lessons as $lesson_key => $lesson): ?>
                          <tr class="course-sidebar-td" style="background-color: <?php if ($lesson_id == $lesson['id']) echo '#EAEAEA'; else echo '#fff';?>;">
                            <td class="course-sidebar-td px-2 py-1">
                              <a href="<?php echo site_url('addons/lessons/play/'.slugify($course_details['title']).'/'.$course_id.'/'.$lesson['id']); ?>" id="<?php echo $lesson['id']; ?>" class="lst" title="<?php echo htmlspecialchars($lesson['title']); ?>">
                                <?php echo $lesson_key + 1; ?>:
                                <?php if ($lesson['lesson_type'] != 'other'): ?>
                                  <?php echo htmlspecialchars($lesson['title']); ?>
                                <?php else: ?>
                                  <?php echo htmlspecialchars($lesson['title']); ?>
                                  <i class="fa fa-paperclip"></i>
                                <?php endif; ?>
                              </a>
                              <div class="lesson_duration">
                                <?php if ($lesson['lesson_type'] == 'video' || $lesson['lesson_type'] == '' || $lesson['lesson_type'] == NULL): ?>
                                  <i class="fa fa-play-circle"></i>
                                  <?php echo readable_time_for_humans($lesson['duration']); ?>
                                <?php elseif($lesson['lesson_type'] == 'quiz'): ?>
                                  <i class="fa fa-question-circle"></i> <?php echo get_phrase('quiz'); ?>
                                <?php else:
                                  $tmp = explode('.', $lesson['attachment']);
                                  $fileExtension = strtolower(end($tmp)); ?>
                                  <?php if ($fileExtension == 'jpg' || $fileExtension == 'jpeg' || $fileExtension == 'png' || $fileExtension == 'bmp' || $fileExtension == 'svg'): ?>
                                    <i class="fa fa-camera-retro"></i> <?php echo get_phrase('attachment'); ?>
                                  <?php elseif($fileExtension == 'pdf'): ?>
                                    <i class="fa fa-file-pdf"></i> <?php echo get_phrase('attachment'); ?>
                                  <?php elseif($fileExtension == 'doc' || $fileExtension == 'docx'): ?>
                                    <i class="fa fa-file-word"></i> <?php echo get_phrase('attachment'); ?>
                                  <?php elseif($fileExtension == 'txt'): ?>
                                    <i class="fa fa-file-alt"></i> <?php echo get_phrase('attachment'); ?>
                                  <?php else: ?>
                                    <i class="fa fa-file"></i> <?php echo get_phrase('attachment'); ?>
                                  <?php endif; ?>
                                <?php endif; ?>
                              </div>
                            </td>
                          </tr>
                        <?php endforeach; ?>
                      </table>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
            
            <!-- Loading spinner -->
            <div id="sectionsLoading" class="sections-loading" style="display: none;">
                <i class="fas fa-spinner fa-spin"></i>
                <span><?php echo get_phrase('loading'); ?>...</span>
            </div>
          </div>
          <!-- Lesson Content ends from here -->
          
          <?php if ($total_sections > $sections_per_page): ?>
          <!-- Pagination -->
          <div class="sidebar-pagination">
              <button type="button" class="pagination-btn" id="sidebarPrevPage" onclick="loadSidebarPage(currentSidebarPage - 1)" <?php echo $initial_page <= 1 ? 'disabled' : ''; ?>>
                  <i class="fas fa-chevron-left"></i>
              </button>
              <span class="pagination-info">
                  <span id="currentSidebarPage"><?php echo $initial_page; ?></span> / <span id="totalSidebarPages"><?php echo $total_pages; ?></span>
              </span>
              <button type="button" class="pagination-btn" id="sidebarNextPage" onclick="loadSidebarPage(currentSidebarPage + 1)" <?php echo $initial_page >= $total_pages ? 'disabled' : ''; ?>>
                  <i class="fas fa-chevron-right"></i>
              </button>
          </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
/* Modern Tab Design */
.modern-tabs {
  border-bottom: none !important;
  display: flex;
  gap: 0;
  padding: 0;
  margin: 0 0 20px 0;
  background: #6366f1;
  border-radius: 16px;
  padding: 4px;
  box-shadow: 0 8px 32px rgba(102, 126, 234, 0.15);
}

.modern-tab-link {
  color: #fff !important;
  font-size: 0.95rem !important;
  font-weight: 600 !important;
  letter-spacing: 0.3px;
  padding: 12px 28px !important;
  border: none !important;
  border-radius: 12px !important;
  background: transparent !important;
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1) !important;
  text-transform: uppercase;
  position: relative;
  overflow: hidden;
  flex: 1;
  text-align: center;
  box-shadow: none !important;
}


.modern-tab-link:hover::before {
  left: 100%;
}


/* Subtle glow effect for active tab */
.modern-tab-link.active::after {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  border-radius: 12px;
  box-shadow: 0 0 20px rgba(76, 81, 191, 0.3);
  pointer-events: none;
}

.tab-content {
  background: #ffffff;
  border-radius: 16px;
  padding: 20px;
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
  transition: all 0.3s ease-in-out;
  border: 1px solid rgba(0, 0, 0, 0.05);
}

/* Smooth fade-in effect for active tab pane */
.tab-pane.fade {
  opacity: 0;
  transition: opacity 0.4s ease-in-out;
}

.tab-pane.fade.show {
  opacity: 1;
}

.card-header {
  background: #f4f5f7 !important;
  border-radius: 12px !important;
  padding: 10px 15px !important;
  border-bottom: none !important;
  margin-bottom: 12px;
}

.custom-accordion-title {
  font-size: 1rem !important;
  font-weight: 600;
  color: #000 !important;
  position: relative;
  padding: 12px 20px !important;
  transition: color 0.3s ease;
  display: block;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  max-width: 100%;
}

.custom-accordion-title:hover {
  color: #6366f1 !important;
}

.badge {
  background: #6366f1 !important;
  color: #ffffff !important;
  margin-right: 10px;
  border-radius: 8px;
  padding: 0.3em 0.6em;
}

.custom-accordion {
  border: none !important;
  box-shadow: none !important;
  background: transparent !important;
}

table.w-100.table-tab {
  background: transparent;
  border-collapse: separate;
  border-spacing: 0 8px;
}

.lesson_duration {
  color: #6b7280 !important;
  font-size: 0.85rem;
  font-weight: 500;
}

.lst {
  color: #000 !important;
  font-weight: 500;
  text-decoration: none !important;
  transition: color 0.3s ease;
  display: block;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  max-width: 100%;
}

.lst:hover {
  color: #6b7280 !important;
}

/* Gestion des textes longs */
.course-sidebar-td {
  max-width: 0;
  width: 100%;
}

.course-sidebar-td td {
  max-width: 250px;
  overflow: hidden;
}

.lesson_duration {
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

input[type="checkbox"] + label:before {
  background-color: #f8fafc;
  border: 2px solid #000;
  border-radius: 6px;
  width: 18px;
  height: 18px;
}

.form-group input:checked + label:after {
  content: "";
  display: block;
  position: absolute;
  top: 6px;
  left: 6px;
  width: 5px;
  height: 11px;
  border: solid #6366f1;
  border-width: 0 2px 2px 0;
  transform: rotate(45deg);
}

@media (max-width: 768px) {
  .tab-content {
    padding: 15px;
    border-radius: 12px;
  }

  .custom-accordion-title {
    font-size: 0.95rem !important;
    padding: 10px 15px !important;
  }

  .badge {
    font-size: 0.65em;
  }
}

/* Sidebar Pagination Styles */
.sidebar-pagination {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    padding: 12px 16px;
    background: #f8fafc;
    border-top: 1px solid #e8ecf1;
    border-radius: 0 0 12px 12px;
    margin-top: 12px;
}

.sidebar-pagination .pagination-btn {
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

.sidebar-pagination .pagination-btn:hover:not(:disabled) {
    background: #f1f5f9;
    border-color: #cbd5e1;
    color: #1e293b;
}

.sidebar-pagination .pagination-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.sidebar-pagination .pagination-btn i {
    font-size: 1rem;
}

.sidebar-pagination .pagination-info {
    font-size: 0.9rem;
    color: #64748b;
    font-weight: 500;
    min-width: 60px;
    text-align: center;
}

.sidebar-pagination .pagination-info span {
    color: #1e293b;
    font-weight: 600;
}

/* Loading Spinner */
.sections-loading {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 40px 20px;
    color: #64748b;
    gap: 12px;
}

.sections-loading i {
    font-size: 2rem;
    color: #6366f1;
}

.sections-loading span {
    font-size: 0.9rem;
}

@media (max-width: 768px) {
    .sidebar-pagination {
        padding: 10px 12px;
        gap: 8px;
    }
    
    .sidebar-pagination .pagination-btn {
        width: 32px;
        height: 32px;
    }
    
    .sidebar-pagination .pagination-info {
        font-size: 0.85rem;
    }
}
</style>

<script>
// Pagination variables
let currentSidebarPage = <?php echo $initial_page; ?>;
let totalSidebarPages = <?php echo $total_pages; ?>;
const sectionsPerPage = <?php echo $sections_per_page; ?>;
const courseId = <?php echo $course_id; ?>;
const currentLessonId = <?php echo $lesson_id; ?>;
const courseSlug = '<?php echo slugify($course_details['title']); ?>';
const ajaxUrl = '<?php echo site_url('addons/lessons/ajax_get_sections_paginated/'.$course_id); ?>';

function loadSidebarPage(page, forceReload = false) {
    if (page < 1) return;
    if (!forceReload && (page > totalSidebarPages || page === currentSidebarPage)) return;
    
    const container = document.getElementById('sectionsContainer');
    const loading = document.getElementById('sectionsLoading');
    
    // Show loading
    container.style.opacity = '0.5';
    loading.style.display = 'flex';
    
    $.ajax({
        url: ajaxUrl,
        type: 'GET',
        data: { page: page },
        dataType: 'json',
        success: function(response) {
            if (response.status) {
                // Generate HTML for sections
                let html = '';
                let sectionNumber = response.start_number;
                
                response.sections.forEach(function(section) {
                    html += renderSection(section, sectionNumber);
                    sectionNumber++;
                });
                
                container.innerHTML = html;
                currentSidebarPage = response.current_page;
                totalSidebarPages = response.total_pages;
                
                // Update pagination UI
                updateSidebarPaginationUI();
            }
        },
        error: function() {
            console.error('Error loading sections');
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
        const isCurrentLesson = (lesson.id == currentLessonId);
        const bgColor = isCurrentLesson ? '#EAEAEA' : '#fff';
        const lessonUrl = '<?php echo site_url('addons/lessons/play/'); ?>' + courseSlug + '/' + courseId + '/' + lesson.id;
        
        let durationHtml = '';
        if (lesson.lesson_type === 'video' || lesson.lesson_type === '' || lesson.lesson_type === null) {
            durationHtml = '<i class="fa fa-play-circle"></i> ' + formatDuration(lesson.duration);
        } else if (lesson.lesson_type === 'quiz') {
            durationHtml = '<i class="fa fa-question-circle"></i> <?php echo get_phrase('quiz'); ?>';
        } else {
            durationHtml = '<i class="fa fa-file"></i> <?php echo get_phrase('attachment'); ?>';
        }
        
        let attachmentIcon = '';
        if (lesson.lesson_type === 'other') {
            attachmentIcon = ' <i class="fa fa-paperclip"></i>';
        }
        
        lessonsHtml += `
            <tr class="course-sidebar-td" style="background-color: ${bgColor};">
                <td class="course-sidebar-td px-2 py-1">
                    <a href="${lessonUrl}" id="${lesson.id}" class="lst" title="${escapeHtml(lesson.title)}">
                        ${lessonNumber}: ${escapeHtml(lesson.title)}${attachmentIcon}
                    </a>
                    <div class="lesson_duration">
                        ${durationHtml}
                    </div>
                </td>
            </tr>
        `;
    });
    
    return `
        <div class="m-0 section-item" data-section-id="${section.id}">
            <div class="card-header ps-0" id="heading-${section.id}">
                <h5>
                    <a class="custom-accordion-title d-block py-1 button-stk collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-${section.id}" aria-expanded="false" aria-controls="collapse-${section.id}" onclick="toggleAccordionIcon(this, '${section.id}')" title="${escapeHtml(section.title)}">
                        <span class="badge">${sectionNumber}</span>
                        ${escapeHtml(section.title)}
                    </a>
                </h5>
            </div>
            <div id="collapse-${section.id}" class="collapse" aria-labelledby="heading-${section.id}" data-bs-parent="#custom-accordion-one">
                <div class="p-0">
                    <table class="w-100 table-tab">
                        ${lessonsHtml}
                    </table>
                </div>
            </div>
        </div>
    `;
}

function updateSidebarPaginationUI() {
    const currentPageEl = document.getElementById('currentSidebarPage');
    const totalPagesEl = document.getElementById('totalSidebarPages');
    const prevBtn = document.getElementById('sidebarPrevPage');
    const nextBtn = document.getElementById('sidebarNextPage');
    
    if (currentPageEl) currentPageEl.textContent = currentSidebarPage;
    if (totalPagesEl) totalPagesEl.textContent = totalSidebarPages;
    if (prevBtn) prevBtn.disabled = (currentSidebarPage <= 1);
    if (nextBtn) nextBtn.disabled = (currentSidebarPage >= totalSidebarPages);
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function formatDuration(seconds) {
    if (!seconds || seconds == 0) return '0:00';
    const minutes = Math.floor(seconds / 60);
    const secs = seconds % 60;
    return minutes + ':' + (secs < 10 ? '0' : '') + secs;
}
</script>
