<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Admission extends CI_Controller
{
    protected $theme;
    protected $active_school_id;

    public function __construct()
    {
        parent::__construct();

        $this->load->database();
        $this->load->library('session');

        /*LOADING ALL THE MODELS HERE*/
        $this->load->model('Crud_model', 'crud_model');
        $this->load->model('User_model', 'user_model');
        $this->load->model('Settings_model', 'settings_model');
        $this->load->model('Payment_model', 'payment_model');
        $this->load->model('Email_model', 'email_model');
        $this->load->model('Addon_model', 'addon_model');
        $this->load->model('Frontend_model', 'frontend_model');

        if (addon_status('alumni')) {
            $this->load->model('addons/Alumni_model', 'alumni_model');
        }
        /*cache control*/
        $this->output->set_header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");
        $this->output->set_header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        $this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        $this->output->set_header("Cache-Control: post-check=0, pre-check=0", false);
        $this->output->set_header("Pragma: no-cache");

        /*SET DEFAULT TIMEZONE*/
        timezone();

        $this->theme = get_frontend_settings('theme');
        $this->active_school_id = $this->frontend_model->get_active_school_id();

        if (!$this->session->userdata('active_school_id')) {
            $this->active_school_id_for_frontend();
        }
    }

    // ACTIVE SCHOOL ID FOR FRONTEND
    function active_school_id_for_frontend($active_school_id = "")
    {
        if (addon_status('multi-school') && $active_school_id > 0) {
            $this->session->set_userdata('active_school_id', $active_school_id);
        } else {
            $active_school_id = get_settings('school_id');
            $this->session->set_userdata('active_school_id', $active_school_id);
        }
    }



    /*Admissions*/
    function online_admission($param1 = "", $param2 = "")
    {
        if ($this->session->userdata('user_id')) {
            redirect(site_url('home'), 'refresh');
        }
        if ($param1 == 'submit') {
            if (!$this->crud_model->check_recaptcha() && get_common_settings('recaptcha_status') == true) {
                redirect(site_url('home/contact'), 'refresh');
            }
            if ($param2 == 'school'){
                echo $this->frontend_model->online_admission_school();
                return;
            }
          
        }

        $page_data['page_name'] = 'online_admission';
        $page_data['page_title'] = get_phrase('online_admission');
        $this->load->view('frontend/' . $this->theme . '/index', $page_data);
    }

        /*Admissions*/
        function online_admission_student($param1 = "", $param2 = "")
        {
            if ($this->session->userdata('user_id')) {
                redirect(site_url('home'), 'refresh');
            }
            if ($param1 == 'submit') {
                if (!$this->crud_model->check_recaptcha() && get_common_settings('recaptcha_status') == true) {
                    redirect(site_url('home/contact'), 'refresh');
                }
                if ($param2 == 'student'){
                    echo $this->user_model->register_user_form();
                    return;
                }

              
            }
    
            $page_data['page_name'] = 'online_admission_student';
            $page_data['page_title'] = get_phrase('online_admission_student');
            $this->load->view('frontend/' . $this->theme . '/index', $page_data);
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
                    'csrfName' => $this->security->get_csrf_token_name(),
                    'csrfHash' => $this->security->get_csrf_hash()
                ]
            ]);
            return;
        }
        
        echo $this->user_model->register_user_form();
    }

    /**
     * Direct endpoint for community registration
     * Bypasses URL rewriting system to preserve POST data
     */
    public function register_community()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => false,
                    'message' => 'Method not allowed',
                    'csrf' => [
                        'csrfName' => $this->security->get_csrf_token_name(),
                        'csrfHash' => $this->security->get_csrf_hash()
                    ]
                ]));
            return;
        }

        $this->output->set_content_type('application/json');

        try {
            ob_start();
            $response = $this->frontend_model->online_admission_school();
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
                        'csrfName' => $this->security->get_csrf_token_name(),
                        'csrfHash' => $this->security->get_csrf_hash()
                    ]
                ]);
            }

            $this->output->set_output($response);
        } catch (Throwable $e) {
            log_message('error', 'register_community failed: ' . $e->getMessage());
            $this->output->set_output(json_encode([
                'status' => false,
                'message' => 'Server error',
                'csrf' => [
                    'csrfName' => $this->security->get_csrf_token_name(),
                    'csrfHash' => $this->security->get_csrf_hash()
                ]
            ]));
        }
    }

    public function check_duplication_ajax()
    {
        $type = $this->input->post('type'); // 'email' or 'school_name'
        $value = $this->input->post('value');

        $response = ['available' => true];

        if ($type === 'email') {
            $exists = $this->user_model->check_duplication('on_create', $value);
            if (!$exists) {
                $response = ['available' => false, 'message' => get_phrase('this_email_already_exist')];
            }
        } elseif ($type === 'school_name') {
            $exists = $this->user_model->check_duplication_school('on_create', $value);
            if (!$exists) {
                $response = ['available' => false, 'message' => get_phrase('this_school_name_already_exist')];
            }
        }

        echo json_encode($response);
    }
    
}

