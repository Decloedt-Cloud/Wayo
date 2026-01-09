<link rel="stylesheet" href="<?php echo base_url(); ?>assets/backend/css/responsive.css">

<style>
/* ============================================================================
   EXPENSE - MODERN DESIGN (MATCHING EXPENSE CATEGORY STYLE)
   ============================================================================ */

:root {
    --exp-primary: #6366f1;
    --exp-primary-light: #eef2ff;
    --exp-success: #059669;
    --exp-dark: #1e293b;
    --exp-gray: #64748b;
    --exp-light: #f8fafc;
    --exp-border: #e2e8f0;
    --exp-warning: #f59e0b;
}

/* Header Card */
.exp-header {
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

.exp-header-left {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.exp-header-icon {
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

.exp-header-text h4 {
    margin: 0;
    color: white;
    font-size: 1.35rem;
    font-weight: 700;
}

.exp-header-text p {
    margin: 0.25rem 0 0;
    color: rgba(255,255,255,0.7);
    font-size: 0.85rem;
}

.exp-header-actions {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
}

.exp-btn {
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

.exp-btn-primary {
    background: linear-gradient(135deg, #1e293b, #334155);
    color: white;
}

.exp-btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(30, 41, 59, 0.4);
    color: white;
}

/* Content Card */
.exp-content-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
    border: 1px solid var(--exp-border);
    overflow: hidden;
}

.exp-content-header {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    padding: 1rem 1.5rem;
    border-bottom: 1px solid var(--exp-border);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.exp-content-title {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 600;
    color: var(--exp-dark);
    font-size: 0.95rem;
}

.exp-content-title i {
    color: var(--exp-primary);
}

.exp-content-body {
    padding: 0;
}

/* Filter Section */
.exp-filter-section {
    padding: 1.5rem;
    background: white;
    border-radius: 16px;
    margin-bottom: 1.5rem;
    border: 1px solid var(--exp-border);
    box-shadow: 0 2px 12px rgba(0,0,0,0.04);
}

.exp-filter-grid {
    display: grid;
    grid-template-columns: 2fr 2fr 1fr;
    gap: 1.25rem;
    align-items: end;
}

.exp-filter-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.exp-filter-label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.8125rem;
    font-weight: 600;
    color: var(--exp-dark);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.exp-filter-label i {
    color: var(--exp-primary);
    font-size: 1rem;
}

.exp-filter-input {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 2px solid var(--exp-border);
    border-radius: 10px;
    font-size: 0.9375rem;
    background: var(--exp-light);
    color: var(--exp-dark);
    transition: all 0.2s;
    cursor: pointer;
}

.exp-filter-input:focus {
    outline: none;
    border-color: var(--exp-primary);
    background: white;
    box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
}

.exp-filter-input:hover {
    border-color: #cbd5e1;
}

.exp-filter-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    background: linear-gradient(135deg, var(--exp-primary), #8b5cf6);
    color: white;
    border: none;
    border-radius: 10px;
    font-size: 0.9375rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    height: fit-content;
}

.exp-filter-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
}

.exp-filter-btn i {
    font-size: 1.125rem;
}

/* Loading State */
.exp-loading {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 3rem;
    color: var(--exp-gray);
}

.exp-loading i {
    font-size: 2rem;
    animation: exp-spin 1s linear infinite;
    margin-right: 0.75rem;
}

@keyframes exp-spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

/* Date Range Picker Styling */
#reportrange {
    background: var(--exp-light) !important;
    border: 2px solid var(--exp-border) !important;
    border-radius: 10px !important;
    padding: 0.75rem 1rem !important;
    font-size: 0.9375rem !important;
    cursor: pointer;
    transition: all 0.2s;
}

#reportrange:hover {
    border-color: #cbd5e1 !important;
}

#reportrange:focus,
#reportrange:active {
    border-color: var(--exp-primary) !important;
    box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1) !important;
}

#reportrange i {
    color: var(--exp-primary);
    margin-right: 0.5rem;
}

/* Responsive */
@media (max-width: 1024px) {
    .exp-filter-grid {
        grid-template-columns: 1fr 1fr;
    }
    
    .exp-filter-btn {
        grid-column: span 2;
    }
}

@media (max-width: 768px) {
    .exp-header {
        flex-direction: column;
        text-align: center;
    }
    
    .exp-header-left {
        flex-direction: column;
    }
    
    .exp-filter-grid {
        grid-template-columns: 1fr;
    }
    
    .exp-filter-btn {
        grid-column: span 1;
    }
}
</style>

<!-- Header -->
<div class="exp-header">
    <div class="exp-header-left">
        <div class="exp-header-icon">
            <i class="fas fa-money-bill-wave"></i>
        </div>
        <div class="exp-header-text">
            <h4><?php echo get_phrase('expense'); ?></h4>
            <p><?php echo get_phrase('manage_all_expenses'); ?></p>
        </div>
    </div>
    <div class="exp-header-actions">
        <button type="button" class="exp-btn exp-btn-primary" onclick="rightModal('<?php echo site_url('modal/popup/expense/create'); ?>', '<?php echo get_phrase('add_new_expense'); ?>')">
            <i class="mdi mdi-plus"></i> <?php echo get_phrase('add_new_expense'); ?>
        </button>
    </div>
</div>

<!-- Filter Section -->
<div class="exp-filter-section">
    <div class="exp-filter-grid">
        <div class="exp-filter-group">
            <label class="exp-filter-label">
                <i class="mdi mdi-calendar-range"></i>
                <span><?php echo get_phrase('date_range'); ?></span>
            </label>
            <div id="reportrange" class="exp-filter-input" data-toggle="date-picker-range" data-target-display="#selectedValue" data-cancel-class="btn-light">
                <i class="mdi mdi-calendar"></i>
                <span id="selectedValue"><?php echo date('F d, Y', strtotime(' -30 day')).' - '.date('F d, Y'); ?></span>
            </div>
        </div>
        
        <div class="exp-filter-group">
            <label class="exp-filter-label">
                <i class="mdi mdi-tag-outline"></i>
                <span><?php echo get_phrase('expense_category'); ?></span>
            </label>
            <select class="exp-filter-input" name="expense_category_id" id="expense_category_id">
                <option value="all"><?php echo get_phrase('all_categories'); ?></option>
                <?php
                $expense_categories = $this->crud_model->get_expense_categories()->result_array();
                foreach ($expense_categories as $expense_category): ?>
                <option value="<?php echo $expense_category['id']; ?>"><?php echo $expense_category['name']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="exp-filter-group">
            <button type="button" class="exp-filter-btn" onclick="showAllExpenses()">
                <i class="mdi mdi-filter-outline"></i>
                <span><?php echo get_phrase('filter'); ?></span>
            </button>
        </div>
    </div>
</div>

<!-- Content Card -->
<div class="exp-content-card">
    <div class="exp-content-header">
        <span class="exp-content-title">
            <i class="mdi mdi-format-list-bulleted"></i>
            <?php echo get_phrase('expense_list'); ?>
        </span>
    </div>
    <div class="exp-content-body">
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
    // Show loading state
    $('.expense_content').html('<div class="exp-loading"><i class="mdi mdi-loading"></i> <?php echo get_phrase('loading'); ?>...</div>');
    
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
            $('.expense_content').html('<div class="exp-loading" style="color: #dc2626;"><i class="mdi mdi-alert-circle"></i> <?php echo get_phrase('error_loading_data'); ?></div>');
        }
    });
}
</script>
