<style>
.credentials-form .provider-section {
    background: #fafafa;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 20px;
}

.credentials-form .provider-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 2px solid #eee;
}

.credentials-form .provider-header .provider-icon {
    font-size: 32px;
    margin-right: 15px;
}

.credentials-form .provider-header h5 {
    margin: 0;
    font-weight: 600;
}

.credentials-form .form-control {
    border: 2px solid #e8e8e8;
    border-radius: 8px;
    padding: 10px 14px;
    font-family: 'Monaco', 'Consolas', monospace;
    font-size: 13px;
}

.credentials-form .form-control:focus {
    border-color: var(--provider-color, #667eea);
}

.credentials-form .mode-toggle {
    display: flex;
    gap: 10px;
}

.credentials-form .mode-toggle .btn {
    flex: 1;
    padding: 10px;
    font-weight: 600;
}

.credentials-form .mode-toggle .btn.active {
    background: var(--provider-color, #667eea);
    border-color: var(--provider-color, #667eea);
    color: white;
}

.key-group {
    position: relative;
}

.key-group .toggle-visibility {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: #888;
    cursor: pointer;
}

.key-group .toggle-visibility:hover {
    color: #333;
}

.status-indicator {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

.status-indicator.configured {
    background: #e8f5e9;
    color: #2e7d32;
}

.status-indicator.not-configured {
    background: #fff3e0;
    color: #ef6c00;
}
</style>

<?php
$billingEntityCredentialsModel = model('BillingEntityCredentials_model');
if (!is_object($billingEntityCredentialsModel)) {
    $billingEntityCredentialsModel = model('App\Models\BillingEntityCredentials_model');
}
$stripe_creds = (is_object($billingEntityCredentialsModel) && method_exists($billingEntityCredentialsModel, 'get_credentials'))
    ? ($billingEntityCredentialsModel->get_credentials($entity['id'], 'stripe') ?? [])
    : [];
$paypal_creds = (is_object($billingEntityCredentialsModel) && method_exists($billingEntityCredentialsModel, 'get_credentials'))
    ? ($billingEntityCredentialsModel->get_credentials($entity['id'], 'paypal') ?? [])
    : [];
?>

<div class="credentials-form">
    <!-- Entity Header -->
    <div class="alert mb-4" style="background: linear-gradient(135deg, <?php echo $entity['color_primary'] ?? '#667eea'; ?> 0%, <?php echo $entity['color_secondary'] ?? '#764ba2'; ?> 100%); color: white; border: none;">
        <div class="d-flex align-items-center">
            <span style="font-size: 32px; margin-right: 15px;"><?php echo $entity['country_flag'] ?? '🏳️'; ?></span>
            <div>
                <h5 class="m-0 text-white"><?php echo htmlspecialchars($entity['name']); ?></h5>
                <small style="opacity: 0.9;"><?php echo get_phrase('Configure payment credentials for this entity'); ?></small>
            </div>
        </div>
    </div>

    <!-- Stripe Section -->
    <div class="provider-section" style="--provider-color: #635bff;">
        <div class="provider-header">
            <div class="d-flex align-items-center">
                <i class="fab fa-cc-stripe provider-icon" style="color: #635bff;"></i>
                <div>
                    <h5>Stripe</h5>
                    <small class="text-muted"><?php echo get_phrase('Card payments'); ?></small>
                </div>
            </div>
            <div>
                <?php if (!empty($stripe_creds['test_secret_key']) || !empty($stripe_creds['live_secret_key'])): ?>
                <span class="status-indicator configured"><i class="fas fa-check-circle"></i> <?php echo get_phrase('Configured'); ?></span>
                <?php else: ?>
                <span class="status-indicator not-configured"><i class="fas fa-exclamation-circle"></i> <?php echo get_phrase('Not configured'); ?></span>
                <?php endif; ?>
            </div>
        </div>

        <form method="POST" class="stripeCredentialsForm" action="<?php echo site_url('superadmin/billing_entities/save_credentials/'.$entity['id'].'/stripe'); ?>">
            <input type="hidden" name="<?php echo csrf_token(); ?>" value="<?php echo csrf_hash(); ?>">
            
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label><?php echo get_phrase('Mode'); ?></label>
                    <div class="mode-toggle">
                        <button type="button" class="btn btn-outline-secondary <?php echo ($stripe_creds['mode'] ?? 'test') === 'test' ? 'active' : ''; ?>" onclick="setStripeMode('test')">
                            <i class="fas fa-flask mr-1"></i> Test
                        </button>
                        <button type="button" class="btn btn-outline-secondary <?php echo ($stripe_creds['mode'] ?? '') === 'live' ? 'active' : ''; ?>" onclick="setStripeMode('live')">
                            <i class="fas fa-rocket mr-1"></i> Live
                        </button>
                    </div>
                    <input type="hidden" name="mode" id="stripe_mode" value="<?php echo $stripe_creds['mode'] ?? 'test'; ?>">
                </div>
                <div class="form-group col-md-6">
                    <label><?php echo get_phrase('Currency'); ?></label>
                    <select class="form-control" name="currency">
                        <?php foreach (['MAD', 'AED', 'EUR', 'USD', 'GBP'] as $curr): ?>
                        <option value="<?php echo $curr; ?>" <?php echo ($stripe_creds['currency'] ?? '') === $curr ? 'selected' : ''; ?>><?php echo $curr; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <h6 class="text-muted mb-3"><i class="fas fa-flask mr-1"></i> <?php echo get_phrase('Test Keys'); ?></h6>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label><?php echo get_phrase('Test Public Key'); ?></label>
                    <input type="text" class="form-control" name="test_public_key" value="<?php echo htmlspecialchars($stripe_creds['test_public_key'] ?? ''); ?>" placeholder="pk_test_...">
                </div>
                <div class="form-group col-md-6">
                    <label><?php echo get_phrase('Test Secret Key'); ?></label>
                    <div class="key-group">
                        <input type="password" class="form-control" name="test_secret_key" value="<?php echo htmlspecialchars($stripe_creds['test_secret_key'] ?? ''); ?>" placeholder="sk_test_...">
                        <button type="button" class="toggle-visibility" onclick="toggleKeyVisibility(this)"><i class="fas fa-eye"></i></button>
                    </div>
                </div>
            </div>

            <h6 class="text-muted mb-3"><i class="fas fa-rocket mr-1"></i> <?php echo get_phrase('Live Keys'); ?></h6>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label><?php echo get_phrase('Live Public Key'); ?></label>
                    <input type="text" class="form-control" name="live_public_key" value="<?php echo htmlspecialchars($stripe_creds['live_public_key'] ?? ''); ?>" placeholder="pk_live_...">
                </div>
                <div class="form-group col-md-6">
                    <label><?php echo get_phrase('Live Secret Key'); ?></label>
                    <div class="key-group">
                        <input type="password" class="form-control" name="live_secret_key" value="<?php echo htmlspecialchars($stripe_creds['live_secret_key'] ?? ''); ?>" placeholder="sk_live_...">
                        <button type="button" class="toggle-visibility" onclick="toggleKeyVisibility(this)"><i class="fas fa-eye"></i></button>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label><?php echo get_phrase('Webhook Secret'); ?> <small class="text-muted">(optional)</small></label>
                <div class="key-group">
                    <input type="password" class="form-control" name="webhook_secret" value="<?php echo htmlspecialchars($stripe_creds['webhook_secret'] ?? ''); ?>" placeholder="whsec_...">
                    <button type="button" class="toggle-visibility" onclick="toggleKeyVisibility(this)"><i class="fas fa-eye"></i></button>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="stripe_active" name="is_active" value="1" <?php echo ($stripe_creds['is_active'] ?? 1) ? 'checked' : ''; ?>>
                        <label class="custom-control-label" for="stripe_active"><?php echo get_phrase('Active'); ?></label>
                    </div>
                </div>
                <div class="form-group col-md-6 text-right">
                    <button type="submit" class="btn btn-primary" style="background: #635bff; border-color: #635bff;">
                        <i class="fas fa-save mr-1"></i> <?php echo get_phrase('Save Stripe'); ?>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- PayPal Section -->
    <div class="provider-section" style="--provider-color: #003087;">
        <div class="provider-header">
            <div class="d-flex align-items-center">
                <i class="fab fa-paypal provider-icon" style="color: #003087;"></i>
                <div>
                    <h5>PayPal</h5>
                    <small class="text-muted"><?php echo get_phrase('PayPal payments'); ?></small>
                </div>
            </div>
            <div>
                <?php if (!empty($paypal_creds['sandbox_client_id']) || !empty($paypal_creds['production_client_id'])): ?>
                <span class="status-indicator configured"><i class="fas fa-check-circle"></i> <?php echo get_phrase('Configured'); ?></span>
                <?php else: ?>
                <span class="status-indicator not-configured"><i class="fas fa-exclamation-circle"></i> <?php echo get_phrase('Not configured'); ?></span>
                <?php endif; ?>
            </div>
        </div>

        <form method="POST" class="paypalCredentialsForm" action="<?php echo site_url('superadmin/billing_entities/save_credentials/'.$entity['id'].'/paypal'); ?>">
            <input type="hidden" name="<?php echo csrf_token(); ?>" value="<?php echo csrf_hash(); ?>">
            
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label><?php echo get_phrase('Mode'); ?></label>
                    <div class="mode-toggle">
                        <button type="button" class="btn btn-outline-secondary <?php echo ($paypal_creds['mode'] ?? 'sandbox') === 'sandbox' ? 'active' : ''; ?>" onclick="setPaypalMode('sandbox')">
                            <i class="fas fa-flask mr-1"></i> Sandbox
                        </button>
                        <button type="button" class="btn btn-outline-secondary <?php echo ($paypal_creds['mode'] ?? '') === 'production' ? 'active' : ''; ?>" onclick="setPaypalMode('production')">
                            <i class="fas fa-rocket mr-1"></i> Production
                        </button>
                    </div>
                    <input type="hidden" name="mode" id="paypal_mode" value="<?php echo $paypal_creds['mode'] ?? 'sandbox'; ?>">
                </div>
                <div class="form-group col-md-6">
                    <label><?php echo get_phrase('Currency'); ?></label>
                    <select class="form-control" name="currency">
                        <?php foreach (['EUR', 'USD', 'GBP', 'CAD', 'AUD'] as $curr): ?>
                        <option value="<?php echo $curr; ?>" <?php echo ($paypal_creds['currency'] ?? '') === $curr ? 'selected' : ''; ?>><?php echo $curr; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <h6 class="text-muted mb-3"><i class="fas fa-flask mr-1"></i> <?php echo get_phrase('Sandbox'); ?></h6>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label><?php echo get_phrase('Sandbox Client ID'); ?></label>
                    <input type="text" class="form-control" name="sandbox_client_id" value="<?php echo htmlspecialchars($paypal_creds['sandbox_client_id'] ?? ''); ?>" placeholder="AZ...">
                </div>
                <div class="form-group col-md-6">
                    <label><?php echo get_phrase('Sandbox Secret'); ?></label>
                    <div class="key-group">
                        <input type="password" class="form-control" name="sandbox_secret" value="<?php echo htmlspecialchars($paypal_creds['sandbox_secret'] ?? ''); ?>" placeholder="EL...">
                        <button type="button" class="toggle-visibility" onclick="toggleKeyVisibility(this)"><i class="fas fa-eye"></i></button>
                    </div>
                </div>
            </div>

            <h6 class="text-muted mb-3"><i class="fas fa-rocket mr-1"></i> <?php echo get_phrase('Production'); ?></h6>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label><?php echo get_phrase('Production Client ID'); ?></label>
                    <input type="text" class="form-control" name="production_client_id" value="<?php echo htmlspecialchars($paypal_creds['production_client_id'] ?? ''); ?>" placeholder="AZ...">
                </div>
                <div class="form-group col-md-6">
                    <label><?php echo get_phrase('Production Secret'); ?></label>
                    <div class="key-group">
                        <input type="password" class="form-control" name="production_secret" value="<?php echo htmlspecialchars($paypal_creds['production_secret'] ?? ''); ?>" placeholder="EL...">
                        <button type="button" class="toggle-visibility" onclick="toggleKeyVisibility(this)"><i class="fas fa-eye"></i></button>
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="paypal_active" name="is_active" value="1" <?php echo ($paypal_creds['is_active'] ?? 1) ? 'checked' : ''; ?>>
                        <label class="custom-control-label" for="paypal_active"><?php echo get_phrase('Active'); ?></label>
                    </div>
                </div>
                <div class="form-group col-md-6 text-right">
                    <button type="submit" class="btn btn-primary" style="background: #003087; border-color: #003087;">
                        <i class="fas fa-save mr-1"></i> <?php echo get_phrase('Save PayPal'); ?>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function setStripeMode(mode) {
    $('#stripe_mode').val(mode);
    $('.provider-section:first .mode-toggle .btn').removeClass('active');
    $('.provider-section:first .mode-toggle .btn:contains(' + (mode === 'test' ? 'Test' : 'Live') + ')').addClass('active');
}

function setPaypalMode(mode) {
    $('#paypal_mode').val(mode);
    $('.provider-section:last .mode-toggle .btn').removeClass('active');
    $('.provider-section:last .mode-toggle .btn:contains(' + (mode === 'sandbox' ? 'Sandbox' : 'Production') + ')').addClass('active');
}

function toggleKeyVisibility(btn) {
    var input = $(btn).siblings('input');
    if (input.attr('type') === 'password') {
        input.attr('type', 'text');
        $(btn).find('i').removeClass('fa-eye').addClass('fa-eye-slash');
    } else {
        input.attr('type', 'password');
        $(btn).find('i').removeClass('fa-eye-slash').addClass('fa-eye');
    }
}

// Form submissions
$('.stripeCredentialsForm, .paypalCredentialsForm').on('submit', function(e) {
    e.preventDefault();
    var form = $(this);
    var btn = form.find('button[type="submit"]');
    var originalHtml = btn.html();
    
    btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Saving...');
    
    $.ajax({
        type: 'POST',
        url: form.attr('action'),
        data: form.serialize(),
        success: function(response) {
            var $data = JSON.parse(response);
            if (data.status) {
                toastr.success(data.notification);
            } else {
                toastr.error(data.notification);
            }
            btn.prop('disabled', false).html(originalHtml);
        },
        error: function() {
            toastr.error('An error occurred');
            btn.prop('disabled', false).html(originalHtml);
        }
    });
});
</script>

