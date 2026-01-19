<?php
$user_id = $this->session->userdata('user_id');
$active_school_id = $this->session->userdata('active_school_id');
// Forcer l'utilisation de l'école active pour l'étudiant
$selected_school_id = $active_school_id ?? $selected_school_id ?? 'all';
$selected_class_id = $selected_class_id ?? 'all';
$selected_user_id = $selected_user_id ?? 'all';
?>

<!--title-->
<div class="col-xl-12">
    <div class="header-card">
        <div class="card-body">
            <h4 class="page-title d-inline-block">
                <i class="fas fa-layer-group fa-fw"></i> <?= get_phrase('classes'); ?>
            </h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="main-card mb-3">
            <div class="card-body">
                <form class="row justify-content-center mb-4" action="javascript:void(0)">
                    <!-- Sélection classe -->
                    <div class="col-md-2 mb-1">
                        <label><?= get_phrase('classes'); ?></label>
                        <select class="form-control" name="class_id" id="class_id_course">
                            <option value="all" <?= $selected_class_id == 'all' ? 'selected' : ''; ?>><?= get_phrase('all'); ?></option>
                            <?php
                            if ($selected_school_id != "all") {
                                $classes = $this->db->select('id, name')
                                    ->where('school_id', $selected_school_id)
                                    ->get('classes')->result_array();

                                foreach ($classes as $classe): ?>
                                    <option value="<?= $classe['id']; ?>" <?= $selected_class_id == $classe['id'] ? 'selected' : ''; ?>>
                                        <?= $classe['name']; ?>
                                    </option>
                                <?php endforeach;
                            } ?>
                        </select>
                    </div>

                    <!-- Sélection enseignant -->
                    <?php if ($this->session->userdata('teacher_login') != 1): ?>
                        <div class="col-md-2 mb-1">
                            <label><?= get_phrase('instructor'); ?></label>
                            <?php
                            $teacher_ids = [];
                            $courses = $this->db->select('course.id')
                                ->from('course')
                                ->join('course_classes', 'course_classes.course_id = course.id')
                                ->where('course.school_id !=', null);

                            if ($selected_school_id != 'all')
                                $courses->where('course.school_id', $selected_school_id);
                            if ($selected_class_id != 'all')
                                $courses->where('course_classes.class_id', $selected_class_id);

                            $courses = $courses->get()->result_array();

                            foreach ($courses as $course) {
                                foreach ($this->lms_model->get_teachers_by_course($course['id']) as $teacher) {
                                    $teacher_ids[$teacher['id']] = $teacher['name'];
                                }
                            }
                            ?>
                            <select class="form-control" name="user_id" id="user_id">
                                <option value="all" <?= $selected_user_id == 'all' ? 'selected' : ''; ?>><?= get_phrase('all'); ?></option>
                                <?php foreach ($teacher_ids as $id => $name): ?>
                                    <option value="<?= $id; ?>" <?= $selected_user_id == $id ? 'selected' : ''; ?>><?= $name; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php endif; ?>

                    <!-- Bouton -->
                    <div class="col-md-1 btncol btnfilter">
                        <label class="text-white">.</label>
                        <button type="submit" class="btn btn-secondary btn-block" onclick="filterCourse()"><?= get_phrase('filter'); ?></button>
                    </div>
                </form>

                <?php
                // 🔹 Récupérer les étudiants liés à l’utilisateur
                $student_ids = $this->db->select('id')
                    ->from('students')
                    ->where(['user_id' => $user_id, 'status' => 1])
                    ->get()->result_array();

                $student_ids = array_column($student_ids, 'id');

                $classes = [];
                if (!empty($student_ids)) {
                    $this->db->select('classes.*, students.user_id AS student_id, students.school_id AS student_school_id')
                        ->from('classes')
                        ->join('students', 'students.school_id = classes.school_id')
                        ->where('classes.statut', 'active')
                        ->where_in('students.id', $student_ids)
                        ->group_start()
                        ->where('classes.date_fin >=', date('Y-m-d'))
                        ->or_where('classes.date_fin', null)
                        ->group_end();

                    if ($selected_school_id != 'all')
                        $this->db->where('classes.school_id', $selected_school_id);
                    if ($selected_class_id != 'all')
                        $this->db->where('classes.id', $selected_class_id);

                    $classes = $this->db->get()->result_array();
                }
                ?>

                <?php if (!empty($classes)): ?>
                    <div class="row mt-2">
                        <?php foreach ($classes as $class):
                            $school_id = $class['school_id'];

                            $enrols_count = $this->db->where(['school_id' => $school_id, 'class_id' => $class['id']])
                                ->count_all_results('enrols');

                            $currencies = $this->db->select('system_currency')
                                ->where('school_id', $school_id)
                                ->get('settings_school')->row('system_currency');

                            $max_members = (int)($class['nombre_max_membre'] ?? 0);
                            $is_full = $max_members > 0 && $enrols_count >= $max_members;

                            $is_enrolled = $this->db->where([
                                'student_id' => $class['student_id'],
                                'school_id' => $school_id,
                                'class_id' => $class['id']
                            ])->count_all_results('enrols') > 0;
                            ?>

                            <div class="col-md-6 col-lg-4 col-xl-3 colcourses">
                                <div class="card d-block">
                                    <div class="w-100 bg_course_thumbnail"
                                         style="background-image: url('<?= base_url('uploads/class/' . ($class['photo'] ?: 'placeholder.png')); ?>');">
                                    </div>
                                    <div class="card-body">
                                        <h4 class="card-title"><?= $class['name']; ?></h4>
                                        <div class="text-center mt-3">
                                            <?php if ($is_enrolled): ?>
                                                <a href="<?= site_url('student/courses/' . $class['id']); ?>"
                                                   class="btn btn-primary"><?= get_phrase('view'); ?></a>
                                            <?php elseif ($is_full): ?>
                                                <a href="javascript:void(0);" class="btn btn-outline-secondary">
                                                    <?= get_phrase('waiting_list'); ?>
                                                </a>
                                            <?php else: ?>
                                                <a href="javascript:;" onclick="rightModal('<?= site_url('modal/popup/academy/add/' . $class['student_id'] . '/' . $class['id'] . '/' . $school_id . '/' . $class['price'] . '/' . $currencies) ?>','<?= get_phrase('join'); ?>');"
                                                   class="btn btn-primary">
                                                    <?= get_phrase('Join ') . 'with ' . $class['price'] . ' ' . $currencies; ?>
                                                </a>
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

<script>
function schoolWiseClasse(school_id) {
    $.get("<?= route('academy/list/'); ?>" + school_id, function (response) {
        $('#class_id_course').html(response);
    });
}
</script>
