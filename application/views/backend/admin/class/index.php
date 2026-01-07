<link rel="stylesheet" href="<?php echo base_url(); ?>assets/backend/css/responsive.css">

<style>
/* ============================================================================
   CLASS - MODERN DESIGN (MATCHING EXPENSE CATEGORY STYLE)
   ============================================================================ */

:root {
    --class-primary: #6366f1;
    --class-primary-light: #eef2ff;
    --class-success: #059669;
    --class-dark: #1e293b;
    --class-gray: #64748b;
    --class-light: #f8fafc;
    --class-border: #e2e8f0;
    --class-warning: #f59e0b;
}

/* Header Card */
.class-header {
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

.class-header-left {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.class-header-icon {
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

.class-header-text h4 {
    margin: 0;
    color: white;
    font-size: 1.35rem;
    font-weight: 700;
}

.class-header-text p {
    margin: 0.25rem 0 0;
    color: rgba(255,255,255,0.7);
    font-size: 0.85rem;
}

.class-header-actions {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
}

.class-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.65rem 1.25rem;
    border-radius: 10px;
    font-size: 0.875rem;
    font-weight: 600;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all 0.2s;
}

.class-btn-primary {
    background: linear-gradient(135deg, #1e293b, #334155);
    color: white;
}

.class-btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(30, 41, 59, 0.4);
    color: white;
}

/* Content Card */
.class-content-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
    border: 1px solid var(--class-border);
    overflow: hidden;
}

.class-content-header {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    padding: 1rem 1.5rem;
    border-bottom: 1px solid var(--class-border);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.class-content-title {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 600;
    color: var(--class-dark);
    font-size: 0.95rem;
}

.class-content-title i {
    color: var(--class-primary);
}

.class-content-body {
    padding: 0;
}

/* Loading State */
.class-loading {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 3rem;
    color: var(--class-gray);
}

.class-loading i {
    font-size: 2rem;
    animation: class-spin 1s linear infinite;
    margin-right: 0.75rem;
}

@keyframes class-spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

/* Responsive */
@media (max-width: 768px) {
    .class-header {
        flex-direction: column;
        text-align: center;
    }

    .class-header-left {
        flex-direction: column;
    }
}

/* ============================================================================
   COMMUNITY PRICING CARD - Based on payment_settings.php design
   ============================================================================ */

.pricing-card {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.06);
    border: 1px solid rgba(0,0,0,0.05);
    overflow: hidden;
    margin-bottom: 1.5rem;
    transition: all 0.3s ease;
    animation: fadeInUp 0.5s ease forwards;
}

.pricing-card:hover {
    box-shadow: 0 8px 30px rgba(0,0,0,0.1);
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.pricing-card-header {
    padding: 20px 24px;
    border-bottom: 1px solid #f0f0f0;
    display: flex;
    align-items: center;
    gap: 14px;
}

.pricing-card-header .icon-box {
    width: 44px;
    height: 44px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    color: #fff;
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
}

.pricing-card-header h5 {
    margin: 0;
    font-size: 17px;
    font-weight: 600;
    color: #1a1a2e;
}

.pricing-card-header small {
    display: block;
    color: #6c757d;
    font-size: 12px;
    margin-top: 2px;
}

.pricing-card-body {
    padding: 24px;
}

.form-group-modern {
    margin-bottom: 20px;
}

.form-group-modern label {
    display: block;
    font-weight: 600;
    color: #344054;
    margin-bottom: 8px;
    font-size: 14px;
}

.form-control-modern {
    width: 100%;
    padding: 12px 16px;
    border: 1.5px solid #e5e7eb;
    border-radius: 10px;
    font-size: 14px;
    transition: all 0.2s ease;
    background: #fafafa;
}

.form-control-modern:focus {
    outline: none;
    border-color: #667eea;
    background: #fff;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.15);
}

.form-control-modern:read-only {
    background: #f3f4f6;
    cursor: not-allowed;
}

.input-with-icon {
    position: relative;
}

.input-with-icon .icon {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: #9ca3af;
    font-size: 16px;
}

.input-with-icon .form-control-modern {
    padding-left: 42px;
}

.btn-modern {
    padding: 12px 24px;
    border-radius: 10px;
    font-weight: 600;
    font-size: 14px;
    border: none;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
}

.btn-modern.btn-success-gradient {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    color: #fff;
}

.btn-modern.btn-success-gradient:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(17, 153, 142, 0.4);
}

.info-box {
    background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
    border-radius: 10px;
    padding: 14px 18px;
    display: flex;
    align-items: flex-start;
    gap: 12px;
    margin-top: 16px;
}

.info-box i {
    color: #92400e;
    font-size: 18px;
    margin-top: 2px;
}

.info-box p {
    margin: 0;
    font-size: 13px;
    color: #92400e;
    line-height: 1.5;
}

.w-100 {
    width: 100%;
}
</style>

<!-- Header -->
<div class="class-header">
    <div class="class-header-left">
        <div class="class-header-icon">
            <i class="fas fa-chalkboard"></i>
        </div>
        <div class="class-header-text">
            <h4><?php echo get_phrase('membership'); ?></h4>
            <p><?php echo get_phrase('manage_all_pricing'); ?></p>
        </div>
    </div>
    <div class="class-header-actions">
        <button type="button" class="class-btn class-btn-primary" onclick="rightModal('<?php echo site_url('modal/popup/class/create'); ?>', '<?php echo htmlspecialchars(get_phrase('create_class'), ENT_QUOTES); ?>')">
            <i class="mdi mdi-plus"></i> <?php echo get_phrase('add_class'); ?>
        </button>
    </div>
</div>

<?php
// Get data for Community Pricing
$school_data = $this->settings_model->get_current_school_data();
$settings_school = $this->settings_model->get_current_settings_school_data();
$result = $this->db->get_where('settings_school', array('school_id' => school_id()))->row_array();
?>

<!-- Community Pricing Card -->
<div class="pricing-card">
    <div class="pricing-card-header">
        <div class="icon-box">
            <i class="fas fa-tag"></i>
        </div>
        <div>
            <h5><?php echo get_phrase('community_pricing'); ?></h5>
            <small><?php echo get_phrase('set_the_subscription_price_for_your_community'); ?></small>
        </div>
    </div>
    <div class="pricing-card-body">
        <form method="POST" class="communityPriceAjaxForm" action="<?php echo route('payment_settings/price'); ?>" id="community_price_settings">
            <input type="hidden" name="<?=$this->security->get_csrf_token_name();?>" value="<?=$this->security->get_csrf_hash();?>" />
            
            <div class="form-group-modern">
                <label for="price_community">
                    <?php echo get_phrase('price'); ?> 
                    <span style="color: #6c757d; font-weight: 400;">(<?php echo $result['system_currency'] ?? 'USD'; ?>)</span>
                </label>
                <div class="input-with-icon">
                    <i class="fas fa-money-bill-wave icon"></i>
                    <input type="text" 
                           id="price_community" 
                           name="price_community" 
                           class="form-control-modern" 
                           value="<?php echo $school_data['price'] ?? '0.00'; ?>" 
                           placeholder="0.00"
                           <?php if (isset($settings_school['type']) && $settings_school['type'] == 'Particulier'): ?> readonly <?php endif; ?>
                           oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1')" />
                </div>
                <?php if (isset($settings_school['type']) && $settings_school['type'] == 'Particulier'): ?>
                <div class="info-box">
                    <i class="fas fa-info-circle"></i>
                    <p><?php echo get_phrase('as_you_are_a_private_individual_the_price_will_be_automatically_set_to_0'); ?></p>
                </div>
                <?php endif; ?>
            </div>

            <button type="submit" class="btn-modern btn-success-gradient w-100">
                <i class="fas fa-save"></i>
                <?php echo get_phrase('update_price'); ?>
            </button>
        </form>
    </div>
</div>

<!-- Content Card -->
<div class="class-content-card">
    <div class="class-content-header">
        <span class="class-content-title">
            <i class="mdi mdi-format-list-bulleted"></i>
            <?php echo get_phrase('class_list'); ?>
        </span>
    </div>
    <div class="class-content-body">
        <div class="class_content">
            <?php include 'list.php'; ?>
        </div>
    </div>
</div>
<script>
$(document).ready(function() {
    $('select.select2:not(.normal)').each(function () { $(this).select2({ dropdownParent: '#right-modal' }); });

    // Community Pricing Form Submission
    $(".communityPriceAjaxForm").submit(function(e) {
        e.preventDefault();
        var submitButton = $(this).find('button[type="submit"]');
        var originalHtml = submitButton.html();
        var updating_text = "<?php echo get_phrase('updating'); ?>...";
        
        submitButton.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> ' + updating_text);
        const formData = new FormData(this);

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function (response) {
                if (response.status) {
                    $('input[name="' + response.csrf.name + '"]').val(response.csrf.hash);
                    showNotification('success', '<?php echo get_phrase('updated_successfully'); ?>');
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                } else {
                    showNotification('error', '<?php echo get_phrase('action_not_allowed'); ?>');
                    submitButton.prop('disabled', false).html(originalHtml);
                }
            },
            error: function () {
                showNotification('error', '<?php echo get_phrase('an_error_occurred_during_submission'); ?>');
                submitButton.prop('disabled', false).html(originalHtml);
            }
        });
    });
});

var showAllClasses = function () {
    // Show loading state
    $('.class_content').html('<div class="class-loading"><i class="mdi mdi-loading"></i> <?php echo get_phrase('loading'); ?>...</div>');

    var url = '<?php echo route('manage_class/list'); ?>';
    $.ajax({
        type : 'GET',
        url: url,
        success : function(response) {
            $('.class_content').html(response);
            initDataTable("basic-datatable");
        },
        error: function() {
            $('.class_content').html('<div class="class-loading" style="color: #dc2626;"><i class="mdi mdi-alert-circle"></i> <?php echo get_phrase('error_loading_data'); ?></div>');
        }
    });
}

function showNotification(type, message) {
    toastr.options = {
        closeButton: true,
        progressBar: true,
        positionClass: 'toast-top-right',
        timeOut: 5000,
        showMethod: 'fadeIn',
        hideMethod: 'fadeOut',
    };
    if (type === 'success') {
        toastr.success(message);
    } else if (type === 'error') {
        toastr.error(message);
    } else if (type === 'warning') {
        toastr.warning(message);
    }
}
</script>



