<?php
/**
 * Expense Category List View
 *
 * This file displays a modern, fully internationalized list of expense categories
 * with advanced filtering, sorting, and pagination features.
 *
 * All text strings are properly translated using get_phrase() function
 * to support multiple languages.
 */

$expense_categories = $this->crud_model->get_expense_categories()->result_array();
$total_categories = count($expense_categories);
$with_cost_center = count(array_filter($expense_categories, function($cat) { return !empty($cat['cost_center']); }));
?>

<?php if ($total_categories > 0): ?>

<!-- Premium Stats Dashboard -->
<div class="ec-dashboard">
    <div class="ec-stats-grid">
        <div class="ec-stat-card ec-stat-total">
            <div class="ec-stat-glow"></div>
            <div class="ec-stat-icon-wrap">
                <i class="mdi mdi-tag-multiple"></i>
                <div class="ec-stat-pulse"></div>
            </div>
            <div class="ec-stat-data">
                <span class="ec-stat-value" data-count="<?php echo $total_categories; ?>">0</span>
                <span class="ec-stat-title"><?php echo get_phrase('total_categories'); ?></span>
            </div>
            <div class="ec-stat-trend">
                <i class="mdi mdi-trending-up"></i>
                <span>100%</span>
            </div>
        </div>

        <div class="ec-stat-card ec-stat-active">
            <div class="ec-stat-glow"></div>
            <div class="ec-stat-icon-wrap">
                <i class="mdi mdi-check-decagram"></i>
                <div class="ec-stat-pulse"></div>
            </div>
            <div class="ec-stat-data">
                <span class="ec-stat-value" data-count="<?php echo $with_cost_center; ?>">0</span>
                <span class="ec-stat-title"><?php echo get_phrase('with_cost_center'); ?></span>
            </div>
            <div class="ec-stat-trend">
                <i class="mdi mdi-chart-line"></i>
                <span><?php echo $total_categories > 0 ? round(($with_cost_center / $total_categories) * 100) : 0; ?>%</span>
            </div>
        </div>
    </div>
</div>

<!-- Premium Search & Filter Bar -->
<div class="ec-toolbar">
    <div class="ec-toolbar-left">
        <div class="ec-search-box">
            <div class="ec-search-icon-wrapper">
                <i class="mdi mdi-magnify"></i>
            </div>
            <input type="text" id="ec-search" class="ec-search-field" placeholder="<?php echo get_phrase('search_categories'); ?>..." autocomplete="off">
            <button type="button" class="ec-search-clear-btn" id="ec-clear-search">
                <i class="mdi mdi-close-circle"></i>
            </button>
            <div class="ec-search-shortcut">
                <kbd>⌘</kbd><kbd>K</kbd>
            </div>
        </div>
    </div>
    
    <div class="ec-toolbar-right">
        <div class="ec-filter-chips">
            <button type="button" class="ec-chip active" data-filter="all">
                <i class="mdi mdi-view-grid"></i>
                <span><?php echo get_phrase('all'); ?></span>
                <span class="ec-chip-count"><?php echo $total_categories; ?></span>
            </button>
            <button type="button" class="ec-chip" data-filter="with_cost">
                <i class="mdi mdi-check-circle"></i>
                <span><?php echo get_phrase('configured'); ?></span>
                <span class="ec-chip-count"><?php echo $with_cost_center; ?></span>
            </button>
        </div>
        
        <div class="ec-view-options">
            <div class="ec-per-page-select">
                <label><?php echo get_phrase('show'); ?></label>
                <select id="ec-per-page">
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
<div class="ec-list-container">
    <!-- List Header -->
    <div class="ec-list-header">
        <div class="ec-col-category ec-sortable-col" data-sort="name">
            <span><?php echo get_phrase('category'); ?></span>
            <div class="ec-sort-icon">
                <i class="mdi mdi-unfold-more-horizontal"></i>
            </div>
        </div>
        <div class="ec-col-cost ec-sortable-col" data-sort="cost_center">
            <span><?php echo get_phrase('cost_center'); ?></span>
            <div class="ec-sort-icon">
                <i class="mdi mdi-unfold-more-horizontal"></i>
            </div>
        </div>
        <div class="ec-col-department ec-sortable-col" data-sort="department">
            <span><?php echo get_phrase('department'); ?></span>
            <div class="ec-sort-icon">
                <i class="mdi mdi-unfold-more-horizontal"></i>
            </div>
        </div>
        <div class="ec-col-date ec-sortable-col" data-sort="date">
            <span><?php echo get_phrase('created'); ?></span>
            <div class="ec-sort-icon">
                <i class="mdi mdi-unfold-more-horizontal"></i>
            </div>
        </div>
        <div class="ec-col-actions">
            <span><?php echo get_phrase('actions'); ?></span>
        </div>
    </div>
    
    <!-- List Body -->
    <div class="ec-list-body" id="ec-list-body">
        <?php foreach ($expense_categories as $index => $category): ?>
        <div class="ec-list-item" 
             data-id="<?php echo $category['id']; ?>"
             data-name="<?php echo strtolower($category['name']); ?>"
             data-cost="<?php echo strtolower($category['cost_center'] ?? ''); ?>"
             data-department="<?php echo strtolower($category['department'] ?? ''); ?>"
             data-has-cost="<?php echo !empty($category['cost_center']) ? '1' : '0'; ?>"
             data-date="<?php echo strtotime($category['date_added'] ?? 'now'); ?>"
             style="--delay: <?php echo $index * 0.03; ?>s">
            
            <!-- Category Column -->
            <div class="ec-col-category">
                <div class="ec-item-avatar" style="--hue: <?php echo (ord($category['name'][0]) * 15) % 360; ?>">
                    <span><?php echo strtoupper(substr($category['name'], 0, 2)); ?></span>
                </div>
                <div class="ec-item-info">
                    <h4 class="ec-item-name"><?php echo $category['name']; ?></h4>
                    <span class="ec-item-id">
                        <i class="mdi mdi-identifier"></i>
                        CAT-<?php echo str_pad($category['id'], 4, '0', STR_PAD_LEFT); ?>
                    </span>
                </div>
            </div>
            
            <!-- Cost Center Column -->
            <div class="ec-col-cost">
                <div class="ec-cost-value">
                    <?php if (!empty($category['cost_center'])): ?>
                        <span class="ec-cost-configured"><?php echo $category['cost_center']; ?></span>
                    <?php else: ?>
                        <span class="ec-cost-empty"><?php echo get_phrase('not_defined'); ?></span>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Department Column -->
            <div class="ec-col-department">
                <div class="ec-department-value">
                    <?php if (!empty($category['department'])): ?>
                        <span class="ec-department-configured"><?php echo $category['department']; ?></span>
                    <?php else: ?>
                        <span class="ec-department-empty"><?php echo get_phrase('not_defined'); ?></span>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Date Column -->
            <div class="ec-col-date">
                <div class="ec-date-display">
                    <div class="ec-date-icon">
                        <i class="mdi mdi-calendar-month"></i>
                    </div>
                    <div class="ec-date-text">
                        <span class="ec-date-main"><?php echo date('d M Y', strtotime($category['date_added'] ?? 'now')); ?></span>
                        <span class="ec-date-time"><?php echo date('H:i', strtotime($category['date_added'] ?? 'now')); ?></span>
                    </div>
                </div>
            </div>
            
            <!-- Actions Column -->
            <div class="ec-col-actions">
                <div class="ec-action-buttons">
                    <button type="button" class="ec-action-btn ec-btn-edit" 
                            title="<?php echo get_phrase('edit'); ?>"
                            onclick="rightModal('<?php echo site_url('modal/popup/expense_category/edit/'.$category['id'])?>', '<?php echo get_phrase('update_expense_category'); ?>')">
                        <i class="mdi mdi-pencil-outline"></i>
                    </button>
                    <button type="button" class="ec-action-btn ec-btn-delete" 
                            title="<?php echo get_phrase('delete'); ?>"
                            onclick="confirmModal('<?php echo route('expense_category/delete/'.$category['id']); ?>', showAllExpenseCategories)">
                        <i class="mdi mdi-trash-can-outline"></i>
                    </button>
                </div>
            </div>
            
            <!-- Hover Indicator -->
            <div class="ec-item-indicator"></div>
        </div>
        <?php endforeach; ?>
    </div>
    
    <!-- No Results State -->
    <div class="ec-no-results" id="ec-no-results" style="display: none;">
        <div class="ec-no-results-icon">
            <i class="mdi mdi-file-search-outline"></i>
        </div>
        <h4><?php echo get_phrase('no_results_found'); ?></h4>
        <p><?php echo get_phrase('try_different_search_terms'); ?></p>
        <button type="button" class="ec-reset-btn" onclick="resetFilters()">
            <i class="mdi mdi-refresh"></i>
            <?php echo get_phrase('reset_filters'); ?>
        </button>
    </div>
</div>

<!-- Premium Pagination -->
<div class="ec-pagination-bar">
    <div class="ec-pagination-info">
        <div class="ec-info-text">
            <span><?php echo get_phrase('showing'); ?></span>
            <strong id="ec-showing-start">1</strong>
            <span>-</span>
            <strong id="ec-showing-end"><?php echo min(10, $total_categories); ?></strong>
            <span><?php echo get_phrase('of'); ?></span>
            <strong id="ec-total-count"><?php echo $total_categories; ?></strong>
            <span><?php echo get_phrase('categories'); ?></span>
        </div>
        <div class="ec-progress-bar">
            <div class="ec-progress-fill" id="ec-progress-fill" style="width: <?php echo min(100, (10 / $total_categories) * 100); ?>%"></div>
        </div>
    </div>
    
    <div class="ec-pagination-nav">
        <button type="button" class="ec-nav-btn ec-nav-first" id="ec-nav-first" title="<?php echo get_phrase('first'); ?>">
            <i class="mdi mdi-page-first"></i>
        </button>
        <button type="button" class="ec-nav-btn ec-nav-prev" id="ec-nav-prev" title="<?php echo get_phrase('previous'); ?>">
            <i class="mdi mdi-chevron-left"></i>
        </button>
        
        <div class="ec-page-indicators" id="ec-page-indicators">
            <!-- Page indicators will be generated by JS -->
        </div>
        
        <button type="button" class="ec-nav-btn ec-nav-next" id="ec-nav-next" title="<?php echo get_phrase('next'); ?>">
            <i class="mdi mdi-chevron-right"></i>
        </button>
        <button type="button" class="ec-nav-btn ec-nav-last" id="ec-nav-last" title="<?php echo get_phrase('last'); ?>">
            <i class="mdi mdi-page-last"></i>
        </button>
    </div>
    
    <div class="ec-pagination-jump">
        <span><?php echo get_phrase('page'); ?></span>
        <input type="number" id="ec-page-input" class="ec-page-field" min="1" value="1">
        <span><?php echo get_phrase('of'); ?></span>
        <span id="ec-total-pages">1</span>
        <button type="button" class="ec-go-btn" id="ec-go-btn"><?php echo get_phrase('go'); ?></button>
    </div>
</div>

<?php else: ?>

<!-- Premium Empty State -->
<div class="ec-empty-state">
    <div class="ec-empty-visual">
        <div class="ec-empty-icon-container">
            <div class="ec-empty-circle ec-circle-1"></div>
            <div class="ec-empty-circle ec-circle-2"></div>
            <div class="ec-empty-circle ec-circle-3"></div>
            <div class="ec-empty-icon">
                <i class="mdi mdi-tag-plus-outline"></i>
            </div>
        </div>
    </div>
    <div class="ec-empty-content">
        <h3><?php echo get_phrase('no_expense_categories'); ?></h3>
        <p><?php echo get_phrase('create_your_first_category_to_organize_expenses'); ?></p>
        <button type="button" class="ec-create-btn" 
                onclick="rightModal('<?php echo site_url('modal/popup/expense_category/create'); ?>', '<?php echo get_phrase('add_expense_category'); ?>')">
            <i class="mdi mdi-plus"></i>
            <span><?php echo get_phrase('create_category'); ?></span>
        </button>
    </div>
</div>

<?php endif; ?>

<script>
(function() {
    'use strict';

    // Translations
    const translations = {
        showing: '<?php echo get_phrase('showing'); ?>',
        to: '<?php echo get_phrase('to'); ?>',
        of: '<?php echo get_phrase('of'); ?>',
        entries: '<?php echo get_phrase('entries'); ?>',
        categories: '<?php echo get_phrase('categories'); ?>',
        page: '<?php echo get_phrase('page'); ?>',
        go: '<?php echo get_phrase('go'); ?>',
        search_categories: '<?php echo get_phrase('search_categories'); ?>',
        all: '<?php echo get_phrase('all'); ?>',
        configured: '<?php echo get_phrase('configured'); ?>',
        reset_filters: '<?php echo get_phrase('reset_filters'); ?>',
        try_different_search_terms: '<?php echo get_phrase('try_different_search_terms'); ?>',
        no_results_found: '<?php echo get_phrase('no_results_found'); ?>',
        create_category: '<?php echo get_phrase('create_category'); ?>',
        first: '<?php echo get_phrase('first'); ?>',
        previous: '<?php echo get_phrase('previous'); ?>',
        next: '<?php echo get_phrase('next'); ?>',
        last: '<?php echo get_phrase('last'); ?>',
        not_defined: '<?php echo get_phrase('not_defined'); ?>',
        total_categories: '<?php echo get_phrase('total_categories'); ?>',
        with_cost_center: '<?php echo get_phrase('with_cost_center'); ?>'
    };

    // State Management
    const state = {
        currentPage: 1,
        perPage: 10,
        totalItems: 0,
        totalPages: 0,
        searchTerm: '',
        filter: 'all',
        sortBy: 'name',
        sortOrder: 'asc',
        items: [],
        filteredItems: []
    };
    
    // DOM Elements
    const elements = {
        listBody: document.getElementById('ec-list-body'),
        searchInput: document.getElementById('ec-search'),
        clearSearch: document.getElementById('ec-clear-search'),
        perPageSelect: document.getElementById('ec-per-page'),
        chips: document.querySelectorAll('.ec-chip'),
        sortCols: document.querySelectorAll('.ec-sortable-col'),
        navFirst: document.getElementById('ec-nav-first'),
        navPrev: document.getElementById('ec-nav-prev'),
        navNext: document.getElementById('ec-nav-next'),
        navLast: document.getElementById('ec-nav-last'),
        pageIndicators: document.getElementById('ec-page-indicators'),
        pageInput: document.getElementById('ec-page-input'),
        goBtn: document.getElementById('ec-go-btn'),
        showingStart: document.getElementById('ec-showing-start'),
        showingEnd: document.getElementById('ec-showing-end'),
        totalCount: document.getElementById('ec-total-count'),
        totalPages: document.getElementById('ec-total-pages'),
        progressFill: document.getElementById('ec-progress-fill'),
        noResults: document.getElementById('ec-no-results')
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
        const items = document.querySelectorAll('.ec-list-item');
        state.items = Array.from(items).map(item => ({
            element: item,
            id: item.dataset.id,
            name: item.dataset.name || '',
            cost: item.dataset.cost || '',
            department: item.dataset.department || '',
            hasCost: item.dataset.hasCost === '1',
            date: parseInt(item.dataset.date) || 0
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
        
        // Filter chips
        elements.chips.forEach(chip => {
            chip.addEventListener('click', handleFilterClick);
        });
        
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
    
    // Handle filter click
    function handleFilterClick(e) {
        const chip = e.currentTarget;
        elements.chips.forEach(c => c.classList.remove('active'));
        chip.classList.add('active');
        state.filter = chip.dataset.filter;
        state.currentPage = 1;
        filterAndSort();
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
            // Search filter
            const matchesSearch = !state.searchTerm ||
                item.name.includes(state.searchTerm) ||
                item.cost.includes(state.searchTerm) ||
                item.department.includes(state.searchTerm);

            // Status filter
            let matchesFilter = true;
            if (state.filter === 'with_cost') {
                matchesFilter = item.hasCost;
            }

            return matchesSearch && matchesFilter;
        });

        // Sort
        state.filteredItems.sort((a, b) => {
            let aVal, bVal;

            switch (state.sortBy) {
                case 'name':
                    aVal = a.name;
                    bVal = b.name;
                    break;
                case 'cost_center':
                    aVal = a.cost;
                    bVal = b.cost;
                    break;
                case 'department':
                    aVal = a.department;
                    bVal = b.department;
                    break;
                case 'date':
                    aVal = a.date;
                    bVal = b.date;
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
            item.element.classList.remove('ec-item-visible');
        });
        
        // Show current page items with animation
        let delay = 0;
        for (let i = startIndex; i < endIndex; i++) {
            const item = state.filteredItems[i];
            if (item) {
                item.element.style.display = '';
                item.element.style.setProperty('--delay', `${delay * 0.04}s`);
                setTimeout(() => {
                    item.element.classList.add('ec-item-visible');
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
                ellipsis.className = 'ec-page-ellipsis';
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
                ellipsis.className = 'ec-page-ellipsis';
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
        btn.className = `ec-page-btn ${isActive ? 'active' : ''}`;
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
        document.querySelectorAll('.ec-stat-value[data-count]').forEach(el => {
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
    window.resetFilters = function() {
        if (elements.searchInput) elements.searchInput.value = '';
        state.searchTerm = '';
        state.filter = 'all';
        state.currentPage = 1;

        elements.chips.forEach(c => c.classList.remove('active'));
        document.querySelector('.ec-chip[data-filter="all"]')?.classList.add('active');

        filterAndSort();
        update();
    };

    // View category details function
    window.viewCategoryDetails = function(categoryId) {
        console.log('Viewing details for category:', categoryId);
        // Implement modal or redirect to details page
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
   PREMIUM EXPENSE CATEGORY LIST - MODERN UI
   ============================================================================ */

:root {
    --ec-primary: #6366f1;
    --ec-primary-rgb: 99, 102, 241;
    --ec-success: #10b981;
    --ec-success-rgb: 16, 185, 129;
    --ec-warning: #f59e0b;
    --ec-warning-rgb: 245, 158, 11;
    --ec-danger: #ef4444;
    --ec-danger-rgb: 239, 68, 68;
    --ec-dark: #1e293b;
    --ec-gray: #64748b;
    --ec-light: #f8fafc;
    --ec-border: #e2e8f0;
    --ec-white: #ffffff;
    --ec-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    --ec-shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}

/* Dashboard Stats */
.ec-dashboard {
    margin-bottom: 2rem;
}

.ec-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.5rem;
}

.ec-stat-card {
    position: relative;
    background: var(--ec-white);
    border-radius: 20px;
    padding: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1.25rem;
    border: 1px solid var(--ec-border);
    overflow: hidden;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.ec-stat-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--ec-shadow-lg);
}

.ec-stat-glow {
    position: absolute;
    top: -50%;
    right: -50%;
    width: 100%;
    height: 200%;
    background: radial-gradient(circle, rgba(var(--ec-primary-rgb), 0.1) 0%, transparent 70%);
    pointer-events: none;
}

.ec-stat-total .ec-stat-glow { background: radial-gradient(circle, rgba(var(--ec-primary-rgb), 0.15) 0%, transparent 70%); }
.ec-stat-active .ec-stat-glow { background: radial-gradient(circle, rgba(var(--ec-success-rgb), 0.15) 0%, transparent 70%); }

.ec-stat-icon-wrap {
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

.ec-stat-total .ec-stat-icon-wrap { background: linear-gradient(135deg, #6366f1, #8b5cf6); color: white; }
.ec-stat-active .ec-stat-icon-wrap { background: linear-gradient(135deg, #10b981, #34d399); color: white; }

.ec-stat-pulse {
    position: absolute;
    inset: 0;
    border-radius: 16px;
    animation: pulse 2s ease-in-out infinite;
}

.ec-stat-total .ec-stat-pulse { background: rgba(var(--ec-primary-rgb), 0.3); }
.ec-stat-active .ec-stat-pulse { background: rgba(var(--ec-success-rgb), 0.3); }

@keyframes pulse {
    0%, 100% { transform: scale(1); opacity: 0; }
    50% { transform: scale(1.2); opacity: 1; }
}

.ec-stat-data {
    flex: 1;
}

.ec-stat-value {
    display: block;
    font-size: 2rem;
    font-weight: 800;
    color: var(--ec-dark);
    line-height: 1;
}

.ec-stat-title {
    display: block;
    font-size: 0.875rem;
    color: var(--ec-gray);
    margin-top: 0.5rem;
    font-weight: 500;
}

.ec-stat-trend {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.5rem 0.75rem;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
    background: rgba(var(--ec-success-rgb), 0.1);
    color: var(--ec-success);
}

.ec-stat-trend.warning {
    background: rgba(var(--ec-warning-rgb), 0.1);
    color: var(--ec-warning);
}

/* Toolbar */
.ec-toolbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1.5rem;
    margin-bottom: 1.5rem;
    padding: 1.25rem;
    background: var(--ec-white);
    border-radius: 16px;
    border: 1px solid var(--ec-border);
    flex-wrap: wrap;
}

.ec-toolbar-left {
    flex: 1;
    min-width: 280px;
    max-width: 400px;
}

.ec-search-box {
    position: relative;
    display: flex;
    align-items: center;
}

.ec-search-icon-wrapper {
    position: absolute;
    left: 1rem;
    color: var(--ec-gray);
    font-size: 1.25rem;
    z-index: 1;
}

.ec-search-field {
    width: 100%;
    padding: 0.875rem 1rem 0.875rem 3rem;
    border: 2px solid var(--ec-border);
    border-radius: 12px;
    font-size: 0.9375rem;
    background: var(--ec-light);
    color: var(--ec-dark);
    transition: all 0.2s;
}

.ec-search-field:focus {
    outline: none;
    border-color: var(--ec-primary);
    background: var(--ec-white);
    box-shadow: 0 0 0 4px rgba(var(--ec-primary-rgb), 0.1);
}

.ec-search-clear-btn {
    position: absolute;
    right: 4.5rem;
    background: none;
    border: none;
    color: var(--ec-gray);
    cursor: pointer;
    padding: 0.25rem;
    opacity: 0.5;
    transition: all 0.2s;
}

.ec-search-clear-btn:hover {
    opacity: 1;
    color: var(--ec-danger);
}

.ec-search-shortcut {
    position: absolute;
    right: 1rem;
    display: flex;
    gap: 0.25rem;
}

.ec-search-shortcut kbd {
    padding: 0.25rem 0.5rem;
    background: var(--ec-border);
    border-radius: 6px;
    font-size: 0.75rem;
    font-family: inherit;
    color: var(--ec-gray);
}

.ec-toolbar-right {
    display: flex;
    align-items: center;
    gap: 1.5rem;
    flex-wrap: wrap;
}

.ec-filter-chips {
    display: flex;
    gap: 0.5rem;
}

.ec-chip {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.625rem 1rem;
    border: 2px solid var(--ec-border);
    border-radius: 100px;
    background: var(--ec-white);
    color: var(--ec-gray);
    font-size: 0.875rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
}

.ec-chip:hover {
    border-color: var(--ec-primary);
    color: var(--ec-primary);
}

.ec-chip.active {
    background: linear-gradient(135deg, var(--ec-primary), #8b5cf6);
    border-color: var(--ec-primary);
    color: white;
}

.ec-chip-count {
    padding: 0.125rem 0.5rem;
    background: rgba(0, 0, 0, 0.1);
    border-radius: 100px;
    font-size: 0.75rem;
    font-weight: 600;
}

.ec-chip.active .ec-chip-count {
    background: rgba(255, 255, 255, 0.2);
}

.ec-per-page-select {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.ec-per-page-select label {
    font-size: 0.875rem;
    color: var(--ec-gray);
    font-weight: 500;
}

.ec-per-page-select select {
    padding: 0.5rem 2rem 0.5rem 0.75rem;
    border: 2px solid var(--ec-border);
    border-radius: 8px;
    font-size: 0.875rem;
    background: var(--ec-white);
    color: var(--ec-dark);
    cursor: pointer;
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%2364748b' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 0.75rem center;
}

/* List Container */
.ec-list-container {
    background: var(--ec-white);
    border-radius: 20px;
    border: 1px solid var(--ec-border);
    overflow: hidden;
}

.ec-list-header {
    display: grid;
    grid-template-columns: 2fr 1.5fr 1.25fr 1fr 120px;
    gap: 1rem;
    padding: 1rem 1.5rem;
    background: linear-gradient(135deg, var(--ec-dark) 0%, #334155 100%);
    color: white;
    font-size: 0.8125rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.ec-sortable-col {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
    transition: all 0.2s;
}

.ec-sortable-col:hover {
    color: rgba(255, 255, 255, 0.8);
}

.ec-sort-icon {
    opacity: 0.5;
    transition: all 0.2s;
}

.ec-sortable-col:hover .ec-sort-icon,
.ec-sortable-col.sort-asc .ec-sort-icon,
.ec-sortable-col.sort-desc .ec-sort-icon {
    opacity: 1;
}

.ec-sortable-col.sort-asc .ec-sort-icon i { transform: rotate(180deg); }

.ec-col-actions {
    text-align: center;
}

/* List Body */
.ec-list-body {
    max-height: 600px;
    overflow-y: auto;
}

.ec-list-item {
    display: grid;
    grid-template-columns: 2fr 1.5fr 1.25fr 1fr 120px;
    gap: 1rem;
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid var(--ec-border);
    position: relative;
    opacity: 0;
    transform: translateY(10px);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    transition-delay: var(--delay, 0s);
}

.ec-list-item.ec-item-visible {
    opacity: 1;
    transform: translateY(0);
}

.ec-list-item:hover {
    background: linear-gradient(135deg, rgba(var(--ec-primary-rgb), 0.03), rgba(var(--ec-primary-rgb), 0.06));
}

.ec-list-item:last-child {
    border-bottom: none;
}

.ec-item-indicator {
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 4px;
    background: transparent;
    transition: all 0.2s;
}

.ec-list-item:hover .ec-item-indicator {
    background: linear-gradient(180deg, var(--ec-primary), #8b5cf6);
}

/* Category Column */
.ec-col-category {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.ec-item-avatar {
    width: 48px;
    height: 48px;
    border-radius: 14px;
    background: var(--ec-primary);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 700;
    font-size: 1rem;
    flex-shrink: 0;
    box-shadow: 0 4px 12px rgba(var(--ec-primary-rgb), 0.3);
}

.ec-item-info {
    min-width: 0;
}

.ec-item-name {
    font-size: 1rem;
    font-weight: 600;
    color: var(--ec-dark);
    margin: 0 0 0.25rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.ec-item-id {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    font-size: 0.75rem;
    color: var(--ec-gray);
    background: var(--ec-light);
    padding: 0.25rem 0.5rem;
    border-radius: 6px;
}

/* Cost Center Column */
.ec-col-cost {
    display: flex;
    align-items: center;
}

.ec-cost-value {
    font-size: 0.875rem;
    font-weight: 600;
}

.ec-cost-configured {
    color: var(--ec-success);
}

.ec-cost-empty {
    color: var(--ec-gray);
    font-style: italic;
}

/* Department Column */
.ec-col-department {
    display: flex;
    align-items: center;
}

.ec-department-value {
    font-size: 0.875rem;
    font-weight: 600;
}

.ec-department-configured {
    color: var(--ec-primary);
}

.ec-department-empty {
    color: var(--ec-gray);
    font-style: italic;
}

/* Date Column */
.ec-col-date {
    display: flex;
    align-items: center;
}

.ec-date-display {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.ec-date-icon {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    background: var(--ec-light);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--ec-primary);
}

.ec-date-text {
    display: flex;
    flex-direction: column;
}

.ec-date-main {
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--ec-dark);
}

.ec-date-time {
    font-size: 0.75rem;
    color: var(--ec-gray);
}

/* Actions Column */
.ec-col-actions {
    display: flex;
    align-items: center;
    justify-content: center;
}

.ec-action-buttons {
    display: flex;
    gap: 0.5rem;
}

.ec-action-btn {
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

.ec-btn-edit {
    background: linear-gradient(135deg, rgba(var(--ec-primary-rgb), 0.1), rgba(var(--ec-primary-rgb), 0.05));
    color: var(--ec-primary);
}

.ec-btn-edit:hover {
    background: linear-gradient(135deg, var(--ec-primary), #8b5cf6);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(var(--ec-primary-rgb), 0.3);
}

.ec-btn-delete {
    background: linear-gradient(135deg, rgba(var(--ec-danger-rgb), 0.1), rgba(var(--ec-danger-rgb), 0.05));
    color: var(--ec-danger);
}

.ec-btn-delete:hover {
    background: linear-gradient(135deg, var(--ec-danger), #f87171);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(var(--ec-danger-rgb), 0.3);
}

/* No Results */
.ec-no-results {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 4rem 2rem;
    text-align: center;
}

.ec-no-results-icon {
    width: 80px;
    height: 80px;
    border-radius: 20px;
    background: var(--ec-light);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.5rem;
    color: var(--ec-gray);
    margin-bottom: 1.5rem;
}

.ec-no-results h4 {
    font-size: 1.25rem;
    color: var(--ec-dark);
    margin: 0 0 0.5rem;
}

.ec-no-results p {
    font-size: 0.9375rem;
    color: var(--ec-gray);
    margin: 0 0 1.5rem;
}

.ec-reset-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    background: var(--ec-primary);
    color: white;
    border: none;
    border-radius: 10px;
    font-size: 0.9375rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
}

.ec-reset-btn:hover {
    background: #4f46e5;
    transform: translateY(-2px);
}

/* Pagination Bar */
.ec-pagination-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.25rem 1.5rem;
    background: linear-gradient(135deg, var(--ec-light), #f1f5f9);
    border-top: 1px solid var(--ec-border);
    gap: 1.5rem;
    flex-wrap: wrap;
}

.ec-pagination-info {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.ec-info-text {
    font-size: 0.875rem;
    color: var(--ec-gray);
}

.ec-info-text strong {
    color: var(--ec-dark);
    font-weight: 600;
}

.ec-progress-bar {
    width: 120px;
    height: 4px;
    background: var(--ec-border);
    border-radius: 2px;
    overflow: hidden;
}

.ec-progress-fill {
    height: 100%;
    background: linear-gradient(90deg, var(--ec-primary), #8b5cf6);
    border-radius: 2px;
    transition: width 0.3s ease;
}

.ec-pagination-nav {
    display: flex;
    align-items: center;
    gap: 0.375rem;
}

.ec-nav-btn {
    width: 40px;
    height: 40px;
    border: 2px solid var(--ec-border);
    background: var(--ec-white);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    color: var(--ec-gray);
    font-size: 1.25rem;
    transition: all 0.2s;
}

.ec-nav-btn:hover:not(:disabled) {
    border-color: var(--ec-primary);
    color: var(--ec-primary);
    transform: translateY(-1px);
}

.ec-nav-btn:disabled {
    opacity: 0.4;
    cursor: not-allowed;
}

.ec-page-indicators {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    margin: 0 0.5rem;
}

.ec-page-btn {
    min-width: 40px;
    height: 40px;
    border: 2px solid var(--ec-border);
    background: var(--ec-white);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    color: var(--ec-gray);
    font-size: 0.875rem;
    font-weight: 600;
    transition: all 0.2s;
    padding: 0 0.5rem;
}

.ec-page-btn:hover {
    border-color: var(--ec-primary);
    color: var(--ec-primary);
}

.ec-page-btn.active {
    background: linear-gradient(135deg, var(--ec-primary), #8b5cf6);
    border-color: var(--ec-primary);
    color: white;
}

.ec-page-ellipsis {
    padding: 0 0.5rem;
    color: var(--ec-gray);
}

.ec-pagination-jump {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.875rem;
    color: var(--ec-gray);
}

.ec-page-field {
    width: 60px;
    padding: 0.5rem;
    border: 2px solid var(--ec-border);
    border-radius: 8px;
    text-align: center;
    font-size: 0.875rem;
    background: var(--ec-white);
    transition: all 0.2s;
}

.ec-page-field:focus {
    outline: none;
    border-color: var(--ec-primary);
}

.ec-go-btn {
    padding: 0.5rem 1rem;
    background: var(--ec-primary);
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 0.8125rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    text-transform: uppercase;
}

.ec-go-btn:hover {
    background: #4f46e5;
}

/* Empty State */
.ec-empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 4rem 2rem;
    text-align: center;
    background: var(--ec-white);
    border-radius: 20px;
    border: 1px solid var(--ec-border);
}

.ec-empty-visual {
    margin-bottom: 2rem;
}

.ec-empty-icon-container {
    position: relative;
    width: 120px;
    height: 120px;
}

.ec-empty-circle {
    position: absolute;
    border-radius: 50%;
    border: 2px dashed var(--ec-border);
}

.ec-circle-1 { inset: 0; animation: rotate 20s linear infinite; }
.ec-circle-2 { inset: 15px; animation: rotate 15s linear infinite reverse; }
.ec-circle-3 { inset: 30px; animation: rotate 10s linear infinite; }

@keyframes rotate {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.ec-empty-icon {
    position: absolute;
    inset: 35px;
    background: linear-gradient(135deg, var(--ec-primary), #8b5cf6);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    color: white;
}

.ec-empty-content h3 {
    font-size: 1.5rem;
    color: var(--ec-dark);
    margin: 0 0 0.75rem;
}

.ec-empty-content p {
    font-size: 1rem;
    color: var(--ec-gray);
    margin: 0 0 2rem;
    max-width: 320px;
}

.ec-create-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 1rem 2rem;
    background: linear-gradient(135deg, var(--ec-primary), #8b5cf6);
    color: white;
    border: none;
    border-radius: 14px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
}

.ec-create-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(var(--ec-primary-rgb), 0.4);
}

/* Responsive */
@media (max-width: 1024px) {
    .ec-toolbar {
        flex-direction: column;
        align-items: stretch;
    }

    .ec-toolbar-left {
        max-width: none;
    }

    .ec-toolbar-right {
        justify-content: space-between;
    }

    .ec-list-header,
    .ec-list-item {
        grid-template-columns: 2fr 1.5fr 1.25fr 100px;
    }

    .ec-col-date {
        display: none;
    }

    .ec-pagination-bar {
        flex-direction: column;
        align-items: stretch;
        text-align: center;
    }

    .ec-pagination-info {
        align-items: center;
    }

    .ec-pagination-nav {
        justify-content: center;
    }

    .ec-pagination-jump {
        justify-content: center;
    }
}

@media (max-width: 768px) {
    .ec-stats-grid {
        grid-template-columns: 1fr;
    }

    .ec-filter-chips {
        flex-wrap: wrap;
        justify-content: center;
    }

    .ec-chip span:not(.ec-chip-count) {
        display: none;
    }

    .ec-list-header,
    .ec-list-item {
        grid-template-columns: 1fr 80px;
    }

    .ec-col-cost,
    .ec-col-department {
        display: none;
    }

    .ec-item-avatar {
        width: 40px;
        height: 40px;
        font-size: 0.875rem;
    }

    .ec-search-shortcut {
        display: none;
    }
}

@media (max-width: 480px) {
    .ec-nav-btn,
    .ec-page-btn {
        width: 36px;
        height: 36px;
        font-size: 0.875rem;
    }

    .ec-action-btn {
        width: 36px;
        height: 36px;
    }
}
</style>
