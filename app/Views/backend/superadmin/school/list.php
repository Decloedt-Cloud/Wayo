<?php
$school_id = school_id();
$schools = db()->table('schools')->where('Etat', 1)->where('status', 1)->where('id !=', 1)->get()->getResultArray();

// Statistics
$stats = [
    'total' => count($schools)
];
?>

<style>
/* ============================================================================
   SCHOOL LIST - COMPACT MODERN STYLES
   ============================================================================ */
:root {
    --ai-primary: #6366f1;
    --ai-primary-light: #eef2ff;
    --ai-success: #059669;
    --ai-success-light: #d1fae5;
    --ai-danger: #dc2626;
    --ai-danger-light: #fee2e2;
    --ai-warning: #d97706;
    --ai-dark: #1e293b;
    --ai-gray: #64748b;
    --ai-light: #f8fafc;
    --ai-border: #e2e8f0;
}

/* Stats Grid */
.ai-stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 0.75rem;
    margin-bottom: 1rem;
}
@media (max-width: 992px) { .ai-stats-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 576px) { .ai-stats-grid { grid-template-columns: 1fr; } }

.ai-stat-card {
    background: white;
    border-radius: 10px;
    padding: 1rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.06);
    border: 1px solid var(--ai-border);
    position: relative;
    overflow: hidden;
}
.ai-stat-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 3px;
}
.ai-stat-card.purple::before { background: linear-gradient(90deg, #8b5cf6, #a78bfa); }

.ai-stat-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem; }
.ai-stat-icon {
    width: 36px; height: 36px;
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.1rem;
}
.ai-stat-card.purple .ai-stat-icon { background: #ede9fe; color: #7c3aed; }

.ai-stat-value { font-size: 1.4rem; font-weight: 700; color: var(--ai-dark); line-height: 1.2; }
.ai-stat-label { font-size: 0.7rem; color: var(--ai-gray); text-transform: uppercase; letter-spacing: 0.5px; }

/* Table Container */
.ai-table-container {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    overflow: hidden;
    border: 1px solid var(--ai-border);
}

/* Toolbar */
.ai-table-toolbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem 1rem;
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    border-bottom: 1px solid var(--ai-border);
    flex-wrap: wrap;
    gap: 0.75rem;
}

.ai-search-box {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    background: white;
    border: 1px solid var(--ai-border);
    border-radius: 8px;
    padding: 0.5rem 0.75rem;
    min-width: 250px;
    transition: all 0.2s;
}
.ai-search-box:focus-within {
    border-color: var(--ai-primary);
    box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
}
.ai-search-box i { color: var(--ai-gray); }
.ai-search-box input {
    border: none;
    outline: none;
    font-size: 0.85rem;
    width: 100%;
    background: transparent;
}

/* Results Info */
.ai-results-bar {
    padding: 0.5rem 1rem;
    background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
    border-bottom: 1px solid #bbf7d0;
    font-size: 0.8rem;
    color: #166534;
    display: none;
}
.ai-results-bar.active { display: flex; justify-content: space-between; align-items: center; }
.ai-results-bar.no-results {
    background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
    border-color: #fecaca;
    color: #991b1b;
}
.ai-results-bar button {
    background: none; border: none; color: inherit;
    cursor: pointer; font-size: 0.75rem; text-decoration: underline;
}

/* Table */
.ai-table { width: 100%; border-collapse: collapse; }
.ai-table thead { background: linear-gradient(135deg, #1e293b 0%, #334155 100%); }
.ai-table thead th {
    padding: 0.85rem 0.75rem;
    text-align: left;
    color: white;
    font-weight: 600;
    font-size: 0.7rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border: none;
}
.ai-table tbody tr {
    border-bottom: 1px solid var(--ai-border);
    transition: background 0.2s;
}
.ai-table tbody tr:hover { background: #fafbfc; }
.ai-table tbody td {
    padding: 0.85rem 0.75rem;
    vertical-align: middle;
    font-size: 0.85rem;
}

/* Cell Styles */
.ai-user { display: flex; align-items: center; gap: 0.6rem; }
.ai-avatar {
    width: 32px; height: 32px;
    border-radius: 6px;
    background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%);
    display: flex; align-items: center; justify-content: center;
    font-weight: 700; color: #4f46e5; font-size: 0.7rem;
}
.ai-user-name { font-weight: 600; color: var(--ai-dark); font-size: 0.85rem; }
.ai-user-detail { font-size: 0.7rem; color: var(--ai-gray); display: flex; align-items: center; gap: 0.3rem; margin-top: 0.1rem; }

/* Actions */
.ai-actions { display: flex; gap: 0.3rem; }
.ai-action {
    width: 28px; height: 28px;
    border-radius: 6px;
    display: inline-flex; align-items: center; justify-content: center;
    border: none; cursor: pointer;
    transition: all 0.2s;
    text-decoration: none;
    font-size: 0.85rem;
}
.ai-action.edit { background: var(--ai-primary-light); color: var(--ai-primary); }
.ai-action.delete { background: #fee2e2; color: #dc2626; }
.ai-action.view { background: #e0f2fe; color: #0284c7; }
.ai-action:hover { transform: translateY(-1px); box-shadow: 0 2px 6px rgba(0,0,0,0.15); }

/* Empty */
.ai-empty {
    text-align: center;
    padding: 3rem 2rem;
    color: var(--ai-gray);
}
.ai-empty i { font-size: 3rem; color: #cbd5e1; margin-bottom: 0.75rem; }
.ai-empty-title { font-size: 1.1rem; font-weight: 600; color: var(--ai-dark); }

/* Footer */
.ai-table-footer {
    padding: 0.75rem 1rem;
    background: var(--ai-light);
    border-top: 1px solid var(--ai-border);
    font-size: 0.8rem;
    color: var(--ai-gray);
}

/* Description Popup */
.description-popup-overlay {
    position: fixed; top: 0; left: 0; width: 100%; height: 100%;
    background: rgba(0,0,0,0.5); z-index: 9998;
    opacity: 0; visibility: hidden; transition: all 0.3s;
}
.description-popup {
    position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%) scale(0.9);
    background: white; width: 90%; max-width: 500px;
    border-radius: 12px; padding: 1.5rem;
    z-index: 9999;
    opacity: 0; visibility: hidden; transition: all 0.3s;
    box-shadow: 0 10px 25px rgba(0,0,0,0.2);
}
.description-popup.is-visible, .description-popup-overlay.is-visible {
    opacity: 1; visibility: visible;
}
.description-popup.is-visible { transform: translate(-50%, -50%) scale(1); }
.description-popup-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; }
.description-popup-header h4 { margin: 0; font-size: 1.1rem; font-weight: 700; color: var(--ai-dark); }
.description-popup-close { background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--ai-gray); }

/* Pagination */
.ai-pagination {
    display: flex;
    justify-content: center;
    gap: 5px;
    margin-top: 1rem;
}
.ai-page-btn {
    width: 32px; height: 32px;
    display: flex; align-items: center; justify-content: center;
    border: 1px solid var(--ai-border);
    background: white;
    border-radius: 8px;
    cursor: pointer;
    font-size: 0.85rem;
    color: var(--ai-gray);
    transition: all 0.2s;
}
.ai-page-btn:hover { background: var(--ai-light); color: var(--ai-dark); border-color: #cbd5e1; }
.ai-page-btn.active { background: var(--ai-primary); color: white; border-color: var(--ai-primary); }
.ai-page-btn.disabled { opacity: 0.5; cursor: not-allowed; pointer-events: none; }
</style>

<!-- Stats Grid -->
<div class="ai-stats-grid">
    <div class="ai-stat-card purple">
        <div class="ai-stat-header">
            <div class="ai-stat-icon"><i class="fa-solid fa-school"></i></div>
        </div>
        <div class="ai-stat-value"><?php echo $stats['total']; ?></div>
        <div class="ai-stat-label"><?php echo get_phrase('total_schools'); ?></div>
    </div>
</div>

<!-- Table -->
<div class="ai-table-container">
    <div class="ai-table-toolbar">
        <div class="ai-search-box">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" id="quickSearchSchool" placeholder="<?php echo get_phrase('search_schools'); ?>..." onkeyup="quickSearchSchool()">
        </div>
    </div>

    <div class="ai-results-bar" id="resultsBarSchool">
        <span id="resultsTextSchool"></span>
        <button onclick="resetFiltersSchool()"><i class="fa-solid fa-times"></i> <?php echo get_phrase('clear_search'); ?></button>
    </div>

    <?php if (count($schools) > 0): ?>
    <div class="table-responsive">
        <table class="ai-table" id="schoolTable">
            <thead>
                <tr>
                    <th><?php echo get_phrase('name'); ?></th>
                    <th><?php echo get_phrase('address'); ?></th>
                    <th><?php echo get_phrase('phone'); ?></th>
                    <th><?php echo get_phrase('description'); ?></th>
                    <th><?php echo get_phrase('category'); ?></th>
                    <th><?php echo get_phrase('options'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($schools as $school): ?>
                <tr>
                    <td>
                        <div class="ai-user">
                            <div class="ai-avatar"><?php echo strtoupper(substr($school['name'], 0, 1)); ?></div>
                            <div>
                                <div class="ai-user-name"><?php echo $school['name']; ?></div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <span title="<?php echo $school['address']; ?>">
                                <?php echo strlen($school['address']) > 25 ? substr($school['address'], 0, 25) . '...' : $school['address']; ?>
                            </span>
                            <?php if (strlen($school['address']) > 25): ?>
                            <a href="javascript:void(0);" class="ai-action view" onclick="showDescriptionPopup('<?php echo htmlspecialchars($school['address'], ENT_QUOTES); ?>', '<?php echo get_phrase('address'); ?>')">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td><?php echo $school['phone']; ?></td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <span title="<?php echo $school['description']; ?>">
                                <?php echo strlen($school['description']) > 25 ? substr($school['description'], 0, 25) . '...' : $school['description']; ?>
                            </span>
                            <?php if (strlen($school['description']) > 25): ?>
                            <a href="javascript:void(0);" class="ai-action view" 
                               data-content="<?php echo htmlspecialchars($school['description'], ENT_QUOTES); ?>"
                               data-title="<?php echo get_phrase('description'); ?>"
                               onclick="showDescriptionPopup(this)">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td><?php echo $school['category']; ?></td>
                    <td>
                        <div class="ai-actions">
                            <a href="javascript:void(0);" class="ai-action edit" onclick="rightModal('<?php echo site_url('modal/popup/school/edit/' . $school['id']); ?>', '<?php echo get_phrase('update_school'); ?>')" title="<?php echo get_phrase('edit'); ?>">
                                <i class="fa-solid fa-pencil"></i>
                            </a>
                            <a href="javascript:void(0);" class="ai-action delete" onclick="confirmModal('<?php echo route('school_crud/delete/' . $school['id']); ?>', showAllSchools )" title="<?php echo get_phrase('delete'); ?>">
                                <i class="fa-solid fa-trash"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="ai-table-footer">
        <div class="d-flex justify-content-between align-items-center">
            <div id="footerInfoSchool">
                <i class="fa-solid fa-circle-info"></i> <?php echo get_phrase('showing'); ?> <strong><?php echo count($schools); ?></strong> <?php echo get_phrase('schools'); ?>
            </div>
            <div id="paginationSchool" class="ai-pagination"></div>
        </div>
    </div>
    <?php else: ?>
        <div class="ai-empty">
            <i class="fa-solid fa-school-flag"></i>
            <div class="ai-empty-title"><?php echo get_phrase('no_schools_found'); ?></div>
        </div>
    <?php endif; ?>
</div>

<!-- Popup for Description/Address -->
<div id="description-popup-overlay" class="description-popup-overlay"></div>
<div id="description-popup" class="description-popup">
    <div class="description-popup-header">
        <h4 id="popup-title"><?php echo get_phrase('details'); ?></h4>
        <button onclick="hideDescriptionPopup()" class="description-popup-close">&times;</button>
    </div>
    <div id="description-popup-content" class="description-popup-content" style="max-height: 300px; overflow-y: auto;"></div>
</div>

<script>
window.currentPageSchool = Number.isInteger(window.currentPageSchool) ? window.currentPageSchool : 1;
window.rowsPerPageSchool = 10;

function quickSearchSchool() {
    const input = document.getElementById('quickSearchSchool');
    const filter = input.value.toLowerCase();
    const table = document.getElementById('schoolTable');
    if (!table) return;
    
    // Get all rows from tbody
    const tbody = table.getElementsByTagName('tbody')[0];
    const tr = tbody.getElementsByTagName('tr');
    
    // 1. Filter Logic
    let matchedRows = [];
    
    for (let i = 0; i < tr.length; i++) {
        const tdName = tr[i].getElementsByTagName('td')[0];
        const tdAddress = tr[i].getElementsByTagName('td')[1];
        const tdPhone = tr[i].getElementsByTagName('td')[2];
        const tdDesc = tr[i].getElementsByTagName('td')[3];
        const tdCat = tr[i].getElementsByTagName('td')[4];
        
        if (tdName) {
            const txtName = tdName.textContent || tdName.innerText;
            const txtAddress = tdAddress.textContent || tdAddress.innerText;
            const txtPhone = tdPhone.textContent || tdPhone.innerText;
            const txtDesc = tdDesc.textContent || tdDesc.innerText;
            const txtCat = tdCat.textContent || tdCat.innerText;
            
            if (txtName.toLowerCase().indexOf(filter) > -1 || 
                txtAddress.toLowerCase().indexOf(filter) > -1 || 
                txtPhone.toLowerCase().indexOf(filter) > -1 || 
                txtDesc.toLowerCase().indexOf(filter) > -1 || 
                txtCat.toLowerCase().indexOf(filter) > -1) {
                matchedRows.push(tr[i]);
            }
        }
        tr[i].style.display = "none"; // Hide all initially
    }

    // 2. Pagination Logic
    const totalRows = matchedRows.length;
    const totalPages = Math.ceil(totalRows / window.rowsPerPageSchool);
    
    // Reset to page 1 if search changes (detected by checking if current page is out of bounds or if we want to reset on typing)
    // Ideally, we reset to page 1 only if the user is typing, but here we can just clamp it.
    // However, if the user types, it's better to jump to page 1.
    // We can detect if this call comes from the input event.
    // For simplicity, let's keep currentPageSchool valid. 
    // BUT: If the user searches, they usually expect to see the top results. 
    // Let's reset to 1 if the filter is not empty, OR we can track if filter changed.
    // Let's just clamp for now, but usually search resets page.
    
    if (currentPageSchool > totalPages) currentPageSchool = totalPages || 1;
    if (currentPageSchool < 1) currentPageSchool = 1;
    
    // Calculate slice
    const startIndex = (currentPageSchool - 1) * window.rowsPerPageSchool;
    const endIndex = Math.min(startIndex + window.rowsPerPageSchool, totalRows);

    // Show visible rows
    for (let i = startIndex; i < endIndex; i++) {
        matchedRows[i].style.display = "";
    }

    // 3. Update UI Elements
    const bar = document.getElementById('resultsBarSchool');
    const text = document.getElementById('resultsTextSchool');
    
    if (filter) {
        bar.classList.add('active');
        if (totalRows === 0) {
            bar.classList.add('no-results');
            text.innerHTML = '<i class="fa-solid fa-circle-exclamation"></i> <?php echo get_phrase('no_results_found'); ?>';
        } else {
            bar.classList.remove('no-results');
            text.innerHTML = `<i class="fa-solid fa-circle-check"></i> <strong>${totalRows}</strong> <?php echo get_phrase('results_found'); ?>`;
        }
        // When searching, we might want to reset page to 1 if we haven't already.
        // But since we didn't track "previous filter", we'll just let the user navigate.
    } else {
        bar.classList.remove('active');
    }

    // Update Footer Info
    const footerInfo = document.getElementById('footerInfoSchool');
    if (totalRows > 0) {
        footerInfo.innerHTML = `<i class="fa-solid fa-circle-info"></i> <?php echo get_phrase('showing'); ?> <strong>${startIndex + 1}-${endIndex}</strong> <?php echo get_phrase('of'); ?> <strong>${totalRows}</strong> <?php echo get_phrase('schools'); ?>`;
    } else {
        footerInfo.innerHTML = `<i class="fa-solid fa-circle-info"></i> <?php echo get_phrase('showing'); ?> <strong>0</strong> <?php echo get_phrase('schools'); ?>`;
    }

    // Render Pagination Controls
    renderPaginationSchool(totalPages, currentPageSchool);
}

function renderPaginationSchool(totalPages, currentPage) {
    const paginationContainer = document.getElementById('paginationSchool');
    paginationContainer.innerHTML = '';
    
    if (totalPages <= 1) return;

    // Prev Button
    const prevBtn = document.createElement('div');
    prevBtn.className = `ai-page-btn ${currentPage === 1 ? 'disabled' : ''}`;
    prevBtn.innerHTML = '<i class="fa-solid fa-chevron-left"></i>';
    prevBtn.onclick = () => changePageSchool(currentPage - 1);
    paginationContainer.appendChild(prevBtn);

    // Page Numbers (Simple version: 1 2 3 ... or just all if few)
    // For simplicity, let's show all if < 7, otherwise show start, end, current
    // Or just simple all pages for now (assuming not thousands of schools)
    
    let startPage = Math.max(1, currentPage - 2);
    let endPage = Math.min(totalPages, currentPage + 2);
    
    if (startPage > 1) {
         addPageBtnSchool(1, paginationContainer);
         if (startPage > 2) addEllipsisSchool(paginationContainer);
    }

    for (let i = startPage; i <= endPage; i++) {
        addPageBtnSchool(i, paginationContainer);
    }
    
    if (endPage < totalPages) {
        if (endPage < totalPages - 1) addEllipsisSchool(paginationContainer);
        addPageBtnSchool(totalPages, paginationContainer);
    }

    // Next Button
    const nextBtn = document.createElement('div');
    nextBtn.className = `ai-page-btn ${currentPage === totalPages ? 'disabled' : ''}`;
    nextBtn.innerHTML = '<i class="fa-solid fa-chevron-right"></i>';
    nextBtn.onclick = () => changePageSchool(currentPage + 1);
    paginationContainer.appendChild(nextBtn);
}

function addPageBtnSchool(page, container) {
    const btn = document.createElement('div');
    btn.className = `ai-page-btn ${page === currentPageSchool ? 'active' : ''}`;
    btn.innerText = page;
    btn.onclick = () => changePageSchool(page);
    container.appendChild(btn);
}

function addEllipsisSchool(container) {
    const span = document.createElement('span');
    span.innerText = '...';
    span.style.color = '#94a3b8';
    span.style.alignSelf = 'center';
    container.appendChild(span);
}

function changePageSchool(page) {
    currentPageSchool = page;
    quickSearchSchool(); // Re-run render
}

// Reset filter now also resets page
function resetFiltersSchool() {
    document.getElementById('quickSearchSchool').value = '';
    currentPageSchool = 1;
    quickSearchSchool();
}

// Initialize immediately (this script is injected after HTML).
quickSearchSchool();

// Detect typing; bind once even if this block is re-injected via AJAX.
var quickSearchSchoolInput = document.getElementById('quickSearchSchool');
if (quickSearchSchoolInput && !quickSearchSchoolInput.dataset.boundSchoolInput) {
    quickSearchSchoolInput.dataset.boundSchoolInput = '1';
    quickSearchSchoolInput.addEventListener('input', function() {
        currentPageSchool = 1;
        quickSearchSchool();
    });
}

// Popup Logic
var popup = document.getElementById('description-popup');
var overlay = document.getElementById('description-popup-overlay');
var popupContent = document.getElementById('description-popup-content');
var popupTitle = document.getElementById('popup-title');

function showDescriptionPopup(elementOrContent, titleOrUndefined) {
    var content = '';
    var title = '<?php echo get_phrase('details'); ?>';

    if (typeof elementOrContent === 'object' && elementOrContent.getAttribute) {
        // Passed 'this' (DOM Element)
        content = elementOrContent.getAttribute('data-content');
        title = elementOrContent.getAttribute('data-title') || title;
    } else {
        // Passed strings directly (fallback)
        content = elementOrContent;
        if (titleOrUndefined) title = titleOrUndefined;
    }
    
    if(popupContent) popupContent.innerText = content;
    if(popupTitle) popupTitle.innerText = title;
    if(popup) popup.classList.add('is-visible');
    if(overlay) overlay.classList.add('is-visible');
}

function hideDescriptionPopup() {
    if(popup) popup.classList.remove('is-visible');
    if(overlay) overlay.classList.remove('is-visible');
}

if(overlay) overlay.addEventListener('click', hideDescriptionPopup);
</script>