<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class Login extends BaseController
{
    protected $theme = 'ultimate';
    protected $supported_lang_codes = ['fr', 'en', 'ar', 'es', 'de', 'zh', 'hi'];
    protected $models = ['User_model', 'Email_model'];

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        helper(['url', 'form']);
        $this->settings_model = model('Settings_model');
        $this->user_model = model('User_model');
    }

	public function index()
	{
		if ($this->session->get('superadmin_login') == true) {
			return redirect()->to(route_to('dashboard'))->send();
		} elseif ($this->session->get('admin_login') == true) {
			return redirect()->to(route_to('dashboard'))->send();
		} elseif ($this->session->get('teacher_login') == true) {
			return redirect()->to(route_to('dashboard'))->send();
		} elseif ($this->session->get('parent_login') == true) {
			return redirect()->to(route_to('dashboard'))->send();
		} elseif ($this->session->get('student_login') == true) {
			if ($this->session->get('student_just_registered')) {
				$this->session->remove('student_just_registered');
				return redirect()->to(site_url('home/communities'))->send();
			} else {
				$redirect_role = $this->session->get('role');
				if ($redirect_role && $redirect_role !== 'student') {
					return redirect()->to(site_url($redirect_role . '/dashboard'))->send();
				} else {
					return redirect()->to(site_url('student/dashboard'))->send();
				}
			}
		} elseif ($this->session->get('accountant_login') == true) {
			return redirect()->to(route_to('dashboard'))->send();
		} elseif ($this->session->get('librarian_login') == true) {
			return redirect()->to(route_to('dashboard'))->send();
		} elseif ($this->session->get('driver_login') == true) {
			return redirect()->to(route_to('dashboard'))->send();
		} else {
			$page_data['settings_model'] = $this->settings_model;
			return view('login', $page_data);
		}
	}

    public function validate_login()
    {
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');
        
        $db = db_connect();
        $user = $db->table('users')
            ->where('email', $email)
            ->where('password', sha1($password))
            ->get()
            ->getRow();
        
        if ($user) {
            $this->session->set('user', $user);
            $this->session->set('password', $password);
            $this->session->set('user_login_type', true);

            $user_id = $user->id;
            $role = $user->role;
            $active_school_id = $user->school_id;

            if (!$active_school_id) {
                $school = $this->db->get_where('user_schools', [
                    'user_id' => $user_id,
                    'role' => $role
                ])->getRow();
                $active_school_id = $school ? $school->school_id : null;
            }

            $this->session->set('active_school_id', $active_school_id);
            $this->session->set('school_id', $active_school_id);
            $this->session->setFlashdata('flash_message', get_phrase('welcome_back'));

            $redirect_url = 'login';
            
            switch ($user->role) {
                case 'superadmin':
                    $this->session->set('superadmin_login', true);
                    $this->session->set('user_id', $user->id);
                    $this->session->set('user_name', $user->name);
                    $this->session->set('user_type', 'superadmin');
                    $this->session->set('role', $user->role);
                    $redirect_url = 'superadmin/dashboard';
                    break;
                case 'admin':
                    $this->session->set('admin_login', true);
                    $this->session->set('user_id', $user->id);
                    $this->session->set('user_name', $user->name);
                    $this->session->set('user_type', 'admin');
                    $this->session->set('role', $user->role);
                    $redirect_url = 'admin/dashboard';
                    break;
                case 'teacher':
                    $this->session->set('teacher_login', true);
                    $this->session->set('user_id', $user->id);
                    $this->session->set('user_name', $user->name);
                    $this->session->set('user_type', 'teacher');
                    $this->session->set('role', $user->role);
                    $redirect_url = 'teacher/dashboard';
                    break;
                case 'student':
                    if ($user->status != 1) {
                        $this->session->setFlashdata('error_message', get_phrase('your_account_has_been_disabled'));
                        return redirect()->to(site_url('login'));
                    }
                    $this->session->set('student_login', true);
                    $this->session->set('user_id', $user->id);
                    $this->session->set('user_name', $user->name);
                    $this->session->set('user_type', 'student');
                    $this->session->set('role', $user->role);
                    $redirect_url = 'student/dashboard';
                    break;
                case 'librarian':
                    $this->session->set('librarian_login', true);
                    $this->session->set('user_id', $user->id);
                    $this->session->set('user_name', $user->name);
                    $this->session->set('user_type', 'librarian');
                    $redirect_url = 'librarian/dashboard';
                    break;
                case 'accountant':
                    $this->session->set('accountant_login', true);
                    $this->session->set('user_id', $user->id);
                    $this->session->set('user_name', $user->name);
                    $this->session->set('user_type', 'accountant');
                    $redirect_url = 'accountant/dashboard';
                    break;
                case 'driver':
                    $this->session->set('driver_login', true);
                    $this->session->set('user_id', $user->id);
                    $this->session->set('user_name', $user->name);
                    $this->session->set('user_type', 'driver');
                    $redirect_url = 'driver/dashboard';
                    break;
                case 'parent':
                    $this->session->set('parent_login', true);
                    $this->session->set('user_id', $user->id);
                    $this->session->set('user_name', $user->name);
                    $this->session->set('user_type', 'parent');
                    $this->session->set('role', $user->role);
                    $redirect_url = 'parents/dashboard';
                    break;
            }

            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status' => true,
                    'csrf' => [
                        'csrfName' => $this->security->getTokenName(),
                        'csrfHash' => $this->security->getHash()
                    ]
                ]);
            } else {
                return redirect()->to(site_url($redirect_url));
            }
        } else {
            $this->session->setFlashdata('error_message', get_phrase('invalid_your_email_or_password'));
            
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => get_phrase('invalid_your_email_or_password'),
                    'csrf' => [
                        'csrfName' => $this->security->getTokenName(),
                        'csrfHash' => $this->security->getHash()
                    ]
                ]);
            } else {
                return redirect()->to(site_url('login'));
            }
        }
    }

    public function logout()
    {
        
        
        $this->session->set('user', null);
        $this->session->set('password', null);
        $this->session->set('user_login_type', null);
        
        $this->session->remove('superadmin_login');
        $this->session->remove('admin_login');
        $this->session->remove('teacher_login');
        $this->session->remove('student_login');
        $this->session->remove('parent_login');
        $this->session->remove('accountant_login');
        $this->session->remove('librarian_login');
        $this->session->remove('driver_login');
        
        $this->session->remove('user_id');
        $this->session->remove('school_id');
        $this->session->remove('user_name');
        $this->session->remove('user_type');
        $this->session->remove('role');
        $this->session->remove('active_school_id');

        return redirect()->to(site_url('login'));
    }

    public function retrieve_password()
    {
        
        
        if ($this->session->get('admin_login') == true) {
            return redirect()->to(site_url('admin/dashboard'));
        }

        return view('backend/login/retrieve_password');
    }

    public function retrieve_password_site()
    {
        
        
        return view('frontend/' . $this->theme . '/retrieve_password');
    }

    public function send_reset_link()
    {
        
        
        $email = $this->request->getPost('email');
        
        $query = $this->db->get_where('users', array('email' => $email));
        
        if ($query->getNumRows() > 0) {
            $user = $query->row();
            
            if ($user->role == 'student' && $user->status != 1) {
                $this->session->setFlashdata('error_message', get_phrase('your_account_has_been_disabled'));
                return redirect()->back();
            }

            $reset_code = substr(md5(time()), 0, 15);
            
            $this->db->where('id', $user->id);
            $this->db->update('users', array('reset_code' => $reset_code));

            $this->email_model->send_reset_link($email, $reset_code);
            
            $this->session->setFlashdata('flash_message', get_phrase('password_reset_link_sent'));
        } else {
            $this->session->setFlashdata('error_message', get_phrase('email_not_found'));
        }
        
        return redirect()->back();
    }

    public function new_password($reset_code = '')
    {
        
        
        $query = $this->db->get_where('users', array('reset_code' => $reset_code));
        
        if ($query->getNumRows() == 0) {
            $this->session->setFlashdata('error_message', get_phrase('invalid_reset_code'));
            return redirect()->to(site_url('login'));
        }
        
        $page_data['reset_code'] = $reset_code;
        $page_data['user'] = $query->row();
        
        return view('backend/login/new_password', $page_data);
    }

    public function new_password_student($student_id = '')
    {
        
        
        $page_data['student_id'] = $student_id;
        
        return view('backend/login/new_password_student', $page_data);
    }

    public function reset_password()
    {
        
        
        $reset_code = $this->request->getPost('reset_code');
        $new_password = $this->request->getPost('new_password');
        $confirm_password = $this->request->getPost('confirm_password');
        
        if ($new_password != $confirm_password) {
            $this->session->setFlashdata('error_message', get_phrase('password_mismatch'));
            return redirect()->back();
        }
        
        $query = $this->db->get_where('users', array('reset_code' => $reset_code));
        
        if ($query->getNumRows() > 0) {
            $user = $query->row();
            
            $this->db->where('id', $user->id);
            $this->db->update('users', array(
                'password' => sha1($new_password),
                'reset_code' => ''
            ));
            
            $this->session->setFlashdata('flash_message', get_phrase('password_changed_successfully'));
            return redirect()->to(site_url('login'));
        } else {
            $this->session->setFlashdata('error_message', get_phrase('invalid_reset_code'));
            return redirect()->to(site_url('login'));
        }
    }

    public function add_new_password()
    {
        
        
        $student_id = $this->request->getPost('student_id');
        $new_password = $this->request->getPost('new_password');
        $confirm_password = $this->request->getPost('confirm_password');
        
        if ($new_password != $confirm_password) {
            $this->session->setFlashdata('error_message', get_phrase('password_mismatch'));
            return redirect()->back();
        }
        
        $this->db->where('id', $student_id);
        $this->db->update('users', array('password' => sha1($new_password)));
        
        $this->session->setFlashdata('flash_message', get_phrase('password_changed_successfully'));
        
        $role = $this->session->get('role');
        if ($role) {
            return redirect()->to(site_url($role . '/profile'));
        }
        
        return redirect()->to(site_url('login'));
    }

    public function validate_credentials()
    {
        
        
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');
        
        $credential = array(
            'email' => $email,
            'password' => sha1($password)
        );
        
        $query = $this->user_model->get_user_by_credentials($credential);
        
        if ($query->getNumRows() > 0) {
            echo 'success';
        } else {
            echo 'failure';
        }
    }

    public function validate_code()
    {
        
        
        $reset_code = $this->request->getPost('reset_code');
        
        $query = $this->db->get_where('users', array('reset_code' => $reset_code));
        
        if ($query->getNumRows() > 0) {
            echo 'valid';
        } else {
            echo 'invalid';
        }
    }

    public function check_email_exists()
    {
        
        
        $email = $this->request->getPost('email');
        
        $query = $this->db->get_where('users', array('email' => $email));
        
        if ($query->getNumRows() > 0) {
            echo 'true';
        } else {
            echo 'false';
        }
    }

    public function check_school_name_exists()
    {
        
        
        $school_name = $this->request->getPost('school_name');
        
        $query = $this->db->get_where('schools', array('name' => $school_name));
        
        if ($query->getNumRows() > 0) {
            echo 'true';
        } else {
            echo 'false';
        }
    }

    public function get_csrf_token()
    {
        
        
        return $this->response->setJSON([
            'csrf_token_name' => $this->security->get_csrf_token_name(),
            'csrf_hash' => $this->security->get_csrf_hash()
        ]);
    }

    public function set_student_just_registered()
    {
        
        
        $this->session->set('student_just_registered', true);
        
        return $this->response->setJSON(['status' => 'success']);
    }
}
