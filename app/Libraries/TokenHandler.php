<?php

namespace App\Libraries;

require_once ROOTPATH . 'app/Libraries/JWT.php';

class TokenHandler
{
    private $key;

    public function __construct()
    {
        $this->key = (string) env('jwt.secret', '');
        if ($this->key === '') {
            throw new \RuntimeException('JWT secret is not configured (jwt.secret).');
        }
    }

    public function GenerateToken($data)
    {
        return JWT::encode($data, $this->key);
    }

    public function DecodeToken($token)
    {
        $decoded = JWT::decode($token, $this->key, ['HS256']);
        return (array) $decoded;
    }
}
