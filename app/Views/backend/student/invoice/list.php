<?php 
$student_data = $this->user_model->get_logged_in_student_details();
$student_code = isset($student_data['user_id']) ? $student_data['user_id'] : null;
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
$search_query = isset($pagination['search']) ? $pagination['search'] : '';

$to_rows = static function ($result): array {
    if (is_array($result)) {
        return array_map(static function ($row) {
            return is_array($row) ? $row : (array) $row;
        }, $result);
    }
    if (is_object($result)) {
        if (method_exists($result, 'getResultArray')) {
            return $result->getResultArray();
        }
        if (method_exists($result, 'result_array')) {
            return $result;
        }
    }
    return [];
};

// Get paginated invoices (filtered)
$invoices = $student_code
    ? $to_rows($this->crud_model->get_invoice_by_student_id($student_code, $per_page, $offset, $active_filter, $search_query))
    : [];

// Get all invoices for stats (without pagination) - keep global stats
$all_invoices = $student_code
    ? $to_rows($this->crud_model->get_invoice_by_student_id($student_code))
    : [];

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
    /* Scoped styles for list specific elements matching Modern Design */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        box-shadow: var(--shadow-sm);
        border: 1px solid transparent;
        transition: all 0.2s;
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }

    .stat-card:hover, .stat-card.active {
        border-color: var(--primary-color);
        box-shadow: var(--shadow-md);
        transform: translateY(-2px);
    }

    .stat-card.active {
        background: #F4F7FE;
    }

    .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        flex-shrink: 0;
    }

    .stat-icon.primary { background: rgba(67, 24, 255, 0.1); color: var(--primary-color); }
    .stat-icon.success { background: rgba(5, 205, 153, 0.1); color: #05CD99; }
    .stat-icon.warning { background: rgba(255, 181, 71, 0.1); color: #FFB547; }
    .stat-icon.info { background: rgba(51, 153, 255, 0.1); color: #3399FF; }

    .stat-info {
        flex-grow: 1;
    }

    .stat-info h4 { 
        margin: 0 0 0.25rem 0; 
        font-size: 1.5rem; 
        font-weight: 700; 
        color: var(--text-color); 
        font-family: var(--font-secondary);
    }

    .stat-info p { 
        margin: 0; 
        color: var(--text-secondary); 
        font-size: 0.85rem; 
        font-weight: 500;
    }

    /* Status Badge */
    .status-badge {
        padding: 0.35rem 0.85rem;
        border-radius: 50px;
        font-size: 0.8rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
    }
    .status-badge.paid { background: rgba(5, 205, 153, 0.1); color: #05CD99; }
    .status-badge.unpaid { background: rgba(238, 93, 80, 0.1); color: #EE5D50; }

    .status-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background-color: currentColor;
    }

    /* Pagination */
    .modern-pagination {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 0.5rem;
        margin-top: 2rem;
        padding-top: 1rem;
        border-top: 1px solid #F4F7FE;
    }

    .page-btn {
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        border: 1px solid transparent;
        background: transparent;
        color: var(--text-secondary);
        font-weight: 600;
        font-size: 0.9rem;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
    }

    .page-btn:hover:not(.disabled) {
        background: #F4F7FE;
        color: var(--primary-color);
    }

    .page-btn.active {
        background: var(--primary-color);
        color: white;
        box-shadow: 0 4px 10px rgba(67, 24, 255, 0.3);
    }

    .page-btn.disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .page-ellipsis {
        color: var(--text-secondary);
        padding: 0 0.25rem;
    }

    /* Clear Filter Button */
    .clear-filter-wrapper {
        text-align: right;
        margin-bottom: 1rem;
    }
    .clear-filter-btn {
        background: none;
        border: none;
        color: var(--text-secondary);
        font-size: 0.85rem;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    .clear-filter-btn:hover {
        color: var(--primary-color);
    }
</style>

<div class="invoice-toolbar" style="margin-bottom: 1.5rem; display: flex; justify-content: flex-end; align-items: center;">
    <div class="search-box" style="position: relative; max-width: 300px; width: 100%;">
        <input type="text" id="invoiceSearch" 
               class="form-control" 
               placeholder="<?php echo get_phrase('search_invoice_title_or_id'); ?>..." 
               value="<?php echo isset($pagination['search']) ? $pagination['search'] : ''; ?>"
               style="padding-left: 2.5rem; border-radius: 10px; height: 45px; border: 1px solid #E0E0E0;">
        <i class="mdi mdi-magnify" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: #A3AED0; font-size: 1.2rem;"></i>
    </div>
</div>

<div id="invoice_results">
<!-- Statistics Cards -->
<div class="stats-grid">
    <div class="stat-card filter-card <?php echo $active_filter == 'all' ? 'active' : ''; ?>" data-filter="all">
        <div class="stat-icon primary">
            <i class="mdi mdi-file-document-multiple"></i>
        </div>
        <div class="stat-info">
            <h4><?php echo $total_invoices; ?></h4>
            <p><?php echo get_phrase('total_invoices'); ?></p>
        </div>
    </div>
    <div class="stat-card filter-card <?php echo $active_filter == 'paid' ? 'active' : ''; ?>" data-filter="paid">
        <div class="stat-icon success">
            <i class="mdi mdi-check-circle"></i>
        </div>
        <div class="stat-info">
            <h4><?php echo $paid_invoices; ?></h4>
            <p><?php echo get_phrase('paid'); ?></p>
        </div>
    </div>
    <div class="stat-card filter-card <?php echo $active_filter == 'pending' ? 'active' : ''; ?>" data-filter="pending">
        <div class="stat-icon warning">
            <i class="mdi mdi-clock-alert"></i>
        </div>
        <div class="stat-info">
            <h4><?php echo $unpaid_invoices; ?></h4>
            <p><?php echo get_phrase('pending'); ?></p>
        </div>
    </div>
    <div class="stat-card filter-card <?php echo $active_filter == 'due' ? 'active' : ''; ?>" data-filter="due">
        <div class="stat-icon info">
            <i class="mdi mdi-currency-usd"></i>
        </div>
        <div class="stat-info">
            <h4><?php echo number_format($total_due, 0); ?></h4>
            <p><?php echo get_phrase('total_due'); ?> (<?php echo $default_currency; ?>)</p>
        </div>
    </div>
</div>

<?php if ($active_filter !== 'all'): ?>
    <div class="clear-filter-wrapper">
        <button class="clear-filter-btn" onclick="clearInvoiceFilter()">
            <i class="mdi mdi-close"></i>
            <?php echo get_phrase('clear_filter'); ?>
        </button>
    </div>
<?php endif; ?>

<!-- Invoice List -->
<div class="modern-table-wrapper">
    <?php if (empty($invoices)): ?>
        <div class="empty-state">
            <img src="<?php echo base_url('assets/backend/images/empty_box.png'); ?>" alt="No Invoices">
            <h3><?php echo get_phrase('no_invoices_found'); ?></h3>
            <p><?php echo get_phrase('you_dont_have_any_invoices_yet'); ?></p>
        </div>
    <?php else: ?>
        <table class="modern-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th><?php echo get_phrase('title'); ?></th>
                    <th><?php echo get_phrase('amount'); ?></th>
                    <th><?php echo get_phrase('status'); ?></th>
                    <th><?php echo get_phrase('date'); ?></th>
                    <th><?php echo get_phrase('actions'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($invoices as $invoice):
                    $class_details = $this->crud_model->get_class_details_by_id($invoice['class_id']); 
                    $is_paid = strtolower($invoice['status']) == 'paid';
                    $due_amount = (float)$invoice['total_amount'] - (float)$invoice['paid_amount'];
                ?>
                    <tr>
                        <td>
                            <span style="font-family: monospace; font-weight: 600; color: var(--primary-color);">
                                #<?php echo sprintf('%06d', $invoice['id']); ?>
                            </span>
                        </td>
                        <td>
                            <div class="d-flex flex-column">
                                <span class="fw-bold">
                                    <?php 
                                    $type_label = !empty($class_details) ? get_phrase('class') : get_phrase('membership');

                                    if ((float)$invoice['total_amount'] <= 0) {
                                        echo get_phrase('free') . ' - ' . $type_label . ' - ' . $invoice['title'];
                                    } else {
                                        echo $type_label . ' - ' . $invoice['title'];
                                    }
                                    ?>
                                </span>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex flex-column">
                                <span class="fw-bold" style="color: var(--text-color);">
                                    <?php echo number_format($invoice['total_amount'], 2); ?> <?php echo $invoice['currency']; ?>
                                </span>
                                <?php if($due_amount > 0): ?>
                                    <small style="color: #EE5D50;">
                                        <?php echo get_phrase('due'); ?>: <?php echo number_format($due_amount, 2); ?>
                                    </small>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td>
                            <span class="status-badge <?php echo $is_paid ? 'paid' : 'unpaid'; ?>">
                                <span class="status-dot"></span>
                                <?php echo $is_paid ? get_phrase('paid') : get_phrase('unpaid'); ?>
                            </span>
                        </td>
                        <td>
                            <span style="color: var(--text-secondary);">
                                <?php echo date('d M, Y', $invoice['created_at']); ?>
                            </span>
                        </td>
                        <td>
                            <div class="d-flex gap-2">
                                <?php if (!$is_paid): ?>
                                    <?php
                                         // Déterminer l'URL de paiement en fonction du type de facture (Classe ou Communauté)
                                         $payment_url = site_url('payment/community/' . $invoice['id']);
                                         if (!empty($invoice['class_id']) && $invoice['class_id'] > 0) {
                                             $payment_url = site_url('app/payment/' . $invoice['id']);
                                         }
                                     ?>
                                    <a href="<?php echo $payment_url; ?>" class="modern-btn primary" style="padding: 0.5rem 1rem; font-size: 0.85rem;">
                                        <i class="mdi mdi-credit-card-outline"></i> <?php echo get_phrase('pay'); ?>
                                    </a>
                                <?php else: ?>
                                    <a href="<?php echo route('invoice/invoice/' . $invoice['id']); ?>" class="modern-btn" style="background: #F4F7FE; color: var(--text-color); padding: 0.5rem 1rem; font-size: 0.85rem;" target="_blank">
                                        <i class="mdi mdi-eye-outline"></i>
                                    </a>
                                    <a href="<?php echo route('invoice_pdf/' . $invoice['id']); ?>" class="modern-btn" style="background: #F4F7FE; color: var(--text-color); padding: 0.5rem 1rem; font-size: 0.85rem;" target="_blank" rel="noopener" download>
                                        <i class="mdi mdi-download"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
            <div class="modern-pagination">
                <!-- Previous Button -->
                <a href="<?php echo route('invoice/page/' . max(1, $current_page - 1)); ?>" 
                   class="page-btn <?php echo $current_page <= 1 ? 'disabled' : ''; ?>"
                   data-page="<?php echo max(1, $current_page - 1); ?>">
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
                        <span class="page-ellipsis">...</span>
                    <?php endif; ?>
                    
                    <a href="<?php echo route('invoice/page/' . $page_num); ?>" 
                       class="page-btn <?php echo $page_num == $current_page ? 'active' : ''; ?>"
                       data-page="<?php echo $page_num; ?>">
                        <?php echo $page_num; ?>
                    </a>
                    
                    <?php $prev_page = $page_num;
                endforeach; ?>
                
                <!-- Next Button -->
                <a href="<?php echo route('invoice/page/' . min($total_pages, $current_page + 1)); ?>" 
                   class="page-btn <?php echo $current_page >= $total_pages ? 'disabled' : ''; ?>"
                   data-page="<?php echo min($total_pages, $current_page + 1); ?>">
                    <i class="mdi mdi-chevron-right"></i>
                </a>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>
</div>

<script>
// Invoice filter functionality
(function() {
    // Make these functions global so they can be reused after AJAX load
    window.loadInvoices = function(page, filter, search) {
        const container = document.getElementById('invoice_results');
        if (container) container.style.opacity = '0.5';

        const url = '<?php echo site_url('student/filter_invoice'); ?>' + 
                    '?page=' + page + 
                    '&filter=' + filter + 
                    '&search=' + encodeURIComponent(search);

        fetch(url)
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newContent = doc.getElementById('invoice_results').innerHTML;
                
                if (container) {
                    container.innerHTML = newContent;
                    container.style.opacity = '1';
                }
                
                // Re-bind events
                initInvoiceFilters();
                
                // Update URL
                const newUrl = new URL(window.location);
                let path = newUrl.pathname;
                
                // Handle page number in path
                if (path.includes('/page/')) {
                    path = path.replace(/\/page\/\d+/, '/page/' + page);
                } else {
                    if (!path.endsWith('/')) path += '/';
                    if (!path.includes('/invoice/')) path += 'invoice/'; // Ensure correct structure if root
                    path += 'page/' + page;
                }
                newUrl.pathname = path;

                if (filter !== 'all') newUrl.searchParams.set('filter', filter);
                else newUrl.searchParams.delete('filter');
                
                if (search) newUrl.searchParams.set('search', search);
                else newUrl.searchParams.delete('search');
                
                window.history.pushState({}, '', newUrl);
            })
            .catch(err => {
                console.error('Error fetching invoices:', err);
                if (container) container.style.opacity = '1';
            });
    };

    window.applyFilter = function(filter) {
        const searchInput = document.getElementById('invoiceSearch');
        const currentSearch = searchInput ? searchInput.value : '';
        loadInvoices(1, filter, currentSearch);
    };

    window.applySearch = function(query) {
        const activeCard = document.querySelector('.filter-card.active');
        const currentFilter = activeCard ? activeCard.getAttribute('data-filter') : 'all';
        loadInvoices(1, currentFilter, query);
    };
    
    window.clearInvoiceFilter = function() {
        applyFilter('all');
    };

    window.initInvoiceFilters = function() {
        // Add click handlers to filter cards
        const filterCards = document.querySelectorAll('.filter-card');
        filterCards.forEach(card => {
            card.onclick = function() {
                const filter = this.getAttribute('data-filter');
                const activeCard = document.querySelector('.filter-card.active');
                const currentFilter = activeCard ? activeCard.getAttribute('data-filter') : 'all';
                
                if (filter === currentFilter) {
                    applyFilter('all');
                } else {
                    applyFilter(filter);
                }
            };
        });

        // Bind Pagination
        const pageBtns = document.querySelectorAll('.page-btn');
        pageBtns.forEach(btn => {
            btn.onclick = function(e) {
                e.preventDefault();
                if (this.classList.contains('disabled') || this.classList.contains('active')) return;
                
                const page = this.getAttribute('data-page');
                const searchInput = document.getElementById('invoiceSearch');
                const currentSearch = searchInput ? searchInput.value : '';
                const activeCard = document.querySelector('.filter-card.active');
                const currentFilter = activeCard ? activeCard.getAttribute('data-filter') : 'all';
                
                loadInvoices(page, currentFilter, currentSearch);
            };
        });
        
        // Tooltips (re-init if needed, using Bootstrap/jQuery if available)
        if (typeof $ !== 'undefined' && $.fn.tooltip) {
            $('[data-bs-toggle="tooltip"]').tooltip();
        }
    };

    // Initial Bind
    initInvoiceFilters();

    // Search functionality (bind only once)
    const searchInput = document.getElementById('invoiceSearch');
    if (searchInput && !searchInput.dataset.bound) {
        let debounceTimer;
        searchInput.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                applySearch(this.value);
            }, 500);
        });
        searchInput.dataset.bound = true; // Mark as bound to avoid duplicates if re-run
    }
})();
</script>