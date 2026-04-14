<?php

namespace App\View;

use CodeIgniter\View\View as BaseView;
use CodeIgniter\Config\Services;

require_once __DIR__ . '/SessionCompat.php';

class CI3_Load_Compat
{
    public function view($view, $data = [], $return = false)
    {
        $output = view($view, $data);
        if ($return) {
            return $output;
        }
        echo $output;
        return null;
    }
    
    public function library($library, $params = null, $object_name = null)
    {
        return null;
    }
    
    public function model($model, $name = '', $dbConnection = '')
    {
        return model($model);
    }
}

class CI3_Config_Compat
{
    private $config;
    
    public function __construct()
    {
        $this->config = config('App');
    }
    
    public function item($key, $value = null)
    {
        if ($key === 'enable_toasts') {
            return true;
        }
        return $this->config->$key ?? $value;
    }
    
    public function __get($name)
    {
        if ($name === 'enable_toasts') {
            return true;
        }
        if (isset($this->config->$name)) {
            return $this->config->$name;
        }
        return null;
    }
    
    public function __isset($name)
    {
        return isset($this->config->$name) || $name === 'enable_toasts';
    }
}

class CI3_Input_Compat
{
    private $request;
    
    public function __construct()
    {
        $this->request = \Config\Services::request();
    }
    
    public function server($key = null)
    {
        if ($key === null) {
            return $_SERVER;
        }
        return $_SERVER[$key] ?? null;
    }
    
    public function post($key = null)
    {
        if ($key === null) {
            return $_POST;
        }
        return $_POST[$key] ?? null;
    }
    
    public function get($key = null)
    {
        if ($key === null) {
            return $_GET;
        }
        return $_GET[$key] ?? null;
    }
}

class CI3_DB_Compat
{
    private $db;
    private $builder;
    private $tableName;
    
    public function __construct($db = null)
    {
        $this->db = $db ?? db_connect();
        $this->builder = null;
        $this->tableName = null;
    }
    
    private function getBuilder($table = null)
    {
        if ($table !== null) {
            $this->tableName = $table;
            $this->builder = $this->db->table($table);
            return $this->builder;
        }
        
        if ($this->builder === null && $this->tableName !== null) {
            $this->builder = $this->db->table($this->tableName);
            return $this->builder;
        }
        
        if ($this->builder === null) {
            $this->tableName = 'users';
            $this->builder = $this->db->table('users');
        }
        
        return $this->builder;
    }
    
    public function reset()
    {
        $this->builder = null;
        $this->tableName = null;
    }
    
    public function get_where($table, $where = [])
    {
        $this->reset();
        
        if (isset($where['_access'])) {
            $userType = \Config\Services::session()->get('user_type');
            if (empty($userType)) {
                $userType = 'superadmin';
            }
            $where[$userType . '_access'] = $where['_access'];
            unset($where['_access']);
        }
        
        $builder = $this->db->table($table);
        
        if (is_array($where) && !empty($where)) {
            foreach ($where as $key => $value) {
                $builder->where($key, $value);
            }
        }
        
        return new CI3_Result_Compat($builder->get());
    }
    
    public function table($table)
    {
        return $this->db->table($table);
    }
    
    public function where($key, $value = null)
    {
        $builder = $this->getBuilder();
        
        if (is_array($key)) {
            $builder->where($key);
        } else {
            $builder->where($key, $value);
        }
        
        return $this;
    }
    
    public function select($select)
    {
        $this->getBuilder()->select($select);
        return $this;
    }
    
    public function from($table)
    {
        return $this->db->table($table);
    }
    
    public function get($table = null)
    {
        if ($table !== null) {
            $builder = $this->db->table($table);
            return new CI3_Result_Compat($builder->get());
        }
        
        $builder = $this->getBuilder();
        return new CI3_Result_Compat($builder->get());
    }
    
    public function __call($method, $args)
    {
        if ($method === 'escape') {
            return $this->db->escapeString($args[0] ?? '');
        }
        
        if ($method === 'count_all_results') {
            return $this->getBuilder()->countAllResults(false);
        }
        
        $methodMap = [
            'order_by' => 'orderBy',
            'group_by' => 'groupBy',
            'like' => 'like',
            'or_like' => 'orLike',
            'not_like' => 'notLike',
            'or_not_like' => 'orNotLike',
            'join' => 'join',
            'limit' => 'limit',
            'offset' => 'offset',
            'having' => 'having',
            'or_having' => 'orHaving',
            'order_by' => 'orderBy',
            'where_in' => 'whereIn',
            'or_where_in' => 'orWhereIn',
            'where_not_in' => 'whereNotIn',
            'or_where_not_in' => 'orWhereNotIn',
        ];
        
        $ci3Method = $methodMap[$method] ?? $method;
        
        $builder = $this->getBuilder();
        
        if (method_exists($builder, $ci3Method)) {
            $result = call_user_func_array([$builder, $ci3Method], $args);
            
            if ($result instanceof \CodeIgniter\Database\BaseBuilder) {
                return $this;
            }
            
            return $result;
        }
        
        $result = call_user_func_array([$builder, $method], $args);
        
        if ($result instanceof \CodeIgniter\Database\BaseBuilder) {
            return $this;
        }
        
        return $result;
    }
}

class CI3_Result_Compat
{
    private $result;
    
    public function __construct($result)
    {
        $this->result = $result;
    }
    
    public function num_rows()
    {
        if ($this->result === null) {
            return 0;
        }
        return $this->result->getNumRows();
    }
    
    public function row($field = null)
    {
        if ($this->result === null) {
            return null;
        }
        if ($field === null) {
            return $this->result->getRow();
        }
        $row = $this->result->getRow();
        return $row->$field ?? null;
    }
    
    public function row_array()
    {
        if ($this->result === null) {
            return null;
        }
        return $this->result->getRowArray();
    }
    
    public function result_array()
    {
        if ($this->result === null) {
            return [];
        }
        return $this->result->getResultArray();
    }
    
    public function result()
    {
        if ($this->result === null) {
            return [];
        }
        return $this->result->getResult();
    }
    
    public function get()
    {
        return $this->result;
    }
}

#[\AllowDynamicProperties]
class View extends BaseView
{
    protected $config;
    protected $security;

    public function render(?string $view = null, ?array $options = null, ?bool $saveData = null): string
    {
        $this->config = new CI3_Config_Compat();
        
        $compat = new SecurityCompat(\CodeIgniter\Config\Services::security());
        $this->security = $compat;
        
        if (!isset($this->tempData['security'])) {
            $this->tempData['security'] = $compat;
        }
        
        if ($saveData === null) {
            $saveData = $this->saveData;
        }
        
        if ($saveData && !empty($this->data)) {
            $this->tempData = array_merge($this->tempData, $this->data);
        }
        
        if (isset($this->tempData['page_data']) && is_array($this->tempData['page_data'])) {
            $session = service('session');
            $user_id = $session->get('user_id');
            $user_type = $session->get('user_type');
            
            $this->tempData['page_data']['user_id'] = $user_id;
            $this->tempData['page_data']['user_type'] = $user_type;
            
            if ($user_id && !isset($this->tempData['page_data']['user_details'])) {
                $user_model = model('User_model');
                $user_details = $user_model->get_user_details($user_id);
                $this->tempData['page_data']['user_details'] = $user_details;
            }
        }
        
        return parent::render($view, $options, $saveData);
    }
    
    public function __get(string $name)
    {
        if ($name === 'security') {
            return new SecurityCompat(\CodeIgniter\Config\Services::security());
        }
        
        if ($name === 'session') {
            return new SessionCompat(service('session'));
        }
        
        if ($name === 'db') {
            return new CI3_DB_Compat();
        }
        
        if ($name === 'config') {
            return new CI3_Config_Compat();
        }
        
        if ($name === 'settings_model') {
            return model('Settings_model');
        }
        
        if ($name === 'load') {
            return new CI3_Load_Compat();
        }
        
        if ($name === 'config') {
            return new CI3_Config_Compat();
        }
        
        if ($name === 'input') {
            return new CI3_Input_Compat();
        }
        
        if (in_array($name, ['user_model', 'crud_model', 'settings_model', 'payment_model', 'invoice_model', 'email_model', 'addon_model', 'frontend_model', 'driver_model', 'room_model', 'db_helper_model', 'admin_model', 'lms_model'])) {
            return model(ucfirst($name));
        }
        
        return parent::__get($name);
    }
}
