<?php
defined('BASEPATH') or exit('No direct script access allowed');

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
    protected $ci;
    protected $current_user;
    protected $current_role;
    protected $current_school_id;

    public function __construct()
    {
        $this->ci =& get_instance();
        $this->ci->load->database();
        $this->ci->load->library('session');
        
        $this->current_user = $this->ci->session->userdata('user_id');
        $this->current_role = $this->ci->session->userdata('role');
        $this->current_school_id = $this->ci->session->userdata('school_id');
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

        return false;
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
        $this->ci->db->select('p.*, w.scope_type, w.scope_id');
        $this->ci->db->from('posts p');
        $this->ci->db->join('walls w', 'w.id = p.wall_id', 'left');
        $this->ci->db->where('p.id', $post_id);
        $post = $this->ci->db->get()->row_array();

        if (!$post) {
            return ['can_moderate' => false, 'reason' => 'post_not_found'];
        }

        // Admin of community can moderate posts in their community
        if ($this->current_role === 'admin') {
            // For community wall posts
            if ($post['scope_type'] === 'community' && $post['scope_id'] == $this->current_school_id) {
                return ['can_moderate' => true];
            }
            
            // For class wall posts - check if class belongs to admin's community
            $this->ci->db->select('school_id');
            $this->ci->db->where('id', $post['scope_id']);
            $class = $this->ci->db->get('classes')->row_array();
            
            if ($class && $class['school_id'] == $this->current_school_id) {
                return ['can_moderate' => true];
            }
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
        $this->ci->db->select('p.*, w.scope_type, w.scope_id');
        $this->ci->db->from('posts p');
        $this->ci->db->join('walls w', 'w.id = p.wall_id', 'left');
        $this->ci->db->where('p.id', $post_id);
        $post = $this->ci->db->get()->row_array();

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
            $this->ci->db->select('school_id');
            $this->ci->db->where('id', $post['scope_id']);
            $class = $this->ci->db->get('classes')->row_array();
            
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
        $this->ci->db->where('school_id', $community_id);
        $this->ci->db->where('id', $this->current_user);
        
        $user = $this->ci->db->get('users')->row_array();
        
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
        $original_debug = $this->ci->db->db_debug;
        $this->ci->db->db_debug = FALSE;

        try {
            // Safety: Clear any previous query state
            $this->ci->db->reset_query();

            // Check 1: Enrollment
            $this->ci->db->select('e.id');
            $this->ci->db->from('enrols e');
            $this->ci->db->join('students s', 's.id = e.student_id', 'left');
            $this->ci->db->join('users u', 'u.id = s.user_id', 'left');
            $this->ci->db->where('u.id', $this->current_user);
            $this->ci->db->where('e.class_id', $class_id);
            $this->ci->db->limit(1);
            
            if ($this->ci->db->get()->num_rows() > 0) {
                $this->ci->db->db_debug = $original_debug;
                return true;
            }

            // Safety: Clear state before next check
            $this->ci->db->reset_query();

            // Check 2: Paid Invoice (Purchased Course)
            $this->ci->db->select('i.id');
            $this->ci->db->from('invoices i');
            $this->ci->db->join('students s', 's.id = i.student_id', 'left');
            $this->ci->db->join('users u', 'u.id = s.user_id', 'left');
            $this->ci->db->where('u.id', $this->current_user);
            $this->ci->db->where('i.class_id', $class_id);
            // Check for various forms of 'paid' status
            $this->ci->db->group_start();
            $this->ci->db->where('i.status', 'paid');
            $this->ci->db->or_where('i.status', 'Paid');
            $this->ci->db->or_where('i.status', 'PAID');
            $this->ci->db->group_end();
            $this->ci->db->limit(1);

            $result = $this->ci->db->get()->num_rows() > 0;
            
            // Log if result is false to help debugging
            if (!$result) {
                log_message('error', "DEBUG_WALL: Invoice check FAILED for user {$this->current_user}, class {$class_id}");
            } else {
                log_message('error', "DEBUG_WALL: Invoice check SUCCESS for user {$this->current_user}, class {$class_id}");
            }
            
            $this->ci->db->db_debug = $original_debug;
            return $result;
            
        } catch (Exception $e) {
            log_message('error', "DEBUG_WALL: Exception in is_class_member: " . $e->getMessage());
            $this->ci->db->reset_query();
            $this->ci->db->db_debug = $original_debug;
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

