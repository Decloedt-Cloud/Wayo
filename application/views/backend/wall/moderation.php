<style>
/* ============================================================================
   SUPERADMIN MODERATION PAGE - MODERN DESIGN
   ============================================================================ */

:root {
    --mod-primary: #6366f1;
    --mod-primary-rgb: 99, 102, 241;
    --mod-success: #10b981;
    --mod-danger: #ef4444;
    --mod-warning: #f59e0b;
    --mod-dark: #1e293b;
    --mod-gray: #64748b;
    --mod-light: #f8fafc;
    --mod-border: #e2e8f0;
    --mod-white: #ffffff;
}

/* Page Header */
.mod-header {
    background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
    border-radius: 16px;
    padding: 1.5rem 2rem;
    margin-bottom: 1.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 4px 20px rgba(99, 102, 241, 0.3);
}

.mod-header-left {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.mod-header-icon {
    width: 56px;
    height: 56px;
    border-radius: 16px;
    background: rgba(255,255,255,0.15);
    backdrop-filter: blur(10px);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
}

.mod-header-title h2 {
    margin: 0;
    color: white;
    font-size: 1.5rem;
    font-weight: 700;
}

.mod-header-title p {
    margin: 0.25rem 0 0;
    color: rgba(255,255,255,0.8);
    font-size: 0.9rem;
}

/* Filters */
.mod-filters {
    background: white;
    padding: 1.5rem;
    border-radius: 16px;
    border: 1px solid var(--mod-border);
    margin-bottom: 1.5rem;
    box-shadow: 0 2px 12px rgba(0,0,0,0.05);
}

.mod-filter-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
}

.mod-filter-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.mod-filter-label {
    font-size: 0.85rem;
    font-weight: 600;
    color: var(--mod-gray);
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.mod-filter-select {
    padding: 0.75rem 1rem;
    border: 1px solid var(--mod-border);
    border-radius: 10px;
    font-size: 0.9rem;
    color: var(--mod-dark);
    background: var(--mod-light);
    transition: all 0.2s;
    width: 100%;
    cursor: pointer;
}

.mod-filter-select:focus {
    outline: none;
    border-color: var(--mod-primary);
    background: white;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}

.mod-filter-actions {
    display: flex;
    gap: 1rem;
    margin-top: 1rem;
}

.mod-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    border-radius: 10px;
    font-size: 0.9rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    border: none;
}

.mod-btn-primary {
    background: linear-gradient(135deg, var(--mod-primary), #8b5cf6);
    color: white;
    box-shadow: 0 4px 12px rgba(var(--mod-primary-rgb), 0.3);
}

.mod-btn-primary:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(var(--mod-primary-rgb), 0.4);
}

/* Posts Grid */
.mod-posts-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

/* Post Card */
.mod-post-card {
    background: white;
    border-radius: 12px;
    border: 1px solid var(--mod-border);
    padding: 1rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    transition: all 0.2s;
}

.mod-post-card:hover {
    box-shadow: 0 4px 16px rgba(0,0,0,0.08);
    transform: translateY(-2px);
}

.mod-post-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
    padding-bottom: 0.75rem;
    border-bottom: 1px solid var(--mod-border);
}

.mod-post-type {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.7rem;
    font-weight: 600;
    text-transform: uppercase;
}

.mod-post-type.announcement {
    background: #dcfce7;
    color: #16a34a;
}

.mod-post-type.post {
    background: var(--mod-light);
    color: var(--mod-gray);
}

.mod-post-type.reported {
    background: #fef3c7;
    color: #dc2626;
}

.mod-post-author {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.mod-post-avatar {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid var(--mod-border);
}

.mod-post-info {
    display: flex;
    flex-direction: column;
}

.mod-post-author-name {
    font-size: 0.85rem;
    font-weight: 600;
    color: var(--mod-dark);
}

.mod-post-meta {
    font-size: 0.75rem;
    color: var(--mod-gray);
}

.mod-post-body {
    color: var(--mod-dark);
    line-height: 1.6;
    font-size: 0.9rem;
    margin-bottom: 1rem;
    max-height: 100px;
    overflow-y: auto;
}

.mod-post-body.hidden-post {
    opacity: 0.5;
}

.mod-post-actions {
    display: flex;
    gap: 0.5rem;
    padding-top: 0.75rem;
    border-top: 1px solid var(--mod-border);
}

.mod-action-btn {
    padding: 0.5rem 0.75rem;
    border-radius: 8px;
    border: 1px solid var(--mod-border);
    background: white;
    color: var(--mod-gray);
    cursor: pointer;
    transition: all 0.2s;
    font-size: 0.85rem;
    font-weight: 500;
}

.mod-action-btn:hover {
    background: var(--mod-light);
    color: var(--mod-dark);
}

.mod-action-btn.success {
    color: var(--mod-success);
    border-color: rgba(16, 185, 129, 0.3);
}

.mod-action-btn.danger {
    color: var(--mod-danger);
    border-color: rgba(239, 68, 68, 0.3);
}

.mod-action-btn.danger:hover {
    background: #fef2f2;
    border-color: var(--mod-danger);
}

.mod-reports-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.25rem 0.75rem;
    background: #fef3c7;
    color: #dc2626;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
}

/* Empty State */
.mod-empty {
    text-align: center;
    padding: 4rem 2rem;
}

.mod-empty-icon {
    font-size: 4rem;
    color: var(--mod-gray);
    margin-bottom: 1.5rem;
    opacity: 0.5;
}

.mod-empty-text {
    font-size: 1.1rem;
    color: var(--mod-gray);
    font-weight: 500;
}

/* Loading */
.mod-loading {
    text-align: center;
    padding: 3rem;
    color: var(--mod-gray);
}

.mod-loading i {
    font-size: 2rem;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    100% { transform: rotate(360deg); }
}

/* Load More */
.mod-load-more {
    text-align: center;
    padding: 1.5rem;
}

/* Reports Panel */
.mod-reports-panel {
    background: #f8fafc;
    border: 1px solid var(--mod-border);
    border-radius: 12px;
    padding: 1rem;
    margin-top: 1rem;
}

.mod-reports-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.mod-reports-title {
    font-size: 0.9rem;
    font-weight: 600;
    color: var(--mod-dark);
}

.mod-reports-list {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.mod-report-item {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    padding: 0.75rem;
    background: white;
    border-radius: 8px;
    border: 1px solid var(--mod-border);
}

.mod-report-icon {
    color: var(--mod-warning);
    font-size: 1rem;
    margin-top: 0.125rem;
}

.mod-report-content {
    flex: 1;
}

.mod-reporter {
    font-size: 0.85rem;
    color: var(--mod-gray);
}

.mod-report-reason {
    font-size: 0.8rem;
    color: var(--mod-dark);
    margin-top: 0.25rem;
}

.mod-report-date {
    font-size: 0.75rem;
    color: var(--mod-gray);
}

.mod-report-status {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.7rem;
    font-weight: 600;
}

.mod-report-status.pending {
    background: #fef3c7;
    color: #dc2626;
}

.mod-report-status.resolved {
    background: #dcfce7;
    color: #16a34a;
}

.mod-report-status.dismissed {
    background: var(--mod-light);
    color: var(--mod-gray);
}
</style>

<!-- Page Header -->
<div class="row">
    <div class="col-12">
        <div class="mod-header">
            <div class="mod-header-left">
                <div class="mod-header-icon">
                    <i class="mdi mdi-shield-check-outline"></i>
                </div>
                <div class="mod-header-title">
                    <h2><?php echo get_phrase('wall_moderation'); ?></h2>
                    <p><?php echo get_phrase('manage_posts_across_all_communities'); ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="row">
    <div class="col-12">
        <div class="mod-filters">
            <div class="mod-filter-grid">
                <div class="mod-filter-group">
                    <label class="mod-filter-label">
                        <i class="mdi mdi-forum-outline"></i>
                        <?php echo get_phrase('post_type'); ?>
                    </label>
                    <select class="mod-filter-select" id="filter-type">
                        <option value=""><?php echo get_phrase('all'); ?></option>
                        <option value="post"><?php echo get_phrase('posts'); ?></option>
                        <option value="announcement"><?php echo get_phrase('announcements'); ?></option>
                    </select>
                </div>
                <div class="mod-filter-group">
                    <label class="mod-filter-label">
                        <i class="mdi mdi-google-classroom"></i>
                        <?php echo get_phrase('wall_type'); ?>
                    </label>
                    <select class="mod-filter-select" id="filter-scope">
                        <option value=""><?php echo get_phrase('all'); ?></option>
                        <option value="community"><?php echo get_phrase('community'); ?></option>
                        <option value="class"><?php echo get_phrase('class'); ?></option>
                    </select>
                </div>
                <div class="mod-filter-group">
                    <label class="mod-filter-label">
                        <i class="mdi mdi-filter-outline"></i>
                        <?php echo get_phrase('status'); ?>
                    </label>
                    <select class="mod-filter-select" id="filter-status">
                        <option value=""><?php echo get_phrase('all'); ?></option>
                        <option value="published"><?php echo get_phrase('published'); ?></option>
                        <option value="hidden"><?php echo get_phrase('hidden'); ?></option>
                    </select>
                </div>
                <div class="mod-filter-group">
                    <label class="mod-filter-label">
                        <i class="mdi mdi-flag-outline"></i>
                        <?php echo get_phrase('reports'); ?>
                    </label>
                    <select class="mod-filter-select" id="filter-reports">
                        <option value=""><?php echo get_phrase('all'); ?></option>
                        <option value="reported"><?php echo get_phrase('reported_only'); ?></option>
                    </select>
                </div>
            </div>
            <div class="mod-filter-actions">
                <button class="mod-btn mod-btn-primary" onclick="applyFilters()">
                    <i class="mdi mdi-magnify"></i>
                    <?php echo get_phrase('search'); ?>
                </button>
                <button class="mod-btn" onclick="resetFilters()">
                    <i class="mdi mdi-refresh"></i>
                    <?php echo get_phrase('reset'); ?>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Posts Grid -->
<div class="row">
    <div class="col-12">
        <div id="posts-container" class="mod-posts-grid">
            <!-- Posts will be loaded dynamically -->
        </div>
    </div>
</div>

<!-- Load More -->
<div class="mod-load-more">
    <button id="load-more-btn" class="mod-btn mod-btn-primary" onclick="loadMore()">
        <i class="mdi mdi-chevron-down"></i>
        <?php echo get_phrase('load_more'); ?>
    </button>
</div>

<script>
let currentPage = 1;
let loading = false;
let filters = {};

// Load initial posts
$(document).ready(function() {
    loadPosts(1, {});
});

// Load posts
function loadPosts(page, newFilters = {}) {
    if (loading) return;
    loading = true;
    
    // Update filters
    if (Object.keys(newFilters).length > 0) {
        filters = newFilters;
        currentPage = 1;
    }
    
    const params = {
        page: page,
        limit: 20,
        ...filters
    };
    
    $('#posts-container').html(`
        <div class="mod-loading">
            <i class="mdi mdi-loading"></i>
        </div>
    `);
    
    $.ajax({
        url: '/api/moderation/posts',
        method: 'GET',
        data: params,
        dataType: 'json',
        success: function(response) {
            loading = false;
            
            if (response.status && response.data) {
                renderPosts(response.data.posts);
                currentPage = response.data.pagination.page;
            } else {
                showEmptyState();
            }
        },
        error: function() {
            loading = false;
            error_notify('<?php echo get_phrase('error_loading_posts'); ?>');
        }
    });
}

// Render posts
function renderPosts(posts) {
    if (posts.length === 0) {
        showEmptyState();
        return;
    }
    
    let html = '';
    posts.forEach(function(post) {
        html += buildPostCard(post);
    });
    
    $('#posts-container').html(html);
}

// Build post card
function buildPostCard(post) {
    const postDate = new Date(post.created_at).toLocaleString();
    const isHidden = post.status === 'hidden';
    const isAnnouncement = post.type === 'announcement';
    const isReported = post.reports_count > 0;
    
    let typeBadge = '';
    if (isAnnouncement) {
        typeBadge = `<span class="mod-post-type announcement"><i class="mdi mdi-bullhorn"></i></span>`;
    } else {
        typeBadge = `<span class="mod-post-type post"><i class="mdi mdi-forum"></i></span>`;
    }
    
    let reportsBadge = '';
    if (isReported) {
        reportsBadge = `<span class="mod-reports-badge"><i class="mdi mdi-flag"></i> ${post.reports_count}</span>`;
    }
    
    const scopeLabel = post.scope_type === 'community' 
        ? '<?php echo get_phrase('community'); ?>' 
        : '<?php echo get_phrase('class'); ?>';
    
    return `
        <div class="mod-post-card ${isHidden ? 'hidden-post' : ''}">
            <div class="mod-post-header">
                <div>
                    ${typeBadge}
                    ${reportsBadge}
                </div>
                <div>
                    <span class="mod-post-meta">
                        <i class="mdi mdi-google-classroom"></i> ${scopeLabel} #${post.scope_id}
                    </span>
                    <span class="mod-post-meta">
                        <i class="mdi mdi-calendar"></i> ${postDate}
                    </span>
                </div>
            </div>
            
            <div class="mod-post-author">
                <img src="${post.author_avatar || '<?php echo base_url('uploads/users/placeholder.jpg'); ?>'}" 
                     alt="${post.author_name}" 
                     class="mod-post-avatar">
                <div class="mod-post-info">
                    <div class="mod-post-author-name">${escapeHtml(post.author_name)}</div>
                </div>
            </div>
            
            ${post.title ? `<h3 class="mod-post-title">${escapeHtml(post.title)}</h3>` : ''}
            
            <div class="mod-post-body ${isHidden ? 'hidden-post' : ''}">
                ${post.body}
            </div>
            
            <div class="mod-post-actions">
                ${isHidden ? `
                    <button class="mod-action-btn success" onclick="unhidePost(${post.id})">
                        <i class="mdi mdi-eye-outline"></i>
                        <?php echo get_phrase('show'); ?>
                    </button>
                ` : `
                    <button class="mod-action-btn danger" onclick="hidePost(${post.id})">
                        <i class="mdi mdi-eye-off-outline"></i>
                        <?php echo get_phrase('hide'); ?>
                    </button>
                `}
                
                <button class="mod-action-btn" onclick="editPost(${post.id})">
                    <i class="mdi mdi-pencil-outline"></i>
                    <?php echo get_phrase('edit'); ?>
                </button>

                <button class="mod-action-btn danger" onclick="deletePost(${post.id})">
                    <i class="mdi mdi-delete-outline"></i>
                    <?php echo get_phrase('delete'); ?>
                </button>
                
                ${isReported ? `
                    <button class="mod-action-btn" onclick="showReports(${post.id})">
                        <i class="mdi mdi-list-box-outline"></i>
                        <?php echo get_phrase('view_reports'); ?>
                    </button>
                ` : ''}
            </div>
            
            ${isReported ? buildReportsPanel(post) : ''}
        </div>
    `;
}

// Build reports panel
function buildReportsPanel(post) {
    let html = `
        <div class="mod-reports-panel">
            <div class="mod-reports-header">
                <div class="mod-reports-title">
                    <i class="mdi mdi-flag-outline"></i>
                    <?php echo get_phrase('reports'); ?> (${post.reports_count})
                </div>
            </div>
            <div class="mod-reports-list">
    `;
    
    if (post.reports && post.reports.length > 0) {
        post.reports.forEach(function(report) {
            const reportDate = new Date(report.created_at).toLocaleString();
            const statusClass = `mod-report-status ${report.status}`;
            const statusLabel = report.status === 'pending' 
                ? '<?php echo get_phrase('pending'); ?>' 
                : (report.status === 'resolved' ? '<?php echo get_phrase('resolved'); ?>' : '<?php echo get_phrase('dismissed'); ?>');
            
            html += `
                <div class="mod-report-item">
                    <div class="mod-report-icon">
                        <i class="mdi mdi-alert-circle"></i>
                    </div>
                    <div class="mod-report-content">
                        <div class="mod-reporter">
                            <strong>${escapeHtml(report.reporter_name)}</strong>
                            <span class="mod-report-date">${reportDate}</span>
                        </div>
                        <div class="mod-report-reason">${escapeHtml(report.reason)}</div>
                    </div>
                    <span class="mod-report-status ${statusClass}">${statusLabel}</span>
                </div>
            `;
        });
    } else {
        html += `
            <div class="mod-reports-list">
                <div style="text-align: center; padding: 1rem; color: var(--mod-gray);">
                    <?php echo get_phrase('no_reports'); ?>
                </div>
            </div>
        `;
    }
    
    html += '</div></div>';
    return html;
}

// Apply filters
function applyFilters() {
    const filterType = $('#filter-type').val();
    const filterScope = $('#filter-scope').val();
    const filterStatus = $('#filter-status').val();
    const filterReports = $('#filter-reports').val();
    
    filters = {};
    
    if (filterType) filters.type = filterType;
    if (filterScope) filters.scope_type = filterScope;
    if (filterStatus) filters.status = filterStatus;
    if (filterReports === 'reported') filters.reported = '1';
    
    loadPosts(1, filters);
}

// Reset filters
function resetFilters() {
    $('#filter-type').val('');
    $('#filter-scope').val('');
    $('#filter-status').val('');
    $('#filter-reports').val('');
    filters = {};
    loadPosts(1, {});
}

// Load more
function loadMore() {
    loadPosts(currentPage + 1, filters);
}

// Post actions
function hidePost(postId) {
    if (!confirm('<?php echo get_phrase('confirm_hide_post'); ?>')) return;
    
    $.ajax({
        url: `/api/posts/${postId}/hide`,
        method: 'POST',
        success: function(response) {
            if (response.status) {
                success_notify(response.message);
                loadPosts(currentPage, filters);
            } else {
                error_notify(response.message);
            }
        }
    });
}

function unhidePost(postId) {
    $.ajax({
        url: `/api/posts/${postId}/unhide`,
        method: 'POST',
        success: function(response) {
            if (response.status) {
                success_notify(response.message);
                loadPosts(currentPage, filters);
            } else {
                error_notify(response.message);
            }
        }
    });
}

function deletePost(postId) {
    if (!confirm('<?php echo get_phrase('confirm_delete_post'); ?>')) return;
    
    $.ajax({
        url: `/api/posts/${postId}`,
        method: 'DELETE',
        success: function(response) {
            if (response.status) {
                success_notify(response.message);
                loadPosts(currentPage, filters);
            } else {
                error_notify(response.message);
            }
        }
    });
}

function editPost(postId) {
    var url = '<?php echo site_url('wall/edit_post/'); ?>' + postId;
    var title = '<i class="mdi mdi-pencil" style="color: #6366f1; font-size: 1.5rem;"></i> <?php echo get_phrase('edit_post'); ?>';
    
    if (typeof largeModal === 'function') {
        largeModal(url, title);
    } else {
        if (typeof jQuery !== 'undefined' && jQuery('#large-modal').length) {
            jQuery('#large-modal').modal('show', {backdrop: 'true'});
            jQuery('#large-modal .modal-body').html('<div class="text-center p-5"><i class="mdi mdi-loading mdi-spin" style="font-size: 2rem;"></i></div>');
            jQuery('#large-modal .modal-title').html(title);
            
            jQuery.ajax({
                url: url,
                success: function(response) {
                    jQuery('#large-modal .modal-body').html(response);
                },
                error: function() {
                    jQuery('#large-modal .modal-body').html('<div class="alert alert-danger">Error loading form</div>');
                }
            });
        } else {
             error_notify('<?php echo get_phrase('unable_to_load_modal'); ?>');
        }
    }
}

// Show reports for a post (expand panel)
function showReports(postId) {
    const panel = $(`.mod-reports-panel[data-post-id="${postId}"]`);
    panel.toggle();
}

// Escape HTML
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
</script>

