<?php

namespace App\Libraries;

require_once ROOTPATH . 'app/Libraries/JWT.php';

class TokenHandler
{
   private $key = "academy-lms-api-token-handler";
   
   public function GenerateToken($data)
   {
       $jwt = JWT::encode($data, $this->key);
       return $jwt;
   }

   public function DecodeToken($token)
   {
       $decoded = JWT::decode($token, $this->key, array('HS256'));
       $decodedData = (array) $decoded;
       return $decodedData;
   }
}
