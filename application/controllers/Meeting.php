<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Meeting extends CI_Controller {

    private $bbbUrl;
    private $bbbSecret;
    private $appBase;

    public function __construct() {
        parent::__construct();
        $this->config->load('bigbluebutton');
        $this->bbbUrl    = $this->config->item('bbb_url');
        $this->bbbSecret = $this->config->item('bbb_secret');
        $this->appBase   = $this->config->item('app_base_url') ;
        $this->output->set_content_type('application/json');
        
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
        $token = $this->config->item('bbb_hook_token');
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
        $name = $this->input->post('name', true);
        $meetingID = $this->input->post('meetingID', true);
        $mpw = $this->input->post('moderatorPW', true) ?: 'mp';
        $apw = $this->input->post('attendeePW', true)  ?: 'ap';

        if (!$name || !$meetingID) {
            return $this->output->set_status_header(400)->set_output(json_encode(['ok'=>false,'msg'=>'Missing params']));
        }

        // 1) create meeting        
        [$ok, $data] = $this->bbbCall('create', [
            'name'        => $name,
            'meetingID'   => $meetingID,
            'moderatorPW' => $mpw,
            'attendeePW'  => $apw,
            'record'      => 'true',
            'allowStartStopRecording' => 'true'        ]);
        if (!$ok) return $this->output->set_output(json_encode(['ok'=>false,'msg'=>$data]));

        // 2) ensure webhook exists (best-effort)
        $this->ensureHookRegistered();

        return $this->output->set_output(json_encode(['ok'=>true,'msg'=>'created']));
    }

    /** GET /meeting/join-link?meetingID=...&fullName=...&role=moderator|attendee */
    public function join_link() {
        $meetingID = $this->input->get('meetingID', true);
        $fullName  = $this->input->get('fullName', true);
        $role      = $this->input->get('role', true) ?: 'attendee';
        if (!$meetingID || !$fullName) {
            return $this->output->set_status_header(400)->set_output(json_encode(['ok'=>false,'msg'=>'Missing params']));
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
        return $this->output->set_output(json_encode(['ok'=>true,'joinUrl'=>$url]));
    }
}