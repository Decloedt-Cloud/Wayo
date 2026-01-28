<?php
$school_id = school_id();
$admins = $this->db->get_where('users', array('role' => 'admin'))->result_array();

// Statistics
$stats = [
    'total' => count($admins)
];
?>

<style>
/* ============================================================================
   ADMIN LIST - COMPACT MODERN STYLES (Adapted from Invoice List)
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

/* Student/Admin Cell */
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

/* Pagination */
.ai-pagination { display: flex; gap: 0.25rem; }
.ai-page-btn {
    width: 28px; height: 28px;
    display: flex; align-items: center; justify-content: center;
    border-radius: 6px;
    background: white;
    border: 1px solid var(--ai-border);
    color: var(--ai-gray);
    cursor: pointer;
    font-size: 0.75rem;
    transition: all 0.2s;
}
.ai-page-btn:hover:not(.disabled) { background: var(--ai-light); color: var(--ai-primary); border-color: var(--ai-primary); }
.ai-page-btn.active { background: var(--ai-primary); color: white; border-color: var(--ai-primary); }
.ai-page-btn.disabled { opacity: 0.5; cursor: not-allowed; }

/* Description Popup */
.description-popup-overlay {
    position: fixed; top: 0; left: 0; width: 100%; height: 100%;
    background: rgba(0, 0, 0, 0.5); z-index: 9998;
    display: none; opacity: 0; transition: opacity 0.3s;
}
.description-popup {
    position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%) scale(0.9);
    background: white; width: 90%; max-width: 500px;
    border-radius: 12px; padding: 20px; z-index: 9999;
    box-shadow: 0 10px 25px rgba(0,0,0,0.2);
    display: none; opacity: 0; transition: all 0.3s;
}
.description-popup.is-visible, .description-popup-overlay.is-visible {
    display: block; opacity: 1;
}
.description-popup.is-visible { transform: translate(-50%, -50%) scale(1); }
.description-popup-header {
    display: flex; justify-content: space-between; align-items: center;
    margin-bottom: 15px; border-bottom: 1px solid #eee; padding-bottom: 10px;
}
.description-popup-close {
    background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #666;
}
</style>

<!-- Stats Grid -->
<div class="ai-stats-grid">
    <div class="ai-stat-card purple">
        <div class="ai-stat-header">
            <div class="ai-stat-icon"><i class="fa-solid fa-user-shield"></i></div>
        </div>
        <div class="ai-stat-value"><?php echo $stats['total']; ?></div>
        <div class="ai-stat-label"><?php echo get_phrase('total_admins'); ?></div>
    </div>
</div>

<div class="ai-table-container">
    <div class="ai-table-toolbar">
        <div class="ai-search-box">
            <i class="fa-solid fa-search"></i>
            <input type="text" id="quickSearchAdmin" placeholder="<?php echo get_phrase('search_by_name_email_school'); ?>...">
        </div>
    </div>

    <div class="ai-results-bar" id="resultsBarAdmin">
        <span id="resultsTextAdmin"></span>
        <button onclick="resetFiltersAdmin()"><i class="fa-solid fa-xmark"></i> <?php echo get_phrase('clear_search'); ?></button>
    </div>

    <table class="ai-table" id="adminTable">
        <thead>
            <tr>
                <th><?php echo get_phrase('name'); ?></th>
                <th><?php echo get_phrase('email'); ?></th>
                <th><?php echo get_phrase('school_details'); ?></th>
                <th><?php echo get_phrase('options'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($admins as $admin): ?>
                <tr>
                    <td>
                        <div class="ai-user">
                            <div class="ai-avatar"><?php echo strtoupper(substr($admin['name'], 0, 2)); ?></div>
                            <div class="ai-user-name"><?php echo $admin['name']; ?></div>
                        </div>
                    </td>
                    <td>
                         <div class="ai-user-detail">
                            <i class="fa-solid fa-envelope"></i>
                            <?php 
                                $email = $admin['email'];
                                echo strlen($email) > 30 ? substr($email, 0, 30) . '...' : $email; 
                            ?>
                            <?php if (strlen($email) > 30): ?>
                            <button type="button" class="btn btn-sm btn-link p-0 ms-1" 
                                onclick="showDescriptionPopup(this)" 
                                data-description="<?php echo htmlspecialchars($email); ?>">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                            <?php endif; ?>
                         </div>
                    </td>
                    <td>
                        <?php
                        $school_details = $this->crud_model->get_school_details_by_id($admin['school_id']);
                        if ($school_details): ?>
                            <div class="ai-user-name"><?php echo $school_details['name']; ?></div>
                            <div class="ai-user-detail"><i class="fa-solid fa-phone"></i> <?php echo $school_details['phone']; ?></div>
                            <div class="ai-user-detail"><i class="fa-solid fa-location-dot"></i> <?php echo $school_details['address']; ?></div>
                        <?php else: ?>
                            <span class="ai-user-detail"><?php echo get_phrase('no_school_assigned'); ?></span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="ai-actions">
                             <a href="javascript:void(0);" class="ai-action edit" onclick="rightModal('<?php echo site_url('modal/popup/admin/edit/' . $admin['id']); ?>', '<?php echo get_phrase('update_admin'); ?>')" title="<?php echo get_phrase('edit'); ?>"><i class="fa-solid fa-pencil"></i></a>
                             <a href="javascript:void(0);" class="ai-action delete" onclick="confirmModal('<?php echo route('admin/delete/' . $admin['id']); ?>', showAllAdmins )" title="<?php echo get_phrase('delete'); ?>"><i class="fa-solid fa-trash"></i></a>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <?php if (empty($admins)): ?>
        <div class="ai-empty">
            <i class="fa-solid fa-user-slash"></i>
            <div class="ai-empty-title"><?php echo get_phrase('no_admins_found'); ?></div>
        </div>
    <?php endif; ?>

    <div class="ai-table-footer">
        <div class="d-flex justify-content-between align-items-center">
            <div id="footerInfoAdmin">
                <i class="fa-solid fa-circle-info"></i> <?php echo get_phrase('showing'); ?> <strong><?php echo count($admins); ?></strong> <?php echo get_phrase('entries'); ?>
            </div>
            <div id="paginationAdmin" class="ai-pagination"></div>
        </div>
    </div>
</div>

<!-- Popup Structure -->
<div id="description-popup-overlay" class="description-popup-overlay"></div>
<div id="description-popup" class="description-popup">
    <div class="description-popup-header">
        <h4><?php echo get_phrase('full_details'); ?></h4>
        <button onclick="hideDescriptionPopup()" class="description-popup-close"><i class="fa-solid fa-times"></i></button>
    </div>
    <div id="description-popup-content" class="description-popup-content"></div>
</div>

<script>
    // Variables
    var currentPageAdmin = 1;
    var rowsPerPageAdmin = 10;
    
    // Popup Logic
    var popup = document.getElementById('description-popup');
    var overlay = document.getElementById('description-popup-overlay');
    var popupContent = document.getElementById('description-popup-content');

    function showDescriptionPopup(button) {
        var description = button.getAttribute('data-description');
        if(popupContent) popupContent.innerText = description;
        if(popup) popup.classList.add('is-visible');
        if(overlay) overlay.classList.add('is-visible');
    }

    function hideDescriptionPopup() {
        if(popup) popup.classList.remove('is-visible');
        if(overlay) overlay.classList.remove('is-visible');
    }

    if(overlay) overlay.addEventListener('click', hideDescriptionPopup);

    // Search & Pagination Logic
    function quickSearchAdmin() {
        var input = document.getElementById('quickSearchAdmin');
        var filter = input.value.toLowerCase();
        var table = document.getElementById('adminTable');
        var tbody = table.getElementsByTagName('tbody')[0];
        var tr = tbody.getElementsByTagName('tr');
        var matchedRows = [];

        // 1. Filter Rows
        for (var i = 0; i < tr.length; i++) {
            var tdName = tr[i].getElementsByTagName('td')[0];
            var tdEmail = tr[i].getElementsByTagName('td')[1];
            var tdSchool = tr[i].getElementsByTagName('td')[2];
            
            if (tdName || tdEmail || tdSchool) {
                var txtName = tdName.textContent || tdName.innerText;
                var txtEmail = tdEmail.textContent || tdEmail.innerText;
                var txtSchool = tdSchool.textContent || tdSchool.innerText;
                
                if (txtName.toLowerCase().indexOf(filter) > -1 || 
                    txtEmail.toLowerCase().indexOf(filter) > -1 || 
                    txtSchool.toLowerCase().indexOf(filter) > -1) {
                    matchedRows.push(tr[i]);
                } else {
                    tr[i].style.display = "none";
                }
            }
        }

        // 2. Pagination Calculation
        var totalRows = matchedRows.length;
        var totalPages = Math.ceil(totalRows / rowsPerPageAdmin);
        
        if (currentPageAdmin > totalPages) currentPageAdmin = totalPages || 1;
        if (currentPageAdmin < 1) currentPageAdmin = 1;
        
        var startIndex = (currentPageAdmin - 1) * rowsPerPageAdmin;
        var endIndex = Math.min(startIndex + rowsPerPageAdmin, totalRows);

        // 3. Show/Hide based on page
        for (var i = 0; i < matchedRows.length; i++) {
            if (i >= startIndex && i < endIndex) {
                matchedRows[i].style.display = "";
            } else {
                matchedRows[i].style.display = "none";
            }
        }

        // 4. Update UI (Results Bar & Footer)
        var bar = document.getElementById('resultsBarAdmin');
        var text = document.getElementById('resultsTextAdmin');
        var footerInfo = document.getElementById('footerInfoAdmin');
        
        // Results Bar
        if (filter) {
            bar.classList.add('active');
            if (totalRows === 0) {
                bar.classList.add('no-results');
                text.innerHTML = '<i class="fa-solid fa-circle-exclamation"></i> <?php echo get_phrase('no_results_found'); ?>';
            } else {
                bar.classList.remove('no-results');
                text.innerHTML = `<i class="fa-solid fa-circle-check"></i> <strong>${totalRows}</strong> <?php echo get_phrase('results_found'); ?>`;
            }
        } else {
            bar.classList.remove('active');
        }

        // Footer Info
        if (totalRows > 0) {
            footerInfo.innerHTML = `<i class="fa-solid fa-circle-info"></i> <?php echo get_phrase('showing'); ?> <strong>${startIndex + 1}-${endIndex}</strong> <?php echo get_phrase('of'); ?> <strong>${totalRows}</strong> <?php echo get_phrase('entries'); ?>`;
        } else {
            footerInfo.innerHTML = `<i class="fa-solid fa-circle-info"></i> <?php echo get_phrase('showing'); ?> <strong>0</strong> <?php echo get_phrase('entries'); ?>`;
        }

        // 5. Render Pagination
        renderPaginationAdmin(totalPages, currentPageAdmin);
    }

    function renderPaginationAdmin(totalPages, currentPage) {
        var container = document.getElementById('paginationAdmin');
        container.innerHTML = '';
        
        if (totalPages <= 1) return;

        // Prev
        var prevBtn = document.createElement('div');
        prevBtn.className = `ai-page-btn ${currentPage === 1 ? 'disabled' : ''}`;
        prevBtn.innerHTML = '<i class="fa-solid fa-chevron-left"></i>';
        prevBtn.onclick = function() { changePageAdmin(currentPage - 1); };
        container.appendChild(prevBtn);

        // Pages
        var startPage = Math.max(1, currentPage - 2);
        var endPage = Math.min(totalPages, currentPage + 2);
        
        if (startPage > 1) {
            addPageBtnAdmin(1, container);
            if (startPage > 2) addEllipsisAdmin(container);
        }

        for (var i = startPage; i <= endPage; i++) {
            addPageBtnAdmin(i, container);
        }
        
        if (endPage < totalPages) {
            if (endPage < totalPages - 1) addEllipsisAdmin(container);
            addPageBtnAdmin(totalPages, container);
        }

        // Next
        var nextBtn = document.createElement('div');
        nextBtn.className = `ai-page-btn ${currentPage === totalPages ? 'disabled' : ''}`;
        nextBtn.innerHTML = '<i class="fa-solid fa-chevron-right"></i>';
        nextBtn.onclick = function() { changePageAdmin(currentPage + 1); };
        container.appendChild(nextBtn);
    }

    function addPageBtnAdmin(page, container) {
        var btn = document.createElement('div');
        btn.className = `ai-page-btn ${page === currentPageAdmin ? 'active' : ''}`;
        btn.innerText = page;
        btn.onclick = function() { changePageAdmin(page); };
        container.appendChild(btn);
    }

    function addEllipsisAdmin(container) {
        var span = document.createElement('span');
        span.innerText = '...';
        span.style.color = '#94a3b8';
        span.style.alignSelf = 'center';
        container.appendChild(span);
    }

    function changePageAdmin(page) {
        currentPageAdmin = page;
        quickSearchAdmin();
    }

    function resetFiltersAdmin() {
        document.getElementById('quickSearchAdmin').value = '';
        currentPageAdmin = 1;
        quickSearchAdmin();
    }

    // Init
    document.addEventListener('DOMContentLoaded', function() {
        quickSearchAdmin();
    });
    
    // Typing resets page
    var searchInput = document.getElementById('quickSearchAdmin');
    if(searchInput) {
        searchInput.addEventListener('input', function() {
            currentPageAdmin = 1;
            quickSearchAdmin();
        });
    }
</script>