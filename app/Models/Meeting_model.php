<?php

namespace App\Models;

use CodeIgniter\Model;

class Meeting_model extends Model {
    protected $table            = 'meetings';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = [];
    protected $useTimestamps    = false;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $deletedField    = 'deleted_at';
    protected $dateFormat       = 'datetime';

    protected $DBGroup = 'default';

    public function __construct()
    {
        parent::__construct();
    }

    public function save_meeting($meetingID, $meetingName, $attendeePW, $moderatorPW, $schoolID, $user_ID, $classID, $appointment_id) {
    // Check if a meeting already exists for this appointment
    $existing_meeting = \db()->table('sessions_meetings')
        ->where('appointment_id', $appointment_id)
        ->where('meeting_id', $meetingID)
        ->get()
        ->getRowArray();
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
    
    return \db()->table('sessions_meetings')->insert($data);
}

    public function get_meetings_by_school($schoolID)
    {
        return \db()->table('sessions_meetings')
            ->where('school_id', $schoolID)
            ->get()
            ->getResultArray();
    }

    public function get_meeting_by_id($meetingID)
    {
        return \db()->table('sessions_meetings')
            ->where('meeting_id', $meetingID)
            ->get()
            ->getRowArray();
    }
    public function save_participant($meetingID, $userID, $username, $role) {
        $data = [
            'meeting_id' => $meetingID,
            'user_id' => $userID,
            'username' => $username,
            'role' => $role,
            'created_at' => date("Y-m-d H:i:s")
        ];
        return \db()->table('participants')->insert($data);
    }

     public function get_bbb_recording_by_appointment($appointment_id)
    {
        if (empty($appointment_id)) {
            return null;
        }
    
        // 🔎 On récupère le meetingID lié à l'appointment
        $appointment = \db()->table('appointments')
            ->where('id', $appointment_id)
            ->get()
            ->getRowArray();
    
        if (!$appointment || empty($appointment['meeting_id'])) {
            return null;
        }
    
        $meetingID = $appointment['meeting_id'];
    
        // 🔐 Préparation de la requête BBB
        $params = ['meetingID' => $meetingID];
        $query = http_build_query($params);
    
        $bbb_url = config('Bigbluebutton')->bbb_url;
        $bbb_secret = config('Bigbluebutton')->bbb_secret;
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

    public function update_table($table, $data, $where) {
        return \db()->table($table)->where($where)->update($data);
    }

    public function get_row($table, $where) {
        return \db()->table($table)->where($where)->get()->getRowArray();
    }

    public function get($table, $where = null) {
        if ($where) {
            return \db()->table($table)->where($where)->get();
        }
        return \db()->table($table)->get()->getResultArray();
    }

    public function get_ordered_row($table, $order_by, $order_dir = 'DESC', $where = null) {
        $builder = \db()->table($table);
        if ($where) {
            $builder->where($where);
        }
        return $builder->orderBy($order_by, $order_dir)->get()->getRow();
    }

    public function get_ordered_row_array($table, $order_by, $order_dir = 'DESC', $where = null) {
        $builder = \db()->table($table);
        if ($where) {
            $builder->where($where);
        }
        return $builder->orderBy($order_by, $order_dir)->get()->getRowArray();
    }

    public function get_appointment_by_event_and_date($event_id, $occurrence_date) {
        return \db()->table('appointments')
            ->where('event_id', $event_id)
            ->where('DATE(start_date)', $occurrence_date)
            ->where('Etat', 1)
            ->get()
            ->getRowArray();
    }

}
