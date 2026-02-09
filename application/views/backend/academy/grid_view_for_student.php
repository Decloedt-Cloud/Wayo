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
        display: flex;
        flex-direction: column;
        flex: 1;
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
        margin-top: auto;
        padding-top: 1rem;
    }
    
    /* Ensure course-card uses full height */
    .course-card {
        display: flex;
        flex-direction: column;
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
    
    .btn-course.success {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
        border: none;
    }
    
    .btn-course.success:hover {
        background: linear-gradient(135deg, #059669, #047857);
        transform: translateY(-1px);
    }
    
    .btn-course.disabled {
        background: #e2e8f0;
        color: #94a3b8;
        border: none;
        cursor: not-allowed;
        opacity: 0.7;
    }

    /* ========== COURSE BADGES ========== */
    .course-badges {
        position: absolute;
        top: 12px;
        left: 12px;
        display: flex;
        flex-direction: column;
        gap: 6px;
        z-index: 10;
    }
    
    .course-badges span {
        padding: 5px 10px;
        border-radius: 8px;
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        backdrop-filter: blur(8px);
    }
    
    .badge-enrolled {
        background: rgba(16, 185, 129, 0.9);
        color: white;
    }
    
    .badge-free {
        background: rgba(99, 102, 241, 0.9);
        color: white;
    }
    
    .badge-vat {
        background: rgba(245, 158, 11, 0.9);
        color: white;
    }
    
    .badge-full {
        background: rgba(239, 68, 68, 0.9);
        color: white;
    }
    
    /* Enrollment Counter */
    .enrollment-counter {
        position: absolute;
        bottom: 12px;
        right: 12px;
        background: rgba(0, 0, 0, 0.7);
        color: white;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 5px;
        backdrop-filter: blur(4px);
    }
    
    /* ========== PRICE SECTION ========== */
    .price-section {
        margin: 1rem 0;
        padding: 0.875rem;
        background: var(--bg-main);
        border-radius: 12px;
        border: 1px solid var(--border-color);
    }
    
    /* Price Breakdown Mini (VAT) */
    .price-breakdown-mini {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }
    
    .price-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.8125rem;
    }
    
    .price-label {
        color: var(--text-muted);
        font-weight: 500;
    }
    
    .price-value {
        color: var(--text-dark);
        font-weight: 600;
    }
    
    .price-row.vat-row {
        padding: 4px 0;
        border-bottom: 1px dashed var(--border-color);
    }
    
    .price-row.vat-row .price-label {
        color: #f59e0b;
        font-size: 0.75rem;
    }
    
    .price-row.vat-row .price-value {
        color: #f59e0b;
        font-size: 0.8125rem;
    }
    
    .price-row.total-row {
        padding-top: 6px;
    }
    
    .price-row.total-row .price-label {
        color: var(--text-dark);
        font-weight: 700;
    }
    
    .price-total {
        font-size: 1rem !important;
        font-weight: 800 !important;
        color: var(--primary) !important;
    }
    
    /* Simple Price (No VAT) */
    .price-simple {
        display: flex;
        align-items: baseline;
        gap: 4px;
        justify-content: center;
    }
    
    .price-amount {
        font-family: 'Outfit', sans-serif;
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--primary);
    }
    
    .price-currency {
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--text-muted);
    }
    
    /* Free Price */
    .price-free {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        color: #10b981;
        font-weight: 700;
        font-size: 1rem;
    }
    
    .price-free i {
        font-size: 1.25rem;
    }

    /* ========== STATUS CARDS (Consistent Height) ========== */
    
    /* Enrolled Status Card */
    .enrolled-status-card {
        display: flex;
        align-items: center;
        gap: 12px;
        min-height: 70px;
    }
    
    .enrolled-icon {
        width: 44px;
        height: 44px;
        background: linear-gradient(135deg, #10b981, #059669);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.25rem;
        flex-shrink: 0;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
    }
    
    .enrolled-info {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }
    
    .enrolled-label {
        font-size: 0.75rem;
        color: var(--text-muted);
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .enrolled-value {
        font-size: 0.9375rem;
        font-weight: 700;
        color: #059669;
    }
    
    /* Free Status Card */
    .free-status-card {
        display: flex;
        align-items: center;
        gap: 12px;
        min-height: 70px;
    }
    
    .free-icon {
        width: 44px;
        height: 44px;
        background: linear-gradient(135deg, var(--primary), var(--primary-light));
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.25rem;
        flex-shrink: 0;
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
    }
    
    .free-info {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }
    
    .free-label {
        font-size: 0.75rem;
        color: var(--text-muted);
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .free-value {
        font-size: 1.125rem;
        font-weight: 700;
        color: var(--primary);
    }
    
    /* Simple Price Card */
    .simple-price-card {
        display: flex;
        align-items: center;
        gap: 12px;
        min-height: 70px;
    }
    
    .price-icon {
        width: 44px;
        height: 44px;
        background: linear-gradient(135deg, #f59e0b, #d97706);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.125rem;
        flex-shrink: 0;
        box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
    }
    
    .price-info {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }
    
    .price-info .price-label {
        font-size: 0.75rem;
        color: var(--text-muted);
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .price-value-large {
        font-family: 'Outfit', sans-serif;
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--primary);
    }
    
    /* Ensure VAT breakdown has same min-height */
    .price-breakdown-mini {
        min-height: 70px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        gap: 6px;
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

    /* ========== RESPONSIVE STYLES ========== */
    
    /* Tablet (768px - 1024px) */
    @media (max-width: 1024px) {
        .modern-dashboard {
            padding: 1rem;
        }
        
        .dash-header h1 {
            font-size: 1.5rem;
        }
        
        .dash-header h1 .icon-box {
            width: 40px;
            height: 40px;
            font-size: 1rem;
        }
        
        .course-card .course-img-wrapper {
            height: 180px;
        }
        
        .course-card .course-content {
            padding: 1.25rem;
        }
        
        .course-card .course-title {
            font-size: 1rem;
        }
        
        .price-section {
            padding: 0.75rem;
        }
        
        /* Status cards responsive */
        .enrolled-icon,
        .free-icon,
        .price-icon {
            width: 40px;
            height: 40px;
            font-size: 1rem;
        }
        
        .enrolled-status-card,
        .free-status-card,
        .simple-price-card {
            gap: 10px;
            min-height: 60px;
        }
        
        .price-breakdown-mini {
            min-height: 60px;
        }
        
        .enrolled-value,
        .free-value {
            font-size: 0.875rem;
        }
        
        .price-value-large {
            font-size: 1.125rem;
        }
    }
    
    /* Mobile Large (576px - 768px) */
    @media (max-width: 768px) {
        .modern-dashboard {
            padding: 0.75rem;
            margin: -10px -10px 0 -10px;
        }
        
        .dash-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
        }
        
        .dash-header h1 {
            font-size: 1.375rem;
        }
        
        .date-badge {
            font-size: 0.8rem;
        }
        
        .modern-filter-form {
            gap: 0.75rem;
        }
        
        .modern-select {
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
        }
        
        .modern-btn {
            padding: 0.625rem 1rem;
            font-size: 0.875rem;
        }
        
        /* Course cards - 2 columns on tablet */
        .course-card .course-img-wrapper {
            height: 160px;
        }
        
        .course-card .course-content {
            padding: 1rem;
        }
        
        .course-card .course-title {
            font-size: 0.9375rem;
            margin-bottom: 0.75rem;
            height: 2.8em;
        }
        
        /* Badges smaller */
        .course-badges span {
            padding: 4px 8px;
            font-size: 0.7rem;
        }
        
        .enrollment-counter {
            padding: 3px 8px;
            font-size: 0.7rem;
        }
        
        /* Price section responsive */
        .price-section {
            margin: 0.75rem 0;
            padding: 0.625rem;
        }
        
        .price-row {
            font-size: 0.75rem;
        }
        
        .price-row.vat-row .price-label,
        .price-row.vat-row .price-value {
            font-size: 0.7rem;
        }
        
        .price-total {
            font-size: 0.9rem !important;
        }
        
        /* Status cards smaller */
        .enrolled-icon,
        .free-icon,
        .price-icon {
            width: 36px;
            height: 36px;
            font-size: 0.9rem;
            border-radius: 10px;
        }
        
        .enrolled-status-card,
        .free-status-card,
        .simple-price-card {
            gap: 8px;
            min-height: 50px;
        }
        
        .price-breakdown-mini {
            min-height: 50px;
            gap: 4px;
        }
        
        .enrolled-label,
        .free-label,
        .price-info .price-label {
            font-size: 0.65rem;
        }
        
        .enrolled-value {
            font-size: 0.8rem;
        }
        
        .free-value {
            font-size: 0.9rem;
        }
        
        .price-value-large {
            font-size: 1rem;
        }
        
        /* Button smaller */
        .btn-course {
            padding: 0.5rem;
            font-size: 0.8rem;
        }
        
        .course-actions {
            padding-top: 0.75rem;
        }
    }
    
    /* Mobile Small (< 576px) */
    @media (max-width: 576px) {
        .modern-dashboard {
            padding: 0.5rem;
            margin: -5px -5px 0 -5px;
        }
        
        .dash-header h1 {
            font-size: 1.25rem;
        }
        
        .dash-header h1 .icon-box {
            width: 36px;
            height: 36px;
            font-size: 0.9rem;
            border-radius: 10px;
        }
        
        .modern-card {
            border-radius: 16px;
        }
        
        .modern-card-body {
            padding: 1rem;
        }
        
        /* Single column on mobile */
        .course-card .course-img-wrapper {
            height: 180px;
        }
        
        .course-card .course-content {
            padding: 1rem;
        }
        
        .course-card .course-title {
            font-size: 1rem;
            height: auto;
            -webkit-line-clamp: 2;
            margin-bottom: 0.75rem;
        }
        
        /* Full width price section */
        .price-section {
            margin: 0.75rem 0;
            padding: 0.75rem;
            border-radius: 10px;
        }
        
        /* Status cards - horizontal layout */
        .enrolled-status-card,
        .free-status-card,
        .simple-price-card {
            min-height: 55px;
            gap: 10px;
        }
        
        .price-breakdown-mini {
            min-height: 55px;
        }
        
        .enrolled-icon,
        .free-icon,
        .price-icon {
            width: 40px;
            height: 40px;
            font-size: 1rem;
        }
        
        .enrolled-label,
        .free-label,
        .price-info .price-label {
            font-size: 0.7rem;
        }
        
        .enrolled-value {
            font-size: 0.875rem;
        }
        
        .free-value {
            font-size: 1rem;
        }
        
        .price-value-large {
            font-size: 1.125rem;
        }
        
        .price-row {
            font-size: 0.8rem;
        }
        
        .price-total {
            font-size: 0.95rem !important;
        }
        
        /* Button full width */
        .btn-course {
            padding: 0.625rem;
            font-size: 0.875rem;
            border-radius: 10px;
        }
        
        /* Badges */
        .course-badges {
            top: 10px;
            left: 10px;
            gap: 5px;
        }
        
        .course-badges span {
            padding: 4px 10px;
            font-size: 0.7rem;
            border-radius: 6px;
        }
        
        .enrollment-counter {
            bottom: 10px;
            right: 10px;
            padding: 4px 10px;
            font-size: 0.7rem;
        }
    }
    
    /* Extra small devices (< 400px) */
    @media (max-width: 400px) {
        .dash-header h1 {
            font-size: 1.125rem;
        }
        
        .course-card .course-title {
            font-size: 0.9375rem;
        }
        
        /* Stack status card vertically on very small screens */
        .enrolled-status-card,
        .free-status-card,
        .simple-price-card {
            flex-direction: column;
            text-align: center;
            padding: 0.5rem 0;
            min-height: auto;
        }
        
        .enrolled-info,
        .free-info,
        .price-info {
            align-items: center;
        }
        
        .enrolled-icon,
        .free-icon,
        .price-icon {
            margin-bottom: 4px;
        }
    }
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
           <?php echo get_phrase(strtolower(date('l'))) . ', ' . date('j') . ' ' . get_phrase(strtolower(date('F'))) . ' ' . date('Y'); ?>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="row mb-4 fade-up delay-1">
        <div class="col-12">
            <div class="modern-card">
                <div class="modern-card-body">
                    <form class="row align-items-end modern-filter-form" action="javascript:void(0)">
                        <!-- Champ caché pour le jeton CSRF -->
                        <input type="hidden" name="<?=$this->security->get_csrf_token_name();?>" value="<?=$this->security->get_csrf_hash();?>" id="csrf_token_filter" />
                        
                        <!-- Sélection classe -->
                        <div class="col-md-4 col-lg-3 mb-3 mb-md-0">
                            <label><?= get_phrase('classes'); ?></label>
                            <select class="form-control modern-select" name="class_id" id="class_id_course">
                                <option value="all" <?= $selected_class_id == 'all' ? 'selected' : ''; ?>><?= get_phrase('all'); ?></option>
                                <?php
                                if ($selected_school_id != "all") {
                                    $classes_list = $this->db->select('id, name')
                                        ->where('school_id', school_id())
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
                            <button type="submit" class="modern-btn w-100" onclick="filterStudentClasses()">
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
            ->or_where('classes.date_fin IS NULL', null, false)
            ->or_where('classes.date_fin', '0000-00-00')
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
        // IMPORTANT: Utiliser real_student_id (ID table students) pour les enrols, pas student_user_id
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

        // 3. Batch Fetch Currencies AND VAT Settings
        $currency_map = [];
        $vat_settings_map = [];
        if (!empty($school_ids)) {
            $this->db->select('school_id, system_currency, vat_enabled, vat_rate');
            $this->db->where_in('school_id', $school_ids);
            $query_settings = $this->db->get('settings_school')->result_array();
            foreach ($query_settings as $row) {
                $currency_map[$row['school_id']] = $row['system_currency'];
                $vat_settings_map[$row['school_id']] = [
                    'enabled' => isset($row['vat_enabled']) ? (int)$row['vat_enabled'] : 0,
                    'rate' => isset($row['vat_rate']) ? (float)$row['vat_rate'] : 20
                ];
            }
        }
        
        // 3b. Batch Fetch Tax Residence from schools table
        $tax_residence_map = [];
        if (!empty($school_ids)) {
            $this->db->select('id, country');
            $this->db->where_in('id', $school_ids);
            $query_schools = $this->db->get('schools')->result_array();
            foreach ($query_schools as $row) {
                $tax_residence_map[$row['id']] = strtoupper($row['country'] ?? '');
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
                // IMPORTANT: Utiliser real_student_id (ID dans table students) et non student_user_id (ID dans table users)
                $student_profile_id = $class['real_student_id']; // ID from students table for invoices/enrollments
                $student_user_id = $class['student_user_id']; // user_id for other purposes

                // Use pre-fetched data
                $enrols_count = $enrollment_counts[$class['id']] ?? 0;
                $currencies = $currency_map[$school_id] ?? 'MAD';

                $max_members = (int)($class['nombre_max_membre'] ?? 0);
                $is_full = $max_members > 0 && $enrols_count >= $max_members;

                // Check pre-fetched map - utiliser real_student_id (ID table students) car c'est ce qui est dans enrols.student_id
                $is_enrolled = isset($my_enrollments[$class['id'] . '_' . $student_profile_id]);
                
                // ========== VAT CALCULATION FOR CLASS ==========
                $class_price = (float)($class['price'] ?? 0);
                $vat_settings = $vat_settings_map[$school_id] ?? ['enabled' => 0, 'rate' => 20];
                $tax_residence = $tax_residence_map[$school_id] ?? '';
                
                $class_vat_applicable = false;
                $class_vat_rate = 0;
                $class_sub_total = $class_price;
                $class_vat_amount = 0;
                $class_grand_total = $class_price;
                
                // Apply VAT if school has it enabled and has tax residence
                if ($vat_settings['enabled'] && !empty($tax_residence)) {
                    $class_vat_applicable = true;
                    
                    // Determine VAT rate based on tax residence
                    if ($tax_residence === 'MA') {
                        $class_vat_rate = 20;
                    } elseif (in_array($tax_residence, ['AE', 'UAE'])) {
                        $class_vat_rate = 5;
                    } else {
                        $class_vat_rate = $vat_settings['rate'] ?: 20;
                    }
                    
                    // Price is TTC (includes VAT) - reverse calculate
                    $class_sub_total = round($class_price / (1 + ($class_vat_rate / 100)), 2);
                    $class_vat_amount = round($class_price - $class_sub_total, 2);
                    $class_grand_total = $class_price;
                }
                
                // Determine if free class
                $is_free = $class_price <= 0;
                ?>

                <div class="col-md-6 col-lg-4 col-xl-3 mb-4">
                    <div class="modern-card course-card">
                        <!-- Image with badges -->
                        <div class="course-img-wrapper">
                            <img src="<?= base_url('uploads/class/' . ($class['photo'] ?: 'placeholder.png')); ?>" 
                                 class="course-img" 
                                 alt="<?= $class['name']; ?>"
                                 onerror="this.src='<?= base_url('uploads/class/placeholder.png'); ?>'">
                            
                            <!-- Status Badges -->
                            <div class="course-badges">
                                <?php if ($is_enrolled): ?>
                                    <span class="badge-enrolled"><i class="fas fa-check-circle"></i> <?= get_phrase('enrolled'); ?></span>
                                <?php elseif ($is_free): ?>
                                    <span class="badge-free"><i class="fas fa-gift"></i> <?= get_phrase('free'); ?></span>
                                <?php elseif ($class_vat_applicable): ?>
                                    <span class="badge-vat"><?= ($tax_residence === 'MA') ? 'TVA' : 'VAT'; ?> <?= $class_vat_rate; ?>%</span>
                                <?php endif; ?>
                                
                                <?php if ($is_full && !$is_enrolled): ?>
                                    <span class="badge-full"><i class="fas fa-users"></i> <?= get_phrase('full'); ?></span>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Enrollment Count -->
                            <?php if ($max_members > 0): ?>
                            <div class="enrollment-counter">
                                <i class="fas fa-users"></i>
                                <span><?= $enrols_count; ?>/<?= $max_members; ?></span>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="course-content">
                            <h4 class="course-title"><?= htmlspecialchars($class['name']); ?></h4>
                            
                            <!-- Price Section - Always visible for consistent card height -->
                            <div class="price-section">
                                <?php if ($is_enrolled): ?>
                                    <!-- Enrolled Status Card -->
                                    <div class="enrolled-status-card">
                                        <div class="enrolled-icon">
                                            <i class="fas fa-check-circle"></i>
                                        </div>
                                        <div class="enrolled-info">
                                            <span class="enrolled-label"><?= get_phrase('member_since'); ?></span>
                                            <span class="enrolled-value"><?= get_phrase('active_membership'); ?></span>
                                        </div>
                                    </div>
                                <?php elseif ($is_free): ?>
                                    <!-- Free Access Card -->
                                    <div class="free-status-card">
                                        <div class="free-icon">
                                            <i class="fas fa-gift"></i>
                                        </div>
                                        <div class="free-info">
                                            <span class="free-label"><?= get_phrase('price'); ?></span>
                                            <span class="free-value"><?= get_phrase('free_access'); ?></span>
                                        </div>
                                    </div>
                                <?php elseif ($class_vat_applicable): ?>
                                    <!-- VAT Breakdown -->
                                    <div class="price-breakdown-mini">
                                        <div class="price-row">
                                            <span class="price-label"><?= get_phrase('subtotal'); ?> HT</span>
                                            <span class="price-value"><?= number_format($class_sub_total, 2); ?> <?= $currencies; ?></span>
                                        </div>
                                        <div class="price-row vat-row">
                                            <span class="price-label"><?= ($tax_residence === 'MA') ? 'TVA' : 'VAT'; ?> <?= $class_vat_rate; ?>%</span>
                                            <span class="price-value"><?= number_format($class_vat_amount, 2); ?> <?= $currencies; ?></span>
                                        </div>
                                        <div class="price-row total-row">
                                            <span class="price-label"><?= get_phrase('total'); ?> TTC</span>
                                            <span class="price-value price-total"><?= number_format($class_grand_total, 2); ?> <?= $currencies; ?></span>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <!-- Simple Price Card -->
                                    <div class="simple-price-card">
                                        <div class="price-icon">
                                            <i class="fas fa-tag"></i>
                                        </div>
                                        <div class="price-info">
                                            <span class="price-label"><?= get_phrase('price'); ?></span>
                                            <span class="price-value-large"><?= number_format($class_price, 2); ?> <?= $currencies; ?></span>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Action Buttons -->
                            <div class="course-actions">
                                <?php if ($is_enrolled): ?>
                                    <a href="<?= site_url('student/courses/' . $class['id']); ?>"
                                       class="btn-course success">
                                        <i class="fas fa-play-circle"></i> <?= get_phrase('access_class'); ?>
                                    </a>
                                <?php elseif ($is_full): ?>
                                    <button class="btn-course disabled" disabled>
                                        <i class="fas fa-ban"></i> <?= get_phrase('class_full'); ?>
                                    </button>
                                <?php elseif ($is_free): ?>
                                    <a href="javascript:;" 
                                       onclick="rightModal('<?= site_url('modal/popup/academy/add/' . $student_profile_id . '/' . $class['id'] . '/' . $school_id . '/0/' . $currencies . '/0/0/0') ?>','<?= get_phrase('join'); ?>');"
                                       class="btn-course success">
                                        <i class="fas fa-plus-circle"></i> <?= get_phrase('join_free'); ?>
                                    </a>
                                <?php else: ?>
                                    <a href="javascript:;" 
                                       onclick="rightModal('<?= site_url('modal/popup/academy/add/' . $student_profile_id . '/' . $class['id'] . '/' . $school_id . '/' . $class_grand_total . '/' . $currencies . '/' . ($class_vat_applicable ? '1' : '0') . '/' . $class_vat_rate . '/' . $class_sub_total) ?>','<?= get_phrase('join'); ?>');"
                                       class="btn-course primary">
                                        <i class="fas fa-credit-card"></i> <?= get_phrase('join_now'); ?>
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
function filterStudentClasses() {
    var class_id = $('#class_id_course').val();
    var user_id = $('#user_id').val();
    
    // Récupérer le nom et la valeur du jeton CSRF
    var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>';
    var csrfHash = $('#csrf_token_filter').val(); // Récupérer la valeur actuelle
    
    // Si l'input n'existe pas (cas de premier chargement ou remplacement), on essaie de le trouver autrement ou on utilise la valeur PHP initiale (risque de péremption)
    // Mais ici on l'a ajouté au DOM.
    // Attention : si la vue est rechargée, le JS aussi (car dans le même fichier), donc on réutilise PHP pour initialiser, mais
    // si on veut supporter la régénération, il faut que le retour AJAX contienne le nouveau token.

    var data = {
        class_id : class_id, 
        user_id : user_id
    };
    data[csrfName] = csrfHash;

    $.ajax({
        type : 'POST',
        url : '<?php echo site_url('academy/student/filter'); ?>',
        data : data,
        success : function(response) {
            $('.academy_content').html(response);
            // Pas de mise à jour de CSRF ici car on remplace tout le contenu y compris l'input caché et ce script.
            // Le nouveau contenu HTML généré par le serveur aura un nouveau token CSRF généré par PHP (via $this->security->get_csrf_hash()).
        }
    });
}


function schoolWiseClasse(school_id) {
alert(school_id);
    $.get("<?= route('academy/list/'); ?>" + school_id, function (response) {
        $('#class_id_course').html(response);
    });
}
</script>