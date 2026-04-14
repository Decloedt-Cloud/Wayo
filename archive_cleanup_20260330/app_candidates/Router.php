<?php

namespace App\Controllers;

use CodeIgniter\HTTP\Request;
use CodeIgniter\HTTP\Response;
use Config\CI3_Controller;

class Router extends \CodeIgniter\Controller
{
    protected $legacyControllersPath;
    protected $legacyCorePath;
    protected $defaultController = 'Home';

    public function __construct()
    {
        parent::__construct();
        $this->legacyControllersPath = dirname(__DIR__, 2) . '/application/controllers/';
        $this->legacyCorePath = dirname(__DIR__, 2) . '/application/core/';
    }

    public function _remap($method, ...$params)
    {
        $controller = strtolower($params[0] ?? $this->defaultController);
        $method = strtolower($params[1] ?? 'index');
        $args = array_slice($params, 2);
        $className = ucfirst($controller);
        $ci4ClassName = "App\\Controllers\\{$className}";

        // Prefer native CI4 controllers first, then fallback to legacy CI3 bridge.
        if (class_exists($ci4ClassName)) {
            return $this->dispatchController(new $ci4ClassName(), $method, $args);
        }

        $controllerFile = $this->legacyControllersPath . $className . '.php';

        if (!file_exists($controllerFile)) {
            return $this->response->setStatusCode(404)->setBody('Controller not found: ' . $controller);
        }

        require_once $this->legacyCorePath . 'CI_Controller.php';
        
        $helpersPath = dirname(__DIR__, 2) . '/application/helpers/';
        if (is_dir($helpersPath)) {
            foreach (glob($helpersPath . '*.php') as $helperFile) {
                require_once $helperFile;
            }
        }
        
        require_once $this->legacyCorePath . 'BaseController.php';

        require_once $controllerFile;

        if (!class_exists($className)) {
            return $this->response->setStatusCode(404)->setBody('Class not found: ' . $className);
        }

        $ci3 = new CI3_Controller();
        
        $controllerInstance = new $className();
        
        foreach (get_object_vars($ci3) as $key => $value) {
            if (!isset($controllerInstance->$key)) {
                $controllerInstance->$key = $value;
            }
        }

        return $this->dispatchController($controllerInstance, $method, $args);
    }

    public function index()
    {
        return $this->_remap('index', $this->defaultController, 'index');
    }

    private function dispatchController($controllerInstance, string $method, array $args)
    {
        if (!method_exists($controllerInstance, $method)) {
            return $this->response->setStatusCode(404)->setBody('Method not found: ' . $method);
        }

        return call_user_func_array([$controllerInstance, $method], $args);
    }
}
