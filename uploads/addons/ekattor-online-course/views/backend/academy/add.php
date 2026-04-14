<?php
// Securely cast and alias input parameters for clarity and safety
$student_id = (int) $param1;
$class_id   = (int) $param2;
$school_id  = (int) $param3;
$price      = (float) html_escape($param4);
$currency   = html_escape($param5);

// VAT Parameters (new)
$vat_applicable = isset($param6) ? (int) $param6 : 0;
$vat_rate       = isset($param7) ? (float) $param7 : 0;
$sub_total      = isset($param8) ? (float) $param8 : $price;

// Calculate VAT amount if applicable
$vat_amount = 0;
if ($vat_applicable && $vat_rate > 0) {
    $vat_amount = round($price - $sub_total, 2);
}

// Get current school ID if not provided
if (empty($school_id)) {
    $school_id = school_id();
}

// Get tax residence for label
$tax_residence = '';
if ($vat_applicable) {
    $school_data = db()->table('schools')->where('id', $school_id)->get()->getRowArray();
    $tax_residence = strtoupper($school_data['country'] ?? '');
}
$vat_label = ($tax_residence === 'MA') ? 'TVA' : 'VAT';
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
    
    /* VAT Breakdown Styles */
    .vat-breakdown-card {
        background: var(--bg-main);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        padding: 1.25rem;
        margin-bottom: 1.5rem;
        text-align: left;
    }
    
    .vat-breakdown-header {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 1rem;
        padding-bottom: 0.75rem;
        border-bottom: 1px dashed var(--border-color);
    }
    
    .vat-breakdown-header i {
        color: #f59e0b;
        font-size: 1.1rem;
    }
    
    .vat-breakdown-header span {
        font-weight: 600;
        color: var(--text-dark);
        font-size: 0.875rem;
    }
    
    .vat-breakdown-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.5rem 0;
        font-size: 0.9rem;
    }
    
    .vat-breakdown-row .label {
        color: var(--text-muted);
    }
    
    .vat-breakdown-row .value {
        font-weight: 600;
        color: var(--text-dark);
    }
    
    .vat-breakdown-row.vat-line {
        color: #f59e0b;
    }
    
    .vat-breakdown-row.vat-line .label,
    .vat-breakdown-row.vat-line .value {
        color: #f59e0b;
    }
    
    .vat-breakdown-row.total-line {
        margin-top: 0.5rem;
        padding-top: 0.75rem;
        border-top: 2px solid var(--primary-lighter);
    }
    
    .vat-breakdown-row.total-line .label {
        font-weight: 700;
        color: var(--text-dark);
    }
    
    .vat-breakdown-row.total-line .value {
        font-size: 1.25rem;
        font-weight: 800;
        color: var(--primary);
    }
    
    .vat-badge-inline {
        background: rgba(245, 158, 11, 0.15);
        color: #f59e0b;
        padding: 2px 8px;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 600;
        margin-left: 6px;
    }
    
    /* Free Class Style */
    .free-badge {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        padding: 1rem 2rem;
        border-radius: 16px;
        font-weight: 700;
        font-size: 1.125rem;
        display: inline-flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
    }
    
    .free-badge i {
        font-size: 1.5rem;
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
        <?php echo get_phrase('are_you_sure_you_want_to_join_this_class'); ?>
    </div>

    <?php if($price <= 0): ?>
        <!-- Free Class -->
        <div class="free-badge">
            <i class="fas fa-gift"></i>
            <?php echo get_phrase('free_access'); ?>
        </div>
    <?php elseif($vat_applicable && $vat_rate > 0): ?>
        <!-- VAT Breakdown Card -->
        <div class="vat-breakdown-card">
            <div class="vat-breakdown-header">
                <i class="fas fa-receipt"></i>
                <span><?php echo get_phrase('price_details'); ?></span>
                <span class="vat-badge-inline"><?php echo $vat_label . ' ' . $vat_rate; ?>%</span>
            </div>
            
            <div class="vat-breakdown-row">
                <span class="label"><?php echo get_phrase('subtotal'); ?> (HT)</span>
                <span class="value"><?php echo number_format($sub_total, 2) . ' ' . $currency; ?></span>
            </div>
            
            <div class="vat-breakdown-row vat-line">
                <span class="label"><?php echo $vat_label; ?> (<?php echo $vat_rate; ?>%)</span>
                <span class="value"><?php echo number_format($vat_amount, 2) . ' ' . $currency; ?></span>
            </div>
            
            <div class="vat-breakdown-row total-line">
                <span class="label"><?php echo get_phrase('total'); ?> (TTC)</span>
                <span class="value"><?php echo number_format($price, 2) . ' ' . $currency; ?></span>
            </div>
        </div>
    <?php else: ?>
        <!-- Simple Price Badge (No VAT) -->
        <div class="price-badge">
            <i class="fas fa-tag"></i>
            <?php echo number_format($price, 2) . ' ' . $currency; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?php echo site_url('student/online_admission/assigned'); ?>" id="joinClassForm">
        <input type="hidden" name="<?=csrf_token();?>" value="<?=csrf_hash();?>" />
        <input type="hidden" name="student_id" value="<?php echo $student_id; ?>">
        <input type="hidden" name="class_id" id="class_id" value="<?php echo $class_id; ?>">
        <input type="hidden" name="school_id" value="<?php echo $school_id; ?>">
        <input type="hidden" name="price" id="price" value="<?php echo $price; ?>">
        <input type="hidden" name="currency" id="currency" value="<?php echo $currency; ?>">
        
        <!-- VAT Fields -->
        <input type="hidden" name="vat_applicable" value="<?php echo $vat_applicable; ?>">
        <input type="hidden" name="vat_rate" value="<?php echo $vat_rate; ?>">
        <input type="hidden" name="vat_amount" value="<?php echo $vat_amount; ?>">
        <input type="hidden" name="sub_total" value="<?php echo $sub_total; ?>">

        <button class="modern-btn-submit" type="submit" id="btnJoin">
            <?php if($price <= 0): ?>
                <i class="fas fa-check-circle"></i> <?php echo get_phrase('join_now'); ?>
            <?php else: ?>
                <i class="fas fa-credit-card"></i> <?php echo get_phrase('proceed_to_payment'); ?>
            <?php endif; ?>
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