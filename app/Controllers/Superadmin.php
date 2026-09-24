<?php

namespace App\Controllers;

use Mpdf\Mpdf;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;


/*
 *  @author   : Creativeitem
 *  date      : November, 2019
 *  Ekattor School Management System With Addons
 *  http://codecanyon.net/user/Creativeitem
 *  http://support.creativeitem.com
 */

class Superadmin extends BaseController
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
        'Superadmin_model' => 'superadmin_model',
    ];

    /**
     * Services to auto-load
     */
    protected $services = [
        'subscriptionService' => 'subscriptionService',
        'billingEntityService' => 'billingEntityService',
        'fxRatesService' => 'fxRatesService',
    ];

    /**
     * @var \App\Libraries\SubscriptionService
     */
    protected $subscriptionService;

    /**
     * @var \App\Libraries\BillingEntityService
     */
    protected $billingEntityService;

    /**
     * @var \App\Libraries\FxRatesService
     */
    protected $fxRatesService;

    /**
     * DB Helper Model (loaded conditionally)
     */
    protected $db_helper_model;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        // Load conditional models
        $this->loadModel('Db_helper_model', 'db_helper_model');

        /*SET DEFAULT TIMEZONE*/
        timezone();

        // Auth check
        if (session()->get('superadmin_login') != 1) {
            return redirect()->to('/login')->send();
        }
    }

    /**
     * Update URL language prefix (only for frontend URLs)
     */
    private function _update_url_language_prefix($url, $new_lang_code)
    {
        return update_url_language_prefix($url, $new_lang_code);
    }

    //dashboard
    public function index()
    {
        return redirect()->to(site_url('app/dashboard'));
    }

    //school
    public function school()
    {
        $page_data['page_title'] = 'School';
        $page_data['folder_name'] = 'school';
        return view('backend/index', $page_data);
    }

    public function dashboard()
    {
        $page_data['page_title'] = 'Dashboard';
        $page_data['folder_name'] = 'dashboard';
        return view('backend/index', $page_data);
    }

  //START CLASS secion
  public function manage_class($param1 = '', $param2 = '', $param3 = '')
    {
        if ($param1 == 'create') {
            $modelResponse = $this->crud_model->class_create();
            $csrf = array(
                'csrfName' => csrf_token(),
                'csrfHash' => csrf_hash()
            );
            $response = array(
                'status' => $modelResponse['status'],
                'notification' => $modelResponse['notification'],
                'csrf' => $csrf
            );
            echo json_encode($response);
        }

        if ($param1 == 'delete') {
            $response = $this->crud_model->class_delete($param2);
            $csrf = array(
                'csrfName' => csrf_token(),
                'csrfHash' => csrf_hash(),
            );
            echo json_encode(array('status' => $response['status'], 'notification' => $response['notification'], 'csrf' => $csrf));
        }

        if ($param1 == 'update') {
            $response = $this->crud_model->class_update($param2);
            $csrf = array(
                'csrfName' => csrf_token(),
                'csrfHash' => csrf_hash(),
            );
            echo json_encode(array('status' => $response['status'], 'notification' => $response['notification'], 'csrf' => $csrf));
        }

        if ($param1 == 'section') {
            $response = $this->crud_model->section_update($param2);
            $csrf = array(
                'csrfName' => csrf_token(),
                'csrfHash' => csrf_hash(),
            );
            echo json_encode(array('status' => $response['status'], 'notification' => $response['notification'], 'csrf' => $csrf));
        }

        if ($param1 == 'list') {
            return view('backend/superadmin/class/list');
        }

        if (empty($param1)) {
            $page_data['folder_name'] = 'class';
            $page_data['page_title'] = 'class';
            return view('backend/index', $page_data);
        }
    }
  //END CLASS section

  //	SECTION STARTED
  public function section($action = "", $id = "")
  {

    // PROVIDE A LIST OF SECTION ACCORDING TO CLASS ID
    if ($action == 'list') {
      $page_data['class_id'] = $id;
      return view('backend/superadmin/section/list', $page_data);
    }
  }
  //	SECTION ENDED

  //START CLASS_ROOM section
  public function class_room($param1 = '', $param2 = '', $param3 = '')
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
          return view('backend/superadmin/class_room/list', ['classe_id' => '', 'room_id' => '']);
        }
  
      if (empty($param1)) {
        $page_data['folder_name'] = 'class_room';
        $page_data['page_title'] = 'DÃ©marrer RÃ©union';
        return view('backend/index', $page_data);
      }
    }
  //END CLASS_ROOM section

  //START SUBJECT section
  public function subject($param1 = '', $param2 = '')
  {

    if ($param1 == 'create') {
      $response = $this->crud_model->subject_create();
      echo $response;
    }

    if ($param1 == 'update') {
      $response = $this->crud_model->subject_update($param2);
      echo $response;
    }

    if ($param1 == 'delete') {
      $response = $this->crud_model->subject_delete($param2);
      echo $response;
    }

    if ($param1 == 'list') {
      $page_data['class_id'] = $param2;
      return view('backend/superadmin/subject/list', $page_data);
    }

    if (empty($param1)) {
      $page_data['folder_name'] = 'subject';
      $page_data['page_title'] = 'subject';
      return view('backend/index', $page_data);
    }
  }

  public function class_wise_subject($class_id)
  {

    // PROVIDE A LIST OF SUBJECT ACCORDING TO CLASS ID
    $page_data['class_id'] = $class_id;
    return view('backend/superadmin/subject/dropdown', $page_data);
  }
  //END SUBJECT section





  //START SYLLABUS section
  public function syllabus($param1 = '', $param2 = '', $param3 = '')
  {

    if ($param1 == 'create') {
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
    
    echo json_encode($response);
  }
    if ($param1 == 'delete') {
      $response = $this->crud_model->syllabus_delete($param2);
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
      $page_data['class_id'] = $param2;
      $page_data['section_id'] = $param3;
      return view('backend/superadmin/syllabus/list', $page_data);
    }

    if ($param1 == 'download') {
      return $this->syllabus_download($param2);
    }

    if (empty($param1)) {
      $page_data['folder_name'] = 'syllabus';
      $page_data['page_title'] = 'syllabus';
      return view('backend/index', $page_data);
    }
  }

  public function syllabus_download($syllabus_id)
  {
    $allowed_user_types = ['superadmin'];
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

  // START ADMIN SECTION
  public function admin($param1 = "", $param2 = "", $param3 = "")
  {
    if ($param1 == 'create') {
      $response = $this->user_model->create_admin();
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
      $response = $this->user_model->update_admin($param2);
      // echo $response;
      // PrÃ©parer la rÃ©ponse avec un nouveau jeton CSRF
      $csrf = array(
        'csrfName' => csrf_token(),
        'csrfHash' => csrf_hash(),
    );

    // Renvoyer la rÃ©ponse avec un nouveau jeton CSRF
    echo json_encode(array('status' => $response, 'csrf' => $csrf));
    }

    if ($param1 == 'delete') {
      $response = $this->user_model->delete_admin($param2);
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
      return view('backend/superadmin/admin/list');
    }

    if (empty($param1)) {
      $page_data['folder_name'] = 'admin';
      $page_data['page_title'] = 'admins';
      return view('backend/index', $page_data);
    }
  }
  // END ADMIN SECTION



  // START ADMIN SECTION
  public function school_crud($param1 = "", $param2 = "", $param3 = "")
  {
    //  print_r($param1);
    if ($param1 == 'create') {
      $response = $this->user_model->create_school();
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
      // die($param1);
      $response = $this->user_model->update_school($param2);
      // echo $response;
            // PrÃ©parer la rÃ©ponse avec un nouveau jeton CSRF
            $csrf = array(
              'csrfName' => csrf_token(),
              'csrfHash' => csrf_hash(),
          );
      
          // Renvoyer la rÃ©ponse avec un nouveau jeton CSRF
          echo json_encode(array('status' => $response, 'csrf' => $csrf));
    }

    if ($param1 == 'delete') {
      $response = $this->user_model->delete_school($param2);
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
      
      return view('backend/superadmin/school/list');
    }

    if (empty($param1)) {
      $page_data['folder_name'] = 'school';
      $page_data['page_title'] = 'schools';
      return view('backend/index', $page_data);
    }
  }
  // END ADMIN SECTION

  //START TEACHER section
  public function teacher($param1 = '', $param2 = '', $param3 = '')
  {


    if ($param1 == 'create') {
      $modelResponse = $this->user_model->create_teacher();
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
    
      echo json_encode($response);
    }

    if ($param1 == 'update') {
      $response = $this->user_model->update_teacher($param2);
          // PrÃ©parer la rÃ©ponse avec un nouveau jeton CSRF
            $csrf = array(
              'csrfName' => csrf_token(),
              'csrfHash' => csrf_hash(),
          );
      
          // Renvoyer la rÃ©ponse avec un nouveau jeton CSRF
          echo json_encode(array('status' => $response, 'csrf' => $csrf));
    }

    if ($param1 == 'delete') {
      $teacher_id = $this->superadmin_model->get_teacher_id_by_user_id($param2);
      $response = $this->user_model->delete_teacher($param2, $teacher_id);
            // PrÃ©parer la rÃ©ponse avec un nouveau jeton CSRF
            $csrf = array(
              'csrfName' => csrf_token(),
              'csrfHash' => csrf_hash(),
          );
      
          // Renvoyer la rÃ©ponse avec un nouveau jeton CSRF
          echo json_encode(array('status' => $response, 'csrf' => $csrf));
    }

    if ($param1 == 'list') {
      return view('backend/superadmin/teacher/list');
    }

    if (empty($param1)) {
      $page_data['folder_name'] = 'teacher';
      $page_data['page_title'] = 'techers';
      return view('backend/index', $page_data);
    }
  }
  //END TEACHER section



  //START TEACHER PERMISSION section
  public function permission($param1 = '', $param2 = '', $param3 = '')
  {
    if (session()->get('superadmin_login') != 1) {
      return redirect()->to('/login')->send();
    }

    if ($param1 == 'filter') {
      $class_id = (int)$param2;
      $section_id = (int)$param3;

      if ($class_id < 0 || $section_id < 0) {
        return redirect()->to(site_url('permission'))->with('error', 'Invalid parameters');
      }

      $page_data['class_id'] = $class_id;
      $page_data['section_id'] = $section_id;
      return view('backend/superadmin/permission/list', $page_data);
    }

    if ($param1 == 'modify_permission') {
      $page_data['class_id'] = htmlspecialchars((string) ($this->request->getPost('class_id') ?? ''));
      $page_data['section_id'] = htmlspecialchars((string) ($this->request->getPost('section_id') ?? ''));
      $this->user_model->teacher_permission();
      $response_html = view('backend/superadmin/permission/list', $page_data, ['cache' => 0]);

      $csrf = array(
          'csrfName' => csrf_token(),
          'csrfHash' => csrf_hash(),
      );

      echo json_encode(array('html' => $response_html, 'csrfName' => $csrf['csrfName'], 'csrfHash' => $csrf['csrfHash']));
    }

    if (empty($param1)) {
      $page_data['folder_name'] = 'permission';
      $page_data['page_title'] = 'teacher_permissions';
      return view('backend/index', $page_data);
    }
  }
  //END TEACHER PERMISSION section


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
          return view('backend/superadmin/bigbleubutton/list');
        }
  
      if (empty($param1)) {
        $page_data['folder_name'] = 'bigbleubutton';
        $page_data['page_title'] = 'DÃ©marrer RÃ©union';
        return view('backend/index', $page_data);
      }
    }


      public function get_appointments() 
      {
        $appointments = $this->room_model->get_all_appointments();
        echo json_encode($appointments);
      }
      
    public function get_meeting_status() {
        if (session()->get('superadmin_login') != 1) {
            echo json_encode(['status' => 'error', 'message' => 'Session expired, please login again']);
            exit;
        }

        $meeting_id = $this->request->getPost('meeting_id');
        if (empty($meeting_id)) {
            echo json_encode(['status' => 'error', 'message' => 'Meeting ID is required']);
            exit;
        }

        $bbb_config = config('bigbluebutton');
        $bbb_url = $bbb_config->bbb_url ?? '';
        $bbb_secret = $bbb_config->bbb_secret ?? '';

        $params = "meetingID=" . urlencode($meeting_id);
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
            log_message('error', 'BigBlueButton cURL error for meeting ID ' . $meeting_id . ': ' . $curl_error);
            echo json_encode(['status' => 'error', 'message' => 'Failed to fetch meeting status']);
            exit;
        }

        $is_running = strpos($response, "<returncode>SUCCESS</returncode>") !== false && strpos($response, "<running>true</running>") !== false;
        $participant_count = 0;
        if ($is_running) {
            // Parse XML response to get participant count
            $xml = simplexml_load_string($response);
            $participant_count = (int)($xml->participantCount ?? 0);
        }

        echo json_encode([
            'status' => 'success',
            'is_running' => $is_running,
            'participant_count' => $participant_count,
            'meeting_id' => $meeting_id,
            'csrf' => [
                'csrfName' => csrf_token(),
                'csrfHash' => csrf_hash(),
            ]
        ]);
    }
      public function get_event_classes() {
        if (session()->get('superadmin_login') != 1) {
            echo json_encode(['status' => 'error', 'message' => 'Session expired, please login again']);
            exit;
        }

        $classes = $this->room_model->get_event_classes();
        $csrf = [
            'csrfName' => csrf_token(),
            'csrfHash' => csrf_hash(),
        ];
        echo json_encode([
            'status' => 'success',
            'classes' => $classes,
            'csrf' => $csrf
        ]);
      } 
      
      public function get_sections() 
      {
        $classe_id = $this->request->getPost('classe_id');
    
        if (!empty($classe_id)) {
            $sections = $this->superadmin_model->get_sections_by_class($classe_id);
        } else {
            $sections = [];
        }
    
        echo json_encode($sections);
      }
      public function delete_room()
      {
          $data = json_decode(file_get_contents("php://input"), true);
          $roomID = $data['selectedRoomID'];
          // $this->db->delete("rooms", ["id" => $roomID]);
          $this->room_model->update_room_by_id($roomID);
          echo json_encode(["status" => "success", "message" => "Room supprimÃ©e avec succÃ¨s !"]);
      }


      public function recording($param1 = '', $param2 = '', $param3 = '') {
    // Check authentication
    if (session()->get('superadmin_login') != 1) {
        return redirect()->to(site_url('login'));
    }

    // RÃ©cupÃ©rer l'ID de l'utilisateur connectÃ©
    $user_id = session()->get('user_id'); // Assurez-vous que 'user_id' est dÃ©fini dans la session lors de la connexion

    // RÃ©cupÃ©rer le school_id depuis la table users
    $user = $this->superadmin_model->get_user_by_id($user_id);
    if (!$user || empty($user['school_id'])) {
        // GÃ©rer le cas oÃ¹ l'utilisateur n'a pas de school_id ou n'existe pas
        log_message('error', 'recording - No school_id found for user_id: ' . $user_id);
        show_error('No school associated with this user.', 403);
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
        // Filtrer les rÃ©unions par school_id
        $meetings = $this->superadmin_model->get_where_result('sessions_meetings', ['school_id' => $school_id]);

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
                    // Replace the domain in the recording URL
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

                    $existing = $this->superadmin_model->get_recordings_by_recording_id($recording_id);
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
                        $this->superadmin_model->insert_table('recordings', $recording_data);
                    } else {
                        $this->superadmin_model->update_table('recordings', ['recording_id' => $recording_id], [
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
    $sql = "SELECT r.*, c.name as class_name 
            FROM recordings r 
            LEFT JOIN classes c ON r.class_id = c.id 
            WHERE r.school_id = ?";
    $params = [$school_id];

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
    $recordings = $this->superadmin_model->get_by_query($sql, $params);

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
    if (session()->get('superadmin_login') != 1) {
        $response = [
            'status' => 'error',
            'message' => get_phrase('unauthorized_access'),
            'csrf_token' => csrf_hash()
        ];
        return $this->response->setJSON($response);
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
    }

    // VÃ©rifier si l'enregistrement existe dans la table recordings
    $existing = $this->superadmin_model->get_row('recordings', ['recording_id' => $recording_id]);

    if (!$existing) {
        $response = [
            'status' => 'error',
            'message' => get_phrase('recording_not_found'),
            'csrf_token' => csrf_hash()
        ];
        log_message('error', 'delete_recording - Recording not found: recording_id=' . $recording_id);
        return $this->response->setJSON($response);
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
    }

    // Supprimer l'enregistrement de la table recordings
    $delete_result = $this->superadmin_model->delete_table('recordings', ['recording_id' => $recording_id]);
    $db_error = $delete_result['error'];


    if ($db_error['code'] == 0 && $delete_result['affected_rows'] > 0) {
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

    
      

      public function filter_recordings()
      {
          $bbb_config = config('bigbluebutton');
          $bbbUrl = rtrim($bbb_config->bbb_url ?? '', '/') . '/';
          $bbbSecret = $bbb_config->bbb_secret ?? '';
      
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
      
          $appointments = $this->superadmin_model->get_by_query($sql, $params);
      
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
                      $name = $this->superadmin_model->get_section_name($id);
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
          $bbb_config = config('bigbluebutton');
          $bbbUrl = $bbb_config->bbb_url ?? '';
          $bbbSecret = $bbb_config->bbb_secret ?? '';
          $meeting_name = $this->request->getGet('meeting_name');
          $date_range = $this->request->getGet('date_range');
          $schoolID = school_id();
          // $this->db->select('*')->from('appointments');

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

          $appointments = $this->superadmin_model->get_by_query($sql, $params);
          

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
                      $name = $this->superadmin_model->get_section_name($id);
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
    
  //START ACCOUNTANT section
  public function accountant($param1 = '', $param2 = '')
  {

    if ($param1 == 'create') {
      $modelResponse = $this->user_model->accountant_create();
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
    
      echo json_encode($response);
    }

    if ($param1 == 'update') {
      $response = $this->user_model->accountant_update($param2);
                  // PrÃ©parer la rÃ©ponse avec un nouveau jeton CSRF
            $csrf = array(
              'csrfName' => csrf_token(),
              'csrfHash' => csrf_hash(),
          );
      
          // Renvoyer la rÃ©ponse avec un nouveau jeton CSRF
          echo json_encode(array('status' => $response, 'csrf' => $csrf));
    }

    if ($param1 == 'delete') {
      $response = $this->user_model->accountant_delete($param2);
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
      return view('backend/superadmin/accountant/list');
    }

    if (empty($param1)) {
      $page_data['folder_name'] = 'accountant';
      $page_data['page_title'] = 'accountant';
      return view('backend/index', $page_data);
    }
  }
  //END ACCOUNTANT section


  //START LIBRARIAN section
  public function librarian($param1 = '', $param2 = '')
  {

    if ($param1 == 'create') {
      $modelResponse = $this->user_model->librarian_create();
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
    
      echo json_encode($response);
    }

    if ($param1 == 'update') {
      $response = $this->user_model->librarian_update($param2);
                  // PrÃ©parer la rÃ©ponse avec un nouveau jeton CSRF
            $csrf = array(
              'csrfName' => csrf_token(),
              'csrfHash' => csrf_hash(),
          );
      
          // Renvoyer la rÃ©ponse avec un nouveau jeton CSRF
          echo json_encode(array('status' => $response, 'csrf' => $csrf));
    }

    if ($param1 == 'delete') {
      $response = $this->user_model->librarian_delete($param2);
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
      return view('backend/superadmin/librarian/list');
    }

    if (empty($param1)) {
      $page_data['folder_name'] = 'librarian';
      $page_data['page_title'] = 'librarian';
      return view('backend/index', $page_data);
    }
  }
  //END LIBRARIAN section

  //START CLASS ROUTINE section
  public function routine($param1 = '', $param2 = '', $param3 = '', $param4 = '')
  {

    if ($param1 == 'create') {
      $modelResponse = $this->crud_model->routine_create();
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
                
                echo json_encode($response);
    }

    if ($param1 == 'update') {
      $response = $this->crud_model->routine_update($param2);
                  // PrÃ©parer la rÃ©ponse avec un nouveau jeton CSRF
            $csrf = array(
              'csrfName' => csrf_token(),
              'csrfHash' => csrf_hash(),
          );
      
          // Renvoyer la rÃ©ponse avec un nouveau jeton CSRF
          echo json_encode(array('status' => $response, 'csrf' => $csrf));
    }

    if ($param1 == 'delete') {
      $response = $this->crud_model->routine_delete($param2);
                  // PrÃ©parer la rÃ©ponse avec un nouveau jeton CSRF
            $csrf = array(
              'csrfName' => csrf_token(),
              'csrfHash' => csrf_hash(),
          );
      
          // Renvoyer la rÃ©ponse avec un nouveau jeton CSRF
          echo json_encode(array('status' => $response, 'csrf' => $csrf));
    }

    if ($param1 == 'filter') {
      $page_data['class_id'] = $param2;
      $page_data['section_id'] = $param3;
      return view('backend/superadmin/routine/list', $page_data);
    }

    if (empty($param1)) {
      $page_data['folder_name'] = 'routine';
      $page_data['page_title'] = 'calendar';
      return view('backend/index', $page_data);
    }
  }
  //END CLASS ROUTINE section


  //START DAILY ATTENDANCE section
  public function attendance($param1 = '', $param2 = '', $param3 = '')
  {

    if ($param1 == 'take_attendance') {
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
      
      echo json_encode($response);
    }

    if ($param1 == 'filter') {
      $month = (string) $this->request->getPost('month');
      $year = (string) $this->request->getPost('year');
      $page_data['attendance_date'] = strtotime('01 ' . $month . ' ' . $year);
      $page_data['class_id'] = htmlspecialchars((string) $this->request->getPost('class_id'));
      $page_data['section_id'] = htmlspecialchars((string) $this->request->getPost('section_id'));
      $page_data['month'] = htmlspecialchars($month);
      $page_data['year'] = htmlspecialchars($year);
      $response_html = view('backend/superadmin/attendance/list', $page_data);

      return $this->response->setJSON([
          'status' => $response_html,
          'csrfName' => csrf_token(),
          'csrfHash' => csrf_hash(),
      ]);
    }

    if ($param1 == 'student') {
      $page_data['attendance_date'] = strtotime($this->request->getPost('date'));
      $page_data['class_id'] = htmlspecialchars($this->request->getPost('class_id'));
      $page_data['section_id'] = htmlspecialchars($this->request->getPost('section_id'));

      // Charger la vue mise Ã  jour
      $response_html = view('backend/superadmin/attendance/student', $page_data, ['cache' => 0]);
         // PrÃ©parer le nouveau jeton CSRF
         $csrf = array(
          'csrfName' => csrf_token(),
          'csrfHash' => csrf_hash(),
      );

      // Renvoyer la rÃ©ponse JSON avec le HTML mis Ã  jour et le nouveau jeton CSRF
      echo json_encode(array('status' => $response_html, 'csrfName' => $csrf['csrfName'], 'csrfHash' => $csrf['csrfHash']));
    }

    if (empty($param1)) {
      $page_data['folder_name'] = 'attendance';
      $page_data['page_title'] = 'attendance';
      return view('backend/index', $page_data);
    }
  }
  //END DAILY ATTENDANCE section


  //START EVENT CALENDAR section
  public function event_calendar($param1 = '', $param2 = '')
  {

    if ($param1 == 'create') {
        $modelResponse = $this->crud_model->event_calendar_create();
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
      
      echo json_encode($response);
    }

    if ($param1 == 'update') {
      $response = $this->crud_model->event_calendar_update($param2);
                  // PrÃ©parer la rÃ©ponse avec un nouveau jeton CSRF
            $csrf = array(
              'csrfName' => csrf_token(),
              'csrfHash' => csrf_hash(),
          );
      
          // Renvoyer la rÃ©ponse avec un nouveau jeton CSRF
          echo json_encode(array('status' => $response, 'csrf' => $csrf));
    }

    if ($param1 == 'delete') {
      $response = $this->crud_model->event_calendar_delete($param2);
                  // PrÃ©parer la rÃ©ponse avec un nouveau jeton CSRF
            $csrf = array(
              'csrfName' => csrf_token(),
              'csrfHash' => csrf_hash(),
          );
      
          // Renvoyer la rÃ©ponse avec un nouveau jeton CSRF
          echo json_encode(array('status' => $response, 'csrf' => $csrf));
    }

    if ($param1 == 'all_events') {
      echo $this->crud_model->all_events();
    }

    if ($param1 == 'list') {
      return view('backend/superadmin/event_calendar/list');
    }

    if (empty($param1)) {
      $page_data['folder_name'] = 'event_calendar';
      $page_data['page_title'] = 'event_calendar';
      return view('backend/index', $page_data);
    }
  }
  //END EVENT CALENDAR section



  //START STUDENT ADN ADMISSION section
  public function student($param1 = '', $param2 = '', $param3 = '', $param4 = '', $param5 = '')
  {
    session()->destroy();
    $page_data['class_id'] = '';
    $page_data['section_id'] = '';

    if ($param1 == 'create') {
      $page_data['student_create_classes'] = $this->getStudentCreateClassesForSchool();
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
        //$this->session->set_flashdata('flash_message', get_phrase('student_added_successfully'));
        // Ajout du token CSRF Ã  la rÃ©ponse
        $response = [
          'status' => $status,
          'message' => $status ? get_phrase('student_added_successfully') : session()->getFlashdata('error'),
          'redirect' => site_url('superadmin/student'),
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
        $page_data['section_id'] = html_escape($this->request->getPost('section_id'));
        $page_data['working_page'] = 'filter';
        $page_data['folder_name'] = 'student';
        $page_data['page_title'] = 'student_list';
        $page_data = $this->prepareStudentListDataForView($page_data);

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
    $page_data['student_id'] = $param2;
    $page_data['working_page'] = 'edit';
    $page_data['folder_name'] = 'student';
    $page_data['page_title'] = 'update_student_information';
    return view('backend/index', $page_data);
  }


    if ($param1 == 'status') {
      $this->superadmin_model->update_table('users', ['id' => $param3], ['status' => $param4]);
      $response = array(
        'status' => true,
        'notification' => get_phrase('status_has_been_updated')
      );
      
        // PrÃ©parer la rÃ©ponse avec un nouveau jeton CSRF
        $csrf = array(
                  'csrfName' => csrf_token(),
                  'csrfHash' => csrf_hash(),
                );
              
      // Renvoyer la rÃ©ponse avec un nouveau jeton CSRF
      echo json_encode(array('status' => json_encode($response), 'csrf' => $csrf));
    }

    //updated to database
    if ($param1 == 'updated') {
      $response = $this->user_model->student_update($param2, $param3);
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
      $response = $this->user_model->delete_student($param2, $param3);
      // echo $response;
       // PrÃ©parer la rÃ©ponse avec un nouveau jeton CSRF
      $csrf = array(
        'csrfName' => csrf_token(),
        'csrfHash' => csrf_hash(),
    );

    // Renvoyer la rÃ©ponse avec un nouveau jeton CSRF
     echo json_encode(array('status' => $response, 'csrf' => $csrf));
    }

    if ($param1 == 'filter') {
            $page_data['class_id'] = ($param2 == '' || $param2 == 'all') ? 'all' : $param2;
            $page_data['section_id'] = ($param3 == '' || $param3 == 'all') ? 'all' : $param3;
            $page_data = $this->prepareStudentListDataForView($page_data);
            $html_content = view('backend/superadmin/student/list', $page_data, ['cache' => 0]);
            $csrf = array(
                'csrfName' => csrf_token(),
                'csrfHash' => csrf_hash(),
            );
            echo json_encode(array('html' => $html_content, 'csrf' => $csrf));
        }

        if (empty($param1)) {
            $page_data['class_id'] = 'all';
            $page_data['section_id'] = 'all';
            $page_data['working_page'] = 'filter';
            $page_data['folder_name'] = 'student';
            $page_data['page_title'] = 'student_list';
            $page_data = $this->prepareStudentListDataForView($page_data);
            return view('backend/index', $page_data);
        }
  }
  //END STUDENT ADN ADMISSION section

  private function prepareStudentListDataForView(array $page_data): array
  {
    $school_id = school_id();
    $class_id = $page_data['class_id'] ?? 'all';
    $school_name = (string) (db()->table('schools', ['id' => $school_id])->row('name') ?? '');

    $classes = db()->table('classes', ['school_id' => $school_id])->getResultArray();
    $where = ['school_id' => $school_id, 'session' => active_session()];
    if (!empty($class_id) && $class_id !== 'all') {
      $where['class_id'] = $class_id;
    }

    $enrols = $this->db->select('DISTINCT(student_id), school_id, session')->get_where('enrols', $where)->getResultArray();
    $rows = [];
    $active_count = 0;

    foreach ($enrols as $enrol) {
      $student = db()->table('students', ['id' => $enrol['student_id']])->getRowArray();
      if (empty($student)) {
        continue;
      }
      $user = db()->table('users', ['id' => $student['user_id']])->getRowArray();
      if (empty($user)) {
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

  private function getStudentCreateClassesForSchool(): array
  {
    return db()->table('classes', ['school_id' => school_id()])->getResultArray();
  }

	public function get_csrf_token()
{
    $csrf = array(
        'csrf_name' => csrf_token(),
        'csrf_hash' => csrf_hash(),
    );
    echo json_encode($csrf);
}

public function get_sections_by_class()
{
    if (session()->get('superadmin_login') != 1) {
        echo json_encode(['error' => 'Unauthorized']);
        exit;
    }

    $class_id = $this->request->getPost('class_id');
    $school_id = school_id();

    if (empty($class_id)) {
        echo json_encode(['sections' => [], 'message' => 'No class ID provided']);
        exit;
    }

    $sql = "SELECT sections.id, sections.name 
            FROM sections 
            LEFT JOIN classes ON sections.class_id = classes.id 
            WHERE sections.class_id = ? AND classes.school_id = ?";
    $sections = $this->superadmin_model->get_by_query($sql, [$class_id, $school_id]);

    echo json_encode([
        'sections' => $sections,
        'message' => empty($sections) ? 'No sections found for this class' : 'Sections retrieved successfully'
    ]);
}



  //START EXAM section
  public function exam($param1 = '', $param2 = '')
{
    if ($param1 == 'create') {

        $response = $this->crud_model->exam_create();
        $response_data = json_decode($response, true);
        
        // RÃ©cupÃ©rer la classe sÃ©lectionnÃ©e (si disponible dans les donnÃ©es POST)
        $class_id = esc($this->request->getPost('class_id')) ?? '';
        
        // Ajouter un nouveau jeton CSRF
        $csrf = array(
            'csrfName' => csrf_token(),
            'csrfHash' => csrf_hash(),
        );
        
        // Construire la rÃ©ponse
        $output = array(
            'status' => $response_data['status'] ?? ($response ? true : false),
            'message' => $response_data['notification'] ?? ($response ? 'Exam created successfully' : 'Failed to create exam'),
            'class_id' => $class_id, // Inclure l'ID de la classe pour le frontend
            'csrf' => $csrf
        );
        
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($output));

    }

    if ($param1 == 'update') {
        $response = $this->crud_model->exam_update($param2);
        // VÃ©rifier si la rÃ©ponse est dÃ©jÃ  encodÃ©e en JSON et la dÃ©coder
        if (is_string($response) && (json_decode($response) !== null)) {
            $response = json_decode($response, true); // Convertir en tableau
        }
        
        $exam = $this->superadmin_model->get_exam_by_id($param2);
        $class_id = $exam['class_id'] ?? ''; // RÃ©cupÃ©rer l'ID de la classe de l'examen
        
        if ($exam) {
            $exam['formatted_date'] = date('D, d-M-Y H:i', $exam['starting_date']);
            $class = $this->superadmin_model->get_class_by_id($exam['class_id']);
            $section = $this->superadmin_model->get_section_by_id($exam['section_id']);
            $exam['class_name'] = $class ? $class['name'] : 'No Class';
            $exam['section_name'] = $section ? $section['name'] : 'No Section';
            $output = array(
                'status' => isset($response['status']) ? $response['status'] : $response,
                'exam' => $exam,
                'class_id' => $class_id, // Inclure l'ID de la classe
                'message' => $response['notification'] ?? ($response ? 'Exam updated successfully' : 'Failed to update exam'),
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
        if (!empty($page_data['filter_class_id'])) {
            $examBuilder->where('exams.class_id', $page_data['filter_class_id']);
        }
        if (!empty($page_data['filter_date_range']) && $date_from && $date_to) {
            $examBuilder->where('exams.starting_date >=', $date_from);
            $examBuilder->where('exams.starting_date <=', $date_to);
        }
        $exams = $examBuilder->orderBy('exams.id', 'DESC')->limit($exams_per_page, 0)->get()->getResultArray();
        $classes = $this->db->table('classes')->where('school_id', $school_id)->get()->getResultArray();

        $page_data['classes'] = $classes;
        $page_data['exams'] = $exams;
        $page_data['total_exams'] = $total_exams;
        $page_data['exams_per_page'] = $exams_per_page;
        $page_data['current_page'] = $current_page;
        $page_data['total_pages'] = $exams_per_page > 0 ? (int) ceil($total_exams / $exams_per_page) : 1;

        $exam_calendar = [];
        foreach ($exams as $exam) {
            $exam_calendar[] = [
                'id' => $exam['id'],
                'title' => $exam['name'],
                'start' => date('Y-m-d H:i:s', $exam['starting_date']),
            ];
        }
        $page_data['exam_calendar_json'] = json_encode($exam_calendar);
        return view('backend/superadmin/exam/list', $page_data);
    }

    if (empty($param1)) {
        $page_data['folder_name'] = 'exam';
        $page_data['page_title'] = 'Certifications';
        return view('backend/index', $page_data);
    }
}
  //END EXAM section

  //START MARKS section
  public function mark($param1 = '', $param2 = '')
  {

    if ($param1 == 'list') {
      $page_data['class_id'] = htmlspecialchars($this->request->getPost('class_id'));
      $page_data['section_id'] = htmlspecialchars($this->request->getPost('section_id'));
      // $page_data['quiz_id'] = htmlspecialchars($this->request->getPost('subject'));
      $page_data['exam_id'] = htmlspecialchars($this->request->getPost('exam'));
      $this->crud_model->mark_insert($page_data['class_id'], $page_data['section_id'], $page_data['exam_id']);
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
      // Charger la vue et capturer le contenu
        $html_content = view('backend/superadmin/mark/list', $page_data, ['cache' => 0]);
        
        // PrÃ©parer le nouveau jeton CSRF
        $csrf = array(
            'csrfName' => csrf_token(),
            'csrfHash' => csrf_hash(),
        );
        
        // Renvoyer la rÃ©ponse JSON avec le HTML et le nouveau jeton CSRF
        echo json_encode(array('html' => $html_content, 'csrf' => $csrf));
    }

    if ($param1 == 'mark_update') {
      $this->crud_model->mark_update();
          // PrÃ©parer le nouveau jeton CSRF
          $csrf = array(
            'csrfName' => csrf_token(),
            'csrfHash' => csrf_hash(),
        );
        
        // Renvoyer la rÃ©ponse JSON avec le nouveau jeton CSRF
   
        echo json_encode(array('csrf' => $csrf));

    }

    if (empty($param1)) {
      $user_id = (int) session()->get('user_id');
      $page_data['mark_school_id'] = (int) ($this->db->table('users')->select('school_id')->where('id', $user_id)->get()->getRow('school_id') ?? 0);
      $page_data['mark_exams'] = [];
      $page_data['mark_classes'] = [];
      if ($page_data['mark_school_id'] > 0) {
        $page_data['mark_exams'] = $this->db->table('exams')
          ->where('school_id', $page_data['mark_school_id'])
          ->where('session', active_session())
          ->get()
          ->getResultArray();
        $classes = $this->db->table('classes')
          ->where('school_id', $page_data['mark_school_id'])
          ->get()
          ->getResultArray();
        foreach ($classes as $class) {
          $total_students = (int) $this->db->table('enrols')
            ->where('class_id', $class['id'])
            ->where('school_id', $page_data['mark_school_id'])
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

   //START quiz section
   public function quiz_result($param1 = '', $param2 = '')
   {
 
     if ($param1 == 'list') {
       $page_data['class_id'] = htmlspecialchars($this->request->getPost('class_id'));
       $page_data['cours_id'] = htmlspecialchars($this->request->getPost('cours_id'));
       $page_data['quiz_id'] = htmlspecialchars($this->request->getPost('quiz_id'));
            // Charger la vue et capturer le contenu
            $html_content = view('backend/superadmin/quiz/list', $page_data, ['cache' => 0]);
        
            // PrÃ©parer le nouveau jeton CSRF
            $csrf = array(
                'csrfName' => csrf_token(),
                'csrfHash' => csrf_hash(),
            );
            
            // Renvoyer la rÃ©ponse JSON avec le HTML et le nouveau jeton CSRF
            echo json_encode(array('html' => $html_content, 'csrf' => $csrf));
     }
 
  
 
     if (empty($param1)) {
       $page_data['folder_name'] = 'quiz';
       $page_data['page_title'] = 'quiz';
       return view('backend/index', $page_data);
     }
   }

   public function quiz($action = "", $id = "")
   {
 
     // PROVIDE A LIST OF SECTION ACCORDING TO CLASS ID
     if ($action == 'list') {
       $page_data['class_id'] = $id;
       return view('backend/superadmin/quiz/list_quiz', $page_data);
     }
   }

  // GET THE GRADE ACCORDING TO MARK
  public function get_grade($acquired_mark)
  {
    echo get_grade($acquired_mark);
  }
  //END MARKS sesction

  // GRADE SECTION STARTS
  public function grade($param1 = "", $param2 = "")
  {

    // store data on database
    if ($param1 == 'create') {
      $modelResponse = $this->crud_model->grade_create();
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
                
                echo json_encode($response);
    }

    // update data on database
    if ($param1 == 'update') {
      $response = $this->crud_model->grade_update($param2);
                  // PrÃ©parer la rÃ©ponse avec un nouveau jeton CSRF
            $csrf = array(
              'csrfName' => csrf_token(),
              'csrfHash' => csrf_hash(),
          );
      
          // Renvoyer la rÃ©ponse avec un nouveau jeton CSRF
          echo json_encode(array('status' => $response, 'csrf' => $csrf));
    }

    // delelte data from database
    if ($param1 == 'delete') {
      $response = $this->crud_model->grade_delete($param2);
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
      return view('backend/superadmin/grade/list');
    }

    // showing the index file
    if (empty($param1)) {
      $page_data['folder_name'] = 'grade';
      $page_data['page_title'] = 'grades';
      return view('backend/index', $page_data);
    }
  }
  // GRADE SECTION ENDS

  // STUDENT PROMOTION SECTION STARTS
  function promotion($param1 = "", $promotion_data = "")
  {

    // Promote students. Here promotion_data contains all the data of a student to promote
    if ($param1 == 'promote') {
      $response = $this->crud_model->promote_student($promotion_data);
                  // PrÃ©parer la rÃ©ponse avec un nouveau jeton CSRF
                  $csrf = array(
                    'csrfName' => csrf_token(),
                    'csrfHash' => csrf_hash(),
                );
            
                // Renvoyer la rÃ©ponse avec un nouveau jeton CSRF
                echo json_encode(array('status' => $response, 'csrf' => $csrf));
    }
    //showing the list of student to promote
    if ($param1 == 'list') {
      $page_data['session_from'] = htmlspecialchars($this->request->getPost('session_from'));
      $page_data['session_to'] = htmlspecialchars($this->request->getPost('session_to'));
      $page_data['class_id_from'] = htmlspecialchars($this->request->getPost('class_id_from'));
      $page_data['class_id_to'] = htmlspecialchars($this->request->getPost('class_id_to'));
      $page_data['class_from_details'] = $this->crud_model->get_classes($this->request->getPost('class_id_from'))->getRowArray();
      $page_data['class_to_details'] = $this->crud_model->get_classes($this->request->getPost('class_id_to'))->getRowArray();
      $page_data['session_from_details'] = $this->crud_model->get_session($this->request->getPost('session_from'))->getRowArray();
      $page_data['session_to_details'] = $this->crud_model->get_session($this->request->getPost('session_to'))->getRowArray();
      $page_data['enrolments'] = $this->crud_model->get_student_list()->getResultArray();

        // Charger la vue et capturer le contenu
        $html_content = view('backend/superadmin/promotion/list', $page_data, ['cache' => 0]);
        
        // PrÃ©parer le nouveau jeton CSRF
        $csrf = array(
            'csrfName' => csrf_token(),
            'csrfHash' => csrf_hash(),
        );
        
        // Renvoyer la rÃ©ponse JSON avec le HTML et le nouveau jeton CSRF
        echo json_encode(array('html' => $html_content, 'csrf' => $csrf));
    }
    // showing the index file
    if (empty($param1)) {
      $page_data['folder_name'] = 'promotion';
      $page_data['page_title'] = 'student_promotion';
      return view('backend/index', $page_data);
    }
  }
  // STUDENT PROMOTION SECTION ENDS

  // ACCOUNTING SECTION STARTS
  public function invoice($param1 = "", $param2 = "")
  {
    // For creating new invoice
    if ($param1 == 'single') {
      $modelResponse = $this->crud_model->create_single_invoice();
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
                
                echo json_encode($response);
    }

    // For creating new mass invoice
    if ($param1 == 'mass') {
        $modelResponse = $this->crud_model->create_mass_invoice();
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
      
      echo json_encode($response);
    }

    // For editing invoice
    if ($param1 == 'update') {
      $response = $this->crud_model->update_invoice($param2);
                  // PrÃ©parer la rÃ©ponse avec un nouveau jeton CSRF
            $csrf = array(
              'csrfName' => csrf_token(),
              'csrfHash' => csrf_hash(),
          );
      
          // Renvoyer la rÃ©ponse avec un nouveau jeton CSRF
          echo json_encode(array('status' => $response, 'csrf' => $csrf));
    }

    // For deleting invoice
    if ($param1 == 'delete') {
      $response = $this->crud_model->delete_invoice($param2);
                  // PrÃ©parer la rÃ©ponse avec un nouveau jeton CSRF
            $csrf = array(
              'csrfName' => csrf_token(),
              'csrfHash' => csrf_hash(),
          );
      
          // Renvoyer la rÃ©ponse avec un nouveau jeton CSRF
          echo json_encode(array('status' => $response, 'csrf' => $csrf));
    }

    // Get the list of student. Here param2 defines classId
    if ($param1 == 'student') {
      $page_data['enrolments'] = $this->user_model->get_student_details_by_id('class', $param2);
      return view('backend/superadmin/student/dropdown', $page_data);
    }

    // showing the list of invoices
    if ($param1 == 'invoice') {
      $page_data['invoice_id'] = $param2;
      $page_data['folder_name'] = 'invoice';
      $page_data['page_name'] = 'invoice';
      $page_data['page_title'] = 'invoice';
      return view('backend/index', $page_data);
    }

    // showing the list of invoices
    if ($param1 == 'list') {
      $date = explode('-', $this->request->getGet('date'));
      $page_data['date_from'] = strtotime($date[0] . ' 00:00:00');
      $page_data['date_to'] = strtotime($date[1] . ' 23:59:59');
      $page_data['selected_class'] = htmlspecialchars($this->request->getGet('selectedClass'));
      $page_data['selected_status'] = htmlspecialchars($this->request->getGet('selectedStatus'));
      return view('backend/superadmin/invoice/list', $page_data);
    }
    // showing the index file
    if (empty($param1)) {
      $page_data['folder_name'] = 'invoice';
      $page_data['page_title'] = 'invoice';
      $first_day_of_month = "1 " . date("M") . " " . date("Y") . ' 00:00:00';
      $last_day_of_month = date("t") . " " . date("M") . " " . date("Y") . ' 23:59:59';
      $page_data['date_from'] = strtotime($first_day_of_month);
      $page_data['date_to'] = strtotime($last_day_of_month);
      $page_data['selected_class'] = 'all';
      $page_data['selected_status'] = 'all';
      return view('backend/index', $page_data);
    }
  }

  //EXPORT STUDENT FEES
  public function export($param1 = "", $date_from = "", $date_to = "", $selected_class = "", $selected_status = "")
  {
    //RETURN EXPORT URL
    if ($param1 == 'url') {
      $type = htmlspecialchars($this->request->getPost('type'));
      $dateRange = $this->request->getPost('dateRange');
      
      // Support both separators: ' â€” ' (em dash) and ' - ' (hyphen)
      if (strpos($dateRange, ' â€” ') !== false) {
        $date = explode(' â€” ', $dateRange);
      } elseif (strpos($dateRange, ' - ') !== false) {
        $date = explode(' - ', $dateRange);
      } else {
        $date = explode('-', $dateRange);
      }
      
      $date_from = isset($date[0]) ? strtotime(trim($date[0]) . ' 00:00:00') : strtotime('first day of this month');
      $date_to = isset($date[1]) ? strtotime(trim($date[1]) . ' 23:59:59') : strtotime('last day of this month');
      $selected_class = htmlspecialchars($this->request->getPost('selectedClass'));
      $selected_status = htmlspecialchars($this->request->getPost('selectedStatus'));
      // echo route('export/' . $type . '/' . $date_from . '/' . $date_to . '/' . $selected_class . '/' . $selected_status);
       // GÃ©nÃ©rer l'URL de l'exportation
       $export_url = route('export/' . $type . '/' . $date_from . '/' . $date_to . '/' . $selected_class . '/' . $selected_status);
        
        // GÃ©nÃ©rer un nouveau jeton CSRF
          $csrf = array(
               'csrfName' => csrf_token(),
                'csrfHash' => csrf_hash(),
            );
    
            // Renvoyer la rÃ©ponse avec l'URL et le nouveau jeton CSRF
          echo json_encode(array('url' => $export_url, 'csrf' => $csrf));
    }
    // EXPORT AS PDF
    if ($param1 == 'pdf' || $param1 == 'print') {
      // PrÃ©parer les donnÃ©es Ã  exporter
      $page_data['action'] = $param1;
      $page_data['date_from'] = $date_from;
      $page_data['date_to'] = $date_to;
      $page_data['selected_class'] = $selected_class;
      $page_data['selected_status'] = $selected_status;

      // Charger la vue comme HTML
      ob_start();
      return view('backend/superadmin/invoice/export', $page_data);
      $html = ob_get_clean();

      try {
          // CrÃ©er une instance de mPDF
          $mpdf = new Mpdf();

          // Charger le contenu HTML dans mPDF
          $mpdf->WriteHTML($html);

          // DÃ©finir le nom du fichier
          $fileName = 'Student_fees-' . date('d-M-Y', $date_from) . '-to-' . date('d-M-Y', $date_to) . '.pdf';

          // Stream pour tÃ©lÃ©charger ou afficher le PDF
          if ($param1 == 'pdf') {
              $mpdf->Output($fileName, \Mpdf\Output\Destination::DOWNLOAD); // TÃ©lÃ©charger le PDF
          } else {
              $mpdf->Output($fileName, \Mpdf\Output\Destination::INLINE); // Afficher le PDF dans le navigateur
          }

      } catch (\Mpdf\MpdfException $e) {
          // GÃ©rer les exceptions de mPDF
          echo $e->getMessage();
      }
    }
    // EXPORT AS CSV
    if ($param1 == 'csv') {
      $date_from = $date_from;
      $date_to = $date_to;
      $selected_class = $selected_class;
      $selected_status = $selected_status;

      $invoices = $this->crud_model->get_invoice_by_date_range($date_from, $date_to, $selected_class, $selected_status)->getResultArray();
      $csv_file = fopen("assets/csv_file/invoices.csv", "w");
      $header = array('Invoice-no', 'Student', 'Class', 'Invoice-Title', 'Total-Amount', 'Paid-Amount', 'Creation-Date', 'Payment-Date', 'Status');
      fputcsv($csv_file, $header);
      foreach ($invoices as $invoice) {
        $student_details = $this->user_model->get_student_details_by_id('student', $invoice['student_id']);
        $class_details = $this->crud_model->get_class_details_by_id($invoice['class_id'])->getRowArray();
        if ($invoice['updated_at'] > 0) {
          $payment_date = date('d-M-Y', $invoice['updated_at']);
        } else {
          $payment_date = get_phrase('not_found');
        }
        $lines = array(sprintf('%08d', $invoice['id']), $student_details['name'], $class_details['name'], $invoice['title'], currency($invoice['total_amount']), currency($invoice['paid_amount']), date('d-M-Y', $invoice['created_at']), $payment_date, ucfirst($invoice['status']));
        fputcsv($csv_file, $lines);
      }

      // FILE DOWNLOADING CODES
      if ($selected_status == 'all') {
        $paymentStatusForTitle = 'paid-and-unpaid';
      } else {
        $paymentStatusForTitle = $selected_status;
      }
      if ($selected_class == 'all') {
        $classNameForTitle = 'all_class';
      } else {
        $class_details = $this->crud_model->get_classes($selected_class)->getRowArray();
        $classNameForTitle = $class_details['name'];
      }
      $fileName = 'Student_fees-' . date('d-M-Y', $date_from) . '-to-' . date('d-M-Y', $date_to) . '-' . $classNameForTitle . '-' . $paymentStatusForTitle . '.csv';
      $this->download_file('assets/csv_file/invoices.csv', $fileName);
    }
  }

  /*FUNCTION FOR DOWNLOADING A FILE*/
  function download_file($path, $name)
  {
    // make sure it's a file before doing anything!
    if (is_file($path)) {
      // required for IE
      if (ini_get('zlib.output_compression')) {
        ini_set('zlib.output_compression', 'Off');
      }

      // get the file mime type using the file extension
      helper('file');

      $mime = get_mime_by_extension($path);

      // Build the headers to push out the file properly.
      header('Pragma: public');     // required
      header('Expires: 0');         // no cache
      header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
      header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($path)) . ' GMT');
      header('Cache-Control: private', false);
      header('Content-Type: ' . $mime);  // Add the mime type from Code igniter.
      header('Content-Disposition: attachment; filename="' . basename($name) . '"');  // Add the file name
      header('Content-Transfer-Encoding: binary');
      header('Content-Length: ' . filesize($path)); // provide file size
      header('Connection: close');
      readfile($path); // push it out
      exit();
    }
  }


  // Expense Category
  public function expense_category($param1 = "", $param2 = "")
  {
    if ($param1 == 'create') {
        $modelResponse = $this->crud_model->create_expense_category();
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
      
        echo json_encode($response);
    }

    if ($param1 == 'update') {
      $response = $this->crud_model->update_expense_category($param2);
                  // PrÃ©parer la rÃ©ponse avec un nouveau jeton CSRF
            $csrf = array(
              'csrfName' => csrf_token(),
              'csrfHash' => csrf_hash(),
          );
      
          // Renvoyer la rÃ©ponse avec un nouveau jeton CSRF
          echo json_encode(array('status' => $response, 'csrf' => $csrf));
    }

    if ($param1 == 'delete') {
      $response = $this->crud_model->delete_expense_category($param2);
                  // PrÃ©parer la rÃ©ponse avec un nouveau jeton CSRF
            $csrf = array(
              'csrfName' => csrf_token(),
              'csrfHash' => csrf_hash(),
          );
      
          // Renvoyer la rÃ©ponse avec un nouveau jeton CSRF
          echo json_encode(array('status' => $response, 'csrf' => $csrf));
    }

    if ($param1 == 'list') {
      return view('backend/superadmin/expense_category/list');
    }
    // showing the index file
    if (empty($param1)) {
      $page_data['folder_name'] = 'expense_category';
      $page_data['page_title'] = 'expense_category';
      return view('backend/index', $page_data);
    }
  }

  //Expense Manager
  public function expense($param1 = "", $param2 = "")
  {

    // adding expense
    if ($param1 == 'create') {
          $modelResponse = $this->crud_model->create_expense();
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
        
        echo json_encode($response);
    }

    // update expense
    if ($param1 == 'update') {
      $response = $this->crud_model->update_expense($param2);
                  // PrÃ©parer la rÃ©ponse avec un nouveau jeton CSRF
            $csrf = array(
              'csrfName' => csrf_token(),
              'csrfHash' => csrf_hash(),
          );
      
          // Renvoyer la rÃ©ponse avec un nouveau jeton CSRF
          echo json_encode(array('status' => $response, 'csrf' => $csrf));
    }

    // deleting expense
    if ($param1 == 'delete') {
      $response = $this->crud_model->delete_expense($param2);
                  // PrÃ©parer la rÃ©ponse avec un nouveau jeton CSRF
            $csrf = array(
              'csrfName' => csrf_token(),
              'csrfHash' => csrf_hash(),
          );
      
          // Renvoyer la rÃ©ponse avec un nouveau jeton CSRF
          echo json_encode(array('status' => $response, 'csrf' => $csrf));
    }
    // showing the list of expense
    if ($param1 == 'list') {
      $date = explode('-', $this->request->getGet('date'));
      $page_data['date_from'] = strtotime($date[0] . ' 00:00:00');
      $page_data['date_to'] = strtotime($date[1] . ' 23:59:59');
      $page_data['expense_category_id'] = htmlspecialchars($this->request->getGet('expense_category_id'));
      return view('backend/superadmin/expense/list', $page_data);
    }

    // showing the index file
    if (empty($param1)) {
      $page_data['folder_name'] = 'expense';
      $page_data['page_title'] = 'expense';
      $page_data['date_from'] = strtotime(date('d-M-Y', strtotime(' -30 day')) . ' 00:00:00');
      $page_data['date_to'] = strtotime(date('d-M-Y') . ' 23:59:59');
      return view('backend/index', $page_data);
    }
  }
  // ACCOUNTING SECTION ENDS

  // BACKOFFICE SECTION

  //START SESSION_MANAGER section
  public function session_manager($param1 = '', $param2 = '')
  {
    $school_id = school_id();

    if ($param1 == 'create') {
      $modelResponse = $this->crud_model->session_create();
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
        
        echo json_encode($response);
    }

    if ($param1 == 'update') {
      $response = $this->crud_model->session_update($param2);
                  // PrÃ©parer la rÃ©ponse avec un nouveau jeton CSRF
            $csrf = array(
              'csrfName' => csrf_token(),
              'csrfHash' => csrf_hash(),
          );
      
          // Renvoyer la rÃ©ponse avec un nouveau jeton CSRF
          echo json_encode(array('status' => $response, 'csrf' => $csrf));
    }

    if ($param1 == 'delete') {
      $response = $this->crud_model->session_delete($param2);
          // PrÃ©parer la rÃ©ponse avec un nouveau jeton CSRF
            $csrf = array(
              'csrfName' => csrf_token(),
              'csrfHash' => csrf_hash(),
          );
      
          // Renvoyer la rÃ©ponse avec un nouveau jeton CSRF
          echo json_encode(array('status' => $response, 'csrf' => $csrf));
    }

    if ($param1 == 'active_session') {
      $response = $this->crud_model->active_session($param2);
      echo $response;
    }

    if ($param1 == 'reopen_session') {
      echo $this->superadmin_model->get_session_by_school_and_status($school_id);
    }

    if ($param1 == 'reopen_list') {
      return view('backend/superadmin/session/table_body');
    }

    if ($param1 == 'list') {
      return view('backend/superadmin/session/list');
    }

    if (empty($param1)) {
      $page_data['folder_name'] = 'session';
      $page_data['page_title'] = 'session_manager';
      return view('backend/index', $page_data);
    }
  }
  //END SESSION_MANAGER section

  //BOOK LIST MANAGER
  public function book($param1 = "", $param2 = "")
  {
    // adding book
    if ($param1 == 'create') {
      $modelResponse = $this->crud_model->create_book();
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
        
        echo json_encode($response);
    }

    // update book
    if ($param1 == 'update') {
      $response = $this->crud_model->update_book($param2);
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
      return view('backend/superadmin/book/list');
    }

    // showing the index file
    if (empty($param1)) {
      $page_data['folder_name'] = 'book';
      $page_data['page_title'] = 'books';
      return view('backend/index', $page_data);
    }
  }

  //BOOK ISSUE LIST MANAGER
  public function book_issue($param1 = "", $param2 = "")
  {
    // adding book
    if ($param1 == 'create') {
      $modelResponse = $this->crud_model->create_book_issue();
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
        
        echo json_encode($response);
    }

    // update book
    if ($param1 == 'update') {
      $response = $this->crud_model->update_book_issue($param2);
                  // PrÃ©parer la rÃ©ponse avec un nouveau jeton CSRF
            $csrf = array(
              'csrfName' => csrf_token(),
              'csrfHash' => csrf_hash(),
          );
      
          // Renvoyer la rÃ©ponse avec un nouveau jeton CSRF
          echo json_encode(array('status' => $response, 'csrf' => $csrf));
    }

    // Returning a book
    if ($param1 == 'return') {
      $response = $this->crud_model->return_issued_book($param2);
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
      $response = $this->crud_model->delete_book_issue($param2);
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
      $date = explode('-', $this->request->getGet('date'));
      $page_data['date_from'] = strtotime($date[0] . ' 00:00:00');
      $page_data['date_to'] = strtotime($date[1] . ' 23:59:59');
      return view('backend/superadmin/book_issue/list', $page_data);
    }

    // showing the index file
    if (empty($param1)) {
      $page_data['folder_name'] = 'book_issue';
      $page_data['page_title'] = 'book_issue';
      $page_data['date_from'] = strtotime(date('d-M-Y', strtotime(' -30 day')) . ' 00:00:00');
      $page_data['date_to'] = strtotime(date('d-M-Y') . ' 23:59:59');
      return view('backend/index', $page_data);
    }
  }

  // ADDON MANAGER
  public function addon_manager($param1 = "", $param2 = "")
  {
    if ($param1 == 'install') {
      $modelResponse = $this->addon_model->install_addon();
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
        
        echo json_encode($response);
    }

    // DEACTIVE ADDONS
    if ($param1 == 'deactive') {
      $response = $this->addon_model->deactivate_addon($param2);
          // PrÃ©parer la rÃ©ponse avec un nouveau jeton CSRF
            $csrf = array(
              'csrfName' => csrf_token(),
              'csrfHash' => csrf_hash(),
          );
      
          // Renvoyer la rÃ©ponse avec un nouveau jeton CSRF
          echo json_encode(array('status' => $response, 'csrf' => $csrf));
    }
    // ACTIVATE ADDONS
    if ($param1 == 'activate') {
      $response = $this->addon_model->activate_addon($param2);
          // PrÃ©parer la rÃ©ponse avec un nouveau jeton CSRF
            $csrf = array(
              'csrfName' => csrf_token(),
              'csrfHash' => csrf_hash(),
          );
      
          // Renvoyer la rÃ©ponse avec un nouveau jeton CSRF
          echo json_encode(array('status' => $response, 'csrf' => $csrf));
    }

    // DELETING ADDONS
    if ($param1 == 'delete') {
      $response = $this->addon_model->remove_addon($param2);
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
      return view('backend/superadmin/addon/list');
    }
    // showing the index file
    if (empty($param1)) {
      $page_data['folder_name'] = 'addon';
      $page_data['page_title'] = 'addon_manager';
      return view('backend/index', $page_data);
    }
  }

  // NOTICEBOARD MANAGER
  public function noticeboard($param1 = "", $param2 = "", $param3 = "")
  {
    // adding notice
    if ($param1 == 'create') {
      $modelResponse = $this->crud_model->create_notice();
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
        
        echo json_encode($response);
    }

    // update notice
    if ($param1 == 'update') {
        $modelResponse = $this->crud_model->update_notice($param2);
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
      
        echo json_encode($response);
    }

    // deleting notice
    if ($param1 == 'delete') {
      $response = $this->crud_model->delete_notice($param2);
          // PrÃ©parer la rÃ©ponse avec un nouveau jeton CSRF
            $csrf = array(
              'csrfName' => csrf_token(),
              'csrfHash' => csrf_hash(),
          );
      
          // Renvoyer la rÃ©ponse avec un nouveau jeton CSRF
          echo json_encode(array('status' => $response, 'csrf' => $csrf));
    }
    // showing the list of notice
    if ($param1 == 'list') {
      return view('backend/superadmin/noticeboard/list');
    }

    // showing the all the notices
    if ($param1 == 'all_notices') {
      $response = $this->crud_model->get_all_the_notices();
      echo $response;
    }

    // showing the index file
    if (empty($param1)) {
      $page_data['folder_name'] = 'noticeboard';
      $page_data['page_title'] = 'noticeboard';
      return view('backend/index', $page_data);
    }
  }

  // SETTINGS MANAGER
  public function system_settings($param1 = "", $param2 = "")
  {
    if ($param1 == 'update') {
      $response = $this->settings_model->update_system_settings();
          // PrÃ©parer la rÃ©ponse avec un nouveau jeton CSRF
            $csrf = array(
              'csrfName' => csrf_token(),
              'csrfHash' => csrf_hash(),
          );
      
          // Renvoyer la rÃ©ponse avec un nouveau jeton CSRF
          echo json_encode(array('status' => $response, 'csrf' => $csrf));
    }

    if ($param1 == 'logo_update') {
      $response = $this->settings_model->update_system_logo();
          // PrÃ©parer la rÃ©ponse avec un nouveau jeton CSRF
            $csrf = array(
              'csrfName' => csrf_token(),
              'csrfHash' => csrf_hash(),
          );
      
          // Renvoyer la rÃ©ponse avec un nouveau jeton CSRF
          echo json_encode(array('status' => $response, 'csrf' => $csrf));
    }
    // showing the System Settings file
    if (empty($param1)) {
      $page_data['folder_name'] = 'settings';
      $page_data['page_title'] = 'system_settings';
      $page_data['settings_type'] = 'system_settings';
      return view('backend/index', $page_data);
    }
  }

  // FRONTEND SETTINGS MANAGER
  public function website_settings($param1 = '', $param2 = '', $param3 = '')
  {
    if ($param1 == 'events') {
      $page_data['page_content'] = 'events';
    }
    if ($param1 == 'gallery') {
      $page_data['page_content'] = 'gallery';
    }
    if ($param1 == 'privacy_policy') {
      $page_data['page_content'] = 'privacy_policy';
    }
    if ($param1 == 'about_us') {
      $page_data['page_content'] = 'about_us';
    }
    if ($param1 == 'terms_and_conditions') {
      $page_data['page_content'] = 'terms_and_conditions';
    }
    if ($param1 == 'homepage_slider') {
      $page_data['page_content'] = 'homepage_slider';
    }
    if ($param1 == 'gallery_image') {
      $page_data['page_content'] = 'gallery_image';
      $page_data['gallery_id'] = $param2;
    }
    if ($param1 == 'other_settings') {
      $page_data['page_content'] = 'other_settings';
    }
    if (empty($param1) || $param1 == 'general_settings') {
      $page_data['page_content'] = 'general_settings';
    }

    $page_data['folder_name'] = 'website_settings';
    $page_data['page_title'] = 'website_settings';
    $page_data['settings_type'] = 'website_settings';
    return view('backend/index', $page_data);
  }

  public function website_update($param1 = "")
{
    if ($param1 == 'general_settings') {
        // Force JSON Content-Type header
        header('Content-Type: application/json');
        
        $update_status = $this->frontend_model->update_frontend_general_settings();
        
        $response = [
            'status' => $update_status,
            'notification' => $update_status ? get_phrase('general_settings_updated') : get_phrase('failed_to_update_settings'),
            'csrf' => [
                'name' => csrf_token(),
                'hash' => csrf_hash()
            ]
        ];
        
        echo json_encode($response);
        exit;
    }
}


  public function other_settings_update($param1 = "")
  {
    $response = $this->frontend_model->other_settings_update();
    // echo $response;
        // PrÃ©parer la rÃ©ponse avec un nouveau jeton CSRF
        $csrf = array(
          'csrfName' => csrf_token(),
          'csrfHash' => csrf_hash(),
                );
              
        // Renvoyer la rÃ©ponse avec un nouveau jeton CSRF
        echo json_encode(array('status' => $response, 'csrf' => $csrf));
  }

  public function update_recaptcha_settings($param1 = "")
  {
    $response = $this->frontend_model->update_recaptcha_settings();
    // echo $response;
      // PrÃ©parer la rÃ©ponse avec un nouveau jeton CSRF
      $csrf = array(
          'csrfName' => csrf_token(),
          'csrfHash' => csrf_hash(),
                );
              
      // Renvoyer la rÃ©ponse avec un nouveau jeton CSRF
      echo json_encode(array('status' => $response, 'csrf' => $csrf));

  }

  public function events($param1 = "", $param2 = "")
  {
    // DEACTIVE ADDONS
    if ($param1 == 'create') {
      $response = $this->frontend_model->event_create();
      // echo $response;
        // PrÃ©parer la rÃ©ponse avec un nouveau jeton CSRF
        $csrf = array(
          'csrfName' => csrf_token(),
          'csrfHash' => csrf_hash(),
                );
              
        // Renvoyer la rÃ©ponse avec un nouveau jeton CSRF
        echo json_encode(array('status' => $response, 'csrf' => $csrf));
    }
    // ACTIVATE ADDONS
    if ($param1 == 'update') {
      $response = $this->frontend_model->event_update($param2);
      // echo $response;
        // PrÃ©parer la rÃ©ponse avec un nouveau jeton CSRF
        $csrf = array(
          'csrfName' => csrf_token(),
          'csrfHash' => csrf_hash(),
                );
              
        // Renvoyer la rÃ©ponse avec un nouveau jeton CSRF
        echo json_encode(array('status' => $response, 'csrf' => $csrf));
    }

    // DELETING ADDONS
    if ($param1 == 'delete') {
      $response = $this->frontend_model->event_delete($param2);
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
      return view('backend/superadmin/website_settings/events');
    }

    // showing the System Settings file
    if (empty($param1)) {
      return redirect()->to(route_to('website_settings/events'));
    }
  }

  //FRONTEND GALLERY
  public function frontend_gallery($param1 = "", $param2 = "", $param3 = "")
  {
    if ($param1 == 'create') {
      $response = $this->frontend_model->add_frontend_gallery();
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
      $response = $this->frontend_model->update_frontend_gallery($param2);
      // echo $response;
        // PrÃ©parer la rÃ©ponse avec un nouveau jeton CSRF
        $csrf = array(
          'csrfName' => csrf_token(),
          'csrfHash' => csrf_hash(),
                );
              
        // Renvoyer la rÃ©ponse avec un nouveau jeton CSRF
        echo json_encode(array('status' => $response, 'csrf' => $csrf));
    }

    if ($param1 == 'delete') {
      $response = $this->frontend_model->delete_frontend_gallery($param2);
      // echo $response;
        // PrÃ©parer la rÃ©ponse avec un nouveau jeton CSRF
        $csrf = array(
          'csrfName' => csrf_token(),
          'csrfHash' => csrf_hash(),
                );
              
        // Renvoyer la rÃ©ponse avec un nouveau jeton CSRF
        echo json_encode(array('status' => $response, 'csrf' => $csrf));
    }

    if ($param1 == 'gallery_list') {
      return view('backend/superadmin/website_settings/gallery');
    }

    // HERE STARTS THE GALLER IMAGES PART

    if ($param1 == 'gallery_photo_list') {
      $page_data['gallery_id'] = $param2;
      return view('backend/superadmin/website_settings/gallery_image', $page_data);
    }

    if ($param1 == 'gallery_photo_delete') {
      $response = $this->frontend_model->delete_gallery_photo($param2);
      // echo $response;
          // PrÃ©parer la rÃ©ponse avec un nouveau jeton CSRF
          $csrf = array(
            'csrfName' => csrf_token(),
            'csrfHash' => csrf_hash(),
                  );
                
        // Renvoyer la rÃ©ponse avec un nouveau jeton CSRF
        echo json_encode(array('status' => $response, 'csrf' => $csrf));
    }

    if ($param1 == 'gallery_photo_upload') {
      $response = $this->frontend_model->upload_gallery_photo($param2);
      // echo $response;
        // PrÃ©parer la rÃ©ponse avec un nouveau jeton CSRF
        $csrf = array(
          'csrfName' => csrf_token(),
          'csrfHash' => csrf_hash(),
                );
              
        // Renvoyer la rÃ©ponse avec un nouveau jeton CSRF
        echo json_encode(array('status' => $response, 'csrf' => $csrf));
    }
  }

  //ABOUT US UPDATE
  public function about_us($param1 = "")
  {
    if ($param1 == 'update') {
      $response = $this->frontend_model->update_about_us();
      // echo $response;
      // PrÃ©parer la rÃ©ponse avec un nouveau jeton CSRF
      $csrf = array(
        'csrfName' => csrf_token(),
        'csrfHash' => csrf_hash(),
              );
            
      // Renvoyer la rÃ©ponse avec un nouveau jeton CSRF
      echo json_encode(array('status' => $response, 'csrf' => $csrf));
    } else {
      return redirect()->to(site_url('app/dashboard'));
    }
  }

  //PRIVACY POLICY UPDATE
  public function privacy_policy($param1 = "")
  {
    if ($param1 == 'update') {
      $response = $this->frontend_model->update_privacy_policy();
      // echo $response;
      // PrÃ©parer la rÃ©ponse avec un nouveau jeton CSRF
      $csrf = array(
        'csrfName' => csrf_token(),
        'csrfHash' => csrf_hash(),
              );
            
      // Renvoyer la rÃ©ponse avec un nouveau jeton CSRF
      echo json_encode(array('status' => $response, 'csrf' => $csrf));
    } else {
      return redirect()->to(site_url('app/dashboard'));
    }
  }

  //TERMS AND CONDITION UPDATE
  public function terms_and_conditions($param1 = "")
  {
    if ($param1 == 'update') {
      $response = $this->frontend_model->update_terms_and_conditions();
      // echo $response;
      // PrÃ©parer la rÃ©ponse avec un nouveau jeton CSRF
      $csrf = array(
        'csrfName' => csrf_token(),
        'csrfHash' => csrf_hash(),
              );
            
      // Renvoyer la rÃ©ponse avec un nouveau jeton CSRF
      echo json_encode(array('status' => $response, 'csrf' => $csrf));
    } else {
      return redirect()->to(site_url('app/dashboard'));
    }
  }
  //TERMS AND CONDITION UPDATE
  public function homepage_slider($param1 = "")
  {
    if ($param1 == 'update') {
      $response = $this->frontend_model->update_homepage_slider();
      // echo $response;
      // PrÃ©parer la rÃ©ponse avec un nouveau jeton CSRF
      $csrf = array(
        'csrfName' => csrf_token(),
        'csrfHash' => csrf_hash(),
              );
            
      // Renvoyer la rÃ©ponse avec un nouveau jeton CSRF
      echo json_encode(array('status' => $response, 'csrf' => $csrf));
    } else {
      return redirect()->to(site_url('app/dashboard'));
    }
  }

  // SETTINGS MANAGER
  public function school_settings($param1 = "", $param2 = "")
  {
    if ($param1 == 'update') {
      $response = $this->settings_model->update_current_school_settings();
      // echo $response;
      // PrÃ©parer la rÃ©ponse avec un nouveau jeton CSRF
      $csrf = array(
        'csrfName' => csrf_token(),
        'csrfHash' => csrf_hash(),
              );
            
      // Renvoyer la rÃ©ponse avec un nouveau jeton CSRF
      echo json_encode(array('status' => $response, 'csrf' => $csrf));
    }

    // showing the System Settings file
    if (empty($param1)) {
      $page_data['folder_name'] = 'settings';
      $page_data['page_title'] = 'school_settings';
      $page_data['settings_type'] = 'school_settings';
      return view('backend/index', $page_data);
    }
  }

  // PAYMENT SETTINGS MANAGER
  public function payment_settings($param1 = "", $param2 = "")
  {
    $actions = [
      'system' => 'update_system_currency_settings',
      'price' => 'update_system_price',
      'vat' => 'update_system_vat',
      'paypal' => 'update_paypal_settings',
      'stripe' => 'update_stripe_settings'
    ];

    if (isset($actions[$param1])) {
      $modelResponse = $this->settings_model->{$actions[$param1]}();
      return $this->response->setJSON($this->normalizePaymentSettingsResponse($modelResponse));
    }

    // showing the Payment Settings file
    if (empty($param1)) {
      $page_data['folder_name'] = 'settings';
      $page_data['page_title'] = 'payment_settings';
      $page_data['settings_type'] = 'payment_settings';
      return view('backend/index', $page_data);
    }
  }

  private function normalizePaymentSettingsResponse($modelResponse): array
  {
    $status = false;
    $notification = get_phrase('action_not_allowed');

    if (is_bool($modelResponse)) {
      $status = $modelResponse;
    } elseif (is_array($modelResponse)) {
      $status = (bool) ($modelResponse['status'] ?? false);
      if (!empty($modelResponse['notification'])) {
        $notification = (string) $modelResponse['notification'];
      }
    } elseif (is_string($modelResponse)) {
      $decoded = json_decode($modelResponse, true);
      if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
        $status = (bool) ($decoded['status'] ?? false);
        if (!empty($decoded['notification'])) {
          $notification = (string) $decoded['notification'];
        }
      } else {
        $status = trim($modelResponse) !== '';
      }
    }

    if ($status && $notification === get_phrase('action_not_allowed')) {
      $notification = get_phrase('updated_successfully');
    }

    return [
      'status' => $status,
      'notification' => $notification,
      'csrf' => [
        'name' => csrf_token(),
        'hash' => csrf_hash(),
      ],
    ];
  }

  // LANGUAGE SETTINGS
  public function language($param1 = "", $param2 = "")
  {
    // adding language
    if ($param1 == 'create') {
      $response = $this->settings_model->create_language();
      // echo $response;
      // PrÃ©parer la rÃ©ponse avec un nouveau jeton CSRF
      $csrf = array(
        'csrfName' => csrf_token(),
        'csrfHash' => csrf_hash(),
              );
            
      // Renvoyer la rÃ©ponse avec un nouveau jeton CSRF
      echo json_encode(array('status' => $response, 'csrf' => $csrf));
    }

    // update language
    if ($param1 == 'update') {
      $response = $this->settings_model->update_language($param2);
      // echo $response;
      // PrÃ©parer la rÃ©ponse avec un nouveau jeton CSRF
      $csrf = array(
        'csrfName' => csrf_token(),
        'csrfHash' => csrf_hash(),
              );
            
      // Renvoyer la rÃ©ponse avec un nouveau jeton CSRF
      echo json_encode(array('status' => $response, 'csrf' => $csrf));
    }

    // deleting language
    if ($param1 == 'delete') {
      $response = $this->settings_model->delete_language($param2);
      // echo $response;
      // PrÃ©parer la rÃ©ponse avec un nouveau jeton CSRF
      $csrf = array(
        'csrfName' => csrf_token(),
        'csrfHash' => csrf_hash(),
              );
            
      // Renvoyer la rÃ©ponse avec un nouveau jeton CSRF
      echo json_encode(array('status' => $response, 'csrf' => $csrf));
    }

    // showing the list of language
    if ($param1 == 'list') {
      return view('backend/superadmin/language/list');
    }

    // showing the list of language
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
    if ($param1 == 'update_phrase') {
      $current_editing_language = htmlspecialchars($this->request->getPost('currentEditingLanguage'));
      $updatedValue = htmlspecialchars($this->request->getPost('updatedValue'));
      $key = htmlspecialchars($this->request->getPost('key'));
      saveJSONFile($current_editing_language, $key, $updatedValue);
      $response =  $current_editing_language . ' ' . $key . ' ' . $updatedValue;
      // PrÃ©parer la rÃ©ponse avec un nouveau jeton CSRF
      $csrf = array(
        'csrfName' => csrf_token(),
        'csrfHash' => csrf_hash(),
              );
            
      // Renvoyer la rÃ©ponse avec un nouveau jeton CSRF
      echo json_encode(array('response' => $response, 'csrf' => $csrf));
    }

    // GET THE DROPDOWN OF LANGUAGES
    if ($param1 == 'dropdown') {
      return view('backend/superadmin/language/dropdown');
    }
    // showing the index file
    if (empty($param1)) {
      $page_data['folder_name'] = 'language';
      $page_data['page_title'] = 'languages';
      return view('backend/index', $page_data);
    }
  }
  // SMTP SETTINGS MANAGER
  public function smtp_settings($param1 = "", $param2 = "")
  {
    if ($param1 == 'update') {
      $response = $this->settings_model->update_smtp_settings();
      // echo $response;
      // PrÃ©parer la rÃ©ponse avec un nouveau jeton CSRF
      $csrf = array(
        'csrfName' => csrf_token(),
        'csrfHash' => csrf_hash(),
              );
            
      // Renvoyer la rÃ©ponse avec un nouveau jeton CSRF
      echo json_encode(array('status' => $response, 'csrf' => $csrf));
    }

    // showing the Smtp Settings file
    if (empty($param1)) {
      $page_data['folder_name'] = 'settings';
      $page_data['page_title'] = 'smtp_settings';
      $page_data['settings_type'] = 'smtp_settings';
      return view('backend/index', $page_data);
    }
  }

  //MANAGE PROFILE STARTS
  public function profile($param1 = "", $param2 = "")
  {
    if ($param1 == 'update_profile') {
      $response = $this->user_model->update_profile();
      echo $response;
           
    }
    if ($param1 == 'update_password') {
      $response = $this->user_model->update_password();
      echo $response;
    }

    // showing the Smtp Settings file
    if (empty($param1)) {
      $page_data['folder_name'] = 'profile';
      $page_data['page_title'] = 'manage_profile';
      return view('backend/index', $page_data);
    }
  }
  //MANAGE PROFILE ENDS


  // ABOUT APPLICATION STARTS
  public function about()
  {
    $page_data['application_details'] = $this->settings_model->get_application_details();
    $page_data['folder_name'] = 'about';
    $page_data['page_title'] = 'about';
    return view('backend/index', $page_data);
  }
  // ABOUT APPLICATION ENDS




  public function online_admission($param1 = "", $user_id = "")
  {


    if ($param1 == 'assigned') {

      $data['student_id'] = $this->request->getPost('student_id');

      $school_id = $this->superadmin_model->get_student_school_id_by_student_id($data['student_id']);
      $user_id = $this->superadmin_model->get_student_user_id_by_student_id($data['student_id']);
      $code = $this->superadmin_model->get_student_code_by_id($data['student_id'], $school_id);

       $this->email_model->approved_online_admission($data['student_id'], $user_id);

      $this->superadmin_model->update_table('students', ['code' => $code, 'id' => $data['student_id']], ['status' => 1]);

      session()->setFlashdata('flash_message', get_phrase('admission_request_has_been_updated'));
      return redirect()->to(site_url('app/online_admission'));

    }

    if ($param1 == 'delete') {
      //$this->db->where('id', $user_id);
      //$this->db->delete('users');
      $this->superadmin_model->delete_table('students', ['user_id' => $user_id]);

      session()->setFlashdata('flash_message', get_phrase('admission_data_deleted_successfully'));
      return redirect()->to(site_url('app/online_admission'));
    }

    $empty = true;
    
    $query = $this->superadmin_model->get_where_result('students', ['school_id' => school_id(), 'status' => 0]);

    if (count($query) > 0) {
      $empty = false;
    }

    if (!$empty) {

      $page_data['applications'] = $this->superadmin_model->get_students_by_status_and_school(0, school_id());
    } else {
      $page_data['applications'] = null;
    }


    $page_data['folder_name'] = 'online_admission';
    $page_data['page_title'] = 'online_admission';
    return view('backend/index', $page_data);
  }



  public function online_admission_school($param1 = "", $school_id = "")
  {

    if ($param1 == 'approved') {

      $response = $this->user_model->approved_school();

      session()->setFlashdata('flash_message', get_phrase('admission_request_has_been_updated'));
      return redirect()->to(site_url('app/online_admission_school'));
    }
    if ($param1 == 'delete') {

      $data['Etat'] = 0;
      $this->superadmin_model->update_table('schools', ['id' => $school_id], $data);

      // $this->db->where('user_id', $user_id);
      // $this->db->delete('students');
      session()->setFlashdata('flash_message', get_phrase('admission_data_deleted_successfully'));
      return redirect()->to(site_url('app/online_admission_school'));
    }
    $page_data['applications'] = $this->superadmin_model->get_users_by_status_and_school(4, school_id());
    $page_data['schools'] = $this->superadmin_model->get_inactive_schools();
    $page_data['folder_name'] = 'online_admission_school';
    $page_data['page_title'] = 'online_admission_school';
    return view('backend/index', $page_data);
  }

  public function exam_results($exam_id = "", $student_id = "") {
    try {
        // VÃ©rifier si l'utilisateur est connectÃ© en tant que superadmin
        if (!session()->get('superadmin_login') || session()->get('user_type') != 'superadmin') {
            return redirect()->to(site_url('login'));
        }

        // RÃ©cupÃ©rer la session active
        $session_id = active_session();

        // RÃ©cupÃ©rer les dÃ©tails de l'examen
        $sql = "SELECT exams.*, classes.name as class_name, sections.name as section_name, schools.name as school_name 
                FROM exams 
                LEFT JOIN classes ON exams.class_id = classes.id 
                LEFT JOIN sections ON exams.section_id = sections.id 
                LEFT JOIN schools ON exams.school_id = schools.id 
                WHERE exams.id = ?";
        $exam = $this->superadmin_model->get_row_by_query($sql, [$exam_id]);

        // VÃ©rifier si l'examen existe
        if (!$exam) {
            return redirect()->to(site_url('app/exam'));
        }

        // VÃ©rifier l'existence de l'Ã©tudiant et son association avec l'Ã©cole
        $student_data = $this->superadmin_model->get_student_by_id_and_school($student_id, $exam['school_id']);
        if (!$student_data) {
            return redirect()->to(site_url('app/exam'));
        }

        // RÃ©cupÃ©rer les rÃ©ponses soumises par l'Ã©tudiant
        $sql = "SELECT er.*, eq.title as question_title 
                FROM exam_responses er 
                LEFT JOIN exam_questions eq ON er.exam_question_id = eq.id 
                WHERE er.exam_id = ? AND er.user_id = ?";
        $submitted_answers = $this->superadmin_model->get_by_query($sql, [$exam_id, $student_data['user_id']]);

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

public function get_classes_by_school()
{
    if (session()->get('superadmin_login') != 1) {
        echo json_encode(['status' => 'error', 'message' => 'Session expired, please login again']);
        exit;
    }

    $school_id = $this->request->getPost('school_id');
    if (empty($school_id)) {
        echo json_encode(['classes' => [], 'status' => 'error', 'message' => 'No school ID provided']);
        exit;
    }

    $classes = $this->superadmin_model->get_classes_by_school($school_id);
    if (empty($classes)) {
        echo json_encode(['classes' => [], 'status' => 'success', 'message' => 'No classes found for this school']);
    } else {
        $csrf = array(
            'csrfName' => csrf_token(),
            'csrfHash' => csrf_hash(),
        );
        echo json_encode(array('classes' => $classes, 'csrf' => $csrf, 'status' => 'success'));
    }
}

public function filter_exams()
{
    if (session()->get('superadmin_login') != 1) {
        echo json_encode(['error' => 'Unauthorized']);
        exit;
    }

    $school_id = school_id();
    $session = active_session();

    $class_id = $this->request->getPost('class_id');
    $section_id = $this->request->getPost('section_id');
    $date_range = $this->request->getPost('date_range');
    $date_from = '';
    $date_to = '';

    if (!empty($date_range)) {
        $dates = explode(' - ', $date_range);
        $date_from = strtotime(trim($dates[0]) . ' 00:00:00');
        $date_to = strtotime(trim($dates[1]) . ' 23:59:59');
    }

    $sql = "SELECT exams.*, classes.name as class_name, sections.name as section_name 
            FROM exams 
            LEFT JOIN classes ON exams.class_id = classes.id 
            LEFT JOIN sections ON exams.section_id = sections.id 
            WHERE exams.school_id = ? AND exams.session = ?";
    $params = [$school_id, $session];
    
    if (!empty($class_id)) {
        $sql .= " AND exams.class_id = ?";
        $params[] = $class_id;
    }
    if (!empty($section_id)) {
        $sql .= " AND exams.section_id = ?";
        $params[] = $section_id;
    }
    if (!empty($date_range)) {
        $sql .= " AND exams.starting_date >= ? AND exams.starting_date <= ?";
        $params[] = $date_from;
        $params[] = $date_to;
    }
    $exams = $this->superadmin_model->get_by_query($sql, $params);

    $exam_data = [];
    foreach ($exams as $exam) {
        $exam_data[] = [
            'id' => $exam['id'],
            'name' => $exam['name'] ?: 'Unnamed Exam',
            'formatted_date' => $exam['starting_date'] ? date('D, d-M-Y H:i', $exam['starting_date']) : 'No Date',
            'class_name' => $exam['class_name'] ?: 'No Class',
            'section_name' => $exam['section_name'] ?: 'No Section'
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
            'section_id' => $section_id,
            'date_range' => $date_range,
            'exam_count' => count($exams)
        ]
    ]);
}

public function calendar($param1 = '', $param2 = '', $param3 = '', $param4 = '') {
    if (session()->get('superadmin_login') != 1) {
        $csrf = [
            'csrfName' => csrf_token(),
            'csrfHash' => csrf_hash(),
        ];
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized', 'csrf' => $csrf]);
        exit;
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
        return view('backend/admin/calendar/list', $page_data);
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
    if (session()->get('superadmin_login') != 1) {
        echo json_encode(['status' => 'error', 'message' => 'Session expired, please login again']);
        exit;
    }

    $user_id = session()->get('user_id');
    $user = $this->superadmin_model->get_user_by_id($user_id);

    if (!$user || empty($user['school_id'])) {
        echo json_encode(['status' => 'error', 'message' => 'No school associated with this user']);
        exit;
    }

    $school = $this->superadmin_model->get_active_school($user['school_id']);
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
    if (session()->get('superadmin_login') != 1) {
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
		'created_by' => session()->get('user_id')
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
    if (!$this->superadmin_model->school_exists($data['school_id'])) {
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

    // Validation de class_id
    $participants = json_decode($this->request->getPost('participants'), true);
    if (json_last_error() !== JSON_ERROR_NONE || !is_array($participants) || empty($participants)) {
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
            if (!$this->superadmin_model->class_exists($participant['id'], $data['school_id'])) {
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
            if (!$this->superadmin_model->user_exists($participant['id'])) {
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

    // Validation de session (exemple)
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
        $event_id = $this->superadmin_model->insert_table('event_calendars', $data);
		foreach ($participants as $participant) {
            $this->superadmin_model->insert_table('participants', [
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
            'message' => get_phrase('Error creating event: ') . $e->getMessage(),
            'csrf' => [
                'csrfName' => csrf_token(),
                'csrfHash' => csrf_hash()
            ]
        ]);
    }
}

    public function update_event() {
        if (session()->get('superadmin_login') != 1) {
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
        if (!$this->superadmin_model->event_exists($event_id)) {
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
        if (!$this->superadmin_model->school_exists($data['school_id'])) {
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
                if (!$this->superadmin_model->class_exists($participant['id'], $data['school_id'])) {
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'Classe invalide pour cette Ã©cole : ' . $participant['id'],
                        'csrf' => $csrf
                    ]);
                    return;
                }
            } elseif ($participant['type'] === 'individual') {
                if (!$this->superadmin_model->user_exists($participant['id'])) {
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
            $this->superadmin_model->update_table('event_calendars', ['id' => $event_id], $data);
            // Mettre Ã  jour les rendez-vous associÃ©s
            $this->superadmin_model->update_table('appointments', ['event_id' => $event_id], [
                'title' => $data['title'],
                'start_date' => $data['starting_date'],
                'school_id' => $data['school_id'],
                'visio' => $data['visio']
            ]);
			 $this->superadmin_model->delete_table('participants', ['event_id' => $event_id]);
            foreach ($participants as $participant) {
                $this->superadmin_model->insert_table('participants', [
                    'event_id' => $event_id,
                    'guest' => $participant['id'],
                    'type' => $participant['type'],
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            }
            echo json_encode([
                'status' => 'success',
                'message' => 'Event successfully updated',
                'csrf' => $csrf,
                'event_data' => $data,
				'participants' => $participants
            ]);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => get_phrase('Error updating event : ') . $e->getMessage(), 'csrf' => $csrf]);
        }
    }

    public function delete_event() {
        if (session()->get('superadmin_login') != 1) {
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
            exit;
        }

        $event_id = filter_var($this->request->getPost('id'), FILTER_VALIDATE_INT);
        if (!$event_id) {
            echo json_encode(['status' => 'error', 'message' => 'Event ID is required']);
            exit;
        }

        // VÃ©rifier si l'Ã©vÃ©nement existe
        if (!$this->superadmin_model->event_exists($event_id)) {
            echo json_encode(['status' => 'error', 'message' => get_phrase('Event not found')]);
            exit;
        }

        try {
            $appointments = $this->superadmin_model->get_appointments_by_event($event_id);
            foreach ($appointments as $appointment) {
                $existing_meeting = $this->superadmin_model->get_session_meeting_by_appointment($appointment['id']);
                if ($existing_meeting && $existing_meeting['meeting_id']) {
                    $bbb_config = config('bigbluebutton');
                    $bbb_url = $bbb_config->bbb_url ?? '';
                    $bbb_secret = $bbb_config->bbb_secret ?? '';
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

            $this->superadmin_model->delete_table('appointments', ['event_id' => $event_id]);

			$this->superadmin_model->delete_table('participants', ['event_id' => $event_id]);

            $this->superadmin_model->delete_table('event_calendars', ['id' => $event_id]);

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
            echo json_encode(['status' => 'error', 'message' => get_phrase('Error deleting event : ') . $e->getMessage()]);
        }
    }

    public function get_events() {
    // VÃ©rifier l'authentification de l'utilisateur
    if (session()->get('superadmin_login') != 1) {
        $csrf = [
            'csrfName' => csrf_token(),
            'csrfHash' => csrf_hash(),
        ];
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized', 'csrf' => $csrf]);
        exit;
    }

    // RÃ©cupÃ©rer l'ID de l'Ã©cole
    $user_id = session()->get('user_id');
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

    // RÃ©cupÃ©rer les Ã©vÃ©nements
    $sql = "SELECT event_calendars.*, schools.name as school_name, users.name as created_by_name 
            FROM event_calendars 
            LEFT JOIN schools ON event_calendars.school_id = schools.id 
            LEFT JOIN users ON event_calendars.created_by = users.id 
            WHERE event_calendars.school_id = ?";
    $params = [$school_id];

    if ($event_id) {
        $sql .= " AND event_calendars.id = ?";
        $params[] = $event_id;
    } else {
        $sql .= " AND (event_calendars.starting_date <= ? AND (event_calendars.ending_date >= ? OR event_calendars.ending_date IS NULL))";
        $params[] = $end_date;
        $params[] = $start_date;
    }

    if ($class_id) {
        $sql .= " LEFT JOIN participants ON event_calendars.id = participants.event_id WHERE participants.guest = ? AND participants.type = 'class'";
        $params[] = $class_id;
    }

    $events = $this->superadmin_model->get_by_query($sql, $params);

    $participantsByEventId = [];
    $allParticipantsRows = [];
    if ($events !== []) {
        $allParticipantsRows = $this->superadmin_model->get_participants_for_events_batch(array_column($events, 'id'));
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
    $classMap = $this->superadmin_model->get_classes_map_by_ids($batchClassIds);
    $userMap = $this->superadmin_model->get_users_map_by_ids($batchUserIds);

    // Charger la configuration BigBlueButton
    $bbb_config = config('bigbluebutton');
    $bbb_url = $bbb_config->bbb_url ?? '';
    $bbb_secret = $bbb_config->bbb_secret ?? '';

    // Post-traitement des Ã©vÃ©nements
    $processed_events = [];
	$now = new DateTime('now', new DateTimeZone('UTC'));
    $threshold = (clone $now)->modify('-24 hours');
    foreach ($events as $event) {
        $event['school_name'] = $event['school_name'] ?? '';
        $event['created_by_name'] = $event['created_by_name'] ?? 'Unknown';
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

        // Check if the event has expired
        $event_start = DateTime::createFromFormat('Y-m-d H:i:s', $event['starting_date'] . ' ' . $event['starting_time'], new DateTimeZone('UTC'));
        $event['is_expired'] = $event['recurrence_type'] === 'does_not_repeat' && $event_start < $threshold;

        // Initialize occurrences array
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
            $appointments = $this->superadmin_model->get_by_query($sql, [$event['id'], $start_date, $end_date]);

            foreach ($appointments as $appointment) {
                $occurrence_date = (new DateTime($appointment['start_date']))->format('Y-m-d');
                $meeting_id = $appointment['meeting_id'] ?? null;

                // Check meeting status via BBB API
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

public function start_meeting(){
    // VÃ©rifier l'authentification
    if (session()->get('superadmin_login') != 1) {
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
    $event = $this->superadmin_model->get_event_by_id($event_id);
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
    $this->superadmin_model->update_table('appointments', ['event_id' => $event_id, 'DATE(start_date) !=' => $occurrence_date, 'Etat' => 1], ['Etat' => 0]);

    // VÃ©rifier si un appointment actif existe pour cette occurrence
    $appointment = $this->superadmin_model->get_row('appointments', ['event_id' => $event_id, 'DATE(start_date)' => $occurrence_date, 'Etat' => 1]);
    $appointment_id = $appointment ? $appointment['id'] : null;

    // Charger la configuration BigBlueButton
    $bbb_config = config('bigbluebutton');
    $bbb_url = $bbb_config->bbb_url ?? '';
    $bbb_secret = $bbb_config->bbb_secret ?? '';

    // Si un appointment existe et le meeting est en cours, le rejoindre
    if ($appointment && $appointment['meeting_id']) {
        $meeting = $this->superadmin_model->get_session_meeting($appointment['meeting_id'], $appointment_id);
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
                $full_name = urlencode($user_details['name'] ?? 'Superadmin-' . rand(1000, 9999));
                $join_params = "fullName=$full_name&meetingID=" . urlencode($appointment['meeting_id']) . "&password=" . $meeting['moderator_pw'] . "&redirect=true";
                $join_checksum = sha1("join" . $join_params . $bbb_secret);
                $join_url = $bbb_url . "join?" . $join_params . "&checksum=" . $join_checksum;

				// Retrieve associated participants
                $participants = $this->superadmin_model->get_where_result('appointment_participants', ['appointment_id' => $appointment_id]);

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
                $this->superadmin_model->update_table('appointments', ['id' => $appointment_id], ['Etat' => 0]);
            }
        }
    }

    // Retrieve all participants (classes and users) for the event
    $participants = $this->superadmin_model->get_where_result('participants', ['event_id' => $event_id]);
    $appointment_data = [
        'event_id' => $event_id,
        'title' => $event['title'],
        'start_date' => $occurrence_date . ' ' . $event['starting_time'],
        'school_id' => $event['school_id'],
        'visio' => $event['visio'],
        'Etat' => 1,
        'meeting_id' => null
    ];
    $appointment_id = $this->superadmin_model->insert_table('appointments', $appointment_data);

	foreach ($participants as $participant) {
        $this->superadmin_model->insert_table('appointment_participants', [
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
        $this->superadmin_model->insert_table('sessions_meetings', $meeting_data);

        // Mettre Ã  jour l'appointment avec le nouveau meeting_id
        $this->superadmin_model->update_table('appointments', ['id' => $appointment_id], ['meeting_id' => $new_meeting_id]);

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

        // Generate Join URL
        $user_details = $this->user_model->get_user_details(session()->get('user_id'));
        $full_name = urlencode($user_details['name'] ?? 'Superadmin-' . rand(1000, 9999));
        $join_params = "fullName=$full_name&meetingID=" . urlencode($new_meeting_id) . "&password=$moderator_password&redirect=true";
        $join_checksum = sha1("join" . $join_params . $bbb_secret);
        $join_url = $bbb_url . "join?" . $join_params . "&checksum=" . $join_checksum;

		// Retrieve associated participants
        $participants = $this->superadmin_model->get_where_result('appointment_participants', ['appointment_id' => $appointment_id]);

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

public function get_school_data() {
    if (session()->get('superadmin_login') != 1) {
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
    if (!$school_id || !$this->superadmin_model->school_exists($school_id)) {
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
        $classes = $this->superadmin_model->get_where_result('classes', ['school_id' => $school_id]);

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
        $sql = "SELECT DISTINCT(u.id), u.name, 'student' as type, u.role, u.status as user_status, s.status as student_status 
                FROM users u 
                INNER JOIN students s ON s.user_id = u.id 
                INNER JOIN enrols e ON e.student_id = s.id 
                WHERE e.school_id = ? AND u.status = 1 AND s.status = 1 AND u.id != ?";
        $params = [$school_id, $current_user_id];
        
        if (!empty($class_ids)) {
            $sql .= " AND e.class_id NOT IN (" . implode(',', array_fill(0, count($class_ids), '?')) . ")";
            $params = array_merge($params, $class_ids);
        }
        $sql .= " ORDER BY u.name ASC";
        $students = $this->superadmin_model->get_by_query($sql, $params);
        $users = array_merge($users, $students);

        // 2. RÃ©cupÃ©rer les enseignants
        $sql = "SELECT DISTINCT(u.id), u.name, 'teacher' as type, u.role 
                FROM users u 
                INNER JOIN teachers t ON t.user_id = u.id 
                WHERE t.school_id = ? AND u.status = 1 AND u.id != ? 
                ORDER BY u.name ASC";
        $teachers = $this->superadmin_model->get_by_query($sql, [$school_id, $current_user_id]);
        $users = array_merge($users, $teachers);

        // 3. RÃ©cupÃ©rer les admins et superadmins
        $sql = "SELECT DISTINCT(u.id), u.name, u.role as type, u.role FROM users u WHERE u.school_id = ? AND u.status = 1 AND u.role IN ('admin', 'superadmin') ORDER BY u.name ASC";
        $admins = $this->superadmin_model->get_by_query($sql, [$school_id]);
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

  public function check_teacher_email()
  {
    $email = $this->request->getPost('email');
    $school_id = school_id();

    $user = $this->superadmin_model->get_row('users', ['email' => $email]);

    if (!$user) {
      // Email n'existe pas
      echo json_encode(['status' => 'new']);
      exit;
    }

    // VÃ©rifier si c'est dÃ©jÃ  un teacher dans cette Ã©cole
    $existing_teacher = $this->superadmin_model->get_row('user_schools', ['user_id' => $user['id'], 'school_id' => $school_id, 'role' => 'teacher']);

    if ($existing_teacher) {
      echo json_encode([
        'status' => 'exists_in_school',
        'message' => get_phrase('this_email_already_exists_as_teacher_in_this_school')
      ]);
      return;
    }

    // Email existe mais pas teacher dans cette Ã©cole â†’ on peut rÃ©utiliser le compte
    echo json_encode([
      'status' => 'exists',
      'user' => [
        'id' => $user['id'],
        'name' => html_entity_decode($user['name']),
        'email' => $user['email']
      ]
    ]);
  }

  // =====================================================
  // BILLING ENTITIES MANAGEMENT
  // =====================================================

  /**
   * Billing Entities main page
   */
  public function billing_entities($param1 = '', $param2 = '')
  {
    // Load the service
    // TODO: Replace with $this->billingEntityService = Config\Services::BillingEntityService();
    
    // Handle actions
    if ($param1 == 'create') {
      $this->_billing_entity_create();
      return;
    }
    
    if ($param1 == 'update' && !empty($param2)) {
      $this->_billing_entity_update($param2);
      return;
    }
    
    if ($param1 == 'delete' && !empty($param2)) {
      $this->_billing_entity_delete($param2);
      return;
    }
    
    if ($param1 == 'set_default' && !empty($param2)) {
      $this->_billing_entity_set_default($param2);
      return;
    }
    
    if ($param1 == 'list') {
      $this->_billing_entity_list();
      return;
    }
    
    if ($param1 == 'save_credentials' && !empty($param2)) {
      $provider = $this->request->getUri()->getSegment(4); // /superadmin/billing_entities/save_credentials/{id}/{provider}
      $this->_billing_entity_save_credentials($param2, $provider);
      return;
    }
    
    // Default: show index page
    $page_data['entities'] = $this->billingEntityService->get_for_admin();
    $page_data['mappings'] = $this->_get_billing_mappings();
    $page_data['folder_name'] = 'billing_entities';
    $page_data['page_title'] = 'Billing Entities';
    return view('backend/index', $page_data);
  }

  /**
   * Create billing entity
   */
  private function _billing_entity_create()
  {
    $data = [
      'code' => strtoupper($this->request->getPost('code')),
      'name' => $this->request->getPost('name'),
      'legal_name' => $this->request->getPost('legal_name'),
      'country_code' => strtoupper($this->request->getPost('country_code')),
      'country_name' => $this->request->getPost('country_name'),
      'country_flag' => $this->request->getPost('country_flag'),
      'vat_rate' => (float) $this->request->getPost('vat_rate'),
      'currency_code' => strtoupper($this->request->getPost('currency_code')),
      'currency_symbol' => $this->request->getPost('currency_symbol'),
      'psp_name' => $this->request->getPost('psp_name'),
      'psp_type' => $this->request->getPost('psp_type'),
      'bank_name' => $this->request->getPost('bank_name'),
      'bank_country' => $this->request->getPost('bank_country'),
      'color_primary' => $this->request->getPost('color_primary'),
      'color_secondary' => $this->request->getPost('color_secondary'),
      'display_order' => (int) $this->request->getPost('display_order'),
      'is_active' => $this->request->getPost('is_active') ? 1 : 0,
      'is_default' => $this->request->getPost('is_default') ? 1 : 0,
      'created_by' => session()->get('user_id')
    ];
    
    $result = $this->billingEntityService->create_entity($data);
    
    if ($result['success']) {
      // Create mappings if provided
      $mappings = $this->request->getPost('mappings');
      if (!empty($mappings)) {
        $this->_create_billing_mappings($result['entity_id'], $mappings);
      }
      
      echo json_encode([
        'status' => true,
        'notification' => get_phrase('Entity created successfully')
      ]);
    } else {
      echo json_encode([
        'status' => false,
        'notification' => $result['message']
      ]);
    }
  }

  /**
   * Update billing entity
   */
  private function _billing_entity_update($id)
  {
    $data = [
      'code' => strtoupper($this->request->getPost('code')),
      'name' => $this->request->getPost('name'),
      'legal_name' => $this->request->getPost('legal_name'),
      'country_code' => strtoupper($this->request->getPost('country_code')),
      'country_name' => $this->request->getPost('country_name'),
      'country_flag' => $this->request->getPost('country_flag'),
      'vat_rate' => (float) $this->request->getPost('vat_rate'),
      'currency_code' => strtoupper($this->request->getPost('currency_code')),
      'currency_symbol' => $this->request->getPost('currency_symbol'),
      'psp_name' => $this->request->getPost('psp_name'),
      'psp_type' => $this->request->getPost('psp_type'),
      'bank_name' => $this->request->getPost('bank_name'),
      'bank_country' => $this->request->getPost('bank_country'),
      'color_primary' => $this->request->getPost('color_primary'),
      'color_secondary' => $this->request->getPost('color_secondary'),
      'display_order' => (int) $this->request->getPost('display_order'),
      'is_active' => $this->request->getPost('is_active') ? 1 : 0,
      'is_default' => $this->request->getPost('is_default') ? 1 : 0
    ];
    
    $result = $this->billingEntityService->update_entity($id, $data);
    
    if ($result['success']) {
      // Update mappings if provided
      $mappings = $this->request->getPost('mappings');
      $this->_update_billing_mappings($id, $mappings);
      
      echo json_encode([
        'status' => true,
        'notification' => get_phrase('Entity updated successfully')
      ]);
    } else {
      echo json_encode([
        'status' => false,
        'notification' => $result['message']
      ]);
    }
  }

  /**
   * Delete billing entity
   */
  private function _billing_entity_delete($id)
  {
    $result = $this->billingEntityService->delete_entity($id);
    
    echo json_encode([
      'status' => $result['success'],
      'notification' => $result['success'] ? get_phrase('Entity deleted successfully') : $result['message']
    ]);
  }

  /**
   * Set default billing entity
   */
  private function _billing_entity_set_default($id)
  {
    $this->loadModel('BillingEntity_model', 'billing_entity_model');
    $result = $this->billing_entity_model->set_default($id);
    
    echo json_encode([
      'status' => $result,
      'notification' => $result ? get_phrase('Default entity updated') : get_phrase('Failed to update default entity')
    ]);
  }

  /**
   * List billing entities (AJAX)
   */
  private function _billing_entity_list()
  {
    $data['entities'] = $this->billingEntityService->get_for_admin();
    $data['mappings'] = $this->_get_billing_mappings();
    return view('backend/superadmin/billing_entities/list', $data);
  }

  /**
   * Get all billing mappings
   */
  private function _get_billing_mappings()
  {
    if (!$this->billingEntityService->is_available()) {
      return [];
    }
    
    $this->loadModel('BillingEntity_model', 'billing_entity_model');
    return $this->billing_entity_model->get_all_mappings();
  }

  /**
   * Create billing mappings from comma-separated string
   */
  private function _create_billing_mappings($entity_id, $mappings_str)
  {
    $this->loadModel('BillingEntity_model', 'billing_entity_model');
    
    $codes = array_filter(array_map('trim', explode(',', $mappings_str)));
    foreach ($codes as $code) {
      $this->billing_entity_model->create_mapping(strtoupper($code), $entity_id);
    }
  }

  /**
   * Update billing mappings
   */
  private function _update_billing_mappings($entity_id, $mappings_str)
  {
    $this->loadModel('BillingEntity_model', 'billing_entity_model');
    
    // Delete existing mappings for this entity
    $this->superadmin_model->delete_table('billing_entity_mappings', ['billing_entity_id' => $entity_id]);
    
    // Create new mappings
    if (!empty($mappings_str)) {
      $this->_create_billing_mappings($entity_id, $mappings_str);
    }
  }

  /**
   * Save credentials for a billing entity
   */
  private function _billing_entity_save_credentials($entity_id, $provider)
  {
    $this->loadModel('BillingEntityCredentials_model', 'credentials_model');
    
    $data = [
      'mode' => $this->request->getPost('mode'),
      'currency' => $this->request->getPost('currency'),
      'is_active' => $this->request->getPost('is_active') ? 1 : 0,
      'account_name' => $this->request->getPost('account_name'),
      'account_email' => $this->request->getPost('account_email'),
      'notes' => $this->request->getPost('notes')
    ];
    
    // Stripe specific fields
    if ($provider === 'stripe') {
      $data['test_public_key'] = $this->request->getPost('test_public_key');
      $data['test_secret_key'] = $this->request->getPost('test_secret_key');
      $data['live_public_key'] = $this->request->getPost('live_public_key');
      $data['live_secret_key'] = $this->request->getPost('live_secret_key');
      $data['webhook_secret'] = $this->request->getPost('webhook_secret');
    }
    
    // PayPal specific fields
    if ($provider === 'paypal') {
      $data['sandbox_client_id'] = $this->request->getPost('sandbox_client_id');
      $data['sandbox_secret'] = $this->request->getPost('sandbox_secret');
      $data['production_client_id'] = $this->request->getPost('production_client_id');
      $data['production_secret'] = $this->request->getPost('production_secret');
    }
    
    $result = $this->credentials_model->save_credentials($entity_id, $provider, $data);
    
    echo json_encode([
      'status' => $result !== false,
      'notification' => $result !== false 
        ? get_phrase('Credentials saved successfully') 
        : get_phrase('Failed to save credentials')
    ]);
  }

  // =====================================================
  // PAYMENT METHODS MANAGEMENT (SimplifiÃ©)
  // Utilise payment_settings existant + liaisons entitÃ©s
  // =====================================================
  
  /**
   * Payment Methods - Gestion des liaisons avec entitÃ©s
   */
  public function payment_methods($param1 = "", $param2 = "")
  {
    $this->loadModel('PaymentMethod_model', 'payment_method_model');
    
    switch ($param1) {
      case 'toggle_entity':
        $this->_payment_method_toggle_entity();
        return;
        
      case 'enable_all_international':
        $this->_payment_method_enable_all_international();
        return;
        
      default:
        // Show main page
        $page_data['page_title'] = 'Payment Methods';
        $page_data['folder_name'] = 'payment_methods';
        return view('backend/index', $page_data);
    }
  }
  
  /**
   * Toggle method-entity link
   */
  private function _payment_method_toggle_entity()
  {
    $method_code = $this->request->getPost('method_code');
    $entity_id = $this->request->getPost('entity_id');
    $enable = $this->request->getPost('enable') === 'true';
    
    if ($enable) {
      $result = $this->payment_method_model->link_to_entity($entity_id, $method_code);
    } else {
      $result = $this->payment_method_model->unlink_from_entity($entity_id, $method_code);
    }
    
    echo json_encode([
      'status' => $result !== false,
      'notification' => $result !== false ? get_phrase('Updated successfully') : get_phrase('Failed to update')
    ]);
  }
  
  /**
   * Enable all international methods for all entities
   */
  private function _payment_method_enable_all_international()
  {
    // TODO: Replace with $this->billingEntityService = Config\Services::BillingEntityService();
    $entities = $this->billingEntityService->get_all_active();
    
    $international_methods = ['stripe', 'paypal', 'bank_transfer'];
    
    foreach ($entities as $entity) {
      foreach ($international_methods as $method_code) {
        $this->payment_method_model->link_to_entity($entity['id'], $method_code);
      }
    }
    
    echo json_encode([
      'status' => true,
      'notification' => get_phrase('International methods enabled for all entities')
    ]);
  }
}
