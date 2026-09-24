<?php
// Modification: Filtering courses/classes for teachers based on 'marks' permission (interpreted as 'mange').
// Date: 2026-02-06

// Filter courses for teacher based on permissions
$teacher_allowed_class_ids = [];
$is_teacher = session()->get('teacher_login') == 1;

// Normalize courses payload (can be null, result object, stdClass rows, or array).
$to_rows = static function ($rows): array {
    if (is_array($rows)) {
        $normalized = [];
        foreach ($rows as $row) {
            if (is_array($row)) {
                $normalized[] = $row;
            } elseif (is_object($row)) {
                $normalized[] = (array) $row;
            }
        }
        return $normalized;
    }
    if (is_object($rows)) {
        if (method_exists($rows, 'result_array')) {
            $result = $rows;
            return is_array($result) ? $result : [];
        }
        if (method_exists($rows, 'getResultArray')) {
            $result = $rows->getResultArray();
            return is_array($result) ? $result : [];
        }
        if (method_exists($rows, 'getResult')) {
            $result = $rows->getResult();
            if (!is_array($result)) {
                return [];
            }
            $normalized = [];
            foreach ($result as $row) {
                $normalized[] = is_object($row) ? (array) $row : (is_array($row) ? $row : []);
            }
            return $normalized;
        }
    }
    return [];
};
$courses = $to_rows($courses ?? []);

if ($is_teacher) {
    $user_id = session()->get('user_id');
    // Modification: Get teacher ID specifically for the current school to avoid conflicts with multiple school profiles
    $school_id = school_id();
    $teacher_data = db()->table('teachers')->where('user_id', $user_id)->where('school_id', $school_id)->get()->getRowArray();
    $real_teacher_id = isset($teacher_data['id']) ? $teacher_data['id'] : 0;

    // Get allowed classes (permission 'marks' = 1)
    $perms = db()->table('teacher_permissions')->select('class_id')->where('teacher_id', $real_teacher_id)->where('marks', 1)->get()->getResultArray();
    $teacher_allowed_class_ids = array_column($perms, 'class_id');

    // Note: Course filtering is now handled in Lms_model::filter_course_for_teacher()
    // We only keep teacher_allowed_class_ids for UI display logic (hiding classes in the list)

    // Recalculate stats for teacher
    $active_courses_count = 0;
    $inactive_courses_count = 0;
    foreach ($courses as $c) {
        if ($c['status'] == 'active') $active_courses_count++;
        else $inactive_courses_count++;
    }
} else {
    // Original stats for admin
    $active_courses_count = 0;
    if (isset($status_wise_courses['active'])) {
        if (is_array($status_wise_courses['active'])) {
            $active_courses_count = count($status_wise_courses['active']);
        } elseif (is_array($status_wise_courses['active'])) {
            $active_courses_count = count($status_wise_courses['active']);
        }
    }

    $inactive_courses_count = 0;
    if (isset($status_wise_courses['inactive'])) {
        if (is_array($status_wise_courses['inactive'])) {
            $inactive_courses_count = count($status_wise_courses['inactive']);
        } elseif (is_array($status_wise_courses['inactive'])) {
            $inactive_courses_count = count($status_wise_courses['inactive']);
        }
    }
}

// Get data checks
$check_data = db()->table('sessions')->get();
?>

<style>
        /* Stats Grid */
        .aca-dashboard {
            margin-bottom: 2rem;
            padding: 0 0.5rem;
        }

        .aca-stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
        }

        .aca-stat-card {
            position: relative;
            background: white;
            border-radius: 20px;
            padding: 1.5rem;
            display: flex;
            align-items: center;
            gap: 1.25rem;
            border: 1px solid var(--aca-border);
            overflow: hidden;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .aca-stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        .aca-stat-icon-wrap {
            position: relative;
            width: 60px;
            height: 60px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.75rem;
            flex-shrink: 0;
        }

        .aca-stat-active .aca-stat-icon-wrap { background: linear-gradient(135deg, #10b981, #34d399); color: white; }
        .aca-stat-inactive .aca-stat-icon-wrap { background: linear-gradient(135deg, #64748b, #94a3b8); color: white; }

        .aca-stat-data { flex: 1; }

        .aca-stat-value {
            display: block;
            font-size: 2rem;
            font-weight: 800;
            color: var(--aca-dark);
            line-height: 1;
            margin-bottom: 0.25rem;
        }

        .aca-stat-label {
            font-size: 0.875rem;
            color: var(--aca-gray);
            font-weight: 500;
        }

        /* Filter Section */
        .aca-filter-section {
            padding: 1.5rem;
            background: white;
            border-radius: 16px;
            margin-bottom: 1.5rem;
            border: 1px solid var(--aca-border);
            box-shadow: 0 2px 12px rgba(0,0,0,0.04);
        }

        .aca-filter-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.25rem;
            align-items: end;
        }

        .aca-filter-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .aca-filter-label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.8125rem;
            font-weight: 600;
            color: var(--aca-dark);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .aca-filter-label i {
            color: var(--aca-primary);
            font-size: 1rem;
        }

        .aca-filter-input {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 2px solid var(--aca-border);
            border-radius: 10px;
            font-size: 0.9375rem;
            background: var(--aca-light);
            color: var(--aca-dark);
            transition: all 0.2s;
            cursor: pointer;
        }

        .aca-filter-input:focus {
            outline: none;
            border-color: var(--aca-primary);
            background: white;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
        }

        .aca-filter-input:hover {
            border-color: #cbd5e1;
        }

        .aca-filter-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            background: linear-gradient(135deg, var(--aca-primary), #8b5cf6);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 0.9375rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            height: fit-content;
            width: 100%;
        }

        .aca-filter-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
        }

        /* List Container (Div Layout) */
        .aca-list-container {
            background: var(--aca-white);
            border-radius: 20px;
            border: 1px solid var(--aca-border);
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        .aca-list-header {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 100px 100px;
            gap: 1rem;
            padding: 1rem 1.5rem;
            background: linear-gradient(135deg, var(--aca-dark) 0%, #334155 100%);
            color: white;
            font-size: 0.8125rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            align-items: center;
        }

        .aca-list-body {
            min-height: 300px;
            max-height: 600px;
            overflow-y: auto;
        }

        .aca-list-item {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 100px 100px;
            gap: 1rem;
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--aca-border);
            position: relative;
            align-items: center;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .aca-list-item:hover {
            background: linear-gradient(135deg, rgba(var(--aca-primary-rgb), 0.03), rgba(var(--aca-primary-rgb), 0.06));
        }

        .aca-list-item:last-child {
            border-bottom: none;
        }

        .aca-item-indicator {
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            background: transparent;
            transition: all 0.2s;
        }

        .aca-list-item:hover .aca-item-indicator {
            background: linear-gradient(180deg, var(--aca-primary), #8b5cf6);
        }

        /* Column Styles */
        .aca-col-title {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .aca-course-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--aca-primary), #8b5cf6);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.25rem;
            flex-shrink: 0;
            box-shadow: 0 4px 12px rgba(var(--aca-primary-rgb), 0.3);
        }

        .aca-course-info {
            display: flex;
            flex-direction: column;
            min-width: 0;
        }

        .aca-course-title {
            font-size: 0.9375rem;
            font-weight: 600;
            color: var(--aca-dark);
            margin-bottom: 0.25rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            text-decoration: none;
        }

        .aca-course-title:hover {
            color: var(--aca-primary);
        }

        .aca-course-meta {
            font-size: 0.75rem;
            color: var(--aca-gray);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .aca-col-class {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .aca-col-stats {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
            font-size: 0.8125rem;
            color: var(--aca-gray);
        }

        .aca-col-stats i {
            color: var(--aca-primary);
            margin-right: 0.35rem;
        }

        .aca-col-status {
            text-align: center;
        }

        .aca-col-actions {
            display: flex;
            justify-content: flex-end;
            padding-right: 1rem;
        }

        /* Badges */
        .aca-badge {
            padding: 0.35rem 0.75rem;
            border-radius: 8px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .aca-badge-dark {
            background: rgba(30, 41, 59, 0.1);
            color: #1e293b;
        }

        .aca-badge-success {
            background: rgba(16, 185, 129, 0.1);
            color: #10b981;
        }

        .aca-badge-secondary {
            background: rgba(100, 116, 139, 0.1);
            color: #64748b;
        }

        /* Pagination Bar */
        .aca-pagination-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.25rem 1.5rem;
            background: linear-gradient(135deg, var(--aca-light), #f1f5f9);
            border-top: 1px solid var(--aca-border);
            gap: 1.5rem;
            flex-wrap: wrap;
        }

        .aca-pagination-info {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .aca-info-text {
            font-size: 0.875rem;
            color: var(--aca-gray);
        }

        .aca-info-text strong {
            color: var(--aca-dark);
            font-weight: 600;
        }

        .aca-progress-bar {
            width: 120px;
            height: 4px;
            background: var(--aca-border);
            border-radius: 2px;
            overflow: hidden;
        }

        .aca-progress-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--aca-primary), #8b5cf6);
            border-radius: 2px;
            transition: width 0.3s ease;
        }

        .aca-pagination-nav {
            display: flex;
            align-items: center;
            gap: 0.375rem;
        }

        .aca-nav-btn {
            width: 40px;
            height: 40px;
            border: 2px solid var(--aca-border);
            background: var(--aca-white);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: var(--aca-gray);
            font-size: 1.25rem;
            transition: all 0.2s;
        }

        .aca-nav-btn:hover:not(:disabled) {
            border-color: var(--aca-primary);
            color: var(--aca-primary);
            transform: translateY(-1px);
        }

        .aca-nav-btn:disabled {
            opacity: 0.4;
            cursor: not-allowed;
        }

        .aca-page-indicators {
            display: flex;
            align-items: center;
            gap: 0.25rem;
            margin: 0 0.5rem;
        }

        .aca-page-btn {
            min-width: 40px;
            height: 40px;
            border: 2px solid var(--aca-border);
            background: var(--aca-white);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: var(--aca-gray);
            font-size: 0.875rem;
            font-weight: 600;
            transition: all 0.2s;
            padding: 0 0.5rem;
        }

        .aca-page-btn:hover {
            border-color: var(--aca-primary);
            color: var(--aca-primary);
        }

        .aca-page-btn.active {
            background: linear-gradient(135deg, var(--aca-primary), #8b5cf6);
            border-color: var(--aca-primary);
            color: white;
        }

        .aca-page-ellipsis {
            padding: 0 0.5rem;
            color: var(--aca-gray);
        }

        .aca-pagination-jump {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
            color: var(--aca-gray);
        }

        .aca-page-field {
            width: 60px;
            padding: 0.5rem;
            border: 2px solid var(--aca-border);
            border-radius: 8px;
            text-align: center;
            font-size: 0.875rem;
            background: var(--aca-white);
            transition: all 0.2s;
        }

        .aca-page-field:focus {
            outline: none;
            border-color: var(--aca-primary);
        }

        .aca-go-btn {
            padding: 0.5rem 1rem;
            background: var(--aca-primary);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 0.8125rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            text-transform: uppercase;
        }

        .aca-go-btn:hover {
            background: #4f46e5;
        }

        .empty_box img,
        .exp-empty-img {
            width: 150px;
            max-width: 100%;
            height: auto;
        }

        /* Responsive Styles */
        @media (max-width: 992px) {
            .aca-list-header, 
            .aca-list-item {
                grid-template-columns: 2fr 1fr 80px 80px;
            }
            
            .aca-list-header div:nth-child(2),
            .aca-list-item .aca-col-class {
                display: none; /* Hide Class column on tablet */
            }
        }

        @media (max-width: 768px) {
            .aca-stats-grid {
                grid-template-columns: 1fr;
            }

            .aca-filter-grid {
                grid-template-columns: 1fr;
            }

            /* List becomes card view on mobile */
            .aca-list-header {
                display: none;
            }

            .aca-list-item {
                display: flex;
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
                padding: 1.5rem;
            }

            .aca-col-title {
                width: 100%;
            }

            .aca-col-stats {
                flex-direction: row;
                gap: 1.5rem;
                width: 100%;
                padding-bottom: 0.5rem;
                border-bottom: 1px dashed var(--aca-border);
            }

            .aca-col-status {
                width: 100%;
                text-align: left;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

            .aca-col-actions {
                position: absolute;
                top: 1.25rem;
                right: 1.5rem;
                padding: 0;
            }

            .aca-pagination-bar {
                flex-direction: column;
                align-items: center;
                text-align: center;
                gap: 1rem;
            }
            
            .aca-pagination-nav {
                order: -1; /* Buttons on top */
            }

            /* Fix dropdown positioning on mobile */
            .aca-col-actions .dropdown-menu {
                position: absolute !important;
                top: 100% !important;
                right: 0 !important;
                left: auto !important;
                transform: none !important;
                margin-top: 0.5rem !important;
                min-width: 160px;
            }
        }
</style>

<!-- Dashboard Stats -->
<div class="aca-dashboard">
    <div class="aca-stats-grid">
        <!-- Active Courses -->
        <div class="aca-stat-card aca-stat-active">
            <div class="aca-stat-icon-wrap">
                <i class="dripicons-link"></i>
            </div>
            <div class="aca-stat-data">
                <span class="aca-stat-value"><?php echo $active_courses_count; ?></span>
                <span class="aca-stat-label"><?php echo get_phrase('active_courses'); ?></span>
            </div>
        </div>
        
        <!-- Inactive Courses -->
        <div class="aca-stat-card aca-stat-inactive">
            <div class="aca-stat-icon-wrap">
                <i class="dripicons-link-broken"></i>
            </div>
            <div class="aca-stat-data">
                <span class="aca-stat-value"><?php echo $inactive_courses_count; ?></span>
                <span class="aca-stat-label"><?php echo get_phrase('inactive_courses'); ?></span>
            </div>
        </div>
    </div>
</div>

<!-- Filter Section -->
<div class="aca-filter-section">
    <form action="javascript:void(0)" method="get">
        <div class="aca-filter-grid">
            <!-- Course Classes -->
            <div class="aca-filter-group">
                <label class="aca-filter-label">
                    <i class="mdi mdi-book-open-variant"></i>
                    <span><?php echo get_phrase('classes'); ?></span>
                </label>
                <select class="aca-filter-input select2" name="class_id" id="class_id_course">
                    <option value="all" <?php if ($selected_class_id == 'all') echo 'selected'; ?>><?php echo get_phrase('all'); ?></option>
                    <?php
                        if (session()->get('teacher_login') == 1) { 
                            // Using previously fetched teacher_allowed_class_ids and real_teacher_id
                            if (!empty($teacher_allowed_class_ids)) {
                                $teacher_classes = db()->table('classes')->distinct()->select('classes.id, classes.name')->join('teacher_permissions', 'teacher_permissions.class_id = classes.id', 'inner')->where('teacher_permissions.teacher_id', $real_teacher_id)->where('teacher_permissions.marks', 1)->where('classes.school_id', school_id())->orderBy('classes.name', 'ASC')->get()->getResultArray();
                            } else {
                                $teacher_classes = [];
                            }

                            if (empty($teacher_classes)): ?>
                                <option value="" disabled><?= get_phrase('no_classes_authorized_for_menu_management'); ?></option>
                            <?php else: ?>
                                <?php foreach ($teacher_classes as $class): ?>
                                    <option value="<?= $class['id']; ?>" <?= ($selected_class_id == $class['id']) ? 'selected' : ''; ?>>
                                        <?= htmlspecialchars($class['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        <?php } else {
                            $classes_rows = [];
                            if (is_array($classes)) {
                                $classes_rows = $classes;
                            } elseif (is_object($classes) && method_exists($classes, 'result_array')) {
                                $classes_rows = $classes;
                            }
                            foreach ($classes_rows as $class): ?>
                                <option value="<?php echo $class['id']; ?>" <?php if ($selected_class_id == $class['id']) echo 'selected'; ?>><?php echo $class['name']; ?></option>
                            <?php endforeach; ?>
                        <?php } ?>
                </select>
            </div>

            <!-- Course Teacher -->
            <?php if (session()->get('teacher_login') != 1): ?>
            <div class="aca-filter-group">
                <label class="aca-filter-label">
                    <i class="mdi mdi-account-tie"></i>
                    <span><?php echo get_phrase('instructor'); ?></span>
                </label>
                <select class="aca-filter-input select2" name="user_id" id='user_id'>
                    <option value="all" <?php if ($selected_user_id == 'all') echo 'selected'; ?>><?php echo get_phrase('all'); ?></option>
                    <?php
                        $teachers_rows = [];
                        if (is_array($all_teachers)) {
                            $teachers_rows = $all_teachers;
                        } elseif (is_object($all_teachers) && method_exists($all_teachers, 'result_array')) {
                            $teachers_rows = $all_teachers;
                        }
                        foreach ($teachers_rows as $teacher):
                    ?>
                        <option value="<?php echo $teacher['id']; ?>" <?php if ($selected_user_id == $teacher['id']) echo 'selected'; ?>><?php echo $teacher['name']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php else: ?>
                <input type="hidden" name="user_id" id="user_id" value="all">
            <?php endif; ?>

            <!-- Course Status -->
            <div class="aca-filter-group">
                <label class="aca-filter-label">
                    <i class="mdi mdi-toggle-switch"></i>
                    <span><?php echo get_phrase('status'); ?></span>
                </label>
                <select class="aca-filter-input select2" name="status" id='course_status'>
                    <option value="all" <?php if ($selected_status == 'all') echo 'selected'; ?>><?php echo get_phrase('all'); ?></option>
                    <option value="active" <?php if ($selected_status == 'active') echo 'selected'; ?>><?php echo get_phrase('active'); ?></option>
                    <option value="inactive" <?php if ($selected_status == 'inactive') echo 'selected'; ?>><?php echo get_phrase('inactive'); ?></option>
                </select>
            </div>

            <div class="aca-filter-group">
                <button type="submit" class="aca-filter-btn" onclick="filterCourse()" name="button">
                    <i class="mdi mdi-filter-outline"></i>
                    <span><?php echo get_phrase('filter'); ?></span>
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Course List Container -->
<div class="mt-4">
    <?php
    if (session()->get('teacher_login') == 1 && empty($courses) && empty($teacher_allowed_class_ids)): ?>
        <div class="exp-empty-state">
            <img class="exp-empty-img" width="150" height="107" alt="" src="<?php echo base_url('assets/backend/images/empty_box.png'); ?>" />
            <div class="exp-empty-text"><?php echo get_phrase('no_classes_authorized_for_menu_management'); ?></div>
        </div>
    <?php elseif (count($courses) > 0): ?>
        <div class="aca-list-container">
            <!-- Header -->
            <div class="aca-list-header">
                <div><?php echo get_phrase('title'); ?></div>
                <div><?php echo get_phrase('class'); ?></div>
                <div><?php echo get_phrase('lesson_and_section'); ?></div>
                <div class="text-center"><?php echo get_phrase('status'); ?></div>
                <div class="text-end"><?php echo get_phrase('actions'); ?></div>
            </div>

            <!-- Body -->
            <div class="aca-list-body">
                <?php
                    $academy_lms_model = null;
                    if (isset($this->lms_model) && is_object($this->lms_model)) {
                        $academy_lms_model = $this->lms_model;
                    } else {
                        try {
                            $academy_lms_model = model('addons/Lms_model');
                        } catch (\Throwable $e) {
                            $academy_lms_model = null;
                        }
                    }
                ?>
                <?php foreach ($courses as $key => $course):
                    // Get teachers
                    $teachers = $academy_lms_model ? $academy_lms_model->get_teachers_by_course($course['id']) : [];
                    $teacher_names = [];
                    if (!empty($teachers)) {
                        foreach ($teachers as $t) $teacher_names[] = $t['name'];
                    }

                    // Get classes
                    $classes = $academy_lms_model ? $academy_lms_model->get_classes_by_course($course['id']) : [];
                    $class_names = [];
                    if (!empty($classes)) {
                        foreach ($classes as $c) {
                            // Filter for teacher
                            if ($is_teacher && !in_array($c['id'], $teacher_allowed_class_ids)) continue;
                            $class_names[] = $c['name'];
                        }
                    }
                    
                    $sections = $academy_lms_model ? $academy_lms_model->get_section('course', $course['id']) : [];
                    $lessons = $academy_lms_model ? $academy_lms_model->get_lessons('course', $course['id']) : [];
                    $section_count = 0;
                    if (is_array($sections)) {
                        $section_count = count($sections);
                    } elseif (is_array($sections)) {
                        $section_count = count($sections);
                    } elseif (is_object($sections) && method_exists($sections, 'getNumRows')) {
                        $section_count = $sections->getNumRows();
                    }

                    $lesson_rows = [];
                    if (is_array($lessons)) {
                        $lesson_rows = $lessons;
                    } elseif (is_object($lessons) && method_exists($lessons, 'result_array')) {
                        $lesson_rows = $lessons;
                    } elseif (is_object($lessons) && method_exists($lessons, 'getResultArray')) {
                        $lesson_rows = $lessons->getResultArray();
                    }
                    $lesson_count = count($lesson_rows);
                    
                    // Find first available lesson
                    $first_lesson_id = null;
                    foreach ($lesson_rows as $lesson) {
                        if (is_object($lesson)) {
                            $lesson = (array) $lesson;
                        }
                        if (strtolower((string) ($lesson['lesson_type'] ?? '')) != 'quiz') {
                            $first_lesson_id = $lesson['id'] ?? null;
                            break;
                        }
                    }
                    if ($first_lesson_id === null && $lesson_count > 0) {
                        $first_lesson = $lesson_rows[0];
                        if (is_object($first_lesson)) {
                            $first_lesson = (array) $first_lesson;
                        }
                        $first_lesson_id = $first_lesson['id'] ?? null;
                    }
                ?>
                    <div class="aca-list-item">
                        <div class="aca-item-indicator"></div>
                        
                        <!-- Title Col -->
                        <div class="aca-col-title">
                            <div class="aca-course-icon">
                                <i class="mdi mdi-book-open-variant"></i>
                            </div>
                            <div class="aca-course-info">
                                <a href="<?php echo site_url('addons/courses/course_edit/' . $course['id']); ?>" class="aca-course-title" title="<?php echo $course['title']; ?>">
                                    <?php echo $course['title']; ?>
                                </a>
                                <div class="aca-course-meta">
                                    <i class="mdi mdi-account-tie"></i>
                                    <?php echo implode(', ', $teacher_names); ?>
                                </div>
                            </div>
                        </div>

                        <!-- Class Col -->
                        <div class="aca-col-class">
                            <?php foreach ($class_names as $cname): ?>
                                <span class="aca-badge aca-badge-dark"><?php echo $cname; ?></span>
                            <?php endforeach; ?>
                        </div>

                        <!-- Stats Col -->
                        <div class="aca-col-stats">
                            <div><i class="mdi mdi-folder-outline"></i> <?php echo $section_count; ?> <?php echo get_phrase('sections'); ?></div>
                            <div><i class="mdi mdi-file-document-outline"></i> <?php echo $lesson_count; ?> <?php echo get_phrase('lessons'); ?></div>
                        </div>

                        <!-- Status Col -->
                        <div class="aca-col-status">
                            <?php if ($course['status'] == 'active'): ?>
                                <span class="aca-badge aca-badge-success"><?php echo get_phrase('active'); ?></span>
                            <?php else: ?>
                                <span class="aca-badge aca-badge-secondary"><?php echo get_phrase('inactive'); ?></span>
                            <?php endif; ?>
                        </div>

                        <!-- Actions Col -->
                        <div class="aca-col-actions">
                            <div class="dropdown">
                                <button type="button" class="btn btn-sm btn-icon btn-rounded btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="mdi mdi-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="<?php echo site_url('addons/lessons/play/' . slugify($course['title']) . '/' . $course['id'] . '/' . $first_lesson_id); ?>" target="_blank"><i class="mdi mdi-play-circle-outline me-1"></i> <?php echo get_phrase('start_course'); ?></a></li>
                                    <li><a class="dropdown-item" href="<?php echo site_url('addons/courses/course_edit/' . $course['id']); ?>"><i class="mdi mdi-pencil-outline me-1"></i> <?php echo get_phrase('edit'); ?></a></li>
                                    <li>
                                        <?php if ($course['status'] == 'active'): ?>
                                            <a class="dropdown-item" href="javascript:void(0)" onclick="course_activity('<?= $course['id']; ?>')">
                                                <i class="mdi mdi-toggle-switch-off-outline me-1"></i> <?php echo get_phrase('mark_as_inactive'); ?>
                                            </a>
                                        <?php else: ?>
                                            <a class="dropdown-item" href="javascript:void(0)" onclick="course_activity('<?= $course['id']; ?>')">
                                                <i class="mdi mdi-toggle-switch-outline me-1"></i> <?php echo get_phrase('mark_as_active'); ?>
                                            </a>
                                        <?php endif; ?>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item text-danger" href="#" onclick="confirmModal('<?php echo site_url('addons/courses/index/delete/' . $course['id']); ?>', filterCourse)"><i class="mdi mdi-delete-outline me-1"></i> <?php echo get_phrase('delete'); ?></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- Pagination -->
        <div class="aca-pagination-bar">
            <div class="aca-pagination-info">
                <span class="aca-info-text">
                    <?php echo get_phrase('showing'); ?> <strong><?php echo count($courses); ?></strong> <?php echo get_phrase('courses'); ?>
                </span>
                <div class="aca-progress-bar">
                    <div class="aca-progress-fill" style="width: 100%;"></div>
                </div>
            </div>
            
            <div class="aca-pagination-nav">
                <button class="aca-nav-btn" disabled><i class="mdi mdi-chevron-left"></i></button>
                <div class="aca-page-indicators">
                    <button class="aca-page-btn active">1</button>
                    <!-- <button class="aca-page-btn">2</button> -->
                    <!-- <span class="aca-page-ellipsis">...</span> -->
                </div>
                <button class="aca-nav-btn" disabled><i class="mdi mdi-chevron-right"></i></button>
            </div>

            <div class="aca-pagination-jump">
                <span><?php echo get_phrase('go_to_page'); ?></span>
                <input type="text" class="aca-page-field" value="1" readonly>
                <button class="aca-go-btn" disabled>GO</button>
            </div>
        </div>

    <?php else: ?>
        <?php include APPPATH . 'Views/backend/empty.php'; ?>
    <?php endif; ?>
</div>

<script type="text/javascript">
    if ($.fn.select2) {
        $('select.select2:not(.normal)').each(function() {
            $(this).select2();
        });
    }
</script>