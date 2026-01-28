<?php
// Load credentials model to check API status
$CI =& get_instance();
$CI->load->model('BillingEntityCredentials_model', 'creds_model');
?>

<?php if (empty($entities)): ?>
<div class="empty-state">
    <i class="fas fa-building"></i>
    <h5><?php echo get_phrase('No billing entities configured'); ?></h5>
    <p><?php echo get_phrase('Create your first billing entity to manage VAT and payment credentials by country.'); ?></p>
    <button type="button" class="btn btn-primary btn-lg" 
            onclick="rightModal('<?php echo site_url('modal/popup/billing_entity/create'); ?>', '<?php echo get_phrase('Create Billing Entity'); ?>')">
        <i class="fas fa-plus mr-2"></i><?php echo get_phrase('Create Entity'); ?>
    </button>
</div>
<?php else: ?>

<!-- Entity Cards Grid -->
<div class="entity-grid">
    <?php foreach ($entities as $entity): ?>
    <?php
    // Check API credentials status
    $stripe_creds = $CI->creds_model->get_credentials($entity['id'], 'stripe');
    $paypal_creds = $CI->creds_model->get_credentials($entity['id'], 'paypal');
    $stripe_configured = !empty($stripe_creds['test_secret_key']) || !empty($stripe_creds['live_secret_key']);
    $paypal_configured = !empty($paypal_creds['sandbox_client_id']) || !empty($paypal_creds['production_client_id']);
    ?>
    <div class="entity-card">
        <!-- Header -->
        <div class="entity-card-header" style="background: linear-gradient(135deg, <?php echo $entity['color_primary'] ?? '#1a237e'; ?> 0%, <?php echo $entity['color_secondary'] ?? '#3949ab'; ?> 100%);">
            <h4>
                <?php echo htmlspecialchars($entity['name']); ?>
                <?php if ($entity['is_default']): ?>
                <span class="entity-badge default"><i class="fas fa-star"></i> <?php echo get_phrase('Default'); ?></span>
                <?php endif; ?>
                <?php if (!$entity['is_active']): ?>
                <span class="entity-badge inactive"><i class="fas fa-ban"></i> <?php echo get_phrase('Inactive'); ?></span>
                <?php endif; ?>
            </h4>
            <div class="legal-name"><?php echo htmlspecialchars($entity['legal_name']); ?></div>
            <span class="entity-flag"><?php echo $entity['country_flag'] ?? '🏳️'; ?></span>
        </div>
        
        <!-- Body -->
        <div class="entity-card-body">
            <!-- Info Grid -->
            <div class="entity-info-grid">
                <div class="entity-info-item">
                    <span class="label"><?php echo get_phrase('Code'); ?></span>
                    <span class="value">
                        <span class="badge badge-primary" style="font-size: 14px; padding: 6px 12px; color:black;"><?php echo $entity['code']; ?></span>
                    </span>
                </div>
                <div class="entity-info-item">
                    <span class="label"><?php echo get_phrase('VAT Rate'); ?></span>
                    <span class="value highlight"><?php echo $entity['vat_rate']; ?>%</span>
                </div>
                <div class="entity-info-item">
                    <span class="label"><?php echo get_phrase('Country'); ?></span>
                    <span class="value"><?php echo $entity['country_flag'] ?? ''; ?> <?php echo $entity['country_name']; ?></span>
                </div>
                <div class="entity-info-item">
                    <span class="label"><?php echo get_phrase('Currency'); ?></span>
                    <span class="value"><?php echo $entity['currency_code']; ?> <small class="text-muted">(<?php echo $entity['currency_symbol']; ?>)</small></span>
                </div>
            </div>
            
            <!-- API Status -->
            <div class="api-status-section">
                <div class="title"><i class="fas fa-key mr-1"></i><?php echo get_phrase('API Credentials Status'); ?></div>
                <div class="api-status-row">
                    <div class="api-status-item <?php echo $stripe_configured ? 'configured' : 'not-configured'; ?>">
                        <i class="fab fa-cc-stripe"></i>
                        <span>Stripe</span>
                        <i class="fas fa-<?php echo $stripe_configured ? 'check-circle' : 'times-circle'; ?> ml-auto"></i>
                    </div>
                    <div class="api-status-item <?php echo $paypal_configured ? 'configured' : 'not-configured'; ?>">
                        <i class="fab fa-paypal"></i>
                        <span>PayPal</span>
                        <i class="fas fa-<?php echo $paypal_configured ? 'check-circle' : 'times-circle'; ?> ml-auto"></i>
                    </div>
                </div>
            </div>
            
            <!-- Mappings -->
            <?php if (!empty($entity['mappings'])): ?>
            <div class="mappings-section">
                <div class="label"><?php echo get_phrase('Tax Residence Mappings'); ?></div>
                <?php foreach ($entity['mappings'] as $mapping): ?>
                <span class="mapping-tag">
                    <i class="fas fa-link"></i> <?php echo $mapping; ?>
                </span>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Footer / Actions -->
        <div class="entity-card-footer">
            <div>
                <?php if (!$entity['is_default']): ?>
                <button class="btn btn-sm btn-outline-success" onclick="setDefaultEntity(<?php echo $entity['id']; ?>)" title="<?php echo get_phrase('Set as Default'); ?>">
                    <i class="fas fa-star mr-1"></i><?php echo get_phrase('Set Default'); ?>
                </button>
                <?php else: ?>
                <span class="text-success"><i class="fas fa-check-circle mr-1"></i><?php echo get_phrase('Default Entity'); ?></span>
                <?php endif; ?>
            </div>
            <div class="btn-group-actions">
                <button class="btn btn-sm btn-api-keys" 
                        onclick="rightModal('<?php echo site_url('modal/popup/billing_entity/credentials/'.$entity['id']); ?>', '<?php echo get_phrase('API Credentials'); ?>')"
                        title="<?php echo get_phrase('Configure Stripe/PayPal API Keys'); ?>">
                    <i class="fas fa-key mr-1"></i><?php echo get_phrase('API Keys'); ?>
                </button>
                <button class="btn btn-sm btn-outline-primary" 
                        onclick="rightModal('<?php echo site_url('modal/popup/billing_entity/edit/'.$entity['id']); ?>', '<?php echo get_phrase('Edit Entity'); ?>')">
                    <i class="fas fa-edit mr-1"></i><?php echo get_phrase('Edit'); ?>
                </button>
                <?php if (!$entity['is_default']): ?>
                <button class="btn btn-sm btn-outline-danger" onclick="deleteEntity(<?php echo $entity['id']; ?>)">
                    <i class="fas fa-trash"></i>
                </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Mappings Table -->
<?php if (!empty($mappings)): ?>
<div class="mappings-table-card">
    <div class="card-header">
        <h5><i class="fas fa-link mr-2"></i><?php echo get_phrase('Tax Residence Mappings Overview'); ?></h5>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th><?php echo get_phrase('Tax Residence'); ?></th>
                    <th><?php echo get_phrase('Billing Entity'); ?></th>
                    <th><?php echo get_phrase('VAT Rate'); ?></th>
                    <th><?php echo get_phrase('Currency'); ?></th>
                    <th><?php echo get_phrase('Priority'); ?></th>
                    <th><?php echo get_phrase('Status'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($mappings as $mapping): ?>
                <?php
                // Find entity details
                $entity_details = null;
                foreach ($entities as $e) {
                    if ($e['code'] == ($mapping['entity_code'] ?? '')) {
                        $entity_details = $e;
                        break;
                    }
                }
                ?>
                <tr>
                    <td>
                        <span class="badge badge-primary" style="font-size: 14px; padding: 8px 14px; color:black;">
                            <?php echo $mapping['tax_residence_code']; ?>
                        </span>
                    </td>
                    <td>
                        <?php if ($entity_details): ?>
                        <span style="color: <?php echo $entity_details['color_primary']; ?>; font-weight: 600;">
                            <?php echo $entity_details['country_flag'] ?? ''; ?> <?php echo $mapping['entity_name'] ?? 'N/A'; ?>
                        </span>
                        <?php else: ?>
                        <strong><?php echo $mapping['entity_name'] ?? 'N/A'; ?></strong>
                        <?php endif; ?>
                        <small class="text-muted ml-1">(<?php echo $mapping['entity_code'] ?? ''; ?>)</small>
                    </td>
                    <td>
                        <?php if ($entity_details): ?>
                        <span style="color: #c62828; font-weight: 700;"><?php echo $entity_details['vat_rate']; ?>%</span>
                        <?php else: ?>
                        -
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($entity_details): ?>
                        <?php echo $entity_details['currency_code']; ?>
                        <?php else: ?>
                        -
                        <?php endif; ?>
                    </td>
                    <td>
                        <span class="badge badge-light" style="color: black;"><?php echo $mapping['priority']; ?></span>
                    </td>
                    <td>
                        <?php if ($mapping['is_active']): ?>
                        <span class="badge badge-success" style="color: black;"><i class="fas fa-check mr-1"></i><?php echo get_phrase('Active'); ?></span>
                        <?php else: ?>
                        <span class="badge badge-danger" style="color: black;"><i class="fas fa-ban mr-1"></i><?php echo get_phrase('Inactive'); ?></span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<?php endif; ?>
