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
?>

<div id="lesson_list_loader" style="display: none; text-align: center; padding: 20px;">
  <img src="<?php echo base_url('assets/backend/images/loader.gif'); ?>" alt="" height="50" width="50">
</div>

<div class="play-lesson-card" id="lesson_list_area" style="overflow: hidden; display: flex; flex-direction: column; max-height: calc(100vh - 100px);">
  <div class="preview-header">
     <h5 style="margin: 0; font-size: 0.9rem; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">
         <i class="fas fa-list-ul" style="margin-right: 8px;"></i>
         <?php echo get_phrase('course_content'); ?>
     </h5>
  </div>

  <div class="" style="flex: 1; overflow-y: auto; padding: 1.5rem;">
        <div class="tab-pane fade show active" id="section_and_lessons" role="tabpanel" aria-labelledby="section_and_lessons-tab">
          <!-- Lesson Content starts from here -->
          <div class="accordion custom-accordion" id="custom-accordion-one">
            <!-- Sections container -->
            <div id="sectionsContainer">
              <?php
              foreach ($sections as $key => $section):
                $section_number = $key + 1;
                $page_num = floor($key / $sections_per_page) + 1;
                $display_style = ($page_num == $initial_page) ? '' : 'display: none;';
                $lessons = $this->lms_model->get_lessons('section', $section['id'])->result_array();
                ?>
                <div class="m-0 section-item sidebar-section-page-<?php echo $page_num; ?>" data-page="<?php echo $page_num; ?>" style="<?php echo $display_style; ?>" data-section-id="<?php echo $section['id']; ?>">
                  <div class="card-header ps-0" id="<?php echo 'heading-'.$section['id']; ?>">
                    <h5 style="margin: 0;">
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
                          <tr class="course-sidebar-td" style="background-color: <?php if ($lesson_id == $lesson['id']) echo '#f1f5f9'; else echo '#fff';?>;">
                            <td class="course-sidebar-td px-2 py-1">
                              <a href="<?php echo site_url('addons/lessons/play/'.slugify($course_details['title']).'/'.$course_id.'/'.$lesson['id']); ?>" id="<?php echo $lesson['id']; ?>" class="lst" title="<?php echo htmlspecialchars($lesson['title']); ?>" style="<?php if ($lesson_id == $lesson['id']) echo 'color: #4f46e5 !important; font-weight: 700;'; ?>">
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
                                    <i class="fa fa-camera-retro"></i> <?php echo get_phrase('lesson'); ?>
                                  <?php elseif($fileExtension == 'pdf'): ?>
                                    <i class="fa fa-file-pdf"></i> <?php echo get_phrase('lesson'); ?>
                                  <?php elseif($fileExtension == 'doc' || $fileExtension == 'docx'): ?>
                                    <i class="fa fa-file-word"></i> <?php echo get_phrase('lesson'); ?>
                                  <?php elseif($fileExtension == 'txt'): ?>
                                    <i class="fa fa-file-alt"></i> <?php echo get_phrase('lesson'); ?>
                                  <?php else: ?>
                                    <i class="fa fa-file"></i> <?php echo get_phrase('lesson'); ?>
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
          <div class="sidebar-pagination" style="margin-top: 15px; display: flex; justify-content: center; align-items: center; gap: 10px;">
              <button type="button" class="pagination-btn" id="sidebarPrevPage" onclick="loadSidebarPage(currentSidebarPage - 1)" <?php echo $initial_page <= 1 ? 'disabled' : ''; ?> style="border: none; background: #f1f5f9; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s;">
                  <i class="fas fa-chevron-left" style="font-size: 0.8rem; color: #64748b;"></i>
              </button>
              <span class="pagination-info" style="font-size: 0.9rem; color: #64748b; font-weight: 500;">
                  <span id="currentSidebarPage"><?php echo $initial_page; ?></span> / <span id="totalSidebarPages"><?php echo $total_pages; ?></span>
              </span>
              <button type="button" class="pagination-btn" id="sidebarNextPage" onclick="loadSidebarPage(currentSidebarPage + 1)" <?php echo $initial_page >= $total_pages ? 'disabled' : ''; ?> style="border: none; background: #f1f5f9; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s;">
                  <i class="fas fa-chevron-right" style="font-size: 0.8rem; color: #64748b;"></i>
              </button>
          </div>
          <?php endif; ?>
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
  background: #4f46e5;
  border-radius: 12px;
  padding: 4px;
  box-shadow: 0 4px 12px rgba(99, 102, 241, 0.2);
}

.modern-tab-link {
  color: #fff !important;
  font-size: 0.9rem !important;
  font-weight: 600 !important;
  letter-spacing: 0.3px;
  padding: 10px 20px !important;
  border: none !important;
  border-radius: 8px !important;
  background: transparent !important;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
  text-transform: uppercase;
  position: relative;
  overflow: hidden;
  flex: 1;
  text-align: center;
  box-shadow: none !important;
}

.modern-tab-link:hover {
  background: rgba(255, 255, 255, 0.1) !important;
}

.card-header {
  background: #f8fafc !important;
  border-radius: 8px !important;
  padding: 0 !important;
  border-bottom: 1px solid #e2e8f0 !important;
  margin-bottom: 8px;
  overflow: hidden;
}

.custom-accordion-title {
  font-size: 0.95rem !important;
  font-weight: 600;
  color: #334155 !important;
  position: relative;
  padding: 12px 16px !important;
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  max-width: 100%;
}

.custom-accordion-title:hover {
  color: #4f46e5 !important;
  background: #f1f5f9;
}

.badge {
  background: #4f46e5 !important;
  color: #ffffff !important;
  margin-right: 12px;
  border-radius: 6px;
  padding: 0.25em 0.6em;
  font-size: 0.75rem;
  font-weight: 600;
}

.custom-accordion {
  border: none !important;
  box-shadow: none !important;
  background: transparent !important;
}

table.w-100.table-tab {
  background: transparent;
  border-collapse: separate;
  border-spacing: 0 4px;
}

.lesson_duration {
  color: #94a3b8 !important;
  font-size: 0.8rem;
  font-weight: 500;
  margin-top: 4px;
}

.lst {
  color: #475569 !important;
  font-weight: 500;
  font-size: 0.9rem;
  text-decoration: none !important;
  transition: color 0.2s ease;
  display: block;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  max-width: 100%;
}

.lst:hover {
  color: #4f46e5 !important;
}

/* Gestion des textes longs */
.course-sidebar-td {
  max-width: 0;
  width: 100%;
  border-radius: 6px;
  transition: background 0.2s;
}

.course-sidebar-td:hover {
    background-color: #f8fafc !important;
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

/* Custom scrollbar for sidebar */
#lesson_list_area .p-3::-webkit-scrollbar {
    width: 6px;
}

#lesson_list_area .p-3::-webkit-scrollbar-track {
    background: transparent;
}

#lesson_list_area .p-3::-webkit-scrollbar-thumb {
    background-color: #cbd5e1;
    border-radius: 3px;
}

input[type="checkbox"] + label:before {
  background-color: #f8fafc;
  border: 2px solid #cbd5e1;
  border-radius: 4px;
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
  border: solid #4f46e5;
  border-width: 0 2px 2px 0;
  transform: rotate(45deg);
}
</style>

<script>
var currentSidebarPage = <?php echo $initial_page; ?>;
var totalSidebarPages = <?php echo $total_pages; ?>;

function loadSidebarPage(page) {
    if (page < 1 || page > totalSidebarPages) return;
    
    // Hide all sections
    $('.section-item').hide();
    
    // Show sections for current page
    $('.sidebar-section-page-' + page).show();
    
    // Update buttons
    currentSidebarPage = page;
    $('#currentSidebarPage').text(currentSidebarPage);
    
    $('#sidebarPrevPage').prop('disabled', currentSidebarPage <= 1);
    $('#sidebarNextPage').prop('disabled', currentSidebarPage >= totalSidebarPages);
    
    // Scroll to top of list if needed
    // $('#lesson_list_area .p-3').scrollTop(0);
}
</script>
