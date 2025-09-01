<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
*  @author   : Creativeitem
*  date      : November, 2019
*  Ekattor School Management System With Addons
*  http://codecanyon.net/user/Creativeitem
*  http://support.creativeitem.com
*/

class Student extends CI_Controller {
	public function __construct(){

		parent::__construct();

		$this->load->database();
		$this->load->library('Humhub_sso');
		$this->load->library('session');
		$this->config->load('config'); 
    	
		 require_once APPPATH . '../vendor/autoload.php';

		/*LOADING ALL THE MODELS HERE*/
		$this->load->model('Crud_model',     'crud_model');
		$this->load->model('User_model',     'user_model');
		$this->load->model('Settings_model', 'settings_model');
		$this->load->model('Payment_model',  'payment_model');
		$this->load->model('Email_model',    'email_model');
		$this->load->model('Addon_model',    'addon_model');
		$this->load->model('Frontend_model', 'frontend_model');
		$this->load->model('Room_model','room_model');

		/*cache control*/
		$this->output->set_header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");
		$this->output->set_header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
		$this->output->set_header("Cache-Control: post-check=0, pre-check=0", false);
		$this->output->set_header("Pragma: no-cache");

		/*SET DEFAULT TIMEZONE*/
		timezone();

		// CHECK WHETHER student IS LOGGED IN
		if($this->session->userdata('student_login') != 1){
			redirect(site_url('login'), 'refresh');
		}
	}

	// INDEX FUNCTION
	public function index(){
		redirect(site_url('student/dashboard'), 'refresh');
	}
	   //START TEACHER Create_Join bigbleubutton 
	   public function Join_Session($param1 = '', $param2 = '', $param3 = '')
	   {
	 
   
	 
		 if (empty($param1)) {
		   $page_data['folder_name'] = 'bigbleubutton';
		   $page_data['page_title'] = 'Démarrer Réunion';
		   $this->load->view('backend/index', $page_data);
		 }
	   }
	   //END TEACHER Create_Join bigbleubutton 

	//DASHBOARD
	public function dashboard(){
		// $page_data['page_title'] = 'Dashboard';
		// $page_data['folder_name'] = 'dashboard';
		  // 1) Vérifier que c'est bien un student
			if (! $this->session->userdata('student_login')) {
				show_error('Accès réservé aux students.');
			}
			
			// 2) Récupérer les infos du user (ici : depuis la table Wayo)
			$userId = $this->session->userdata('user_id');
			log_message('debug', 'USER ID SESSION: ' . $userId);
			$wUser  = $this->db->get_where('users', ['id' => $userId])->row();
			if (empty($wUser) || ! filter_var($wUser->email, FILTER_VALIDATE_EMAIL)) {
				log_message('error', 'Invalid Wayo user data: ' . print_r($wUser, true));
				show_error('Impossible de retrouver votre compte Wayo pour SSO.');
			}
			log_message('debug', 'Wayo user data: ' . print_r($wUser, true));

			// 3) Stocker temporairement cet objet pour la librairie SSO
			$this->session->set_userdata('user', $wUser);

			// 4) Générer l’URL SSO
			$iframeUrl = $this->humhub_sso->provisionAndGetIframeUrl();
			log_message('debug', 'Generated iframe URL: ' . $iframeUrl);

			if (! $iframeUrl) {
				show_error('Impossible de générer l’URL SSO HumHub.');
			}
			
			// 5) Passer à la vue
			$page_data = [
				'folder_name' => 'dashboard',
				'page_title'  => 'Dashboard',
				
				'iframe_url'  => $iframeUrl,
			];
		
		$this->load->view('backend/index', $page_data);
	}
	public function get_appointments() {
		$appointments = $this->room_model->get_all_appointments_student();
		echo json_encode($appointments);
	}
	

	//START CLASS secion
	public function manage_class($param1 = '', $param2 = '', $param3 = ''){
		if($param1 == 'section'){
			$response = $this->crud_model->section_update($param2);
			echo $response;
		}

		// show data from database
		if ($param1 == 'list') {
			$this->load->view('backend/student/class/list');
		}

		if(empty($param1)){
			$page_data['folder_name'] = 'class';
			$page_data['page_title'] = 'class';
			$this->load->view('backend/index', $page_data);
		}
	}
	//END CLASS section
	//	SECTION STARTED
	public function section($action = "", $id = "") {
		if ($action == 'list') {
			$user_id = $this->session->userdata('user_id');
			$session_id = active_session();
	
			// Récupérer les sections où l'étudiant est inscrit pour la classe donnée
			$this->db->select('sections.id, sections.name');
			$this->db->from('sections');
			$this->db->join('enrols', 'enrols.section_id = sections.id', 'left');
			$this->db->join('students', 'students.id = enrols.student_id', 'left');
			$this->db->where('sections.class_id', $id);
			$this->db->where('students.user_id', $user_id);
			$this->db->where('enrols.session', $session_id);
			$sections = $this->db->get()->result_array();
	
			$output = '<option value="">' . get_phrase('select_section') . '</option>';
			foreach ($sections as $section) {
				$output .= '<option value="' . $section['id'] . '">' . $section['name'] . '</option>';
			}
			echo $output;
		}
	}
    public function section_course($action = "", $id = "") {
		if ($action == 'list') {
			$user_id = $this->session->userdata('user_id');
			$session_id = active_session();
	
			// Récupérer les sections où l'étudiant est inscrit pour la classe donnée
			$this->db->select('sections.id, sections.name');
			$this->db->from('sections');
			// $this->db->join('enrols', 'enrols.section_id = sections.id', 'left');
			// $this->db->join('students', 'students.id = enrols.student_id', 'left');
			$this->db->where('sections.class_id', $id);
			// $this->db->where('students.user_id', $user_id);
			// $this->db->where('enrols.session', $session_id);
			$sections = $this->db->get()->result_array();
	
			$output = '<option value="">' . get_phrase('select_section') . '</option>';
			foreach ($sections as $section) {
				$output .= '<option value="' . $section['id'] . '">' . $section['name'] . '</option>';
			}
			echo $output;
		}
	}
    
	//	SECTION ENDED
      //START student Create_Join bigbleubutton 
      public function Recording($param1 = '', $param2 = '', $param3 = '')
      {
    
        $school_id = school_id();

        // Récupère les données nécessaires
        $page_data['appointments'] = $this->room_model->get_all_appointments_student();
        $page_data['classes'] = $this->db->get_where('classes', array('school_id' => $school_id))->result_array();
        $page_data['rooms'] = $this->db->get_where('rooms', array('school_id' => $school_id, 'Etat' => 1))->result_array();

        // Récupère les enregistrements pour chaque rendez-vous
        foreach ($page_data['appointments'] as &$appointment) {
            $appointment['recordings'] = $this->room_model->get_bbb_recording_by_appointment($appointment['id']);
           
        }
        unset($appointment); // Nettoie la référence
    
  
        $page_data['page_name'] = 'bigbleubutton/Recording';
        $page_data['page_title'] = 'Recording';

        $this->load->view('backend/index', $page_data);

   
      }
      //END student Create_Join bigbleubutton

	  
	  public function filter_recordings()
	  {
		  $this->load->config('bigbluebutton');
		  $bbbUrl = $this->config->item('bbb_url');
		  $bbbSecret = $this->config->item('bbb_secret');
	  
		  $meeting_name = $this->input->post('meeting_name', true);
		  $date_range = $this->input->post('date_range', true);
		  $school_id = $this->input->post('school_id', true);
	  
		  // $this->db->select('*')->from('appointments');
		  $schoolID = school_id();
		  // die($schoolID);
		  // Sélection des colonnes nécessaires

		  $this->db->select('
			  appointments.id, 
			  appointments.title, 
			  appointments.start_date AS start, 
			
			  appointments.description, 
			  appointments.sections_id AS section, 
			  appointments.classe_id, 
			  appointments.room_id, 
			 
			  rooms.name,
			  meeting_id,

		  ');
		  $this->db->from('appointments');
	  
		  // Jointure avec la table rooms pour récupérer les informations des salles
		  $this->db->join('rooms', 'rooms.id = appointments.room_id', 'left');
	  
		  // Filtre : récupérer uniquement les rendez-vous actifs
		  $this->db->where('appointments.Etat', 1);
		  //$this->db->where('rooms.school_id', $schoolID);
		  $this->db->where('appointments.school_id', $school_id);
	
	  
		
	  
		  if (!empty($meeting_name)) {
			  $this->db->like('appointments.title', $meeting_name);
		  }
	  
		  if (!empty($date_range)) {
			  $dates = explode(' to ', $date_range);
			  if (count($dates) === 2) {
				  $this->db->where('appointments.start_date >=', $dates[0] . ' 00:00:00');
				  $this->db->where('appointments.start_date <=', $dates[1] . ' 23:59:59');
			  }
		  }
	  
		  $appointments = $this->db->get()->result_array();
	  
		  foreach ($appointments as &$appointment_rec) {
			  $meetingId = $appointment_rec['meeting_id'] ?? null;
			  $appointment_rec['recordings'] = [];
		
			  if ($meetingId) {
				  // Générer l’URL avec meetingID spécifique
				  // $query = http_build_query(['meetingID' => $meetingId]);
				  // $checksum = sha1('getRecordings' . $query . $bbbSecret);
				  // $url = $bbbUrl . 'api/getRecordings?' . $query . '&checksum=' . $checksum;
				  $params = ['meetingID' => $meetingId];
				  $query = http_build_query($params);
				  $checksum = sha1('getRecordings' . $query . $bbbSecret);
				  $url = $bbbUrl . 'getRecordings?' . $query . '&checksum=' . $checksum;
	  
				  // Appel de l’API
				  $ch = curl_init();
				  curl_setopt($ch, CURLOPT_URL, $url);
				  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
				  $response = curl_exec($ch);
				  curl_close($ch);
	  
				  $xml = @simplexml_load_string($response);

				  if ($xml && $xml->returncode == 'SUCCESS') {
					  foreach ($xml->recordings->recording as $rec) {
						  $appointment_rec['recordings'][] = [
							  'meetingID' => (string) $rec->meetingID,
							  'playback_url' => (string) $rec->playback->format->url,
							  'duration' => (string) $rec->playback->format->length,
							  'video_download_url' => (string) $rec->playback->format->url,
							  'endTime' => (string) $rec->endTime
						  ];
					  }
				  }
			  }
		  }
	  
		  // Affichage HTML comme avant
		  foreach ($appointments as $appointment) {
			$classe_name = $this->db->get_where('classes', array('id' => $appointment['classe_id']))->row('name');
			$section = " - ";
			  if (!empty($appointment['section'])) {
						  $section_ids = explode(',', $appointment['section']);
						  $section_names = [];
							  foreach ($section_ids as $id) {
										  $name = $this->db->get_where('sections', array('id' => $id))->row('name');
										  if ($name) $section_names[] = $name;
									  }
									$section =  implode(', ', $section_names);
								  } 
			  echo '<tr>';
			  echo '<td>' . htmlspecialchars($appointment['title']) . '</td>';
			  echo '<td>' . htmlspecialchars($appointment['name']) . '</td>';
			  echo '<td>' . htmlspecialchars($classe_name) . '</td>';
			  echo '<td>' . htmlspecialchars($section) . '</td>';
			  echo '<td>' . date('d-m-Y H:i', strtotime($appointment['start'])) . '</td>';
			  echo '<td>' . (!empty($appointment_rec['recordings']) ? $appointment_rec['recordings'][0]['duration'] : '—') . '</td>';
			  echo '<td>';
	  
			  if (!empty($appointment['recordings'])) {
				  $rec = $appointment_rec['recordings'][0];
				  $endTime = !empty($rec['endTime']) ? (int)$rec['endTime'] / 1000 : null;
				  $isExpired = $endTime ? (time() > ($endTime + (7 * 24 * 60 * 60))) : false;
	  
				  if ($isExpired) {
					  echo '<span class="badge bg-warning text-dark"> Expired</span>';
				  } elseif (!empty($rec['playback_url'])) {
					  echo '<a href="' . htmlspecialchars($rec['playback_url']) . '" target="_blank" class="btn btn-sm btn-success">VIDEO</a>';
				  } else {
					  echo '<span class="badge bg-danger">NOT RECORDED</span>';
				  }
			  } else {
				  echo '<span class="badge bg-danger">NOT RECORDED</span>';
			  }
	  
			  echo '</td><td>';
	  
			  if (!empty($appointment_rec['recordings'])) {
				  $rec = $appointment_rec['recordings'][0];
				  echo '<a href="' . htmlspecialchars($rec['video_download_url']) . '" class="btn btn-sm btn-success">Download</a> ';
			  }
	  
		
			  echo '</td></tr>';
		  }
	  }
		//	SECTION STARTED
		public function exam_class($action = "", $id = "") {
			if ($action == 'list') {
				$user_id = $this->session->userdata('user_id');
				$session_id = active_session();
		
				// Récupérer les classes où l'étudiant est inscrit pour l'examen donné
				$this->db->select('classes.id, classes.name');
				$this->db->from('classes');
				$this->db->join('enrols', 'enrols.class_id = classes.id', 'left');
				$this->db->join('students', 'students.id = enrols.student_id', 'left');
				$this->db->join('exams', 'exams.class_id = classes.id', 'left');
				$this->db->where('exams.id', $id);
				$this->db->where('students.user_id', $user_id);
				$this->db->where('enrols.session', $session_id);
				$classes = $this->db->get()->result_array();
		
				$output = '<option value="">' . get_phrase('select_a_class') . '</option>';
				foreach ($classes as $class) {
					$output .= '<option value="' . $class['id'] . '">' . $class['name'] . '</option>';
				}
				echo $output;
			}
		}
		//	SECTION ENDED

	//START SUBJECT section
	public function subject($param1 = '', $param2 = ''){

		if($param1 == 'list'){
			$page_data['class_id'] = $param2;
			$this->load->view('backend/student/subject/list', $page_data);
		}

		if(empty($param1)){
			$page_data['folder_name'] = 'subject';
			$page_data['page_title'] = 'subject';
			$this->load->view('backend/index', $page_data);
		}
	}

	public function class_wise_subject($class_id) {

		// PROVIDE A LIST OF SUBJECT ACCORDING TO CLASS ID
		$page_data['class_id'] = $class_id;
		$this->load->view('backend/student/subject/dropdown', $page_data);
	}
	//END SUBJECT section


	//START SYLLABUS section
	public function syllabus($param1 = '', $param2 = '', $param3 = ''){

		if($param1 == 'list'){
			$page_data['class_id'] = $param2;
			$page_data['section_id'] = $param3;
			$this->load->view('backend/student/syllabus/list', $page_data);
		}

		if(empty($param1)){
			$page_data['folder_name'] = 'syllabus';
			$page_data['page_title'] = 'syllabus';
			$this->load->view('backend/index', $page_data);
		}
	}
	//END SYLLABUS section
  // LANGUAGE SETTINGS
  public function language($param1 = "", $param2 = "")
  {
    // adding language
    // if ($param1 == 'create') {
    //   $response = $this->settings_model->create_language();
    //   // echo $response;
    //   // Préparer la réponse avec un nouveau jeton CSRF
    //   $csrf = array(
    //     'csrfName' => $this->security->get_csrf_token_name(),
    //     'csrfHash' => $this->security->get_csrf_hash(),
    //           );
            
    //   // Renvoyer la réponse avec un nouveau jeton CSRF
    //   echo json_encode(array('status' => $response, 'csrf' => $csrf));
    // }

    // update language
    // if ($param1 == 'update') {
    //   $response = $this->settings_model->update_language($param2);
    //   // echo $response;
    //   // Préparer la réponse avec un nouveau jeton CSRF
    //   $csrf = array(
    //     'csrfName' => $this->security->get_csrf_token_name(),
    //     'csrfHash' => $this->security->get_csrf_hash(),
    //           );
            
    //   // Renvoyer la réponse avec un nouveau jeton CSRF
    //   echo json_encode(array('status' => $response, 'csrf' => $csrf));
    // }

    // deleting language
    // if ($param1 == 'delete') {
    //   $response = $this->settings_model->delete_language($param2);
    //   // echo $response;
    //   // Préparer la réponse avec un nouveau jeton CSRF
    //   $csrf = array(
    //     'csrfName' => $this->security->get_csrf_token_name(),
    //     'csrfHash' => $this->security->get_csrf_hash(),
    //           );
            
    //   // Renvoyer la réponse avec un nouveau jeton CSRF
    //   echo json_encode(array('status' => $response, 'csrf' => $csrf));
    // }

    // // showing the list of language
    // if ($param1 == 'list') {
    //   $this->load->view('backend/superadmin/language/list');
    // }

	 if ($param1 == 'active') {
			$user_id = $this->session->userdata('user_id');
			$this->settings_model->update_system_language($user_id, $param2);

			// Redirige vers la page précédente si elle existe, sinon vers le dashboard
			$referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
			if (!empty($referer)) {
				redirect($referer, 'refresh');
			} else {
				redirect(site_url('home'), 'refresh');
			}
		}
  

    // showing the list of language
    // if ($param1 == 'update_phrase') {
    //   $current_editing_language = htmlspecialchars($this->input->post('currentEditingLanguage'));
    //   $updatedValue = htmlspecialchars($this->input->post('updatedValue'));
    //   $key = htmlspecialchars($this->input->post('key'));
    //   saveJSONFile($current_editing_language, $key, $updatedValue);
    //   $response =  $current_editing_language . ' ' . $key . ' ' . $updatedValue;
    //   // Préparer la réponse avec un nouveau jeton CSRF
    //   $csrf = array(
    //     'csrfName' => $this->security->get_csrf_token_name(),
    //     'csrfHash' => $this->security->get_csrf_hash(),
    //           );
            
    //   // Renvoyer la réponse avec un nouveau jeton CSRF
    //   echo json_encode(array('response' => $response, 'csrf' => $csrf));
    // }

    // GET THE DROPDOWN OF LANGUAGES
    if ($param1 == 'dropdown') {
      $this->load->view('backend/student/language/dropdown');
    }
    // showing the index file
    if (empty($param1)) {
      $page_data['folder_name'] = 'language';
      $page_data['page_title'] = 'languages';
      $this->load->view('backend/index', $page_data);
    }
  }

	//START TEACHER section
	public function teacher($param1 = '', $param2 = '', $param3 = ''){
		$page_data['folder_name'] = 'teacher';
		$page_data['page_title'] = 'techers';
		$this->load->view('backend/index', $page_data);
	}
	//END TEACHER section

	//START CLASS ROUTINE section
	public function routine($param1 = '', $param2 = '', $param3 = '', $param4 = ''){

		if($param1 == 'filter'){
			$page_data['class_id'] = $param2;
			$page_data['section_id'] = $param3;
			$this->load->view('backend/student/routine/list', $page_data);
		}

		if(empty($param1)){
			$page_data['folder_name'] = 'routine';
			$page_data['page_title'] = 'routine';
			$this->load->view('backend/index', $page_data);
		}
	}
	//END CLASS ROUTINE section


	//START DAILY ATTENDANCE section
	public function attendance($param1 = '', $param2 = '', $param3 = ''){
		if($param1 == 'filter'){
			$date = '01 '.$this->input->post('month').' '.$this->input->post('year');
			$page_data['attendance_date'] = strtotime($date);
			$page_data['class_id'] = htmlspecialchars($this->input->post('class_id'));
			$page_data['section_id'] = htmlspecialchars($this->input->post('section_id'));
			$page_data['school_id'] = htmlspecialchars($this->input->post('school_id'));
			$page_data['month'] = htmlspecialchars($this->input->post('month'));
			$page_data['year'] = htmlspecialchars($this->input->post('year'));
			// $this->load->view('backend/student/attendance/list', $page_data);
			// Charger la vue mise à jour
			$response_html = $this->load->view('backend/student/attendance/list', $page_data, TRUE);
		    // Préparer le nouveau jeton CSRF
			$csrf = array(
					 'csrfName' => $this->security->get_csrf_token_name(),
					 'csrfHash' => $this->security->get_csrf_hash(),
				 );
			
			// Renvoyer la réponse JSON avec le HTML mis à jour et le nouveau jeton CSRF
			echo json_encode(array('status' => $response_html, 'csrf' => $csrf));
		}

		if(empty($param1)){
			$page_data['folder_name'] = 'attendance';
			$page_data['page_title'] = 'attendance';
			$this->load->view('backend/index', $page_data);
		}
	}
	//END DAILY ATTENDANCE section

  //	academy STARTED
  public function academy($action = "", $id = "") {
		
    // PROVIDE A LIST OF SECTION ACCORDING TO CLASS ID
    if ($action == 'list') {
		// echo $id;
      $page_data['school_id'] = $id;
      $this->load->view('backend/academy/liste_classe', $page_data);
    }
  }
  //	academy ENDED
  public function online_admission($param1 = "", $user_id = "")
  {

	
    if ($param1 == 'assigned') {
		
	 // Stocker les données de l'inscription dans la session pour un accès ultérieur
		$data['student_id'] = htmlspecialchars($this->input->post('student_id'));
		$data['class_id'] = htmlspecialchars($this->input->post('class_id'));
		$data['section_id'] = htmlspecialchars($this->input->post('section_id'));
		$data['school_id'] = htmlspecialchars($this->input->post('school_id'));
		$data['price'] = htmlspecialchars($this->input->post('price'));
		$data['currency'] = htmlspecialchars($this->input->post('currency'));
		$data['session'] = active_session();
	
	  	$this->session->set_userdata('enrolment_data', $data);


		$num_rows_invoices = $this->db->get_where('invoices', array('class_id' => $data['class_id'],'student_id' => $data['student_id']))->num_rows();
		// print_r($num_rows_invoices);die;
		if($num_rows_invoices == 0){
			$name = $this->db->get_where('schools', array('id' => $data['school_id']))->row('name');
			$classe_name = $this->db->get_where('classes', array('id' => $data['class_id']))->row('name');
			$data_invoice['title'] = $name." - ".$classe_name ;
			$data_invoice['total_amount'] = $data['price'];
			$data_invoice['class_id'] = $data['class_id'] ;
			$data_invoice['student_id'] = $data['student_id'];
			$data_invoice['status'] = "unpaid";
			$data_invoice['school_id'] =$data['school_id'];
			$data_invoice['session'] = $data['session'];
			$data_invoice['created_at'] = strtotime(date('d-M-Y'));
			$this->db->insert('invoices', $data_invoice);
			$invoice_id = $this->db->insert_id();
		}else{
			$invoice_id = $this->db->get_where('invoices', array('class_id' => $data['class_id'],'student_id' => $data['student_id']))->row('id');
		}


	  redirect(site_url('Student/payment/' . $invoice_id), 'refresh');

    //   $this->session->set_flashdata('flash_message', get_phrase('admission_request_has_been_updated'));
    //   redirect(site_url('addons/courses'), 'refresh');
    }








    $page_data['folder_name'] = 'academy';
    $page_data['page_title'] = 'academy';
    $this->load->view('backend/index', $page_data);
  }
	//START EVENT CALENDAR section
	public function event_calendar($param1 = '', $param2 = ''){

		if($param1 == 'all_events'){
			echo $this->crud_model->all_events();
		}

		if ($param1 == 'list') {
			$this->load->view('backend/student/event_calendar/list');
		}

		if(empty($param1)){
			$page_data['folder_name'] = 'event_calendar';
			$page_data['page_title'] = 'event_calendar';
			$this->load->view('backend/index', $page_data);
		}
	}
	//END EVENT CALENDAR section

	//START EXAM section
	public function exam($param1 = '', $param2 = ''){

		if ($param1 == 'list') {
			$this->load->view('backend/student/exam/list');
		}

		if(empty($param1)){
			$page_data['folder_name'] = 'exam';
			$page_data['page_title'] = 'exam';
			$this->load->view('backend/index', $page_data);
		}
	}
	//END EXAM section

		//HUMHUB DASHBOARD
		public function wall()
		{
			// 1) Vérifier que c'est bien un student
			if (! $this->session->userdata('student_login')) {
				show_error('Accès réservé aux students.');
			}
			
			// 2) Récupérer les infos du user (ici : depuis la table Wayo)
			$userId = $this->session->userdata('user_id');
			$wUser  = $this->db->get_where('users', ['id' => $userId])->row();
			if (empty($wUser) || ! filter_var($wUser->email, FILTER_VALIDATE_EMAIL)) {
				log_message('error', 'Invalid Wayo user data: ' . print_r($wUser, true));
				show_error('Impossible de retrouver votre compte Wayo pour SSO.');
			}
			log_message('debug', 'Wayo user data: ' . print_r($wUser, true));

			// 3) Stocker temporairement cet objet pour la librairie SSO
			$this->session->set_userdata('user', $wUser);

			// 4) Générer l’URL SSO
			$iframeUrl = $this->humhub_sso->provisionAndGetIframeUrl();
			log_message('debug', 'Generated iframe URL: ' . $iframeUrl);

			if (! $iframeUrl) {
				show_error('Impossible de générer l’URL SSO HumHub.');
			}
			
			// 5) Passer à la vue
			$page_data = [
				'folder_name' => 'humhub',
				'page_title'  => 'wall',
				'page_name'   => 'wall',
				'iframe_url'  => $iframeUrl,
			];
			$this->load->view('backend/index', $page_data);
		}
			//HUMHUB PEOPLE
		public function people()
		{
			// 1) Vérifier que c'est bien un student
			if (! $this->session->userdata('student_login')) {
				show_error('Accès réservé aux students.');
			}
			
			// 2) Récupérer les infos du user (ici : depuis la table Wayo)
			$userId = $this->session->userdata('user_id');
			$wUser  = $this->db->get_where('users', ['id' => $userId])->row();

			if (empty($wUser) || ! filter_var($wUser->email, FILTER_VALIDATE_EMAIL)) {
				log_message('error', 'Invalid Wayo user data: ' . print_r($wUser, true));
				show_error('Impossible de retrouver votre compte Wayo pour SSO.');
			}
			log_message('debug', 'Wayo user data: ' . print_r($wUser, true));

			// 3) Stocker temporairement cet objet pour la librairie SSO
			$this->session->set_userdata('user', $wUser);

			// 4) Générer l’URL SSO
			$iframeUrl = $this->humhub_sso->provisionAndGetIframeUrl();
			log_message('debug', 'Generated iframe URL: ' . $iframeUrl);
		
			if (! $iframeUrl) {
				show_error('Impossible de générer l’URL SSO HumHub.');
			}

			// 5) Passer à la vue
			$page_data = [
				'folder_name' => 'humhub',
				'page_title'  => 'People',
				'page_name'   => 'people',
				'iframe_url'  => $iframeUrl,
			];
			$this->load->view('backend/index', $page_data);
		}

		//HUMHUB SPEACES
		public function spaces() {
			// 1) Vérifier que c'est bien un student
			if (! $this->session->userdata('student_login')) {
				show_error('Accès réservé aux students.');
			}
			
			// 2) Récupérer les infos du user (ici : depuis la table Wayo)
			$userId = $this->session->userdata('user_id');
			$wUser  = $this->db->get_where('users', ['id' => $userId])->row();
			if (empty($wUser) || ! filter_var($wUser->email, FILTER_VALIDATE_EMAIL)) {
				log_message('error', 'Invalid Wayo user data: ' . print_r($wUser, true));
				show_error('Impossible de retrouver votre compte Wayo pour SSO.');
			}
			log_message('debug', 'Wayo user data: ' . print_r($wUser, true));

			// 3) Stocker temporairement cet objet pour la librairie SSO
			$this->session->set_userdata('user', $wUser);

			// 4) Générer l’URL SSO
			$iframeUrl = $this->humhub_sso->provisionAndGetIframeUrl();
			log_message('debug', 'Generated iframe URL: ' . $iframeUrl);

			if (! $iframeUrl) {
				show_error('Impossible de générer l’URL SSO HumHub.');
			}

			// 5) Passer à la vue
			$page_data = [
				'folder_name' => 'humhub',
				'page_title'  => 'Spaces',
				'page_name'   => 'spaces',
				'iframe_url'  => $iframeUrl,
			];
			$this->load->view('backend/index', $page_data);
		
		}

		//HUMHUB MESSAGING
		public function chat() {
			// 1) Vérifier que c'est bien un student
			if (! $this->session->userdata('student_login')) {
				show_error('Accès réservé aux students.');
			}
			
			// 2) Récupérer les infos du user (ici : depuis la table Wayo)
			$userId = $this->session->userdata('user_id');
			
			$wUser  = $this->db->get_where('users', ['id' => $userId])->row();
			if (empty($wUser) || ! filter_var($wUser->email, FILTER_VALIDATE_EMAIL)) {
				log_message('error', 'Invalid Wayo user data: ' . print_r($wUser, true));
				show_error('Impossible de retrouver votre compte Wayo pour SSO.');
			}
			log_message('debug', 'Wayo user data: ' . print_r($wUser, true));

			// 3) Stocker temporairement cet objet pour la librairie SSO
			$this->session->set_userdata('user', $wUser);

			// 4) Générer l’URL SSO
			$iframeUrl = $this->humhub_sso->provisionAndGetIframeUrl();
			log_message('debug', 'Generated iframe URL: ' . $iframeUrl);

			if (! $iframeUrl) {
				show_error('Impossible de générer l’URL SSO HumHub.');
			}
		// Ajoute ça pour le badge :
			
			$unread_messages = $this->user_model->get_unread_messages_count($userId);
			// 5) Passer à la vue
			$page_data = [
				'folder_name' => 'humhub',
				'page_title'  => 'Messages',
				'page_name'   => 'message',
				'iframe_url'  => $iframeUrl,
				'unread_messages'  => $unread_messages,
			];
			$this->load->view('backend/index', $page_data);
		
		}

	//START MARKS section
	public function mark($param1 = '', $param2 = ''){

		if($param1 == 'list'){
			$page_data['class_id'] = htmlspecialchars($this->input->post('class_id'));
			$page_data['section_id'] = htmlspecialchars($this->input->post('section_id'));
			// $page_data['subject_id'] = htmlspecialchars($this->input->post('subject'));
			$page_data['exam_id'] = htmlspecialchars($this->input->post('exam'));
			// $this->crud_model->mark_insert($page_data['class_id'], $page_data['section_id'], $page_data['subject_id'], $page_data['exam_id']);
			// $this->load->view('backend/student/mark/list', $page_data);
			// Charger la vue mise à jour
			$response_html = $this->load->view('backend/student/mark/list', $page_data, TRUE);
		    // Préparer le nouveau jeton CSRF
			$csrf = array(
					 'csrfName' => $this->security->get_csrf_token_name(),
					 'csrfHash' => $this->security->get_csrf_hash(),
				 );
			
			// Renvoyer la réponse JSON avec le HTML mis à jour et le nouveau jeton CSRF
			echo json_encode(array('status' => $response_html, 'csrf' => $csrf));
		}

		if($param1 == 'mark_update'){
			$this->crud_model->mark_update();
		}

		if(empty($param1)){
			$page_data['folder_name'] = 'mark';
			$page_data['page_title'] = 'marks';
			$this->load->view('backend/index', $page_data);
		}
	}
	//END MARKS sesction

	// GRADE SECTION STARTS
	public function grade($param1 = "", $param2 = "") {
		$page_data['folder_name'] = 'grade';
		$page_data['page_title'] = 'grades';
		$this->load->view('backend/index', $page_data);
	}
	// GRADE SECTION ENDS

	// ACCOUNT SECTION STARTS
	public function invoice($param1 = "", $param2 = "") {
		// showing the list of invoices
		if ($param1 == 'invoice') {
			$page_data['invoice_id'] = $param2;
			$page_data['folder_name'] = 'invoice';
			$page_data['page_name'] = 'invoice';
			$page_data['page_title']  = 'invoice';
			$this->load->view('backend/index', $page_data);
		}

		// showing the index file
		if(empty($param1)){
			$page_data['folder_name'] = 'invoice';
			$page_data['page_title']  = 'invoice';
			$this->load->view('backend/index', $page_data);
		}
	}

	// PAYPAL CHECKOUT
	public function paypal_checkout() {
		$invoice_id = htmlspecialchars($this->input->post('invoice_id'));
		$invoice_details = $this->crud_model->get_invoice_by_id($invoice_id);

		$page_data['invoice_id']   = $invoice_id;
		$page_data['user_details']    = $this->user_model->get_student_details_by_id('student', $invoice_details['student_id']);
		$page_data['amount_to_pay']   = $invoice_details['total_amount'] - $invoice_details['paid_amount'];
		$page_data['folder_name'] = 'paypal';
		$page_data['page_title']  = 'paypal_checkout';
		$this->load->view('backend/payment_gateway/paypal_checkout', $page_data);
	}
	// STRIPE CHECKOUT
	public function stripe_checkout() {
		$invoice_id = htmlspecialchars($this->input->post('invoice_id'));
		$invoice_details = $this->crud_model->get_invoice_by_id($invoice_id);

		$page_data['invoice_id']   = $invoice_id;
		$page_data['user_details']    = $this->user_model->get_student_details_by_id('student', $invoice_details['student_id']);
		$page_data['amount_to_pay']   = $invoice_details['total_amount'] - $invoice_details['paid_amount'];
		$page_data['folder_name'] = 'paypal';
		$page_data['page_title']  = 'paypal_checkout';
		$this->load->view('backend/payment_gateway/stripe_checkout', $page_data);
	}

	private function add_student_to_class_space($student_id, $class_id)
    {
        // 1. Récupérer les infos de l'étudiant
        $student = $this->user_model->get_student_details_by_id('student', $student_id);
       
		// 2. Récupérer la classe et son humhub_space_id
		$class = $this->db
					->get_where('classes', ['id' => $class_id])
					->row_array();

		if (! $class) {
			log_message('error', "Classe #{$class_id} introuvable");
			return;
		}

		$space_id       = $class['humhub_space_id'];

		// 3. Récupérer l’ID HumHub de l’utilisateur via son email
		$humhubUser = $this->humhub_sso->getUserByEmail($student['email']);
		if (empty($humhubUser['id'])) {
			log_message('error', "Utilisateur HumHub introuvable pour {$student['email']}");
			return;
    }

		$humhub_user_id = $humhubUser['id'];  

		// 4. Appel à l’API HumHub pour ajouter le membre
		if (!empty($space_id) && !empty($humhub_user_id)) {
			$this->humhub_sso->addUserSpace($space_id, $humhub_user_id);
			log_message('debug', "Étudiant HumHub #{$humhub_user_id} ajouté à l’espace #{$space_id}");
		} else {
			log_message('error', "Impossible d’ajouter l’étudiant à l’espace (space_id ou humhub_user_id manquant)");
		}
    }
	
	public function payment_success($payment_method = "", $invoice_id = "", $amount_paid = "", $reference = "") {
		if ($payment_method == 'stripe') {
			$stripe = json_decode(get_payment_settings('stripe_settings'));
			$token_id = $this->input->post('stripeToken');
			$stripe_test_mode = $stripe[0]->stripe_mode;
            if ($stripe_test_mode == 'on') {
                $public_key = $stripe[0]->stripe_test_public_key;
                $secret_key = $stripe[0]->stripe_test_secret_key;
            } else {
                $public_key = $stripe[0]->stripe_live_public_key;
                $secret_key = $stripe[0]->stripe_live_secret_key;
            }
            $payment_status = $this->payment_model->stripe_payment($token_id, $invoice_id, $amount_paid, $secret_key);
		}elseif($payment_method == 'paystack'){
			$this->load->model('addons/paystack_model');
			$payment_status = $this->paystack_model->check_payment($reference);
		}

		$data['payment_method'] = $payment_method;
		$data['invoice_id'] = $invoice_id;
		$data['amount_paid'] = $amount_paid;
		
		//Pour chaque mode de paiement, si succès → marquer facture ET ajouter étudiant
    if ($payment_method === 'stripe'  && $payment_status === true ||
            $payment_method === 'paystack' && $payment_status === true ||
            $payment_method === 'paypal'  && $payment_status === true) {

        // Marquer la facture comme payée
       $this->crud_model->payment_success($data);
        // Récupérer les détails et ajouter l’étudiant à l’espace HumHub
        $details = $this->crud_model->get_invoice_by_id($invoice_id);
        $this->add_student_to_class_space($details['student_id'], $details['class_id']);

    } else {
        log_message('error', "Échec du paiement pour invoice #{$invoice_id} via {$payment_method}");
    }


		redirect(route('invoice'), 'refresh');
	}
	// ACCOUNT SECTION ENDS

	// BACKOFFICE SECTION

	//BOOK LIST MANAGER
	public function book($param1 = "", $param2 = "") {
		$page_data['folder_name'] = 'book';
		$page_data['page_title']  = 'books';
		$this->load->view('backend/index', $page_data);
	}

	// BOOK ISSUED BY THE STUDENT
	public function book_issue($param1 = "", $param2 = "") {
		// showing the index file
		$page_data['folder_name'] = 'book_issue';
		$page_data['page_title']  = 'issued_book';
		$this->load->view('backend/index', $page_data);
	}

	//MANAGE PROFILE STARTS
	public function profile($param1 = "", $param2 = "") {
		if ($param1 == 'update_profile') {
			$response = $this->user_model->update_profile();
			echo $response;
		}
		if ($param1 == 'update_password') {
			$response = $this->user_model->update_password();
			echo $response;
		}

		// showing the Smtp Settings file
		if(empty($param1)){
			$page_data['folder_name'] = 'profile';
			$page_data['page_title']  = 'manage_profile';
			$this->load->view('backend/index', $page_data);
		}
	}
	//MANAGE PROFILE ENDS

	public function payment($invoice_id = ""){
		$page_data['page_title']  = 'payment_gateway';
		$page_data['invoice_details'] = $this->crud_model->get_invoice_by_id($invoice_id);
		$this->load->view('backend/payment_gateway/index', $page_data);
	}

	// Récupérer les classes par école pour l'étudiant connecté
	public function get_classes_by_school() {
    if ($this->session->userdata('student_login') != 1) {
        $csrf = [
            'csrfName' => $this->security->get_csrf_token_name(),
            'csrfHash' => $this->security->get_csrf_hash(),
        ];
        log_message('error', 'get_classes_by_school - Unauthorized access attempt');
        echo json_encode(['status' => 'error', 'message' => 'Session expired, please login again', 'csrf' => $csrf]);
        return;
    }

    $user_id = $this->session->userdata('user_id');
    log_message('debug', 'get_classes_by_school - user_id: ' . ($user_id ?? 'null'));

    // Mapper user_id à student_id dans la table students
    $this->db->select('id');
    $this->db->from('students');
    $this->db->where('user_id', $user_id);
    $student = $this->db->get()->row_array();
    $student_id = $student['id'] ?? null;
    log_message('debug', 'get_classes_by_school - student_id: ' . ($student_id ?? 'null'));

    if (!$student_id) {
        $csrf = [
            'csrfName' => $this->security->get_csrf_token_name(),
            'csrfHash' => $this->security->get_csrf_hash(),
        ];
        log_message('error', 'get_classes_by_school - No student_id found for user_id: ' . $user_id);
        echo json_encode(['status' => 'error', 'message' => 'No student associated with this user', 'csrf' => $csrf]);
        return;
    }

    $school_id = $this->input->post('school_id', true);
    log_message('debug', 'get_classes_by_school - school_id: ' . ($school_id ?? 'null'));

    // Vérifier que school_id correspond à celui de l'utilisateur
    $user_details = $this->user_model->get_user_details($user_id);
    $user_school_id = $user_details['school_id'] ?? null;
    log_message('debug', 'get_classes_by_school - user_school_id: ' . ($user_school_id ?? 'null'));

    if (empty($school_id) || $school_id != $user_school_id) {
        $csrf = [
            'csrfName' => $this->security->get_csrf_token_name(),
            'csrfHash' => $this->security->get_csrf_hash(),
        ];
        log_message('error', 'get_classes_by_school - Invalid or unauthorized school_id: ' . ($school_id ?? 'null'));
        echo json_encode(['status' => 'error', 'message' => 'Invalid or unauthorized school ID', 'csrf' => $csrf]);
        return;
    }

    // Récupérer la session active
    $session_id = active_session();
    log_message('debug', 'get_classes_by_school - session_id: ' . ($session_id ?? 'null'));

    // Vérifier les classes autorisées pour l'étudiant via enrols
    $this->db->select('classes.id, classes.name');
    $this->db->from('classes');
    $this->db->join('enrols', 'enrols.class_id = classes.id', 'inner');
    $this->db->join('students', 'students.id = enrols.student_id', 'inner');
    $this->db->where('enrols.student_id', $student_id);
    $this->db->where('enrols.school_id', $school_id);
    $this->db->where('enrols.session', $session_id);
    $this->db->where('students.user_id', $user_id);
    $classes = $this->db->get()->result_array();
    log_message('debug', 'get_classes_by_school - SQL Query: ' . $this->db->last_query());
    log_message('debug', 'get_classes_by_school - Classes found: ' . json_encode($classes));

    $csrf = [
        'csrfName' => $this->security->get_csrf_token_name(),
        'csrfHash' => $this->security->get_csrf_hash(),
    ];

    echo json_encode([
        'status' => 'success',
        'classes' => $classes,
        'message' => empty($classes) ? 'Aucune classe autorisée pour cet étudiant dans cette session' : '',
        'csrf' => $csrf
    ]);
}

// Filtrer les examens en fonction des sélections
public function filter_exams() {
    try {
        log_message('debug', 'Début de filter_exams');
        log_message('debug', 'Données POST reçues : ' . json_encode($this->input->post()));
        log_message('debug', 'Utilisateur connecté : ' . $this->session->userdata('user_id'));

        $school_id = $this->input->post('school_id');
        $class_id = $this->input->post('class_id');
        $section_id = $this->input->post('section_id');
        $date_filter = $this->input->post('date_filter');
        $user_id = $this->session->userdata('user_id');

        if (!$school_id || !$class_id || !$section_id || !$user_id) {
            http_response_code(400);
            echo json_encode([
                'error' => 'Données manquantes',
                'csrf_hash' => $this->security->get_csrf_hash()
            ]);
            return;
        }

        $session_id = active_session();

        if (!$session_id) {
            http_response_code(400);
            echo json_encode([
                'error' => 'Aucune session active',
                'csrf_hash' => $this->security->get_csrf_hash()
            ]);
            return;
        }

        // Vérification des permissions
        $this->db->select('students.id');
        $this->db->from('students');
        $this->db->join('enrols', 'enrols.student_id = students.id', 'left');
        $this->db->where('students.user_id', $user_id);
        $this->db->where('enrols.school_id', $school_id);
        $this->db->where('enrols.class_id', $class_id);
        $this->db->where('enrols.section_id', $section_id);
        $this->db->where('enrols.session', $session_id);

        $student = $this->db->get()->row_array();

        if (!$student) {
            http_response_code(403);
            echo json_encode([
                'error' => 'Non autorisé',
                'csrf_hash' => $this->security->get_csrf_hash()
            ]);
            return;
        }

        // Construire la requête pour récupérer les examens
        $this->db->reset_query();
        $this->db->select('exams.*, classes.name as class_name, sections.name as section_name, schools.name as school_name');
        $this->db->from('exams');
        $this->db->join('classes', 'exams.class_id = classes.id', 'left');
        $this->db->join('sections', 'exams.section_id = sections.id', 'left');
        $this->db->join('schools', 'exams.school_id = schools.id', 'left');
        $this->db->where('exams.school_id', $school_id);
        $this->db->where('exams.class_id', $class_id);
        $this->db->where('exams.section_id', $section_id);
        $this->db->where('exams.session', $session_id);

        // Gérer le filtre de date
        if (!empty($date_filter)) {
            // Parser le filtre de date
            $dates = explode(' - ', $date_filter);
            if (count($dates) == 1) {
                // Date unique
                $start_date = DateTime::createFromFormat('d-m-Y', trim($dates[0]));
                if ($start_date) {
                    $start_timestamp = $start_date->setTime(0, 0, 0)->getTimestamp();
                    $end_timestamp = $start_date->setTime(23, 59, 59)->getTimestamp();
                    $this->db->where('exams.starting_date >=', $start_timestamp);
                    $this->db->where('exams.starting_date <=', $end_timestamp);
                }
            } elseif (count($dates) == 2) {
                // Plage de dates
                $start_date = DateTime::createFromFormat('d-m-Y', trim($dates[0]));
                $end_date = DateTime::createFromFormat('d-m-Y', trim($dates[1]));
                if ($start_date && $end_date) {
                    $start_timestamp = $start_date->setTime(0, 0, 0)->getTimestamp();
                    $end_timestamp = $end_date->setTime(23, 59, 59)->getTimestamp();
                    $this->db->where('exams.starting_date >=', $start_timestamp);
                    $this->db->where('exams.starting_date <=', $end_timestamp);
                }
            }
        }

        $exams = $this->db->get()->result_array();
        $exam_calendar = [];
        $current_time = time(); // Current timestamp

        foreach ($exams as $exam) {
            $exam_calendar[] = [
                'title' => $exam['name'],
                'start' => date('Y-m-d H:i:s', $exam['starting_date'])
            ];
        }

        // Génération du tableau HTML
        $table_html = '';
        foreach ($exams as $exam) {
            $exam_start_time = $exam['starting_date'];
            $table_html .= '<tr>';
            $table_html .= '<td>' . htmlspecialchars($exam['name']) . '</td>';
            $table_html .= '<td>' . date('D, d-M-Y H:i', $exam_start_time) . '</td>';
            $table_html .= '<td>' . (!empty($exam['class_name']) ? htmlspecialchars($exam['class_name']) : get_phrase('no_class')) . '</td>';
            $table_html .= '<td>' . (!empty($exam['section_name']) ? htmlspecialchars($exam['section_name']) : get_phrase('no_section')) . '</td>';

            // Vérifier si l'examen a déjà été soumis
            $this->db->select('id');
            $this->db->from('exam_responses');
            $this->db->where('exam_id', $exam['id']);
            $this->db->where('user_id', $user_id);
            $has_submitted = $this->db->get()->row_array();

            if ($has_submitted) {
                // Bouton pour afficher les résultats dans un popup
                $table_html .= '<td><button class="btn btn-sm btn-success view-results-btn" data-exam-id="' . $exam['id'] . '" data-student-id="' . $student['id'] . '">' . get_phrase('view_results') . '</button></td>';
            } elseif ($exam_start_time > $current_time) {
                // Afficher le compteur pour les examens futurs
                $table_html .= '<td data-exam-start="' . $exam_start_time . '" data-exam-id="' . $exam['id'] . '" class="exam-countdown">';
                $table_html .= get_phrase('exam_not_yet_available') . '<br>';
                $table_html .= '<span class="countdown-text" style="background-color: #3A87AD; color:white; border-radius: 5px; padding:3px;"></span></td>';
            } else {
                // Afficher le bouton d'accès
                $table_html .= '<td><a href="' . site_url('student/online_exam/' . $exam['id']) . '" target="_blank" class="btn btn-sm btn-primary access-exam-btn">' . get_phrase('access') . '</a></td>';
            }

            $table_html .= '</tr>';
        }
        log_message('debug', 'Tableau HTML généré : ' . $table_html);

        echo json_encode([
            'exam_calendar' => $exam_calendar,
            'table_html' => $table_html,
            'csrf_hash' => $this->security->get_csrf_hash()
        ]);
    } catch (Exception $e) {
        log_message('error', 'Erreur dans filter_exams : ' . $e->getMessage());
        http_response_code(500);
        echo json_encode([
            'error' => 'Erreur serveur interne : ' . $e->getMessage(),
            'csrf_hash' => $this->security->get_csrf_hash()
        ]);
    }
}

public function get_sections() {
	try {
		$class_id = $this->input->post('classe_id');
		$user_id = $this->session->userdata('user_id');

		if (!$class_id || !$user_id) {
			log_message('error', 'class_id ou user_id manquant dans get_sections');
			echo json_encode(['sections' => [], 'csrf_hash' => $this->security->get_csrf_hash()]);
			return;
		}

		$session_id = active_session();
		log_message('debug', 'Session active : ' . $session_id);

		$this->db->select('sections.*');
		$this->db->from('sections');
		$this->db->join('enrols', 'enrols.section_id = sections.id', 'left');
		$this->db->join('students', 'students.id = enrols.student_id', 'left');
		$this->db->where('sections.class_id', $class_id);
		$this->db->where('students.user_id', $user_id);
		$this->db->where('enrols.session', $session_id);

		$sections = $this->db->get()->result_array();

		log_message('debug', 'Requête SQL exécutée : ' . $this->db->last_query());
		log_message('debug', 'Sections trouvées : ' . json_encode($sections));

		echo json_encode([
			'sections' => $sections,
			'csrf_hash' => $this->security->get_csrf_hash()
		]);
	} catch (Exception $e) {
		log_message('error', 'Erreur dans get_sections : ' . $e->getMessage());
		http_response_code(500);
		echo json_encode([
			'error' => 'Erreur serveur interne',
			'csrf_hash' => $this->security->get_csrf_hash()
		]);
	}
}

// Dans Student.php
public function online_exam($exam_id = "") {
    try {
        if (empty($exam_id)) {
            log_message('error', 'ID de l\'examen manquant dans online_exam');
            redirect(site_url('student/exam'), 'refresh');
        }

        // Ajouter des en-têtes anti-cache
        $this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        $this->output->set_header("Cache-Control: post-check=0, pre-check=0", false);
        $this->output->set_header("Pragma: no-cache");
        $this->output->set_header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");

        $user_id = $this->session->userdata('user_id');
        $session_id = active_session();

        // Vérifier si l'étudiant a accès à cet examen
        $this->db->select('exams.*, classes.name as class_name, sections.name as section_name, schools.name as school_name');
        $this->db->from('exams');
        $this->db->join('classes', 'exams.class_id = classes.id', 'left');
        $this->db->join('sections', 'exams.section_id = sections.id', 'left');
        $this->db->join('schools', 'exams.school_id = schools.id', 'left');
        $this->db->join('enrols', 'enrols.class_id = exams.class_id AND enrols.section_id = exams.section_id', 'left');
        $this->db->join('students', 'students.id = enrols.student_id', 'left');
        $this->db->where('exams.id', $exam_id);
        $this->db->where('students.user_id', $user_id);
        $this->db->where('enrols.session', $session_id);

        $exam = $this->db->get()->row_array();

        if (!$exam) {
            log_message('error', 'Examen non trouvé ou accès non autorisé pour exam_id: ' . $exam_id);
            $this->session->set_flashdata('error_message', get_phrase('exam_not_found_or_unauthorized'));
            redirect(site_url('student/exam'), 'refresh');
        }

        // Vérifier si l'étudiant a déjà soumis l'examen
        $this->db->select('id');
        $this->db->from('exam_responses');
        $this->db->where('exam_id', $exam_id);
        $this->db->where('user_id', $user_id);
        $existing_submission = $this->db->get()->row_array();

        if ($existing_submission) {
            // L'étudiant a déjà passé l'examen, rediriger vers la liste des examens
            $this->session->set_flashdata('success_message', get_phrase('exam_already_submitted'));
            redirect(site_url('student/exam'), 'refresh');
        }

        // Vérifier si l'examen a commencé
        $current_time = time();
        if ($exam['starting_date'] > $current_time) {
            log_message('error', 'L\'examen n\'a pas encore commencé pour exam_id: ' . $exam_id);
            $this->session->set_flashdata('error_message', get_phrase('exam_not_yet_available'));
            redirect(site_url('student/exam'), 'refresh');
        }

        // Récupérer les questions de l'examen
        $questions = $this->get_exam_questions($exam_id);

        // Préparer les données pour la vue
        $page_data['exam_id'] = $exam_id;
        $page_data['exam_details'] = $exam;
        $page_data['questions'] = $questions;
        $page_data['page_title'] = $exam['name'];

        // Charger la vue via online_exams/index.php
        $this->load->view('online_exams/index', $page_data);
    } catch (Exception $e) {
        log_message('error', 'Erreur dans online_exam : ' . $e->getMessage());
        $this->session->set_flashdata('error_message', get_phrase('server_error'));
        redirect(site_url('student/exam'), 'refresh');
    }
}

public function get_exam_questions($exam_id) {
    $this->db->select('exam_questions.*');
    $this->db->from('exam_questions');
    $this->db->where('exam_id', $exam_id);
    return $this->db->get()->result_array();
}

public function submit_exam() {
    try {
        // Vérifier si la requête est POST
        if ($this->input->server('REQUEST_METHOD') !== 'POST') {
            log_message('error', 'Méthode non autorisée dans submit_exam');
            http_response_code(405);
            echo json_encode(['error' => 'Méthode non autorisée']);
            return;
        }

        $user_id = $this->session->userdata('user_id');
        $exam_id = $this->input->post('exam_id');
        $session_id = active_session();

        if (!$user_id || !$exam_id) {
            log_message('error', 'user_id ou exam_id manquant dans submit_exam');
            http_response_code(400);
            echo json_encode(['error' => 'Données manquantes']);
            return;
        }

        // Vérifier si l'étudiant a le droit de soumettre cet examen
        $this->db->select('exams.*, classes.id as class_id, sections.id as section_id, schools.id as school_id');
        $this->db->from('exams');
        $this->db->join('classes', 'exams.class_id = classes.id', 'left');
        $this->db->join('sections', 'exams.section_id = sections.id', 'left');
        $this->db->join('schools', 'exams.school_id = schools.id', 'left');
        $this->db->join('enrols', 'enrols.class_id = exams.class_id AND enrols.section_id = exams.section_id', 'left');
        $this->db->join('students', 'students.id = enrols.student_id', 'left');
        $this->db->where('exams.id', $exam_id);
        $this->db->where('students.user_id', $user_id);
        $this->db->where('enrols.session', $session_id);

        $exam = $this->db->get()->row_array();

        if (!$exam) {
            log_message('error', 'Examen non trouvé ou accès non autorisé pour exam_id: ' . $exam_id);
            http_response_code(403);
            echo json_encode(['error' => 'Non autorisé']);
            return;
        }

        // Récupérer l'étudiant
        $student_data = $this->db->get_where('students', ['user_id' => $user_id, 'school_id' => $exam['school_id']])->row_array();
        if (!$student_data) {
            log_message('error', 'Étudiant non trouvé pour user_id: ' . $user_id);
            http_response_code(400);
            echo json_encode(['error' => 'Étudiant non trouvé']);
            return;
        }

        // Récupérer les questions de l'examen
        $questions = $this->get_exam_questions($exam_id);
        $total_questions = count($questions);
        $total_correct_answers = 0;
        $submitted_answers = [];

        if ($total_questions == 0) {
            log_message('error', 'Aucune question trouvée pour exam_id: ' . $exam_id);
            http_response_code(400);
            echo json_encode(['error' => 'Aucune question trouvée']);
            return;
        }

        // Étape 1 : Supprimer les anciennes réponses pour cet examen et cet utilisateur
        $this->db->where('exam_id', $exam_id);
        $this->db->where('user_id', $user_id);
        $this->db->delete('exam_responses');
        log_message('debug', 'Anciennes réponses supprimées pour exam_id: ' . $exam_id . ' et user_id: ' . $user_id);

        // Étape 2 : Traiter les réponses soumises
        foreach ($questions as $question) {
            $question_id = $question['id'];

            // Récupérer les options disponibles pour la question
            $options = json_decode($question['options'], true);
            if (!is_array($options) || empty($options)) {
                log_message('error', 'Options mal formatées pour la question ID ' . $question_id . ': ' . $question['options']);
                $options = [];
            }

            // Extraire les réponses correctes
            $correct_answers_data = json_decode($question['correct_answers'], true);
            if (!is_array($correct_answers_data) || empty($correct_answers_data)) {
                $correct_answers_raw = is_array($correct_answers_data) ? $correct_answers_data[0] : $question['correct_answers'];
                if (is_numeric($correct_answers_raw) && isset($options[$correct_answers_raw - 1])) {
                    $correct_answers_index = (int)$correct_answers_raw - 1;
                    $correct_answers = $options[$correct_answers_index];
                    log_message('debug', 'Index base-1 détecté pour question ID ' . $question_id . ': ' . $correct_answers_raw . ' -> ' . $correct_answers);
                } else {
                    log_message('error', 'Réponses correctes mal formatées pour la question ID ' . $question_id . ': ' . $question['correct_answers']);
                    $correct_answers = '';
                }
            } else {
                $correct_answers = trim((string)$correct_answers_data[0]);
                if (is_numeric($correct_answers) && isset($options[$correct_answers - 1])) {
                    $correct_answers_index = (int)$correct_answers - 1;
                    $correct_answers = $options[$correct_answers_index];
                    log_message('debug', 'Index base-1 détecté pour question ID ' . $question_id . ': ' . $correct_answers_data[0] . ' -> ' . $correct_answers);
                }
            }

            // Vérifier si la réponse correcte est dans les options
            if (!in_array($correct_answers, $options) && $correct_answers !== '') {
                log_message('error', 'Réponse correcte "' . $correct_answers . '" pour la question ID ' . $question_id . ' ne correspond à aucune option: ' . json_encode($options));
                $correct_answers = '';
            }

            $submitted_answers_value = $this->input->post('question_' . $question_id);
            $submitted_answers_value = trim((string)$submitted_answers_value);

            $submitted_answer_status = ($submitted_answers_value == $correct_answers) ? 1 : 0;
            log_message('debug', 'Comparaison pour question ID ' . $question_id . ': submitted="' . $submitted_answers_value . '", correct="' . $correct_answers . '", status=' . $submitted_answer_status);

            if ($submitted_answer_status) {
                $total_correct_answers++;
            }

            // Stocker les détails de la réponse dans exam_responses
            $data = [
                'user_id' => $user_id,
                'exam_id' => $exam_id,
                'exam_question_id' => $question_id,
                'submitted_answers' => $submitted_answers_value ?: 'Aucune réponse',
                'correct_answers' => $correct_answers,
                'submitted_answer_status' => $submitted_answer_status,
                'date_submitted' => date('Y-m-d H:i:s')
            ];
            $this->db->insert('exam_responses', $data);

            $submitted_answers[] = [
                'question_id' => $question_id,
                'question_title' => $question['title'],
                'submitted_answer' => $submitted_answers_value ?: 'Aucune réponse',
                'correct_answer' => $correct_answers,
                'status' => $submitted_answer_status,
                'options' => $options
            ];
        }

        // Calculer la note (sur 100, arrondie car mark_obtained est un int)
        $mark_obtained = ($total_questions > 0) ? round(($total_correct_answers / $total_questions) * 100) : 0;

        // Vérifier si une entrée existe déjà dans la table marks
        $this->db->where([
            'student_id' => $student_data['id'],
            'exam_id' => $exam_id,
            'class_id' => $exam['class_id'],
            'section_id' => $exam['section_id'],
            'school_id' => $exam['school_id'],
            'session' => $session_id
        ]);
        $existing_mark = $this->db->get('marks')->row_array();

        if ($existing_mark) {
            // Mettre à jour la note existante
            $this->db->where('id', $existing_mark['id']);
            $this->db->update('marks', [
                'mark_obtained' => $mark_obtained,
            ]);
        } else {
            // Insérer une nouvelle entrée
            $this->db->insert('marks', [
                'student_id' => $student_data['id'],
                'subject_id' => NULL, // Pas de matière pour les examens en ligne
                'exam_id' => $exam_id,
                'class_id' => $exam['class_id'],
                'section_id' => $exam['section_id'],
                'school_id' => $exam['school_id'],
                'session' => $session_id,
                'mark_obtained' => $mark_obtained,
                'comment' => ''
            ]);
        }

        // Réponse JSON pour le front-end
        $response = [
            'message' => get_phrase('exam_submitted_successfully'),
            'total_questions' => $total_questions,
            'total_correct_answers' => $total_correct_answers,
            'mark_obtained' => $mark_obtained,
            'submitted_answers' => $submitted_answers,
            'csrf_hash' => $this->security->get_csrf_hash()
        ];

        log_message('debug', 'Examen soumis avec succès pour exam_id: ' . $exam_id);
        echo json_encode($response);
    } catch (Exception $e) {
        log_message('error', 'Erreur dans submit_exam : ' . $e->getMessage());
        http_response_code(500);
        echo json_encode([
            'error' => 'Erreur serveur interne : ' . $e->getMessage(),
            'csrf_hash' => $this->security->get_csrf_hash()
        ]);
    }
}

public function exam_results($exam_id = "", $student_id = "") {
    try {

        if (!$this->session->userdata('student_login') || $this->session->userdata('user_type') != 'student') {
            redirect(site_url('login'), 'refresh');
        }

        $user_id = $this->session->userdata('user_id');
        $session_id = active_session();

        $this->db->select('exams.*, classes.name as class_name, sections.name as section_name, schools.name as school_name');
        $this->db->from('exams');
        $this->db->join('classes', 'exams.class_id = classes.id', 'left');
        $this->db->join('sections', 'exams.section_id = sections.id', 'left');
        $this->db->join('schools', 'exams.school_id = schools.id', 'left');
        $this->db->join('enrols', 'enrols.class_id = exams.class_id AND enrols.section_id = exams.section_id', 'left');
        $this->db->join('students', 'students.id = enrols.student_id', 'left');
        $this->db->where('exams.id', $exam_id);
        $this->db->where('students.user_id', $user_id);
        $this->db->where('enrols.session', $session_id);

        $exam = $this->db->get()->row_array();

        if (!$exam) {
            redirect(site_url('student/exam'), 'refresh');
        }

        // Récupérer l'ID de l'étudiant à partir de user_id
        $student_data = $this->db->get_where('students', ['user_id' => $user_id, 'school_id' => $exam['school_id']])->row_array();
        if (!$student_data || $student_data['id'] != $student_id) {
            redirect(site_url('student/exam'), 'refresh');
        }

        // Récupérer les réponses soumises
        $this->db->select('er.*, eq.title as question_title');
        $this->db->from('exam_responses er');
        $this->db->join('exam_questions eq', 'er.exam_question_id = eq.id', 'left');
        $this->db->where('er.exam_id', $exam_id);
        $this->db->where('er.user_id', $user_id);
        $submitted_answers = $this->db->get()->result_array();

        // Préparer les données pour la vue
        $page_data['exam_details'] = $exam;
        $page_data['submitted_answers'] = $submitted_answers;
        $page_data['page_title'] = $exam['name'] . ' - ' . get_phrase('results');

        // Charger la vue partielle pour le modal
        $this->load->view('backend/student/mark/exam_results_modal', $page_data);
    } catch (Exception $e) {
        redirect(site_url('student/exam'), 'refresh');
    }
}

public function get_exam_results_popup($exam_id = "", $student_id = "") {
    try {
        if (!$this->session->userdata('student_login') || $this->session->userdata('user_type') != 'student') {
            http_response_code(403);
            echo json_encode(['error' => 'Non autorisé']);
            return;
        }

        $user_id = $this->session->userdata('user_id');
        $session_id = active_session();

        // Vérifier si l'étudiant a accès à cet examen
        $this->db->select('exams.*, classes.name as class_name, sections.name as section_name, schools.name as school_name');
        $this->db->from('exams');
        $this->db->join('classes', 'exams.class_id = classes.id', 'left');
        $this->db->join('sections', 'exams.section_id = sections.id', 'left');
        $this->db->join('schools', 'exams.school_id = schools.id', 'left');
        $this->db->join('enrols', 'enrols.class_id = exams.class_id AND enrols.section_id = exams.section_id', 'left');
        $this->db->join('students', 'students.id = enrols.student_id', 'left');
        $this->db->where('exams.id', $exam_id);
        $this->db->where('students.user_id', $user_id);
        $this->db->where('enrols.session', $session_id);

        $exam = $this->db->get()->row_array();

        if (!$exam) {
            http_response_code(403);
            echo json_encode(['error' => 'Examen non trouvé ou accès non autorisé']);
            return;
        }

        // Vérifier si l'étudiant correspond
        $student_data = $this->db->get_where('students', ['user_id' => $user_id, 'school_id' => $exam['school_id']])->row_array();
        if (!$student_data || $student_data['id'] != $student_id) {
            http_response_code(403);
            echo json_encode(['error' => 'Étudiant non autorisé']);
            return;
        }

        // Récupérer les réponses soumises
        $this->db->select('er.*, eq.title as question_title');
        $this->db->from('exam_responses er');
        $this->db->join('exam_questions eq', 'er.exam_question_id = eq.id', 'left');
        $this->db->where('er.exam_id', $exam_id);
        $this->db->where('er.user_id', $user_id);
        $submitted_answers = $this->db->get()->result_array();

        // Préparer les données pour la vue
        $page_data['exam_details'] = $exam;
        $page_data['submitted_answers'] = $submitted_answers;
        $page_data['page_title'] = $exam['name'] . ' - ' . get_phrase('results');

        // Rendre la vue partielle et retourner le HTML
        $html_content = $this->load->view('backend/student/mark/exam_results_modal', $page_data, TRUE);

        echo json_encode([
            'html' => $html_content,
            'csrf_hash' => $this->security->get_csrf_hash()
        ]);
    } catch (Exception $e) {
        log_message('error', 'Erreur dans get_exam_results_popup : ' . $e->getMessage());
        http_response_code(500);
        echo json_encode([
            'error' => 'Erreur serveur interne : ' . $e->getMessage(),
            'csrf_hash' => $this->security->get_csrf_hash()
        ]);
    }
}

public function load_initial_exams() {
    try {
        log_message('debug', 'Début de load_initial_exams');
        log_message('debug', 'Données POST reçues : ' . json_encode($this->input->post()));
        log_message('debug', 'Utilisateur connecté : ' . $this->session->userdata('user_id'));

        $class_id = $this->input->post('class_id');
        $section_id = $this->input->post('section_id');
        $date_filter = $this->input->post('date_filter');
        $user_id = $this->session->userdata('user_id');

        if (!$class_id || !$section_id || !$user_id) {
            http_response_code(400);
            echo json_encode([
                'error' => 'Données manquantes',
                'csrf_hash' => $this->security->get_csrf_hash()
            ]);
            return;
        }

        $session_id = active_session();
        if (!$session_id) {
            http_response_code(400);
            echo json_encode([
                'error' => 'Aucune session active',
                'csrf_hash' => $this->security->get_csrf_hash()
            ]);
            return;
        }

        // Récupérer les écoles auxquelles l'étudiant est inscrit
        $this->db->select('schools.id');
        $this->db->from('schools');
        $this->db->join('enrols', 'enrols.school_id = schools.id');
        $this->db->join('students', 'students.id = enrols.student_id');
        $this->db->where('students.user_id', $user_id);
        $this->db->where('enrols.session', $session_id);
        $school_ids = $this->db->get()->result_array();
        $school_ids = array_column($school_ids, 'id');

        if (empty($school_ids)) {
            http_response_code(403);
            echo json_encode([
                'error' => 'Non autorisé',
                'csrf_hash' => $this->security->get_csrf_hash()
            ]);
            return;
        }

        // Vérification des permissions
        $this->db->select('students.id');
        $this->db->from('students');
        $this->db->join('enrols', 'enrols.student_id = students.id', 'left');
        $this->db->where('students.user_id', $user_id);
        $this->db->where_in('enrols.school_id', $school_ids);
        $this->db->where('enrols.class_id', $class_id);
        $this->db->where('enrols.section_id', $section_id);
        $this->db->where('enrols.session', $session_id);

        $student = $this->db->get()->row_array();

        if (!$student) {
            http_response_code(403);
            echo json_encode([
                'error' => 'Non autorisé',
                'csrf_hash' => $this->security->get_csrf_hash()
            ]);
            return;
        }

        // Début de la journée actuelle (00:00:00)
        $today_start = strtotime('today midnight');

        // Construire la requête pour récupérer les examens
        $this->db->reset_query();
        $this->db->select('exams.*, classes.name as class_name, sections.name as section_name, schools.name as school_name');
        $this->db->from('exams');
        $this->db->join('classes', 'exams.class_id = classes.id', 'left');
        $this->db->join('sections', 'exams.section_id = sections.id', 'left');
        $this->db->join('schools', 'exams.school_id = schools.id', 'left');
        $this->db->where_in('exams.school_id', $school_ids);
        $this->db->where('exams.class_id', $class_id);
        $this->db->where('exams.section_id', $section_id);
        $this->db->where('exams.session', $session_id);
        $this->db->where('exams.starting_date >=', $today_start);

        // Gérer le filtre de date
        if (!empty($date_filter)) {
            // Parser le filtre de date
            $dates = explode(' - ', $date_filter);
            if (count($dates) == 1) {
                // Date unique
                $start_date = DateTime::createFromFormat('d-m-Y', trim($dates[0]));
                if ($start_date) {
                    $start_timestamp = $start_date->setTime(0, 0, 0)->getTimestamp();
                    $end_timestamp = $start_date->setTime(23, 59, 59)->getTimestamp();
                    $this->db->where('exams.starting_date >=', $start_timestamp);
                    $this->db->where('exams.starting_date <=', $end_timestamp);
                }
            } elseif (count($dates) == 2) {
                // Plage de dates
                $start_date = DateTime::createFromFormat('d-m-Y', trim($dates[0]));
                $end_date = DateTime::createFromFormat('d-m-Y', trim($dates[1]));
                if ($start_date && $end_date) {
                    $start_timestamp = $start_date->setTime(0, 0, 0)->getTimestamp();
                    $end_timestamp = $end_date->setTime(23, 59, 59)->getTimestamp();
                    $this->db->where('exams.starting_date >=', $start_timestamp);
                    $this->db->where('exams.starting_date <=', $end_timestamp);
                }
            }
        }

        $exams = $this->db->get()->result_array();
        $exam_calendar = [];
        $current_time = time();

        foreach ($exams as $exam) {
            $exam_calendar[] = [
                'title' => $exam['name'],
                'start' => date('Y-m-d H:i:s', $exam['starting_date'])
            ];
        }

        // Génération du tableau HTML
        $table_html = '';
        foreach ($exams as $exam) {
            $exam_start_time = $exam['starting_date'];
            $table_html .= '<tr>';
            $table_html .= '<td>' . htmlspecialchars($exam['name']) . '</td>';
            $table_html .= '<td>' . date('D, d-M-Y H:i', $exam_start_time) . '</td>';
            $table_html .= '<td>' . (!empty($exam['class_name']) ? htmlspecialchars($exam['class_name']) : get_phrase('no_class')) . '</td>';
            $table_html .= '<td>' . (!empty($exam['section_name']) ? htmlspecialchars($exam['section_name']) : get_phrase('no_section')) . '</td>';

            $this->db->select('id');
            $this->db->from('exam_responses');
            $this->db->where('exam_id', $exam['id']);
            $this->db->where('user_id', $user_id);
            $has_submitted = $this->db->get()->row_array();

            if ($has_submitted) {
                $table_html .= '<td><button class="btn btn-sm btn-success view-results-btn" data-exam-id="' . $exam['id'] . '" data-student-id="' . $student['id'] . '">' . get_phrase('view_results') . '</button></td>';
            } elseif ($exam_start_time > $current_time) {
                $table_html .= '<td data-exam-start="' . $exam_start_time . '" data-exam-id="' . $exam['id'] . '" class="exam-countdown">';
                $table_html .= get_phrase('exam_not_yet_available') . '<br>';
                $table_html .= '<span class="countdown-text" style="background-color: #3A87AD; color:white; border-radius: 5px; padding:3px;"></span></td>';
            } else {
                $table_html .= '<td><a href="' . site_url('student/online_exam/' . $exam['id']) . '" target="_blank" class="btn btn-sm btn-primary access-exam-btn">' . get_phrase('access') . '</a></td>';
            }

            $table_html .= '</tr>';
        }
        log_message('debug', 'Tableau HTML généré : ' . $table_html);

        echo json_encode([
            'exam_calendar' => $exam_calendar,
            'table_html' => $table_html,
            'csrf_hash' => $this->security->get_csrf_hash()
        ]);
    } catch (Exception $e) {
        log_message('error', 'Erreur dans load_initial_exams : ' . $e->getMessage());
        http_response_code(500);
        echo json_encode([
            'error' => 'Erreur serveur interne : ' . $e->getMessage(),
            'csrf_hash' => $this->security->get_csrf_hash()
        ]);
    }
}

public function get_classes_by_student() {
    $user_id = $this->session->userdata('user_id');
    $session_id = active_session();

    $this->db->select('classes.id, classes.name');
    $this->db->from('classes');
    $this->db->join('enrols', 'enrols.class_id = classes.id');
    $this->db->join('students', 'students.id = enrols.student_id');
    $this->db->where('students.user_id', $user_id);
    $this->db->where('enrols.session', $session_id);
    $this->db->group_by('classes.id');

    $classes = $this->db->get()->result_array();
    echo json_encode([
        'classes' => $classes,
        'csrf_hash' => $this->security->get_csrf_hash()
    ]);
}

public function calendar($param1 = '', $param2 = '', $param3 = '', $param4 = '') {
    if ($this->session->userdata('student_login') != 1) {
        $csrf = [
            'csrfName' => $this->security->get_csrf_token_name(),
            'csrfHash' => $this->security->get_csrf_hash(),
        ];
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized', 'csrf' => $csrf]);
        return;
    }

    if ($param1 == 'get_events') {
        $this->get_events();
    }

    if ($param1 == 'get_user_school') {
        $this->get_user_school();
    }

    if ($param1 == 'get_classes_by_school') {
        $this->get_classes_by_school();
    }

    if ($param1 == 'get_classes_with_events') {
        $this->get_classes_with_events();
    }

    if ($param1 == 'start_meeting') {
        $this->start_meeting();
    }

    if ($param1 == 'filter') {
        $page_data['class_id'] = $param2;
        $page_data['start_date'] = $param3;
        $page_data['end_date'] = $param4;
        $this->load->view('backend/student/calendar/list', $page_data);
    }

    if (empty($param1)) {
        $page_data['folder_name'] = 'calendar';
        $page_data['page_title'] = 'calendar';
        $this->load->view('backend/index', $page_data);
    }
}

public function get_user_school() {
    if ($this->session->userdata('student_login') != 1) {
        echo json_encode(['status' => 'error', 'message' => 'Session expired, please login again']);
        return;
    }

    $user_id = $this->session->userdata('user_id');
    $user = $this->db->get_where('users', ['id' => $user_id])->row_array();

    if (!$user || empty($user['school_id'])) {
        echo json_encode(['status' => 'error', 'message' => 'No school associated with this user']);
        return;
    }

    $school = $this->db->get_where('schools', ['id' => $user['school_id'], 'Etat' => 1, 'status' => 1])->row_array();
    if (!$school) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid school']);
        return;
    }

    $csrf = array(
        'csrfName' => $this->security->get_csrf_token_name(),
        'csrfHash' => $this->security->get_csrf_hash(),
    );

    echo json_encode([
        'status' => 'success',
        'data' => [
            'id' => $school['id'],
            'name' => $school['name']
        ],
        'csrf' => $csrf
    ]);
}

      public function get_events() {
    // Vérifier l'authentification de l'utilisateur
    if ($this->session->userdata('student_login') != 1) {
        $csrf = [
            'csrfName' => $this->security->get_csrf_token_name(),
            'csrfHash' => $this->security->get_csrf_hash(),
        ];
        log_message('error', 'get_events - Unauthorized access attempt. Session data: ' . json_encode($this->session->userdata()));
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized', 'csrf' => $csrf]);
        return;
    }

    // Récupérer l'ID de l'utilisateur et mapper à student_id
    $user_id = $this->session->userdata('user_id');
    log_message('debug', 'get_events - user_id: ' . ($user_id ?? 'null'));

    // Mapper user_id à student_id dans la table students
    $this->db->select('id');
    $this->db->from('students');
    $this->db->where('user_id', $user_id);
    $student = $this->db->get()->row_array();
    $student_id = $student['id'] ?? null;
    log_message('debug', 'get_events - student_id: ' . ($student_id ?? 'null'));

    if (!$student_id) {
        $csrf = [
            'csrfName' => $this->security->get_csrf_token_name(),
            'csrfHash' => $this->security->get_csrf_hash(),
        ];
        log_message('error', 'get_events - No student_id found for user_id: ' . $user_id);
        echo json_encode(['status' => 'error', 'message' => 'No student associated with this user', 'csrf' => $csrf]);
        return;
    }

    // Récupérer l'ID de l'école de l'utilisateur connecté
    $user_details = $this->user_model->get_user_details($user_id);
    $school_id = $user_details['school_id'] ?? null;

    if (!$school_id) {
        $csrf = [
            'csrfName' => $this->security->get_csrf_token_name(),
            'csrfHash' => $this->security->get_csrf_hash(),
        ];
        log_message('error', 'get_events - No school ID found for user_id: ' . $user_id);
        echo json_encode(['status' => 'error', 'message' => 'No school associated with this user', 'csrf' => $csrf]);
        return;
    }

    // Récupérer les classes autorisées pour l'étudiant via enrols
    $this->db->select('class_id');
    $this->db->from('enrols');
    $this->db->where('student_id', $student_id);
    $this->db->where('school_id', $school_id);
    $this->db->where('session', active_session());
    $permitted_classes = $this->db->get()->result_array();
    $permitted_class_ids = array_column($permitted_classes, 'class_id');
    log_message('debug', 'get_events - permitted_class_ids: ' . json_encode($permitted_class_ids));

    if (empty($permitted_class_ids)) {
        $csrf = [
            'csrfName' => $this->security->get_csrf_token_name(),
            'csrfHash' => $this->security->get_csrf_hash(),
        ];
        echo json_encode(['status' => 'success', 'data' => [], 'message' => 'No classes assigned to this student', 'csrf' => $csrf]);
        return;
    }

    $start_date = $this->input->get('start_date', true);
    $end_date = $this->input->get('end_date', true);
    $event_id = $this->input->get('id', true); // Récupérer l'ID de l'événement
    $class_id = $this->input->get('class_id', true); // Récupérer l'ID de la classe

    // Validation des dates
    if ($start_date && $end_date) {
        $start_date_obj = DateTime::createFromFormat('Y-m-d', $start_date);
        $end_date_obj = DateTime::createFromFormat('Y-m-d', $end_date);
        if ($start_date_obj === false || $end_date_obj === false || $start_date_obj->format('Y-m-d') !== $start_date || $end_date_obj->format('Y-m-d') !== $end_date) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Format de date invalide (attendu : YYYY-MM-DD)',
                'csrf' => [
                    'csrfName' => $this->security->get_csrf_token_name(),
                    'csrfHash' => $this->security->get_csrf_hash()
                ]
            ]);
            return;
        }
        $start_date = $start_date_obj->format('Y-m-d');
        $end_date = $end_date_obj->format('Y-m-d');
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Les paramètres start_date et end_date sont requis',
            'csrf' => [
                'csrfName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash()
            ]
        ]);
        return;
    }

    // Construction de la requête
    $this->db->select('event_calendars.*, schools.name as school_name, classes.name as class_name');
    $this->db->from('event_calendars');
    $this->db->join('schools', 'event_calendars.school_id = schools.id', 'left');
    $this->db->join('classes', 'event_calendars.class_id = classes.id', 'left');
    // Filtrer par l'ID de l'école de l'utilisateur
    $this->db->where('event_calendars.school_id', $school_id);
    // Filtrer par les classes autorisées pour l'étudiant
    $this->db->where_in('event_calendars.class_id', $permitted_class_ids);

    // Filtrer par ID si fourni
    if ($event_id) {
        $this->db->where('event_calendars.id', $event_id);
    } else {
        // Filtrer les événements qui chevauchent la période demandée
        $this->db->where('(event_calendars.starting_date <= "' . $end_date . '" AND (event_calendars.ending_date >= "' . $start_date . '" OR event_calendars.ending_date IS NULL))');
    }

    // Filtrer par class_id si fourni et valide
    if ($class_id && in_array($class_id, $permitted_class_ids)) {
        $this->db->where('event_calendars.class_id', $class_id);
    } elseif ($class_id) {
        // Si class_id est fourni mais non autorisé, retourner une erreur ou ignorer
        $csrf = [
            'csrfName' => $this->security->get_csrf_token_name(),
            'csrfHash' => $this->security->get_csrf_hash(),
        ];
        log_message('error', 'get_events - Unauthorized class_id: ' . $class_id);
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized class ID', 'csrf' => $csrf]);
        return;
    }

    $events = $this->db->get()->result_array();
    log_message('debug', 'get_events - SQL Query: ' . $this->db->last_query());
    log_message('debug', 'get_events - Events found: ' . json_encode($events));

    // Post-traitement des événements pour générer les occurrences récurrentes
    $processed_events = [];
    foreach ($events as $event) {
        $event['school_name'] = $event['school_name'] ?? '';
        $event['class_name'] = $event['class_name'] ?? '';
        $event['class_id'] = $event['class_id'] ?? null;

        // Normaliser les dates
        $start_date_obj = DateTime::createFromFormat('Y-m-d', $event['starting_date']);
        if ($start_date_obj) {
            $event['starting_date'] = $start_date_obj->format('Y-m-d');
        }
        if ($event['ending_date']) {
            $end_date_obj = DateTime::createFromFormat('Y-m-d', $event['ending_date']);
            if ($end_date_obj) {
                $event['ending_date'] = $end_date_obj->format('Y-m-d');
            }
        }
        if ($event['recurrence_end_date']) {
            $recurrence_end_date_obj = DateTime::createFromFormat('Y-m-d H:i:s', $event['recurrence_end_date']);
            if ($recurrence_end_date_obj === false) {
                $recurrence_end_date_obj = DateTime::createFromFormat('Y-m-d', $event['recurrence_end_date']);
            }
            $event['recurrence_end_date'] = $recurrence_end_date_obj ? $recurrence_end_date_obj->format('Y-m-d') : null;
        }

        // Vérifier si l'événement est expiré (plus de 24 heures)
        $event_start = DateTime::createFromFormat('Y-m-d H:i:s', $event['starting_date'] . ' ' . $event['starting_time'], new DateTimeZone('UTC'));
        $now = new DateTime('now', new DateTimeZone('UTC'));
        $threshold = (clone $now)->modify('-24 hours');
        $event['is_expired'] = $event_start < $threshold;

        // Si un ID spécifique est demandé, retourner uniquement l'événement de base
        if ($event_id) {
            $processed_events[] = $event;
            continue;
        }

        // Générer les occurrences pour les événements récurrents
        if ($event['recurrence_type'] !== 'does_not_repeat' && $event['recurrence_end_date']) {
            $recurrence_end_date = new DateTime($event['recurrence_end_date']);
            $current_date = new DateTime(max($event['starting_date'], $start_date));
            $end_period = new DateTime($end_date);

            $interval = null;
            if ($event['recurrence_type'] === 'daily') {
                $interval = new DateInterval('P1D');
            } elseif ($event['recurrence_type'] === 'weekly') {
                $interval = new DateInterval('P1D'); // Vérifier chaque jour pour les jours personnalisés
            } elseif ($event['recurrence_type'] === 'every_weekday') {
                $interval = new DateInterval('P1D');
            } elseif ($event['recurrence_type'] === 'monthly') {
                $interval = new DateInterval('P1M');
            } elseif ($event['recurrence_type'] === 'yearly') {
                $interval = new DateInterval('P1Y');
            }

            $custom_days = ($event['recurrence_type'] === 'weekly' && $event['custom_recurrence']) ? json_decode($event['custom_recurrence'], true) : [];

            while ($current_date <= $recurrence_end_date && $current_date <= $end_period) {
                $is_valid_date = true;
                if ($event['recurrence_type'] === 'every_weekday') {
                    $day_of_week = $current_date->format('l');
                    $is_valid_date = !in_array($day_of_week, ['Saturday', 'Sunday']);
                } elseif ($event['recurrence_type'] === 'weekly' && !empty($custom_days)) {
                    $day_of_week = $current_date->format('l');
                    $is_valid_date = in_array($day_of_week, $custom_days);
                } elseif ($event['recurrence_type'] === 'weekly') {
                    $is_valid_date = $current_date->format('l') === $start_date_obj->format('l');
                }

                if ($is_valid_date && $current_date->format('Y-m-d') >= $start_date) {
                    $event_copy = $event;
                    $event_copy['starting_date'] = $current_date->format('Y-m-d');
                    // Conserver la date de fin originale pour les événements récurrents
                    $event_copy['ending_date'] = $event['ending_date'];
                    // Vérifier si l'occurrence est expirée
                    $occurrence_start = DateTime::createFromFormat('Y-m-d H:i:s', $event_copy['starting_date'] . ' ' . $event['starting_time'], new DateTimeZone('UTC'));
                    $event_copy['is_expired'] = $occurrence_start < $threshold;
                    $processed_events[] = $event_copy;
                }

                $current_date->add($interval);
            }
        } else {
            // Événement non récurrent : vérifier si la période chevauche la plage demandée
            $event_start = new DateTime($event['starting_date']);
            $event_end = $event['ending_date'] ? new DateTime($event['ending_date']) : $event_start;
            $range_start = new DateTime($start_date);
            $range_end = new DateTime($end_date);

            if ($event_start <= $range_end && $event_end >= $range_start) {
                $processed_events[] = $event;
            }
        }
    }

    echo json_encode([
        'status' => 'success',
        'data' => $processed_events,
        'message' => empty($processed_events) ? 'No events found for your classes' : '',
        'csrf' => [
            'csrfName' => $this->security->get_csrf_token_name(),
            'csrfHash' => $this->security->get_csrf_hash()
        ]
    ]);
}

public function start_meeting() {
    // Vérifier l'authentification de l'utilisateur
    if ($this->session->userdata('student_login') != 1) {
        $csrf = [
            'csrfName' => $this->security->get_csrf_token_name(),
            'csrfHash' => $this->security->get_csrf_hash(),
        ];
        log_message('error', 'start_meeting - Unauthorized access attempt. Session data: ' . json_encode($this->session->userdata()));
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized', 'csrf' => $csrf]);
        return;
    }

    // Log des informations de session pour débogage
    log_message('debug', 'start_meeting - Session data: ' . json_encode($this->session->userdata()));

    $event_id = filter_var($this->input->post('event_id', true), FILTER_VALIDATE_INT);
    $occurrence_date = $this->input->post('occurrence_date', true);
    if (!$event_id || !$occurrence_date) {
        $csrf = [
            'csrfName' => $this->security->get_csrf_token_name(),
            'csrfHash' => $this->security->get_csrf_hash(),
        ];
        log_message('error', 'start_meeting - Invalid or missing event_id or occurrence_date');
        echo json_encode(['status' => 'error', 'message' => 'Event ID and occurrence date are required', 'csrf' => $csrf]);
        return;
    }

    // Validation de la date d'occurrence
    $occurrence_date_obj = DateTime::createFromFormat('Y-m-d', $occurrence_date);
    if ($occurrence_date_obj === false || $occurrence_date_obj->format('Y-m-d') !== $occurrence_date) {
        $csrf = [
            'csrfName' => $this->security->get_csrf_token_name(),
            'csrfHash' => $this->security->get_csrf_hash(),
        ];
        log_message('error', 'start_meeting - Invalid occurrence_date format for event_id: ' . $event_id);
        echo json_encode(['status' => 'error', 'message' => 'Invalid occurrence date format (expected: YYYY-MM-DD)', 'csrf' => $csrf]);
        return;
    }
    $occurrence_date = $occurrence_date_obj->format('Y-m-d');

    // Vérifier si l'événement existe
    $event = $this->db->get_where('event_calendars', ['id' => $event_id])->row_array();
    if (!$event) {
        $csrf = [
            'csrfName' => $this->security->get_csrf_token_name(),
            'csrfHash' => $this->security->get_csrf_hash(),
        ];
        log_message('error', 'start_meeting - Event not found for event_id: ' . $event_id);
        echo json_encode(['status' => 'error', 'message' => 'Event not found', 'csrf' => $csrf]);
        return;
    }

    // Vérifier si visio est activé
    if ($event['visio'] != 1) {
        $csrf = [
            'csrfName' => $this->security->get_csrf_token_name(),
            'csrfHash' => $this->security->get_csrf_hash(),
        ];
        log_message('error', 'start_meeting - Visio not enabled for event_id: ' . $event_id);
        echo json_encode(['status' => 'error', 'message' => 'Video conferencing is not enabled for this event', 'csrf' => $csrf]);
        return;
    }

    // Charger la configuration BBB
    $this->load->config('bigbluebutton');
    $bbb_url = $this->config->item('bbb_url');
    $bbb_secret = $this->config->item('bbb_secret');

    // Vérifier si un appointment existe pour cette occurrence spécifique
    $this->db->where('event_id', $event_id);
    $this->db->where('DATE(start_date)', $occurrence_date);
    $this->db->where('Etat', 1);
    $appointment = $this->db->get('appointments')->row_array();
    $appointment_id = $appointment ? $appointment['id'] : null;

    log_message('debug', 'start_meeting - Appointment check for event_id: ' . $event_id . ', occurrence_date: ' . $occurrence_date . ', appointment_id: ' . ($appointment_id ?? 'none'));

    // Si un appointment existe, vérifier la réunion associée
    if ($appointment && $appointment['meeting_id']) {
        $meeting = $this->db->get_where('sessions_meetings', ['meeting_id' => $appointment['meeting_id'], 'appointment_id' => $appointment_id])->row_array();
        if ($meeting) {
            // Vérifier si la réunion est active via l'API BigBlueButton
            $params = "meetingID=" . urlencode($appointment['meeting_id']);
            $checksum = sha1("getMeetingInfo" . $params . $bbb_secret);
            $api_url = $bbb_url . "getMeetingInfo?" . $params . "&checksum=" . $checksum;

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $api_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            $response = curl_exec($ch);
            $curl_error = curl_error($ch);
            curl_close($ch);

            if ($curl_error) {
                log_message('error', 'start_meeting - cURL error checking meeting status for meeting_id: ' . $appointment['meeting_id'] . ': ' . $curl_error);
                // En cas d'erreur cURL, ne pas créer un nouvel appointment, retourner une erreur
                $csrf = [
                    'csrfName' => $this->security->get_csrf_token_name(),
                    'csrfHash' => $this->security->get_csrf_hash(),
                ];
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Failed to check meeting status due to server error',
                    'csrf' => $csrf
                ]);
                return;
            }

            $xml = simplexml_load_string($response);
            log_message('debug', 'start_meeting - BBB getMeetingInfo response for meeting_id: ' . $appointment['meeting_id'] . ': ' . $response);

            // Vérifier si la réunion est active
            if ($xml && (string)$xml->returncode === "SUCCESS" && (string)$xml->running === "true") {
                // La réunion est active, rejoindre
                $user_details = $this->user_model->get_user_details($this->session->userdata('user_id'));
                $full_name = urlencode($user_details['name'] ?? 'Admin-' . rand(1000, 9999));
                $password = $meeting['moderator_pw'];
                $join_params = "fullName=$full_name&meetingID=" . urlencode($appointment['meeting_id']) . "&password=$password&redirect=true";
                $join_checksum = sha1("join" . $join_params . $bbb_secret);
                $join_url = $bbb_url . "join?" . $join_params . "&checksum=" . $join_checksum;
                log_message('debug', 'start_meeting - Joining existing meeting for event_id: ' . $event_id . ', occurrence_date: ' . $occurrence_date . ', meeting_id: ' . $appointment['meeting_id'] . ', join_url: ' . $join_url);
                try {
                        require_once APPPATH . '../vendor/autoload.php';
                        $client = new \WebSocket\Client('ws://localhost:8080/ws');
                        $client->send(json_encode([
                            'action' => 'update_participants',
                            'meetingID' => $appointment['meeting_id'],
                            'participantCount' => $participant_count,
                            'isRunning' => true
                        ]));
                        log_message('debug', 'start_meeting - Sent WebSocket message for meeting_id: ' . $appointment['meeting_id'] . ', participant_count: ' . $participant_count);
                        $client->close();
                    } catch (Exception $e) {
                        log_message('error', 'start_meeting - WebSocket error for meeting_id: ' . $appointment['meeting_id'] . ': ' . $e->getMessage());
                    }
                $csrf = [
                    'csrfName' => $this->security->get_csrf_token_name(),
                    'csrfHash' => $this->security->get_csrf_hash(),
                ];
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Joining existing meeting',
                    'join_url' => $join_url,
                    'meeting_id' => $appointment['meeting_id'],
                    'appointment_id' => $appointment_id,
                    'is_running' => true,
                    'csrf' => $csrf
                ]);
                return;
            } else {
                log_message('debug', 'start_meeting - Meeting ended or not found for meeting_id: ' . $appointment['meeting_id'] . ', creating new appointment');
                $this->db->where('id', $appointment_id);
                $this->db->update('appointments', ['Etat' => 0]);
                $appointment = null;
            }
        }
    }

    // Si aucun appointment n'existe ou si la réunion précédente est terminée, créer un nouveau
    if (!$appointment) {
        $appointment_data = [
            'event_id' => $event_id,
            'title' => $event['title'],
            'start_date' => $occurrence_date . ' ' . $event['starting_time'],
            'classe_id' => $event['class_id'],
            'school_id' => $event['school_id'],
            'visio' => $event['visio'],
            'Etat' => 1,
            'meeting_id' => null
        ];
        $this->db->insert('appointments', $appointment_data);
        $appointment_id = $this->db->insert_id();
        log_message('debug', 'start_meeting - Created new appointment for event_id: ' . $event_id . ', occurrence_date: ' . $occurrence_date . ', appointment_id: ' . $appointment_id);
    } else {
        $appointment_id = $appointment['id'];
    }

    // Créer une nouvelle réunion BBB
    $meeting_name = $event['title'] ? $event['title'] : "Meeting for Event $event_id";
    $new_meeting_id = "meeting-$appointment_id-" . time();
    $attendee_password = "ap_" . $appointment_id;
    $moderator_password = "mp_" . $appointment_id;
    $start_time = $occurrence_date . ' ' . $event['starting_time'];
    $end_time = date('Y-m-d H:i:s', strtotime($start_time . ' +2 hours')); // Durée par défaut : 2 heures

    $params = "name=" . urlencode($meeting_name) .
              "&meetingID=" . urlencode($new_meeting_id) .
              "&attendeePW=$attendee_password" .
              "&moderatorPW=$moderator_password" .
              "&record=true" .
              "&autoStartRecording=false" .
              "&allowStartStopRecording=true" .
              "&welcome=" . urlencode("Welcome to the meeting: " . $event['title']) .
              "&endWhenNoModerator=false" .
              "&duration=120"; // 120 minutes (2 heures)

    $checksum = sha1("create" . $params . $bbb_secret);
    $create_url = $bbb_url . "create?" . $params . "&checksum=" . $checksum;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $create_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    $response = curl_exec($ch);
    $curl_error = curl_error($ch);
    curl_close($ch);

    if ($curl_error) {
        $csrf = [
            'csrfName' => $this->security->get_csrf_token_name(),
            'csrfHash' => $this->security->get_csrf_hash(),
        ];
        log_message('error', 'start_meeting - cURL error creating meeting for meeting_id: ' . $new_meeting_id . ': ' . $curl_error);
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to create meeting due to server error',
            'csrf' => $csrf
        ]);
        return;
    }

    if (empty($response)) {
            $csrf = [
                'csrfName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
            ];
            log_message('error', 'start_meeting - Empty response from BBB API for meeting_id: ' . $new_meeting_id);
            echo json_encode([
                'status' => 'error',
                'message' => 'Failed to create meeting: No response from BBB server',
                'csrf' => $csrf
            ]);
            return;
        }

        $xml = simplexml_load_string($response);
        if ($xml === false || !isset($xml->returncode)) {
            $csrf = [
                'csrfName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
            ];
            log_message('error', 'start_meeting - Invalid XML response from BBB API for meeting_id: ' . $new_meeting_id . ': ' . $response);
            echo json_encode([
                'status' => 'error',
                'message' => 'Failed to create meeting: Invalid response from BBB server',
                'csrf' => $csrf
            ]);
            return;
        }

        if ((string)$xml->returncode === "SUCCESS") {
            $meeting_data = [
                'meeting_id' => $new_meeting_id,
                'appointment_id' => $appointment_id,
                'name' => $meeting_name,
                'start_time' => $start_time,
                'end_time' => $end_time,
                'attendee_pw' => $attendee_password,
                'moderator_pw' => $moderator_password,
                'school_id' => $event['school_id'],
                'user_id' => $this->session->userdata('user_id'),
                'class_id' => $event['class_id'],
                'created_at' => date('Y-m-d H:i:s'),
                'description' => null
            ];
            $this->db->insert('sessions_meetings', $meeting_data);

            $this->db->where('id', $appointment_id);
            $this->db->update('appointments', ['meeting_id' => $new_meeting_id]);

            $is_running_params = "meetingID=" . urlencode($new_meeting_id);
            $is_running_checksum = sha1("isMeetingRunning" . $is_running_params . $bbb_secret);
            $is_running_url = $bbb_url . "isMeetingRunning?" . $is_running_params . "&checksum=" . $is_running_checksum;

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $is_running_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            $is_running_response = curl_exec($ch);
            $curl_error = curl_error($ch);
            curl_close($ch);

            $is_running = false;
            $participant_count = 0;
            if (!$curl_error) {
                $is_running_xml = simplexml_load_string($is_running_response);
                log_message('debug', 'start_meeting - BBB isMeetingRunning response for meeting_id: ' . $new_meeting_id . ': ' . $is_running_response);
                if ($is_running_xml && (string)$is_running_xml->returncode === "SUCCESS") {
                    $is_running = (string)$is_running_xml->running === "true";
                    if ($is_running) {
                        $params = "meetingID=" . urlencode($new_meeting_id);
                        $checksum = sha1("getMeetingInfo" . $params . $bbb_secret);
                        $api_url = $bbb_url . "getMeetingInfo?" . $params . "&checksum=" . $checksum;
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $api_url);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                        $response = curl_exec($ch);
                        curl_close($ch);
                        $xml = simplexml_load_string($response);
                        if ($xml && (string)$xml->returncode === "SUCCESS") {
                            $participant_count = (int)$xml->participantCount;
                        }
                    }
                }
            }

            // Send initial participant count update via WebSocket
            try {
                require_once APPPATH . '../vendor/autoload.php';
                $client = new \WebSocket\Client('ws://localhost:8080/ws');
                $client->send(json_encode([
                    'action' => 'update_participants',
                    'meetingID' => $new_meeting_id,
                    'participantCount' => $participant_count,
                    'isRunning' => $is_running
                ]));
                log_message('debug', 'start_meeting - Sent WebSocket message for meeting_id: ' . $new_meeting_id . ', participant_count: ' . $participant_count);
                $client->close();
            } catch (Exception $e) {
                log_message('error', 'start_meeting - WebSocket error for meeting_id: ' . $new_meeting_id . ': ' . $e->getMessage());
            }

            $user_details = $this->user_model->get_user_details($this->session->userdata('user_id'));
            $full_name = urlencode($user_details['name'] ?? 'Member-' . rand(1000, 9999));
            $join_params = "fullName=$full_name&meetingID=" . urlencode($new_meeting_id) . "&password=$attendee_password&redirect=true";
            $join_checksum = sha1("join" . $join_params . $bbb_secret);
            $join_url = $bbb_url . "join?" . $join_params . "&checksum=" . $join_checksum;

            log_message('debug', 'start_meeting - Created new meeting for event_id: ' . $event_id . ', occurrence_date: ' . $occurrence_date . ', meeting_id: ' . $new_meeting_id . ', join_url: ' . $join_url);
            $csrf = [
                'csrfName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
            ];
            echo json_encode([
                'status' => 'success',
                'message' => 'New meeting created successfully',
                'join_url' => $join_url,
                'meeting_id' => $new_meeting_id,
                'appointment_id' => $appointment_id,
                'participant_count' => $participant_count,
                'is_running' => $is_running,
                'csrf' => $csrf
            ]);
        } else {
            $csrf = [
                'csrfName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
            ];
            log_message('error', 'start_meeting - BBB API error for meeting_id: ' . $new_meeting_id . ': ' . (string)$xml->message);
            echo json_encode([
                'status' => 'error',
                'message' => 'Failed to create meeting: ' . (string)$xml->message,
                'csrf' => $csrf
            ]);
        }
}

public function get_classes_with_events() {
    if ($this->session->userdata('student_login') != 1) {
        $csrf = [
            'csrfName' => $this->security->get_csrf_token_name(),
            'csrfHash' => $this->security->get_csrf_hash(),
        ];
        log_message('error', 'get_classes_with_events - Unauthorized access attempt');
        echo json_encode(['status' => 'error', 'message' => 'Session expired, please login again', 'csrf' => $csrf]);
        return;
    }

    $user_id = $this->session->userdata('user_id');
    log_message('debug', 'get_classes_with_events - user_id: ' . ($user_id ?? 'null'));

    // Mapper user_id à student_id dans la table students
    $this->db->select('id');
    $this->db->from('students');
    $this->db->where('user_id', $user_id);
    $student = $this->db->get()->row_array();
    $student_id = $student['id'] ?? null;
    log_message('debug', 'get_classes_with_events - student_id: ' . ($student_id ?? 'null'));

    if (!$student_id) {
        $csrf = [
            'csrfName' => $this->security->get_csrf_token_name(),
            'csrfHash' => $this->security->get_csrf_hash(),
        ];
        log_message('error', 'get_classes_with_events - No student_id found for user_id: ' . $user_id);
        echo json_encode(['status' => 'error', 'message' => 'No student associated with this user', 'csrf' => $csrf]);
        return;
    }

    $user_details = $this->user_model->get_user_details($user_id);
    $user_school_id = $user_details['school_id'] ?? null;
    log_message('debug', 'get_classes_with_events - school_id: ' . ($user_school_id ?? 'null'));

    $school_id = $this->input->post('school_id', true);
    $start_date = $this->input->post('start_date', true);
    $end_date = $this->input->post('end_date', true);
    log_message('debug', 'get_classes_with_events - Parameters: school_id=' . ($school_id ?? 'null') . ', start_date=' . ($start_date ?? 'null') . ', end_date=' . ($end_date ?? 'null'));

    if (empty($school_id) || empty($start_date) || empty($end_date) || $school_id != $user_school_id) {
        $csrf = [
            'csrfName' => $this->security->get_csrf_token_name(),
            'csrfHash' => $this->security->get_csrf_hash(),
        ];
        log_message('error', 'get_classes_with_events - Invalid or unauthorized school_id: ' . ($school_id ?? 'null'));
        echo json_encode(['status' => 'error', 'message' => 'Invalid or unauthorized school ID', 'csrf' => $csrf]);
        return;
    }

    // Vérifier les classes autorisées pour l'étudiant via enrols
    $this->db->select('class_id');
    $this->db->from('enrols');
    $this->db->where('student_id', $student_id);
    $this->db->where('school_id', $school_id);
    $permitted_classes = $this->db->get()->result_array();
    $permitted_class_ids = array_column($permitted_classes, 'class_id');
    log_message('debug', 'get_classes_with_events - permitted_class_ids: ' . json_encode($permitted_class_ids));

    if (empty($permitted_class_ids)) {
        $csrf = [
            'csrfName' => $this->security->get_csrf_token_name(),
            'csrfHash' => $this->security->get_csrf_hash(),
        ];
        echo json_encode(['status' => 'success', 'message' => 'Aucune classe autorisée pour cet étudiant', 'classes' => [], 'csrf' => $csrf]);
        return;
    }

    // Vérifier si les class_id existent dans la table classes
    $this->db->select('id');
    $this->db->from('classes');
    $this->db->where('school_id', $school_id);
    $this->db->where_in('id', $permitted_class_ids);
    $valid_classes = $this->db->get()->result_array();
    $valid_class_ids = array_column($valid_classes, 'id');
    log_message('debug', 'get_classes_with_events - valid_class_ids: ' . json_encode($valid_class_ids));

    if (empty($valid_class_ids)) {
        $csrf = [
            'csrfName' => $this->security->get_csrf_token_name(),
            'csrfHash' => $this->security->get_csrf_hash(),
        ];
        echo json_encode(['status' => 'success', 'message' => 'Aucune classe valide trouvée pour votre école', 'classes' => [], 'csrf' => $csrf]);
        return;
    }

    // Récupérer les classes avec des événements
    $this->db->select('DISTINCT(event_calendars.class_id) as class_id, classes.name');
    $this->db->from('event_calendars');
    $this->db->join('classes', 'event_calendars.class_id = classes.id');
    $this->db->where('event_calendars.school_id', $school_id);
    $this->db->where_in('event_calendars.class_id', $valid_class_ids);
    $this->db->where('event_calendars.starting_date >=', $start_date);
    $this->db->where('event_calendars.starting_date <=', $end_date);
    $query = $this->db->get();
    log_message('debug', 'get_classes_with_events - SQL Query: ' . $this->db->last_query());

    $classes = $query->result_array();

    $csrf = [
        'csrfName' => $this->security->get_csrf_token_name(),
        'csrfHash' => $this->security->get_csrf_hash(),
    ];

    echo json_encode([
        'status' => 'success',
        'classes' => $classes,
        'message' => empty($classes) ? 'Aucune classe avec des événements pour vos permissions' : '',
        'csrf' => $csrf
    ]);
}
}
