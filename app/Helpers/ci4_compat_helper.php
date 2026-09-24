<?php

if (!class_exists('FakeSession')) {
    class FakeSession {
        private static $data = [];
        
        public static function userdata($key = null) {
            if ($key === null) return self::$data;
            return self::$data[$key] ?? null;
        }
        
        public static function set_userdata($key, $value) {
            self::$data[$key] = $value;
        }
    }
}

if (!function_exists('db')) {
    function db($dbGroup = null)
    {
        return \Config\Database::connect($dbGroup);
    }
}

if (!function_exists('ci3_compat_enabled')) {
    /**
     * Feature flag for gradual CI3 compatibility retirement.
     * Set CI3_COMPAT_MODE=false (or 0/off/no) in env to disable shims globally.
     */
    function ci3_compat_enabled(): bool
    {
        $value = env('CI3_COMPAT_MODE', true);
        if (is_bool($value)) {
            return $value;
        }

        $normalized = strtolower((string) $value);
        return !in_array($normalized, ['0', 'false', 'off', 'no'], true);
    }
}

if (!function_exists('community_billing_enabled')) {
    /**
     * Platform subscription and trial for communities.
     * billing.communityEnabled=false keeps the payment code and turns billing off.
     */
    function community_billing_enabled(): bool
    {
        static $enabled = null;
        if ($enabled === null) {
            $enabled = filter_var(env('billing.communityEnabled', false), FILTER_VALIDATE_BOOLEAN);
            community_billing_sync($enabled);
        }

        return $enabled;
    }
}

if (!function_exists('community_subscription_seed')) {
    /**
     * Subscription fields written when a community is created.
     */
    function community_subscription_seed(): array
    {
        if (!community_billing_enabled()) {
            return [
                'trial_start' => null,
                'trial_end' => null,
                'is_trial' => 0,
                'is_paid' => 0,
                'subscription_status' => 'complimentary',
            ];
        }

        $now = time();

        return [
            'trial_start' => $now,
            'trial_end' => $now + (60 * 60 * 24 * 14),
            'is_trial' => 1,
            'is_paid' => 0,
            'subscription_status' => 'trialing',
        ];
    }
}

if (!function_exists('community_billing_sync')) {
    /**
     * When billing is switched back on, unpaid communities receive a fresh 14-day trial.
     */
    function community_billing_sync(bool $enabled): void
    {
        $path = WRITEPATH . 'community_billing_state.json';
        $previous = null;

        if (is_file($path)) {
            $decoded = json_decode((string) file_get_contents($path), true);
            if (is_array($decoded) && array_key_exists('enabled', $decoded)) {
                $previous = (bool) $decoded['enabled'];
            }
        }

        if ($previous === false && $enabled === true) {
            $now = time();
            db()->query(
                'UPDATE schools SET trial_start = ?, trial_end = ?, is_trial = 1, subscription_status = ? WHERE IFNULL(is_paid, 0) = 0',
                [$now, $now + (14 * 86400), 'trialing']
            );
        }

        if ($previous !== $enabled && is_writable(dirname($path))) {
            file_put_contents($path, json_encode([
                'enabled' => $enabled,
                'updated_at' => time(),
            ]));
        }
    }
}

if (!function_exists('html_escape')) {
    /**
     * CI3 compatibility helper used throughout migrated controllers/models.
     */
    function html_escape($var, bool $double_encode = true)
    {
        if (is_array($var)) {
            return array_map(static function ($value) use ($double_encode) {
                return html_escape($value, $double_encode);
            }, $var);
        }

        if ($var === null) {
            return '';
        }

        return htmlspecialchars((string) $var, ENT_QUOTES, 'UTF-8', $double_encode);
    }
}

if (!function_exists('get_controller_instance')) {
    function &get_controller_instance()
    {
        return \CodeIgniter\Config\Services::getControllerInstance();
    }
}

if (!function_exists('get_instance')) {
    function &get_instance()
    {
        static $ci_instance = null;
        
        $controller = \CodeIgniter\Config\Services::getControllerInstance();
        
        if ($controller !== null) {
            if (!isset($controller->load)) {
                $controller->load = new class {
                    public function database(): \CodeIgniter\Database\BaseConnection {
                        return \CodeIgniter\Database\Config::connect();
                    }
                    
                    public function helper($helpers) {
                        if (is_array($helpers)) {
                            foreach ($helpers as $helper) {
                                helper($helper);
                            }
                        } else {
                            helper($helpers);
                        }
                    }
                    
                    public function model($model) {
                        model($model);
                    }
                    
                    public function view($view, $data = [], $options = []) {
                        return view($view, $data, $options);
                    }
                };
            }
            
            if (!isset($controller->db)) {
                $controller->db = \CodeIgniter\Database\Config::connect();
            }
            
            if (!isset($controller->session)) {
                $controller->session = new class {
                    public function userdata($key = null) {
                        $session = \CodeIgniter\Config\Services::session();
                        if ($key === null) {
                            return $session->get();
                        }
                        return $session->get($key);
                    }
                    
                    public function set($key, $value = null) {
                        $session = \CodeIgniter\Config\Services::session();
                        if (is_array($key)) {
                            foreach ($key as $k => $v) {
                                $session->set($k, $v);
                            }
                        } else {
                            $session->set($key, $value);
                        }
                    }
                    
                    public function unset($key) {
                        $session = \CodeIgniter\Config\Services::session();
                        $session->remove($key);
                    }
                    
                    public function flashdata($key = null) {
                        $session = \CodeIgniter\Config\Services::session();
                        if ($key === null) {
                            return $session->getFlashdata();
                        }
                        return $session->getFlashdata($key);
                    }
                    
                    public function set_flashdata($key, $value = null) {
                        $session = \CodeIgniter\Config\Services::session();
                        if (is_array($key)) {
                            foreach ($key as $k => $v) {
                                $session->setFlashdata($k, $v);
                            }
                        } else {
                            $session->setFlashdata($key, $value);
                        }
                    }
                };
            }
            
            return $controller;
        }
        
        if ($ci_instance === null) {
            $ci_instance = new class {
                public $db = null;
                public $session = null;
                public $load = null;
                
                public function __construct() {
                    $this->db = \CodeIgniter\Database\Config::connect();
                    $this->session = \CodeIgniter\Config\Services::session();
                    
                    $this->load = new class {
                        public function database(): \CodeIgniter\Database\BaseConnection {
                            return \CodeIgniter\Database\Config::connect();
                        }
                        
                        public function helper($helpers) {
                            if (is_array($helpers)) {
                                foreach ($helpers as $helper) {
                                    helper($helper);
                                }
                            } else {
                                helper($helpers);
                            }
                        }
                        
                        public function model($model) {
                            model($model);
                        }
                        
                        public function view($view, $data = [], $options = []) {
                            return view($view, $data, $options);
                        }
                    };
                }
            };
        }
        
        return $ci_instance;
    }
}

if (!function_exists('get_ci_instance')) {
    function &get_ci_instance()
    {
        return get_instance();
    }
}

if (!function_exists('get_common_settings')) {
    function get_common_settings($type = '')
    {
        $db = \CodeIgniter\Database\Config::connect();
        
        $result = $db->table('settings')->where('id', 1)->get()->getRowArray();
        
        if ($result && isset($result[$type])) {
            return $result[$type];
        }
        
        $defaults = [
            'recaptcha_status' => 0,
            'recaptcha_site_key' => '',
            'recaptcha_secret_key' => '',
            'timezone' => 'UTC',
            'language' => 'english'
        ];
        
        return $type ? ($defaults[$type] ?? null) : $defaults;
    }
}

if (!function_exists('get_settings')) {
    function get_settings($type = '')
    {
        $db = \Config\Database::connect();
        
        $result = $db->table('settings')->where('id', 1)->get()->getRowArray();
        
        if ($result && isset($result[$type])) {
            return $result[$type];
        }
        return null;
    }
}

if (!function_exists('get_frontend_settings')) {
    function get_frontend_settings($type = '')
    {
        $db = \Config\Database::connect();
        
        $result = $db->table('frontend_settings')->where('id', 1)->get()->getRowArray();
        
        if ($result && isset($result[$type])) {
            return $result[$type];
        }
        return null;
    }
}

if (!function_exists('get_logo_light')) {
    function get_logo_light($type = '')
    {
        $model = new \App\Models\Settings_model();
        return $model->get_logo_light($type);
    }
}

if (!function_exists('get_user_image')) {
    function get_user_image($user_id)
    {
        $model = new \App\Models\User_model();
        return $model->get_user_image($user_id);
    }
}

if (!function_exists('get_user_language')) {
    function get_user_language()
    {
        $session = \Config\Services::session();
        $language = $session->get('language');
        
        if (!$language) {
            $language = 'english';
        }
        
        return $language;
    }
}

if (!function_exists('get_current_lang_code')) {
    function get_current_lang_code()
    {
        $language = get_user_language();
        
        $lang_map = [
            'english' => 'en',
            'french' => 'fr',
            'arabic' => 'ar',
            'spanish' => 'es',
            'dutch' => 'nl',
            'german' => 'de',
            'chinese' => 'zh',
            'hindi' => 'hi'
        ];
        
        return $lang_map[$language] ?? 'en';
    }
}

if (!function_exists('addon_status')) {
    function addon_status($unique_identifier = '')
    {
        $db = \Config\Database::connect();
        
        $result = $db->table('addons')->where('unique_identifier', $unique_identifier)->get()->getRow();
        
        if ($result) {
            return $result->status;
        }
        return 0;
    }
}

class CI3_Load_shim
{
    private $controller;
    
    public function __construct($controller)
    {
        $this->controller = $controller;
    }
    
    public function view($view, $data = [], $return = false)
    {
        log_message('warning', 'DEPRECATED: $this->load->view() - Use view("' . $view . '") helper instead');
        $output = view($view, $data);
        if ($return) {
            return $output;
        }
        echo $output;
        return null;
    }
    
    public function library($library, $params = null, $object_name = null)
    {
        log_message('warning', 'DEPRECATED: $this->load->library() - Use service() or constructor injection instead');
        $libName = ucfirst($library);
        
        $mapping = [
            'Pdf' => '\App\Libraries\Pdf',
            'SubscriptionService' => '\App\Libraries\SubscriptionService',
            'MoroccoB2BService' => '\App\Libraries\MoroccoB2BService',
            'FxRatesService' => '\App\Libraries\FxRatesService',
        ];
        
        if (isset($mapping[$libName])) {
            $className = $mapping[$libName];
            $alias = $object_name ?: strtolower($library);
            $this->controller->$alias = new $className($params);
        }
        
        return true;
    }
    
    public function config($config, $use_section = true)
    {
        log_message('warning', 'DEPRECATED: $this->load->config() - Use config("' . $config . '") instead');
        $configObj = config($config);
        if ($configObj) {
            $this->controller->config = $configObj;
        }
        return true;
    }
    
    public function helper($helpers = [])
    {
        log_message('warning', 'DEPRECATED: $this->load->helper() - Use helper() function instead');
        if (is_array($helpers)) {
            foreach ($helpers as $helper) {
                helper($helper);
            }
        } else {
            helper($helpers);
        }
        return true;
    }
    
    public function model($model, $name = '', $db_conn = false)
    {
        log_message('warning', 'DEPRECATED: $this->load->model() - Use model() function or constructor injection instead');
        $modelName = ucfirst($model);
        $alias = $name ?: strtolower($model);
        $className = 'App\\Models\\' . $modelName;
        
        if (class_exists($className)) {
            $this->controller->$alias = new $className();
        }
        return true;
    }
}

if (!function_exists('csrf_token')) {
    function csrf_token()
    {
        $security = \CodeIgniter\Config\Services::security();
        return $security->getHash() ?? '';
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field()
    {
        $security = \CodeIgniter\Config\Services::security();
        $hash = $security->getHash();
        
        if ($hash) {
            return '<input type="hidden" name="csrf_test_name" value="' . $hash . '">';
        }
        return '';
    }
}

if (!function_exists('get_csrf_token_name')) {
    function get_csrf_token_name()
    {
        return 'csrf_test_name';
    }
}

if (!function_exists('get_csrf_hash')) {
    function get_csrf_hash()
    {
        $security = \CodeIgniter\Config\Services::security();
        return $security->getHash() ?? '';
    }
}

if (!function_exists('show_404')) {
    /**
     * CI3 compatibility shim for show_404().
     */
    function show_404(string $page = '', bool $log_error = true)
    {
        if ($log_error) {
            log_message('error', '404 Page Not Found: ' . ($page !== '' ? $page : current_url(true)->getPath()));
        }

        throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound($page);
    }
}

if (!function_exists('get_where')) {
    function get_where($table, $where = null, $limit = null, $orderby = null) {
        $db = \Config\Database::connect();
        $builder = $db->table($table);
        
        if (is_array($where)) {
            foreach ($where as $key => $val) {
                $builder->where($key, $val);
            }
        }
        
        if ($orderby) {
            $builder->orderBy($orderby);
        }
        
        if ($limit) {
            $builder->limit($limit);
        }
        
        return new \App\View\ResultCompat($builder->get());
    }
}

if (!function_exists('wrap_result')) {
    function wrap_result($result) {
        return new \App\View\ResultCompat($result);
    }
}

if (!function_exists('route')) {
    function route($name, $params = [], $secure = null) {
        $routes = service('routes');
        
        try {
            $url = $routes->reverseRoute($name);
        } catch (Throwable $e) {
            $url = false;
        }
        
        if ($url === false) {
            $name = ltrim((string) $name, '/');
            if ($name === '') {
                return site_url('app/dashboard');
            }

            $segments = array_values(array_filter(explode('/', $name), static fn($s) => $s !== ''));
            if (empty($segments)) {
                return site_url('app/dashboard');
            }

            $first = strtolower((string) $segments[0]);
            $second = strtolower((string) ($segments[1] ?? ''));

            $roleControllers = ['admin', 'teacher', 'student', 'superadmin', 'accountant', 'librarian', 'parent', 'parents'];
            $roleAliasMap = [
                'student' => 'member',
                'teacher' => 'mentor',
                'exam' => 'certifications',
                'event_calendar' => 'announcements',
                'school_settings' => 'community_settings',
                'school' => 'community_list',
            ];

            if ($first === 'app') {
                return site_url(implode('/', $segments));
            }

            if ($first === 'addons' && isset($segments[1])) {
                if (strtolower((string) $segments[1]) === 'courses') {
                    return site_url(implode('/', array_merge(['app', 'courses'], array_slice($segments, 2))));
                }
                if (strtolower((string) $segments[1]) === 'lessons') {
                    return site_url(implode('/', array_merge(['app', 'lessons'], array_slice($segments, 2))));
                }
            }

            if ($first === 'chat') {
                return site_url(implode('/', array_merge(['app', 'chat'], array_slice($segments, 1))));
            }

            if (in_array($first, $roleControllers, true)) {
                if (isset($roleAliasMap[$second])) {
                    return site_url(implode('/', array_merge(['app', $roleAliasMap[$second]], array_slice($segments, 2))));
                }
                return site_url(implode('/', array_merge(['app'], array_slice($segments, 1))));
            }

            return site_url('app/' . implode('/', $segments));
        }
        
        if (!empty($params)) {
            foreach ($params as $key => $val) {
                $url = str_replace('{' . $key . '}', $val, $url);
            }
        }
        
        return $url;
    }
}

if (!function_exists('base_url')) {
    function base_url($uri = '')
    {
        $config = config(\Config\App::class);
        $baseUrl = $config->baseURL ?? '';
        
        if ($uri) {
            return rtrim($baseUrl, '/') . '/' . ltrim($uri, '/');
        }
        
        return rtrim($baseUrl, '/');
    }
}

if (!function_exists('site_url')) {
    function site_url($uri = '')
    {
        $config = config('App');
        $baseUrl = $config->baseURL ?? '';
        
        if ($uri) {
            if (is_array($uri)) {
                $uri = implode('/', $uri);
            }
            return rtrim($baseUrl, '/') . '/' . ltrim($uri, '/');
        }
        
        return rtrim($baseUrl, '/');
    }
}

if (!function_exists('get_phrase')) {
    function get_phrase($phrase = '')
    {
        $language_code = get_user_language();
        
        $langFile = APPPATH . 'Language/' . ucfirst($language_code) . '.json';
        
        if (!file_exists($langFile)) {
            $langFile = APPPATH . 'Language/English.json';
        }
        
        if (file_exists($langFile)) {
            $jsonString = file_get_contents($langFile);
            $langArray = json_decode($jsonString, true);
            
            $key = strtolower(str_replace(' ', '_', preg_replace('/\s+/', ' ', $phrase)));
            
            if (isset($langArray[$key])) {
                return htmlspecialchars($langArray[$key], ENT_QUOTES, 'UTF-8');
            }
        }
        
        return ucfirst(str_replace('_', ' ', $phrase));
    }
}

if (!function_exists('openJSONFile')) {
    function openJSONFile($code)
    {
        $jsonString = [];
        $langFile = APPPATH . 'Language/' . ucfirst($code) . '.json';
        
        if (file_exists($langFile)) {
            $jsonString = file_get_contents($langFile);
            $jsonString = json_decode($jsonString, true);
        }
        
        return $jsonString;
    }
}
if (!function_exists('school_id')) {
    function school_id()
    {
        $session = \Config\Services::session();
        
        $user_type = $session->get('user_type');
        
        if ($user_type == 'superadmin') {
            return get_settings('school_id');
        } else {
            $school_id = $session->get('school_id');
            if ($school_id > 0) {
                return $school_id;
            }
            return 0;
        }
    }
}

if (!function_exists('user_id')) {
    function user_id()
    {
        $session = \Config\Services::session();
        
        $user_id = $session->get('user_id');
        
        if ($user_id) {
            $db = \CodeIgniter\Database\Config::connect();
            $user = $db->where('id', $user_id)->get('users')->getRowArray();
            return $user ? $user['id'] : 0;
        }
        
        return 0;
    }
}

