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

        [$headb64, $bodyb64, $cryptob64] = $segments;

        $header = json_decode(self::urlsafeB64Decode($headb64), true);
        if (!is_array($header) || empty($header['alg'])) {
            throw new \Exception('Invalid token header');
        }

        $alg = $header['alg'];
        if (!in_array($alg, (array) $allowed_algs, true)) {
            throw new \Exception('Algorithm not allowed');
        }

        $payload = json_decode(self::urlsafeB64Decode($bodyb64), true);
        if (!is_array($payload)) {
            throw new \Exception('Invalid token payload');
        }

        $signing_input = $headb64 . '.' . $bodyb64;
        $expected = self::sign($signing_input, $key, $alg);
        $actual = self::urlsafeB64Decode($cryptob64);

        if (!is_string($actual) || !hash_equals($expected, $actual)) {
            throw new \Exception('Signature verification failed');
        }

        $now = time();
        if (isset($payload['nbf']) && is_numeric($payload['nbf']) && ($now + self::$leeway) < (int) $payload['nbf']) {
            throw new \Exception('Token not yet valid');
        }
        if (isset($payload['exp']) && is_numeric($payload['exp']) && ($now - self::$leeway) >= (int) $payload['exp']) {
            throw new \Exception('Token expired');
        }

        return (object) $payload;
    }

    private static function sign($input, $key, $alg)
    {
        if ($alg !== 'HS256') {
            throw new \Exception('Unsupported algorithm');
        }
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
