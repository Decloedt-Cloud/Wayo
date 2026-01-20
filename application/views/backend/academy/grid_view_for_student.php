<?php
$user_id = $this->session->userdata('user_id');
$active_school_id = $this->session->userdata('active_school_id');
// Forcer l'utilisation de l'école active pour l'étudiant
$selected_school_id = $active_school_id ?? $selected_school_id ?? 'all';
$selected_class_id = $selected_class_id ?? 'all';
$selected_user_id = $selected_user_id ?? 'all';
?>

<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
    /* ========== MODERN DASHBOARD STYLES (Copied from Dashboard) ========== */
    .modern-dashboard {
        --primary: #6366f1;
        --primary-light: #818cf8;
        --primary-lighter: #e0e7ff;
        --primary-dark: #4338ca;
        --bg-main: #f8fafc;
        --bg-card: #ffffff;
        --text-dark: #1e293b;
        --text-muted: #64748b;
        --border-color: #e2e8f0;
        --shadow-sm: 0 1px 2px rgba(0,0,0,0.05);
        --shadow-md: 0 4px 6px -1px rgba(0,0,0,0.07), 0 2px 4px -1px rgba(0,0,0,0.04);
        --shadow-lg: 0 10px 15px -3px rgba(0,0,0,0.08), 0 4px 6px -2px rgba(0,0,0,0.04);
        
        font-family: 'DM Sans', sans-serif;
        background: var(--bg-main);
        min-height: 100vh;
        padding: 1.5rem;
        margin: -15px -15px 0 -15px;
    }

    /* Header */
    .dash-header {
        margin-bottom: 2rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .dash-header h1 {
        font-family: 'Outfit', sans-serif;
        font-size: 1.875rem;
        font-weight: 700;
        color: var(--text-dark);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .dash-header h1 .icon-box {
        width: 48px;
        height: 48px;
        background: linear-gradient(135deg, var(--primary), var(--primary-light));
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.25rem;
        box-shadow: 0 4px 14px rgba(99, 102, 241, 0.4);
    }

    /* Modern Card */
    .modern-card {
        background: var(--bg-card);
        border-radius: 20px;
        box-shadow: var(--shadow-md);
        border: 1px solid var(--border-color);
        overflow: hidden;
        transition: all 0.3s ease;
        height: 100%; /* For grid consistency */
    }

    .modern-card:hover {
        box-shadow: var(--shadow-lg);
        transform: translateY(-2px);
    }

    .modern-card-body {
        padding: 1.5rem;
    }

    /* Form Styles */
    .modern-filter-form label {
        font-weight: 600;
        color: var(--text-dark);
        font-size: 0.875rem;
        margin-bottom: 0.5rem;
    }

    .modern-select {
        border: 2px solid var(--border-color);
        border-radius: 12px;
        padding: 0.625rem 1rem;
        height: auto;
        font-size: 0.9375rem;
        color: var(--text-dark);
        transition: all 0.2s;
    }

    .modern-select:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
    }

    .modern-btn {
        background: var(--primary);
        color: white;
        border: none;
        border-radius: 12px;
        padding: 0.75rem 1.5rem;
        font-weight: 600;
        transition: all 0.2s;
    }

    .modern-btn:hover {
        background: var(--primary-dark);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
        color: white;
    }

    /* Course Card Specifics */
    .course-card .course-img-wrapper {
        position: relative;
        height: 200px; /* Increased height */
        overflow: hidden;
        background: #f1f5f9; /* Fallback background */
    }

    .course-card .course-img {
        width: 100%;
        height: 100%;
        object-fit: cover; /* Ensures image covers area */
        object-position: center;
        transition: transform 0.5s ease;
    }

    .course-card:hover .course-img {
        transform: scale(1.05);
    }

    .course-card .course-content {
        padding: 1.5rem;
    }

    .course-card .course-title {
        font-family: 'Outfit', sans-serif;
        font-size: 1.125rem;
        font-weight: 700;
        color: var(--text-dark);
        margin-bottom: 1rem;
        line-height: 1.4;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        height: 3.2em; /* Force height for alignment */
    }

    .course-card .course-actions {
        margin-top: 1rem;
    }

    .btn-course {
        width: 100%;
        padding: 0.625rem;
        border-radius: 10px;
        font-weight: 600;
        font-size: 0.875rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }
    
    .btn-course.primary {
        background: linear-gradient(135deg, var(--primary), var(--primary-light));
        color: white;
        border: none;
    }
    
    .btn-course.outline {
        background: transparent;
        border: 2px solid var(--border-color);
        color: var(--text-muted);
    }
    
    .btn-course.outline:hover {
        border-color: var(--primary);
        color: var(--primary);
    }

    /* Animation */
    .fade-up {
        animation: fadeUp 0.5s ease forwards;
        opacity: 0;
    }
    
    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .delay-1 { animation-delay: 0.1s; }
    .delay-2 { animation-delay: 0.2s; }
</style>

<div class="modern-dashboard">
    
    <!-- Header -->
    <div class="dash-header fade-up">
        <h1>
            <span class="icon-box"><i class="fas fa-layer-group"></i></span>
            <?= get_phrase('classes'); ?>
        </h1>
        <div class="date-badge">
            <i class="far fa-calendar-alt"></i>
            <?php echo date('l, j F Y'); ?>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="row mb-4 fade-up delay-1">
        <div class="col-12">
            <div class="modern-card">
                <div class="modern-card-body">
                    <form class="row align-items-end modern-filter-form" action="javascript:void(0)">
                        <!-- Sélection classe -->
                        <div class="col-md-4 col-lg-3 mb-3 mb-md-0">
                            <label><?= get_phrase('classes'); ?></label>
                            <select class="form-control modern-select" name="class_id" id="class_id_course">
                                <option value="all" <?= $selected_class_id == 'all' ? 'selected' : ''; ?>><?= get_phrase('all'); ?></option>
                                <?php
                                if ($selected_school_id != "all") {
                                    $classes_list = $this->db->select('id, name')
                                        ->where('school_id', $selected_school_id)
                                        ->get('classes')->result_array();

                                    foreach ($classes_list as $classe): ?>
                                        <option value="<?= $classe['id']; ?>" <?= $selected_class_id == $classe['id'] ? 'selected' : ''; ?>>
                                            <?= $classe['name']; ?>
                                        </option>
                                    <?php endforeach;
                                } ?>
                            </select>
                        </div>

                        <!-- Sélection enseignant -->
                        <?php if ($this->session->userdata('teacher_login') != 1): ?>
                            <div class="col-md-4 col-lg-3 mb-3 mb-md-0">
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
                                <select class="form-control modern-select" name="user_id" id="user_id">
                                    <option value="all" <?= $selected_user_id == 'all' ? 'selected' : ''; ?>><?= get_phrase('all'); ?></option>
                                    <?php foreach ($teacher_ids as $id => $name): ?>
                                        <option value="<?= $id; ?>" <?= $selected_user_id == $id ? 'selected' : ''; ?>><?= $name; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        <?php endif; ?>

                        <!-- Bouton -->
                        <div class="col-md-3 col-lg-2">
                            <button type="submit" class="modern-btn w-100" onclick="filterCourse()">
                                <i class="fas fa-filter mr-2"></i> <?= get_phrase('filter'); ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php
    // 🔹 Récupérer les étudiants liés à l’utilisateur
    $student_ids = $this->db->select('id')
        ->from('students')
        ->where(['user_id' => $user_id, 'status' => 1])
        ->get()->result_array();

    $student_ids = array_column($student_ids, 'id');

    $classes = [];
    if (!empty($student_ids)) {
        // OPTIMIZATION: Select specific columns and fix ID aliasing
        $this->db->select('classes.*, students.user_id AS student_user_id, students.id AS real_student_id, students.school_id AS student_school_id')
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
        <?php
        // --- OPTIMIZATION START ---
        // 1. Collect IDs
        $class_ids = array_column($classes, 'id');
        $school_ids = array_unique(array_column($classes, 'school_id'));
        $student_profile_ids = array_unique(array_column($classes, 'real_student_id'));

        // 2. Batch Fetch Enrollment Counts (Global for class)
        $enrollment_counts = [];
        if (!empty($class_ids)) {
            $this->db->select('class_id, COUNT(*) as count');
            $this->db->where_in('class_id', $class_ids);
            $this->db->group_by('class_id');
            $query_counts = $this->db->get('enrols')->result_array();
            foreach ($query_counts as $row) {
                $enrollment_counts[$row['class_id']] = $row['count'];
            }
        }

        // 3. Batch Fetch Currencies
        $currency_map = [];
        if (!empty($school_ids)) {
            $this->db->select('school_id, system_currency');
            $this->db->where_in('school_id', $school_ids);
            $query_currency = $this->db->get('settings_school')->result_array();
            foreach ($query_currency as $row) {
                $currency_map[$row['school_id']] = $row['system_currency'];
            }
        }

        // 4. Batch Check "Is Enrolled" for current student(s)
        $my_enrollments = [];
        if (!empty($class_ids) && !empty($student_profile_ids)) {
            $this->db->select('class_id, student_id');
            $this->db->where_in('class_id', $class_ids);
            $this->db->where_in('student_id', $student_profile_ids);
            $query_enrols = $this->db->get('enrols')->result_array();
            foreach ($query_enrols as $row) {
                $my_enrollments[$row['class_id'] . '_' . $row['student_id']] = true;
            }
        }
        // --- OPTIMIZATION END ---
        ?>

        <div class="row fade-up delay-2">
            <?php foreach ($classes as $class):
                $school_id = $class['school_id'];
                $student_profile_id = $class['real_student_id']; // Use correct student ID

                // Use pre-fetched data
                $enrols_count = $enrollment_counts[$class['id']] ?? 0;
                $currencies = $currency_map[$school_id] ?? ' ';

                $max_members = (int)($class['nombre_max_membre'] ?? 0);
                $is_full = $max_members > 0 && $enrols_count >= $max_members;

                // Check pre-fetched map
                $is_enrolled = isset($my_enrollments[$class['id'] . '_' . $student_profile_id]);
                ?>

                <div class="col-md-6 col-lg-4 col-xl-3 mb-4">
                    <div class="modern-card course-card">
                        <div class="course-img-wrapper">
                            <img src="<?= base_url('uploads/class/' . ($class['photo'] ?: 'placeholder.png')); ?>" 
                                 class="course-img" 
                                 alt="<?= $class['name']; ?>"
                                 onerror="this.src='<?= base_url('uploads/class/placeholder.png'); ?>'">
                        </div>
                        <div class="course-content">
                            <h4 class="course-title"><?= $class['name']; ?></h4>
                            <div class="course-actions">
                                <?php if ($is_enrolled): ?>
                                    <a href="<?= site_url('student/courses/' . $class['id']); ?>"
                                       class="btn-course primary">
                                        <i class="fas fa-eye"></i> <?= get_phrase('view'); ?>
                                    </a>
                                <?php elseif ($is_full): ?>
                                    <a href="javascript:void(0);" class="btn-course outline" style="cursor: not-allowed; opacity: 0.6;">
                                        <i class="fas fa-lock"></i> <?= get_phrase('waiting_list'); ?>
                                    </a>
                                <?php else: ?>
                                    <a href="javascript:;" onclick="rightModal('<?= site_url('modal/popup/academy/add/' . $student_profile_id . '/' . $class['id'] . '/' . $school_id . '/' . $class['price'] . '/' . $currencies) ?>','<?= get_phrase('join'); ?>');"
                                       class="btn-course primary">
                                        <i class="fas fa-plus-circle"></i> <?= get_phrase('Join ') . ' ' . $class['price'] . ' ' . $currencies; ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="modern-card fade-up delay-2">
            <div class="modern-card-body text-center p-5">
                <?php include APPPATH . 'views/backend/empty.php'; ?>
            </div>
        </div>
    <?php endif; ?>

</div>

<script>
function schoolWiseClasse(school_id) {
    $.get("<?= route('academy/list/'); ?>" + school_id, function (response) {
        $('#class_id_course').html(response);
    });
}
</script>