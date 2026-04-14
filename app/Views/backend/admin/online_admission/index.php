<link rel="stylesheet" href="<?php echo base_url(); ?>assets/backend/css/responsive.min.css">

<style>
/* ============================================================================
   ONLINE ADMISSION - MODERN DESIGN (MATCHING EXPENSE STYLE)
   ============================================================================ */

:root {
    --adm-primary: #6366f1;
    --adm-primary-light: #eef2ff;
    --adm-success: #059669;
    --adm-dark: #1e293b;
    --adm-gray: #64748b;
    --adm-light: #f8fafc;
    --adm-border: #e2e8f0;
    --adm-warning: #f59e0b;
}

/* Header Card */
.adm-header {
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

.adm-header-left {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.adm-header-icon {
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

.adm-header-text h4 {
    margin: 0;
    color: white;
    font-size: 1.35rem;
    font-weight: 700;
}

.adm-header-text p {
    margin: 0.25rem 0 0;
    color: rgba(255,255,255,0.7);
    font-size: 0.85rem;
}

/* Content Card */
.adm-content-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
    border: 1px solid var(--adm-border);
    overflow: hidden;
}

.adm-content-header {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    padding: 1rem 1.5rem;
    border-bottom: 1px solid var(--adm-border);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.adm-content-title {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 600;
    color: var(--adm-dark);
    font-size: 0.95rem;
}

.adm-content-title i {
    color: var(--adm-primary);
}

.adm-content-body {
    padding: 0;
}

/* Loading State */
.adm-loading {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 3rem;
    color: var(--adm-gray);
}

.adm-loading i {
    font-size: 2rem;
    animation: adm-spin 1s linear infinite;
    margin-right: 0.75rem;
}

@keyframes adm-spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

/* Responsive */
@media (max-width: 768px) {
    .adm-header {
        flex-direction: column;
        text-align: center;
    }
    
    .adm-header-left {
        flex-direction: column;
    }
}
</style>

<!-- Header -->
<div class="adm-header">
    <div class="adm-header-left">
        <div class="adm-header-icon">
            <i class="fas fa-user-graduate"></i>
        </div>
        <div class="adm-header-text">
            <h4><?php echo get_phrase('online_admission'); ?></h4>
            <p><?php echo get_phrase('manage_admission_applications'); ?></p>
        </div>
    </div>
</div>

<!-- Content Card -->
<div class="adm-content-card">
    <div class="adm-content-header">
        <span class="adm-content-title">
            <i class="mdi mdi-format-list-bulleted"></i>
            <?php echo get_phrase('admission_applications'); ?>
        </span>
    </div>
    <div class="adm-content-body">
        <div class="admission_content">
            <?php include 'list.php'; ?>
        </div>
    </div>
</div>
