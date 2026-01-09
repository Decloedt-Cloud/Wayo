<link rel="stylesheet" href="<?php echo base_url(); ?>assets/backend/css/responsive.css">

<style>
/* ============================================================================
   EXPENSE CATEGORY - MODERN DESIGN (MATCHING INVOICE STYLE)
   ============================================================================ */

:root {
    --ec-primary: #6366f1;
    --ec-primary-light: #eef2ff;
    --ec-success: #059669;
    --ec-dark: #1e293b;
    --ec-gray: #64748b;
    --ec-light: #f8fafc;
    --ec-border: #e2e8f0;
    --ec-warning: #f59e0b;
}

/* Header Card */
.ec-header {
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

.ec-header-left {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.ec-header-icon {
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

.ec-header-text h4 {
    margin: 0;
    color: white;
    font-size: 1.35rem;
    font-weight: 700;
}

.ec-header-text p {
    margin: 0.25rem 0 0;
    color: rgba(255,255,255,0.7);
    font-size: 0.85rem;
}

.ec-header-actions {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
}

.ec-btn {
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

.ec-btn-primary {
    background: linear-gradient(135deg, #1e293b, #334155);
    color: white;
}

.ec-btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(30, 41, 59, 0.4);
    color: white;
}

/* Content Card */
.ec-content-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
    border: 1px solid var(--ec-border);
    overflow: hidden;
}

.ec-content-header {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    padding: 1rem 1.5rem;
    border-bottom: 1px solid var(--ec-border);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.ec-content-title {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 600;
    color: var(--ec-dark);
    font-size: 0.95rem;
}

.ec-content-title i {
    color: var(--ec-primary);
}

.ec-content-body {
    padding: 0;
}

/* Loading State */
.ec-loading {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 3rem;
    color: var(--ec-gray);
}

.ec-loading i {
    font-size: 2rem;
    animation: ec-spin 1s linear infinite;
    margin-right: 0.75rem;
}

@keyframes ec-spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

/* Table Styles */
.ec-table {
    width: 100%;
    border-collapse: collapse;
}

.ec-table thead {
    background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
}

.ec-table thead th {
    padding: 1rem 1.5rem;
    color: white;
    font-weight: 600;
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border: none;
}

.ec-table thead th i {
    margin-right: 0.5rem;
    opacity: 0.7;
}

.ec-table tbody tr {
    border-bottom: 1px solid var(--ec-border);
    transition: all 0.2s;
}

.ec-table tbody tr:hover {
    background: var(--ec-primary-light);
}

.ec-table tbody td {
    padding: 1rem 1.5rem;
    color: var(--ec-dark);
    font-size: 0.9rem;
    vertical-align: middle;
}

.ec-table tbody tr:last-child {
    border-bottom: none;
}

/* Category Badge */
.ec-category-name {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    background: linear-gradient(135deg, var(--ec-primary-light), #e0e7ff);
    border-radius: 8px;
    font-weight: 600;
    color: var(--ec-primary);
}

.ec-category-name i {
    font-size: 1rem;
}

/* Cost Center Badge */
.ec-cost-center {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.35rem 0.75rem;
    background: linear-gradient(135deg, #fef3c7, #fde68a);
    border-radius: 6px;
    font-size: 0.8rem;
    font-weight: 500;
    color: #92400e;
}

.ec-cost-center i {
    font-size: 0.85rem;
}

.ec-cost-center.empty {
    background: #f1f5f9;
    color: #94a3b8;
}

/* Action Buttons */
.ec-actions {
    display: flex;
    gap: 0.5rem;
    justify-content: center;
}

.ec-action-btn {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    border: none;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
    font-size: 1rem;
}

.ec-action-btn.edit {
    background: linear-gradient(135deg, #eef2ff, #e0e7ff);
    color: var(--ec-primary);
}

.ec-action-btn.edit:hover {
    background: linear-gradient(135deg, var(--ec-primary), #8b5cf6);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
}

.ec-action-btn.delete {
    background: linear-gradient(135deg, #fef2f2, #fecaca);
    color: #dc2626;
}

.ec-action-btn.delete:hover {
    background: linear-gradient(135deg, #dc2626, #ef4444);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
}

/* Empty State */
.ec-empty-state {
    text-align: center;
    padding: 4rem 2rem;
    color: var(--ec-gray);
}

.ec-empty-state i {
    font-size: 4rem;
    color: #cbd5e1;
    margin-bottom: 1rem;
}

.ec-empty-state h5 {
    font-size: 1.1rem;
    font-weight: 600;
    color: var(--ec-dark);
    margin-bottom: 0.5rem;
}

.ec-empty-state p {
    font-size: 0.9rem;
    margin: 0;
}

/* Responsive */
@media (max-width: 768px) {
    .ec-header {
        flex-direction: column;
        text-align: center;
    }
    
    .ec-header-left {
        flex-direction: column;
    }
    
    .ec-table thead th,
    .ec-table tbody td {
        padding: 0.75rem 1rem;
    }
}
</style>

<!-- Header -->
<div class="ec-header">
    <div class="ec-header-left">
        <div class="ec-header-icon">
            <i class="mdi mdi-tag-multiple"></i>
        </div>
        <div class="ec-header-text">
            <h4><?php echo get_phrase('expense_category'); ?></h4>
            <p><?php echo get_phrase('manage_expense_categories'); ?></p>
        </div>
    </div>
    <div class="ec-header-actions">
        <button type="button" class="ec-btn ec-btn-primary" onclick="rightModal('<?php echo site_url('modal/popup/expense_category/create'); ?>', '<?php echo get_phrase('add_expense_category'); ?>')">
            <i class="mdi mdi-plus"></i> <?php echo get_phrase('add_expense_category'); ?>
        </button>
    </div>
</div>

<!-- Content Card -->
<div class="ec-content-card">
    <div class="ec-content-header">
        <span class="ec-content-title">
            <i class="mdi mdi-format-list-bulleted"></i>
            <?php echo get_phrase('category_list'); ?>
        </span>
    </div>
    <div class="ec-content-body">
        <div class="expense_category_content">
            <?php include 'list.php'; ?>
        </div>
    </div>
</div>

<script>
var showAllExpenseCategories = function() {
    // Show loading state
    $('.expense_category_content').html('<div class="ec-loading"><i class="mdi mdi-loading"></i> <?php echo get_phrase('loading'); ?>...</div>');
    
    var url = '<?php echo route('expense_category/list'); ?>';

    $.ajax({
        type: 'GET',
        url: url,
        success: function(response) {
            $('.expense_category_content').html(response);
            initDataTable('basic-datatable');
        },
        error: function() {
            $('.expense_category_content').html('<div class="ec-loading" style="color: #dc2626;"><i class="mdi mdi-alert-circle"></i> <?php echo get_phrase('error_loading_data'); ?></div>');
        }
    });
}
</script>
