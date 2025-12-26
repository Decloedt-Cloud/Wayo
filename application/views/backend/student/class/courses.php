<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="row ">
  <div class="col-xl-12">
    <div class="card">
      <div class="card-body py-2">
        <h4 class="page-title d-inline-block">
            <i class="fas fa-layer-group fa-fw"></i> <?php echo get_phrase('class_courses'); ?> : <?php echo html_escape($class_details['name']); ?>
        </h4>
        <button type="button" class="btn btn-outline-secondary btn-rounded alignToTitle float-end mt-1" onclick="history.back();">
            <i class="mdi mdi-arrow-left"></i> <?php echo get_phrase('back'); ?>
        </button>
      </div> <!-- end card body-->
    </div> <!-- end card -->
  </div><!-- end col-->
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <?php if (!empty($courses)): ?>
                    <div class="row">
                        <?php foreach ($courses as $course):
                            $owner_names = [];

                            if (!empty($course['user_id'])) {
                                $name = $this->user_model->get_user_details($course['user_id'], 'name');
                                if (!empty($name)) {
                                    $owner_names[] = $name;
                                }
                            }

                            $mentors = $this->db->where('course_id', $course['id'])->get('course_teachers')->result_array();
                            foreach ($mentors as $mentor) {
                                if (!empty($mentor['user_id'])) {
                                    $name = $this->user_model->get_user_details($mentor['user_id'], 'name');
                                    if (!empty($name)) {
                                        $owner_names[] = $name;
                                    }
                                }
                            }

                            $progress_value = course_progress($course['id']);
                            $lessons = $this->lms_model->get_lessons('course', $course['id']);
                            // Trouver la première leçon qui n'est pas un quiz
                            $first_lesson = null;
                            foreach ($lessons->result_array() as $lesson) {
                                if (strtolower($lesson['lesson_type']) != 'quiz') {
                                    $first_lesson = $lesson['id'];
                                    break;
                                }
                            }
                            // Si aucune leçon non-quiz trouvée, prendre la première disponible
                            if ($first_lesson === null && $lessons->num_rows() > 0) {
                                $lessons->data_seek(0);
                                $first_lesson = $lessons->row('id');
                            }

                            if (file_exists('uploads/course_thumbnail/' . $course['thumbnail'])) {
                                $course_thumbnail = base_url('uploads/course_thumbnail/' . $course['thumbnail']);
                            } else {
                                $course_thumbnail = base_url('uploads/course_thumbnail/placeholder.png');
                            }
                        ?>
                            <div class="col-md-6 col-lg-4 col-xl-3 colcourses">
                                <div class="card d-block">
                                    <div class="w-100 bg_course_thumbnail" style="background-image: url('<?php echo $course_thumbnail; ?>');"></div>
                                    <div class="card-body">
                                        <h4 class="card-title"><?php echo html_escape($course['title']); ?></h4>
                                        <div class="w-100 mb-3">
                                            <div class="media">
                                                <img class="mr-2 rounded-circle" src="<?= $this->user_model->get_user_image($course['user_id']); ?>" width="30" alt="instructor">
                                                <div class="media-body pt-1">
                                                    <span class="font-13 text-muted"><?php echo implode(', ', $owner_names); ?></span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-sm-10 col-md-10">
                                                <div class="progress mb-2 h-5px">
                                                    <div class="progress-bar <?php echo ($progress_value >= 100) ? 'bg-green-low' : ''; ?>" role="progressbar" style="width: <?php echo $progress_value; ?>%;" aria-valuenow="<?php echo $progress_value; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                            </div>
                                            <div class="col-sm-2 col-md-2 text-left p-0 progress_value_count colcourses">
                                                <p><?php echo ceil($progress_value); ?>%</p>
                                            </div>
                                        </div>

                                        <div class="w-100 text-center">
                                            <?php if ($lessons->num_rows() > 0): ?>
                                                <a href="<?php echo site_url('addons/lessons/play/' . slugify($course['title']) . '/' . $course['id'] . '/' . $first_lesson); ?>" class="btn <?php echo ($progress_value > 0) ? 'btn-secondary' : 'btn-primary'; ?> mw-50">
                                                    <?php echo ($progress_value > 0) ? get_phrase('continue_lesson') : get_phrase('start_course'); ?>
                                                </a>
                                            <?php else: ?>
                                                <a href="javascript:void(0);"  class="btn btn-outline-secondary mw-50"><?php echo get_phrase('under_construction'); ?></a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <?php include APPPATH . 'views/backend/empty.php'; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
