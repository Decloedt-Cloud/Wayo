<?php

namespace Config;

class CI3_Controller
{
    public $input;
    public $load;
    public $db;
    public $output;
    public $form_validation;
    public $session;
    public $config;
    public $email;
    public $pagination;
    public $upload;
    public $encryption;
    
    protected $CI;
    
    public function __construct()
    {
        $this->CI =& get_instance();
        
        $this->input = (object)[
            'post' => $_POST ?? [],
            'get' => $_GET ?? [],
            'server' => $_SERVER ?? [],
            'post_get' => array_merge($_POST ?? [], $_GET ?? []),
            'get_post' => array_merge($_GET ?? [], $_POST ?? []),
        ];
        
        $this->config = new \stdClass();
        $this->config->config = [];
        
        $this->output = new \stdClass();
        
        $this->session = isset($_SESSION) ? (object)$_SESSION : new \stdClass();
        
        $this->form_validation = new \stdClass();
        
        $this->db = null;
        
        $this->email = new \stdClass();
        
        $this->pagination = new \stdClass();
        
        $this->upload = new \stdClass();
        
        $this->encryption = new \stdClass();
    }
    
    public function __get($key)
    {
        if (isset($this->$key)) {
            return $this->$key;
        }
        return null;
    }
}

function &get_instance()
{
    static $instance = null;
    if ($instance === null) {
        $instance = new \stdClass();
    }
    return $instance;
}
