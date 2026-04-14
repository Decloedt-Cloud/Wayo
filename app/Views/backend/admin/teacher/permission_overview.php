<style>
    :root {
        --exp-primary: #6366f1;
        --exp-primary-light: #eef2ff;
        --exp-dark: #1e293b;
        --exp-gray: #64748b;
        --exp-border: #e2e8f0;
        --exp-success: #10b981;
        --exp-danger: #ef4444;
    }

    .exp-card {
        background: white;
        border-radius: 16px;
        border: 1px solid var(--exp-border);
        box-shadow: 0 2px 12px rgba(0,0,0,0.05);
        overflow: hidden;
        margin-bottom: 1.5rem;
    }

    .exp-info-list {
        display: flex;
        flex-direction: column;
        gap: 1rem;
        margin-top: 1.5rem;
    }

    .exp-info-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 0.75rem;
        background: #f8fafc;
        border-radius: 12px;
        border: 1px solid var(--exp-border);
    }

    .exp-info-icon {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        background: white;
        color: var(--exp-gray);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
    }

    .exp-section-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--exp-dark);
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .exp-section-title i {
        color: var(--exp-primary);
    }

    .exp-label {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--exp-gray);
        margin-bottom: 0.25rem;
    }

    .exp-value {
        font-weight: 600;
        color: var(--exp-dark);
        font-size: 0.95rem;
    }

    .exp-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        padding: 0.25rem 0.5rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
    }
    
    .exp-badge-success {
        background: #dcfce7;
        color: #16a34a;
    }
    
    .exp-badge-danger {
        background: #fef2f2;
        color: #dc2626;
    }
</style>

<div class="row" style="min-width: 300px;">
    <div class="col-md-12">
        <div class="exp-card" style="padding: 1.5rem;">
            <h4 class="exp-section-title justify-content-center">
                <i class="mdi mdi-account-key-outline"></i>
                <?php echo db()->table('users')->where('id', $param2)->get()->getRow()->name ?? ''; ?>
            </h4>

            <div class="exp-info-list">
                <?php
                $teacher_permissions = db()->table('teacher_permissions')->where('teacher_id', $param1)->get()->getResultArray();
                if (count($teacher_permissions) > 0):
                    foreach($teacher_permissions as $permission):
                        $class_name = db()->table('classes')->where('id', $permission['class_id'])->get()->getRow()->name ?? '';
                ?>
                    <div class="exp-info-item justify-content-between">
                        <div class="d-flex align-items-center gap-3">
                            <div class="exp-info-icon">
                                <i class="mdi mdi-google-classroom"></i>
                            </div>
                            <div>
                                <div class="exp-label"><?php echo get_phrase('class'); ?></div>
                                <div class="exp-value"><?php echo $class_name; ?></div>
                            </div>
                        </div>
                        
                        <div class="d-flex gap-3">
                            <!-- Marks Permission -->
                             <div class="text-center">
                                <div class="exp-label mb-1" style="font-size: 0.65rem;"><?php echo get_phrase('marks'); ?></div>
                                <?php if($permission['marks'] == 1): ?>
                                    <span class="exp-badge exp-badge-success" title="<?php echo get_phrase('allowed'); ?>">
                                        <i class="mdi mdi-check"></i>
                                    </span>
                                <?php else: ?>
                                    <span class="exp-badge exp-badge-danger" title="<?php echo get_phrase('denied'); ?>">
                                        <i class="mdi mdi-close"></i>
                                    </span>
                                <?php endif; ?>
                             </div>

                             <!-- Attendance Permission -->
                             <div class="text-center">
                                <div class="exp-label mb-1" style="font-size: 0.65rem;"><?php echo get_phrase('attendance'); ?></div>
                                <?php if($permission['attendance'] == 1): ?>
                                    <span class="exp-badge exp-badge-success" title="<?php echo get_phrase('allowed'); ?>">
                                        <i class="mdi mdi-check"></i>
                                    </span>
                                <?php else: ?>
                                    <span class="exp-badge exp-badge-danger" title="<?php echo get_phrase('denied'); ?>">
                                        <i class="mdi mdi-close"></i>
                                    </span>
                                <?php endif; ?>
                             </div>
                        </div>
                    </div>
                <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center py-4">
                        <div class="text-muted mb-2">
                            <i class="mdi mdi-alert-circle-outline" style="font-size: 3rem; color: var(--exp-gray); opacity: 0.5;"></i>
                        </div>
                        <p class="text-muted small"><?php echo get_phrase('no_permission_assigned_yet'); ?></p>
                    </div>
                <?php endif; ?>
            </div>

            <div class="mt-4">
                <a href="<?php echo route('permission'); ?>" class="btn btn-primary w-100 rounded-pill fw-semibold" style="background-color: var(--exp-primary); border-color: var(--exp-primary);">
                    <i class="mdi mdi-pencil-outline me-1"></i> <?php echo get_phrase('update_permissions'); ?>
                </a>
            </div>
        </div>
    </div>
</div>