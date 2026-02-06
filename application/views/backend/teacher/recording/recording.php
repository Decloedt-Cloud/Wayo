<link rel="stylesheet" href="<?php echo base_url(); ?>assets/backend/css/responsive.min.css">

<style>
/* ============================================================================
   RECORDING MANAGER - MODERN DESIGN (MATCHING ADMIN STYLE)
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
    --exp-danger: #ef4444;
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
    padding: 1.5rem;
}

/* Filter Section */
.exp-filter-section {
    background: white;
    padding: 1.5rem;
    border-radius: 16px;
    border: 1px solid var(--exp-border);
    margin-bottom: 1.5rem;
    box-shadow: 0 2px 12px rgba(0,0,0,0.05);
}

.exp-filter-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1rem;
    align-items: end;
}

.exp-filter-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.exp-filter-label {
    font-size: 0.85rem;
    font-weight: 600;
    color: var(--exp-gray);
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.exp-filter-input {
    padding: 0.75rem 1rem;
    border: 1px solid var(--exp-border);
    border-radius: 10px;
    font-size: 0.9rem;
    color: var(--exp-dark);
    background: var(--exp-light);
    transition: all 0.2s;
    width: 100%;
}

.exp-filter-input:focus {
    outline: none;
    border-color: var(--exp-primary);
    background: white;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}

.exp-filter-input:hover {
    border-color: var(--exp-primary);
}

.exp-filter-btn {
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: 10px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    height: 45px; /* Match input height */
}

.exp-filter-btn-primary {
    background: var(--exp-dark);
    color: white;
}

.exp-filter-btn-primary:hover {
    background: #0f172a;
    transform: translateY(-2px);
    box-shadow: none;
}

.exp-filter-btn-outline {
    background: transparent;
    border: 1px solid var(--exp-border);
    color: var(--exp-gray);
}

.exp-filter-btn-outline:hover {
    background: var(--exp-light);
    color: var(--exp-dark);
}

/* List Styles */
.exp-list-container {
    border: 1px solid var(--exp-border);
    border-radius: 12px;
    overflow: hidden;
    background: white;
}

.exp-list-header {
    display: grid;
    grid-template-columns: 2fr 1.5fr 1.5fr 1fr;
    padding: 1rem 1.5rem;
    background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
    border-bottom: 1px solid var(--exp-border);
    font-size: 0.85rem;
    font-weight: 600;
    text-transform: uppercase;
    color: white;
    letter-spacing: 0.5px;
}

.exp-list-header div {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.exp-list-header i {
    opacity: 0.7;
}

.exp-list-item {
    display: grid;
    grid-template-columns: 2fr 1.5fr 1.5fr 1fr;
    padding: 1rem 1.5rem;
    border-bottom: 1px solid var(--exp-border);
    align-items: center;
    transition: background 0.2s;
    font-size: 0.9rem;
    color: var(--exp-dark);
}

.exp-list-item:hover {
    background: var(--exp-primary-light);
}

.exp-list-item:last-child {
    border-bottom: none;
}

.exp-action-btn {
    width: 32px; 
    height: 32px; 
    border-radius: 8px; 
    border: none; 
    cursor: pointer; 
    display: flex; 
    align-items: center; 
    justify-content: center;
    transition: all 0.2s;
}

.exp-action-btn:hover {
    transform: translateY(-2px);
}

/* Pagination */
.exp-pagination-bar {
    display: flex; 
    justify-content: space-between; 
    align-items: center; 
    padding: 1rem 0; 
    flex-wrap: wrap; 
    gap: 1rem; 
    margin-top: 1rem;
}

.exp-pagination-nav button {
    width: 36px; 
    height: 36px; 
    border: 1px solid var(--exp-border); 
    background: white; 
    border-radius: 8px; 
    display: flex; 
    align-items: center; 
    justify-content: center; 
    cursor: pointer;
    transition: all 0.2s;
}

.exp-pagination-nav button:hover:not(:disabled) {
    border-color: var(--exp-primary);
    color: var(--exp-primary);
}

.exp-pagination-nav button:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.daterangepicker {
    z-index: 1050;
}
</style>

<!-- Title -->
<div class="row">
    <div class="col-12">
        <div class="exp-header">
            <div class="exp-header-left">
                <div class="exp-header-icon">
                    <i class="fas fa-save"></i>
                </div>
                <div class="exp-header-text">
                    <h4><?php echo get_phrase('Recordings'); ?></h4>
                    <p><?php echo get_phrase('manage_meeting_recordings'); ?></p>
                </div>
            </div>
            <div class="exp-header-actions">
                <!-- Info Popover -->
                <div class="alert-modern d-flex align-items-center" style="background: rgba(255,255,255,0.1); padding: 0.5rem 1rem; border-radius: 10px;">
                    <span style="color: white; font-size: 0.85rem; margin-right: 0.5rem;"><?php echo get_phrase('recording_info'); ?></span>
                    <div class="icon flex-shrink-0"
                         style="background: rgba(255,255,255,0.2); width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; border-radius: 50%; cursor: help;"
                         data-bs-toggle="popover"
                         data-bs-trigger="hover focus"
                         data-bs-content="<?php echo get_phrase("If you can't find your recording, we are currently preparing it, and it will be ready soon.") ?>"
                         data-bs-placement="top">
                        <i class="dripicons-information" style="color: white; font-size: 14px;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="row">
    <div class="col-12">
        <form id="filterForm">
            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
            <div class="exp-filter-section">
                <div class="exp-filter-grid">
                    <div class="exp-filter-group">
                        <label class="exp-filter-label"><i class="mdi mdi-text-search"></i> <?php echo get_phrase('Meeting Name'); ?></label>
                        <input type="text" class="exp-filter-input" id="meeting_name" name="meeting_name" placeholder="Ex: Réunion pédagogique" value="<?= htmlspecialchars($filters['meeting_name'] ?? '') ?>">
                    </div>
                    <div class="exp-filter-group">
                        <label class="exp-filter-label"><i class="mdi mdi-calendar-range"></i> <?php echo get_phrase('Date Range'); ?></label>
                        <input type="text" class="exp-filter-input" id="date_range" name="date_range" placeholder="Select date or range" value="<?= htmlspecialchars($filters['date_range'] ?? '') ?>">
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="exp-filter-btn exp-filter-btn-primary w-100">
                            <i class="mdi mdi-filter"></i> <?php echo get_phrase('Apply'); ?>
                        </button>
                        <button type="button" id="clearFilters" class="exp-filter-btn exp-filter-btn-outline w-100">
                            <i class="mdi mdi-refresh"></i> <?php echo get_phrase('Clear'); ?>
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- List -->
<div class="row">
    <div class="col-12">
        <div class="exp-content-card">
            <div class="exp-content-header">
                <span class="exp-content-title">
                    <i class="mdi mdi-history"></i>
                    <?php echo get_phrase("History") ?>
                </span>
                
                <!-- Per Page Selector -->
                <div class="exp-per-page-select" style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.85rem; color: var(--exp-gray);">
                    <label><?php echo get_phrase('show'); ?></label>
                    <select id="exp-per-page" style="padding: 0.4rem 2rem 0.4rem 0.8rem; border: 1px solid var(--exp-border); border-radius: 6px; cursor: pointer;">
                        <option value="5">5</option>
                        <option value="10" selected>10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                </div>
            </div>
            
            <div class="exp-content-body">
                <div class="exp-list-container">
                    <!-- List Header -->
                    <div class="exp-list-header">
                        <div class="exp-sortable-col" data-sort="name" style="cursor: pointer;">
                            <?php echo get_phrase('Name'); ?> <i class="mdi mdi-unfold-more-horizontal"></i>
                        </div>
                        <div class="exp-sortable-col" data-sort="date" style="cursor: pointer;">
                            <?php echo get_phrase('Creation Date'); ?> <i class="mdi mdi-unfold-more-horizontal"></i>
                        </div>
                        <div><?php echo get_phrase('Duration'); ?></div>
                        <div><?php echo get_phrase('Action'); ?></div>
                    </div>
                    
                    <!-- List Body -->
                    <div id="exp-list-body">
                        <?php foreach ($recordings as $recording): ?>
                            <div class="exp-list-item" 
                                 data-name="<?php echo strtolower($recording['name']); ?>"
                                 data-date="<?php echo strtotime($recording['created_at']); ?>">
                                
                                <!-- Name -->
                                <div style="font-weight: 600;">
                                    <?php echo htmlspecialchars($recording['name']); ?>
                                </div>
                                
                                <!-- Date -->
                                <div style="color: var(--exp-gray);">
                                    <?php echo date('d/m/Y H:i', strtotime($recording['created_at'])); ?>
                                </div>
                                
                                <!-- Duration -->
                                <div>
                                    <span style="padding: 0.25rem 0.75rem; border-radius: 20px; background: #f1f5f9; color: var(--exp-dark); font-size: 0.8rem; font-weight: 600;">
                                        <i class="mdi mdi-clock-outline"></i> <?php echo htmlspecialchars($recording['formatted_duration']); ?>
                                    </span>
                                </div>
                                
                                <!-- Actions -->
                                <div style="display: flex; gap: 0.5rem;">
                                    <a href="<?php echo htmlspecialchars($recording['recording_url']); ?>" target="_blank" 
                                       class="exp-action-btn" 
                                       title="<?php echo get_phrase('View'); ?>"
                                       style="background: linear-gradient(135deg, #eef2ff, #e0e7ff); color: var(--exp-primary);">
                                        <i class="mdi mdi-eye-outline"></i>
                                    </a>
                                    
                                    <button type="button" class="exp-action-btn delete-recording" 
                                            data-recording-id="<?php echo htmlspecialchars($recording['recording_id']); ?>"
                                            title="<?php echo get_phrase('Delete'); ?>"
                                            style="background: linear-gradient(135deg, #fef2f2, #fecaca); color: #dc2626;">
                                        <i class="mdi mdi-trash-can-outline"></i>
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        
                        <?php if (empty($recordings)): ?>
                            <div id="exp-no-results" style="padding: 3rem; text-align: center; color: var(--exp-gray);">
                                <i class="mdi mdi-history" style="font-size: 3rem; opacity: 0.5; margin-bottom: 1rem;"></i>
                                <p><?php echo get_phrase('No recordings found'); ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Pagination -->
                <div class="exp-pagination-bar">
                    <div class="exp-pagination-info" style="font-size: 0.85rem; color: var(--exp-gray);">
                        <span><?php echo get_phrase('showing'); ?></span>
                        <strong id="exp-showing-start">0</strong>
                        <span>-</span>
                        <strong id="exp-showing-end">0</strong>
                        <span><?php echo get_phrase('of'); ?></span>
                        <strong id="exp-total-count">0</strong>
                        <span><?php echo get_phrase('entries'); ?></span>
                    </div>
                    
                    <div class="exp-pagination-nav">
                        <button type="button" id="exp-prev-btn"><i class="mdi mdi-chevron-left"></i></button>
                        <span id="exp-page-display" style="display: flex; align-items: center; padding: 0 0.5rem; font-weight: 600;">1</span>
                        <button type="button" id="exp-next-btn"><i class="mdi mdi-chevron-right"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
<script>
(function() {
    'use strict';
    
    const state = {
        currentPage: 1,
        perPage: 10,
        items: [],
        sortBy: 'date',
        sortOrder: 'desc' // Newest first by default
    };
    
    const elements = {
        listBody: document.getElementById('exp-list-body'),
        perPageSelect: document.getElementById('exp-per-page'),
        sortCols: document.querySelectorAll('.exp-sortable-col'),
        prevBtn: document.getElementById('exp-prev-btn'),
        nextBtn: document.getElementById('exp-next-btn'),
        pageDisplay: document.getElementById('exp-page-display'),
        showingStart: document.getElementById('exp-showing-start'),
        showingEnd: document.getElementById('exp-showing-end'),
        totalCount: document.getElementById('exp-total-count'),
    };
    
    function init() {
        collectItems();
        bindEvents();
        sortItems(); // Initial sort
        update();
    }
    
    function collectItems() {
        if(!elements.listBody) return;
        const items = elements.listBody.querySelectorAll('.exp-list-item');
        state.items = Array.from(items).map(item => ({
            element: item,
            name: item.dataset.name,
            date: parseInt(item.dataset.date)
        }));
    }
    
    function bindEvents() {
        if(elements.perPageSelect) {
            elements.perPageSelect.addEventListener('change', (e) => {
                state.perPage = parseInt(e.target.value);
                state.currentPage = 1;
                update();
            });
        }
        
        elements.sortCols.forEach(col => {
            col.addEventListener('click', (e) => {
                const sort = e.currentTarget.dataset.sort;
                if(state.sortBy === sort) {
                    state.sortOrder = state.sortOrder === 'asc' ? 'desc' : 'asc';
                } else {
                    state.sortBy = sort;
                    state.sortOrder = 'asc';
                }
                sortItems();
                update();
            });
        });
        
        if(elements.prevBtn) {
            elements.prevBtn.addEventListener('click', () => {
                if(state.currentPage > 1) {
                    state.currentPage--;
                    update();
                }
            });
        }
        
        if(elements.nextBtn) {
            elements.nextBtn.addEventListener('click', () => {
                const maxPage = Math.ceil(state.items.length / state.perPage);
                if(state.currentPage < maxPage) {
                    state.currentPage++;
                    update();
                }
            });
        }
    }
    
    function sortItems() {
        state.items.sort((a, b) => {
            let valA = a[state.sortBy];
            let valB = b[state.sortBy];
            
            if(valA < valB) return state.sortOrder === 'asc' ? -1 : 1;
            if(valA > valB) return state.sortOrder === 'asc' ? 1 : -1;
            return 0;
        });
        
        // Re-append in correct order (but hidden initially)
        state.items.forEach(item => elements.listBody.appendChild(item.element));
    }
    
    function update() {
        const totalItems = state.items.length;
        const totalPages = Math.ceil(totalItems / state.perPage) || 1;
        
        if(state.currentPage > totalPages) state.currentPage = totalPages;
        if(state.currentPage < 1) state.currentPage = 1;
        
        const start = (state.currentPage - 1) * state.perPage;
        const end = Math.min(start + state.perPage, totalItems);
        
        // Show/Hide items
        state.items.forEach((item, index) => {
            if(index >= start && index < end) {
                item.element.style.display = 'grid';
            } else {
                item.element.style.display = 'none';
            }
        });
        
        // Update UI
        if(elements.pageDisplay) elements.pageDisplay.textContent = state.currentPage;
        if(elements.showingStart) elements.showingStart.textContent = totalItems > 0 ? start + 1 : 0;
        if(elements.showingEnd) elements.showingEnd.textContent = end;
        if(elements.totalCount) elements.totalCount.textContent = totalItems;
        
        if(elements.prevBtn) elements.prevBtn.disabled = state.currentPage === 1;
        if(elements.nextBtn) elements.nextBtn.disabled = state.currentPage === totalPages;
        
        // Handle "No Results"
        const noResults = document.getElementById('exp-no-results');
        if(totalItems === 0) {
             if(!noResults) {
                 // Should ideally be there from PHP, but if not...
             } else {
                 noResults.style.display = 'block';
             }
        } else {
             if(noResults) noResults.style.display = 'none';
        }
    }
    
    // Expose update function for AJAX calls
    window.updateRecordingList = function(recordings) {
        const listBody = document.getElementById('exp-list-body');
        listBody.innerHTML = '';
        
        if(recordings.length === 0) {
            listBody.innerHTML = `
                <div id="exp-no-results" style="padding: 3rem; text-align: center; color: var(--exp-gray);">
                    <i class="mdi mdi-history" style="font-size: 3rem; opacity: 0.5; margin-bottom: 1rem;"></i>
                    <p><?php echo get_phrase('No recordings found'); ?></p>
                </div>
            `;
        } else {
            recordings.forEach(rec => {
                const date = new Date(rec.created_at);
                const dateStr = date.toLocaleDateString('fr-FR') + ' ' + date.toLocaleTimeString('fr-FR', {hour: '2-digit', minute:'2-digit'});
                const timestamp = Math.floor(date.getTime() / 1000);
                
                const html = `
                    <div class="exp-list-item" 
                         data-name="${rec.name.toLowerCase()}"
                         data-date="${timestamp}"
                         style="display: none;">
                        
                        <div style="font-weight: 600;">${rec.name}</div>
                        <div style="color: var(--exp-gray);">${dateStr}</div>
                        <div>
                            <span style="padding: 0.25rem 0.75rem; border-radius: 20px; background: #f1f5f9; color: var(--exp-dark); font-size: 0.8rem; font-weight: 600;">
                                <i class="mdi mdi-clock-outline"></i> ${rec.formatted_duration}
                            </span>
                        </div>
                        <div style="display: flex; gap: 0.5rem;">
                            <a href="${rec.recording_url}" target="_blank" 
                               class="exp-action-btn" 
                               title="<?php echo get_phrase('View'); ?>"
                               style="background: linear-gradient(135deg, #eef2ff, #e0e7ff); color: var(--exp-primary);">
                                <i class="mdi mdi-eye-outline"></i>
                            </a>
                            <button type="button" class="exp-action-btn delete-recording" 
                                    data-recording-id="${rec.recording_id}"
                                    title="<?php echo get_phrase('Delete'); ?>"
                                    style="background: linear-gradient(135deg, #fef2f2, #fecaca); color: #dc2626;">
                                <i class="mdi mdi-trash-can-outline"></i>
                            </button>
                        </div>
                    </div>
                `;
                listBody.insertAdjacentHTML('beforeend', html);
            });
        }
        
        // Re-init
        collectItems();
        sortItems();
        update();
    };
    
    // Run on load
    document.addEventListener('DOMContentLoaded', init);
    // Also run immediately in case DOM is already ready
    init();

})();

$(document).ready(function() {
    // Initialisation de daterangepicker
    $('#date_range').daterangepicker({
        locale: {
            format: 'DD-MM-YYYY',
            separator: ' - ',
            applyLabel: '<?php echo get_phrase('apply'); ?>',
            cancelLabel: '<?php echo get_phrase('cancel'); ?>',
            fromLabel: '<?php echo get_phrase('from'); ?>',
            toLabel: '<?php echo get_phrase('to'); ?>',
            customRangeLabel: '<?php echo get_phrase('custom'); ?>',
            weekLabel: 'W',
            daysOfWeek: [
                '<?php echo get_phrase('su'); ?>',
                '<?php echo get_phrase('mo'); ?>',
                '<?php echo get_phrase('tu'); ?>',
                '<?php echo get_phrase('we'); ?>',
                '<?php echo get_phrase('th'); ?>',
                '<?php echo get_phrase('fr'); ?>',
                '<?php echo get_phrase('sa'); ?>'
            ],
            monthNames: [
                '<?php echo get_phrase('january'); ?>',
                '<?php echo get_phrase('february'); ?>',
                '<?php echo get_phrase('march'); ?>',
                '<?php echo get_phrase('april'); ?>',
                '<?php echo get_phrase('may'); ?>',
                '<?php echo get_phrase('june'); ?>',
                '<?php echo get_phrase('july'); ?>',
                '<?php echo get_phrase('august'); ?>',
                '<?php echo get_phrase('september'); ?>',
                '<?php echo get_phrase('october'); ?>',
                '<?php echo get_phrase('november'); ?>',
                '<?php echo get_phrase('december'); ?>'
            ],
            firstDay: 1
        },
        autoUpdateInput: false,
        ranges: {
            "<?php echo get_phrase('today'); ?>": [moment(), moment()],
            '<?php echo get_phrase('yesterday'); ?>': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            '<?php echo get_phrase('last_7_days'); ?>': [moment().subtract(6, 'days'), moment()],
            '<?php echo get_phrase('this_month'); ?>': [moment().startOf('month'), moment().endOf('month')],
            '<?php echo get_phrase('last_month'); ?>': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
    }).on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('DD-MM-YYYY') + ' - ' + picker.endDate.format('DD-MM-YYYY'));
    }).on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
    });

    function showNotification(type, message, duration = 3000) {
        <?php if ($this->config->item('enable_toasts') == FALSE): ?>
            return;
        <?php endif; ?>
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: type,
            title: message,
            showConfirmButton: false,
            timer: duration,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });
    }

    // Apply filters
    $('#filterForm').on('submit', function(event) {
        event.preventDefault();
        var formData = $(this).serialize();
        
        $.ajax({
            url: "<?php echo site_url('teacher/recording'); ?>",
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    $('input[name="<?php echo $this->security->get_csrf_token_name(); ?>"]').val(response.csrf_token);
                    if(window.updateRecordingList) {
                        window.updateRecordingList(response.recordings);
                    }
                }
            },
            error: function(xhr, status, error) {
                console.error('Filter error:', xhr, status, error);
            }
        });
    });

    // Clear filters
    $('#clearFilters').on('click', function(event) {
        event.preventDefault();
        $('#meeting_name').val('');
        $('#date_range').val('');
        $('#date_range').daterangepicker('clear');
        var formData = {
            meeting_name: '',
            date_range: '',
            "<?php echo $this->security->get_csrf_token_name(); ?>": $('input[name="<?php echo $this->security->get_csrf_token_name(); ?>"]').val()
        };

        $.ajax({
            url: "<?php echo site_url('teacher/recording'); ?>",
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    $('input[name="<?php echo $this->security->get_csrf_token_name(); ?>"]').val(response.csrf_token);
                    if(window.updateRecordingList) {
                        window.updateRecordingList(response.recordings);
                    }
                }
            },
            error: function(xhr, status, error) {
                console.error('Clear filter error:', xhr, status, error);
            }
        });
    });

    // Handle deletion
    $(document).on('click', '.delete-recording', function(event) {
        event.preventDefault();
        var recordingId = $(this).data('recording-id');
        var btn = $(this);

        Swal.fire({
            title: '<?php echo addslashes(get_phrase("are_you_sure")); ?>',
            text: '<?php echo addslashes(get_phrase("you_will_not_be_able_to_revert_this")); ?>',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#6366f1',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<?php echo addslashes(get_phrase("yes_delete_it")); ?>',
            cancelButtonText: '<?php echo addslashes(get_phrase("cancel")); ?>',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "<?php echo site_url('teacher/delete_recording'); ?>",
                    type: 'POST',
                    data: {
                        recording_id: recordingId,
                        skip_sync: 1,
                        "<?php echo $this->security->get_csrf_token_name(); ?>": $('input[name="<?php echo $this->security->get_csrf_token_name(); ?>"]').val()
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            showNotification('success', "<?php echo get_phrase("Recording deleted successfully"); ?>");
                            
                            // Remove element and update list logic
                            const item = btn.closest('.exp-list-item');
                            item.remove();
                            
                            // Refresh filters to update counts
                            $('#filterForm').trigger('submit');
                            
                            if (response.csrf_token) {
                                $('input[name="<?php echo $this->security->get_csrf_token_name(); ?>"]').val(response.csrf_token);
                            }
                        } else {
                            showNotification('error', response.message || "<?php echo get_phrase("Failed to delete recording"); ?>");
                            if (response.csrf_token) {
                                $('input[name="<?php echo $this->security->get_csrf_token_name(); ?>"]').val(response.csrf_token);
                            }
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Delete error:', xhr, status, error);
                        showNotification('error', "<?php echo get_phrase("Failed to delete recording"); ?>");
                    }
                });
            }
        });
    });
});
</script>