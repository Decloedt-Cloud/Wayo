<?php
$school_id = school_id();

// Récupérer l'ID de l'enseignant connecté
$user_id = $this->session->userdata('user_id');
$teacher = $this->db->get_where('teachers', ['user_id' => $user_id])->row_array();
$teacher_id = $teacher['id'] ?? null;

// Récupérer les classes autorisées pour cet enseignant (attendance = 1)
$permitted_class_ids = [];
if ($teacher_id) {
    $this->db->select('class_id');
    $this->db->from('teacher_permissions');
    $this->db->where('teacher_id', $teacher_id);
    $this->db->where('attendance', 1);
    $permitted_classes = $this->db->get()->result_array();
    $permitted_class_ids = array_column($permitted_classes, 'class_id');
}

$where = array('school_id' => $school_id, 'session' => active_session());
if (!empty($class_id) && $class_id != 'all') {
    $where['class_id'] = $class_id;
}

// Fetch enrols
$enrols = $this->db->select('DISTINCT(student_id), school_id, session')->get_where('enrols', $where)->result_array();
$student_count = count($enrols);

// Pre-process data for stats and list
$students_data = array();
$active_count = 0;

foreach($enrols as $enroll){
    $student = $this->db->get_where('students', array('id' => $enroll['student_id']))->row_array();
    if(!$student) continue;
    
    // Fetch user details directly for performance
    $user = $this->db->get_where('users', array('id' => $student['user_id']))->row_array();
    if(!$user) continue;
    
    // Exclure tous les teachers de la liste des étudiants
    $is_teacher = $this->db->get_where('teachers', array('user_id' => $student['user_id']))->row_array();
    if($is_teacher) continue;

    if($user['status'] == 1) $active_count++;
    
    $student['user_name'] = $user['name'];
    $student['user_image'] = $this->user_model->get_user_image($student['user_id']);
    $student['user_status'] = $user['status'];
    $student['user_id'] = $user['id']; // Important for actions
    
    $students_data[] = $student;
}
?>

<?php if ($student_count > 0): ?>

<!-- Premium Stats Dashboard -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="exp-stat-card" style="background: white; padding: 1.5rem; border-radius: 16px; border: 1px solid var(--exp-border); display: flex; align-items: center; gap: 1rem; box-shadow: 0 2px 12px rgba(0,0,0,0.05);">
            <div class="exp-stat-icon" style="width: 48px; height: 48px; border-radius: 12px; background: var(--exp-primary-light); color: var(--exp-primary); display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                <i class="mdi mdi-account-group"></i>
            </div>
            <div>
                <h3 style="margin: 0; font-size: 1.5rem; font-weight: 700; color: var(--exp-dark);"><?php echo $student_count; ?></h3>
                <p style="margin: 0; color: var(--exp-gray); font-size: 0.9rem;"><?php echo get_phrase('total_students'); ?></p>
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
                <p style="margin: 0; color: var(--exp-gray); font-size: 0.9rem;"><?php echo get_phrase('active_students'); ?></p>
            </div>
        </div>
    </div>
</div>

<!-- Premium Toolbar -->
<div class="exp-toolbar" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
    <div class="exp-toolbar-left" style="flex: 1; min-width: 250px; max-width: 600px; display: flex; gap: 0.5rem; align-items: center;">
        <div class="exp-class-filter" style="min-width: 180px;">
            <select id="class_id" onchange="filter_student()" style="width: 100%; padding: 0.75rem 1rem; border: 1px solid var(--exp-border); border-radius: 10px; outline: none; transition: all 0.2s; background: white; color: var(--exp-dark); cursor: pointer; height: 45px;">
                <option value="all"><?php echo get_phrase('all_classes'); ?></option>
                <?php
                if (!empty($permitted_class_ids)) {
                    $this->db->where_in('id', $permitted_class_ids);
                    $this->db->where('school_id', $school_id);
                    $classes = $this->db->get('classes')->result_array();
                } else {
                    $classes = [];
                }
                foreach ($classes as $class): ?>
                    <option value="<?php echo $class['id']; ?>" <?php if (isset($class_id) && $class_id == $class['id']) echo 'selected'; ?>>
                        <?php echo $class['name']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="exp-search-box" style="position: relative; flex: 1;">
            <i class="mdi mdi-magnify" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--exp-gray);"></i>
            <input type="text" id="exp-search" class="exp-search-field" placeholder="<?php echo get_phrase('search_students'); ?>..." 
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
                <span class="exp-chip-count"><?php echo $student_count; ?></span>
            </button>
            <button type="button" class="exp-chip" data-filter="active">
                <span><?php echo get_phrase('active'); ?></span>
                <span class="exp-chip-count"><?php echo $active_count; ?></span>
            </button>
            <button type="button" class="exp-chip" data-filter="inactive">
                <span><?php echo get_phrase('inactive'); ?></span>
                <span class="exp-chip-count"><?php echo $student_count - $active_count; ?></span>
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
        <div class="exp-sortable-col" data-sort="code" style="cursor: pointer;">
            <?php echo get_phrase('code'); ?> <i class="mdi mdi-unfold-more-horizontal"></i>
        </div>
        <div><?php echo get_phrase('photo'); ?></div>
        <div class="exp-sortable-col" data-sort="name" style="cursor: pointer;">
            <?php echo get_phrase('name'); ?> <i class="mdi mdi-unfold-more-horizontal"></i>
        </div>
        <div class="exp-sortable-col" data-sort="status" style="cursor: pointer;">
            <?php echo get_phrase('status'); ?> <i class="mdi mdi-unfold-more-horizontal"></i>
        </div>
        <div><?php echo get_phrase('actions'); ?></div>
    </div>
    
    <!-- List Body -->
    <div id="exp-list-body">
        <?php foreach($students_data as $index => $student): ?>
        <div class="exp-list-item" 
             data-code="<?php echo strtolower($student['code']); ?>"
             data-name="<?php echo strtolower($student['user_name']); ?>"
             data-status="<?php echo $student['user_status']; ?>">
            
            <!-- Code -->
            <div style="font-weight: 600; color: var(--exp-dark);"><?php echo $student['code']; ?></div>
            
            <!-- Photo -->
            <div>
                <img src="<?php echo $student['user_image']; ?>" class="rounded-circle" width="40" height="40" style="border: 2px solid var(--exp-border);">
            </div>
            
            <!-- Name -->
            <div style="font-weight: 600; color: var(--exp-dark); font-size: 0.95rem;"><?php echo $student['user_name']; ?></div>
            
            <!-- Status -->
            <div>
                <?php if($student['user_status'] == 1): ?>
                    <span style="padding: 0.25rem 0.75rem; border-radius: 20px; background: #dcfce7; color: #16a34a; font-size: 0.75rem; font-weight: 600; text-transform: uppercase;"><?php echo get_phrase('active'); ?></span>
                <?php else: ?>
                    <span style="padding: 0.25rem 0.75rem; border-radius: 20px; background: #f1f5f9; color: var(--exp-gray); font-size: 0.75rem; font-weight: 600; text-transform: uppercase;"><?php echo get_phrase('inactive'); ?></span>
                <?php endif; ?>
            </div>
            
            <!-- Actions -->
            <div style="display: flex; gap: 0.5rem;">
                <?php if(addon_status('id-card')):?>
                    <button type="button" class="exp-action-btn" title="<?php echo get_phrase('generate_id_card'); ?>" 
                            onclick="largeModal('<?php echo site_url('modal/popup/student/id_card/'.$student['id'])?>', '<?php echo $this->db->get_where('schools', array('id' => $school_id))->row('name'); ?>')"
                            style="width: 32px; height: 32px; border-radius: 8px; border: none; background: linear-gradient(135deg, #eef2ff, #e0e7ff); color: var(--exp-primary); cursor: pointer; display: flex; align-items: center; justify-content: center;">
                        <i class="mdi mdi-card-account-details-outline"></i>
                    </button>
                <?php endif;?>
                
                <button type="button" class="exp-action-btn" title="<?php echo get_phrase('profile'); ?>"
                        onclick="largeModal('<?php echo site_url('modal/popup/student/profile/'.$student['id'])?>', '<?php echo $this->db->get_where('schools', array('id' => $school_id))->row('name'); ?>')"
                        style="width: 32px; height: 32px; border-radius: 8px; border: none; background: linear-gradient(135deg, #eef2ff, #e0e7ff); color: var(--exp-primary); cursor: pointer; display: flex; align-items: center; justify-content: center;">
                    <i class="mdi mdi-account-circle-outline"></i>
                </button>
                
                <a href="<?php echo route('student/edit/'.$student['id']); ?>" class="exp-action-btn" title="<?php echo get_phrase('edit'); ?>"
                   style="width: 32px; height: 32px; border-radius: 8px; border: none; background: linear-gradient(135deg, #eef2ff, #e0e7ff); color: var(--exp-primary); cursor: pointer; display: flex; align-items: center; justify-content: center;">
                    <i class="mdi mdi-pencil-outline"></i>
                </a>

                <?php if($student['user_status'] == 1): ?>
                    <button type="button" class="exp-action-btn" title="<?php echo get_phrase('deactivate'); ?>"
                            onclick="confirmModal('<?php echo route('student/status/'.$student['id'].'/'.$student['user_id'].'/0'); ?>', showAllStudents)"
                            style="width: 32px; height: 32px; border-radius: 8px; border: none; background: linear-gradient(135deg, #fef2f2, #fecaca); color: #dc2626; cursor: pointer; display: flex; align-items: center; justify-content: center;">
                        <i class="mdi mdi-account-off-outline"></i>
                    </button>
                <?php else: ?>
                    <button type="button" class="exp-action-btn" title="<?php echo get_phrase('activate'); ?>"
                            onclick="confirmModal('<?php echo route('student/status/'.$student['id'].'/'.$student['user_id'].'/1'); ?>', showAllStudents)"
                            style="width: 32px; height: 32px; border-radius: 8px; border: none; background: linear-gradient(135deg, #dcfce7, #bbf7d0); color: #16a34a; cursor: pointer; display: flex; align-items: center; justify-content: center;">
                        <i class="mdi mdi-account-check-outline"></i>
                    </button>
                <?php endif; ?>
                
                <button type="button" class="exp-action-btn" title="<?php echo get_phrase('delete'); ?>"
                        onclick="confirmModal('<?php echo route('student/delete/'.$student['id'].'/'.$student['user_id']); ?>', showAllStudents)"
                        style="width: 32px; height: 32px; border-radius: 8px; border: none; background: linear-gradient(135deg, #fef2f2, #fecaca); color: #dc2626; cursor: pointer; display: flex; align-items: center; justify-content: center;">
                    <i class="mdi mdi-trash-can-outline"></i>
                </button>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    
    <!-- No Results State -->
    <div id="exp-no-results" style="display: none; text-align: center; padding: 4rem 2rem;">
        <i class="mdi mdi-account-search-outline" style="font-size: 3rem; color: #cbd5e1; margin-bottom: 1rem;"></i>
        <h4 style="font-size: 1.1rem; font-weight: 600; color: var(--exp-dark); margin-bottom: 0.5rem;"><?php echo get_phrase('no_students_found'); ?></h4>
        <p style="color: var(--exp-gray); margin-bottom: 1rem;"><?php echo get_phrase('try_changing_filters_or_search'); ?></p>
        <button type="button" onclick="resetAll()" style="padding: 0.5rem 1rem; border: 1px solid var(--exp-border); background: white; border-radius: 6px; cursor: pointer; color: var(--exp-primary);">
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
        <strong id="exp-showing-end"><?php echo min(10, $student_count); ?></strong>
        <span><?php echo get_phrase('of'); ?></span>
        <strong id="exp-total-count"><?php echo $student_count; ?></strong>
        <span><?php echo get_phrase('students'); ?></span>
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

<?php else: ?>
    <!-- Empty State -->
    <div class="exp-empty-state" style="text-align: center; padding: 4rem 2rem; background: white; border-radius: 16px; border: 1px solid var(--exp-border);">
        <div style="font-size: 4rem; color: #cbd5e1; margin-bottom: 1rem;"><i class="mdi mdi-account-off-outline"></i></div>
        <h3 style="font-size: 1.1rem; font-weight: 600; color: var(--exp-dark); margin-bottom: 0.5rem;"><?php echo get_phrase('no_students_found'); ?></h3>
        <p style="color: var(--exp-gray);"><?php echo get_phrase('add_students_to_get_started'); ?></p>
    </div>
<?php endif; ?>

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
        noResults: document.getElementById('exp-no-results')
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
            code: item.dataset.code,
            name: item.dataset.name,
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
    }
    
    function filterAndSort() {
        state.filteredItems = state.items.filter(item => {
            const matchesSearch = !state.searchTerm || 
                item.name.includes(state.searchTerm) || 
                item.code.includes(state.searchTerm);
                
            let matchesFilter = true;
            if(state.filter === 'active') matchesFilter = item.status === 1;
            else if(state.filter === 'inactive') matchesFilter = item.status !== 1;
            
            return matchesSearch && matchesFilter;
        });
        
        // Sort
        state.filteredItems.sort((a, b) => {
            let valA = a[state.sortBy];
            let valB = b[state.sortBy];
            
            if(valA < valB) return state.sortOrder === 'asc' ? -1 : 1;
            if(valA > valB) return state.sortOrder === 'asc' ? 1 : -1;
            return 0;
        });
        
        update();
    }
    
    function update() {
        const total = state.filteredItems.length;
        const maxPage = Math.ceil(total / state.perPage) || 1;
        
        if(state.currentPage > maxPage) state.currentPage = maxPage;
        
        const start = (state.currentPage - 1) * state.perPage;
        const end = start + state.perPage;
        const pageItems = state.filteredItems.slice(start, end);
        
        // Hide all items first
        state.items.forEach(item => item.element.style.display = 'none');
        
        // Show page items
        pageItems.forEach(item => item.element.style.display = 'grid'); // Grid as defined in inline style
        
        // Update UI
        if(elements.showingStart) elements.showingStart.textContent = total > 0 ? start + 1 : 0;
        if(elements.showingEnd) elements.showingEnd.textContent = Math.min(end, total);
        if(elements.totalCount) elements.totalCount.textContent = total;
        if(elements.pageDisplay) elements.pageDisplay.textContent = `Page ${state.currentPage} / ${maxPage}`;
        
        if(elements.prevBtn) elements.prevBtn.disabled = state.currentPage === 1;
        if(elements.nextBtn) elements.nextBtn.disabled = state.currentPage === maxPage;
        
        if(total === 0) {
            if(elements.noResults) elements.noResults.style.display = 'block';
            if(elements.listBody) elements.listBody.style.display = 'none';
        } else {
            if(elements.noResults) elements.noResults.style.display = 'none';
            if(elements.listBody) elements.listBody.style.display = 'block';
        }
    }
    
    window.resetAll = function() {
        elements.searchInput.value = '';
        state.searchTerm = '';
        elements.clearSearch.style.display = 'none';
        state.filter = 'all';
        elements.chips.forEach(c => c.classList.remove('active'));
        elements.chips[0].classList.add('active'); // Assume first is 'all'
        state.currentPage = 1;
        filterAndSort();
    }
    
    init();
})();
</script>