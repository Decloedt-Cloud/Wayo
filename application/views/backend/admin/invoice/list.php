<?php
/**
 * Admin Invoice List - Professional Edition
 * Optimized for use with index.php filters
 */

$invoices = $this->crud_model->get_invoice_by_date_range($date_from, $date_to, $selected_class, $selected_status)->result_array();

// Get school currency for costs display
$school_currency = $this->db->get_where('settings_school', array('school_id' => school_id()))->row('system_currency') ?? 'EUR';

// ============================================================================
// STATISTICS CALCULATION
// ============================================================================
$stats = [
    'total' => count($invoices),
    'paid' => 0,
    'unpaid' => 0,
    'overdue' => 0,
    'total_amount' => 0,
    'total_paid' => 0,
    'total_due' => 0,
    'fx_payments' => 0,
    'total_cost' => 0,
    'currencies' => [],
    'methods' => []
];

// Calculate total expenses/costs for the same period
$expenses = $this->crud_model->get_expense($date_from, $date_to)->result_array();
foreach ($expenses as $expense) {
    $stats['total_cost'] += (float)$expense['amount'];
}

$today = time();
foreach ($invoices as $inv) {
    $amount = (float)$inv['total_amount'];
    $paid = (float)$inv['paid_amount'];
    $stats['total_amount'] += $amount;
    $stats['total_paid'] += $paid;
    
    $curr = isset($inv['currency']) ? $inv['currency'] : 'EUR';
    if (!isset($stats['currencies'][$curr])) $stats['currencies'][$curr] = 0;
    $stats['currencies'][$curr] += $amount;
    
    if (!empty($inv['payment_method'])) {
        $method = ucfirst($inv['payment_method']);
        if (!isset($stats['methods'][$method])) $stats['methods'][$method] = 0;
        $stats['methods'][$method]++;
    }
    
    if (strtolower($inv['status']) == 'paid') {
        $stats['paid']++;
        if (isset($inv['conversion_applied']) && $inv['conversion_applied'] == 1) {
            $stats['fx_payments']++;
        }
    } else {
        $stats['unpaid']++;
        if (($today - $inv['created_at']) > (30 * 24 * 60 * 60)) {
            $stats['overdue']++;
        }
    }
}
$stats['total_due'] = $stats['total_amount'] - $stats['total_paid'];
$stats['payment_rate'] = $stats['total'] > 0 ? round(($stats['paid'] / $stats['total']) * 100, 1) : 0;
?>

<style>
/* ============================================================================
   INVOICE LIST - COMPACT MODERN STYLES
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
    grid-template-columns: repeat(7, 1fr);
    gap: 0.75rem;
    margin-bottom: 1rem;
}
@media (max-width: 1400px) { .ai-stats-grid { grid-template-columns: repeat(4, 1fr); } }
@media (max-width: 992px) { .ai-stats-grid { grid-template-columns: repeat(3, 1fr); } }
@media (max-width: 768px) { .ai-stats-grid { grid-template-columns: repeat(2, 1fr); } }

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
.ai-stat-card.purple::before { background: linear-gradient(90deg, #8b5cf6, #a78bfa); }
.ai-stat-card.green::before { background: linear-gradient(90deg, #10b981, #34d399); }
.ai-stat-card.red::before { background: linear-gradient(90deg, #ef4444, #f87171); }
.ai-stat-card.blue::before { background: linear-gradient(90deg, #3b82f6, #60a5fa); }
.ai-stat-card.orange::before { background: linear-gradient(90deg, #f59e0b, #fbbf24); }
.ai-stat-card.cyan::before { background: linear-gradient(90deg, #06b6d4, #22d3ee); }
.ai-stat-card.pink::before { background: linear-gradient(90deg, #ec4899, #f472b6); }

.ai-stat-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem; }
.ai-stat-icon {
    width: 36px; height: 36px;
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.1rem;
}
.ai-stat-card.purple .ai-stat-icon { background: #ede9fe; color: #7c3aed; }
.ai-stat-card.green .ai-stat-icon { background: #d1fae5; color: #059669; }
.ai-stat-card.red .ai-stat-icon { background: #fee2e2; color: #dc2626; }
.ai-stat-card.blue .ai-stat-icon { background: #dbeafe; color: #2563eb; }
.ai-stat-card.orange .ai-stat-icon { background: #fef3c7; color: #d97706; }
.ai-stat-card.cyan .ai-stat-icon { background: #cffafe; color: #0891b2; }
.ai-stat-card.pink .ai-stat-icon { background: #fce7f3; color: #db2777; }

.ai-stat-trend { font-size: 0.65rem; padding: 0.15rem 0.4rem; border-radius: 4px; font-weight: 600; }
.ai-stat-trend.up { background: #d1fae5; color: #059669; }
.ai-stat-trend.down { background: #fee2e2; color: #dc2626; }

.ai-stat-value { font-size: 1.4rem; font-weight: 700; color: var(--ai-dark); line-height: 1.2; }
.ai-stat-label { font-size: 0.7rem; color: var(--ai-gray); text-transform: uppercase; letter-spacing: 0.5px; }

.ai-stat-progress { margin-top: 0.5rem; height: 3px; background: var(--ai-border); border-radius: 2px; overflow: hidden; }
.ai-stat-progress-bar { height: 100%; border-radius: 2px; }
.ai-stat-card.green .ai-stat-progress-bar { background: linear-gradient(90deg, #10b981, #34d399); }

/* Mini Charts Row */
.ai-stats-secondary {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 0.75rem;
    margin-bottom: 1rem;
}
@media (max-width: 992px) { .ai-stats-secondary { grid-template-columns: 1fr; } }

.ai-mini-chart {
    background: white;
    border-radius: 10px;
    padding: 0.75rem 1rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.06);
    border: 1px solid var(--ai-border);
}
.ai-mini-chart-header { margin-bottom: 0.5rem; }
.ai-mini-chart-title { font-size: 0.7rem; font-weight: 600; color: var(--ai-dark); text-transform: uppercase; letter-spacing: 0.5px; }
.ai-mini-chart-items { display: flex; flex-wrap: wrap; gap: 0.4rem; }
.ai-chart-item {
    display: inline-flex; align-items: center; gap: 0.35rem;
    background: var(--ai-light); padding: 0.3rem 0.6rem; border-radius: 5px; font-size: 0.75rem;
}
.ai-chart-item-dot { width: 6px; height: 6px; border-radius: 50%; }
.ai-chart-item-value { font-weight: 600; color: var(--ai-dark); }
.ai-chart-item-label { color: var(--ai-gray); }

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

/* Invoice Badge */
.ai-inv-badge {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 0.3rem 0.5rem;
    border-radius: 5px;
    font-weight: 600;
    font-size: 0.75rem;
    font-family: monospace;
}
.ai-inv-date { font-size: 0.7rem; color: var(--ai-gray); margin-top: 0.25rem; }

/* Student Cell */
.ai-student { display: flex; align-items: center; gap: 0.6rem; }
.ai-avatar {
    width: 32px; height: 32px;
    border-radius: 6px;
    background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%);
    display: flex; align-items: center; justify-content: center;
    font-weight: 700; color: #4f46e5; font-size: 0.7rem;
}
.ai-student-name { font-weight: 600; color: var(--ai-dark); font-size: 0.85rem; }
.ai-student-email { font-size: 0.7rem; color: var(--ai-gray); }
.ai-student-class {
    display: inline-flex; align-items: center; gap: 0.2rem;
    font-size: 0.65rem; color: var(--ai-gray);
    background: var(--ai-light); padding: 0.15rem 0.4rem; border-radius: 3px;
    margin-top: 0.2rem;
}

/* Amount */
.ai-amount { font-weight: 700; color: var(--ai-dark); }
.ai-currency { font-size: 0.75rem; color: var(--ai-gray); font-weight: 400; }

/* Paid Cell */
.ai-paid { font-weight: 600; color: var(--ai-success); }
.ai-paid-zero { color: var(--ai-gray); font-weight: 400; }
.ai-fx-badge {
    display: inline-flex; align-items: center; gap: 0.2rem;
    background: #dbeafe; padding: 0.15rem 0.4rem; border-radius: 3px;
    font-size: 0.65rem; color: #1d4ed8; margin-top: 0.2rem;
}
.ai-fx-rate {
    background: #fef3c7; color: #b45309;
    padding: 0.1rem 0.3rem; border-radius: 3px;
    font-size: 0.6rem; margin-top: 0.15rem; display: inline-block;
}

/* Status */
.ai-status {
    display: inline-flex; align-items: center; gap: 0.3rem;
    padding: 0.3rem 0.6rem; border-radius: 15px;
    font-size: 0.7rem; font-weight: 600;
}
.ai-status.paid { background: var(--ai-success-light); color: var(--ai-success); }
.ai-status.unpaid { background: var(--ai-danger-light); color: var(--ai-danger); }
.ai-status.overdue { background: #fef2f2; color: #991b1b; border: 1px dashed #fca5a5; }
.ai-status-dot { width: 5px; height: 5px; border-radius: 50%; background: currentColor; }
.ai-fx-tag {
    display: inline-flex; align-items: center; gap: 0.2rem;
    margin-top: 0.3rem; padding: 0.15rem 0.4rem;
    background: #e0e7ff; border-radius: 3px;
    font-size: 0.6rem; color: #4338ca; font-weight: 600;
}

/* Method Badge */
.ai-method {
    display: inline-flex; align-items: center; gap: 0.3rem;
    padding: 0.25rem 0.5rem; border-radius: 5px;
    font-size: 0.7rem; font-weight: 500;
    background: var(--ai-light); color: var(--ai-gray);
}
.ai-method.stripe { background: #e0e7ff; color: #4f46e5; }
.ai-method.paypal { background: #dbeafe; color: #1d4ed8; }
.ai-method.bank { background: #d1fae5; color: #059669; }

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
.ai-action.view { background: var(--ai-primary-light); color: var(--ai-primary); } /* Primary Chart Color */
.ai-action.pdf { background: var(--ai-primary-light); color: var(--ai-primary); } /* Primary Chart Color */
.ai-action.edit { background: var(--ai-primary-light); color: var(--ai-primary); } /* Primary Chart Color */
.ai-action.delete { background: #fee2e2; color: #dc2626; } /* Red */
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
    <div class="ai-stat-card purple">
        <div class="ai-stat-header">
            <div class="ai-stat-icon"><i class="fa-solid fa-file-invoice"></i></div>
        </div>
        <div class="ai-stat-value"><?php echo $stats['total']; ?></div>
        <div class="ai-stat-label"><?php echo get_phrase('total'); ?></div>
    </div>
    <div class="ai-stat-card green">
        <div class="ai-stat-header">
            <div class="ai-stat-icon"><i class="fa-solid fa-circle-check"></i></div>
            <span class="ai-stat-trend up"><?php echo $stats['payment_rate']; ?>%</span>
        </div>
        <div class="ai-stat-value"><?php echo $stats['paid']; ?></div>
        <div class="ai-stat-label"><?php echo get_phrase('paid'); ?></div>
        <div class="ai-stat-progress"><div class="ai-stat-progress-bar" style="width: <?php echo $stats['payment_rate']; ?>%;"></div></div>
    </div>
    <div class="ai-stat-card red">
        <div class="ai-stat-header">
            <div class="ai-stat-icon"><i class="fa-solid fa-triangle-exclamation"></i></div>
            <?php if ($stats['overdue'] > 0): ?><span class="ai-stat-trend down"><?php echo $stats['overdue']; ?></span><?php endif; ?>
        </div>
        <div class="ai-stat-value"><?php echo $stats['unpaid']; ?></div>
        <div class="ai-stat-label"><?php echo get_phrase('unpaid'); ?></div>
    </div>
    <div class="ai-stat-card blue">
        <div class="ai-stat-header"><div class="ai-stat-icon"><i class="fa-solid fa-money-bill-wave"></i></div></div>
        <div class="ai-stat-value"><?php echo number_format($stats['total_paid'], 0); ?></div>
        <div class="ai-stat-label"><?php echo get_phrase('collected'); ?></div>
    </div>
    <div class="ai-stat-card orange">
        <div class="ai-stat-header"><div class="ai-stat-icon"><i class="fa-solid fa-file-invoice-dollar"></i></div></div>
        <div class="ai-stat-value"><?php echo number_format($stats['total_due'], 0); ?></div>
        <div class="ai-stat-label"><?php echo get_phrase('outstanding'); ?></div>
    </div>
    <div class="ai-stat-card cyan">
        <div class="ai-stat-header"><div class="ai-stat-icon"><i class="fa-solid fa-arrow-right-arrow-left"></i></div></div>
        <div class="ai-stat-value"><?php echo $stats['fx_payments']; ?></div>
        <div class="ai-stat-label"><?php echo get_phrase('Exchanges'); ?></div>
    </div>
    <div class="ai-stat-card pink">
        <div class="ai-stat-header"><div class="ai-stat-icon"><i class="fa-solid fa-money-bill-transfer"></i></div></div>
        <div class="ai-stat-value"><?php echo number_format($stats['total_cost'], 0); ?> <small style="font-size: 0.7rem; color: var(--ai-gray);"><?php echo $school_currency; ?></small></div>
        <div class="ai-stat-label"><?php echo get_phrase('cost'); ?></div>
    </div>
</div>

<!-- Mini Charts -->
<div class="ai-stats-secondary">
    <div class="ai-mini-chart">
        <div class="ai-mini-chart-header"><span class="ai-mini-chart-title"><i class="fa-solid fa-coins"></i> <?php echo get_phrase('by_currency'); ?></span></div>
        <div class="ai-mini-chart-items">
            <?php $colors = ['#4f46e5', '#059669', '#d97706', '#dc2626', '#0891b2']; $i = 0;
            foreach ($stats['currencies'] as $curr => $amount): ?>
                <div class="ai-chart-item">
                    <span class="ai-chart-item-dot" style="background: <?php echo $colors[$i % 5]; ?>;"></span>
                    <span class="ai-chart-item-value"><?php echo number_format($amount, 0); ?></span>
                    <span class="ai-chart-item-label"><?php echo $curr; ?></span>
                </div>
            <?php $i++; endforeach; ?>
        </div>
    </div>
    <div class="ai-mini-chart">
        <div class="ai-mini-chart-header"><span class="ai-mini-chart-title"><i class="fa-solid fa-credit-card"></i> <?php echo get_phrase('methods'); ?></span></div>
        <div class="ai-mini-chart-items">
            <?php foreach ($stats['methods'] as $method => $count): ?>
                <div class="ai-chart-item">
                    <span class="ai-chart-item-dot" style="background: #4f46e5;"></span>
                    <span class="ai-chart-item-value"><?php echo $count; ?></span>
                    <span class="ai-chart-item-label"><?php echo $method; ?></span>
                </div>
            <?php endforeach; ?>
            <?php if (empty($stats['methods'])): ?><span class="text-muted">—</span><?php endif; ?>
        </div>
    </div>
    <div class="ai-mini-chart">
        <div class="ai-mini-chart-header"><span class="ai-mini-chart-title"><i class="fa-solid fa-chart-pie"></i> <?php echo get_phrase('summary'); ?></span></div>
        <div class="ai-mini-chart-items">
            <div class="ai-chart-item">
                <span class="ai-chart-item-dot" style="background: #4f46e5;"></span>
                <span class="ai-chart-item-value"><?php echo number_format($stats['total_amount'], 0); ?></span>
                <span class="ai-chart-item-label"><?php echo get_phrase('total'); ?></span>
            </div>
            <div class="ai-chart-item">
                <span class="ai-chart-item-dot" style="background: #059669;"></span>
                <span class="ai-chart-item-value"><?php echo $stats['payment_rate']; ?>%</span>
                <span class="ai-chart-item-label"><?php echo get_phrase('rate'); ?></span>
            </div>
        </div>
    </div>
</div>

<!-- Table -->
<div class="ai-table-container">
    <!-- Toolbar -->
    <div class="ai-table-toolbar">
        <div class="ai-search-box">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" id="quickSearch" placeholder="<?php echo get_phrase('search'); ?>..." onkeyup="quickSearch()">
        </div>
        <div class="ai-filter-chips">
            <span class="ai-chip active" data-filter="all" onclick="filterStatus('all', this)">
                <i class="fa-solid fa-list"></i> <?php echo get_phrase('all'); ?>
                <span class="count"><?php echo $stats['total']; ?></span>
            </span>
            <span class="ai-chip" data-filter="paid" onclick="filterStatus('paid', this)">
                <i class="fa-solid fa-check"></i> <?php echo get_phrase('paid'); ?>
                <span class="count"><?php echo $stats['paid']; ?></span>
            </span>
            <span class="ai-chip" data-filter="unpaid" onclick="filterStatus('unpaid', this)">
                <i class="fa-solid fa-clock"></i> <?php echo get_phrase('pending'); ?>
                <span class="count"><?php echo $stats['unpaid']; ?></span>
            </span>
            <?php if ($stats['fx_payments'] > 0): ?>
            <span class="ai-chip" data-filter="fx" onclick="filterStatus('fx', this)">
                <i class="fa-solid fa-arrow-right-arrow-left"></i> <?php echo get_phrase('Exchanges'); ?>
                <span class="count"><?php echo $stats['fx_payments']; ?></span>
            </span>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Results Bar -->
    <div class="ai-results-bar" id="resultsBar">
        <span id="resultsText"></span>
        <button onclick="resetFilters()"><?php echo get_phrase('clear'); ?></button>
    </div>
    
    <!-- Table -->
    <table class="ai-table" id="invoiceTable">
        <thead>
            <tr>
                <th>#</th>
                <th><?php echo get_phrase('student'); ?></th>
                <th><?php echo get_phrase('description'); ?></th>
                <th><?php echo get_phrase('amount'); ?></th>
                <th><?php echo get_phrase('paid'); ?></th>
                <th><?php echo get_phrase('method'); ?></th>
                <th><?php echo get_phrase('status'); ?></th>
                <th><?php echo get_phrase('actions'); ?></th>
        </tr>
    </thead>
    <tbody>
            <?php if (empty($invoices)): ?>
            <tr><td colspan="8">
                <div class="ai-empty">
                    <i class="fa-solid fa-file-invoice"></i>
                    <div class="ai-empty-title"><?php echo get_phrase('no_invoices_found'); ?></div>
                </div>
            </td></tr>
            <?php else: ?>
            <?php foreach ($invoices as $inv):
                            // 1. Essai prioritaire : table students (ID = Student ID)
                            $student_entry = $this->db->get_where('students', array('id' => $inv['student_id']))->row_array();
                            $student = [];
                            
                            if ($student_entry && isset($student_entry['user_id'])) {
                                $student = $this->db->get_where('users', array('id' => $student_entry['user_id']))->row_array();
                            }
                            // 2. Si échec, essai direct sur la table users (cas School Join / Adhésion communauté où ID = User ID)
                            if (empty($student)) {
                                $student = $this->db->get_where('users', array('id' => $inv['student_id']))->row_array();
                            }

                            $class = isset($inv['class_id']) ? $this->crud_model->get_class_details_by_id($inv['class_id'])->row_array() : null;
                $fx = isset($inv['conversion_applied']) && $inv['conversion_applied'] == 1;
                $fx_curr = isset($inv['payment_currency']) ? $inv['payment_currency'] : null;
                $fx_amt = isset($inv['payment_amount_converted']) ? (float)$inv['payment_amount_converted'] : null;
                $fx_rate = isset($inv['fx_rate']) ? (float)$inv['fx_rate'] : null;
                $is_paid = strtolower($inv['status']) == 'paid';
                $is_overdue = !$is_paid && (time() - $inv['created_at']) > (30 * 24 * 60 * 60);
                $initials = isset($student['name']) ? strtoupper(substr($student['name'], 0, 2)) : '??';
            ?>
            <tr data-status="<?php echo $inv['status']; ?>" data-fx="<?php echo $fx ? '1' : '0'; ?>">
                <td>
                    <span class="ai-inv-badge">#<?php echo sprintf('%06d', $inv['id']); ?></span>
                    <div class="ai-inv-date"><i class="fa-solid fa-calendar-days"></i> <?php echo date('d M Y', $inv['created_at']); ?></div>
                </td>
                <td>
                    <div class="ai-student">
                        <div class="ai-avatar"><?php echo $initials; ?></div>
                        <div>
                            <?php if (isset($student['name']) && !empty($student['name'])): ?>
                                <div class="ai-student-name"><?php echo htmlspecialchars($student['name']); ?></div>
                                <div class="ai-student-email"><?php echo htmlspecialchars($student['email']); ?></div>
                            <?php else: ?>
                                <div class="ai-student-name" style="color: var(--ai-gray);">
                                    <?php echo get_phrase('subscription'); ?>
                                </div>
                            <?php endif; ?>
                            <?php if ($class): ?>
                                <div class="ai-student-class"><?php echo $class['name']; ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </td>
                <td>
                    <strong>
                        <?php 
                        $type_label = $class ? get_phrase('class') : get_phrase('membership');
                        
                        if ((float)$inv['total_amount'] <= 0) {
                             echo get_phrase('free') . ' - ' . $type_label . ' - ' . htmlspecialchars($inv['title']);
                        } else {
                             echo $type_label . ' - ' . htmlspecialchars($inv['title']);
                        }
                        ?>
                    </strong>
                </td>
                <td>
                    <div class="ai-amount"><?php echo number_format($inv['total_amount'], 2); ?></div>
                    <div class="ai-currency"><?php echo $inv['currency']; ?></div>
                </td>
                <td>
                    <?php if ($is_paid): ?>
                        <?php if ($fx && $fx_curr && $fx_amt): ?>
                        <div class="ai-paid"><?php echo number_format($fx_amt, 2); ?> <?php echo $fx_curr; ?></div>
                        <div class="ai-fx-badge"><i class="fa-solid fa-arrow-right-arrow-left"></i> <?php echo number_format($inv['paid_amount'], 2); ?> <?php echo $inv['currency']; ?></div>
                        <?php if ($fx_rate): ?><div class="ai-fx-rate">1 <?php echo $inv['currency']; ?> = <?php echo number_format($fx_rate, 4); ?> <?php echo $fx_curr; ?></div><?php endif; ?>
                        <?php else: ?>
                        <div class="ai-paid"><?php echo number_format($inv['paid_amount'], 2); ?> <?php echo $inv['currency']; ?></div>
                        <?php endif; ?>
                    <?php else: ?>
                        <span class="ai-paid-zero">0.00 <?php echo $inv['currency']; ?></span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php 
                    // Déterminer la méthode de paiement
                    $method_display = '';
                    $method_class = '';
                    
                    if ((float)$inv['total_amount'] == 0) {
                        $method_display = 'Free_access';
                        $method_class = 'free';
                    } elseif (!empty($inv['payment_method'])) {
                        $m = strtolower($inv['payment_method']);
                        $method_display = ucfirst($inv['payment_method']);
                        $method_class = (strpos($m,'stripe')!==false)?'stripe':((strpos($m,'paypal')!==false)?'paypal':((strpos($m,'bank')!==false)?'bank':''));
                    } elseif ($is_paid && (float)$inv['paid_amount'] > 0) {
                        // Pour les factures payées sans méthode enregistrée, déduire la méthode
                        // Vérifier si c'est un paiement avec conversion FX (souvent Stripe)
                        if ($fx || !empty($inv['stripe_payment_intent_id'])) {
                            $method_display = 'Stripe';
                            $method_class = 'stripe';
                        } elseif (!empty($inv['paypal_payment_id'])) {
                            $method_display = 'PayPal';
                            $method_class = 'paypal';
                        } else {
                            // Défaut: Stripe si payé (méthode la plus courante)
                            $method_display = 'Stripe';
                            $method_class = 'stripe';
                        }
                    }
                    
                    if ($method_display): ?>
                    <span class="ai-method <?php echo $method_class; ?>">
                        <i class="<?php echo $method_class === 'stripe' ? 'fa-solid fa-credit-card' : ($method_class === 'paypal' ? 'fa-brands fa-paypal' : ($method_class === 'free' ? 'fa-solid fa-unlock' : 'fa-solid fa-building-columns')); ?>"></i> 
                        <?php echo $method_display; ?>
                    </span>
                    <?php else: ?>—<?php endif; ?>
                </td>
                <td>
                    <span class="ai-status <?php echo $is_paid ? 'paid' : ($is_overdue ? 'overdue' : 'unpaid'); ?>">
                        <span class="ai-status-dot"></span>
                        <?php echo $is_paid ? get_phrase('paid') : ($is_overdue ? get_phrase('overdue') : get_phrase('pending')); ?>
                    </span>
                    <?php if ($fx): ?><div class="ai-fx-tag"><i class="fa-solid fa-arrow-right-arrow-left"></i> <?php echo get_phrase('Exchanges'); ?></div><?php endif; ?>
                </td>
                <td>
                    <div class="ai-actions">
                        <a href="<?php echo route('invoice/invoice/'.$inv['id']); ?>" class="ai-action view" target="_blank" title="<?php echo get_phrase('view'); ?>"><i class="fa-solid fa-eye"></i></a>
                        <a href="<?php echo route('invoice_pdf/'.$inv['id']); ?>" class="ai-action pdf" target="_blank" title="PDF"><i class="fa-solid fa-file-pdf"></i></a>
                        <?php if (empty($inv['payment_type']) || $inv['payment_type'] != 'subscription_admin'): ?>
                        <a href="javascript:void(0);" class="ai-action edit" onclick="rightModal('<?php echo site_url('modal/popup/invoice/edit/'.$inv['id']); ?>', '<?php echo get_phrase('edit'); ?>')" title="<?php echo get_phrase('edit'); ?>"><i class="fa-solid fa-pencil"></i></a>
                        <?php endif; ?>
                        <a href="javascript:void(0);" class="ai-action delete" onclick="confirmModal('<?php echo route('invoice/delete/'.$inv['id']); ?>', showAllInvoices)" title="<?php echo get_phrase('delete'); ?>"><i class="fa-solid fa-trash"></i></a>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
            <?php endif; ?>
    </tbody>
</table>
    
    <!-- Footer -->
    <div class="ai-table-footer">
        <?php echo get_phrase('showing'); ?> <strong><?php echo count($invoices); ?></strong> <?php echo get_phrase('invoices'); ?>
    </div>
</div>

<script>
// Quick search
function quickSearch() {
    const q = document.getElementById('quickSearch').value.toLowerCase();
    const rows = document.querySelectorAll('#invoiceTable tbody tr[data-status]');
    let visible = 0;
    
    rows.forEach(row => {
        const match = row.textContent.toLowerCase().includes(q);
        row.style.display = match ? '' : 'none';
        if (match) visible++;
    });
    
    updateResults(visible, rows.length, q);
}

// Filter by status
function filterStatus(status, el) {
    document.querySelectorAll('.ai-chip').forEach(c => c.classList.remove('active'));
    el.classList.add('active');
    
    const rows = document.querySelectorAll('#invoiceTable tbody tr[data-status]');
    let visible = 0;
    
    rows.forEach(row => {
        let show = true;
        if (status === 'fx') show = row.dataset.fx === '1';
        else if (status !== 'all') show = row.dataset.status.toLowerCase() === status;
        row.style.display = show ? '' : 'none';
        if (show) visible++;
    });
    
    updateResults(visible, rows.length, status !== 'all' ? status : '');
}

// Update results bar
function updateResults(visible, total, filter) {
    const bar = document.getElementById('resultsBar');
    const text = document.getElementById('resultsText');
    
    if (filter) {
        bar.classList.add('active');
        bar.classList.toggle('no-results', visible === 0);
        text.innerHTML = visible === 0 
            ? '<i class="fa-solid fa-circle-exclamation"></i> <?php echo get_phrase('no_results'); ?>'
            : `<i class="fa-solid fa-circle-check"></i> <strong>${visible}</strong> <?php echo get_phrase('found'); ?>`;
    } else {
        bar.classList.remove('active');
    }
}

// Reset
function resetFilters() {
    document.getElementById('quickSearch').value = '';
    document.querySelectorAll('.ai-chip').forEach(c => c.classList.remove('active'));
    document.querySelector('.ai-chip[data-filter="all"]').classList.add('active');
    document.querySelectorAll('#invoiceTable tbody tr').forEach(r => r.style.display = '');
    document.getElementById('resultsBar').classList.remove('active');
}
</script>
