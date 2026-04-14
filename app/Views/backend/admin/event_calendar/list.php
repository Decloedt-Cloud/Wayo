<?php
$school_id = school_id();
// $user_id = session()->get('user_id');
// $school_id = db()->table('users')->where('id', $user_id)->get()->getResultArray()->row('school_id');

$announcements = db()->table('announcement')->where('school_id', $school_id)->where('session', active_session())->getResultArray();
$total_events = count($announcements);
?>

<?php if ($total_events > 0): ?>

<!-- Stats Dashboard -->
<div class="exp-dashboard">
    <div class="exp-stats-grid">
        <div class="exp-stat-card exp-stat-total">
            <div class="exp-stat-glow"></div>
            <div class="exp-stat-icon-wrap">
                <i class="mdi mdi-calendar-check"></i>
                <div class="exp-stat-pulse"></div>
            </div>
            <div class="exp-stat-data">
                <span class="exp-stat-value" data-count="<?php echo $total_events; ?>">0</span>
                <span class="exp-stat-title"><?php echo get_phrase('total_events'); ?></span>
            </div>
            <div class="exp-stat-trend">
                <i class="mdi mdi-trending-up"></i>
                <span><?php echo get_phrase('active_session'); ?></span>
            </div>
        </div>
    </div>
</div>

<!-- Toolbar -->
<div class="exp-toolbar">
    <div class="exp-toolbar-left">
        <div class="exp-search-box">
            <div class="exp-search-icon-wrapper">
                <i class="mdi mdi-magnify"></i>
            </div>
            <input type="text" id="exp-search" class="exp-search-field" placeholder="<?php echo get_phrase('search_events'); ?>..." autocomplete="off">
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

<!-- List Container -->
<div class="exp-list-container">
    <!-- List Header -->
    <div class="exp-list-header">
        <div class="exp-col-title exp-sortable-col" data-sort="title">
            <span><?php echo get_phrase('event_title'); ?></span>
            <div class="exp-sort-icon">
                <i class="mdi mdi-unfold-more-horizontal"></i>
            </div>
        </div>
        <div class="exp-col-date exp-sortable-col" data-sort="start_date">
            <span><?php echo get_phrase('from'); ?></span>
            <div class="exp-sort-icon">
                <i class="mdi mdi-unfold-more-horizontal"></i>
            </div>
        </div>
        <div class="exp-col-date exp-sortable-col" data-sort="end_date">
            <span><?php echo get_phrase('to'); ?></span>
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
        <?php foreach ($announcements as $index => $announcement): ?>
        <div class="exp-list-item" 
             data-id="<?php echo $announcement['id']; ?>"
             data-title="<?php echo htmlspecialchars($announcement['title']); ?>"
             data-start-date="<?php echo strtotime($announcement['starting_date']); ?>"
             data-end-date="<?php echo strtotime($announcement['ending_date']); ?>"
             style="--delay: <?php echo $index * 0.03; ?>s">
            
            <!-- Title Column -->
            <div class="exp-col-title">
                <div class="exp-item-info">
                    <h4 class="exp-item-name" 
                        data-bs-toggle="tooltip" 
                        data-bs-placement="top" 
                        title="<?php echo htmlspecialchars($announcement['title']); ?>">
                        <?php echo strlen($announcement['title']) > 30 ? substr($announcement['title'], 0, 30) . '...' : $announcement['title']; ?>
                    </h4>
                    <button type="button" class="btn btn-sm mobile-description-btn d-md-none" 
                            data-description="<?php echo htmlspecialchars($announcement['title']); ?>" 
                            onclick="showDescriptionPopup(this)">
                        <i class="mdi mdi-eye-outline"></i>
                    </button>
                </div>
            </div>
            
            <!-- Start Date Column -->
            <div class="exp-col-date">
                <div class="exp-date-display">
                    <div class="exp-date-icon">
                        <i class="mdi mdi-calendar-start"></i>
                    </div>
                    <div class="exp-date-text">
                        <span class="exp-date-main"><?php echo date('d M Y', strtotime($announcement['starting_date'])); ?></span>
                        <span class="exp-date-day"><?php echo date('l', strtotime($announcement['starting_date'])); ?></span>
                    </div>
                </div>
            </div>

            <!-- End Date Column -->
            <div class="exp-col-date">
                <div class="exp-date-display">
                    <div class="exp-date-icon">
                        <i class="mdi mdi-calendar-end"></i>
                    </div>
                    <div class="exp-date-text">
                        <span class="exp-date-main"><?php echo date('d M Y', strtotime($announcement['ending_date'])); ?></span>
                        <span class="exp-date-day"><?php echo date('l', strtotime($announcement['ending_date'])); ?></span>
                    </div>
                </div>
            </div>
            
            <!-- Actions Column -->
            <div class="exp-col-actions">
                <div class="exp-action-buttons">
                    <button type="button" class="exp-action-btn exp-btn-edit" 
                            title="<?php echo get_phrase('edit'); ?>"
                            onclick="rightModal('<?php echo site_url('modal/popup/event_calendar/edit/' . $announcement['id']); ?>', '<?php echo get_phrase('update_event'); ?>')">
                        <i class="mdi mdi-pencil-outline"></i>
                    </button>
                    <button type="button" class="exp-action-btn exp-btn-delete" 
                            title="<?php echo get_phrase('delete'); ?>"
                            onclick="confirmModal('<?php echo route('event_calendar/delete/' . $announcement['id']); ?>', showAllEvents)">
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
        <button type="button" class="exp-reset-btn" onclick="resetEventFilters()">
            <i class="mdi mdi-refresh"></i>
            <?php echo get_phrase('reset_filters'); ?>
        </button>
    </div>
</div>

<!-- Pagination -->
<div class="exp-pagination-bar">
    <div class="exp-pagination-info">
        <div class="exp-info-text">
            <span><?php echo get_phrase('showing'); ?></span>
            <strong id="exp-showing-start">1</strong>
            <span>-</span>
            <strong id="exp-showing-end"><?php echo min(10, $total_events); ?></strong>
            <span><?php echo get_phrase('of'); ?></span>
            <strong id="exp-total-count"><?php echo $total_events; ?></strong>
            <span><?php echo get_phrase('events'); ?></span>
        </div>
        <div class="exp-progress-bar">
            <div class="exp-progress-fill" id="exp-progress-fill" style="width: <?php echo min(100, ($total_events > 0 ? (10 / $total_events) * 100 : 100)); ?>%"></div>
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

<!-- Popup for Description -->
<div id="description-popup-overlay" class="description-popup-overlay"></div>
<div id="description-popup" class="description-popup">
    <div class="description-popup-header">
        <h4><?php echo get_phrase('contenu_complet'); ?></h4>
        <button onclick="hideDescriptionPopup()" class="description-popup-close">&times;</button>
    </div>
    <div id="description-popup-content" class="description-popup-content">
        <!-- Content injected by JS -->
    </div>
</div>

<?php else: ?>

<!-- Empty State -->
<div class="exp-empty-state">
    <div class="exp-empty-visual">
        <div class="exp-empty-icon-container">
            <div class="exp-empty-circle exp-circle-1"></div>
            <div class="exp-empty-circle exp-circle-2"></div>
            <div class="exp-empty-circle exp-circle-3"></div>
            <div class="exp-empty-icon">
                <i class="mdi mdi-calendar-blank"></i>
            </div>
        </div>
    </div>
    <div class="exp-empty-content">
        <h3><?php echo get_phrase('no_events_found'); ?></h3>
        <p><?php echo get_phrase('create_your_first_event'); ?></p>
        <button type="button" class="exp-create-btn" 
                onclick="rightModal('<?php echo site_url('modal/popup/event_calendar/create'); ?>', '<?php echo get_phrase('add_new_event'); ?>')">
            <i class="mdi mdi-plus"></i>
            <span><?php echo get_phrase('create_event'); ?></span>
        </button>
    </div>
</div>

<?php endif; ?>

<script>
    // Popup Logic
    var popup = document.getElementById('description-popup');
    var overlay = document.getElementById('description-popup-overlay');
    var popupContent = document.getElementById('description-popup-content');

    window.showDescriptionPopup = function(button) {
        const descriptionText = button.dataset.description;
        if(popupContent) popupContent.innerText = descriptionText;
        if(popup) popup.classList.add('is-visible');
        if(overlay) overlay.classList.add('is-visible');
    }

    window.hideDescriptionPopup = function() {
        if(popup) popup.classList.remove('is-visible');
        if(overlay) overlay.classList.remove('is-visible');
    }

    if(overlay) overlay.addEventListener('click', hideDescriptionPopup);

    window.resetEventFilters = function() {
        const searchInput = document.getElementById('exp-search');
        if(searchInput) {
            searchInput.value = '';
            searchInput.dispatchEvent(new Event('input'));
        }
    };

    (function() {
        'use strict';

        // State Management
        const state = {
            currentPage: 1,
            perPage: 10,
            totalItems: 0,
            totalPages: 0,
            searchTerm: '',
            sortBy: 'start_date',
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
            if(!elements.listBody) return;
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
                title: (item.dataset.title || '').toLowerCase(),
                start_date: parseInt(item.dataset.startDate) || 0,
                end_date: parseInt(item.dataset.endDate) || 0
            }));
            state.filteredItems = [...state.items];
            state.totalItems = state.items.length;
        }
        
        // Bind events
        function bindEvents() {
            if (elements.searchInput) {
                elements.searchInput.addEventListener('input', debounce(handleSearch, 300));
                elements.clearSearch.addEventListener('click', clearSearch);
            }
            if (elements.perPageSelect) {
                elements.perPageSelect.addEventListener('change', handlePerPageChange);
            }
            elements.sortCols.forEach(col => {
                col.addEventListener('click', handleSortClick);
            });
            if (elements.navFirst) elements.navFirst.addEventListener('click', () => goToPage(1));
            if (elements.navPrev) elements.navPrev.addEventListener('click', () => goToPage(state.currentPage - 1));
            if (elements.navNext) elements.navNext.addEventListener('click', () => goToPage(state.currentPage + 1));
            if (elements.navLast) elements.navLast.addEventListener('click', () => goToPage(state.totalPages));
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
            state.filteredItems = state.items.filter(item => {
                return !state.searchTerm || item.title.includes(state.searchTerm);
            });

            state.filteredItems.sort((a, b) => {
                let aVal = a[state.sortBy];
                let bVal = b[state.sortBy];
                
                if (typeof aVal === 'string') {
                    return state.sortOrder === 'asc' ? aVal.localeCompare(bVal) : bVal.localeCompare(aVal);
                }
                return state.sortOrder === 'asc' ? aVal - bVal : bVal - aVal;
            });
        }
        
        // Update view
        function update() {
            state.totalItems = state.filteredItems.length;
            state.totalPages = Math.ceil(state.totalItems / state.perPage);
            state.currentPage = Math.min(Math.max(1, state.currentPage), state.totalPages || 1);
            
            // Update items visibility
            state.items.forEach(item => {
                item.element.style.display = 'none';
                item.element.classList.remove('exp-item-visible');
            });
            
            const start = (state.currentPage - 1) * state.perPage;
            const end = start + state.perPage;
            const pageItems = state.filteredItems.slice(start, end);
            
            pageItems.forEach((item, index) => {
                item.element.style.display = 'grid'; // Restore grid display
                // Small delay for animation
                setTimeout(() => item.element.classList.add('exp-item-visible'), index * 30);
            });
            
            // Update no results
            if (elements.noResults) {
                elements.noResults.style.display = state.totalItems === 0 ? 'flex' : 'none';
            }
            
            // Update pagination info
            if (elements.showingStart) elements.showingStart.textContent = state.totalItems > 0 ? start + 1 : 0;
            if (elements.showingEnd) elements.showingEnd.textContent = Math.min(end, state.totalItems);
            if (elements.totalCount) elements.totalCount.textContent = state.totalItems;
            if (elements.totalPages) elements.totalPages.textContent = state.totalPages;
            if (elements.progressFill) elements.progressFill.style.width = `${(state.totalItems > 0 ? (Math.min(end, state.totalItems) / state.totalItems) * 100 : 0)}%`;
            
            // Update pagination buttons
            if (elements.navFirst) elements.navFirst.disabled = state.currentPage === 1;
            if (elements.navPrev) elements.navPrev.disabled = state.currentPage === 1;
            if (elements.navNext) elements.navNext.disabled = state.currentPage === state.totalPages;
            if (elements.navLast) elements.navLast.disabled = state.currentPage === state.totalPages;
            
            // Generate page indicators
            renderPageIndicators();
        }
        
        function renderPageIndicators() {
            if (!elements.pageIndicators) return;
            elements.pageIndicators.innerHTML = '';
            
            // Simple pagination logic for brevity
            let pages = [];
            if (state.totalPages <= 7) {
                pages = Array.from({length: state.totalPages}, (_, i) => i + 1);
            } else {
                if (state.currentPage <= 4) {
                    pages = [1, 2, 3, 4, 5, '...', state.totalPages];
                } else if (state.currentPage >= state.totalPages - 3) {
                    pages = [1, '...', state.totalPages - 4, state.totalPages - 3, state.totalPages - 2, state.totalPages - 1, state.totalPages];
                } else {
                    pages = [1, '...', state.currentPage - 1, state.currentPage, state.currentPage + 1, '...', state.totalPages];
                }
            }
            
            pages.forEach(page => {
                if (page === '...') {
                    const span = document.createElement('span');
                    span.className = 'exp-page-ellipsis';
                    span.textContent = '...';
                    elements.pageIndicators.appendChild(span);
                } else {
                    const btn = document.createElement('button');
                    btn.className = `exp-page-btn ${page === state.currentPage ? 'active' : ''}`;
                    btn.textContent = page;
                    btn.addEventListener('click', () => goToPage(page));
                    elements.pageIndicators.appendChild(btn);
                }
            });
        }
        
        function goToPage(page) {
            state.currentPage = page;
            update();
        }
        
        function handlePageJump() {
            const page = parseInt(elements.pageInput.value);
            if (page >= 1 && page <= state.totalPages) {
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
        
        // Start
        init();
    })();
</script>

<style>
/* CSS copied from expense/list.php */
:root {
    --exp-primary: #6366f1;
    --exp-primary-rgb: 99, 102, 241;
    --exp-primary-light: #eef2ff;
    --exp-success: #059669;
    --exp-success-rgb: 5, 150, 105;
    --exp-dark: #1e293b;
    --exp-gray: #64748b;
    --exp-light: #f8fafc;
    --exp-border: #e2e8f0;
    --exp-warning: #f59e0b;
    --exp-warning-rgb: 245, 158, 11;
    --exp-danger: #ef4444;
    --exp-danger-rgb: 239, 68, 68;
    --exp-white: #ffffff;
    --exp-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    --exp-shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}

/* Dashboard Stats */
.exp-dashboard { margin-bottom: 2rem; padding: 1.5rem; }
.exp-stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem; }
.exp-stat-card { position: relative; background: var(--exp-white); border-radius: 20px; padding: 1.5rem; display: flex; align-items: center; gap: 1.25rem; border: 1px solid var(--exp-border); overflow: hidden; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
.exp-stat-card:hover { transform: translateY(-4px); box-shadow: var(--exp-shadow-lg); }
.exp-stat-glow { position: absolute; top: -50%; right: -50%; width: 100%; height: 200%; background: radial-gradient(circle, rgba(var(--exp-primary-rgb), 0.1) 0%, transparent 70%); opacity: 0; transition: opacity 0.3s; transform: rotate(45deg); }
.exp-stat-card:hover .exp-stat-glow { opacity: 1; }
.exp-stat-icon-wrap { width: 60px; height: 60px; border-radius: 16px; background: var(--exp-primary-light); color: var(--exp-primary); display: flex; align-items: center; justify-content: center; font-size: 1.75rem; position: relative; z-index: 1; }
.exp-stat-data { flex: 1; position: relative; z-index: 1; }
.exp-stat-value { display: block; font-size: 1.5rem; font-weight: 800; color: var(--exp-dark); line-height: 1; }
.exp-stat-title { display: block; font-size: 0.875rem; color: var(--exp-gray); margin-top: 0.5rem; font-weight: 500; }
.exp-stat-trend { display: flex; align-items: center; gap: 0.25rem; padding: 0.5rem 0.75rem; border-radius: 12px; font-size: 0.75rem; font-weight: 600; background: rgba(var(--exp-success-rgb), 0.1); color: var(--exp-success); }

/* Toolbar */
.exp-toolbar { display: flex; justify-content: space-between; align-items: center; gap: 1.5rem; margin-bottom: 1.5rem; padding: 1.25rem 1.5rem; background: var(--exp-white); border-radius: 16px; border: 1px solid var(--exp-border); flex-wrap: wrap; }
.exp-toolbar-left { flex: 1; min-width: 280px; max-width: 400px; }
.exp-search-box { position: relative; display: flex; align-items: center; }
.exp-search-icon-wrapper { position: absolute; left: 1rem; color: var(--exp-gray); font-size: 1.25rem; z-index: 1; }
.exp-search-field { width: 100%; padding: 0.875rem 1rem 0.875rem 3rem; border: 2px solid var(--exp-border); border-radius: 12px; font-size: 0.9375rem; background: var(--exp-light); color: var(--exp-dark); transition: all 0.2s; }
.exp-search-field:focus { outline: none; border-color: var(--exp-primary); background: var(--exp-white); box-shadow: 0 0 0 4px rgba(var(--exp-primary-rgb), 0.1); }
.exp-search-clear-btn { position: absolute; right: 4.5rem; background: none; border: none; color: var(--exp-gray); cursor: pointer; padding: 0.25rem; opacity: 0.5; transition: all 0.2s; }
.exp-search-clear-btn:hover { opacity: 1; color: var(--exp-danger); }
.exp-search-shortcut { position: absolute; right: 1rem; display: flex; gap: 0.25rem; }
.exp-search-shortcut kbd { padding: 0.25rem 0.5rem; background: var(--exp-border); border-radius: 6px; font-size: 0.75rem; font-family: inherit; color: var(--exp-gray); }

.exp-toolbar-right { display: flex; align-items: center; gap: 1.5rem; flex-wrap: wrap; }
.exp-per-page-select { display: flex; align-items: center; gap: 0.5rem; }
.exp-per-page-select select { padding: 0.5rem 2rem 0.5rem 0.75rem; border: 2px solid var(--exp-border); border-radius: 8px; font-size: 0.875rem; background: var(--exp-white); color: var(--exp-dark); cursor: pointer; }

/* List Container */
.exp-list-container { background: var(--exp-white); border-radius: 20px; border: 1px solid var(--exp-border); overflow: hidden; }
.exp-list-header { display: grid; grid-template-columns: 2fr 1fr 1fr 100px; gap: 1rem; padding: 1rem 1.5rem; background: linear-gradient(135deg, var(--exp-dark) 0%, #334155 100%); color: white; font-size: 0.8125rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
.exp-sortable-col { display: flex; align-items: center; gap: 0.5rem; cursor: pointer; transition: all 0.2s; }
.exp-sortable-col:hover { color: rgba(255, 255, 255, 0.8); }
.exp-sort-icon { opacity: 0.5; transition: all 0.2s; }
.exp-sortable-col.sort-asc .exp-sort-icon i { transform: rotate(180deg); }

/* List Body */
.exp-list-body { max-height: 600px; overflow-y: auto; }
.exp-list-item { display: grid; grid-template-columns: 2fr 1fr 1fr 100px; gap: 1rem; padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--exp-border); position: relative; opacity: 0; transform: translateY(10px); transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); transition-delay: var(--delay, 0s); }
.exp-list-item.exp-item-visible { opacity: 1; transform: translateY(0); }
.exp-list-item:hover { background: linear-gradient(135deg, rgba(var(--exp-primary-rgb), 0.03), rgba(var(--exp-primary-rgb), 0.06)); }
.exp-item-indicator { position: absolute; left: 0; top: 0; bottom: 0; width: 4px; background: transparent; transition: all 0.2s; }
.exp-list-item:hover .exp-item-indicator { background: linear-gradient(180deg, var(--exp-primary), #8b5cf6); }

/* Columns */
.exp-col-title { display: flex; align-items: center; }
.exp-item-name { font-size: 0.9375rem; font-weight: 600; color: var(--exp-dark); margin: 0; }
.exp-col-date { display: flex; align-items: center; }
.exp-date-display { display: flex; align-items: center; gap: 0.75rem; }
.exp-date-icon { width: 40px; height: 40px; border-radius: 10px; background: var(--exp-light); display: flex; align-items: center; justify-content: center; color: var(--exp-primary); font-size: 1.25rem; }
.exp-date-text { display: flex; flex-direction: column; }
.exp-date-main { font-size: 0.9375rem; font-weight: 600; color: var(--exp-dark); }
.exp-date-day { font-size: 0.75rem; color: var(--exp-gray); }
.exp-col-actions { display: flex; align-items: center; justify-content: center; }
.exp-action-buttons { display: flex; gap: 0.5rem; }
.exp-action-btn { width: 40px; height: 40px; border-radius: 10px; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 1.125rem; transition: all 0.2s; position: relative; overflow: hidden; }
.exp-btn-edit { background: linear-gradient(135deg, rgba(var(--exp-primary-rgb), 0.1), rgba(var(--exp-primary-rgb), 0.05)); color: var(--exp-primary); }
.exp-btn-edit:hover { background: var(--exp-primary); color: white; }
.exp-btn-delete { background: linear-gradient(135deg, rgba(var(--exp-danger-rgb), 0.1), rgba(var(--exp-danger-rgb), 0.05)); color: var(--exp-danger); }
.exp-btn-delete:hover { background: var(--exp-danger); color: white; }

/* No Results */
.exp-no-results { display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 4rem 2rem; text-align: center; }
.exp-no-results-icon { width: 80px; height: 80px; border-radius: 20px; background: var(--exp-light); display: flex; align-items: center; justify-content: center; font-size: 2.5rem; color: var(--exp-gray); margin-bottom: 1.5rem; }
.exp-reset-btn { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.75rem 1.5rem; background: var(--exp-primary); color: white; border: none; border-radius: 10px; font-size: 0.9375rem; font-weight: 600; cursor: pointer; }

/* Pagination */
.exp-pagination-bar { display: flex; justify-content: space-between; align-items: center; padding: 1.25rem 1.5rem; background: var(--exp-light); border-top: 1px solid var(--exp-border); gap: 1.5rem; flex-wrap: wrap; }
.exp-pagination-info { display: flex; flex-direction: column; gap: 0.5rem; }
.exp-info-text { font-size: 0.875rem; color: var(--exp-gray); }
.exp-info-text strong { color: var(--exp-dark); font-weight: 600; }
.exp-progress-bar { width: 120px; height: 4px; background: var(--exp-border); border-radius: 2px; overflow: hidden; }
.exp-progress-fill { height: 100%; background: var(--exp-primary); border-radius: 2px; transition: width 0.3s ease; }
.exp-pagination-nav { display: flex; align-items: center; gap: 0.375rem; }
.exp-nav-btn { width: 40px; height: 40px; border: 2px solid var(--exp-border); background: var(--exp-white); border-radius: 10px; display: flex; align-items: center; justify-content: center; cursor: pointer; color: var(--exp-gray); font-size: 1.25rem; transition: all 0.2s; }
.exp-nav-btn:hover:not(:disabled) { border-color: var(--exp-primary); color: var(--exp-primary); transform: translateY(-1px); }
.exp-nav-btn:disabled { opacity: 0.4; cursor: not-allowed; }
.exp-page-indicators { display: flex; align-items: center; gap: 0.25rem; margin: 0 0.5rem; }
.exp-page-btn { min-width: 40px; height: 40px; border: 2px solid var(--exp-border); background: var(--exp-white); border-radius: 10px; display: flex; align-items: center; justify-content: center; cursor: pointer; color: var(--exp-gray); font-size: 0.875rem; font-weight: 600; transition: all 0.2s; padding: 0 0.5rem; }
.exp-page-btn.active { background: var(--exp-primary); border-color: var(--exp-primary); color: white; }
.exp-pagination-jump { display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem; color: var(--exp-gray); }
.exp-page-field { width: 60px; padding: 0.5rem; border: 2px solid var(--exp-border); border-radius: 8px; text-align: center; font-size: 0.875rem; }
.exp-go-btn { padding: 0.5rem 1rem; background: var(--exp-primary); color: white; border: none; border-radius: 8px; font-size: 0.8125rem; font-weight: 600; cursor: pointer; text-transform: uppercase; }

/* Empty State */
.exp-empty-state { display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 4rem 2rem; text-align: center; background: var(--exp-white); border-radius: 20px; border: 1px solid var(--exp-border); }
.exp-empty-visual { margin-bottom: 2rem; }
.exp-empty-icon-container { position: relative; width: 120px; height: 120px; }
.exp-empty-circle { position: absolute; border-radius: 50%; border: 2px dashed var(--exp-border); }
.exp-circle-1 { inset: 0; animation: rotate 20s linear infinite; }
.exp-circle-2 { inset: 15px; animation: rotate 15s linear infinite reverse; }
.exp-circle-3 { inset: 30px; animation: rotate 10s linear infinite; }
.exp-empty-icon { position: absolute; inset: 35px; background: linear-gradient(135deg, var(--exp-primary), #8b5cf6); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2rem; color: white; }
.exp-empty-content h3 { font-size: 1.5rem; color: var(--exp-dark); margin: 0 0 0.75rem; }
.exp-empty-content p { font-size: 1rem; color: var(--exp-gray); margin: 0 0 2rem; max-width: 320px; }
.exp-create-btn { display: inline-flex; align-items: center; gap: 0.5rem; padding: 1rem 2rem; background: var(--exp-primary); color: white; border: none; border-radius: 14px; font-size: 1rem; font-weight: 600; cursor: pointer; transition: all 0.2s; }
@keyframes rotate { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }

/* Description Popup */
.description-popup { position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%) scale(0.9); background: white; width: 90%; max-width: 500px; padding: 0; border-radius: 20px; box-shadow: 0 20px 40px rgba(0,0,0,0.2); z-index: 10000; opacity: 0; visibility: hidden; transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1); }
.description-popup.is-visible { transform: translate(-50%, -50%) scale(1); opacity: 1; visibility: visible; }
.description-popup-header { display: flex; justify-content: space-between; align-items: center; padding: 1.5rem; border-bottom: 1px solid var(--exp-border); }
.description-popup-header h4 { margin: 0; font-size: 1.25rem; color: var(--exp-dark); }
.description-popup-close { background: none; border: none; font-size: 2rem; line-height: 1; color: var(--exp-gray); cursor: pointer; transition: color 0.2s; }
.description-popup-close:hover { color: var(--exp-danger); }
.description-popup-content { padding: 1.5rem; color: var(--exp-dark); font-size: 1rem; line-height: 1.6; max-height: 60vh; overflow-y: auto; }
.description-popup-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); z-index: 9999; opacity: 0; visibility: hidden; transition: all 0.3s; }
.description-popup-overlay.is-visible { opacity: 1; visibility: visible; }

/* Responsive */
@media (max-width: 768px) {
    .exp-list-header, .exp-list-item { grid-template-columns: 1fr 100px; }
    .exp-col-date { display: none; }
    .exp-toolbar { flex-direction: column; align-items: stretch; }
}
</style>
