<?php

namespace App\Controllers;

class Meeting extends BaseController {

    private $bbbUrl;
    private $bbbSecret;
    private $appBase;

    public function __construct() {
        parent::__construct();
        $this->bbbUrl    = getenv('BBB_URL') ?: 'https://your-bbb-server.com/bigbluebutton/api';
        $this->bbbSecret = getenv('BBB_SECRET') ?: 'your_secret_here';
        $this->appBase   = getenv('APP_BASE_URL') ?: base_url();
    }

    /* ===== Helpers BBB ===== */
    private function checksum($apiCall, $qs) {
        // SHA-256 (BBB récent). Si votre instance exige SHA-1, utilisez: return sha1($apiCall.$qs.$this->bbbSecret);
        return hash('sha256', $apiCall.$qs.$this->bbbSecret);
    }
    private function bbbCall($apiCall, $params) {
        $qs  = http_build_query($params);
        $sum = $this->checksum($apiCall, $qs);
        $url = "{$this->bbbUrl}/{$apiCall}?{$qs}&checksum={$sum}";
        $resp = @file_get_contents($url);
        if ($resp === false) return [false, 'BBB unreachable', null];
        $xml = @simplexml_load_string($resp);
        if (!$xml) return [false, 'Invalid XML', $resp];
        $ok = ((string)$xml->returncode === 'SUCCESS');
        return [$ok, $ok ? $xml : ((string)($xml->message ?? 'BBB error')), $resp];
    }

    private function ensureHookRegistered() {
        // Enregistre un hook “global” (reçoit tous les events), filtrage par meetingID côté PHP.
        $token = config('Bigbluebutton')->bbb_hook_token;
        $callback = $this->appBase . '/bbb/webhook/' . rawurlencode($token);

        // getRaw=true pour avoir l’XML complet  
        [$ok, $data] = $this->bbbCall('hooks/create', [
            'callbackURL' => $callback,
            'getRaw'      => 'true'            
            // Optionnel: 'meetingID' => '...' (certaines versions supportent le filtre par meeting)
        ]);
        // On ignore l’échec silencieusement (si déjà créé ou version BBB sans hooks, rien ne casse le start).
        return $ok;
    }

    /** POST /meeting/start (name, meetingID, moderatorPW?, attendeePW?) */
    public function start() {
        $name = $this->request->getPost('name');
        $meetingID = $this->request->getPost('meetingID');
        $mpw = esc($this->request->getPost('moderatorPW')) ?? 'mp';
        $apw = esc($this->request->getPost('attendeePW')) ?? 'ap';

        if (!$name || !$meetingID) {
            return $this->response->setStatusCode(400)->setJSON(['ok' => false, 'msg' => 'Missing params']);
        }

        // 1) create meeting        
        [$ok, $data] = $this->bbbCall('create', [
            'name'        => $name,
            'meetingID'   => $meetingID,
            'moderatorPW' => $mpw,
            'attendeePW'  => $apw,
            'record'      => 'true',
            'allowStartStopRecording' => 'true'        ]);
        if (!$ok) {
            return $this->response->setJSON(['ok' => false, 'msg' => $data]);
        }

        // 2) ensure webhook exists (best-effort)
        $this->ensureHookRegistered();

        return $this->response->setJSON(['ok' => true, 'msg' => 'created']);
    }

    /** GET /meeting/join-link?meetingID=...&fullName=...&role=moderator|attendee */
    public function join_link() {
        $meetingID = $this->request->getGet('meetingID');
        $fullName  = $this->request->getGet('fullName');
        $role      = esc($this->request->getGet('role')) ?? 'attendee';
        if (!$meetingID || !$fullName) {
            return $this->response->setStatusCode(400)->setJSON(['ok' => false, 'msg' => 'Missing params']);
        }
        $password = ($role === 'moderator') ? 'mp' : 'ap';
        $apiCall  = 'join';
        $params   = [
            'fullName'  => $fullName,
            'meetingID' => $meetingID,
            'password'  => $password,
            'redirect'  => 'true'        ];
        $qs  = http_build_query($params);
        $sum = $this->checksum($apiCall, $qs);
        $url = "{$this->bbbUrl}/{$apiCall}?{$qs}&checksum={$sum}";
        return $this->response->setJSON(['ok' => true, 'joinUrl' => $url]);
    }
}