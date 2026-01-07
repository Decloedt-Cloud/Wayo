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
  
  private $purifier;
  
  public function __construct(){

    parent::__construct();

    $this->load->database();
    $this->load->library('session');
    
    // DeepSeek API Configuration
    $this->deepseekUrl = 'https://api.deepseek.com/v1/chat/completions';
    $this->deepseekApiKey = 'sk-249b9057de6f47029c596004558ab8ce'; // DeepSeek API Key
    
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
      echo json_encode(array(
        'content' => $lesson['summary'] ?? '',
        'title' => $lesson['title'] ?? '',
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
      'max_size' => 10240,
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

    if ($generation_lock && (time() - $generation_lock) < 300) {
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
}