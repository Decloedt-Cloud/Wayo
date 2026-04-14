<?php
namespace App\Libraries;
if (!class_exists('JWT')) {
class JWT
{
    public static $leeway = 0;
    public static function encode($payload, $key, $alg = 'HS256')
    {
        $header = ['typ' => 'JWT', 'alg' => $alg];
        $segments = [
            self::urlsafeB64Encode(json_encode($header)),
            self::urlsafeB64Encode(json_encode($payload))
        ];
        $signing_input = implode('.', $segments);
        $signature = self::sign($signing_input, $key, $alg);
        $segments[] = self::urlsafeB64Encode($signature);
        return implode('.', $segments);
    }
    public static function decode($jwt, $key, $allowed_algs = ['HS256'])
    {
        $segments = explode('.', $jwt);
        if (count($segments) != 3) {
            throw new \Exception('Wrong number of segments');
        }
        $payload = json_decode(self::urlsafeB64Decode($segments[1]), true);
        return (object) $payload;
    }
    private static function sign($input, $key, $alg)
    {
        return hash_hmac('sha256', $input, $key, true);
    }
    private static function urlsafeB64Encode($input)
    {
        return str_replace('=', '', strtr(base64_encode($input), '+/', '-_'));
    }
    private static function urlsafeB64Decode($input)
    {
        $remainder = strlen($input) % 4;
        if ($remainder) {
            $input .= str_repeat('=', 4 - $remainder);
        }
        return base64_decode(strtr($input, '-_', '+/'));
    }
}
}
