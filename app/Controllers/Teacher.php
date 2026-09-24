<?php

namespace App\Controllers;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use DateTime;
use DateTimeZone;
use DateInterval;


/*
*  @author   : Creativeitem
*  date      : November, 2019
*  Ekattor School Management System With Addons
*  http://codecanyon.net/user/Creativeitem
*  http://support.creativeitem.com
*/

class Teacher extends BaseController
{
    /**
     * Models to auto-load
     */
    protected $models = [
        'Crud_model' => 'crud_model',
        'User_model' => 'user_model',
        'Settings_model' => 'settings_model',
        'Payment_model' => 'payment_model',
        'Email_model' => 'email_model',
        'Addon_model' => 'addon_model',
        'Frontend_model' => 'frontend_model',
        'Room_model' => 'room_model',
        'Teacher_model' => 'teacher_model',
    ];

    /**
     * Services to auto-load
     */
    protected $services = [
        'subscriptionService' => 'subscriptionService',
    ];

    /**
     * @var \App\Libraries\SubscriptionService
     */
    protected $subscriptionService;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        /*SET DEFAULT TIMEZONE*/
        timezone();

        if (session()->get('teacher_login') != 1) {
            return redirect()->to('/login')->send();
        }

        // Keep backend context consistent for view includes and route() helper.
        if (session()->get('user_type') !== 'teacher' || session()->get('role') !== 'teacher') {
            session()->set([
                'user_type' => 'teacher',
                'role' => 'teacher',
            ]);
        }

        // ---- VÃ©rification du statut de paiement de la communautÃ© pour bloquer les mentors ----
        $this->initializeSchoolAccess();
    }

    /**
     * Initialize school-based access control
     */
    protected function initializeSchoolAccess()
    {
        $school_id = session()->get('school_id');
        if (!$school_id) {
            $school_id = session()->get('active_school_id');
        }

        if ($school_id) {
            $school = $this->teacher_model->get_school_by_id($school_id);
        } else {
            $school = null;
        }

        $current_method = $this->request->getMethod();

        log_message('debug', "Teacher construct - is_teacher: true, school_id: $school_id, method: $current_method");

        // ---- Gestion de la pÃ©riode d'essai de 14 jours et de l'abonnement mensuel ----
        $trial_expired = false;
        $subscription_expired = false;
        $school_not_approved = false;

        // Protection de base : communautÃ© non approuvÃ©e
        if (!$school || (int)$school['status'] !== 1) {
            $school_not_approved = true;
            log_message('debug', "Teacher construct - School not approved or not found");
        }

        if ($school && community_billing_enabled()) {
            $now = time();
            $is_trial = isset($school['is_trial']) ? (int)$school['is_trial'] : 0;
            $is_paid = isset($school['is_paid']) ? (int)$school['is_paid'] : 0;
            $trial_end = isset($school['trial_end']) ? (int)$school['trial_end'] : 0;
            $subscription_end = isset($school['subscription_end']) ? (int)$school['subscription_end'] : 0;

            // DEBUG: Log pour vÃ©rifier les valeurs
            log_message('debug', "Teacher construct - school_id: $school_id, is_paid: $is_paid, subscription_end: $subscription_end, now: $now");
            if ($subscription_end > 0) {
                log_message('debug', "Teacher construct - subscription_end date: " . date('Y-m-d H:i:s', $subscription_end) . ", now date: " . date('Y-m-d H:i:s', $now));
            }

            // VÃ©rifier si l'essai de 14 jours est expirÃ©
            if ($is_trial === 1 && $is_paid === 0 && $trial_end > 0 && $now > $trial_end) {
                $trial_expired = true;
                log_message('debug', "Teacher construct - Trial expired detected");
            }

            // VÃ©rifier si l'abonnement mensuel est expirÃ©
            if ($is_trial === 0 && $subscription_end > 0 && $now > $subscription_end) {
                $subscription_expired = true;
                log_message('debug', "Teacher construct - Subscription expired detected: subscription_end (" . date('Y-m-d H:i:s', $subscription_end) . ") < now (" . date('Y-m-d H:i:s', $now) . ")");
            } elseif ($is_trial === 0 && $subscription_end > 0 && $now <= $subscription_end) {
                log_message('debug', "Teacher construct - Subscription still valid: subscription_end (" . date('Y-m-d H:i:s', $subscription_end) . ") >= now (" . date('Y-m-d H:i:s', $now) . ")");
            }
        }

        // Partage l'info avec les vues
        $this->trial_expired = $trial_expired || $subscription_expired || $school_not_approved;
        $this->school_data = $school;

        // Si l'essai ou l'abonnement est expirÃ©, OU si la communautÃ© n'est pas approuvÃ©e, on bloque l'accÃ¨s
        if ($trial_expired || $subscription_expired || $school_not_approved) {
            // Laisser accÃ¨s uniquement au dashboard, au logout et au changement de langue
            $allowed_methods_trial = ['dashboard', 'logout', 'language'];

            if (!in_array($current_method, $allowed_methods_trial)) {
                log_message('debug', "Teacher construct - Access blocked for method '$current_method' (trial_expired: " . ($trial_expired ? 'true' : 'false') . ", subscription_expired: " . ($subscription_expired ? 'true' : 'false') . ", school_not_approved: " . ($school_not_approved ? 'true' : 'false') . ")");
                return redirect()->to(site_url('app/dashboard'));
            } else {
                log_message('debug', "Teacher construct - Access allowed for method '$current_method' (blocked but method is in allowed list)");
            }
        } else {
            log_message('debug', "Teacher construct - Access allowed for method '$current_method' (no blocking conditions)");
        }
    }

    private function _update_url_language_prefix($url, $new_lang_code) {
        return update_url_language_prefix($url, $new_lang_code);
    }

	//dashboard
	public function index() {
		return redirect()->to(route('dashboard'));
	}

	public function dashboard() {
		$page_data['page_title'] = 'Dashboard';
		$page_data['folder_name'] = 'dashboard';
		return view('backend/index', $page_data);
	}


	   //START TEACHER Create_Join bigbleubutton 
	   public function Create_Join($param1 = '', $param2 = '', $param3 = '')
	   {
	 
   
	 
				if (empty($param1)) {
				$page_data['folder_name'] = 'bigbleubutton';
				$page_data['page_title'] = 'DÃ©marrer RÃ©union';
				return view('backend/index', $page_data);
				}
	   }
	   //END TEACHER Create_Join bigbleubutton 
		//START TEACHER Create_Join bigbleubutton 
		public function Liveclasse($param1 = '', $param2 = '', $param3 = '')
		{
		
			if ($param1 == 'create') {
			$response = $this->room_model->create_room();
			// echo $response;
			// PrÃ©parer la rÃ©ponse avec un nouveau jeton CSRF
			$csrf = array(
				'csrfName' => csrf_token(),
				'csrfHash' => csrf_hash(),
			);
		
			// Renvoyer la rÃ©ponse avec un nouveau jeton CSRF
			echo json_encode(array('status' => $response, 'csrf' => $csrf));
			}

			if ($param1 == 'update') {
			$response = $this->room_model->update_room($param2);
			// echo $response;
			// PrÃ©parer la rÃ©ponse avec un nouveau jeton CSRF
			$csrf = array(
				'csrfName' => csrf_token(),
				'csrfHash' => csrf_hash(),
			);
		
			// Renvoyer la rÃ©ponse avec un nouveau jeton CSRF
			echo json_encode(array('status' => $response, 'csrf' => $csrf));
			}
			if ($param1 == 'list') {
				return view('backend/teacher/bigbleubutton/list');
			}
		
			if (empty($param1)) {
			$page_data['folder_name'] = 'bigbleubutton';
			$page_data['page_title'] = 'DÃ©marrer RÃ©union';
			return view('backend/index', $page_data);
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
			// RÃ©cupÃ©ration et sÃ©curisation des donnÃ©es
			$title = $this->request->getPost('title');
			$start_date = $this->request->getPost('start');
			$description = $this->request->getPost('description');
			$classe_id = $this->request->getPost('classe_id');
			$room_id = $this->request->getPost('room_id');
			$sections = $this->request->getPost('sections'); // Tableau de sections sÃ©lectionnÃ©es
			
		  
		   
			// VÃ©rification : Les champs obligatoires ne doivent pas Ãªtre vides
			if (empty($title) || empty($start_date)) {
				echo json_encode(["status" => "error", "message" => "Titre et date sont obligatoires"]);
				exit;
			}
		
			// CrÃ©ation du tableau de donnÃ©es Ã  insÃ©rer
			$data = array(
				'title' => $title,
				'start_date' => $start_date,
				'description' => $description,
				'classe_id' => $classe_id,
				'sections_id' => $sections, // Stockage sous forme "1,2,3"
				'room_id' => $room_id,
				'school_id' => $schoolID
			);
		
			// Insertion dans la base de donnÃ©es avec gestion d'erreur
			try {
				$this->teacher_model->insert_appointment($data);
				echo json_encode(["status" => "success", "message" => "Rendez-vous ajoutÃ© avec succÃ¨s"]);
			} catch (Exception $e) {
				echo json_encode(["status" => "error", "message" => "Erreur lors de l'ajout du rendez-vous : " . $e->getMessage()]);
			}
		}
		
		  public function update_appointment()
		   {
				$id = $this->request->getPost('id');
				// die( $id);
			
				$data = array(
					'title' => $this->request->getPost('title'),
					'start_date' => $this->request->getPost('start'),
					'description' => $this->request->getPost('description'),
					'classe_id' => $this->request->getPost('classe_id'),
					'sections_id' => $this->request->getPost('sections'),
					'room_id' => $this->request->getPost('room_id')
				);
			
				$this->teacher_model->update_appointment($id, $data);
				echo json_encode(["status" => "updated"]);
			}
		
		  public function delete_appointment() {
			  $id = $this->request->getPost('id');
	
			  $appointments ['Etat'] = 0;
	
			  $this->teacher_model->update_appointment($id, $appointments);
		  
		  
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
			  echo json_encode(["status" => "success", "message" => "Room supprimÃ©e avec succÃ¨s !"]);
		  }
	
	// public function recording($param1 = '', $param2 = '', $param3 = '')
    // {
    //         // Check authentication
    //         if (session()->get('teacher_login') != 1) {
    //             return redirect()->to(site_url('login'));
    //         }

    //         // Get the logged-in user's ID
    //         $user_id = session()->get('user_id');

    //         // Map user_id to teacher_id
    //         $this->db->select('id');
    //         $this->db->from('teachers');
    //         $this->db->where('user_id', $user_id);
    //         $teacher = $this->db->get();
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
    //         $permitted_classes = $this->db->get();
    //         $permitted_class_ids = array_map('strval', array_column($permitted_classes, 'class_id'));

    //         // Get school_id from users table
    //         $user = db()->table('users')->where(['id' => $user_id])->getRowArray();
    //         if (!$user || empty($user['school_id'])) {
    //             log_message('error', 'recording - No school_id found for user_id: ' . $user_id);
    //             show_error('No school associated with this user.', 403);
    //             return;
    //         }
    //         $school_id = $user['school_id'];

    //         // Synchronize recordings (unchanged)
    //         $bbbConfig = config('Bigbluebutton');
    //         $bbb_url = $bbbConfig->bbb_url ?? '';
    //         $bbb_secret = $bbbConfig->bbb_secret ?? '';

    //         $last_sync = session()->get('last_recording_sync');
    //         $current_time = time();
    //         $sync_interval = 10; // Synchronize every 10 seconds (for testing, adjust as needed)

    //         if (!$last_sync || ($current_time - $last_sync) > $sync_interval) {
    //             // Filter meetings by school_id
    //             $this->db->where('school_id', $school_id);
    //             $meetings = $this->db->get('sessions_meetings');

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

    //                         $existing = $this->teacher_model->get_recording_by_id($recording_id);
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
    //                             $this->teacher_model->insert_table('recordings', $recording_data);
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

    //             session()->set_userdata('last_recording_sync', $current_time);
    //         }

    //         // Initialize filters
    //         $filters = [
    //             'meeting_name' => $this->request->getPost('meeting_name') ?? '',
    //             'date_range' => $this->request->getPost('date_range') ?? ''
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
    //         $recordings = $this->db->get();

    //         // Handle AJAX request
    //         if ($this->request->isAJAX()) {
    //             header('Content-Type: application/json');
    //             echo json_encode([
    //                 'status' => 'success',
    //                 'recordings' => $recordings,
    //                 'filters' => $filters,
    //                 'csrf_token' => csrf_hash()
    //             ]);
    //             return;
    //         }

    //         // Load view for non-AJAX request
    //         $page_data['recordings'] = $recordings;
    //         $page_data['filters'] = $filters;
    //         $page_data['page_name'] = 'recording/recording';
    //         $page_data['page_title'] = 'recording';

    //         return view('backend/index', $page_data);
    // }

    public function recording($param1 = '', $param2 = '', $param3 = '') 
    {
        // Check authentication
        if (session()->get('teacher_login') != 1) {
            return redirect()->to(site_url('login'));
            }

        // Get User and Teacher ID
        $user_id = session()->get('user_id');
        $teacher = $this->teacher_model->get_teacher_by_user_id($user_id);
        if (!$teacher) {
            show_error('No teacher found', 403);
            return;
        }
        $teacher_id = $teacher['id'];

        // Get school_id
        $user = $this->teacher_model->get_user_by_id($user_id);
        if (!$user || empty($user['school_id'])) {
            show_error('No school associated', 403);
            return;
        }
        $school_id = $user['school_id'];

        // Get permitted classes
        $permitted_classes = $this->teacher_model->get_where_result('teacher_permissions', ['teacher_id' => $teacher_id, 'attendance' => 1], 'class_id');
        $permitted_class_ids = array_column($permitted_classes, 'class_id');
        
        if (empty($permitted_class_ids)) {
            $permitted_class_ids = [-1]; 
        }

        // Synchronize recordings
        $bbbConfig = config('Bigbluebutton');
        $bbb_url = $bbbConfig->bbb_url ?? '';
        $bbb_secret = $bbbConfig->bbb_secret ?? '';

        $last_sync = session()->get('last_recording_sync');
        $current_time = time();
        $sync_interval = 10; 

        if (!$last_sync || ($current_time - $last_sync) > $sync_interval) {
            $seven_days_ago = date('Y-m-d H:i:s', strtotime('-7 days'));
            $placeholders = implode(',', array_fill(0, count($permitted_class_ids), '?'));
            $meetings = $this->teacher_model->get_by_query(
                "SELECT * FROM sessions_meetings WHERE school_id = ? AND created_at >= ? AND class_id IN ($placeholders) ORDER BY created_at DESC LIMIT 15",
                array_merge([$school_id, $seven_days_ago], $permitted_class_ids)
            );

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

                        $existing = $this->teacher_model->get_recording_by_id($recording_id);
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
                            $this->teacher_model->insert_table('recordings', $recording_data);
                        } else {
                            $this->teacher_model->update_recording($recording_id, [
                                'recording_url' => $recording_url,
                                'updated_at' => date('Y-m-d H:i:s')
                            ]);
                        }
                    }
                }
            }

            session()->set('last_recording_sync', $current_time);
        }

        // Initialize filters
        $filters = [
            'meeting_name' => $this->request->getPost('meeting_name') ?? '',
            'date_range' => $this->request->getPost('date_range') ?? ''
        ];

        // Build query
        $sql = "SELECT r.*, c.name as class_name FROM recordings r LEFT JOIN classes c ON r.class_id = c.id WHERE r.school_id = ? AND r.class_id IN (" . implode(',', array_fill(0, count($permitted_class_ids), '?')) . ")";
        $params = array_merge([$school_id], $permitted_class_ids);

        // Apply filters
        if (!empty($filters['meeting_name'])) {
            $sql .= " AND r.name LIKE ?";
            $params[] = '%' . $filters['meeting_name'] . '%';
        }
        if (!empty($filters['date_range'])) {
            $dates = explode(' - ', $filters['date_range']);
            if (count($dates) == 1) {
                $date = DateTime::createFromFormat('d-m-Y', trim($dates[0]));
                if ($date) {
                    $sql .= " AND DATE(r.start_time) = ?";
                    $params[] = $date->format('Y-m-d');
                }
            } elseif (count($dates) == 2) {
                $date_from = DateTime::createFromFormat('d-m-Y', trim($dates[0]));
                $date_to = DateTime::createFromFormat('d-m-Y', trim($dates[1]));
                if ($date_from && $date_to) {
                    $sql .= " AND r.start_time >= ? AND r.start_time <= ?";
                    $params[] = $date_from->format('Y-m-d 00:00:00');
                    $params[] = $date_to->format('Y-m-d 23:59:59');
                }
            }
        }

        $sql .= " ORDER BY r.created_at DESC";
        $recordings = $this->teacher_model->get_by_query($sql, $params);

        // Handle AJAX request
        if ($this->request->isAJAX()) {
            header('Content-Type: application/json');
            echo json_encode([
                'status' => 'success',
                'recordings' => $recordings,
                'filters' => $filters,
                'csrf_token' => csrf_hash()
            ]);
            return;
        }

        // Load view for non-AJAX request
        $page_data['recordings'] = $recordings;
        $page_data['filters'] = $filters;
        $page_data['page_name'] = 'recording/recording';
        $page_data['page_title'] = 'recording';

        return view('backend/index', $page_data);
    }
		   
			public function delete_recording()
            {
                // VÃ©rifier l'authentification
                if (session()->get('teacher_login') != 1) {
                    $response = [
                        'status' => 'error',
                        'message' => get_phrase('unauthorized_access'),
                        'csrf_token' => csrf_hash()
                    ];
                    return $this->response->setJSON($response);
                    return;
                }

                // Get permitted classes for security check
                $user_id = session()->get('user_id');
                $teacher = $this->teacher_model->get_teacher_by_user_id($user_id);
                if (!$teacher) {
                     return $this->response->setStatusCode(403);
                }
                $teacher_id = $teacher['id'];

                $permitted_classes = $this->teacher_model->get_teacher_classes($teacher_id);
                $permitted_class_ids = array_column($permitted_classes, 'class_id');
                
                if (empty($permitted_class_ids)) {
                    $response = [
                        'status' => 'error',
                        'message' => get_phrase('unauthorized_access'),
                        'csrf_token' => csrf_hash()
                    ];
                    return $this->response->setJSON($response);
                    return;
                }

                // RÃ©cupÃ©rer l'ID de l'enregistrement depuis la requÃªte POST
                $recording_id = trim(esc($this->request->getPost('recording_id')));

                if (empty($recording_id)) {
                    $response = [
                        'status' => 'error',
                        'message' => get_phrase('invalid_recording_id'),
                        'csrf_token' => csrf_hash()
                    ];
                    log_message('error', 'delete_recording - Invalid recording_id received');
                    return $this->response->setJSON($response);
                    return;
                }

                // VÃ©rifier si l'enregistrement existe dans la table recordings
                $existing = $this->teacher_model->get_row('recordings', ['recording_id' => $recording_id]);

                if (!$existing) {
                    $response = [
                        'status' => 'error',
                        'message' => get_phrase('recording_not_found'),
                        'csrf_token' => csrf_hash()
                    ];
                    log_message('error', 'delete_recording - Recording not found: recording_id=' . $recording_id);
                    return $this->response->setJSON($response);
                    return;
                }

                // Security Check: Is this recording from a permitted class?
                if (!in_array($existing['class_id'], $permitted_class_ids)) {
                     $response = [
                        'status' => 'error',
                        'message' => get_phrase('unauthorized_action_for_this_class'),
                        'csrf_token' => csrf_hash()
                    ];
                    return $this->response->setJSON($response);
                    return;
                }

                // Charger la configuration BigBlueButton
                $bbbConfig = config('Bigbluebutton');
                $bbb_url = $bbbConfig->bbb_url ?? '';
                $bbb_secret = $bbbConfig->bbb_secret ?? '';

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
                        'csrf_token' => csrf_hash()
                    ];
                    return $this->response->setJSON($response);
                    return;
                }

                // VÃ©rifier la rÃ©ponse de l'API BBB
                $delete_xml = simplexml_load_string($delete_response);
                if (!$delete_xml || (string)$delete_xml->returncode !== "SUCCESS") {
                    log_message('error', 'delete_recording - BBB API delete failed: recording_id=' . $recording_id . ', Response: ' . $delete_response);
                    $response = [
                        'status' => 'error',
                        'message' => get_phrase('failed_to_delete_recording_from_bbb'),
                        'csrf_token' => csrf_hash()
                    ];
                    return $this->response->setJSON($response);
                    return;
                }

                // Supprimer l'enregistrement de la table recordings
                $this->teacher_model->delete_table('recordings', ['recording_id' => $recording_id]);
                $db_error = db()->error();


                if ($db_error['code'] == 0 && db()->affectedRows() > 0) {
                    $response = [
                        'status' => 'success',
                        'message' => get_phrase('recording_deleted_successfully'),
                        'csrf_token' => csrf_hash()
                    ];
                } else {
                    log_message('error', 'delete_recording - Failed to delete recording: recording_id=' . $recording_id . ', Last Query: ' . db()->getLastQuery() . ', DB Error: ' . json_encode($db_error));
                    $response = [
                        'status' => 'error',
                        'message' => get_phrase('database_error'),
                        'csrf_token' => csrf_hash()
                    ];
                }
                return $this->response->setJSON($response);
            }
	
		  public function filter_recordings()
		  {
			  $bbbConfig = config('Bigbluebutton');
			  $bbbUrl = rtrim($bbbConfig->bbb_url ?? '', '/') . '/';
			  $bbbSecret = $bbbConfig->bbb_secret ?? '';
		  
			  $meeting_name = trim(esc($this->request->getPost('meeting_name')));
			  $date_range = $this->request->getPost('date_range');
			  $schoolID = school_id();
		  
			  // PrÃ©paration de la requÃªte principale
			  $sql = "SELECT 
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
			  FROM appointments
			  LEFT JOIN rooms ON rooms.id = appointments.room_id
			  LEFT JOIN classes ON classes.id = appointments.classe_id
			  WHERE appointments.Etat = 1 AND appointments.school_id = ?";
			  $params = [$schoolID];
		  
			  if (!empty($meeting_name)) {
				  $sql .= " AND appointments.title LIKE ?";
				  $params[] = '%' . $meeting_name . '%';
			  }
		  
			  if (!empty($date_range)) {
				  $dates = explode(' to ', $date_range);
				  if (count($dates) === 2) {
					  $sql .= " AND appointments.start_date >= ? AND appointments.start_date <= ?";
					  $params[] = $dates[0] . ' 00:00:00';
					  $params[] = $dates[1] . ' 23:59:59';
				  }
			  }
		  
			  $appointments = $this->teacher_model->get_by_query($sql, $params);
		  
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
	   
			  // ðŸ”½ Affichage HTML
			  foreach ($appointments as $appointment) {
				  // Traitement des sections
				  $section_label = 'â€”';
				  if (!empty($appointment['section'])) {
					  $section_ids = explode(',', $appointment['section']);
					  $section_names = [];
					  foreach ($section_ids as $id) {
						  $name = $this->teacher_model->get_section_name($id);
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
				  echo '<td>' . (!empty($appointment_rec['recordings']) ? $appointment_rec['recordings'][0]['duration'] : 'â€”') . '</td>';
		  
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
		  
				  echo '<a href="' . site_url('superadmin/delete_appointment_and_recording/' . $appointment['id']) . '" class="btn btn-sm btn-danger" onclick="return confirm(\'â— Cette action supprimera le rendez-vous et lâ€™enregistrement associÃ©. Continuer ?\')">ðŸ—‘ï¸ Supprimer</a>';
				  echo '</td></tr>';
			  }
		  }
		  
	
		  
		  public function export_recordings_csv() {
			  $bbbConfig = config('Bigbluebutton');
			  $bbbUrl = $bbbConfig->bbb_url ?? '';
			  $bbbSecret = $bbbConfig->bbb_secret ?? '';
			  $meeting_name = $this->request->getGet('meeting_name');
			  $date_range = $this->request->getGet('date_range');
			  $schoolID = school_id();
	
			  $sql = "SELECT 
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
			  FROM appointments
			  LEFT JOIN rooms ON rooms.id = appointments.room_id
			  LEFT JOIN classes ON classes.id = appointments.classe_id
			  WHERE appointments.Etat = 1 AND appointments.school_id = ?";
			  $params = [$schoolID];
			
			  // Filtrer par nom de rÃ©union
			  if (!empty($meeting_name)) {
				$sql .= " AND appointments.title LIKE ?";
				$params[] = '%' . $meeting_name . '%';
			  }
		  
			  if (!empty($date_range)) {
				  $dates = explode(' to ', $date_range);
				  if (count($dates) === 2) {
					  $sql .= " AND appointments.start_date >= ? AND appointments.start_date <= ?";
					  $params[] = $dates[0] . ' 00:00:00';
					  $params[] = $dates[1] . ' 23:59:59';
				  }
			  }
	
			  $appointments = $this->teacher_model->get_by_query($sql, $params);
			  
	
			  // PrÃ©parer le fichier CSV
			  header('Content-Type: text/csv');
			  header('Content-Disposition: attachment;filename="recordings.csv"');
	
	
	
			  $output = fopen('php://output', 'w');
			  fputcsv($output, ['Name', 'Room', 'Class', 'Section', 'Creation Date', 'Duration', 'Recording']);
	
			  foreach ($appointments as &$appointment_reco) {
				$meetingId = $appointment_reco['meeting_id'] ?? null;
				$appointment_reco['recordings'] = [];
		   
				if ($meetingId) {
					// GÃ©nÃ©rer lâ€™URL avec meetingID spÃ©cifique
					// $query = http_build_query(['meetingID' => $meetingId]);
					// $checksum = sha1('getRecordings' . $query . $bbbSecret);
					// $url = $bbbUrl . 'api/getRecordings?' . $query . '&checksum=' . $checksum;
					$params = ['meetingID' => $meetingId];
					$query = http_build_query($params);
					$checksum = sha1('getRecordings' . $query . $bbbSecret);
					$url = $bbbUrl . 'getRecordings?' . $query . '&checksum=' . $checksum;
		
					// Appel de lâ€™API
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
				  $section_label = 'â€”';
				  if (!empty($appointment['section'])) {
					  $section_ids = explode(',', $appointment['section']);
					  $section_names = [];
					  foreach ($section_ids as $id) {
						  $name = $this->teacher_model->get_section_name($id);
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
						  : 'â€”',
					  $recording_url
				  ]);
			  }
	
			  fclose($output);
			  exit;
		  }
		
		  public function delete_appointment_and_recording($appointment_id)
		  {
		
		
			  if (empty($appointment_id)) {
				  session()->setFlashdata('error_message', "ID de l'appointment invalide.");
				  return redirect()->to(site_url('app/Recording'));
				  return;
			  }
	
			  // Appel Ã  la mÃ©thode de suppression dans le modÃ¨le
			  $success = $this->room_model->delete_bbb_recording_by_appointment($appointment_id);
			  die($success);
			  // VÃ©rification du succÃ¨s de la suppression
			  if ($success) {
				  session()->setFlashdata('success_message', 'Appointment et enregistrements supprimÃ©s avec succÃ¨s.');
			  } else {
				  session()->setFlashdata('error_message', "Erreur lors de la suppression de l'enregistrement ou de l'appointment.");
			  }
	
			  // Redirection vers la liste des enregistrements
			  return redirect()->to(site_url('app/Recording'));
		  }
	
		
		  
	
	//START STUDENT ADN ADMISSION section
	public function student($param1 = '', $param2 = '', $param3 = '', $param4 = '', $param5 = '')
  {
    // Keep auth/session context; only clear student list filters.
    session()->remove(['class_id', 'section_id']);
    $page_data['class_id'] = '';
   

    if ($param1 == 'create') {
      $page_data['student_create_classes'] = $this->getTeacherStudentCreateClassesForSchool();
      //form view
      if ($param2 == 'bulk') {
        $page_data['aria_expand'] = 'bulk';
        $page_data['working_page'] = 'create';
        $page_data['folder_name'] = 'student';
        $page_data['page_title'] = 'add_student';
        return view('backend/index', $page_data);
      } elseif ($param2 == 'excel') {
        $page_data['aria_expand'] = 'excel';
        $page_data['working_page'] = 'create';
        $page_data['folder_name'] = 'student';
        $page_data['page_title'] = 'add_student';
        return view('backend/index', $page_data);
      } else {
        $page_data['aria_expand'] = 'single';
        $page_data['working_page'] = 'create';
        $page_data['folder_name'] = 'student';
        $page_data['page_title'] = 'add_student';
        return view('backend/index', $page_data);
      }
    }

    //create to database
    if ($param1 == 'create_single_student') {

      if ($param2 == "submit") {
        header('Content-Type: application/json'); // Force le retour JSON
        $response_from_model = $this->user_model->single_student_create();
        $status = ($response_from_model === true); // Check if the model returned true for success
        //session()->set_flashdata('flash_message', get_phrase('student_added_successfully'));
        // Ajout du token CSRF Ã  la rÃ©ponse
        $response = [
          'status' => $status,
          'message' => $status ? get_phrase('student_added_successfully') : session()->getFlashdata('error'),
          'redirect' => site_url('teacher/student'),
          'csrf' => [
              'name' => csrf_token(),
              'hash' => csrf_hash()
          ]
      ];

      echo json_encode($response);
      exit;
    } else {
        // Load the view with filtered data
        $page_data['class_id'] = html_escape($this->request->getPost('class_id'));
      
        $page_data['working_page'] = 'filter';
        $page_data['folder_name'] = 'student';
        $page_data['page_title'] = 'student_list';
        $page_data = $this->prepareTeacherStudentListDataForView($page_data);

        return view('backend/index', $page_data);
    }
  }else {
    // Nouveau else ajoutÃ© pour la condition parente
    session()->setFlashdata('flash_message', get_phrase('welcome_back'));
  }

    if ($param1 == 'create_bulk_student') {
      $response = $this->user_model->bulk_student_create();
      // echo $response;
      // PrÃ©parer la rÃ©ponse avec un nouveau jeton CSRF
      $csrf = array(
            'csrfName' => csrf_token(),
              'csrfHash' => csrf_hash(),
            );
            
      // Renvoyer la rÃ©ponse avec un nouveau jeton CSRF
      echo json_encode(array('status' => $response, 'csrf' => $csrf));
    }

    if ($param1 == 'create_excel') {
      $response = $this->user_model->excel_create();
      // die($response) ;
      // PrÃ©parer la rÃ©ponse avec un nouveau jeton CSRF
      $csrf = array(
            'csrfName' => csrf_token(),
            'csrfHash' => csrf_hash(),
            );
                
      // Renvoyer la rÃ©ponse avec un nouveau jeton CSRF
      echo json_encode(array('status' => $response, 'csrf' => $csrf));
    }

   // form view
   if ($param1 == 'edit') {
    $student_id = (int) $param2;
    if (!$this->teacherCanAccessStudent($student_id)) {
      return redirect()->to(site_url('app/mentor/student'));
    }
    $page_data['student_id'] = $student_id;
    $page_data['working_page'] = 'edit';
    $page_data['folder_name'] = 'student';
    $page_data['page_title'] = 'update_student_information';
    return view('backend/index', $page_data);
  }

    if ($param1 == 'status') {
      $student_id = (int) $param2;
      $user_id = (int) $param3;
      $new_status = ((int) $param4) === 1 ? 1 : 0;
      if ($student_id <= 0) {
        $student_id = (int) $this->request->getPost('student_id');
      }
      if ($user_id <= 0) {
        $user_id = (int) $this->request->getPost('user_id');
      }
      if ($param4 === '' || $param4 === null) {
        $new_status = ((int) $this->request->getPost('new_status')) === 1 ? 1 : 0;
      }
      $expected_user_id = $this->getAccessibleStudentUserId($student_id);
      $school_id = (int) school_id();
      if ($school_id <= 0) {
        $school_id = (int) (session()->get('active_school_id') ?? session()->get('school_id') ?? 0);
      }

      // PrÃ©parer la rÃ©ponse avec un nouveau jeton CSRF
      $csrf = array(
        'csrfName' => csrf_token(),
        'csrfHash' => csrf_hash(),
      );

      if ($student_id <= 0 || $user_id <= 0 || $expected_user_id <= 0 || $expected_user_id !== $user_id) {
        return $this->response->setJSON(array(
          'status' => false,
          'notification' => get_phrase('action_not_allowed'),
          'csrf' => $csrf
        ));
      }

      $targetUser = $this->db->table('users')
        ->select('id, school_id, status')
        ->where('id', $expected_user_id)
        ->get()
        ->getRowArray();
      if (empty($targetUser)) {
        return $this->response->setJSON(array(
          'status' => false,
          'notification' => get_phrase('action_not_allowed'),
          'csrf' => $csrf
        ));
      }

      $targetUserSchoolId = (int) ($targetUser['school_id'] ?? 0);
      // Keep strict cross-school protection, but allow legacy rows with school_id=0/null.
      if ($school_id > 0 && $targetUserSchoolId > 0 && $targetUserSchoolId !== $school_id) {
        return $this->response->setJSON(array(
          'status' => false,
          'notification' => get_phrase('action_not_allowed'),
          'csrf' => $csrf
        ));
      }

      $updated = (bool) $this->db->table('users')
        ->where('id', $expected_user_id)
        ->update(['status' => $new_status]);

      return $this->response->setJSON(array(
        'status' => $updated,
        'notification' => $updated ? get_phrase('status_has_been_updated') : get_phrase('action_not_allowed'),
        'csrf' => $csrf
      ));
    }

    //updated to database
    if ($param1 == 'updated') {
      $student_id = (int) $param2;
      $user_id = (int) $param3;
      $expected_user_id = $this->getAccessibleStudentUserId($student_id);
      if ($student_id <= 0 || $user_id <= 0 || $expected_user_id <= 0 || $expected_user_id !== $user_id) {
        $csrf = array(
          'csrfName' => csrf_token(),
          'csrfHash' => csrf_hash(),
        );
        return $this->response->setJSON(array(
          'status' => false,
          'notification' => get_phrase('action_not_allowed'),
          'csrf' => $csrf
        ));
      }

      $response = $this->user_model->student_update($student_id, $user_id);
      // echo $response;
         // PrÃ©parer la rÃ©ponse avec un nouveau jeton CSRF
         $csrf = array(
          'csrfName' => csrf_token(),
          'csrfHash' => csrf_hash(),
      );
  
      // Renvoyer la rÃ©ponse avec un nouveau jeton CSRF
       echo json_encode(array('status' => $response, 'csrf' => $csrf));
    }
    //updated to database
    if ($param1 == 'id_card') {
      $page_data['student_id'] = $param2;
      $page_data['folder_name'] = 'student';
      $page_data['page_title'] = 'identity_card';
      $page_data['page_name'] = 'id_card';
      return view('backend/index', $page_data);
    }

    if ($param1 == 'delete') {
      $student_id = (int) $param2;
      $user_id = (int) $param3;
      $expected_user_id = $this->getAccessibleStudentUserId($student_id);
      $school_id = (int) school_id();
      if ($school_id <= 0) {
        $school_id = (int) (session()->get('active_school_id') ?? session()->get('school_id') ?? 0);
      }

      if ($student_id <= 0 || $user_id <= 0 || $expected_user_id <= 0 || $expected_user_id !== $user_id) {
        $csrf = array(
          'csrfName' => csrf_token(),
          'csrfHash' => csrf_hash(),
        );
        return $this->response->setJSON(array(
          'status' => false,
          'notification' => get_phrase('action_not_allowed'),
          'csrf' => $csrf
        ));
      }

      $response = $this->user_model->delete_student($student_id, $user_id, $school_id);
      // echo $response;
       // PrÃ©parer la rÃ©ponse avec un nouveau jeton CSRF
      $csrf = array(
        'csrfName' => csrf_token(),
        'csrfHash' => csrf_hash(),
    );

    header('Content-Type: application/json');
    // Renvoyer la rÃ©ponse avec un nouveau jeton CSRF
     echo json_encode(array(
         'status' => $response, 
         'notification' => get_phrase('student_deleted_successfully'),
         'csrf' => $csrf
     ));
     exit;
    }

    if ($param1 == 'filter') {
            $page_data['class_id'] = ($param2 == '' || $param2 == 'all') ? 'all' : $param2;
          
            $page_data = $this->prepareTeacherStudentListDataForView($page_data);
            $html_content = view('backend/teacher/student/list', $page_data, ['cache' => 0]);
            $csrf = array(
                'csrfName' => csrf_token(),
                'csrfHash' => csrf_hash(),
            );
            $payload = array('html' => $html_content, 'csrf' => $csrf);
            $json = json_encode($payload, JSON_INVALID_UTF8_SUBSTITUTE);
            if ($json === false) {
              log_message('error', 'Teacher::student(filter) JSON encode failed: {error}', [
                'error' => json_last_error_msg(),
              ]);
              $payload['html'] = '';
              $json = json_encode($payload, JSON_INVALID_UTF8_SUBSTITUTE);
            }
            return $this->response
              ->setContentType('application/json')
              ->setBody($json);
        }

        if (empty($param1)) {
            $page_data['class_id'] = 'all';
          
            $page_data['working_page'] = 'filter';
            $page_data['folder_name'] = 'student';
            $page_data['page_title'] = 'student_list';
            $page_data = $this->prepareTeacherStudentListDataForView($page_data);
            return view('backend/index', $page_data);
        }
  }

  private function prepareTeacherStudentListDataForView(array $page_data): array
  {
    $school_id = (int) school_id();
    if ($school_id <= 0) {
      $school_id = (int) (session()->get('active_school_id') ?? session()->get('school_id') ?? 0);
    }
    $class_id = $page_data['class_id'] ?? 'all';
    $school_row = db()->table('schools')->where('id', $school_id)->get()->getRowArray();
    $school_name = (string) ($school_row['name'] ?? '');

    $user_id = session()->get('user_id');
    $teacher = [];
    if ($school_id > 0) {
      $teacher = db()->table('teachers')->where(['user_id' => $user_id, 'school_id' => $school_id])->get()->getRowArray();
    }
    if (empty($teacher)) {
      $teacher = db()->table('teachers')->where(['user_id' => $user_id])->get()->getRowArray();
    }
    $teacher_id = $teacher['id'] ?? null;
    $permitted_class_ids = [];

    if ($teacher_id) {
      $permitted_classes = db()->table('teacher_permissions')->select('class_id')->where('teacher_id', $teacher_id)->groupStart()->where('attendance', 1)->orWhere('marks', 1)->groupEnd()->get()->getResultArray();
      $permitted_class_ids = array_values(array_unique(array_map('intval', array_column($permitted_classes, 'class_id'))));
      // Fallback for migrated data where flag columns can be inconsistent.
      if (empty($permitted_class_ids)) {
        $allPermRows = db()->table('teacher_permissions')->where(['teacher_id' => $teacher_id])->get()->getResultArray();
        $permitted_class_ids = array_values(array_unique(array_map('intval', array_column($allPermRows, 'class_id'))));
      }
    }

    $classes = [];
    if (!empty($permitted_class_ids)) {
      $classes = db()->table('classes')->whereIn('id', $permitted_class_ids)->where('school_id', $school_id)->get()->getResultArray();
    }

    $where = ['school_id' => $school_id];
    $activeSession = active_session();
    if (!empty($activeSession)) {
      $where['session'] = $activeSession;
    }
    if (!empty($class_id) && $class_id !== 'all') {
      if (in_array((int) $class_id, $permitted_class_ids, true)) {
        $where['class_id'] = $class_id;
      } else {
        $page_data['student_list_school_id'] = $school_id;
        $page_data['student_list_school_name'] = $school_name;
        $page_data['student_list_classes'] = $classes;
        $page_data['student_list_rows'] = [];
        $page_data['student_list_count'] = 0;
        $page_data['student_list_active_count'] = 0;
        return $page_data;
      }
    }

    if (empty($permitted_class_ids)) {
      $page_data['student_list_school_id'] = $school_id;
      $page_data['student_list_school_name'] = $school_name;
      $page_data['student_list_classes'] = $classes;
      $page_data['student_list_rows'] = [];
      $page_data['student_list_count'] = 0;
      $page_data['student_list_active_count'] = 0;
      return $page_data;
    }

    $enrols = db()->table('enrols')->select('DISTINCT(student_id), school_id, session')->where($where)->whereIn('class_id', $permitted_class_ids)->get()->getResultArray();
    if (empty($enrols) && !empty($activeSession)) {
      $fallbackWhere = ['school_id' => $school_id];
      $enrols = db()->table('enrols')->select('DISTINCT(student_id), school_id, session')->where($fallbackWhere)->whereIn('class_id', $permitted_class_ids)->get()->getResultArray();
    }
    $rows = [];
    $active_count = 0;

    foreach ($enrols as $enrol) {
      $student = db()->table('students')->where(['id' => $enrol['student_id']])->get()->getRowArray();
      if (empty($student)) {
        continue;
      }
      $user = db()->table('users')->where(['id' => $student['user_id']])->get()->getRowArray();
      if (empty($user)) {
        continue;
      }
      $is_teacher = db()->table('teachers')->where(['user_id' => $student['user_id']])->get()->getRowArray();
      if (!empty($is_teacher)) {
        continue;
      }

      $status = (int) ($user['status'] ?? 0);
      if ($status === 1) {
        $active_count++;
      }

      $student['user_name'] = (string) ($user['name'] ?? '');
      $student['user_status'] = $status;
      $student['user_image'] = $this->user_model->get_user_image($student['user_id']);
      $student['user_id'] = $user['id'];
      $rows[] = $student;
    }

    $page_data['student_list_school_id'] = $school_id;
    $page_data['student_list_school_name'] = $school_name;
    $page_data['student_list_classes'] = $classes;
    $page_data['student_list_rows'] = $rows;
    $page_data['student_list_count'] = count($rows);
    $page_data['student_list_active_count'] = $active_count;
    return $page_data;
  }

  private function getTeacherStudentCreateClassesForSchool(): array
  {
    $school_id = (int) (function_exists('school_id') ? school_id() : 0);
    if ($school_id <= 0) {
      $school_id = (int) (session()->get('active_school_id') ?? session()->get('school_id') ?? 0);
    }

    if ($school_id <= 0) {
      return [];
    }

    $user_id = (int) (session()->get('user_id') ?? 0);
    $teacher = $this->db->table('teachers')
      ->select('id')
      ->where('user_id', $user_id)
      ->where('school_id', $school_id)
      ->get()
      ->getRowArray();
    $teacher_id = (int) ($teacher['id'] ?? 0);

    $builder = $this->db->table('classes')
      ->where('school_id', $school_id)
      ->orderBy('name', 'ASC');

    // If teacher permissions exist, limit classes to allowed ones.
    if ($teacher_id > 0) {
      $permissionRows = $this->db->table('teacher_permissions')
        ->select('class_id')
        ->where('teacher_id', $teacher_id)
        ->where('attendance', 1)
        ->get()
        ->getResultArray();
      $class_ids = array_values(array_unique(array_map('intval', array_column($permissionRows, 'class_id'))));
      if (!empty($class_ids)) {
        $builder->whereIn('id', $class_ids);
      }
    }

    return $builder->get()->getResultArray();
  }

  private function getTeacherPermittedClassIdsForActiveSchool(): array
  {
    $school_id = (int) school_id();
    if ($school_id <= 0) {
      $school_id = (int) (session()->get('active_school_id') ?? session()->get('school_id') ?? 0);
    }
    if ($school_id <= 0) {
      return [];
    }

    $user_id = (int) (session()->get('user_id') ?? 0);
    if ($user_id <= 0) {
      return [];
    }

    $teacher = $this->db->table('teachers')
      ->select('id')
      ->where('user_id', $user_id)
      ->where('school_id', $school_id)
      ->get()
      ->getRowArray();
    $teacher_id = (int) ($teacher['id'] ?? 0);
    if ($teacher_id <= 0) {
      return [];
    }

    $permissionRows = $this->db->table('teacher_permissions')
      ->select('class_id')
      ->where('teacher_id', $teacher_id)
      ->groupStart()
      ->where('attendance', 1)
      ->orWhere('marks', 1)
      ->groupEnd()
      ->get()
      ->getResultArray();

    return array_values(array_unique(array_map('intval', array_column($permissionRows, 'class_id'))));
  }

  private function getAccessibleStudentUserId(int $student_id): int
  {
    if ($student_id <= 0) {
      return 0;
    }

    $school_id = (int) school_id();
    if ($school_id <= 0) {
      $school_id = (int) (session()->get('active_school_id') ?? session()->get('school_id') ?? 0);
    }
    if ($school_id <= 0) {
      return 0;
    }

    $permitted_class_ids = $this->getTeacherPermittedClassIdsForActiveSchool();
    if (empty($permitted_class_ids)) {
      return 0;
    }

    $builder = $this->db->table('students s');
    $builder->select('s.user_id');
    $builder->join('enrols e', 'e.student_id = s.id', 'inner');
    $builder->where('s.id', $student_id);
    $builder->where('s.school_id', $school_id);
    $builder->where('e.school_id', $school_id);
    $builder->whereIn('e.class_id', $permitted_class_ids);
    $builder->limit(1);

    $row = $builder->get()->getRowArray();
    return (int) ($row['user_id'] ?? 0);
  }

  private function teacherCanAccessStudent(int $student_id): bool
  {
    return $this->getAccessibleStudentUserId($student_id) > 0;
  }

	public function teacher($param1 = '', $param2 = '', $param3 = ''){


		if($param1 == 'create'){
			$response = $this->user_model->create_teacher();
			// echo $response;
			// PrÃ©parer la rÃ©ponse avec un nouveau jeton CSRF
			$csrf = array(
				'csrfName' => csrf_token(),
				'csrfHash' => csrf_hash(),
			);
		
			// Renvoyer la rÃ©ponse avec un nouveau jeton CSRF
			echo json_encode(array('status' => $response, 'csrf' => $csrf));
		}

		if($param1 == 'update'){
			$response = $this->user_model->update_teacher($param2);
			// echo $response;
			// PrÃ©parer la rÃ©ponse avec un nouveau jeton CSRF
			$csrf = array(
				'csrfName' => csrf_token(),
				'csrfHash' => csrf_hash(),
			);
		
			// Renvoyer la rÃ©ponse avec un nouveau jeton CSRF
			echo json_encode(array('status' => $response, 'csrf' => $csrf));
		}

		if($param1 == 'delete'){
			$teacher = $this->teacher_model->get_teacher_by_user_id($param2);
			$teacher_id = $teacher['id'] ?? null;
			$response = $this->user_model->delete_teacher($param2, $teacher_id);
			// echo $response;
			// PrÃ©parer la rÃ©ponse avec un nouveau jeton CSRF
			$csrf = array(
				'csrfName' => csrf_token(),
				'csrfHash' => csrf_hash(),
			);
		
			// Renvoyer la rÃ©ponse avec un nouveau jeton CSRF
			echo json_encode(array('status' => $response, 'csrf' => $csrf));
		}

		if ($param1 == 'list') {
			return view('backend/teacher/teacher/list');
		}

		if(empty($param1)){
			$page_data['folder_name'] = 'teacher';
			$page_data['page_title'] = 'techers';
			return view('backend/index', $page_data);
		}
	}
	//END TEACHER section

	//START CLASS secion
	public function manage_class($param1 = '', $param2 = '', $param3 = ''){

		if($param1 == 'create'){
			$response = $this->crud_model->class_create();
			// echo $response;
			// PrÃ©parer la rÃ©ponse avec un nouveau jeton CSRF
			$csrf = array(
			'csrfName' => csrf_token(),
			'csrfHash' => csrf_hash(),
				);
	
			// Renvoyer la rÃ©ponse avec un nouveau jeton CSRF
			echo json_encode(array('status' => $response, 'csrf' => $csrf));
		}

		if($param1 == 'delete'){
			$response = $this->crud_model->class_delete($param2);
			// echo $response;
			       // PrÃ©parer la rÃ©ponse avec un nouveau jeton CSRF
				   $csrf = array(
					'csrfName' => csrf_token(),
					'csrfHash' => csrf_hash(),
				);
			
				// Renvoyer la rÃ©ponse avec un nouveau jeton CSRF
				echo json_encode(array('status' => $response, 'csrf' => $csrf));
		}

		if($param1 == 'update'){
			$response = $this->crud_model->class_update($param2);
			// echo $response;
			       // PrÃ©parer la rÃ©ponse avec un nouveau jeton CSRF
				   $csrf = array(
					'csrfName' => csrf_token(),
					'csrfHash' => csrf_hash(),
				);
			
				// Renvoyer la rÃ©ponse avec un nouveau jeton CSRF
				echo json_encode(array('status' => $response, 'csrf' => $csrf));
		}


		// show data from database
		if ($param1 == 'list') {
			return view('backend/teacher/class/list');
		}

		if(empty($param1)){
			$page_data['folder_name'] = 'class';
			$page_data['page_title'] = 'class';
			return view('backend/index', $page_data);
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
    //   // PrÃ©parer la rÃ©ponse avec un nouveau jeton CSRF
    //   $csrf = array(
    //     'csrfName' => csrf_token(),
    //     'csrfHash' => csrf_hash(),
    //           );
            
    //   // Renvoyer la rÃ©ponse avec un nouveau jeton CSRF
    //   echo json_encode(array('status' => $response, 'csrf' => $csrf));
    // }

    // update language
    // if ($param1 == 'update') {
    //   $response = $this->settings_model->update_language($param2);
    //   // echo $response;
    //   // PrÃ©parer la rÃ©ponse avec un nouveau jeton CSRF
    //   $csrf = array(
    //     'csrfName' => csrf_token(),
    //     'csrfHash' => csrf_hash(),
    //           );
            
    //   // Renvoyer la rÃ©ponse avec un nouveau jeton CSRF
    //   echo json_encode(array('status' => $response, 'csrf' => $csrf));
    // }

    // deleting language
    // if ($param1 == 'delete') {
    //   $response = $this->settings_model->delete_language($param2);
    //   // echo $response;
    //   // PrÃ©parer la rÃ©ponse avec un nouveau jeton CSRF
    //   $csrf = array(
    //     'csrfName' => csrf_token(),
    //     'csrfHash' => csrf_hash(),
    //           );
            
    //   // Renvoyer la rÃ©ponse avec un nouveau jeton CSRF
    //   echo json_encode(array('status' => $response, 'csrf' => $csrf));
    // }

    
	if ($param1 == 'active') {
    $user_id = session()->get('user_id');
    $this->settings_model->update_system_language($user_id, $param2);

    // Get the new language code
    $lang_codes = array(
        'french' => 'fr',
        'english' => 'en',
        'arabic' => 'ar',
        'spanish' => 'es',
        'dutch' => 'nl'
    );
    $new_lang_code = isset($lang_codes[strtolower($param2)]) ? $lang_codes[strtolower($param2)] : 'en';

    // Redirige vers la page prÃ©cÃ©dente avec le nouveau prÃ©fixe de langue
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
    if (!empty($referer)) {
        $redirect_url = $this->_update_url_language_prefix($referer, $new_lang_code);
        return redirect()->to($redirect_url);
    } else {
        return redirect()->to(site_url('app/dashboard'));
    }
}

    // showing the list of language
    // if ($param1 == 'update_phrase') {
    //   $current_editing_language = htmlspecialchars($this->request->getPost('currentEditingLanguage'));
    //   $updatedValue = htmlspecialchars($this->request->getPost('updatedValue'));
    //   $key = htmlspecialchars($this->request->getPost('key'));
    //   saveJSONFile($current_editing_language, $key, $updatedValue);
    //   $response =  $current_editing_language . ' ' . $key . ' ' . $updatedValue;
    //   // PrÃ©parer la rÃ©ponse avec un nouveau jeton CSRF
    //   $csrf = array(
    //     'csrfName' => csrf_token(),
    //     'csrfHash' => csrf_hash(),
    //           );
            
    //   // Renvoyer la rÃ©ponse avec un nouveau jeton CSRF
    //   echo json_encode(array('response' => $response, 'csrf' => $csrf));
    // }

    // GET THE DROPDOWN OF LANGUAGES
    if ($param1 == 'dropdown') {
      return view('backend/teacher/language/dropdown');
    }
    // showing the index file
    if (empty($param1)) {
      $page_data['folder_name'] = 'language';
      $page_data['page_title'] = 'languages';
      return view('backend/index', $page_data);
    }
  }
	public function class_wise_subject($class_id) {

		// PROVIDE A LIST OF SUBJECT ACCORDING TO CLASS ID
		$page_data['class_id'] = $class_id;
		return view('backend/teacher/subject/dropdown', $page_data);
	}
	//END SUBJECT section

	//START SYLLABUS section
	public function syllabus($param1 = '', $param2 = '', $param3 = ''){

		if($param1 == 'create'){
			$modelResponse = $this->crud_model->syllabus_create();
			// PrÃ©parer la rÃ©ponse avec un nouveau jeton CSRF
			$csrf = array(
			  'name' => csrf_token(),
			  'hash' => csrf_hash()
		  );
		  
		  // Fusionner la rÃ©ponse du modÃ¨le avec le CSRF
		  $response = array(
			  'status' => $modelResponse['status'],
			  'notification' => $modelResponse['notification'],
			  'csrf' => $csrf
		  );
		  
		  return $this->response->setJSON($response);
		}

		if($param1 == 'delete'){
			$response = $this->crud_model->syllabus_delete($param2);
			// echo $response;
			       // PrÃ©parer la rÃ©ponse avec un nouveau jeton CSRF
				   $csrf = array(
					'csrfName' => csrf_token(),
					'csrfHash' => csrf_hash(),
				);
			
				// Renvoyer la rÃ©ponse avec un nouveau jeton CSRF
				return $this->response->setJSON(array('status' => $response, 'csrf' => $csrf));
		}

		if($param1 == 'list'){
			$page_data['class_id'] = $param2;
			$page_data['page'] = !empty($param3) && is_numeric($param3) ? (int) $param3 : 1;

			return view('backend/teacher/syllabus/list', $page_data);
		}

		if($param1 == 'download'){
			return $this->syllabus_download($param2);
		}

		if(empty($param1)){
			$page_data['folder_name'] = 'syllabus';
			$page_data['page_title'] = 'syllabus';
			return view('backend/index', $page_data);
		}
	}

	public function syllabus_download($syllabus_id)
	{
		$allowed_user_types = ['teacher'];
		$current_user_type = session()->get('user_type');
		$current_user_id = session()->get('user_id');
		
		if (!$current_user_id || !in_array($current_user_type, $allowed_user_types)) {
			return $this->response->setStatusCode(403)->setJSON([
				'status' => false,
				'notification' => get_phrase('access_denied')
			]);
		}

		$syllabus_id = (int) $syllabus_id;
		$school_id = school_id();
		
		$syllabus = db()->table('syllabuses')
			->where('id', $syllabus_id)
			->where('school_id', $school_id)
			->get()
			->getRowArray();
		
		if (!$syllabus) {
			return $this->response->setStatusCode(404)->setJSON([
				'status' => false,
				'notification' => get_phrase('syllabus_not_found')
			]);
		}

		$file_path = FCPATH . 'uploads/syllabus/' . $syllabus['file'];
		
		if (!file_exists($file_path)) {
			return $this->response->setStatusCode(404)->setJSON([
				'status' => false,
				'notification' => get_phrase('file_not_found')
			]);
		}

		$audit_log_model = new \App\Models\Audit_log_model();
		$audit_log_model->log_syllabus_download(
			$current_user_id,
			$current_user_type,
			$school_id,
			$syllabus_id,
			$syllabus
		);

		return $this->response->download($file_path, null);
	}
	//END SYLLABUS section

	//START CLASS ROUTINE section
	public function routine($param1 = '', $param2 = '', $param3 = '', $param4 = ''){

		if($param1 == 'create'){
			$response = $this->crud_model->routine_create();
			// echo $response;
			// PrÃ©parer la rÃ©ponse avec un nouveau jeton CSRF
			$csrf = array(
				'csrfName' => csrf_token(),
				'csrfHash' => csrf_hash(),
			);
		
			// Renvoyer la rÃ©ponse avec un nouveau jeton CSRF
			echo json_encode(array('status' => $response, 'csrf' => $csrf));
		}

		if($param1 == 'update'){
			$response = $this->crud_model->routine_update($param2);
			// echo $response;
			// PrÃ©parer la rÃ©ponse avec un nouveau jeton CSRF
			$csrf = array(
				'csrfName' => csrf_token(),
				'csrfHash' => csrf_hash(),
			);
		
			// Renvoyer la rÃ©ponse avec un nouveau jeton CSRF
			echo json_encode(array('status' => $response, 'csrf' => $csrf));
		}

		if($param1 == 'delete'){
			$response = $this->crud_model->routine_delete($param2);
			// echo $response;
			// PrÃ©parer la rÃ©ponse avec un nouveau jeton CSRF
			$csrf = array(
				'csrfName' => csrf_token(),
				'csrfHash' => csrf_hash(),
			);
		
			// Renvoyer la rÃ©ponse avec un nouveau jeton CSRF
			echo json_encode(array('status' => $response, 'csrf' => $csrf));
		}

		if($param1 == 'filter'){
			$page_data['class_id'] = $param2;
			return view('backend/teacher/routine/list', $page_data);
		}

		if(empty($param1)){
			$page_data['folder_name'] = 'routine';
			$page_data['page_title'] = 'routine';
			return view('backend/index', $page_data);
		}
	}
	//END CLASS ROUTINE section


	//START DAILY ATTENDANCE section
	public function attendance($param1 = '', $param2 = '', $param3 = ''){

		if($param1 == 'take_attendance'){
			$modelResponse = $this->crud_model->take_attendance();
			// PrÃ©parer la rÃ©ponse avec un nouveau jeton CSRF
			$csrf = array(
			  'name' => csrf_token(),
			  'hash' => csrf_hash()
		  );
		  
		  // Fusionner la rÃ©ponse du modÃ¨le avec le CSRF
		  $response = array(
			  'status' => $modelResponse['status'],
			  'notification' => $modelResponse['notification'],
			  'csrf' => $csrf
		  );
		  
		  return $this->response->setJSON($response);
		}

		if($param1 == 'filter'){
			$month = (string) $this->request->getPost('month');
			$year = (string) $this->request->getPost('year');
			$page_data['attendance_date'] = strtotime('01 ' . $month . ' ' . $year);
			$page_data['class_id'] = htmlspecialchars((string) $this->request->getPost('class_id'));
			$page_data['month'] = htmlspecialchars($month);
			$page_data['year'] = htmlspecialchars($year);

			// Charger la vue mise Ã  jour
			$response_html = view('backend/teacher/attendance/list', $page_data, ['cache' => 0]);
			// PrÃ©parer le nouveau jeton CSRF
			$csrf = array(
			'csrfName' => csrf_token(),
			'csrfHash' => csrf_hash(),
				);
	
			// Renvoyer la rÃ©ponse JSON avec le HTML mis Ã  jour et le nouveau jeton CSRF
			return $this->response->setJSON(array('status' => $response_html, 'csrfName' => $csrf['csrfName'], 'csrfHash' => $csrf['csrfHash']));
		}

		if($param1 == 'student'){
			$page_data['attendance_date'] = strtotime((string) $this->request->getPost('date'));
			$page_data['class_id'] = (int) htmlspecialchars((string) $this->request->getPost('class_id'));

			$classRow = [];
			if ($page_data['class_id'] > 0) {
				$classRow = $this->db->table('classes')
					->select('id, school_id')
					->where('id', $page_data['class_id'])
					->get()
					->getRowArray() ?? [];
			}
			$page_data['attendance_school_id'] = (int) ($classRow['school_id'] ?? school_id());

			$current_user_id = (int) (session()->get('user_id') ?? 0);
			$teacherRows = [];
			if ($current_user_id > 0) {
				$teacherRows = $this->db->table('teachers')
					->select('id')
					->where('user_id', $current_user_id)
					->get()
					->getResultArray();
			}
			$teacherIds = array_values(array_unique(array_map('intval', array_column($teacherRows, 'id'))));

			$allowedClassIds = [];
			if (!empty($teacherIds)) {
				$allowedRows = $this->db->table('teacher_permissions')
					->select('class_id')
					->whereIn('teacher_id', $teacherIds)
					->where('attendance', 1)
					->get()
					->getResultArray();
				$allowedClassIds = array_values(array_unique(array_map('intval', array_column($allowedRows, 'class_id'))));
			}

			$page_data['check_permission'] = (!empty($page_data['class_id']) && in_array((int) $page_data['class_id'], $allowedClassIds, true)) ? 1 : 0;

			      // Charger la vue mise Ã  jour
			$response_html = view('backend/teacher/attendance/student', $page_data, ['cache' => 0]);
			// PrÃ©parer le nouveau jeton CSRF
			$csrf = array(
			'csrfName' => csrf_token(),
			'csrfHash' => csrf_hash(),
			);
	
		// Renvoyer la rÃ©ponse JSON avec le HTML mis Ã  jour et le nouveau jeton CSRF
				return $this->response->setJSON(array('status' => $response_html, 'csrfName' => $csrf['csrfName'], 'csrfHash' => $csrf['csrfHash']));
		}

		if(empty($param1)){
			$school_id = (int) (session()->get('active_school_id') ?? 0);
			if ($school_id <= 0) {
				return redirect()->to(site_url('app/dashboard'));
			}
			$user_id = (int) session()->get('user_id');

			$teacher = $this->db->table('teachers')
				->select('id')
				->where('user_id', $user_id)
				->where('school_id', $school_id)
				->get()
				->getRowArray();
			$teacher_id = (int) ($teacher['id'] ?? 0);
			$permitted_class_ids = [];
			$attendance_classes = [];
			if ($teacher_id > 0) {
				$permissionRows = $this->db->table('teacher_permissions')
					->select('class_id')
					->where('teacher_id', $teacher_id)
					->where('attendance', 1)
					->get()
					->getResultArray();
				$candidate_ids = array_values(array_unique(array_map('intval', array_column($permissionRows, 'class_id'))));

				if (!empty($candidate_ids)) {
					$classRows = $this->db->table('classes')
						->select('id, name')
						->where('school_id', $school_id)
						->whereIn('id', $candidate_ids)
						->get()
						->getResultArray();
					$permitted_class_ids = array_values(array_map('intval', array_column($classRows, 'id')));

					foreach ($classRows as $classRow) {
						$classId = (int) ($classRow['id'] ?? 0);
						if ($classId <= 0) {
							continue;
						}
						$totalStudents = (int) $this->db->table('enrols')
							->where('class_id', $classId)
							->where('school_id', $school_id)
							->countAllResults();

						$attendance_classes[] = [
							'id' => $classId,
							'name' => (string) ($classRow['name'] ?? ''),
							'total_students' => $totalStudents,
						];
					}
				}
			}

			$page_data['permitted_class_ids'] = $permitted_class_ids;
			$page_data['attendance_classes'] = $attendance_classes;
			$page_data['attendance_school_id'] = $school_id;
			$page_data['folder_name'] = 'attendance';
			$page_data['page_title'] = 'attendance';
			return view('backend/index', $page_data);
		}
	}
	//END DAILY ATTENDANCE section


	//START EVENT CALENDAR section
	public function event_calendar($param1 = '', $param2 = ''){

		if($param1 == 'create'){
			$response = $this->crud_model->event_calendar_create();
			// echo $response;
                  // PrÃ©parer la rÃ©ponse avec un nouveau jeton CSRF
				  $csrf = array(
					'csrfName' => csrf_token(),
					'csrfHash' => csrf_hash(),
				);
			
				// Renvoyer la rÃ©ponse avec un nouveau jeton CSRF
				echo json_encode(array('status' => $response, 'csrf' => $csrf));
		}

		if($param1 == 'update'){
			$response = $this->crud_model->event_calendar_update($param2);
			// echo $response;
				// PrÃ©parer la rÃ©ponse avec un nouveau jeton CSRF
				$csrf = array(
				'csrfName' => csrf_token(),
				'csrfHash' => csrf_hash(),
			);
		
			// Renvoyer la rÃ©ponse avec un nouveau jeton CSRF
			echo json_encode(array('status' => $response, 'csrf' => $csrf));
		}

		if($param1 == 'delete'){
			$response = $this->crud_model->event_calendar_delete($param2);
			// echo $response;
                  // PrÃ©parer la rÃ©ponse avec un nouveau jeton CSRF
				  $csrf = array(
					'csrfName' => csrf_token(),
					'csrfHash' => csrf_hash(),
				);
			
				// Renvoyer la rÃ©ponse avec un nouveau jeton CSRF
				echo json_encode(array('status' => $response, 'csrf' => $csrf));
		}

		if($param1 == 'all_events'){
			echo $this->crud_model->all_events();
		}

		if ($param1 == 'list') {
			return view('backend/teacher/event_calendar/list');
		}

		if(empty($param1)){
			$page_data['folder_name'] = 'event_calendar';
			$page_data['page_title'] = 'event_calendar';
			return view('backend/index', $page_data);
		}
	}
	//END EVENT CALENDAR section


	//START EXAM section
	public function exam($param1 = '', $param2 = '')
{
    // Get teacher ID and permitted classes for permission checking
    $user_id = session()->get('user_id');
    $teacher = $this->teacher_model->get_teacher_by_user_id($user_id);
    $teacher_id = $teacher['id'] ?? null;
    
    // Get permitted class IDs from teacher_permissions where attendance = 1
    $permitted_class_ids = [];
    if ($teacher_id) {
        $permitted_classes = $this->teacher_model->get_teacher_classes($teacher_id);
        $permitted_class_ids = array_column($permitted_classes, 'class_id');
    }
    
    if ($param1 == 'create') {
        // Check if teacher has permission for the selected class
        $class_id = esc($this->request->getPost('class_id')) ?? '';
        
        if (!empty($class_id) && !empty($permitted_class_ids)) {
            if (!in_array($class_id, $permitted_class_ids)) {
                // Teacher doesn't have permission for this class
                $output = array(
                    'status' => false,
                    'message' => get_phrase('you_do_not_have_permission_for_this_class'),
                    'csrf' => array(
                        'csrfName' => csrf_token(),
                        'csrfHash' => csrf_hash(),
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
                    'csrfName' => csrf_token(),
                    'csrfHash' => csrf_hash(),
                )
            );
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode($output));
            return;
        }
        
        $response = $this->crud_model->exam_create();
        if (is_array($response)) {
            $response_data = $response;
        } elseif (is_string($response) && json_decode($response, true) !== null) {
            $response_data = json_decode($response, true);
        } else {
            $response_data = [
                'status' => false,
                'notification' => 'Failed to create exam'
            ];
        }
        
        // Ajouter un nouveau jeton CSRF
        $csrf = array(
            'csrfName' => csrf_token(),
            'csrfHash' => csrf_hash(),
        );
        
        // Construire la rÃ©ponse
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
        $exam = $this->teacher_model->get_exam_by_id($param2);
        
        if (!$exam) {
            $output = array(
                'status' => false,
                'message' => 'Exam not found',
                'csrf' => array(
                    'csrfName' => csrf_token(),
                    'csrfHash' => csrf_hash(),
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
                        'csrfName' => csrf_token(),
                        'csrfHash' => csrf_hash(),
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
                    'csrfName' => csrf_token(),
                    'csrfHash' => csrf_hash(),
                )
            );
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode($output));
            return;
        }
        
        // Also check if trying to change to a class without permission
        $new_class_id = esc($this->request->getPost('class_id')) ?? $exam['class_id'];
        if ($new_class_id != $exam['class_id'] && !empty($permitted_class_ids)) {
            if (!in_array($new_class_id, $permitted_class_ids)) {
                // Teacher doesn't have permission for the new class
                $output = array(
                    'status' => false,
                    'message' => get_phrase('you_do_not_have_permission_for_the_selected_class'),
                    'csrf' => array(
                        'csrfName' => csrf_token(),
                        'csrfHash' => csrf_hash(),
                    )
                );
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode($output));
                return;
            }
        }
        
        $response = $this->crud_model->exam_update($param2);
        // Normalize model response (array in CI4, sometimes JSON string in legacy paths).
        if (is_array($response)) {
            $response_data = $response;
        } elseif (is_string($response) && json_decode($response, true) !== null) {
            $response_data = json_decode($response, true);
        } else {
            $response_data = [
                'status' => false,
                'notification' => 'Failed to update exam'
            ];
        }
        
        $class_id = $exam['class_id'] ?? ''; // RÃ©cupÃ©rer l'ID de la classe de l'examen
        
        if ($exam) {
            $startingTs = 0;
            $rawStartingDate = $exam['starting_date'] ?? null;
            if (is_numeric($rawStartingDate)) {
                $startingTs = (int) $rawStartingDate;
            } elseif (is_string($rawStartingDate) && trim($rawStartingDate) !== '') {
                $parsed = strtotime($rawStartingDate);
                $startingTs = $parsed !== false ? (int) $parsed : 0;
            }
            $exam['formatted_date'] = $startingTs > 0 ? date('D, d-M-Y H:i', $startingTs) : 'No Date';
            $class = $this->teacher_model->get_class_by_id($exam['class_id']);
           
            $exam['class_name'] = $class ? $class['name'] : 'No Class';
           
            $output = array(
                'status' => $response_data['status'] ?? false,
                'exam' => $exam,
                'class_id' => $class_id, // Inclure l'ID de la classe
                'message' => $response_data['notification'] ?? 'Failed to update exam',
                'csrf' => array(
                    'csrfName' => csrf_token(),
                    'csrfHash' => csrf_hash(),
                )
            );
        } else {
            $output = array(
                'status' => false,
                'message' => 'Exam not found',
                'csrf' => array(
                    'csrfName' => csrf_token(),
                    'csrfHash' => csrf_hash(),
                )
            );
        }
        
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($output));
    }

    if ($param1 == 'delete') {
        // First check if teacher has permission to delete this exam
        $exam = $this->teacher_model->get_exam_by_id($param2);
        
        if (!$exam) {
            $output = array(
                'status' => false,
                'message' => 'Exam not found',
                'csrf' => array(
                    'csrfName' => csrf_token(),
                    'csrfHash' => csrf_hash(),
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
                        'csrfName' => csrf_token(),
                        'csrfHash' => csrf_hash(),
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
                    'csrfName' => csrf_token(),
                    'csrfHash' => csrf_hash(),
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
        $school_id = school_id();
        $session = active_session();
        $exams_per_page = 10;
        $current_page = 1;

        $user_id = session()->get('user_id');
        $teacher = $this->teacher_model->get_teacher_by_user_id($user_id);
        $teacher_id = $teacher['id'] ?? null;
        $permitted_class_ids = [];
        if ($teacher_id) {
            $permitted_classes = $this->teacher_model->get_teacher_classes($teacher_id);
            $permitted_class_ids = array_column($permitted_classes, 'class_id');
        }

        $page_data['filter_class_id'] = htmlspecialchars((string) $this->request->getPost('class_id'));
        $page_data['filter_date_range'] = htmlspecialchars((string) $this->request->getPost('date_range'));

        $date_from = null;
        $date_to = null;
        if (!empty($page_data['filter_date_range'])) {
            $dates = explode(' - ', $page_data['filter_date_range']);
            if (count($dates) === 2) {
                $date_from = strtotime(trim($dates[0]) . ' 00:00:00');
                $date_to = strtotime(trim($dates[1]) . ' 23:59:59');
            }
        }

        $countBuilder = $this->db->table('exams')
            ->where('school_id', $school_id)
            ->where('session', $session);
        if (!empty($permitted_class_ids)) {
            $countBuilder->whereIn('class_id', $permitted_class_ids);
        } else {
            $countBuilder->where('class_id', 0);
        }
        if (!empty($page_data['filter_class_id'])) {
            $countBuilder->where('class_id', $page_data['filter_class_id']);
        }
        if (!empty($page_data['filter_date_range']) && $date_from && $date_to) {
            $countBuilder->where('starting_date >=', $date_from);
            $countBuilder->where('starting_date <=', $date_to);
        }
        $total_exams = (int) $countBuilder->countAllResults();

        $examBuilder = $this->db->table('exams');
        $examBuilder->select('exams.*, classes.name as class_name')
            ->join('classes', 'exams.class_id = classes.id', 'left')
            ->where('exams.school_id', $school_id)
            ->where('exams.session', $session);
        if (!empty($permitted_class_ids)) {
            $examBuilder->whereIn('exams.class_id', $permitted_class_ids);
        } else {
            $examBuilder->where('exams.class_id', 0);
        }
        if (!empty($page_data['filter_class_id'])) {
            $examBuilder->where('exams.class_id', $page_data['filter_class_id']);
        }
        if (!empty($page_data['filter_date_range']) && $date_from && $date_to) {
            $examBuilder->where('exams.starting_date >=', $date_from);
            $examBuilder->where('exams.starting_date <=', $date_to);
        }
        $exams = $examBuilder->orderBy('exams.id', 'DESC')->limit($exams_per_page, 0)->get()->getResultArray();

        $classesBuilder = $this->db->table('classes')->where('school_id', $school_id);
        if (!empty($permitted_class_ids)) {
            $classesBuilder->whereIn('id', $permitted_class_ids);
        } else {
            $classesBuilder->where('id', 0);
        }
        $classes = $classesBuilder->get()->getResultArray();

        $page_data['classes'] = $classes;
        $page_data['exams'] = $exams;
        $page_data['total_exams'] = $total_exams;
        $page_data['exams_per_page'] = $exams_per_page;
        $page_data['current_page'] = $current_page;
        $page_data['total_pages'] = $exams_per_page > 0 ? (int) ceil($total_exams / $exams_per_page) : 1;
        return view('backend/teacher/exam/list', $page_data);
    }

    if (empty($param1)) {
        $page_data['folder_name'] = 'exam';
        $page_data['page_title'] = 'Certifications';
        return view('backend/index', $page_data);
    }
}
	//END EXAM section
	public function filter_exams()
{
    header('Content-Type: application/json');
    
    if (session()->get('teacher_login') != 1) {
        echo json_encode(['error' => 'Unauthorized']);
        exit;
    }

    $school_id = school_id();
    $session = active_session();

    // Get teacher ID
    $user_id = session()->get('user_id');
    $teacher = $this->teacher_model->get_teacher_by_user_id($user_id);
    $teacher_id = $teacher['id'] ?? null;
    
    if (!$teacher_id) {
        echo json_encode(['error' => 'No teacher associated with this user']);
        exit;
    }
    
    // Get permitted class IDs from teacher_permissions where attendance = 1
    $permitted_classes = $this->teacher_model->get_teacher_classes($teacher_id);
    $permitted_class_ids = array_column($permitted_classes, 'class_id');

    $class_id = $this->request->getPost('class_id');

    $date_range = $this->request->getPost('date_range');
    $date_from = '';
    $date_to = '';

    if (!empty($date_range)) {
        $dates = explode(' - ', $date_range);
        $date_from = strtotime(trim($dates[0]) . ' 00:00:00');
        $date_to = strtotime(trim($dates[1]) . ' 23:59:59');
    }

    $sql = "SELECT exams.*, classes.name as class_name FROM exams LEFT JOIN classes ON exams.class_id = classes.id WHERE exams.school_id = ? AND exams.session = ?";
    $params = [$school_id, $session];
    
    // Filter by permitted classes only
    if (!empty($permitted_class_ids)) {
        $sql .= " AND exams.class_id IN (" . implode(',', array_fill(0, count($permitted_class_ids), '?')) . ")";
        $params = array_merge($params, $permitted_class_ids);
    } else {
        $sql .= " AND 1 = 0";
    }
    
    if (!empty($class_id)) {
        $sql .= " AND exams.class_id = ?";
        $params[] = $class_id;
    }
 
    if (!empty($date_range)) {
        $sql .= " AND exams.starting_date >= ? AND exams.starting_date <= ?";
        $params[] = $date_from;
        $params[] = $date_to;
    }
    $sql .= " ORDER BY exams.starting_date DESC";
    $exams = $this->teacher_model->get_by_query($sql, $params);

    $exam_data = [];
    foreach ($exams as $exam) {
        $startingTs = 0;
        if (isset($exam['starting_date'])) {
            $rawStartingDate = $exam['starting_date'];
            if (is_numeric($rawStartingDate)) {
                $startingTs = (int) $rawStartingDate;
            } elseif (is_string($rawStartingDate) && trim($rawStartingDate) !== '') {
                $parsed = strtotime($rawStartingDate);
                $startingTs = $parsed !== false ? (int) $parsed : 0;
            }
        }
        $exam_data[] = [
            'id' => $exam['id'],
            'name' => $exam['name'] ?: 'Unnamed Exam',
            'formatted_date' => $startingTs > 0 ? date('D, d-M-Y H:i', $startingTs) : 'No Date',
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
    
    if (session()->get('teacher_login') != 1) {
        echo json_encode(['status' => false, 'error' => 'Unauthorized']);
        exit;
    }

    $school_id = school_id();
    $session = active_session();
    
    // Get teacher ID
    $user_id = session()->get('user_id');
    $teacher = $this->teacher_model->get_teacher_by_user_id($user_id);
    $teacher_id = $teacher['id'] ?? null;
    
    if (!$teacher_id) {
        echo json_encode(['status' => false, 'error' => 'No teacher associated with this user']);
        exit;
    }
    
    // Get permitted class IDs from teacher_permissions where attendance = 1
    $permitted_classes = $this->teacher_model->get_teacher_classes($teacher_id);
    $permitted_class_ids = array_column($permitted_classes, 'class_id');
    
    $page = (int) esc($this->request->getGet('page')) ?? 1;
    $limit = 10; // Exams per page
    $offset = ($page - 1) * $limit;
    
    // Get filters if any
    $class_id = $this->request->getGet('class_id');
    $date_range = $this->request->getGet('date_range');
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
    $count_where = "school_id = '" . $school_id . "' AND session = '" . $session . "'";
    
    // Filter by permitted classes only
    if (!empty($permitted_class_ids)) {
        $count_where .= " AND class_id IN (" . implode(',', array_map('intval', $permitted_class_ids)) . ")";
    } else {
        $count_where .= " AND 1 = 0";
    }
    
    if (!empty($class_id)) {
        $count_where .= " AND class_id = " . intval($class_id);
    }
    if (!empty($date_range) && $date_from && $date_to) {
        $count_where .= " AND starting_date >= '" . $date_from . "' AND starting_date <= '" . $date_to . "'";
    }
    
    $count_result = $this->teacher_model->get_by_query("SELECT COUNT(*) as cnt FROM exams WHERE " . $count_where, []);
    $total_exams = $count_result[0]['cnt'] ?? 0;
    $total_pages = ceil($total_exams / $limit);

    $sql = "SELECT exams.*, classes.name as class_name FROM exams LEFT JOIN classes ON exams.class_id = classes.id WHERE exams.school_id = ? AND exams.session = ?";
    $params = [$school_id, $session];
    
    // Filter by permitted classes only
    if (!empty($permitted_class_ids)) {
        $sql .= " AND exams.class_id IN (" . implode(',', array_fill(0, count($permitted_class_ids), '?')) . ")";
        $params = array_merge($params, $permitted_class_ids);
    } else {
        $sql .= " AND 1 = 0";
    }
    
    if (!empty($class_id)) {
        $sql .= " AND exams.class_id = ?";
        $params[] = $class_id;
    }
    if (!empty($date_range) && $date_from && $date_to) {
        $sql .= " AND exams.starting_date >= ? AND exams.starting_date <= ?";
        $params[] = $date_from;
        $params[] = $date_to;
    }
    $sql .= " ORDER BY exams.id DESC LIMIT " . intval($limit) . " OFFSET " . intval($offset);
    $exams = $this->teacher_model->get_by_query($sql, $params);

    $exam_data = [];
    foreach ($exams as $exam) {
        $startingTs = 0;
        if (isset($exam['starting_date'])) {
            $rawStartingDate = $exam['starting_date'];
            if (is_numeric($rawStartingDate)) {
                $startingTs = (int) $rawStartingDate;
            } elseif (is_string($rawStartingDate) && trim($rawStartingDate) !== '') {
                $parsed = strtotime($rawStartingDate);
                $startingTs = $parsed !== false ? (int) $parsed : 0;
            }
        }
        $exam_data[] = [
            'id' => $exam['id'],
            'name' => $exam['name'] ?: 'Unnamed Exam',
            'formatted_date' => $startingTs > 0 ? date('D, d-M-Y H:i', $startingTs) : 'No Date',
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
            'csrfName' => csrf_token(),
            'csrfHash' => csrf_hash()
        ]
    ]);
}

	//START MARKS section
	public function mark($param1 = '', $param2 = ''){

		if($param1 == 'list'){
			$page_data['class_id'] = htmlspecialchars($this->request->getPost('class_id'));
			// $page_data['subject_id'] = htmlspecialchars($this->request->getPost('subject'));
			$page_data['exam_id'] = htmlspecialchars($this->request->getPost('exam'));
			$this->crud_model->mark_insert($page_data['class_id'], $page_data['exam_id']);
			$school_id = school_id();
			$page_data['marks'] = $this->crud_model->get_marks($page_data['class_id'], $page_data['exam_id'], $school_id)->getResultArray();
			$page_data['exam_details'] = $this->db->table('exams')->where('id', $page_data['exam_id'])->get()->getRowArray() ?? [];
			$page_data['class_name'] = (string) ($this->db->table('classes')->select('name')->where('id', $page_data['class_id'])->get()->getRow('name') ?? '');
			$student_ids = array_values(array_unique(array_map('intval', array_column($page_data['marks'], 'student_id'))));
			$page_data['student_names'] = [];
			if (!empty($student_ids)) {
				$rows = $this->db->table('students s')
					->select('s.id, u.name')
					->join('users u', 'u.id = s.user_id', 'left')
					->whereIn('s.id', $student_ids)
					->get()
					->getResultArray();
				foreach ($rows as $row) {
					$page_data['student_names'][(int) $row['id']] = (string) ($row['name'] ?? '');
				}
			}
			$page_data['check_permission'] = has_permission($page_data['class_id'], 'marks');
			// Charger la vue et capturer le contenu
			$html_content = view('backend/teacher/mark/list', $page_data, ['cache' => 0]);
				
			// PrÃ©parer le nouveau jeton CSRF
			$csrf = array(
				'csrfName' => csrf_token(),
				'csrfHash' => csrf_hash(),
			);
			
			// Renvoyer la rÃ©ponse JSON avec le HTML et le nouveau jeton CSRF
			echo json_encode(array('html' => $html_content, 'csrf' => $csrf));
		}

		if($param1 == 'mark_update'){
			$this->crud_model->mark_update();
				// PrÃ©parer le nouveau jeton CSRF
				$csrf = array(
				'csrfName' => csrf_token(),
				'csrfHash' => csrf_hash(),
			);
			
			// Renvoyer la rÃ©ponse JSON avec le nouveau jeton CSRF
		
			echo json_encode(array('csrf' => $csrf));
		}

		if(empty($param1)){
			$school_id = school_id();
			$user_id = (int) session()->get('user_id');
			$active_school_id = (int) (session()->get('active_school_id') ?? 0);
			if ($active_school_id <= 0) {
				$active_school_id = (int) $school_id;
			}
			$teacher = [];
			if ($active_school_id > 0) {
				$teacher = $this->db->table('teachers')
					->select('id')
					->where('user_id', $user_id)
					->where('school_id', $active_school_id)
					->get()
					->getRowArray() ?? [];
			}
			if (empty($teacher)) {
				$teacher = $this->db->table('teachers')
					->select('id')
					->where('user_id', $user_id)
					->get()
					->getRowArray() ?? [];
			}
			$teacher_id = $teacher['id'] ?? null;
			$permitted_class_ids = [];
			if ($teacher_id) {
				$permitted_classes = $this->db->table('teacher_permissions')
					->select('class_id')
					->where('teacher_id', $teacher_id)
					->where('marks', 1)
					->get()
					->getResultArray();
				$permitted_class_ids = array_column($permitted_classes, 'class_id');
			}

			$page_data['mark_exams'] = $this->db->table('exams')
				->where('school_id', $school_id)
				->where('session', active_session())
				->get()
				->getResultArray();
			$page_data['mark_classes'] = [];
			if (!empty($permitted_class_ids)) {
				$classes = $this->db->table('classes')
					->where('school_id', $school_id)
					->whereIn('id', $permitted_class_ids)
					->get()
					->getResultArray();
				foreach ($classes as $class) {
					$total_students = (int) $this->db->table('enrols')
						->where('class_id', $class['id'])
						->where('school_id', $school_id)
						->countAllResults();
					$page_data['mark_classes'][] = [
						'id' => $class['id'],
						'name' => $class['name'],
						'total_students' => $total_students,
					];
				}
			}

			$page_data['folder_name'] = 'mark';
			$page_data['page_title'] = 'marks';
			return view('backend/index', $page_data);
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
			// PrÃ©parer la rÃ©ponse avec un nouveau jeton CSRF
			$csrf = array(
			'csrfName' => csrf_token(),
			'csrfHash' => csrf_hash(),
			);
	
			// Renvoyer la rÃ©ponse avec un nouveau jeton CSRF
			echo json_encode(array('status' => $response, 'csrf' => $csrf));
		}

		// update book
		if ($param1 == 'update') {
			$response = $this->crud_model->update_book($param2);
			// echo $response;
			// PrÃ©parer la rÃ©ponse avec un nouveau jeton CSRF
			$csrf = array(
				'csrfName' => csrf_token(),
				'csrfHash' => csrf_hash(),
				);
		
				// Renvoyer la rÃ©ponse avec un nouveau jeton CSRF
				echo json_encode(array('status' => $response, 'csrf' => $csrf));
		}

		// deleting book
		if ($param1 == 'delete') {
			$response = $this->crud_model->delete_book($param2);
			// echo $response;
			// PrÃ©parer la rÃ©ponse avec un nouveau jeton CSRF
			$csrf = array(
				'csrfName' => csrf_token(),
				'csrfHash' => csrf_hash(),
				);
		
				// Renvoyer la rÃ©ponse avec un nouveau jeton CSRF
				echo json_encode(array('status' => $response, 'csrf' => $csrf));
		}
		// showing the list of book
		if ($param1 == 'list') {
			return view('backend/teacher/book/list');
		}

		// showing the index file
		if(empty($param1)){
			$page_data['folder_name'] = 'book';
			$page_data['page_title']  = 'books';
			return view('backend/index', $page_data);
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
			return view('backend/index', $page_data);
		}
	}
	//MANAGE PROFILE ENDS

	public function calendar($param1 = '', $param2 = '', $param3 = '', $param4 = '') {
    if (session()->get('teacher_login') != 1) {
        $csrf = [
            'csrfName' => csrf_token(),
            'csrfHash' => csrf_hash(),
        ];
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized', 'csrf' => $csrf]);
        exit;
    }

    if ($param1 == 'create') {
        return $this->create_event();
    }

    if ($param1 == 'update') {
        return $this->update_event();
    }

    if ($param1 == 'delete') {
        return $this->delete_event();
    }

    if ($param1 == 'get_events') {
        return $this->get_events();
    }

    if ($param1 == 'get_user_school') {
        return $this->get_user_school();
    }

    if ($param1 == 'get_school_data') {
        return $this->get_school_data();
    }

    if ($param1 == 'start_meeting') {
        return $this->start_meeting();
    }

    if ($param1 == 'filter') {
        $page_data['class_id'] = $param2;
        $page_data['start_date'] = $param3;
        $page_data['end_date'] = $param4;
        return view('backend/teacher/calendar/list', $page_data);
    }

    if (empty($param1)) {
        $page_data['folder_name'] = 'calendar';
        $page_data['page_title'] = 'calendar';
        return view('backend/index', $page_data);
    }

    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid calendar action',
        'csrf' => [
            'csrfName' => csrf_token(),
            'csrfHash' => csrf_hash()
        ]
    ]);
}

public function get_user_school() {
    if (session()->get('teacher_login') != 1) {
        echo json_encode(['status' => 'error', 'message' => 'Session expired, please login again']);
        exit;
    }

    $user_id = session()->get('user_id');
    $user = $this->teacher_model->get_user_by_id($user_id);

    if (!$user || empty($user['school_id'])) {
        echo json_encode(['status' => 'error', 'message' => 'No school associated with this user']);
        exit;
    }

    $school = $this->teacher_model->get_active_school($user['school_id']);
    if (!$school) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid school']);
        exit;
    }

    $csrf = array(
        'csrfName' => csrf_token(),
        'csrfHash' => csrf_hash(),
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
    if (session()->get('teacher_login') != 1) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Unauthorized',
            'csrf' => [
                'csrfName' => csrf_token(),
                'csrfHash' => csrf_hash()
            ]
        ]);
        return;
    }

    $teacher_id = session()->get('user_id'); // Get the logged-in teacher's ID
    $data = [
        'title' => htmlspecialchars($this->request->getPost('title')),
        'starting_date' => $this->request->getPost('starting_date'),
        'starting_time' => $this->request->getPost('starting_time'),
        'ending_date' => esc($this->request->getPost('ending_date')) ?? null,
        'ending_time' => $this->request->getPost('ending_time'),
        'school_id' => filter_var($this->request->getPost('school_id'), FILTER_VALIDATE_INT),
        'session' => $this->request->getPost('session'),
        'description' => htmlspecialchars($this->request->getPost('description')),
        'recurrence_type' => $this->request->getPost('recurrence_type'),
        'recurrence_end_date' => $this->request->getPost('recurrence_end_date'),
        'custom_recurrence' => $this->request->getPost('custom_recurrence'),
        'visio' => !empty($this->request->getPost('visio')) && $this->request->getPost('visio') == 1 ? 1 : 0,
        'created_by' => $teacher_id
    ];

    // Validation des champs obligatoires
    if (empty($data['title']) || empty($data['starting_date']) || empty($data['starting_time']) || empty($data['ending_time']) || empty($data['school_id'])) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Tous les champs obligatoires doivent Ãªtre remplis',
            'csrf' => [
                'csrfName' => csrf_token(),
                'csrfHash' => csrf_hash()
            ]
        ]);
        return;
    }

    // Validation du format de la date (strictement YYYY-MM-DD)
    $start_date_obj = DateTime::createFromFormat('Y-m-d', $data['starting_date']);
    if ($start_date_obj === false || $start_date_obj->format('Y-m-d') !== $data['starting_date']) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Format de date de dÃ©but invalide (attendu : YYYY-MM-DD)',
            'csrf' => [
                'csrfName' => csrf_token(),
                'csrfHash' => csrf_hash()
            ]
        ]);
        return;
    }
    $data['starting_date'] = $start_date_obj->format('Y-m-d');

    // VÃ©rifier que la date de dÃ©but n'est pas dans le passÃ©
    $now = new DateTime('now', new DateTimeZone('UTC'));
    $event_start = DateTime::createFromFormat('Y-m-d H:i:s', $data['starting_date'] . ' ' . $data['starting_time'], new DateTimeZone('UTC'));
    if ($event_start === false) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Format de date et heure de dÃ©but invalide',
            'csrf' => [
                'csrfName' => csrf_token(),
                'csrfHash' => csrf_hash()
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
                    'csrfName' => csrf_token(),
                    'csrfHash' => csrf_hash()
                ]
            ]);
            return;
        }
        $data['ending_date'] = $end_date_obj->format('Y-m-d');

        // Validation: ending_date must be on or after starting_date
        if ($end_date_obj < $start_date_obj) {
            echo json_encode([
                'status' => 'error',
                'message' => 'La date de fin doit Ãªtre postÃ©rieure ou Ã©gale Ã  la date de dÃ©but',
                'csrf' => [
                    'csrfName' => csrf_token(),
                    'csrfHash' => csrf_hash()
                ]
            ]);
            return;
        }
    }

    if ($event_start < $now) {
        echo json_encode([
            'status' => 'error',
            'message' => 'La date et l\'heure de dÃ©but ne peuvent pas Ãªtre dans le passÃ©',
            'csrf' => [
                'csrfName' => csrf_token(),
                'csrfHash' => csrf_hash()
            ]
        ]);
        return;
    }

    // Validation des heures (strictement HH:mm:ss)
    if (!preg_match('/^([01][0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/', $data['starting_time'])) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Format d\'heure de dÃ©but invalide (attendu : HH:mm:ss)',
            'csrf' => [
                'csrfName' => csrf_token(),
                'csrfHash' => csrf_hash()
            ]
        ]);
        return;
    }
    if (!preg_match('/^([01][0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/', $data['ending_time'])) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Format d\'heure de fin invalide (attendu : HH:mm:ss)',
            'csrf' => [
                'csrfName' => csrf_token(),
                'csrfHash' => csrf_hash()
            ]
        ]);
        return;
    }

    // Validation de school_id
    if (!$this->teacher_model->school_exists($data['school_id'])) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Ã‰cole invalide',
            'csrf' => [
                'csrfName' => csrf_token(),
                'csrfHash' => csrf_hash()
            ]
        ]);
        return;
    }

    // Handle participants
    $participants = json_decode($this->request->getPost('participants'), true);
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
                'csrfName' => csrf_token(),
                'csrfHash' => csrf_hash()
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
                    'csrfName' => csrf_token(),
                    'csrfHash' => csrf_hash()
                ]
            ]);
            return;
        }
        if ($participant['type'] === 'class') {
            if (!$this->teacher_model->class_exists($participant['id'], $data['school_id'])) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Classe invalide pour cette Ã©cole : ' . $participant['id'],
                    'csrf' => [
                        'csrfName' => csrf_token(),
                        'csrfHash' => csrf_hash()
                    ]
                ]);
                return;
            }
        } elseif ($participant['type'] === 'individual') {
            if (!$this->teacher_model->user_exists($participant['id'])) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Utilisateur invalide : ' . $participant['id'],
                    'csrf' => [
                        'csrfName' => csrf_token(),
                        'csrfHash' => csrf_hash()
                    ]
                ]);
                return;
            }
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Type de participant invalide : ' . $participant['type'],
                'csrf' => [
                    'csrfName' => csrf_token(),
                    'csrfHash' => csrf_hash()
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
                'csrfName' => csrf_token(),
                'csrfHash' => csrf_hash()
            ]
        ]);
        return;
    }

    // Validation de la rÃ©currence
    if (!in_array($data['recurrence_type'], ['does_not_repeat', 'every_weekday', 'daily', 'weekly', 'monthly', 'yearly'])) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Type de rÃ©currence invalide',
            'csrf' => [
                'csrfName' => csrf_token(),
                'csrfHash' => csrf_hash()
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
                    'message' => 'Format de date de fin de rÃ©currence invalide (attendu : YYYY-MM-DD)',
                    'csrf' => [
                        'csrfName' => csrf_token(),
                        'csrfHash' => csrf_hash()
                    ]
                ]);
                return;
            }
            if ($recurrence_end_date_obj < $start_date_obj) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'La date de fin de rÃ©currence doit Ãªtre postÃ©rieure Ã  la date de dÃ©but',
                    'csrf' => [
                        'csrfName' => csrf_token(),
                        'csrfHash' => csrf_hash()
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
                'message' => 'La rÃ©currence personnalisÃ©e doit Ãªtre un tableau JSON valide pour la rÃ©currence hebdomadaire',
                'csrf' => [
                    'csrfName' => csrf_token(),
                    'csrfHash' => csrf_hash()
                ]
            ]);
            return;
        }
        $validDays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        if (array_diff($decoded, $validDays)) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Les jours dans la rÃ©currence personnalisÃ©e doivent Ãªtre des jours valides',
                'csrf' => [
                    'csrfName' => csrf_token(),
                    'csrfHash' => csrf_hash()
                ]
            ]);
            return;
        }
    } elseif ($data['recurrence_type'] !== 'weekly') {
        $data['custom_recurrence'] = null;
    }

    try {
        $event_id = $this->teacher_model->insert_table('event_calendars', $data);
        // Insert participants into participants table
        foreach ($participants as $participant) {
            $this->teacher_model->insert_table('participants', [
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
                'csrfName' => csrf_token(),
                'csrfHash' => csrf_hash()
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
                'csrfName' => csrf_token(),
                'csrfHash' => csrf_hash()
            ]
        ]);
    }
}

    public function update_event() {
        if (session()->get('teacher_login') != 1) {
            $csrf = [
                'csrfName' => csrf_token(),
                'csrfHash' => csrf_hash(),
            ];
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized', 'csrf' => $csrf]);
            exit;
        }

        $event_id = filter_var($this->request->getPost('id'), FILTER_VALIDATE_INT);
        if (!$event_id) {
            $csrf = [
                'csrfName' => csrf_token(),
                'csrfHash' => csrf_hash(),
            ];
            echo json_encode(['status' => 'error', 'message' => 'Event ID is required', 'csrf' => $csrf]);
            exit;
        }

        // VÃ©rifier si l'Ã©vÃ©nement existe
        if (!$this->teacher_model->event_exists($event_id)) {
            $csrf = [
                'csrfName' => csrf_token(),
                'csrfHash' => csrf_hash(),
            ];
            echo json_encode(['status' => 'error', 'message' => get_phrase('Event not found'), 'csrf' => $csrf]);
            exit;
        }

        $data = [
            'title' => htmlspecialchars($this->request->getPost('title')),
            'starting_date' => $this->request->getPost('starting_date'),
            'starting_time' => $this->request->getPost('starting_time'),
			'ending_date' => esc($this->request->getPost('ending_date')) ?? null,
            'ending_time' => $this->request->getPost('ending_time'),
            'school_id' => filter_var($this->request->getPost('school_id'), FILTER_VALIDATE_INT),
            'session' => $this->request->getPost('session'),
            'description' => htmlspecialchars($this->request->getPost('description')),
            'recurrence_type' => $this->request->getPost('recurrence_type'),
            'recurrence_end_date' => $this->request->getPost('recurrence_end_date'),
            'custom_recurrence' => $this->request->getPost('custom_recurrence'),
            'visio' => !empty($this->request->getPost('visio')) && $this->request->getPost('visio') == 1 ? 1 : 0
        ];

        $csrf = [
            'csrfName' => csrf_token(),
            'csrfHash' => csrf_hash(),
        ];

        // Validation des champs obligatoires
        if (empty($data['title']) || empty($data['starting_date']) || empty($data['starting_time']) || empty($data['ending_time']) || empty($data['school_id'])) {
            echo json_encode(['status' => 'error', 'message' => 'Tous les champs obligatoires doivent Ãªtre remplis', 'csrf' => $csrf]);
            exit;
        }

        // Validation du format de la date (strictement YYYY-MM-DD)
        $start_date_obj = DateTime::createFromFormat('Y-m-d', $data['starting_date']);
        if ($start_date_obj === false || $start_date_obj->format('Y-m-d') !== $data['starting_date']) {
            echo json_encode(['status' => 'error', 'message' => 'Format de date de dÃ©but invalide (attendu : YYYY-MM-DD)', 'csrf' => $csrf]);
            exit;
        }
        $data['starting_date'] = $start_date_obj->format('Y-m-d');

        $now = new DateTime('now', new DateTimeZone('UTC'));
        $event_start = DateTime::createFromFormat('Y-m-d H:i:s', $data['starting_date'] . ' ' . $data['starting_time'], new DateTimeZone('UTC'));
        if ($event_start === false) {
            echo json_encode(['status' => 'error', 'message' => 'Format de date et heure de dÃ©but invalide', 'csrf' => $csrf]);
            exit;
        }
        if ($event_start < $now) {
            echo json_encode(['status' => 'error', 'message' => 'La date et l\'heure de dÃ©but ne peuvent pas Ãªtre dans le passÃ©', 'csrf' => $csrf]);
            exit;
        }
		if ($data['ending_date']) {
    $end_date_obj = DateTime::createFromFormat('Y-m-d', $data['ending_date']);
    if ($end_date_obj === false || $end_date_obj->format('Y-m-d') !== $data['ending_date']) {
        echo json_encode(['status' => 'error', 'message' => 'Format de date de fin invalide (attendu : YYYY-MM-DD)', 'csrf' => $csrf]);
        exit;
    }
    $data['ending_date'] = $end_date_obj->format('Y-m-d');

    // Validation: ending_date must be on or after starting_date
    if ($end_date_obj < $start_date_obj) {
        echo json_encode(['status' => 'error', 'message' => 'La date de fin doit Ãªtre postÃ©rieure ou Ã©gale Ã  la date de dÃ©but', 'csrf' => $csrf]);
        exit;
    }
}
        // Validation des heures (strictement HH:mm:ss)
        if (!preg_match('/^([01][0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/', $data['starting_time'])) {
            echo json_encode(['status' => 'error', 'message' => 'Format d\'heure de dÃ©but invalide (attendu : HH:mm:ss)', 'csrf' => $csrf]);
            exit;
        }
        if (!preg_match('/^([01][0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/', $data['ending_time'])) {
            echo json_encode(['status' => 'error', 'message' => 'Format d\'heure de fin invalide (attendu : HH:mm:ss)', 'csrf' => $csrf]);
            exit;
        }


        // Validation de school_id
        if (!$this->teacher_model->school_exists($data['school_id'])) {
            echo json_encode(['status' => 'error', 'message' => 'Ã‰cole invalide', 'csrf' => $csrf]);
            exit;
        }

        // Validation de class_id
        $participants = json_decode($this->request->getPost('participants'), true);
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
                if (!$this->teacher_model->class_exists($participant['id'], $data['school_id'])) {
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'Classe invalide pour cette Ã©cole : ' . $participant['id'],
                        'csrf' => $csrf
                    ]);
                    return;
                }
            } elseif ($participant['type'] === 'individual') {
                if (!$this->teacher_model->user_exists($participant['id'])) {
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

        // Validation de la rÃ©currence
        if (!in_array($data['recurrence_type'], ['does_not_repeat', 'every_weekday', 'daily', 'weekly', 'monthly', 'yearly'])) {
            echo json_encode(['status' => 'error', 'message' => 'Type de rÃ©currence invalide', 'csrf' => $csrf]);
            exit;
        }

        if ($data['recurrence_type'] !== 'does_not_repeat') {
            if (!empty($data['recurrence_end_date'])) {
                $recurrence_end_date_obj = DateTime::createFromFormat('Y-m-d', $data['recurrence_end_date']);
                if ($recurrence_end_date_obj === false || $recurrence_end_date_obj->format('Y-m-d') !== $data['recurrence_end_date']) {
                    echo json_encode(['status' => 'error', 'message' => 'Format de date de fin de rÃ©currence invalide (attendu : YYYY-MM-DD)', 'csrf' => $csrf]);
                    exit;
                }
                if ($recurrence_end_date_obj < $start_date_obj) {
                    echo json_encode(['status' => 'error', 'message' => 'La date de fin de rÃ©currence doit Ãªtre postÃ©rieure Ã  la date de dÃ©but', 'csrf' => $csrf]);
                    exit;
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
                echo json_encode(['status' => 'error', 'message' => 'La rÃ©currence personnalisÃ©e doit Ãªtre un tableau JSON valide pour la rÃ©currence hebdomadaire', 'csrf' => $csrf]);
                exit;
            }
            $validDays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
            if (array_diff($decoded, $validDays)) {
                echo json_encode(['status' => 'error', 'message' => 'Les jours dans la rÃ©currence personnalisÃ©e doivent Ãªtre des jours valides', 'csrf' => $csrf]);
                exit;
            }
        } elseif ($data['recurrence_type'] !== 'weekly') {
            $data['custom_recurrence'] = null;
        }

        try {
            $this->teacher_model->update_event($event_id, $data);
            $this->teacher_model->update_table('appointments', ['event_id' => $event_id], [
                'title' => $data['title'],
                'start_date' => $data['starting_date'],
                'school_id' => $data['school_id'],
                'visio' => $data['visio']
            ]);
            $this->teacher_model->delete_participants_by_event($event_id);
            foreach ($participants as $participant) {
                $this->teacher_model->insert_table('participants', [
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
        if (session()->get('teacher_login') != 1) {
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
            exit;
        }

        $event_id = filter_var($this->request->getPost('id'), FILTER_VALIDATE_INT);
        if (!$event_id) {
            echo json_encode(['status' => 'error', 'message' => 'Event ID is required']);
            exit;
        }

        // VÃ©rifier si l'Ã©vÃ©nement existe
        if (!$this->teacher_model->event_exists($event_id)) {
            echo json_encode(['status' => 'error', 'message' => get_phrase('Event not found')]);
            exit;
        }

        try {
            // Terminer les rÃ©unions BBB associÃ©es
            $appointments = $this->teacher_model->get_appointments_by_event($event_id);
            foreach ($appointments as $appointment) {
                $existing_meeting = $this->teacher_model->get_session_meeting_by_appointment($appointment['id']);
                if ($existing_meeting && $existing_meeting['meeting_id']) {
                    $bbbConfig = config('Bigbluebutton');
                    $bbb_url = $bbbConfig->bbb_url ?? '';
                    $bbb_secret = $bbbConfig->bbb_secret ?? '';
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

            $this->teacher_model->delete_table('appointments', ['event_id' => $event_id]);
            $this->teacher_model->delete_participants_by_event($event_id);
            $this->teacher_model->delete_event($event_id);

            $csrf = [
                'csrfName' => csrf_token(),
                'csrfHash' => csrf_hash(),
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
    // VÃ©rifier l'authentification de l'utilisateur
    if (session()->get('teacher_login') != 1) {
        $csrf = [
            'csrfName' => csrf_token(),
            'csrfHash' => csrf_hash(),
        ];
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized', 'csrf' => $csrf]);
        exit;
    }

    // RÃ©cupÃ©rer l'ID de l'utilisateur connectÃ©
    $user_id = session()->get('user_id');
    
    // Mapper user_id Ã  teacher_id
    $teacher = $this->teacher_model->get_row('teachers', ['user_id' => $user_id]);
    $teacher_id = $teacher['id'] ?? null;

    if (!$teacher_id) {
        $csrf = [
            'csrfName' => csrf_token(),
            'csrfHash' => csrf_hash(),
        ];
        echo json_encode(['status' => 'error', 'message' => 'No teacher associated with this user', 'csrf' => $csrf]);
        exit;
    }

    // RÃ©cupÃ©rer les classes autorisÃ©es avec attendance = 1
    $permitted_classes = $this->teacher_model->get_where_result('teacher_permissions', ['teacher_id' => $teacher_id, 'attendance' => 1], 'class_id');
    $permitted_class_ids = array_column($permitted_classes, 'class_id');


    // RÃ©cupÃ©rer l'ID de l'Ã©cole
    $user_details = $this->user_model->get_user_details($user_id);
    $school_id = $user_details['school_id'] ?? null;

    if (!$school_id) {
        $csrf = [
            'csrfName' => csrf_token(),
            'csrfHash' => csrf_hash(),
        ];
        echo json_encode(['status' => 'error', 'message' => 'No school associated with this user', 'csrf' => $csrf]);
        exit;
    }

    $start_date = $this->request->getGet('start_date');
    $end_date = $this->request->getGet('end_date');
    $event_id = $this->request->getGet('id');
    $class_id = $this->request->getGet('class_id');

    // Validation des dates
    if ($start_date && $end_date) {
        try {
            $start_date_obj = new DateTime($start_date);
            $end_date_obj = new DateTime($end_date);
            $start_date = $start_date_obj->format('Y-m-d');
            $end_date = $end_date_obj->format('Y-m-d');
        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Invalid date format (expected: YYYY-MM-DD)',
                'csrf' => [
                    'csrfName' => csrf_token(),
                    'csrfHash' => csrf_hash()
                ]
            ]);
            exit;
        }
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'start_date and end_date are required',
            'csrf' => [
                'csrfName' => csrf_token(),
                'csrfHash' => csrf_hash()
            ]
        ]);
        exit;
    }

    // Construction de la requÃªte pour les Ã©vÃ©nements
    $sql = "SELECT event_calendars.*, schools.name as school_name, users.name as created_by_name FROM event_calendars LEFT JOIN schools ON event_calendars.school_id = schools.id LEFT JOIN users ON event_calendars.created_by = users.id WHERE event_calendars.school_id = ?";
    $params = [$school_id];

    if ($event_id) {
        $sql .= " AND event_calendars.id = ?";
        $params[] = $event_id;
    } else {
        $sql .= " AND (event_calendars.starting_date <= ? AND (event_calendars.ending_date >= ? OR event_calendars.ending_date IS NULL))";
        $params[] = $end_date;
        $params[] = $start_date;
    }

    $sql .= " GROUP BY event_calendars.id";
    $events = $this->teacher_model->get_by_query($sql, $params);

    $participantsByEventId = [];
    if ($events !== []) {
        $allParticipantsRows = $this->teacher_model->get_participants_for_events_batch(array_column($events, 'id'));
        foreach ($allParticipantsRows as $prow) {
            $participantsByEventId[$prow['event_id']][] = $prow;
        }
    }

    $batchClassIds = [];
    $batchUserIds = [];
    foreach ($events as $evt) {
        $plist = $participantsByEventId[$evt['id']] ?? [];
        $canSee = false;
        foreach ($plist as $p) {
            if ($p['type'] === 'class' && in_array($p['guest'], $permitted_class_ids)) {
                $canSee = true;
                break;
            }
            if ($p['type'] === 'individual' && $p['guest'] == $user_id) {
                $canSee = true;
                break;
            }
        }
        if (!$canSee) {
            continue;
        }
        foreach ($plist as $p) {
            if ($p['type'] === 'class') {
                $batchClassIds[] = (int) $p['guest'];
            } elseif ($p['type'] === 'individual') {
                $batchUserIds[] = (int) $p['guest'];
            }
        }
    }
    $classMap = $this->teacher_model->get_classes_map_by_ids($batchClassIds);
    $userMap = $this->teacher_model->get_users_map_by_ids($batchUserIds);

    // Charger la configuration BigBlueButton
    $bbbConfig = config('Bigbluebutton');
    $bbb_url = $bbbConfig->bbb_url ?? '';
    $bbb_secret = $bbbConfig->bbb_secret ?? '';

    // Post-traitement des Ã©vÃ©nements
    $processed_events = [];
    $now = new DateTime('now', new DateTimeZone('UTC'));
    $threshold = (clone $now)->modify('-24 hours');
    foreach ($events as $event) {
        $participants = $participantsByEventId[$event['id']] ?? [];

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
                $class = $classMap[(int) $p['guest']] ?? null;
                $data['name'] = $class['name'] ?? 'Unknown';
            } else {
                $user = $userMap[(int) $p['guest']] ?? null;
                $data['name'] = $user['name'] ?? 'Unknown';
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

        // VÃ©rifier si l'Ã©vÃ©nement est expirÃ©
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
            $sql = "SELECT start_date, meeting_id FROM appointments WHERE event_id = ? AND DATE(start_date) >= ? AND DATE(start_date) <= ?";
            $appointments = $this->teacher_model->get_by_query($sql, [$event['id'], $start_date, $end_date]);

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
            'csrfName' => csrf_token(),
            'csrfHash' => csrf_hash()
        ]
    ]);
    exit;
}

public function start_meeting() {
    // VÃ©rifier l'authentification
    if (session()->get('teacher_login') != 1) {
        $csrf = [
            'csrfName' => csrf_token(),
            'csrfHash' => csrf_hash(),
        ];
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized', 'csrf' => $csrf]);
        exit;
    }

    // RÃ©cupÃ©rer et valider les paramÃ¨tres
    $event_id = filter_var($this->request->getPost('event_id'), FILTER_VALIDATE_INT);
    $occurrence_date = $this->request->getPost('occurrence_date');
    if (!$event_id || !$occurrence_date) {
        $csrf = [
            'csrfName' => csrf_token(),
            'csrfHash' => csrf_hash(),
        ];
        echo json_encode(['status' => 'error', 'message' => 'Event ID and occurrence date are required', 'csrf' => $csrf]);
        exit;
    }

    // Valider le format de la date
    $occurrence_date_obj = DateTime::createFromFormat('Y-m-d', $occurrence_date);
    if ($occurrence_date_obj === false || $occurrence_date_obj->format('Y-m-d') !== $occurrence_date) {
        $csrf = [
            'csrfName' => csrf_token(),
            'csrfHash' => csrf_hash(),
        ];
        echo json_encode(['status' => 'error', 'message' => 'Invalid occurrence date format (expected: YYYY-MM-DD)', 'csrf' => $csrf]);
        exit;
    }
    $occurrence_date = $occurrence_date_obj->format('Y-m-d');

    // Charger l'Ã©vÃ©nement
    $event = $this->teacher_model->get_event_by_id($event_id);
    if (!$event) {
        $csrf = [
            'csrfName' => csrf_token(),
            'csrfHash' => csrf_hash(),
        ];
        echo json_encode(['status' => 'error', 'message' => 'Event not found', 'csrf' => $csrf]);
        exit;
    }

    // VÃ©rifier si l'Ã©vÃ©nement est en visio
    if ($event['visio'] != 1) {
        $csrf = [
            'csrfName' => csrf_token(),
            'csrfHash' => csrf_hash(),
        ];
        echo json_encode(['status' => 'error', 'message' => 'Video conferencing is not enabled for this event', 'csrf' => $csrf]);
        exit;
    }

    // DÃ©sactiver les autres appointments pour cet Ã©vÃ©nement
    $this->teacher_model->update_table('appointments', [
        'event_id' => $event_id,
        'DATE(start_date) !=' => $occurrence_date,
        'Etat' => 1
    ], ['Etat' => 0]);
    
    // VÃ©rifier si un appointment actif existe pour cette occurrence
    $appointment = $this->teacher_model->get_by_query(
        "SELECT * FROM appointments WHERE event_id = ? AND DATE(start_date) = ? AND `Etat` = 1 LIMIT 1",
        [$event_id, $occurrence_date]
    );
    $appointment = $appointment ? $appointment[0] : null;
    $appointment_id = $appointment ? $appointment['id'] : null;

    // Charger la configuration BigBlueButton
    $bbbConfig = config('Bigbluebutton');
    $bbb_url = $bbbConfig->bbb_url ?? '';
    $bbb_secret = $bbbConfig->bbb_secret ?? '';

    // Si un appointment existe et le meeting est en cours, le rejoindre
    if ($appointment && $appointment['meeting_id']) {
        $meeting = $this->teacher_model->get_session_meeting_by_appointment($appointment_id);
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
                    'csrfName' => csrf_token(),
                    'csrfHash' => csrf_hash(),
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

                $user_details = $this->user_model->get_user_details(session()->get('user_id'));
                $full_name = urlencode($user_details['name'] ?? 'Teacher-' . rand(1000, 9999));
                $join_params = "fullName=$full_name&meetingID=" . urlencode($appointment['meeting_id']) . "&password=" . $meeting['moderator_pw'] . "&redirect=true";
                $join_checksum = sha1("join" . $join_params . $bbb_secret);
                $join_url = $bbb_url . "join?" . $join_params . "&checksum=" . $join_checksum;

                $participants = $this->teacher_model->get_where_result('appointment_participants', ['appointment_id' => $appointment_id], 'guest, type');

                $csrf = [
                    'csrfName' => csrf_token(),
                    'csrfHash' => csrf_hash(),
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
                $this->teacher_model->update_appointment($appointment_id, ['Etat' => 0]);
            }
        }
    }

    $participants = $this->teacher_model->get_where_result('participants', ['event_id' => $event_id], 'guest, type');

    // CrÃ©er un nouvel appointment pour cette occurrence
    $appointment_data = [
        'event_id' => $event_id,
        'title' => $event['title'],
        'start_date' => $occurrence_date . ' ' . $event['starting_time'],
        'school_id' => $event['school_id'],
        'visio' => $event['visio'],
        'Etat' => 1,
        'meeting_id' => null
    ];
    $appointment_id = $this->teacher_model->insert_table('appointments', $appointment_data);
    foreach ($participants as $participant) {
        $this->teacher_model->insert_table('appointment_participants', [
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
            'csrfName' => csrf_token(),
            'csrfHash' => csrf_hash(),
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
            'csrfName' => csrf_token(),
            'csrfHash' => csrf_hash(),
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
            'csrfName' => csrf_token(),
            'csrfHash' => csrf_hash(),
        ];
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to create meeting: Invalid response from BBB server',
            'csrf' => $csrf
        ]);
        return;
    }

    if ((string)$xml->returncode === "SUCCESS") {
        // Enregistrer les dÃ©tails du meeting
        $meeting_data = [
            'meeting_id' => $new_meeting_id,
            'appointment_id' => $appointment_id,
            'name' => $meeting_name,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'attendee_pw' => $attendee_password,
            'moderator_pw' => $moderator_password,
            'school_id' => $event['school_id'],
            'user_id' => session()->get('user_id'),
            'class_id' => null,
            'created_at' => date('Y-m-d H:i:s'),
            'description' => null
        ];
        $this->teacher_model->insert_table('sessions_meetings', $meeting_data);

        // Mettre Ã  jour l'appointment avec le nouveau meeting_id
        $this->teacher_model->update_appointment($appointment_id, ['meeting_id' => $new_meeting_id]);

        // VÃ©rifier si le meeting est en cours
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

        // GÃ©nÃ©rer l'URL de jointure
        $user_details = $this->user_model->get_user_details(session()->get('user_id'));
        $full_name = urlencode($user_details['name'] ?? 'Teacher-' . rand(1000, 9999));
        $join_params = "fullName=$full_name&meetingID=" . urlencode($new_meeting_id) . "&password=$moderator_password&redirect=true";
        $join_checksum = sha1("join" . $join_params . $bbb_secret);
        $join_url = $bbb_url . "join?" . $join_params . "&checksum=" . $join_checksum;

        $participants = $this->teacher_model->get_where_result('appointment_participants', ['appointment_id' => $appointment_id], 'guest, type');

        $csrf = [
            'csrfName' => csrf_token(),
            'csrfHash' => csrf_hash(),
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
            'csrfName' => csrf_token(),
            'csrfHash' => csrf_hash(),
        ];
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to create meeting: ' . (string)$xml->message,
            'csrf' => $csrf
        ]);
    }
}

public function get_classes_by_school() {
    if (session()->get('teacher_login') != 1) {
        $csrf = [
            'csrfName' => csrf_token(),
            'csrfHash' => csrf_hash(),
        ];
        echo json_encode(['status' => 'error', 'message' => 'Session expired, please login again', 'csrf' => $csrf]);
        exit;
    }

    $school_id = $this->request->getPost('school_id');
    if (empty($school_id)) {
        $csrf = [
            'csrfName' => csrf_token(),
            'csrfHash' => csrf_hash(),
        ];
        echo json_encode(['classes' => [], 'status' => 'error', 'message' => 'Aucun ID d\'Ã©cole fourni', 'csrf' => $csrf]);
        exit;
    }

    $user_id = session()->get('user_id');

    // Mapper user_id Ã  teacher_id
    $teacher = $this->teacher_model->get_row('teachers', ['user_id' => $user_id]);
    $teacher_id = $teacher['id'] ?? null;

    if (!$teacher_id) {
        $csrf = [
            'csrfName' => csrf_token(),
            'csrfHash' => csrf_hash(),
        ];
        echo json_encode(['status' => 'error', 'message' => 'Aucun enseignant associÃ© Ã  cet utilisateur', 'csrf' => $csrf]);
        exit;
    }

    // RÃ©cupÃ©rer les classes autorisÃ©es avec attendance = 1
    $permitted_classes = $this->teacher_model->get_where_result('teacher_permissions', ['teacher_id' => $teacher_id, 'attendance' => 1], 'class_id');
    $permitted_class_ids = array_column($permitted_classes, 'class_id');

    if (empty($permitted_class_ids)) {
        $csrf = [
            'csrfName' => csrf_token(),
            'csrfHash' => csrf_hash(),
        ];
        echo json_encode(['classes' => [], 'status' => 'success', 'message' => 'Aucune classe autorisÃ©e pour cet enseignant', 'csrf' => $csrf]);
        exit;
    }

    // VÃ©rifier si les class_id existent dans la table classes
    $classes = $this->teacher_model->get_where_in('classes', 'id', $permitted_class_ids, ['school_id' => $school_id], 'id, name');

    $csrf = [
        'csrfName' => csrf_token(),
        'csrfHash' => csrf_hash(),
    ];

    echo json_encode([
        'status' => 'success',
        'classes' => $classes,
        'message' => empty($classes) ? 'Aucune classe valide trouvÃ©e pour votre Ã©cole' : '',
        'csrf' => $csrf
    ]);
}

public function get_school_data() {
    if (session()->get('teacher_login') != 1) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Unauthorized',
            'csrf' => [
                'csrfName' => csrf_token(),
                'csrfHash' => csrf_hash()
            ]
        ]);
        return;
    }

    $school_id = filter_var($this->request->getPost('school_id'), FILTER_VALIDATE_INT);
    if (!$school_id || !$this->teacher_model->school_exists($school_id)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Ã‰cole invalide',
            'csrf' => [
                'csrfName' => csrf_token(),
                'csrfHash' => csrf_hash()
            ]
        ]);
        return;
    }

    try {
        // RÃ©cupÃ©rer l'ID de l'enseignant connectÃ©
        $current_user_id = session()->get('user_id');
        $teacher = $this->teacher_model->get_teacher_by_user_id($current_user_id);
        $teacher_id = $teacher['id'] ?? null;
        
        // RÃ©cupÃ©rer les classes autorisÃ©es pour cet enseignant (attendance = 1)
        $permitted_class_ids = [];
        if ($teacher_id) {
            $permitted_classes = $this->teacher_model->get_teacher_classes($teacher_id);
            $permitted_class_ids = array_column($permitted_classes, 'class_id');
        }
        
        // RÃ©cupÃ©rer les classes
        if (!empty($permitted_class_ids)) {
            $classes = $this->teacher_model->get_where_in('classes', 'id', $permitted_class_ids, ['school_id' => $school_id], 'id, name');
        } else {
            $classes = [];
        }

        // RÃ©cupÃ©rer l'ID du superadmin connectÃ©
        $current_user_id = session()->get('user_id');

        // RÃ©cupÃ©rer les participants envoyÃ©s dans la requÃªte
        $participants = json_decode($this->request->getPost('participants'), true);
        $class_ids = [];

        if (is_array($participants) && !empty($participants)) {
            foreach ($participants as $participant) {
                if (isset($participant['type']) && $participant['type'] === 'class' && isset($participant['id'])) {
                    $class_ids[] = $participant['id'];
                }
            }
        }

        $users = [];

        // 1. RÃ©cupÃ©rer les Ã©tudiants
        $sql_students = "SELECT DISTINCT(u.id), u.name, 'student' as type, u.role, u.status as user_status, s.status as student_status FROM users u INNER JOIN students s ON s.user_id = u.id INNER JOIN enrols e ON e.student_id = s.id WHERE e.school_id = ? AND u.status = 1 AND s.status = 1 AND u.id != ?";
        $params_students = [$school_id, $current_user_id];
        if (!empty($class_ids)) {
            $sql_students .= " AND e.class_id NOT IN (" . implode(',', array_fill(0, count($class_ids), '?')) . ")";
            $params_students = array_merge($params_students, $class_ids);
        }
        $sql_students .= " ORDER BY u.name ASC";
        $students = $this->teacher_model->get_by_query($sql_students, $params_students);
        $users = array_merge($users, $students);

        // 2. RÃ©cupÃ©rer les enseignants
        $sql_teachers = "SELECT DISTINCT(u.id), u.name, 'teacher' as type, u.role FROM users u INNER JOIN teachers t ON t.user_id = u.id WHERE t.school_id = ? AND u.status = 1 ORDER BY u.name ASC";
        $teachers = $this->teacher_model->get_by_query($sql_teachers, [$school_id]);
        $users = array_merge($users, $teachers);

        // 3. RÃ©cupÃ©rer les admins et superadmins
        $sql_admins = "SELECT DISTINCT(u.id), u.name, u.role as type, u.role FROM users u WHERE u.school_id = ? AND u.status = 1 AND u.role IN ('admin', 'superadmin') AND u.id != ? ORDER BY u.name ASC";
        $admins = $this->teacher_model->get_by_query($sql_admins, [$school_id, $current_user_id]);
        $users = array_merge($users, $admins);

        // Ã‰liminer les doublons basÃ©s sur l'ID
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
                'csrfName' => csrf_token(),
                'csrfHash' => csrf_hash()
            ]
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Erreur lors de la rÃ©cupÃ©ration des donnÃ©es',
            'csrf' => [
                'csrfName' => csrf_token(),
                'csrfHash' => csrf_hash()
            ]
        ]);
    }
}
}
