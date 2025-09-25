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
            <?php
            foreach ($sections as $key => $section):
              $lessons = $this->lms_model->get_lessons('section', $section['id'])->result_array();?>
              <div class="m-0">
                <div class="card-header ps-0" id="<?php echo 'heading-'.$section['id']; ?>">
                  <h5>
                    <a class="custom-accordion-title d-block py-1 button-stk" type="button" data-bs-toggle="collapse" data-bs-target="<?php echo '#collapse-'.$section['id']; ?>" <?php if($opened_section_id == $section['id']): ?> aria-expanded="true" <?php else: ?> aria-expanded="false" <?php endif; ?> aria-controls="<?php echo 'collapse-'.$section['id']; ?>" onclick="toggleAccordionIcon(this, '<?php echo $section['id']; ?>')">
                      <span class="badge"><?php echo $key++; ?></span>
                      <?php echo $section['title']; ?>
                    </a>
                  </h5>
                </div>
                <div id="<?php echo 'collapse-'.$section['id']; ?>" class="collapse <?php if($section_id == $section['id']) echo 'show'; ?>" aria-labelledby="<?php echo 'heading-'.$section['id']; ?>" data-bs-parent="#custom-accordion-one">
                  <div class="p-0">
                    <table class="w-100 table-tab">
                      <?php foreach ($lessons as $key => $lesson): ?>
                        <tr class="course-sidebar-td" style="background-color: <?php if ($lesson_id == $lesson['id']) echo '#EAEAEA'; else echo '#fff';?>;">
                          <td class="course-sidebar-td px-2 py-1">
                            <?php
                            $lesson_progress = lesson_progress($lesson['id']);
                            ?>
                            <?php if($lesson['lesson_type'] == 'quiz'): ?>
                              <div class="form-group">
                                <input type="checkbox" id="<?php echo $lesson['id']; ?>" onchange="markThisLessonAsCompleted(this.id)" <?php if($lesson_progress == 1): ?> checked <?php endif; ?>>
                                <label for="<?php echo $lesson['id']; ?>"></label>
                              </div>
                            <?php endif; ?>
                            <a href="<?php echo site_url('addons/lessons/play/'.slugify($course_details['title']).'/'.$course_id.'/'.$lesson['id']); ?>" id="<?php echo $lesson['id']; ?>" class="lst">
                              <?php echo $key+1; ?>:
                              <?php if ($lesson['lesson_type'] != 'other'): ?>
                                <?php echo $lesson['title']; ?>
                              <?php else: ?>
                                <?php echo $lesson['title']; ?>
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
          <!-- Lesson Content ends from here -->
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
  background: #FC7B30;
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
}

.custom-accordion-title:hover {
  color: #FC7B30 !important;
}

.badge {
  background: #FC7B30 !important;
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
}

.lst:hover {
  color: #6b7280 !important;
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
  border: solid #FC7B30;
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
</style>