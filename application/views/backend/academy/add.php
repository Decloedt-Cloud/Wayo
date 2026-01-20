<?php
// Securely cast and alias input parameters for clarity and safety
$student_id = (int) $param1;
$class_id   = (int) $param2;
$school_id  = (int) $param3;
$price      = html_escape($param4);
$currency   = html_escape($param5);

// Get current school ID if not provided
if (empty($school_id)) {
    $school_id = school_id();
}
?>

<style>
    /* Scoped Modern Styles for Modal */
    .modern-modal-wrapper {
        /* Dashboard Color Palette */
        --primary: #6366f1;
        --primary-light: #818cf8;
        --primary-lighter: #e0e7ff;
        --primary-dark: #4338ca;
        --bg-main: #f8fafc;
        --bg-card: #ffffff;
        --text-dark: #1e293b;
        --text-muted: #64748b;
        --border-color: #e2e8f0;
        
        font-family: 'DM Sans', sans-serif;
        padding: 1.5rem;
        text-align: center;
        background: var(--bg-card);
        border-radius: 12px;
    }

    .modal-icon-box {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, var(--primary-lighter), #c7d2fe);
        color: var(--primary);
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        margin: 0 auto 1.5rem;
        box-shadow: 0 10px 15px -3px rgba(99, 102, 241, 0.2);
    }

    .modal-title {
        font-family: 'Outfit', sans-serif;
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--text-dark);
        margin-bottom: 0.75rem;
    }

    .modal-text {
        color: var(--text-muted);
        margin-bottom: 2rem;
        font-size: 1rem;
        line-height: 1.5;
    }

    .price-badge {
        background: var(--bg-main);
        border: 1px solid var(--border-color);
        padding: 0.75rem 1.25rem;
        border-radius: 50px;
        font-weight: 600;
        color: var(--primary-dark);
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 2rem;
    }

    .modern-btn-submit {
        background: var(--primary);
        color: white;
        border: none;
        border-radius: 12px;
        padding: 1rem;
        font-weight: 600;
        width: 100%;
        transition: all 0.3s ease;
        font-size: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        cursor: pointer;
        box-shadow: 0 4px 6px -1px rgba(99, 102, 241, 0.4);
    }

    .modern-btn-submit:hover {
        background: var(--primary-dark);
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(99, 102, 241, 0.5);
    }
    
    .modern-btn-submit i {
        font-size: 1.1rem;
    }
</style>

<div class="modern-modal-wrapper">
    <div class="modal-icon-box">
        <i class="fas fa-user-graduate"></i>
    </div>
    
    <h3 class="modal-title"><?php echo get_phrase('join_class'); ?></h3>
    
    <div class="modal-text">
        <?php echo get_phrase('are_you_sure_you_want_to_join_this_class'); ?>?
    </div>

    <?php if(!empty($price) && $price > 0): ?>
        <div class="price-badge">
            <i class="fas fa-tag"></i>
            <?php echo $price . ' ' . $currency; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?php echo site_url('student/online_admission/assigned'); ?>" id="joinClassForm">
        <input type="hidden" name="<?=$this->security->get_csrf_token_name();?>" value="<?=$this->security->get_csrf_hash();?>" />
        <input type="hidden" name="student_id" value="<?php echo $student_id; ?>">
        <input type="hidden" name="class_id" id="class_id" value="<?php echo $class_id; ?>">
        <input type="hidden" name="school_id" value="<?php echo $school_id; ?>">
        <input type="hidden" name="price" id="price" value="<?php echo $price; ?>">
        <input type="hidden" name="currency" id="currency" value="<?php echo $currency; ?>">

        <button class="modern-btn-submit" type="submit" id="btnJoin">
            <i class="fas fa-check-circle"></i> <?php echo get_phrase('confirm_and_join'); ?>
        </button>
    </form>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        // Prevent double submission
        $('#joinClassForm').on('submit', function() {
            var btn = $('#btnJoin');
            btn.prop('disabled', true);
            btn.html('<i class="fas fa-spinner fa-spin"></i> <?php echo get_phrase('processing'); ?>...');
        });
    });
</script>