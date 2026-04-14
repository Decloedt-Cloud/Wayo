<?php

namespace App\Controllers;

class Bbb_webhook extends BaseController {

    private $hookToken;
    private $emitUrl;
    private $emitKey;

    public function __construct() {
        parent::__construct();
        $this->hookToken = getenv('BBB_HOOK_TOKEN') ?: 'your_hook_token_here';
        $this->emitUrl   = getenv('SOCKET_EMIT_URL') ?: 'http://localhost:3000';
        $this->emitKey   = getenv('SOCKET_EMIT_KEY') ?: 'your_key_here';
    }

    public function receive($token = null) {
        if ($token !== $this->hookToken) {
            return $this->response->setStatusCode(403);
        }

        $raw = file_get_contents('php://input');
        if (!$raw) {
            return $this->response->setStatusCode(400);
        }

        $xml = @simplexml_load_string($raw);
        if (!$xml) {
            return $this->response->setStatusCode(415);
        }

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
        if (!$meetingID) {
            return $this->response->setStatusCode(204);
        }

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

        return $this->response->setStatusCode(204);
    }
}