<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class Register extends BaseController
{
    protected $theme = 'ultimate';
    protected $models = ['User_model'];

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        $this->theme = function_exists('get_frontend_settings') ? get_frontend_settings('theme') : 'ultimate';
    }

    public function index()
    {
        return view('frontend/' . $this->theme . '/register');
    }

    public function create()
    {
        return $this->register_user();
    }

    /**
     * GET /register/communities — public community list (JSON).
     */
    public function communities()
    {
        $communities = db()->table('schools')
            ->select('id, name, category, access, description, country, Ville, phone, status')
            ->orderBy('id', 'DESC')
            ->get()
            ->getResultArray();

        return $this->response->setJSON([
            'status' => true,
            'data' => $communities,
            'communities' => $communities,
        ]);
    }

    /**
     * GET /register/community/{id} — public community details (JSON).
     */
    public function community($id = null)
    {
        $community = db()->table('schools')
            ->select('id, name, category, access, description, country, Ville, Rue, Numero, Codepostal, phone, status')
            ->where('id', (int) $id)
            ->get()
            ->getRowArray();

        if (!$community) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Community not found',
                'data' => null,
            ]);
        }

        return $this->response->setJSON([
            'status' => true,
            'data' => $community,
            'community' => $community,
        ]);
    }

    public function register_user()
    {
        
        
        $this->user_model->register_user();

        if (isset($_SERVER['HTTP_REFERER'])) {
            return redirect()->to($_SERVER['HTTP_REFERER']);
        }

        return redirect()->back();
    }

    public function register_user_form()
    {
        
        
        echo $this->user_model->register_user_form();
        return;
    }

    public function validate_email()
    {
        
        
        $email = $this->request->getPost('email');
        
        $user = $this->user_model->get_user_by_email($email);
        $num_rows = !empty($user) ? 1 : 0;
        
        $csrf = array(
            'csrfName' => csrf_token(),
            'csrfHash' => csrf_hash()
        );
        
        return $this->response->setJSON([
            'is_exists' => $num_rows,
            'csrf' => $csrf
        ]);
    }
}
