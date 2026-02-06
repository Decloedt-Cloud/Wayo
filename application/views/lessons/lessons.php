<link rel="stylesheet" href="<?php echo base_url(); ?>assets/backend/css/custom/navbar.css">
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/backend/css/curriculum.min.css">

<style>
/* ========== MODERN LESSONS LAYOUT ========== */

/* Navbar Modern */
.lessons-navbar {
    background: linear-gradient(135deg, var(--primary), var(--primary-light)) !important;
    box-shadow: var(--shadow-lg);
    padding: 0.875rem 1.5rem;
    position: sticky;
    top: 0;
    z-index: 1000;
}

.lessons-navbar .container-fluid {
    max-width: 100%;
    padding: 0;
}

.navbar-brand-section {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.navbar-logo {
    height: 40px;
    filter: brightness(0) invert(1);
    opacity: 0.95;
    transition: opacity 0.2s;
}

.navbar-logo:hover {
    opacity: 1;
}

.navbar-divider {
    width: 1px;
    height: 28px;
    background: rgba(255, 255, 255, 0.3);
    margin: 0 0.5rem;
}

.navbar-course-info {
    display: flex;
    flex-direction: column;
}

.navbar-course-label {
    font-size: 0.7rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: rgba(255, 255, 255, 0.7);
    line-height: 1;
}

.navbar-course-title {
    font-family: var(--font-header);
    font-size: 1rem;
    font-weight: 600;
    color: white;
    max-width: 400px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    line-height: 1.3;
}

.navbar-actions {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.nav-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.625rem 1.25rem;
    border-radius: var(--radius-md);
    font-size: 0.875rem;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.2s ease;
    cursor: pointer;
    border: none;
}

.nav-btn-ghost {
    background: rgba(255, 255, 255, 0.15);
    color: white;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.nav-btn-ghost:hover {
    background: rgba(255, 255, 255, 0.25);
    color: white;
    transform: translateY(-1px);
}

.nav-btn-icon {
    width: 40px;
    height: 40px;
    padding: 0;
    justify-content: center;
}

/* Main Layout Container */
.lessons-main {
    display: flex;
    min-height: calc(100vh - 70px);
    background: var(--bg-main);
}

.lessons-content {
    flex: 1;
    padding: 1.5rem;
    min-width: 0;
    overflow-y: auto;
    animation: fadeUp 0.4s ease;
}

.lessons-sidebar {
    width: 380px;
    min-width: 320px;
    max-width: 420px;
    background: var(--bg-card);
    border-left: 1px solid var(--border-color);
    box-shadow: -4px 0 20px rgba(0, 0, 0, 0.03);
    animation: slideIn 0.3s ease;
    display: flex;
    flex-direction: column;
}

/* Modern Card Base */
.modern-lesson-card {
    background: var(--bg-card);
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-md);
    border: 1px solid var(--border-color);
    overflow: hidden;
    transition: all 0.3s ease;
}

.modern-lesson-card:hover {
    box-shadow: var(--shadow-lg);
}

/* Preview Card Styles */
.preview-card {
    display: flex;
    flex-direction: column;
    height: 100%;
}

.preview-header {
    padding: 1rem 1.5rem;
    background: linear-gradient(180deg, rgba(99, 102, 241, 0.05), transparent);
    border-bottom: 1px solid var(--border-color);
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.preview-type {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.375rem 0.875rem;
    background: var(--primary-lighter);
    color: var(--primary-dark);
    border-radius: 50px;
    font-size: 0.8125rem;
    font-weight: 600;
}

.preview-type i {
    font-size: 0.9rem;
}

.preview-title-section {
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid var(--border-color);
}

.preview-title-section h2 {
    font-family: var(--font-header);
    font-size: 1.375rem;
    font-weight: 700;
    color: var(--text-dark);
    margin: 0;
    line-height: 1.4;
}

.preview-content {
    padding: 1.5rem;
    flex: 1;
    overflow-y: auto;
}

.preview-content p {
    color: var(--text-muted);
    line-height: 1.7;
}

/* Video Container */
.video-container {
    position: relative;
    width: 100%;
    background: #000;
    border-radius: var(--radius-md);
    overflow: hidden;
}

.video-container iframe,
.video-container video {
    width: 100%;
    aspect-ratio: 16/9;
    display: block;
}

/* Plyr Override */
.plyr {
    border-radius: var(--radius-md);
    overflow: hidden;
}

.plyr__video-embed {
    border-radius: var(--radius-md);
    overflow: hidden;
}

/* Responsive Layout */
@media (max-width: 1200px) {
    .lessons-sidebar {
        width: 340px;
        min-width: 280px;
    }
    
    .navbar-course-title {
        max-width: 280px;
    }
}

@media (max-width: 992px) {
    .lessons-main {
        flex-direction: column;
    }
    
    .lessons-sidebar {
        width: 100%;
        max-width: 100%;
        min-width: 100%;
        border-left: none;
        border-top: 1px solid var(--border-color);
        max-height: none;
    }
    
    .lessons-content {
        padding: 1rem;
    }
    
    .navbar-course-info {
        display: none;
    }
    
    .navbar-divider {
        display: none;
    }
}

@media (max-width: 576px) {
    .lessons-navbar {
        padding: 0.75rem 1rem;
    }
    
    .navbar-logo {
        height: 32px;
    }
    
    .nav-btn span {
        display: none;
    }
    
    .nav-btn {
        padding: 0.5rem 0.75rem;
    }
    
    .nav-btn-icon {
        width: 36px;
        height: 36px;
    }
    
    .lessons-content {
        padding: 0.75rem;
    }
    
    .preview-header,
    .preview-title-section,
    .preview-content {
        padding: 1rem;
    }
    
    .preview-title-section h2 {
        font-size: 1.125rem;
    }
}

/* Toggle Sidebar Button Mobile */
.toggle-sidebar-btn {
    display: none;
    position: fixed;
    bottom: 1.5rem;
    right: 1.5rem;
    width: 56px;
    height: 56px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--primary), var(--primary-light));
    color: white;
    border: none;
    box-shadow: var(--shadow-lg);
    z-index: 999;
    cursor: pointer;
    transition: all 0.3s ease;
}

.toggle-sidebar-btn:hover {
    transform: scale(1.05);
    box-shadow: var(--shadow-xl);
}

.toggle-sidebar-btn i {
    font-size: 1.25rem;
}

@media (max-width: 992px) {
    .lessons-sidebar.hidden-mobile {
        display: none;
    }
    
    .toggle-sidebar-btn {
        display: flex;
        align-items: center;
        justify-content: center;
    }
}

/* Expanded View */
.lessons-main.expanded .lessons-sidebar {
    display: none;
}

.lessons-main.expanded .lessons-content {
    max-width: 100%;
}
</style>

<?php
$course_details = $this->lms_model->get_course_by_id($course_id);
?>

<!-- Modern Navbar -->
<nav class="lessons-navbar">
    <div class="container-fluid">
        <div class="row align-items-center w-100 g-0">
            <div class="col">
                <div class="navbar-brand-section">
                    <a href="<?php echo site_url('addons/courses'); ?>">
                        <img src="<?php echo $this->settings_model->get_logo_light(); ?>" alt="Logo" class="navbar-logo">
                    </a>
                    <div class="navbar-divider"></div>
                    <div class="navbar-course-info">
                        <span class="navbar-course-label"><?php echo get_phrase('online_course'); ?></span>
                        <span class="navbar-course-title" title="<?php echo htmlspecialchars($course_details['title']); ?>">
                            <?php echo $course_details['title']; ?>
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-auto">
                <div class="navbar-actions">
                    <button type="button" class="nav-btn nav-btn-ghost nav-btn-icon d-none d-md-flex" onclick="toggleLessonView()" title="<?php echo get_phrase('toggle_sidebar'); ?>">
                        <i class="mdi mdi-arrow-expand-horizontal"></i>
                    </button>
                    <a href="<?php echo site_url('addons/courses'); ?>" class="nav-btn nav-btn-ghost">
                        <i class="mdi mdi-arrow-left"></i>
                        <span><?php echo get_phrase('back_to_courses'); ?></span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</nav>

<!-- Main Content -->
<div class="lessons-main" id="lessons-main">
    <?php if (isset($lesson_id)): ?>
    <!-- Lesson Content Area -->
    <div class="lessons-content">
        <?php include 'course_content_body.php'; ?>
    </div>
    <?php endif; ?>
    
    <!-- Course Sidebar -->
    <div class="lessons-sidebar" id="lessons-sidebar">
        <?php include 'course_content_sidebar.php'; ?>
    </div>
</div>

<!-- Mobile Toggle Button -->
<button type="button" class="toggle-sidebar-btn" onclick="toggleMobileSidebar()" id="toggle-sidebar-btn">
    <i class="mdi mdi-format-list-bulleted"></i>
</button>

<script>
function toggleLessonView() {
    document.getElementById('lessons-main').classList.toggle('expanded');
}

function toggleMobileSidebar() {
    const sidebar = document.getElementById('lessons-sidebar');
    const btn = document.getElementById('toggle-sidebar-btn');
    sidebar.classList.toggle('hidden-mobile');
    
    if (sidebar.classList.contains('hidden-mobile')) {
        btn.innerHTML = '<i class="mdi mdi-format-list-bulleted"></i>';
    } else {
        btn.innerHTML = '<i class="mdi mdi-close"></i>';
    }
}
</script>
