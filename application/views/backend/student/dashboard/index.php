<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
/* ========== MODERN DASHBOARD STYLES ========== */
.modern-dashboard {
  /* MONOCHROMATIC THEME (INDIGO) */
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
  --shadow-xl: 0 20px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04);
  
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

.dash-header .date-badge {
  background: var(--bg-card);
  padding: 0.625rem 1rem;
  border-radius: 50px;
  font-size: 0.875rem;
  color: var(--text-muted);
  box-shadow: var(--shadow-sm);
  border: 1px solid var(--border-color);
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.dash-header .date-badge i {
  color: var(--primary);
}

/* Alert - Unified Color (Primary/Indigo) */
.modern-alert {
  background: linear-gradient(135deg, var(--primary-lighter), #c7d2fe);
  border: none;
  border-radius: 16px;
  padding: 1rem 1.5rem;
  margin-bottom: 1.5rem;
  display: flex;
  align-items: center;
  gap: 1rem;
  box-shadow: var(--shadow-md);
}

.modern-alert .alert-icon {
  width: 40px;
  height: 40px;
  background: var(--primary);
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  flex-shrink: 0;
}

.modern-alert .alert-content strong {
  color: var(--primary-dark);
  display: block;
  margin-bottom: 0.25rem;
}

.modern-alert .alert-content span {
  color: var(--text-dark);
  font-size: 0.875rem;
  opacity: 0.9;
}

.modern-alert a {
    color: var(--primary-dark);
    text-decoration: underline;
    font-weight: 600;
}

/* Stats Grid */
.stats-row {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1.25rem;
  margin-bottom: 1.5rem;
}

@media (max-width: 1200px) {
  .stats-row { grid-template-columns: repeat(2, 1fr); }
}

@media (max-width: 576px) {
  .stats-row { grid-template-columns: 1fr; }
}

.stat-card {
  background: var(--bg-card);
  border-radius: 20px;
  padding: 1.5rem;
  position: relative;
  overflow: hidden;
  box-shadow: var(--shadow-md);
  border: 1px solid var(--border-color);
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  cursor: pointer;
}

.stat-card:hover {
  transform: translateY(-4px);
  box-shadow: var(--shadow-xl);
}

.stat-card::before {
  content: '';
  position: absolute;
  top: 0;
  right: 0;
  width: 120px;
  height: 120px;
  border-radius: 50%;
  opacity: 0.1;
  transform: translate(30%, -30%);
  transition: all 0.3s ease;
  background: var(--primary);
}

.stat-card:hover::before {
  transform: translate(20%, -20%) scale(1.2);
  opacity: 0.15;
}

.stat-card .stat-icon {
  width: 52px;
  height: 52px;
  border-radius: 14px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.375rem;
  color: white;
  margin-bottom: 1.25rem;
  position: relative;
  /* Unified Gradient */
  background: linear-gradient(135deg, var(--primary), var(--primary-light));
  box-shadow: 0 4px 14px rgba(99, 102, 241, 0.35);
}

.stat-card .stat-label {
  font-size: 0.8125rem;
  font-weight: 500;
  color: var(--text-muted);
  text-transform: uppercase;
  letter-spacing: 0.5px;
  margin-bottom: 0.5rem;
}

.stat-card .stat-value {
  font-family: 'Outfit', sans-serif;
  font-size: 2.25rem;
  font-weight: 700;
  color: var(--text-dark);
  line-height: 1;
  margin-bottom: 0.5rem;
}

.stat-card .stat-desc {
  font-size: 0.8125rem;
  color: var(--text-muted);
}

.stat-card .stat-link {
  position: absolute;
  top: 1.25rem;
  right: 1.25rem;
  width: 32px;
  height: 32px;
  border-radius: 8px;
  background: var(--bg-main);
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--text-muted);
  text-decoration: none;
  transition: all 0.3s ease;
  opacity: 0;
}

.stat-card:hover .stat-link {
  opacity: 1;
}

.stat-card .stat-link:hover {
  background: var(--primary);
  color: white;
}

/* Modern Cards */
.modern-card {
  background: var(--bg-card);
  border-radius: 20px;
  box-shadow: var(--shadow-md);
  border: 1px solid var(--border-color);
  overflow: hidden;
  transition: all 0.3s ease;
}

.modern-card:hover {
  box-shadow: var(--shadow-lg);
}

.modern-card-header {
  padding: 1.25rem 1.5rem;
  border-bottom: 1px solid var(--border-color);
  display: flex;
  align-items: center;
  justify-content: space-between;
  background: linear-gradient(180deg, #fafbfc, transparent);
}

.modern-card-header h3 {
  font-family: 'Outfit', sans-serif;
  font-size: 1.0625rem;
  font-weight: 600;
  color: var(--text-dark);
  margin: 0;
  display: flex;
  align-items: center;
  gap: 0.625rem;
}

.modern-card-header h3 i {
  color: var(--primary);
  font-size: 1.125rem;
}

.modern-card-header .view-link {
  font-size: 0.8125rem;
  color: var(--primary);
  text-decoration: none;
  font-weight: 500;
  display: flex;
  align-items: center;
  gap: 0.375rem;
  transition: all 0.2s ease;
}

.modern-card-header .view-link:hover {
  color: var(--primary-light);
  gap: 0.5rem;
}

.modern-card-body {
  padding: 1.5rem;
}

/* Course Card Specifics */
.course-card {
    height: 100%;
    display: flex;
    flex-direction: column;
}

.course-card .course-img-wrapper {
    height: 140px;
    overflow: hidden;
    position: relative;
}

.course-card .course-img-wrapper img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
}

.course-card:hover .course-img-wrapper img {
    transform: scale(1.05);
}

.course-card .modern-card-body {
    padding: 1.25rem;
    flex-grow: 1;
    display: flex;
    flex-direction: column;
}

.course-card .course-title {
    font-family: 'Outfit', sans-serif;
    font-size: 1rem;
    font-weight: 700;
    color: var(--text-dark);
    margin-bottom: 0.75rem;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    height: 2.4rem;
    line-height: 1.2;
}

.course-card .instructor-box {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 1rem;
}

.course-card .instructor-avatar {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    object-fit: cover;
    border: 1px solid var(--primary-lighter);
}

.course-card .instructor-name {
    font-size: 0.75rem;
    color: var(--text-muted);
    font-weight: 500;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 150px;
}

.course-card .course-progress-wrapper {
    margin-top: auto;
    margin-bottom: 1rem;
}

.course-card .course-progress-bar {
    height: 6px;
    background: var(--bg-main);
    border-radius: 10px;
    overflow: hidden;
    margin-bottom: 0.35rem;
    border: 1px solid var(--border-color);
}

.course-card .course-progress-fill {
    height: 100%;
    background: var(--primary);
    border-radius: 10px;
    transition: width 1s ease;
}

.course-card .course-progress-fill.completed {
    background: var(--secondary);
}

.course-card .course-progress-text {
    display: flex;
    justify-content: space-between;
    font-size: 0.7rem;
    font-weight: 600;
    color: var(--text-muted);
}

.course-card .action-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    width: 100%;
    padding: 0.6rem;
    text-align: center;
    background: var(--bg-main);
    color: var(--primary);
    font-weight: 600;
    font-size: 0.85rem;
    border-radius: 10px;
    text-decoration: none;
    transition: all 0.3s;
    border: 1px solid var(--primary-lighter);
}

.course-card .action-btn:hover {
    background: var(--primary);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
    border-color: var(--primary);
}

.course-card .action-btn.primary {
    background: var(--primary);
    color: white;
    border: 1px solid var(--primary);
}

.course-card .action-btn.primary:hover {
    background: var(--primary-dark);
    border-color: var(--primary-dark);
}

.section-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1.25rem;
    margin-top: 2rem;
}

.section-header h3 {
    font-family: 'Outfit', sans-serif;
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--text-dark);
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.section-header .view-all {
    font-size: 0.875rem;
    color: var(--primary);
    font-weight: 600;
    text-decoration: none;
}

/* Events List */
.events-list {
  max-height: 280px;
  overflow-y: auto;
}

.events-list::-webkit-scrollbar {
  width: 4px;
}

.events-list::-webkit-scrollbar-track {
  background: var(--bg-main);
  border-radius: 2px;
}

.events-list::-webkit-scrollbar-thumb {
  background: var(--border-color);
  border-radius: 2px;
}

.event-item {
  display: flex;
  align-items: flex-start;
  gap: 1rem;
  padding: 1rem;
  border-radius: 12px;
  transition: all 0.2s ease;
  margin-bottom: 0.5rem;
}

.event-item:hover {
  background: var(--bg-main);
}

/* Unified Event Date Colors */
.event-item .event-date {
  width: 48px;
  height: 52px;
  background: linear-gradient(135deg, var(--primary-lighter), #c7d2fe);
  border-radius: 10px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.event-item .event-date .day {
  font-family: 'Outfit', sans-serif;
  font-size: 1.25rem;
  font-weight: 700;
  color: var(--primary-dark);
  line-height: 1;
}

.event-item .event-date .month {
  font-size: 0.625rem;
  font-weight: 600;
  color: var(--primary);
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.event-item .event-info h4 {
  font-size: 0.9375rem;
  font-weight: 600;
  color: var(--text-dark);
  margin: 0 0 0.375rem 0;
}

.event-item .event-info p {
  font-size: 0.8125rem;
  color: var(--text-muted);
  margin: 0;
}

.empty-msg {
  text-align: center;
  padding: 2.5rem 1rem;
  color: var(--text-muted);
}

.empty-msg i {
  font-size: 2.5rem;
  margin-bottom: 0.75rem;
  opacity: 0.4;
  display: block;
}

/* Animations */
.fade-up {
  animation: fadeUp 0.5s ease forwards;
  opacity: 0;
}

@keyframes fadeUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.delay-1 { animation-delay: 0.05s; }
.delay-2 { animation-delay: 0.1s; }
.delay-3 { animation-delay: 0.15s; }

/* Simple List Styles */
.course-list-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1rem 1.5rem;
    border-bottom: 1px solid var(--border-color);
    transition: background 0.2s ease;
    gap: 1rem;
}

.course-list-item:last-child {
    border-bottom: none;
}

.course-list-item:hover {
    background: #f8fafc;
}

.course-thumb-small {
    width: 60px;
    height: 60px;
    border-radius: 10px;
    object-fit: cover;
    flex-shrink: 0;
}

.course-info {
    flex-grow: 1;
    min-width: 0; /* Enable text truncation */
}

.course-title-small {
    font-family: 'Outfit', sans-serif;
    font-size: 1rem;
    font-weight: 600;
    color: var(--text-dark);
    margin: 0 0 0.25rem 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.course-meta {
    font-size: 0.8125rem;
    color: var(--text-muted);
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.course-meta i {
    color: var(--primary);
    opacity: 0.8;
}

.btn-mini {
    padding: 0.5rem 1rem;
    border-radius: 8px;
    font-size: 0.8125rem;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.2s;
    white-space: nowrap;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-mini.start {
    background: var(--primary);
    color: white;
}

.btn-mini.continue {
    background: var(--primary-lighter);
    color: var(--primary-dark);
}

.btn-mini:hover {
    transform: translateY(-1px);
    box-shadow: var(--shadow-sm);
}
</style>

<?php
$user_id = $this->session->userdata('user_id');
$student_data = $this->db->get_where('students', array('user_id' => $user_id));

// Fetch Courses Logic
$my_courses = array();
if ($student_data->num_rows() > 0) {
    $student_ids = array();
    foreach ($student_data->result_array() as $student) {
        $student_ids[] = $student['id'];
    }
    $student_ids = array_unique($student_ids); // Remove duplicates

    if (!empty($student_ids)) {
        $active_session_id = active_session();
        $this->db->select('class_id');
        $this->db->from('enrols');
        $this->db->where_in('student_id', $student_ids);
        $this->db->where('session', $active_session_id);
        $enrol_query = $this->db->get();
        
        if ($enrol_query->num_rows() > 0) {
            $class_ids = array();
            foreach ($enrol_query->result_array() as $enrol) {
                $class_ids[] = $enrol['class_id'];
            }
            $class_ids = array_unique($class_ids);
            
            if (!empty($class_ids)) {
                 $this->db->select('course.*');
                 $this->db->from('course');
                 $this->db->join('course_classes', 'course.id = course_classes.course_id');
                 $this->db->where_in('course_classes.class_id', $class_ids);
                 $this->db->where('course.status', 'active');
                 $this->db->group_by('course.id');
                 $my_courses = $this->db->get()->result_array();
            }
        }
    }
}
?>

<div class="modern-dashboard">
    
    <?php if ($student_data->num_rows() == 0): ?>
    <div class="modern-alert fade-up">
      <div class="alert-icon">
        <i class="fas fa-exclamation-triangle"></i>
      </div>
      <div class="alert-content">
        <strong><?php echo get_phrase('no_course_or_school'); ?></strong>
        <span>
            <a target="_blank" href="<?php echo site_url('home/communities'); ?>">
                <?php echo get_phrase('click_here'); ?>
            </a>
        </span>
      </div>
    </div>
    <?php endif; ?>

    <!-- Header -->
    <div class="dash-header fade-up">
      <h1>
        <span class="icon-box"><i class="fas fa-home"></i></span>
        <?php echo get_phrase('dashboard'); ?>
      </h1>
      <div class="date-badge">
        <i class="far fa-calendar-alt"></i>
         <?php echo get_phrase(strtolower(date('l'))) . ', ' . date('j') . ' ' . get_phrase(strtolower(date('F'))) . ' ' . date('Y'); ?>
      </div>
    </div>

    <div class="row">
        <!-- Stats -->
        <div class="col-xl-8">
            <div class="stats-row">
                <!-- Schools -->
                <div class="stat-card schools fade-up delay-1">
                    <a href="" class="stat-link" id="student_list" style="display: none;">
                        <i class="fas fa-arrow-right"></i>
                    </a>
                    <div class="stat-icon">
                        <i class="fas fa-people-arrows"></i>
                    </div>
                    <div class="stat-label"><?php echo get_phrase('schools'); ?></div>
                    <div class="stat-value">
                        <?php echo $student_data->num_rows(); ?>
                    </div>
                    <div class="stat-desc"><?php echo get_phrase('total_number_of_school'); ?></div>
                </div>

                <!-- Classes -->
                <div class="stat-card classes fade-up delay-2">
                    <a href="" class="stat-link" id="teacher_list" style="display: none;">
                        <i class="fas fa-arrow-right"></i>
                    </a>
                    <div class="stat-icon">
                        <i class="fas fa-chalkboard"></i>
                    </div>
                    <div class="stat-label"><?php echo get_phrase('classes'); ?></div>
                    <div class="stat-value">
                        <?php
                        if ($student_data->num_rows() > 0) {
                          // Re-fetch or reset pointer if needed, but we can reuse ids if we had them.
                          // Simplest is to just re-count enrols logic as before for classes
                          // Using logic from original file slightly adapted
                          $student_list =  $student_data->result_array(); // Pointer might be at end?
                          $student_data->data_seek(0); // Reset pointer
                          $student_list = $student_data->result_array();
                          
                          $student_ids = array();
                          foreach ($student_list as $student) {
                            if (!in_array($student['id'], $student_ids)) {
                              array_push($student_ids, $student['id']);
                            }
                          }

                          if(!empty($student_ids)){
                             $this->db->where_in('student_id', $student_ids);
                             $student_cours = $this->db->get('enrols')->num_rows();
                             echo $student_cours;
                          } else {
                             echo 0;
                          }
                        } else {
                          echo $student_data->num_rows();
                        }
                        ?>
                    </div>
                    <div class="stat-desc"><?php echo get_phrase('total_number_of_class'); ?></div>
                </div>

                <!-- Online Courses -->
                <div class="stat-card courses fade-up delay-3">
                    <a href="<?php echo site_url('addons/courses'); ?>" class="stat-link">
                        <i class="fas fa-arrow-right"></i>
                    </a>
                    <div class="stat-icon">
                        <i class="fas fa-laptop-code"></i>
                    </div>
                    <div class="stat-label"><?php echo get_phrase('online_courses'); ?></div>
                    <div class="stat-value">
                        <?php echo count($my_courses); ?>
                    </div>
                    <div class="stat-desc"><?php echo get_phrase('active_courses'); ?></div>
                </div>
            </div>
            
            <!-- My Courses Section (Simple List) -->
            <?php if (!empty($my_courses)): ?>
            <div class="modern-card fade-up delay-3" style="margin-top: 1.5rem;">
                <div class="modern-card-header">
                    <h3><i class="fas fa-layer-group"></i> <?php echo get_phrase('my_courses'); ?></h3>
                    <a href="<?php echo site_url('addons/courses'); ?>" class="view-link">
                        <?php echo get_phrase('view_all'); ?> <i class="fas fa-chevron-right"></i>
                    </a>
                </div>
                
                <div class="modern-card-body p-0">
                    <?php foreach (array_slice($my_courses, 0, 5) as $course): ?>
                        <?php
                        // Logic to get owner names
                        $owner_names = [];
                        if (!empty($course['user_id'])) {
                            $name = $this->user_model->get_user_details($course['user_id'], 'name');
                            if (!empty($name)) {
                                $owner_names[] = $name;
                            }
                        }
                        
                        // Progress
                        $progress_value = course_progress($course['id']);
                        
                        // First Lesson
                        $lessons = $this->lms_model->get_lessons('course', $course['id']);
                        $first_lesson = null;
                        foreach ($lessons->result_array() as $lesson) {
                            if (strtolower($lesson['lesson_type']) != 'quiz') {
                                $first_lesson = $lesson['id'];
                                break;
                            }
                        }
                        if ($first_lesson === null && $lessons->num_rows() > 0) {
                            $lessons->data_seek(0);
                            $first_lesson = $lessons->row('id');
                        }
                        
                        // Thumbnail
                        if (file_exists('uploads/course_thumbnail/' . $course['thumbnail'])) {
                            $course_thumbnail = base_url('uploads/course_thumbnail/' . $course['thumbnail']);
                        } else {
                            $course_thumbnail = base_url('uploads/course_thumbnail/placeholder.png');
                        }
                        ?>
                        <div class="course-list-item">
                            <img src="<?php echo $course_thumbnail; ?>" alt="" class="course-thumb-small">
                            
                            <div class="course-info">
                                <h4 class="course-title-small" title="<?php echo html_escape($course['title']); ?>">
                                    <?php echo html_escape($course['title']); ?>
                                </h4>
                                <div class="course-meta">
                                    <span><i class="fas fa-user-tie"></i> <?php echo implode(', ', $owner_names); ?></span>
                                    
                                    <span class="<?php echo $progress_value >= 100 ? 'text-success' : 'text-primary'; ?>" style="font-weight: 600;">
                                        <i class="fas fa-chart-pie"></i> <?php echo ceil($progress_value); ?>%
                                    </span>
                                </div>
                            </div>
                            
                            <div class="course-action">
                                <?php if ($lessons->num_rows() > 0): ?>
                                    <a href="<?php echo site_url('addons/lessons/play/' . slugify($course['title']) . '/' . $course['id'] . '/' . $first_lesson); ?>" 
                                       class="btn-mini <?php echo ($progress_value > 0) ? 'continue' : 'start'; ?>">
                                        <?php if($progress_value > 0): ?>
                                            <i class="fas fa-play"></i> <?php echo get_phrase('continue'); ?>
                                        <?php else: ?>
                                            <i class="fas fa-rocket"></i> <?php echo get_phrase('start'); ?>
                                        <?php endif; ?>
                                    </a>
                                <?php else: ?>
                                    <span class="btn-mini" style="background: #f1f5f9; color: #94a3b8;">
                                        <i class="fas fa-tools"></i> <?php echo get_phrase('soon'); ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

        </div>

        <!-- Recent Events -->
        <div class="col-xl-4">
            <div class="modern-card fade-up delay-3">
              <div class="modern-card-header">
                <h3><i class="far fa-calendar-alt"></i> <?php echo get_phrase('recent_events'); ?></h3>
                <a href="<?php echo route('event_calendar'); ?>" class="view-link">
                  <?php echo get_phrase('view_all'); ?> <i class="fas fa-chevron-right"></i>
                </a>
              </div>
              <div class="modern-card-body">
                <div class="events-list">
                  <?php
                  $date_from = strtotime(date('m/01/Y')." 00:00:00");
                  $date_to = strtotime(date('m/t/Y')." 23:59:59");
                  $events = $this->crud_model->get_current_month_events()->result_array();
                  $has_events = false;
                  foreach ($events as $event):
                    if (strtotime($event['starting_date']) >= $date_from && strtotime($event['starting_date']) <= $date_to):
                      $has_events = true;
                  ?>
                    <div class="event-item">
                      <div class="event-date">
                        <span class="day"><?php echo date('d', strtotime($event['starting_date'])); ?></span>
                        <span class="month"><?php echo date('M', strtotime($event['starting_date'])); ?></span>
                      </div>
                      <div class="event-info">
                        <h4><?php echo $event['title']; ?></h4>
                        <p><i class="far fa-clock"></i> <?php echo date('d M', strtotime($event['starting_date'])); ?> - <?php echo date('d M Y', strtotime($event['ending_date'])); ?></p>
                      </div>
                    </div>
                  <?php 
                    endif;
                  endforeach;
                  
                  if (!$has_events): ?>
                    <div class="empty-msg">
                      <i class="far fa-calendar-times"></i>
                      <p><?php echo get_phrase('no_events_this_month'); ?></p>
                    </div>
                  <?php endif; ?>
                </div>
              </div>
            </div>
        </div>
    </div>
</div>

<script>
  $(document).ready(function() {
    initDataTable("expense-datatable");
  });
</script>