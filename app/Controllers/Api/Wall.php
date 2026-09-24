<?php

namespace App\Controllers\api;

use App\Libraries\REST_Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;


require APPPATH . 'Libraries/TokenHandler.php';
require APPPATH . 'Libraries/REST_Controller.php';

/**
 * Wall API Controller
 * 
 * RESTful API endpoints for wall operations
 * 
 * @author Wayo Team
 * @date 2026-02-03
 */
class Wall extends REST_Controller
{
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        
        ini_set('display_errors', 0);
        ini_set('error_reporting', 0);

        $this->loadModel('Wall_model', 'wall_model');
        $this->loadModel('User_model', 'user_model');
        $this->wallauthorization = new \App\Libraries\WallAuthorization();
        
        timezone();
    }

    // ============================================================
    // COMMUNITY WALL ENDPOINTS
    // ============================================================

    /**
     * GET /api/communities/{communityId}/wall
     * Get community wall posts
     */
    public function community_get($community_id)
    {
        // Validate community_id
        if (!is_numeric($community_id)) {
            return $this->response([
                'status' => false,
                'message' => get_phrase('invalid_community_id')
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        // Check authorization
        if (!$this->wallauthorization->can_read_community_wall($community_id)) {
            return $this->response([
                'status' => false,
                'message' => get_phrase('unauthorized')
            ], REST_Controller::HTTP_UNAUTHORIZED);
        }

        // Get or create wall
        $wall = $this->wall_model->get_or_create_wall('community', $community_id);

        if (!$wall) {
            return $this->response([
                'status' => false,
                'message' => get_phrase('failed_to_get_wall')
            ], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
        }

        // Parse pagination parameters
        $page = $this->request->getGet('page') ? (int)$this->request->getGet('page') : 1;
        $limit = $this->request->getGet('limit') ? (int)$this->request->getGet('limit') : 20;
        $include_hidden = $this->request->getGet('include_hidden') === '1';
        $filter = $this->request->getGet('filter');

        // Check if user can view hidden posts
        if ($include_hidden) {
            $can_view_hidden = $this->wallauthorization->is_superadmin();
            
            // Allow community admins to view hidden posts in their community
            if (!$can_view_hidden && $this->wallauthorization->is_admin()) {
                $user = $this->wallauthorization->get_current_user();
                if ($user['school_id'] == $community_id) {
                    $can_view_hidden = true;
                }
            }
            
            if (!$can_view_hidden) {
                $include_hidden = false;
            }
        }

        // Determine post type based on filter
        $post_type = null;
        if ($filter === 'announcements') {
            $post_type = 'announcement';
        } elseif ($filter === 'posts') {
            $post_type = 'post';
        }

        // Get posts
        $posts = $this->wall_model->get_wall_posts($wall['id'], [
            'page' => $page,
            'limit' => $limit,
            'include_hidden' => $include_hidden,
            'type' => $post_type
        ]);

        // Count total
        $total = $this->wall_model->count_wall_posts($wall['id'], $include_hidden, $post_type);

        // Add attachments to posts
        foreach ($posts as &$post) {
            $post['attachments'] = $this->wall_model->get_post_attachments($post['id']);
            $post['reports_count'] = $this->wall_model->count_all('post_reports', [
                'post_id' => $post['id'],
                'status' => 'pending'
            ]);
        }

        return $this->response([
            'status' => true,
            'data' => [
                'wall' => $wall,
                'posts' => $posts,
                'pagination' => [
                    'page' => $page,
                    'limit' => $limit,
                    'total' => $total,
                    'total_pages' => ceil($total / $limit)
                ]
            ]
        ], REST_Controller::HTTP_OK);
    }

    /**
     * POST /api/communities/{communityId}/wall/posts
     * Create post on community wall
     */
    public function community_posts_post($community_id)
    {
        // Check authorization
        if (!$this->wallauthorization->can_post_community_wall($community_id)) {
            $this->response([
                'status' => false,
                'message' => get_phrase('unauthorized')
            ], REST_Controller::HTTP_UNAUTHORIZED);
            return;
        }

        // Validate input
        $body = $this->request->getPost('body');
        if (empty($body)) {
            $this->response([
                'status' => false,
                'message' => get_phrase('post_body_is_required')
            ], REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        // Sanitize HTML
        $body = $this->wallauthorization->sanitize_html($body);

        // Get or create wall
        $wall = $this->wall_model->get_or_create_wall('community', $community_id);

        if (!$wall) {
            $this->response([
                'status' => false,
                'message' => get_phrase('failed_to_get_wall')
            ], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
            return;
        }

        // Create post
        $user_id = session()->get('user_id');
        $post_data = [
            'wall_id' => $wall['id'],
            'author_user_id' => $user_id,
            'type' => 'post',
            'body' => $body,
            'status' => 'published'
        ];

        $post_id = $this->wall_model->create_post($post_data);

        if (!$post_id) {
            $this->response([
                'status' => false,
                'message' => get_phrase('failed_to_create_publication')
            ], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
            return;
        }

        // Handle file attachments
        $attachments = [];
        if (isset($_FILES['attachments'])) {
            $attachments = $this->_handle_attachments($post_id, $_FILES['attachments']);
        }

        // Get created post
        $post = $this->wall_model->get_post_by_id($post_id);

        $this->response([
            'status' => true,
            'message' => get_phrase('publication_created_successfully'),
            'data' => [
                'post' => $post,
                'attachments' => $attachments
            ]
        ], REST_Controller::HTTP_CREATED);
    }

    /**
     * POST /api/communities/{communityId}/announcements
     * Create announcement (extends existing announcement screen)
     */
    public function announcements_post($community_id)
    {
        // Check authorization (Admin only)
        if (!$this->wallauthorization->can_post_community_wall($community_id)) {
            $this->response([
                'status' => false,
                'message' => get_phrase('unauthorized')
            ], REST_Controller::HTTP_UNAUTHORIZED);
            return;
        }

        // Validate input
        $title = $this->request->getPost('title');
        $date = $this->request->getPost('starting_date');
        $content = $this->request->getPost('content');

        if (empty($title) || empty($date) || empty($content)) {
            $this->response([
                'status' => false,
                'message' => get_phrase('title_date_and_content_are_required')
            ], REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        // Sanitize HTML content
        $content = $this->wallauthorization->sanitize_html($content);

        // Get or create wall
        $wall = $this->wall_model->get_or_create_wall('community', $community_id);

        if (!$wall) {
            $this->response([
                'status' => false,
                'message' => get_phrase('failed_to_get_wall')
            ], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
            return;
        }

        // Create announcement post on wall
        $user_id = session()->get('user_id');
        $post_data = [
            'wall_id' => $wall['id'],
            'author_user_id' => $user_id,
            'type' => 'announcement',
            'title' => $title,
            'body' => $content,
            'status' => 'published'
        ];

        $post_id = $this->wall_model->create_post($post_data);

        if (!$post_id) {
            $this->response([
                'status' => false,
                'message' => get_phrase('failed_to_create_announcement')
            ], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
            return;
        }

        // Handle file attachments
        $attachments = [];
        if (isset($_FILES['attachments'])) {
            $attachments = $this->_handle_attachments($post_id, $_FILES['attachments']);
        }

        // Get created post
        $post = $this->wall_model->get_post_by_id($post_id);

        $this->response([
            'status' => true,
            'message' => get_phrase('announcement_created_successfully'),
            'data' => [
                'post' => $post,
                'attachments' => $attachments
            ]
        ], REST_Controller::HTTP_CREATED);
    }

    // ============================================================
    // CLASS WALL ENDPOINTS
    // ============================================================

    /**
     * GET /api/classes/{classId}/wall
     * Get class wall posts
     */
    public function class_get($class_id)
    {
        // Validate class_id
        if (!is_numeric($class_id)) {
            return $this->response([
                'status' => false,
                'message' => get_phrase('invalid_class_id')
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        // Get class to get school_id
        $class = $this->wall_model->get_row('classes', ['id' => $class_id]);

        if (!$class) {
            return $this->response([
                'status' => false,
                'message' => get_phrase('class_not_found')
            ], REST_Controller::HTTP_NOT_FOUND);
        }

        $class_school_id = $class['school_id'];

        // Check authorization
        log_message('error', "DEBUG_WALL: Checking auth for class $class_id in controller");
        if (!$this->wallauthorization->can_read_class_wall($class_id, $class_school_id)) {
            log_message('error', "DEBUG_WALL: Authorization failed for class $class_id");
            return $this->response([
                'status' => false,
                'message' => get_phrase('unauthorized')
            ], REST_Controller::HTTP_UNAUTHORIZED);
        }

        // Get or create wall
        $wall = $this->wall_model->get_or_create_wall('class', $class_id);

        if (!$wall) {
            return $this->response([
                'status' => false,
                'message' => get_phrase('failed_to_get_wall')
            ], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
        }

        // Parse pagination parameters
        $page = $this->request->getGet('page') ? (int)$this->request->getGet('page') : 1;
        $limit = $this->request->getGet('limit') ? (int)$this->request->getGet('limit') : 20;
        $include_hidden = $this->request->getGet('include_hidden') === '1';

        // Check if user can view hidden posts
        if ($include_hidden) {
            $can_view_hidden = $this->wallauthorization->is_superadmin();
            
            // Allow community admins to view hidden posts in their community
            if (!$can_view_hidden && $this->wallauthorization->is_admin()) {
                $user = $this->wallauthorization->get_current_user();
                if ($user['school_id'] == $class_school_id) {
                    $can_view_hidden = true;
                }
            }

            // Allow teachers assigned to the class to view hidden posts
            if (!$can_view_hidden && $this->wallauthorization->is_teacher_of_class($class_id)) {
                $can_view_hidden = true;
            }
            
            if (!$can_view_hidden) {
                $include_hidden = false;
            }
        }

        log_message('error', "DEBUG_WALL: class_get for Wall ID {$wall['id']}, Include Hidden: " . ($include_hidden ? 'Yes' : 'No'));

        // Get posts
        $posts = $this->wall_model->get_wall_posts($wall['id'], [
            'page' => $page,
            'limit' => $limit,
            'include_hidden' => $include_hidden
        ]);

        // Count total
        $total = $this->wall_model->count_wall_posts($wall['id'], $include_hidden);

        // Add attachments to posts
        foreach ($posts as &$post) {
            $post['attachments'] = $this->wall_model->get_post_attachments($post['id']);
            $post['reports_count'] = $this->wall_model->count_all('post_reports', [
                'post_id' => $post['id'],
                'status' => 'pending'
            ]);
        }

        return $this->response([
            'status' => true,
            'data' => [
                'wall' => $wall,
                'class' => $class,
                'posts' => $posts,
                'pagination' => [
                    'page' => $page,
                    'limit' => $limit,
                    'total' => $total,
                    'total_pages' => ceil($total / $limit)
                ]
            ]
        ], REST_Controller::HTTP_OK);
    }

    /**
     * POST /api/classes/{classId}/wall/posts
     * Create post on class wall
     */
    public function class_posts_post($class_id)
    {
        // Get class to get school_id
        $class = $this->wall_model->get_row('classes', ['id' => $class_id]);

        if (!$class) {
            $this->response([
                'status' => false,
                'message' => get_phrase('class_not_found')
            ], REST_Controller::HTTP_NOT_FOUND);
            return;
        }

        $class_school_id = $class['school_id'];

        // Check authorization
        if (!$this->wallauthorization->can_post_class_wall($class_id, $class_school_id)) {
            $this->response([
                'status' => false,
                'message' => 'Unauthorized'
            ], REST_Controller::HTTP_UNAUTHORIZED);
            return;
        }

        // Validate input
        $body = $this->request->getPost('body');
        if (empty($body)) {
            $this->response([
                'status' => false,
                'message' => get_phrase('publication_body_is_required')
            ], REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        // Sanitize HTML
        $body = $this->wallauthorization->sanitize_html($body);

        // Get or create wall
        $wall = $this->wall_model->get_or_create_wall('class', $class_id);

        if (!$wall) {
            $this->response([
                'status' => false,
                'message' => get_phrase('failed_to_get_wall')
            ], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
            return;
        }

        // Create post
        $user_id = session()->get('user_id');
        $post_data = [
            'wall_id' => $wall['id'],
            'author_user_id' => $user_id,
            'type' => 'post',
            'body' => $body,
            'status' => 'published'
        ];

        $post_id = $this->wall_model->create_post($post_data);

        if (!$post_id) {
            $this->response([
                'status' => false,
                'message' => get_phrase('failed_to_create_publication')
            ], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
            return;
        }

        // Handle file attachments
        $attachments = [];
        if (isset($_FILES['attachments'])) {
            $attachments = $this->_handle_attachments($post_id, $_FILES['attachments']);
        }

        // Get created post
        $post = $this->wall_model->get_post_by_id($post_id);

        $this->response([
            'status' => true,
            'message' => get_phrase('publication_created_successfully'),
            'data' => [
                'post' => $post,
                'attachments' => $attachments
            ]
        ], REST_Controller::HTTP_CREATED);
    }

    /**
     * POST /api/posts/{postId}/edit
     * Edit a post
     */
    public function edit_post($post_id)
    {
        // Validate post_id
        if (!is_numeric($post_id)) {
            $this->response([
                'status' => false,
                'message' => 'Invalid post ID'
            ], REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        $user_id = session()->get('user_id');
        $post = $this->wall_model->get_post_by_id($post_id);
        
        if (!$post) {
             $this->response([
                'status' => false,
                'message' => 'Post not found'
            ], REST_Controller::HTTP_NOT_FOUND);
            return;
        }

        $can_moderate = $this->wallauthorization->can_moderate_post($post_id);
        $is_author = ($post['author_user_id'] == $user_id);
        
        if (!$can_moderate['can_moderate'] && !$is_author) {
            $this->response([
                'status' => false,
                'message' => 'Unauthorized'
            ], REST_Controller::HTTP_UNAUTHORIZED);
            return;
        }

        // Validate input
        $body = $this->request->getPost('body');
        $title = $this->request->getPost('title');

        if (empty($body)) {
            $this->response([
                'status' => false,
                'message' => get_phrase('publication_body_is_required')
            ], REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        // Sanitize HTML
        $body = $this->wallauthorization->sanitize_html($body);

        // Update post
        $data = [
            'body' => $body,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if (!empty($title)) {
            $data['title'] = $title;
        }

        $success = $this->wall_model->update_post($post_id, $data);

        if ($success) {
            $this->response([
                'status' => true,
                'message' => get_phrase('publication_updated_successfully'),
                'data' => [
                    'post' => $this->wall_model->get_post_by_id($post_id)
                ]
            ], REST_Controller::HTTP_OK);
        } else {
             $this->response([
                'status' => false,
                'message' => get_phrase('failed_to_update_publication')
            ], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // ============================================================
    // POST MODERATION ENDPOINTS
    // ============================================================

    /**
     * POST /api/posts/{postId}/report
     * Report a post
     */
    public function report_post($post_id)
    {
        // Validate post_id
        if (!is_numeric($post_id)) {
            $this->response([
                'status' => false,
                'message' => get_phrase('invalid_publication_id')
            ], REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        // Check authorization
        $can_report = $this->wallauthorization->can_report_post($post_id);
        if (!$can_report['can_report']) {
            $this->response([
                'status' => false,
                'message' => $can_report['reason']
            ], REST_Controller::HTTP_UNAUTHORIZED);
            return;
        }

        // Validate reason
        $reason = $this->request->getPost('reason');
        if (empty($reason)) {
            $this->response([
                'status' => false,
                'message' => get_phrase('report_reason_is_required')
            ], REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        // Create report
        $user_id = session()->get('user_id');
        $report_id = $this->wall_model->report_post($post_id, $user_id, $reason);

        if (!$report_id) {
            $this->response([
                'status' => false,
                'message' => get_phrase('you_have_already_reported_this_publication')
            ], REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        $this->response([
            'status' => true,
            'message' => get_phrase('publication_reported_successfully'),
            'data' => [
                'report_id' => $report_id
            ]
        ], REST_Controller::HTTP_CREATED);
    }

    /**
     * POST /api/posts/{postId}/hide
     * Hide a post
     */
    public function hide_post($post_id)
    {
        // Validate post_id
        if (!is_numeric($post_id)) {
            $this->response([
                'status' => false,
                'message' => 'Invalid post ID'
            ], REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        // Check authorization
        $can_moderate = $this->wallauthorization->can_moderate_post($post_id);
        if (!$can_moderate['can_moderate']) {
            $this->response([
                'status' => false,
                'message' => $can_moderate['reason']
            ], REST_Controller::HTTP_UNAUTHORIZED);
            return;
        }

        // Hide post
        $user_id = session()->get('user_id');
        $success = $this->wall_model->update_post_status($post_id, 'hidden', $user_id);

        if ($success) {
            $this->response([
                'status' => true,
                'message' => get_phrase('publication_hidden_successfully'),
            ], REST_Controller::HTTP_OK);
        } else {
            $this->response([
                'status' => false,
                'message' => get_phrase('failed_to_hide_publication')
            ], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * POST /api/posts/{postId}/unhide
     * Unhide a post
     */
    public function unhide_post($post_id)
    {
        // Validate post_id
        if (!is_numeric($post_id)) {
            $this->response([
                'status' => false,
                'message' => get_phrase('invalid_publication_id')
            ], REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        // Check authorization
        $can_moderate = $this->wallauthorization->can_moderate_post($post_id);
        if (!$can_moderate['can_moderate']) {
            $this->response([
                'status' => false,
                'message' => $can_moderate['reason']
            ], REST_Controller::HTTP_UNAUTHORIZED);
            return;
        }

        // Unhide post
        $user_id = session()->get('user_id');
        $success = $this->wall_model->update_post_status($post_id, 'published', $user_id);

        if ($success) {
            $this->response([
                'status' => true,
                'message' => get_phrase('publication_unhidden_successfully')
            ], REST_Controller::HTTP_OK);
        } else {
            $this->response([
                'status' => false,
                'message' => get_phrase('failed_to_unhide_publication')
            ], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * DELETE /api/posts/{postId}
     * Delete a post
     */
    public function posts_delete($post_id)
    {
        // Validate post_id
        if (!is_numeric($post_id)) {
            $this->response([
                'status' => false,
                'message' => 'Invalid post ID'
            ], REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        // Check authorization
        $can_moderate = $this->wallauthorization->can_moderate_post($post_id);
        if (!$can_moderate['can_moderate']) {
            $this->response([
                'status' => false,
                'message' => $can_moderate['reason']
            ], REST_Controller::HTTP_UNAUTHORIZED);
            return;
        }

        // Delete post
        $user_id = session()->get('user_id');
        $success = $this->wall_model->delete_post($post_id, $user_id);

        if ($success) {
            $this->response([
                'status' => true,
                'message' => get_phrase('publication_deleted_successfully')
            ], REST_Controller::HTTP_OK);
        } else {
            $this->response([
                'status' => false,
                'message' => get_phrase('failed_to_delete_publication')
            ], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // ============================================================
    // MODERATION DASHBOARD ENDPOINTS
    // ============================================================

    /**
     * GET /api/moderation/posts
     * Get all posts for SuperAdmin moderation
     */
    public function moderation_posts_get()
    {
        // Only SuperAdmin
        if (!$this->wallauthorization->is_superadmin()) {
            $this->response([
                'status' => false,
                'message' => get_phrase('unauthorized')
            ], REST_Controller::HTTP_UNAUTHORIZED);
            return;
        }

        // Parse filters
        $filters = [];
        $filters['status'] = $this->request->getGet('status');
        $filters['scope_type'] = $this->request->getGet('scope_type');
        $filters['scope_id'] = esc($this->request->getGet('scope_id')) ?? null;
        $filters['author_user_id'] = esc($this->request->getGet('author_user_id')) ?? null;
        $filters['reported'] = $this->request->getGet('reported') === '1';

        // Parse pagination
        $pagination = [];
        $pagination['page'] = $this->request->getGet('page') ? (int)$this->request->getGet('page') : 1;
        $pagination['limit'] = $this->request->getGet('limit') ? (int)$this->request->getGet('limit') : 50;

        // Get posts
        $posts = $this->wall_model->get_all_posts($filters, $pagination);

        // Add attachments and report counts
        foreach ($posts as &$post) {
            $post['attachments'] = $this->wall_model->get_post_attachments($post['id']);
            $post['reports_count'] = $this->wall_model->count_all('post_reports', [
                'post_id' => $post['id'],
                'status' => 'pending'
            ]);
            $post['reports'] = $this->wall_model->get_post_reports($post['id']);
            $post['moderation_history'] = $this->wall_model->get_post_moderation_history($post['id']);
        }

        $this->response([
            'status' => true,
            'data' => [
                'posts' => $posts,
                'filters' => $filters,
                'pagination' => $pagination
            ]
        ], REST_Controller::HTTP_OK);
    }

    /**
     * GET /api/moderation/reports
     * Get all reports
     */
    public function reports_get()
    {
        // Only SuperAdmin
        if (!$this->wallauthorization->is_superadmin()) {
            $this->response([
                'status' => false,
                'message' => get_phrase('unauthorized')
            ], REST_Controller::HTTP_UNAUTHORIZED);
            return;
        }

        // Parse filters
        $filters = [];
        $filters['status'] = $this->request->getGet('status');

        // Parse pagination
        $pagination = [];
        $pagination['page'] = $this->request->getGet('page') ? (int)$this->request->getGet('page') : 1;
        $pagination['limit'] = $this->request->getGet('limit') ? (int)$this->request->getGet('limit') : 50;

        // Get reports
        $reports = $this->wall_model->get_all_reports($filters, $pagination);

        $this->response([
            'status' => true,
            'data' => [
                'reports' => $reports,
                'filters' => $filters,
                'pagination' => $pagination
            ]
        ], REST_Controller::HTTP_OK);
    }

    /**
     * PUT /api/reports/{reportId}/status
     * Update report status
     */
    public function report_status_put($report_id)
    {
        // Only SuperAdmin
        if (!$this->wallauthorization->is_superadmin()) {
            $this->response([
                'status' => false,
                'message' => get_phrase('unauthorized')
            ], REST_Controller::HTTP_UNAUTHORIZED);
            return;
        }

        // Validate status
        $json = $this->request->getJSON(true);
        $status = is_array($json) ? ($json['status'] ?? null) : null;
        if (!in_array($status, ['resolved', 'dismissed'])) {
            $this->response([
                'status' => false,
                'message' => get_phrase('invalid_report_status')
            ], REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        // Update report
        $success = $this->wall_model->update_report_status($report_id, $status);

        if ($success) {
            $this->response([
                'status' => true,
                'message' => get_phrase('report_updated_successfully')
            ], REST_Controller::HTTP_OK);
        } else {
            $this->response([
                'status' => false,
                'message' => get_phrase('failed_to_update_report')
            ], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // ============================================================
    // HELPER METHODS
    // ============================================================

    /**
     * Handle file attachments
     * 
     * @param int $post_id
     * @param array $files $_FILES['attachments']
     * @return array
     */
    private function _handle_attachments($post_id, $files)
    {
        $attachments = [];
        $upload_dir = FCPATH . 'uploads/wall_attachments/';

        // Create directory if not exists
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        // Allowed mime types
        $allowed_mimes = [
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/webp',
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        ];

        // Max file size (10MB)
        $max_size = 10 * 1024 * 1024;

        // Handle multiple files
        $files_array = [];
        if (isset($files['name']) && is_array($files['name'])) {
            for ($i = 0; $i < count($files['name']); $i++) {
                $files_array[] = [
                    'name' => $files['name'][$i],
                    'type' => $files['type'][$i],
                    'tmp_name' => $files['tmp_name'][$i],
                    'error' => $files['error'][$i],
                    'size' => $files['size'][$i]
                ];
            }
        } else {
            $files_array[] = $files;
        }

        foreach ($files_array as $file) {
            if ($file['error'] !== UPLOAD_ERR_OK) {
                continue;
            }

            // Validate file size
            if ($file['size'] > $max_size) {
                continue;
            }

            // Validate mime type
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);

            if (!in_array($mime, $allowed_mimes)) {
                continue;
            }

            // Generate unique filename
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = md5(uniqid($post_id . '_', true)) . '.' . $extension;
            $path = 'uploads/wall_attachments/' . $filename;

            // Move file
            if (move_uploaded_file($file['tmp_name'], FCPATH . $path)) {
                $attachment_data = [
                    'post_id' => $post_id,
                    'path' => $path,
                    'filename' => $file['name'],
                    'mime_type' => $mime,
                    'size' => $file['size']
                ];

                $attachment_id = $this->wall_model->add_attachment($attachment_data);

                if ($attachment_id) {
                    $attachments[] = array_merge($attachment_data, ['id' => $attachment_id]);
                }
            }
        }

        return $attachments;
    }
}

