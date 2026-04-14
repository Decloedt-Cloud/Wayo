<?php

namespace App\Controllers;


class Bigbluebutton extends BaseController {

    // private $bbb_url = "https://192.168.119.131/bigbluebutton/api"; // Remplace par l'URL de ton serveur BBB
    // private $bbb_secret = "mNPemKRmyLZlmVlQ4AafeUB5IBrHFYtfS5T3HU370"; // Remplace par ta clÃ© secrÃ¨te BBB



    private $bbb_url;
    private $bbb_secret;

    public function __construct()
    {
        $bbb_config = config('bigbluebutton');

            $this->crud_model = model('Crud_model');
            $this->user_model = model('User_model');
            $this->settings_model = model('Settings_model');
            $this->payment_model = model('Payment_model');
            $this->email_model = model('Email_model');
            $this->addon_model = model('Addon_model');
            $this->frontend_model = model('Frontend_model');
            $this->meeting_model = model('App\Models\Meeting_model');
            $this->participant_model = model('App\Models\Participant_model');
            $this->room_model = model('App\Models\Room_model');
        

        $this->bbb_url = $bbb_config->bbb_url ?? '';
        $this->bbb_secret = $bbb_config->bbb_secret ?? '';
    }


    public function create_and_join_meeting($role = "attendee")
    {
        if (session()->get('role') !== 'mentor' && session()->get('role') !== 'admin' && session()->get('role') !== 'superadmin') {
            echo json_encode(["status" => "error", "message" => "AccÃ¨s refusÃ©."]);
            return;
        }

        $schoolID = school_id();
        $user_ID = session()->get('user_id');
        $meetingID = "meeting-" . rand(100000, 999999);
        $meetingName = urlencode($meetingID);
        $attendeePW = "ap";
        $moderatorPW = "mp";
        $voiceBridge = rand(10000, 99999);
        $welcome = urlencode("<br>Welcome to <b>%%CONFNAME%%</b>!");
    
        // Construire la chaÃ®ne de paramÃ¨tres pour la crÃ©ation de la rÃ©union
        $params = "allowStartStopRecording=true&autoStartRecording=false";
        $params .= "&meetingID=$meetingID&name=$meetingName";
        $params .= "&attendeePW=$attendeePW&moderatorPW=$moderatorPW";
        $params .= "&record=false&voiceBridge=$voiceBridge&welcome=$welcome";
    
        // GÃ©nÃ©rer le checksum SHA1
        $checksum = sha1("create" . $params . $this->bbb_secret);
        $api_url = $this->bbb_url . "create?" . $params . "&checksum=" . $checksum;
    
        // Utiliser cURL pour envoyer la requÃªte
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $response = curl_exec($ch);
        curl_close($ch);
    
        if (strpos($response, "<returncode>SUCCESS</returncode>") !== false) {
            
            $this->Meeting_model->save_meeting($meetingID, $meetingName, $attendeePW, $moderatorPW, $schoolID, $user_ID);

            $fullName = "User-" . rand(1000, 9999);
            $password = ($role === "moderator") ? $moderatorPW : $attendeePW;
    
            $join_params = "fullName=" . urlencode($fullName) . "&meetingID=$meetingID&password=$password&redirect=true";
            $join_checksum = sha1("join" . $join_params . $this->bbb_secret);
            $join_url = $this->bbb_url . "join?" . $join_params . "&checksum=" . $join_checksum;
    
            header("Location: " . $join_url);
            exit();
        } else {
            echo json_encode(["status" => "error", "message" => "Ã‰chec de la crÃ©ation de la rÃ©union"]);
        }
    }

    // public function create_room()
    // {
    //     try {
    //         // die('room_name');
    //         // die($this->request->getPost('classSelect'));
    //         // RÃ©cupÃ©ration et dÃ©codage des donnÃ©es JSON envoyÃ©es
    //         $roomName = html_escape($this->request->getPost('roomName'));
    //         $classID = html_escape($this->request->getPost('classSelect'));
    //         $description = !empty($this->request->getPost('description')) ? htmlspecialchars($this->request->getPost('description')) : null;

    
    //         // VÃ©rification des champs obligatoires
    //         if (empty($roomName) || empty($classID) || empty($description) ) {
    //             echo json_encode(["status" => "error", "message" => "Tous les champs obligatoires doivent Ãªtre remplis."]);
    //             return;
    //         }
    


    //         $schoolID = school_id(); // Fonction pour rÃ©cupÃ©rer l'ID de l'Ã©cole
    //         $userID = session()->get('user_id');
    

    
    //         // VÃ©rifier si la salle existe dÃ©jÃ  pour cette Ã©cole
    //         $exists = $this->db_helper_model->get('rooms', ['name' => $roomName, 'school_id' => $schoolID], 'row');
    //         if ($exists) {
    //             echo json_encode(["status" => "error", "message" => "Cette salle existe dÃ©jÃ ."]);
    //             return;
    //         }
    
    //         // PrÃ©paration des donnÃ©es pour l'insertion
    //         $roomData = [
    //             'name' => $roomName,
    //             'description' => $description,
    //             'school_id' => $schoolID,
    //             'user_id' => $userID,
    //             'class_id' => $classID
    //         ];
    
    //         // Insertion dans la base de donnÃ©es
    //         $this->db->insert('rooms', $roomData);
    
    //         // VÃ©rification de l'insertion
    //         if ($this->db->affected_rows() > 0) {
    //             echo json_encode(["status" => "success", "message" => "Salle crÃ©Ã©e avec succÃ¨s"]);

    //             // if ($param1 == 'list') {
    //             // }
    //         } else {
    //             echo json_encode(["status" => "error", "message" => "Erreur lors de la crÃ©ation de la salle."]);
    //         }
    //     } catch (Exception $e) {
    //         echo json_encode(["status" => "error", "message" => "Erreur serveur : " . $e->getMessage()]);
    //     }
    // }
    

 
    // CrÃ©er une nouvelle session BigBlueButton
    public function create_meeting($roomID,$appointment_id) {


        $schoolID = school_id();

        
        
        $room = $this->db_helper_model->get_by_id('rooms', $roomID);

        $user_ID = session()->get('user_id');
        $meetingID = "meeting-" . rand(100000, 999999);
         $meetingName = urlencode($meetingID);
        $attendeePW = "ap";
        $moderatorPW = "mp";
        $voiceBridge = rand(10000, 99999);
        $welcome = urlencode("<br>Welcome to <b>%%CONFNAME%%</b>!");
        $endWhenNoModerator = "false"; // Ne pas terminer si l'hÃ´te quitte
        $meetingKeepEvents = "true"; // Conserver l'Ã©tat de la rÃ©union
      
        $params = "allowStartStopRecording=true&autoStartRecording=false";
        $params .= "&meetingID=$meetingID&name=$meetingName";
        $params .= "&attendeePW=$attendeePW&moderatorPW=$moderatorPW";
        $params .= "&record=true&voiceBridge=$voiceBridge&welcome=$welcome";
        $params .= "&endWhenNoModerator=$endWhenNoModerator"; // NE PAS FERMER SI LE MODÃ‰RATEUR QUITTE
        $params .= "&meetingKeepEvents=$meetingKeepEvents"; // GARDER L'Ã‰TAT DE LA RÃ‰UNION    
        $params .= "&meta_roomID=" . $roomID;
        // GÃ©nÃ©rer le checksum SHA1
        $checksum = sha1("create" . $params . $this->bbb_secret);
        $api_url = $this->bbb_url . "create?" . $params . "&checksum=" . $checksum;
        // echo "URL de crÃ©ation : " . $api_url;
        // die();
        
    
        // Utiliser cURL pour envoyer la requÃªte
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $response = curl_exec($ch);
        curl_close($ch);
        // var_dump($response);die;
        if (strpos($response, "<returncode>SUCCESS</returncode>") !== false) {



            $this->Meeting_model->save_meeting($meetingID, $meetingName, $attendeePW, $moderatorPW, $schoolID, $user_ID,$room['classe_id'],$room['id']);

                $data['meeting_id']   = $meetingID;

                $this->Meeting_model->update('appointments', $data, ['id' => $appointment_id]);
                $page_data['folder_name'] = 'bigbleubutton';
              
                $page_data['page_title'] = 'DÃ©marrer RÃ©union';
                $user_details = $this->user_model->get_user_details(session()->get('user_id'));
                $fullName = $user_details['name'];
                $password = $moderatorPW ;
        
                $join_params = "fullName=" . urlencode($fullName) . "&meetingID=$meetingID&password=$password&redirect=true";
                $join_checksum = sha1("join" . $join_params . $this->bbb_secret);
                $join_url = $this->bbb_url . "join?" . $join_params . "&checksum=" . $join_checksum;
        
                header("Location: " . $join_url);
                exit();
              
        
              
        } else {
            echo json_encode(["status" => "error", "message" => "Erreur lors de la crÃ©ation de la rÃ©union."]);
        }
    }
  
    
    
    

    public function room_has_active_meeting55($roomID)
    {
        // RÃ©cupÃ©rer l'ID de la derniÃ¨re rÃ©union crÃ©Ã©e pour cette Room
        $meeting = $this->Meeting_model->get_ordered_row('sessions_meetings', 'id', 'DESC', ['class_id' => $roomID]);

        if (!$meeting) {
            return false; // Pas de rÃ©union trouvÃ©e
        }

        $meetingID = $meeting->meeting_id;

        // VÃ©rifier si la rÃ©union est active sur BigBlueButton
        $checksum = sha1("getMeetings" . $this->bbb_secret);
        $api_url = $this->bbb_url . "getMeetings?checksum=" . $checksum;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $response = curl_exec($ch);
        curl_close($ch);

        $xml = simplexml_load_string($response);

        if ($xml->returncode == "SUCCESS") {
            foreach ($xml->meetings->meeting as $activeMeeting) {
                if ((string) $activeMeeting->meetingID === $meetingID) {
                    return true; // La rÃ©union existe et est active
                }
            }
        }

        return false; // La rÃ©union n'est pas active
    }

    public function start_meeting($roomID)
    {
        // RÃ©cupÃ©rer l'ID de l'appointment depuis les paramÃ¨tres GET
            $appointment_id = $this->request->getGet('appointment_id');
        // VÃ©rifier si la salle a dÃ©jÃ  une rÃ©union active
        if ($this->room_has_active_meeting($roomID)) {
            echo json_encode(["status" => "error", "message" => "Une rÃ©union est dÃ©jÃ  en cours pour cette salle."]);
            return;
        }
        

        // Si aucune rÃ©union active, crÃ©er une nouvelle rÃ©union
        $this->create_meeting($roomID,$appointment_id);
    }


    public function check_active_meetings()
{
    // Appel de l'API BigBlueButton pour obtenir la liste des rÃ©unions actives
    $checksum = sha1("getMeetings" . $this->bbb_secret);
    $api_url = $this->bbb_url . "getMeetings?checksum=" . $checksum;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    $response = curl_exec($ch);
    curl_close($ch);

    // Convertir la rÃ©ponse XML en tableau PHP
    $xml = simplexml_load_string($response);
    $json = json_encode($xml);
    $array = json_decode($json, true);

    // VÃ©rifier si l'API retourne un succÃ¨s et qu'il y a des rÃ©unions actives
    $activeMeetings = [];
    if (isset($array['meetings']['meeting'])) {
        $meetings = is_array($array['meetings']['meeting']) ? $array['meetings']['meeting'] : [$array['meetings']['meeting']];
        foreach ($meetings as $meeting) {
            $activeMeetings[$meeting['meetingID']] = [
                "running" => $meeting['running'] === "true",
                "participantCount" => $meeting['participantCount'] ?? 0
            ];
        }
    }

    // Retourner la liste des rÃ©unions actives
    echo json_encode(["status" => "success", "meetings" => $activeMeetings]);
}

    public function is_meeting_running($meetingID)
    {
        $checksum = sha1("isMeetingRunningmeetingID=" . $meetingID . $this->bbb_secret);
        $api_url = $this->bbb_url . "isMeetingRunning?meetingID=" . $meetingID . "&checksum=" . $checksum;
    
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        curl_close($ch);
    
        // âœ… Ajout de logs pour voir la rÃ©ponse brute
        header("Content-Type: application/json");
        echo json_encode(["meetingID" => $meetingID, "response_raw" => $response]);
        exit();
    }

    


    public function get_meetings()
    {
        $checksum = sha1("getMeetings" . $this->bbb_secret);
        $api_url = $this->bbb_url . "getMeetings?checksum=" . $checksum;
    
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $response = curl_exec($ch);
        curl_close($ch);
    
        echo $response;
    }
    public function breakout_rooms($meetingID)
    {
        die;
        $data['meeting_id'] = $meetingID;
        $data['breakout_rooms'] = $this->Meeting_model->get_breakout_rooms($meetingID);
        $data['participants'] = $this->Participant_model->get_participants($meetingID);
        
        return view('breakout_rooms', $data);
    }

    // Exemple d'utilisation
    public function index() {
        $meetingID = "unique_meeting_id_123";
        $meetingName = "Ma rÃ©union de test";
        
        // CrÃ©er la rÃ©union
        if ($this->create_meeting($meetingID, $meetingName)) {
            echo "RÃ©union crÃ©Ã©e avec succÃ¨s!<br>";

            // Rejoindre la rÃ©union en tant que modÃ©rateur
            $this->join_meeting($meetingID, "ModÃ©rateur", "mp");
        } else {
            echo "Erreur lors de la crÃ©ation de la rÃ©union.";
        }
    }






    public function room_has_active_meeting($room_id)
{
    $meeting = $this->Meeting_model->get_ordered_row_array('sessions_meetings', 'created_at', 'DESC', ['room_id' => $room_id]);

    if (!$meeting) {
        return false; // Aucune rÃ©union trouvÃ©e pour cette room
    }

    // VÃ©rifier via l'API BigBlueButton si la rÃ©union est en cours
    $meetingID = $meeting['meeting_id'];
    $checksum = sha1("getMeetingInfomeetingID=$meetingID" . $this->bbb_secret);
    $api_url = $this->bbb_url . "getMeetingInfo?meetingID=$meetingID&checksum=" . $checksum;

    $response = file_get_contents($api_url);
    if (strpos($response, "<returncode>SUCCESS</returncode>") !== false && strpos($response, "<running>true</running>") !== false) {
        return true; // La rÃ©union est en cours
    }

    return false; // RÃ©union non active
}

public function get_active_meetings()
{
    $checksum = sha1("getMeetings" . $this->bbb_secret);
    $api_url = $this->bbb_url . "getMeetings?checksum=" . $checksum;
    // echo "URL de crÃ©ation : " . $api_url;
    // die();


    // $response = file_get_contents($api_url);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    $response = curl_exec($ch);
    curl_close($ch);
    $xml = simplexml_load_string($response);

    $active_meetings = [];
    if ($xml->returncode == "SUCCESS") {
        foreach ($xml->meetings->meeting as $meeting) {
            $roomID = isset($meeting->metadata->roomid) ? (string)$meeting->metadata->roomid : null;

            $active_meetings[] = [
                "meeting_id" => (string)$meeting->meetingID,
                "room_id" => $roomID,
                "running" => (string)$meeting->running,
                "participant_count" => isset($meeting->participantCount) ? (int)$meeting->participantCount : 0
                // "api_url" => $api_url
            ];
        }
    }

    echo json_encode(["active_meetings" => $active_meetings]);
}

public function join_meeting($meetingID) {
    log_message('debug', 'join_meeting - Attempting to join meeting with ID: ' . $meetingID);
    $meeting = $this->Meeting_model->get_meeting_by_id($meetingID);
    
    if (!$meeting) {
        log_message('error', 'join_meeting - Meeting not found for ID: ' . $meetingID);
        show_error("RÃ©union introuvable ou supprimÃ©e.", 404);
        return;
    }
    
    $user_details = $this->user_model->get_user_details(session()->get('user_id'));
    $password = ($user_details['role'] === "teacher" || $user_details['role'] === "admin" || $user_details['role'] === "superadmin") ? $meeting['moderator_pw'] : $meeting['attendee_pw'];
    $fullName = $user_details['name'];
    
    $params = "fullName=" . urlencode($fullName) . "&meetingID=" . urlencode($meetingID) . "&password=" . urlencode($password) . "&redirect=true";
    $checksum = sha1("join" . $params . $this->bbb_secret);
    $join_url = $this->bbb_url . "join?" . $params . "&checksum=" . $checksum;
    
    log_message('debug', 'join_meeting - Redirecting to: ' . $join_url);
    header("Location: " . $join_url);
    exit();
}

public function webhook() {
    // Verify the request is from BBB
    $bbb_config = config('bigbluebutton');
    $bbb_secret = $bbb_config->bbb_secret ?? '';
    $raw_post_data = file_get_contents('php://input');
    log_message('debug', 'Webhook - Raw data received: ' . $raw_post_data);
    $checksum = $this->request->getGet('checksum');

    // Validate checksum
    $calculated_checksum = sha1($raw_post_data . $bbb_secret);
    if ($calculated_checksum !== $checksum) {
        log_message('error', 'Webhook - Invalid checksum');
        http_response_code(401);
        echo json_encode(['status' => 'error', 'message' => 'Invalid checksum']);
        return;
    }

    // Parse the webhook data
    $data = json_decode($raw_post_data, true);
    if (!$data || !isset($data['event'])) {
        log_message('error', 'Webhook - Invalid or missing event data');
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Invalid event data']);
        return;
    }

    $event_type = $data['event'];
    $meeting_id = $data['data']['meeting']['meeting-id'] ?? null;
    if (!$meeting_id) {
        log_message('error', 'Webhook - Missing meeting_id in event data');
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Missing meeting_id']);
        return;
    }

    // Initialize participant count from Socket.IO server (optional, for consistency)
    // We rely on Socket.IO to maintain the count, so we don't fetch from DB
    $participant_count = 0; // Will be updated by Socket.IO server
    $is_running = true;

    // Handle specific event types
    switch ($event_type) {
    case 'user-joined':
        $participant_count = 1;
        log_message('debug', 'Webhook - User joined meeting_id: ' . $meeting_id);
        break;
    case 'user-left':
        $participant_count = -1;
        log_message('debug', 'Webhook - User left meeting_id: ' . $meeting_id);
        break;
    case 'meeting-ended':
        $participant_count = 0;
        $is_running = false;
        log_message('debug', 'Webhook - Meeting ended for meeting_id: ' . $meeting_id);
        break;
    default:
        log_message('debug', 'Webhook - Unhandled event type: ' . $event_type);
        http_response_code(200);
        echo json_encode(['status' => 'success', 'message' => 'Event received but not processed']);
        return;
}

    // Notify Socket.IO server
    $this->notify_socket_server($meeting_id, $event_type, $participant_count, $is_running);

    http_response_code(200);
    echo json_encode(['status' => 'success', 'message' => 'Webhook processed']);
}

/**
 * Notify the Socket.IO server of webhook events
 * @param string $meeting_id
 * @param string $event_type
 * @param int $participant_change (positive for join, negative for leave, 0 for end)
 * @param bool $is_running
 */
private function notify_socket_server($meeting_id, $event_type, $participant_change, $is_running) {
    $socket_io_url = 'https://preprod.wayo.site/notify';
    $data = [
        'meetingID' => $meeting_id,
        'eventType' => $event_type,
        'participantChange' => $participant_change,
        'isRunning' => $is_running
    ];
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $socket_io_url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    $response = curl_exec($ch);
    $curl_error = curl_error($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    log_message('debug', 'notify_socket_server - Sent data: ' . json_encode($data));
    log_message('debug', 'notify_socket_server - HTTP Code: ' . $http_code);
    log_message('debug', 'notify_socket_server - Response: ' . $response);
    if ($curl_error) {
        log_message('error', 'notify_socket_server - cURL error for meeting_id: ' . $meeting_id . ': ' . $curl_error);
    }
}

public function meeting_states()
{
    $csrfName = csrf_token();
    $csrfHash = csrf_hash();

    // Case 1: Handle POST request with meetingIDs
    $meetingIDs = $this->request->getPost('meetingIDs');
    if (is_array($meetingIDs) && !empty($meetingIDs)) {
        $results = [];

        foreach ($meetingIDs as $meetingID) {
            $meeting = $this->Meeting_model->get_meeting_by_id($meetingID);
            if (!$meeting) {
                log_message('error', 'meeting_states - Meeting not found for meetingID: ' . $meetingID);
                $results[] = [
                    'meeting_id' => $meetingID,
                    'status' => 'error',
                    'message' => 'Meeting not found'
                ];
                continue;
            }

            $params = "meetingID=" . urlencode($meetingID);
            $checksum = sha1("getMeetingInfo" . $params . $this->bbb_secret);
            $api_url = $this->bbb_url . "getMeetingInfo?" . $params . "&checksum=" . $checksum;

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $api_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            $response = curl_exec($ch);
            $curl_error = curl_error($ch);
            curl_close($ch);

            if ($curl_error) {
                log_message('error', 'meeting_states - cURL error for meetingID: ' . $meetingID . ': ' . $curl_error);
                $results[] = [
                    'meeting_id' => $meetingID,
                    'status' => 'error',
                    'message' => 'Failed to fetch meeting state due to server error'
                ];
                continue;
            }

            $xml = simplexml_load_string($response);
            if ($xml === false || !isset($xml->returncode)) {
                log_message('error', 'meeting_states - Invalid XML response for meetingID: ' . $meetingID . ': ' . $response);
                $results[] = [
                    'meeting_id' => $meetingID,
                    'status' => 'error',
                    'message' => 'Invalid response from BBB server'
                ];
                continue;
            }

            if ((string)$xml->returncode === "SUCCESS") {
                $is_running = (string)$xml->running === "true";
                $participant_count = (int)$xml->participantCount;

                log_message('debug', 'meeting_states - Meeting state for meetingID: ' . $meetingID . ', isRunning: ' . ($is_running ? 'true' : 'false') . ', participantCount: ' . $participant_count);

                $results[] = [
                    'meeting_id' => $meetingID,
                    'status' => 'success',
                    'participant_count' => $participant_count,
                    'is_running' => $is_running
                ];
            } else {
                log_message('error', 'meeting_states - BBB API error for meetingID: ' . $meetingID . ': ' . (string)$xml->message);
                $results[] = [
                    'meeting_id' => $meetingID,
                    'status' => 'error',
                    'message' => 'Failed to fetch meeting state: ' . (string)$xml->message
                ];
            }
        }

        echo json_encode([
            'status' => 'success',
            'data' => $results,
            'csrf' => ['csrfHash' => $csrfHash]
        ]);
        return;
    }

    // Case 2: Handle GET request with event_id and occurrence_date
    $event_id = $this->request->getGet('event_id');
    $occurrence_date = $this->request->getGet('occurrence_date');

    if ($event_id && $occurrence_date) {
        // Validate occurrence_date format (e.g., 'YYYY-MM-DD')
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $occurrence_date)) {
            log_message('error', 'meeting_states - Invalid occurrence_date format: ' . $occurrence_date);
            echo json_encode([
                'status' => 'error',
                'message' => 'Invalid occurrence date format',
                'csrf' => ['csrfHash' => $csrfHash]
            ]);
            return;
        }

        // Query appointments table for matching event_id and occurrence_date
        $appointment = $this->Meeting_model->get_appointment_by_event_and_date($event_id, $occurrence_date);

        if (!$appointment || empty($appointment['meeting_id'])) {
            log_message('debug', 'meeting_states - No meeting found for event_id: ' . $event_id . ', occurrence_date: ' . $occurrence_date);
            echo json_encode([
                'status' => 'success',
                'meeting_id' => null,
                'is_running' => false,
                'participant_count' => 0,
                'csrf' => ['csrfHash' => $csrfHash]
            ]);
            return;
        }

        // Meeting exists, check its state via BBB API
        $meeting_id = $appointment['meeting_id'];
        $params = "meetingID=" . urlencode($meeting_id);
        $checksum = sha1("getMeetingInfo" . $params . $this->bbb_secret);
        $api_url = $this->bbb_url . "getMeetingInfo?" . $params . "&checksum=" . $checksum;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $response = curl_exec($ch);
        $curl_error = curl_error($ch);
        curl_close($ch);

        if ($curl_error) {
            log_message('error', 'meeting_states - cURL error for meetingID: ' . $meeting_id . ': ' . $curl_error);
            echo json_encode([
                'status' => 'error',
                'message' => 'Failed to fetch meeting state due to server error',
                'meeting_id' => $meeting_id,
                'csrf' => ['csrfHash' => $csrfHash]
            ]);
            return;
        }

        $xml = simplexml_load_string($response);
        if ($xml === false || !isset($xml->returncode)) {
            log_message('error', 'meeting_states - Invalid XML response for meetingID: ' . $meeting_id . ': ' . $response);
            echo json_encode([
                'status' => 'error',
                'message' => 'Invalid response from BBB server',
                'meeting_id' => $meeting_id,
                'csrf' => ['csrfHash' => $csrfHash]
            ]);
            return;
        }

        if ((string)$xml->returncode === "SUCCESS") {
            $is_running = (string)$xml->running === "true";
            $participant_count = (int)$xml->participantCount;

            log_message('debug', 'meeting_states - Meeting state for meetingID: ' . $meeting_id . ', isRunning: ' . ($is_running ? 'true' : 'false') . ', participantCount: ' . $participant_count);

            echo json_encode([
                'status' => 'success',
                'meeting_id' => $meeting_id,
                'is_running' => $is_running,
                'participant_count' => $participant_count,
                'csrf' => ['csrfHash' => $csrfHash]
            ]);
        } else {
            log_message('error', 'meeting_states - BBB API error for meetingID: ' . $meeting_id . ': ' . (string)$xml->message);
            echo json_encode([
                'status' => 'error',
                'message' => 'Failed to fetch meeting state: ' . (string)$xml->message,
                'meeting_id' => $meeting_id,
                'csrf' => ['csrfHash' => $csrfHash]
            ]);
        }
        return;
    }

    // Case 3: Invalid parameters
    log_message('error', 'meeting_states - Missing or invalid parameters');
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid parameters: either meetingIDs or event_id with occurrence_date required',
        'csrf' => ['csrfHash' => $csrfHash]
    ]);
}
}
