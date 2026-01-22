<?php
    $student = $this->db->get_where('students', array('id' => $param1))->row_array();
    $user_id = $student['user_id'];
    $user = $this->db->get_where('users', array('id' => $user_id))->row_array();
    
    // Fetch classes
    $enrols = $this->db->get_where('enrols', array('student_id' => $param1))->result_array();
?>

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

    .exp-cover {
        height: 120px;
        background: linear-gradient(135deg, var(--exp-primary-light) 0%, #e0e7ff 100%);
        position: relative;
    }

    .exp-avatar-wrapper {
        position: absolute;
        bottom: -40px;
        left: 50%;
        transform: translateX(-50%);
        padding: 4px;
        background: white;
        border-radius: 50%;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .exp-avatar {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid white;
    }

    .exp-profile-body {
        padding: 3.5rem 1.5rem 1.5rem;
        text-align: center;
    }

    .exp-name {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--exp-dark);
        margin-bottom: 0.25rem;
    }

    .exp-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
    }
    
    .exp-badge-primary {
        background: var(--exp-primary-light);
        color: var(--exp-primary);
    }
    
    .exp-badge-success {
        background: #dcfce7;
        color: #16a34a;
    }
    
    .exp-badge-danger {
        background: #fef2f2;
        color: #dc2626;
    }

    .exp-info-list {
        display: flex;
        flex-direction: column;
        gap: 1rem;
        margin-top: 1.5rem;
        text-align: left;
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
        font-size: 1rem;
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

    .exp-detail-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 1rem;
    }

    .exp-detail-box {
        padding: 1rem;
        border: 1px solid var(--exp-border);
        border-radius: 12px;
        background: #f8fafc;
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
        word-break: break-word;
    }

    .exp-chip {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        background: white;
        border: 1px solid var(--exp-border);
        border-radius: 6px;
        font-size: 0.85rem;
        color: var(--exp-dark);
        margin-right: 0.5rem;
        margin-bottom: 0.5rem;
    }
</style>

<div class="row">
    <!-- Left Column: Profile Card -->
    <div class="col-lg-4">
        <div class="exp-card">
            <div class="exp-cover">
                <div class="exp-avatar-wrapper">
                    <img src="<?php echo $this->user_model->get_user_image($user_id); ?>" class="exp-avatar" alt="Profile">
                </div>
            </div>
            
            <div class="exp-profile-body">
                <h3 class="exp-name"><?php echo $user['name']; ?></h3>
                <div class="mb-3">
                    <span class="exp-badge exp-badge-primary">
                        <i class="mdi mdi-identifier"></i> <?php echo $student['code']; ?>
                    </span>
                    <?php if($user['status'] == 1): ?>
                        <span class="exp-badge exp-badge-success ml-2">
                            <i class="mdi mdi-check-circle"></i> <?php echo get_phrase('active'); ?>
                        </span>
                    <?php else: ?>
                        <span class="exp-badge exp-badge-danger ml-2">
                            <i class="mdi mdi-alert-circle"></i> <?php echo get_phrase('inactive'); ?>
                        </span>
                    <?php endif; ?>
                </div>

                <div class="exp-info-list">
                    <div class="exp-info-item">
                        <div class="exp-info-icon">
                            <i class="mdi mdi-email-outline"></i>
                        </div>
                        <div style="flex: 1;">
                            <div class="exp-label"><?php echo get_phrase('email'); ?></div>
                            <div class="exp-value"><?php echo $user['email']; ?></div>
                        </div>
                    </div>
                    
                    <div class="exp-info-item">
                        <div class="exp-info-icon">
                            <i class="mdi mdi-phone-outline"></i>
                        </div>
                        <div style="flex: 1;">
                            <div class="exp-label"><?php echo get_phrase('phone'); ?></div>
                            <div class="exp-value"><?php echo $user['phone'] ? $user['phone'] : '-'; ?></div>
                        </div>
                    </div>

                    <div class="exp-info-item">
                        <div class="exp-info-icon">
                            <i class="mdi mdi-cake-variant-outline"></i>
                        </div>
                        <div style="flex: 1;">
                            <div class="exp-label"><?php echo get_phrase('birthday'); ?></div>
                            <div class="exp-value"><?php echo $user['birthday'] ? date('d M, Y', strtotime($user['birthday'])) : '-'; ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Details -->
    <div class="col-lg-8">
        <!-- Academic Info -->
        <div class="exp-card" style="padding: 1.5rem;">
            <h4 class="exp-section-title">
                <i class="mdi mdi-school-outline"></i> <?php echo get_phrase('academic_information'); ?>
            </h4>
            
            <div class="mb-4">
                <div class="exp-label mb-2"><?php echo get_phrase('enrolled_classes'); ?></div>
                <div>
                    <?php foreach ($enrols as $enrol): 
                        $class = $this->db->get_where('classes', array('id' => $enrol['class_id']))->row_array();
                        if($class):
                    ?>
                        <span class="exp-chip">
                            <i class="mdi mdi-google-classroom"></i> <?php echo $class['name']; ?>
                        </span>
                    <?php endif; endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Address Info -->
        <div class="exp-card" style="padding: 1.5rem;">
            <h4 class="exp-section-title">
                <i class="mdi mdi-map-marker-radius-outline"></i> <?php echo get_phrase('address_details'); ?>
            </h4>
            
            <div class="exp-detail-grid">
                <div class="exp-detail-box">
                    <div class="exp-label"><?php echo get_phrase('street'); ?></div>
                    <div class="exp-value"><?php echo $user['Rue'] ? $user['Rue'] : '-'; ?></div>
                </div>
                
                <div class="exp-detail-box">
                    <div class="exp-label"><?php echo get_phrase('number'); ?></div>
                    <div class="exp-value"><?php echo $user['Numero'] ? $user['Numero'] : '-'; ?></div>
                </div>
                
                <div class="exp-detail-box">
                    <div class="exp-label"><?php echo get_phrase('city'); ?></div>
                    <div class="exp-value"><?php echo $user['Ville'] ? $user['Ville'] : '-'; ?></div>
                </div>
                
                <div class="exp-detail-box">
                    <div class="exp-label"><?php echo get_phrase('postal_code'); ?></div>
                    <div class="exp-value"><?php echo $user['Codepostal'] ? $user['Codepostal'] : '-'; ?></div>
                </div>
            </div>
        </div>
        
        <!-- Additional Info -->
        <div class="exp-card" style="padding: 1.5rem;">
            <h4 class="exp-section-title">
                <i class="mdi mdi-information-outline"></i> <?php echo get_phrase('other_information'); ?>
            </h4>
            
            <div class="exp-detail-grid">
                <div class="exp-detail-box">
                    <div class="exp-label"><?php echo get_phrase('gender'); ?></div>
                    <div class="exp-value"><?php echo get_phrase(strtolower($user['gender'])); ?></div>
                </div>
                
                <div class="exp-detail-box">
                    <div class="exp-label"><?php echo get_phrase('blood_group'); ?></div>
                    <div class="exp-value"><?php echo $user['blood_group'] ? $user['blood_group'] : '-'; ?></div>
                </div>

                <?php if(!empty($user['num_vat'])): ?>
                <div class="exp-detail-box">
                    <div class="exp-label"><?php echo get_phrase('vat_number'); ?></div>
                    <div class="exp-value"><?php echo $user['num_vat']; ?></div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>