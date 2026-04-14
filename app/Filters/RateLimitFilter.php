<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RateLimitFilter implements FilterInterface
{
    protected $maxRequests = 60;
    protected $windowSeconds = 60;

    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        $ip = $request->getIPAddress();
        $uri = $request->getUri()->getPath();
        $method = $request->getMethod();

        $cache = \Config\Services::cache();
        $cacheKey = 'rate_limit_' . md5($ip . $uri . $method);

        $requests = $cache->get($cacheKey);

        if ($requests === null) {
            $cache->save($cacheKey, 1, $this->windowSeconds);
            return;
        }

        if ($requests >= $this->maxRequests) {
            $response = service('response');
            return $response->setStatusCode(429)->setJSON([
                'status' => false,
                'message' => 'Too many requests. Please try again later.',
                'retry_after' => $this->windowSeconds
            ]);
        }

        $cache->save($cacheKey, $requests + 1, $this->windowSeconds);
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return $response;
    }
}
