<link rel="stylesheet" href="<?php echo base_url(); ?>assets/backend/css/responsive.min.css">

<style>
/* ============================================================================
   EXPENSE INDEX - MODERN FILTER DESIGN
   ============================================================================ */

:root {
    --idx-primary: #6366f1;
    --idx-primary-light: #eef2ff;
    --idx-success: #059669;
    --idx-dark: #1e293b;
    --idx-gray: #64748b;
    --idx-light: #f8fafc;
    --idx-border: #e2e8f0;
}

/* Header Card */
.idx-header {
    background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
    border-radius: 16px;
    padding: 1.5rem 2rem;
    margin-bottom: 1.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
}

.idx-header-left {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.idx-header-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    background: rgba(255,255,255,0.1);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
}

.idx-header-text h4 {
    margin: 0;
    color: white;
    font-size: 1.35rem;
    font-weight: 700;
}

.idx-header-text p {
    margin: 0.25rem 0 0;
    color: #94a3b8;
    font-size: 0.85rem;
}

.idx-header-actions {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
}

.idx-btn {
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

.idx-btn-primary {
    background: linear-gradient(135deg, var(--idx-primary), #8b5cf6);
    color: white;
    border: 1px solid rgba(255, 255, 255, 0.25);
    backdrop-filter: blur(10px);
}

.idx-btn-primary:hover {
    transform: translateY(-3px) scale(1.02);
    box-shadow: 
        0 12px 30px rgba(99, 102, 241, 0.6),
        0 4px 15px rgba(139, 92, 246, 0.4),
        inset 0 1px 0 rgba(255, 255, 255, 0.2);
    background: linear-gradient(135deg, #8b5cf6, var(--idx-primary));
}

/* Filter Panel */
.idx-filter-panel {
    background: white;
    border-radius: 16px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
    border: 1px solid var(--idx-border);
    margin-bottom: 1.5rem;
    overflow: hidden;
}

.idx-filter-header {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    padding: 1rem 1.5rem;
    border-bottom: 1px solid var(--idx-border);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.idx-filter-title {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 600;
    color: var(--idx-dark);
    font-size: 0.95rem;
}

.idx-filter-title i {
    color: var(--idx-primary);
}

.idx-filter-body {
    padding: 1.5rem;
}

.idx-filter-grid {
    display: grid;
    grid-template-columns: 2fr 1fr 1fr;
    gap: 1rem;
    align-items: end;
}

@media (max-width: 1200px) {
    .idx-filter-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .idx-filter-grid {
        grid-template-columns: 1fr;
    }
}

.idx-filter-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.idx-filter-label {
    font-size: 0.75rem;
    font-weight: 600;
    color: var(--idx-gray);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    display: flex;
    align-items: center;
    gap: 0.35rem;
}

.idx-filter-label i {
    font-size: 0.9rem;
    color: var(--idx-primary);
}

.idx-date-picker {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1rem;
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    border: 2px solid var(--idx-border);
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.2s;
    font-size: 0.9rem;
    color: var(--idx-dark);
}

.idx-date-picker:hover {
    border-color: var(--idx-primary);
    background: white;
}

.idx-date-picker i {
    color: var(--idx-primary);
}

.idx-date-picker .idx-date-text {
    flex: 1;
    text-align: center;
    font-weight: 500;
}

.idx-select {
    padding: 0.75rem 1rem;
    border: 2px solid var(--idx-border);
    border-radius: 10px;
    font-size: 0.9rem;
    background: white;
    color: var(--idx-dark);
    cursor: pointer;
    transition: all 0.2s;
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%234f46e5' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 1rem center;
    padding-right: 2.5rem;
}

.idx-select:focus {
    outline: none;
    border-color: var(--idx-primary);
    box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
}

.idx-filter-btn {
    padding: 0.75rem 1.5rem;
    border-radius: 10px;
    font-size: 0.9rem;
    font-weight: 600;
    border: none;
    cursor: pointer;
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    color: white;
}

.idx-filter-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
}

/* Content Card */
.idx-content-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
    border: 1px solid var(--idx-border);
    overflow: hidden;
}

.idx-content-body {
    padding: 0;
}

/* Loading State */
.idx-loading {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 3rem;
    color: var(--idx-gray);
}

.idx-loading i {
    font-size: 2rem;
    animation: spin 1s linear infinite;
    margin-right: 0.75rem;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
</style>

<!-- Header -->
<div class="idx-header">
    <div class="idx-header-left">
        <div class="idx-header-icon">
            <i class="fa-solid fa-money-bill-wave"></i>
        </div>
        <div class="idx-header-text">
            <h4><?php echo get_phrase('expense'); ?></h4>
            <p><?php echo get_phrase('manage_expenses'); ?></p>
        </div>
    </div>
    <div class="idx-header-actions">
        <button type="button" class="idx-btn idx-btn-primary" onclick="rightModal('<?php echo site_url('modal/popup/expense/create'); ?>', '<?php echo get_phrase('add_new_expense'); ?>')">
            <i class="fa-solid fa-plus"></i> <?php echo get_phrase('add_new_expense'); ?>
        </button>
    </div>
</div>

<!-- Filter Panel -->
<div class="idx-filter-panel">
    <div class="idx-filter-header">
        <span class="idx-filter-title">
            <i class="fa-solid fa-filter"></i>
            <?php echo get_phrase('filter_expenses'); ?>
        </span>
    </div>
    <div class="idx-filter-body">
        <div class="idx-filter-grid">
            <!-- Date Range -->
            <div class="idx-filter-group">
                <label class="idx-filter-label">
                    <i class="fa-solid fa-calendar-days"></i>
                    <?php echo get_phrase('date_range'); ?>
                </label>
                <div id="reportrange" class="idx-date-picker" data-toggle="date-picker-range" data-target-display="#selectedValue" data-cancel-class="btn-light">
                    <i class="fa-solid fa-calendar"></i>
                    <span id="selectedValue" class="idx-date-text"><?php echo date('F d, Y', strtotime(' -30 day')).' - '.date('F d, Y'); ?></span>
                    <i class="fa-solid fa-chevron-down"></i>
                </div>
            </div>
            
            <!-- Category Filter -->
            <div class="idx-filter-group">
                <label class="idx-filter-label">
                    <i class="fa-solid fa-tags"></i>
                    <?php echo get_phrase('expense_category'); ?>
                </label>
                <select name="expense_category_id" id="expense_category_id" class="idx-select">
                    <option value="all"><?php echo get_phrase('all_categories'); ?></option>
                    <?php
                    $expense_categories = $this->crud_model->get_expense_categories()->result_array();
                    foreach ($expense_categories as $expense_category): ?>
                    <option value="<?php echo $expense_category['id']; ?>"><?php echo $expense_category['name']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <!-- Filter Button -->
            <div class="idx-filter-group">
                <label class="idx-filter-label">&nbsp;</label>
                <button type="button" class="idx-filter-btn" onclick="showAllExpenses()">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <?php echo get_phrase('filter'); ?>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Content Card -->
<div class="idx-content-card">
    <div class="idx-content-body">
        <div class="expense_content">
            <?php include 'list.php'; ?>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('select.select2:not(.normal)').each(function () { $(this).select2({ dropdownParent: '#right-modal' }); });
});

var showAllExpenses = function () {
    $('.expense_content').html('<div class="idx-loading"><i class="fa-solid fa-spinner fa-spin"></i> '+<?php echo js_phrase('loading'); ?>+'...</div>');

    var url = '<?php echo route('expense/list'); ?>';
    $.ajax({
        type : 'GET',
        url: url,
        data : {date : $('#selectedValue').text(), expense_category_id : $('#expense_category_id').val()},
        success : function(response) {
            $('.expense_content').html(response);
            initDataTable("basic-datatable");
        },
        error: function() {
            $('.expense_content').html('<div class="idx-loading" style="color: #dc2626;"><i class="fa-solid fa-circle-exclamation"></i> '+<?php echo js_phrase('error_loading_data'); ?>+'</div>');
        }
    });
}
</script>