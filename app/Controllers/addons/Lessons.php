<?php

namespace App\Controllers\addons;

use App\Controllers\BaseController;

/*
*  @author   : Creativeitem
*  date      : November, 2019
*  Ekattor School Management System With Addons
*  http://codecanyon.net/user/Creativeitem
*  http://support.creativeitem.com
*/

class Lessons extends BaseController {
    public function __construct(){
        $this->session = \Config\Services::session();

        /*LOADING ALL THE MODELS HERE*/
        $this->loadModel('Crud_model',     'crud_model');
        $this->loadModel('User_model',     'user_model');
        $this->loadModel('Settings_model', 'settings_model');
        $this->loadModel('Payment_model',  'payment_model');
        $this->loadModel('Email_model',    'email_model');
        $this->loadModel('Addon_model',    'addon_model');
        $this->loadModel('Frontend_model', 'frontend_model');
        $this->loadModel('addons/Lms_model','lms_model');
        $this->loadModel('addons/Video_model','video_model');

        $user_login_type = session()->get('user_login_type');
        if($user_login_type != 1)
        return redirect()->to(site_url('login'));

        $student_login = session()->get('student_login');
        if ($student_login == 1) {
             $user_id = session()->get('user_id');
            // Check if school_id helper is available, if not load it
            if (!function_exists('school_id')) {
                 helper('common_helper');
            }
            // However, usually CI loads helpers in autoload.
            
            $current_school_id = session()->get('active_school_id'); // Safer to use session directly if helper not sure
            if (!$current_school_id) {
                 $current_school_id = session()->get('school_id');
            }

            // Using school_id() is better if available.
            // Let's assume it is available as Courses.php used it without loading it explicitly (maybe in autoload).
             $current_school_id = school_id();

            $student_check = $this->lms_model->get_row('students', ['user_id' => $user_id, 'school_id' => $current_school_id]);
            
            if (!$student_check || $student_check['status'] != 1) {
                return redirect()->to(site_url('app/invoice'));
            }
        }
    }

    public function index(){

    }

    public function play($slug = "", $course_id = "", $lesson_id = "") {
        $course_details = $this->lms_model->get_course_by_id($course_id);
        $sections = $this->lms_model->get_section('course', $course_id);
        $as_result_array = static function ($result): array {
            if (is_array($result)) {
                return $result;
            }
            if (is_object($result)) {
                if (method_exists($result, 'result_array')) {
                    return $result;
                }
                if (method_exists($result, 'getResultArray')) {
                    return $result->getResultArray();
                }
                if (method_exists($result, 'getResult')) {
                    $rows = $result->getResult();
                    return is_array($rows) ? $rows : [];
                }
            }
            return [];
        };
        $sections_rows = $as_result_array($sections);
        if (count($sections_rows) > 0) {
            $page_data['sections'] = $sections_rows;
            if ($lesson_id == "") {
                // Chercher la premiÃ¨re leÃ§on qui n'est pas un quiz
                $default_lesson = null;
                $default_section_id = null;
                
                foreach ($page_data['sections'] as $section) {
                    $lessons = $as_result_array($this->lms_model->get_lessons('section', $section['id']));
                    foreach ($lessons as $lesson) {
                        // SÃ©lectionner la premiÃ¨re leÃ§on qui n'est pas un quiz (comparaison insensible Ã  la casse)
                        if (strtolower($lesson['lesson_type']) != 'quiz') {
                            $default_lesson = $lesson;
                            $default_section_id = $section['id'];
                            break 2; // Sortir des deux boucles
                        }
                    }
                }
                
                // Si aucune leÃ§on non-quiz n'est trouvÃ©e, prendre la premiÃ¨re leÃ§on disponible
                if ($default_lesson === null) {
                    $first_section = $sections_rows[0] ?? null;
                    $lessons = $first_section ? $as_result_array($this->lms_model->get_lessons('section', $first_section['id'])) : [];
                    if (!empty($lessons)) {
                        $default_lesson = $lessons[0];
                        $default_section_id = $first_section['id'];
                    }
                }
                
                if ($default_lesson !== null) {
                    $lesson_id = $default_lesson['id'];
                    $page_data['lesson_id'] = $default_lesson['id'];
                    $page_data['section_id'] = $default_section_id;
                } else {
                    $page_data['page_name'] = 'empty';
                    $page_data['page_title'] = get_phrase('no_lesson_found');
                    $page_data['page_body'] = get_phrase('no_lesson_found');
                }
            }else {
                $page_data['lesson_id']  = $lesson_id;
                $section_id = $this->lms_model->get_row('lesson', ['id' => $lesson_id]);
                $section_id = $section_id ? $section_id['section_id'] : null;
                $page_data['section_id'] = $section_id;
            }

        }else {
            $page_data['sections'] = array();
            $page_data['page_name'] = 'empty';
            $page_data['page_title'] = get_phrase('no_section_found');
            $page_data['page_body'] = get_phrase('no_section_found');
        }


        $page_data['course_id']  = $course_id;
        $page_data['page_name']  = 'lessons';
        $page_data['page_title'] = $course_details['title'];
        return view('lessons/index', $page_data);
    }

    public function submit_quiz($from = "") {
    $submitted_quiz_info = array();
    $container = array();
    $quiz_id = $this->request->getPost('lesson_id');
    $user_id = session()->get('user_id');
    $overwrite_results = $this->request->getPost('overwrite_results') == '1';

    // If overwrite_results is true, delete existing responses for this user and quiz
    if ($overwrite_results) {
        $this->db->table('quiz_responses')
            ->where('user_id', $user_id)
            ->where('quiz_id', $quiz_id)
            ->delete();
    }

    $quiz_questions = $this->toArrayResult($this->lms_model->get_quiz_questions($quiz_id));
    $total_correct_answers = 0;

    foreach ($quiz_questions as $quiz_question) {
        $submitted_answer_status = 0;
        $correct_answers = json_decode($quiz_question['correct_answers']);
        $submitted_answers = array();
        $post_answers = $this->request->getPost($quiz_question['id']);
        if (is_array($post_answers)) {
            foreach ($post_answers as $each_submission) {
                if (isset($each_submission)) {
                    array_push($submitted_answers, $each_submission);
                }
            }
        }
        sort($correct_answers);
        sort($submitted_answers);
        if ($correct_answers == $submitted_answers) {
            $submitted_answer_status = 1;
            $total_correct_answers++;
        }
        $container = array(
            "question_id" => $quiz_question['id'],
            'submitted_answer_status' => $submitted_answer_status,
            "submitted_answers" => json_encode($submitted_answers),
            "correct_answers"  => json_encode($correct_answers),
        );
        array_push($submitted_quiz_info, $container);

        // Insert the new response
        $response_data = array(
            'user_id' => $user_id,
            'quiz_id' => $quiz_id,
            'question_id' => $quiz_question['id'],
            'submitted_answers' => json_encode($submitted_answers),
            'correct_answers' => json_encode($correct_answers),
            'submitted_answer_status' => $submitted_answer_status
        );
        $this->db->table('quiz_responses')->insert($response_data);
    }

    // RÃ©cupÃ©rer les informations du cours pour le bouton "Refaire le quiz"
    $lesson_details = $this->lms_model->get_row('lesson', ['id' => $quiz_id]);
    $course_id = $lesson_details['course_id'];
    $course_details = $this->lms_model->get_course_by_id($course_id);
    
    $page_data['submitted_quiz_info']   = $submitted_quiz_info;
    $page_data['total_correct_answers'] = $total_correct_answers;
    $page_data['total_questions'] = count($quiz_questions);
    $page_data['quiz_id'] = $quiz_id;
    $page_data['course_id'] = $course_id;
    $page_data['course_slug'] = slugify($course_details['title']);
    return view('lessons/quiz_result', $page_data);
}
    public function check_result() {
        $submitted_quiz_info = array();
                $container = array();
                $quiz_id = $this->request->getPost('lesson_id');
                $user_id = session()->get('user_id'); // Assurez-vous d'obtenir l'ID de l'utilisateur connectÃ©

                // RÃ©cupÃ©rer les rÃ©ponses soumises depuis la table 'quiz_responses'
                $quiz_responses = $this->lms_model->get_where_result('quiz_responses', ['user_id' => $user_id, 'quiz_id' => $quiz_id]);

                // RÃ©cupÃ©rer les questions du quiz
                $quiz_questions = $this->toArrayResult($this->lms_model->get_quiz_questions($quiz_id));
                $total_correct_answers = 0;

                foreach ($quiz_questions as $quiz_question) {
                    $submitted_answer_status = 0;
                    $correct_answers = json_decode($quiz_question['correct_answers']);
                    $submitted_answers = array();

                    // Rechercher les rÃ©ponses de l'utilisateur pour la question actuelle
                    foreach ($quiz_responses as $response) {
                        if ($response['question_id'] == $quiz_question['id']) {
                            $submitted_answers = json_decode($response['submitted_answers']);
                            $submitted_answer_status = $response['submitted_answer_status'];
                            break;
                        }
                    }

                    if ($correct_answers == $submitted_answers) {
                        $submitted_answer_status = 1;
                        $total_correct_answers++;
                    }
                    
                    $container = array(
                        "question_id" => $quiz_question['id'],
                        "question_title" => $quiz_question['title'], // Optionnel : afficher la question
                        'submitted_answer_status' => $submitted_answer_status,
                        "submitted_answers" => json_encode($submitted_answers),
                        "correct_answers"  => json_encode($correct_answers),
                    );
                    array_push($submitted_quiz_info, $container);
                }

                // RÃ©cupÃ©rer les informations du cours pour le bouton "Refaire le quiz"
                $lesson_details = $this->lms_model->get_row('lesson', ['id' => $quiz_id]);
                $course_id = $lesson_details['course_id'];
                $course_details = $this->lms_model->get_course_by_id($course_id);
                
                $page_data['submitted_quiz_info']   = $submitted_quiz_info;
                $page_data['total_correct_answers'] = $total_correct_answers;
                $page_data['total_questions'] = count($quiz_questions);
                $page_data['quiz_id'] = $quiz_id;
                $page_data['course_id'] = $course_id;
                $page_data['course_slug'] = slugify($course_details['title']);
                return view('lessons/quiz_result', $page_data);
    }
    public function check_result_pop_up() {
        $submitted_quiz_info = array();
        $container = array();
        $quiz_id = $this->request->getPost('quiz_id');
        $user_id = $this->request->getPost('user_id');
        // print_r($quiz_id ) ;die;
        // VÃ©rification des valeurs de $quiz_id et $user_id
        // log_message('debug', 'Quiz ID: ' . $quiz_id);
        // log_message('debug', 'User ID: ' . $user_id);
    
        // RÃ©cupÃ©rer les rÃ©ponses soumises depuis la table 'quiz_responses'
        $quiz_responses = $this->lms_model->get_where_result('quiz_responses', ['user_id' => $user_id, 'quiz_id' => $quiz_id]);
       
        // VÃ©rification des rÃ©ponses rÃ©cupÃ©rÃ©es
        log_message('debug', 'Quiz Responses: ' . print_r($quiz_responses, true));
    
        // RÃ©cupÃ©rer les questions du quiz
        $quiz_questions = $this->toArrayResult($this->lms_model->get_quiz_questions($quiz_id));
        $total_correct_answers = 0;
    
        // VÃ©rification des questions rÃ©cupÃ©rÃ©es
        log_message('debug', 'Quiz Questions: ' . print_r($quiz_questions, true));
    
        foreach ($quiz_questions as $quiz_question) {
            $submitted_answer_status = 0;
            $correct_answers = json_decode($quiz_question['correct_answers']);
            $submitted_answers = array();
    
            // Rechercher les rÃ©ponses de l'utilisateur pour la question actuelle
            foreach ($quiz_responses as $response) {
                if ($response['question_id'] == $quiz_question['id']) {
                    $submitted_answers = json_decode($response['submitted_answers']);
                    $submitted_answer_status = $response['submitted_answer_status'];
                    break;
                }
            }
    
            if ($correct_answers == $submitted_answers) {
                $submitted_answer_status = 1;
                $total_correct_answers++;
            }
    
            $container = array(
                "question_id" => $quiz_question['id'],
                "question_title" => $quiz_question['title'],
                'submitted_answer_status' => $submitted_answer_status,
                "submitted_answers" => json_encode($submitted_answers),
                "correct_answers"  => json_encode($correct_answers),
            );
            array_push($submitted_quiz_info, $container);
        }
    
        $page_data['submitted_quiz_info']   = $submitted_quiz_info;
        $page_data['total_correct_answers'] = $total_correct_answers;
        $page_data['total_questions'] = count($quiz_questions);
    
        // Chargement de la vue

        // Charger la vue mise Ã  jour
		$response_html = view('backend/superadmin/quiz/quiz_popup_result', $page_data, ['cache' => 0]);
		// PrÃ©parer le nouveau jeton CSRF
		$csrf = array(
					'csrfName' => csrf_token(),
					'csrfHash' => csrf_hash(),
				);
			
		// Renvoyer la rÃ©ponse JSON avec le HTML mis Ã  jour et le nouveau jeton CSRF
		echo json_encode(array('status' => $response_html, 'csrf' => $csrf));
    }


    // Pagination AJAX des sections pour le sidebar
    public function ajax_get_sections_paginated($course_id) {
        $page = (int) esc($this->request->getGet('page')) ?? 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;
        
        $sections = $this->lms_model->get_sections_paginated($course_id, $limit, $offset);
        $total_sections = $this->lms_model->count_sections($course_id);
        $total_pages = ceil($total_sections / $limit);
        
        // Charger les leÃ§ons pour chaque section
        foreach ($sections as &$section) {
            $section['lessons'] = $this->toArrayResult($this->lms_model->get_lessons('section', $section['id']));
        }
        
        // Calculer le numÃ©ro de dÃ©part des sections pour cette page
        $start_number = $offset + 1;
        
        $response = array(
            'status' => true,
            'sections' => $sections,
            'current_page' => $page,
            'total_pages' => $total_pages,
            'total_sections' => $total_sections,
            'start_number' => $start_number
        );
        
        header('Content-Type: application/json');
        echo json_encode($response);
    }

    public function afficher_fichier($nom_fichier)
    {
        $chemin = FCPATH . 'uploads/' . $nom_fichier;

        if (file_exists($chemin)) {
            $type = mime_content_type($chemin);

            switch ($type) {
                case 'application/pdf':
                    header('Content-type: application/pdf');
                    readfile($chemin);
                    break;

                case 'image/jpeg':
                case 'image/png':
                case 'image/gif':
                    header('Content-type: ' . $type);
                    readfile($chemin);
                    break;

                case 'video/mp4':
                case 'video/webm':
                case 'video/ogg':
                    header('Content-type: ' . $type);
                    readfile($chemin);
                    break;

                case 'application/msword':
                case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
                case 'application/vnd.ms-excel':
                case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet':
                    // TÃ©lÃ©charger directement ces fichiers
                    header('Content-Disposition: inline; filename="' . $nom_fichier . '"');
                    header('Content-type: ' . $type);
                    readfile($chemin);
                    break;

                default:
                    show_error('Ce type de fichier ne peut pas Ãªtre affichÃ© directement.');
            }
        } else {
            show_404();
        }
    }

    private function toArrayResult($result): array
    {
        if (is_array($result)) {
            return $result;
        }
        if (is_object($result)) {
            if (method_exists($result, 'result_array')) {
                return $result;
            }
            if (method_exists($result, 'getResultArray')) {
                return $result->getResultArray();
            }
            if (method_exists($result, 'getResult')) {
                $rows = $result->getResult();
                return is_array($rows) ? $rows : [];
            }
        }
        return [];
    }

    

}
