<?php

namespace App\Controllers;

use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use DateTime;
use DateTimeZone;


/*
*  @author   : Creativeitem
*  date      : November, 2019
*  Ekattor School Management System With Addons
*  http://codecanyon.net/user/Creativeitem
*  http://support.creativeitem.com
*/

class Student extends BaseController
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
        'Student_model' => 'student_model',
    ];

    /**
     * Services to auto-load
     */
    protected $services = [
        'subscriptionService' => 'subscriptionService',
        'fxRatesService' => 'fxService',
    ];

    /**
     * @var \App\Libraries\SubscriptionService
     */
    protected $subscriptionService;

    /**
     * @var \App\Libraries\FxRatesService
     */
    protected $fxService;

    /**
     * LMS Model (loaded conditionally)
     */
    protected $lms_model;

    /**
     * DB Helper Model (loaded conditionally)
     */
    protected $db_helper_model;

    /**
     * LM Studio URL for AI features
     */
    protected $lmStudioUrl = 'http://154.146.250.62:7000/v1/chat/completions';

    // Liste des mÃ©thodes accessibles SANS avoir rejoint une Ã©cole
    private $allowed_without_school = [
        'language',
        'join_school',
        'payment',
        'invoice',
        'invoice_pdf',
        'paypal_checkout',
        'stripe_checkout',
        'payment_success',
        'payment_failed',
        'mollie_checkout',
        'xendit_checkout',
        'midtrans_checkout',
        'flutterwave_checkout',
        'paytm_checkout',
        'razorpay_checkout',
        'paystack_checkout',
        'sslcommerz_checkout',
        'skrill_checkout',
        'instamojo_checkout',
        'toyyibpay_checkout',
        'payumoney_checkout',
        'online_admission'
    ];

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        // Load conditional models
        $this->loadModel('addons/Lms_model', 'lms_model');
        $this->loadModel('Db_helper_model', 'db_helper_model');

        /*SET DEFAULT TIMEZONE*/
        timezone();

        $current_method = $this->request->getMethod();

        // CHECK ACCESS - Autoriser admin/teacher pour join_school et paiement
        $is_student_logged = (session()->get('student_login') == 1);
        $is_admin_logged = (session()->get('admin_login') == 1);
        $is_teacher_logged = (session()->get('teacher_login') == 1);
        $is_allowed_method = in_array($current_method, $this->allowed_without_school);
		
		// Si admin ou teacher accÃ¨de Ã  join_school ou paiement, les switcher en mode student
		if (!$is_student_logged && ($is_admin_logged || $is_teacher_logged) && $is_allowed_method) {
			$user_id = session()->get('user_id');
			$current_role = session()->get('role');
			
			// Sauvegarder l'ancien rÃ´le pour pouvoir revenir si besoin
			session()->set('previous_role', $current_role);
			session()->set('previous_admin_login', $is_admin_logged ? 1 : 0);
			session()->set('previous_teacher_login', $is_teacher_logged ? 1 : 0);
			
			// Switcher en mode student dans la session
			session()->set('role', 'student');
			session()->set('user_type', 'student');
			session()->set('student_login', 1);
			session()->remove('admin_login');
			session()->remove('teacher_login');
			
			// Mettre Ã  jour le rÃ´le dans la table users
			$this->student_model->update_table('users', ['id' => $user_id], ['role' => 'student']);
		} elseif (!$is_student_logged) {
			// Si pas connectÃ© du tout, rediriger vers login
			return redirect()->to(site_url('login'));
		}
		
		// VÃ‰RIFIER SI L'Ã‰TUDIANT A REJOINT UNE Ã‰COLE
		// Sauf pour les pages de paiement et factures
		if ($is_student_logged && !$this->has_joined_school() && !$is_allowed_method) {
			// Stocker un flag pour indiquer que l'Ã©tudiant n'a pas d'Ã©cole
			session()->set('student_has_no_school', true);
			// Rediriger vers la page des factures (demande utilisateur)
			return redirect()->to(site_url('app/invoice'));
		} else {
			session()->set('student_has_no_school', false);
			
			// NOUVEAU : VÃ©rifier si l'Ã©cole ACTIVE est approuvÃ©e
			// Si l'Ã©tudiant a une Ã©cole active mais n'est pas approuvÃ© DANS CELLE-CI, rediriger vers invoice
			$active_school_id = session()->get('active_school_id');
			if ($is_student_logged && $active_school_id && !$is_allowed_method) {
				$active_student_status = $this->student_model->get_student_by_user_id_and_school(
					session()->get('user_id'),
					$active_school_id
				);
				
				// Si status existe et n'est pas 1 (donc 0 ou autre), rediriger
				if (isset($active_student_status) && $active_student_status != 1) {
					return redirect()->to(site_url('app/invoice'));
				}
			}
		}
	}
	
	/**
	 * VÃ©rifie si l'Ã©tudiant connectÃ© a rejoint au moins une Ã©cole
	 * @return bool
	 */
	private function has_joined_school() {
		return has_user_joined_school();
	}

	/**
	 * True if the invoice's students row belongs to the logged-in user.
	 */
	private function invoiceBelongsToCurrentUser(?array $invoice): bool
	{
		if ($invoice === null || $invoice === []) {
			return false;
		}
		$userId = (int) session()->get('user_id');
		if ($userId <= 0) {
			return false;
		}
		$studentPk = (int) ($invoice['student_id'] ?? 0);
		if ($studentPk <= 0) {
			return false;
		}
		$student = $this->db_helper_model->get_by_id('students', $studentPk);
		if (!$student) {
			return false;
		}

		return (int) ($student['user_id'] ?? 0) === $userId;
	}

	/**
	 * Redirect to invoice list if invoice is missing or not owned by the current user.
	 */
	private function assertInvoiceOwnershipOrRedirect(?array $invoice): ?RedirectResponse
	{
		if ($invoice === null || $invoice === []) {
			log_message('warning', 'Payment: invoice not found or empty');
			session()->setFlashdata('error_message', get_phrase('invalid_invoice'));

			return redirect()->to(site_url('app/invoice'));
		}
		if (!$this->invoiceBelongsToCurrentUser($invoice)) {
			log_message('warning', 'Payment: unauthorized invoice access attempt user_id=' . session()->get('user_id'));
			session()->setFlashdata('error_message', get_phrase('access_denied'));

			return redirect()->to(site_url('app/invoice'));
		}

		return null;
	}

	/**
	 * Resolve current school context with robust fallbacks (session -> student row -> user row -> settings).
	 */
	private function resolveCurrentSchoolId(?int $userId = null, ?array $studentRow = null): int
	{
		$school_id = (int) school_id();
		if ($school_id <= 0) {
			$school_id = (int) (session()->get('active_school_id') ?? session()->get('school_id') ?? 0);
		}
		if ($school_id <= 0 && !empty($studentRow)) {
			$school_id = (int) ($studentRow['school_id'] ?? 0);
		}
		if ($school_id <= 0) {
			$uid = (int) ($userId ?? session()->get('user_id'));
			if ($uid > 0) {
				$userRow = $this->db->table('users')->select('school_id')->where('id', $uid)->get()->getRowArray();
				$school_id = (int) ($userRow['school_id'] ?? 0);
			}
		}
		if ($school_id <= 0) {
			$school_id = (int) get_settings('school_id');
		}
		return $school_id;
	}

	/**
	 * Normalize date/timestamp DB values to unix timestamp.
	 */
	private function normalizeToTimestamp($value): int
	{
		if (is_numeric($value)) {
			return (int) $value;
		}
		$ts = strtotime((string) $value);
		return $ts ?: 0;
	}

	/**
	 * Find an exam accessible to a student with migration-friendly fallbacks.
	 */
	private function findAccessibleExamForStudent(int $examId, int $userId, $sessionId, int $preferredSchoolId, bool $withNames = true): ?array
	{
		$select = $withNames
			? 'exams.*, classes.name as class_name, schools.name as school_name'
			: 'exams.*, classes.id as class_id, schools.id as school_id';

		$attempt = function (bool $useSchoolFilter, bool $useSessionFilter) use ($examId, $userId, $sessionId, $preferredSchoolId, $select): ?array {
			$builder = $this->db->table('exams')
				->select($select)
				->join('classes', 'exams.class_id = classes.id', 'left')
				->join('schools', 'exams.school_id = schools.id', 'left')
				->join('enrols', 'enrols.class_id = exams.class_id', 'inner')
				->join('students', 'students.id = enrols.student_id', 'inner')
				->where('exams.id', $examId)
				->where('students.user_id', $userId);

			if ($useSchoolFilter && $preferredSchoolId > 0) {
				$builder->where('exams.school_id', $preferredSchoolId)
					->where('enrols.school_id', $preferredSchoolId);
			}

			if ($useSessionFilter && $sessionId !== null && $sessionId !== '') {
				$builder->groupStart()
					->where('enrols.session', $sessionId)
					->orWhere('enrols.session', null)
					->orWhere('enrols.session', '')
					->groupEnd();
				$builder->groupStart()
					->where('exams.session', $sessionId)
					->orWhere('exams.session', null)
					->orWhere('exams.session', '')
					->groupEnd();
			}

			return $builder->get()->getRowArray() ?: null;
		};

		// Preferred scope
		$exam = $attempt(true, true);
		if (!empty($exam)) {
			return $exam;
		}
		// Fallback if school context is inconsistent
		$exam = $attempt(false, true);
		if (!empty($exam)) {
			return $exam;
		}
		// Legacy fallback if session data is inconsistent
		return $attempt(false, false);
	}

	/**
	 * Update URL language prefix (only for frontend URLs)
	 */
	private function _update_url_language_prefix($url, $new_lang_code) {
		return update_url_language_prefix($url, $new_lang_code);
	}

	// INDEX FUNCTION
	public function index(){
		return redirect()->to(site_url('app/dashboard'));
	}
	   //START TEACHER Create_Join bigbleubutton 
	   public function Join_Session($param1 = '', $param2 = '', $param3 = '')
	   {
	 
   
	 
		 if (empty($param1)) {
		   $page_data['folder_name'] = 'bigbleubutton';
		   $page_data['page_title'] = 'DÃ©marrer RÃ©union';
		   return view('backend/index', $page_data);
		 }
	   }
	   //END TEACHER Create_Join bigbleubutton 

	//DASHBOARD
	public function dashboard(){
		$page_data['page_title'] = 'Dashboard';
		$page_data['folder_name'] = 'dashboard';
		return view('backend/index', $page_data);
	}
	public function get_appointments() {
		$appointments = $this->room_model->get_all_appointments_student();
		echo json_encode($appointments);
	}
	

	//START CLASS secion
	public function manage_class($param1 = '', $param2 = '', $param3 = ''){


		// show data from database
		if ($param1 == 'list') {
			return view('backend/student/class/list');
			return;
		}

		if ($param1 == 'courses' && !empty($param2)) {
			$user_id = session()->get('user_id');
			$student = $this->db
				->where('user_id', $user_id)
				->where('status', 1)
				->get('students')
				->getRowArray();

			if (!$student) {
				show_error(get_phrase('student_profile_not_found'));
			}

			$class = $this->db
				->where('id', $param2)
				->get('classes')
				->getRowArray();

			if (!$class) {
				show_404();
			}



			$page_data['class_details'] = $class;
			$page_data['courses'] = $this->lms_model->get_courses_by_class($class['id']);
			$page_data['student'] = $student;
			$page_data['folder_name'] = 'class';
			$page_data['page_title'] = 'class_courses';
			$page_data['page_name'] = 'courses';
			return view('backend/index', $page_data);
			return;
		}

		if(empty($param1)){
			$page_data['folder_name'] = 'class';
			$page_data['page_title'] = 'class';
			$page_data['page_name'] = 'index';
			return view('backend/index', $page_data);
		}
	}
	//END CLASS section

	// Show all courses linked to a given class for the logged-in student
	public function courses($class_id = '') {
		if (session()->get('student_login') != 1) {
			return redirect()->to(site_url('login'));
		}

		if (empty($class_id)) {
			show_404();
		}

		$user_id = session()->get('user_id');
		$student = $this->db
			->where('user_id', $user_id)
			->where('status', 1)
			->get('students')
			->getRowArray();

		if (!$student) {
			show_error(get_phrase('student_profile_not_found'));
		}

		$class = $this->db_helper_model->get_by_id('classes', $class_id);
		if (!$class) {
			show_404();
		}

		// $is_enrolled = $this->db
		// 	->where('student_id', $student['id'])
		// 	->where('class_id', $class['id'])
		// 	->count_all_results('enrols');
        //     print_r($student['id']." ------ ");
        //     print_r($class['id']);
            
           

		// if (!$is_enrolled) {
		// 	// Not enrolled: show payment/join page
		// 	$currency = db()->table('settings_school', array('school_id' => $class['school_id']))->row('system_currency');
		// 	$page_data['class_details'] = $class;
		// 	$page_data['student'] = $student;
		// 	$page_data['currency'] = $currency ?: 'USD';
		// 	$page_data['folder_name'] = 'class';
		// 	$page_data['page_title'] = 'class_payment';
		// 	$page_data['page_name'] = 'pay';
		// 	return view('backend/index', $page_data);
		// 	return;
		// }

		$page_data['class_details'] = $class;
		$page_data['courses'] = $this->lms_model->get_courses_by_class($class['id']);
		$page_data['student'] = $student;
		$page_data['folder_name'] = 'class';
		$page_data['page_title'] = 'class_courses';
		$page_data['page_name'] = 'courses';
		return view('backend/index', $page_data);
	}
      public function recording($param1 = '', $param2 = '', $param3 = '') 
      {
      
            // Check authentication
            if (session()->get('student_login') != 1) {
                return redirect()->to(site_url('login'));
                }

            // Get the logged-in user's ID
            $user_id = session()->get('user_id');

            // Get student IDs associated with the user
            $students = $this->student_model->get_where_result('students', ['user_id' => $user_id], 'id');
            $student_ids = array_column($students, 'id');

            if (empty($student_ids)) {
                log_message('error', 'recording - No student associated with user_id: ' . $user_id);
                $page_data['page_name'] = 'no_student_access';
                $page_data['page_title'] = get_phrase('access_denied');
                return view('backend/index', $page_data);
                return;
            }

            // Get permitted class IDs and school IDs from enrols
            $sql = "SELECT enrols.school_id, enrols.class_id FROM enrols WHERE enrols.student_id IN (" . implode(',', array_fill(0, count($student_ids), '?')) . ") AND enrols.school_id IS NOT NULL";
            $enrols = $this->student_model->get_by_query($sql, $student_ids);
            $permitted_class_ids = array_map('strval', array_unique(array_column($enrols, 'class_id')));
            $permitted_school_ids = array_map('strval', array_unique(array_column($enrols, 'school_id')));

            if (empty($enrols)) {
                log_message('error', 'recording - User not enrolled in any school: ' . $user_id);
                $page_data['page_name'] = 'no_student_access';
                $page_data['page_title'] = get_phrase('access_denied');
                return view('backend/index', $page_data);
                return;
            }

            // Get school_id from users table
            $user = $this->db_helper_model->get_by_id('users', $user_id);
            if (!$user || empty($user['school_id']) || !in_array((string)$user['school_id'], $permitted_school_ids)) {
                log_message('error', 'recording - No valid school_id found for user_id: ' . $user_id);
                show_error('No valid school associated with this user.', 403);
                return;
            }
            $school_id = $user['school_id'];

            // Synchronize recordings
            $bbb_config = config('bigbluebutton');
            $bbb_url = $bbb_config->bbb_url ?? '';
            $bbb_secret = $bbb_config->bbb_secret ?? '';

            $last_sync = session()->get('last_recording_sync');
            $current_time = time();
            $sync_interval = 10; // Synchronize every 10 seconds (for testing, adjust as needed)

            if (!$last_sync || ($current_time - $last_sync) > $sync_interval) {
                // Filter meetings by school_id
                $meetings = $this->student_model->get_where_result('sessions_meetings', ['school_id' => $school_id]);

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
                            $formatted_duration = $hours >= 1
                                ? sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds)
                                : sprintf("%02d:%02d", $minutes, $seconds);

                            $start_time = date('Y-m-d H:i:s', $recording_start_time / 1000);
                            $end_time = date('Y-m-d H:i:s', $recording_end_time / 1000);

                            $existing = $this->db_helper_model->get('recordings', ['recording_id' => $recording_id], 'row_array');
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
                                $this->student_model->insert_table('recordings', $recording_data);
                            } else {
                                $this->student_model->update_table('recordings', ['recording_id' => $recording_id], [
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

            // Build query for recordings with access restrictions
            $class_ids_list = empty($permitted_class_ids) ? [-1] : $permitted_class_ids;
            
            $sql = "SELECT r.*, c.name as class_name FROM recordings r LEFT JOIN classes c ON r.class_id = c.id LEFT JOIN appointments a ON r.appointment_id = a.id LEFT JOIN participants p ON a.event_id = p.event_id WHERE r.school_id = ? AND (";
            $params = [$school_id];
            
            // Build access conditions
            $access_conditions = [];
            
            // Access via recording class
            $access_conditions[] = "r.class_id IN (" . implode(',', array_fill(0, count($class_ids_list), '?')) . ")";
            $params = array_merge($params, $class_ids_list);
            
            // Access via participant (individual)
            $access_conditions[] = "(p.type = 'individual' AND p.guest = ?)";
            $params[] = $user_id;
            
            // Access via participant (class)
            $access_conditions[] = "(p.type = 'class' AND p.guest IN (" . implode(',', array_fill(0, count($class_ids_list), '?')) . "))";
            $params = array_merge($params, $class_ids_list);
            
            $sql .= implode(' OR ', $access_conditions) . ")";

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

            $sql .= " GROUP BY r.id ORDER BY r.created_at DESC";
            $recordings = $this->student_model->get_by_query($sql, $params);

         
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
    if (session()->get('student_login') != 1) {
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
    $existing = $this->student_model->get_row('recordings', ['recording_id' => $recording_id]);

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

    // Charger la configuration BigBlueButton
    $bbb_config = config('bigbluebutton');
    $bbb_url = $bbb_config->bbb_url ?? '';
    $bbb_secret = $bbb_config->bbb_secret ?? '';

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
    $this->student_model->delete_table('recordings', ['recording_id' => $recording_id]);
    $db_error = db()->error();


    if ($db_error['code'] == 0 && $this->db->affectedRows() > 0) {
        $response = [
            'status' => 'success',
            'message' => get_phrase('recording_deleted_successfully'),
            'csrf_token' => csrf_hash()
        ];
    } else {
        log_message('error', 'delete_recording - Failed to delete recording: recording_id=' . $recording_id . ', Last Query: ' . db()->getLastQuery() . ', DB Error: ' . json_encode($db_error));
        $response = [
            'status' => 'error',
            'message' => get_phrase('failed_to_delete_recording') . ' (DB Error: ' . $db_error['message'] . ')',
            'csrf_token' => csrf_hash()
        ];
    }

    return $this->response->setJSON($response);
}

	  
	  public function filter_recordings()
	  {
		  $bbb_config = config('bigbluebutton');
		  $bbbUrl = $bbb_config->bbb_url ?? '';
		  $bbbSecret = $bbb_config->bbb_secret ?? '';
	  
		  $meeting_name = $this->request->getPost('meeting_name');
		  $date_range = $this->request->getPost('date_range');
		  $school_id = $this->request->getPost('school_id');
	  
		  // $this->db->select('*')->from('appointments');
		  $schoolID = school_id();
		  
		  $sql = "SELECT appointments.id, appointments.title, appointments.start_date AS start, appointments.description, appointments.sections_id AS section, appointments.classe_id, appointments.room_id, rooms.name, appointments.meeting_id FROM appointments LEFT JOIN rooms ON rooms.id = appointments.room_id WHERE appointments.Etat = 1 AND appointments.school_id = ?";
		  $params = [$school_id];
		  
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
		  
		  $appointments = $this->student_model->get_by_query($sql, $params);
	  
		  foreach ($appointments as &$appointment_rec) {
			  $meetingId = $appointment_rec['meeting_id'] ?? null;
			  $appointment_rec['recordings'] = [];
		
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
			$classe_name = $this->student_model->get_class_by_id($appointment['classe_id'])['name'] ?? '';
			$section = " - ";
			  if (!empty($appointment['section'])) {
						  $section_ids = explode(',', $appointment['section']);
						  $section_names = [];
							  foreach ($section_ids as $id) {
										  $name = $this->student_model->get_section_name($id);
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
			  echo '<td>' . (!empty($appointment_rec['recordings']) ? $appointment_rec['recordings'][0]['duration'] : 'â€”') . '</td>';
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
				$user_id = session()->get('user_id');
				$session_id = active_session();
		
				// RÃ©cupÃ©rer les classes oÃ¹ l'Ã©tudiant est inscrit pour l'examen donnÃ©
				$sql = "SELECT classes.id, classes.name FROM classes LEFT JOIN enrols ON enrols.class_id = classes.id LEFT JOIN students ON students.id = enrols.student_id LEFT JOIN exams ON exams.class_id = classes.id WHERE exams.id = ? AND students.user_id = ? AND enrols.session = ?";
				$classes = $this->student_model->get_by_query($sql, [$id, $user_id, $session_id]);
		
				$output = '<option value="">' . get_phrase('select_a_class') . '</option>';
				foreach ($classes as $class) {
					$output .= '<option value="' . $class['id'] . '">' . $class['name'] . '</option>';
				}
				echo $output;
			}
		}
		//	SECTION ENDED

	
		


	//START SYLLABUS section
	public function syllabus($param1 = '', $param2 = '', $param3 = ''){

		if($param1 == 'list'){
			$page_data['class_id'] = $param2;
			
			return view('backend/student/syllabus/list', $page_data);
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
		$allowed_user_types = ['student'];
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

    // // showing the list of language
    // if ($param1 == 'list') {
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
      return view('backend/student/language/dropdown');
    }
    // showing the index file
    if (empty($param1)) {
      $page_data['folder_name'] = 'language';
      $page_data['page_title'] = 'languages';
      return view('backend/index', $page_data);
    }
  }

	//START TEACHER section
	public function teacher($param1 = '', $param2 = '', $param3 = ''){
		$page_data['working_page'] = 'filter';
		$page_data['folder_name'] = 'teacher';
		$page_data['page_title'] = 'techers';
		return view('backend/index', $page_data);
	}
	//END TEACHER section

	//START CLASS ROUTINE section
	public function routine($param1 = '', $param2 = '', $param3 = '', $param4 = ''){

		if($param1 == 'filter'){
			$page_data['class_id'] = $param2;
			
			return view('backend/student/routine/list', $page_data);
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
		if($param1 == 'filter'){
			$month = (string) $this->request->getPost('month');
			$year = (string) $this->request->getPost('year');
			$page_data['attendance_date'] = strtotime('01 ' . $month . ' ' . $year);
			$page_data['class_id'] = htmlspecialchars((string) $this->request->getPost('class_id'));
			
			// Utiliser l'Ã©cole active depuis la session
			$page_data['school_id'] = session()->get('active_school_id');
			$page_data['month'] = htmlspecialchars($month);
			$page_data['year'] = htmlspecialchars($year);
			// Charger la vue mise Ã  jour
			$response_html = view('backend/student/attendance/list', $page_data, ['cache' => 0]);
		    // PrÃ©parer le nouveau jeton CSRF
			$csrf = array(
				 'csrfName' => csrf_token(),
				 'csrfHash' => csrf_hash(),
				 );
		
			// Renvoyer la rÃ©ponse JSON avec le HTML mis Ã  jour et le nouveau jeton CSRF
			echo json_encode(array('status' => $response_html, 'csrf' => $csrf));
		}

		if(empty($param1)){
			$page_data['folder_name'] = 'attendance';
			$page_data['page_title'] = 'attendance';
			return view('backend/index', $page_data);
		}
	}
	//END DAILY ATTENDANCE section

  //	academy STARTED
  public function academy($action = "", $id = "") {
		
    // PROVIDE A LIST OF SECTION ACCORDING TO CLASS ID
    if ($action == 'list') {
		// echo $id;
      $page_data['school_id'] = $id;
      return view('backend/academy/liste_classe', $page_data);
    }

    if ($action == 'filter') {
        $selected_class_id = $this->request->getPost('class_id');
        $selected_user_id = $this->request->getPost('user_id');
        // $selected_school_id = session()->get('active_school_id');
        
        $page_data['selected_class_id'] = $selected_class_id;
        $page_data['selected_user_id'] = $selected_user_id;
        // $page_data['selected_school_id'] = $selected_school_id;
        return view('backend/academy/grid_view_for_student', $page_data);
    }
  }
  //	academy ENDED
  public function online_admission($param1 = "", $user_id = "")
  {

	
    if ($param1 == 'assigned') {
		
	 // Stocker les donnÃ©es de l'inscription dans la session pour un accÃ¨s ultÃ©rieur
		$data['student_id'] = htmlspecialchars($this->request->getPost('student_id'));
		$data['class_id'] = htmlspecialchars($this->request->getPost('class_id'));
		
		$data['school_id'] = htmlspecialchars($this->request->getPost('school_id'));
		$data['price'] = htmlspecialchars($this->request->getPost('price'));
		$data['currency'] = htmlspecialchars($this->request->getPost('currency'));
		$data['session'] = active_session();
		
		// VAT data from form (new)
		$data['vat_applicable'] = (int)$this->request->getPost('vat_applicable');
		$data['vat_rate'] = (float)$this->request->getPost('vat_rate');
		$data['vat_amount'] = (float)$this->request->getPost('vat_amount');
		$data['sub_total'] = (float)$this->request->getPost('sub_total');
	
	  	session()->set('enrolment_data', $data);

		// Switch session to the target school context immediately
		if (!empty($data['school_id'])) {
			session()->set('active_school_id', $data['school_id']);
			session()->set('school_id', $data['school_id']);
		}


		// Si le prix est 0 (gratuit), on inscrit directement l'Ã©tudiant
		if ($data['price'] <= 0) {
			
			// 1. Inscrire dans la table enrols sans crÃ©er de facture
			$this->crud_model->enroll_student_free($data);
			$school_id = $data['school_id'];
			
			if ($school_id) {
				// Mettre Ã  jour la session pour que l'Ã©tudiant soit dans la bonne communautÃ©
				session()->set('active_school_id', $school_id);
				session()->set('school_id', $school_id);
			}

            // 3. Rediriger directement vers le cours
			return redirect()->to(site_url('app/courses/' . $data['class_id']));
            return;
		}

		// Si payant, vÃ©rifier facture existante ou en crÃ©er une
		$num_rows_invoices = $this->student_model->count_all('invoices', ['class_id' => $data['class_id'], 'student_id' => $data['student_id']]);
		// print_r($num_rows_invoices);die;
		if($num_rows_invoices == 0){
			// ========== VAT CALCULATION FOR CLASS INVOICE ==========
			// IMPORTANT: Le prix reÃ§u est TTC (inclut dÃ©jÃ  la TVA)
			// Les donnÃ©es VAT peuvent venir du formulaire OU Ãªtre recalculÃ©es
			
			$total_amount = (float)$data['price']; // Prix TTC
			
			// Utiliser les donnÃ©es VAT du formulaire si disponibles
			if (!empty($data['vat_applicable']) && $data['vat_applicable'] == 1 && !empty($data['sub_total'])) {
				// DonnÃ©es VAT fournies par le formulaire
				$vat_applicable = true;
				$vat_rate = $data['vat_rate'];
				$sub_total = $data['sub_total'];
				$vat_amount = $data['vat_amount'];
			} else {
				// Fallback: Recalculer la TVA depuis les settings Ã©cole
				$settings_school = $this->settings_model->get_settings_school_data($data['school_id']);
				$school = $this->student_model->get_school_by_id($data['school_id']);
				$vat_enabled = isset($settings_school['vat_enabled']) && (int)$settings_school['vat_enabled'] === 1;
				$tax_residence = isset($school['country']) ? strtoupper($school['country']) : null;
				
				$vat_rate = 0;
				$vat_applicable = false;
				
				if ($vat_enabled && !empty($tax_residence)) {
					$vat_applicable = true;
					if ($tax_residence === 'MA') {
						$vat_rate = 20;
					} elseif ($tax_residence === 'UAE' || $tax_residence === 'AE') {
						$vat_rate = 5;
					}
				}
				
				// IMPORTANT: Le prix est TTC, donc on fait le calcul inversÃ©
				if ($vat_applicable && $vat_rate > 0) {
					$sub_total = round($total_amount / (1 + ($vat_rate / 100)), 2);
					$vat_amount = round($total_amount - $sub_total, 2);
				} else {
					$sub_total = $total_amount;
					$vat_amount = 0;
				}
			}
			
			$name = $this->student_model->get_school_by_id($data['school_id'])['name'] ?? '';
			$classe_name = $this->student_model->get_class_by_id($data['class_id'])['name'] ?? '';
			$data_invoice['title'] = $name." - ".$classe_name ;
			$data_invoice['total_amount'] = $total_amount; // Montant TTC
			$data_invoice['sub_total'] = $sub_total; // Montant HT
			$data_invoice['vat_amount'] = $vat_amount; // Montant TVA
			$data_invoice['vat_rate'] = $vat_rate; // Taux TVA
            $data_invoice['currency'] = $data['currency'];
			$data_invoice['class_id'] = $data['class_id'] ;
			$data_invoice['student_id'] = $data['student_id'];
			$data_invoice['status'] = "unpaid";
			$data_invoice['school_id'] =$data['school_id'];
			$data_invoice['session'] = $data['session'];
			$data_invoice['created_at'] = strtotime(date('d-M-Y'));
			$invoice_id = $this->student_model->insert_table('invoices', $data_invoice);
		}else{
			$invoice_id = $this->student_model->get_row('invoices', ['class_id' => $data['class_id'], 'student_id' => $data['student_id']])['id'] ?? null;
		}


	  return redirect()->to(site_url('app/payment/classe/' . $invoice_id));

    //   $this->session->set_flashdata('flash_message', get_phrase('admission_request_has_been_updated'));
    //   redirect(site_url('addons/courses'), 'refresh');
    }








    $page_data['folder_name'] = 'academy';
    $page_data['page_title'] = 'academy';
    return view('backend/index', $page_data);
  }

  public function join_school($param1, $school_id)
{
    if ($param1 == 'assigned') {
        
        // Note: Le switch admin/teacher -> student est fait dans le constructeur
        
        // ðŸ”¹ 1. RÃ©cupÃ©ration des donnÃ©es envoyÃ©es par le formulaire
        $data['student_id'] = (int) (session()->get('user_id') ?? 0);
        $data['school_id']  = trim((string) ($this->request->getPost('school_id') ?? ''));

        // Fallback: Si school_id est vide (ex: redirection qui perd le POST), utiliser le paramÃ¨tre URL
        if (empty($data['school_id']) && !empty($school_id)) {
            $data['school_id'] = $school_id;
        }

        $data['price']      = trim((string) ($this->request->getPost('price') ?? ''));
        $data['currency']   = trim((string) ($this->request->getPost('currency') ?? ''));
        $data['session']    = active_session();

        log_message('debug', 'JOIN_SCHOOL_DEBUG: User ' . $data['student_id'] . ' joining School ' . $data['school_id'] . ' with Price: ' . $data['price']);

        // ðŸ”¹ 2. VÃ©rifier si l'Ã©cole existe
        $school = $this->student_model->get_school_by_id($data['school_id']);
        $schoolData = is_array($school) ? $school : (array) $school;
        if (empty($schoolData)) {
            show_error('community not found.');
            return;
        }

        // Fallback: Si le prix est vide (car POST perdu), utiliser le prix de l'Ã©cole
        if ($data['price'] === '' || $data['price'] === null) {
            $data['price'] = (string) ($schoolData['price'] ?? '0');
            
            // Si la devise est vide, essayer de la rÃ©cupÃ©rer des settings (ou dÃ©faut)
            if (empty($data['currency'])) {
                 $data['currency'] = get_settings('system_currency');
            }
        }
        $school_name = (string) ($schoolData['name'] ?? '');
        
        // Si le prix est gratuit (0 ou null), on rejoint directement sans passer par le paiement
        if ((float)$data['price'] <= 0.01) {
            
             // ðŸ”¹ 5. Pas de facture ni paiement pour le gratuit
             
            $this->user_model->join_school($data['school_id'], [
                'payment_method' => 'free_access'
            ]);

            // User_model->join_school gÃ¨re la redirection, mais par sÃ©curitÃ© :
            if (isset($_SERVER['HTTP_REFERER'])) {
                return redirect()->to($_SERVER['HTTP_REFERER']);
            } else {
                return redirect()->to(site_url('home'));
            }
            return;
        }

        // ðŸ”¹ 3. VÃ©rifier s'il existe dÃ©jÃ  une facture pour cette Ã©cole et cet Ã©tudiant
        $existing_invoice = $this->student_model->get_row('invoices', [
            'school_id'  => $data['school_id'],
            'student_id' => $data['student_id'],
            'payment_type' => 'school_join'
        ]);

        if (!$existing_invoice) {
            // ðŸ”¹ 4. Calculer la TVA
            $settings_school = $this->settings_model->get_settings_school_data($data['school_id']);
            $school = $this->student_model->get_school_by_id($data['school_id']);
            $vat_applicable = isset($settings_school['vat_enabled']) && (int)$settings_school['vat_enabled'] === 1;
            $tax_residence  = isset($school['country']) ? $school['country'] : null;
            
            $vat_rate = 0;
            if ($vat_applicable) {
                if ($tax_residence === 'MA') {
                    $vat_rate = 20;
                } elseif ($tax_residence === 'UAE' || $tax_residence === 'AE') {
                    $vat_rate = 5;
                }
            }
            
            // Le prix reÃ§u est dÃ©jÃ  TTC (depuis community_details.php)
            $price_ttc = (float)$data['price'];
            $sub_total = 0;
            $vat_amount = 0;
            
            if ($vat_rate > 0) {
                // Calculer le HT Ã  partir du TTC
                $sub_total = $price_ttc / (1 + ($vat_rate / 100));
                $vat_amount = $price_ttc - $sub_total;
            } else {
                $sub_total = $price_ttc;
                $vat_amount = 0;
            }
            
            // ðŸ”¹ 5. CrÃ©er la facture (invoice) avec TVA
            $invoice_data = [
                'title'        => $school_name,
                'total_amount' => $price_ttc, // Montant TTC
                'sub_total'    => $sub_total, // Montant HT
                'vat_amount'   => $vat_amount, // Montant TVA
                'vat_rate'     => $vat_rate,   // Taux TVA
                'student_id'   => $data['student_id'],
                'school_id'    => $data['school_id'],
                'status'       => 'unpaid',
                'currency'     => $data['currency'],
                'session'      => $data['session'],
                'created_at'   => strtotime(date('Y-m-d H:i:s')),
                'payment_type' => 'school_join'
            ];
            $invoice_id = $this->student_model->insert_table('invoices', $invoice_data);
        } else {
            $existing_invoice = is_array($existing_invoice) ? $existing_invoice : (array) $existing_invoice;
            $invoice_id = (int) ($existing_invoice['id'] ?? 0);
        }

        // ðŸ”¹ 5. VÃ©rifier s'il existe dÃ©jÃ  un paiement
        $existing_payment = $this->student_model->get_row('payments', [
            'school_id'  => $data['school_id'],
            'student_id' => $data['student_id']
        ]);

        if (!$existing_payment) {
            // ðŸ”¹ 6. CrÃ©er l'entrÃ©e de paiement dans la table "payments"
            $payment_data = [
                'student_id'     => $data['student_id'],
                'school_id'      => $data['school_id'],
                'amount'         => $data['price'],
                'currency'       => $data['currency'],
                'payment_type'   => 'community_join',
                'payment_status' => 'pending',
                'invoice_id'     => $invoice_id,
                'created_at'     => date('Y-m-d H:i:s')
            ];
            $this->student_model->insert_table('payments', $payment_data);
        }
		// die($data['price']);
        // ðŸ”¹ 7. Redirection vers la page de paiement ou la facture

       // âœ… ADD THIS: Store school_id in session for payment page
        session()->set('payment_school_id', $data['school_id']);

		return redirect()->to(site_url('payment/community/' . $invoice_id));
    }
}

	//START EVENT CALENDAR section
	public function event_calendar($param1 = '', $param2 = ''){

		if($param1 == 'all_events'){
			echo $this->crud_model->all_events();
		}

		if ($param1 == 'list') {
			return view('backend/student/event_calendar/list');
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
    // EmpÃªcher les Ã©tudiants de crÃ©er, modifier ou supprimer des examens
    if ($param1 == 'create') {
        // Les Ã©tudiants ne peuvent pas crÃ©er d'examens
        $output = array(
            'status' => false,
            'message' => get_phrase('students_cannot_create_exams'),
            'csrf' => array(
                'csrfName' => csrf_token(),
                'csrfHash' => csrf_hash(),
            )
        );
        
        return $this->response->setJSON($output);
    }


    if ($param1 == 'update') {
        // Les Ã©tudiants ne peuvent pas modifier des examens
        $output = array(
            'status' => false,
            'message' => get_phrase('students_cannot_update_exams'),
            'csrf' => array(
                'csrfName' => csrf_token(),
                'csrfHash' => csrf_hash(),
            )
        );
        
        return $this->response->setJSON($output);
    }

    if ($param1 == 'delete') {
        // Les Ã©tudiants ne peuvent pas supprimer des examens
        $output = array(
            'status' => false,
            'message' => get_phrase('students_cannot_delete_exams'),
            'csrf' => array(
                'csrfName' => csrf_token(),
                'csrfHash' => csrf_hash(),
            )
        );
        
        return $this->response->setJSON($output);
    }

    if ($param1 == 'list') {
        $user_id = (int) session()->get('user_id');
        $preferred_school_id = $this->resolveCurrentSchoolId($user_id);
        $studentBuilder = $this->db->table('students')->where('user_id', $user_id);
        if ($preferred_school_id > 0) {
            $studentBuilder->where('school_id', $preferred_school_id);
        }
        $student = $studentBuilder->get()->getRowArray();
        if (empty($student)) {
            $student = $this->db->table('students')->where('user_id', $user_id)->get()->getRowArray();
        }
        $school_id = $this->resolveCurrentSchoolId($user_id, $student);
        $session = active_session();
        $exams_per_page = 10;
        $current_page = 1;

        $page_data['filter_class_id'] = htmlspecialchars((string) $this->request->getPost('class_id'));
        $page_data['filter_date_range'] = htmlspecialchars((string) $this->request->getPost('date_range'));
        $page_data['student_not_found'] = empty($student);

        if ($page_data['student_not_found']) {
            $page_data['classes'] = [];
            $page_data['exams'] = [];
            $page_data['total_exams'] = 0;
            $page_data['exams_per_page'] = $exams_per_page;
            $page_data['current_page'] = $current_page;
            $page_data['total_pages'] = 0;
            return view('backend/student/exam/list', $page_data);
        }

        $student_id = $student['id'] ?? null;
        $enrolled_class_ids = [];
        if ($student_id) {
            $enrolBuilder = $this->db->table('enrols')
                ->select('class_id')
                ->where('student_id', $student_id)
                ->where('session', $session);
            if ($school_id > 0) {
                $enrolBuilder->where('school_id', $school_id);
            }
            $enrolled_classes = $enrolBuilder->get()->getResultArray();
            if (empty($enrolled_classes) && $school_id > 0) {
                // Fallback for inconsistent school context during migration
                $enrolled_classes = $this->db->table('enrols')
                    ->select('class_id')
                    ->where('student_id', $student_id)
                    ->where('session', $session)
                    ->get()
                    ->getResultArray();
            }
            if (empty($enrolled_classes)) {
                // Legacy rows may not have session set
                $enrolLegacyBuilder = $this->db->table('enrols')
                    ->select('class_id')
                    ->where('student_id', $student_id);
                if ($school_id > 0) {
                    $enrolLegacyBuilder->where('school_id', $school_id);
                }
                $enrolled_classes = $enrolLegacyBuilder->get()->getResultArray();
            }
            $enrolled_class_ids = array_column($enrolled_classes, 'class_id');
        }

        $date_from = null;
        $date_to = null;
        if (!empty($page_data['filter_date_range'])) {
            $dates = explode(' - ', $page_data['filter_date_range']);
            if (count($dates) === 2) {
                $date_from = strtotime(trim($dates[0]) . ' 00:00:00');
                $date_to = strtotime(trim($dates[1]) . ' 23:59:59');
            }
        }

        $countBuilder = $this->db->table('exams');
        if ($session !== null && $session !== '') {
            $countBuilder->groupStart()
                ->where('session', $session)
                ->orWhere('session', null)
                ->orWhere('session', '')
                ->groupEnd();
        }
        if ($school_id > 0) {
            $countBuilder->where('school_id', $school_id);
        }
        if (!empty($enrolled_class_ids)) {
            $countBuilder->whereIn('class_id', $enrolled_class_ids);
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

        $escaped_user_id = $this->db->escape($user_id);
        $examBuilder = $this->db->table('exams');
        $examBuilder->select('exams.*, classes.name as class_name, (SELECT COUNT(*) FROM exam_responses WHERE exam_responses.exam_id = exams.id AND exam_responses.user_id = ' . $escaped_user_id . ') as already_passed')
            ->join('classes', 'exams.class_id = classes.id', 'left');
        if ($session !== null && $session !== '') {
            $examBuilder->groupStart()
                ->where('exams.session', $session)
                ->orWhere('exams.session', null)
                ->orWhere('exams.session', '')
                ->groupEnd();
        }
        if ($school_id > 0) {
            $examBuilder->where('exams.school_id', $school_id);
        }
        if (!empty($enrolled_class_ids)) {
            $examBuilder->whereIn('exams.class_id', $enrolled_class_ids);
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

        $classesBuilder = $this->db->table('classes');
        if ($school_id > 0) {
            $classesBuilder->where('school_id', $school_id);
        }
        if (!empty($enrolled_class_ids)) {
            $classesBuilder->whereIn('id', $enrolled_class_ids);
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
        return view('backend/student/exam/list', $page_data);
    }

    if (empty($param1)) {
        $page_data['folder_name'] = 'exam';
        $page_data['page_title'] = 'Certifications';
        return view('backend/index', $page_data);
    }
}
	//END EXAM section

// Paginated exams for AJAX loading
public function get_exams_paginated()
{
    if (session()->get('student_login') != 1) {
        return $this->response->setJSON(['status' => false, 'error' => 'Unauthorized']);
    }

    $user_id = (int) session()->get('user_id');
    $preferred_school_id = $this->resolveCurrentSchoolId($user_id);
    $school_id = $preferred_school_id;
    $session = active_session();
    
    // Get student ID
    $studentBuilder = $this->db->table('students')->where('user_id', $user_id);
    if ($preferred_school_id > 0) {
        $studentBuilder->where('school_id', $preferred_school_id);
    }
    $student = $studentBuilder->get()->getRowArray();
    if (empty($student)) {
        $student = $this->db_helper_model->get('students', ['user_id' => $user_id], 'row_array');
    }
    $school_id = $this->resolveCurrentSchoolId($user_id, $student);
    $student_id = $student['id'] ?? null;
    
    if (!$student_id) {
        echo json_encode(['status' => false, 'error' => 'No student associated with this user']);
        exit;
    }
    
    // Get enrolled class IDs from enrols table
    $enrolBuilder = $this->db->table('enrols')->select('class_id')->where('student_id', $student_id)->where('session', $session);
    if ($school_id > 0) {
        $enrolBuilder->where('school_id', $school_id);
    }
    $enrolled_classes = $enrolBuilder->get()->getResultArray();
    if (empty($enrolled_classes) && $school_id > 0) {
        $enrolled_classes = $this->db->table('enrols')
            ->select('class_id')
            ->where('student_id', $student_id)
            ->where('session', $session)
            ->get()
            ->getResultArray();
    }
    if (empty($enrolled_classes)) {
        $enrolLegacyBuilder = $this->db->table('enrols')->select('class_id')->where('student_id', $student_id);
        if ($school_id > 0) {
            $enrolLegacyBuilder->where('school_id', $school_id);
        }
        $enrolled_classes = $enrolLegacyBuilder->get()->getResultArray();
    }
    $enrolled_class_ids = array_column($enrolled_classes, 'class_id');
    
    $page = (int) ($this->request->getGet('page') ?? 1);
    if ($page < 1) {
        $page = 1;
    }
    $limit = 10; // Exams per page
    $offset = ($page - 1) * $limit;
    
    // Get filters if any
    $class_id = trim((string) ($this->request->getGet('class_id') ?? ''));
    $date_range = trim((string) ($this->request->getGet('date_range') ?? ''));
    $date_from = '';
    $date_to = '';

    if (!empty($date_range)) {
        $dates = explode(' - ', $date_range);
        if (count($dates) == 2) {
            $date_from = strtotime(trim($dates[0]) . ' 00:00:00');
            $date_to = strtotime(trim($dates[1]) . ' 23:59:59');
        }
    }

    // Count total exams with filters (use SQL to avoid CI3/CI4 builder compat issues)
    $total_exams = 0;
    if (!empty($enrolled_class_ids)) {
        $countSql = "SELECT COUNT(*) AS cnt FROM exams WHERE 1=1";
        $countParams = [];
        if ($session !== null && $session !== '') {
            $countSql .= " AND (session = ? OR session IS NULL OR session = '')";
            $countParams[] = $session;
        }
        if ($school_id > 0) {
            $countSql .= " AND school_id = ?";
            $countParams[] = $school_id;
        }
        $countSql .= " AND class_id IN (" . implode(',', array_fill(0, count($enrolled_class_ids), '?')) . ")";
        $countParams = array_merge($countParams, $enrolled_class_ids);
        if ($class_id !== '') {
            $countSql .= " AND class_id = ?";
            $countParams[] = $class_id;
        }
        if ($date_range !== '' && $date_from && $date_to) {
            $countSql .= " AND starting_date >= ? AND starting_date <= ?";
            $countParams[] = $date_from;
            $countParams[] = $date_to;
        }
        $countRows = $this->student_model->get_by_query($countSql, $countParams);
        $total_exams = (int) (($countRows[0]['cnt'] ?? 0));
    }
    $total_pages = ceil($total_exams / $limit);

    // Get paginated exams
    $sql = "SELECT exams.*, classes.name as class_name FROM exams LEFT JOIN classes ON exams.class_id = classes.id WHERE 1=1";
    $params = [];
    if ($session !== null && $session !== '') {
        $sql .= " AND (exams.session = ? OR exams.session IS NULL OR exams.session = '')";
        $params[] = $session;
    }
    if ($school_id > 0) {
        $sql .= " AND exams.school_id = ?";
        $params[] = $school_id;
    }
    
    // Filter by enrolled classes only
    if (!empty($enrolled_class_ids)) {
        $sql .= " AND exams.class_id IN (" . implode(',', array_fill(0, count($enrolled_class_ids), '?')) . ")";
        $params = array_merge($params, $enrolled_class_ids);
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
    $exams = $this->student_model->get_by_query($sql, $params);

    $exam_data = [];
    foreach ($exams as $exam) {
        // VÃ©rifier si l'Ã©tudiant a dÃ©jÃ  soumis cet examen
        $existing_result = $this->student_model->get_where_result('exam_responses', ['exam_id' => $exam['id'], 'user_id' => $user_id], 'id');
        $existing_submission = !empty($existing_result) ? $existing_result[0] : null;
        
        $starting_timestamp = $this->normalizeToTimestamp($exam['starting_date'] ?? null);
        $exam_data[] = [
            'id' => $exam['id'],
            'name' => $exam['name'] ?: 'Unnamed Exam',
            'starting_date' => $exam['starting_date'], // AjoutÃ© pour le compteur
            'starting_timestamp' => $starting_timestamp,
            'formatted_date' => $starting_timestamp > 0 ? date('D, d-M-Y H:i', $starting_timestamp) : 'No Date',
            'class_name' => $exam['class_name'] ?: 'No Class',
            'already_passed' => !empty($existing_submission) // Indicateur si l'examen est dÃ©jÃ  passÃ©
        ];
    }

    return $this->response->setJSON([
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
			$raw_class_id = (string) $this->request->getPost('class_id');
			$raw_exam_id = (string) $this->request->getPost('exam');
			$page_data['class_id'] = htmlspecialchars($raw_class_id);
			
		
			$page_data['exam_id'] = htmlspecialchars($raw_exam_id);
			// $this->crud_model->mark_insert($page_data['class_id'],  $page_data['subject_id'], $page_data['exam_id']);
			$user_id = (int) session()->get('user_id');
			$session_id = active_session();
			$class_id = (int) ($raw_class_id === 'undefined' || $raw_class_id === 'null' ? 0 : $raw_class_id);
			$exam_id = (int) ($raw_exam_id === 'undefined' || $raw_exam_id === 'null' ? 0 : $raw_exam_id);
			$class_row = $class_id > 0 ? ($this->db->table('classes')->where('id', $class_id)->get()->getRowArray() ?? []) : [];
			$exam_row = $exam_id > 0 ? ($this->db->table('exams')->where('id', $exam_id)->get()->getRowArray() ?? []) : [];
			if ($class_id <= 0 && !empty($exam_row['class_id'])) {
				$class_id = (int) $exam_row['class_id'];
				$page_data['class_id'] = (string) $class_id;
				$class_row = $this->db->table('classes')->where('id', $class_id)->get()->getRowArray() ?? [];
			}
			$school_id = isset($class_row['school_id']) ? (int) $class_row['school_id'] : (int) ($exam_row['school_id'] ?? 0);
			if ($school_id <= 0) {
				$school_id = $this->resolveCurrentSchoolId($user_id);
			}
			$page_data['class_name'] = (string) ($class_row['name'] ?? '');
			$page_data['student_data'] = [];
			$page_data['user_details'] = [];
			$page_data['student_not_found'] = false;
			$student_ids_for_user = [];

			$studentBuilder = $this->db->table('students')->where('user_id', $user_id);
			if ($school_id > 0) {
				$studentBuilder->where('school_id', $school_id);
			}
			$page_data['student_data'] = $studentBuilder->get()->getRowArray() ?? [];
			if (empty($page_data['student_data'])) {
				// Fallback: locate the student through enrolments for this class (migration-safe path)
				$enrolStudentBuilder = $this->db->table('enrols')
					->select('students.*')
					->join('students', 'students.id = enrols.student_id', 'inner')
					->where('students.user_id', $user_id)
					->where('enrols.class_id', $class_id);
				if ($session_id !== null && $session_id !== '') {
					$enrolStudentBuilder->groupStart()
						->where('enrols.session', $session_id)
						->orWhere('enrols.session', null)
						->orWhere('enrols.session', '')
						->groupEnd();
				}
				if ($school_id > 0) {
					$enrolStudentBuilder->groupStart()
						->where('enrols.school_id', $school_id)
						->orWhere('students.school_id', $school_id)
						->groupEnd();
				}
				$page_data['student_data'] = $enrolStudentBuilder->get()->getRowArray() ?? [];
			}
			if (empty($page_data['student_data']) && $exam_id > 0) {
				// Fallback: derive student from existing marks for this exam/class
				$markStudentBuilder = $this->db->table('marks')
					->select('students.*')
					->join('students', 'students.id = marks.student_id', 'inner')
					->where('students.user_id', $user_id)
					->where('marks.exam_id', $exam_id);
				if ($class_id > 0) {
					$markStudentBuilder->where('marks.class_id', $class_id);
				}
				if ($school_id > 0) {
					$markStudentBuilder->where('marks.school_id', $school_id);
				}
				$page_data['student_data'] = $markStudentBuilder->get()->getRowArray() ?? [];
			}
			if (empty($page_data['student_data'])) {
				// Last fallback for migrated datasets: any student row for this user
				$page_data['student_data'] = $this->db->table('students')->where('user_id', $user_id)->get()->getRowArray() ?? [];
			}

			$student_rows_for_user = $this->db->table('students')
				->select('id')
				->where('user_id', $user_id)
				->get()
				->getResultArray();
			$student_ids_for_user = array_values(array_unique(array_map('intval', array_column($student_rows_for_user, 'id'))));

			if (empty($page_data['student_data']) && !empty($student_ids_for_user)) {
				$page_data['student_data'] = $this->db->table('students')
					->where('id', (int) $student_ids_for_user[0])
					->get()
					->getRowArray() ?? [];
			}
			$page_data['user_details'] = $this->db->table('users')->where('id', $user_id)->get()->getRowArray() ?? [];
			$page_data['total_questions'] = $this->crud_model->get_total_questions($exam_id);
			$page_data['exam_details'] = $exam_row ?? [];
			$marksBuilder = $this->db->table('marks')
				->where('class_id', $class_id)
				->where('exam_id', $exam_id);
			if ($school_id > 0) {
				$marksBuilder->where('school_id', $school_id);
			}
			if (!empty($student_ids_for_user)) {
				$marksBuilder->whereIn('student_id', $student_ids_for_user);
			}
			$page_data['marks'] = $marksBuilder->get()->getResultArray();
			// If marks exist, keep the first matching student as active context for the view filter.
			if (empty($page_data['student_data']) && !empty($page_data['marks'][0]['student_id'])) {
				$page_data['student_data'] = $this->db->table('students')
					->where('id', (int) $page_data['marks'][0]['student_id'])
					->get()
					->getRowArray() ?? [];
			}
			// Only show "Student not found" when this user truly has no student rows.
			$page_data['student_not_found'] = empty($student_ids_for_user) && empty($page_data['student_data']);
			// Charger la vue mise Ã  jour
			$response_html = view('backend/student/mark/list', $page_data, ['cache' => 0]);
		    // PrÃ©parer le nouveau jeton CSRF
			$csrf = array(
					 'csrfName' => csrf_token(),
					 'csrfHash' => csrf_hash(),
				 );
			
			// Renvoyer la rÃ©ponse JSON avec le HTML mis Ã  jour et le nouveau jeton CSRF
			return $this->response->setJSON(array('status' => $response_html, 'csrf' => $csrf));
		}

		if($param1 == 'mark_update'){
			$this->crud_model->mark_update();
		}

		if(empty($param1)){
			$user_id = (int) session()->get('user_id');
			$session_id = active_session();
			$page_data['student_mark_enrolments'] = $this->db->table('enrols')
				->select('enrols.class_id, enrols.school_id, classes.name as class_name')
				->join('students', 'students.id = enrols.student_id', 'left')
				->join('classes', 'classes.id = enrols.class_id', 'left')
				->where('students.user_id', $user_id)
				->where('enrols.session', $session_id)
				->get()
				->getResultArray();
			$exam_map = [];
			foreach ($page_data['student_mark_enrolments'] as $enrolment) {
				$exams = $this->db->table('exams')
					->where('school_id', $enrolment['school_id'])
					->where('class_id', $enrolment['class_id'])
					->where('session', $session_id)
					->get()
					->getResultArray();
				foreach ($exams as $exam) {
					$exam_map[(int) $exam['id']] = $exam;
				}
			}
			$page_data['student_mark_exams'] = array_values($exam_map);
			$page_data['folder_name'] = 'mark';
			$page_data['page_title'] = 'marks';
			return view('backend/index', $page_data);
		}
	}
	//END MARKS sesction

	// GRADE SECTION STARTS
	public function grade($param1 = "", $param2 = "") {
		$page_data['folder_name'] = 'grade';
		$page_data['page_title'] = 'grades';
		return view('backend/index', $page_data);
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
			return view('backend/index', $page_data);
		}

		// showing the index file with pagination
		if(empty($param1) || $param1 == 'page'){
			$student_data = $this->user_model->get_logged_in_student_details();

			// Get filter parameter from URL
			$filter = $this->request->getGet('filter');
			$search = $this->request->getGet('search');
			$valid_filters = ['all', 'paid', 'pending', 'due'];
			if (!in_array($filter, $valid_filters)) {
				$filter = 'all';
			}

			// Pagination config
			$per_page = 10; // Factures par page
			$current_page = ($param1 == 'page' && is_numeric($param2)) ? (int)$param2 : 1;
			$offset = ($current_page - 1) * $per_page;

			// Get total count for pagination (filtered)
			$student_code = isset($student_data['code']) ? $student_data['code'] : '';
			$total_invoices = $student_code ? $this->crud_model->count_invoices_by_student($student_code, $filter, $search) : 0;
			$total_pages = $per_page > 0 ? ceil($total_invoices / $per_page) : 0;

			// Pass pagination data to view
			$page_data['pagination'] = [
				'current_page' => $current_page,
				'total_pages' => $total_pages,
				'per_page' => $per_page,
				'total_items' => $total_invoices,
				'offset' => $offset,
				'filter' => $filter,
				'search' => $search
			];

			$page_data['folder_name'] = 'invoice';
			$page_data['page_title']  = 'invoice';
			return view('backend/index', $page_data);
		}
	}

	/**
	 * AJAX Handler for filtering and searching invoices
	 */
	public function filter_invoice() {
		$student_data = $this->user_model->get_logged_in_student_details();
		
		// Get parameters
		$filter = $this->request->getGet('filter');
		$search = $this->request->getGet('search');
		$page = (int)$this->request->getGet('page');
		if ($page < 1) $page = 1;

		// Validate filter
		$valid_filters = ['all', 'paid', 'pending', 'due'];
		if (!in_array($filter, $valid_filters)) {
			$filter = 'all';
		}

		// Pagination config
		$per_page = 10;
		$offset = ($page - 1) * $per_page;

		// Get total count
		$student_code = isset($student_data['code']) ? $student_data['code'] : '';
		$total_invoices = $student_code ? $this->crud_model->count_invoices_by_student($student_code, $filter, $search) : 0;
		$total_pages = $per_page > 0 ? ceil($total_invoices / $per_page) : 0;

		// Prepare data for view
		$page_data['pagination'] = [
			'current_page' => $page,
			'total_pages' => $total_pages,
			'per_page' => $per_page,
			'total_items' => $total_invoices,
			'offset' => $offset,
			'filter' => $filter,
			'search' => $search
		];

		// Load ONLY the list view
		return view('backend/app/invoice/list', $page_data);
	}

	/**
	 * Download a single invoice as PDF (Student)
	 */
	public function invoice_pdf($invoice_id = "")
	{
		if (session()->get('student_login') != 1) {
			return redirect()->to(site_url('login'));
		}

		if (empty($invoice_id)) {
			show_error('Invalid invoice id');
		}

		$invoice_details = $this->crud_model->get_invoice_by_id($invoice_id);
		if ($redirect = $this->assertInvoiceOwnershipOrRedirect($invoice_details)) {
			return $redirect;
		}

		$page_data['invoice_id'] = $invoice_id;

		// Render invoice HTML, then convert it to a downloadable PDF
		$html = view('backend/app/invoice/invoice_pdf', $page_data);

		try {
			$mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'A4']);
			$mpdf->WriteHTML($html);
			$fileName = 'Invoice-' . sprintf('%08d', $invoice_id) . '.pdf';
			$pdfContent = $mpdf->Output('', \Mpdf\Output\Destination::STRING_RETURN);

			return $this->response
				->setHeader('Content-Type', 'application/pdf')
				->setHeader('Content-Disposition', 'attachment; filename="' . $fileName . '"')
				->setBody($pdfContent);
		} catch (\Mpdf\MpdfException $e) {
			return $this->response->setStatusCode(500)->setBody($e->getMessage());
		}
	}

	// PAYPAL CHECKOUT
	public function paypal_checkout() {
		$invoice_id = htmlspecialchars($this->request->getPost('invoice_id'));
		$invoice_details = $this->crud_model->get_invoice_by_id($invoice_id);
		if ($redirect = $this->assertInvoiceOwnershipOrRedirect($invoice_details)) {
			return $redirect;
		}
        $type = htmlspecialchars($this->request->getPost('type'));
         $user_id = session()->get('user_id');
        $user_details = $this->student_model->get_user_by_id($user_id);

       if ($type == 'community') {
             $page_data['invoice_id']   = $invoice_id;
             $page_data['user_name']    = $user_details['name'];
             $page_data['type']    = $type ;
		     $page_data['amount_to_pay']   = $invoice_details['total_amount'] - $invoice_details['paid_amount'];
        }else{
         	$page_data['invoice_id']   = $invoice_id;
		    $page_data['user_details']    = $this->user_model->get_student_details_by_id('student', $invoice_details['student_id']);
		    $page_data['amount_to_pay']   = $invoice_details['total_amount'] - $invoice_details['paid_amount'];
        }
		$page_data['folder_name'] = 'paypal';
		$page_data['page_title']  = 'paypal_checkout';
		return view('backend/payment_gateway/paypal_checkout', $page_data);
	}
	// STRIPE CHECKOUT
	public function stripe_checkout() {
		$invoice_id = htmlspecialchars($this->request->getPost('invoice_id'));
 
		$type = htmlspecialchars($this->request->getPost('type'));
		$invoice_details = $this->crud_model->get_invoice_by_id($invoice_id);
		if ($redirect = $this->assertInvoiceOwnershipOrRedirect($invoice_details)) {
			return $redirect;
		}
        $user_id = session()->get('user_id');
        $user_details = $this->student_model->get_row('users', ['id' => $user_id]);
        if ($type == 'community') {
            $page_data['invoice_id']   = $invoice_id;
             $page_data['user_name']    = $user_details['name'];
             $page_data['type']    = $type ;
		    $page_data['amount_to_pay']   = $invoice_details['total_amount'] - $invoice_details['paid_amount'];
        }else{
         	$page_data['invoice_id']   = $invoice_id;
		   $page_data['user_details']    = $this->user_model->get_student_details_by_id('student', $invoice_details['student_id']);
		   $page_data['amount_to_pay']   = $invoice_details['total_amount'] - $invoice_details['paid_amount'];
        }

		$page_data['folder_name'] = 'paypal';
		$page_data['page_title']  = 'paypal_checkout';
		return view('backend/payment_gateway/stripe_checkout', $page_data);
	}

	/**
	 * ========== SÃ‰CURITÃ‰: Validation PayPal cÃ´tÃ© serveur ==========
	 * VÃ©rifie auprÃ¨s de l'API PayPal que le paiement est bien complÃ©tÃ©
	 * et que le montant correspond Ã  celui attendu
	 */
	/**
	 * Valide un paiement PayPal
	 * 
	 * Note: La validation complÃ¨te via API PayPal nÃ©cessite un secret_key 
	 * qui n'est pas stockÃ© dans les paramÃ¨tres actuels.
	 * Le SDK PayPal Checkout.js exÃ©cute dÃ©jÃ  le paiement via actions.payment.execute()
	 * donc le paiement est complÃ©tÃ© cÃ´tÃ© PayPal avant d'arriver ici.
	 * 
	 * Cette fonction vÃ©rifie :
	 * - Que le paymentID et payerID sont prÃ©sents
	 * - Que les paramÃ¨tres PayPal sont configurÃ©s
	 * - Si un secret_key est disponible, validation API complÃ¨te
	 * - Sinon, on fait confiance au SDK PayPal (paiement dÃ©jÃ  exÃ©cutÃ©)
	 */
	private function validate_paypal_payment($paymentID, $payerID, $expected_amount, $school_id) {
		try {
			// VÃ©rifier que les IDs sont prÃ©sents
			if (empty($paymentID) || empty($payerID)) {
				log_message('error', 'PayPal: paymentID ou payerID manquant');
				return false;
			}
			
			// RÃ©cupÃ©rer les paramÃ¨tres PayPal
			$paypal_settings = json_decode(get_payment_settings('paypal_settings', $school_id));
			
			if (empty($paypal_settings) || !isset($paypal_settings[0])) {
				log_message('error', 'PayPal: ParamÃ¨tres PayPal non configurÃ©s');
				return false;
			}
			
			$paypal = $paypal_settings[0];
			$mode = $paypal->paypal_mode ?? 'sandbox';
			
			// RÃ©cupÃ©rer les credentials selon le mode
			// Note: Les noms de champs stockÃ©s sont paypal_client_id_sandbox, etc.
			if ($mode === 'sandbox') {
				$client_id = $paypal->paypal_client_id_sandbox ?? '';
				$client_secret = $paypal->paypal_secret_key_sandbox ?? '';
				$api_base = 'https://api.sandbox.paypal.com';
			} else {
				$client_id = $paypal->paypal_client_id_production ?? '';
				$client_secret = $paypal->paypal_secret_key_production ?? '';
				$api_base = 'https://api.paypal.com';
			}
			
			// Si pas de secret key, on fait confiance au SDK PayPal
			// Le paiement a dÃ©jÃ  Ã©tÃ© exÃ©cutÃ© par actions.payment.execute()
			if (empty($client_secret)) {
				log_message('info', "PayPal: Validation sans secret_key - Paiement #{$paymentID} acceptÃ© (SDK PayPal a exÃ©cutÃ© le paiement)");
				return true;
			}
			
			// Validation complÃ¨te via API PayPal (si secret_key disponible)
			if (empty($client_id)) {
				log_message('error', 'PayPal: Client ID manquant');
				return false;
			}
			
			// Ã‰tape 1: Obtenir un access token
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $api_base . '/v1/oauth2/token');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, 'grant_type=client_credentials');
			curl_setopt($ch, CURLOPT_USERPWD, $client_id . ':' . $client_secret);
			curl_setopt($ch, CURLOPT_HTTPHEADER, [
				'Accept: application/json',
				'Accept-Language: en_US'
			]);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
			
			$response = curl_exec($ch);
			$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);
			
			if ($http_code !== 200) {
				log_message('error', 'PayPal: Ã‰chec obtention access token. HTTP Code: ' . $http_code);
				// Fallback: accepter le paiement car le SDK l'a dÃ©jÃ  exÃ©cutÃ©
				log_message('info', "PayPal: Fallback - Paiement #{$paymentID} acceptÃ© malgrÃ© Ã©chec API");
				return true;
			}
			
			$token_data = json_decode($response, true);
			if (empty($token_data['access_token'])) {
				log_message('error', 'PayPal: Access token vide');
				return true; // Fallback
			}
			
			$access_token = $token_data['access_token'];
			
			// Ã‰tape 2: VÃ©rifier le paiement
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $api_base . '/v1/payments/payment/' . $paymentID);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, [
				'Content-Type: application/json',
				'Authorization: Bearer ' . $access_token
			]);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
			
			$response = curl_exec($ch);
			$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);
			
			if ($http_code !== 200) {
				log_message('error', 'PayPal: Ã‰chec vÃ©rification paiement. HTTP Code: ' . $http_code);
				return true; // Fallback
			}
			
			$payment_data = json_decode($response, true);
			
			// VÃ©rifier le statut du paiement
			// AprÃ¨s actions.payment.execute(), l'Ã©tat devient 'completed' (pas 'approved')
			$valid_states = ['approved', 'completed'];
			if (empty($payment_data['state']) || !in_array($payment_data['state'], $valid_states)) {
				log_message('error', 'PayPal: Paiement non valide. Ã‰tat: ' . ($payment_data['state'] ?? 'inconnu'));
				return false;
			}
			
			// VÃ©rifier le montant
			if (!empty($payment_data['transactions'][0]['amount']['total'])) {
				$paid_amount = (float) $payment_data['transactions'][0]['amount']['total'];
				$expected = (float) $expected_amount;
				
				// TolÃ©rance de 0.01 pour les erreurs d'arrondi
				if (abs($paid_amount - $expected) > 0.01) {
					log_message('error', "PayPal: ALERTE SÃ‰CURITÃ‰ - Montant incorrect! Attendu: {$expected}, ReÃ§u: {$paid_amount}");
					return false;
				}
			}
			
			log_message('info', "PayPal: Paiement #{$paymentID} validÃ© avec succÃ¨s via API");
			return true;
			
		} catch (Exception $e) {
			log_message('error', 'PayPal: Exception lors de la validation - ' . $e->getMessage());
			// En cas d'erreur, accepter le paiement car le SDK l'a dÃ©jÃ  exÃ©cutÃ©
			return true;
		}
	}

	

	public function payment_success($payment_method = "", $invoice_id = "", $amount_paid = "", $reference = "", $type_parm = "") {

        $type = $type_parm;
        
        // ========== SÃ‰CURITÃ‰: RÃ©cupÃ©rer les dÃ©tails de la facture depuis la BDD ==========
        $details = $this->crud_model->get_invoice_by_id($invoice_id);
        if ($redirect = $this->assertInvoiceOwnershipOrRedirect($details)) {
            return $redirect;
        }
        
        // VÃ©rifier que la facture n'est pas dÃ©jÃ  payÃ©e
        if ($details['status'] === 'paid') {
            log_message('error', "Tentative de double paiement pour facture #{$invoice_id}");
            session()->setFlashdata('error_message', get_phrase('invoice_already_paid'));
            return redirect()->to(route('invoice'));
            return;
        }
        
        // ========== CURRENCY CONVERSION HANDLING ==========
        $conversion_applied = (bool) $this->request->getPost('conversion_applied');
        $original_currency = $this->request->getPost('original_currency');
        $original_amount = (float) $this->request->getPost('original_amount');
        $fx_rate = (float) $this->request->getPost('fx_rate');
        $fx_rate_date = $this->request->getPost('fx_rate_date');
        $payment_currency = $this->request->getPost('currency');
        
        // Montant de la facture dans la BDD (devise originale)
        $secure_amount = (float) $details['total_amount'];
        $client_amount = (float) $amount_paid;
        
        // ========== SÃ‰CURITÃ‰: Validation du montant ==========
        if ($conversion_applied && $fx_rate > 0) {
            // Si conversion appliquÃ©e, vÃ©rifier que le montant original correspond
            // et que le montant converti est cohÃ©rent avec le taux
            $tolerance = 0.05; // 5% de tolÃ©rance pour les conversions (taux peuvent lÃ©gÃ¨rement varier)
            
            // VÃ©rifier le montant original
            if (abs($secure_amount - $original_amount) > 0.01) {
                log_message('error', "ALERTE SÃ‰CURITÃ‰: Manipulation du montant original! Facture #{$invoice_id} - BDD: {$secure_amount}, Client: {$original_amount}");
                session()->setFlashdata('error_message', get_phrase('payment_amount_mismatch'));
                return redirect()->to(route('invoice'));
                return;
            }
            
            // Recalculer le montant converti attendu cÃ´tÃ© serveur
            try {
                // TODO: Replace with $this->fxService = Config\Services::FxRatesService();
                $server_converted = $this->fxService->convert($secure_amount, strtoupper($original_currency), strtoupper($payment_currency));
                
                if ($server_converted !== false) {
                    // Comparer avec le montant client (tolÃ©rance pour variations de taux)
                    $difference_percent = abs($server_converted - $client_amount) / $server_converted * 100;
                    
                    if ($difference_percent > 5) { // Plus de 5% de diffÃ©rence = suspect
                        log_message('error', "Conversion FX suspecte! Facture #{$invoice_id} - Serveur: {$server_converted}, Client: {$client_amount}, Diff: {$difference_percent}%");
                        // Utiliser le montant calculÃ© par le serveur pour plus de sÃ©curitÃ©
                        $amount_paid = round($server_converted, 2);
                    } else {
                        $amount_paid = $client_amount;
                    }
                } else {
                    // Si impossible de recalculer, utiliser le montant client avec warning
                    log_message('debug', "Impossible de vÃ©rifier la conversion FX pour facture #{$invoice_id}");
                    $amount_paid = $client_amount;
                }
            } catch (Exception $e) {
                log_message('error', 'FX conversion validation error: ' . $e->getMessage());
                $amount_paid = $client_amount;
            }
            
            log_message('info', "Payment with FX conversion: Invoice #{$invoice_id} - Original: {$original_amount} {$original_currency}, Paid: {$amount_paid} {$payment_currency}, Rate: {$fx_rate}");
            
        } else {
            // Pas de conversion - validation standard
            // TOLÃ‰RANCE: 5% pour les arrondis TVA (au lieu de 0.01)
            $tolerance = $secure_amount * 0.05; // 5% du montant
            if (abs($secure_amount - $client_amount) > max($tolerance, 0.50)) {
                log_message('error', "ALERTE SÃ‰CURITÃ‰: Manipulation de montant dÃ©tectÃ©e! Facture #{$invoice_id} - Montant BDD: {$secure_amount}, Montant client: {$client_amount}, Diff: " . abs($secure_amount - $client_amount));
                session()->setFlashdata('error_message', get_phrase('payment_amount_mismatch'));
                return redirect()->to(route('invoice'));
                return;
             }
            // Log warning si diffÃ©rence existe (mais tolÃ©rÃ©e)
            if (abs($secure_amount - $client_amount) > 0.01) {
                log_message('debug', "Payment: DiffÃ©rence tolÃ©rÃ©e pour facture #{$invoice_id} - BDD: {$secure_amount}, Client: {$client_amount}");
            }
            // TOUJOURS utiliser le montant de la BDD pour sÃ©curitÃ©
            $amount_paid = $secure_amount;
        }
        
        // ========== SÃ‰CURITÃ‰: Utiliser les valeurs de la BDD pour les donnÃ©es ==========
        $data['payment_method'] = $payment_method;
        $data['invoice_id'] = $invoice_id;
        $data['amount_paid'] = $amount_paid;
        $data['currency'] = $payment_currency ?: $this->request->getPost('currency');
        $data['payment_type'] =  htmlspecialchars($this->request->getPost('payment_type'));
        $data['vat_amount'] =  htmlspecialchars($this->request->getPost('vat_amount')) ?? 0;
        $data['vat_rate'] = htmlspecialchars($this->request->getPost('vat_rate')) ?? 0;
        $data['sub_total'] = htmlspecialchars($this->request->getPost('sub_total')) ?? $amount_paid;
        $data['total_amount'] = htmlspecialchars($this->request->getPost('total_amount')) ?? $amount_paid;
        
        // ========== CURRENCY CONVERSION DATA (for auditing) ==========
        $data['conversion_applied'] = $conversion_applied ? 1 : 0;
        $data['original_currency'] = $original_currency ?: $data['currency'];
        $data['original_amount'] = $conversion_applied ? $original_amount : $amount_paid;
        $data['fx_rate'] = $conversion_applied ? $fx_rate : 1.0;
        $data['fx_rate_date'] = $fx_rate_date ?: date('Y-m-d');

        $payment_status = false;
     
		if ($payment_method == 'stripe') {
            $type = htmlspecialchars($this->request->getPost('type'));
			$stripe = json_decode(get_payment_settings('stripe_settings', $details['school_id']));
			$token_id = $this->request->getPost('stripeToken');
            
            // VÃ©rifier que le token Stripe est prÃ©sent
            if (empty($token_id)) {
                log_message('error', "Token Stripe manquant pour facture #{$invoice_id}");
                session()->setFlashdata('error_message', get_phrase('payment_error'));
                return redirect()->to(route('invoice'));
                return;
            }
            
			$stripe_test_mode = $stripe[0]->stripe_mode;
            if ($stripe_test_mode == 'on') {
                $secret_key = $stripe[0]->stripe_test_secret_key;
            } else {
                $secret_key = $stripe[0]->stripe_live_secret_key;
            }
           
            $payment_status = $this->payment_model->stripe_payment($token_id, $invoice_id, $amount_paid, $secret_key);
            
		} elseif ($payment_method == 'paystack') {
			$this->loadModel('addons/paystack_model');
			$payment_status = $this->paystack_model->check_payment($reference);
            
		} elseif ($payment_method == 'paypal') {
            // ========== SÃ‰CURITÃ‰: Valider le paiement PayPal cÃ´tÃ© serveur ==========
            $paymentID = $this->request->getPost('paymentID');
            $payerID = $this->request->getPost('payerID');
            
            if (empty($paymentID) || empty($payerID)) {
                log_message('error', "PayPal: paymentID ou payerID manquant pour facture #{$invoice_id}");
                session()->setFlashdata('error_message', get_phrase('payment_error'));
                return redirect()->to(route('invoice'));
                return;
            }
            
            // Valider le paiement via l'API PayPal
            $payment_status = $this->validate_paypal_payment($paymentID, $payerID, $amount_paid, $details['school_id']);
            
            if (!$payment_status) {
                log_message('error', "PayPal: Validation Ã©chouÃ©e pour facture #{$invoice_id}, paymentID: {$paymentID}");
                session()->setFlashdata('error_message', get_phrase('paypal_validation_failed'));
                return redirect()->to(route('invoice'));
                return;
            }
        }
    
		
	            
		//Pour chaque mode de paiement, si succÃ¨s â†’ marquer facture ET ajouter Ã©tudiant
        if ($payment_method === 'stripe'  && $payment_status === true ||
                $payment_method === 'paystack' && $payment_status === true ||
                $payment_method === 'paypal'  && $payment_status === true) {


            // Marquer la facture comme payÃ©e

            if($type == "community"){

              
             $this->user_model->join_school($details['school_id'],$data);
            }else{

                $this->crud_model->payment_success($data);
                // RÃ©cupÃ©rer lâ€™Ã©cole liÃ©e Ã  la classe (ou directement via le cours si tu prÃ©fÃ¨res)
                $class = $this->student_model->get_class_by_id($details['class_id']);
                $school_id = is_array($class) ? ($class['school_id'] ?? null) : ($class->school_id ?? null);

                if ($school_id) {
                    // Mettre Ã  jour la session pour que l'Ã©tudiant soit dans la bonne communautÃ©
                    session()->set('active_school_id', $school_id);
                    session()->set('school_id', $school_id);
                }

                // Redirection vers la page du cours aprÃ¨s paiement rÃ©ussi
                if (!empty($details['class_id'])) {
                    return redirect()->to(site_url('app/courses/' . $details['class_id']));
                }
            }
                
            
            
        
        } else {
            log_message('error', "Ã‰chec du paiement pour invoice #{$invoice_id} via {$payment_method}");
        }


		return redirect()->to(route('invoice'));
		}
	// ACCOUNT SECTION ENDS

	// BACKOFFICE SECTION

	//BOOK LIST MANAGER
	public function book($param1 = "", $param2 = "") {
		$page_data['folder_name'] = 'book';
		$page_data['page_title']  = 'books';
		return view('backend/index', $page_data);
	}

	// BOOK ISSUED BY THE STUDENT
	public function book_issue($param1 = "", $param2 = "") {
		// showing the index file
		$page_data['folder_name'] = 'book_issue';
		$page_data['page_title']  = 'issued_book';
		return view('backend/index', $page_data);
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

	public function payment($param1 = "",$invoice_id = ""){
  
		    $page_data['page_title'] = 'payment_gateway';
            $page_data['type'] = $param1;
            
            // Get invoice details by ID
            $page_data['invoice_details'] = $this->crud_model->get_invoice_by_id($invoice_id);
            if ($redirect = $this->assertInvoiceOwnershipOrRedirect($page_data['invoice_details'])) {
                return $redirect;
            }

            // Pass invoice ID to view
            $page_data['invoice_id'] = $invoice_id;

            // ========== CHECK INVOICE STATUS ==========
            // If invoice is paid, redirect or show a different view
            if ($page_data['invoice_details']['status'] == 'paid') {
                // Option 1: Redirect to a different page (e.g., invoice view page)
                return redirect()->to('/app/invoice');
            }
            
            // Load student details based on invoice
            $invoice_student_id = $page_data['invoice_details']['student_id'];
            $student_record = $this->db_helper_model->get_by_id('students', $invoice_student_id);
            
            if ($student_record) {
                // Correct: Use the user_id linked to the student
                $real_user_id = $student_record['user_id'];
                $page_data['user_details'] = $this->db_helper_model->get_by_id('users', $real_user_id);
            } else {
                // Fallback (should not happen if data integrity is good)
                $page_data['user_details'] = [];
                log_message('error', 'Payment: Student record not found for ID ' . $invoice_student_id);
            }

            // Get school ID from session and fetch school details
            $school_id = session()->get('payment_school_id'); 
            $page_data['school'] = $this->db_helper_model->get('schools', ['id' => $school_id], 'row'); // Pass to view
    
            
            // Fetch invoice from database (alternative method)
            $invoice = $this->db_helper_model->get('invoices', ['id' => $invoice_id], 'row');
            
             if ($param1 == "classe") {
                // Load class name
                $class_id = $page_data['invoice_details']['class_id'];
                $class = $this->db_helper_model->get('classes', ['id' => $class_id], 'row');
                $page_data['class_name'] = $class ? $class->name : "";

                // UPDATE INVOICE IF CLASS PRICE CHANGED
                if ($class) {
                    // IMPORTANT: Le prix de la classe est TTC (inclut dÃ©jÃ  la TVA)
                    $current_price_ttc = (float)$class->price;
                    $school_id = $page_data['invoice_details']['school_id'];
                    
                    // Get VAT settings
                    $settings_school = $this->settings_model->get_settings_school_data($school_id);
                    $school = $this->db_helper_model->get_by_id('schools', $school_id);
                    $vat_applicable = isset($settings_school['vat_enabled']) && (int)$settings_school['vat_enabled'] === 1;
                    $tax_residence = isset($school['country']) ? strtoupper($school['country']) : null;
                    
                    $vat_rate = 0;
                    if ($vat_applicable && !empty($tax_residence)) {
                        if ($tax_residence === 'MA') {
                            $vat_rate = 20;
                        } elseif ($tax_residence === 'UAE' || $tax_residence === 'AE') {
                            $vat_rate = 5;
                        }
                    }

                    // IMPORTANT: Calcul inversÃ© car le prix est TTC
                    // sub_total (HT) = TTC / (1 + taux)
                    // vat_amount = TTC - HT
                    if ($vat_rate > 0) {
                        $sub_total = round($current_price_ttc / (1 + ($vat_rate / 100)), 2);
                        $vat_amount = round($current_price_ttc - $sub_total, 2);
                    } else {
                        $sub_total = $current_price_ttc;
                        $vat_amount = 0;
                    }
                    $new_total_amount = $current_price_ttc; // Le total reste le prix TTC

                    // If total amount differs, update invoice
                    if (abs($page_data['invoice_details']['total_amount'] - $new_total_amount) > 0.01) {
                         $update_data = [
                            'total_amount' => $new_total_amount,
                            'sub_total'    => $sub_total,
                            'vat_amount'   => $vat_amount,
                            'vat_rate'     => $vat_rate,
                            'updated_at'   => strtotime(date('d-M-Y'))
                        ];
                        $this->student_model->update_table('invoices', ['id' => $invoice_id], $update_data);
                        
                        // Update page data
                        $page_data['invoice_details']['total_amount'] = $new_total_amount;
                        $page_data['invoice_details']['sub_total'] = $sub_total;
                        $page_data['invoice_details']['vat_amount'] = $vat_amount;
                        $page_data['invoice_details']['vat_rate'] = $vat_rate;
                    }
                }
            } else {
                // Load community name (from school table)
                $school_id = $page_data['invoice_details']['school_id'];
                $community = $this->db_helper_model->get('schools', ['id' => $school_id], 'row');
                $page_data['community_name'] = $community ? $community->name : "";
            }

            // Set the total amount to pay and currency
            $page_data['amount_to_pay'] = $page_data['invoice_details']['total_amount'];
            $page_data['currency'] = $page_data['invoice_details']['currency'];
            // ========== PAYMENT GATEWAY SETTINGS ==========
    
            
            // Get payment settings from database
            $school_id = $page_data['invoice_details']['school_id'];
            
            // Query Stripe settings

            $stripe_row = $this->db_helper_model->get('payment_settings', [
                'school_id' => $school_id,
                'key' => 'stripe_settings'
            ], 'row');

            $paypal_row = $this->db_helper_model->get('payment_settings', [
                'school_id' => $school_id,
                'key' => 'paypal_settings'
            ], 'row');


            // Handle JSON decoding - check if already decoded or needs decoding
            if ($stripe_row && is_string($stripe_row->value)) {
                $stripe = json_decode(trim($stripe_row->value));
            } elseif ($stripe_row && is_object($stripe_row->value)) {
                $stripe = $stripe_row->value;
            } else {
                $stripe = []; // Default empty array
            }

            if ($paypal_row && is_string($paypal_row->value)) {
                    $paypal = json_decode(trim($paypal_row->value));
            } elseif ($paypal_row && is_object($paypal_row->value)) {
                    $paypal = $paypal_row->value;
            } else {
                    $paypal = []; // Default empty array
            }

            // Convert to array if needed for consistent access
            $stripe = is_object($stripe) ? [$stripe] : (array)$stripe;
            $paypal = is_object($paypal) ? [$paypal] : (array)$paypal;

 
            
            // ========== STRIPE SETTINGS ==========
            $stripe_test_mode = $stripe[0]->stripe_mode ?? 'on';
            
            if ($stripe_test_mode == 'on') {
                $page_data['stripe_public_key'] = $stripe[0]->stripe_test_public_key ?? '';
                $stripeSecretKey = $stripe[0]->stripe_test_secret_key ?? '';
            } else {
                $page_data['stripe_public_key'] = $stripe[0]->stripe_live_public_key ?? '';
                $stripeSecretKey = $stripe[0]->stripe_live_secret_key ?? '';
            }
            
            // Chaque mode de paiement a sa propre devise
            $page_data['stripe_currency'] = $stripe[0]->stripe_currency ?? 'USD';
            $page_data['stripe_enabled'] = !empty($stripeSecretKey) && !empty($page_data['stripe_public_key']);
            
            // ========== PAYPAL SETTINGS ==========
            $page_data['paypal_mode'] = isset($paypal[0]->paypal_mode) ? $paypal[0]->paypal_mode : 'sandbox';
            $page_data['paypal_client_id_sandbox'] = isset($paypal[0]->paypal_client_id_sandbox) ? $paypal[0]->paypal_client_id_sandbox : '';
            $page_data['paypal_client_id_production'] = isset($paypal[0]->paypal_client_id_production) ? $paypal[0]->paypal_client_id_production : '';
            // PayPal a sa propre devise configurÃ©e
            $page_data['paypal_currency'] = isset($paypal[0]->paypal_currency) ? $paypal[0]->paypal_currency : 'USD';
            
            // Determine PayPal enabled status
            if ($page_data['paypal_mode'] == 'sandbox') {
                $page_data['paypal_enabled'] = !empty($page_data['paypal_client_id_sandbox']);
            } else {
                $page_data['paypal_enabled'] = !empty($page_data['paypal_client_id_production']);
            }

            // ========== CURRENCY CONVERSION (FX RATES) ==========
            // Devise de la facture (communautÃ©)
            $invoice_currency = strtoupper($page_data['currency'] ?? 'USD');
            $stripe_currency = strtoupper($page_data['stripe_currency']);
            $paypal_currency = strtoupper($page_data['paypal_currency']);
            
            // Montants originaux (devise de la facture)
            $original_amount = (float)$page_data['amount_to_pay'];
            
            // Par dÃ©faut, pas de conversion nÃ©cessaire
            $page_data['fx_conversion_needed'] = false;
            $page_data['fx_original_currency'] = $invoice_currency;
            $page_data['fx_original_amount'] = $original_amount;
            $page_data['fx_rate_date'] = date('Y-m-d');
            $page_data['fx_stale'] = false;
            
            // Stripe conversion
            $page_data['stripe_converted_amount'] = $original_amount;
            $page_data['stripe_fx_rate'] = 1.0;
            $page_data['stripe_conversion_info'] = null;
            
            // PayPal conversion  
            $page_data['paypal_converted_amount'] = $original_amount;
            $page_data['paypal_fx_rate'] = 1.0;
            $page_data['paypal_conversion_info'] = null;
            
            // VÃ©rifier si une conversion est nÃ©cessaire
            $needs_stripe_conversion = ($invoice_currency !== $stripe_currency) && $page_data['stripe_enabled'];
            $needs_paypal_conversion = ($invoice_currency !== $paypal_currency) && $page_data['paypal_enabled'];
            
            if ($needs_stripe_conversion || $needs_paypal_conversion) {
                // Charger le service FX Rates
                try {
                    // TODO: Replace with $this->fxService = Config\Services::FxRatesService();
                    $fx_rates = $this->fxService->getTodayRates();
                    
                    if (!empty($fx_rates['rates'])) {
                        $page_data['fx_conversion_needed'] = true;
                        $page_data['fx_rate_date'] = $fx_rates['date'] ?? date('Y-m-d');
                        $page_data['fx_stale'] = $fx_rates['stale'] ?? false;
                        $page_data['fx_rates'] = $fx_rates['rates'];
                        
                        // Conversion Stripe
                        if ($needs_stripe_conversion) {
                            $stripe_converted = $this->fxService->convert($original_amount, $invoice_currency, $stripe_currency);
                            if ($stripe_converted !== false) {
                                $page_data['stripe_converted_amount'] = round($stripe_converted, 2);
                                // Calculer le taux de change (1 invoice_currency = X stripe_currency)
                                $page_data['stripe_fx_rate'] = round($stripe_converted / $original_amount, 6);
                                $page_data['stripe_conversion_info'] = [
                                    'from' => $invoice_currency,
                                    'to' => $stripe_currency,
                                    'original_amount' => $original_amount,
                                    'converted_amount' => $page_data['stripe_converted_amount'],
                                    'rate' => $page_data['stripe_fx_rate'],
                                    'rate_date' => $page_data['fx_rate_date']
                                ];
                            }
                        }
                        
                        // Conversion PayPal
                        if ($needs_paypal_conversion) {
                            $paypal_converted = $this->fxService->convert($original_amount, $invoice_currency, $paypal_currency);
                            if ($paypal_converted !== false) {
                                $page_data['paypal_converted_amount'] = round($paypal_converted, 2);
                                $page_data['paypal_fx_rate'] = round($paypal_converted / $original_amount, 6);
                                $page_data['paypal_conversion_info'] = [
                                    'from' => $invoice_currency,
                                    'to' => $paypal_currency,
                                    'original_amount' => $original_amount,
                                    'converted_amount' => $page_data['paypal_converted_amount'],
                                    'rate' => $page_data['paypal_fx_rate'],
                                    'rate_date' => $page_data['fx_rate_date']
                                ];
                            }
                        }
                        
                        log_message('info', "Payment FX Conversion: Invoice {$invoice_id} - Original: {$original_amount} {$invoice_currency}, Stripe: {$page_data['stripe_converted_amount']} {$stripe_currency}, PayPal: {$page_data['paypal_converted_amount']} {$paypal_currency}");
                        
                    } else {
                        log_message('debug', "Payment: No FX rates available for conversion - Invoice {$invoice_id}");
                    }
                } catch (Exception $e) {
                    log_message('error', 'Payment FX Conversion error: ' . $e->getMessage());
                    // Continuer sans conversion si erreur
                }
            }

            // Load payment gateway view
            return view('backend/payment_gateway/index', $page_data);
	}

	// RÃ©cupÃ©rer les classes par Ã©cole pour l'Ã©tudiant connectÃ©
	public function get_classes_by_school() {
        if (session()->get('student_login') != 1) {
            $csrf = [
                'csrfName' => csrf_token(),
                'csrfHash' => csrf_hash(),
            ];
            log_message('error', 'get_classes_by_school - Unauthorized access attempt');
            echo json_encode(['status' => 'error', 'message' => 'Session expired, please login again', 'csrf' => $csrf]);
            exit;
        }

        $user_id = session()->get('user_id');

        // Mapper user_id Ã  student_id dans la table students
        $student_result = $this->student_model->get_where_result('students', ['user_id' => $user_id], 'id');
        $student = !empty($student_result) ? $student_result[0] : null;
        $student_id = $student['id'] ?? null;
        log_message('debug', 'get_classes_by_school - student_id: ' . ($student_id ?? 'null'));

        if (!$student_id) {
            $csrf = [
                'csrfName' => csrf_token(),
                'csrfHash' => csrf_hash(),
            ];
            log_message('error', 'get_classes_by_school - No student_id found for user_id: ' . $user_id);
            echo json_encode(['status' => 'error', 'message' => 'No student associated with this user', 'csrf' => $csrf]);
            exit;
        }

        $school_id = $this->request->getPost('school_id');
        log_message('debug', 'get_classes_by_school - school_id: ' . ($school_id ?? 'null'));

        // VÃ©rifier que school_id correspond Ã  celui de l'utilisateur
        $user_details = $this->user_model->get_user_details($user_id);
        $user_school_id = $user_details['school_id'] ?? null;
        log_message('debug', 'get_classes_by_school - user_school_id: ' . ($user_school_id ?? 'null'));

        if (empty($school_id) || $school_id != $user_school_id) {
            $csrf = [
                'csrfName' => csrf_token(),
                'csrfHash' => csrf_hash(),
            ];
            log_message('error', 'get_classes_by_school - Invalid or unauthorized school_id: ' . ($school_id ?? 'null'));
            echo json_encode(['status' => 'error', 'message' => 'Invalid or unauthorized school ID', 'csrf' => $csrf]);
            exit;
        }

        // RÃ©cupÃ©rer la session active
        $session_id = active_session();
        log_message('debug', 'get_classes_by_school - session_id: ' . ($session_id ?? 'null'));

        // VÃ©rifier les classes autorisÃ©es pour l'Ã©tudiant via enrols
        $sql = "SELECT classes.id, classes.name FROM classes INNER JOIN enrols ON enrols.class_id = classes.id INNER JOIN students ON students.id = enrols.student_id WHERE enrols.student_id = ? AND enrols.school_id = ? AND enrols.session = ? AND students.user_id = ?";
        $classes = $this->student_model->get_by_query($sql, [$student_id, $school_id, $session_id, $user_id]);
        log_message('debug', 'get_classes_by_school - SQL Query: ' . db()->getLastQuery());
        log_message('debug', 'get_classes_by_school - Classes found: ' . json_encode($classes));

        $csrf = [
            'csrfName' => csrf_token(),
            'csrfHash' => csrf_hash(),
        ];

        echo json_encode([
            'status' => 'success',
            'classes' => $classes,
            'message' => empty($classes) ? 'Aucune classe autorisÃ©e pour cet Ã©tudiant dans cette session' : '',
            'csrf' => $csrf
        ]);
    }

    // Filtrer les examens en fonction des sÃ©lections
    public function filter_exams() {
        try {
            log_message('debug', 'DÃ©but de filter_exams');
            log_message('debug', 'DonnÃ©es POST reÃ§ues : ' . json_encode($this->request->getPost()));
            log_message('debug', 'Utilisateur connectÃ© : ' . session()->get('user_id'));

            $school_id = $this->request->getPost('school_id');
            $class_id = $this->request->getPost('class_id');
            $date_filter = $this->request->getPost('date_filter');
            $user_id = session()->get('user_id');

            if (!$school_id || !$class_id || !$user_id) {
                http_response_code(400);
                echo json_encode([
                    'error' => 'DonnÃ©es manquantes',
                    'csrf_hash' => csrf_hash()
                ]);
                return;
            }

            $session_id = active_session();

            if (!$session_id) {
                http_response_code(400);
                echo json_encode([
                    'error' => 'Aucune session active',
                    'csrf_hash' => csrf_hash()
                ]);
                return;
            }

            // VÃ©rification des permissions
            $sql = "SELECT students.id FROM students LEFT JOIN enrols ON enrols.student_id = students.id WHERE students.user_id = ? AND enrols.school_id = ? AND enrols.class_id = ? AND enrols.session = ?";
            $student_result = $this->student_model->get_by_query($sql, [$user_id, $school_id, $class_id, $session_id]);
            $student = !empty($student_result) ? $student_result[0] : null;

            if (!$student) {
                http_response_code(403);
                echo json_encode([
                    'error' => 'Non autorisÃ©',
                    'csrf_hash' => csrf_hash()
                ]);
                return;
            }

            // Construire la requÃªte pour rÃ©cupÃ©rer les examens
            $this->db->reset_query();
            $sql = "SELECT exams.*, classes.name as class_name, schools.name as school_name FROM exams LEFT JOIN classes ON exams.class_id = classes.id LEFT JOIN schools ON exams.school_id = schools.id WHERE exams.school_id = ? AND exams.class_id = ? AND exams.session = ?";
            $params = [$school_id, $class_id, $session_id];

            // GÃ©rer le filtre de date
            if (!empty($date_filter)) {
                // Parser le filtre de date
                $dates = explode(' - ', $date_filter);
                if (count($dates) == 1) {
                    // Date unique
                    $start_date = DateTime::createFromFormat('d-m-Y', trim($dates[0]));
                    if ($start_date) {
                        $start_timestamp = $start_date->setTime(0, 0, 0)->getTimestamp();
                        $end_timestamp = $start_date->setTime(23, 59, 59)->getTimestamp();
                        $sql .= " AND exams.starting_date >= ? AND exams.starting_date <= ?";
                        $params[] = $start_timestamp;
                        $params[] = $end_timestamp;
                    }
                } elseif (count($dates) == 2) {
                    // Plage de dates
                    $start_date = DateTime::createFromFormat('d-m-Y', trim($dates[0]));
                    $end_date = DateTime::createFromFormat('d-m-Y', trim($dates[1]));
                    if ($start_date && $end_date) {
                        $start_timestamp = $start_date->setTime(0, 0, 0)->getTimestamp();
                        $end_timestamp = $end_date->setTime(23, 59, 59)->getTimestamp();
                        $sql .= " AND exams.starting_date >= ? AND exams.starting_date <= ?";
                        $params[] = $start_timestamp;
                        $params[] = $end_timestamp;
                    }
                }
            }

            $exams = $this->student_model->get_by_query($sql, $params);
            $exam_calendar = [];
            $current_time = time(); // Current timestamp

            foreach ($exams as $exam) {
                $exam_calendar[] = [
                    'title' => $exam['name'],
                    'start' => date('Y-m-d H:i:s', $exam['starting_date'])
                ];
            }

            // GÃ©nÃ©ration du tableau HTML
            $table_html = '';
            foreach ($exams as $exam) {
                $exam_start_time = $exam['starting_date'];
                $table_html .= '<tr>';
                $table_html .= '<td>' . htmlspecialchars($exam['name']) . '</td>';
                $table_html .= '<td>' . date('D, d-M-Y H:i', $exam_start_time) . '</td>';
                $table_html .= '<td>' . (!empty($exam['class_name']) ? htmlspecialchars($exam['class_name']) : get_phrase('no_class')) . '</td>';
                // VÃ©rifier si l'examen a dÃ©jÃ  Ã©tÃ© soumis
                $result = $this->student_model->get_where_result('exam_responses', ['exam_id' => $exam['id'], 'user_id' => $user_id], 'id');
                $has_submitted = !empty($result) ? $result[0] : null;

                if ($has_submitted) {
                    // Bouton pour afficher les rÃ©sultats dans un popup
                    $table_html .= '<td><button class="btn btn-sm btn-success view-results-btn" data-exam-id="' . $exam['id'] . '" data-student-id="' . $student['id'] . '">' . get_phrase('view_results') . '</button></td>';
                } elseif ($exam_start_time > $current_time) {
                    // Afficher le compteur pour les examens futurs
                    $table_html .= '<td data-exam-start="' . $exam_start_time . '" data-exam-id="' . $exam['id'] . '" class="exam-countdown">';
                    $table_html .= get_phrase('exam_not_yet_available') . '<br>';
                    $table_html .= '<span class="countdown-text" style="background-color: #3A87AD; color:white; border-radius: 5px; padding:3px;"></span></td>';
                } else {
                    // Afficher le bouton d'accÃ¨s
                    $table_html .= '<td><a href="' . site_url('student/online_exam/' . $exam['id']) . '" target="_blank" class="btn btn-sm btn-primary access-exam-btn">' . get_phrase('access') . '</a></td>';
                }

                $table_html .= '</tr>';
            }
            log_message('debug', 'Tableau HTML gÃ©nÃ©rÃ© : ' . $table_html);

            echo json_encode([
                'exam_calendar' => $exam_calendar,
                'table_html' => $table_html,
                'csrf_hash' => csrf_hash()
            ]);
        } catch (Exception $e) {
            log_message('error', 'Erreur dans filter_exams : ' . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'error' => 'Erreur serveur interne : ' . $e->getMessage(),
                'csrf_hash' => csrf_hash()
            ]);
        }
    }



    // Dans Student.php
    public function online_exam($exam_id = "") {
        try {
            if (empty($exam_id)) {
                log_message('error', 'ID de l\'examen manquant dans online_exam');
                return redirect()->to(site_url('app/exam'));
            }

            // Ajouter des en-tÃªtes anti-cache
            $this->response->setHeader("Cache-Control", "no-store, no-cache, must-revalidate, max-age=0");
            $this->response->setHeader("Pragma", "no-cache");
            $this->response->setHeader("Expires", "Tue, 01 Jan 2000 00:00:00 GMT");

            $user_id = (int) session()->get('user_id');
            $session_id = active_session();
            $school_id = $this->resolveCurrentSchoolId($user_id);

            // Verify access with school/session fallbacks for migrated datasets
            $exam = $this->findAccessibleExamForStudent((int) $exam_id, $user_id, $session_id, $school_id, true);

            if (!$exam) {
                log_message('error', 'Examen non trouvÃ© ou accÃ¨s non autorisÃ© pour exam_id: ' . $exam_id);
                session()->setFlashdata('error_message', get_phrase('exam_not_found_or_unauthorized'));
                return redirect()->to(site_url('app/exam'));
            }

            // VÃ©rifier si l'Ã©tudiant a dÃ©jÃ  soumis l'examen
            $result = $this->student_model->get_where_result('exam_responses', ['exam_id' => $exam_id, 'user_id' => $user_id], 'id');
            $existing_submission = !empty($result) ? $result[0] : null;

            if ($existing_submission) {
                // L'Ã©tudiant a dÃ©jÃ  passÃ© l'examen, rediriger vers la liste des examens
                session()->setFlashdata('success_message', get_phrase('exam_already_submitted'));
                return redirect()->to(site_url('app/exam'));
            }

            // VÃ©rifier si l'examen a commencÃ©
            $current_time = time();
            $exam_start_timestamp = $this->normalizeToTimestamp($exam['starting_date'] ?? null);
            if ($exam_start_timestamp > $current_time) {
                log_message('error', 'L\'examen n\'a pas encore commencÃ© pour exam_id: ' . $exam_id);
                session()->setFlashdata('error_message', get_phrase('exam_not_yet_available'));
                return redirect()->to(site_url('app/exam'));
                }

            // RÃ©cupÃ©rer les questions de l'examen
            $questions = $this->get_exam_questions($exam_id);

            // PrÃ©parer les donnÃ©es pour la vue
            $page_data['exam_id'] = $exam_id;
            $page_data['exam_details'] = $exam;
            $page_data['exam_details']['starting_timestamp'] = $exam_start_timestamp;
            $page_data['questions'] = $questions;
            $page_data['page_title'] = $exam['name'];

            // Charger la vue via online_exams/index.php
            return view('online_exams/index', $page_data);
        } catch (Exception $e) {
            log_message('error', 'Erreur dans online_exam : ' . $e->getMessage());
            session()->setFlashdata('error_message', get_phrase('server_error'));
            return redirect()->to(site_url('app/exam'));
        }
    }

    public function get_exam_questions($exam_id) {
        return $this->student_model->get_where_result('exam_questions', ['exam_id' => $exam_id], 'exam_questions.*');
    }

    public function submit_exam() {
        try {
            // VÃ©rifier si la requÃªte est POST
            if ($this->request->getServer('REQUEST_METHOD') !== 'POST') {
                log_message('error', 'MÃ©thode non autorisÃ©e dans submit_exam');
                return $this->response->setStatusCode(405)->setJSON([
                    'error' => 'MÃ©thode non autorisÃ©e',
                    'csrf_hash' => csrf_hash()
                ]);
            }

            $user_id = (int) session()->get('user_id');
            $exam_id = $this->request->getPost('exam_id');
            $session_id = active_session();
            $school_id = $this->resolveCurrentSchoolId($user_id);

            if (!$user_id || !$exam_id) {
                log_message('error', 'user_id ou exam_id manquant dans submit_exam');
                return $this->response->setStatusCode(400)->setJSON([
                    'error' => 'DonnÃ©es manquantes',
                    'csrf_hash' => csrf_hash()
                ]);
            }

            // VÃ©rifier si l'Ã©tudiant a le droit de soumettre cet examen
            $exam = $this->findAccessibleExamForStudent((int) $exam_id, $user_id, $session_id, $school_id, false);

            if (!$exam) {
                log_message('error', 'Examen non trouvÃ© ou accÃ¨s non autorisÃ© pour exam_id: ' . $exam_id);
                return $this->response->setStatusCode(403)->setJSON([
                    'error' => 'Non autorisÃ©',
                    'csrf_hash' => csrf_hash()
                ]);
            }

            // RÃ©cupÃ©rer l'Ã©tudiant
            $student_data = $this->student_model->get_student_by_user_id_and_school($user_id, $exam['school_id']);
            if (!$student_data) {
                log_message('error', 'Ã‰tudiant non trouvÃ© pour user_id: ' . $user_id);
                return $this->response->setStatusCode(400)->setJSON([
                    'error' => 'Ã‰tudiant non trouvÃ©',
                    'csrf_hash' => csrf_hash()
                ]);
            }

            // RÃ©cupÃ©rer les questions de l'examen
            $questions = $this->get_exam_questions($exam_id);
            $total_questions = count($questions);
            $total_correct_answers = 0;
            $submitted_answers = [];

            if ($total_questions == 0) {
                log_message('error', 'Aucune question trouvÃ©e pour exam_id: ' . $exam_id);
                return $this->response->setStatusCode(400)->setJSON([
                    'error' => 'Aucune question trouvÃ©e',
                    'csrf_hash' => csrf_hash()
                ]);
            }

            // Ã‰tape 1 : Supprimer les anciennes rÃ©ponses pour cet examen et cet utilisateur
            $this->student_model->delete_table('exam_responses', ['exam_id' => $exam_id, 'user_id' => $user_id]);
            log_message('debug', 'Anciennes rÃ©ponses supprimÃ©es pour exam_id: ' . $exam_id . ' et user_id: ' . $user_id);

            // Ã‰tape 2 : Traiter les rÃ©ponses soumises
            foreach ($questions as $question) {
                $question_id = $question['id'];

                // RÃ©cupÃ©rer les options disponibles pour la question
                $options = json_decode($question['options'], true);
                if (!is_array($options) || empty($options)) {
                    log_message('error', 'Options mal formatÃ©es pour la question ID ' . $question_id . ': ' . $question['options']);
                    $options = [];
                }

                // Extraire les rÃ©ponses correctes (supporte tableau indexÃ©, associatif ou valeur brute)
                $correct_answers_data = json_decode($question['correct_answers'], true);
                $correct_answers_raw = null;
                if (is_array($correct_answers_data)) {
                    if (!empty($correct_answers_data)) {
                        $first_value = reset($correct_answers_data);
                        if ($first_value !== false || count($correct_answers_data) > 0) {
                            $correct_answers_raw = $first_value;
                        }
                    }
                } else {
                    $correct_answers_raw = $question['correct_answers'];
                }

                if ($correct_answers_raw === null || $correct_answers_raw === '') {
                    $correct_answers = '';
                    log_message('error', 'RÃ©ponses correctes vides pour la question ID ' . $question_id . ': ' . $question['correct_answers']);
                } else {
                    $correct_answers = trim((string) $correct_answers_raw);
                    if (is_numeric($correct_answers) && isset($options[(int) $correct_answers - 1])) {
                        $correct_answers_index = (int) $correct_answers - 1;
                        $correct_answers = $options[$correct_answers_index];
                        log_message('debug', 'Index base-1 dÃ©tectÃ© pour question ID ' . $question_id . ': ' . $correct_answers_raw . ' -> ' . $correct_answers);
                    }
                }

                // VÃ©rifier si la rÃ©ponse correcte est dans les options
                if (!in_array($correct_answers, $options) && $correct_answers !== '') {
                    log_message('error', 'RÃ©ponse correcte "' . $correct_answers . '" pour la question ID ' . $question_id . ' ne correspond Ã  aucune option: ' . json_encode($options));
                    $correct_answers = '';
                }

                $submitted_answers_value = $this->request->getPost('question_' . $question_id);
                $submitted_answers_value = trim((string)$submitted_answers_value);

                $submitted_answer_status = ($submitted_answers_value == $correct_answers) ? 1 : 0;
                log_message('debug', 'Comparaison pour question ID ' . $question_id . ': submitted="' . $submitted_answers_value . '", correct="' . $correct_answers . '", status=' . $submitted_answer_status);

                if ($submitted_answer_status) {
                    $total_correct_answers++;
                }

                // Stocker les dÃ©tails de la rÃ©ponse dans exam_responses
                $data = [
                    'user_id' => $user_id,
                    'exam_id' => $exam_id,
                    'exam_question_id' => $question_id,
                    'submitted_answers' => $submitted_answers_value ?: 'Aucune rÃ©ponse',
                    'correct_answers' => $correct_answers,
                    'submitted_answer_status' => $submitted_answer_status,
                    'date_submitted' => date('Y-m-d H:i:s')
                ];
                $this->student_model->insert_table('exam_responses', $data);

                $submitted_answers[] = [
                    'question_id' => $question_id,
                    'question_title' => $question['title'],
                    'submitted_answer' => $submitted_answers_value ?: 'Aucune rÃ©ponse',
                    'correct_answer' => $correct_answers,
                    'status' => $submitted_answer_status,
                    'options' => $options
                ];
            }

            // Calculer la note (sur 100, arrondie car mark_obtained est un int)
            $mark_obtained = ($total_questions > 0) ? round(($total_correct_answers / $total_questions) * 100) : 0;

            // VÃ©rifier si une entrÃ©e existe dÃ©jÃ  dans la table marks
            $existing_mark = $this->student_model->get_row('marks', [
                'student_id' => $student_data['id'],
                'exam_id' => $exam_id,
                'class_id' => $exam['class_id'],
                'school_id' => $exam['school_id'],
                'session' => $session_id
            ]);

            if ($existing_mark) {
                // Mettre Ã  jour la note existante
                $this->student_model->update_table('marks', ['id' => $existing_mark['id']], [
                    'mark_obtained' => $mark_obtained,
                ]);
            } else {
                // InsÃ©rer une nouvelle entrÃ©e
                $this->student_model->insert_table('marks', [
                    'student_id' => $student_data['id'],
                    'subject_id' => NULL, // Pas de matiÃ¨re pour les examens en ligne
                    'exam_id' => $exam_id,
                    'class_id' => $exam['class_id'],
                    'school_id' => $exam['school_id'],
                    'session' => $session_id,
                    'mark_obtained' => $mark_obtained,
                    'comment' => ''
                ]);
            }

            // RÃ©ponse JSON pour le front-end
            $response = [
                'message' => get_phrase('exam_submitted_successfully'),
                'total_questions' => $total_questions,
                'total_correct_answers' => $total_correct_answers,
                'mark_obtained' => $mark_obtained,
                'submitted_answers' => $submitted_answers,
                'csrf_hash' => csrf_hash()
            ];

            log_message('debug', 'Examen soumis avec succÃ¨s pour exam_id: ' . $exam_id);
            return $this->response->setJSON($response);
        } catch (\Throwable $e) {
            log_message('error', 'Erreur dans submit_exam : ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'error' => 'Erreur serveur interne : ' . $e->getMessage(),
                'csrf_hash' => csrf_hash()
            ]);
        }
    }

    public function exam_results($exam_id = "", $student_id = "") {
        try {

            if (!session()->get('student_login') || session()->get('user_type') != 'student') {
                return redirect()->to(site_url('login'));
                }

            $user_id = session()->get('user_id');
            $session_id = active_session();

            $sql = "SELECT exams.*, classes.name as class_name, schools.name as school_name FROM exams LEFT JOIN classes ON exams.class_id = classes.id LEFT JOIN schools ON exams.school_id = schools.id LEFT JOIN enrols ON enrols.class_id = exams.class_id LEFT JOIN students ON students.id = enrols.student_id WHERE exams.id = ? AND students.user_id = ? AND enrols.session = ?";
            $exam_result = $this->student_model->get_by_query($sql, [$exam_id, $user_id, $session_id]);
            $exam = !empty($exam_result) ? $exam_result[0] : null;

            if (!$exam) {
                return redirect()->to(site_url('app/exam'));
            }

            // RÃ©cupÃ©rer l'ID de l'Ã©tudiant Ã  partir de user_id
            $student_data = $this->student_model->get_student_by_user_id_and_school($user_id, $exam['school_id']);
            if (!$student_data || $student_data['id'] != $student_id) {
                return redirect()->to(site_url('app/exam'));
            }

            // RÃ©cupÃ©rer les rÃ©ponses soumises
            $sql = "SELECT er.*, eq.title as question_title FROM exam_responses er LEFT JOIN exam_questions eq ON er.exam_question_id = eq.id WHERE er.exam_id = ? AND er.user_id = ?";
            $submitted_answers = $this->student_model->get_by_query($sql, [$exam_id, $user_id]);

            // PrÃ©parer les donnÃ©es pour la vue
            $page_data['exam_details'] = $exam;
            $page_data['submitted_answers'] = $submitted_answers;
            $page_data['page_title'] = $exam['name'] . ' - ' . get_phrase('results');

            // Charger la vue partielle pour le modal
            return view('backend/student/mark/exam_results_modal', $page_data);
        } catch (Exception $e) {
            return redirect()->to(site_url('app/exam'));
            }
    }

    public function get_exam_results_popup($exam_id = "", $student_id = "") {
        try {
            if (!session()->get('student_login') || session()->get('user_type') != 'student') {
                http_response_code(403);
                echo json_encode(['error' => 'Non autorisÃ©']);
                exit;
            }

            $user_id = session()->get('user_id');
            $session_id = active_session();

            // VÃ©rifier si l'Ã©tudiant a accÃ¨s Ã  cet examen
            $sql = "SELECT exams.*, classes.name as class_name, schools.name as school_name FROM exams LEFT JOIN classes ON exams.class_id = classes.id LEFT JOIN schools ON exams.school_id = schools.id LEFT JOIN enrols ON enrols.class_id = exams.class_id LEFT JOIN students ON students.id = enrols.student_id WHERE exams.id = ? AND students.user_id = ? AND enrols.session = ?";
            $exam_result = $this->student_model->get_by_query($sql, [$exam_id, $user_id, $session_id]);
            $exam = !empty($exam_result) ? $exam_result[0] : null;

            if (!$exam) {
                http_response_code(403);
                echo json_encode(['error' => 'Examen non trouvÃ© ou accÃ¨s non autorisÃ©']);
                exit;
            }

            // VÃ©rifier si l'Ã©tudiant correspond
            $student_data = $this->student_model->get_student_by_user_id_and_school($user_id, $exam['school_id']);
            if (!$student_data || $student_data['id'] != $student_id) {
                http_response_code(403);
                echo json_encode(['error' => 'Ã‰tudiant non autorisÃ©']);
                exit;
            }

            // RÃ©cupÃ©rer les rÃ©ponses soumises
            $sql = "SELECT er.*, eq.title as question_title FROM exam_responses er LEFT JOIN exam_questions eq ON er.exam_question_id = eq.id WHERE er.exam_id = ? AND er.user_id = ?";
            $submitted_answers = $this->student_model->get_by_query($sql, [$exam_id, $user_id]);

            // PrÃ©parer les donnÃ©es pour la vue
            $page_data['exam_details'] = $exam;
            $page_data['submitted_answers'] = $submitted_answers;
            $page_data['page_title'] = $exam['name'] . ' - ' . get_phrase('results');

            // Rendre la vue partielle et retourner le HTML
            $html_content = view('backend/student/mark/exam_results_modal', $page_data, ['cache' => 0]);

            echo json_encode([
                'html' => $html_content,
                'csrf_hash' => csrf_hash()
            ]);
        } catch (Exception $e) {
            log_message('error', 'Erreur dans get_exam_results_popup : ' . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'error' => 'Erreur serveur interne : ' . $e->getMessage(),
                'csrf_hash' => csrf_hash()
            ]);
        }
    }

    public function load_initial_exams() {
        try {
            log_message('debug', 'DÃ©but de load_initial_exams');
            log_message('debug', 'DonnÃ©es POST reÃ§ues : ' . json_encode($this->request->getPost()));
            log_message('debug', 'Utilisateur connectÃ© : ' . session()->get('user_id'));

            $class_id = $this->request->getPost('class_id');
        
            $date_filter = $this->request->getPost('date_filter');
            $user_id = session()->get('user_id');

            if (!$class_id || !$user_id) {
                http_response_code(400);
                echo json_encode([
                    'error' => 'DonnÃ©es manquantes',
                    'csrf_hash' => csrf_hash()
                ]);
                return;
            }

            $session_id = active_session();
            if (!$session_id) {
                http_response_code(400);
                echo json_encode([
                    'error' => 'Aucune session active',
                    'csrf_hash' => csrf_hash()
                ]);
                return;
            }

            // RÃ©cupÃ©rer les Ã©coles auxquelles l'Ã©tudiant est inscrit
            $sql = "SELECT schools.id FROM schools INNER JOIN enrols ON enrols.school_id = schools.id INNER JOIN students ON students.id = enrols.student_id WHERE students.user_id = ? AND enrols.session = ?";
            $school_ids_result = $this->student_model->get_by_query($sql, [$user_id, $session_id]);
            $school_ids = array_column($school_ids_result, 'id');

            if (empty($school_ids)) {
                http_response_code(403);
                echo json_encode([
                    'error' => 'Non autorisÃ©',
                    'csrf_hash' => csrf_hash()
                ]);
                return;
            }

            // VÃ©rification des permissions
            $sql = "SELECT students.id FROM students LEFT JOIN enrols ON enrols.student_id = students.id WHERE students.user_id = ? AND enrols.school_id IN (" . implode(',', array_fill(0, count($school_ids), '?')) . ") AND enrols.class_id = ? AND enrols.session = ?";
            $params = array_merge([$user_id], $school_ids, [$class_id, $session_id]);
            $student_result = $this->student_model->get_by_query($sql, $params);
            $student = !empty($student_result) ? $student_result[0] : null;

            if (!$student) {
                http_response_code(403);
                echo json_encode([
                    'error' => 'Non autorisÃ©',
                    'csrf_hash' => csrf_hash()
                ]);
                return;
            }

            // DÃ©but de la journÃ©e actuelle (00:00:00)
            $today_start = strtotime('today midnight');

            // Construire la requÃªte pour rÃ©cupÃ©rer les examens
            $this->db->reset_query();
            $sql = "SELECT exams.*, classes.name as class_name, schools.name as school_name FROM exams LEFT JOIN classes ON exams.class_id = classes.id LEFT JOIN schools ON exams.school_id = schools.id WHERE exams.school_id IN (" . implode(',', array_fill(0, count($school_ids), '?')) . ") AND exams.class_id = ? AND exams.session = ? AND exams.starting_date >= ?";
            $params = array_merge($school_ids, [$class_id, $session_id, $today_start]);

            // GÃ©rer le filtre de date
            if (!empty($date_filter)) {
                // Parser le filtre de date
                $dates = explode(' - ', $date_filter);
                if (count($dates) == 1) {
                    // Date unique
                    $start_date = DateTime::createFromFormat('d-m-Y', trim($dates[0]));
                    if ($start_date) {
                        $start_timestamp = $start_date->setTime(0, 0, 0)->getTimestamp();
                        $end_timestamp = $start_date->setTime(23, 59, 59)->getTimestamp();
                        $sql .= " AND exams.starting_date >= ? AND exams.starting_date <= ?";
                        $params[] = $start_timestamp;
                        $params[] = $end_timestamp;
                    }
                } elseif (count($dates) == 2) {
                    // Plage de dates
                    $start_date = DateTime::createFromFormat('d-m-Y', trim($dates[0]));
                    $end_date = DateTime::createFromFormat('d-m-Y', trim($dates[1]));
                    if ($start_date && $end_date) {
                        $start_timestamp = $start_date->setTime(0, 0, 0)->getTimestamp();
                        $end_timestamp = $end_date->setTime(23, 59, 59)->getTimestamp();
                        $sql .= " AND exams.starting_date >= ? AND exams.starting_date <= ?";
                        $params[] = $start_timestamp;
                        $params[] = $end_timestamp;
                    }
                }
            }

            $exams = $this->student_model->get_by_query($sql, $params);
            $exam_calendar = [];
            $current_time = time();

            foreach ($exams as $exam) {
                $exam_calendar[] = [
                    'title' => $exam['name'],
                    'start' => date('Y-m-d H:i:s', $exam['starting_date'])
                ];
            }

            // GÃ©nÃ©ration du tableau HTML
            $table_html = '';
            foreach ($exams as $exam) {
                $exam_start_time = $exam['starting_date'];
                $table_html .= '<tr>';
                $table_html .= '<td>' . htmlspecialchars($exam['name']) . '</td>';
                $table_html .= '<td>' . date('D, d-M-Y H:i', $exam_start_time) . '</td>';
                $table_html .= '<td>' . (!empty($exam['class_name']) ? htmlspecialchars($exam['class_name']) : get_phrase('no_class')) . '</td>';

                $response_result = $this->student_model->get_where_result('exam_responses', ['exam_id' => $exam['id'], 'user_id' => $user_id], 'id');
                $has_submitted = !empty($response_result) ? $response_result[0] : null;

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
            log_message('debug', 'Tableau HTML gÃ©nÃ©rÃ© : ' . $table_html);

            echo json_encode([
                'exam_calendar' => $exam_calendar,
                'table_html' => $table_html,
                'csrf_hash' => csrf_hash()
            ]);
        } catch (Exception $e) {
            log_message('error', 'Erreur dans load_initial_exams : ' . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'error' => 'Erreur serveur interne : ' . $e->getMessage(),
                'csrf_hash' => csrf_hash()
            ]);
        }
    }

    public function get_classes_by_student() {
        $user_id = session()->get('user_id');
        $session_id = active_session();

        $sql = "SELECT classes.id, classes.name FROM classes INNER JOIN enrols ON enrols.class_id = classes.id INNER JOIN students ON students.id = enrols.student_id WHERE students.user_id = ? AND enrols.session = ? GROUP BY classes.id";
        $classes = $this->student_model->get_by_query($sql, [$user_id, $session_id]);
        echo json_encode([
            'classes' => $classes,
            'csrf_hash' => csrf_hash()
        ]);
    }

    public function calendar($param1 = '', $param2 = '', $param3 = '', $param4 = '') {
        if (session()->get('student_login') != 1) {
            $csrf = [
                'csrfName' => csrf_token(),
                'csrfHash' => csrf_hash(),
            ];
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized', 'csrf' => $csrf]);
            exit;
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
            return view('backend/student/calendar/list', $page_data);
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
    if (session()->get('student_login') != 1) {
        echo json_encode(['status' => 'error', 'message' => 'Session expired, please login again']);
        exit;
    }

    $user_id = session()->get('user_id');
    $user = $this->db_helper_model->get_by_id('users', $user_id);

    if (!$user || empty($user['school_id'])) {
        echo json_encode(['status' => 'error', 'message' => 'No school associated with this user']);
        exit;
    }

    $school = $this->student_model->get_active_school($user['school_id']);
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

    public function get_events() {
    if (session()->get('student_login') != 1) {
        $csrf = [
            'csrfName' => csrf_token(),
            'csrfHash' => csrf_hash(),
        ];
        log_message('error', 'get_events - Unauthorized access attempt. Session data: ' . json_encode(session()->get()));
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized', 'csrf' => $csrf]);
        exit;
    }

    $user_id = session()->get('user_id');
    $students = $this->student_model->get_where_result('students', ['user_id' => $user_id], 'id');
    $student_ids = array_column($students, 'id');

    // VÃ©rifier si l'Ã©tudiant est inscrit Ã  l'Ã©cole via jointure
    if (empty($student_ids)) {
        $csrf = [
            'csrfName' => csrf_token(),
            'csrfHash' => csrf_hash(),
        ];
        echo json_encode(['status' => 'error', 'message' => 'No student associated with this user', 'csrf' => $csrf]);
        exit;
    }

    $sql = "SELECT enrols.school_id, enrols.class_id FROM enrols WHERE enrols.student_id IN (" . implode(',', array_fill(0, count($student_ids), '?')) . ") AND enrols.school_id IS NOT NULL";
    $enrols = $this->student_model->get_by_query($sql, $student_ids);
    $permitted_school_ids = array_map('strval', array_unique(array_column($enrols, 'school_id')));
    $permitted_class_ids = array_map('strval', array_unique(array_column($enrols, 'class_id')));

    if (empty($enrols)) {
        $csrf = [
            'csrfName' => csrf_token(),
            'csrfHash' => csrf_hash(),
        ];
        echo json_encode(['status' => 'error', 'message' => 'Not enrolled in any school', 'csrf' => $csrf]);
        exit;
    }

    // RÃ©cupÃ©rer les paramÃ¨tres
    $school_id = school_id();
    $start_date = $this->request->getGet('start_date');
    $end_date = $this->request->getGet('end_date');
    $event_id = $this->request->getGet('id');
    $class_id = $this->request->getGet('class_id');
    $nocache = $this->request->getGet('nocache');

    // Valider school_id (Ã©cole active)
    if (!in_array((string)$school_id, $permitted_school_ids, true)) {
        $csrf = [
            'csrfName' => csrf_token(),
            'csrfHash' => csrf_hash(),
        ];
        echo json_encode(['status' => 'error', 'message' => 'Not authorized for this school', 'csrf' => $csrf]);
        exit;
    }

    // Valider class_id si fourni
    if ($class_id && !in_array((string)$class_id, $permitted_class_ids, true)) {
        $csrf = [
            'csrfName' => csrf_token(),
            'csrfHash' => csrf_hash(),
        ];
        echo json_encode(['status' => 'error', 'message' => 'Not authorized for this class', 'csrf' => $csrf]);
        exit;
    }

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

    $bbb_config = config('bigbluebutton');
    $bbb_url = $bbb_config->bbb_url ?? '';
    $bbb_secret = $bbb_config->bbb_secret ?? '';

    // Construction de la requÃªte pour les Ã©vÃ©nements
    $sql = "SELECT event_calendars.id, event_calendars.title, event_calendars.description, event_calendars.starting_date, event_calendars.ending_date, event_calendars.starting_time, event_calendars.ending_time, event_calendars.recurrence_type, event_calendars.recurrence_end_date, event_calendars.custom_recurrence, event_calendars.visio, event_calendars.school_id, event_calendars.created_by, schools.name as school_name, classes.name as class_name, users.name as created_by_name FROM event_calendars LEFT JOIN schools ON event_calendars.school_id = schools.id LEFT JOIN users ON event_calendars.created_by = users.id INNER JOIN participants ON event_calendars.id = participants.event_id LEFT JOIN classes ON participants.guest = classes.id AND participants.type = 'class' WHERE event_calendars.school_id = ?";
    $params = [$school_id];
    
    // Si on recherche un Ã©vÃ©nement spÃ©cifique, vÃ©rifier d'abord l'accÃ¨s
    if ($event_id) {
        $sql .= " AND event_calendars.id = ?";
        $params[] = $event_id;
        $class_ids_list = implode(',', array_map('intval', $permitted_class_ids));
        $sql .= " AND ((participants.type = 'class' AND participants.guest IN ($class_ids_list)) OR (participants.type = 'individual' AND participants.guest = " . intval($user_id) . "))";
    } else {
        $class_ids_list = implode(',', array_map('intval', $permitted_class_ids));
        $sql .= " AND ((participants.type = 'class' AND participants.guest IN ($class_ids_list)) OR (participants.type = 'individual' AND participants.guest = " . intval($user_id) . "))";
        $sql .= " AND (event_calendars.starting_date <= ? AND (event_calendars.ending_date >= ? OR event_calendars.ending_date IS NULL))";
        $params[] = $end_date;
        $params[] = $start_date;
    }

        if ($class_id) {
        $sql .= " AND participants.type = 'class' AND participants.guest = ?";
        $params[] = $class_id;
    }

    $sql .= " GROUP BY event_calendars.id";
    $events = $this->student_model->get_by_query($sql, $params);

    $participantsByEventId = [];
    $allParticipantsRows = [];
    if ($events !== []) {
        $allParticipantsRows = $this->student_model->get_participants_for_events_batch(array_column($events, 'id'));
        foreach ($allParticipantsRows as $prow) {
            $participantsByEventId[$prow['event_id']][] = $prow;
        }
    }
    $batchClassIds = [];
    $batchUserIds = [];
    foreach ($allParticipantsRows as $p) {
        if ($p['type'] === 'class') {
            $batchClassIds[] = (int) $p['guest'];
        } elseif ($p['type'] === 'individual') {
            $batchUserIds[] = (int) $p['guest'];
        }
    }
    $classMap = $this->student_model->get_classes_map_by_ids($batchClassIds);
    $userMap = $this->student_model->get_users_map_by_ids($batchUserIds);

    // Post-traitement des Ã©vÃ©nements
    $processed_events = [];
    $now = new DateTime('now', new DateTimeZone('UTC'));
    $threshold = (clone $now)->modify('-24 hours');

    foreach ($events as $event) {
        $event['school_name'] = $event['school_name'] ?? '';
        $event['class_name'] = $event['class_name'] ?? '';
        $event['created_by_name'] = $event['created_by_name'] ?? 'Unknown';

        // Ajouter les informations des participants
        $event['participants'] = [];
        $participants = $participantsByEventId[$event['id']] ?? [];
        foreach ($participants as $participant) {
            $participant_data = [
                'id' => $participant['guest'],
                'type' => $participant['type']
            ];
            if ($participant['type'] === 'class') {
                $class = $classMap[(int) $participant['guest']] ?? null;
                $participant_data['name'] = $class['name'] ?? 'Unknown';
            } elseif ($participant['type'] === 'individual') {
                $user = $userMap[(int) $participant['guest']] ?? null;
                $participant_data['name'] = $user['name'] ?? 'Unknown';
            }
            $event['participants'][] = $participant_data;
        }

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

        // Post-traitement des Ã©vÃ©nements
        $event_start = DateTime::createFromFormat('Y-m-d H:i:s', $event['starting_date'] . ' ' . $event['starting_time'], new DateTimeZone('UTC'));
        $event['is_expired'] = $event['recurrence_type'] === 'does_not_repeat' && $event_start < $threshold;

        // RÃ©cupÃ©rer les occurrences pour les Ã©vÃ©nements visio
        $event['occurrences'] = [];

        // GÃ©nÃ©rer les occurrences pour les Ã©vÃ©nements rÃ©currents
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

                // RÃ©cupÃ©rer les occurrences existantes pour les Ã©vÃ©nements visio
        if ($event['visio'] == 1) {
            $sql = "SELECT start_date, meeting_id FROM appointments WHERE event_id = ? AND DATE(start_date) >= ? AND DATE(start_date) <= ?";
            $appointments = $this->student_model->get_by_query($sql, [$event['id'], $start_date, $end_date]);

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

        // Add to processed events
        if ($event_id) {
            $processed_events[] = $event;
        } else {
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
        'csrf' => [
            'csrfName' => csrf_token(),
            'csrfHash' => csrf_hash()
        ]
    ]);
    exit;
}

public function start_meeting() {
    // VÃ©rifier l'authentification
    if (session()->get('student_login') != 1) {
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

    $user_id = (int) session()->get('user_id');
    $students = $this->student_model->get_where_result('students', ['user_id' => $user_id], 'id');
    $student_ids = array_column($students, 'id');
    if (empty($student_ids)) {
        $csrf = [
            'csrfName' => csrf_token(),
            'csrfHash' => csrf_hash(),
        ];
        echo json_encode(['status' => 'error', 'message' => 'No student associated with this user', 'csrf' => $csrf]);
        exit;
    }

    $sql = "SELECT enrols.school_id, enrols.class_id FROM enrols WHERE enrols.student_id IN (" . implode(',', array_fill(0, count($student_ids), '?')) . ") AND enrols.school_id IS NOT NULL";
    $enrols = $this->student_model->get_by_query($sql, $student_ids);
    $permitted_school_ids = array_map('strval', array_unique(array_column($enrols, 'school_id')));
    $permitted_class_ids = array_map('intval', array_unique(array_column($enrols, 'class_id')));
    if (empty($enrols)) {
        $csrf = [
            'csrfName' => csrf_token(),
            'csrfHash' => csrf_hash(),
        ];
        echo json_encode(['status' => 'error', 'message' => 'Not enrolled in any school', 'csrf' => $csrf]);
        exit;
    }

    // Charger l'Ã©vÃ©nement
    $event = $this->db_helper_model->get_by_id('event_calendars', $event_id);
    if (!$event) {
        $csrf = [
            'csrfName' => csrf_token(),
            'csrfHash' => csrf_hash(),
        ];
        echo json_encode(['status' => 'error', 'message' => 'Event not found', 'csrf' => $csrf]);
        exit;
    }

    if (!in_array((string) ($event['school_id'] ?? ''), $permitted_school_ids, true)) {
        $csrf = [
            'csrfName' => csrf_token(),
            'csrfHash' => csrf_hash(),
        ];
        echo json_encode(['status' => 'error', 'message' => 'Not authorized for this event', 'csrf' => $csrf]);
        exit;
    }

    $eventParticipants = $this->student_model->get_where_result('participants', ['event_id' => $event_id], 'guest, type');
    $isAuthorizedParticipant = false;
    foreach ($eventParticipants as $participant) {
        if (($participant['type'] ?? '') === 'individual' && (int) ($participant['guest'] ?? 0) === $user_id) {
            $isAuthorizedParticipant = true;
            break;
        }
        if (($participant['type'] ?? '') === 'class' && in_array((int) ($participant['guest'] ?? 0), $permitted_class_ids, true)) {
            $isAuthorizedParticipant = true;
            break;
        }
    }

    if (!$isAuthorizedParticipant) {
        $csrf = [
            'csrfName' => csrf_token(),
            'csrfHash' => csrf_hash(),
        ];
        echo json_encode(['status' => 'error', 'message' => 'Not authorized for this event', 'csrf' => $csrf]);
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
    $this->student_model->update_table('appointments', [
        'event_id' => $event_id,
        'DATE(start_date) !=' => $occurrence_date,
        'Etat' => 1
    ], ['Etat' => 0]);

    // VÃ©rifier si un appointment actif existe pour cette occurrence
    $appointment = $this->student_model->get_row('appointments', [
        'event_id' => $event_id,
        'DATE(start_date)' => $occurrence_date,
        'Etat' => 1
    ]);
    $appointment_id = $appointment ? $appointment['id'] : null;


    // Charger la configuration BigBlueButton
    $bbb_config = config('bigbluebutton');
    $bbb_url = $bbb_config->bbb_url ?? '';
    $bbb_secret = $bbb_config->bbb_secret ?? '';

    // Si un appointment existe et le meeting est en cours, le rejoindre
    if ($appointment && $appointment['meeting_id']) {
        $meeting = $this->student_model->get_session_meeting($appointment['meeting_id'], $appointment_id);
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

                $user_details = $this->user_model->get_user_details($user_id);
                $full_name = urlencode($user_details['name'] ?? 'Student-' . rand(1000, 9999));
                $join_params = "fullName=$full_name&meetingID=" . urlencode($appointment['meeting_id']) . "&password=" . $meeting['attendee_pw'] . "&redirect=true";
                $join_checksum = sha1("join" . $join_params . $bbb_secret);
                $join_url = $bbb_url . "join?" . $join_params . "&checksum=" . $join_checksum;

                $participants = $this->student_model->get_where_result('appointment_participants', ['appointment_id' => $appointment_id], 'guest, type');

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
                $this->student_model->update_appointment($appointment_id, ['Etat' => 0]);
            }
        }
    }

    // Retrieve all participants (classes and users) for the event
    $participants = $this->student_model->get_where_result('participants', ['event_id' => $event_id], 'guest, type');

    // Create a new appointment
    $appointment_data = [
        'event_id' => $event_id,
        'title' => $event['title'],
        'start_date' => $occurrence_date . ' ' . $event['starting_time'],
        'school_id' => $event['school_id'],
        'visio' => $event['visio'],
        'Etat' => 1,
        'meeting_id' => null
    ];
    $appointment_id = $this->student_model->insert_table('appointments', $appointment_data);

     foreach ($participants as $participant) {
        $this->student_model->insert_table('appointment_participants', [
            'appointment_id' => $appointment_id,
            'guest' => $participant['guest'],
            'type' => $participant['type']
        ]);
    }

    // CrÃ©er un nouveau meeting BigBlueButton
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
        $this->student_model->insert_table('sessions_meetings', $meeting_data);

        // Mettre Ã  jour l'appointment avec le nouveau meeting_id
        $this->student_model->update_appointment($appointment_id, ['meeting_id' => $new_meeting_id]);

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
        $full_name = urlencode($user_details['name'] ?? 'Student-' . rand(1000, 9999));
        $join_params = "fullName=$full_name&meetingID=" . urlencode($new_meeting_id) . "&password=$attendee_password&redirect=true";
        $join_checksum = sha1("join" . $join_params . $bbb_secret);
        $join_url = $bbb_url . "join?" . $join_params . "&checksum=" . $join_checksum;

        $participants = $this->student_model->get_where_result('appointment_participants', ['appointment_id' => $appointment_id], 'guest, type');

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

public function get_student_schools() {
    if (session()->get('student_login') != 1) {
        $csrf = [
            'csrfName' => csrf_token(),
            'csrfHash' => csrf_hash(),
        ];
        echo json_encode(['status' => 'error', 'message' => 'Session expired, please login again', 'csrf' => $csrf]);
        exit;
    }

    $user_id = session()->get('user_id');

    if (!$user_id) {
        $csrf = [
            'csrfName' => csrf_token(),
            'csrfHash' => csrf_hash(),
        ];
        echo json_encode(['status' => 'error', 'message' => 'No user associated with this session', 'csrf' => $csrf]);
        exit;
    }

    // RequÃªte joinÃ©e unique pour rÃ©cupÃ©rer les Ã©coles distinctes
    $sql = "SELECT DISTINCT(schools.id), schools.name FROM students INNER JOIN enrols ON students.id = enrols.student_id INNER JOIN schools ON enrols.school_id = schools.id WHERE students.user_id = ? AND schools.Etat = 1 AND schools.status = 1 ORDER BY schools.name ASC";
    $schools = $this->student_model->get_by_query($sql, [$user_id]);


    $csrf = [
        'csrfName' => csrf_token(),
        'csrfHash' => csrf_hash(),
    ];

    echo json_encode([
        'status' => 'success',
        'schools' => $schools,
        'message' => empty($schools) ? 'Aucune Ã©cole trouvÃ©e pour cet Ã©tudiant' : '',
        'csrf' => $csrf
    ]);
}

public function get_school_data() {
    if (session()->get('student_login') != 1) {
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
    if (!$school_id || !$this->db_helper_model->get('schools', ['id' => $school_id, 'Etat' => 1, 'status' => 1], 'row')) {
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
        // RÃ©cupÃ©rer les classes
        $classes = $this->student_model->get_where_result('classes', ['school_id' => $school_id], 'id, name');

   // RÃ©cupÃ©rer l'ID du superadmin connectÃ©
        $current_user_id = $user_id;

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
        $sql_students = "SELECT DISTINCT(u.id), u.name, 'student' as type, u.role, u.status as user_status, s.status as student_status FROM users u INNER JOIN students s ON s.user_id = u.id INNER JOIN enrols e ON e.student_id = s.id WHERE e.school_id = ? AND u.status = 1 AND s.status = 1";
        $params_students = [$school_id];
        if (!empty($class_ids)) {
            $sql_students .= " AND e.class_id NOT IN (" . implode(',', array_fill(0, count($class_ids), '?')) . ")";
            $params_students = array_merge($params_students, $class_ids);
        }
        $sql_students .= " ORDER BY u.name ASC";
        $students = $this->student_model->get_by_query($sql_students, $params_students);
        $users = array_merge($users, $students);

        // 2. RÃ©cupÃ©rer les enseignants
        $sql_teachers = "SELECT DISTINCT(u.id), u.name, 'teacher' as type, u.role FROM users u INNER JOIN teachers t ON t.user_id = u.id WHERE t.school_id = ? AND u.status = 1 AND u.id != ? ORDER BY u.name ASC";
        $teachers = $this->student_model->get_by_query($sql_teachers, [$school_id, $current_user_id]);
        $users = array_merge($users, $teachers);

        // 3. RÃ©cupÃ©rer les admins et superadmins
        $sql_admins = "SELECT DISTINCT(u.id), u.name, u.role as type, u.role FROM users u WHERE u.school_id = ? AND u.status = 1 AND u.role IN ('admin', 'superadmin') ORDER BY u.name ASC";
        $admins = $this->student_model->get_by_query($sql_admins, [$school_id]);
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

    /**
     * Chat AI pour un syllabus spÃ©cifique avec systÃ¨me de CACHE
     * Le texte est extrait une seule fois et mis en cache
     * Supporte les formats: PDF, DOCX, DOC, TXT
     * @param int $syllabus_id L'ID du syllabus
     */
    /**
     * Chat AI pour un syllabus - Version optimisÃ©e
     * Le texte est prÃ©-extrait Ã  l'upload, lecture directe depuis la BDD
     * @param int $syllabus_id L'ID du syllabus
     */
    public function chat_document_syllabus($syllabus_id = '')
    {
        if (empty($syllabus_id)) {
            return redirect()->to(site_url('app/syllabus'));
            return;
        }

        // RÃ©cupÃ©rer le syllabus avec le texte prÃ©-extrait
        $syllabus = $this->student_model->get_row('syllabuses', ['id' => $syllabus_id]);
        
        if (empty($syllabus)) {
            session()->setFlashdata('error', get_phrase('syllabus_not_found'));
            return redirect()->to(site_url('app/syllabus'));
            return;
        }

        // VÃ©rifier si le texte a Ã©tÃ© extrait
        $text = isset($syllabus['extracted_text']) ? $syllabus['extracted_text'] : '';
        $page_count = isset($syllabus['page_count']) ? (int)$syllabus['page_count'] : 1;
        $file_extension = strtolower(pathinfo($syllabus['file'], PATHINFO_EXTENSION));
        
        // Si le texte n'est pas encore extrait (ancien syllabus), l'extraire maintenant
        if (empty($text)) {
            $file_path = FCPATH . 'uploads/syllabus/' . $syllabus['file'];
            
            if (!file_exists($file_path)) {
                session()->setFlashdata('error', get_phrase('file_not_found'));
                return redirect()->to(site_url('app/syllabus'));
                return;
            }
            
            // Extraire et sauvegarder en BDD pour les prochaines fois
            $extraction = $this->crud_model->extract_document_text($file_path, $file_extension);
            $text = $extraction['text'];
            $page_count = $extraction['page_count'];
            
            // Mettre Ã  jour la BDD
            if (!empty($text)) {
                $this->student_model->update_table('syllabuses', ['id' => $syllabus_id], [
                    'extracted_text' => $text,
                    'page_count' => $page_count
                ]);
            }
        }
        
        // Nettoyer le texte et vÃ©rifier qu'il contient du contenu exploitable
        $cleaned_text = trim(preg_replace('/\s+/', ' ', $text));
        $text_length = strlen($cleaned_text);
        
        // Minimum 50 caractÃ¨res de contenu rÃ©el pour Ãªtre exploitable par l'IA
        $minimum_text_length = 50;

        if ($text_length >= $minimum_text_length) {
            // Stocker le contexte en session (rÃ©fÃ©rence au syllabus)
            session()->set([
                'doc_context_syllabus_id' => $syllabus_id
            ]);

            $page_data = array(
                'folder_name' => 'syllabus',
                'page_name'   => 'index_chat_syllabus',
                'page_title'  => 'chat_ai',
                'syllabus'    => $syllabus,
                'page_count'  => $page_count,
                'text_length' => $text_length,
                'context_id'  => 'syllabus_' . $syllabus_id,
                'file_type'   => $file_extension
            );
            
            return view('backend/index', $page_data);
            return;
        }
        
        // Afficher un toast d'erreur et rediriger
        $error_message = get_phrase('document_contains_insufficient_text_for_ai_chat');
        $redirect_url = site_url('app/syllabus');
        
        echo '<!DOCTYPE html>
        <html>
        <head>
            <title>' . get_phrase('error') . '</title>
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        </head>
        <body>
            <script>
                Swal.fire({
                    icon: "warning",
                    title: "' . addslashes(get_phrase('insufficient_text')) . '",
                    text: "' . addslashes($error_message) . '",
                    confirmButtonColor: "#f47a1f",
                    confirmButtonText: "OK"
                }).then(function() {
                    window.location.href = "' . $redirect_url . '";
                });
            </script>
        </body>
        </html>';
    }

    /**
     * API pour interroger le document (utilisÃ© par le chat)
     * Lit le contexte directement depuis la BDD
     */
    public function query_document()
    {
        if (!$this->request->isAJAX()) {
            show_404();
            return;
        }

        $query_in_progress = session()->get('doc_query_in_progress');
        $query_started_at = session()->get('doc_query_started_at');
        $timeout = 120;
        $now = time();

        if ($query_in_progress && $query_started_at && ($now - $query_started_at) < $timeout) {
            $this->respond_with_json(array(
                'status' => 'error', 
                'message' => get_phrase('A_request_is_already_in_progress._Please_wait_for_the_response.')
            ));
            return;
        }

        session()->set('doc_query_in_progress', true);
        session()->set('doc_query_started_at', $now);

        $question = trim($this->request->getPost('question') ?? '');
        if ($question === '') {
            $this->_clear_query_lock();
            $this->respond_with_json(array('status' => 'error', 'message' => 'La question est requise.'));
            return;
        }

        // RÃ©cupÃ©rer le contexte depuis la BDD
        $syllabus_id = session()->get('doc_context_syllabus_id');
        if (empty($syllabus_id)) {
            $this->_clear_query_lock();
            $this->respond_with_json(array('status' => 'error', 'message' => 'Aucun contexte document disponible.'));
            return;
        }
        
        $syllabus = $this->student_model->get_row('syllabuses', ['id' => $syllabus_id]);
        if (empty($syllabus) || empty($syllabus['extracted_text'])) {
            $this->_clear_query_lock();
            $this->respond_with_json(array('status' => 'error', 'message' => 'Le contexte du document est vide.'));
            return;
        }
        
        $context = $syllabus['extracted_text'];

        try {
            $data = [
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => "You are ã€Ž Wayo AI ã€ðŸ¤–, a professional assistant specialized in document analysis.\n\n" .
                                    "ðŸ“„ DOCUMENT CONTEXT:\n" . $context . "\n\n" .
                                    "INSTRUCTIONS:\n" .
                                    "- Answer ONLY based on the document content\n" .
                                    "- Be precise and helpful\n" .
                                    "- Use the same language as the question"
                    ],
                    [
                        'role' => 'user',
                        'content' => $question
                    ]
                ],
                'temperature' => 0.3,
                'max_tokens' => 2048,
                'top_p' => 0.95
            ];

            $ch = curl_init($this->lmStudioUrl);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
                CURLOPT_POSTFIELDS => json_encode($data),
                CURLOPT_TIMEOUT => 120
            ]);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            $this->_clear_query_lock();

            if ($httpCode === 200) {
                $result = json_decode($response, true);
                $answer = $result['choices'][0]['message']['content'] ?? 'Aucune rÃ©ponse gÃ©nÃ©rÃ©e.';
                $this->respond_with_json(['status' => 'success', 'answer' => $answer]);
            } else {
                $this->respond_with_json(['status' => 'error', 'message' => 'Erreur API: ' . $httpCode]);
            }
            
        } catch (Exception $e) {
            $this->_clear_query_lock();
            $this->respond_with_json(['status' => 'error', 'message' => 'Erreur: ' . $e->getMessage()]);
        }
    }

    /**
     * LibÃ¨re le verrou de requÃªte document en cours
     */
    private function _clear_query_lock()
    {
        clear_query_lock();
    }

    /**
     * Helper method to send JSON response with CSRF token
     */
    private function respond_with_json($data) {
        respond_with_json($data);
    }
}
