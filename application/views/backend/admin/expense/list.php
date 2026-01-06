<?php
/**
 * Expense List View
 *
 * Modern design matching expense_category style with advanced filtering,
 * sorting, and pagination features.
 */

$expenses = array();
if (isset($expense_category_id) && $expense_category_id > 0) {
    if ($expense_category_id != 'all') {
        $expenses = $this->crud_model->get_expense($date_from, $date_to, $expense_category_id)->result_array();
    } else {
        $expenses = $this->crud_model->get_expense($date_from, $date_to)->result_array();
    }
} else {
    $expenses = $this->crud_model->get_expense($date_from, $date_to)->result_array();
}

$total_expenses = count($expenses);
$total_amount = array_sum(array_column($expenses, 'amount'));
?>

<?php if ($total_expenses > 0): ?>

<!-- Premium Stats Dashboard -->
<div class="exp-dashboard">
    <div class="exp-stats-grid">
        <div class="exp-stat-card exp-stat-total">
            <div class="exp-stat-glow"></div>
            <div class="exp-stat-icon-wrap">
                <i class="mdi mdi-receipt"></i>
                <div class="exp-stat-pulse"></div>
            </div>
            <div class="exp-stat-data">
                <span class="exp-stat-value" data-count="<?php echo $total_expenses; ?>">0</span>
                <span class="exp-stat-title"><?php echo get_phrase('total_expenses'); ?></span>
            </div>
            <div class="exp-stat-trend">
                <i class="mdi mdi-trending-up"></i>
                <span>100%</span>
            </div>
        </div>

        <div class="exp-stat-card exp-stat-amount">
            <div class="exp-stat-glow"></div>
            <div class="exp-stat-icon-wrap">
                <i class="mdi mdi-cash-multiple"></i>
                <div class="exp-stat-pulse"></div>
            </div>
            <div class="exp-stat-data">
                <span class="exp-stat-value-amount"><?php echo currency($total_amount); ?></span>
                <span class="exp-stat-title"><?php echo get_phrase('total_amount'); ?></span>
            </div>
            <div class="exp-stat-trend warning">
                <i class="mdi mdi-chart-line"></i>
                <span><?php echo get_phrase('period'); ?></span>
            </div>
        </div>
    </div>
</div>

<!-- Premium Search & Filter Bar -->
<div class="exp-toolbar">
    <div class="exp-toolbar-left">
        <div class="exp-search-box">
            <div class="exp-search-icon-wrapper">
                <i class="mdi mdi-magnify"></i>
            </div>
            <input type="text" id="exp-search" class="exp-search-field" placeholder="<?php echo get_phrase('search_expenses'); ?>..." autocomplete="off">
            <button type="button" class="exp-search-clear-btn" id="exp-clear-search">
                <i class="mdi mdi-close-circle"></i>
            </button>
            <div class="exp-search-shortcut">
                <kbd>⌘</kbd><kbd>K</kbd>
            </div>
        </div>
    </div>
    
    <div class="exp-toolbar-right">
        <div class="exp-view-options">
            <div class="exp-per-page-select">
                <label><?php echo get_phrase('show'); ?></label>
                <select id="exp-per-page">
                    <option value="5">5</option>
                    <option value="10" selected>10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>
            </div>
        </div>
    </div>
</div>

<!-- Premium List Container -->
<div class="exp-list-container">
    <!-- List Header -->
    <div class="exp-list-header">
        <div class="exp-col-date exp-sortable-col" data-sort="date">
            <span><?php echo get_phrase('date'); ?></span>
            <div class="exp-sort-icon">
                <i class="mdi mdi-unfold-more-horizontal"></i>
            </div>
        </div>
        <div class="exp-col-amount exp-sortable-col" data-sort="amount">
            <span><?php echo get_phrase('amount'); ?></span>
            <div class="exp-sort-icon">
                <i class="mdi mdi-unfold-more-horizontal"></i>
            </div>
        </div>
        <div class="exp-col-category exp-sortable-col" data-sort="category">
            <span><?php echo get_phrase('category'); ?></span>
            <div class="exp-sort-icon">
                <i class="mdi mdi-unfold-more-horizontal"></i>
            </div>
        </div>
        <div class="exp-col-actions">
            <span><?php echo get_phrase('actions'); ?></span>
        </div>
    </div>
    
    <!-- List Body -->
    <div class="exp-list-body" id="exp-list-body">
        <?php foreach ($expenses as $index => $expense): 
            $expense_category_details = $this->db->get_where('expense_categories', array('id' => $expense['expense_category_id']))->row_array();
            $category_name = isset($expense_category_details['name']) ? $expense_category_details['name'] : get_phrase('unknown');
        ?>
        <div class="exp-list-item" 
             data-id="<?php echo $expense['id']; ?>"
             data-date="<?php echo $expense['date']; ?>"
             data-amount="<?php echo $expense['amount']; ?>"
             data-category="<?php echo strtolower($category_name); ?>"
             style="--delay: <?php echo $index * 0.03; ?>s">
            
            <!-- Date Column -->
            <div class="exp-col-date">
                <div class="exp-date-display">
                    <div class="exp-date-icon">
                        <i class="mdi mdi-calendar-month"></i>
                    </div>
                    <div class="exp-date-text">
                        <span class="exp-date-main"><?php echo date('d M Y', $expense['date']); ?></span>
                        <span class="exp-date-day"><?php echo date('l', $expense['date']); ?></span>
                    </div>
                </div>
            </div>
            
            <!-- Amount Column -->
            <div class="exp-col-amount">
                <div class="exp-amount-display">
                  <?php $school_currency = $this->db->get_where('settings_school', array('school_id' => school_id()))->row('system_currency') ?? 'EUR'; ?>
                    <span class="exp-amount-value"><?php echo $expense['amount'] . ' ' . $school_currency; ?></span>
                </div>
            </div>
            
            <!-- Category Column -->
            <div class="exp-col-category">
                <div class="exp-item-avatar" style="--hue: <?php echo (ord($category_name[0]) * 15) % 360; ?>">
                    <span><?php echo strtoupper(substr($category_name, 0, 2)); ?></span>
                </div>
                <div class="exp-item-info">
                    <h4 class="exp-item-name"><?php echo $category_name; ?></h4>
                    <span class="exp-item-id">
                        <i class="mdi mdi-identifier"></i>
                        EXP-<?php echo str_pad($expense['id'], 4, '0', STR_PAD_LEFT); ?>
                    </span>
                </div>
            </div>
            
            <!-- Actions Column -->
            <div class="exp-col-actions">
                <div class="exp-action-buttons">
                    <button type="button" class="exp-action-btn exp-btn-edit" 
                            title="<?php echo get_phrase('edit'); ?>"
                            onclick="rightModal('<?php echo site_url('modal/popup/expense/edit/'.$expense['id'])?>', '<?php echo get_phrase('update_expense'); ?>')">
                        <i class="mdi mdi-pencil-outline"></i>
                    </button>
                    <button type="button" class="exp-action-btn exp-btn-delete" 
                            title="<?php echo get_phrase('delete'); ?>"
                            onclick="confirmModal('<?php echo route('expense/delete/'.$expense['id']); ?>', showAllExpenses)">
                        <i class="mdi mdi-trash-can-outline"></i>
                    </button>
                </div>
            </div>
            
            <!-- Hover Indicator -->
            <div class="exp-item-indicator"></div>
        </div>
        <?php endforeach; ?>
    </div>
    
    <!-- No Results State -->
    <div class="exp-no-results" id="exp-no-results" style="display: none;">
        <div class="exp-no-results-icon">
            <i class="mdi mdi-file-search-outline"></i>
        </div>
        <h4><?php echo get_phrase('no_results_found'); ?></h4>
        <p><?php echo get_phrase('try_different_search_terms'); ?></p>
        <button type="button" class="exp-reset-btn" onclick="resetExpenseFilters()">
            <i class="mdi mdi-refresh"></i>
            <?php echo get_phrase('reset_filters'); ?>
        </button>
    </div>
</div>

<!-- Premium Pagination -->
<div class="exp-pagination-bar">
    <div class="exp-pagination-info">
        <div class="exp-info-text">
            <span><?php echo get_phrase('showing'); ?></span>
            <strong id="exp-showing-start">1</strong>
            <span>-</span>
            <strong id="exp-showing-end"><?php echo min(10, $total_expenses); ?></strong>
            <span><?php echo get_phrase('of'); ?></span>
            <strong id="exp-total-count"><?php echo $total_expenses; ?></strong>
            <span><?php echo get_phrase('expenses'); ?></span>
        </div>
        <div class="exp-progress-bar">
            <div class="exp-progress-fill" id="exp-progress-fill" style="width: <?php echo min(100, ($total_expenses > 0 ? (10 / $total_expenses) * 100 : 100)); ?>%"></div>
        </div>
    </div>
    
    <div class="exp-pagination-nav">
        <button type="button" class="exp-nav-btn exp-nav-first" id="exp-nav-first" title="<?php echo get_phrase('first'); ?>">
            <i class="mdi mdi-page-first"></i>
        </button>
        <button type="button" class="exp-nav-btn exp-nav-prev" id="exp-nav-prev" title="<?php echo get_phrase('previous'); ?>">
            <i class="mdi mdi-chevron-left"></i>
        </button>
        
        <div class="exp-page-indicators" id="exp-page-indicators">
            <!-- Page indicators will be generated by JS -->
        </div>
        
        <button type="button" class="exp-nav-btn exp-nav-next" id="exp-nav-next" title="<?php echo get_phrase('next'); ?>">
            <i class="mdi mdi-chevron-right"></i>
        </button>
        <button type="button" class="exp-nav-btn exp-nav-last" id="exp-nav-last" title="<?php echo get_phrase('last'); ?>">
            <i class="mdi mdi-page-last"></i>
        </button>
    </div>
    
    <div class="exp-pagination-jump">
        <span><?php echo get_phrase('page'); ?></span>
        <input type="number" id="exp-page-input" class="exp-page-field" min="1" value="1">
        <span><?php echo get_phrase('of'); ?></span>
        <span id="exp-total-pages">1</span>
        <button type="button" class="exp-go-btn" id="exp-go-btn"><?php echo get_phrase('go'); ?></button>
    </div>
</div>

<?php else: ?>

<!-- Premium Empty State -->
<div class="exp-empty-state">
    <div class="exp-empty-visual">
        <div class="exp-empty-icon-container">
            <div class="exp-empty-circle exp-circle-1"></div>
            <div class="exp-empty-circle exp-circle-2"></div>
            <div class="exp-empty-circle exp-circle-3"></div>
            <div class="exp-empty-icon">
                <i class="mdi mdi-cash-plus"></i>
            </div>
        </div>
    </div>
    <div class="exp-empty-content">
        <h3><?php echo get_phrase('no_expenses_found'); ?></h3>
        <p><?php echo get_phrase('create_your_first_expense_to_track_spending'); ?></p>
        <button type="button" class="exp-create-btn" 
                onclick="rightModal('<?php echo site_url('modal/popup/expense/create'); ?>', '<?php echo get_phrase('add_new_expense'); ?>')">
            <i class="mdi mdi-plus"></i>
            <span><?php echo get_phrase('create_expense'); ?></span>
        </button>
    </div>
</div>

<?php endif; ?>

<script>
(function() {
    'use strict';

    // State Management
    const state = {
        currentPage: 1,
        perPage: 10,
        totalItems: 0,
        totalPages: 0,
        searchTerm: '',
        sortBy: 'date',
        sortOrder: 'desc',
        items: [],
        filteredItems: []
    };
    
    // DOM Elements
    const elements = {
        listBody: document.getElementById('exp-list-body'),
        searchInput: document.getElementById('exp-search'),
        clearSearch: document.getElementById('exp-clear-search'),
        perPageSelect: document.getElementById('exp-per-page'),
        sortCols: document.querySelectorAll('.exp-sortable-col'),
        navFirst: document.getElementById('exp-nav-first'),
        navPrev: document.getElementById('exp-nav-prev'),
        navNext: document.getElementById('exp-nav-next'),
        navLast: document.getElementById('exp-nav-last'),
        pageIndicators: document.getElementById('exp-page-indicators'),
        pageInput: document.getElementById('exp-page-input'),
        goBtn: document.getElementById('exp-go-btn'),
        showingStart: document.getElementById('exp-showing-start'),
        showingEnd: document.getElementById('exp-showing-end'),
        totalCount: document.getElementById('exp-total-count'),
        totalPages: document.getElementById('exp-total-pages'),
        progressFill: document.getElementById('exp-progress-fill'),
        noResults: document.getElementById('exp-no-results')
    };
    
    // Initialize
    function init() {
        collectItems();
        bindEvents();
        animateCounters();
        update();
    }
    
    // Collect items from DOM
    function collectItems() {
        const items = document.querySelectorAll('.exp-list-item');
        state.items = Array.from(items).map(item => ({
            element: item,
            id: item.dataset.id,
            date: parseInt(item.dataset.date) || 0,
            amount: parseFloat(item.dataset.amount) || 0,
            category: item.dataset.category || ''
        }));
        state.filteredItems = [...state.items];
        state.totalItems = state.items.length;
    }
    
    // Bind events
    function bindEvents() {
        // Search
        if (elements.searchInput) {
            elements.searchInput.addEventListener('input', debounce(handleSearch, 300));
            elements.clearSearch.addEventListener('click', clearSearch);
        }
        
        // Per page
        if (elements.perPageSelect) {
            elements.perPageSelect.addEventListener('change', handlePerPageChange);
        }
        
        // Sort columns
        elements.sortCols.forEach(col => {
            col.addEventListener('click', handleSortClick);
        });
        
        // Navigation
        if (elements.navFirst) elements.navFirst.addEventListener('click', () => goToPage(1));
        if (elements.navPrev) elements.navPrev.addEventListener('click', () => goToPage(state.currentPage - 1));
        if (elements.navNext) elements.navNext.addEventListener('click', () => goToPage(state.currentPage + 1));
        if (elements.navLast) elements.navLast.addEventListener('click', () => goToPage(state.totalPages));
        
        // Page input
        if (elements.goBtn) {
            elements.goBtn.addEventListener('click', handlePageJump);
            elements.pageInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') handlePageJump();
            });
        }
        
        // Keyboard shortcuts
        document.addEventListener('keydown', handleKeyboard);
    }
    
    // Debounce utility
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, args), wait);
        };
    }
    
    // Handle search
    function handleSearch(e) {
        state.searchTerm = e.target.value.toLowerCase().trim();
        state.currentPage = 1;
        filterAndSort();
        update();
    }
    
    // Clear search
    function clearSearch() {
        elements.searchInput.value = '';
        state.searchTerm = '';
        state.currentPage = 1;
        filterAndSort();
        update();
    }
    
    // Handle per page change
    function handlePerPageChange(e) {
        state.perPage = parseInt(e.target.value);
        state.currentPage = 1;
        update();
    }
    
    // Handle sort click
    function handleSortClick(e) {
        const col = e.currentTarget;
        const sortBy = col.dataset.sort;
        
        if (state.sortBy === sortBy) {
            state.sortOrder = state.sortOrder === 'asc' ? 'desc' : 'asc';
        } else {
            state.sortBy = sortBy;
            state.sortOrder = 'asc';
        }
        
        elements.sortCols.forEach(c => {
            c.classList.remove('sort-asc', 'sort-desc');
        });
        col.classList.add(`sort-${state.sortOrder}`);
        
        filterAndSort();
        update();
    }
    
    // Filter and sort
    function filterAndSort() {
        // Filter
        state.filteredItems = state.items.filter(item => {
            const matchesSearch = !state.searchTerm ||
                item.category.includes(state.searchTerm);
            return matchesSearch;
        });

        // Sort
        state.filteredItems.sort((a, b) => {
            let aVal, bVal;

            switch (state.sortBy) {
                case 'date':
                    aVal = a.date;
                    bVal = b.date;
                    break;
                case 'amount':
                    aVal = a.amount;
                    bVal = b.amount;
                    break;
                case 'category':
                    aVal = a.category;
                    bVal = b.category;
                    break;
                default:
                    return 0;
            }

            if (state.sortOrder === 'asc') {
                return aVal > bVal ? 1 : aVal < bVal ? -1 : 0;
            } else {
                return aVal < bVal ? 1 : aVal > bVal ? -1 : 0;
            }
        });

        state.totalItems = state.filteredItems.length;
    }
    
    // Update display
    function update() {
        state.totalPages = Math.max(1, Math.ceil(state.totalItems / state.perPage));
        
        if (state.currentPage > state.totalPages) {
            state.currentPage = state.totalPages;
        }
        
        const startIndex = (state.currentPage - 1) * state.perPage;
        const endIndex = Math.min(startIndex + state.perPage, state.totalItems);
        
        // Hide all items
        state.items.forEach(item => {
            item.element.style.display = 'none';
            item.element.classList.remove('exp-item-visible');
        });
        
        // Show current page items with animation
        let delay = 0;
        for (let i = startIndex; i < endIndex; i++) {
            const item = state.filteredItems[i];
            if (item) {
                item.element.style.display = '';
                item.element.style.setProperty('--delay', `${delay * 0.04}s`);
                setTimeout(() => {
                    item.element.classList.add('exp-item-visible');
                }, 10);
                delay++;
            }
        }
        
        // Show/hide no results
        if (elements.noResults) {
            elements.noResults.style.display = state.totalItems === 0 ? 'flex' : 'none';
            if (elements.listBody) {
                elements.listBody.style.display = state.totalItems === 0 ? 'none' : '';
            }
        }
        
        updatePaginationInfo(startIndex + 1, endIndex);
        updatePaginationNav();
        updatePageIndicators();
    }
    
    // Update pagination info
    function updatePaginationInfo(start, end) {
        if (elements.showingStart) elements.showingStart.textContent = state.totalItems > 0 ? start : 0;
        if (elements.showingEnd) elements.showingEnd.textContent = end;
        if (elements.totalCount) elements.totalCount.textContent = state.totalItems;
        if (elements.totalPages) elements.totalPages.textContent = state.totalPages;
        if (elements.pageInput) elements.pageInput.max = state.totalPages;

        // Update progress bar
        if (elements.progressFill && state.totalItems > 0) {
            const progress = (end / state.totalItems) * 100;
            elements.progressFill.style.width = `${progress}%`;
        }
    }
    
    // Update navigation buttons
    function updatePaginationNav() {
        const isFirst = state.currentPage === 1;
        const isLast = state.currentPage === state.totalPages;
        
        if (elements.navFirst) elements.navFirst.disabled = isFirst;
        if (elements.navPrev) elements.navPrev.disabled = isFirst;
        if (elements.navNext) elements.navNext.disabled = isLast;
        if (elements.navLast) elements.navLast.disabled = isLast;
    }
    
    // Update page indicators
    function updatePageIndicators() {
        if (!elements.pageIndicators) return;
        
        elements.pageIndicators.innerHTML = '';
        
        const maxVisible = 5;
        let start = Math.max(1, state.currentPage - Math.floor(maxVisible / 2));
        let end = Math.min(state.totalPages, start + maxVisible - 1);
        
        if (end - start + 1 < maxVisible) {
            start = Math.max(1, end - maxVisible + 1);
        }
        
        // First page + ellipsis
        if (start > 1) {
            elements.pageIndicators.appendChild(createPageBtn(1));
            if (start > 2) {
                const ellipsis = document.createElement('span');
                ellipsis.className = 'exp-page-ellipsis';
                ellipsis.textContent = '...';
                elements.pageIndicators.appendChild(ellipsis);
            }
        }
        
        // Page numbers
        for (let i = start; i <= end; i++) {
            elements.pageIndicators.appendChild(createPageBtn(i, i === state.currentPage));
        }
        
        // Last page + ellipsis
        if (end < state.totalPages) {
            if (end < state.totalPages - 1) {
                const ellipsis = document.createElement('span');
                ellipsis.className = 'exp-page-ellipsis';
                ellipsis.textContent = '...';
                elements.pageIndicators.appendChild(ellipsis);
            }
            elements.pageIndicators.appendChild(createPageBtn(state.totalPages));
        }
    }
    
    // Create page button
    function createPageBtn(page, isActive = false) {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = `exp-page-btn ${isActive ? 'active' : ''}`;
        btn.textContent = page;
        btn.addEventListener('click', () => goToPage(page));
        return btn;
    }
    
    // Go to page
    function goToPage(page) {
        if (page >= 1 && page <= state.totalPages && page !== state.currentPage) {
            state.currentPage = page;
            elements.pageInput.value = page;
            update();
            
            // Scroll to top of list
            if (elements.listBody) {
                elements.listBody.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }
    }
    
    // Handle page jump
    function handlePageJump() {
        const page = parseInt(elements.pageInput.value);
        if (!isNaN(page)) {
            goToPage(page);
        }
    }
    
    // Handle keyboard shortcuts
    function handleKeyboard(e) {
        // Cmd/Ctrl + K for search focus
        if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
            e.preventDefault();
            if (elements.searchInput) {
                elements.searchInput.focus();
            }
        }
        
        // Arrow keys for pagination (when not in input)
        if (document.activeElement.tagName !== 'INPUT') {
            if (e.key === 'ArrowLeft') {
                goToPage(state.currentPage - 1);
            } else if (e.key === 'ArrowRight') {
                goToPage(state.currentPage + 1);
            }
        }
    }
    
    // Animate counters
    function animateCounters() {
        document.querySelectorAll('.exp-stat-value[data-count]').forEach(el => {
            const target = parseInt(el.dataset.count);
            const duration = 1000;
            const start = performance.now();
            
            function animate(currentTime) {
                const elapsed = currentTime - start;
                const progress = Math.min(elapsed / duration, 1);
                
                // Easing function
                const easeOut = 1 - Math.pow(1 - progress, 3);
                const current = Math.round(easeOut * target);
                
                el.textContent = current;
                
                if (progress < 1) {
                    requestAnimationFrame(animate);
                }
            }
            
            requestAnimationFrame(animate);
        });
    }
    
    // Reset filters (global function)
    window.resetExpenseFilters = function() {
        if (elements.searchInput) elements.searchInput.value = '';
        state.searchTerm = '';
        state.currentPage = 1;

        filterAndSort();
        update();
    };
    
    // Init on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
</script>

<style>
/* ============================================================================
   PREMIUM EXPENSE LIST - MODERN UI
   ============================================================================ */

:root {
    --exp-primary: #6366f1;
    --exp-primary-rgb: 99, 102, 241;
    --exp-success: #10b981;
    --exp-success-rgb: 16, 185, 129;
    --exp-warning: #f59e0b;
    --exp-warning-rgb: 245, 158, 11;
    --exp-danger: #ef4444;
    --exp-danger-rgb: 239, 68, 68;
    --exp-dark: #1e293b;
    --exp-gray: #64748b;
    --exp-light: #f8fafc;
    --exp-border: #e2e8f0;
    --exp-white: #ffffff;
    --exp-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    --exp-shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}

/* Dashboard Stats */
.exp-dashboard {
    margin-bottom: 2rem;
    padding: 1.5rem;
}

.exp-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.5rem;
}

.exp-stat-card {
    position: relative;
    background: var(--exp-white);
    border-radius: 20px;
    padding: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1.25rem;
    border: 1px solid var(--exp-border);
    overflow: hidden;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.exp-stat-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--exp-shadow-lg);
}

.exp-stat-glow {
    position: absolute;
    top: -50%;
    right: -50%;
    width: 100%;
    height: 200%;
    background: radial-gradient(circle, rgba(var(--exp-primary-rgb), 0.1) 0%, transparent 70%);
    pointer-events: none;
}

.exp-stat-total .exp-stat-glow { background: radial-gradient(circle, rgba(var(--exp-primary-rgb), 0.15) 0%, transparent 70%); }
.exp-stat-amount .exp-stat-glow { background: radial-gradient(circle, rgba(var(--exp-success-rgb), 0.15) 0%, transparent 70%); }

.exp-stat-icon-wrap {
    position: relative;
    width: 60px;
    height: 60px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.75rem;
    flex-shrink: 0;
}

.exp-stat-total .exp-stat-icon-wrap { background: linear-gradient(135deg, #6366f1, #8b5cf6); color: white; }
.exp-stat-amount .exp-stat-icon-wrap { background: linear-gradient(135deg, #10b981, #34d399); color: white; }

.exp-stat-pulse {
    position: absolute;
    inset: 0;
    border-radius: 16px;
    animation: pulse 2s ease-in-out infinite;
}

.exp-stat-total .exp-stat-pulse { background: rgba(var(--exp-primary-rgb), 0.3); }
.exp-stat-amount .exp-stat-pulse { background: rgba(var(--exp-success-rgb), 0.3); }

@keyframes pulse {
    0%, 100% { transform: scale(1); opacity: 0; }
    50% { transform: scale(1.2); opacity: 1; }
}

.exp-stat-data {
    flex: 1;
}

.exp-stat-value {
    display: block;
    font-size: 2rem;
    font-weight: 800;
    color: var(--exp-dark);
    line-height: 1;
}

.exp-stat-value-amount {
    display: block;
    font-size: 1.5rem;
    font-weight: 800;
    color: var(--exp-dark);
    line-height: 1;
}

.exp-stat-title {
    display: block;
    font-size: 0.875rem;
    color: var(--exp-gray);
    margin-top: 0.5rem;
    font-weight: 500;
}

.exp-stat-trend {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.5rem 0.75rem;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
    background: rgba(var(--exp-success-rgb), 0.1);
    color: var(--exp-success);
}

.exp-stat-trend.warning {
    background: rgba(var(--exp-warning-rgb), 0.1);
    color: var(--exp-warning);
}

/* Toolbar */
.exp-toolbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1.5rem;
    margin-bottom: 1.5rem;
    padding: 1.25rem 1.5rem;
    background: var(--exp-white);
    border-radius: 16px;
    border: 1px solid var(--exp-border);
    flex-wrap: wrap;
}

.exp-toolbar-left {
    flex: 1;
    min-width: 280px;
    max-width: 400px;
}

.exp-search-box {
    position: relative;
    display: flex;
    align-items: center;
}

.exp-search-icon-wrapper {
    position: absolute;
    left: 1rem;
    color: var(--exp-gray);
    font-size: 1.25rem;
    z-index: 1;
}

.exp-search-field {
    width: 100%;
    padding: 0.875rem 1rem 0.875rem 3rem;
    border: 2px solid var(--exp-border);
    border-radius: 12px;
    font-size: 0.9375rem;
    background: var(--exp-light);
    color: var(--exp-dark);
    transition: all 0.2s;
}

.exp-search-field:focus {
    outline: none;
    border-color: var(--exp-primary);
    background: var(--exp-white);
    box-shadow: 0 0 0 4px rgba(var(--exp-primary-rgb), 0.1);
}

.exp-search-clear-btn {
    position: absolute;
    right: 4.5rem;
    background: none;
    border: none;
    color: var(--exp-gray);
    cursor: pointer;
    padding: 0.25rem;
    opacity: 0.5;
    transition: all 0.2s;
}

.exp-search-clear-btn:hover {
    opacity: 1;
    color: var(--exp-danger);
}

.exp-search-shortcut {
    position: absolute;
    right: 1rem;
    display: flex;
    gap: 0.25rem;
}

.exp-search-shortcut kbd {
    padding: 0.25rem 0.5rem;
    background: var(--exp-border);
    border-radius: 6px;
    font-size: 0.75rem;
    font-family: inherit;
    color: var(--exp-gray);
}

.exp-toolbar-right {
    display: flex;
    align-items: center;
    gap: 1.5rem;
    flex-wrap: wrap;
}

.exp-per-page-select {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.exp-per-page-select label {
    font-size: 0.875rem;
    color: var(--exp-gray);
    font-weight: 500;
}

.exp-per-page-select select {
    padding: 0.5rem 2rem 0.5rem 0.75rem;
    border: 2px solid var(--exp-border);
    border-radius: 8px;
    font-size: 0.875rem;
    background: var(--exp-white);
    color: var(--exp-dark);
    cursor: pointer;
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%2364748b' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 0.75rem center;
}

/* List Container */
.exp-list-container {
    background: var(--exp-white);
    border-radius: 20px;
    border: 1px solid var(--exp-border);
    overflow: hidden;
}

.exp-list-header {
    display: grid;
    grid-template-columns: 1.5fr 1fr 2fr 120px;
    gap: 1rem;
    padding: 1rem 1.5rem;
    background: linear-gradient(135deg, var(--exp-dark) 0%, #334155 100%);
    color: white;
    font-size: 0.8125rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.exp-sortable-col {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
    transition: all 0.2s;
}

.exp-sortable-col:hover {
    color: rgba(255, 255, 255, 0.8);
}

.exp-sort-icon {
    opacity: 0.5;
    transition: all 0.2s;
}

.exp-sortable-col:hover .exp-sort-icon,
.exp-sortable-col.sort-asc .exp-sort-icon,
.exp-sortable-col.sort-desc .exp-sort-icon {
    opacity: 1;
}

.exp-sortable-col.sort-asc .exp-sort-icon i { transform: rotate(180deg); }

.exp-col-actions {
    text-align: center;
}

/* List Body */
.exp-list-body {
    max-height: 600px;
    overflow-y: auto;
}

.exp-list-item {
    display: grid;
    grid-template-columns: 1.5fr 1fr 2fr 120px;
    gap: 1rem;
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid var(--exp-border);
    position: relative;
    opacity: 0;
    transform: translateY(10px);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    transition-delay: var(--delay, 0s);
}

.exp-list-item.exp-item-visible {
    opacity: 1;
    transform: translateY(0);
}

.exp-list-item:hover {
    background: linear-gradient(135deg, rgba(var(--exp-primary-rgb), 0.03), rgba(var(--exp-primary-rgb), 0.06));
}

.exp-list-item:last-child {
    border-bottom: none;
}

.exp-item-indicator {
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 4px;
    background: transparent;
    transition: all 0.2s;
}

.exp-list-item:hover .exp-item-indicator {
    background: linear-gradient(180deg, var(--exp-primary), #8b5cf6);
}

/* Date Column */
.exp-col-date {
    display: flex;
    align-items: center;
}

.exp-date-display {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.exp-date-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    background: var(--exp-light);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--exp-primary);
    font-size: 1.25rem;
}

.exp-date-text {
    display: flex;
    flex-direction: column;
}

.exp-date-main {
    font-size: 0.9375rem;
    font-weight: 600;
    color: var(--exp-dark);
}

.exp-date-day {
    font-size: 0.75rem;
    color: var(--exp-gray);
}

/* Amount Column */
.exp-col-amount {
    display: flex;
    align-items: center;
}

.exp-amount-display {
    display: flex;
    flex-direction: column;
}

.exp-amount-value {
    font-size: 1.125rem;
    font-weight: 700;
    color: var(--exp-success);
    background: linear-gradient(135deg, rgba(var(--exp-success-rgb), 0.1), rgba(var(--exp-success-rgb), 0.05));
    padding: 0.5rem 1rem;
    border-radius: 10px;
}

/* Category Column */
.exp-col-category {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.exp-item-avatar {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    background: linear-gradient(135deg, 
        hsl(var(--hue, 250), 80%, 60%), 
        hsl(calc(var(--hue, 250) + 30), 80%, 50%));
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 700;
    font-size: 0.875rem;
    flex-shrink: 0;
    box-shadow: 0 4px 12px hsla(var(--hue, 250), 80%, 50%, 0.3);
}

.exp-item-info {
    min-width: 0;
}

.exp-item-name {
    font-size: 0.9375rem;
    font-weight: 600;
    color: var(--exp-dark);
    margin: 0 0 0.25rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.exp-item-id {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    font-size: 0.75rem;
    color: var(--exp-gray);
    background: var(--exp-light);
    padding: 0.25rem 0.5rem;
    border-radius: 6px;
}

/* Actions Column */
.exp-col-actions {
    display: flex;
    align-items: center;
    justify-content: center;
}

.exp-action-buttons {
    display: flex;
    gap: 0.5rem;
}

.exp-action-btn {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.125rem;
    transition: all 0.2s;
    position: relative;
    overflow: hidden;
}

.exp-btn-edit {
    background: linear-gradient(135deg, rgba(var(--exp-primary-rgb), 0.1), rgba(var(--exp-primary-rgb), 0.05));
    color: var(--exp-primary);
}

.exp-btn-edit:hover {
    background: linear-gradient(135deg, var(--exp-primary), #8b5cf6);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(var(--exp-primary-rgb), 0.3);
}

.exp-btn-delete {
    background: linear-gradient(135deg, rgba(var(--exp-danger-rgb), 0.1), rgba(var(--exp-danger-rgb), 0.05));
    color: var(--exp-danger);
}

.exp-btn-delete:hover {
    background: linear-gradient(135deg, var(--exp-danger), #f87171);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(var(--exp-danger-rgb), 0.3);
}

/* No Results */
.exp-no-results {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 4rem 2rem;
    text-align: center;
}

.exp-no-results-icon {
    width: 80px;
    height: 80px;
    border-radius: 20px;
    background: var(--exp-light);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.5rem;
    color: var(--exp-gray);
    margin-bottom: 1.5rem;
}

.exp-no-results h4 {
    font-size: 1.25rem;
    color: var(--exp-dark);
    margin: 0 0 0.5rem;
}

.exp-no-results p {
    font-size: 0.9375rem;
    color: var(--exp-gray);
    margin: 0 0 1.5rem;
}

.exp-reset-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    background: var(--exp-primary);
    color: white;
    border: none;
    border-radius: 10px;
    font-size: 0.9375rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
}

.exp-reset-btn:hover {
    background: #4f46e5;
    transform: translateY(-2px);
}

/* Pagination Bar */
.exp-pagination-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.25rem 1.5rem;
    background: linear-gradient(135deg, var(--exp-light), #f1f5f9);
    border-top: 1px solid var(--exp-border);
    gap: 1.5rem;
    flex-wrap: wrap;
}

.exp-pagination-info {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.exp-info-text {
    font-size: 0.875rem;
    color: var(--exp-gray);
}

.exp-info-text strong {
    color: var(--exp-dark);
    font-weight: 600;
}

.exp-progress-bar {
    width: 120px;
    height: 4px;
    background: var(--exp-border);
    border-radius: 2px;
    overflow: hidden;
}

.exp-progress-fill {
    height: 100%;
    background: linear-gradient(90deg, var(--exp-primary), #8b5cf6);
    border-radius: 2px;
    transition: width 0.3s ease;
}

.exp-pagination-nav {
    display: flex;
    align-items: center;
    gap: 0.375rem;
}

.exp-nav-btn {
    width: 40px;
    height: 40px;
    border: 2px solid var(--exp-border);
    background: var(--exp-white);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    color: var(--exp-gray);
    font-size: 1.25rem;
    transition: all 0.2s;
}

.exp-nav-btn:hover:not(:disabled) {
    border-color: var(--exp-primary);
    color: var(--exp-primary);
    transform: translateY(-1px);
}

.exp-nav-btn:disabled {
    opacity: 0.4;
    cursor: not-allowed;
}

.exp-page-indicators {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    margin: 0 0.5rem;
}

.exp-page-btn {
    min-width: 40px;
    height: 40px;
    border: 2px solid var(--exp-border);
    background: var(--exp-white);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    color: var(--exp-gray);
    font-size: 0.875rem;
    font-weight: 600;
    transition: all 0.2s;
    padding: 0 0.5rem;
}

.exp-page-btn:hover {
    border-color: var(--exp-primary);
    color: var(--exp-primary);
}

.exp-page-btn.active {
    background: linear-gradient(135deg, var(--exp-primary), #8b5cf6);
    border-color: var(--exp-primary);
    color: white;
}

.exp-page-ellipsis {
    padding: 0 0.5rem;
    color: var(--exp-gray);
}

.exp-pagination-jump {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.875rem;
    color: var(--exp-gray);
}

.exp-page-field {
    width: 60px;
    padding: 0.5rem;
    border: 2px solid var(--exp-border);
    border-radius: 8px;
    text-align: center;
    font-size: 0.875rem;
    background: var(--exp-white);
    transition: all 0.2s;
}

.exp-page-field:focus {
    outline: none;
    border-color: var(--exp-primary);
}

.exp-go-btn {
    padding: 0.5rem 1rem;
    background: var(--exp-primary);
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 0.8125rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    text-transform: uppercase;
}

.exp-go-btn:hover {
    background: #4f46e5;
}

/* Empty State */
.exp-empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 4rem 2rem;
    text-align: center;
    background: var(--exp-white);
    border-radius: 20px;
    border: 1px solid var(--exp-border);
}

.exp-empty-visual {
    margin-bottom: 2rem;
}

.exp-empty-icon-container {
    position: relative;
    width: 120px;
    height: 120px;
}

.exp-empty-circle {
    position: absolute;
    border-radius: 50%;
    border: 2px dashed var(--exp-border);
}

.exp-circle-1 { inset: 0; animation: rotate 20s linear infinite; }
.exp-circle-2 { inset: 15px; animation: rotate 15s linear infinite reverse; }
.exp-circle-3 { inset: 30px; animation: rotate 10s linear infinite; }

@keyframes rotate {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.exp-empty-icon {
    position: absolute;
    inset: 35px;
    background: linear-gradient(135deg, var(--exp-primary), #8b5cf6);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    color: white;
}

.exp-empty-content h3 {
    font-size: 1.5rem;
    color: var(--exp-dark);
    margin: 0 0 0.75rem;
}

.exp-empty-content p {
    font-size: 1rem;
    color: var(--exp-gray);
    margin: 0 0 2rem;
    max-width: 320px;
}

.exp-create-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 1rem 2rem;
    background: linear-gradient(135deg, var(--exp-primary), #8b5cf6);
    color: white;
    border: none;
    border-radius: 14px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
}

.exp-create-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(var(--exp-primary-rgb), 0.4);
}

/* Responsive */
@media (max-width: 1024px) {
    .exp-toolbar {
        flex-direction: column;
        align-items: stretch;
    }

    .exp-toolbar-left {
        max-width: none;
    }

    .exp-toolbar-right {
        justify-content: space-between;
    }

    .exp-list-header,
    .exp-list-item {
        grid-template-columns: 1.5fr 1fr 100px;
    }

    .exp-col-category {
        display: none;
    }

    .exp-pagination-bar {
        flex-direction: column;
        align-items: stretch;
        text-align: center;
    }

    .exp-pagination-info {
        align-items: center;
    }

    .exp-pagination-nav {
        justify-content: center;
    }

    .exp-pagination-jump {
        justify-content: center;
    }
}

@media (max-width: 768px) {
    .exp-stats-grid {
        grid-template-columns: 1fr;
    }

    .exp-list-header,
    .exp-list-item {
        grid-template-columns: 1fr 80px;
    }

    .exp-col-amount {
        display: none;
    }

    .exp-date-icon {
        width: 36px;
        height: 36px;
        font-size: 1rem;
    }

    .exp-search-shortcut {
        display: none;
    }
}

@media (max-width: 480px) {
    .exp-nav-btn,
    .exp-page-btn {
        width: 36px;
        height: 36px;
        font-size: 0.875rem;
    }

    .exp-action-btn {
        width: 36px;
        height: 36px;
    }
}
</style>
