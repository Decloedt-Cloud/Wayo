<link rel="stylesheet" href="<?php echo base_url(); ?>assets/backend/css/responsive.min.css">
<style>
    /* ========== PAGE HEADER ========== */
    .billing-page-header {
        background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
        border-radius: 16px;
        padding: 30px;
        margin-bottom: 25px;
        color: white;
        position: relative;
        overflow: hidden;
    }
    .billing-page-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 400px;
        height: 400px;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        border-radius: 50%;
    }
    .billing-page-header h2 {
        margin: 0 0 8px;
        font-size: 28px;
        font-weight: 700;
    }
    .billing-page-header p {
        margin: 0;
        opacity: 0.8;
        font-size: 14px;
    }
    .billing-page-header .btn-add {
        background: rgba(255,255,255,0.15);
        border: 2px solid rgba(255,255,255,0.3);
        color: white;
        padding: 12px 24px;
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    .billing-page-header .btn-add:hover {
        background: white;
        color: #1a1a2e;
        transform: translateY(-2px);
    }

    /* ========== STATS GRID ========== */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        margin-bottom: 25px;
    }
    @media (max-width: 992px) {
        .stats-grid { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 576px) {
        .stats-grid { grid-template-columns: 1fr; }
    }
    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        display: flex;
        align-items: center;
        gap: 15px;
        transition: all 0.3s ease;
    }
    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.12);
    }
    .stat-icon {
        width: 56px;
        height: 56px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
    }
    .stat-content h3 {
        margin: 0;
        font-size: 28px;
        font-weight: 700;
        color: #1a1a2e;
    }
    .stat-content p {
        margin: 4px 0 0;
        color: #6c757d;
        font-size: 13px;
    }

    /* ========== ENTITY CARDS ========== */
    .entity-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 24px;
    }
    @media (max-width: 992px) {
        .entity-grid { grid-template-columns: 1fr; }
    }
    .entity-card {
        background: white;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
        border: 1px solid #e8e8e8;
    }
    .entity-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 40px rgba(0,0,0,0.15);
    }
    .entity-card-header {
        padding: 24px;
        color: white;
        position: relative;
    }
    .entity-card-header .entity-flag {
        position: absolute;
        right: 20px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 60px;
        opacity: 0.25;
    }
    .entity-card-header h4 {
        margin: 0 0 6px;
        font-size: 20px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .entity-card-header .legal-name {
        font-size: 13px;
        opacity: 0.85;
    }
    .entity-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
    }
    .entity-badge.default {
        background: rgba(255,255,255,0.25);
        color: white;
    }
    .entity-badge.inactive {
        background: rgba(220,53,69,0.9);
        color: white;
    }

    /* ========== ENTITY BODY ========== */
    .entity-card-body {
        padding: 20px 24px;
    }
    .entity-info-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 16px;
        margin-bottom: 16px;
    }
    .entity-info-item {
        display: flex;
        flex-direction: column;
    }
    .entity-info-item .label {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #6c757d;
        margin-bottom: 4px;
    }
    .entity-info-item .value {
        font-size: 15px;
        font-weight: 600;
        color: #1a1a2e;
    }
    .entity-info-item .value.highlight {
        font-size: 22px;
        color: #c62828;
    }

    /* ========== API STATUS ========== */
    .api-status-section {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 14px;
        margin-top: 16px;
    }
    .api-status-section .title {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #6c757d;
        margin-bottom: 10px;
    }
    .api-status-row {
        display: flex;
        gap: 10px;
    }
    .api-status-item {
        flex: 1;
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 8px 12px;
        background: white;
        border-radius: 8px;
        font-size: 13px;
    }
    .api-status-item i {
        font-size: 18px;
    }
    .api-status-item.configured {
        color: #28a745;
        border: 1px solid #28a745;
    }
    .api-status-item.not-configured {
        color: #dc3545;
        border: 1px solid #dc3545;
    }

    /* ========== MAPPINGS SECTION ========== */
    .mappings-section {
        margin-top: 16px;
        padding-top: 16px;
        border-top: 1px solid #e8e8e8;
    }
    .mappings-section .label {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #6c757d;
        margin-bottom: 8px;
    }
    .mapping-tag {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 5px 12px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        margin-right: 6px;
        margin-bottom: 6px;
    }

    /* ========== ENTITY ACTIONS ========== */
    .entity-card-footer {
        padding: 16px 24px;
        background: #f8f9fa;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-top: 1px solid #e8e8e8;
    }
    .entity-card-footer .btn-group-actions {
        display: flex;
        gap: 8px;
    }
    .entity-card-footer .btn {
        padding: 8px 14px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 500;
    }
    .btn-api-keys {
        background: linear-gradient(135deg, #f5af19 0%, #f12711 100%);
        border: none;
        color: white;
    }
    .btn-api-keys:hover {
        background: linear-gradient(135deg, #f12711 0%, #f5af19 100%);
        color: white;
    }

    /* ========== MAPPINGS TABLE CARD ========== */
    .mappings-table-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        overflow: hidden;
        margin-top: 30px;
    }
    .mappings-table-card .card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 20px 24px;
    }
    .mappings-table-card .card-header h5 {
        margin: 0;
        font-weight: 600;
    }
    .mappings-table-card .table {
        margin: 0;
    }
    .mappings-table-card .table th {
        background: #f8f9fa;
        border-top: none;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #6c757d;
        padding: 14px 20px;
    }
    .mappings-table-card .table td {
        padding: 14px 20px;
        vertical-align: middle;
    }

    /* ========== EMPTY STATE ========== */
    .empty-state {
        text-align: center;
        padding: 60px 40px;
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    }
    .empty-state i {
        font-size: 64px;
        color: #dee2e6;
        margin-bottom: 20px;
    }
    .empty-state h5 {
        color: #495057;
        margin-bottom: 10px;
    }
    .empty-state p {
        color: #6c757d;
        margin-bottom: 20px;
    }
</style>

<!-- Page Header -->
<div class="billing-page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h2><i class="fas fa-building mr-2"></i><?php echo get_phrase('Billing Entities'); ?></h2>
            <p><?php echo get_phrase('Manage your billing entities, VAT configurations, and payment credentials'); ?></p>
        </div>
        <button type="button" class="btn btn-add" 
                onclick="rightModal('<?php echo site_url('modal/popup/billing_entity/create'); ?>', '<?php echo get_phrase('Create Billing Entity'); ?>')">
            <i class="fas fa-plus mr-2"></i><?php echo get_phrase('Add Entity'); ?>
        </button>
    </div>
</div>

<!-- Stats Grid -->
<?php
$total_entities = count($entities);
$active_entities = count(array_filter($entities, function($e) { return $e['is_active']; }));
$total_mappings = count($mappings);

// Count API credentials
$CI =& get_instance();
$CI->load->model('BillingEntityCredentials_model', 'creds_model');
$configured_apis = 0;
foreach ($entities as $entity) {
    $stripe = $CI->creds_model->get_credentials($entity['id'], 'stripe');
    $paypal = $CI->creds_model->get_credentials($entity['id'], 'paypal');
    if (!empty($stripe['test_secret_key']) || !empty($stripe['live_secret_key'])) $configured_apis++;
    if (!empty($paypal['sandbox_client_id']) || !empty($paypal['production_client_id'])) $configured_apis++;
}
?>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
            <i class="fas fa-building"></i>
        </div>
        <div class="stat-content">
            <h3><?php echo $total_entities; ?></h3>
            <p><?php echo get_phrase('Total Entities'); ?></p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); color: white;">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="stat-content">
            <h3><?php echo $active_entities; ?></h3>
            <p><?php echo get_phrase('Active Entities'); ?></p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white;">
            <i class="fas fa-link"></i>
        </div>
        <div class="stat-content">
            <h3><?php echo $total_mappings; ?></h3>
            <p><?php echo get_phrase('Tax Mappings'); ?></p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background: linear-gradient(135deg, #f5af19 0%, #f12711 100%); color: white;">
            <i class="fas fa-key"></i>
        </div>
        <div class="stat-content">
            <h3><?php echo $configured_apis; ?></h3>
            <p><?php echo get_phrase('API Credentials'); ?></p>
        </div>
    </div>
</div>

<!-- Entities List -->
<div class="billing_entities_content">
    <?php include 'list.php'; ?>
</div>

<script>
function refreshEntitiesList() {
    $.ajax({
        type: 'GET',
        url: '<?php echo route('billing_entities/list'); ?>',
        success: function(response) {
            $('.billing_entities_content').html(response);
        }
    });
}

function deleteEntity(id) {
    if (confirm('<?php echo get_phrase('Are you sure you want to delete this entity?'); ?>')) {
        $.ajax({
            type: 'POST',
            url: '<?php echo route('billing_entities/delete'); ?>/' + id,
            data: {
                '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'
            },
            success: function(response) {
                var data = JSON.parse(response);
                if (data.status) {
                    toastr.success(data.notification);
                    setTimeout(function() { location.reload(); }, 1000);
                } else {
                    toastr.error(data.notification);
                }
            }
        });
    }
}

function setDefaultEntity(id) {
    $.ajax({
        type: 'POST',
        url: '<?php echo route('billing_entities/set_default'); ?>/' + id,
        data: {
            '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'
        },
        success: function(response) {
            var data = JSON.parse(response);
            if (data.status) {
                toastr.success(data.notification);
                setTimeout(function() { location.reload(); }, 1000);
            } else {
                toastr.error(data.notification);
            }
        }
    });
}
</script>
