<?php
$school_id = school_id();
$total_applications = $applications ? count($applications) : 0;
?>

<style>
:root {
    --adm-primary: #6366f1;
    --adm-primary-rgb: 99, 102, 241;
    --adm-success: #10b981;
    --adm-danger: #ef4444;
    --adm-dark: #1e293b;
    --adm-gray: #64748b;
    --adm-light: #f8fafc;
    --adm-border: #e2e8f0;
    --adm-white: #ffffff;
}

.adm-dashboard { margin-bottom: 1.5rem; padding: 1rem; }
.adm-stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem; }
.adm-stat-card { position: relative; background: var(--adm-white); border-radius: 16px; padding: 1.25rem; display: flex; align-items: center; gap: 1rem; border: 1px solid var(--adm-border); overflow: hidden; transition: all 0.3s; }
.adm-stat-card:hover { transform: translateY(-3px); box-shadow: 0 8px 20px rgba(0,0,0,0.1); }
.adm-stat-icon-wrap { width: 50px; height: 50px; border-radius: 12px; background: linear-gradient(135deg, var(--adm-primary), #8b5cf6); display: flex; align-items: center; justify-content: center; }
.adm-stat-icon-wrap i { font-size: 1.5rem; color: white; }
.adm-stat-data { flex: 1; }
.adm-stat-value { font-size: 1.75rem; font-weight: 700; color: var(--adm-dark); }
.adm-stat-title { font-size: 0.8rem; color: var(--adm-gray); }

.adm-toolbar { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; padding: 1rem; background: var(--adm-light); border-bottom: 1px solid var(--adm-border); }
.adm-search-box { position: relative; min-width: 250px; }
.adm-search-box i { position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); color: var(--adm-gray); }
.adm-search-field { width: 100%; padding: 0.625rem 0.75rem 0.625rem 2.5rem; border: 2px solid var(--adm-border); border-radius: 10px; font-size: 0.875rem; }
.adm-search-field:focus { outline: none; border-color: var(--adm-primary); }

.adm-list-header { display: grid; grid-template-columns: 70px 1.5fr 2fr 100px; gap: 0.75rem; padding: 0.75rem 1rem; background: var(--adm-light); border-bottom: 2px solid var(--adm-border); font-size: 0.75rem; font-weight: 700; text-transform: uppercase; color: var(--adm-gray); }
.adm-list-item { display: grid; grid-template-columns: 70px 1.5fr 2fr 100px; gap: 0.75rem; padding: 0.875rem 1rem; border-bottom: 1px solid var(--adm-border); transition: all 0.2s; }
.adm-list-item:hover { background: rgba(var(--adm-primary-rgb), 0.03); }

.adm-avatar { width: 45px; height: 45px; border-radius: 10px; overflow: hidden; border: 2px solid var(--adm-border); }
.adm-avatar img { width: 100%; height: 100%; object-fit: cover; }

.adm-name-display { display: flex; flex-direction: column; gap: 0.125rem; justify-content: center; }
.adm-name-main { margin: 0; font-size: 0.875rem; font-weight: 600; color: var(--adm-dark); }
.adm-name-id { font-size: 0.7rem; color: var(--adm-gray); }

.adm-email-display { display: flex; align-items: center; gap: 0.5rem; color: var(--adm-gray); font-size: 0.8rem; }
.adm-email-display i { color: var(--adm-primary); }

.adm-action-buttons { display: flex; gap: 0.375rem; justify-content: center; }
.adm-action-btn { width: 32px; height: 32px; border-radius: 8px; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s; }
.adm-btn-view { background: rgba(var(--adm-primary-rgb), 0.1); color: var(--adm-primary); }
.adm-btn-view:hover { background: var(--adm-primary); color: white; }
.adm-btn-approve { background: rgba(16, 185, 129, 0.1); color: var(--adm-success); }
.adm-btn-approve:hover { background: var(--adm-success); color: white; }
.adm-btn-delete { background: rgba(239, 68, 68, 0.1); color: var(--adm-danger); }
.adm-btn-delete:hover { background: var(--adm-danger); color: white; }

.adm-empty-state { display: flex; flex-direction: column; align-items: center; padding: 3rem 1.5rem; text-align: center; }
.adm-empty-icon { width: 80px; height: 80px; border-radius: 50%; background: linear-gradient(135deg, var(--adm-primary), #8b5cf6); display: flex; align-items: center; justify-content: center; margin-bottom: 1.25rem; }
.adm-empty-icon i { font-size: 2rem; color: white; }
.adm-empty-state h3 { margin: 0 0 0.5rem; font-size: 1.125rem; color: var(--adm-dark); }
.adm-empty-state p { margin: 0; color: var(--adm-gray); font-size: 0.875rem; }

/* Pagination Styles */
.adm-pagination { display: flex; justify-content: center; gap: 0.5rem; margin-top: 2rem; padding-bottom: 1rem; }
.adm-page-btn { min-width: 36px; height: 36px; border-radius: 8px; border: 1px solid var(--adm-border); background: white; color: var(--adm-dark); font-weight: 600; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s; }
.adm-page-btn:hover:not(:disabled) { border-color: var(--adm-primary); color: var(--adm-primary); }
.adm-page-btn.active { background: var(--adm-primary); color: white; border-color: var(--adm-primary); }
.adm-page-btn:disabled { opacity: 0.5; cursor: not-allowed; background: var(--adm-light); }
.adm-page-dots { display: flex; align-items: center; justify-content: center; color: var(--adm-gray); }

@media (max-width: 768px) {
    .adm-list-header { display: none; }
    .adm-list-item { grid-template-columns: 1fr; gap: 0.5rem; }
}
</style>

<?php if ($total_applications > 0): ?>

<div class="adm-dashboard">
    <div class="adm-stats-grid">
        <div class="adm-stat-card">
            <div class="adm-stat-icon-wrap"><i class="mdi mdi-account-clock"></i></div>
            <div class="adm-stat-data">
                <span class="adm-stat-value"><?php echo $total_applications; ?></span>
                <span class="adm-stat-title"><?php echo get_phrase('pending_applications'); ?></span>
            </div>
        </div>
    </div>
</div>

<div class="adm-toolbar">
    <div class="adm-search-box">
        <i class="mdi mdi-magnify"></i>
        <input type="text" id="adm-search" class="adm-search-field" placeholder="<?php echo get_phrase('search'); ?>..." onkeyup="filterAdmissions()">
    </div>
</div>

<div class="adm-list-header">
    <div><?php echo get_phrase('photo'); ?></div>
    <div><?php echo get_phrase('name'); ?></div>
    <div><?php echo get_phrase('email'); ?></div>
    <div><?php echo get_phrase('actions'); ?></div>
</div>

<div id="adm-list-body">
<?php foreach($applications as $application):
    $student = db()->table('students')->where('user_id', $application['id'])->where('school_id', $school_id)->get()->getRowArray();
?>
<div class="adm-list-item" data-name="<?php echo strtolower($application['name']); ?>" data-email="<?php echo strtolower($application['email']); ?>">
    <div class="adm-col-photo"><div class="adm-avatar"><img src="<?php echo $this->user_model->get_user_image($application['id']); ?>" alt=""></div></div>
    <div class="adm-col-name"><div class="adm-name-display"><h4 class="adm-name-main"><?php echo $application['name']; ?></h4><span class="adm-name-id"><i class="mdi mdi-identifier"></i> APP-<?php echo str_pad($application['id'], 4, '0', STR_PAD_LEFT); ?></span></div></div>
    <div class="adm-col-email"><div class="adm-email-display"><i class="mdi mdi-email-outline"></i><span title="<?php echo htmlspecialchars($application['email']); ?>"><?php echo strlen($application['email']) > 25 ? substr($application['email'], 0, 25) . '...' : $application['email']; ?></span></div></div>
    <div class="adm-col-actions">
        <div class="adm-action-buttons">
            <button type="button" class="adm-action-btn adm-btn-view" title="<?php echo get_phrase('profile'); ?>" onclick="largeModal('<?php echo site_url('modal/popup/student/profile/'.$student['id'])?>', '<?php echo db()->table('schools')->where('id', $school_id)->get()->getRow()->name ?? ''; ?>')"><i class="mdi mdi-eye-outline"></i></button>
            <button type="button" class="adm-action-btn adm-btn-approve" title="<?php echo get_phrase('approved'); ?>" onclick="rightModal('<?php echo site_url('modal/popup/online_admission/add/'.$student['id'].'/'.$school_id)?>','<?php echo get_phrase('approved'); ?>')"><i class="mdi mdi-check-circle-outline"></i></button>
            <button type="button" class="adm-action-btn adm-btn-delete" title="<?php echo get_phrase('delete'); ?>" onclick="confirmModalRedirect('<?php echo site_url('superadmin/online_admission/delete/'.$student['id']); ?>')"><i class="mdi mdi-trash-can-outline"></i></button>
        </div>
    </div>
</div>
<?php endforeach; ?>
</div>

<div id="adm-pagination" class="adm-pagination"></div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    initPagination();
});

let currentPage = 1;
const itemsPerPage = 10;

function initPagination() {
    renderPagination();
}

function filterAdmissions() {
    currentPage = 1; // Reset to first page on search
    renderPagination();
}

function renderPagination() {
    const searchInput = document.getElementById('adm-search');
    const search = searchInput ? searchInput.value.toLowerCase() : '';
    const allItems = document.querySelectorAll('.adm-list-item');
    const paginationContainer = document.getElementById('adm-pagination');
    
    let matchedItems = [];
    
    // 1. Filter items
    allItems.forEach(item => {
        const name = item.dataset.name || '';
        const email = item.dataset.email || '';
        
        if (name.includes(search) || email.includes(search)) {
            matchedItems.push(item);
        } else {
            item.style.display = 'none';
        }
    });
    
    // 2. Paginate matched items
    const totalItems = matchedItems.length;
    const totalPages = Math.ceil(totalItems / itemsPerPage);
    
    // Ensure currentPage is valid
    if (currentPage > totalPages) currentPage = totalPages || 1;
    if (currentPage < 1) currentPage = 1;
    
    const startIndex = (currentPage - 1) * itemsPerPage;
    const endIndex = startIndex + itemsPerPage;
    
    // Show/Hide items based on page
    matchedItems.forEach((item, index) => {
        if (index >= startIndex && index < endIndex) {
            item.style.display = ''; // Show
        } else {
            item.style.display = 'none'; // Hide
        }
    });
    
    // 3. Render Pagination Controls
    let paginationHTML = '';
    
    if (totalPages > 1) {
        // Previous Button
        paginationHTML += `<button class="adm-page-btn" onclick="changePage(${currentPage - 1})" ${currentPage === 1 ? 'disabled' : ''}><i class="mdi mdi-chevron-left"></i></button>`;
        
        // Page Numbers (Smart range)
        for (let i = 1; i <= totalPages; i++) {
            if (i === 1 || i === totalPages || (i >= currentPage - 1 && i <= currentPage + 1)) {
                paginationHTML += `<button class="adm-page-btn ${i === currentPage ? 'active' : ''}" onclick="changePage(${i})">${i}</button>`;
            } else if (i === currentPage - 2 || i === currentPage + 2) {
                paginationHTML += `<span class="adm-page-dots">...</span>`;
            }
        }
        
        // Next Button
        paginationHTML += `<button class="adm-page-btn" onclick="changePage(${currentPage + 1})" ${currentPage === totalPages ? 'disabled' : ''}><i class="mdi mdi-chevron-right"></i></button>`;
    }
    
    if (paginationContainer) {
        paginationContainer.innerHTML = paginationHTML;
    }
}

function changePage(page) {
    if (page < 1) return;
    currentPage = page;
    renderPagination();
    // Optional: Scroll to top of list
    // document.querySelector('.adm-list-header').scrollIntoView({ behavior: 'smooth' });
}
</script>

<?php else: ?>

<div class="adm-empty-state">
    <div class="adm-empty-icon"><i class="mdi mdi-account-plus"></i></div>
    <h3><?php echo get_phrase('no_applications_found'); ?></h3>
    <p><?php echo get_phrase('no_pending_admission_applications'); ?></p>
</div>

<?php endif; ?>
