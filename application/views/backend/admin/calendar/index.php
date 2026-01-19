<link rel="stylesheet" href="<?php echo base_url(); ?>assets/backend/css/custom/event_calendar.css">

<style>
/* ============================================================================
   CALENDAR MANAGER - MODERN DESIGN (MATCHING TEACHER/STUDENT STYLE)
   ============================================================================ */

:root {
    --exp-primary: #6366f1;
    --exp-primary-light: #eef2ff;
    --exp-success: #059669;
    --exp-dark: #1e293b;
    --exp-gray: #64748b;
    --exp-light: #f8fafc;
    --exp-border: #e2e8f0;
    --exp-warning: #f59e0b;
    --exp-danger: #ef4444;
}

/* Header Card */
.exp-header {
    background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
    border-radius: 16px;
    padding: 1.5rem 2rem;
    margin-bottom: 1.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
    box-shadow: 0 4px 20px rgba(99, 102, 241, 0.3);
}

.exp-header-left {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.exp-header-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    background: rgba(255,255,255,0.15);
    backdrop-filter: blur(10px);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
}

.exp-header-text h4 {
    margin: 0;
    color: white;
    font-size: 1.35rem;
    font-weight: 700;
}

.exp-header-text p {
    margin: 0.25rem 0 0;
    color: rgba(255,255,255,0.7);
    font-size: 0.85rem;
}

.exp-header-actions {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
}

.exp-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.625rem;
    padding: 0.875rem 1.75rem;
    border-radius: 16px;
    font-size: 0.95rem;
    font-weight: 700;
    letter-spacing: 0.5px;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
    box-shadow: 
        0 4px 12px rgba(0, 0, 0, 0.08),
        inset 0 1px 0 rgba(255, 255, 255, 0.15);
    text-transform: uppercase;
    color: var(--exp-dark);
    background: white;
}

.exp-btn-primary {
    background: linear-gradient(135deg, var(--exp-primary), #8b5cf6);
    color: white;
    border: 1px solid rgba(255, 255, 255, 0.25);
    backdrop-filter: blur(10px);
}

.exp-btn-primary:hover {
    transform: translateY(-3px) scale(1.02);
    box-shadow: 
        0 12px 30px rgba(99, 102, 241, 0.6),
        0 4px 15px rgba(139, 92, 246, 0.4);
    background: linear-gradient(135deg, #8b5cf6, var(--exp-primary));
}

.exp-btn-outline {
    background: transparent;
    border: 1px solid var(--exp-primary);
    color: var(--exp-primary);
}

.exp-btn-outline:hover, .exp-btn-outline.active {
    background: var(--exp-primary);
    color: white;
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
}

/* Content Card */
.exp-content-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
    border: 1px solid var(--exp-border);
    overflow: hidden;
}

.exp-content-header {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    padding: 1rem 1.5rem;
    border-bottom: 1px solid var(--exp-border);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.exp-content-title {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 600;
    color: var(--exp-dark);
    font-size: 0.95rem;
}

.exp-content-title i {
    color: var(--exp-primary);
}

.exp-content-body {
    padding: 1.5rem;
}

/* Calendar Customizations to match theme */
.fc-toolbar-title {
    font-size: 1.5rem !important;
    font-weight: 700;
    color: var(--exp-dark);
}

.fc-button-primary {
    background-color: var(--exp-primary) !important;
    border-color: var(--exp-primary) !important;
}

.fc-button-primary:hover {
    background-color: #4f46e5 !important;
    border-color: #4f46e5 !important;
}

.fc-button-active {
    background-color: #4338ca !important;
    border-color: #4338ca !important;
}

/* Modal Customizations */
.exp-modal-header {
    background: linear-gradient(135deg, var(--exp-primary) 0%, #8b5cf6 100%);
    color: white;
    border-bottom: 1px solid rgba(255,255,255,0.1);
    border-radius: 16px 16px 0 0;
    padding: 1.5rem;
}

.exp-modal-header .modal-title {
    color: white;
    font-weight: 700;
}

.exp-modal-header .btn-close {
    filter: brightness(0) invert(1);
    opacity: 0.8;
}

.exp-modal-content {
    border: none;
    border-radius: 16px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.1);
}

.exp-form-group {
    margin-bottom: 1.25rem;
}

.exp-form-label {
    display: block;
    font-size: 0.85rem;
    font-weight: 600;
    color: var(--exp-gray);
    margin-bottom: 0.5rem;
}

.exp-form-control {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 1px solid var(--exp-border);
    border-radius: 10px;
    font-size: 0.95rem;
    color: var(--exp-dark);
    background: var(--exp-light);
    transition: all 0.2s;
}

.exp-form-control:focus {
    outline: none;
    border-color: var(--exp-primary);
    background: white;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}

.exp-modal-footer {
    padding: 1.5rem;
    border-top: 1px solid var(--exp-border);
    display: flex;
    justify-content: flex-end;
    gap: 1rem;
}
</style>

<!--title-->
<div class="row">
    <div class="col-12">
        <div class="exp-header">
            <div class="exp-header-left">
                <div class="exp-header-icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="exp-header-text">
                    <h4><?php echo get_phrase('calendar'); ?></h4>
                    <p><?php echo get_phrase('manage_events_and_schedules'); ?></p>
                </div>
            </div>
            <div class="exp-header-actions">
                <button type="button" class="exp-btn exp-btn-primary" data-bs-toggle="modal" data-bs-target="#createEventModal">
                    <i class="mdi mdi-plus"></i>
                    <span><?php echo get_phrase('new_event'); ?></span>
                </button>
            </div>
        </div>
    </div>
</div>

<div class="row">
  <div class="col-12">
      <div class="exp-content-card">
        <div class="exp-content-header">
            <span class="exp-content-title">
                <i class="mdi mdi-calendar-month"></i>
                <?php echo get_phrase('event_calendar'); ?>
            </span>
        </div>
        <div class="exp-content-body">
          <?php include 'list.php'; ?>
        </div>
      </div>
  </div>
</div>