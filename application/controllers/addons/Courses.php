<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
*  @author   : Creativeitem
*  date      : November, 2019
*  Ekattor School Management System With Addons
*  http://codecanyon.net/user/Creativeitem
*  http://support.creativeitem.com
*/

class Courses extends CI_Controller {
  public function __construct(){

    parent::__construct();

    $this->load->database();
    $this->load->library('session');
    $this->lmStudioUrl = 'http://154.146.250.62:7000/v1/chat/completions';

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
    $page_data['course_sections'] = $this->lms_model->get_section('course', $course_id)->result_array();
    // $page_data['subjects']        = $this->db->get_where('subjects', array('class_id' => $page_data['course']['class_id']))->result_array();
    $page_data['first_lesson_id']  = $this->db->get_where('lesson', array('course_id' => $course_id))->row_array();
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

  public function ajax_sort_section() {
    $section_json = $this->input->post('itemJSON');
    $this->lms_model->sort_section($section_json);
      // Préparer le nouveau jeton CSRF
      $csrf = array(
                'csrfName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
              );
          
      // Renvoyer la réponse JSON avec le HTML mis à jour et le nouveau jeton CSRF
      echo json_encode(array('csrf' => $csrf));
  }



  public function ajax_get_section($course_id){
    $page_data['course_sections'] = $this->lms_model->get_section('course', $course_id)->result_array();
    return $this->load->view('backend/academy/ajax_get_section', $page_data);
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

  // Return JSON response
  $this->output
      ->set_content_type('application/json')
      ->set_output(json_encode($csrf));
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
      'max_size'      => 3000,
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

    $extra_message = $should_overwrite ? ' Les questions existantes ont été remplacées.' : '';

    $this->respond_json(array(
      'status'  => true,
      'message' => "$added questions générées et ajoutées avec succès !" . $extra_message,
      'questions_count' => $added
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
        'timeout'         => 400,
        'connect_timeout' => 15,
      ]);

      $response = $client->post($this->lmStudioUrl, [
        'headers' => ['Content-Type' => 'application/json'],
        'json'    => [
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
          'temperature'        => 0.6,
          'max_tokens'         => 4000,
          'top_p'              => 0.95,
          'repetition_penalty' => 1.1,
          'stop'               => null
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
      log_message('error', 'Qwen Guzzle Error: ' . $e->getMessage());
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