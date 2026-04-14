<?php

namespace App\Models;

use CodeIgniter\Model;

class Wall_model extends Model {
    protected $table            = 'posts';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = [];
    protected $useTimestamps    = false;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $deletedField    = 'deleted_at';
    protected $dateFormat       = 'datetime';

    protected $DBGroup = 'default';

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
        $wall = \db()->table('walls')->where('scope_type', $scope_type)->where('scope_id', $scope_id)->get()->getRowArray();

        if ($wall) {
            return $wall;
        }

        try {
            $data = [
                'scope_type' => $scope_type,
                'scope_id' => $scope_id
            ];

            $existing = \db()->table('walls')->where('scope_type', $scope_type)->where('scope_id', $scope_id)->get()->getRowArray();

            if ($existing) {
                return $existing;
            }

            \db()->table('walls')->insert($data);
            $wall_id = \db()->insertID();

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
        return \db()->table('walls')->where('id', $wall_id)->get()->getRowArray();
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
            'type' => null
        ];

        $options = array_merge($defaults, $options);

        $builder = \db()->table('posts p');
        $builder->select('p.*, u.name as author_name, u.email as author_email');
        $builder->join('users u', 'u.id = p.author_user_id', 'left');
        
        $builder->where('p.wall_id', $wall_id);
        $builder->where('p.deleted_at', NULL);

        if (!empty($options['type'])) {
            $builder->where('p.type', $options['type']);
        }
        
        if (!$options['include_hidden']) {
            $builder->where('p.status', 'published');
        }

        $builder->orderBy('p.created_at', 'DESC');

        $offset = ($options['page'] - 1) * $options['limit'];
        $builder->limit($options['limit'], $offset);

        $sql = $builder->getCompiledSelect(false);
        log_message('error', 'Wall_model::get_wall_posts Query: ' . $sql);

        $result = $builder->get()->getResultArray();
        
        log_message('error', 'Wall_model::get_wall_posts Result Count: ' . count($result));
        
        return $result;
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
        $builder = \db()->table('posts');
        $builder->where('wall_id', $wall_id);
        $builder->where('deleted_at', NULL);

        if ($type) {
            $builder->where('type', $type);
        }

        if (!$include_hidden) {
            $builder->where('status', 'published');
        }

        return $builder->countAllResults();
    }

    /**
     * Create a new post
     * 
     * @param array $data ['wall_id', 'author_user_id', 'type', 'title', 'body']
     * @return int|false post_id or false on failure
     */
    public function create_post($data)
    {
        \db()->table('posts')->insert($data);
        return \db()->insertID();
    }

    /**
     * Get post by ID
     * 
     * @param int $post_id
     * @return array|false
     */
    public function get_post_by_id($post_id)
    {
        $builder = \db()->table('posts p');
        $builder->select('p.*, w.scope_type, w.scope_id, u.name as author_name, u.email as author_email');
        $builder->join('walls w', 'w.id = p.wall_id', 'left');
        $builder->join('users u', 'u.id = p.author_user_id', 'left');
        $builder->where('p.id', $post_id);
        return $builder->get()->getRowArray();
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
        return \db()->table('posts')->where('id', $post_id)->update($data);
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
        $result = \db()->table('posts')->where('id', $post_id)->update(['status' => $status]);

        if (\db()->affectedRows() > 0) {
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
        $result = \db()->table('posts')->where('id', $post_id)->update(['deleted_at' => date('Y-m-d H:i:s')]);

        if (\db()->affectedRows() > 0) {
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
        \db()->table('post_attachments')->insert($data);
        return \db()->insertID();
    }

    /**
     * Get post attachments
     * 
     * @param int $post_id
     * @return array
     */
    public function get_post_attachments($post_id)
    {
        return \db()->table('post_attachments')->where('post_id', $post_id)->orderBy('created_at', 'ASC')->get()->getResultArray();
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
        $existing = \db()->table('post_reports')->where('post_id', $post_id)->where('reporter_user_id', $reporter_user_id)->get()->getRowArray();

        if ($existing) {
            return false;
        }

        $data = [
            'post_id' => $post_id,
            'reporter_user_id' => $reporter_user_id,
            'reason' => $reason
        ];

        \db()->table('post_reports')->insert($data);
        return \db()->insertID();
    }

    /**
     * Get post reports
     * 
     * @param int $post_id
     * @return array
     */
    public function get_post_reports($post_id)
    {
        $builder = \db()->table('post_reports pr');
        $builder->select('pr.*, u.name as reporter_name, u.email as reporter_email');
        $builder->join('users u', 'u.id = pr.reporter_user_id', 'left');
        $builder->where('pr.post_id', $post_id);
        $builder->orderBy('pr.created_at', 'DESC');
        return $builder->get()->getResultArray();
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
        $builder = \db()->table('post_reports pr');
        $builder->select('pr.*, p.*, w.scope_type, w.scope_id, u.name as reporter_name, author.name as author_name');
        $builder->join('posts p', 'p.id = pr.post_id', 'left');
        $builder->join('walls w', 'w.id = p.wall_id', 'left');
        $builder->join('users u', 'u.id = pr.reporter_user_id', 'left');
        $builder->join('users author', 'author.id = p.author_user_id', 'left');

        if (!empty($filters['status'])) {
            $builder->where('pr.status', $filters['status']);
        }

        if (!empty($filters['scope_type'])) {
            $builder->where('w.scope_type', $filters['scope_type']);
        }

        if (!empty($filters['scope_id'])) {
            $builder->where('w.scope_id', $filters['scope_id']);
        }

        $builder->orderBy('pr.created_at', 'DESC');

        if (!empty($pagination['limit'])) {
            $page = $pagination['page'] ?? 1;
            $offset = ($page - 1) * $pagination['limit'];
            $builder->limit($pagination['limit'], $offset);
        }

        return $builder->get()->getResultArray();
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

        \db()->table('moderation_actions')->insert($data);
        return \db()->insertID();
    }

    /**
     * Get moderation history for a post
     * 
     * @param int $post_id
     * @return array
     */
    public function get_post_moderation_history($post_id)
    {
        $builder = \db()->table('moderation_actions ma');
        $builder->select('ma.*, u.name as actor_name, u.email as actor_email');
        $builder->join('users u', 'u.id = ma.actor_user_id', 'left');
        $builder->where('ma.post_id', $post_id);
        $builder->orderBy('ma.created_at', 'DESC');
        return $builder->get()->getResultArray();
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
        $builder = \db()->table('posts p');
        $builder->select('p.*, w.scope_type, w.scope_id, u.name as author_name, u.email as author_email, u.photo as author_avatar');
        $builder->join('walls w', 'w.id = p.wall_id', 'left');
        $builder->join('users u', 'u.id = p.author_user_id', 'left');

        $builder->where('p.deleted_at', NULL);

        if (!empty($filters['status'])) {
            $builder->where('p.status', $filters['status']);
        }

        if (!empty($filters['scope_type'])) {
            $builder->where('w.scope_type', $filters['scope_type']);
        }

        if (!empty($filters['scope_id'])) {
            $builder->where('w.scope_id', $filters['scope_id']);
        }

        if (!empty($filters['author_user_id'])) {
            $builder->where('p.author_user_id', $filters['author_user_id']);
        }

        if (!empty($filters['reported']) && $filters['reported'] === true) {
            $builder->where('p.id IN (SELECT post_id FROM post_reports)', NULL, FALSE);
        }

        $builder->orderBy('p.created_at', 'DESC');

        if (!empty($pagination['limit'])) {
            $page = $pagination['page'] ?? 1;
            $offset = ($page - 1) * $pagination['limit'];
            $builder->limit($pagination['limit'], $offset);
        }

        return $builder->get()->getResultArray();
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
        return \db()->table('post_reports')->where('id', $report_id)->update(['status' => $status]) && \db()->affectedRows() > 0;
    }

    /**
     * Delete attachment
     * 
     * @param int $attachment_id
     * @return bool
     */
    public function delete_attachment($attachment_id)
    {
        $attachment = \db()->table('post_attachments')->where('id', $attachment_id)->get()->getRowArray();
        
        if ($attachment && file_exists(FCPATH . $attachment['path'])) {
            unlink(FCPATH . $attachment['path']);
        }

        return \db()->table('post_attachments')->where('id', $attachment_id)->delete()->affectedRows() > 0;
    }

    public function get_school_by_id($school_id) {
        return \db()->table('schools')->where('id', $school_id)->get()->getRowArray();
    }

    public function get_student_by_user_id($user_id) {
        return \db()->table('students')->where('user_id', $user_id)->get()->getRowArray();
    }

    public function get_student_by_user_id_and_school($user_id, $school_id) {
        return \db()->table('students')->where('user_id', $user_id)->where('school_id', $school_id)->get()->getRowArray();
    }

    public function get_class_by_id($class_id) {
        return \db()->table('classes')->where('id', $class_id)->get()->getRowArray();
    }

    public function get_teacher_by_user_id($user_id) {
        return \db()->table('teachers')->where('user_id', $user_id)->get()->getRowArray();
    }

    public function get_classes_by_school($school_id) {
        return \db()->table('classes')->where('school_id', $school_id)->get()->getResultArray();
    }

    public function get_by_query($sql, $params = []) {
        if (!empty($params)) {
            return \db()->query($sql, $params)->getResultArray();
        }
        return \db()->query($sql)->getResultArray();
    }

    public function get_row($table, $where) {
        return \db()->table($table)->where($where)->get()->getRowArray();
    }

    public function get_where_result($table, $where = [], $select = '*') {
        return \db()->table($table)->select($select)->where($where)->get()->getResultArray();
    }

    public function count_all($table, $where = []) {
        $builder = \db()->table($table);
        if (!empty($where)) {
            $builder->where($where);
        }
        return $builder->countAllResults();
    }
}

