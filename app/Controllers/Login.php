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

        helper(['url', 'form', 'global_helper']);
        $this->settings_model = model('Settings_model');
        $this->user_model = model('User_model');
    }

    public function index()
    {
        if (session()->get('superadmin_login') == true) {
            return redirect()->to(site_url('app/dashboard'))->send();
        } elseif (session()->get('admin_login') == true) {
            return redirect()->to(site_url('app/dashboard'))->send();
        } elseif (session()->get('teacher_login') == true) {
            return redirect()->to(site_url('app/dashboard'))->send();
        } elseif (session()->get('parent_login') == true) {
            return redirect()->to(site_url('app/dashboard'))->send();
        } elseif (session()->get('student_login') == true) {
            if (session()->get('student_just_registered')) {
                session()->remove('student_just_registered');
                return redirect()->to(site_url('home/communities'))->send();
            } else {
                $redirect_role = session()->get('role');
                if ($redirect_role && $redirect_role !== 'student') {
                    return redirect()->to(site_url('app/dashboard'))->send();
                } else {
                    return redirect()->to(site_url('app/dashboard'))->send();
                }
            }
        } elseif (session()->get('accountant_login') == true) {
            return redirect()->to(site_url('app/dashboard'))->send();
        } elseif (session()->get('librarian_login') == true) {
            return redirect()->to(site_url('app/dashboard'))->send();
        } elseif (session()->get('driver_login') == true) {
            return redirect()->to(site_url('app/dashboard'))->send();
        } else {
            $page_data['settings_model'] = $this->settings_model;
            return view('login', $page_data);
        }
    }

    public function validate_login()
    {
        return $this->handleLoginAttempt();
    }

    public function validate_login_frontend()
    {
        return $this->handleLoginAttempt();
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to(base_url('login'));
    }

    public function send_reset_link()
    {
        if ($this->request->getPost('email')) {
            $email = trim((string) $this->request->getPost('email'));
            $query = $this->db->table('users')->where('email', $email)->get();
            if ($query->getNumRows() > 0) {
                $user = $query->getRow();
                $this->email_model->send_password_reset_email($user->id, $email);
            }

            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => get_phrase('check_your_email'),
                    'csrf_token_name' => get_csrf_token_name(),
                    'csrf_hash' => csrf_hash(),
                    'csrf' => [
                        'name' => get_csrf_token_name(),
                        'hash' => csrf_hash()
                    ]
                ]);
            }

            session()->setFlashdata('flash_message', get_phrase('check_your_email'));
            return redirect()->to(base_url('login'));
        } else {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => get_phrase('please_provide_valid_email'),
                    'csrf_token_name' => get_csrf_token_name(),
                    'csrf_hash' => csrf_hash(),
                    'csrf' => [
                        'name' => get_csrf_token_name(),
                        'hash' => csrf_hash()
                    ]
                ]);
            }
            return redirect()->to(base_url('login'));
        }
    }

    private function handleLoginAttempt()
    {
        $email = trim((string) $this->request->getPost('email'));
        $password = (string) $this->request->getPost('password');

        if ($email === '' || $password === '') {
            return $this->loginErrorResponse(get_phrase('please_fill_all_the_fields'));
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->loginErrorResponse(get_phrase('invalid_email_format'));
        }

        $rateLimit = $this->checkLoginRateLimit($email);
        if ($rateLimit['blocked']) {
            log_message('warning', 'Login attempt blocked due to rate limit - Email: {email}, IP: {ip}', [
                'email' => $email,
                'ip' => $this->request->getIPAddress()
            ]);
            return $this->loginErrorResponse(
                get_phrase('too_many_requests') . '. ' . get_phrase('please_try_again_after_some_time')
            );
        }

        $row = $this->db->table('users')->where('email', $email)->get()->getRow();
        
        $storedHash = '';
        $userExists = false;
        
        if ($row) {
            $storedHash = (string) ($row->password ?? '');
            $userExists = true;
        } else {
            $storedHash = '$2y$10$K8/Qp/7r/4r/4r/4r/4r/4r/4r/4r/4r/4r/4r/4r/4r/4r/4r/4r/4r/4';
        }
        
        $isValidPassword = $this->verifyPassword($password, $storedHash);
        
        if (!$userExists || !$isValidPassword) {
            $this->increaseLoginRateLimit($email);
            log_message('warning', 'Failed login attempt - Email: {email}, IP: {ip}, Reason: {reason}', [
                'email' => $email,
                'ip' => $this->request->getIPAddress(),
                'reason' => !$userExists ? 'User not found' : 'Invalid password'
            ]);
            return $this->loginErrorResponse(get_phrase('invalid_your_email_or_password'));
        }

        if (isset($row->status) && (int) $row->status !== 1) {
            $this->increaseLoginRateLimit($email);
            log_message('warning', 'Login attempt on inactive account - Email: {email}, IP: {ip}, Status: {status}', [
                'email' => $email,
                'ip' => $this->request->getIPAddress(),
                'status' => $row->status
            ]);
            if ((int) $row->status === 3) {
                return $this->loginErrorResponse('Please verify your email address before logging in. Check your inbox for the verification link.');
            }
            return $this->loginErrorResponse(get_phrase('your_account_has_been_disabled'));
        }

        $this->upgradeLegacyPassword((int) $row->id, $password, $storedHash);
        $this->clearLoginRateLimit($email);
        session()->regenerate(true);
        $this->clearRoleLoginFlags();
        
        log_message('info', 'Successful login - Email: {email}, IP: {ip}, Role: {role}', [
            'email' => $email,
            'ip' => $this->request->getIPAddress(),
            'role' => $row->role
        ]);

        // Keep minimal user footprint in session and avoid sensitive fields.
        session()->set('user', (object) [
            'id' => $row->id,
            'name' => $row->name,
            'email' => $row->email,
            'role' => $row->role,
            'school_id' => $row->school_id
        ]);
        session()->set('user_login_type', true);

        $user_id = $row->id;
        $role = $row->role;
        $active_school_id = $row->school_id;

        if (!$active_school_id) {
            $school = $this->db->table('user_schools')->where(['user_id' => $user_id, 'role' => $role])->get()->getRow();
            $active_school_id = $school ? $school->school_id : null;
        }

        session()->set('active_school_id', $active_school_id);
        session()->set('school_id', $active_school_id);
        session()->setFlashdata('flash_message', get_phrase('welcome_back'));

        switch ($row->role) {
            case 'superadmin':
                session()->set('superadmin_login', true);
                session()->set('user_id', $row->id);
                session()->set('user_name', $row->name);
                session()->set('user_type', 'superadmin');
                session()->set('role', $row->role);
                $redirect_url = site_url('app/dashboard');
                break;

            case 'admin':
                session()->set('admin_login', true);
                session()->set('user_id', $row->id);
                session()->set('user_name', $row->name);
                session()->set('user_type', 'admin');
                session()->set('role', $row->role);
                $redirect_url = site_url('app/dashboard');
                break;

            case 'teacher':
                session()->set('teacher_login', true);
                session()->set('user_id', $row->id);
                session()->set('user_name', $row->name);
                session()->set('user_type', 'teacher');
                session()->set('role', $row->role);
                $redirect_url = site_url('app/dashboard');
                break;

            case 'student':
                session()->set('student_login', true);
                session()->set('user_id', $row->id);
                session()->set('user_name', $row->name);
                session()->set('user_type', 'student');
                session()->set('role', $row->role);
                if (session()->get('student_just_registered')) {
                    session()->remove('student_just_registered');
                    $redirect_url = site_url('home/communities');
                } else {
                    $redirect_url = site_url('app/dashboard');
                }
                break;

            case 'parent':
                session()->set('parent_login', true);
                session()->set('user_id', $row->id);
                session()->set('user_name', $row->name);
                session()->set('user_type', 'parent');
                session()->set('role', $row->role);
                $redirect_url = site_url('app/dashboard');
                break;

            case 'librarian':
                session()->set('librarian_login', true);
                session()->set('user_id', $row->id);
                session()->set('user_name', $row->name);
                session()->set('user_type', 'librarian');
                session()->set('role', $row->role);
                $redirect_url = site_url('app/dashboard');
                break;

            case 'accountant':
                session()->set('accountant_login', true);
                session()->set('user_id', $row->id);
                session()->set('user_name', $row->name);
                session()->set('user_type', 'accountant');
                session()->set('role', $row->role);
                $redirect_url = site_url('app/dashboard');
                break;

            case 'driver':
                session()->set('driver_login', true);
                session()->set('user_id', $row->id);
                session()->set('user_name', $row->name);
                session()->set('user_type', 'driver');
                session()->set('role', $row->role);
                $redirect_url = site_url('app/dashboard');
                break;

            default:
                $redirect_url = site_url('login');
                break;
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'status' => 'success',
                'redirect' => $redirect_url,
                'csrf_token_name' => get_csrf_token_name(),
                'csrf_hash' => csrf_hash(),
                'csrf' => [
                    'name' => get_csrf_token_name(),
                    'hash' => csrf_hash()
                ]
            ]);
        }

        return redirect()->to($redirect_url);
    }

    private function loginErrorResponse(string $message)
    {
        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $message,
                'csrf_token_name' => get_csrf_token_name(),
                'csrf_hash' => csrf_hash(),
                'csrf' => [
                    'name' => get_csrf_token_name(),
                    'hash' => csrf_hash()
                ]
            ]);
        }

        session()->setFlashdata('error_message', $message);
        return redirect()->to(base_url('login'));
    }

    private function getLoginRateLimitKey(string $email): string
    {
        $ip = (string) $this->request->getIPAddress();
        return 'login_rate_limit_' . sha1(strtolower($email) . '|' . $ip);
    }

    private function checkLoginRateLimit(string $email): array
    {
        $cache = cache();
        $key = $this->getLoginRateLimitKey($email);
        $state = $cache->get($key);
        $now = time();

        if (!is_array($state)) {
            return ['blocked' => false];
        }

        $locked_until = isset($state['locked_until']) ? (int) $state['locked_until'] : 0;
        if ($locked_until > $now) {
            return [
                'blocked' => true,
                'retry_after' => $locked_until - $now
            ];
        }

        return ['blocked' => false];
    }

    private function increaseLoginRateLimit(string $email): void
    {
        $cache = cache();
        $key = $this->getLoginRateLimitKey($email);
        $state = $cache->get($key);
        $now = time();

        if (!is_array($state)) {
            $state = [
                'count' => 0,
                'locked_until' => 0
            ];
        }

        $count = isset($state['count']) ? (int) $state['count'] : 0;
        $count++;

        if ($count >= 5) {
            $state['count'] = 0;
            $state['locked_until'] = $now + 300;
            $cache->save($key, $state, 300);
            return;
        }

        $state['count'] = $count;
        $state['locked_until'] = 0;
        $cache->save($key, $state, 300);
    }

    private function clearLoginRateLimit(string $email): void
    {
        $cache = cache();
        $cache->delete($this->getLoginRateLimitKey($email));
    }

    private function clearRoleLoginFlags(): void
    {
        session()->remove([
            'superadmin_login',
            'admin_login',
            'teacher_login',
            'student_login',
            'parent_login',
            'accountant_login',
            'librarian_login',
            'driver_login'
        ]);
    }

    private function verifyPassword(string $plainPassword, string $storedHash): bool
    {
        if ($storedHash === '') {
            return false;
        }

        if ($this->isLegacySha1Password($storedHash)) {
            return hash_equals(strtolower($storedHash), sha1($plainPassword));
        }

        return password_verify($plainPassword, $storedHash);
    }

    private function isLegacySha1Password(string $storedHash): bool
    {
        return (bool) preg_match('/^[a-f0-9]{40}$/i', $storedHash);
    }

    private function upgradeLegacyPassword(int $userId, string $plainPassword, string $storedHash): void
    {
        $needsUpgrade = $this->isLegacySha1Password($storedHash)
            || password_needs_rehash($storedHash, PASSWORD_DEFAULT);

        if (!$needsUpgrade) {
            return;
        }

        $this->db->table('users')
            ->where('id', $userId)
            ->update(['password' => password_hash($plainPassword, PASSWORD_DEFAULT)]);
    }

    private function validatePasswordComplexity(string $password): array
    {
        $errors = [];
        $minLength = 8;
        $hasUpperCase = preg_match('/[A-Z]/', $password);
        $hasLowerCase = preg_match('/[a-z]/', $password);
        $hasDigit = preg_match('/[0-9]/', $password);
        $hasSpecialChar = preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password);

        if (strlen($password) < $minLength) {
            $errors[] = get_phrase('password_must_be_at_least') . ' ' . $minLength . ' ' . get_phrase('characters');
        }

        if (!$hasUpperCase) {
            $errors[] = get_phrase('password_must_contain_at_least_one_uppercase_letter');
        }

        if (!$hasLowerCase) {
            $errors[] = get_phrase('password_must_contain_at_least_one_lowercase_letter');
        }

        if (!$hasDigit) {
            $errors[] = get_phrase('password_must_contain_at_least_one_digit');
        }

        if (!$hasSpecialChar) {
            $errors[] = get_phrase('password_must_contain_at_least_one_special_character');
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }
}
