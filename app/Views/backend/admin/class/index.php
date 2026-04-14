<link rel="stylesheet" href="<?php echo base_url(); ?>assets/backend/css/responsive.min.css">

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
}

.class-btn-primary {
    background: linear-gradient(135deg, var(--class-primary), #8b5cf6);
    color: white;
    border: 1px solid rgba(255, 255, 255, 0.25);
    backdrop-filter: blur(10px);
}

.class-btn-primary::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    transition: left 0.6s ease-in-out;
    z-index: 1;
}

.class-btn-primary::after {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, transparent 100%);
    opacity: 0;
    transition: opacity 0.3s ease;
    z-index: 0;
}

.class-btn-primary span,
.class-btn-primary i {
    position: relative;
    z-index: 2;
}

.class-btn-primary i {
    font-size: 1.1rem;
    transition: transform 0.3s ease;
}

.class-btn-primary:hover {
    transform: translateY(-3px) scale(1.02);
    box-shadow: 
        0 12px 30px rgba(99, 102, 241, 0.6),
        0 4px 15px rgba(139, 92, 246, 0.4),
        inset 0 1px 0 rgba(255, 255, 255, 0.2);
    background: linear-gradient(135deg, #8b5cf6, var(--class-primary));
    color: white;
}

.class-btn-primary:hover::before {
    left: 100%;
}

.class-btn-primary:hover::after {
    opacity: 1;
}

.class-btn-primary:hover i {
    transform: scale(1.15);
}

.class-btn-primary:active {
    transform: translateY(-1px) scale(0.99);
    box-shadow: 
        0 6px 20px rgba(99, 102, 241, 0.5),
        0 2px 8px rgba(139, 92, 246, 0.3);
}

.class-btn-primary:focus {
    outline: none;
    box-shadow: 
        0 0 0 3px rgba(99, 102, 241, 0.3),
        0 12px 30px rgba(99, 102, 241, 0.6);
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
    background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
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
    border-color: #6366f1;
    background: #fff;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
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
    background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
    color: #fff;
}

.btn-modern.btn-success-gradient:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(99, 102, 241, 0.4);
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

/* ============================================================================
   COMMUNITY STATE TOGGLE
   ============================================================================ */

.community-state-section {
    margin-bottom: 24px;
    padding-bottom: 24px;
    border-bottom: 1.5px solid #f0f0f0;
}

.community-state-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 14px;
}

.community-state-info {
    display: flex;
    align-items: center;
    gap: 12px;
}

.community-state-info > i {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    color: #fff;
    background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
    transition: all 0.4s ease;
}

.community-state-info > i.fa-lock {
    background: linear-gradient(135deg, #6b7280 0%, #9ca3af 100%);
}

.community-state-label {
    display: block;
    font-weight: 600;
    color: #344054;
    font-size: 14px;
}

.community-state-value {
    display: block;
    font-size: 12px;
    color: #6c757d;
    margin-top: 2px;
    transition: all 0.3s ease;
}

/* Toggle Switch */
.community-toggle-wrapper {
    flex-shrink: 0;
}

.community-toggle {
    position: relative;
    display: inline-block;
    width: 52px;
    height: 28px;
    cursor: pointer;
}

.community-toggle input {
    opacity: 0;
    width: 0;
    height: 0;
}

.community-toggle-slider {
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    background: #d1d5db;
    border-radius: 28px;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: inset 0 1px 3px rgba(0,0,0,0.1);
}

.community-toggle-slider::before {
    content: '';
    position: absolute;
    width: 22px;
    height: 22px;
    left: 3px;
    bottom: 3px;
    background: white;
    border-radius: 50%;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 2px 6px rgba(0,0,0,0.15);
}

.community-toggle input:checked + .community-toggle-slider {
    background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
}

.community-toggle input:checked + .community-toggle-slider::before {
    transform: translateX(24px);
}

.community-toggle:hover .community-toggle-slider {
    box-shadow: inset 0 1px 3px rgba(0,0,0,0.1), 0 0 0 3px rgba(99, 102, 241, 0.15);
}

/* State Badge */
.community-state-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    border-radius: 10px;
    font-size: 13px;
    font-weight: 500;
    transition: all 0.4s ease;
}

.community-state-badge.badge-public {
    background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
    color: #065f46;
    border: 1px solid #a7f3d0;
}

.community-state-badge.badge-private {
    background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
    color: #4b5563;
    border: 1px solid #d1d5db;
}

/* Disabled Price Field */
.form-control-modern.field-disabled {
    background: #f3f4f6 !important;
    color: #9ca3af !important;
    cursor: not-allowed !important;
    border-color: #e5e7eb !important;
    opacity: 0.7;
}

.btn-modern:disabled {
    opacity: 0.5;
    cursor: not-allowed;
    transform: none !important;
    box-shadow: none !important;
}

.info-box-private {
    background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
    border: 1px solid #fecaca;
}

.info-box-private i {
    color: #991b1b;
}

.info-box-private p {
    color: #991b1b;
}

/* Toggle loading state */
.community-toggle.loading {
    pointer-events: none;
    opacity: 0.6;
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
$result = db()->table('settings_school')->where('school_id', school_id())->get()->getResultArray();
?>

<!-- Community Pricing Card -->
<?php $is_private = (isset($school_data['access']) && $school_data['access'] == 1); ?>
<div class="pricing-card">
    <div class="pricing-card-header">
        <div class="icon-box">
            <i class="fas fa-tag"></i>
        </div>
        <div style="flex: 1;">
            <h5><?php echo get_phrase('community_pricing'); ?></h5>
            <small><?php echo get_phrase('set_the_subscription_price_for_your_community'); ?></small>
        </div>
    </div>
    <div class="pricing-card-body">

        <!-- Community State Toggle -->
        <div class="community-state-section">
            <div class="community-state-header">
                <div class="community-state-info">
                    <i class="fas <?php echo $is_private ? 'fa-lock' : 'fa-globe'; ?>" id="etat-icon"></i>
                    <div>
                        <span class="community-state-label"><?php echo get_phrase('community_visibility'); ?></span>
                        <span class="community-state-value" id="etat-status-text">
                            <?php echo $is_private ? get_phrase('private_community') : get_phrase('public_community'); ?>
                        </span>
                    </div>
                </div>
                <div class="community-toggle-wrapper">
                    <label class="community-toggle" title="<?php echo get_phrase('toggle_community_visibility'); ?>">
                        <input type="checkbox" id="etat-toggle" <?php echo !$is_private ? 'checked' : ''; ?>>
                        <span class="community-toggle-slider"></span>
                    </label>
                </div>
            </div>
            <div class="community-state-badge <?php echo $is_private ? 'badge-private' : 'badge-public'; ?>" id="etat-badge">
                <i class="fas <?php echo $is_private ? 'fa-shield-alt' : 'fa-users'; ?>"></i>
                <span id="etat-badge-text">
                    <?php echo $is_private 
                        ? get_phrase('private_community_-_join_request_only') 
                        : get_phrase('public_community_-_open_access'); ?>
                </span>
            </div>
        </div>

        <!-- Price Form -->
        <form method="POST" class="communityPriceAjaxForm" action="<?php echo route('payment_settings/price'); ?>" id="community_price_settings">
            <input type="hidden" name="<?=csrf_token();?>" value="<?=csrf_hash();?>" />
            
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
                           class="form-control-modern <?php echo $is_private ? 'field-disabled' : ''; ?>" 
                           value="<?php echo $is_private ? '0.00' : ($school_data['price'] ?? '0.00'); ?>" 
                           placeholder="0.00"
                           <?php if ($is_private || (isset($settings_school['type']) && $settings_school['type'] == 'Particulier')): ?> readonly <?php endif; ?>
                           oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1')" />
                </div>
                <?php if (isset($settings_school['type']) && $settings_school['type'] == 'Particulier'): ?>
                <div class="info-box">
                    <i class="fas fa-info-circle"></i>
                    <p><?php echo get_phrase('as_you_are_a_private_individual_the_price_will_be_automatically_set_to_0'); ?></p>
                </div>
                <?php endif; ?>
                <div class="info-box info-box-private" id="private-price-info" style="<?php echo $is_private ? '' : 'display:none;'; ?>">
                    <i class="fas fa-info-circle"></i>
                    <p><?php echo get_phrase('you_cannot_monetize_a_private_community'); ?></p>
                </div>
            </div>

            <button type="submit" class="btn-modern btn-success-gradient w-100" id="btn-update-price" <?php echo $is_private ? 'disabled' : ''; ?>>
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

    // Community Etat Toggle (Public/Private)
    $('#etat-toggle').on('change', function() {
        var toggle = $(this);
        var isPublic = toggle.is(':checked') ? 1 : 0;
        var toggleLabel = toggle.closest('.community-toggle');
        
        toggleLabel.addClass('loading');
        
        $.ajax({
            url: '<?php echo route('payment_settings/toggle_etat'); ?>',
            type: 'POST',
            data: {
                '<?=csrf_token();?>': $('input[name="<?=csrf_token();?>"]').val(),
                'etat': isPublic
            },
            dataType: 'json',
            success: function(response) {
                toggleLabel.removeClass('loading');
                if (response.status) {
                    // Update CSRF token
                    $('input[name="' + response.csrf.name + '"]').val(response.csrf.hash);
                    
                    var priceInput = $('#price_community');
                    var priceInfo = $('#private-price-info');
                    var btnUpdate = $('#btn-update-price');
                    var etatIcon = $('#etat-icon');
                    var etatText = $('#etat-status-text');
                    var etatBadge = $('#etat-badge');
                    var etatBadgeText = $('#etat-badge-text');
                    
                    if (isPublic) {
                        // Public state
                        priceInput.removeClass('field-disabled').prop('readonly', false);
                        priceInfo.slideUp(300);
                        btnUpdate.prop('disabled', false);
                        etatIcon.removeClass('fa-lock').addClass('fa-globe');
                        etatText.text('<?php echo get_phrase('public_community'); ?>');
                        etatBadge.removeClass('badge-private').addClass('badge-public');
                        etatBadgeText.html('<?php echo get_phrase('public_community_-_open_access'); ?>');
                        etatBadge.find('i:first').removeClass('fa-shield-alt').addClass('fa-users');
                    } else {
                        // Private state
                        priceInput.addClass('field-disabled').prop('readonly', true).val('0.00');
                        priceInfo.slideDown(300);
                        btnUpdate.prop('disabled', true);
                        etatIcon.removeClass('fa-globe').addClass('fa-lock');
                        etatText.text('<?php echo get_phrase('private_community'); ?>');
                        etatBadge.removeClass('badge-public').addClass('badge-private');
                        etatBadgeText.html('<?php echo get_phrase('private_community_-_join_request_only'); ?>');
                        etatBadge.find('i:first').removeClass('fa-users').addClass('fa-shield-alt');
                    }
                } else {
                    // Revert toggle on failure
                    toggle.prop('checked', !toggle.is(':checked'));
                }
            },
            error: function() {
                toggleLabel.removeClass('loading');
                toggle.prop('checked', !toggle.is(':checked'));
            }
        });
    });

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



