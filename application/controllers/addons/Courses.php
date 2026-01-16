<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
*  @author   : Creativeitem
*  date      : November, 2019
*  Ekattor School Management System With Addons
*  http://codecanyon.net/user/Creativeitem
*  http://support.creativeitem.com
*/

// Include Composer autoloader for HTMLPurifier
require_once FCPATH . 'vendor/autoload.php';

class Courses extends CI_Controller {

  // Constants for AI generation
  const AI_GENERATION_TIMEOUT = 300; // 5 minutes
  const AI_GENERATION_LOCK_DURATION = 300; // 5 minutes
  const PDF_MAX_SIZE_MB = 10;
  const PDF_MAX_SIZE_BYTES = 10240; // 10MB in KB

  // AI model settings
  const AI_MODEL = 'deepseek-chat';
  const AI_TEMPERATURE = 0.7;

  private $purifier;
  
  public function __construct(){

    parent::__construct();

    $this->load->database();
    $this->load->library('session');
    
    // DeepSeek API Configuration
    $this->deepseekUrl = 'https://api.deepseek.com/v1/chat/completions';
    $this->deepseekApiKey = 'sk-249b9057de6f47029c596004558ab8ce'; // DeepSeek API Key
    
    // Local LLM API Configuration (LM Studio / Ollama / vLLM)
    $this->localLlmUrl = 'http://154.146.250.62:7000/v1/chat/completions';
    
    // Initialize HTMLPurifier for XSS protection
    $this->initHtmlPurifier();

    /*LOADING ALL THE MODELS HERE*/
    $this->load->model('Crud_model',     'crud_model');
    $this->load->model('User_model',     'user_model');
    $this->load->model('Settings_model', 'settings_model');
    $this->load->model('Payment_model',  'payment_model');
    $this->load->model('Email_model',    'email_model');
    $this->load->model('Addon_model',    'addon_model');
    $this->load->model('Frontend_model', 'frontend_model');
    $this->load->model('addons/Lms_model','lms_model');
    $this->load->model('addons/Video_model','video_model');

    $superadmin_login = $this->session->userdata('superadmin_login');
    $admin_login = $this->session->userdata('admin_login');
    $teacher_login = $this->session->userdata('teacher_login');
    $student_login = $this->session->userdata('student_login');
    if($teacher_login == 1 || $superadmin_login == 1 || $admin_login == 1 || $student_login == 1){

    }else{
      redirect(site_url('login'), 'refresh');
    }
  }
  
  /**
   * Initialize HTMLPurifier with safe configuration
   */
  private function initHtmlPurifier() {
    $config = HTMLPurifier_Config::createDefault();
    
    // Cache directory for HTMLPurifier
    $cache_dir = FCPATH . 'uploads/cache/htmlpurifier';
    if (!file_exists($cache_dir)) {
      mkdir($cache_dir, 0755, true);
    }
    $config->set('Cache.SerializerPath', $cache_dir);
    
    // Allow safe HTML elements
    $config->set('HTML.Allowed', 
      'p,br,strong,b,em,i,u,s,strike,sub,sup,'.
      'h1,h2,h3,h4,h5,h6,'.
      'ul,ol,li,'.
      'a[href|target|rel],'.
      'img[src|alt|title|width|height|loading|class],'.
      'table,thead,tbody,tfoot,tr,th,td[colspan|rowspan],'.
      'blockquote,pre,code,'.
      'div[class],span[class],'.
      'iframe[src|width|height|frameborder|allowfullscreen|allow|class],'.
      'video[src|controls|width|height],'.
      'hr'
    );
    
    // Allow data URIs for images (base64)
    $config->set('URI.AllowedSchemes', array(
      'http' => true,
      'https' => true,
      'data' => true,
      'mailto' => true
    ));
    
    // Allow target="_blank" for links
    $config->set('Attr.AllowedFrameTargets', array('_blank', '_self'));
    
    // Allow YouTube, Vimeo, Loom embeds
    $config->set('HTML.SafeIframe', true);
    $config->set('URI.SafeIframeRegexp', '%^(https?:)?//(www\.youtube\.com/embed/|player\.vimeo\.com/video/|www\.loom\.com/embed/|www\.dailymotion\.com/embed/)%');
    
    // Allow CSS classes
    $config->set('Attr.AllowedClasses', array(
      'table-responsive-wrapper', 'video-embed-container', 'embed-fallback', 'embed-blocked',
      'ql-align-center', 'ql-align-right', 'ql-align-justify',
      'ql-indent-1', 'ql-indent-2', 'ql-indent-3', 'ql-indent-4',
      'ql-size-small', 'ql-size-large', 'ql-size-huge'
    ));
    
    // Enable auto paragraphs
    $config->set('AutoFormat.AutoParagraph', false);
    
    $this->purifier = new HTMLPurifier($config);
  }
  
  /**
   * Sanitize HTML content to prevent XSS
   * Also adds lazy loading to images
   */
  public function sanitizeHtml($html) {
    if (empty($html)) {
      return '';
    }
    
    // Purify HTML
    $clean = $this->purifier->purify($html);
    
    // Add lazy loading to images that don't already have a loading attribute
    $clean = preg_replace(
      '/<img(?![^>]*\sloading=)([^>]*)>/i',
      '<img$1 loading="lazy">',
      $clean
    );
    
    // Add lazy loading to iframes (videos) that don't already have a loading attribute
    $clean = preg_replace(
      '/<iframe(?![^>]*\sloading=)([^>]*)>/i',
      '<iframe$1 loading="lazy">',
      $clean
    );
    
    // Wrap tables in responsive container
    $clean = preg_replace(
      '/<table([^>]*)>/i',
      '<div class="table-responsive-wrapper"><table$1>',
      $clean
    );
    $clean = str_replace('</table>', '</table></div>', $clean);
    
    return $clean;
  }
  
  //dashboard
  public function index($param1 = '', $param2 = ''){
   
    $this->teacher_access($param2);
    if($param1 == 'create'){
      $this->student_access_denied();
      $this->lms_model->course_add();
      $this->session->set_flashdata('flash_message', get_phrase('course_added_successfully'));
      redirect(site_url('addons/courses'));
    }

    if($param1 == 'update'){
      $this->student_access_denied();
      $this->lms_model->course_edit($param2);
      $this->session->set_flashdata('flash_message', get_phrase('course_updated_successfully'));
      redirect(site_url('addons/courses'));
    }

    if($param1 == 'activity'){
      $this->student_access_denied();
      $this->lms_model->course_activity($param2);
                  // Préparer le nouveau jeton CSRF
                  $csrf = array(
                    'csrfName' => $this->security->get_csrf_token_name(),
                    'csrfHash' => $this->security->get_csrf_hash(),
                );
                          // Renvoyer la réponse JSON avec le HTML mis à jour et le nouveau jeton CSRF
          echo json_encode(array('csrf' => $csrf));
    }

    if($param1 == 'delete'){
      $this->student_access_denied();
      $response = $this->lms_model->delete_course($param2);
      // Préparer le nouveau jeton CSRF
      $csrf = array(
                'csrfName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                    );
      // Renvoyer la réponse JSON avec le HTML mis à jour et le nouveau jeton CSRF
      echo json_encode(array('status' => $response, 'csrf' => $csrf));
      // echo $response;
    }

    if(empty($param1)){
      $page_data['selected_class_id']   = isset($_GET['class_id']) ? $_GET['class_id'] : "all";
      $page_data['selected_user_id'] = isset($_GET['user_id']) ? $_GET['user_id'] : "all";
      $page_data['selected_status']     = isset($_GET['status']) ? $_GET['status'] : "all";
      $page_data['selected_school_id']     = isset($_GET['school_id']) ? $_GET['school_id'] : "all";
      $only_list = isset($_GET['only_list']) ? $_GET['only_list'] : "false";

      // Courses query is used for deciding if there is any course or not. Check the view you will get it
      $page_data['courses']                = $this->lms_model->filter_course_for_backend($page_data['selected_class_id'], $page_data['selected_user_id'], $page_data['selected_status'], $page_data['selected_school_id']);

      $page_data['status_wise_courses']    = $this->lms_model->get_status_wise_courses();
      $page_data['all_teachers']           = $this->user_model->get_all_teachers();
      $page_data['classes']             = $this->crud_model->get_classes();
      $page_data['folder_name'] = 'academy';
      $page_data['page_title'] = 'all_courses';

      


      if($only_list == 'true'){
        if($this->session->userdata('student_login') == 1){ 
          $this->load->view('backend/academy/grid_view_for_student', $page_data);
        }else{
      
          $this->load->view('backend/academy/list', $page_data);
        }
      }else{
 
          $this->load->view('backend/index', $page_data);
       }
    
       

    }
  }

  public function get_subject_by_class($class_id = ""){
    echo $this->lms_model->get_subject_by_class($class_id);
  }

  public function course_add(){
    $this->student_access_denied();
    $page_data['all_teachers']    = $this->user_model->get_all_teachers();
    $page_data['classes']     = $this->crud_model->get_classes();
    $page_data['categories']  = $this->db->get_where('categories', array())->result_array();
    
    // Récupérer la catégorie de l'école actuelle
    $current_school_id = school_id();
    $school_category = '';
    if ($current_school_id) {
        $school = $this->db->get_where('schools', array('id' => $current_school_id))->row_array();
        if ($school && isset($school['category'])) {
            $school_category = $school['category'];
        }
    }
    $page_data['school_category'] = $school_category;
    
    $page_data['course_classes'] = [];   // Pas encore de classes liées
    $page_data['course_teachers'] = [];  // Pas encore de mentors liés
    $page_data['folder_name'] = 'academy';
    $page_data['page_title']  = 'courses_add';
    $page_data['page_name']   = 'create';

    $this->load->view('backend/index', $page_data);
  }

  public function course_edit($course_id = ""){
    $this->student_access_denied();
    $this->teacher_access($course_id);

    $page_data['course']          = $this->lms_model->get_course_by_id($course_id);
    $page_data['all_teachers']        = $this->user_model->get_all_teachers();
    $page_data['classes']         = $this->crud_model->get_classes();
    $page_data['categories']      = $this->db->get_where('categories', array())->result_array();
    
    // Récupérer la catégorie de l'école actuelle
    $current_school_id = school_id();
    $school_category = '';
    if ($current_school_id) {
        $school = $this->db->get_where('schools', array('id' => $current_school_id))->row_array();
        if ($school && isset($school['category'])) {
            $school_category = $school['category'];
        }
    }
    $page_data['school_category'] = $school_category;
    // Pagination des sections (10 par page)
    $sections_per_page = 10;
    $page_data['course_sections'] = $this->lms_model->get_sections_paginated($course_id, $sections_per_page, 0);
    $page_data['total_sections'] = $this->lms_model->count_sections($course_id);
    $page_data['total_pages'] = ceil($page_data['total_sections'] / $sections_per_page);
    $page_data['sections_per_page'] = $sections_per_page;
    // $page_data['subjects']        = $this->db->get_where('subjects', array('class_id' => $page_data['course']['class_id']))->result_array();
    // Trouver la première leçon qui n'est pas un quiz
    $this->db->order_by('order', 'ASC');
    $this->db->where('course_id', $course_id);
    $this->db->where("LOWER(lesson_type) !=", 'quiz');
    $first_non_quiz_lesson = $this->db->get('lesson')->row_array();
    
    // Si aucune leçon non-quiz trouvée, prendre la première leçon disponible
    if (empty($first_non_quiz_lesson)) {
        $first_non_quiz_lesson = $this->db->get_where('lesson', array('course_id' => $course_id))->row_array();
    }
    $page_data['first_lesson_id'] = $first_non_quiz_lesson;
    // Relations : classes et enseignants du cours
    $page_data['course_classes'] = $this->lms_model->get_classes_by_course($course_id);
    $page_data['course_teachers'] = $this->lms_model->get_teachers_by_course($course_id);

    // Sujets en fonction de la 1ère classe (ou tu peux adapter pour plusieurs classes)
    // if (!empty($page_data['course_classes'])) {
    //     $first_class_id = $page_data['course_classes'][0]['id'];
    //     $page_data['subjects'] = $this->db->get_where('subjects', array('class_id' => $first_class_id))->result_array();
    // } else {
    //     $page_data['subjects'] = [];
    // }
    $page_data['folder_name']     = 'academy';
    $page_data['page_title']      = 'courses_edit';
    $page_data['page_name']       = 'edit';

    $this->load->view('backend/index', $page_data);
  }

  public function filter() {
      $class_id = $this->input->get('class_id');
      $user_id = $this->input->get('user_id');
      $status = $this->input->get('status');
      $school_id = $this->input->get('school_id'); // Optional, defaulting to empty/all if not passed

      $page_data['selected_class_id']   = $class_id;
      $page_data['selected_user_id']    = $user_id;
      $page_data['selected_status']     = $status;
      $page_data['selected_school_id']  = $school_id;

      $page_data['courses']             = $this->lms_model->filter_course_for_backend($class_id, $user_id, $status);
      $page_data['status_wise_courses'] = $this->lms_model->get_status_wise_courses();
      $page_data['all_teachers']        = $this->user_model->get_all_teachers();
      $page_data['classes']             = $this->crud_model->get_classes();
      
      $this->load->view('backend/academy/list', $page_data);
  }

  public function update_status($course_id = "") {
    $this->student_access_denied();
    $this->teacher_access($course_id);
    
    $this->lms_model->course_activity($course_id);
    
    echo true;
  }

  public function course_sections($param1 = "", $param2 = "", $param3 = "") {
    $this->student_access_denied();
    $this->teacher_access($param1);
    if ($param2 == 'add') {
      $this->lms_model->add_course_section($param1);
      $this->session->set_flashdata('flash_message', get_phrase('section_has_been_added_successfully'));
    }
    elseif ($param2 == 'edit') {
      $this->lms_model->edit_course_section($param3);
      $this->session->set_flashdata('flash_message', get_phrase('section_has_been_updated_successfully'));
    }
    elseif ($param2 == 'delete') {
      $response = $this->lms_model->delete_course_section($param1, $param3);
      // echo $response;
            // Préparer le nouveau jeton CSRF
            $csrf = array(
              'csrfName' => $this->security->get_csrf_token_name(),
              'csrfHash' => $this->security->get_csrf_hash(),
                  );
    // Renvoyer la réponse JSON avec le HTML mis à jour et le nouveau jeton CSRF
    echo json_encode(array('status' => $response, 'csrf' => $csrf));
    }
    redirect(site_url('addons/courses/course_edit/'.$param1), 'refresh');
  }

  // AJAX: Add section
  public function ajax_add_section($course_id) {
    $this->student_access_denied();
    $this->teacher_access($course_id);
    
    $csrf = array(
      'csrfName' => $this->security->get_csrf_token_name(),
      'csrfHash' => $this->security->get_csrf_hash(),
    );
    
    $title = $this->input->post('title');
    
    if (empty($title)) {
      echo json_encode(array('success' => false, 'message' => get_phrase('title_required'), 'csrf' => $csrf));
      return;
    }
    
    $this->lms_model->add_course_section($course_id);
    
    // Calculate new total pages
    $total_sections = $this->lms_model->count_sections($course_id);
    $total_pages = ceil($total_sections / 10);
    
    echo json_encode(array('success' => true, 'csrf' => $csrf, 'total_pages' => $total_pages));
  }

  // AJAX: Update section
  public function ajax_update_section($section_id) {
    $this->student_access_denied();
    
    // Get section to verify ownership
    $section = $this->db->where('id', $section_id)->get('course_section')->row_array();
    if (!$section) {
      echo json_encode(array('success' => false, 'message' => get_phrase('section_not_found')));
      return;
    }
    
    $this->teacher_access($section['course_id']);
    
    $csrf = array(
      'csrfName' => $this->security->get_csrf_token_name(),
      'csrfHash' => $this->security->get_csrf_hash(),
    );
    
    $title = $this->input->post('title');
    
    if (empty($title)) {
      echo json_encode(array('success' => false, 'message' => get_phrase('title_required'), 'csrf' => $csrf));
      return;
    }
    
    $this->lms_model->edit_course_section($section_id);
    
    echo json_encode(array('success' => true, 'csrf' => $csrf));
  }

  public function quizes($course_id = "", $action = "", $quiz_id = "") {
    $this->student_access_denied();
    $this->teacher_access($course_id);

    if ($action == 'add') {
      $this->lms_model->add_quiz($course_id);
      $this->session->set_flashdata('flash_message', get_phrase('quiz_has_been_added_successfully'));
    }
    elseif ($action == 'edit') {
      $this->lms_model->edit_quiz($quiz_id);
      $this->session->set_flashdata('flash_message', get_phrase('quiz_has_been_updated_successfully'));
    }
    elseif ($action == 'delete') {
      $response = $this->lms_model->delete_course_section($course_id, $quiz_id);
      echo $response;
    }
    redirect(site_url('addons/courses/course_edit/'.$course_id));
  }

  public function lessons($course_id = "", $param1 = "", $param2 = "") {
    $this->teacher_access($course_id);
    if ($param1 == 'add') {
      $this->student_access_denied();
      $this->lms_model->add_lesson();
      $this->session->set_flashdata('flash_message', get_phrase('lesson_has_been_added_successfully'));
      redirect('addons/courses/course_edit/'.$course_id);
    }
    elseif ($param1 == 'edit') {
      $this->student_access_denied();
      $this->lms_model->edit_lesson($param2);
      $this->session->set_flashdata('flash_message', get_phrase('lesson_has_been_updated_successfully'));
      redirect('addons/courses/course_edit/'.$course_id);
    }
    elseif ($param1 == 'delete') {
      $this->student_access_denied();
      $response = $this->lms_model->delete_lesson($param2);
      // echo $response;
      			// Préparer la réponse avec un nouveau jeton CSRF
        $csrf = array(
          'csrfName' => $this->security->get_csrf_token_name(),
          'csrfHash' => $this->security->get_csrf_hash(),
          );
        
        // Renvoyer la réponse avec un nouveau jeton CSRF
        echo json_encode(array('status' => $response, 'csrf' => $csrf));
    }
    if(empty($param1)){
      $page_data['page_name'] = 'lessons';
      $page_data['lessons'] = $this->lms_model->get_lessons('course', $course_id);
      $page_data['course_id'] = $course_id;
      $page_data['page_title'] = get_phrase('lessons');
      $this->load->view('backend/index', $page_data);
    }
  }

  public function ajax_get_video_details() {
    $video_details = $this->video_model->getVideoDetails($_POST['video_url']);
    // echo $video_details['duration'];

      // Préparer le nouveau jeton CSRF
          $csrf = array(
                'csrfName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
                     );
      // Renvoyer la réponse JSON avec le HTML mis à jour et le nouveau jeton CSRF
      echo json_encode(array('duration' => $video_details['duration'], 'csrf' => $csrf));
  }

  // Get lesson content for TipTap editor
  public function get_lesson_content($lesson_id = "") {
    if (empty($lesson_id)) {
      echo json_encode(array('content' => '', 'error' => 'No lesson ID provided'));
      return;
    }

    $lesson = $this->db->get_where('lesson', array('id' => $lesson_id))->row_array();

    $csrf = array(
      'csrfName' => $this->security->get_csrf_token_name(),
      'csrfHash' => $this->security->get_csrf_hash(),
    );

    if ($lesson) {
      // Clean up the content - remove literal \n sequences that may appear as "n" in HTML
      $content = html_entity_decode($lesson['summary'] ?? '', ENT_QUOTES, 'UTF-8');
      $content = str_replace('\\n', '', $content);
      $content = str_replace('\n', '', $content);
      $content = preg_replace('/(?<=>)\s*\n\s*(?=<)/', '', $content); // Remove newlines between tags
      $content = preg_replace('/\s+/', ' ', $content); // Normalize spaces

      $clean_content = trim($content);


      echo json_encode(array(
        'content' => $clean_content,
        'title' => html_entity_decode($lesson['title'] ?? '', ENT_QUOTES, 'UTF-8'),
        'last_modified' => $lesson['last_modified'] ?? '',
        'csrf' => $csrf
      ));
    } else {
      echo json_encode(array('content' => '', 'error' => 'Lesson not found', 'csrf' => $csrf));
    }
  }

  // Get quiz content for editing
  public function get_quiz_content($quiz_id) {
    $csrf = array(
      'csrfName' => $this->security->get_csrf_token_name(),
      'csrfHash' => $this->security->get_csrf_hash(),
    );

    $quiz = $this->db->get_where('lesson', array('id' => $quiz_id, 'lesson_type' => 'quiz'))->row_array();
    
    if ($quiz) {
      // Get questions for this quiz
      $this->db->order_by('order', 'ASC');
      $questions = $this->db->get_where('question', array('quiz_id' => $quiz_id))->result_array();
      
      echo json_encode(array(
        'success' => true,
        'title' => $quiz['title'],
        'summary' => $quiz['summary'],
        'last_modified' => $quiz['last_modified'] ?? '',
        'questions' => $questions,
        'csrf' => $csrf
      ));
    } else {
      echo json_encode(array(
        'success' => false,
        'error' => 'Quiz not found',
        'csrf' => $csrf
      ));
    }
  }

  // Save lesson content from TipTap editor
  public function save_lesson($lesson_id = "") {
    $this->student_access_denied();
    
    $title = $this->input->post('title');
    $content = $this->input->post('content');
    $section_id = $this->input->post('section_id');
    $course_id = $this->input->post('course_id');
    
    // Verify teacher access if applicable
    if (!empty($course_id)) {
      $this->teacher_access($course_id);
    }
    
    $csrf = array(
      'csrfName' => $this->security->get_csrf_token_name(),
      'csrfHash' => $this->security->get_csrf_hash(),
    );
    
    if (empty($title)) {
      echo json_encode(array(
        'success' => false,
        'message' => get_phrase('please_enter_lesson_title'),
        'csrf' => $csrf
      ));
      return;
    }
    
    // Set last_modified timestamp
    $new_last_modified = strtotime(date('D, d-M-Y H:i:s'));
    
    // Sanitize HTML content with HTMLPurifier (XSS protection + lazy load)
    $sanitized_content = $this->sanitizeHtml($content);
    
    // Prepare lesson data - use summary field for HTML content
    $data = array(
      'title' => html_escape($title),
      'summary' => $sanitized_content, // Sanitized HTML content
      'lesson_type' => 'text',
      'attachment_type' => 'text',
      'last_modified' => $new_last_modified
    );
    
    if (empty($lesson_id)) {
      // Create new lesson
      $data['course_id'] = $course_id;
      $data['section_id'] = $section_id;
      $data['date_added'] = strtotime(date('D, d-M-Y'));
      $data['duration'] = '00:00:00';
      
      $this->db->insert('lesson', $data);
      $new_lesson_id = $this->db->insert_id();
      
      if ($new_lesson_id) {
        echo json_encode(array(
          'success' => true,
          'message' => get_phrase('lesson_saved_successfully'),
          'lesson_id' => $new_lesson_id,
          'last_modified' => $new_last_modified,
          'csrf' => $csrf
        ));
      } else {
        echo json_encode(array(
          'success' => false,
          'message' => get_phrase('error_saving_lesson'),
          'csrf' => $csrf
        ));
      }
    } else {
      // Update existing lesson
      $this->db->where('id', $lesson_id);
      $result = $this->db->update('lesson', $data);
      
      if ($result) {
        echo json_encode(array(
          'success' => true,
          'message' => get_phrase('lesson_saved_successfully'),
          'lesson_id' => $lesson_id,
          'last_modified' => $new_last_modified,
          'csrf' => $csrf
        ));
      } else {
        echo json_encode(array(
          'success' => false,
          'message' => get_phrase('error_saving_lesson'),
          'csrf' => $csrf
        ));
      }
    }
  }

  // Save quiz from curriculum editor
  public function save_quiz($quiz_id = "") {
    $this->student_access_denied();

    $title = $this->input->post('title');
    $summary = $this->input->post('summary');
    $section_id = $this->input->post('section_id');
    $course_id = $this->input->post('course_id');
    $questions_json = $this->input->post('questions');

    // Verify teacher access if applicable
    if (!empty($course_id)) {
      $this->teacher_access($course_id);
    }

    $csrf = array(
      'csrfName' => $this->security->get_csrf_token_name(),
      'csrfHash' => $this->security->get_csrf_hash(),
    );

    if (empty($title)) {
      echo json_encode(array(
        'success' => false,
        'message' => get_phrase('please_enter_quiz_title'),
        'csrf' => $csrf
      ));
      return;
    }

    // Set last_modified timestamp
    $new_last_modified = strtotime(date('D, d-M-Y H:i:s'));

    // Sanitize quiz summary/instructions
    $sanitized_summary = $this->sanitizeHtml($summary);
    
    // Prepare quiz data
    $data = array(
      'title' => html_escape($title),
      'summary' => $sanitized_summary,
      'lesson_type' => 'quiz',
      'last_modified' => $new_last_modified
    );

    $target_quiz_id = $quiz_id;

    if (empty($quiz_id)) {
      // Create new quiz
      $data['course_id'] = $course_id;
      $data['section_id'] = $section_id;
      $data['date_added'] = strtotime(date('D, d-M-Y'));
      $data['duration'] = '00:00:00';
      $data['attachment_type'] = '';

      $this->db->insert('lesson', $data);
      $target_quiz_id = $this->db->insert_id();

      if (!$target_quiz_id) {
        echo json_encode(array(
          'success' => false,
          'message' => get_phrase('error_saving_quiz'),
          'csrf' => $csrf
        ));
        return;
      }
    } else {
      // Update existing quiz
      $this->db->where('id', $quiz_id);
      $this->db->update('lesson', $data);
    }

    // Save questions if provided
    if (!empty($questions_json)) {
      $questions = json_decode($questions_json, true);
      
      if (is_array($questions) && count($questions) > 0) {
        // Delete existing questions for this quiz (for update)
        if (!empty($quiz_id)) {
          $this->db->where('quiz_id', $quiz_id);
          $this->db->delete('question');
        }
        
        // Insert new questions
        $order = 0;
        foreach ($questions as $q) {
          $order++;
          $question_data = array(
            'quiz_id' => $target_quiz_id,
            'title' => html_escape($q['question']),
            'type' => 'mcq',
            'number_of_options' => count($q['options']),
            'options' => json_encode($q['options']),
            'correct_answers' => json_encode($q['correct_answers']),
            'order' => $order
          );
          $this->db->insert('question', $question_data);
        }
      }
    }

    echo json_encode(array(
      'success' => true,
      'message' => get_phrase('quiz_saved_successfully'),
      'quiz_id' => $target_quiz_id,
      'last_modified' => $new_last_modified,
      'csrf' => $csrf
    ));
  }

  // Upload image for Quill editor
  public function upload_editor_image() {
    $this->student_access_denied();
    
    $csrf = array(
      'csrfName' => $this->security->get_csrf_token_name(),
      'csrfHash' => $this->security->get_csrf_hash(),
    );
    
    if (empty($_FILES['image']['name'])) {
      echo json_encode(array(
        'success' => false,
        'message' => get_phrase('no_image_selected'),
        'csrf' => $csrf
      ));
      return;
    }
    
    // Create upload directory if not exists
    $upload_dir = 'uploads/lesson_images/';
    $upload_path = './uploads/lesson_images/';
    
    if (!file_exists($upload_path)) {
      mkdir($upload_path, 0777, true);
    }
    
    // Configure upload
    $config = array(
      'upload_path' => $upload_path,
      'allowed_types' => 'gif|jpg|jpeg|png|webp|GIF|JPG|JPEG|PNG|WEBP',
      'max_size' => 5120,
      'encrypt_name' => true
    );
    
    $this->load->library('upload');
    $this->upload->initialize($config);
    
    if ($this->upload->do_upload('image')) {
      $upload_data = $this->upload->data();
      $file_path = $upload_path . $upload_data['file_name'];
      
      // Compress and resize image
      $this->compressImage($file_path, $upload_data['file_type']);
      
      $file_url = base_url($upload_dir . $upload_data['file_name']);
      
      echo json_encode(array(
        'success' => true,
        'url' => $file_url,
        'csrf' => $csrf
      ));
    } else {
      echo json_encode(array(
        'success' => false,
        'message' => strip_tags($this->upload->display_errors()),
        'csrf' => $csrf
      ));
    }
  }
  
  /**
   * Compress and resize image
   * - Max width: 1920px
   * - Quality: 80%
   */
  private function compressImage($file_path, $image_type) {
    if (!file_exists($file_path)) return;
    
    // Check if GD library and getimagesize function are available
    if (!function_exists('imagecreatetruecolor') || !function_exists('getimagesize')) {
      log_message('debug', 'GD library or getimagesize not available, skipping image compression');
      return;
    }
    
    // Get image info
    list($width, $height) = getimagesize($file_path);
    if ($width === false || $height === false) {
      log_message('debug', 'Failed to get image dimensions, skipping compression');
      return;
    }
    
    // Max dimensions
    $max_width = 1920;
    $max_height = 1080;
    $quality = 80;
    
    // Calculate new dimensions if needed
    $new_width = $width;
    $new_height = $height;
    
    if ($width > $max_width) {
      $ratio = $max_width / $width;
      $new_width = $max_width;
      $new_height = round($height * $ratio);
    }
    
    if ($new_height > $max_height) {
      $ratio = $max_height / $new_height;
      $new_height = $max_height;
      $new_width = round($new_width * $ratio);
    }
    
    // Create image resource based on type
    switch (strtolower($image_type)) {
      case 'image/jpeg':
      case 'image/jpg':
        $source = imagecreatefromjpeg($file_path);
        break;
      case 'image/png':
        $source = imagecreatefrompng($file_path);
        break;
      case 'image/gif':
        $source = imagecreatefromgif($file_path);
        break;
      case 'image/webp':
        if (function_exists('imagecreatefromwebp')) {
          $source = imagecreatefromwebp($file_path);
        } else {
          return; // WebP not supported
        }
        break;
      default:
        return; // Unsupported format
    }
    
    if (!$source) return;
    
    // Create new image with new dimensions
    $destination = imagecreatetruecolor($new_width, $new_height);
    
    // Preserve transparency for PNG and GIF
    if (in_array(strtolower($image_type), ['image/png', 'image/gif'])) {
      imagealphablending($destination, false);
      imagesavealpha($destination, true);
      $transparent = imagecolorallocatealpha($destination, 255, 255, 255, 127);
      imagefilledrectangle($destination, 0, 0, $new_width, $new_height, $transparent);
    }
    
    // Resize
    imagecopyresampled($destination, $source, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
    
    // Save compressed image
    switch (strtolower($image_type)) {
      case 'image/jpeg':
      case 'image/jpg':
        imagejpeg($destination, $file_path, $quality);
        break;
      case 'image/png':
        // PNG quality is 0-9 (0 = no compression, 9 = max compression)
        $png_quality = round((100 - $quality) / 10);
        imagepng($destination, $file_path, $png_quality);
        break;
      case 'image/gif':
        imagegif($destination, $file_path);
        break;
      case 'image/webp':
        if (function_exists('imagewebp')) {
          imagewebp($destination, $file_path, $quality);
        }
        break;
    }
    
    // Free memory
    imagedestroy($source);
    imagedestroy($destination);
  }
  
  // Upload attachment for Quill editor
  public function upload_editor_attachment() {
    $this->student_access_denied();
    
    $csrf = array(
      'csrfName' => $this->security->get_csrf_token_name(),
      'csrfHash' => $this->security->get_csrf_hash(),
    );
    
    if (empty($_FILES['file']['name'])) {
      echo json_encode(array(
        'success' => false,
        'message' => get_phrase('no_file_selected'),
        'csrf' => $csrf
      ));
      return;
    }
    
    // Create upload directory if not exists
    $upload_dir = 'uploads/lesson_attachments/';
    $upload_path = './uploads/lesson_attachments/';
    
    if (!file_exists($upload_path)) {
      mkdir($upload_path, 0777, true);
    }
    
    // Configure upload
    $config = array(
      'upload_path' => $upload_path,
      'allowed_types' => 'pdf|doc|docx|xls|xlsx|ppt|pptx|zip|rar|txt|PDF|DOC|DOCX|XLS|XLSX|PPT|PPTX|ZIP|RAR|TXT',
      'max_size' => self::PDF_MAX_SIZE_BYTES,
      'encrypt_name' => true
    );
    
    $this->load->library('upload');
    $this->upload->initialize($config);
    
    if ($this->upload->do_upload('file')) {
      $upload_data = $this->upload->data();
      $file_url = base_url($upload_dir . $upload_data['file_name']);
      $original_name = $upload_data['orig_name'];
      
      echo json_encode(array(
        'success' => true,
        'url' => $file_url,
        'name' => $original_name,
        'csrf' => $csrf
      ));
    } else {
      echo json_encode(array(
        'success' => false,
        'message' => strip_tags($this->upload->display_errors()),
        'csrf' => $csrf
      ));
    }
  }

  public function ajax_sort_section() {
    $section_json = $this->input->post('itemJSON');
    $start_order = (int) $this->input->post('startOrder') ?: 1;
    
    if (!empty($section_json)) {
      $this->lms_model->sort_section($section_json, $start_order);
      $success = true;
    } else {
      $success = false;
    }
    
    // Préparer le nouveau jeton CSRF
    $csrf = array(
      'csrfName' => $this->security->get_csrf_token_name(),
      'csrfHash' => $this->security->get_csrf_hash(),
    );
          
    // Renvoyer la réponse JSON
    echo json_encode(array('success' => $success, 'startOrder' => $start_order, 'csrf' => $csrf));
  }

  // Move section to a specific position
  public function ajax_move_section() {
    $this->student_access_denied();
    
    $section_id = (int) $this->input->post('section_id');
    $target_position = (int) $this->input->post('target_position');
    $course_id = (int) $this->input->post('course_id');
    
    $csrf = array(
      'csrfName' => $this->security->get_csrf_token_name(),
      'csrfHash' => $this->security->get_csrf_hash(),
    );
    
    if (!$section_id || !$target_position || !$course_id) {
      echo json_encode(array('success' => false, 'message' => get_phrase('invalid_parameters'), 'csrf' => $csrf));
      return;
    }
    
    $this->teacher_access($course_id);
    
    $result = $this->lms_model->move_section_to_position($section_id, $target_position, $course_id);
    
    echo json_encode(array('success' => $result, 'csrf' => $csrf));
  }

  public function ajax_get_section($course_id){
    $page_data['course_sections'] = $this->lms_model->get_section('course', $course_id)->result_array();
    return $this->load->view('backend/academy/ajax_get_section', $page_data);
  }

  // Pagination des sections du curriculum
  public function ajax_get_sections_paginated($course_id) {
    $page = (int) $this->input->get('page') ?: 1;
    $limit = 10;
    $offset = ($page - 1) * $limit;
    
    $sections = $this->lms_model->get_sections_paginated($course_id, $limit, $offset);
    $total_sections = $this->lms_model->count_sections($course_id);
    $total_pages = ceil($total_sections / $limit);
    
    // Charger les leçons pour chaque section
    foreach ($sections as &$section) {
      $section['lessons'] = $this->lms_model->get_lessons('section', $section['id'])->result_array();
    }
    
    // Calculer le numéro de départ des sections pour cette page
    $start_number = $offset + 1;
    
    $response = array(
      'status' => true,
      'sections' => $sections,
      'current_page' => $page,
      'total_pages' => $total_pages,
      'total_sections' => $total_sections,
      'start_number' => $start_number,
      'csrf' => array(
        'csrfName' => $this->security->get_csrf_token_name(),
        'csrfHash' => $this->security->get_csrf_hash()
      )
    );
    
    echo json_encode($response);
  }

  public function ajax_sort_lesson() {
    $lesson_json = $this->input->post('itemJSON');
    $this->lms_model->sort_lesson($lesson_json);
    // Préparer le nouveau jeton CSRF
    $csrf = array(
              'csrfName' => $this->security->get_csrf_token_name(),
              'csrfHash' => $this->security->get_csrf_hash(),
          );
      
    // Renvoyer la réponse JSON avec le HTML mis à jour et le nouveau jeton CSRF
    echo json_encode(array('csrf' => $csrf));
  }

public function ajax_sort_question() {
    $question_json = $this->input->post('itemJSON');
    $exam_id = $this->input->post('exam_id');

    if ($exam_id) {
        $this->lms_model->sort_exam_question($question_json);
    } else {
        $this->lms_model->sort_question($question_json);
    }

    $csrf = array(
        'csrfName' => $this->security->get_csrf_token_name(),
        'csrfHash' => $this->security->get_csrf_hash(),
    );

    echo json_encode(array('status' => true, 'csrf' => $csrf));
}

  // this function is responsible for managing multiple choice question
public function manage_multiple_choices_options() {
    $number_of_options = $this->input->post('number_of_options');
    $html = '';
    for ($i = 0; $i < $number_of_options; $i++) {
        $html .= '<div class="form-group mb-2 options">';
        $html .= '<label>' . get_phrase('option') . ' ' . ($i + 1) . '</label>';
        $html .= '<div class="input-group">';
        $html .= '<input type="text" class="form-control" name="options[]" id="option_' . $i . '" placeholder="' . get_phrase('option_') . ($i + 1) . '" required>';
        $html .= '<div class="input-group-append">';
        $html .= '<span class="input-group-text d-block">';
        $html .= '<input type="checkbox" name="correct_answers[]" value="' . ($i + 1) . '">';
        $html .= '</span>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
    }

    $csrf = array(
        'csrfName' => $this->security->get_csrf_token_name(),
        'csrfHash' => $this->security->get_csrf_hash(),
    );

    echo json_encode(array('html' => $html, 'csrf' => $csrf));
}
  // Manage Quize Questions

  public function quiz_questions($quiz_id = "", $action = "", $question_id = "") {
    // Check if the user is authenticated based on role
    $superadmin_login = $this->session->userdata('superadmin_login');
    $admin_login = $this->session->userdata('admin_login');
    $teacher_login = $this->session->userdata('teacher_login');
    $student_login = $this->session->userdata('student_login');

    if (!($superadmin_login == 1 || $admin_login == 1 || $teacher_login == 1 || $student_login == 1)) {
        log_message('error', 'Unauthorized access: No valid role found in session');
        echo json_encode([
            'status' => false,
            'message' => 'Unauthorized access',
            'csrf' => [
                'csrfName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash()
            ]
        ]);
        return;
    }

    // Additional check for student access
    if ($student_login == 1 && in_array($action, ['add', 'edit', 'delete'])) {
        echo json_encode([
            'status' => false,
            'message' => 'Students are not authorized to perform this action',
            'csrf' => [
                'csrfName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash()
            ]
        ]);
        return;
    }

    $response = [];
    try {
        if ($action == 'add') {
            $result = $this->lms_model->add_quiz_questions($quiz_id);
            $response = [
                'html' => $result ? 1 : 0,
                'message' => $result ? 'Question added successfully' : 'Failed to add question',
                'csrf' => [
                    'csrfName' => $this->security->get_csrf_token_name(),
                    'csrfHash' => $this->security->get_csrf_hash()
                ]
            ];
        } elseif ($action == 'edit') {
            $result = $this->lms_model->update_quiz_questions($question_id);
            $response = [
                'html' => $result ? 1 : 0,
                'message' => $result ? 'Question updated successfully' : 'Failed to update question',
                'csrf' => [
                    'csrfName' => $this->security->get_csrf_token_name(),
                    'csrfHash' => $this->security->get_csrf_hash()
                ]
            ];
        } elseif ($action == 'delete') {
            $result = json_decode($this->lms_model->delete_quiz_question($question_id), true); // Decode JSON string
            $response = [
                'status' => isset($result['status']) ? $result['status'] : false,
                'message' => isset($result['notification']) ? $result['notification'] : 'Failed to delete question',
                'csrf' => [
                    'csrfName' => $this->security->get_csrf_token_name(),
                    'csrfHash' => $this->security->get_csrf_hash()
                ]
            ];
        } else {
            $page_data['page_name'] = 'quiz_questions';
            $page_data['page_title'] = get_phrase('manage_quiz_questions');
            $page_data['quiz_id'] = $quiz_id;
            $this->load->view('backend/index', $page_data);
            return;
        }
    } catch (Exception $e) {
        $response = [
            'status' => false,
            'message' => 'An error occurred: ' . $e->getMessage(),
            'csrf' => [
                'csrfName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash()
            ]
        ];
    }

    echo json_encode($response);
}

  // Mark this lesson as completed codes
  public function save_course_progress() {
    $response = $this->lms_model->save_course_progress();
    // Préparer le nouveau jeton CSRF
    $csrf = array(
            'csrfName' => $this->security->get_csrf_token_name(),
            'csrfHash' => $this->security->get_csrf_hash(),
          );
        
    // Renvoyer la réponse JSON avec le HTML mis à jour et le nouveau jeton CSRF
    echo json_encode(array('html' => $response, 'csrf' => $csrf));
  }

  public function teacher_access($course_id = ""){
    if($course_id != ""){
      $course_data = $this->lms_model->get_course_by_id($course_id);
      if($this->session->userdata('teacher_login') == 1 && $course_data['user_id'] != $this->session->userdata('user_id')){
        $this->session->set_flashdata('error_message', get_phrase('you_do_not_have_access_to_this_course'));
        redirect(site_url('addons/courses'), 'refresh');
      }
    }
  }
  public function student_access_denied(){
    if($this->session->userdata('student_login') == 1){
      $this->session->set_flashdata('error_message', get_phrase('you_do_not_have_access_to_this_course'));
      redirect(site_url('addons/courses'), 'refresh');
    }
  }

  public function course_preview($course_id = ""){
    $page_data['course'] = $this->lms_model->get_course_by_id($course_id);
    $this->load->view('backend/academy/course_preview', $page_data);
  }
  public function course_information($course_id = ""){
    $page_data['course'] = $this->lms_model->get_course_by_id($course_id);
    $this->load->view('backend/academy/course_information', $page_data);
  }

  // Manage Exam Questions
  public function exam_questions($exam_id = "", $action = "", $question_id = "") {
    $this->student_access_denied();
    $exam_details = $this->lms_model->get_exams('exam', $exam_id)->row_array();

    if ($action == 'add') {
        $response = $this->lms_model->add_exam_questions($exam_id);
        echo json_encode(array('html' => $response)); // Supprimez 'csrf'
    } elseif ($action == 'edit') {
        $response = $this->lms_model->update_exam_questions($question_id);
        echo json_encode(array('html' => $response)); // Supprimez 'csrf'
    } elseif ($action == 'delete') {
        $success = $this->lms_model->delete_exam_question($question_id);
        $response = array(
            'status' => $success ? true : false
        );
        echo json_encode($response); // Supprimez 'csrf'
    }
}

// Save exam questions from integrated editor (autosave)
public function save_exam_questions() {
    $this->student_access_denied();

    $exam_id = $this->input->post('exam_id');
    $questions_json = $this->input->post('questions');

    $csrf = array(
        'csrfName' => $this->security->get_csrf_token_name(),
        'csrfHash' => $this->security->get_csrf_hash(),
    );

    if (empty($exam_id)) {
        echo json_encode(array(
            'success' => false,
            'message' => get_phrase('exam_id_required'),
            'csrf' => $csrf
        ));
        return;
    }

    // Verify exam exists
    $exam = $this->lms_model->get_exams('exam', $exam_id)->row_array();
    if (!$exam) {
        echo json_encode(array(
            'success' => false,
            'message' => get_phrase('exam_not_found'),
            'csrf' => $csrf
        ));
        return;
    }

    // Save questions if provided
    if (!empty($questions_json)) {
        $questions = json_decode($questions_json, true);
        
        if (is_array($questions)) {
            // Delete existing questions for this exam
            $this->db->where('exam_id', $exam_id);
            $this->db->delete('exam_questions');
            
            // Insert new questions
            $order = 0;
            foreach ($questions as $q) {
                if (empty($q['question']) || empty($q['options']) || count($q['options']) < 2) {
                    continue;
                }
                
                $order++;
                $correct_answers = isset($q['correct_answers']) ? $q['correct_answers'] : array();
                
                $question_data = array(
                    'exam_id' => $exam_id,
                    'title' => html_escape($q['question']),
                    'type' => 'multiple_choice',
                    'number_of_options' => count($q['options']),
                    'options' => json_encode($q['options']),
                    'correct_answers' => json_encode($correct_answers),
                    'order' => $order
                );
                $this->db->insert('exam_questions', $question_data);
            }
        }
    }

    // Get updated question count
    $question_count = $this->db->where('exam_id', $exam_id)->count_all_results('exam_questions');

    echo json_encode(array(
        'success' => true,
        'message' => get_phrase('questions_saved_successfully'),
        'question_count' => $question_count,
        'csrf' => $csrf
    ));
}

// Get all exam questions as JSON (for refreshing editor after generation)
public function get_exam_questions_json($exam_id = "") {
    $this->student_access_denied();

    $csrf = array(
        'csrfName' => $this->security->get_csrf_token_name(),
        'csrfHash' => $this->security->get_csrf_hash(),
    );

    if (empty($exam_id)) {
        echo json_encode(array('success' => false, 'questions' => [], 'csrf' => $csrf));
        return;
    }

    $questions_result = $this->lms_model->get_exam_questions($exam_id)->result_array();
    
    $questions = array_map(function($q) {
        return array(
            'id' => $q['id'],
            'question' => html_entity_decode($q['title'], ENT_QUOTES, 'UTF-8'),
            'options' => json_decode($q['options'], true) ?: [],
            'correct_answers' => json_decode($q['correct_answers'], true) ?: []
        );
    }, $questions_result);

    echo json_encode(array(
        'success' => true,
        'questions' => $questions,
        'csrf' => $csrf
    ));
}

// Get exam question by ID for editing
public function get_exam_question($question_id = "") {
    $this->student_access_denied();

    $csrf = array(
        'csrfName' => $this->security->get_csrf_token_name(),
        'csrfHash' => $this->security->get_csrf_hash(),
    );

    if (empty($question_id)) {
        echo json_encode(array('success' => false, 'csrf' => $csrf));
        return;
    }

    $question = $this->lms_model->get_exam_question_by_id($question_id)->row_array();
    
    if ($question) {
        $question['options'] = json_decode($question['options'], true);
        $question['correct_answers'] = json_decode($question['correct_answers'], true);
        echo json_encode(array('success' => true, 'question' => $question, 'csrf' => $csrf));
    } else {
        echo json_encode(array('success' => false, 'message' => get_phrase('question_not_found'), 'csrf' => $csrf));
    }
}

public function manage_exam_multiple_choices_options() {
    $number_of_options = $this->input->post('number_of_options');
    $html = '';
    for ($i = 0; $i < $number_of_options; $i++) {
        $option_label = html_escape(get_phrase('option') . ' ' . ($i + 1));
        $placeholder = html_escape(get_phrase('option_') . ($i + 1));
        $html .= '<div class="form-group mb-2 options">';
        $html .= '<label>' . $option_label . '</label>';
        $html .= '<div class="input-group">';
        $html .= '<input type="text" class="form-control" name="options[]" id="option_' . $i . '" placeholder="' . $placeholder . '" required>';
        $html .= '<div class="input-group-append">';
        $html .= '<span class="input-group-text d-block">';
        $html .= '<input type="checkbox" name="correct_answers[]" value="' . ($i + 1) . '">';
        $html .= '</span>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
    }


    $csrf = array(
        'csrfName' => $this->security->get_csrf_token_name(),
        'csrfHash' => $this->security->get_csrf_hash(),
    );

    $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode(array('html' => $html, 'csrf' => $csrf)));
}

public function get_csrf_token() {
  // Ensure the request is AJAX
  if (!$this->input->is_ajax_request()) {
      show_404();
  }

  // Prepare the CSRF token response
  $csrf = array(
      'csrfName' => $this->security->get_csrf_token_name(),
      'csrfHash' => $this->security->get_csrf_hash()
  );

  // Return JSON response with 'csrf' wrapper for compatibility
  $this->output
      ->set_content_type('application/json')
      ->set_output(json_encode(array('success' => true, 'csrf' => $csrf)));
}

public function generate_questions_from_pdf()
  {
    if (!$this->input->is_ajax_request()) {
      $this->respond_json([
        'status' => false,
        'message' => 'Accès refusé'
      ], 400);
      return;
    }

    // Vérification anti-doublon : empêcher plusieurs générations simultanées
    $generation_key = 'pdf_generation_in_progress_' . $this->session->userdata('user_id');
    $generation_lock = $this->session->userdata($generation_key);

    if ($generation_lock && (time() - $generation_lock) < self::AI_GENERATION_LOCK_DURATION) {
      $this->respond_json([
        'status' => false,
        'message' => 'Une génération est déjà en cours. Veuillez patienter ou réessayer dans quelques instants.'
      ], 429);
      return;
    }

    // Activer le verrouillage
    $this->session->set_userdata($generation_key, time());

    $exam_id         = $this->input->post('exam_id');
    $num_questions   = (int) $this->input->post('number_of_questions');
    $difficulty_level = $this->input->post('difficulty_level') ?: 'medium';
    $overwrite_flag  = strtolower((string) $this->input->post('overwrite_existing'));
    $should_overwrite = in_array($overwrite_flag, array('1', 'true', 'on', 'yes'), true);
    
    // Valider le niveau de difficulté
    $valid_difficulties = array('easy', 'medium', 'hard', 'mixed');
    if (!in_array($difficulty_level, $valid_difficulties)) {
        $difficulty_level = 'medium';
    }

    if (empty($_FILES['pdf_file']['name'])) {
      $this->session->unset_userdata($generation_key);
      $this->respond_json([
        'status' => false,
        'message' => 'Aucun fichier PDF'
      ], 400);
      return;
    }

    // === 1. Vérification MIME + Upload ===
    if (!empty($_FILES['pdf_file']['tmp_name'])) {
      $finfo = finfo_open(FILEINFO_MIME_TYPE);
      $mime  = finfo_file($finfo, $_FILES['pdf_file']['tmp_name']);
      finfo_close($finfo);
      if ($mime !== 'application/pdf') {
        $this->session->unset_userdata($generation_key);
        $this->respond_json([
          'status' => false,
          'message' => 'Fichier non PDF détecté'
        ], 400);
        return;
      }
    }

    $upload_path = FCPATH . 'uploads/temp_pdf/';
    if (!is_dir($upload_path)) {
      mkdir($upload_path, 0755, true);
    }

    $this->load->library('upload');
    $config = array(
      'upload_path'   => $upload_path,
      'allowed_types' => 'pdf',
      'max_size'      => 10240,
      'encrypt_name'  => true
    );
    $this->upload->initialize($config);

    if (!$this->upload->do_upload('pdf_file')) {
      $this->session->unset_userdata($generation_key);
      $this->respond_json([
        'status' => false,
        'message' => strip_tags($this->upload->display_errors())
      ], 400);
      return;
    }

    $upload_data = $this->upload->data();
    $pdf_path    = $upload_data['full_path'];

    // === 2. Extraction texte avec Smalot ===
    try {
      $parser = new \Smalot\PdfParser\Parser();
      $pdf    = $parser->parseFile($pdf_path);
      $text   = $pdf->getText();
      unlink($pdf_path);

      if (empty(trim($text))) {
        $this->session->unset_userdata($generation_key);
        $this->respond_json([
          'status' => false,
          'message' => 'Aucun texte extrait du PDF'
        ], 422);
        return;
      }
    } catch (Exception $e) {
      @unlink($pdf_path);
      $this->session->unset_userdata($generation_key);
      $this->respond_json([
        'status' => false,
        'message' => 'Erreur lecture PDF'
      ], 500);
      return;
    }

    // === 3. Génération QCM avec GUZZLE ===
    $questions = $this->generate_mcq($text, $num_questions, $difficulty_level);

    if (empty($questions)) {
      $this->session->unset_userdata($generation_key);
      $this->respond_json([
        'status' => false,
        'message' => 'L\'IA n\'a pas généré de questions.'
      ], 500);
      return;
    }

    $this->db->trans_begin();

    if ($should_overwrite && $exam_id) {
      $this->db->where('exam_id', $exam_id);
      $this->db->delete('exam_questions');
    }

    $added = 0;
    foreach ($questions as $q) {
      if ($this->add_mcq_from_ai($exam_id, $q)) {
        $added++;
      }
    }

    if ($added === 0) {
      $this->db->trans_rollback();
      $this->session->unset_userdata($generation_key);
      $this->respond_json(array(
        'status'  => false,
        'message' => 'Aucune nouvelle question n\'a pu être créée. Les questions existantes sont inchangées.'
      ), 422);
      return;
    }

    if ($this->db->trans_status() === false) {
      $this->db->trans_rollback();
      $this->session->unset_userdata($generation_key);
      $this->respond_json(array(
        'status'  => false,
        'message' => 'Une erreur est survenue lors de l\'enregistrement des questions.'
      ), 500);
      return;
    }

    $this->db->trans_commit();

    // Libérer le verrouillage après succès
    $this->session->unset_userdata($generation_key);

    // Get total question count for this exam
    $total_questions = $this->db->where('exam_id', $exam_id)->count_all_results('exam_questions');

    $extra_message = $should_overwrite ? ' Les questions existantes ont été remplacées.' : '';

    $this->respond_json(array(
      'status'  => true,
      'message' => "$added questions générées et ajoutées avec succès !" . $extra_message,
      'questions_count' => $total_questions,
      'added_count' => $added
    ));
  }

  private function generate_mcq($text, $num = 10, $difficulty = 'medium')
  {
    $text = substr(trim($text), 0, 28000);
    
    // Définir les instructions de difficulté
    $difficulty_instructions = array(
      'easy' => "NIVEAU DE DIFFICULTÉ : FACILE\n" .
                "- Questions simples et directes\n" .
                "- Réponses évidentes pour qui a lu le document\n" .
                "- Évite les pièges et les nuances complexes\n" .
                "- Les mauvaises réponses doivent être clairement incorrectes\n",
      'medium' => "NIVEAU DE DIFFICULTÉ : MOYEN\n" .
                 "- Questions de compréhension standard\n" .
                 "- Nécessite une bonne lecture du document\n" .
                 "- Inclus quelques questions de réflexion\n" .
                 "- Les mauvaises réponses peuvent être plausibles\n",
      'hard' => "NIVEAU DE DIFFICULTÉ : DIFFICILE\n" .
                "- Questions approfondies et analytiques\n" .
                "- Requiert une compréhension fine du contenu\n" .
                "- Inclus des questions de synthèse et d'analyse\n" .
                "- Les mauvaises réponses doivent être très plausibles (pièges subtils)\n",
      'mixed' => "NIVEAU DE DIFFICULTÉ : MIXTE\n" .
                "- Varie les niveaux : 30% facile, 40% moyen, 30% difficile\n" .
                "- Commence par des questions simples et augmente progressivement\n" .
                "- Inclus tous types de questions (mémorisation, compréhension, analyse)\n"
    );
    
    $difficulty_text = isset($difficulty_instructions[$difficulty]) 
                       ? $difficulty_instructions[$difficulty] 
                       : $difficulty_instructions['medium'];

    try {
      $client = new \GuzzleHttp\Client([
        'timeout'         => 120,
        'connect_timeout' => 30,
      ]);

      $response = $client->post($this->deepseekUrl, [
        'headers' => [
          'Content-Type' => 'application/json',
          'Authorization' => 'Bearer ' . $this->deepseekApiKey
        ],
        'json'    => [
          'model' => 'deepseek-chat',
          'messages' => [
            [
              'role' => 'system',
              'content' => "Tu es un expert en création de QCM pédagogiques.\n" .
                "Tu dois générer EXACTEMENT $num questions à choix multiples (4 options, 1 seule bonne réponse).\n\n" .
                $difficulty_text . "\n" .
                "Réponds UNIQUEMENT avec du JSON valide, RIEN d'autre. Pas de markdown, pas de texte avant/après.\n\n" .
                "Format strict :\n" .
                "[\n" .
                "  {\"title\": \"Question ?\", \"options\": [\"A\", \"B\", \"C\", \"D\"], \"correct_answer\": 1}\n" .
                "]\n" .
                "Commence directement par [ et termine par ]."
            ],
            [
              'role' => 'user',
              'content' => "Voici le document :\n\n$text\n\n" .
                "Génère exactement $num questions QCM (4 options, 1 bonne réponse) en français."
            ]
          ],
          'temperature' => 0.6,
          'max_tokens' => 4000,
          'stream' => false
        ]
      ]);

      $result = json_decode($response->getBody()->getContents(), true);
      $content = $result['choices'][0]['message']['content'] ?? '';

      // Extraction ultra-robuste du JSON
      if (preg_match('/\[[\s\S]*\]/', $content, $m)) {
        $json = json_decode($m[0], true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($json)) {
          return $json;
        }
      }

      return [];
    } catch (Exception $e) {
      log_message('error', 'DeepSeek API Error: ' . $e->getMessage());
      return [];
    }
  }

  /**
   * Normalize multilingual strings so that accents and apostrophes are stored as-is.
   * We also decode any HTML entities that might have been introduced upstream.
   */
  private function sanitize_multilingual_text($value)
  {
    if (!is_string($value)) {
      return '';
    }

    $value = trim($value);

    if ($value === '') {
      return '';
    }

    $html_entity_flags = defined('ENT_HTML5') ? ENT_QUOTES | ENT_HTML5 : ENT_QUOTES;
    $value = html_entity_decode($value, $html_entity_flags, 'UTF-8');

    if (function_exists('iconv')) {
      $converted = @iconv('UTF-8', 'UTF-8//IGNORE', $value);
      if ($converted !== false) {
        $value = $converted;
      }
    }

    return $value;
  }

  private function add_mcq_from_ai($exam_id, $question_data)
  {
    // Validation minimale
    if (empty($question_data['title']) || empty($question_data['options']) || !isset($question_data['correct_answer'])) {
      return false;
    }

    // Nettoyage et normalisation pour préserver les caractères accentués
    $title   = $this->sanitize_multilingual_text($question_data['title']);
    $options = $question_data['options'];
    $correct_answer  = (int)$question_data['correct_answer']; // 1, 2, 3 ou 4

    // On s'assure qu'il y a bien 4 options
    if (count($options) !== 4) {
      return false;
    }

    // On nettoie les options
    $clean_options = [];
    foreach ($options as $opt) {
      $clean = $this->sanitize_multilingual_text($opt);
      if ($clean === '') {
        return false;
      }
      $clean_options[] = $clean;
    }

    // On vérifie que la bonne réponse est valide (1 à 4)
    if ($correct_answer < 1 || $correct_answer > 4) {
      $correct_answer = 1; // fallback
    }

    $data = [
      'exam_id'           => $exam_id,
      'title'             => $title,
      'type'              => 'multiple_choice',
      'number_of_options' => 4,
      'options'           => json_encode($clean_options, JSON_UNESCAPED_UNICODE),
      'correct_answers'   => json_encode([$correct_answer]), // tableau car ton modèle gère les réponses multiples
      'order'             => $this->db->count_all('exam_questions') + 1 // ordre auto
    ];

    return $this->db->insert('exam_questions', $data);
  }

  private function respond_json($payload = array(), $status_code = 200)
  {
    if (!is_array($payload)) {
      $payload = array();
    }

    $payload['csrf'] = array(
      'csrfName' => $this->security->get_csrf_token_name(),
      'csrfHash' => $this->security->get_csrf_hash(),
    );

    $this->output
      ->set_status_header($status_code)
      ->set_content_type('application/json')
      ->set_output(json_encode($payload, JSON_UNESCAPED_UNICODE));
  }

  /**
   * Extract PDF structure (TOC + H1/H2 headings) and save as JSON
   * Uses Python script with PyMuPDF for better extraction
   */
  public function extract_pdf_structure()
  {
    // Get Python executable path from config or auto-detect
    $python_path = $this->get_python_path();
    
    if (!$python_path) {
      log_message('error', 'Python not found. Please configure PYTHON_EXECUTABLE_PATH in constants.php');
      return $this->respond_json(['error' => 'Python not configured. Contact administrator.'], 500);
    }
    
    // Auth check
    if (!$this->session->userdata('teacher_login') && 
        !$this->session->userdata('admin_login') && 
        !$this->session->userdata('superadmin_login')) {
      return $this->respond_json(['error' => 'Unauthorized'], 403);
    }

    // Validate upload
    if (!isset($_FILES['pdf_file']) || $_FILES['pdf_file']['error'] !== UPLOAD_ERR_OK) {
      return $this->respond_json(['error' => 'No PDF file uploaded'], 400);
    }

    $file = $_FILES['pdf_file'];
    
    // Validate PDF type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    if (finfo_file($finfo, $file['tmp_name']) !== 'application/pdf') {
      finfo_close($finfo);
      return $this->respond_json(['error' => 'Only PDF files are allowed'], 400);
    }
    finfo_close($finfo);

    // Setup directories
    $temp_dir = FCPATH . 'uploads/temp_pdf/';
    $json_dir = FCPATH . 'uploads/pdf_context/';
    foreach ([$temp_dir, $json_dir] as $dir) {
      if (!file_exists($dir)) mkdir($dir, 0755, true);
    }

    // Save temp file
    $temp_path = $temp_dir . uniqid('pdf_') . '.pdf';
    if (!move_uploaded_file($file['tmp_name'], $temp_path)) {
      return $this->respond_json(['error' => 'Failed to process file'], 500);
    }

    // Run Python extraction script
    $script = APPPATH . 'scripts/extract_pdf_structure.py';
    if (!file_exists($script)) {
      unlink($temp_path);
      return $this->respond_json(['error' => 'Extraction script not found'], 500);
    }

    log_message('debug', 'Starting PDF extraction with Python: ' . $temp_path);
    log_message('debug', 'Python path: ' . $python_path);
    log_message('debug', 'Script path: ' . $script);

    $output = shell_exec(sprintf('"%s" %s %s 2>&1', 
      $python_path,
      escapeshellarg($script), 
      escapeshellarg($temp_path)
    ));
    log_message('debug', "Python output: " . $output);
    unlink($temp_path); // Cleanup

    // Parse result
    if (!$output) {
      log_message('error', 'Python returned no output');
      return $this->respond_json(['error' => 'Python extraction failed'], 500);
    }

    $result = json_decode($output, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
      log_message('error', 'JSON decode error: ' . json_last_error_msg() . ' - Output: ' . substr($output, 0, 500));
      return $this->respond_json(['error' => 'Extraction failed: Invalid JSON response'], 500);
    }
    
    if (isset($result['error'])) {
      log_message('error', 'Python script error: ' . $result['error']);
      return $this->respond_json(['error' => $result['error']], 500);
    }

    // Save JSON
    $result['source_file'] = $file['name'];
    $result['extracted_at'] = date('Y-m-d H:i:s');
    
    $filename = pathinfo($file['name'], PATHINFO_FILENAME) . '_' . date('Ymd_His') . '.json';
    $json_path = $json_dir . $filename;
    
    if (!file_put_contents($json_path, json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))) {
      return $this->respond_json(['error' => 'Failed to save JSON'], 500);
    }

    log_message('debug', 'PDF extraction successful, saved to: ' . $json_path);

    $this->respond_json([
      'success' => true,
      'json_file' => $filename,
      'json_path' => 'uploads/pdf_context/' . $filename,
      'csrf_hash' => $this->security->get_csrf_hash()
    ]);
  }
  
  /**
   * Get Python executable path from config or auto-detect
   * @return string|false Python path or false if not found
   */
  private function get_python_path()
  {
    // Check if configured in constants.php
    if (defined('PYTHON_EXECUTABLE_PATH') && PYTHON_EXECUTABLE_PATH !== 'auto') {
      $configured_path = PYTHON_EXECUTABLE_PATH;
      log_message('debug', 'Using configured Python path: ' . $configured_path);
      
      // Try to resolve relative paths if needed
      if (strpos($configured_path, '/') === 0 && !file_exists($configured_path)) {
        // Try common Linux paths
        $alternative_paths = [
          '/usr/bin/' . basename($configured_path),
          '/usr/local/bin/' . basename($configured_path),
          '/opt/' . basename($configured_path),
        ];
        
        foreach ($alternative_paths as $alt_path) {
          if (file_exists($alt_path)) {
            log_message('debug', 'Resolved Python path to: ' . $alt_path);
            return $alt_path;
          }
        }
      }
      
      if (file_exists($configured_path)) {
        return $configured_path;
      }
      log_message('error', 'Configured Python path does not exist: ' . $configured_path);
    }
    
    // Auto-detect Python with enhanced logging
    $possible_paths = [];
    
    // Detect OS
    $is_windows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
    log_message('debug', 'OS detected: ' . ($is_windows ? 'Windows' : 'Linux/Unix'));
    
    if ($is_windows) {
      // Windows paths
      $possible_paths = [
        'C:\\Users\\AbdelfattahAllam\\AppData\\Local\\Python\\bin\\python.exe',
        'C:\\Python312\\python.exe',
        'C:\\Python311\\python.exe',
        'C:\\Python310\\python.exe',
        'C:\\Python39\\python.exe',
        'C:\\Program Files\\Python312\\python.exe',
        'C:\\Program Files\\Python311\\python.exe',
        'C:\\Program Files\\Python310\\python.exe',
        getenv('LOCALAPPDATA') . '\\Programs\\Python\\Python312\\python.exe',
        getenv('LOCALAPPDATA') . '\\Programs\\Python\\Python311\\python.exe',
        getenv('LOCALAPPDATA') . '\\Programs\\Python\\Python310\\python.exe',
      ];
    } else {
      // Linux/Unix paths - include the specific path from the user
      $possible_paths = [
        '/home/ubuntu/.pyenv/shims/python3',
        '/usr/bin/python3',
        '/usr/local/bin/python3',
        '/usr/bin/python',
        '/usr/local/bin/python',
        '/opt/python3/bin/python3',
        '/usr/bin/python2',
        '/usr/local/bin/python2',
      ];
    }
    
    log_message('debug', 'Checking Python paths: ' . implode(', ', $possible_paths));
    
    // Try each path with enhanced logging
    foreach ($possible_paths as $path) {
      if ($path && file_exists($path)) {
        log_message('debug', 'Found Python at: ' . $path);
        return $path;
      }
      log_message('debug', 'Python not found at: ' . $path);
    }
    
    // Try to find Python using shell command with enhanced logging
    if ($is_windows) {
      $where_output = shell_exec('where python 2>nul');
      if ($where_output) {
        $paths = explode("\n", trim($where_output));
        if (!empty($paths[0]) && file_exists(trim($paths[0]))) {
          log_message('debug', 'Found Python via where command: ' . trim($paths[0]));
          return trim($paths[0]);
        }
      }
    } else {
      $which_output = shell_exec('which python3 2>/dev/null');
      if ($which_output) {
        $paths = explode("\n", trim($which_output));
        foreach ($paths as $path) {
          $path = trim($path);
          if (file_exists($path)) {
            log_message('debug', 'Found Python3 via which command: ' . $path);
            return $path;
          }
        }
      }
      
      $which_output = shell_exec('which python 2>/dev/null');
      if ($which_output) {
        $paths = explode("\n", trim($which_output));
        foreach ($paths as $path) {
          $path = trim($path);
          if (file_exists($path)) {
            log_message('debug', 'Found Python via which command: ' . $path);
            return $path;
          }
        }
      }
    }
    
    log_message('error', 'Python not found in any known location');
    return false;
  }
  
  /**
   * Extract headings from page texts using regex patterns
   */
/**
   * Generate outline schemas using DeepSeek API
   */
  public function generate_outline_schemas()
  {
    // Increase timeout for schema generation
    ini_set('max_execution_time', 300); // 5 minutes for schema generation
    set_time_limit(300); // Also set time limit
    
    // Send headers to prevent timeout
    if (ob_get_level() == 0) {
      ob_start();
    }
    
    // Send initial response headers to keep connection alive
    header('Content-Type: application/json');
    header('X-Accel-Buffering: no'); // Disable nginx buffering
    header('Connection: keep-alive');
    
    // Flush output buffer to prevent timeout
    if (ob_get_level() > 0) {
      ob_flush();
      flush();
    }

    // Auth check
    if (!$this->session->userdata('teacher_login') &&
        !$this->session->userdata('admin_login') &&
        !$this->session->userdata('superadmin_login')) {
      return $this->respond_json(['error' => 'Unauthorized'], 403);
    }

    // Get POST data
    $course_id = $this->input->post('course_id') ?? 0;
    $pdf_json_path = $this->input->post('pdf_json_path') ?? '';
    $outline_rules_json = $this->input->post('outline_rules') ?? '{}';
    $outline_rules = json_decode($outline_rules_json, true) ?? [];

    // Load PDF extracted content
    $pdf_json_file = FCPATH . $pdf_json_path;
    if (!file_exists($pdf_json_file)) {
      return $this->respond_json(['error' => 'PDF context file not found'], 404);
    }

    $pdf_content = json_decode(file_get_contents($pdf_json_file), true);
    if (!$pdf_content) {
      // Clean up invalid file
      unlink($pdf_json_file);
      return $this->respond_json(['error' => 'Invalid PDF context file'], 400);
    }

    // Get course context if available
    $course_context = [];
    if ($course_id > 0) {
      $course = $this->db->get_where('course', ['id' => $course_id])->row_array();
      if ($course) {
        $course_context = [
          'prerequisites' => $course['prerequisites'] ?? '',
          'field_of_activity' => $course['field_of_activity'] ?? '',
          'course_style' => $course['course_style'] ?? ''
        ];
      }
    }

    // Build optimized prompt for Local LLM
    $prompt = $this->build_outline_prompt($pdf_content, $outline_rules, $course_context);
    
    log_message('debug', 'Starting Local LLM API call for outline generation');

    // Call Local LLM API for outline generation
    $deepseek_response = $this->call_deepseek_api($prompt, 300); // 5 minutes timeout for local LLM

    if (isset($deepseek_response['error'])) {
      log_message('error', 'Local LLM API Error: ' . $deepseek_response['error']);
      // Clean up JSON file on error
      if (file_exists($pdf_json_file)) {
        unlink($pdf_json_file);
      }
      return $this->respond_json(['error' => 'API Error: ' . $deepseek_response['error']], 500);
    }

    if (!isset($deepseek_response['content'])) {
      log_message('error', 'No response content from Local LLM API');
      // Clean up JSON file on error
      if (file_exists($pdf_json_file)) {
        unlink($pdf_json_file);
      }
      return $this->respond_json(['error' => 'No response content from API'], 500);
    }

    log_message('debug', 'Local LLM API response received, parsing schemas');

    // Parse DeepSeek response to extract 3 schemas
    $schemas = $this->parse_deepseek_schemas($deepseek_response);

    if (empty($schemas) || !is_array($schemas)) {
      log_message('error', 'Failed to parse DeepSeek schemas');
      // Clean up JSON file on error
      if (file_exists($pdf_json_file)) {
        unlink($pdf_json_file);
      }
      return $this->respond_json(['error' => 'Failed to parse API response'], 500);
    }

    log_message('debug', 'Schemas parsed successfully, count: ' . count($schemas));

    // Clean up the JSON file after successful generation
    if (file_exists($pdf_json_file)) {
      unlink($pdf_json_file);
    }

    // Reset PHP execution time to default
    ini_restore('max_execution_time');

    $this->respond_json([
      'success' => true,
      'schemas' => $schemas
    ]);
  }

  /**
   * Build prompt for DeepSeek API
   */
  private function build_outline_prompt($pdf_content, $outline_rules, $course_context)
  {
    // Parse lessons per section range (e.g., "2-3" => min=2, max=3)
    $lessons_range = explode('-', $outline_rules['lessonsPerSection'] ?? '2-3');
    $lessons_min = $lessons_range[0] ?? 2;
    $lessons_max = $lessons_range[1] ?? 3;

    // Parse max sections range (e.g., "3-6" => min=3, max=6)
    $sections_range = explode('-', $outline_rules['maxSections'] ?? '3-6');
    $sections_min = intval($sections_range[0] ?? 3);
    $sections_max = intval($sections_range[1] ?? 6);

    // Calculate max CORE sections based on whether intro/outro are enabled
    $include_intro = $outline_rules['includeIntro'] ?? true;
    $include_outro = $outline_rules['includeConclusion'] ?? true;

    $intro_outro_count = ($include_intro ? 1 : 0) + ($include_outro ? 1 : 0);
    $max_core_sections = $sections_max - $intro_outro_count;

    // Ensure at least 1 core section if intro/outro take all slots
    $max_sections = max(1, $max_core_sections);
    
    // Map numbering mode
    $numbering_map = [
      'none' => 'NONE',
      'auto_123' => 'AUTO_NUMERIC',
      'auto_nested' => 'AUTO_DECIMAL',
      'from_pdf' => 'FROM_SOURCE'
    ];
    $numbering_mode = $numbering_map[$outline_rules['numbering'] ?? 'none'] ?? 'NONE';
    
    // Map quiz policy
    $quiz_policy_map = [
      'none' => 'MANUAL',
      'per_section' => 'PER_SECTION',
      'per_lesson' => 'EVERY_N_LESSONS',
      'final' => 'MANUAL'
    ];
    $quiz_policy = $quiz_policy_map[$outline_rules['quizFrequency'] ?? 'per_section'] ?? 'PER_SECTION';
    $quiz_every_n = $quiz_policy === 'EVERY_N_LESSONS' ? 1 : 'null';
    
    // Map difficulty
    $difficulty_map = [
      'easy' => 'EASY',
      'medium' => 'MEDIUM',
      'hard' => 'HARD'
    ];
    $quiz_difficulty = $difficulty_map[$outline_rules['difficulty'] ?? 'medium'] ?? 'MEDIUM';
    
    // Map language
    $language_map = [
      'french' => 'fr',
      'english' => 'en',
      'spanish' => 'es',
      'dutch' => 'nl',
      'arabic' => 'ar'
    ];
    $language = $language_map[$outline_rules['language'] ?? 'french'] ?? 'fr';
    
    // Include intro/outro
    $include_intro = ($outline_rules['includeIntro'] ?? true) ? 'true' : 'false';
    $include_outro = ($outline_rules['includeConclusion'] ?? true) ? 'true' : 'false';
    
    // Quiz questions count
    $quiz_questions = $outline_rules['questionsCount'] ?? 10;
    
    // Convert PDF content to JSON string
    $pdf_json = json_encode($pdf_content, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

    $prompt = <<<PROMPT
      Generate 3 different course outline proposals from this source document.

      RULES:
      - max_core_sections: {$max_sections}
      - lessons_per_section: {$lessons_min}-{$lessons_max}
      - include_intro: {$include_intro}
      - include_outro: {$include_outro}
      - numbering: {$numbering_mode}
      - quiz_policy: {$quiz_policy}
      - language: {$language}

      CONTENT CLEANING:
      - Ignore API docs, tables of contents, noise headings
      - Keep logical order, prefer H1→sections, H2→lessons

      INTRO/OUTRO:
      If include_intro=true: ONE INTRO section with {$lessons_min}-{$lessons_max} lessons
      If include_outro=true: ONE OUTRO section with {$lessons_min}-{$lessons_max} lessons

      OUTPUT (STRICT JSON ONLY — NO MARKDOWN, NO EXTRA TEXT)
      Return exactly this JSON structure:

      {
        "courseTitle": "<infer from source>",
        "numberingMode": "{$numbering_mode}",
        "quizPolicy": "{$quiz_policy}",
        "proposals": [
          {
            "id": "proposal_1",
            "label": "Design schéma 1",
            "strategy": "<1 short sentence: how this outline is organized>",
            "sections": [ ... ]
          },
          {
            "id": "proposal_2",
            "label": "Design schéma 2",
            "strategy": "<1 short sentence>",
            "sections": [ ... ]
          },
          {
            "id": "proposal_3",
            "label": "Design schéma 3",
            "strategy": "<1 short sentence>",
            "sections": [ ... ]
          }
        ]
      }

      SECTION OBJECT
      {
        "id": "sec_01",
        "type": "INTRO|CORE|OUTRO",
        "order": 1,
        "title": "…",
        "sourcePages": {"start": 1, "end": 5},
        "children": [
          {
            "id": "les_01_01",
            "type": "LESSON",
            "order": 1,
            "title": "…",
            "subtitle": "…",
            "sourceHeadings": ["…"],
            "sourcePages": {"start": 1, "end": 2}
          },
          {
            "id": "quiz_01",
            "type": "QUIZ",
            "order": 99,
            "title": "Quiz – …",
            "questionsCount": {$quiz_questions},
            "difficulty": "{$quiz_difficulty}"
          }
        ]
      }

      ID RULES
      - Sections: sec_01, sec_02, ...
      - Lessons: les_01_01 (section 01 lesson 01), ...
      - Quiz: quiz_01 (section 01 quiz)
      IDs must be unique WITHIN EACH PROPOSAL.

      DIFFERENTIATION REQUIREMENTS (VERY IMPORTANT)
      Each proposal must be meaningfully different:

      Proposal 1 (Balanced / Default):
      - Closely follows PDF flow (H1/H2), minimal rewriting.
      - Standard granularity.
      - STRICTLY RESPECT: lessons_per_section_min ({$lessons_min}) to lessons_per_section_max ({$lessons_max}) lessons per section

      Proposal 2 (Consolidated):
      - Fewer sections (combine adjacent H1 topics).
      - Keep lessons within limits by merging similar H2.
      - Titles are more thematic.
      - STRICTLY RESPECT: lessons_per_section_min ({$lessons_min}) to lessons_per_section_max ({$lessons_max}) lessons per section

      Proposal 3 (More granular / Learning path):
      - More sections up to max_sections.
      - Break down broad H1 into clearer learning steps.
      - If quiz_policy=PER_SECTION: quizzes focus on key takeaways.
      - If quiz_policy=EVERY_N_LESSONS: quizzes placed exactly every N lessons.
      - STRICTLY RESPECT: lessons_per_section_min ({$lessons_min}) to lessons_per_section_max ({$lessons_max}) lessons per section

      VALIDATION:
      - Max {$max_sections} core sections
      - {$lessons_min}-{$lessons_max} lessons per section
      - Valid JSON output only

      NOW PROCESS THIS SOURCE JSON:
      {$pdf_json}
      PROMPT;

    return $prompt;
  }

  /**
   * Build optimized outline prompt for Local LLM API
   * Simplified and more direct prompt for better performance with local models
   */
  private function build_outline_prompt_llm($pdf_content, $outline_rules, $course_context)
  {
    // Parse lessons per section range
    $lessons_range = explode('-', $outline_rules['lessonsPerSection'] ?? '2-3');
    $lessons_min = intval($lessons_range[0] ?? 2);
    $lessons_max = intval($lessons_range[1] ?? 3);

    // Parse max sections range
    $sections_range = explode('-', $outline_rules['maxSections'] ?? '3-6');
    $sections_max = intval($sections_range[1] ?? 6);

    // Calculate max CORE sections
    $include_intro = $outline_rules['includeIntro'] ?? true;
    $include_outro = $outline_rules['includeConclusion'] ?? true;
    $intro_outro_count = ($include_intro ? 1 : 0) + ($include_outro ? 1 : 0);
    $max_sections = max(1, $sections_max - $intro_outro_count);
    
    // Map settings
    $numbering_mode = match($outline_rules['numbering'] ?? 'none') {
      'auto_123' => 'AUTO_NUMERIC',
      'auto_nested' => 'AUTO_DECIMAL',
      'from_pdf' => 'FROM_SOURCE',
      default => 'NONE'
    };
    
    $quiz_policy = match($outline_rules['quizFrequency'] ?? 'per_section') {
      'per_section' => 'PER_SECTION',
      'per_lesson' => 'EVERY_N_LESSONS',
      default => 'MANUAL'
    };
    
    $quiz_difficulty = strtoupper($outline_rules['difficulty'] ?? 'medium');
    
    $language = match($outline_rules['language'] ?? 'french') {
      'english' => 'en',
      'spanish' => 'es',
      'dutch' => 'nl',
      'arabic' => 'ar',
      default => 'fr'
    };
    
    $quiz_questions = intval($outline_rules['questionsCount'] ?? 10);
    $include_intro_str = $include_intro ? 'true' : 'false';
    $include_outro_str = $include_outro ? 'true' : 'false';
    
    // Simplify PDF content for local LLM (reduce token usage)
    $simplified_content = [
      'title' => $pdf_content['title'] ?? 'Untitled',
      'total_pages' => $pdf_content['total_pages'] ?? 0,
      'toc' => array_slice($pdf_content['toc'] ?? [], 0, 50), // Limit TOC entries
      'headings' => array_slice($pdf_content['headings'] ?? [], 0, 100), // Limit headings
    ];
    
    $pdf_json = json_encode($simplified_content, JSON_UNESCAPED_UNICODE);

    $prompt = <<<PROMPT
      Tu es un expert en conception pédagogique. Génère 3 propositions de plan de cours à partir du document source.

      RÈGLES:
      - Sections principales max: {$max_sections}
      - Leçons par section: {$lessons_min} à {$lessons_max}
      - Inclure intro: {$include_intro_str}
      - Inclure conclusion: {$include_outro_str}
      - Numérotation: {$numbering_mode}
      - Quiz: {$quiz_policy}
      - Langue: {$language}

      RÉPONDS UNIQUEMENT EN JSON VALIDE (pas de markdown, pas de texte avant/après).

      Format de réponse:
      {
        "courseTitle": "Titre du cours",
        "numberingMode": "{$numbering_mode}",
        "quizPolicy": "{$quiz_policy}",
        "proposals": [
          {
            "id": "proposal_1",
            "label": "Schéma équilibré",
            "strategy": "Description courte",
            "sections": [
              {
                "id": "sec_01",
                "type": "INTRO",
                "order": 1,
                "title": "Introduction",
                "sourcePages": {"start": 1, "end": 2},
                "children": [
                  {
                    "id": "les_01_01",
                    "type": "LESSON",
                    "order": 1,
                    "title": "Titre leçon",
                    "subtitle": "Sous-titre",
                    "sourceHeadings": ["Heading"],
                    "sourcePages": {"start": 1, "end": 1}
                  },
                  {
                    "id": "quiz_01",
                    "type": "QUIZ",
                    "order": 99,
                    "title": "Quiz",
                    "questionsCount": {$quiz_questions},
                    "difficulty": "{$quiz_difficulty}"
                  }
                ]
              }
            ]
          },
          {
            "id": "proposal_2",
            "label": "Schéma consolidé",
            "strategy": "Moins de sections, plus thématique",
            "sections": []
          },
          {
            "id": "proposal_3",
            "label": "Schéma détaillé",
            "strategy": "Plus granulaire, parcours d'apprentissage",
            "sections": []
          }
        ]
      }

      TYPES DE SECTIONS:
      - INTRO: 1 section d'introduction (si include_intro=true)
      - CORE: Sections principales du contenu
      - OUTRO: 1 section de conclusion (si include_outro=true)

      IDs UNIQUES:
      - Sections: sec_01, sec_02...
      - Leçons: les_01_01 (section 01, leçon 01)
      - Quiz: quiz_01 (section 01)

      3 PROPOSITIONS DIFFÉRENTES:
      1. Équilibré: Suit le flux du PDF
      2. Consolidé: Moins de sections, fusionne les sujets similaires
      3. Détaillé: Plus de sections, décompose en étapes claires

      DOCUMENT SOURCE:
      {$pdf_json}

      Génère maintenant les 3 propositions complètes en JSON:
      PROMPT;

    return $prompt;
  }

  /**
   * Generate content for multiple lessons in a single API call
   */
  private function generate_lessons_content_batch($lessons_data, $outline_rules)
  {
    if (empty($lessons_data)) {
      return [];
    }

    $language = $outline_rules['language'] ?? 'french';
    $language_map = [
      'french' => 'fr',
      'english' => 'en',
      'spanish' => 'es',
      'dutch' => 'nl',
      'arabic' => 'ar'
    ];
    $lang_code = $language_map[$language] ?? 'fr';

    $tone_map = [
      'very_concise' => 'Very concise (bullet points)',
      'concise_technical' => 'Concise & technical',
      'neutral_professional' => 'Neutral & professional',
      'pedagogical_progressive' => 'Pedagogical & progressive',
      'coach_motivating' => 'Coach / motivating',
      'conversational' => 'Conversational (friendly)',
      'corporate_institutional' => 'Corporate / institutional',
      'expert_best_practices' => 'Expert / best practices',
      'storytelling' => 'Storytelling',
      'action_oriented' => 'Action oriented (checklists)',
      'compliance_oriented' => 'Compliance oriented (rigorous)',
      'support_troubleshooting' => 'Support / troubleshooting'
    ];
    $tone = $tone_map[$outline_rules['tone'] ?? 'concise_technical'] ?? 'Concise & technical';

    $audience = $outline_rules['audience'] ?? 'intermediate';

    // Build the batch prompt
    $lessons_list = '';
    foreach ($lessons_data as $index => $lesson) {
      $lesson_num = $index + 1;
      $lessons_list .= "\nLESSON {$lesson_num}:\n";
      $lessons_list .= "- Title: \"{$lesson['title']}\"\n";
      $lessons_list .= "- Section: \"{$lesson['section_context']}\"\n";
      $lessons_list .= "- Course: \"{$lesson['course_context']}\"\n";
    }

    $prompt = <<<PROMPT
Generate comprehensive content for {$language} language courses. Target audience: {$audience} level learners. Use {$tone} tone.

CONTENT REQUIREMENTS:
1. Write all content in {$language}
2. Include 2-3 practical examples per lesson
3. Structure with headings, paragraphs, and bullet points
4. End each lesson with 3-5 key takeaways
5. Keep each lesson concise but complete (400-800 words)

{$lessons_list}

OUTPUT: Return content in this exact format:

=== LESSON 1 ===
[HTML content for lesson 1]
=== END LESSON 1 ===

=== LESSON 2 ===
[HTML content for lesson 2]
=== END LESSON 2 ===

Do not include JSON, just use the === LESSON X === markers.
PROMPT;

    $response = $this->call_deepseek_api($prompt, 150); // Extended timeout for batch processing

    if (!isset($response['content']) || empty($response['content'])) {
      log_message('error', "No content received from batch API call");
      return [];
    }

    $content = $response['content'];

    // Clean up escape sequences - convert literal \n to actual newlines then remove them
    $content = str_replace('\\n', '', $content); // Remove literal \n sequences
    $content = str_replace('\n', '', $content);  // Also try without double backslash
    $content = stripslashes($content);

    // Parse response using markers instead of JSON
    $result = [];

    foreach ($lessons_data as $index => $lesson) {
      $lesson_num = $index + 1;

      // Extract content between markers
      $pattern = "/=== LESSON {$lesson_num} ===(.*?)(?:=== LESSON " . ($lesson_num + 1) . " ===|=== END LESSON {$lesson_num} ===|$)/s";
      if (preg_match($pattern, $content, $matches)) {
        $lesson_content = trim($matches[1]);

        // Clean up the HTML content
        if (!empty($lesson_content)) {
          // Remove any remaining literal \n sequences
          $lesson_content = str_replace('\\n', '', $lesson_content);
          $lesson_content = str_replace('\n', '', $lesson_content);
          $lesson_content = preg_replace('/\s*\n\s*/', '', $lesson_content); // Remove actual newlines
          $lesson_content = preg_replace('/>\s+</', '><', $lesson_content); // Remove whitespace between tags
          $lesson_content = preg_replace('/\s+/', ' ', $lesson_content); // Normalize multiple spaces to single

          $lesson_content = trim($lesson_content);
          $result[$lesson_num] = $lesson_content;
        }
      }
    }
    return $result;
  }

  /**
   * Generate content for a single lesson using AI (fallback method)
   */
  private function generate_lesson_content($lesson_title, $section_context, $course_context, $outline_rules)
  {
    // Use batch method with single lesson
    $lessons_data = [[
      'title' => $lesson_title,
      'section_context' => $section_context,
      'course_context' => $course_context
    ]];

    $batch_result = $this->generate_lessons_content_batch($lessons_data, $outline_rules);
    return $batch_result[1] ?? '';
  }

  /**
   * Generate quiz questions using AI
   */
  private function generate_quiz_questions($quiz_title, $section_context, $course_context, $outline_rules, $question_count = 10)
  {
    $language = $outline_rules['language'] ?? 'french';
    $difficulty = $outline_rules['difficulty'] ?? 'medium';

    $language_map = [
      'french' => 'fr',
      'english' => 'en',
      'spanish' => 'es',
      'dutch' => 'nl',
      'arabic' => 'ar'
    ];
    $lang_code = $language_map[$language] ?? 'fr';

    $prompt = <<<PROMPT
You are an expert quiz designer. Generate {$question_count} multiple-choice questions for a quiz.

QUIZ DETAILS:
- Title: "{$quiz_title}"
- Section Context: "{$section_context}"
- Course Context: "{$course_context}"
- Difficulty: "{$difficulty}"
- Language: "{$language}" ({$lang_code})

REQUIREMENTS:
1. Write in {$language} language
2. Create {$question_count} multiple-choice questions (4 options: A, B, C, D)
3. {$difficulty} difficulty level
4. One correct answer per question, 3 plausible wrong answers
5. Test understanding of key concepts

OUTPUT: Valid JSON array only, this exact format:
[{"question": "Question?", "options": ["A) Opt1", "B) Opt2", "C) Opt3", "D) Opt4"], "correct_answer": "A"}]
PROMPT;

    $response = $this->call_deepseek_api($prompt);

    if (isset($response['content'])) {
      $content = $response['content'];

      // Clean up Unicode escape sequences
      $content = preg_replace_callback('/\\\\u([0-9a-fA-F]{4})/', function($match) {
        return mb_convert_encoding(pack('H*', $match[1]), 'UTF-8', 'UCS-2BE');
      }, $content);

      // Remove any remaining escape sequences
      $content = stripslashes($content);

      $questions = json_decode($content, true);
      return is_array($questions) ? $questions : [];
    }

    return [];
  }

  /**
   * Call DeepSeek API
   */
  private function call_deepseek_api($prompt, $timeout_seconds = 60)
  {
    $api_key = 'sk-249b9057de6f47029c596004558ab8ce'; // DeepSeek API key
    $api_url = 'https://api.deepseek.com/v1/chat/completions';

    $data = [
      'model' => 'deepseek-chat',
      'messages' => [
        [
          'role' => 'system',
          'content' => 'You are an expert instructional designer for EdTech. You MUST respond with valid JSON ONLY. No markdown, no code blocks, no explanations - just pure JSON that can be parsed directly.'
        ],
        [
          'role' => 'user',
          'content' => $prompt
        ]
      ],
      'temperature' => 0.7,
      'max_tokens' => 8192, // Maximum allowed by DeepSeek API
      'response_format' => ['type' => 'json_object']
    ];

    // Retry mechanism for connection errors
    $max_retries = 3;
    $retry_delay = 2; // seconds

    for ($attempt = 1; $attempt <= $max_retries; $attempt++) {
      error_log("DeepSeek API Attempt {$attempt}/{$max_retries}");

      $ch = curl_init($api_url);
      curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($data),
        CURLOPT_HTTPHEADER => [
          'Content-Type: application/json',
          'Authorization: Bearer ' . $api_key
        ],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => $timeout_seconds,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, // Force HTTP/1.1 to avoid HTTP/2 issues
        CURLOPT_FOLLOWLOCATION => true, // Follow redirects
        CURLOPT_MAXREDIRS => 3, // Limit redirects
        CURLOPT_CONNECTTIMEOUT => 60, // Increased connection timeout from 30 to 60 seconds
        CURLOPT_SSL_VERIFYPEER => false, // Disable SSL verification if needed
        CURLOPT_SSL_VERIFYHOST => 2,
        CURLOPT_FORBID_REUSE => false, // Allow connection reuse
        CURLOPT_FRESH_CONNECT => ($attempt > 1), // Force new connection on retries
        CURLOPT_BUFFERSIZE => 1048576, // 1MB buffer for large responses
        CURLOPT_TCP_NODELAY => true, // Disable Nagle's algorithm for better performance
        CURLOPT_FAILONERROR => false, // Don't fail on HTTP errors
        CURLOPT_ENCODING => 'gzip, deflate', // Accept compressed responses
      ]);

      $response = curl_exec($ch);
      $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
      $curl_error = curl_error($ch);
      $curl_errno = curl_errno($ch);
      curl_close($ch);

      // Debug logging
      error_log('DeepSeek API HTTP Code: ' . $http_code);
      error_log('DeepSeek API Response (first 1000 chars): ' . substr($response, 0, 1000));

      // Check for connection errors that are retryable
      if ($curl_error) {
        error_log('DeepSeek API cURL Error (Attempt ' . $attempt . '): ' . $curl_error);
        
        // Check for timeout errors specifically
        if ($curl_errno == CURLE_OPERATION_TIMEOUTED || 
            $curl_errno == CURLE_OPERATION_TIMEDOUT ||
            strpos($curl_error, 'timeout') !== false ||
            strpos($curl_error, 'timed out') !== false) {
          error_log('DeepSeek API Timeout detected');
          
          // Don't retry on timeout, return error immediately
          return ['error' => 'API request timed out. The request took too long to process. Please try again with a smaller PDF or simpler outline rules.'];
        }

        // Retry on other connection errors
        if ($attempt < $max_retries) {
          error_log('Retrying in ' . $retry_delay . ' seconds...');
          sleep($retry_delay);
          continue;
        }

        return ['error' => 'API connection failed: ' . $curl_error];
      }
      
      // Check for timeout via HTTP code (504 Gateway Timeout)
      if ($http_code == 504) {
        error_log('DeepSeek API Gateway Timeout (504)');
        return ['error' => 'Gateway timeout. The request took too long. Please try again with a smaller PDF or simpler outline rules.'];
      }

      if ($http_code !== 200) {
        $error_data = json_decode($response, true);
        $error_msg = 'API error: ' . ($error_data['error']['message'] ?? 'Unknown error');
        error_log('DeepSeek API HTTP Error: ' . $error_msg);
        error_log('Full error response: ' . $response);
        return ['error' => $error_msg];
      }

      $result = json_decode($response, true);

      if (!isset($result['choices'][0]['message']['content'])) {
        error_log('DeepSeek API Invalid Response: ' . print_r($result, true));
        return ['error' => 'Invalid API response'];
      }

      $content = $result['choices'][0]['message']['content'];
      error_log('DeepSeek API Content length: ' . strlen($content));
      error_log('DeepSeek API Content (first 500 chars): ' . substr($content, 0, 500));

      return ['content' => $content];
    }

    return ['error' => 'API connection failed after ' . $max_retries . ' attempts'];
  }

  /**
   * Call Local LLM API (LM Studio / Ollama / vLLM compatible)
   * Server: http://154.146.250.62:7000/v1/chat/completions
   */
  private function call_local_llm_api($prompt, $timeout_seconds = 120)
  {
    $api_url = $this->localLlmUrl;

    $data = [
      'messages' => [
        [
          'role' => 'system',
          'content' => 'You are an expert instructional designer for EdTech. You MUST respond with valid JSON ONLY. No markdown, no code blocks, no explanations - just pure JSON that can be parsed directly.'
        ],
        [
          'role' => 'user',
          'content' => $prompt
        ]
      ],
      'temperature' => 0.7,
      'max_tokens' => 8192,
      'top_p' => 0.95,
      'repetition_penalty' => 1.1,
      'stop' => null
    ];

    // Retry mechanism for connection errors
    $max_retries = 3;
    $retry_delay = 2; // seconds

    for ($attempt = 1; $attempt <= $max_retries; $attempt++) {
      error_log("Local LLM API Attempt {$attempt}/{$max_retries}");

      $ch = curl_init($api_url);
      curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($data),
        CURLOPT_HTTPHEADER => [
          'Content-Type: application/json'
          // No Authorization header needed for local API
        ],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => $timeout_seconds,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS => 3,
        CURLOPT_CONNECTTIMEOUT => 30,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_FORBID_REUSE => false,
        CURLOPT_FRESH_CONNECT => ($attempt > 1),
        CURLOPT_BUFFERSIZE => 1048576,
        CURLOPT_TCP_NODELAY => true,
        CURLOPT_FAILONERROR => false,
      ]);

      $response = curl_exec($ch);
      $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
      $curl_error = curl_error($ch);
      $curl_errno = curl_errno($ch);
      curl_close($ch);

      // Debug logging
      error_log('Local LLM API HTTP Code: ' . $http_code);
      error_log('Local LLM API Response (first 1000 chars): ' . substr($response, 0, 1000));

      // Check for connection errors
      if ($curl_error) {
        error_log('Local LLM API cURL Error (Attempt ' . $attempt . '): ' . $curl_error);
        
        // Check for timeout errors
        if ($curl_errno == CURLE_OPERATION_TIMEOUTED || 
            $curl_errno == CURLE_OPERATION_TIMEDOUT ||
            strpos($curl_error, 'timeout') !== false ||
            strpos($curl_error, 'timed out') !== false) {
          error_log('Local LLM API Timeout detected');
          return ['error' => 'API request timed out. Please try again with a smaller PDF.'];
        }

        // Retry on other connection errors
        if ($attempt < $max_retries) {
          error_log('Retrying in ' . $retry_delay . ' seconds...');
          sleep($retry_delay);
          continue;
        }

        return ['error' => 'API connection failed: ' . $curl_error];
      }
      
      // Check for HTTP errors
      if ($http_code == 504) {
        error_log('Local LLM API Gateway Timeout (504)');
        return ['error' => 'Gateway timeout. Please try again with a smaller PDF.'];
      }

      if ($http_code !== 200) {
        $error_data = json_decode($response, true);
        $error_msg = 'API error: ' . ($error_data['error']['message'] ?? $error_data['error'] ?? 'Unknown error');
        error_log('Local LLM API HTTP Error: ' . $error_msg);
        error_log('Full error response: ' . $response);
        return ['error' => $error_msg];
      }

      $result = json_decode($response, true);

      if (!isset($result['choices'][0]['message']['content'])) {
        error_log('Local LLM API Invalid Response: ' . print_r($result, true));
        return ['error' => 'Invalid API response'];
      }

      $content = $result['choices'][0]['message']['content'];
      error_log('Local LLM API Content length: ' . strlen($content));
      error_log('Local LLM API Content (first 500 chars): ' . substr($content, 0, 500));

      return ['content' => $content];
    }

    return ['error' => 'API connection failed after ' . $max_retries . ' attempts'];
  }

  /**
   * Parse DeepSeek response to extract schemas
   */
  private function parse_deepseek_schemas($response)
  {
    $content = $response['content'] ?? '';
    
    // Try to extract JSON from the response (handle markdown code blocks)
    if (preg_match('/```(?:json)?\s*(.*?)\s*```/s', $content, $matches)) {
      $json_str = $matches[1];
    } else {
      $json_str = $content;
    }
    
    // Clean up the JSON string
    $json_str = trim($json_str);
    
    $data = json_decode($json_str, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
      return $this->get_default_schemas();
    }
    
    // Handle new format with proposals array
    if (isset($data['proposals'])) {
      return $this->transform_proposals_to_schemas($data);
    }
    
    // Handle old format with schemas array
    if (isset($data['schemas'])) {
      return $data['schemas'];
    }

    return $this->get_default_schemas();
  }

  /**
   * Transform new DeepSeek proposals format to frontend schema format
   */
  private function transform_proposals_to_schemas($data)
  {
    $schemas = [];
    $courseTitle = $data['courseTitle'] ?? get_phrase('course');
    $numberingMode = $data['numberingMode'] ?? 'NONE'; // Get numbering mode from API response
    
    $badge_types = ['Balanced', 'Consolidated', 'Progressive Learning'];
    
    foreach ($data['proposals'] as $index => $proposal) {
      // Extract section titles
      $section_titles = [];
      $details = [];
      $total_lessons = 0;
      $total_quizzes = 0;
      
      foreach ($proposal['sections'] ?? [] as $section) {
        $section_titles[] = $section['title'] ?? '';
        
        // Count lessons and quizzes in children
        $lesson_count = 0;
        foreach ($section['children'] ?? [] as $child) {
          if (($child['type'] ?? '') === 'LESSON') {
            $lesson_count++;
            $total_lessons++;
          }
          if (($child['type'] ?? '') === 'QUIZ') {
            $total_quizzes++;
          }
        }
        
        $lesson_text = $lesson_count . ' ' . ($lesson_count > 1 ? get_phrase('lessons') : get_phrase('lesson'));
        $details[] = [
          'section' => $section['order'] . '. ' . ($section['title'] ?? ''),
          'lessons' => $lesson_text
        ];
      }
      
      $schemas[] = [
        'name' => $badge_types[$index] ?? get_phrase('schema') . ' ' . ($index + 1),
        'label' => $proposal['label'] ?? get_phrase('design_schema') . ' ' . ($index + 1),
        'description' => $proposal['strategy'] ?? '',
        'sections' => $section_titles,
        'total_sections' => count($proposal['sections'] ?? []),
        'total_quizzes' => $total_quizzes,
        'total_lessons' => $total_lessons,
        'details' => $details,
        'numberingMode' => $numberingMode, // Pass numbering mode to each schema
        'raw_data' => $proposal // Keep raw data for later use
      ];
    }
    
    return $schemas;
  }

  /**
   * Get default schemas if API fails
   */
  private function get_default_schemas()
  {
    return [
      [
        'name' => 'Balanced',
        'label' => 'Design schéma 1',
        'description' => 'Balanced plan that closely follows the PDF flow.',
        'sections' => ['Introduction', 'Core Concepts', 'Advanced Topics', 'Practical Applications', 'Assessment'],
        'total_sections' => 5,
        'total_quizzes' => 5,
        'total_lessons' => 11,
        'details' => [
          ['section' => '1. Introduction', 'lessons' => '2 lessons'],
          ['section' => '2. Core Concepts', 'lessons' => '3 lessons'],
          ['section' => '3. Advanced Topics', 'lessons' => '3 lessons'],
          ['section' => '4. Practical Applications', 'lessons' => '2 lessons'],
          ['section' => '5. Assessment', 'lessons' => '1 lesson']
        ],
        'raw_data' => [
          'sections' => [
            ['order' => 1, 'title' => 'Introduction', 'children' => [
              ['type' => 'LESSON', 'order' => 1, 'title' => 'Welcome'],
              ['type' => 'LESSON', 'order' => 2, 'title' => 'Overview'],
              ['type' => 'QUIZ', 'order' => 3, 'title' => 'Introduction Quiz', 'questions' => 5]
            ]],
            ['order' => 2, 'title' => 'Core Concepts', 'children' => [
              ['type' => 'LESSON', 'order' => 1, 'title' => 'Fundamentals'],
              ['type' => 'LESSON', 'order' => 2, 'title' => 'Key Principles'],
              ['type' => 'LESSON', 'order' => 3, 'title' => 'Best Practices'],
              ['type' => 'QUIZ', 'order' => 4, 'title' => 'Core Concepts Quiz', 'questions' => 5]
            ]],
            ['order' => 3, 'title' => 'Advanced Topics', 'children' => [
              ['type' => 'LESSON', 'order' => 1, 'title' => 'Advanced Techniques'],
              ['type' => 'LESSON', 'order' => 2, 'title' => 'Complex Scenarios'],
              ['type' => 'LESSON', 'order' => 3, 'title' => 'Expert Tips'],
              ['type' => 'QUIZ', 'order' => 4, 'title' => 'Advanced Quiz', 'questions' => 5]
            ]],
            ['order' => 4, 'title' => 'Practical Applications', 'children' => [
              ['type' => 'LESSON', 'order' => 1, 'title' => 'Real-world Examples'],
              ['type' => 'LESSON', 'order' => 2, 'title' => 'Case Studies'],
              ['type' => 'QUIZ', 'order' => 3, 'title' => 'Practice Quiz', 'questions' => 5]
            ]],
            ['order' => 5, 'title' => 'Assessment', 'children' => [
              ['type' => 'LESSON', 'order' => 1, 'title' => 'Final Review'],
              ['type' => 'QUIZ', 'order' => 2, 'title' => 'Final Assessment', 'questions' => 10]
            ]]
          ]
        ]
      ],
      [
        'name' => 'Consolidated',
        'label' => 'Design schéma 2',
        'description' => 'Consolidated plan that combines neighboring chapters into themes.',
        'sections' => ['Fundamentals', 'Core Module', 'Advanced Module', 'Conclusion'],
        'total_sections' => 4,
        'total_quizzes' => 4,
        'total_lessons' => 15,
        'details' => [
          ['section' => '1. Fundamentals', 'lessons' => '4 lessons'],
          ['section' => '2. Core Module', 'lessons' => '5 lessons'],
          ['section' => '3. Advanced Module', 'lessons' => '4 lessons'],
          ['section' => '4. Conclusion', 'lessons' => '2 lessons']
        ],
        'raw_data' => [
          'sections' => [
            ['order' => 1, 'title' => 'Fundamentals', 'children' => [
              ['type' => 'LESSON', 'order' => 1, 'title' => 'Getting Started'],
              ['type' => 'LESSON', 'order' => 2, 'title' => 'Basic Concepts'],
              ['type' => 'LESSON', 'order' => 3, 'title' => 'Setup Guide'],
              ['type' => 'LESSON', 'order' => 4, 'title' => 'First Steps'],
              ['type' => 'QUIZ', 'order' => 5, 'title' => 'Fundamentals Quiz', 'questions' => 5]
            ]],
            ['order' => 2, 'title' => 'Core Module', 'children' => [
              ['type' => 'LESSON', 'order' => 1, 'title' => 'Core Feature 1'],
              ['type' => 'LESSON', 'order' => 2, 'title' => 'Core Feature 2'],
              ['type' => 'LESSON', 'order' => 3, 'title' => 'Core Feature 3'],
              ['type' => 'LESSON', 'order' => 4, 'title' => 'Integration'],
              ['type' => 'LESSON', 'order' => 5, 'title' => 'Best Practices'],
              ['type' => 'QUIZ', 'order' => 6, 'title' => 'Core Module Quiz', 'questions' => 8]
            ]],
            ['order' => 3, 'title' => 'Advanced Module', 'children' => [
              ['type' => 'LESSON', 'order' => 1, 'title' => 'Advanced Topic 1'],
              ['type' => 'LESSON', 'order' => 2, 'title' => 'Advanced Topic 2'],
              ['type' => 'LESSON', 'order' => 3, 'title' => 'Performance'],
              ['type' => 'LESSON', 'order' => 4, 'title' => 'Optimization'],
              ['type' => 'QUIZ', 'order' => 5, 'title' => 'Advanced Quiz', 'questions' => 8]
            ]],
            ['order' => 4, 'title' => 'Conclusion', 'children' => [
              ['type' => 'LESSON', 'order' => 1, 'title' => 'Summary'],
              ['type' => 'LESSON', 'order' => 2, 'title' => 'Next Steps'],
              ['type' => 'QUIZ', 'order' => 3, 'title' => 'Final Assessment', 'questions' => 10]
            ]]
          ]
        ]
      ],
      [
        'name' => 'Progressive Learning',
        'label' => 'Design schéma 3',
        'description' => 'More detailed plan with step-by-step progression.',
        'sections' => ['Getting Started', 'Basic Concepts', 'Intermediate Skills', 'Advanced Techniques', 'Expert Level', 'Final Project', 'Review'],
        'total_sections' => 7,
        'total_quizzes' => 7,
        'total_lessons' => 16,
        'details' => [
          ['section' => '1. Getting Started', 'lessons' => '2 lessons'],
          ['section' => '2. Basic Concepts', 'lessons' => '3 lessons'],
          ['section' => '3. Intermediate Skills', 'lessons' => '3 lessons'],
          ['section' => '4. Advanced Techniques', 'lessons' => '3 lessons'],
          ['section' => '5. Expert Level', 'lessons' => '2 lessons'],
          ['section' => '6. Final Project', 'lessons' => '2 lessons'],
          ['section' => '7. Review', 'lessons' => '1 lesson']
        ],
        'raw_data' => [
          'sections' => [
            ['order' => 1, 'title' => 'Getting Started', 'children' => [
              ['type' => 'LESSON', 'order' => 1, 'title' => 'Welcome'],
              ['type' => 'LESSON', 'order' => 2, 'title' => 'Prerequisites'],
              ['type' => 'QUIZ', 'order' => 3, 'title' => 'Intro Quiz', 'questions' => 3]
            ]],
            ['order' => 2, 'title' => 'Basic Concepts', 'children' => [
              ['type' => 'LESSON', 'order' => 1, 'title' => 'Concept 1'],
              ['type' => 'LESSON', 'order' => 2, 'title' => 'Concept 2'],
              ['type' => 'LESSON', 'order' => 3, 'title' => 'Concept 3'],
              ['type' => 'QUIZ', 'order' => 4, 'title' => 'Basics Quiz', 'questions' => 5]
            ]],
            ['order' => 3, 'title' => 'Intermediate Skills', 'children' => [
              ['type' => 'LESSON', 'order' => 1, 'title' => 'Skill 1'],
              ['type' => 'LESSON', 'order' => 2, 'title' => 'Skill 2'],
              ['type' => 'LESSON', 'order' => 3, 'title' => 'Skill 3'],
              ['type' => 'QUIZ', 'order' => 4, 'title' => 'Intermediate Quiz', 'questions' => 5]
            ]],
            ['order' => 4, 'title' => 'Advanced Techniques', 'children' => [
              ['type' => 'LESSON', 'order' => 1, 'title' => 'Technique 1'],
              ['type' => 'LESSON', 'order' => 2, 'title' => 'Technique 2'],
              ['type' => 'LESSON', 'order' => 3, 'title' => 'Technique 3'],
              ['type' => 'QUIZ', 'order' => 4, 'title' => 'Advanced Quiz', 'questions' => 5]
            ]],
            ['order' => 5, 'title' => 'Expert Level', 'children' => [
              ['type' => 'LESSON', 'order' => 1, 'title' => 'Expert Topic 1'],
              ['type' => 'LESSON', 'order' => 2, 'title' => 'Expert Topic 2'],
              ['type' => 'QUIZ', 'order' => 3, 'title' => 'Expert Quiz', 'questions' => 5]
            ]],
            ['order' => 6, 'title' => 'Final Project', 'children' => [
              ['type' => 'LESSON', 'order' => 1, 'title' => 'Project Setup'],
              ['type' => 'LESSON', 'order' => 2, 'title' => 'Project Implementation'],
              ['type' => 'QUIZ', 'order' => 3, 'title' => 'Project Review', 'questions' => 5]
            ]],
            ['order' => 7, 'title' => 'Review', 'children' => [
              ['type' => 'LESSON', 'order' => 1, 'title' => 'Course Summary'],
              ['type' => 'QUIZ', 'order' => 2, 'title' => 'Final Assessment', 'questions' => 10]
            ]]
          ]
        ]
      ]
    ];
  }

  /**
   * Apply numbering to section title based on mode
   */
  private function apply_numbering_to_title($base_title, $order, $numbering_mode)
  {
    // First, remove any existing numbering from the title
    $clean_title = preg_replace('/^\d+[\.\)]\s*/', '', $base_title);
    $clean_title = trim($clean_title);

    switch ($numbering_mode) {
      case 'AUTO_NUMERIC':
        return $order . '. ' . $clean_title;
      case 'AUTO_DECIMAL':
        return $order . '.0 ' . $clean_title;
      case 'FROM_SOURCE':
        // Keep original numbering if present, otherwise fallback to auto numeric
        if (preg_match('/^\d+[\.\)]\s*/', $base_title)) {
          return $base_title; // Already has numbering - keep it
        }
        return $order . '. ' . $clean_title;
      case 'NONE':
      default:
        return $clean_title; // Return clean title without numbering
    }
  }

  /**
   * Apply selected outline schema - Create sections, lessons and quizzes
   */
  public function apply_outline_schema()
  {
    // Increase PHP execution time for content generation
    ini_set('max_execution_time', 300); // 5 minutes instead of 120 seconds

    $this->student_access_denied();

    $course_id = $this->input->post('course_id');
    $schema_json = $this->input->post('schema');
    $outline_rules_json = $this->input->post('outline_rules') ?? '{}';
    $outline_rules = json_decode($outline_rules_json, true) ?? [];

    if (empty($course_id) || empty($schema_json)) {
      echo json_encode(['error' => 'Missing required parameters']);
      return;
    }
    
    // Verify course exists and user has access
    $course = $this->lms_model->get_course_by_id($course_id);
    if (!$course) {
      echo json_encode(['error' => 'Course not found']);
      return;
    }
    
    $this->teacher_access($course_id);
    
    // Parse schema JSON
    $schema = json_decode($schema_json, true);
    if (!$schema) {
      echo json_encode(['error' => 'Invalid schema format']);
      return;
    }

    // Get numbering mode from schema
    $numbering_mode = $schema['numberingMode'] ?? 'NONE';

    // Get raw_data which contains the detailed structure
    $raw_data = isset($schema['raw_data']) ? $schema['raw_data'] : $schema;
    $sections = isset($raw_data['sections']) ? $raw_data['sections'] : [];
    
    if (empty($sections)) {
      echo json_encode(['error' => 'No sections found in schema']);
      return;
    }
    
    // Track created items
    $created_sections = 0;
    $created_lessons = 0;
    $created_quizzes = 0;

    // Collect all lessons that need content generation
    $lessons_to_generate = [];

    // Get current max order for sections
    $this->db->select_max('orders');
    $this->db->where('course_id', $course_id);
    $max_order_result = $this->db->get('course_section')->row();
    $section_order = ($max_order_result && $max_order_result->orders !== null) ? $max_order_result->orders : 0;

    // Get current course section IDs
    $course_details = $this->lms_model->get_course_by_id($course_id);
    $previous_sections = json_decode($course_details['section'], true) ?? [];
    
    // Process each section
    foreach ($sections as $section_data) {
      $section_order++;

      // Create section
      $base_title = $section_data['title'] ?? 'Section ' . $section_order;

      // Apply numbering based on mode
      $section_title = $this->apply_numbering_to_title($base_title, $section_order, $numbering_mode);
      
      $section_insert = [
        'title' => html_escape($section_title),
        'course_id' => $course_id,
        'orders' => $section_order
      ];
      
      $this->db->insert('course_section', $section_insert);
      $section_id = $this->db->insert_id();
      $created_sections++;
      
      // Add section ID to course sections array
      $previous_sections[] = $section_id;
      
      // Process children (lessons and quizzes)
      $children = isset($section_data['children']) ? $section_data['children'] : [];
      $lesson_order = 0;
      
      foreach ($children as $child) {
        $lesson_order++;
        $child_type = strtolower($child['type'] ?? 'lesson');
        $child_title = $child['title'] ?? ($child_type == 'quiz' ? 'Quiz ' . $lesson_order : 'Lesson ' . $lesson_order);
        
        if ($child_type == 'quiz' && ($outline_rules['quizFrequency'] ?? 'per_section') !== 'none') {
          // Create quiz
          $quiz_data = [
            'course_id' => $course_id,
            'title' => html_escape($child_title),
            'section_id' => $section_id,
            'lesson_type' => 'quiz',
            'duration' => 0,
            'date_added' => strtotime(date('D, d-M-Y')),
            'summary' => html_escape($child['summary'] ?? ''),
            'order' => $lesson_order
          ];
          
          $this->db->insert('lesson', $quiz_data);
          $quiz_id = $this->db->insert_id();
          $created_quizzes++;
          
          // Generate real questions
          $questions_count = intval($child['questions'] ?? $outline_rules['questionsCount'] ?? 10);
          $section_context = $section_data['title'] ?? '';
          $course_context = $course['title'] ?? '';

          if (($outline_rules['generateContent'] ?? true) && ($outline_rules['quizFrequency'] ?? 'per_section') !== 'none') {
            try {
              $generated_questions = $this->generate_quiz_questions($child_title, $section_context, $course_context, $outline_rules, $questions_count);

              if (!empty($generated_questions) && is_array($generated_questions)) {
                foreach ($generated_questions as $q_index => $question) {
                  // Convert letter answer to numeric index (A=1, B=2, C=3, D=4)
                  $correct_answer_letter = strtoupper($question['correct_answer'] ?? 'A');
                  $correct_answer_index = ord($correct_answer_letter) - ord('A') + 1; // A=1, B=2, C=3, D=4

                  $question_data = [
                    'quiz_id' => $quiz_id,
                    'title' => $question['question'] ?? 'Question ' . ($q_index + 1),
                    'number_of_options' => 4,
                    'type' => 'multiple_choice',
                    'options' => json_encode($question['options'] ?? ['A) Option A', 'B) Option B', 'C) Option C', 'D) Option D']),
                    'correct_answers' => json_encode([$correct_answer_index]) // Store as numeric index
                  ];
                  $this->db->insert('question', $question_data);
                }
              } else {
                throw new Exception("No valid questions generated");
              }
            } catch (Exception $e) {
            // Fallback to placeholder questions if generation fails
            for ($q = 1; $q <= $questions_count; $q++) {
              $question_data = [
                'quiz_id' => $quiz_id,
                'title' => 'Question ' . $q . ' (Content will be generated shortly)',
                'number_of_options' => 4,
                'type' => 'multiple_choice',
                'options' => json_encode(['A) Option A', 'B) Option B', 'C) Option C', 'D) Option D']),
                'correct_answers' => json_encode([1]) // Default to first option (A)
              ];
              $this->db->insert('question', $question_data);
            }
            }
          } else {
            // Create placeholder questions when content generation is disabled
            for ($q = 1; $q <= $questions_count; $q++) {
              $question_data = [
                'quiz_id' => $quiz_id,
                'title' => 'Question ' . $q . ' (Content will be generated when you enable full content generation)',
                'number_of_options' => 4,
                'type' => 'multiple_choice',
                'options' => json_encode(['A) Option A', 'B) Option B', 'C) Option C', 'D) Option D']),
                'correct_answers' => json_encode([1]) // Default to first option (A)
              ];
              $this->db->insert('question', $question_data);
            }
          }
        } else {
          // Create lesson structure first
          $lesson_content = $child['summary'] ?? $child['content'] ?? '';

          $lesson_data = [
            'course_id' => $course_id,
            'title' => html_escape($child_title),
            'section_id' => $section_id,
            'lesson_type' => 'text',
            'attachment_type' => 'description',
            'duration' => 0,
            'date_added' => strtotime(date('D, d-M-Y')),
            'summary' => $lesson_content, // Will be updated after batch generation
            'order' => $lesson_order
          ];

          $this->db->insert('lesson', $lesson_data);
          $lesson_id = $this->db->insert_id();
          $created_lessons++;

          // Collect lesson for batch content generation
          if (empty($lesson_content) && ($outline_rules['generateContent'] ?? true)) {
            $lessons_to_generate[] = [
              'id' => $lesson_id,
              'title' => $child_title,
              'section_context' => $section_data['title'] ?? '',
              'course_context' => $course['title'] ?? ''
            ];
          }
        }
      }
    }

    // Generate content for all lessons in batch
    if (!empty($lessons_to_generate)) {
      try {
        $batch_content = $this->generate_lessons_content_batch($lessons_to_generate, $outline_rules);

        // Update lessons with generated content
        foreach ($lessons_to_generate as $index => $lesson_info) {
          $lesson_number = $index + 1;
          $generated_content = $batch_content[$lesson_number] ?? '';

          if (!empty($generated_content)) {
            $this->db->where('id', $lesson_info['id']);
            $this->db->update('lesson', ['summary' => $generated_content]);
          }
        }
      } catch (Exception $e) {
        log_message('error', "Batch content generation failed: " . $e->getMessage());
        // Leave placeholder content as is
      }
    } else {
      log_message('info', "No lessons need content generation");
    }

    // Update course with new section IDs
    $this->db->where('id', $course_id);
    $this->db->update('course', ['section' => json_encode($previous_sections)]);
    
    // Return success response with CSRF token
    $csrf = [
      'csrfName' => $this->security->get_csrf_token_name(),
      'csrfHash' => $this->security->get_csrf_hash(),
    ];
    
    // Reset PHP execution time to default
    ini_restore('max_execution_time');

    echo json_encode([
      'success' => true,
      'message' => 'Course structure generated successfully',
      'created' => [
        'sections' => $created_sections,
        'lessons' => $created_lessons,
        'quizzes' => $created_quizzes
      ],
      'csrf' => $csrf
    ]);
  }

  /**
   * Generate quiz from PDF using AI
   * Creates a quiz with questions generated from uploaded PDF content
   */
  public function generate_quiz_from_pdf()
  {
    if (!$this->input->is_ajax_request()) {
      echo json_encode(['success' => false, 'message' => 'Access denied']);
      return;
    }

    // Anti-duplicate lock
    $generation_key = 'quiz_pdf_generation_' . $this->session->userdata('user_id');
    $generation_lock = $this->session->userdata($generation_key);

    if ($generation_lock && (time() - $generation_lock) < self::AI_GENERATION_LOCK_DURATION) {
      $csrf = [
        'csrfName' => $this->security->get_csrf_token_name(),
        'csrfHash' => $this->security->get_csrf_hash(),
      ];
      echo json_encode([
        'success' => false,
        'message' => get_phrase('generation_already_in_progress'),
        'csrf' => $csrf
      ]);
      return;
    }

    // Set lock
    $this->session->set_userdata($generation_key, time());

    // Increase timeout for AI generation
    set_time_limit(self::AI_GENERATION_TIMEOUT);

    $section_id = $this->input->post('section_id');
    $course_id = $this->input->post('course_id');
    $quiz_title = $this->input->post('quiz_title');
    $questions_count = (int) $this->input->post('questions_count') ?: 10;
    $difficulty = $this->input->post('difficulty') ?: 'medium';

    // Validate inputs
    if (empty($section_id) || empty($course_id) || empty($quiz_title)) {
      $this->session->unset_userdata($generation_key);
      echo json_encode([
        'success' => false,
        'message' => get_phrase('missing_required_fields'),
        'csrf' => [
          'csrfName' => $this->security->get_csrf_token_name(),
          'csrfHash' => $this->security->get_csrf_hash(),
        ]
      ]);
      return;
    }

    // Check PDF file
    if (empty($_FILES['pdf_file']['name'])) {
      $this->session->unset_userdata($generation_key);
      echo json_encode([
        'success' => false,
        'message' => get_phrase('please_select_a_pdf_file'),
        'csrf' => [
          'csrfName' => $this->security->get_csrf_token_name(),
          'csrfHash' => $this->security->get_csrf_hash(),
        ]
      ]);
      return;
    }

    // Verify MIME type
    if (!empty($_FILES['pdf_file']['tmp_name'])) {
      $finfo = finfo_open(FILEINFO_MIME_TYPE);
      $mime = finfo_file($finfo, $_FILES['pdf_file']['tmp_name']);
      finfo_close($finfo);
      if ($mime !== 'application/pdf') {
        $this->session->unset_userdata($generation_key);
        echo json_encode([
          'success' => false,
          'message' => get_phrase('please_select_valid_pdf'),
          'csrf' => [
            'csrfName' => $this->security->get_csrf_token_name(),
            'csrfHash' => $this->security->get_csrf_hash(),
          ]
        ]);
        return;
      }
    }

    // Upload PDF
    $upload_path = FCPATH . 'uploads/temp_pdf/';
    if (!is_dir($upload_path)) {
      mkdir($upload_path, 0755, true);
    }

    $this->load->library('upload');
    $config = [
      'upload_path' => $upload_path,
      'allowed_types' => 'pdf',
      'max_size' => self::PDF_MAX_SIZE_BYTES, // 10MB
      'encrypt_name' => true
    ];
    $this->upload->initialize($config);

    if (!$this->upload->do_upload('pdf_file')) {
      $this->session->unset_userdata($generation_key);
      echo json_encode([
        'success' => false,
        'message' => strip_tags($this->upload->display_errors()),
        'csrf' => [
          'csrfName' => $this->security->get_csrf_token_name(),
          'csrfHash' => $this->security->get_csrf_hash(),
        ]
      ]);
      return;
    }

    $upload_data = $this->upload->data();
    $pdf_path = $upload_data['full_path'];

    // Extract text from PDF
    try {
      $parser = new \Smalot\PdfParser\Parser();
      $pdf = $parser->parseFile($pdf_path);
      $text = $pdf->getText();
      unlink($pdf_path);

      if (empty(trim($text))) {
        $this->session->unset_userdata($generation_key);
        echo json_encode([
          'success' => false,
          'message' => get_phrase('no_text_extracted_from_pdf'),
          'csrf' => [
            'csrfName' => $this->security->get_csrf_token_name(),
            'csrfHash' => $this->security->get_csrf_hash(),
          ]
        ]);
        return;
      }
    } catch (Exception $e) {
      @unlink($pdf_path);
      $this->session->unset_userdata($generation_key);
      echo json_encode([
        'success' => false,
        'message' => get_phrase('error_reading_pdf'),
        'csrf' => [
          'csrfName' => $this->security->get_csrf_token_name(),
          'csrfHash' => $this->security->get_csrf_hash(),
        ]
      ]);
      return;
    }

    // Generate questions using AI
    $questions = $this->generate_quiz_questions_from_text($text, $questions_count, $difficulty);

    if (empty($questions)) {
      $this->session->unset_userdata($generation_key);
      echo json_encode([
        'success' => false,
        'message' => get_phrase('ai_failed_to_generate_questions'),
        'csrf' => [
          'csrfName' => $this->security->get_csrf_token_name(),
          'csrfHash' => $this->security->get_csrf_hash(),
        ]
      ]);
      return;
    }

    // Get next lesson order
    $this->db->select('MAX(`order`) as max_order');
    $this->db->where('section_id', $section_id);
    $max_order_result = $this->db->get('lesson')->row();
    $next_order = ($max_order_result && $max_order_result->max_order) ? $max_order_result->max_order + 1 : 1;

    // Create the quiz lesson
    $quiz_data = [
      'title' => $quiz_title,
      'course_id' => $course_id,
      'section_id' => $section_id,
      'lesson_type' => 'quiz',
      'summary' => '',
      'order' => $next_order,
      'duration' => 0,
      'date_added' => strtotime(date('D, d-M-Y'))
    ];

    $this->db->insert('lesson', $quiz_data);
    $quiz_id = $this->db->insert_id();

    if (!$quiz_id) {
      $this->session->unset_userdata($generation_key);
      echo json_encode([
        'success' => false,
        'message' => get_phrase('error_creating_quiz'),
        'csrf' => [
          'csrfName' => $this->security->get_csrf_token_name(),
          'csrfHash' => $this->security->get_csrf_hash(),
        ]
      ]);
      return;
    }

    // Add questions to quiz
    $added_questions = 0;
    foreach ($questions as $q) {
      $options = isset($q['options']) ? $q['options'] : [];
      $correct_index = isset($q['correct_answer']) ? (int)$q['correct_answer'] : 0;
      
      // Ensure correct_index is within bounds
      if ($correct_index < 0 || $correct_index >= count($options)) {
        $correct_index = 0;
      }

      $question_data = [
        'quiz_id' => $quiz_id,
        'title' => html_escape($q['title'] ?? $q['question'] ?? ''),
        'number_of_options' => count($options),
        'type' => 'multiple_choice',
        'options' => json_encode($options),
        'correct_answers' => json_encode([$correct_index])
      ];

      $this->db->insert('question', $question_data);
      if ($this->db->insert_id()) {
        $added_questions++;
      }
    }

    $this->session->unset_userdata($generation_key);

    echo json_encode([
      'success' => true,
      'message' => sprintf(get_phrase('quiz_created_with_n_questions'), $added_questions),
      'quiz_id' => $quiz_id,
      'questions_count' => $added_questions,
      'csrf' => [
        'csrfName' => $this->security->get_csrf_token_name(),
        'csrfHash' => $this->security->get_csrf_hash(),
      ]
    ]);
  }

  /**
   * Generate quiz questions from text using DeepSeek AI
   */
  private function generate_quiz_questions_from_text($text, $num = 10, $difficulty = 'medium')
  {
    $text = substr(trim($text), 0, 25000);
    
    $difficulty_instructions = [
      'easy' => "DIFFICULTY: EASY\n" .
                "- Simple and direct questions\n" .
                "- Obvious answers for anyone who read the document\n" .
                "- Avoid tricks and complex nuances\n",
      'medium' => "DIFFICULTY: MEDIUM\n" .
                 "- Standard comprehension questions\n" .
                 "- Requires good reading of the document\n" .
                 "- Include some reflection questions\n",
      'hard' => "DIFFICULTY: HARD\n" .
                "- In-depth and analytical questions\n" .
                "- Requires fine understanding of the content\n" .
                "- Include synthesis and analysis questions\n",
      'mixed' => "DIFFICULTY: MIXED\n" .
                "- Vary levels: 30% easy, 40% medium, 30% hard\n" .
                "- Start with simple questions and increase gradually\n"
    ];
    
    $difficulty_text = $difficulty_instructions[$difficulty] ?? $difficulty_instructions['medium'];

    $prompt = "You are an expert in creating educational MCQ questions.\n" .
      "Generate EXACTLY $num multiple choice questions (4 options, 1 correct answer).\n\n" .
      $difficulty_text . "\n" .
      "Respond ONLY with valid JSON, NOTHING else. No markdown, no text before/after.\n\n" .
      "Strict format:\n" .
      "[\n" .
      "  {\"title\": \"Question?\", \"options\": [\"A\", \"B\", \"C\", \"D\"], \"correct_answer\": 0}\n" .
      "]\n" .
      "correct_answer is the 0-based index of the correct option.\n" .
      "Start directly with [ and end with ].\n\n" .
      "Here is the document content:\n\n" . $text . "\n\n" .
      "Generate exactly $num MCQ questions (4 options, 1 correct answer).";

    // Use DeepSeek API with extended timeout for question generation
    $response = $this->call_deepseek_api($prompt, 120);

    if (isset($response['error'])) {
      log_message('error', 'Quiz generation DeepSeek API error: ' . $response['error']);
      return [];
    }

    $content = $response['content'] ?? '';

    if (empty($content)) {
      return [];
    }

    // Extract JSON from response
    if (preg_match('/\[[\s\S]*\]/', $content, $m)) {
      $json = json_decode($m[0], true);
      if (json_last_error() === JSON_ERROR_NONE && is_array($json)) {
        return $json;
      }
    }

    return [];
  }

  /**
   * Get lessons from sections that precede the target section
   * Only includes sections with at least one non-empty lesson
   */
  public function get_lessons_for_quiz($course_id = '', $target_section_id = '')
  {
    if (!$this->input->is_ajax_request()) {
      echo json_encode(['success' => false, 'message' => 'Access denied']);
      return;
    }

    if (empty($course_id) || empty($target_section_id)) {
      echo json_encode([
        'success' => false,
        'message' => get_phrase('missing_required_fields')
      ]);
      return;
    }

    // Check if sections_to_include is provided (for including current section)
    $sections_to_include = $this->input->post('sections_to_include');
    if (!empty($sections_to_include)) {
      // Decode JSON array of section IDs
      $section_ids = json_decode($sections_to_include, true);
      if (json_last_error() !== JSON_ERROR_NONE || empty($section_ids)) {
        echo json_encode([
          'success' => false,
          'message' => get_phrase('invalid_section_data')
        ]);
        return;
      }

      // Get sections by IDs
      $this->db->select('id, title, orders');
      $this->db->where('course_id', $course_id);
      $this->db->where_in('id', $section_ids);
      $this->db->order_by('orders', 'ASC');
      $sections = $this->db->get('course_section')->result_array();
    } else {
      // Legacy behavior: Get all sections before the target section (orders < target_order)
      $this->db->select('orders');
      $this->db->where('id', $target_section_id);
      $target_section = $this->db->get('course_section')->row();

      if (!$target_section) {
        echo json_encode([
          'success' => false,
          'message' => get_phrase('section_not_found')
        ]);
        return;
      }

      $target_order = $target_section->orders;

      // Get all sections before the target section (orders < target_order)
      $this->db->select('id, title, orders');
      $this->db->where('course_id', $course_id);
      $this->db->where('orders <', $target_order);
      $this->db->order_by('orders', 'ASC');
      $sections = $this->db->get('course_section')->result_array();
    }

    $sections_with_lessons = [];

    foreach ($sections as $section) {
      // Get lessons (not quizzes) from this section that have content
      $this->db->select('id, title, summary');
      $this->db->where('section_id', $section['id']);
      $this->db->where('lesson_type', 'text'); // Only text lessons, not quizzes
      $this->db->order_by('order', 'ASC');
      $lessons = $this->db->get('lesson')->result_array();

      $valid_lessons = [];
      foreach ($lessons as $lesson) {
        // Check if lesson has content (not empty)
        $content = trim(strip_tags($lesson['summary']));
        if (!empty($content) && strlen($content) > 50) {
          $valid_lessons[] = [
            'id' => $lesson['id'],
            'title' => html_entity_decode($lesson['title'], ENT_QUOTES, 'UTF-8')
          ];
        }
      }

      // Only include section if it has valid lessons
      if (!empty($valid_lessons)) {
        $sections_with_lessons[] = [
          'id' => $section['id'],
          'title' => html_entity_decode($section['title'], ENT_QUOTES, 'UTF-8'),
          'lessons' => $valid_lessons
        ];
      }
    }

    echo json_encode([
      'success' => true,
      'sections' => $sections_with_lessons,
      'csrf' => [
        'csrfName' => $this->security->get_csrf_token_name(),
        'csrfHash' => $this->security->get_csrf_hash()
      ]
    ]);
  }

  /**
   * Generate quiz from selected lessons using AI
   */
  public function generate_quiz_from_lessons()
  {
    if (!$this->input->is_ajax_request()) {
      echo json_encode(['success' => false, 'message' => 'Access denied']);
      return;
    }

    // Anti-duplicate lock
    $generation_key = 'quiz_lessons_generation_' . $this->session->userdata('user_id');
    $generation_lock = $this->session->userdata($generation_key);

    if ($generation_lock && (time() - $generation_lock) < self::AI_GENERATION_LOCK_DURATION) {
      echo json_encode([
        'success' => false,
        'message' => get_phrase('generation_already_in_progress'),
        'csrf' => [
          'csrfName' => $this->security->get_csrf_token_name(),
          'csrfHash' => $this->security->get_csrf_hash(),
        ]
      ]);
      return;
    }

    // Set lock
    $this->session->set_userdata($generation_key, time());

    // Increase timeout for AI generation
    set_time_limit(self::AI_GENERATION_TIMEOUT);

    $section_id = $this->input->post('section_id');
    $course_id = $this->input->post('course_id');
    $quiz_title = $this->input->post('quiz_title');
    $questions_count = (int) $this->input->post('questions_count') ?: 10;
    $difficulty = $this->input->post('difficulty') ?: 'medium';
    $lesson_ids_json = $this->input->post('lesson_ids');

    // Validate inputs
    if (empty($section_id) || empty($course_id) || empty($quiz_title) || empty($lesson_ids_json)) {
      $this->session->unset_userdata($generation_key);
      echo json_encode([
        'success' => false,
        'message' => get_phrase('missing_required_fields'),
        'csrf' => [
          'csrfName' => $this->security->get_csrf_token_name(),
          'csrfHash' => $this->security->get_csrf_hash(),
        ]
      ]);
      return;
    }

    $lesson_ids = json_decode($lesson_ids_json, true);

    if (empty($lesson_ids) || !is_array($lesson_ids)) {
      $this->session->unset_userdata($generation_key);
      echo json_encode([
        'success' => false,
        'message' => get_phrase('please_select_at_least_one_lesson'),
        'csrf' => [
          'csrfName' => $this->security->get_csrf_token_name(),
          'csrfHash' => $this->security->get_csrf_hash(),
        ]
      ]);
      return;
    }

    // Get lesson content
    $combined_text = '';
    foreach ($lesson_ids as $lesson_id) {
      $this->db->select('title, summary');
      $this->db->where('id', $lesson_id);
      $lesson = $this->db->get('lesson')->row();

      if ($lesson && !empty($lesson->summary)) {
        $lesson_title = html_entity_decode($lesson->title, ENT_QUOTES, 'UTF-8');
        $lesson_content = strip_tags($lesson->summary);
        $lesson_content = html_entity_decode($lesson_content, ENT_QUOTES, 'UTF-8');
        $combined_text .= "\n\n=== " . $lesson_title . " ===\n" . $lesson_content;
      }
    }

    $combined_text = trim($combined_text);

    if (empty($combined_text) || strlen($combined_text) < 100) {
      $this->session->unset_userdata($generation_key);
      echo json_encode([
        'success' => false,
        'message' => get_phrase('selected_lessons_have_no_content'),
        'csrf' => [
          'csrfName' => $this->security->get_csrf_token_name(),
          'csrfHash' => $this->security->get_csrf_hash(),
        ]
      ]);
      return;
    }

    // Generate questions using AI
    $questions = $this->generate_quiz_questions_from_text($combined_text, $questions_count, $difficulty);

    if (empty($questions)) {
      $this->session->unset_userdata($generation_key);
      echo json_encode([
        'success' => false,
        'message' => get_phrase('ai_failed_to_generate_questions'),
        'csrf' => [
          'csrfName' => $this->security->get_csrf_token_name(),
          'csrfHash' => $this->security->get_csrf_hash(),
        ]
      ]);
      return;
    }

    // Get next lesson order
    $this->db->select('MAX(`order`) as max_order');
    $this->db->where('section_id', $section_id);
    $max_order_result = $this->db->get('lesson')->row();
    $next_order = ($max_order_result && $max_order_result->max_order) ? $max_order_result->max_order + 1 : 1;

    // Create the quiz lesson
    $quiz_data = [
      'title' => $quiz_title,
      'course_id' => $course_id,
      'section_id' => $section_id,
      'lesson_type' => 'quiz',
      'summary' => '',
      'order' => $next_order,
      'duration' => 0,
      'date_added' => strtotime(date('D, d-M-Y'))
    ];

    $this->db->insert('lesson', $quiz_data);
    $quiz_id = $this->db->insert_id();

    if (!$quiz_id) {
      $this->session->unset_userdata($generation_key);
      echo json_encode([
        'success' => false,
        'message' => get_phrase('error_creating_quiz'),
        'csrf' => [
          'csrfName' => $this->security->get_csrf_token_name(),
          'csrfHash' => $this->security->get_csrf_hash(),
        ]
      ]);
      return;
    }

    // Add questions to quiz
    $added_questions = 0;
    foreach ($questions as $q) {
      $options = isset($q['options']) ? $q['options'] : [];
      $correct_index = isset($q['correct_answer']) ? (int)$q['correct_answer'] : 0;
      
      // Ensure correct_index is within bounds
      if ($correct_index < 0 || $correct_index >= count($options)) {
        $correct_index = 0;
      }

      $question_data = [
        'quiz_id' => $quiz_id,
        'title' => html_escape($q['title'] ?? $q['question'] ?? ''),
        'number_of_options' => count($options),
        'type' => 'multiple_choice',
        'options' => json_encode($options),
        'correct_answers' => json_encode([$correct_index])
      ];

      $this->db->insert('question', $question_data);
      if ($this->db->insert_id()) {
        $added_questions++;
      }
    }

    $this->session->unset_userdata($generation_key);

    echo json_encode([
      'success' => true,
      'message' => sprintf(get_phrase('quiz_created_with_n_questions'), $added_questions),
      'quiz_id' => $quiz_id,
      'questions_count' => $added_questions,
      'csrf' => [
        'csrfName' => $this->security->get_csrf_token_name(),
        'csrfHash' => $this->security->get_csrf_hash(),
      ]
    ]);
  }

  /**
   * Generate lesson content from AI
   */
  public function generate_lesson_from_ai()
  {
    // Validate AJAX request and anti-duplicate lock
    $validation = $this->_validate_ai_lesson_request();
    if ($validation !== true) {
      echo json_encode($validation);
      return;
    }

    // Get and validate input data
    $input_data = $this->_get_ai_lesson_input_data();
    $validation_result = $this->_validate_ai_lesson_input_data($input_data);

    if ($validation_result !== true) {
      $this->_release_ai_lesson_lock();
      echo json_encode($validation_result);
      return;
    }

    // Process the lesson generation
    $this->_process_ai_lesson_generation($input_data);
  }

  public function update_lesson_from_ai()
  {
    // Validate AJAX request and anti-duplicate lock
    $validation = $this->_validate_ai_lesson_request();
    if ($validation !== true) {
      echo json_encode($validation);
      return;
    }

    // Get and validate input data
    $input_data = $this->_get_ai_lesson_input_data();
    
    // Validate lesson_id
    if (empty($input_data['lesson_id'])) {
        $this->_release_ai_lesson_lock();
        echo json_encode([
            'success' => false, 
            'message' => get_phrase('lesson_id_required'),
            'csrf' => [
                'csrfName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
            ]
        ]);
        return;
    }

    $validation_result = $this->_validate_ai_lesson_input_data($input_data);

    if ($validation_result !== true) {
      $this->_release_ai_lesson_lock();
      echo json_encode($validation_result);
      return;
    }

    // Process the lesson update
    $this->_process_ai_lesson_update($input_data);
  }

  /**
   * Get generation lock key for current user
   */
  private function _get_ai_lesson_generation_key()
  {
    return 'lesson_ai_generation_' . $this->session->userdata('user_id');
  }

  /**
   * Set generation lock
   */
  private function _set_ai_lesson_lock()
  {
    $generation_key = $this->_get_ai_lesson_generation_key();
    $this->session->set_userdata($generation_key, time());
  }

  /**
   * Release generation lock
   */
  private function _release_ai_lesson_lock()
  {
    $generation_key = $this->_get_ai_lesson_generation_key();
    $this->session->unset_userdata($generation_key);
  }

  /**
   * Validate AJAX request and check for duplicate generation locks
   */
  private function _validate_ai_lesson_request()
  {
    if (!$this->input->is_ajax_request()) {
      return ['success' => false, 'message' => 'Access denied'];
    }

    // Anti-duplicate lock
    $generation_key = $this->_get_ai_lesson_generation_key();
    $generation_lock = $this->session->userdata($generation_key);

    if ($generation_lock && (time() - $generation_lock) < self::AI_GENERATION_LOCK_DURATION) {
      return [
        'success' => false,
        'message' => get_phrase('generation_already_in_progress'),
        'csrf' => [
          'csrfName' => $this->security->get_csrf_token_name(),
          'csrfHash' => $this->security->get_csrf_hash(),
        ]
      ];
    }

    // Set lock
    $this->_set_ai_lesson_lock();

    // Increase timeout for AI generation
    set_time_limit(self::AI_GENERATION_TIMEOUT);

    return true;
  }

  /**
   * Extract input data from POST request
   */
  private function _get_ai_lesson_input_data()
  {
    return [
      'lesson_id' => $this->input->post('lesson_id'),
      'section_id' => $this->input->post('section_id'),
      'course_id' => $this->input->post('course_id'),
      'source' => $this->input->post('source'),
      'audience' => $this->input->post('audience'),
      'duration' => $this->input->post('duration'),
      'language' => $this->input->post('language'),
      'tone' => $this->input->post('tone'),
      'instructions' => $this->input->post('instructions'),
      'pdf_file' => isset($_FILES['pdf_file']) ? $_FILES['pdf_file'] : null
    ];
  }

  /**
   * Validate input data
   */
  private function _validate_ai_lesson_input_data($data)
  {
    if (empty($data['section_id']) || empty($data['course_id']) || empty($data['source'])) {
      return [
        'success' => false,
        'message' => get_phrase('missing_required_fields'),
        'csrf' => [
          'csrfName' => $this->security->get_csrf_token_name(),
          'csrfHash' => $this->security->get_csrf_hash(),
        ]
      ];
    }

    // Additional validation for PDF source
    if ($data['source'] === 'pdf' && (!$data['pdf_file'] || $data['pdf_file']['error'] !== UPLOAD_ERR_OK)) {
      return [
        'success' => false,
        'message' => get_phrase('pdf_file_required'),
        'csrf' => [
          'csrfName' => $this->security->get_csrf_token_name(),
          'csrfHash' => $this->security->get_csrf_hash(),
        ]
      ];
    }

    return true;
  }

  /**
   * Process the AI lesson generation
   */
  private function _process_ai_lesson_generation($input_data)
  {
    try {
      // Get course and section info
      $course = $this->lms_model->get_course_by_id($input_data['course_id']);
      $section = $this->lms_model->get_section('section', $input_data['section_id'])->row_array();

      if (!$course || !$section) {
        throw new Exception(get_phrase('course_or_section_not_found'));
      }

      // Prepare context based on source
      $context = '';
      if ($input_data['source'] === 'pdf') {
        // Handle PDF upload
        if (!empty($_FILES['pdf_file']['name'])) {
          $upload_path = FCPATH . 'uploads/ai_pdfs/';
          if (!is_dir($upload_path)) {
            mkdir($upload_path, 0755, true);
          }

          $this->load->library('upload');
          $config = array(
            'upload_path'   => $upload_path,
            'allowed_types' => 'pdf',
            'max_size'      => self::PDF_MAX_SIZE_BYTES,
            'encrypt_name'  => true
          );

          $this->upload->initialize($config);

          if (!$this->upload->do_upload('pdf_file')) {
            throw new Exception($this->upload->display_errors());
          }

          $upload_data = $this->upload->data();
          $pdf_path = $upload_data['full_path'];

          // Extract structure from PDF using Python script
          $context = $this->extract_text_from_pdf($pdf_path);
          
          // Clean up uploaded file
          unlink($pdf_path);

          if (empty($context)) {
            throw new Exception(get_phrase('failed_to_extract_text_from_pdf'));
          }
        } else {
          throw new Exception(get_phrase('pdf_file_required'));
        }
      } else {
        // Use course outline
        $sections = $this->lms_model->get_section('course', $input_data['course_id'])->result_array();
        $course_outline = "Course: " . $course['title'] . "\n\n";

        foreach ($sections as $sec) {
          $course_outline .= "Section: " . $sec['title'] . "\n";
          $lessons = $this->lms_model->get_lessons('section', $sec['id'])->result_array();
          foreach ($lessons as $lesson) {
            $course_outline .= "  - Lesson: " . $lesson['title'] . "\n";
          }
          $course_outline .= "\n";
        }

        $context = "Course Outline:\n" . $course_outline . "\n";
      }

      // Get current section lessons for context
      $current_lessons = $this->lms_model->get_lessons('section', $input_data['section_id'])->result_array();
      $lesson_count = count($current_lessons) + 1;

      // Generate lesson title
      $lesson_title = "Lesson " . $lesson_count . ": " . $section['title'];

      // Prepare AI prompt
      $prompt = $this->build_lesson_prompt(
        $lesson_title,
        $context,
        $input_data['audience'],
        $input_data['duration'],
        $input_data['language'],
        $input_data['tone'],
        $input_data['instructions'],
        $course['title'],
        $section['title']
      );

      // Call DeepSeek API with longer timeout for PDF processing
      $timeout = ($input_data['source'] === 'pdf') ? 180 : 120; // 3 minutes for PDF, 2 for outline
      $ai_response = $this->call_deepseek_api($prompt, $timeout);

      if (!$ai_response || empty($ai_response['content'])) {
        throw new Exception(get_phrase('ai_failed_to_generate_content'));
      }

      // Parse JSON response from DeepSeek
      $json_content = json_decode($ai_response['content'], true);
      if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Failed to parse AI response JSON: ' . json_last_error_msg());
      }

      // Extract HTML content from JSON response
      $raw_content = $json_content['lesson_content'] ?? $json_content['content'] ?? $ai_response['content'];

      // Parse AI response
      $lesson_content = $this->parse_lesson_content($raw_content, $input_data['language']);

      // Create new lesson
      $lesson_data = [
        'course_id' => $input_data['course_id'],
        'section_id' => $input_data['section_id'],
        'title' => $lesson_title,
        'summary' => $lesson_content,
        'lesson_type' => 'text',
        'attachment_type' => 'text',
        'duration' => '00:00:00',
        'date_added' => time(),
        'last_modified' => time()
      ];

      $this->db->insert('lesson', $lesson_data);
      $lesson_id = $this->db->insert_id();

      if (!$lesson_id) {
        throw new Exception(get_phrase('failed_to_create_lesson'));
      }

      // Remove lock
      $this->_release_ai_lesson_lock();

      echo json_encode([
        'success' => true,
        'message' => get_phrase('lesson_generated_successfully'),
        'lesson_id' => $lesson_id,
        'lesson_title' => $lesson_title,
        'csrf' => [
          'csrfName' => $this->security->get_csrf_token_name(),
          'csrfHash' => $this->security->get_csrf_hash(),
        ]
      ]);

    } catch (Exception $e) {
      // Remove lock on error
      $this->_release_ai_lesson_lock();

      echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'csrf' => [
          'csrfName' => $this->security->get_csrf_token_name(),
          'csrfHash' => $this->security->get_csrf_hash(),
        ]
      ]);
    }
  }

  private function _process_ai_lesson_update($input_data)
  {
    try {
      // Get course and section info
      $course = $this->lms_model->get_course_by_id($input_data['course_id']);
      $section = $this->lms_model->get_section('section', $input_data['section_id'])->row_array();
      $existing_lesson = $this->lms_model->get_lessons('lesson', $input_data['lesson_id'])->row_array();

      if (!$course || !$section || !$existing_lesson) {
        throw new Exception(get_phrase('course_section_or_lesson_not_found'));
      }

      // Prepare context based on source
      $context = '';
      if ($input_data['source'] === 'pdf') {
        // Handle PDF upload
        if (!empty($_FILES['pdf_file']['name'])) {
          $upload_path = FCPATH . 'uploads/ai_pdfs/';
          if (!is_dir($upload_path)) {
            mkdir($upload_path, 0755, true);
          }

          $this->load->library('upload');
          $config = array(
            'upload_path'   => $upload_path,
            'allowed_types' => 'pdf',
            'max_size'      => self::PDF_MAX_SIZE_BYTES,
            'encrypt_name'  => true
          );

          $this->upload->initialize($config);

          if (!$this->upload->do_upload('pdf_file')) {
            throw new Exception($this->upload->display_errors());
          }

          $upload_data = $this->upload->data();
          $pdf_path = $upload_data['full_path'];

          // Extract structure from PDF using Python script
          $context = $this->extract_text_from_pdf($pdf_path);
          
          // Clean up uploaded file
          unlink($pdf_path);

          if (empty($context)) {
            throw new Exception(get_phrase('failed_to_extract_text_from_pdf'));
          }
        } else {
          throw new Exception(get_phrase('pdf_file_required'));
        }
      } else {
        // Use course outline
        $sections = $this->lms_model->get_section('course', $input_data['course_id'])->result_array();
        $course_outline = "Course: " . $course['title'] . "\n\n";

        foreach ($sections as $sec) {
          $course_outline .= "Section: " . $sec['title'] . "\n";
          $lessons = $this->lms_model->get_lessons('section', $sec['id'])->result_array();
          foreach ($lessons as $lesson) {
            $course_outline .= "  - Lesson: " . $lesson['title'] . "\n";
          }
          $course_outline .= "\n";
        }

        $context = "Course Outline:\n" . $course_outline . "\n";
      }

      // Use existing lesson title
      $lesson_title = $existing_lesson['title'];

      // Prepare AI prompt
      $prompt = $this->build_lesson_prompt(
        $lesson_title,
        $context,
        $input_data['audience'],
        $input_data['duration'],
        $input_data['language'],
        $input_data['tone'],
        $input_data['instructions'],
        $course['title'],
        $section['title']
      );

      // Call DeepSeek API with longer timeout for PDF processing
      $timeout = ($input_data['source'] === 'pdf') ? 180 : 120; // 3 minutes for PDF, 2 for outline
      $ai_response = $this->call_deepseek_api($prompt, $timeout);

      // Debug logging
      error_log('AI Response from API (UPDATE): ' . print_r($ai_response, true));

      if (!$ai_response || empty($ai_response['content'])) {
        $error_msg = isset($ai_response['error']) ? $ai_response['error'] : get_phrase('ai_failed_to_generate_content');
        error_log('AI Response Error (UPDATE): ' . $error_msg);
        throw new Exception($error_msg);
      }

      // Parse JSON response from DeepSeek
      $json_content = json_decode($ai_response['content'], true);
      error_log('Parsed JSON content (UPDATE): ' . print_r($json_content, true));

      if (json_last_error() !== JSON_ERROR_NONE) {
        error_log('JSON decode error (UPDATE): ' . json_last_error_msg());
        error_log('Raw content (UPDATE): ' . substr($ai_response['content'], 0, 1000));
        throw new Exception('Failed to parse AI response JSON: ' . json_last_error_msg());
      }

      // Extract HTML content from JSON response
      $raw_content = $json_content['lesson_content'] ?? $json_content['content'] ?? $ai_response['content'];
      error_log('Raw content extracted (UPDATE), length: ' . strlen($raw_content));

      // Parse AI response
      $lesson_content = $this->parse_lesson_content($raw_content, $input_data['language']);

      // Update lesson
      $lesson_data = [
        'summary' => $lesson_content,
        'lesson_type' => 'text',
        'attachment_type' => 'text',
        'last_modified' => time()
      ];

      $this->db->where('id', $input_data['lesson_id']);
      $this->db->update('lesson', $lesson_data);

      // Remove lock
      $this->_release_ai_lesson_lock();

      echo json_encode([
        'success' => true,
        'message' => get_phrase('lesson_updated_successfully'),
        'lesson_id' => $input_data['lesson_id'],
        'lesson_title' => $lesson_title,
        'csrf' => [
          'csrfName' => $this->security->get_csrf_token_name(),
          'csrfHash' => $this->security->get_csrf_hash(),
        ]
      ]);

    } catch (Exception $e) {
      // Remove lock on error
      $this->_release_ai_lesson_lock();

      echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'csrf' => [
          'csrfName' => $this->security->get_csrf_token_name(),
          'csrfHash' => $this->security->get_csrf_hash(),
        ]
      ]);
    }
  }

  /**
   * Extract text and structure from PDF file using Python script
   */
  private function extract_text_from_pdf($pdf_path)
  {
    try {
      // Use the Python script through extract_raw_pdf_text
      $json_output = $this->extract_raw_pdf_text($pdf_path);

      if (!$json_output) {
        throw new Exception('Failed to extract PDF structure');
      }

      // Parse JSON output
      $json_data = json_decode($json_output, true);

      if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid JSON output from Python script: ' . json_last_error_msg());
      }

      if (isset($json_data['error'])) {
        throw new Exception($json_data['error']);
      }

      // Format the extracted data for AI context
      $context = $this->format_pdf_context($json_data);

      return $context;

    } catch (Exception $e) {
      // Fallback to simple text extraction if Python script fails
      error_log('PDF extraction failed: ' . $e->getMessage());

      // Try to extract raw text as fallback using pdftotext if available
      if (function_exists('shell_exec')) {
        $pdftotext_check = shell_exec('pdftotext -v 2>&1');
        if (strpos($pdftotext_check, 'pdftotext') !== false) {
          $temp_file = tempnam(sys_get_temp_dir(), 'pdf_text_');
          $command = escapeshellcmd('pdftotext "' . $pdf_path . '" "' . $temp_file . '"');
          shell_exec($command);

          if (file_exists($temp_file)) {
            $text = file_get_contents($temp_file);
            unlink($temp_file);
            return "PDF Content (raw text extraction):\n" . substr($text, 0, 5000) . "\n\n";
          }
        }
      }

      throw new Exception('PDF extraction failed: ' . $e->getMessage());
    }
  }
  
  /**
   * Format PDF structure data for AI context
   */
  private function format_pdf_context($pdf_data)
  {
    $context = "PDF STRUCTURE ANALYSIS:\n\n";

    // Add metadata
    if (!empty($pdf_data['title'])) {
      $context .= "Document Title: " . $pdf_data['title'] . "\n";
    }
    if (!empty($pdf_data['total_pages'])) {
      $context .= "Total Pages: " . $pdf_data['total_pages'] . "\n";
    }
    if (!empty($pdf_data['metadata']['author'])) {
      $context .= "Author: " . $pdf_data['metadata']['author'] . "\n";
    }

    $context .= "\n";

    // Add table of contents if available (limit to first 30 items to keep context manageable)
    if (!empty($pdf_data['toc'])) {
      $context .= "TABLE OF CONTENTS (showing first 30 items):\n";
      $toc_items = array_slice($pdf_data['toc'], 0, 30);
      foreach ($toc_items as $item) {
        $indent = str_repeat('  ', max(0, $item['level'] - 1));
        $context .= $indent . "• " . $item['title'] . " (page " . $item['page'] . ")\n";
      }
      if (count($pdf_data['toc']) > 30) {
        $context .= "  ... and " . (count($pdf_data['toc']) - 30) . " more items\n";
      }
      $context .= "\n";
    }
    
    // Add headings structure (limited to keep context manageable)
    if (!empty($pdf_data['headings'])) {
      $context .= "DOCUMENT STRUCTURE (Headings, limited):\n";

      // Group by level and limit to reduce context size
      $h1_headings = [];
      $h2_headings = [];

      foreach (array_slice($pdf_data['headings'], 0, 100) as $heading) { // Limit to first 100 headings
        if ($heading['level'] == 1) {
          $h1_headings[] = $heading;
        } elseif ($heading['level'] == 2) {
          $h2_headings[] = $heading;
        }
      }

      // Add H1 headings (limit to 20)
      if (!empty($h1_headings)) {
        $context .= "\nMAIN SECTIONS (H1, showing first 20):\n";
        foreach (array_slice($h1_headings, 0, 20) as $h1) {
          $context .= "• " . $h1['title'] . " (pages " . $h1['page'];
          if (isset($h1['end_page'])) {
            $context .= "-" . $h1['end_page'];
          }
          $context .= ")\n";

          // Add H2 subheadings for this H1 (limit to 5 per H1)
          $h2_for_h1 = array_filter($h2_headings, function($h2) use ($h1) {
            return $h2['page'] >= $h1['page'] &&
                   (!isset($h1['end_page']) || $h2['page'] <= $h1['end_page']);
          });

          $h2_count = 0;
          foreach (array_slice($h2_for_h1, 0, 5) as $h2) {
            $context .= "  ◦ " . $h2['title'] . " (page " . $h2['page'] . ")\n";
            $h2_count++;
          }
          if (count($h2_for_h1) > 5) {
            $context .= "  ... and " . (count($h2_for_h1) - 5) . " more subsections\n";
          }
        }
        if (count($h1_headings) > 20) {
          $context .= "  ... and " . (count($h1_headings) - 20) . " more sections\n";
        }
      } elseif (!empty($h2_headings)) {
        // If no H1, show H2 as main sections (limit to 30)
        $context .= "\nMAIN SECTIONS (showing first 30):\n";
        foreach (array_slice($h2_headings, 0, 30) as $h2) {
          $context .= "• " . $h2['title'] . " (page " . $h2['page'] . ")\n";
        }
        if (count($h2_headings) > 30) {
          $context .= "  ... and " . (count($h2_headings) - 30) . " more sections\n";
        }
      }

      $context .= "\n";
    }
    
    // Add sample content from first few pages
    $context .= "DOCUMENT OVERVIEW:\n";
    $context .= "This PDF document contains structured educational content. ";
    $context .= "The analysis shows a clear hierarchical organization with ";
    
    if (!empty($pdf_data['toc'])) {
      $context .= count($pdf_data['toc']) . " items in the table of contents, ";
    }
    
    if (!empty($h1_headings)) {
      $context .= count($h1_headings) . " main sections, ";
      if (!empty($h2_headings)) {
        $context .= "and " . count($h2_headings) . " subsections. ";
      }
    } elseif (!empty($pdf_data['headings'])) {
      $context .= count($pdf_data['headings']) . " identified headings. ";
    } else {
      $context .= "structured content suitable for lesson creation. ";
    }
    
    $context .= "The content appears to be well-organized for educational purposes.\n\n";
    
    return $context;
  }
  
  /**
   * Fallback: Extract raw text from PDF using PHP
   */
  private function extract_raw_pdf_text($pdf_path)
  {
    // Use Python script for PDF structure extraction
    if (function_exists('shell_exec')) {
      $script_path = FCPATH . 'application/scripts/extract_pdf_structure.py';

      // Check if Python script exists
      if (!file_exists($script_path)) {
        error_log('Python script not found: ' . $script_path);
        return false;
      }

      // Check if Python is available
      $python_check = shell_exec('python --version 2>&1');
      if (strpos($python_check, 'Python') === false) {
        // Try python3
        $python_check = shell_exec('python3 --version 2>&1');
        if (strpos($python_check, 'Python') === false) {
          error_log('Python is not installed or not in PATH');
          return false;
        }
        $python_cmd = 'python3';
      } else {
        $python_cmd = 'python';
      }

      // Check if PyMuPDF (fitz) is installed
      $check_fitz = shell_exec($python_cmd . ' -c "import fitz; print(\'OK\')" 2>&1');
      if (strpos($check_fitz, 'OK') === false) {
        error_log('PyMuPDF (fitz) library is not installed. Please install it with: pip install PyMuPDF');
        return false;
      }

      // Execute the Python script
      $command = escapeshellcmd($python_cmd . ' "' . $script_path . '" "' . $pdf_path . '"');
      $output = shell_exec($command . ' 2>&1');

      if ($output === null) {
        error_log('Failed to execute Python script');
        return false;
      }

      // Parse JSON output
      $json_data = json_decode($output, true);

      if (json_last_error() !== JSON_ERROR_NONE) {
        // If not JSON, there might be an error message
        if (strpos($output, 'error') !== false) {
          $error_match = [];
          if (preg_match('/"error"\s*:\s*"([^"]+)"/', $output, $error_match)) {
            error_log('Python script error: ' . $error_match[1]);
            return false;
          }
        }
        error_log('Invalid JSON output from Python script: ' . substr($output, 0, 200));
        return false;
      }

      if (isset($json_data['error'])) {
        error_log('Python script error: ' . $json_data['error']);
        return false;
      }

      // Return the structured data as JSON string for processing
      return json_encode($json_data);
    }

    error_log('shell_exec not available');
    return false;
  }

  /**
   * Build prompt for lesson generation
   */
  private function build_lesson_prompt($lesson_title, $context, $audience, $duration, $language, $tone, $instructions, $course_title, $section_title)
  {
    // Map language codes
    $language_map = [
      'english' => 'English',
      'french' => 'French',
      'spanish' => 'Spanish',
      'arabic' => 'Arabic'
    ];

    // Map tone
    $tone_map = [
      'formal' => 'Formal and professional',
      'casual' => 'Casual and relaxed',
      'friendly' => 'Friendly and approachable',
      'academic' => 'Academic and scholarly',
      'conversational' => 'Conversational and engaging'
    ];

    // Map audience
    $audience_map = [
      'beginner' => 'Beginner level learners with no prior knowledge',
      'intermediate' => 'Intermediate level learners with basic understanding',
      'advanced' => 'Advanced level learners with good understanding',
      'expert' => 'Expert level learners with deep knowledge'
    ];

    $language_text = $language_map[$language] ?? 'English';
    $tone_text = $tone_map[$tone] ?? 'Formal and professional';
    $audience_text = $audience_map[$audience] ?? 'Intermediate level learners';

    // Limit context size to avoid overwhelming the API (max 15000 chars for context)
    if (strlen($context) > 15000) {
      $context = substr($context, 0, 15000) . "\n\n... [Context truncated to fit API limits]";
    }
    error_log('Context size: ' . strlen($context) . ' chars');

    // Check if context is from PDF structure analysis
    $is_pdf_structure = strpos($context, 'PDF STRUCTURE ANALYSIS:') !== false;
    
    if ($is_pdf_structure) {
      $source_info = "The lesson should be based on the PDF document structure analysis provided below. ";
      $source_info .= "Use the document's table of contents and heading structure as a guide for creating comprehensive lesson content. ";
      $source_info .= "Focus on explaining the key concepts presented in the document's sections.";
    } else {
      $source_info = "The lesson should be based on the course outline structure provided below. ";
      $source_info .= "Use the course sections and lesson titles as context for creating relevant educational content.";
    }

    $prompt = <<<PROMPT
Create lesson content in {$language_text} with {$tone_text} tone for {$audience_text}.

Course: {$course_title} | Section: {$section_title} | Lesson: {$lesson_title} ({$duration} min)

Source: {$source_info}

Context: {$context}

Requirements:
- HTML format (h2, h3, p, ul, ol, strong tags)
- 3-5 key concepts with examples
- Engaging introduction and clear summary
- {$instructions}

Response MUST be valid JSON only:
{"lesson_content": "<div>HTML lesson content here</div>"}
PROMPT;

    return $prompt;
  }

  /**
   * Parse lesson content from AI response
   */
  private function parse_lesson_content($ai_content, $language)
  {
    // Clean up the AI response
    $content = trim($ai_content);
    
    // Remove any markdown code blocks
    $content = preg_replace('/```(?:html)?\s*(.*?)\s*```/s', '$1', $content);
    
    // Ensure proper HTML structure
    if (!str_contains($content, '<html') && !str_contains($content, '<body')) {
      // Wrap in div if not already HTML
      $content = '<div class="ai-generated-content">' . $content . '</div>';
    }
    
    // Sanitize HTML
    $content = $this->sanitizeHtml($content);
    
    return $content;
  }
}
