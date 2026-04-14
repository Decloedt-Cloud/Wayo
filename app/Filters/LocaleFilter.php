<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class LocaleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $uri = $request->getUri();
        $path = $uri->getPath();
        
        $segments = explode('/', $path);
        
        if (!empty($segments) && !empty($segments[0])) {
            $supportedLocales = ['en', 'fr', 'ar', 'es', 'nl'];
            
            if (in_array($segments[0], $supportedLocales)) {
                $locale = array_shift($segments);
                
                $request->setLocale($locale);
                
                $newPath = implode('/', $segments);
                
                $newUri = $uri->setPath($newPath);
                
                $_SERVER['REQUEST_URI'] = '/' . $newPath;
                
                if (isset($_SERVER['QUERY_STRING']) && !empty($_SERVER['QUERY_STRING'])) {
                    $_SERVER['REQUEST_URI'] .= '?' . $_SERVER['QUERY_STRING'];
                }
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
