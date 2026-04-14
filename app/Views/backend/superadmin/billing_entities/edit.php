<style>
.entity-edit-form {
    background: #fff;
}

.entity-edit-form .section-header {
    display: flex;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 12px;
    border-bottom: 2px solid #f0f0f0;
}

.entity-edit-form .section-header .section-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 12px;
    font-size: 16px;
    color: white;
}

.entity-edit-form .section-header h6 {
    margin: 0;
    font-weight: 600;
    color: #1a1a2e;
    font-size: 15px;
}

.entity-edit-form .section-header small {
    color: #888;
    font-size: 12px;
}

.entity-edit-form .form-control {
    border: 2px solid #e8e8e8;
    border-radius: 8px;
    padding: 10px 14px;
    font-size: 14px;
    transition: all 0.2s ease;
}

.entity-edit-form .form-control:focus {
    border-color: var(--entity-color, #667eea);
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.entity-edit-form label {
    font-weight: 500;
    color: #444;
    font-size: 13px;
    margin-bottom: 6px;
}

.entity-edit-form .required-star {
    color: #e53935;
    font-weight: bold;
}

.entity-edit-form .form-group {
    margin-bottom: 18px;
}

.entity-edit-form .custom-switch .custom-control-label {
    padding-top: 2px;
    cursor: pointer;
}

.entity-edit-form .custom-switch .custom-control-input:checked ~ .custom-control-label::before {
    background-color: var(--entity-color, #667eea);
    border-color: var(--entity-color, #667eea);
}

/* Entity Header Card */
.entity-header-card {
    border-radius: 16px;
    padding: 20px;
    margin-bottom: 25px;
    color: white;
    position: relative;
    overflow: hidden;
}

.entity-header-card::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -20%;
    width: 200px;
    height: 200px;
    background: rgba(255,255,255,0.1);
    border-radius: 50%;
}

.entity-header-card .entity-flag {
    font-size: 48px;
    position: absolute;
    right: 20px;
    top: 50%;
    transform: translateY(-50%);
    opacity: 0.9;
}

.entity-header-card h4 {
    margin: 0 0 5px 0;
    font-weight: 700;
}

.entity-header-card .entity-meta {
    opacity: 0.9;
    font-size: 13px;
}

/* Color Picker Enhancement */
.color-picker-wrapper {
    display: flex;
    align-items: center;
    gap: 12px;
}

.color-picker-wrapper input[type="color"] {
    width: 50px;
    height: 50px;
    border: none;
    border-radius: 10px;
    cursor: pointer;
    padding: 0;
}

.color-picker-wrapper .color-hex {
    font-family: 'Monaco', 'Consolas', monospace;
    font-size: 13px;
    color: #666;
    background: #f5f5f5;
    padding: 6px 12px;
    border-radius: 6px;
}

/* Live Preview Card */
.live-preview-card {
    border-radius: 12px;
    padding: 20px;
    color: white;
    position: relative;
    overflow: hidden;
    transition: all 0.3s ease;
}

.live-preview-card .preview-entity-name {
    font-size: 18px;
    font-weight: 700;
    margin-bottom: 8px;
}

.live-preview-card .preview-details {
    display: flex;
    gap: 20px;
    font-size: 13px;
    opacity: 0.9;
}

.live-preview-card .preview-flag {
    position: absolute;
    right: 20px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 40px;
}

/* Mapping Tags */
.mapping-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-top: 10px;
}

.mapping-tag {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.mapping-tag .remove-tag {
    cursor: pointer;
    opacity: 0.7;
    transition: opacity 0.2s;
}

.mapping-tag .remove-tag:hover {
    opacity: 1;
}

/* Status Badge */
.status-indicator {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
}

.status-indicator.active {
    background: #e8f5e9;
    color: #2e7d32;
}

.status-indicator.inactive {
    background: #ffebee;
    color: #c62828;
}

/* Submit Button */
.btn-submit-entity {
    background: linear-gradient(135deg, var(--entity-color, #667eea) 0%, var(--entity-color-secondary, #764ba2) 100%);
    border: none;
    padding: 14px 28px;
    font-size: 15px;
    font-weight: 600;
    border-radius: 10px;
    color: white;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
}

.btn-submit-entity:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
    color: white;
}

/* Info Tooltips */
.field-hint {
    font-size: 11px;
    color: #888;
    margin-top: 4px;
    display: flex;
    align-items: center;
    gap: 4px;
}

.field-hint i {
    color: #667eea;
}

/* Section Cards */
.section-card {
    background: #fafafa;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 20px;
}

/* Input Groups */
.input-with-icon {
    position: relative;
}

.input-with-icon .input-icon {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: #888;
    font-size: 14px;
}

.input-with-icon .form-control {
    padding-left: 40px;
}
</style>

<form method="POST" class="entity-edit-form billingEntityEditForm" action="<?php echo route('billing_entities/update/'.$entity['id']); ?>" style="--entity-color: <?php echo $entity['color_primary'] ?? '#667eea'; ?>; --entity-color-secondary: <?php echo $entity['color_secondary'] ?? '#764ba2'; ?>;">
    <input type="hidden" name="<?php echo csrf_token(); ?>" value="<?php echo csrf_hash(); ?>">
    
    <!-- Entity Header Preview -->
    <div class="entity-header-card" id="header-preview" style="background: linear-gradient(135deg, <?php echo $entity['color_primary'] ?? '#667eea'; ?> 0%, <?php echo $entity['color_secondary'] ?? '#764ba2'; ?> 100%);">
        <span class="entity-flag" id="preview-flag"><?php echo $entity['country_flag'] ?? '🏳️'; ?></span>
        <h4 id="preview-name"><?php echo htmlspecialchars($entity['name']); ?></h4>
        <div class="entity-meta">
            <span id="preview-legal"><?php echo htmlspecialchars($entity['legal_name']); ?></span>
            <span class="mx-2">•</span>
            <span id="preview-country"><?php echo htmlspecialchars($entity['country_name']); ?></span>
            <span class="mx-2">•</span>
            <span>TVA <span id="preview-vat"><?php echo $entity['vat_rate']; ?></span>%</span>
        </div>
    </div>
    
    <!-- Section 1: Identity -->
    <div class="section-card">
        <div class="section-header">
            <div class="section-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <i class="fas fa-building"></i>
            </div>
            <div>
                <h6><?php echo get_phrase('Entity Identity'); ?></h6>
                <small><?php echo get_phrase('Basic identification information'); ?></small>
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group col-md-3">
                <label for="code"><?php echo get_phrase('Code'); ?> <span class="required-star">*</span></label>
                <div class="input-with-icon">
                    <i class="fas fa-hashtag input-icon"></i>
                    <input type="text" class="form-control" id="code" name="code" value="<?php echo htmlspecialchars($entity['code']); ?>" required maxlength="10" style="text-transform: uppercase;" placeholder="MA">
                </div>
                <div class="field-hint"><i class="fas fa-info-circle"></i> <?php echo get_phrase('Unique identifier'); ?></div>
            </div>
            <div class="form-group col-md-9">
                <label for="name"><?php echo get_phrase('Entity Name'); ?> <span class="required-star">*</span></label>
                <div class="input-with-icon">
                    <i class="fas fa-tag input-icon"></i>
                    <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($entity['name']); ?>" required placeholder="Decloedt SARL">
                </div>
            </div>
        </div>
        
        <div class="form-group">
            <label for="legal_name"><?php echo get_phrase('Legal Name (Full)'); ?> <span class="required-star">*</span></label>
            <div class="input-with-icon">
                <i class="fas fa-file-contract input-icon"></i>
                <input type="text" class="form-control" id="legal_name" name="legal_name" value="<?php echo htmlspecialchars($entity['legal_name']); ?>" required placeholder="Decloedt SARL - Société à Responsabilité Limitée">
            </div>
        </div>
    </div>
    
    <!-- Section 2: Country -->
    <div class="section-card">
        <div class="section-header">
            <div class="section-icon" style="background: linear-gradient(135deg, #00b894 0%, #00cec9 100%);">
                <i class="fas fa-globe-africa"></i>
            </div>
            <div>
                <h6><?php echo get_phrase('Country & Region'); ?></h6>
                <small><?php echo get_phrase('Geographic information'); ?></small>
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group col-md-2">
                <label for="country_code"><?php echo get_phrase('ISO Code'); ?> <span class="required-star">*</span></label>
                <input type="text" class="form-control text-center" id="country_code" name="country_code" value="<?php echo htmlspecialchars($entity['country_code']); ?>" required maxlength="3" style="text-transform: uppercase; font-weight: 700; font-size: 16px;" placeholder="MA">
            </div>
            <div class="form-group col-md-2">
                <label for="country_flag"><?php echo get_phrase('Flag'); ?></label>
                <input type="text" class="form-control text-center" id="country_flag" name="country_flag" value="<?php echo htmlspecialchars($entity['country_flag'] ?? ''); ?>" maxlength="10" style="font-size: 24px;" placeholder="🇲🇦">
            </div>
            <div class="form-group col-md-8">
                <label for="country_name"><?php echo get_phrase('Country Name'); ?> <span class="required-star">*</span></label>
                <div class="input-with-icon">
                    <i class="fas fa-map-marker-alt input-icon"></i>
                    <input type="text" class="form-control" id="country_name" name="country_name" value="<?php echo htmlspecialchars($entity['country_name']); ?>" required placeholder="Morocco">
                </div>
            </div>
        </div>
    </div>
    
    <!-- Section 3: VAT & Currency -->
    <div class="section-card">
        <div class="section-header">
            <div class="section-icon" style="background: linear-gradient(135deg, #fdcb6e 0%, #f39c12 100%);">
                <i class="fas fa-percent"></i>
            </div>
            <div>
                <h6><?php echo get_phrase('VAT & Currency'); ?></h6>
                <small><?php echo get_phrase('Tax and monetary configuration'); ?></small>
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group col-md-4">
                <label for="vat_rate"><?php echo get_phrase('VAT Rate'); ?> <span class="required-star">*</span></label>
                <div class="input-group">
                    <input type="number" step="0.01" min="0" max="100" class="form-control text-center" id="vat_rate" name="vat_rate" value="<?php echo $entity['vat_rate']; ?>" required style="font-size: 20px; font-weight: 700;">
                    <div class="input-group-append">
                        <span class="input-group-text" style="font-weight: 700; font-size: 18px;">%</span>
                    </div>
                </div>
            </div>
            <div class="form-group col-md-4">
                <label for="currency_code"><?php echo get_phrase('Currency Code'); ?> <span class="required-star">*</span></label>
                <input type="text" class="form-control text-center" id="currency_code" name="currency_code" value="<?php echo htmlspecialchars($entity['currency_code']); ?>" required maxlength="3" style="text-transform: uppercase; font-weight: 700; font-size: 16px;" placeholder="MAD">
            </div>
            <div class="form-group col-md-4">
                <label for="currency_symbol"><?php echo get_phrase('Symbol'); ?></label>
                <input type="text" class="form-control text-center" id="currency_symbol" name="currency_symbol" value="<?php echo htmlspecialchars($entity['currency_symbol'] ?? ''); ?>" maxlength="10" style="font-size: 18px; font-weight: 700;" placeholder="DH">
            </div>
        </div>
    </div>
    
    <!-- Section 4: Payment -->
    <div class="section-card">
        <div class="section-header">
            <div class="section-icon" style="background: linear-gradient(135deg, #a29bfe 0%, #6c5ce7 100%);">
                <i class="fas fa-credit-card"></i>
            </div>
            <div>
                <h6><?php echo get_phrase('Payment & Banking'); ?></h6>
                <small><?php echo get_phrase('PSP and bank information'); ?></small>
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="psp_name"><?php echo get_phrase('PSP Name'); ?></label>
                <select class="form-control" id="psp_name" name="psp_name">
                    <option value="">-- <?php echo get_phrase('Select PSP'); ?> --</option>
                    <option value="Stripe" <?php echo ($entity['psp_name'] ?? '') === 'Stripe' ? 'selected' : ''; ?>>💳 Stripe</option>
                    <option value="PayPal" <?php echo ($entity['psp_name'] ?? '') === 'PayPal' ? 'selected' : ''; ?>>🅿️ PayPal</option>
                    <!-- CashPlus temporairement désactivé -->
                    <!-- <option value="CashPlus" <?php echo ($entity['psp_name'] ?? '') === 'CashPlus' ? 'selected' : ''; ?>>💵 CashPlus</option> -->
                    <option value="CMI" <?php echo ($entity['psp_name'] ?? '') === 'CMI' ? 'selected' : ''; ?>>🏦 CMI</option>
                    <option value="Other" <?php echo !in_array($entity['psp_name'] ?? '', ['Stripe', 'PayPal', 'CMI', '']) ? 'selected' : ''; ?>><?php echo get_phrase('Other'); ?></option>
                </select>
            </div>
            <div class="form-group col-md-6">
                <label for="psp_type"><?php echo get_phrase('PSP Type'); ?></label>
                <div class="btn-group btn-group-toggle w-100" data-toggle="buttons">
                    <label class="btn btn-outline-primary <?php echo ($entity['psp_type'] ?? 'international') === 'international' ? 'active' : ''; ?>" style="flex: 1;">
                        <input type="radio" name="psp_type" value="international" <?php echo ($entity['psp_type'] ?? 'international') === 'international' ? 'checked' : ''; ?>>
                        <i class="fas fa-globe mr-1"></i> <?php echo get_phrase('International'); ?>
                    </label>
                    <label class="btn btn-outline-success <?php echo ($entity['psp_type'] ?? '') === 'local' ? 'active' : ''; ?>" style="flex: 1;">
                        <input type="radio" name="psp_type" value="local" <?php echo ($entity['psp_type'] ?? '') === 'local' ? 'checked' : ''; ?>>
                        <i class="fas fa-map-pin mr-1"></i> <?php echo get_phrase('Local'); ?>
                    </label>
                </div>
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="bank_name"><?php echo get_phrase('Bank Name'); ?></label>
                <div class="input-with-icon">
                    <i class="fas fa-university input-icon"></i>
                    <input type="text" class="form-control" id="bank_name" name="bank_name" value="<?php echo htmlspecialchars($entity['bank_name'] ?? ''); ?>" placeholder="Attijariwafa Bank">
                </div>
            </div>
            <div class="form-group col-md-6">
                <label for="bank_country"><?php echo get_phrase('Bank Country'); ?></label>
                <div class="input-with-icon">
                    <i class="fas fa-flag input-icon"></i>
                    <input type="text" class="form-control" id="bank_country" name="bank_country" value="<?php echo htmlspecialchars($entity['bank_country'] ?? ''); ?>" placeholder="Morocco">
                </div>
            </div>
        </div>
    </div>
    
    <!-- Section 5: Appearance -->
    <div class="section-card">
        <div class="section-header">
            <div class="section-icon" style="background: linear-gradient(135deg, #fd79a8 0%, #e84393 100%);">
                <i class="fas fa-palette"></i>
            </div>
            <div>
                <h6><?php echo get_phrase('Appearance'); ?></h6>
                <small><?php echo get_phrase('Colors and visual settings'); ?></small>
            </div>
        </div>
        
        <div class="form-row align-items-end">
            <div class="form-group col-md-4">
                <label><?php echo get_phrase('Primary Color'); ?></label>
                <div class="color-picker-wrapper">
                    <input type="color" id="color_primary" name="color_primary" value="<?php echo $entity['color_primary'] ?? '#c62828'; ?>">
                    <span class="color-hex" id="hex-primary"><?php echo $entity['color_primary'] ?? '#c62828'; ?></span>
                </div>
            </div>
            <div class="form-group col-md-4">
                <label><?php echo get_phrase('Secondary Color'); ?></label>
                <div class="color-picker-wrapper">
                    <input type="color" id="color_secondary" name="color_secondary" value="<?php echo $entity['color_secondary'] ?? '#e53935'; ?>">
                    <span class="color-hex" id="hex-secondary"><?php echo $entity['color_secondary'] ?? '#e53935'; ?></span>
                </div>
            </div>
            <div class="form-group col-md-4">
                <label for="display_order"><?php echo get_phrase('Display Order'); ?></label>
                <input type="number" class="form-control text-center" id="display_order" name="display_order" value="<?php echo $entity['display_order'] ?? 0; ?>" min="0" style="font-weight: 600;">
            </div>
        </div>
        
        <!-- Live Preview -->
        <div class="mt-3">
            <label class="mb-2"><i class="fas fa-eye mr-1"></i> <?php echo get_phrase('Live Preview'); ?></label>
            <div class="live-preview-card" id="color-preview" style="background: linear-gradient(135deg, <?php echo $entity['color_primary'] ?? '#c62828'; ?> 0%, <?php echo $entity['color_secondary'] ?? '#e53935'; ?> 100%);">
                <span class="preview-flag" id="preview-flag-small"><?php echo $entity['country_flag'] ?? '🏳️'; ?></span>
                <div class="preview-entity-name" id="preview-entity-name"><?php echo htmlspecialchars($entity['name']); ?></div>
                <div class="preview-details">
                    <span><i class="fas fa-percent mr-1"></i> TVA <span id="preview-vat-small"><?php echo $entity['vat_rate']; ?></span>%</span>
                    <span><i class="fas fa-coins mr-1"></i> <span id="preview-currency"><?php echo htmlspecialchars($entity['currency_code']); ?></span></span>
                    <span><i class="fas fa-credit-card mr-1"></i> <span id="preview-psp"><?php echo htmlspecialchars($entity['psp_name'] ?? 'N/A'); ?></span></span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Section 6: Payment Methods -->
    <div class="section-card">
        <div class="section-header">
            <div class="section-icon" style="background: linear-gradient(135deg, #00b894 0%, #00cec9 100%);">
                <i class="fas fa-credit-card"></i>
            </div>
            <div>
                <h6><?php echo get_phrase('Payment Methods'); ?></h6>
                <small><?php echo get_phrase('Select which payment methods are available for this entity'); ?></small>
            </div>
        </div>
        
        <?php
        $paymentMethodModel = model('PaymentMethod_model');
        if (!is_object($paymentMethodModel)) {
            $paymentMethodModel = model('App\Models\PaymentMethod_model');
        }
        $all_methods = (is_object($paymentMethodModel) && method_exists($paymentMethodModel, 'get_all_known'))
            ? ($paymentMethodModel->get_all_known() ?? [])
            : [];
        $linked_methods = (is_object($paymentMethodModel) && method_exists($paymentMethodModel, 'get_for_entity'))
            ? ($paymentMethodModel->get_for_entity($entity['id']) ?? [])
            : [];
        $linked_codes = array_column($linked_methods, 'code');
        ?>
        
        <div class="row">
            <?php foreach ($all_methods as $code => $method): 
                $is_linked = in_array($code, $linked_codes);
            ?>
            <div class="col-md-4 mb-3">
                <div class="payment-method-toggle p-3 rounded <?php echo $is_linked ? 'active' : ''; ?>" 
                     style="border: 2px solid <?php echo $is_linked ? $method['color'] : '#e0e0e0'; ?>; 
                            background: <?php echo $is_linked ? $method['color'] . '15' : '#fafafa'; ?>;
                            cursor: pointer; transition: all 0.2s ease;"
                     onclick="togglePaymentMethod('<?php echo $code; ?>', <?php echo $entity['id']; ?>, this, <?php echo $is_linked ? 'false' : 'true'; ?>);">
                    <div class="d-flex align-items-center">
                        <div class="mr-3">
                            <i class="<?php echo $method['icon']; ?>" style="font-size: 28px; color: <?php echo $method['color']; ?>;"></i>
                        </div>
                        <div class="flex-grow-1">
                            <strong style="color: <?php echo $is_linked ? $method['color'] : '#666'; ?>;"><?php echo $method['name']; ?></strong>
                            <div style="font-size: 11px; color: #888;">
                                <?php 
                                $types = ['international' => '🌍 International', 'local' => '📍 Local', 'regional' => '🗺️ Regional'];
                                echo $types[$method['provider_type']] ?? '';
                                ?>
                            </div>
                        </div>
                        <div>
                            <i class="fas fa-<?php echo $is_linked ? 'check-circle' : 'circle'; ?>" 
                               style="font-size: 20px; color: <?php echo $is_linked ? '#00b894' : '#ccc'; ?>;"></i>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div class="alert alert-info mt-2 mb-0" style="font-size: 12px;">
            <i class="fas fa-info-circle mr-1"></i>
            <?php echo get_phrase('Note: Stripe and PayPal require API keys configuration in Admin settings to be functional.'); ?>
        </div>
    </div>
    
    <!-- Section 7: Settings & Mappings -->
    <div class="section-card">
        <div class="section-header">
            <div class="section-icon" style="background: linear-gradient(135deg, #74b9ff 0%, #0984e3 100%);">
                <i class="fas fa-cogs"></i>
            </div>
            <div>
                <h6><?php echo get_phrase('Settings & Mappings'); ?></h6>
                <small><?php echo get_phrase('Status and tax residence associations'); ?></small>
            </div>
        </div>
        
        <div class="form-row mb-4">
            <div class="col-md-6">
                <div class="d-flex align-items-center p-3 rounded" style="background: <?php echo $entity['is_active'] ? '#e8f5e9' : '#ffebee'; ?>;">
                    <div class="custom-control custom-switch mr-3">
                        <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" <?php echo $entity['is_active'] ? 'checked' : ''; ?>>
                        <label class="custom-control-label" for="is_active"></label>
                    </div>
                    <div>
                        <strong style="color: <?php echo $entity['is_active'] ? '#2e7d32' : '#c62828'; ?>;">
                            <i class="fas fa-<?php echo $entity['is_active'] ? 'check-circle' : 'times-circle'; ?> mr-1"></i>
                            <?php echo $entity['is_active'] ? get_phrase('Active') : get_phrase('Inactive'); ?>
                        </strong>
                        <div class="text-muted" style="font-size: 12px;"><?php echo get_phrase('Entity status'); ?></div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="d-flex align-items-center p-3 rounded" style="background: <?php echo $entity['is_default'] ? '#e3f2fd' : '#f5f5f5'; ?>;">
                    <div class="custom-control custom-switch mr-3">
                        <input type="checkbox" class="custom-control-input" id="is_default" name="is_default" value="1" <?php echo $entity['is_default'] ? 'checked' : ''; ?>>
                        <label class="custom-control-label" for="is_default"></label>
                    </div>
                    <div>
                        <strong style="color: <?php echo $entity['is_default'] ? '#1565c0' : '#666'; ?>;">
                            <i class="fas fa-<?php echo $entity['is_default'] ? 'star' : 'star'; ?> mr-1" style="color: <?php echo $entity['is_default'] ? '#ffc107' : '#ccc'; ?>;"></i>
                            <?php echo $entity['is_default'] ? get_phrase('Default Entity') : get_phrase('Not Default'); ?>
                        </strong>
                        <div class="text-muted" style="font-size: 12px;"><?php echo get_phrase('Fallback entity'); ?></div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="form-group">
            <label><i class="fas fa-link mr-1"></i> <?php echo get_phrase('Tax Residence Mappings'); ?></label>
            <input type="text" class="form-control" id="mappings" name="mappings" value="<?php echo implode(', ', $entity['mappings'] ?? []); ?>" placeholder="MA, MAR, Morocco">
            <div class="field-hint">
                <i class="fas fa-info-circle"></i> 
                <?php echo get_phrase('Comma-separated tax residence codes that use this entity'); ?>
            </div>
            
            <?php if (!empty($entity['mappings'])): ?>
            <div class="mapping-tags mt-2">
                <?php foreach ($entity['mappings'] as $mapping): ?>
                <span class="mapping-tag" style="background: linear-gradient(135deg, <?php echo $entity['color_primary'] ?? '#667eea'; ?> 0%, <?php echo $entity['color_secondary'] ?? '#764ba2'; ?> 100%);">
                    <?php echo htmlspecialchars($mapping); ?>
                </span>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Submit Button -->
    <div class="text-center mt-4">
        <button type="submit" class="btn btn-submit-entity btn-lg px-5">
            <i class="fas fa-save mr-2"></i><?php echo get_phrase('Update Entity'); ?>
        </button>
    </div>
</form>

<script>
$(document).ready(function() {
    // Live preview updates
    function updatePreview() {
        var primary = $('#color_primary').val();
        var secondary = $('#color_secondary').val();
        var gradient = 'linear-gradient(135deg, ' + primary + ' 0%, ' + secondary + ' 100%)';
        
        $('#header-preview, #color-preview').css('background', gradient);
        $('#hex-primary').text(primary);
        $('#hex-secondary').text(secondary);
        
        // Update CSS variables
        $('.entity-edit-form').css('--entity-color', primary);
        $('.entity-edit-form').css('--entity-color-secondary', secondary);
    }
    
    $('#color_primary, #color_secondary').on('input', updatePreview);
    
    // Live text updates
    $('#name').on('input', function() {
        $('#preview-name, #preview-entity-name').text($(this).val() || 'Entity Name');
    });
    
    $('#legal_name').on('input', function() {
        $('#preview-legal').text($(this).val() || 'Legal Name');
    });
    
    $('#country_name').on('input', function() {
        $('#preview-country').text($(this).val() || 'Country');
    });
    
    $('#country_flag').on('input', function() {
        $('#preview-flag, #preview-flag-small').text($(this).val() || '🏳️');
    });
    
    $('#vat_rate').on('input', function() {
        $('#preview-vat, #preview-vat-small').text($(this).val() || '0');
    });
    
    $('#currency_code').on('input', function() {
        $('#preview-currency').text($(this).val() || 'XXX');
    });
    
    $('#psp_name').on('change', function() {
        $('#preview-psp').text($(this).val() || 'N/A');
    });
    
    // Status toggle visual feedback
    $('#is_active').on('change', function() {
        var isActive = $(this).is(':checked');
        $(this).closest('.d-flex').css('background', isActive ? '#e8f5e9' : '#ffebee');
        $(this).closest('.d-flex').find('strong').css('color', isActive ? '#2e7d32' : '#c62828');
        $(this).closest('.d-flex').find('i').removeClass('fa-check-circle fa-times-circle').addClass(isActive ? 'fa-check-circle' : 'fa-times-circle');
    });
    
    $('#is_default').on('change', function() {
        var isDefault = $(this).is(':checked');
        $(this).closest('.d-flex').css('background', isDefault ? '#e3f2fd' : '#f5f5f5');
        $(this).closest('.d-flex').find('strong').css('color', isDefault ? '#1565c0' : '#666');
        $(this).closest('.d-flex').find('.fa-star').css('color', isDefault ? '#ffc107' : '#ccc');
    });
    
    // Toggle payment method
    window.togglePaymentMethod = function(methodCode, entityId, element, enable) {
        $.ajax({
            url: '<?php echo site_url('superadmin/payment_methods/toggle_entity'); ?>',
            type: 'POST',
            data: {
                method_code: methodCode,
                entity_id: entityId,
                enable: enable,
                '<?php echo csrf_token(); ?>': '<?php echo csrf_hash(); ?>'
            },
            success: function(response) {
                var $data = JSON.parse(response);
                if (data.status) {
                    // Toggle visual state
                    var $el = $(element);
                    var isNowActive = enable === 'true';
                    var color = $el.find('i:first').css('color');
                    
                    if (isNowActive) {
                        $el.addClass('active');
                        $el.css({'border-color': color, 'background': color.replace(')', ', 0.1)').replace('rgb', 'rgba')});
                        $el.find('.fa-circle').removeClass('fa-circle').addClass('fa-check-circle').css('color', '#00b894');
                        $el.find('strong').css('color', color);
                    } else {
                        $el.removeClass('active');
                        $el.css({'border-color': '#e0e0e0', 'background': '#fafafa'});
                        $el.find('.fa-check-circle').removeClass('fa-check-circle').addClass('fa-circle').css('color', '#ccc');
                        $el.find('strong').css('color', '#666');
                    }
                    
                    // Update onclick for next click
                    $el.attr('onclick', "togglePaymentMethod('" + methodCode + "', " + entityId + ", this, " + (isNowActive ? 'false' : 'true') + ");");
                    
                    toastr.success(data.notification);
                } else {
                    toastr.error(data.notification);
                }
            },
            error: function() {
                toastr.error('<?php echo get_phrase('An error occurred'); ?>');
            }
        });
    };
    
    // Form submission
    $('.billingEntityEditForm').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var btn = form.find('button[type="submit"]');
        var originalHtml = btn.html();
        
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i><?php echo get_phrase('Updating...'); ?>');
        
        $.ajax({
            type: 'POST',
            url: form.attr('action'),
            data: form.serialize(),
            success: function(response) {
                try {
                    var $data = JSON.parse(response);
                    if (data.status) {
                        toastr.success(data.notification);
                        setTimeout(function() {
                            location.reload();
                        }, 1000);
                    } else {
                        toastr.error(data.notification);
                        btn.prop('disabled', false).html(originalHtml);
                    }
                } catch (e) {
                    toastr.error('<?php echo get_phrase('Invalid response'); ?>');
                    btn.prop('disabled', false).html(originalHtml);
                }
            },
            error: function() {
                toastr.error('<?php echo get_phrase('An error occurred'); ?>');
                btn.prop('disabled', false).html(originalHtml);
            }
        });
    });
});
</script>
