<?php

namespace App\Models;

use CodeIgniter\Model;

class Room_model extends Model {
    protected $table            = 'rooms';
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

    protected $request;
    protected $session;

    public function __construct()
    {
        parent::__construct();
        $this->request = \Config\Services::request();
        $this->session = \Config\Services::session();
    }

    public function create_room()
    {
        try {
            // Récupération et décodage des données envoyées
            $roomName = html_escape($this->request->getPost('roomName'));
            $classID = html_escape($this->request->getPost('classSelect'));

            // Vérification des champs obligatoires
            if (empty($roomName) || empty($classID)) {
                return json_encode([
                    'status' => false,
                    'notification' => get_phrase('tous_les_champs_obligatoires_doivent_etre_remplis')
                ]);
            }

            $schoolID = school_id(); // Fonction pour récupérer l'ID de l'école
            $userID = session()->get('user_id');

            // Vérifier si la salle existe déjà pour cette école
            $exists = \db()->table('rooms')
                ->where('name', $roomName)
                ->where('school_id', $schoolID)
                ->get()
                ->getRow();
            if ($exists) {
                return json_encode([
                    'status' => false,
                    'notification' => get_phrase('cette_salle_existe_deja')
                ]);
            }

            // Préparation des données pour l'insertion
            $roomData = [
                'name' => $roomName,
                'school_id' => $schoolID,
                'user_id' => $userID,
                'class_id' => $classID
            ];

            // Insertion dans la base de données
            \db()->table('rooms')->insert($roomData);

            // Vérification de l'insertion
            if (\db()->affectedRows() > 0) {
                return json_encode([
                    'status' => true,
                    'notification' => get_phrase('salle_creee_avec_succes')
                ]);
            } else {
                return json_encode([
                    'status' => false,
                    'notification' => get_phrase('erreur_lors_de_la_creation_de_la_salle')
                ]);
            }
        } catch (\Throwable $e) {
            return json_encode([
                'status' => false,
                'notification' => get_phrase('erreur_serveur') . ': ' . $e->getMessage()
            ]);
        }
    }
    public function update_room($param1 = '')
    {
        $data['name'] = html_escape($this->request->getPost('roomName'));

        \db()->table('rooms')->where('id', $param1)->update($data);

        return json_encode([
            'status' => true,
            'notification' => get_phrase('room_has_been_updated_successfully')
        ]);
    }

    public function get_all_appointments() {
        $schoolID = school_id();
        $query = \db()->table('appointments')
            ->select('
                appointments.id,
                appointments.title,
                appointments.start_date AS start,
                appointments.end_date AS end,
                appointments.description,
                appointments.classe_id,
                classes.name AS class_name,
                appointments.school_id,
                schools.name AS school_name,
                appointments.recurrence_type,
                appointments.recurrence_end_date,
                appointments.custom_recurrence,
                appointments.visio,
                appointments.meeting_id
            ')
            ->join('classes', 'classes.id = appointments.classe_id', 'left')
            ->join('schools', 'schools.id = appointments.school_id', 'left')
            ->where('appointments.Etat', 1)
            ->where('appointments.school_id', $schoolID)
            ->get();
        
        $appointments = $query->getResultArray();
        $recurringAppointments = [];
        $maxRecurrenceYears = 1; // Limit recurrences to 5 years if no end date is set

        foreach ($appointments as $appointment) {
            $recurringAppointments[] = $appointment;

            if ($appointment['recurrence_type'] && $appointment['recurrence_type'] !== 'does_not_repeat') {
                try {
                    $startDate = new DateTime($appointment['start'], new DateTimeZone('UTC'));
                    $endDate = $appointment['recurrence_end_date'] ? new DateTime($appointment['recurrence_end_date'], new DateTimeZone('UTC')) : null;
                    $currentDate = clone $startDate;

                    // Set a default end date if none is provided (5 years from start)
                    if (!$endDate) {
                        $endDate = (clone $startDate)->modify("+{$maxRecurrenceYears} years");
                    }

                    // Get the original day, hour, minute, and second
                    $originalDay = (int)$startDate->format('d');
                    $originalHour = (int)$startDate->format('H');
                    $originalMinute = (int)$startDate->format('i');
                    $originalSecond = (int)$startDate->format('s');

                    // Handle daily recurrence
                    if ($appointment['recurrence_type'] === 'daily') {
                        while ($currentDate <= $endDate) {
                            if ($currentDate > $startDate) {
                                $newAppointment = $appointment;
                                $newAppointment['start'] = $currentDate->format('Y-m-d H:i:s');
                                $newAppointment['end'] = (clone $currentDate)->modify('+ ' . $this->getDuration($appointment['start'], $appointment['end']) . ' seconds')->format('Y-m-d H:i:s');
                                $newAppointment['meeting_id'] = null; // Pas de meeting_id pour les événements récurrents
                                $recurringAppointments[] = $newAppointment;
                            }
                            $currentDate->modify('+1 day');
                        }
                    }

                    // Handle weekly recurrence
                    if ($appointment['recurrence_type'] === 'weekly' && $appointment['custom_recurrence']) {
                        $days = json_decode($appointment['custom_recurrence'], true);
                        if (is_array($days)) {
                            while ($currentDate <= $endDate) {
                                $currentDay = $currentDate->format('l');
                                if (in_array($currentDay, $days) && $currentDate > $startDate) {
                                    $newAppointment = $appointment;
                                    $newAppointment['start'] = $currentDate->format('Y-m-d H:i:s');
                                    $newAppointment['end'] = (clone $currentDate)->modify('+ ' . $this->getDuration($appointment['start'], $appointment['end']) . ' seconds')->format('Y-m-d H:i:s');
                                    $newAppointment['meeting_id'] = null; // Pas de meeting_id pour les événements récurrents
                                    $recurringAppointments[] = $newAppointment;
                                }
                                $currentDate->modify('+1 day');
                            }
                        }
                    }

                    // Handle monthly recurrence
                    if ($appointment['recurrence_type'] === 'monthly') {
                        while ($currentDate <= $endDate) {
                            if ($currentDate > $startDate) {
                                $newAppointment = $appointment;
                                $newAppointment['start'] = $currentDate->format('Y-m-d H:i:s');
                                $newAppointment['end'] = (clone $currentDate)->modify('+ ' . $this->getDuration($appointment['start'], $appointment['end']) . ' seconds')->format('Y-m-d H:i:s');
                                $newAppointment['meeting_id'] = null; // Pas de meeting_id pour les événements récurrents
                                $recurringAppointments[] = $newAppointment;
                                log_message('debug', 'Monthly event generated: ' . $newAppointment['start']);
                            }
                            // Move to the next month and set the exact day
                            $currentYear = (int)$currentDate->format('Y');
                            $currentMonth = (int)$currentDate->format('m') + 1;
                            if ($currentMonth > 12) {
                                $currentMonth = 1;
                                $currentYear++;
                            }
                            // Get the number of days in the target month
                            $daysInMonth = (int)(new DateTime("$currentYear-$currentMonth-01", new DateTimeZone('UTC')))->format('t');
                            $targetDay = min($originalDay, $daysInMonth); // Use original day or last day of month
                            $currentDate->setDate($currentYear, $currentMonth, $targetDay);
                            $currentDate->setTime($originalHour, $originalMinute, $originalSecond);
                            log_message('debug', 'Next monthly date: ' . $currentDate->format('Y-m-d H:i:s') . ', Target day: ' . $targetDay);
                        }
                    }

                    // Handle yearly recurrence
                    if ($appointment['recurrence_type'] === 'yearly') {
                        while ($currentDate <= $endDate) {
                            if ($currentDate > $startDate) {
                                $newAppointment = $appointment;
                                $newAppointment['start'] = $currentDate->format('Y-m-d H:i:s');
                                $newAppointment['end'] = (clone $currentDate)->modify('+ ' . $this->getDuration($appointment['start'], $appointment['end']) . ' seconds')->format('Y-m-d H:i:s');
                                $newAppointment['meeting_id'] = null; // Pas de meeting_id pour les événements récurrents
                                $recurringAppointments[] = $newAppointment;
                            }
                            $currentDate->modify('+1 year');
                        }
                    }
                } catch (\Throwable $e) {
                    log_message('error', 'Error processing recurrence for appointment ID ' . $appointment['id'] . ': ' . $e->getMessage());
                    continue;
                }
            }
        }

        log_message('debug', 'SQL Query: ' . \db()->getLastQuery());
        log_message('debug', 'Appointments fetched: ' . json_encode($recurringAppointments));

        return $recurringAppointments;
    }

// Helper function to calculate duration in seconds
private function getDuration($start, $end) {
    try {
        $startDate = new DateTime($start, new DateTimeZone('UTC'));
        $endDate = new DateTime($end, new DateTimeZone('UTC'));
        $interval = $startDate->diff($endDate);
        return ($interval->days * 24 * 3600) + ($interval->h * 3600) + ($interval->i * 60) + $interval->s;
    } catch (Exception $e) {
        log_message('error', 'Error calculating duration: ' . $e->getMessage());
        return 3600; // Default to 1 hour if calculation fails
    }
}

    public function get_event_classes() {
    $schoolID = school_id();
    return \db()->table('appointments')
        ->select('classes.id, classes.name AS class_name')
        ->distinct()
        ->join('classes', 'classes.id = appointments.classe_id', 'inner')
        ->where('appointments.Etat', 1)
        ->where('appointments.school_id', $schoolID)
        ->get()
        ->getResultArray();
}

    public function get_all_appointments_student() {
        $user_id = session()->get('user_id');
    
        $students = \db()->table('students')->where('user_id', $user_id)->get()->getResultArray();
    
        if (empty($students)) return [];
    
        $student_ids = array_column($students, 'id');
    
        $enrolled_classes = \db()->table('enrols')->whereIn('student_id', $student_ids)->get()->getResultArray();
    
        if (empty($enrolled_classes)) return [];
    
        $class_ids   = array_column($enrolled_classes, 'class_id');
        $school_ids  = array_column($enrolled_classes, 'school_id');
        $section_ids = array_unique(array_column($enrolled_classes, 'sections_id'));
    
        $query = \db()->table('appointments')
            ->select('
                appointments.id, 
                appointments.title, 
                appointments.start_date AS start, 
                appointments.end_date AS end, 
                appointments.description, 
                appointments.classe_id, 
                appointments.room_id, 
                rooms.name,
                appointments.recurrence_type,
                appointments.recurrence_end_date,
                appointments.custom_recurrence,
                appointments.visio
            ')
            ->join('rooms', 'rooms.id = appointments.room_id', 'left')
            ->where('appointments.Etat', 1)
            ->where('rooms.Etat', 1)
            ->whereIn('appointments.classe_id', $class_ids)
            ->whereIn('rooms.school_id', $school_ids);
    
        if (!empty($section_ids)) {
            $query->groupStart();
            foreach ($section_ids as $sid) {
                $query->orWhere("FIND_IN_SET('$sid', appointments.sections_id) !=", 0);
            }
            $query->groupEnd();
        }
    
        $result = $query->get();
    
        return $result->numRows() > 0 ? $result->getResultArray() : [];
    }
    


    
    public function get_all_rooms() {
        return \db()->table('bbb_rooms')->get()->getResultArray();
    }

    public function get_room_by_id($roomID) {
        return \db()->table('bbb_rooms')->where('room_id', $roomID)->get()->getRowArray();
    }

    public function update_room_by_id($roomID) {
               	// Update Admin User Status
		$rooms['Etat'] = 0;
		$appointments ['Etat'] = 0;

		\db()->table('appointments')->where('room_id', $roomID)->update($appointments);
		return \db()->table('rooms')->where('id', $roomID)->update($rooms);
    }



        public function get_bbb_recording_by_appointment($appointment_id)
        {
            if (empty($appointment_id)) {
                return null;
            }

            // 🔎 Récupération de l'appointment et du meetingID
            $appointment = \db()->table('appointments')
                ->where('id', $appointment_id)
                ->get()
                ->getRowArray();
            if (!$appointment || empty($appointment['meeting_id'])) {
                return null;
            }

            $meetingID = $appointment['meeting_id'];

            // 📡 Construction de la requête getRecordings
            $params = ['meetingID' => $meetingID];
            $query = http_build_query($params);
            $checksum = sha1('getRecordings' . $query . $this->bbb_secret);
            $url = $this->bbb_url . 'getRecordings?' . $query . '&checksum=' . $checksum;

            // 🔁 Appel de l'API BBB
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            $response = curl_exec($ch);
            curl_close($ch);

            $xml = @simplexml_load_string($response);
            if ($xml === false || (string)$xml->returncode !== 'SUCCESS') {
                return null;
            }

            if (!isset($xml->recordings->recording)) {
                return null;
            }

            $recordings = [];

            foreach ($xml->recordings->recording as $rec) {
                if ((string)$rec->published !== 'true') {
                    continue;
                }

                $recordID = (string)$rec->recordID;
                $playback_url = '';
                $download_url = '';

                // 🔍 Recherche du format de playback
                if (isset($rec->playback->format)) {
                    foreach ($rec->playback->format as $format) {
                        $url = (string)$format->url;
                        $type = (string)$format->type;

                        // Préférence pour "presentation"
                        if ($type === 'presentation' && empty($playback_url)) {
                            $playback_url = $url;
                        }

                        // Si un format vidéo téléchargeable est détecté
                        if ($type === 'video' || str_ends_with($url, '.mp4')) {
                            $download_url = $url;
                        }
                    }
                }

                // 🛠 Correction du playback_url si mal généré
                // $parsed = parse_url($playback_url);
                // if (isset($parsed['host']) && ($parsed['host'] === 'http' || $parsed['host'] === 'playback')) {
                //     $playback_url = str_replace('http://'.$parsed['host'], $this->bbb_url_play, $playback_url);
                // }
                $parsed = parse_url($playback_url);
                if (isset($parsed['host']) && strpos($parsed['host'], '192.168.') !== false) {
                    $playback_url = str_replace($parsed['host'], $this->bbb_url_play, $playback_url);
                }

                // 📥 Fallback download_url si aucune URL MP4 trouvée
                if (empty($download_url)) {
                    $download_url = str_replace('/playback/', '/download/', $playback_url);
                }

                // 🔗 Liens HTML prêts à afficher
                $playback_link = '<a href="' . $playback_url . '" target="_blank" class="btn btn-success">📹 Voir la vidéo</a>';
                $download_link = '<a href="' . $download_url . '" target="_blank" class="btn btn-primary">⬇️ Télécharger</a>';

                $recordings[] = [
                    'recordID'            => $recordID,
                    'meetingID'           => (string)$rec->meetingID,
                    'playback_url'        => $playback_url,
                    'download_url'        => $download_url,
                    'playback_html'       => $playback_link,
                    'download_html'       => $download_link,
                    'duration'            => isset($rec->startTime, $rec->endTime)
                        ? round(((int)$rec->endTime - (int)$rec->startTime) / 60000) . ' min'
                        : '—',
                    'endTime'             => (string)$rec->endTime
                ];
            }

            return !empty($recordings) ? $recordings : null;
        }

  
          
       

            public function delete_bbb_recording_by_appointment($appointment_id)
                {
                    // if (empty($appointment_id)) {
                    //     return false;
                    // }
                   
                    // Récupération de l'appointment
                    $appointment = \db()->table('appointments')
                        ->where('id', $appointment_id)
                        ->get()
                        ->getRowArray();
                    if (!$appointment || empty($appointment['meeting_id'])) {
                        return false;
                    }
                    $meetingID = $appointment['meeting_id'];

                    // Récupération des enregistrements liés via l'API BBB
                    $params = ['meetingID' => $meetingID];
                    $query = http_build_query($params);
                    $checksum = sha1('getRecordings' . $query . $this->bbb_secret);
                    $url = $this->bbb_url . 'getRecordings?' . $query . '&checksum=' . $checksum;
                    // 🔁 Appel de l'API BBB
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $url);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                        $response = curl_exec($ch);
                        curl_close($ch);
                    // $response = file_get_contents($url);
                    $xml = @simplexml_load_string($response);
                   
                    if ($xml && $xml->returncode == 'SUCCESS' && isset($xml->recordings->recording)) {
                        foreach ($xml->recordings->recording as $rec) {
                            $recordID = (string)$rec->recordID;
                            //  die($recordID);
                            // Suppression via API
                            $deleteParams = ['recordID' => $recordID];
                            $deleteQuery = http_build_query($deleteParams);
                            $deleteChecksum = sha1('deleteRecordings' . $deleteQuery . $this->bbb_secret);
                            $deleteUrl = $this->bbb_url . 'deleteRecordings?' . $deleteQuery . '&checksum=' . $deleteChecksum;

                            // file_get_contents($deleteUrl);
                                 // 🔁 Appel de l'API BBB
                                $ch = curl_init();
                                curl_setopt($ch, CURLOPT_URL, $deleteUrl);
                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                                $response01 = curl_exec($ch);
                                curl_close($ch);
                                $xml01 = @simplexml_load_string($response01);
                                if ($xml && $xml01->returncode == 'SUCCESS') {
                                    echo "Les métadonnées de l'enregistrement {$recordID} ont été supprimées avec succès.\n";
                                } else {
                                    echo "Erreur lors de la suppression des métadonnées : " . ($xml01->messageKey ?? 'Inconnue') . "\n";
                                }
                                // var_dump($xml01);
                                // die;
                                // die($xml01->returncode);
                           
                        }
                    }

                    // Suppression de l'appointment dans la base de données
                    \db()->table('appointments')->where('id', $appointment_id)->delete();
               
                    return true;
                }


}
