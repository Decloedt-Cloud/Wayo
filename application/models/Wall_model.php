<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Wall Model
 * 
 * Handles all wall-related database operations for Wayo Walls V1
 * 
 * @author Wayo Team
 * @date 2026-02-03
 */

class Wall_model extends CI_Model
{
    protected $school_id;
    protected $active_session;

    public function __construct()
    {
        parent::__construct();
        $this->school_id = school_id();
        $this->active_session = active_session();
    }

    /**
     * Get or create wall for a scope (community or class)
     * Uses lazy creation with concurrency safety
     * 
     * @param string $scope_type 'community' or 'class'
     * @param int $scope_id community_id or class_id
     * @return array|false Wall data or false on failure
     */
    public function get_or_create_wall($scope_type, $scope_id)
    {
        // Try to get existing wall
        $this->db->where('scope_type', $scope_type);
        $this->db->where('scope_id', $scope_id);
        $wall = $this->db->get('walls')->row_array();

        if ($wall) {
            return $wall;
        }

        // Lazy creation with retry on duplicate
        try {
            $data = [
                'scope_type' => $scope_type,
                'scope_id' => $scope_id
            ];

            // Check for race condition
            $this->db->where('scope_type', $scope_type);
            $this->db->where('scope_id', $scope_id);
            $existing = $this->db->get('walls')->row_array();

            if ($existing) {
                return $existing;
            }

            // Insert new wall
            $this->db->insert('walls', $data);
            $wall_id = $this->db->insert_id();

            if ($wall_id) {
                $data['id'] = $wall_id;
                $data['created_at'] = date('Y-m-d H:i:s');
                return $data;
            }
        } catch (Exception $e) {
            log_message('error', 'Wall creation error: ' . $e->getMessage());
        }

        return false;
    }

    /**
     * Get wall by ID
     * 
     * @param int $wall_id
     * @return array|false
     */
    public function get_wall_by_id($wall_id)
    {
        $this->db->where('id', $wall_id);
        return $this->db->get('walls')->row_array();
    }

    /**
     * Get wall posts with pagination
     * 
     * @param int $wall_id
     * @param array $options ['include_hidden' => bool, 'page' => int, 'limit' => int]
     * @return array
     */
    public function get_wall_posts($wall_id, $options = [])
    {
        $defaults = [
            'include_hidden' => false,
            'page' => 1,
            'limit' => 20,
            'user_id' => null,
            'type' => null
        ];

        $options = array_merge($defaults, $options);

        $this->db->select('p.*, u.name as author_name, u.email as author_email');
        $this->db->from('posts p');
        $this->db->join('users u', 'u.id = p.author_user_id', 'left');
        $this->db->where('p.wall_id', $wall_id);
        $this->db->where('p.deleted_at', NULL);

        // Filter by post type if specified
        if (!empty($options['type'])) {
            $this->db->where('p.type', $options['type']);
        }
        
        // Debug Query
        log_message('error', 'Wall_model::get_wall_posts Query: ' . $this->db->get_compiled_select('', false));

        // Hide hidden posts unless explicitly requested and user has permission
        if (!$options['include_hidden']) {
            $this->db->where('p.status', 'published');
        }

        $this->db->order_by('p.created_at', 'DESC');

        // Pagination
        $offset = ($options['page'] - 1) * $options['limit'];
        $this->db->limit($options['limit'], $offset);

        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Count total posts for a wall
     * 
     * @param int $wall_id
     * @param bool $include_hidden
     * @param string|null $type
     * @return int
     */
    public function count_wall_posts($wall_id, $include_hidden = false, $type = null)
    {
        $this->db->from('posts');
        $this->db->where('wall_id', $wall_id);
        $this->db->where('deleted_at', NULL);

        if ($type) {
            $this->db->where('type', $type);
        }

        if (!$include_hidden) {
            $this->db->where('status', 'published');
        }

        return $this->db->count_all_results();
    }

    /**
     * Create a new post
     * 
     * @param array $data ['wall_id', 'author_user_id', 'type', 'title', 'body']
     * @return int|false post_id or false on failure
     */
    public function create_post($data)
    {
        $this->db->insert('posts', $data);
        return $this->db->insert_id();
    }

    /**
     * Get post by ID
     * 
     * @param int $post_id
     * @return array|false
     */
    public function get_post_by_id($post_id)
    {
        $this->db->select('p.*, w.scope_type, w.scope_id, u.name as author_name, u.email as author_email');
        $this->db->from('posts p');
        $this->db->join('walls w', 'w.id = p.wall_id', 'left');
        $this->db->join('users u', 'u.id = p.author_user_id', 'left');
        $this->db->where('p.id', $post_id);
        return $this->db->get()->row_array();
    }

    /**
     * Update post data
     * 
     * @param int $post_id
     * @param array $data
     * @return bool
     */
    public function update_post($post_id, $data)
    {
        $this->db->where('id', $post_id);
        $this->db->update('posts', $data);
        return true;
    }

    /**
     * Update post status (hide/unhide)
     * 
     * @param int $post_id
     * @param string $status 'published' or 'hidden'
     * @param int $actor_user_id
     * @return bool
     */
    public function update_post_status($post_id, $status, $actor_user_id)
    {
        $this->db->where('id', $post_id);
        $this->db->update('posts', ['status' => $status]);

        // Log moderation action
        if ($this->db->affected_rows() > 0) {
            $this->log_moderation_action($post_id, $actor_user_id, ($status === 'hidden' ? 'hide' : 'unhide'));
            return true;
        }

        return false;
    }

    /**
     * Soft delete a post
     * 
     * @param int $post_id
     * @param int $actor_user_id
     * @return bool
     */
    public function delete_post($post_id, $actor_user_id)
    {
        $this->db->where('id', $post_id);
        $this->db->update('posts', ['deleted_at' => date('Y-m-d H:i:s')]);

        // Log moderation action
        if ($this->db->affected_rows() > 0) {
            $this->log_moderation_action($post_id, $actor_user_id, 'delete');
            return true;
        }

        return false;
    }

    /**
     * Add attachment to post
     * 
     * @param array $data ['post_id', 'path', 'filename', 'mime_type', 'size']
     * @return int|false attachment_id or false on failure
     */
    public function add_attachment($data)
    {
        $this->db->insert('post_attachments', $data);
        return $this->db->insert_id();
    }

    /**
     * Get post attachments
     * 
     * @param int $post_id
     * @return array
     */
    public function get_post_attachments($post_id)
    {
        $this->db->where('post_id', $post_id);
        $this->db->order_by('created_at', 'ASC');
        return $this->db->get('post_attachments')->result_array();
    }

    /**
     * Report a post
     * 
     * @param int $post_id
     * @param int $reporter_user_id
     * @param string $reason
     * @return int|false report_id or false on failure
     */
    public function report_post($post_id, $reporter_user_id, $reason)
    {
        // Check if already reported by this user
        $this->db->where('post_id', $post_id);
        $this->db->where('reporter_user_id', $reporter_user_id);
        $existing = $this->db->get('post_reports')->row_array();

        if ($existing) {
            return false; // Already reported
        }

        $data = [
            'post_id' => $post_id,
            'reporter_user_id' => $reporter_user_id,
            'reason' => $reason
        ];

        $this->db->insert('post_reports', $data);
        return $this->db->insert_id();
    }

    /**
     * Get post reports
     * 
     * @param int $post_id
     * @return array
     */
    public function get_post_reports($post_id)
    {
        $this->db->select('pr.*, u.name as reporter_name, u.email as reporter_email');
        $this->db->from('post_reports pr');
        $this->db->join('users u', 'u.id = pr.reporter_user_id', 'left');
        $this->db->where('pr.post_id', $post_id);
        $this->db->order_by('pr.created_at', 'DESC');
        return $this->db->get()->result_array();
    }

    /**
     * Get all reports (for moderation)
     * 
     * @param array $filters ['status' => string, 'scope_type' => string, 'scope_id' => int]
     * @param array $pagination ['page' => int, 'limit' => int]
     * @return array
     */
    public function get_all_reports($filters = [], $pagination = [])
    {
        $this->db->select('pr.*, p.*, w.scope_type, w.scope_id, u.name as reporter_name, author.name as author_name');
        $this->db->from('post_reports pr');
        $this->db->join('posts p', 'p.id = pr.post_id', 'left');
        $this->db->join('walls w', 'w.id = p.wall_id', 'left');
        $this->db->join('users u', 'u.id = pr.reporter_user_id', 'left');
        $this->db->join('users author', 'author.id = p.author_user_id', 'left');

        // Apply filters
        if (!empty($filters['status'])) {
            $this->db->where('pr.status', $filters['status']);
        }

        if (!empty($filters['scope_type'])) {
            $this->db->where('w.scope_type', $filters['scope_type']);
        }

        if (!empty($filters['scope_id'])) {
            $this->db->where('w.scope_id', $filters['scope_id']);
        }

        $this->db->order_by('pr.created_at', 'DESC');

        // Pagination
        if (!empty($pagination['limit'])) {
            $page = $pagination['page'] ?? 1;
            $offset = ($page - 1) * $pagination['limit'];
            $this->db->limit($pagination['limit'], $offset);
        }

        return $this->db->get()->result_array();
    }

    /**
     * Log moderation action
     * 
     * @param int $post_id
     * @param int $actor_user_id
     * @param string $action 'hide', 'unhide', or 'delete'
     * @param string $note Optional note
     * @return int|false
     */
    public function log_moderation_action($post_id, $actor_user_id, $action, $note = null)
    {
        $data = [
            'post_id' => $post_id,
            'actor_user_id' => $actor_user_id,
            'action' => $action,
            'note' => $note
        ];

        $this->db->insert('moderation_actions', $data);
        return $this->db->insert_id();
    }

    /**
     * Get moderation history for a post
     * 
     * @param int $post_id
     * @return array
     */
    public function get_post_moderation_history($post_id)
    {
        $this->db->select('ma.*, u.name as actor_name, u.email as actor_email');
        $this->db->from('moderation_actions ma');
        $this->db->join('users u', 'u.id = ma.actor_user_id', 'left');
        $this->db->where('ma.post_id', $post_id);
        $this->db->order_by('ma.created_at', 'DESC');
        return $this->db->get()->result_array();
    }

    /**
     * Get all posts across all walls (for SuperAdmin moderation)
     * 
     * @param array $filters ['status' => string, 'scope_type' => string, 'scope_id' => int, 'author_user_id' => int, 'reported' => bool]
     * @param array $pagination ['page' => int, 'limit' => int]
     * @return array
     */
    public function get_all_posts($filters = [], $pagination = [])
    {
        $this->db->select('p.*, w.scope_type, w.scope_id, u.name as author_name, u.email as author_email, u.photo as author_avatar');
        $this->db->from('posts p');
        $this->db->join('walls w', 'w.id = p.wall_id', 'left');
        $this->db->join('users u', 'u.id = p.author_user_id', 'left');

        // Soft delete filter
        $this->db->where('p.deleted_at', NULL);

        // Apply filters
        if (!empty($filters['status'])) {
            $this->db->where('p.status', $filters['status']);
        }

        if (!empty($filters['scope_type'])) {
            $this->db->where('w.scope_type', $filters['scope_type']);
        }

        if (!empty($filters['scope_id'])) {
            $this->db->where('w.scope_id', $filters['scope_id']);
        }

        if (!empty($filters['author_user_id'])) {
            $this->db->where('p.author_user_id', $filters['author_user_id']);
        }

        // Filter by reported posts
        if (!empty($filters['reported']) && $filters['reported'] === true) {
            $this->db->where('p.id IN (SELECT post_id FROM post_reports)', NULL, FALSE);
        }

        $this->db->order_by('p.created_at', 'DESC');

        // Pagination
        if (!empty($pagination['limit'])) {
            $page = $pagination['page'] ?? 1;
            $offset = ($page - 1) * $pagination['limit'];
            $this->db->limit($pagination['limit'], $offset);
        }

        return $this->db->get()->result_array();
    }

    /**
     * Update report status
     * 
     * @param int $report_id
     * @param string $status 'resolved' or 'dismissed'
     * @return bool
     */
    public function update_report_status($report_id, $status)
    {
        $this->db->where('id', $report_id);
        $this->db->update('post_reports', ['status' => $status]);
        return $this->db->affected_rows() > 0;
    }

    /**
     * Delete attachment
     * 
     * @param int $attachment_id
     * @return bool
     */
    public function delete_attachment($attachment_id)
    {
        // Get attachment path for file deletion
        $attachment = $this->db->get_where('post_attachments', ['id' => $attachment_id])->row_array();
        
        if ($attachment && file_exists(FCPATH . $attachment['path'])) {
            unlink(FCPATH . $attachment['path']);
        }

        // Delete from database
        $this->db->where('id', $attachment_id);
        $this->db->delete('post_attachments');
        
        return $this->db->affected_rows() > 0;
    }
}

