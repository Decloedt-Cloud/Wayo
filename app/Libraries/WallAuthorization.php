<?php

namespace App\Libraries;
use Config\Database;
use Config\Services;

/**
 * Wall Authorization Library
 * 
 * Centralized authorization service for Wayo Walls V1
 * Handles all permission checks for walls, posts, and moderation
 * 
 * @author Wayo Team
 * @date 2026-02-03
 */
class WallAuthorization
{
    protected $db;
    protected $current_user;
    protected $current_role;
    protected $current_school_id;

    public function __construct()
    {
        $session = Services::session();
        $this->db = Database::connect();
        
        $this->current_user = $session->get('user_id');
        $this->current_role = $session->get('role');
        $this->current_school_id = $session->get('school_id');
    }

    /**
     * Check if user can read community wall
     * 
     * @param int $community_id
     * @return bool
     */
    public function can_read_community_wall($community_id)
    {
        // SuperAdmin can read everything
        if ($this->current_role === 'superadmin') {
            return true;
        }

        // Community users can read community wall
        // Check if user belongs to the community
        if ($this->is_community_user($community_id)) {
            return true;
        }

        return false;
    }

    /**
     * Check if user can post to community wall
     * 
     * @param int $community_id
     * @return bool
     */
    public function can_post_community_wall($community_id)
    {
        // Only Admin of community can post
        if ($this->current_role === 'admin' && $this->current_school_id == $community_id) {
            return true;
        }

        return false;
    }

    /**
     * Check if user can read class wall
     * 
     * @param int $class_id
     * @param int $class_school_id The school_id this class belongs to
     * @return bool
     */
    public function can_read_class_wall($class_id, $class_school_id)
    {
        $user_str = is_array($this->current_user) ? json_encode($this->current_user) : $this->current_user;
        log_message('error', "DEBUG_WALL: User {$user_str}, Role {$this->current_role}, Class {$class_id}, School {$class_school_id}");

        // SuperAdmin can read everything
        if ($this->current_role === 'superadmin') {
            return true;
        }

        // Admin of community can read all class walls in their community
        if ($this->current_role === 'admin' && $this->current_school_id == $class_school_id) {
            return true;
        }

        // Teacher/Mentor can read if they are assigned to the class
        if ($this->current_role === 'teacher') {
            if ($this->is_teacher_of_class($class_id)) {
                return true;
            }
        }

       // Members who are enrolled in the class OR belong to the same school
        if (($this->current_role === 'member' || $this->current_role === 'student')) {
            // Check enrollment first
            if ($this->is_class_member($class_id)) {
                return true;
            }
            
            // Allow if in the same school (Relaxed permission for viewing)
            if ($this->current_school_id == $class_school_id) {
                return true;
            }
        return false;
        }}

    /**
     * Check if user can post to class wall
     * 
     * @param int $class_id
     * @param int $class_school_id The school_id this class belongs to
     * @return bool
     */
    public function can_post_class_wall($class_id, $class_school_id)
    {
        // Members cannot post
        if ($this->current_role === 'member' || $this->current_role === 'student') {
            return false;
        }

        // SuperAdmin can post (though typically won't)
        if ($this->current_role === 'superadmin') {
            return true;
        }

        // Admin of community can always post to class walls in their community
        if ($this->current_role === 'admin' && $this->current_school_id == $class_school_id) {
            return true;
        }

        // Teacher/Mentor can post if they are assigned to the class
        if ($this->current_role === 'teacher') {
            if ($this->is_teacher_of_class($class_id)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if current user is a teacher assigned to the class
     * 
     * @param int $class_id
     * @return bool
      */
    public function is_teacher_of_class($class_id)
    {
        $teacher = $this->db->table('teachers')->where('user_id', $this->current_user)->get()->getRowArray();
        
        if (!$teacher) {
            return false;
        }

        $builder = $this->db->table('teacher_permissions');
        $builder->where('teacher_id', $teacher['id']);
        $builder->where('class_id', $class_id);
        
        $builder->groupStart();
        $builder->where('marks', 1);
        $builder->orWhere('attendance', 1);
        $builder->orWhere('assignment', 1);
        $builder->groupEnd();
        
        $query = $builder->get();

        return $query->getNumRows() > 0;
    }

    /**
     * Check if user can moderate a post
     * 
     * @param int $post_id
     * @return array ['can_moderate' => bool, 'reason' => string]
     */
    public function can_moderate_post($post_id)
    {
        // SuperAdmin can moderate everything
        if ($this->current_role === 'superadmin') {
            return ['can_moderate' => true];
        }

        // Get post details to check scope
        $builder = $this->db->table('posts p');
        $builder->select('p.*, w.scope_type, w.scope_id');
        $builder->join('walls w', 'w.id = p.wall_id', 'left');
        $builder->where('p.id', $post_id);
        $post = $builder->get()->getRowArray();

        if (!$post) {
            return ['can_moderate' => false, 'reason' => 'post_not_found'];
        }

        // Author can always moderate (delete/hide) their own post
        if ($post['author_user_id'] == $this->current_user) {
            return ['can_moderate' => true];
        }

        // Admin of community can moderate posts in their community
        if ($this->current_role === 'admin') {
            // For community wall posts
            if ($post['scope_type'] === 'community' && $post['scope_id'] == $this->current_school_id) {
                return ['can_moderate' => true];
            }
            
            // For class wall posts - Admin can ONLY moderate their own posts (already checked above)
            // So we do NOT return true here for class wall posts
        }

        return ['can_moderate' => false, 'reason' => 'insufficient_permissions'];
    }

    /**
     * Check if user can report a post
     * 
     * @param int $post_id
     * @return array ['can_report' => bool, 'reason' => string]
     */
    public function can_report_post($post_id)
    {
        // SuperAdmin can report
        if ($this->current_role === 'superadmin') {
            return ['can_report' => true];
        }

        // Get post details
        $builder = $this->db->table('posts p');
        $builder->select('p.*, w.scope_type, w.scope_id');
        $builder->join('walls w', 'w.id = p.wall_id', 'left');
        $builder->where('p.id', $post_id);
        $post = $builder->get()->getRowArray();

        if (!$post) {
            return ['can_report' => false, 'reason' => 'post_not_found'];
        }

        // Users who can read the wall can report
        if ($post['scope_type'] === 'community') {
            if ($this->can_read_community_wall($post['scope_id'])) {
                return ['can_report' => true];
            }
        } elseif ($post['scope_type'] === 'class') {
            // Get class school_id
            $class = $this->db->table('classes')->select('school_id')->where('id', $post['scope_id'])->get()->getRowArray();
            
            if ($class && $this->can_read_class_wall($post['scope_id'], $class['school_id'])) {
                return ['can_report' => true];
            }
        }

        return ['can_report' => false, 'reason' => 'cannot_read_post'];
    }

  
    /**
     * Check if user is a community user
     * 
     * @param int $community_id (school_id)
     * @return bool
     */
    protected function is_community_user($community_id)
    {
        $user = $this->db->table('users')->where('school_id', $community_id)->where('id', $this->current_user)->get()->getRowArray();
        
        return $user !== null;
    }

    /**
     * Check if member is enrolled in class
     * 
     * @param int $class_id
     * @return bool
     */
    protected function is_class_member($class_id)
    {
        // Force debug off to prevent HTML leaks
        $original_debug = $this->db->db_debug;
        $this->db->db_debug = FALSE;

        try {
            $builder = $this->db->table('enrols e');
            $builder->select('e.id');
            $builder->join('students s', 's.id = e.student_id', 'left');
            $builder->join('users u', 'u.id = s.user_id', 'left');
            $builder->where('u.id', $this->current_user);
            $builder->where('e.class_id', $class_id);
            $builder->limit(1);
            
            if ($builder->get()->getNumRows() > 0) {
                return true;
            }

            $builder = $this->db->table('invoices i');
            $builder->select('i.id');
            $builder->join('students s', 's.id = i.student_id', 'left');
            $builder->join('users u', 'u.id = s.user_id', 'left');
            $builder->where('u.id', $this->current_user);
            $builder->where('i.class_id', $class_id);
            $builder->groupStart();
            $builder->where('i.status', 'paid');
            $builder->orWhere('i.status', 'Paid');
            $builder->orWhere('i.status', 'PAID');
            $builder->groupEnd();
            $builder->limit(1);

            $result = $builder->get()->getNumRows() > 0;
            
            // Log if result is false to help debugging
            if (!$result) {
                log_message('error', "DEBUG_WALL: Invoice check FAILED for user {$this->current_user}, class {$class_id}");
            } else {
                log_message('error', "DEBUG_WALL: Invoice check SUCCESS for user {$this->current_user}, class {$class_id}");
            }
            
            $this->db->db_debug = $original_debug;
            return $result;
            
        } catch (Exception $e) {
            log_message('error', "DEBUG_WALL: Exception in is_class_member: " . $e->getMessage());
            $this->db->reset_query();
            $this->db->db_debug = $original_debug;
            return false;
        }
    }

    /**
     * Get current user info
     * 
     * @return array
     */
    public function get_current_user()
    {
        return [
            'user_id' => $this->current_user,
            'role' => $this->current_role,
            'school_id' => $this->current_school_id
        ];
    }

    /**
     * Check if current user is SuperAdmin
     * 
     * @return bool
     */
    public function is_superadmin()
    {
        return $this->current_role === 'superadmin';
    }

    /**
     * Check if current user is Admin
     * 
     * @return bool
     */
    public function is_admin()
    {
        return $this->current_role === 'admin';
    }

    /**
     * Check if current user is Mentor
     * 
     * @return bool
     */
    public function is_mentor()
    {
        return $this->current_role === 'mentor' || $this->current_role === 'teacher';
    }

    /**
     * Check if current user is Member
     * 
     * @return bool
     */
    public function is_member()
    {
        return $this->current_role === 'member' || $this->current_role === 'student';
    }

    /**
     * Check if current user can access all walls (SuperAdmin)
     * 
     * @return bool
     */
    public function can_access_all_walls()
    {
        return $this->current_role === 'superadmin';
    }

    /**
     * Sanitize HTML content
     * 
     * @param string $html
     * @return string
     */
    public function sanitize_html($html)
    {
        // Basic HTML sanitization - remove script tags and dangerous attributes
        $html = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $html);
        $html = preg_replace('/on\w+="[^"]*"/i', '', $html);
        $html = preg_replace('/on\w+=\'[^\']*\'/i', '', $html);
        
        return $html;
    }
}

