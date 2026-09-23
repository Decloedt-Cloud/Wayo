<?php
$school_id = school_id();
$teachers = db()->table('teachers')->where('school_id', $school_id)->get()->getResultArray();
$teacher_count = count($teachers);

// Calculate active teachers
$active_count = 0;
$teachers_data = array();

foreach($teachers as $teacher){
    $user = db()->table('users')->where('id', $teacher['user_id'])->get()->getRowArray();
    if(!$user) continue;
    
    if($user['status'] == 1) $active_count++;
    
    $teacher['user_name'] = $user['name'];
    $teacher['user_email'] = $user['email'];
    $teacher['user_image'] = $this->user_model->get_user_image($teacher['user_id']);
    $teacher['user_status'] = $user['status'];
    $teacher['user_phone'] = $user['phone'];
    
    $teachers_data[] = $teacher;
}
?>

<?php if ($teacher_count > 0): ?>

<!-- Premium Stats Dashboard -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="exp-stat-card" style="background: white; padding: 1.5rem; border-radius: 16px; border: 1px solid var(--exp-border); display: flex; align-items: center; gap: 1rem; box-shadow: 0 2px 12px rgba(0,0,0,0.05);">
            <div class="exp-stat-icon" style="width: 48px; height: 48px; border-radius: 12px; background: var(--exp-primary-light); color: var(--exp-primary); display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                <i class="mdi mdi-account-tie"></i>
            </div>
            <div>
                <h3 style="margin: 0; font-size: 1.5rem; font-weight: 700; color: var(--exp-dark);"><?php echo $teacher_count; ?></h3>
                <p style="margin: 0; color: var(--exp-gray); font-size: 0.9rem;"><?php echo get_phrase('total_teachers'); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="exp-stat-card" style="background: white; padding: 1.5rem; border-radius: 16px; border: 1px solid var(--exp-border); display: flex; align-items: center; gap: 1rem; box-shadow: 0 2px 12px rgba(0,0,0,0.05);">
            <div class="exp-stat-icon" style="width: 48px; height: 48px; border-radius: 12px; background: #dcfce7; color: #16a34a; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                <i class="mdi mdi-account-check"></i>
            </div>
            <div>
                <h3 style="margin: 0; font-size: 1.5rem; font-weight: 700; color: var(--exp-dark);"><?php echo $active_count; ?></h3>
                <p style="margin: 0; color: var(--exp-gray); font-size: 0.9rem;"><?php echo get_phrase('active_teachers'); ?></p>
            </div>
        </div>
    </div>
</div>

<!-- Premium Toolbar -->
<div class="exp-toolbar" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
    <div class="exp-toolbar-left" style="flex: 1; min-width: 250px; max-width: 600px; display: flex; gap: 0.5rem; align-items: center;">
        
        <div class="exp-search-box" style="position: relative; flex: 1;">
            <i class="mdi mdi-magnify" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--exp-gray);"></i>
            <input type="text" id="exp-search" class="exp-search-field" placeholder="<?php echo get_phrase('search_teachers'); ?>..." 
                   style="width: 100%; padding: 0.75rem 1rem 0.75rem 2.5rem; border: 1px solid var(--exp-border); border-radius: 10px; outline: none; transition: all 0.2s; height: 45px;">
            <button type="button" id="exp-clear-search" style="position: absolute; right: 0.75rem; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--exp-gray); cursor: pointer; display: none;">
                <i class="mdi mdi-close-circle"></i>
            </button>
        </div>
    </div>
    
    <div class="exp-toolbar-right" style="display: flex; gap: 1rem; align-items: center;">
        <div class="exp-filter-chips">
            <button type="button" class="exp-chip active" data-filter="all">
                <span><?php echo get_phrase('all'); ?></span>
                <span class="exp-chip-count"><?php echo $teacher_count; ?></span>
            </button>
            <button type="button" class="exp-chip" data-filter="active">
                <span><?php echo get_phrase('active'); ?></span>
                <span class="exp-chip-count"><?php echo $active_count; ?></span>
            </button>
            <button type="button" class="exp-chip" data-filter="inactive">
                <span><?php echo get_phrase('inactive'); ?></span>
                <span class="exp-chip-count"><?php echo $teacher_count - $active_count; ?></span>
            </button>
        </div>

        <div class="exp-per-page-select" style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.85rem; color: var(--exp-gray);">
            <label><?php echo get_phrase('show'); ?></label>
            <select id="exp-per-page" style="padding: 0.4rem 2rem 0.4rem 0.8rem; border: 1px solid var(--exp-border); border-radius: 6px; cursor: pointer;">
                <option value="10" selected>10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
        </div>
    </div>
</div>

<!-- Premium List Container -->
<div class="exp-list-container" style="border: 1px solid var(--exp-border); border-radius: 12px; overflow: hidden; background: white;">
    <!-- List Header -->
    <div class="exp-list-header">
        <div><?php echo get_phrase('photo'); ?></div>
        <div class="exp-sortable-col" data-sort="name" style="cursor: pointer;">
            <?php echo get_phrase('name'); ?> <i class="mdi mdi-unfold-more-horizontal"></i>
        </div>
        <div class="exp-sortable-col" data-sort="designation" style="cursor: pointer;">
            <?php echo get_phrase('designation'); ?> <i class="mdi mdi-unfold-more-horizontal"></i>
        </div>
        <div class="exp-sortable-col" data-sort="status" style="cursor: pointer;">
            <?php echo get_phrase('status'); ?> <i class="mdi mdi-unfold-more-horizontal"></i>
        </div>
        <div><?php echo get_phrase('actions'); ?></div>
    </div>
    
    <!-- List Body -->
    <div id="exp-list-body">
        <?php foreach($teachers_data as $teacher): ?>
        <div class="exp-list-item" 
             data-name="<?php echo strtolower($teacher['user_name']); ?>"
             data-designation="<?php echo strtolower($teacher['designation']); ?>"
             data-status="<?php echo $teacher['user_status']; ?>">
            
            <!-- Photo -->
            <div>
                <img src="<?php echo $teacher['user_image']; ?>" class="rounded-circle" width="40" height="40" style="border: 2px solid var(--exp-border);">
            </div>
            
            <!-- Name -->
            <div>
                <div style="font-weight: 600; color: var(--exp-dark); font-size: 0.95rem;"><?php echo $teacher['user_name']; ?></div>
                <div style="font-size: 0.8rem; color: var(--exp-gray);"><?php echo $teacher['user_email']; ?></div>
            </div>
            
            <!-- Designation -->
            <div style="color: var(--exp-gray); font-size: 0.9rem;">
                <?php echo $teacher['designation']; ?>
            </div>

            <!-- Status -->
            <div>
                <?php if($teacher['user_status'] == 1): ?>
                    <span style="padding: 0.25rem 0.75rem; border-radius: 20px; background: #dcfce7; color: #16a34a; font-size: 0.75rem; font-weight: 600; text-transform: uppercase;"><?php echo get_phrase('active'); ?></span>
                <?php else: ?>
                    <span style="padding: 0.25rem 0.75rem; border-radius: 20px; background: #f1f5f9; color: var(--exp-gray); font-size: 0.75rem; font-weight: 600; text-transform: uppercase;"><?php echo get_phrase('inactive'); ?></span>
                <?php endif; ?>
            </div>
            
            <!-- Actions -->
            <div style="display: flex; gap: 0.5rem;">
                <button type="button" class="exp-action-btn" title="<?php echo get_phrase('profile'); ?>"
                        onclick="largeModal('<?php echo site_url('modal/popup/teacher/profile/'.$teacher['id'])?>', '<?php echo db()->table('schools')->where('id', $school_id)->get()->getRow()->name ?? ''; ?>')"
                        style="width: 32px; height: 32px; border-radius: 8px; border: none; background: linear-gradient(135deg, #eef2ff, #e0e7ff); color: var(--exp-primary); cursor: pointer; display: flex; align-items: center; justify-content: center;">
                    <i class="mdi mdi-account-circle-outline"></i>
                </button>
                
                <button type="button" class="exp-action-btn" title="<?php echo get_phrase('permissions'); ?>"
                        onclick="rightModal('<?php echo site_url('modal/popup/teacher/permission_overview/' . $teacher['id'] . '/' . $teacher['user_id']); ?>', '<?php echo get_phrase('assigned_permissions'); ?>')"
                        style="width: 32px; height: 32px; border-radius: 8px; border: none; background: linear-gradient(135deg, #eef2ff, #e0e7ff); color: var(--exp-primary); cursor: pointer; display: flex; align-items: center; justify-content: center;">
                    <i class="mdi mdi-lock-open-outline"></i>
                </button>
                
                <button type="button" class="exp-action-btn" title="<?php echo get_phrase('edit'); ?>"
                        onclick="rightModal('<?php echo site_url('modal/popup/teacher/edit/' . $teacher['user_id']); ?>', '<?php echo get_phrase('update_teacher'); ?>')"
                        style="width: 32px; height: 32px; border-radius: 8px; border: none; background: linear-gradient(135deg, #eef2ff, #e0e7ff); color: var(--exp-primary); cursor: pointer; display: flex; align-items: center; justify-content: center;">
                    <i class="mdi mdi-pencil-outline"></i>
                </button>

                <?php if($teacher['user_status'] == 1): ?>
                    <button type="button" class="exp-action-btn" title="<?php echo get_phrase('deactivate'); ?>"
                            onclick="confirmModal('<?php echo route('teacher/status/'.$teacher['user_id'].'/0'); ?>', showAllTeachers)"
                            style="width: 32px; height: 32px; border-radius: 8px; border: none; background: linear-gradient(135deg, #fef2f2, #fecaca); color: #dc2626; cursor: pointer; display: flex; align-items: center; justify-content: center;">
                        <i class="mdi mdi-account-off-outline"></i>
                    </button>
                <?php else: ?>
                    <button type="button" class="exp-action-btn" title="<?php echo get_phrase('activate'); ?>"
                            onclick="confirmModal('<?php echo route('teacher/status/'.$teacher['user_id'].'/1'); ?>', showAllTeachers)"
                            style="width: 32px; height: 32px; border-radius: 8px; border: none; background: linear-gradient(135deg, #dcfce7, #bbf7d0); color: #16a34a; cursor: pointer; display: flex; align-items: center; justify-content: center;">
                        <i class="mdi mdi-account-check-outline"></i>
                    </button>
                <?php endif; ?>

                <button type="button" class="exp-action-btn" title="<?php echo get_phrase('delete'); ?>"
                        onclick="confirmModal('<?php echo route('teacher/delete/' . $teacher['user_id']); ?>', showAllTeachers)"
                        style="width: 32px; height: 32px; border-radius: 8px; border: none; background: linear-gradient(135deg, #fef2f2, #fecaca); color: #dc2626; cursor: pointer; display: flex; align-items: center; justify-content: center;">
                    <i class="mdi mdi-trash-can-outline"></i>
                </button>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    
    <!-- No Results Message -->
    <div id="exp-no-results" style="display: none; padding: 3rem; text-align: center; color: var(--exp-gray);">
        <i class="mdi mdi-account-search-outline" style="font-size: 3rem; opacity: 0.5; margin-bottom: 1rem;"></i>
        <p><?php echo get_phrase('no_teachers_found'); ?></p>
        <button type="button" id="exp-reset-filters" style="padding: 0.5rem 1rem; border: 1px solid var(--exp-border); background: white; border-radius: 6px; cursor: pointer; color: var(--exp-primary); margin-top: 0.5rem;">
            <i class="mdi mdi-refresh"></i> <?php echo get_phrase('reset_filters'); ?>
        </button>
    </div>
</div>

<!-- Pagination -->
<div class="exp-pagination-bar" style="display: flex; justify-content: space-between; align-items: center; padding: 1rem 0; flex-wrap: wrap; gap: 1rem; margin-top: 1rem;">
    <div class="exp-pagination-info" style="font-size: 0.85rem; color: var(--exp-gray);">
        <span><?php echo get_phrase('showing'); ?></span>
        <strong id="exp-showing-start">1</strong>
        <span>-</span>
        <strong id="exp-showing-end"><?php echo min(10, $teacher_count); ?></strong>
        <span><?php echo get_phrase('of'); ?></span>
        <strong id="exp-total-count"><?php echo $teacher_count; ?></strong>
        <span><?php echo get_phrase('teachers'); ?></span>
    </div>
    
    <div class="exp-pagination-nav" style="display: flex; gap: 0.5rem;">
        <button type="button" id="exp-prev-btn" style="width: 36px; height: 36px; border: 1px solid var(--exp-border); background: white; border-radius: 8px; display: flex; align-items: center; justify-content: center; cursor: pointer;">
            <i class="mdi mdi-chevron-left"></i>
        </button>
        <span id="exp-page-display" style="display: flex; align-items: center; padding: 0 0.5rem; font-weight: 600;">Page 1</span>
        <button type="button" id="exp-next-btn" style="width: 36px; height: 36px; border: 1px solid var(--exp-border); background: white; border-radius: 8px; display: flex; align-items: center; justify-content: center; cursor: pointer;">
            <i class="mdi mdi-chevron-right"></i>
        </button>
    </div>
</div>

<script>
(function() {
    'use strict';
    
    const state = {
        currentPage: 1,
        perPage: 10,
        items: [],
        filteredItems: [],
        filter: 'all',
        searchTerm: '',
        sortBy: 'name',
        sortOrder: 'asc'
    };
    
    const elements = {
        listBody: document.getElementById('exp-list-body'),
        searchInput: document.getElementById('exp-search'),
        clearSearch: document.getElementById('exp-clear-search'),
        perPageSelect: document.getElementById('exp-per-page'),
        chips: document.querySelectorAll('.exp-chip'),
        sortCols: document.querySelectorAll('.exp-sortable-col'),
        prevBtn: document.getElementById('exp-prev-btn'),
        nextBtn: document.getElementById('exp-next-btn'),
        pageDisplay: document.getElementById('exp-page-display'),
        showingStart: document.getElementById('exp-showing-start'),
        showingEnd: document.getElementById('exp-showing-end'),
        totalCount: document.getElementById('exp-total-count'),
        noResults: document.getElementById('exp-no-results'),
        resetFilters: document.getElementById('exp-reset-filters')
    };
    
    function init() {
        collectItems();
        bindEvents();
        update();
    }
    
    function collectItems() {
        if(!elements.listBody) return;
        const items = elements.listBody.querySelectorAll('.exp-list-item');
        state.items = Array.from(items).map(item => ({
            element: item,
            name: item.dataset.name,
            designation: item.dataset.designation,
            status: parseInt(item.dataset.status)
        }));
        state.filteredItems = [...state.items];
    }
    
    function bindEvents() {
        if(elements.searchInput) {
            elements.searchInput.addEventListener('input', (e) => {
                state.searchTerm = e.target.value.toLowerCase().trim();
                if(state.searchTerm) elements.clearSearch.style.display = 'block';
                else elements.clearSearch.style.display = 'none';
                state.currentPage = 1;
                filterAndSort();
            });
            
            elements.clearSearch.addEventListener('click', () => {
                elements.searchInput.value = '';
                state.searchTerm = '';
                elements.clearSearch.style.display = 'none';
                state.currentPage = 1;
                filterAndSort();
            });
        }
        
        if(elements.perPageSelect) {
            elements.perPageSelect.addEventListener('change', (e) => {
                state.perPage = parseInt(e.target.value);
                state.currentPage = 1;
                update();
            });
        }
        
        elements.chips.forEach(chip => {
            chip.addEventListener('click', (e) => {
                elements.chips.forEach(c => c.classList.remove('active'));
                e.currentTarget.classList.add('active');
                state.filter = e.currentTarget.dataset.filter;
                state.currentPage = 1;
                filterAndSort();
            });
        });
        
        elements.sortCols.forEach(col => {
            col.addEventListener('click', (e) => {
                const sort = e.currentTarget.dataset.sort;
                if(state.sortBy === sort) {
                    state.sortOrder = state.sortOrder === 'asc' ? 'desc' : 'asc';
                } else {
                    state.sortBy = sort;
                    state.sortOrder = 'asc';
                }
                filterAndSort();
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
                const maxPage = Math.ceil(state.filteredItems.length / state.perPage);
                if(state.currentPage < maxPage) {
                    state.currentPage++;
                    update();
                }
            });
        }
        
        if(elements.resetFilters) {
            elements.resetFilters.addEventListener('click', () => {
                elements.searchInput.value = '';
                state.searchTerm = '';
                elements.clearSearch.style.display = 'none';
                
                elements.chips.forEach(c => c.classList.remove('active'));
                document.querySelector('.exp-chip[data-filter="all"]').classList.add('active');
                state.filter = 'all';
                
                state.currentPage = 1;
                filterAndSort();
            });
        }
    }
    
    function filterAndSort() {
        state.filteredItems = state.items.filter(item => {
            const matchesSearch = !state.searchTerm || 
                                item.name.includes(state.searchTerm) || 
                                item.designation.includes(state.searchTerm);
            
            let matchesFilter = true;
            if(state.filter === 'active') matchesFilter = item.status === 1;
            if(state.filter === 'inactive') matchesFilter = item.status === 0;
            
            return matchesSearch && matchesFilter;
        });
        
        // Sort
        state.filteredItems.sort((a, b) => {
            let valA = a[state.sortBy];
            let valB = b[state.sortBy];
            
            if(state.sortBy === 'status') {
                valA = a.status;
                valB = b.status;
            }
            
            if(valA < valB) return state.sortOrder === 'asc' ? -1 : 1;
            if(valA > valB) return state.sortOrder === 'asc' ? 1 : -1;
            return 0;
        });
        
        update();
    }
    
    function update() {
        const totalItems = state.filteredItems.length;
        const totalPages = Math.ceil(totalItems / state.perPage) || 1;
        
        // Adjust current page if needed
        if(state.currentPage > totalPages) state.currentPage = totalPages;
        
        // Hide all
        state.items.forEach(item => item.element.style.display = 'none');
        
        // Show filtered and paginated
        const start = (state.currentPage - 1) * state.perPage;
        const end = Math.min(start + state.perPage, totalItems);
        
        for(let i = start; i < end; i++) {
            const item = state.filteredItems[i];
            // We append to make sure order is correct based on sort
            elements.listBody.appendChild(item.element); 
            item.element.style.display = 'grid'; // Grid display as per CSS
        }
        
        // Update UI info
        elements.showingStart.textContent = totalItems > 0 ? start + 1 : 0;
        elements.showingEnd.textContent = end;
        elements.totalCount.textContent = totalItems;
        elements.pageDisplay.textContent = `Page ${state.currentPage}`;
        
        // Update buttons
        elements.prevBtn.disabled = state.currentPage === 1;
        elements.nextBtn.disabled = state.currentPage === totalPages;
        elements.prevBtn.style.opacity = elements.prevBtn.disabled ? '0.5' : '1';
        elements.nextBtn.style.opacity = elements.nextBtn.disabled ? '0.5' : '1';
        
        // No results
        elements.noResults.style.display = totalItems === 0 ? 'block' : 'none';
        document.querySelector('.exp-pagination-bar').style.display = totalItems === 0 ? 'none' : 'flex';
    }
    
    // Initialize
    init();
})();
</script>

<?php else: ?>
    <?php include APPPATH . 'Views/backend/empty.php'; ?>
<?php endif; ?>
