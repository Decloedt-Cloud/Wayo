<?php

namespace App\Controllers;

use App\Libraries\TokenHandler;

class Chat extends BaseController {

    protected $chat_service_url;
    protected $tokenHandler;

    public function __construct()
    {
        $this->user_model = model('User_model');
        $this->settings_model = model('Settings_model');
        $this->crud_model = model('Crud_model');
        
        // No backend logic allowed here
        // The chat is fully handled by chat-service
        // ❌ NE PAS : Créer de modèles CodeIgniter (Models), Gérer la base de données
        
        // Vérification de la session : on vérifie si l'ID utilisateur est présent
        // (Login.php ne définit pas 'user_login', mais définit 'user_id', 'admin_login', etc.)
        $user_id = session()->get('user_id');
        if (empty($user_id)) {
            return redirect()->to(site_url('login'));
        }

        // Configuration de l'API backend (URL du microservice)
        $this->chat_service_url = "https://wayochat.wayo.ac/api/v1"; 
        
        // Load TokenHandler to generate WAP Token for Chat Service
        $this->tokenHandler = new TokenHandler();
    }

    public function index()
    {
        $user_id = session()->get('user_id');
        
        // Generate Token for Chat Service
        // This token allows the JS client to authenticate against chat-service
        // It creates a trust relationship: chat-service validates this token by calling back api/user
        $tokenData = ['user_id' => $user_id, 'issued_at' => time()];
        $page_data['wap_token'] = $this->tokenHandler->GenerateToken($tokenData);
        
        // Basic Page Data
        $page_data['current_user_id'] = $user_id;
        $page_data['current_user_name'] = session()->get('name'); // 'name' might not be set for all, 'user_name' is set for superadmin
        if (empty($page_data['current_user_name'])) {
             $page_data['current_user_name'] = session()->get('user_name');
        }

        $page_data['folder_name'] = 'chat';
        $page_data['page_name']  = 'index';
        $page_data['page_title'] = get_phrase('chat');
        
        // No Conversation Loading here! JS will fetch it from chat-service.
        
        return view('backend/index', $page_data);
    }
}
