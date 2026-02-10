<link rel="stylesheet" href="<?php echo base_url('assets/backend/css/quilljs/quill.snow.css'); ?>">
<style>
/* ============================================================================
   COMMUNITY WALL PAGE - MODERN DESIGN
   ============================================================================ */

:root {
    --wall-primary: #6366f1;
    --wall-primary-rgb: 99, 102, 241;
    --wall-success: #10b981;
    --wall-danger: #ef4444;
    --wall-warning: #f59e0b;
    --wall-dark: #1e293b;
    --wall-gray: #64748b;
    --wall-light: #f8fafc;
    --wall-border: #e2e8f0;
    --wall-white: #ffffff;
}

/* Page Header */
.wall-header {
    
    padding: 0.5rem 0.5rem;;
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
   
}

.wall-header-left {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.wall-header-icon {
    width: 48px;
    height: 48px;
    border-radius: 16px;
    background: linear-gradient(135deg, #6366f1, #818cf8);
      backdrop-filter: blur(10px);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
}

.wall-header-title h1 {
    font-family: 'Outfit', sans-serif;
    font-size: 1.875rem;
    font-weight: 700;
    color: #1e293b;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.wall-header-title p {
    margin: 0.25rem 0 0;
    font-size: 0.9rem;
}

/* Filters */
.wall-filters {
    background: white;
    padding: 1rem;
    border-radius: 12px;
    border: 1px solid var(--wall-border);
    margin-bottom: 1.5rem;
    display: flex;
    gap: 1rem;
    align-items: center;
}

.wall-filter-btn {
    padding: 0.5rem 1rem;
    border-radius: 8px;
    border: 1px solid var(--wall-border);
    background: white;
    color: var(--wall-gray);
    cursor: pointer;
    transition: all 0.2s;
    font-size: 0.85rem;
    font-weight: 500;
}

.wall-filter-btn:hover,
.wall-filter-btn.active {
    background: var(--wall-primary);
    color: white;
}

/* Modal Modernization */
.modal-backdrop.show {
    backdrop-filter: blur(5px);
    -webkit-backdrop-filter: blur(5px);
    background-color: rgba(0, 0, 0, 0.5);
}

.modal.fade .modal-dialog {
    transform: scale(0.95);
    opacity: 0;
    transition: transform 0.3s ease-out, opacity 0.3s ease-out;
    max-width: 600px;
    margin: 1.75rem auto;
}

.modal.show .modal-dialog {
    transform: scale(1);
    opacity: 1;
}

.modal-content {
    border: none;
    border-radius: 16px;
    box-shadow: 0 20px 60px rgba(0,0,0,0.15);
    overflow: hidden;
    background-color: #fff;
}

.modal-header {
    border-bottom: 1px solid #f1f5f9;
    padding: 1.5rem 2rem;
    background: #fff;
    display: flex;
    align-items: center;
}

.modal-title {
    font-family: 'Outfit', sans-serif;
    font-weight: 700;
    color: #1e293b;
    font-size: 1.25rem;
    display: flex;
    align-items: center;
    gap: 12px;
}

/* Posts Feed */
.wall-feed {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

/* Post Card */
.wall-post-card {
    background: white;
    border-radius: 16px;
    border: 1px solid var(--wall-border);
    padding: 1.5rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    transition: all 0.3s;
}

.wall-post-card:hover {
    box-shadow: 0 4px 16px rgba(0,0,0,0.08);
    transform: translateY(-2px);
}

.wall-post-card.announcement {
    background: linear-gradient(135deg, rgba(var(--wall-primary-rgb), 0.03), rgba(var(--wall-primary-rgb), 0.08));
    border-left: 4px solid var(--wall-primary);
}

/* Post Header */
.wall-post-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid var(--wall-border);
}

.wall-post-avatar {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid var(--wall-border);
}

.wall-post-info {
    flex: 1;
}

.wall-post-author {
    font-size: 0.95rem;
    font-weight: 600;
    color: var(--wall-dark);
}

.wall-post-date {
    font-size: 0.8rem;
    color: var(--wall-gray);
}

.wall-post-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.7rem;
    font-weight: 600;
    text-transform: uppercase;
}

.wall-post-badge.announcement {
    background: #dcfce7;
    color: #16a34a;
}

.wall-post-badge.post {
    background: var(--wall-light);
    color: var(--wall-gray);
}

/* Post Title */
.wall-post-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: var(--wall-dark);
    margin-bottom: 0.75rem;
}

/* Post Body */
.wall-post-body {
    color: var(--wall-dark);
    line-height: 1.7;
    margin-bottom: 1rem;
    font-size: 0.95rem;
}

.wall-post-body a {
    color: var(--wall-primary);
    text-decoration: none;
    font-weight: 500;
}

.wall-post-body a:hover {
    text-decoration: underline;
}

/* Post Attachments */
.wall-post-attachments {
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;
    margin-bottom: 1rem;
}

.wall-attachment {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 0.75rem;
    background: var(--wall-light);
    border-radius: 8px;
    border: 1px solid var(--wall-border);
    text-decoration: none;
    color: var(--wall-dark);
    transition: all 0.2s;
    font-size: 0.85rem;
}

.wall-attachment:hover {
    background: var(--wall-primary-light);
    border-color: var(--wall-primary);
    color: var(--wall-primary);
}

.wall-attachment-icon {
    font-size: 1.25rem;
    color: var(--wall-primary);
}

/* Post Actions */
.wall-post-actions {
    display: flex;
    gap: 0.5rem;
    padding-top: 1rem;
    border-top: 1px solid var(--wall-border);
    justify-content: flex-end;
}

.wall-action-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.5rem 0.75rem;
    border-radius: 8px;
    border: 1px solid var(--wall-border);
    background: white;
    color: var(--wall-gray);
    cursor: pointer;
    transition: all 0.2s;
    font-size: 0.8rem;
    font-weight: 500;
}

.wall-action-btn:hover {
    background: var(--wall-light);
    color: var(--wall-dark);
}

.wall-action-btn.danger {
    color: var(--wall-danger);
    border-color: rgba(239, 68, 68, 0.3);
}

.wall-action-btn.danger:hover {
    background: #fef2f2;
    border-color: var(--wall-danger);
}

/* Load More */
.wall-load-more {
    text-align: center;
    padding: 1.5rem;
}

.wall-load-more-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 2rem;
    background: var(--wall-dark);
    color: white;
    border-radius: 12px;
    border: none;
    font-size: 0.95rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
}

.wall-load-more-btn:hover {
    background: #0f172a;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
}

/* Create Post Button */
.wall-create-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    background: linear-gradient(135deg, var(--wall-primary), #8b5cf6);
    color: white;
    border-radius: 12px;
    border: none;
    font-size: 0.95rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    box-shadow: 0 4px 12px rgba(var(--wall-primary-rgb), 0.3);
}

.wall-create-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(var(--wall-primary-rgb), 0.4);
}

/* Empty State */
.wall-empty {
    text-align: center;
    padding: 4rem 2rem;
}

.wall-empty-icon {
    font-size: 4rem;
    color: var(--wall-gray);
    margin-bottom: 1rem;
    opacity: 0.5;
}

.wall-empty-text {
    font-size: 1.1rem;
    color: var(--wall-gray);
    font-weight: 500;
}

/* Loading */
.wall-loading {
    text-align: center;
    padding: 3rem;
    color: var(--wall-gray);
}

.wall-loading i {
    font-size: 2rem;
    animation: spin 1s linear infinite;
}

/* RTL Support */
body[dir="rtl"] .wall-post-header {
    flex-direction: row-reverse;
}

body[dir="rtl"] .wall-post-actions {
    margin-right: auto;
    margin-left: 0;
    justify-content: flex-end;
}

body[dir="rtl"] .wall-header-left {
    flex-direction: row-reverse;
}

body[dir="rtl"] .wall-header-title h1 {
    flex-direction: row-reverse;
}

body[dir="rtl"] .wall-header-title span {
    margin-left: 0 !important;
    margin-right: 10px !important;
}

@keyframes spin {
    100% { transform: rotate(360deg); }
}
</style>

<script src="<?php echo base_url('assets/backend/js/sweetalert.js'); ?>"></script>
<script src="<?php echo base_url('assets/backend/js/quilljs/quill.min.js'); ?>"></script>
<!-- Page Header -->
<div class="row">
    <div class="col-12">
        <div class="wall-header">
            <div class="wall-header-left">
                <div class="wall-header-title">
                    <h1>
                        <div class="wall-header-icon">
                            <i class="mdi mdi-account-group"></i>
                        </div>
                        <?php echo get_phrase('community_wall'); ?>
                        <span style="font-size: 1rem; font-weight: normal; margin-left: 10px; color: #64748b;"><?php echo $community['name']; ?></span>
                    </h1>
                </div>
            </div>
            <?php if ($can_post): ?>
                <button class="wall-create-btn" onclick="showPostComposer()">
                    <i class="mdi mdi-plus-circle"></i>
                    <span><?php echo get_phrase('create_post'); ?></span>
                </button>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="row">
    <div class="col-12">
        <div class="wall-filters">
            <button class="wall-filter-btn active" data-filter="all">
                <i class="mdi mdi-format-list-bulleted"></i>
                <?php echo get_phrase('all_posts'); ?>
            </button>
            <button class="wall-filter-btn" data-filter="announcements">
                <i class="mdi mdi-bullhorn"></i>
                <?php echo get_phrase('announcements'); ?>
            </button>
            <button class="wall-filter-btn" data-filter="posts">
                <i class="mdi mdi-forum"></i>
                <?php echo get_phrase('posts'); ?>
            </button>
            
            <?php if ($can_moderate): ?>
            <div style="margin-left: auto; display: flex; align-items: center; gap: 0.5rem; padding-right: 0.5rem;">
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" id="show-hidden-posts" onchange="toggleHiddenPosts()">
                    <label class="custom-control-label" for="show-hidden-posts" style="cursor: pointer; user-select: none; color: var(--wall-gray); font-size: 0.9rem; font-weight: 500;">
                        <?php echo get_phrase('show_hidden_items'); ?>
                    </label>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Posts Feed -->
<div class="row">
    <div class="col-12">
        <div id="wall-feed" class="wall-feed">
            <!-- Posts will be loaded here -->
        </div>
        
        <div class="wall-load-more">
            <button id="load-more-btn" class="wall-load-more-btn" onclick="loadMorePosts()" style="display: none;">
                <i class="mdi mdi-refresh"></i>
                <?php echo get_phrase('load_more'); ?>
            </button>
        </div>
    </div>
</div>

<script>
// Ensure jQuery is available
var $ = (typeof jQuery !== 'undefined') ? jQuery : null;
if (!$) {
    console.error('jQuery is not loaded! Please check includes_top.php');
    if (typeof window.jQuery !== 'undefined') $ = window.jQuery;
}

// Configuration
const WALL_ID = <?php echo $wall['id']; ?>;
const COMMUNITY_ID = <?php echo $community['id']; ?>;
const CAN_POST = <?php echo $can_post ? 'true' : 'false'; ?>;
const CAN_MODERATE = <?php echo $can_moderate ? 'true' : 'false'; ?>;
const CURRENT_USER_ID = <?php echo $this->session->userdata('user_id'); ?>;

let currentPage = 1;
let currentFilter = 'all';
let loading = false;
let showHidden = false;

// Load initial posts
if ($) {
    $(document).ready(function() {
        loadPosts(1, currentFilter);
    });
}

// Toggle hidden posts
function toggleHiddenPosts() {
    showHidden = $('#show-hidden-posts').is(':checked');
    currentPage = 1;
    loadPosts(1, currentFilter);
}

// Load posts function
function loadPosts(page, filter = 'all') {
    if (loading) return;
    loading = true;
    
    const url = `<?php echo site_url('api/communities/'); ?>${COMMUNITY_ID}/wall`;
    const params = {
        page: page,
        limit: 10
    };
    
    if (filter !== 'all') {
        params.filter = filter;
    }
    
    if (showHidden) {
        params.include_hidden = 1;
    }
    
    // Show loading
    $('#wall-feed').html(`
        <div class="wall-loading">
            <i class="mdi mdi-loading"></i>
        </div>
    `);
    
    $.ajax({
        url: url,
        method: 'GET',
        data: params,
        dataType: 'json',
        success: function(response) {
            loading = false;
            
            if (response.status && response.data) {
                renderPosts(response.data.posts);
                updatePagination(response.data.pagination);
            } else {
                showEmptyState();
            }
        },
        error: function(xhr, status, error) {
            loading = false;
            console.error('AJAX Error:', status, error);
            console.log('Response:', xhr.responseText);
            
            let errorMessage = '<?php echo get_phrase('error_loading_posts'); ?>';
            
            $('#wall-feed').html(`
                <div class="text-center p-5">
                    <div class="text-danger mb-3">
                        <i class="mdi mdi-alert-circle-outline" style="font-size: 3rem;"></i>
                    </div>
                    <h4><?php echo get_phrase('error_loading_posts'); ?></h4>
                    <p class="text-muted">${error || 'Unknown error'}</p>
                    <div class="text-muted small mb-3">Status: ${xhr.status}</div>
                    <button class="btn btn-primary" onclick="loadPosts(currentPage, currentFilter)">
                        <i class="mdi mdi-refresh"></i> <?php echo get_phrase('retry'); ?>
                    </button>
                </div>
            `);
            
            if (typeof error_notify === 'function') {
                error_notify(errorMessage);
            }
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
    
    $('#wall-feed').html(html);
}

// Build post card HTML
function buildPostCard(post) {
    const isAnnouncement = post.type === 'announcement';
    const isHidden = post.status === 'hidden';
    const dateObj = new Date(post.created_at);
    const day = String(dateObj.getDate()).padStart(2, '0');
    const month = String(dateObj.getMonth() + 1).padStart(2, '0');
    const year = dateObj.getFullYear();
    const hours = String(dateObj.getHours()).padStart(2, '0');
    const minutes = String(dateObj.getMinutes()).padStart(2, '0');
    const postDate = `${day}/${month}/${year} ${hours}:${minutes}`;
    const attachmentsHtml = post.attachments && post.attachments.length > 0 
        ? buildAttachmentsHtml(post.attachments) 
        : '';
    
    const canManage = CAN_MODERATE || (post.author_user_id == CURRENT_USER_ID);

    return `
        <div class="wall-post-card ${isAnnouncement ? 'announcement' : ''}" ${isHidden ? 'style="opacity: 0.8; background-color: #f8fafc; border-style: dashed;"' : ''}>
            <div class="wall-post-header">
                <img src="${post.author_avatar || '<?php echo base_url('uploads/users/placeholder.jpg'); ?>'}" 
                     alt="${post.author_name}" 
                     class="wall-post-avatar">
                <div class="wall-post-info">
                    <div class="wall-post-author">${post.author_name}</div>
                    <div class="wall-post-date">${postDate}</div>
                    ${isAnnouncement 
                        ? '<span class="wall-post-badge announcement"><i class="mdi mdi-bullhorn"></i> <?php echo get_phrase('announcement'); ?></span>'
                        : '<span class="wall-post-badge post"><i class="mdi mdi-forum"></i> <?php echo get_phrase('post'); ?></span>'
                    }
                    ${isHidden ? '<span class="wall-post-badge" style="background: #64748b; color: white; margin-left: 0.5rem;"><i class="mdi mdi-eye-off"></i> <?php echo get_phrase('hidden'); ?></span>' : ''}
                </div>
                ${canManage ? buildPostActions(post) : ''}
            </div>
            
            ${post.title ? `<h3 class="wall-post-title">${escapeHtml(post.title)}</h3>` : ''}
            
            <div class="wall-post-body">
                ${post.body}
            </div>
            
            ${attachmentsHtml}
            
            <div class="wall-post-actions">
                ${post.author_user_id != CURRENT_USER_ID ? `
                <button class="wall-action-btn" onclick="reportPost(${post.id})">
                    <i class="mdi mdi-flag-outline"></i>
                    <?php echo get_phrase('report'); ?>
                </button>
                ` : ''}
            </div>
        </div>
    `;
}

// Build post actions (Edit, Delete, Hide)
function buildPostActions(post) {
    const isHidden = post.status === 'hidden';
    const isAuthor = (post.author_user_id == CURRENT_USER_ID);
    
    let actions = '';
    
    // Edit button (Author or Moderator)
    if ((isAuthor || CAN_MODERATE) && post.type !== 'announcement') {
        actions += `
            <button class="wall-action-btn" onclick="editPost(${post.id})">
                <i class="mdi mdi-pencil-outline"></i>
                <?php echo get_phrase('edit'); ?>
            </button>
        `;
    }

    // Hide/Unhide button (Moderator only)
    if (CAN_MODERATE) {
        if (isHidden) {
            actions += `
                <button class="wall-action-btn" onclick="unhidePost(${post.id})">
                    <i class="mdi mdi-eye-outline"></i>
                    <?php echo get_phrase('unhide'); ?>
                </button>
            `;
        } else {
            actions += `
                <button class="wall-action-btn danger" onclick="hidePost(${post.id})">
                    <i class="mdi mdi-eye-off-outline"></i>
                    <?php echo get_phrase('hide'); ?>
                </button>
            `;
        }
    }
    
    // Delete button (Author or Moderator)
    if (isAuthor || CAN_MODERATE) {
        actions += `
            <button class="wall-action-btn danger" onclick="deletePost(${post.id})">
                <i class="mdi mdi-delete-outline"></i>
                <?php echo get_phrase('delete'); ?>
            </button>
        `;
    }
    
    return actions;
}

// Build attachments HTML
function buildAttachmentsHtml(attachments) {
    let html = '<div class="wall-post-attachments">';
    attachments.forEach(function(att) {
        const isImage = att.mime_type && att.mime_type.startsWith('image/');
        const icon = isImage ? 'mdi-image' : 'mdi-file-document';
        
        html += `
            <a href="<?php echo base_url(); ?>${att.path}" target="_blank" class="wall-attachment">
                <i class="mdi ${icon} wall-attachment-icon"></i>
                <span>${escapeHtml(att.filename)}</span>
            </a>
        `;
    });
    html += '</div>';
    return html;
}

// Show empty state
function showEmptyState() {
    $('#wall-feed').html(`
        <div class="wall-empty">
            <i class="mdi mdi-forum-outline wall-empty-icon"></i>
            <div class="wall-empty-text"><?php echo get_phrase('no_posts_yet'); ?></div>
        </div>
    `);
    $('#load-more-btn').hide();
}

// Update pagination
function updatePagination(pagination) {
    currentPage = pagination.page;
    
    if (pagination.page >= pagination.total_pages) {
        $('#load-more-btn').hide();
    } else {
        $('#load-more-btn').show();
    }
}

// Load more posts
function loadMorePosts() {
    loadPosts(currentPage + 1, currentFilter);
}

// Filter posts
if ($) {
    $(document).ready(function() {
        $('.wall-filter-btn').on('click', function() {
            $('.wall-filter-btn').removeClass('active');
            $(this).addClass('active');
            currentFilter = $(this).data('filter');
            currentPage = 1;
            loadPosts(1, currentFilter);
        });
    });
}

// Post actions
function hidePost(postId) {
    Swal.fire({
        title: '<?php echo get_phrase('confirm_hide_post'); ?>',
        text: '<?php echo get_phrase('this_action_will_hide_the_publication'); ?>',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#f59e0b',
        cancelButtonColor: '#64748b',
        confirmButtonText: '<?php echo get_phrase('yes_hide_it'); ?>',
        cancelButtonText: '<?php echo get_phrase('cancel'); ?>'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `<?php echo site_url('api/posts/'); ?>${postId}/hide`,
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                success: function(response) {
                    if (response.status) {
                        Swal.fire({
                            title: '<?php echo get_phrase('success'); ?>',
                            text: response.message,
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        });
                        loadPosts(currentPage, currentFilter);
                    } else {
                        Swal.fire({
                            title: '<?php echo get_phrase('error'); ?>',
                            text: response.message,
                            icon: 'error'
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        title: '<?php echo get_phrase('error'); ?>',
                        text: '<?php echo get_phrase('an_error_occurred'); ?>',
                        icon: 'error'
                    });
                }
            });
        }
    });
}

function unhidePost(postId) {
    Swal.fire({
        title: '<?php echo get_phrase('confirm_unhide_publication'); ?>',
        text: '<?php echo get_phrase('this_action_will_unhide_the_confirm_unhide_publication'); ?>',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#64748b',
        confirmButtonText: '<?php echo get_phrase('yes_unhide_it'); ?>',
        cancelButtonText: '<?php echo get_phrase('cancel'); ?>'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `<?php echo site_url('api/posts/'); ?>${postId}/unhide`,
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                success: function(response) {
                    if (response.status) {
                        Swal.fire({
                            title: '<?php echo get_phrase('success'); ?>',
                            text: response.message,
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        });
                        loadPosts(currentPage, currentFilter);
                    } else {
                        Swal.fire({
                            title: '<?php echo get_phrase('error'); ?>',
                            text: response.message,
                            icon: 'error'
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        title: '<?php echo get_phrase('error'); ?>',
                        text: '<?php echo get_phrase('an_error_occurred'); ?>',
                        icon: 'error'
                    });
                }
            });
        }
    });
}

function deletePost(postId) {
    Swal.fire({
        title: '<?php echo get_phrase('confirm_delete_post'); ?>',
        text: '<?php echo get_phrase('this_action_cannot_be_undone'); ?>',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: '<?php echo get_phrase('yes_delete_it'); ?>',
        cancelButtonText: '<?php echo get_phrase('cancel'); ?>'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `<?php echo site_url('api/posts/'); ?>${postId}`,
                method: 'DELETE',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                success: function(response) {
                    if (response.status) {
                        Swal.fire({
                            title: '<?php echo get_phrase('success'); ?>',
                            text: response.message,
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        });
                        loadPosts(currentPage, currentFilter);
                    } else {
                        Swal.fire({
                            title: '<?php echo get_phrase('error'); ?>',
                            text: response.message,
                            icon: 'error'
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        title: '<?php echo get_phrase('error'); ?>',
                        text: '<?php echo get_phrase('an_error_occurred'); ?>',
                        icon: 'error'
                    });
                }
            });
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
             Swal.fire({
                title: '<?php echo get_phrase('error'); ?>',
                text: '<?php echo get_phrase('unable_to_load_modal'); ?>',
                icon: 'error'
            });
        }
    }
}

function reportPost(postId) {
    Swal.fire({
        title: '<?php echo get_phrase('report_post'); ?>',
        input: 'text',
        inputLabel: '<?php echo get_phrase('report_reason'); ?>',
        inputPlaceholder: '<?php echo get_phrase('enter_reason_here'); ?>',
        showCancelButton: true,
        confirmButtonText: '<?php echo get_phrase('submit'); ?>',
        cancelButtonText: '<?php echo get_phrase('cancel'); ?>',
        inputValidator: (value) => {
            if (!value) {
                return '<?php echo get_phrase('you_need_to_write_something'); ?>!'
            }
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const reason = result.value;
            $.ajax({
                url: `<?php echo site_url('api/posts/'); ?>${postId}/report`,
                method: 'POST',
                data: { reason: reason.trim() },
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                success: function(response) {
                    if (response.status) {
                        Swal.fire({
                            title: '<?php echo get_phrase('submitted'); ?>',
                            text: response.message,
                            icon: 'success'
                        });
                    } else {
                        Swal.fire({
                            title: '<?php echo get_phrase('error'); ?>',
                            text: response.message,
                            icon: 'error'
                        });
                    }
                }
            });
        }
    });
}
// Show post composer
function showPostComposer() {
    var url = '<?php echo site_url('wall/create_post/'); ?>' + WALL_ID;
    var title = '<i class="mdi mdi-text-box-plus-outline" style="color: #4f46e5; font-size: 1.5rem;"></i> <?php echo get_phrase('create_new_post'); ?>';
    
    if (typeof largeModal === 'function') {
        largeModal(url, title);
    } else {
        console.warn('largeModal is not defined. Using jQuery fallback.');
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
            Swal.fire({
                title: '<?php echo get_phrase('error'); ?>',
                text: '<?php echo get_phrase('unable_to_load_modal'); ?>',
                icon: 'error'
            });
        }
    }
}

// Escape HTML
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
</script>

