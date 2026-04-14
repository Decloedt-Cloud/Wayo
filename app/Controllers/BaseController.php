<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use App\View\SecurityCompat;
use App\View\DatabaseCompat;
use App\View\SessionCompat;

/**
 * Base Controller
 *
 * All controllers should extend this class.
 * Provides CI4-native service injection with backward compatibility shims.
 */
#[\AllowDynamicProperties]
abstract class BaseController extends Controller
{
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * CI4 Session with CI3 compatibility wrapper
     * @var SessionCompat
     */
    protected $session;

    /**
     * CI4 Database with CI3 compatibility wrapper
     * @var DatabaseCompat
     */
    protected $db;

    /**
     * CI4 Security with CI3 compatibility wrapper
     * @var SecurityCompat
     */
    protected $security;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Helpers to load automatically
     * @var array
     */
    protected $helpers = [];

    /**
     * Models to load automatically (defined in child controllers)
     * Format: ['Model_name' => 'alias'] or ['Model_name']
     * @var array
     */
    protected $models = [];

    /**
     * Services to inject automatically
     * Format: ['serviceName' => 'propertyName'] or ['serviceName']
     * @var array
     */
    protected $services = [];

    /**
     * CI3 Load shim for backward compatibility (DEPRECATED)
     * @var \CI3_Load_shim|null
     * @deprecated Use service() or model() helpers instead
     */
    protected $load;

    /**
     * Initialize the controller with request, response, and logger.
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        // Load CI3 compatibility helpers
        $this->loadCompatHelpers();

        // Initialize core services with CI3 compatibility wrappers
        $this->session = new SessionCompat(\Config\Services::session());
        $this->db = new DatabaseCompat(\Config\Database::connect());
        $this->security = new SecurityCompat(\Config\Services::security());

        // Load helpers
        helper($this->helpers);

        // Auto-load models defined in child controller
        $this->loadModels();

        // Auto-load services defined in child controller
        $this->loadServices();

        // Legacy: load shim only when compatibility mode is enabled.
        if (
            class_exists('CI3_Load_shim', false)
            && (!function_exists('ci3_compat_enabled') || ci3_compat_enabled())
        ) {
            $this->load = new \CI3_Load_shim($this);
        }
    }

    /**
     * Load CI3 compatibility helpers
     */
    protected function loadCompatHelpers(): void
    {
        $helperFile = defined('APPPATH')
            ? APPPATH . 'Helpers/ci4_compat_helper.php'
            : __DIR__ . '/../Helpers/ci4_compat_helper.php';

        if (file_exists($helperFile) && !class_exists('CI3_Load_shim', false)) {
            require $helperFile;
        }
    }

    /**
     * Load models defined in $models property
     */
    protected function loadModels(): void
    {
        foreach ($this->models as $modelName => $alias) {
            if (is_numeric($modelName)) {
                $modelName = $alias;
                $alias = $this->getModelAlias($modelName);
            }

            $className = $this->getModelClassName($modelName);

            if (!isset($this->$alias) && class_exists($className)) {
                $this->$alias = new $className();
            }
        }
    }

    /**
     * Load services defined in $services property
     */
    protected function loadServices(): void
    {
        foreach ($this->services as $serviceName => $propertyName) {
            if (is_numeric($serviceName)) {
                $serviceName = $propertyName;
                $propertyName = $serviceName;
            }

            if (method_exists(\Config\Services::class, $serviceName)) {
                $this->$propertyName = \Config\Services::$serviceName();
            }
        }
    }

    /**
     * Get the alias for a model
     */
    protected function getModelAlias(string $modelName): string
    {
        $parts = explode('/', $modelName);
        return strtolower(end($parts));
    }

    /**
     * Get the full class name for a model
     */
    protected function getModelClassName(string $modelName): string
    {
        $fullName = $modelName;
        if (strpos($modelName, '/') !== false) {
            $parts = explode('/', $modelName);
            $modelName = end($parts);
        }

        $className = 'App\\Models\\' . $modelName;

        if (strpos($fullName, 'api/') !== false) {
            $className = 'App\\Models\\api\\' . $modelName;
        } elseif (strpos($fullName, 'addons/') !== false) {
            $className = 'App\\Models\\addons\\' . $modelName;
        }

        return $className;
    }

    /**
     * Manually load a model
     *
     * @param string $modelName Model name (e.g., 'User_model' or 'api/Admin_model')
     * @param string|null $alias Property name to assign the model to
     * @return $this
     */
    public function loadModel(string $modelName, ?string $alias = null): self
    {
        if ($alias === null) {
            $alias = $this->getModelAlias($modelName);
        }

        $className = $this->getModelClassName($modelName);

        if (class_exists($className)) {
            try {
                $this->$alias = new $className();
            } catch (\Throwable $e) {
                log_message('error', 'Failed to load model ' . $className . ': ' . $e->getMessage());
                $this->$alias = null;
            }
        }

        return $this;
    }

    /**
     * Get a service instance
     *
     * @param string $serviceName Service name (must exist in Config\Services)
     * @param bool $getShared Whether to get shared instance
     * @return mixed
     */
    protected function getService(string $serviceName, bool $getShared = true)
    {
        return \Config\Services::$serviceName(null, $getShared);
    }

    /**
     * Magic getter for backward compatibility
     *
     * @deprecated Use proper service injection instead
     */
    public function __get($name)
    {
        // Config access
        if ($name === 'config') {
            log_message('warning', 'DEPRECATED: $this->config - Use config("App") or inject config instead');
            return config('App');
        }

        // Email model shortcut
        if ($name === 'email_model') {
            return model('Email_model');
        }

        // Output shim (DEPRECATED)
        if ($name === 'output') {
            log_message('warning', 'DEPRECATED: $this->output - Use $this->response instead');
            return new class {
                public function set_header($header, $replace = true) {
                    service('response')->setHeader(explode(':', $header)[0] ?? $header, explode(':', $header)[1] ?? '');
                    return $this;
                }
                public function set_content_type($content_type) {
                    service('response')->setContentType($content_type);
                    return $this;
                }
                public function set_output($output) {
                    echo $output;
                    return $this;
                }
                public function append_output($output) {
                    echo $output;
                    return $this;
                }
                public function get_output() {
                    return '';
                }
            };
        }

        // Router access
        if ($name === 'router') {
            return service('router');
        }

        // Load shim (DEPRECATED)
        if ($name === 'load') {
            if (function_exists('ci3_compat_enabled') && !ci3_compat_enabled()) {
                log_message('warning', 'CI3 compatibility disabled: attempted access to $this->load');
                return null;
            }
            log_message('warning', 'DEPRECATED: $this->load - Use service(), model(), or view() helpers instead');
            if (class_exists('CI3_Load_shim', false)) {
                return new \CI3_Load_shim($this);
            }
            return null;
        }

        // Try to get from CI instance only in compatibility mode.
        if (
            (!function_exists('ci3_compat_enabled') || ci3_compat_enabled())
            && function_exists('get_instance')
        ) {
            $ci = get_instance();
            if (isset($ci->$name)) {
                return $ci->$name;
            }
        }

        return null;
    }

    /**
     * Get CI instance (for backward compatibility)
     *
     * @deprecated Use dependency injection instead
     */
    public function getCI()
    {
        if (function_exists('ci3_compat_enabled') && !ci3_compat_enabled()) {
            log_message('warning', 'CI3 compatibility disabled: getCI() returned current controller instance');
            return $this;
        }

        if (function_exists('get_instance')) {
            return get_instance();
        }
        return $this;
    }
}
