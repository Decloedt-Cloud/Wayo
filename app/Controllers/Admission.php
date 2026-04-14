<?php

namespace App\Controllers;


class Admission extends BaseController
{
    protected $theme = 'ultimate';
    protected $active_school_id;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        /*cache control*/
        $response->setHeader('Expires', 'Tue, 01 Jan 2000 00:00:00 GMT');
        $response->setHeader('Last-Modified', gmdate("D, d M Y H:i:s") . " GMT");
        $response->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
        $response->appendHeader('Cache-Control', 'post-check=0, pre-check=0');
        $response->setHeader('Pragma', 'no-cache');

        /*SET DEFAULT TIMEZONE*/
        timezone();

        $this->theme = get_frontend_settings('theme');
        
        $frontend_model = model('Frontend_model');
        try {
            $this->active_school_id = $frontend_model->get_active_school_id();
        } catch (\Exception $e) {
            $this->active_school_id = null;
        }

        if (!session()->get('active_school_id')) {
            $this->active_school_id_for_frontend();
        }
    }

    // ACTIVE SCHOOL ID FOR FRONTEND
    function active_school_id_for_frontend($active_school_id = "")
    {
        if (addon_status('multi-school') && $active_school_id > 0) {
            session()->set('active_school_id', $active_school_id);
        } else {
            $active_school_id = get_settings('school_id');
            session()->set('active_school_id', $active_school_id);
        }
    }



    /*Admissions*/
    function online_admission($param1 = "", $param2 = "")
    {
        if (session()->get('user_id')) {
            return redirect()->to(site_url('home'));
        }
        if ($param1 == 'submit') {
            if (!$this->crud_model->check_recaptcha() && get_common_settings('recaptcha_status') == true) {
                return redirect()->to(site_url('home/contact'));
            }
            if ($param2 == 'school'){
                echo $this->frontend_model->online_admission_school();
                return;
            }
          
        }

        $page_data['page_name'] = 'online_admission';
        $page_data['page_title'] = get_phrase('online_admission');
        echo view('frontend/' . $this->theme . '/index', $page_data);
    }

        /*Admissions*/
        function online_admission_student($param1 = "", $param2 = "")
        {
            if (session()->get('user_id')) {
                return redirect()->to(site_url('home'));
                }
            if ($param1 == 'submit') {
                if (!$this->crud_model->check_recaptcha() && get_common_settings('recaptcha_status') == true) {
                    return redirect()->to(site_url('home/contact'));
                    }
                if ($param2 == 'student'){
                    echo $this->user_model->register_user_form();
                    return;
                }
          
            }

            $page_data['page_name'] = 'online_admission_student';
            $page_data['page_title'] = get_phrase('online_admission');
            echo view('frontend/' . $this->theme . '/index', $page_data);
        }

    /**
     * Direct endpoint for member registration
     * Bypasses URL rewriting system to preserve POST data
     */
    public function register_member()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode([
                'status' => false,
                'message' => 'Method not allowed',
                'csrf' => [
                    'csrfName' => $this->security->getTokenName(),
                'csrfHash' => $this->security->getHash()
                ]
            ]);
            return;
        }

        $cache = \Config\Services::cache();
        $ip = $this->request->getIPAddress();
        $cacheKey = 'register_attempts_' . $ip;
        $attempts = $cache->get($cacheKey) ?: 0;
        $maxAttempts = 5;
        $ttl = 3600;

        if ($attempts >= $maxAttempts) {
            log_message('warning', 'Rate limit exceeded for member registration from IP: ' . $ip);
            echo json_encode([
                'status' => false,
                'message' => get_phrase('too_many_registration_attempts_please_try_again_later'),
                'csrf' => [
                    'csrfName' => $this->security->getTokenName(),
                    'csrfHash' => $this->security->getHash()
                ]
            ]);
            return;
        }
        
        $result = $this->user_model->register_user_form();
        $decoded = json_decode($result, true);
        if (isset($decoded['status']) && $decoded['status'] === false) {
            $cache->save($cacheKey, $attempts + 1, $ttl);
        }
        echo $result;
    }

    /**
     * Direct endpoint for community registration
     * Bypasses URL rewriting system to preserve POST data
     */
    public function register_community()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Method not allowed',
                'csrf' => [
                    'csrfName' => $this->security->getTokenName(),
                    'csrfHash' => $this->security->getHash()
                ]
            ]);
        }

        $cache = \Config\Services::cache();
        $ip = $this->request->getIPAddress();
        $cacheKey = 'register_attempts_' . $ip;
        $attempts = $cache->get($cacheKey) ?: 0;
        $maxAttempts = 5;
        $ttl = 3600;

        if ($attempts >= $maxAttempts) {
            log_message('warning', 'Rate limit exceeded for registration from IP: ' . $ip);
            return $this->response->setJSON([
                'status' => false,
                'message' => get_phrase('too_many_registration_attempts_please_try_again_later'),
                'csrf' => [
                    'csrfName' => $this->security->getTokenName(),
                    'csrfHash' => $this->security->getHash()
                ]
            ]);
        }

        if (get_common_settings('recaptcha_status') == true) {
            if (!$this->crud_model->check_recaptcha()) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => get_phrase('recaptcha_validation_failed'),
                    'csrf' => [
                        'csrfName' => $this->security->getTokenName(),
                        'csrfHash' => $this->security->getHash()
                    ]
                ]);
            }
        }

        $response = service('response');
        $response->setContentType('application/json');

        try {
            $frontend_model = model('Frontend_model');
            
            ob_start();
            $response = $frontend_model->online_admission_school();
            $unexpected_output = ob_get_clean();

            if (!empty($unexpected_output)) {
                log_message('error', 'Unexpected output in register_community: ' . trim($unexpected_output));
            }

            $decoded = json_decode($response, true);
            if ($decoded === null && json_last_error() !== JSON_ERROR_NONE) {
                log_message('error', 'Invalid JSON from online_admission_school: ' . json_last_error_msg());
                $response = json_encode([
                    'status' => false,
                    'message' => 'Server returned invalid JSON',
                    'csrf' => [
                        'csrfName' => $this->security->getTokenName(),
                        'csrfHash' => $this->security->getHash()
                    ]
                ]);
                $decoded = json_decode($response, true);
            }

            if (isset($decoded['status']) && $decoded['status'] === false) {
                $cache->save($cacheKey, $attempts + 1, $ttl);
            }

            $resp = service('response');
            $resp->setBody($response);
            return $resp;
        } catch (Throwable $e) {
            log_message('error', 'register_community failed: ' . $e->getMessage());
            $cache->save($cacheKey, $attempts + 1, $ttl);
            $resp = service('response');
            $resp->setBody(json_encode([
                'status' => false,
                'message' => 'Server error',
                'csrf' => [
                    'csrfName' => $this->security->getTokenName(),
                    'csrfHash' => $this->security->getHash()
                ]
            ]));
        }
    }

    public function postCheckDuplicationAjax()
    {
        try {
            $type = $this->request->getPost('type');
            $value = $this->request->getPost('value');

            $response = ['available' => true];

            if ($type === 'email') {
                $user_model = model('User_model');
                $exists = $user_model->check_duplication('on_create', $value);
                if (!$exists) {
                    $response = ['available' => false, 'message' => get_phrase('this_email_already_exist')];
                }
            } elseif ($type === 'school_name') {
                $user_model = model('User_model');
                $exists = $user_model->check_duplication_school('on_create', $value);
                if (!$exists) {
                    $response = ['available' => false, 'message' => get_phrase('this_school_name_already_exist')];
                }
            }

            $response['csrf'] = [
                'csrfName' => csrf_token(),
                'csrfHash' => csrf_hash()
            ];

            return $this->response->setJSON($response);
        } catch (\Exception $e) {
            log_message('error', 'postCheckDuplicationAjax Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'available' => true,
                'error' => $e->getMessage(),
                'csrf' => [
                    'csrfName' => csrf_token(),
                    'csrfHash' => csrf_hash()
                ]
            ]);
        }
    }
    
}

