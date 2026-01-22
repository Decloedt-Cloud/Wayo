<link rel="stylesheet" href="<?php echo base_url(); ?>assets/backend/css/main-responsive.css">
<?php include APPPATH . 'views/backend/shared/trial_expired_modal.php'; ?>

<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
/* ========== MODERN DASHBOARD STYLES ========== */
.modern-dashboard {
  --primary: #6366f1;
  --primary-light: #818cf8;
  --primary-lighter: #e0e7ff;
  --secondary: #6366f1; /* Changed to primary for monochrome */
  --accent: #6366f1;    /* Changed to primary for monochrome */
  --danger: #6366f1;    /* Changed to primary for monochrome */
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

/* Alert */
.modern-alert {
  background: linear-gradient(135deg, var(--primary-lighter), #eef2ff);
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
  color: var(--text-muted);
  font-size: 0.875rem;
}

/* Stats Grid */
.stats-row {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1.25rem;
  margin-bottom: 1.5rem;
}

@media (max-width: 992px) {
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
}

.stat-card:hover::before {
  transform: translate(20%, -20%) scale(1.2);
  opacity: 0.15;
}

.stat-card.students::before { background: var(--primary); }
.stat-card.teachers::before { background: var(--primary); }
.stat-card.staff::before { background: var(--primary); }

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
}

.stat-card.students .stat-icon {
  background: linear-gradient(135deg, var(--primary), var(--primary-light));
  box-shadow: 0 4px 14px rgba(99, 102, 241, 0.35);
}

.stat-card.teachers .stat-icon {
  background: linear-gradient(135deg, var(--primary), var(--primary-light));
  box-shadow: 0 4px 14px rgba(99, 102, 241, 0.35);
}

.stat-card.staff .stat-icon {
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

/* Content Grid */
.content-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1.25rem;
  margin-bottom: 1.5rem;
}

@media (max-width: 992px) {
  .content-row { grid-template-columns: 1fr; }
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

/* Attendance Highlight */
.attendance-banner {
  background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 50%, #a78bfa 100%);
  border-radius: 16px;
  padding: 2rem;
  text-align: center;
  position: relative;
  overflow: hidden;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  min-height: 250px;
}

.attendance-banner::before {
  content: '';
  position: absolute;
  top: -50%;
  left: -50%;
  width: 200%;
  height: 200%;
  background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
  animation: slide 20s linear infinite;
}

@keyframes slide {
  0% { transform: translateX(0) translateY(0); }
  100% { transform: translateX(50%) translateY(50%); }
}

.attendance-banner .big-num {
  font-family: 'Outfit', sans-serif;
  font-size: 4rem;
  font-weight: 700;
  color: white;
  line-height: 1;
  margin-bottom: 0.5rem;
  position: relative;
  z-index: 1;
  text-shadow: 0 4px 20px rgba(0,0,0,0.2);
}

.attendance-banner p {
  color: rgba(255,255,255,0.9);
  font-size: 0.9375rem;
  margin-bottom: 1.25rem;
  position: relative;
  z-index: 1;
}

.attendance-banner .btn-banner {
  background: rgba(255,255,255,0.2);
  border: 1px solid rgba(255,255,255,0.3);
  color: white;
  padding: 0.625rem 1.25rem;
  border-radius: 10px;
  font-weight: 500;
  font-size: 0.875rem;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  transition: all 0.3s ease;
  position: relative;
  z-index: 1;
  backdrop-filter: blur(4px);
}

.attendance-banner .btn-banner:hover {
  background: rgba(255,255,255,0.3);
  transform: translateY(-2px);
  color: white;
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

.event-item .event-date {
  width: 48px;
  height: 52px;
  background: linear-gradient(135deg, var(--primary-lighter), #eef2ff);
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
  color: var(--primary);
  line-height: 1;
}

.event-item .event-date .month {
  font-size: 0.625rem;
  font-weight: 600;
  color: var(--primary-light);
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
</style>

<div class="modern-dashboard">
    <!-- Header -->
    <div class="dash-header">
        <h1>
            <div class="icon-box">
                <i class="fas fa-home"></i>
            </div>
            <?php echo get_phrase('dashboard'); ?>
        </h1>
        <div class="date-badge">
            <i class="far fa-calendar-alt"></i>
            <span><?php echo date('l, j F Y'); ?></span>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="stats-row">
        <!-- Students -->
        <div class="stat-card students fade-up delay-1" onclick="location.href='<?php echo route('student'); ?>'">
            <div class="stat-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-label"><?php echo get_phrase('students'); ?></div>
            <div class="stat-value">
                <?php
                    $current_session_students = $this->user_model->get_session_wise_student();
                    echo $current_session_students->num_rows();
                ?>
            </div>
            <div class="stat-desc"><?php echo get_phrase('total_number_of_student'); ?></div>
            <a href="<?php echo route('student'); ?>" class="stat-link">
                <i class="mdi mdi-arrow-right"></i>
            </a>
        </div>

        <!-- Teachers -->
        <div class="stat-card teachers fade-up delay-2" onclick="location.href='<?php echo route('teacher'); ?>'">
            <div class="stat-icon">
                <i class="fas fa-chalkboard-teacher"></i>
            </div>
            <div class="stat-label"><?php echo get_phrase('teacher'); ?></div>
            <div class="stat-value">
                <?php
                    $teachers = $this->user_model->get_teachers();
                    echo $teachers->num_rows();
                ?>
            </div>
            <div class="stat-desc"><?php echo get_phrase('total_number_of_teacher'); ?></div>
            <a href="<?php echo route('teacher'); ?>" class="stat-link">
                <i class="mdi mdi-arrow-right"></i>
            </a>
        </div>

        <!-- Staff -->
        <div class="stat-card staff fade-up delay-3">
            <div class="stat-icon">
                <i class="fas fa-user-tie"></i>
            </div>
            <div class="stat-label"><?php echo get_phrase('staff'); ?></div>
            <div class="stat-value">
                <?php
                    $accountants = $this->user_model->get_accountants()->num_rows();
                    $librarians = $this->user_model->get_librarians()->num_rows();
                    echo $accountants + $librarians;
                ?>
            </div>
            <div class="stat-desc"><?php echo get_phrase('total_number_of_staff'); ?></div>
        </div>
    </div>

    <!-- Content Row -->
    <div class="content-row">
        <!-- Attendance Banner -->
        <div class="attendance-banner fade-up delay-2">
            <h2 class="big-num"><?php echo $this->crud_model->get_todays_attendance(); ?></h2>
            <p><?php echo get_phrase('students_are_attending_today'); ?></p>
            <a href="<?php echo route('attendance'); ?>" class="btn-banner">
                <?php echo get_phrase('go_to_attendance'); ?>
                <i class="mdi mdi-arrow-right"></i>
            </a>
        </div>

        <!-- Events -->
        <div class="modern-card fade-up delay-3">
            <div class="modern-card-header">
                <h3>
                    <i class="far fa-calendar-alt"></i>
                    <?php echo get_phrase('recent_events'); ?>
                </h3>
                <a href="<?php echo route('event_calendar'); ?>" class="view-link">
                    <?php echo get_phrase('view_all'); ?> <i class="mdi mdi-arrow-right"></i>
                </a>
            </div>
            <div class="modern-card-body">
                <div class="events-list">
                    <?php 
                    $date_from = strtotime(date('m/01/Y')." 00:00:00");
                    $date_to   = strtotime(date('m/t/Y')." 23:59:59");
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
                            <p><?php echo date('D, d M', strtotime($event['starting_date'])); ?> - <?php echo date('D, d M', strtotime($event['ending_date'])); ?></p>
                        </div>
                    </div>
                    <?php 
                        endif; 
                    endforeach; 
                    
                    if (!$has_events):
                    ?>
                    <div class="empty-msg">
                        <i class="mdi mdi-calendar-blank"></i>
                        <p><?php echo get_phrase('no_events_this_month'); ?></p>
                    </div>
                    <?php endif; ?>
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