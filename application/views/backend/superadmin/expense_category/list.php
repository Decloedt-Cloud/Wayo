<?php $expense_categories = $this->crud_model->get_expense_categories()->result_array(); ?>

<style>
/* ============================================================================
   INVOICE LIST - COMPACT MODERN STYLES (Adapted for Expense Categories)
   ============================================================================ */
:root {
    --ai-primary: #6366f1;
    --ai-primary-light: #eef2ff;
    --ai-success: #059669;
    --ai-success-light: #d1fae5;
    --ai-danger: #dc2626;
    --ai-danger-light: #fee2e2;
    --ai-warning: #d97706;
    --ai-dark: #1e293b;
    --ai-gray: #64748b;
    --ai-light: #f8fafc;
    --ai-border: #e2e8f0;
}

/* Stats Grid */
.ai-stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 0.75rem;
    margin-bottom: 1rem;
}
@media (max-width: 992px) { .ai-stats-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 576px) { .ai-stats-grid { grid-template-columns: 1fr; } }

.ai-stat-card {
    background: white;
    border-radius: 10px;
    padding: 1rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.06);
    border: 1px solid var(--ai-border);
    position: relative;
    overflow: hidden;
}
.ai-stat-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 3px;
}
.ai-stat-card.blue::before { background: linear-gradient(90deg, #3b82f6, #60a5fa); }

.ai-stat-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem; }
.ai-stat-icon {
    width: 36px; height: 36px;
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.1rem;
}
.ai-stat-card.blue .ai-stat-icon { background: #dbeafe; color: #2563eb; }

.ai-stat-value { font-size: 1.4rem; font-weight: 700; color: var(--ai-dark); line-height: 1.2; }
.ai-stat-label { font-size: 0.7rem; color: var(--ai-gray); text-transform: uppercase; letter-spacing: 0.5px; }

/* Table Container */
.ai-table-container {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    overflow: hidden;
    border: 1px solid var(--ai-border);
}

/* Toolbar */
.ai-table-toolbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem 1rem;
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    border-bottom: 1px solid var(--ai-border);
    flex-wrap: wrap;
    gap: 0.75rem;
}

.ai-search-box {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    background: white;
    border: 1px solid var(--ai-border);
    border-radius: 8px;
    padding: 0.5rem 0.75rem;
    min-width: 250px;
    transition: all 0.2s;
}
.ai-search-box:focus-within {
    border-color: var(--ai-primary);
    box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
}
.ai-search-box i { color: var(--ai-gray); }
.ai-search-box input {
    border: none;
    outline: none;
    font-size: 0.85rem;
    width: 100%;
    background: transparent;
}

.ai-filter-chips { display: flex; gap: 0.4rem; flex-wrap: wrap; }
.ai-chip {
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    padding: 0.35rem 0.7rem;
    border-radius: 20px;
    font-size: 0.7rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
    border: 1px solid var(--ai-border);
    background: white;
    color: var(--ai-gray);
}
.ai-chip:hover, .ai-chip.active {
    border-color: var(--ai-primary);
    color: var(--ai-primary);
    background: var(--ai-primary-light);
}
.ai-chip .count {
    background: var(--ai-border);
    padding: 0.1rem 0.35rem;
    border-radius: 10px;
    font-size: 0.6rem;
}
.ai-chip.active .count { background: var(--ai-primary); color: white; }

/* Results Info */
.ai-results-bar {
    padding: 0.5rem 1rem;
    background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
    border-bottom: 1px solid #bbf7d0;
    font-size: 0.8rem;
    color: #166534;
    display: none;
}
.ai-results-bar.active { display: flex; justify-content: space-between; align-items: center; }
.ai-results-bar.no-results {
    background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
    border-color: #fecaca;
    color: #991b1b;
}
.ai-results-bar button {
    background: none; border: none; color: inherit;
    cursor: pointer; font-size: 0.75rem; text-decoration: underline;
}

/* Table */
.ai-table { width: 100%; border-collapse: collapse; }
.ai-table thead { background: linear-gradient(135deg, #1e293b 0%, #334155 100%); }
.ai-table thead th {
    padding: 0.85rem 0.75rem;
    text-align: left;
    color: white;
    font-weight: 600;
    font-size: 0.7rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border: none;
}
.ai-table tbody tr {
    border-bottom: 1px solid var(--ai-border);
    transition: background 0.2s;
}
.ai-table tbody tr:hover { background: #fafbfc; }
.ai-table tbody td {
    padding: 0.85rem 0.75rem;
    vertical-align: middle;
    font-size: 0.85rem;
}

/* Actions */
.ai-actions { display: flex; gap: 0.3rem; }
.ai-action {
    width: 28px; height: 28px;
    border-radius: 6px;
    display: inline-flex; align-items: center; justify-content: center;
    border: none; cursor: pointer;
    transition: all 0.2s;
    text-decoration: none;
    font-size: 0.85rem;
}
.ai-action.edit { background: var(--ai-primary-light); color: var(--ai-primary); }
.ai-action.delete { background: #fee2e2; color: #dc2626; }
.ai-action:hover { transform: translateY(-1px); box-shadow: 0 2px 6px rgba(0,0,0,0.15); }

/* Empty */
.ai-empty {
    text-align: center;
    padding: 3rem 2rem;
    color: var(--ai-gray);
}
.ai-empty i { font-size: 3rem; color: #cbd5e1; margin-bottom: 0.75rem; }
.ai-empty-title { font-size: 1.1rem; font-weight: 600; color: var(--ai-dark); }

/* Footer */
.ai-table-footer {
    padding: 0.75rem 1rem;
    background: var(--ai-light);
    border-top: 1px solid var(--ai-border);
    font-size: 0.8rem;
    color: var(--ai-gray);
}
</style>

<!-- Stats Grid -->
<div class="ai-stats-grid">
    <div class="ai-stat-card blue">
        <div class="ai-stat-header">
            <div class="ai-stat-icon"><i class="fa-solid fa-tags"></i></div>
        </div>
        <div class="ai-stat-value"><?php echo count($expense_categories); ?></div>
        <div class="ai-stat-label"><?php echo get_phrase('total_categories'); ?></div>
    </div>
</div>

<!-- Table -->
<div class="ai-table-container">
    <div class="ai-table-toolbar">
         <div class="ai-search-box">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" id="quickSearchCat" placeholder="<?php echo get_phrase('search'); ?>..." onkeyup="quickSearchCat()">
        </div>
         <div class="ai-filter-chips">
            <span class="ai-chip active" data-filter="all">
                <i class="fa-solid fa-list"></i> <?php echo get_phrase('all'); ?>
                <span class="count"><?php echo count($expense_categories); ?></span>
            </span>
        </div>
    </div>
    
    <div class="ai-results-bar" id="resultsBarCat">
        <span id="resultsTextCat"></span>
        <button onclick="resetFiltersCat()"><?php echo get_phrase('clear'); ?></button>
    </div>

    <table class="ai-table" id="expenseCatTable">
        <thead>
            <tr>
                <th><i class="fa-solid fa-tags thead-icon"></i> <?php echo get_phrase('name'); ?></th>
                <th><?php echo get_phrase('option'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($expense_categories)): ?>
             <tr><td colspan="2">
                <div class="ai-empty">
                    <i class="fa-solid fa-tags"></i>
                    <div class="ai-empty-title"><?php echo get_phrase('no_categories_found'); ?></div>
                </div>
            </td></tr>
            <?php else: ?>
                <?php foreach ($expense_categories as $expense_category): ?>
                <tr>
                    <td>
                        <div style="font-weight: 600; color: var(--ai-dark); font-size: 0.85rem;">
                            <?php echo $expense_category['name']; ?>
                        </div>
                    </td>
                    <td>
                        <div class="ai-actions">
                            <a href="javascript:void(0);" class="ai-action edit" onclick="rightModal('<?php echo site_url('modal/popup/expense_category/edit/'.$expense_category['id'])?>', '<?php echo get_phrase('update_expense_category'); ?>');" title="<?php echo get_phrase('edit'); ?>"><i class="fa-solid fa-pencil"></i></a>
                            <a href="javascript:void(0);" class="ai-action delete" onclick="confirmModal('<?php echo route('expense_category/delete/'.$expense_category['id']); ?>', showAllExpenseCategories )" title="<?php echo get_phrase('delete'); ?>"><i class="fa-solid fa-trash"></i></a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
    
    <div class="ai-table-footer">
        <?php echo get_phrase('showing'); ?> <strong><?php echo count($expense_categories); ?></strong> <?php echo get_phrase('categories'); ?>
    </div>
</div>

<script>
function quickSearchCat() {
    const q = document.getElementById('quickSearchCat').value.toLowerCase();
    const rows = document.querySelectorAll('#expenseCatTable tbody tr');
    let visible = 0;
    
    rows.forEach(row => {
        if (row.querySelector('td[colspan]')) return; 
        const match = row.textContent.toLowerCase().includes(q);
        row.style.display = match ? '' : 'none';
        if (match) visible++;
    });
    
    const bar = document.getElementById('resultsBarCat');
    const text = document.getElementById('resultsTextCat');
    
    if (q) {
        bar.classList.add('active');
        bar.classList.toggle('no-results', visible === 0);
        text.innerHTML = visible === 0 
            ? '<i class="fa-solid fa-circle-exclamation"></i> <?php echo get_phrase('no_results'); ?>'
            : `<i class="fa-solid fa-circle-check"></i> <strong>${visible}</strong> <?php echo get_phrase('found'); ?>`;
    } else {
        bar.classList.remove('active');
    }
}

function resetFiltersCat() {
    document.getElementById('quickSearchCat').value = '';
    document.querySelectorAll('#expenseCatTable tbody tr').forEach(r => r.style.display = '');
    document.getElementById('resultsBarCat').classList.remove('active');
}
</script>