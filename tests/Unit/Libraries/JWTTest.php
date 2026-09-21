<?php

namespace Tests\Unit\Libraries;

use App\Libraries\JWT;
use CodeIgniter\Test\CIUnitTestCase;

class JWTTest extends CIUnitTestCase
{
    public function testEncodeDecodeRoundTrip(): void
    {
        require_once ROOTPATH . 'app/Libraries/JWT.php';

        $key = 'unit-test-secret';
        $token = JWT::encode(['uid' => 42, 'exp' => time() + 60], $key);
        $payload = (array) JWT::decode($token, $key, ['HS256']);

        $this->assertSame(42, $payload['uid']);
    }

    public function testRejectsWrongKey(): void
    {
        require_once ROOTPATH . 'app/Libraries/JWT.php';

        $token = JWT::encode(['uid' => 1], 'good-key');

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Signature verification failed');
        JWT::decode($token, 'bad-key', ['HS256']);
    }

    public function testRejectsTamperedPayload(): void
    {
        require_once ROOTPATH . 'app/Libraries/JWT.php';

        $key = 'unit-test-secret';
        $token = JWT::encode(['uid' => 1], $key);
        $parts = explode('.', $token);
        $fake = rtrim(strtr(base64_encode(json_encode(['uid' => 999])), '+/', '-_'), '=');
        $tampered = $parts[0] . '.' . $fake . '.' . $parts[2];

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Signature verification failed');
        JWT::decode($tampered, $key, ['HS256']);
    }
}
