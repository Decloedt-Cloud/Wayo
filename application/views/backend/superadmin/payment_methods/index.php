<style>
.pm-dashboard { padding: 20px 0; }

.pm-info-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 16px;
    padding: 25px;
    color: white;
    margin-bottom: 25px;
}

.pm-info-card h4 { margin: 0 0 10px 0; font-weight: 700; }
.pm-info-card p { margin: 0; opacity: 0.9; }

/* Method Cards */
.method-card {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    margin-bottom: 20px;
}

.method-card .method-header {
    padding: 20px;
    color: white;
    display: flex;
    align-items: center;
    gap: 15px;
}

.method-card .method-icon { font-size: 32px; }
.method-card .method-name { font-size: 18px; font-weight: 700; margin: 0; }
.method-card .method-type { font-size: 12px; opacity: 0.9; }

.method-card .method-body { padding: 20px; }

/* Entity Chips */
.entity-chips { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 10px; }

.entity-chip {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

.entity-chip.linked {
    background: #e8f5e9;
    color: #2e7d32;
}

.entity-chip.not-linked {
    background: #f5f5f5;
    color: #999;
    text-decoration: line-through;
}

/* Provider Badge */
.provider-badge {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
}

.provider-badge.international { background: #e3f2fd; color: #1565c0; }
.provider-badge.local { background: #e8f5e9; color: #2e7d32; }
.provider-badge.regional { background: #fff3e0; color: #ef6c00; }

.settings-link {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    color: #667eea;
    font-size: 13px;
    text-decoration: none;
}

.settings-link:hover { text-decoration: underline; }
</style>

<div class="pm-dashboard">
    <!-- Info Card -->
    <div class="pm-info-card">
        <h4><i class="fas fa-info-circle mr-2"></i><?php echo get_phrase('Payment Methods Configuration'); ?></h4>
        <p><?php echo get_phrase('Configure which payment methods are available for each billing entity. The actual API keys are configured by each Admin in their school settings.'); ?></p>
    </div>

    <div class="row">
        <!-- Existing payment_settings info -->
        <div class="col-12 mb-4">
            <div class="alert alert-info">
                <i class="fas fa-database mr-2"></i>
                <strong><?php echo get_phrase('Note'); ?>:</strong> 
                <?php echo get_phrase('Stripe and PayPal API keys are configured per school in'); ?> 
                <code>payment_settings</code> 
                <?php echo get_phrase('table. This page only controls which methods are allowed per billing entity.'); ?>
            </div>
        </div>

        <?php 
        // Récupérer les entités et leurs méthodes liées
        $CI =& get_instance();
        $CI->load->model('PaymentMethod_model', 'pm_model');
        $CI->load->library('BillingEntityService', null, 'billingEntityService');
        $entities = $CI->billingEntityService->get_all_active();
        $all_methods = $CI->pm_model->get_all_known();
        
        foreach ($all_methods as $code => $method): 
            // Récupérer quelles entités sont liées
            $linked_entities = $CI->db
                ->select('be.code, be.name, be.country_flag, be.color_primary')
                ->from('billing_entity_payment_methods bepm')
                ->join('billing_entities be', 'be.id = bepm.billing_entity_id')
                ->where('bepm.method_code', $code)
                ->where('bepm.is_active', 1)
                ->get()
                ->result_array();
            $linked_codes = array_column($linked_entities, 'code');
        ?>
        <div class="col-lg-4 col-md-6">
            <div class="method-card">
                <div class="method-header" style="background: <?php echo $method['color']; ?>;">
                    <i class="method-icon <?php echo $method['icon']; ?>"></i>
                    <div>
                        <h5 class="method-name"><?php echo $method['name']; ?></h5>
                        <div class="method-type"><?php echo $method['display_name']; ?></div>
                    </div>
                </div>
                
                <div class="method-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="provider-badge <?php echo $method['provider_type']; ?>">
                            <?php 
                            $labels = ['international' => '🌍 International', 'local' => '📍 Local', 'regional' => '🗺️ Regional'];
                            echo $labels[$method['provider_type']] ?? $method['provider_type'];
                            ?>
                        </span>
                        
                        <?php if ($method['settings_key']): ?>
                        <a href="<?php echo site_url('superadmin/payment_settings'); ?>" class="settings-link">
                            <i class="fas fa-cog"></i> <?php echo get_phrase('Global Settings'); ?>
                        </a>
                        <?php endif; ?>
                    </div>
                    
                    <label class="text-muted mb-2" style="font-size: 12px;">
                        <i class="fas fa-link mr-1"></i><?php echo get_phrase('Available for entities'); ?>:
                    </label>
                    
                    <div class="entity-chips">
                        <?php foreach ($entities as $entity): 
                            $is_linked = in_array($entity['code'], $linked_codes);
                        ?>
                        <span class="entity-chip <?php echo $is_linked ? 'linked' : 'not-linked'; ?>" 
                              title="<?php echo $is_linked ? 'Enabled' : 'Disabled'; ?>"
                              onclick="toggleMethodEntity('<?php echo $code; ?>', <?php echo $entity['id']; ?>, <?php echo $is_linked ? 'false' : 'true'; ?>);"
                              style="cursor: pointer;">
                            <?php echo $entity['country_flag'] ?? '🏳️'; ?>
                            <?php echo $entity['code']; ?>
                            <i class="fas fa-<?php echo $is_linked ? 'check' : 'times'; ?>" style="font-size: 10px;"></i>
                        </span>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Quick Actions -->
    <div class="card mt-4">
        <div class="card-header">
            <h6 class="m-0"><i class="fas fa-bolt mr-2"></i><?php echo get_phrase('Quick Actions'); ?></h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <button class="btn btn-outline-primary btn-block" onclick="enableAllInternational();">
                        <i class="fas fa-globe mr-2"></i><?php echo get_phrase('Enable all international methods for all entities'); ?>
                    </button>
                </div>
                <div class="col-md-6">
                    <a href="<?php echo site_url('superadmin/payment_settings'); ?>" class="btn btn-outline-secondary btn-block">
                        <i class="fas fa-cog mr-2"></i><?php echo get_phrase('Go to Payment Settings'); ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleMethodEntity(methodCode, entityId, enable) {
    $.ajax({
        url: '<?php echo site_url('superadmin/payment_methods/toggle_entity'); ?>',
        type: 'POST',
        data: {
            method_code: methodCode,
            entity_id: entityId,
            enable: enable,
            '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'
        },
        success: function(response) {
            var data = JSON.parse(response);
            if (data.status) {
                toastr.success(data.notification);
                location.reload();
            } else {
                toastr.error(data.notification);
            }
        }
    });
}

function enableAllInternational() {
    if (confirm('<?php echo get_phrase('Enable Stripe, PayPal and Bank Transfer for all entities?'); ?>')) {
        $.ajax({
            url: '<?php echo site_url('superadmin/payment_methods/enable_all_international'); ?>',
            type: 'POST',
            data: {
                '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'
            },
            success: function(response) {
                var data = JSON.parse(response);
                if (data.status) {
                    toastr.success(data.notification);
                    location.reload();
                } else {
                    toastr.error(data.notification);
                }
            }
        });
    }
}
</script>
