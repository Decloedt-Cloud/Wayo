<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
    /* ========== MODERN DASHBOARD STYLES ========== */
    .modern-dashboard {
        --primary: #6366f1;
        --primary-light: #818cf8;
        --primary-lighter: #e0e7ff;
        --primary-dark: #4338ca;
        --secondary: #10b981; /* Green for success */
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

    .btn-back {
        background: white;
        border: 1px solid var(--border-color);
        color: var(--text-dark);
        padding: 0.6rem 1.25rem;
        border-radius: 50px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        cursor: pointer;
        transition: all 0.2s;
        box-shadow: var(--shadow-sm);
    }
    
    .btn-back:hover {
        background: var(--bg-main);
        color: var(--primary);
        transform: translateX(-2px);
    }

    /* Course Card */
    .modern-card {
        background: var(--bg-card);
        border-radius: 20px;
        box-shadow: var(--shadow-md);
        border: 1px solid var(--border-color);
        overflow: hidden;
        transition: all 0.3s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .modern-card:hover {
        box-shadow: var(--shadow-lg);
        transform: translateY(-4px);
    }

    .course-img-wrapper {
        height: 180px;
        overflow: hidden;
        position: relative;
    }

    .course-img-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .modern-card:hover .course-img-wrapper img {
        transform: scale(1.05);
    }

    .modern-card-body {
        padding: 1.5rem;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }

    .course-title {
        font-family: 'Outfit', sans-serif;
        font-size: 1.15rem;
        font-weight: 700;
        color: var(--text-dark);
        margin-bottom: 1rem;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        height: 2.8rem;
        line-height: 1.4;
    }

    .instructor-box {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 1.5rem;
    }

    .instructor-avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid var(--primary-lighter);
    }

    .instructor-name {
        font-size: 0.85rem;
        color: var(--text-muted);
        font-weight: 500;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 180px;
    }

    .course-progress-wrapper {
        margin-top: auto; /* Push to bottom */
        margin-bottom: 1.25rem;
    }

    .course-progress-bar {
        height: 8px;
        background: var(--bg-main);
        border-radius: 10px;
        overflow: hidden;
        margin-bottom: 0.5rem;
        border: 1px solid var(--border-color);
    }

    .course-progress-fill {
        height: 100%;
        background: var(--primary);
        border-radius: 10px;
        transition: width 1s ease;
    }
    
    .course-progress-fill.completed {
        background: var(--secondary);
    }

    .course-progress-text {
        display: flex;
        justify-content: space-between;
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--text-muted);
    }

    .action-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        width: 100%;
        padding: 0.875rem;
        text-align: center;
        background: var(--bg-main);
        color: var(--primary);
        font-weight: 600;
        border-radius: 12px;
        text-decoration: none;
        transition: all 0.3s;
        border: 1px solid var(--primary-lighter);
    }

    .action-btn:hover {
        background: var(--primary);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
        border-color: var(--primary);
    }

    .action-btn.primary {
        background: var(--primary);
        color: white;
        border: 1px solid var(--primary);
    }

    .action-btn.primary:hover {
        background: var(--primary-dark);
        border-color: var(--primary-dark);
    }
    
    .action-btn.disabled {
        opacity: 0.6;
        cursor: not-allowed;
        background: var(--bg-main);
        color: var(--text-muted);
        border-color: var(--border-color);
    }
    
    .action-btn.disabled:hover {
        transform: none;
        box-shadow: none;
        background: var(--bg-main);
        color: var(--text-muted);
    }

    /* Animations */
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
    .delay-3 { animation-delay: 0.3s; }
</style>

<div class="modern-dashboard">
    
    <!-- Header -->
    <div class="dash-header fade-up">
        <h1>
            <span class="icon-box"><i class="fas fa-layer-group"></i></span>
            <?php echo get_phrase('class_courses'); ?>
            <span style="font-weight: 400; color: var(--text-muted); font-size: 1.5rem; margin-left: 0.5rem;">/ <?php echo html_escape($class_details['name']); ?></span>
        </h1>
        <button type="button" class="btn-back" onclick="history.back();">
            <i class="fas fa-arrow-left"></i> <?php echo get_phrase('back'); ?>
        </button>
    </div>

    <!-- Content -->
    <?php if (!empty($courses)): ?>
        <div class="row">
            <?php 
            helper('lms');
            $lms_model = $this->lms_model ?? model('App\\Models\\addons\\Lms_model');
            $delay_count = 1;
            foreach ($courses as $course):
                // Logic to get owner names
                $owner_names = [];
                if (!empty($course['user_id'])) {
                    $name = $this->user_model->get_user_details($course['user_id'], 'name');
                    if (!empty($name)) {
                        $owner_names[] = $name;
                    }
                }
                $mentors = db()->table('course_teachers')
                    ->where('course_id', $course['id'])
                    ->get()
                    ->getResultArray();
                foreach ($mentors as $mentor) {
                    if (!empty($mentor['user_id'])) {
                        $name = $this->user_model->get_user_details($mentor['user_id'], 'name');
                        if (!empty($name)) {
                            $owner_names[] = $name;
                        }
                    }
                }

                // Logic for progress and lessons
                $progress_value = course_progress($course['id']);
                $lessons = is_object($lms_model) && method_exists($lms_model, 'get_lessons')
                    ? $lms_model->get_lessons('course', $course['id'])
                    : [];
                $lessons_rows = [];
                if (is_object($lessons)) {
                    if (method_exists($lessons, 'result_array')) {
                        $lessons_rows = $lessons;
                    } elseif (method_exists($lessons, 'getResultArray')) {
                        $lessons_rows = $lessons->getResultArray();
                    }
                } elseif (is_array($lessons)) {
                    $lessons_rows = $lessons;
                }
                $first_lesson = null;
                foreach ($lessons_rows as $lesson) {
                    $lesson_type = is_array($lesson) ? ($lesson['lesson_type'] ?? '') : ($lesson->lesson_type ?? '');
                    $lesson_id = is_array($lesson) ? ($lesson['id'] ?? null) : ($lesson->id ?? null);
                    if (strtolower((string) $lesson_type) != 'quiz' && $lesson_id !== null) {
                        $first_lesson = $lesson_id;
                        break;
                    }
                }
                if ($first_lesson === null && !empty($lessons_rows)) {
                    $first_row = $lessons_rows[0];
                    $first_lesson = is_array($first_row) ? ($first_row['id'] ?? null) : ($first_row->id ?? null);
                }

                // Thumbnail logic
                if (file_exists('uploads/course_thumbnail/' . $course['thumbnail'])) {
                    $course_thumbnail = base_url('uploads/course_thumbnail/' . $course['thumbnail']);
                } else {
                    $course_thumbnail = base_url('uploads/course_thumbnail/placeholder.png');
                }
                
                // Instructor image
                $instructor_image = $this->user_model->get_user_image($course['user_id']);
                
                // Animation delay class
                $delay_class = 'delay-' . ($delay_count % 5); 
                $delay_count++;
            ?>
                <div class="col-md-6 col-lg-4 col-xl-3 mb-4 fade-up <?php echo $delay_class; ?>">
                    <div class="modern-card">
                        <div class="course-img-wrapper">
                            <img src="<?php echo $course_thumbnail; ?>" alt="<?php echo html_escape($course['title']); ?>">
                        </div>
                        
                        <div class="modern-card-body">
                            <h4 class="course-title" title="<?php echo html_escape($course['title']); ?>">
                                <?php echo html_escape($course['title']); ?>
                            </h4>
                            
                            <div class="instructor-box">
                                <img class="instructor-avatar" src="<?php echo $instructor_image; ?>" alt="instructor">
                                <span class="instructor-name"><?php echo implode(', ', $owner_names); ?></span>
                            </div>

                            <div class="course-progress-wrapper">
                                <div class="course-progress-bar">
                                    <div class="course-progress-fill <?php echo ($progress_value >= 100) ? 'completed' : ''; ?>" 
                                         style="width: <?php echo $progress_value; ?>%;"></div>
                                </div>
                                <div class="course-progress-text">
                                    <span><?php echo get_phrase('progress'); ?></span>
                                    <span><?php echo ceil($progress_value); ?>%</span>
                                </div>
                            </div>

                            <div class="w-100">
                                <?php if (count($lessons_rows) > 0): ?>
                                    <a href="<?php echo site_url('addons/lessons/play/' . slugify($course['title']) . '/' . $course['id'] . '/' . $first_lesson); ?>" 
                                       class="action-btn <?php echo ($progress_value > 0) ? '' : 'primary'; ?>">
                                        <?php if($progress_value > 0): ?>
                                            <i class="fas fa-play"></i> <?php echo get_phrase('continue_lesson'); ?>
                                        <?php else: ?>
                                            <i class="fas fa-rocket"></i> <?php echo get_phrase('start_course'); ?>
                                        <?php endif; ?>
                                    </a>
                                <?php else: ?>
                                    <a href="javascript:void(0);" class="action-btn disabled">
                                        <i class="fas fa-tools"></i> <?php echo get_phrase('under_construction'); ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="fade-up">
            <?php include APPPATH . 'Views/backend/empty.php'; ?>
        </div>
    <?php endif; ?>
</div>
