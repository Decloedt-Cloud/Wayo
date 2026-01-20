<?php 
$student_data = $this->user_model->get_logged_in_student_details();

// Pagination data
$pagination = isset($pagination) ? $pagination : [
    'current_page' => 1,
    'total_pages' => 1,
    'per_page' => 10,
    'total_items' => 0,
    'offset' => 0
];

$per_page = $pagination['per_page'];
$offset = $pagination['offset'];
$current_page = $pagination['current_page'];
$total_pages = $pagination['total_pages'];
$total_items = $pagination['total_items'];
$active_filter = isset($pagination['filter']) ? $pagination['filter'] : 'all';

// Get paginated invoices (filtered)
$invoices = $this->crud_model->get_invoice_by_student_id($student_data['code'], $per_page, $offset, $active_filter)->result_array();

// Debug removed - filter should work now

// Get all invoices for stats (without pagination) - keep global stats
$all_invoices = $this->crud_model->get_invoice_by_student_id($student_data['code'])->result_array();

// Calculate statistics from all invoices
$total_invoices = count($all_invoices);
$paid_invoices = 0;
$unpaid_invoices = 0;
$total_amount = 0;
$total_paid = 0;

foreach ($all_invoices as $inv) {
    $total_amount += (float)$inv['total_amount'];
    $total_paid += (float)$inv['paid_amount'];
    if (strtolower($inv['status']) == 'paid') {
        $paid_invoices++;
    } else {
        $unpaid_invoices++;
    }
}
$total_due = $total_amount - $total_paid;
$default_currency = !empty($all_invoices) ? $all_invoices[0]['currency'] : 'USD';
?>

<style>
/* Modern Invoice List Styles */
.invoice-stats-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.invoice-stat-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 16px;
    padding: 1.25rem;
    color: white;
    position: relative;
    overflow: hidden;
}

.invoice-stat-card.success {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
}

.invoice-stat-card.warning {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}

.invoice-stat-card.info {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
}

.invoice-stat-card::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 100%;
    height: 100%;
    background: rgba(255,255,255,0.1);
    border-radius: 50%;
}

.invoice-stat-card .stat-icon {
    font-size: 2.5rem;
    opacity: 0.3;
    position: absolute;
    right: 1rem;
    top: 50%;
    transform: translateY(-50%);
}

.invoice-stat-card .stat-value {
    font-size: 1.75rem;
    font-weight: 700;
    margin-bottom: 0.25rem;
}

.invoice-stat-card .stat-label {
    font-size: 0.85rem;
    opacity: 0.9;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Active filter card styles */
.invoice-stat-card.active {
    transform: scale(1.02);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    border: 2px solid rgba(255,255,255,0.3);
}

.invoice-stat-card.active::before {
    background: rgba(255,255,255,0.2);
}

.invoice-stat-card.active .stat-icon {
    opacity: 0.8;
}

.invoice-stat-card.active .stat-value {
    font-weight: 800;
}

/* Filter card hover effects */
.invoice-stat-card.filter-card {
    cursor: pointer;
    transition: all 0.3s ease;
}

.invoice-stat-card.filter-card:hover {
    transform: translateY(-3px) scale(1.01);
    box-shadow: 0 6px 20px rgba(0,0,0,0.1);
}

.invoice-stat-card.filter-card:active {
    transform: translateY(-1px) scale(0.99);
}

/* Clear filter button */
.clear-filter-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    background: #f1f5f9;
    color: #64748b;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    font-size: 0.85rem;
    font-weight: 500;
    text-decoration: none;
    cursor: pointer;
    transition: all 0.2s ease;
    margin-left: 1rem;
}

.clear-filter-btn:hover {
    background: #e2e8f0;
    color: #475569;
}

/* Invoice Cards */
.invoice-list-modern {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.invoice-card {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
    overflow: hidden;
    border: 1px solid #eef2f7;
}

.invoice-card:hover {
    box-shadow: 0 8px 25px rgba(0,0,0,0.12);
    transform: translateY(-2px);
}

.invoice-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 1.25rem;
    background: linear-gradient(135deg, #f8f9fc 0%, #ffffff 100%);
    border-bottom: 1px solid #eef2f7;
}

.invoice-number {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.invoice-number .number-badge {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 0.4rem 0.75rem;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.85rem;
    font-family: 'SF Mono', 'Monaco', 'Inconsolata', monospace;
}

.invoice-date {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    font-size: 0.85rem;
    color: #6c757d;
}

.invoice-date .date-value {
    font-weight: 600;
    color: #344054;
}

.invoice-card-body {
    padding: 1.25rem;
}

.invoice-info-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
    margin-bottom: 1rem;
}

@media (max-width: 768px) {
    .invoice-info-grid {
        grid-template-columns: 1fr;
    }
}

.invoice-info-item {
    display: flex;
    flex-direction: column;
}

.invoice-info-item .label {
    font-size: 0.75rem;
    color: #98a6ad;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 0.25rem;
}

.invoice-info-item .value {
    font-size: 1rem;
    font-weight: 600;
    color: #344054;
}

.invoice-info-item .value.title {
    font-size: 1.1rem;
}

/* Amount Section */
.invoice-amount-section {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem;
    background: #f8f9fc;
    border-radius: 12px;
    margin-top: 1rem;
}

.amount-block {
    text-align: center;
    flex: 1;
}

.amount-block .amount-label {
    font-size: 0.7rem;
    color: #98a6ad;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 0.25rem;
}

.amount-block .amount-value {
    font-size: 1.25rem;
    font-weight: 700;
    color: #344054;
}

.amount-block .amount-value.total {
    color: #667eea;
}

.amount-block .amount-value.paid {
    color: #10b981;
}

.amount-block .amount-value.due {
    color: #ef4444;
}

.amount-divider {
    width: 1px;
    height: 40px;
    background: #dee2e6;
    margin: 0 0.5rem;
}

/* FX Conversion Badge */
.fx-conversion-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%);
    padding: 0.5rem 0.75rem;
    border-radius: 8px;
    margin-top: 0.75rem;
    font-size: 0.8rem;
}

.fx-conversion-badge .fx-icon {
    color: #667eea;
    font-size: 1rem;
}

.fx-conversion-badge .fx-details {
    display: flex;
    flex-direction: column;
}

.fx-conversion-badge .fx-amount {
    font-weight: 600;
    color: #4338ca;
}

.fx-conversion-badge .fx-rate {
    font-size: 0.7rem;
    color: #6366f1;
}

/* Status Badge */
.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.35rem 0.75rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
}

.status-badge.paid {
    background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
    color: #059669;
}

.status-badge.unpaid {
    background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
    color: #dc2626;
}

.status-badge .status-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: currentColor;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

/* Invoice Card Footer */
.invoice-card-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 1.25rem;
    background: #fafbfc;
    border-top: 1px solid #eef2f7;
}

.invoice-class {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.85rem;
    color: #6c757d;
}

.invoice-class i {
    color: #667eea;
}

.invoice-actions {
    display: flex;
    gap: 0.5rem;
}

.btn-invoice-action {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.5rem 1rem;
    border-radius: 8px;
    font-size: 0.85rem;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.2s ease;
    border: none;
    cursor: pointer;
}

.btn-invoice-action.primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.btn-invoice-action.primary:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
}

.btn-invoice-action.secondary {
    background: #f1f5f9;
    color: #475569;
}

.btn-invoice-action.secondary:hover {
    background: #e2e8f0;
}

/* Empty State */
.invoice-empty-state {
    text-align: center;
    padding: 3rem 2rem;
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
}

.invoice-empty-state .empty-icon {
    font-size: 4rem;
    color: #cbd5e1;
    margin-bottom: 1rem;
}

.invoice-empty-state .empty-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: #344054;
    margin-bottom: 0.5rem;
}

.invoice-empty-state .empty-text {
    color: #6c757d;
}

/* ========== PAGINATION STYLES ========== */
.pagination-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 2rem;
    padding: 1rem;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
}

.pagination-info {
    font-size: 0.9rem;
    color: #64748b;
}

.pagination-info strong {
    color: #1e293b;
}

.pagination-controls {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.pagination-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 40px;
    height: 40px;
    padding: 0 0.75rem;
    border: none;
    border-radius: 10px;
    font-size: 0.9rem;
    font-weight: 500;
    text-decoration: none;
    cursor: pointer;
    transition: all 0.2s ease;
    background: #f1f5f9;
    color: #475569;
}

.pagination-btn:hover:not(.disabled):not(.active) {
    background: #e2e8f0;
    color: #1e293b;
}

.pagination-btn.active {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

.pagination-btn.disabled {
    opacity: 0.5;
    cursor: not-allowed;
    pointer-events: none;
}

.pagination-btn.nav-btn {
    background: #fff;
    border: 1px solid #e2e8f0;
}

.pagination-btn.nav-btn:hover:not(.disabled) {
    background: #667eea;
    color: white;
    border-color: #667eea;
}

.pagination-ellipsis {
    color: #94a3b8;
    padding: 0 0.5rem;
}

/* Per page selector */
.per-page-selector {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.85rem;
    color: #64748b;
}

.per-page-selector select {
    padding: 0.5rem 0.75rem;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    background: #fff;
    font-size: 0.85rem;
    cursor: pointer;
}

@media (max-width: 768px) {
    .pagination-container {
        flex-direction: column;
        gap: 1rem;
    }
    
    .pagination-controls {
        flex-wrap: wrap;
        justify-content: center;
    }
}
</style>

<!-- Statistics Cards -->
<div class="invoice-stats-container">
    <div class="invoice-stat-card filter-card <?php echo $active_filter == 'all' ? 'active' : ''; ?>" data-filter="all">
        <i class="mdi mdi-file-document-multiple stat-icon"></i>
        <div class="stat-value"><?php echo $total_invoices; ?></div>
        <div class="stat-label"><?php echo get_phrase('total_invoices'); ?></div>
    </div>
    <div class="invoice-stat-card success filter-card <?php echo $active_filter == 'paid' ? 'active' : ''; ?>" data-filter="paid">
        <i class="mdi mdi-check-circle stat-icon"></i>
        <div class="stat-value"><?php echo $paid_invoices; ?></div>
        <div class="stat-label"><?php echo get_phrase('paid'); ?></div>
    </div>
    <div class="invoice-stat-card warning filter-card <?php echo $active_filter == 'pending' ? 'active' : ''; ?>" data-filter="pending">
        <i class="mdi mdi-clock-alert stat-icon"></i>
        <div class="stat-value"><?php echo $unpaid_invoices; ?></div>
        <div class="stat-label"><?php echo get_phrase('pending'); ?></div>
    </div>
    <div class="invoice-stat-card info filter-card <?php echo $active_filter == 'due' ? 'active' : ''; ?>" data-filter="due">
        <i class="mdi mdi-currency-usd stat-icon"></i>
        <div class="stat-value"><?php echo number_format($total_due, 0); ?></div>
        <div class="stat-label"><?php echo get_phrase('total_due'); ?> (<?php echo $default_currency; ?>)</div>
    </div>

    <?php if ($active_filter !== 'all'): ?>
        <button class="clear-filter-btn" onclick="clearInvoiceFilter()">
            <i class="mdi mdi-close"></i>
            <?php echo get_phrase('clear_filter'); ?>
        </button>
    <?php endif; ?>
</div>

<!-- Invoice List -->
<?php if (empty($invoices)): ?>
    <div class="invoice-empty-state">
        <i class="mdi mdi-file-document-outline empty-icon"></i>
        <div class="empty-title"><?php echo get_phrase('no_invoices_found'); ?></div>
        <div class="empty-text"><?php echo get_phrase('you_dont_have_any_invoices_yet'); ?></div>
    </div>
<?php else: ?>
    <div class="invoice-list-modern">
        <?php foreach ($invoices as $invoice):
      $class_details = $this->crud_model->get_class_details_by_id($invoice['class_id'])->row_array(); 
      
            // FX conversion info
            $conversion_applied = isset($invoice['conversion_applied']) && $invoice['conversion_applied'] == 1;
            $payment_currency = isset($invoice['payment_currency']) ? $invoice['payment_currency'] : null;
            $payment_amount_converted = isset($invoice['payment_amount_converted']) ? $invoice['payment_amount_converted'] : null;
            $fx_rate = isset($invoice['fx_rate']) ? $invoice['fx_rate'] : null;
            
            $is_paid = strtolower($invoice['status']) == 'paid';
            $due_amount = (float)$invoice['total_amount'] - (float)$invoice['paid_amount'];
        ?>
            <div class="invoice-card">
                <!-- Header -->
                <div class="invoice-card-header">
                    <div class="invoice-number">
                        <span class="number-badge">#<?php echo sprintf('%06d', $invoice['id']); ?></span>
                        <span class="status-badge <?php echo $is_paid ? 'paid' : 'unpaid'; ?>">
                            <span class="status-dot"></span>
                            <?php echo $is_paid ? get_phrase('paid') : get_phrase('unpaid'); ?>
                        </span>
                    </div>
                    <div class="invoice-date">
                        <span class="date-label"><?php echo get_phrase('created'); ?></span>
                        <span class="date-value"><?php echo date('d M Y', $invoice['created_at']); ?></span>
                    </div>
                </div>
                
                <!-- Body -->
                <div class="invoice-card-body">
                    <div class="invoice-info-grid">
                        <div class="invoice-info-item">
                            <span class="label"><?php echo get_phrase('invoice_title'); ?></span>
                            <span class="value title"><?php echo $invoice['title']; ?></span>
                        </div>
                        <div class="invoice-info-item">
                            <span class="label"><?php echo get_phrase('payment_method'); ?></span>
                            <span class="value">
                                <?php if (!empty($invoice['payment_method'])): ?>
                                    <i class="mdi mdi-<?php echo $invoice['payment_method'] == 'stripe' ? 'credit-card' : ($invoice['payment_method'] == 'paypal' ? 'paypal' : 'cash'); ?>"></i>
                                    <?php echo ucfirst($invoice['payment_method']); ?>
          <?php else: ?>
                                    <span class="text-muted">—</span>
          <?php endif; ?>
                            </span>
                        </div>
                    </div>
                    
                    <!-- Amount Section -->
                    <div class="invoice-amount-section">
                        <div class="amount-block">
                            <div class="amount-label"><?php echo get_phrase('total_amount'); ?></div>
                            <div class="amount-value total"><?php echo number_format($invoice['total_amount'], 2); ?> <?php echo $invoice['currency']; ?></div>
                        </div>
                        <div class="amount-divider"></div>
                        <div class="amount-block">
                            <div class="amount-label"><?php echo get_phrase('paid'); ?></div>
                            <div class="amount-value paid"><?php echo number_format($invoice['paid_amount'], 2); ?> <?php echo $invoice['currency']; ?></div>
                        </div>
                        <div class="amount-divider"></div>
                        <div class="amount-block">
                            <div class="amount-label"><?php echo get_phrase('due'); ?></div>
                            <div class="amount-value due"><?php echo number_format($due_amount, 2); ?> <?php echo $invoice['currency']; ?></div>
              </div>
            </div>
                    
                    <!-- FX Conversion Info -->
                    <?php if ($is_paid && $conversion_applied && $payment_currency && $payment_amount_converted): ?>
                        <div class="fx-conversion-badge">
                            <i class="mdi mdi-swap-horizontal fx-icon"></i>
                            <div class="fx-details">
                                <span class="fx-amount">
                                    <?php echo get_phrase('paid'); ?>: <?php echo number_format($payment_amount_converted, 2); ?> <?php echo $payment_currency; ?>
                                </span>
                                <?php if ($fx_rate): ?>
                                    <span class="fx-rate">
                                        1 <?php echo $invoice['currency']; ?> = <?php echo number_format($fx_rate, 4); ?> <?php echo $payment_currency; ?>
                                        <?php if (isset($invoice['fx_rate_date'])): ?>
                                            (<?php echo $invoice['fx_rate_date']; ?>)
                                        <?php endif; ?>
                                    </span>
                                <?php endif; ?>
              </div>
            </div>
          <?php endif; ?>
                </div>
                
                <!-- Footer -->
                <div class="invoice-card-footer">
                    <div class="invoice-class">
                        <i class="mdi mdi-school"></i>
                        <?php echo !empty($class_details) ? $class_details['name'] : get_phrase('subscription'); ?>
                    </div>
                    <div class="invoice-actions">
                        <?php if (!$is_paid): ?>
                            <a href="<?php echo route('payment/' . $invoice['id']); ?>" class="btn-invoice-action primary">
                                <i class="mdi mdi-credit-card-outline"></i>
                                <?php echo get_phrase('pay_now'); ?>
                            </a>
                        <?php else: ?>
                            <a href="<?php echo route('invoice/invoice/' . $invoice['id']); ?>" class="btn-invoice-action secondary" target="_blank">
                                <i class="mdi mdi-eye-outline"></i>
                                <?php echo get_phrase('view'); ?>
                            </a>
                            <a href="<?php echo route('invoice_pdf/' . $invoice['id']); ?>" class="btn-invoice-action secondary" target="_blank">
                                <i class="mdi mdi-download"></i>
                                PDF
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
    <?php endforeach; ?>
    </div>
    
    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
        <div class="pagination-container">
            <div class="pagination-info">
                <?php 
                $start_item = $offset + 1;
                $end_item = min($offset + $per_page, $total_items);
                ?>
                <?php echo get_phrase('showing'); ?> <strong><?php echo $start_item; ?></strong> - <strong><?php echo $end_item; ?></strong> 
                <?php echo get_phrase('of'); ?> <strong><?php echo $total_items; ?></strong> <?php echo get_phrase('invoices'); ?>
            </div>
            
            <div class="pagination-controls">
                <!-- Previous Button -->
                <a href="<?php echo route('invoice/page/' . max(1, $current_page - 1)); ?>" 
                   class="pagination-btn nav-btn <?php echo $current_page <= 1 ? 'disabled' : ''; ?>">
                    <i class="mdi mdi-chevron-left"></i>
                </a>
                
                <?php
                // Calculate which page numbers to show
                $show_pages = [];
                $show_pages[] = 1; // Always show first page
                
                // Pages around current
                for ($i = max(2, $current_page - 1); $i <= min($total_pages - 1, $current_page + 1); $i++) {
                    $show_pages[] = $i;
                }
                
                if ($total_pages > 1) {
                    $show_pages[] = $total_pages; // Always show last page
                }
                
                $show_pages = array_unique($show_pages);
                sort($show_pages);
                
                $prev_page = 0;
                foreach ($show_pages as $page_num):
                    // Show ellipsis if there's a gap
                    if ($prev_page && $page_num - $prev_page > 1): ?>
                        <span class="pagination-ellipsis">...</span>
                    <?php endif; ?>
                    
                    <a href="<?php echo route('invoice/page/' . $page_num); ?>" 
                       class="pagination-btn <?php echo $page_num == $current_page ? 'active' : ''; ?>">
                        <?php echo $page_num; ?>
                    </a>
                    
                    <?php $prev_page = $page_num;
                endforeach; ?>
                
                <!-- Next Button -->
                <a href="<?php echo route('invoice/page/' . min($total_pages, $current_page + 1)); ?>" 
                   class="pagination-btn nav-btn <?php echo $current_page >= $total_pages ? 'disabled' : ''; ?>">
                    <i class="mdi mdi-chevron-right"></i>
                </a>
            </div>
        </div>
    <?php endif; ?>
<?php endif; ?>

<script>
// Invoice filter functionality
document.addEventListener('DOMContentLoaded', function() {
    // Get current URL and filter state
    const urlParams = new URLSearchParams(window.location.search);
    let currentFilter = urlParams.get('filter') || 'all';

    // Add click handlers to filter cards
    document.querySelectorAll('.filter-card').forEach(card => {
        card.addEventListener('click', function() {
            const filter = this.getAttribute('data-filter');

            // If clicking the same active filter, reset to 'all'
            if (filter === currentFilter) {
                applyFilter('all');
            } else {
                applyFilter(filter);
            }
        });
    });
});

function applyFilter(filter) {
    // Update URL with filter parameter
    const url = new URL(window.location);
    if (filter === 'all') {
        url.searchParams.delete('filter');
    } else {
        url.searchParams.set('filter', filter);
    }

    // Navigate to the filtered URL (this will reload the page with filtered data)
    window.location.href = url.toString();
}

function clearInvoiceFilter() {
    applyFilter('all');
}

// Optional: Add visual feedback on filter change
function updateActiveFilterUI(activeFilter) {
    // Remove active class from all cards
    document.querySelectorAll('.filter-card').forEach(card => {
        card.classList.remove('active');
    });

    // Add active class to the selected filter card
    const activeCard = document.querySelector(`[data-filter="${activeFilter}"]`);
    if (activeCard) {
        activeCard.classList.add('active');
    }
}

// Initialize active state on page load
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const currentFilter = urlParams.get('filter') || 'all';
    updateActiveFilterUI(currentFilter);
});
</script>
