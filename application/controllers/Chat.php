<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Chat extends CI_Controller {

    protected $chat_service_url;
    protected $tokenHandler;

    public function __construct()
    {
        parent::__construct();
        
        $this->load->database();
        $this->load->library('session');
        $this->load->model('User_model', 'user_model');
        $this->load->model('Settings_model', 'settings_model');
        $this->load->model('Crud_model', 'crud_model');
        
        // No backend logic allowed here
        // The chat is fully handled by chat-service
        // ❌ NE PAS : Créer de modèles CodeIgniter (Models), Gérer la base de données
        
        // Vérification de la session : on vérifie si l'ID utilisateur est présent
        // (Login.php ne définit pas 'user_login', mais définit 'user_id', 'admin_login', etc.)
        $user_id = $this->session->userdata('user_id');
        if (empty($user_id)) {
            redirect(site_url('login'), 'refresh');
        }

        // Configuration de l'API backend (URL du microservice)
        $this->chat_service_url = "https://chat.wayo.site/api/v1"; 
        
        // Load TokenHandler to generate WAP Token for Chat Service
        require_once APPPATH . '/libraries/TokenHandler.php';
        $this->tokenHandler = new TokenHandler();
    }

    public function index()
    {
        $user_id = $this->session->userdata('user_id');
        
        // Generate Token for Chat Service
        // This token allows the JS client to authenticate against chat-service
        // It creates a trust relationship: chat-service validates this token by calling back api/user
        $tokenData = ['user_id' => $user_id, 'issued_at' => time()];
        $page_data['wap_token'] = $this->tokenHandler->GenerateToken($tokenData);
        
        // Basic Page Data
        $page_data['current_user_id'] = $user_id;
        $page_data['current_user_name'] = $this->session->userdata('name'); // 'name' might not be set for all, 'user_name' is set for superadmin
        if (empty($page_data['current_user_name'])) {
             $page_data['current_user_name'] = $this->session->userdata('user_name');
        }

        $page_data['folder_name'] = 'chat';
        $page_data['page_name']  = 'index';
        $page_data['page_title'] = get_phrase('chat');
        
        // No Conversation Loading here! JS will fetch it from chat-service.
        
        $this->load->view('backend/index', $page_data);
    }
}
