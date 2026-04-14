<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;


/**
 * Wall Controller
 * 
 * Handles all wall-related operations for Wayo Walls V1
 * Includes wall viewing, post creation, and moderation
 * 
 * @author Wayo Team
 * @date 2026-02-03
 */
class Wall extends BaseController
{
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        
        $this->loadModel('Wall_model', 'wall_model');
        $this->loadModel('User_model', 'user_model');
        $this->loadModel('Settings_model', 'settings_model');
        
        if (!isset($this->wallauthorization)) {
            $this->wallauthorization = new \App\Libraries\WallAuthorization();
        }
    }
    
    public function index()
    {
        return redirect()->to(site_url('wall/community'));
    }

    /**
     * Community Wall Page
     * 
     * @param int $community_id
     */
    public function community($community_id = null)
    {
        try {
            // If no ID provided, use current school ID
            if ($community_id === null) {
                $community_id = school_id();
            }

            // Check authorization
            if (!$this->wallauthorization->can_read_community_wall($community_id)) {
                return redirect()->to('login');
                return;
            }

            // Get or create community wall
            $wall = $this->wall_model->get_or_create_wall('community', $community_id);

            if (!$wall) {
                log_message('error', 'Failed to create wall for community ' . $community_id);
                show_error('Failed to create wall. Please check logs.', 500);
                return;
            }

            // Get community details
            $community = $this->wall_model->get_school_by_id($community_id);

            if (!$community) {
                show_404();
                return;
            }

            // Page data
            $page_data = [
                'page_name' => 'community_wall',
                'page_title' => get_phrase('community_wall'),
                'folder_name' => 'wall',
                'wall' => $wall,
                'community' => $community,
                'can_post' => $this->wallauthorization->can_post_community_wall($community_id),
                'can_moderate' => $this->wallauthorization->is_superadmin() || 
                                ($this->wallauthorization->is_admin() && session()->get('school_id') == $community_id),
                'current_user' => $this->wallauthorization->get_current_user()
            ];

            return view('backend/index', $page_data);
            
        } catch (Exception $e) {
            log_message('error', 'Wall::community Exception: ' . $e->getMessage());
            show_error('An unexpected error occurred: ' . $e->getMessage(), 500);
        } catch (Error $e) {
            log_message('error', 'Wall::community Error: ' . $e->getMessage());
            show_error('A fatal error occurred: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Class Wall Page
     * 
     * @param int $class_id
     */
    public function class($class_id = null)
    {
        // Check if user is logged in
        if (!session()->get('user_id')) {
            return redirect()->to('login');
            return;
        }

        $school_id = school_id();
        $user_id = session()->get('user_id');
        $is_student = session()->get('student_login') == 1;
        $is_admin = $this->wallauthorization->is_admin() || $this->wallauthorization->is_superadmin();

        // Page data initialization
        $page_data = [
            'page_name' => 'class_wall',
            'page_title' => get_phrase('class_wall'),
            'folder_name' => 'wall',
            'wall' => null,
            'class' => null,
            'classes' => [], // List of classes to display
            'can_post' => false,
            'can_moderate' => false,
            'can_view_hidden' => false,
            'current_user' => $this->wallauthorization->get_current_user()
        ];

        // --------------------------------------------------------------------
        // SCENARIO 1: LIST VIEW (No Class Selected)
        // --------------------------------------------------------------------
        if ($class_id === null) {
            if ($is_student) {
                // Get student ID for current school
                $student = $this->wall_model->get_student_by_user_id_and_school($user_id, $school_id);
                
                if (!$student) {
                    $student = $this->wall_model->get_student_by_user_id($user_id);
                }

                if ($student) {
                    // Fetch enrolled classes for current session
                    $active_session_id = active_session();
                    
                    // Log for debugging
                    log_message('error', 'Wall::class - Student ID: ' . $student['id'] . ', School ID: ' . $school_id . ', Session: ' . $active_session_id);

                    $sql = "SELECT classes.* FROM enrols 
                            JOIN classes ON enrols.class_id = classes.id 
                            JOIN invoices ON invoices.class_id = classes.id AND invoices.student_id = enrols.student_id 
                            WHERE enrols.student_id = ? AND enrols.session = ? AND classes.statut = 'active' AND invoices.status = 'paid' 
                            GROUP BY classes.id";
                    $page_data['classes'] = $this->wall_model->get_by_query($sql, [$student['id'], $active_session_id]);
                    
                    log_message('error', 'Wall::class - Found ' . count($page_data['classes']) . ' classes');
                }
            } else {
                // Admin/Teacher sees all active classes
                if ($this->wallauthorization->is_admin() || $this->wallauthorization->is_superadmin()) {
                    $page_data['classes'] = $this->wall_model->get_classes_by_school($school_id);
                } else {
                    $teacher = $this->wall_model->get_teacher_by_user_id($user_id);
                    if ($teacher) {
                        $sql = "SELECT classes.* FROM classes 
                                JOIN teacher_permissions ON teacher_permissions.class_id = classes.id 
                                WHERE teacher_permissions.teacher_id = ? 
                                AND (teacher_permissions.marks = 1 OR teacher_permissions.attendance = 1 OR teacher_permissions.assignment = 1) 
                                AND classes.school_id = ? AND classes.statut = 'active' 
                                GROUP BY classes.id";
                        $page_data['classes'] = $this->wall_model->get_by_query($sql, [$teacher['id'], $school_id]);
                    } else {
                        $page_data['classes'] = [];
                    }
                }
            }
        } 
        // --------------------------------------------------------------------
        // SCENARIO 2: WALL VIEW (Class Selected)
        // --------------------------------------------------------------------
        else {
            // Verify class exists and belongs to school
            $class = $this->wall_model->get_class_by_id($class_id);
            
            if (!$class || $class['school_id'] != $school_id) {
                show_404();
                return;
            }

            // Verify Access Permissions
            if ($is_student) {
                $student = $this->wall_model->get_student_by_user_id_and_school($user_id, $school_id);
                
                if (!$student) {
                    $student = $this->wall_model->get_student_by_user_id($user_id);
                }

                if ($student) {
                    $active_session_id = active_session();
                    
                    // Check enrollment or purchase
                    // We check if student is enrolled in the active session
                    $enrolled_count = $this->wall_model->count_all('enrols', [
                        'student_id' => $student['id'],
                        'class_id' => $class_id,
                        'session' => $active_session_id
                    ]);

                    // Also check for paid invoice to be consistent with list view logic
                    // This allows access if enrolled via purchase
                    $has_paid_invoice = $this->wall_model->count_all('invoices', [
                        'student_id' => $student['id'],
                        'class_id' => $class_id,
                        'status' => 'paid'
                    ]) > 0;

                    if ($enrolled_count == 0 && !$has_paid_invoice) {
                        show_error(get_phrase('permission_denied'), 403);
                        return;
                    }
                } else {
                     show_error(get_phrase('permission_denied'), 403);
                     return;
                }
                
                // Students are READ ONLY
                $page_data['can_post'] = false;
                $page_data['can_moderate'] = false;
            } 
            elseif ($is_admin) {
                // Admins have FULL ACCESS
                $page_data['can_post'] = true;
                $page_data['can_moderate'] = false; // Admins can only moderate their own posts in class wall
                $page_data['can_view_hidden'] = true;
            } else {
                // Teachers/Mentors
                // Check authorization
                if (!$this->wallauthorization->can_read_class_wall($class_id, $school_id)) {
                    show_error(get_phrase('permission_denied'), 403);
                    return;
                }
                
                $page_data['can_post'] = $this->wallauthorization->can_post_class_wall($class_id, $school_id);
                $page_data['can_moderate'] = false; 
                
                // Mentors can view hidden items
                if ($this->wallauthorization->is_teacher_of_class($class_id)) {
                    $page_data['can_view_hidden'] = true;
                }
            }

            // Get or create class wall
            $wall = $this->wall_model->get_or_create_wall('class', $class_id);
            
            if (!$wall) {
                show_error('Failed to create wall.', 500);
                return;
            }

            $page_data['wall'] = $wall;
            $page_data['class'] = $class;
            $page_data['page_title'] = 'wall';
        }

        return view('backend/index', $page_data);
    }

    /**
     * SuperAdmin Moderation Page
     */
    public function moderation()
    {
        // Only SuperAdmin can access
        if (!$this->wallauthorization->is_superadmin()) {
            return redirect()->to('login');
            return;
        }

        $page_data = [
            'page_name' => 'wall_moderation',
            'current_user' => $this->wallauthorization->get_current_user()
        ];

        return view('backend/wall/moderation', $page_data);
    }

    /**
     * Show Create Post Modal
     * 
     * @param int $wall_id
     */
    public function create_post($wall_id)
    {
        // Get wall details
        $wall = $this->wall_model->get_wall_by_id($wall_id);
        if (!$wall) {
            echo get_phrase('wall_not_found');
            return;
        }

        // Determine context for permissions
        $can_post = false;
        $can_moderate = false;
        
        if ($wall['scope_type'] == 'community') {
            $can_post = $this->wallauthorization->can_post_community_wall($wall['scope_id']);
            $can_moderate = $this->wallauthorization->is_superadmin() || 
                           ($this->wallauthorization->is_admin() && session()->get('school_id') == $wall['scope_id']);
        } elseif ($wall['scope_type'] == 'class') {
             $class = $this->wall_model->get_class_by_id($wall['scope_id']);
             if ($class) {
                 $can_post = $this->wallauthorization->can_post_class_wall($wall['scope_id'], $class['school_id']);
                 $can_moderate = $this->wallauthorization->is_superadmin() || 
                                ($this->wallauthorization->is_admin() && session()->get('school_id') == $class['school_id']);
             }
        }

        if (!$can_post) {
            echo get_phrase('permission_denied');
            return;
        }

        $page_data['wall_id'] = $wall_id;
        $page_data['wall'] = $wall;
        $page_data['can_moderate'] = $can_moderate;
        return view('backend/wall/create_post', $page_data);
    }



    /**
     * Handle Post Creation
     * 
     * @param int $wall_id
     */
    public function create_post_action($wall_id)
    {
        // Response format
        $response = ['status' => false, 'message' => ''];

        // Get wall details
        $wall = $this->wall_model->get_wall_by_id($wall_id);
        if (!$wall) {
            $response['message'] = get_phrase('wall_not_found');
            echo json_encode($response);
            return;
        }

        // Check authorization
        $can_post = false;
        $can_moderate = false;
        
        if ($wall['scope_type'] == 'community') {
            $can_post = $this->wallauthorization->can_post_community_wall($wall['scope_id']);
            $can_moderate = $this->wallauthorization->is_superadmin() || 
                           ($this->wallauthorization->is_admin() && session()->get('school_id') == $wall['scope_id']);
        } elseif ($wall['scope_type'] == 'class') {
             $class = $this->wall_model->get_class_by_id($wall['scope_id']);
             if ($class) {
                 $can_post = $this->wallauthorization->can_post_class_wall($wall['scope_id'], $class['school_id']);
                 $can_moderate = $this->wallauthorization->is_superadmin() || 
                                ($this->wallauthorization->is_admin() && session()->get('school_id') == $class['school_id']);
             }
        }

        if (!$can_post) {
            $response['message'] = get_phrase('permission_denied');
            echo json_encode($response);
            return;
        }

        // Form validation
        $title = $this->request->getPost('title');
        $body = $this->request->getPost('body');
        $post_type = $this->request->getPost('post_type');

        if (empty($body)) {
             $response['message'] = get_phrase('content_required');
             echo json_encode($response);
             return;
        }
        
        // Validate post type
        if ($post_type == 'announcement' && !$can_moderate) {
            log_message('error', 'Wall::create_post_action - User ' . session()->get('user_id') . ' attempted to create announcement but lacks permission. Downgrading to post.');
            $post_type = 'post';
        }

        // Create Post Data
        $post_data = [
            'wall_id' => $wall_id,
            'author_user_id' => session()->get('user_id'),
            'title' => $title,
            'body' => $body,
            'type' => $post_type ?? 'post',
            'status' => 'published',
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        log_message('error', 'Wall::create_post_action - Creating post: ' . print_r($post_data, true));

        // Save post
        $post_id = $this->wall_model->create_post($post_data);

        if ($post_id) {
            // Handle Attachments
            if (!empty($_FILES['attachments']['name'][0])) {
                $files = $_FILES['attachments'];
                $count = count($files['name']);
                
                // Create upload directory if not exists
                if (!is_dir('uploads/wall')) {
                    mkdir('uploads/wall', 0777, true);
                }

                $config['upload_path'] = 'uploads/wall/';
                $config['allowed_types'] = 'jpg|jpeg|png|gif|pdf|doc|docx|xls|xlsx|ppt|pptx|txt';
                
                // TODO: Replace with service: Config\Services::upload();

                for($i = 0; $i < $count; $i++) {
                    if(!empty($files['name'][$i])) {
                        $_FILES['file']['name'] = $files['name'][$i];
                        $_FILES['file']['type'] = $files['type'][$i];
                        $_FILES['file']['tmp_name'] = $files['tmp_name'][$i];
                        $_FILES['file']['error'] = $files['error'][$i];
                        $_FILES['file']['size'] = $files['size'][$i];
        
                        $config['file_name'] = time() . '_' . str_replace(' ', '_', $files['name'][$i]);
                        $this->upload->initialize($config);
        
                        if ($this->upload->do_upload('file')) {
                            $uploadData = $this->upload->data();
                            $attachment_data = [
                                'post_id' => $post_id,
                                'filename' => $files['name'][$i],
                                'path' => 'uploads/wall/' . $uploadData['file_name'],
                                'mime_type' => $uploadData['file_type'],
                                'size' => $uploadData['file_size']
                            ];
                            $this->wall_model->add_attachment($attachment_data);
                        }
                    }
                }
            }

            $response['status'] = true;
            $response['message'] = get_phrase('publication_published_successfully');
        } else {
            $response['message'] = get_phrase('failed_to_publish_publication');
        }

        echo json_encode($response);
    }

    /**
     * Show Edit Post Modal
     * 
     * @param int $post_id
     */
    public function edit_post($post_id)
    {
        // Get post details
        $post = $this->wall_model->get_post_by_id($post_id);
        if (!$post) {
            echo get_phrase('post_not_found');
            return;
        }

        // Check authorization
        $user_id = session()->get('user_id');
        $can_moderate_check = $this->wallauthorization->can_moderate_post($post_id);
        $is_author = ($post['author_user_id'] == $user_id);
        
        if (!$can_moderate_check['can_moderate'] && !$is_author) {
            echo get_phrase('permission_denied');
            return;
        }

        $page_data['post'] = $post;
        return view('backend/wall/edit_post', $page_data);
    }
}

