<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Meeting_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function save_meeting($meetingID, $meetingName, $attendeePW, $moderatorPW, $schoolID, $user_ID, $classID, $appointment_id) {
    // Check if a meeting already exists for this appointment
    $existing_meeting = $this->db->get_where('sessions_meetings', ['appointment_id' => $appointment_id, 'meeting_id' => $meetingID])->row_array();
    if ($existing_meeting) {
        log_message('debug', 'save_meeting - Meeting already exists for appointment_id ' . $appointment_id . ' and meeting_id ' . $meetingID);
        return true; // Meeting already exists, no need to insert
    }

    $data = [
        'meeting_id' => $meetingID,
        'name' => $meetingName,
        'attendee_pw' => $attendeePW,
        'moderator_pw' => $moderatorPW,
        'school_id' => $schoolID,
        'user_id' => $user_ID,
        'class_id' => $classID,
        'appointment_id' => $appointment_id,
        'created_at' => date('Y-m-d H:i:s')
    ];
    
    return $this->db->insert('sessions_meetings', $data);
}

    public function get_meetings_by_school($schoolID)
    {
        return $this->db->get_where('sessions_meetings', ['school_id' => $schoolID])->result_array();
    }

    public function get_meeting_by_id($meetingID)
    {
        return $this->db->get_where('sessions_meetings', ['meeting_id' => $meetingID])->row_array();
    }
    public function save_participant($meetingID, $userID, $username, $role) {
        $data = [
            'meeting_id' => $meetingID,
            'user_id' => $userID,
            'username' => $username,
            'role' => $role,
            'created_at' => date("Y-m-d H:i:s")
        ];
        return $this->db->insert('participants', $data);
    }

     public function get_bbb_recording_by_appointment($appointment_id)
    {
        die('fettah');
        if (empty($appointment_id)) {
            return null;
        }
    
        // 🔎 On récupère le meetingID lié à l'appointment
        $appointment = $this->db->get_where('appointments', ['id' => $appointment_id])->row_array();
    
        if (!$appointment || empty($appointment['meeting_id'])) {
            return null;
        }
    
        $meetingID = $appointment['meeting_id'];
    
        // 🔐 Préparation de la requête BBB
        $params = ['meetingID' => $meetingID];
        $query = http_build_query($params);
    
        $bbb_url = $this->config->item('bbb_url');
        $bbb_secret = $this->config->item('bbb_secret');
        $checksum = sha1('getRecordings' . $query . $bbb_secret);
        $url = $bbb_url . 'getRecordings?' . $query . '&checksum=' . $checksum;
    
        $xml = @simplexml_load_file($url);
        if ($xml === false || (string)$xml->returncode !== 'SUCCESS') {
            return null;
        }
    
        if (!isset($xml->recordings->recording)) {
            return null;
        }
    
        $recordings = [];
    
        foreach ($xml->recordings->recording as $rec) {
            if (isset($rec->published) && (string)$rec->published !== 'true') {
                continue;
            }
    
            $playback_url = '';
            if (isset($rec->playback->format)) {
                foreach ($rec->playback->format as $format) {
                    if ((string)$format->type === 'presentation') {
                        $playback_url = (string)$format->url;
                        break;
                    } elseif (empty($playback_url)) {
                        $playback_url = (string)$format->url;
                    }
                }
            }
    
            $recordings[] = [
                'recordID'     => (string) $rec->recordID,
                'meetingID'    => (string) $rec->meetingID,
                'playback_url' => $playback_url,
                'duration'     => isset($rec->startTime, $rec->endTime)
                    ? round(((int)$rec->endTime - (int)$rec->startTime) / 60000) . ' min'
                    : '—'
            ];
        }
    
        return !empty($recordings) ? $recordings : null;
    }
    
}
