<?php
$school_id = school_id();
$classes = db()->table('classes')->where('school_id', $school_id)->get()->getResultArray();
$currencies = db()->table('settings_school')->where('school_id', school_id())->get()->getRowArray()['system_currency'] ?? '';

$total_classes = count($classes);
$total_price = array_sum(array_column($classes, 'price'));
$average_price = $total_classes > 0 ? $total_price / $total_classes : 0;
$max_price = !empty($classes) ? max(array_column($classes, 'price')) : 0;
$min_price = !empty($classes) ? min(array_column($classes, 'price')) : 0;

if ($total_classes > 0): ?>
<!-- Premium Stats Dashboard -->
<div class="class-dashboard">
    <div class="class-stats-grid">
        <div class="class-stat-card class-stat-total">
            <div class="class-stat-glow"></div>
            <div class="class-stat-icon-wrap">
                <i class="mdi mdi-school"></i>
                <div class="class-stat-pulse"></div>
            </div>
            <div class="class-stat-data">
                <span class="class-stat-value" data-count="<?php echo $total_classes; ?>">0</span>
                <span class="class-stat-title"><?php echo get_phrase('total_classes'); ?></span>
            </div>
            <div class="class-stat-trend">
                <i class="mdi mdi-trending-up"></i>
                <span>100%</span>
            </div>
        </div>

        <div class="class-stat-card class-stat-price">
            <div class="class-stat-glow"></div>
            <div class="class-stat-icon-wrap">
                <i class="mdi mdi-cash-multiple"></i>
                <div class="class-stat-pulse"></div>
            </div>
            <div class="class-stat-data">
                <span class="class-stat-value-amount"><?php echo number_format($total_price, 2) . ' ' . $currencies; ?></span>
                <span class="class-stat-title"><?php echo get_phrase('total_value'); ?></span>
            </div>
            <div class="class-stat-trend warning">
                <i class="mdi mdi-chart-line"></i>
                <span><?php echo get_phrase('all_classes'); ?></span>
            </div>
        </div>

        <div class="class-stat-card class-stat-average">
            <div class="class-stat-glow"></div>
            <div class="class-stat-icon-wrap">
                <i class="mdi mdi-calculator"></i>
                <div class="class-stat-pulse"></div>
            </div>
            <div class="class-stat-data">
                <span class="class-stat-value-amount"><?php echo number_format($average_price, 2) . ' ' . $currencies; ?></span>
                <span class="class-stat-title"><?php echo get_phrase('average_price'); ?></span>
            </div>
            <div class="class-stat-trend">
                <i class="mdi mdi-gauge"></i>
                <span><?php echo get_phrase('per_class'); ?></span>
            </div>
        </div>
    </div>
</div>

<!-- Premium Search & Filter Bar -->
<div class="class-toolbar">
    <div class="class-toolbar-left">
        <div class="class-search-box">
            <div class="class-search-icon-wrapper">
                <i class="mdi mdi-magnify"></i>
            </div>
            <input type="text" id="class-search" class="class-search-field" placeholder="<?php echo get_phrase('search_classes'); ?>..." autocomplete="off">
            <button type="button" class="class-search-clear-btn" id="class-clear-search">
                <i class="mdi mdi-close-circle"></i>
            </button>
            <div class="class-search-shortcut">
                <kbd>⌘</kbd><kbd>K</kbd>
            </div>
        </div>
    </div>

    <div class="class-toolbar-right">
        <div class="class-view-options">
            <div class="class-per-page-select">
                <label><?php echo get_phrase('show'); ?></label>
                <select id="class-per-page">
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
<div class="class-list-container">
    <!-- List Header -->
    <div class="class-list-header">
        <div class="class-col-name class-sortable-col" data-sort="name">
            <span><?php echo get_phrase('class_name'); ?></span>
            <div class="class-sort-icon">
                <i class="mdi mdi-unfold-more-horizontal"></i>
            </div>
        </div>
        <div class="class-col-price class-sortable-col" data-sort="price">
            <span><?php echo get_phrase('price'); ?></span>
            <div class="class-sort-icon">
                <i class="mdi mdi-unfold-more-horizontal"></i>
            </div>
        </div>
        <div class="class-col-actions">
            <span><?php echo get_phrase('actions'); ?></span>
        </div>
    </div>

    <!-- List Body -->
    <div class="class-list-body" id="class-list-body">
        <?php foreach ($classes as $index => $class): ?>
        <div class="class-list-item"
             data-id="<?php echo $class['id']; ?>"
             data-name="<?php echo strtolower($class['name']); ?>"
             data-price="<?php echo $class['price']; ?>"
             style="--delay: <?php echo $index * 0.03; ?>s">

            <!-- Name Column -->
            <div class="class-col-name">
                <div class="class-item-avatar">
                    <span><?php echo strtoupper(substr($class['name'], 0, 2)); ?></span>
                </div>
                <div class="class-item-info">
                    <h4 class="class-item-name"><?php echo $class['name']; ?></h4>
                    <span class="class-item-id">
                        <i class="mdi mdi-identifier"></i>
                        CLASS-<?php echo str_pad($class['id'], 4, '0', STR_PAD_LEFT); ?>
                    </span>
                </div>
            </div>

            <!-- Price Column -->
            <div class="class-col-price">
                <div class="class-price-display">
                    <span class="class-price-value"><?php echo number_format($class['price'], 2) . ' ' . $currencies; ?></span>
                </div>
            </div>

            <!-- Actions Column -->
            <div class="class-col-actions">
                <div class="class-action-buttons">
                    <button type="button" class="class-action-btn class-btn-view"
                            title="<?php echo get_phrase('read'); ?>"
                            onclick="largeModal('<?php echo site_url('modal/popup/class/read/'.$class['id'])?>', '<?php echo $class['name']; ?>');">
                        <i class="mdi mdi-eye-outline"></i>
                    </button>
                    <button type="button" class="class-action-btn class-btn-edit"
                            title="<?php echo get_phrase('edit'); ?>"
                            onclick="rightModal('<?php echo site_url('modal/popup/class/edit/'.$class['id'])?>', '<?php echo get_phrase('update_class'); ?>');">
                        <i class="mdi mdi-pencil-outline"></i>
                    </button>
                    <button type="button" class="class-action-btn class-btn-delete"
                            title="<?php echo get_phrase('delete'); ?>"
                            onclick="confirmModal('<?php echo route('manage_class/delete/'.$class['id']); ?>', showAllClasses)">
                        <i class="mdi mdi-trash-can-outline"></i>
                    </button>
                </div>
            </div>

            <!-- Hover Indicator -->
            <div class="class-item-indicator"></div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- No Results State -->
    <div class="class-no-results" id="class-no-results" style="display: none;">
        <div class="class-no-results-icon">
            <i class="mdi mdi-school-outline"></i>
        </div>
        <h4><?php echo get_phrase('no_results_found'); ?></h4>
        <p><?php echo get_phrase('try_different_search_terms'); ?></p>
        <button type="button" class="class-reset-btn" onclick="resetClassFilters()">
            <i class="mdi mdi-refresh"></i>
            <?php echo get_phrase('reset_filters'); ?>
        </button>
    </div>
</div>

<!-- Premium Pagination -->
<div class="class-pagination-bar">
    <div class="class-pagination-info">
        <div class="class-info-text">
            <span><?php echo get_phrase('showing'); ?></span>
            <strong id="class-showing-start">1</strong>
            <span>-</span>
            <strong id="class-showing-end"><?php echo min(10, $total_classes); ?></strong>
            <span><?php echo get_phrase('of'); ?></span>
            <strong id="class-total-count"><?php echo $total_classes; ?></strong>
            <span><?php echo get_phrase('classes'); ?></span>
        </div>
        <div class="class-progress-bar">
            <div class="class-progress-fill" id="class-progress-fill" style="width: <?php echo min(100, ($total_classes > 0 ? (10 / $total_classes) * 100 : 100)); ?>%"></div>
        </div>
    </div>

    <div class="class-pagination-nav">
        <button type="button" class="class-nav-btn class-nav-first" id="class-nav-first" title="<?php echo get_phrase('first'); ?>">
            <i class="mdi mdi-page-first"></i>
        </button>
        <button type="button" class="class-nav-btn class-nav-prev" id="class-nav-prev" title="<?php echo get_phrase('previous'); ?>">
            <i class="mdi mdi-chevron-left"></i>
        </button>

        <div class="class-page-indicators" id="class-page-indicators">
            <!-- Page indicators will be generated by JS -->
        </div>

        <button type="button" class="class-nav-btn class-nav-next" id="class-nav-next" title="<?php echo get_phrase('next'); ?>">
            <i class="mdi mdi-chevron-right"></i>
        </button>
        <button type="button" class="class-nav-btn class-nav-last" id="class-nav-last" title="<?php echo get_phrase('last'); ?>">
            <i class="mdi mdi-page-last"></i>
        </button>
    </div>

    <div class="class-pagination-jump">
        <span><?php echo get_phrase('page'); ?></span>
        <input type="number" id="class-page-input" class="class-page-field" min="1" value="1">
        <span><?php echo get_phrase('of'); ?></span>
        <span id="class-total-pages">1</span>
        <button type="button" class="class-go-btn" id="class-go-btn"><?php echo get_phrase('go'); ?></button>
    </div>
</div>
<?php else: ?>

<!-- Premium Empty State -->
<div class="class-empty-state">
    <div class="class-empty-visual">
        <div class="class-empty-icon-container">
            <div class="class-empty-circle class-circle-1"></div>
            <div class="class-empty-circle class-circle-2"></div>
            <div class="class-empty-circle class-circle-3"></div>
            <div class="class-empty-icon">
                <i class="mdi mdi-school-plus"></i>
            </div>
        </div>
    </div>
    <div class="class-empty-content">
        <h3><?php echo get_phrase('no_classes_found'); ?></h3>
        <p><?php echo get_phrase('create_your_first_class_to_start_managing_students'); ?></p>
        <button type="button" class="class-create-btn"
                onclick="rightModal('<?php echo site_url('modal/popup/class/create'); ?>', '<?php echo htmlspecialchars(get_phrase('create_class'), ENT_QUOTES); ?>')">
            <i class="mdi mdi-plus"></i>
            <span><?php echo get_phrase('create_class'); ?></span>
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
        sortBy: 'name',
        sortOrder: 'asc',
        items: [],
        filteredItems: []
    };

    // DOM Elements
    const elements = {
        listBody: document.getElementById('class-list-body'),
        searchInput: document.getElementById('class-search'),
        clearSearch: document.getElementById('class-clear-search'),
        perPageSelect: document.getElementById('class-per-page'),
        sortCols: document.querySelectorAll('.class-sortable-col'),
        navFirst: document.getElementById('class-nav-first'),
        navPrev: document.getElementById('class-nav-prev'),
        navNext: document.getElementById('class-nav-next'),
        navLast: document.getElementById('class-nav-last'),
        pageIndicators: document.getElementById('class-page-indicators'),
        pageInput: document.getElementById('class-page-input'),
        goBtn: document.getElementById('class-go-btn'),
        showingStart: document.getElementById('class-showing-start'),
        showingEnd: document.getElementById('class-showing-end'),
        totalCount: document.getElementById('class-total-count'),
        totalPages: document.getElementById('class-total-pages'),
        progressFill: document.getElementById('class-progress-fill'),
        noResults: document.getElementById('class-no-results')
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
        const items = document.querySelectorAll('.class-list-item');
        state.items = Array.from(items).map(item => ({
            element: item,
            id: item.dataset.id,
            name: item.dataset.name,
            price: parseFloat(item.dataset.price) || 0
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
                item.name.includes(state.searchTerm);
            return matchesSearch;
        });

        // Sort
        state.filteredItems.sort((a, b) => {
            let aVal, bVal;

            switch (state.sortBy) {
                case 'name':
                    aVal = a.name;
                    bVal = b.name;
                    break;
                case 'price':
                    aVal = a.price;
                    bVal = b.price;
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
            item.element.classList.remove('class-item-visible');
        });

        // Show current page items with animation
        let delay = 0;
        for (let i = startIndex; i < endIndex; i++) {
            const item = state.filteredItems[i];
            if (item) {
                item.element.style.display = '';
                item.element.style.setProperty('--delay', `${delay * 0.04}s`);
                setTimeout(() => {
                    item.element.classList.add('class-item-visible');
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
                ellipsis.className = 'class-page-ellipsis';
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
                ellipsis.className = 'class-page-ellipsis';
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
        btn.className = `class-page-btn ${isActive ? 'active' : ''}`;
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
        document.querySelectorAll('.class-stat-value[data-count]').forEach(el => {
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
    window.resetClassFilters = function() {
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
   PREMIUM CLASS LIST - MODERN UI
   ============================================================================ */

:root {
    --class-primary: var(--bs-primary);
    --class-primary-rgb: var(--bs-primary-rgb);
    --class-success: var(--bs-success);
    --class-success-rgb: var(--bs-success-rgb);
    --class-warning: var(--bs-warning);
    --class-warning-rgb: var(--bs-warning-rgb);
    --class-danger: var(--bs-danger);
    --class-danger-rgb: var(--bs-danger-rgb);
    --class-dark: var(--bs-dark);
    --class-gray: var(--bs-gray);
    --class-light: var(--bs-light);
    --class-border: var(--bs-gray-200);
    --class-white: var(--bs-white);
    --class-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    --class-shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}

/* Dashboard Stats */
.class-dashboard {
    margin-bottom: 2rem;
    padding: 1.5rem;
}

.class-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.5rem;
}

.class-stat-card {
    position: relative;
    background: var(--class-white);
    border-radius: 20px;
    padding: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1.25rem;
    border: 1px solid var(--class-border);
    overflow: hidden;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.class-stat-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--class-shadow-lg);
}

.class-stat-glow {
    position: absolute;
    top: -50%;
    right: -50%;
    width: 100%;
    height: 200%;
    background: radial-gradient(circle, rgba(var(--class-primary-rgb), 0.1) 0%, transparent 70%);
    pointer-events: none;
}

.class-stat-total .class-stat-glow { background: radial-gradient(circle, rgba(var(--class-primary-rgb), 0.15) 0%, transparent 70%); }
.class-stat-price .class-stat-glow { background: radial-gradient(circle, rgba(var(--class-success-rgb), 0.15) 0%, transparent 70%); }
.class-stat-average .class-stat-glow { background: radial-gradient(circle, rgba(var(--class-warning-rgb), 0.15) 0%, transparent 70%); }

.class-stat-icon-wrap {
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

.class-stat-total .class-stat-icon-wrap { background: linear-gradient(135deg, #6366f1, #8b5cf6); color: white; }
.class-stat-price .class-stat-icon-wrap { background: linear-gradient(135deg, #10b981, #34d399); color: white; }
.class-stat-average .class-stat-icon-wrap { background: linear-gradient(135deg, #f59e0b, #fbbf24); color: white; }

.class-stat-pulse {
    position: absolute;
    inset: 0;
    border-radius: 16px;
    animation: pulse 2s ease-in-out infinite;
}

.class-stat-total .class-stat-pulse { background: rgba(var(--class-primary-rgb), 0.3); }
.class-stat-price .class-stat-pulse { background: rgba(var(--class-success-rgb), 0.3); }
.class-stat-average .class-stat-pulse { background: rgba(var(--class-warning-rgb), 0.3); }

@keyframes pulse {
    0%, 100% { transform: scale(1); opacity: 0; }
    50% { transform: scale(1.2); opacity: 1; }
}

.class-stat-data {
    flex: 1;
}

.class-stat-value {
    display: block;
    font-size: 2rem;
    font-weight: 800;
    color: var(--class-dark);
    line-height: 1;
}

.class-stat-value-amount {
    display: block;
    font-size: 1.5rem;
    font-weight: 800;
    color: var(--class-dark);
    line-height: 1;
}

.class-stat-title {
    display: block;
    font-size: 0.875rem;
    color: var(--class-gray);
    margin-top: 0.5rem;
    font-weight: 500;
}

.class-stat-trend {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.5rem 0.75rem;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
    background: rgba(var(--class-success-rgb), 0.1);
    color: var(--class-success);
}

.class-stat-trend.warning {
    background: rgba(var(--class-warning-rgb), 0.1);
    color: var(--class-warning);
}

/* Toolbar */
.class-toolbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1.5rem;
    margin-bottom: 1.5rem;
    padding: 1.25rem 1.5rem;
    background: var(--class-white);
    border-radius: 16px;
    border: 1px solid var(--class-border);
    flex-wrap: wrap;
}

.class-toolbar-left {
    flex: 1;
    min-width: 280px;
    max-width: 400px;
}

.class-search-box {
    position: relative;
    display: flex;
    align-items: center;
}

.class-search-icon-wrapper {
    position: absolute;
    left: 1rem;
    color: var(--class-gray);
    font-size: 1.25rem;
    z-index: 1;
}

.class-search-field {
    width: 100%;
    padding: 0.875rem 1rem 0.875rem 3rem;
    border: 2px solid var(--class-border);
    border-radius: 12px;
    font-size: 0.9375rem;
    background: var(--class-light);
    color: var(--class-dark);
    transition: all 0.2s;
}

.class-search-field:focus {
    outline: none;
    border-color: var(--class-primary);
    background: var(--class-white);
    box-shadow: 0 0 0 4px rgba(var(--class-primary-rgb), 0.1);
}

.class-search-clear-btn {
    position: absolute;
    right: 4.5rem;
    background: none;
    border: none;
    color: var(--class-gray);
    cursor: pointer;
    padding: 0.25rem;
    opacity: 0.5;
    transition: all 0.2s;
}

.class-search-clear-btn:hover {
    opacity: 1;
    color: var(--class-danger);
}

.class-search-shortcut {
    position: absolute;
    right: 1rem;
    display: flex;
    gap: 0.25rem;
}

.class-search-shortcut kbd {
    padding: 0.25rem 0.5rem;
    background: var(--class-border);
    border-radius: 6px;
    font-size: 0.75rem;
    font-family: inherit;
    color: var(--class-gray);
}

.class-toolbar-right {
    display: flex;
    align-items: center;
    gap: 1.5rem;
    flex-wrap: wrap;
}

.class-per-page-select {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.class-per-page-select label {
    font-size: 0.875rem;
    color: var(--class-gray);
    font-weight: 500;
}

.class-per-page-select select {
    padding: 0.5rem 2rem 0.5rem 0.75rem;
    border: 2px solid var(--class-border);
    border-radius: 8px;
    font-size: 0.875rem;
    background: var(--class-white);
    color: var(--class-dark);
    cursor: pointer;
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%2364748b' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 0.75rem center;
}

/* List Container */
.class-list-container {
    background: var(--class-white);
    border-radius: 20px;
    border: 1px solid var(--class-border);
    overflow: hidden;
}

.class-list-header {
    display: grid;
    grid-template-columns: 2fr 1fr 120px;
    gap: 1rem;
    padding: 1rem 1.5rem;
    background: linear-gradient(135deg, var(--class-dark) 0%, #334155 100%);
    color: white;
    font-size: 0.8125rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.class-sortable-col {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
    transition: all 0.2s;
}

.class-sortable-col:hover {
    color: rgba(255, 255, 255, 0.8);
}

.class-sort-icon {
    opacity: 0.5;
    transition: all 0.2s;
}

.class-sortable-col:hover .class-sort-icon,
.class-sortable-col.sort-asc .class-sort-icon,
.class-sortable-col.sort-desc .class-sort-icon {
    opacity: 1;
}

.class-sortable-col.sort-asc .class-sort-icon i { transform: rotate(180deg); }

.class-col-actions {
    text-align: center;
}

/* List Body */
.class-list-body {
    max-height: 600px;
    overflow-y: auto;
}

.class-list-item {
    display: grid;
    grid-template-columns: 2fr 1fr 120px;
    gap: 1rem;
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid var(--class-border);
    position: relative;
    opacity: 0;
    transform: translateY(10px);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    transition-delay: var(--delay, 0s);
}

.class-list-item.class-item-visible {
    opacity: 1;
    transform: translateY(0);
}

.class-list-item:hover {
    background: linear-gradient(135deg, rgba(var(--class-primary-rgb), 0.03), rgba(var(--class-primary-rgb), 0.06));
}

.class-list-item:last-child {
    border-bottom: none;
}

.class-item-indicator {
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 4px;
    background: transparent;
    transition: all 0.2s;
}

.class-list-item:hover .class-item-indicator {
    background: linear-gradient(180deg, var(--class-primary), #8b5cf6);
}

/* Name Column */
.class-col-name {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.class-item-avatar {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 700;
    font-size: 0.875rem;
    flex-shrink: 0;
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
}

.class-item-info {
    min-width: 0;
}

.class-item-name {
    font-size: 0.9375rem;
    font-weight: 600;
    color: var(--class-dark);
    margin: 0 0 0.25rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.class-item-id {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    font-size: 0.75rem;
    color: var(--class-gray);
    background: var(--class-light);
    padding: 0.25rem 0.5rem;
    border-radius: 6px;
}

/* Price Column */
.class-col-price {
    display: flex;
    align-items: center;
}

.class-price-display {
    display: flex;
    flex-direction: column;
}

.class-price-value {
    font-size: 1.125rem;
    font-weight: 700;
    color: var(--class-success);
    background: linear-gradient(135deg, rgba(var(--class-success-rgb), 0.1), rgba(var(--class-success-rgb), 0.05));
    padding: 0.5rem 1rem;
    border-radius: 10px;
}

/* Actions Column */
.class-col-actions {
    display: flex;
    align-items: center;
    justify-content: center;
}

.class-action-buttons {
    display: flex;
    gap: 0.5rem;
}

.class-action-btn {
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

.class-btn-view {
    background: linear-gradient(135deg, rgba(var(--class-primary-rgb), 0.1), rgba(var(--class-primary-rgb), 0.05));
    color: var(--class-primary);
}

.class-btn-view:hover {
    background: linear-gradient(135deg, var(--class-primary), #8b5cf6);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(var(--class-primary-rgb), 0.3);
}

.class-btn-edit {
    background: linear-gradient(135deg, rgba(var(--class-primary-rgb), 0.1), rgba(var(--class-primary-rgb), 0.05));
    color: var(--class-primary);
}

.class-btn-edit:hover {
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
}

.class-btn-delete {
    background: linear-gradient(135deg, rgba(var(--class-danger-rgb), 0.1), rgba(var(--class-danger-rgb), 0.05));
    color: var(--class-danger);
}

.class-btn-delete:hover {
    background: linear-gradient(135deg, var(--class-danger), #f87171);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(var(--class-danger-rgb), 0.3);
}

/* No Results */
.class-no-results {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 4rem 2rem;
    text-align: center;
}

.class-no-results-icon {
    width: 80px;
    height: 80px;
    border-radius: 20px;
    background: var(--class-light);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.5rem;
    color: var(--class-gray);
    margin-bottom: 1.5rem;
}

.class-no-results h4 {
    font-size: 1.25rem;
    color: var(--class-dark);
    margin: 0 0 0.5rem;
}

.class-no-results p {
    font-size: 0.9375rem;
    color: var(--class-gray);
    margin: 0 0 1.5rem;
}

.class-reset-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    background: var(--class-primary);
    color: white;
    border: none;
    border-radius: 10px;
    font-size: 0.9375rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
}

.class-reset-btn:hover {
    background: #4f46e5;
    transform: translateY(-2px);
}

/* Pagination Bar */
.class-pagination-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.25rem 1.5rem;
    background: linear-gradient(135deg, var(--class-light), #f1f5f9);
    border-top: 1px solid var(--class-border);
    gap: 1.5rem;
    flex-wrap: wrap;
}

.class-pagination-info {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.class-info-text {
    font-size: 0.875rem;
    color: var(--class-gray);
}

.class-info-text strong {
    color: var(--class-dark);
    font-weight: 600;
}

.class-progress-bar {
    width: 120px;
    height: 4px;
    background: var(--class-border);
    border-radius: 2px;
    overflow: hidden;
}

.class-progress-fill {
    height: 100%;
    background: linear-gradient(90deg, var(--class-primary), #8b5cf6);
    border-radius: 2px;
    transition: width 0.3s ease;
}

.class-pagination-nav {
    display: flex;
    align-items: center;
    gap: 0.375rem;
}

.class-nav-btn {
    width: 40px;
    height: 40px;
    border: 2px solid var(--class-border);
    background: var(--class-white);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    color: var(--class-gray);
    font-size: 1.25rem;
    transition: all 0.2s;
}

.class-nav-btn:hover:not(:disabled) {
    border-color: var(--class-primary);
    color: var(--class-primary);
    transform: translateY(-1px);
}

.class-nav-btn:disabled {
    opacity: 0.4;
    cursor: not-allowed;
}

.class-page-indicators {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    margin: 0 0.5rem;
}

.class-page-btn {
    min-width: 40px;
    height: 40px;
    border: 2px solid var(--class-border);
    background: var(--class-white);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    color: var(--class-gray);
    font-size: 0.875rem;
    font-weight: 600;
    transition: all 0.2s;
    padding: 0 0.5rem;
}

.class-page-btn:hover {
    border-color: var(--class-primary);
    color: var(--class-primary);
}

.class-page-btn.active {
    background: linear-gradient(135deg, var(--class-primary), #8b5cf6);
    border-color: var(--class-primary);
    color: white;
}

.class-page-ellipsis {
    padding: 0 0.5rem;
    color: var(--class-gray);
}

.class-pagination-jump {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.875rem;
    color: var(--class-gray);
}

.class-page-field {
    width: 60px;
    padding: 0.5rem;
    border: 2px solid var(--class-border);
    border-radius: 8px;
    text-align: center;
    font-size: 0.875rem;
    background: var(--class-white);
    transition: all 0.2s;
}

.class-page-field:focus {
    outline: none;
    border-color: var(--class-primary);
}

.class-go-btn {
    padding: 0.5rem 1rem;
    background: var(--class-primary);
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 0.8125rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    text-transform: uppercase;
}

.class-go-btn:hover {
    background: #4f46e5;
}

/* Empty State */
.class-empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 4rem 2rem;
    text-align: center;
    background: var(--class-white);
    border-radius: 20px;
    border: 1px solid var(--class-border);
}

.class-empty-visual {
    margin-bottom: 2rem;
}

.class-empty-icon-container {
    position: relative;
    width: 120px;
    height: 120px;
}

.class-empty-circle {
    position: absolute;
    border-radius: 50%;
    border: 2px dashed var(--class-border);
}

.class-circle-1 { inset: 0; animation: rotate 20s linear infinite; }
.class-circle-2 { inset: 15px; animation: rotate 15s linear infinite reverse; }
.class-circle-3 { inset: 30px; animation: rotate 10s linear infinite; }

@keyframes rotate {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.class-empty-icon {
    position: absolute;
    inset: 35px;
    background: linear-gradient(135deg, var(--class-primary), #8b5cf6);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    color: white;
}

.class-empty-content h3 {
    font-size: 1.5rem;
    color: var(--class-dark);
    margin: 0 0 0.75rem;
}

.class-empty-content p {
    font-size: 1rem;
    color: var(--class-gray);
    margin: 0 0 2rem;
    max-width: 320px;
}

.class-create-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 1rem 2rem;
    background: linear-gradient(135deg, var(--class-primary), #8b5cf6);
    color: white;
    border: none;
    border-radius: 14px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
}

.class-create-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(var(--class-primary-rgb), 0.4);
}

/* Responsive */
@media (max-width: 1024px) {
    .class-toolbar {
        flex-direction: column;
        align-items: stretch;
    }

    .class-toolbar-left {
        max-width: none;
    }

    .class-toolbar-right {
        justify-content: space-between;
    }

    .class-list-header,
    .class-list-item {
        grid-template-columns: 2fr 1fr 100px;
    }

    .class-pagination-bar {
        flex-direction: column;
        align-items: stretch;
        text-align: center;
    }

    .class-pagination-info {
        align-items: center;
    }

    .class-pagination-nav {
        justify-content: center;
    }

    .class-pagination-jump {
        justify-content: center;
    }
}

@media (max-width: 768px) {
    .class-stats-grid {
        grid-template-columns: 1fr;
    }

    .class-list-header,
    .class-list-item {
        grid-template-columns: 1fr 80px;
    }

    .class-col-price {
        display: none;
    }

    .class-search-shortcut {
        display: none;
    }
}

@media (max-width: 480px) {
    .class-nav-btn,
    .class-page-btn {
        width: 36px;
        height: 36px;
        font-size: 0.875rem;
    }

    .class-action-btn {
        width: 36px;
        height: 36px;
    }
}
</style>
