<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
*  @author   : Creativeitem
*  date      : November, 2019
*  Ekattor School Management System With Addons
*  http://codecanyon.net/user/Creativeitem
*  http://support.creativeitem.com
*/

class Teacher extends CI_Controller {

	public function __construct(){

		parent::__construct();

		$this->load->database();
		$this->load->library('session');
		$this->config->load('config'); 
    
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
		
		if($this->session->userdata('teacher_login') != 1){
			redirect(site_url('login'), 'refresh');
		}

		// ---- Vérification du statut de paiement de la communauté pour bloquer les mentors ----
		// Après la vérification ci-dessus, on est sûr que c'est un teacher
		// Donc on vérifie toujours le statut de la communauté
		{
			$school_id = $this->session->userdata('school_id');
			if (!$school_id) {
				$school_id = $this->session->userdata('active_school_id');
			}
			
			if ($school_id) {
				$school = $this->db->get_where('schools', ['id' => $school_id])->row_array();
			} else {
				$school = null;
			}

			$current_method = $this->router->method;
			
			log_message('debug', "Teacher construct - is_teacher: true, school_id: $school_id, method: $current_method");

			// ---- Gestion de la période d'essai de 14 jours et de l'abonnement mensuel ----
			$trial_expired = false;
			$subscription_expired = false;
			$school_not_approved = false;
			
			// Protection de base : communauté non approuvée
			if (!$school || (int)$school['status'] !== 1) {
				$school_not_approved = true;
				log_message('debug', "Teacher construct - School not approved or not found");
			}
			
			if ($school) {
				$now             = time();
				$is_trial        = isset($school['is_trial']) ? (int)$school['is_trial'] : 0;
				$is_paid         = isset($school['is_paid']) ? (int)$school['is_paid'] : 0;
				$trial_end       = isset($school['trial_end']) ? (int)$school['trial_end'] : 0;
				$subscription_end = isset($school['subscription_end']) ? (int)$school['subscription_end'] : 0;

				// DEBUG: Log pour vérifier les valeurs
				log_message('debug', "Teacher construct - school_id: $school_id, is_paid: $is_paid, subscription_end: $subscription_end, now: $now");
				if ($subscription_end > 0) {
					log_message('debug', "Teacher construct - subscription_end date: " . date('Y-m-d H:i:s', $subscription_end) . ", now date: " . date('Y-m-d H:i:s', $now));
				}

				// Vérifier si l'essai de 14 jours est expiré
				if ($is_trial === 1 && $is_paid === 0 && $trial_end > 0 && $now > $trial_end) {
					$trial_expired = true;
					log_message('debug', "Teacher construct - Trial expired detected");
				}

				// Vérifier si l'abonnement mensuel est expiré
				// Si subscription_end existe et est passé, l'abonnement est expiré (peu importe is_paid)
				// On vérifie seulement si l'école n'est pas en période d'essai (is_trial = 0)
				if ($is_trial === 0 && $subscription_end > 0 && $now > $subscription_end) {
					$subscription_expired = true;
					log_message('debug', "Teacher construct - Subscription expired detected: subscription_end (" . date('Y-m-d H:i:s', $subscription_end) . ") < now (" . date('Y-m-d H:i:s', $now) . ")");
				} elseif ($is_trial === 0 && $subscription_end > 0 && $now <= $subscription_end) {
					// Log pour confirmer que l'abonnement est encore valide
					log_message('debug', "Teacher construct - Subscription still valid: subscription_end (" . date('Y-m-d H:i:s', $subscription_end) . ") >= now (" . date('Y-m-d H:i:s', $now) . ")");
				}
			}

			// Partage l'info avec les vues
			$this->trial_expired = $trial_expired || $subscription_expired || $school_not_approved;
			$this->school_data   = $school;

			// Si l'essai ou l'abonnement est expiré, OU si la communauté n'est pas approuvée, on bloque l'accès
			if ($trial_expired || $subscription_expired || $school_not_approved) {
				// Laisser accès uniquement au dashboard, au logout et au changement de langue
				// Note: Les mentors ne peuvent pas payer directement, seul l'admin peut payer
				$allowed_methods_trial = ['dashboard', 'logout', 'language'];

				if (!in_array($current_method, $allowed_methods_trial)) {
					// Bloquer l'accès : rediriger vers le dashboard où un message sera affiché
					log_message('debug', "Teacher construct - Access blocked for method '$current_method' (trial_expired: " . ($trial_expired ? 'true' : 'false') . ", subscription_expired: " . ($subscription_expired ? 'true' : 'false') . ", school_not_approved: " . ($school_not_approved ? 'true' : 'false') . ")");
					redirect(site_url('teacher/dashboard'));
				} else {
					log_message('debug', "Teacher construct - Access allowed for method '$current_method' (blocked but method is in allowed list)");
				}
			} else {
				log_message('debug', "Teacher construct - Access allowed for method '$current_method' (no blocking conditions)");
			}
		}
	}
	//dashboard
	public function index(){
		redirect(route('dashboard'), 'refresh');
	}

	public function dashboard(){

			$page_data['page_title'] = 'Dashboard';
		    $page_data['folder_name'] = 'dashboard';
			$this->load->view('backend/index', $page_data);
	}


	   //START TEACHER Create_Join bigbleubutton 
	   public function Create_Join($param1 = '', $param2 = '', $param3 = '')
	   {
	 
   
	 
				if (empty($param1)) {
				$page_data['folder_name'] = 'bigbleubutton';
				$page_data['page_title'] = 'Démarrer Réunion';
				$this->load->view('backend/index', $page_data);
				}
	   }
	   //END TEACHER Create_Join bigbleubutton 
		//START TEACHER Create_Join bigbleubutton 
		public function Liveclasse($param1 = '', $param2 = '', $param3 = '')
		{
		
			if ($param1 == 'create') {
			$response = $this->room_model->create_room();
			// echo $response;
			// Préparer la réponse avec un nouveau jeton CSRF
			$csrf = array(
				'csrfName' => $this->security->get_csrf_token_name(),
				'csrfHash' => $this->security->get_csrf_hash(),
			);
		
			// Renvoyer la réponse avec un nouveau jeton CSRF
			echo json_encode(array('status' => $response, 'csrf' => $csrf));
			}

			if ($param1 == 'update') {
			$response = $this->room_model->update_room($param2);
			// echo $response;
			// Préparer la réponse avec un nouveau jeton CSRF
			$csrf = array(
				'csrfName' => $this->security->get_csrf_token_name(),
				'csrfHash' => $this->security->get_csrf_hash(),
			);
		
			// Renvoyer la réponse avec un nouveau jeton CSRF
			echo json_encode(array('status' => $response, 'csrf' => $csrf));
			}
			if ($param1 == 'list') {
				$this->load->view('backend/teacher/bigbleubutton/list');
			}
		
			if (empty($param1)) {
			$page_data['folder_name'] = 'bigbleubutton';
			$page_data['page_title'] = 'Démarrer Réunion';
			$this->load->view('backend/index', $page_data);
			}
		}
		//END TEACHER Create_Join bigbleubutton 
		
	
		  public function get_appointments() 
		  {
			$appointments = $this->room_model->get_all_appointments();
			echo json_encode($appointments);
		  }
		  
		public function add_appointment() 
		{
			$schoolID = school_id();
			// Récupération et sécurisation des données
			$title = $this->input->post('title', true);
			$start_date = $this->input->post('start', true);
			$description = $this->input->post('description', true);
			$classe_id = $this->input->post('classe_id', true);
			$room_id = $this->input->post('room_id', true);
			$sections = $this->input->post('sections'); // Tableau de sections sélectionnées
			
		  
		   
			// Vérification : Les champs obligatoires ne doivent pas être vides
			if (empty($title) || empty($start_date)) {
				echo json_encode(["status" => "error", "message" => "Titre et date sont obligatoires"]);
				return;
			}
		
			// Création du tableau de données à insérer
			$data = array(
				'title' => $title,
				'start_date' => $start_date,
				'description' => $description,
				'classe_id' => $classe_id,
				'sections_id' => $sections, // Stockage sous forme "1,2,3"
				'room_id' => $room_id,
				'school_id' => $schoolID
			);
		
			// Insertion dans la base de données avec gestion d'erreur
			try {
				$this->db->insert('appointments', $data);
				echo json_encode(["status" => "success", "message" => "Rendez-vous ajouté avec succès"]);
			} catch (Exception $e) {
				echo json_encode(["status" => "error", "message" => "Erreur lors de l'ajout du rendez-vous : " . $e->getMessage()]);
			}
		}
		
		  public function update_appointment()
		   {
				$id = $this->input->post('id');
				// die( $id);
			
				$data = array(
					'title' => $this->input->post('title'),
					'start_date' => $this->input->post('start'),
					'description' => $this->input->post('description'),
					'classe_id' => $this->input->post('classe_id'),
					'sections_id' => $this->input->post('sections'),
					'room_id' => $this->input->post('room_id')
				);
			
				$this->db->where('id', $id);
				$this->db->update('appointments', $data);
				echo json_encode(["status" => "updated"]);
			}
		
		  public function delete_appointment() {
			  $id = $this->input->post('id');
	
			  $appointments ['Etat'] = 0;
	
			  $this->db->where('id', $id);
			  $this->db->update('appointments', $appointments);
		  
		  
			  // $this->db->where('id', $id);
			  // $this->db->delete('appointments');
			  echo json_encode(["status" => "deleted"]);
		  }

		  public function delete_room()
		  {
			  $data = json_decode(file_get_contents("php://input"), true);
			  $roomID = $data['selectedRoomID'];
			  // $this->db->delete("rooms", ["id" => $roomID]);
			  $this->room_model->update_room_by_id($roomID);
			  echo json_encode(["status" => "success", "message" => "Room supprimée avec succès !"]);
		  }
	
	// public function recording($param1 = '', $param2 = '', $param3 = '')
    // {
    //         // Check authentication
    //         if ($this->session->userdata('teacher_login') != 1) {
    //             redirect(site_url('login'), 'refresh');
    //         }

    //         // Get the logged-in user's ID
    //         $user_id = $this->session->userdata('user_id');

    //         // Map user_id to teacher_id
    //         $this->db->select('id');
    //         $this->db->from('teachers');
    //         $this->db->where('user_id', $user_id);
    //         $teacher = $this->db->get()->row_array();
    //         $teacher_id = $teacher['id'] ?? null;

    //         if (!$teacher_id) {
    //             log_message('error', 'recording - No teacher associated with user_id: ' . $user_id);
    //             show_error('No teacher associated with this user.', 403);
    //             return;
    //         }

    //         // Get permitted class IDs from teacher_permissions where attendance = 1
    //         $this->db->select('class_id');
    //         $this->db->from('teacher_permissions');
    //         $this->db->where('teacher_id', $teacher_id);
    //         $this->db->where('attendance', 1);
    //         $permitted_classes = $this->db->get()->result_array();
    //         $permitted_class_ids = array_map('strval', array_column($permitted_classes, 'class_id'));

    //         // Get school_id from users table
    //         $user = $this->db->get_where('users', ['id' => $user_id])->row_array();
    //         if (!$user || empty($user['school_id'])) {
    //             log_message('error', 'recording - No school_id found for user_id: ' . $user_id);
    //             show_error('No school associated with this user.', 403);
    //             return;
    //         }
    //         $school_id = $user['school_id'];

    //         // Synchronize recordings (unchanged)
    //         $this->load->config('bigbluebutton');
    //         $bbb_url = $this->config->item('bbb_url');
    //         $bbb_secret = $this->config->item('bbb_secret');

    //         $last_sync = $this->session->userdata('last_recording_sync');
    //         $current_time = time();
    //         $sync_interval = 10; // Synchronize every 10 seconds (for testing, adjust as needed)

    //         if (!$last_sync || ($current_time - $last_sync) > $sync_interval) {
    //             // Filter meetings by school_id
    //             $this->db->where('school_id', $school_id);
    //             $meetings = $this->db->get('sessions_meetings')->result_array();

    //             foreach ($meetings as $meeting) {
    //                 $params = "meetingID=" . urlencode($meeting['meeting_id']);
    //                 $checksum = sha1("getRecordings" . $params . $bbb_secret);
    //                 $recordings_url = $bbb_url . "getRecordings?" . $params . "&checksum=" . $checksum;

    //                 $ch = curl_init();
    //                 curl_setopt($ch, CURLOPT_URL, $recordings_url);
    //                 curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    //                 curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    //                 curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    //                 $recordings_response = curl_exec($ch);
    //                 $curl_error = curl_error($ch);
    //                 curl_close($ch);

    //                 if ($curl_error) {
    //                     log_message('error', 'recording - cURL error for meeting_id: ' . $meeting['meeting_id'] . ': ' . $curl_error);
    //                     continue;
    //                 }

    //                 $recordings_xml = simplexml_load_string($recordings_response);
    //                 if ($recordings_xml && (string)$recordings_xml->returncode === "SUCCESS") {
    //                     foreach ($recordings_xml->recordings->recording as $recording) {
    //                         if ((string)$recording->state !== 'published') {
    //                             continue;
    //                         }
    //                         $recording_id = (string)$recording->recordID;
    //                         $recording_start_time = (string)$recording->startTime;
    //                         $recording_end_time = (string)$recording->endTime;
    //                         $original_recording_url = (string)$recording->playback->format->url;
    //                         $recording_url = str_replace('https://31.97.52.98', 'https://visio.wayo.site', $original_recording_url);
    //                         $duration_seconds = (int)(($recording_end_time - $recording_start_time) / 1000);

    //                         // Format duration
    //                         $hours = floor($duration_seconds / 3600);
    //                         $minutes = floor(($duration_seconds % 3600) / 60);
    //                         $seconds = $duration_seconds % 60;
    //                         $formatted_duration = $hours >= 1
    //                             ? sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds)
    //                             : sprintf("%02d:%02d", $minutes, $seconds);

    //                         $start_time = date('Y-m-d H:i:s', $recording_start_time / 1000);
    //                         $end_time = date('Y-m-d H:i:s', $recording_end_time / 1000);

    //                         $existing = $this->db->get_where('recordings', ['recording_id' => $recording_id])->row_array();
    //                         if (!$existing) {
    //                             $recording_data = [
    //                                 'meeting_id' => $meeting['meeting_id'],
    //                                 'appointment_id' => $meeting['appointment_id'],
    //                                 'recording_id' => $recording_id,
    //                                 'name' => $meeting['name'],
    //                                 'class_id' => $meeting['class_id'],
    //                                 'school_id' => $meeting['school_id'],
    //                                 'start_time' => $start_time,
    //                                 'end_time' => $end_time,
    //                                 'duration' => $duration_seconds,
    //                                 'formatted_duration' => $formatted_duration,
    //                                 'recording_url' => $recording_url,
    //                                 'created_at' => date('Y-m-d H:i:s'),
    //                                 'updated_at' => date('Y-m-d H:i:s')
    //                             ];
    //                             $this->db->insert('recordings', $recording_data);
    //                         } else {
    //                             $this->db->where('recording_id', $recording_id);
    //                             $this->db->update('recordings', [
    //                                 'recording_url' => $recording_url,
    //                                 'updated_at' => date('Y-m-d H:i:s')
    //                             ]);
    //                         }
    //                     }
    //                 }
    //             }

    //             $this->session->set_userdata('last_recording_sync', $current_time);
    //         }

    //         // Initialize filters
    //         $filters = [
    //             'meeting_name' => $this->input->post('meeting_name', true) ?? '',
    //             'date_range' => $this->input->post('date_range', true) ?? ''
    //         ];

    //         // Build query for recordings with access restrictions
    //         $this->db->select('r.*, c.name as class_name');
    //         $this->db->from('recordings r');
    //         $this->db->join('classes c', 'r.class_id = c.id', 'left');
    //         $this->db->join('appointments a', 'r.appointment_id = a.id', 'inner');
    //         $this->db->join('appointment_participants ap', 'a.id = ap.appointment_id', 'left');
    //         $this->db->where('r.school_id', $school_id);

    //         // Allow access if teacher is invited individually or has class permissions
    //         if (!empty($permitted_class_ids)) {
    //             $this->db->where('
    //                 (ap.type = "class" AND ap.guest IN (' . implode(',', array_map('intval', $permitted_class_ids)) . '))
    //                 OR (ap.type = "individual" AND ap.guest = ' . intval($user_id) . ')
    //             ');
    //         } else {
    //             // Only check for individual invitations if no class permissions exist
    //             $this->db->where('ap.type', 'individual');
    //             $this->db->where('ap.guest', $user_id);
    //         }

    //         // Apply filters
    //         if (!empty($filters['meeting_name'])) {
    //             $this->db->like('r.name', $filters['meeting_name'], 'both');
    //         }
    //         if (!empty($filters['date_range'])) {
    //             $dates = explode(' - ', $filters['date_range']);
    //             if (count($dates) == 1) {
    //                 $date = DateTime::createFromFormat('d-m-Y', trim($dates[0]));
    //                 if ($date) {
    //                     $this->db->where('DATE(r.start_time)', $date->format('Y-m-d'));
    //                 }
    //             } elseif (count($dates) == 2) {
    //                 $date_from = DateTime::createFromFormat('d-m-Y', trim($dates[0]));
    //                 $date_to = DateTime::createFromFormat('d-m-Y', trim($dates[1]));
    //                 if ($date_from && $date_to) {
    //                     $this->db->where('r.start_time >=', $date_from->format('Y-m-d 00:00:00'));
    //                     $this->db->where('r.start_time <=', $date_to->format('Y-m-d 23:59:59'));
    //                 }
    //             }
    //         }

    //         $this->db->group_by('r.id'); // Avoid duplicates
    //         $this->db->order_by('r.created_at', 'DESC');
    //         $recordings = $this->db->get()->result_array();

    //         // Handle AJAX request
    //         if ($this->input->is_ajax_request()) {
    //             header('Content-Type: application/json');
    //             echo json_encode([
    //                 'status' => 'success',
    //                 'recordings' => $recordings,
    //                 'filters' => $filters,
    //                 'csrf_token' => $this->security->get_csrf_hash()
    //             ]);
    //             return;
    //         }

    //         // Load view for non-AJAX request
    //         $page_data['recordings'] = $recordings;
    //         $page_data['filters'] = $filters;
    //         $page_data['page_name'] = 'recording/recording';
    //         $page_data['page_title'] = 'recording';

    //         $this->load->view('backend/index', $page_data);
    // }

    public function recording($param1 = '', $param2 = '', $param3 = '') 
    {
        // Check authentication
        if ($this->session->userdata('teacher_login') != 1) {
            redirect(site_url('login'), 'refresh');
        }

        // Get User and Teacher ID
        $user_id = $this->session->userdata('user_id');
        $teacher = $this->db->get_where('teachers', ['user_id' => $user_id])->row_array();
        if (!$teacher) {
            show_error('No teacher found', 403);
            return;
        }
        $teacher_id = $teacher['id'];

        // Get school_id
        $user = $this->db->get_where('users', ['id' => $user_id])->row_array();
        if (!$user || empty($user['school_id'])) {
            show_error('No school associated', 403);
            return;
        }
        $school_id = $user['school_id'];

        // Get permitted classes
        $this->db->select('class_id');
        $this->db->from('teacher_permissions');
        $this->db->where('teacher_id', $teacher_id);
        $this->db->where('attendance', 1);
        $permitted_classes = $this->db->get()->result_array();
        $permitted_class_ids = array_column($permitted_classes, 'class_id');
        
        if (empty($permitted_class_ids)) {
            $permitted_class_ids = [-1]; 
        }

        // Synchronize recordings
        $this->load->config('bigbluebutton');
        $bbb_url = $this->config->item('bbb_url');
        $bbb_secret = $this->config->item('bbb_secret');

        $last_sync = $this->session->userdata('last_recording_sync');
        $current_time = time();
        $sync_interval = 10; 

        if (!$last_sync || ($current_time - $last_sync) > $sync_interval) {
            $this->db->where('school_id', $school_id);
            // Optimization: Limit to recent meetings and permitted classes
            $this->db->where('created_at >=', date('Y-m-d H:i:s', strtotime('-7 days')));
            $this->db->where_in('class_id', $permitted_class_ids);
            $this->db->order_by('created_at', 'DESC');
            $this->db->limit(15);
            $meetings = $this->db->get('sessions_meetings')->result_array();

            foreach ($meetings as $meeting) {
                $params = "meetingID=" . urlencode($meeting['meeting_id']);
                $checksum = sha1("getRecordings" . $params . $bbb_secret);
                $recordings_url = $bbb_url . "getRecordings?" . $params . "&checksum=" . $checksum;

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $recordings_url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                $recordings_response = curl_exec($ch);
                $curl_error = curl_error($ch);
                curl_close($ch);

                if ($curl_error) {
                    log_message('error', 'recording - cURL error for meeting_id: ' . $meeting['meeting_id'] . ': ' . $curl_error);
                    continue;
                }

                $recordings_xml = simplexml_load_string($recordings_response);
                if ($recordings_xml && (string)$recordings_xml->returncode === "SUCCESS") {
                    foreach ($recordings_xml->recordings->recording as $recording) {
                        if ((string)$recording->state !== 'published') {
                            continue;
                        }
                        $recording_id = (string)$recording->recordID;
                        $recording_start_time = (string)$recording->startTime;
                        $recording_end_time = (string)$recording->endTime;
                        $original_recording_url = (string)$recording->playback->format->url;
                        $recording_url = str_replace('https://31.97.52.98', 'https://visio.wayo.site', $original_recording_url);
                        $duration_seconds = (int)(($recording_end_time - $recording_start_time) / 1000);

                        // Format duration
                        $hours = floor($duration_seconds / 3600);
                        $minutes = floor(($duration_seconds % 3600) / 60);
                        $seconds = $duration_seconds % 60;
                        
                        if ($hours >= 1) {
                            $formatted_duration = sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds);
                        } else {
                            $formatted_duration = sprintf("%02d:%02d", $minutes, $seconds);
                        }

                        $start_time = date('Y-m-d H:i:s', $recording_start_time / 1000);
                        $end_time = date('Y-m-d H:i:s', $recording_end_time / 1000);

                        $existing = $this->db->get_where('recordings', ['recording_id' => $recording_id])->row_array();
                        if (!$existing) {
                            $recording_data = [
                                'meeting_id' => $meeting['meeting_id'],
                                'appointment_id' => $meeting['appointment_id'],
                                'recording_id' => $recording_id,
                                'name' => $meeting['name'],
                                'class_id' => $meeting['class_id'],
                                'school_id' => $meeting['school_id'],
                                'start_time' => $start_time,
                                'end_time' => $end_time,
                                'duration' => $duration_seconds,
                                'formatted_duration' => $formatted_duration,
                                'recording_url' => $recording_url,
                                'created_at' => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s')
                            ];
                            $this->db->insert('recordings', $recording_data);
                        } else {
                            $this->db->where('recording_id', $recording_id);
                            $this->db->update('recordings', [
                                'recording_url' => $recording_url,
                                'updated_at' => date('Y-m-d H:i:s')
                            ]);
                        }
                    }
                }
            }

            $this->session->set_userdata('last_recording_sync', $current_time);
        }

        // Initialize filters
        $filters = [
            'meeting_name' => $this->input->post('meeting_name', true) ?? '',
            'date_range' => $this->input->post('date_range', true) ?? ''
        ];

        // Build query
        $this->db->select('r.*, c.name as class_name');
        $this->db->from('recordings r');
        $this->db->join('classes c', 'r.class_id = c.id', 'left');
        $this->db->where('r.school_id', $school_id);
        $this->db->where_in('r.class_id', $permitted_class_ids); // Permission check

        // Apply filters
        if (!empty($filters['meeting_name'])) {
            $this->db->like('r.name', $filters['meeting_name'], 'both');
        }
        if (!empty($filters['date_range'])) {
            $dates = explode(' - ', $filters['date_range']);
            if (count($dates) == 1) {
                $date = DateTime::createFromFormat('d-m-Y', trim($dates[0]));
                if ($date) {
                    $this->db->where('DATE(r.start_time)', $date->format('Y-m-d'));
                }
            } elseif (count($dates) == 2) {
                $date_from = DateTime::createFromFormat('d-m-Y', trim($dates[0]));
                $date_to = DateTime::createFromFormat('d-m-Y', trim($dates[1]));
                if ($date_from && $date_to) {
                    $this->db->where('r.start_time >=', $date_from->format('Y-m-d 00:00:00'));
                    $this->db->where('r.start_time <=', $date_to->format('Y-m-d 23:59:59'));
                }
            }
        }

        $this->db->order_by('r.created_at', 'DESC');
        $recordings = $this->db->get()->result_array();

        // Handle AJAX request
        if ($this->input->is_ajax_request()) {
            header('Content-Type: application/json');
            echo json_encode([
                'status' => 'success',
                'recordings' => $recordings,
                'filters' => $filters,
                'csrf_token' => $this->security->get_csrf_hash()
            ]);
            return;
        }

        // Load view for non-AJAX request
        $page_data['recordings'] = $recordings;
        $page_data['filters'] = $filters;
        $page_data['page_name'] = 'recording/recording';
        $page_data['page_title'] = 'recording';

        $this->load->view('backend/index', $page_data);
    }
		   
			public function delete_recording()
            {
                // Vérifier l'authentification
                if ($this->session->userdata('teacher_login') != 1) {
                    $response = [
                        'status' => 'error',
                        'message' => get_phrase('unauthorized_access'),
                        'csrf_token' => $this->security->get_csrf_hash()
                    ];
                    $this->output->set_content_type('application/json')->set_output(json_encode($response));
                    return;
                }

                // Get permitted classes for security check
                $user_id = $this->session->userdata('user_id');
                $teacher = $this->db->get_where('teachers', ['user_id' => $user_id])->row_array();
                if (!$teacher) {
                     $this->output->set_status_header(403);
                     return;
                }
                $teacher_id = $teacher['id'];

                $this->db->select('class_id');
                $this->db->from('teacher_permissions');
                $this->db->where('teacher_id', $teacher_id);
                $this->db->where('attendance', 1);
                $permitted_classes = $this->db->get()->result_array();
                $permitted_class_ids = array_column($permitted_classes, 'class_id');
                
                if (empty($permitted_class_ids)) {
                    $response = [
                        'status' => 'error',
                        'message' => get_phrase('unauthorized_access'),
                        'csrf_token' => $this->security->get_csrf_hash()
                    ];
                    $this->output->set_content_type('application/json')->set_output(json_encode($response));
                    return;
                }

                // Récupérer l'ID de l'enregistrement depuis la requête POST
                $recording_id = trim($this->input->post('recording_id', true));

                if (empty($recording_id)) {
                    $response = [
                        'status' => 'error',
                        'message' => get_phrase('invalid_recording_id'),
                        'csrf_token' => $this->security->get_csrf_hash()
                    ];
                    log_message('error', 'delete_recording - Invalid recording_id received');
                    $this->output->set_content_type('application/json')->set_output(json_encode($response));
                    return;
                }

                // Vérifier si l'enregistrement existe dans la table recordings
                $this->db->where('recording_id', $recording_id);
                $existing = $this->db->get('recordings')->row_array();

                if (!$existing) {
                    $response = [
                        'status' => 'error',
                        'message' => get_phrase('recording_not_found'),
                        'csrf_token' => $this->security->get_csrf_hash()
                    ];
                    log_message('error', 'delete_recording - Recording not found: recording_id=' . $recording_id);
                    $this->output->set_content_type('application/json')->set_output(json_encode($response));
                    return;
                }

                // Security Check: Is this recording from a permitted class?
                if (!in_array($existing['class_id'], $permitted_class_ids)) {
                     $response = [
                        'status' => 'error',
                        'message' => get_phrase('unauthorized_action_for_this_class'),
                        'csrf_token' => $this->security->get_csrf_hash()
                    ];
                    $this->output->set_content_type('application/json')->set_output(json_encode($response));
                    return;
                }

                // Charger la configuration BigBlueButton
                $this->load->config('bigbluebutton');
                $bbb_url = $this->config->item('bbb_url');
                $bbb_secret = $this->config->item('bbb_secret');

                // Supprimer l'enregistrement de BigBlueButton
                $params = "recordID=" . urlencode($recording_id);
                $checksum = sha1("deleteRecordings" . $params . $bbb_secret);
                $delete_url = $bbb_url . "deleteRecordings?" . $params . "&checksum=" . $checksum;

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $delete_url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                $delete_response = curl_exec($ch);
                $curl_error = curl_error($ch);
                curl_close($ch);

                if ($curl_error) {
                    log_message('error', 'delete_recording - cURL error while deleting from BBB: recording_id=' . $recording_id . ', Error: ' . $curl_error);
                    $response = [
                        'status' => 'error',
                        'message' => get_phrase('failed_to_delete_recording_from_bbb'),
                        'csrf_token' => $this->security->get_csrf_hash()
                    ];
                    $this->output->set_content_type('application/json')->set_output(json_encode($response));
                    return;
                }

                // Vérifier la réponse de l'API BBB
                $delete_xml = simplexml_load_string($delete_response);
                if (!$delete_xml || (string)$delete_xml->returncode !== "SUCCESS") {
                    log_message('error', 'delete_recording - BBB API delete failed: recording_id=' . $recording_id . ', Response: ' . $delete_response);
                    $response = [
                        'status' => 'error',
                        'message' => get_phrase('failed_to_delete_recording_from_bbb'),
                        'csrf_token' => $this->security->get_csrf_hash()
                    ];
                    $this->output->set_content_type('application/json')->set_output(json_encode($response));
                    return;
                }

                // Supprimer l'enregistrement de la table recordings
                $this->db->where('recording_id', $recording_id);
                $this->db->delete('recordings');
                $db_error = $this->db->error();


                if ($db_error['code'] == 0 && $this->db->affected_rows() > 0) {
                    $response = [
                        'status' => 'success',
                        'message' => get_phrase('recording_deleted_successfully'),
                        'csrf_token' => $this->security->get_csrf_hash()
                    ];
                } else {
                    log_message('error', 'delete_recording - Failed to delete recording: recording_id=' . $recording_id . ', Last Query: ' . $this->db->last_query() . ', DB Error: ' . json_encode($db_error));
                    $response = [
                        'status' => 'error',
                        'message' => get_phrase('database_error'),
                        'csrf_token' => $this->security->get_csrf_hash()
                    ];
                }
                $this->output->set_content_type('application/json')->set_output(json_encode($response));
            }
	
		  public function filter_recordings()
		  {
			  $this->load->config('bigbluebutton');
			  $bbbUrl = rtrim($this->config->item('bbb_url'), '/') . '/';
			  $bbbSecret = $this->config->item('bbb_secret');
		  
			  $meeting_name = trim($this->input->post('meeting_name', true));
			  $date_range = $this->input->post('date_range', true);
			  $schoolID = school_id();
		  
			  // Préparation de la requête principale
			  $this->db->select('
				  appointments.id,
				  appointments.title,
				  appointments.start_date AS start,
				  appointments.description,
				  appointments.sections_id AS section,
				  appointments.classe_id,
				  classes.name AS class_name,
				  appointments.room_id,
				  rooms.name AS room_name,
				  appointments.meeting_id
			  ');
			  $this->db->from('appointments');
			  $this->db->join('rooms', 'rooms.id = appointments.room_id', 'left');
			  $this->db->join('classes', 'classes.id = appointments.classe_id', 'left');
			  $this->db->where('appointments.Etat', 1);
			  $this->db->where('appointments.school_id', $schoolID);
		  
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
				  $appointment['recordings'] = [];
				  $meetingId = $appointment_rec['meeting_id'] ?? null;
		  
				  if ($meetingId) {
					  $params = ['meetingID' => $meetingId];
					  $query = http_build_query($params);
					  $checksum = sha1('getRecordings' . $query . $bbbSecret);
					  $url = $bbbUrl . 'getRecordings?' . $query . '&checksum=' . $checksum;
		  
					  $ch = curl_init($url);
					  curl_setopt_array($ch, [
						  CURLOPT_RETURNTRANSFER => 1,
						  CURLOPT_SSL_VERIFYPEER => false,
						  CURLOPT_SSL_VERIFYHOST => false,
					  ]);
					  $response = curl_exec($ch);
					  curl_close($ch);
		  
					  $xml = @simplexml_load_string($response);
					  if ($xml && $xml->returncode == 'SUCCESS') {
						  foreach ($xml->recordings->recording as $rec) {
							  $appointment_rec['recordings'][] = [
								  'meetingID' => (string)$rec->meetingID,
								  'playback_url' => (string)$rec->playback->format->url,
								  'duration' => (string)$rec->playback->format->length,
								  'video_download_url' => (string)$rec->playback->format->url,
								  'endTime' => (string)$rec->endTime,
							  ];
						  }
					  }
				  }
			  }
	   
			  // 🔽 Affichage HTML
			  foreach ($appointments as $appointment) {
				  // Traitement des sections
				  $section_label = '—';
				  if (!empty($appointment['section'])) {
					  $section_ids = explode(',', $appointment['section']);
					  $section_names = [];
					  foreach ($section_ids as $id) {
						  $name = $this->db->get_where('sections', ['id' => $id])->row('name');
						  if ($name) $section_names[] = $name;
					  }
					  $section_label = implode(', ', $section_names);
				  }
		  
				  // Affichage ligne
				  echo '<tr>';
				  echo '<td>' . htmlspecialchars($appointment['title']) . '</td>';
				  echo '<td>' . htmlspecialchars($appointment['room_name']) . '</td>';
				  echo '<td>' . htmlspecialchars($appointment['class_name']) . '</td>';
				  echo '<td>' . htmlspecialchars($section_label) . '</td>';
				  echo '<td>' . date('d-m-Y H:i', strtotime($appointment['start'])) . '</td>';
				  echo '<td>' . (!empty($appointment_rec['recordings']) ? $appointment_rec['recordings'][0]['duration'] : '—') . '</td>';
		  
				  echo '<td>';
				  if (!empty($appointment_rec['recordings'])) {
					  $rec = $appointment_rec['recordings'][0];
					  $endTime = !empty($rec['endTime']) ? (int)$rec['endTime'] / 1000 : null;
					  $isExpired = $endTime ? (time() > ($endTime + 7 * 24 * 3600)) : false;
		  
					  if ($isExpired) {
						  echo '<span class="badge bg-warning text-dark">Expired</span>';
					  } elseif (!empty($rec['playback_url'])) {
						  echo '<a href="' . htmlspecialchars($rec['playback_url']) . '" target="_blank" class="btn btn-sm btn-success">VIDEO</a>';
					  } else {
						  echo '<span class="badge bg-danger">NOT RECORDED</span>';
					  }
				  } else {
					  echo '<span class="badge bg-danger">NOT RECORDED</span>';
				  }
				  echo '</td>';
		  
				  echo '<td>';
				  if (!empty($appointment_rec['recordings'])) {
					  $rec = $appointment_rec['recordings'][0];
					  echo '<a href="' . htmlspecialchars($rec['video_download_url']) . '" class="btn btn-sm btn-success">Download</a> ';
				  }
		  
				  echo '<a href="' . site_url('superadmin/delete_appointment_and_recording/' . $appointment['id']) . '" class="btn btn-sm btn-danger" onclick="return confirm(\'❗ Cette action supprimera le rendez-vous et l’enregistrement associé. Continuer ?\')">🗑️ Supprimer</a>';
				  echo '</td></tr>';
			  }
		  }
		  
	
		  
		  public function export_recordings_csv() {
			  $this->load->config('bigbluebutton');
			  $bbbUrl = $this->config->item('bbb_url');
			  $bbbSecret = $this->config->item('bbb_secret');
			  $meeting_name = $this->input->get('meeting_name', true);
			  $date_range = $this->input->get('date_range', true);
			  $schoolID = school_id();
			  // $this->db->select('*')->from('appointments');
	
			  $this->db->select('
				  appointments.id,
				  appointments.title,
				  appointments.start_date AS start,
				  appointments.description,
				  appointments.sections_id AS section,
				  appointments.classe_id,
				  classes.name AS class_name,
				  appointments.room_id,
				  rooms.name AS room_name,
				  appointments.meeting_id
			  ');
			  $this->db->from('appointments');
			  $this->db->join('rooms', 'rooms.id = appointments.room_id', 'left');
			  $this->db->join('classes', 'classes.id = appointments.classe_id', 'left');
			  $this->db->where('appointments.Etat', 1);
			  $this->db->where('appointments.school_id', $schoolID);
			  // Filtrer par nom de réunion
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
			  
	
			  // Préparer le fichier CSV
			  header('Content-Type: text/csv');
			  header('Content-Disposition: attachment;filename="recordings.csv"');
	
	
	
			  $output = fopen('php://output', 'w');
			  fputcsv($output, ['Name', 'Room', 'Class', 'Section', 'Creation Date', 'Duration', 'Recording']);
	
			  foreach ($appointments as &$appointment_reco) {
				$meetingId = $appointment_reco['meeting_id'] ?? null;
				$appointment_reco['recordings'] = [];
		   
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
							$appointment_reco['recordings'][] = [
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
	
			  foreach ($appointments as $appointment) {
				  $recording_url = !empty($appointment_reco['recordings']) && isset($appointment_reco['recordings'][0]['playback_url']) 
					  ? $appointment_reco['recordings'][0]['playback_url'] 
					  : 'NOT RECORDED';
	
						 // Traitement des sections
				  $section_label = '—';
				  if (!empty($appointment['section'])) {
					  $section_ids = explode(',', $appointment['section']);
					  $section_names = [];
					  foreach ($section_ids as $id) {
						  $name = $this->db->get_where('sections', ['id' => $id])->row('name');
						  if ($name) $section_names[] = $name;
					  }
					  $section_label = implode(', ', $section_names);
				  }
			   
	
				  fputcsv($output, [
					  $appointment['title'],
					  $appointment['room_name'],
					  $appointment['class_name'],
					  $section_label,
					  date('d-m-Y H:i', strtotime($appointment['start'])),
					  !empty($appointment_reco['recordings']) && isset($appointment_reco['recordings'][0]['duration']) 
						  ? $appointment_reco['recordings'][0]['duration'] 
						  : '—',
					  $recording_url
				  ]);
			  }
	
			  fclose($output);
			  exit;
		  }
		
		  public function delete_appointment_and_recording($appointment_id)
		  {
		
		
			  if (empty($appointment_id)) {
				  $this->session->set_flashdata('error_message', "ID de l'appointment invalide.");
				  redirect(site_url('teacher/Recording'), 'refresh');
				  return;
			  }
	
			  // Appel à la méthode de suppression dans le modèle
			  $success = $this->room_model->delete_bbb_recording_by_appointment($appointment_id);
			  die($success);
			  // Vérification du succès de la suppression
			  if ($success) {
				  $this->session->set_flashdata('success_message', 'Appointment et enregistrements supprimés avec succès.');
			  } else {
				  $this->session->set_flashdata('error_message', "Erreur lors de la suppression de l'enregistrement ou de l'appointment.");
			  }
	
			  // Redirection vers la liste des enregistrements
			  redirect(site_url('teacher/Recording'), 'refresh');
		  }
	
		
		  
	
	//START STUDENT ADN ADMISSION section
	public function student($param1 = '', $param2 = '', $param3 = '', $param4 = '', $param5 = '')
  {
    $this->session->unset_session();
    $page_data['class_id'] = '';
   

    if ($param1 == 'create') {
      //form view
      if ($param2 == 'bulk') {
        $page_data['aria_expand'] = 'bulk';
        $page_data['working_page'] = 'create';
        $page_data['folder_name'] = 'student';
        $page_data['page_title'] = 'add_student';
        $this->load->view('backend/index', $page_data);
      } elseif ($param2 == 'excel') {
        $page_data['aria_expand'] = 'excel';
        $page_data['working_page'] = 'create';
        $page_data['folder_name'] = 'student';
        $page_data['page_title'] = 'add_student';
        $this->load->view('backend/index', $page_data);
      } else {
        $page_data['aria_expand'] = 'single';
        $page_data['working_page'] = 'create';
        $page_data['folder_name'] = 'student';
        $page_data['page_title'] = 'add_student';
        $this->load->view('backend/index', $page_data);
      }
    }

    //create to database
    if ($param1 == 'create_single_student') {

      if ($param2 == "submit") {
        header('Content-Type: application/json'); // Force le retour JSON
        $response_from_model = $this->user_model->single_student_create();
        $status = ($response_from_model === true); // Check if the model returned true for success
        //$this->session->set_flashdata('flash_message', get_phrase('student_added_successfully'));
        // Ajout du token CSRF à la réponse
        $response = [
          'status' => $status,
          'message' => $status ? get_phrase('student_added_successfully') : $this->session->flashdata('error'),
          'redirect' => site_url('teacher/student'),
          'csrf' => [
              'name' => $this->security->get_csrf_token_name(),
              'hash' => $this->security->get_csrf_hash()
          ]
      ];

      echo json_encode($response);
      exit;
    } else {
        // Load the view with filtered data
        $page_data['class_id'] = html_escape($this->input->post('class_id'));
      
        $page_data['working_page'] = 'filter';
        $page_data['folder_name'] = 'student';
        $page_data['page_title'] = 'student_list';

        $this->load->view('backend/index', $page_data);
    }
  }else {
    // Nouveau else ajouté pour la condition parente
    $this->session->set_flashdata('flash_message', get_phrase('welcome_back'));
  }

    if ($param1 == 'create_bulk_student') {
      $response = $this->user_model->bulk_student_create();
      // echo $response;
      // Préparer la réponse avec un nouveau jeton CSRF
      $csrf = array(
            'csrfName' => $this->security->get_csrf_token_name(),
              'csrfHash' => $this->security->get_csrf_hash(),
            );
            
      // Renvoyer la réponse avec un nouveau jeton CSRF
      echo json_encode(array('status' => $response, 'csrf' => $csrf));
    }

    if ($param1 == 'create_excel') {
      $response = $this->user_model->excel_create();
      // die($response) ;
      // Préparer la réponse avec un nouveau jeton CSRF
      $csrf = array(
            'csrfName' => $this->security->get_csrf_token_name(),
            'csrfHash' => $this->security->get_csrf_hash(),
            );
                
      // Renvoyer la réponse avec un nouveau jeton CSRF
      echo json_encode(array('status' => $response, 'csrf' => $csrf));
    }

   // form view
   if ($param1 == 'edit') {
    $page_data['student_id'] = $param2;
    $page_data['working_page'] = 'edit';
    $page_data['folder_name'] = 'student';
    $page_data['page_title'] = 'update_student_information';
    $this->load->view('backend/index', $page_data);
  }


    if ($param1 == 'status') {
      $this->db->where('id', $param3);
      $this->db->update('users', array('status' => $param4));
      
        // Préparer la réponse avec un nouveau jeton CSRF
        $csrf = array(
                  'csrfName' => $this->security->get_csrf_token_name(),
                  'csrfHash' => $this->security->get_csrf_hash(),
                );
      
      header('Content-Type: application/json');
      // Renvoyer la réponse avec un nouveau jeton CSRF
      echo json_encode(array(
          'status' => true, 
          'notification' => get_phrase('status_has_been_updated'),
          'csrf' => $csrf
      ));
      exit;
    }

    //updated to database
    if ($param1 == 'updated') {
      $response = $this->user_model->student_update($param2, $param3);
      // echo $response;
         // Préparer la réponse avec un nouveau jeton CSRF
         $csrf = array(
          'csrfName' => $this->security->get_csrf_token_name(),
          'csrfHash' => $this->security->get_csrf_hash(),
      );
  
      // Renvoyer la réponse avec un nouveau jeton CSRF
       echo json_encode(array('status' => $response, 'csrf' => $csrf));
    }
    //updated to database
    if ($param1 == 'id_card') {
      $page_data['student_id'] = $param2;
      $page_data['folder_name'] = 'student';
      $page_data['page_title'] = 'identity_card';
      $page_data['page_name'] = 'id_card';
      $this->load->view('backend/index', $page_data);
    }

    if ($param1 == 'delete') {
      $response = $this->user_model->delete_student($param2, $param3);
      // echo $response;
       // Préparer la réponse avec un nouveau jeton CSRF
      $csrf = array(
        'csrfName' => $this->security->get_csrf_token_name(),
        'csrfHash' => $this->security->get_csrf_hash(),
    );

    header('Content-Type: application/json');
    // Renvoyer la réponse avec un nouveau jeton CSRF
     echo json_encode(array(
         'status' => $response, 
         'notification' => get_phrase('student_deleted_successfully'),
         'csrf' => $csrf
     ));
     exit;
    }

    if ($param1 == 'filter') {
            $page_data['class_id'] = ($param2 == '' || $param2 == 'all') ? 'all' : $param2;
          
            $html_content = $this->load->view('backend/teacher/student/list', $page_data, TRUE);
            $csrf = array(
                'csrfName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
            );
            echo json_encode(array('html' => $html_content, 'csrf' => $csrf));
        }

        if (empty($param1)) {
            $page_data['class_id'] = 'all';
          
            $page_data['working_page'] = 'filter';
            $page_data['folder_name'] = 'student';
            $page_data['page_title'] = 'student_list';
            $this->load->view('backend/index', $page_data);
        }
  }

	public function teacher($param1 = '', $param2 = '', $param3 = ''){


		if($param1 == 'create'){
			$response = $this->user_model->create_teacher();
			// echo $response;
			// Préparer la réponse avec un nouveau jeton CSRF
			$csrf = array(
				'csrfName' => $this->security->get_csrf_token_name(),
				'csrfHash' => $this->security->get_csrf_hash(),
			);
		
			// Renvoyer la réponse avec un nouveau jeton CSRF
			echo json_encode(array('status' => $response, 'csrf' => $csrf));
		}

		if($param1 == 'update'){
			$response = $this->user_model->update_teacher($param2);
			// echo $response;
			// Préparer la réponse avec un nouveau jeton CSRF
			$csrf = array(
				'csrfName' => $this->security->get_csrf_token_name(),
				'csrfHash' => $this->security->get_csrf_hash(),
			);
		
			// Renvoyer la réponse avec un nouveau jeton CSRF
			echo json_encode(array('status' => $response, 'csrf' => $csrf));
		}

		if($param1 == 'delete'){
			$teacher_id = $this->db->get_where('teachers', array('user_id' => $param2))->row('id');
			$response = $this->user_model->delete_teacher($param2, $teacher_id);
			// echo $response;
			// Préparer la réponse avec un nouveau jeton CSRF
			$csrf = array(
				'csrfName' => $this->security->get_csrf_token_name(),
				'csrfHash' => $this->security->get_csrf_hash(),
			);
		
			// Renvoyer la réponse avec un nouveau jeton CSRF
			echo json_encode(array('status' => $response, 'csrf' => $csrf));
		}

		if ($param1 == 'list') {
			$this->load->view('backend/teacher/teacher/list');
		}

		if(empty($param1)){
			$page_data['folder_name'] = 'teacher';
			$page_data['page_title'] = 'techers';
			$this->load->view('backend/index', $page_data);
		}
	}
	//END TEACHER section

	//START CLASS secion
	public function manage_class($param1 = '', $param2 = '', $param3 = ''){

		if($param1 == 'create'){
			$response = $this->crud_model->class_create();
			// echo $response;
			// Préparer la réponse avec un nouveau jeton CSRF
			$csrf = array(
			'csrfName' => $this->security->get_csrf_token_name(),
			'csrfHash' => $this->security->get_csrf_hash(),
				);
	
			// Renvoyer la réponse avec un nouveau jeton CSRF
			echo json_encode(array('status' => $response, 'csrf' => $csrf));
		}

		if($param1 == 'delete'){
			$response = $this->crud_model->class_delete($param2);
			// echo $response;
			       // Préparer la réponse avec un nouveau jeton CSRF
				   $csrf = array(
					'csrfName' => $this->security->get_csrf_token_name(),
					'csrfHash' => $this->security->get_csrf_hash(),
				);
			
				// Renvoyer la réponse avec un nouveau jeton CSRF
				echo json_encode(array('status' => $response, 'csrf' => $csrf));
		}

		if($param1 == 'update'){
			$response = $this->crud_model->class_update($param2);
			// echo $response;
			       // Préparer la réponse avec un nouveau jeton CSRF
				   $csrf = array(
					'csrfName' => $this->security->get_csrf_token_name(),
					'csrfHash' => $this->security->get_csrf_hash(),
				);
			
				// Renvoyer la réponse avec un nouveau jeton CSRF
				echo json_encode(array('status' => $response, 'csrf' => $csrf));
		}


		// show data from database
		if ($param1 == 'list') {
			$this->load->view('backend/teacher/class/list');
		}

		if(empty($param1)){
			$page_data['folder_name'] = 'class';
			$page_data['page_title'] = 'class';
			$this->load->view('backend/index', $page_data);
		}
	}
	//END CLASS section



	//START SUBJECT section
	
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
      $this->load->view('backend/teacher/language/dropdown');
    }
    // showing the index file
    if (empty($param1)) {
      $page_data['folder_name'] = 'language';
      $page_data['page_title'] = 'languages';
      $this->load->view('backend/index', $page_data);
    }
  }
	public function class_wise_subject($class_id) {

		// PROVIDE A LIST OF SUBJECT ACCORDING TO CLASS ID
		$page_data['class_id'] = $class_id;
		$this->load->view('backend/teacher/subject/dropdown', $page_data);
	}
	//END SUBJECT section

	//START SYLLABUS section
	public function syllabus($param1 = '', $param2 = '', $param3 = ''){

		if($param1 == 'create'){
			$modelResponse = $this->crud_model->syllabus_create();
			// Préparer la réponse avec un nouveau jeton CSRF
			$csrf = array(
			  'name' => $this->security->get_csrf_token_name(),
			  'hash' => $this->security->get_csrf_hash()
		  );
		  
		  // Fusionner la réponse du modèle avec le CSRF
		  $response = array(
			  'status' => $modelResponse['status'],
			  'notification' => $modelResponse['notification'],
			  'csrf' => $csrf
		  );
		  
		  echo json_encode($response);
		}

		if($param1 == 'delete'){
			$response = $this->crud_model->syllabus_delete($param2);
			// echo $response;
			       // Préparer la réponse avec un nouveau jeton CSRF
				   $csrf = array(
					'csrfName' => $this->security->get_csrf_token_name(),
					'csrfHash' => $this->security->get_csrf_hash(),
				);
			
				// Renvoyer la réponse avec un nouveau jeton CSRF
				echo json_encode(array('status' => $response, 'csrf' => $csrf));
		}

		if($param1 == 'list'){
			$page_data['class_id'] = $param2;

			$this->load->view('backend/teacher/syllabus/list', $page_data);
		}

		if(empty($param1)){
			$page_data['folder_name'] = 'syllabus';
			$page_data['page_title'] = 'syllabus';
			$this->load->view('backend/index', $page_data);
		}
	}
	//END SYLLABUS section

	//START CLASS ROUTINE section
	public function routine($param1 = '', $param2 = '', $param3 = '', $param4 = ''){

		if($param1 == 'create'){
			$response = $this->crud_model->routine_create();
			// echo $response;
			// Préparer la réponse avec un nouveau jeton CSRF
			$csrf = array(
				'csrfName' => $this->security->get_csrf_token_name(),
				'csrfHash' => $this->security->get_csrf_hash(),
			);
		
			// Renvoyer la réponse avec un nouveau jeton CSRF
			echo json_encode(array('status' => $response, 'csrf' => $csrf));
		}

		if($param1 == 'update'){
			$response = $this->crud_model->routine_update($param2);
			// echo $response;
			// Préparer la réponse avec un nouveau jeton CSRF
			$csrf = array(
				'csrfName' => $this->security->get_csrf_token_name(),
				'csrfHash' => $this->security->get_csrf_hash(),
			);
		
			// Renvoyer la réponse avec un nouveau jeton CSRF
			echo json_encode(array('status' => $response, 'csrf' => $csrf));
		}

		if($param1 == 'delete'){
			$response = $this->crud_model->routine_delete($param2);
			// echo $response;
			// Préparer la réponse avec un nouveau jeton CSRF
			$csrf = array(
				'csrfName' => $this->security->get_csrf_token_name(),
				'csrfHash' => $this->security->get_csrf_hash(),
			);
		
			// Renvoyer la réponse avec un nouveau jeton CSRF
			echo json_encode(array('status' => $response, 'csrf' => $csrf));
		}

		if($param1 == 'filter'){
			$page_data['class_id'] = $param2;
			$this->load->view('backend/teacher/routine/list', $page_data);
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

		if($param1 == 'take_attendance'){
			$modelResponse = $this->crud_model->take_attendance();
			// Préparer la réponse avec un nouveau jeton CSRF
			$csrf = array(
			  'name' => $this->security->get_csrf_token_name(),
			  'hash' => $this->security->get_csrf_hash()
		  );
		  
		  // Fusionner la réponse du modèle avec le CSRF
		  $response = array(
			  'status' => $modelResponse['status'],
			  'notification' => $modelResponse['notification'],
			  'csrf' => $csrf
		  );
		  
		  echo json_encode($response);
		}

		if($param1 == 'filter'){
			$date = '01 '.$this->input->post('month').' '.$this->input->post('year');
			$page_data['attendance_date'] = strtotime($date);
			$page_data['class_id'] = htmlspecialchars($this->input->post('class_id'));
			$page_data['month'] = htmlspecialchars($this->input->post('month'));
			$page_data['year'] = htmlspecialchars($this->input->post('year'));
			// $this->load->view('backend/teacher/attendance/list', $page_data);

			// Charger la vue mise à jour
			$response_html = $this->load->view('backend/teacher/attendance/list', $page_data, TRUE);
			// Préparer le nouveau jeton CSRF
			$csrf = array(
			'csrfName' => $this->security->get_csrf_token_name(),
			'csrfHash' => $this->security->get_csrf_hash(),
				);
	
			// Renvoyer la réponse JSON avec le HTML mis à jour et le nouveau jeton CSRF
			echo json_encode(array('status' => $response_html, 'csrfName' => $csrf['csrfName'], 'csrfHash' => $csrf['csrfHash']));
		}

		if($param1 == 'student'){
			$page_data['attendance_date'] = strtotime($this->input->post('date'));
			$page_data['class_id'] = htmlspecialchars($this->input->post('class_id'));
			// $this->load->view('backend/teacher/attendance/student', $page_data);
			      // Charger la vue mise à jour
			$response_html = $this->load->view('backend/teacher/attendance/student', $page_data, TRUE);
			// Préparer le nouveau jeton CSRF
			$csrf = array(
			'csrfName' => $this->security->get_csrf_token_name(),
			'csrfHash' => $this->security->get_csrf_hash(),
			);
	
		// Renvoyer la réponse JSON avec le HTML mis à jour et le nouveau jeton CSRF
				echo json_encode(array('status' => $response_html, 'csrfName' => $csrf['csrfName'], 'csrfHash' => $csrf['csrfHash']));
		}

		if(empty($param1)){
			$page_data['folder_name'] = 'attendance';
			$page_data['page_title'] = 'attendance';
			$this->load->view('backend/index', $page_data);
		}
	}
	//END DAILY ATTENDANCE section


	//START EVENT CALENDAR section
	public function event_calendar($param1 = '', $param2 = ''){

		if($param1 == 'create'){
			$response = $this->crud_model->event_calendar_create();
			// echo $response;
                  // Préparer la réponse avec un nouveau jeton CSRF
				  $csrf = array(
					'csrfName' => $this->security->get_csrf_token_name(),
					'csrfHash' => $this->security->get_csrf_hash(),
				);
			
				// Renvoyer la réponse avec un nouveau jeton CSRF
				echo json_encode(array('status' => $response, 'csrf' => $csrf));
		}

		if($param1 == 'update'){
			$response = $this->crud_model->event_calendar_update($param2);
			// echo $response;
				// Préparer la réponse avec un nouveau jeton CSRF
				$csrf = array(
				'csrfName' => $this->security->get_csrf_token_name(),
				'csrfHash' => $this->security->get_csrf_hash(),
			);
		
			// Renvoyer la réponse avec un nouveau jeton CSRF
			echo json_encode(array('status' => $response, 'csrf' => $csrf));
		}

		if($param1 == 'delete'){
			$response = $this->crud_model->event_calendar_delete($param2);
			// echo $response;
                  // Préparer la réponse avec un nouveau jeton CSRF
				  $csrf = array(
					'csrfName' => $this->security->get_csrf_token_name(),
					'csrfHash' => $this->security->get_csrf_hash(),
				);
			
				// Renvoyer la réponse avec un nouveau jeton CSRF
				echo json_encode(array('status' => $response, 'csrf' => $csrf));
		}

		if($param1 == 'all_events'){
			echo $this->crud_model->all_events();
		}

		if ($param1 == 'list') {
			$this->load->view('backend/teacher/event_calendar/list');
		}

		if(empty($param1)){
			$page_data['folder_name'] = 'event_calendar';
			$page_data['page_title'] = 'event_calendar';
			$this->load->view('backend/index', $page_data);
		}
	}
	//END EVENT CALENDAR section


	//START EXAM section
	public function exam($param1 = '', $param2 = '')
{
    // Get teacher ID and permitted classes for permission checking
    $user_id = $this->session->userdata('user_id');
    $teacher = $this->db->get_where('teachers', ['user_id' => $user_id])->row_array();
    $teacher_id = $teacher['id'] ?? null;
    
    // Get permitted class IDs from teacher_permissions where attendance = 1
    $permitted_class_ids = [];
    if ($teacher_id) {
        $this->db->select('class_id');
        $this->db->from('teacher_permissions');
        $this->db->where('teacher_id', $teacher_id);
        $this->db->where('attendance', 1);
        $permitted_classes = $this->db->get()->result_array();
        $permitted_class_ids = array_column($permitted_classes, 'class_id');
    }
    
    if ($param1 == 'create') {
        // Check if teacher has permission for the selected class
        $class_id = $this->input->post('class_id') ?: '';
        
        if (!empty($class_id) && !empty($permitted_class_ids)) {
            if (!in_array($class_id, $permitted_class_ids)) {
                // Teacher doesn't have permission for this class
                $output = array(
                    'status' => false,
                    'message' => get_phrase('you_do_not_have_permission_for_this_class'),
                    'csrf' => array(
                        'csrfName' => $this->security->get_csrf_token_name(),
                        'csrfHash' => $this->security->get_csrf_hash(),
                    )
                );
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode($output));
                return;
            }
        } elseif (empty($permitted_class_ids)) {
            // Teacher has no permitted classes at all
            $output = array(
                'status' => false,
                'message' => get_phrase('you_do_not_have_permission_for_any_classes'),
                'csrf' => array(
                    'csrfName' => $this->security->get_csrf_token_name(),
                    'csrfHash' => $this->security->get_csrf_hash(),
                )
            );
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode($output));
            return;
        }
        
        $response = $this->crud_model->exam_create();
        $response_data = json_decode($response, true);
        
        // Ajouter un nouveau jeton CSRF
        $csrf = array(
            'csrfName' => $this->security->get_csrf_token_name(),
            'csrfHash' => $this->security->get_csrf_hash(),
        );
        
        // Construire la réponse
        $output = array(
            'status' => $response_data['status'] ?? false,
            'message' => $response_data['notification'] ?? 'Failed to create exam',
            'class_id' => $class_id, // Inclure l'ID de la classe pour le frontend
            'csrf' => $csrf
        );
        
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($output));
    }


    if ($param1 == 'update') {
        // First check if teacher has permission to update this exam
        $exam = $this->db->get_where('exams', array('id' => $param2))->row_array();
        
        if (!$exam) {
            $output = array(
                'status' => false,
                'message' => 'Exam not found',
                'csrf' => array(
                    'csrfName' => $this->security->get_csrf_token_name(),
                    'csrfHash' => $this->security->get_csrf_hash(),
                )
            );
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode($output));
            return;
        }
        
        // Check if teacher has permission for the exam's current class
        if (!empty($permitted_class_ids)) {
            if (!in_array($exam['class_id'], $permitted_class_ids)) {
                // Teacher doesn't have permission for this exam's class
                $output = array(
                    'status' => false,
                    'message' => get_phrase('you_do_not_have_permission_for_this_exam'),
                    'csrf' => array(
                        'csrfName' => $this->security->get_csrf_token_name(),
                        'csrfHash' => $this->security->get_csrf_hash(),
                    )
                );
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode($output));
                return;
            }
        } else {
            // Teacher has no permitted classes at all
            $output = array(
                'status' => false,
                'message' => get_phrase('you_do_not_have_permission_for_any_classes'),
                'csrf' => array(
                    'csrfName' => $this->security->get_csrf_token_name(),
                    'csrfHash' => $this->security->get_csrf_hash(),
                )
            );
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode($output));
            return;
        }
        
        // Also check if trying to change to a class without permission
        $new_class_id = $this->input->post('class_id') ?: $exam['class_id'];
        if ($new_class_id != $exam['class_id'] && !empty($permitted_class_ids)) {
            if (!in_array($new_class_id, $permitted_class_ids)) {
                // Teacher doesn't have permission for the new class
                $output = array(
                    'status' => false,
                    'message' => get_phrase('you_do_not_have_permission_for_the_selected_class'),
                    'csrf' => array(
                        'csrfName' => $this->security->get_csrf_token_name(),
                        'csrfHash' => $this->security->get_csrf_hash(),
                    )
                );
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode($output));
                return;
            }
        }
        
        $response = $this->crud_model->exam_update($param2);
        // Vérifier si la réponse est déjà encodée en JSON et la décoder
        if (is_string($response) && (json_decode($response) !== null)) {
            $response = json_decode($response, true); // Convertir en tableau
        }
        
        $class_id = $exam['class_id'] ?? ''; // Récupérer l'ID de la classe de l'examen
        
        if ($exam) {
            $exam['formatted_date'] = date('D, d-M-Y H:i', $exam['starting_date']);
            $class = $this->db->get_where('classes', array('id' => $exam['class_id']))->row_array();
           
            $exam['class_name'] = $class ? $class['name'] : 'No Class';
           
            $output = array(
                'status' => $response['status'] ?? false,
                'exam' => $exam,
                'class_id' => $class_id, // Inclure l'ID de la classe
                'message' => $response['notification'] ?? 'Failed to update exam',
                'csrf' => array(
                    'csrfName' => $this->security->get_csrf_token_name(),
                    'csrfHash' => $this->security->get_csrf_hash(),
                )
            );
        } else {
            $output = array(
                'status' => false,
                'message' => 'Exam not found',
                'csrf' => array(
                    'csrfName' => $this->security->get_csrf_token_name(),
                    'csrfHash' => $this->security->get_csrf_hash(),
                )
            );
        }
        
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($output));
    }

    if ($param1 == 'delete') {
        // First check if teacher has permission to delete this exam
        $exam = $this->db->get_where('exams', array('id' => $param2))->row_array();
        
        if (!$exam) {
            $output = array(
                'status' => false,
                'message' => 'Exam not found',
                'csrf' => array(
                    'csrfName' => $this->security->get_csrf_token_name(),
                    'csrfHash' => $this->security->get_csrf_hash(),
                )
            );
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode($output));
            return;
        }
        
        // Check if teacher has permission for the exam's class
        if (!empty($permitted_class_ids)) {
            if (!in_array($exam['class_id'], $permitted_class_ids)) {
                // Teacher doesn't have permission for this exam's class
                $output = array(
                    'status' => false,
                    'message' => get_phrase('you_do_not_have_permission_to_delete_this_exam'),
                    'csrf' => array(
                        'csrfName' => $this->security->get_csrf_token_name(),
                        'csrfHash' => $this->security->get_csrf_hash(),
                    )
                );
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode($output));
                return;
            }
        } else {
            // Teacher has no permitted classes at all
            $output = array(
                'status' => false,
                'message' => get_phrase('you_do_not_have_permission_for_any_classes'),
                'csrf' => array(
                    'csrfName' => $this->security->get_csrf_token_name(),
                    'csrfHash' => $this->security->get_csrf_hash(),
                )
            );
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode($output));
            return;
        }
        
        $response = $this->crud_model->exam_delete($param2);
        $this->output
            ->set_content_type('application/json')
            ->set_output($response);
    }

    if ($param1 == 'list') {
        $this->load->view('backend/teacher/exam/list');
    }

    if (empty($param1)) {
        $page_data['folder_name'] = 'exam';
        $page_data['page_title'] = 'Certifications';
        $this->load->view('backend/index', $page_data);
    }
}
	//END EXAM section
	public function filter_exams()
{
    header('Content-Type: application/json');
    
    if ($this->session->userdata('teacher_login') != 1) {
        echo json_encode(['error' => 'Unauthorized']);
        return;
    }

    $school_id = school_id();
    $session = active_session();

    // Get teacher ID
    $user_id = $this->session->userdata('user_id');
    $teacher = $this->db->get_where('teachers', ['user_id' => $user_id])->row_array();
    $teacher_id = $teacher['id'] ?? null;
    
    if (!$teacher_id) {
        echo json_encode(['error' => 'No teacher associated with this user']);
        return;
    }
    
    // Get permitted class IDs from teacher_permissions where attendance = 1
    $this->db->select('class_id');
    $this->db->from('teacher_permissions');
    $this->db->where('teacher_id', $teacher_id);
    $this->db->where('attendance', 1);
    $permitted_classes = $this->db->get()->result_array();
    $permitted_class_ids = array_column($permitted_classes, 'class_id');

    $class_id = $this->input->post('class_id');

    $date_range = $this->input->post('date_range');
    $date_from = '';
    $date_to = '';

    if (!empty($date_range)) {
        $dates = explode(' - ', $date_range);
        $date_from = strtotime(trim($dates[0]) . ' 00:00:00');
        $date_to = strtotime(trim($dates[1]) . ' 23:59:59');
    }

    $this->db->select('exams.*, classes.name as class_name');
    $this->db->from('exams');
    $this->db->join('classes', 'exams.class_id = classes.id', 'left');
    $this->db->where('exams.school_id', $school_id);
    $this->db->where('exams.session', $session);
    
    // Filter by permitted classes only
    if (!empty($permitted_class_ids)) {
        $this->db->where_in('exams.class_id', $permitted_class_ids);
    } else {
        // If teacher has no permitted classes, return empty results
        $this->db->where('1', '0');
    }
    
    if (!empty($class_id)) {
        $this->db->where('exams.class_id', $class_id);
    }
 
    if (!empty($date_range)) {
        $this->db->where('exams.starting_date >=', $date_from);
        $this->db->where('exams.starting_date <=', $date_to);
    }
    $this->db->order_by('exams.starting_date', 'DESC');
    $exams = $this->db->get()->result_array();

    $exam_data = [];
    foreach ($exams as $exam) {
        $exam_data[] = [
            'id' => $exam['id'],
            'name' => $exam['name'] ?: 'Unnamed Exam',
            'formatted_date' => $exam['starting_date'] ? date('D, d-M-Y H:i', $exam['starting_date']) : 'No Date',
            'class_name' => $exam['class_name'] ?: 'No Class'
           
        ];
    }

    $exam_calendar = [];
    foreach ($exams as $exam) {
        if ($exam['starting_date']) {
            $exam_calendar[] = [
                'title' => $exam['name'] ?: 'Unnamed Exam',
                'start' => date('Y-m-d H:i:s', $exam['starting_date'])
            ];
        }
    }

    echo json_encode([
        'exams' => $exam_data,
        'calendar' => $exam_calendar,
        'debug' => [
            'class_id' => $class_id,
            'date_range' => $date_range,
            'exam_count' => count($exams)
        ]
    ]);
}

// Paginated exams for AJAX loading
public function get_exams_paginated()
{
    header('Content-Type: application/json');
    
    if ($this->session->userdata('teacher_login') != 1) {
        echo json_encode(['status' => false, 'error' => 'Unauthorized']);
        return;
    }

    $school_id = school_id();
    $session = active_session();
    
    // Get teacher ID
    $user_id = $this->session->userdata('user_id');
    $teacher = $this->db->get_where('teachers', ['user_id' => $user_id])->row_array();
    $teacher_id = $teacher['id'] ?? null;
    
    if (!$teacher_id) {
        echo json_encode(['status' => false, 'error' => 'No teacher associated with this user']);
        return;
    }
    
    // Get permitted class IDs from teacher_permissions where attendance = 1
    $this->db->select('class_id');
    $this->db->from('teacher_permissions');
    $this->db->where('teacher_id', $teacher_id);
    $this->db->where('attendance', 1);
    $permitted_classes = $this->db->get()->result_array();
    $permitted_class_ids = array_column($permitted_classes, 'class_id');
    
    $page = (int) $this->input->get('page') ?: 1;
    $limit = 10; // Exams per page
    $offset = ($page - 1) * $limit;
    
    // Get filters if any
    $class_id = $this->input->get('class_id');
    $date_range = $this->input->get('date_range');
    $date_from = '';
    $date_to = '';

    if (!empty($date_range)) {
        $dates = explode(' - ', $date_range);
        if (count($dates) == 2) {
            $date_from = strtotime(trim($dates[0]) . ' 00:00:00');
            $date_to = strtotime(trim($dates[1]) . ' 23:59:59');
        }
    }

    // Count total exams with filters
    $this->db->from('exams');
    $this->db->where('school_id', $school_id);
    $this->db->where('session', $session);
    
    // Filter by permitted classes only
    if (!empty($permitted_class_ids)) {
        $this->db->where_in('class_id', $permitted_class_ids);
    } else {
        // If teacher has no permitted classes, return empty results
        $this->db->where('1', '0');
    }
    
    if (!empty($class_id)) {
        $this->db->where('class_id', $class_id);
    }
    if (!empty($date_range) && $date_from && $date_to) {
        $this->db->where('starting_date >=', $date_from);
        $this->db->where('starting_date <=', $date_to);
    }
    $total_exams = $this->db->count_all_results();
    $total_pages = ceil($total_exams / $limit);

    // Get paginated exams
    $this->db->select('exams.*, classes.name as class_name');
    $this->db->from('exams');
    $this->db->join('classes', 'exams.class_id = classes.id', 'left');
    $this->db->where('exams.school_id', $school_id);
    $this->db->where('exams.session', $session);
    
    // Filter by permitted classes only
    if (!empty($permitted_class_ids)) {
        $this->db->where_in('exams.class_id', $permitted_class_ids);
    } else {
        // If teacher has no permitted classes, return empty results
        $this->db->where('1', '0');
    }
    
    if (!empty($class_id)) {
        $this->db->where('exams.class_id', $class_id);
    }
    if (!empty($date_range) && $date_from && $date_to) {
        $this->db->where('exams.starting_date >=', $date_from);
        $this->db->where('exams.starting_date <=', $date_to);
    }
    $this->db->order_by('exams.id', 'DESC');
    $this->db->limit($limit, $offset);
    $exams = $this->db->get()->result_array();

    $exam_data = [];
    foreach ($exams as $exam) {
        $exam_data[] = [
            'id' => $exam['id'],
            'name' => $exam['name'] ?: 'Unnamed Exam',
            'formatted_date' => $exam['starting_date'] ? date('D, d-M-Y H:i', $exam['starting_date']) : 'No Date',
            'class_name' => $exam['class_name'] ?: 'No Class'
        ];
    }

    echo json_encode([
        'status' => true,
        'exams' => $exam_data,
        'current_page' => $page,
        'total_pages' => $total_pages,
        'total_exams' => $total_exams,
        'csrf' => [
            'csrfName' => $this->security->get_csrf_token_name(),
            'csrfHash' => $this->security->get_csrf_hash()
        ]
    ]);
}

	//START MARKS section
	public function mark($param1 = '', $param2 = ''){

		if($param1 == 'list'){
			$page_data['class_id'] = htmlspecialchars($this->input->post('class_id'));
			// $page_data['subject_id'] = htmlspecialchars($this->input->post('subject'));
			$page_data['exam_id'] = htmlspecialchars($this->input->post('exam'));
			$this->crud_model->mark_insert($page_data['class_id'], $page_data['exam_id']);
			// $this->load->view('backend/teacher/mark/list', $page_data);
			// Charger la vue et capturer le contenu
			$html_content = $this->load->view('backend/teacher/mark/list', $page_data, TRUE);
				
			// Préparer le nouveau jeton CSRF
			$csrf = array(
				'csrfName' => $this->security->get_csrf_token_name(),
				'csrfHash' => $this->security->get_csrf_hash(),
			);
			
			// Renvoyer la réponse JSON avec le HTML et le nouveau jeton CSRF
			echo json_encode(array('html' => $html_content, 'csrf' => $csrf));
		}

		if($param1 == 'mark_update'){
			$this->crud_model->mark_update();
				// Préparer le nouveau jeton CSRF
				$csrf = array(
				'csrfName' => $this->security->get_csrf_token_name(),
				'csrfHash' => $this->security->get_csrf_hash(),
			);
			
			// Renvoyer la réponse JSON avec le nouveau jeton CSRF
		
			echo json_encode(array('csrf' => $csrf));
		}

		if(empty($param1)){
			$page_data['folder_name'] = 'mark';
			$page_data['page_title'] = 'marks';
			$this->load->view('backend/index', $page_data);
		}
	}

	// GET THE GRADE ACCORDING TO MARK
	public function get_grade($acquired_mark) {
		echo get_grade($acquired_mark);
	}
	//END MARKS sesction


	// BACKOFFICE SECTION

	//BOOK LIST MANAGER
	public function book($param1 = "", $param2 = "") {
		// adding book
		if ($param1 == 'create') {
			$response = $this->crud_model->create_book();
			// echo $response;
			// Préparer la réponse avec un nouveau jeton CSRF
			$csrf = array(
			'csrfName' => $this->security->get_csrf_token_name(),
			'csrfHash' => $this->security->get_csrf_hash(),
			);
	
			// Renvoyer la réponse avec un nouveau jeton CSRF
			echo json_encode(array('status' => $response, 'csrf' => $csrf));
		}

		// update book
		if ($param1 == 'update') {
			$response = $this->crud_model->update_book($param2);
			// echo $response;
			// Préparer la réponse avec un nouveau jeton CSRF
			$csrf = array(
				'csrfName' => $this->security->get_csrf_token_name(),
				'csrfHash' => $this->security->get_csrf_hash(),
				);
		
				// Renvoyer la réponse avec un nouveau jeton CSRF
				echo json_encode(array('status' => $response, 'csrf' => $csrf));
		}

		// deleting book
		if ($param1 == 'delete') {
			$response = $this->crud_model->delete_book($param2);
			// echo $response;
			// Préparer la réponse avec un nouveau jeton CSRF
			$csrf = array(
				'csrfName' => $this->security->get_csrf_token_name(),
				'csrfHash' => $this->security->get_csrf_hash(),
				);
		
				// Renvoyer la réponse avec un nouveau jeton CSRF
				echo json_encode(array('status' => $response, 'csrf' => $csrf));
		}
		// showing the list of book
		if ($param1 == 'list') {
			$this->load->view('backend/teacher/book/list');
		}

		// showing the index file
		if(empty($param1)){
			$page_data['folder_name'] = 'book';
			$page_data['page_title']  = 'books';
			$this->load->view('backend/index', $page_data);
		}
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

	public function calendar($param1 = '', $param2 = '', $param3 = '', $param4 = '') {
    if ($this->session->userdata('teacher_login') != 1) {
        $csrf = [
            'csrfName' => $this->security->get_csrf_token_name(),
            'csrfHash' => $this->security->get_csrf_hash(),
        ];
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized', 'csrf' => $csrf]);
        return;
    }

    if ($param1 == 'create') {
        $this->create_event();
    }

    if ($param1 == 'update') {
        $this->update_event();
    }

    if ($param1 == 'delete') {
        $this->delete_event();
    }

    if ($param1 == 'get_events') {
        $this->get_events();
    }

    if ($param1 == 'get_user_school') {
        $this->get_user_school();
    }

    if ($param1 == 'get_school_data') {
        $this->get_school_data();
    }

    if ($param1 == 'start_meeting') {
        $this->start_meeting();
    }

    if ($param1 == 'filter') {
        $page_data['class_id'] = $param2;
        $page_data['start_date'] = $param3;
        $page_data['end_date'] = $param4;
        $this->load->view('backend/teacher/calendar/list', $page_data);
    }

    if (empty($param1)) {
        $page_data['folder_name'] = 'calendar';
        $page_data['page_title'] = 'calendar';
        $this->load->view('backend/index', $page_data);
    }
}

public function get_user_school() {
    if ($this->session->userdata('teacher_login') != 1) {
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

public function create_event() {
    if ($this->session->userdata('teacher_login') != 1) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Unauthorized',
            'csrf' => [
                'csrfName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash()
            ]
        ]);
        return;
    }

    $teacher_id = $this->session->userdata('user_id'); // Get the logged-in teacher's ID
    $data = [
        'title' => htmlspecialchars($this->input->post('title', true)),
        'starting_date' => $this->input->post('starting_date', true),
        'starting_time' => $this->input->post('starting_time', true),
        'ending_date' => $this->input->post('ending_date', true) ?: null,
        'ending_time' => $this->input->post('ending_time', true),
        'school_id' => filter_var($this->input->post('school_id', true), FILTER_VALIDATE_INT),
        'session' => $this->input->post('session', true),
        'description' => htmlspecialchars($this->input->post('description', true)),
        'recurrence_type' => $this->input->post('recurrence_type', true),
        'recurrence_end_date' => $this->input->post('recurrence_end_date', true),
        'custom_recurrence' => $this->input->post('custom_recurrence', true),
        'visio' => !empty($this->input->post('visio', true)) && $this->input->post('visio', true) == 1 ? 1 : 0,
        'created_by' => $teacher_id
    ];

    // Validation des champs obligatoires
    if (empty($data['title']) || empty($data['starting_date']) || empty($data['starting_time']) || empty($data['ending_time']) || empty($data['school_id'])) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Tous les champs obligatoires doivent être remplis',
            'csrf' => [
                'csrfName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash()
            ]
        ]);
        return;
    }

    // Validation du format de la date (strictement YYYY-MM-DD)
    $start_date_obj = DateTime::createFromFormat('Y-m-d', $data['starting_date']);
    if ($start_date_obj === false || $start_date_obj->format('Y-m-d') !== $data['starting_date']) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Format de date de début invalide (attendu : YYYY-MM-DD)',
            'csrf' => [
                'csrfName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash()
            ]
        ]);
        return;
    }
    $data['starting_date'] = $start_date_obj->format('Y-m-d');

    // Vérifier que la date de début n'est pas dans le passé
    $now = new DateTime('now', new DateTimeZone('UTC'));
    $event_start = DateTime::createFromFormat('Y-m-d H:i:s', $data['starting_date'] . ' ' . $data['starting_time'], new DateTimeZone('UTC'));
    if ($event_start === false) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Format de date et heure de début invalide',
            'csrf' => [
                'csrfName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash()
            ]
        ]);
        return;
    }

    if ($data['ending_date']) {
        $end_date_obj = DateTime::createFromFormat('Y-m-d', $data['ending_date']);
        if ($end_date_obj === false || $end_date_obj->format('Y-m-d') !== $data['ending_date']) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Format de date de fin invalide (attendu : YYYY-MM-DD)',
                'csrf' => [
                    'csrfName' => $this->security->get_csrf_token_name(),
                    'csrfHash' => $this->security->get_csrf_hash()
                ]
            ]);
            return;
        }
        $data['ending_date'] = $end_date_obj->format('Y-m-d');

        // Validation: ending_date must be on or after starting_date
        if ($end_date_obj < $start_date_obj) {
            echo json_encode([
                'status' => 'error',
                'message' => 'La date de fin doit être postérieure ou égale à la date de début',
                'csrf' => [
                    'csrfName' => $this->security->get_csrf_token_name(),
                    'csrfHash' => $this->security->get_csrf_hash()
                ]
            ]);
            return;
        }
    }

    if ($event_start < $now) {
        echo json_encode([
            'status' => 'error',
            'message' => 'La date et l\'heure de début ne peuvent pas être dans le passé',
            'csrf' => [
                'csrfName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash()
            ]
        ]);
        return;
    }

    // Validation des heures (strictement HH:mm:ss)
    if (!preg_match('/^([01][0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/', $data['starting_time'])) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Format d\'heure de début invalide (attendu : HH:mm:ss)',
            'csrf' => [
                'csrfName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash()
            ]
        ]);
        return;
    }
    if (!preg_match('/^([01][0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/', $data['ending_time'])) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Format d\'heure de fin invalide (attendu : HH:mm:ss)',
            'csrf' => [
                'csrfName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash()
            ]
        ]);
        return;
    }

    // Validation de school_id
    if (!$this->db->get_where('schools', ['id' => $data['school_id'], 'Etat' => 1, 'status' => 1])->row()) {
        echo json_encode([
            'status' => 'error',
            'message' => 'École invalide',
            'csrf' => [
                'csrfName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash()
            ]
        ]);
        return;
    }

    // Handle participants
    $participants = json_decode($this->input->post('participants', true), true);
    if (json_last_error() !== JSON_ERROR_NONE || !is_array($participants)) {
        $participants = []; // Initialize as empty array if invalid or null
    }

    // Automatically add the teacher as a participant if not already included
    $teacher_exists = false;
    foreach ($participants as $participant) {
        if (isset($participant['type']) && $participant['type'] === 'individual' && isset($participant['id']) && $participant['id'] == $teacher_id) {
            $teacher_exists = true;
            break;
        }
    }
    if (!$teacher_exists) {
        $participants[] = [
            'type' => 'individual',
            'id' => $teacher_id
        ];
    }

    // Validation of participants
    if (empty($participants)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'You must assign the event to someone.',
            'csrf' => [
                'csrfName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash()
            ]
        ]);
        return;
    }

    foreach ($participants as $participant) {
        if (!isset($participant['type']) || !isset($participant['id'])) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Chaque participant doit avoir un type et un ID',
                'csrf' => [
                    'csrfName' => $this->security->get_csrf_token_name(),
                    'csrfHash' => $this->security->get_csrf_hash()
                ]
            ]);
            return;
        }
        if ($participant['type'] === 'class') {
            if (!$this->db->get_where('classes', ['id' => $participant['id'], 'school_id' => $data['school_id']])->row()) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Classe invalide pour cette école : ' . $participant['id'],
                    'csrf' => [
                        'csrfName' => $this->security->get_csrf_token_name(),
                        'csrfHash' => $this->security->get_csrf_hash()
                    ]
                ]);
                return;
            }
        } elseif ($participant['type'] === 'individual') {
            if (!$this->db->get_where('users', ['id' => $participant['id']])->row()) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Utilisateur invalide : ' . $participant['id'],
                    'csrf' => [
                        'csrfName' => $this->security->get_csrf_token_name(),
                        'csrfHash' => $this->security->get_csrf_hash()
                    ]
                ]);
                return;
            }
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Type de participant invalide : ' . $participant['type'],
                'csrf' => [
                    'csrfName' => $this->security->get_csrf_token_name(),
                    'csrfHash' => $this->security->get_csrf_hash()
                ]
            ]);
            return;
        }
    }

    if (!empty($data['session']) && !preg_match('/^\d{4}-\d{4}$/', $data['session'])) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Format de session invalide (ex. 2024-2025)',
            'csrf' => [
                'csrfName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash()
            ]
        ]);
        return;
    }

    // Validation de la récurrence
    if (!in_array($data['recurrence_type'], ['does_not_repeat', 'every_weekday', 'daily', 'weekly', 'monthly', 'yearly'])) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Type de récurrence invalide',
            'csrf' => [
                'csrfName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash()
            ]
        ]);
        return;
    }

    if ($data['recurrence_type'] !== 'does_not_repeat') {
        if (!empty($data['recurrence_end_date'])) {
            $recurrence_end_date_obj = DateTime::createFromFormat('Y-m-d', $data['recurrence_end_date']);
            if ($recurrence_end_date_obj === false || $recurrence_end_date_obj->format('Y-m-d') !== $data['recurrence_end_date']) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Format de date de fin de récurrence invalide (attendu : YYYY-MM-DD)',
                    'csrf' => [
                        'csrfName' => $this->security->get_csrf_token_name(),
                        'csrfHash' => $this->security->get_csrf_hash()
                    ]
                ]);
                return;
            }
            if ($recurrence_end_date_obj < $start_date_obj) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'La date de fin de récurrence doit être postérieure à la date de début',
                    'csrf' => [
                        'csrfName' => $this->security->get_csrf_token_name(),
                        'csrfHash' => $this->security->get_csrf_hash()
                    ]
                ]);
                return;
            }
            $data['recurrence_end_date'] = $recurrence_end_date_obj->format('Y-m-d');
        } else {
            // Set default recurrence end date to 1 year from starting_date
            $default_end_date = clone $start_date_obj;
            $default_end_date->modify('+1 year');
            $data['recurrence_end_date'] = $default_end_date->format('Y-m-d');
        }
    } else {
        $data['recurrence_end_date'] = null;
        $data['custom_recurrence'] = null;
    }

    // Validation de custom_recurrence pour weekly
    if ($data['recurrence_type'] === 'weekly' && !empty($data['custom_recurrence'])) {
        $decoded = json_decode($data['custom_recurrence'], true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($decoded) || empty($decoded)) {
            echo json_encode([
                'status' => 'error',
                'message' => 'La récurrence personnalisée doit être un tableau JSON valide pour la récurrence hebdomadaire',
                'csrf' => [
                    'csrfName' => $this->security->get_csrf_token_name(),
                    'csrfHash' => $this->security->get_csrf_hash()
                ]
            ]);
            return;
        }
        $validDays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        if (array_diff($decoded, $validDays)) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Les jours dans la récurrence personnalisée doivent être des jours valides',
                'csrf' => [
                    'csrfName' => $this->security->get_csrf_token_name(),
                    'csrfHash' => $this->security->get_csrf_hash()
                ]
            ]);
            return;
        }
    } elseif ($data['recurrence_type'] !== 'weekly') {
        $data['custom_recurrence'] = null;
    }

    try {
        $this->db->insert('event_calendars', $data);
        $event_id = $this->db->insert_id();
        // Insert participants into participants table
        foreach ($participants as $participant) {
            $this->db->insert('participants', [
                'event_id' => $event_id,
                'guest' => $participant['id'],
                'type' => $participant['type'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }
        echo json_encode([
            'status' => 'success',
            'message' => get_phrase('Event created successfully'),
            'csrf' => [
                'csrfName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash()
            ],
            'event_id' => $event_id,
            'event_data' => $data,
            'participants' => $participants
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => get_phrase('Error creating event : ') . $e->getMessage(),
            'csrf' => [
                'csrfName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash()
            ]
        ]);
    }
}

    public function update_event() {
        if ($this->session->userdata('teacher_login') != 1) {
            $csrf = [
                'csrfName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
            ];
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized', 'csrf' => $csrf]);
            return;
        }

        $event_id = filter_var($this->input->post('id', true), FILTER_VALIDATE_INT);
        if (!$event_id) {
            $csrf = [
                'csrfName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
            ];
            echo json_encode(['status' => 'error', 'message' => 'Event ID is required', 'csrf' => $csrf]);
            return;
        }

        // Vérifier si l'événement existe
        if (!$this->db->get_where('event_calendars', ['id' => $event_id])->row()) {
            $csrf = [
                'csrfName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
            ];
            echo json_encode(['status' => 'error', 'message' => get_phrase('Event not found'), 'csrf' => $csrf]);
            return;
        }

        $data = [
            'title' => htmlspecialchars($this->input->post('title', true)),
            'starting_date' => $this->input->post('starting_date', true),
            'starting_time' => $this->input->post('starting_time', true),
			'ending_date' => $this->input->post('ending_date', true) ?: null,
            'ending_time' => $this->input->post('ending_time', true),
            'school_id' => filter_var($this->input->post('school_id', true), FILTER_VALIDATE_INT),
            'session' => $this->input->post('session', true),
            'description' => htmlspecialchars($this->input->post('description', true)),
            'recurrence_type' => $this->input->post('recurrence_type', true),
            'recurrence_end_date' => $this->input->post('recurrence_end_date', true),
            'custom_recurrence' => $this->input->post('custom_recurrence', true),
            'visio' => !empty($this->input->post('visio', true)) && $this->input->post('visio', true) == 1 ? 1 : 0
        ];

        $csrf = [
            'csrfName' => $this->security->get_csrf_token_name(),
            'csrfHash' => $this->security->get_csrf_hash(),
        ];

        // Validation des champs obligatoires
        if (empty($data['title']) || empty($data['starting_date']) || empty($data['starting_time']) || empty($data['ending_time']) || empty($data['school_id'])) {
            echo json_encode(['status' => 'error', 'message' => 'Tous les champs obligatoires doivent être remplis', 'csrf' => $csrf]);
            return;
        }

        // Validation du format de la date (strictement YYYY-MM-DD)
        $start_date_obj = DateTime::createFromFormat('Y-m-d', $data['starting_date']);
        if ($start_date_obj === false || $start_date_obj->format('Y-m-d') !== $data['starting_date']) {
            echo json_encode(['status' => 'error', 'message' => 'Format de date de début invalide (attendu : YYYY-MM-DD)', 'csrf' => $csrf]);
            return;
        }
        $data['starting_date'] = $start_date_obj->format('Y-m-d');

        $now = new DateTime('now', new DateTimeZone('UTC'));
        $event_start = DateTime::createFromFormat('Y-m-d H:i:s', $data['starting_date'] . ' ' . $data['starting_time'], new DateTimeZone('UTC'));
        if ($event_start === false) {
            echo json_encode(['status' => 'error', 'message' => 'Format de date et heure de début invalide', 'csrf' => $csrf]);
            return;
        }
        if ($event_start < $now) {
            echo json_encode(['status' => 'error', 'message' => 'La date et l\'heure de début ne peuvent pas être dans le passé', 'csrf' => $csrf]);
            return;
        }
		if ($data['ending_date']) {
    $end_date_obj = DateTime::createFromFormat('Y-m-d', $data['ending_date']);
    if ($end_date_obj === false || $end_date_obj->format('Y-m-d') !== $data['ending_date']) {
        echo json_encode(['status' => 'error', 'message' => 'Format de date de fin invalide (attendu : YYYY-MM-DD)', 'csrf' => $csrf]);
        return;
    }
    $data['ending_date'] = $end_date_obj->format('Y-m-d');

    // Validation: ending_date must be on or after starting_date
    if ($end_date_obj < $start_date_obj) {
        echo json_encode(['status' => 'error', 'message' => 'La date de fin doit être postérieure ou égale à la date de début', 'csrf' => $csrf]);
        return;
    }
}
        // Validation des heures (strictement HH:mm:ss)
        if (!preg_match('/^([01][0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/', $data['starting_time'])) {
            echo json_encode(['status' => 'error', 'message' => 'Format d\'heure de début invalide (attendu : HH:mm:ss)', 'csrf' => $csrf]);
            return;
        }
        if (!preg_match('/^([01][0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/', $data['ending_time'])) {
            echo json_encode(['status' => 'error', 'message' => 'Format d\'heure de fin invalide (attendu : HH:mm:ss)', 'csrf' => $csrf]);
            return;
        }


        // Validation de school_id
        if (!$this->db->get_where('schools', ['id' => $data['school_id'], 'Etat' => 1, 'status' => 1])->row()) {
            echo json_encode(['status' => 'error', 'message' => 'École invalide', 'csrf' => $csrf]);
            return;
        }

        // Validation de class_id
        $participants = json_decode($this->input->post('participants', true), true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($participants) || empty($participants)) {
            echo json_encode([
                'status' => 'error',
                'message' => 'You must assign the event to someone.',
                'csrf' => $csrf
            ]);
            return;
        }

        foreach ($participants as $participant) {
            if (!isset($participant['type']) || !isset($participant['id'])) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Chaque participant doit avoir un type et un ID',
                    'csrf' => $csrf
                ]);
                return;
            }
            if ($participant['type'] === 'class') {
                if (!$this->db->get_where('classes', ['id' => $participant['id'], 'school_id' => $data['school_id']])->row()) {
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'Classe invalide pour cette école : ' . $participant['id'],
                        'csrf' => $csrf
                    ]);
                    return;
                }
            } elseif ($participant['type'] === 'individual') {
                if (!$this->db->get_where('users', ['id' => $participant['id']])->row()) {
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'Utilisateur invalide : ' . $participant['id'],
                        'csrf' => $csrf
                    ]);
                    return;
                }
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Type de participant invalide : ' . $participant['type'],
                    'csrf' => $csrf
                ]);
                return;
            }
        }

        // Validation de la récurrence
        if (!in_array($data['recurrence_type'], ['does_not_repeat', 'every_weekday', 'daily', 'weekly', 'monthly', 'yearly'])) {
            echo json_encode(['status' => 'error', 'message' => 'Type de récurrence invalide', 'csrf' => $csrf]);
            return;
        }

        if ($data['recurrence_type'] !== 'does_not_repeat') {
            if (!empty($data['recurrence_end_date'])) {
                $recurrence_end_date_obj = DateTime::createFromFormat('Y-m-d', $data['recurrence_end_date']);
                if ($recurrence_end_date_obj === false || $recurrence_end_date_obj->format('Y-m-d') !== $data['recurrence_end_date']) {
                    echo json_encode(['status' => 'error', 'message' => 'Format de date de fin de récurrence invalide (attendu : YYYY-MM-DD)', 'csrf' => $csrf]);
                    return;
                }
                if ($recurrence_end_date_obj < $start_date_obj) {
                    echo json_encode(['status' => 'error', 'message' => 'La date de fin de récurrence doit être postérieure à la date de début', 'csrf' => $csrf]);
                    return;
                }
                $data['recurrence_end_date'] = $recurrence_end_date_obj->format('Y-m-d');
            } else {
                // Set default recurrence end date to 1 year from starting_date
                $default_end_date = clone $start_date_obj;
                $default_end_date->modify('+1 year');
                $data['recurrence_end_date'] = $default_end_date->format('Y-m-d');
            }
        } else {
            $data['recurrence_end_date'] = null;
            $data['custom_recurrence'] = null;
        }

        // Validation de custom_recurrence pour weekly
        if ($data['recurrence_type'] === 'weekly' && !empty($data['custom_recurrence'])) {
            $decoded = json_decode($data['custom_recurrence'], true);
            if (json_last_error() !== JSON_ERROR_NONE || !is_array($decoded) || empty($decoded)) {
                echo json_encode(['status' => 'error', 'message' => 'La récurrence personnalisée doit être un tableau JSON valide pour la récurrence hebdomadaire', 'csrf' => $csrf]);
                return;
            }
            $validDays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
            if (array_diff($decoded, $validDays)) {
                echo json_encode(['status' => 'error', 'message' => 'Les jours dans la récurrence personnalisée doivent être des jours valides', 'csrf' => $csrf]);
                return;
            }
        } elseif ($data['recurrence_type'] !== 'weekly') {
            $data['custom_recurrence'] = null;
        }

        try {
            $this->db->where('id', $event_id);
            $this->db->update('event_calendars', $data);
            // Mettre à jour les rendez-vous associés
            $this->db->where('event_id', $event_id);
            $this->db->update('appointments', [
                'title' => $data['title'],
                'start_date' => $data['starting_date'],
                'school_id' => $data['school_id'],
                'visio' => $data['visio']
            ]);
             $this->db->where('event_id', $event_id);
            $this->db->delete('participants');
            foreach ($participants as $participant) {
                $this->db->insert('participants', [
                    'event_id' => $event_id,
                    'guest' => $participant['id'],
                    'type' => $participant['type'],
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            }
            echo json_encode([
                'status' => 'success',
                'message' => get_phrase('Event successfully updated'),
                'csrf' => $csrf,
                'event_data' => $data,
                'participants' => $participants
            ]);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => get_phrase('Error updating event : ') . $e->getMessage(), 'csrf' => $csrf]);
        }
    }

    public function delete_event() {
        if ($this->session->userdata('teacher_login') != 1) {
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
            return;
        }

        $event_id = filter_var($this->input->post('id', true), FILTER_VALIDATE_INT);
        if (!$event_id) {
            echo json_encode(['status' => 'error', 'message' => 'Event ID is required']);
            return;
        }

        // Vérifier si l'événement existe
        if (!$this->db->get_where('event_calendars', ['id' => $event_id])->row()) {
            echo json_encode(['status' => 'error', 'message' => get_phrase('Event not found')]);
            return;
        }

        try {
            // Terminer les réunions BBB associées
            $appointments = $this->db->get_where('appointments', ['event_id' => $event_id])->result_array();
            foreach ($appointments as $appointment) {
                $existing_meeting = $this->db->get_where('sessions_meetings', ['appointment_id' => $appointment['id']])->row_array();
                if ($existing_meeting && $existing_meeting['meeting_id']) {
                    $this->load->config('bigbluebutton');
                    $bbb_url = $this->config->item('bbb_url');
                    $bbb_secret = $this->config->item('bbb_secret');
                    $params = "meetingID=" . urlencode($existing_meeting['meeting_id']);
                    $checksum = sha1("end" . $params . $bbb_secret);
                    $api_url = $bbb_url . "end?" . $params . "&checksum=" . $checksum;
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $api_url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                    curl_exec($ch);
                    curl_close($ch);
                }
            }

            $this->db->where('event_id', $event_id);
            $this->db->delete('appointments');

            $this->db->where('event_id', $event_id);
            $this->db->delete('participants');

            $this->db->where('id', $event_id);
            $this->db->delete('event_calendars');

            $csrf = [
                'csrfName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash(),
            ];
            echo json_encode([
                'status' => 'success',
                'message' => get_phrase('Event successfully deleted'),
                'csrf' => $csrf
            ]);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => get_phrase('Error deleting event: ') . $e->getMessage()]);
        }
    }

    public function get_events() {
    // Vérifier l'authentification de l'utilisateur
    if ($this->session->userdata('teacher_login') != 1) {
        $csrf = [
            'csrfName' => $this->security->get_csrf_token_name(),
            'csrfHash' => $this->security->get_csrf_hash(),
        ];
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized', 'csrf' => $csrf]);
        return;
    }

    // Récupérer l'ID de l'utilisateur connecté
    $user_id = $this->session->userdata('user_id');
    
    // Mapper user_id à teacher_id
    $this->db->select('id');
    $this->db->from('teachers');
    $this->db->where('user_id', $user_id);
    $teacher = $this->db->get()->row_array();
    $teacher_id = $teacher['id'] ?? null;

    if (!$teacher_id) {
        $csrf = [
            'csrfName' => $this->security->get_csrf_token_name(),
            'csrfHash' => $this->security->get_csrf_hash(),
        ];
        echo json_encode(['status' => 'error', 'message' => 'No teacher associated with this user', 'csrf' => $csrf]);
        return;
    }

    // Récupérer les classes autorisées avec attendance = 1
    $this->db->select('class_id');
    $this->db->from('teacher_permissions');
    $this->db->where('teacher_id', $teacher_id);
    $this->db->where('attendance', 1);
    $permitted_classes = $this->db->get()->result_array();
    $permitted_class_ids = array_column($permitted_classes, 'class_id');


    // Récupérer l'ID de l'école
    $user_details = $this->user_model->get_user_details($user_id);
    $school_id = $user_details['school_id'] ?? null;

    if (!$school_id) {
        $csrf = [
            'csrfName' => $this->security->get_csrf_token_name(),
            'csrfHash' => $this->security->get_csrf_hash(),
        ];
        echo json_encode(['status' => 'error', 'message' => 'No school associated with this user', 'csrf' => $csrf]);
        return;
    }

    $start_date = $this->input->get('start_date', true);
    $end_date = $this->input->get('end_date', true);
    $event_id = $this->input->get('id', true);
    $class_id = $this->input->get('class_id', true);

    // Validation des dates
    if ($start_date && $end_date) {
        $start_date_obj = DateTime::createFromFormat('Y-m-d', $start_date);
        $end_date_obj = DateTime::createFromFormat('Y-m-d', $end_date);
        if ($start_date_obj === false || $end_date_obj === false || $start_date_obj->format('Y-m-d') !== $start_date || $end_date_obj->format('Y-m-d') !== $end_date) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Invalid date format (expected: YYYY-MM-DD)',
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
            'message' => 'start_date and end_date are required',
            'csrf' => [
                'csrfName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash()
            ]
        ]);
        return;
    }

    // Construction de la requête pour les événements
    $this->db->select('event_calendars.*, schools.name as school_name, users.name as created_by_name');
    $this->db->from('event_calendars');
    $this->db->join('schools', 'event_calendars.school_id = schools.id', 'left');
    $this->db->join('users', 'event_calendars.created_by = users.id', 'left');
    $this->db->where('event_calendars.school_id', $school_id);

    if ($event_id) {
        $this->db->where('event_calendars.id', $event_id);
    } else {
        $this->db->where('(event_calendars.starting_date <= "' . $end_date . '" AND (event_calendars.ending_date >= "' . $start_date . '" OR event_calendars.ending_date IS NULL))');
    }

    $this->db->group_by('event_calendars.id');
    $events = $this->db->get()->result_array();

    // Charger la configuration BigBlueButton
    $this->load->config('bigbluebutton');
    $bbb_url = $this->config->item('bbb_url');
    $bbb_secret = $this->config->item('bbb_secret');

    // Post-traitement des événements
    $processed_events = [];
    $now = new DateTime('now', new DateTimeZone('UTC'));
    $threshold = (clone $now)->modify('-24 hours');
    foreach ($events as $event) {
        $participants = $this->db->get_where('participants', ['event_id' => $event['id']])->result_array();

        // Check access (class OR individual)
        $has_access = false;
        foreach ($participants as $p) {
            if ($p['type'] === 'class' && in_array($p['guest'], $permitted_class_ids)) {
                $has_access = true;
                break;
            }
            if ($p['type'] === 'individual' && $p['guest'] == $user_id) {
                $has_access = true;
                break;
            }
        }
        if (!$has_access) continue;
        $event['school_name'] = $event['school_name'] ?? '';
        $event['created_by_name'] = $event['created_by_name'] ?? 'Unknown';
        $event['participants'] = [];

        foreach ($participants as $p) {
            $data = ['id' => $p['guest'], 'type' => $p['type']];
            if ($p['type'] === 'class') {
                $class = $this->db->get_where('classes', ['id' => $p['guest']])->row();
                $data['name'] = $class ? $class->name : 'Unknown';
            } else {
                $user = $this->db->get_where('users', ['id' => $p['guest']])->row();
                $data['name'] = $user ? $user->name : 'Unknown';
            }
            $event['participants'][] = $data;
        }

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

        // Vérifier si l'événement est expiré
        $event_start = DateTime::createFromFormat('Y-m-d H:i:s', $event['starting_date'] . ' ' . $event['starting_time'], new DateTimeZone('UTC'));
        $event['is_expired'] = $event['recurrence_type'] === 'does_not_repeat' && $event_start < $threshold;

        $event['occurrences'] = [];
        if ($event['recurrence_type'] !== 'does_not_repeat' && $event['recurrence_end_date']) {
            $recurrence_end_date = new DateTime($event['recurrence_end_date']);
            $current_date = new DateTime(max($event['starting_date'], $start_date));
            $end_period = new DateTime($end_date);

            $interval = null;
            if ($event['recurrence_type'] === 'daily') {
                $interval = new DateInterval('P1D');
            } elseif ($event['recurrence_type'] === 'weekly') {
                $interval = new DateInterval('P1D');
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
                    $occurrence_date = $current_date->format('Y-m-d');
                    $occurrence_start = DateTime::createFromFormat('Y-m-d H:i:s', $occurrence_date . ' ' . $event['starting_time'], new DateTimeZone('UTC'));
                    if ($occurrence_start === false) {
                        $current_date->add($interval);
                        continue;
                    }
                    $is_expired = $occurrence_start < $threshold;

                    $event['occurrences'][$occurrence_date] = [
                        'meeting_id' => null,
                        'is_running' => false,
                        'participant_count' => 0,
                        'is_expired' => $is_expired
                    ];
                }

                $current_date->add($interval);
            }
        }
        if ($event['visio'] == 1) {
            $this->db->select('start_date, meeting_id');
            $this->db->from('appointments');
            $this->db->where('event_id', $event['id']);
            $this->db->where('Etat', 1);
            $this->db->where('DATE(start_date) >=', $start_date);
            $this->db->where('DATE(start_date) <=', $end_date);
            $appointments = $this->db->get()->result_array();

            foreach ($appointments as $appointment) {
                $occurrence_date = (new DateTime($appointment['start_date']))->format('Y-m-d');
                $meeting_id = $appointment['meeting_id'] ?? null;

                $is_running = false;
                $participant_count = 0;
                if ($meeting_id) {
                    $params = "meetingID=" . urlencode($meeting_id);
                    $checksum = sha1("isMeetingRunning" . $params . $bbb_secret);
                    $is_running_url = $bbb_url . "isMeetingRunning?" . $params . "&checksum=" . $checksum;

                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $is_running_url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                    $is_running_response = curl_exec($ch);
                    $curl_error = curl_error($ch);
                    curl_close($ch);

                    if (!$curl_error) {
                        $is_running_xml = simplexml_load_string($is_running_response);
                        if ($is_running_xml && (string)$is_running_xml->returncode === "SUCCESS") {
                            $is_running = (string)$is_running_xml->running === "true";
                            if ($is_running) {
                                $params = "meetingID=" . urlencode($meeting_id);
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
                }
                $occurrence_start = DateTime::createFromFormat('Y-m-d H:i:s', $occurrence_date . ' ' . $event['starting_time'], new DateTimeZone('UTC'));
                $is_expired = $occurrence_start < $threshold;
                $event['occurrences'][$occurrence_date] = [
                    'meeting_id' => $meeting_id,
                    'is_running' => $is_running,
                    'participant_count' => $participant_count,
                    'is_expired' => $is_expired
                ];
            }
        }
        $processed_events[] = $event;
    }

    echo json_encode([
        'status' => 'success',
        'data' => $processed_events,
        'csrf' => [
            'csrfName' => $this->security->get_csrf_token_name(),
            'csrfHash' => $this->security->get_csrf_hash()
        ]
    ]);
}

public function start_meeting() {
    // Vérifier l'authentification
    if ($this->session->userdata('teacher_login') != 1) {
        $csrf = [
            'csrfName' => $this->security->get_csrf_token_name(),
            'csrfHash' => $this->security->get_csrf_hash(),
        ];
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized', 'csrf' => $csrf]);
        return;
    }

    // Récupérer et valider les paramètres
    $event_id = filter_var($this->input->post('event_id', true), FILTER_VALIDATE_INT);
    $occurrence_date = $this->input->post('occurrence_date', true);
    if (!$event_id || !$occurrence_date) {
        $csrf = [
            'csrfName' => $this->security->get_csrf_token_name(),
            'csrfHash' => $this->security->get_csrf_hash(),
        ];
        echo json_encode(['status' => 'error', 'message' => 'Event ID and occurrence date are required', 'csrf' => $csrf]);
        return;
    }

    // Valider le format de la date
    $occurrence_date_obj = DateTime::createFromFormat('Y-m-d', $occurrence_date);
    if ($occurrence_date_obj === false || $occurrence_date_obj->format('Y-m-d') !== $occurrence_date) {
        $csrf = [
            'csrfName' => $this->security->get_csrf_token_name(),
            'csrfHash' => $this->security->get_csrf_hash(),
        ];
        echo json_encode(['status' => 'error', 'message' => 'Invalid occurrence date format (expected: YYYY-MM-DD)', 'csrf' => $csrf]);
        return;
    }
    $occurrence_date = $occurrence_date_obj->format('Y-m-d');

    // Charger l'événement
    $event = $this->db->get_where('event_calendars', ['id' => $event_id])->row_array();
    if (!$event) {
        $csrf = [
            'csrfName' => $this->security->get_csrf_token_name(),
            'csrfHash' => $this->security->get_csrf_hash(),
        ];
        echo json_encode(['status' => 'error', 'message' => 'Event not found', 'csrf' => $csrf]);
        return;
    }

    // Vérifier si l'événement est en visio
    if ($event['visio'] != 1) {
        $csrf = [
            'csrfName' => $this->security->get_csrf_token_name(),
            'csrfHash' => $this->security->get_csrf_hash(),
        ];
        echo json_encode(['status' => 'error', 'message' => 'Video conferencing is not enabled for this event', 'csrf' => $csrf]);
        return;
    }

    // Désactiver les autres appointments pour cet événement
    $this->db->where('event_id', $event_id);
    $this->db->where('DATE(start_date) !=', $occurrence_date);
    $this->db->where('Etat', 1);
    $this->db->update('appointments', ['Etat' => 0]);
    
    // Vérifier si un appointment actif existe pour cette occurrence
    $this->db->where('event_id', $event_id);
    $this->db->where('DATE(start_date)', $occurrence_date);
    $this->db->where('Etat', 1);
    $appointment = $this->db->get('appointments')->row_array();
    $appointment_id = $appointment ? $appointment['id'] : null;

    // Charger la configuration BigBlueButton
    $this->load->config('bigbluebutton');
    $bbb_url = $this->config->item('bbb_url');
    $bbb_secret = $this->config->item('bbb_secret');

    // Si un appointment existe et le meeting est en cours, le rejoindre
    if ($appointment && $appointment['meeting_id']) {
        $meeting = $this->db->get_where('sessions_meetings', ['meeting_id' => $appointment['meeting_id'], 'appointment_id' => $appointment_id])->row_array();
        if ($meeting) {
            $params = "meetingID=" . urlencode($appointment['meeting_id']);
            $checksum = sha1("isMeetingRunning" . $params . $bbb_secret);
            $is_running_url = $bbb_url . "isMeetingRunning?" . $params . "&checksum=" . $checksum;

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $is_running_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            $is_running_response = curl_exec($ch);
            $curl_error = curl_error($ch);
            curl_close($ch);

            if ($curl_error) {
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

            $is_running_xml = simplexml_load_string($is_running_response);
            if ($is_running_xml && (string)$is_running_xml->returncode === "SUCCESS" && (string)$is_running_xml->running === "true") {
                $params = "meetingID=" . urlencode($appointment['meeting_id']);
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
                $participant_count = ($xml && (string)$xml->returncode === "SUCCESS") ? (int)$xml->participantCount : 0;

                $user_details = $this->user_model->get_user_details($this->session->userdata('user_id'));
                $full_name = urlencode($user_details['name'] ?? 'Teacher-' . rand(1000, 9999));
                $join_params = "fullName=$full_name&meetingID=" . urlencode($appointment['meeting_id']) . "&password=" . $meeting['moderator_pw'] . "&redirect=true";
                $join_checksum = sha1("join" . $join_params . $bbb_secret);
                $join_url = $bbb_url . "join?" . $join_params . "&checksum=" . $join_checksum;

                $this->db->select('guest, type');
                $this->db->from('appointment_participants');
                $this->db->where('appointment_id', $appointment_id);
                $participants = $this->db->get()->result_array();

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
                    'participant_count' => $participant_count,
                    'is_running' => true,
                    'participants' => $participants,
                    'csrf' => $csrf
                ]);
                return;
            } else {
                // Marquer l'ancien appointment comme inactif
                $this->db->where('id', $appointment_id);
                $this->db->update('appointments', ['Etat' => 0]);
            }
        }
    }

    $this->db->select('guest, type');
    $this->db->from('participants');
    $this->db->where('event_id', $event_id);
    $participants = $this->db->get()->result_array();

    // Créer un nouvel appointment pour cette occurrence
    $appointment_data = [
        'event_id' => $event_id,
        'title' => $event['title'],
        'start_date' => $occurrence_date . ' ' . $event['starting_time'],
        'school_id' => $event['school_id'],
        'visio' => $event['visio'],
        'Etat' => 1,
        'meeting_id' => null
    ];
    $this->db->insert('appointments', $appointment_data);
    $appointment_id = $this->db->insert_id();
    foreach ($participants as $participant) {
        $this->db->insert('appointment_participants', [
            'appointment_id' => $appointment_id,
            'guest' => $participant['guest'],
            'type' => $participant['type']
        ]);
    }
    // Create a new BigBlueButton meeting
    $meeting_name = $event['title'] ? $event['title'] : "Meeting for Event $event_id";
    $new_meeting_id = "meeting-$appointment_id-" . time();
    $attendee_password = "ap_" . $appointment_id;
    $moderator_password = "mp_" . $appointment_id;
    $start_time = $occurrence_date . ' ' . $event['starting_time'];
    $end_time = date('Y-m-d H:i:s', strtotime($start_time . ' +2 hours'));

    $params = "name=" . urlencode($meeting_name) .
              "&meetingID=" . urlencode($new_meeting_id) .
              "&attendeePW=$attendee_password" .
              "&moderatorPW=$moderator_password" .
              "&record=true" .
              "&autoStartRecording=false" .
              "&allowStartStopRecording=true" .
              "&welcome=" . urlencode(get_phrase("Welcome to the meeting") . ': ' . $event['title']) .
              "&endWhenNoModerator=false" .
              "&duration=120";

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
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to create meeting: Invalid response from BBB server',
            'csrf' => $csrf
        ]);
        return;
    }

    if ((string)$xml->returncode === "SUCCESS") {
        // Enregistrer les détails du meeting
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
            'class_id' => null,
            'created_at' => date('Y-m-d H:i:s'),
            'description' => null
        ];
        $this->db->insert('sessions_meetings', $meeting_data);

        // Mettre à jour l'appointment avec le nouveau meeting_id
        $this->db->where('id', $appointment_id);
        $this->db->update('appointments', ['meeting_id' => $new_meeting_id]);

        // Vérifier si le meeting est en cours
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

        // Générer l'URL de jointure
        $user_details = $this->user_model->get_user_details($this->session->userdata('user_id'));
        $full_name = urlencode($user_details['name'] ?? 'Teacher-' . rand(1000, 9999));
        $join_params = "fullName=$full_name&meetingID=" . urlencode($new_meeting_id) . "&password=$moderator_password&redirect=true";
        $join_checksum = sha1("join" . $join_params . $bbb_secret);
        $join_url = $bbb_url . "join?" . $join_params . "&checksum=" . $join_checksum;

        $this->db->select('guest, type');
        $this->db->from('appointment_participants');
        $this->db->where('appointment_id', $appointment_id);
        $participants = $this->db->get()->result_array();

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
            'participants' => $participants,
            'csrf' => $csrf
        ]);
    } else {
        $csrf = [
            'csrfName' => $this->security->get_csrf_token_name(),
            'csrfHash' => $this->security->get_csrf_hash(),
        ];
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to create meeting: ' . (string)$xml->message,
            'csrf' => $csrf
        ]);
    }
}

public function get_classes_by_school() {
    if ($this->session->userdata('teacher_login') != 1) {
        $csrf = [
            'csrfName' => $this->security->get_csrf_token_name(),
            'csrfHash' => $this->security->get_csrf_hash(),
        ];
        echo json_encode(['status' => 'error', 'message' => 'Session expired, please login again', 'csrf' => $csrf]);
        return;
    }

    $school_id = $this->input->post('school_id', true);
    if (empty($school_id)) {
        $csrf = [
            'csrfName' => $this->security->get_csrf_token_name(),
            'csrfHash' => $this->security->get_csrf_hash(),
        ];
        echo json_encode(['classes' => [], 'status' => 'error', 'message' => 'Aucun ID d\'école fourni', 'csrf' => $csrf]);
        return;
    }

    $user_id = $this->session->userdata('user_id');

    // Mapper user_id à teacher_id
    $this->db->select('id');
    $this->db->from('teachers');
    $this->db->where('user_id', $user_id);
    $teacher = $this->db->get()->row_array();
    $teacher_id = $teacher['id'] ?? null;

    if (!$teacher_id) {
        $csrf = [
            'csrfName' => $this->security->get_csrf_token_name(),
            'csrfHash' => $this->security->get_csrf_hash(),
        ];
        echo json_encode(['status' => 'error', 'message' => 'Aucun enseignant associé à cet utilisateur', 'csrf' => $csrf]);
        return;
    }

    // Récupérer les classes autorisées avec attendance = 1
    $this->db->select('class_id');
    $this->db->from('teacher_permissions');
    $this->db->where('teacher_id', $teacher_id);
    $this->db->where('attendance', 1);
    $permitted_classes = $this->db->get()->result_array();
    $permitted_class_ids = array_column($permitted_classes, 'class_id');

    if (empty($permitted_class_ids)) {
        $csrf = [
            'csrfName' => $this->security->get_csrf_token_name(),
            'csrfHash' => $this->security->get_csrf_hash(),
        ];
        echo json_encode(['classes' => [], 'status' => 'success', 'message' => 'Aucune classe autorisée pour cet enseignant', 'csrf' => $csrf]);
        return;
    }

    // Vérifier si les class_id existent dans la table classes
    $this->db->select('id, name');
    $this->db->from('classes');
    $this->db->where('school_id', $school_id);
    $this->db->where_in('id', $permitted_class_ids);
    $classes = $this->db->get()->result_array();

    $csrf = [
        'csrfName' => $this->security->get_csrf_token_name(),
        'csrfHash' => $this->security->get_csrf_hash(),
    ];

    echo json_encode([
        'status' => 'success',
        'classes' => $classes,
        'message' => empty($classes) ? 'Aucune classe valide trouvée pour votre école' : '',
        'csrf' => $csrf
    ]);
}

public function get_school_data() {
    if ($this->session->userdata('teacher_login') != 1) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Unauthorized',
            'csrf' => [
                'csrfName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash()
            ]
        ]);
        return;
    }

    $school_id = filter_var($this->input->post('school_id', true), FILTER_VALIDATE_INT);
    if (!$school_id || !$this->db->get_where('schools', ['id' => $school_id, 'Etat' => 1, 'status' => 1])->row()) {
        echo json_encode([
            'status' => 'error',
            'message' => 'École invalide',
            'csrf' => [
                'csrfName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash()
            ]
        ]);
        return;
    }

    try {
        // Récupérer les classes
        $this->db->select('id, name');
        $this->db->where('school_id', $school_id);
        $classes = $this->db->get('classes')->result_array();

        // Récupérer l'ID du superadmin connecté
        $current_user_id = $this->session->userdata('user_id');

        // Récupérer les participants envoyés dans la requête
        $participants = json_decode($this->input->post('participants', true), true);
        $class_ids = [];

        if (is_array($participants) && !empty($participants)) {
            foreach ($participants as $participant) {
                if (isset($participant['type']) && $participant['type'] === 'class' && isset($participant['id'])) {
                    $class_ids[] = $participant['id'];
                }
            }
        }

        $users = [];

        // 1. Récupérer les étudiants
        $this->db->select('DISTINCT(u.id), u.name, "student" as type, u.role, u.status as user_status, s.status as student_status');
        $this->db->from('users u');
        $this->db->join('students s', 's.user_id = u.id', 'inner');
        $this->db->join('enrols e', 'e.student_id = s.id', 'inner');
        $this->db->where('e.school_id', $school_id);
        $this->db->where('u.status', 1);
        $this->db->where('s.status', 1);
        $this->db->where('u.id !=', $current_user_id);
        if (!empty($class_ids)) {
            $this->db->where_not_in('e.class_id', $class_ids);
        }
        $this->db->order_by('u.name', 'ASC');
        $students = $this->db->get()->result_array();
        $users = array_merge($users, $students);

        // 2. Récupérer les enseignants
        $this->db->select('DISTINCT(u.id), u.name, "teacher" as type, u.role');
        $this->db->from('users u');
        $this->db->join('teachers t', 't.user_id = u.id', 'inner');
        $this->db->where('t.school_id', $school_id);
        $this->db->where('u.status', 1);
        // Remove or adjust the exclusion of the current user
        // $this->db->where('u.id !=', $current_user_id); // Comment this out
        $this->db->order_by('u.name', 'ASC');
        $teachers = $this->db->get()->result_array();
        $users = array_merge($users, $teachers);

        // 3. Récupérer les admins et superadmins
        $this->db->select('DISTINCT(u.id), u.name, u.role as type, u.role');
        $this->db->from('users u');
        $this->db->where('u.school_id', $school_id);
        $this->db->where('u.status', 1);
        $this->db->where_in('u.role', ['admin', 'superadmin']);
        $this->db->where('u.id !=', $current_user_id);
        $this->db->order_by('u.name', 'ASC');
        $admins = $this->db->get()->result_array();
        $users = array_merge($users, $admins);

        // Éliminer les doublons basés sur l'ID
        $unique_users = [];
        $seen_ids = [];
        foreach ($users as $user) {
            if (!in_array($user['id'], $seen_ids)) {
                $seen_ids[] = $user['id'];
                $unique_users[] = $user;
            }
        }

        // Trier les utilisateurs par nom
        usort($unique_users, function($a, $b) {
            return strcmp($a['name'], $b['name']);
        });

        echo json_encode([
            'status' => 'success',
            'classes' => $classes,
            'users' => $unique_users,
            'csrf' => [
                'csrfName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash()
            ]
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Erreur lors de la récupération des données',
            'csrf' => [
                'csrfName' => $this->security->get_csrf_token_name(),
                'csrfHash' => $this->security->get_csrf_hash()
            ]
        ]);
    }
}
}
