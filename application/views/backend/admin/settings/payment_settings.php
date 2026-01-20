<style>
/* Premium Payment Settings Design */
.payment-settings-container {
    padding: 0;
}

.settings-header {
    background: linear-gradient(135deg, #1e3c72 0%, #2a5298 50%, #1e3c72 100%);
    border-radius: 16px;
    padding: 30px;
    margin-bottom: 30px;
    position: relative;
    overflow: hidden;
}

.settings-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 100%;
    height: 200%;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 60%);
    pointer-events: none;
}

.settings-header h2 {
    color: #fff;
    font-size: 28px;
    font-weight: 700;
    margin: 0 0 8px 0;
    display: flex;
    align-items: center;
    gap: 12px;
}

.settings-header p {
    color: rgba(255,255,255,0.85);
    margin: 0;
    font-size: 15px;
}

.settings-header .header-icon {
    width: 50px;
    height: 50px;
    background: rgba(255,255,255,0.15);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
}

/* Quick Stats */
.quick-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: #fff;
    border-radius: 12px;
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 15px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.06);
    border: 1px solid rgba(0,0,0,0.05);
    transition: all 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
}

.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    color: #fff;
}

.stat-icon.currency { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
.stat-icon.vat { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
.stat-icon.price { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
.stat-icon.status { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); }

.stat-content h4 {
    margin: 0;
    font-size: 20px;
    font-weight: 700;
    color: #1a1a2e;
}

.stat-content p {
    margin: 0;
    font-size: 13px;
    color: #6c757d;
}

/* Settings Cards */
.settings-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 24px;
    margin-bottom: 30px;
}

@media (max-width: 1200px) {
    .settings-grid { grid-template-columns: 1fr; }
}

.settings-card {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.06);
    border: 1px solid rgba(0,0,0,0.05);
    overflow: hidden;
    transition: all 0.3s ease;
}

.settings-card:hover {
    box-shadow: 0 8px 30px rgba(0,0,0,0.1);
}

.settings-card.full-width {
    grid-column: 1 / -1;
}

.card-header-custom {
    padding: 20px 24px;
    border-bottom: 1px solid #f0f0f0;
    display: flex;
    align-items: center;
    gap: 14px;
}

.card-header-custom .icon-box {
    width: 44px;
    height: 44px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    color: #fff;
}

.card-header-custom .icon-box.pricing { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); }
.card-header-custom .icon-box.vat { background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%); }
.card-header-custom .icon-box.currency { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
.card-header-custom .icon-box.paypal { background: linear-gradient(135deg, #003087 0%, #009cde 100%); }
.card-header-custom .icon-box.stripe { background: linear-gradient(135deg, #635bff 0%, #a855f7 100%); }

.card-header-custom h5 {
    margin: 0;
    font-size: 17px;
    font-weight: 600;
    color: #1a1a2e;
}

.card-header-custom small {
    display: block;
    color: #6c757d;
    font-size: 12px;
    margin-top: 2px;
}

.card-body-custom {
    padding: 24px;
}

/* Form Styling */
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

.form-group-modern label .required {
    color: #ef4444;
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

/* Input with Icon */
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

/* Toggle Switch */
.toggle-wrapper {
    display: flex;
    align-items: center;
    gap: 12px;
}

.toggle-switch {
    position: relative;
    width: 52px;
    height: 28px;
}

.toggle-switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.toggle-slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #e5e7eb;
    transition: 0.3s;
    border-radius: 28px;
}

.toggle-slider:before {
    position: absolute;
    content: "";
    height: 22px;
    width: 22px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    transition: 0.3s;
    border-radius: 50%;
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
}

.toggle-switch input:checked + .toggle-slider {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.toggle-switch input:checked + .toggle-slider:before {
    transform: translateX(24px);
}

.toggle-label {
    font-size: 14px;
    color: #374151;
    font-weight: 500;
}

/* Buttons */
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

.btn-modern.btn-primary-gradient {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: #fff;
}

.btn-modern.btn-primary-gradient:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
}

.btn-modern.btn-success-gradient {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    color: #fff;
}

.btn-modern.btn-paypal {
    background: linear-gradient(135deg, #003087 0%, #009cde 100%);
    color: #fff;
}

.btn-modern.btn-stripe {
    background: linear-gradient(135deg, #635bff 0%, #a855f7 100%);
    color: #fff;
}

/* Info Box */
.info-box {
    background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%);
    border-radius: 10px;
    padding: 14px 18px;
    display: flex;
    align-items: flex-start;
    gap: 12px;
    margin-top: 16px;
}

.info-box i {
    color: #4f46e5;
    font-size: 18px;
    margin-top: 2px;
}

.info-box p {
    margin: 0;
    font-size: 13px;
    color: #3730a3;
    line-height: 1.5;
}

/* Mode Badge */
.mode-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

.mode-badge.sandbox {
    background: #fef3c7;
    color: #92400e;
}

.mode-badge.production {
    background: #d1fae5;
    color: #065f46;
}

.mode-badge.test {
    background: #fee2e2;
    color: #991b1b;
}

/* Key Input */
.key-input-wrapper {
    position: relative;
}

.key-input-wrapper .toggle-visibility {
    position: absolute;
    right: 14px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: #9ca3af;
    cursor: pointer;
    padding: 4px;
    transition: color 0.2s;
}

.key-input-wrapper .toggle-visibility:hover {
    color: #667eea;
}

.key-input-wrapper .form-control-modern {
    padding-right: 45px;
    font-family: 'Courier New', monospace;
    font-size: 13px;
}

/* Payment Method Card Preview */
.payment-preview {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 16px;
    background: #f9fafb;
    border-radius: 10px;
    margin-bottom: 20px;
}

.payment-preview img {
    height: 32px;
    object-fit: contain;
}

.payment-preview .status {
    margin-left: auto;
}

.status-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    display: inline-block;
    margin-right: 6px;
}

.status-dot.active { background: #10b981; }
.status-dot.inactive { background: #ef4444; }

/* Animations */
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

.settings-card {
    animation: fadeInUp 0.5s ease forwards;
}

.settings-card:nth-child(1) { animation-delay: 0.1s; }
.settings-card:nth-child(2) { animation-delay: 0.2s; }
.settings-card:nth-child(3) { animation-delay: 0.3s; }
.settings-card:nth-child(4) { animation-delay: 0.4s; }
.settings-card:nth-child(5) { animation-delay: 0.5s; }
</style>

<?php
$paypal = json_decode(get_payment_settings('paypal_settings', school_id()));
$stripe = json_decode(get_payment_settings('stripe_settings', school_id()));
$school_data = $this->settings_model->get_current_school_data();
$settings_school = $this->settings_model->get_current_settings_school_data();
$result = $this->db->get_where('settings_school', array('school_id' => school_id()))->row_array();

// Status checks
$paypal_active = isset($paypal[0]->paypal_active) && $paypal[0]->paypal_active == 'yes';
$stripe_active = isset($stripe[0]->stripe_active) && $stripe[0]->stripe_active == 'yes';
$vat_enabled = isset($result['vat']) && $result['vat'] == 1;
?>

<div class="payment-settings-container">
    <!-- Header -->
    <div class="settings-header">
        <div class="d-flex align-items-center gap-3">
            <div class="header-icon">
                <i class="fas fa-cog"></i>
            </div>
            <div>
                <h2><?php echo get_phrase('Payment Settings'); ?></h2>
                <p><?php echo get_phrase('Configure your payment methods, currency and tax settings'); ?></p>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="quick-stats">
        <div class="stat-card">
            <div class="stat-icon currency">
                <i class="fas fa-coins"></i>
            </div>
            <div class="stat-content">
                <h4><?php echo $result['system_currency'] ?? 'USD'; ?></h4>
                <p><?php echo get_phrase('System Currency'); ?></p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon vat">
                <i class="fas fa-percent"></i>
            </div>
            <div class="stat-content">
                <?php 
                // Utiliser country depuis school_data (source unique de vérité)
                $tax_country = $school_data['country'] ?? '';
                ?>
                <h4><?php echo $vat_enabled ? ($tax_country == 'MA' ? '20%' : (in_array($tax_country, ['UAE', 'AE']) ? '5%' : '--')) : get_phrase('Disabled'); ?></h4>
                <p><?php echo get_phrase('VAT Rate'); ?></p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon price">
                <i class="fas fa-tag"></i>
            </div>
            <div class="stat-content">
                <h4><?php echo number_format($school_data['price'] ?? 0, 2); ?> <?php echo $result['system_currency'] ?? ''; ?></h4>
                <p><?php echo get_phrase('Community Price'); ?></p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon status">
                <i class="fas fa-credit-card"></i>
            </div>
            <div class="stat-content">
                <h4><?php echo ($stripe_active ? 1 : 0) + ($paypal_active ? 1 : 0); ?> <?php echo get_phrase('Active'); ?></h4>
                <p><?php echo get_phrase('Payment Methods'); ?></p>
            </div>
        </div>
    </div>

    <!-- Settings Grid -->
    <div class="settings-grid">
        
        <!-- Community Pricing -->
        <div class="settings-card">
            <div class="card-header-custom">
                <div class="icon-box pricing">
                    <i class="fas fa-tag"></i>
                </div>
                <div>
                    <h5><?php echo get_phrase('Community Pricing'); ?></h5>
                    <small><?php echo get_phrase('Set the subscription price for your community'); ?></small>
                </div>
            </div>
            <div class="card-body-custom">
                <form method="POST" class="systempriceAjaxForm" action="<?php echo route('payment_settings/price'); ?>" id="price_settings">
                    <input type="hidden" name="<?=$this->security->get_csrf_token_name();?>" value="<?=$this->security->get_csrf_hash();?>" />
                    
                    <div class="form-group-modern">
                        <label for="price_community">
                            <?php echo get_phrase('Price'); ?> 
                            <span style="color: #6c757d; font-weight: 400;">(<?php echo $result['system_currency'] ?? 'USD'; ?>)</span>
                        </label>
                        <div class="input-with-icon">
                            <i class="fas fa-money-bill-wave icon"></i>
                            <input type="text" 
                                   id="price_community" 
                                   name="price_community" 
                                   class="form-control-modern" 
                                   value="<?php echo $school_data['price']; ?>" 
                                   placeholder="0.00"
                                   <?php if ($settings_school['type'] == 'Particulier'): ?> readonly <?php endif; ?>
                                   oninput="checkPriceForParticulier(this)" />
                        </div>
                        <?php if ($settings_school['type'] == 'Particulier'): ?>
                        <div class="info-box" style="background: #fef3c7; margin-top: 12px;">
                            <i class="fas fa-info-circle" style="color: #92400e;"></i>
                            <p style="color: #92400e;"><?php echo get_phrase('as_you_are_a_private_individual_the_price_will_be_automatically_set_to_0'); ?></p>
                        </div>
                        <?php endif; ?>
                    </div>

                    <button type="submit" class="btn-modern btn-success-gradient w-100">
                        <i class="fas fa-save"></i>
                        <?php echo get_phrase('Update Price'); ?>
                    </button>
                </form>
            </div>
        </div>

        <!-- VAT Settings -->
        <div class="settings-card">
            <div class="card-header-custom">
                <div class="icon-box vat">
                    <i class="fas fa-percent"></i>
                </div>
                <div>
                    <h5><?php echo get_phrase('VAT Settings'); ?></h5>
                    <small><?php echo get_phrase('Configure tax settings for your community'); ?></small>
                </div>
            </div>
            <div class="card-body-custom">
                <form method="POST" class="systemvatAjaxForm" action="<?php echo route('payment_settings/vat'); ?>" id="vat_settings">
                    <input type="hidden" name="<?=$this->security->get_csrf_token_name();?>" value="<?=$this->security->get_csrf_hash();?>" />
                    <input type="hidden" name="tax_residence" id="tax_residence" value="<?php echo $school_data['country'] ?? ''; ?>">

                    <div class="form-group-modern">
                        <label><?php echo get_phrase('VAT Applicable'); ?></label>
                        <select class="form-control-modern" name="vat_applicable" id="vat_applicable">
                            <option value=""><?php echo get_phrase('Select'); ?>...</option>
                            <option value="1" <?php if ($result['vat_enabled'] == 1) echo 'selected'; ?>><?php echo get_phrase('Yes'); ?></option>
                            <option value="0" <?php if ($result['vat_enabled'] == 0) echo 'selected'; ?>><?php echo get_phrase('No'); ?></option>
                        </select>
                    </div>

                    <div class="form-group-modern">
                        <label><?php echo get_phrase('VAT Rate'); ?></label>
                        <div class="input-with-icon">
                            <i class="fas fa-percentage icon"></i>
                            <input type="text" id="vat_rate" name="vat_rate" class="form-control-modern" readonly value="<?php echo $result['vat_rate']; ?>" placeholder="--" />
                        </div>
                    </div>

                    <div class="info-box">
                        <i class="fas fa-info-circle"></i>
                        <p><?php echo get_phrase('wayo_is_not_responsible_for_the_choice,_consult_an_accountant_to_define_if_your_entity_is_subject_to_VAT.'); ?></p>
                    </div>

                    <button type="submit" class="btn-modern btn-primary-gradient w-100 mt-3">
                        <i class="fas fa-save"></i>
                        <?php echo get_phrase('Update VAT'); ?>
                    </button>
                </form>
            </div>
        </div>

        <!-- System Currency -->
        <div class="settings-card full-width">
            <div class="card-header-custom">
                <div class="icon-box currency">
                    <i class="fas fa-coins"></i>
                </div>
                <div>
                    <h5><?php echo get_phrase('System Currency'); ?></h5>
                    <small><?php echo get_phrase('Set default currency for your community'); ?></small>
                </div>
            </div>
            <div class="card-body-custom">
                <form method="POST" class="systemAjaxForm" action="<?php echo route('payment_settings/system'); ?>" id="system_settings">
                    <input type="hidden" name="<?=$this->security->get_csrf_token_name();?>" value="<?=$this->security->get_csrf_hash();?>" />

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group-modern">
                                <label for="system_currency"><?php echo get_phrase('Currency'); ?> <span class="required">*</span></label>
                                <select class="form-control-modern" id="system_currency" name="system_currency" required>
                                    <option value=""><?php echo get_phrase('Select Currency'); ?></option>
                                    <?php
                                    $currencies = $this->settings_model->get_currencies();
                                    foreach ($currencies as $currency): ?>
                                        <option value="<?php echo $currency['code']; ?>" <?php if ($result['system_currency'] == $currency['code']) echo 'selected'; ?>>
                                            <?php echo $currency['code']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group-modern">
                                <label for="currency_position"><?php echo get_phrase('Currency Position'); ?> <span class="required">*</span></label>
                                <select class="form-control-modern" id="currency_position" name="currency_position" required>
                                    <option value="left" <?php if ($result['currency_position'] == 'left') echo 'selected'; ?>><?php echo get_phrase('Left'); ?> ($100)</option>
                                    <option value="right" <?php if ($result['currency_position'] == 'right') echo 'selected'; ?>><?php echo get_phrase('Right'); ?> (100$)</option>
                                    <option value="left-space" <?php if ($result['currency_position'] == 'left-space') echo 'selected'; ?>><?php echo get_phrase('Left with space'); ?> ($ 100)</option>
                                    <option value="right-space" <?php if ($result['currency_position'] == 'right-space') echo 'selected'; ?>><?php echo get_phrase('Right with space'); ?> (100 $)</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn-modern btn-primary-gradient">
                        <i class="fas fa-save"></i>
                        <?php echo get_phrase('Update Currency'); ?>
                    </button>
                </form>
            </div>
        </div>

        <!-- Stripe Settings -->
        <div class="settings-card">
            <div class="card-header-custom">
                <div class="icon-box stripe">
                    <i class="fab fa-cc-stripe"></i>
                </div>
                <div>
                    <h5>Stripe</h5>
                    <small><?php echo get_phrase('Accept card payments worldwide'); ?></small>
                </div>
                <div class="ml-auto">
                    <span class="mode-badge <?php echo ($stripe[0]->stripe_mode ?? 'on') == 'on' ? 'test' : 'production'; ?>">
                        <i class="fas fa-<?php echo ($stripe[0]->stripe_mode ?? 'on') == 'on' ? 'flask' : 'check-circle'; ?>"></i>
                        <?php echo ($stripe[0]->stripe_mode ?? 'on') == 'on' ? 'Test Mode' : 'Live'; ?>
                    </span>
                </div>
            </div>
            <div class="card-body-custom">
                <!-- Preview -->
                <div class="payment-preview">
                    <img src="<?php echo base_url('assets/backend/images/payments/stripe.png'); ?>" alt="Stripe">
                    <div>
                        <strong>Stripe Payments</strong>
                        <div style="font-size: 12px; color: #6c757d;"><?php echo $stripe[0]->stripe_currency ?? 'USD'; ?></div>
                    </div>
                    <div class="status">
                        <span class="status-dot <?php echo $stripe_active ? 'active' : 'inactive'; ?>"></span>
                        <?php echo $stripe_active ? get_phrase('Active') : get_phrase('Inactive'); ?>
                    </div>
                </div>

                <form method="POST" class="stripeAjaxForm" action="<?php echo route('payment_settings/stripe'); ?>" id="stripe_settings">
                    <input type="hidden" name="<?=$this->security->get_csrf_token_name();?>" value="<?=$this->security->get_csrf_hash();?>" />

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group-modern">
                                <label><?php echo get_phrase('Status'); ?></label>
                                <select class="form-control-modern" name="stripe_active" id="stripe_active">
                                    <option value="yes" <?php if ($stripe[0]->stripe_active == 'yes') echo 'selected'; ?>><?php echo get_phrase('Active'); ?></option>
                                    <option value="no" <?php if ($stripe[0]->stripe_active == 'no') echo 'selected'; ?>><?php echo get_phrase('Inactive'); ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group-modern">
                                <label><?php echo get_phrase('Currency'); ?> <span class="required">*</span></label>
                                <select class="form-control-modern" id="stripe_currency" name="stripe_currency" required>
                                    <?php
                                    $currencies = $this->settings_model->get_stripe_supported_currencies();
                                    foreach ($currencies as $currency): ?>
                                        <option value="<?php echo $currency['code']; ?>" <?php if ($stripe[0]->stripe_currency == $currency['code']) echo 'selected'; ?>>
                                            <?php echo $currency['code']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group-modern">
                        <label><?php echo get_phrase('Mode'); ?></label>
                        <select class="form-control-modern" name="stripe_mode" id="stripe_mode">
                            <option value="on" <?php if ($stripe[0]->stripe_mode == 'on') echo 'selected'; ?>><?php echo get_phrase('Test Mode'); ?></option>
                            <option value="off" <?php if ($stripe[0]->stripe_mode == 'off') echo 'selected'; ?>><?php echo get_phrase('Live Mode'); ?></option>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group-modern">
                                <label><?php echo get_phrase('Test Secret Key'); ?> <span class="required">*</span></label>
                                <div class="key-input-wrapper">
                                    <input type="password" name="stripe_test_secret_key" class="form-control-modern" value="<?php echo $stripe[0]->stripe_test_secret_key; ?>" required>
                                    <button type="button" class="toggle-visibility" onclick="toggleKeyVisibility(this)">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group-modern">
                                <label><?php echo get_phrase('Test Public Key'); ?> <span class="required">*</span></label>
                                <div class="key-input-wrapper">
                                    <input type="password" name="stripe_test_public_key" class="form-control-modern" value="<?php echo $stripe[0]->stripe_test_public_key; ?>" required>
                                    <button type="button" class="toggle-visibility" onclick="toggleKeyVisibility(this)">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group-modern">
                                <label><?php echo get_phrase('Live Secret Key'); ?> <span class="required">*</span></label>
                                <div class="key-input-wrapper">
                                    <input type="password" name="stripe_live_secret_key" class="form-control-modern" value="<?php echo $stripe[0]->stripe_live_secret_key; ?>" required>
                                    <button type="button" class="toggle-visibility" onclick="toggleKeyVisibility(this)">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group-modern">
                                <label><?php echo get_phrase('Live Public Key'); ?> <span class="required">*</span></label>
                                <div class="key-input-wrapper">
                                    <input type="password" name="stripe_live_public_key" class="form-control-modern" value="<?php echo $stripe[0]->stripe_live_public_key; ?>" required>
                                    <button type="button" class="toggle-visibility" onclick="toggleKeyVisibility(this)">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn-modern btn-stripe w-100">
                        <i class="fab fa-cc-stripe"></i>
                        <?php echo get_phrase('Update Stripe'); ?>
                    </button>
                </form>
            </div>
        </div>

        <!-- PayPal Settings -->
        <div class="settings-card">
            <div class="card-header-custom">
                <div class="icon-box paypal">
                    <i class="fab fa-paypal"></i>
                </div>
                <div>
                    <h5>PayPal</h5>
                    <small><?php echo get_phrase('Accept PayPal payments'); ?></small>
                </div>
                <div class="ml-auto">
                    <span class="mode-badge <?php echo ($paypal[0]->paypal_mode ?? 'sandbox') == 'sandbox' ? 'sandbox' : 'production'; ?>">
                        <i class="fas fa-<?php echo ($paypal[0]->paypal_mode ?? 'sandbox') == 'sandbox' ? 'flask' : 'check-circle'; ?>"></i>
                        <?php echo ($paypal[0]->paypal_mode ?? 'sandbox') == 'sandbox' ? 'Sandbox' : 'Production'; ?>
                    </span>
                </div>
            </div>
            <div class="card-body-custom">
                <!-- Preview -->
                <div class="payment-preview">
                    <img src="<?php echo base_url('assets/backend/images/payments/Paypal1.png'); ?>" alt="PayPal">
                    <div>
                        <strong>PayPal Checkout</strong>
                        <div style="font-size: 12px; color: #6c757d;"><?php echo $paypal[0]->paypal_currency ?? 'USD'; ?></div>
                    </div>
                    <div class="status">
                        <span class="status-dot <?php echo $paypal_active ? 'active' : 'inactive'; ?>"></span>
                        <?php echo $paypal_active ? get_phrase('Active') : get_phrase('Inactive'); ?>
                    </div>
                </div>

                <form method="POST" class="paypalAjaxForm" action="<?php echo route('payment_settings/paypal'); ?>" id="paypal_settings">
                    <input type="hidden" name="<?=$this->security->get_csrf_token_name();?>" value="<?=$this->security->get_csrf_hash();?>" />

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group-modern">
                                <label><?php echo get_phrase('Status'); ?></label>
                                <select class="form-control-modern" name="paypal_active" id="paypal_active">
                                    <option value="yes" <?php if ($paypal[0]->paypal_active == 'yes') echo 'selected'; ?>><?php echo get_phrase('Active'); ?></option>
                                    <option value="no" <?php if ($paypal[0]->paypal_active == 'no') echo 'selected'; ?>><?php echo get_phrase('Inactive'); ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group-modern">
                                <label><?php echo get_phrase('Currency'); ?> <span class="required">*</span></label>
                                <select class="form-control-modern" id="paypal_currency" name="paypal_currency" required>
                                    <?php
                                    $currencies = $this->settings_model->get_paypal_supported_currencies();
                                    foreach ($currencies as $currency): ?>
                                        <option value="<?php echo $currency['code']; ?>" <?php if ($paypal[0]->paypal_currency == $currency['code']) echo 'selected'; ?>>
                                            <?php echo $currency['code']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group-modern">
                        <label><?php echo get_phrase('Mode'); ?></label>
                        <select class="form-control-modern" name="paypal_mode" id="paypal_mode">
                            <option value="sandbox" <?php if ($paypal[0]->paypal_mode == 'sandbox') echo 'selected'; ?>><?php echo get_phrase('Sandbox'); ?></option>
                            <option value="production" <?php if ($paypal[0]->paypal_mode == 'production') echo 'selected'; ?>><?php echo get_phrase('Production'); ?></option>
                        </select>
                    </div>

                    <div class="form-group-modern">
                        <label><?php echo get_phrase('Client ID (Sandbox)'); ?> <span class="required">*</span></label>
                        <div class="key-input-wrapper">
                            <input type="password" name="paypal_client_id_sandbox" class="form-control-modern" value="<?php echo $paypal[0]->paypal_client_id_sandbox; ?>" required>
                            <button type="button" class="toggle-visibility" onclick="toggleKeyVisibility(this)">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="form-group-modern">
                        <label><?php echo get_phrase('Client ID (Production)'); ?> <span class="required">*</span></label>
                        <div class="key-input-wrapper">
                            <input type="password" name="paypal_client_id_production" class="form-control-modern" value="<?php echo $paypal[0]->paypal_client_id_production; ?>" required>
                            <button type="button" class="toggle-visibility" onclick="toggleKeyVisibility(this)">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn-modern btn-paypal w-100">
                        <i class="fab fa-paypal"></i>
                        <?php echo get_phrase('Update PayPal'); ?>
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>

<?php if(addon_status('payumoney') == 1): ?>
    <?php include 'payumoney_settings.php'; ?>
<?php endif; ?>
<?php if(addon_status('paystack') == 1): ?>
    <?php include 'paystack_settings.php'; ?>
<?php endif; ?>

<script type="text/javascript">
$(document).ready(function() {
    $('select.select2:not(.normal)').each(function () { 
        $(this).select2({ dropdownParent: '#right-modal' }); 
    });

    function getCsrfToken() {
        var csrfName = $('input[name="<?= $this->security->get_csrf_token_name(); ?>"]').attr('name');
        var csrfHash = $('input[name="<?= $this->security->get_csrf_token_name(); ?>"]').val();
        return { csrfName: csrfName, csrfHash: csrfHash };
    }

    $(".paypalAjaxForm, .systemAjaxForm, .stripeAjaxForm, .systemvatAjaxForm, .systempriceAjaxForm").submit(function(e) {
        e.preventDefault();
        var submitButton = $(this).find('button[type="submit"]');
        var originalHtml = submitButton.html();
        var updating_text = "<?php echo get_phrase('updating'); ?>...";
        
        submitButton.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> ' + updating_text);
        var csrf = getCsrfToken();
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
                    success_notify('<?= js_phrase(get_phrase('updated_successfully')); ?>');
                    setTimeout(function() {
                        location.reload();
                    }, 2000);
                } else {
                    error_notify('<?= js_phrase(get_phrase('action_not_allowed')); ?>');
                    submitButton.prop('disabled', false).html(originalHtml);
                }
            },
            error: function () {
                error_notify('<?= js_phrase(get_phrase('an_error_occurred_during_submission')); ?>');
                submitButton.prop('disabled', false).html(originalHtml);
            }
        });
    });
});

// Toggle password visibility
function toggleKeyVisibility(btn) {
    var input = btn.previousElementSibling;
    var icon = btn.querySelector('i');
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

// VAT Rate calculation
document.addEventListener('DOMContentLoaded', function () {
    const vatApplicable = document.getElementById('vat_applicable');
    const vatRate = document.getElementById('vat_rate');
    const taxResidence = document.getElementById('tax_residence');

    const DEFAULT_VAT_BY_COUNTRY = {
        'MA': '20%',
        'UAE': '5%',
    };

    function computeVatRate() {
        const applicable = String(vatApplicable.value);
        const country = String((taxResidence?.value || '').toUpperCase());

        if (applicable === '1') {
            vatRate.value = DEFAULT_VAT_BY_COUNTRY[country] || '--';
        } else {
            vatRate.value = '--';
        }
    }

    computeVatRate();
    vatApplicable.addEventListener('change', computeVatRate);
});

function checkPriceForParticulier(input) {
    input.value = input.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');
}
</script>
