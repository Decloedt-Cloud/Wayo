<?php $school_id = school_id(); ?>

<style>
:root {
    --form-primary: #6366f1;
    --form-primary-rgb: 99, 102, 241;
    --form-success: #10b981;
    --form-dark: #1e293b;
    --form-gray: #64748b;
    --form-light: #f8fafc;
    --form-border: #e2e8f0;
    --form-white: #ffffff;
}

.adm-form-container { padding: 0.5rem; }

.adm-form-header { display: flex; align-items: center; gap: 1rem; margin-bottom: 1.5rem; padding-bottom: 1.25rem; border-bottom: 2px solid var(--form-border); }
.adm-form-icon { width: 50px; height: 50px; border-radius: 14px; background: linear-gradient(135deg, var(--form-primary), #8b5cf6); display: flex; align-items: center; justify-content: center; font-size: 1.25rem; color: white; box-shadow: 0 6px 16px rgba(var(--form-primary-rgb), 0.3); }
.adm-form-title { flex: 1; }
.adm-form-title h3 { margin: 0; font-size: 1.125rem; font-weight: 700; color: var(--form-dark); }
.adm-form-title p { margin: 0.25rem 0 0; font-size: 0.8rem; color: var(--form-gray); }

.adm-info-card { display: flex; align-items: flex-start; gap: 0.875rem; padding: 1rem; background: linear-gradient(135deg, rgba(var(--form-primary-rgb), 0.05), rgba(var(--form-primary-rgb), 0.1)); border-radius: 12px; margin-bottom: 1.5rem; border: 1px solid rgba(var(--form-primary-rgb), 0.2); }
.adm-info-icon { width: 36px; height: 36px; border-radius: 10px; background: var(--form-primary); display: flex; align-items: center; justify-content: center; color: white; font-size: 1rem; flex-shrink: 0; }
.adm-info-content h4 { margin: 0 0 0.25rem; font-size: 0.8rem; font-weight: 600; color: var(--form-dark); }
.adm-info-content p { margin: 0; font-size: 0.75rem; color: var(--form-gray); line-height: 1.5; }

.adm-form-actions { margin-top: 1.5rem; }

.adm-btn { display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; width: 100%; padding: 0.875rem 1.5rem; border-radius: 12px; font-size: 0.9375rem; font-weight: 600; cursor: pointer; transition: all 0.2s; border: none; }
.adm-btn-primary { background: linear-gradient(135deg, var(--form-primary), #8b5cf6); color: white; box-shadow: 0 4px 12px rgba(var(--form-primary-rgb), 0.3); }
.adm-btn-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(var(--form-primary-rgb), 0.4); }
.adm-btn-primary:active { transform: translateY(0); }
</style>

<div class="adm-form-container">
    <div class="adm-form-header">
        <div class="adm-form-icon">
            <i class="mdi mdi-account-check"></i>
        </div>
        <div class="adm-form-title">
            <h3><?php echo get_phrase('approve_application'); ?></h3>
            <p><?php echo get_phrase('confirm_student_admission'); ?></p>
        </div>
    </div>
    
    <div class="adm-info-card">
        <div class="adm-info-icon">
            <i class="mdi mdi-information-outline"></i>
        </div>
        <div class="adm-info-content">
            <h4><?php echo get_phrase('about_approval'); ?></h4>
            <p><?php echo get_phrase('approving_will_activate_student_account'); ?></p>
        </div>
    </div>
    
    <form method="POST" class="d-block" action="<?php echo site_url('admin/online_admission/assigned'); ?>">
        <input type="hidden" name="<?=$this->security->get_csrf_token_name();?>" value="<?=$this->security->get_csrf_hash();?>" />
        <input type="hidden" name="student_id" value="<?php echo $param1; ?>">
        <input type="hidden" name="school_id" value="<?php echo $param2; ?>">
        
        <div class="adm-form-actions">
            <button class="adm-btn adm-btn-primary" type="submit">
                <i class="mdi mdi-check-circle"></i>
                <span><?php echo get_phrase('approve'); ?></span>
            </button>
        </div>
    </form>
</div>