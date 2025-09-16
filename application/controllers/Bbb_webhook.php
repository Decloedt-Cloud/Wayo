<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Bbb_webhook extends CI_Controller {

    private $hookToken;
    private $emitUrl;
    private $emitKey;

    public function __construct() {
        parent::__construct();
        $this->config->load('bigbluebutton');
        $this->hookToken = $this->config->item('bbb_hook_token');
        $this->emitUrl   = $this->config->item('socket_emit_url');
        $this->emitKey   = $this->config->item('socket_emit_key');
    }

    public function receive($token = null) {
        if ($token !== $this->hookToken) return $this->output->set_status_header(403);

        $raw = file_get_contents('php://input');
        if (!$raw) return $this->output->set_status_header(400);

        $xml = @simplexml_load_string($raw);
        if (!$xml) return $this->output->set_status_header(415);

        // Normalisation minimale des principaux événements// BBB varie selon versions; on cherche dans plusieurs chemins
        $eventType = (string)($xml->eventname ?? $xml->event ?? 'unknown');

        $meetingID =
            (string)($xml->meeting->meetingID
            ?? $xml->data->meeting->meetingID
            ?? $xml->data->meeting->internalMeetingID
            ?? '');

        $userName =
            (string)($xml->user->name
            ?? $xml->data->user->name
            ?? '');

        $role = (string)($xml->user->role ?? $xml->data->user->role ?? '');

        // On diffuse seulement si on a un meetingID
        if (!$meetingID) return $this->output->set_status_header(204);

        // Map basique des types
        $ev = strtolower($eventType);
        if (strpos($ev, 'meetingcreated') !== false) $ev = 'meeting_started';
        elseif (strpos($ev, 'meetingended') !== false) $ev = 'meeting_ended';
        elseif (strpos($ev, 'participantjoined') !== false) $ev = 'participant_joined';
        elseif (strpos($ev, 'participantleft') !== false) $ev = 'participant_left';

        // (Optionnel) compteur participants: on peut l’ignorer pour rester “compact”
        $payload = [
            'event'     => $ev,
            'meetingID' => $meetingID,
            'userName'  => $userName,
            'role'      => $role,
            'ts'        => time()
        ];

        // Relais vers le hub Node
        $ch = curl_init($this->emitUrl);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json', 'X-API-Key: '.$this->emitKey],
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_TIMEOUT => 3        ]);
        curl_exec($ch);
        curl_close($ch);

        return $this->output->set_status_header(204);
    }
}